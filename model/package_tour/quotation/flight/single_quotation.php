<?php
include_once('../../../model.php');
global $app_name, $app_contact_no, $app_email_id, $app_landline_no,$app_address,$app_website,$similar_text;

$quotation_id1 = $_GET['quotation'];
$quotation_id = base64_decode($quotation_id1);
$count = 0;

$in = 'in';

  $sq_quotation = mysql_fetch_assoc(mysql_query("select * from flight_quotation_master where quotation_id='$quotation_id'"));

	$quotation_date = $sq_quotation['quotation_date'];
	$yr = explode("-", $quotation_date);
  $year =$yr[0];
  
  
  $sq_login = mysql_fetch_assoc(mysql_query("select * from roles where id='$sq_quotation[login_id]'"));
  $sq_emp_info = mysql_fetch_assoc(mysql_query("select * from emp_master where emp_id='$sq_login[emp_id]'"));

  if($sq_emp_info['first_name']==''){
    $emp_name = 'Admin';
  }
  else{
    $emp_name = $sq_emp_info['first_name'].' '.$sq_emp_info['last_name'];
  }

 // $sq_costing = mysql_fetch_assoc(mysql_query("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id'"));
  $basic_cost = $sq_quotation['subtotal'];
  $service_charge = $sq_quotation['service_charge'];
  $tour_cost= $basic_cost + $service_charge;
      $service_tax_amount = 0;
      $tax_show = '';
      $bsmValues = json_decode($sq_quotation['bsmValues']);
      // var_dump($bsmValues);
  if($sq_quotation['service_tax'] !== 0.00 && ($sq_quotation['service_tax']) !== ''){
    $service_tax_subtotal1 = explode(',',$sq_quotation['service_tax']);
    for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
      $service_tax = explode(':',$service_tax_subtotal1[$i]);
      $service_tax_amount +=  $service_tax[2];
      $name .= $service_tax[0] . ' ';
      $percent = $service_tax[1];
    }
  }
  if($bsmValues[0]->service != ''){   //inclusive service charge
    $newBasic = $tour_cost + $service_tax_amount;
    $tax_show = '';
  }
  else{
    // $tax_show = $service_tax_amount;
    $tax_show =  $name . $percent. ($service_tax_amount);
    $newBasic = $tour_cost;
  }

  ////////////Basic Amount Rules
  if($bsmValues[0]->basic != ''){ //inclusive markup
    $newBasic = $tour_cost + $service_tax_amount;
    $tax_show = '';
  }
  $quotation_cost = $basic_cost +$service_charge+ $service_tax_amount+ $sq_quotation['train_cost'] + $sq_quotation['cruise_cost']+ $sq_quotation['flight_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'];
  // $tour_cost = $sq_costing['tour_cost'] + $sq_costing['transport_cost']+ $sq_costing['excursion_cost'];
  $sq_transport = mysql_fetch_assoc(mysql_query("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'"));
  $sq_package_program = mysql_query("select * from  package_quotation_program where quotation_id='$quotation_id'");
  $sq_train = mysql_query("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id'");
  $sq_plane = mysql_query("select * from flight_quotation_plane_entries where quotation_id='$quotation_id'");

  $sq_package = mysql_fetch_assoc(mysql_query("select * from custom_package_master where package_id = '$sq_quotation[package_id]'"));
  $sq_terms = mysql_fetch_assoc(mysql_query("select * from terms_and_conditions where type='FIT Quotation' and active_flag='Active'"));
  $route='';
  if($sq_quotation['travel_type']=='Local'){
      $route=$sq_quotation['local_places_to_visit'];
  }
  else{
      $route=$sq_quotation['places_to_visit'];
  }

$quotation_cost = $sq_quotation['quotation_cost'] ;
$travel_date=date('d-m-Y', strtotime($sq_quotation['from_date'])).' To '.date('d-m-Y', strtotime($sq_quotation['to_date']));

?>
<!DOCTYPE html>
<html>
<head>
  <title>Flight Quotation</title>

  <meta property="og:title" content="Tour Operator Software - iTours" />
  <meta property="og:description" content="Welcome to tiTOurs leading tour operator software, CRM, Accounting, Billing, Invocing, B2B, B2C Online Software for all small scale & large scale companies" />
  <meta property="og:url" content="http://www.itouroperatorsoftware.com" />
  <meta property="og:site_name" content="iTour Operator Software" />
  <meta property="og:image" content="http://www.itouroperatorsoftware.com/images/iTours-Tour-Operator-Software-logo.png" />
  <meta property="og:image:width" content="215" />
  <meta property="og:image:height" content="83" />

  <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,500" rel="stylesheet">



  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/font-awesome-4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/jquery-ui.min.css" type="text/css" />

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/bootstrap.min.css">

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/owl.carousel.css" type="text/css" />

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/owl.theme.css" type="text/css" />

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/app/app.php">

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/app/modules/single_quotation.php">  

</head>


<body>

<header>
  <!-- Header -->
  <nav class="navbar navbar-default">

      <!-- Header-Top -->
      <div class="Header_Top">
        <div class="container">
          <div class="row">
            <div class="col-xs-12">
              <ul class="company_contact">
                <li><a href="mailto:email@company_name.com"><i class="fa fa-envelope"></i> <?= $app_email_id; ?></a></li>
                <li><i class="fa fa-mobile"></i> <?= $app_contact_no; ?></li>
                <li><i class="fa fa-phone"></i>  <?= $app_landline_no; ?></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    <div class="container">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header single_quotation_head">
        <a class="navbar-brand" href="http://<?= $app_website ?>"><img src="<?php echo BASE_URL ?>images/Admin-Area-Logo.png" class="img-responsive"></a>
        <div class="logo_right_part">
          <h1><i class="fa fa-pencil-square-o"></i> Flight Quotation</h1>
        </div>
      </div>
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="nav">
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul id="menu-center" class="nav navbar-nav">
            <li class="active"><a href="#0">Flight Details</a></li>
            <li><a href="#1">Costing</a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div>
    </div><!-- /.container-fluid -->
  </nav>
  <!-- Header-End -->
</header>





<!-- Package -->

  <section id="0" class="main_block link_page_section">

    <div class="container">

      <div class="sec_heding">

        <h2> Details</h2>

      </div>

      <div class="row">

        <div class="col-md-6 col-xs-12">

          <ul class="pack_info">

          <li><span>Customer Name </span>: <?= $sq_quotation['customer_name']; ?></li>

            <li><span>Quotation ID </span>:  <?= get_quotation_id($quotation_id,$year); ?></li>

          </ul>

        </div>

        <div class="col-md-6 col-xs-12">

          <ul class="pack_info">


            <li><span>E-mail ID </span>: <?= $sq_quotation['email_id']; ?></li>

          </ul>

        </div>

      </div>


    </div>

  </section>

<!-- Flight -->

<?php 
$str="select * from flight_quotation_plane_entries where quotation_id='$quotation_id'";
$sq_plane_count = mysql_num_rows(mysql_query($str));



if($sq_plane_count>0){ ?>

  <section id="6" class="main_block link_page_section">

    <div class="container">

      <div class="sec_heding">

        <h2>Flight</h2>

      </div>

      <div class="row">

        <div class="col-md-12">

         <div class="table-responsive">

          <table class="table table-bordered no-marg" id="tbl_emp_list">

            <thead>

              <tr class="table-heading-row">

                <th>From</th>

                <th>To</th>

                <th>Airline</th>

                <th>Class</th>

                <th>Departure_DateTime</th>

                <th>Arrival_DateTime</th>

              </tr>

            </thead>

            <tbody>

            <?php  while($row_plane = mysql_fetch_assoc($sq_plane)){   
              $sq_airline = mysql_fetch_assoc(mysql_query("select * from airline_master where airline_id='$row_plane[airline_name]'"));
              ?> 
              <tr>

                <td><?= $row_plane['from_location']; ?></td>

                <td><?= $row_plane['to_location']; ?></td>

                <td><?= $sq_airline['airline_name'].' ('.$sq_airline['airline_code'].')'; ?></td>

                <td><?= $row_plane['class']; ?></td>

                <td><?= date('d-m-Y H:i:s', strtotime($row_plane['dapart_time'])); ?></td>

                <td><?= date('d-m-Y H:i:s', strtotime($row_plane['arraval_time'])); ?></td>

              </tr>

              <?php } ?>

            </tbody>

          </table>

         </div>

       </div>

      </div>

    </div>

  </section>

<?php } ?>


<!-- Flight -->



<!-- Costing -->

  <section id="1" class="main_block link_page_section">

    <div class="container">

      <div class="sec_heding">

        <h2>Costing</h2>

      </div>

      <div class="row">

        <div class="col-md-12">

          <div class="adolence_info">

            <ul class="main_block">

              <div class="row">
                <li class="col-md-4 col-sm-6 col-xs-12 mg_bt_10"><span>Tour Cost : </span><?= round($newBasic) ?></li>
               
                <li class="col-md-4 col-sm-6 col-xs-12 mg_bt_10 sm_r_brd_r8"><span>Tax : </span><?= $tax_show ?></li>
         
                <li class="col-md-4 col-sm-6 col-xs-12 mg_bt_10_xs sm_r_brd_r8 highlight" style="font-weight: 600; color: #016d01;"><span class="highlight">Quotation Cost : </span><?= round($quotation_cost) ?></li>

              </div>
            </ul>

          </div>

        </div>

      </div>

    </div>

  </section>





<!-- Feedback -->
 <?php
 $quotation_id = base64_encode($quotation_id);
 ?>
  <section id="8" class="main_block link_page_section">

    <div class="container">

      <div class="feedback_action text-center">

        <div class="row">

            <div class="col-sm-4 col-xs-12">

              <div class="feedback_btn succes mg_bt_20">

                <button value="Interested in Booking"><a href="template_mail/quotation_email_interested.php?quotation_id=<?php echo $quotation_id; ?>" style="color:#ffffff;text-decoration:none">I'm Interested</a>

              </div>

            </div>

            <div class="col-sm-4 col-xs-12">

              <div class="feedback_btn danger mg_bt_20">

               <button value="Interested in Booking"><a href = "template_mail/quotation_email_not_interested.php?quotation_id=<?php echo $quotation_id; ?>" style="color:#ffffff;text-decoration:none">Not Interested</a>

              </div>

            </div>

            <div class="col-sm-4 col-xs-12">

              <div class="feedback_btn info mg_bt_20">

                <button type="button" data-toggle="modal" data-target="#feedback_suggestion" title="Write Suggestion">Give Suggestion</button>

              </div>

            </div>

        </div>

      </div>

    </div>

  </section>



<!-- Footer -->



  <footer class="main_block">

    <div class="footer_part">

      <div class="container">

        <div class="row">

          <div class="col-md-8 col-sm-6 col-xs-12 mg_bt_10_sm_xs">

            <div class="footer_company_cont">

              <p><i class="fa fa-map-marker"></i> <?php echo $app_address; ?></p>

            </div>

          </div>

          <div class="col-md-4 col-sm-6 col-xs-12">

            <div class="footer_company_cont text-center text_left_sm_xs">

              <p><i class="fa fa-phone"></i> <?php echo $app_contact_no; ?></p>

            </div>

          </div>

        </div>

      </div>

    </div>

  </footer>





<div class="modal fade" id="feedback_suggestion" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-md" role="document">

      <div class="modal-content">

        <div class="modal-header">

          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

          <h4 class="modal-title" id="myModalLabel">Suggestion</h4>

        </div>

        <div class="modal-body">

          <textarea class="form-control" placeholder="*Write Suggestion" id="suggestion" rows="5"></textarea>

          <div class="row mg_tp_20 text-center">

              <button class="btn btn-success" id="btn_quotation_send" onclick="multiple_suggestion_mail('<?php echo $quotation_id; ?>');"><i class="fa fa-paper-plane-o"></i>&nbsp;&nbsp;Send</a></button>

          </div>

        </div>

      </div>

    </div>

  </div>





<!-- Footer-End-->


             

  <script src="<?php echo BASE_URL ?>js/jquery-3.1.0.min.js"></script>

  <script src="<?php echo BASE_URL ?>js/jquery-ui.min.js"></script>
  <script src="<?php echo BASE_URL ?>js/bootstrap.min.js"></script>
  <script src="<?php echo BASE_URL ?>js/owl.carousel.min.js"></script>
  <script src="<?php echo BASE_URL ?>js/responsive-tabs.js"></script>

  <script type="text/javascript">
      (function($) {
          fakewaffle.responsiveTabs(['xs', 'sm']);
      })(jQuery);
    </script>



  <script type="text/javascript">



  function multiple_suggestion_mail(quotation_id){

     var base_url = $('#base_url').val();

     var suggestion = $('#suggestion').val();

    if(suggestion == ''){
      alert('Enter suggestion'); return false;
    }

    $('#btn_quotation_send').button('loading'); 

    $.ajax({

        type:'post',

        url: 'template_mail/suggestion_email_send.php',

        data:{ quotation_id : quotation_id , suggestion : suggestion},

        success: function(message){

            alert(message); 

            $('#feedback_suggestion').modal('hide');  

            $('#btn_quotation_send').button('reset');             

       }  

      }); 

  }

  </script>





  <!-- sticky-header -->

  <script type="text/javascript">

        $(document).ready(function(){



          $(window).bind('scroll', function() {

        

            var navHeight = 159; // custom nav height

        

            ($(window).scrollTop() > navHeight) ? $('div.nav').addClass('goToTop') : $('div.nav').removeClass('goToTop');

        

          });

        

        });



  // Smooth-scroll -->

         $(document).on('click', '#menu-center a', function(event){

            event.preventDefault();



            $('html, body').animate({

                scrollTop: $( $.attr(this, 'href') ).offset().top

            }, 500);

        });



  //Active-menu -->

    $("#menu-center a").click(function(){

        $(this).parent().siblings().removeClass('active');

        $(this).parent().addClass('active');

    });



  // Accordion -->

    $('#myCollapsible').collapse({

      toggle: false

    })



  function display_destination(newurl)

  {

    $.post('display_destination_image.php', { newurl : newurl}, function(data){

      $('#div_quotation_form1').html(data);

    });

    

  }

  function display_gallery(hotel_name)

  {

    $.post('display_hotel_gallery.php', { hotel_name : hotel_name}, function(data){

      $('#div_quotation_form').html(data);

    });

    

  }



  </script>



  </body>
</html>
<?php
$date = date('d-m-Y H:i:s');

$content ='

<tr>
            <table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
              <tr><td style="text-align:left;border: 1px solid #888888;">Name</td>   <td style="text-align:left;border: 1px solid #888888;">'.$sq_quotation['customer_name'].'</td></tr>
              <tr><td style="text-align:left;border: 1px solid #888888;">Quotation Id</td>   <td style="text-align:left;border: 1px solid #888888;" >'. base64_decode($quotation_id1).'</td></tr>
              <tr><td style="text-align:left;border: 1px solid #888888;">On Date</td>   <td style="text-align:left;border: 1px solid #888888;">'.$date.'</td></tr>
            </table>
          </tr>';


$subject = 'Customer viewed quotation! (ID : '. base64_decode($quotation_id1).' , '.$sq_quotation['customer_name'].' )';
$model->app_email_send('9','Admin',$app_email_id, $content, $subject);


?>