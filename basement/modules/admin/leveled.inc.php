<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "Level Management Applikation";
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

    // warning ausgeben
    if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

    // path fuer die schaltflaechen anpassen
    if ( $cfg["iconpath"] == "" ) $cfg["iconpath"] = "/images/default/";

    // label bearbeitung aktivieren
    if ( isset($HTTP_GET_VARS["edit"]) ) {
        $specialvars["editlock"] = 0;
    } else {
        $specialvars["editlock"] = -1;
    }

    if ( $environment["kategorie"] == "modify" && $rechte[$cfg["right"]] == -1 ) {

        if ( $environment["parameter"][1] == "add" ) {

            // form otions holen
            $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

            // form elememte bauen
            $element = form_elements( $cfg["db"]["level"]["entries"], $HTTP_POST_VARS );

            // form elemente erweitern
            #n/a

            // user management form form elemente begin
            // ***
            $element["add"] = "";
            $element["del"] = "";
            $element["actual"] = "";
            $element["avail"] = "<select name=\"avail[]\" size=\"10\" multiple>";
            $sql = "SELECT uid, username
                        FROM ".$cfg["db"]["user"]["entries"]."
                    ORDER BY ".$cfg["db"]["user"]["order"];
            $result = $db -> query($sql);
            while ( $all = $db -> fetch_array($result,1) ) {
                $element["avail"] .= "<option value=\"".$all["uid"]."\">".$all["username"]."</option>\n";
            }
            $element["avail"] .= "</select>";
            // +++
            // user management form form elemente end

            // fehlermeldungen
            $ausgaben["form_error"] = "";

            // navigation erstellen
            $ausgaben["form_aktion"] = $cfg["basis"]."/modify,add,verify.html";
            $ausgaben["form_break"] = $cfg["basis"]."/list.html";

            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".modify";

            // unzugaengliche #(marken) sichtbar machen
            if ( isset($HTTP_GET_VARS["edit"]) ) {
                $ausgaben["inaccessible"] = "inaccessible values:<br />";
                $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
            } else {
                $ausgaben["inaccessible"] = "";
            }

            if ( $environment["parameter"][2] == "verify"
                && $HTTP_POST_VARS["send"] != "" ) {

                // form eingaben prüfen
                form_errors( $form_options, $HTTP_POST_VARS );

                // form eingaben prüfen erweitern
                #n/a

                // gibt es diesen level bereits?
                $sql = "SELECT level
                          FROM ".$cfg["db"]["level"]["entries"]."
                         WHERE level = '".$HTTP_POST_VARS["level"]."'";
                $result  = $db -> query($sql);
                $num_rows = $db -> num_rows($result);
                if ( $num_rows >= 1 ) $ausgaben["form_error"] = "#(error_dupe)";


                // level hinzufuegen
                if ( $ausgaben["form_error"] == "" ) {
                    $sql = "INSERT INTO  ".$cfg["db"]["level"]["entries"]."
                                        (level, beschreibung)
                                    VALUES ('".$HTTP_POST_VARS["level"]."',
                                            '".$HTTP_POST_VARS["beschreibung"]."')";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                }

                // usern mit neuem level versehen
                if ( $ausgaben["form_error"] == "" ) {
                    if ( is_array($HTTP_POST_VARS["avail"]) ) {
                        $lid = $db -> lastid();
                        foreach ($HTTP_POST_VARS["avail"] as $name => $value ) {
                            $sql = "INSERT INTO auth_right (lid, uid) VALUES ('".$lid."', '".$value."')";
                            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                            $db -> query($sql);
                            if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                        }
                    }
                }

                // wohin schicken
                if ( $ausgaben["form_error"] == "" ) {
                    header("Location: ".$cfg["basis"]."/list.html");
                }
            }

        } elseif ( $environment["parameter"][1] == "edit" ) {

            // form values ueber db oder post setzen
            if ( count($HTTP_POST_VARS) == 0 ) {
                $sql = "SELECT * FROM ".$cfg["db"]["level"]["entries"]." WHERE lid='".$environment["parameter"][2]."'";
                $result = $db -> query($sql);
                $form_values = $db -> fetch_array($result,$nop);
            } else {
                $form_values = $HTTP_POST_VARS;
            }

            // form otions holen
            $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

            // form elememte bauen
            $element = form_elements( $cfg["db"]["level"]["entries"], $form_values );

            // form elemente erweitern
            #n/a

            // user management form form elemente begin
            // ***
            $element["add"] = "<input type=\"submit\" name=\"add[]\" value=\"#(add)\">";
            $element["del"] = "<input type=\"submit\" name=\"del[]\" value=\"#(del)\">";
            $element["actual"] = "<select name=\"actual[]\" size=\"10\" multiple>";
            $element["avail"] = "<select name=\"avail[]\" size=\"10\" multiple>";
            # nice sql query tnx@bastard!
            $sql = "SELECT auth_user.uid, auth_user.username, auth_right.lid, auth_right.rid FROM auth_user LEFT JOIN auth_right ON auth_user.uid = auth_right.uid and auth_right.lid = ".$environment["parameter"][2]." ORDER by username";
            $result = $db -> query($sql);
            while ( $all = $db -> fetch_array($result,1) ) {
                if ( $all["lid"] == $environment["parameter"][2] ) {
                    $element["actual"] .= "<option value=\"".$all["rid"]."\">".$all["username"]."</option>\n";
                } else {
                    $element["avail"] .= "<option value=\"".$all["uid"]."\">".$all["username"]."</option>\n";
                }
            }
            $element["actual"] .= "</select>";
            $element["avail"] .= "</select>";
            // +++
            // user management form form elemente end

            // fehlermeldungen
            $ausgaben["form_error"] = "";

            // navigation erstellen
            $ausgaben["form_aktion"] = $cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";
            $ausgaben["form_break"] = $cfg["basis"]."/list.html";

            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".modify";

            // unzugaengliche #(marken) sichtbar machen
            if ( isset($HTTP_GET_VARS["edit"]) ) {
                $ausgaben["inaccessible"] = "inaccessible values:<br />";
                $ausgaben["inaccessible"] .= "# (error_?) #(error_?)<br />";
            } else {
                $ausgaben["inaccessible"] = "";
            }

            if ( $environment["parameter"][3] == "verify"
                &&  ( $HTTP_POST_VARS["send"] != ""
                   || $HTTP_POST_VARS["add"] != ""
                   || $HTTP_POST_VARS["del"] != "" ) ) {

                // form eigaben prüfen
                form_errors( $form_options, $HTTP_POST_VARS );

                // form eingaben prüfen erweitern
                #n/a

                // ohne fehler sql bauen und ausfuehren
                if ( $ausgaben["form_error"] == "" ) {

                    // user hinzufuegen
                    if ( is_array($HTTP_POST_VARS["avail"]) ) {
                        foreach ($HTTP_POST_VARS["avail"] as $name => $value ) {
                            $sql = "INSERT INTO auth_right
                                                (lid, uid)
                                         VALUES ('".$environment["parameter"][2]."',
                                                 '".$value."')";
                            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                            $result = $db -> query($sql);
                            if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                        }
                        if ( $HTTP_POST_VARS["add"] ) {
                            #header("Location: ".$cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html");
                            $header = $cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";
                        }
                    }

                    // user entfernen
                    if ( is_array($HTTP_POST_VARS["actual"]) ) {
                        foreach ($HTTP_POST_VARS["actual"] as $name => $value ) {
                            $sql = "DELETE FROM auth_right where rid='".$value."'";
                            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                            $result = $db -> query($sql);
                            if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                        }
                        if ( $HTTP_POST_VARS["del"] ) {
                            #header("Location: ".$cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html");
                            $header = $cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";
                        }
                    }

                    // level aendern
                    $sql = "UPDATE ".$cfg["db"]["level"]["entries"]."
                               SET level = '".$HTTP_POST_VARS["level"]."',
                                   beschreibung = '".$HTTP_POST_VARS["beschreibung"]."'
                             WHERE lid='".$environment["parameter"][2]."'";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                    if ( $header == "" ) $header = $cfg["basis"]."/list.html";

                    if ( $ausgaben["form_error"] == "" ) {
                        header("Location: ".$header);
                    }
                }
            }

        } elseif ( $environment["parameter"][1] == "delete" ) {

            // ausgaben variablen bauen
            $sql = "SELECT * FROM ".$cfg["db"]["level"]["entries"]." WHERE lid='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $field = $db -> fetch_array($result,$nop);
            foreach($field as $name => $value) {
                $ausgaben[$name] = $value;
            }

            // fehlermeldungen
            #n/a

            // navigation erstellen
            $ausgaben["form_aktion"] = $cfg["basis"]."/modify,delete,".$environment["parameter"][2].".html";
            $ausgaben["form_break"] = $cfg["basis"]."/list.html";

            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".delete";
            $mapping["navi"] = "leer";

            // unzugaengliche #(marken) sichtbar machen
            if ( isset($HTTP_GET_VARS["edit"]) ) {
                $ausgaben["inaccessible"] = "inaccessible values:<br />";
                $ausgaben["inaccessible"] .= "# (error_?) #(error_?)<br />";
            } else {
                $ausgaben["inaccessible"] = "";
            }

            // wohin schicken
            #n/a

            if ( $HTTP_POST_VARS["send"] ) {

                // diesen level bei allen usern loeschen
                $sql = "DELETE FROM auth_right where lid='".$environment["parameter"][2]."'";
                $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");

                // diesen level loeschen
                if ( $ausgaben["form_error"] == "" ) {
                    $sql = "DELETE FROM ".$cfg["db"]["level"]["entries"]." WHERE lid='".$environment["parameter"][2]."'";
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                }

                if ( $ausgaben["form_error"] == "" ) {
                    header("Location: ".$cfg["basis"]."/list.html");
                }
            }
        }

    //
    // Details anzeigen
    //
    } elseif ( $environment["kategorie"] == "details" && $rechte[$cfg["right"]] == -1 ) {

        // ausgaben variablen bauen
        $sql = "SELECT * FROM ".$cfg["db"]["level"]["entries"]." WHERE lid='".$environment["parameter"][1]."'";
        $result = $db -> query($sql);
        $field = $db -> fetch_array($result,$nop);
        foreach($field as $name => $value) {
            $ausgaben[$name] = $value;
        }

        // user management form form elemente begin
        // ***
        $sql = "SELECT auth_right.lid, auth_user.username FROM auth_user INNER JOIN auth_right ON auth_user.uid = auth_right.uid WHERE auth_right.lid = ".$environment["parameter"][1]." order by username";
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            if ( isset($ausgaben["users"]) ) $ausgaben["users"] .= ", ";
            $ausgaben["users"] .= $all["username"]."";
        }
        if ( !isset($ausgaben["users"]) ) $ausgaben["users"] = "---";
        // +++
        // user management form form elemente begin

        // ausgaben anpassen
        $ldate = $ausgaben["ldate"];
        $ausgaben["ldate"] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);
        $ausgaben["ldetail"] = nlreplace($ausgaben["ldetail"]);

        // fehlermeldungen
        #n/a

        // navigation erstellen
        $ausgaben["link_edit"] = $cfg["basis"]."/modify,edit,".$environment["parameter"][1].".html\"";
        $ausgaben["link_list"] = $cfg["basis"]."/list.html\"";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".details";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            #$ausgaben["inaccessible"] .= "# (error_?) #(error_?)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

    //
    // Liste anzeigen
    //
    } elseif ( $environment["kategorie"] == "list" && $rechte[$cfg["right"]] == -1 ) {

        // inhalt selector init
        $sql = "SELECT * FROM ".$cfg["db"]["level"]["entries"].$where." ORDER by level";
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["rows"], $parameter );
        $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
        $sql = $inhalt_selector[1];

        // tabellen spiel
        $ausgaben["output"] .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";

        $ausgaben["output"] .= "<tr>";
        $class = " class=\"lines\"";
        $ausgaben["output"] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
        $ausgaben["output"] .= "</tr>";

        $class = " class=\"contenthead\"";
        $size  = " width=\"30\"";

        $ausgaben["output"] .= "<td".$class.">#(level)</td>\n";
        $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>\n";
        $ausgaben["output"] .= "<td".$class.">#(beschreibung)</td>\n";
        $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>\n";
        $ausgaben["output"] .= "<td".$class.">#(modify)</td>\n";
        $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>\n";
        $ausgaben["output"] .= "</tr><tr>\n";

        $class = " class=\"lines\"";

        $ausgaben["output"] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars["images"]."pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>\n";
        $ausgaben["output"] .= "</tr>\n";


        $result = $db -> query($sql);
        $modify  = array (
            "edit"      => array("modify,", "#(edit)", "cms_admin"),
            "delete"    => array("modify,", "#(delete)", "cms_admin"),
            "details"   => array("", "#(details)")
        );

        while ( $field = $db -> fetch_array($result,$nop) ) {

            // tabellen farben wechseln
            if ( $cfg["color"]["set"] == $cfg["color"]["a"]) {
                $cfg["color"]["set"] = $cfg["color"]["b"];
            } else {
                $cfg["color"]["set"] = $cfg["color"]["a"];
            }

            $ausgaben["output"] .= "<tr bgcolor=\"".$cfg["color"]["set"]."\">\n";

            $class = " class=\"contenttabs\"";
            $size  = " width=\"30\"";

            $ausgaben["output"] .= "<td".$class.">".$field["level"]."</td>\n";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>\n";
            $ausgaben["output"] .= "<td".$class.">".substr($field["beschreibung"],0,20)." ...</td>\n";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>\n";

            $aktion = "";
            foreach($modify as $name => $value) {
                if ( $value[2] == "" || $rechte[$value[2]] == -1) {
                    $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$field["lid"].".html\"><img src=\"".$cfg["iconpath"].$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                } else {
                    $aktion .= "<img src=\"".$cfg["iconpath"]."pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                }
            }
            $ausgaben["output"] .= "<td".$class.">".$aktion."</td>\n";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>\n";
            $ausgaben["output"] .= "</tr><tr>";
            $class = " class=\"lines\"";
            $ausgaben["output"] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>\n";
            $ausgaben["output"] .= "</tr>";
        }
        $ausgaben["output"] .= "</table>\n";

        // navigation erstellen
        $ausgaben["link_new"] = $cfg["basis"]."/modify,add.html";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".list";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            #$ausgaben["inaccessible"] .= "# (error_?) #(error_?)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>