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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $rechte[$cfg["fileed"]["right"]] == "" || $rechte[$cfg["fileed"]["right"]] == -1 ) {

        // funktions bereich fuer erweiterungen
        // ***

        if ( count($_SESSION["file_memo"]) > 0 ) {
            $environment["parameter"][1] = current($_SESSION["file_memo"]);
        } else  {
            header("Location: ".$cfg["fileed"]["basis"]."/list.html");
            exit();
        }

        ### put your code here ###

        // wird die datei im content verwendet?
        $old = "\_".$environment["parameter"][1].".";
        $new = "/".$environment["parameter"][1]."/";
        #$new = "=".$cfg["file"]["base"]["webdir"].$data["ffart"]."/".$data["fid"]."/";
        $sql = "SELECT *
                  FROM ".$cfg["fileed"]["db"]["content"]["entries"]."
                 WHERE ".$cfg["fileed"]["db"]["content"]["content"]." LIKE '%".$old."%'
                    OR ".$cfg["fileed"]["db"]["content"]["content"]." LIKE '%".$new."%'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $num_rows = $db -> num_rows($result);

        // +++
        // funktions bereich fuer erweiterungen

        if ( $num_rows > 0 ) {

            // was anzeigen
            $mapping["main"] = eCRC($environment["ebene"]).".list";
            $mapping["navi"] = "leer";

            // wohin schicken
            header("Location: ".$cfg["fileed"]["basis"]."/list.html?error=1");

        } else {

            // datensatz holen
            $sql = "SELECT *
                      FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                     WHERE ".$cfg["fileed"]["db"]["file"]["key"]."='".$environment["parameter"][1]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,$nop);
            $ausgaben["form_id1"] = $data["fid"];
            #$ausgaben["field1"] = $data["field1"];
            #$ausgaben["field2"] = $data["field2"];
            $ausgaben["ffname"] = $data["ffname"];
            $ausgaben["ffart"] = $data["ffart"];

            // funktions bereich fuer erweiterungen
            // ***

            ### put your code here ###

            if ( $_SESSION["uid"] != $data["fuid"] && in_array( $environment["kategorie"], $cfg["fileed"]["restrict"]) ) { # nur eigene dateien duerfen gel�scht werden
                header("Location: ".$cfg["fileed"]["basis"]."/list.html?error=2");
                exit();
            }


            /* z.B. evtl. verknuepfte datensatze holen
            $sql = "SELECT *
                      FROM ".$cfg["fileed"]["db"]["more"]["entries"]."
                     WHERE ".$cfg["fileed"]["db"]["more"]["key"]." ='".$environment["parameter"][1]."'";
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
            $ausgaben["form_aktion"] = $cfg["fileed"]["basis"]."/delete,".$environment["parameter"][1].".html";
            $ausgaben["form_break"] = $cfg["fileed"]["basis"]."/list.html";

            // hidden values
            $ausgaben["form_hidden"] = "";
            $ausgaben["form_delete"] = "true";

            // was anzeigen
            #$mapping["main"] = eCRC($environment["ebene"]).".delete";
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
                    $sql = "DELETE FROM ".$cfg["fileed"]["db"]["more"]["entries"]."
                                  WHERE ".$cfg["fileed"]["db"]["more"]["key"]." = '".$HTTP_POST_VARS["id2"]."'";
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

                    $type = $cfg["file"]["filetyp"][$data["ffart"]];
                    if ( $type == "img" ) {
                        $art = array( "o" => "img", "s" => "img", "m" => "img", "b" => "img", "tn" => "tn" );
                        foreach ( $art as $key => $value ) {
                            $return = unlink($cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["base"]["pic"][$key].$value."_".$id.".".$data["ffart"]);
                            ### sollte evtl. anderst gel�st werden, existiert nur ein file nicht
                            ### laesst sich der datensatz nie l�schen!
                            if ( $return != 1 ) {
                                #$ausgaben["form_error"] = "error delete files";
                                break;
                            }
                        }
                    } else {
                        $return = unlink($cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["fileopt"][$type]["name"]."_".$id.".".$data["ffart"]);
                        if ( $return != "1" ) {
                            $ausgaben["form_error"] = "error delete file";
                        }
                    }
                    unset ($_SESSION["file_memo"][$id]);
                }

                // datensatz loeschen
                if ( $ausgaben["form_error"] == "" ) {
                    $sql = "DELETE FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                                  WHERE ".$cfg["fileed"]["db"]["file"]["key"]."='".$HTTP_POST_VARS["id1"]."';";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result1)<br />");
                }
                // +++

                // wohin schicken
                if ( $ausgaben["form_error"] == "" ) {
                    header("Location: ".$cfg["fileed"]["basis"]."/delete.html");
                }
            }
            // +++
            // das loeschen wurde bestaetigt, loeschen!
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
