<?php
  require __DIR__ . '/vendor/autoload.php';

  if (isset($_REQUEST["profile"])) {
    $meta = yaml_parse_file($_REQUEST["profile"].".yml");
  }

  $cfg = yaml_parse_file("vars.yml");

  $EUR_FMT="%!.2n €";

  $smarty = new Smarty;

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    setlocale(LC_MONETARY, 'de_DE');
	$tmp = tempnam('/tmp','reko');

	$paramFile = fopen($tmp,"w");

	//echo $tmp;


//	print_r($_REQUEST);


  $reko_data = array();
  
	$reko_data["datum"] = strftime("%d.%m.%Y");
	$common = array(  "name","strasse","ort","fkt","iban","bic","bank","grund","beginn", "von","nach","ueber","beginnZeit","ende","endeZeit");

	$kostenLabel = array( "hotel","bahn","oeff","taxi","pkwkm","sonst");


	echo "Ueber: "+$_REQUEST["ueber"];
	$route = $_REQUEST["von"];

	if (isset ($_REQUEST["ueber"]) && strlen($_REQUEST["ueber"])>0) {
		$route.="+ über ".$_REQUEST["ueber"];
	}
	$route.= " nach ".$_REQUEST["nach"];

  $reko_data["route"]=$route;

	foreach  ( $common as  $key ) {
    $reko_data[$key] = $_REQUEST[$key];
	}

  $reko_data["kosten"] = array();

	$kosten =0;
	foreach ( $kostenLabel as $kst ) {
    $row = array();

		if (! strcmp("pkwkm",$kst)) {
			$val = $_REQUEST[$kst]*$cfg["tariff"]["pkwKm"];
			if ( $val > $cfg["tariff"]["pkwMax"] && ! $cfg["tariff"]["pkwNoLimit"]) { 
				$val = $cfg["tariff"]["pkwMax"];
			}
    
			$row["descr"] = $cfg["labels"]["pkwkm"];
			$row["unit"] = sprintf("%d km",$_REQUEST[$kst]);
			$row["each"] = "à ".money_format($EUR_FMT,$cfg["tariff"]["pkwKm"]);
			$row["sum"] = money_format($EUR_FMT,$val);
		} else {
			$val = $_REQUEST[$kst]*1.0;
			$row["descr"] = $cfg["labels"][$kst];
			$row["unit"] = " ";
			$row["each"] = " "; 
			$row["sum"] = money_format($EUR_FMT,$val);
		}
		$kosten+=$val;
		if ( $val >0 ) {
      $reko_data["kosten"][] = $row;
		}
	}

	if ( isset ($_REQUEST["abweichungen"]) && strlen($_REQUEST["abweichungen"])>0) {
    $reko_data["abweichungen"] = "\\textbf{".$cfg["labels"]["abweichungen"]."} \\\\ ".$_REQUEST["abweichungen"]."\\\\";
	} else {
		$reko_data["abweichungen"] = " ";
	}
/*Tagegeld & 1.1.2011  & & 12 € \\
- Abzug Frühstück,Mittagessen,Abendessen & & & -12.00 € \\
Tagegeld & 2.1.2011 & & 12.00 € \\
- Abzug Frühstück, Abendessen & & 10.00 € \\
Tagegeld & 3.1.2011 & & 12.00 € \\
- Abzug Frühstück, Abendessen & & & 10.00 € \\*/

	$tgNr =1;
	while (isset ($_REQUEST["tg_sum".$tgNr]) ) {
		$amount = $_REQUEST["tg_sum".$tgNr];
		$tgRate = $_REQUEST["tg_tariff".$tgNr];

    error_log("tgRate: ".$tgRate." amount:".$amount);
		$reduction =0;
		$reductionDescr="";

		if (array_key_exists("tg_f".$tgNr,$_REQUEST)) {
			$reduction+=$cfg["tariff"]["ueber24"]*0.2; 
			$reductionDescr="Frühstück";
		}

		if (array_key_exists("tg_m".$tgNr,$_REQUEST)) { 
			if ($reduction>0) { $reductionDescr.=", "; }
			$reduction+=$cfg["tariff"]["ueber24"]*0.4; 
			$reductionDescr.="Mittagessen";
		}

		if (array_key_exists("tg_a".$tgNr,$_REQUEST)) { 
			if ($reduction>0) { $reductionDescr.=", "; }
			$reduction+=$cfg["tariff"]["ueber24"]*0.4; 
			$reductionDescr.="Abendessen";
		}

    $row = array();

		$row["descr"] = $cfg["labels"]["tg"];
		$row["unit"] = $_REQUEST["tg_tag".$tgNr];
		$row["each"] = " ";
		$row["sum"] = money_format($EUR_FMT,$tgRate);
    
    $reko_data["kosten"][] = $row;

		if ($reduction > 0 ) {
      $row = array();
			$row["descr"] = "- Abzug für ".$reductionDescr;
			$row["unit"] = " ";
			$row["each"] = " ";
			$row["sum"] = money_format($EUR_FMT, $reduction > $tgRate? -1* $tgRate : -1 * $reduction);
      $reko_data["kosten"][] = $row;

//      $amount = $tgRate -$reductio;
//      if ( $amount < 0 ) { $amount =0;}
		}	
		$kosten+=$amount;
	  error_log("kosten: ".$kosten);
		$tgNr++;
	}


  $reko_data["master_sum"] = 	money_format($EUR_FMT,$kosten);


  $smarty->assign("reko_data",$reko_data);

  $smarty->left_delimiter = "<%"; 
  $smarty->right_delimiter = "%>"; 


  $output = $smarty->fetch('templates/reko.tex.tpl'); 

	fwrite($paramFile,$output);
	fclose($paramFile);

//	print_r($_REQUEST);
	Header("Content-type: application/pdf");
	header('Content-Disposition: attachment; filename="reko.pdf"');
	
	system("/srv/bin/tmppdf.sh $tmp");
} else {


  // PROFILE

  if (isset($meta)) {
    $smarty->assign("meta",$meta);
  }

  $smarty->assign("cfg",$cfg);

  $rates= array();

  $bis24 = $cfg["tariff"]["bis24"];
  $rates["bis24"]["F"]=$bis24*0.2;
  $rates["bis24"]["M"]=$bis24*0.4;
  $rates["bis24"]["A"]=$bis24*0.4;
  $rates["ueber24"]["F"]=$ueber24*0.2;
  $rates["ueber24"]["M"]=$ueber24*0.4;
  $rates["ueber24"]["A"]=$ueber24*0.4;

  $smarty->assign("rates",$rates);

  $smarty->assign("date",strftime("%d.%m.%Y"));
  
  $smarty->display("templates/reko.tpl");
}
?>
