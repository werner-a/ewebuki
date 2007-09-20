<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "nexte generation des menu script";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    function menu_generate($refid=0, $level=1, $arrEbene="", $url=""){
        global $db, $cfg, $debugging, $environment, $pathvars, $rechte, $dataloop, $hidedata, $ausgaben;

        if ( $cfg["menu"]["level".$level]["enable"] == "-1" ) {
            $mandatory = " AND ((".$cfg["menu"]["db"]["entries"].".mandatory)='-1')";
            if ( $cfg["menu"]["level".$level]["full"] == "-1" ) $mandatory = "";
            if ( $cfg["menu"]["level".$level]["extend"] == "-1" ) $extenddesc = $cfg["menu"]["db"]["entries"]."_lang.extend,";

            if ( $arrEbene == "" ) {
                $ebene = $environment["ebene"]."/".$environment["kategorie"];
                $arrEbene = explode("/",$ebene);
                $url = $pathvars["virtual"];
            }

            $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid,"
                            .$cfg["menu"]["db"]["entries"].".refid,"
                            .$cfg["menu"]["db"]["entries"].".entry,"
                            .$cfg["menu"]["db"]["entries"].".picture,"
                            .$cfg["menu"]["db"]["entries"].".level,"
                            .$cfg["menu"]["db"]["entries"]."_lang.lang,"
                            .$cfg["menu"]["db"]["entries"]."_lang.label,"
                            .$extenddesc." "
                            .$cfg["menu"]["db"]["entries"]."_lang.exturl".
                    " FROM ".$cfg["menu"]["db"]["entries"].
              " INNER JOIN ".$cfg["menu"]["db"]["entries"]."_lang".
                      " ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["entries"]."_lang.mid".
                   " WHERE (".
                         "(".$cfg["menu"]["db"]["entries"].".refid=".$refid.")".
                    " AND (".$cfg["menu"]["db"]["entries"].".hide <> '-1' OR ".$cfg["menu"]["db"]["entries"].".hide IS NULL)".
                    " AND (".$cfg["menu"]["db"]["entries"]."_lang.lang='".$environment["language"]."')"
                            .$mandatory.
                          ")".
               " ORDER BY sort, label;";
            if ( $cfg["menu"]["db"]["debug"] ) $debugging["ausgabe"] .= "level".$level."sql: ".$sql.$debugging["char"];

            $result = $db->query($sql);

            $buffer = "";
            $menu2 = "";

            while ( $data = $db -> fetch_array($result,1) ) {

                // berechtigung abfragen
                if ( $data["level"] != "" ) {
                    if ( $rechte[$data["level"]] != -1 ) {
                        continue;
                    }
                }

                // link und ziel
                if ( $data["exturl"] == "" ) {
                    $link   = $url."/".$data["entry"].".html";
                    $target = "";

                    // eintrag aktiv?
                    if ( $data["entry"] == $arrEbene[1] ) {
                        $aktiv = "aktiv";
                    } else {
                        $aktiv = "";
                    }

                } else {
                    $link   = $data["exturl"];
                    $target = $cfg["menu"]["level".$level]["target"];
                }

                // label,die boese schneide ab funktion
                $label = $data["label"];
                if ( strlen($data["label"]) > $cfg["menu"]["level".$level]["length"] ) {
                    $label = substr($data["label"],0,$cfg["menu"]["level".$level]["length"]-3)."...";
                }

                // was wird wodurch ersetzt
                $marken = array("##target##", "##link##", "##title##", "##label##", "##picture##", "##extend##", "##aktiv##");
                $ersatz = array($target, $link, $data["label"], $label, $data["picture"], $data["extend"], $aktiv);

                // version mit template
                if ( $cfg["menu"]["generate"] == false ) {
                    if ( $level != 1 ){
                        $ausgaben["punkte"] .= str_replace($marken,$ersatz,$cfg["menu"]["level".$level]["link"]);
                    } else {
                        if ( $data["entry"] == $arrEbene[1] ){
                            // open folder
                            $ausgaben["ordner"] = str_replace($marken,$ersatz,$cfg["menu"]["level1"]["icona"]);
                        } else {
                            // closed folder
                            $ausgaben["ordner"] = str_replace($marken,$ersatz,$cfg["menu"]["level1"]["iconb"]);
                        }
                    }
                }

                // css-klasse und naechste ebene
                $class = "Level".$level;
                $next_level = "";
                if ( $data["entry"] == $arrEbene[1] ) {
                    // css-klasse erzeugen
                    $class = "Level".$level."Active";

                    // ebenen-array veraendern
                    unset($arrEbene[1]);
                    $arrEbene = array_values($arrEbene);

                    // naechste ebene abarbeiten
                    $next_level = menu_generate($data["mid"],$level + 1,$arrEbene, $url."/".$data["entry"]);

                }
                $marken[] = "##class##";
                $ersatz[] = $class;
                $marken[] = "##next_level##";
                $ersatz[] = $next_level;

                // version mit template
                if ( $cfg["menu"]["generate"] == false ) {
                    if ( $level == 1 ){
                        $ausgaben["ueberschrift"] = str_replace($marken,$ersatz,$cfg["menu"]["level1"]["link"]);
                        $menu2 .= parser( $cfg["menu"]["name"], "", $parse_find, $parse_put);
                        $ausgaben["punkte"] = "";
                    }
                }

                // dataloop und hideloop fuer die entsprechende Ebene wird gebaut
                $dataloop["level".$level][] =array(
                    "link"  => $link,
                    "title" => $data["label"],
                    "item"  => $label,
                    "class" => $class
                );
                $hidedata["level".$level][0] = "enable";

                // welcher link aufbau
                if ( $cfg["menu"]["level1"]["link2"] == "" ) {
                    $link_build = "link";
                } else {
                    if ( $aktiv == "" ) {
                        $link_build = "link1";
                    } else {
                        $link_build = "link2";
                    }
                }

                // komplett
                $buffer .= str_replace( $marken, $ersatz, $cfg["menu"]["level".$level][$link_build] );
            }

            if ( $cfg["menu"]["generate"] == true ) {
                if ( $buffer != "" ) $menu2 = $cfg["menu"]["level".$level]["on"].$buffer.$cfg["menu"]["level".$level]["off"];
            }

            return $menu2;
        }
    }
    $ausgaben["menu"] = menu_generate();

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
