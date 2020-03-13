<?php $templatePath = $this->getTemplatePath(); ?>
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

	<div id="col-aside">
		<?php include $templatePath . "inc/asidenav.inc.php"; ?>
	</div>
	<div id="col-main">
		<form action="" class="frm" rel="async" method="post">
			<table class="data-tbl frm-row-tbl design_image_tbl">
				<tbody>
				<tr>
					<td>账号</td>
					<td>
						<input type="text" name="account" required readonly style="background:rgb(235,235,228)"
						       value="<?php echo $user['account']; ?>">
					</td>
				</tr>
				<tr>
					<td>密码</td>
					<td><input type="password" name="password" required="required"></td>
				</tr>
				<tr>
					<td>角色</td>
					<td>
						<select name="role_id" class="input-sm">
							<?php foreach ($roleList as $key => $val) : ?>
								<option value="<?php echo $val['id']; ?>" <?php if ($val['id'] == $user['role_id'])
									echo "selected"; ?>><?php echo $val['role_name'] . '(' . $val['role_id'] . ')'; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<?php
				$img = $user['avatar']; ?>
				<?php if($img):?>
					<tr>
						<td>商品图片</td>
						<td>
							<input name="product_img[]" type="hidden" rel="upload-image" data-src="<?php echo $img;?>" value="<?php echo $img;?>"/>
						</td>
						<td>
							<span class="small-btn small-delete-btn" rel="row-delete-btn-self"  title="Delete">Delete</span>
							<span class="small-btn small-up-btn" rel="row-up-btn" title="Up">Up</span>
							<span class="small-btn small-down-btn" rel="row-down-btn" title="Down">Down</span>
						</td>
					</tr>
				<?php else:?>
					<?php echo $TPL; ?>
				<?php endif;?>
				<tr>
					<td></td>
					<td><input type="submit" value="submit" class="btn"></td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>