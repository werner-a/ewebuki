<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "eWeBuKi cms editor";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

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

    // label bearbeitung aktivieren
    if ( isset($HTTP_GET_VARS["edit"]) ) {
        $specialvars["editlock"] = 0;
    } else {
        $specialvars["editlock"] = -1;
    }

    // erlaubnis bei intrabvv speziell setzen
    $database = $environment["parameter"][1];
    if ( is_array($_SESSION["katzugriff"]) ) {
        if ( in_array("-1:".$database.":".$environment["parameter"][2],$_SESSION["katzugriff"]) ) $erlaubnis = -1;
    }
    if ( is_array($_SESSION["dbzugriff"]) ) {
        if ( in_array($database,$_SESSION["dbzugriff"]) ) $erlaubnis = -1;
    }

    if ( $rechte["cms_edit"] == -1
      #|| $rechte["administration"] == -1 && $rechte["sti"] == -1 ) { ### loesung?
      || $rechte["administration"] == -1 || $erlaubnis == -1 ) {

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene: ".$_SESSION["ebene"].$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "kategorie: ".$_SESSION["kategorie"].$debugging["char"];

        $db->selectDb($database,FALSE);

        if ( $environment["kategorie"] == "edit" ) {

            $ausgaben["ce_tem_db"]      = "#(db): ".$environment["parameter"][1];
            $ausgaben["ce_tem_name"]    = "#(template): ".$environment["parameter"][2];
            $ausgaben["ce_tem_label"]   = "#(label): ".$environment["parameter"][3];
            $ausgaben["ce_tem_convert"] = "#(convert): ".$environment["parameter"][5];
            $ausgaben["ce_tem_lang"]    = "#(language): ".$environment["language"];

            $sql = "SELECT html, content FROM ". SITETEXT ." WHERE tname='".$environment["parameter"][2]."' AND lang='".$environment["language"]."' AND label='".$environment["parameter"][3]."'";
            $result  = $db -> query($sql);
            $data = $db -> fetch_array($result, $nop);
            $found = $db -> num_rows($result);

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
                default:
                    $data["html"] = 0;
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

            // navigation erstellen
            $ausgaben["form_hidden"] = $data["html"];
            $ausgaben["form_abbrechen"] = $_SESSION["page"];

            // was anzeigen
            $mapping["main"] = $template;
            $mapping["navi"] = "";

            // unzugaengliche #(marken) sichtbar machen
            if ( isset($HTTP_GET_VARS["edit"]) ) {
                $ausgaben["inaccessible"]  = "inaccessible values:<br />";
                $ausgaben["inaccessible"] .= "# (upload) #(upload)<br />";
                $ausgaben["inaccessible"] .= "# (file) #(file)<br />";
                $ausgaben["inaccessible"] .= "# (files) #(files)<br />";
            } else {
                $ausgaben["inaccessible"] = "";
            }

            // wohin schicken
            $ausgaben["form_aktion"] = $pathvars["virtual"]."/cms/save,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",".$found.".html";

       } elseif ( $environment["kategorie"] == "save" ) {

            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene: ".$_SESSION["ebene"].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "kategorie: ".$_SESSION["kategorie"].$debugging["char"];

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


            if ( $environment["parameter"][4] == 1 ) {
                if ( $HTTP_POST_VARS["content"] == "" ) {
                    $sql = "DELETE FROM ". SITETEXT ."
                                  WHERE  label ='".$environment["parameter"][3]."'
                                    AND  tname ='".$environment["parameter"][2]."'
                                    AND  lang = '".$environment["language"]."'";
                } else {
                    $sql = "UPDATE ". SITETEXT ." set
                                    content = '".$content."',
                                      crc32 = '".$specialvars["crc32"]."',
                                       html = '".$HTTP_POST_VARS["html"]."',
                                      ebene = '".$_SESSION["ebene"]."',
                                  kategorie = '".$_SESSION["kategorie"]."',
                                    changed = '".date("Y-m-d H:i:s")."',
                                  bysurname = '".$_SESSION["surname"]."',
                                 byforename = '".$_SESSION["forename"]."',
                                    byemail = '".$_SESSION["email"]."',
                                    byalias = '".$_SESSION["alias"]."'
                             WHERE  label = '".$environment["parameter"][3]."'
                               AND  tname = '".$environment["parameter"][2]."'
                               AND  lang = '".$environment["language"]."'";
                }
            } else {
                $sql = "INSERT INTO ". SITETEXT ."
                                    (lang, crc32, label,
                                     tname, ebene, kategorie,
                                     html, content,
                                     changed, bysurname, byforename, byemail, byalias)
                             VALUES ( '".$environment["language"]."',
                                      '".$specialvars["crc32"]."',
                                      '".$environment["parameter"][3]."',
                                      '".$environment["parameter"][2]."',
                                      '".$_SESSION["ebene"]."',
                                      '".$_SESSION["kategorie"]."',
                                      '".$HTTP_POST_VARS["html"]."',
                                      '".$content."',
                                      '".date("Y-m-d H:i:s")."',
                                      '".$_SESSION["surname"]."',
                                      '".$_SESSION["forename"]."',
                                      '".$_SESSION["email"]."',
                                      '".$_SESSION["alias"]."')";
            }
            $result  = $db -> query($sql);
            if ( !$result ) die($db -> error("DB ERROR: "));


            if ( $HTTP_POST_VARS["add"] || $HTTP_POST_VARS["upload"] > 0 ) {

                $_SESSION["referer"] = $ausgaben["form_referer"];
                $_SESSION["return"] = str_replace("save,", "edit,", $pathvars["requested"]);

                if ( $HTTP_POST_VARS["upload"] > 0 ) {
                    header("Location: ".$pathvars["virtual"]."/admin/fileed/upload.html?anzahl=".$HTTP_POST_VARS["upload"]);
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
