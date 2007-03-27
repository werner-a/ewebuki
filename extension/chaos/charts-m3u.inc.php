<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  "$Id$";
//  "m3u import";
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

    if ( $environment["parameter"][1] == "check" ) {

        $fp = fopen($_FILES["upload1"]["tmp_name"],r);
        while (!feof($fp)) {
            $line = fgets($fp, 4096);
            $newentry[] = substr($line,0,3)+0;
        }
        fclose($fp);

        $fp = fopen($_FILES["upload2"]["tmp_name"],r);
        while (!feof($fp)) {
            $line = fgets($fp, 4096);

            $line = strtolower($line);

            $line = str_replace("Ä","ä",$line);
            $line = str_replace("Ö","ö",$line);
            $line = str_replace("Ü","ü",$line);

            $ip = explode(" - ",$line);
            $ip[0] = $ip[0]+0;

            $data[] = array( trim($ip[0]), trim($ip[1]), trim($ip[2]) );
            #echo $ip[0]." - ".$ip[1]." - ".$ip[2]."<br>";
        }
        fclose($fp);

        $erstellt = $HTTP_POST_VARS["date"]." ".$HTTP_POST_VARS["time"];

        foreach ( $data as $value ) {

            if ( in_array($value[0], $newentry) ) {
                $new = "-1";
            } else {
                $new = "0";
            }

            $sql = "SELECT id FROM ".$cfg["db"]["titel"]." WHERE titel = '".$value[2]."'";
            #echo $sql."<br>";
            $result  = $db -> query($sql);
            $titel["data"] = $db -> fetch_row($result);
            if ( $titel["data"][0]  == "" ) {
                $sql = "INSERT INTO ".$cfg["db"]["titel"]." ( titel ) VALUES ( '".addslashes($value[2])."' )";
                #echo $sql."<br>";
                $result  = $db -> query($sql);
                $titel["id"] = $db -> lastid();
            } else {
                $titel["id"] = $titel["data"][0];
            }

            $sql = "SELECT id FROM ".$cfg["db"]["interpret"]." WHERE interpret = '".$value[1]."'";
            #echo $sql."<br>";
            $result  = $db -> query($sql);
            $interpret["data"] = $db -> fetch_row($result);
            if ( $interpret["data"][0]  == "" ) {
                $sql = "INSERT INTO ".$cfg["db"]["interpret"]." ( interpret ) VALUES ( '".addslashes($value[1])."' )";
                #echo $sql."<br>";
                $result  = $db -> query($sql);
                $interpret["id"] = $db -> lastid();
            } else {
                $interpret["id"] = $interpret["data"][0];
            }

            $sql  = "INSERT INTO ".$cfg["db"]["platzierung"]." ( erstellt, newentry, platz, titelid, interpretid) VALUES ";
            $sql .= "( '".$erstellt."', '".$new."', '".$value[0]."', '".$titel["id"]."', '".$interpret["id"]."' )";
            #echo $sql."<br><br>";
            $result = $db -> query($sql);
            $ausgaben["output"] .= $value[0]." - ".$value[1]." - ".$value[2];
            if ( in_array($value[0], $newentry) ) {
                $ausgaben["output"] .= " <b>(new)</b>";
            }
            $ausgaben["output"] .= "<br>";
        }


    } else {

        $ausgaben["output"] .="<form action=\"".$cfg["basis"]."/m3u,check.html\" method=\"post\" enctype=\"multipart/form-data\">";
        #$ausgaben[output] .="<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"200000000\">";
        $ausgaben["output"] .="<input type=\"text\" name=\"date\" value=\"".date("Y-m-d")."\"><br>";
        $ausgaben["output"] .="<input type=\"text\" name=\"time\" value=\"".date("H:i:s")."\"><br>";
        $ausgaben["output"] .="<input type=\"file\" name=\"upload1\"><br>";
        $ausgaben["output"] .="<input type=\"file\" name=\"upload2\"><br>";
        $ausgaben["output"] .="<input type=\"submit\" value=\"los\">";
        $ausgaben["output"] .="</form>";

    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
