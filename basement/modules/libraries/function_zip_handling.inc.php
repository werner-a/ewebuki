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

    function zip_handling( $file, $extract_dest="", $restrict_type=array(), $restrict_size="", $restrict_dir="" ) {
        global $db, $pathvars;

        $zip = new ZipArchive;
        if ($zip->open($file) === TRUE) {
            $zip_content = array();
            // beschraenkung, welche unterordner im zip bearbeitet werden sollen
            $restrict = explode(",",$restrict_dir);
            // zip durchgehen und dateien-informationen holen
            for ( $i=0; $i<$zip->numFiles; $i++ ) {
                $buffer = $zip->statIndex($i);
                $path = explode("/",$buffer["name"]);
                $name = array_pop($path);
                $dir  = implode("/",$path);
                if ( $name != "" ) {
                    $zip_content[$buffer["index"]] = array(
                            "name" => $name,
                             "dir" => $dir,
                            "file" => $buffer["name"],
                            "size" => $buffer["size"],
                    );
                }
            }

            if ( $extract_dest != "" ) {
                foreach ( $zip_content as $key=>$value ) {
                    // falls angegeben werden nur bestimmte unterordner abgearbeitet
                    if ( ($restrict_dir == "" || in_array($value["dir"],$restrict))
                      && $value["name"] != "" ) {
                        // 1. datei auf den server schreiben
                        $target = $extract_dest.str_replace("/","--",$value["file"]);
                        $handle = fopen($target,"a");
                        fwrite($handle, $zip->getFromIndex($key));
                        fclose($handle);
                        // 2. file ueberpruefen
                        $error = file_validate($target, $value["size"], $restrict_size, $restrict_type);
                        // 3. loeschen oeder umbenennen
                        if ( $error == 0 ) {
                            rename($target,dirname($target)."/".$_SESSION["uid"]."_".basename($target));
//                             rename($file,dirname($file)."/xxx_".basename($file));
                        } else {
                            unlink($target);
                        }
//                         unlink($target);
//                     } else {
// echo "<p> Auspacken NICHT moeglich</p>";
                    }
                }
            }
        }

        return $zip_content;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>