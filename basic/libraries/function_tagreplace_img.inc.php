<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_tagreplace_img.inc.php v1 chaot
// tagreplace "img" funktion
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

    function tagreplace_img($replace, $opentag, $tagoriginal, $closetag, $sign) {

        global $db, $pathvars, $cfg, $defaults, $specialvars, $LB_IMG_counter, $hidedata, $dataloop;

        $tagwert = $tagoriginal;
        // ------------------------------


        // tcpdf extra
        if ( $cfg["pdfc"]["state"] == true ) {

            // /file/jpg/10/s/wolken.jpg;;0;l]Wolkenblick
            // /file/picture/medium/img_10.jpg;;0;b]Wolkenblick

            $tagwerte = explode("]",$tagwert,2);
            $imgwerte = explode(";",$tagwerte[0]);

            $s = array("/tn/", "/s/", "/m/", "/b/", "/o/");
            $r = array("/thumbnail/", "/small/", "/medium/", "/big/", "/original/");
            $imgwerte[0] = str_replace($s, $r, $imgwerte[0]);

            $oldpath = explode("/", $imgwerte[0]);

            if ( $oldpath[4] == "thumbnail" ) {
                $name = "tn_";
            } else {
                $name = "img_";
            }

            $newpath = array( $oldpath[0], $oldpath[1]."/picture", $oldpath[4], $name.$oldpath[3].".".$oldpath[2] );

            #echo "<pre>"; print_r($imgwerte); echo "</pre>";
            // no view
            $imgwerte[3] = null;

            $imgwerte[0] = implode("/", $newpath);
            $tagwerte[0] = implode(";", $imgwerte);
            $tagwert = implode("]", $tagwerte);

            #echo "<pre>"; print_r($tagwerte); echo "</pre>";
            #echo $tagwert."<br />";
        }


        $LB_IMG_counter++;
        $imgsize = null; $imgurl = null;
        if ( $sign == "]" ) {
            if ( !strstr($tagwert, "/") ) {
                $imgfile = $pathvars["fileroot"]."images/".$environment["design"]."/".$tagwert;
                if ( file_exists($imgfile) ) {
                    $imgsize = getimagesize($imgfile);
                    $imgsize = " ".$imgsize[3];
                    $imgurl = $pathvars["images"].$tagwert;
                }
            } else {
                $imgurl = $tagwert;
                if ( !strstr($tagwert, "http") ) {
                    if ( strpos($tagwert,$cfg["file"]["base"]["pic"]["root"]) === false ) {
                        $opt = explode("/",$tagwert);
                        $imgfile = $cfg["file"]["base"]["maindir"]
                                   .$cfg["file"]["base"]["pic"]["root"]
                                   .$cfg["file"]["base"]["pic"][$opt[4]]
                                   ."img_".$opt[3].".".$opt[2];
                    } elseif ( strstr($tagwert, $cfg["file"]["base"]["webdir"]) ) {
                        $imgfile = str_replace($cfg["file"]["base"]["webdir"],"",$tagwert);
                        $imgfile = $cfg["file"]["base"]["maindir"].$imgfile;
                    } else {
                        $imgfile = $pathvars["fileroot"].$tagwert;
                    }
                    if ( file_exists($imgfile) ) {
                        $imgsize = getimagesize($imgfile);
                        $imgsize = " ".$imgsize[3];
                    }
                }
            }
            $ausgabewert = "<img src=\"".$imgurl."\" title=\"".$tagwert."\" alt=\"".$tagwert."\"".$imgsize." />";
            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
        } else {
            if ( !isset($defaults["tag"]["img_w3c"]) ) $defaults["tag"]["img_w3c"] = "<img src=\"##imgurl##\" title=\"##beschriftung##\" alt=\"##beschriftung##\"##imgsize## style=\"##style_align####style_border####style_hspace####style_vspace##\"##attrib## />";
            if ( !isset($defaults["tag"]["img"]) ) $defaults["tag"]["img"] = "<img src=\"##imgurl##\"##attrib####vspace####hspace## title=\"##beschriftung##\" alt=\"##beschriftung##\"##align####border####imgsize## />";
            if ( !isset($defaults["tag"]["img_link"]) ) $defaults["tag"]["img_link"] = "<a href=\"##imglnk##\" title=\"##beschriftung##\">";
            if ( !isset($defaults["tag"]["img_link_lb"]) ) $defaults["tag"]["img_link_lb"] = "<a href=\"##imglnk##\" title=\"##beschriftung##\" ##lightbox## >";
            if ( !isset($defaults["tag"]["/img_link"]) ) $defaults["tag"]["/img_link"] = "</a>";
            $repl = array("imgurl", "beschriftung", "funder", "fdesc",
                          "imgsize", "attrib", "vspace", "hspace",
                          "align", "border", "style_align", "style_border",
                          "style_hspace", "style_vspace", "imglnk", "lightbox");
            $tagwerte = explode("]",$tagwert,2);
            $imgwerte = explode(";",$tagwerte[0]);
            $extrawerte = @explode(":",$imgwerte[1]);
            if ( isset($extrawerte[1]) ) $imgwerte[1] = $extrawerte[1];
            if ( $extrawerte[0] == "id" ) {
                $art = " id";
            } else {
                $art = " class";
            }
            $align = null; $attrib = null; $style_align = null;
            if ( @$imgwerte[1] == "r" ) {
                $align = " align=\"right\"";
                $style_align = "float:right;";
            } elseif ( @$imgwerte[1] == "l" ) {
                $align = " align=\"left\"";
                $style_align = "float:left;";
            } elseif ( isset($imgwerte[1]) ) {
                $attrib = " ".$art."=\"".$imgwerte[1]."\"";
            }
            if ( @$imgwerte[2] == "0" ) {
                $border = " border=\"0\"";
                $style_border = "border-width:0;";
            } elseif ( @$imgwerte[2] > 0 ) {
                $border = " border=\"".$imgwerte[2]."\"";
                $style_border = "border-width:".$imgwerte[2]."px;";
            } else {
                $border = null;
                $style_border = null;
            }
            if ( !isset($imgwerte[3]) ) $imgwerte[3] = null;
            if ($imgwerte[3] == "l" ) {
                $lightbox = "rel=\"lightbox[".$LB_IMG_counter."]\"";
            }
            if ( !isset($imgwerte[4]) ) {
                $vspace = null;
                $style_vspace = null;
            } else {
                $vspace = " vspace=\"".$imgwerte[4]."\"";
                $style_vspace = "margin-top:".$imgwerte[4]."px;margin-bottom:".$imgwerte[4]."px;";
            }
            if ( !isset($imgwerte[6]) ) {
                $hspace = null;
                $style_hspace = null;
            } else {
                $hspace = " hspace=\"".$imgwerte[6]."\"";
                if ( $imgwerte[1] == "r" ) {
                    $style_hspace = "margin-left:".$imgwerte[6]."px;margin-right:0px;";
                } elseif ( $imgwerte[1] == "l" ) {
                    $style_hspace = "margin-left:0px;margin-right:".$imgwerte[6]."px;";
                } else {
                    $style_hspace = "margin-left:".$imgwerte[6]."px;margin-right:".$imgwerte[6]."px;";
                }
            }
            if ( empty($tagwerte[1]) ) {
                $beschriftung = $imgwerte[0];
            } else {
                $beschriftung = $tagwerte[1];
            }
            // weitere informationen aus datenbank holen
            if ( preg_match("/^\//",$tagwerte[0]) ) {
                $img_path = explode("/",str_replace($cfg["file"]["base"]["maindir"],"",$tagwerte[0]) );
                if ( is_numeric($img_path[3]) ) {
                    $fid = $img_path[3];
                //subdir quickfix
                } elseif ( is_numeric($img_path[4]) ) {
                    $fid = $img_path[4];
                } else {
                    $fid = substr($tagwerte[0],0,strpos($tagwerte[0],";"));
                    $fid = strrchr($fid,"_");
                    $fid = substr( $fid , 1 , strpos($fid,".") - 1 );
                }
                $sql = "SELECT *
                            FROM site_file
                            WHERE fid=".$fid;
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,1);
                $funder = $data["funder"];
                $fdesc = $data["fdesc"];
            } else {
                $funder = $beschriftung;
                $fdesc = $beschriftung;
            }

            $linka = null; $linkb = null;
            if ( !strstr($imgwerte[0], "/") ) {
                $imgfile = $pathvars["fileroot"]."images/".$environment["design"]."/".$imgwerte[0];
                if ( file_exists($imgfile) ) {
                    $imgsize = getimagesize($imgfile);
                    $imgsize = " ".$imgsize[3];
                    $imgurl = $pathvars["images"].$imgwerte[0];
                } else {
                    $imgsize = null;
                }
            } else {
                $imgurl = $imgwerte[0];
                if ( !strstr($imgwerte[0], "http") ) {
                    if ( strpos($imgwerte[0],$cfg["file"]["base"]["pic"]["root"]) === false ) {
                        $opt = explode("/",str_replace($pathvars["subdir"],"",$imgwerte[0]));
                        $imgfile = $cfg["file"]["base"]["maindir"]
                                .$cfg["file"]["base"]["pic"]["root"]
                                .$cfg["file"]["base"]["pic"][$opt[4]]
                                ."img_".$opt[3].".".$opt[2];
                    } elseif ( strstr($imgwerte[0], $cfg["file"]["base"]["webdir"]) ) {
                        $imgfile = str_replace($cfg["file"]["base"]["webdir"],"",$imgwerte[0]);
                        $imgfile = $cfg["file"]["base"]["maindir"].$imgfile;
                    } else {
                        $imgfile = $pathvars["fileroot"].$imgwerte[0];
                    }
                    if ( file_exists($imgfile) ) {
                        $imgsize = getimagesize($imgfile);
                        $imgsize = " ".$imgsize[3];
                    }
                    if ( !isset($imgwerte[3]) ) $imgwerte[3] = null;
                    if ( !isset($imgwerte[7]) ) $imgwerte[7] = null;
                    if ( $imgwerte[3] != "" ) {
                        $bilderstrecke = ",".$imgwerte[7];
                    } else {
                        $bilderstrecke = null;
                    }
                    if ( $imgwerte[3] != "" ) {
                        if ( strpos($imgurl,$cfg["file"]["base"]["pic"]["root"]) === false ) {
                            $opt = explode("/",str_replace($pathvars["subdir"],"",$imgurl));
                            $imgid = $opt[3];
                        } else {
                            #$opt = split("[_.]",$imgurl); // deprecated
                            $opt = preg_split("[_.]",$imgurl);
                            $imgid = $opt[1];
                        }
                        $path = dirname($pathvars["requested"]);
                        if ( substr( $path, -1 ) != '/') $path = $path."/";
                        $imglnk = $path.basename($pathvars["requested"],".html")."/view,".$imgwerte[3].",".$imgid.$bilderstrecke.".html";
                        if ( $imgwerte[3] == "l" ) {
                            $imglnk = preg_replace("/\/(tn|s|m|b)\//","/o/",$imgurl);
                            $linka = $defaults["tag"]["img_link_lb"];
                        } else {
                            $linka = $defaults["tag"]["img_link"];
                        }
                        $linkb = $defaults["tag"]["/img_link"];
                    }
                } else {
                    $imgsize = null;
                }
            }

            if ( $specialvars["w3c"] == "strict" ) {
                $ausgabewert = $linka.$defaults["tag"]["img_w3c"].$linkb;
            } else {
                $ausgabewert = $linka.$defaults["tag"]["img"].$linkb;
            }

            // tcpdf extra
            $dataloop["img_meta_".$specialvars["actual_label"]][] = array(
                                        "tag" => $beschriftung,
                                    "caption" => $funder,
                                "description" => $fdesc
                                      );
            if ( isset($dataloop["img_meta_".$specialvars["actual_label"]]) ) {
                $hidedata["img_meta_".$specialvars["actual_label"]][0] = true;
            }

            foreach ( $repl as $value ) {
                if ( !isset($$value) ) $$value = null;
                $ausgabewert = str_replace("##".$value."##",$$value,$ausgabewert);
                $$value = "";
            }
            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
        }

        // ------------------------------
        return $replace;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
