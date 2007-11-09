<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menued-functions.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "funktion loader";
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

    function file_validate($file, $file_size, $limit, $valid_type) {
        global $pathvars, $debugging;

        // find file basename, extension
        $file_extension = strtolower(substr(strrchr($file,"."),1));
        $file_basename = basename($file,".".$file_extension);

        // i don't want jpeg files
        if ( $file_extension == "jpeg" ) $file_extension = "jpg";

        $error_code = 0;

        if ( $error_code == 0 ) {
            if ( count($valid_type) == 0 || (!in_array($file_extension, $valid_type) && !array_key_exists($file_extension, $valid_type)) ) {
                $error_code = 11;
            } elseif ( $file_size >= $limit ) {
                $error_code = 10;
            }
        }

        $images = array("gif"  => 1, "jpg"  => 2, "jpeg" => 2, "png"  => 3);
        $weitere = array("pdf" => "%PDF", "zip" => "PK", "odt" => "PK", "ods" => "PK", "odp" => "PK", "bz2" => "BZ", "gz" => chr(hexdec("1F")).chr(hexdec("8B")).chr(hexdec("08")).chr(hexdec("08")));
        // grafik formate testen
        if ( $images[$file_extension] != "" && $error_code == 0 ) {

            /*
            1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP,
            7 = TIFF(intel byte order), 8 = TIFF(motorola byte order),
            9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF
            */
            $imgsize = getimagesize($file);
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "chk type soll: ".$images[$file_extension].$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "chk type ist: ".$imgsize[2].$debugging["char"];
            if ( $images[$file_extension] != $imgsize[2] ) {
                $error_code = 11;
            }
        // weitere formate testen
        } elseif ( $weitere[$file_extension] != "" && $error_code == 0 ) {
            $fp = fopen($file, "r");
            $buffer = fgets($fp, 5);
            if ( strpos($buffer,$weitere[$file_extension]) === false ) {
                $array["returncode"] = 11;
            }
            unset($buffer);
            fclose($fp);
        // sonstiges ablehnen
        } elseif ( $error_code == 0 ) {
            $error_code = 11;
        }

        if ( $error_code == 0 ) {
            $MySafeModeUid = getmyuid();
            passthru ("chuid ".$file["tmp_name"]." ".$MySafeModeUid);
            chmod($file,0664);
        }

        return $error_code;

    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>