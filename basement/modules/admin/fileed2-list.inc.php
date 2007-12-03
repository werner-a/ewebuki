<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "fileed - list funktion";
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

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich ( aufbau )
        // ***

        // file_memo verwalten
        if ( $environment["parameter"][2] ){
            if ( isset($_SESSION["file_memo"][$environment["parameter"][2]]) ){
                unset($_SESSION["file_memo"][$environment["parameter"][2]]);
            } else {
                $_SESSION["file_memo"][$environment["parameter"][2]] = $environment["parameter"][2];
            }
            if ( count($_SESSION["file_memo"]) == 0 ) unset($_SESSION["file_memo"]);
            if ( isset($_GET["ajax"]) ){
                if ( count($_SESSION["file_memo"]) == 0 ) {
                    header("HTTP/1.0 404 Not Found");
                }
                exit;
            } else {
                header("Location: ".$cfg["basis"]."/".$environment["parameter"][0].",".$environment["parameter"][1].",,".$environment["parameter"][3].".html");
            }
        }

        $debugging["ausgabe"] .= "<pre>".print_r($_SESSION["file_memo"],True)."</pre>";

        // auswahllisten erstellen
        $set = array(); $data = array();
        $_SESSION["fileed_filter0"] = $_SESSION["fileed_filter0"] + 0;
        $_SESSION["fileed_filter1"] = $_SESSION["fileed_filter1"] + 0;
        foreach( $cfg["filter"] as $set => $data ) {
            if ( $_GET["filter".$set] != "" ) {
                $_SESSION["fileed_filter".$set] = $_GET["filter".$set];
            }
            if ( $environment["parameter"][2] != "" ){
                $_SESSION["fileed_filter".$set] = $environment["parameter"][2];
            }
            foreach ( $data as $key => $value ) {
                if ( $key == $_SESSION["fileed_filter".$set] ) {
                    $dataloop["filter".$set][$key]["select"] = "selected";
                } else {
                    $dataloop["filter".$set][$key]["select"] = "";
                }
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
            $hidedata["cms"] = array(
                   "link" => $_SESSION["cms_last_edit"]."?referer=".$_SESSION["cms_last_referer"],
                "display" => "inline",
            );
        } else {
            $hidedata["cms"] = array(
                   "link" => "",
                "display" => "none",
            );
        }

        // bearbeiten- und loeschen link erstellen
        $hidedata["file"] = array(
                "edit" => $cfg["basis"]."/edit.html",
                "delete" => $cfg["basis"]."/delete.html",
            "collect" => $cfg["basis"]."/collect.html",
            "display" => "inline"
        );
        if ( count($_SESSION["file_memo"]) == 0 ) {
            $hidedata["file"]["display"] = "none";
        }

        // +++
        // funktions bereich ( aufbau )



        // funktions bereich ( auswertung )
        // ***

        // where init
        $part = array();

        // suche verarbeiten
        if ( isset($_GET["search"]) ) {
            $_SESSION["fileed_position"] = 0;
            $_SESSION["fileed_search"] = $_GET["search"];
        } elseif ( isset($_GET["search"]) && $_GET["search"] == "" ) {
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

        function collect_filetyps($kategorie){
            global $cfg;

            $types = explode( ",",$kategorie );
            foreach ( $types as $kat ){
                foreach ( $cfg["filetyp"] as $key=>$value ){
                    if ( $value == $kat ) $array[] = "'".$key."'";
                }
            }
            return implode(",",$array);
        }

        switch ( $_SESSION["fileed_filter1"] ) {
            case 3:
                foreach ( $_SESSION["file_memo"] as $value ) {
                    if ( $pattern == "" ) {
                        $pattern = "(".$cfg["db"]["file"]["key"]." = ".$value.")";
                    } else {
                        $pattern .= " OR (".$cfg["db"]["file"]["key"]." = ".$value.")";
                    }
                }
                if ( $pattern == "" ) $pattern = $cfg["db"]["file"]["key"]." = -1";

                $part["auswahl2"] = $part["auswahl2"] = " ffart IN (".collect_filetyps("img").") AND (".$pattern.")";
                $hidedata["images"] = array();
                break;
            case 2:
                $part["auswahl2"] = " ffart IN (".collect_filetyps("arc").")";
                $hidedata["other"] = array();
                break;
            case 1:
                $part["auswahl2"] = " ffart IN (".collect_filetyps("odf,pdf").")";
                $hidedata["other"] = array();
                break;
        }

        if ( $environment["parameter"][3] == "sel" ) {
            if ( is_array($_SESSION["file_memo"]) ) {
                foreach ( $_SESSION["file_memo"] as $value ) {
                    if ( $part["sel"] == "" ) {
                        $part["sel"] = "(".$cfg["db"]["file"]["key"]." = ".$value.")";
                    } else {
                        $part["sel"] .= " OR (".$cfg["db"]["file"]["key"]." = ".$value.")";
                    }
                }
            }
            if ( $part["sel"] == "" ) $part["sel"] = $cfg["db"]["file"]["key"]." = -1";
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
                  FROM ".$cfg["db"]["file"]["entries"]."
                  ".$where."
              ORDER BY ".$cfg["db"]["file"]["order"];
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        if ( $environment["parameter"][1] != "" ) {
            $_SESSION["fileed_position"] = $environment["parameter"][1];
        }
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["db"]["file"]["rows"], ",".$environment["parameter"][2], 1, 5, Null );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0];
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];

        $result = $db -> query($sql); $i = 0;
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        if ( $db->num_rows($result) == 0 ) {
            #$ausgaben["result"] .= " keine Einträge gefunden.";
            $ausgaben["result"] .= " #(answerc_no)";
        } else {
            // nur erweitern wenn bereits was drin steht
            if ( $ausgaben["result"] ) {
                #$ausgaben["result"] .= " folgende Einträge gefunden.";
                $ausgaben["result"] .= " #(answerc_yes)";
            } else {
                $ausgaben["result"]  = "";
            }
        }

        // dataloop wird ueber eine share-funktion aufgebaut
        filelist($result);

        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $_GET["error"] != "" ) {
            if ( $_GET["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            } else {
                $ausgaben["form_error"] = "#(error2)";
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
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (error2) #(error2)<br />";

            $ausgaben["inaccessible"] .= "g (cmslink) g(cmslink)<br />";
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
        $ausgaben["form1_aktion"] = $cfg["basis"]."/list.html";
        $ausgaben["form2_aktion"] = $cfg["basis"]."/upload.html";

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>