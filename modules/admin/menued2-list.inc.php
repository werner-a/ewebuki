<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "menued - liste";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon <wa@chaos.de>

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["menued"]["right"] == "" || priv_check('', $cfg["menued"]["right"]) || ($cfg["auth"]["menu"]["menued"][2] == -1 &&  priv_check('', $cfg["menued"]["right"],$specialvars["dyndb"] ) ) ) {

        // array umdrehen
        $modify = array_reverse($cfg["menued"]["modify"]);

        // variablen u. arrays definieren
        $stop["nop"] = "nop";
        $positionArray["nop"] = "nop";
        $ausgaben["path"] = "";
        $ausgaben["back"] = "";
        if (!isset($ausgaben["show_menu"]) ) $ausgaben["show_menu"] = null;

        if ( empty($environment["parameter"][1]) ) $environment["parameter"][1] = 0;
        if ( $environment["parameter"][1] == 0 ) {
            $_SESSION["menued_id"] = "";
            $_SESSION["menued_opentree"] = "";
            $_SESSION["menued_design"] = "";
            $check_parameter = 0;
        } else {
            $_SESSION["menued_id"] = $environment["parameter"][1];
            $check_parameter = $environment["parameter"][1];
        }

        if ( !empty($_SESSION["menued_id"]) ) {

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
            $treelink = null;
            foreach ( $opentree as $key => $value ) {
                $treelink == "" ? $trenner = "" : $trenner = "-";
                $treelink .= $trenner.$value;
                if ( $value != "" ) {
                    locate($value);
                }
            }

            //$_SESSION["menued_design"] = $design; design ist hier immer leer???
        } else {
            $positionArray[0] = 0;
        }

        // multidesign - verwalten nur ein TEST ( ueberhaupt sinnvoll ??? )
        $ausgaben["design"] = "";
        if ( $cfg["menued"]["design"] == "" ) {
            $design = $cfg["menued"]["design_available"][0];
            if ( $_SESSION["menued_design"] != "" ) {
                $design = $_SESSION["menued_design"];
            }
            // design - umschalter
            foreach ( $cfg["menued"]["design_available"] as $value ) {
                if ( $value != $design ) {
                    if ( $_SESSION["menued_design"] == "" ) {
                        $ausgaben["design"] = "<a href=\"".str_replace("list.","list,,,".$value.".",$pathvars["uri"])."\">".$value."</a>";
                    } else {
                        $ausgaben["design"] = "<a href=\"".str_replace($_SESSION["menued_design"],$value,$pathvars["uri"])."\">".$value."</a>";
                    }
                }
            }
        } else {
            $design = $cfg["menued"]["design"];
        }

        $ausgaben["show_menu"] .= sitemap(0, "menued", "menued", $modify,"");

        // fehlermeldungen
        if ( isset($_GET["error"]) ) {
            if ( $_GET["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["renumber"] = "<a href=\"".$cfg["menued"]["basis"]."/sort,all,nop,0.html\">#(renumber)</a>";

        $ausgaben["new"] = null; $ausgaben["root"] = null;
        if ( priv_check( make_ebene($check_parameter),$cfg["menued"]["modify"]["add"][2],$specialvars["dyndb"]) ) {
            $ausgaben["new"] .= "<a href=\"".$cfg["menued"]["basis"]."/add,".$check_parameter.",".@$array["refid"].".html\">g(new)</a>";
            $ausgaben["root"] = "";
            if ( $specialvars["security"]["new"] == -1 && priv_check("/",$cfg["menued"]["modify"]["rights"][2],$specialvars["dyndb"]) && $environment["parameter"][1] == "0" ) {
                $ausgaben["root"] ="<ul class=\"menued\"><li><a style=\"float:right\" href=\"".$pathvars["virtual"]."/".$cfg["menued"]["subdir"]."/righted/edit,0.html\"><img style=\"float:right\" src=\"/images/default/rights.png\" alt=\"righted\" title=\"RIGHTED\" width=\"24\" height=\"18\"></img></a><span>/</span></li></ul>";
            }
        }

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".list";
        $mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
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