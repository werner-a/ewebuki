<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "$Id$";
  $Script_desc = "Menu Management Applikation (alpha)";
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

    // content umschaltung verhindern
    $specialvars["dynlock"] = True;

    if ( $rechte[$cfg["right"]["admin"]] == -1 ) {

        if ( $cfg["db"]["change"] == -1 ) {
            // lokale db auswaehlen
            if ( $environment["fqdn"][0] == $specialvars["dyndb"] && in_array($specialvars["dyndb"],$HTTP_SESSION_VARS["dbzugriff"]) ) {
                $db->selectDb($specialvars["dyndb"],FALSE);
            } elseif ( $environment["fqdn"][0] == $cfg["fqdn0"] && $HTTP_SESSION_VARS["sti"] == -1 ) {
                ### loesung?
            } else {
                $sql = "SELECT adakz FROM db_adrd where adid='".$HTTP_SESSION_VARS["custom"]."'";
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,$nop);
                $db->selectDb("intra".$data["adakz"],FALSE);
            }
        }

        $ausgaben["menuname"] = $db->getDb();

        //
        // rekursive renumber funktion
        //
        function renumber($mt, $refid, $rekursiv) {
            global $environment, $debugging, $db;
            $mtl = $mt."_lang";
            $sql = "SELECT $mt.mid, $mt.entry, $mt.refid, $mt.level, $mt.sort, $mtl.lang, $mtl.label, $mtl.exturl FROM $mt INNER JOIN $mtl ON $mt.mid = $mtl.mid WHERE ((($mt.refid)=$refid) AND (($mtl.lang)='".$environment["language"]."')) order by sort, label;";
            $menuresult  = $db -> query($sql);
            while ( $menuarray = $db -> fetch_array($menuresult,1) ) {
                $sort += 10;
                $sql = "UPDATE ".$mt." SET sort=".$sort." WHERE mid='".$menuarray["mid"]."'";
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $db -> query($sql);
                if ( $rekursiv == -1 ) renumber($mt, $menuarray["mid"], $rekursiv);
            }
        }


        //
        // Bearbeiten
        //
        if ( strstr($environment["kategorie"], "modify") ) {

            // warning ausgeben
            if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];


            if ( $environment["parameter"][1] == "add" && $rechte[$cfg["right"]["admin"]] == -1 ) {

                // form options holen
                $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

                // form elememte bauen
                $element = form_elements( $db_entries, $HTTP_POST_VARS );

                // form elemente erweitern
                if ( $HTTP_POST_VARS["refid"] == "" ) {
                    $value = $environment["parameter"][2];
                } else {
                    $value = $HTTP_POST_VARS["refid"];
                }
                $element["refid"] = str_replace("refid\"","refid\" value=\"".$value."\" readonly",$element["refid"]);
                $element["new_lang"] = "<input name=\"new_lang\" type=\"text\" maxlength=\"3\" size=\"3\" value=\"n/a\" readonly>";

                // lang management form elemente begin
                // ***
                $ausgaben["langtabelle"]  = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
                $ausgaben["langtabelle"] .= "<tr><td>Sprache</td><td>angezeigter Menutext</td><td>externer Link</td><td>&nbsp;</td></tr>\n";
                $element_lang = form_elements( $db_entries_lang, $HTTP_POST_VARS );
                $ausgaben["langtabelle"] .= "<tr>";
                $ausgaben["langtabelle"] .= "<td>".$element_lang["lang"]."</td>";
                $ausgaben["langtabelle"] .= "<td>".$element_lang["label"]."</td>";
                $ausgaben["langtabelle"] .= "<td>".$element_lang["exturl"]."</td>";
                $ausgaben["langtabelle"] .= "<td>";
                #$ausgaben["langtabelle"] .= "<input name=\"edit\" type=\"image\" src=\"".$pathvars["images"]."edit.png\" width=\"24\" height=\"18\" border=\"0\" value=\"".$lang["mlid"]."\">";
                #$ausgaben["langtabelle"] .= "<input name=\"delete\" type=\"image\" src=\"".$pathvars["images"]."delete.png\" width=\"24\" height=\"18\" border=\"0\" value=\"".$lang["mlid"]."\">";
                $ausgaben["langtabelle"] .= "</td></tr>";
                $ausgaben["langtabelle"] .= "</table>";
                // +++
                // lang management form elemente end

                // was anzeigen
                $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];

                // wohin schicken
                $ausgaben["form_error"] = "";
                $ausgaben["form_aktion"] = $environment["basis"]."/".$environment["kategorie"].",add,verify.html";

                // referer im form mit hidden element mitschleppen
                if ( $HTTP_GET_VARS["referer"] != "" ) {
                    $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
                    $ausgaben["form_break"] = $ausgaben["form_referer"];
                } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
                    $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                    $ausgaben["form_break"] = $ausgaben["form_referer"];
                } else {
                    $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                    $ausgaben["form_break"] = $ausgaben["form_referer"];
                }

                if ( $environment["parameter"][2] == "verify" ) {

                    // form eigaben prüfen
                    form_errors( $form_options, $HTTP_POST_VARS );

                    // ohne fehler sql bauen und ausfuehren
                    if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" || $HTTP_POST_VARS["add"] ) ) {
                        $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "add_x", "add_y", "add", "form_referer", "lang", "label", "exturl", "new_lang", "entry");
                        foreach($HTTP_POST_VARS as $name => $value) {
                            if ( !in_array($name,$kick) ) {
                                if ( $sqla != "" ) $sqla .= ",";
                                $sqla .= " ".$name;
                                if ( $sqlb != "" ) $sqlb .= ",";
                                $sqlb .= " '".$value."'";
                            }
                        }

                        // Sql um spezielle Felder erweitern
                        $entry = strtolower($HTTP_POST_VARS["entry"]);
                        $entry = str_replace(" ", "", $entry);
                        $sqla .= ", entry";
                        $sqlb .= ", '".$entry."'";
                        #$ldate = $HTTP_POST_VARS["ldate"];
                        #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                        #$sqla .= ", refid";
                        #$sqlb .= ", '".$environment["parameter"][2]."'";

                        $sql = "insert into ".$db_entries." (".$sqla.") VALUES (".$sqlb.")";
                        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $result  = $db -> query($sql);

                        // lang management form elemente begin
                        // ***
                        if ( $result ) {
                            $lastid = $db -> lastid();
                            $sql = "insert into ".$db_entries_lang." (mid, lang, label) VALUES ('".$lastid."', '".$HTTP_POST_VARS["lang"]."', '".$HTTP_POST_VARS["label"]."' )";
                            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                            $result  = $db -> query($sql);
                            if ( $HTTP_POST_VARS["add"] ) {
                                header("Location: ".$environment["basis"]."/modify,edit,".$lastid.",verify.html?referer=".$ausgaben["form_referer"]);
                            } else {
                                header("Location: ".$ausgaben["form_referer"]);
                            }
                        }
                        // +++
                        // lang management form elemente end
                    }
                }

            } elseif ( $environment["parameter"][1] == "edit" && $rechte[$cfg["right"]["admin"]] == -1 ) {
                #echo $db->getDb();
                if ( count($HTTP_POST_VARS) == 0 ) {
                    $sql = "SELECT * FROM ".$db_entries." WHERE ".$db_entries_key."='".$environment["parameter"][2]."'";
                    $result = $db -> query($sql);
                    $form_values = $db -> fetch_array($result,$nop);
                } else {
                    $form_values = $HTTP_POST_VARS;
                }

                // form otions holen
                $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

                // form elememte bauen
                $element = form_elements( $db_entries, $form_values );

                // form elemente erweitern
                $element["new_lang"] = "<input name=\"new_lang\" type=\"text\" maxlength=\"3\" size=\"3\">";

                // lang management form elemente begin
                // ***
                $ausgaben["langtabelle"]  = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
                $ausgaben["langtabelle"] .= "<tr><td>Sprache</td><td>angezeigter Menutext</td><td>externer Link</td><td>&nbsp;</td></tr>\n";
                $sql = "SELECT * FROM ".$db_entries_lang." where mid=".$environment["parameter"][2]." ORDER by lang";
                $result = $db -> query($sql);
                while ( $lang = $db -> fetch_array($result,1) ) {
                    $element_lang = form_elements( $db_entries_lang, $lang );
                    $ausgaben["langtabelle"] .= "<tr>";
                    $ausgaben["langtabelle"] .= "<td>".str_replace("name=\"","name=\"".$lang["mlid"].")",$element_lang["lang"])."</td>";
                    $ausgaben["langtabelle"] .= "<td>".str_replace("name=\"","name=\"".$lang["mlid"].")",$element_lang["label"])."</td>";
                    $ausgaben["langtabelle"] .= "<td>".str_replace("name=\"","name=\"".$lang["mlid"].")",$element_lang["exturl"])."</td>";
                    $ausgaben["langtabelle"] .= "<td>";
                    $ausgaben["langtabelle"] .= "<input name=\"edit\" type=\"image\" src=\"".$pathvars["images"]."edit.png\" width=\"24\" height=\"18\" border=\"0\" value=\"".$lang["mlid"]."\">";
                    $ausgaben["langtabelle"] .= "<input name=\"delete\" type=\"image\" src=\"".$pathvars["images"]."delete.png\" width=\"24\" height=\"18\" border=\"0\" value=\"".$lang["mlid"]."\">";
                    $ausgaben["langtabelle"] .= "</td></tr>";
                }
                $ausgaben["langtabelle"] .= "</table>";
                // +++
                // lang management form elemente end

                // was anzeigen
                $mapping["main"] = crc32($environment["ebene"]).".modify";

                // wohin schicken
                $ausgaben["form_error"] = "";
                $ausgaben["form_aktion"] = $environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";

                // referer im form mit hidden element mitschleppen
                if ( $HTTP_GET_VARS["referer"] != "" ) {
                    $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
                    $ausgaben["form_break"] = $ausgaben["form_referer"];
                } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
                    $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                    $ausgaben["form_break"] = $ausgaben["form_referer"];
                } else {
                    $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                    $ausgaben["form_break"] = $ausgaben["form_referer"];
                }

                if ( $environment["parameter"][3] == "verify" ) {

                    // form eigaben prüfen
                    form_errors( $form_options, $HTTP_POST_VARS );

                    // lang management form elemente begin
                    // ***
                    if ( $HTTP_POST_VARS["add"] ) {
                        $sql = "insert into ".$db_entries_lang." (mid, lang, label) VALUES ('".$environment["parameter"][2]."', '".$HTTP_POST_VARS["new_lang"]."', '".$HTTP_POST_VARS["entry"]."' )";
                        $result  = $db -> query($sql);
                        header("Location: ".$environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html?referer=".$ausgaben["form_referer"]);
                    } elseif ( $HTTP_POST_VARS["edit"] ) {
                        $sql = "update ".$db_entries_lang." set label='".$HTTP_POST_VARS[$HTTP_POST_VARS["edit"].")label"]."', exturl='".$HTTP_POST_VARS[$HTTP_POST_VARS["edit"].")exturl"]."' where mlid=".$HTTP_POST_VARS["edit"];
                        $result  = $db -> query($sql);
                        header("Location: ".$environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html?referer=".$ausgaben["form_referer"]);
                    } elseif ( $HTTP_POST_VARS["delete"] ) {
                        $sql = "delete from ".$db_entries_lang." where mlid=".$HTTP_POST_VARS["delete"];
                        $result  = $db -> query($sql);
                        header("Location: ".$environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html?referer=".$ausgaben["form_referer"]);
                    }
                    // +++
                    // lang management form elemente end

                    // ohne fehler sql bauen und ausfuehren
                    if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) ){

                        $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer", "new_lang", "entry" );
                        foreach($HTTP_POST_VARS as $name => $value) {
                            if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                                if ( $sqla != "" ) $sqla .= ", ";
                                $sqla .= $name."='".$value."'";
                            }
                        }

                        // Sql um spezielle Felder erweitern
                        $entry = strtolower($HTTP_POST_VARS["entry"]);
                        $entry = str_replace(" ", "", $entry);
                        $sqla .= ", entry='".$entry."'";
                        #$ldate = $HTTP_POST_VARS["ldate"];
                        #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                        #$sqla .= ", ldate='".$ldate."'";

                        $sql = "update ".$db_entries." SET ".$sqla." WHERE ".$db_entries_key."='".$environment["parameter"][2]."'";
                        $result  = $db -> query($sql);
                        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        if ( $result ) {
                            header("Location: ".$ausgaben["form_referer"]);
                        }
                    }
                }

            } elseif ( $environment["parameter"][1] == "delete" && $rechte[$cfg["right"]["admin"]] == -1 ) {

                // ausgaben variablen bauen
                $sql = "SELECT * FROM ".$db_entries." WHERE ".$db_entries_key."='".$environment["parameter"][2]."'";
                $result = $db -> query($sql);
                $field = $db -> fetch_array($result,$nop);
                foreach($field as $name => $value) {
                    $ausgaben[$name] = $value;
                }

                // was anzeigen
                $mapping["main"] = crc32($environment["ebene"]).".delete";
                $mapping["navi"] = "leer";

                // wohin schicken
                $ausgaben["form_error"] = "";
                $ausgaben["form_aktion"] = $environment["basis"]."/modify,delete,".$environment["parameter"][2].".html";
                $ausgaben["form_break"] = $_SERVER["HTTP_REFERER"];

                if ( $HTTP_POST_VARS["delete"] == "true" ) {
                    $sql = "SELECT ".$db_entries_key." FROM ".$db_entries." WHERE refid='".$environment["parameter"][2]."'";
                    $result = $db -> query($sql);
                    $num_rows = $db -> num_rows($result);
                    if ( $num_rows > 0 ) {
                        $ausgaben["form_error"] = "Loeschen nicht möglich, löschen Sie zuerst die Unterpunkte!";
                    } else {
                        $sql = "DELETE FROM ".$db_entries." WHERE ".$db_entries_key."='".$environment["parameter"][2]."'";
                        $result  = $db -> query($sql);
                        if ( $result ) {
                            $sql = "DELETE FROM ".$db_entries_lang." WHERE mid='".$environment["parameter"][2]."'";
                            $result  = $db -> query($sql);
                            if ( $result ) {
                                header("Location: ".$environment["basis"]."/list.html");
                            }
                        }
                    }
                }
            }
        //
        // reihenfolge aendern
        //
        } elseif ( $environment["kategorie"] == "move"  && $rechte[$cfg["right"]["admin"]] == -1 ) {

            if ( $environment["parameter"][1] == "up" ) {
                $sql = "UPDATE ".$db_entries." SET sort=sort-11 WHERE mid='".$environment["parameter"][2]."'";
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $db -> query($sql);
            } elseif ( $environment["parameter"][1] == "down" ) {
                $sql = "UPDATE ".$db_entries." SET sort=sort+11 WHERE mid='".$environment["parameter"][2]."'";
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $db -> query($sql);
            }
            renumber($db_entries, $environment["parameter"][3], 0);
            header("Location: ".$environment["basis"]."/list.html");

        //
        // neu numerieren der sort reihenfolge
        //
        } elseif ( $environment["kategorie"] == "renumber" && $rechte[$cfg["right"]["admin"]] == -1 ) {

            #renumber($environment["parameter"][1], $environment["parameter"][2], $environment["parameter"][3]);
            renumber($db_entries, $environment["parameter"][2], $environment["parameter"][3]);
            header("Location: ".$environment["basis"]."/list.html");

        //
        // Liste anzeigen
        //
        } elseif ( $environment["kategorie"] == $environment["name"] || $environment["kategorie"] == "list" ) {

            $mt = $db_entries;
            $mtl = $db_entries_lang;
            $refid = 0;
            #$ausgaben["output"] = "|<br>";

            function sitemap($refid) {
                global $environment, $db, $mt, $mtl, $pathvars, $specialvars, $rechte, $ast, $astpath, $lokal;
                $sql = "SELECT $mt.mid, $mt.entry, $mt.refid, $mt.level, $mt.sort, $mtl.lang, $mtl.label, $mtl.exturl FROM $mt INNER JOIN $mtl ON $mt.mid = $mtl.mid WHERE ((($mt.refid)=$refid) AND (($mtl.lang)='".$environment["language"]."')) order by sort, label;";
                #echo $sql;
                $menuresult  = $db -> query($sql);

                $modify  = array (
                    "add"       => array("modify,", "Hinzufügen", $cfg["right"]["admin"]),
                    "edit"      => array("modify,", "Editieren", $cfg["right"]["admin"]),
                    "delete"    => array("modify,", "Löschen", $cfg["right"]["admin"]),
                    "up"        => array("move,", "Nach oben", $cfg["right"]["admin"]),
                    "down"      => array("move,", "Nach unten", $cfg["right"]["admin"])
                );
                $imgpath = $pathvars["images"];

                while ( $menuarray = $db -> fetch_array($menuresult,1) ) {
                    if ( $menuarray["level"] == "" ) {
                        $right = -1;
                    } else {
                        if ( $rechte[$menuarray["level"]] == -1 ) {
                            $right = -1;
                        } else {
                            $right = 0;
                        }
                    }
                    if ( $right == -1 ) {
                        if ( $refid == 0 ) {
                            $ast = array(0);
                            $astpath = array($menuarray["entry"]);
                        }
                        // ast einruecken
                        if ( !in_array($refid, $ast, TRUE) ) {
                            $ast[] = $refid;
                            $astpath[] = $menuarray["entry"];
                            $tiefe = array_search($refid, $ast, TRUE);
                        // ast ausruecken bzw. auf dem aktuellen wert setzen
                        } else {
                            // aktuellen wert loeschen
                            array_pop($ast);
                            array_pop($astpath);

                            // evtl. ast ausruecken
                            if ( array_search($refid, $ast, TRUE) >= 1 ) {
                              array_pop($ast);
                              array_pop($astpath);
                            }
                            // aktuellen wert setzen
                            $ast[] = $refid;
                            $astpath[] = $menuarray["entry"];
                            $tiefe = array_search($refid, $ast, TRUE);
                        }
                        // tiefe in anzeige wandeln
                        $path = "";
                        $level = "";
                        for ( $i=0 ; $i < $tiefe ; $i++ ) {
                           $path .= $astpath[$i]."/";
                           $level .= "__________";
                        }

                        $aktion = "";

                        foreach($modify as $name => $value) {
                            if ( $name == "up" || $name == "down" ) {
                                if ( $menuarray["refid"] == 0 ) {
                                    $ankerpos = "<a name=\"".$menuarray["mid"]."\"</a>";
                                    $ankerlnk = "#".$menuarray["mid"];
                                } else {
                                    #$anker   = "#".$ankerid;
                                    $ankerpos = "";
                                    $ankerlnk = "#".$ast[1];
                                }
                            } else {
                                $ankerlnk = "";
                            }
                            if ( $value[2] == "" || $rechte[$value[2]] == -1 ) {
                                $aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$menuarray["mid"].",".$menuarray["refid"].".html".$ankerlnk."\"><img src=\"".$pathvars["images"].$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                            } else {
                                $aktion .= "<img src=\"".$imgpath."pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                            }
                        }

                        #echo "<pre>";
                        #print_r($ast);
                        #echo ">".$menuarray["label"].":".$menuarray["entry"];
                        #echo "</pre>";

                        #$tree .= "<tr><td>".$tet.$level."<a class=\"\" href=\"".$pathvars["virtual"]."/".$path.$menuarray["entry"].".html\">".$menuarray["label"]."</a></td><td>".$menuarray["entry"]."</td><td>m: ".$menuarray["mid"]."</td><td>r: ".$menuarray["refid"]./*"</td><td>s: ".$menuarray["sort"].*/"</td><td>".$aktion."</td></tr>";
                        if ( $level == "" ) $menuarray["label"] = "<b>".$menuarray["label"]."</b>";
                        $tree .= "<tr><td>".$level.$ankerpos."<a class=\"\" href=\"".$pathvars["virtual"]."/".$path.$menuarray["entry"].".html\"><img src=\"".$pathvars["images"]."sitemap.png\" width=\"16\" height=\"16\" align=\"absbottom\" border=\"0\"><img src=\"".$pathvars["images"]."pos.png\" width=\"3\" height=\"1\" align=\"absbottom\" border=\"0\">".$menuarray["label"]."</a></td><td>".$aktion."</td></tr>";
                        $tree .= sitemap($menuarray["mid"]);
                    }
                }

                return $tree;
            }

            $ausgaben["output"] = "<table width=\"100%\">";
            $ausgaben["output"] .= sitemap($refid);
            $ausgaben["output"] .= "</table>";

            $mapping["main"] = crc32($environment["ebene"]).".list";
        }

        if ( $cfg["db"]["change"] == -1 ) {
            // globale db auswaehlen
                $db -> selectDb(DATABASE,FALSE);
        }

    } else {
        header("Location: ".$pathvars["webroot"]."/".$environment["design"]."/".$environment["language"]."/index.html");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
