<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<div class="operate-bar">
		<a href="/trans/addSearchKeywords" rel="popup" data-width="1000" class="btn" data-width="700">Add</a>
	</div>
	<form action="" class="search-frm well">
		<div class="frm-item">
			<label>
				Vietnamese：
				<input type="text" class="txt" name="vi_vn" value="<?php echo $search['vi_vn']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Chinese(Simplified)：
				<input type="text" class="txt" name="zh_cn" value="<?php echo $search['zh_cn']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<input type="submit" value="Search" class="btn"/>
			<a href="/trans/showSearchKeywordsList">Reset</a>
		</div>
	</form>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>Id</th>
			<th>Vietnamese</th>
			<th>Chinese(Simplified)</th>
			<th>Option</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($keywordsList as $keywords): ?>
			<?php
			$viVn = $keywords["vi_vn"];
			$zhCn = $keywords["zh_cn"];
			$isCompleted = $viVn && $zhCn; ?>
			<tr <?php if (!$isCompleted): ?> style="background-color:deeppink;" <?php endif; ?>>
				<td><?php echo $keywords["id"]; ?></td>
				<td><?php echo $viVn; ?></td>
				<td><?php echo $zhCn; ?></td>
				<td>
					<a rel="popup" data-width="1000" href="/trans/editSearchKeywords?id=<?php echo $keywords["id"]; ?>" target="_blank"
					   class="btn-link">Edit</a>|
					<a href="/trans/deleteSearchKeywords?id=<?php echo $keywords["id"]; ?>" rel="async"
					   data-confirm="Sure to delete this?" class="btn-link">Delete</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
