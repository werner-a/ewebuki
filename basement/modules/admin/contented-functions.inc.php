<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
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

//     if ( in_array("makece", $cfg["contented"]["function"][$environment["kategorie"]]) ) {
//          function function_name(  $var1, $var2 = "") {
//             ### put your code here ###
//          }
//     }

    // content editor erstellen
    if ( in_array("makece", $cfg["contented"]["function"][$environment["kategorie"]]) ) {

        function makece($ce_formname, $ce_name, $ce_inhalt) {
            global $debugging, $db, $cfg, $pathvars, $ausgaben, $specialvars, $defaults;

            // vogelwilde regex die alte & neue file links findet
            // und viel arbeit erspart
            preg_match_all("/[_\/]([0-9]+)[.\/]/",$ce_inhalt,$found);
            $debugging["ausgabe"] .= "<pre>".print_r($found,True)."</pre>";

            // file memo auslesen und zuruecksetzen
            if ( is_array($_SESSION["file_memo"]) ) {
                $array = array_merge($_SESSION["file_memo"],$found[1]);
//                 unset($_SESSION["file_memo"]);
            } else {
                $array = $found[1];
            }

            // wenn es thumbnails gibt, anzeigen
            if ( count($array) >= 1 ) {
                $merken = $db -> getDb();
                if ( $merken != DATABASE ) {
                    $db -> selectDB( DATABASE ,"");
                }
                foreach ( $array as $value ) {
                    if ( $where != "" ) $where .= " OR ";
                    $where .= "fid = '".$value."'";
                }
                $sql = "SELECT * FROM site_file WHERE ".$where." ORDER BY ffname, funder";
                $result = $db -> query($sql);
                if ( $merken != DATABASE ) {
                    $db -> selectDB($merken,"");
                }

                $ausgaben["extension"] = ""; $sp = "    ";
                #$tn = "\n<table width=\"100%\"><tr><td>";
                $tn2 ="<br clear=\"all\" /><br />";
                while ( $data = $db -> fetch_array($result, NOP) ) {
                    #$file[$data["fid"]] = array(
                    #                        "fart"  =>  $data["ffart"],
                    #                        "fdesc" =>  $data["fdesc"],
                    #                        );
                    switch ( $data["ffart"] ) {
                        case "pdf": case "odt":  case "ods": case "odp":
                            // die boese schneide ab funktion
                            if ( strlen($data["funder"]) > 6 ) {
                                $funder = substr($data["funder"],0,5)." ...";
                            } else {
                                $funder = $data["funder"];
                            }
                            #$tn .= "<a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img src=\"".$pathvars["images"]."pdf.png"."\"></a> ";
                            $tnd .= "\n<table align=\"left\" width=\"96\">";
                            $tnd .= "<tr><td><a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\">".$funder."</a></td></tr>";
                            $tn2 .= "<a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\">".$data["ffname"]."</a> ( ";
                            $tn2 .= "<a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\">".$data["funder"]."</a> )<br />";

                            if ( $defaults["icon"]["pdf"] == "" ) $defaults["icon"]["pdf"] = "<img src=\"/images/default/icon_pdf.png\" width=\"64\" height=\"64\" title=\"".$data["funder"]."\"/>";
                            $tnd .= "<tr><td>".$defaults["icon"]["pdf"]."</td></tr>";
                            $tnd .= "</table>";

                            if ( $cfg["file"]["base"]["realname"] == True ) {
                                $ausgaben["extension"] .= $sp."else if (st=='doc".$data["fid"]."')\n".$sp.$sp."st='[LINK=".$cfg["file"]["base"]["webdir"].$data["ffart"]."/".$data["fid"]."/".$data["ffname"]."]".$data["funder"]."[/LINK]';";
                            } else {
                                $ausgaben["extension"] .= $sp."else if (st=='doc".$data["fid"]."')\n".$sp.$sp."st='[LINK=".$cfg["file"]["base"]["webdir"].$cfg["file"]["base"]["doc"]."doc_".$data["fid"].".".$data["ffart"]."]".$data["fdesc"]."[/LINK]';";
                            }
                            break;
                        case "zip": case "bz2": case "gz":
                            // die boese schneide ab funktion
                            if ( strlen($data["funder"]) > 7 ) {
                                $funder = substr($data["funder"],0,6)." ...";
                            } else {
                                $funder = $data["funder"];
                            }
                            #$tn .= "<a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img src=\"".$pathvars["images"]."pdf.png"."\"></a> ";
                            $tnd .= "\n<table align=\"left\" width=\"96\">";
                            $tnd .= "<tr><td><a href=\"#\" onclick=\"INSst('arc".$data["fid"]."','".$ce_formname."','".$ce_name."')\">".$funder."</a></td></tr>";
                            $tn2 .= "<a href=\"#\" onclick=\"INSst('arc".$data["fid"]."','".$ce_formname."','".$ce_name."')\">".$data["ffname"]."</a> ( ";
                            $tn2 .= "<a href=\"#\" onclick=\"INSst('arc".$data["fid"]."','".$ce_formname."','".$ce_name."')\">".$data["funder"]."</a> )<br />";
                            if ( $defaults["icon"]["zip"] == "" ) $defaults["icon"]["zip"] = "<img src=\"/images/default/icon_zip.png\" width=\"64\" height=\"64\" title=\"".$data["funder"]."\"/>";
                            $tnd .= "<tr><td>".$defaults["icon"]["zip"]."</td></tr>";
                            $tnd .= "</table>";

                            if ( $cfg["file"]["base"]["realname"] == True ) {
                                $ausgaben["extension"] .= $sp."else if (st=='arc".$data["fid"]."')\n".$sp.$sp."st='[LINK=".$cfg["file"]["base"]["webdir"].$data["ffart"]."/".$data["fid"]."/".$data["ffname"]."]".$data["funder"]."[/LINK]';";
                            } else {
                                $ausgaben["extension"] .= $sp."else if (st=='arc".$data["fid"]."')\n".$sp.$sp."st='[LINK=".$cfg["file"]["base"]["webdir"].$cfg["file"]["base"]["arc"]."arc_".$data["fid"].".".$data["ffart"]."]".$data["fdesc"]."[/LINK]';";
                            }
                            break;
                        default:
                            $imgsize = getimagesize($cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["pic"]["root"].$cfg["file"]["base"]["pic"]["tn"]."tn_".$data["fid"].".".$data["ffart"]);
                            $imgsize = " ".$imgsize[3];
                            #$tn .= "<a href=\"#\" onclick=\"INSst('imb".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img src=\"/dateien/bilder/thumbnail/tn_".$data["fid"].".".$data["ffart"]."\"></a> ";
                            $tn1 .= "\n<table align=\"left\" width=\"96\">";
                            $tn1 .= "<tr><td><a href=\"#\" onclick=\"INSst('imo".$data["fid"]."','".$ce_formname."','".$ce_name."')\" title=\"Original (original)\">O</a> ";
                            $tn1 .= "<a href=\"#\" onclick=\"INSst('imb".$data["fid"]."','".$ce_formname."','".$ce_name."')\" title=\"Gross (big)\">B</a> ";
                            $tn1 .= "<a href=\"#\" onclick=\"INSst('imm".$data["fid"]."','".$ce_formname."','".$ce_name."')\" title=\"Mittel (middle)\">M</a> ";
                            $tn1 .= "<a href=\"#\" onclick=\"INSst('ims".$data["fid"]."','".$ce_formname."','".$ce_name."')\" title=\"Klein (small)\">S</a></td></tr>";

                            $tn1 .= "<tr><td><a href=\"#\" onclick=\"INSst('imo".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img".$imgsize." border=\"0\" src=\"".$cfg["file"]["base"]["webdir"].$cfg["file"]["base"]["pic"]["root"].$cfg["file"]["base"]["pic"]["tn"]."tn_".$data["fid"].".".$data["ffart"]."\" alt=\"id:".$data["fid"].", .".$data["ffart"]."\" title=\"id:".$data["fid"].", .".$data["ffart"]."\"></a></td></tr>";
                            $tn1 .= "</table>";

                            if ( $defaults["cms-tag"]["grafik"] == "" ) {
                                $defaults["cms-tag"]["grafik"] = "[IMG=";
                                $defaults["cms-tag"][",grafik"] = "";
                                $defaults["cms-tag"]["/grafik"] = "[/IMG]";
                            }
                            if ( $cfg["file"]["base"]["realname"] == True ) {
                                $ausgaben["extension"] .= $sp."else if (st=='imo".$data["fid"]."')\n".$sp.$sp."st='".$defaults["cms-tag"]["grafik"].$cfg["file"]["base"]["webdir"].$data["ffart"]."/".$data["fid"]."/"."o/".$data["ffname"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                                $ausgaben["extension"] .= $sp."else if (st=='imb".$data["fid"]."')\n".$sp.$sp."st='".$defaults["cms-tag"]["grafik"].$cfg["file"]["base"]["webdir"].$data["ffart"]."/".$data["fid"]."/"."b/".$data["ffname"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                                $ausgaben["extension"] .= $sp."else if (st=='imm".$data["fid"]."')\n".$sp.$sp."st='".$defaults["cms-tag"]["grafik"].$cfg["file"]["base"]["webdir"].$data["ffart"]."/".$data["fid"]."/"."m/".$data["ffname"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                                $ausgaben["extension"] .= $sp."else if (st=='ims".$data["fid"]."')\n".$sp.$sp."st='".$defaults["cms-tag"]["grafik"].$cfg["file"]["base"]["webdir"].$data["ffart"]."/".$data["fid"]."/"."s/".$data["ffname"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                            } else {
                                $ausgaben["extension"] .= $sp."else if (st=='imo".$data["fid"]."')\n".$sp.$sp."st='".$defaults["cms-tag"]["grafik"].$cfg["file"]["base"]["webdir"].$cfg["file"]["base"]["pic"]["root"].$cfg["file"]["base"]["pic"]["o"]."img_".$data["fid"].".".$data["ffart"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                                $ausgaben["extension"] .= $sp."else if (st=='imb".$data["fid"]."')\n".$sp.$sp."st='".$defaults["cms-tag"]["grafik"].$cfg["file"]["base"]["webdir"].$cfg["file"]["base"]["pic"]["root"].$cfg["file"]["base"]["pic"]["b"]."img_".$data["fid"].".".$data["ffart"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                                $ausgaben["extension"] .= $sp."else if (st=='imm".$data["fid"]."')\n".$sp.$sp."st='".$defaults["cms-tag"]["grafik"].$cfg["file"]["base"]["webdir"].$cfg["file"]["base"]["pic"]["root"].$cfg["file"]["base"]["pic"]["m"]."img_".$data["fid"].".".$data["ffart"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                                $ausgaben["extension"] .= $sp."else if (st=='ims".$data["fid"]."')\n".$sp.$sp."st='".$defaults["cms-tag"]["grafik"].$cfg["file"]["base"]["webdir"].$cfg["file"]["base"]["pic"]["root"].$cfg["file"]["base"]["pic"]["s"]."img_".$data["fid"].".".$data["ffart"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                            }
                            $i++;
                            $a = $i / 6;
                            if ( is_int($a) ) $tn1 .="<br clear=\"all\" />";
                    }
                }
                $tn .= $tn1.$tn2;
                #."</td></tr></table>";
            }

            // path fuer alle schaltflaechen anpassen
            if ( $defaults["cms-tag"]["path"] == "" ) $defaults["cms-tag"]["path"] = "/images/default/";

            $danei ='[TAB=l;300]\n[ROW]\n[COL]1,1[\/COL]\n[COL]1,2[\/COL]\n[COL]1,3[\/COL]\n[\/ROW][ROW]\n[COL]2,1[\/COL]\n[COL]2,2[\/COL]\n[COL]2,3[\/COL]\n[\/ROW]\n[\/TAB]';

            $ausgaben["extension"] .= $sp."else if (st=='tabb')\n".$sp.$sp."st='".$danei."';";
            $tn .= "<br clear=\"all\" /><a href=\"#\" onclick=\"INSst('tabb','".$ce_formname."','".$ce_name."')\">Tabellen Beispiel</a>";

            $ausgaben["ce_dropdown"]  = "<select style=\"width:95px;font-family:Helvetica, Verdana, Arial, sans-serif;font-size:12px;\" name=\"st\" size=\"1\" onChange=\"INSst(this.options[this.selectedIndex].value,'".$ce_formname."','".$ce_name."');this.selectedIndex=0;\">";
            $ausgaben["ce_dropdown"] .= "<option value=\"\">#(tagselect)</option>";

            #$debugging["ausgabe"] .= "<pre>".print_r($cfg["contented"]["tags"],True)."</pre>";

            $cms_old_mode = False;
            foreach( $cfg["contented"]["tags"] as $key => $value ) {

                // js code erstellen
                if ( $ausgaben["js"] == "" ) {
                    $c = "if";
                } else {
                    $c = "else if";
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

                $ausgaben["js"] .= "    ".$c." (st=='".$key."')\n";
                $ausgaben["js"] .= "        st='[".strtoupper($key).$l.$s.$value[4]."[\/".strtoupper($key)."]'\n";



                if ( $value[0] == "" && $cfg["contented"]["debug"] == True ) $value[0] = "T";

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
                                    ,'".strtoupper($key)." [KEY-".$value[1]."]'
                                    ,'".$value[0]."'
                                    ,'".$value[1]."'
                                    ,'noSelect'
                                    ,'[".strtoupper($keyX).$l."'
                                    ,'".$value[4]."'
                                    ,'".$value[5]."[/".strtoupper($keyX)."]'\n";
                $ausgaben["njs"] .= ");\n";



                // buttons bauen
                if ( $value[0] == "T" ) {
                    if ( $cms_old_mode == True ) {
                        #$ausgaben["ce_button"] .= "<a href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\" onMouseOver=\"status='".$value[3]."';return true;\" onMouseOut=\"status='';return true;\"><img src=\"".$defaults["cms-tag"]["path"]."cms-tag-".$key.".png\" alt=\"".$value[3]."\" title=\"".$value[3]."\" width=\"23\" height=\"22\" border=\"0\" /></a>\n ";
                        $ausgaben["ce_button"] .= "<a href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\" onMouseOver=\"status='#(".$key.")';return true;\" onMouseOut=\"status='';return true;\"><img src=\"".$defaults["cms-tag"]["path"]."cms-tag-".$key.".png\" alt=\"#(".$key.")\" title=\"#(".$key.")\" width=\"23\" height=\"22\" border=\"0\" /></a>\n ";
                    } else {
                        $ausgaben["ce_button"] .= "<a class=\"buttag\" href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\" alt=\"#(".$key.")\" title=\"#(".$key.")\" onMouseOver=\"status='#(".$key.")';return true;\" onMouseOut=\"status='';return true;\">".strtoupper($key)."</a>\n ";
                    }
                } elseif ( $value[0] == "B" ) {
                    $ausgaben["ce_bottom_button"] .= "<a class=\"buttag\" href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\" alt=\"#(".$key.")\" title=\"#(".$key.")\" onMouseOver=\"status='#(".$key.")';return true;\" onMouseOut=\"status='';return true;\">".strtoupper($key)."</a>\n ";
                }

                // dropdown bauen
                if ( $value[5] == "" ) {
                    $ausgaben["ce_dropdown"] .= "<option value=\"".$key."\">".strtoupper($key)." #(".$key.")</option>\n";
                }
                #ce_anker
            }

#echo "<pre>".$ausgaben["njs"]."</pre>";

            $ausgaben["ce_dropdown"] .= "</select>";

            // script in seite parsen
            #echo "<pre>".$ausgaben["js"]."</pre>";
            $ausgaben["ce_script"] = parser($cfg["contented"]["tagjs"],"");

            if ( $cms_old_mode == True ) {
                $ausgaben["ce_button"] .= "<input name=\"add[]\" type=\"image\" id=\"image\" value=\"add\" src=\"".$defaults["cms-tag"]["path"]."cms-tag-imgb.png\" title=\"#(add)\" width=\"23\" height=\"22\">";
            } else {
                $ausgaben["ce_button"] .= "<input type=\"submit\" name=\"add[]\" value=\"FILE\" title=\"#(add)\" class=\"butoth\">";
            }

            $ausgaben["ce_upload"] .= "<select style=\"width:95px;font-family:Helvetica, Verdana, Arial, sans-serif;font-size:12px;\" name=\"upload\" onChange=\"submit()\">";
            $ausgaben["ce_upload"] .= "<option value=\"\">#(upload)</option>";
            $ausgaben["ce_upload"] .= "<option value=\"1\">1 #(file)</option>";
            $ausgaben["ce_upload"] .= "<option value=\"2\">2 #(files)</option>";
            $ausgaben["ce_upload"] .= "<option value=\"3\">3 #(files)</option>";
            $ausgaben["ce_upload"] .= "<option value=\"4\">4 #(files)</option>";
            $ausgaben["ce_upload"] .= "<option value=\"5\">5 #(files)</option>";
            $ausgaben["ce_upload"] .= "</select>";

            return $tn;
        }

    }

    ### platz fuer weitere funktionen ###

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
