<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $main_script_name = "$Id$";
    $main_script_desc = "main include file";
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

    // path config
    $pathvars["webroot"]   = "http://".$_SERVER["HTTP_HOST"];
    $pathvars["webimages"] = "/images/main"."/";                        # gilt nur fuer select seite
    $pathvars["webcss"]    = "/css"."/";                                # gilt nur fuer select seite

    // site config
    require dirname(dirname(dirname(__FILE__)))."/conf/site.cfg.php";

    // berlios fix
    if ( $pathvars["fileroot"] == "" ) {
        $pathvars["fileroot"] = rtrim($_SERVER["DOCUMENT_ROOT"],"/")."/";
    }

    // subdir support
    if ( $specialvars["subdir"] != "" ) {
        $pathvars["subdir"] = trim($specialvars["subdir"],"/");
        $pathvars["fileroot"] = $pathvars["fileroot"].$pathvars["subdir"]."/";
    }

    $pathvars["basicroot"]  = $pathvars["fileroot"]."basic/";
    $pathvars["addonroot"]  = $pathvars["fileroot"]."modules/";

    $pathvars["libraries"]  = $pathvars["basicroot"]."libraries"."/";
    $pathvars["config"]     = $pathvars["fileroot"]."conf"."/";

    $pathvars["templates"]  = $pathvars["fileroot"]."templates/main"."/";  # gilt nur fuer select seite

    // file config
    require $pathvars["config"]."file.cfg.php";

    // automatic db access
    foreach ( (array)$access as $name => $value ) {
        if ( strpos($_SERVER["SERVER_NAME"],$value["server"]) !== false ) {
            define ('DB_HOST', $access[$name]["host"]);
            define ('DATABASE', $access[$name]["db"]);
            define ('DB_USER', $access[$name]["user"]);
            define ('DB_PASSWORD', $access[$name]["pass"]);
            break;
        }
    }

    // debug array init
    $debugging["ausgabe"] = "";
    if ( $debugging["html_enable"] == -1 ) {
        $debugging["ausgabe"] .= $debugging["char"].$debugging["char"].$debugging["head"].$debugging["char"];
        $debugging["ausgabe"] .= "#### Debug Ausgabe: ####".$debugging["char"];
    }

    // ausgaben array init
    $ausgaben["output"] = "";

    // required libs
    require $pathvars["libraries"]."function_nlreplace.inc.php"; // new line in <br /> wandeln und formatieren
    require $pathvars["libraries"]."function_intelilink.inc.php"; // intelligenter link funktion (kompatibel)
    require $pathvars["libraries"]."function_tagreplace.inc.php"; // tagreplace funktion
    require $pathvars["libraries"]."function_tagremove.inc.php"; // tagremove funktion
    require $pathvars["libraries"]."function_content.inc.php"; // content sprachabhaengig holen
    require $pathvars["libraries"]."function_gerdate.inc.php"; // german date
    require $pathvars["libraries"]."function_form_options.inc.php"; // formular optionen holen
    require $pathvars["libraries"]."function_form_elements.inc.php"; // formular elemente bauen
    require $pathvars["libraries"]."function_form_errors.inc.php"; // formular elemente pruefen
    require $pathvars["libraries"]."function_inhalt_selector.inc.php"; // seiten umschalter bauen
    require $pathvars["libraries"]."function_file_verarbeitung.inc.php"; // upload verarbeitung
    require $pathvars["libraries"]."function_makece.inc.php"; // content editor erstellen
    require $pathvars["libraries"]."function_parser.inc.php"; // parser funktion
    require $pathvars["libraries"]."function_right_check.inc.php"; // rechte in bereichen pruefen
    require $pathvars["libraries"]."function_rparser.inc.php"; // parser funktion recursiv

    if ( $specialvars["postgres"] == "-1" ) {
        require $pathvars["libraries"]."dbclass_postgres.php";  // sql class
    } else {
        require $pathvars["libraries"]."dbclass_mysql.php";     // sql class
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
