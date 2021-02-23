<?php
include "../../../model/model.php";

?>
<form id="frm_save">
<div class="modal fade" id="save_modal" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Tax Details</h4>
      </div>
      <div class="modal-body">
        
		<div class="row">
			<div class="col-sm-6 mg_bt_10">
				<select name="tax_type_id" id="tax_type_id" title="Tax Name">
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
				<input type="text" id="tax_in_percentage" name="tax_in_percentage" onchange="validate_balance(this.id)" placeholder="*Tax i.e.5%" title="Tax in percentage">
			</div>			
		</div>
		<div class="row">
			<div class="col-sm-6">
				<select name="active_flag" id="active_flag" title="Status" class="hidden">
					<option value="Active">Active</option>
					<option value="Inactive">Inactive</option>
				</select>
			</div>
		</div>
		
		<div class="row text-center mg_tp_10">
              <div class="col-md-12">
                <button class="btn btn-sm btn-success" id="btn_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>  
              </div>      
        </div>


      </div>
    </div>
  </div>
</div>
</form>

<script>
$('#save_modal').modal('show');

$('#from_date, #to_date').datetimepicker({ timepicker:false, format:'d-m-Y' });
$(function(){
  $('#frm_save').validate({
    rules:{
			tax_type_id : { required : true },
			tax_in_percentage : { required : true },
			active_flag : { required : true },
    },
    submitHandler:function(form){

        var base_url = $('#base_url').val();
		var tax_type_id = $('#tax_type_id').val();
		var tax_in_percentage = $('#tax_in_percentage').val();
		var active_flag = $('#active_flag').val();

 
        $('#btn_save').button('loading');

        $.post(
               base_url+"controller/finance_master/taxation_master/taxation_master_save.php",
               { tax_type_id : tax_type_id, tax_in_percentage : tax_in_percentage, active_flag : active_flag },
               function(data) {
                  $('#btn_save').button('reset');
                  var msg = data.split('--');
                  if(msg[0]=="error"){
                    error_msg_alert(msg[1]);
                  }else{
                    msg_alert(data);
                    $('#save_modal').modal('hide');  
                    $('#save_modal').on('hidden.bs.modal', function(){
                      list_reflect();
                    });
                  }
                  
        });  

    }
  });
});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>