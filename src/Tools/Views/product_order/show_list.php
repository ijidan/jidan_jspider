<?php use Lib\Util\CommonUtil;
use Tools\Models\ProductOrder;

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>
<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>STT</br>编号</th>
			<th>Số điện thoại</br> di động</th>
			<th>Tên hàng </br>商品名称</th>
			<th>Mô tả sản phẩm </br>商品属性</th>
			<th>Link sản phẩm</br>商品链接</th>
			<th>Hình ảnh sản phẩm</br>商品图片</th>
			<th>Đơn giá /sp</br>单价</th>
			<th>Số lượng</br>数量</th>
			<th>Operation</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($orderList as $order): ?>
			<?php
				$orderId=$order['id'];
				$productImg=CommonUtil::convertStringToArray($order['product_img']);
				$dealStatus=$order['deal_status'];
			?>
			<tr>
				<td><?php echo $orderId; ?></td>
				<td><?php echo $order['user_mobile'];?></td>
				<td><?php echo $order['product_name']; ?></td>
				<td><?php echo $order['product_attr']; ?></td>
				<td><?php echo $order['product_link']; ?></td>
				<td rel="img-slide"><?php foreach($productImg as $img):?>
						<img rel="slide-img" data-big_img="<?php echo $img;?>" src="<?php echo $img;?>" style="width:50px;height:50px;border:none"/>
					<?php endforeach;?>
				</td>
				<td><?php echo $order['product_price']; ?></td>
				<td><?php echo $order['product_num']; ?></td>
				<td>
					<a rel="popup" data-width="1000" href="/productOrder/showDetail?id=<?php echo $orderId; ?>" class="btn-link">Detail</a>|
					<a rel="popup" data-width="1000" href="/productOrder/editDetail?id=<?php echo $orderId; ?>" class="btn-link">Edit</a>|
					<a rel="async" data-width="1000" href="/productOrder/delete?id=<?php echo $orderId; ?>" data-confirm="Sure to delete?" class="btn-link">Delete</a>|
					<a rel="async" data-width="1000" href="/productOrder/bought?id=<?php echo $orderId; ?>" data-confirm="sure you have bought?" class="btn-link">I bought</a>
<!--					<a rel="async" data-width="1000" href="/productOrder/cancelBought?id=--><?php //echo $orderId; ?><!--" data-confirm="sure to cancel bought order?" class="btn-link">cancel bought</a>-->
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
