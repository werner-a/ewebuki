<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
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
    if ( in_array("locate", $cfg["menued"]["function"][$environment["kategorie"]]) ) {

       function delete($id,$fd) {
            global $environment,$db,$cfg,$stop;
            $sql = "SELECT * FROM ".$cfg["menued"]["db"]["menu"]["entries"]." where mid=".$id;
            $result  = $db -> query($sql);
            $data = $db -> fetch_array($result,1);
            if ( $_SESSION["menued_id"] == $data["mid"] ) {
                $stop[] = $fd;
            }
            if ( $data["refid"] != 0 ) {
                delete($data["refid"],$fd);
            }
        }

       function locate($id) {
            global $positionArray,$db,$cfg;
            $sql = "SELECT * FROM ".$cfg["menued"]["db"]["menu"]["entries"]." where mid=".$id;

            $result  = $db -> query($sql);
            $data = $db -> fetch_array($result,1);

            $positionArray[$data["mid"]] = $data["mid"];

            if ( $data["refid"] != 0 ) {
                locate($data["refid"]);
            }
        }
    }

    // rekursive renumber funktion
    if ( in_array("renumber", $cfg["menued"]["function"][$environment["kategorie"]]) ) {

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
    if ( in_array("make_ebene", $cfg["menued"]["function"][$environment["kategorie"]]) ) {

        function make_ebene($mid, $ebene="") {
            # call: make_ebene(refid);
            global $db, $cfg;
            $sql = "SELECT refid, entry
                    FROM ".$cfg["menued"]["db"]["menu"]["entries"]."
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


    // funktion um den content eines eintrags, sowie aller unterpunkte und aller viewer-contents zu verschieben
    if ( in_array("update_tname", $cfg["menued"]["function"][$environment["kategorie"]]) ) {

        function update_tname($mid, $new_url) {
            global $db, $cfg, $debugging, $ausgaben;

            $sql = "SELECT mid, refid, entry
                      FROM ".$cfg["menued"]["db"]["menu"]["entries"]."
                     WHERE mid ='".$mid."'";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,1);

            $extend = "";
            $old_level = explode("/",make_ebene($data["mid"]));
            $old_kategorie = array_pop($old_level);
            $old_ebene = implode("/",$old_level);
            if ( count($old_level) > 1 ) $extend = eCRC($old_ebene).".";
            $old_tname = $extend.$data["entry"];

            // neuer tname
            $extend = "";
            $new_level = explode("/",$new_url);
            $new_kategorie = array_pop($new_level);
            $new_ebene = implode("/",$new_level);
            if ( count($new_level) > 1 ) $extend = eCRC($new_ebene).".";
            $new_tname = $extend.$new_kategorie;

            // aktualisierung der content-rechte
            $old_rights_url = $old_ebene."/".$old_kategorie;
            $new_rights_url = $new_ebene."/".$new_kategorie;
            $sql_rights = "UPDATE ".$cfg["menued"]["db"]["content"]["entries"]."
                       SET tname = '".$new_rights_url."'
                     WHERE tname = '".$old_rights_url."'";
            $result_rights = $db -> query($sql_rights);
            // aktualisierung der content-rechte

            $sql = "UPDATE ".$cfg["menued"]["db"]["text"]["entries"]."
                       SET tname = '".$new_tname."',
                           ebene = '".$new_ebene."',
                           kategorie = '".$new_kategorie."'
                     WHERE tname = '".$old_tname."'";
            $subresult = $db -> query($sql);

            // view-check
            $old_view_ebene = $old_ebene."/".$old_kategorie."/view";
            $new_view_ebene = $new_ebene."/".$new_kategorie."/view";
            $sql = "SELECT *
                      FROM ".$cfg["menued"]["db"]["text"]["entries"]."
                     WHERE tname LIKE '".eCRC($old_view_ebene)."%'";
            $result = $db -> query($sql);
            while ( $data = $db -> fetch_array($result,1) ) {
                $sql = "UPDATE ".$cfg["menued"]["db"]["text"]["entries"]."
                           SET tname='".str_replace(eCRC($old_view_ebene),eCRC($new_view_ebene),$data["tname"])."'
                         WHERE tname='".$data["tname"]."'";
                $subresult = $db -> query($sql);
            }

            $sql = "SELECT mid, refid, entry
                      FROM ".$cfg["menued"]["db"]["menu"]["entries"]."
                     WHERE refid ='".$mid."'";
            $result = $db -> query($sql);
            while ( $data = $db -> fetch_array($result,1) ) {
                $next_item = $new_ebene."/".$new_kategorie."/".$data["entry"];
                update_tname($data["mid"], /*$new = "",*/ $next_item);
            }

        }
    }

    // funktion um zu pruefen, ob das feld extend in der lang tabelle existiert
    #if ( in_array("checkext", $cfg["function"][$environment["kategorie"]]) ) {

        function checkext() {
            global $db, $cfg;

            // extend - db test
            $sql = "select extend from ".$cfg["menued"]["db"]["lang"]["entries"] ;
            $result = @$db -> query($sql);
            return $result;
        }
    #}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
