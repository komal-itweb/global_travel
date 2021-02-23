<?php
include "../../../../../../../model/model.php";
include_once('../purchase/vendor_generic_functions.php');
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
  die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../../../../../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

//This function generates the background color
function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

//This array sets the font atrributes
$header_style_Array = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 12,
        'name'  => 'Verdana'
    ));
$table_header_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 11,
        'name'  => 'Verdana'
    ));
$content_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 9,
        'name'  => 'Verdana'
    ));

//This is border array
$borderArray = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          )
      );

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                             ->setLastModifiedBy("Maarten Balliauw")
                             ->setTitle("Office 2007 XLSX Test Document")
                             ->setSubject("Office 2007 XLSX Test Document")
                             ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                             ->setKeywords("office 2007 openxml php")
                             ->setCategory("Test result file");

//////////////////////////****************Content start**************////////////////////////////////

$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$taxation_id = $_GET['taxation_id'];
$branch_status = $_GET['branch_status'];
$role = $_GET['role'];
$branch_admin_id = $_GET['branch_admin_id'];

if($from_date!="" && $to_date!=""){
  $from_date1 = get_date_user($from_date);
  $to_date1 = get_date_user($to_date);
  $date_str = $from_date1.' to '.$to_date1;
}
else{
    $date_str = "";
}
if($taxation_id != '0'){
  $sq_taxation = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$taxation_id'"));
  $sq_tax_type = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$sq_taxation[tax_type_id]'"));
}

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
           ->setCellValue('B2', 'Report Name')
            ->setCellValue('C2', 'GST On Purchase Cancellation')
            ->setCellValue('B3', 'From-To-Date')
            ->setCellValue('C3', $date_str)
            ->setCellValue('B4', 'TAX(%)')
            ->setCellValue('C4', $sq_tax_type['tax_type'].'-'.$sq_taxation['tax_in_percentage']);
             
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray); 

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);    


$count = 0;
$row_count = 7;
   
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "Sr.No")
        ->setCellValue('C'.$row_count, "Service Name")
        ->setCellValue('D'.$row_count, "SAC/HSN Code")
        ->setCellValue('E'.$row_count, "Supplier Name")
        ->setCellValue('F'.$row_count, "GSTIN/UIN")
        ->setCellValue('G'.$row_count, "Account State")
        ->setCellValue('H'.$row_count, "Purchase ID")
        ->setCellValue('I'.$row_count, "Purchase Date")
        ->setCellValue('J'.$row_count, "Type of Supplier")
        ->setCellValue('K'.$row_count, "Place of Supply")
        ->setCellValue('L'.$row_count, "Tax Type")
        ->setCellValue('M'.$row_count, "Rate")
        ->setCellValue('N'.$row_count, "Taxable Amount")
        ->setCellValue('O'.$row_count, "IGST_%")
        ->setCellValue('P'.$row_count, "IGST_Amount")
        ->setCellValue('Q'.$row_count, "CGST_%")
        ->setCellValue('R'.$row_count, "CGST Amount")
        ->setCellValue('S'.$row_count, "SGST_%")
        ->setCellValue('T'.$row_count, "SGST Amount")
        ->setCellValue('U'.$row_count, "UTGST_s%")
        ->setCellValue('V'.$row_count, "UTGST Amount")
        ->setCellValue('W'.$row_count, "Cess%")
        ->setCellValue('X'.$row_count, "Cess Amount")
        ->setCellValue('Y'.$row_count, "ITC Eligibility")
        ->setCellValue('Z'.$row_count, "Reverse Charge");

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($header_style_Array);

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($borderArray);  

$row_count++;

$count = 1;
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
if($branch_status=='yes'){
  if($role=='Branch Admin'){
  $query .= " and branch_admin_id = '$branch_admin_id'";
  } 
  elseif($role!='Admin' && $role!='Branch Admin'){
    $query .= " and emp_id='$emp_id'";
    }
}
$sq_setting = mysql_fetch_assoc(mysql_query("select * from app_settings where setting_id='1'"));
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

      $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B'.$row_count, $count++)
            ->setCellValue('C'.$row_count, $row_query['estimate_type'])
            ->setCellValue('D'.$row_count, $hsn_code)
            ->setCellValue('E'.$row_count, $vendor_name)
            ->setCellValue('F'.$row_count, ($vendor_info['service_tax'] == '') ? 'NA' : $vendor_info['service_tax'])
            ->setCellValue('G'.$row_count, ($sq_state['state_name'] == '') ? 'NA' : $sq_state['state_name'])
            ->setCellValue('H'.$row_count, $row_query['estimate_id'])
            ->setCellValue('I'.$row_count, get_date_user($row_query['created_at']))
            ->setCellValue('J'.$row_count, ($vendor_info['service_tax'] == '') ? 'Unregistered' : 'Registered')
            ->setCellValue('K'.$row_count, ($sq_supply['state_name'] == '') ? 'NA' : $sq_supply['state_name'])
            ->setCellValue('L'.$row_count, $row_query['taxation_type'])
            ->setCellValue('M'.$row_count, $row_query['service_tax'])
            ->setCellValue('N'.$row_count, number_format($taxable_amount,2))
            ->setCellValue('O'.$row_count, ($sq_tax_name['tax_type'] == 'IGST') ? $tax_per : '0')
            ->setCellValue('P'.$row_count, ($sq_tax_name['tax_type'] == 'IGST') ? $tax_amount : '0')
            ->setCellValue('Q'.$row_count, ($sq_tax_name['tax_type'] == 'SGST+CGST') ? ($tax_per/2) : '0')
            ->setCellValue('R'.$row_count, ($sq_tax_name['tax_type'] == 'SGST+CGST') ? ($tax_amount/2) : '0')
            ->setCellValue('S'.$row_count, ($sq_tax_name['tax_type'] == 'SGST+CGST') ? ($tax_per/2) : '0')
            ->setCellValue('T'.$row_count, ($sq_tax_name['tax_type'] == 'SGST+CGST') ? ($tax_amount/2) : '0')
            ->setCellValue('U'.$row_count, ($sq_tax_name['tax_type'] == 'UGST') ? $tax_per : '0')
            ->setCellValue('V'.$row_count, ($sq_tax_name['tax_type'] == 'UGST') ? $tax_amount : '0') 
            ->setCellValue('W'.$row_count,'0.00')
            ->setCellValue('X'.$row_count,'0.00')
            ->setCellValue('Y'.$row_count, '')
            ->setCellValue('Z'.$row_count, '');


        $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($content_style_Array);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($borderArray);    

      $row_count++;   
}

$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count,'' )
        ->setCellValue('C'.$row_count, '')
        ->setCellValue('D'.$row_count, '')
        ->setCellValue('E'.$row_count,'' )
        ->setCellValue('F'.$row_count, '')
        ->setCellValue('G'.$row_count,'' )
        ->setCellValue('H'.$row_count,'' )
        ->setCellValue('I'.$row_count,'' )
        ->setCellValue('J'.$row_count,'' )
        ->setCellValue('K'.$row_count,'' )
        ->setCellValue('L'.$row_count, 'Total')
        ->setCellValue('M'.$row_count,'' )
        ->setCellValue('N'.$row_count,'' )
        ->setCellValue('O'.$row_count,'' )
        ->setCellValue('P'.$row_count,number_format($igst_total,2) )
        ->setCellValue('Q'.$row_count,'' )
        ->setCellValue('R'.$row_count,number_format($scgst_total/2,2))
        ->setCellValue('S'.$row_count,'' )
        ->setCellValue('T'.$row_count,number_format($scgst_total/2,2))
        ->setCellValue('U'.$row_count, '')
        ->setCellValue('V'.$row_count,number_format($ugst_total,2))
        ->setCellValue('W'.$row_count,'' )
        ->setCellValue('X'.$row_count, '')
        ->setCellValue('Y'.$row_count,'' )
        ->setCellValue('Z'.$row_count, '');

    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($header_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Z'.$row_count)->applyFromArray($borderArray);
//////////////////////////****************Content End**************////////////////////////////////
  

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


for($col = 'A'; $col !== 'N'; $col++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="GST On Purchase Cancellation('.date('d-m-Y H:i:s').').xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
