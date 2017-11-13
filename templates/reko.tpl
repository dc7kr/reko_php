<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<meta charset='utf-8'>
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/base/jquery-ui.css" type="text/css" media="all" />
	<link rel="stylesheet" type="text/css" href="style.css"/>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-timepicker-addon.js"></script>

	<script type="text/javascript" src="common-functions.js"></script>
  <script type="text/javascript">
var tg25_rate = {$cfg["tariff"]["ueber24"]};
var tg14_rate = {$cfg["tariff"]["bis24"]};
var tg8_rate = {$cfg["tariff"]["bis14"]};
var pkw_rate = {$cfg["tariff"]["pkwKm"]};
var pkw_nolimit = {$cfg["tariff"]["pkwNoLimit"]};
var pkw_maxSum = {$cfg["tariff"]["pkwMax"]};
</script>
	<script type="text/javascript" src="reko.js"></script>
</head>
<body>
<h1>Bund Deutscher Zupfmusiker</h1> 
						
<h2>Reisekostenabrechnung</h2>
				
<form method="POST">		
<div id="tabs">
<ul>
	<li><a href="#tabs-1">Angaben zur Person</a></li>
	<li><a href="#tabs-2">Reise</a></li>
	<li><a href="#tabs-3">Entstandene Kosten</a></li>
</ul>
<div id="tabs-1">
<table>
	<tr>
		<td class="label">Vorname und Name:</td>
		<td><input type="text" name="name" value="{$meta["name"]}"/></td>
	</tr>
	<tr>
		<td class="label">Strasse:</td>
		<td><input type="text" name="strasse" value="{$meta["street"]}"/></td>
	</tr>
	<tr>
		<td class="label">PLZ/Ort:</td>
		<td><input type="text" name="ort" value="{$meta["city"]}"/></td>
	</tr>
	<tr>
		<td class="label">Funktion im BDZ:</td>
		<td><input type="text" name="fkt" value="{$meta["role"]}"/></td>
	</tr>	

	<tr>
		<td colspan="2"><h2>Bankverbindung</h2></td>
	</tr>
	<tr>
		<td class="label">BIC:</td>
		<td><input type="text" name="bic" value="{$meta["bic"]}"/></td>
	</tr>
	<tr>
		<td class="label">IBAN:</td>
		<td><input type="text" name="iban" value="{$meta["iban"]}"/></td>
	</tr>
	<tr>
		<td class="label">Name der Bank:</td>
		<td><input type="text" name="bank" value="{$meta["bank"]}"/></td>
	</tr>
</table>
</div>
<div id="tabs-2">
<h2>Reiseroute</h2>
<table>
	<tr>
		<td class="label">von:</td>
		<td><input type="text" name="von" value="{$meta["start_city"]}"/></td>
	</tr>
	<tr>
		<td class="label">nach:</td>
		<td><input type="text" name="nach" value="{$meta["end_city"]}"/></td>
	</tr>
	<tr>
		<td class="label">&uuml;ber:</td>
		<td><input type="text" name="ueber"/></td>
	</tr>
	<tr>
		<td class="label">Beginn der Reise:</td>
		<td><input class="datepicker" type="text" id="beginn" name="beginn"/></td>
		<td class="label">Uhrzeit:</td>
		<td><input type="text" id="beginnZeit" name="beginnZeit" editable="false"/></td>
	</tr>
	<tr>
		<td class="label">Ende der Reise:</td>
		<td><input class="datepicker" id="ende" type="text" name="ende"/></td>
		<td class="label">Uhrzeit:</td>
		<td><input type="text" id="endeZeit" name="endeZeit"/></td>
	</tr>
	<tr>
		<td class="label">Grund der Reise:</td>
		<td><input type="text" name="grund" value="{$meta["reason"]}"/></td>
	</tr>
</table>
</div>

<div id="tabs-3">					
<h2>Entstandene Kosten</h2>
<table>						
	<tr>
		<td class="label">Hotelkosten</td>
		<td colspan="2">&nbsp;</td>
		<td><input class="summand" type="text" id="hotel" name="hotel" size="4"/> €</td>
	</tr>
	<tr>
		<td class="label">Fahrtkosten Deutsche Bahn 2. Klasse</td>
		<td colspan="2">&nbsp;</td>
		<td><input class="summand" type="text" id="bahn" name="bahn" size="4"/> €</td>
	</tr>
	<tr>
		<td class="label">Fahrtkosten Bus, Straßenbahn, U-Bahn</td>
		<td colspan="2">&nbsp;</td>
		<td><input class="summand" type="text" size="4" id="oeff" name="oeff"/> €</td>
	</tr>
	<tr>
		<td class="label">Taxi (mit Begründung)</td>
		<td colspan="2">&nbsp;</td>
		<td><input class="summand" type="text" size="4" id="taxi" name="taxi"/> €</td>
	</tr>
	<tr>
		<td class="label">Fahrt mit eigenem PKW</td>
		<td><input type="text" size="4" id="pkwkm" name="pkwkm"/></td>
		<td>km zu {$cfg["tariff"]["pkwKm"]} €</td>
		<td><input class="summand" readonly="readonly" type="text" size="4" id="pkwkm_sum" name="pkwkm_sum"/> €</td>
	</tr>
	<tr>
		<td class="label">Begründung für Abweichungen (z.B. Sonderpreise Bahn, Flug, Taxi, ...)</td>
		<td><input type="text" name="abweichungen"/></td>
	</tr>
	</table>

	<h2>Tagegeld</h2>
	<table border="1" width="1000">
	<tr>
		<td>&nbsp;</td>
		<td>F</td>
		<td>M</td>
		<td>A</td>
		<td>Tagegeld</td>
		<td>&nbsp;</td>
	</tr>
	<tr id="tgrow1" class="tgrow">
		<td class="tg_data"><input type="text" id="tg_tag1" name="tg_tag1" readonly="readonly" class="tg_tag"/></td>
		<td class="tg_data"><input type="checkbox" id="tg_f1" name="tg_f1" class="tg_f"/></td>
		<td class="tg_data"><input type="checkbox" id="tg_m1" name="tg_m1" class="tg_m"/></td>
		<td class="tg_data"><input type="checkbox" id="tg_a1" name="tg_a1" class="tg_a"/></td>
		<td class="label tg_data"><input type="text" class="tg_tariff" id="tg_tariff1" name="tg_tariff1" readonly="readonly" size="5"/>&nbsp; € </td>
		<td class="tg_data sum"><input class="summand tg_sum" type="text" id="tg_sum1" name="tg_sum1" readonly="readonly" size="10" />&nbsp;€</td>
	</tr>
	<tr>
		<td class="label">Sonstige Ausgaben (mit Begründung)</td>
		<td colspan="4">&nbsp;</td>
		<td class="sum"><input class="summand" type="text" size="4" name="sonst" id="sonst"/>&nbsp;€</td>
	</tr>
	<tr>
		<td>Summe</td>
		<td colspan="4">&nbsp;</td>
		<td class="tg_data sum">
			<input type="text" size="10" readonly="readonly" id="master_sum"/>&nbsp;€</td>
	</tr>
</table>						
<input type="submit" value="Absenden"/><br/>
<div id="msgs"></div>
</div>
</div>
</form>
</body>
</html>
