<?php $t_start = array_sum(explode(' ', microtime())); require "libraries/global.inc.php";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $main_script_name = "$Id$";
    $main_script_desc = "haupt script";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $main_script_name ** ]".$debugging["char"];

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "script name: ".$_SERVER["SCRIPT_NAME"].$debugging["char"];

    // path in die bestandteile zerlegen
    $pathvars["uri"] = $_SERVER["REQUEST_URI"];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "pathvars uri: ".$pathvars["uri"].$debugging["char"];

    $pathvars["requested"] = explode("?", $_SERVER["REQUEST_URI"]);
    $pathvars["requested"] = $pathvars["requested"][0];

    // url ohne .html wird auf index.html gesetzt
    if ( !strstr($pathvars["requested"],".html") ) {
       $pathvars["requested"] = $pathvars["requested"]."index.html"; ###
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "pathvars requested: ".$pathvars["requested"].$debugging["char"];

    $pathvars["level"] = explode("/", $pathvars["requested"]);
    $pieces = "";
    foreach($pathvars["level"] as $piece) {
        $pieces .= $piece." ";
    }
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "pathvars level: array(".$pieces.")".$debugging["char"];
    $pathvars["level_depth"] = count($pathvars["level"])-1;
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "pathvars level depth: ".$pathvars["level_depth"].$debugging["char"];

    // design detection
    if ( $pathvars["level"][1] != "" && is_dir($pathvars["fileroot"]."templates/".$pathvars["level"][1]) ) {
      $environment["design"] = $pathvars["level"][1];
      $pathvars["virtual"] = "/".$environment["design"];
      $authcount++;
      $designsw = " (user)";
    } else {
      $environment["design"] = $specialvars["default_design"];
      $designsw = " (default)";
    }
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "design".$designsw.": ".$environment["design"].$debugging["char"];

    // language detection
    for ( $i=1; $i<=2 ; $i++ ) {
      if ( in_array($pathvars["level"][$i],$specialvars["available_languages"]) ) {
        $position = $i;
        break;
      }
    }
    if ( $position >= 1) {

      // language converter
      if ( is_array($specialvars["convert_languages"]) ) {
        foreach ( $specialvars["convert_languages"] as $key => $value ) {
          if ( $pathvars["level"][$position] == $key ) {
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "lang convert: ".$key."->".$value.$debugging["char"];
            $pathvars["level"][$position] = $value;
            $coverted = "converted ";
            # noch zwei $pathvars umbauen, damit alles schoen umgeleitet wird!
            $pathvars["requested"] = str_replace($key,$value,$pathvars["requested"]);
            $pathvars["uri"] = str_replace($key,$value,$pathvars["uri"]);
            break;
          }
        }
      }

      $environment["language"] = $pathvars["level"][$position];
      $pathvars["virtual"] .= "/".$environment["language"];
      $authcount++;
      $langsw .= " (".$coverted."user)";
      if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "lang".$langsw.": ".$environment["language"].$debugging["char"];
    } else {
      if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "http accept lang: ".$_SERVER["HTTP_ACCEPT_LANGUAGE"].$debugging["char"];
      $http_accept_language = explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
      foreach( $http_accept_language as $lang ) {
        if ( in_array($lang,$specialvars["available_languages"]) ) {
          $environment["language"] = $lang;
          $langsw = " (browser)";
          break;
        }
      }
      if ( $environment["language"] == "" ) {
        $environment["language"] = $specialvars["default_language"];
        $langsw = " (default)";
      }
      if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "lang".$langsw.": ".$environment["language"].$debugging["char"];
    }

    // host werte "www.xxx.yyy.de" in "www" und "xxx.yyy.de" array
    $environment["fqdn"] = explode(".", $_SERVER["HTTP_HOST"], 2);

    // virtual path auth korrektur und auth url init
    $ausgaben["auth_url"] = $pathvars["virtual"];
    $authcount++;
    if ( strstr($pathvars["level"][$authcount],"auth" ) ) {
        $pathvars["virtual"] .= "/auth";
    }
    $pathvars["virtual_depth"] = count(split("/",$pathvars["virtual"]));
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "virtual path: ".$pathvars["virtual"].$debugging["char"];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "virtual path depth: ".$pathvars["virtual_depth"].$debugging["char"];


    //
    // strg: neues ebene + kategorie verfahren
    //

    // neuer parameter 'ebene'
    $environment["ebene"] = substr_replace($pathvars["requested"], '', 0, strlen($pathvars["virtual"]));
    $paramstr = strrchr($environment["ebene"],"/");
    $environment["ebene"] = str_replace($paramstr,"",$environment["ebene"]);
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "strg ebene: ".$environment["ebene"].$debugging["char"];

    // neuer parameter 'parameter'
    $paramlen = (strlen($paramstr)-6);
    $paramstr = substr($paramstr,1,$paramlen);
    $environment["parameter"] = explode(",", $paramstr);
    foreach( $environment["parameter"] as $key => $piece ) {
        if ( $key == 0 ) {
            $pieces = $piece;
            $environment["allparameter"] = $piece;
        } else {
            $environment["allparameter"] .= ",".$piece;
            $pieces .= " ".$piece;
        }
    }
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "strg parameter: array( ".$pieces." )".$debugging["char"];

    // neuer parameter 'kategorie'
    $environment["kategorie"] = $environment["parameter"][0];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "strg kategorie: ".$environment["kategorie"].$debugging["char"];

    // neu: ausgabe template
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "strg ebene crc32: ".crc32($environment["ebene"]).$debugging["char"];
    $environment["template"] = crc32($environment["ebene"]).".".$environment["kategorie"].".tem.html";
    $template_link = "<a href=\"file://".$environment["template"]."\">".$environment["template"]."</a>";
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "strg template (auto): ".$template_link.$debugging["char"];

    // auth url complete
    $ausgaben["auth_url"] = $ausgaben["auth_url"].$environment["ebene"]."/".$environment["allparameter"].".html";
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "auth url: ".$ausgaben["auth_url"].$debugging["char"];

    // print url
    #$ausgaben["print_url"] = $pathvars["uri"]."?print=true";
    $ausgaben["print_url"] = $pathvars["uri"];
    #$ausgaben["print_url"] = "leer";
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "print url: ".$ausgaben["print_url"].$debugging["char"];


    //
    // strg: altes katid + subkatid verfahren
    //

    // welche position haben katid und subkatid
    if ( $pathvars["virtual_depth"] == $pathvars["level_depth"] ) {
        $environment["param"] = $pathvars["level"][$pathvars["level_depth"]];
        #$ausgaben["login_url"] .= "/".$environment["param"];
        #$ausgaben["logout_url"] .= "/".$environment["param"];
    } else {
        $environment["param"] = $pathvars["level"][$pathvars["level_depth"]-1];
        $environment["subparam"] = $pathvars["level"][$pathvars["level_depth"]];
        #$ausgaben["login_url"] .= "/".$environment["param"]."/".$environment["subparam"];
        #$ausgaben["logout_url"] .=  "/".$environment["param"]."/".$environment["subparam"];
    }

    // param und katid extrahieren und evtl. zurechtschneiden
    if (strstr($environment["param"],"html")) {
        $paramlen = (strlen($environment["param"])-5);
        $environment["param"]  = substr($environment["param"],0,$paramlen);
    }
    $environment["param"] = explode(",", $environment["param"]);
    $environment["katid"] = $environment["param"][0];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "strg (alt) katid: ".$environment["katid"].$debugging["char"];

    // subparam und subkatid extrahieren und evtl. zurechtschneiden
    if (strstr($environment["subparam"],"html")) {
        $subparamlen = (strlen($environment["subparam"])-5);
        $environment["subparam"] = substr($environment["subparam"],0,$subparamlen);
    }
    $environment["subparam"] = explode(",", $environment["subparam"]);
    $environment["subkatid"] = $environment["subparam"][0];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "strg (alt) subkatid: ".$environment["subkatid"].$debugging["char"];


    // die drei design abhaengigen variablen werden angepasst
    $pathvars["menuroot"]  = "http://".$_SERVER["HTTP_HOST"].$pathvars["virtual"];
    $pathvars["images"]    = "/images/".$environment["design"]."/";
    $pathvars["templates"] = $pathvars["fileroot"]."templates/".$environment["design"]."/";

    // grundmapping main output
    if ( $specialvars["crc32"] == -1 ) {
        if ( $environment["kategorie"] != "" && $environment["kategorie"] != "index" ) {
             $mapping["main"] = $environment["kategorie"];
        }
    } else {
        if ( $environment["katid"] != "" && $environment["katid"] != "index" ) {
             $mapping["main"] = $environment["katid"];
        }
    }

    // was steht in den post vars
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= $debugging["char"]."form (post):".$debugging["char"];
    foreach($HTTP_POST_VARS as $name => $value) {
         if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= $name." => ".$value.$debugging["char"];
    }

    // was steht in den get vars
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= $debugging["char"]."form (get):".$debugging["char"];
    foreach($HTTP_GET_VARS as $name => $value) {
         if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= $name." => ".$value.$debugging["char"];
    }

    // hallo zur datenbank
    $db      = new DB_connect();
    $version = $db->getVERSION();
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "db version: ".$version.$debugging["char"];

    $connect = $db->connect();
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "db connect: ".$connect.$debugging["char"];

    // login verwalten ( muss erste funktion nach datenbank connect sein !!!)
    require $pathvars["config"]."auth.cfg.php";
    require $pathvars["libraries"]."auth.inc.php";

    // überschreiben von default werten
    require $pathvars["config"]."overwrite.cfg.php";

    if ( $environment["katid"] == "cms") {
        include $pathvars["libraries"]."cms.inc.php";
    } elseif ($environment["katid"] == "fileed") {
        #keine session variable ebene und kategorie erzeugen (mor 1909)
    } else {
        if ( strstr($_SERVER["REQUEST_URI"],"/auth/") ) {
            session_register("page");
            $HTTP_SESSION_VARS["page"] = $_SERVER["REQUEST_URI"];
            session_register("ebene");
            $HTTP_SESSION_VARS["ebene"] = $environment["ebene"];
            session_register("kategorie");
            $HTTP_SESSION_VARS["kategorie"] = $environment["kategorie"];
        }
    }

    // view funktion
    if ( $environment["katid"] == "view" ) {
        include $pathvars["libraries"]."view.inc.php";
    }

    // steuerung der funktionen
    require $pathvars["config"]."addon.cfg.php";

    // webdesigner kann mit dieser datei das laden der templates beinflussen
    if ( file_exists($pathvars["templates"]."linking.inc.php") ) {
      $linking_path = $pathvars["templates"];
      include $linking_path."linking.inc.php";
    } elseif ( file_exists($pathvars["fileroot"]."templates/default/linking.inc.php") ) {
      $linking_path = $pathvars["fileroot"]."templates/default/";
      include $linking_path."linking.inc.php";
    }

    // rekursiven parser aufrufen
    if ( $HTTP_POST_VARS["print"] != "" || $HTTP_GET_VARS["print"] != "" ) {
        $debugging["html_enable"] = 0;
        $print_template = $HTTP_POST_VARS["print"][2].$HTTP_GET_VARS["print"][2];
        rparser( $print_template.".tem.html", $specialvars["default_template"].".tem.html");
    } elseif ( $HTTP_POST_VARS["hijack"] != "" || $HTTP_GET_VARS["hijack"] != "" ) {
        foreach ( $HTTP_GET_VARS as $key => $value ) {
            if ( $hijack == "" ) {
                $hijack  = $value;
            } else {
                $hijack .= "&".$key."=".$value;
            }
        }
        $frameset_template = $HTTP_POST_VARS["hijack"].$hijack;
        #$ausgaben["navigation"] = "/templates/net/frameset.head.tem.html";
        $ausgaben["navigation"] = "frameset.head.tem.html?head=true";
        $ausgaben["hijack"] = $frameset_template;
        rparser("frameset.tem.html", $specialvars["default_template"].".tem.html");
    } elseif ( $HTTP_POST_VARS["head"] != "" || $HTTP_GET_VARS["head"] != "" ) {
        $array = explode("?",$_SERVER["HTTP_REFERER"]);
        $ausgaben["seite"] = $array[0];
        rparser("frameset.head.tem.html", $specialvars["default_template"].".tem.html");
    } else {
        rparser("index.tem.html", $specialvars["default_template"].".tem.html");
    }

    $exec_time = array_sum(explode(' ', microtime())) - $t_start;
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Execution Time: ".$exec_time." seconds".$debugging["char"];

    // entgueltige Debug Ausgabe zusammensetzen und ausgeben
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $main_script_name ++ ]".$debugging["char"];
    if ( $debugging["html_enable"] ) echo $debugging["ausgabe"].$debugging["footer"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
