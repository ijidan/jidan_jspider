<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>
<?php
$TPL = <<<EOT
<tr>
	<td>商品图片</td>
	<td>
		<input name="product_img[]" type="hidden" rel="upload-image" data-src="" value=""/>
	</td>
	<td>
		<span class="small-btn small-delete-btn" rel="row-delete-btn-self"  title="Delete">Delete</span>
		<span class="small-btn small-up-btn" rel="row-up-btn" title="Up">Up</span>
		<span class="small-btn small-down-btn" rel="row-down-btn" title="Down">Down</span>
	</td>
</tr>
EOT;
?>
	<style type="text/css">
		tr td:first-child { min-width:100px; }

		tr input[class*=txt], tr input[type=number] { min-width:300px; }

		tr input[type=radio] {width:80px;}
	</style>
	<link rel="stylesheet" href="/kindeditor-4.1.7/themes/default/default.css"/>
	<link rel="stylesheet" href="/kindeditor-4.1.7/plugins/code/prettify.css"/>
	<script charset="utf-8" src="/kindeditor-4.1.7/kindeditor.js"></script>
	<script charset="utf-8" src="/kindeditor-4.1.7/lang/en.js"></script>
	<script charset="utf-8" src="/kindeditor-4.1.7/plugins/code/prettify.js"></script>
	<script>
		KindEditor.ready(function (K) {
			window.editor = K.create('#editor_id', {langType: 'en'});
		});
	</script>
	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form action="" class="frm" rel="async" method="post">
			<caption>商品详情</caption>
			<table class="data-tbl frm-tbl">
				<tbody>
				<tr><td>商品编号</td><td><?php echo $productInfo["product_model_no"];?></td></tr>
				<tr><td>商品分类</td><td><?php echo $productInfo["product_category"];?></td></tr>
				<tr><td>中文名称</td><td><?php echo $productInfo["product_chinese_name"];?></td></tr>
				<tr><td>英文名称</td><td><?php echo $productInfo["product_english_name"];?></td></tr>
				<tr><td>商品单价</td><td><?php echo $productInfo["product_item_price"];?></td></tr>
				<tr><td>主要功能</td><td><?php echo $productInfo["product_function"];?></td></tr>
				<tr><td>主要特性</td><td><?php echo $productInfo["product_main_features"];?></td></tr>
				<tr><td>商品规格</td><td><?php echo $productInfo["product_item_specifics"];?></td></tr>
				<tr><td>商品描述</td><td><?php echo $productInfo["product_description"];?></td></tr>
				<tr><td>装箱信息</td><td><?php echo $productInfo["product_packing_information"];?></td></tr>
				<tr><td>参考链接</td><td><?php echo $productInfo["product_urls"];?></td></tr>
			</table>
		</form>
	</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>