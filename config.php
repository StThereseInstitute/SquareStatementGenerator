<?php

// ************ $CardBrands array 
// Brands of cards / txn types to report on.
//Syntax:
// '[Card Brand, as contained in "Card Brand" column of the CSV file]' => array('default'=>'[true=check by default, false=unchecked by default]', 'icon'=>'[icon css class name, as defined in styles.css or inline]');
$CardBrands = array(
	'Interac'=> array ('default'=>true, 'icon'=>'icon-tender_debit') ,
	'MasterCard'=> array ('default'=>true, 'icon'=>'icon-tender_mastercard') ,
	'Visa'=> array ('default'=>true, 'icon'=>'icon-tender_visa') ,
	'American Express'=> array ('default'=>true, 'icon'=>'icon-tender_amex') ,
	'Cash'=> array ('default'=>false, 'icon'=>'icon-tender_cash') ,
	// OTHER must be the last item in the list as it acts as a catch-all for everything not defined above!
	'Other'=> array ('default'=>true, 'icon'=>'icon-tender_other')
);
// end of $CardBrand array ***********************

?>