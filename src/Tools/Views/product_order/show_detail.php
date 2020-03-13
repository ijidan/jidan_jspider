<?php use Lib\Util\CommonUtil;

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>
	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form action="" class="frm" rel="async" method="post">
			<table class="data-tbl frm-tbl">
				<tbody>
				<tr>
					<td>Tên hàng </br>
						商品名称
					</td>
					<td><?php echo $productOrder["product_name"]; ?></td>
					<td></td>
				</tr>
				<tr>
					<td>Mô tả sản phẩm (Kích thước, trọng lượng, màu sắc, cỡ, size...)</br>
						商品属性
					</td>
					<td><?php echo $productOrder["product_attr"]; ?></td>
					<td></td>
				</tr>
				<tr>
					<td>Link sản phẩm</br>
						商品链接
					</td>
					<td><a target="_blank" href="<?php echo $productOrder["product_link"]; ?>"><?php echo $productOrder["product_link"]; ?></a>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>Đơn giá /sp</br>
						单价
					</td>
					<td><?php echo $productOrder["product_price"]; ?></td>
					<td></td>
				</tr>
				<tr>
					<td>Số lượng</br>数量
					</td>
					<td><?php echo $productOrder["product_num"]; ?></td>
					<td></td>
				</tr>
				<tr>
					<td>Ship nội địa TQ/sp</br>
						中国内地 运费
					</td>
					<td><?php echo $productOrder["cn_freight"]; ?></td>
					<td></td>
				</tr
				<tr>
					<td>Ship về Hà Nội /sp</br>
						越南河内运费
					</td>
					<td><?php echo $productOrder["cn_vn_freight"]; ?></td>
					<td></td>
				</tr>
				<tr>
					<td>Việt Nam /sp</br>
						越南河内运费
					</td>
					<td><?php echo $productOrder["vn_freight"]; ?></td>
					<td></td>
				</tr>
				<tr>
					<td>Phí mua hàng(5%-8%)</br>购买费用
					</td>
					<td><?php echo $productOrder["buy_fee"]; ?></td>
					<td></td>
				</tr>
				<tr>
					<td>Phí tìm kiếm nguồn hàng( Nếu có)
						</br>商品寻找费用
					</td>
					<td><?php echo $productOrder["search_fee"]; ?></td>
					<td></td>
				</tr>
				<tr>
					<td>Phí bảo hiểm 10%( Nếu có)</br>
						保险费
					</td>
					<td><?php echo $productOrder["insure_fee"]; ?></td>
					<td></td>
				</tr>
				<tr>
					<td>Hình ảnh sản phẩm</br>商品图片</td>
					<td rel="img-slide">
						<?php
						$productImg = CommonUtil::convertStringToArray($productOrder['product_img']); ?>
						<?php foreach ($productImg as $img): ?>
							<img rel="slide-img" data-big_img="<?php echo $img;?>" src="<?php echo $img; ?>" style="width:100px;height: 100px;;"/></br>
						<?php endforeach; ?>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>