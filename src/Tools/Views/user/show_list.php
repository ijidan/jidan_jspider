<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<div class="operate-bar">
		<a href="/user/add" class="btn" rel="popup" data-width="1000">add user</a>
	</div>
	<form action="" class="search-frm well">
		<div class="frm-item">
			<label>
				Mobile：
				<input type="text" class="txt" name="mobile" value="<?php echo $search['mobile']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<input type="submit" value="Search" class="btn"/>
			<a href="/user/showList">Reset</a>
		</div>
	</form>
	<?php if (count($userList) == 0): ?>
		<div class="operate-bar">
			<a href="/user/add" target="_blank" class="btn" data-width="700">Add</a>
		</div>
	<?php endif; ?>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Mobile</th>
			<th>Operation</th>
		</tr>
		</thead>
		<tbody>

		<?php foreach ($userList as $user): ?>
			<tr>
				<td><?php echo $user["id"]; ?></td>
				<td><?php echo $user["username"] ?></td>
				<td><?php echo $user["mobile"] ?></td>
				<td>
					<a target="_blank" href="/order/showList?user_mobile=<?php echo $user["username"]; ?>"
					   class="btn-link">orders</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
