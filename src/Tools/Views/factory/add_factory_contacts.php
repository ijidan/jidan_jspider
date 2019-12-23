<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>
<?php
$TPL = <<<EOT
<tr>
	<td>工厂图片</td>
	<td>
		<input name="factory_img[]" type="hidden" rel="upload-image" data-src="" value=""/>
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
			<caption>添加工厂</caption>
			<table class="data-tbl frm-tbl">
				<tbody>
				<tr>
					<td>工厂名称</td>
					<td><input class="txt" type="text" name="factory_name"/></td>
					<td></td>
				</tr>
				<tr>
					<td>主营产品</td>
					<td>
						<?php foreach ($factoryProductCategoryList as $category_key => $category_name): ?>
							<label><input type="checkbox" name="factory_product_category"
							              value="<?php echo $category_key; ?>">
								<?php echo $category_name; ?></label>
						<?php endforeach; ?>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>证书情况</td>
					<td>
						<?php foreach ($factoryCertList as $cert_key => $cert_name): ?>
							<label><input type="checkbox" name="product_certificate"
							              value="<?php echo $cert_key; ?>"><?php echo $cert_name; ?></label>
						<?php endforeach; ?>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>加工方式</td>
					<td><input class="txt" type="text" name="factory_process_method"/>（参考:来图加工,来样加工,ODM加工,OEM加工）多个英文逗号分隔
					</td>
					<td></td>
				</tr>
				<tr>
					<td>工艺类型</td>
					<td><input class="txt" type="text" name="factory_process_type"/>（参考:注塑/注射,吸塑成型,吹塑成型,电子组装加工,注塑加工）多个英文逗号分隔
					</td>
					<td></td>
				</tr>
				<tr>
					<td>工厂自有品牌</td>
					<td><input class="txt" type="text" name="factory_brand"/>（参考:品牌A,品牌B,品牌C）多个英文逗号分隔</td>
					<td></td>
				</tr>

				<tr>
					<td>工厂网站</td>
					<td><input class="txt" type="text" name="factory_website"/>（参考:www.a.com,www.b.com）多个英文逗号分隔</td>
					<td></td>
				</tr>

				<tr>
					<td>工厂地址</td>
					<td><input class="txt" type="text" name="factory_address"/>（参考:广东省深圳市宝安区福永街道X号,广东省深圳市龙岗区XX街道X号）多个英文逗号分隔
					</td>
					<td></td>
				</tr>
				<tr>
					<td>交通说明</td>
					<td><input class="txt" type="text" name="factory_traffic"/>（参考:离地铁较远，公交直达）</td>
					<td></td>
				</tr>

				<tr>
					<td>是否工厂</td>
					<td><input type="radio" name="is_real_factory" value="1" checked>是<input type="radio"
					                                                                         name="is_real_factory"
					                                                                         value="0">否
					</td>
					<td></td>
				</tr>
				<tr>
					<td>厂房说明</td>
					<td><input class="txt" type="text" name="factory_situation"/>（参考:上下两层,工厂在一楼，办公室在六楼，干净简洁）</td>
					<td></td>
				</tr>

				<tr>
					<td>员工数量</td>
					<td>
						<select name="factory_staff_num">
							<?php foreach ($factoryStaffNumRange as $range_key => $range_value): ?>
								<option value="<?php echo $range_key; ?>"><?php echo $range_value; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>工厂态度</td>
					<td colspan="2">
						<input class="txt" type="text" name="product_attitude"/>
						（参考用词:<?php foreach ($factoryAttitudeList as $att_key => $att_value): ?>
							<?php echo $att_value; ?>,
						<?php endforeach; ?>）
					</td>
				</tr>
				<tr>
					<td>联系人员1</td>
					<td>
						<table>
							<tr>
								<td>姓名：</td>
								<td><input class="txt" type="text" name="contacts1_name"/></td>
								<td>职位：</td>
								<td><input class="txt" type="text" name="contacts1_post"/></td>
								<td>手机：</td>
								<td><input class="txt" type="text" name="contacts1_mobile"/></td>
							</tr>

							<tr>
								<td>微信：</td>
								<td><input class="txt" type="text" name="contacts1_wechat"/></td>
								<td>QQ：</td>
								<td><input class="txt" type="text" name="contacts1_qq"/></td>
								<td>邮箱：</td>
								<td><input class="txt" type="text" name="contacts1_email"/></td>
							</tr>
							<tr>
								<td> skype：</td>
								<td><input class="txt" type="text" name="contacts1_skype"/></td>
								<td>whatsapp：</td>
								<td><input class="txt" type="text" name="contacts1_whatsapp"/></td>
								<td> Twitter：</td>
								<td><input class="txt" type="text" name="contacts1_twitter"/></td>
							</tr>
							<tr>
								<td> Facebook：</td>
								<td><input class="txt" type="text" name="contacts1_facebook"/></td>
								<td>instagram：</td>
								<td><input class="txt" type="text" name="contacts1_instagram"/></td>
							</tr>
						</table>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>联系人员2</td>
					<td colspan="2">
						<table>
							<tr>
								<td>姓名：</td>
								<td><input class="txt" type="text" name="contacts2_name"/></td>
								<td>职位：</td>
								<td><input class="txt" type="text" name="contacts2_post"/></td>
								<td>手机：</td>
								<td><input class="txt" type="text" name="contacts2_mobile"/></td>
							</tr>

							<tr>
								<td>微信：</td>
								<td><input class="txt" type="text" name="contacts2_wechat"/></td>
								<td>QQ：</td>
								<td><input class="txt" type="text" name="contacts2_qq"/></td>
								<td>邮箱：</td>
								<td><input class="txt" type="text" name="contacts2_email"/></td>
							</tr>
							<tr>
								<td> skype：</td>
								<td><input class="txt" type="text" name="contacts2_skype"/></td>
								<td>whatsapp：</td>
								<td><input class="txt" type="text" name="contacts2_whatsapp"/></td>
								<td> Twitter：</td>
								<td><input class="txt" type="text" name="contacts2_twitter"/></td>
							</tr>
							<tr>
								<td> Facebook：</td>
								<td><input class="txt" type="text" name="contacts2_facebook"/></td>
								<td>instagram：</td>
								<td><input class="txt" type="text" name="contacts2_instagram"/></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>综合评价</td>
					<td>
						<textarea name="factory_comment" rows="10" cols="130"></textarea>（参考:该工厂整体看还可以，特别是周边环境较好）
					</td>
					<td></td>
				</tr>
				<tr>
					<td>工厂评分</td>
					<td>
						<input name="factory_grade" type="text">分(说明:1-100之间)
					</td>
					<td></td>
				</tr>
				<tr>
					<td>是否推荐</td>
					<td><input type="radio" name="is_recommend" value="1" checked>是<input type="radio"
					                                                                      name="is_recommend" value="0">否
					</td>
					<td></td>
				</tr>
				<tr>
					<td>推荐/不推荐理由</td>
					<td><input class="txt" type="text" name="recommend_reason"/>（参考:态度奇奇怪怪，看不准）</td>
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