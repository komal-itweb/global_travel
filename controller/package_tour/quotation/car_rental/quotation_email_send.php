<?php 
include "../../../../model/model.php"; 
include "../../../../model/package_tour/quotation/car_rental/quotation_email_send.php"; 

$quotation_email_send = new quotation_email_send;
$quotation_email_send->quotation_email();
?>