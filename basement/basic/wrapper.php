<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "file wrapper";
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
    # http://dev2/file/jpg/6/o/filename.extension

    $pathvars["fileroot"] = dirname(dirname(__FILE__))."/";

    require $pathvars["fileroot"]."conf/site.cfg.php";
    require $pathvars["fileroot"]."conf/file.cfg.php";

    // subdir support
    $specialvars["subdir"] = trim(dirname(dirname($_SERVER["SCRIPT_NAME"])),"/");
    if ( $specialvars["subdir"] != "" ) {
        $value = str_replace( $specialvars["subdir"]."/", "", $_SERVER["REQUEST_URI"] );
    } else {
        $value = $_SERVER["REQUEST_URI"];
    }

    $value = explode("/",$value);

    if ( $value[6] == "d" ) {
        echo "<pre>";
        print_r($pathvars["filebase"]);
        print_r($value);
        echo "</pre>";
    }


    // path finden
    $path["img"] = $pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"][$value[4]];
    if ( $value[4] == "tn" ) {
        $path["img"] = $path["img"]."tn_";
    } else {
        $path["img"] = $path["img"]."img_";
    }

    $path["doc"] = $pathvars["filebase"]["doc"]."doc_";
    $path["arc"] = $pathvars["filebase"]["arc"]."arc_";


    // filetyp auswerten
    switch( $value[2] ) {
        case "gif":
            $type ="image/gif";
            $filepath = $path["img"];
            break;
        case "jpg":
            $type ="image/jpeg";
            $filepath = $path["img"];
            break;
        case "png":
            $type ="image/png";
            $filepath = $path["img"];
            break;
        case "odp":
            $type ="application/odp";
            $filepath = $path["doc"];
            break;
        case "ods":
            $type ="application/ods";
            $filepath = $path["doc"];
            break;
        case "odt":
            $type ="application/odt";
            $filepath = $path["doc"];
            break;
        case "pdf":
            $type ="application/pdf";
            $filepath = $path["doc"];
            break;
        case "bz2":
            $type ="application/bz2";
            $filepath = $path["arc"];
            break;
        case "gz":
            $type ="application/gz";
            $filepath = $path["arc"];
            break;
        case "zip":
            $type ="application/zip";
            $filepath = $path["arc"];
            break;
        default:
            die("Bad File");
    }


    // filenamen zusammensetzen
    $file = $pathvars["filebase"]["maindir"].$filepath.$value[3].".".$value[2];

    if ( $value[6] == "d" ) {
        echo $type."<br>";
    } else {
        header("Content-type: ".$type);
    }

    if ( $value[6] == "d" ) {
        echo $file."<br>";
    } else {
        $f = fopen($file, 'r');
        $datei = fread($f, filesize ($file));
        fclose ($f);
        echo $datei;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
