<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "edit - edit funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2009 Werner Ammon ( wa<at>chaos.de )

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["fileed"]["right"] == "" ||
        priv_check("/".$cfg["fileed"]["subdir"]."/".$cfg["fileed"]["name"],$cfg["fileed"]["right"]) ||
        priv_check_old("",$cfg["fileed"]["right"]) ) {

        // funktions bereich fuer erweiterungen
        // ***

        if ( strstr($_SERVER["HTTP_REFERER"],$pathvars["virtual"]."/wizard") ) {
            $_SESSION["wizard_last_edit"] = $_SERVER["HTTP_REFERER"];
        }

        // markierte Dateien werden nacheinander abgearbeitet
        if ( $environment["parameter"][1] == "" ) {
            if ( count($_SESSION["file_memo"]) > 0 ) {
                $environment["parameter"][1] = current($_SESSION["file_memo"]);
            } else {
                $header = $_SESSION["adv_referer"][$environment["ebene"]."/".$environment["kategorie"]];
                unset($_SESSION["adv_referer"][$environment["ebene"]."/".$environment["kategorie"]]);
                header("Location: ".$header);
            }
        }

        // advanced referer
        if ( !strstr($_SERVER["HTTP_REFERER"],$environment["ebene"]."/".$environment["kategorie"]) ) {
            $_SESSION["adv_referer"][$environment["ebene"]."/".$environment["kategorie"]] = $_SERVER["HTTP_REFERER"];
        }

        // +++
        // funktions bereich fuer erweiterungen

        // page basics
        // ***

        $sql = "SELECT *
                  FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
             LEFT JOIN ".$cfg["fileed"]["db"]["user"]["entries"]."
                    ON (".$cfg["fileed"]["db"]["file"]["user"]."=".$cfg["fileed"]["db"]["user"]["key"].")
                 WHERE ".$cfg["fileed"]["db"]["file"]["key"]."='".$environment["parameter"][1]."'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $form_values = $db -> fetch_array($result,1);

        // ist die gruppenberechtigung aktiviert
        if ( isset($form_values[$cfg["fileed"]["db"]["file"]["grant_grp"]]) ) $grant_grp_mode = -1;

        if ( count($_POST) != 0 ) {
            $form_values = array_merge($form_values,$_POST);
            $form_values["ffart"] = strtolower(substr(strrchr($form_values["ffname"],"."),1));
        }

        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".modify");

        // form elememte bauen
        $element = form_elements( $cfg["fileed"]["db"]["file"]["entries"], $form_values );

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // form elemente erweitern
        // link zum thumbnail wird gebaut
        $type = $cfg["file"]["filetyp"][$form_values["ffart"]];
        if ( $type == "img" ) {
            $filename = $cfg["file"]["base"]["webdir"].
                        $form_values["ffart"]."/".
                        $form_values["fid"]."/".
                        $cfg["file"]["fileopt"]["preview_size"]."/".
                        $form_values["fname"];
            $hidedata["preview_img"]["id"] = $form_values["fid"];
            $hidedata["preview_img"]["path"] = $cfg["file"]["base"]["webdir"].
                                               $form_values["ffart"]."/";
        } else {
            $filename = $cfg["fileed"]["iconpath"].$cfg["file"]["fileopt"][$type]["thumbnail"];
            $hidedata["preview_def"]["file"] = $cfg["fileed"]["iconpath"].$cfg["file"]["fileopt"][$type]["thumbnail"];
        }
        $ausgaben["thumbnail"] = $filename;

        // dummy-fhit-feld
        // * * * * *
        // fhit aufsplitten
        $block_elements = array();
        if ( is_array($cfg["fileed"]["dummy_regex"]) ) {
            foreach ( $cfg["fileed"]["dummy_regex"] as $pattern ) {
                preg_match_all("/".$pattern."/Ui",$form_values["fhit"],$match);
                $block_elements = array_merge($block_elements,$match[0]);
            }
        }
        $fhit_dummy = trim(str_replace($block_elements,"",$form_values["fhit"]));
        $fhit_delicate = trim(implode(" ",$block_elements));

        if ( !priv_check("/".$cfg["fileed"]["subdir"]."/".$cfg["fileed"]["name"],$cfg["fileed"]["no_dummy"])
            && $cfg["fileed"]["no_dummy"] != "" ) {
            if ( isset($_POST["fhit_dummy"]) ) {
                $fhit_dummy = $_POST["fhit_dummy"];
            }
            // fhit_dummy von "verbotenen "eingaben bereinigen
            if ( is_array($cfg["fileed"]["dummy_regex"]) ) {
                foreach ( $cfg["fileed"]["dummy_regex"] as $pattern ) {
                    preg_match_all("/".$pattern."/Ui",$fhit_dummy,$match);
                    $fhit_dummy = str_replace($match[0],"",$fhit_dummy);
                }
            }
            $hidedata["fhit_dummy"]["value"] = $fhit_dummy;
            $hidedata["fhit_dummy"]["readonly"] = "";
        } else {
            $hidedata["fhit_admin"]["value"] = $form_values["fhit"];
            $hidedata["fhit_admin"]["readonly"] = "";
        }
        // dummy-fhit-feld
        // + + + + +


        // grant edit-rechte
        // * * * * *
        if ( $grant_grp_mode == -1 && function_exists("group_permit") ) {

            $group_permit = group_permit( $form_values[$cfg["fileed"]["db"]["file"]["grant_grp"]] );
            $perm_groups      = $group_permit["perm_groups"];
            $own_groups       = $group_permit["own_groups"];
            $intersect_groups = $group_permit["intersect_groups"];

            if ( $_SESSION["uid"] == $form_values["fuid"] ) {
            // nur besitzer darf gruppenrechte setzen
                if ( $form_values[$cfg["fileed"]["db"]["file"]["grant_grp"]] == "-1" || $form_values["grant_all"] == "-1" ) {
                    $hidedata["grant"]["radio_grant_me"] = "";
                    $hidedata["grant"]["radio_grant_all"] = " checked=\"true\"";
                } else {
                    $hidedata["grant"]["radio_grant_me"] = " checked=\"true\"";
                    $hidedata["grant"]["radio_grant_all"] = "";
                }
                // kombination der vergebenen und eigenen gruppen, keine doppelte
                if ( $form_values[$cfg["fileed"]["db"]["file"]["grant_grp"]] == "-1" ) {
                    $avail_groups = $own_groups;
                } else {
                    $avail_groups = array_flip(array_flip(array_merge($perm_groups,$own_groups)));
                }
            } else {
            // alle andere bekommen nur eine auflistung der berechtigten gruppen
                if ( $form_values[$cfg["fileed"]["db"]["file"]["grant_grp"]] == "-1" ) {
                    $hidedata["all_groups_allowed"] = array();
                } else {
                    $avail_groups = $perm_groups;
                    if ( count($avail_groups) > 0 ) $hidedata["show_allowed_groups"] = array();
                }
            }

            // auflistung der gruppen
            if ( count($avail_groups) > 0 ) {
                $sql = "SELECT *
                          FROM  auth_group
                         WHERE gid IN (".implode(",",$avail_groups).")
                      ORDER BY ggroup";
                $result = $db -> query($sql);
                while ( $data = $db -> fetch_array($result,1) ) {
                    $check = "";
                    if ( in_array($data["gid"],$perm_groups)
                    && $form_values["grant_all"] != "-1"
                    && $form_values[$cfg["fileed"]["db"]["file"]["grant_grp"]] != "-1"  ) {
                        $check = " checked=\"true\"";
                    }
                    if ( is_array($cfg["fileed"]["su_groups"]) && in_array($data["gid"],$cfg["fileed"]["su_groups"]) ) {
                        $class = "inaktiv";
                        $disabled = " disabled=\"disabled\"";
                        $check = " checked=\"true\"";
                    } else {
                        $class = "";
                        $disabled = "";
                    }
                    $dataloop["avail_groups"][] = array(
                             "gid" => $data["gid"],
                           "group" => $data["ggroup"],
                            "desc" => $data["beschreibung"],
                           "check" => $check,
                           "class" => $class,
                        "disabled" => $disabled,
                    );
                }
            }
        }
        // + + + + +
        // grant edit-rechte


        $hidedata["references"] = array();

        // wo im content wird die datei verwendet
        $used_in = content_check($environment["parameter"][1]);
        if ( count($used_in) > 0 ) {
            $ausgaben["reference"] = implode("<br />",$used_in);
        } else {
            $ausgaben["reference"] = "---";
        }

        // in welchen galerien wird die datei verwendet
        $compilations = compilation_list($environment["parameter"][1]);
        preg_match_all("/#p([0-9]+),/U",$form_values["fhit"],$match);
        $intersect = array_intersect_key($compilations,array_flip($match[1]));
        ksort($intersect);
        $ausgaben["ref_comp"] = "";
        if ( count($intersect) > 0 ) {
            foreach ( $intersect as $value ) {
                $group_content = ""; $i = 1;
                if ( count($value["content"]) > 0 ) {
                    foreach ( $value["content"] as $content ) {
                        if ( $group_content != "" ) $group_content .= ", ";
                        $used_in = tname2path($content);
                        $group_content .= "<a href=\"".$used_in.".html\" title=\"/".$used_in.".html\">[".$i."]</a>";
                        $i++;
                    }
                }
                if ( $group_content != "" ) $group_content = " (#(used_in) ".$group_content.")";
                $ausgaben["ref_comp"] .= "<b>#".$value["id"]."</b>".$group_content."<br>";
            }
        } else {
            $ausgaben["ref_comp"] = "---";
        }

        // ersetzen-feld
        if ( $_SESSION["uid"] == $form_values["fuid"]           # nur eigene dateien duerfen ersetzt werden
          || count($intersect_groups) > 0 ) {                   # oder wenn man in berechtigter gruppe ist
            // dateien duerfen nur ersetzt werden, wenn sie nirgends verwendet werden
            if ( (count($used_in) == 0 && count($intersect) == 0) || $cfg["fileed"]["replace_used"] == true ) {
                $hidedata["upload"][0] = -1;
                $owner_error = "";
            }
        } else {
            if ( count($perm_groups) == 0 ) $owner_error = "#(error_edit)";
            $element["ffname"] = str_replace(">"," readonly=\"true\">",$element["ffname"]);
            $element["fdesc"] = str_replace(">"," readonly=\"true\">",$element["fdesc"]);
            $element["fhit"] = str_replace(">"," readonly=\"true\">",$element["fhit"]);
            $element["funder"] = str_replace(">"," readonly=\"true\">",$element["funder"]);
            if ( is_array($hidedata["fhit_dummy"]) ) $hidedata["fhit_dummy"]["readonly"] = " readonly=\"true\"";
            if ( is_array($hidedata["fhit_admin"]) ) $hidedata["fhit_admin"]["readonly"] = " readonly=\"true\"";
        }

        // besitzer feststellen
        $hidedata["owner"] = array(
             "name" => $form_values[$cfg["fileed"]["db"]["user"]["forename"]]." ".$form_values[$cfg["fileed"]["db"]["user"]["surname"]],
            "email" => $form_values[$cfg["fileed"]["db"]["user"]["email"]],
            "error" => $owner_error,
        );

        // falls zip wird der inhalt gebaut
        if ( $form_values["ffart"] == "zip" && function_exists("zip_open") ) {
            $file_srv = $cfg["file"]["fileopt"][$type]["path"].$type."_".$form_values["fid"].".".$form_values["ffart"];
            $dataloop["zip"] = zip_handling($file_srv);
            if ( count($dataloop["zip"]) > 0 ) {
                $hidedata["zip"][] = -1;
            }
        }

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        #$ausgaben["form_error"] = ""; siehe edit sperre!

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["fileed"]["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].",verify.html";
        $ausgaben["form_break"] = $cfg["fileed"]["basis"]."/list.html";
        if ( $_SESSION["wizard_last_edit"] != "" ) $ausgaben["form_break"] = $_SESSION["wizard_last_edit"];

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".modify";
        #$mapping["navi"] = "leer";
        if ( $environment["parameter"][2] == "extract" ) {
            $ausgaben["style_file_edit"]    = "display:none;";
            $ausgaben["style_extract_edit"] = "display:block;";
        } else {
            $ausgaben["style_file_edit"]    = "display:block;";
            $ausgaben["style_extract_edit"] = "display:none;";
        }

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_edit) #(error_edit)<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_replace) #(error_replace)<br />";
            $ausgaben["inaccessible"] .= "# (all_groups_allowed) #(all_groups_allowed)<br />";
            $ausgaben["inaccessible"] .= "# (allowed_groups) #(allowed_groups)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

        // beim abbrechen werden alle eigenen dateien aus new-ordner geloescht
        if ( $_POST["abort"] != "" ) {
//             $header = $ausgaben["form_break"];
            $header = $_SESSION["adv_referer"][$environment["ebene"]."/".$environment["kategorie"]];
            unset($_SESSION["adv_referer"][$environment["ebene"]."/".$environment["kategorie"]]);
            $dp = opendir($cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"]);
            while ( $file = readdir($dp) ) {
                $info  = explode( "_", $file, 2 );
                if ( $info[0] == $_SESSION["uid"] ) {
                    unlink($cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"].$file);
                }
            }
            header("Location: ".$header);
        }

        if ( $environment["parameter"][3] == "verify"
            &&  ( $_POST["send"] != ""
                || $_POST["extract"] != ""
                || $_POST["extension2"] != "" ) ) {

            // form eingaben pruefen
            form_errors( $form_options, $_POST );

            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""   ) {

                if ( $owner_error == "" ) {

                    // funktions bereich fuer erweiterungen
                    // ***

                    // file ersetzen
                    if ( $_FILES["upload"]["name"] != "" ) {
                            $error = file_validate($_FILES["upload"]["tmp_name"], $_FILES["upload"]["size"], $cfg["file"]["filesize"], array($form_values["ffart"]), "upload");
                            if ( $error == 0 ) {
                                $newname = $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"].$_SESSION["uid"]."_".$_FILES["upload"]["name"];
                                rename($_FILES["upload"]["tmp_name"],$newname);
                                $file_id = $form_values["fid"];
                                arrange( $file_id, $newname, $_FILES["upload"]["name"] );
                            } else {
                                $ausgaben["form_error"] .= "#(error_replace) ".$file["name"]." g(file_error".$error.")";
                            }
                    }

                    if ( $_POST["extract"] != "" ) {
                        // naechste freie compilation-id suchen
                        if ( $_POST["selection"] == -1 ) {
                            $buffer = compilation_list();
                            reset($buffer);
                            $compid = key($buffer) + 1;
                        } else {
                            $compid = "";
                        }
                        // zip auspacken
                        $not_extracted = zip_handling($file_srv,
                                                      $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"],
                                                      $cfg["file"]["filetyp"],
                                                      $cfg["file"]["filesize"],
                                                      "",
                                                      $compid,
                                                      $cfg["fileed"]["zip_handling"]["sektions"]
                        );
                        if ( count($not_extracted) > 0 ) {
                            $buffer = array();
                            foreach ( $not_extracted as $value ) {
                                $buffer[] = $value["name"];
                            }
                            $ausgaben["form_error"] .= "#(not_compl_extracted)".implode(", ",$buffer);
                        } else {
                            header("Location: ".$cfg["fileed"]["basis"]."/add.html");
                            exit;
                        }
                    }

                    // ggf versteckte fhit-eingtraege wieder anhaengen
                    if ( !priv_check("/".$cfg["fileed"]["subdir"]."/".$cfg["fileed"]["name"],$cfg["fileed"]["no_dummy"]) ) {
                        // dummy wird ergaenzt
                        $fhit = $fhit_delicate." ".trim($fhit_dummy);
                        $_POST["fhit"] = trim($fhit);
                    }

                    // +++
                    // funktions bereich fuer erweiterungen

                    $kick = array( "PHPSESSID", "form_referer", "send", "image", "image_x", "image_y", "fdesc", "extract", "selection", "bnet", "cnet", "zip_fdesc", "zip_fhit", "zip_funder", "fhit_dummy", "grant_all", "perm_groups" );
                    foreach($_POST as $name => $value) {
                        if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                            if ( $sqla != "" ) $sqla .= ",\n ";
                            $sqla .= $name."='".$value."'";
                        }
                    }

                    // Sql um spezielle Felder erweitern
                    #$ldate = $_POST["ldate"];
                    #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                    #$sqla .= ", ldate='".$ldate."'";
                    if ( trim($_POST["fdesc"]) == "" ) {
                        $sqla .= ",\n fdesc='".$_POST["funder"]."'";
                    } else {
                        $sqla .= ",\n fdesc='".$_POST["fdesc"]."'";
                    }
                    // grant edit-rechte
                    if ( $_SESSION["uid"] == $form_values["fuid"] && $grant_grp_mode == -1 ) {
                        if ( $_POST["grant_all"] == -1 ) {
                            $sqla .= ",\n ".$cfg["fileed"]["db"]["file"]["grant_grp"]."='-1'";
                        } else {
                            if ( is_array($_POST["perm_groups"]) ) {
                                $sqla .= ",\n ".$cfg["fileed"]["db"]["file"]["grant_grp"]."='".implode(":",$_POST["perm_groups"])."'";
                            } else {
                                $sqla .= ",\n ".$cfg["fileed"]["db"]["file"]["grant_grp"]."=''";
                            }
                        }
                    }

                    $sql = "UPDATE ".$cfg["fileed"]["db"]["file"]["entries"]."
                               SET ".$sqla."
                             WHERE ".$cfg["fileed"]["db"]["file"]["key"]."='".$environment["parameter"][1]."'";

                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) {
                        $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                    }
                    if ( $header == "" ) $header = $cfg["fileed"]["basis"]."/edit.html";

                } else {
                    if ( $header == "" ) $header = $cfg["fileed"]["basis"]."/edit.html";
                }

                if ( $_SESSION["file_memo"][$environment["parameter"][1]] != "" ) unset ($_SESSION["file_memo"][$environment["parameter"][1]]);
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
