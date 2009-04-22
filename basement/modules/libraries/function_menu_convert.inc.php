<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menu-convert.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "funktion loader";
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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( !function_exists(make_id)) {
    function make_id($url) {
        global $db;
        $leer[] = "";
        $test = split("/",$url);
        $cleaned_up = array_diff($test, $leer);

        $data["mid"] = 0;
        foreach ( $cleaned_up as $value ) {
            $sql = "SELECT *
                      FROM site_menu
                     WHERE entry = '".$value."'
                       AND refid = ".$data["mid"];
            $result = $db -> query($sql);
            if ( $db -> num_rows($result) == 1 ) {
                $data = $db -> fetch_array($result,1);
            } else {
                break;
            }
        }
        return $data;
    }
}
if ( !function_exists(make_ebene)) {
    function make_ebene($mid, $ebene="") {
        # call: make_ebene(refid);
        global $db, $cfg;
        $sql = "SELECT refid, entry
                FROM site_menu
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
if ( !function_exists(tname2path)) {
    // findet zu einem tname die passende url
    function tname2path($tname,$refid=0,$ebene="") {
        global $db;

        $sql = "SELECT *
                  FROM site_menu
                 WHERE refid=".$refid."
              ORDER BY mid";
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result) ) {
            if ( $ebene == "" ) {
                $tmp_tname = $data["entry"];
            } else {
                $tmp_tname = eCRC($ebene).".".$data["entry"];
            }
            $path = $ebene."/".$data["entry"];
            $return_value = "";
            if ( $tname == $tmp_tname ) {
                return $path;
            } elseif ( strstr($tname,eCRC($ebene."/".$data["entry"]).".") ) {
                return $path."/".substr($tname,(strpos($tname,".")+1));
            } else {
                $return_value = tname2path($tname,$data["mid"],$path);
            }
            if ( $return_value != "" ) return $return_value;
        }
        if ( $return_value == "" && $refid == 0 ) {
            $return_value = "/";
            if ( !strstr($tname,".") ) {
                $return_value .= $tname;
            }
        }
        return $return_value;
    }
}
if ( !function_exists(url2Loop)) {
    function url2Loop( $url , &$array=array() , &$array_used=array() , $refid=0 ) {
        global $db, $pathvars, $environment;

        $path_parts = explode("/",trim($url,"/") );
        $work_part = array_shift($path_parts);
        $array_used[] = $work_part;

        $sql = "SELECT *
                  FROM site_menu
                  JOIN site_menu_lang ON (site_menu.mid=site_menu_lang.mid)
                 WHERE entry='".$work_part."'
                   AND lang='".$environment["language"]."'
                   AND refid=".$refid;
        $result = $db -> query($sql);
        $num = $db -> num_rows($result);
        $data = $db -> fetch_array($result);

        if ( $data["label"] != "" ) {
            $label = $data["label"];
        } else {
            $label = "#(your_position)";
        }
        $array[] = array(
            "entry" => $work_part,
            "label" => $label,
             "link" => $pathvars["virtual"]."/".implode("/",$array_used).".html",
        );

        if ( count($path_parts) > 0 ) {
            url2Loop( implode("/",$path_parts) , $array , $array_used , $data["mid"] );
        }

        return $array;
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
