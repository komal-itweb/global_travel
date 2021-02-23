<?php 
include "../../../../../../../model/model.php";
include_once('../sale/sale_generic_functions.php');

$branch_status = $_POST['branch_status'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$taxation_id = $_POST['taxation_id'];
$role = $_POST['role'];
$branch_admin_id = $_POST['branch_admin_id'];
$scgst_total = 0;
$igst_total = 0;
$ugst_total = 0;
$array_s = array();
$temp_arr = array();

$sq_setting = mysql_fetch_assoc(mysql_query("select * from app_settings where setting_id='1'"));
$sq_supply = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_setting[state_id]'"));

	$count = 1;
	//Passport Booking
	$query = "select * from passport_master where 1 ";
	if($from_date !='' && $to_date != ''){
		$from_date = get_date_db($from_date);
		$to_date = get_date_db($to_date);
		$query .= " and created_at between '$from_date' and '$to_date' ";
	}
	if($taxation_id != '0'){
		$query .= " and taxation_id = '$taxation_id'";
	}
	include "../../../../../../../model/app_settings/branchwise_filteration.php";
	$sq_query = mysql_query($query);
    while($row_query = mysql_fetch_assoc($sq_query))
    {
    	//Total count
	 	$sq_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as booking_count from passport_master_entries where passport_id ='$row_query[passport_id]'"));

	 	//Cancelled count
	 	$sq_cancel_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as cancel_count from passport_master_entries where passport_id ='$row_query[passport_id]' and status ='Cancel'"));
	 	if($sq_count['booking_count'] == $sq_cancel_count['cancel_count'])
		{
	    	$sq_cust = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$row_query[customer_id]'"));
	    	if($sq_cust['type'] == 'Corporate'){
	    		$cust_name = $sq_cust['company_name'];
	    	}else{
	    		$cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
	    	}
	    	$taxable_amount = $row_query['passport_issue_amount'] + $row_query['service_charge'];
	    	$hsn_code = get_service_info('Passport');

	    	$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_cust[state_id]'"));

	    	$sq_tax1 = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$row_query[taxation_id]'"));
	    	$sq_tax_name = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_tax1[tax_type_id]'"));
	    	
			$tax_per = $row_query['service_tax'];
			$tax_amount = $row_query['service_tax_subtotal'];
			if($sq_tax_name['tax_type'] == 'IGST'){ $igst_total += $tax_amount; }
			else if($sq_tax_name['tax_type'] == 'SGST+CGST'){ $scgst_total += $tax_amount; }
			else if($sq_tax_name['tax_type'] == 'UGST'){ $ugst_total += $tax_amount; }
			else{}

				$temp_arr = array( "data" => array(
					(int)($count++),
					"Passport Booking",
					$hsn_code ,
					$cust_name,
					($sq_cust['service_tax_no'] == '') ? 'NA' : $sq_cust['service_tax_no'] ,
					($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
					get_passport_booking_id($row_query['passport_id']) ,
					get_date_user($row_query['created_at']),
					($sq_cust['service_tax_no'] == '') ? 'Unregistered' : 'Registered',
					($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
					$row_query['taxation_type'] ,
					$row_query['service_tax'] ,
					$row_query['passport_total_cost'] ,
					number_format($taxable_amount,2) ,
					($sq_tax_name['tax_type'] == 'IGST')? $tax_per:'0',
					($sq_tax_name['tax_type'] == 'IGST')?$tax_amount: '0',
					($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
					($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
					($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
					($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
					($sq_tax_name['tax_type'] == 'UGST')? $tax_per: '0',
					($sq_tax_name['tax_type'] == 'UGST')?$tax_amount: '0',
					"0.00",
					"0.00",
					"",
					""
				), "bg" =>$bg);
			array_push($array_s,$temp_arr);
		} 
	    }
		//Visa Booking
		$query = "select * from visa_master where 1 ";
		if($from_date !='' && $to_date != ''){
			$from_date = get_date_db($from_date);
			$to_date = get_date_db($to_date);
			$query .= " and created_at between '$from_date' and '$to_date' ";
		}
		if($taxation_id != '0'){
			$query .= " and taxation_id = '$taxation_id'";
		}
		include "../../../../../../../model/app_settings/branchwise_filteration.php";
		$sq_query = mysql_query($query);
	    while($row_query = mysql_fetch_assoc($sq_query))
	    {
	    	//Total count
		 	$sq_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as booking_count from visa_master_entries where visa_id ='$row_query[visa_id]'"));

		 	//Cancelled count
		 	$sq_cancel_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as cancel_count from visa_master_entries where visa_id ='$row_query[visa_id]' and status ='Cancel'"));
		 	if($sq_count['booking_count'] == $sq_cancel_count['cancel_count'])
			{
		    	$sq_cust = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$row_query[customer_id]'"));
		    	if($sq_cust['type'] == 'Corporate'){
		    		$cust_name = $sq_cust['company_name'];
		    	}else{
		    		$cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
		    	}
		    	$taxable_amount = $row_query['visa_issue_amount'] + $row_query['service_charge'];
		    	$hsn_code = get_service_info('Visa');

		    	$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_cust[state_id]'"));

		    	$sq_tax1 = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$row_query[taxation_id]'"));
		    	$sq_tax_name = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_tax1[tax_type_id]'"));
		    	
				$tax_per = $row_query['service_tax'];
				$tax_amount = $row_query['service_tax_subtotal'];
				if($sq_tax_name['tax_type'] == 'IGST'){ $igst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'SGST+CGST'){ $scgst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'UGST'){ $ugst_total += $tax_amount; }
				else{}
					$temp_arr = array( "data" => array(
						(int)($count++),
						"Passport Booking",
						$hsn_code ,
						$cust_name,
						($sq_cust['service_tax_no'] == '') ? 'NA' : $sq_cust['service_tax_no'] ,
						($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
						get_visa_booking_id($row_query['visa_id']) ,
						get_date_user($row_query['created_at']),
						($sq_cust['service_tax_no'] == '') ? 'Unregistered' : 'Registered',
						($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
						$row_query['taxation_type'] ,
						$row_query['service_tax'] ,
						$row_query['visa_total_cost'] ,
						number_format($taxable_amount,2) ,
						($sq_tax_name['tax_type'] == 'IGST')? $tax_per:'0',
						($sq_tax_name['tax_type'] == 'IGST')?$tax_amount: '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'UGST')? $tax_per: '0',
						($sq_tax_name['tax_type'] == 'UGST')?$tax_amount: '0',
						"0.00",
						"0.00",
						"",
						""
					), "bg" =>$bg);
				array_push($array_s,$temp_arr);
			 }
		} 
		//Bus Booking
		$query = "select * from bus_booking_master where 1 ";
		if($from_date !='' && $to_date != ''){
			$from_date = get_date_db($from_date);
			$to_date = get_date_db($to_date);
			$query .= " and created_at between '$from_date' and '$to_date' ";
		}
		if($taxation_id != '0'){
			$query .= " and taxation_id = '$taxation_id'";
		}
		include "../../../../../../../model/app_settings/branchwise_filteration.php";
		$sq_query = mysql_query($query);
	    while($row_query = mysql_fetch_assoc($sq_query))
	    {
	    	//Total count
		 	$sq_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as booking_count from bus_booking_entries where booking_id ='$row_query[booking_id]'"));

		 	//Cancelled count
		 	$sq_cancel_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as cancel_count from bus_booking_entries where booking_id ='$row_query[booking_id]' and status ='Cancel'"));
		 	if($sq_count['booking_count'] == $sq_cancel_count['cancel_count'])
			{
		    	$sq_cust = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$row_query[customer_id]'"));
		    	if($sq_cust['type'] == 'Corporate'){
		    		$cust_name = $sq_cust['company_name'];
		    	}else{
		    		$cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
		    	}
		    	$taxable_amount = $row_query['basic_cost'] + $row_query['service_charge'];
		    	$hsn_code = get_service_info('Bus');

		    	$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_cust[state_id]'"));

		    	$sq_tax1 = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$row_query[taxation_id]'"));
		    	$sq_tax_name = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_tax1[tax_type_id]'"));
		    	
				$tax_per = $row_query['service_tax'];
				$tax_amount = $row_query['service_tax_subtotal'];
				if($sq_tax_name['tax_type'] == 'IGST'){ $igst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'SGST+CGST'){ $scgst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'UGST'){ $ugst_total += $tax_amount; }
				else{}
					$temp_arr = array( "data" => array(
						(int)($count++),
						"Bus Booking",
						$hsn_code ,
						$cust_name,
						($sq_cust['service_tax_no'] == '') ? 'NA' : $sq_cust['service_tax_no'] ,
						($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
						get_bus_booking_id($row_query['booking_id']) ,
						get_date_user($row_query['created_at']),
						($sq_cust['service_tax_no'] == '') ? 'Unregistered' : 'Registered',
						($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
						$row_query['taxation_type'] ,
						$row_query['service_tax'] ,
						$row_query['net_total'] ,
						number_format($taxable_amount,2) ,
						($sq_tax_name['tax_type'] == 'IGST')? $tax_per:'0',
						($sq_tax_name['tax_type'] == 'IGST')?$tax_amount: '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'UGST')? $tax_per: '0',
						($sq_tax_name['tax_type'] == 'UGST')?$tax_amount: '0',
						"0.00",
						"0.00",
						"",
						""
					), "bg" =>$bg);
				array_push($array_s,$temp_arr);
			 }
		} 
		//Excursion Booking
		$query = "select * from excursion_master where 1 ";
		if($from_date !='' && $to_date != ''){
			$from_date = get_date_db($from_date);
			$to_date = get_date_db($to_date);
			$query .= " and created_at between '$from_date' and '$to_date' ";
		}
		if($taxation_id != '0'){
			$query .= " and taxation_id = '$taxation_id'";
		}
		include "../../../../../../../model/app_settings/branchwise_filteration.php";
		$sq_query = mysql_query($query);
	    while($row_query = mysql_fetch_assoc($sq_query))
	    {
	    	//Total count
		 	$sq_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as booking_count from excursion_master_entries where exc_id ='$row_query[exc_id]'"));

		 	//Cancelled count
		 	$sq_cancel_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as cancel_count from excursion_master_entries where exc_id ='$row_query[exc_id]' and status ='Cancel'"));
		 	if($sq_count['booking_count'] == $sq_cancel_count['cancel_count'])
			{
		    	$sq_cust = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$row_query[customer_id]'"));
		    	if($sq_cust['type'] == 'Corporate'){
		    		$cust_name = $sq_cust['company_name'];
		    	}else{
		    		$cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
		    	}
		    	$taxable_amount = $row_query['exc_issue_amount'] + $row_query['service_charge'];
		    	$hsn_code = get_service_info('Excursion');

		    	$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_cust[state_id]'"));

		    	$sq_tax1 = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$row_query[taxation_id]'"));
		    	$sq_tax_name = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_tax1[tax_type_id]'"));
		    	
				$tax_per = $row_query['service_tax'];
				$tax_amount = $row_query['service_tax_subtotal'];
				if($sq_tax_name['tax_type'] == 'IGST'){ $igst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'SGST+CGST'){ $scgst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'UGST'){ $ugst_total += $tax_amount; }
				else{}

					$temp_arr = array( "data" => array(
						(int)($count++),
						"Excursion Booking",
						$hsn_code ,
						$cust_name,
						($sq_cust['service_tax_no'] == '') ? 'NA' : $sq_cust['service_tax_no'] ,
						($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
						get_exc_booking_id($row_query['exc_id']) ,
						get_date_user($row_query['created_at']),
						($sq_cust['service_tax_no'] == '') ? 'Unregistered' : 'Registered',
						($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
						$row_query['taxation_type'] ,
						$row_query['service_tax'] ,
						$row_query['exc_total_cost'] ,
						number_format($taxable_amount,2) ,
						($sq_tax_name['tax_type'] == 'IGST')? $tax_per:'0',
						($sq_tax_name['tax_type'] == 'IGST')?$tax_amount: '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'UGST')? $tax_per: '0',
						($sq_tax_name['tax_type'] == 'UGST')?$tax_amount: '0',
						"0.00",
						"0.00",
						"",
						""
					), "bg" =>$bg);
				array_push($array_s,$temp_arr);
			 }
		} 
		//Miscellaneous Booking
		$query = "select * from miscellaneous_master where 1 ";
		if($from_date !='' && $to_date != ''){
			$from_date = get_date_db($from_date);
			$to_date = get_date_db($to_date);
			$query .= " and created_at between '$from_date' and '$to_date' ";
		}
		if($taxation_id != '0'){
			$query .= " and taxation_id = '$taxation_id'";
		}
		include "../../../../../../../model/app_settings/branchwise_filteration.php";
		$sq_query = mysql_query($query);
	    while($row_query = mysql_fetch_assoc($sq_query))
	    {
	    	//Total count
		 	$sq_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as booking_count from miscellaneous_master_entries where misc_id ='$row_query[misc_id]'"));

		 	//Cancelled count
		 	$sq_cancel_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as cancel_count from miscellaneous_master_entries where misc_id ='$row_query[misc_id]' and status ='Cancel'"));
		 	if($sq_count['booking_count'] == $sq_cancel_count['cancel_count'])
			{
		    	$sq_cust = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$row_query[customer_id]'"));
		    	if($sq_cust['type'] == 'Corporate'){
		    		$cust_name = $sq_cust['company_name'];
		    	}else{
		    		$cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
		    	}
		    	$taxable_amount = $row_query['misc_issue_amount'] + $row_query['service_charge'];
		    	$hsn_code = get_service_info('Miscellaneous');

		    	$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_cust[state_id]'"));

		    	$sq_tax1 = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$row_query[taxation_id]'"));
		    	$sq_tax_name = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_tax1[tax_type_id]'"));
		    	
				$tax_per = $row_query['service_tax'];
				$tax_amount = $row_query['service_tax_subtotal'];
				if($sq_tax_name['tax_type'] == 'IGST'){ $igst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'SGST+CGST'){ $scgst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'UGST'){ $ugst_total += $tax_amount; }
				else{}
				
					$temp_arr = array( "data" => array(
						(int)($count++),
						"Miscellaneous Booking",
						$hsn_code ,
						$cust_name,
						($sq_cust['service_tax_no'] == '') ? 'NA' : $sq_cust['service_tax_no'] ,
						($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
						get_misc_booking_id($row_query['misc_id']) ,
						get_date_user($row_query['created_at']),
						($sq_cust['service_tax_no'] == '') ? 'Unregistered' : 'Registered',
						($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
						$row_query['taxation_type'] ,
						$row_query['service_tax'] ,
						$row_query['misc_total_cost'] ,
						number_format($taxable_amount,2) ,
						($sq_tax_name['tax_type'] == 'IGST')? $tax_per:'0',
						($sq_tax_name['tax_type'] == 'IGST')?$tax_amount: '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'UGST')? $tax_per: '0',
						($sq_tax_name['tax_type'] == 'UGST')?$tax_amount: '0',
						"0.00",
						"0.00",
						"",
						""
					), "bg" =>$bg);
				array_push($array_s,$temp_arr);
			 }
		} 

		//Hotel Booking
		$query = "select * from hotel_booking_master where 1 ";
		if($from_date !='' && $to_date != ''){
			$from_date = get_date_db($from_date);
			$to_date = get_date_db($to_date);
			$query .= " and created_at between '$from_date' and '$to_date' ";
		}
		if($taxation_id != '0'){
			$query .= " and taxation_id = '$taxation_id'";
		}
		include "../../../../../../../model/app_settings/branchwise_filteration.php";
		$sq_query = mysql_query($query);
	    while($row_query = mysql_fetch_assoc($sq_query))
	    {
	    	//Total count
		 	$sq_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as booking_count from hotel_booking_entries where booking_id ='$row_query[booking_id]'"));

		 	//Cancelled count
		 	$sq_cancel_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as cancel_count from hotel_booking_entries where booking_id ='$row_query[booking_id]' and status ='Cancel'"));

		 	if($sq_count['booking_count'] == $sq_cancel_count['cancel_count'])
			{
		    	$sq_cust = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$row_query[customer_id]'"));
		    	if($sq_cust['type'] == 'Corporate'){
		    		$cust_name = $sq_cust['company_name'];
		    	}else{
		    		$cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
		    	}
		    	$taxable_amount = $row_query['sub_total'] + $row_query['service_charge'];
		    	$hsn_code = get_service_info('Hotel / Accommodation');

		    	$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_cust[state_id]'"));

		    	$sq_tax1 = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$row_query[taxation_id]'"));
		    	$sq_tax_name = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_tax1[tax_type_id]'"));
		    	
				$tax_per = $row_query['service_tax'];
				$tax_amount = $row_query['service_tax_subtotal'];
				if($sq_tax_name['tax_type'] == 'IGST'){ $igst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'SGST+CGST'){ $scgst_total += $tax_amount; }
				else if($sq_tax_name['tax_type'] == 'UGST'){ $ugst_total += $tax_amount; }
				else{}

					$temp_arr = array( "data" => array(
						(int)($count++),
						"Hotel Booking",
						$hsn_code ,
						$cust_name,
						($sq_cust['service_tax_no'] == '') ? 'NA' : $sq_cust['service_tax_no'] ,
						($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
						get_hotel_booking_id($row_query['booking_id']) ,
						get_date_user($row_query['created_at']),
						($sq_cust['service_tax_no'] == '') ? 'Unregistered' : 'Registered',
						($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
						$row_query['taxation_type'] ,
						$row_query['service_tax'] ,
						$row_query['total_fee'] ,
						number_format($taxable_amount,2) ,
						($sq_tax_name['tax_type'] == 'IGST')? $tax_per:'0',
						($sq_tax_name['tax_type'] == 'IGST')?$tax_amount: '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
						($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
						($sq_tax_name['tax_type'] == 'UGST')? $tax_per: '0',
						($sq_tax_name['tax_type'] == 'UGST')?$tax_amount: '0',
						"0.00",
						"0.00",
						"",
						""
					), "bg" =>$bg);
				array_push($array_s,$temp_arr);
			 }
		} 
		//Car Rental Booking
		$query = "select * from car_rental_booking where status = 'Cancel' ";
		if($from_date !='' && $to_date != ''){
			$from_date = get_date_db($from_date);
			$to_date = get_date_db($to_date);
			$query .= " and created_at between '$from_date' and '$to_date' ";
		}
		if($taxation_id != '0'){
			$query .= " and taxation_id = '$taxation_id'";
		}
		include "../../../../../../../model/app_settings/branchwise_filteration.php";
		$sq_query = mysql_query($query);
	    while($row_query = mysql_fetch_assoc($sq_query))
	    {
	    	$sq_cust = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$row_query[customer_id]'"));
	    	if($sq_cust['type'] == 'Corporate'){
	    		$cust_name = $sq_cust['company_name'];
	    	}else{
	    		$cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
	    	}
	    	$taxable_amount = $row_query['actual_cost'] + $row_query['km_total_fee'];
	    	$hsn_code = get_service_info('Car Rental');

	    	$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_cust[state_id]'"));

	    	$sq_tax1 = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$row_query[taxation_id]'"));
	    	$sq_tax_name = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_tax1[tax_type_id]'"));
	    	
			$tax_per = $row_query['service_tax'];
			$tax_amount = $row_query['service_tax_subtotal'];
			if($sq_tax_name['tax_type'] == 'IGST'){ $igst_total += $tax_amount; }
			else if($sq_tax_name['tax_type'] == 'SGST+CGST'){ $scgst_total += $tax_amount; }
			else if($sq_tax_name['tax_type'] == 'UGST'){ $ugst_total += $tax_amount; }
			else{}
			
				$temp_arr = array( "data" => array(
					(int)($count++),
					"Car Rental Booking",
					$hsn_code ,
					$cust_name,
					($sq_cust['service_tax_no'] == '') ? 'NA' : $sq_cust['service_tax_no'] ,
					($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
					get_car_rental_booking_id($row_query['booking_id']) ,
					get_date_user($row_query['created_at']),
					($sq_cust['service_tax_no'] == '') ? 'Unregistered' : 'Registered',
					($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
					$row_query['taxation_type'] ,
					$row_query['service_tax'] ,
					$row_query['total_fees'] ,
					number_format($taxable_amount,2) ,
					($sq_tax_name['tax_type'] == 'IGST')? $tax_per:'0',
					($sq_tax_name['tax_type'] == 'IGST')?$tax_amount: '0',
					($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
					($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
					($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
					($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
					($sq_tax_name['tax_type'] == 'UGST')? $tax_per: '0',
					($sq_tax_name['tax_type'] == 'UGST')?$tax_amount: '0',
					"0.00",
					"0.00",
					"",
					""
				), "bg" =>$bg);
			array_push($array_s,$temp_arr);
			
		    } 
		    //Flight Booking
			$query = "select * from ticket_master where 1 ";
			if($from_date !='' && $to_date != ''){
				$from_date = get_date_db($from_date);
				$to_date = get_date_db($to_date);
				$query .= " and created_at between '$from_date' and '$to_date' ";
			}
			if($taxation_id != '0'){
				$query .= " and taxation_id = '$taxation_id'";
			}
			include "../../../../../../../model/app_settings/branchwise_filteration.php";
			$sq_query = mysql_query($query);
		    while($row_query = mysql_fetch_assoc($sq_query))
		    {
		    	//Total count
			 	$sq_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as booking_count from ticket_master_entries where ticket_id ='$row_query[ticket_id]'"));

			 	//Cancelled count
			 	$sq_cancel_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as cancel_count from ticket_master_entries where ticket_id ='$row_query[ticket_id]' and status ='Cancel'"));
			 	if($sq_count['booking_count'] == $sq_cancel_count['cancel_count'])
				{
			    	$sq_cust = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$row_query[customer_id]'"));
			    	if($sq_cust['type'] == 'Corporate'){
			    		$cust_name = $sq_cust['company_name'];
			    	}else{
			    		$cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
			    	}
			    	$taxable_amount = $row_query['basic_cost'] + $row_query['basic_cost_markup'] - $row_query['basic_cost_discount']+ $row_query['yq_tax'] + $row_query['yq_tax_markup'] - $row_query['yq_tax_discount'] + $row_query['g1_plus_f2_tax'] + $row_query['service_charge'];
			    	$hsn_code = get_service_info('Flight');

			    	$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_cust[state_id]'"));

			    	$sq_tax1 = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$row_query[taxation_id]'"));
			    	$sq_tax_name = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_tax1[tax_type_id]'"));
			    	
					$tax_per = $row_query['service_tax'];
					$tax_amount = $row_query['service_tax_subtotal'];
					if($sq_tax_name['tax_type'] == 'IGST'){ $igst_total += $tax_amount; }
					else if($sq_tax_name['tax_type'] == 'SGST+CGST'){ $scgst_total += $tax_amount; }
					else if($sq_tax_name['tax_type'] == 'UGST'){ $ugst_total += $tax_amount; }
					else{}
						$temp_arr = array( "data" => array(
							(int)($count++),
							"Ticket Booking",
							$hsn_code ,
							$cust_name,
							($sq_cust['service_tax_no'] == '') ? 'NA' : $sq_cust['service_tax_no'] ,
							($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
							get_ticket_booking_id($row_query['ticket_id']) ,
							get_date_user($row_query['created_at']),
							($sq_cust['service_tax_no'] == '') ? 'Unregistered' : 'Registered',
							($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
							$row_query['taxation_type'] ,
							$row_query['service_tax'] ,
							$row_query['ticket_total_cost'] ,
							number_format($taxable_amount,2) ,
							($sq_tax_name['tax_type'] == 'IGST')? $tax_per:'0',
							($sq_tax_name['tax_type'] == 'IGST')?$tax_amount: '0',
							($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
							($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
							($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
							($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
							($sq_tax_name['tax_type'] == 'UGST')? $tax_per: '0',
							($sq_tax_name['tax_type'] == 'UGST')?$tax_amount: '0',
							"0.00",
							"0.00",
							"",
							""
						), "bg" =>$bg);
					array_push($array_s,$temp_arr);
				 }
			} 
			//Train Booking
			$query = "select * from train_ticket_master where 1 ";
			if($from_date !='' && $to_date != ''){
				$from_date = get_date_db($from_date);
				$to_date = get_date_db($to_date);
				$query .= " and created_at between '$from_date' and '$to_date' ";
			}
			if($taxation_id != '0'){
				$query .= " and taxation_id = '$taxation_id'";
			}
			include "../../../../../../../model/app_settings/branchwise_filteration.php";
			$sq_query = mysql_query($query);
		    while($row_query = mysql_fetch_assoc($sq_query))
		    {
		    	//Total count
			 	$sq_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as booking_count from train_ticket_master_entries where train_ticket_id ='$row_query[train_ticket_id]'"));

			 	//Cancelled count
			 	$sq_cancel_count = mysql_fetch_assoc(mysql_query("select count(entry_id) as cancel_count from train_ticket_master_entries where train_ticket_id ='$row_query[train_ticket_id]' and status ='Cancel'"));
			 	if($sq_count['booking_count'] == $sq_cancel_count['cancel_count'])
				{
			    	$sq_cust = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$row_query[customer_id]'"));
			    	if($sq_cust['type'] == 'Corporate'){
			    		$cust_name = $sq_cust['company_name'];
			    	}else{
			    		$cust_name = $sq_cust['first_name'].' '.$sq_cust['last_name'];
			    	}
			    	$taxable_amount = $row_query['basic_fair'] + $row_query['service_charge'] + $row_query['delivery_charges'];
			    	$hsn_code = get_service_info('Train');

			    	$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_cust[state_id]'"));

			    	$sq_tax1 = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$row_query[taxation_id]'"));
			    	$sq_tax_name = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_tax1[tax_type_id]'"));
			    	
					$tax_per = $row_query['service_tax'];
					$tax_amount = $row_query['service_tax_subtotal'];
					if($sq_tax_name['tax_type'] == 'IGST'){ $igst_total += $tax_amount; }
					else if($sq_tax_name['tax_type'] == 'SGST+CGST'){ $scgst_total += $tax_amount; }
					else if($sq_tax_name['tax_type'] == 'UGST'){ $ugst_total += $tax_amount; }
					else{}
					
						$temp_arr = array( "data" => array(
							(int)($count++),
							"Train Ticket Booking",
							$hsn_code ,
							$cust_name,
							($sq_cust['service_tax_no'] == '') ? 'NA' : $sq_cust['service_tax_no'] ,
							($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
							get_train_ticket_booking_id($row_query['train_ticket_id']) ,
							get_date_user($row_query['created_at']),
							($sq_cust['service_tax_no'] == '') ? 'Unregistered' : 'Registered',
							($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
							$row_query['taxation_type'] ,
							$row_query['service_tax'] ,
							$row_query['net_total'] ,
							number_format($taxable_amount,2) ,
							($sq_tax_name['tax_type'] == 'IGST')? $tax_per:'0',
							($sq_tax_name['tax_type'] == 'IGST')?$tax_amount: '0',
							($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
							($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
							($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_per/2): '0',
							($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2): '0',
							($sq_tax_name['tax_type'] == 'UGST')? $tax_per: '0',
							($sq_tax_name['tax_type'] == 'UGST')?$tax_amount: '0',
							"0.00",
							"0.00",
							"",
							""
						), "bg" =>$bg);
					array_push($array_s,$temp_arr);
				 }
			} 
include_once '../sale/get_git_fit_amount.php'; 
			
$footer_data = array("footer_data" => array(
	'total_footers' => 6,
	
	'foot0' => "TOTAL : ",
	'col0' => 14,
	'class0' =>"info text-right",

	'foot1' => 'IGST :'.number_format($igst_total,2),
	'col1' => 2,
	'class1' =>"info text-right",

	'foot2' => 'CGST :'.number_format($scgst_total/2,2),
	'col2' => 2,
	'class2' =>"info text-right",

	'foot3' => 'SGST :'.number_format($scgst_total/2,2),
	'col3' => 2,
	'class3' =>"info text-right",

	'foot4' => 'UGST :'.number_format($ugst_total,2),
	'col4' => 2,
	'class4' =>"info text-right",

	'foot5' => "",
	'col5' => 4,
	'class5' =>"info text-right"
	)
);
array_push($array_s, $footer_data);
echo json_encode($array_s);
?>
	