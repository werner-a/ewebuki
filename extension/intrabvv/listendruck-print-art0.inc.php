<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
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

    // kopfdaten holen

    $result = $db -> query($cfg["db"]["sql"][1]);
    $data = $db -> fetch_array($result,1);

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
            $pdf->addText($links+40,$center-13,'10','Telefon '.$data["adtelver"].' - Telefax '.$data["adfax"]);
            $pdf->addText($links+186,$center+4,'14',$va);
            $pdf->addText($rechts+$cfg["title"][$environment["parameter"][1]]["move"],$center+4.5,'14',$cfg["title"][$environment["parameter"][1]]["text"]);
            $pdf->addText($rechts+35,$center-13,'10','Stand '.$cfg["heute"]);
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

        case "telint-StMF":
            // bfd kopf
            $bfdhead = $pdf->openObject();
            $bfd = $data["adststelle"];
            $links = 40;
            $center = 797;
            $rechts = 455;
            $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/wappen-gross-sw.png",$links,780,80);
            $pdf->addText($links+80,$center+15,'14','Bayer. Staatsministerium der Finanzen');
            $pdf->addText($links+80,$center,'14','Vermessungsabteilung');
            $pdf->addText($links+80,$center-18,'10','Telefon: '.$data["adtelver"]);
            #$pdf->addText($links+220,$center+15,'14',$bfd);
            $pdf->addText($rechts+$cfg["title"][$environment["parameter"][1]]["move"],$center+15,'14',$cfg["title"][$environment["parameter"][1]]["text"]);
            $pdf->addText($rechts+35,$center-18,'10','Stand: '.$cfg["heute"]);
            $pdf->line($links,$center-26,$rechts+117,$center-26);
            $pdf->closeObject();
            $pdf->addObject($bfdhead,'all');
            break;
    }



    // fuss

    if ( $HTTP_POST_VARS["abad"]) {
        $foot = $pdf->openObject();
        $va = $data["adststelle"];
        $links = 40;
        #$start = 190;
        $start = 100;
        $rechts = 455;

        $pdf->line($links,$start-20,$rechts+117,$start-20);
        $spalte = 80;

        $text = "* diese Personen sind überwiegend im Außendienst tätig";
        $pdf->addText($links,$start-41,'8',$text);
        #$text = "<i>Hinweis:</i> Sie können unsere Mitarbeiter <i><b>online</b></i> erreichen";
        #$pdf->addText($links,$start-65,'10',$text);
        #$text = "Die jeweilige <i><b>E-Mail Adresse</b></i> setzt sich zusammen aus: <i><b>vorname.nachname".substr($data["ademail"],10)."</b></i>";
        #$pdf->addText($links,$start-77,'10',$text);
        #$text = "Nachrichten an Mitarbeiter, die vorwiegend im Außendienst tätig sind, sollten an";
        #$pdf->addText($links,$start-101,'10',$text);
        #$text = $data["ademail"]." adressiert werden mit der Bitte um Weiterleitung";
        #$pdf->addText($links,$start-113,'10',$text);
        #$text = "an den gewünschten Empfänger.";
        #$pdf->addText($links,$start-125,'10',$text);

        #$pdf->addText($links+$spalte,$start-65,'10',.' - 0 Vermittlung' );

        $pdf->closeObject();
        $pdf->addObject($foot,'all');
    }


    // spaltenweise ausgabe an
    $pdf->ezColumnsStart(array( "num"=>1, "gap"=>20));


    // ueberschrift
    $pdf->ezText("Personen",10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1
    $pdf->ezSetDy(-5);


    // tabellenüberschrift und sql erstellen
    foreach($HTTP_POST_VARS as $name => $value) {
        if ( !in_array($name,$kck_main) ) {
            $colum[$name] = $cfg["field"][$environment["parameter"][1]][$name][0];
            // moeglichkeit fuer leere spalten
            if ( $name != "leer" ) {
                if ( $field != "" ) $field .= ",";
                $field .= " ".$name;
            }
            // Tabellenbreite berechnen
            #echo $name;
            $breite = $breite + $cfg["colsize"][$environment["parameter"][1]][$name];
            #echo $breite;
            // Grösse ermitteln
            if ($breite != "0") {
                $spalte[$name] = array("width"=>$cfg["colsize"][$environment["parameter"][1]][$name]);
            }
        }
    }
    if ($HTTP_POST_VARS["adid"] == 48 && $environment["parameter"][1] == "privat") {

        if($breite >= 528) {
    #echo $breite;
    #break;
    #    $spalte["abprivmobil"] = array("width" => 50);
            $spalte[0] = $spalte["abnamra"];
            unset ($spalte["abnamra"]);
        #echo "<pre>";
        #print_r($spalte);
        #echo "</pre>";
        #break;
        } else { unset($spalte);}
    #unset ($spalte);
        #        echo "<pre>";
#        print_r($spalte);
#        echo "</pre>";
        #echo $breite;
        #break;

    } else { $spalte = "";}
    #if ( is_array($firma_join) ) {
        #array_unshift($colum,"Firma");
    #}

  #  array_unshift($colum,"Firma");
    array_unshift($colum,"Name");

    // haupttabelle
    // ***

    // ueberschrift felder die im name_join array sind löschen
    foreach( $name_join as $name ) {
        if ( $HTTP_POST_VARS [$name]) {
            unset ($colum[$name]);
        }
    }

    // ueberschrift felder die im firma_join array sind löschen
    #if ( is_array($firma_join) ) {
        #foreach( $firma_join as $name ) {
            #if ( $HTTP_GET_VARS [$name]) {
                #unset ($colum[$name]);
            #}
        #}
    #}
    // haupttabelle erstellen
    $cfg["db"]["sql"][2] = str_replace("!felder!",$field,$cfg["db"]["sql"][2]);
    $cfg["db"]["sql"][2] = str_replace("!where!","abnet = ".$data["adbnet"]." AND acnet = ".$data["adcnet"],$cfg["db"]["sql"][2]);

    $result = $db -> query($cfg["db"]["sql"][2]);

    $rows = $db-> num_rows( $result);

    #echo $cfg["db"]["sql"][2];
    #$spalte[0] = array("width"=>150);

    while ( $data = $db->fetch_array($result,1) ) {

        if ( $data["abad"] == -1 ) {
            $data["abad"] = "*";
        } else {
            $data["abad"] = "";
        }

        // name zusammenfassen
        $leer = "";
        $test = "";
        foreach ($name_join as $name) {
            #if ( $data[$name] && $data[$name] != "" ) {
            if ( $data[$name] != "" ) {
                #$leer = " ";
                if ( $test != "" ) $leer = " ";
                if ( $name == "abamtbezkurz" ) $leer = ", ";
                $test .=  $leer.$data[$name];

            }
        unset ($data[$name]);
        }

        // firma1 und firma2 zusammenfassen zusammenfassen
        #if ( is_array($firma_join) ) {
            #$leer = "";
            #$firma = "";
            #foreach ($firma_join as $name) {
                #if ( $data[$name] != "" ) {
                    #if ( $firma != "" ) $leer = " ";
                    #if ( $name == "abamtbezkurz" ) $leer = ", ";
                    #$firma .=  $leer.$data[$name];
                    #unset ($data[$name]);
                #}
            #}
        #}

        // telefon kuerzen
        if ($data["abdsttel"])   {
            $data["abdsttel"] = substr($data["abdsttel"], strrpos($data["abdsttel"],"-")+1 ,strlen($data["abdsttel"]));
        }

        /*
        // Grösse ermitteln
        foreach ($data as $name => $value) {
            $spalte[$name] = array("width"=>$cfg["colsize"][$name]);
        }
        */
        #$text1 = $test;

        if ($rows <= 500 ) {
            #array_unshift($data,$test);

            #if ( is_array($firma_join) ) {
                $data[1] = $firma;
                #$data[0] = $test;
            #} else {
                $data[0] = $test;

            #}

            $table[] = $data;


        } else {
            $buchstabe = substr($data["abnamra"],0,1);
            if ( $merker != "" && $merker != $buchstabe ) {
                $pdf->ezTable($table,$colum,$merker,
                              array( "showHeadings"     =>1,
                                     "fontSize"         =>9,
                                     /*"xPos"             =>($abstand+$breite),*/
                                     "xPos"             => 'left',
                                     "xOrientation"     => 'right',
                                     "width"            =>528,
                                     "maxWidth"         =>528,
                                     "cols"             => $spalte
                                   )
                             );
                $table = "";
            }
            $merker = $buchstabe;
            $table[] = $data;
        }
    }

/*    echo "<pre>";
    print_r($table);
    print_r($colum);
    echo "</pre>";  */

    $dy = $pdf->ezTable($table,$colum,$merker,
                    array("showHeadings"     =>1,
                          "fontSize"         =>9,
                          /*"xPos"             =>($abstand+$breite),*/
                          "xPos"             => 'left',
                          "xOrientation"     => 'right',
                          "width"            => 528,
                          #"maxWidth"         =>450,
                          "cols"             => $spalte
                         )
                    );

    // +++
    // haupttabelle

    // tabelle links unten
    // ***
    if( $environment["parameter"][1] == "telint"
     || $environment["parameter"][1] == "telext" ) {
        // ueberschrift
        #$pdf->ezSetDy(-20);
        #$pdf->ezText("Räume",10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1

        // tabellenüberschrift
        foreach($HTTP_POST_VARS as $name => $value) {
            if ( in_array($name,$tab_left) ) {
                $colum_left[$name] = $cfg["field"][$environment["parameter"][1]][$name][0];
                if ( $field_left != "" ) $field_left .= ",";
                $field_left .= " ".$name;
            }
        }
        // nicht benötigte überschriften löschen
        $name_join = array("abtitel", "abnamvor");
        foreach( $name_join as $name ) {
            if ( $HTTP_POST_VARS [$name] && $HTTP_POST_VARS["abnamra"] ) {
                $breite = $breite - $cfg["colsize"][$name];
                unset ($colum_left[$name]);
            }
        }


        // tabelle erstellen
        $cfg["db"]["sql"][3] = str_replace("!felder!",$field_left,$cfg["db"]["sql"][3]);
        $result = $db -> query($cfg["db"]["sql"][3]);
        while ( $data = $db->fetch_array($result,1) ) {
            if ($title_left == "Räume") {
                // telefon kürzen
                if ($data["abdsttel"])   {
                    $data["abdsttel"] = substr($data["abdsttel"], strrpos($data["abdsttel"],"-")+1 ,strlen($data["abdsttel"]));
                }
            } elseif ($title_left == "Mobilfunk") {
                // name zusammenfassen
                $leer = "";
                foreach ($name_join as $name) {
                    if (($data[$name]) && ($data[$name] != "") && ($data["abnamra"])) {
                        $leer = " ";
                        if ($name == "abamtbezkurz") $leer = ", ";
                        $data["abnamra"] .= $leer.$data[$name];
                        unset ($data[$name]);
                    }
                }
            }
            $table_left[] = $data;
        }

        if (is_array($table_left)) {
            $pdf->addText(45,$dy-30,12,$title_left);
        }
        // tabelle
        $pdf->ezSetDy(-35);
        $pdf->ezTable($table_left,$colum_left,"",
                        array("showHeadings"     =>1,
                              "fontSize"         =>9,
                              #"xPos"             =>($abstand+$breite),
                              "xPos"             => 'left',
                              "xOrientation"     => 'right',
                              #"width"            =>550,
                              "maxWidth"         =>528,
                              "cols"             => $spalte
                              )
                      ) ;
    }
    // +++
    // tabelle links unten


    // tabelle rechts unten
    // ***

    // tablelle bauen wenn checkbox "Mobil" gedrückt
    if ($environment["parameter"][1] == "telint" AND $HTTP_POST_VARS["abdstmobil"] != "") {

        // ueberschrift
        #$pdf->ezSetDy(-20);
        #$pdf->ezText("Räume rechts",10,array( "justification" => "right", "left" => -5 )); // "leading"=>20, "spacing"=>1

        // tabellenüberschrift
        foreach($HTTP_POST_VARS as $name => $value) {
            if ( in_array($name,$tab_right) ) {
                $colum_right[$name] = $cfg["field"][$environment["parameter"][1]][$name][0];
                if ( $field_right != "" ) $field_right .= ",";
                $field_right .= " ".$name;
            }
        }

        // nicht benötigte überschriften löschen
        $name_join = array("abtitel", "abnamvor");
        foreach( $name_join as $name ) {
            if ( $HTTP_POST_VARS [$name] && $HTTP_POST_VARS["abnamra"] ) {
                $breite = $breite - $cfg["colsize"][$name];
                unset ($colum_right[$name]);
            }
        }



        // tabelle erstellen
        #reset($name_join);
        $cfg["db"]["sql"][4] = str_replace("!felder!",$field_right,$cfg["db"]["sql"][4]);
        $result = $db -> query($cfg["db"]["sql"][4]);
        while ( $data = $db->fetch_array($result,1) ) {
            // name zusammenfassen
            $leer = "";
            foreach ($name_join as $name) {
                if (($data[$name]) && ($data[$name] != "") && ($data["abnamra"])) {
                    $leer = " ";
                    if ($name == "abamtbezkurz") $leer = ", ";
                    $data["abnamra"] .= $leer.$data[$name];
                    unset ($data[$name]);
                }
            }
            $table_right[] = $data;
        }

        if (is_array($table_right)) {
            $pdf->addText(305,$dy-30,12,$title_right);
        }

        // tabelle
            $pdf->ezSetY($dy);
            $pdf->ezSetDy(-35);
            $pdf->ezTable($table_right,$colum_right,"",
                            array("showHeadings"     =>1,
                                  "fontSize"         =>9,
                                  /*"xPos"             =>($abstand+$breite),*/
                                  "xPos"             => 310,
                                  "xOrientation"     => 'right',
                                  /*"width"            =>550,*/
                                  "maxWidth"         =>528
                                  #"cols"             => $spalte
                                  )
                          ) ;

        // +++
        // tabelle rechts unten
    }

    // Text
    $pdf-> ezText($text1);

    // umbruch
    #$pdf->ezSetDy(-10);

    // spaltenweise ausgabe aus
    $pdf->ezColumnsStop();

    // ausgabe
    $pdf->ezStream();

    #echo "<pre>";
    #print_r($table);
    #print_r($colum);
    #echo "</pre>";


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
