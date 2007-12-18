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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["fileed"]["right"] == "" || $rechte[$cfg["fileed"]["right"]] == -1 ) {

        // funktions bereich ( aufbau )
        // ***

        // file_memo verwalten
        if ( $environment["parameter"][2] ) {
            $key = $environment["parameter"][2];
            $wert = $environment["parameter"][2];
            if ( is_array( $_SESSION["file_memo"] ) ) {
                if ( in_array($key, $_SESSION["file_memo"] ) ) {
                    unset ( $_SESSION["file_memo"][$key] );
                } else {
                    $_SESSION["file_memo"][$key] = $wert;
                }
            } else {
                $_SESSION["file_memo"][$key] = $wert;
            }
        }
        $debugging["ausgabe"] .= "<pre>".print_r($_SESSION["file_memo"],True)."</pre>";

        // auswahllisten erstellen
        $set = array(); $data = array();
        $_SESSION["fileed_filter0"] = $_SESSION["fileed_filter0"] + 0;
        $_SESSION["fileed_filter1"] = $_SESSION["fileed_filter1"] + 0;
        foreach( $cfg["fileed"]["filter"] as $set => $data ) {
            if ( $HTTP_GET_VARS["filter".$set] != "" ) {
                $_SESSION["fileed_filter".$set] = $HTTP_GET_VARS["filter".$set];
            }
            $dataloop["filter".$set][$_SESSION["fileed_filter".$set]]["select"] =  " selected";
            foreach ( $data as $key => $value ) {
                $dataloop["filter".$set][$key]["value"] = $key;
                $dataloop["filter".$set][$key]["label"] = $value;
            }
            $debugging["ausgabe"] .= "<pre>".print_r($dataloop["filter".$set],True)."</pre>";
        }

        // content editor link erstellen
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "SESSION (cms_last_edit): ".$_SESSION["cms_last_edit"].$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "SESSION (cms_last_referer): ".$_SESSION["cms_last_referer"].$debugging["char"];
        if ( isset($_SESSION["cms_last_edit"]) ) {
            // abrechen im cms editor soll zur ursrungseite springen und nicht in den fileed
            $_SESSION["page"] = $_SESSION["cms_last_referer"];
            $ausgaben["cmslink"] = "<a href=\"".$_SESSION["cms_last_edit"]."?referer=".$_SESSION["cms_last_referer"]."\">#(cmslink)</a>";
        } else {
            $ausgaben["cmslink"] = "";
        }

        // bearbeiten- und loeschen link erstellen
        if ( count($_SESSION["file_memo"]) >= 1 ) {
            $ausgaben["fileedit"] = "<a href=\"".$cfg["fileed"]["basis"]."/edit.html\">#(fileedit)</a>";
            $ausgaben["filedelete"] = "<a href=\"".$cfg["fileed"]["basis"]."/delete.html\">#(filedelete)</a>";
        } else {
            $ausgaben["fileedit"] = "";
            $ausgaben["filedelete"] = "";
        }

        // +++
        // funktions bereich ( aufbau )



        // funktions bereich ( auswertung )
        // ***

        // where init
        $part = array();

        // suche verarbeiten
        if ( isset($HTTP_GET_VARS["search"]) ) {
            $_SESSION["fileed_position"] = 0;
            $_SESSION["fileed_search"] = $HTTP_GET_VARS["search"];
        } elseif ( isset($HTTP_GET_VARS["search"]) && $HTTP_GET_VARS["search"] == "" ) {
            unset($_SESSION["fileed_search"]);
        }
        if ( $_SESSION["fileed_search"] ) {
            $ausgaben["search"] = $_SESSION["fileed_search"];
            $ausgaben["result"] = "#(answera) \"".$_SESSION["fileed_search"]."\" #(answerb) ";
            $array1 = explode( " ", $_SESSION["fileed_search"] );
            $array2 = array( "ffname", "fdesc", "fhit" );

            foreach ( $array1 as $value1 ) {
                if ( $value1 != "" ) {
                    foreach ( $array2 as $value2 ) {
                        if ( $part["search"] != "" ) $part["search"] .= " or ";
                        $part["search"] .= $value2. " LIKE '%".$value1."%'";
                    }
                }
            }
            if ( $part["search"] != "" ) $part["search"] = "(".$part["search"].")";
        } else {
            $ausgaben["search"] = "";
        }

        // auswahlliste 1 verarbeiten
        switch ( $_SESSION["fileed_filter0"] ) {
            case 2:
                #$part["auswahl1"] = "";
                break;
            case 1:
                $part["auswahl1"] = " fdid = '".$_SESSION["custom"]."'";
                break;
            default:
                $part["auswahl1"] = " fuid = '".$_SESSION["uid"]."'";
        }

        // auswahlliste 2 verarbeiten
        switch ( $_SESSION["fileed_filter1"] ) {
            case 2:
                $part["auswahl2"] = " ffart in ('zip','bz2','gz')";
                $hidedata["other"] = array();
                break;
            case 1:
                $part["auswahl2"] = " ffart in ('pdf','odt','ods','odp')";
                $hidedata["other"] = array();
                break;
            default:
                $part["auswahl2"] = " ffart in ('gif','jpg','png')";
                $hidedata["images"] = array();
        }

        // where build
        if ( count($part) >= 2 ) $binder = " AND ";
        foreach ( $part as $value ) {
            if ( $where == "" ) {
                $where = " WHERE ".$value;
            } else {
                $where .= $binder.$value;
            }
        }

        // +++
        // funktions bereich ( auswertung )



        // funktions bereich
        // ***

        // db query
        $sql = "SELECT *
                  FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                  ".$where."
              ORDER BY ".$cfg["fileed"]["db"]["file"]["order"];
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        if ( $environment["parameter"][1] != "" ) {
            $_SESSION["fileed_position"] = $environment["parameter"][1];
        }
        $inhalt_selector = inhalt_selector( $sql, $_SESSION["fileed_position"], $cfg["fileed"]["db"]["file"]["rows"], Null, 1, 3, Null );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0];
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];

        $result = $db -> query($sql); $i = 0;
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        if ( $db->num_rows($result) == 0 ) {
            #$ausgaben["result"] .= " keine Eintr�ge gefunden.";
            $ausgaben["result"] .= " #(answerc_no)";
        } else {
            // nur erweitern wenn bereits was drin steht
            if ( $ausgaben["result"] ) {
                #$ausgaben["result"] .= " folgende Eintr�ge gefunden.";
                $ausgaben["result"] .= " #(answerc_yes)";
            } else {
                $ausgaben["result"]  = "";
            }
        }

        if ( $getvalues != "" ) $getvalues = "?".$getvalues;
        while ( $data = $db -> fetch_array($result,1) ) {

            if (is_array($_SESSION["file_memo"])) {
                if (in_array($data["fid"],$_SESSION["file_memo"])) {
                    $cb = "<a href=".$cfg["fileed"]["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$getvalues."><img width=\"13\" height\"13\" border=\"0\" src=\"".$cfg["fileed"]["iconpath"]."cms-cb1.png\"></a>";
                } else {
                    $cb = "<a href=".$cfg["fileed"]["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$getvalues."><img width=\"13\" height\"13\" border=\"0\" src=\"".$cfg["fileed"]["iconpath"]."cms-cb0.png\"></a>";
                }
            } else {
                $cb = "<a href=".$cfg["fileed"]["basis"]."/list,".$environment["parameter"][1].",".$data["fid"].".html".$getvalues."><img width=\"13\" height\"13\" border=\"0\" src=".$cfg["fileed"]["iconpath"]."cms-cb0.png border=0></a>";
            }

            // tabellen farben wechseln
            if ( $cfg["fileed"]["color"]["set"] == $cfg["fileed"]["color"]["a"]) {
                $cfg["fileed"]["color"]["set"] = $cfg["fileed"]["color"]["b"];
            } else {
                $cfg["fileed"]["color"]["set"] = $cfg["fileed"]["color"]["a"];
            }
            $dataloop["list"][$data["fid"]]["color"] = $cfg["fileed"]["color"]["set"];

            $dataloop["list"][$data["fid"]]["ehref"] = "edit,".$data["fid"].".html";

            $type = $cfg["file"]["filetyp"][$data["ffart"]];
            $dataloop["list"][$data["fid"]]["dhref"] = $cfg["file"]["base"]["webdir"].
                                                       $cfg["file"]["base"][$cfg["fileopt"][$type]["name"]].
                                                       $cfg["file"]["fileopt"][$type]["name"]."_".
                                                       $data["fid"].".".$data["ffart"];
            if ( $data["ffart"] == "pdf" ) {
                $dataloop["list"][$data["fid"]]["dtarget"] = "_blank";
            } else {
                $dataloop["list"][$data["fid"]]["dtarget"] = "";
            }


            $dataloop["list"][$data["fid"]]["src"] = $cfg["file"]["base"]["webdir"].
                                                     $cfg["file"]["base"]["pic"]["root"].
                                                     $cfg["file"]["base"]["pic"]["tn"]."tn_".
                                                     $data["fid"].".".$data["ffart"];

            $dataloop["list"][$data["fid"]]["alt"] = $data["ffname"];
            $dataloop["list"][$data["fid"]]["title"] = $data["ffname"];

            $dataloop["list"][$data["fid"]]["cb"] = $cb;

            $dataloop["list"][$data["fid"]]["ohref"] = "list/view,o,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["bhref"] = "list/view,b,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["mhref"] = "list/view,m,".$data["fid"].".html";
            $dataloop["list"][$data["fid"]]["shref"] = "list/view,s,".$data["fid"].".html";

            $i++;
            $even = $i / $cfg["fileed"]["db"]["file"]["line"];
            if ( is_int($even) ) {
                $dataloop["list"][$data["fid"]]["newline"] = $cfg["fileed"]["db"]["file"]["newline"];
            } else {
                $dataloop["list"][$data["fid"]]["newline"] = "";
            }
        }

        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $HTTP_GET_VARS["error"] != "" ) {
            if ( $HTTP_GET_VARS["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            } else {
                $ausgaben["form_error"] = "#(error2)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["link_new"] = $cfg["fileed"]["basis"]."/add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["fileed"]["path"] = str_replace($pathvars["virtual"],"",$cfg["fileed"]["basis"]);
        $mapping["main"] = crc32($cfg["fileed"]["path"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (error2) #(error2)<br />";

            $ausgaben["inaccessible"] .= "# (cmslink) #(cmslink)<br />";
            $ausgaben["inaccessible"] .= "# (fileedit) #(fileedit)<br />";
            $ausgaben["inaccessible"] .= "# (filedelete) #(filedelete)<br />";

            $ausgaben["inaccessible"] .= "# (answera) #(answera)<br />";
            $ausgaben["inaccessible"] .= "# (answerb) #(answerb)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_no) #(answerc_no)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes) #(answerc_yes)<br />";

            $ausgaben["inaccessible"] .= "# (prev) #(next)<br />";
            $ausgaben["inaccessible"] .= "# (next) #(prev)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        $ausgaben["form1_aktion"] = $cfg["fileed"]["basis"]."/list.html";
        $ausgaben["form2_aktion"] = $cfg["fileed"]["basis"]."/upload.html";

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>