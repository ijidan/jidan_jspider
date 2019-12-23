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
						<?php $img = "//g-search3.alicdn.com/img/bao/uploaded/i4/i2/701836241/TB2v5YknFXXXXagXFXXXXXXXXXX_!!701836241.jpg_230x230.jpg_.webp"; ?>
						<img src="<?php echo $img; ?>" width="200px;height:200px;"/>
					</td>
				</tr>
				<tr>
					<td>Vietnamese Name</td>
					<td>
						<?php echo $category["vi_vn_name"]; ?>
					</td>
				</tr>
				<tr>
					<td>Chinese Name</td>
					<td>
						<?php echo $category["zh_cn_name"]; ?>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>