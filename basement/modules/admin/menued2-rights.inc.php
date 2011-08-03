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

    $kategorie2check = substr(make_ebene($environment["parameter"][1]),0,strpos(make_ebene($environment["parameter"][1]),"/"));
    $ebene2check = substr(make_ebene($environment["parameter"][1]),strpos(make_ebene($environment["parameter"][1]),"/"));

    if ( $cfg["menued"]["modify"]["rights"][2] == "" || priv_check('', $cfg["menued"]["modify"]["rights"][2] ) || ($cfg["auth"]["menu"]["menued"][2] == -1 &&  priv_check('', $cfg["menued"]["modify"]["rights"][2],$specialvars["dyndb"] ) ) ) {
        $specialvars["dynlock"] = True;
        $sql = "SELECT * FROM ".$cfg["menued"]["db"]["menu"]["entries"]." INNER JOIN ".$cfg["menued"]["db"]["lang"]["entries"]." ON (site_menu.mid=site_menu_lang.mid) WHERE site_menu.mid = ".$environment["parameter"][1];
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $entry[] = $data["entry"];
        $show_path = $data["label"];
    
        if ( $data["refid"] != "0" ) {
            while ( $count != "0" ) {
                $sql = "SELECT * FROM ".$cfg["menued"]["db"]["menu"]["entries"]." INNER JOIN ".$cfg["menued"]["db"]["lang"]["entries"]." ON (site_menu.mid=site_menu_lang.mid) WHERE site_menu.mid = ".$data["refid"];            
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,1); 
                $entry[] = $data["entry"];
                $show_path = $data["label"]."/".$show_path;
                $count = $data["refid"];  
            }
        }
    
        // pfad umdrehen
        $entry = array_reverse($entry);
    
        // letztes element ist die kategorie anschließend entfernen
        $last_element = array_pop($entry);
    
        // crc bauen
        foreach ( $entry as $key => $value ) {
            $crc_part .= "/".$value;
        }
        $crc = eCRC($crc_part).".".$last_element;
    
        // in auth_special wird immer die aktuelle db eingetragen
        $base = $db -> getdb();
    
        $ausgaben["path"] = $show_path;
        $ausgaben["database"] = $base;
    
        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);
    
        // form elememte bauen
        #$element = form_elements( $cfg["menued"]["db"]["menu"]["entries"], $form_values );
    
        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".rights";
    
        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["menued"]["basis"]."/rights,".$environment["parameter"][1].",".$environment["parameter"][2].",verify.html";
        $ausgaben["form_break"] = $cfg["menued"]["basis"]."/list.html";    
    
        // page basics
        // ***
    
        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        // +++
        // unzugaengliche #(marken) sichtbar machen
    
        // +++
        // page basics
    
        $order = explode(",",$cfg["menued"]["db"]["user"]["order"]);
    
        // zum eintrag in auth_special immer nach DATABASE wechseln
        $db -> selectdb(DATABASE,FALSE);
    
        // level management form form elemente begin
        // ***
        $element["add"] = "<input type=\"submit\" name=\"add\" value=\"&lt;&lt;&lt;\">";
        $element["del"] = "<input type=\"submit\" name=\"del\" value=\"&gt;&gt;&gt;\">";
        $element["actual"] = "<select name=\"actual[]\" size=\"10\" multiple>";
        $element["avail"] = "<select name=\"avail[]\" size=\"10\" multiple>";
    
        $sql = "SELECT * FROM ".$cfg["menued"]["db"]["user"]["entries"]." INNER JOIN ".$cfg["menued"]["db"]["right"]["entries"]." ON (".$cfg["menued"]["db"]["user"]["entries"].".".$cfg["menued"]["db"]["user"]["key"]."=".$cfg["menued"]["db"]["right"]["entries"].".".$cfg["menued"]["db"]["right"]["userkey"].") INNER JOIN ".$cfg["menued"]["db"]["level"]["entries"]." on (".$cfg["menued"]["db"]["right"]["entries"].".".$cfg["menued"]["db"]["right"]["levelkey"]."=auth_level.lid) WHERE ".$cfg["menued"]["db"]["level"]["levelkey"]." = 'cms_edit' ORDER by ".$cfg["menued"]["db"]["user"]["order"];
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            $text = "";
            foreach ( $order as $value ) {
                $text .= $all[$value]." ";
            }
            $element["avail"] .= "<option value=\"".$all[$cfg["menued"]["db"]["user"]["key"]]."\">".$text."</option>\n";
        }
        $element["avail"] .= "</select>";
        $sql = "SELECT * FROM ".$cfg["menued"]["db"]["special"]["entries"]." INNER JOIN ".$cfg["menued"]["db"]["user"]["entries"]." ON (".$cfg["menued"]["db"]["user"]["key"]."=".$cfg["menued"]["db"]["special"]["userkey"].") WHERE ".$cfg["menued"]["db"]["special"]["dbasekey"]."='".$base."' AND ".$cfg["menued"]["db"]["special"]["tnamekey"]."='".$crc."' AND ".$cfg["menued"]["db"]["special"]["contentkey"]."='-1'";
        $result = $db -> query($sql);
    
        while ( $all = $db -> fetch_array($result,1) ) {
            $text = "";
            foreach ( $order as $value ) {
                $text .= $all[$value]." ";
            } 
            $actuallarray[] =  $all[$cfg["menued"]["db"]["user"]["key"]];
            $element["actual"] .= "<option value=\"".$all[$cfg["menued"]["db"]["user"]["key"]]."\">".$text."</option>\n";
        }
        $element["actual"] .= "</select>";
    
        if ( $environment["parameter"][3] == "verify" ) {
            if ( is_array($HTTP_POST_VARS["avail"]) && isset($HTTP_POST_VARS["add"]) ) {
                foreach ($HTTP_POST_VARS["avail"] as $name => $value ) {
                    if ( is_array($actuallarray)) {
                        if (in_array($value,$actuallarray)) {
                            $ausgaben["form_error"] = "#(error_dupe)"; 
                        } else {
                            $sql = "INSERT INTO ".$cfg["menued"]["db"]["special"]["entries"]." (".$cfg["menued"]["db"]["special"]["userkey"].",".$cfg["menued"]["db"]["special"]["contentkey"].",".$cfg["menued"]["db"]["special"]["dbasekey"].",".$cfg["menued"]["db"]["special"]["tnamekey"].") VALUES ('".$value."','-1','".$base."','".$crc."')";
                            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                            $db -> query($sql);
                        }
                    } else {
                        $sql = "INSERT INTO ".$cfg["menued"]["db"]["special"]["entries"]." (".$cfg["menued"]["db"]["special"]["userkey"].",".$cfg["menued"]["db"]["special"]["contentkey"].",".$cfg["menued"]["db"]["special"]["dbasekey"].",".$cfg["menued"]["db"]["special"]["tnamekey"].") VALUES ('".$value."','-1','".$base."','".$crc."')";
                        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $db -> query($sql);
                    }
                }
                if ( isset($HTTP_POST_VARS["add"]) && $ausgaben["form_error"] == "" ) {
                header("Location: ".$cfg["menued"]["basis"]."/rights,".$environment["parameter"][1].",".$environment["parameter"][2].".html");
                }
            }
    
            if ( is_array($HTTP_POST_VARS["actual"]) && isset($HTTP_POST_VARS["del"]) ) {
                foreach ($HTTP_POST_VARS["actual"] as $name => $value ) {
                    $sql = "DELETE FROM ".$cfg["menued"]["db"]["special"]["entries"]." WHERE ".$cfg["menued"]["db"]["special"]["userkey"]."='".$value."' AND ".$cfg["menued"]["db"]["special"]["dbasekey"]."='".$base."' AND ".$cfg["menued"]["db"]["special"]["contentkey"]." = '-1' AND ".$cfg["menued"]["db"]["special"]["tnamekey"]."='".$crc."'";
                    $db -> query($sql);
                }
                if ( isset($HTTP_POST_VARS["del"]) && $ausgaben["form_error"] == "" ) {
                header("Location: ".$cfg["menued"]["basis"]."/rights,".$environment["parameter"][1].",".$environment["parameter"][2].".html");
                }
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
