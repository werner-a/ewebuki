<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "$Id$";
  $Script_desc = "ergebnisprotokoll";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

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

    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ** $script_name ** ]".$debugging[char];


    if ( strstr($environment[kategorie], "display") ) {


        // filenamen bauen
        if ( $environment["parameter"][1] != "" ) $unterdir = $environment["parameter"][1]."/";
        $file = $path.$unterdir.$environment["parameter"][2];
        if ( $debugging[html_enable] ) $debugging[ausgabe] .= "filename: ".$file.$debugging[char];

        if ( file_exists($file) ) {
            #$handle = fopen($path."20020930-technik-protokoll.txt", r);
            $handle = fopen($file, r);
            $sitzungs_protokoll = sipro_read($handle);

            // thema
            if ( $sitzungs_protokoll["thema"] != "" ) {
                $thema = $sitzungs_protokoll["thema"];
            } else {
                $ausgaben["output"] .= "Dateiformat ungültig: Thema nicht lesbar<br>";
            }

            // teilnehmer
            if ( is_array($sitzungs_protokoll["teilnehmer"]) ) {
                $teilnehmer = "<table cellpadding=\"0\" cellspacing=\"0\">";
                foreach( $sitzungs_protokoll["teilnehmer"] as $value ) {
                    $teilnehmer .= "<tr><td>".$value[0]."&nbsp;&nbsp;</td><td>".$value[1]."</td></tr>";
                }
                $teilnehmer .= "</table>";
            } else {
                $ausgaben["output"] .= "Dateiformat ungültig: Teilnehmer nicht lesbar<br>";
            }

            if ( is_array($sitzungs_protokoll["top_punkte"]) ) {
                // tagesordnung
                $tagesordnung = "<table cellpadding=\"0\" cellspacing=\"0\">";
                $i = 0;
                foreach( $sitzungs_protokoll["top_punkte"] as $value ) {
                    $i++;
                    $tagesordnung .= "<tr><td valign=\"top\" nowrap><a href=\"#".$i."\">TOP ".$i.":</a></td><td>&nbsp;&nbsp;</td><td><a href=\"#".$i."\">".$value."</a></td></tr>";
                }
                $i++;
                #$tagesordnung .= "<tr><td><a href=\"#".$i."\">TOP: ".$i."</a></td><td>&nbsp;&nbsp;</td><td><a href=\"#".$i."\">TODO Liste</a></td></tr>";
                $tagesordnung .= "<tr><td colspan=\"3\">&nbsp;</td></tr>";
                if ( $sitzungs_protokoll["todo"] != "keine" ) $tagesordnung .= "<tr><td colspan=\"3\"><a href=\"#".$i."\">TODO Liste</a></td></tr>";
                $tagesordnung .= "</table>";
                $ausgaben["output"] .= parser( "protokoll-1", "");

                // top
                foreach( $sitzungs_protokoll["top_punkte"] as $key => $value ) {
                    $top = $key+1;
                    $punkt = "<a name=\"".$top."\"></a>TOP ".$top.": ".$value;
                    while ( strrchr($sitzungs_protokoll["top_texte"][$top],"<br>") == "<br>" ) {
                        $sitzungs_protokoll["top_texte"][$top] = substr($sitzungs_protokoll["top_texte"][$top],0,strrpos($sitzungs_protokoll["top_texte"][$top],"<"));
                    }
                    $text  = $sitzungs_protokoll["top_texte"][$top];
                    $ausgaben["output"] .= parser( "protokoll-2", "");
                }
            } else {
                $ausgaben["output"] .= "Dateiformat ungültig: TOP Bereiche können nicht gelesen werden<br>";
            }


            // todo
            if ( is_array($sitzungs_protokoll["todo"]) ) {
                $todo  = "<table cellpadding=\"2\" cellspacing=\"0\" border=\"1\">";
                $todo .= "<tr><td nowrap><a name=\"".$i."\"><b>Nr.</p></td><td><b>Aufgabe</b></td><td><b>JF/TOP</b></td><td><b>Wer</b><td align=\"right\"><b>Bis wann</b></td><tr>";
                $i = 0;
                foreach( $sitzungs_protokoll["todo"] as $value ) {
                    $i++;
                    if ( $value[1] == "" ) $value[1] = "&nbsp;"; // 2. was
                    if ( $value[2] == "" ) $value[2] = "&nbsp;"; // 3. referenz
                    if ( $value[3] == "" ) $value[3] = "&nbsp;"; // 4. wer
                    if ( $value[4] == "" ) $value[4] = "&nbsp;"; // 5. bis wann
                    $todo .= "<tr><td valign=\"top\" align=\"right\">".$i."</td><td valign=\"top\">".$value[0]."</td><td valign=\"top\" nowrap>".$value[1]."</td><td valign=\"top\">".$value[2]."</td><td valign=\"top\" align=\"right\">".$value[3]."</td><tr>";
                }
                $todo .= "</table>";
                $ausgaben["output"] .= parser( "protokoll-3", "");
            } elseif ( $sitzungs_protokoll["todo"] == "keine" ) {
                # kein todo vorhanden.
            } else {
                $ausgaben["output"] .= "Dateiformat ungültig: TODO kann nicht gelesen werden";
            }

            if ( $environment["parameter"][3] == "download" ) {
                // filenamen bauen
                if ( $environment["parameter"][1] != "" ) $environment["parameter"][1] = $environment["parameter"][1]."/";
                $file = $path.$environment["parameter"][1].$environment["parameter"][2];
                if ( $debugging[html_enable] ) $debugging[ausgabe] .= "filename: ".$file.$debugging[char];

                // Passenden Datentyp erzeugen.
                header("Content-Type: application/octet-stream");

                // Passenden Dateinamen im Download-Requester vorgeben,
                // z. B. den Original-Dateinamen
                $basename = basename($environment["parameter"][2],".txt");
                header("Content-Disposition: attachment; filename=\"".$basename.".html\"");

                // Datei ausgeben.
                #readfile($file);
                $output = $ausgaben["output"];
                echo parser( "protokoll-0", "");
                exit();
            }

            $ausgaben["output"] .= "<br><br><a href=\"display,".$environment["parameter"][1].",".$environment["parameter"][2].",download.html\">HTML herunterladen</a>";

        } else {
            $ausgaben["output"] = "Datei <b>".$file."</b> existiert nicht.";
        }

    #} elseif ( $environment["kategorie"] == "list" || $environment["kategorie"] == $environment["name"] ) {
    } elseif ( $environment["kategorie"] == "list" ) {
        if ( @chdir($path) ) {

            $handle=opendir($path.$environment["parameter"][1]);
            $ausgaben["output"] = "";
            while ( $file = readdir ($handle) ) {

                if ( $file != "." ) {
                    if ( substr($file, -4) == ".txt" ) {
                        $ausgaben["output"] .= "<a href=\"protokoll/display,".$environment["parameter"][1].",".$file.".html\">".$file."</a><br>";
                    } else {
                        if ( is_dir($file) ) {
                            if ( $file == ".." && $environment["parameter"][1] != "" ) {
                                $ausgaben["output"] .= "&lt;dir&gt; <a href=\"protokoll.html\">".$file."</a><br>";
                            } elseif ( $file != ".." ) {
                                $ausgaben["output"] .= "&lt;dir&gt; <a href=\"protokoll,".$file.".html\">".$file."</a><br>";
                            }
                        } else {
                            $ausgaben["output"] .= $file."</br>";
                        }
                    }
                }
            }
            closedir($handle);





            // neue liste der protokolle
            // wa 0110

            $position = $environment["parameter"][1]+0;

            $form_values = $HTTP_POST_VARS;

            // form options holen

            #$form_options = form_options(crc32($environment[ebene]).".".$environment[kategorie]);
            $form_options = form_options("1943315524.list");

            // form elememte bauen
            $element = form_elements( $cfg["db"]["entries"], $HTTP_POST_VARS );


            // form elememt itext umbauen
            $form_fields = array("ptext"
                                 );
            foreach ($form_fields as $key){
                ( $form_options[$key]["fsize"] > 0 ) ? $size = " size=\"".$form_options[$key]["fsize"]."\"" : $size = "";
                ( $form_options[$key]["fclass"] != "" ) ? $class = " class=\"".$form_options[$key]["fclass"]."\"" : $class = " class=\"".$form_defaults["class"]["textfield"]."\"";
                ( $form_options[$key]["fstyle"] != "" ) ? $style = " style=\"".$form_options[$key]["fstyle"]."\"" : $style = "";
                ( $form_values[$key] != "" ) ? $value = " value=\"".$form_values[$key]."\"" : $value = "";

                $element[$key] = "<input type=\"text\"".$size.$class.$style." name=\"".$key."\" ".$value.">\n";
            }

            //datumsfeld von und bis erstellen und leeren
            $element["pvon"] = $element["pdatum"];
            $element["pbis"] = $element["pdatum"];

            $pos=strrpos($element["pvon"], "value");
            $element["pvon"] = substr($element["pvon"], 0, $pos-1).">";
            $pos=strrpos($element["pbis"], "value");
            $element["pbis"] = substr($element["pbis"], 0, $pos-1).">";


            #$ausgaben["form_aktion"] = $cfg["basis"]."/".$cfg["ebene"]["zwei"]."/list,".$position.",esearch.html";
            $ausgaben["form_aktion"] = $cfg["basis"].",0,esearch.html";


            //  Suche
            // ***
            #if ( $environment[parameter][2] == "esearch" ) {
            if ( $environment[parameter][2] == "esearch" || $HTTP_GET_VARS["esearch"] == true) {
                    if ( $HTTP_GET_VARS["esearch"] == true ) {
                        $kick = array( "image_x", "image_y", "esearch");
                    } else {
                        $kick = array( "image_x", "image_y");
                    }
                    foreach ($HTTP_GET_VARS as $key => $value) {
                        if ( !in_array($key,$kick)  ) {
                            if ( $value != "" ) {
                                if ($getvalues != "") $getvalues .= "&";
                                // sql WHERE bauen
                                if ( $where == "" ) $where = " WHERE ";
                                if ( $where2 != "" ) $where2 .= " AND ";
                                if ($key == "pvon") {
                                    $getval = substr($value,0,10);
                                    $convert = $value;
                                    $value = substr($convert,6,4)."-".substr($convert,3,2)."-".substr($convert,0,2)." 00:00:00";
                                    $where2 .= "pdatum >= '".$value."'";
                                } elseif ($key == "pbis") {
                                    $getval = substr($value,0,10);
                                    $convert = $value;
                                    $value = substr($convert,6,4)."-".substr($convert,3,2)."-".substr($convert,0,2)." 23:59:59";
                                    $where2 .= "pdatum <= '".$value."'";
                                #} elseif ($key == "ptext") {
                                #    $where2 .= $key." LIKE '%".$value."%'";
                                #    $getval = $value;
                                } else {
                                    $where2 .= $key." LIKE '%".$value."%'";
                                    $getval = $value;
                                }

                                $getvalues .= $key."=".$getval;
                                // suchergebnis ausgabe bauen
                                #if ( $suchergebnis !="" ) $suchergebnis .= " und ";
                                #$suchergebnis .= "\"".$value."\"";
                            }
                        }
                    }
                    $getvalues .= "&esearch=true";
                    if ( $where != "" ) {
                        $ausgaben["result"] = "Ihre Erweiterte Suche nach ".$suchergebnis." hat";
                        // where zusammenbauen
                        $where .= $where2;
                    }
            }

            // +++
            // Suche

            // Sql Query
            $sql = "SELECT * FROM ".$cfg["db"]["entries"].$where." ORDER by ".$cfg["db"]["order"]." DESC";
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">HIER:".$sql."</font>".$debugging["char"];
            #echo $sql;
            // Inhalt Selector erstellen und SQL modifizieren
            $inhalt_selector = inhalt_selector( $sql, $position, $cfg["db"]["rows"], $parameter, 1, 10, $getvalues );
            $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
            $sql = $inhalt_selector[1];
            $ausgaben["gesamt"] = $inhalt_selector[2];

            // head spulen
            $ausgaben["output"] .= "<br>PROTOKOLLE NEU: <br>";
            $ausgaben["output"] .= parser( "protokoll.list-head", "");

            // Daten holen und ausgeben
            $result = $db -> query($sql);

            while ( $data = $db -> fetch_array($result,$nop) ) {
                foreach($data as $key => $value) {
                    $$key = $value;
                }
                $mehr = $cfg["basis"]."/display,2003-protokolle,".$data["pname"].".html";

                // anlagen holen
                $sql = "SELECT * FROM db_protokolle_anl where pid = '".$data["pid"]."'";
                $result2 = $db -> query($sql);
                $i = 1;
                while ( $data2 = $db -> fetch_array($result2,$nop) ) {
                    if ( $i == 1 ) {
                        $break = "";
                    } else {
                        $break = "<br>";
                    }
                    $anlage .= $break."Anlage ".$i.": ".$data2["paanlage"];
                $i++;
                }
                // row spulen
                $ausgaben["output"] .= parser( "protokoll.list-row", "");

            }

            // foot spulen
            $ausgaben["output"] .= parser( "protokoll.list-foot", "");
            // wa 0110




        } else {
            $ausgaben["output"] = "Verzeichnis <b>".$path."</b> existiert nicht.";
        }
    } elseif ( $environment["parameter"][1] == "select") {

        $ausgaben["output"] .="<form action=\"".$cfg["basis"]."/describe,check.html\" method=\"post\" enctype=\"multipart/form-data\">";
        $ausgaben["output"] .="<input type=\"file\" name=\"upload".$i."\"><br>";
        $ausgaben["output"] .="<input type=\"submit\" value=\"los\">";
        $ausgaben["output"] .="</form>";
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function sipro_read($handle) {
        while (!feof($handle)) {
            $i++;
            $buffer = fgets($handle, 4096);

            if ( !strstr($buffer, "#") ) {

                $buffer = trim($buffer);

                if ( strstr($buffer, "THEMA:") ) {
                    $grp = 1;
                    # echo "---grp ".$grp."---------------<br>";
                } elseif ( strstr($buffer, "TEILNEHMER:") ) {
                    $grp = 2;
                    # echo "---grp ".$grp."---------------<br>";
                } elseif ( strstr($buffer, "TOP:") ) {
                    $grp = 3;
                    $grpcount[3] = 0;
                    $beg = strpos($buffer, ":")+1;
                    $end = strpos($buffer, " ")-4;
                    $top = substr($buffer, $beg, $end);
                    # echo "---grp ".$grp.".".$top."---------------<br>";
                } elseif ( strstr($buffer, "TODO") ) {
                    $grp = 4;
                    $kein_todo = substr($buffer, 6, 5);
                    # echo "---grp ".$grp."---------------<br>";
                }

                switch ($grp) {
                    case 1:
                        $beg = strpos($buffer, ":");
                        if ( $buffer != "" ) {
                            $sitzungs_protokoll["thema"] .= substr($buffer, $beg+2)."<br>";
                        }
                        break;
                    case 2:
                        $grpcount[2]++;
                        if ( $grpcount[2] > 1 ) {
                            $felder = explode(";", $buffer);
                            if ( $felder[0] != "" ) {
                                $sitzungs_protokoll["teilnehmer"][] = $felder;
                            }
                        }
                        break;
                    case 3:
                        $grpcount[3]++;
                        // ueberschrift
                        if ( $grpcount[3] == 1 ) {
                            /*
                            $beg = strpos($buffer, ":");
                            $ueberschrift = substr($buffer, $beg+2);
                            */
                            $beg = strpos($buffer, $top);
                            $ueberschrift = trim(substr($buffer,$beg+strlen($top)));
                            $sitzungs_protokoll["top_punkte"][] = $ueberschrift;
                        // erste zeile text
                        } elseif ( $grpcount[3] == 2 ) {
                            if ( strlen($buffer) == 0 ) {
                                $sitzungs_protokoll["top_texte"][$top] = "";
                            } else {
                                $sitzungs_protokoll["top_texte"][$top] .= $buffer."<br>";
                            }
                        // rest des text
                        } else {
                            if ( substr($buffer, 0, 2) == "- " && $li == "" ) {
                                $li = -1;
                                if  ( strrchr($sitzungs_protokoll["top_texte"][$top],"<br>") ) {
                                    $laenge = strlen($sitzungs_protokoll["top_texte"][$top]) - 4;
                                    $sitzungs_protokoll["top_texte"][$top] = substr($sitzungs_protokoll["top_texte"][$top], 0, $laenge);
                                }
                                $sitzungs_protokoll["top_texte"][$top] .= "<ul>";
                            }

                            if ( substr($buffer, 0, 2) != "- " && $li == -1 ) {
                                $sitzungs_protokoll["top_texte"][$top] .= "</ul>";
                                $li = "";
                            }

                            if ( substr($buffer, 0, 3) == "UP:" ) {
                                if  ( strrchr($sitzungs_protokoll["top_texte"][$top],"<br>") ) {
                                    $laenge = strlen($sitzungs_protokoll["top_texte"][$top]) - 4;
                                    $sitzungs_protokoll["top_texte"][$top] = substr($sitzungs_protokoll["top_texte"][$top], 0, $laenge);
                                }
                                $sitzungs_protokoll["top_texte"][$top] .= "<b>".substr($buffer,4)."</b><br>";
                            } elseif ( $li == "-1" && $buffer != "" ) {
                                $sitzungs_protokoll["top_texte"][$top] .= "<li>".substr($buffer, 2)."</li>";
                            } else {
                                $sitzungs_protokoll["top_texte"][$top] .= $buffer."<br>";
                            }
                        }
                        break;
                    case 4:
                        $grpcount[4]++;
                        if ( $grpcount[4] > 1 ) {
                            $felder = explode(";", $buffer);
                            if ( $felder[0] != "" ) {
                                $sitzungs_protokoll["todo"][] = $felder;
                            }
                        }
                        if ( $kein_todo == "keine" ) $sitzungs_protokoll["todo"] = "keine";
                        break;
                }
            }
        }
        fclose($handle);
        return $sitzungs_protokoll;
    }



    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ++ $script_name ++ ]".$debugging[char];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
