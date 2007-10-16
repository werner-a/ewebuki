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

    function filelist($result,$group="") {
        global $db, $cfg, $defaults, $pathvars, $dataloop;

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

            // table color change
            if ( $cfg["color"]["set"] == $cfg["color"]["a"]) {
                $cfg["color"]["set"] = $cfg["color"]["b"];
            } else {
                $cfg["color"]["set"] = $cfg["color"]["a"];
            }

            // file art
            $type = $cfg["filetyp"][$data["ffart"]];

            // link target
            if ( $data["ffart"] == "pdf" ) {
                $target = "_blank";
            } else {
                $target = "";
            }

            // new line?
            $i++; $even = $i / $cfg["db"]["file"]["line"];
            if ( is_int($even) ) {
                $newline = $cfg["db"]["file"]["newline"];
            } else {
                $newline = "";
            }

            // onclick link start / end
            $la = $cfg["tags"]["img"][3]
                 .$pathvars["filebase"]["webdir"]
                 .$data["ffart"]."/"
                 .$data["fid"]."/";
            //   "o/"
            $lb = $data["ffname"]
                 .$cfg["tags"]["img"][4];

            // sortierkritierium fuer die compilations
            if ( $group != "" ){
                preg_match("/#p".$group."[,]*([0-9]*)#/i",$data["fhit"],$match);
                $sort = $match[1];
            }else{
                $sort = "";
            }

            $dataloop["list"][$data["fid"]] = array (
                                            "color" => $cfg["color"]["set"],
                                            "ehref" => "edit,".$data["fid"].".html",
                                            "dhref" => $pathvars["filebase"]["webdir"].
                                                       $pathvars["filebase"][$cfg["fileopt"][$type]["name"]].
                                                       $cfg["fileopt"][$type]["name"]."_".
                                                       $data["fid"].".".$data["ffart"],
                                            "vhref" => "view,o,".$data["fid"].",".$group.".html",
                                              "src" => $pathvars["filebase"]["webdir"].
                                                       $pathvars["filebase"]["pic"]["root"].
                                                       $pathvars["filebase"]["pic"]["tn"]."tn_".
                                                       $data["fid"].".".$data["ffart"],
                                          "dtarget" => $target,
                                              "alt" => $data["ffname"],
                                            "title" => $data["ffname"],
                                               "cb" => $cb,
                                            "ohref" => "list/view,o,".$data["fid"].".html",
                                            "bhref" => "list/view,b,".$data["fid"].".html",
                                            "mhref" => "list/view,m,".$data["fid"].".html",
                                            "shref" => "list/view,s,".$data["fid"].".html",
                                                       // new: ebInsertImage(ebCanvas);
                                           "oclick" => "ebInsertImage(ebCanvas, '', '".$la."o/".$lb."', '".$data["funder"]."', '".$cfg["tags"]["img"][5]."');",
                                           "bclick" => "ebInsertImage(ebCanvas, '', '".$la."b/".$lb."', '".$data["funder"]."', '".$cfg["tags"]["img"][5]."');",
                                           "mclick" => "ebInsertImage(ebCanvas, '', '".$la."m/".$lb."', '".$data["funder"]."', '".$cfg["tags"]["img"][5]."');",
                                           "sclick" => "ebInsertImage(ebCanvas, '', '".$la."s/".$lb."', '".$data["funder"]."', '".$cfg["tags"]["img"][5]."');",
                                          "newline" => $newline,
                                             "sort" => $sort,
                                              );
        }
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
