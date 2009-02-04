<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: contented-edit.inc.php 1242 2008-02-08 16:16:50Z chaot $";
// "contented - edit funktion";
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

    // parameter-verzeichnis:
    // 1: Datenbank
    // 2: tname
    // 3: label
    // 4: [leer]
    // 5: version
    // 6: index des bereichs der im show angezeigt wird

    // erlaubnis bei intrabvv speziell setzen
    $database = $environment["parameter"][1];
    if ( is_array($_SESSION["katzugriff"]) ) {
        if ( in_array("-1:".$database.":".$environment["parameter"][2],$_SESSION["katzugriff"]) ) $erlaubnis = -1;
    }
    if ( is_array($_SESSION["dbzugriff"]) ) {
        if ( in_array($database,$_SESSION["dbzugriff"]) ) $erlaubnis = -1;
    }

    // form_referer
    if ( $_SERVER["HTTP_REFERER"] &&  !preg_match("/wizard$/",dirname($_SERVER["HTTP_REFERER"])) ) {
        $_SESSION["form_referer"] = $_SERVER["HTTP_REFERER"];
    }
    $ausgaben["form_referer"] = $_SESSION["form_referer"];

    // tname in pfad umwandeln
    $tname2path = tname2path($environment["parameter"][2]);
    if ( $tname2path == "" ) {
        $tname2path = str_replace($pathvars["menuroot"],"",$_SESSION["form_referer"]);
        $tname2path = substr($tname2path, 0, strpos($tname2path,".") );
        // tname-kontrolle
        $tname_tmp = explode("/",$tname2path);
        $kategorie = array_pop($tname_tmp);
        if ( count($tname_tmp) > 1 ) {
            $tname = eCRC(implode("/",$tname_tmp)).".".$kategorie;
        } else {
            $tname = $kategorie;
        }
        if ( $tname != $environment["parameter"][2] ) $tname2path = "/";
    }

    // welche seite wird bearbeitet
    $ausgaben["url"] = $pathvars["webroot"].$tname2path.".html";

    // leere parameter abfangen
    // * * *
    $reload = 0;
    /* fehlende datenbank */
    if ( $environment["parameter"][1] != "" ) {
        $db->selectDb($database,FALSE);
    } else {
        $reload = -1;
    }
    $environment["parameter"][1] = $db->getDb();
    /* fehlender tname */
    if ( $environment["parameter"][2] == "" ) {
        // wo kommt der nutzer her
        $path = explode("/",str_replace($pathvars["menuroot"],"",$_SERVER["HTTP_REFERER"]));
        $kategorie = str_replace(".html","", array_pop($path));
        $ebene = implode("/",$path);
        if ( strstr($kategorie,",") ) {
            $buffer = explode(",",$kategorie,2);
            $kategorie = $buffer[0];
            if ( preg_match("/^v([0-9]+)/U",$buffer[1],$match) ) {
                $environment["parameter"][4] = "";
                $environment["parameter"][5] = $match[1];
            }
        }
        if ( $kategorie == "" ) $kategorie = "index";
        if ( count($path) == 0 || (count($path) == 1 && $path[0]=="") ) {
            $environment["parameter"][2] = $kategorie;
        } else {
            $environment["parameter"][2] = eCRC($ebene).".".$kategorie;
        }
        $reload = -1;
    }
    /* fehlende label-beizeichnung */
    if ( $environment["parameter"][3] == "" ) {
        // standard-labelname einfuegen
        $environment["parameter"][3] = $cfg["wizard"]["wizardtyp"]["standard"]["def_label"];
        $reload = -1;
    }
    if ( $reload == -1 ) {
        ksort($environment["parameter"]);
        header("Location: ".$pathvars["webroot"].$cfg["wizard"]["basis"]."/".implode(",",$environment["parameter"]).".html");
        exit;
    }
    // + + +
    // leere parameter abfangen

    $ausgaben["empty_show_url"] = $cfg["wizard"]["basis"]."/".implode( ",", array_slice($environment["parameter"],0,6) ).",none.html";

    // spezial-check fuer artikel mit kategorie
    $artikel_check = "";
    $artikel_check_publish = "";
    if ( is_array($cfg["bloged"]["blogs"][substr($tname2path,0,strrpos($tname2path,"/"))])
        && $cfg["bloged"]["blogs"][substr($tname2path,0,strrpos($tname2path,"/"))]["category"] != "" ) {
        $kate = $cfg["bloged"]["blogs"][substr($tname2path,0,strrpos($tname2path,"/"))]["category"];
        $laenge = strlen($kate)+2;
        $sql = "SELECT SUBSTR(content,POSITION('[".$kate."]' IN content)+".$laenge.",POSITION('[/".$kate."]' IN content)-".$laenge."-POSITION('[".$kate."]' IN content) )as check_url from site_text where tname = '".$environment["parameter"][2]."'";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $artikel_check = priv_check($data["check_url"],$cfg["wizard"]["right"]["edit"]);
        $artikel_check_publish = priv_check($data["check_url"],"publish");

    }


    if ( priv_check($tname2path,$cfg["wizard"]["right"]["edit"])|| $artikel_check|| priv_check($tname2path,$cfg["wizard"]["right"]["publish"]) ||
         priv_check_old("",$cfg["wizard"]["right"]) ) {

        // page basics
        // ***
        if ( $environment["parameter"][5] != "" ) {
            $version = " AND version=".$environment["parameter"][5];
        } else {
            $version = "";
        }

        $sql = "SELECT *
                  FROM ". SITETEXT ."
                 WHERE lang = '".$environment["language"]."'
                   AND label ='".$environment["parameter"][3]."'
                   AND tname ='".$environment["parameter"][2]."'
                       $version
              ORDER BY version DESC
                 LIMIT 0,1";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $content_exists = $db -> num_rows($result);
        $form_values = $db -> fetch_array($result,1);

        // falls content in session zwischengespeichert ist, diesen holen
        $identifier = $environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3];
        if ( $_SESSION["wizard_content"][$identifier] != "" ) {
            $form_values["content"] = $_SESSION["wizard_content"][$identifier];
        }

        // verarbeitung fuer die ajax-vorschau auf bestimmte bereiche
        if ( $_POST["ajax_preview"] == "on" ) {
            $ajax_buffer = content_level1($form_values["content"]);
                    echo preg_replace(
                        array("/#\{.+\}/U","/g\(.+\)/U"),
                        array("",""),
                        tagreplace($ajax_buffer[$_POST["block"]])
                    );
            header("HTTP/1.0 200 OK");
            die;
        }

        if ( isset($_GET["preview"]) ) {

//             $ausgaben["output"] = tagreplace($form_values["content"]);

        } else {

            // versionen-links
            // * * *
            $sql = "SELECT version, html, content, changed, byalias
                      FROM ". SITETEXT ."
                     WHERE lang = '".$environment["language"]."'
                       AND label ='".$environment["parameter"][3]."'
                       AND tname ='".$environment["parameter"][2]."'
                  ORDER BY version";
            $result_version = $db -> query($sql);
            $$num_versions = $db -> num_rows($result_version);
            $index = 1; $hit = 0;
            while ( $data = $db -> fetch_array($result_version) ) {
                if ( $index == 1 ) $first = $data["version"];
                if ( $index == $$num_versions ) $last = $data["version"];
                if ( $hit == -1 ) {
                    $next = $data["version"];
                    $hit = 0;
                }
                if ( $data["version"] == $form_values["version"] ) {
                    $aktuell = $data["version"];
                    $i_aktuell = $index;
                    $prev = $tmp_prev;
                    $hit = -1;
                }
                $tmp_prev = $data["version"];
                $index++;
            }
            $link1 = $environment["parameter"][0].",".
                     $environment["parameter"][1].",".
                     $environment["parameter"][2].",".
                     $environment["parameter"][3].",".
                     $environment["parameter"][4].",";
            $link2 = $environment["parameter"][6];
            if ( $first != $aktuell ) {
                $hidedata["version_prev"]["link_prev"] = $link1.$prev.",".$link2.".html";
                $hidedata["version_prev"]["link_first"] = $link1.$first.",".$link2.".html";
            }
            if ( $last != $aktuell ) {
                $hidedata["version_next"]["link_next"] = $link1.$next.",".$link2.".html";
                $hidedata["version_next"]["link_last"] = $link1.$last.",".$link2.".html";
            }
            $ausgaben["vaktuell"] = $i_aktuell;
            $ausgaben["vgesamt"] = $$num_versions;
            // + + +
            // versionen-link

            // wizard-infos rausfinden (z.b. wizard-typ,..)
            // * * *
            preg_match("/\[!\]wizard:(.+)\[\/!\]/Ui",$form_values["content"],$match);
            $wizard_name = "standard";
            if ( $match[1] != "" ) {
                $info = explode(";",$match[1]);
                // typ
                if ( is_array($cfg["wizard"]["wizardtyp"][$info[0]]) ) $wizard_name = $info[0];
            }
            // + + +
            // wizard-infos rausfinden

            // freigabe-test
            // * * *
            $blocked = 0;
            if ( $specialvars["content_release"] == -1 ) {
                if ( priv_check($tname2path,"publish") || $artikel_check_publish ) {
                    $hidedata["publish"] = array();
                } else {
                    // ist bereits eine freigabe angefordert, dann blocken
                    $sql = "SELECT *
                            FROM ". SITETEXT ."
                            WHERE lang = '".$environment["language"]."'
                            AND label ='".$environment["parameter"][3]."'
                            AND tname ='".$environment["parameter"][2]."'
                            AND status=-2";
                    $result = $db -> query($sql);
                    $blocked = $db->num_rows($result);
                    if ( $blocked > 0 ) {
                        $hidedata["blocked"] = array();
                    } else {
                        $hidedata["edit"] = array();
                    }
                }
            } else {
                $hidedata["default"] = array();
            }
            // + + +
            // freigabe-test

            // bauen der zu bearbeitenden bereiche
            // * * *

            $tag_meat = content_split_all($form_values["content"]);
            $content = $form_values["content"];
            $i = 0;
            // 1. Durchlauf: die einzelnen tags werden markiert
            foreach ( $tag_sort as $pos => $value ) {
                $edit_marker = "<!--ID:".sprintf("%04d",$i)."-->";
                $pre_mark  = substr($content,0,($value["start"] + $i*strlen($edit_marker)));
                $post_mark = substr($content,($value["start"] + $i*strlen($edit_marker)));
                // welche "verschachtelungs-ebene"
                $level = count(explode("->",$tag_meat[$value["para"][0]][$value["para"][1]]["keks"]));
                if ( $tag_meat[$value["para"][0]][$value["para"][1]]["keks"] == "" ) $level = 0;
                $nested[$level][$i] = array($value["para"][0],$value["para"][1]);
                // content ergaenzen
                $content = $pre_mark.$edit_marker.$post_mark;
                $i++;
            }
            // tag
            $tmp_tag_meat = content_split_all($content);
            // 2. Durchlauf: die bearbeiten-bereiche werden gesetzt
            foreach ( $nested as $level=>$value ) {
                foreach ( $value as $id=>$tag_id ) {
                    $tag_name = $tag_id[0];
                    $tag_key  = $tag_id[1];
                    $tag_info = $tmp_tag_meat[$tag_name][$tag_key];
                    // links bauen
                    $edit = $cfg["wizard"]["basis"]."/editor,".
                            $environment["parameter"][1].",".
                            $environment["parameter"][2].",".
                            $environment["parameter"][3].",".
                            $tag_id[0].":".$tag_id[1].",".
                            $environment["parameter"][5].",".
                            $environment["parameter"][6].".html";
                    $del = $cfg["wizard"]["basis"]."/modify,".
                            $environment["parameter"][1].",".
                            $environment["parameter"][2].",".
                            $environment["parameter"][3].",".
                            $tag_id[0].":".$tag_id[1].",".
                            $environment["parameter"][5].",".
                            $environment["parameter"][6].",".
                            "delete.html";
                    $rip = $cfg["wizard"]["basis"]."/modify,".
                            $environment["parameter"][1].",".
                            $environment["parameter"][2].",".
                            $environment["parameter"][3].",".
                            $tag_id[0].":".$tag_id[1].",".
                            $environment["parameter"][5].",".
                            $environment["parameter"][6].",".
                            "rip.html";

                    // buffy: alle tags werden in ein hidedata-array geschrieben
                    ( $tag_name == "SORT" ) ? $hidevalue = substr($tag_info["meat"],8,2).".".substr($tag_info["meat"],5,2).".".substr($tag_info["meat"],0,4) : $hidevalue = $tag_info["meat"];
                    $hidedata["wizardtags"][$tag_name."_".$tag_key] = $hidevalue;
                    $hidedata["wizardtags"][$tag_name."_".$tag_key."_link"] = $edit;
                    // buffy: alle tags werden in ein hidedata-array geschrieben

//                     // test: inline-elemente als solche umzusetzen
//                     $display = "";
//                     $inline = array("LINK","IMG","Fett");
//                     if ( in_array($tag,$inline) ) {
//                         $display = "display:inline;";
//                     }

                    // knoepfe sammeln
                    $button = "";
                    if ( is_array($tag_info["buttons"]) ) {
                        foreach ( $tag_info["buttons"] as $buttons ) {
                            if ( is_array($cfg["wizard"]["ed_boxed"][$tag_name][3][$tag_info["keks"]]) ) {
                                if ( !in_array($buttons,$cfg["wizard"]["ed_boxed"][$tag_name][3][$tag_info["keks"]]) ) continue;
                            }
                            $button .= "<!--button_".$buttons."_beginn--><a href=\"".$$buttons."\">#(tag_".$buttons.")</a><!--button_".$buttons."_end-->";
                        }
                    }
                    // bauen der "bereichsumrandung"
                    if ( $blocked > 0 ) {
                        $section = "<!--edit_begin-->".
                                    $tag_info["complete"]."
                                    <!--edit_end-->";
                    } elseif ( $tag_info["type"] == "inline" ) {
                        $section = "<!--edit_begin--><span class=\"wiz_edit\" style=\"".$display."\">".
                                    trim($tag_info["complete"]).
                                    "<span class=\"buttons\"> ".
                                        $button.
                                    "</span>".
                                    "</span><!--edit_end-->";
                    } elseif ( $tag_info["type"] == "hide" ) {
                        $section = trim($tag_info["complete"]);
                    } else {
                        $section = "<!--edit_begin--><div class=\"wiz_edit\" style=\"".$display."\">".
                                    $tag_info["complete"]."
                                    <p style=\"clear:both;".$display."\" />
                                    <div class=\"buttons\">".
                                        $button.
                                    "</div>
                                    </div><!--edit_end-->";
                    }
                    // ersetzen der betroffenen bereiche
                    $edit_marker = "<!--ID:".sprintf("%04d",$id)."-->";
                    $sear  = $edit_marker.$tag_info["complete"];
                    $repl = $section;
                    $content = str_replace($sear,$repl,$content);
                }
            }
            // + + +
            // bauen der zu bearbeitenden bereiche

            // bauen der "uebergeordneten" bereiche (keine verschachtelung)
            // * * *
            $allcontent = content_level1($content);
            if ( count($allcontent) > 0 ) {
                // vorbereitung fuer die array-sortierung fuer das verschieben
                // * * *
                $i = 10;
                foreach ( $allcontent as $key=>$value ) {
                    if ($key < $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][0]
                    || (count($allcontent) - $key) <= $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][1]) {
                        continue;
                    } else {
                        $sort_array[($key*10)] = "content_blocks[]=".$key;
                        $i = $i +10;
                    }
                }
                // verschiebt die array-elemente
                function arrange_elements($sort_array, $key, $direction) {
                    global $environment, $cfg;

                    if ( $direction == "up" ) {
                        $sort_array[($key*10)-11] = $sort_array[($key*10)];
                    } elseif ( $direction == "down" ) {
                        $sort_array[($key*10)+11] = $sort_array[($key*10)];
                    }
                    unset($sort_array[($key*10)]);
                    ksort($sort_array);
                    $link = $cfg["wizard"]["basis"]."/modify,".
                            $environment["parameter"][1].",".
                            $environment["parameter"][2].",".
                            $environment["parameter"][3].",".
                            "nop,".
                            $environment["parameter"][5].",".
                            $environment["parameter"][6].",".
                            "move.html?".implode("&",$sort_array);
                    return $link;
                }
                // + + +
                // vorbereitung fuer die array-sortierung fuer das verschieben

                // bereiche in eine liste pressen
                // * * *
                $buffer = ""; $i=-1; $block=0; $pre = "";
                $dataloop["sort_content"] = array();
                foreach ( $allcontent as $key=>$value ) {
                    // kommentar-bereich nicht beruecksichtigen
                    if ( preg_match("/^\[!\].*\[\/!\]/is",$value) ) {
                        continue;
                    } elseif ( preg_match("/^\[!\]/is",$value) )  {
                        $block = -1;
                        continue;
                    } elseif ( $block == -1 ) {
                        if ( preg_match("/\[\/!\]/is",$value) ) {
                            $block = 0;
                        }
                        continue;
                    }
                    $i++;
                    $ajax_class = array();
                    // links zum verschieben bauen
                    if ( $i < $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][0]
                      || (count($allcontent) - $key) <= $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][1]
                      || $next != ""
                      || $blocked > 0 ) {
                        $ajax_class = "";
                        $modify_class = " style=\"display:none;\"";
                        $link_up = "";
                        $link_down = "";
                    } else {
                        $ajax_class = "ajax_move";
                        $modify_class = "";
                        $link_up = arrange_elements($sort_array, $key, "up");
                        $link_down = arrange_elements($sort_array, $key, "down");
                    }
                    // loeschen-link
                    $del = $cfg["wizard"]["basis"]."/modify,".
                        $environment["parameter"][1].",".
                        $environment["parameter"][2].",".
                        $environment["parameter"][3].",".
                        "section:".$key.",".
                        $environment["parameter"][5].",".
                        $environment["parameter"][6].",".
                        "delete.html";
                    // hintergrundbild-schnickschnack
                    preg_match("/\[(.+)\]/U",$value,$match);
                    $pic = strtolower(str_replace("=","-",$match[1]));
                    if ( strstr($pic,";") ) $pic = trim(substr($pic,0,strpos($pic,";")),"-");
                    $pic_array = explode("-",$pic);
                    $style = "";
                    while ( count($pic_array) > 0 ) {
                        $buffer = "wizard-icon-".implode("-",$pic_array).".png";
                        if ( file_exists($pathvars["fileroot"].$pathvars["images"].$buffer) ) {
                            $style = "background-image:url('".$pathvars["images"].$buffer."');";
                            break;
                        } elseif ( file_exists($pathvars["fileroot"]."/images/default/".$buffer) ) {
                            $style = "background-image:url('/images/default/".$buffer."');";
                            break;
                        } else {
                            array_pop($pic_array);
                        }
                    }
                    // welcher tag ist es
                    preg_match("/\[(.+)\]/U",$value,$tag_match);

                    $array = array("del","rip");
                    if ( $parent_tag == "DIV=present" ) {
                        foreach ( $array as $tag_key ) {
                            $value = preg_replace("/<!--button_".$tag_key."_beginn-->.*<!--button_".$tag_key."_end-->/U","",$value);
                        }
                    }

                    $dataloop["sort_content"][$key] = array(
                                "key"        => $key,
                                "tag"        => $tag_match[1],
                                "value"      => $value,
                                "value_html" => tagreplace($value),
                                "class"      => $ajax_class,
                                "style"      => $style,
                                "modify"     => $modify_class,
                                "link_up"    => $link_up,
                                "link_down"  => $link_down,
                                "delete"     => $del,
                    );
                }
            }
            // + + +
            // bereiche in eine liste pressen

            // bauen des dataloops fuer die image map
            $mapping["image_map"] = "leer";
            if ( file_exists($pathvars["templates"]."img_map_".$wizard_name.".tem.html")
              || file_exists($pathvars["fileroot"]."templates/default/"."img_map_".$wizard_name.".tem.html") ) {
                $mapping["image_map"] = "img_map_".$wizard_name;
                // anzahl der uebergeordneten bereiche
                $num_cont_blocks = count($dataloop["sort_content"]);
                $index=0;
                foreach ($dataloop["sort_content"] as $i=>$value) {
                    $index++;
                    // moegliche vorschau-bild-namen durchgehen
                    $src_tag = strtolower($dataloop["sort_content"][$i]["tag"]);
                    $src_tag_tmp = "";
                    if ( strstr($src_tag,"=") ) {
                        $src_tag_tmp    = str_replace("=","-",$src_tag);
                        if ( strstr($src_tag_tmp,";") ) {
                            $src_tag_tmp    = substr($src_tag_tmp,0,strpos($src_tag_tmp,";") );
                        }
                    }
                    if ( strstr($src_tag,"=") ) $src_tag = substr($src_tag,0,strpos($src_tag,"=") );
                    $src = "";
                    if ( file_exists($pathvars["fileroot"]."images/".$environment["design"]."/img_map_part_".$src_tag_tmp.".png") ) {
                        $src = "/images/".$environment["design"]."/img_map_part_".$src_tag_tmp.".png";
                    } elseif ( file_exists($pathvars["fileroot"]."images/default/img_map_part_".$src_tag_tmp.".png") ) {
                        $src = "/images/default/img_map_part_".$src_tag_tmp.".png";
                    } elseif ( file_exists($pathvars["fileroot"]."images/".$environment["design"]."/img_map_part_".$src_tag.".png") ) {
                        $src = "/images/".$environment["design"]."/img_map_part_".$src_tag.".png";
                    } elseif ( file_exists($pathvars["fileroot"]."images/default/img_map_part_".$src_tag.".png") ) {
                        $src = "/images/default/img_map_part_".$src_tag.".png";
                    }
                    // link
                    $link = $cfg["wizard"]["basis"]."/".
                            $environment["parameter"][0].",".
                            $environment["parameter"][1].",".
                            $environment["parameter"][2].",".
                            $environment["parameter"][3].",".
                            /*$i.*/",".
                            $environment["parameter"][5].",".$i.".html";
                    $ausgaben["item_".$i."_link"] = $link;
                    // dataloop bauen
                    if ( $index >  $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][0]
                      && $index <= $num_cont_blocks - $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][1]  ) {
                        $dataloop["img_map"][] = array(
                            "key"  => $dataloop["sort_content"][$i]["key"],
                            "src"  => $src,
                            "link" => $link,
                        );
                    }
                    // falls nur eine section angezeigt werden soll die anderen aus dem dataloop loeschen
                    if ( ($environment["parameter"][6] != "" && $i != $environment["parameter"][6])
                    || $environment["parameter"][6] == "none" ) {
                        unset($dataloop["sort_content"][$i]);
                    }
                }
                if ( $environment["parameter"][6] == "none" ) {
                    $hidedata["no_sort_content"] = array();
                }
            }
            if ( count($dataloop["sort_content"]) > 0 ) $hidedata["sort_content"] = array();

            // link-ziel fuer die ajax-verschieb-sache
            // * * *
            $ausgaben["ajax_request"] = $cfg["wizard"]["basis"]."/modify,".
                                        $environment["parameter"][1].",".
                                        $environment["parameter"][2].",".
                                        $environment["parameter"][3].",".
                                        "nop,".
                                        $environment["parameter"][5].",".
                                        $environment["parameter"][6].",".
                                        "move.html";
            // + + +

            // add-buttons
            // * * *
            foreach ( $cfg["wizard"]["add_tags"] as $key=>$value ) {
                $debug_add_buttons .= "# (".$key.") #(".$key.")<br />";
                if ( is_array($cfg["wizard"]["wizardtyp"][$wizard_name]["add_tags"])
                  && !in_array($key,$cfg["wizard"]["wizardtyp"][$wizard_name]["add_tags"]) ) {
                    continue;
                }
                $dataloop["add_buttons"][] = array(
                    "link" => $cfg["wizard"]["basis"]."/modify,".
                            $environment["parameter"][1].",".
                            $environment["parameter"][2].",".
                            $environment["parameter"][3].",".
                            $key.":".strlen($form_values["content"]).",".
                            $environment["parameter"][5].",".
                            $environment["parameter"][6].",".
                            "add,".
                            (count($dataloop["sort_content"]) - $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][1]).
                            ".html",
                    "item" => "#(".$key.")",
                );
            }
            // + + +

            // navigation erstellen
            $ausgaben["form_aktion"] = $cfg["wizard"]["basis"]."/show,".
                                                                $environment["parameter"][1].",".
                                                                $environment["parameter"][2].",".
                                                                $environment["parameter"][3].",".
                                                                $environment["parameter"][4].",".
                                                                $environment["parameter"][5].",verify.html";

            // was anzeigen
            $mapping["main"] = "wizard-show";
            #$mapping["navi"] = "leer";

            // unzugaengliche #(marken) sichtbar machen
            // ***
            if ( isset($_GET["edit"]) ) {
                $ausgaben["inaccessible"] = "inaccessible values:<br />";
                $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
                $ausgaben["inaccessible"] .= "# (tag_edit) #(tag_edit)<br />";
                $ausgaben["inaccessible"] .= "# (tag_del) #(tag_del)<br />";
                $ausgaben["inaccessible"] .= "# (tag_rip) #(tag_rip)<br />";

                $ausgaben["inaccessible"] .= "# (wiz_moveup) #(wiz_moveup)<br />";
                $ausgaben["inaccessible"] .= "# (wiz_moveup_desc) #(wiz_moveup_desc)<br />";
                $ausgaben["inaccessible"] .= "# (wiz_movedown) #(wiz_movedown)<br />";
                $ausgaben["inaccessible"] .= "# (wiz_movedown_desc) #(wiz_movedown_desc)<br />";
                $ausgaben["inaccessible"] .= "# (wiz_delete) #(wiz_delete)<br />";
                $ausgaben["inaccessible"] .= "# (wiz_delete_desc) #(wiz_delete_desc)<br />";

                $ausgaben["inaccessible"] .= "# (ask_release) #(ask_release)<br />";
                $ausgaben["inaccessible"] .= "# (release) #(release)<br />";
                $ausgaben["inaccessible"] .= "# (blocked_release) #(blocked_release)<br />";
                $ausgaben["inaccessible"] .= $debug_add_buttons;
            } else {
                $ausgaben["inaccessible"] = "";
            }

            $publisher = 0;
            if ( priv_check($tname2path,"publish") || $artikel_check_publish ) $publisher = -1;

            if ( ( $environment["parameter"][6] == "verify"
                   && ( $_POST["save"] != ""
                     || $_POST["version"] != ""
                     || $_POST["cancel"] != ""
                      )
                 )
              || $_SESSION["form_send"] != "" ) {

                // ebene und kategorie aus tname ableiten
//                 $tname2path = tname2path($environment["parameter"][2]);
                if ( $ausgaben["url"] != "" ) {
                    $url = explode("/",$tname2path);
                    $kategorie = array_pop($url);
                    $ebene = implode("/",$url);
                } else {
                    $ebene = str_replace(array($pathvars["virtual"],$pathvars["webroot"]),"",dirname($_SESSION["form_referer"]));
                    $kategorie = str_replace(".html","",basename($_SESSION["form_referer"]));
                }
                if ( strstr($kategorie,",") ) $kategorie = substr($kategorie,0,strpos($kategorie,","));

                // die naechste freie versionsnummer finden
                $sql = "SELECT max(version) as max_version
                        FROM ". SITETEXT ."
                        WHERE lang = '".$environment["language"]."'
                        AND label ='".$environment["parameter"][3]."'
                        AND tname ='".$environment["parameter"][2]."'";
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,1);
                $next_version = $data["max_version"] + 1;

                if ( $content_exists == 0
                  || $_POST["version"] != ""
                  || $_SESSION["form_send"] == "version" ) {
                    // freigabe-test
                    $status1 = "";
                    $status2 = "";
                    if ( $specialvars["content_release"] == -1 ) {
                        $status1 = ",status";
                        if ( $_POST["release_mark"] == -1 ) {
                            $status2 = ",-2";
                        } else {
                            $status2 = ",-1";
                        }
                    }

                    // alle dazugehoerigen blogs updaten
                    if ( is_array($tag_meat["BLOG"]) ) {
                        $blog_sql = "SELECT tname FROM ".SITETEXT." WHERE content ~ '\\\[KATEGORIE\]".tname2path($environment["parameter"][2])."\\\[\/KATEGORIE\]' group by tname";
                        $blog_result = $db -> query($blog_sql);
                        while ( $blog_data = $db -> fetch_array($blog_result,1) ) {
                            if ( $_SESSION["wizard_content"][DATABASE.",".$blog_data["tname"].",".$environment["parameter"][3]] ) {
                                // die naechste freie versionsnummer finden
                                $sql = "SELECT max(version) as max_version, ebene, kategorie
                                        FROM ". SITETEXT ."
                                        WHERE lang = '".$environment["language"]."'
                                        AND label ='".$environment["parameter"][3]."'
                                        AND tname ='".$blog_data["tname"]."' group by ebene,kategorie";
                                $result = $db -> query($sql);
                                $data = $db -> fetch_array($result,1);
                                $next_blog_version = $data["max_version"] + 1;
                                $sql = "INSERT INTO ". SITETEXT ."
                                                    (lang, label, tname, version,
                                                    ebene, kategorie,
                                                    crc32, html, content,
                                                    changed, bysurname, byforename, byemail, byalias".$status1.")
                                            VALUES (
                                                    '".$environment["language"]."',
                                                    '".$environment["parameter"][3]."',
                                                    '".$blog_data["tname"]."',
                                                    '".$next_blog_version."',
                                                    '".$data["ebene"]."',
                                                    '".$data["kategorie"]."',
                                                    '".$specialvars["crc32"]."',
                                                    '0',
                                                    '".addslashes($_SESSION["wizard_content"][DATABASE.",".$blog_data["tname"].",".$environment["parameter"][3]])."',
                                                    '".date("Y-m-d H:i:s")."',
                                                    '".$_SESSION["surname"]."',
                                                    '".$_SESSION["forename"]."',
                                                    '".$_SESSION["email"]."',
                                                    '".$_SESSION["alias"]."'
                                                    ,-1)";
                                $result = $db -> query($sql);
                            }
                        }
                    }

                    $sql = "INSERT INTO ". SITETEXT ."
                                        (lang, label, tname, version,
                                        ebene, kategorie,
                                        crc32, html, content,
                                        changed, bysurname, byforename, byemail, byalias".$status1.")
                                VALUES (
                                        '".$environment["language"]."',
                                        '".$environment["parameter"][3]."',
                                        '".$environment["parameter"][2]."',
                                        '".$next_version."',
                                        '".$ebene."',
                                        '".$kategorie."',
                                        '".$specialvars["crc32"]."',
                                        '0',
                                        '".addslashes($form_values["content"])."',
                                        '".date("Y-m-d H:i:s")."',
                                        '".$_SESSION["surname"]."',
                                        '".$_SESSION["forename"]."',
                                        '".$_SESSION["email"]."',
                                        '".$_SESSION["alias"]."'
                                        ".$status2.")";
                    $release_version = $next_version;

                } elseif ($_POST["save"] != ""
                       || $_SESSION["form_send"] == "save") {
                    // preview mit ajax
                    if ( $_POST["ajax"] == "on" ) {
                        // parameter-manipulation fuer blog-tag
                        $environment["ebene"] = dirname($tname2path);
                        $environment["kategorie"] = basename($tname2path);
                        $cfg["auth"]["ghost"]["contented"] = "none";

                        if ( file_exists($pathvars["moduleroot"]."customer/".$wizard_name.".inc.php" ) ) {
                            include $pathvars["moduleroot"]."customer/".$wizard_name.".inc.php";
                        }
                        $content = tagreplace($form_values["content"]);
                        $content = tagremove($content);
                        if ( get_magic_quotes_gpc() == 1 ) {
                            $content = stripslashes($content);
                        }
                        if ( $cfg["wizard"]["utf8"] != TRUE ) {
                            $content = utf8_encode($content);
                        }
                        header("HTTP/1.0 200 OK");
                        $content = str_replace($cfg["wizard"]["basis"],$environment["ebene"],$content);
                        echo preg_replace(
                            array("/#\{.+\}/U","/g\(.+\)/U"),
                            array("",""),
                            $content
                        );
                        die ;
                    }

                    // freigabe-test
                    $status = "";
                    if ( $specialvars["content_release"] == -1 ) {
                        if ( $_POST["release_mark"] == -1 ) {
                            $status = ",status=-2";
                        } else {
                            $status = ",status=-1";
                        }
                    }

                    $sql = "UPDATE ". SITETEXT ."
                               SET ebene = '".$ebene."',
                                   kategorie = '".$kategorie."',
                                   crc32 = '".$specialvars["crc32"]."',
                                   html = '0',
                                   content = '".addslashes($form_values["content"])."',
                                   changed = '".date("Y-m-d H:i:s")."',
                                   bysurname = '".$_SESSION["surname"]."',
                                   byforename = '".$_SESSION["forename"]."',
                                   byemail = '".$_SESSION["email"]."',
                                   byalias = '".$_SESSION["alias"]."'
                                   ".$status."
                             WHERE lang = '".$environment["language"]."'
                               AND label ='".$environment["parameter"][3]."'
                               AND tname ='".$environment["parameter"][2]."'
                               AND version ='".$form_values["version"]."'";
                    $release_version = $form_values["version"];

                } elseif ($_POST["cancel"] != "") {
                    unset($_SESSION["wizard_content"]);
                }

                if ( $result  = $db -> query($sql) ) {
                    if ( $cfg["wizard"]["wizardtyp"][$wizard_name]["blog_date"] == true ) {
                        $sql_blog = "SELECT *
                                FROM ". SITETEXT ."
                                WHERE lang = '".$environment["language"]."'
                                AND label ='".$environment["parameter"][3]."'
                                AND tname ='".$environment["parameter"][2]."'
                                    AND status = 1";
                        $result_blog  = $db -> query($sql_blog);
                        if ( $db -> num_rows($result_blog) == 0 ) {
                            $sql_blog = "UPDATE ". SITETEXT ." SET content=regexp_replace(content,'\\\[SORT\\\].*\\\[\\\/SORT\\\]','[SORT]".date("Y-m-d H:i:s")."[/SORT]') WHERE tname like '".$environment["parameter"][2]."'";
                            $result_blog  = $db -> query($sql_blog);
                        }
                    }
                    unset($_SESSION["wizard_content"]);
                }

                // sprungziele definieren
                $header = $_SESSION["form_referer"];
                unset($_SESSION["form_send"]);
                // content ggf. sofort freigeben
                if ( $specialvars["content_release"] == -1
                  && $publisher == -1
                  && $_POST["release_mark"] == -1 ) {
                    $header = $cfg["wizard"]["basis"]."/release,".
                                $environment["parameter"][1].",".
                                $environment["parameter"][2].",".
                                $environment["parameter"][3].",release,".
                                $release_version.".html";
                    header("Location: ".$header);
                } else {
                    unset($_SESSION["form_referer"]);
                    header("Location: ".$header);
                }

            }

        }

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    //
    $db -> selectDb(DATABASE,FALSE);



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>