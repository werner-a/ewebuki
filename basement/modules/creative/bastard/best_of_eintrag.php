<?
function form($error, $nick, $mail, $datum, $zeit_von, $zeit_bis, $text)
{
    global $PHP_SELF;
?>

<form method="POST" action="<?echo"$PHP_SELF";?>">
  <table border="0" cellpadding="2">
    <tr>
      <td valign="top" align="left" colspan="2"><font face="Verdana">Bitte die folgenden Felder
      ausfüllen und Abschicken. Datum/Zeit Werte werden geprüft. Bei ungültigen Werten kommt
      diese Formular mit einer entspr. Fehlermeldung wieder ...</font></td>
    </tr>
    <tr>
      <td valign="top" align="left"><font face="Verdana">Dein Nickname:</font></td>
      <td valign="top" align="left"><font face="Verdana"><input type="text" name="nick" size="20" value="<?echo"$nick";?>"></font>
      <? if($error & 1)
      {
      		echo "<br><font color=\"#FF0000\">Keinen Nicknamen angegeben!</font>";
      } ?>
      </td>
    </tr>
    <tr>
      <td valign="top" align="left"><font face="Verdana">Deine Mail-Adresse:</font></td>
      <td valign="top" align="left"><font face="Verdana"><input type="text" name="mail" size="40" value="<?echo"$mail";?>"></font>
      <? if($error & 2)
      {
      		echo "<br><font color=\"#FF0000\">Keine oder ungültige Mailadresse angegeben!</font>";
      } ?>      
      </td>
    </tr>
    <tr>
      <td valign="top" align="left"><font face="Verdana">Datum des Mitschnitts / Log:</font></td>
      <td valign="top" align="left"><font face="Verdana"><input type="text" name="datum" size="10" 
      <? if($error & 4)
      {
      	echo "value=\"$datum\">";
      	echo "<br><font color=\"#FF0000\">Kein oder ungültiges Datum angegeben!";
      } elseif ($error != 0)
      {	
      	echo "value=\"$datum\">";
      } else
      {
      	echo"value=\"tt.mm.jjjj\">";
      } ?> </font>           
      </td>
    </tr>
    <tr>
      <td valign="top" align="left"><font face="Verdana">Uhrzeit des Mittschnitts / Log:</font></td>
      <td valign="top" align="left"><font face="Verdana">von <input type="text" name="zeit_von" size="5"
      <? if ($error & 8)
      {  
      	echo "value=\"$zeit_von\">";
    
      } elseif ($error != 0)
      {	
      	echo "value=\"$zeit_von\">";
      } else
      {
      	echo"value=\"ss:mm\">";
      } ?> Uhr bis <input type="text" name="zeit_bis" size="5"
      <? if ($error & 16)
      {  
      	echo "value=\"$zeit_bis\">";
    
      } elseif ($error != 0)
      {	
      	echo "value=\"$zeit_bis\">";
      } else
      {
      	echo"value=\"ss:mm\">";
      } ?> Uhr 
      <? if ($error & 8)
      {
      	echo "<br><font color=\"#FF0000\">Keine oder ungültige 'von' Zeit angegeben!";
      }	
      if ($error & 16)
      {
      	echo "<br><font color=\"#FF0000\">Keine oder ungültige 'bis' Zeit angegeben!";
      }	?>      
       </font>
      </td>
    </tr>
    <tr>
      <td valign="top" align="left"><font face="Verdana">Der Mitschnitt / Log:</font>
       <ul>
        <li><font face="Verdana"><small>bitte hier keine Zeitangaben mehr, lediglich den Nicknamen und den Text</small></font></li>
        <li><font face="Verdana"><small>einleitende Zeilen, die das Thema erläutern sind ebenfalls erwünscht.</small></font></li>
        <? if ($error & 32)
        {
           echo "<li><font face=\"Verdana\" color=\"#FF0000\"><small>Keinen Text angegeben, oder Beispieltext stehen gelassen!</small></font></li>";
        } ?>
       </ul>
      </td>
      <td valign="top" align="left"><font face="Verdana"><textarea rows="20" name="text" cols="60" wrap="off"
      <? if ($error & 32)
      {
           echo ">" . $text;
      } elseif ($error != 0)
      {
      	   echo ">" . $text;
      } else
      {
           echo ">&lt;bastard&gt; nur ein beispiel, so sollte ne zeile aussehen ...";
      } ?> </textarea></font>
      </td>
    </tr>
    <tr>
      <td valign="top" align="left"><font face="Verdana"><input type="submit" value="Abschicken" name="senden"></font></td>
      <td valign="top" align="left"><font face="Verdana"></font></td>
    </tr>
  </table>
</form>
<?
}
?>
<html>

<head>
<title>Neuer Eintrag in 'Best of #fli4l'</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
</head>
<body>
<?
$error=0;
if (!isset($senden))
{
	form($error, $nick, $mail, $datum, $zeit_von, $zeit_bis, $text);
} else
{
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
}

if ($error!=0)
{
	form($error, $nick, $mail, $datum, $zeit_von, $zeit_bis, $text);
}
if ($error==0 && isset($senden))
{
	$vom=substr($datum, 6,4) . "-" . substr($datum, 3,2) . "-" . substr($datum, 0,2) . " " . $zeit_von . ":00";
	$bis=substr($datum, 6,4) . "-" . substr($datum, 3,2) . "-" . substr($datum, 0,2) . " " . $zeit_bis . ":00";
	$nick="pre__" . $nick;
	// Speichern in Datenbank ...
	$handle=mysql_connect("sql.linovate.de", "creative", "---passwort---");
	$result=mysql_select_db("creative", $handle);

	$sql="insert into bestof_fli4l
	      (vom, bis, nick, mail, text)
	      values ('$vom', '$bis', '$nick', '$mail', '$text')";
	$result=mysql_query($sql, $handle);     


	if ($result)
	{
?>
	<p><font face="Verdana">Ihr Beitrag wurde mit Erfolg in die Datenbank eingetragen. Nach der Freischaltung steht er online auf der '<a href="http://creative.chaos.de/fli4l/best_of.php">Best of #fli4l</a>'. Vielen Dank.</font><br></p>	
<?
	$mailtext = "Erfolgreiche Speicherung eines 'Best of #fli4l' Beitrages\n\n";
	$mailtext .= "Nick:        " . $nick . "\n";
	$mailtext .= "Mailadresse: " . $mail . "\n";
	$mailtext .= "Von:         " . $vom . "\n";
	$mailtext .= "Bis:         " . $bis . "\n";
	$mailtext .= "Beitrag:\n\n" . $text . "\n";
	} else
	{
?>
	<p><font face="Verdana">Ihr Beitrag konnte nicht in die Datenbank gespeicert werden, versuchen Sie es zu einem Späteren Zeitpunkt nochmal. Vielen Dank.<br>Weiter zu '<a href="http://creative.chaos.de/fli4l/best_of.php">Best of #fli4l</a>'</font><br></p>	
<?	
	$mailtext = "Fehler beim Speichern eines 'Best of #fli4l' Beitrages\n\n";
	$mailtext .= "Nick:        " . $nick . "\n";
	$mailtext .= "Mailadresse: " . $mail . "\n";
	$mailtext .= "Von:         " . $vom . "\n";
	$mailtext .= "Bis:         " . $bis . "\n";
	$mailtext .= "Beitrag:\n\n" . $text . "\n";
	$mailtext .= "\n\nMysql-Result:" . $result . "\n";
	}

$header = "From: webserver@creative.chaos.de\r\n";
$header .= "Cc: stefan.krister@keimfarben.de\r\n";
//$header .= "Bcc: birthdaycheck@example.com\r\n";

$email = "stefan.krister@creative.chaos.de";
$subject = "Best of #fli4l - Eintrag";
mail($email, $subject, $mailtext, $header);

}	


	
?>
</body>
</html>
