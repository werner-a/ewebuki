<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id: leer.inc.php 1355 2008-05-29 12:38:53Z buffy1860 $";
  $Script["desc"] = "sortierfunktion der blogs";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2008 Werner Ammon ( wa<at>chaos.de )

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

    if ( $cfg["bloged"]["blogs"][$kat]["right"] == "" || 
    ( priv_check(make_ebene($environment["parameter"][1]),$cfg["bloged"]["blogs"][$kat]["right"]) || ( function_exists(priv_check_old) && priv_check_old("",$cfg["bloged"]["blogs"][$kat]["right"]) ) )
    ) {

        function renumber_blog () {
            global $db,$environment;
            $sql = "SELECT Cast(SUBSTR(content,4,POSITION(';' IN content)-4) as SIGNED) AS date,content,tname 
                    FROM site_text 
                    WHERE status = 1 AND tname like '".eCRC(make_ebene($environment["parameter"][4])).".%' order by date ASC";
            $result = $db -> query($sql);
            $count = 0;
            $preg = "^\[!\][0-9]*";
            while ( $data = $db -> fetch_array($result,1) ) {
                $count = $count+10;
                $content = preg_replace("|^\[!\][0-9]*|","[!]".$count,$data["content"]);
                $sql_update = "UPDATE site_text SET content='".$content."' WHERE status = 1 and tname ='".$data["tname"]."'";
                $result_update = $db -> query($sql_update);
            }
        }

        renumber_blog();

        // dann punkt hoch oder runter
        $sql = "SELECT Cast(SUBSTR(content,4,POSITION(';' IN content)-4) as SIGNED) AS date,content,tname 
                FROM site_text 
                WHERE status = 1 AND tname ='".eCRC(make_ebene($environment["parameter"][4])).".".$environment["parameter"][2]."'";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);

        if ( $environment["parameter"][1] == "down" ) {
            $sort = $data["date"]-11;
        } else {
            $sort = $data["date"]+11;
        }

        $content = preg_replace("|^\[!\][0-9]*|","[!]".$sort,$data["content"]);
        $sql = "UPDATE site_text SET content='".$content."' WHERE status = 1 and tname ='".$data["tname"]."'";
        $result = $db -> query($sql);

        renumber_blog();

         header("Location: ".$pathvars["virtual"].make_ebene($environment["parameter"][4]).".html");

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>