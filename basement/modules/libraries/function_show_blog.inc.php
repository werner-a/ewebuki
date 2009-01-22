<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menu-convert.inc.php 311 2005-03-12 21:46:39Z chaot $";
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

     function show_blog($url,$tags,$right="",$limit="",$kategorie="") {
        global $db,$pathvars,$ausgaben,$mapping,$hidedata,$environment,$cfg,$specialvars;

        // parameter-erklaerung
        // 1: vorgesehen fuer inhalt_selector
        // 2: aufruf eines einzigen contents
        // 3: anzeige als faq

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // label bearbeitung aktivieren
        if ( isset($_GET["edit"]) ) {
            $specialvars["editlock"] = 0;
        } else {
            $specialvars["editlock"] = -1;
        }

        // aus der url eine id machen
        $id = make_id($url);
        $new = $id["mid"];
        $where = "";

        $sort_len = strlen($cfg["bloged"]["blogs"][$url]["sort"][0])+2;

        // hier erfolgt der rechte-check
        $check_url = $url;
        if ( $kategorie != "" ) $check_url = $kategorie;

        $status = "status=1";
        $order = "";
        $wizard_right = "";
        // falls der der content bei dem der blog eingebunden ist, zur Freigabe angefordert ist, darf hier nichts mehr passieren
        if ( $kategorie == tname2path($environment["parameter"][2]) && !priv_check($check_url,"publish")) {
            $sql = "SELECT status from site_text WHERE tname='".$environment["parameter"][2]."' AND label='".$environment["parameter"][3]."' ORDER by version DESC";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,1);
            if ( $data["status"] == -2 ) $wizard_right = "NO";
        }

        if ( $right == ""  ||
         ( priv_check($check_url,$right) || ( function_exists(priv_check_old) && priv_check_old("",$right) )  ) && $wizard_right == ""
         ) {
             $hidedata["new"]["link"] = $url;
             $hidedata["new"]["kategorie"] = $kategorie;
            if ( $environment["ebene"] == "/wizard" ) {
                $status = "(status=1 OR status = -1)";
                $order=" DESC ,changed";
            }
         }


        // erster test einer suchanfrage per kalender
        //

        if ( $environment["parameter"][4] && $environment["parameter"][5] ) {
            if ( $cfg["bloged"]["blogs"][$url]["sort"][1] != -1 ) {
                $heute = getdate(mktime(0, 0, 0, ($environment["parameter"][5])+1, 0, $environment["parameter"][4]));
                if ( !$environment["parameter"][6] ) {
                    $day1 = $heute["mday"];
                    $day2 = "1";
                } else {
                    $day1 = $environment["parameter"][6];
                    $day2 = $environment["parameter"][6];
                }
                if ( $cfg["bloged"]["blogs"][$url]["ext_sort"] == "" ) {
                    $where .= " AND Cast(SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)+".$sort_len.",POSITION('[/".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-".$sort_len.") as DATETIME) < '".$environment["parameter"][4]."-".$environment["parameter"][5]."-".$day1." 23:59:59' AND Cast(SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)+".$sort_len.",POSITION('[/".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-".$sort_len.") as DATETIME) > '".$environment["parameter"][4]."-".$environment["parameter"][5]."-".$day2." 00:00:00'";
                }  else {
                    $where .= " AND (( Cast(SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)+".$sort_len.",POSITION('[/".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-".$sort_len.") as DATETIME) < '".$environment["parameter"][4]."-".$environment["parameter"][5]."-".$day1." 23:59:59' AND Cast(SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)+".$sort_len.",POSITION('[/".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-".$sort_len.") as DATETIME) >= '".$environment["parameter"][4]."-".$environment["parameter"][5]."-".$day2." 00:00:00')";
                    $sort_len2 = strlen($cfg["bloged"]["blogs"][$url]["ext_sort"])+2;
                    $where .= " OR ( Cast(SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)+".$sort_len.",POSITION('[/".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-".$sort_len.") as DATETIME) <= '".$environment["parameter"][4]."-".$environment["parameter"][5]."-".$day2." 00:00:00' AND  Cast(SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$url]["ext_sort"]."]' IN content)+".$sort_len2.",POSITION('[/".$cfg["bloged"]["blogs"][$url]["ext_sort"]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$url]["ext_sort"]."]' IN content)-".$sort_len2.") as DATETIME) > '".$environment["parameter"][4]."-".$environment["parameter"][5]."-".$day2." 00:00:00'))";
                }
            }
        }
        //
        // erster test einer suchanfrage per kalender

        // falls kategorie , werden nur diese angezeigt
        if ( $kategorie != "" ) {
            $cat_len = strlen($cfg["bloged"]["blogs"][$url]["category"])+2;
            $where .= "  AND SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$url]["category"]."]' IN content),POSITION('[/".$cfg["bloged"]["blogs"][$url]["category"]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$url]["category"]."]' IN content)) ='[".$cfg["bloged"]["blogs"][$url]["category"]."]".$kategorie."'";
        }

        $tname = eCRC($url).".%";

        // falls parameter 2 gesetzt, wird nur dieser content geholt
        if ( $environment["parameter"][2] != "" && $environment["ebene"] != "/wizard" ) {
            $tname = eCRC($url).".".$environment["parameter"][2];
        }

        // falls sort auf -1 wird anstatt ein datum ein integer als sortiermerkmal gesetzt um ein manuelles sortieren zu ermoeglichen
        if ( $cfg["bloged"]["blogs"][$url]["sort"][1] == "-1" ) {
            $art = "SIGNED";
        } else {
            $art = "DATETIME";
        }

        // hier der endgueltige sql !!
        $sql = "SELECT Cast(SUBSTR(content,POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)+".$sort_len.",POSITION('[/".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-POSITION('[".$cfg["bloged"]["blogs"][$url]["sort"][0]."]' IN content)-".$sort_len.") AS ".$art.") AS date,status,content,tname from site_text WHERE ".$status." AND tname like '".$tname."'".$where." order by date".$order." DESC";
        // damit kann man beliebig viele blogs manuell holen

        $ausgaben["inhalt_selector"] = "";
        if ( strpos($limit,"," ) ){
            $sql = $sql." LIMIT ".$limit;
        } else {
            if ( $limit != "" ) {
                $hidedata["inhalt_selector"]["on"] = "on";
                $p=$environment["parameter"][1]+0;
                // seiten umschalter
                $inhalt_selector = inhalt_selector( $sql, $p, $limit, $parameter, 1, 10, $getvalues );
                $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
                $sql = $inhalt_selector[1];
                $ausgaben["anzahl"] = $inhalt_selector[2];
            }
        }

        $counter = 0;
        $result = $db -> query($sql);
        $preg1 = "\.([0-9]*)$";

        // evtl wizard einbinden
        if ( $cfg["bloged"]["blogs"][$url]["wizard"] != "" ) {
            $editlink = "/wizard/show,";
        } else {
            $editlink = "/admin/contented/edit,";
        }

        while ( $data = $db -> fetch_array($result,1) ) {
            $tag_parameter="";
            $counter++;
            // im wizard wird der content aus der SESSION-Variablen genommen
            if ( $_SESSION["wizard_content"][DATABASE.",".$data["tname"].",inhalt"] && $environment["ebene"] == "/wizard") {
                $test = preg_replace("|\r\n|","\\r\\n",$_SESSION["wizard_content"][DATABASE.",".$data["tname"].",inhalt"]);
            } else {
                $test = preg_replace("|\r\n|","\\r\\n",$data["content"]);
            }
            foreach ( $tags as $key => $value ) {
                // finden der parameter sowie begin und endtag
                $invisible = "";
                if (is_array($value)) {
                    $tag_parameter= $value["parameter"];
                    $invisible = $value["invisible"];
                    $show = $value["show"];
                    $value = $value["tag"];
                }
                if (strpos($value,"=")) {
                     $endtag= substr($value,0,strpos($value,"="));
                    if ( $value == "IMG=") {
                        $value .= ".*";
                    } else {
                        $value = $value.$tag_parameter;
                    }
                } else {
                    $endtag=$value;
                }
                // preg nach den tags in der config
                $preg = "(\[".addcslashes($value,"/")."\])(.*)\[\/".$endtag."\]";
                if ( preg_match("/$preg/Us",$test,$regs) ) {
                    $rep_tag = str_replace('\r\n',"<br>",$regs[0]);
                    $org_tag = str_replace('\r\n',"<br>",$regs[2]);
                } else {
                    $rep_tag = "";
                    $org_tag = "";
                }

                // gefundene werte in array schreiben
                if ( $invisible != -1 ) {
                    $array[$counter][$key."_wizard_edit_link"] = $pathvars["virtual"]."/wizard/editor,".DATABASE.",".$data["tname"].",inhalt,".$value.":0,,,.html";
                    $array[$counter][$key."_org"] = str_replace("\"","'",$org_tag);
                    $array[$counter][$key."_org_tag"] = $value;
                    $array[$counter][$key] = tagreplace($rep_tag);
                    if ( $org_tag == "" ) $array[$counter][$key] = "";
                    if ( preg_match("/^\[IMG/",$rep_tag,$regs_img) ) {
                        $image_para = explode("/",$rep_tag);
                        $array[$counter][$key."_img_art"] = $image_para[2];
                        $array[$counter][$key."_img_id"] = $image_para[3];
                        $array[$counter][$key."_img_size"] = $image_para[4];
                        $sql_img = "SELECT * FROM site_file WHERE fid='".$image_para[3]."'";
                        $result_img = $db -> query($sql_img);
                        $data_img = $db -> fetch_array($result_img,1);
                        $array[$counter][$key."_img_desc"] = $data_img["fdesc"];
                        $array[$counter][$key."_img_under"] = $data_img["funder"];
                        $array[$counter][$key."_img_fname"] = $data_img["ffname"];
                        if ( $show != "" ) {
                            $rep_tag = str_replace("/".$image_para[4]."/","/".$show."/",$rep_tag);
                        }
                    }
                } else {
                    if ( preg_match("/^\[IMG/",$rep_tag,$reg_img) ) {
                        $image_para = explode("/",$rep_tag);
                        $invisible_array[$counter][$key."_img_art"] = $image_para[2];
                        $invisible_array[$counter][$key."_img_id"] = $image_para[3];
                        $invisible_array[$counter][$key."_img_size"] = $image_para[4];
                        if ( $show != "" ) {
                            $rep_tag = str_replace("/".$image_para[4]."/","/".$show."/",$rep_tag);
                        }
                    }
                    $invisible_array[$counter][$key."_org"] = str_replace("\"","'",$org_tag);
                    $invisible_array[$counter][$key] = tagreplace($rep_tag);
                    $array[$counter][$key."_org"] = "";
                    $array[$counter][$key] = "";
                }
            }

            preg_match("/$preg1/",$data["tname"],$regs);
            if ( $environment["parameter"][2] != "" && $environment["ebene"] != "/wizard" ) {
                $array[$counter]["all"] = tagreplace($data["content"]);
                $array[$counter]["id"] = $regs[1];
            } else {
                $array[$counter]["datum"] = substr($data["date"],8,2).".".substr($data["date"],5,2).".".substr($data["date"],0,4);
                $array[$counter]["detaillink"] = $pathvars["virtual"].$url."/".$regs[1].".html";

                if ( $environment["ebene"] == "" ) {
                    $faq_url = "/".$environment["kategorie"];
                } else {
                    $faq_url = $environment["ebene"]."/".$environment["kategorie"];
                }

                $array[$counter]["faqlink"] = $pathvars["virtual"].$faq_url.",,,".$regs[1].".html#faq_".$regs[1];
                $array[$counter]["faqanker"] = "faq_".$regs[1];
                $array[$counter]["allink"] = $pathvars["virtual"].$faq_url.",,".$regs[1].".html";
                $array[$counter]["id"] = $regs[1];
                $array[$counter]["status"] = $data["status"];
                // Sortierung ausgeben
                // ausgabe der aktions-buttons
                if ( $right == "" ||
                ( priv_check($check_url,$right) || ( function_exists(priv_check_old) && priv_check_old("",$right) ) ) && $wizard_right == ""
                ) {

                    if ( $cfg["bloged"]["blogs"][$url]["sort"][1] == "-1") {
                        $sort_kat = "";
                        if ( $kategorie != "" ) {
                            $id = make_id($kategorie);
                            $sort_kat = $id["mid"];
                        }
                        $array[$counter]["sort_up"] = $pathvars["virtual"]."/admin/bloged/sort,up,".$regs[1].",".$sort_kat.",".$new.".html";
                        $array[$counter]["sort_down"] = $pathvars["virtual"]."/admin/bloged/sort,down,".$regs[1].",".$sort_kat.",".$new.".html";
                    } else {
                        $array[$counter]["sort_up"] = "";
                        $array[$counter]["sort_down"] = "";
                    }

                    $array[$counter]["wizard_delete_link"] = "<a href=\"".$pathvars["virtual"]."/admin/bloged/delete,,".$regs[1].",".$sort_kat.",".$new.".html\">delete</a>";
                    $array[$counter]["deletelink"] = "<a href=\"".$pathvars["virtual"]."/admin/bloged/delete,,".$regs[1].",".$sort_kat.",".$new.".html\">delete</a>";
                    $array[$counter]["editlink"] = "<a href=\"".$pathvars["virtual"].$editlink.DATABASE.",".$data["tname"].",inhalt.html\">edit</a>";
                    $array[$counter]["tname"] = eCrc($url);
                } else {
                    $array[$counter]["editlink"] = "";
                    $array[$counter]["deletelink"] = "";
                    $array[$counter]["sort"] = "";
                }
            }

            if ( $environment["parameter"][3] == $regs[1] ) {
                if ( is_array($invisible_array) ){
                    foreach ( $invisible_array[$counter] as $key => $value ) {
                        $array[$counter][$key] = $value;
                    }
                }
            }
        }

            // was anzeigen
            if ( $environment["ebene"] == "" ) {
                $templ = $environment["kategorie"];
            } else {
                $templ = eCRC($environment["ebene"]).".".$environment["kategorie"];
            }

            if ( file_exists($pathvars["templates"].$templ.".tem.html") ) {
                $mapping["main"] = $templ;
            } elseif ( $cfg["bloged"]["blogs"][$url]["own_list_template"] != "" ) {
                $mapping["main"] = "-2051315182.".$cfg["bloged"]["blogs"][$url]["own_list_template"];
            } elseif ( $cfg["bloged"]["blogs"][$url]["sort"][1] != "" ) {
                $mapping["main"] = "-2051315182.faq";
            } else {
                $mapping["main"] = "-2051315182.list";
            }
            return $array;
        }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
