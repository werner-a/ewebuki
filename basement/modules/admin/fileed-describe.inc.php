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


    if ( $environment[parameter][1] == "check" ) {
        foreach ( $_FILES as $key => $value ) {
            $file = file_verarbeitung($cfg["file"]["new"], $key, $cfg["filesize"], $cfg["filetyp"], $cfg["file"]["maindir"]);

            if ( $file["returncode"] == 0 ) {
                rename($cfg["file"]["maindir"].$cfg["file"]["new"].$file["name"],$cfg["file"]["maindir"].$cfg["file"]["new"].$HTTP_SESSION_VARS["uid"]."_".$file["name"]);
            } else {
                $ausgaben["output"] .= "Ergebnis: ".$file["name"]." ";
                $ausgaben["output"] .= file_error($file["returncode"])."<br>";
            }

        }
        if ( $ausgaben["output"] == "" ) {
            header("Location: ".$cfg["basis"]."/".$environment["kategorie"].",add.html");
        } else {
            $ausgaben["output"] .= "<br><br><a href=\"".$cfg["basis"]."/".$environment["kategorie"].",add.html\">Trotzdem weiter</a>";
            $mapping["main"] = "default1";
        }



    } elseif ( $environment[parameter][1] == "add" ) {

        $form_values = $HTTP_POST_VARS;

        $dp = opendir($cfg["file"]["maindir"].$cfg["file"]["new"]);
        while ( $file = readdir ($dp) ) {

            $pos = strpos($file,"_");
            if ( substr($file,0,$pos+1) == $HTTP_SESSION_VARS["uid"]."_" ) {

                $found = true;
                break;
            }
        }
        closedir($dp);
        if ( $found != true ) header("Location: ".$cfg["basis"]."/list.html");

        $ausgaben["image_print"] .= "<img src=\"".$pathvars["webroot"]."/images/magic.php?path=".$pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file."&size=280\">";

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );

        // form elemente erweitern
        $modify  = array ("ffname" => str_replace($HTTP_SESSION_VARS["uid"]."_","",$file));
        foreach($modify as $key => $value) {
            if ( $HTTP_POST_VARS[$value] == "" )
            {
                $element[$key] = str_replace($key."\"", $key."\" value=\"".$value."\"", $element[$key]);
            }
        }
        $element["upload"] = "";
        $element["fid"] = "";

        // was anzeigen
        # automatik sollte gehen $mapping[main] = crc32($environment[ebene]).".".$environment[kategorie];
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["basis"]."/".$environment["kategorie"].",add,verify.html";

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        if ( $environment["parameter"][2] == "verify" ) {

            // form eingaben prüfen
            form_errors( $form_options, $form_values );

            if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS[image] != "" ) ) {
                $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer", "bnet", "cnet" );
                    foreach($form_values as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                         if ( $sqla != "" ) $sqla .= ",";
                         $sqla .= " ".$name;
                         if ( $sqlb != "" ) $sqlb .= ",";
                         $sqlb .= " '".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                $sqla .= ", ffart";
                $sqlb .= ", '".substr(strrchr($file,"."),1)."'";
                $sqla .= ", fuid";
                $sqlb .= ", '".$HTTP_SESSION_VARS["uid"]."'";
                $sqla .= ", fdid";
                $sqlb .= ", '".$HTTP_SESSION_VARS["custom"]."'";

                #$ldate = $HTTP_POST_VARS[ldate];
                #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);

                $sql = "insert into ".$cfg["db"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                $result  = $db -> query($sql);
                if ( $result ) {
                    $file_id = $db->lastid();

                    $file_org = $cfg["file"]["maindir"].$cfg["file"]["new"].$file;
                    $file_ext = strrchr($file,".");

                    switch ($file_ext) {
                        case ".zip":
                            rename($file_org, $cfg["file"]["maindir"].$cfg["file"]["archiv"]."arc_".$file_id.$file_ext);
                            break;
                        case ".pdf":
                            rename($file_org, $cfg["file"]["maindir"].$cfg["file"]["text"]."doc_".$file_id.$file_ext);
                            break;
                        case ".png":
                            // quellbild in speicher einlesen
                            $img_src = @imagecreatefrompng($file_org);
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["big"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."big", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["medium"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."medium", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["small"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."small", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["thumb"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."thumbnail", "tn" );

                            // orginal bild nach max resizen oder loeschen
                            if ( $cfg["size"]["max"] == "" || imagesx($img_src) <= $cfg["size"]["max"] || imagesy($img_src) <= $cfg["size"]["max"] ) {
                                rename($file_org,$cfg["file"]["maindir"].$cfg["file"]["picture"].$pathvars["filebase"]["pic"]["o"]."img_".$file_id.".png");
                            } else {
                                img_resize( $file_org, $file_id, $img_src, $cfg["size"]["max"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."original", "img" );
                                unlink($file_org);
                            }

                            // speicher des quellbild freigeben
                            imagedestroy($img_src);
                            break;
                        case ".jpg":
                            // quellbild in speicher einlesen
                            $img_src = @imagecreatefromjpeg($file_org);

                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["big"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."big", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["medium"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."medium", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["small"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."small", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["thumb"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."thumbnail", "tn" );

                            // orginal bild nach max resizen oder loeschen
                            #if ( $cfg["size"]["max"] == "" || imagesx($img_src) <= $cfg["size"]["max"] || imagesy($img_src) <= $cfg["size"]["max"] ) {
                            if ( $cfg["size"]["max"] == "" || (imagesx($img_src) <= $cfg["size"]["max"] && imagesy($img_src) <= $cfg["size"]["max"] )) {
                                rename($file_org,$cfg["file"]["maindir"].$cfg["file"]["picture"].$pathvars["filebase"]["pic"]["o"]."img_".$file_id.".jpg");
                            } else {
                                img_resize( $file_org, $file_id, $img_src, $cfg["size"]["max"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."original", "img" );
                                unlink($file_org);
                            }
                            // speicher des quellbild freigeben
                            imagedestroy($img_src);
                            break;
                        default:
                            echo "da ist der wurm drin";
                    }

                }

                if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];
                #header("Location: ".$ausgaben[form_referer]);
                header("Location: ".$cfg[basis]."/".$environment[kategorie].",add.html");

            }
        }

    } elseif ( $environment["parameter"][1] == "edit" ){#&& $rechte[$cfg["right"]["adress"]] == -1) {

        while ( count($HTTP_SESSION_VARS["images_memo"]) > 0 ) {
            $wert = current($HTTP_SESSION_VARS["images_memo"]);
            $found = true;
            break;
        }

        if ( count($HTTP_POST_VARS) == 0 ) {
            $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$wert."'";
            $result = $db -> query($sql);
            $form_values = $db -> fetch_array($result,1);
        } else {
            $form_values = $HTTP_POST_VARS;
            $form_values["ffart"] = substr($form_values["ffname"],strrpos($form_values["ffname"],".")+1,3);
        }

        // bildausgabe
        if (strstr($form_values["ffname"],".pdf")) {
            $ausgaben["image_print"] = "<img src=\"".$pathvars["images"]."pdf_big.png\">";
        } else {
            $ausgaben["image_print"] .= "<img src=\"".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["m"]."img_".$wert.".".$form_values["ffart"]."\">";
        }

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );

        // form elemente erweitern

        $element["upload"] = "Ersetzen durch<br>";
        $element["upload"] .= "<input type=\"file\" name=\"upload\">";
        $element["upload"] .= "<br><br>Die Endung der vorhandenen Datei muß mit der Endung der neuen Datei identisch sein!";


        // was anzeigen
        #$mapping["main"] = crc32($environment["ebene"]).".modify";
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["basis"]."/describe,edit,verify.html";


        // referer immer auf list setzen
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        if ( $environment["parameter"][2] == "verify" ) {

            // form eingaben prüfen
            form_errors( $form_options, $form_values );

            // files ersetzen
            if ( $_FILES["upload"]["name"] != "" ) {
                if ( substr($form_values["ffname"],-4,1) == "." ) {
                    $dateiendung = substr($form_values["ffname"],-3,3);
                } else {
                    $dateiendung = substr($form_values["ffname"],-4,4);
                }
                $replace = file_verarbeitung($cfg["file"]["new"], "upload", $cfg["filesize"], array($dateiendung), $cfg["file"]["maindir"]);
                if ( $replace["returncode"] == 0 ) {
                    #rename($cfg["file"]["maindir"].$cfg["file"]["new"].$file["name"],$cfg["file"]["maindir"].$cfg["file"]["new"].$HTTP_SESSION_VARS["uid"]."_".$file["name"]);
                    $file_id = $form_values["fid"];

                    $file = $replace["name"];
                    $file_org = $cfg["file"]["maindir"].$cfg["file"]["new"].$file;
                    $file_ext = strrchr($file,".");

                    switch ($file_ext) {
                        case ".zip":
                            rename($file_org, $cfg["file"]["maindir"].$cfg["file"]["archiv"]."arc_".$file_id.$file_ext);
                            break;
                        case ".pdf":
                            rename($file_org, $cfg["file"]["maindir"].$cfg["file"]["text"]."doc_".$file_id.$file_ext);
                            break;
                        case ".png":
                            // quellbild in speicher einlesen
                            $img_src = @imagecreatefrompng($file_org);
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["big"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."big", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["medium"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."medium", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["small"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."small", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["thumb"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."thumbnail", "tn" );

                            // orginal bild nach max resizen oder loeschen
                            if ( $cfg["size"]["max"] == "" || imagesx($img_src) <= $cfg["size"]["max"] || imagesy($img_src) <= $cfg["size"]["max"] ) {
                                rename($file_org,$cfg["file"]["maindir"].$cfg["file"]["picture"].$pathvars["filebase"]["pic"]["o"]."img_".$file_id.".png");
                            } else {
                                img_resize( $file_org, $file_id, $img_src, $cfg["size"]["max"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."original", "img" );
                                unlink($file_org);
                            }

                            // speicher des quellbild freigeben
                            imagedestroy($img_src);
                            break;
                        case ".jpg":
                            // quellbild in speicher einlesen
                            $img_src = @imagecreatefromjpeg($file_org);

                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["big"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."big", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["medium"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."medium", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["small"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."small", "img" );
                            img_resize( $file_org, $file_id, $img_src, $cfg["size"]["thumb"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."thumbnail", "tn" );

                            // orginal bild nach max resizen oder loeschen
                            #if ( $cfg["size"]["max"] == "" || imagesx($img_src) <= $cfg["size"]["max"] || imagesy($img_src) <= $cfg["size"]["max"] ) {
                            if ( $cfg["size"]["max"] == "" || (imagesx($img_src) <= $cfg["size"]["max"] && imagesy($img_src) <= $cfg["size"]["max"] )) {
                                rename($file_org,$cfg["file"]["maindir"].$cfg["file"]["picture"].$pathvars["filebase"]["pic"]["o"]."img_".$file_id.".jpg");
                            } else {
                                img_resize( $file_org, $file_id, $img_src, $cfg["size"]["max"], $cfg["file"]["maindir"].$cfg["file"]["picture"]."original", "img" );
                                unlink($file_org);
                            }
                            // speicher des quellbild freigeben
                            imagedestroy($img_src);
                            break;
                        default:
                            echo "da ist der wurm drin";
                    }
                } else {
                    $ausgaben["form_error"] .= "Ergebnis: ".$replace["name"]." ";
                    $ausgaben["form_error"] .= file_error($replace["returncode"])."<br>";
                }
            }

            // ohne fehler sql bauen und ausfuehren
            if ( $ausgaben["form_error"] == "" /* && ( $HTTP_POST_VARS[submit] != "" || $HTTP_POST_VARS[image] != "" ) */ ){
                #echo "ja";

                $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer", "add", "add_x", "add_y", "delete", "delete_x", "delete_y","akfggeb","akfgstart" );
                foreach($form_values as $name => $value) {
                    if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                        if ( $sqla != "" ) $sqla .= ", ";
                        $sqla .= $name."='".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern

                $sql = "update ".$cfg["db"]["entries"]." SET ".$sqla." WHERE ".$cfg["db"]["key"]."='".$wert."'";
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "mainsql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                unset($HTTP_SESSION_VARS["images_memo"][$wert]);
                if (count($HTTP_SESSION_VARS["images_memo"]) > 0) {
                    header("Location: ".$cfg["basis"]."/describe,edit.html");
                } else {
                    header("Location: ".$cfg["basis"]."/list.html");
                }
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            }
        }
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function img_resize( $img_org, $img_id, $img_src, $max_size, $img_path, $img_name ) {

        // hochformat oder querformat
        $src_width = imagesx($img_src);
        $src_height = imagesy($img_src);
        if ( $src_width > $src_height ) {
            $dest_width = $max_size;
            $dest_height = (int)(($max_size * $src_height) / $src_width );
        } else {
            $dest_height = $max_size;
            $dest_width = (int)(($max_size * $src_width) / $src_height );
        }

        // leeres image erstellen
        $img_dst = @imagecreatetruecolor($dest_width,$dest_height);
        if ( $img_dst ) {
            // groesse aendern
            imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
        } else {
            // gd < 2.0 fallback
            $img_dst = imagecreate($dest_width,$dest_height);
            imagecopyresized($img_dst, $img_src, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
        }

        if ( strrchr($img_org,".") == ".jpg" ) {
            imagejpeg($img_dst,$img_path."/".$img_name."_".$img_id.".jpg");
        } elseif ( strrchr($img_org,".") == ".png" ) {
            imagepng($img_dst,$img_path."/".$img_name."_".$img_id.".png");
        } else {
            echo "da ist der wurm drin";
        }

        // speicher freigeben
        imagedestroy($img_dst);
    }


    function file_error($error) {
        if ( $error == 0 ) {
            $meldung .= "There is no error, the file uploaded with success.";
        } elseif ( $error == 1 ) {
            $meldung .= "The uploaded file exceeds the upload_max_filesize directive ( ".get_cfg_var(upload_max_filesize)." ) in php.ini.";
        } elseif ( $error == 2 ) {
            $meldung .= "The uploaded file exceeds the MAX_FILE_SIZE ( ".$HTTP_POST_VARS["MAX_FILE_SIZE"]."kb ) directive that was specified in the html form.";
        } elseif ( $error == 3 ) {
            $meldung .= "The uploaded file was only partially uploaded.";
        } elseif ( $error == 4 ) {
            $meldung .= "No file was uploaded.";
        } elseif ( $error == 7 ) {
            $meldung .= "File Size to big.";
        } elseif ( $error == 8 ) {
            $meldung .= "File Type not valid.";
        } elseif ( $error == 9 ) {
            $meldung .= "File Name already exists.";
        } elseif ( $error == 10 ) {
            $meldung .= "Unknown Error. Maybe post_max_size directive ( ".get_cfg_var(post_max_size)." ) in php.ini. Please do not try again.";
        } elseif ( $error == 11 ) {
            $meldung .= "Sorry, you need minimal PHP/4.x.x to handle uploads!";
        }
        return $meldung;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
