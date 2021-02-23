<?php
include "../../../model/model.php";
?>
<div class="row"> <div class="col-md-12 no-pad"> <div class="table-responsive">
	
<table class="table table-hover" id="tbl_list" style="margin: 20px 0 !important;">
	<thead>
		<tr class="table-heading-row">
			<th>S_No.</th>
			<th>Tax_Name</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$count = 0;
		$sq_tax_type = mysql_query("select * from tax_type_master");
		while($row_tax_type = mysql_fetch_assoc($sq_tax_type)){


			$bg = ($row_tax_type['active_flag']=="Active") ? "" : "danger";
			?>
			<tr class="<?= $bg ?>">
				<td><?= ++$count ?></td>
				<td><?= strtoupper($row_tax_type['tax_type']) ?></td>
				<td>
					<button class="btn btn-info btn-sm" onclick="update_modal(<?= $row_tax_type['tax_type_id'] ?>)" title="Update Taxation"><i class="fa fa-pencil-square-o"></i></button>
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