<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menued-functions.inc.php 311 2005-03-12 21:46:39Z chaot $";
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

    function filelist($result,$script_name,$group="") {
        global $db, $cfg, $defaults, $pathvars, $environment, $dataloop, $hidedata;

        // Suchstring wird mitgegeben - wird (vermutlich nicht mehr benoetigt)
        $getvalues = "";
//         if ( $_SERVER["QUERY_STRING"] != "" ){
//             $getvalues = "?".$_SERVER["QUERY_STRING"];
//         }

        $dataloop["list"] = array();

        while ( $data = $db -> fetch_array($result,1) ) {

            if ( is_array($_SESSION["file_memo"]) && $environment["parameter"][0] == "list" ) {
                if (in_array($data["fid"],$_SESSION["file_memo"])) {
                    $link = $cfg[$script_name]["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].",".$environment["parameter"][3].".html".$getvalues;
                    $icon = $cfg[$script_name]["iconpath"]."cms-cb1.png";
                    $checked = " checked=\"checked\"";
                } else {
                    $link = $cfg[$script_name]["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].",".$environment["parameter"][3].".html".$getvalues;
                    $icon = $cfg[$script_name]["iconpath"]."cms-cb0.png";
                    $checked = "";
                }
            } elseif ( is_array($_SESSION["compilation_memo"][$environment["parameter"][1]]) && $environment["parameter"][0] == "compilation") {
                if (in_array($data["fid"],$_SESSION["compilation_memo"][$environment["parameter"][1]])) {
                    $link = $cfg[$script_name]["basis"]."/compilation,".$environment["parameter"][1].",".$data["fid"].",".$environment["parameter"][3].".html".$getvalues;
                    $icon = $cfg[$script_name]["iconpath"]."cms-cb1.png";
                    $checked = " checked=\"checked\"";
                } else {
                    $link = $cfg[$script_name]["basis"]."/compilation,".$environment["parameter"][1].",".$data["fid"].",".$environment["parameter"][3].".html".$getvalues;
                    $icon = $cfg[$script_name]["iconpath"]."cms-cb0.png";
                    $checked = "";
                }
            } else {
                $link = $cfg[$script_name]["basis"]."/".$environment["parameter"][0].",".$environment["parameter"][1].",".$data["fid"].",".$environment["parameter"][3].".html".$getvalues;
                $icon = $cfg[$script_name]["iconpath"]."cms-cb0.png";
                $checked = "";
            }
            $cb = "<a href=".$link."><img width=\"13\" height\"13\" border=\"0\" src=\"".$icon."\"></a>";

            // table color change
            if ( $cfg[$script_name]["color"]["set"] == $cfg[$script_name]["color"]["a"]) {
                $cfg[$script_name]["color"]["set"] = $cfg[$script_name]["color"]["b"];
            } else {
                $cfg[$script_name]["color"]["set"] = $cfg[$script_name]["color"]["a"];
            }

            // file art
            $type = $cfg["file"]["filetyp"][$data["ffart"]];

            // link target
            if ( $data["ffart"] == "pdf" ) {
                $target = "_blank";
            } else {
                $target = "";
            }

            // new line?
            if ( $cfg[$script_name]["db"]["file"]["line"] != "" ) {
                $i++; $even = $i / $cfg[$script_name]["db"]["file"]["line"];
                if ( is_int($even) ) {
                    $newline = $cfg[$script_name]["db"]["file"]["newline"];
                } else {
                    $newline = "";
                }
            }

            // onclick link start / end
            if ( $cfg[$script_name]["image_tag"] == "" ) $cfg[$script_name]["image_tag"] = "img"; # kompatibilitaet
            $la = $cfg[$script_name]["tags"][$cfg[$script_name]["image_tag"]][3]
                 .$cfg["file"]["base"]["webdir"]
                 .$data["ffart"]."/"
                 .$data["fid"]."/";
            //   "o/"
            $lb = $data["ffname"]
                 .$cfg[$script_name]["tags"][$cfg[$script_name]["image_tag"]][4];

            // keine weiteren parameter fuer others
            if ( $cfg["file"]["filetyp"][$data["ffart"]] != "img" ) {
                $lb = $data["ffname"];
            }

            // csv-datei?
            if ( $data["ffart"] == "csv" ) {
                $cvs_click = "ebInsertCVS(ebCanvas, '', '".$la.$lb."', '".$data["funder"]."', '');";
                $cvs_title = "[Datei als Tabelle einf&uuml;gen]";
             } else {
                $cvs_click = "";
                $cvs_title = "";
            }


            // src-pfad fuer das thumbnail
            $src = $cfg["file"]["base"]["webdir"].
                   $cfg["file"]["base"]["pic"]["root"].
                   $cfg["file"]["base"]["pic"]["tn"]."tn_".
                   $data["fid"].".".$data["ffart"];
            $src_realname = $cfg["file"]["base"]["webdir"].
                            $data["ffart"]."/".
                            $data["fid"]."/".
                           "tn/".
                           $data["ffname"];

            // download-link
            if ( $cfg["file"]["base"]["realname"] == True ) {
                $download = $cfg["file"]["base"]["webdir"].
                            $data["ffart"]."/".
                            $data["fid"]."/".
                            $data["ffname"];
            } else {
                $download = $cfg["file"]["base"]["webdir"].
                            $cfg["file"]["base"][$cfg["file"]["fileopt"][$type]["name"]].
                            $cfg["file"]["fileopt"][$type]["name"]."_".
                            $data["fid"].".".$data["ffart"];
            }

            // sortierkritierium fuer die compilations
            if ( $group != "" ) {
                preg_match("/#p".$group."[,]*([0-9]*)#/i",$data["fhit"],$match);
                $sort = $match[1];
            } else {
                $sort = "";
            }

            $name = "list";
            if ( $cfg["file"]["filetyp"][$data["ffart"]] == "img" ){
                $name = "list_images";
            } else {
                $name = "list_other";
            }

            $dataloop[$name][$data["fid"]] = array (
                                               "id" => $data["fid"],
                                              "art" => $data["ffart"],
                                            "color" => $cfg[$script_name]["color"]["set"],
                                          "checked" => $checked,
                                            "ehref" => "edit,".$data["fid"].".html",
                                            "dhref" => $download,
                                            "vhref" => $environment["allparameter"]."/view,o,".$data["fid"].",".$group.".html",
                                              "src" => $src,
                                          "dtarget" => $target,
                                              "alt" => htmlspecialchars($data["ffname"]),
                                            "title" => htmlspecialchars($data["ffname"]),
                                            "under" => htmlspecialchars($data["funder"]),
                                             "desc" => htmlspecialchars($data["fdesc"]),
                                               "cb" => $cb,
                                            "ohref" => "list/view,o,".$data["fid"].".html",
                                            "bhref" => "list/view,b,".$data["fid"].".html",
                                            "mhref" => "list/view,m,".$data["fid"].".html",
                                            "shref" => "list/view,s,".$data["fid"].".html",
                                         "ohref_lb" => str_replace("/tn/","/o/",$src_realname),
                                         "bhref_lb" => str_replace("/tn/","/b/",$src_realname),
                                         "mhref_lb" => str_replace("/tn/","/m/",$src_realname),
                                         "shref_lb" => str_replace("/tn/","/s/",$src_realname),
                                                       // new: ebInsertImage(ebCanvas);
                                           "oclick" => "ebInsertImage(ebCanvas, '".strtoupper($cfg[$script_name]["image_tag"])."', '".$la."o/".$lb."', '".htmlspecialchars($data["funder"])."', '".$cfg[$script_name]["tags"]["img"][5]."');",
                                           "bclick" => "ebInsertImage(ebCanvas, '".strtoupper($cfg[$script_name]["image_tag"])."', '".$la."b/".$lb."', '".htmlspecialchars($data["funder"])."', '".$cfg[$script_name]["tags"]["img"][5]."');",
                                           "mclick" => "ebInsertImage(ebCanvas, '".strtoupper($cfg[$script_name]["image_tag"])."', '".$la."m/".$lb."', '".htmlspecialchars($data["funder"])."', '".$cfg[$script_name]["tags"]["img"][5]."');",
                                           "sclick" => "ebInsertImage(ebCanvas, '".strtoupper($cfg[$script_name]["image_tag"])."', '".$la."s/".$lb."', '".htmlspecialchars($data["funder"])."', '".$cfg[$script_name]["tags"]["img"][5]."');",
                                           "fclick" => "ebInsertother(ebCanvas, '".strtoupper($cfg[$script_name]["image_tag"])."', '".$la.$lb."', '".htmlspecialchars($data["funder"])."', '');",
                                        "cvs_click" => $cvs_click,
                                        "cvs_title" => $cvs_title,
                                          "newline" => $newline,
                                             "sort" => $sort,
                                              );
            $dataloop["list_files"][$data["fid"]] = $dataloop[$name][$data["fid"]];
        }
        if ( count($dataloop["list_images"]) > 0 ) $hidedata["list_images"] = array();
        if ( count($dataloop["list_other"]) > 0 )  $hidedata["list_other"]  = array();
        if ( count($dataloop["list_files"]) > 0 )  $hidedata["list_files"]  = array();
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>