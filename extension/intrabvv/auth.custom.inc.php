<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "customer auth extension";
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

    // welche dienstellen darf die uid bearbeiten
    // pruefung erfolgt mit
    // in_array(<dst id>,$HTTP_SESSION_VARS["dstzugriff"])
    session_register("dstzugriff");
    session_register("dbzugriff");
    if ( !is_array($HTTP_SESSION_VARS["dstzugriff"]) ) {

        $sql = "SELECT adkate, adstbfd FROM db_adrd where adid='".$HTTP_SESSION_VARS["custom"]."'";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,$nop);

        // serviceteam intranet darf alles bearbeiten
        if ( $rechte["administration"] && $HTTP_SESSION_VARS["custom"] == 5 ) {
            $data["adkate"] = "STI";

            session_register("sti"); ### loesung?
            $HTTP_SESSION_VARS["sti"] = "-1";
        }

        #$dstkategorie = $kategorie["adkate"];
        #if ( $rechte["administration"] == -1 ) {
            switch ( $data["adkate"] ) {
                #case "VA":
                #    #$dstadmin[] = $HTTP_SESSION_VARS["custom"];
                #    $HTTP_SESSION_VARS["dstzugriff"][] = $HTTP_SESSION_VARS["custom"];
                #    break;
                case "BFD":
                    $sql = "SELECT adid, adakz FROM db_adrd where adstbfd='".$data["adstbfd"]."'";
                    $result = $db -> query($sql);
                    while ( $field = $db -> fetch_row($result,$nop)) {
                        #$dstadmin[] = $field[0];
                        $HTTP_SESSION_VARS["dstzugriff"][] = $field[0];
                        $HTTP_SESSION_VARS["dbzugriff"][] = "intra".$field[1];
                    }
                    break;
                #case "StMF":
                #    #$dstadmin[] = $HTTP_SESSION_VARS["custom"];
                #    $HTTP_SESSION_VARS["dstzugriff"][] = $HTTP_SESSION_VARS["custom"];
                #    break;
                case "STI": # (BFD Augsburg)
                    $sql = "SELECT adid, adakz FROM db_adrd where 1";
                    $result = $db -> query($sql);
                    while ( $field = $db -> fetch_array($result,$nop)) {
                        #$dstadmin[] = $field[0];
                        $HTTP_SESSION_VARS["dstzugriff"][] = $field[0];
                        $HTTP_SESSION_VARS["dbzugriff"][] = "intra".$field[1];
                    }
                    // zugriff auf globale inhalte erlauben
                    $HTTP_SESSION_VARS["dbzugriff"][] = DATABASE;
                    break;
                default:
                    $sql = "SELECT adid, adakz FROM db_adrd where adid='".$HTTP_SESSION_VARS["custom"]."'";
                    $result = $db -> query($sql);
                    $field = $db -> fetch_array($result,$nop);
                    $HTTP_SESSION_VARS["dstzugriff"][] = $field[0];
                    $HTTP_SESSION_VARS["dbzugriff"][] = "intra".$field[1];
            }
        #}
    }

    // sonderreglung sti
    if ( $HTTP_SESSION_VARS["sti"] == -1 ) { ### loesung?
        $rechte["sti"] = -1;
    }

    // spezial zugriff auf bestimmte kategorien
    // pruefung erfolgt mit
    // in_array(<tnmame>,$HTTP_SESSION_VARS["katzugriff"])
    session_register("katzugriff");
    if ( !is_array($HTTP_SESSION_VARS["katzugriff"]) ) {
        $sql = "SELECT content, sdb, stname FROM auth_special where suid='".$HTTP_SESSION_VARS["uid"]."'";
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,$nop) ) {
            $HTTP_SESSION_VARS["katzugriff"][] = $data["content"].":".$data["sdb"].":".$data["stname"];
        }
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
