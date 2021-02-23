<?php 
class taxation_master{

public function taxation_master_save()
{
	$tax_type_id = $_POST['tax_type_id'];
	$tax_in_percentage = $_POST['tax_in_percentage'];
	$active_flag = $_POST['active_flag'];

	$created_at = date('Y-m-d H:i:s');

	$sq_count = mysql_num_rows(mysql_query("select taxation from sl_master where taxation='$taxation'"));
	if($sq_count>0){
		echo "error--TAX type already exists!";
		exit;
	}

	$sq_max = mysql_fetch_assoc(mysql_query("select max(taxation_id) as max from taxation_master"));
	$taxation_id = $sq_max['max'] + 1;

	begin_t();

	$sq_taxation = mysql_query("insert into taxation_master(taxation_id, tax_type_id, tax_in_percentage, active_flag, created_at) values ('$taxation_id', '$tax_type_id', '$tax_in_percentage', '$active_flag', '$created_at')");

	if($sq_taxation){
		commit_t();		
		echo "Tax has been successfully saved.";
		exit;
	}
	else{
		rollback_t();
		echo "error--Sorry, Taxation not saved!";
		exit;
	}

}

public function taxation_master_update()
{
	$taxation_id = $_POST['taxation_id'];
	$tax_type_id = $_POST['tax_type_id'];
	$tax_in_percentage = $_POST['tax_in_percentage'];
	$active_flag = $_POST['active_flag'];
	$created_at = date('Y-m-d H:i:s');

	 

	$sq_count = mysql_num_rows(mysql_query("select tax_type_id from sl_master where tax_type_id='$tax_type_id' and taxation_id!='$taxation_id'"));
	if($sq_count>0){
		echo "error--Taxation already exists!";
		exit;
	}

	begin_t();
	$sq_taxation = mysql_query("update taxation_master set tax_type_id='$tax_type_id', tax_in_percentage='$tax_in_percentage',  active_flag='$active_flag' where taxation_id='$taxation_id'");

	if($sq_taxation){
		commit_t();		
		echo "Tax has been successfully updated.";
		exit;
	}
	else{
		rollback_t();
		echo "error--Sorry, Taxation not updated!";
		exit;
	}

}

}
?>