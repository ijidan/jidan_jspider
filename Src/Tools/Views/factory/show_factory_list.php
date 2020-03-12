<?php

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<div class="operate-bar">
		<a href="/factory/updateFactory" class="btn" data-width="1400">添加工厂</a>
	</div>
	<h3 class="table-title">工厂列表</h3>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<td>工厂ID</td>
			<td>工厂名称</td>
			<td>工厂信息</td>
			<td>操作</td>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($factoryList as $factory): ?>
			<tr>
				<td><?php echo $factory["id"]; ?></td>
				<td><?php echo $factory["factory_name"]; ?></td>
				<td>
					<table>
						<tbody>
						<tr><td>主营产品</td><td><?php echo $factory["factory_product_category_name"]; ?></td></tr>
						<tr><td>证书情况</td><td><?php echo $factory["product_certificate_name"]; ?></td></tr>
						<tr><td>工厂自有品牌</td><td><?php echo $factory["factory_brand"]; ?></td></tr>
						<tr><td>工厂网站</td><td><?php echo $factory["factory_website"]; ?></td></tr>
						<tr><td>工厂地址</td><td><?php echo $factory["factory_address"]; ?></td></tr>
						<tr><td>是否工厂</td><td><?php echo $factory["is_real_factory_name"]; ?></td></tr>
						<tr><td>员工数量</td><td><?php echo $factory["factory_staff_num_name"]; ?></td></tr>
						<tr><td>工厂态度</td><td><?php echo $factory["factory_attitude"]; ?></td></tr>
						<tr><td>工厂评分</td><td><?php echo $factory["factory_grade"]; ?></td></tr>
						<tr><td>综合评价</td><td><?php echo $factory["factory_comment"]; ?></td></tr>
						</tbody>
					</table>
				</td>
				<td>
					<a href="/factory/showDetail?factory_id=<?php echo $factory['id'];?>" target="_blank">详情</a>|
					<a href="/factory/updateFactory?factory_id=<?php echo $factory['id'];?>" target="_blank">编辑</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
