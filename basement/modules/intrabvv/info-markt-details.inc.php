<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "short description";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    $print = array(
            "namen"         => "iautor",
            "akkate"        => "ikategorie",
            "schlagzeile"   => "ititel",
            "edatum"        => "ierstellt"
            );

    $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][1]."'";
    $result  = $db -> query($sql);
    $data = $db -> fetch_array($result,$nop);
    foreach ($print as $key => $value) {
        if ($key == "edatum") {
            $ausgaben[$key] = substr($data[$value],8,2).".".substr($data[$value],5,2).".".substr($data[$value],0,4);
        } else {
            $ausgaben[$key] = $data[$value];
        }

    }

    // intelligenten link tag bearbeiten
    $replace = intelilink($data["itext"]);

    // neues generelles tagreplace
    $replace = tagreplace($replace);

    // newlines nach br wandeln (muss zuletzt gemacht werden)
    $ausgaben["output"] = nlreplace($replace);

    $ausgaben["uebersicht"] = "<a href=".$cfg["basis"]."/".$cfg["ebene"]["zwei"].".html>Zurück zur Übericht</a>";

    $ausgaben["versenden"] = "<a href=".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/email,form,".$data["iid"].".html>Artikel empfehlen</a>";

    $ausgaben["edit"] = "<a href=".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/modify,edit,".$environment["parameter"][1].".html>Artikel editieren</a>";

    $mapping["navi"] = "leer";
    $mapping["main"] = "1943315524.details";

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];


    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
