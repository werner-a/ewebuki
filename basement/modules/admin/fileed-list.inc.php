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

    $ausgaben["form_error"] = "";
    if ( $environment["parameter"][1] == "delete" ) {
        foreach ($HTTP_SESSION_VARS["images_memo"] as $key => $value) {
            $sql = "SELECT ffart,fuid FROM site_file WHERE fid =".$value;
            $result = $db -> query($sql);
            $file_art = $db -> fetch_array($result,$nop);
            if ($file_art["fuid"] == $HTTP_SESSION_VARS["uid"]) {
                $sql = "DELETE FROM site_file WHERE fid=".$value;
                if ($file_art["ffart"] == "pdf") {
                    $error  = unlink($pathvars["filebase"]["maindir"].$cfg["file"]["text"]."doc_".$value.".".$file_art["ffart"]);
                    if ($error == "1") {
                        $result = $db -> query($sql);
                    }
                } elseif ($file_art["ffart"] == "zip") {
                    $error  = unlink($pathvars["filebase"]["maindir"].$cfg["file"]["archiv"]."arc_".$value.".".$file_art["ffart"]);
                    if ($error == "1") {
                        $result = $db -> query($sql);
                    }
                } else {
                    $error  = unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["o"]."img_".$value.".".$file_art["ffart"]);
                    $error .= unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["s"]."img_".$value.".".$file_art["ffart"]);
                    $error .= unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["m"]."img_".$value.".".$file_art["ffart"]);
                    $error .= unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["b"]."img_".$value.".".$file_art["ffart"]);
                    $error .= unlink($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."tn_".$value.".".$file_art["ffart"]);
                    if ($error == "11111") {
                        $result = $db -> query($sql);
                    }
                }
                unset ($HTTP_SESSION_VARS["images_memo"][$value]);
            } else {
                $ausgaben["form_error"] .= "Fehler ! Es können nur eigene Dateien gelöscht werden<br>";
                unset ($HTTP_SESSION_VARS["images_memo"][$environment["parameter"][2]]);
            }
        }
        #$HTTP_SESSION_VARS["images_memo"] = "";

    }

    session_register("return");
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Session (return): ".$HTTP_SESSION_VARS["return"].$debugging["char"];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "UID: ".$HTTP_SESSION_VARS["uid"].$debugging["char"];
    $ausgaben["images_aktion"] = $HTTP_SESSION_VARS["return"];


    $position = $environment["parameter"][1]+0;
    $ausgaben["search"] = "";

    #$ausgaben["1"] = "";
    #$ausgaben["2"] = "";
    #$ausgaben["3"] = "";
    #$ausgaben["4"] = "";
    #$ausgaben["5"] = "";

    /*
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
    */

    session_register("art");
    if ( $HTTP_GET_VARS["art"] != "") {
        $art = $HTTP_GET_VARS["art"];
        $HTTP_SESSION_VARS["art"] = $HTTP_GET_VARS["art"];
    } else {
        $art = $HTTP_SESSION_VARS["art"];
    }

    switch ( $art ) {
        case "pdf":
            $ausgaben["4"] = " checked";
            $art = "'pdf'";
            break;
        default:
            $art = "'jpg','png'";
            $ausgaben["5"] = " checked";
    }



    // filter selektoren erstellen
    foreach( $cfg["filter"] as $key => $value ) {
        unset($filter);
        $ausgaben["filter"] .= " ";
        session_register("filter".$key);
        if ( $HTTP_GET_VARS["filter".$key] != "" ) {
            $filter[$HTTP_GET_VARS["filter".$key]] = " selected";
            $HTTP_SESSION_VARS["filter".$key] = $HTTP_GET_VARS["filter".$key];
        } else {
            $filter[$HTTP_SESSION_VARS["filter".$key]] = " selected";
        }
        $ausgaben["filter"]  .= "<select name=\"filter".$key."\" onChange=\"submit()\">";
        foreach ( $value as $num => $label ) {
            $ausgaben["filter"] .= "<option value=\"".$num."\"".$filter[$num].">".$label."</option>";
        }
        $ausgaben["filter"] .= "</select>";
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
    switch ( $HTTP_SESSION_VARS["filter0"] ) {
        case 2:
            $whereb = "";
            $getvalues .= "&what=3";
            break;
        case 1:
            $whereb = " fdid = '".$HTTP_SESSION_VARS["custom"]."' AND";
            $getvalues .= "&what=2";
            break;
        default:
           $whereb = " fuid = '".$HTTP_SESSION_VARS["uid"]."' AND";
           #$getvalues .= "&what=1&art=".$HTTP_GET_VARS["art"];
    }


    switch ( $HTTP_SESSION_VARS["filter1"] ) {
        case 2:
            $whereb .= " ffart in ('zip')";
            $getvalues .= "&art=".$HTTP_SESSION_VARS["filter1"];
            break;
        case 1:
            $whereb .= " ffart in ('pdf')";
            $getvalues .= "&art=".$HTTP_SESSION_VARS["filter1"];
            break;
        default:
            $whereb .= " ffart in ('jpg','png')";
            #$getvalues .= "&what=1&art=".$HTTP_GET_VARS["art"];
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

    $ausgaben["output"] .= "<table width=\"100%\" border=\"0\"><tr><td>";

    if ( $db->num_rows($result) == 0 ) {
        $ausgaben["result"] .= " keine Einträge gefunden.";
    } else {

        // nur erweitern wenn bereits was drin steht
        if ( $ausgaben["result"] ) {
            $ausgaben["result"] .= " folgende Einträge gefunden.";
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
        if ( $HTTP_GET_VARS["search"] != "" ) {
            $anhang = "?".$getvalues;
        } else {
            $anhang = "";
        }

        // daten holen, row spulen
        while ( $data = $db -> fetch_array($result,$nop) ) {
            $i++;
            foreach($data as $key => $value) {
                $$key = $value;
            }

            $environment["parameter"][1] = $environment["parameter"][1] + 0;

            if (is_array($HTTP_SESSION_VARS["images_memo"])) {
                if (in_array($fid,$HTTP_SESSION_VARS["images_memo"])) {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$fid.".html".$anhang."><img width=\"13\" height\"13\" border=\"0\" src=\"".$pathvars["images"]."cms-cb1.png\"></a>";
                } else {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$fid.".html".$anhang."><img width=\"13\" height\"13\" border=\"0\" src=\"".$pathvars["images"]."cms-cb0.png\"></a>";
                }
            } else {
                $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$fid.".html".$anhang."><img src=".$pathvars["images"]."cms-cb0.png border=0></a>";
            }

#            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">BUFFY: ".$ffname."--".$fdesc."</font>".$debugging["char"];
            switch ( $ffart ) {
                case ("zip"):
                    $ausgaben["output"] .="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"";
                    $ausgaben["output"] .= "<tr><td width=\"8\">".$cb."</td><td align=left width=\"30\"><a href=".$cfg["file"]["webdir"].$cfg["file"]["archiv"]."arc_".$fid.".zip><img src=\"".$pathvars["images"]."details.png\" border=0></a></td><td align=left width=\"612\">".$fdesc."</td>";
                    $ausgaben["output"] .= "</table>";
                    break;
                case ("pdf"):
                    $ausgaben["output"] .="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"";
                    $ausgaben["output"] .= "<tr><td width=\"8\">".$cb."</td><td align=left width=\"30\"><a target=\"_blank\" href=".$cfg["file"]["webdir"].$cfg["file"]["text"]."doc_".$fid.".pdf><img src=\"".$pathvars["images"]."details.png\" border=0></a></td><td align=left width=\"612\">".$fdesc."</td>";
                    $ausgaben["output"] .= "</table>";
                    break;
                default:
                    $image = $pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."tn_".$fid.".".$ffart;
                    if ( file_exists($image) ) {
                        $imgsize = getimagesize($image);
                        $imgsize = " ".$imgsize[3];
                    }
                    #$imgsize = str_replace("\"","",$imgsize);
                    #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: IMAGSIZE ".$imgsize."</font>".$debugging["char"];
                    #echo $imgsize;
                    $ausgaben["output"] .="<table border=\"0\" cellspacing=\"1\" cellpading=\"1\" align=\"left\">";
                    $ausgaben["output"] .= "<tr><td height=\"100\" colspan=2 align=\"left\" valign=\"bottom\"><a href=".$cfg["basis"]."/preview,".$fid.",original.html><img border=\"0\" ".$imgsize." src=\"".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."tn_".$data["fid"].".".$data["ffart"]."\"></a></td></tr>";
                    $ausgaben["output"] .= "<tr><td width=\"13\">".$cb."</td>";
                    $ausgaben["output"] .= "<td><a href=\"".$cfg["basis"]."/preview,".$fid.",big.html\">Big</a> ";
                    $ausgaben["output"] .= "<a href=\"".$cfg["basis"]."/preview,".$fid.",medium.html\">Med</a> ";
                    $ausgaben["output"] .= "<a href=\"".$cfg["basis"]."/preview,".$fid.",small.html\">Sma</a></td></tr>";
                    $ausgaben["output"] .= "<tr><td colspan=\"2\" align=\"left\"><img width=\"100\" height=\"1\" src=\"".$pathvars["images"]."pos.png\"></td></tr>";
                    $ausgaben["output"] .= "</table>";
                    $j++;
                    $ja = $j / $cfg["db"]["line"];
                    if ( is_int($ja) ) $ausgaben["output"] .="</td></tr><tr><td>";
                    break;
            }
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
            $ausgaben["filemodify"] = "<a href=\"".$cfg["basis"]."/describe,edit.html\">#(describe)</a>";
            $ausgaben["filedel"]    = "<a href=\"".$cfg["basis"]."/list,delete.html\">#(delete1)</a>";
            break;

        default:
            $ausgaben["filemodify"] = "<a href=\"".$cfg["basis"]."/describe,edit.html\">#(describe)</a>";
            $ausgaben["filedel"]    = "<a href=\"".$cfg["basis"]."/list,delete.html\">#(delete2)</a>";
    }

    if ($HTTP_SESSION_VARS["return"]) {
        $ausgaben["send_image"] = "<a href=".$HTTP_SESSION_VARS["return"]."?referer=".$HTTP_SESSION_VARS["referer"].">#(send_image)</a>";
    } else {
        $ausgaben["send_image"] = "";
    }

    if ( $rechte["redaktion"] == -1 ) {
        #$neu = "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/modify,add.html\">Neu</a>";
    } else {
        #$neu = "";
    }
    $ausgaben["output"] .= "</td></tr></table>";
    $ausgaben["output"] .= parser( "-939795212.list-foot", "");


    // was anzeigen
    #$mapping["main"] = "152366123.list";
    $mapping["navi"] = "leer";
    #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];

    // wohin schicken ?
    $ausgaben["form1_aktion"] = $cfg["basis"]."/list.html";
    $ausgaben["form2_aktion"] = $cfg["basis"]."/select.html";

    /*
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
    */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
