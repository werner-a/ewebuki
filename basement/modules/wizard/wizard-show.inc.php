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

    // erlaubnis bei intrabvv speziell setzen
    $database = $environment["parameter"][1];
    if ( is_array($_SESSION["katzugriff"]) ) {
        if ( in_array("-1:".$database.":".$environment["parameter"][2],$_SESSION["katzugriff"]) ) $erlaubnis = -1;
    }
    if ( is_array($_SESSION["dbzugriff"]) ) {
        if ( in_array($database,$_SESSION["dbzugriff"]) ) $erlaubnis = -1;
    }

    // form_referer
    if ( !preg_match("/wizard$/",dirname($_SERVER["HTTP_REFERER"])) ) {
        $_SESSION["form_referer"] = $_SERVER["HTTP_REFERER"];
    }
    $ausgaben["form_referer"] = $_SESSION["form_referer"];

    // welche seite wird bearbeitet
    $ausgaben["url"] = $pathvars["webroot"].tname2path($environment["parameter"][2]).".html";

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
    }
    // + + +
    // leere parameter abfangen


    if ( priv_check("/".$cfg["wizard"]["subdir"]."/".$cfg["wizard"]["name"],$cfg["wizard"]["right"]) ||
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

        if ( isset($_GET["preview"]) ) {

            $ausgaben["output"] = tagreplace($form_values["content"]);

        } else {

            // versionen-links
            // * * *
            $ausgaben["vaktuell"] = $form_values["version"];
            $sql = "SELECT version, html, content, changed, byalias
                      FROM ". SITETEXT ."
                     WHERE lang = '".$environment["language"]."'
                       AND label ='".$environment["parameter"][3]."'
                       AND tname ='".$environment["parameter"][2]."'
                  ORDER BY version";
            $result_version = $db -> query($sql);
            $ausgaben["vgesamt"] = $db -> num_rows($result_version);
            $aktuell = 0; $back = ""; $next = "";
            while ( $data = $db -> fetch_array($result_version) ) {
                if ( $data["version"] == $form_values["version"] ) {
                    $aktuell = -1;
                    continue;
                }
                if ( $aktuell == 0 ) $back = $data["version"];
                if ( $aktuell == -1 ) {
                    $next = $data["version"];
                    break;
                }
            }
            $link = $environment["parameter"][0].",".
                    $environment["parameter"][1].",".
                    $environment["parameter"][2].",".
                    $environment["parameter"][3].",".
                    $environment["parameter"][4].",";
            if ( $back != "" ) {
                $hidedata["version_prev"]["link_prev"] = $link.$back.".html";
                $hidedata["version_prev"]["link_first"] = $link."1.html";
            }
            if ( $next != "" ) {
                $hidedata["version_next"]["link_next"] = $link.$next.".html";
                $hidedata["version_next"]["link_last"] = $link.$ausgaben["vgesamt"].".html";
            }
            // + + +
            // versionen-link

            // wizard-infos rausfinden (z.b. wizard-typ,..)
            // * * *
            preg_match("/\[!\]wizard:(.+)\[\/!\]/i",$form_values["content"],$match);
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
                if ( priv_check(tname2path($environment["parameter"][2]),"publish") ) {
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
            $tag_order = $tag_meat["order"];
            unset($tag_meat["order"]);
            $tmp_tag_meat = $tag_meat;

            $content = $form_values["content"];
            foreach ( $tag_meat as $tag=>$sections ) {
                foreach ( $sections as $key=>$value ) {
                    // links bauen
                    $edit = $cfg["wizard"]["basis"]."/editor,".
                            $environment["parameter"][1].",".
                            $environment["parameter"][2].",".
                            $environment["parameter"][3].",".
                            $tag.":".$key.",".
                            $environment["parameter"][5].",".
                            $environment["parameter"][6].".html";
                    $del = $cfg["wizard"]["basis"]."/modify,".
                            $environment["parameter"][1].",".
                            $environment["parameter"][2].",".
                            $environment["parameter"][3].",".
                            $tag.":".$key.",".
                            $environment["parameter"][5].",".
                            "delete.html";
                    $rip = $cfg["wizard"]["basis"]."/modify,".
                            $environment["parameter"][1].",".
                            $environment["parameter"][2].",".
                            $environment["parameter"][3].",".
                            $tag.":".$key.",".
                            $environment["parameter"][5].",".
                            "rip.html";
                    // bereiche vor oder nach den tag
                    $pre_section  = substr($content,0,$tmp_tag_meat[$tag][$key]["start"]);
                    $pre_section  = preg_replace("/[ ]$/","&nbsp;",$pre_section);
                    $post_section = substr($content,$tmp_tag_meat[$tag][$key]["end"]);
                    $post_section  = preg_replace("/^[ ]/","&nbsp;",$post_section);
//                     // test: inline-elemente als solche umzusetzen
//                     $display = "";
//                     $inline = array("LINK","IMG","Fett");
//                     if ( in_array($tag,$inline) ) {
//                         $display = "display:inline;";
//                     }
                    // knoepfe
                    $button = "";
                    if ( is_array($tmp_tag_meat[$tag][$key]["buttons"]) ) {
                        foreach ( $tmp_tag_meat[$tag][$key]["buttons"] as $buttons ) {
                            $button .= "<a href=\"".$$buttons."\">#(tag_".$buttons.")</a>";
                        }
                    }
                    // bauen der "bereichsumrandung"
                    if ( $blocked > 0 ) {
                        $section = "<!--edit_begin-->".
                                    $tmp_tag_meat[$tag][$key]["complete"]."
                                    <!--edit_end-->";
                    } elseif ( $value["type"] == "inline" ) {
                        $section = "<!--edit_begin--><span class=\"wiz_edit\" style=\"".$display."\">".
                                    trim($tmp_tag_meat[$tag][$key]["complete"]).
                                    "<span class=\"buttons\"> ".
                                        $button.
                                    "</span>".
                                    "</span><!--edit_end-->";
                    } elseif ( $value["type"] == "hide" ) {
                        $section = trim($tmp_tag_meat[$tag][$key]["complete"]);
                    } else {
                        $section = "<!--edit_begin--><div class=\"wiz_edit\" style=\"".$display."\">".
                                    $tmp_tag_meat[$tag][$key]["complete"]."
                                    <p style=\"clear:both;".$display."\" />
                                    <div class=\"buttons\">".
                                        $button.
                                    "</div>
                                    </div><!--edit_end-->";
                    }
                    // tag_meat-array neu durchzaehlen
                    $content = $pre_section.$section.$post_section;
                    $tmp_tag_meat = content_split_all($content);
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
                            "move.html?".implode("&",$sort_array);
                    return $link;
                }
                // + + +
                // vorbereitung fuer die array-sortierung fuer das verschieben

                // bereiche in eine liste pressen
                // * * *
                $buffer = ""; $i=-1; $block=0; $pre = "";
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

                    $dataloop["sort_content"][] = array(
                                "key"       => $key,
                                "value"     => tagreplace($value),
                                "class"     => $ajax_class,
                                "style"     => $style,
                                "modify"    => $modify_class,
                                "link_up"   => $link_up,
                                "link_down" => $link_down,
                                "delete"    => $del,
                    );
                }
            }
            // + + +
            // bereiche in eine liste pressen

            // link-ziel fuer die ajax-verschieb-sache
            // * * *
            $ausgaben["ajax_request"] = $cfg["wizard"]["basis"]."/modify,".
                                        $environment["parameter"][1].",".
                                        $environment["parameter"][2].",".
                                        $environment["parameter"][3].",".
                                        "nop,".
                                        $environment["parameter"][5].",".
                                        "move.html";
            // + + +

            // add-buttons
            // * * *
            foreach ( $cfg["wizard"]["add_tags"] as $key=>$value ) {
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
                            "add,".
                            (count($dataloop["sort_content"]) - $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][1]).
                            ".html",
                    "item" => $key
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
            } else {
                $ausgaben["inaccessible"] = "";
            }

            $publisher = 0;
            if ( priv_check(tname2path($environment["parameter"][2]),"publish") ) $publisher = -1;

            if ( ($environment["parameter"][6] == "verify"
                && $_POST["send"] != "") || $_SESSION["form_send"] != "" ) {

                // ebene und kategorie aus tname ableiten
                $tname2path = tname2path($environment["parameter"][2]);
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

                if ( $content_exists == 0 || $_POST["send"][0] == "version" || $_SESSION["form_send"] == "version" ) {
                    // notwendig fuer die artikelverwaltung , der bisher aktive artikel wird auf inaktiv gesetzt
                    if ( preg_match("/^\[!\]/",$content,$regs) ) {
                        $sql_regex = "SELECT * FROM ". SITETEXT ." WHERE content REGEXP '^\\\[!\\\]1' AND tname like '".$environment["parameter"][2]."'";
                        $result_regex  = $db -> query($sql_regex);
                        $data_regex = $db -> fetch_array($result_regex,1);
                        $new_content = preg_replace("/\[!\]1/","[!]0",$data_regex["content"]);
                        $sql_regex = "UPDATE ". SITETEXT ." SET content ='".$new_content."' WHERE content REGEXP '^\\\[!\\\]1' AND tname like '".$environment["parameter"][2]."'";
                        $result_regex  = $db -> query($sql_regex);
                    }
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

                } elseif ($_POST["send"][0] == "save" || $_SESSION["form_send"] == "save") {
                    // preview mit ajax
                    if ( $_POST["ajax"] == "on" ) {
                        $content = tagreplace($form_values["content"]);
                        $content = tagremove($content);
                        if ( get_magic_quotes_gpc() == 1 ) {
                            $content = stripslashes($content);
                        }
                        $content = utf8_encode($content);
                        echo preg_replace(array("/#\{.+\}/U","/g\(.+\)/U"),"",$content);
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

                } elseif ($_POST["send"][0] == "cancel") {
                    unset($_SESSION["wizard_content"]);
                }

                if ( $result  = $db -> query($sql) ) {
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