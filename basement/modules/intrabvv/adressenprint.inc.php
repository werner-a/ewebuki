<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "short description";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    phpWEBkit - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of phpWEBkit

    phpWEBkit is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    phpWEBkit is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with phpWEBkit; If you did not, you may download a copy at:

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

    include_once $pathvars["libraries"]."xtra.pdf.php";
    include_once $pathvars["libraries"]."xtra.ezpdf.php";

    $pdf = new Cezpdf();

    // seiten raender
    $pdf->ezSetMargins(220,180,50,40); // top, bottom, left, right
    $pdf->selectFont('./fonts/Helvetica.afm');
    #$pdf->openHere("Fit");


    // kunden
    if (strstr($environment["ebene"],"kunden")) {
        $abstand = -60;
        $aheader = "<b>Ansprechpartner</b>";
        $art = "kunden";
        $anhang = "Firma";
        $pdfkopf = "Kunden";
        #$header_allgemein = "<b>Angaben zur ".$anhang."</b>";
        $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);
        $sql = "SELECT * FROM db_adrk WHERE akid = ".$environment["parameter"][1];
        $result = $db -> query($sql);
        $data = $db->fetch_array($result,1);
        if ($data["akkate"] == 22) $anhang = "Person";
        $header_allgemein = "<b>Angaben zur ".$anhang."</b>";
        $sqlansp = "SELECT * FROM db_adrk_ansp WHERE eid = ".$environment["parameter"][1]." AND abnet=".$ip_class[1]." AND acnet=".$ip_class[2]." ORDER BY kaid";
        $resultansp = $db -> query($sqlansp);
        while ($partner = $db->fetch_array($resultansp,1)) {
            $red_or_ans[] = $partner;
        }
    }

    // beschäftigte
    if (strstr($environment["ebene"],"beschaeftigte")) {
        $art = "beschaeftigte";
        $header = $data["adstbfd"];
        $header_allgemein = "<b>Dienstliche Angaben</b>";
        $sql = "SELECT * FROM db_adrb INNER JOIN db_adrb_amtbez ON abamtbez=abamtbez_id INNER JOIN db_adrb_dienst ON abdstposten=abdienst_id INNER JOIN db_adrd ON abdststelle=adid WHERE abid = ".$environment["parameter"][1];
        $result = $db -> query($sql);
        $data = $db->fetch_array($result,1);
        if (!$data["abnamra"]) {
            $sql = "SELECT * FROM db_adrb INNER JOIN db_adrd ON abdststelle=adid WHERE abid = ".$environment["parameter"][1];
            $result = $db -> query($sql);
            $data = $db->fetch_array($result,1);
        }
        $pdfkopf = $data["adkate"];

        // priv angaben holen
        $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);
        if (($data["abbnet"] == $ip_class[1]) && ($data["abcnet"] == $ip_class[2])) {
            foreach($cfg["field"]["priv_".$art] as $key => $sonstwas) {
                $j++;
                if ($data[$key] == "") continue;
                if ( !$cfg["field"]["priv_".$art][$key][1] == "") {
                    foreach ($cfg["field"]["priv_".$art][$key][1] as $value) {
                        if ($data[$value] == "") continue;
                        if ($zusatz == "") {
                            $leer = "";
                        } else {
                            $leer = " ";
                        }
                        $zusatz .= $leer.$data[$value];
                    }
                    $al_or_priv[$j][] =$cfg["field"]["priv_".$art][$key][0];
                    $al_or_priv[$j][] = $zusatz." ".$data[$key];
                }
                $al_or_priv[$j][] = $cfg["field"]["priv_".$art][$key][0];
                $al_or_priv[$j][] = $data[$key];
            }
            if(is_array($al_or_priv )) {
                $theader = "<b>Private Angaben</b>";
            } else {
                $theader = "";
            }
        }
    }

    // dienststellen
    if (strstr($environment["ebene"],"dienststellen")) {
        $abstand = -30;
        $aheader = "<b>Redaktion</b>";
        $theader = "<b>Amtsleitung</b>";
        $art = "dienststellen";
        $header_allgemein = "<b>Allgemeine Angaben</b>";
        // query für dienststellen bauen
        foreach ($cfg["field"]["dienststellen"] as $key => $value) {
            if (!$felder_dst == "") $trenner = ",";
            $felder_dst .= $trenner.$key;
        }
        $sql = "SELECT ".$felder_dst.",adststelle,adbnet,abdsttel,abnamra,abgrad,abtitel,abnamvor,abdstemail,abdstfax,adkate,adwebmid1,adwebmid2 FROM db_adrd INNER JOIN db_adrb ON adleiter = abid WHERE adid = ".$environment["parameter"][1];
        $result = $db -> query($sql);
        $data = $db->fetch_array($result,1);
        $pdfkopf = $data["adkate"];
        foreach ($cfg["field"]["amtsleiter"] as $key => $value) {
            $i++;
            $zusatz = "";
            if ($data[$key] == "") continue;
            if ( !$cfg["field"]["beschaeftigte"][$key][1] == "") {
                foreach ($cfg["field"]["beschaeftigte"][$key][1] as $value1) {
                    if ($data[$value1] == "") continue;
                    $zusatz .= $data[$value1]." ";
                }
            }
            $al_or_priv[$i][] = $value;
            $al_or_priv[$i][] = $zusatz.$data[$key];
        }
        $sql = "SELECT abnamra,abnamvor,abdsttel,abdstemail FROM db_adrb WHERE abid = ".$data["adwebmid1"]." OR abid = ".$data["adwebmid2"];
        $result = $db -> query($sql);
        while ($redaktion = $db->fetch_array($result,1)) {
            $redaktion["abnamra"] = $redaktion["abnamra"].", ".$redaktion["abnamvor"];
            unset($redaktion["abnamvor"]);
            $red_or_ans[] = $redaktion;
        }
    }
    switch( $pdfkopf ) {
        case "VA":

            // va kopf
            $vahead = $pdf->openObject();
            $va = $data["adststelle"];
            $links = 40;
            $center = 797;
            $rechts = 455;
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/wappen-klein-sw.png",$links,780,35);
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/auge-klein.png",$links+150,$center+9,34);
            $pdf->addText($links+40,$center+13,'14','Vermessungsamt');
            $pdf->addText($links+186,$center+13,'14',$va);
            $pdf->addText($links+40,$center-10,'14','BFD Bereich: '.$data["adstbfd"]);
            $pdf->addText($rechts-9,$center+10,'10','<b>Intranet-Adressverwaltung</b>');
            $pdf->addText($rechts+49,$center-13,'10','<b>Einzel-Auszug</b>');
            $pdf->line($links,$center-26,$rechts+117,$center-26);
            $pdf->closeObject();
            $pdf->addObject($vahead,'all');
            break;

        case "BFD":
            // bfd kopf
            $bfdhead = $pdf->openObject();
            $bfd = $data["adststelle"];
            $links = 40;
            $center = 797;
            $rechts = 455;
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/wappen-gross-sw.png",$links,780,80);
            $pdf->addText($links+80,$center+13,'14','Bezirksfinanzdirektion');
            $pdf->addText($links+80,$center-10,'14','Vermessungsabteilung ');
            $pdf->addText($links+220,$center+13,'14',$bfd);
            $pdf->addText($rechts-9,$center+10,'10','<b>Intranet-Adressverwaltung</b>');
            $pdf->addText($rechts+49,$center-13,'10','<b>Einzel-Auszug</b>');
            $pdf->line($links,$center-26,$rechts+117,$center-26);
            $pdf->closeObject();
            $pdf->addObject($bfdhead,'all');
            break;

        case "StMF":
            // stmf kopf
            $bfdhead = $pdf->openObject();
            $bfd = $data["adststelle"];
            $links = 40;
            $center = 797;
            $rechts = 455;
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/wappen-gross-sw.png",$links,780,80);
            $pdf->addText($links+80,$center+13,'14','Bayer. Staatsministerium der Finanzen');
            $pdf->addText($links+80,$center-10,'14','Vermessungsabteilung');
            #$pdf->addText($links+220,$center+15,'14',$bfd);
            $pdf->addText($rechts-9,$center+10,'10','<b>Intranet-Adressverwaltung</b>');
            $pdf->addText($rechts+49,$center-13,'10','<b>Einzel-Auszug</b>');
            $pdf->line($links,$center-26,$rechts+117,$center-26);
            $pdf->closeObject();
            $pdf->addObject($bfdhead,'all');
            break;

        case "Kunden":
            // kunden kopf
            $kunden = $pdf->openObject();
            $links = 40;
            $center = 797;
            $rechts = 455;
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/auge.png",$links,780,80);
            $pdf->addText($links+80,$center+11,'14','Kunden und Partner');
            $pdf->addText($links+80,$center-8,'14','der bayer. Vermessungsverwaltung');
            $pdf->addText($rechts-9,$center+10,'10','<b>Intranet-Adressverwaltung</b>');
            $pdf->addText($rechts+49,$center-10,'10','<b>Einzel-Auszug</b>');
            $pdf->line($links,$center-26,$rechts+117,$center-26);
            $pdf->closeObject();
            $pdf->addObject($kunden,'all');
            break;
    }

    // daten holen
    foreach($cfg["field"][$art] as $key => $sonstwas) {
        $i++;
        $zusatz= "";
        $leerzeichen = " ";
        if ($data[$key] == "") continue;
        // daten zusammenfassen
        $table[$i][] =$cfg["field"][$art][$key][0];
        if ( !$cfg["field"][$art][$key][1] == "") {
                foreach ($cfg["field"][$art][$key][1] as $value) {
                    if ($data[$value] == "") continue;
                    if ($key == "adcnet") {
                        $zusatz = "10.";
                        $leerzeichen = ".";
                    }
                    $zusatz .= $data[$value].$leerzeichen;
                }
                $table[$i][] = $zusatz.$data[$key];
                continue;
        }

        // interessen exploden
        if ($key == "abinteressen") {
            $int = explode(";",$data[$key]);
            foreach ($int as $value) {
                $komma = ", ";
                if ($interessen == "") $komma = "";
                $interessen  .= $komma.$value;
            }
            $table[$i][] = $interessen;
        } else {
            $table[$i][] = $data[$key];
        }
    }




    // hauptteil ausgeben
    $pdf->ezSetDy(123);
    $pdf -> ezText($header_allgemein,11);
    $pdf->ezSetDy(-10);
    $lage = $pdf->ezTable($table,'','',
             array( "showLines"        =>0,
                    "showHeadings"     =>0,
                    "shaded"           =>0,
                    #"shadeCol"         => array(255,255,255),
                    #"shadeCol2"        => 0.8,0.8,0.8,
                    "fontSize"         =>11,
                     /*"xPos"             =>($abstand+$breite),*/
                     "xPos"             => 'left',
                     "xOrientation"     => 'right',
                     /*"width"            =>528,
                     "maxWidth"         =>528,*/
                     "width"            =>450,
                     #"maxWidth"         =>250,
                     "cols"             => $col_width_allg,
                     "protectRows"      => 2
                   )
             );
        $pdf->line($links,$lage-30,$rechts+117,$lage-30);


    // amtsleiter oder private angaben ausgeben
    if (is_array($al_or_priv)) {
        $pdf->ezSetDy(-60);
        $pdf -> ezText($theader,11);
        $pdf->ezSetDy(-10);
        $lage = $pdf->ezTable($al_or_priv,'','',array(
                                                            "showLines"        =>0,
                                                            "showHeadings"     =>0,
                                                            "shaded"           =>0,
                                                            #"shadeCol"         => array(255,255,255),
                                                            #"shadeCol2"        => 0.8,0.8,0.8,
                                                            "fontSize"         =>11,
                                                             /*"xPos"             =>($abstand+$breite),*/
                                                             "xPos"             => 'left',
                                                             "xOrientation"     => 'right',
                                                             #"width"            =>528,
                                                             "maxWidth"         =>528,
                                                             #"width"            =>250,
                                                             "maxWidth"         =>450,
                                                             "cols"             => $col_width_priv,
                                                             "protectRows"      => 2
                                                           )
                              );
    }

    // redaktion oder ansprechpartner ausgeben
    if (is_array($red_or_ans)) {
        $pdf->ezSetDy($abstand);
        $pdf -> ezText($aheader,11);
        $pdf->ezSetDy(-10);
        $lage = $pdf->ezTable($red_or_ans,$colum[$art],'',array(
                                                            "showLines"        =>0,
                                                            "showHeadings"     =>1,
                                                            "shaded"           =>0,
                                                            #"shadeCol"         => array(255,255,255),
                                                            #"shadeCol2"        => 0.8,0.8,0.8,
                                                            "fontSize"         =>11,
                                                             /*"xPos"             =>($abstand+$breite),*/
                                                             "xPos"             => 'left',
                                                             "xOrientation"     => 'right',
                                                             "width"            =>528,
                                                             "maxWidth"         =>528,
                                                             #"width"            =>250,
                                                             #"maxWidth"         =>450,
                                                             #"cols"             => array('abnamra' =>array('width'=>178),
                                                             #                            #'abnamvor' =>array('width'=>80),
                                                             #                            'abdstemail' =>array('width'=>200),
                                                             #                            'abdsttel' =>array('width'=>150)
                                                              #                           ),
                                                             "protectRows"      => 2
                                                           )
                              );
    }
    // fuss ausgeben
    $pdf->line($links,$lage-30,$rechts+117,$lage-30);
    $heute = date("d.m.Y");
    $foot = $pdf->openObject();
    $pdf->addText($links+412,$lage-50,'9','Auszug erstellt am '.$heute);
    $pdf->closeObject();
    $pdf->addObject($foot,'all');

    // ausgabe
    $pdf->ezStream();

    // was anzeigen
    exit(); // nichts

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
