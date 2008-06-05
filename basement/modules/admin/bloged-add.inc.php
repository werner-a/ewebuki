<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - add funktion";
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

    if ( $cfg["bloged"]["blogs"][$kat]["right"] == "" || 
    ( priv_check(make_ebene($environment["parameter"][1]),$cfg["bloged"]["blogs"][$kat]["right"]) || ( function_exists(priv_check_old) && priv_check_old("",$cfg["bloged"]["blogs"][$kat]["right"]) ) )
    ) {

        function create( $id ) {
            global $cfg,$db, $header, $debugging, $_POST,$environment,$pathvars,$ebene;

            if ( $cfg["bloged"]["blogs"][$ebene]["sortable"] == -1 ) {
                $sort = "0";
            } else {
                $sort = date("Y-m-d H:i:s");
            }

            $sqla  = "lang";
            $sqlb  = "'de'";
    
            $sqla .= ", label";
            $sqlb .= ", 'inhalt'";
    
            $sqla .= ", tname";
            $sqlb .= ", '".eCRC(make_ebene($environment["parameter"][1])).".".$id."'";
    
            $sqla .= ", crc32";
            $sqlb .= ", '-1'";
    
            $sqla .= ", ebene";
            $sqlb .= ", '".make_ebene($environment["parameter"][1])."'";
    
            $sqla .= ", kategorie";
            $sqlb .= ", '".$id."'";
    
            $sqla .= ", bysurname";
            $sqlb .= ", '".$_SESSION["surname"]."'";
    
            $sqla .= ", byforename";
            $sqlb .= ", '".$_SESSION["forename"]."'";

            $sqla .= ", byemail";
            $sqlb .= ", '".$_SESSION["email"]."'";
    
            $sqla .= ", byalias";
            $sqlb .= ", '".$_SESSION["alias"]."'";
    
            $sqla .= ", changed";
            $sqlb .= ", '".date("Y-m-d H:i:s")."'";

            $sqla .= ", html";
            $sqlb .= ", 0";

            if ( is_array($cfg["bloged"]["blogs"][$ebene]["kategorie"]) ) {
                if ( $environment["parameter"][2] == "") $environment["parameter"][2] = $environment["parameter"][1];
                $kategorie = "[KATEGORIE]".make_ebene($environment["parameter"][2])."[/KATEGORIE]";
            } else {
                $kategorie = "";
            }

            $content  = "[!][SORT]".$sort."[/SORT]".$kategorie;

            // fuellen per posts
            if ( $_POST["send"] != "" ) {
                foreach ( $_POST as $key => $value ) {
                    if ( $key == "send" ) continue;
                    if ( is_array($value) ) {
                        $time = mktime(0,0,0,(int)$value[1],(int)$value[0],(int)$value[2]);
                        if ( $value[0] == "" ) {
                            $time = mktime(1,0,0,1,1,1970);
                        }
                        $value = $time;
                    }

                    $content .= "\r\n[".$key."]".$value."[/".$key."]";
                }
                $header = $pathvars["virtual"].$ebene.".html";
            } else {
                if ( is_array($cfg["bloged"]["blogs"][$ebene]["addons"]) ) {
                    foreach ( $cfg["bloged"]["blogs"][$ebene]["addons"] as $key => $value ) {
                        (strpos($value["tag"],"=")) ? $endtag= substr($value["tag"],0,strpos($value["tag"],"=")): $endtag=$value["tag"];
                        $content .= "\r\n[".$value["tag"]."]".$value["content"]."[/".$endtag."]";
                    }
                }
            }
            $content .= "[/!]\r\n";
            if ( $cfg["bloged"]["blogs"][$ebene]["wizard"] != "" ) {
                $content .= "[!]wizard:".$cfg["bloged"]["blogs"][$ebene]["wizard"]."[/!]\r\n";
                $sqla .= ", status";
                $sqlb .= ", -1";
            } else {
                $sqla .= ", status";
                $sqlb .= ", 1";
            }
            if ( is_array($cfg["bloged"]["blogs"][$ebene]["tags"]) ) {
                foreach ( $cfg["bloged"]["blogs"][$ebene]["tags"] as $key => $value ) {
                    if (is_array($value)) {
                        $cont = $value["content"];
                        $para = $value["parameter"];
                        $value = $value["tag"];
                    } else {
                        $cont = "";
                        $para = "";
                    }
                    (strpos($value,"=")) ? $endtag= substr($value,0,strpos($value,"=")): $endtag=$value;
                    $content .= "[".$value.$para."]".$cont."[/".$endtag."]\r\n";
                }
            }

            $sqla .= ", content";
            $sqlb .= ", '".$content."'";

            $sql = "insert into ".$cfg["bloged"]["db"]["bloged"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";

            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result  = $db -> query($sql);
            if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
            if ( $cfg["bloged"]["blogs"][$ebene]["wizard"] != "" ) {
                if ( $header == "" ) $header = $pathvars["virtual"]."/wizard/show,".DATABASE.",".eCRC(make_ebene($environment["parameter"][1])).".".$id.",inhalt.html";
            } else {
                if ( $header == "" ) $header = $pathvars["virtual"]."/admin/contented/edit,".DATABASE.",".eCRC(make_ebene($environment["parameter"][1])).".".$id.",inhalt.html";
            }
        }

        $ebene = make_ebene($environment["parameter"][1]);

        $laenge = strlen(eCRC(make_ebene($environment["parameter"][1])))+2;
        $sql = "SELECT Cast(SUBSTR(tname,".$laenge.") as unsigned) AS id 
                  FROM ".$cfg["bloged"]["db"]["bloged"]["entries"]."
                 WHERE ".$cfg["bloged"]["db"]["bloged"]["key"]." LIKE '".eCRC(make_ebene($environment["parameter"][1])).".%' AND tname REGEXP '[0-9]$'
                 ORDER BY id DESC";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $id = $data["id"]+1;

        // funktions bereich fuer erweiterungen
        // ***

        // automatische generierung von beliebigen datensaetzen, durch parameter[2]
        if ( $environment["parameter"][3] != "" ) {
            for ( $i = 1; $i <= $environment["parameter"][3]; $i++ ) {
                create($id+$i);
            }
        }
        // +++
        // funktions bereich fuer erweiterungen

        create($id);
        header("Location: ".$header);

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
