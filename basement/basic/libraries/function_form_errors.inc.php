<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "form_errors";
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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    // aufruf: form_errors( form optionen, post array);

    function form_errors( $form_options, $form_values ) {
        global $ausgaben, $element, $dataloop, $hidedata;
        #$ausgaben["form_error"] = "";
        if ( is_array($form_options) && count($form_values) > 0 ) {
            // form options durchlaufen
            foreach($form_options as $name => $value) {
                if ( $value["frequired"] == "-1" && array_key_exists($name, $form_values) ) {
                    // ist das form feld leer ?
                    if ( $form_values[$name] == "" ) {
                        // gibt es eine fehlermeldung in der db ?
                        if ( $value["ferror"] == "" ) {
                            $ausgaben["form_error"] .= "Field: ".$value["flabel"]." required!<br />";
                            $dataloop["form_error"][$name]["text"] = "Field: ".$value["flabel"]." required!";
                        } else {
                            $ausgaben["form_error"] .= $value["ferror"]."<br />";
                            $dataloop["form_error"][$name]["text"] = $value["ferror"];
                        }
                    }
                }
                // sind die eingaben plausibel?
                if ( $value["fcheck"] != "" ) {
                    # thanks @ buffy-1860@gmx.net

                    if ( strstr($value["fcheck"], "PREG:") ) {
                        $preg = substr($value["fcheck"],5);
                        if (!preg_match_all("/$preg/",$form_values[$name],$regs) && !$form_values[$name] == "") {
                            if ( $value["fchkerror"] == "" ) {
                                $ausgaben["form_error"] .= "Field: ".$value["flabel"]." check failed!<br />";
                                $dataloop["form_error"][$name]["text"] = "Field: ".$value["flabel"]." check failed!<br />";
                            } else {
                                $ausgaben["form_error"] .= $value["fchkerror"]."<br />";
                                $dataloop["form_error"][$name]["text"] = $value["fchkerror"];
                            }
                        }
                    }
                }
                // form_element manipulieren
                if ( $dataloop["form_error"][$name]["text"] != "" && $element[$name] != "" ) {
                    preg_match("/class=\"(.*)\"/Ui",$element[$name],$match);
                    $class = "form_error";
                    if ( $match[1] != "" ) $class .= " ".$match[1];
                    $element[$name] = str_replace($match[0],"class=\"".$class."\"",$element[$name]);
                }
            }
            if ( count($dataloop["form_error"]) > 0 ) $hidedata["form_error"] = array();
        }
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
