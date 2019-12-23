<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form action="" class="frm" rel="async" method="post">
			<table class="data-tbl frm-row-tbl design_image_tbl">
				<tbody>
				<tr style="display:none;">
					<td>Order Goods Id</td>
					<td><input type="text" name="id" value="<?php echo $orderGoods['id'];?>"></td>
				</tr>
				<tr>
					<td>Order Id</td>
					<td><?php echo $orderGoods['order_id']; ?></td>
				</tr>
				<tr>
					<td>Goods Id</td>
					<td><?php echo $orderGoods['goods_id']; ?></td>
				</tr>
				<tr>
					<td>Goods Name</td>
					<td><?php echo $orderGoods['goods_name']; ?></td>
				</tr>
				<tr>
					<td>Goods Attr</td>
					<td>
						<input type="text" name="goods_attr" value="<?php echo $orderGoods['goods_attr']; ?>">
					</td>
				</tr>
				<tr>
					<td>Goods Weight</td>
					<td>
						<input type="number" step="0.01" name="goods_weight" value="<?php echo $orderGoods['goods_weight']; ?>">
					</td>
				</tr>
				<tr>
					<td>Goods Price</td>
					<td>
						<input type="number" step="0.01" name="goods_price" value="<?php echo $orderGoods['goods_price']; ?>">
					</td>
				</tr>
				<tr>
					<td>Goods Number</td>
					<td>
						<input type="number" step="1" name="goods_num" value="<?php echo $orderGoods['goods_num']; ?>">
					</td>
				</tr>
				<tr>
					<td>China Freight</td>
					<td>
						<input type="number" step="0.01"  name="goods_freight" value="<?php echo $orderGoods['goods_freight']; ?>">
					</td>
				</tr>
				<tr>
					<td>Inter Freight</td>
					<td><input type="number" step="0.01"  name="inter_freight" value="<?php echo $orderGoods['inter_freight']; ?>">
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="submit" class="btn"></td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>