<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "form_elements";
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

    // aufruf: $form_elements = form_options( formular tabelle, form werte des user );
    function form_elements( $table, $form_values, $extend = "" ) {
        global $db, $form_options, $defaults;

        // wenn magic_quotes_gpc an ist muessen alle daten
        // eines post von \ befreit werden.
        if ( get_magic_quotes_gpc() ) {
            $stripslashes = True;
        }

        $columns = $db -> show_columns($table);
        #echo "<pre>".print_r($columns,True)."</pre>";
        foreach ( $columns as /* $key => */ $fields ) {

            // stripslashes fuer das affenform
            if ( $stripslashes == True ) $form_values[$fields["Field"]] = stripslashes($form_values[$fields["Field"]]);

            // not null bedeutet feld ausfuellen
            if ( $fields["Null"] == "" && $form_options[$fields["Field"]]["frequired"] == "" ) {
                $form_options[$fields["Field"]]["flabel"] = $fields["Field"];
                $form_options[$fields["Field"]]["frequired"] = "-1";
            }

            // postgres workaround to get the enum option :)
            if ( ($form_options[$fields["Field"]]["foption"] == "pgenum") && ($form_options[$fields["Field"]]["fpgenum"] != "") ) {
                $fields["Type"] = "enum(".$form_options[$fields["Field"]]["fpgenum"].")";
            }


            // textfelder
            if ( strstr($fields["Type"], "char")) {
                if ( strstr($form_options[$fields["Field"]]["foption"], "hidden") ) {
                    $type = "hidden";
                } elseif ( strstr($form_options[$fields["Field"]]["foption"], "password") ) {
                    $type = "password";
                    $form_values[$fields["Field"]]= "";
                } elseif ( strstr($form_options[$fields["Field"]]["foption"], "file") ) {
                    $type = "file";
                    $form_values[$fields["Field"]]= "";
                } else {
                    $type = "text";
                }
                ( $form_options[$fields["Field"]]["fsize"] > 0 ) ? $size = " size=\"".$form_options[$fields["Field"]]["fsize"]."\"" : $size = "";
                ( $form_options[$fields["Field"]]["fclass"] != "" ) ? $class = " class=\"".$form_options[$fields["Field"]]["fclass"]."\"" : $class = " class=\"".$defaults["form"]["textfield"]["class"]."\"";
                ( $form_options[$fields["Field"]]["fstyle"] != "" ) ? $style = " style=\"".$form_options[$fields["Field"]]["fstyle"]."\"" : $style = "";
                ( $form_values[$fields["Field"]] != "" ) ? $value = " value=\"".$form_values[$fields["Field"]]."\"" : $value = " value=\"".$fields["Default"]."\"";
                ( strstr($form_options[$fields["Field"]]["foption"], "readonly") ) ? $readonly = " readonly" : $readonly = "";
                $maxlength = strstr($fields["Type"],"(");
                $maxlength = str_replace("("," maxlength=\"",$maxlength);
                $maxlength = str_replace(")","\"",$maxlength);
                $formularobject = "<input type=\"".$type."\"".$size.$maxlength.$class.$style." name=\"".$fields["Field"].$extend."\" ".$value.$readonly.">\n";
                $element[$fields["Field"].$extend] = $formularobject;
            }
            // textfelder (mehrzeilig)
            if ( strstr($fields["Type"], "text")) {
                if ( $form_options[$fields["Field"]]["fsize"] != "" ) {
                    $col_row = explode(";",$form_options[$fields["Field"]]["fsize"],2);
                    $cols = " cols=\"".$col_row[0]."\"";
                    $rows = " rows=\"".$col_row[1]."\"";
                } else {
                    $cols = " cols=\"45\"";
                    $rows = " rows=\"5\"";
                }
                ( $form_options[$fields["Field"]]["fclass"] != "" ) ? $class = " class=\"".$form_options[$fields["Field"]]["fclass"]."\"" : $class = " class=\"".$defaults["form"]["textbox"]["class"]."\"";
                ( $form_options[$fields["Field"]]["fstyle"] != "" ) ? $style = " style=\"".$form_options[$fields["Field"]]["fstyle"]."\"" : $style = "";
                $formularobject = "<textarea".$cols.$rows.$class.$style." name=\"".$fields["Field"].$extend."\">".$form_values[$fields["Field"]]."</textarea>\n";
                $element[$fields["Field"].$extend] = $formularobject;
            }
            // checkbox, dropdown
            if ( strstr($fields["Type"], "enum(") ) {
                // get values
                $len = strlen($fields["Type"])-8;
                $options = substr($fields["Type"],6,$len);
                $options = explode("','", $options);
                // get labels
                $label = explode(";", $form_options[$fields["Field"]]["fwerte"]); #
                if ( count($options) == 1 ) {
                    if ( $form_values[$fields["Field"]] == $options[0] ) {
                        $checked = " checked";
                    } else {
                        $checked = "";
                    }
                    // hack: bei nicht auf "checked" gesetzten check boxen
                    // bleibt der post/get value leer
                    // der required check versagt, das feld kann nicht geaendert werden
                    $formularobject  = "<input type=\"hidden\" name=\"".$fields["Field"].$extend."\" value=\"\" class=\"hidden\" />\n";
                    $formularobject .= "<label for=\"".$fields["Field"]."\">".$options[0]."</label>\n";
                    $formularobject .= "<input type=\"checkbox\" name=\"".$fields["Field"].$extend."\" value=\"".$options[0]."\"".$checked." />\n";
                } elseif ( count($options) >= 4 ) {
                    ( $form_options[$fields["Field"]]["fsize"] > 0 ) ? $size = " size=\"".$form_options[$fields["Field"]]["fsize"]."\"" : $size = "1";
                    ( $form_options[$fields["Field"]]["fclass"] != "" ) ? $class = " class=\"".$form_options[$fields["Field"]]["fclass"]."\"" : $class = " class=\"".$defaults["form"]["dropdown"]["class"]."\"";
                    ( $form_options[$fields["Field"]]["fstyle"] != "" ) ? $style = " style=\"".$form_options[$fields["Field"]]["fstyle"]."\"" : $style = "";
                    // multiple koennte gehen, mommentan deaktiviert
                    ( strstr($form_options[$fields["Field"]]["opt"], "multiple") ) ? $multiple = " multiple" : $multiple = "";
                    ( strstr($form_options[$fields["Field"]]["opt"], "multiple") ) ? $isarray = "[]" : $isarray = "";
                    $formularobject = "<select name=\"".$fields["Field"].$extend.$isarray."\"".$size.$class.$style."".$multiple.">\n";
                    foreach( $options as $value ) {
                        if ( $form_values[$fields["Field"]] == $value ) {
                            $selected = " selected";
                        } else {
                            $selected = "";
                        }
                        if ( current($label) != "" ) {     #
                            $label_wert = current($label); #
                            next($label);                  #
                        } else {                           #
                            $label_wert = $value;          #
                        }                                  #
                        $formularobject .= "<option value=\"".$value."\"".$selected.">".$label_wert."</option>\n";
                    }
                    $formularobject .= "</select>\n";
                } else {
                    unset($formularobject);
                    // hack: bei nicht auf "checked" gesetzten radio buttons
                    // bleibt der post/get value leer
                    // der required check versagt!
                    $formularobject .= "<input type=\"hidden\" name=\"".$fields["Field"].$extend."\" value=\"".$form_values[$fields["Field"]]."\" class=\"hidden\" />\n";
                    foreach( $options as $value ) {
                        if ( $form_values[$fields["Field"]] == $value ) {
                            $checked = " checked";
                        } else {
                            $checked = "";
                        }
                        if ( current($label) != "" ) {     #
                            $label_wert = current($label); #
                            next($label);                  #
                        } else {                           #
                            $label_wert = $value;          #
                        }                                  #
                        $formularobject .= "<label for=\"".$fields["Field"]."\">".$label_wert."</label>\n";
                        $formularobject .= "<input type=\"radio\" name=\"".$fields["Field"].$extend."\" value=\"".$value."\"".$checked." />\n";
                    }
                    $element[$fields["Field"].$extend] = $formularobject;
                }
                $element[$fields["Field"].$extend] = $formularobject;
            // datetime (timestamp)
            } elseif ( strstr($fields["Type"], "datetime")) {
                $preg = "^([0-9]{4})\-([0-9]{2})\-([0-9]{2})";
                #$formularobject = date("d.m.Y G:i:s");
                #$element[$fields["Field"]] = $formularobject;
                if ( $form_values[$fields["Field"]] == "" ) {
                    $form_values[$fields["Field"]] = date("d.m.Y G:i:s");
                } elseif ( substr($form_values[$fields["Field"]],2,1) != "." ) {
                    $convert = $form_values[$fields["Field"]];
                    $form_values[$fields["Field"]] = substr($convert,8,2).".".substr($convert,5,2).".".substr($convert,0,4)." ".substr($convert,11,9);
                } else {

                }

                ( $form_options[$fields["Field"]]["fsize"] > 0 ) ? $size = " size=\"".$form_options[$fields["Field"]]["fsize"]."\"" : $size = "";
                ( $form_options[$fields["Field"]]["fclass"] != "" ) ? $class = " class=\"".$form_options[$fields["Field"]]["fclass"]."\"" : $class = " class=\"".$defaults["form"]["date"]["class"]."\"";;
                ( $form_options[$fields["Field"]]["fstyle"] != "" ) ? $style = " style=\"".$form_options[$fields["Field"]]["fstyle"]."\"" : $style = "";
                ( strstr($form_options[$fields["Field"]]["foption"], "readonly") ) ? $readonly = " readonly" : $readonly = "";
                ( strstr($form_options[$fields["Field"]]["foption"], "hidden") ) ? $type = "hidden" : $type = "";
                $maxlength = strstr($fields["Type"],"(");
                $maxlength = str_replace("("," maxlength=\"",$maxlength);
                $maxlength = str_replace(")","\"",$maxlength);
                $formularobject = "<input type=\"".$type."\"".$size.$maxlength.$class.$style." name=\"".$fields["Field"].$extend."\" value=\"".$form_values[$fields["Field"]]."\"".$readonly.">\n";
                $element[$fields["Field"].$extend] = $formularobject;
            // date
            } elseif ( strstr($fields["Type"], "date")) {
                $preg = "^([0-9]{4})\-([0-9]{2})\-([0-9]{2})";
                if ( $form_values[$fields["Field"]] == "" ) {
                    if ( $fields["Default"] != "" ) {
                        $form_values[$fields["Field"]] = $fields["Default"];
                    } else {
                        $form_values[$fields["Field"]] = date("d.m.Y");
                    }
                } elseif (preg_match_all("/$preg/",$form_values[$fields["Field"]],$regs)) {
                #} elseif ( substr($form_values[$fields["Field"]],2,1) != "." ) {
                    $convert = $form_values[$fields["Field"]];
                    $form_values[$fields["Field"]] = substr($convert,8,2).".".substr($convert,5,2).".".substr($convert,0,4);

                } else {

                }

                ( $form_options[$fields["Field"]]["fsize"] > 0 ) ? $size = " size=\"".$form_options[$fields["Field"]]["fsize"]."\"" : $size = "";
                ( $form_options[$fields["Field"]]["fclass"] != "" ) ? $class = " class=\"".$form_options[$fields["Field"]]["fclass"]."\"" : $class = " class=\"".$defaults["form"]["date"]["class"]."\"";;
                ( $form_options[$fields["Field"]]["fstyle"] != "" ) ? $style = " style=\"".$form_options[$fields["Field"]]["fstyle"]."\"" : $style = "";
                ( strstr($form_options[$fields["Field"]]["foption"], "readonly") ) ? $readonly = " readonly" : $readonly = "";
                $maxlength = strstr($fields["Type"],"(");
                $maxlength = str_replace("("," maxlength=\"",$maxlength);
                $maxlength = str_replace(")","\"",$maxlength);
                $formularobject = "<input type=\"".$type."\"".$size.$maxlength.$class.$style." name=\"".$fields["Field"].$extend."\" value=\"".$form_values[$fields["Field"]]."\"".$readonly.">\n";
                $element[$fields["Field"].$extend] = $formularobject;
            // id feld
            } elseif ( strstr($fields["Type"], "int")) {
                if ( strstr($form_options[$fields["Field"]]["foption"], "hidden") ) {
                    $type = "hidden";
                } else {
                    $type = "text";
                }
                ( $form_options[$fields["Field"]]["fsize"] > 0 ) ? $size = " size=\"".$form_options[$fields["Field"]]["fsize"]."\"" : $size = "";
                ( $form_options[$fields["Field"]]["fclass"] != "" ) ? $class = " class=\"".$form_options[$fields["Field"]]["fclass"]."\"" : $class = " class=\"".$defaults["form"]["int"]["class"]."\"";
                ( $form_options[$fields["Field"]]["fstyle"] != "" ) ? $style = " style=\"".$form_options[$fields["Field"]]["fstyle"]."\"" : $style = "";
                ( $form_values[$fields["Field"]] != "" ) ? $value = " value=\"".$form_values[$fields["Field"]]."\"" : $value = " value=\"".$fields["Default"]."\"";
                ( strstr($form_options[$fields["Field"]]["foption"], "readonly") ) ? $readonly = " readonly" : $readonly = "";
                $maxlength = strstr($fields["Type"],"(");
                $maxlength = str_replace("("," maxlength=\"",$maxlength);
                $maxlength = str_replace(")","\"",$maxlength);
                $formularobject = "<input type=\"".$type."\"".$size.$maxlength.$class.$style." name=\"".$fields["Field"].$extend."\" ".$value.$readonly.">\n";
                $element[$fields["Field"].$extend] = $formularobject;
            }
        }
        return $element;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
