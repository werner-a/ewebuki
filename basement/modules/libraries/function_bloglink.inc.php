<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: bloglink.inc.php 311 2005-03-12 21:46:39Z chaot $";
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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function bloglink($url,$anzahl,$tag,$length=25) {
        global $db;
        $sql = "SELECT Cast(SUBSTR(content,6,19) as DATETIME) AS date,content,tname from site_text WHERE content REGEXP '^\\\[!\\\]1;' AND tname like '".crc32($url).".%' order by date DESC Limit 0,5";
        $result = $db -> query($sql);
        $links = "<ul>";
        while ( $data = $db -> fetch_array($result,1) ) {
            $preg1 = "\.([0-9]*)$";
            preg_match("/$preg1/",$data["tname"],$id);
            $test = preg_replace("|\r\n|","\\r\\n",$data["content"]);
            $preg = "\[".$tag."\](.*)\[\/".$tag."\]";
            preg_match("/$preg/U",$test,$regs);
            if ( $regs[1] == "" ) continue;
            $regs[1] = preg_replace("|\\\\r\\\\n|","",$regs[1]);   
            $regs[1] = substr($regs[1],0,$length);
            $links .= "<li><a href=\"".$url."/".$id[1].".html\">".$regs[1]."</a></li>";
        }
        $links .= "</ul>";
        return $links;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
