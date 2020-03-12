<?php use Tools\Models\Article;

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>


<style type="text/css">
	h1.table-title{margin-top:20px;margin-bottom:10px;font-family:"宋体"}
</style>
<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<div class="operate-bar">
		<a href="/trade/editProduct" class="btn" data-width="1400">添加商品</a>
	</div>
	<?php foreach ($goodsCategory as $catId=>$catName):?>
		<h1 class="table-title"><?php echo $catName;?></h1>
		<table class="data-tbl" data-empty-fill="1">
			<thead>
			<tr>
				<th>商品ID</th>
				<th>商品编号</th>
				<th>商品名称</th>
				<th>商品信息</th>
				<th>商品图片</th>
				<th>操作</th>
			</tr>
			</thead>
			<tbody>
			<?php $currentGoodsList=$goodsListByCategory[$catId];?>
			<?php foreach ($currentGoodsList as $productInfo): ?>
				<tr>
					<td><?php echo $productInfo["id"];?></td>
					<td><?php echo $productInfo["product_model_no"];?></td>
					<td><?php echo $productInfo["product_english_name"];?><br><?php echo $productInfo["product_chinese_name"];?></td>
					<td>
							<table>
								<tbody>
									<tr><td>商品编号</td><td><?php echo $productInfo["product_model_no"];?></td></tr>
									<tr><td>商品分类</td><td><?php echo $productInfo["product_category"];?></td></tr>
									<tr><td>中文名称</td><td><?php echo $productInfo["product_chinese_name"];?></td></tr>
									<tr><td>英文名称</td><td><?php echo $productInfo["product_english_name"];?></td></tr>
									<tr><td>商品单价</td><td><?php echo $productInfo["product_item_price"];?></td></tr>
									<tr><td>主要功能</td><td><?php echo $productInfo["product_function"];?></td></tr>
									<tr><td>主要特性</td><td><?php echo $productInfo["product_main_features"];?></td></tr>
									<tr><td>商品规格</td><td><?php echo $productInfo["product_item_specifics"];?></td></tr>
									<tr><td>装箱信息</td><td><?php echo $productInfo["product_packing_information"];?></td></tr>
									<tr><td>参考链接</td><td><?php echo $productInfo["product_urls"];?></td></tr>
								</tbody>
						</table>
					</td>
					<td rel="img-slide">
						<?php foreach ($productInfo["product_img"] as $img): ?>
							<img rel="slide-img" data-big_img="<?php echo $img; ?>" src="<?php echo $img; ?>"
							     style="width:50px;height:50px;border:none"/>
						<?php endforeach; ?>
					</td>
					<td><a href="/trade/showDetail?product_id=<?php echo $productInfo['id'];?>" target="_blank">详情</a>|
						<a href="/trade/editProduct?product_id=<?php echo $productInfo['id'];?>" target="_blank">编辑</a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endforeach;?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
