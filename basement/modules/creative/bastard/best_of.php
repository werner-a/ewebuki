<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta http-equiv="expires" content="0">
<title>Best of #fli4l</title>
</head>
<body>

<?
function kopf()
{
    $mail="<a href=\"mailto:stefan.krister@creative.chaos.de?" .
          "subject=Neuer Eintrag in 'Best of #fli4l'\">mich</a>";
?>
<p><font face="Verdana">Best of <a href="http://www.fli4l.de/german/irc.htm">#fli4l</a> / <a href="irc://main.freechat-network.de:6667/fli4l">mitchatten</a> / einen Mitschnitt / Log kann man <a href="http://creative.chaos.de/fli4l/best_of_eintrag.php">hier</a> eintragen)</font></p>
<?
}

function suche($Anzahl, $suchtext)
{
	global $PHP_SELF;
?>
<form method="POST" action="<?echo"$PHP_SELF";?>">
  <table border="0" cellspacing="4" cellpadding="3">
    <tr>
      <td><font face="Verdana">Volltextsuche:</font></td>
      <td colspan="2"><font face="Verdana">nach <input type="text" name="suchtext" size="40"></font></td>
      <td><font face="Verdana">&nbsp;<input type="submit" value="Suchen" name="suchen">
      <? if ($suchtext!="")
      { ?>
            <input type="submit" value="Alles anzeigen" name="alles">
        <?
      }
      ?>
      </font></td>
    </tr>
<? if ($suchtext!="")
   {
?>   	    
    <tr>
      <td colspan="3"><font face="Verdana">Die Suche nach "<?echo"$suchtext";?>" ergab <?echo"$Anzahl";?> Treffer.</font></td>
    </tr>
<? } ?>    
  </table>
  <hr>
<?
}

function navigation($Anfang, $Zurueck, $LinkSeiten, $Weiter, $Ende)
{
?>
  <table border="0" cellspacing="4" cellpadding="3">
    <tr>
      <td nowrap valign="top"><font face="Verdana"><?echo"$Anfang $Zurueck $LinkSeiten $Weiter $Ende";?></td>
    </tr>
  </table>

<?	
}

function anzeige_kopf($vom, $bis, $nick, $mail, $Anfang, $Zurueck, $LinkSeiten, $Weiter, $Ende)
{
	$vom_datum=substr($vom,8,2) . "." . substr($vom,5,2) . "." . substr($vom,0,4);
	$vom_zeit=substr($vom,11,5);
	$bis_zeit=substr($bis,11,5);
	

?>

    <tr>
      <td><font face="Verdana">Mitschnitt vom <?echo"$vom_datum";?> in der Zeit von <?echo"$vom_zeit";?> bis <?echo"$bis_zeit";?><br>
      Eingeschickt von '<?echo"$nick";?>' / <a href="mailto:<?echo"$mail" . "\">" . "$mail";?></a></font></td>
    </tr>
  </table>
<?
}

function anzeige_body($text)
{
	$text=htmlentities($text);
	$text=str_replace("  ", "&nbsp;&nbsp;",$text);
	$text=str_replace("\\'", "'",$text);
//	$text=str_replace("\\\\'", "'",$text);
	$text=nl2br($text);
//	$text=stripslashes($text);

?>
  <table border="0" width="100%">
   <tr>
      <td><font face="Courier" size="3"><?echo $text;?></font></td>
   </tr>
  </table> 
  
<?
}

function fehler($text, $variable)
{
    echo "<p>$text=" . htmlentities($variable . "") . "</p>";
}

if (isset($alles))
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
if (!isset($Anfangsposition)) {
  $Anfangsposition = 0;
}

$handle=mysql_connect("sql.linovate.de", "creative", "---passwort---");
$result=mysql_select_db("creative", $handle);

$sql="select vom, bis, nick, mail, text from bestof_fli4l where " . $bedingung . " order by vom desc ".
     "limit $Anfangsposition,$Zeilen_pro_Seite";
$result=mysql_query($sql, $handle);     


$sql="select vom from bestof_fli4l where " . $bedingung . " order by vom ";
     
$result1=mysql_query($sql, $handle);
$Anzahl=mysql_num_rows($result1);


kopf();
suche($Anzahl, $suchtext);

if($Anfangsposition > 0)
{
    $Anfang="<a href=\"$PHP_SELF?Anfangsposition=0&suchtext=$suchtext\">[erste Seite]</a>&nbsp;";
    $back=$Anfangsposition-$Zeilen_pro_Seite;
    if($back < 0)
    {
        $back = 0;
    }
    $Zurueck="<a href=\"$PHP_SELF?Anfangsposition=$back&suchtext=$suchtext\">[eine Seite zur&uuml;ck]</a>&nbsp;";
 }

if($Anzahl>$Zeilen_pro_Seite)
{
    $Seiten=intval($Anzahl/$Zeilen_pro_Seite);
    if($Anzahl%$Zeilen_pro_Seite) 
    {
        $Seiten++;
    }
}

//fehler("Seiten", $Seiten);

for ($i=1;$i<=$Seiten;$i++)
{
    $fwd=($i-1)*$Zeilen_pro_Seite;
    $LinkSeiten=$LinkSeiten . "<a href=\"$PHP_SELF?Anfangsposition=$fwd&suchtext=$suchtext\">$i</a>&nbsp;";
}



if($Anfangsposition < $Anzahl-$Zeilen_pro_Seite)
{
    $fwd=$Anfangsposition+$Zeilen_pro_Seite;
    $Weiter="<a href=\"$PHP_SELF?Anfangsposition=$fwd&suchtext=$suchtext\">[eine Seite weiter]</a>&nbsp;";
    $fwd=$Anzahl-$Zeilen_pro_Seite;
    $Ende="<a href=\"$PHP_SELF?Anfangsposition=$fwd&suchtext=$suchtext\">[letzte Seite]</a>";
}
	
//echo "<p>Anfang=" . $Anfang ."Zurück=" . $Zurueck . "Linkseiten=" . $LinkSeiten . "Weiter=" . $Weiter . "Ende" . $Ende . "</p>";
$navi=$Anfang . $Zurueck . $LinkSeiten . $Weiter . $Ende;
if ($navi!="")
{
	navigation($Anfang, $Zurueck, $LinkSeiten, $Weiter, $Ende);	
	echo "<hr>";
}	

echo "<table border=\"0\" cellspacing=\"4\" cellpadding=\"3\">";

while ($zeile=mysql_fetch_array($result))
{
    $vom=$zeile[0];
    $bis=$zeile[1];
    $nick=$zeile[2];
    $mail=$zeile[3];
    $text=$zeile[4];
    anzeige_kopf($vom, $bis, $nick, $mail, $Anfang, $Zurueck, $LinkSeiten, $Weiter, $Ende );
    anzeige_body($text);
    echo "</table><hr>";
    echo "<table border=\"0\" cellspacing=\"4\" cellpadding=\"3\">";
}
echo "</table>";
if ($navi!="")
{
	navigation($Anfang, $Zurueck, $LinkSeiten, $Weiter, $Ende);	
	echo "<hr>";
}	


?>
</body>
</html>
