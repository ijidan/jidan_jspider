<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form action="" class="search-frm well">
		<div class="frm-item">
			<label>常用网站列表（点击链接直接打开）</label>
		</div>
	</form>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>网站名称</th>
			<th>网站链接</th>
			<th>说明</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($websiteList as $website): ?>
			<tr>
				<td <?php if ($website["key_point"]): ?> style="color:red;" <?php endif; ?>><?php echo $website["name"]; ?></td>
				<td>
					<?php if ($website["readonly"]): ?>
						<?php echo $website["url"]; ?>
					<?php else: ?>
						<a href="http://<?php echo $website["url"]; ?>"
						   target="_blank" <?php if ($website["readonly"]): ?> readOnly="readOnly" <?php endif; ?> <?php if ($website["key_point"]): ?> style="color:red;text-decoration:underline;" <?php else: ?> style="text-decoration:underline;" <?php endif; ?> >
							http://<?php echo $website["url"]; ?>
						</a>
					<?php endif; ?>
					<?php if($website["cross_the_wall"]):?>
						<span style="margin-left:40px;color:red;font-weight:700;">（需要翻墙）</span>
					<?php endif;?>
				</td>
				<td><?php echo $goods["note"]; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
