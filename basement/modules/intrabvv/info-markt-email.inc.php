<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    phpWEBkit - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of phpWEBkit

    phpWEBkit is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    phpWEBkit is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with phpWEBkit; If you did not, you may download a copy at:

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


    if ( $environment["parameter"][1] == "form" ) {

        if (in_array("send",$environment["parameter"])) {
            $para = 3;
        } else {
            $para = 2;
        }
        $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][$para]."'";
        $result  = $db -> query($sql);
        $data = $db -> fetch_array($result,$nop);


        // bilder zum entfernen
        $image_delete = array("extlink.gif",
                              "schlagzeile.gif"
                              );


        $form_fields = array("emailfrom",
                             "emailto",
                             "emailzusatz",
                             "emailabsname"
                             );

        if ( count($HTTP_POST_VARS) == 0 ) {
            $form_values["emailfrom"] = "weam@va-bfdau.bayern.de";
            $form_values["emailto"] = "mor@va-bfdau.bayern.de";
        } else {
            $form_values = $HTTP_POST_VARS;
        }

        // form options holen
        $form_options = form_options("1943315524.email");


        // form elememte bauen
        foreach ($form_fields as $key){
            ( $form_options[$key]["fsize"] > 0 ) ? $size = " size=\"".$form_options[$key]["fsize"]."\"" : $size = "";
            ( $form_options[$key]["fclass"] != "" ) ? $class = " class=\"".$form_options[$key]["fclass"]."\"" : $class = " class=\"".$form_defaults["class"]["textfield"]."\"";
            ( $form_options[$key]["fstyle"] != "" ) ? $style = " style=\"".$form_options[$key]["fstyle"]."\"" : $style = "";

            ( $form_values[$key] != "" ) ? $value = " value=\"".$form_values[$key]."\"" : $value = "";

            $element[$key] = "<input type=\"text\"".$size.$class.$style." name=\"".$key."\" ".$value.">\n";
        }

         /*
        ( $form_options["emailfrom"]["fsize"] > 0 ) ? $size = " size=\"".$form_options["emailfrom"]["fsize"]."\"" : $size = "";
        ( $form_options["emailfrom"]["fclass"] != "" ) ? $class = " class=\"".$form_options["emailfrom"]["fclass"]."\"" : $class = " class=\"".$form_defaults["class"]["textfield"]."\"";
        ( $form_options["emailfrom"]["fstyle"] != "" ) ? $style = " style=\"".$form_options["emailfrom"]["fstyle"]."\"" : $style = "";

        ( $form_values["emailfrom"] != "" ) ? $value = " value=\"".$form_values["emailfrom"]."\"" : $value = "";

        $element["emailfrom"] = "<input type=\"text\"".$size.$class.$style." name=\"emailfrom\" ".$value.">\n";


        ( $form_options["emailto"]["fsize"] > 0 ) ? $size = " size=\"".$form_options["emailto"]["fsize"]."\"" : $size = "";
        ( $form_options["emailto"]["fclass"] != "" ) ? $class = " class=\"".$form_options["emailto"]["fclass"]."\"" : $class = " class=\"".$form_defaults["class"]["textfield"]."\"";
        ( $form_options["emailto"]["fstyle"] != "" ) ? $style = " style=\"".$form_options["emailto"]["fstyle"]."\"" : $style = "";

        ( $form_values["emailto"] != "" ) ? $value = " value=\"".$form_values["emailto"]."\"" : $value = "";

        $element["emailto"] = "<input type=\"text\"".$size.$class.$style." name=\"emailto\" ".$value.">\n";
        */


        // was anzeigen
        # automatik geht $mapping["main"] = "1943315524.email";
        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["basis"]."/".$cfg["ebene"]["zwei"]."/email,form,send,".$environment["parameter"][2].".html";

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }


        function tagremove2 ($text,$was="") {
            global $image_ausgabe, $links_ausgabe;
            // tags entfernen
            $i = 0;
            $preg = "\[[A-Z]{1,6}(\]|=)";
            while ( preg_match("/$preg/",$text,$regs ) ) {
                $opentag = $regs[0];
                #echo $opentag;
                if ( strstr($text, $opentag) ){
                    // wo beginnt der tag
                    $tagbeg = strpos($text,$opentag);
                    // wie sieht der endtag aus
                    if ( strstr($opentag, "=") ) {
                      $endtag = str_replace("=","]",$opentag);
                      $endtag = str_replace("[","[/",$endtag);
                    } else {
                      $endtag = str_replace("[","[/",$opentag);
                    }
                    // wo endet der tag
                    $tagend = strpos($text,$endtag);
                    // wie lang ist der tag
                    $taglen = (int) $tagend-$tagbeg;
                    // wie lautet der tagwert
                    $tagwertbeg = $tagbeg + strlen($opentag);
                    $tagwertlen = $taglen - strlen($endtag)+1;
                    $tagwert = substr($text,$tagwertbeg,$tagwertlen);

                 /*   // cariage return + linefeed fix
                    $tagwert_nocrlf = str_replace("AB]\r\n","AB]",$tagwert);
                    $tagwert_nocrlf = str_replace("W]\r\n","W]",$tagwert_nocrlf);
                    $tagwert_nocrlf = str_replace("L]\r\n","L]",$tagwert_nocrlf);
                    $tagwert_nocrlf = str_replace("\r\n[","[",$tagwert_nocrlf);
                 */


                 /*   // offene tags abfangen
                    #if ( strstr($tagwert, $opentag) || ( strstr($replace, $opentag) && $tagwert == "" ) ) {
                    if ( strstr($tagwert, $opentag) || $tagwertlen < 0 ) {
                        $i++;
                        $merk_es_dir["##$i##"] = $opentag;
                        $ausgabewert = "<big><font color=\"#FF0000\">##$i## (close tag?) </font></big>";
                        $replace = str_replace($opentag,$ausgabewert,$replace);
                    }
                  */
                }
                switch ($opentag) {
                    case "[IMG=":
                            if (strstr($tagwert,"schlagzeile.") || strstr($tagwert,"extlink.gif") || strstr($tagwert,"bullet.gif") || strstr($tagwert,"space20.gif")) {
                                $text = str_replace($opentag.$tagwert.$endtag,"",$text);
                            continue;
                            }
                            $j++;
                            $image[$j] = $tagwert;
                            $text = str_replace($opentag.$tagwert.$endtag,"{BILD $j}",$text);
                        break;
                    case "[IMG]":
                            if (strstr($tagwert,"schlagzeile.") || strstr($tagwert,"extlink.gif") || strstr($tagwert,"bullet.gif") || strstr($tagwert,"space20.gif")) {
                                $text = str_replace($opentag.$tagwert.$endtag,"",$text);
                            continue;
                            }
                            $j++;
                            $image[$j] = $tagwert;
                            $text = str_replace($opentag.$tagwert.$endtag,"{BILD $j}",$text);
                        break;

                    case "[LINK]":
                            $i++;
                            $text = str_replace($opentag.$tagwert.$endtag,"{LINK ".$i."}",$text);
                            $links[$i] = $tagwert;
                            break;

                    case "[LINK=":
                            $tagwerte = explode("]",$tagwert,2);
                            if ( strstr($tagwerte[0],"#") ) {
                                $text = str_replace($opentag.$tagwert.$endtag,$tagwerte[1],$text);
                                continue;
                            }
                            $i++;
                            $pos = strrpos($tagwerte[0],";");
                            if ( $pos >= 1 ) {
                                $replace = substr($tagwerte[0],0,$pos);
                            } else {
                                $replace = $tagwerte[0];
                            }
                            $text = str_replace($opentag.$tagwert.$endtag,"{LINK ".$i."}".$tagwerte[1],$text);
                            $links[$i] = $replace;
                        break;

                    case "[LIST]":
                            $text = str_replace($opentag.$tagwert.$endtag,"§§".$tagwert."%%",$text);
                        break;

                    case "[LIST=":
                            $taglistend = strpos($tagwert,"]");
                            $taglist = substr($tagwert,$taglistend+1);
                            $text = str_replace($opentag.$tagwert.$endtag,"§§".$taglist."%%",$text);
                        break;

                    case "[EMAIL=":
                            $tagwerte = explode("]",$tagwert,2);
                            if ( $tagwerte[1] == "" ) {
                                $beschriftung = $tagwerte[0];
                            } else {
                                $beschriftung = $tagwerte[1]." ".$tagwerte[0];
                            }
                            $text = str_replace($opentag.$tagwert,$beschriftung,$text);
                            $text = str_replace($endtag,"",$text);

                    case "[ANK=":

                        $tagrestbeg = strpos($tagwert,"]");
                        $ankart = substr($tagwert,0,$tagrestbeg+1);
                        $text = str_replace($opentag.$ankart,"",$text);
                        $text = str_replace($endtag,"",$text);
                        break;

                    default:
                        $text = str_replace($opentag,"",$text);
                        $text = str_replace($endtag,"",$text);
                }
            }
            $i = 0;
            if (is_array($links) && $was == "text") {
                foreach ($links as $key => $value) {
                    $i++;
                    $links_ausgabe .= "Link ".$i.": ".$value."\n";
                }
                $links_ausgabe = "\nLinks zum Beitrag:\n".$links_ausgabe;
            }

            if (is_array($image) && $was == "text") {
                foreach ($image as $key => $value) {
                    if ( strstr($value, ";") ) {
                        $pos = strpos($value,";");
                        $value = substr($value,0,$pos);
                    }
                    if ( strstr($value, "]") ) {
                        $pos = strpos($value,"]");
                        $value = substr($value,0,$pos);
                    }
                    $image_ausgabe .= "Bild ".$key." http://www.bvv.bayern.de".$value."\n";
                }
                $image_ausgabe = "\nBilder zum Beitrag:\n".$image_ausgabe;
            }

            // den markierten LIST-Tag finden und formatiert ausgeben
            $preg= "§§";
            while ( preg_match("/$preg/",$text,$regs ) ) {
                $listbeg = strpos($text,"§§");
                $listend = strpos($text,"%%");
                $listlen = (int) ($listend)-$listbeg;
                $wert = substr($text,$listbeg,$listlen+2);
                $listausgabe = substr($text,$listbeg+2,$listlen-2);
                #echo $listausgabe;
                $liste = explode("[*]",$listausgabe);
                foreach ($liste as $key) {
                    $key = wordwrap($key,60,"\n",0);
                    $key = str_replace("\n","\n     ",$key);
                    $ersatz .= "\n   * ".$key."\n";
                }
                $text = str_replace($wert,$ersatz."\n",$text);
                $wert = "";
                $ersatz = "";
            }
            return($text);
        }



        $ausgabe = tagremove($data["itext"],true);
        $ausgabe_ilead = tagremove($data["ilead"]);

        // beschreibung ausgeben
        if ($data["ilead"]) {
            $ausgaben["itext"] = $ausgabe_ilead;
        } else {
#            $ausgaben["itext"] = substr($ausgabe,0,300)."...";
            $ausgaben["itext"] = tagremove (substr ($data["itext"],0,300)."...");
        }
        $ausgaben["iautor"] = $data["iautor"];
        $ausgaben["ikategorie"] = $data["ikategorie"];
        $ausgaben["ititel"] = $data["ititel"];

        if ( $environment["parameter"][2] == "send" ) {


            // form eingaben prüfen
            form_errors( $form_options, $form_values );

            // artikel abschicken
            if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) ) {

                $url = "URL dieses Beitrages:\n"."http://www.bvv.bayern.de".$cfg["basis"]."/".$environment["katid"]."/details,".$environment["parameter"][3].".html";

                // itext u. ititel mit einigen angaben erweitern
                if ($form_values["emailzusatz"] != "") {
                    $zusatz = "-----\n".$form_values["emailzusatz"]."\n-----\n";
                 } else {
                    $zusatz = "";
                }
                if ($form_values["emailabsname"] != "") {
                    $from = "\"".$form_values["emailabsname"]."\" <".$form_values["emailfrom"].">";
                } else {
                    $from = $form_values["emailfrom"];
                }

                $from_ausgabe = "Dieser Beitrag aus dem Intranet wurde Ihnen von\n".$from." gesandt. \n";
                $ausgabe = $from_ausgabe.$zusatz."\n".$ausgabe."\n\n";
                $ausgabe = wordwrap($ausgabe,80,"\n",1);
                $ausgabe = $ausgabe.$url."\n".$image_ausgabe.$links_ausgabe;

                // text versenden
                mail($form_values["emailto"],"Intranet: ".$data["ititel"],$ausgabe,"From: ".$from."\r\n");
                header("Location: ".$ausgaben["form_referer"]);

            }
        }

    } elseif ( $environment["parameter"][2] == "irgendwas" ) {

    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
