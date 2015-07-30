<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menued.cfg.php-dist 779 2007-09-14 09:53:39Z chaot $";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2011 Werner Ammon ( wa<at>chaos.de )

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
////+///////+///////+///////+///////+///////+///////+///////////////////////////////////////////////////////////

    // content umschaltung verhindern
    $specialvars["dynlock"] = True;

    $cfg["menued"] = array(
           "subdir" => "admin",
             "name" => "menued",
             "real" => "menued2",
            "basis" => $pathvars["virtual"]."/admin/menued",
         "iconpath" => "", # leer: /images/default/; automatik: $pathvars["images"]
           "design" => "modern",
 "design_available" => array("modern","classic"),
         "function" => array(
              "list,shared" => array("menutree"),
                      "list"=> array("locate","make_ebene"),
                      "add" => array("make_ebene", "black_list"),
                     "edit" => array("make_ebene", "update_tname", "black_list"),
                   "delete" => array("make_ebene"),
                     "sort" => array("sitemap", "renumber","make_ebene"),
              "move,shared" => array("menutree"),
                     "move" => array("locate", "make_ebene", "update_tname"),
                   "rights" => array("make_ebene"),
                       ),
               "db" => array(
                   "change" => 0,
                     "menu" => array(
                          "entries" => "site_menu",
                              "key" => "mid",
                            "order" => "sort, label",
                               ),
                     "lang" => array(
                          "entries" => "site_menu_lang",
                              "key" => "mlid",
                            "order" => "lang",
                               ),
                     "text" => array(
                          "entries" => "site_text",
                               ),
                     "content" => array(
                          "entries" => "auth_content",
                               ),
                     "user" => array(
                          "entries" => "auth_user",
                              "key" => "uid",
                            "order" => "nachname,vorname",
                               ),
                    "level" => array(
                          "entries" => "auth_level",
                              "key" => "lid",
                         "levelkey" => "level",
                               ),
                    "right" => array(
                          "entries" => "auth_right",
                          "userkey" => "uid",
                         "levelkey" => "lid",
                               ),
                  "special" => array(
                          "entries" => "auth_special",
                          "userkey" => "suid",
                       "contentkey" => "content",
                         "dbasekey" => "sdb",
                         "tnamekey" => "stname"
                               ),
                       ),
            "modify"=> array(
                "sort"      => array("","#(button_desc_sort)", $cfg["auth"]["menu"]["menued"][1]), # edit
                "jump"      => array("", "#(button_desc_jump)", $cfg["auth"]["menu"]["menued"][1]), # edit
                "add"       => array("", "#(button_desc_add)", $cfg["auth"]["menu"]["menued"][1]), # add
                "edit"      => array("", "#(button_desc_edit)", $cfg["auth"]["menu"]["menued"][1]), # edit
                "delete"    => array("", "#(button_desc_delete)", $cfg["auth"]["menu"]["menued"][1]), # admin
                "up"        => array("sort,", "#(button_desc_up)", $cfg["auth"]["menu"]["menued"][1]), # admin
                "down"      => array("sort,", "#(button_desc_down)", $cfg["auth"]["menu"]["menued"][1]), # admin
                "move"      => array("", "#(button_desc_move)", $cfg["auth"]["menu"]["menued"][1]), # admin
                "rights"    => array("", "#(button_desc_right)", $cfg["auth"]["menu"]["menued"][1]) # admin
                            ),
        "black_list"=> array(
                    "entry" => array("view", "new", "history"),
                    "url"   => array(
                                        "/admin/bloged",
                                        "/admin/contented",
                                        "/admin/grouped",
                                        "/admin/righted",
                                        "/admin/leveled",
                                        "/admin/usered",
                                        "/admin/menued",
                                        "/admin/fileed",
                                        "/admin/passed",
                                    ),
                            ),
            "fqdn0" => "www",
      "right_admin" => $cfg["auth"]["menu"]["menued"][1],
            "right" => "",
	
	# see also rights at $cfg["menued"]["modify"] up in this file
    );

////+///////+///////+///////+///////+///////+///////+///////////////////////////////////////////////////////////
?>
