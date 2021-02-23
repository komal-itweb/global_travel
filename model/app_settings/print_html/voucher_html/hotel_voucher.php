<?php
//Generic Files
include "../../../model.php"; 
include "../print_functions.php";
?>
<?php
$hotel_accomodation_id = $_GET['hotel_accomodation_id'];

$sq_service_voucher1 =mysql_fetch_assoc(mysql_query("select * from terms_and_conditions where type='Hotel Service Voucher' and active_flag ='Active'"));
$sq_accomodation1 =  mysql_query("select * from hotel_booking_entries where booking_id='$hotel_accomodation_id'") ;
while($sq_accomodation = mysql_fetch_assoc( $sq_accomodation1)){


$hotel_id = $sq_accomodation['hotel_id'];

$sq_hotel = mysql_fetch_assoc( mysql_query("select * from hotel_master where hotel_id='$hotel_id'") );
$mobile_no = $encrypt_decrypt->fnDecrypt($sq_hotel['mobile_no'], $secret_key);
$email_id1 = $encrypt_decrypt->fnDecrypt($sq_hotel['email_id'], $secret_key);

$booking_id = $sq_accomodation['booking_id'];
$sq_booking = mysql_fetch_assoc(mysql_query("select * from hotel_booking_master where booking_id='$booking_id'"));
$pass_name=$sq_booking['pass_name'];
$total_pax = $sq_booking['adults'] + $sq_booking['childrens'] + $sq_booking['infants'] ;

//Total days
$total_days1=strtotime($sq_accomodation['check_out']) - strtotime($sq_accomodation['check_in']);
$total_days = round($total_days1 / 86400);

$sq_customer_name = mysql_fetch_assoc(mysql_query("select * from customer_master where customer_id='$sq_booking[customer_id]'"));
$name = $sq_customer_name['first_name'].' '.$sq_customer_name['last_name'];
$contact_no = $sq_customer_name['contact_no'];
$email_id = $sq_customer_name['email_id'];

$emp_id = $_SESSION['emp_id'];
$sq_emp = mysql_fetch_assoc(mysql_query("select * from emp_master where emp_id='$emp_id'"));
if($emp_id == '0'){ $emp_name = 'Admin';}
else { $emp_name = $sq_emp['first_name'].' ' .$sq_emp['last_name']; }
?>
 <div class="repeat_section main_block">
    <!-- header -->
    <section class="print_header main_block">
    <div class="row">
    <div class="col-md-4">
    </div>
      <div class="col-md-6 no-pad">

        <span class="title" style="font-size:18px !important;"><i class="fa fa-file-text"></i> HOTEL SERVICE VOUCHER</span>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 col-offset-md-3 mg_tp_20" style="top:30px">
        <div class="print_header_logo">
          <ul class="no-pad no-marg font_s_12 noType">
            <li><h3 class=" font_5 font_s_16 no-marg no-pad caps_text"><?php echo $app_name; ?></h3></li>
            <li><p><i class="fa fa-address-card" style="margin-right: 5px;"></i><span><?php echo ($branch_status=='yes' && $role!='Admin') ? $branch_details['address1'].','.$branch_details['address2'].','.$branch_details['city'] : $app_address ?></span></p></li>
            <li><i class="fa fa-phone" style="margin-right: 5px;"></i> <?php echo ($branch_status=='yes' && $role!='Admin') ? 
           $branch_details['contact_no'] : $app_contact_no ?></li>
            <li><i class="fa fa-envelope" style="margin-right: 5px;"></i> <?php echo $app_email_id; ?></li>
          </ul>      
        </div>
      </div>
      <div class="col-md-4 col-offset-md-3 no-pad">
        <div class="print_header_logo">
          <img src="<?= $admin_logo_url ?>" class="img-responsive mg_tp_10">
        </div>
      </div>
      <div class="col-md-4 mg_tp_20" style="top:30px">
        <div class="print_header_contact text-right">
          <span class="title"><?php echo $sq_hotel['hotel_name']; ?></span><br>
          <p> <i class="fa fa-address-card" style="margin-right: 5px;"></i><?php echo $sq_hotel['hotel_address']; ?></p>
          <p class="no-marg"><i class="fa fa-phone" style="margin-right: 5px;"></i> <?php echo $mobile_no; ?></p>
          <p><i class="fa fa-envelope" style="margin-right: 5px;"></i> <?php echo $email_id1; ?></p>
        </div>
      </div>
      </div>
    </section>

    <!-- print-detail -->
    <section class="print_sec main_block">
    <div class="section_heding">
        <h2>BOOKING DETAILS</h2>
        <div class="section_heding_img">
          <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="print_info_block">
            <ul class="main_block noType">
              <li class="col-md-3 mg_tp_10 mg_bt_10">
                <div class="print_quo_detail_block">
                  <i class="fa fa-hourglass-half" aria-hidden="true"></i><br>
                  <span>DURATION</span><br>
                  <?= ($total_days).' Nights' ?><br>
                </div>
              </li>
              <li class="col-md-3 mg_tp_10 mg_bt_10">
                <div class="print_quo_detail_block">
                  <i class="fa fa-users" aria-hidden="true"></i><br>
                  <span>TOTAL GUEST(s) <?php echo $sq_booking['adults']+$sq_booking['childrens']+$sq_booking['infants']?></span><br>
                  <?=$sq_booking['adults'] ?> Adult,<?= $sq_booking['childrens'] ?> Child,<?= $sq_booking['infants'] ?> Infant<br>
                </div>
              </li>
              <li class="col-md-3 mg_tp_10 mg_bt_10">
                <div class="print_quo_detail_block">
                  <i class="fa fa-home" aria-hidden="true"></i><br>
                  <span>TOTAL ROOM(s)</span><br>
                  <?= $sq_accomodation['rooms'] ?><br>
                </div>
              </li>
              <li class="col-md-3 mg_tp_10 mg_bt_10">
                <div class="print_quo_detail_block">
                  <i class="fa fa-university" aria-hidden="true"></i><br>
                  <span>ROOM CATEGORY</span><br>
                  <?= $sq_accomodation['category'] ?><br>
                </div>
              </li>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- BOOKING -->
    <section class="print_sec main_block">
      <div class="row">
        
        <div class="col-md-6 mg_bt_10">
          <ul class="print_info_list no-pad noType">
            <li><span>GUEST NAME :</span> <?= $name ?></li>
          </ul>
        </div>
        <div class="col-md-6 mg_bt_10">
          <ul class="print_info_list no-pad noType">
            <li><span>CONFIRMATION ID :</span> <?= $sq_accomodation['conf_no'] ?></li>
          </ul>
        </div>
        </div>
        <!-- <div class="row">
        <div class="col-md-6 mg_bt_10">
          <ul class="print_info_list no-pad noType">
            <li><span>PASSENGER NAME :</span> <?= $pass_name ?></li>
          </ul>
        </div>
        <div class="col-md-6 mg_bt_10">
          <ul class=" no-pad noType">
            <li><span class="hidden">PASSENGER NAME :</span></li>
          </ul>
        </div> 
        </div>-->
        <div class="row">
        <div class="col-md-6 mg_bt_10">
          <ul class="print_info_list no-pad noType">
            <li><span>CHECK-IN  :</span> <?= date('d-m-Y H:i',strtotime($sq_accomodation['check_in'])) ?></li>
          </ul>
        </div>
        <div class="col-md-6 mg_bt_10">
          <ul class="print_info_list no-pad noType">
            <li><span>CHECK-OUT :</span> <?= date('d-m-Y H:i',strtotime($sq_accomodation['check_out'])) ?></li>
          </ul>
        </div>
        <div class="col-md-6 mg_bt_10">
          <ul class="print_info_list no-pad noType">
            <li><span>MEAL PLAN :</span> <?= $sq_accomodation['meal_plan'] ?></li>
          </ul>
        </div>
        <div class="col-md-6 mg_bt_10">
          <ul class="print_info_list no-pad noType">
            <li><span>CONTACT :</span> <?= $sq_hotel['immergency_contact_no'] ?></li>
          </ul>
        </div>
      </div>
    </section>
<!--- other details --->
    <section class="print_sec main_block">
      <div class="row">
        <div class="col-md-12 mg_bt_10">
        <span style="font-size:14px;font-weight:bold;">Other Details :</span><?= $sq_booking['other_comment'];?>
        </div>
      </div>
    </section>
    
    <!-- Terms and Conditions -->
    <section class="print_sec main_block">
      <div class="row">
        <div class="col-md-12">
          <div class="section_heding">
            <h2>Terms and Conditions</h2>
            <div class="section_heding_img">
              <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
            </div>
          </div>
          <div class="print_text_bolck">
            <?= $sq_service_voucher1['terms_and_conditions']; ?>
          </div>
        </div>
      </div>
    </section>

    <p style="float: left;width: 100%;"><b>Note: Please present this service voucher to service provider (Hotel/Transport) upon arrival</b></p>

    <!-- Payment Detail -->
    <section class="print_sec main_block">
      <div class="row">
        <div class="col-md-7"></div>
        <div class="col-md-5">
          <div class="print_quotation_creator text-center">
            <span>Generated BY </span><br><?= $emp_name ?>
          </div>
        </div>
      </div>
    </section>
    </div>
  </body>
</html>
<?php } ?>