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
    // 4: wizardtyp
    // 5: mid

    // erlaubnis bei intrabvv speziell setzen
    $database = $environment["parameter"][1];
    if ( is_array($_SESSION["katzugriff"]) ) {
        if ( in_array("-1:".$database.":".$environment["parameter"][2],$_SESSION["katzugriff"]) ) $erlaubnis = -1;
    }

    if ( is_array($_SESSION["dbzugriff"]) ) {
        if ( in_array($database,$_SESSION["dbzugriff"]) ) $erlaubnis = -1;
    }

    // rausfinden, welcher menupunkt einen unterpunkt bekommen soll
    if ( $environment["parameter"][4] != "" ) {
        $url = tname2path($environment["parameter"][2]);
        #$mid = $environment["parameter"][5];
    } else {
        if ( $_SESSION["REFERER"] != "" && preg_match("/wizard$/",dirname($_SESSION["REFERER"])) ) {
            $url = str_replace(array($pathvars["virtual"],".html"),"",$_SESSION["REFERER"]);
        } else {
            $url = str_replace(array($pathvars["webroot"].$pathvars["virtual"],".html"),"",$_SERVER["HTTP_REFERER"]);
            $_SESSION["REFERER"] = $pathvars["virtual"].$url;
        }
        $point = make_id($url);
        #$mid = $point["mid"];
    }

    if ( $cfg["wizard"]["right"]["add"] == "" ||
        priv_check($url,$cfg["wizard"]["right"]["add"]) || priv_check(tname2path($environment["parameter"][2]),$cfg["wizard"]["right"]["add"]) ||
        priv_check_old("",$cfg["wizard"]["right"]) ||
        $rechte["administration"] == -1 ||
        $erlaubnis == -1 ) {

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        if ( $environment["parameter"][2] == "" || $environment["parameter"][3] == "" ) {
            $hidedata["add_menu"] = array();

            // auswahl fuer die unterschiedlichen Wizardtypen
            $mark = 0;
            $user_level = "wizards_default";
            if ( priv_check("/".$cfg["wizard"]["subdir"]."/".$cfg["wizard"]["name"],"admin") ) $user_level = "wizards_admin";
            foreach ( $cfg["wizard"]["wizardtyp"] as $key=>$value ) {
                if ( $value["show_add"] === False ) continue;
                $checked = "";
                if ( $key == $environment["parameter"][4] ) {
                    $check = " checked=\"checked\"";
                    $mark = -1;
                }
                $dataloop[$user_level][] = array(
                    "value" => $key,
                     "name" => "#(wiz_".$key.")",
                     "link" => $cfg["wizard"]["basis"]."/default_content/".$key.".html",
                    "check" => $check,
                );
                if ( isset($_GET["edit"]) ) $ausgaben["inaccessible"] .= "# (wiz_".$key.") #(wiz_".$key.")<br />";
            }
            if ( $mark == 0 ) $dataloop[$user_level][0]["check"] = " checked=\"checked\"";

            // form options holen
            $form_options = form_options(eCRC("/admin/menued").".add");

            // fehlermeldungen
            $ausgaben["form_error"] = $_SESSION["form_error"]["desc"];

            // form elememte bauen
            $element = array_merge(form_elements( "site_menu", $form_values ), form_elements( "site_menu_lang", $form_values ));

            // form elemente erweitern
            if ( $element["extend"] != "" ) $hidedata["extend"] = array();
            if ( is_array($_SESSION["form_error"]) ) {
                foreach ( $_SESSION["form_error"]["post"] as $key=>$value ) {
                    $element[$key] = str_replace("value=\"\"","value=\"".$value."\"",$element[$key]);
                }
                unset($_SESSION["form_error"]);
            }

            // freigabe-test
            if ( $specialvars["content_release"] == -1 ) {
                $hidedata["add_menu"]["hide"] = -1;
            } else {
                $hidedata["add_menu"]["hide"] = "";
            }

            //wohin schicken
            $ausgaben["form_aktion"] = $pathvars["virtual"]."/admin/menued/add,".$point["mid"].",,verify.html";
            $ausgaben["refid"] = $point["mid"];

        } elseif ( $environment["parameter"][1] != "" && $environment["parameter"][2] != "" && $environment["parameter"][3] != "" && $environment["parameter"][4] != "" ) {

            if ( priv_check($url,$cfg["wizard"]["right"]["edit"]) || priv_check($url,$cfg["wizard"]["right"]["publish"]) ) {

                // test, ob menue-punkt schon vorhanden ist
                $db->selectDb($environment["parameter"][1],FALSE);
                $sql = "SELECT version, html, content, changed, byalias
                        FROM ". SITETEXT ."
                        WHERE lang = '".$environment["language"]."'
                        AND label ='".$environment["parameter"][3]."'
                        AND tname ='".$environment["parameter"][2]."'
                    ORDER BY version DESC";
                $result = $db -> query($sql);
    
                if ( $db -> num_rows($result) == 0 ) {
                    $sql = "SELECT version, html, content, changed, byalias
                            FROM ". SITETEXT ."
                            WHERE lang = '".$environment["language"]."'
                            AND label ='".$environment["parameter"][3]."'
                            AND tname ='".eCRC($environment["ebene"]."/default_content").".".$environment["parameter"][4]."'
                        ORDER BY version DESC
                            LIMIT 0,1";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result = $db -> query($sql);
                    $data = $db -> fetch_array($result,1);
                    $content = "[!]wizard:".$environment["parameter"][4]."[/!]\n\n".$data["content"];
    
                    if ( $specialvars["content_release"] == -1 ) {
                        $sqla = ", status";
                        $sqlb = ", 0";
                    }
    
                    $sql = "INSERT INTO ". SITETEXT ."
                                        (lang, label, tname, version,
                                        ebene, kategorie,
                                        crc32, html, content,
                                        changed, bysurname, byforename, byemail, byalias".$sqla.")
                                    VALUES (
                                            '".$environment["language"]."',
                                            '".$environment["parameter"][3]."',
                                            '".$environment["parameter"][2]."',
                                            '1',
                                            '".str_replace($pathvars["virtual"],"",dirname($_SESSION["form_referer"]))."',
                                            '".str_replace(".html","",basename($_SESSION["form_referer"]))."',
                                            '".$specialvars["crc32"]."',
                                            '0',
                                            '".$content."',
                                            '".date("Y-m-d H:i:s")."',
                                            '".$_SESSION["surname"]."',
                                            '".$_SESSION["forename"]."',
                                            '".$_SESSION["email"]."',
                                            '".$_SESSION["alias"]."'
                                            ".$sqlb.")";
    
                    if ( $result = $db -> query($sql) ) {
                        $header = $cfg["wizard"]["basis"]."/show,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].".html";
                    } else {
                        $header = $_SESSION["form_referer"];
                    }
                    unset($_SESSION["REFERER"]);
                    header("Location: ".$header);
                }
            } else {
                $header = $_SESSION["form_referer"];
                unset($_SESSION["REFERER"]);
                header("Location: ".$header);
            }

            $db -> selectDb(DATABASE,FALSE);
        }

        // was anzeigen
        $mapping["main"] = "wizard-add";

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>