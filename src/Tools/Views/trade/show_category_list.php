<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form action="" class="search-frm well">
		<div class="frm-item">
			<label>商品分类</label>
		</div>
	</form>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>分类ID</th>
			<th>分类名称</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($categoryList as $categoryId=> $categoryName): ?>
			<tr>
				<td> <?php echo $categoryId;?></td>
				<td> <?php echo $categoryName;?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
