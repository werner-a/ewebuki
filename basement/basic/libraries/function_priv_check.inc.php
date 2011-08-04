<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "rekursive pruefung der rechte";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

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

    // aufruf: $priv_check(ebene,kategorie,database,$right);
    // funktion prueft rekursiv, ob die aktuelle url rechte in der $_SESSION["content"] besitzt !

    function priv_check($url,$required,$dbase="") { 
        global $cfg,$specialvars,$rechte;
        if ( !function_exists(priv_check_path) ) {            
            function priv_check_path($url,$required,&$hit,&$del,$dbase) {                
                global $environment;
                if ( $url == "" ) $url = $environment["ebene"]."/".$environment["kategorie"];
                if ( is_array($_SESSION["content"] ) ){                    
                    $array = explode(";",$required);
                    foreach ( $array as $value ) {
                        if ( strpos($_SESSION["content"][$dbase][$url]["del"],$value) !== False ) {
                            $del[$value] = -1;
                        }
                        if ( strpos($_SESSION["content"][$dbase][$url]["add"],$value) !== False && $del[$value] != -1) {
                            $hit = True;
                        }
                    }
                }
                if ( $url != "/" ) {
                    $url = dirname($url);
                    priv_check_path($url,$required,$hit,$del,$dbase);
                }
            }
        }
        if ( $specialvars["security"]["new"] != -1 ) {
            if ( $required == "" ) {
                $url = dirname($url);
                $funktion = basename($url);
                $required = $cfg["auth"]["menu"][$funktion][1];
            }
            $array = explode(";",$required);
            foreach( $array as $value) {
                if ( $rechte[$value] == -1 ) return True;
            }
        } else {
            $hit = "";
            $del= array();
            if ( $required != "" ) {
                priv_check_path($url,$required,$hit,$del,$dbase);
            }
            return $hit;
        }
    }

    function priv_info($url,&$hit) {
        global $db;
        $sql = "SELECT * FROM auth_content INNER JOIN auth_group ON (auth_content.gid=auth_group.gid) INNER JOIN auth_priv ON (auth_content.pid=auth_priv.pid) WHERE tname='".$url."'";
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            if ( $all["neg"] == -1 ) {
                $hit[$url]["del"][$all["ggroup"]] .= $all["priv"].",";
            } else {
                $hit[$url]["add"][$all["ggroup"]] .= $all["priv"].",";
            }
        }
        if ( $url != "/" ) {
            $url = dirname($url);
            priv_info($url,$hit);
        }
        return $hit;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>