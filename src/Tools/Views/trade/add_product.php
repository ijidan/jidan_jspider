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
				<tr>
					<td>商品分类</td>
					<td>
						<select name="product_category">
							<?php foreach ($categoryList as $category_key => $category_name): ?>
								<option value="<?php echo $category_key; ?>"><?php echo $category_name; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>中文名称</td>
					<td><input class="txt" type="text" name="product_chinese_name"/></td>
					<td></td>
				</tr>
				<tr>
					<td>英文名称</td>
					<td><input class="txt" type="text" name="product_english_name"/></td>
					<td></td>
				</tr>
				<tr>
					<td>商品链接</td>
					<td>
						<input type="text" class="long-txt" name="product_urls" value="">(多个用英文逗号,分隔)
					</td>
					<td></td>
				</tr>
				<tr>
					<td>商品描述</td>
					<td>
						<textarea id="editor_id" name="product_information"
						          style="width:1000px;height:600px;visibility:hidden;"></textarea>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>是否显示</td>
					<td>
						<input type="radio" name="is_show" value="1" checked>是<input type="radio" name="is_show"
						                                                             value="0">否
					</td>
					<td></td>
				</tr>
				<?php echo $TPL; ?>
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
		seajs.use(["jquery", "ywj/msg", 'ywj/net'], function ($, Msg, net) {
			$("input[type='submit']").click(function () {

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
				debugger;
				var product_img = product_img_list.length > 0 ? product_img_list.join(",") : "";
				var content = window.editor.html();

				var request_param = {
					"product_category": product_category,
					"product_english_name": product_english_name,
					"product_chinese_name": product_chinese_name,
					"product_urls": product_urls,
					"product_information": content,
					"is_show": is_show,
					"product_img": product_img
				};
				debugger;
				net.post("/trade/addProduct", request_param, function (json) {
					Msg.show("success");
				});
			});
			return false;
		});
	</script>
<?php include $templatePath . "inc/footer.inc.php"; ?>