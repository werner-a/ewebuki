<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "$Id$";
  $Script_desc = "Hier kurze Beschreibung";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

    #$pathvars["fqdn"] = "http://www.bvv.bayern.de";
    include $pathvars["addonroot"]."intrabvv/menu-global.cfg.php";
    include $pathvars["addonroot"]."intrabvv/menu.inc.php";

    if ( $ausgaben["globalmenu"] == "" ) $ausgaben["globalmenu"] = "Menu ist Leer";

    if ( !strstr($environment["fqdn"][0], "intra") /* || $environment["fqdn"][0] == "intrabvv" */ ) {

        // über ip standort rausfinden
        $ip = $_SERVER["REMOTE_ADDR"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ip: ".$ip.$debugging["char"];
        $ip_class = explode(".",$_SERVER["REMOTE_ADDR"]);

        // manuelle steuerung der ip
        #$ip_class[2] = 66;
        #$ip_class[1] = 240;

        // eigenes amt finden
        $sql = "SELECT adakz, adkate, adststelle, adkurzbez FROM db_adrd WHERE adbnet = ".$ip_class[1]." AND adcnet= ".$ip_class[2];
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "amt: ".$data["adkate"].$debugging["char"];
        $specialvars["dyndb"] = "intra".$data["adakz"];
    } else {

        // amtsbezeichnung
        $sql = "SELECT adakz, adkate, adststelle, adkurzbez FROM db_adrd WHERE adakz = ".substr($environment["fqdn"][0],5);
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "amt: ".$data["adkate"].$debugging["char"];
        $specialvars["dyndb"] = "intra".$data["adakz"];

    }
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "localdb: ".$specialvars["dyndb"].$debugging["char"];


    // lokale db auswaehlen
    if ( $db->selectDb($specialvars["dyndb"],FALSE) == 1 ) {

        #$pathvars["fqdn"] = "http://".$specialvars["dyndb"].".bvv.bayern.de";
        include $pathvars["addonroot"]."intrabvv/menu-local.cfg.php";
        include $pathvars["addonroot"]."intrabvv/menu.inc.php";

        if ( $ausgaben["localmenu"] == "" ) $ausgaben["localmenu"] = "Menu ist Leer";

    } else {
        $ausgaben["localmenu"] = "Die lokalen Inhalte können nicht automatisch bestimmt werden.<br><br>Eventuell ist der Proxy Server für \".bvv.bayern.de\" nicht deaktiviert.";
    }

    // lange dienststellennamen kürzen
    if ($data["adkurzbez"]) {
        #$pos = strpos($data["adststelle"]," ");
        $localprint = $data["adkurzbez"];
        #if (strstr($data["adststelle"],"Bad ") ) {
            #$pos_bad = strpos($data["adststelle"],"Bad ");
#            echo $pos_bad;
        #}
    } else {
        $localprint = $data["adststelle"];
    }

    $ausgaben["amt"] = $data["adkate"]." ".$localprint;

    // globale db auswaehlen
    $db -> selectDb(DATABASE,FALSE);

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
