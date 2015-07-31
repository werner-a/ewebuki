<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// funtion_zip_handling.inc.php v1 krompi
// funktion loader: zip handling
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function zip_handling( $file, $extract_dest="", $restrict_type=array(), $restrict_size="", $restrict_dir="", $compid="", $section=array(), $wave_thru=0 ) {
        global $db, $pathvars, $cfg, $ausgaben;

        $text_files = array();
        $zip = new ZipArchive;
        if ($zip->open($file) == TRUE) {
            $zip_content = array();
            // beschraenkung, welche unterordner im zip bearbeitet werden sollen
            $restrict = explode(",",$restrict_dir);
            // zip durchgehen und dateien-informationen holen
            for ( $i=0; $i<$zip->numFiles; $i++ ) {
                $buffer = $zip->statIndex($i);
                $path = explode("/",$buffer["name"]);
                $name = str_replace(array("/"," "),
                                    array("--","_"),
                                    array_pop($path)
                );
                $dir  = implode("/",$path);
                $extension = trim(strrchr($name,"."),".");
                if ( $name != "" && array_key_exists($extension,$cfg["file"]["filetyp"]) ) {
                    $zip_content[$buffer["index"]] = array(
                            "name" => $name,
                             "dir" => $dir,
                            "file" => str_replace(array("/"," "),
                                                  array("--","_"),
                                                  $buffer["name"]
                                      ),
                            "path" => $buffer["name"],
                            "size" => $buffer["size"],
                    );
                }
                // textdateien in eigenes array
                if ( preg_match("/.*\.txt$/i",$name) ) {

                    $content = addslashes($zip->getFromIndex($buffer["index"]));
                    $textfile = explode("\n",$content);
                    $var_name = "";$array = array();
                    // text wird in zeilen aufgesplittet und in abgelegt (vgl. $section bzw $cfg["fileed"]["zip_handling"]["sektions"])
                    foreach ( $textfile as $value ) {
                        if ( array_key_exists(strtolower(trim($value)), $section) ) {
                            $var_name = $section[strtolower(trim($value))];
                            continue;
                        }
                        if ( $var_name != "" && $value != "" ) {
                            if ( $array[$var_name] != "" ) {
                                $array[$var_name] .= "\n";
                            }
                            $array[$var_name] .= trim($value);
                        }
                    }

                    $key = str_replace(array("/"," "),
                                        array("--","_"),
                                        $buffer["name"]
                    );

                    $text_files[$key] = array(
                        "id" => $buffer["index"],
                        "content" => addslashes(substr($zip->getFromIndex($buffer["index"]),0,400))
                    );

                    foreach ( $array as $label => $value ) {
                        $text_files[$key][$label] = $value;
                    }
                } elseif ( preg_match("/.*\.csv$/i",$name) ) {
                    $content = addslashes($zip->getFromIndex($buffer["index"]));
                    $textfile = explode("\n",$content);
                    foreach ( $textfile as $value ) {
                        if ( trim($value) == "" ) continue;
                        $csv_info = explode(";",$value);
                        $key = str_replace(array("/"," "),
                                           array("--","_"),
                                           array_shift($csv_info).".txt"
                        );
                        foreach ( $section as $label ) {
                            $text_files_csv[$key][$label] = array_shift($csv_info);
                        }
                    }
                }
            }
            if ( is_array($text_files) && is_array($text_files_csv) ) $text_files = array_merge($text_files,$text_files_csv);

            // auspacken
            if ( $extract_dest != "" ) {
                unset($_SESSION["zip_extracted"]);
                $i = 1;
                foreach ( $zip_content as $key=>$value ) {
                    // falls angegeben werden nur bestimmte unterordner abgearbeitet
                    if ( ($restrict_dir == "" || in_array($value["dir"],$restrict))
                      && $value["name"] != "" ) {

                        // 1. datei auf den server schreiben
                        if ( !is_array($text_files[$value["name"]]) /*&& !preg_match("/\.csv$/",$value["name"])*/ ) {
                            $tmp_file = $extract_dest.str_replace(array("/"," "),
                                                                array("--","_"),
                                                                $value["file"]
                            );
                            $handle = fopen($tmp_file,"a");
                            fwrite($handle, $zip->getFromIndex($key));
                            fclose($handle);
                        } else {
                            // textdatei wird ausgelassen
                            unset($zip_content[$key]);
                            continue;
                        }

                        // 2. file ueberpruefen
                        $error = file_validate($tmp_file, $value["size"], $restrict_size, $restrict_type);

                        // 3. file weiterverarbeiten (umbenennen/loeschen)
                        if ( $error == 0 ) {
                            $new_file = $_SESSION["uid"]."_".basename($tmp_file);
                            rename($tmp_file,dirname($tmp_file)."/".$new_file);
                            // session schreiben fuer weitere verarbeitung
                            if ( $compid != "" && $restrict_type[strtolower(substr(strrchr($tmp_file,"."),1))] == "img" ) {
                                $compilation = "#p".$compid.",".($i*10)."#";
                                $i++;
                            } else {
                                $compilation = "";
                            }

                            $new_file = $_SESSION["uid"]."_".basename($tmp_file);
                            $fdesc  = $_POST["zip_fdesc"];
                            $funder = $_POST["zip_funder"];
                            $fhit   = $_POST["zip_fhit"];
                            if ( $text_files[basename($tmp_file).".txt"]["fdesc"] != "" ) {
                                $fdesc .= "\n".$text_files[basename($tmp_file).".txt"]["fdesc"];
                            } elseif ( $text_files[$value["name"].".txt"]["fdesc"] != "" ) {
                                $fdesc .= "\n".$text_files[$value["name"].".txt"]["fdesc"];
                            }
                            if ( $text_files[basename($tmp_file).".txt"]["funder"] != "" ) {
                                $funder .= "\n".$text_files[basename($tmp_file).".txt"]["funder"];
                            } elseif ( $text_files[$value["name"].".txt"]["funder"] != "" ) {
                                $funder .= "\n".$text_files[$value["name"].".txt"]["funder"];
                            }
                            if ( $text_files[basename($tmp_file).".txt"]["fhit"] != "" ) {
                                $fhit .= "\n".$text_files[basename($tmp_file).".txt"]["fhit"];
                            } elseif ( $text_files[$value["name"].".txt"]["fhit"] != "" ) {
                                $fhit .= "\n".$text_files[$value["name"].".txt"]["fhit"];
                            }
                            $_SESSION["zip_extracted"][$new_file] = array(
                                       "name" => $new_file,
                                "compilation" => $compilation,
                                      "fdesc" => trim($fdesc),
                                     "funder" => trim($funder),
                                       "fhit" => trim($fhit),
                                  "wave_thru" => $wave_thru,
                            );
                            // zip_content soll die nicht auszupackenden dateien ausgeben
                            unset($zip_content[$key]);
                            if ( is_array( $text_files[basename($tmp_file).".txt"] ) ) unset($zip_content[$text_files[basename($tmp_file).".txt"]["id"]]);
                        } else {
                            unlink($tmp_file);
                        }
                    } else {
// echo "<p> Auspacken NICHT moeglich</p>";
                    }
                }
            }
        }


        return $zip_content;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
