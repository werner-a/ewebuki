<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: captcha.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "captcha builder";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2009 Werner Ammon ( wa<at>chaos.de )

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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            function captcha_randomize($length,$config) {
                $random = "";
                while ( strlen($random) < $length ) {
                    $random .= substr($config["letter_pot"], rand(0,strlen($config["letter_pot"])), 1);
                }
                return $random;
            }


            function captcha_create($text,$config) {
                global $cfg;
                // anzahl der zeichen
                $count = strlen($text);
                // schriftarten festlegen
                $ttf = $config["ttf"];
                // schriftgroesse festlegen
                $ttf_size = $config["font"]["size"];
                // schriftabstand rechts
                $ttf_x = $config["font"]["x"];
                // schriftabstand oben
                $ttf_y = $config["font"]["y"];

                // hintergrund erstellen
                $ttf_img = ImageCreate($count*2*$ttf_size,2*$ttf_size);
                // bgfarbe festlegen
                $bg_color = ImageColorAllocate ($ttf_img, $config["bg_color"][0], $config["bg_color"][1], $config["bg_color"][2]);
                // textfarbe festlegen
                $font_color = ImageColorAllocate($ttf_img, $config["font_color"][0], $config["font_color"][1], $config["font_color"][2]);
                // schrift in bild einfuegen
                foreach ( str_split($text) as $key=>$character ) {
                    // schriftwinkel festlegen
                    $ttf_angle = rand(-25,25);
                    // schriftarten auswaehlen
                    $ttf_font = $ttf[rand(0,(count($ttf)-1))];
                    imagettftext($ttf_img, $ttf_size, $ttf_angle, $ttf_size*2*$key+$ttf_x, $ttf_y, $font_color, $ttf_font, $character);
                }
                // bild temporaer als datei ausgeben
                $captcha_crc = crc32($text.$config["randomize"]);
                $captcha_name = "captcha-".$captcha_crc.".png";
                $captcha_path = $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"];
                imagepng($ttf_img,$captcha_path.$captcha_name);
                // bild loeschen
                imagedestroy($ttf_img);
            }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>