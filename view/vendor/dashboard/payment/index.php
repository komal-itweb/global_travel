<?php
include "../../../../model/model.php";
$branch_status = $_POST['branch_status'];
include_once('payment_save_modal.php');
?>
<div class="row text-right">
	<div class="col-md-12">
		<button class="btn btn-excel btn-sm" onclick="excel_report()" data-toggle="tooltip" title="Generate Excel"><i class="fa fa-file-excel-o"></i></button>&nbsp;&nbsp;
		<button class="btn btn-info btn-sm ico_left" data-toggle="modal" data-target="#v_payment_save_modal"><i class="fa fa-plus"></i>&nbsp;&nbsp;Payment</button>
	</div>
</div>
<div class="col-md-12 col-sm-9 no-pad mg_bt_20 text-right">
	<span style="color: red;line-height: 35px;" data-original-title="" title="" class="note">Please make Supplier Payment entry against multiple bookings only.</span>
</div>

<div class="app_panel_content Filter-panel">
	<div class="row">
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
			<select name="vendor_type2" id="vendor_type2" title="Supplier Type" onchange="vendor_type_data_load(this.value, 'div_vendor_type_content2')">
				<option value="">Supplier Type</option>
				<?php 
				$sq_vendor = mysql_query("select * from vendor_type_master order by vendor_type");
				while($row_vendor = mysql_fetch_assoc($sq_vendor)){
					?>
					<option value="<?= $row_vendor['vendor_type'] ?>"><?= $row_vendor['vendor_type'] ?></option>
					<?php
				}
				?>
			</select>
		</div>
		<div id="div_vendor_type_content2"></div>
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
          <select name="estimate_type2" id="estimate_type2" title="Purchase Type" onchange="payment_for_data_load(this.value, 'div_payment_for_content_i', '2')">
            <option value="">Purchase Type</option>
            <?php 
            $sq_estimate_type = mysql_query("select * from estimate_type_master order by estimate_type");
            while($row_estimate = mysql_fetch_assoc($sq_estimate_type)){
              ?>
              <option value="<?= $row_estimate['estimate_type'] ?>"><?= $row_estimate['estimate_type'] ?></option>
              <?php
            }
            ?>
          </select>
        </div>
        <div id="div_payment_for_content_i"></div>
	</div>
	<div class="row">
		<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10 hidden">
			<select name="financial_year_id_filter" id="financial_year_id_filter" title="Financial Year">
				<?php get_financial_year_dropdown(); ?>
			</select>
		</div>
		<div class="col-md-3">
			<button class="btn btn-sm btn-info ico_right" onclick="payment_list_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
		</div>
	</div>
</div>
<div id="div_payment_list_reflect" class="main_block loader_parent">
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
	<table id="payment" class="table table-hover" style="margin: 20px 0 !important;">         
	</table>
</div></div></div>
</div>
<div id="div_vendor_report_content" class="main_block loader_parent"></div>
<div id="div_payment_update_content"></div>
<style>
.action_width{
	width : 250px;
}
</style>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script>
var column = [
	{ title : "S_No."},
	{ title:"Supplier_Type", className:"text-center"},
	{ title : "Supplier_Name"},
	{ title : "Purchase_Type"},
	{ title : "Purchase_ID"},
	{ title : "Date"},
	{ title : "Amount"},
	{ title : "Mode"},
	{ title : "Bank_Name"},
	{ title : "Cheque_No/Id"},
	{ title : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Actions", className:"text-center action_width"}
];
function payment_list_reflect()
{
  $('#div_vendor_report_content').append('<div class="loader"></div>');
	var vendor_type = $('#vendor_type2').val();
 	var vendor_type_id = get_vendor_type_id('vendor_type2');
 	var estimate_type = $('#estimate_type2').val();
 	var estimate_type_id = get_estimate_type_id('estimate_type2', '2');
 	var financial_year_id = $('#financial_year_id_filter').val();
  var branch_status = $('#branch_status').val();
	$.post('payment/payment_list_reflect.php', { vendor_type : vendor_type, vendor_type_id : vendor_type_id,estimate_type : estimate_type, estimate_type_id : estimate_type_id, financial_year_id : financial_year_id, branch_status : branch_status }, function(data){
		pagination_load(data, column, true, true, 20, 'payment');
		$('.loader').remove();
	});
}
payment_list_reflect();

payment_evidence_upload();
function payment_evidence_upload(offset='')
{
    var btnUpload=$('#payment_evidence_upload'+offset);
    var status=$('#payment_evidence_status'+offset);
    new AjaxUpload(btnUpload, {
      action: 'payment/upload_payment_evidence.php',
      name: 'uploadfile',
      onSubmit: function(file, ext){

         var id_proof_url = $("#payment_evidence_url"+offset).val();
          
     
         if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
          status.text('Only JPG, PNG or GIF files are allowed');
          //return false;
        }
        status.text('Uploading...');
      },
      onComplete: function(file, response){
        //On completion clear the status
        status.text('');
        //Add uploaded file to list
        if(response==="error"){          
          alert("File is not uploaded.");           
          //$('<li></li>').appendTo('#files').html('<img src="./uploads/'+file+'" alt="" /><br />'+file).addClass('success');
        } else{
          ///$('<li></li>').appendTo('#files').text(file).addClass('error');
          $("#payment_evidence_url"+offset).val(response);
          msg_alert('File uploaded!');
        }
      }
    });

}

function payment_update_modal(payment_id){
	$.post('payment/payment_update_modal.php', { payment_id : payment_id }, function(data){
		$('#div_payment_update_content').html(data);
		payment_list_reflect();
	});
}

function excel_report()
{
	var vendor_type = $('#vendor_type2').val();
	var vendor_type_id = get_vendor_type_id('vendor_type2');
 	var estimate_type = $('#estimate_type2').val();
 	var estimate_type_id = get_estimate_type_id('estimate_type2', '2');
	var financial_year_id = $('#financial_year_id_filter').val();
  var branch_status = $('#branch_status').val();
	window.location = 'payment/excel_report.php?financial_year_id='+financial_year_id+'&vendor_type='+vendor_type+''+'&vendor_type_id='+vendor_type_id+'&estimate_type='+estimate_type+''+'&estimate_type_id='+estimate_type_id+'&branch_status='+branch_status;
}

</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>