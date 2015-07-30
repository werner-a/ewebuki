<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "menu.inc.php v1 chaot";
  $Script_desc = "menu generieren - 3 stufen ";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon <wa@chaos.de>

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

    $ebene = explode("/",$environment["ebene"]);
    if ( empty($ebene[1]) ) $ebene[1] = null;
    if ( empty($ebene[2]) ) $ebene[2] = null;
    include $pathvars["moduleroot"]."libraries/function_menu_convert.inc.php";
    //
    // menupunkte level 1
    //
    $mandatory = " AND ((".$cfg["menu"]["db"]["entries"].".mandatory)='-1')";
    if ( $cfg["menu"]["level1"]["full"] == "-1" ) $mandatory = "";
    $extenddesc = null;
    if ( !empty($cfg["menu"]["level1"]["extend"]) && $cfg["menu"]["level1"]["extend"] == "-1" ) $extenddesc = $cfg["menu"]["db"]["entries"]."_lang.extend,";

    $sql = "SELECT  ".$cfg["menu"]["db"]["entries"].".mid,
                    ".$cfg["menu"]["db"]["entries"].".refid,
                    ".$cfg["menu"]["db"]["entries"].".entry,
                    ".$cfg["menu"]["db"]["entries"].".picture,
                    ".$cfg["menu"]["db"]["entries"].".level,
                    ".$cfg["menu"]["db"]["entries"]."_lang.lang,
                    ".$cfg["menu"]["db"]["entries"]."_lang.label,
                    ".$extenddesc."
                    ".$cfg["menu"]["db"]["entries"]."_lang.exturl
              FROM  ".$cfg["menu"]["db"]["entries"]."
        INNER JOIN  ".$cfg["menu"]["db"]["entries"]."_lang
                ON  ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["entries"]."_lang.mid
             WHERE (
                   (".$cfg["menu"]["db"]["entries"].".refid=0)
               AND (".$cfg["menu"]["db"]["entries"].".hide <> '-1' OR ".$cfg["menu"]["db"]["entries"].".hide IS NULL)
               AND (".$cfg["menu"]["db"]["entries"]."_lang.lang='".$environment["language"]."')
               ".$mandatory."
                   )
          ORDER BY sort, label;";
    $level1result  = $db -> query($sql);
    if ( $cfg["menu"]["db"]["debug"] ) $debugging["ausgabe"] .= "level1sql: ".$sql.$debugging["char"];
    if ( $cfg["menu"]["db"]["debug"] ) $debugging["ausgabe"] .= "level1res: ".$level1result.$debugging["char"];

    // entweder alle in anderer sprache oder nichts
    if ( $db -> num_rows($level1result) == 0 ){
        $ausgaben[$cfg["menu"]["name"]] = "language not found";
    #    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: \"".$environment["language"]."\" for menu not found using default: \"".$specialvars["default_language"]."\"".$debugging["char"];
    #    $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid, ".$cfg["menu"]["db"]["entries"].".entry, ".$cfg["menu"]["db"]["entries"].".refid, ".$cfg["menu"]["db"]["entries"].".level, ".$cfg["menu"]["db"]["language"].".lang, ".$cfg["menu"]["db"]["language"].".label, ".$cfg["menu"]["db"]["language"].".exturl FROM ".$cfg["menu"]["db"]["entries"]." INNER JOIN ".$cfg["menu"]["db"]["language"]." ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid WHERE (((".$cfg["menu"]["db"]["entries"].".refid)=0) AND ((".$cfg["menu"]["db"]["language"].".lang)='".$specialvars["default_language"]."')) order by sort;";
    #    $mainmenuresult  = $db -> query($sql);
    }

    if ( $cfg["menu"]["level1"]["enable"] == -1 ) $ausgaben[$cfg["menu"]["name"]] = $cfg["menu"]["level1"]["on"];
    while ( $level1array = $db -> fetch_array($level1result,1) ) {
        if ( empty($level1array["extend"]) ) $level1array["extend"] = null;
        if ( $cfg["menu"]["level1"]["enable"] == -1 ) {
            if ( $level1array["level"] == "" ) {
                $right = -1;
                $parser = -1;
            } else {
                if ( priv_check(make_ebene($level1array["mid"]),$level1array["level"]) ) {
                    $right = -1;
                    $parser = -1;
                } else {
                    $right = 0;
                    $parser = 0;
                }
            }
            if ( $right == -1 ) {

                // die boese schneide ab funktion
                if ( strlen($level1array["label"]) > $cfg["menu"]["level1"]["length"] ) {
                    $level1array["label"] = substr($level1array["label"],0,$cfg["menu"]["level1"]["length"]-4)." ...";
                }

                // wo geht der href hin?
                $aktiv = "";
                if ( $level1array["exturl"] == "" ) {
                    $href = $cfg["menu"]["base"]."/".$level1array["entry"].".html";
                    $target = "";
                    $aktiv = "";
                    if ( empty($ebene[1])
                      && empty($ebene[2])
                      && $level1array["entry"] == $environment["kategorie"] ) {
                        $aktiv = "aktiv";
                    }
                    $mandatory = " AND ((".$cfg["menu"]["db"]["entries"].".mandatory)='-1')";
                    if ( $cfg["menu"]["level1"]["force"] == -1 ) $mandatory = "";
                } else {
                    $href = $level1array["exturl"];
                    $target = $cfg["menu"]["level1"]["target"];
                }
                $marken = array("##target##", "##link##", "##label##", "##picture##", "##extend##", "##aktiv##");
                $ersatz = array($target, $href, $level1array["label"], $level1array["picture"], $level1array["extend"], $aktiv);

                // multiple db support
                $aktdb = null; $aktlev = null;
                if ( $cfg["menu"]["mdbsupp"] == -1 ) {
                  $aktdb = $db->getDb();
                  if ( $environment["fqdn"][0] == $cfg["menu"]["mdbname"] ) {
                      $aktlev = DATABASE;
                  } else {
                      $aktlev = $environment["fqdn"][0];
                  }
                }

                if ( strpos($environment["ebene"], $level1array["entry"]) == 1 && $aktlev == $aktdb || ( $environment["kategorie"] == $level1array["entry"] && $environment["ebene"] == "" && $aktlev == $aktdb ) ) {
                    // open folder
                    if ( empty($cfg["menu"]["level1"]["icona"]) ) $cfg["menu"]["level1"]["icona"] = null;
                    $ausgaben["ordner"] = str_replace($marken,$ersatz,$cfg["menu"]["level1"]["icona"]);

                    if ( $cfg["menu"]["level2"]["full"] == -1 ) $mandatory = "";
                    if ( $cfg["menu"]["level2"]["dynamic"] == -1 ) $cfg["menu"]["level2"]["enable"] = -1;
                } else {
                    // closed folder
                    if ( empty($cfg["menu"]["level1"]["iconb"]) ) $cfg["menu"]["level1"]["iconb"] = null;
                    $ausgaben["ordner"] = str_replace($marken,$ersatz,$cfg["menu"]["level1"]["iconb"]);

                    if ( $cfg["menu"]["level2"]["dynamic"] == -1 ) $cfg["menu"]["level2"]["enable"] = 0;
                }
                if ( empty($cfg["menu"]["level1"]["link"]) ) $cfg["menu"]["level1"]["link"] = null;
                $ausgaben["ueberschrift"] = str_replace($marken,$ersatz,$cfg["menu"]["level1"]["link"]);
                if ( $cfg["menu"]["level1"]["link2"] == "" ) {
                    $ausgaben["ueberschrift"] = str_replace($marken,$ersatz,$cfg["menu"]["level1"]["link"]);
                } else {
                    if ( $aktiv == "" ) {
                        $ausgaben["ueberschrift"] = str_replace($marken,$ersatz,$cfg["menu"]["level1"]["link1"]);
                    } else {
                        $ausgaben["ueberschrift"] = str_replace($marken,$ersatz,$cfg["menu"]["level1"]["link2"]);
                    }
                }
            }
        } else {
            $ausgaben["ordner"] = "";
            $ausgaben["ueberschrift"] = "";
        }

        //
        // menupunkte level 2
        //
        $extenddesc = null;
        if ( !empty($cfg["menu"]["level2"]["extend"]) && $cfg["menu"]["level2"]["extend"] == "-1" ) $extenddesc = $cfg["menu"]["db"]["entries"]."_lang.extend,";
        $sql = "SELECT  ".$cfg["menu"]["db"]["entries"].".mid,
                        ".$cfg["menu"]["db"]["entries"].".refid,
                        ".$cfg["menu"]["db"]["entries"].".entry,
                        ".$cfg["menu"]["db"]["entries"].".picture,
                        ".$cfg["menu"]["db"]["entries"].".sort,
                        ".$cfg["menu"]["db"]["entries"].".level,
                        ".$cfg["menu"]["db"]["language"].".lang,
                        ".$cfg["menu"]["db"]["language"].".label,
                        ".$extenddesc."
                        ".$cfg["menu"]["db"]["language"].".exturl
                  FROM  ".$cfg["menu"]["db"]["entries"]."
            INNER JOIN  ".$cfg["menu"]["db"]["language"]."
                    ON  ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid
                 WHERE (
                       (".$cfg["menu"]["db"]["entries"].".refid=".$level1array["mid"].")
                   AND (".$cfg["menu"]["db"]["entries"].".hide <> '-1' OR ".$cfg["menu"]["db"]["entries"].".hide IS NULL)
                   AND (".$cfg["menu"]["db"]["language"].".lang='".$environment["language"]."')
                   ".$mandatory."
                       )
              ORDER BY sort, label;";
        $level2result = $db -> query($sql);
        $level2rows = $db -> num_rows($level2result);
        if ( $cfg["menu"]["db"]["debug"] ) $debugging["ausgabe"] .= "level2sql: ".$sql.$debugging["char"];
        if ( $cfg["menu"]["db"]["debug"] ) $debugging["ausgabe"] .= "level2res: ".$level2result.$debugging["char"];

        // entweder alle in anderer sprache oder nichts
        #if ( $db -> num_rows($level1result) == 0 ){
        #    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: \"".$environment["language"]."\" for submenu not found using default: \"".$specialvars["default_language"]."\"".$debugging["char"];
        #    $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid, ".$cfg["menu"]["db"]["entries"].".entry, ".$cfg["menu"]["db"]["entries"].".refid, ".$cfg["menu"]["db"]["entries"].".level, ".$cfg["menu"]["db"]["language"].".lang, ".$cfg["menu"]["db"]["language"].".label, ".$cfg["menu"]["db"]["language"].".exturl FROM ".$cfg["menu"]["db"]["entries"]." INNER JOIN ".$cfg["menu"]["db"]["language"]." ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid WHERE (((".$cfg["menu"]["db"]["entries"].".refid)=".$level1array["mid"].") AND ((".$cfg["menu"]["db"]["language"].".lang)='".$specialvars["default_language"]."')) order by sort;";
        #    $submenuresult  = $db -> query($sql);
        #}

        if ( $level2rows > 0 ) {
            if ( empty($cfg["menu"]["level2"]["on"]) ) $cfg["menu"]["level2"]["on"] = null;
            $ausgaben["punkte"] = $cfg["menu"]["level2"]["on"];
        } else {
            $ausgaben["punkte"] = "";
        }
        while ( $level2array = $db -> fetch_array($level2result, 1) ) {
            if ( empty($level2array["extend"]) ) $level2array["extend"] = null;
            if ( $cfg["menu"]["level2"]["enable"] == -1 ) {
                if ( $level2array["level"] == "" ) {
                    $right = -1;
                } else {
                    if ( priv_check(make_ebene($level2array["mid"]),$level2array["level"]) ) {
                        $right = -1;
                    } else {
                        $right = 0;
                    }
                }
                if ( $right == -1 ) {
                    // die boese schneide ab funktion
                    if ( strlen($level2array["label"]) > $cfg["menu"]["level2"]["length"] ) {
                        $level2array["label"] = substr($level2array["label"],0,$cfg["menu"]["level2"]["length"]-4)." ...";
                    }
                    // wo geht der href hin?
                    if ( $level2array["exturl"] == "" ) {
                        $href = $cfg["menu"]["base"]."/".$level1array["entry"]."/".$level2array["entry"].".html";
                        $target = "";
                        $aktiv = "";
                        if ( $level1array["entry"] == $ebene[1]
                          && empty($ebene[2])
                          && $level2array["entry"] == $environment["kategorie"] ) {
                            $aktiv = "aktiv";
                        }
                        $mandatory = " AND ((".$cfg["menu"]["db"]["entries"].".mandatory)='-1')";
                        if ( $cfg["menu"]["level1"]["force"] == -1 ) $mandatory = "";

                        // verhalten der naechsten ebene steuern
                        if ( strstr($environment["ebene"],"/".$level1array["entry"]."/".$level2array["entry"]) || strstr($environment["kategorie"],$level2array["entry"]) ) {
                            if ( $cfg["menu"]["level3"]["full"] == -1 ) $mandatory = "";
                            if ( $cfg["menu"]["level3"]["dynamic"] == -1 ) $cfg["menu"]["level3"]["enable"] = -1;
                        } else {
                            if ( $cfg["menu"]["level3"]["dynamic"] == -1 ) $cfg["menu"]["level3"]["enable"] = 0;
                        }
                    } else {
                        $href = $level2array["exturl"];
                        $target = $cfg["menu"]["level2"]["target"];
                    }
                    $marken = array("##target##", "##link##", "##label##", "##picture##", "##extend##", "##aktiv##");
                    $ersatz = array($target, $href, $level2array["label"], $level2array["picture"], $level2array["extend"], $aktiv);

                    if ( empty($cfg["menu"]["level2"]["link"]) ) $cfg["menu"]["level2"]["link"] = null;
                    $ausgaben["punkte"] .= str_replace($marken, $ersatz, $cfg["menu"]["level2"]["link"]);
                }
            }

            //
            // menupunkte level 3
            //
            #if ( strstr($environment["ebene"],"/".$level1array["entry"]) || strstr($environment["kategorie"],$level1array["entry"]) ) {
            if ( strpos($environment["ebene"]."/".$environment["kategorie"],$level1array["entry"]."/".$level2array["entry"]) !== false ) {
                $extenddesc = null;
                if ( !empty($cfg["menu"]["level3"]["extend"]) && $cfg["menu"]["level3"]["extend"] == "-1" ) $extenddesc = $cfg["menu"]["db"]["entries"]."_lang.extend,";
                $sql = "SELECT  ".$cfg["menu"]["db"]["entries"].".mid,
                                ".$cfg["menu"]["db"]["entries"].".refid,
                                ".$cfg["menu"]["db"]["entries"].".entry,
                                ".$cfg["menu"]["db"]["entries"].".picture,
                                ".$cfg["menu"]["db"]["entries"].".sort,
                                ".$cfg["menu"]["db"]["entries"].".level,
                                ".$cfg["menu"]["db"]["language"].".lang,
                                ".$cfg["menu"]["db"]["language"].".label,
                                ".$extenddesc."
                                ".$cfg["menu"]["db"]["language"].".exturl
                          FROM  ".$cfg["menu"]["db"]["entries"]."
                    INNER JOIN  ".$cfg["menu"]["db"]["language"]."
                            ON  ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid
                         WHERE (
                               (".$cfg["menu"]["db"]["entries"].".refid=".$level2array["mid"].")
                           AND (".$cfg["menu"]["db"]["entries"].".hide <> '-1' OR ".$cfg["menu"]["db"]["entries"].".hide IS NULL)
                           AND (".$cfg["menu"]["db"]["language"].".lang='".$environment["language"]."')
                           ".$mandatory."
                               )
                      ORDER BY  sort, label;";
                $level3result = $db -> query($sql);
                $level3rows = $db -> num_rows($level3result);
                if ( $cfg["menu"]["db"]["debug"] ) $debugging["ausgabe"] .= "level3sql: ".$sql.$debugging["char"];
                if ( $cfg["menu"]["db"]["debug"] ) $debugging["ausgabe"] .= "level3res: ".$level3result.$debugging["char"];

                #if ( $db -> num_rows($level1result) == 0 ){
                #    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: \"".$environment["language"]."\" for submenu not found using default: \"".$specialvars["default_language"]."\"".$debugging["char"];
                #    $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid, ".$cfg["menu"]["db"]["entries"].".entry, ".$cfg["menu"]["db"]["entries"].".refid, ".$cfg["menu"]["db"]["entries"].".level, ".$cfg["menu"]["db"]["language"].".lang, ".$cfg["menu"]["db"]["language"].".label, ".$cfg["menu"]["db"]["language"].".exturl FROM ".$cfg["menu"]["db"]["entries"]." INNER JOIN ".$cfg["menu"]["db"]["language"]." ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid WHERE (((".$cfg["menu"]["db"]["entries"].".refid)=".$level2array["mid"].") AND ((".$cfg["menu"]["db"]["language"].".lang)='".$specialvars["default_language"]."')) order by sort;";
                #    $submenuresult  = $db -> query($sql);
                #}

                if ( $level3rows > 0 ) $ausgaben["punkte"] .= $cfg["menu"]["level3"]["on"];
                while ( $level3array = $db -> fetch_array($level3result,$nop) ) {
                    if ( empty($level3array["extend"]) ) $level3array["extend"] = null;
                    if ( $cfg["menu"]["level3"]["enable"] == -1 ) {
                        if ( $level3array["level"] == "" ) {
                            $right = -1;
                        } else {
                            if ( priv_check(make_ebene($level3array["mid"]),$level3array["level"]) ) {
                                $right = -1;
                            } else {
                                $right = 0;
                            }
                        }
                        if ( $right == -1 ) {
                            // die boese schneide ab funktion
                            if ( strlen($level3array["label"]) > $cfg["menu"]["level3"]["length"] ) {
                                $level3array["label"] = substr($level3array["label"],0,$cfg["menu"]["level3"]["length"]-4)." ...";
                            }
                            // wo geht der href hin?
                            if ( $level3array["exturl"] == "" ) {
                                $href = $cfg["menu"]["base"]."/".$level1array["entry"]."/".$level2array["entry"]."/".$level3array["entry"].".html";
                                $target = "";
                                $aktiv = "";
                                if ( $level1array["entry"] == $ebene[1]
                                  && $level2array["entry"] == $ebene[2]
                                  && $level3array["entry"] == $environment["kategorie"] ) {
                                    $aktiv = "aktiv";
                                }
                            } else {
                                $href = $level3array["exturl"];
                                $target = $cfg["menu"]["level3"]["target"];
                            }
                            $marken = array("##target##", "##link##", "##label##", "##picture##", "##extend##", "##aktiv##");
                            $ersatz = array($target, $href, $level3array["label"], $level3array["picture"], $level3array["extend"], $aktiv);

                            if ( empty($cfg["menu"]["level3"]["link"]) ) $cfg["menu"]["level3"]["link"] = null;
                            $ausgaben["punkte"] .= str_replace($marken,$ersatz,$cfg["menu"]["level3"]["link"]);
                        }
                    }
                }
                if ( empty($cfg["menu"]["level3"]["off"]) ) $cfg["menu"]["level3"]["off"] = null;
                if ( $level3rows > 0 ) $ausgaben["punkte"] .= $cfg["menu"]["level3"]["off"];
            }
        }
        if ( empty($cfg["menu"]["level2"]["off"]) ) $cfg["menu"]["level2"]["off"] = null;
        if ( $level2rows > 0 ) $ausgaben["punkte"] .= $cfg["menu"]["level2"]["off"];
        if ( $cfg["menu"]["level1"]["enable"] == -1 && $parser == -1 ) {
            if ( $cfg["menu"]["generate"] == true ) {
                $ausgaben[$cfg["menu"]["name"]] .= $ausgaben["ueberschrift"].$ausgaben["punkte"];
            } else {
                $ausgaben[$cfg["menu"]["name"]] .= parser( $cfg["menu"]["name"], "", $parse_find, $parse_put);
            }
        }
    }
    if ( empty($cfg["menu"]["level1"]["off"]) ) $cfg["menu"]["level1"]["off"] = null;
    $ausgaben[$cfg["menu"]["name"]] .= $cfg["menu"]["level1"]["off"];

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
