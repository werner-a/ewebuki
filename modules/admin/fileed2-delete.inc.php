<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - delete funktion";
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

    if ( $cfg["fileed"]["right"] == "" || priv_check('', $cfg["fileed"]["right"] ) || ($cfg["auth"]["menu"]["fileed"][2] == -1 &&  priv_check('', $cfg["fileed"]["right"],$specialvars["dyndb"] ) ) ) {

        // funktions bereich fuer erweiterungen
        // ***
        if ( count($_SESSION["file_memo"]) == 0 ) {
            header("Location: ".$cfg["fileed"]["basis"]."/list.html");
        }

        // +++
        // funktions bereich fuer erweiterungen

        $sql = "SELECT *
                  FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                 WHERE ".$cfg["fileed"]["db"]["file"]["key"]." IN (".implode(",",$_SESSION["file_memo"]).")";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $forbidden = array();
        while ( $data = $db -> fetch_array($result,1) ) {
            if ( $cfg["file"]["filetyp"][$data["ffart"]] == "img" ) {
                $link = $cfg["fileed"]["basis"]."/delete/view,o,".$data["fid"].".html";
            } else {
                $link = $cfg["file"]["base"]["webdir"].$data["ffart"]."/".$data["fid"]."/".$data["ffname"];
            }

            // berechtigte gruppen rausfinden
            if ( isset($cfg["fileed"]["db"]["file"]["grant_grp"]) ) {
                $group_permit = group_permit( $data[$cfg["fileed"]["db"]["file"]["grant_grp"]] );
            }


            // berechtigter personenkreis
            if ( $_SESSION["uid"] != $data["fuid"]                  # kein Eigentuemer
              && count($group_permit["intersect_groups"]) == 0      # weder berechtigte gruppe noch superuser
            ) {
                $dataloop["list"][$data["fid"]] = array(
                            "id" => $data["fid"],
                          "item" => $data["ffname"],
                          "link" => $link,
                        "reason" => "#(user_error)",
                );
                $forbidden[$data["fid"]] = $data["fid"];
            } else {
                $pages = content_check($data["fid"]);
                if ( count($pages) > 0 ) {
                    foreach ( $pages as $value ) {
                        $dataloop["list"][$data["fid"]] = array(
                                    "id" => $data["fid"],
                                  "item" => $data["ffname"],
                                  "link" => $link,
                                "reason" => "#(content_error)"
                        );
                    }
                        $forbidden[$data["fid"]] = $data["fid"];
                    }
                // selection-check
                if ( strstr($data["fhit"],"#p") ) {
                    preg_match_all("/#p([0-9]*)[,0-9]*#/i",$data["fhit"],$match);
                    foreach ( $match[1] as $value ) {
                        $view_link = "<a href=\"".$cfg["fileed"]["basis"]."/delete/view,o,".$data["fid"].",".$value.".html\">Gruppe #".$value."</a>";
                        $dataloop["list"][] = array(
                                    "id" => $data["fid"],
                                  "item" => $data["ffname"],
                                  "link" => $link,
                                "reason" => "#(group_error)".$view_link,
                        );
                        $forbidden["sel_db".$value] = $data["fid"];
                    }
             
                }
                // selection-check2
                $compilations_OnTheFly = compilation_list("",25,1);
                foreach ( $compilations_OnTheFly as $ofl_id) {
                    $ofl = trim($ofl_id["id"],":");
                    $ofl_array =explode(":", $ofl);
                    if (in_array($data["fid"], $ofl_array)) {
                        if ( count($ofl_id["content"]) > 0 ) {
                            foreach ( $ofl_id["content"] as $content ) {
                                $view_link = "Gruppe (On The Fly)";
                                $dataloop["list"][] = array(
                                            "id" => "a".$data["fid"],
                                          "item" => $data["ffname"],
                                          "link" => $link,
                                        "reason" => "#(group_error)".$view_link,
                                );
                            }
                        }
                        if ( isset($group_content) ) $group_content = " (#(used_in) ".$group_content.")";
                        $forbidden["sel_fly"] = $data["fid"];
                    }
                }
            }


            if ( !in_array($data["fid"],$forbidden) ) {
                $dataloop["list"][$data["fid"]] = array(
                            "id" => $data["fid"],
                          "item" => $data["ffname"],
                          "link" => $link,
                        "reason" => "#(delete_ok)",
                );
            }
        }

        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

        // +++
        // funktions bereich fuer erweiterungen

        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["fileed"]["basis"]."/delete.html";
        $ausgaben["form_break"] = $cfg["fileed"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] = "";
        $ausgaben["form_delete"] = "true";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".delete";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (user_error) #(user_error)<br />";
            $ausgaben["inaccessible"] .= "# (content_error) #(content_error)<br />";
            $ausgaben["inaccessible"] .= "# (group_error) #(group_error)<br />";
            $ausgaben["inaccessible"] .= "# (delete_error) #(delete_error)<br />";
            $ausgaben["inaccessible"] .= "# (delete_ok) #(delete_ok)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        // +++
        // unzugaengliche #(marken) sichtbar machen

        // wohin schicken
        #n/a

        // +++
        // page basics


        // das loeschen wurde bestaetigt, loeschen!
        // ***
        if ( isset($_POST["send"]) ) {
            $delete_admin = FALSE;
            if ( isset($cfg["fileed"]["delete_admin"] ) ) {
                if ( priv_check("/", $cfg["fileed"]["delete_admin"] ) ) {
                    $delete_admin = TRUE;        
                }
            }
            foreach ( $_SESSION["file_memo"] as $value ) {
                if ( !in_array($value,$forbidden) || $delete_admin == TRUE ) {
                    // feststellen ob es ein bild ist
                    $sql = "SELECT ffart, fuid
                              FROM site_file
                             WHERE fid =".$value;
                    $result = $db -> query($sql);
                    $data = $db -> fetch_array($result,1);

                    $type = $cfg["file"]["filetyp"][$data["ffart"]];
                    if ( $type == "img" ) {

                        $art = array( "o" => "img", "s" => "img", "m" => "img", "b" => "img", "tn" => "tn" );
                        foreach ( $art as $key => $pre ) {
                            $file_name = $cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["base"]["pic"][$key].$pre."_".$value.".".$data["ffart"];
                            if ( file_exists($file_name) ) {
                                $return = unlink($file_name);
                                if ( $return != 1 ) {
                                    $error[$value] = "#(delete_error)";
                                }
                            }
                        }
                    } else {
                        $file_name = $cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["fileopt"][$type]["name"]."_".$value.".".$data["ffart"];
                            if ( file_exists($file_name) ) {
                                $return = unlink($file_name);
                                if ( $return != 1 ) {
                                    $error[$value] = "#(delete_error)";
                                }
                            }
                    }

                    // datensatz loeschen
                    if ( $error[$value] == "" ) {
                        $sql = "DELETE FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                                      WHERE ".$cfg["fileed"]["db"]["file"]["key"]."='".$value."';";
                        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $result  = $db -> query($sql);
                        if ( $result ) {
                            unset ($dataloop["list"][$value]);
                            unset ($_SESSION["file_memo"][$value]);
                        }

                        if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result1)<br />");

                    } else {
                        $dataloop["list"][$value]["reason"] = $error[$value];
                    }
                }
            }

            // wohin schicken
            if ( $ausgaben["form_error"] == "" && count($error) == 0 ) {
                header("Location: ".$cfg["fileed"]["basis"]."/list.html");
            }
        }
        // +++
        // das loeschen wurde bestaetigt, loeschen!

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
