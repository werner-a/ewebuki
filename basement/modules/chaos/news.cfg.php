<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "news.cfg.php";
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

    // einstellungen uebersicht
    $environment[news_uebersicht_aktiv] = "-1"; # 0 / -1
    $environment[news_uebersicht_katid] = "index";
    $environment[news_uebersicht_pre_inh] = "inh_2";
    $environment[news_uebersicht_pre_len] = "120";
    $environment[news_scheme] = "ausgabe";  # quartal, ausgabe
    $environment[news_ausgabe] = "2003-02";

    // konstanten fuer beitraege initialisieren
    define ('BEITRAG_KOPF', 'news_header');
    define ('BEITRAG_INHALT', 'news_content');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
