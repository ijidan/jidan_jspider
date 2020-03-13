<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form action="" class="frm" rel="async" method="post">
			<table class="data-tbl frm-row-tbl design_image_tbl">
				<tbody>
				<tr>
					<td>Vietnamese</td>
					<td>
						<input type="text" name="vi_vn">
					</td>
				</tr>
				<tr>
					<td>Chinese(Simplified)</td>
					<td><input type="text" name="zh_cn"></td>
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