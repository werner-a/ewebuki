<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menued-functions.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "funktion loader";
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

    function filelist($result) {
        global $db, $cfg, $dataloop, $pathvars;

        // Suchstring wird mitgegeben - wird (vermutlich nicht mehr benoetigt)
        $getvalues = "";
//         if ( $_SERVER["QUERY_STRING"] != "" ){
//             $getvalues = "?".$_SERVER["QUERY_STRING"];
//         }

        while ( $data = $db -> fetch_array($result,1) ) {

            if (is_array($_SESSION["file_memo"])) {
                if (in_array($data["fid"],$_SESSION["file_memo"])) {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$getvalues."><img width=\"13\" height\"13\" border=\"0\" src=\"".$cfg["iconpath"]."cms-cb1.png\"></a>";
                } else {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$getvalues."><img width=\"13\" height\"13\" border=\"0\" src=\"".$cfg["iconpath"]."cms-cb0.png\"></a>";
                }
            } else {
                $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$getvalues."><img width=\"13\" height\"13\" border=\"0\" src=".$cfg["iconpath"]."cms-cb0.png border=0></a>";
            }

            // tabellen farben wechseln
            if ( $cfg["color"]["set"] == $cfg["color"]["a"]) {
                $cfg["color"]["set"] = $cfg["color"]["b"];
            } else {
                $cfg["color"]["set"] = $cfg["color"]["a"];
            }
            $dataloop["list"][$data["fid"]]["color"] = $cfg["color"]["set"];

            $dataloop["list"][$data["fid"]]["ehref"] = "edit,".$data["fid"].".html";

            $type = $cfg["filetyp"][$data["ffart"]];
            $dataloop["list"][$data["fid"]]["dhref"] = $pathvars["filebase"]["webdir"].
                                                       $pathvars["filebase"][$cfg["fileopt"][$type]["name"]].
                                                       $cfg["fileopt"][$type]["name"]."_".
                                                       $data["fid"].".".$data["ffart"];
            if ( $data["ffart"] == "pdf" ) {
                $dataloop["list"][$data["fid"]]["dtarget"] = "_blank";
            } else {
                $dataloop["list"][$data["fid"]]["dtarget"] = "";
            }


            $dataloop["list"][$data["fid"]]["src"] = $pathvars["filebase"]["webdir"].
                                                     $pathvars["filebase"]["pic"]["root"].
                                                     $pathvars["filebase"]["pic"]["tn"]."tn_".
                                                     $data["fid"].".".$data["ffart"];

            $dataloop["list"][$data["fid"]]["alt"] = $data["ffname"];
            $dataloop["list"][$data["fid"]]["title"] = $data["ffname"];

            $dataloop["list"][$data["fid"]]["cb"] = $cb;

            $dataloop["list"][$data["fid"]]["ohref"] = "list/view,o,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["bhref"] = "list/view,b,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["mhref"] = "list/view,m,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["shref"] = "list/view,s,".$data["fid"].".html";

            $i++;
            $even = $i / $cfg["db"]["file"]["line"];
            if ( is_int($even) ) {
                $dataloop["list"][$data["fid"]]["newline"] = $cfg["db"]["file"]["newline"];
            } else {
                $dataloop["list"][$data["fid"]]["newline"] = "";
            }
        }
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
