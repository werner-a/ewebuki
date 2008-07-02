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

    if ( $cfg["bloged"]["blogs"][make_ebene($environment["parameter"][4])]["right"] == "" || 
    ( priv_check(make_ebene($environment["parameter"][3]),$cfg["bloged"]["blogs"][make_ebene($environment["parameter"][4])]["right"]) || ( function_exists(priv_check_old) && priv_check_old("",$cfg["bloged"]["blogs"][make_ebene($environment["parameter"][4])]["right"]) ) )
    ) {

        function renumber_blog ($kategorie="") {
            global $db,$environment,$blog,$cfg;
            $where = "";
            if ( $kategorie != "" ) {
                $where = " AND SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$blog]["category"]."]' IN content)+11,POSITION('[/".$cfg["bloged"]["blogs"][$blog]["category"]."]' IN content)-11-POSITION('[".$cfg["bloged"]["blogs"][$blog]["category"]."]' IN content)) ='".$kategorie."'";
            }
            $sql = "SELECT Cast(SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$blog]["sort"][0]."]' IN content)+6,POSITION('[/".$cfg["bloged"]["blogs"][$blog]["sort"][0]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$blog]["sort"][0]."]' IN content)-6) AS SIGNED) AS date,content,tname 
                    FROM site_text 
                    WHERE status = 1 AND tname like '".eCRC(make_ebene($environment["parameter"][4])).".%'".$where." order by date ASC";
            $result = $db -> query($sql);
            $count = 0;
            $preg = "^\[!\][0-9]*";
            while ( $data = $db -> fetch_array($result,1) ) {
                $count = $count+10;
                $content = preg_replace("|\[".$cfg["bloged"]["blogs"][$blog]["sort"][0]."\][-0-9]*\[\/".$cfg["bloged"]["blogs"][$blog]["sort"][0]."\]|","\[".$cfg["bloged"]["blogs"][$blog]["sort"][0]."\]".$count."[\/".$cfg["bloged"]["blogs"][$blog]["sort"][0]."\]",$data["content"]);
                $sql_update = "UPDATE site_text SET content='".$content."' WHERE status = 1 and tname ='".$data["tname"]."'";
                $result_update = $db -> query($sql_update);
            }
        }
            if ($cfg["bloged"]["blogs"][make_ebene($environment["parameter"][4])]["category"] != "" ) {
                $kati = make_ebene($environment["parameter"][3]); 
            } else { 
                $kati = "";
            }

        $blog = make_ebene($environment["parameter"][4]);

        renumber_blog($kati);


        // dann punkt hoch oder runter
        $sql = "SELECT SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$blog]["category"]."]' IN content),POSITION('[/".$cfg["bloged"]["blogs"][$blog]["category"]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$blog]["category"]."]' IN content)) AS kategorie,Cast(SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$blog]["sort"][0]."]' IN content)+6,POSITION('[/".$cfg["bloged"]["blogs"][$blog]["sort"][0]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$blog]["sort"][0]."]' IN content)-6) AS SIGNED) AS date,content,tname 
                FROM site_text 
                WHERE status = 1 AND tname ='".eCRC(make_ebene($environment["parameter"][4])).".".$environment["parameter"][2]."'";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        if ( $data["kategorie"] != "" ) {
            $jump = substr($data["kategorie"],11);
        } else {
            $jump = make_ebene($environment["parameter"][4]);
        }
        if ( $environment["parameter"][1] == "down" ) {
            $sort = $data["date"]-11;
        } else {
            $sort = $data["date"]+11;
        }

        $content = preg_replace("|\[".$cfg["bloged"]["blogs"][$blog]["sort"][0]."\][0-9]*\[\/".$cfg["bloged"]["blogs"][$blog]["sort"][0]."\]|","\[".$cfg["bloged"]["blogs"][$blog]["sort"][0]."\]".$sort."[\/".$cfg["bloged"]["blogs"][$blog]["sort"][0]."\]",$data["content"]);
        $sql = "UPDATE site_text SET content='".$content."' WHERE status = 1 and tname ='".$data["tname"]."'";
        $result = $db -> query($sql);

        renumber_blog($kati);

        header("Location: ".$pathvars["virtual"].$jump.".html");

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>