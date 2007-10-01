<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menued-functions.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "funktion loader";
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

    // funktion um eine sitemap zu erstellen
    if ( in_array("locate", $cfg["function"][$environment["kategorie"]]) ) {

        function locate($id) {
            global $positionArray,$db,$cfg;
            $sql = "SELECT * FROM ".$cfg["db"]["menu"]["entries"]." where mid=".$id;
            $result  = $db -> query($sql);
            $data = $db -> fetch_array($result,1);
            $positionArray[$data["mid"]] = $data["mid"];
            if ( $data["refid"] != 0 ) {
                locate($data["refid"]);
            }
        }
    }

    // rekursive renumber funktion
    if ( in_array("renumber", $cfg["function"][$environment["kategorie"]]) ) {

        function renumber($mt, $mtl, $refid, $rekursiv=0) {
            global $environment, $debugging, $db;
            $sql = "SELECT  ".$mt.".mid
                      FROM  ".$mt."
                INNER JOIN  ".$mtl."
                        ON  ".$mt.".mid = ".$mtl.".mid
                     WHERE (".$mt.".refid=".$refid.")
                       AND (".$mtl.".lang='".$environment["language"]."')
                  ORDER BY sort, label;";
            $menuresult  = $db -> query($sql);
            while ( $menuarray = $db -> fetch_array($menuresult,1) ) {
                $sort += 10;
                $sql = "UPDATE ".$mt."
                           SET sort=".$sort."
                         WHERE mid='".$menuarray["mid"]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $db -> query($sql);
                if ( $rekursiv == -1 ) renumber($mt, $mtl, $menuarray["mid"], -1);
            }
        }
    }


    // funktion um die ebene aus der refid zu erstellen
    if ( in_array("make_ebene", $cfg["function"][$environment["kategorie"]]) ) {

        function make_ebene($mid, $ebene="") {
            # call: make_ebene(refid);
            global $db, $cfg;
            $sql = "SELECT refid, entry
                    FROM ".$cfg["db"]["menu"]["entries"]."
                    WHERE mid='".$mid."'";
            $result = $db -> query($sql);
            $array = $db -> fetch_array($result,$nop);
            $ebene = "/".$array["entry"].$ebene;
            if ( $array["refid"] != 0 ) {
                $ebene = make_ebene($array["refid"],$ebene);
            }
            return $ebene;
        }
    }


    // funktion um den content unterhalb eine eintrags zu verschieben
    if ( in_array("update_tname", $cfg["function"][$environment["kategorie"]]) ) {

        function update_tname($refid, /*$new = "",*/ $suchmuster = "", $ersatz = "") {
            global $db, $cfg, $debugging, $ausgaben;
            $sql = "SELECT mid, refid, entry FROM ".$cfg["db"]["menu"]["entries"]." WHERE refid ='".$refid."'";
            $result = $db -> query($sql);
            while ( $data = $db -> fetch_array($result,1) ) {

                // aktuelle ebene suchen
                $ebene = make_ebene($data["refid"]);

                // eindeutiges suchmuster erstellen
                #if ( $suchmuster == "" ) {
                #    $suchmuster = $ebene;
                #    $ersatz = substr($ebene,0,strrpos($ebene,"/"))."/".$new;
                #}

                // alter tname
                if ( $ebene != "/" ) $extend = crc32($ebene).".";
                $old_tname = $extend.$data["entry"];
                #echo $ebene.":".$old_tname."<br>";

                // neuer tname
                $ebene = str_replace($suchmuster, $ersatz, $ebene);
                if ( $ebene != "/" ) $extend = crc32($ebene).".";
                $new_tname = $extend.$data["entry"];
                #echo $ebene.":".$new_tname."<br>";

                $sql = "UPDATE ".$cfg["db"]["text"]["entries"]."
                            SET tname = '".$new_tname."',
                                ebene = '".$ebene."',
                                kategorie = '".$data["entry"]."'
                            WHERE tname = '".$old_tname."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $subresult = $db -> query($sql);
                if ( !$subresult ) $ausgaben["form_error"] .= $db -> error("#(menu_error)<br />");

                // und das gleiche fuer alle unterpunkte
                update_tname($data["mid"], /*$new,*/ $suchmuster, $ersatz);
            }
        }
    }

    // funktion um zu pruefen, ob das feld extend in der lang tabelle existiert
    #if ( in_array("checkext", $cfg["function"][$environment["kategorie"]]) ) {

        function checkext() {
            global $db, $cfg;

            // extend - db test
            $sql = "select extend from ".$cfg["db"]["lang"]["entries"] ;
            $result = $db -> query($sql);
            return $result;
        }
    #}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
