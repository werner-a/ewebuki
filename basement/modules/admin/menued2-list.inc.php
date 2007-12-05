<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "menued - liste";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

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

    if ( priv_check("/".$cfg["subdir"]."/".$cfg["name"],$cfg["right"]) ||
        priv_check_old("",$cfg["right"]) ) {

        // nur zum testen
        if ( $rechte[$cfg["right_admin"]] == -1 ) {
            $rechte[$cfg["right"]] = -1;
        }

        $modify  = array (
            "sort"      => array("","#(button_desc_sort)"),
            "jump"      => array("", "#(button_desc_jump)"),
            "add"       => array("", "#(button_desc_add)", $cfg["right"]),
            "edit"      => array("", "#(button_desc_edit)", $cfg["right"]),
            "delete"    => array("", "#(button_desc_delete)", $cfg["right"]),
            "up"        => array("sort,", "#(button_desc_up)", $cfg["right"]),
            "down"      => array("sort,", "#(button_desc_down)", $cfg["right"]),
            "move"      => array("", "#(button_desc_move)", $cfg["right"]),
        );

        // bei eingeschalteten content recht wird button hinzugefuegt
        if ( $specialvars["security"]["enable"] == -1 ) {
            $modify["rights"] = array("", "#(button_desc_right)", $cfg["right"]);
        }

        // array umdrehen
        $modify = array_reverse($modify);

        // variablen u. arrays definieren
        $stop["nop"] = "nop";
        $positionArray["nop"] = "nop";
        $ausgaben["path"] = "";
        $ausgaben["back"] = "";

        if ( $environment["parameter"][1] == "" ) {
            $_SESSION["menued_id"] = "";
            $_SESSION["menued_opentree"] = "";
            $_SESSION["menued_design"] = "";
        } else {
            $_SESSION["menued_id"] = $environment["parameter"][1];
        }

        if ( $_SESSION["menued_id"] != "" ) {

            // explode des parameters
            $opentree = explode("-",$_SESSION["menued_opentree"]);

            // was muss geschlossen werden ?!?!?
            foreach ( $opentree as $key => $value ) {
                if ( $value != "" ) {
                    delete($value,$value);
                }
                if ( $stop != "" ) {
                    if ( in_array($value,$stop) ) {
                        unset ($opentree[$key]);
                    }
                }
            }

            // punkt oeffnen
            if ( !in_array($_SESSION["menued_id"],$stop) ) {
                $opentree[] = $_SESSION["menued_id"];
            }

            // link bauen und positionArray bauen
            foreach ( $opentree as $key => $value ) {
                $treelink == "" ? $trenner = "" : $trenner = "-";
                $treelink .= $trenner.$value;
                if ( $value != "" ) {
                    locate($value);
                }
            }

            $_SESSION["menued_design"] = $design;
        } else {
            $positionArray[0] = 0;
        }

        // multidesign - verwalten nur ein TEST ( ueberhaupt sinnvoll ??? )
        $ausgaben["design"] = "";
        if ( $cfg["design"] == "" ) {
            $design = $cfg["design_available"][0];
            if ( $_SESSION["menued_design"] != "" ) {
                $design = $_SESSION["menued_design"];
            }
            // design - umschalter 
            foreach ( $cfg["design_available"] as $value ) {
                if ( $value != $design ) {
                    if ( $_SESSION["menued_design"] == "" ) { 
                        $ausgaben["design"] = "<a href=\"".str_replace("list.","list,,,".$value.".",$pathvars["uri"])."\">".$value."</a>";
                    } else {
                        $ausgaben["design"] = "<a href=\"".str_replace($_SESSION["menued_design"],$value,$pathvars["uri"])."\">".$value."</a>";
                    }
                }
            }
        } else {
            $design = $cfg["design"];
        }

        $ausgaben["show_menu"] .= sitemap(0, "menued", $modify,"");

        // fehlermeldungen
        if ( $HTTP_GET_VARS["error"] != "" ) {
            if ( $HTTP_GET_VARS["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["renumber"] = "<a href=\"".$cfg["basis"]."/sort,all,nop,0.html\">#(renumber)</a>";
        $ausgaben["new"] = "<a href=\"".$cfg["basis"]."/add,0.html\">g(new)</a>";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".list";
        $mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (disabled) #(disabled)<br />";
            $ausgaben["inaccessible"] .= "# (enabled) #(enabled)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
