<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: contented-edit.inc.php 1242 2008-02-08 16:16:50Z chaot $";
// "contented - edit funktion";
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

    // parameter-verzeichnis:
    // 1: Datenbank
    // 2: tname
    // 3: label
    // 4: [leer]
    // 5: version

    // get-aufruf um das neue eingefuegte markierungsfeld automatisch richtig zu fuellen
    if ( $_GET["transform"] == "mark_content" ) {
        $sql = "SELECT *
                  FROM site_text
              ORDER BY lang, label, tname, version DESC";
        $result = $db -> query($sql);
        $unique = "";
        while ( $data = $db -> fetch_array($result,1) ) {
            if ( $data["hide"] < 0 ) continue;
            if ( $unique != $data["lang"]."|".$data["label"]."|".$data["tname"]
               && $data["hide"] == 0 ) {
                $sql = "UPDATE site_text
                           SET hide=1
                         WHERE lang='".$data["lang"]."'
                           AND label='".$data["label"]."'
                           AND tname='".$data["tname"]."'
                           AND version=".$data["version"];
                $res = $db -> query($sql);
            }
            $unique = $data["lang"]."|".$data["label"]."|".$data["tname"];
        }
    }

    // erlaubnis bei intrabvv speziell setzen
    $database = $environment["parameter"][1];
    if ( is_array($_SESSION["katzugriff"]) ) {
        if ( in_array("-1:".$database.":".$environment["parameter"][2],$_SESSION["katzugriff"]) ) $erlaubnis = -1;
    }
    if ( is_array($_SESSION["dbzugriff"]) ) {
        if ( in_array($database,$_SESSION["dbzugriff"]) ) $erlaubnis = -1;
    }

    // form_referer
    if ( !preg_match("/wizard$/",dirname($_SERVER["HTTP_REFERER"])) ) {
        $_SESSION["form_referer"] = $_SERVER["HTTP_REFERER"];
    }
    $ausgaben["form_referer"] = $_SESSION["form_referer"];


    // leere parameter abfangen
    // * * *
    $reload = 0;
    if ( $environment["parameter"][1] != "" ) {
        $db->selectDb($database,FALSE);
    } else {
        $reload = -1;
    }
    $environment["parameter"][1] = $db->getDb();
    if ( $environment["parameter"][2] == "" ) {
        $path = explode("/",str_replace($pathvars["menuroot"],"",$_SERVER["HTTP_REFERER"]));
        $kategorie = str_replace(".html","", array_pop($path));
        if ( strstr($kategorie,",") ) $kategorie = substr($kategorie,0,strpos($kategorie,","));
        $ebene = implode("/",$path);
        if ( $kategorie == "" ) $kategorie = "index";
        if ( count($path) == 0 || (count($path) == 1 && $path[0]=="") ) {
            $environment["parameter"][2] = $kategorie;
        } else {
            $environment["parameter"][2] = crc32($ebene).".".$kategorie;
        }
        $reload = -1;
    }
    if ( $environment["parameter"][3] == "" ) {
        $environment["parameter"][3] = $cfg["wizard"]["wizardtyp"]["default"]["def_label"];
        $reload = -1;
    }
    if ( $reload == -1 ) header("Location: ".$cfg["wizard"]["basis"]."/".implode(",",$environment["parameter"]).".html");
    // + + +


    if ( $cfg["wizard"]["right"] == "" ||
        priv_check("/".$cfg["wizard"]["subdir"]."/".$cfg["wizard"]["name"],$cfg["wizard"]["right"]) ||
        priv_check_old("",$cfg["wizard"]["right"]) ||
        $rechte["administration"] == -1 ||
        $erlaubnis == -1 ) {

        // page basics
        // ***
        if ( $environment["parameter"][5] != "" ) {
            $version = " AND version=".$environment["parameter"][5];
        } else {
            $version = "";
        }

        $url = tname2path($environment["parameter"][2]);
        $buffer = find_marked_content($url, $cfg["wizard"], $cfg["wizard"]["default_label"]);
        $dataloop["releases"] = $buffer[-2];
        if ( count($dataloop["releases"]) > 0 ) {
            $hidedata["releases"] = array();
        } else {
            $hidedata["nop"] = array();
        }

        if ( $environment["parameter"][4] != "" && $environment["parameter"][5] != "" ) {
            if ( $environment["parameter"][4] == "release" ) {
                // naechste nicht versteckte versions-nummer finden
                $sql = "SELECT max(version) as max_version
                        FROM ". SITETEXT ."
                        WHERE lang = '".$environment["language"]."'
                        AND label ='".$environment["parameter"][3]."'
                        AND tname ='".$environment["parameter"][2]."' AND hide=0";
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,1);
                $next_version = $data["max_version"] + 1;
                // alle "unnoetigen" versionen loeschen
                $sql = "DELETE
                        FROM ". SITETEXT ."
                        WHERE lang = '".$environment["language"]."'
                        AND label ='".$environment["parameter"][3]."'
                        AND tname ='".$environment["parameter"][2]."'
                        AND hide<0
                        AND version<>".$environment["parameter"][5];
                $result = $db -> query($sql);
                // freigegebenen Datensatz aktualisieren
                $sql = "UPDATE ". SITETEXT ."
                        SET version=".$next_version.", hide=1
                        WHERE lang = '".$environment["language"]."'
                        AND label ='".$environment["parameter"][3]."'
                        AND tname ='".$environment["parameter"][2]."'
                        AND version=".$environment["parameter"][5];
                $result = $db -> query($sql);
                // checken, ob menuepunkt aktivert ist
                $menu_entry = make_id(tname2path($environment["parameter"][2]));
                $sql = "UPDATE site_menu
                           SET hide='0'
                         WHERE mid=".$menu_entry["mid"];
                $result = $db -> query($sql);
            } elseif ( $environment["parameter"][4] == "unlock" ) {
                // version wird wieder entsperrt
                $sql = "UPDATE ". SITETEXT ."
                        SET hide=-1
                        WHERE lang = '".$environment["language"]."'
                        AND label ='".$environment["parameter"][3]."'
                        AND tname ='".$environment["parameter"][2]."'
                        AND version=".$environment["parameter"][5];
                $result = $db -> query($sql);
            }
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        // was anzeigen
        $mapping["main"] = "wizard-release";
        #$mapping["navi"] = "leer";

//         // freigabe-test
//         if ( $specialvars["content_release"] == -1 ) {
//             $hidedata["edit"] = array();
//         } else {
//             $hidedata["default"] = array();
//         }
//
//         // unzugaengliche #(marken) sichtbar machen
//         // ***
//         if ( isset($_GET["edit"]) ) {
//             $ausgaben["inaccessible"] = "inaccessible values:<br />";
//             $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
//             $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
//         } else {
//             $ausgaben["inaccessible"] = "";
//         }
//
//         if ( $environment["parameter"][6] == "verify"
//             && $_POST["send"] != "" ) {
//
//             $ebene = str_replace(array($pathvars["virtual"],$pathvars["webroot"]),"",dirname($_SESSION["form_referer"]));
//             $kategorie = str_replace(".html","",basename($_SESSION["form_referer"]));
//             if ( strstr($kategorie,",") ) $kategorie = substr($kategorie,0,strpos($kategorie,","));
//
//             if ( $content_exists == 0 || $_POST["send"][0] == "version" ) {
//                 // notwendig fuer die artikelverwaltung , der bisher aktive artikel wird auf inaktiv gesetzt
//                 if ( preg_match("/^\[!\]/",$content,$regs) ) {
//                     $sql_regex = "SELECT * FROM ". SITETEXT ." WHERE content REGEXP '^\\\[!\\\]1' AND tname like '".$environment["parameter"][2]."'";
//                     $result_regex  = $db -> query($sql_regex);
//                     $data_regex = $db -> fetch_array($result_regex,1);
//                     $new_content = preg_replace("/\[!\]1/","[!]0",$data_regex["content"]);
//                     $sql_regex = "UPDATE ". SITETEXT ." SET content ='".$new_content."' WHERE content REGEXP '^\\\[!\\\]1' AND tname like '".$environment["parameter"][2]."'";
//                     $result_regex  = $db -> query($sql_regex);
//                 }
//                 // freigabe-test
//                 if ( $specialvars["content_release"] == -1 ) {
//                     $hide1 = ",hide";
//                     if ( $_POST["release_mark"] == -1 ) {
//                         $hide2 = ",-2";
//                     } else {
//                         $hide2 = ",-1";
//                     }
//                 } else {
//                     $hide1 = "";
//                     $hide2 = "";
//                 }
//
//                 $sql = "INSERT INTO ". SITETEXT ."
//                                     (lang, label, tname, version,
//                                     ebene, kategorie,
//                                     crc32, html, content,
//                                     changed, bysurname, byforename, byemail, byalias".$hide1.")
//                             VALUES (
//                                     '".$environment["language"]."',
//                                     '".$environment["parameter"][3]."',
//                                     '".$environment["parameter"][2]."',
//                                     '".++$form_values["version"]."',
//                                     '".$ebene."',
//                                     '".$kategorie."',
//                                     '".$specialvars["crc32"]."',
//                                     '0',
//                                     '".$form_values["content"]."',
//                                     '".date("Y-m-d H:i:s")."',
//                                     '".$_SESSION["surname"]."',
//                                     '".$_SESSION["forename"]."',
//                                     '".$_SESSION["email"]."',
//                                     '".$_SESSION["alias"]."'
//                                     ".$hide2.")";
//             } elseif ($_POST["send"][0] == "save") {
//                 // freigabe-test
//                 if ( $specialvars["content_release"] == -1 ) {
//                     if ( $_POST["release_mark"] == -1 ) {
//                         $hide = ",hide=-2";
//                     } else {
//                         $hide = ",hide=-1";
//                     }
//                 } else {
//                     $hide = "";
//                 }
//                 $sql = "UPDATE ". SITETEXT ." SET
//                                     ebene = '".$ebene."',
//                                     kategorie = '".$kategorie."',
//                                     crc32 = '".$specialvars["crc32"]."',
//                                     html = '0',
//                                     content = '".$form_values["content"]."',
//                                     changed = '".date("Y-m-d H:i:s")."',
//                                     bysurname = '".$_SESSION["surname"]."',
//                                     byforename = '".$_SESSION["forename"]."',
//                                     byemail = '".$_SESSION["email"]."',
//                                     byalias = '".$_SESSION["alias"]."'
//                                     ".$hide."
//                               WHERE lang = '".$environment["language"]."'
//                                 AND label ='".$environment["parameter"][3]."'
//                                 AND tname ='".$environment["parameter"][2]."'
//                                 AND version ='".$form_values["version"]."'";
//             } elseif ($_POST["send"][0] == "cancel") {
//                 unset($_SESSION["wizard_content"]);
//             }
//             if ( $result  = $db -> query($sql) ) {
//                 unset($_SESSION["wizard_content"]);
//             }
//             $header = $_SESSION["form_referer"];
//             unset($_SESSION["form_referer"]);
//             header("Location: ".$header);
//
//         }


    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }



    $db -> selectDb(DATABASE,FALSE);



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>