<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">每日日志信息汇总</h1>
	</div>
</div>

<form method="post" class="form-inline" action="/log/appDailyLogs">
	<div class="row">
		<div class="form-group">
			<label for="date">Date：</label>
			<input type="text" name="date" id="date" value="<?php echo $date; ?>">
		</div>
		<button type="submit" class="btn btn-default">查询</button>
	</div>
</form>

<div class="row">
	<table class="table table-bordered table-hover table-striped" style="word-break:break-all; word-wrap:break-all">
		<tr>
			<td>message</td>
			<td>file</td>
			<td>line</td>
			<td>count</td>
		</tr>
		<?php foreach ($list as $item) { ?>
			<tr>
				<td><?php echo $item['msg']; ?></td>
				<td><?php echo $item['file']; ?></td>
				<td><?php echo $item['line']; ?></td>
				<td><?php echo $item['count']; ?></td>
			</tr>
		<?php } ?>
	</table>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		$("#date").datepicker({
			dateFormat: "yy-mm-dd",
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true
		});
	});
</script>