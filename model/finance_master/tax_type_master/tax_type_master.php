<?php 
class tax_type_master{

public function tax_type_master_save()
{
	$tax_type = $_POST['tax_type'];	
	$active_flag = $_POST['active_flag'];

	$created_at = date('Y-m-d H:i:s');


	$sq_max = mysql_fetch_assoc(mysql_query("select max(tax_type_id) as max from tax_type_master"));
	$tax_type_id = $sq_max['max'] + 1;

	begin_t();

	$sq_tax_type = mysql_query("insert into tax_type_master(tax_type_id, tax_type, active_flag, created_at) values ('$tax_type_id', '$tax_type', '$active_flag', '$created_at')");

	if($sq_tax_type){
		commit_t();		
		echo "Tax type saved!";
		exit;
	}
	else{
		rollback_t();
		echo "error--Sorry, Tax type not saved!";
		exit;
	}

}

public function tax_type_master_update()
{
	$tax_type_id = $_POST['tax_type_id'];
	$tax_type = $_POST['tax_type'];
	$active_flag = $_POST['active_flag'];

	$created_at = date('Y-m-d H:i:s');


	$sq_count = mysql_num_rows(mysql_query("select tax_type from sl_master where tax_type='$tax_type' and tax_type_id!='$tax_type_id'"));
	if($sq_count>0){
		echo "error--TAX type already exists!";
		exit;
	}

	begin_t();
	$sq_tax_type = mysql_query("update tax_type_master set tax_type='$tax_type', active_flag='$active_flag' where tax_type_id='$tax_type_id'");

	if($sq_tax_type){
		commit_t();		
		echo "Tax type updated!";
		exit;
	}
	else{
		rollback_t();
		echo "error--Sorry, Tax type not updated!";
		exit;
	}

}

}
?>