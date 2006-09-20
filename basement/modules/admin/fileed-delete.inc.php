<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - delete funktion";
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

    if ( $rechte[$cfg["right"]] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich fuer erweiterungen
        // ***

        if ( count($_SESSION["images_memo"]) > 0 ) {
            $environment["parameter"][1] = current($_SESSION["images_memo"]);
        } else  {
            header("Location: ".$cfg["basis"]."/list.html");
        }

        ### put your code here ###

        /* z.B. evtl. auf verknuepften datensatz pruefen
        $sql = "SELECT ".$cfg["db"]["menu"]["key"]."
                  FROM ".$cfg["db"]["menu"]["entries"]."
                 WHERE refid='".$environment["parameter"][1]."'";
        $result = $db -> query($sql);
        $num_rows = $db -> num_rows($result);
        */

        // +++
        // funktions bereich fuer erweiterungen

        if ( $num_rows > 0 ) {

            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".list";
            $mapping["navi"] = "leer";

            // wohin schicken
            header("Location: ".$cfg["basis"]."/list.html?error=1");

        } else {

            // datensatz holen
            $sql = "SELECT *
                      FROM ".$cfg["db"]["file"]["entries"]."
                     WHERE ".$cfg["db"]["file"]["key"]."='".$environment["parameter"][1]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,$nop);
            $ausgaben["form_id1"] = $data["fid"];
            #$ausgaben["field1"] = $data["field1"];
            #$ausgaben["field2"] = $data["field2"];
            $ausgaben["ffname"] = $data["ffname"];

            // funktions bereich fuer erweiterungen
            // ***

            ### put your code here ###

            /* z.B. evtl. verknuepfte datensatze holen
            $sql = "SELECT *
                      FROM ".$cfg["db"]["more"]["entries"]."
                     WHERE ".$cfg["db"]["more"]["key"]." ='".$environment["parameter"][1]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            while ( $data2 = $db -> fetch_array($result,$nop) ) {
                if ( $ids != "" ) $ids .= ",";
                $ids .= $array["id"];
                $ausgaben["field3"] .= $array["field1"]." ";
                $ausgaben["field3"] .= $array["field2"]."<br />";
            }
            $ausgaben["form_id2"] = $ids;
            */

            // +++
            // funktions bereich fuer erweiterungen


            // page basics
            // ***

            // fehlermeldungen
            $ausgaben["form_error"] = "";

            // navigation erstellen
            $ausgaben["form_aktion"] = $cfg["basis"]."/delete,".$environment["parameter"][1].".html";
            $ausgaben["form_break"] = $cfg["basis"]."/list.html";

            // hidden values
            $ausgaben["form_hidden"] = "";
            $ausgaben["form_delete"] = "true";

            // was anzeigen
            #$mapping["main"] = crc32($environment["ebene"]).".delete";
            #$mapping["navi"] = "leer";

            // unzugaengliche #(marken) sichtbar machen
            // ***
            if ( isset($HTTP_GET_VARS["edit"]) ) {
                $ausgaben["inaccessible"] = "inaccessible values:<br />";
                $ausgaben["inaccessible"] .= "# (error_result1) #(error_result1)<br />";
                $ausgaben["inaccessible"] .= "# (error_result2) #(error_result2)<br />";
            } else {
                $ausgaben["inaccessible"] = "";
            }
            // +++
            // unzugaengliche #(marken) sichtbar machen

            // wohin schicken
            #n/a

            // +++
            // page basics


            // das loeschen wurde bestaetigt, loeschen!
            // ***
            if ( $HTTP_POST_VARS["delete"] != ""
                && $HTTP_POST_VARS["send"] != "" ) {

                // evtl. zusaetzlichen datensatz loeschen
                if ( $HTTP_POST_VARS["id2"] != "" ) {
                    // funktions bereich fuer erweiterungen
                    // ***

                    ### put your code here ###

                    /* z.B. evtl. verknuepfte datensatze loeschen
                    $sql = "DELETE FROM ".$cfg["db"]["more"]["entries"]."
                                  WHERE ".$cfg["db"]["more"]["key"]." = '".$HTTP_POST_VARS["id2"]."'";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result2)<br />");
                    */

                    // +++
                    // funktions bereich fuer erweiterungen
                }

                // datei loeschen
                if ( $ausgaben["form_error"] == "" ) {

                    $id = $HTTP_POST_VARS["id1"];

                    $sql = "SELECT ffart, fuid FROM site_file WHERE fid =".$id;
                    $result = $db -> query($sql);
                    $data = $db -> fetch_array($result,$nop);

                    $type = $cfg["filetyp"][$data["ffart"]];
                    if ( $type == "img" ) {
                        $art = array( "o" => "img", "s" => "img", "m" => "img", "b" => "img", "tn" => "tn" );
                        foreach ( $art as $key => $value ) {
                            $return = unlink($cfg["fileopt"][$type]["path"].$pathvars["filebase"]["pic"][$key].$value."_".$id.".".$data["ffart"]);
                            ### sollte evtl. anderst gelöst werden, existiert nur ein file nicht
                            ### laesst sich der datensatz nie löschen!
                            if ( $return != 1 ) {
                                #$ausgaben["form_error"] = "error delete files";
                                break;
                            }
                        }
                    } else {
                        $return = unlink($cfg["fileopt"][$type]["path"].$cfg["fileopt"][$type]["name"]."_".$id.".".$data["ffart"]);
                        if ( $return != "1" ) {
                            $ausgaben["form_error"] = "error delete file";
                        }
                    }
                    unset ($_SESSION["images_memo"][$id]);
                }

                // datensatz loeschen
                if ( $ausgaben["form_error"] == "" ) {
                    $sql = "DELETE FROM ".$cfg["db"]["file"]["entries"]."
                                  WHERE ".$cfg["db"]["file"]["key"]."='".$HTTP_POST_VARS["id1"]."';";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result1)<br />");
                }
                // +++

                // wohin schicken
                if ( $ausgaben["form_error"] == "" ) {
                    header("Location: ".$cfg["basis"]."/delete.html");
                }
            }
            // +++
            // das loeschen wurde bestaetigt, loeschen!
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }


    /*
    if ( $environment["parameter"][1] == "delete" ) {
        foreach ($_SESSION["images_memo"] as $key => $value) {
            $sql = "SELECT ffart,fuid FROM site_file WHERE fid =".$value;
            $result = $db -> query($sql);
            $file_art = $db -> fetch_array($result,$nop);
            if ($file_art["fuid"] == $_SESSION["uid"]) {
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
                    #if ($error == "11111") {
                        $result = $db -> query($sql);
                    #}
                }
                unset ($_SESSION["images_memo"][$value]);
            } else {
                $ausgaben["form_error"] .= "Fehler ! Es können nur eigene Dateien gelöscht werden<br>";
                unset ($_SESSION["images_memo"][$environment["parameter"][2]]);
            }
        }
        #$_SESSION["images_memo"] = "";

    }
    */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
