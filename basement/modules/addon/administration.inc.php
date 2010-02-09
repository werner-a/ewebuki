<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//   "$Id$";
//   "administration";
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

    // ip_check
    $ip_c = explode(".",$_SERVER["REMOTE_ADDR"]);
    if ( $ip_c[0] == $cfg["admin"]["ip_check"][0] && $ip_c[1] == $cfg["admin"]["ip_check"][1] && $ip_c[2] == $cfg["admin"]["ip_check"][2] ) {
        header("Location: ".$pathvars["virtual"]."/index.html");
    }

    if ( $cfg["admin"]["right"] == "" || priv_check("/".$cfg["admin"]["subdir"],$cfg["admin"]["right"]) ) {

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

        // page basics
        // ***

        if ( $_POST["ajax"] != "" ) {
            echo "<pre>".print_r($_POST,true)."</pre>";
            if ( $_POST["ajax"] == "blinddown" ) {
                $_SESSION["admin_toggle"][$_POST['id']] = $_POST['id'];
            } elseif ( $_POST["ajax"] == "blindup" ) {
                if ( $_SESSION["admin_toggle"][$_POST['id']] != "" ) {
                    unset($_SESSION["admin_toggle"][$_POST['id']]);
                }
            }
            header("HTTP/1.0 200 OK");exit;
            die();
        }

        include $pathvars["moduleroot"]."wizard/wizard.cfg.php";
        unset($cfg["wizard"]["function"]);
        include $pathvars["moduleroot"]."wizard/wizard-functions.inc.php";
        // +++
        // page basics

        if ( $_POST["main"] == "ajax_suche" ) {
            sleep(1.0);
            header("HTTP/1.1 200 OK");
            $released_content = find_marked_content( $_POST["art"]."/", $cfg, "inhalt", array(1), array("max_age"=>$_POST["time"]), FALSE ,array('presse','ausstellungen','termine'));
            $counter = 0;
            $hit = $released_content[1];

            if ( is_array($hit) ) {
                uasort($hit,'sort_marked_content');

                foreach ( $hit as $key => $value ) {
                    if ( $value["path"] == "---" && $value)continue;

                    if ( !priv_check($value["path"],"admin;publish;edit") ) continue;

                    if ( $_POST["text"] ){
                        if ( stristr($value["titel"],$_POST["text"]) || stristr($value["ext"],$_POST["text"]) ) {

                        } else {
                            continue;
                        }
                    }
                    if ( $value["titel"] == "---" ) $value["titel"] = $value["ext"];

                    $counter++;
                    $itext .= "<li style=\"margin:2px 0 0 0;padding:0.2em;border:1px solid ".$cfg["admin"]["color"]["a"].";\">
                        <span style=\"float: right; text-align: right; display: block;\">
                        <a href=\"".$value["edit"]."\">Bearbeiten</a><br>
                        <a href=\"".$value["view"]."\">zur Seite</a>
                        </span>
                          <b>".$value["titel"]."</b><br />
                          freigegeben am :".$value["changed"]."<br />
                          letzte Version von:".$value["last_author"]."</li>";
                }
            }
            echo "<ul style=\"margin: 3px; padding: 1px; overflow: hidden; list-style-type: none; list-style-position: outside; list-style-image: none; width: auto;\">";
            if ( $_POST["time"] > 0 ) {
                if ( $counter == 0 ) {
                    echo "<li>Keine Treffer</li><ul>";
                } else {
                    echo $itext;
                }
            } else {
                echo "<li>Bitte Zeitraum eingeben!</li>";
            }
            echo "</ul>";
            die();
        }

        // funktions bereich
        // ***

        // banner einbinden
        if ( $pathvars["virtual"] == "" || $_GET["edit"] ) {
            $hidedata["adminbild"] = array();
        }

        // benutzer und gruppen holen
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
            foreach ( $cfg["admin"]["specials"] as $url=>$bereich ) {
               // chefredakteure holen
                get_chefred($url);

                // dataloop holen
                $buffer = find_marked_content( $url, $cfg, "inhalt", array(-2,-1), array(), FALSE );
                $dataloop[$bereich."_edit"] = $buffer[-1];
                $dataloop[$bereich."_release_queue"] = $buffer[-2];
                $dataloop[$bereich."_release_wait"] = $buffer[-2];

                // unterschiedliche "toggle-bereiche" nachbearbeiten
                $toggle_fields = array(
                              "edit" => array("all" => "publish;edit"),
                     "release_queue" => array("all" => "publish"),
                      "release_wait" => array("all" => "edit"),
                );
                foreach ( $toggle_fields as $tog_key=>$tog_value ) {
                    $ausgaben["toggle_".$bereich."_".$tog_key] = "none";
                    $ausgaben["toggle_lokal_".$bereich."_".$tog_key] = "none";
                    if ( is_array ( $dataloop[$bereich."_".$tog_key] )  ) {
                        foreach ( $dataloop[$bereich."_".$tog_key] as $key => $value ) {
                            $zugang = 0;
                            $my = 0;
                            if ( $value["path"] != "---" && $value["path"] != "" && priv_check($value["path"],"admin;publish;edit") ) {
                                if ( $value["author"] == $_SESSION["forename"]." ".$_SESSION["surname"] ) {
                                    $my = -1;
                                }

                                if ( priv_check($value["path"],$tog_value["all"] ) ){
                                    $zugang = -1;                                    
                                }
 
                                if ( priv_check($value["path"],$tog_value["own"]) && $zugang != -1 && $tog_value["own"] != "" ) {
                                    if ( $my == -1 ) {
                                        $zugang = -1;
                                    }                                     
                                }
          
                                if ( $zugang != -1 ) {
                                    continue;
                                }
                                $dataloop[$bereich."_".$tog_key][$key]["color"] = "#CCCCCC";
                                if ( $my == -1 ) $dataloop[$bereich."_".$tog_key][$key]["color"] = "#FF9148";
                                $dataloop[$bereich."_".$tog_key][$key]["red"] = implode(", ",$member_edit);
                                $dataloop[$bereich."_".$tog_key][$key]["chefred"] = implode(", ",$member_publish);
                            } else {
                                unset($dataloop[$bereich."_".$tog_key][$key]);
                            }
                        }
                    }
                }

            if ( is_array($dataloop[$bereich."_edit"]) )           uasort($dataloop[$bereich."_edit"],'sort_marked_content');
            if ( is_array($dataloop[$bereich."_release_queue"]) )  uasort($dataloop[$bereich."_release_queue"],'sort_marked_content');
            if ( is_array($dataloop[$bereich."_release_wait"]) )   uasort($dataloop[$bereich."_release_wait"],'sort_marked_content');

                // globale bereiche
                if ( count($dataloop[$bereich."_edit"]) > 0 && priv_check($url,"admin;edit") ) {
                    $hidedata[$bereich."_edit"]["num"] = count($dataloop[$bereich."_edit"]);
                }
                if ( count($dataloop[$bereich."_release_wait"]) > 0 && !priv_check($url,"admin;publish") && priv_check($url,"admin;edit") ) {
                    $hidedata[$bereich."_release_wait"]["num"] = count($dataloop[$bereich."_release"]);
                }
                if ( count($dataloop[$bereich."_release_queue"]) > 0 && priv_check($url,"admin;publish") ) {
                    $hidedata[$bereich."_release_queue"]["num"] = count($dataloop[$bereich."_release_queue"]);
                }
                // suche in freigebenen artikeln immer einblenden
                $search= $url;
                $id = $url;
                $kate = $url;
                $ausgaben[$bereich."_search"] = parser("administration-recent",'');

                // berechtigung checken
                if ( !priv_check($url,"admin;edit") ) continue;
                $hidedata[$bereich."_section"] = array(
                    "heading" => "#(".$bereich."_heading)",
                        "new" => "#(".$bereich."_new)",
                );

            }

            // normalen content ausschliesslich spezielle bereiche durchgehen
            // * * *
            $bereich = "content";
            $buffer = find_marked_content( "/", $cfg, "inhalt", array(-2,-1), array(), FALSE, array("/blog"));
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
            $design = "modern";
            $stop["nop"] = "nop";
            $positionArray["nop"] = "nop";
            include $pathvars["moduleroot"]."admin/menued2.cfg.php";
            $cfg["menued"]["function"]["login"] = array("locate","make_ebene");
            include $pathvars["moduleroot"]."admin/menued2-functions.inc.php";
            include $pathvars["moduleroot"]."libraries/function_menutree.inc.php";


            if ( $environment["parameter"][1] == "" ) {
                $_SESSION["menued_id"] = "";
                $_SESSION["menued_opentree"] = "";
                $_SESSION["menued_design"] = "";
            } else {
                $_SESSION["menued_id"] = $environment["parameter"][1];
            }

            if ( $_SESSION["menued_id"] != "" ) {

                $ausgaben["edmenu"] = "<script type=\"text/javascript\">Effect.ScrollTo('trigger_sitemap')</script>";

                // explode des parameters
                $opentree = explode("-",$_SESSION["menued_opentree"]);
                // was muss geschlossen werden ?!?!?
                foreach ( $opentree as $key => $value ) {
                    if ( $value != "" ) {
                        delete($value,$value);
                    }
                    if ( $stop != "" ) {
                        if ( in_array($value,$stop) ) {
                            unset ($opentree[$key]);
                        }
                    }
                }

                // punkt oeffnen
                if ( !in_array($_SESSION["menued_id"],$stop) ) {
                    $opentree[] = $_SESSION["menued_id"];
                }

                // link bauen und positionArray bauen
                foreach ( $opentree as $key => $value ) {
                    $treelink == "" ? $trenner = "" : $trenner = "-";
                    $treelink .= $trenner.$value;
                    if ( $value != "" ) {
                        locate($value);
                    }
                }

                $_SESSION["menued_design"] = $design;
            } else {
                $positionArray[0] = 0;
            }

            // welche buttons sollen angezeigt werden
            $mod = array(
                        "edit"=> array("", "Seite editieren", "edit"),
                        "add"=> array("", "Seite hinzufuegen", "add"),
                        "jump"=> array("", "zur Seite", "edit;publish")
                        );

            $blacklist = "/aktuell";
            $wizard_menu = sitemap(0, "admin", "menued",$mod,"");
            $test = explode("<li>",$wizard_menu);
            array_shift($test);
            $preg = '/<img.*\/img>/Ui';
            $preg_link = '/^<a (href)="\/auth\/edit,([0-9]*),[0-9]*\.html"/ui';
            $preg_black = '/(href="\/auth\/login,)([0-9]*)\.html"/ui';
            $color = $cfg["wizard"]["color"]["a"];
            preg_match($preg_black,$line,$black);

            foreach ( $test as $line ) {
                ( $color == $cfg["wizard"]["color"]["a"] ) ? $color = $cfg["wizard"]["color"]["b"] : $color = $cfg["wizard"]["color"]["a"];
                preg_match($preg_black,$line,$black);
                preg_match($preg_link,$line,$regis);

                if ( $black[2] == 263 ) continue;

                if ( $regis[2] ) {
                    if ( eCrc(substr(make_ebene($regis[2]),0,strrpos(make_ebene($regis[2]),"/"))) == "0" ) {
                        $make_crc = "";
                    } else {
                        $make_crc = eCrc(substr(make_ebene($regis[2]),0,strrpos(make_ebene($regis[2]),"/"))).".";
                    }
                    $edit_crc = $make_crc.substr(make_ebene($regis[2]),strrpos(make_ebene($regis[2]),"/")+1);
                    $line = preg_replace($preg_link,"<a href=/auth/wizard/show,".DATABASE.",".$edit_crc.",inhalt,,,.html",$line);
                    $line = preg_replace("/<a href=\"\/auth\/add,[0-9]*,[0-9]*.html\"/","<a href=/auth/wizard/add,,".$edit_crc.".html",$line);
                    $ausgaben["edmenu"] .= "<li style=\"background-color:".$color.";margin:0;padding:0.5em;clear:both;\">".$line."</li>";
                } else {
                    $ausgaben["edmenu"] .= "<li style=\"background-color:".$color.";margin:0;padding:0.5em;clear:both;\">".$line."</li>";
                }
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
            $ausgaben["inaccessible"] .= "# (adminbild) #(adminbild)<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        
        // was anzeigen
        $mapping["main"] = "administration";
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
