<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Fli4l-Erfassung</title>
</head>
<body bgcolor="#6666FF" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">

<?

function formular()
{
	global $PHP_SELF;
	?>
	<p><big><font face="Verdana" color="#FFFFFF"><u>Fli4l - Routerstatistik</u></font></big></p>
	<p><font face="Verdana" color="#FFFFFF"><a href="http://creative.chaos.de/fli4l/auswertung.php">Zur Auswertung</a></font></p>	
	<form method="POST" action="<? echo "$PHP_SELF"; ?>">
  	<table border="0" cellpadding="4" cellspacing="0" bgcolor="#AAAA00">
    	<tr>
      		<td colspan="2" bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Freiwillige Datenerfassung um mal zu sehen, wieviele Router es an welchen Orten so gibt.</font><br>
                    <font face="Verdana" color="#FFFFFF"><u><br>
                    Für den Index:</u></font>
                </td>
        </tr>
        <tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Pseudonym / Nickname</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana"><font color="#FFFFFF"><input type="text" name="nick" size="20"></font><font color="#400040"><sup><big>*</big></sup></font></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Postleitzahl / Routerstandort</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana"><font color="#FFFFFF">
      			<select name="land" size="1">
        			<option selected value="D">D</option>
        			<option value="A">A</option>
        			<option value="CH">CH</option>
        			<option value="S">S</option>
      			</select>-
      			<input type="text" name="plz" size="5"></font><font color="#400040"><sup><big><big>*</big></big></sup></font></font>
      		</td>
    	</tr>
    	<tr>
      		<td colspan="2" bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Diese </font><u><font face="Verdana" color="#400040">drei Felder</font></u><font face="Verdana" color="#FFFFFF"> dienen der Eindeutigkeit und werden in der Datenbank<br>
      			als sog. </font><u><font face="Verdana" color="#400040">Primärschlüssel</font></u><font face="Verdana" color="#FFFFFF"> verwendet. Über die </font><font face="Verdana" color="#400040"><u>Postleitzahl</u></font><font face="Verdana" color="#FFFFFF"> versuche ich<br>
      			eine regionale Zuordnung als Grafik.</font>
      		</td>
    	</tr>
    	<tr>
      		<td colspan="2" bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF"><u><br>
      			Details zum Router:</u></font></td>
    		</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">CPU / Taktrate</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="cpu" size="1">
        			<option selected value="386">386er</option>
        			<option value="486">486er</option>
        			<option value="586">Pentium</option>
        			<option value="K6">AMD-K6</option>
        			<option value="K7">AMD-K7</option>
        			<option value="NS Geode">NS Geode</option>
        			<option value="besser">besser</option>
      				</select> bei 
      			<select name="mhz" size="1">
        			<option selected value="32">&lt; 33</option>
        			<option value="33">33</option>
        			<option value="40">40</option>
        			<option value="50">50</option>
        			<option value="66">66</option>
        			<option value="75">75</option>        			
        			<option value="90">90</option>        			
        			<option value="100">100</option>
        			<option value="133">133</option>
        			<option value="166">166</option>
        			<option value="167">&gt; 166</option>
      			</select> MHz</font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Größe d. Arbeitsspeichers</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="ram" size="1">
        			<option selected value="7">&lt; 8</option>
        			<option value="8">8 bis 15</option>
        			<option value="16">16 bis 23</option>
        			<option value="24">24 bis 31</option>
        			<option value="32">32 bis 63</option>
        			<option value="64">64 oder mehr</option>
      			</select> MBytes</font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Peripheriegeräte</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="cdrom" size="1">
      				<option selected value="n">ohne</option>
      				<option value="j">mit</option> 
      			</select> CD-ROM / Festplatte 
      			<select name="hdd" size="1">
        			<option selected value="0">keine</option>
        			<option value="8">CF 8</option>
        			<option value="16">CF 16</option>
        			<option value="32">CF 32</option>
        			<option value="Z100">ZIP 100</option>
        			<option value="100">bis 100</option>
        			<option value="200">bis 200</option>
        			<option value="500">bis 500</option>
        			<option value="1000">bis 1000</option>
        			<option value="5000">bis 5000</option>
        			<option value="5001">über 5000</option>
      			</select> MBytes</font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Gehäuseform</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="gehaeuse" size="1">
        			<option value="Desktop">Desktop</option>
        			<option value="Slimline">Slimline</option>
        			<option value="Notebook">Notebook</option>
        			<option value="19&quot; 1HE">19&quot; 1HE</option>
        			<option value="19&quot; 2HE">19&quot; 2HE</option>
        			<option value="19&quot; 3HE">19&quot; 3HE</option>
        			<option value="Minitower">Minitower</option>
        			<option value="Miditower">Miditower</option>
        			<option value="Bigtower">Bigtower</option>
        			<option value="Eigenbau">Eigenbau</option>
      			</select></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">In Betrieb seit</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="monat" size="1">
        			<option selected value="1">Januar</option>
        			<option value="2">Februar</option>
        			<option value="3">März</option>
        			<option value="4">April</option>
        			<option value="5">Mai</option>
        			<option value="6">Juni</option>
        			<option value="7">Juli</option>
        			<option value="8">August</option>
        			<option value="9">September</option>
        			<option value="10">Oktober</option>
        			<option value="11">November</option>
        			<option value="12">Dezember</option>
      			</select> 
      			<select name="jahr" size="1">
        			<option value="2000">2000</option>
        			<option value="2001">2001</option>
        			<option selected value="2002">2002</option>
      			</select></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">OPT-Pakete</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="opt" size="1">
        			<option value="4">weniger als 5</option>
        			<option value="10">5 bis 10</option>
        			<option value="11">mehr als 10</option>
      			</select></font>
      		</td>
    	</tr>
    	<tr>
      		<td colspan="2" bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF"><u><br>
      			Details zur Internetverbindung:</u></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Einwahl / Technik</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="einwahl" size="1">
        			<option value="DSL">DSL</option>
        			<option value="ISDN">ISDN</option>
        			<option value="Kabelmodem">Kabelmodem</option>
        			<option value="Ethernet">Ethernet</option>
        			<option value="sonstige">sonstige</option>
        			<option value="Powerline">Powerline</option>
        			<option value="Modem">analog (Modem)</option>
      			</select></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Abrechnung</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="tarif" size="1">
        			<option value="flat">pauschal (Flat)</option>
        			<option value="volumen">volumen</option>
        			<option value="misch">gemischt</option>
        			<option value="zeit">zeit</option>
        			<option value="andere">andere</option>
      			</select></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF" valign="top"><font face="Verdana" color="#FFFFFF">Geschwindigkeit</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Upstream 
      			<select name="upstream" size="1">
        			<option value="56">&lt; 56</option>
        			<option value="64">64</option>
        			<option value="128">128</option>
        			<option value="192">192</option>
        			<option value="256">256</option>
        			<option value="384">384</option>
        			<option value="512">512</option>
        			<option value="1024">1024</option>
        			<option value="2048">2048</option>
        			<option value="besser">besser</option>
      			</select> kbit/s<br>Downstream 
      			<select name="downstream" size="1">
        			<option value="56">&lt; 56</option>
        			<option value="64">64</option>
        			<option value="128">128</option>
        			<option value="256">256</option>
        			<option value="512">512</option>
        			<option value="768">768</option>
        			<option value="1024">1024</option>
        			<option value="2048">2048</option>
        			<option value="besser">besser</option>
      			</select> kbit/s</font>
      		</td>
    	</tr>
    	<tr>
      		<td colspan="2" bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF"><u><br>
      			Details zum LAN hinter dem Router:</u></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Anzahl der Clients</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="clients" size="1">
        			<option selected value="1">&lt; 5</option>
        			<option value="5">5-10</option>
        			<option value="11">&gt; 10</option>
      			</select></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Anzahl der Server</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="server" size="1">
        			<option selected value="1">&lt; 5</option>
        			<option value="5">5-10</option>
        			<option value="11">&gt; 10</option>
      			</select></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">Anzahl der User</font>
      		</td>
      		<td bgcolor="#6666FF"><font face="Verdana" color="#FFFFFF">
      			<select name="user" size="1">
        			<option selected value="1">&lt; 5</option>
        			<option value="5">5-10</option>
        			<option value="11">&gt; 10</option>
      			</select></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF" colspan="2"><font face="Verdana" color="#FFFFFF"><u><br>
      			Bemerkungen / Ergänzungen:</u></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF" colspan="2"><font face="Verdana" color="#FFFFFF">
      			<p>Alle Details, die in die Statistik einfließen sollen, aber durch obige Felder nicht genau genug erfasst werden, können hier eingetragen werden. Ich versuche, die Datenbank dynamisch den Erfordernissen anzupassen.</p>
      			<textarea rows="10" name="bemerkung" cols="60"></textarea></font>
      		</td>
    	</tr>
    	<tr>
      		<td bgcolor="#6666FF" colspan="2"><input type="submit" value="Abschicken" name="submit">
      		</td>
    	</tr>
  	</table>
	</form>
	<?
}

// main

if ( ! isset($submit))
{
	formular();
} else	
{
?>
	<p><big><font face="Verdana" color="#FFFFFF"><u>Fli4l - Routerstatistik</u><br><br>Verarbeitung Ihrer Daten ...</font></big><br><br></p>	
<?
	$handle=mysql_connect("sql.linovate.de", "creative", "");
	$result=mysql_select_db("creative", $handle);
	
	$query="insert into router
			(nick, land, plz, cpu, mhz, ram, cdrom, hdd, gehaeuse, monat, jahr, opt,
		 	 einwahl, tarif, upstream, downstream, clients, server, user, bemerkung)
		values ('$nick', '$land', '$plz', '$cpu', '$mhz', '$ram', '$cdrom', '$hdd',
			'$gehaeuse', '$monat', '$jahr', '$opt', '$einwahl', '$tarif', '$upstream',
			'$downstream', '$clients', '$server', '$user', '$bemerkung')";
	$result=mysql_query($query, $handle);
	mysql_close();
	if ($result)
	{
?>
	<p><font face="Verdana" color="#FFFFFF">... mit Erfolg abgeschlossen.</font><br></p>	
<?
	} else
	{
?>
	<p><font face="Verdana" color="#FFFFFF">... konnte nicht abgeschlossen werden.</font><br></p>	
<?	
	}
}

?>
<p><font face="Verdana" color="#FFFFFF"><a href="http://creative.chaos.de/fli4l/auswertung.php">Zur Auswertung</a></font></p>	
<p>&nbsp;</p>
</body>
</html>
