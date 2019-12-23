<?php use Tools\Models\AdminRole;
use Tools\Models\AdminUser;

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>账号</th>
			<th>角色</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($userList as $user): ?>
			<tr>
				<td><?php echo $user["account"]; ?></td>
				<td><?php echo $roleList[$user["role_id"]]["role_name"]; ?></td>
				<td><?php echo AdminUser::$STATUS_DES_MAP[$user["is_del"]]; ?></td>
				<td>
					<a href="/admin/editOperator?id=<?php echo $user["id"]; ?>" class="btn-link">Edit</a>
					<?php if ($user["role_id"] != AdminRole::ROLE_ID_ADMIN): ?>
						|<a href="/admin/updateOperator?id=<?php echo $user["id"]; ?>" class="btn-link">
							<?php if ($user["is_del"] == AdminUser::STATUS_IS_DEL_YES): ?>
								Active
							<?php else: ?>
								Inactive
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
