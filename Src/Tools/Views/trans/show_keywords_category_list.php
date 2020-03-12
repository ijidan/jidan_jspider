<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>Id</th>
			<th>Img</th>
			<th>Chinese Name</th>
			<th>Vietnamese Name</th>
			<th>Option</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($categoryList as $category): ?>
			<tr>
				<td><?php echo "|-" . $category["id"]; ?></td>
				<td></td>
				<td><?php echo "|-" . $category["zh_cn_name"]; ?></td>
				<td><?php echo "|-" . $category["en_us_name"]; ?></td>
				<td></td>
			</tr>
			<?php if ($level2List = $category["parent_id"]): ?>
				<?php foreach ($level2List as $level2): ?>
					<tr>
						<td style="border:red dashed 1px;border-right:none;border-left:none;"><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;|--" . $level2["id"]; ?></td>
						<td style="border:red dashed 1px;border-right:none;"></td>
						<td style="border:red dashed 1px;border-right:none;"><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;|--" . $level2["zh_cn_name"]; ?></td>
						<td style="border:red dashed 1px;border-right:none;"><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;|--" . $level2["en_us_name"]; ?></td>
						<td style="border:red dashed 1px;border-right:none;">
							<a target="_blank" href="/trans/addCategory?id=<?php echo $level2['id']; ?>"
							   class="btn-link">Add</a>
						</td>
					</tr>
					<?php if ($level3List = $level2["parent_id"]): ?>
						<?php foreach ($level3List as $level3): ?>
							<tr>
								<td><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|---" . $level3["id"]; ?></td>
								<td>
									<?php $img = $level3['img']?:""; ?>
									<a href="<?php echo $img; ?>" class="jqzoom" target="_blank">
										<img class="lazy" data-original="<?php echo $img; ?>"
										     jqimg="<?php echo $img; ?>" src="<?php echo $img; ?>"
										     width="40px;height:40px;" border="0"/>
									</a>
								</td>
								<td><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|---" . $level3["zh_cn_name"]; ?></td>
								<td><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|---" . $level3["en_us_name"]; ?></td>
								<td>
									<?php $level3Id = $level3['id']; ?>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<a rel="popup" data-width="1000" href="/trans/categoryDetail?id=<?php echo $level3Id; ?>"
									   class="btn-link">Detail</a>|
									<a rel="popup" data-width="1000" href="/trans/editCategory?id=<?php echo $level3Id; ?>"
									   class="btn-link">Edit</a>|
									<a rel="async" data-confirm="sure to delete this goods?"
									   href="/trans/deleteCategory?id=<?php echo $level3Id; ?>"
									   class="btn-link">Delete</a>
								</td>
							</tr>
						<?php endforeach;; ?>
					<?php endif; ?>
				<?php endforeach;; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	seajs.use(["jquery", "jquery-lazyload", "jqzoom"], function ($, lazyLoad, jqzoom) {
		$(document).ready(function () {
			//图片懒加载
			$(".lazy").lazyload({effect: "fadeIn"});
			//放大镜
			$(".jquery").jqzoom({ //绑定图片放大插件jqzoom
				zoomWidth: 200, //小图片所选区域的宽
				zoomHeight: 200, //小图片所选区域的高
				zoomType: 'reverse' //设置放大镜的类型，默认standard即选中的部分变灰，这里是reverse即非选中变灰
			});
		});
	});
</script>
<?php include $templatePath . "inc/footer.inc.php"; ?>
