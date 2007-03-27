<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "fli4l datenerfassung";
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

    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ** $script_name ** ]".$debugging[char];

        // konfiguration
        define ('TABLE', 'router');
        #require $pathvars[config]."addon/fli4l.datenerfassung.cfg.php";


        // affen form bauen
        $sql = "SELECT * FROM site_form LEFT JOIN site_form_lang ON site_form.fid = site_form_lang.fid WHERE ( site_form.tname = 'fli4l.datenerfassung' ) AND ( site_form_lang.lang = 'ger' or site_form_lang.lang Is Null )";
        $result = $db -> query($sql);
        while ( $site_form = $db -> fetch_array($result,$nop) ) {
            $form_options[$site_form[label]] = $site_form;
        }

        $sql = "SHOW COLUMNS FROM ". TABLE;
        $result = $db -> query($sql);
        while ( $fields = $db -> fetch_array($result,$nop) ) {
            if ( strstr($fields[Type], "char")) {
                ( strstr($form_options[$fields[Field]][opt], "password") ) ? $type = "password" : $type = "text";
                ( $form_options[$fields[Field]][size] > 0 ) ? $size = " size=\"".$form_options[$fields[Field]][size]."\"" : $size = "";
                ( $form_options[$fields[Field]]["class"] != "" ) ? $class = " class=\"".$form_options[$fields[Field]]["class"]."\"" : $class = "";
                ( $form_options[$fields[Field]][style] != "" ) ? $style = " style=\"".$form_options[$fields[Field]][style]."\"" : $style = "";
                ( strstr($form_options[$fields[Field]][opt], "readonly") ) ? $readonly = " readonly" : $readonly = "";
                $maxlength = strstr($fields[Type],"(");
                $maxlength = str_replace("("," maxlength=\"",$maxlength);
                $maxlength = str_replace(")","\"",$maxlength);
                $formularobject = "<input type=\"".$type."\"".$size.$maxlength.$class.$style." name=\"".$fields[Field]."\" value=\"".$HTTP_POST_VARS[$fields[Field]]."\"".$readonly.">\n";
                $element[$fields[Field]] = $formularobject;
            }
            if ( strstr($fields[Type], "text")) {
                if ( $form_options[$fields[Field]][size] != "" ) {
                    $col_row = explode(";",$form_options[$fields[Field]][size],2);
                    $cols = " cols=\"".$col_row[0]."\"";
                    $rows = " rows=\"".$col_row[1]."\"";
                } else {
                    $rows = "60";
                    $rows = "20";
                }
                ( $form_options[$fields[Field]]["class"] != "" ) ? $class = " class=\"".$form_options[$fields[Field]]["class"]."\"" : $class = "";
                ( $form_options[$fields[Field]][style] != "" ) ? $style = " style=\"".$form_options[$fields[Field]][style]."\"" : $style = "";
                $formularobject = "<textarea".$cols.$rows.$class.$style." name=\"".$fields[Field]."\">".$HTTP_POST_VARS[$fields[Field]]."</textarea>\n";
                $element[$fields[Field]] = $formularobject;
            }
            if ( strstr($fields[Type], "enum(")) {
                $len = strlen($fields[Type])-8;
                $options = substr($fields[Type],6,$len);
                $options = explode("','", $options);
                $label = explode(";", $form_options[$fields[Field]][werte]); #
                if ( count($options) == 1 ) {
                    if ( $HTTP_POST_VARS[$fields[Field]] == $options[0] ) {
                        $checked = " checked";
                    } else {
                        $checked = "";
                    }
                    $formularobject = "<input type=\"checkbox\" name=\"".$fields[Field]."\" value=\"".$options[0]."\"".$checked.">\n";
                } elseif ( count($options) >= 4 ) {
                    ( $form_options[$fields[Field]][size] > 0 ) ? $size = " size=\"".$form_options[$fields[Field]][size]."\"" : $size = "1";
                    ( $form_options[$fields[Field]]["class"] != "" ) ? $class = " class=\"".$form_options[$fields[Field]]["class"]."\"" : $class = "";
                    ( $form_options[$fields[Field]][style] != "" ) ? $style = " style=\"".$form_options[$fields[Field]][style]."\"" : $style = "";
                    // multiple koennte gehen, mommentan deaktiviert
                    #( strstr($form_options[$fields[Field]][opt], "multiple") ) ? $multiple = " multiple" : $multiple = "";
                    #( strstr($form_options[$fields[Field]][opt], "multiple") ) ? $isarray = "[]" : $isarray = "";
                    $formularobject = "<select name=\"".$fields[Field].$isarray."\"".$size.$class.$style."".$multiple.">\n";
                    foreach( $options as $value ) {
                        if ( $HTTP_POST_VARS[$fields[Field]] == $value ) {
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
                    foreach( $options as $value ) {
                        if ( $HTTP_POST_VARS[$fields[Field]] == $value ) {
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
                        $formularobject .= "<input type=\"radio\" name=\"".$fields[Field]."\" value=\"".$value."\"".$checked.">".$label_wert." \n";
                    }
                        $element[$fields[Field]] = $formularobject;
                }
                $element[$fields[Field]] = $formularobject;
            }
        }



        // error meldung init
        $ausgaben[fli4l_dae_error] = "";

        if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(1) funktion: ".$environment[subparam][1].$debugging[char];
        if ( $environment[subparam][1] == "save" ) {
            if ( get_cfg_var('register_globals') == 1 ) $debugging[ausgabe] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging[char];

            // post vars pruefen
            foreach($form_options as $name => $value) {
                if ( $value[required] == -1 ) {
                    if ( $value[check] == "" ) {
                        if ( $value[check] == $HTTP_POST_VARS[$name] ) {
                           $ausgaben[fli4l_dae_error] .= $name." failure, ";
                        }
                    }
                } else {

                }
            }

            // sql bauen
            array_splice($HTTP_POST_VARS, count($HTTP_POST_VARS)-1);
            foreach($HTTP_POST_VARS as $name => $value) {
                if ( $sqla != "" ) $sqla .= ",";
                $sqla .= " ".$name;
                if ( $sqlb != "" ) $sqlb .= ",";
                $sqlb .= " '".$value."'";
            }
            if ( $ausgaben[fli4l_dae_error] != "" ) {
                $mapping[main] = "fli4l.datenerfassung.form";
                $ausgaben[fli4l_dae_aktion] = $pathvars[virtual]."/fli4l/datenerfassung,save.html";
            } else {
                $sql = "insert into ". TABLE ." (".$sqla.") VALUES (".$sqlb.")";
                $result  = $db -> query($sql);
                if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];

                $ausgaben[output] = "Verarbeitung Ihrer Daten ";
                if ($result) {
                    $ausgaben[output] .= "... mit Erfolg abgeschlossen.";
                } else {
                    $ausgaben[output] .= "... konnte nicht abgeschlossen werden.";
                }
            }
        } else {
            $mapping[main] = "fli4l.datenerfassung.form";
            $ausgaben[fli4l_dae_aktion] = $pathvars[virtual]."/fli4l/datenerfassung,save.html";
        }

    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ++ $script_name ++ ]".$debugging[char];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
