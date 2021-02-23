<?php 
include_once('../../../model/model.php');
include_once('../../../model/finance_master/taxation_master/taxation_master.php');

$taxation_master = new taxation_master;
$taxation_master->taxation_master_update();
?>