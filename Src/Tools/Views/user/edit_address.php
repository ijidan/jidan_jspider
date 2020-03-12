<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form action="" class="frm" rel="async" method="post">
			<table class="data-tbl frm-row-tbl">
				<tbody>
				<tr>
					<td>ID</td>
					<td>
						<input type="text" name="id" style="background-color:#ccc;" readonly
						       value="<?php echo $addressData['id']; ?>">
					</td>
				</tr>
				<tr>
					<td>Name</td>
					<td>
						<input type="text" name="name" required value="<?php echo $addressData['accept_name']; ?>">
					</td>
				</tr>
				<tr>
					<td>Mobile</td>
					<td><input type="tel" name="mobile" required="required"
					           value="<?php echo $addressData['telphone']; ?>"></td>
				</tr>
				<tr>
					<td>Address</td>
					<td><input type="text" name="address" required="required"
					           value="<?php echo $addressData['address']; ?>"></td>
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