<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "fulltext db site search";
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

      // konfiguration
      #require $pathvars["config"]."addon/sitesearch.inc.php";
      if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "(1) funktion: ".$environment["subparam"][1].$debugging["char"];

      $ausgaben["sitesearch_aktion"] = $pathvars["virtual"]."/sitesearch,search.html";
      $ausgaben["sitesearch_search"] = $HTTP_POST_VARS["search"];
      if ( $environment["param"][1] == "search" ) {
          $sql = "SELECT lang, tname, content, ebene, kategorie FROM site_text WHERE content LIKE '%".$HTTP_POST_VARS["search"]."%'";
          $result = $db -> query($sql);
          while ( $site_text = $db -> fetch_array($result,$nop) ) {
               #$path = str_replace(".","/",$site_text["tname"]);
               $ausgaben["output"] .= "<a target=\"_blank\" href=\"/".$environment["design"]."/".$site_text["lang"].$site_text["ebene"]."/".$site_text["kategorie"].".html\">".$specialvars["pagetitle"]." - ".$site_text["kategorie"]."</a><br>";
               $site_text["content"] = tagremove($site_text["content"]);


               $pos = strpos($site_text["content"], $HTTP_POST_VARS["search"]);
               $laenge = strlen($site_text["content"]);

               $bereich = 120;
               $how = $bereich / 2;
               if ( $bereich >= $laenge ) {
                  $start = 0;
               } elseif ( $pos >= ($how) ) {
                  $start = $pos-$how;
               } elseif ( $pos == 0 ) {
                  $start = $pos;
               } else {
                  $start = $how-$pos;
               }

               $found = substr($site_text["content"],$start,$bereich);
               $ausgaben["output"] .= "... "." ".$found." ( $laenge / $bereich / $pos / $start )"." ...<br><br>";
          }
          $ergebnisse = $db -> num_rows($result);
          $ausgaben["output"] = "Durchsuche die Website:<br><br>".$ausgaben["output"];
          if ( $ergebnisse >= 1 ) {
              $ausgaben["output"] .= "<br> ... ".$ergebnisse." Bereich(e) wurde gefunden.";
          } else {
              $ausgaben["output"] .= "... leider nichts gefunden.";
          }
      }

  if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
