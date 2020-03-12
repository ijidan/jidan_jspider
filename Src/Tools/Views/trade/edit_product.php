<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>
<?php
$TPL = <<<EOT
<tr>
	<td>商品图片</td>
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
		tr td:first-child { min-width:100px; }

		tr input[class*=txt], tr input[type=number] { min-width:300px; }

		tr input[type=radio] {width:80px;}

		.tips {color:red;font-weight:700;}
	</style>
	<link rel="stylesheet" href="/kindeditor-4.1.7/themes/default/default.css"/>
	<link rel="stylesheet" href="/kindeditor-4.1.7/plugins/code/prettify.css"/>
	<script charset="utf-8" src="/kindeditor-4.1.7/kindeditor.js"></script>
	<script charset="utf-8" src="/kindeditor-4.1.7/lang/en.js"></script>
	<script charset="utf-8" src="/kindeditor-4.1.7/plugins/code/prettify.js"></script>
	<script>
		KindEditor.ready(function (K) {
			window.editor = K.create('#editor_id', {langType: 'en'});
		});
	</script>
	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form action="" class="frm" rel="async" method="post">
			<caption>添加商品</caption>
			<table class="data-tbl frm-tbl">
				<tbody>
				<tr style="display:none;">
					<td>商品ID</td>
					<td><input class="long-txt" type="text" name="product_id"
					           value="<?php echo $productInfo['id']; ?>"/></td>
					<td></td>
				</tr>
				<tr>
					<td>商品编号</td>
					<td><input class="long-txt" type="text" name="product_model_no"
					           value="<?php echo $productInfo['product_model_no']; ?>"/><br>
						（规则：SMP-BTS-A10，说明：SMP-Smile Deer Product,BT-Bluetooth,S-Speaker，即：公司-商品分类-商品型号）
					</td>
					<td></td>
				<tr>
					<td>商品分类</td>
					<td>
						<select name="product_category">
							<?php foreach ($categoryList as $category_key => $category_name): ?>
								<option value="<?php echo $category_key; ?>" <?php if ($productInfo["product_category"] == $category_key): ?> selected="selected" <?php endif; ?> ><?php echo $category_name; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>中文名称</td>
					<td><input class="long-txt" type="text" name="product_chinese_name"
					           value="<?php echo $productInfo['product_chinese_name']; ?>"/></td>
					<td></td>
				</tr>
				<tr>
					<td>英文名称</td>
					<td><input class="long-txt" type="text" name="product_english_name"
					           value="<?php echo $productInfo['product_english_name']; ?>"/></td>
					<td></td>
				</tr>
				<tr>
					<td>单价(Item Price)</td>
					<td>
						<input class="long-txt" type="text" name="product_item_price"
						       value="<?php echo $productInfo['product_item_price']; ?>"/><br>
						（参考：FOB SHENZHEN : USD 6.50 /pcs）
					</td>
					<td></td>
				</tr>
				<tr>
					<td>商品功能(Function)</td>
					<td>
						<textarea class="txt" cols="150" rows="10"
						          name="product_function"><?php echo $productInfo['product_function']; ?></textarea><br>
						（参考：1.heart rate; 2.step count; 3.blood pressure.<span class="tips">每行一个功能</span>）
					</td>
					<td></td>
				</tr>
				<tr>
					<td>主要特性(Main Features)</td>
					<td><textarea class="txt" cols="150" rows="10"
					              name="product_main_features"><?php echo $productInfo['product_main_features']; ?></textarea><br>
						（参考：1.Support DAB and DAB+; 2.support Bluetooth player,Bluetooth handfree.<span class="tips">每行一个特性</span>）
					</td>
					<td></td>
				</tr>
				<tr>
					<td>商品规格(Item specifics)</td>
					<td><textarea class="txt" cols="150" rows="10"
					              name="product_item_specifics"><?php echo $productInfo['product_item_specifics']; ?></textarea><br>
						（参考：1.Sensitivity:40±2dB; 2.Waterproof:Yes.<span class="tips">每行一对规格</span>）
					</td>
					<td></td>
				</tr>
				<tr>
					<td>商品描述(Product Description)</td>
					<td>
						<textarea id="editor_id" name="product_description" class="rich-txt"
						          style="width:900px;height:600px;visibility:hidden;"><?php echo $productInfo['product_description']; ?></textarea>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>装箱信息(Packing information)</td>
					<td>
						<textarea cols="150" rows="10"
						          name="product_packing_information"><?php echo $productInfo['product_packing_information']; ?></textarea><br>
						（参考：80 pcs/ctn. G/W:8kg.Carton size:372*244*282mm.<span class="tips">按行分割</span>）
					</td>
					<td></td>
				</tr>
				<?php
				$productImg = $productInfo['product_img_list'];
				?>
				<?php if ($productImg): ?>
					<?php foreach ($productImg as $img): ?>
						<tr>
							<td>商品图片</td>
							<td>
								<input name="product_img[]" type="hidden" rel="upload-image"
								       data-src="<?php echo $img; ?>" value="<?php echo $img; ?>"/>
							</td>
							<td>
								<span class="small-btn small-delete-btn" rel="row-delete-btn-self"
								      title="Delete">Delete</span>
								<span class="small-btn small-up-btn" rel="row-up-btn" title="Up">Up</span>
								<span class="small-btn small-down-btn" rel="row-down-btn" title="Down">Down</span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<?php echo $TPL; ?>
				<?php endif; ?>
				</tbody>
				<tfoot>
				<tr>
					<td></td>
					<td colspan="20">
						<span class="small-btn small-add-btn" rel="row-append-btn"
						      data-tpl="row-template">add one</span>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>商品参考链接</td>
					<td>
						<input type="text" class="long-txt" name="product_urls"
						       value="<?php echo $productInfo['product_urls']; ?>">(多个用英文逗号,分隔)
					</td>
					<td></td>
				</tr>
				<tr>
					<td>是否显示</td>
					<td>
						<input type="radio" name="is_show"
						       value="1" <?php if ($productInfo['is_show']): ?> checked <?php endif; ?> >是<input
								type="radio" name="is_show"
								value="0" <?php if (!$productInfo['is_show']): ?> checked <?php endif; ?>>否
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
	<script>
		/*
		seajs.use(["jquery", "ywj/msg", 'ywj/net'], function ($, Msg, net) {
			$("input[type='submit']").click(function () {

				var product_id="";
				var product_category = $.trim($("select[name='product_category']").val());
				var product_chinese_name = $.trim($("input[name=product_chinese_name]").val());
				var product_english_name = $.trim($("input[name=product_english_name]").val());
				var product_urls = $.trim($("input[name=product_urls]").val());
				var is_show = $("input[name='radio']:checked").val();
				var product_img_list = [];

				$("input[name='product_img[]']").each(function () {
					var $this = $(this);
					var current_img = $this.attr("value");
					product_img_list.push(current_img);
				});
				var product_img = product_img_list.length > 0 ? product_img_list.join(",") : "";
				var content = window.editor.html();

				var request_param = {
					"id": product_id,
					"product_category": product_category,
					"product_english_name": product_english_name,
					"product_chinese_name": product_chinese_name,
					"product_urls": product_urls,
					"product_information": content,
					"is_show": is_show,
					"product_img": product_img
				};
				debugger;
				net.post("/trade/editProduct", request_param, function (json) {
					Msg.show("success");
				});
			});
			return false;
		});
		*/
	</script>
<?php include $templatePath . "inc/footer.inc.php"; ?>