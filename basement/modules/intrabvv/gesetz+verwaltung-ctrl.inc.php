<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "gesetze+verwaltung-ctrl";
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


    // magic include loader
    if ( in_array($environment["kategorie"], $cfg["function"]) ) {
        #echo $environment["kategorie"];
        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-".$environment["kategorie"].".inc.php";
    } else {
        // welches include ansonsten laden
        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-list.inc.php";
        // welches template anzeigen
        $mapping["main"] = "-900193709.list";
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];
    }
    /*
    //
    //  auswahl durch user
    //
    if ( $environment["kategorie"] == "irgendwas" ) {

        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-list.inc.php";

    } elseif ( $environment["kategorie"] == "modify" ) {

        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-modify.inc.php";

    //
    // übersicht
    //
    } else {
        echo $cfg["name"]."-list.inc.php"   ;
        #include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-list.inc.php";

    }
    */

    if ( $cfg["enable"] == -1 ) {

        #$url = "http://www.jurisweb.testa-de.net/jurisweb/j2_web_home/database_home/byk/Normenkomplexe/VermKatG_BY.html";
        #$url = "http://www.chaos.de/business/ger/about.html";
        #$url = "http://www.bvv.bayern.de/net/ger/index.html";
        $url = "http://jurisweb.bybn.de:8081/jurisweb/j2_web_home/database_home/byk/Normenkomplexe/KostG_BY_1998.html";

        /*
        $fp = fopen($url, "r");
        while (!feof($fp)) {
            echo fgets($fp, 4096);
            #$ausgaben["output"] .= fgets($fp, 4096);
        }
        fclose($fp);
        */

        function fget_proxy($url)  {

            $PROXY_URL="www-proxy.bybn.de";
            $PROXY_PORT=80;

            putenv("http_proxy=$PROXY_URL:$PROXY_PORT");
            $result = shell_exec("wget -q -O - $url");

            return $result;
        }

        $ausgaben["output"] = fget_proxy($url);
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
