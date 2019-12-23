<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<style type="text/css">
	.info-container{display:inline-block; border:#00a2d4 solid 1px;margin-right:10px;}
	.info-title{display:inline-block;font-weight:600;padding:5px 10px;background-color:transparent; border-right:#00a2d4 dashed 1px;}
	.info-content{display:inline-block; padding:5px 10px;min-width:10px;}
	.del{text-decoration:line-through;}
</style>
<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form action="" class="search-frm well">
		<div class="frm-item">
			<label>
				Order No.：
				<input type="text" class="txt" name="order_no" value="<?php echo $search['order_no']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Order Status：
				<input type="text" class="txt" name="order_status" value="<?php echo $search['order_status']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Create Time：
				<input type="text" name="start_time" value="<?php echo date('Y-m-d H:i:s', $search['start_time']) ?>"
				       class="txt date-time-txt"/> -
				<input type="text" name="end_time" value="<?php echo date('Y-m-d H:i:s', $search['end_time']) ?>"
				       class="txt date-time-txt"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Receiver Name：
				<input type="text" class="txt" name="receiver_name" value="<?php echo $search['receiver_name']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Receiver Mobile：
				<input type="text" class="txt" name="receiver_mobile"
				       value="<?php echo $search['receiver_mobile']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				User Mobile：
				<input type="text" class="txt" name="user_mobile" value="<?php echo $search['user_mobile']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<input type="submit" value="Search" class="btn"/>
			<a href="/order/showList">Reset</a>
		</div>
	</form>
	<?php foreach ($orderList as $order): ?>
		<caption><span style="font-weight:700;font-size:14px;" >Order No:<?php echo $order['order_no'];?></span></caption>
		<table class="data-tbl" data-empty-fill="1" style="margin-bottom:10px;border:black solid 1px;">
			<?php
			$orderId = $order["id"];
			$user = $userListById[$order["user_id"]];
			$orderGoods = $orderGoodsListByOrderId[$orderId];
			?>
			<tbody>
			<tr>
				<th>Order Info</th>
				<td><div class="info-container">
						<div class="info-title">No.</div>
						<div class="info-content"><?php echo $order['order_no']; ?></div>
					</div>
					<div class="info-container">
						<div class="info-title">Crate Time</div>
						<div class="info-content"><?php echo $order['create_time']; ?></div>
					</div>
				</td>
			</tr>
			<tr>
				<th>Receiver Info</th>
				<td>
					<div class="info-container">
						<div class="info-title">Name</div>
						<div class="info-content"><?php echo $order['accept_name']; ?></div>
					</div>
					<div class="info-container">
						<div class="info-title">Mobile</div>
						<div class="info-content"><?php echo $order['mobile']; ?></div>
					</div>
					<div class="info-container">
						<div class="info-title">Address</div>
						<div class="info-content"><?php echo $order['address']; ?></div>
					</div>
				</td>
			</tr>
			<tr>
				<th>User Info</th>
				<td>
					<div class="info-container">
						<div class="info-title">Account</div>
						<div class="info-content"><?php echo $user['username']; ?></div>
					</div>
					<div class="info-container">
						<div class="info-title">Email</div>
						<div class="info-content"><?php echo $user['email']; ?></div>
					</div>
				</td>
			</tr>
			<tr style="margin-bottom:10px;">
				<th style="border-bottom:red dashed 1px;">Operation</th>
				<td style=" border-bottom:red dashed 1px;">
					<a href="/order/addGoods" class="btn-link" rel="popup">Add Goods</a>
					|<a href="" class="btn-link">Bought</a>
					|<a href="" class="btn-link">Cancel</a>
					|<a href="" class="btn-link">Logistics</a>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table style="width:100%;">
						<thead>
						<tr>
							<td>Id</td>
							<td>Goods Id</td>
							<td>Img</td>
							<td>Name</td>
							<td>Attr</td>
							<td>Weight</td>
							<td>Price</td>
							<td>Number</td>
							<td>China Freight</td>
							<td>Inter Freight</td>
							<td>Status</td>
							<td>Operation</td>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($orderGoods as $goods): ?>
							<?php
							$orderGoodsId = $goods["id"];
							$goodsId = $goods["goods_id"];
							$isDel=$goods["is_del"];
							?>
							<tr>
								<td <?php if($isDel):?> class="del" <?php endif;?>><?php echo $orderGoodsId;?></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>><?php echo $goodsId; ?></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>><img style="width:40px;height:40px;" src="<?php echo $goods['goods_img']; ?>"/></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>><?php echo $goods['goods_name']; ?></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>><?php echo $goods['goods_attr']; ?></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>><?php echo $goods['goods_weight']; ?></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>><?php echo $goods['goods_price']; ?></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>><?php echo $goods['goods_num']; ?></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>><?php echo $goods['goods_freight']; ?></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>><?php echo $goods['inter_freight']; ?></td>
								<td <?php if($isDel):?> class="del" <?php endif;?>>
									<?php if($isDel):?>
										<span>Deleted</span>
									<?php else:?>
										<span style="color:red;">Valid</span>
									<?php endif;?>

								<td>
									<?php if($isDel):?>
										<a rel="async" data-confirm="sure to Active this goods?"
										   href="/order/activeGoods?id=<?php echo $orderGoodsId; ?>"
										   class="btn-link">Active</a>

									<?php else:?>
										<a href="/order/editGoods?id=<?php echo $orderGoodsId; ?>" target="_blank"
										   class="btn-link">Edit</a>|
										<a rel="async" data-confirm="sure to delete this goods?"
										   href="/order/cancelGoods?id=<?php echo $orderGoodsId; ?>"
										   class="btn-link">Delete</a>
									<?php endif;?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
			</tbody>
		</table>
	<?php endforeach; ?>
	<?php echo $paginate; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
