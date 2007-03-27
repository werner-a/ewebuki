<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "fli4l best_of";
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
    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(1) Anfangsparameter: ".$environment[subparam][1].$debugging[char];
    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(2) Suchtext: ".$environment[subparam][2].$debugging[char];
    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "(3) alles: ".$environment[subparam][3].$debugging[char];

//
/// Liste anzeigen
//
    if ($environment[subparam][1] == "list" || $environment[subparam][1] == "")
    {

        $Anfangsposition = $environment[subparam][2];
        if ($HTTP_POST_VARS[suchtext]!="")
        {
            $suchtext = $HTTP_POST_VARS[suchtext];
        } else
        {
            $suchtext = $environment[subparam][3];
        }

        $alles = $HTTP_POST_VARS[alles];


        if ($alles == "Alles anzeigen")
        {
        $suchtext="";
        }

        if ($suchtext !="")
        {
        $suchwoerter=explode(" ", $suchtext);
        $suchtext=$suchwoerter[0];
        $bedingung="substring(nick,1,5) != 'pre__' and text like '%" . $suchtext . "%'";
        } else
        {
        $bedingung="substring(nick,1,5) != 'pre__'";
        }

        $Zeilen_pro_Seite = 3;
        if ($Anfangsposition == "") {
           $Anfangsposition = 0;
        }

        $sql="select vom from bestof_fli4l where " . $bedingung . " order by vom ";
        $result1 = $db -> query($sql);
        $Anzahl=$db -> num_rows($result1);

        $sql="select vom, bis, nick, mail, text from bestof_fli4l where " . $bedingung . " order by vom desc ".
         "limit $Anfangsposition,$Zeilen_pro_Seite";
        $result = $db -> query($sql);

    //
    /// Kopf
    //
        $ausgaben[output]  = "Best of <a href=\"http://www.fli4l.de/german/irc.htm\">#fli4l</a>";
        $ausgaben[output] .= " / <a href=\"irc://main.freechat-network.de:6667/fli4l\">mitchatten</a>";
        $ausgaben[output] .= " (einen Mitschnitt / Log kann man <a href=\"" . $pathvars[virtual] . "/fli4l/best_of,eintrag.html\">hier</a> eintragen)</p>";
    //
    /// Suche
    //
        $ausgaben[output] .= "<form method=\"POST\" action=\"" . $pathvars[virtual] . "/fli4l/best_of,list.html\">";
        $ausgaben[output] .= "<table border=\"0\" cellspacing=\"4\" cellpadding=\"3\">";
        $ausgaben[output] .= "<tr>";
        $ausgaben[output] .= "<td>Volltextsuche:</td>";
        $ausgaben[output] .= "<td>nach</td><td><input type=\"text\" name=\"suchtext\" size=\"40\"></td>";
        $ausgaben[output] .= "<td>&nbsp;<input type=\"submit\" value=\"Suchen\" name=\"suchen\">";
        if ($suchtext!="")
        {
            $ausgaben[output] .= "<input type=\"submit\" value=\"Alles anzeigen\" name=\"alles\">";
        }
        $ausgaben[output] .= "</td>";
        $ausgaben[output] .= "</tr>";
        if ($suchtext!="")
        {
            $ausgaben[output] .= "<tr>";
            $ausgaben[output] .= "<td colspan=\"3\">Die Suche nach \"" . $suchtext . "\" ergab " . $Anzahl . " Treffer.</td>";
            $ausgaben[output] .= "</tr>";
        }
        $ausgaben[output] .= "</table></form><hr>";

        if($Anfangsposition > 0)
        {
            $Anfang="<a href=\"" . $pathvars[virtual]."/fli4l/best_of,list,0," . $suchtext . ".html\">[erste Seite]</a>&nbsp;";
            $back=$Anfangsposition-$Zeilen_pro_Seite;
            if($back < 0)
            {
                $back = 0;
            }
            $Zurueck="<a href=\"" . $pathvars[virtual]."/fli4l/best_of,list," . $back . "," . $suchtext . ".html\">[erste Seite]</a>&nbsp;";
        }

        if($Anzahl>$Zeilen_pro_Seite)
        {
            $Seiten=intval($Anzahl/$Zeilen_pro_Seite);
            if($Anzahl%$Zeilen_pro_Seite)
            {
                $Seiten++;
            }
        }

        for ($i = 1; $i <= $Seiten; $i++ )
        {
            $fwd=($i-1)*$Zeilen_pro_Seite;
            $LinkSeiten=$LinkSeiten . "<a href=\"" . $pathvars[virtual]."/fli4l/best_of,list," . $fwd . "," . $suchtext . ".html\">" . $i . "</a>&nbsp;";
        }

        if($Anfangsposition < $Anzahl-$Zeilen_pro_Seite)
        {
            $fwd=$Anfangsposition+$Zeilen_pro_Seite;
            $Weiter="<a href=\"" . $pathvars[virtual]."/fli4l/best_of,list," . $fwd . "," . $suchtext . ".html\">[eine Seite weiter]</a>&nbsp;";
            $fwd=$Anzahl-$Zeilen_pro_Seite;
            $Ende="<a href=\"" . $pathvars[virtual]."/fli4l/best_of,list," . $fwd . "," . $suchtext . ".html\">[letzte Seite]</a>";
        }

        $navi=$Anfang . $Zurueck . $LinkSeiten . $Weiter . $Ende;
        if ($navi!="")
        {
        $ausgaben[output] .= navigation($Anfang, $Zurueck, $LinkSeiten, $Weiter, $Ende);
        $ausgaben[output] .= "<hr>";
        }

        $ausgaben[output] .= "<table border=\"0\" cellspacing=\"4\" cellpadding=\"3\">";

        while ($zeile = $db -> fetch_array($result,$nop))
        {
            $vom=$zeile[vom];
            $bis=$zeile[bis];
            $nick=$zeile[nick];
            $mail=$zeile[mail];
            $text=$zeile[text];

            $vom_datum=substr($vom,8,2) . "." . substr($vom,5,2) . "." . substr($vom,0,4);
            $vom_zeit=substr($vom,11,5);
            $bis_zeit=substr($bis,11,5);
            $ausgaben[output] .= "<tr>";
            $ausgaben[output] .= "<td>Mitschnitt vom " . $vom_datum . " in der Zeit von " . $vom_zeit . " bis " . $bis_zeit . "<br>";
            $ausgaben[output] .= "Eingeschickt von '" . $nick . "' / <a href=\"mailto:" . $mail . "\">" . $mail . "</a></td>";
            $ausgaben[output] .= "</tr>";
            $ausgaben[output] .= "</table>";

            $text=htmlentities($text);
            $text=str_replace("  ", "&nbsp;&nbsp;",$text);
            $text=str_replace("\\'", "'",$text);
            $text=nl2br($text);

            $ausgaben[output] .= "<table border=\"0\" width=\"100%\">";
            $ausgaben[output] .= "<tr>";
            $ausgaben[output] .= "<td><font face=\"Courier\" size=\"2\">" . $text . "</font></td>";
            $ausgaben[output] .= "</tr>";
            $ausgaben[output] .= "</table><hr>";
            $ausgaben[output] .= "<table border=\"0\" cellspacing=\"4\" cellpadding=\"3\">";
        }

        $ausgaben[output] .= "</table>";
        if ($navi!="")
        {
        $ausgaben[output] .= navigation($Anfang, $Zurueck, $LinkSeiten, $Weiter, $Ende);
        $ausgaben[output] .= "<hr>";
        }
    } else
    {
//
/// neuer Eintrag
//

    $nick=$HTTP_POST_VARS[nick];
    $mail=$HTTP_POST_VARS[mail];
    $datum=$HTTP_POST_VARS[datum];
    $zeit_von=$HTTP_POST_VARS[zeit_von];
    $zeit_bis=$HTTP_POST_VARS[zeit_bis];
    $text=$HTTP_POST_VARS[text];

    if (!isset($nick) || $nick=="")
    {
        $error=1;
    }

    if (!eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4}$", $mail))
    {
        $error=$error+2;
    }
    if (!checkdate(substr($datum,3,2), substr($datum,0,2), substr($datum,6,4)))
    {
        $error=$error+4;
    }
    if (!eregi("^([01][0-9])|[2][0-3]:[0-5][0-9]", $zeit_von))
    {
        $error=$error+8;
    }
    if (!eregi("^([01][0-9])|[2][0-3]:[0-5][0-9]", $zeit_bis))
    {
        $error=$error+16;
    }
    if (!isset($text) || $text=="" || substr($text,0,59) == "<bastard> nur ein beispiel, so sollte ne zeile aussehen ...")
    {
        $error=$error+32;
    }

        if (!isset($HTTP_POST_VARS[senden]))
        {
            $error=0;
        }

    if (isset($HTTP_POST_VARS[senden]) && $error==0)
    {
        $ausgaben[output]  = "Best of <a href=\"http://www.fli4l.de/german/irc.htm\">#fli4l</a> - neuer Eintrag.";

        $vom=substr($datum, 6,4) . "-" . substr($datum, 3,2) . "-" . substr($datum, 0,2) . " " . $zeit_von . ":00";
        $bis=substr($datum, 6,4) . "-" . substr($datum, 3,2) . "-" . substr($datum, 0,2) . " " . $zeit_bis . ":00";
        $nick="pre__" . $nick;
        // Speichern in Datenbank ...
        $sql="insert into bestof_fli4l
                  (vom, bis, nick, mail, text)
                   values ('$vom', '$bis', '$nick', '$mail', '$text')";
                $result = $db -> query($sql);

        $ausgaben[output]  .= "<p>Ihr Beitrag wurde mit Erfolg in die Datenbank eingetragen. Nach der Freischaltung steht er online. Vielen Dank.<br></p>";

        $mailtext = "Erfolgreiche Speicherung eines 'Best of #fli4l' Beitrages\n\n";
        $mailtext .= "Nick:        " . $nick . "\n";
        $mailtext .= "Mailadresse: " . $mail . "\n";
        $mailtext .= "Von:         " . $vom . "\n";
        $mailtext .= "Bis:         " . $bis . "\n";
        $mailtext .= "Beitrag:\n\n" . $text . "\n";

        $header = "From: webserver@creative.chaos.de\r\n";
        $header .= "Cc: stefan.krister@keimfarben.de\r\n";
        //$header .= "Bcc: birthdaycheck@example.com\r\n";
        $email = "stefan@bastard.lan";
        $subject = "Best of #fli4l - Eintrag";
        mail($email, $subject, $mailtext, $header);



    } else
    {

        $ausgaben[output]  = "Best of <a href=\"http://www.fli4l.de/german/irc.htm\">#fli4l</a> - neuer Eintrag";
        $ausgaben[output] .= "<form method=\"POST\" action=\"" . $pathvars[virtual] . "/fli4l/best_of,eintrag.html\">";
        $ausgaben[output] .= "<table border=\"0\" cellpadding=\"2\">";
        $ausgaben[output] .= "<tr>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\" colspan=\"2\">Bitte die folgenden Felder ausfüllen und Abschicken. Datum/Zeit Werte werden geprüft. Bei ungültigen Werten kommt diese Formular mit einer entspr. Fehlermeldung wieder ...</td>";
        $ausgaben[output] .= "</tr>";
        $ausgaben[output] .= "<tr>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\">Dein Nickname:</td>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\"><input type=\"text\" name=\"nick\" size=\"20\" value=\"" . $nick . "\">";
        if($error & 1)
        {
            $ausgaben[output] .= "<br><font color=\"#FF0000\">Keinen Nicknamen angegeben!</font>";
        }
        $ausgaben[output] .= "</td></tr>";
        $ausgaben[output] .= "<tr>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\">Deine Mail-Adresse:</td>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\"><input type=\"text\" name=\"mail\" size=\"40\" value=\"" . $mail . "\">";
        if($error & 2)
        {
                  $ausgaben[output] .= "<br><font color=\"#FF0000\">Keine oder ungültige Mailadresse angegeben!</font>";
        }
        $ausgaben[output] .= "</td></tr>";
        $ausgaben[output] .= "<tr>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\">Datum des Mitschnitts / Log:</td>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\"><input type=\"text\" name=\"datum\" size=\"10\" ";
        if($error & 4)
        {
            $ausgaben[output] .= "value=\"" . $datum . "\">";
              $ausgaben[output] .= "<br><font color=\"#FF0000\">Kein oder ungültiges Datum angegeben!";
        } elseif ($error != 0)
        {
              $ausgaben[output] .= "value=\"" . $datum . "\">";
        } else
        {
              $ausgaben[output] .= "value=\"tt.mm.jjjj\">";
        }
        $ausgaben[output] .= "</font></td></tr>";
        $ausgaben[output] .= "<tr>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\">Uhrzeit des Mittschnitts / Log:</font></td>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\">von <input type=\"text\" name=\"zeit_von\" size=\"5\" ";
        if ($error & 8)
        {
            $ausgaben[output] .= "value=\"" . $zeit_von . "\">";
        } elseif ($error != 0)
        {
              $ausgaben[output] .= "value=\"" . $zeit_von . "\">";
        } else
        {
            $ausgaben[output] .= "value=\"ss:mm\">";
        }
        $ausgaben[output] .= "Uhr bis <input type=\"text\" name=\"zeit_bis\" size=\"5\" ";
        if ($error & 16)
        {
            $ausgaben[output] .= "value=\"" . $zeit_bis . "\">";
        } elseif ($error != 0)
        {
            $ausgaben[output] .= "value=\"" . $zeit_bis . "\">";
        } else
        {
            $ausgaben[output] .= "value=\"ss:mm\">";
        }
        $ausgaben[output] .= "Uhr";
        if ($error & 8)
        {
            $ausgaben[output] .= "<br><font color=\"#FF0000\">Keine oder ungültige 'von' Zeit angegeben!";
        }
        if ($error & 16)
        {
              $ausgaben[output] .= "<br><font color=\"#FF0000\">Keine oder ungültige 'bis' Zeit angegeben!";
        }
        $ausgaben[output] .= "</font></td></tr>";
        $ausgaben[output] .= "<tr>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\">Der Mitschnitt / Log:";
        $ausgaben[output] .= "<ul>";
        $ausgaben[output] .= "<li>bitte hier keine Zeitangaben mehr, lediglich den Nicknamen und den Text</li>";
        $ausgaben[output] .= "<li>einleitende Zeilen, die das Thema erläutern sind ebenfalls erwünscht.</li>";
        if ($error & 32)
        {
            $ausgaben[output] .= "<li><font color=\"#FF0000\">Keinen Text angegeben, oder Beispieltext stehen gelassen!</font></li>";
        }
        $ausgaben[output] .= "</ul></td>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\"><textarea rows=\"20\" name=\"text\" cols=\"60\" wrap=\"off\"";
        if ($error & 32)
        {
        $ausgaben[output] .= ">" . $text;
        } elseif ($error != 0)
        {
            $ausgaben[output] .= ">" . $text;
        } else
        {
        $ausgaben[output] .= ">&lt;bastard&gt; nur ein beispiel, so sollte ne zeile aussehen ...";
        }
        $ausgaben[output] .= "</textarea>";
        $ausgaben[output] .= "</td></tr>";
        $ausgaben[output] .= "<tr>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\"><input type=\"submit\" value=\"Abschicken\" name=\"senden\"></td>";
        $ausgaben[output] .= "<td valign=\"top\" align=\"left\"></td>";

        $ausgaben[output] .= "</tr></table></form>";

}





    }




    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ++ $script_name ++ ]".$debugging[char];


function navigation($Anfang, $Zurueck, $LinkSeiten, $Weiter, $Ende)
{
    $return  = "<table border=\"0\" cellspacing=\"4\" cellpadding=\"3\">";
    $return .= "<tr>";
    $return .= "<td nowrap valign=\"top\">" . $Anfang . $Zurueck . $LinkSeiten .$Weiter . $Ende . "</td>";
    $return .= "</tr>";
    $return .= "</table>";

    return($return);
}

?>