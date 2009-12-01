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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    // was anzeigen
    $mapping["main"] = "wizard-edit";

    if ( $wizard_name == "termine" ) {
        $dataloop["calendar"][1]["id"] =  "date1";
        $dataloop["calendar"][1]["button"] =  "trigger1";
        $dataloop["calendar"][2]["id"] =  "date2";
        $dataloop["calendar"][2]["button"] =  "trigger2";

        $ausgaben["error"] = "";
        $ausgaben["checked"] = "";
        $hidedata["termine"]["on"] = "ON";

        $preg = "/\[([_A-Z]*)\](.*)\[\/[_A-Z]*\]/Us";
        if  ( $_GET["_NAME"] ) $ausgaben["error"] = "Beginn und Ende-Datum sind nicht korrekt";
        preg_match_all($preg,$tag_meat["!"][0]["complete"],$regs);
        foreach ( $regs[1] as $key => $value ) {
            if ( $value == "KATEGORIE" ) continue;
            if  ( $_GET[$value] ) {
                $regs[2][$key] = $_GET[$value];
            }
            $hidedata["termine"][$value] = $regs[2][$key];
            $$value = $regs[2][$key];
            if ( $value == "_TERMIN" && $regs[2][$key] != "1970-01-01" ) $ausgaben["checked"] = "checked";
            if ( $_POST["send"] ) {
                if ( $value == "_TERMIN" || $value == "SORT" ) {
                    $_POST[$value] = substr($_POST[$value],6,4)."-".substr($_POST[$value],3,2)."-".substr($_POST[$value],0,2);
                }
                $tag_meat["!"][0]["complete"] = preg_replace("/\[".$value."\]".$$value."\[\/".$value."\]/","[".$value."]".$_POST[$value]."[/".$value."]",$tag_meat["!"][0]["complete"]);
            }
        }
        $SORT = substr($SORT,0,10);

        $SORT = substr($SORT,8,2).".".substr($SORT,5,2).".".substr($SORT,0,4);
        $_TERMIN = substr($_TERMIN,8,2).".".substr($_TERMIN,5,2).".".substr($_TERMIN,0,4);

        if ( $_TERMIN != "01.01.1970" ) {
            $display = "";
        } else {
            $display = "none";
        }
        $hidedata["termine"]["sort"] = $SORT;
        $hidedata["termine"]["display"] = $display;
        $hidedata["termine"]["_TERMIN"] = $_TERMIN;

        if ( $_POST["send"]  ) {
            if ( $_POST["_TERMIN"] != "1970-01-01" && mktime(0,0,0,substr($_POST["_TERMIN"],5,2),substr($_POST["_TERMIN"],8,2),substr($_POST["_TERMIN"],0,4)) <= mktime(0,0,0,substr($_POST["SORT"],5,2),substr($_POST["SORT"],8,2),substr($_POST["SORT"],0,4)) ) {
                echo "Beginn und Ende-Datum sind nicht korrekt";
            if ( $_POST["send"][0] == "Abschicken" ) {
                header("Location: ".$_SESSION["page"]."?_NAME=".$_POST["_NAME"]."&_VERANSTALTER=".$_POST["_VERANSTALTER"]."&_ORT=".$_POST["_ORT"]."&_BESCHREIBUNG=".urlencode($_POST["_BESCHREIBUNG"]));
                exit;
                }
            }
            $to_insert = $tag_meat["!"][0]["complete"];
        }
    } elseif ( $wizard_name == "artikel" || $wizard_name="ausstellungen" ) {
        $ausgaben["error"] = "";
        if ( $_GET["error"] == 1 )$ausgaben["error"] = "#(antedate)";
        if ( $_GET["error"] == 2 )$ausgaben["error"] = "#(date_begin_end)";
        if ( $_GET["error"] == 3 )$ausgaben["error"] = "#(date_periode)";

        // einstellen was sichtbar sein soll
        $hidedata["artikel"]["on"] = "ON";
        $dataloop["calendar"][1]["id"] =  "date1";
        $dataloop["calendar"][1]["button"] =  "trigger1";
        $dataloop["calendar"][2]["id"] =  "date2";
        $dataloop["calendar"][2]["button"] =  "trigger2";

        $preg = "/\[([_A-Z]*)\](.*)\[\/[_A-Z]*\]/Us";
        preg_match_all($preg,$tag_meat["!"][0]["complete"],$regs);

        // bei globalen artikeln nichts moeglich
        if ( $regs[2][1] == "/aktuell/archiv" ) {
            $hidedata["artikel"]["button1_display"] = "display:none";
            $hidedata["artikel"]["button2_display"] = "display:none";
        }

        foreach ( $regs[1] as $key => $value ) {
            if ( $value == "KATEGORIE" ) continue;
            if  ( $_GET[$value] ) {
                $regs[2][$key] = $_GET[$value];
            }

            // variablen erzeugen
            $$value = $regs[2][$key];
            if ( $_POST["send"] ) {
                if ( $value == "ENDE" || $value == "SORT" ) {
                    $_POST[$value] = substr($_POST[$value],6,4)."-".substr($_POST[$value],3,2)."-".substr($_POST[$value],0,2);
                }
                $tag_meat["!"][0]["complete"] = preg_replace("/\[".$value."\]".$$value."\[\/".$value."\]/","[".$value."]".$_POST[$value]."[/".$value."]",$tag_meat["!"][0]["complete"]);
            }
        }

        $SORT = substr($SORT,0,10);
        $SORT = substr($SORT,8,2).".".substr($SORT,5,2).".".substr($SORT,0,4);
        $ENDE = substr($ENDE,0,10);
        $ENDE = substr($ENDE,8,2).".".substr($ENDE,5,2).".".substr($ENDE,0,4);
        if ( $ENDE != "01.01.1970" ) {
            $display = "";
        } else {
            $display = "none";
        }
        if ( $ENDE == ".." ) {
            $hidedata["artikel"]["button2_display"] = "display:none";
            $display = "none";
        }

        $hidedata["artikel"]["display"] = $display;
        $hidedata["artikel"]["sort"] = $SORT;
        $hidedata["artikel"]["ende"] = $ENDE;
        $sort_timestamp = mktime(0,0,0,substr($SORT,3,2),substr($SORT,0,2),substr($SORT,6,4));
        $now_timestamp = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $postsort_timestamp = mktime(0,0,0,substr($_POST["SORT"],5,2),substr($_POST["SORT"],8,2),substr($_POST["SORT"],0,4));

        $sql = "Select Cast(SUBSTR(content,POSITION('[SORT]' IN content)+6,POSITION('[/SORT]' IN content)-POSITION('[SORT]' IN content)-6) AS DATETIME ) AS date from site_text where status =1 AND tname ='".$environment["parameter"][2]."'";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        if ( $db -> num_rows($result)  > 0 ) {
            $check_date = mktime(0,0,0,substr($data["date"],5,2),substr($data["date"],8,2),substr($data["date"],0,4));
        }

        if ( $_POST["send"] ) {
            if ( $regs[2][1] != "/aktuell/archiv" ) {
                if ( $postsort_timestamp < $now_timestamp && ( $check_date == "" || $postsort_timestamp < $check_date )) {
                    if ( $_POST["send"][0] == "Abschicken" ) {
                        header("Location: ".$_SESSION["page"]."?error=1");
                        exit;
                    }
                }
                if ( $_POST["ENDE"] != "..") {
                    $postend_timestamp = mktime(0,0,0,substr($_POST["ENDE"],5,2),substr($_POST["ENDE"],8,2),substr($_POST["ENDE"],0,4));
                    $periode = $postend_timestamp-$postsort_timestamp;
                    if ( ( $_POST["ENDE"] && $_POST["ENDE"] != "1970-01-01") && ($postend_timestamp <= $postsort_timestamp ) ) {
                        if ( $_POST["send"][0] == "Abschicken" ) {
                            header("Location: ".$_SESSION["page"]."?error=2");
                            exit;
                        }
                    }
                    if ( ( $_POST["ENDE"] && $_POST["ENDE"] != "1970-01-01") && ($periode > ( 86400 * 130 ) ) ) {
                        if ( $_POST["send"][0] == "Abschicken" ) {
                            header("Location: ".$_SESSION["page"]."?error=3");
                            exit;
                        }
                    }
                }
            }
            $to_insert = $tag_meat["!"][0]["complete"];
        }
    }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>