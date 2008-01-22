<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
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

    /* um funktionen z.b. in der kategorie add zu laden, leer.cfg.php wie folgt aendern
    /*
    /*    "function" => array(
    /*                 "add" => array( "function1_name", "function2_name"),
    */

    // beschreibung der funktion
    if ( in_array("function_name", $cfg["fileed"]["function"][$environment["kategorie"]]) ) {
        function function_name(  $var1, $var2 = "") {
           ### put your code here ###
        }
    }

    // flexible thumbnail builder
    if ( in_array("thumbnail", $cfg["fileed"]["function"][$environment["kategorie"]]) ) {

        function thumbnail() {

            global $_SESSION, $cfg, $pathvars, $file;

            $thumbnail = "";
            $dp = opendir($cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"]);
            while ( $file = readdir($dp) ) {
                $info  = explode( "_", $file, 2 );
                if ( $info[0] == $_SESSION["uid"] ) {
                    $extension = strtolower(substr(strrchr($info[1],"."),1));
                    $type = $cfg["fileed"]["filetyp"][$extension];
                    if ( $type == "img" ) {
                        $path = $cfg["fileed"]["fileopt"][$type]["tnpath"];
                        $filename = $file;
                    } else {
                        $path = $cfg["fileed"]["fileopt"][$type]["tnpath"].ltrim($cfg["fileed"]["iconpath"],"/");
                        $filename = $cfg["fileed"]["fileopt"][$type]["thumbnail"];
                    }
                    $thumbnail = $pathvars["webroot"]."/images/magic.php?path=".$path.$filename."&size=280";
                    break;
                }
            }
            closedir($dp);
            return $thumbnail;
        }
    }


    // picture resize
    if ( in_array("resize", $cfg["fileed"]["function"][$environment["kategorie"]]) ) {

        function resize( $img_org, $img_id, $img_src, $max_size, $img_path, $img_name ) {

            $src_width = imagesx($img_src);
            $src_height = imagesy($img_src);
            // maximale breite und hoehe bestimmen
            $dest_size = explode(":",$max_size);
            if ( count($dest_size) == 1 ) {
                $max_width = $max_size;
                $max_height = $max_size;
            } elseif ( $dest_size[1] == "" ) {
                $max_width = $dest_size[0];
                $max_height = $dest_size[0];
            } else {
                $max_width = $dest_size[0];
                $max_height = $dest_size[1];
            }
            $src_ratio = $src_width/$src_height;
            $max_ratio = $max_width/$max_height;
            // groesse des zielbildes bestimmen
            $src_x = 0;
            $src_y = 0;
            if ( $src_ratio <= $max_ratio  ) {
                $dest_height = $max_height;
                $dest_width = $src_ratio*$max_height;
                if ( $dest_size[2] == "crop" ) {
                    // bildausschnitt
                    $dest_width = $max_width;
                    $dest_height = $max_height;
                    $src_y = ($src_height - $src_width/$max_ratio )/2;
                    $src_height = $src_width/$max_ratio;
                }
            } else {
                $dest_height = $max_width/$src_ratio;
                $dest_width = $max_width;
                if ( $dest_size[2] == "crop" ) {
                    // bildausschnitt
                    $dest_width = $max_width;
                    $dest_height = $max_height;
                    $src_x = ($src_width - $src_height*$max_ratio )/2;
                    $src_width = $src_height*$max_ratio;
                }
            }
            $file_ext = strtolower(substr(strrchr($img_org,"."),1));

            // gd < 2.0 fallback
            if ( function_exists(imagecreatetruecolor) ) {


                // leeres image erstellen
                $img_dst = @imagecreatetruecolor($dest_width,$dest_height);

                if ( $file_ext == "gif" ) {
                    $colorTrans = imagecolortransparent($img_src);
                    imagepalettecopy($img_src,$img_dst);
                    imagefill($img_dst, 0, 0, $colorTrans);
                    imagecolortransparent($img_dst, $colorTrans);
                    imagetruecolortopalette($img_dst,true,256);
                } elseif ( $file_ext == "png" ) {
                    imageantialias($img_dst,true);
                    imagealphablending($img_dst, False);
                    imagesavealpha($img_dst, True);
                }

                // groesse aendern
                imagecopyresampled($img_dst, $img_src, 0, 0, $src_x, $src_y, $dest_width, $dest_height, $src_width, $src_height);

            } else {

                // transparente farbe finden
                $colorTrans = imagecolortransparent($img_src);

                // leeres image erstellen
                $img_dst = imagecreate($dest_width,$dest_height);

                // palette kopieren
                imagepalettecopy($img_dst,$img_src);

                // mit transparenter farbe fuellen
                imagefill($img_dst,0,0,$colorTrans);

                // transparent setzen
                imagecolortransparent($img_dst, $colorTrans);

                // groesse aendern
                imagecopyresized($img_dst, $img_src, 0, 0, $src_x, $src_y, $dest_width, $dest_height, $src_width, $src_height);
            }

            switch ( $file_ext ) {
                case "gif":
                    imagegif($img_dst,$img_path."/".$img_name."_".$img_id.".gif");
                    break;
                case "jpg":
                    imagejpeg($img_dst,$img_path."/".$img_name."_".$img_id.".jpg");
                    break;
                case "png":
                    imagepng($img_dst,$img_path."/".$img_name."_".$img_id.".png");
                    break;
                default:
                    die("config error. can't handle ".$extension." file");
            }

            // speicher freigeben
            imagedestroy($img_dst);
        }
    }


    // file arrange
    if ( in_array("resize", $cfg["fileed"]["function"][$environment["kategorie"]]) ) {

        function arrange( $id, $source, $file, $move=-1 ) {

            global $cfg, $pathvars;

            $extension = strtolower(substr(strrchr($file,"."),1));
            $type = $cfg["file"]["filetyp"][$extension];
            if ( $type == "img" ) {
                // quellbild in speicher einlesen
                switch ( $extension ) {
                    case "gif":
                        $img_src = @imagecreatefromgif($source);
                        break;
                    case "jpg":
                        $img_src = @imagecreatefromjpeg($source);
                        break;
                    case "png":
                        $img_src = @imagecreatefrompng($source);
                        break;
                    default:
                        die("config error. can't handle ".$extension." file");
                }
                $art = array( "s" => "img", "m" => "img", "b" => "img", "tn" => "tn" );
                foreach ( $art as $key => $value ) {
                    resize( $source, $id, $img_src, $cfg["file"]["size"][$key], $cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["base"]["pic"][$key], $value, $cfg["file"]["resize"][$key] );
                }

                // orginal bild nach max resizen oder loeschen
                $max_size = explode(":",$cfg["file"]["size"]["max"]);
                if ( count($dest_size) == 1 ) {
                    $max_width = $max_size[0];
                    $max_height = $max_size[1];
                } else {
                    $max_width = $cfg["file"]["size"]["max"];
                    $max_height = $cfg["file"]["size"]["max"];
                }
                if ( $cfg["file"]["size"]["max"] == "" || imagesx($img_src) <= $max_width || imagesy($img_src) <= $max_height ) {
                    if ( $move == -1 ) {
                        rename( $source, $cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["base"]["pic"]["o"].$cfg["file"]["fileopt"][$type]["name"]."_".$id.".".$extension);
                    } else {
                        copy( $source, $cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["base"]["pic"]["o"].$cfg["file"]["fileopt"][$type]["name"]."_".$id.".".$extension);
                    }
                } else {
                    resize( $source, $id, $img_src, $cfg["file"]["size"]["max"], $cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["base"]["pic"]["o"], $cfg["file"]["fileopt"][$type]["name"] );
                    if ( $move == -1 ) {
                        unlink( $source );
                    }
                }

                // speicher des quellbild freigeben
                imagedestroy($img_src);
            } else {
                if ( $move == -1 ) {
                    rename( $source, $cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["fileopt"][$type]["name"]."_".$id.".".$extension);
                } else {
                    copy($source, $cfg["file"]["fileopt"][$type]["path"].$cfg["file"]["fileopt"][$type]["name"]."_".$id.".".$extension);
                }
            }


        }
    }

    // check, ob dateien geloescht werden duerfen
    if ( in_array("file_check", $cfg["fileed"]["function"][$environment["kategorie"]]) ) {

        // function content_check
        // ------------------
        //
        //          Ueberprueft, ob content mit dieser Datei vorhanden ist
        //
        // Parameter:
        //
        //     $id: ID der zu untersuchenden Datei
        //
        // Rueckgabewerte:
        //
        //       True: Content vorhanden
        //             $arrError: Links zu den entsprechenden Seiten
        //      False: kein Content vorhanden
        //

        function content_check($id) {
            global $db, $_SESSION, $cfg, $pathvars, $file, $debugging;

            $content_error = "";
            $old = "\_".$id.".";
            $new = "/".$id."/";
            $sql2 = "SELECT DISTINCT ".$cfg["fileed"]["db"]["content"]["path"]."
                       FROM ".$cfg["fileed"]["db"]["content"]["entries"]."
                      WHERE ".$cfg["fileed"]["db"]["content"]["content"]." LIKE '%".$old."%'
                         OR ".$cfg["fileed"]["db"]["content"]["content"]." LIKE '%".$new."%'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql2: ".$sql2.$debugging["char"];

            /* multi-db-support */
            if ( $cfg["fileed"]["db"]["multi"]["change"] == True ) {
                $sql = "SELECT ".$cfg["fileed"]["db"]["multi"]["field"]."
                          FROM ".$cfg["fileed"]["db"]["multi"]["entries"]."
                         WHERE ".$cfg["fileed"]["db"]["multi"]["where"];
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql (multi-db): ".$sql.$debugging["char"];
                $result_db = $db -> query($sql);
                while ( $data_db = $db -> fetch_array($result_db,1) ) {
                    $db -> selectDb($data_db["addbase"],FALSE);

                    $result2 = $db -> query($sql2);
                    while ( $data2 = $db -> fetch_array($result2,1) ) {

                        $ebene = $data2["ebene"]."/";
                        $kategorie = $data2["kategorie"].".html";

                        $url = str_replace($environment["fqdn"][0],$db -> getdb(),$pathvars["menuroot"]).$ebene.$kategorie;

                        $label = str_replace($environment["fqdn"][0],$db -> getdb(),$pathvars["menuroot"]).$ebene.$kategorie;
                        $found_in[] .= "<a href=\"".$url."\">".$label."</a>"."<br />";
                    }

                }
                $db -> selectDb(DATABASE,FALSE);
            }

            $result2 = $db -> query($sql2);
            $num_rows = $db -> num_rows($result2);
            if ( $num_rows > 0 && $error == 0 ){
                while ( $data2 = $db -> fetch_array($result2,1) ) {
                    $ebene = $data2["ebene"]."/";
                    $kategorie = $data2["kategorie"].".html";
                    $url = str_replace($environment["fqdn"][0],"www",$pathvars["menuroot"]).$ebene.$kategorie;
                    $label = $ebene.$kategorie;
                    $found_in[] = "<a href=\"".$url."\">".$label."</a>";
                }
            }

            if ( count($found_in) > 0 ){
                return $found_in;
            } else {
                return array();
            }
        }
    }

    // funktionen fuer die compilation-liste
    if ( in_array("compilationlist", $cfg["fileed"]["function"][$environment["kategorie"]]) ) {
        function compilation_list( $select="", $length=25 ) {
            global $db;

            // selection-bilder, werden aus der site_file geholt
            $sql = "SELECT *
                    FROM site_file
                    WHERE fhit LIKE '%#p%'";
            $result = $db -> query($sql);

            $compilations = array();
            while ( $data = $db -> fetch_array($result,1) ){
                // alle gruppeneintraege holen
                preg_match_all("/#p([0-9]+)[,]*([0-9]*)#/i",$data["fhit"],$match);
                foreach ( $match[1] as $key=>$value ){

                    if ( $match[2][$key] == "" ){
                        $sort[$value] = 0;
                    } else {
                        $sort[$value] = $match[2][$key];
                    }
                    // falsche ausgabe verhindern, falls zwei dateien die gleiche sortiernummer hat
                    if ( is_array($dataloop["compilations"][$value]["pics"]) ){
                        while ( is_array($dataloop["compilations"][$value]["pics"][$sort[$value]]) ){
                            $sort[$value]++;
                        }
                    }

                    $compilations[$value]["id"]         = $value;
                    $compilations[$value]["name"]       = "---";
                    $compilations[$value]["name_short"] = "---";
                    $compilations[$value]["desc"]      .= $data["fdesc"]." ";

                    if ( $value == $select ) {
                        $compilations[$value]["select"] = ' selected="true"';
                    } else {
                        $compilations[$value]["select"] = "";
                    }
                }
            }

            // aus dem content werden die gruppen rausgezogen und ggf. das dataloop um einen gruppennamen ergaenzt
            $sql = "SELECT * FROM site_text WHERE content LIKE '%[/SEL]%'";
            $result = $db -> query($sql);
            while ( $data = $db -> fetch_array($result,1) ) {

                preg_match_all("/(.*)\[SEL=(.*)\](.*)\[\/SEL\]/Usi",$data["content"],$match);

                foreach ( $match[2] as $key=>$value ){

                    // den fall abfangen, dass die selection in [E]-Tags steht
                    if ( !strstr($match[0][$key],"[E]")
                        || ( strstr($match[0][$key],"[E]") && strstr($match[0][$key],"[/E]") ) ){

                        $parameter = explode(";",$value);
                        $sel_name  = $match[3][$key];
                        $id = $parameter[0];

                        // gibt es keine bilder zur gruppe, werden die fehlenden dataloop-eintraege nachgeholt
                        if ( !is_array($compilations[$id]) ){
                            $compilations[$id]["id"]   = $id;
                            if ( $id == $select ) {
                                $compilations[$id]["select"] = ' selected="true"';
                            } else {
                                $compilations[$id]["select"] = "";
                            }
                        }

                        if ( $compilations[$id]["name"] == "---"
                          || $compilations[$id]["name"] == "" ){
                            $name = $sel_name;
                        } else {
                            // name wird nur erfasst, wenn er nicht schon drinsteht
                            $buffer_names = explode(", ",$compilations[$id]["name"]);
                            if ( !in_array($sel_name,$buffer_names) ){
                                $name = $compilations[$id]["name"].", ".$sel_name;
                            }
                        }
                        $name = preg_replace(array("/(, )*$/","/(, ){2}/"),
                                             "",
                                             $name
                        );

                        $compilations[$id]["name"] = $name;
                        if ( strlen($name) > $length ) {
                            $compilations[$id]["name_short"] = substr($name,0,$length)."...";
                        } else {
                            $compilations[$id]["name_short"] = $name;
                        }
                    }

                }
            }

            krsort($compilations);

            return $compilations;
        }
    }


    ### platz fuer weitere funktionen ###

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
