<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>
<div id="col-aside" >
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form action="" class="search-frm well">
		<div class="frm-item">
			<label>
				Order No.：
				<input type="text" class="txt" name="order_no" value="<?php echo $search['order_no'];?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Order Status：
				<input type="text" class="txt" name="order_status" value="<?php echo $search['order_status'];?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Create Time：
				<input type="text" name="start_time" value="<?php echo date('Y-m-d H:i:s', $search['start_time'])?>"
				       class="txt date-time-txt"/> 至
				<input type="text" name="end_time" value="<?php echo date('Y-m-d H:i:s', $search['end_time'])?>"
				       class="txt date-time-txt"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Receiver Name：
				<input type="text" class="txt" name="receiver_name" value="<?php echo $search['receiver_name'];?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				Receiver Mobile：
				<input type="text" class="txt" name="receiver_mobile"
				       value="<?php echo $search['receiver_mobile']; ?>"/>
			</label>
		</div>
		<div class="frm-item">
			<label>
				User Mobile：
				<input type="text" class="txt" name="user_mobile" value="<?php echo $search['user_mobile'];?>"/>
			</label>
		</div>
		<div class="frm-item">
			<input type="submit" value="Search" class="btn"/>
			<a href="/order/showList">Reset</a>
		</div>
	</form>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>Order Info</th>
			<th>Receiver Info</th>
			<th>User Info</th>
			<th>Operation</th>
		</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
