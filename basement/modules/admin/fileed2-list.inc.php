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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["fileed"]["right"] == "" ||
        priv_check("/".$cfg["fileed"]["subdir"]."/".$cfg["fileed"]["name"],$cfg["fileed"]["right"]) ||
        priv_check_old("",$cfg["fileed"]["right"]) ) {

        // funktions bereich ( aufbau )
        // ***

        // file_memo verwalten, inkl. ajax-checkboxen
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
                header("Location: ".$cfg["fileed"]["basis"]."/".$environment["parameter"][0].",".$environment["parameter"][1].",,".$environment["parameter"][3].".html");
            }
        }
        $debugging["ausgabe"] .= "<pre>".print_r($_SESSION["file_memo"],True)."</pre>";

        // get-verarbeitung: mimes
        $mime_session = 0;
        if ( is_array($_SESSION["fileed_filter_mime"]) ) $mime_session = -1;
        foreach ( $cfg["file"]["filetyp"] as $key => $value ) {
            if ( !is_array($_GET["mimes"]) && $mime_session == 0 ) {
                $_SESSION["fileed_filter_mime"][$value] = $value;
            } elseif ( !isset($_GET["mimes"])
                   && !isset($_GET["send"])
                   && $mime_session == -1
                   && $_SESSION["fileed_filter_mime"][$value] != "" ) {
                $_SESSION["fileed_filter_mime"][$value] = $value;
            } elseif ( is_array($_GET["mimes"]) && $_GET["mimes"][$value] != "" ) {
                $_SESSION["fileed_filter_mime"][$value] = $value;
            } elseif ( $_SESSION["fileed_filter_mime"][$value] != "" ) {
                unset($_SESSION["fileed_filter_mime"][$value]);
            }
        }

        // get-verarbeitung: filter-remove
        if ( is_array($_GET["remove_filter"]) ) {
            foreach ( $_GET["remove_filter"] as $key => $value ) {
                if ( $key == "mime" ) {
                    if ( $_SESSION["fileed_filter_mime"][$value] != "" ) {
                        unset($_SESSION["fileed_filter_mime"][$value]);
                    }
                }
                if ( $key == "search" ) {
                    $search = array_flip(explode( " ", $_SESSION["fileed_search"] ));
                    unset( $search[$value] );
                    $_SESSION["fileed_search"] = implode(" ",array_flip($search) );
                }
                $header = $cfg["fileed"]["basis"]."/".$environment["kategorie"].".html";
            }
            header("Location: ".$header);
        }
        if ( count($_SESSION["fileed_filter_mime"]) == 0 ) unset($_SESSION["fileed_filter_mime"]);

        // get-verarbeitung: schnellsuche verarbeiten
        if ( isset($_GET["search"]) ) {
            $_SESSION["fileed_position"] = 0;
            $_SESSION["fileed_search"] = $_GET["search"];
        } elseif ( isset($_GET["search"]) && $_GET["search"] == "" ) {
            unset($_SESSION["fileed_search"]);
        }

        // auswahllisten erstellen
        $set = array(); $data = array();
        $_SESSION["fileed_filter0"] = $_SESSION["fileed_filter0"] + 0;
        $_SESSION["fileed_filter1"] = $_SESSION["fileed_filter1"] + 0;
        foreach( $cfg["fileed"]["filter"] as $set => $data ) {
            if ( $_GET["filter".$set] != "" ) {
                $_SESSION["fileed_filter".$set] = $_GET["filter".$set];
            }
            if ( $environment["parameter"][2] != "" ){
                $_SESSION["fileed_filter".$set] = $environment["parameter"][2];
            }
            foreach ( $data as $key => $value ) {
                if ( $key == $_SESSION["fileed_filter".$set] ) {
                    $select = "selected=\"selected\"";
                    $check = "checked=\"checked\"";
                } else {
                    $select = "";
                    $check = "";
                }
                $dataloop["filter".$set][$key] = array(
                    "value" => $key,
                    "label" => $value,
                   "select" => $select,
                    "check" => $check,
                );
            }
            $debugging["ausgabe"] .= "<pre>".print_r($dataloop["filter".$set],True)."</pre>";
        }

        // umleitung, damit die Get-Vars wieder weg sind
        if ( count($_GET) > 0 && !isset($_GET["edit"]) ) {
            $_GET["filter_sel"] == "sel" ? $filter_sel = "sel" : $filter_sel = "all";
            $header = $cfg["fileed"]["basis"]."/list,,,".$filter_sel.".html";
            header("Location: ".$header);
        }

        // radio-auswahl: nur angekreuzte dateien oder alle
        $environment["parameter"][3] != "sel" ? $check = " checked=\"checked\"" : $check = "";
        $dataloop["filter_sel"][] = array(
            "value" => "all",
            "label" => "alle Dateien",
            "check" => $check,
        );
        $check == "" ? $check = " checked=\"checked\"" : $check = "";
        $dataloop["filter_sel"][] = array(
            "value" => "sel",
            "label" => "nur ausgew&auml;hlte",
            "check" => $check,
        );

        // dateitypen-checkboxen bauen
        foreach ( $cfg["file"]["filetyp"] as $key => $value ) {
            if ( (is_array($_SESSION["fileed_filter_mime"]) && in_array($value,$_SESSION["fileed_filter_mime"]))
              || !is_array($_SESSION["fileed_filter_mime"])
            ) {
                $checked = " checked=\"checked\"";
                $class = "checked";
                $link = "?remove_filter[mime]=".$value;
            } else {
                $checked = "";
                $class = "";
                $link = "?add_filter[mime]=".$value;
            }
            $dataloop["mimes"][$value] = array(
                  "label" => $value,
                   "link" => $link,
                "checked" => $checked,
                  "class" => $class,
            );
        }

        // ansichtslinks bauen
        $views = array("default","details","symbols");
        if ( $_COOKIE["fileed_view"][$_SESSION["uid"]] != "" ) {
            $cfg["fileed"]["default_view"] = $_COOKIE["fileed_view"][$_SESSION["uid"]];
            $view_mode = $_COOKIE["fileed_view"][$_SESSION["uid"]];
        }
        if ( $cfg["fileed"]["default_view"] == "" ) $cfg["fileed"]["default_view"] = "default";
        if ( $environment["parameter"][4] != "" ) {
            $view_mode = $environment["parameter"][4];
        } else {
            $view_mode = $cfg["fileed"]["default_view"];
        }
        foreach ( $views as $value ) {
            if ( ($view_mode != "" && $value == $view_mode)
              || ($view_mode == "" && $value == $cfg["fileed"]["default_view"]) ) {
                $icon = "/images/default/view_icon_".$value."_sel.png";
            } else {
                $icon = "/images/default/view_icon_".$value.".png";
            }
            $dataloop["view"][] = array(
                  "title" => "#(".$value.")",
                   "icon" => $icon,
                   "link" => $cfg["fileed"]["basis"]."/list,".
                             $environment["parameter"][1].",".
                             $environment["parameter"][2].",".
                             $environment["parameter"][3].",".
                             $value.",".
                             $environment["parameter"][5].".html",
            );
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
                  "edit" => $cfg["fileed"]["basis"]."/edit.html",
                "delete" => $cfg["fileed"]["basis"]."/delete.html",
               "collect" => $cfg["fileed"]["basis"]."/collect.html",
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
        if ( $_SESSION["fileed_search"] ) {
            $ausgaben["search"] = $_SESSION["fileed_search"];
            $filters[] = $_SESSION["fileed_search"];
            $array1 = explode( " ", $_SESSION["fileed_search"] );
            $array2 = array( "ffname", "fdesc", "fhit", "fid" );

            foreach ( $array1 as $value1 ) {
                if ( $value1 != "" ) {
                    foreach ( $array2 as $value2 ) {
                        if ( $part["search"] != "" ) $part["search"] .= " or ";
                        if ( $value2 == "fid" ) {
                            $part["search"] .= "CAST(".$value2." as char) LIKE '%".$value1."%'";
                        } else {
                            $part["search"] .= $value2. " LIKE '%".$value1."%'";
                        }
                    }
                    $dataloop["filter"][] = array(
                        "label" => $value1,
                        "del" => "?remove_filter[search]=".$value1,
                    );
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
                $filters[] = $cfg["fileed"]["filter"][0][$_SESSION["fileed_filter0"]];
                break;
            default:
                $filters[] = $cfg["fileed"]["filter"][0][$_SESSION["fileed_filter0"]];
                $part["auswahl1"] = " fuid = '".$_SESSION["uid"]."'";
        }

        // auswahlliste 2 verarbeiten

        if ( $environment["parameter"][3] == "sel" ) {
            if ( is_array($_SESSION["file_memo"]) ) {
                foreach ( $_SESSION["file_memo"] as $value ) {
                    if ( $part["sel"] == "" ) {
                        $part["sel"] = "(".$cfg["fileed"]["db"]["file"]["key"]." = ".$value.")";
                    } else {
                        $part["sel"] .= " OR (".$cfg["fileed"]["db"]["file"]["key"]." = ".$value.")";
                    }
                }
            }
            if ( $part["sel"] == "" ) {
                $part["sel"] = $cfg["fileed"]["db"]["file"]["key"]." = -1";
            } else {
                $part["sel"] = "(".$part["sel"].")";
            }
            $filters[] = "nur ausgew&auml;hlte";
        }

        $buffer = array();
        foreach ( $cfg["file"]["filetyp"] as $key => $value ) {
            if ( (is_array($_SESSION["fileed_filter_mime"]) && in_array($value,$_SESSION["fileed_filter_mime"]))
                || !is_array($_SESSION["fileed_filter_mime"]) ) {
                $buffer[] = $key;
                $dataloop["filter"][$value] = array(
                    "label" => $value,
                    "del" => "?remove_filter[mime]=".$value,
                );
            }
        }
        if ( count($buffer) != count($cfg["file"]["filetyp"]) ) $filters[] = implode(", ",$buffer);
        $part["mimes"] = "(ffart IN ('".implode("','",$buffer)."'))";

        // where build
        $where = "";
        if ( count($part) >= 2 ) $binder = " AND ";
        foreach ( $part as $value ) {
            if ( $where == "" ) {
                $where = " WHERE ".$value;
            } else {
                $where .= $binder.$value;
            }
        }

        if ( count($filters) > 0 ) $ausgaben["result"] = "#(answera) <b>\"".implode("\"</b> und <b>\"",$filters)."\"</b> #(answerb) ";

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
        $inhalt_selector = inhalt_selector(
                                $sql,
                                $environment["parameter"][1],
                                $cfg["fileed"]["db"]["file"]["rows"],
                                ",".$environment["parameter"][2].",".$environment["parameter"][3].",".$view_mode.",".$environment["parameter"][5],
                                1, 5, Null
                           );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0];
        $ausgaben["inhalt_selected"] = $inhalt_selector[3];
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];

        $result = $db -> query($sql); $i = 0;
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        if ( $db->num_rows($result) == 0 ) {
            #$ausgaben["result"] .= " keine Eintr�ge gefunden.";
            $ausgaben["result"] .= " #(answerc_no)";
            $hidedata["empty_search"]["search"] = "<b>\"".implode("\"</b> und <b>\"",$filters)."\"</b>";
            $hidedata["file"]["display"] = "none";
        } else {
            // nur erweitern wenn bereits was drin steht
            if ( $ausgaben["result"] ) {
                #$ausgaben["result"] .= " folgende Eintr�ge gefunden.";
                $ausgaben["result"] .= " #(answerc_yes)";
            } else {
                $ausgaben["result"]  = "";
            }

            $hidedata["search_result"]["search"] = "<b>\"".implode("\"</b> und <b>\"",$filters)."\"</b>";

            // dataloop wird ueber eine share-funktion aufgebaut
            filelist($result, "fileed");

            if ( $view_mode == "details" || $view_mode == "symbols" ) {

                if ( is_array($dataloop["list_files"]) ) {
                    foreach ( $dataloop["list_files"] as $key=>$value ) {
                        $filetyp = $cfg["file"]["filetyp"][$value["art"]];
                        if ( $filetyp == "img" ) {
                            $array = array(
                                "o" => "Vorschau Original (original)",
                                "b" => "Vorschau Gross (big)",
                                "m" => "Vorschau Mittel (middle)",
                                "s" => "Vorschau Klein (small)",
                            );
                            $aktion = "";
                            foreach ( $array as $size=>$title ) {
                                $srv_path = $cfg["file"]["fileopt"][$filetyp]["path"].
                                            $cfg["file"]["base"]["pic"][$size].
                                            $cfg["file"]["fileopt"][$filetyp]["name"]."_".$value["id"].".".$value["art"];
                                if ( file_exists($srv_path) ) {
                                    $aktion .= '<a rel="lightbox['.$value["id"].']" href="'.$value[$size."href_lb"].'" title="'.$title.'" class="fileed_preview">'.strtoupper($size).'</a>';
                                } else {
                                    $aktion .= '<s title="Error" class="fileed_preview">'.strtoupper($size).'</s>';
                                }
                            }

                            $srv_path = $cfg["file"]["fileopt"][$filetyp]["path"]."thumbnail/tn_".$value["id"].".".$value["art"];
                            $dataloop["list_files"][$key]["thumb_src"] = $dataloop["list_files"][$key]["src"];
                        } else {
                            $aktion = '<a title="Herunterladen (download)" href="'.$value["dhref"].'">D</a>';
                            $srv_path = $cfg["file"]["fileopt"][$filetyp]["path"].$cfg["file"]["fileopt"][$filetyp]["name"]."_".$value["id"].".".$value["art"];
                            $dataloop["list_files"][$key]["thumb_src"] = "/images/default/thumbs_".$filetyp.".png";
                        }
                        $dataloop["list_files"][$key]["icon_src"] = "/images/default/text_icon_".$filetyp.".png";

                        if ( !file_exists($srv_path) ) {
                            $dataloop["list_files"][$key]["thumb_src"] = "/images/default/thumbs_broken.png";
                        }
                        $dataloop["list_files"][$key]["kategorie"] = $cfg["file"]["filetyp"][$value["art"]];
                        $dataloop["list_files"][$key]["aktion"] = $aktion;
                    }
                }

                unset($hidedata["list_images"]);
                unset($hidedata["list_other"]);
                unset($dataloop["list_images"]);
                unset($dataloop["list_other"]);
                $hidedata["list_".$view_mode."_frame"] = array();
                if ( $cfg["fileed"]["ajax-modus"] == FALSE ) {
                    $hidedata["list_".$view_mode."_plain"] = array();
                } else {
                    $hidedata["list_".$view_mode."_ajax"] = array();
                }
                setcookie("fileed_view[".$_SESSION["uid"]."]",$view_mode);

            } else {
                unset($hidedata["list_files"]);
                unset($dataloop["list_files"]);
                setcookie("fileed_view[".$_SESSION["uid"]."]",$cfg["fileed"]["default_view"]);
            }

        }

        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["link_new"] = $cfg["fileed"]["basis"]."/add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["fileed"]["path"] = str_replace($pathvars["virtual"],"",$cfg["fileed"]["basis"]);
        $mapping["main"] = eCRC($cfg["fileed"]["path"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "g (cmslink) g(cmslink)<br />";
            $ausgaben["inaccessible"] .= "# (fileedit) #(fileedit)<br />";
            $ausgaben["inaccessible"] .= "# (filecollect) #(filecollect)<br />";
            $ausgaben["inaccessible"] .= "# (filedelete) #(filedelete)<br />";
            $ausgaben["inaccessible"] .= "# (answera) #(answera)<br />";
            $ausgaben["inaccessible"] .= "# (answerb) #(answerb)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_no) #(answerc_no)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes) #(answerc_yes)<br />";
            $ausgaben["inaccessible"] .= "# (prev) #(next)<br />";
            $ausgaben["inaccessible"] .= "# (next) #(prev)<br />";
            $ausgaben["inaccessible"] .= "# (images) #(images)<br />";
            $ausgaben["inaccessible"] .= "# (other) #(other)<br />";

            $ausgaben["inaccessible"] .= "# (img) #(img)<br />";
            $ausgaben["inaccessible"] .= "# (pdf) #(pdf)<br />";
            $ausgaben["inaccessible"] .= "# (odf) #(odf)<br />";

            $ausgaben["inaccessible"] .= "# (default) #(default)<br />";
            $ausgaben["inaccessible"] .= "# (details) #(details)<br />";
            $ausgaben["inaccessible"] .= "# (symbols) #(symbols)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        $ausgaben["form1_aktion"] = $cfg["fileed"]["basis"]."/list,,,".
                                    $environment["parameter"][3].",".
                                    $view_mode.",".
                                    $environment["parameter"][5].".html";
        $ausgaben["form2_aktion"] = $cfg["fileed"]["basis"]."/upload.html";

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>