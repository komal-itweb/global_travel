<?php
include "../../../model/model.php";
?>
<div class="row"> <div class="col-md-12 no-pad"> <div class="table-responsive">
	
<table class="table table-hover" id="tbl_list" style="margin: 20px 0 !important;">
	<thead>
		<tr class="table-heading-row">
			<th>S_No.</th>
			<th>Tax_Name</th>
			<th>Tax (%)</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$count = 0;
		$sq_taxation = mysql_query("select * from taxation_master");
		while($row_taxation = mysql_fetch_assoc($sq_taxation)){

			$sq_gl = mysql_fetch_assoc(mysql_query("select * from gl_master where gl_id='$row_taxation[gl_id]'"));
			$sq_sl = mysql_fetch_assoc(mysql_query("select * from sl_master where sl_id='$row_taxation[sl_id]'"));

			$sq_tax_type = mysql_fetch_assoc(mysql_query("select * from tax_type_master where tax_type_id='$row_taxation[tax_type_id]'"));

			$bg = ($row_taxation['active_flag']=="Active") ? "" : "danger";
			?>
			<tr class="<?= $bg ?>">
				<td><?= ++$count ?></td>
				<td><?= strtoupper($sq_tax_type['tax_type']) ?></td>
				<td><?= $row_taxation['tax_in_percentage'] ?></td>
				<td>
					<button class="btn btn-info btn-sm" onclick="update_modal(<?= $row_taxation['taxation_id'] ?>,<?= $row_taxation['tax_type_id'] ?>)" title="Update Tax"><i class="fa fa-pencil-square-o"></i></button>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>

</div> </div> </div>

<script>
$('#tbl_list').dataTable({
		"pagingType": "full_numbers"
	});
</script>