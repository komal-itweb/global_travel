<?php 
include_once('../../../model/model.php');
include_once('../../../model/finance_master/tax_type_master/tax_type_master.php');

$tax_type_master = new tax_type_master;
$tax_type_master->tax_type_master_save();
?>