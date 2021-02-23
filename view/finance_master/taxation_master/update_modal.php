<?php
include "../../../model/model.php";

$taxation_id = $_POST['taxation_id'];
$tax_type_id = $_POST['tax_type_id'];

$sq_taxation = mysql_fetch_assoc(mysql_query("select * from taxation_master where taxation_id='$taxation_id'"));

$sq_tax_type = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$tax_type_id'"));
?>
<form id="frm_update">
<input type="hidden" id="taxation_id" name="taxation_id" value="<?= $taxation_id ?>">

<div class="modal fade" id="update_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Update Tax</h4>
      </div>
      <div class="modal-body">
        
		<div class="row">
			<div class="col-sm-6 mg_bt_10">
				<select name="tax_type_id" id="tax_type_id" title="Tax Name" disabled>
					<?php 
					
					?>
					<option value="<?= $sq_tax_type['tax_type_id'] ?>"><?=  strtoupper($sq_tax_type['tax_type']) ?></option>
					<option value="">Tax Name</option>
					<?php
					$sq_tax_type = mysql_query("select * from tax_type_master where country_id='$setup_country_id'");
					while($row_tax_type = mysql_fetch_assoc($sq_tax_type)){
						?>
						<option value="<?= $row_tax_type['tax_type_id'] ?>"><?=  strtoupper($row_tax_type['tax_type']) ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<div class="col-sm-6 mg_bt_10">
				<input type="text" id="tax_in_percentage" name="tax_in_percentage" onchange="validate_balance(this.id)" placeholder="Tax in percentage" title="Tax in percentage" value="<?= $sq_taxation['tax_in_percentage'] ?>" readonly>
			</div>			
		</div>
		<div class="row">
			<div class="col-sm-6 mg_bt_10">
				<select name="active_flag" id="active_flag" title="Status">
				    <option value="<?php echo $sq_taxation['active_flag']; ?>"><?php echo $sq_taxation['active_flag'];  ?></option>
					<option value="Active">Active</option>
					<option value="Inactive">Inactive</option>
				</select>
			</div>	
		</div>		
		<div class="row text-center mg_tp_10">
              <div class="col-md-12">
                <button class="btn btn-sm btn-success" id="btn_update"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
              </div>      
        </div>
      </div>
    </div>
  </div>
</div>
</form>

<script>
$('#update_modal').modal('show');
$('#from_date, #to_date').datetimepicker({ timepicker:false, format:'d-m-Y' });

$(function(){
  $('#frm_update').validate({
    rules:{
			tax_type_id : { required : true },
			tax_in_percentage : { required : true },
			active_flag : { required : true },
    },
    submitHandler:function(form){

        var base_url = $('#base_url').val();
		var taxation_id = $('#taxation_id').val();
		var tax_type_id = $('#tax_type_id').val();
		var tax_in_percentage = $('#tax_in_percentage').val();
		var active_flag = $('#active_flag').val();
 
        $('#btn_update').button('loading');

        $.post(
               base_url+"controller/finance_master/taxation_master/taxation_master_update.php",
               { taxation_id : taxation_id, tax_type_id : tax_type_id, tax_in_percentage : tax_in_percentage, active_flag : active_flag },
               function(data) {
                  $('#btn_update').button('reset');
                  var msg = data.split('--');
                  if(msg[0]=="error"){
                    error_msg_alert(msg[1]);
                  }else{
                    msg_alert(data);
                    $('#update_modal').modal('hide');  
                    $('#update_modal').on('hidden.bs.modal', function(){
                      list_reflect();
                    });
                  }
                  
        });  

    }
  });
});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>