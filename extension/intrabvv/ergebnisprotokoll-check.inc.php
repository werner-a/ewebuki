<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "$Id$";
  $Script_desc = "protokoll migration";
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


    if ( substr($_FILES["upload-protokoll"]["name"],-4,1) == "." ) {
        $dateiendung = substr($_FILES["upload-protokoll"]["name"],-3,3);
    } else {
        $dateiendung = substr($_FILES["upload-protokoll"]["name"],-4,4);
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "file extension: ".$dateiendung.$debugging["char"];

    // zulaessige endung fuer protokoll
    $valid = array("txt");

    if ( !in_array($dateiendung, $valid) ) {
        $ausgaben["output"] .= "Dateiformat ungültig, Endung .txt erforderlich.<br>";
    } else {
        if ( $debugging[html_enable] ) $debugging[ausgabe] .= "filetype: ".$_FILES["upload-protokoll"]["type"].$debugging[char];
        if ( $debugging[html_enable] ) $debugging[ausgabe] .= "filename: ".$_FILES["upload-protokoll"]["tmp_name"].$debugging[char];

        $file =  $_FILES["upload-protokoll"]["tmp_name"];



        if ( file_exists($file) ) {
            $handle = fopen($file, r);
            $sitzungs_protokoll = sipro_read($handle);

            $fieldarray = array( "thema", "datum", "teilnehmer" );

            foreach($sitzungs_protokoll as $name => $value) {
                if ( in_array($name,$fieldarray) ) {
                    if ( $name == "teilnehmer" ) {
                        $value = serialize($sitzungs_protokoll["teilnehmer"]);
                    }
                    if ( $sqla != "" ) $sqla .= ",";
                    // feldnamen beginnen mit p in datenbank
                    $sqla .= " p".$name;
                    if ( $sqlb != "" ) $sqlb .= ",";
                    $sqlb .= " '".$value."'";
                }
            }

            $sql = "INSERT INTO ".$cfg["db"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
            $result  = $db -> query($sql);
            echo "sql: ".$sql."<br>";

            if ($result == 1) {
                $pid = $db -> lastid();

                $i = 1;
                for ($i; $i <= count($sitzungs_protokoll["top_punkte"])-1; $i++) {

                    echo $i.count($sitzungs_protokoll["top_punkte"])."<br>";
                    $sql = "INSERT INTO db_protokolle_top (pid, ptoppunkt, ptoptext) VALUES ('".$pid."', '".$sitzungs_protokoll["top_punkte"][$i]."', '".$sitzungs_protokoll["top_texte"][$i]."')";
                    $result  = $db -> query($sql);
                    echo "sql: ".$sql."<br>";
                }

                $i = 0;
                for ($i; $i <= count($sitzungs_protokoll["todo"])-1; $i++) {

                    echo $i.count($sitzungs_protokoll["todo"])."<br>";
                    $sql = "INSERT INTO db_protokolle_todo (pid, ptdwas, ptdwo, ptdwer, ptdwann) VALUES ('".$pid."', '".$sitzungs_protokoll["todo"][$i][0]."', '".$sitzungs_protokoll["todo"][$i][1]."', '".$sitzungs_protokoll["todo"][$i][2]."', '".$sitzungs_protokoll["todo"][$i][3]."')";
                    $result  = $db -> query($sql);
                    echo "sql: ".$sql."<br>";
                }
            }




            // thema
            if ( $sitzungs_protokoll["thema"] != "" ) {
                $thema = $sitzungs_protokoll["thema"];
            } else {
                $ausgaben["output"] .= "Dateiformat ungültig: Thema nicht lesbar<br>";
            }
            // datum
            if ( $sitzungs_protokoll["datum"] != "" ) {
                $datum = $sitzungs_protokoll["datum"];
            } else {
                $ausgaben["output"] .= "Dateiformat ungültig: Datum nicht lesbar<br>";
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


            $ausgaben["output"] .= "<br><br><a href=\"display,".$environment["parameter"][1].",".$environment["parameter"][2].",download.html\">HTML herunterladen</a>";

        } else {
            $ausgaben["output"] = "Datei <b>".$file."</b> existiert nicht.";
        }
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function sipro_read($handle) {
        $sitzungs_protokoll["top_punkte"][0] = "";
        while (!feof($handle)) {
            $i++;
            $buffer = fgets($handle, 4096);

            if ( !strstr($buffer, "#") ) {

                $buffer = trim($buffer);

                if ( strstr($buffer, "THEMA:") ) {
                    $grp = 1;
                    # echo "---grp ".$grp."---------------<br>";
                } elseif ( strstr($buffer, "DATUM:") ) {
                    $grp = 2;
                    # echo "---grp ".$grp."---------------<br>";
                } elseif ( strstr($buffer, "TEILNEHMER:") ) {
                    $grp = 3;
                    # echo "---grp ".$grp."---------------<br>";
                } elseif ( strstr($buffer, "TOP:") ) {
                    $grp = 4;
                    $grpcount[4] = 0;
                    $beg = strpos($buffer, ":")+1;
                    $end = strpos($buffer, " ")-4;
                    $top = substr($buffer, $beg, $end);
                    # echo "---grp ".$grp.".".$top."---------------<br>";
                } elseif ( strstr($buffer, "TODO") ) {
                    $grp = 5;
                    $kein_todo = substr($buffer, 6, 5);
                    # echo "---grp ".$grp."---------------<br>";
                }

                switch ($grp) {
                    case 1:
                        $beg = strpos($buffer, ":");
                        if ( $buffer != "" ) {
                            $sitzungs_protokoll["thema"] .= substr($buffer, $beg+2);
                            #$sitzungs_protokoll["thema"] .= substr($buffer, $beg+2)."<br>";
                        }
                        break;
                    case 2:
                        $beg = strpos($buffer, ":");
                        if ( $buffer != "" ) {
                            $sitzungs_protokoll["datum"] .= substr($buffer, $beg+2);
                            #$sitzungs_protokoll["datum"] .= substr($buffer, $beg+2)."<br>";
                        }
                        break;
                    case 3:
                        $grpcount[3]++;
                        if ( $grpcount[3] > 1 ) {
                            $felder = explode(";", $buffer);
                            if ( $felder[0] != "" ) {
                                $sitzungs_protokoll["teilnehmer"][] = $felder;
                            }
                        }
                        break;
                    case 4:
                        $grpcount[4]++;
                        // ueberschrift
                        if ( $grpcount[4] == 1 ) {
                            /*
                            $beg = strpos($buffer, ":");
                            $ueberschrift = substr($buffer, $beg+2);
                            */
                            $beg = strpos($buffer, $top);
                            $ueberschrift = trim(substr($buffer,$beg+strlen($top)));
                            $sitzungs_protokoll["top_punkte"][] = $ueberschrift;
                        // erste zeile text
                        } elseif ( $grpcount[4] == 2 ) {
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
                    case 5:
                        $grpcount[5]++;
                        if ( $grpcount[5] > 1 ) {
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



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
