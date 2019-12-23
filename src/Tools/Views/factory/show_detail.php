<?php

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<div class="operate-bar">
		<h3 class="table-title">工厂详情</h3>
	</div>

		<table class="data-tbl" data-empty-fill="1">
			<tbody>
			<tr><td>工厂编号</td><td><?php echo $factory_info["id"];?></td></tr>
			<tr><td>工厂名称</td><td><?php echo $factory_info["factory_name"];?></td></tr>
			<tr><td>主营产品</td><td><?php echo $factory_info["factory_product_category_name"];?></td></tr>
			<tr><td>证书情况</td><td><?php echo $factory_info["product_certificate_name"];?></td></tr>
			<tr><td>加工方式</td><td><?php echo $factory_info["factory_process_method"];?></td></tr>
			<tr><td>工艺类型</td><td><?php echo $factory_info["factory_process_type"];?></td>
			<tr><td>工厂自有品牌</td><td><?php echo $factory_info["factory_brand"];?></td></tr>
			<tr><td>工厂网站</td><td><?php echo $factory_info["factory_website"];?></td></tr>
			<tr><td>工厂地址</td><td><?php echo $factory_info["factory_address"];?></td></tr>
			<tr><td>交通说明</td><td><?php echo $factory_info["factory_traffic"];?></td></tr>
			<tr><td>是否工厂</td><td><?php echo $factory_info["is_real_factory_name"];?></td></tr>
			<tr><td>厂房说明</td><td><?php echo $factory_info["factory_situation "];?></td></tr>
			<tr><td>员工数量</td><td><?php echo $factory_info["factory_staff_num_name"];?></td></tr>
			<tr><td>工厂态度</td><td><?php echo $factory_info["factory_attitude"];?></td</tr>
			<tr><td>工厂评分</td><td><?php echo $factory_info["factory_grade"];?></td></tr>
			<tr><td>是否推荐</td><td><?php echo $factory_info["is_recommend_name"];?></td></tr>
			<tr><td>推荐/不推荐理由</td><td><?php echo $factory_info["recommend_reason"];?></td></tr>
			<tr><td>综合评价</td><td><?php echo $factory_info["factory_comment"];?></td></tr>
			<tr><td>工厂图片</td><td><?php echo $factory_info["factory_img"];?></td></tr>
			</tbody>
		</table>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
