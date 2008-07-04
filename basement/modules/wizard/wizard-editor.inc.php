<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: contented-edit.inc.php 1242 2008-02-08 16:16:50Z chaot $";
// "contented - edit funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // parameter-verzeichnis:
    // 1: Datenbank
    // 2: tname
    // 3: label
    // 4: tag-name:tag-index
    // 5: version
    // 6: index des bereichs der im show angezeigt wird

    // datenbank auswaehlen
    $database = $environment["parameter"][1];
    $db->selectDb($database,FALSE);

    if ( is_array($_SESSION["content"]) ) {

        // daten holen
        // ***
        $environment["parameter"][5] != "" ? $version = " AND version=".$environment["parameter"][5] : $version = "";
        $sql = "SELECT version, html, content, changed, byalias
                    FROM ". SITETEXT ."
                    WHERE lang = '".$environment["language"]."'
                    AND label ='".$environment["parameter"][3]."'
                    AND tname ='".$environment["parameter"][2]."'
                    $version
                    ORDER BY version DESC
                    LIMIT 0,1";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);

        $form_values = $db -> fetch_array($result,1);

        // falls content in session zwischengespeichert ist, diesen holen
        $identifier = $environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3];
        if ( $_SESSION["wizard_content"][$identifier] != "" ) {
            $form_values["content"] = $_SESSION["wizard_content"][$identifier];
        }

        $tag_meat = content_split_all($form_values["content"]);

        if ( count($_POST) > 0 ) {
            $form_values = $_POST;
        }
        // +++
        // daten holen

        // evtl. spezielle section
        $tag_marken = explode(":",$environment["parameter"][4]);
        $form_values["content"] = $tag_meat[$tag_marken[0]][$tag_marken[1]]["meat"];
        if ( $_POST["content"] != "" ) {
            $form_values["content"] = $_POST["content"];
        }

        // buchstaben zaehlen
        // * * *
        $ausgaben["name"] = "content";
        if ( $cfg["wizard"]["letters"] != "" ) {
            $ausgaben["charakters"] = "#(charakters)";
            $ausgaben["eventh2"] = "onKeyDown=\"count('content',".$cfg["wizard"]["letters"].");\" onChange=\"chk('content',".$cfg["wizard"]["letters"].");\"";
        } else {
            $ausgaben["charakters"] = "";
            $ausgaben["eventh2"] = "";
        }
        $ausgaben["inhalt"] = $form_values["content"];
        // + + +

        // feststellen, welche Tags erlaubt sind
        // * * *
        $allowed_tags = $cfg["wizard"]["allowed_tags"][$tag_marken[0]];
        if ( count($tag_marken) > 1 ) {
            $tag_compl = str_replace(array("[","]"),"",$tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"]);
            if ( is_array($cfg["wizard"]["allowed_tags"][$tag_compl]) ) {
                $allowed_tags = $cfg["wizard"]["allowed_tags"][$tag_compl];
            }
        }
        if ($allowed_tags == "") $allowed_tags = array();
        $ausgaben["tn"] = makece("ceform", "content", $form_values["content"], $allowed_tags);
        // + + +

        // referer in SESSION mitschleppen
        if ( $_SESSION["form_referer"] == ""
          && !strstr($_SERVER["HTTP_REFERER"],$cfg["wizard"]["basis"])
          && !strstr($_SERVER["HTTP_REFERER"],"/admin/") ) {
            $_SESSION["form_referer"] = $_SERVER["HTTP_REFERER"];
            $_SESSION["form_send"] = "version";
        }

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["wizard"]["basis"]."/editor,".
                                                            $environment["parameter"][1].",".
                                                            $environment["parameter"][2].",".
                                                            $environment["parameter"][3].",".
                                                            $environment["parameter"][4].",,".
                                                            $environment["parameter"][6].",verify.html";
        $ausgaben["form_break"] = $cfg["wizard"]["basis"]."/show,".
                                                            $environment["parameter"][1].",".
                                                            $environment["parameter"][2].",".
                                                            $environment["parameter"][3].",".
                                                            $environment["parameter"][4].",,".
                                                            $environment["parameter"][6].",unlock.html";

        if ( count($tag_marken) > 0 ) {

            // abspeichern, part1
            // * * *
            if ( $environment["parameter"][7] == "verify"
                &&  ( $_POST["send"] != ""
                    || $_POST["add"] != ""
                    || $_POST["sel"] != ""
                    || $_POST["refresh"] != ""
                    || $_POST["upload"] != "" ) ) {

                // form eingaben pr�fen
                form_errors( $form_options, $_POST );
                if ( $ausgaben["form_error"] == ""  ) {
                    // falls content in session steht diesen holen, ansonsten aus der db
                    if ( $_SESSION["wizard_content"][$identifier] != "" ) {
                        $old_content = $_SESSION["wizard_content"][$identifier];
                    } else {
                        $sql = "SELECT version, html, content
                                FROM ". SITETEXT ."
                                WHERE tname='".$environment["parameter"][2]."'
                                AND lang='".$environment["language"]."'
                                AND label='".$environment["parameter"][3]."'
                            ORDER BY version DESC
                                LIMIT 0,1";
                        $result = $db -> query($sql);
                        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $data = $db -> fetch_array($result, $nop);
                        $old_content = $data["content"];
                    }
                    $tag_meat = content_split_all($old_content);
                    // verbotenen tags rausfiltern
                    foreach ( $allowed_tags as $value ) {
                        $buffer[] = "[/".strtoupper($value)."]";
                    }
                }
            }
            // + + +

            // auf spezial-wizard-editor testen
            $wizard_file = $pathvars["moduleroot"].$cfg["wizard"]["subdir"]."/".$cfg["wizard"]["name"]."-".$environment["kategorie"]."-".strtolower($tag_marken[0]).".inc.php";
            if ( file_exists($wizard_file) ) {

                include $wizard_file;

            } else {
                // was anzeigen
                $mapping["main"] = "wizard-edit";
                $hidedata["default"] = array();

                // vogelwilde regexen die alte & neue links zu ewebuki-files findet
                // und viel arbeit erspart
                preg_match_all("/".str_replace("/","\/",$cfg["file"]["base"]["webdir"])."[a-z]+\/([0-9]+)\//",$form_values["content"],$found1);
                preg_match_all("/".str_replace("/","\/",$cfg["file"]["base"]["webdir"])."[a-z]+\/[a-z]+\/[a-z]+_([0-9]+)\./",$form_values["content"],$found2);
                $found = array_merge($found1[1],$found2[1]);
                $debugging["ausgabe"] .= "<pre>".print_r($found,True)."</pre>";

                // file memo auslesen und zuruecksetzen
                if ( is_array($_SESSION["file_memo"]) ) {
                    $array = array_merge($_SESSION["file_memo"],$found);
    //                 unset($_SESSION["file_memo"]);
                } else {
                    $array = $found;
                }

                // wenn es thumbnails gibt, anzeigen
                if ( count($array) >= 1 ) {

                    $merken = $db -> getDb();
                    if ( $merken != DATABASE ) {
                        $db -> selectDB( DATABASE ,"");
                    }

                    foreach ( $array as $value ) {
                        if ( $where != "" ) $where .= " OR ";
                        $where .= "fid = '".$value."'";
                    }
                    $sql = "SELECT *
                            FROM site_file
                            WHERE ".$where."
                        ORDER BY ffname, funder";
                    $result = $db -> query($sql);


                    if ( $merken != DATABASE ) {
                        $db -> selectDB($merken,"");
                    }

                    filelist($result, "contented");
                }

                // abspeichern, part 2
                // * * *
                if ( $environment["parameter"][7] == "verify"
                    &&  ( $_POST["send"] != ""
                        || $_POST["add"] != ""
                        || $_POST["sel"] != ""
                        || $_POST["refresh"] != ""
                        || $_POST["upload"] != "" ) ) {

                    // neuen content bauen
                    // * * *
                    // markeninhalt
                    $to_insert = $tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"].
                                    tagremove($_POST["content"],False,$buffer).
//                                     // experimentell: html in tag umwandeln
//                                     html2tag($_POST["content"],$buffer).
                                    $tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_end"];
                    // + + +
                }
                // + + +

            }

            if ( $environment["parameter"][7] == "verify" && $_POST["cancel"] != "" ) {
                $header = $cfg["wizard"]["basis"]."/show,".$environment["parameter"][1].",".
                                                    $environment["parameter"][2].",".
                                                    $environment["parameter"][3].",".
                                                    ",".
                                                    $environment["parameter"][5].",".
                                                    $environment["parameter"][6].".html";
                header("Location: ".$header);
            }

            // abspeichern, part 3
            // * * *
            if ( $environment["parameter"][7] == "verify" && $ausgaben["form_error"] == ""
                &&  ( $_POST["send"] != ""
                    || $_POST["add"] != ""
                    || $_POST["sel"] != ""
                    || $_POST["refresh"] != ""
                    || $_POST["upload"] != "" ) ) {

                // vor-,nachlauf
                $pre_content = substr($old_content,0,$tag_meat[$tag_marken[0]][$tag_marken[1]]["start"]);
                $post_content = substr($old_content,$tag_meat[$tag_marken[0]][$tag_marken[1]]["end"]);

//                 // experimentell: html in tag umwandeln
//                 $to_insert = html2tag($to_insert);

                // zusammenbauen
                $content = $pre_content.
                        $to_insert.
                        $post_content;

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

                // neuen content in session zwischenscheichern
                if ( $_POST["ajax"] == "on" ) {
//                     // experimentell: html in tag umwandeln
//                     $content = html2tag($content,"none");
                    $content = tagreplace($content);
                    $content = tagremove($content);
                    if ( get_magic_quotes_gpc() == 1 ) {
                        $content = stripslashes($content);
                    }
                    $content = utf8_encode($content);
                    echo preg_replace(array("/#\{.+\}/U","/g\(.+\)/U"),"",$content);
                    die ;
                }
                if ( get_magic_quotes_gpc() == 1 ) {
                    $content = stripslashes($content);
                }
                $_SESSION["wizard_content"][$identifier] = $content;

                if ( $_POST["add"] || $_POST["sel"] || $_POST["upload"] ) {

                    $_SESSION["cms_last_edit"] = str_replace(",verify", "", $pathvars["requested"]);
                    $_SESSION["wizard_last_edit"] = str_replace(",verify", "", $pathvars["requested"]);

                    $_SESSION["cms_last_referer"] = $ausgaben["form_referer"];
                    $_SESSION["cms_last_ebene"] = $_SESSION["ebene"];
                    $_SESSION["cms_last_kategorie"] = $_SESSION["kategorie"];

                    if ( $_POST["sel"] != "" ) {
                        unset($_SESSION["compilation_memo"]);
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/compilation.html");
                    } elseif ( $_POST["upload"] != "" && $error == 0 ) {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/add.html");
                    } else {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/list.html");
                    }

                } elseif ( $_POST["refresh"] != "" ) {
                    header("Location: ".$ausgaben["form_aktion"]."");
                } else {
                    $header = $cfg["wizard"]["basis"]."/show,".$environment["parameter"][1].",".
                                                        $environment["parameter"][2].",".
                                                        $environment["parameter"][3].",".
                                                        ",".
                                                        $environment["parameter"][5].",".
                                                        $environment["parameter"][6].".html";
                    header("Location: ".$header);
                }
            }
            // + + +

        }

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";

            $ausgaben["inaccessible"] .= "# (description) #(description)<br />";
            $ausgaben["inaccessible"] .= "# (get_file) #(get_file)<br />";
            $ausgaben["inaccessible"] .= "# (upload_file) #(upload_file)<br />";
            $ausgaben["inaccessible"] .= "# (get_sel) #(get_sel)<br />";

            $ausgaben["inaccessible"] .= "# (refresh) #(refresh)<br />";



            $ausgaben["inaccessible"] .= "# (upload) #(upload)<br />";
            $ausgaben["inaccessible"] .= "# (file) #(file)<br />";
            $ausgaben["inaccessible"] .= "# (files) #(files)<br />";
            $ausgaben["inaccessible"] .= "g (preview) g(preview)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }



    $db -> selectDb(DATABASE,FALSE);



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>