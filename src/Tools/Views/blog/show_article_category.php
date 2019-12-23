<?php
$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>


<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>ID</th>
			<th>Key</th>
			<th>Name</th>
			<th>Sort</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($articleCategory as $category): ?>
			<tr>
				<td><?php echo $category["id"]; ?></td>
				<td><?php echo $category["category_key"] ?></td>
				<td><?php echo $category["name"] ?></td>
				<td><?php echo $category["sort"] ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
