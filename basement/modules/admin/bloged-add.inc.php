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


    $check_url = $_POST["kategorie"];
    if ( $_POST["kategorie"] == "" ) $check_url = $_POST["link"];

    if ( $cfg["bloged"]["blogs"][$_POST["link"]]["right"] == "" ||
    ( priv_check($check_url,$cfg["bloged"]["blogs"][$_POST["link"]]["right"]) || ( function_exists(priv_check_old) && priv_check_old("",$cfg["bloged"]["blogs"][$_POST["link"]]["right"]) ) )
    ) {

        function create( $id ) {
            global $cfg,$db, $header, $debugging, $_POST,$environment,$pathvars,$ebene;
            if ( $cfg["bloged"]["blogs"][$ebene]["sort"][1] == -1 ) {
                $sort = "1000";
            } else {
                if ( $_POST["SORT"] != "" ) {
                    $sort = $_POST["SORT"];
                } else {
                    $sort = date("Y-m-d H:i:s");
                }
            }

            $sqla  = "lang";
            $sqlb  = "'de'";
    
            $sqla .= ", label";
            $sqlb .= ", 'inhalt'";
    
            $sqla .= ", tname";
            $sqlb .= ", '".eCRC($_POST["link"]).".".$id."'";
    
            $sqla .= ", crc32";
            $sqlb .= ", '-1'";
    
            $sqla .= ", ebene";
            $sqlb .= ", '".$_POST["link"]."'";
    
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

            if ( $cfg["bloged"]["blogs"][$ebene]["category"] != "" ) {
                $kategorie = "[".$cfg["bloged"]["blogs"][$ebene]["category"]."]".$_POST["kategorie"]."[/".$cfg["bloged"]["blogs"][$ebene]["category"]."]";
            } else {
                $kategorie = "";
            }

            $content  = "[!][".$cfg["bloged"]["blogs"][$ebene]["sort"][0]."]".$sort."[/".$cfg["bloged"]["blogs"][$ebene]["sort"][0]."]".$kategorie;

            if ( is_array($cfg["bloged"]["blogs"][$ebene]["addons"]) ) {
              #  $header = $pathvars["virtual"].$_POST["kategorie"].".html";
                foreach ( $cfg["bloged"]["blogs"][$ebene]["addons"] as $key => $value ) {
                    if ( $value == "SORT" ) continue;
                    if ( !is_array($value) ) {
                        $cont = $_POST[$value];
                        $para = "";
                    } else {
                       if ( $_POST[$value["tag"]] != "" ) {
                            $cont = $_POST[$value["tag"]];
                        } else {
                            $cont = $value["content"];
                        }
                        $para = $value["parameter"];
                        $value = $value["tag"];
                    }
                    (strpos($value,"=")) ? $endtag= substr($value,0,strpos($value,"=")): $endtag=$value;
                    $content .= "\r\n[".$value.$para."]".$cont."[/".$endtag."]\r\n";
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
                if ( $header == "" ) $header = $pathvars["virtual"]."/wizard/show,".DATABASE.",".eCRC($_POST["link"]).".".$id.",inhalt,,,.html";
            } else {
                if ( $header == "" ) $header = $pathvars["virtual"]."/admin/contented/edit,".DATABASE.",".eCRC($_POST["link"]).".".$id.",inhalt.html";
            }
        }

        $ebene = $_POST["link"];

        $laenge = strlen(eCRC($ebene))+2;
        $sql = "SELECT Cast(SUBSTRING(tname,".$laenge.") as unsigned) AS id
                  FROM ".$cfg["bloged"]["db"]["bloged"]["entries"]."
                 WHERE ".$cfg["bloged"]["db"]["bloged"]["key"]." LIKE '".eCRC($ebene).".%' AND tname REGEXP '[0-9]$'
                 ORDER BY id DESC";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $id = $data["id"]+1;

        // funktions bereich fuer erweiterungen
        // ***

        // automatische generierung von beliebigen datensaetzen, durch parameter[2]
        if ( $_POST["anzahl"] != "" ) {
            for ( $i = 1; $i <= $_POST["anzahl"]; $i++ ) {
                create($id+$i);
            }
        }
        // +++
        // funktions bereich fuer erweiterungen

        create($id);
        if ( $_POST["special"] ) {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        } else {
            header("Location: ".$header);
        }

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
