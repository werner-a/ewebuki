<?php require "../basic/libraries/global.inc.php";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $main_script_name = "$Id$";
  $main_script_desc = "converter fuer alte inhalte";
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

    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ** $main_script_name ** ]".$debugging[char];

    // hallo zur datenbank
    $db      = new DB_connect();
    $connect = $db->connect();
    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "data connect: ".$connect.$debugging[char];


    $sql = "SELECT * FROM site_text WHERE tname like '%.%' and crc32='0'";
    $result = $db -> query($sql,2);
    $update_count = 0;
    while ( $data = $db -> fetch_array($result,1) ) {
        echo "Found: ".$data[tid]." ".$data[label]." ".$data[tname]."<br>";
        $tname = explode(".", $data[tname]);
        $newtname = crc32("/".$tname[0]).".".$tname[1];
        $sql = "UPDATE site_text SET tname='".$newtname."', crc32='-1' WHERE tid=".$data[tid];
        if ( count($tname) > 2 ) {
            echo "Daten von ".$data[label]." - ".$data[tname]." inkombatibel! - Konvertierung fehlgeschlagen!<br>";
        } else {
            if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];
            $insertresult = $db -> query($sql,2);
            if ( $insertresult ) {
                $update_count++;
            }
        }
    }
    echo $update_count." Datensätze aktualisiert.";


    // entgueltige Debug Ausgabe zusammensetzen und ausgeben
    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ++ $main_script_name ++ ]".$debug_chr;
    if ( $debugging[html_enable] ) echo $debugging[ausgabe].$debugging[footer];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>