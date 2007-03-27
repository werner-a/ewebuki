<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  "$Id$";
//  "listendruck";
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

    // allgemeine nicht benötigte felder entfernen
    $kick = array( "adid","lochrand","image","image_x","image_y","form_referer" );

    switch ( $environment["parameter"][1] ) {
        case "telint":

            // name zusammenfassen
            $name_join = array("abnamra","abtitel", "abnamvor", "abamtbezkurz", "abad");

            // tabellen überschrift
            $title_left = "Räume";
            $title_right = "Mobilfunk";

            // alle sql befehle
            $cfg["db"]["sql"][1] = "SELECT adkate, adststelle, adtelver, adfax FROM db_adrd WHERE adid =".$HTTP_POST_VARS["adid"];
            $cfg["db"]["sql"][2] = "SELECT !felder! FROM db_adrb INNER JOIN db_adrb_dienst ON abdienst_id=abdstposten INNER JOIN db_adrb_amtbez ON abamtbez_id=abamtbez WHERE abdststelle = ". $HTTP_POST_VARS["adid"]." AND abanrede != 'Raum' ORDER BY abnamra";
            $cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = ". $HTTP_POST_VARS["adid"]." AND abanrede = 'Raum' ORDER BY abnamra";
            $cfg["db"]["sql"][4] = "SELECT !felder! FROM db_adrb WHERE abdststelle = ". $HTTP_POST_VARS["adid"]." AND (abdstmobil != '+49-' AND abdstmobil !='') ORDER BY abnamra";

            // besondere nicht benötigte felder entfernen
            $kck_main  = array_merge($kick, array("abdstmobil"));
            $tab_left  = array( "abnamra", "abdstzinr", "abdsttel");
            $tab_right = array( "abtitel", "abnamra", "abnamvor", "abdstmobil");

            $pdfkopf = "telint";
            break;

        case "telext":

            // name zusammenfassen
            $name_join = array("abnamra", "abtitel", "abnamvor", "abad");

            // tabellen überschrift
            $title_eins = "Mobilfunk";
            $title_zwei = "Räume";

            // alle sql befehle
            $cfg["db"]["sql"][1] = "SELECT * FROM db_adrd WHERE adid = ".$HTTP_POST_VARS["adid"];
            $cfg["db"]["sql"][2] = "SELECT !felder! FROM db_adrb INNER JOIN db_adrb_amtbez ON abamtbez_id=abamtbez WHERE abdststelle = ". $HTTP_POST_VARS["adid"]." AND abanrede != 'Raum' ORDER BY abnamra";
            $cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = ". $HTTP_POST_VARS["adid"]." AND abanrede = 'Raum' ORDER BY abnamra";
            $cfg["db"]["sql"][4] = "SELECT !felder! FROM db_adrb WHERE abdststelle = ". $HTTP_POST_VARS["adid"]." AND (abdstmobil != '+49-' AND abdstmobil != '') ORDER BY abnamra";

            // besondere nicht benötigte felder entfernen
            $kck_main  = array_merge($kick, array("abdstmobil"));
            $tab_left  = array( "abnamra", "abdstzinr", "abdsttel");
            $tab_right = array( "abtitel", "abnamra", "abnamvor", "abdstmobil");

            $pdfkopf = "telext";
            break;

        case "feldgesch":

            // name zusammenfassen
            $name_join = array("aknam", "akvor");

            // alle sql befehle
            $cfg["db"]["sql"][1] = "SELECT adkate, adststelle, adtelver, adbnet, adcnet  FROM db_adrd WHERE adid =".$HTTP_POST_VARS["adid"];
            $cfg["db"]["sql"][2] = "SELECT !felder! FROM db_adrk WHERE !where! AND akkate =\"22\" ORDER BY akort";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = \"". $HTTP_GET_VARS["adid"]."\" AND abanrede =\"Raum\" ORDER BY abnamra";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = '". $HTTP_GET_VARS["adid"]."' AND (abdstmobil != '+49-' AND abdstmobil != '') ORDER BY abnamra";

            // besondere nicht benötigte felder entfernen
            $kck_main  = $kick;
            #$tab_left = array( "abtitel", "abnamra", "abnamvor", "abdstmobil");

            $pdfkopf = "telint";
            break;

        case "kunden":
            // Tabellenüberschriften für print-art2
            $head1 = "aknam";
            $head2 = "akfirma1";
            $head3 = "akvor";
            $ort = "akort";
            $plz = "akplz";

            // ueberschrift name zusammenfassen
            #$name_join = array("");
            #$name_join = array("aknam", "akvor", "akfirma1", "akfirma2");
            #$firma_join = array("akfirma1", "akfirma2");

            // alle sql befehle
            $cfg["db"]["sql"][1] = "SELECT adkate, adststelle, adtelver, adbnet, adcnet  FROM db_adrd WHERE adid =".$HTTP_POST_VARS["adid"];
            #$cfg["db"]["sql"][2] = "SELECT !felder! FROM db_adrk WHERE !where!";
            $cfg["db"]["sql"][2] = "SELECT !felder!, akid , CONCAT(aknam,akfirma1) as sort FROM db_adrk WHERE !where! ORDER BY sort";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = \"". $HTTP_GET_VARS["adid"]."\" AND abanrede =\"Raum\" ORDER BY abnamra";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = '". $HTTP_GET_VARS["adid"]."' AND (abdstmobil != '+49-' AND abdstmobil != '') ORDER BY abnamra";

            // besondere nicht benötigte felder entfernen
            $kck_main  = array_merge($kick, array("akpartner"));
            #$tab_left = array( "abtitel", "abnamra", "abnamvor", "abdstmobil");

            $pdfkopf = "telint";
            break;

        case "dststelle":
            // Tabellenüberschriften für print-art2
            $head1 = "adkate";
            $head2 = "adststelle";
            $ort = "adort";
            $plz = "adplz";
            // ueberschrift name zusammenfassen
            #$name_join = array("adkate","adststelle","adort");

            // alle sql befehle
            $cfg["db"]["sql"][1] = "SELECT adkate, adststelle, adtelver, adbnet, adcnet  FROM db_adrd WHERE adid =".$HTTP_POST_VARS["adid"];
            $cfg["db"]["sql"][2] = "SELECT !felder!,adkate FROM db_adrd ORDER BY adsort, adststelle";
            #$cfg["db"]["sql"][2] = "SELECT !felder!,adkate FROM db_adrd INNER JOIN db_adrb ON adleiter=abid ORDER BY adsort, adststelle";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = \"". $HTTP_GET_VARS["adid"]."\" AND abanrede =\"Raum\" ORDER BY abnamra";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = '". $HTTP_GET_VARS["adid"]."' AND (abdstmobil != '+49-' AND abdstmobil != '') ORDER BY abnamra";

            // besondere nicht benötigte felder entfernen
            $kck_main  = $kick;
            #$tab_left = array( "abtitel", "abnamra", "abnamvor", "abdstmobil");

            $pdfkopf = "tel-dst";
            break;

        case "privat":

            // name zusammenfassen
            $name_join = array("abtitel", "abnamra","abnamvor");

            // alle sql befehle
            $cfg["db"]["sql"][1] = "SELECT adkate, adststelle, adtelver, adbnet, adcnet  FROM db_adrd WHERE adid =".$HTTP_POST_VARS["adid"];
            $cfg["db"]["sql"][2] = "SELECT !felder! FROM db_adrb WHERE abdststelle =  ".$HTTP_POST_VARS["adid"]."  AND abanrede != 'Raum' ORDER BY abnamra";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = \"". $HTTP_GET_VARS["adid"]."\" AND abanrede =\"Raum\" ORDER BY abnamra";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = '". $HTTP_GET_VARS["adid"]."' AND (abdstmobil != '+49-' AND abdstmobil != '') ORDER BY abnamra";

            // besondere nicht benötigte felder entfernen
            $kck_main  = $kick;
            #$tab_left = array( "abtitel", "abnamra", "abnamvor", "abdstmobil");

            $pdfkopf = "telint";
            break;

        case "geburtstag":

            // name zusammenfassen
            $name_join = array("abtitel", "abnamra","abnamvor", "abamtbezkurz", "abad");

            //$cfg["db"]["sql"][2] = "SELECT !felder! FROM db_adrb INNER JOIN db_adrb_dienst ON abdienst_id=abdstposten INNER JOIN db_adrb_amtbez ON abamtbez_id=abamtbez WHERE abdststelle = \"". $HTTP_GET_VARS["adid"]."\" AND abanrede !=\"Raum\" ORDER BY abnamra";
            // alle sql befehle
            $cfg["db"]["sql"][1] = "SELECT adkate, adststelle, adtelver, adbnet, adcnet  FROM db_adrd WHERE adid =".$HTTP_POST_VARS["adid"];
            $cfg["db"]["sql"][2] = "SELECT !felder! FROM db_adrb INNER JOIN db_adrb_amtbez ON abamtbez_id=abamtbez WHERE abdststelle =  ".$HTTP_POST_VARS["adid"]." ORDER BY abnamra";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = \"". $HTTP_GET_VARS["adid"]."\" AND abanrede =\"Raum\" ORDER BY abnamra";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = '". $HTTP_GET_VARS["adid"]."' AND (abdstmobil != '+49-' AND abdstmobil != '') ORDER BY abnamra";

            // besondere nicht benötigte felder entfernen
            $kck_main  = $kick;
            #$tab_left = array( "abtitel", "abnamra", "abnamvor", "abdstmobil");

            $pdfkopf = "telint";
            break;

        case "namen":

            // name zusammenfassen
            $name_join = array("abtitel", "abnamra","abnamvor", "abamtbezkurz", "abad");

            //$cfg["db"]["sql"][2] = "SELECT !felder! FROM db_adrb INNER JOIN db_adrb_dienst ON abdienst_id=abdstposten INNER JOIN db_adrb_amtbez ON abamtbez_id=abamtbez WHERE abdststelle = \"". $HTTP_GET_VARS["adid"]."\" AND abanrede !=\"Raum\" ORDER BY abnamra";
            // alle sql befehle
            $cfg["db"]["sql"][1] = "SELECT adkate, adststelle, adtelver, adbnet, adcnet  FROM db_adrd WHERE adid =".$HTTP_POST_VARS["adid"];
            $cfg["db"]["sql"][2] = "SELECT !felder! FROM db_adrb INNER JOIN db_adrb_dienst ON abdienst_id=abdstposten INNER JOIN db_adrb_amtbez ON abamtbez_id=abamtbez WHERE abdststelle =  ".$HTTP_POST_VARS["adid"]." ORDER BY abnamra";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = \"". $HTTP_GET_VARS["adid"]."\" AND abanrede =\"Raum\" ORDER BY abnamra";
            #$cfg["db"]["sql"][3] = "SELECT !felder! FROM db_adrb WHERE abdststelle = '". $HTTP_GET_VARS["adid"]."' AND (abdstmobil != '+49-' AND abdstmobil != '') ORDER BY abnamra";

            // besondere nicht benötigte felder entfernen
            $kck_main  = $kick;
            #$tab_left = array( "abtitel", "abnamra", "abnamvor", "abdstmobil");

            $pdfkopf = "telint";
            break;

#        case "adrd":
#            $cfg["sql"][1] = "";
#            $cfg["sql"][2] = "";
#            break;
    }


    include_once $pathvars["libraries"]."xtra.pdf.php";
    include_once $pathvars["libraries"]."xtra.ezpdf.php";

    $pdf = new Cezpdf();
    $pdf->selectFont('./fonts/Helvetica.afm');
    $pdf->openHere("Fit");

    // seiten raender
#    $pdf->ezSetMargins(80,40,50,40); // top, bottom, left, right
    $pdf->ezSetMargins(80,90,50,40); // top, bottom, left, right

    // seiten nummern
    $pdf->ezStartPageNumbers(330,20,12,'','Seite {PAGENUM} von {TOTALPAGENUM}',1);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
