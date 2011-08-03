<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "contented - edit funktion";
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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    // erlaubnis bei intrabvv speziell setzen
    $database = $environment["parameter"][1];
    if ( is_array($_SESSION["katzugriff"]) ) {
        if ( in_array("-1:".$database.":".$environment["parameter"][2],$_SESSION["katzugriff"]) ) $erlaubnis = -1;
    }

    if ( is_array($_SESSION["dbzugriff"]) ) {
        if ( in_array($database,$_SESSION["dbzugriff"]) ) $erlaubnis = -1;
    }

    $db->selectDb($database,FALSE);


    // spezial-check fuer artikel
    $tname2path = tname2path($environment["parameter"][2]);
    $erlaubnis = "";
    if ( is_array($cfg["bloged"]["blogs"][substr($tname2path,0,strrpos($tname2path,"/"))])
        && $cfg["bloged"]["blogs"][substr($tname2path,0,strrpos($tname2path,"/"))]["category"] != "" ) {
        $kate = $cfg["bloged"]["blogs"][substr($tname2path,0,strrpos($tname2path,"/"))]["category"];
        $laenge = strlen($kate)+2;
        $art_version = "1";
        if ( $environment["parameter"][6] != "" ) {
            $art_version = $environment["parameter"][6];
        }
        $sql = "SELECT SUBSTR(content,POSITION('[".$kate."]' IN content)+".$laenge.",POSITION('[/".$kate."]' IN content)-".$laenge."-POSITION('[".$kate."]' IN content) )as check_url from site_text where version=".$art_version." AND tname = '".$environment["parameter"][2]."'";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $erlaubnis = priv_check($data["check_url"],$cfg["contented"]["right"]);
    }

    if ( ( $cfg["contented"]["right"] == "" || priv_check($tname2path,$cfg["contented"]["right"],$specialvars["dyndb"]) || $erlaubnis == 1) && $tname2path != "" ) {

        // page basics
        // ***

        $environment["parameter"][6] != "" ? $version = " AND version=".$environment["parameter"][6] : $version = "";

        if ( count($_POST) == 0 ) {

            #$sql = "SELECT *
            #          FROM ".$cfg["contented"]["db"]["leer"]["entries"]."
            #         WHERE ".$cfg["contented"]["db"]["leer"]["key"]."='".$environment["parameter"][1]."'";

            if ( $specialvars["content_release"] == -1 && $version == "" ) {
                $content_release = "AND status>0";
            } else {
                $content_release = "";
            }

            $sql = "SELECT *
                      FROM ". SITETEXT ."
                     WHERE lang = '".$environment["language"]."'
                       AND label ='".$environment["parameter"][3]."'
                       AND tname ='".$environment["parameter"][2]."'
                       ".$content_release.
                       $version."
                     ORDER BY version DESC
                     LIMIT 0,1";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);

            #$data = $db -> fetch_array($result, $nop);
            $form_values = $db -> fetch_array($result,1);

        } else {
            $form_values = $_POST;
        }

        // form options holen
        #$form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        #$element = form_elements( $cfg["contented"]["db"]["leer"]["entries"], $form_values );

        // form elemente erweitern
        #$element["extension1"] = "<input name=\"extension1\" type=\"text\" maxlength=\"5\" size=\"5\">";
        #$element["extension2"] = "<input name=\"extension2\" type=\"text\" maxlength=\"5\" size=\"5\">";

        if ( $cfg["contented"]["revision_control"] == true ) {
            $hidedata["revision_control"] = array();
        }

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

        // funktion_content.inc.php zeile 181,182 reicht nicht (mehr)
        // eine funktion die nicht aufgerufen wird f�llt auch die variablen nicht
        if ( $defaults["section"]["label"] == "" ) $defaults["section"]["label"] = "inhalt";
        if ( $defaults["section"]["tag"] == "" ) $defaults["section"]["tag"] = "[H";

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene: ".$_SESSION["ebene"].$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "kategorie: ".$_SESSION["kategorie"].$debugging["char"];



        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "last edit: ".$_SESSION["cms_last_edit"].$debugging["char"];;
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "last ebene: ".$_SESSION["cms_last_ebene"].$debugging["char"];;
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "last kategorie: ".$_SESSION["cms_last_kategorie"].$debugging["char"];;

        if ( isset($_SESSION["cms_last_edit"]) && $_GET["referer"] != "" ) {
            unset($_SESSION["cms_last_edit"]);

            $_SESSION["ebene"] = $_SESSION["cms_last_ebene"];
            $_SESSION["kategorie"] = $_SESSION["cms_last_kategorie"];

            unset($_SESSION["cms_last_ebene"]);
            unset($_SESSION["cms_last_kategorie"]);
        }

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "neue ebene    : ".$_SESSION["ebene"].$debugging["char"];;
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "neue kategorie: ".$_SESSION["kategorie"].$debugging["char"];;



        // status anzeigen
        $ausgaben["ce_tem_db"]      = "#(db): ".$environment["parameter"][1];
        $ausgaben["ce_tem_name"]    = "#(template): ".$environment["parameter"][2];
        $ausgaben["ce_tem_label"]   = "#(label): ".$environment["parameter"][3];
        $ausgaben["version"]        = "#(version): ".$form_values["version"];

        # $environment["parameter"][4] -> abschnitt bearbeiten -> war: datensatz in db gefunden

        $ausgaben["ce_tem_lang"]    = "#(language): ".$environment["language"];
        $ausgaben["ce_tem_convert"] = "#(convert): ".$environment["parameter"][5];


        // lock erzeugen, anzeigen
        $sql = "SELECT byalias, lockat
                    FROM site_lock
                   WHERE lang = '".$environment["language"]."'
                     AND label ='".$environment["parameter"][3]."'
                     AND tname ='".$environment["parameter"][2]."'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        if ( $data = $db -> fetch_array($result, $nop) ) {
            $ausgaben["lock"] .= "lock by ".$data["byalias"]." @ ".$data["lockat"];
            $ausgaben["class"] = "ta_lock";
        } else {
            $sql = "INSERT INTO site_lock
                    (tname, lang, label, byalias, lockat)
            VALUES ('".$environment["parameter"][2]."',
                    '".$environment["language"]."',
                    '".$environment["parameter"][3]."',
                    '".$_SESSION["alias"]."',
                    '".date("Y-m-d H:i:s")."')";
            $result  = $db -> query($sql);
            $ausgaben["lock"] .= "lock by ".$_SESSION["alias"]." @ ".date("Y-m-d H:i:s");
            $ausgaben["class"] = "ta_norm";
        }


        // eWeBuKi tag schutz - sections 1
        if ( strpos( $form_values["content"], "[/E]") !== false ) {
            $preg = "|\[E\](.*)\[/E\]|Us";
            preg_match_all($preg, $form_values["content"], $match, PREG_PATTERN_ORDER );
            $mark = $defaults["section"]["tag"];
            $hide = "++";
            foreach ( $match[0] as $key => $value ) {
                $escape = str_replace( $mark, $hide, $match[1][$key]);
                $form_values["content"] = str_replace( $value, "[E]".$escape."[/E]", $form_values["content"]);
            }
        }


        // evtl. spezielle section
        if ( is_array($defaults["section"]["tag"]) ) {
            $preg_search = str_replace(
                            array("[", "]", "/"),
                            array("\[","\]","\/"),
                            implode("|",$defaults["section"]["tag"])
            );
            $allcontent = preg_split("/(".$preg_search.")/",$form_values["content"],-1,PREG_SPLIT_DELIM_CAPTURE);
            $i = 0;
            foreach ( $allcontent as $key=>$value ) {
                if ( in_array($value,$defaults["section"]["tag"]) ) {
                    $join[$i] = "{".$i."}".$value;
                } else {
                    $join[$i] .= $value;
                    $i++;
                }
            }

            if ( $environment["parameter"][4] != "" ) {
                $form_values["content"] = preg_replace("/\{[0-9]+\}/U","",$join[$environment["parameter"][4]]);
            }
        } else {
            $alldata = explode($defaults["section"]["tag"], $form_values["content"]);
            if ( $environment["parameter"][4] != "" ) {
                $form_values["content"] = $defaults["section"]["tag"].$alldata[$environment["parameter"][4]];
            }
        }


        // eWeBuKi tag schutz - sections 2
        $form_values["content"] = str_replace( $hide, $mark, $form_values["content"]);



        /*
        / wenn preview gedrueckt wird, hidedata erzeugen und $form_values["content"] aendern
        /
        / so funktioniert das ganze nicht
        / (es wird nie gespeichert -> "edit" anstatt "save" in der aktion url)
        / der extra parameter in der aktion url und
        / die if abfrage die den save verhindert
        / hat mir nicht gefallen!
        */
        if ( $HTTP_POST_VARS["PREVIEW"]  ){
            $hidedata["preview"]["content"] = "#(preview)";
            $preview = intelilink($HTTP_POST_VARS["content"]);
            $preview = tagreplace($preview);
            $hidedata["preview"]["content"] .= nlreplace($preview);
            $form_values["content"] = $HTTP_POST_VARS["content"];
        }



        // convert tag 2 html
        switch ( $environment["parameter"][5] ) {
            case "html":
                // content nach html wandeln
                $form_values["content"] = tagreplace($form_values["content"]);
                // intelligenten link tag bearbeiten
                $form_values["content"] = intelilink($form_values["content"]);
                // newlines nach br wandeln
                $form_values["content"] = nlreplace($form_values["content"]);
                // html db value aendern
                $form_values["html"] = -1;
                break;
            case "tag":
                // content nach cmstag wandeln
                ###
                // html db value aendern
                $form_values["html"] = 0;
                break;
            default:
                $form_values["html"] = 0;
        }


        // eWeBuKi tag schutz part 3
        $mark_o = array( "#(", "g(", "#{", "!#" );
        $hide_o = array( "::1::", "::2::", "::3::", "::4::" );
        $form_values["content"] = str_replace( $mark_o, $hide_o, $form_values["content"]);


        // wie wird content verarbeitet
        if ( $form_values["html"] == "-1" ) {
            $ausgaben["ce_name"] = "content";
            $ausgaben["ce_inhalt"] = $form_values["content"];

            // epoz fix
            if ( $specialvars["wysiwyg"] == "epoz" ) {
                $sea = array("\\","\n","\r","'");
                $rep = array("\\\\","\\n","\\r","\\'");
                $ausgaben["ce_inhalt"] = str_replace( $sea, $rep, $ausgaben["ce_inhalt"]);
            }

            // template version
            $art = "-".$specialvars["wysiwyg"];
        } else {
            // ce editor bauen

            $ausgaben["name"] = "content";
            if ( $cfg["contented"]["letters"] != "" ) {
                $ausgaben["charakters"] = "#(charakters)";
                $ausgaben["eventh2"] = "onKeyDown=\"count('content',".$cfg["contented"]["letters"].");\" onChange=\"chk('content',".$cfg["contented"]["letters"].");\"";
            } else {
                $ausgaben["charakters"] = "";
            }
            $ausgaben["inhalt"] = $form_values["content"];


            $ausgaben["tn"] = makece("ceform", "content", $form_values["content"]);


            // vogelwilde regexen die alte & neue links zu ewebuki-files findet
            // und viel arbeit erspart
            preg_match_all("/".str_replace("/","\/",$cfg["file"]["base"]["webdir"])."[a-z]+\/([0-9]+)\//",$form_values["content"],$found1);
            preg_match_all("/".str_replace("/","\/",$cfg["file"]["base"]["webdir"])."[a-z]+\/[a-z]+\/[a-z]+_([0-9]+)\./",$form_values["content"],$found2);
            $found = array_merge($found1[1],$found2[1]);
            $debugging["ausgabe"] .= "<pre>".print_r($found,True)."</pre>";

            // file memo auslesen und zuruecksetzen
            if ( is_array($_SESSION["file_memo"]) ) {
                $array = array_merge($_SESSION["file_memo"],$found);
//                 unset($_SESSION["file_memo"]);
            } else {
                $array = $found;
            }

            // wenn es thumbnails gibt, anzeigen
            if ( count($array) >= 1 ) {

                $merken = $db -> getDb();
                if ( $merken != DATABASE ) {
                    $db -> selectDB( DATABASE ,"");
                }

                $where = "";
                foreach ( $array as $value ) {
                    if ( $where != "" ) $where .= " OR ";
                    $where .= "fid = '".$value."'";
                }
                $sql = "SELECT *
                          FROM site_file
                         WHERE ".$where."
                      ORDER BY ffname, funder";
                $result = $db -> query($sql);


                if ( $merken != DATABASE ) {
                    $db -> selectDB($merken,"");
                }

                filelist($result, "contented");
            }

            if ( is_array($_SESSION["compilation_memo"]) ) {
                foreach ( $_SESSION["compilation_memo"] as $compid=>$value ) {
                    $pics = implode(":",$value);
                    $dataloop["selection"][] = array(
                            "id" => $compid,
                          "pics" => $pics,
                       "onclick" => "ebInsertSelNG(ebCanvas, '".$compid."', '".$cfg["contented"]["sel_tag"][0]."', '".$cfg["contented"]["sel_tag"][1]."', '".$pics."', '".$cfg["contented"]["sel_tag"][2]."', '".$cfg["contented"]["sel_tag"][3]."');",
                    );
                }
                if ( count($dataloop["selection"]) > 0 ) $hidedata["selection"] = array();
            }


            // template version
            $art = "";
        }



        // referer im form mit hidden element mitschleppen
        if ( $HTTP_GET_VARS["referer"] != "" ) {
            $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
            $ausgaben["form_break"] = $HTTP_GET_VARS["referer"];
        } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
        }



        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        #$ausgaben["form_aktion"] = $cfg["contented"]["basis"]."/edit,".$environment["parameter"][1].",verify.html";
        #$ausgaben["form_break"] = $cfg["contented"]["basis"]."/list.html";

        #$ausgaben["form_aktion"] = $cfg["contented"]["basis"]."edit/save,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",".$environment["parameter"][4].".html";
        $ausgaben["form_aktion"] = $cfg["contented"]["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",".$environment["parameter"][4].",,,verify.html";
        #$ausgaben["form_abbrechen"] = $_SESSION["page"];
        $ausgaben["form_break"] = $cfg["contented"]["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",".$environment["parameter"][4].",,,unlock.html";


        // hidden values
        #$ausgaben["form_hidden"] .= "";
        $ausgaben["form_hidden_html"] .= $form_values["html"];
        $ausgaben["form_hidden_version"] .= $form_values["version"];
        $ausgaben["form_hidden_status"] .= $form_values["status"];

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".modify".$art;
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
            $ausgaben["inaccessible"] .= "# (upload) #(upload)<br />";
            $ausgaben["inaccessible"] .= "# (file) #(file)<br />";
            $ausgaben["inaccessible"] .= "# (files) #(files)<br />";
            $ausgaben["inaccessible"] .= "g (overwrite) g(overwrite)<br />";
            $ausgaben["inaccessible"] .= "g (version) g(version)<br />";
            $ausgaben["inaccessible"] .= "g (reset) g(reset)<br />";
            $ausgaben["inaccessible"] .= "g (abort) g(abort)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a


        // +++
        // page basics

        // lock aufheben
        if ( $environment["parameter"][7] != "" ) {
            $sql = "DELETE FROM site_lock
                          WHERE label ='".$environment["parameter"][3]."'
                            AND tname ='".$environment["parameter"][2]."'
                            AND lang = '".$environment["language"]."'";
            $result  = $db -> query($sql);
            $header = $_SESSION["page"];
        }

        if ( $environment["parameter"][7] == "verify"
            &&  ( $HTTP_POST_VARS["send"] != ""
                || $HTTP_POST_VARS["add"] != ""
                || $HTTP_POST_VARS["sel"] != ""
                || $HTTP_POST_VARS["upload"] != "" ) ) {


            // form eingaben pr�fen
            form_errors( $form_options, $HTTP_POST_VARS );


            // gibt es bereits content?
            $sql = "SELECT version, html, content
                      FROM ". SITETEXT ."
                     WHERE tname='".$environment["parameter"][2]."'
                       AND lang='".$environment["language"]."'
                       AND label='".$environment["parameter"][3]."'
                  ORDER BY version DESC
                     LIMIT 0,1";
            $result = $db -> query($sql);
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $data = $db -> fetch_array($result, $nop);
            $content_exist = $db -> num_rows($result);

            // evtl. spezielle section
            if ( $environment["parameter"][4] != "" ) {

                // eWeBuKi tag schutz - sections 1
                if ( strpos( $data["content"], "[/E]") !== false ) {
                    $preg = "|\[E\](.*)\[/E\]|Us";
                    preg_match_all($preg, $data["content"], $match, PREG_PATTERN_ORDER );
                    $mark = $defaults["section"]["tag"];
                    $hide = "++";
                    foreach ( $match[0] as $key => $value ) {
                        $escape = str_replace( $mark, $hide, $match[1][$key]);
                        $data["content"] = str_replace( $value, "[E]".$escape."[/E]", $data["content"]);
                    }
                }

                if ( is_array($defaults["section"]["tag"]) ) {

                    $preg_search = str_replace(
                                    array("[", "]", "/"),
                                    array("\[","\]","\/"),
                                    implode("|",$defaults["section"]["tag"])
                    );
                    $allcontent = preg_split("/(".$preg_search.")/",$data["content"],-1,PREG_SPLIT_DELIM_CAPTURE);
                    $i = 0;
                    foreach ( $allcontent as $key=>$value ) {
                        if ( in_array($value,$defaults["section"]["tag"]) ) {
                            $join[$i] = "{".$i."}".$value;
                        } else {
                            $join[$i] .= $value;
                            $i++;
                        }
                    }

                    $content = "";
                    foreach ( $join as $key=>$value ) {
                        if ( $key == $environment["parameter"][4] ) {
                            $content .= $_POST["content"];
                        } elseif ( $key > 0 ) {
                            $content .= preg_replace("/\{[0-9]+\}/U","",$value);
                        } else {
                            $content .= $value;
                        }
                    }
                    // eWeBuKi tag schutz - sections 2
                    $content = str_replace( $hide, $mark, $content );

                } else {
                    $allcontent = explode($defaults["section"]["tag"], addslashes($data["content"]) );
                    $content = "";
                    foreach ($allcontent as $key => $value) {
                        if ( $key == $environment["parameter"][4] ) {
                            $length = strlen( $defaults["section"]["tag"] );
                            if ( substr($HTTP_POST_VARS["content"],0,$length) == $defaults["section"]["tag"] ) {
                                $content .= $defaults["section"]["tag"].substr($HTTP_POST_VARS["content"],$length);
                            } else {
                                $content .= $HTTP_POST_VARS["content"];
                            }
                        } elseif ( $key > 0 ) {
                            $content .= $defaults["section"]["tag"].$value;
                        } else {
                            $content .= $value;
                        }

                    // eWeBuKi tag schutz - sections 2
                    $content = str_replace( $hide, $mark, $content );

                    }
                }
            } else {
                $content = $HTTP_POST_VARS["content"];
            }


            // html killer :)
            if ( $specialvars["denyhtml"] == -1 ) {
                $content = strip_tags($content);
            }


            // space killer
            if ( $specialvars["denyspace"] == -1 ) {
                $pattern = "  +";
                while ( preg_match("/".$pattern."/", $content, $tag) ) {
                    $content = str_replace($tag[0]," ",$content);
                }
            }




            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                ### put your code here ###

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                // ticks sicher maskieren
                $content = addslashes(stripslashes($content));

                $mark = "";$marka = "";$markb = "";
                if ( $specialvars["content_release"] == -1 ) {
                    $mark = ", status=1";
                    $marka = ", status";
                    $markb = ", 1";
                    // alle freigegeben versionen erstmal historisieren
                    $sql = "UPDATE ". SITETEXT ." SET
                                    status=0
                                WHERE lang = '".$environment["language"]."'
                                AND label ='".$environment["parameter"][3]."'
                                AND tname ='".$environment["parameter"][2]."'
                                AND status>=0";
                    $result  = $db -> query($sql);
                }

                if ( $content_exist == 1 && isset($_POST["send"]["save"]) ) {
                    if ( $environment["parameter"][4] == "" && $_POST["content"] == "" ) {
                        $sql = "DELETE FROM ". SITETEXT ."
                                      WHERE lang = '".$environment["language"]."'
                                        AND label ='".$environment["parameter"][3]."'
                                        AND tname ='".$environment["parameter"][2]."'";
                    } else {
                        $sql = "UPDATE ". SITETEXT ." SET
                                       ebene = '".$_SESSION["ebene"]."',
                                       kategorie = '".$_SESSION["kategorie"]."',
                                       crc32 = '".$specialvars["crc32"]."',
                                       html = '".$_POST["html"]."',
                                       content = '".$content."',
                                       changed = '".date("Y-m-d H:i:s")."',
                                       bysurname = '".$_SESSION["surname"]."',
                                       byforename = '".$_SESSION["forename"]."',
                                       byemail = '".$_SESSION["email"]."',
                                       byalias = '".$_SESSION["alias"]."'
                                     ".$mark."
                                 WHERE lang = '".$environment["language"]."'
                                   AND label ='".$environment["parameter"][3]."'
                                   AND tname ='".$environment["parameter"][2]."'
                                   AND version=".$_POST["version"];
                    }
                } else {
                    $sql = "INSERT INTO ". SITETEXT ."
                                        (lang, label, tname, version,
                                        ebene, kategorie,
                                        crc32, html, content,
                                        changed, bysurname, byforename, byemail, byalias".$marka.")
                                 VALUES (
                                         '".$environment["language"]."',
                                         '".$environment["parameter"][3]."',
                                         '".$environment["parameter"][2]."',
                                         '".++$data["version"]."',
                                         '".$_SESSION["ebene"]."',
                                         '".$_SESSION["kategorie"]."',
                                         '".$specialvars["crc32"]."',
                                         '".$HTTP_POST_VARS["html"]."',
                                         '".$content."',
                                         '".date("Y-m-d H:i:s")."',
                                         '".$_SESSION["surname"]."',
                                         '".$_SESSION["forename"]."',
                                         '".$_SESSION["email"]."',
                                         '".$_SESSION["alias"]."'".
                                            $markb.")";
                }

                // Sql um spezielle Felder erweitern
                #$ldate = $HTTP_POST_VARS["ldate"];
                #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                #$sqla .= ", ldate='".$ldate."'";

//                 $sql = "update ".$cfg["contented"]["db"]["leer"]["entries"]." SET ".$sqla." WHERE ".$cfg["contented"]["db"]["leer"]["key"]."='".$environment["parameter"][1]."'";

                // notwendig fuer die artikelverwaltung , der bisher aktive artikel wird auf inaktiv gesetzt
                if ( preg_match("/^\[!\]/",$content,$regs) ) {
                    $sql_regex = "SELECT * FROM ". SITETEXT ." WHERE content REGEXP '^\\\[!\\\]1' AND tname like '".$environment["parameter"][2]."'";
                    $result_regex  = $db -> query($sql_regex);
                    $data_regex = $db -> fetch_array($result_regex,1);
                    $new_content = preg_replace("/\[!\]1/","[!]0",$data_regex["content"]);
                    $sql_regex = "UPDATE ". SITETEXT ." SET content ='".$new_content."' WHERE content REGEXP '^\\\[!\\\]1' AND tname like '".$environment["parameter"][2]."'";
                    $result_regex  = $db -> query($sql_regex);
                }

                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                #if ( $header == "" ) $header = $cfg["contented"]["basis"]."/list.html";
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                if ( $HTTP_POST_VARS["add"] || $HTTP_POST_VARS["sel"] || $HTTP_POST_VARS["upload"] > 0 ) {

                    $_SESSION["cms_last_edit"] = str_replace(",verify", "", $pathvars["requested"]);

                    $_SESSION["cms_last_referer"] = $ausgaben["form_referer"];
                    $_SESSION["cms_last_ebene"] = $_SESSION["ebene"];
                    $_SESSION["cms_last_kategorie"] = $_SESSION["kategorie"];

                    if ( $HTTP_POST_VARS["upload"] > 0 ) {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/upload.html?anzahl=".$HTTP_POST_VARS["upload"]);
                    } elseif ( $_POST["sel"] != "" ) {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/compilation.html");
                    } else {
                        header("Location: ".$pathvars["virtual"]."/admin/fileed/list.html");
                    }

                } else {
                    $pattern = ",v[0-9]*\.html$";
                    $ausgaben["form_referer"] = preg_replace("/".$pattern."/",".html",$ausgaben["form_referer"] );
                    header("Location: ".$ausgaben["form_referer"]."");
                }
                #header("Location: ".$header);
            }
        }
        // abbrechen button verarbeiten (siehe 465 $header variable)
        if ( $environment["parameter"][7] == "unlock" ) {
            header("Location: ".$header);
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }



    $db -> selectDb(DATABASE,FALSE);



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
