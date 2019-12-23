<?php use Tools\Models\AdminRole;

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>角色ID</th>
			<th>角色名称</th>
			<th>操作</th>
		</tr>
		</thead>
		<tbody>

		<?php foreach ($roleList as $role): ?>
			<?php $roleId = $role["id"]; ?>
			<tr>
				<td><?php echo $role["role_id"]; ?></td>
				<td><?php echo $role["role_name"] ?></td>
				<td>
					<a href="/admin/editRole?id=<?php echo $roleId; ?>" class="btn-link">Edit</a>
					<?php if ($role["role_id"] !== AdminRole::ROLE_ID_ADMIN): ?>
						|<a href="/admin/editRoleAccess?id=<?php echo $roleId; ?>" class="btn-link">Access Rights</a>
						|<a href="/admin/deleteRole?id=<?php echo $roleId; ?>" class="btn-link">Delete</a>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
