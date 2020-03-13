<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form action="" class="search-frm well">
		<div class="frm-item">
			<label>
				Mobile：
				<input type="text" class="txt" name="mobile" value="<?php echo $search['mobile']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<input type="submit" value="Search" class="btn"/>
			<a href="/address/showList">Reset</a>
		</div>
	</form>
	<?php if (count($addressList) == 0): ?>
		<div class="operate-bar">
			<a href="/user/addAddress" class="btn" rel="popup" data-width="1000">Add</a>
		</div>
	<?php endif; ?>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Mobile</th>
			<th>Address</th>
			<th>Operation</th>
		</tr>
		</thead>
		<tbody>

		<?php foreach ($addressList as $address): ?>
			<tr>
				<td><?php echo $address["id"]; ?></td>
				<td><?php echo $address["accept_name"] ?></td>
				<td><?php echo $address["telphone"] ?></td>
				<td><?php echo $address["address"] ?></td>
				<td>
					<a rel="popup" data-width="1000" href="/productOrder/addProduct" class="btn-link">Add Product To Buy</a>|
					<a target="_blank" href="/order/showList?user_mobile=<?php echo $address["telphone"]; ?>"
					   class="btn-link">Orders</a>|
					<a rel="popup" data-width="1000" href="/user/editAddress?id=<?php echo $address["id"]; ?>"
					   class="btn-link">Edit</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
