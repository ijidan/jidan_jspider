<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<?php echo date(DATE_ISO8601);?></br>
	Hello, <?php echo $account; ?>
</div>
<?php include $templatePath . "inc/footer.inc.php"; ?>
