<?php
	require_once("vars.php");
	require_once("HTML/Template/IT.php");

$EUR_FMT="%!.2n €";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    setlocale(LC_MONETARY, 'de_DE');
	$tmp = tempnam('/tmp','reko');

	$paramFile = fopen($tmp,"w");

	//echo $tmp;

	$tpl = new HTML_Template_IT("templates/");
	$tpl->loadTemplateFile("tpl.reko.tex");

//	print_r($_REQUEST);


	$tpl->setVariable("DATUM",strftime("%d.%m.%Y"));
	$common = array(  "name","strasse","ort","fkt","konto","blz","bank","grund","beginn", "von","nach","ueber","beginnZeit","ende","endeZeit");

	$kostenLabel = array( "hotel","bahn","oeff","taxi","pkwkm","sonst");


	echo "Ueber: "+$_REQUEST["ueber"];
	$route = $_REQUEST["von"];
	if (isset ($_REQUEST["ueber"]) && strlen($_REQUEST["ueber"])>0) {
		$route.="+ über ".$_REQUEST["ueber"];
	}
	$route.= " nach ".$_REQUEST["nach"];

	$tpl->setVariable("ROUTE",$route);
	
	foreach  ( $common as  $key ) {
		$tpl->setVariable(strtoupper($key),$_REQUEST[$key]);	
	}

	$kosten =0;
	foreach ( $kostenLabel as $kst ) {
		$tpl->setCurrentBlock("COST_ROW");

		if (! strcmp("pkwkm",$kst)) {
			$val = $_REQUEST[$kst]*$pkwKm;
			if ( $val > 150 ) { 
				$val = 150;
			}
			$tpl->setVariable("COST_DESCR",$labels["pkwkm"]);
			$tpl->setVariable("COST_UNIT",sprintf("%d km",$_REQUEST[$kst]));
			$tpl->setVariable("COST_EACH","à ".money_format($EUR_FMT,$pkwKm));
			$tpl->setVariable("COST_SUM",money_format($EUR_FMT,$val));
		} else {
			$val = $_REQUEST[$kst];
			$tpl->setVariable("COST_DESCR",$labels[$kst]);
			$tpl->setVariable("COST_UNIT"," ");
			$tpl->setVariable("COST_EACH"," ");
			$tpl->setVariable("COST_SUM",money_format($EUR_FMT,$val));
		}
		$kosten+=$val;
		if ( $val >0 ) {
			$tpl->setVariable("KOSTEN_DATA",$row);
			$tpl->parseCurrentBlock();
		}
	}

	$tpl->setCurrentBlock();
	if ( isset ($_REQUEST["abweichungen"]) && strlen($_REQUEST["abweichungen"])>0) {
		$tpl->setVariable("ABWEICHUNGEN","\\textbf{".$labels["abweichungen"]."} \\\\ ".$_REQUEST["abweichungen"]."\\\\");
	} else {
		$tpl->setVariable("ABWEICHUNGEN"," ");
	}
/*Tagegeld & 1.1.2011  & & 12 € \\
- Abzug Frühstück,Mittagessen,Abendessen & & & -12.00 € \\
Tagegeld & 2.1.2011 & & 12.00 € \\
- Abzug Frühstück, Abendessen & & 10.00 € \\
Tagegeld & 3.1.2011 & & 12.00 € \\
- Abzug Frühstück, Abendessen & & & 10.00 € \\*/

	$mitfNr =1;
	while ($_REQUEST["mitfkm".$mitfNr] >0) {
		$tpl->setCurrentBlock("COST_ROW");
		$name = $_REQUEST["mitfname".$mitfNr];
		$val = $_REQUEST["mitfkm".$mitfNr];
		$tpl->setVariable("COST_DESCR",$labels["mitf"]." ".$name);
		$tpl->setVariable("COST_UNIT",sprintf("%d km",$val));
		$tpl->setVariable("COST_EACH","à ".money_format($EUR_FMT,$mitfahrerKm));
		$tpl->setVariable("COST_SUM",money_format($EUR_FMT,$mitfahrerKm*$val));
		$tpl->parseCurrentBlock();
		$kosten+=$val*$mitfahrerKm;
		$mitfNr++;
	}


	$tgNr =1;
	while (isset ($_REQUEST["tg_sum".$tgNr]) ) {
		$tpl->setCurrentBlock("COST_ROW");
		$amount = $_REQUEST["tg_sum".$tgNr];
		$tgRate = $_REQUEST["tg_tariff".$tgNr];
		$reduction =0;
		$reductionDescr="";
		if (array_key_exists("tg_f".$tgNr,$_REQUEST)) {
			$reduction+=$ueber24*0.2; 
			$reductionDescr="Frühstück";
		}
		if (array_key_exists("tg_m".$tgNr,$_REQUEST)) { 
			if ($reduction>0) { $reductionDescr.=", "; }
			$reduction+=$ueber24*0.4; 
			$reductionDescr.="Mittagessen";
		}
		if (array_key_exists("tg_a".$tgNr,$_REQUEST)) { 
			if ($reduction>0) { $reductionDescr.=", "; }
			$reduction+=$ueber24*0.4; 
			$reductionDescr.="Abendessen";
		}
		$tpl->setVariable("COST_DESCR",$labels["tg"]);
		$tpl->setVariable("COST_UNIT",$_REQUEST["tg_tag".$tgNr]);
		$tpl->setVariable("COST_EACH"," ");
		$tpl->setVariable("COST_SUM",money_format($EUR_FMT,$tgRate));
		$tpl->parseCurrentBlock();
		if ($reduction > 0 ) {
			$tpl->setCurrentBlock("COST_ROW");
			$tpl->setVariable("COST_DESCR","- Abzug für ".$reductionDescr);
			$tpl->setVariable("COST_UNIT"," ");
			$tpl->setVariable("COST_EACH"," ");
			$tpl->setVariable("COST_SUM", money_format($EUR_FMT, $reduction > $tgRate? -1* $tgRate : -1 * $reduction));
			$tpl->parseCurrentBlock();
		}		
		$kosten+=$amount;
		$tgNr++;
	}	

	$tpl->setCurrentBlock();
	$tpl->setVariable("MASTER_SUM",money_format($EUR_FMT,$kosten));


//	$tpl->closingDelimiter=">#";
//	$tpl->openingDelimiter="#<";
	$tpl->removeUnknownVariables=false;
	$tpl->removeEmptyBlocks=true;


	fwrite($paramFile,$tpl->get());
	fclose($paramFile);

//	print_r($_REQUEST);
	Header("Content-type: application/pdf");
	header('Content-Disposition: attachment; filename="reko.pdf"');
	
	system("/srv/bin/tmppdf.sh $tmp");
} else {


	$main = new HTML_Template_IT("templates/");

	$main->loadTemplateFile("tpl.reko.html");

	$main->setVariable("BIS14_RATE",$bis14);
	$main->setVariable("BIS24_RATE",$bis24);
	$main->setVariable("BIS24_F_RATE",sprintf("%.2f",$bis24*0.2));
	$main->setVariable("BIS24_M_RATE",sprintf("%.2f",$bis24*0.4));
	$main->setVariable("BIS24_A_RATE",sprintf("%.2f",$bis24*0.4));

	$main->setVariable("BIS14_RATE",$bis14);
	$main->setVariable("BIS14_F_RATE",sprintf("%.2f",$bis14*0.2));
	$main->setVariable("BIS14_M_RATE",sprintf("%.2f",$bis14*0.4));
	$main->setVariable("BIS14_A_RATE",sprintf("%.2f",$bis14*0.4));

	$main->setVariable("UEBER24_RATE",$ueber24);
	$main->setVariable("UEBER24_F_RATE",sprintf("%.2f",$ueber24*0.2));
	$main->setVariable("UEBER24_M_RATE",sprintf("%.2f",$ueber24*0.4));
	$main->setVariable("UEBER24_A_RATE",sprintf("%.2f",$ueber24*0.4));

	$main->setVariable("PKW_KM_RATE",$pkwKm);
	$main->setVariable("MITFAHRER_RATE",$mitfahrerKm);
	$main->setVariable("DATE",strftime("%d.%m.%Y"));
	$main->show();	
}
?>
