<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Tools日志信息</h1>
	</div>
</div>

<div class="row">
	<table class="table table-bordered table-hover table-striped" style="word-break:break-all; word-wrap:break-all">
		<tr>
			<td>date (UTC)</td>
			<td>user</td>
			<td>level</td>
			<td width="70%">message</td>
			<td></td>
		</tr>
		<?php foreach ($list as $item) { ?>
			<tr>
				<td><?php echo $item['datetime']; ?></td>
				<td><?php echo $item['context']['user']; ?></td>
				<td><?php echo $item['level_name']; ?></td>
				<td><?php echo $item['message']; ?></td>
				<td>
					<a href="/log/toolLogDetails?<?php echo "id={$item['_id']}"; ?>" class="btn btn-link">Detail</a>
				</td>
			</tr>
		<?php } ?>
	</table>
	<?php echo $pageInfo; ?>
</div>