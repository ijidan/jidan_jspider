<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form class="frm" rel="async" method="post">
			<table class="data-tbl frm-row-tbl design_image_tbl">

				<tbody>
				<tr>
					<td>角色ID</td>
					<td>
						<input type="text" name="role_id" required readonly style="background:rgb(235,235,228)"
						       value="<?php echo $role['role_id']; ?>">
					</td>
				</tr>
				<tr>
					<td>角色名称</td>
					<td><input type="text" required name="role_name" value="<?php echo $role['role_name']; ?>"></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<button type="submit" class="btn btn-primary">Submit</button>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>