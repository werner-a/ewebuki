<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "fileed - list funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

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

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich
        // ***

        $ausgaben["search"] = "";
        $position = $environment["parameter"][1]+0;



        // file_memo verwalten (neu)
        if ( $environment["parameter"][2] ) {

            $key = $environment["parameter"][2];
            $wert = $environment["parameter"][2];

            if ( is_array($_SESSION["file_memo"]) ) {
                if ( in_array($key, $_SESSION["file_memo"] ) ) {
                    unset ( $_SESSION["file_memo"][$key] );
                } else {
                    $_SESSION["file_memo"][$key] = $wert;
                }
            } else {
                $_SESSION["file_memo"][$key] = $wert;
            }
        }
        $debugging["ausgabe"] .= "<pre>".print_r($_SESSION,True)."</pre>";


        // filter selektoren erstellen
        foreach( $cfg["filter"] as $key => $value ) {
            unset($filter);
            $ausgaben["filter"] .= " ";
            if ( $HTTP_GET_VARS["filter".$key] != "" ) {
                $filter[$HTTP_GET_VARS["filter".$key]] = " selected";
                $_SESSION["filter".$key] = $HTTP_GET_VARS["filter".$key];
            } else {
                $filter[$_SESSION["filter".$key]] = " selected";
            }
            $ausgaben["filter"]  .= "<select name=\"filter".$key."\" onChange=\"submit()\">";
            foreach ( $value as $num => $label ) {
                $ausgaben["filter"] .= "<option value=\"".$num."\"".$filter[$num].">".$label."</option>";
            }
            $ausgaben["filter"] .= "</select>";
        }




        // content editor link erstellen (neu)
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "SESSION (cms_last_edit): ".$_SESSION["cms_last_edit"].$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "SESSION (cms_last_referer): ".$_SESSION["cms_last_referer"].$debugging["char"];
        if ( isset($_SESSION["cms_last_edit"]) ) {
            // abrechen im cms editor soll zur ursrungseite springen und nicht in den fileed
            $_SESSION["page"] = $_SESSION["cms_last_referer"];
            $ausgaben["cmslink"] = "<a href=\"".$_SESSION["cms_last_edit"]."?referer=".$_SESSION["cms_last_referer"]."\">#(cmslink)</a>";
        } else {
            $ausgaben["cmslink"] = "";
        }

        // bearbeiten- und loeschen link erstellen (neu)
        if ( count($_SESSION["file_memo"]) >= 1 ) {
            $ausgaben["fileedit"] = "<a href=\"".$cfg["basis"]."/edit.html\">#(fileedit)</a>";
            $ausgaben["filedelete"] = "<a href=\"".$cfg["basis"]."/delete.html\">#(filedelete)</a>";
        } else {
            $ausgaben["fileedit"] = "";
            $ausgaben["filedelete"] = "";
        }





        // art der anzeige
        if ( $HTTP_GET_VARS["art"] != "") {
            $art = $HTTP_GET_VARS["art"];
            $_SESSION["art"] = $HTTP_GET_VARS["art"];
        } else {
            $art = $_SESSION["art"];
        }

        switch ( $art ) {
            case "pdf": case "pdt": case "ods": case "odp":
                $ausgaben["4"] = " checked";
                $art = "'pdf','pdt','ods','odp'";
                break;
            default:
                $art = "'gif', 'jpg','png'";
                $ausgaben["5"] = " checked";
        }



        // Suche
        if ( $HTTP_GET_VARS["search"] != "" ) {
            $anhang = "?".$getvalues;
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
        switch ( $_SESSION["filter0"] ) {
            case 2:
                $whereb = "";
                $getvalues .= "&what=3";
                break;
            case 1:
                $whereb = " fdid = '".$_SESSION["custom"]."' AND";
                $getvalues .= "&what=2";
                break;
            default:
            $whereb = " fuid = '".$_SESSION["uid"]."' AND";
        }

        switch ( $_SESSION["filter1"] ) {
            case 2:
                $whereb .= " ffart in ('zip','bz2','gz')";
                $getvalues .= "&art=".$_SESSION["filter1"];
                $hidedata["other"] = array();
                break;
            case 1:
                $whereb .= " ffart in ('pdf','odt','ods','odp')";
                $getvalues .= "&art=".$_SESSION["filter1"];
                $hidedata["other"] = array();
                break;
            default:
                $whereb .= " ffart in ('gif','jpg','png')";
                $hidedata["images"] = array();


        }
        // gibt es beide
        if ($wherea && $whereb) $trenner = " AND ";
        // ist wherea da, klammern setzen
        if ($wherea) $wherea = "(".$wherea.")";
        // where zusammensetzen
        if ($wherea || $whereb) $where = " WHERE ".$wherea.$trenner.$whereb;




        ### put your code here ###

        /* z.B. db query */

        $sql = "SELECT *
                  FROM ".$cfg["db"]["file"]["entries"]."
                  ".$where."
              ORDER BY ".$cfg["db"]["file"]["order"];
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["db"]["file"]["rows"], $parameter, 1, 3, $getvalues );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0];
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];

        $result = $db -> query($sql); $i = 0;

        if ( $db->num_rows($result) == 0 ) {
            $ausgaben["result"] .= " keine Einträge gefunden.";
        } else {
            // nur erweitern wenn bereits was drin steht
            if ( $ausgaben["result"] ) {
                $ausgaben["result"] .= " folgende Einträge gefunden.";
            } else {
                $ausgaben["result"]  = "";
            }
        }

        while ( $data = $db -> fetch_array($result,1) ) {

            if (is_array($_SESSION["file_memo"])) {
                if (in_array($data["fid"],$_SESSION["file_memo"])) {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$anhang."><img width=\"13\" height\"13\" border=\"0\" src=\"".$cfg["iconpath"]."cms-cb1.png\"></a>";
                } else {
                    $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$anhang."><img width=\"13\" height\"13\" border=\"0\" src=\"".$cfg["iconpath"]."cms-cb0.png\"></a>";
                }
            } else {
                $cb = "<a href=".$cfg["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$anhang."><img width=\"13\" height\"13\" border=\"0\" src=".$cfg["iconpath"]."cms-cb0.png border=0></a>";
            }


            # -> $cfg["db"]["file"]["key"] "fid"
            #$dataloop["list"][$data["fid"]][0] = $data["field1"];
            #$dataloop["list"][$data["fid"]][1] = $data["field2"];

            // tabellen farben wechseln
            if ( $cfg["color"]["set"] == $cfg["color"]["a"]) {
                $cfg["color"]["set"] = $cfg["color"]["b"];
            } else {
                $cfg["color"]["set"] = $cfg["color"]["a"];
            }
            $dataloop["list"][$data["fid"]]["color"] = $cfg["color"]["set"];

            #$dataloop["list"][$data["fid"]]["href"] = "list/view,o,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["ehref"] = "edit,".$data["fid"].".html";

            $type = $cfg["filetyp"][$data["ffart"]];
            $dataloop["list"][$data["fid"]]["dhref"] = $pathvars["filebase"]["webdir"].
                                                       $pathvars["filebase"][$cfg["fileopt"][$type]["name"]].
                                                       $cfg["fileopt"][$type]["name"]."_".
                                                       $data["fid"].".".$data["ffart"];
            if ( $data["ffart"] == "pdf" ) {
                $dataloop["list"][$data["fid"]]["dtarget"] = "_blank";
            } else {
                $dataloop["list"][$data["fid"]]["dtarget"] = "";
            }


            $dataloop["list"][$data["fid"]]["src"] = $pathvars["filebase"]["webdir"].
                                                     $pathvars["filebase"]["pic"]["root"].
                                                     $pathvars["filebase"]["pic"]["tn"]."tn_".
                                                     $data["fid"].".".$data["ffart"];

            $dataloop["list"][$data["fid"]]["alt"] = $data["ffname"];
            $dataloop["list"][$data["fid"]]["title"] = $data["ffname"];

            $dataloop["list"][$data["fid"]]["cb"] = $cb;

            $dataloop["list"][$data["fid"]]["ohref"] = "list/view,o,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["bhref"] = "list/view,b,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["mhref"] = "list/view,m,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["shref"] = "list/view,s,".$data["fid"].".html";

            $i++;
            $even = $i / $cfg["db"]["file"]["line"];
            if ( is_int($even) ) {
                $dataloop["list"][$data["fid"]]["newline"] = $cfg["db"]["file"]["newline"];
            } else {
                $dataloop["list"][$data["fid"]]["newline"] = "";
            }

            #$dataloop["list"][$data["fid"]]["editlink"] = $cfg["basis"]."/edit,".$data["fid"].".html";
            #$dataloop["list"][$data["fid"]]["edittitel"] = "#(edittitel)";

            #$dataloop["list"][$data["fid"]]["deletelink"] = $cfg["basis"]."/delete,".$data["fid"].".html";
            #$dataloop["list"][$data["fid"]]["deletetitel"] = "#(deletetitel)";
        }


        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $HTTP_GET_VARS["error"] != "" ) {
            if ( $HTTP_GET_VARS["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["link_new"] = $cfg["basis"]."/add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["path"] = str_replace($pathvars["virtual"],"",$cfg["basis"]);
        $mapping["main"] = crc32($cfg["path"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (edittitel) #(edittitel)<br />";
            $ausgaben["inaccessible"] .= "# (deletetitel) #(deletetitel)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        $ausgaben["form1_aktion"] = $cfg["basis"]."/list.html";
        $ausgaben["form2_aktion"] = $cfg["basis"]."/upload.html";

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
