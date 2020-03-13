<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form class="frm" action="/index/login" method="post">
		<table class="data-tbl frm-row-tbl design_image_tbl">
			<tbody>
			<tr>
				<td>Account</td>
				<td>
					<input type="text"  placeholder="Account" name="account" autofocus>
				</td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" placeholder="Password" name="password"></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input name="remember" type="checkbox" value="1">Remember Me
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