<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
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

    if ( $environment[parameter][1] == "delete" ) {
        foreach ($HTTP_SESSION_VARS["images_memo"] as $key => $value) {
            $sql = "DELETE FROM site_file WHERE fid=".$value;
            #echo $pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["o"]."img_".$value.".jpg";
            $error  = unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["o"]."img_".$value.".jpg");
            $error .= unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["s"]."img_".$value.".jpg");
            $error .= unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["m"]."img_".$value.".jpg");
            $error .= unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["b"]."img_".$value.".jpg");
            $error .= unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."tn_".$value.".jpg");
            if ($error == "11111") {
                $result = $db -> query($sql);
            }
            #echo $pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."img_".$value.".jpg"."<br>";
        }
        $HTTP_SESSION_VARS["images_memo"] = "";

    }
    session_register("return");

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Session (return): ".$HTTP_SESSION_VARS["return"].$debugging["char"];

    $ausgaben["images_aktion"] = $HTTP_SESSION_VARS["return"];


    $position = $environment["parameter"][1]+0;
    $ausgaben["search"] = "";

    $ausgaben["1"] = "";
    $ausgaben["2"] = "";
    $ausgaben["3"] = "";


    session_register("what");
    if ( $HTTP_GET_VARS["what"] != "" ) {
        $what = $HTTP_GET_VARS["what"];
        $HTTP_SESSION_VARS["what"] = $HTTP_GET_VARS["what"];
    } else {
        $what = $HTTP_SESSION_VARS["what"];
    }

    switch ( $what ) {
        case 3:
            $ausgaben["3"] = " checked";
            break;
        case 2:
            $ausgaben["2"] = " checked";
            break;
        default:
            $ausgaben["1"] = " checked";
    }


    // Suche
    if ( $HTTP_GET_VARS["search"] != "" ) {
        $search_value = $HTTP_GET_VARS["search"];
        $ausgaben["search"] = $search_value;
        $ausgaben["result"] = "Ihre Schnellsuche nach \"".$search_value."\" hat ";
        $search_value = explode(" ",$search_value);
        $suche = array("ffname","fdesc","fhit");
        $wherea = "";
        foreach ( $search_value as $value1 ) {
            if ( $value1 != "" ) {
                if ($getvalues == "") $getvalues = "search=";
                $getvalues .= $value1." ";
                foreach ($suche as $value2) {
                    if ($wherea != "") $wherea .= " or ";
                    $wherea .= $value2. " LIKE '%" .$value1."%'";
               }
            }
        }

    }
    // sql erweitern
    switch ( $what ) {
        case 3:
            $whereb = "";
            $getvalues .= "&what=3";
            break;
        case 2:
            $whereb = " fdid = '".$HTTP_SESSION_VARS["custom"]."'";
            $getvalues .= "&what=2";
            break;
        default:
           $whereb = " fuid = '".$HTTP_SESSION_VARS["uid"]."'";

    }
    // gibt es beide
    if ($wherea && $whereb) $trenner = " AND ";
    // ist wherea da, klammern setezn
    if ($wherea) $wherea = "(".$wherea.")";
    // where zusammensetzen
    if ($wherea || $whereb) $where = " WHERE ".$wherea.$trenner.$whereb;


    // Sql Query
    $sql = "SELECT * FROM ".$cfg["db"]["entries"].$where." ORDER by ".$cfg["db"]["order"]." ".$cfg["db"]["sort"];

    // Inhalt Selector erstellen und SQL modifizieren
    $inhalt_selector = inhalt_selector( $sql, $position, $cfg["db"]["rows"], $parameter, 1, 10, $getvalues );
    $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
    $sql = $inhalt_selector[1];
    $ausgaben["gesamt"] = $inhalt_selector[2];

    // head spulen
    $ausgaben["output"] .= parser( "-939795212.list-head", "");

    // query absetzen, variablen bauen
    $result = $db -> query($sql);
    $modify  = array (
      #"edit"        => array("modify,", "Editieren", $cfg["right"]["red"]),
      #"replace"     => array("modify,", "Ersetzen", $cfg["right"]["chf"]),
      #"details"     => array("", "Details", ""),
      #"form"       => array("email,", "E-Mail", ""),
      #"delete"      => array("modify,", "Löschen")
    );
    $imgpath = $pathvars["images"];

    if ( $db->num_rows($result) == 0 ) {
        $ausgaben["result"] .= " keine Einträge gefunden.<br><br>";
    } else {

        // nur erweitern wenn bereits was drin steht
        if ( $ausgaben["result"] ) {
            $ausgaben["result"] .= " folgende Einträge gefunden.<br><br>";
        } else {
            $ausgaben["result"]  = "";
        }

        // array images_memo aufbauen
        session_register("images_memo");
        if ($environment["parameter"][2]) {
            if (is_array($HTTP_SESSION_VARS["images_memo"])) {
                 if (in_array($environment["parameter"][2],$HTTP_SESSION_VARS["images_memo"])) {
                    unset ($HTTP_SESSION_VARS["images_memo"][$environment["parameter"][2]]);
                } else {
                    $HTTP_SESSION_VARS["images_memo"][$environment["parameter"][2]] = $environment["parameter"][2];
                }
            } else {
                    $HTTP_SESSION_VARS["images_memo"][$environment["parameter"][2]] = $environment["parameter"][2];
              }
        }


        // daten holen, row spulen
        while ( $data = $db -> fetch_array($result,$nop) ) {

            $i++;
            foreach($data as $key => $value) {
                $$key = $value;
            }


            // thumbnail anzeigen
            switch ( $ffart ) {
                case jpg:
                    $thumbnail = "<a href=\"".$cfg["basis"]."/preview,".$fid.",medium.html\"><img border=\"0\" src=\"".$cfg["file"]["webdir"].$cfg["file"]["picture"]."/"."thumbnail/tn_".$fid.".".$ffart."\"></a>";
                    #vspace=\"5\" hspace=\"5\"
                    break;
                case png:
                    $thumbnail = "<a href=\"".$cfg["basis"]."/preview,".$fid.".,medium.html\"><img border=\"0\" src=\"".$cfg["file"]["webdir"].$cfg["file"]["picture"]."/"."thumbnail/tn_".$fid.".".$ffart."\"></a>";
                    break;
                case pdf:
                    $thumbnail = "<a href=\"".$cfg["basis"]."/preview,".$fid.",medium.html\"><img hight=\"64\" width\"64\" border=\"0\" src=\"".$pathvars["images"]."pdf.png\"></a>";
                    break;
            }

            $environment["parameter"][1] = $environment["parameter"][1] + 0;

            // zu lange filenamen kuerzen
            if ( strlen($ffname) > 10 ) {
                $ffname = substr($ffname,0,10)."...";
            }
            if (is_array($HTTP_SESSION_VARS[images_memo])) {
                if (in_array($fid,$HTTP_SESSION_VARS[images_memo])) {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$fid.".html><img src=".$pathvars["images"]."cms-cb1.png border=0></a>";
                } else {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$fid.".html><img src=".$pathvars["images"]."cms-cb0.png border=0></a>";
                }
            } else {
                $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$fid.".html><img src=".$pathvars["images"]."cms-cb0.png border=0></a>";
            }

            // datum richtig setzen
            #$ierstellt = substr($ierstellt,8,2).".".substr($ierstellt,5,2).".".substr($ierstellt,0,4);

            // mehr link
            #$mehr = $cfg["basis"]."/".$cfg["ebene"]["zwei"]."/details,".$data[$cfg["db"]["key"]].".html";
            #$environment["parameter"][1] = $environment["parameter"][1] + 0;
            #$mehr = $cfg["basis"]."/list,".$environment["parameter"][1].",".$fid.".html";

            // aktionen erstellen
            $aktion = "";
            foreach($modify as $name => $value) {
                if ( $rechte[$value[2]] == -1 || $value[2] == "" ) {
                    $aktion .= "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/".$value[0].$name.",".$data[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath."/".$name.".gif\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                } else {
                    $aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                }
            }
            $feld = "feld".$i;
            $$feld = parser( "-939795212.list-feld", "");

            if ( $i == 6 ) {
                $i = 0;
                $ausgaben["output"] .= parser( "-939795212.list-row", "");
            }
        }
        if ( $i << 6 ) {
            while ( $i < 6 ) {
                $i++;
                $feld = "feld".$i;
                $$feld = "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"96\" height=\"96\">";
            }
            $ausgaben["output"] .= parser( "-939795212.list-row", "");
        }
    }

    // foot spulen
    $anzahl = count($HTTP_SESSION_VARS["images_memo"]);
    switch ($anzahl) {
        case 0:
            $ausgaben["filemodify"] = "";
            $ausgaben["filedel"]    = "";
            break;

        case 1:
            $ausgaben["filemodify"] = "<a href=\"".$cfg["basis"]."/describe,edit.html\">Datei editieren</a>";
            $ausgaben["filedel"]    = "<a href=\"".$cfg["basis"]."/list,delete.html\">ausgewählte Datei löschen</a>";
            break;

        default:
            $ausgaben["filemodify"] = "<a href=\"".$cfg["basis"]."/describe,edit.html\">Dateien editieren</a>";
            $ausgaben["filedel"]    = "<a href=\"".$cfg["basis"]."/list,delete.html\">ausgewählte Dateien löschen</a>";
    }

    if ($HTTP_SESSION_VARS["return"]) {
        $ausgaben["send_image"] = "<a href=".$HTTP_SESSION_VARS["return"]."?referer=".$HTTP_SESSION_VARS["referer"].">Zum Beitrag</a>";
    } else {
        $ausgaben["send_image"] = "";
    }

    if ( $rechte["redaktion"] == -1 ) {
        #$neu = "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/modify,add.html\">Neu</a>";
    } else {
        #$neu = "";
    }
    $ausgaben["output"] .= parser( "-939795212.list-foot", "");

    // was anzeigen
    #$mapping["main"] = "152366123.list";
    $mapping["navi"] = "leer";
    #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];

    // wohin schicken ?
    $ausgaben["form_aktion"] = $cfg["basis"]."/list.html";

    // upload auswahl
    #$ausgaben["upload"] .="<br>";
    $ausgaben["upload"] .="<form action=\"".$cfg["basis"]."/select.html\" method=\"get\" enctype=\"multipart/form-data\">";
    $ausgaben["upload"] .="<select class=\"dropdown\" name=\"anzahl\">";
    $ausgaben["upload"] .="<option value=\"1\">1 Datei</option>";
    $ausgaben["upload"] .="<option value=\"2\">2 Dateien</option>";
    $ausgaben["upload"] .="<option value=\"3\">3 Dateien</option>";
    $ausgaben["upload"] .="<option value=\"4\">4 Dateien</option>";
    $ausgaben["upload"] .="<option value=\"5\">5 Dateien</option>";
    $ausgaben["upload"] .="</select>";
    $ausgaben["upload"] .="<input type=\"submit\" value=\"Weiter\">";
    $ausgaben["upload"] .="</form>";
    #$ausgaben["upload"] .= "<a href=\"".$cfg["basis"]."/fbrowse.html\">Browser</a>";


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
