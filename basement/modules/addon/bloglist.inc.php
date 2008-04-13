<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id: bloglist.inc.php $";
  $Script["desc"] = "short description";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    // warnung ausgeben
    if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

    // path fuer die schaltflaechen anpassen
    if ( $cfg["bloglist"]["iconpath"] == "" ) $cfg["bloglist"]["iconpath"] = "/images/default/";

    // label bearbeitung aktivieren
    if ( isset($HTTP_GET_VARS["edit"]) ) {
        $specialvars["editlock"] = 0;
    } else {
        $specialvars["editlock"] = -1;
    }

    // erstellen der crc
    if ( $environment["ebene"] == "" ) {
        $kat = "/".$environment["kategorie"];
    } else {
        $kat = $environment["ebene"]."/".$environment["kategorie"];
    }

    // rechte werden aus der bloged.cfg gelesen
    include $pathvars["moduleroot"]."admin/bloged.cfg.php";

    // herausfinden der id,noetig fuer neueintrag
    include $pathvars["moduleroot"]."libraries/function_menu_convert.inc.php";

    // laden der eigentlichen funktion
    include $pathvars["moduleroot"]."libraries/function_show_blog.inc.php";

    // erstellen der tags die angezeigt werden
    foreach ( $cfg["bloged"]["blogs"][$kat]["tags"] as $key => $value) {
        if ( $value["show"] == 1 ) {
            $tags[$key] = $value["name"];
        }
    }

    $dataloop["list"] = show_blog($kat,$tags,$cfg["bloged"]["blogs"][$kat]["right"],$cfg["bloged"]["blogs"][$kat]["wizard"]);

    // was anzeigen
    if ( $cfg["bloged"]["blogs"][$kat]["own_list_template"] == "" ) {
        $mapping["main"] = "-2051315182.list";
    } else {
        $mapping["main"] = "-2051315182.".$cfg["bloged"]["blogs"][$kat]["own_list_template"];
    }

    // fehlermeldungen
    if ( $HTTP_GET_VARS["error"] != "" ) {
        if ( $HTTP_GET_VARS["error"] == 1 ) {
            $ausgaben["form_error"] = "#(error1)";
        }
    } else {
        $ausgaben["form_error"] = "";
    }

    // unzugaengliche #(marken) sichtbar machen
    if ( isset($HTTP_GET_VARS["edit"]) ) {
        $ausgaben["inaccessible"] = "inaccessible values:<br />";
        $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
    } else {
        $ausgaben["inaccessible"] = "";
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
