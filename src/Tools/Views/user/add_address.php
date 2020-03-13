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
					<td>Name</td>
					<td>
						<input type="text" name="name" required value="">
					</td>
				</tr>
				<tr>
					<td>Mobile</td>
					<td><input type="tel" name="mobile" required="required"></td>
				</tr>
				<tr>
					<td>Address</td>
					<td><input type="text" name="address" required="required"></td>
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