<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<style type="text/css">
	td .title { display:inline-block; width:70px; text-align:right; }
	td .content { display:inline-block; width:100px; text-align:left; }
</style>
<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form action="" class="search-frm well">
		<div class="frm-item">
			<label>
				Id：
				<input type="text" class="txt" name="goods_id" value="<?php echo $search['goods_id']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Name：
				<input type="text" class="txt" name="goods_name" value="<?php echo $search['goods_name']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Price：
				<input type="text" name="start_price" value="<?php echo $search['start_price'];?>"
				       class="txt"/> -
				<input type="text" name="end_price" value="<?php echo $search['end_price'];?>"
				       class="txt"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Website：
				<select name="website_id" id="website_id">
					<?php if(!$search["website_id"]):?>
						<option value="0">--All--</option>
					<?php endif;?>
					<?php foreach(\Tools\Models\Goods::$websiteIdNameMap as $k=>$v):?>
						<option value="<?php echo $k;?>" <?php if($k==$search["website_id"]): ?> selected="selected" <?php endif;?>>
							<?php echo $v;?>
						</option>
					<?php endforeach?>
				</select>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Website Goods Id：
				<input type="text" class="txt" name="website_goods_id" id="website_goods_id" value="<?php echo $search['website_goods_id']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<input type="submit" value="Search" class="btn"/>
			<a href="/goods/showList">Reset</a>
		</div>
	</form>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>Id</th>
			<th>Img</th>
			<th>Name</th>
			<th>Sell Price</th>
			<th>Website</th>
			<th>Website Goods Id</th>
			<th>Website Link</th>
			<th>Update Time</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($goodsList as $goods): ?>
			<?php
			$imgList = $goods["img_list"];
			$imgListArr = $imgList ? explode(",", $imgList) : [];
			$goodsImg = $imgListArr ? $imgListArr[0] : "";
			?>
			<tr>
				<td><?php echo $goods["id"]; ?></td>
				<td>
					<?php if ($goodsImg): ?>
						<a href="<?php echo $goodsImg;?>" target="_blank"><img class="lazy" data-original="<?php echo $goodsImg; ?>" width="60px;height:60px;" border="0"/></a>
					<?php endif; ?>
				</td>
				<td><?php echo $goods["name"]; ?></td>
				<td><?php echo $goods["sell_price"]; ?></td>
				<td><?php echo \Tools\Models\Goods::$websiteIdNameMap[$goods["website_id"]]; ?></td>
				<td><?php echo $goods["website_goods_id"]; ?></td>
				<td><a target="_blank" href="<?php echo $goods["website_link"]; ?>"><?php echo $goods["website_link"]; ?></a></td>
				<td><?php echo date("Y-m-d H:i:s", $goods["update_time"]); ?></td>、
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
