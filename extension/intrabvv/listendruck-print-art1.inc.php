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

    // seiten raender
    $pdf->ezSetMargins(220,110,50,40); // top, bottom, left, right

    // kopfdaten holen
    $result = $db -> query($cfg["db"]["sql"][1]);
    $data = $db -> fetch_array($result,1);

    // kopf
    $head = $pdf->openObject();
    $va = $data["adststelle"];
    $links = 40;
    $center = 797;
    $rechts = 455;
    $pdf->addText($links,$center+4,'14','Vermessungsamt');
    $pdf->addPngFromFile($pathvars["fileroot"]."/images/net/auge-klein.png",$links+110,$center,34);
    $pdf->addText($links+146,$center+4,'14',$va);


    $pdf->addText($links,$center-13,'10',$data["adstr"]);
    $pdf->addText($links,$center-25,'10',$data["adplz"].' '.$data["adort"]);

    $pdf->addText($rechts+$cfg["title"][$environment["parameter"][1]]["move"],$center+4.5,'14',$cfg["title"][$environment["parameter"][1]]["text"]);

    $pdf->addText($rechts+35,$center-25,'10','Stand '.$cfg["heute"]);
    $pdf->line($links,$center-35,$rechts+117,$center-35);

    $spalte = 80;
    $pdf->addText($links,$center-65,'10','Telefon');
    $pdf->addText($links+$spalte,$center-65,'10',$data["adtelver"].' (Vermittlung)' );
    $pdf->addText($links,$center-77,'10','Durchwahl');

    $basis = substr($data["adtelver"],0,strrpos($data["adtelver"],"-")+1);
    $pdf->addText($links+$spalte,$center-77,'10',$basis.' Nebenstelle');
    $pdf->addText($links,$center-89,'10','Telefax');
    $pdf->addText($links+$spalte,$center-89,'10',$data["adfax"]);
    $pdf->addText($links,$center-101,'10','E-Mail');
    $pdf->addText($links+$spalte,$center-101,'10',$data["ademail"]);
    $pdf->addText($links,$center-113,'10','Online');
    $pdf->addText($links+$spalte,$center-113,'10',$data["adinternet"]);

    // amtsleiter holen
    $pdf->addText($links,$center-149,'10','Amtsleiter:');
    $sql = "SELECT abgrad, abtitel, abnamra, abnamvor FROM db_adrb WHERE abid = '".$data["adleiter"]."'";
    $result = $db -> query($sql);
    $leiter = $db -> fetch_array($result,1);
    $text = $leiter["abnamra"].' '.$leiter["abnamvor"];
    if ( $leiter["abgrad"] != "" ) {
        $text = $leiter["abgrad"]." ".$text;
    } elseif ( $leiter["abtitel"] != "" ) {
        $text = $leiter["abtitel"]." ".$text;
    }
    $pdf->addText($links+$spalte,$center-149,'10',$text);


    $pdf->closeObject();
    $pdf->addObject($head,'all');


    // fuss
    $foot = $pdf->openObject();
    $va = $data["adststelle"];
    $links = 40;
    $start = 100;
    #$rechts = 455;

    $pdf->line($links,$start-20,$rechts+117,$start-20);
    $spalte = 80;

    $text = "* diese Personen sind überwiegend im Außendienst tätig";
    $pdf->addText($links,$start-41,'8',$text);
    #$text = "<i>Hinweis:</i> Die <i><b>E-Mail Adresse</b></i> unserer Mitarbeiter setzt sich zusammen aus:";
    #$pdf->addText($links,$start-65,'10',$text);
    #$text = "<i><b>vorname.nachname".substr($data["ademail"],10)."</b></i>";
    #$pdf->addText($links,$start-77,'10',$text);
    #$text = "Nachrichten an Mitarbeiter, die überwiegend im Außendienst tätig sind, sollten an";
    #$pdf->addText($links,$start-101,'10',$text);
    #$text = $data["ademail"]." adressiert werden mit der Bitte um Weiterleitung";
    #$pdf->addText($links,$start-113,'10',$text);
    #$text = "an den gewünschten Empfänger.";
    #$pdf->addText($links,$start-125,'10',$text);

    #$pdf->addText($links+$spalte,$start-65,'10',.' - 0 Vermittlung' );

    $pdf->closeObject();
    $pdf->addObject($foot,'all');


    ###
    // ueberschrift
    $pdf->ezText("<b>Nebenstellen - Mitarbeiter</b>",10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1
    $pdf->ezText("alphabetisch sortiert",10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1
    $pdf->ezSetDy(-5);

    // spaltenweise ausgabe an
    $pdf->ezColumnsStart(array( "num"=>2, "gap"=>20));

    // tabellenüberschrift und sql erstellen
    foreach($HTTP_POST_VARS as $name => $value) {
        if ( !in_array($name,$kck_main) ) {
            $colum[$name] = $cfg["field"][$environment["parameter"][1]][$name][0];
            if ( $field != "" ) $field .= ",";
            $field .= " ".$name;
            // Tabellenbreite berechnen
            $breite = $breite + $cfg["colsize"][$name];
            // Grösse ermitteln
            #$spalte[$data["content"]] = array("width"=>$cfg["colsize"][$name]);
        }
    }




    // haupttabelle
    // ***

    // ueberschrift felder die im name_join array sind löschen
    foreach( $name_join as $name ) {
        if ( $HTTP_POST_VARS [$name]) {
            unset ($colum[$name]);
        }
    }
    array_unshift($colum,"Name");


    // haupttabelle erstellen
    $cfg["db"]["sql"][2] = str_replace("!felder!",$field,$cfg["db"]["sql"][2]);
    $cfg["db"]["sql"][2] = str_replace("!where!","abnet = ".$data["adbnet"]." AND acnet = ".$data["adcnet"],$cfg["db"]["sql"][2]);
    $result = $db -> query($cfg["db"]["sql"][2]);
    $rows = $db-> num_rows( $result);
    while ( $data = $db->fetch_array($result,1) ) {

        if ( $data["abad"] == -1 ) {
            $data["abad"] = "*";
        } else {
            $data["abad"] = "";
        }

        // name zusammenfassen
        $leer = "";
        $feld0 = "";
        foreach ($name_join as $name) {
            #if ( $data[$name] && $data[$name] != "" ) {
            if ( $data[$name] != "" ) {
                $leer = " ";
                if ($name == "abamtbezkurz") $leer = ", ";
                $feld0 .=  $leer.$data[$name];
                unset ($data[$name]);
            }
        }

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
            $data[0] = $feld0;
            $table[] = $data;

        } else {
            $buchstabe = substr($data["abnamra"],0,1);
            if ( $merker != "" && $merker != $buchstabe ) {
                ###
                $pdf->ezTable($table,$colum,$merker,
                              array( "showLines"        =>0,
                                     "showHeadings"     =>0,
                                     "shaded"           =>0,
                                     "fontSize"         =>9,
                                     "colGap"           =>0,
                                     /*"xPos"             =>($abstand+$breite),*/
                                     "xPos"             => 'left',
                                     "xOrientation"     => 'right',
                                     "width"            =>528,
                                     "maxWidth"         =>528,
                                     /*"width"            =>264,
                                     "maxWidth"         =>264,*/
                                     "cols"             => $spalte
                                   )
                             );
                $table = "";
            }
            $merker = $buchstabe;
            $table[] = $data;
        }
    }
    #echo "<pre>";
    #print_r($colum);
    #echo "</pre>";

    $dy = $pdf->ezTable($table,$colum,$merker,
                    array("showLines"        =>0,
                          "showHeadings"     =>0,
                          "shaded"           =>0,
                          "fontSize"         =>9,
                          #"colGap"           =>0,
                          #"xPos"             =>($abstand+$breite),
                          "xPos"             => 'left',
                          "xOrientation"     => 'right',
                          "width"            =>250,
                          "maxWidth"         =>250
                          #"cols"             => $spalte
                         )
                    );

    // +++
    // haupttabelle


    // tabelle eins
    // ***

    // ueberschrift
    $pdf->ezSetDy(-500);
    #$pdf->ezSetDy(-30);


    #$pdf->addText(45,$dy-30,12,$title_right);

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
    if ($HTTP_POST_VARS["abdstmobil"]) {
        $cfg["db"]["sql"][4] = str_replace("!felder!",$field_right,$cfg["db"]["sql"][4]);
        #echo $cfg["db"]["sql"][4];
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
    }
    if (is_array($table_right)) {
        $dy = $pdf->ezText('<b>'.$title_eins.'</b>',10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1
        $dy = $pdf->ezText('',10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1
        $pdf->ezSetDy(-5);
        $test = -30;
    }

    // tabelle
    #$pdf->ezSetY($dy);
    #$pdf->ezSetDy(-35);
    $dy = $pdf->ezTable($table_right,$colum_right,"",
                    array("showLines"        =>0,
                          "showHeadings"     =>0,
                          "shaded"           =>0,
                          "fontSize"         =>9,
                          "xPos"             => 'left',
                          "xOrientation"     => 'right',
                          #"xPos"             =>($abstand+$breite),
                          #"xPos"             => 310,
                          #"xOrientation"     => 'right',
                          "width"            =>250,
                          "maxWidth"         =>250
                          #"cols"             => $spalte
                          )
                  ) ;

    // +++
    // tabelle eins



    // tabelle zwei
    // ***

    // ueberschrift
#    if (is_array($table_left)) {
    $pdf->ezSetDy($test);
#        $dy = $pdf->ezText('<b>'.$title_zwei.'</b>',10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1
#        $dy = $pdf->ezText('',10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1
#        $pdf->ezSetDy(-5);
#    }
    #$pdf->addText('',$dy-30,12,$title_left);

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
        if ($title_zwei == "Räume") {
            // telefon kürzen
            if ($data["abdsttel"])   {
                $data["abdsttel"] = substr($data["abdsttel"], strrpos($data["abdsttel"],"-")+1 ,strlen($data["abdsttel"]));
            }
        } elseif ($title_zwei == "Mobilfunk") {
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

    // ueberschrift
    if (is_array($table_left)) {
    #$pdf->ezSetDy(-30);
        $dy = $pdf->ezText('<b>'.$title_zwei.'</b>',10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1
        $dy = $pdf->ezText('',10,array( "justification" => "left", "left" => -5 )); // "leading"=>20, "spacing"=>1
        $pdf->ezSetDy(-5);
    }

    // tabelle
    #$pdf->ezSetDy(-10);
    $dy = $pdf->ezTable($table_left,$colum_left,"",
                    array("showLines"        =>0,
                          "showHeadings"     =>0,
                          "shaded"           =>0,
                          "fontSize"         =>9,
                          #"xPos"             =>($abstand+$breite),
                          "xPos"             => 'left',
                          "xOrientation"     => 'right',
                          "width"            =>250,
                          "maxWidth"         =>250
                          #"cols"             => $spalte
                          )
                  ) ;

    // +++
    // tabelle zwei




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
