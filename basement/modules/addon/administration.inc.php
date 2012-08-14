<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id: leer.inc.php 1131 2007-12-12 08:45:50Z chaot $";
  $Script["desc"] = "short description";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2010 Werner Ammon ( wa<at>chaos.de )

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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    if ( $cfg["admin"]["right"] == "" || $rechte[$cfg["admin"]["right"]] == -1 ) {
        include $pathvars["moduleroot"]."wizard/wizard.cfg.php";
        unset($cfg["wizard"]["function"]);
        include $pathvars["moduleroot"]."wizard/wizard-functions.inc.php";
        
        // bereiche werden nach aenderungsdatum sortiert        
        if ( !function_exists("sort_marked_content") ) {            
            function sort_marked_content($a,$b) {
                if ( $a["changed_db"] < $b["changed_db"] ) {
                    return 1;
                } elseif ( $a["changed_db"] == $b["changed_db"] ) {
                    return 0;
                } else {
                    return -1;
                }
            }
        }
        
        
        if ( $_GET["main"] == "ajax_suche" ) {

            header("HTTP/1.1 200 OK");
            $released_content = find_marked_content( $_GET["art"], $cfg, "inhalt", array(1), array("max_age"=>1700), FALSE ,array('presse','ausstellungen','termine'));


            
            $counter = 0;
            $hit = $released_content[1];

            if ( is_array($hit) ) {
                uasort($hit,'sort_marked_content');

                foreach ( $hit as $key => $value ) {

//                    if ( $value["kategorie"] == "---")continue;
//                    if ( $_GET["kate"] == "lokal" ) {
//                        if ( !strstr($value["kategorie"],"aemter") ) continue;
//                    } else {
//                        if ( $value["kategorie"] != $_GET["kate"] ) continue;
//                    }

//                    if ( !priv_check($value["kategorie"],"admin;publish;edit") ) continue;

                    if ( $_GET["term"] ){
                        if ( stristr($value["titel"],$_GET["term"]) || stristr($value["ext"],$_GET["term"]) ) {

                        } else {
                            continue;
                        }
                    }
                    if ( $value["titel"] == "---" ) $value["titel"] = $value["ext"];

                    $counter++;
                    $value["titel"] = str_replace("\"","", $value["titel"]);
                    $value["titel"] = str_replace("'","", $value["titel"]);                    
                    $value["titel"] = str_replace("\r\n","", $value["titel"]);
                    $value["titel"] = str_replace("\n","", $value["titel"]);
                    $value["titel"] = str_replace("\r","", $value["titel"]);
                    $value["titel"] = str_replace("\n\r","", $value["titel"]);
                    
                    $buffer[] = '{
                            "id": "'.$counter.'",
                            "label": "'.$value["titel"].'",
                             "edit":"'.$value["edit"].'",
                              "view":"'.$value["view"].'",
                            "last_author": "'.$value["last_author"].'",
                            "changed": "'.$value["changed"].'"
                         }';
                }
            }
            if ( $counter == 0 ) {
                echo "[{\"label\": \"Keine Treffer\"}]";
            } else {
                echo "[ ".implode(" , ",$buffer)." ]";
            }
            die();
        }
              
        
                        
        // Wer bin ich - Benutzer und Gruppen holen
        $ausgaben["user"] = $_SESSION["username"];
        if ( $_SESSION["username"] != "" ) {
            $sql = "SELECT *
                      FROM auth_member
                      JOIN auth_group
                        ON (auth_member.gid=auth_group.gid)
                     WHERE uid=".$_SESSION["uid"];
            $result = $db -> query($sql);
            while ( $data = $db -> fetch_array($result,1) ) {
                $dataloop["groups"][]["groups"] = $data["beschreibung"];
                $dataloop["group_id"][] = $data["gid"];
            }
        }
        
        if ( $ausgaben["user"] != "" ) {
            
            // lokale redakteure erkennen
            $halt = "";
            if ( is_array($dataloop["group_id"]) ) {
                foreach ( $dataloop["group_id"] as $gruppe ) {
                    $sql = "SELECT *
                              FROM auth_content
                             WHERE gid='".$gruppe."'
                               AND ( pid='3' OR pid='2')"; // 2: edit; 3: publish
                    $result = $db -> query($sql);
                    // dataloop mit zugewiesenen aemtern fuellen
                    while ( $data = $db -> fetch_array($result,1) ) {
                        if ( substr($data["tname"],0,8) == "/aemter/" ) {
                            $sql_amt = "SELECT *
                                          FROM db_aemter
                                         WHERE adakz='".substr($data["tname"],8,2)."'";
                            $result_amt = $db -> query($sql_amt);
                            $data_amt = $db -> fetch_array($result_amt,1);
                            $halt = -1;
                            $dataloop["artikel_aemter"][substr($data["tname"],8,2)]["url"] = substr($data["tname"],0,10);
                            $dataloop["artikel_aemter"][substr($data["tname"],8,2)]["name"] = $data_amt["kat_kurz"]." ".$data_amt["adststelle"];
                        }
                    }
                }
            }

            if ( priv_check("/aemter","eddit") ) {
                $sql_amt = "SELECT * FROM db_aemter WHERE adkate in ('3','4')";
                $result_amt = $db -> query($sql_amt);
                while ( $data = $db -> fetch_array($result_amt,1) ) {
                    $dataloop["artikel_aemter"][$data["adakz"]]["url"] = "/aemter/".$data["adakz"];
                    $dataloop["artikel_aemter"][$data["adakz"]]["name"] = $data["kat_kurz"]." ".$data["adststelle"];
                }
            }

            if ( $halt == -1 || priv_check("/aemter","edit") ) {
                $hidedata["lokal_presse_section"][0] = "on";
                $hidedata["lokal_artikel_section"][0] = "on";
                $hidedata["lokal_termine_section"][0] = "on";
            }
            // lokale redakteure erkennen

            // marginalspalten-bearbeitung
            if ( priv_check("/global","publish") ) {
                $hidedata["marginal"] = array();
                $hidedata["marginal"]["url"] = "/auth/wizard/show,devel0,global,marginal,,,.html";
                $hidedata["marginal"]["url"] = $pathvars["virtual"]."/wizard/show,".$db->getDb().",global,marginal.html";
                $hidedata["marginal"]["url_va"] = $pathvars["virtual"]."/wizard/show,".$db->getDb().",global,marginal_va.html";
            }

            function get_chefred($url) {
                global $db,$member_edit,$member_publish;

                // chefredakteure holen
                $infos = "";
                $priv_info = priv_info($url,$infos);
                $array_priv_edit = array();
                $member_edit = array();
                $member_publish = array();
                foreach ( $priv_info as $priv_url=>$value ) {
                    if ( is_array($value["add"]) ) {
                        foreach ( $value["add"] as $group=>$rights ) {
                            $sql = "SELECT *
                                    FROM auth_group
                                    JOIN auth_member
                                        ON (auth_group.gid=auth_member.gid)
                                    JOIN auth_user
                                        ON (auth_member.uid=auth_user.uid)
                                    WHERE ggroup='".$group."'";
                            $result = $db -> query($sql);
                            $member = array();
                            while ( $data = $db -> fetch_array($result,1) ) {
                                $member[$data["username"]] = "<a href=\"mailto:".$data["email"]."\">".$data["vorname"]." ".$data["nachname"]."</a>";
                                if ( strstr($rights,"edit") && !strstr($value["del"][$group],"edit") ) {
                                    $member_edit[$data["username"]] = "<a href=\"mailto:".$data["email"]."\">".$data["vorname"]." ".$data["nachname"]."</a>";
                                }
                                if ( strstr($rights,"publish") && !strstr($value["del"][$group],"publish") ) {
                                    $member_publish[$data["username"]] = "<a href=\"mailto:".$data["email"]."\">".$data["vorname"]." ".$data["nachname"]."</a>";
                                }
                            }
                        }
                    }
                }
            }

            // einzelne bereiche durchgehen (artikel, termine, ...)
            foreach ( $cfg["admin"]["specials"] as $url => $bereich ) {

                // chefredakteure holen
                get_chefred($url);
                
                unset($dataloop["edit_section"]);            
                unset($hidedata["edit_section"]);
                unset($dataloop["release_wait_section"]);
                unset($hidedata["release_wait_section"]);
                unset($dataloop["release_queue"]);
                unset($hidedata["release_queue"]);
                
                // dataloop holen
                $buffer = find_marked_content( $url, $cfg, "inhalt", array(-2,-1), array(), FALSE );

//echo "<pre>";
//print_r($buffer);
//echo "</pre>";
                // 
                // buffer durchgehen und nach bereiche aufteilen
                if ( is_array($buffer) ) {                    
                    foreach ( $buffer as $key => $value ) {
                        foreach ( $value as $own_key => $own_value ) {
                            if ( crc32($url) == substr($own_value["tname"],0,strpos($own_value["tname"],".")) ) {
                                ${$bereich}[$key][] = $buffer[$key][$own_key];
                                unset($buffer[$key][$own_key]);
                            }
                        } 
                    }
                }
            #echo count(${$bereich}[-1])." -".priv_check($url, $cfg["wizard"]["right"]["edit"]);
 
                if ( count(${$bereich}[-1]) > 0 && priv_check($url, $cfg["wizard"]["right"]["edit"]) ) {
                    $hidedata["edit_section"]["name"] = $bereich;
                    $dataloop["edit_section"] = ${$bereich}[-1];
                    
                    uasort($dataloop["edit_section"],'sort_marked_content');
                }

                if ( count(${$bereich}[-2]) > 0 && priv_check($url, $cfg["wizard"]["right"]["publish"]) ) {                            
                    $hidedata["release_queue"]["name"] = $bereich;
                    $dataloop["release_queue"] = ${$bereich}[-2];                    
                }
                                
                if ( ( !is_array($hidedata["release_queue"]) && count(${$bereich}[-2]) ) && priv_check($url, $cfg["wizard"]["right"]["edit"]) ) {
                    $hidedata["release_wait_section"]["name"] = $bereich;
                    $dataloop["release_wait_section"] = ${$bereich}[-2]; 
                }

 
                    $ausgaben["bereiche"] .= parser("administration-specials",'');
                    $ausgaben["bereiche"] .= parser("administration-search",'');

            }                                                      
            // normalen content ausschliesslich spezielle bereiche durchgehen
            // * * *
            $bereich = "content";
            $buffer = find_marked_content( "/", $cfg, "inhalt", array(-2,-1), array(), FALSE, array("/blog","/2_blog"));
                
            $dataloop[$bereich."_edit"] = $buffer[-1];
            $dataloop[$bereich."_release_queue"] = $buffer[-2];
            $dataloop[$bereich."_release_wait"] = $buffer[-2];
            $toggle_fields = array(
                          "edit" => array("all","edit;publish"),
                 "release_queue" => array("all","publish"),
                  "release_wait" => array("own","edit"),
                "release_recent" => array("own","edit;publish"),
            );
            foreach ( $toggle_fields as $tog_key=>$tog_value ) {
                if ( is_array ( $dataloop[$bereich."_".$tog_key] )  ) {
                    foreach ( $dataloop[$bereich."_".$tog_key] as $key => $value ) {
                        get_chefred($value["path"]);
                        if ( $tog_value[0] == "own" &&
                                $value["author"] != $_SESSION["forename"]." ".$_SESSION["surname"] ) {
                            unset($dataloop[$bereich."_".$tog_key][$key]);
                            continue;
                        }
                        if ( priv_check($value["path"],$tog_value[1]) ) {
                            // tabellen farben wechseln
                            if ( $color[$bereich."_".$tog_key] == $cfg["wizard"]["color"]["a"]) {
                                $color[$bereich."_".$tog_key] = $cfg["wizard"]["color"]["b"];
                            } else {
                                $color[$bereich."_".$tog_key] = $cfg["wizard"]["color"]["a"];
                            }
                            $dataloop[$bereich."_".$tog_key][$key]["color"] = $color[$bereich."_".$tog_key];
                            $dataloop[$bereich."_".$tog_key][$key]["red"] = implode(", ",$member_edit);
                            $dataloop[$bereich."_".$tog_key][$key]["chefred"] = implode(", ",$member_publish);
                        } else {
                            unset($dataloop[$bereich."_".$tog_key][$key]);
                        }
                    }
                    if ( count($dataloop[$bereich."_".$tog_key]) > 0 ) {
                        $hidedata[$bereich."_".$tog_key][0] = array();
                    }
                }
            }

            $ausgaben["user"] = $_SESSION["username"];
            // ggf. toggles ausklappen
            if ( is_array($_SESSION["admin_toggle"]) ) {
                foreach ( $_SESSION["admin_toggle"] as $toggle ) {
        //             $ausgaben["toggle_".$toggle] = "block";
                    $dataloop["toggles"][]["element"] = $toggle;
                }
            }
            // +++
            // funktions bereich

            // TEST MENUED
            if ( $_SESSION["uid"] ) $hidedata["menu_edit"]["on"] = "on";
            $ausgaben["ajax_menu"] = "login.html";
            if ( $_POST["ajax_menu"] != "" ) {
                 $design = "modern";
                $stop["nop"] = "nop";
                $positionArray = "";
                include $pathvars["moduleroot"]."admin/menued2.cfg.php";
                $cfg["menued"]["function"]["login"] = array("locate","make_ebene");
                include $pathvars["moduleroot"]."admin/menued2-functions.inc.php";
                include $pathvars["moduleroot"]."libraries/function_menutree.inc.php";
            
                // welche buttons sollen angezeigt werden
                $mod = array(
                            "edit"=> array("", "Seite editieren", "edit"),
                            "add"=> array("", "Seite hinzufuegen", "add"),
                        "jump"=> array("", "zur Seite", "edit;publish")
                            );
               
                $_SESSION["menued_id"] = $_POST["point_id"];
                locate($_POST["point_id"]);

                $wizard_menu = sitemap($_POST["point_id"], "admin", "wizard",$mod,"");
                                             
                $lines = explode("<li>",$wizard_menu);                
                array_shift($lines);
              
                $preg = '/(href="\/auth\/login,)([0-9]*)\.html"/i';

                $color = $cfg["wizard"]["color"]["a"];
               echo "<ul style=\"list-style: none\">";

                // zurueck - link bauen              
                if ( is_numeric($positionArray[0]) ) {
                   if ( is_numeric($positionArray[1]) ) {
                       $back_id = $positionArray[1];
                   } else {
                       $back_id = 0;
                   }                             
                   echo "<li><a onclick=\"aj_menu(".$back_id.")\">zurÃ¼ck</a></li>";
                }
                // zurueck - link bauen   
                
                foreach ( $lines as $line ) {
                    
                    ( $color == $cfg["wizard"]["color"]["a"] ) ? $color = $cfg["wizard"]["color"]["b"] : $color = $cfg["wizard"]["color"]["a"];
                    preg_match($preg,$line,$regs);

                    if ( $regs[2] ) {
                        $line = str_replace("href=\"/auth/login,".$regs[2].".html\"","onclick= aj_menu(".$regs[2].")",$line);
                       echo "<li style=\"background-color:".$color.";margin:0;padding:0.5em;clear:both;\">".$line."</li>";
                    } else {
                       echo "<li style=\"background-color:".$color.";margin:0;padding:0.5em;clear:both;\">".$line."</li>"; 
                    }
                }
               echo "</ul>";
               exit;
            }
            // TEST MENUED

            // page basics
            // ***

            // label bearbeitung aktivieren
            if ( isset($_GET["edit"]) ) {
                $specialvars["editlock"] = 0;
            } else {
                $specialvars["editlock"] = -1;
            }

            // wohin schicken
            $backlink = "";
            if ( $_SERVER["HTTP_REFERER"] != "" ) {
                if ( strstr($_SERVER["HTTP_REFERER"],"/login.html" )
                  || strstr($_SERVER["HTTP_REFERER"],"/wizard/")
                  || strstr($_SERVER["HTTP_REFERER"],"/admin/") ) {
                    if ( $_SESSION["admin_back_link"] != "" ) {
                        $backlink = $_SESSION["admin_back_link"];
                    } else {
                        $backlink = "/index.html";
                    }
                } else {
                    $backlink = $_SERVER["HTTP_REFERER"];
                }
            } else {
                if ( $_SESSION["admin_back_link"] != "" ) {
                    $backlink = $_SESSION["admin_back_link"];
                } else {
                    $backlink = "/index.html";
                }
            }
            $backlink = preg_replace(
                            array("/^(".str_replace("/","\/",$pathvars["webroot"]).")/","/^\/auth/"),
                            "",
                            $backlink
                        );
            if ( $_SESSION["uid"] != "" ) {
                $backlink = "/auth".$backlink;
            }
            session_start();
            $_SESSION["admin_back_link"] = $backlink;
            $ausgaben["back_link"] = $backlink;

            // +++
            // page basics
        }

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (login) #(login)<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        
        // was anzeigen
        $mapping["main"] = "administration";
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    // SPÄTER

    
    
    
    
//        if ( $_POST["ajax"] != "" ) {
//            echo "<pre>".print_r($_POST,true)."</pre>";
//            if ( $_POST["ajax"] == "blinddown" ) {
//                $_SESSION["admin_toggle"][$_POST['id']] = $_POST['id'];
//            } elseif ( $_POST["ajax"] == "blindup" ) {
//                if ( $_SESSION["admin_toggle"][$_POST['id']] != "" ) {
//                    unset($_SESSION["admin_toggle"][$_POST['id']]);
//                }
//            }
//            header("HTTP/1.0 200 OK");exit;
//            die();
//        }
    
    
    // RECHTE
//                   foreach ( $toggle_fields as $tog_key=>$tog_value ) {
//                    $ausgaben["toggle_".$bereich."_".$tog_key] = "none";
//                    $ausgaben["toggle_lokal_".$bereich."_".$tog_key] = "none";
//                    if ( is_array ( $dataloop[$bereich."_".$tog_key] )  ) {
//                        foreach ( $dataloop[$bereich."_".$tog_key] as $key => $value ) {
//                            $zugang = 0;
//                            $my = 0;
//                            if ( $value["kategorie"] != "---" && $value["kategorie"] != "" && priv_check($value["kategorie"],"admin;publish;edit") ) {
//                                if ( $value["author"] == $_SESSION["forename"]." ".$_SESSION["surname"] ) {
//                                    $my = -1;
//                                }
//
//                                if ( priv_check($value["kategorie"],$tog_value["all"] ) ){
//                                    $zugang = -1;                                    
//                                }
// 
//                                if ( priv_check($value["kategorie"],$tog_value["own"]) && $zugang != -1 && $tog_value["own"] != "" ) {
//                                    if ( $my == -1 ) {
//                                        $zugang = -1;
//                                    }                                     
//                                }
//          
//                                if ( $zugang != -1 ) {
//                                    continue;
//                                }
//
//                                // lokale Eintrage
//                                if ( $value["kategorie"] != "/aktuell/archiv"
//                                  && $value["kategorie"] != "/aktuell/presse"
//                                  && $value["kategorie"] != "/aktuell/ausstellungen"
//                                  && $value["kategorie"] != "/aktuell/termine" ) {
//                                    if ( $tog_key == "release_wait" ) {
//                                        get_chefred($value["kategorie"]);
//                                    }
//                                    $sql_amt = "SELECT *
//                                                  FROM db_aemter
//                                                 WHERE adakz='".substr($value["kategorie"],8,2)."'";
//                                    $result_amt = $db -> query($sql_amt);
//                                    $data_amt = $db -> fetch_array($result_amt,1);
//                                    $value["amt"] = $data_amt["kat_kurz"]." ".$data_amt["adststelle"];
//                                    $dataloop["lokal_".$bereich."_".$tog_key][$key] = $value;
//                                    $zw = str_replace("/index","/".$bereich,$value["kategorie"].",,");
//                                    ( $bereich == "termine" ) ? $regi = str_replace("/","\/",$url).",," : $regi = str_replace("/","\/",$url)."\/";
//                                    $dataloop["lokal_".$bereich."_".$tog_key][$key]["site"] = preg_replace("/".$regi."/",$zw,$value["view"]);
//                                    unset($dataloop[$bereich."_".$tog_key][$key]);
//
//                                    // tabellen farben wechseln
//                                    $dataloop["lokal_".$bereich."_".$tog_key][$key]["color"] = $cfg["admin"]["color"]["a"];
//                                    if ( $my == -1 ) $dataloop["lokal_".$bereich."_".$tog_key][$key]["color"] = $cfg["admin"]["color"]["b"];
//                                    $dataloop["lokal_".$bereich."_".$tog_key][$key]["red"] = implode(", ",$member_edit);
//                                    $dataloop["lokal_".$bereich."_".$tog_key][$key]["chefred"] = implode(", ",$member_publish);
//                                // globale Eintraege
//                                } else {
//                                    $dataloop[$bereich."_".$tog_key][$key]["color"] = "#CCCCCC";
//                                    if ( $my == -1 ) $dataloop[$bereich."_".$tog_key][$key]["color"] = "#FF9148";
//                                    $dataloop[$bereich."_".$tog_key][$key]["red"] = implode(", ",$member_edit);
//                                    $dataloop[$bereich."_".$tog_key][$key]["chefred"] = implode(", ",$member_publish);
//                                }                                                  
//                            } else {
//                                unset($dataloop[$bereich."_".$tog_key][$key]);
//                            }
//                        }
//                    }
//                }
    
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
