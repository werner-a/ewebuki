<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "info-markt modify";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    phpWEBkit - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of phpWEBkit

    phpWEBkit is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    phpWEBkit is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with phpWEBkit; If you did not, you may download a copy at:

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

    // warning ausgeben
    if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

    if ( $environment["parameter"][1] == "add" ) {

        if ( count($HTTP_POST_VARS) == 0 ) {
            // spezielle default values setzen
            $form_values["ivon"] = "0000-01-01 00:00:00";
            $form_values["ibis"] = "0000-01-01 00:00:00";
            $form_values["iauth_id"] = $HTTP_SESSION_VARS["uid"];
        } else {
            $form_values = $HTTP_POST_VARS;
        }

        // form options holen
        $form_options = form_options("1943315524.modify");

        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );

        // form elemente erweitern
        $element["ibereich"] = str_replace("ibereich\"", "ibereich\" value=\"".$cfg["ebene"]["zwei"]."\"", $element["ibereich"]);

        // ce editor bauen
        $ausgaben["tn"] = makece("modify", "itext", $form_values["itext"]);

        // was anzeigen
        # automatik geht $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];
        #$mapping["main"] = "1943315524.modify";
        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["basis"]."/".$cfg["ebene"]["zwei"]."/modify,add,verify.html";

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_GET_VARS["referer"] != "" ) {
            $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        if ( $environment["parameter"][2] == "verify" ) {

            // form eingaben prüfen
            form_errors( $form_options, $form_values );

            // ohne fehler sql bauen und ausfuehren
            if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" || $HTTP_POST_VARS["upload"] != "" ) ) {
                $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "upload", "st", "form_referer", "ierstellt", "igeaendert", "ivon", "ibis" );
                foreach($form_values as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                         if ( $sqla != "" ) $sqla .= ",";
                         $sqla .= " ".$name;
                         if ( $sqlb != "" ) $sqlb .= ",";
                         $sqlb .= " '".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                $change = array( "ierstellt", "igeaendert", "ivon", "ibis" );
                foreach( $change as $value ) {
                    $$value = $HTTP_POST_VARS[$value];
                    $$value = substr($$value,6,4)."-".substr($$value,3,2)."-".substr($$value,0,2)." ".substr($$value,11,9);
                    $sqla .= ", ".$value;
                    $sqlb .= ", '".$$value."'";
                    #echo $$value.":".$value."<br>";
                }
                #$sqla .= ", iauth_id";
                #$sqlb .= ", '".$HTTP_SESSION_VARS["uid"]."'";

                $sql = "insert into ".$cfg["db"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                $result  = $db -> query($sql);
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                if ( $HTTP_POST_VARS["image"] == "add" || $HTTP_POST_VARS["upload"] > 0 ) {
                    session_register("referer");
                    $HTTP_SESSION_VARS["referer"] = $ausgaben["form_referer"];
                    session_register("return");
                    #$HTTP_SESSION_VARS["return"] = str_replace(",verify", "", $pathvars["requested"]);
                    $HTTP_SESSION_VARS["return"] = str_replace("add,verify", "edit,".$db->lastid(), $pathvars["requested"]);
                    if ( $HTTP_POST_VARS["upload"] > 0 ) {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/select.html?anzahl=".$HTTP_POST_VARS["upload"]);
                    } else {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/list.html");
                    }
                } else {
                    header("Location: ".$ausgaben["form_referer"]);
                }
            }
        }

    } elseif ( $environment["parameter"][1] == "replace" ) {

        if ( count($HTTP_POST_VARS) == 0 ) {
            $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $form_values = $db -> fetch_array($result,$nop);
        } else {
            $form_values = $HTTP_POST_VARS;
        }

        // spezielle default values setzen
        $form_values["igeaendert"] = date("Y.m.d G:i:s");
        if ( $form_values["iparent"] == 0 ) {
            $iparent = $environment["parameter"][3];
        } else {
            $iparent = $form_values["iparent"];
        }

        // form options holen
        $form_options = form_options("1943315524.modify");

        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );

        // form elemente erweitern
        $element["ibereich"] = str_replace("ibereich\"", "ibereich\" value=\"".$cfg["ebene"]["zwei"]."\"", $element["ibereich"]);

        // ce editor bauen
        $ausgaben["tn"] = makece("modify", "itext", $form_values["itext"]);

        // was anzeigen
        # automatik geht $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];
        #$mapping["main"] = "1943315524.modify";
        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["basis"]."/".$cfg["ebene"]["zwei"]."/modify,replace,verify,".$environment["parameter"][2].".html";

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_GET_VARS["referer"] != "" ) {
            $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        if ( $environment["parameter"][2] == "verify" ) {

            // form eingaben prüfen
            form_errors( $form_options, $form_values );

            // ohne fehler sql bauen und ausfuehren
            if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" || $HTTP_POST_VARS["upload"] != "" ) ) {
                $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "upload", "st", "form_referer", "ierstellt", "igeaendert", "ivon", "ibis", "iparent" );
                foreach($form_values as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                         if ( $sqla != "" ) $sqla .= ",";
                         $sqla .= " ".$name;
                         if ( $sqlb != "" ) $sqlb .= ",";
                         $sqlb .= " '".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                $change = array( "ierstellt", "igeaendert", "ivon", "ibis" );
                foreach( $change as $value ) {
                    $$value = $HTTP_POST_VARS[$value];
                    $$value = substr($$value,6,4)."-".substr($$value,3,2)."-".substr($$value,0,2)." ".substr($$value,11,9);
                    $sqla .= ", ".$value;
                    $sqlb .= ", '".$$value."'";
                    #echo $$value.":".$value."<br>";
                }
                #$sqla .= ", iaktiv, iparent";
                #$sqlb .= ", '-1', '".$iparent."'";
                $sqla .= ", iparent";
                $sqlb .= ", '".$iparent."'";

                $sql = "insert into ".$cfg["db"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                $result  = $db -> query($sql);
                $ersatz = $db->lastid();

                if ( $result ) {
                    $sql = "update ".$cfg["db"]["entries"]." SET iarchiv = '-1' WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][3]."'";
                    $result  = $db -> query($sql);
                }
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                if ( $HTTP_POST_VARS["image"] == "add" || $HTTP_POST_VARS["upload"] > 0 ) {
                    session_register("referer");
                    $HTTP_SESSION_VARS["referer"] = $ausgaben["form_referer"];
                    session_register("return");
                    #$HTTP_SESSION_VARS["return"] = str_replace(",verify", "", $pathvars["requested"]);
                    $HTTP_SESSION_VARS["return"] = str_replace("replace,verify,".$environment["parameter"][3], "edit,".$ersatz, $pathvars["requested"]);
                    if ( $HTTP_POST_VARS["upload"] > 0 ) {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/select.html?anzahl=".$HTTP_POST_VARS["upload"]);
                    } else {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/list.html");
                    }
                } else {
                    header("Location: ".$ausgaben["form_referer"]);
                }
            }
        }

    } elseif ( $environment["parameter"][1] == "edit" ) {

        if ( count($HTTP_POST_VARS) == 0 ) {
            $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $form_values = $db -> fetch_array($result,$nop);
        } else {
            $form_values = $HTTP_POST_VARS;
        }
        // spezielle default values setzen
        $form_values["igeaendert"] = date("Y.m.d G:i:s");

        // form otions holen
        $form_options = form_options("1943315524.modify");
        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );
        // form elemente erweitern
        #$element["itext"] = $form_values["itext"];

        // ce editor bauen
        $ausgaben["tn"] = makece("modify", "itext", $form_values["itext"]);

        // was anzeigen
        # automatik geht $mapping["main"] = "1943315524.modify";
        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["basis"]."/".$cfg["ebene"]["zwei"]."/modify,edit,".$environment["parameter"][2].",verify.html";

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_GET_VARS["referer"] != "" ) {
            $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        if ( $environment["parameter"][3] == "verify" ) {

            // form eigaben prüfen
            form_errors( $form_options, $form_values );

            // vogelwilde regex die viel arbeit erspart hat
            #preg_match_all("/_([0-9]*)./",$form_values["itext"],$found);
            #echo crc32($environment["ebene"]);
            #echo "<pre>";
            #print_r($found);
            #echo "</pre>";

            // ohne fehler sql bauen und ausfuehren
            if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" || $HTTP_POST_VARS["upload"] != "" ) ){

                $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "upload", "st", "form_referer", "ierstellt", "igeaendert", "ivon", "ibis" );
                #bugfix# foreach($HTTP_POST_VARS as $name => $value) {
                foreach($form_values as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                        if ( $sqla != "" ) $sqla .= ", ";
                        $sqla .= $name."='".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                $change = array( "ierstellt", "igeaendert", "ivon", "ibis" );
                foreach( $change as $value ) {
                    $$value = $HTTP_POST_VARS[$value];
                    $$value = substr($$value,6,4)."-".substr($$value,3,2)."-".substr($$value,0,2)." ".substr($$value,11,9);
                    $sqla .= ", ".$value."='".$$value."'";
                    #echo $$value.":".$value."<br>";
                }

                $sql = "update ".$cfg["db"]["entries"]." SET ".$sqla." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                $result  = $db -> query($sql);
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                if ( $HTTP_POST_VARS["image"] == "add" || $HTTP_POST_VARS["upload"] > 0 ) {
                    session_register("referer");
                    $HTTP_SESSION_VARS["referer"] = $ausgaben["form_referer"];
                    session_register("return");
                    $HTTP_SESSION_VARS["return"] = str_replace(",verify", "", $pathvars["requested"]);
                    if ( $HTTP_POST_VARS["upload"] > 0 ) {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/select.html?anzahl=".$HTTP_POST_VARS["upload"]);
                    } else {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/list.html");
                    }
                } else {
                    header("Location: ".$ausgaben["form_referer"]);
                }
            }
        }
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
