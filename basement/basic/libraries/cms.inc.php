<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "eWeBuKi cms editor";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of eWeBuKi

    eWeBuKi is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    eWeBuKi is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with eWeBuKi; If you did not, you may download a copy at:

    URL:  http://www.gnu.org/licenses/gpl.txt

    You may also request a copy from:

    Free Software Foundation, Inc.
    59 Temple Place, Suite 330
    Boston, MA 02111-1307
    USA

    You may contact the author/development team at:

    Chaos Networks
    c/o Werner Ammon
    Lerchenstr. 11c

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

    // erlaubnis bei intrabvv speziell setzen
    #global $HTTP_SESSION_VARS;
    $database = $environment["parameter"][1];
    if ( is_array($HTTP_SESSION_VARS["katzugriff"]) ) {
        if ( in_array("-1:".$database.":".$environment["parameter"][2],$HTTP_SESSION_VARS["katzugriff"]) ) $erlaubnis = -1;
    }
    if ( is_array($HTTP_SESSION_VARS["dbzugriff"]) ) {
        if ( in_array($database,$HTTP_SESSION_VARS["dbzugriff"]) ) $erlaubnis = -1;
    }

    if ( $rechte["cms_edit"] == -1
      #|| $rechte["administration"] == -1 && $rechte["sti"] == -1 ) { ### loesung?
      || $rechte["administration"] == -1 || $erlaubnis == -1 ) {

        $db->selectDb($database,FALSE);

        if ( $environment["kategorie"] == "edit" ) {

            session_register("ebene");
            session_register("kategorie");

            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene: ".$HTTP_SESSION_VARS["ebene"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "kategorie: ".$HTTP_SESSION_VARS["kategorie"].$debugging["char"];

            $ausgaben["ce_tem_db"]      = "DB: ".$environment["parameter"][1];
            $ausgaben["ce_tem_name"]    = "Template: ".$environment["parameter"][2];
            $ausgaben["ce_tem_label"]   = "Label: ".$environment["parameter"][3];
            $ausgaben["ce_tem_convert"] = "Convert: ".$environment["parameter"][5];
            $ausgaben["ce_tem_lang"]    = "Sprache: ".$environment["language"];

            $sql = "SELECT tid, html, content FROM ". SITETEXT ." WHERE tname='".$environment["parameter"][2]."' AND lang='".$environment["language"]."' AND label='".$environment["parameter"][3]."'";
            $result  = $db -> query($sql);
            $data = $db -> fetch_array($result, $nop);

            // convert tag 2 html
            switch ( $environment["parameter"][5] ) {
                case "html":
                    // content nach html wandeln
                    $data["content"] = tagreplace($data["content"]);
                    // intelligenten link tag bearbeiten
                    $data["content"] = intelilink($data["content"]);
                    // newlines nach br wandeln
                    $data["content"] = nlreplace($data["content"]);
                    // html db value aendern
                    $data["html"] = -1;
                    break;
                case "tag":
                    // content nach cmstag wandeln
                    ###
                    // html db value aendern
                    $data["html"] = 0;
                    break;
            }

            if ( $data["html"] == "-1" ) {
                $ausgaben["ce_name"] = "content";
                $ausgaben["ce_inhalt"] = $data["content"];

                // epoz fix
                if ( $specialvars["wysiwyg"] == "epoz" ) {
                    $sea = array("\\","\n","\r","'");
                    $rep = array("\\\\","\\n","\\r","\\'");
                    $ausgaben["ce_inhalt"] = str_replace( $sea, $rep, $ausgaben["ce_inhalt"]);
                }

                // template
                $template = "cms.edit.".$specialvars["wysiwyg"];
            } else {
                // ce editor bauen
                $ausgaben["tn"] = makece("ceform", "content", $data["content"]);
                // template
                $template = "cms.edit.cmstag";
            }

            // referer im form mit hidden element mitschleppen
            if ( $HTTP_GET_VARS["referer"] != "" ) {
                $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
            } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
                $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            } else {
                $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            }

            // was anzeigen
            $ausgaben["form_hidden"] = $data["html"];
            $ausgaben["form_abbrechen"] = $HTTP_SESSION_VARS["page"];
            $mapping["main"] = $template;

            // wohin schicken
            $ausgaben["form_aktion"] = $pathvars["virtual"]."/cms/save,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",".$data["tid"].".html";

       } elseif ( $environment["kategorie"] == "save" ) {

            session_register("ebene");
            session_register("kategorie");

            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene: ".$HTTP_SESSION_VARS["ebene"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "kategorie: ".$HTTP_SESSION_VARS["kategorie"].$debugging["char"];

            // referer im form mit hidden element mitschleppen
            if ( $HTTP_GET_VARS["referer"] != "" ) {
                $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
            } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
                $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            } else {
                $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            }

            $content = $HTTP_POST_VARS["content"];

            // html killer :)
            if ( $specialvars["denyhtml"] == -1 ) {
                $content = strip_tags($content);
            }

            // space killer
            if ( $specialvars["denyspace"] == -1 ) {
                $pattern = "  +";
                while ( preg_match("/".$pattern."/", $content, $tag) ) {
                    $content = str_replace($tag[0]," ",$content);
                }
            }


            if ( $environment["parameter"][4] != "" ) {
                if ( $HTTP_POST_VARS["content"] == "" ) {
                    $sql = "DELETE FROM ". SITETEXT ." WHERE tid='".$environment["parameter"][4]."'";
                } else {
                    $sql = "UPDATE ". SITETEXT ." set
                    content='".$content."',
                    crc32='".$specialvars["crc32"]."',
                    html='".$HTTP_POST_VARS["html"]."',
                    ebene='".$HTTP_SESSION_VARS["ebene"]."',
                    kategorie='".$HTTP_SESSION_VARS["kategorie"]."'
                    WHERE tid='".$environment["parameter"][4]."'";
                }
            } else {
                $sql = "INSERT INTO ". SITETEXT ."
                        (lang, crc32, tname, label, ebene, kategorie, html, content)
                        VALUES (
                        '".$environment["language"]."',
                        '".$specialvars["crc32"]."',
                        '".$environment["parameter"][2]."',
                        '".$environment["parameter"][3]."',
                        '".$HTTP_SESSION_VARS["ebene"]."',
                        '".$HTTP_SESSION_VARS["kategorie"]."',
                        '".$HTTP_POST_VARS["html"]."',
                        '".$content."')";
            }
            $result  = $db -> query($sql);

            if ( $HTTP_POST_VARS["image"] == "add" || $HTTP_POST_VARS["upload"] > 0 ) {

                session_register("referer");
                $HTTP_SESSION_VARS["referer"] = $ausgaben["form_referer"];

                session_register("return");
                $HTTP_SESSION_VARS["return"] = str_replace("save,", "edit,", $pathvars["requested"]);

                if ( $HTTP_POST_VARS["upload"] > 0 ) {
                    header("Location: ".$pathvars["virtual"]."/admin/fileed/select.html?anzahl=".$HTTP_POST_VARS["upload"]);
                } else {
                    header("Location: ".$pathvars["virtual"]."/admin/fileed/list.html");
                }

            } else {
                header("Location: ".$ausgaben["form_referer"]."");
            }
        }
        $db -> selectDb(DATABASE,FALSE);
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
