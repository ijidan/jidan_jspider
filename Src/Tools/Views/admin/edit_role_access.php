<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form class="frm" rel="async" method="post">
		<table class="data-tbl" data-empty-fill="1">
			<thead>
			<tr>
				<th>父菜单</th>
				<th>子菜单[访问权限]</th>
			</tr>
			</thead>
			<tbody>
			<?php use Tools\Models\Menu;

			foreach ($menus as $title => $menu): ?>
				<tr>
					<td><?php echo $title; ?></td>
					<td>
						<?php foreach ($menu as $item): ?>
							<input type="checkbox" name="role_access[]"
							<?php if (in_array(Menu::cleanUrl($item["url"]), $roleAccess)): ?> checked="checked"<?php endif; ?>
							       value="<?php echo Menu::cleanUrl($item["url"]) ?>"><?php echo $item["name"] ?>
							----( <?php echo $item["url"] ?> )</br>
						<?php endforeach; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td></td>
				<td>
					<button type="submit" class="btn btn-primary">Submit</button>
				</td>
			</tr>
			</tbody>
		</table>
	</form>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>

