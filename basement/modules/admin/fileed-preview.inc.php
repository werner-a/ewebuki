<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
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

    // Sql Query
    $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]." = ".$environment["parameter"][1];
    $result = $db -> query($sql);
    $data = $db -> fetch_array($result, NOP);

    switch ( $data["ffart"] ) {
        case jpg:
            $ausgaben["output"] .= "<img border=\"0\" src=\"".$cfg["file"]["webdir"].$cfg["file"]["picture"]."/".$environment["parameter"][2]."/img_".$data["fid"].".".$data["ffart"]."\">";
            $ausgaben["output"] .= "<br><br><a href=\"".$cfg["basis"]."/preview,".$data["fid"].",original.html\">Original</a> ";
            $ausgaben["output"] .= "<a href=\"".$cfg["basis"]."/preview,".$data["fid"].",small.html\">Klein</a> ";
            $ausgaben["output"] .= "<a href=\"".$cfg["basis"]."/preview,".$data["fid"].",medium.html\">Mittel</a> ";
            $ausgaben["output"] .= "<a href=\"".$cfg["basis"]."/preview,".$data["fid"].",big.html\">Groß</a> ";
            break;
        case png:
            $ausgaben["output"] .= "<img border=\"0\" src=\"".$cfg["file"]["webdir"].$cfg["file"]["picture"]."/".$environment["parameter"][2]."/img_".$data["fid"].".".$data["ffart"]."\">";
            $ausgaben["output"] .= "<br><br><a href=\"".$cfg["basis"]."/preview,".$data["fid"].",original.html\">Original</a> ";
            $ausgaben["output"] .= "<a href=\"".$cfg["basis"]."/preview,".$data["fid"].",small.html\">Klein</a> ";
            $ausgaben["output"] .= "<a href=\"".$cfg["basis"]."/preview,".$data["fid"].",medium.html\">Mittel</a> ";
            $ausgaben["output"] .= "<a href=\"".$cfg["basis"]."/preview,".$data["fid"].",big.html\">Groß</a> ";
            break;
        case pdf:
            $ausgaben["output"] .= "<a target=\"_blank\" href=\"".$cfg["file"]["webdir"].$cfg["file"]["text"]."/doc_".$data["fid"].".".$data["ffart"]."\"><img hight=\"64\" width\"64\" border=\"0\" src=\"".$pathvars["images"]."pdf.png\"></a>";
            break;
    }


    $ausgaben["output"] .= "<br>".$data["ffname"]."<br><br>";
    #$ausgaben["output"] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\">Zurueck</a>";
    $ausgaben["output"] .= "<a href=\"".$cfg["basis"]."/list.html\">Zurueck</a>";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
