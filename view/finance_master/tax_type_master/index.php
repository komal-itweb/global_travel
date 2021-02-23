<?php
include "../../../model/model.php";
/*======******Header******=======*/
require_once('../../layouts/admin_header.php');
?>
<div class="app_panel">


  <!--=======Header panel======-->
      <div class="app_panel_head">
          <h2 class="pull-left">Tax Type Master</h2>
          <div class="pull-left header_btn">
            <button title="Help">
              <a href="http://itourscloud.com/User-Manual/" target="_blank" title="Manual">
                <i class="fa fa-info" aria-hidden="true"></i>
              </a>
            </button>
          </div>
          <div class="pull-left header_btn">
            <button data-target="#myModaltax" data-toggle="modal">
              <a title="Help">
                <i class="fa fa-question" aria-hidden="true"></i>
              </a>
            </button>
          </div>
      </div>
      <div class="header_bottom">
        <div class="row">
            <div class="col-md-10">
            </div>
            <div class="col-md-2 text-right">
                <button class="btn btn-info btn-sm ico_left" id="btn_save_modal" onclick="save_modal()"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Tax</button>
            </div>
        </div>
      </div> 

  <!--=======Header panel end======-->



<div class="app_panel_content">

<div id="div_modal"></div>
<div id="div_list_content"></div>
<?php include "guidline_modal.php";  ?>
<script>
function save_modal()
{
    $('#btn_save_modal').button('loading');
    $.post('save_modal.php', {}, function(data){
        $('#btn_save_modal').button('reset');
        $('#div_modal').html(data);
    });
}

function list_reflect()
{
	$.post('list_reflect.php', {}, function(data){
        $('#div_list_content').html(data);
    });
}
list_reflect();

function update_modal(tax_type_id)
{
    $.post('update_modal.php', {tax_type_id : tax_type_id}, function(data){
        $('#div_modal').html(data);
    });
}
function sl_dropdown_load()
{
	var gl_id = $('#gl_id').val();
	$.post('sl_dropdown_load.php', {gl_id : gl_id}, function(data){
        $('#sl_id').html(data);
    });
}
</script>
<?= end_panel() ?>
<?php
/*======******Footer******=======*/
require_once('../../layouts/admin_footer.php'); 
?>