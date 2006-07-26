<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "content editor erstellen";
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

    function makece($ce_formname, $ce_name, $ce_inhalt) {
        global $db, $pathvars, $ausgaben, $extension, $specialvars, $defaults;
        $ausgaben["ce_name"] = $ce_name;

        // vogelwilde regex die viel arbeit erspart hat
        preg_match_all("/_([0-9]*)\./",$ce_inhalt,$found);
        #$array = array_merge(explode(";",$fileid),$found[1]);

        // file memo auslesen und zuruecksetzen
        global $HTTP_SESSION_VARS;
        session_register("images_memo");
        if ( is_array($HTTP_SESSION_VARS["images_memo"]) ) {
            $array = array_merge($HTTP_SESSION_VARS["images_memo"],$found[1]);
            unset($HTTP_SESSION_VARS["images_memo"]);
        } else {
            $array = $found[1];
        }

        // wenn es thumbnails gibt, anzeigen
        if ( count($array) >= 1 ) {
            $merken = $db -> getDb();
            if ( $merken != DATABASE ) {
                $db -> selectDB( DATABASE ,"");
            }
            foreach ( $array as $value ) {
                if ( $where != "" ) $where .= " OR ";
                $where .= "fid = '".$value."'";
            }
            $sql = "SELECT * FROM site_file WHERE ".$where." ORDER BY fdesc";
            $result = $db -> query($sql);
            if ( $merken != DATABASE ) {
                $db -> selectDB($merken,"");
            }
            $extension= "";

            $tn = "<table><tr><td>";
            while ( $data = $db -> fetch_array($result, NOP) ) {
                #$file[$data["fid"]] = array(
                #                        "fart"  =>  $data["ffart"],
                #                        "fdesc" =>  $data["fdesc"],
                #                        );

                switch ( $data["ffart"] ) {
                    case "pdf":
                        // die boese schneide ab funktion
                        if ( strlen($data["fdesc"]) > 10 ) {
                            $fdesc = substr($data["fdesc"],0,10)." ...";
                        } else {
                            $fdesc = $data["fdesc"];
                        }
                        #$tn .= "<a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img src=\"".$pathvars["images"]."pdf.png"."\"></a> ";
                        $tn .= "<table align=\"left\" width=\"96\">";
                        $tn .= "<tr><td><a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\">".$fdesc."</a></td></tr>";

                        if ( $defaults["icon"]["pdf"] == "" ) $defaults["icon"]["pdf"] = "<img src=\"/images/default/icon_pdf.png\" />";
                        $tn .= "<tr><td>".$defaults["icon"]["pdf"]."</td></tr>";
                        $tn .= "</table>";

                        $extension .= "else if (st=='doc".$data["fid"]."')\nst='[LINK=".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["doc"]."doc_".$data["fid"].".".$data["ffart"]."]".$data["fdesc"]."[/LINK]';";
                        break;
                    case "zip":
                        // die boese schneide ab funktion
                        if ( strlen($data["fdesc"]) > 10 ) {
                            $fdesc = substr($data["fdesc"],0,10)." ...";
                        } else {
                            $fdesc = $data["fdesc"];
                        }
                        #$tn .= "<a href=\"#\" onclick=\"INSst('doc".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img src=\"".$pathvars["images"]."pdf.png"."\"></a> ";
                        $tn .= "<table align=\"left\" width=\"96\">";
                        $tn .= "<tr><td><a href=\"#\" onclick=\"INSst('arc".$data["fid"]."','".$ce_formname."','".$ce_name."')\">".$fdesc."</a></td></tr>";

                        if ( $defaults["icon"]["zip"] == "" ) $defaults["icon"]["zip"] = "<img src=\"/images/default/icon_zip.png\" />";
                        $tn .= "<tr><td>".$defaults["icon"]["zip"]."</td></tr>";
                        $tn .= "</table>";

                        $extension .= "else if (st=='arc".$data["fid"]."')\nst='[LINK=".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["arc"]."arc_".$data["fid"].".".$data["ffart"]."]".$data["fdesc"]."[/LINK]';";
                        break;
                    default:
                        $imgsize = getimagesize($pathvars["filebase"]["maindir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."tn_".$data["fid"].".".$data["ffart"]);
                        $imgsize = " ".$imgsize[3];
                        #$tn .= "<a href=\"#\" onclick=\"INSst('imb".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img src=\"/dateien/bilder/thumbnail/tn_".$data["fid"].".".$data["ffart"]."\"></a> ";
                        $tn .= "<table align=\"left\">";
                        $tn .= "<tr><td><a href=\"#\" onclick=\"INSst('imo".$data["fid"]."','".$ce_formname."','".$ce_name."')\">Org</a> ";
                        $tn .= "<a href=\"#\" onclick=\"INSst('imb".$data["fid"]."','".$ce_formname."','".$ce_name."')\">Big</a> ";
                        $tn .= "<a href=\"#\" onclick=\"INSst('imm".$data["fid"]."','".$ce_formname."','".$ce_name."')\">Mid</a> ";
                        $tn .= "<a href=\"#\" onclick=\"INSst('ims".$data["fid"]."','".$ce_formname."','".$ce_name."')\">Sma</a></td></tr>";

                        $tn .= "<tr><td><a href=\"#\" onclick=\"INSst('imo".$data["fid"]."','".$ce_formname."','".$ce_name."')\"><img".$imgsize." border=\"0\" src=\"".$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["tn"]."tn_".$data["fid"].".".$data["ffart"]."\" alt=\"id:".$data["fid"].", .".$data["ffart"]."\" title=\"id:".$data["fid"].", .".$data["ffart"]."\"></a></td></tr>";
                        $tn .= "</table>";

                        if ( $defaults["cms-tag"]["grafik"] == "" ) {
                            $defaults["cms-tag"]["grafik"] = "[IMG=";
                            $defaults["cms-tag"][",grafik"] = "";
                            $defaults["cms-tag"]["/grafik"] = "[/IMG]";
                        }
                        $extension .= "else if (st=='imo".$data["fid"]."')\nst='".$defaults["cms-tag"]["grafik"].$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["o"]."img_".$data["fid"].".".$data["ffart"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                        $extension .= "else if (st=='imb".$data["fid"]."')\nst='".$defaults["cms-tag"]["grafik"].$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["b"]."img_".$data["fid"].".".$data["ffart"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                        $extension .= "else if (st=='imm".$data["fid"]."')\nst='".$defaults["cms-tag"]["grafik"].$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["m"]."img_".$data["fid"].".".$data["ffart"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                        $extension .= "else if (st=='ims".$data["fid"]."')\nst='".$defaults["cms-tag"]["grafik"].$pathvars["filebase"]["webdir"].$pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"]["s"]."img_".$data["fid"].".".$data["ffart"].$defaults["cms-tag"][",grafik"]."]".$data["funder"].$defaults["cms-tag"]["/grafik"]."';";
                }
                $i++;
                $a = $i / 6;
                if ( is_int($a) ) $tn .="</td></tr><tr><td>";
            }
            $tn .= "</td></tr></table>";
        }

        // path fuer alle schaltflaechen anpassen
        if ( $defaults["cms-tag"]["path"] == "" ) $defaults["cms-tag"]["path"] = "/images/default/";

        $danei ='[TAB=l;300]\n[ROW]\n[COL]1,1[\/COL]\n[COL]1,2[\/COL]\n[COL]1,3[\/COL]\n[\/ROW][ROW]\n[COL]2,1[\/COL]\n[COL]2,2[\/COL]\n[COL]2,3[\/COL]\n[\/ROW]\n[\/TAB]';

        $extension .= "else if (st=='tabb')\nst='".$danei."';";
        $tn .= "<a href=\"#\" onclick=\"INSst('tabb','".$ce_formname."','".$ce_name."')\">Tabellen Beispiel</a>";


        $ausgaben["ce_script"] = parser("cms.edit-js","");
        $cetag = array( // physisch
                        "b"      => array( "1", "1", "#(b)"),
                        "i"      => array( "1",  "1", "#(i)"),
                        "tt"     => array( "1",  "",  "#(tt)"),
                        "u"      => array( "1",  "",  "#(u)"),
                        "s"      => array( "1",  "",  "#(s)"),
                        "st"     => array( "0",  "",  "#(st)"),
                        "big"    => array( "1",  "",  "#(big)"),
                        "small"  => array( "1",  "",  "#(small)"),
                        "sup"    => array( "1",  "",  "#(sup)"),
                        "sub"    => array( "1",  "",  "#(sub)"),

                        // logisch
                        "em"     => array( "1",  "",  "#(em)"),
                        "strong" => array( "1",  "",  "#(strong)"),
                        "cite"   => array( "1",  "",  "#(cite)"),

                        // allgemein
                        "div"    => array( "1",  "", "#(div)"),
                        "center" => array( "1",  "1", "#(center)"),
                        "quote"  => array( "1",  "",  "#(quote)"),
                        "pre"    => array( "1",  "",  "#(pre)"),

                        // ueberschrrift, absatz, umbruch, trennlinie
                        "h1"     => array( "1",  "1", "#(h1)"),
                        "h2"     => array( "1",  "1", "#(h2)"),
                        "p"      => array( "1",  "1", "#(p)"),
                        "hr"     => array( "1",  "", "#(hr)"),
                        "sp"     => array( "1",  "",  "#(sp)"),
                        "br"     => array( "1",  "",  "#(br)"),

                        // spezielle
                        "hl"     => array( "1",  "1", "#(hl)"),
                        "in"     => array( "1",  "",  "#(in)"),
                        "m1"     => array( "1",  "",  "#(m1)"),
                        "m2"     => array( "1",  "",  "#(m2)"),
                        "up"     => array( "1",  "",  "#(up)"),

                        // erweiterte
                        "list"   => array( "1",  "1", "#(list)"),
                        "img"    => array( "1",  "",  "#(img)"),
                        "imgb"   => array( "1",  "",  "#(imgb)"),
                        "link"   => array( "1",  "1", "#(link)"),
                        "email"  => array( "1",  "1", "#(email)"),
                        "tab"    => array( "1",  "1", "#(tab)"),
                        "row"    => array( "1",  "1", "#(row)"),
                        "col"    => array( "1",  "1", "#(col)"),

                        // alt
                        "int"    => array( "",   "",  "#(int)"),
                      );

        $ausgaben["ce_dropdown"]  = "<select style=\"width:95px;font-family:Helvetica, Verdana, Arial, sans-serif;font-size:12px;\" name=\"st\" size=\"1\" onChange=\"INSst(this.options[this.selectedIndex].value,'".$ce_formname."','".$ce_name."');this.selectedIndex=0;\">";
        $ausgaben["ce_dropdown"] .= "<option value=\"\">#(tagselect)</option>";
        foreach( $cetag as $key => $value ) {
            if ( $value[1] == 1 ) {
                $ausgaben["ce_button"] .= "<a href=\"#\" onclick=\"INSst('".$key."','".$ce_formname."','".$ce_name."')\" onMouseOver=\"status='".$value[2]."';return true;\" onMouseOut=\"status='';return true;\"><img src=\"".$defaults["cms-tag"]["path"]."cms-tag-".$key.".png\" alt=\"".$value[2]."\" title=\"".$value[2]."\" width=\"23\" height=\"22\" border=\"0\" /></a>\n ";
            }
            if ( $value[0] == 1 ) {
                $ausgaben["ce_dropdown"] .= "<option value=\"".$key."\">".$value[2]."</option>\n";
            }
            #ce_anker
        }
        $ausgaben["ce_dropdown"] .= "</select>";
        $ausgaben["ce_button"] .= "<input name=\"add[]\" type=\"image\" id=\"image\" value=\"add\" src=\"".$defaults["cms-tag"]["path"]."cms-tag-imgb.png\" title=\"#(add)\" width=\"23\" height=\"22\">";

        $ausgaben["ce_upload"] .= "<select style=\"width:95px;font-family:Helvetica, Verdana, Arial, sans-serif;font-size:12px;\" name=\"upload\" onChange=\"submit()\">";
        $ausgaben["ce_upload"] .= "<option value=\"\">#(upload)</option>";
        $ausgaben["ce_upload"] .= "<option value=\"1\">1 #(file)</option>";
        $ausgaben["ce_upload"] .= "<option value=\"2\">2 #(files)</option>";
        $ausgaben["ce_upload"] .= "<option value=\"3\">3 #(files)</option>";
        $ausgaben["ce_upload"] .= "<option value=\"4\">4 #(files)</option>";
        $ausgaben["ce_upload"] .= "<option value=\"5\">5 #(files)</option>";
        $ausgaben["ce_upload"] .= "</select>";

        $ausgaben["ce_inhalt"] = $ce_inhalt;
        $ausgaben["ce_eventh"] = "onKeyDown=\"zaehler();\" onSelect=\"chk('content',500);\"";
        return $tn;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
