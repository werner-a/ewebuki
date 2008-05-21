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
    if ( $_GET["transform"] == "mark_content" && priv_check("/".$cfg["wizard"]["subdir"]."/".$cfg["wizard"]["name"],"admin") ) {
        $sql = "SELECT *
                  FROM site_text
              ORDER BY lang, label, tname, version DESC";
        $result = $db -> query($sql);
        $unique = "";
        while ( $data = $db -> fetch_array($result,1) ) {
            if ( $data["status"] < 0 ) continue;
            if ( $unique != $data["lang"]."::".$data["label"]."::".$data["tname"]
               && $data["status"] == 0 ) {
                $sql = "UPDATE site_text
                           SET status=1
                         WHERE lang='".$data["lang"]."'
                           AND label='".$data["label"]."'
                           AND tname='".$data["tname"]."'
                           AND version=".$data["version"];
                $res = $db -> query($sql);
            }
            $unique = $data["lang"]."::".$data["label"]."::".$data["tname"];
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
    /* fehlende datenbank */
    if ( $environment["parameter"][1] != "" ) {
        $db->selectDb($database,FALSE);
    } else {
        $reload = -1;
    }
    $environment["parameter"][1] = $db->getDb();
    /* fehlender tname */
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
    /* fehlende label-beizeichnung */
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

        // freizugebene seiten finden
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
                           AND tname ='".$environment["parameter"][2]."'
                           AND status>=0";
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,1);
                $next_version = $data["max_version"] + 1;
                // alle "unnoetigen" versionen loeschen
                $sql = "DELETE
                          FROM ". SITETEXT ."
                         WHERE lang = '".$environment["language"]."'
                           AND label ='".$environment["parameter"][3]."'
                           AND tname ='".$environment["parameter"][2]."'
                           AND status<0
                           AND version<>".$environment["parameter"][5];
                $result = $db -> query($sql);
                // bisher aktuelle inhalte historisieren
                $sql = "UPDATE ". SITETEXT ." SET
                                status=0
                         WHERE lang = '".$environment["language"]."'
                           AND label ='".$environment["parameter"][3]."'
                           AND tname ='".$environment["parameter"][2]."'
                           AND status>=0";
                $result  = $db -> query($sql);
                // freigegebenen Datensatz aktualisieren
                $sql = "UPDATE ". SITETEXT ."
                           SET version=".$next_version.",
                               status=1
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
                           SET status=-1
                         WHERE lang = '".$environment["language"]."'
                           AND label ='".$environment["parameter"][3]."'
                           AND tname ='".$environment["parameter"][2]."'
                           AND version=".$environment["parameter"][5];
                $result = $db -> query($sql);
            }

            if ( $_SESSION["form_referer"] != "" ) {
                $header = $_SESSION["form_referer"];
                unset($_SESSION["form_referer"]);
            } else {
                $header = $_SERVER["HTTP_REFERER"];
            }
            header("Location: ".$header);
        }

        // was anzeigen
        $mapping["main"] = "wizard-release";
        #$mapping["navi"] = "leer";

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }



    $db -> selectDb(DATABASE,FALSE);



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>