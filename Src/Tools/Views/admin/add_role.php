<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form action="/admin/addRole" class="frm" rel="async" method="post">
		<table class="data-tbl frm-row-tbl design_image_tbl">
			<tbody>
			<tr>
				<td class="col-label">角色ID</td>
				<td><input type="text" value="" class="txt" name="role_id" required="required"></td>
			</tr>
			<tr>
				<td class="col-label">角色名称</td>
				<td>
					<input type="text" value="" class="txt" name="role_name" required="required">
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="submit" class="btn"></td>
			</tr>
			</tbody>
		</table>
	</form>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>


