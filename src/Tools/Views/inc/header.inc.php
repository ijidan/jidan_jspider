<?php

use Tools\Controllers\IndexController;

$inIFrame = $_GET['ref'] == 'iframe';
?>
<!DOCTYPE html>
<!--<html class="--><?php //echo $in_iframe ? 'page-iframe' : '';?><!-- SERVER-IDENTIFY---><?php //echo Server::getEnvConfig('ENV');?><!--">-->
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>smileDeer Manager</title>
	<link rel="stylesheet" href="../ywj/ui/backend/default.css"/>
	<link rel="stylesheet" href="../css/jquery.jqzoom.css"/>
	<link rel="stylesheet" href="/ChatJs/css/jquery.chatjs.css"/>
	<script type="text/javascript" src="/seajs/sea.js"></script>
	<script type="text/javascript" src="/seajs/config.js"></script>
	<script type="text/javascript">
		window.UPLOAD_URL='/common/uploadImg';
		seajs.use('ywj/auto');
	</script>
	<?php if($inIFrame):?>
		<script type="text/javascript">
			seajs.use(["jquery","ywj/msg"],function($,Msg){
				$(document).ready(function(){
					$("#header").hide();
					$("#col-aside").hide();
				});
			});
		</script>
	<?php endif;?>
</head>
<body>
<div id="page">
	<div id="header">
		<h1 id="logo">
			<a href="/">Manager</a>
		</h1>
		<div id="welcome">
			<?php $loginUser = IndexController::getLoginUser(); ?>
			<?php if ($loginUser["account"]): ?>
				Welcome, <b><?php echo $loginUser['account']; ?></b> &nbsp;
				<a href="/index/logout">Logout</a>
			<?php else: ?>
				Hi,Please <a href="/index/login">Login</a>
			<?php endif; ?>
		</div>
		<ul id="main-nav">
			<?php
			foreach ([] as $k => $item) {
				?>
				<li <?php echo $item[2] ? 'class="active"' : ''; ?>>
					<a href="<?php echo $this->getUrl($item[1]); ?>"><?php echo $item[0]; ?></a>

					<?php if (!empty($item[3])): ?>
						<dl class="sub-nav">
							<?php foreach ($item[3] as $cap => $sub_nav_list): ?>

								<?php if (count($item[3]) > 1): ?><?php //单个子菜单不显示title?>
									<dt><span><?php echo $cap; ?></span></dt>
								<?php endif; ?>

								<?php foreach ($sub_nav_list as $sub_item): ?>
									<dd>
										<a href="<?php echo $this->getUrl($sub_item[1]); ?>"><?php echo $sub_item[0]; ?></a>
									</dd>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
				</li>
			<?php } ?>
		</ul>
	</div>
	<div id="container">
