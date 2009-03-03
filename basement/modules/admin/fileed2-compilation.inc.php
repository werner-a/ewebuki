<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - list funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2009 Werner Ammon ( wa<at>chaos.de )

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

    if ( $cfg["fileed"]["right"] == "" ||
        priv_check("/".$cfg["fileed"]["subdir"]."/".$cfg["fileed"]["name"],$cfg["fileed"]["right"]) ||
        priv_check_old("",$cfg["fileed"]["right"]) ) {

        // funktions bereich
        // ***

        // ajax-steuerung der compilation-auswahl
        if ( $_POST["ajax"] != "" ) {

            echo "<pre>".print_r($environment["parameter"],true)."</pre>";

            $cid = $environment["parameter"][1];
            $pid = $environment["parameter"][2];

            // alle nicht-ausgewaehlten compilations aus session loeschen
            if ( is_array($_SESSION["compilation_memo"]) ) {
                foreach ( $_SESSION["compilation_memo"] as $key=>$value ) {
                    if ( $key != $cid ) unset($_SESSION["compilation_memo"][$key]);
                }
            }

            if ( is_numeric($pid) ) {
                if ( $_SESSION["compilation_memo"][$cid][$pid] != "" ) {
                    unset($_SESSION["compilation_memo"][$cid][$pid]);
                } else {
                    $_SESSION["compilation_memo"][$cid][$pid] = $pid;
                }
            } else {
                if ( is_array($_SESSION["compilation_memo"][$cid]) ) {
                    unset ($_SESSION["compilation_memo"]);
                } else {
                    $_SESSION["compilation_memo"][$cid] = array();
                }
            }
            die();
        }

        // compilation-array bauen lassen
        $compilations = compilation_list($environment["parameter"][1]);

        // suche
        // * * * * *

        /* suchfeld */
        $filters = array();
        if ( $_POST["send"] != "" ) {
            if ( $_POST["search"] != "" ) {
                $_SESSION["compilation_search"] = $_POST["search"];
            } elseif ( isset($_SESSION["compilation_search"]) ) {
                unset( $_SESSION["compilation_search"] );
            }
        }
        $ausgaben["search"] = $_SESSION["compilation_search"];
        if ( $_SESSION["compilation_search"] != "" ) {
            // array wird durchsucht
            function compilation_search($comp) {
                if ( $comp["id"] == $_SESSION["compilation_search"]
                  || stristr($comp["name"],$_SESSION["compilation_search"])
                  || stristr($comp["desc"],$_SESSION["compilation_search"]) ) {
                    return $comp;
                }
            }
            $compilations = array_filter($compilations,"compilation_search");
            $filters[] = $_SESSION["compilation_search"];
        }

        /* nur ausgewaehlte gruppierungen */
        if ( $_POST["send"] != "" ) {
            if ( $_POST["sel_search"] == -1 ) {
                $header = $cfg["fileed"]["basis"]."/compilation,,sel,".$environment["parameter"][3].".html";
            } else {
                $header = $cfg["fileed"]["basis"]."/compilation,,,".$environment["parameter"][3].".html";
            }
            header("Location:".$header);
        }
        if ( $environment["parameter"][2] == "sel" ) {
            if ( is_array($_SESSION["compilation_memo"]) ) {
                $compilations = array_intersect_key($compilations,$_SESSION["compilation_memo"]);
                $filters[] = "#(filter_selected)";
            }
        } else {
            $hidedata["search_sel"]["check"] = "";
        }
        if ( is_array($_SESSION["compilation_memo"]) ) {
            $hidedata["search_sel"]["display"] = "block";
            $hidedata["search_sel"]["count"] = count($_SESSION["compilation_memo"]);
        } else {
            $hidedata["search_sel"]["display"] = "none";
        }
        $ausgaben["result"] = "";
        if ( count($filters) > 0 ) $ausgaben["result"] = "#(answera) <b>\"".implode("\"</b> und <b>\"",$filters)."\"</b> #(answerb) ";

        // + + + + +

        // inhaltselektor-imitat
        // * * * * *
        $position = 0;
        if ( $environment["parameter"][3] != "" ) $position = $environment["parameter"][3];
        // gesamtanzahl der selektoren (z.B. 1-4)
        $gesamt = count($compilations);
        $hidedata["search_result"] = array();
        $ausgaben["anzahl"] = $gesamt;
        // wie gross ist ein selektor
        $menge = $cfg["fileed"]["compilation"]["rows"];
        // wieviele elemente darf eine selektor-gruppen maximal haben
        $sel_groups_max = $cfg["fileed"]["compilation"]["selektor"];

        // selektor bauen
        if ( $gesamt > $menge ) {
            // wieviel selektoren gibt es insgesamt
            $sel_parts = ceil($gesamt/$menge);
            $rest = $gesamt%$menge;

            // wieviel selektor-gruppen gibt es
            $sel_groups = ceil($sel_parts/$sel_groups_max);

            $inh_selector = array(); $sel_parts_index = 0; $sel_groups_index = 0; $g_index = 0;
            for ( $i=0; $i<$sel_parts; $i++ ) {
                if ( $i%$sel_groups_max == 0 ) $g_index++;
                $start = ($i*$menge);
                $end = (($i+1)*$menge);
                if ( $i+1 == $sel_parts && $rest > 0 ) {
                    $end = $i*$menge+$rest;
                }
                $link = $cfg["fileed"]["basis"]."/compilation,".$environment["parameter"][1].",".$environment["parameter"][2].",".$start.".html";
                $inh_link[$i] = $link;
                if ( $position == $start ) {
                    $inh_selector[$i] = "<b>".($start+1)."-".$end."</b>";
                    $ausgaben["inhalt_selected"] = ($start+1)."-".$end;
                    $sel_groups_index = $g_index;
                    $sel_parts_index = $i + 1;
                } else {
                    $inh_selector[$i] = "<a href=\"".$link."\">".($start+1)."-".$end."</a>";
                }
            }

            // nur die selektoren-gruppe anzeigen
            if ( $sel_groups_index != 0 ) $inh_selector = array_slice($inh_selector,(($sel_groups_index-1)*$sel_groups_max),$sel_groups_max,true);

            // gibt es vorher und nochher noch gruppen
            if ( $sel_groups_index > 1 ) array_unshift($inh_selector,"...");
            if ( $sel_groups_index < $sel_groups ) array_push($inh_selector,"...");

            // elemente zusammenfuegen
            $ausgaben["inhalt_selector"] = implode(" | ",$inh_selector);

            // pfeile zum vor- und zuruecksteuern
            $defaults["select"]["prev"] == "" ? $defaults["select"]["prev"] = "<img src=\"/images/default/left.png\" height=\"18\" width=\"24\" border=\"0\" align=\"top\" alt=\"#(prev)\" title=\"#(prev)\" />" : NOP;
            $defaults["select"]["next"] == "" ? $defaults["select"]["next"] = "<img src=\"/images/default/right.png\" height=\"18\" width=\"24\" border=\"0\" align=\"top\" alt=\"#(next)\" title=\"#(next)\" />" : NOP;
            if ( $sel_parts_index > 1 )  {
                $ausgaben["inhalt_selector"] = "<a href=\"".$inh_link[$sel_parts_index-2]."\">".$defaults["select"]["prev"]."</a>".$ausgaben["inhalt_selector"];
            }
            if ( $sel_parts_index < $sel_parts )  {
                $ausgaben["inhalt_selector"] .= "<a href=\"".$inh_link[$sel_parts_index]."\">".$defaults["select"]["next"]."</a>";
            }

        } elseif ( $gesamt > 0 ) {
            $ausgaben["inhalt_selected"] = "1-".$gesamt;
            $ausgaben["inhalt_selector"] = "";
        } else {
            $ausgaben["inhalt_selected"] = "0";
            $ausgaben["inhalt_selector"] = "";
        }
        // + + + + +
        // inhaltselektor-imitat


        function pics_sort($a, $b) {
            return ($a["sort"] < $b["sort"]) ? -1 : 1;
        }

        // gruppierungsarray wird zugeschnitten
        $sliced_groups = array_slice($compilations,$position,$menge,true);

        foreach ( $sliced_groups as $key=>$value ){
            $id = $value["id"];
            $check = "";
            if ( is_array($_SESSION["compilation_memo"][$id]) ) $check = " checked=\"true\"";
            $edit = "&nbsp;";
            if ( $value["name"] == "---" || $cfg["fileed"]["compilation"]["blocked_used"] != true ) {
                $edit = "<a href=\"".$cfg["fileed"]["basis"]."/collect,".$id.".html\" title=\"g(edit)\"><img src=\"/images/default/edit.png\" alt=\"g(edit)\" /></a>";
            }

            $used_on = "";
            if ( is_array($value["content"]) ) {
                $used_on = "<br />#(used_on)";
                foreach ( $value["content"] as $tname ) {
                    $link = tname2path($tname).".html";
                    $used_on .= "<br /><a href=\"".$link."\">".$link."</a>";
                }
            }

            $used_title_text = "";
            $used_title_show = "display:none;";
            if ( $value["name"] != "---" ) {
                $used_title_text = str_replace(";;","<br />",$value["name"]);
                $used_title_show = "";
            }

            $dataloop["compilation"][$id] = array(
                        "id" => $id,
                     "count" => $num_pics,
                   "used_on" => $used_on,
                     "check" => $check,
                      "edit" => $edit,
           "used_title_text" => $used_title_text,
           "used_title_show" => $used_title_show,
                "used_title" => "",
            );

            // bilder der compilation finden
            $sql = "SELECT *
                      FROM site_file
                     WHERE fhit
                      LIKE '%#p".$id.",%'
                  ORDER BY fid";
            $list_item = "<li class=\"thumbs\">
                                <a title=\"##title##\" class=\"pic\" rel=\"lightbox[##cid##]\" href=\"##src_lb##\"><img title=\"##title##\" alt=\"##title##\" src=\"##src##\"/></a>
                                <input id=\"c##cid##p##pid##\" class=\"sel_pic_checkbox\" type=\"checkbox\" value=\"-1\" onclick=\"session_update(##cid##,##pid##);\"##check## />
                          </li>";
            $search = array('##title##','##cid##','##pid##','##src_lb##','##src##','##check##');
            $result = $db -> query($sql);
            $pic_array = array();
            $dataloop["list_images"] = array();
            filelist($result, "fileed", $key);
            uasort($dataloop["list_images"],"pics_sort");
            // anzahl der bilder
            $num_pics = count($dataloop["list_images"]);
            $dataloop["compilation"][$id]["count"] = $num_pics;
            // galerie bauen
            $i = 0;$lb_pics="";$pics="";
            foreach ( $dataloop["list_images"] as $pic ) {

                $check = "";
                if ( $_SESSION["compilation_memo"][$id][$pic["id"]] != "" ) $check = " checked=\"true\"";
                $replace = array(
                    $pic["under"],
                    $id,
                    $pic["id"],
                    $pic["ohref_lb"],
                    $pic["src"],
                    $check,
                );

                $pics .= str_replace($search,$replace,$list_item);
                $i++;
            }
            // restliche lightbox-bilder
            $dataloop["compilation"][$id]["lb_pics"] = $lb_pics;
            $dataloop["compilation"][$id]["pics"] = $pics;
        }

        if ( isset($_SESSION["cms_last_edit"]) ) {
            // abrechen im cms editor soll zur ursrungseite springen und nicht in den fileed
            $_SESSION["page"] = $_SESSION["cms_last_referer"];
            $hidedata["cms"] = array(
                   "link" => $_SESSION["cms_last_edit"]."?referer=".$_SESSION["cms_last_referer"],
                "display" => "inline",
            );
        }

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["fileed"]["basis"]."/compilation,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].".html";
        $ausgaben["form_break"]  = $cfg["fileed"]["basis"]."/list.html";
        $ausgaben["edit"]        = $cfg["fileed"]["basis"]."/collect,".$environment["parameter"][1].".html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["fileed"]["path"] = str_replace($pathvars["virtual"],"",$cfg["fileed"]["basis"]);
        $mapping["main"] = eCRC($cfg["fileed"]["path"]).".compilation";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "g (cmslink) g(cmslink)<br />";
            $ausgaben["inaccessible"] .= "# (img_plural) #(img_plural)<br />";
            $ausgaben["inaccessible"] .= "# (img_sing) #(img_sing)<br />";
            $ausgaben["inaccessible"] .= "# (all_names) #(all_names)<br />";
            $ausgaben["inaccessible"] .= "# (check_error1) #(check_error1)<br />";
            $ausgaben["inaccessible"] .= "# (check_error2) #(check_error2)<br />";

            $ausgaben["inaccessible"] .= "# (answera) #(answera)<br />";
            $ausgaben["inaccessible"] .= "# (answerb) #(answerb)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_no) #(answerc_no)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes) #(answerc_yes)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes_sing) #(answerc_yes_sing)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
