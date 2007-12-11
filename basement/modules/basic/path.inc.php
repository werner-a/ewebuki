<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "path handling: 'kekse', [UP], [M0], [M1], [M2], [PREV], [NEXT], !#lnk* + 404 handling";
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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

    // datenbank wechseln -> variablen in menuctrl.inc.php
    if ( $environment["fqdn"][0] == $specialvars["dyndb"] ) {
        $db->selectDb($specialvars["dyndb"],FALSE);
        $specialvars["rootname"] = $db->getDb();
    }

    // altes verhalten wiederherstellen
    $defaults["split"]["title"] == "" ? $defaults["split"]["title"] = " - " : NOP;
    $defaults["split"]["kekse"] == "" ? $defaults["split"]["kekse"] = " - " : NOP;
    $defaults["split"]["m1"] == "" ? $defaults["split"]["m0"] = " &middot; " : NOP ;
    $defaults["split"]["m1"] == "" ? $defaults["split"]["m1"] = " &middot; " : NOP ;
    $defaults["split"]["m2"] == "" ? $defaults["split"]["m2"] = " &middot; " : NOP ;
    $defaults["split"]["l0"] == "" ? $defaults["split"]["l2"] = " &middot; " : NOP ;
    $defaults["split"]["l1"] == "" ? $defaults["split"]["l1"] = " &middot; " : NOP ;
    $defaults["split"]["l2"] == "" ? $defaults["split"]["l2"] = " &middot; " : NOP ;

    // dynamic style - db test/extension
    $sql = "select dynamiccss from ".$cfg["path"]["db"]["menu"]["entries"];
    $result = $db -> query($sql);
    if ( $result ) {
        $dynamiccss = $cfg["path"]["db"]["menu"]["entries"].".dynamiccss,";
    } else {
        unset($dynamiccss);
    }

    // dynamic bg - db test/extension
    $sql = "select dynamicbg from ".$cfg["path"]["db"]["menu"]["entries"];
    $result = $db -> query($sql);
    if ( $result ) {
        $dynamicbg = $cfg["path"]["db"]["menu"]["entries"].".dynamicbg,";
    } else {
        unset($dynamicbg);
    }

    // link zum webroot
    $environment["kekse"] = "<a href=\"".$pathvars["virtual"]."/index.html\">".$specialvars["rootname"]."</a>";

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
    unset($path);

    $ausgaben["M0"] = "";
    $ausgaben["M1"] = "";
    $ausgaben["M2"] = "";

    foreach ($kekspath as $key => $value) {
        $search = "like '".$value."'";
        $sql = "SELECT ".$cfg["path"]["db"]["menu"]["entries"].".mid,
                       ".$cfg["path"]["db"]["menu"]["entries"].".refid,
                       ".$cfg["path"]["db"]["menu"]["entries"].".entry,
                       ".$cfg["path"]["db"]["menu"]["entries"].".sort,
                       ".$cfg["path"]["db"]["menu"]["entries"].".level,
                       ".$cfg["path"]["db"]["menu"]["entries"].".defaulttemplate,
                       ".$dynamiccss.$dynamicbg."
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
            if ( $data["level"] != "" && $rechte[$data["level"]] != -1 ) break;

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
                        ".$dynamiccss.$dynamicbg."
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
            if ( $path == "" ) {
                $ausgaben["UP"] = $pathvars["virtual"]."/index.html";
            } else {
                $ausgaben["UP"] = $pathvars["virtual"].$path.".html";
            }

            // seitentitel und kekse zusammensetzen
            $path .= "/".$data["entry"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "path: ".$path.$debugging["char"];
            $specialvars["pagetitle"] .= $defaults["split"]["title"].$data["label"];
            $environment["kekse"] .= $defaults["split"]["kekse"]."<a href=\"".$pathvars["virtual"].$path.".html\">".$data["label"]."</a>";

            // variables template laut menueintrag setzen
            $specialvars["default_template"] = $data["defaulttemplate"];

            // variables css file - erweiterung laut menueintrag setzen
            if ( $data["dynamiccss"] != "" ) {
                $specialvars["dynamiccss"] = $data["dynamiccss"];
            }

            // variables bg bild - erweiterung laut menueintrag setzen
            if ( $data["dynamicbg"] != "" ) {
                $specialvars["dynamicbg"] = $data["dynamicbg"];
            }


            // content navigation erstellen
            // ***
            $ausgaben["M3"] = crc32($path)." <a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$back.".html\">Zurück</a>";

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
                        if ( $rechte[$navbararray["level"]] == -1 ) {
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
                        if ( $rechte[$navbararray["level"]] == -1 ) {
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
                        if ( $navbararray["sort"] == $data["sort"] - 10 ) {
                            $prev = "<a href=\"./".$navbararray["entry"].".html\">".$navbararray["label"]."</a>";
                        }
                        if ( $navbararray["entry"] == $environment["kategorie"] ) {
                            if ( $prev == "" ) {
                                $prev = "<a href=\"../".$lower[$navbararray["refid"]]["entry"].".html\">".$lower[$navbararray["refid"]]["label"]."</a>";
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
                while ( $navbararray = $db -> fetch_array($navbarresult,1) ) {
                    if ( $navbararray["level"] == "" ) {
                        $right = -1;
                    } else {
                        if ( $rechte[$navbararray["level"]] == -1 ) {
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
                if ( $next == "" ) {
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
                    $next = $next = "<a href=\"".$last["entry"].".html\">".$last["label"]."</a>";
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


    // 404 error handling
    // ***
    if ( $specialvars["404"]["enable"] ) {
        foreach( $specialvars["404"]["nochk"]["ebene"] as $value ) {
           $nochk .= strstr($environment["ebene"],$value);
        }
        foreach( $specialvars["404"]["nochk"]["kategorie"] as $value ) {
           $nochk .= strstr($environment["kategorie"],$value);
        }
        if ( $nochk == "" && $count_url != $count_menu ) {
            $ausgaben["404seite"] = $environment["ebene"]."/".$environment["kategorie"].".html";
            if ( $_SERVER["HTTP_REFERER"] ) {
                $ausgaben["404referer"] = $_SERVER["HTTP_REFERER"];
                $mapping["main"] = "404referer";
                header("HTTP/1.0 404 Not Found");
            } else {
                $mapping["main"] = "404";
                header("HTTP/1.0 404 Not Found");
                #$ausgaben["404referer"] = "#(unbekannt)";
            }

        }
    }
    // +++
    // 404 error handling


    // zurück zur haupdatenbank
    $db -> selectDb(DATABASE,FALSE);

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
