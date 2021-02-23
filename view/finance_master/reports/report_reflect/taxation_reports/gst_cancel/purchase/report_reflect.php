<?php include "../../../../../../../model/model.php";
include_once('../purchase/vendor_generic_functions.php');

$branch_status = $_POST['branch_status'];
$role = $_POST['role'];
$branch_admin_id = $_POST['branch_admin_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$taxation_id = $_POST['taxation_id'];
$array_s = array();
$temp_arr = array();
$scgst_total = 0;
$igst_total = 0;
$ugst_total = 0;
$query = "select * from vendor_estimate where status='Cancel' ";
if($from_date !='' && $to_date != ''){
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$query .= " and created_at between '$from_date' and '$to_date' ";
}
if($taxation_id != '0'){
	$query .= " and taxation_id = '$taxation_id'";
}
include "../../../../../../../model/app_settings/branchwise_filteration.php";
$sq_setting = mysql_fetch_assoc(mysql_query("select * from app_settings where setting_id='1'"));

$count = 1;
$sq_query = mysql_query($query);
	while($row_query = mysql_fetch_assoc($sq_query))
	{
		$taxable_amount = $row_query['basic_cost'] + $row_query['non_recoverable_taxes'] + $row_query['service_charge'] + $row_query['other_charges'];
		$vendor_name = get_vendor_name($row_query['vendor_type'],$row_query['vendor_type_id']);
		$vendor_info = get_vendor_info($row_query['vendor_type'], $row_query['vendor_type_id']);
		$hsn_code = get_service_info($row_query['estimate_type']);

		$sq_state = mysql_fetch_assoc(mysql_query("select * from state_master where id='$vendor_info[state_id]'"));
		$sq_supply = mysql_fetch_assoc(mysql_query("select * from state_master where id='$sq_setting[state_id]'"));

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
			$row_query['estimate_type'] ,
			$hsn_code ,
			$vendor_name ,
			($vendor_info['service_tax_no'] == '') ? 'NA' : $vendor_info['service_tax_no'] ,
			($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'] ,
			$row_query['estimate_id'] ,
			get_date_user($row_query['created_at']) ,
			($vendor_info['service_tax_no'] == '') ? 'Unregistered' : 'Registered' ,
			($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'] ,
			$row_query['taxation_type'] ,
			$row_query['service_tax'] ,
			number_format($taxable_amount,2) ,
			($sq_tax_name['tax_type'] == 'IGST')? $tax_per :  '0' ,
			($sq_tax_name['tax_type'] == 'IGST')? $tax_amount : '0' ,
			($sq_tax_name['tax_type'] == 'SGST+CGST')? ($tax_per/2) :'0' ,
			($sq_tax_name['tax_type'] == 'SGST+CGST')?  ($tax_amount/2): '0' ,
			($sq_tax_name['tax_type'] == 'SGST+CGST')?  ($tax_per/2) :  '0'  ,
			($sq_tax_name['tax_type'] == 'SGST+CGST')?($tax_amount/2) :'0'  ,
			($sq_tax_name['tax_type'] == 'UGST')? $tax_per :'0'  ,
			($sq_tax_name['tax_type'] == 'UGST')? $tax_amount :'0' ,
			"0.00" ,
			"0.00",
			"",
			""
			), "bg" =>$bg);
		array_push($array_s,$temp_arr);	
		} 
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
	