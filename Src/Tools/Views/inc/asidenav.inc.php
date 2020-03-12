<?php
use Tools\Models\Menu;

$menuModel = new Menu();
$side_nav = $menuModel->getMenus();
?>
<?php foreach ($side_nav as $col_title => $nav_list): ?>
	<dl class="aside-nav">
		<dt><?php echo $col_title; ?></dt>
		<?php foreach ($nav_list as $item): ?>
			<?php $url=$item['url'];?>
			<dd <?php if($url==$currentPath):?>class="active"<?php endif;?> style="margin-left:20px;">
				<a href="<?php echo $url; ?>">
					<?php echo $item['name']; ?>
				</a>
			</dd>
		<?php endforeach; ?>
	</dl>
<?php endforeach; ?>
