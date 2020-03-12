<?php use Tools\Controllers\ToolsController;

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>Id</th>
			<th>NAME</th>
			<th>Des</th>
			<th>Operation</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach (ToolsController::$toolsList as $tools): ?>
			<?php $toolsInfo=ToolsController::$toolsInfoMap[$tools];?>
			<tr>
				<td><?php echo $tools; ?></td>
				<td><?php echo $toolsInfo["name"]; ?></td>
				<td><?php echo $toolsInfo["desc"]; ?></td>
				<td><a rel="async" href="/tools/execTools?id=<?php echo $tools; ?>" target="_blank"
				           class="btn-link">Exec</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
</div>
<script type="text/javascript">
	seajs.use(["jquery","ywj/msg","jquery-lazyload"],function($,Msg){
		$(document).ready(function(){
			//图片懒加载
			$("img.lazy").lazyload({effect:"fadeIn"});

			$("input[type=submit]").click(function(){
				var $websiteId=$("#website_id");
				var $websiteGoodsId=$("#website_goods_id");
				var websiteId=$.trim($websiteId.val());
				var websiteGoodsId=$.trim($websiteGoodsId.val());
				if(websiteGoodsId && websiteId=="0"){
					Msg.show("please select website id!");
					return false;
				}
				return true;
			});
		});
	});
</script>
<?php include $templatePath . "inc/footer.inc.php"; ?>
