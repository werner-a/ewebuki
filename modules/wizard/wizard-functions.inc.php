<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// wizard-functions.inc.php v1 emnili/krompi
// wizard: function loader
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
    if ( is_array(@$cfg["wizard"]["function"]) && in_array("makece", $cfg["wizard"]["function"][$environment["kategorie"]]) ) {

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
            $ausgaben["njs"] = "";
            foreach( $cfg["wizard"]["tags"] as $key => $value ) {

                // feststellen, ob der tag erlaubt ist
                if ( is_array($allowed_tags) && !in_array($key,$allowed_tags) ) {
                    continue;
                }

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

                if ( $value[0] == "" && $cfg["wizard"]["debug"] == True ) $value[0] = "T";

                // position (T=top, B=bottom), access key, no select, links, rechts, disable
                //                                                     ebButtons[ebButtons.length] = new ebButton(
                // id           used to name the toolbar button           'eb_h1'
                // key          label on button                          ,'H1'
                // tit          button title                             ,'�berschrift [Alt-1]'
                // position     position (top, bot)                      ,'T'
                // access       access key                               ,'1'
                // noSelect                                              ,'-1'
                // tagStart     open tag                                 ,'[H1]'
                // tagMid       mid tag                                  ,''
                // tagEnd       close tag                                ,'[/H1]'
                //                                                     );

                $ausgaben["njs"] .= "ebButtons[ebButtons.length] = new ebButton(\n";
                $button_label = strtoupper($key);
                if ( $cfg["wizard"]["rename_buttons"][$key])  {
                    $button_label = $cfg["wizard"]["rename_buttons"][$key];
                }
                $ausgaben["njs"] .= "'eb_".$key."'
                                    ,'".$button_label."'
                                    ,'".$label[$key].$k."'
                                    ,'".$value[0]."'
                                    ,'".$value[1]."'
                                    ,'noSelect'
                                    ,'[".strtoupper($keyX).$l."'
                                    ,'".$value[4]."'
                                    ,'".$value[5]."[/".strtoupper($keyX)."]'\n";
                $ausgaben["njs"] .= ");\n";

            }

            // script in seite parsen
            $ausgaben["ce_script"] = parser($cfg["wizard"]["tagjs"],"");

            return $tn;
        }

        // erzeugt ein array mit den informationen aller gesuchten tags (inhalt der tags, position,...)
        // aufbau: $tag_meat[tagname][index] ( z.B. $tag_meat["H"][1] )
        function content_split_all($content) {
            global $cfg, $value, $tag_sort;

            $tmp_content = $content;
            $tag_meat = array();$tag_sort = array();
            foreach ( $cfg["wizard"]["ed_boxed"] as $tag=>$preg ) {

                $pre = "";
                $index = 0;
                $preg1 = "/(\[\/)(".$tag."[0-9]{0,1})(\])/";

                $buffer = array();
                while ( preg_match( $preg1 , $tmp_content, $match ) ) {

                    // endtag
                    $closetag = $match[0];
                    $closetag_len = strlen($match[0]);

                    // wie faengt der opentag an?
                    $opentag = "[".$match[2];

                    // wo beginnt der endtag?
                    $closetagbeg = strpos($tmp_content,$closetag);

                    // heuhaufen
                    $haystack = substr($tmp_content,0,$closetagbeg);
                    $org_haystack = substr($content,0,$closetagbeg);

                    // zugehoerigen opentag finden
                    $opentagbeg = strrpos($haystack,$opentag);


                    // temporaere heuhaufen
                    $tmp_haystack = substr($haystack,$opentagbeg);
                    $tmp_org_haystack = substr($org_haystack,$opentagbeg);

                    $opentagbeg_endklammer = strpos($tmp_haystack,"]");

                    // reele opentag
                    $real_opentag = substr($tmp_haystack,0,$opentagbeg_endklammer+1);

                    // laenge des opentags
                    $opentaglen = strlen($real_opentag);

                    // mit unnuetzen zeichen auffuellen
                    $var = '';
                    $right = str_pad ( $var, $opentaglen, 'e' );

                    // wie lautet der tagwert
                    $tagwertbeg = strlen($haystack) - (strpos(strrev($haystack), strrev($real_opentag)) + strlen($real_opentag)) + $opentaglen + 1;

                    $tagoriginal = substr($tmp_content,$opentagbeg,$closetagbeg+strlen($closetag)-$opentagbeg);
                    $tagoriginal_org = substr($content,$opentagbeg,$closetagbeg+strlen($closetag)-$opentagbeg);

                     // open und endtags zerstören
                     $tmp_content = preg_replace(
                                "/".preg_quote($haystack.$closetag,"/")."/" ,
                                $haystack."==".$match[2]."#" ,
                                $tmp_content,
                                1
                    );
                     $tmp_content = preg_replace(
                                "/^".preg_quote(substr($haystack,0,$opentagbeg).$real_opentag,"/")."/" ,
                                substr($haystack,0,$opentagbeg).$right ,
                                $tmp_content,
                                1
                    );

                    $buffer[$index] = array(
                               "start" => strrpos($haystack,$real_opentag),
                                 "end" => strlen($haystack)+strlen($closetag),
                            "complete" => $tagoriginal_org,
                           "tag_start" => $real_opentag,
                                "meat" => substr($tmp_org_haystack,$opentaglen),
                             "tag_end" => $closetag,
                                "keks" => "",
                                "type" => $preg[1],
                             "buttons" => $preg[2],
                        );
                    $tag_sort[strrpos($haystack,$real_opentag)] = array(
                        "para" => array($tag,$index),
                       "start" => strrpos($haystack,$real_opentag),
                         "end" => strlen($haystack)+strlen($closetag),
                    );
                    $tagwert = $tagoriginal;
                    $index++;
                    $tag_meat[$tag] = $buffer;
                }
            }

            // verschachtelte tags werden gesucht und eingetragen
            ksort($tag_sort);
            if ( !function_exists(filter_alternate) ) {
                function filter_alternate($var) {
                    global $value, $start;
                    if ( $value["start"] < $var["start"] && $value["end"] > $var["end"] ) return $var;
                }
            }
            foreach ( $tag_sort as $key=>$value ) {
                $start = $value["start"];
                $nested = (array_filter($tag_sort, "filter_alternate"));
                foreach ( $nested as $index => $nest_value ) {
                    $keks = trim($tag_meat[$value["para"][0]][$value["para"][1]]["tag_start"],"[]");
                    $tag_meat[$nest_value["para"][0]][$nest_value["para"][1]]["keks"][] = $keks;
                }
            }
            return $tag_meat;
        }

        // aufteilung in bloecke der "ersten ebene", ohne verschachtlung
        function content_level1($content) {
            global $cfg;

            // suchmuster bauen und open- und close-tags finden
            $preg = array();
            $split_tags["open"][] = "<!--edit_begin-->";
            $split_tags["close"][] = "<!--edit_end-->";
            foreach ( $cfg["wizard"]["ed_boxed"] as $key=>$value ) {
                $start_tag = $value[0][0];
                if ( $value[0][1] == "" ) {
                    $end_tag = str_replace("[","[/",$value[0][0]);
                } else {
                    $end_tag = $value[0][1];
                }
                $start_tag = str_replace("]","",$start_tag);
                $end_tag = str_replace("]","",$end_tag);
                $split_tags["open"][]  = $start_tag;
                $split_tags["close"][] = $end_tag;
                $preg[] = str_replace(array("[","/"),array("\[","\/"),$start_tag);
                $preg[] = str_replace(array("[","/"),array("\[","\/"),$end_tag);
            }
            $separate = preg_split("/(".implode("|",$preg).")|(<!--edit_begin-->)|(<!--edit_end-->)/",$content,-1,PREG_SPLIT_DELIM_CAPTURE);

            $i = 0; $close = 0;
            $allcontent = array();
            foreach ( $separate as $index => $line ) {
                if ( trim($line," \n\r") == "" ) continue;
                if ( in_array($line,$split_tags["open"]) ) {
                    if ($close == 0) $i++;
                    $close++;
                } elseif ( in_array($line,$split_tags["close"]) ) {
                    $close--; $mark = -1;
                }
                $allcontent[$i] .= $line;
            }

            return array_merge($allcontent);
        }

    }

        // welche seiten sind in bearbeitung und welche warten auf freigabe
        // parameter:
        //
        //      $url..........: ueberpruefter pfad
        //      $cfg..........: cfg-datei
        //      $label........: durchsuchtes site_text-label
        //      $status.......: welche zustaende sollen durchsucht werden
        //      $add_filter...: zusaetzliche filter, z.B.
        //                        username, max_age (tage)
        //      $check_privs..: sollen rechte ueberprueft werden
        //      $ignore.......: pfade, die ausgespart werden sollen
        //
        function find_marked_content( $url = "/", $cfg, $label, $status = array(-2,-1), $add_filter = array(), $check_privs = TRUE, $ignore = array() ) {
            global $db, $pathvars, $environment;

            $path = explode("/",$url);
            $kategorie = array_pop($path);
            $ebene = implode("/",$path);
            $new_releases = null;

            if ( !isset($environment["parameter"][1]) ) $environment["parameter"][1] = null;

            if ( $kategorie ) {
                $tmp_tname = eCRC($url);
            } else {
                $tmp_tname = "";
            }
            // gibt es bereiche, die nicht untersucht werden sollen
            $where = null; $filter = null;
            if ( count($ignore) > 0 ) {
                foreach ( $ignore as $value ) {
                    $where[] = "ebene NOT LIKE '".$value."%'";
                    $where[] = "kategorie NOT LIKE '".$value."%'";
                }
                $where = " AND (".implode(" AND ",$where).")";
            }
            // zusaetzliche filter
            $buffer = array();
            foreach ( $add_filter as $key=>$value ) {
                switch ( $key ) {
                    case "user":
                        $buffer[] = "byalias='".$value."'";
                        break;
                    case "max_age":
                        $buffer[] = "changed>='".date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), (date("d")-$value), date("Y")))."'";
                        break;
                }
            }
            if ( count($buffer) > 0 ) $filter = "AND ".implode(" AND ",$buffer);
            $sql = "SELECT *
                      FROM site_text
                     WHERE
                            tname LIKE '".$tmp_tname."%'
                            ".$where."
                       AND label='".$label."'
                       AND status IN (".implode(",",$status).")
                       ".$filter."
                  ORDER BY tname, status ASC, version DESC";

            $result = $db -> query($sql);

            $dataset = "";
            while ( $data = $db -> fetch_array($result) ) {
                // weiterspringen, falls es von diesen content bereits eine freizugebene version gibt
                if ( $dataset == $data["tname"]."::".$label ) continue;
                $dataset = $data["tname"]."::".$label;

                if ( $data["ebene"] == "" ) {
                    $tname = $data["kategorie"];
                } else {
                    $tname = eCRC($data["ebene"]).".".$data["kategorie"];
                }
                $path = $data["ebene"]."/".$data["kategorie"];
                // rechte checken
                if ( $check_privs == TRUE || $_SESSION["uid"] == "" ) {
                    if ( $data["status"] == -2 && !priv_check($path,"publish") ) {
                        continue;
                    } elseif ( $data["status"] == -1 && !priv_check($path,"edit;publish") ) {
                        continue;
                    }
                }

                // titel
                $titel = "---";
                preg_match("/\[H[0-9]{1}\](.+)\[\/H/Us",$data["content"],$match);
                if ( count($match) > 1 ) {
                    $titel = $match[1];
                }

                // link anpassen
                if ( $data["status"] > 0 ) {
                    $view_link = $pathvars["menuroot"].$data["ebene"]."/".$data["kategorie"].".html";
                } else {
                    $view_link = $pathvars["menuroot"].$data["ebene"]."/".$data["kategorie"].",v".$data["version"].".html";
                }

                // ggf kategorie
                $kategorie = "---";
                $ext = "---";
                if  ( !empty($cfg["bloged"]["blogs"][$url]["category"]) ) {
                    preg_match("/\[".$cfg["bloged"]["blogs"][$url]["addons"]["name"]["tag"]."\](.+)\[\/".$cfg["bloged"]["blogs"][$url]["addons"]["name"][0]."/Us",$data["content"],$termine_match);
                    if ( count($termine_match) > 1 ) {
                        $ext = $termine_match[1];
//                         if ( $data["status"] > 0 ) {
                            $view_link = $pathvars["menuroot"].$data["ebene"].",,".$data["kategorie"].".html";
//                         } else {
//                             $view_link = $pathvars["menuroot"].$data["ebene"].",,".$data["kategorie"].",v".$data["version"].".html";
//                         }
                    }
                    preg_match("/\[".$cfg["bloged"]["blogs"][$url]["category"]."\](.+)\[\/".$cfg["bloged"]["blogs"][$url]["category"]."/U",$data["content"],$match);
                    if ( count($match) > 1 ) {
                        $kategorie = $match[1];

                        $path = $kategorie;
                    }
                }

                // letzte aktuelle version finden
                $sql = "SELECT *
                          FROM site_text
                         WHERE tname='".$data["tname"]."'
                           AND label='".$data["label"]."'
                           AND lang='".$data["lang"]."'
                           AND status=1
                  ORDER BY version DESC";
                $res_akt = $db -> query($sql);
                if ( $db->num_rows($res_akt) == 0 ) {
                    $last_author = "";
                    $last_uid = "";
                } else {
                    $dat_akt = $db -> fetch_array($res_akt);
                    $last_author = $dat_akt["byforename"]." ".$dat_akt["bysurname"];
                    $last_uid = $dat_akt["byalias"];
                }

                // tabellen farben wechseln
                if ( @$cfg[$data["status"]]["color"]["set"] == $cfg["wizard"]["color"]["a"]) {
                    $cfg[$data["status"]]["color"]["set"] = $cfg["wizard"]["color"]["b"];
                } else {
                    $cfg[$data["status"]]["color"]["set"] = $cfg["wizard"]["color"]["a"];
                }

                // datum bearbeiten
                $tmp_date1 = explode(" ",$data["changed"]);
                $tmp_date = explode("-",$tmp_date1[0]);
                $date = $tmp_date[2].".".$tmp_date[1].".".$tmp_date[0];

                // Sortierdatum
                $sort_date = $date;
                $sort_date_db = $data["changed"];
                preg_match("/\[SORT\](.+)\[\/SORT\]/Us",$data["content"],$match);
                if ( count($match) > 1 ) {
                    $tmp_date1 = explode(" ",$match[1]);
                    $tmp_date = explode("-",$tmp_date1[0]);
                    $sort_date = $tmp_date[2].".".$tmp_date[1].".".$tmp_date[0];
                    $sort_date_db = $match[1];
                }

                $new_releases[$data["status"]][] = array(
                    "path" => $path,
                    "tname"=> $data["tname"],
                    "preview" => $pathvars["virtual"]."/wizard/show,".$db->getDb().",".$tname.",inhalt,,,verify.html",
                   "titel" => $titel,
                     "ext" => $ext,
               "kategorie" => $kategorie,
                  "author" => $data["byforename"]." ".$data["bysurname"],
             "last_author" => $last_author,
                "last_uid" => $last_uid,
                 "changed" => $date,
              "changed_db" => $data["changed"],
                    "sort" => $sort_date,
                 "sort_db" => $sort_date_db,
                    "view" => $view_link,
                    "edit" => $pathvars["virtual"]."/wizard/show,".$db->getDb().",".$tname.",".$label.".html",
                     "del" => $pathvars["virtual"]."/wizard/delete,".$db->getDb().",".$tname.",".$label.".html",
                  "unlock" => $pathvars["virtual"]."/wizard/release,".$environment["parameter"][1].",".$tname.",".$label.",unlock,".$data["version"].".html",
                 "release" => $pathvars["virtual"]."/wizard/release,".$environment["parameter"][1].",".$tname.",".$label.",release,".$data["version"].".html",
                 "history" => $pathvars["virtual"]."/admin/contented/history,,".$tname.",".$label.",".@$dat_akt["version"].",".$data["version"].".html",
                   "color" => $cfg[$data["status"]]["color"]["set"],
                   "status" => $data["status"],
                );
            }

            return $new_releases;
        }

        function lockat( $art="update" ) {
            global  $db, $cfg, $pathvars, $environment, $ausgaben, $hidedata;

            $sql = "SELECT byalias, lockat
                    FROM site_lock
                    WHERE lang = '".$environment["language"]."'
                    AND label ='".$environment["parameter"][3]."'
                    AND tname ='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result, $nop);

            $LockYear = substr($data["lockat"],0,4);
            $LockMonth = substr($data["lockat"],5,2);
            $LockDay= substr($data["lockat"],8,2);

            $LockHour = substr($data["lockat"],11,2);
            $LockMinute = substr($data["lockat"],14,2);
            $LockSecond = substr($data["lockat"],17,2);

            $timestamp = mktime($LockHour,$LockMinute,$LockSecond,$LockMonth,$LockDay,$LockYear);
            $check_time = $timestamp+$cfg["wizard"]["lock_time"]+60;
            $ausgaben["lock_time"] = $cfg["wizard"]["lock_time"]*1000;

            // lockat setzen oder updaten
            if ( $art == "update" ) {
                // VON MIR GESPERRT
                if ( $_SESSION["alias"] == $data["byalias"] ) {
                    $sql = "UPDATE site_lock SET byalias='".$_SESSION["alias"]."',lockat='".date("Y-m-d H:i:s")."' WHERE tname='".$environment["parameter"][2]."' and lang = '".$environment["language"]."' and label='".$environment["parameter"][3]."'";
                    $result  = $db -> query($sql);
                // VON KEINEM GESPERRT
                } elseif ( !$data ) {
                    $sql = "INSERT INTO site_lock
                            (tname, lang, label, byalias, lockat)
                    VALUES ('".$environment["parameter"][2]."',
                            '".$environment["language"]."',
                            '".$environment["parameter"][3]."',
                            '".$_SESSION["alias"]."',
                            '".date("Y-m-d H:i:s")."')";
                    $result  = $db -> query($sql);
                }
            } elseif ( $art == "check" ) {
                if ( $_SESSION["alias"] != $data["byalias"] ) {
                    if (  $check_time < time() ) {
                        if ( $data ) {
                            $sql = "DELETE from site_lock WHERE tname='".$environment["parameter"][2]."' and lang = '".$environment["language"]."' and label='".$environment["parameter"][3]."'";
                            $result = $db -> query($sql);
                        }
                        $sql = "INSERT INTO site_lock
                            (tname, lang, label, byalias, lockat)
                            VALUES ('".$environment["parameter"][2]."',
                                    '".$environment["language"]."',
                                    '".$environment["parameter"][3]."',
                                    '".$_SESSION["alias"]."',
                                    '".date("Y-m-d H:i:s")."')";
                        $result  = $db -> query($sql);
                    } else {

                        $hidedata["lock_time"]["user"] = $data["byalias"];
                        $hidedata["lock_time"]["time"] = date("d.m.Y H:i:s",$check_time);
                        $hidedata["lock_time"]["akt_time"] = date("d.m.Y H:i:s");
                        header("HTTP/1.0 404 Not Found");
                    }
                } else {
                    $sql = "UPDATE site_lock SET byalias='".$_SESSION["alias"]."',lockat='".date("Y-m-d H:i:s")."' WHERE tname='".$environment["parameter"][2]."' and lang = '".$environment["language"]."' and label='".$environment["parameter"][3]."'";
                    $result  = $db -> query($sql);
                }

            } elseif ( $art == "close") {
                if ( $_SESSION["alias"] == $data["byalias"] ) {
                    $sql = "DELETE from site_lock WHERE byalias='".$_SESSION["alias"]."' and tname='".$environment["parameter"][2]."' and lang = '".$environment["language"]."' and label='".$environment["parameter"][3]."'";
                    $result = $db -> query($sql);
                }
            }
        }
    ### platz fuer weitere funktionen ###

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
