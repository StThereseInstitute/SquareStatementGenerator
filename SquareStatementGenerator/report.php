<?php 
// Error reporting ON
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// begin variable setup
require_once('config.php'); // loads $CardBrands array (and more if others have been added)
$batchclose_hour = (isset($_POST['batchclose_hour']))?$_POST['batchclose_hour']:0;
$CardBrands_selected = (isset($_POST['ShowTxnTypes']))?$_POST['ShowTxnTypes']:die('No transaction types selected.');
htmlspecialchars_array ($CardBrands_selected); // probably unnecessary... but would rather try to prevent possible injection attacks it someone were to modify the html select entries than fall victem to some attack because I didn't include one line of code...



//$CardBrands_icons = (isset($_POST['TxnTypesIcon']))?array_combine($CardBrands,$_POST['TxnTypesIcon']):die('Error. No transaction type icons defined.');
//echo '<pre>';
//echo '$CardBrands_selected <br>';
//print_r($CardBrands_selected); 
//echo 'CardBrands_icons <br>';
//print_r($CardBrands_icons);
//die();
//echo '</pre>';

if (in_array('Cash',$CardBrands_selected,true)) $showcash = true; else $showcash= false; // TO BE REMOVED ONCE 100% CODED-OUT

// runs htmlspecialchars() on single- & multi-dim array to prevent injection attack attempts (from http://hawkee.com/snippet/8641/)
// usage syntax: htmlspecialchars_array([your-array]);
function htmlspecialchars_array(&$variable) {
    foreach ($variable as &$value) {
        if (!is_array($value)) { $value = htmlspecialchars($value); }
        else { htmlspecialchars_array($value); }
    }
}

// The nested array to hold all the arrays
$transactions = array();;

// Check for POST data
if (!isset($_POST['CSV'])) die( 'CSV data does not exist!' );
$CSV = $_POST['CSV'];
// Prep the CSV data
htmlspecialchars($CSV); // make in safe in case of any malicious code injection attempts
$data = str_getcsv($CSV, "\n"); //parse the row
$rowcounter=0;
// dump the CSV data into the the arrays
foreach($data as &$row) {
	$row = str_getcsv($row, ",");
	if ($rowcounter==0) $transactions_header = $row;
	else $transactions[] = array_combine($transactions_header,$row);
	$rowcount[$rowcounter] = count($row);
	$rowcounter++;
}
$TxnDates = array_column($transactions, 'Date');
array_multisort($TxnDates, (($_POST['SortOrder'])=="DESC")?SORT_DESC:SORT_ASC, $transactions);

/* echo "<div style='display:none;'>rowcount:<br>";
print_r($rowcount);echo "<pre>";

echo "<hr>transactions_header:<br>";
print_r($transactions_header);
echo "<hr>transactions:<br>";
print_r($transactions );
echo "</pre></div>"; 
//die (); 
*/

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Square Statement</title>
<link href="styles.css" rel="stylesheet" type="text/css">
<!-- <link href="https://fonts.googleapis.com/css?family=Almarai|Roboto&display=swap" rel="stylesheet"> -->
<!-- <script src="https://kit.fontawesome.com/20e9d6cd18.js" crossorigin="anonymous"></script> --> 
</head>

<body>
	<div class="logo"><table align="center">
<tr>
	<td align="right"><span style="font-weight: 100;">custom<br />
		statement</span></td><td><img src="images/Square-logo-(grey).png" alt="[•] Square"/></td></tr></table></div>
	<table border="0" cellpadding="0" cellspacing="0" class="wrap_table" >
		<thead><tr><th>Report Dates: 
			<?php 
				if ($_POST['TitleDate']=="RANGE") {
					if ($_POST['SortOrder']=="DESC") echo end($transactions)['Date'].' – '.$transactions[0]['Date'];
					if ($_POST['SortOrder']=="ASC") echo $transactions[0]['Date'].' – '.end($transactions)['Date'];
				} else {
					// Force DD/MM/YY date to DD-MM-20YY instead for PHP compatibility
					$statementdate=explode('/',$transactions[0]['Date']);
					$statementdate[2] =+ 2000; // not Y2.1K compatible. Will only work from 2000-2099, but I *really* hope Square will provide statements by then!?!?!??! Maybe?!?!?!
					$statementdate = strtotime(implode("-",$statementdate));
					echo date('F 01–t, Y', $statementdate);
				}
			 	echo '<br /><em style="font-size:0.8em;">(batch close: '.str_pad($batchclose_hour, 2, '0', STR_PAD_LEFT).':00 daily)</em>'; ?>
	
			
		</th></tr></thead><tbody><tr><td class="report_totalsums_wrap">
		<table class="report_sum report_totalsums">
		<thead class="report_head">
			<tr>
			<th>Total<br />Gross
			</th>
			<th>Total<br />Fees
			</th>
			<th>Total<br />Net
			</th>
		</tr>
		</thead>
		<tbody><tr>
		<td class="report_totalsum"><div id="report_totalsum-Gross">-</div>
		<td class="report_totalsum"><div id="report_totalsum-Fee">-</div></td>
		<td class="report_totalsum"><div id="report_totalsum-Net">-</div></td>
		</td></tr></tbody>
	</table></td></tr></tbody>
	<?php 
	$total_sumGross = 0;
	$total_sumFee = 0;
	$total_sumNet = 0;
	$date = '';
	
	$counters = array_fill_keys($CardBrands_selected,0);
	$cardSums = array_fill_keys($CardBrands_selected,0);
	$total_sum = array('Total_SumGross_card'=>0,'Total_SumGross_cash'=>0,'Total_SumFee_card'=>0,'Total_SumNet_card'=>0);
	// $transactions = array_reverse($transactions) // for reverse order

	foreach ($transactions as &$txn_row) {
		if ($txn_row['Card Brand']=='') $txn_row['Card Brand'] = 'Cash';
		if (!in_array($txn_row['Card Brand'], $CardBrands_selected)) continue; // skip if this txn is not wanted for display
		$Gross = floatval(preg_replace('/[^\d\.\-]/', '', $txn_row['Total Collected']));
		$Fee = floatval(preg_replace('/[^\d\.\-]/', '', $txn_row['Fees']));
		$Net = floatval(preg_replace('/[^\d\.\-]/', '', $txn_row['Net Total']));

		$counters[$txn_row['Card Brand']] ++; 
		$cardSums[$txn_row['Card Brand']] += $Gross;
		
		if ($txn_row['Card Brand']=='Cash') { // CASH transaction
			$total_sum['Total_SumGross_cash'] += $Gross;
		} else { 
			$total_sum['Total_SumGross_card'] += $Gross;
			$total_sum['Total_SumFee_card'] += $Fee;
			$total_sum['Total_SumNet_card'] += $Net;
		}
		//if (($txn_row['Card Brand']=='') && ($showcash===false)) goto skipcash;
		$date_mod='';
		if ((intval(substr($txn_row['Time'],0,2))>=$batchclose_hour)&&($batchclose_hour!=0)){// TIME CUTOFF
			 $date_explode = explode('/',$txn_row['Date']);
			$date_month = str_pad($date_explode[0], 2, '0', STR_PAD_LEFT);
			$date_day = str_pad($date_explode[1], 2, '0', STR_PAD_LEFT);
			$date_year = '20'.str_pad($date_explode[2], 2, '0', STR_PAD_LEFT);
			$date_mod = new DateTime("$date_year-$date_month-$date_day ".$txn_row['Time']);
			$date_mod->modify('+1 day');
			$date_mod = ' (posted '.$date_mod->format("m/d/y").')';
			//$date .= $date_mod; 
		}
		if (($date!=$txn_row['Date'])||($date_mod!=$date_modcheck)) { 
			if ($date!=''){ 
				echo ('</table></td></tr>');
				 ?>
		<script>
					document.getElementById("report_sum-Gross-<?php echo $dateid; ?>").innerHTML = "$<?php echo number_format($sumGross,2); ?>";
					document.getElementById("report_sum-Fee-<?php echo $dateid; ?>").innerHTML = "$<?php echo number_format($sumFee,2); ?>";
					document.getElementById("report_sum-Net-<?php echo $dateid; ?>").innerHTML = "$<?php echo number_format($sumNet,2); ?>";
				</script>
				<?php 
			}
			$date=$txn_row['Date'];
			$date_modcheck = $date_mod;
			if ($date_mod) $dateid = str_replace('/', '', $date).'x'; else $dateid = str_replace('/', '', $date);
			
			$sumGross = 0;
			$sumFee = 0;
			$sumNet = 0;
		?> 
		<tr id="<?php echo $dateid;  if ($date_mod) echo 'x'; ?>">
			<td class="report_date" >
				<?php  echo $date.$date_mod; ?>
				<div class="report_sum report_sum-Net" id="report_sum-Net-<?php echo $dateid; ?>"></div>
				<div class="report_sum report_sum-Fee" id="report_sum-Fee-<?php echo $dateid; ?>"></div>
				<div class="report_sum report_sum-Gross" id="report_sum-Gross-<?php echo $dateid; ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				<table class='report' id="report_<?php echo str_replace('/', '', $date);?>" >
					<thead class="report_head"><tr>
						<th>Type</th>
						<th>Time</th>
						<th>Description</th>
						<th>Gross</th>
						<th>Fee</th>
						<th>Net</th>
					</tr></thead>

		<?php } //end if 
		//if (($txn_row['Card Brand']=='') && ($showcash===false)) goto skipcash;

		
		$sumGross += $Gross;
		$sumFee += $Fee;
		$sumNet += $Net;
		//$total_sumGross += $sumGross;
		//$total_sumFee += $sumFee;
		//$total_sumNet += $sumNet;
		?>
					
					<tr class='report_row'>
						<td class="report_cardbrand">
							<i class="<?php echo $CardBrands[$txn_row['Card Brand']]['icon']; ?>"></i>
							<div class="report_cardbrand_text">
							<?php if ($txn_row['Card Brand']) {echo $txn_row['PAN Suffix'];} 
									else {echo "Cash Sale";} 
							?>
							</div>
							<div class="report_customername">
								<?php echo $txn_row['Customer Name'];?>
							</div>
						</td>
						<td class='report_time'><?php echo $txn_row['Time']; ?></td>
						<td class='report_disc'><?php echo $txn_row['Description']; ?></td>
						<td class='report_gross'>
							<?php 
								
								if ($txn_row['Total Collected'][1] == '-') 
									$moneyformat = array('<span class="neg_money">', '</span>');
								else $moneyformat = array('','');
								echo $moneyformat[0].$txn_row['Total Collected'].$moneyformat[1];
							?>
						</td>
						<td class='report_fee'><?php if ($txn_row['Fees'] != '$0.00') echo $txn_row['Fees']; else echo '–'; ?></td>
						<td class='report_net'><?php echo $moneyformat[0].$txn_row['Net Total'].$moneyformat[1]; ?></td>
					</tr>
			

	<?php
		skiploop:
	} // end FOREACH?>
				</table>
			</td>
		</tr>	
	</table>

<?php
	$total_sumGross = $total_sum['Total_SumGross_card'];
	$total_sumFee = $total_sum['Total_SumFee_card'];
	$total_sumNet = $total_sum['Total_SumNet_card'];
	if ($showcash) {
		$total_sumGross += $total_sum['Total_SumGross_cash'];
		$total_sumNet += $total_sum['Total_SumGross_cash'];
	}

?>
	
<script>
		document.getElementById("report_sum-Gross-<?php echo $dateid; ?>").innerHTML = "$<?php echo number_format($sumGross,2); ?>";
		document.getElementById("report_sum-Fee-<?php echo $dateid; ?>").innerHTML = "$<?php echo number_format($sumFee,2); ?>";
		document.getElementById("report_sum-Net-<?php echo $dateid; ?>").innerHTML = "$<?php echo number_format($sumNet,2); ?>"; 

	document.getElementById("report_totalsum-Gross").innerHTML = "$<?php echo number_format($total_sumGross,2); ?>";
		document.getElementById("report_totalsum-Fee").innerHTML = "$<?php echo number_format($total_sumFee,2); ?>";
		document.getElementById("report_totalsum-Net").innerHTML = "$<?php echo number_format($total_sumNet,2); ?>";
	</script>
	
<?php	
	//if (!$showcash) { $counters['Cash']=$counters['Cash'].', but not included'; }?>
<br />
	<div class="summary">
	<table>
		<tr><td align="left" valign="top">&nbsp; Total transactions: <?php echo array_sum($counters); ?> &nbsp;</td>
		<td align="left" valign="top">&nbsp; Breakdown:</td>
		<td align="left" valign="top">
	<?php 
	$breakdown = explode('[',print_r($counters,true));
	unset($breakdown[0]);
		$i=0;
	foreach ($counters as $key=>$value) {
		if ($i++ ==3) echo ('</td><td align="left" valign="top">');
		echo '<i class="'.$CardBrands[$key]['icon'].'"></i>'.$key.': '.$value.' @ $'.number_format($cardSums[$key],2).' &nbsp;<br />';
	}
	?></td></tr></table>
</div>

</body>
</html>
