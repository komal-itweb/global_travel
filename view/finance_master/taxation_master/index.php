<?php
include "../../../model/model.php";
/*======******Header******=======*/
// require_once('../../layouts/admin_header.php');
?>
 <!-- begin_panel('Tax Details',34) ?> -->
      <div class="header_bottom">
        <div class="row">
            <div class="col-md-10">
            </div>
            <div class="col-md-2 text-right">
                <button class="btn btn-info btn-sm ico_left" id="btn_save_modal" onclick="save_modal()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Tax</button>
            </div>
        </div>
      </div> 

  <!--=======Header panel end======-->

<div class="app_panel_content">

<div id="div_modal"></div>
<div id="div_list_content"></div>

<?= end_panel() ?>
<script src="<?= BASE_URL ?>js/app/field_validation.js"></script>
<script>
function save_modal()
{
    $('#btn_save_modal').button('loading');
    $.post('../finance_master/taxation_master/save_modal.php', {}, function(data){
        $('#btn_save_modal').button('reset');
        $('#div_modal').html(data);
    });
}

function list_reflect()
{
	$.post('../finance_master/taxation_master/list_reflect.php', {}, function(data){
        $('#div_list_content').html(data);
    });
}
list_reflect();

function update_modal(taxation_id, tax_type_id)
{
    $.post('../finance_master/taxation_master/update_modal.php', {taxation_id : taxation_id , tax_type_id : tax_type_id}, function(data){
        $('#div_modal').html(data);
    });
}
function sl_dropdown_load()
{
	var gl_id = $('#gl_id').val();
	$.post('../finance_master/taxation_master/sl_dropdown_load.php', {gl_id : gl_id}, function(data){
        $('#sl_id').html(data);
    });
}
</script>
<?= end_panel() ?>
<?php
/*======******Footer******=======*/
require_once('../../layouts/admin_footer.php'); 
?>