<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "path.inc.php v1 chaot";
    $Script_desc = "path handling: 'kekse', [UP], [M0], [M1], [M2], [PREV], [NEXT], !#lnk* + 404 handling";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

    // datenbank wechseln -> variablen in menuctrl.inc.php
    if ( $environment["fqdn"][0] == @$specialvars["dyndb"] ) {
        $db->selectDb($specialvars["dyndb"],FALSE);
        $specialvars["rootname"] = $db->getDb();
    }

    // altes verhalten wiederherstellen
    if ( !isset($defaults["split"]["title"]) ) $defaults["split"]["title"] = " - " ;
    if ( !isset($defaults["split"]["kekse"]) ) $defaults["split"]["kekse"] = " - ";
    if ( !isset($defaults["split"]["m1"]) ) $defaults["split"]["m0"] = " &middot; ";
    if ( !isset($defaults["split"]["m1"]) ) $defaults["split"]["m1"] = " &middot; ";
    if ( !isset($defaults["split"]["m2"]) ) $defaults["split"]["m2"] = " &middot; ";
    if ( !isset($defaults["split"]["l0"]) ) $defaults["split"]["l0"] = " &middot; ";
    if ( !isset($defaults["split"]["l1"]) ) $defaults["split"]["l1"] = " &middot; ";
    if ( !isset($defaults["split"]["l2"]) ) $defaults["split"]["l2"] = " &middot; ";

    $ausgaben["M0"] = null;
    $ausgaben["M1"] = null;
    $ausgaben["M2"] = null;
    $ausgaben["L0"] = null;
    $ausgaben["L1"] = null;

    // dynamic style - db test/extension
    $sql = "select dynamiccss from ".$cfg["path"]["db"]["menu"]["entries"];
    @$result = $db -> query($sql);
    $dynamiccss = null;
    if ( $result ) $dynamiccss = $cfg["path"]["db"]["menu"]["entries"].".dynamiccss, ";

    // dynamic bg - db test/extension
    $sql = "select dynamicbg from ".$cfg["path"]["db"]["menu"]["entries"];
    @$result = $db -> query($sql);
    $dynamicbg = null;
    if ( $result ) $dynamicbg = $cfg["path"]["db"]["menu"]["entries"].".dynamicbg, ";

    // zusaetzliche informationen aus den feld extended (muss vorhanden sein!)
    $extenddesc = null;
    if ( $cfg["path"]["ext_info"] == "-1" ) $extenddesc = $cfg["path"]["db"]["lang"]["entries"].".extend, ";

    // disable pdf - db test/extension
    $sql = "select disablepdf from ".$cfg["path"]["db"]["menu"]["entries"];
    @$result = $db -> query($sql);
    $disablepdf = null;
    if ( $result ) $disablepdf = $cfg["path"]["db"]["menu"]["entries"].".disablepdf, ";

    // link zum webroot
    if ( $cfg["pdfc"]["state"] == true ) {
        $KekseServerName = $_SERVER["SERVER_NAME"];
        if ( !empty($cfg["pdfc"]["server_name"]) ) {
            $KekseServerName = $cfg["pdfc"]["server_name"];
        }
        $kekse["html"][] = "<a href=\"http://".$KekseServerName.$pathvars["virtual"]."/index.html\" title=\"".$specialvars["rootname"]."\" class=\"".$cfg["path"]["css"]["crumb"]."\">".$specialvars["rootname"]."</a>";
    } else {
        $kekse["html"][] = "<a href=\"".$pathvars["virtual"]."/index.html\" title=\"".$specialvars["rootname"]."\" class=\"".$cfg["path"]["css"]["crumb"]."\">".$specialvars["rootname"]."</a>";
    }
    $kekse["label"][] = $specialvars["rootname"];
    $kekse["title"][] = $specialvars["rootname"];
    $kekse["link"][]  = $pathvars["virtual"]."/index.html";

    // kekspath splitten und fuer jede ebene die beschreibung holen
    $kekspath = substr( $environment["ebene"]."/".$environment["kategorie"], 1);
    $kekspath = explode('/', $kekspath);

    // navi tags und marken
    $ausgaben["UP"] = $cfg["path"]["menuroot"];
    $lnk[0] = $cfg["path"]["menuroot"];

    // 404 error handling
    $count_url = count($kekspath);
    $count_menu = 0;

    $actid = 0;
    $path = null;
    $ausgaben["pdfbutton0"] = null;
    $ausgaben["pdfbutton1"] = null;
    $ausgaben["pdfbutton2"] = null;

    foreach ($kekspath as $value) {
        $search = "like '".preg_replace("/[^A-Za-z_\-\.0-9]+/", "", $value)."'";
        $sql = "SELECT ".$cfg["path"]["db"]["menu"]["entries"].".mid,
                       ".$cfg["path"]["db"]["menu"]["entries"].".refid,
                       ".$cfg["path"]["db"]["menu"]["entries"].".entry,
                       ".$cfg["path"]["db"]["menu"]["entries"].".sort,
                       ".$cfg["path"]["db"]["menu"]["entries"].".level,
                       ".$cfg["path"]["db"]["menu"]["entries"].".defaulttemplate,
                       ".$dynamiccss.$dynamicbg.$extenddesc.$disablepdf."
                       ".$cfg["path"]["db"]["lang"]["entries"].".label
                  FROM ".$cfg["path"]["db"]["menu"]["entries"]."
            INNER JOIN ".$cfg["path"]["db"]["lang"]["entries"]."
                    ON ".$cfg["path"]["db"]["menu"]["entries"].".mid = ".$cfg["path"]["db"]["lang"]["entries"].".mid
                 WHERE ".$cfg["path"]["db"]["menu"]["entries"].".entry ".$search."
                   AND ".$cfg["path"]["db"]["menu"]["entries"].".refid = '".$actid."'
                   AND ".$cfg["path"]["db"]["lang"]["entries"].".lang='".$environment["language"]."';";
        #if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        if ( $db -> num_rows($result) == 1 ) {
            $data = $db -> fetch_array($result,1);
            if ( $data["level"] != "" && !priv_check('', $data["level"]) ) break;
            // gefundene eintraege
            $count_menu++;

            // refid setzen um richtigen eintrag zu finden
            $refid = $actid;
            $actid = $data["mid"];

            // prev + next handling
            // ***
            #echo "other: ".$data["mid"]." - ".$data["refid"]." - ".$data["sort"]." - ".$data["entry"]." - ".$data["label"]."<br />";
            $tet = $data["sort"] + 10;
            $sql = "SELECT ".$cfg["path"]["db"]["menu"]["entries"].".mid,
                        ".$cfg["path"]["db"]["menu"]["entries"].".refid,
                        ".$cfg["path"]["db"]["menu"]["entries"].".entry,
                        ".$cfg["path"]["db"]["menu"]["entries"].".sort,
                        ".$cfg["path"]["db"]["menu"]["entries"].".level,
                        ".$cfg["path"]["db"]["menu"]["entries"].".defaulttemplate,
                        ".$dynamiccss.$dynamicbg.$extenddesc.$disablepdf."
                        ".$cfg["path"]["db"]["lang"]["entries"].".label
                   FROM ".$cfg["path"]["db"]["menu"]["entries"]."
             INNER JOIN ".$cfg["path"]["db"]["lang"]["entries"]."
                     ON ".$cfg["path"]["db"]["menu"]["entries"].".mid = ".$cfg["path"]["db"]["lang"]["entries"].".mid
                  WHERE ".$cfg["path"]["db"]["menu"]["entries"].".sort = ".$tet."
                    AND ".$cfg["path"]["db"]["menu"]["entries"].".refid = '".$data["refid"]."'
                    AND ".$cfg["path"]["db"]["lang"]["entries"].".lang='".$environment["language"]."';";
            $result = $db -> query($sql);
            if ( $db -> num_rows($result) == 1 ) {
                $data2 = $db -> fetch_array($result,1);
                $upper[] = array( "mid" => $data2["mid"], "entry" => $data2["entry"], "label" => $data2["label"] );
            }
            //
            // prev + next handling

            // navbar links
            if ( empty($path) ) {
                $ausgaben["UP"] = $pathvars["virtual"]."/index.html";
            } else {
                $ausgaben["UP"] = $pathvars["virtual"].$path.".html";
            }

            // seitentitel
            $path .= "/".$data["entry"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "path: ".$path.$debugging["char"];
            if ( !isset($cfg["path"]["title_reverse"]) ) $cfg["path"]["title_reverse"] = null;
            if ( $cfg["path"]["title_reverse"] == -1 ) {
                $specialvars["pagetitle"] = $data["label"].$defaults["split"]["title"].$specialvars["pagetitle"];
            } else {
                $specialvars["pagetitle"] .= $defaults["split"]["title"].$data["label"];
            }
            if ( isset($data["extend"]) ) {
                $title = $data["extend"];
            } else {
                $title = $data["label"];
            }

            // pdf button disabled?
            if ( array_key_exists("disablepdf", $data) && ( $data["disablepdf"] != -1 && $path == $environment["ebene"]."/".$environment["kategorie"] )) {
                $ausgaben["pdfbutton0"] = $cfg["pdfc"]["buttons"]["b0"].$ausgaben["auth_url"].$cfg["pdfc"]["buttons"]["e0"];
                $ausgaben["pdfbutton1"] = $cfg["pdfc"]["buttons"]["b1"].$ausgaben["auth_url"].$cfg["pdfc"]["buttons"]["e1"];
                $ausgaben["pdfbutton2"] = $cfg["pdfc"]["buttons"]["b2"].$ausgaben["auth_url"].$cfg["pdfc"]["buttons"]["e2"];
            }

            // aktuelle seite?
            if ( $path == $environment["ebene"]."/".$environment["kategorie"] ) {
                $actual = true;
                $kekse["active"]["mid"] = $actid;
                $css = $cfg["path"]["css"]["last"];
            } else {
                $actual = false;
                $css = $cfg["path"]["css"]["crumb"];
            }

            // kekse array bauen
            if ( $cfg["pdfc"]["state"] == true ) {
                $kekse["html"][] = "<a href=\"http://".$KekseServerName.$pathvars["virtual"].$path.".html\" title=\"".$title."\" class=\"".$css."\">".$data["label"]."</a>";
            } else {
                $kekse["html"][] = "<a href=\"".$pathvars["virtual"].$path.".html\" title=\"".$title."\" class=\"".$css."\">".$data["label"]."</a>";
            }
            $kekse["label"][] = $data["label"];
            $kekse["title"][] = $title;
            $kekse["link"][]  = $pathvars["virtual"].$path.".html";

            // wenn konfiguriert und keine pdf seite, aktuelle seite als ohne link ersetzen
            if ( $cfg["path"]["link_last"] != "-1" && $cfg["pdfc"]["state"] != true && $actual == true ) {
                end($kekse["html"]);
                $kekse["html"][key($kekse["html"])]  = "<span class=\"".$css."\">".$data["label"]."</span>";
            }

            // variables template laut menueintrag setzen
            $specialvars["default_template"] = $data["defaulttemplate"];

            // variables css file - erweiterung laut menueintrag setzen
            if ( isset($data["dynamiccss"]) ) {
                $specialvars["dynamiccss"] = $data["dynamiccss"];
            }

            // variables bg bild - erweiterung laut menueintrag setzen
            if ( isset($data["dynamicbg"]) ) {
                $specialvars["dynamicbg"] = $data["dynamicbg"];
            }

            // content navigation erstellen
            // ***
            if ( !isset($back) ) $back = null;
            $ausgaben["M3"] = eCRC($path)." <a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$back.".html\">Zur�ck</a>";

            // M0 -> ebene darueber
            if ( $path == $environment["ebene"] ) {
                $sql = "SELECT  ".$cfg["path"]["db"]["menu"]["entries"].".mid,
                                ".$cfg["path"]["db"]["menu"]["entries"].".refid,
                                ".$cfg["path"]["db"]["menu"]["entries"].".entry,
                                ".$cfg["path"]["db"]["menu"]["entries"].".sort,
                                ".$cfg["path"]["db"]["menu"]["entries"].".level,
                                ".$cfg["path"]["db"]["lang"]["entries"].".lang,
                                ".$cfg["path"]["db"]["lang"]["entries"].".label,
                                ".$cfg["path"]["db"]["lang"]["entries"].".exturl
                          FROM  ".$cfg["path"]["db"]["menu"]["entries"]."
                    INNER JOIN  ".$cfg["path"]["db"]["lang"]["entries"]."
                            ON  ".$cfg["path"]["db"]["menu"]["entries"].".mid = ".$cfg["path"]["db"]["lang"]["entries"].".mid
                         WHERE (
                               (".$cfg["path"]["db"]["menu"]["entries"].".refid=".$refid.")
                           AND (".$cfg["path"]["db"]["menu"]["entries"].".hide <> '-1' OR ".$cfg["path"]["db"]["menu"]["entries"].".hide is NULL)
                           AND (".$cfg["path"]["db"]["lang"]["entries"].".lang='".$environment["language"]."')
                               )
                      ORDER BY  ".$cfg["path"]["db"]["menu"]["order"].";";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $navbarresult  = $db -> query($sql);
                while ( $navbararray = $db -> fetch_array($navbarresult,1) ) {

                    if ( $navbararray["level"] == "" ) {
                        $right = -1;
                    } else {
                        if ( priv_check('',$navbararray["level"]) ) {
                            $right = -1;
                        } else {
                            $right = 0;
                        }
                    }

                    if ( $right == -1 ) {
                        if ( $ausgaben["M0"] != "" ) $ausgaben["M0"] .= $defaults["split"]["m0"];
                        if ( $navbararray["exturl"] == "" ) {
                            $link1url = "../".$navbararray["entry"].".html";
                        } else {
                            $link1url = $navbararray["exturl"];
                        }
                        $lower[$navbararray["mid"]] = $navbararray;
                        $ausgaben["M0"] .= "<a class=\"menu_punkte\" href=\"".$link1url."\">".$navbararray["label"]."</a>";
                        $ausgaben["L0"] .= $defaults["split"]["l0"]."<a class=\"menu_punkte\" href=\"".$link1url."\">".$navbararray["label"]."</a><br />";
                    }
                }
            }

            // M1 -> gleiche ebene
            if ( $path.".html" == $environment["ebene"]."/".$environment["kategorie"].".html" ) {
                $sql = "SELECT  ".$cfg["path"]["db"]["menu"]["entries"].".refid,
                                ".$cfg["path"]["db"]["menu"]["entries"].".entry,
                                ".$cfg["path"]["db"]["menu"]["entries"].".sort,
                                ".$cfg["path"]["db"]["menu"]["entries"].".level,
                                ".$cfg["path"]["db"]["lang"]["entries"].".lang,
                                ".$cfg["path"]["db"]["lang"]["entries"].".label,
                                ".$cfg["path"]["db"]["lang"]["entries"].".exturl
                          FROM  ".$cfg["path"]["db"]["menu"]["entries"]."
                    INNER JOIN  ".$cfg["path"]["db"]["lang"]["entries"]."
                            ON  ".$cfg["path"]["db"]["menu"]["entries"].".mid = ".$cfg["path"]["db"]["lang"]["entries"].".mid
                         WHERE (
                               (".$cfg["path"]["db"]["menu"]["entries"].".refid=".$data["refid"].")
                           AND (".$cfg["path"]["db"]["menu"]["entries"].".hide <> '-1' OR ".$cfg["path"]["db"]["menu"]["entries"].".hide is NULL)
                           AND (".$cfg["path"]["db"]["lang"]["entries"].".lang='".$environment["language"]."')
                               )
                      ORDER BY  ".$cfg["path"]["db"]["menu"]["order"].";";
                $navbarresult  = $db -> query($sql);


                while ( $navbararray = $db -> fetch_array($navbarresult,1) ) {
                    if ( $navbararray["level"] == "" ) {
                        $right = -1;
                    } else {
                        if ( priv_check('',$navbararray["level"]) ) {
                            $right = -1;
                        } else {
                            $right = 0;
                        }
                    }

                    if ( $right == -1 ) {
                        if ( $ausgaben["M1"] != "" ) $ausgaben["M1"] .= $defaults["split"]["m1"];
                        if ( $navbararray["exturl"] == "" ) {
                            $link1url = "./".$navbararray["entry"].".html";
                        } else {
                            $link1url = $navbararray["exturl"];
                        }
                        $ausgaben["M1"] .= "<a class=\"menu_punkte\" href=\"".$link1url."\">".$navbararray["label"]."</a>";
                        $ausgaben["L1"] .= $defaults["split"]["l1"]."<a class=\"menu_punkte\" href=\"".$link1url."\">".$navbararray["label"]."</a><br />";

                        // prev + next handling
                        // ***
                        $prev = null; $next = null;
                        if ( $navbararray["sort"] == $data["sort"] - 10 ) {
                            $prev = "<a href=\"./".$navbararray["entry"].".html\">".$navbararray["label"]."</a>";
                        }
                        if ( $navbararray["entry"] == $environment["kategorie"] ) {
                            if ( !isset($prev) ) {
                                $prev = "<a href=\"../".@$lower[$navbararray["refid"]]["entry"].".html\">".@$lower[$navbararray["refid"]]["label"]."</a>";
                            }
                            #echo sprintf("<pre>%s</pre>",print_r($lower[$navbararray["refid"]],True));
                            #echo sprintf("<pre>%s</pre>",print_r($lower,True));
                            #echo $navbararray["refid"];
                        }
                        if ( $navbararray["sort"] == $data["sort"] + 10 ) {
                            $next = "<a href=\"./".$navbararray["entry"].".html\">".$navbararray["label"]."</a>";$navbararray["entry"];
                        }
                        // +++
                        // prev + next handling
                    }
                }

                // $lnk_0 mit back link belegen
                $lnkcount = 0;
                $lnk[$lnkcount] = $ausgaben["UP"];

                // M2 -> ebene darunter (unterpunkte)
                $sql = "SELECT  ".$cfg["path"]["db"]["menu"]["entries"].".entry,
                                ".$cfg["path"]["db"]["menu"]["entries"].".refid,
                                ".$cfg["path"]["db"]["menu"]["entries"].".sort,
                                ".$cfg["path"]["db"]["menu"]["entries"].".level,
                                ".$cfg["path"]["db"]["lang"]["entries"].".lang,
                                ".$cfg["path"]["db"]["lang"]["entries"].".label,
                                ".$cfg["path"]["db"]["lang"]["entries"].".exturl
                          FROM  ".$cfg["path"]["db"]["menu"]["entries"]."
                    INNER JOIN  ".$cfg["path"]["db"]["lang"]["entries"]."
                            ON  ".$cfg["path"]["db"]["menu"]["entries"].".mid = ".$cfg["path"]["db"]["lang"]["entries"].".mid
                         WHERE (
                               (".$cfg["path"]["db"]["menu"]["entries"].".refid=".$data["mid"].")
                           AND (".$cfg["path"]["db"]["menu"]["entries"].".hide <> '-1' OR ".$cfg["path"]["db"]["menu"]["entries"].".hide is NULL)
                           AND (".$cfg["path"]["db"]["lang"]["entries"].".lang='".$environment["language"]."'))
                      ORDER BY  ".$cfg["path"]["db"]["menu"]["order"].";";
                $navbarresult  = $db -> query($sql);

                $ausgaben["L2"] = null;
                while ( $navbararray = $db -> fetch_array($navbarresult,1) ) {
                    if ( $navbararray["level"] == "" ) {
                        $right = -1;
                    } else {
                        if ( priv_check('',$navbararray["level"]) ) {
                            $right = -1;
                        } else {
                            $right = 0;
                        }
                    }

                    if ( $right == -1 ) {
                        if ( $ausgaben["M2"] != "" ) $ausgaben["M2"] .=$defaults["split"]["m2"] ;
                        if ( $navbararray["exturl"] == "" ) {
                            $link2url = $pathvars["virtual"].$path."/".$navbararray["entry"].".html";
                        } else {
                            $link2url = $navbararray["exturl"];
                        }
                        $ausgaben["M2"] .= "<a class=\"menu_punkte\" href=\"".$link2url."\">".$navbararray["label"]."</a>";
                        $ausgaben["L2"] .= $defaults["split"]["l2"]."<a class=\"menu_punkte\" href=\"".$link2url."\">".$navbararray["label"]."</a><br />";

                        // $lnk_* mit links belegen
                        $lnkcount++;
                        $lnk[$lnkcount] = $link2url;

                        // prev + next handling
                        // ***
                        if ( $navbararray["sort"] == 10 ) {
                            $next = "<a href=\"".$pathvars["virtual"].$path."/".$navbararray["entry"].".html\">".$navbararray["label"]."</a>";
                        }
                        // +++
                        // prev + next handling
                    }
                }

                // prev + next handling
                // ***
                function prev_child_find( $mid ) {
                    global $db, $environment, $cfg, $children;
                    // gibts unterpunkte?
                    $sql = "SELECT ".$cfg["path"]["db"]["menu"]["entries"].".mid,
                                ".$cfg["path"]["db"]["menu"]["entries"].".refid,
                                ".$cfg["path"]["db"]["menu"]["entries"].".entry,
                                ".$cfg["path"]["db"]["menu"]["entries"].".sort,
                                ".$cfg["path"]["db"]["menu"]["entries"].".level,
                                ".$cfg["path"]["db"]["lang"]["entries"].".label
                            FROM ".$cfg["path"]["db"]["menu"]["entries"]."
                        INNER JOIN ".$cfg["path"]["db"]["lang"]["entries"]."
                                ON ".$cfg["path"]["db"]["menu"]["entries"].".mid = ".$cfg["path"]["db"]["lang"]["entries"].".mid
                            WHERE ".$cfg["path"]["db"]["menu"]["entries"].".refid = '".$mid."'
                            AND ".$cfg["path"]["db"]["lang"]["entries"].".lang='".$environment["language"]."'
                    ORDER BY SORT DESC;";
                    $result = $db -> query($sql);
                    if ( $db -> num_rows($result) > 0 ) {
                        $data3 = $db -> fetch_array($result,1);
                        $children[] = array( "mid" => $data3["mid"], "entry" => $data3["entry"], "label" => $data3["label"] );
                        $prev = prev_child_find( $data3["mid"] );
                        return $prev;
                    } else {
                        #echo sprintf("<pre>%s</pre>",print_r($children,True));
                        $path = null;
                        foreach ( $children as $entry ) {
                            $path .= "/".$entry["entry"];
                        }
                        $prev = "<a href=\".".$path.".html\">".$entry["label"]."</a>";
                        return $prev;
                    }
                }

                // eintrag vorher suchen
                $sort = $data["sort"] - 10;
                $sql = "SELECT ".$cfg["path"]["db"]["menu"]["entries"].".mid,
                            ".$cfg["path"]["db"]["menu"]["entries"].".refid,
                            ".$cfg["path"]["db"]["menu"]["entries"].".entry,
                            ".$cfg["path"]["db"]["menu"]["entries"].".sort,
                            ".$cfg["path"]["db"]["menu"]["entries"].".level,
                            ".$cfg["path"]["db"]["lang"]["entries"].".label
                       FROM ".$cfg["path"]["db"]["menu"]["entries"]."
                 INNER JOIN ".$cfg["path"]["db"]["lang"]["entries"]."
                         ON ".$cfg["path"]["db"]["menu"]["entries"].".mid = ".$cfg["path"]["db"]["lang"]["entries"].".mid
                      WHERE ".$cfg["path"]["db"]["menu"]["entries"].".sort = ".$sort."
                        AND ".$cfg["path"]["db"]["menu"]["entries"].".refid = '".$data["refid"]."'
                        AND ".$cfg["path"]["db"]["lang"]["entries"].".lang='".$environment["language"]."';";
                $result = $db -> query($sql);
                if ( $db -> num_rows($result) == 1 ) {
                    $data2 = $db -> fetch_array($result,1);
                    $children[] = array( "entry" => $data2["entry"], "label" => $data2["label"] );
                    $prev = prev_child_find( $data2["mid"] );
                }
                if ( !isset($next) ) {
                    $last = @end($upper);
                    function depth_find( $refid, &$count ) {
                        global $debugging, $db, $cfg;
                        $sql = "SELECT refid
                                  FROM ".$cfg["path"]["db"]["menu"]["entries"]."
                                 WHERE mid = ".$refid;
                        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $result = $db -> query($sql);
                        if ( $db -> num_rows($result) > 0 ) {
                            $data = $db -> fetch_array($result,1);
                            $count++;
                            depth_find($data["refid"],$count);
                        }
                    }
                    $count = 0;
                    if ( is_array($last) ) depth_find($last["mid"],$count);
                    $tiefe = $count_menu - $count;
                    for ( $i=0 ; $i < $tiefe ; $i++ ) {
                        $last["entry"] = "../".$last["entry"];
                    }
                    if ( !isset($last["label"]) ) $last["label"] = null;
                    $next = "<a href=\"".$last["entry"].".html\">".$last["label"]."</a>";
                }
                $ausgaben["prev"] = $prev;
                $ausgaben["next"] = $next;
                // +++
                // prev + next handling
            }
            // +++
            // content navigation erstellen
        }
    }

    // brotkrumen kuerzen, bis sie passen
    if ( !empty($cfg["path"]["max_length"]) && $cfg["pdfc"]["state"] != true ) {
        $cut_link = "";
        while ( strlen(implode(" > ",$kekse["label"])) > $cfg["path"]["max_length"] ) {
            array_shift($kekse["html"]);
            $cut_label = array_shift($kekse["label"]);
            $cut_title = array_shift($kekse["title"]);
            $cut_link  = array_shift($kekse["link"]);
        }
        if ( !empty($cut_link) ) {
            array_unshift( $kekse["html"] , "<a href=\"".$cut_link."\" title=\"".$cut_title."\" class=\"".$cfg["path"]["css"]["crumb"]."\">...</a>" );
        }
    }

    // brotkrumen zusammensetzen
    $environment["kekse"] = implode($defaults["split"]["kekse"], $kekse["html"]);

    // 404 error handling
    // ***
    if ( $specialvars["404"]["enable"] ) {
        $nochk = null;
        foreach( $specialvars["404"]["nochk"]["ebene"] as $value ) {
           $nochk .= strstr($environment["ebene"],$value);
        }
        foreach( $specialvars["404"]["nochk"]["kategorie"] as $value ) {
           $nochk .= strstr($environment["kategorie"],$value);
        }
        if ( $nochk == "" && $count_url != $count_menu ) {
            $ausgaben["404seite"] = $environment["ebene"]."/".$environment["kategorie"].".html";
            if ( isset($_SERVER["HTTP_REFERER"]) ) {
                $ausgaben["404referer"] = $_SERVER["HTTP_REFERER"];
                $mapping["main"] = "404referer";
                header("HTTP/1.0 404 Not Found");
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<b>404 error detected (with referer): ".$_SERVER["HTTP_REFERER"]."</b>".$debugging["char"];
            } else {
                $mapping["main"] = "404";
                header("HTTP/1.0 404 Not Found");
                #$ausgaben["404referer"] = "#(unbekannt)";
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<b>404 error detected</b>".$debugging["char"];
            }
        }
    }
    // +++
    // 404 error handling


    // zur�ck zur haupdatenbank
    $db -> selectDb(DATABASE,FALSE);

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
