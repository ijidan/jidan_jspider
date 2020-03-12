<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>
<?php
$TPL = <<<EOT
<tr>
	<td>Avatar</td>
	<td>
		<input name="avatar" type="hidden" rel="upload-image" data-src="" value=""/>
	</td>
</tr>
EOT;
?>
	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form action="/admin/addOperator" class="frm" rel="async" method="post">
			<table class="data-tbl frm-row-tbl design_image_tbl">
				<tbody>
				<tr>
					<td>账号</td>
					<td>
						<input type="text" name="account" required="required">
					</td>
				</tr>
				<tr>
					<td>密码</td>
					<td><input type="password" name="password" required="required"></td>
				</tr>
				<tr>
					<td>角色</td>
					<td>
						<select name="role_id" class="input-sm">
							<?php foreach ($roleList as $key => $val) : ?>
								<option
									value="<?php echo $val['id']; ?>"><?php echo $val['role_name'] . '(' . $val['role_id'] . ')'; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<?php echo $TPL; ?>
				<tr>
					<td></td>
					<td><input type="submit" value="submit" class="btn"></td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>