<?php 
require_once('config.php');
// ^^^^^^ make sure this list includes the cardbrands you wish to report on. Additional cards need to be added into the "switch ($txn_row['Card Brand'])"" statement in report.php prior to using.
// To set the checkbox to be checked by default, set the value to 1. Unchecked by default, set to 0.
//$CardBrand_default = array_combine($CardBrands, array(1,1,1,1,0,1));

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Square Statement</title>
<link href="styles.css" rel="stylesheet" type="text/css">
</head>

<body>
	<div class="logo"><table align="center">
<tr>
	<td align="right"><span style="font-weight: 100;">custom<br />
		statement</span></td><td><img src="images/Square-logo-(grey).png" alt="[•] Square"/></td></tr></table></div><hr />
			<form action="report.php" method="post" name="form1" id="form1" enctype="multipart/form-data">
	<div id="CSVpaste" >
		<p>&nbsp;		</p>

		<p>
			<label for="CSV"><strong>Go to <a href="https://squareup.com/dashboard/sales/transactions">squareup.com/dashboard/sales/transactions</a>, set your date range and select [EXPORT &gt; Transactions CSV] then [Open with &gt; Notepad]. <br>
				When the CSV displays in NotePad, [Select All &gt; Copy] then [Paste] the contents of the CSV here:</strong><br>
			</label>
			<textarea name="CSV" rows="10" id="CSV"></textarea>
		</p>
		<p>
			<input type="submit" name="submit" id="submit" value="Make something AWESOME!" style="background-color: #5376F9; color: #FFF; font-size: 16px; padding: 5px 10px; font-weight: bold;">
		</p>
		
		<div class="options_wrapper"><hr style="margin-top:0px;" /><div class="options"> <h3>OPTIONS:</h3><p>
			<label for="batchclose_hour"><strong>What time does your batch close?</strong></label>
			<select name="batchclose_hour" id="batchclose_hour">
				<?php if (isset($_REQUEST['batchclose'])) $batchclose = intval($_REQUEST['batchclose']); else $batchclose=0;
				for ($bc_counter=0; $bc_counter<=23; $bc_counter++){
				?>
				<option value="<?php echo $bc_counter;?>"
						<?php if ($batchclose == $bc_counter) echo 'selected="select"'; ?>
						>
						<?php echo date ('h:i a',mktime($bc_counter,0,0,1,1,2000));
						if ($bc_counter==0) echo ' midnight';
						else if ($bc_counter==12) echo ' noon &nbsp;.&nbsp;.';
						else echo ' &nbsp;.&nbsp;.&nbsp;.&nbsp;.&nbsp;.&nbsp;.';
						echo ' ('.sprintf('%02d', $bc_counter).':00)';
					?>
				</option>
				<?php } ?>
			</select>
		<div>
				<label for="TitleDate"><strong>Title as a MONTHLY REPORT or  title with EXACT DATE RANGE contained in the CSV?</strong><br>
			</label>
			<select name="TitleDate" id="TitleDate">
				<option value="MONTH" selected="select">Monthly Report</option>
					<option value="RANGE">Date Range from CSV</option>
			</select>
			<br>
			<em style="font-weight: normal; font-size: 0.8em;">"Monthly" will title the according to the earliest transaction date in the CSV data.<br>Please use "Date Range" if your CSV contains more than one calendar month worth of tranactions.</em><br>
&nbsp;		</div>
		<div>
				<label for="SortOrder"><strong>Sort order to display the transactions in?</strong><br>
			</label>
			<select name="SortOrder" id="SortOrder">
				<option value="ASC" >Ascending (Earliest transactions at top)</option>
				<option value="DESC" selected="select">Decending (Lastest transactions at top)</option>
			</select>
			<br>
&nbsp;		</div>
		
		
		<div><strong>Which transactions types do you want to show in the report?</strong><br />
			<?php
			
			foreach ($CardBrands as $CardBrand=>$values) {
			?>
				<input type="checkbox" name="ShowTxnTypes[]" value="<?php echo $CardBrand; ?>" id="ShowTxnTypes_<?php echo str_replace(' ','',$CardBrand); ?>"  <?php if ($values['default']) echo 'checked'; ?>>
			<label for="ShowTxnTypes_<?php echo str_replace(' ', '', $CardBrand); ?>" class="<?php echo ($values['default'])?'default':''; ?>">
				<?php echo $CardBrand; ?> <i class="<?php echo $values['icon'] ?>" style="display:inline-block;"></i>
			</label> &nbsp;&nbsp;
			<?php } ?>
			
		</div>
		
			</p></div></div>

</div>		</form>

</body>
</html>
