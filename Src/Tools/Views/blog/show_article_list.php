<?php use Tools\Models\Article;

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>


<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<div class="operate-bar">
		<a href="/blog/addArticle" class="btn" rel="popup" data-width="1200">new article</a>
	</div>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Category</th>
			<th>Operation</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($articleList as $article): ?>
			<?php ?>
			<tr>
				<td><?php echo $article["id"]; ?></td>
				<td><?php echo $article["title"] ?></td>
				<td>
					<?php if($article["category_id"]):?>
						<?php echo $categoryListById[$article["category_id"]]["name"]?:$categoryListById[$article["category_id"]]["category_key"];?>
					<?php endif;?>
				</td>
				<td>
					<a target="_blank" href="/blog/editArticle?id=<?php echo $article["id"]; ?>" class="btn-link">Edit</a>|
					<?php if($article['visibility']==Article::VISIBILITY_YES):?>
						<a rel="async" href="/blog/toggleArticle?id=<?php echo $article["id"]; ?>&visibility=<?php echo Article::VISIBILITY_NO;?>" class="btn-link">delete</a>
					<?php else:?>
						<a rel="async" href="/blog/toggleArticle?id=<?php echo $article["id"]; ?>&visibility=<?php echo Article::VISIBILITY_YES;?>" class="btn-link">publish</a>
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
