<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "$Id$";
  $Script_desc = "sitemap generator";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    phpWEBkit - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of phpWEBkit

    phpWEBkit is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    phpWEBkit is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with phpWEBkit; If you did not, you may download a copy at:

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

    $mt = $db_entries;
    $mtl = $db_entries_lang;
    $refid = 0;

    function sitemap($refid) {
        global $environment, $db, $mt, $mtl, $pathvars, $specialvars, $rechte, $ast, $astpath;
        $sql = "SELECT $mt.mid, $mt.entry, $mt.refid, $mt.level, $mt.sort, $mtl.lang, $mtl.label, $mtl.exturl FROM $mt INNER JOIN $mtl ON $mt.mid = $mtl.mid WHERE ((($mt.refid)=$refid) AND (($mtl.lang)='".$environment["language"]."')) order by sort, label;";
        $menuresult  = $db -> query($sql);

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

                if ( $level == "" ) $menuarray["label"] = "<b>".$menuarray["label"]."</b>";
                $tree .= "<tr><td>".$level."<a class=\"\" href=\"".$pathvars["virtual"]."/".$path.$menuarray["entry"].".html\"><img src=\"".$pathvars["images"]."sitemap.png\" width=\"16\" height=\"16\" align=\"absbottom\" border=\"0\"><img src=\"".$pathvars["images"]."pos.png\" width=\"3\" height=\"1\" align=\"absbottom\" border=\"0\">".$menuarray["label"]."</a></td><td>".$aktion."</td></tr>";
                $tree .= sitemap($menuarray["mid"]);
            }
        }
        return $tree;
    }

    $ausgaben["output"]  = "<table width=\"100%\">";
    $ausgaben["output"] .= sitemap($refid);
    $ausgaben["output"] .= "</table>";

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>