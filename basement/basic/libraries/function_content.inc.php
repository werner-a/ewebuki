<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "content sprachabhaengig holen";
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

    function content($line, $tname) {

        global $db, $debugging, $pathvars, $specialvars, $environment, $ausgaben, $rechte;

        if ( $specialvars["crc32"] == -1 ) {
            if ( $environment["ebene"] != "" && $tname == $environment["kategorie"] ) {
                $dbtname = crc32($environment["ebene"]).".".$tname;
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "crc32 tname \"".$dbtname."\" forced!!!".$debugging["char"];
            } else {
                $dbtname = $tname;
            }
        } else {
            // ist das eine sub kategorie ?
            if ( $environment["subkatid"] != "" && $tname == $environment["katid"] ) {
                $dbtname = $tname.".".$environment["subkatid"];
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sub tname \"".$dbtname."\" forced!!!".$debugging["char"];
                #$dbtname = $tname;
            } else {
                $dbtname = $tname;
            }
        }

        while ( strstr($line, "#(") ) {

            // wo beginnt die marke
            $labelbeg=strpos($line,"#(");
            // wo endet die marke (wichtig der offset!)
            $labelend=strpos($line,")",$labelbeg);
            // wie lang ist die marke
            $labellen=$labelend-$labelbeg;
            // token name extrahieren
            $label=substr($line,$labelbeg+2,$labellen-2);

            $sql = "SELECT html, content FROM ". SITETEXT ." WHERE tname='".$dbtname."' AND lang='".$environment["language"]."' AND label='$label'";
            #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result  = $db -> query($sql);
            $row = $db -> fetch_row($result);

            if ( !is_array($row) ) {
                // wenn "aktuelle sprache" = "default sprache" ueberfluessige fehlermeldung nicht anzeigen!
                if ( $environment["language"] != $specialvars["default_language"] ) {
                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: \"".$environment["language"]."\" for #(".$label.") in template \"".$dbtname."\" not found using default: \"".$specialvars["default_language"]."\"".$debugging["char"];
                }
                $sql = "SELECT html, content FROM ". SITETEXT ." WHERE tname='$dbtname' AND lang='".$specialvars["default_language"]."' AND label='$label'";
                $result  = $db -> query($sql);
                $row = $db -> fetch_row($result);
            }

            if ( $row[1] == "" ) {
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: Uuuuups no default language \"".$specialvars["default_language"]."\" for #(".$label.") in template \"".$dbtname."\" found. Giving up!".$debugging["char"];
            }

            // erlaubnis bei intrabvv speziell setzen
            global $HTTP_SESSION_VARS;
            $database = $db->getDb();
            if ( is_array($HTTP_SESSION_VARS["dbzugriff"]) ) {
                if ( in_array($database,$HTTP_SESSION_VARS["dbzugriff"]) ) $dbzugriff = -1;
            }
            if ( is_array($HTTP_SESSION_VARS["katzugriff"]) ) {
                if ( in_array("-1:".$database.":".$dbtname,$HTTP_SESSION_VARS["katzugriff"]) ) $katzugriff = -1;
            }

            $replace = $row[1];
            // cms edit link einblenden
            if ( $rechte["cms_edit"] == -1
              /* || $rechte["administration"] == -1 && $rechte["sti"] == -1 ### loesung? */
              || $rechte["administration"] == -1 && $dbzugriff == -1
              || $katzugriff == -1 ) {

                // konvertieren ?
                if ( $specialvars["wysiwyg"] == "" && $row[0] == -1 ) {
                    $convert = ",,tag";
                    $signal = "c";
                } elseif ( $specialvars["wysiwyg"] != "" && $row[0] != -1 ) {
                    $convert = ",,html";
                    $signal = "c";
                } else {
                    $convert = "";
                    $signal = "e";
                }
                $editurl = $pathvars["virtual"]."/cms/edit,".$db->getDb().",".$dbtname.",".$label;

                // wenn es kein button ist
                if ( !strstr($line,"value=\"") ) {
                    $replace .= " <a target=\"_top\" href=\"".$editurl.$convert.".html\"><img src=\"".$pathvars["images"]."cms-tag-".$signal.".png\" width=\"4\" height=\"4\" border=\"0\" alt=\"Bearbeiten\"></a>";
                } else {
                    $line = $line." <a target=\"_top\" href=\"".$editurl.".html\"><img src=\"".$pathvars["images"]."cms-tag-".$signal.".png\" width=\"4\" height=\"4\" border=\"0\" alt=\"Bearbeiten\"></a>";
                }
            }

            // wenn content nicht in html ist
            if ( $row[0] != -1 ) {
                // intelligenten link tag bearbeiten
                $replace = intelilink($replace);
                // neues generelles tagreplace
                $replace = tagreplace($replace);
                // newlines nach br wandeln (muss zuletzt gemacht werden)
                $replace = nlreplace($replace);
            }

            // marke ersetzen
            if ( strstr($line,"#(") ) {
                $line = str_replace("#(".$label.")",$replace,$line);
            }
        }
        return($line);
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
