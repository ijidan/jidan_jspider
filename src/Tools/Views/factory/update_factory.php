<?php use Tools\Models\Factory;

$templatePath = $this->getTemplatePath(); ?>
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
		<form class="frm" rel="async" method="post">
			<caption>添加工厂</caption>
			<table class="data-tbl frm-tbl">
				<tbody>
				<tr>
					<td>工厂ID</td>
					<td><input class="txt" type="text" name="factory_id" value="<?php echo $factory_id ?>" readonly
					           style="background-color: #dddddd; "/></td>
					<td></td>
				</tr>
				<tr>
					<td>工厂名称</td>
					<td><input class="txt" type="text" name="factory_name"
					           value="<?php echo $factory_info['factory_name']; ?>"/></td>
					<td></td>
				</tr>
				<tr>
					<td>主营产品</td>
					<td>
						<?php $factory_product_category_array = $factory_info['factory_product_category_array']; ?>
						<?php foreach ($factoryProductCategoryList as $category_key => $category_name): ?>
							<label>
								<input type="checkbox" name="factory_product_category[]"
								       value="<?php echo $category_key; ?>" <?php if (in_array($category_key, $factory_product_category_array)): ?> checked <?php endif; ?>>
								<?php echo $category_name; ?>
							</label>
						<?php endforeach; ?>
					</td>
					<td></td>
				</tr>
				<tr>
					<?php $product_certificate_array = $factory_info['product_certificate_array']; ?>
					<td>证书情况</td>
					<td>
						<?php foreach ($factoryCertList as $cert_key => $cert_name): ?>
							<label><input type="checkbox" name="product_certificate[]"
							              value="<?php echo $cert_key; ?>" <?php if (in_array($cert_key, $product_certificate_array)): ?> checked <?php endif; ?>>
								<?php echo $cert_name; ?>
							</label>
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
					<td><input class="txt" type="text" name="factory_brand"
					           value="<?php echo $factory_info['factory_brand']; ?>"/>（参考:品牌A,品牌B,品牌C）多个英文逗号分隔
					</td>
					<td></td>
				</tr>

				<tr>
					<td>工厂网站</td>
					<td><input class="txt" type="text" name="factory_website"
					           value="<?php echo $factory_info['factory_website']; ?>"/>（参考:www.a.com,www.b.com）多个英文逗号分隔
					</td>
					<td></td>
				</tr>

				<tr>
					<td>工厂地址</td>
					<td><input class="txt" type="text" name="factory_address"
					           value="<?php echo $factory_info['factory_address']; ?>"/>（参考:广东省深圳市宝安区福永街道X号,广东省深圳市龙岗区XX街道X号）多个英文逗号分隔
					</td>
					<td></td>
				</tr>
				<tr>
					<td>交通说明</td>
					<td><input class="txt" type="text" name="factory_traffic"
					           value="<?php echo $factory_info['factory_traffic']; ?>"/>（参考:离地铁较远，公交直达）
					</td>
					<td></td>
				</tr>
				<tr>
					<?php $is_real_factory = $factory_info['is_real_factory']; ?>
					<td>是否工厂</td>
					<td>
						<?php foreach (Factory::$IS_REAL_FACTORY_ID_DESC_MAP as $id => $name): ?>
							<input type="radio" name="is_real_factory"
							       value="<?php echo $id; ?>" <?php if ($is_real_factory == $id): ?> checked="checked" <?php endif; ?> ><?php echo $name; ?>
						<?php endforeach; ?>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>厂房说明</td>
					<td><input class="txt" type="text" name="factory_situation"
					           value="<?php echo $factory_info['factory_situation']; ?>"/>（参考:上下两层,工厂在一楼，办公室在六楼，干净简洁）
					</td>
					<td></td>
				</tr>

				<tr>
					<td>员工数量</td>
					<td>
						<?php $factory_staff_num = $factory_info['factory_staff_num']; ?>
						<select name="factory_staff_num">
							<?php foreach ($factoryStaffNumRange as $range_key => $range_value): ?>
								<option value="<?php echo $range_key; ?> <?php if($range_key==$factory_staff_num):?> selected="selected" <?php endif; ?> "><?php echo $range_value; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>工厂态度</td>
					<td colspan="2">
						<input class="txt" type="text" name="factory_attitude"
						       value="<?php echo $factory_info['factory_attitude']; ?>"/>
						（参考用词:<?php foreach ($factoryAttitudeList as $att_key => $att_value): ?>
							<?php echo $att_value; ?>,
						<?php endforeach; ?>）
					</td>
				</tr>
				<tr>
					<td>工厂评分</td>
					<td>
						<input name="factory_grade" type="number" class="txt"
						       value="<?php echo $factory_info['factory_grade']; ?>" min="1" max="100">分(说明:1-100之间)
					</td>
					<td></td>
				</tr>
				<tr>
					<?php $is_recommend = $factory_info['is_recommend']; ?>
					<td>是否推荐</td>
					<td>
						<?php foreach (Factory::$IS_RECOMMEND_ID_DESC_MAP as $id => $name): ?>
							<input type="radio" name="is_recommend" value="<?php echo $id;?>" <?php if ($is_recommend == $id): ?> checked="checked" <?php endif; ?> ><?php echo $name;?>
						<?php endforeach;?>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>推荐/不推荐理由</td>
					<td><input class="txt" type="text" name="recommend_reason"
					           value="<?php echo $factory_info['recommend_reason']; ?>"/>（参考:态度奇奇怪怪，看不准）
					</td>
					<td></td>
				</tr>
				<tr>
					<td>综合评价</td>
					<td colspan="2">
						<textarea name="factory_comment" rows="10"
						          cols="130"><?php echo $factory_info['factory_comment']; ?></textarea>（参考:该工厂整体看还可以，特别是周边环境较好）
					</td>
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
				<tr>
					<?php $is_show = $factory_info['is_show']; ?>
					<td>是否显示</td>
					<td>
						<input type="radio" name="is_show"
						       value="1" <?php if ($is_show == 1): ?> checked="checked" <?php endif; ?> >是
						<input type="radio" name="is_show"
						       value="0" <?php if ($is_show == 0): ?> checked="checked" <?php endif; ?> >否
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