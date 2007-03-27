<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "upload manager";
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

  $environment[basis] = $pathvars[virtual]."/upload";

    if ( $environment[parameter][1] == "check" ) {

    $valid = array("zip", "pdf", "html");
    $file = file_verarbeitung("/my/upload", "upload1", 2000000, $valid);
        $ausgaben[output] .= "Filename: ".$file[name]."<br><br>";

    $error = $file[returncode];
    if ( $error == 0 ) {
        $ausgaben[output] .= "There is no error, the file uploaded with success.";
    } elseif ( $error == 1 ) {
        $ausgaben[output] .= "The uploaded file exceeds the upload_max_filesize directive ( ".get_cfg_var(upload_max_filesize)." ) in php.ini.";
    } elseif ( $error == 2 ) {
        $ausgaben[output] .= "The uploaded file exceeds the MAX_FILE_SIZE ( ".$HTTP_POST_VARS[MAX_FILE_SIZE]."kb ) directive that was specified in the html form.";
    } elseif ( $error == 3 ) {
        $ausgaben[output] .= "The uploaded file was only partially uploaded.";
    } elseif ( $error == 4 ) {
        $ausgaben[output] .= "No file was uploaded.";
    } elseif ( $error == 7 ) {
        $ausgaben[output] .= "File Size to big.";
    } elseif ( $error == 8 ) {
        $ausgaben[output] .= "File Type not valid.";
    } elseif ( $error == 9 ) {
        $ausgaben[output] .= "File Name already exists.";
    } elseif ( $error == 10 ) {
        $ausgaben[output] .= "Unknown Error. Maybe post_max_size directive ( ".get_cfg_var(post_max_size)." ) in php.ini. Please do not try again.";
    } elseif ( $error == 11 ) {
        $ausgaben[output] .= "Sorry, you need minimal PHP/4.x.x to handle uploads!";
    }

  } else {

    $ausgaben[output]  ="<h1>Hallo</h1>";
    $ausgaben[output] .="<form action=\"".$environment[basis].",check.html\" method=\"post\" enctype=\"multipart/form-data\">";
    #$ausgaben[output] .="<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"200000000\">";
    $ausgaben[output] .="<input type=\"file\" name=\"upload1\">";
    $ausgaben[output] .="<input type=\"submit\" value=\"los\">";
    $ausgaben[output] .="</form>";

  }

  if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ++ $script_name ++ ]".$debugging[char];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
