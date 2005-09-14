<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "zeigt last changed info aus content tabelle an";
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

        // <!--##hide-changed-->
        // Letzte &Auml;nderung !{changed} von <a href="mailto:!{email}">!{alias}</a> (!{forename} !{surname})
        // <!--##show-->

        // funktions bereich
        // ***

        // lokale db auswaehlen
        if ( $environment["fqdn"][0] == $specialvars["dyndb"] ) {
            $db->selectDb($specialvars["dyndb"],FALSE);
        }

        if ( $environment["ebene"] != ""  ) {
            $tname = crc32($environment["ebene"]).".".$environment["kategorie"];
        } else {
            $tname = $environment["kategorie"];
        }
        $sql = "SELECT ".$cfg["db"]["changed"]["lang"].",
                       ".$cfg["db"]["changed"]["changed"].",
                       ".$cfg["db"]["changed"]["surname"].",
                       ".$cfg["db"]["changed"]["forename"].",
                       ".$cfg["db"]["changed"]["email"].",
                       ".$cfg["db"]["changed"]["alias"]."
                  FROM ".$cfg["db"]["changed"]["entries"]."
                 WHERE tname = '".$tname."'
              ORDER BY ".$cfg["db"]["changed"]["changed"];
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {
            $changed[$data["lang"]] = $data;
        }

        if ( is_array($changed[$environment["language"]]) ) {
            $lang = $environment["language"];
        } else {
            $lang = $specialvars["default_language"];
        }

        if ( $changed[$lang][$cfg["db"]["changed"]["alias"]] != "" ) {
            $hidedata["changed"]["changed"] = date($cfg["format"],strtotime($changed[$lang][$cfg["db"]["changed"]["changed"]]));
            $hidedata["changed"]["surname"] = $changed[$lang][$cfg["db"]["changed"]["surname"]];
            $hidedata["changed"]["forename"] = $changed[$lang][$cfg["db"]["changed"]["forename"]];
            $hidedata["changed"]["email"] = $changed[$lang][$cfg["db"]["changed"]["email"]];
            $hidedata["changed"]["alias"] = $changed[$lang][$cfg["db"]["changed"]["alias"]];
        }

        // globale db auswaehlen
        $db->selectDb(DATABASE,FALSE);

        // +++
        // funktions bereich

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
