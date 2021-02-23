<?php
include_once('../../../model/model.php');
global $similar_text,$currency_logo;
$quotation_id1 = $_GET['quotation_id'];
$quotation_id = base64_decode($quotation_id1);

//$quotation_id = 40;
$sq_quotation = mysql_fetch_assoc(mysql_query("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));
$date = $sq_quotation['created_at'];
$yr = explode("-", $date);
$year =$yr[0];
$sq_cost =  mysql_fetch_assoc(mysql_query("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id'"));

$basic_cost = $sq_cost['basic_amount'];
	$service_charge = $sq_cost['service_charge'];
	$tour_cost= $basic_cost +$service_charge;
		$service_tax_amount = 0;
		$tax_show = '';
		$bsmValues = json_decode($sq_cost['bsmValues']);
		// var_dump($bsmValues);
	if($sq_cost['service_tax_subtotal'] !== 0.00 && ($sq_cost['service_tax_subtotal']) !== ''){
	  $service_tax_subtotal1 = explode(',',$sq_cost['service_tax_subtotal']);
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

$quotation_cost = $basic_cost +$service_charge+ $service_tax_amount+ + $sq_quotation['train_cost']+ $sq_quotation['flight_cost'] + $sq_quotation['cruise_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'];

$sq_package_name = mysql_fetch_assoc(mysql_query("select * from package_quotation_program where quotation_id='$quotation_id'"));


$schedule_content = '';

$sq_train_count = mysql_num_rows(mysql_query("select * from custom_package_program where package_id ='$sq_quotation[package_id]'"));

if($sq_train_count>0){



	$schedule_content .= '

				<tr>

					<td>
						<table style="padding:0; width:100%; border-collapse:collapse; margin-bottom:20px; text-align:left;">
								<tr>
									<td colspan="5" style="padding:5px; border:1px solid #c1c1c1; text-align:center; font-weight:600; background: #ddd;color: #3c3c3c;">Itinerary Information</td>
								</tr>
								<tr>
									<th style="padding:5px; border:1px solid #c1c1c1">Attraction</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Day-wise Program</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Overnight Stay</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Meal Plan</th>
								</tr>

	';

 

	$count = 0;

	$sq_package_program = mysql_query("select * from  package_quotation_program where quotation_id='$quotation_id' and package_id ='$sq_quotation[package_id]'");



	while($row_schedule = mysql_fetch_assoc($sq_package_program)){



		$schedule_content .= '

							<tr>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_schedule['attraction'].'</td>

								<td style="padding:5px; border:1px solid #c1c1c1"><pre style="font-family: Roboto, sans-serif;overflow: initial;background: transparent;border: 0;white-space: pre-wrap;

    word-wrap: break-word;line-height: 21px;">'.$row_schedule['day_wise_program'].'</pre></td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_schedule['stay'].'</td>
								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_schedule['meal_plan'].'</td>

							</tr>	

		';



	}



	$schedule_content .= '

						</table>

					</td>

				</tr>

	';



}



$train_content = '';

$sq_train_count = mysql_num_rows(mysql_query("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id'"));

if($sq_train_count>0){



	$train_content .= '

				<tr>

					<td>

						<table style="padding:0; width:100%; border-collapse:collapse; margin-bottom:20px; text-align:left;">

								<tr>

									<td colspan="6" style="padding:5px; border:1px solid #c1c1c1; text-align:center; font-weight:600; background: #ddd;color: #3c3c3c;">Train Information</td>

								</tr>

								<tr>

									<th style="padding:5px; border:1px solid #c1c1c1">Sr. No</th>

									<th style="padding:5px; border:1px solid #c1c1c1">From</th>

									<th style="padding:5px; border:1px solid #c1c1c1">To</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Class</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Departure_Datetime</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Arrival_Datetime</th> 

								</tr>

																

						

	';



	$count = 0;

	$sq_train = mysql_query("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id'");

	while($row_train = mysql_fetch_assoc($sq_train)){



		$train_content .= '

							<tr>

								<td style="padding:5px; border:1px solid #c1c1c1">'.++$count.'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_train['from_location'].'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_train['to_location'].'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_train['class'].'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.get_datetime_user($row_train['departure_date']).'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.get_datetime_user($row_train['arrival_date']).'</td>

							</tr>	

		';



	}



	$train_content .= '

						</table>

					</td>

				</tr>

	';



}



$plane_content = '';

$sq_plane_count = mysql_num_rows(mysql_query("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id'"));

if($sq_plane_count>0){



	$plane_content .= '

				<tr>

					<td>

						<table style="padding:0; width:100%; border-collapse:collapse; margin-bottom:20px; text-align:left;">

								<tr>

									<td colspan="7" style="padding:5px; border:1px solid #c1c1c1; text-align:center; font-weight:600; background: #ddd;color: #3c3c3c;">Flight Information</td>

								</tr>

								<tr>

									<th style="padding:5px; border:1px solid #c1c1c1">Sr. No</th>

									<th style="padding:5px; border:1px solid #c1c1c1">From</th>

									<th style="padding:5px; border:1px solid #c1c1c1">To</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Airline</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Class</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Departure_Datetime</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Arrival_Datetime</th>

								</tr>

																

						

	';



	$count = 0;

	$sq_plane = mysql_query("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id'");

	while($row_plane = mysql_fetch_assoc($sq_plane)){
		$sq_airline = mysql_fetch_assoc(mysql_query("select * from airline_master where airline_id='$row_plane[airline_name]'"));


		$plane_content .= '

							<tr>

								<td style="padding:5px; border:1px solid #c1c1c1">'.++$count.'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_plane['from_location'].'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_plane['to_location'].'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$sq_airline['airline_name'].' ('.$sq_airline['airline_code'].')'.'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_plane['class'].'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.get_datetime_user($row_plane['dapart_time']).'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.get_datetime_user($row_plane['arraval_time']).'</td>

							</tr>	

		';



	}



	$plane_content .= '

						</table>

					</td>

				</tr>

	';



}

$cruise_content = '';
$sq_cruise_count = mysql_num_rows(mysql_query("select * from package_tour_quotation_cruise_entries where quotation_id ='$quotation_id'"));

if($sq_cruise_count>0){



	$cruise_content .= '

				<tr>

					<td>

						<table style="padding:0; width:100%; border-collapse:collapse; margin-bottom:20px; text-align:left;">

								<tr>

									<td colspan="6" style="padding:5px; border:1px solid #c1c1c1; text-align:center; font-weight:600; background: #ddd;color: #3c3c3c;">Cruise Information</td>

								</tr>

								<tr>

									<th style="padding:5px; border:1px solid #c1c1c1">Sr. No</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Departure_Datetime</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Arrival_Datetime</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Route</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Cabin</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Sharing</th>

								</tr>

																

						

	';

 

	$count = 0;

	$sq_cruise_program = mysql_query("select * from  package_tour_quotation_cruise_entries where quotation_id ='$quotation_id'");



	while($row_cruise_program = mysql_fetch_assoc($sq_cruise_program)){



		$cruise_content .= '

							<tr>

								<td style="padding:5px; border:1px solid #c1c1c1">'.++$count.'</td>
								<td style="padding:5px; border:1px solid #c1c1c1">'.get_datetime_user($row_cruise_program['dept_datetime']).'</td>
								<td style="padding:5px; border:1px solid #c1c1c1">'.get_datetime_user($row_cruise_program['arrival_datetime']).'</td>
								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_cruise_program['route'].'</td>
								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_cruise_program['cabin'].'</td>
								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_cruise_program['sharing'].'</td>

							</tr>	

		';



	}



	$cruise_content .= '

						</table>

					</td>

				</tr>

	';



}

$hotel_content = '';

$sq_hotel_count = mysql_num_rows(mysql_query("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'"));

if($sq_hotel_count>0){



	$hotel_content .= '

				<tr>

					<td>

						<table style="padding:0; width:100%; border-collapse:collapse; margin-bottom:20px; text-align:left;">

								<tr>

									<td colspan="7" style="padding:5px; border:1px solid #c1c1c1; text-align:center; font-weight:600; background: #ddd;color: #3c3c3c;">Hotel Information</td>

								</tr>

								<tr>

									<th style="padding:5px; border:1px solid #c1c1c1">Sr. No</th>

									<th style="padding:5px; border:1px solid #c1c1c1">City Name</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Hotel Name</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Room Type</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Check-IN</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Check-OUT</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Meal Plan</th>

								</tr>

																

						

	';



	$count = 0;

	$sq_hotel = mysql_query("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'");

	while($row_hotel = mysql_fetch_assoc($sq_hotel)){
		$sq_city = mysql_fetch_assoc(mysql_query("select * from city_master where city_id='$row_hotel[city_id]'"));
		$hotel_name = mysql_fetch_assoc(mysql_query("select * from hotel_master where hotel_id='$row_hotel[hotel_name]'"));
		$city_name = mysql_fetch_assoc(mysql_query("select * from city_master where city_id='$row_hotel[city_name]'"));

		

		$hotel_content .= '

							<tr>

								<td style="padding:5px; border:1px solid #c1c1c1">'.++$count.'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$city_name['city_name'].'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$hotel_name['hotel_name'].$similar_text.'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_hotel['room_category'].'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.get_date_user($row_hotel['check_in']).'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.get_date_user($row_hotel['check_out']).'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$hotel_name['meal_plan'].'</td>

							</tr>	

		';



	}



	$hotel_content .= '

						</table>

					</td>

				</tr>

	';



}



$transport_content = '';

$sq_transport_count = mysql_num_rows(mysql_query("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'"));

if($sq_transport_count>0){



	$transport_content .= '

				<tr>

					<td>

						<table style="padding:0; width:100%; border-collapse:collapse; margin-bottom:20px; text-align:left;">

								<tr>

									<td colspan="6" style="padding:5px; border:1px solid #c1c1c1; text-align:center; font-weight:600; background: #ddd;color: #3c3c3c;">Transport Information</td>

								</tr>

								<tr>

									<th style="padding:5px; border:1px solid #c1c1c1">Sr. No</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Vehicle</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Start Date</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Pickup Location</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Drop Location</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Total Vehicles</th>

								</tr>

																

						

	';



	$count = 0;

	$sq_transport = mysql_query("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'");

	while($row_transport = mysql_fetch_assoc($sq_transport)){

		$transport_name = mysql_fetch_assoc(mysql_query("select * from b2b_transfer_master where entry_id='$row_transport[vehicle_name]'"));
		// Pickup
		if($row_transport['pickup_type'] == 'city'){
		  $row = mysql_fetch_assoc(mysql_query("select city_id,city_name from city_master where city_id='$row_transport[pickup]'"));
		  $pickup = $row['city_name'];
		}
		else if($row_transport['pickup_type'] == 'hotel'){
		  $row = mysql_fetch_assoc(mysql_query("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[pickup]'"));
		  $pickup = $row['hotel_name'];
		}
		else{
		  $row = mysql_fetch_assoc(mysql_query("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[pickup]'"));
		  $airport_nam = clean($row['airport_name']);
		  $airport_code = clean($row['airport_code']);
		  $pickup = $airport_nam." (".$airport_code.")";
		}
		//Drop-off
		if($row_transport['drop_type'] == 'city'){
		  $row = mysql_fetch_assoc(mysql_query("select city_id,city_name from city_master where city_id='$row_transport[drop]'"));
		  $drop = $row['city_name'];
		}
		else if($row_transport['drop_type'] == 'hotel'){
		  $row = mysql_fetch_assoc(mysql_query("select hotel_id,hotel_name from hotel_master where hotel_id='$row_transport[drop]'"));
		  $drop = $row['hotel_name'];
		}
		else{
		  $row = mysql_fetch_assoc(mysql_query("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_transport[drop]'"));
		  $airport_nam = clean($row['airport_name']);
		  $airport_code = clean($row['airport_code']);
		  $drop = $airport_nam." (".$airport_code.")";
		}

		$transport_content .= '

							<tr>

								<td style="padding:5px; border:1px solid #c1c1c1">'.++$count.'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$transport_name['vehicle_name'].$similar_text.'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.get_date_user($row_transport['start_date']).'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$pickup.'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$drop.'</td>

								<td style="padding:5px; border:1px solid #c1c1c1">'.$row_transport['vehicle_count'].'</td>

							</tr>	

		';



	}



	$transport_content .= '

						</table>

					</td>

				</tr>

	';



}

$excursion_content = '';

$sq_ex_count = mysql_num_rows(mysql_query("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'"));

if($sq_ex_count>0){



	$excursion_content .= '

				<tr>

					<td>

						<table style="padding:0; width:100%; border-collapse:collapse; margin-bottom:20px; text-align:left;">

								<tr>

									<td colspan="6" style="padding:5px; border:1px solid #c1c1c1; text-align:center; font-weight:600; background: #ddd;color: #3c3c3c;">Activity Information</td>

								</tr>

								<tr>

									<th style="padding:5px; border:1px solid #c1c1c1">Sr. No</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Activity Date</th>
									<th style="padding:5px; border:1px solid #c1c1c1">City Name</th>

									<th style="padding:5px; border:1px solid #c1c1c1">Activity Name</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Transfer Option</th>
									<th style="padding:5px; border:1px solid #c1c1c1">Activity Amount</th>

								</tr>
	';



	$count = 0;
	$sq_excursion = mysql_query("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'");

	while($row_excursion = mysql_fetch_assoc($sq_excursion)){

		$sq_city = mysql_fetch_assoc(mysql_query("select * from city_master where city_id='$row_excursion[city_name]'"));
		$sq_ex_name = mysql_fetch_assoc(mysql_query("select * from excursion_master_tariff where entry_id='$row_excursion[excursion_name]'"));

		$excursion_content .= '

			<tr>

				<td style="padding:5px; border:1px solid #c1c1c1">'.++$count.'</td>
				<td style="padding:5px; border:1px solid #c1c1c1">'.get_datetime_user($row_excursion['exc_date']).'</td>
				<td style="padding:5px; border:1px solid #c1c1c1">'.$sq_city['city_name'].'</td>

				<td style="padding:5px; border:1px solid #c1c1c1">'.$sq_ex_name['excursion_name'].'</td>
				<td style="padding:5px; border:1px solid #c1c1c1">'.$row_excursion['transfer_option'].'</td>
				<td style="padding:5px; border:1px solid #c1c1c1">'.$currency_logo.' '.$row_excursion['excursion_amount'].'</td>

			</tr>

		';



	}



	$excursion_content .= '

						</table>

					</td>

				</tr>

	';



}



	global $app_email_id, $app_name, $app_contact_no, $admin_logo_url, $app_website;
    global $mail_em_style, $mail_em_style1, $mail_font_family, $mail_strong_style, $mail_color;

    $em_style = "border-bottom: 1px dotted #1f1f1f; padding-bottom: 4px; margin-bottom: 4px; display: inline-block; font-style:normal; color: #2fa6df; ";
    $font_family = "font-family: 'Lato', sans-serif;";
    $strong_style = "font-weight: 500; color:#000";

$sq_terms = mysql_fetch_assoc(mysql_query("select * from terms_and_conditions where type='Package Quotation' and active_flag='Active'"));



$content .= '
<!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Online Booking</title>
      <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    </head>
    <body style="font-family: Roboto, sans-serif;background-color: #ffffff;margin-top: 0px !important;margin-right: 0px !important;margin-left: 0px !important;padding: 0px !important;">
      
      <table cellspacing="0" cellpadding="0" style="width: 900px;margin: 0 auto;border: 1px solid #e2e2e2;margin-top: 1px;border-bottom: 0;">
        <tr> 
          <td>
             <a href='.$app_website.' target="blank">
                <img src="'.BASE_URL.'/images/Admin-Area-Logo.png" style="width: 225px;">
             </a>
          </td>
          <td>
            <h1 style="color: #009898;text-transform: uppercase;text-align: right;padding-right: 20px;font-weight: 400;font-size: 24px;">'.$app_name.'</h1>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <table style="width: 900px;margin-left: -4px;margin-right: -4px;background-image: url('.BASE_URL.'/images/email/bg.jpg);background-repeat: no-repeat;background-size: cover;padding: 50px 15px 15px 50px;color: #fff;line-height: 36px;font-size: 14px;">
	<tr>
		<td>
			<table style="padding:0 30px; width:100%">	

				<tr>

					<td>

						<p>We are thank you for choosing '.$app_name.'. Please refer to your quotation details.</p>

					</td>

				</tr>				

				<tr>

					<td>

						<table style="padding:0; width:100%; border-collapse:collapse; margin-bottom:20px">

								<tr>

									<td colspan="3" style="padding:5px; border:1px solid #c1c1c1; text-align:center; font-weight:600; background: #ddd;color: #3c3c3c;">Quotation Information</td>

								</tr>

								<tr>

									<td colspan="1" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Customer Name</span> : '.$sq_quotation['customer_name'].'</td>

								

									<td colspan="2" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Package Name</span> : '.$sq_quotation['tour_name'].'</td>

								</tr>

								<tr>

									<td colspan="1" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Tour Date</span> : '.date('d-m-Y', strtotime($sq_quotation['from_date'])).' to '.date('d-m-Y', strtotime($sq_quotation['to_date'])).'</td>

									<td colspan="2" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Total Days</span> : '.$sq_quotation['total_days'].'</td>

								</tr>

								<tr>

									<td colspan="1" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Adults</span> : '.$sq_quotation['total_adult'].'</td>

									<td colspan="2" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Child</span> : '.$sq_quotation['total_children'].'</td>

								</tr>

								<tr>

									<td colspan="1" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Infants</span> : '.$sq_quotation['total_infant'].'</td>

									<td colspan="1" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Child Without Bed</span> : '.$sq_quotation['children_without_bed'].'</td>

									<td colspan="1" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Child With Bed</span> : '.$sq_quotation['children_with_bed'].'</td>

								</tr>

								<tr>

									<td colspan="3" style="padding:5px; border:1px solid #c1c1c1"><span style="font-weight: 600; color: #3c3c3c;">Quotation Cost</span> : <b>'.number_format($quotation_cost,2).'</b></td>

								</tr>

						</table>

					</td>

				</tr>

				'.$train_content.$plane_content.$cruise_content.$hotel_content.$transport_content.$excursion_content.$schedule_content.'		
				<tr>

					<td>

						<table style="padding:0; width:100%; border-collapse:collapse; margin-bottom:20px; text-align:left">

								<tr>

									<td colspan="2" style="padding:5px; border:1px solid #c1c1c1; text-align:center; font-weight:600; background: #ddd;color: #3c3c3c;">Inclusions & Exclusions</td>

								</tr>									

								<tr>

									<th style="padding:5px; border:1px solid #c1c1c1">Inclusions</th>

									<td style="padding:5px; border:1px solid #c1c1c1">'.$sq_quotation['inclusions'].'</td>

								</tr>

								<tr>

									<th style="padding:5px; border:1px solid #c1c1c1">Exclusions</th>

									<td style="padding:5px; border:1px solid #c1c1c1">'.$sq_quotation['exclusions'].'.</td>

								</tr>

						</table>

					</td>

				</tr>

			</table>
		</td>
	</tr>
	</table>
	              </td>
	          </tr>
	          <tr>
	            <td colspan="2">
	              <table style="width: 900px;margin-left: -4px;margin-right: -4px;background: #1da38a;color: #fff;padding-left: 50px;font-size: 14px;padding: 10px 0 10px 50px;">
	                <tr>
	                  <td><span>'.$app_name.'</span></td>
	                  <td style="text-align: right;padding-right: 38px;"><img src="'.BASE_URL.'/images/email/phone.png" style="margin-bottom: -1px;"> <span>'.$app_contact_no.'</span></td>
	                </tr> 
	              </table>
	            </td>
	          </tr>
	        </table>
	      </body>
	    </html>

';

echo $content;

$date = date('d-m-Y H:i:s');

$content ='

<tr>
            <table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
              <tr><td style="text-align:left;border: 1px solid #888888;">Name</td>   <td style="text-align:left;border: 1px solid #888888;">'.$sq_quotation['customer_name'].'</td></tr>
              <tr><td style="text-align:left;border: 1px solid #888888;">Quotation Id</td>   <td style="text-align:left;border: 1px solid #888888;" >'.$quotation_id.'</td></tr>
              <tr><td style="text-align:left;border: 1px solid #888888;">On Date</td>   <td style="text-align:left;border: 1px solid #888888;">'.$date.'</td></tr>
            </table>
          </tr>';


$subject = 'Customer viewed quotation! ('.get_quotation_id($quotation_id,$year).' , '.$sq_quotation['customer_name'].' )';
$model->app_email_send('9','Admin',$app_email_id, $content, $subject);

?>