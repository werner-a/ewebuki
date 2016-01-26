<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// pdfc.cfg.php-dist v1 chaot
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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
////+///////+///////+///////+///////+///////+///////+///////////////////////////////////////////////////////////

    $cfg["pdfc"] = array(
             "path" => array (
                      "lib" => "/usr/local/share/php5/tcpdf/tcpdf.php",
                 "function" => $pathvars["libraries"]."function_tcpdf.inc.php",
                       ),
         "document" => array (
                    "title" => "eWeBuKi - ChaoS Networks",
                   "author" => "ChaoS Networks",
                 "keywords" => "Content, PDF, Export",
              "name_prefix" => "eWeBuKi_-_",
                       ),

         "constant" => array (
            #"K_PATH_IMAGES" => "/images/custom/",
                       ),

           "change" => array (
          #"PDF_HEADER_LOGO" => "header_tcpdf.png",
    #"PDF_HEADER_LOGO_WIDTH" => "180",
           #"PDF_MARGIN_TOP" => "30",
         "PDF_HEADER_TITLE" => "eWeBuKi - TCPDF Connector",
        "PDF_HEADER_STRING" => "by Werner Ammon - ChaoS Networks\nwww.ewebuki.de",
                       ),
      "server_name" => "",
            "state" => false,
            "debug" => false,
         "template" => "pdfc",
       "force_utf8" => true,

          "buttons" => array(
                        "b0" => "<a href=\"",
                        "e0" => "?pdf=0\" target=\"_blank\" title=\"PDF Datei in einem neuen Fenster\">PDF: debug</a>",
                        "b1" => "<a href=\"",
                        "e1" => "?pdf=1\" target=\"_blank\" title=\"PDF Datei in einem neuen Fenster\">PDF: base</a>",
                        "b2" => "<a href=\"",
                        "e2" => "?pdf=2\" target=\"_blank\" title=\"PDF Datei in einem neuen Fenster\">PDF: pdf</a>",
                       ),
    );

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
