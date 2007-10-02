<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: magic.php 476 2006-09-18 11:42:46Z chaot $";
// "image resize on demand";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

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

    // Schneidet einen ausgewaehlten Bereich eines startbildes aus
    // -----------------------------------------------------------
    //
    // benoetigte $_GET-Variable:
    //
    //     file: bestimmt das bild. besteht aus der ID und der Dateiart, getrennt durch einen punkt
    //           z.B. 1.jpg
    //
    // optionale $_GET-Variablen:
    //
    //   size_x: breite des neuerzeugten bildes    - NUR IN VERBINDUNG MIT size_y !!!!
    //   size_y: hoehe des neuerzeugten bildes     - NUR IN VERBINDUNG MIT size_x !!!!
    //     size: ueberschreibt size_x und size_y und macht sie unnoetig.
    //           erzeugt quadratisches Bild
    //     type: legt fest, welches Datei-Format das neue Bild haben soll.
    //           moegliche werte: jpg, jpeg, gif, png
    //  pickpos: von welcher seite soll das Startbild "angeschnitten werden"
    //           Standardwert: 5
    //                +-----+-----+-----+
    //                |     |     |     |
    //                |  1  |  2  |  3  |
    //                |     |     |     |
    //                +-----+-----+-----+
    //                |     |     |     |
    //                |  4  |  5  |  6  |
    //                |     |     |     |
    //                +-----+-----+-----+
    //                |     |     |     |
    //                |  7  |  8  |  9  |
    //                |     |     |     |
    //                +-----+-----+-----+
    //


    // Beispiele fuer die vorgehensweise des skripts
    //
    // Beispiel 1:
    //   Groesse des Startbildes:            1000x500px
    //   Gewuenschte Groesse des Zielbildes:  100x100px
    //
    //   - Das Skript schneidet vom Startbild ein Quadrat von 500px Breite und 500px Hoehe ab
    //     (Seitenverhaeltnis des Zielbildes und dem groesstmoeglichsten Bereich des Startbildes)
    //      bei einem pickposwert von 1,2,3: das auszuschneidende Quadrat wird am oberen Rand angelegt
    //      bei einem pickposwert von 4,5,6: das auszuschneidende Quadrat wird mittig angelegt
    //      bei einem pickposwert von 7,8,9: das auszuschneidende Quadrat wird am unteren Rand angelegt
    //   - Erzeugen eines leeren Bildes 100x100px
    //   - Verkleinern und Einfuegen des ausgeschnittenen Quadrats
    //
    // Beispiel 2:
    //   Groesse des Startbildes:            750x1000px
    //   Gewuenschte Groesse des Zielbildes:  100x100px
    //
    //   - Das Skript schneidet vom Startbild ein Quadrat von 750px Breite und 750px Hoehe ab
    //     (Seitenverhaeltnis des Zielbildes und dem groesstmoeglichsten Bereich des Startbildes)
    //      bei einem pickposwert von 1,4,7: das auszuschneidende Quadrat wird am linken Rand angelegt
    //      bei einem pickposwert von 2,5,8: das auszuschneidende Quadrat wird mittig angelegt
    //      bei einem pickposwert von 3,6,9: das auszuschneidende Quadrat wird am rechten Rand angelegt
    //   - Erzeugen eines leeren Bildes 100x100px
    //   - Verkleinern und Einfuegen des ausgeschnittenen Quadrats


    $pathvars["fileroot"] = dirname(dirname(__FILE__))."/";

    require $pathvars["fileroot"]."conf/site.cfg.php";
    require $pathvars["fileroot"]."conf/file.cfg.php";

    // welche dateitypen koennen ueber die $_GET-variablen ausgewaehlt werden
    $arrType = array("png","jpg","jpeg","gif");

    // testen, ob die fileID in der richten form angegeben wird
    if ( preg_match("/^([0-9]+)\.([a-zA-Z]{3,4})$/i",$_GET["file"],$match) ){
        // pfad zum bild bauen
        $image["file"] = $pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["o"]."img_".$_GET["file"];
        // testen, ob es das bild gibt
        if ( !file_exists($image["file"]) ) {
            die("can't find source file: "."img_".$_GET["file"]);
        }
        // groesse bestimmen
        $image["size"] = getimagesize($image["file"]);
        // bild einlesen und funktion aufrufen
        switch ( $image["size"][2] ) {
            case 1: // gif
                $img_src = @imagecreatefromgif($image["file"]);
                $img_dst = magic_resize( $img_src, $image,$arrType);
                echo imagegif($img_dst);
                break;
            case 2: // jpg
                $img_src = @imagecreatefromjpeg($image["file"]);
                $img_dst = magic_resize( $img_src, $image,$arrType);
                echo imagejpeg($img_dst);
                break;
            case 3: // png
                $img_src = @imagecreatefrompng($image["file"]);
                $img_dst = magic_resize( $img_src, $image,$arrType);
                echo imagepng($img_dst);
                break;
            default:
                die("source is not a valid image (gif, jpg, png)");
        }

        imagedestroy($img_src);
        imagedestroy($img_dst);

    }else{
        die("No valid File-ID: ".$_GET["file"]);
    }

    function magic_resize($img_src,$image,$arrType){
        // seitenverhaeltnisse berechnen (x/y)
        if ( $_GET["size"] ){
            $dst_w = $_GET["size"];
            $dst_h = $_GET["size"];
        }elseif( $_GET["size_x"] && $_GET["size_y"] ){
            $dst_w = $_GET["size_x"];
            $dst_h = $_GET["size_y"];
        }else{
            $dst_w = $image["size"][0];
            $dst_h = $image["size"][1];
        }
        $ratio_src  = ($image["size"][0]/$image["size"][1]);
        $ratio_dest = ($dst_w/$dst_h);

        if ( $dst_w > $image["size"][0] || $dst_h> $image["size"][1] ){
        // Startbild ist kleiner als Zielbild

            /* wo kommt das startbild im zielbild hin */
            $dst_x = ( $dst_w - $image["size"][0] )/2;
            $dst_y = ( $dst_h - $image["size"][1] )/2;
            /* welcher teil des startbildes wird genommen */
            $src_x = 0;
            $src_y = 0;
            $src_w = $image["size"][0];
            $src_h = $image["size"][1];

            // leeres image erstellen
            if ( function_exists(imagecreatetruecolor) ) {
                $img_dst = @imagecreatetruecolor($dst_w,$dst_h);
            } else {
                // gd < 2.0 fallback
                $img_dst = imagecreate($size_x,$size_y);
            }
            // weiss fuellen
            imagefilledrectangle($img_dst,0,0,$dst_w,$dst_h,imagecolorallocate($img_dst,255,255,255));
            // startbild einfuegen
            imagecopy($img_dst, $img_src, $dst_x, $dst_y, $src_x, $src_y,$src_w, $src_h);

        }else{
            /* wo beginnt das start- im zielbild */
            $dst_x = 0;
            $dst_y = 0;

            if ( $ratio_dest < $ratio_src ){
            // zielbild ist "hochformatiger" als das startbild

                /* welcher teil des startbildes wird genommen */
                $src_w = ( $image["size"][1]*$ratio_dest );
                $src_h = $image["size"][1];

                /* wo kommt das startbild im zielbild hin */
                if ( $_GET["pickpos"] == 1 || $_GET["pickpos"] == 4 || $_GET["pickpos"] == 7 ){
                    $src_x = 0;
                }elseif ( $_GET["pickpos"] == 3 || $_GET["pickpos"] == 6 || $_GET["pickpos"] == 9 ){
                    $src_x = ( $image["size"][0] - $src_w );
                }else{
                    $src_x = ( ( $image["size"][0] - $src_w ) / 2 );
                }
                $src_y = 0;
            }else{
            // zielbild ist "querformatiger" als das startbild

                /* welcher teil des startbildes wird genommen */
                $src_w = $image["size"][0];
                $src_h = ( $image["size"][0]/$ratio_dest );

                /* wo kommt das startbild im zielbild hin */
                $src_x = 0;
                if ( $_GET["pickpos"] == 1 || $_GET["pickpos"] == 2 || $_GET["pickpos"] == 3 ){
                    $src_y = 0;
                }elseif ( $_GET["pickpos"] == 7 || $_GET["pickpos"] == 8 || $_GET["pickpos"] == 9 ){
                    $src_y = ( $image["size"][1] - $src_h );
                }else{
                    $src_y = ( ( $image["size"][1] - $src_h ) / 2 );
                }
            }

            // leeres, weisses image erstellen und groesse aendern
            if ( function_exists(imagecreatetruecolor) ) {
                $img_dst = @imagecreatetruecolor($dst_w,$dst_h);
                imagefilledrectangle($img_dst,0,0,$dst_w,$dst_h,imagecolorallocate($img_dst,255,255,255));
                imagecopyresampled($img_dst, $img_src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
            } else {
                // gd < 2.0 fallback
                $img_dst = imagecreate($size_x,$size_y);
                imagefilledrectangle($img_dst,0,0,$dst_w,$dst_h,imagecolorallocate($img_dst,255,255,255));
                imagecopyresized($img_dst, $img_src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
            }
        }

        // bildformat wird bestimmt bzw. festgelegt
        if ( in_array($_GET["type"],$arrType) ){
            $header = "Content-type: image/".$_GET["type"];
        }else{
            $header = "Content-type: ".$image["size"]["mime"];
        }
        header($header);

        return $img_dst;
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
