<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
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


    // kopfdaten holen
   # echo $environment[parameter][1];
    if ($environment["parameter"][1] != "dststelle") {
        $result = $db -> query($cfg["db"]["sql"][1]);
        $data = $db -> fetch_array($result,1);
    }
    if ( $pdfkopf == "telint" ) {
        $pdfkopf = $pdfkopf."-".$data["adkate"];
    }

    switch( $pdfkopf ) {
        case "telint-VA":

            // va kopf
            $vahead = $pdf->openObject();
            $va = $data["adststelle"];
            $links = 40;
            $center = 797;
            $rechts = 455;
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/wappen-klein-sw.png",$links,780,35);
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/auge-klein.png",$links+150,$center,34);
            $pdf->addText($links+40,$center+4,'14','Vermessungsamt');
            $pdf->addText($links+40,$center-13,'10','Telefon: '.$data["adtelver"]);
            $pdf->addText($links+186,$center+4,'14',$va);
            $pdf->addText($rechts+$cfg["title"][$environment["parameter"][1]]["move"],$center+4.5,'14',$cfg["title"][$environment["parameter"][1]]["text"]);
            $pdf->addText($rechts+35,$center-13,'10','Stand: '.$cfg["heute"]);
            $pdf->line($links,$center-26,$rechts+117,$center-26);
            $pdf->closeObject();
            $pdf->addObject($vahead,'all');
            break;

        case "telint-BFD":
            // bfd kopf
            $bfdhead = $pdf->openObject();
            $bfd = $data["adststelle"];
            $links = 40;
            $center = 797;
            $rechts = 455;
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/wappen-gross-sw.png",$links,780,80);
            $pdf->addText($links+80,$center+15,'14','Bezirksfinanzdirektion');
            $pdf->addText($links+80,$center,'14','Vermessungsabteilung');
            $pdf->addText($links+80,$center-18,'10','Telefon: '.$data["adtelver"]);
            $pdf->addText($links+220,$center+15,'14',$bfd);
            $pdf->addText($rechts+$cfg["title"][$environment["parameter"][1]]["move"],$center+15,'14',$cfg["title"][$environment["parameter"][1]]["text"]);
            $pdf->addText($rechts+35,$center-18,'10','Stand: '.$cfg["heute"]);
            $pdf->line($links,$center-26,$rechts+117,$center-26);
            $pdf->closeObject();
            $pdf->addObject($bfdhead,'all');
            break;

        case "telint-STMF":
            // bfd kopf
            $bfdhead = $pdf->openObject();
            $bfd = $data["adststelle"];
            $links = 40;
            $center = 797;
            $rechts = 455;
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/wappen-gross-sw.png",$links,780,80);
            $pdf->addText($links+80,$center+15,'14','Bezirksfinanzdirektion');
            $pdf->addText($links+80,$center,'14','Vermessungsabteilung');
            $pdf->addText($links+80,$center-18,'10','Telefon: '.$data["adtelver"]);
            $pdf->addText($links+220,$center+15,'14',$bfd);
            $pdf->addText($rechts+$cfg["title"][$environment["parameter"][1]]["move"],$center+15,'14',$cfg["title"][$environment["parameter"][1]]["text"]);
            $pdf->addText($rechts+35,$center-18,'10','Stand: '.$cfg["heute"]);
            $pdf->line($links,$center-26,$rechts+117,$center-26);
            $pdf->closeObject();
            $pdf->addObject($bfdhead,'all');
            break;

        case "tel-dst":
            // bfd kopf
            $bfdhead = $pdf->openObject();
            $bfd = $data["adststelle"];
            $links = 40;
            $center = 797;
            $rechts = 455;
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/wappen-gross-sw.png",$links,780,80);
            $pdf->addText($links+80,$center+17,'14','Bayerische Vermessungsverwaltung');
            $pdf->addText($links+80,$center-17,'14','Verzeichnis der Dienststellen');
           # $pdf->addText($links+80,$center-18,'10','Telefon: '.$data["adtelver"]);
           # $pdf->addText($links+220,$center+15,'14',$bfd);
            #$pdf->addText($rechts+$cfg["title"][$environment["parameter"][1]]["move"],$center+15,'14',$cfg["title"][$environment["parameter"][1]]["text"]);
            $pdf->addText($rechts+21,$center-16,'10','Stand: '.$cfg["heute"]);
            $pdf->line($links,$center-26,$rechts+117,$center-26);
            $pdf->closeObject();
            $pdf->addObject($bfdhead,'all');
            break;
    }

    ###
    // spaltenweise ausgabe an
    $pdf->ezColumnsStart(array( "num"=>2, "gap"=>20));




    ###
    // ueberschrift

    $name_join = "";

    // name_join (fuer zeilenweise ausgabe)  und sql (mit $field) erstellen
    foreach($HTTP_GET_VARS as $name => $value) {
        if ( !in_array($name,$kck_main) ) {
            $name_join[$name] = $name;
            if ( $field != "" ) $field .= ",";
            $field .= " ".$name;

        }
    }
    if ($HTTP_GET_VARS["akpartner"]) {
        $name_join["akpartner"] = "akpartner";
    }
    #echo "<pre>";
    #print_r($name_join);
    #echo "</pre>";


    // haupttabelle
    // ***

    // fremde kunden mit eigenen ansprechpartnern holen
    if ($environment["parameter"][1] == "kunden") {
        $sql_fremd = "SELECT DISTINCT eid FROM db_adrk_ansp WHERE abnet=".$data["adbnet"]." AND acnet=".$data["adcnet"];
        $result_fremd = $db -> query($sql_fremd);
        while ( $data_fremd = $db->fetch_array($result_fremd,1) ) {
            $where_fremd .= " OR akid=".$data_fremd["eid"];
        }
        $where = "(abnet = ".$data["adbnet"]." AND acnet = ".$data["adcnet"].")";
        $where_eigpartner = "AND ".$where;
        if ($where_fremd != "") $where .= $where_fremd;
    }

    $cfg["db"]["sql"][2] = str_replace("!felder!",$field,$cfg["db"]["sql"][2]);

    $cfg["db"]["sql"][2] = str_replace("!where!",$where,$cfg["db"]["sql"][2]);
    #echo   $cfg["db"]["sql"][2];
    $result = $db -> query($cfg["db"]["sql"][2]);

    // haupttabelle erstellen
    while ( $data = $db->fetch_array($result,1) ) {
        if ($data[$head1] != "") $blank = " ";
        $colum[0] = $data[$head1].$blank.$data[$head2].$data[$head3];
        unset ($data[$head1]);
        unset ($data[$head2]);
        unset ($data[$head3]);
        // amtsleiterdaten ergänzen
        if ($data["adleiter"]) {
            $sql1 = "SELECT abnamra, abtitel, abnamvor, abamtbezkurz, abdsttel FROM db_adrb INNER JOIN db_adrb_amtbez ON abamtbez_id=abamtbez WHERE abid=".$data["adleiter"];
            #echo $sql1;
            $result1 = $db -> query($sql1);
            $leiter = $db -> fetch_array($result1,1);
            // telefon amtsleiter kuerzen und anfuegen an va-tel anhängen
            $leiter["abdsttel"] = substr($leiter["abdsttel"], strrpos($leiter["abdsttel"],"-")+1 ,strlen($leiter["abdsttel"]));
            if ($data["adtelver"] && $leiter["abdsttel"] != "" ) $data["adtelver"] .= "; AL: ".$leiter["abdsttel"];
            if ($leiter["abtitel"]) {
                $leerzeichen = " ";
            } else {
                $leerzeichen = "";
            }
            $data["adleiter"] = $leiter["abamtbezkurz"].$leerzeichen.$leiter["abtitel"]." ".$leiter["abnamvor"]." ".$leiter["abnamra"]."\n";
        }
        // plz und ort zusammnfassen und ort löschen
        if ($data[$ort] && $data[$plz]) {
            $data[$plz] .= " ".$data[$ort];
            unset ($data[$ort]);
        }
        // ansprechpartner
        #$text1 .= $data[akid];
        if ($HTTP_GET_VARS["akpartner"]) {

            $sqlpart = "SELECT kanam,kavor,katel,kaemail FROM db_adrk_ansp WHERE eid=".$data["akid"]." ".$where_eigpartner;
            #echo $sqlpart;
            $resultpart = $db -> query($sqlpart);
            while ( $partner = $db->fetch_array($resultpart,1) ) {
                #echo $partner["kavor"];
                if ($data["akpartner"] == "") $data["akpartner"] = "\nAnsprechpartner:\n";
                if ( $partner["kanam"]) {
                    $dpunkt = ": ";
                } else {
                    $dpunkt = "";
                }
                if ($partner["kavor"]) {
                    $komma = ", ";
                } else {
                    $komma = "";
                }
                $data["akpartner"] .= "\n".$partner["kanam"].$dpunkt.$partner["kavor"];
                if ($partner["katel"] != "") $data["akpartner"] .= $komma." Tel.: ".$partner["katel"];
            }
        }
        foreach ($name_join as $name) {
            if ( $data[$name] != "" ) {
                if ($cfg["field"][$environment["parameter"][1]][$name][3] != "" ) {
                    $beschreibung =  $cfg["field"][$environment["parameter"][1]][$name][3]." ";
                } else {
                    $beschreibung = "";
                }
                $print .= $beschreibung.$data[$name]."\n";
                #echo $print;
            }

        unset ($data[$name]);
        }
        $table[] = array($colum[0]);
        $table[] = array($print);

                $pdf->ezTable($table,$colum,'',
                             array( "showLines"        =>0,
                                    "showHeadings"     =>0,
                                    "shaded"           =>2,
                                    "shadeCol"         => array(255,255,255),
                                    #"shadeCol2"        => 0.8,0.8,0.8,
                                    "fontSize"         =>9,
                                     /*"xPos"             =>($abstand+$breite),*/
                                     "xPos"             => 'left',
                                     "xOrientation"     => 'right',
                                     /*"width"            =>528,
                                     "maxWidth"         =>528,*/
                                     "width"            =>250,
                                     "maxWidth"         =>250,
                                     #"cols"             => $spalte
                                     "protectRows"      => 2
                                   )
                             );
        $blank = "";
        $print = "";
        $table = "";

        $pdf-> ezSetdy(-5);
        }
    // +++
    // haupttabelle
    #echo "<pre>";
    #print_r($data);
    #print_r($colum);
    #echo "</pre>";
   #$text1 = $data[0];
    // Text
    $pdf-> ezText($text1);

    // umbruch
    #$pdf->ezSetDy(-10);

    // spaltenweise ausgabe aus
    $pdf->ezColumnsStop();

    // ausgabe
    $pdf->ezStream();

    /*
    echo "<pre>";
    print_r($table);
    print_r($test);
    echo "</pre>";
    */

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
