<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_priv_check.inc.php v1 emnili
// rekursive pruefung der rechte
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

    // aufruf: $priv_check(ebene,kategorie,database,$right);
    // funktion prueft rekursiv, ob die aktuelle url rechte in der $_SESSION["content"] besitzt !

    function priv_check($url,$required,$dbase="") {
        global $cfg,$specialvars,$rechte;
        if ( !function_exists('priv_check_path') ) {
            function priv_check_path($url,$required,&$hit,&$del,$dbase) {
                global $environment;
                if ( $url == "" ) $url = $environment["ebene"]."/".$environment["kategorie"];
                if ( @is_array($_SESSION["content"] ) ){
                    $array = explode(";",$required);
                    $del_array = explode(",",@$_SESSION["content"][$dbase][$url]["del"]);
                    $add_array = explode(",",@$_SESSION["content"][$dbase][$url]["add"]);
                    foreach ( $array as $value ) {
                        if ( in_array($value,$del_array) ) {
                            $del[$value] = -1;
                        }
                        if ( in_array($value,$add_array) && @$del[$value] != -1) {
                            $hit = True;
                        }
                    }
                }
                if ( $url != "/" && $url != ".html/" ) {
                    $url = dirname($url);
                    priv_check_path($url,$required,$hit,$del,$dbase);
                }
            }
        }
        if ( $specialvars["security"]["new"] != -1 ) {
            if ( $required == "" ) {
                $url = dirname($url);
                $funktion = basename($url);
                $required = $cfg["auth"]["menu"][$funktion][1];
            }
            $array = explode(";",$required);
            foreach( $array as $value) {
                if ( $rechte[$value] == -1 ) return True;
            }
        } else {
            $hit = "";
            $del= array();
            if ( $required != "" ) {
                priv_check_path($url,$required,$hit,$del,$dbase);
            }
            return $hit;
        }
    }

    function priv_info($url,&$hit,$art='',$self='') {
        global $db,$url_orig,$url_orig_user;

        // beim ersten aufruf $art auf gruppe setzen
        if ( $art == "" ) {
           $art = "group";
           // einstiegsurl speichern damit man weiss wo man ist
           $url_orig = $url;
           // einstiegsurl nochmal speichen fuer den user-zweig
           $url_orig_user = $url;
        }

        // url pruefen
        if ( !preg_match("/^[A-Za-z_\-\.0-9\/]+$/",$url) ){
            return;
        }
    
        if ( $art=="group") {
            $sql = "SELECT * FROM auth_content INNER JOIN auth_group ON (auth_content.gid=auth_group.gid) INNER JOIN auth_priv ON (auth_content.pid=auth_priv.pid) WHERE auth_content.gid != 0 AND tname='".$url."'";
            $result = $db -> query($sql);
            while ( $all = $db -> fetch_array($result,1) ) {
                if ( $all["neg"] == -1 ) {
                    @$hit["group"][$url]["del"][$all["ggroup"]] .= $all["priv"].",";
                } elseif ( $url_orig != $all["tname"]) {
                    @$hit["group"][$url]["inh"][$all["ggroup"]] .= $all["priv"].",";
                } else {
                    @$hit["group"][$url]["add"][$all["ggroup"]] .= $all["priv"].",";
                }
            }
            if ( $url != "/" ) {
                $url = dirname($url);
                priv_info($url,$hit,$art,"self");
            }

            // nachdem die gruppe abgearbeitet ist, mit user weitermachen
            if ( $self == "" ) {
                priv_info($url_orig,$hit,"user");
            }

        }  elseif ( $art == "user" ) {
            $sql = "SELECT * FROM auth_content INNER JOIN auth_user ON (auth_content.uid=auth_user.uid) INNER JOIN auth_priv ON (auth_content.pid=auth_priv.pid) WHERE auth_content.uid != 0 AND tname='".$url_orig."'";
            $result = $db -> query($sql);
            while ( $all = $db -> fetch_array($result,1) ) {
                if ( $all["neg"] == -1 ) {
                    if ( isset($hit["user"][$url_orig]["del"][$all["username"]]) ) {
                        $hit["user"][$url_orig]["del"][$all["username"]] .= $all["priv"].",";
                    } else {
                        $hit["user"][$url_orig]["del"][$all["username"]] = $all["priv"].",";
                    }
                } elseif ( $url_orig_user != $all["tname"]) {
                    if ( isset($hit["user"][$url_orig]["inh"][$all["username"]]) ) {
                        $hit["user"][$url_orig]["inh"][$all["username"]] .= $all["priv"].",";    
                    } else {
                        $hit["user"][$url_orig]["inh"][$all["username"]] = $all["priv"].",";    
                     }                    
                } else {
                    if ( isset($hit["user"][$url_orig]["add"][$all["username"]]) ) {
                        $hit["user"][$url_orig]["add"][$all["username"]] .= $all["priv"].",";
                    } else {
                        $hit["user"][$url_orig]["add"][$all["username"]] = $all["priv"].",";
                    }
                }
            }
            if ( $url_orig != "/" ) {
                $url_orig = dirname($url_orig);
                priv_info($url_orig,$hit,"user","self");
            }
        }

            return $hit;
    }

        function plausibleCheck($modus="display") {
            global $db,$ausgaben;

            if ( !function_exists("negCheck") ) {
                function posnegCheck($all, &$found) {
                    global $db;
                    $sql = "";
                    $white_list = array("uid", "gid", "pid", "db", "tmp_tname");
                    foreach ( $all as $key => $value ) {
                        if ( !in_array($key, $white_list) || is_integer($key)) continue;
                        if ( $key == "tmp_tname" ) $key = "tname";
                        if ( $key == "gid" && $value == "" ) $value = "0";
                        if ( $key == "uid" && $value == "" ) $value = "0";
                        $sql  .= "auth_content.".$key."='".$value."' AND ";
                    }
                    $and = strrpos($sql, " AND ");
                    $sql = substr($sql, 0, $and);
                    $sql = "SELECT * FROM auth_content
                             INNER JOIN auth_priv ON ( auth_content.pid=auth_priv.pid )
                             LEFT JOIN auth_group ON ( auth_content.gid=auth_group.gid )
                             LEFT JOIN auth_user ON ( auth_content.uid=auth_user.uid )
                             WHERE ".$sql;
                    $result = $db -> query($sql);
                    $data = $db -> fetch_array($result,1);
                    $found = "";
                    if ( $data["neg"] == -1 ) $found = "neg";
                    if ( $data["pid"] && $data["neg"] != -1 ) $found = "pos";
                    if  ( $all["tmp_tname"] != "/" && !$found ) {
                       $all["tmp_tname"] = dirname($all["tmp_tname"]);
                       posnegCheck($all, $found);
                    }
                    return $found;
                }
            }

            // array u. variable leeren
            $plausible_error = "";
            $counter = 0;

            // Positiv-Check
            $sql = "SELECT * FROM auth_content  INNER JOIN auth_priv ON ( auth_content.pid=auth_priv.pid ) LEFT JOIN auth_group ON ( auth_group.gid=auth_content.gid ) LEFT JOIN auth_user ON ( auth_user.uid=auth_content.uid ) WHERE tname != '/' AND neg!='-1'";
            $result = $db -> query($sql);
            while ( $all = $db -> fetch_array($result,1) ) {
                $all["tmp_tname"] = dirname($all["tname"]);
                if ( posnegCheck($all,$nop,"pos") == "pos" )  {
                    $counter++;
                    $RechtInhaberText = "Gruppe";
                    $RechtInhaberDaten = $all["ggroup"];
                    if ( $all["uid"] != 0 ) {
                        $RechtInhaberText = "Benutzer";
                        $RechtInhaberDaten = $all["username"];
                    }
                    $plausible_error[$counter]["message"] = "Fehler: Doppeltes Recht <b>".$all["priv"]."</b>  bei <b>".$all["tname"]."</b> fuer ".$RechtInhaberText.": ".$RechtInhaberDaten;
                    $plausible_error[$counter]["group_beschreibung"] = $all["ggroup"];
                    $plausible_error[$counter]["right"] = $all["priv"];
                    $plausible_error[$counter]["group_id"] = $all["gid"];
                    $plausible_error[$counter]["user_id"] = $all["uid"];
                }
            }

            // Negativ-Check
            $sql = "SELECT * FROM auth_content  INNER JOIN auth_priv ON ( auth_content.pid=auth_priv.pid ) LEFT JOIN auth_group ON ( auth_group.gid=auth_content.gid ) LEFT JOIN auth_user ON ( auth_user.uid=auth_content.uid ) WHERE neg='-1'";
            $result = $db -> query($sql);
            while ( $all = $db -> fetch_array($result,1) ) {
                $all["tmp_tname"] = dirname($all["tname"]);
                if ( posnegCheck($all,$nop) != "pos" )  {
                    $counter++;
                    $RechtInhaberText = "Gruppe";
                    $RechtInhaberDaten = $all["ggroup"];
                    if ( $all["uid"] != 0 ) {
                        $RechtInhaberText = "Benutzer";
                        $RechtInhaberDaten = $all["username"];
                    }
                    $plausible_error[$counter]["message"] = "Fehler: Alleinstehendes Negiertes <b>".$all["priv"]."</b>  bei <b>".$all["tname"]."</b> fuer ".$RechtInhaberText.": ".$RechtInhaberDaten;
                    $plausible_error[$counter]["group_beschreibung"] = $all["ggroup"];
                    $plausible_error[$counter]["right"] = $all["priv"];
                    $plausible_error[$counter]["group_id"] = $all["gid"];
                    $plausible_error[$counter]["user_id"] = $all["uid"];
                }
            }
            return $plausible_error;
        }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
