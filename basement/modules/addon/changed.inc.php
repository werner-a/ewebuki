<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "zeigt last changed info aus content tabelle an";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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
            $tname = eCRC($environment["ebene"]).".".$environment["kategorie"];
        } else {
            $tname = $environment["kategorie"];
        }
        $where = "";
        if ( $specialvars["content_release"] == -1 ) {
            if ( is_array($cfg["changed"]["blog_date"]) ){
                if ( array_key_exists($environment["ebene"],$cfg["changed"]["blog_date"]) ) {
                    $sort_len = strlen($cfg["changed"]["blog_date"][$environment["ebene"]])+2;
                    $sql_blog = "SELECT Cast(SUBSTR(content,POSITION('[".$cfg["changed"]["blog_date"][$environment["ebene"]]."]' IN content)+".$sort_len.",POSITION('[/".$cfg["changed"]["blog_date"][$environment["ebene"]]."]' IN content)-POSITION('[".$cfg["changed"]["blog_date"][$environment["ebene"]]."]' IN content)-".$sort_len.")AS DATETIME) AS date
                                    FROM site_text
                                    WHERE tname = '".$tname."' AND status =1";
                    $result_blog = $db -> query($sql_blog);
                    $data_blog = $db -> fetch_array($result_blog,1);
                    $ext_date = $data_blog["date"];
                }
            }
            $where = " AND status = 1";
        }

        $sql = "SELECT ".$cfg["changed"]["db"]["changed"]["lang"].",
                       ".$cfg["changed"]["db"]["changed"]["changed"].",
                       ".$cfg["changed"]["db"]["changed"]["surname"].",
                       ".$cfg["changed"]["db"]["changed"]["forename"].",
                       ".$cfg["changed"]["db"]["changed"]["email"].",
                       ".$cfg["changed"]["db"]["changed"]["alias"]."
                  FROM ".$cfg["changed"]["db"]["changed"]["entries"]."
                 WHERE tname = '".$tname."'".$where."
              ORDER BY ".$cfg["changed"]["db"]["changed"]["changed"];
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

        if ( $changed[$lang][$cfg["changed"]["db"]["changed"]["alias"]] != "" ) {
            if ( $ext_date != "" ) {
                $changed[$lang][$cfg["changed"]["db"]["changed"]["changed"]] = $ext_date;
            }
            $hidedata["changed"]["changed"] = date($cfg["changed"]["format"],strtotime($changed[$lang][$cfg["changed"]["db"]["changed"]["changed"]]));
            $hidedata["changed"]["surname"] = $changed[$lang][$cfg["changed"]["db"]["changed"]["surname"]];
            $hidedata["changed"]["forename"] = $changed[$lang][$cfg["changed"]["db"]["changed"]["forename"]];
            $hidedata["changed"]["email"] = $changed[$lang][$cfg["changed"]["db"]["changed"]["email"]];
            $hidedata["changed"]["alias"] = $changed[$lang][$cfg["changed"]["db"]["changed"]["alias"]];
        }

        // globale db auswaehlen
        $db->selectDb(DATABASE,FALSE);

        // +++
        // funktions bereich

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
