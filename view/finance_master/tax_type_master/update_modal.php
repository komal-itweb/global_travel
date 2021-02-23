<?php
include "../../../model/model.php";

$tax_type_id = $_POST['tax_type_id'];
$sq_tax_type = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$tax_type_id'"));
?>
<form id="frm_update">
<input type="hidden" id="tax_type_id" name="tax_type_id" value="<?= $tax_type_id ?>">

<div class="modal fade" id="update_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Update Taxation</h4>
      </div>
      <div class="modal-body">
        
		<div class="row">
			<div class="col-sm-6 mg_bt_10_sm_xs">
				<input type="text" id="tax_type" name="tax_type" placeholder="TAX Name" title="TAX Name" style="text-transform: uppercase" value="<?= strtoupper($sq_tax_type['tax_type']) ?>">
			</div>			
			<div class="col-sm-6 mg_bt_10_sm_xs">
				<select name="active_flag" id="active_flag" title="Status">
          <option value="<?= $sq_tax_type['active_flag'] ?>"><?= $sq_tax_type['active_flag'] ?></option>
					<option value="Active">Active</option>
					<option value="Inactive">Inactive</option>
				</select>
			</div>
		</div>
		
		<div class="row text-center mg_tp_20">
              <div class="col-md-12">
                <button class="btn btn-success" id="btn_update"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>  
              </div>      
        </div>


      </div>
    </div>
  </div>
</div>
</form>

<script>
$('#update_modal').modal('show');

$(function(){
  $('#frm_update').validate({
    rules:{
			tax_type : { required : true },			
			active_flag : { required : true },
    },
    submitHandler:function(form){

        var base_url = $('#base_url').val();
		var tax_type_id = $('#tax_type_id').val();		
		var tax_type = $('#tax_type').val();
		var active_flag = $('#active_flag').val();
 
        $('#btn_update').button('loading');

        $.post(
               base_url+"controller/finance_master/tax_type_master/tax_type_master_update.php",
               { tax_type_id : tax_type_id, tax_type : tax_type, active_flag : active_flag },
               function(data) {
                  $('#btn_update').button('reset');
                  var msg = data.split('--');
                  if(msg[0]=="error"){
                    error_msg_alert(msg[1]);
                  }else{
                    msg_alert(data);
                    $('#btn_update').button('reset');
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