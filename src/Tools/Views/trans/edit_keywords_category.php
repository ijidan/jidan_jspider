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
					<td>Id</td>
					<td>
						<?php echo $category["id"]; ?>
					</td>
				</tr>
				<tr>
					<td>Img</td>
					<td>
						<img src="<?php echo $category['img']; ?>" width="200px;height:200px;"/>
					</td>
				</tr>
				<tr>
					<td>New Img</td>
					<td>
						<input class="long-txt txt" type="text" name="img" value="<?php echo $category['img']; ?>""/>
					</td>
				</tr>
				<tr>
					<td>Vietnamese Name</td>
					<td>
						<input class="txt" type="text" name="vi_vn_name" value="<?php echo $category["vi_vn_name"]; ?>"/>
					</td>
				</tr>
				<tr>
					<td>English Name</td>
					<td>
						<input class="txt" type="text" name="en_us_name" value="<?php echo $category["en_us_name"]; ?>"/>
					</td>
				</tr>
				<tr>
					<td>Chinese Name</td>
					<td>
						<input class="txt" type="text" name="zh_cn_name" value="<?php echo $category["zh_cn_name"]; ?>"/>
					</td>
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