<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "news page editor/generator";
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

    // konfiguration
    require $pathvars[addonroot]."chaos/news.cfg.php";

    // wie sieht where aus ?
    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "news scheme: ".$environment[news_scheme].$debugging[char];
    switch( $environment[news_scheme] ) {
        case "quartal":
            $news_where_statement_aktuell = "QUARTER(erstellt) = QUARTER(NOW())";
            $news_where_statement_archiv = "QUARTER(erstellt) < QUARTER(NOW())";
            break;

        case "ausgabe":
            $news_where_statement_aktuell = "ausgabe = '".$environment[news_ausgabe]."' order by leitartikel desc, erstellt asc";
            $news_where_statement_archiv = "ausgabe < '".$environment[news_ausgabe]."' order by ausgabe";
            // ausgaben anzeige
            if ( $environment[katid] == "news" && $environment[param][1] > 0 ) {
                $sql = "SELECT ausgabe FROM ". BEITRAG_KOPF ." WHERE beitragid = '".$environment[param][1]."'";
                $result = $db -> query($sql);
                $row = $db -> fetch_row($result);
                if ( $row[0] != $environment[news_ausgabe] ) {
                    $news_where_statement_aktuell = "ausgabe = '".$row[0]."'";
                    $ausgaben[news_ausgabe] = "Archiv ".substr($row[0],5,2)."/".substr($row[0],0,4);
                } else {
                    $ausgaben[news_ausgabe] = "Ausgabe ".substr($row[0],5,2)."/".substr($row[0],0,4);
                }
            } else {
                $ausgaben[news_ausgabe] = "Ausgabe ".substr($environment[news_ausgabe],5,2)."/".substr($environment[news_ausgabe],0,4);
            }
            break;

        default:
    }


        if ( $environment[katid] == "news" || $environment[katid] == $environment[news_uebersicht_katid] ) {
        if ( $environment[subkatid] == "" ) {
            if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(1) beitragid: ".$environment[param][1].$debugging[char];
            if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(2) seite: ".$environment[param][2].$debugging[char];
            if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(3): ".$environment[param][3].$debugging[char];

            if ( $environment[news_uebersicht_aktiv] != "-1" || $environment[katid] != $environment[news_uebersicht_katid] ) {

                // leitartikel suchen
                if ( $environment[param][1] == "" ) {
                    $sql = "SELECT beitragid FROM ". BEITRAG_KOPF ." WHERE leitartikel = '-1' AND ".$news_where_statement_aktuell;
                    $result = $db -> query($sql);
                    $row = $db -> fetch_row($result);
                    $environment[param][1] = $row[0];
                    $environment[param][2] = 1;
                }

                // einzelenen beitrag zusammensetzen
                $sql = "SELECT * FROM ". BEITRAG_INHALT ." where beitragid = '".$environment[param][1]."' AND seite='".$environment[param][2]."'";
                $result = $db -> query($sql);
                $news_content = $db -> fetch_array($result,1);
                $news_content_columns = $db -> show_columns(BEITRAG_INHALT);
                foreach ( $news_content_columns as $key => $rows ) {
                    $fieldname = $news_content_columns[$key]["Field"];
                    $ausgaben[$fieldname] = $news_content[$fieldname];
                    // felder die nicht befuellt wurden, uebernehmen den inhalt der ersten seite
                    if ( $ausgaben[$fieldname] == "" ) {
                        $sql = "SELECT ".$fieldname." FROM ". BEITRAG_INHALT ." where beitragid = '".$environment[param][1]."' AND seite='1'";
                        $result2 = $db -> query($sql);
                        $row = $db -> fetch_row($result2);
                        $ausgaben[$fieldname] = $row[0];
                    }
                    $ausgaben[$fieldname] = nlreplace($ausgaben[$fieldname]);
                    $ausgaben[$fieldname] = tagreplace($ausgaben[$fieldname]);
                    if ( $environment[param][1] != "" && $rechte[news_edit] == -1 ) {

                        $edit_link = $pathvars[virtual]."/news/edit,".$news_content[inhaltid].",".$fieldname.".html";
                        $ausgaben[$fieldname] .= "<a target=\"_top\" href=\"".$edit_link."\">(E)</a>";
                    }
                }
                //  inhalt selector erstellen
                $ausgaben[inh_select] = "";
                $sql = "SELECT seite FROM ". BEITRAG_INHALT ." where beitragid = '".$environment[param][1]."'";
                $result  = $db -> query($sql);
                $num = $db -> num_rows($result);
                $neueseite = $num+1;
                if ( $debugging[html_enable] ) $debugging[ausgabe] .= "seitenanzahl: ".$num.$debugging[char];
                if ( $num > 1 ) {
                    $links = $environment[param][2]-1;
                    $rechts = $environment[param][2]+1;
                    if ( $environment[param][2] != 1 ) {
                        $ausgaben[inh_select] .= "<a href=\"".$pathvars[virtual]."/news,".$environment[param][1].",".$links.".html\"><img src=\"".$pathvars[images]."pf_zur.gif\" border=\"0\"></a> ";
                    } else {
                        $ausgaben[inh_select] .= "&nbsp;&nbsp;";
                    }
                    for ( $i = 1; $i <= $num; $i++ ) {
                        if ( $environment[param][2] == $i ) {
                            $ausgaben[inh_select] .= "<b>".$i."</b> ";
                        } else {
                            $ausgaben[inh_select] .= "<a href=\"".$pathvars[virtual]."/news,".$environment[param][1].",".$i.".html\">".$i."</a> ";
                        }
                    }
                    if ( $environment[param][2] != $num ) {
                        $ausgaben[inh_select] .= "<a href=\"".$pathvars[virtual]."/news,".$environment[param][1].",".$rechts.".html\"><img src=\"".$pathvars[images]."pf_vor.gif\" border=\"0\"></a> ";
                    } else {
                        $ausgaben[inh_select] .= "&nbsp;&nbsp;";
                    }
                }
                if ( $rechte[news_edit] == -1 ) { # seite hinzufuehgen
                    $ausgaben[inh_select] .= "<a href=\"".$pathvars[virtual]."/news/create_page,".$environment[param][1].",".$neueseite.".html\">[New]</a>";
                }
                $mapping[main] = "news.tem1";
            } else {
                // beitrag uebersicht zusammensetzen
                $ausgaben[news_rspalte] = "";
                $sql = "SELECT * FROM ". BEITRAG_KOPF ." WHERE ".$news_where_statement_aktuell;
                $result  = $db -> query($sql);
                while ( $news = $db -> fetch_array($result,1) ) {
                    $ausgaben[news_ueberschrift] = "<a href=\"".$pathvars[virtual]."/news,".$news[beitragid].",1.html\">".$news[beitrag]."</a><br />";
                    $sql = "SELECT ".$environment[news_uebersicht_pre_inh]." FROM ". BEITRAG_INHALT ." where beitragid = '".$news[beitragid]."'";
                    $inh_result  = $db -> query($sql);
                    $row = $db -> fetch_row($inh_result,1);
                    $ausgaben[news_teiltext] = tagremove($row[0]);
                    $ausgaben[news_teiltext] = substr($ausgaben[news_teiltext],0,$environment[news_uebersicht_pre_len])."&nbsp;<a href=\"".$pathvars[virtual]."/news,".$news[beitragid].",1.html\">... mehr?</a><br />";
                    if ( $spalte == "news_lspalte" ) {
                         $spalte = "news_rspalte";
                    } else {
                         $spalte = "news_lspalte";
                    }
                    $ausgaben[$spalte] .= parser("news.preview", "")."<br />";
                }
                $mapping[main] = "news";
                if ( $ausgaben[news_lspalte] == "" ) $ausgaben[news_lspalte] = "Aktuell keine Eintr&auml;ge";
                if ( $ausgaben[news_rspalte] == "" ) $ausgaben[news_rspalte] = "Aktuell keine weiteren Eintr&auml;ge";
            }
        } elseif ( $environment[subkatid] == "archiv" ) {
            $sql = "SELECT * FROM ". BEITRAG_KOPF ." WHERE ".$news_where_statement_archiv;
            $result  = $db -> query($sql);
            while ( $news = $db -> fetch_array($result,1) ) {
                if ( $news[ausgabe] != $newsausgabe ) {
                    if ( $newsausgabe != "" ) $ausgaben[news_lspalte] .= "<br />";
                    $newsausgabe = $news[ausgabe];
                    $ausgaben[news_archiv_liste] .= "<b>Ausgabe ".substr($news[ausgabe],5,2)."/".substr($news[ausgabe],0,4)."</b><br />";
                }
                $ausgaben[news_archiv_liste] .= "<a href=\"".$pathvars[virtual]."/news,".$news[beitragid].",1.html\">".$news[beitrag]."</a>";
                if ( $news[autor] != "" ) {
                    $ausgaben[news_archiv_liste] .= " von ".$news[autor]."<br />";
                } else {
                    $ausgaben[news_archiv_liste] .= "<br />";
                }
            }
            if ( $ausgaben[news_archiv_liste] == "" ) $ausgaben[news_archiv_liste] = "Kein Eintrag";
            $mappinh[main] = "news.archiv";
        } elseif ( $environment[subkatid] == "create" && $rechte[news_edit] == -1 ) {
            $ausgaben[news_create_aktion] = $pathvars[virtual]."/news/create_save.html";
            $mapping[main] = "news.create";
        } elseif ( $environment[subkatid] == "edit" && $rechte[news_edit] == -1 ) {
            if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(1) inhaltid: ".$environment[subparam][1].$debugging[char];
            if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(2) fieldname: ".$environment[subparam][2].$debugging[char];
            $sql = "SELECT ".$environment[subparam][2]." FROM ". BEITRAG_INHALT ." WHERE inhaltid='".$environment[subparam][1]."'";
            $result  = $db -> query($sql);
            $row = $db -> fetch_row($result);
            $ausgaben[news_edit_content] = $row[0];
            $ausgaben[news_edit_aktion] = $pathvars[virtual]."/news/edit_save,".$environment[subparam][1].",".$environment[subparam][2].".html";
            $sql = "SELECT beitragid, seite FROM ". BEITRAG_INHALT ." WHERE inhaltid='".$environment[subparam][1]."'";
            $result  = $db -> query($sql);
            $row = $db -> fetch_row($result);
            $ausgaben[news_edit_abbrechen] = $pathvars[virtual]."/news,".$row[0].",".$row[1].".html";
            $mapping[main] = "news.edit";
        } elseif ( $environment[subkatid] == "create_save" && $rechte[news_edit] == -1 ) {
            $date = date("Y-m-d H:i:s");
            if ( $debugging[html_enable] ) $debugging[ausgabe] .= "date: ".$date.$debugging[char];
            $sql = "INSERT INTO ". BEITRAG_KOPF ." (beitrag, autor, erstellt, leitartikel, ausgabe) VALUES ('".$HTTP_POST_VARS[beitrag]."', '".$HTTP_POST_VARS[autor]."', NOW(), '".$HTTP_POST_VARS[leitartikel]."', '".$HTTP_POST_VARS[ausgabe_jahr]."-".$HTTP_POST_VARS[ausgabe_monat]."')";
            $result  = $db -> query($sql);
            $lastid = $db -> lastid();
            $sql = "INSERT INTO ". BEITRAG_INHALT ." (beitragid, seite, template) VALUES ('".$lastid."', '1', '1')";
            $result  = $db -> query($sql);
            header("Location: ".$pathvars[virtual]."/news,".$lastid.",1.html");
        } elseif ( $environment[subkatid] == "create_page" && $rechte[news_edit] == -1 ) {
            if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(1) beitragid: ".$environment[subparam][1].$debugging[char];
            if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(2) neue seite: ".$environment[subparam][2].$debugging[char];
            $sql = "INSERT INTO ". BEITRAG_INHALT ." (beitragid, seite, template) VALUES ('".$environment[subparam][1]."', '".$environment[subparam][2]."', '1')";
            $result  = $db -> query($sql);
            header("Location: ".$pathvars[virtual]."/news,".$environment[subparam][1].",".$environment[subparam][2].".html".$specialvars[phpsessid]);
        } elseif ( $environment[subkatid] == "edit_save" && $rechte[news_edit] == -1 ) {
            if ( $environment[subparam][1] != "" ) {
                $sql = "UPDATE ". BEITRAG_INHALT ." set ".$environment[subparam][2]."='".$HTTP_POST_VARS[content]."'WHERE inhaltid='".$environment[subparam][1]."'";
                $result  = $db -> query($sql);
            }
            $sql = "SELECT beitragid, seite FROM ". BEITRAG_INHALT ." WHERE inhaltid='".$environment[subparam][1]."'";
            $result  = $db -> query($sql);
            $row = $db -> fetch_row($result);

            if ( $row[0] != "" ) {
                $sql = "UPDATE ". BEITRAG_KOPF ." set geaendert = NOW() WHERE beitragid = '".$row[0]."'";
                $result  = $db -> query($sql);
            }

            header("Location: ".$pathvars[virtual]."/news,".$row[0].",".$row[1].".html");
        }
    }
    // menu bauen
    $sql = "SELECT * FROM ". BEITRAG_KOPF ." WHERE ".$news_where_statement_aktuell;
    $result  = $db -> query($sql);
    // bullet suchen und setzen falls vorhanden
    $image = "bullet.gif";
    $imagefile = $pathvars[fileroot]."images/".$environment[design]."/".$image;
    if ( file_exists($imagefile) ) {
        $imagesize = getimagesize($imagefile);
        $imageurl = $pathvars[images].$image;
        $imagesize = " ".$imagesize[3];
        $bullet = "<img src=\"".$imageurl."\"".$imagesize." alt=\"\"> ";
    }
    while ( $news = $db -> fetch_array($result,1) ) {
        $ausgaben[news_menu] .= $bullet."<a class=\"menu_punkte\" href=\"".$pathvars[virtual]."/news,".$news[beitragid].",1.html\">".$news[beitrag]."</a><br />";
    }
    if ( $ausgaben[news_menu] == "" ) $ausgaben[news_menu] = "Kein Eintrag";
    if ( $rechte[news_edit] == -1 ) $ausgaben[news_menu] .= "<a href=\"".$pathvars[virtual]."/news/create.html\">[New]</a><br />";

    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ++ $script_name ++ ]".$debugging[char];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
