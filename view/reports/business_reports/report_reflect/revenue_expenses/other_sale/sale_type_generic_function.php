<?php
function get_sale_purchase($sale_type, $from_date ,$to_date)
{
	$sale_array = array();
	$total_sale = 0;
	$total_purchase = 0;
	$total_expense = 0;
///All Start
if($sale_type == 'All'){
	//Sale
	$str_q="select *  from tourwise_traveler_details where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_q .= "and form_date BETWEEN '$from_date' AND '$to_date'";
	}
	$q1 = mysql_query($str_q);
	
while($tourwise_details = mysql_fetch_assoc($q1)){
	$sq_sum = mysql_fetch_assoc(mysql_query("select sum(basic_amount) as incentive_amount from booker_incentive_group_tour where tourwise_traveler_id='$tourwise_details[id]'"));
	$incentive_amount = $sq_sum['incentive_amount'];
	//Cancel consideration
	$sq_tr_refund = mysql_num_rows(mysql_query("select * from refund_traveler_estimate where tourwise_traveler_id='$tourwise_details[id]'"));
	$sq_tour_refund = mysql_num_rows(mysql_query("select * from refund_tour_estimate where tourwise_traveler_id='$tourwise_details[id]'"));
	$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from package_payment_master where booking_id='$tourwise_details[booking_id]' and clearance_status!='Cancelled'"));
	$credit_charges = $sq_paid_amount['sumc'];

	if($sq_tour_refund == '0' || $sq_tr_refund == '0'){
		$actual_travel_expense = $tourwise_details['total_travel_expense'];
		$actual_tour_expense = $tourwise_details['total_tour_fee'];
		$sale_amount = $tourwise_details['net_total'] - $incentive_amount;
		$tax_amount = $tourwise_details['train_service_tax_subtotal'] + $tourwise_details['plane_service_tax_subtotal'] + $tourwise_details['cruise_service_tax_subtotal'] + $tourwise_details['visa_service_tax_subtotal'] + $tourwise_details['insuarance_service_tax_subtotal'] + $tourwise_details['service_tax'];
		$sale_amount -= $tax_amount;
		$total_sale += $sale_amount;
	}

$total_sale += $credit_charges;

// Purchase
$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Group Tour' and estimate_type_id ='$tourwise_details[tour_group_id]' and status!='Cancel'");
while($row_purchase = mysql_fetch_assoc($sq_purchase)){
	$total_purchase += $row_purchase['net_total'] ;
	$total_purchase -= $row_purchase['service_tax_subtotal'];
}

//Other Expense
$sq_other_purchase = mysql_fetch_assoc(mysql_query("select sum(amount) as amount_total from group_tour_estimate_expense where tour_id='$tourwise_details[tour_id]' and tour_group_id ='$tourwise_details[tour_group_id]'"));
$total_purchase += $sq_other_purchase['amount_total'];

}
//Revenue & Expenses
$result = $total_sale - $total_purchase;

//Sale
$str_p="select * from package_tour_booking_master where 1 ";
if($from_date != '' && $to_date !='')
{
	$str_p .= "and booking_date BETWEEN '$from_date' AND '$to_date'";
}
$package_query=mysql_query($str_p);
while($tourwise_details = mysql_fetch_assoc($package_query)){
	$sq_sum = mysql_fetch_assoc(mysql_query("select sum(basic_amount) as incentive_amount from booker_incentive_package_tour where booking_id='$tourwise_details[booking_id]'"));
$incentive_amount = $sq_sum['incentive_amount'];
//Cancel consideration
$sq_tr_refund = mysql_num_rows(mysql_query("select * from package_refund_traveler_cancalation_entries where tourwise_traveler_id='$tourwise_details[booking_id]'"));
$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from package_payment_master where booking_id='$tourwise_details[booking_id]' and clearance_status!='Cancelled'"));
$credit_charges = $sq_paid_amount['sumc'];
$tax_amount1 = 0;
if($sq_tr_refund == ''){
	$actual_travel_expense = $tourwise_details['total_travel_expense'];
	$actual_tour_expense = $tourwise_details['actual_tour_expense'];
	$total_sale += $tourwise_details['net_total'] - $incentive_amount;
	$tax_amount = $tourwise_details['tour_service_tax_subtotal'];
	$tax_amount = explode(":",$tax_amount);
	$tax_amount1 = $tax_amount1 + $tax_amount[2];

	$total_sale -= $tax_amount1;
}
$total_sale += $credit_charges;

// Purchase
$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Package Tour' and estimate_type_id ='$tourwise_details[booking_id]' and status!='Cancel'");
while($row_purchase = mysql_fetch_assoc($sq_purchase)){
	$total_purchase += $row_purchase['net_total'];
	$total_purchase -= $row_purchase['service_tax_subtotal'];
}

//Other Expense
$sq_other_purchase = mysql_fetch_assoc(mysql_query("select sum(amount) as amount_total from package_tour_estimate_expense where booking_id='$tourwise_details[booking_id]' "));
$total_purchase += $sq_other_purchase['amount_total'];

//Revenue & Expenses
$result = $total_sale - $total_purchase;
}
///Visa Start
	//Sale
	$str_v="select * from visa_master where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_v .= "and created_at BETWEEN '$from_date' AND '$to_date' order by visa_id desc";
	}
		$sq_query = mysql_query($str_v);
		while ($row_visa = mysql_fetch_assoc($sq_visa)) {
		$sq_visa_entry = mysql_num_rows(mysql_query("select * from visa_master_entries where visa_id='$row_visa[visa_id]'"));
		$sq_visa_cancel = mysql_num_rows(mysql_query("select * from visa_master_entries where visa_id='$row_visa[visa_id]' and status = 'Cancel'"));
		
		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if($row_visa['service_tax_subtotal'] !== 0.00 && ($row_visa['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_visa['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			$service_tax = explode(':',$service_tax_subtotal1[$i]);
			$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if($row_visa['markup_tax'] !== 0.00 && $row_visa['markup_tax'] !== ""){
			$service_tax_markup1 = explode(',',$row_visa['markup_tax']);
			for($i=0;$i<sizeof($service_tax_markup1);$i++){
			$service_tax = explode(':',$service_tax_markup1[$i]);
			$markupservice_tax_amount += $service_tax[2];
		
			}
		}
		$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from visa_payment_master where visa_id='$row_visa[visa_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];

		if($sq_visa_entry != $sq_visa_cancel){
			$total_sale += $row_visa['visa_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;
		}	
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Visa Booking' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Visa End

///Passport Start
	//Sale
	$str_pass="select * from passport_master where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_pass .= "and created_at BETWEEN '$from_date' AND '$to_date' order by passport_id desc";
	}
	$sq_passport = mysql_query($str_pass);
	while ($row_forex = mysql_fetch_assoc($sq_passport)) {
		
		$sq_passport_entry = mysql_num_rows(mysql_query("select * from passport_master_entries where passport_id='$row_forex[passport_id]'"));
		$sq_passport_cancel = mysql_num_rows(mysql_query("select * from passport_master_entries where passport_id='$row_forex[passport_id]' and status = 'Cancel'"));
		
		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if($row_forex['service_tax_subtotal'] !== 0.00 && ($row_forex['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_forex['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			$service_tax = explode(':',$service_tax_subtotal1[$i]);
			$service_tax_amount +=  $service_tax[2];
			}
		}
		$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from passport_payment_master where passport_id='$row_forex[passport_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];

		if($sq_passport_entry != $sq_passport_cancel){
			$total_sale += $row_forex['passport_total_cost'] - $service_tax_amount + $credit_charges;
		}
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Passport Booking' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Passport End
///Excursion Start
	//Sale
	$str_e="select * from excursion_master where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_e .= "and created_at BETWEEN '$from_date' AND '$to_date' order by exc_id desc";
	}
	$sq_exc = mysql_query($str_e);
		while ($row_exc = mysql_fetch_assoc($sq_exc)) {
		$sq_exc_entry = mysql_num_rows(mysql_query("select * from excursion_master_entries where exc_id='$row_exc[exc_id]'"));
		$sq_exc_cancel = mysql_num_rows(mysql_query("select * from excursion_master_entries where exc_id='$row_exc[exc_id]' and status = 'Cancel'"));
		//// Calculate Service Tax//////
		$service_tax_amount = 0;
		if($row_exc['service_tax_subtotal'] !== 0.00 && ($row_exc['service_tax_subtotal']) !== ''){
		$service_tax_subtotal1 = explode(',',$row_exc['service_tax_subtotal']);
		for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
		  $service_tax = explode(':',$service_tax_subtotal1[$i]);
		  $service_tax_amount +=  $service_tax[2];
		  }
		}
  
		//// Calculate Markup Tax//////
  
		$markupservice_tax_amount = 0;
		if($row_exc['service_tax_markup'] !== 0.00 && $row_exc['service_tax_markup'] !== ""){
		$service_tax_markup1 = explode(',',$row_exc['service_tax_markup']);
		for($i=0;$i<sizeof($service_tax_markup1);$i++){
		  $service_tax = explode(':',$service_tax_markup1[$i]);
		  $markupservice_tax_amount += $service_tax[2];
  
		  }
		}
		$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from exc_payment_master where exc_id='$row_exc[exc_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];

		if($sq_exc_entry != $sq_exc_cancel){ 		
			$total_sale += $row_exc['exc_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;
		}	
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Excursion Booking' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Excursion End	
///Forex Start
	//Sale
	$str_f="select * from forex_booking_master order by booking_id desc where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_f .= "and created_at BETWEEN '$from_date' AND '$to_date'";
	}
	$sq_forex = mysql_query($str_f);
		while ($row_forex = mysql_fetch_assoc($sq_forex)) {
		
		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if($row_forex['service_tax_subtotal'] !== 0.00 && ($row_forex['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_forex['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			$service_tax = explode(':',$service_tax_subtotal1[$i]);
			$service_tax_amount +=  $service_tax[2];
			}
		}
		$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from forex_booking_payment_master where booking_id='$row_forex[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];

		$total_sale += $row_forex['net_total'] - $service_tax_amount + $credit_charges;
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Forex Booking' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Forex End
///Bus Start
	//Sale
	$str_b="select * from bus_booking_master where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_b .= "and created_at BETWEEN '$from_date' AND '$to_date' order by booking_id desc";
	}
		$sq_bus = mysql_query($str_b);
	while ($row_bus = mysql_fetch_assoc($sq_bus)) {
		
		$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from bus_booking_payment_master where booking_id='$row_bus[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];

		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if($row_bus['service_tax_subtotal'] !== 0.00 && ($row_bus['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_bus['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			$service_tax = explode(':',$service_tax_subtotal1[$i]);
			$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if($row_bus['markup_tax'] !== 0.00 && $row_bus['markup_tax'] !== ""){
			$service_tax_markup1 = explode(',',$row_bus['markup_tax']);
			for($i=0;$i<sizeof($service_tax_markup1);$i++){
			$service_tax = explode(':',$service_tax_markup1[$i]);
			$markupservice_tax_amount += $service_tax[2];
		
			}
		}
		$sq_exc_entry = mysql_num_rows(mysql_query("select * from bus_booking_entries where booking_id='$row_bus[booking_id]'"));
		$sq_exc_cancel = mysql_num_rows(mysql_query("select * from bus_booking_entries where booking_id='$row_bus[booking_id]' and status = 'Cancel'"));
		if($sq_exc_entry != $sq_exc_cancel){
			$total_sale += $row_bus['net_total'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;
		}	
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Bus Booking' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Bus End

///Hotel Start
	//Sale
	$str_h="select * from hotel_booking_master where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_h .= "and created_at BETWEEN '$from_date' AND '$to_date' order by booking_id desc";
	}
	$sq_hotel = mysql_query($str_h);
		while ($row_hotel = mysql_fetch_assoc($sq_hotel)) {

		$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from hotel_booking_payment where booking_id='$row_hotel[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_charges = $sq_paid_amount['sumc'];
		//// Calculate Service Tax//////
		$service_tax_amount = 0;
		if($row_hotel['service_tax_subtotal'] !== 0.00 && ($row_hotel['service_tax_subtotal']) !== ''){
		$service_tax_subtotal1 = explode(',',$row_hotel['service_tax_subtotal']);
		for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
		$service_tax = explode(':',$service_tax_subtotal1[$i]);
		$service_tax_amount +=  $service_tax[2];
		}
		}

		//// Calculate Markup Tax//////

		$markupservice_tax_amount = 0;
		if($row_hotel['markup_tax'] !== 0.00 && $row_hotel['markup_tax'] !== ""){
		$service_tax_markup1 = explode(',',$row_hotel['markup_tax']);
		for($i=0;$i<sizeof($service_tax_markup1);$i++){
		$service_tax = explode(':',$service_tax_markup1[$i]);
		$markupservice_tax_amount += $service_tax[2];

		}
		}
		$sq_exc_entry = mysql_num_rows(mysql_query("select * from hotel_booking_entries where booking_id='$row_hotel[booking_id]'"));
		$sq_exc_cancel = mysql_num_rows(mysql_query("select * from hotel_booking_entries where booking_id='$row_hotel[booking_id]' and status = 'Cancel'"));
		if($sq_exc_entry != $sq_exc_cancel){
			$total_sale += $row_hotel['total_fee'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;
		}	
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Hotel Booking' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Hotel End
///Car Start
	//Sale
	$str_c="select * from car_rental_booking where status != 'Cancel' where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_c .= "and created_at BETWEEN '$from_date' AND '$to_date' order by booking_id desc ";
	}
	$sq_car = mysql_query($str_c);
		while ($row_car = mysql_fetch_assoc($sq_car)) {	
		$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(payment_amount) as sum ,sum(`credit_charges`) as sumc from car_rental_payment where booking_id='$row_car[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if($row_car['service_tax_subtotal'] !== 0.00 && ($row_car['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_car['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			$service_tax = explode(':',$service_tax_subtotal1[$i]);
			$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if($row_car['markup_cost_subtotal'] !== 0.00 && $row_car['markup_cost_subtotal'] !== ""){
			$service_tax_markup1 = explode(',',$row_car['markup_cost_subtotal']);
			for($i=0;$i<sizeof($service_tax_markup1);$i++){
			$service_tax = explode(':',$service_tax_markup1[$i]);
			$markupservice_tax_amount += $service_tax[2];
		
			}
		}

		$total_sale += $row_car['total_fees'] - $service_tax_amount -$markupservice_tax_amount + $sq_paid_amount['sumc'];
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Car Rental' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Car End
///Ticket Start
	//Sale
	$str_t="select * from ticket_master where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_t .= "and created_at BETWEEN '$from_date' AND '$to_date' order by ticket_id desc";
	}
	$sq_ticket = mysql_query($str_t);
		while ($row_ticket = mysql_fetch_assoc($sq_ticket)) {
		$sq_exc_entry = mysql_num_rows(mysql_query("select * from ticket_master_entries where ticket_id='$row_ticket[ticket_id]'"));
		$sq_exc_cancel = mysql_num_rows(mysql_query("select * from ticket_master_entries where ticket_id='$row_ticket[ticket_id]' and status = 'Cancel'"));
		
		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if($row_ticket['service_tax_subtotal'] !== 0.00 && ($row_ticket['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_ticket['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			$service_tax = explode(':',$service_tax_subtotal1[$i]);
			$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if($row_ticket['markup_tax'] !== 0.00 && $row_ticket['markup_tax'] !== ""){
			$service_tax_markup1 = explode(',',$row_ticket['markup_tax']);
			for($i=0;$i<sizeof($service_tax_markup1);$i++){
			$service_tax = explode(':',$service_tax_markup1[$i]);
			$markupservice_tax_amount += $service_tax[2];
		
			}
		}
		
		$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from ticket_payment_master where ticket_id='$row_ticket[ticket_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		
		if($sq_exc_entry != $sq_exc_cancel){
			$total_sale += $row_ticket['ticket_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_card_charges;
		}	
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Ticket Booking' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Ticket End
///Train Start
	//Sale
	$str_tm="select * from train_ticket_master where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_tm .= "and created_at BETWEEN '$from_date' AND '$to_date' order by train_ticket_id desc";
	}
	$sq_train = mysql_query($str_tm);
		while ($row_train = mysql_fetch_assoc($sq_train)) {
		$sq_exc_entry = mysql_num_rows(mysql_query("select * from train_ticket_master_entries where train_ticket_id='$row_train[train_ticket_id]'"));
		$sq_exc_cancel = mysql_num_rows(mysql_query("select * from train_ticket_master_entries where train_ticket_id='$row_train[train_ticket_id]' and status = 'Cancel'"));
		
		$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(payment_amount) as sum,sum(credit_charges) as sumc from train_ticket_payment_master where train_ticket_id='$row_train[train_ticket_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount['sumc'];
		
		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if($row_train['service_tax_subtotal'] !== 0.00 && ($row_train['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_train['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			$service_tax = explode(':',$service_tax_subtotal1[$i]);
			$service_tax_amount +=  $service_tax[2];
			}
		}
		
		if($sq_exc_entry != $sq_exc_cancel){
			$total_sale += $row_train['net_total'] - $service_tax_amount + $credit_card_charges;
		}	
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Train Ticket Booking' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Train End

///Miscellaneous Start
	//Sale
	$str_m="select * from miscellaneous_master where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_m .= "and created_at BETWEEN '$from_date' AND '$to_date' order by misc_id desc";
	}
	$sq_misc = mysql_query($str_m);
		while ($row_misc = mysql_fetch_assoc($sq_misc)) {
		
		$sq_paid_amount1 = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from miscellaneous_payment_master where misc_id='$row_misc[misc_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
		$credit_card_charges = $sq_paid_amount1['sumc'];
		
		//Service Tax and Markup Tax
		$service_tax_amount = 0;
		if($row_misc['service_tax_subtotal'] !== 0.00 && ($row_misc['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_misc['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			$service_tax = explode(':',$service_tax_subtotal1[$i]);
			$service_tax_amount +=  $service_tax[2];
			}
		}
		$markupservice_tax_amount = 0;
		if($row_misc['service_tax_markup'] !== 0.00 && $row_misc['service_tax_markup'] !== ""){
			$service_tax_markup1 = explode(',',$row_misc['service_tax_markup']);
			for($i=0;$i<sizeof($service_tax_markup1);$i++){
			$service_tax = explode(':',$service_tax_markup1[$i]);
			$markupservice_tax_amount += $service_tax[2];
			}
		}
		$sq_visa_entry = mysql_num_rows(mysql_query("select * from miscellaneous_master_entries where misc_id='$row_misc[misc_id]'"));
		$sq_visa_cancel = mysql_num_rows(mysql_query("select * from miscellaneous_master_entries where misc_id='$row_misc[misc_id]' and status = 'Cancel'"));
		if($sq_visa_entry != $sq_visa_cancel){
			$total_sale += $row_misc['misc_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_card_charges;
		}	
	}

	//Purchase
	$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Miscellaneous Booking' and status!='Cancel'");
	while($row_purchase = mysql_fetch_assoc($sq_purchase)){
		$total_purchase += $row_purchase['net_total'];
		$total_purchase -= $row_purchase['service_tax_subtotal'];
	}
///Miscellaneous End

}
	///Visa Start
	if($sale_type == 'Visa'){
		//Sale
		$str_v="select * from visa_master where 1 ";
		if($from_date != '' && $to_date !='')
		{
			$str_v .= "and created_at BETWEEN '$from_date' AND '$to_date' order by visa_id desc";
		}
		$sq_query = mysql_query($str_v);
			while ($row_visa = mysql_fetch_assoc($sq_query)) {
			$sq_visa_entry = mysql_num_rows(mysql_query("select * from visa_master_entries where visa_id='$row_visa[visa_id]'"));
			$sq_visa_cancel = mysql_num_rows(mysql_query("select * from visa_master_entries where visa_id='$row_visa[visa_id]' and status = 'Cancel'"));
			
			//Service Tax and Markup Tax
			$service_tax_amount = 0;
			if($row_visa['service_tax_subtotal'] !== 0.00 && ($row_visa['service_tax_subtotal']) !== ''){
				$service_tax_subtotal1 = explode(',',$row_visa['service_tax_subtotal']);
				for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
				$service_tax = explode(':',$service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
				}
			}
			$markupservice_tax_amount = 0;
			if($row_visa['markup_tax'] !== 0.00 && $row_visa['markup_tax'] !== ""){
				$service_tax_markup1 = explode(',',$row_visa['markup_tax']);
				for($i=0;$i<sizeof($service_tax_markup1);$i++){
				$service_tax = explode(':',$service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			
				}
			}
			$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from visa_payment_master where visa_id='$row_visa[visa_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
			$credit_charges = $sq_paid_amount['sumc'];

			if($sq_visa_entry != $sq_visa_cancel){
				$total_sale += $row_visa['visa_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;
			}	
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Visa Booking' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Visa End

	///Passport Start
	if($sale_type == 'Passport'){
		//Sale
		$str_pass="select * from passport_master where 1 ";
		if($from_date != '' && $to_date !='')
		{
			$str_pass .= "and created_at BETWEEN '$from_date' AND '$to_date' order by passport_id desc";
		}
		$sq_passport = mysql_query($str_pass);
				while ($row_forex = mysql_fetch_assoc($sq_passport)) {
			
			$sq_passport_entry = mysql_num_rows(mysql_query("select * from passport_master_entries where passport_id='$row_forex[passport_id]'"));
			$sq_passport_cancel = mysql_num_rows(mysql_query("select * from passport_master_entries where passport_id='$row_forex[passport_id]' and status = 'Cancel'"));
			
			//Service Tax and Markup Tax
			$service_tax_amount = 0;
			if($row_forex['service_tax_subtotal'] !== 0.00 && ($row_forex['service_tax_subtotal']) !== ''){
				$service_tax_subtotal1 = explode(',',$row_forex['service_tax_subtotal']);
				for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
				$service_tax = explode(':',$service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
				}
			}
			$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from passport_payment_master where passport_id='$row_forex[passport_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
			$credit_charges = $sq_paid_amount['sumc'];

			if($sq_passport_entry != $sq_passport_cancel){
				$total_sale += $row_forex['passport_total_cost'] - $service_tax_amount + $credit_charges;
			}
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Passport Booking' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Passport End
	///Excursion Start
	if($sale_type == 'Excursion'){
		//Sale
		$str_e="select * from excursion_master where 1 ";
		if($from_date != '' && $to_date !='')
		{
			$str_e .= "and created_at BETWEEN '$from_date' AND '$to_date' order by exc_id desc";
		}
		$sq_exc = mysql_query($str_e);
				while ($row_exc = mysql_fetch_assoc($sq_exc)) {
			$sq_exc_entry = mysql_num_rows(mysql_query("select * from excursion_master_entries where exc_id='$row_exc[exc_id]'"));
			$sq_exc_cancel = mysql_num_rows(mysql_query("select * from excursion_master_entries where exc_id='$row_exc[exc_id]' and status = 'Cancel'"));
			//// Calculate Service Tax//////
			$service_tax_amount = 0;
			if($row_exc['service_tax_subtotal'] !== 0.00 && ($row_exc['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_exc['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			  $service_tax = explode(':',$service_tax_subtotal1[$i]);
			  $service_tax_amount +=  $service_tax[2];
			  }
			}
	  
			//// Calculate Markup Tax//////
	  
			$markupservice_tax_amount = 0;
			if($row_exc['service_tax_markup'] !== 0.00 && $row_exc['service_tax_markup'] !== ""){
			$service_tax_markup1 = explode(',',$row_exc['service_tax_markup']);
			for($i=0;$i<sizeof($service_tax_markup1);$i++){
			  $service_tax = explode(':',$service_tax_markup1[$i]);
			  $markupservice_tax_amount += $service_tax[2];
	  
			  }
			}
			$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from exc_payment_master where exc_id='$row_exc[exc_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
			$credit_charges = $sq_paid_amount['sumc'];

			if($sq_exc_entry != $sq_exc_cancel){ 		
				$total_sale += $row_exc['exc_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;
			}	
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Excursion Booking' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Excursion End	
	///Forex Start
	if($sale_type == 'Forex'){
		//Sale
		$str_f="select * from forex_booking_master where 1 ";
		if($from_date != '' && $to_date !='')
		{
			$str_f .= "and created_at BETWEEN '$from_date' AND '$to_date' order by booking_id desc";
		}
		$sq_forex = mysql_query($str_f);
				while ($row_forex = mysql_fetch_assoc($sq_forex)) {
			
			//Service Tax and Markup Tax
			$service_tax_amount = 0;
			if($row_forex['service_tax_subtotal'] !== 0.00 && ($row_forex['service_tax_subtotal']) !== ''){
				$service_tax_subtotal1 = explode(',',$row_forex['service_tax_subtotal']);
				for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
				$service_tax = explode(':',$service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
				}
			}
			$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from forex_booking_payment_master where booking_id='$row_forex[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
			$credit_charges = $sq_paid_amount['sumc'];

			$total_sale += $row_forex['net_total'] - $service_tax_amount + $credit_charges;
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Forex Booking' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Forex End
	///Bus Start
	if($sale_type == 'Bus'){
		//Sale
		$str_b="select * from bus_booking_master where 1 ";
		if($from_date != '' && $to_date !='')
		{
			$str_b .= "and created_at BETWEEN '$from_date' AND '$to_date' order by booking_id desc";
		}
			$sq_bus = mysql_query($str_b);
					while ($row_exc = mysql_fetch_assoc($sq_bus)) {
			
			$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from bus_booking_payment_master where booking_id='$row_exc[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
			$credit_charges = $sq_paid_amount['sumc'];

			//Service Tax and Markup Tax
			$service_tax_amount = 0;
			if($row_exc['service_tax_subtotal'] !== 0.00 && ($row_exc['service_tax_subtotal']) !== ''){
				$service_tax_subtotal1 = explode(',',$row_exc['service_tax_subtotal']);
				for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
				$service_tax = explode(':',$service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
				}
			}
			$markupservice_tax_amount = 0;
			if($row_exc['markup_tax'] !== 0.00 && $row_exc['markup_tax'] !== ""){
				$service_tax_markup1 = explode(',',$row_exc['markup_tax']);
				for($i=0;$i<sizeof($service_tax_markup1);$i++){
				$service_tax = explode(':',$service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			
				}
			}
			$sq_exc_entry = mysql_num_rows(mysql_query("select * from bus_booking_entries where booking_id='$row_exc[booking_id]'"));
			$sq_exc_cancel = mysql_num_rows(mysql_query("select * from bus_booking_entries where booking_id='$row_exc[booking_id]' and status = 'Cancel'"));
			if($sq_exc_entry != $sq_exc_cancel){
				$total_sale += $row_exc['net_total'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;
			}	
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Bus Booking' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Bus End

	///Hotel Start
	if($sale_type == 'Hotel'){
		//Sale
		$str_h="select * from hotel_booking_master where 1 ";
	if($from_date != '' && $to_date !='')
	{
		$str_h .= "and created_at BETWEEN '$from_date' AND '$to_date' order by booking_id desc";
	}
	$sq_hotel = mysql_query($str_h);
	while ($row_exc = mysql_fetch_assoc($sq_hotel)) {

			$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from hotel_booking_payment where booking_id='$row_exc[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
			$credit_charges = $sq_paid_amount['sumc'];
			//// Calculate Service Tax//////
			$service_tax_amount = 0;
			if($row_exc['service_tax_subtotal'] !== 0.00 && ($row_exc['service_tax_subtotal']) !== ''){
			$service_tax_subtotal1 = explode(',',$row_exc['service_tax_subtotal']);
			for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
			$service_tax = explode(':',$service_tax_subtotal1[$i]);
			$service_tax_amount +=  $service_tax[2];
			}
			}

			//// Calculate Markup Tax//////

			$markupservice_tax_amount = 0;
			if($row_exc['markup_tax'] !== 0.00 && $row_exc['markup_tax'] !== ""){
			$service_tax_markup1 = explode(',',$row_exc['markup_tax']);
			for($i=0;$i<sizeof($service_tax_markup1);$i++){
			$service_tax = explode(':',$service_tax_markup1[$i]);
			$markupservice_tax_amount += $service_tax[2];

			}
			}
			$sq_exc_entry = mysql_num_rows(mysql_query("select * from hotel_booking_entries where booking_id='$row_exc[booking_id]'"));
			$sq_exc_cancel = mysql_num_rows(mysql_query("select * from hotel_booking_entries where booking_id='$row_exc[booking_id]' and status = 'Cancel'"));
			if($sq_exc_entry != $sq_exc_cancel){ 		
				$total_sale += $row_exc['total_fee'] - $service_tax_amount - $markupservice_tax_amount + $credit_charges;
			}	
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Hotel Booking' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Hotel End
	///Car Start
	if($sale_type == 'Car Rental'){
		//Sale
		$str_c="select * from car_rental_booking where status != 'Cancel' where 1 ";
		if($from_date != '' && $to_date !='')
		{
			$str_c .= "and created_at BETWEEN '$from_date' AND '$to_date' order by booking_id desc";
		}
		$sq_car = mysql_query($str_c);
			while ($row_exc = mysql_fetch_assoc($sq_car)) {	
			$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(payment_amount) as sum ,sum(`credit_charges`) as sumc from car_rental_payment where booking_id='$row_exc[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

//Service Tax and Markup Tax
            $service_tax_amount = 0;
            if($row_exc['service_tax_subtotal'] !== 0.00 && ($row_exc['service_tax_subtotal']) !== ''){
                $service_tax_subtotal1 = explode(',',$row_exc['service_tax_subtotal']);
                for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
                $service_tax = explode(':',$service_tax_subtotal1[$i]);
                $service_tax_amount +=  $service_tax[2];
                }
            }
            $markupservice_tax_amount = 0;
            if($row_exc['markup_cost_subtotal'] !== 0.00 && $row_exc['markup_cost_subtotal'] !== ""){
                $service_tax_markup1 = explode(',',$row_exc['markup_cost_subtotal']);
                for($i=0;$i<sizeof($service_tax_markup1);$i++){
                $service_tax = explode(':',$service_tax_markup1[$i]);
                $markupservice_tax_amount += $service_tax[2];
            
                }
            }

			$total_sale += $row_exc['total_fees'] - $service_tax_amount -$markupservice_tax_amount + $sq_paid_amount['sumc'];
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Car Rental' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Car End
	///Ticket Start
	if($sale_type == 'Flight Ticket'){
		//Sale
		$str_t="select * from ticket_master where 1 ";
		if($from_date != '' && $to_date !='')
		{
			$str_t .= "and created_at BETWEEN '$from_date' AND '$to_date' order by ticket_id desc";
		}
		$sq_ticket = mysql_query($str_t);
				while ($row_exc = mysql_fetch_assoc($sq_ticket)) {
			$sq_exc_entry = mysql_num_rows(mysql_query("select * from ticket_master_entries where ticket_id='$row_exc[ticket_id]'"));
			$sq_exc_cancel = mysql_num_rows(mysql_query("select * from ticket_master_entries where ticket_id='$row_exc[ticket_id]' and status = 'Cancel'"));
			
			//Service Tax and Markup Tax
			$service_tax_amount = 0;
			if($row_exc['service_tax_subtotal'] !== 0.00 && ($row_exc['service_tax_subtotal']) !== ''){
				$service_tax_subtotal1 = explode(',',$row_exc['service_tax_subtotal']);
				for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
				$service_tax = explode(':',$service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
				}
			}
			$markupservice_tax_amount = 0;
			if($row_exc['markup_tax'] !== 0.00 && $row_exc['markup_tax'] !== ""){
				$service_tax_markup1 = explode(',',$row_exc['markup_tax']);
				for($i=0;$i<sizeof($service_tax_markup1);$i++){
				$service_tax = explode(':',$service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
			
				}
			}
			
			$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from ticket_payment_master where ticket_id='$row_exc[ticket_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
			$credit_card_charges = $sq_paid_amount['sumc'];
			
			if($sq_exc_entry != $sq_exc_cancel){
				$total_sale += $row_exc['ticket_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_card_charges;
			}	
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Ticket Booking' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Ticket End
	///Train Start
	if($sale_type == 'Train Ticket'){
		//Sale
		$str_tm="select * from train_ticket_master where 1 ";
		if($from_date != '' && $to_date !='')
		{
			$str_tm .= "and created_at BETWEEN '$from_date' AND '$to_date' order by train_ticket_id desc";
		}
		$sq_train = mysql_query($str_tm);
					while ($row_exc = mysql_fetch_assoc($sq_train)) {
			$sq_exc_entry = mysql_num_rows(mysql_query("select * from train_ticket_master_entries where train_ticket_id='$row_exc[train_ticket_id]'"));
			$sq_exc_cancel = mysql_num_rows(mysql_query("select * from train_ticket_master_entries where train_ticket_id='$row_exc[train_ticket_id]' and status = 'Cancel'"));
			
			$sq_paid_amount = mysql_fetch_assoc(mysql_query("SELECT sum(payment_amount) as sum,sum(credit_charges) as sumc from train_ticket_payment_master where train_ticket_id='$row_exc[train_ticket_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
			$credit_card_charges = $sq_paid_amount['sumc'];
			
			//Service Tax and Markup Tax
			$service_tax_amount = 0;
			if($row_exc['service_tax_subtotal'] !== 0.00 && ($row_exc['service_tax_subtotal']) !== ''){
				$service_tax_subtotal1 = explode(',',$row_exc['service_tax_subtotal']);
				for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
				$service_tax = explode(':',$service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
				}
			}
			
			if($sq_exc_entry != $sq_exc_cancel){
				$total_sale += $row_exc['net_total'] - $service_tax_amount + $credit_card_charges;
			}	
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Train Ticket Booking' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Train End

	///Miscellaneous Start
	if($sale_type == 'Miscellaneous'){
		//Sale
		$str_m="select * from miscellaneous_master where 1 ";
		if($from_date != '' && $to_date !='')
		{
			$str_m .= "and created_at BETWEEN '$from_date' AND '$to_date' order by misc_id desc";
		}
		$sq_misc = mysql_query($str_m);
				while ($row_visa = mysql_fetch_assoc($sq_misc)) {
			
			$sq_paid_amount1 = mysql_fetch_assoc(mysql_query("SELECT sum(credit_charges) as sumc from miscellaneous_payment_master where misc_id='$row_visa[misc_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
			$credit_card_charges = $sq_paid_amount1['sumc'];
			
			//Service Tax and Markup Tax
			$service_tax_amount = 0;
			if($row_visa['service_tax_subtotal'] !== 0.00 && ($row_visa['service_tax_subtotal']) !== ''){
				$service_tax_subtotal1 = explode(',',$row_visa['service_tax_subtotal']);
				for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
				$service_tax = explode(':',$service_tax_subtotal1[$i]);
				$service_tax_amount +=  $service_tax[2];
				}
			}
			$markupservice_tax_amount = 0;
			if($row_visa['service_tax_markup'] !== 0.00 && $row_visa['service_tax_markup'] !== ""){
				$service_tax_markup1 = explode(',',$row_visa['service_tax_markup']);
				for($i=0;$i<sizeof($service_tax_markup1);$i++){
				$service_tax = explode(':',$service_tax_markup1[$i]);
				$markupservice_tax_amount += $service_tax[2];
				}
			}
			$sq_visa_entry = mysql_num_rows(mysql_query("select * from miscellaneous_master_entries where misc_id='$row_visa[misc_id]'"));
			$sq_visa_cancel = mysql_num_rows(mysql_query("select * from miscellaneous_master_entries where misc_id='$row_visa[misc_id]' and status = 'Cancel'"));
			if($sq_visa_entry != $sq_visa_cancel){
				$total_sale += $row_visa['misc_total_cost'] - $service_tax_amount - $markupservice_tax_amount + $credit_card_charges;
			}	
		}

		//Purchase
		$sq_purchase = mysql_query("select * from vendor_estimate where estimate_type='Miscellaneous Booking' and status!='Cancel'");
		while($row_purchase = mysql_fetch_assoc($sq_purchase)){
			$total_purchase += $row_purchase['net_total'];
			$total_purchase -= $row_purchase['service_tax_subtotal'];
		}
	}///Miscellaneous End
	return array('total_sale'=>$total_sale,'total_purchase'=>$total_purchase);
}
?>