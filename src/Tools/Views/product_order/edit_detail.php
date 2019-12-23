<?php use Lib\Util\CommonUtil;

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>
<?php
$TPL = <<<EOT
<tr>
	<td>Hình ảnh sản phẩm</br>商品图片</td>
	<td>
		<input name="product_img[]" type="hidden" rel="upload-image" data-src="" value=""/>
	</td>
	<td>
		<span class="small-btn small-delete-btn" rel="row-delete-btn-self"  title="Delete">Delete</span>
		<span class="small-btn small-up-btn" rel="row-up-btn" title="Up">Up</span>
		<span class="small-btn small-down-btn" rel="row-down-btn" title="Down">Down</span>
	</td>
</tr>
EOT;
?>
	<style type="text/css">
		tr td:first-child { max-width:100px; }

		tr input[class*=txt], tr input[type=number] { min-width:400px; }
	</style>
	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form action="" class="frm" rel="async" method="post">
			<input type="hidden" name="id" value="<?php echo $id;?>"/>
			<table class="data-tbl frm-tbl">
				<tbody>
				<tr>
					<td>Tên hàng </br>商品名称
					</td>
					<td><input class="txt" type="text" name="product_name"
					           value="<?php echo $productOrder['product_name']; ?>"/></td>
					<td></td>
				</tr>
				<tr>
					<td>Mô tả sản phẩm (Kích thước, trọng lượng, màu sắc, cỡ, size...)</br>
						商品属性
					</td>
					<td><input class="txt" type="text" name="product_attr"
					           value="<?php echo $productOrder['product_attr']; ?>"/></td>
					<td></td>
				</tr>
				<tr>
					<td>Link sản phẩm</br>
						商品链接
					</td>
					<td><input class="txt" type="text" name="product_link"
					           value="<?php echo $productOrder['product_link']; ?> "/></td>
					<td></td>
				</tr>
				<tr>
					<td>Đơn giá /sp</br>
						单价
					</td>
					<td>
						<input class="txt" type="number" step="0.01" name="product_price"
						       value="<?php echo $productOrder['product_price']; ?>"/>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>Số lượng</br>数量
					</td>
					<td>
						<input class="txt" type="number" step="1" name="product_num"
						       value="<?php echo $productOrder['product_num']; ?>"/>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>Ship nội địa TQ/sp</br>
						中国内地 运费
					</td>
					<td>
						<input class="txt" type="number" step="0.01" name="cn_freight"
						       value="<?php echo $productOrder['cn_freight']; ?>"/>
					</td>
					<td></td>
				</tr
				<tr>
					<td>Ship về Hà Nội /sp</br>
						越南河内运费
					</td>
					<td><input class="txt" type="number" step="0.01" name="cn_vn_freight"
					           value="<?php echo $productOrder['cn_vn_freight']; ?>"/>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>Việt Nam /sp</br>
						越南河内运费
					</td>
					<td><input class="txt" type="number" step="0.01" name="vn_freight"
					           value="<?php echo $productOrder['vn_freight']; ?>"/></td>
					<td></td>
				</tr>
				<tr>
					<td>Phí mua hàng
						(5%-8%)</br>
						购买 费用
					</td>
					<td><input class="txt" type="number" step="0.01" name="buy_fee"
					           value="<?php echo $productOrder['buy_fee']; ?>"/></td>
					<td></td>
				</tr>
				<tr>
					<td>Phí tìm kiếm nguồn hàng( Nếu có)
						</br>商品寻找费用
					</td>
					<td><input class="txt" type="number" step="0.01" name="search_fee"
					           value="<?php echo $productOrder['search_fee']; ?>"/></td>
					<td></td>
				</tr>
				<tr>
					<td>Phí bảo hiểm 10%( Nếu có)</br>
						保险费
					</td>
					<td><input class="txt" type="number" step="0.01" name="insure_fee"
					           value="<?php echo $productOrder['insure_fee']; ?>"/ >
					</td>
					<td></td>
				</tr>
				<?php
				$productImg = CommonUtil::convertStringToArray($productOrder['product_img']); ?>
				<?php if($productImg):?>
					<?php foreach ($productImg as $img): ?>
						<tr>
							<td>Hình ảnh sản phẩm</br>商品图片</td>
							<td>
								<input name="product_img[]" type="hidden" rel="upload-image" data-src="<?php echo $img;?>" value="<?php echo $img;?>"/>
							</td>
							<td>
								<span class="small-btn small-delete-btn" rel="row-delete-btn-self"  title="Delete">Delete</span>
								<span class="small-btn small-up-btn" rel="row-up-btn" title="Up">Up</span>
								<span class="small-btn small-down-btn" rel="row-down-btn" title="Down">Down</span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else:?>
					<?php echo $TPL; ?>
				<?php endif;?>
				</tbody>
				<tfoot>
				<tr>
					<td></td>
					<td colspan="20">
						<span class="small-btn small-add-btn" rel="row-append-btn" data-tpl="row-template">add one</span>
					</td>
					<td></td>
				</tr>
				</tfoot>
			</table>
			<table class="frm-tbl">
				<tr>
					<td></td>
					<td><input type="submit" value="submit" class="btn"></td>
				</tr>
			</table>
		</form>
	</div>
	<script type="text/template" id="row-template"><?php echo $TPL; ?></script>
	<script>
		seajs.use(["jquery", 'ywj/table'], function ($, T) {
			$('body').delegate('*[rel=row-delete-btn-self]', 'click', function () {
				var row = $(this).parentsUntil('tr').parent();
				T.deleteRow(row, true);
			});
		});
	</script>
<?php include $templatePath . "inc/footer.inc.php"; ?>