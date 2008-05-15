<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: contented-functions.inc.php 1252 2008-02-25 11:46:56Z krompi $";
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

    /* um funktionen z.b. in der kategorie add zu laden, leer.cfg.php wie folgt aendern
    /*
    /*    "function" => array(
    /*                 "add" => array( "function1_name", "function2_name"),
    */

//     if ( in_array("makece", $cfg["wizard"]["function"][$environment["kategorie"]]) ) {
//          function function_name(  $var1, $var2 = "") {
//             ### put your code here ###
//          }
//     }

    // content editor erstellen
    if ( is_array($cfg["wizard"]["function"]) && in_array("makece", $cfg["wizard"]["function"][$environment["kategorie"]]) ) {

        function makece($ce_formname, $ce_name, $ce_inhalt,$allowed_tags=array()) {
            global $debugging, $environment, $db, $cfg, $pathvars, $ausgaben, $specialvars, $defaults;

            // label fuer neue buttons fuellen
            $sql = "SELECT label, content
                      FROM ". SITETEXT ."
                     WHERE tname='-141347382.modify'
                       AND lang='".$environment["language"]."'";
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result  = $db -> query($sql);
            while ( $data = $db -> fetch_array($result) ) {
                $label[$data["label"]] = $data["content"];
            }

            $cms_old_mode = False;
            $tag_marken = explode(":",$environment["parameter"][4]);
            foreach( $cfg["wizard"]["tags"] as $key => $value ) {

                // feststellen, ob der tag erlaubt ist
                if ( is_array($allowed_tags) && !in_array($key,$allowed_tags) ) {
                    continue;
                }

//                 // js code erstellen
//                 if ( $ausgaben["js"] == "" ) {
//                     $c = "if";
//                 } else {
//                     $c = "else if";
//                 }
//
                if ( $value[1] != "" ) {
                    $k = " [KEY-".$value[1]."]";
                } else {
                    $k = "";
                }

                if ( $value[2] == False ) {
                    $s = "' + selText + '";
                } else {
                    $s = "";
                }

                if ( $value[3] != "" ) {
                    $l = $value[3];
                } else {
                    $l = "]";
                }

                if ( $value[6] == "" ) {
                    $keyX = $key;
                } else {
                    $keyX = $value[6];
                }

//              else if (st=='b')
//              st='[B]' + selText + '[\/B]';
//
//                 $ausgaben["js"] .= "    ".$c." (st=='".$key."')\n";
//                 $ausgaben["js"] .= "        st='[".strtoupper($key).$l.$s.$value[4]."[\/".strtoupper($key)."]'\n";



                if ( $value[0] == "" && $cfg["wizard"]["debug"] == True ) $value[0] = "T";

                // position (T=top, B=bottom), access key, no select, links, rechts, disable
                //                                                     ebButtons[ebButtons.length] = new ebButton(
                // id           used to name the toolbar button           'eb_h1'
                // key          label on button                          ,'H1'
                // tit          button title                             ,'Überschrift [Alt-1]'
                // position     position (top, bot)                      ,'T'
                // access       access key                               ,'1'
                // noSelect                                              ,'-1'
                // tagStart     open tag                                 ,'[H1]'
                // tagMid       mid tag                                  ,''
                // tagEnd       close tag                                ,'[/H1]'
                //                                                     );

                $ausgaben["njs"] .= "ebButtons[ebButtons.length] = new ebButton(\n";
                $ausgaben["njs"] .= "'eb_".$key."'
                                    ,'".strtoupper($key)."'
                                    ,'".$label[$key].$k."'
                                    ,'".$value[0]."'
                                    ,'".$value[1]."'
                                    ,'noSelect'
                                    ,'[".strtoupper($keyX).$l."'
                                    ,'".$value[4]."'
                                    ,'".$value[5]."[/".strtoupper($keyX)."]'\n";
                $ausgaben["njs"] .= ");\n";



//                 // buttons bauen
//                 if ( $value[0] == "T" ) {
//                     if ( $cms_old_mode == True ) {
//                         #$ausgaben["ce_button"] .= "<a href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\" onMouseOver=\"status='".$value[3]."';return true;\" onMouseOut=\"status='';return true;\"><img src=\"".$defaults["cms-tag"]["path"]."cms-tag-".$key.".png\" alt=\"".$value[3]."\" title=\"".$value[3]."\" width=\"23\" height=\"22\" border=\"0\" /></a>\n ";
//                         $ausgaben["ce_button"] .= "<a href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\" onMouseOver=\"status='#(".$key.")';return true;\" onMouseOut=\"status='';return true;\"><img src=\"".$defaults["cms-tag"]["path"]."cms-tag-".$key.".png\" alt=\"#(".$key.")\" title=\"#(".$key.")\" width=\"23\" height=\"22\" border=\"0\" /></a>\n ";
//                     } else {
//                         $ausgaben["ce_button"] .= "<a class=\"buttag\" href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\" alt=\"#(".$key.")\" title=\"#(".$key.")\" onMouseOver=\"status='#(".$key.")';return true;\" onMouseOut=\"status='';return true;\">".strtoupper($key)."</a>\n ";
//                     }
//                 } elseif ( $value[0] == "B" ) {
//                     $ausgaben["ce_bottom_button"] .= "<a class=\"buttag\" href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\" alt=\"#(".$key.")\" title=\"#(".$key.")\" onMouseOver=\"status='#(".$key.")';return true;\" onMouseOut=\"status='';return true;\">".strtoupper($key)."</a>\n ";
//                 }

//                 // dropdown bauen
//                 if ( $value[5] == "" ) {
//                     $ausgaben["ce_dropdown"] .= "<option value=\"".$key."\">".strtoupper($key)." #(".$key.")</option>\n";
//                 }
//                 #ce_anker
            }

#echo "<pre>".$ausgaben["njs"]."</pre>";

//             $ausgaben["ce_dropdown"] .= "</select>";

            // script in seite parsen
            #echo "<pre>".$ausgaben["js"]."</pre>";
            $ausgaben["ce_script"] = parser($cfg["wizard"]["tagjs"],"");

//             if ( $cms_old_mode == True ) {
//                 $ausgaben["ce_button"] .= "<input name=\"add[]\" type=\"image\" id=\"image\" value=\"add\" src=\"".$defaults["cms-tag"]["path"]."cms-tag-imgb.png\" title=\"#(add)\" width=\"23\" height=\"22\">";
//             } else {
//                 $ausgaben["ce_button"] .= "<input type=\"submit\" name=\"add[]\" value=\"FILE\" title=\"#(add)\" class=\"butoth\">";
//             }

//             $ausgaben["ce_upload"] .= "<select style=\"width:95px;font-family:Helvetica, Verdana, Arial, sans-serif;font-size:12px;\" name=\"upload\" onChange=\"submit()\">";
//             $ausgaben["ce_upload"] .= "<option value=\"\">#(upload)</option>";
//             $ausgaben["ce_upload"] .= "<option value=\"1\">1 #(file)</option>";
//             $ausgaben["ce_upload"] .= "<option value=\"2\">2 #(files)</option>";
//             $ausgaben["ce_upload"] .= "<option value=\"3\">3 #(files)</option>";
//             $ausgaben["ce_upload"] .= "<option value=\"4\">4 #(files)</option>";
//             $ausgaben["ce_upload"] .= "<option value=\"5\">5 #(files)</option>";
//             $ausgaben["ce_upload"] .= "</select>";

            return $tn;
        }

        function content_split_all($content) {
            global $cfg;

            $tag_meat = array();
            foreach ( $cfg["wizard"]["ed_boxed"] as $tag=>$preg ) {
                $open_tag = $preg[0][0];
                $close_tag = $preg[0][1];
                if ( $close_tag == "" ) $close_tag = str_replace("[","[/",$open_tag);
                $preg_tag[] = str_replace(
                                array("[","]","/"),
                                array("\[","\]","\/"),
                                $open_tag
                );
                $preg_tag[] = str_replace(
                                array("[","]","/"),
                                array("\[","\]","\/"),
                                $close_tag
                );
                $match = preg_split("/(".implode(")|(",$preg_tag).")/Us",$content,-1,PREG_SPLIT_DELIM_CAPTURE);
                $index = 0;

                $level = 0; $index = -1; $max = -1; $mark = 0; $work = $match; $pre = "";$buffer = array(); $ind_lev = array();
                foreach ( $match as $value ) {
                    if ( $value == $open_tag ) {
                        $level++; $max++;
                        $index = $max;
                        $ind_lev[$level] = $index;
                        $buffer[$index]["start"] = strlen($pre);
                    } elseif ( $value == $close_tag ) {
                        $buffer[$index]["complete"] .= $value;
                        $mark = -1;
                        $pre .= array_shift($work);
                        continue;
                    } elseif ( $mark == -1 ) {
                        $split = explode("]",$value,2);
                        // tag-infos einarbeiten
                        $buffer[$index]["complete"] .= $split[0]."]";
                        $buffer[$index]["end"] .= $buffer[$index]["start"] + strlen($buffer[$index]["complete"]);
                        $buffer[$index]["tag_start"] = substr($buffer[$index]["complete"],0,strpos($buffer[$index]["complete"],"]"))."]";
                        $buffer[$index]["tag_end"] = strrchr($buffer[$index]["complete"],"[");
                        $len = $buffer[$index]["end"] - $buffer[$index]["start"];
                        $buffer[$index]["meat"] = substr(
                                                        $buffer[$index]["complete"],
                                                        strpos($buffer[$index]["complete"],"]") + 1,
                                                        $len - strlen($buffer[$index]["tag_start"]) - strlen($buffer[$index]["tag_end"])
                                                    );
                        $buffer[$index]["type"] = $preg[1];
                        $buffer[$index]["buttons"] = $preg[2];
                        if ( $level > 1 ) {
                            $value = $buffer[$ind_lev[$level]]["complete"].$split[1];
                        } else {
                            $value = $split[1];
                        }
                        $level--; $mark = 0;
                        $index = $ind_lev[$level];
                    }
                    $pre .= array_shift($work);
                    if ( $level == 0 ) continue;
                    $buffer[$index]["complete"] .= $value;

                }
                $tag_meat[$tag] = $buffer;
            }
            return $tag_meat;
        }

        function content_level1($content) {
            global $cfg;

            // suchmuster bauen und open- und close-tags finden
            $preg = array();
            $split_tags["open"][] = "<!--edit_begin-->";
            $split_tags["close"][] = "<!--edit_end-->";
            foreach ( $cfg["wizard"]["ed_boxed"] as $key=>$value ) {
                if ( $value[0][1] == "" ) {
                    $end_tag = str_replace("[","[/",$value[0][0]);
                } else {
                    $end_tag = $value[0][1];
                }
                $split_tags["open"][]  = $value[0][0];
                $split_tags["close"][] = $end_tag;
                $preg[] = str_replace(array("[","/"),array("\[","\/"),$value[0][0]);
                $preg[] = str_replace(array("[","/"),array("\[","\/"),$end_tag);
            }
            $separate = preg_split("/(".implode("|",$preg).")|(<!--edit_begin-->)|(<!--edit_end-->)/",$content,-1,PREG_SPLIT_DELIM_CAPTURE);

            $end = "--"; $i = 0; $close = 0; $mark = 0;
            $allcontent = array();
            foreach ( $separate as $index => $line ) {
                if ( trim($line) == "" ) continue;
                if ( in_array($line,$split_tags["open"]) ) {
                    if ($close == 0) $i++;
                    $close++;
                } elseif ( in_array($line,$split_tags["close"]) ) {
                    $close--; $mark = -1;
                }
                $allcontent[$i] .= trim($line,"\n");
            }

            return array_merge($allcontent);
        }

    }

        // welche seiten unterhalb der url sind zur freigabe vorgesehen
        function find_marked_content( $url = "/", $cfg, $label, $ignore = array() ) {
            global $db, $pathvars, $environment;

            $path = explode("/",$url);
            $kategorie = array_pop($path);
            $ebene = implode("/",$path);

            // gibt es bereiche, die nicht untersucht werden sollen
            if ( count($ignore) > 0 ) {
                foreach ( $ignore as $value ) {
                    $where[] = "ebene NOT LIKE '".$value."%'";
                    $where[] = "kategorie NOT LIKE '".$value."%'";
                }
                $where = " AND (".implode(" AND ",$where).")";
            }
            $sql = "SELECT *
                      FROM site_text
                     WHERE (
                            ebene LIKE '".$url."%'
                            OR (ebene='".$ebene."' AND kategorie='".$kategorie."')
                           )".$where."
                       AND label='".$label."'
                       AND hide<0
                  ORDER BY tname, hide ASC, version DESC";
            $result = $db -> query($sql);

            $dataset = "";
            while ( $data = $db -> fetch_array($result) ) {
                if ( $dataset == $data["tname"]."::".$label ) continue;
                $dataset = $data["tname"]."::".$label;

                if ( $data["ebene"] == "" ) {
                    $tname = $data["kategorie"];
                } else {
                    $tname = crc32($data["ebene"]).".".$data["kategorie"];
                }
                $path = $data["ebene"]."/".$data["kategorie"];

                // rechte checken
                if ( $data["hide"] == -2 && !priv_check($path,"publish") ) {
                    continue;
                } elseif ( $data["hide"] == -1 && !priv_check($path,"edit") ) {
                    continue;
                }

                // titel
                $titel = "---";
                preg_match("/\[H[0-9]{1}\](.+)\[\/H/U",$data["content"],$match);
                if ( count($match) > 1 ) {
                    $titel = $match[1];
                }

                // tabellen farben wechseln
                if ( $cfg[$data["hide"]]["color"]["set"] == $cfg["color"]["a"]) {
                    $cfg[$data["hide"]]["color"]["set"] = $cfg["color"]["b"];
                } else {
                    $cfg[$data["hide"]]["color"]["set"] = $cfg["color"]["a"];
                }
                $new_releases[$data["hide"]][] = array(
                    "path" => $path,
                   "titel" => $titel,
                    "view" => $pathvars["menuroot"].$data["ebene"]."/".$data["kategorie"].",v".$data["version"].".html",
                    "edit" => $pathvars["virtual"]."/wizard/show,".$db->getDb().",".$tname.",inhalt.html",
                  "unlock" => $pathvars["virtual"]."/wizard/release,".$environment["parameter"][1].",".$tname.",".$label.",unlock,".$data["version"].".html",
                 "release" => $pathvars["virtual"]."/wizard/release,".$environment["parameter"][1].",".$tname.",".$label.",release,".$data["version"].".html",
                   "color" => $cfg[$data["hide"]]["color"]["set"],
                );
            }

            return $new_releases;
        }

    ### platz fuer weitere funktionen ###

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
