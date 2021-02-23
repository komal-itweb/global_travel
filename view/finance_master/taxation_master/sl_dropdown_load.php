<?php
include "../../../model/model.php";

$gl_id = $_POST['gl_id'];
?>
<option value="">SL Code</option>
<?php 
$sq_sl = mysql_query("select * from sl_master where sl_id in (select sl_id from sl_gl_mapping where gl_id='$gl_id')");
while($row_sl = mysql_fetch_assoc($sq_sl)){
	?>
	<option value="<?= $row_sl['sl_id'] ?>"><?= $row_sl['sl_name'] ?></option>
	<?php
}
?>