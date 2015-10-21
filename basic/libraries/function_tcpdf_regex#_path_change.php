<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_tcpdf_path_change.inc.php v0 chaot
// TCPDF connector regex extender: path_change
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

/*
    ** autoloader: remove the # in the filename to enable it ***
*/

    //[IMG=/file/picture/medium/img_10.jpg;;0;b]Wolkenblick[/IMG]
    #$suchmuster = '~src="/file/(jpg|png|gif)/(\d+)/(b|m|s|o|tn)/.+"~';
    #$ersetzung = 'src="/file/picture/$3/img_${2}.${1}"';
    #$buffer = preg_replace($suchmuster, $ersetzung, $buffer);

    // pfad korrektur experimente:
    
    $s = '~src="/file/(jpg|png|gif)/(\d+)/tn/.+"~';
    $r = 'src="/file/picture/thumbnail/tn_${2}.${1}"';
    $buffer = preg_replace($s, $r, $buffer);

    $s = '~src="/file/(jpg|png|gif)/(\d+)/s/.+"~';
    $r = 'src="/file/picture/small/img_${2}.${1}"';
    $buffer = preg_replace($s, $r, $buffer);

    $s = '~src="/file/(jpg|png|gif)/(\d+)/m/.+"~';
    $r = 'src="/file/picture/medium/img_${2}.${1}"';
    $buffer = preg_replace($s, $r, $buffer);

    $s = '~src="/file/(jpg|png|gif)/(\d+)/b/.+"~';
    $r = 'src="/file/picture/big/img_${2}.${1}"';
    $buffer = preg_replace($s, $r, $buffer);

    $s = '~src="/file/(jpg|png|gif)/(\d+)/o/.+"~';
    $r = 'src="/file/picture/original/img_${2}.${1}"';
    $buffer = preg_replace($s, $r, $buffer);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
