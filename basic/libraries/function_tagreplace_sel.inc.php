<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_tagreplace_sel.inc.php v1 chaot
// tagreplace "sel" funktion
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function tagreplace_sel($replace, $opentag, $tagoriginal, $closetag, $sign) {

        global $db, $pathvars, $cfg, $defaults, $specialvars, $SEL_GRP_counter, $hidedata, $dataloop;

        $tagwert = $tagoriginal;
        // ------------------------------

        $SEL_GRP_counter++;
        $tag_value = explode("]",$tagwert,2);
        $tag_param = explode(";",$tag_value[0]);
        $tag_extra = explode(":",$tag_param[3]);
        $tag_special = explode(":",$tag_param[0]);

        if ( !preg_match("/[0-9]+/", $tag_param[0])  ) {
            $sel = "selection not ready";
            $replace = str_replace($opentag.$tagoriginal.$closetag, $sel, $replace);
        } else {
            $path = dirname($pathvars["requested"]);
            if ( substr( $path, -1 ) != '/') $path = $path."/";
            $link = $path.basename($pathvars["requested"],".html")."/view,".$tag_param[1].",#,".$tag_param[0].",".$tag_param[2].".html"; # /view,groesse,bild,selektion,thumbs

            // tcpdf extra
            if ( $cfg["pdfc"]["state"] == true ) {
                if ( empty($defaults["tag"]["sel_pdfc"]) )  $defaults["tag"]["sel_pdfc"]  = "<b>##title##</b><br />\n<br />\n";
                if ( empty($defaults["tag"]["*sel_pdfc"]) ) $defaults["tag"]["*sel_pdfc"] = "<img src=\"##tn##\" alt=\"##funder##\" title=\"##funder##\" ##imgsize## />\n";
                if ( empty($defaults["tag"]["/sel_pdfc"]) ) $defaults["tag"]["/sel_pdfc"] = "";

                $selection["sel"]  = $defaults["tag"]["sel_pdfc"];
                $selection["*sel"] = $defaults["tag"]["*sel_pdfc"];
                $selection["/sel"] = $defaults["tag"]["/sel_pdfc"];
            } else {
                if ( empty($defaults["tag"]["sel"]) )  $defaults["tag"]["sel"]  = "<div style=\"position:relative\" class=\"selection_teaser\">##no_image####youtube_div##\n<b>##title## ##youtube_link##</b>\n##no_image_end##<div>\n<ul>\n";
                if ( empty($defaults["tag"]["*sel"]) ) $defaults["tag"]["*sel"] = "<li class=\"thumbs\"##style##>\n<a href=\"##link##\" ##lb##class=\"pic\" title=\"##fdesc##\"><img src=\"##tn##\" alt=\"##funder##\" title=\"##funder##\"/></a>\n</li>\n";
                if ( empty($defaults["tag"]["/sel"]) ) $defaults["tag"]["/sel"] = "</ul>\n</div>\n<span##display##>g(compilation_info)(##count## g(compilation_pics))</span>\n</div>";

                $selection["sel"]  = $defaults["tag"]["sel"];
                $selection["*sel"] = $defaults["tag"]["*sel"];
                $selection["/sel"] = $defaults["tag"]["/sel"];
            }

            if ( strstr($tag_param[0],":") ) {
                $sel_pics = null;
                foreach ( $tag_special as $pics ) {
                    if ( isset($pics) ) {
                        if ( $pics == "" ) continue;
                    }
                    ( isset($sel_pics) ) ? $trenner = " ," : $trenner = null;
                    $sel_pics .= $trenner.$pics;
                }
                if ( !isset($sel_pics) ) $sel_pics = 0;
                $sql = "SELECT * FROM site_file WHERE fid in (".$sel_pics.")";
                $sel_pics1 = explode(":",$tag_param[0]);
                $i = 0;
                foreach ( $sel_pics1 as $key => $value ) {
                    $i++;
                    $tmp_sort[$value] = $i;
                }

                $result = $db -> query($sql);
                $files = array();
                $sortarray = array();
                while ( $data = $db -> fetch_array($result,1) ) {
                    $sortarray[] = $tmp_sort[$data["fid"]];
                    $files[] = array(
                                "fid"    => $data["fid"],
                                "sort"   => -1, // $counter ?????
                                "ffart"  => $data["ffart"],
                                "ffname" => $data["ffname"],
                                "funder" => $data["funder"],
                                "fdesc"  => $data["fdesc"]
                                );
                }
                if ( count($files) > 0 ) {
                    array_multisort( $sortarray, $files);
                }
            } else {
                $sql = "SELECT *
                          FROM site_file
                         WHERE fhit LIKE '%#p".$tag_param[0]."%'";
                $result = $db -> query($sql);
                $files = array();
                while ( $data = $db -> fetch_array($result,1) ) {
                    preg_match("/#p".$tag_param[0]."[,]*([0-9]*)#/i",$data["fhit"],$match);
                    $files[$match[1]] = array(
                                "fid"    => $data["fid"],
                                "sort"   => $match[1],
                                "ffart"  => $data["ffart"],
                                "ffname" => $data["ffname"],
                                "funder" => $data["funder"],
                                "fdesc"  => $data["fdesc"]
                                );
                }
                ksort($files);
                $sort = array();
                foreach ($files as $key => $row) {
                    $sort[$key]  = $row['sort'];
                }
                array_multisort($sort, $files);
            }

            $sel = str_replace("##title##", $tag_value[1], $selection["sel"]);

            // wenn video-parameter vorhanden dann marken ersetzen
            if ( isset($tag_param[5]) ) {
                if ( $tag_param[5] != "" ) {
                    $sel = str_replace("##youtube_div##","<div class=\"new_box new_space_inside\" style=\"background: #EEF3FB;height:212px;width:250px;display:none\" id=\"".$tag_param[0]."_video\">
                    [OBJECT=http://www.youtube.com/v/".$tag_param[5]."&hl=de_DE&fs=1&;250;192;application/x-shockwave-flash]
                    [PARAM=movie]http://www.youtube.com/v/".$tag_param[5]."&hl=de_DE&fs=1&[/PARAM]
                    [PARAM=wmode]transparent[/PARAM]
                    [/OBJECT]
                    <span style=\"float:right\"><b><a onclick=\"Element.setStyle('".$tag_param[0]."_video', 'display:none');\">Schliessen</a></b></span></div>",$sel);
                    $sel = str_replace("##youtube_link##","<a onclick=\"Element.setStyle('".$tag_param[0]."_video', 'display:block;position:absolute;left:-1px;top:-228px');\">Video</a>",$sel);
                } else {
                    $sel = str_replace("##youtube_div##","",$sel);
                    $sel = str_replace("##youtube_link##","",$sel);
                }
            } else {
                $sel = str_replace("##youtube_div##","",$sel);
                $sel = str_replace("##youtube_link##","",$sel);
            }

            $lb_helper = null;
            $file_counter = 0;
            foreach ( $files as $row ) {
                $file_counter ++;

                $file = $cfg["file"]["base"]["maindir"]
                       .$cfg["file"]["base"]["pic"]["root"]
                       .$cfg["file"]["base"]["pic"]["tn"]
                       ."tn_".$row["fid"].".".$row["ffart"];
                $imgsize = getimagesize($file);
                #echo "<pre>"; print_r($imgsize); echo "</pre>";

                // tcpdf extra
                if ( $cfg["pdfc"]["state"] == true ) {
                    $img = $cfg["file"]["base"]["webdir"]
                          .$row["ffart"]."/"
                          .$row["fid"]."/"
                          .$tag_param[1]."/"
                          .$row["ffname"];
                    $tn = $cfg["file"]["base"]["webdir"]
                         .$cfg["file"]["base"]["pic"]["root"]
                         .$cfg["file"]["base"]["pic"]["tn"]
                         ."tn_".$row["fid"].".".$row["ffart"];
                    $hidedata["img_meta"][0] = true;
                    $dataloop["img_meta_".$specialvars["actual_label"]][] = array(
                                                "tag" => "", //$beschriftung
                                            "caption" => $row["funder"],
                                        "description" => $row["fdesc"]
                                              );
                } elseif ( $cfg["file"]["base"]["realname"] == True ) {
                    $img = $cfg["file"]["base"]["webdir"]
                          .$row["ffart"]."/"
                          .$row["fid"]."/"
                          .$tag_param[1]."/"
                          .$row["ffname"];
                    $tn =$cfg["file"]["base"]["webdir"]
                          .$row["ffart"]."/"
                          .$row["fid"]."/"
                          ."tn/"
                          .$row["ffname"];
                } else {
                    $img = $cfg["file"]["base"]["webdir"]
                          .$cfg["file"]["base"]["pic"]["root"]
                          .$cfg["file"]["base"]["pic"][$tag_param[1]]
                          ."img_".$row["fid"].".".$row["ffart"];
                    $tn = $cfg["file"]["base"]["webdir"]
                         .$cfg["file"]["base"]["pic"]["root"]
                         .$cfg["file"]["base"]["pic"]["tn"]
                         ."tn_".$row["fid"].".".$row["ffart"];
                }

                $style = null;
                if ( !isset($tag_param[4]) ) $tag_param[4] = null;

                if ( !in_array( $row["fid"], $tag_extra ) && $tag_param[3] != "a" ) {
                    if ( $tag_param[4] == "l" ) {
                        $style = " style=\"display:none;\"";
                    } else {
                        continue;
                    }
                }

                if ( $tag_param[4] == "l" ) {
                    $changed = $img;
                    $lb = "rel=\"lightbox[group_".$SEL_GRP_counter."]\" ";
                } else {
                    $changed = str_replace( "#", $row["fid"], $link);
                    $lb = null;
                }

                if ( !isset($tag_param[3]) && $tag_param[4] == "l" && $file_counter == 1 ){
                    $tn1 = $img;
                    continue;
                }

                $s = array("##link##", "##lb##", "##tn##", "##img##", "##funder##", "##fdesc##", "##style##", "##imgsize##");
                $r = array($changed, $lb, $tn, $img, $row["funder"], $row["fdesc"], $style, $imgsize[3]);
                $sel .= str_replace($s, $r, $selection["*sel"]);
            }

            if ( !isset($tag_param[3]) && $tag_param[4] == "l" ) {
               $ArrayReplace = array(count($files), " style=\"display:none\"");
            } else {
               $ArrayReplace = array(count($files), "");
            }

            $sel .= str_replace(array("##count##", "##display##"), $ArrayReplace, $selection["/sel"]);

            if ( !isset($tag_param[3]) ) {
                if ( $tag_param[4] == "l" ) {
                    $sel = str_replace("##no_image##", "<a href=\"".$tn1."\" ".$lb.">",$sel);
                    $sel = str_replace("##no_image_end##", "</a>",$sel);
                } else {
                    $changed = str_replace( "#", $files[0]["fid"], $link);
                    $sel = "<a href=\"".$changed."\">".$tag_value[1]."</a>";
                }
            } else {
                $sel = str_replace("##no_image##", "", $sel);
                $sel = str_replace("##no_image_end##", "", $sel);
            }
            $replace = str_replace($opentag.$tagoriginal.$closetag,$sel,$replace);
        }

        // ------------------------------
        return $replace;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
