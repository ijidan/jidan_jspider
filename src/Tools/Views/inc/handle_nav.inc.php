<?php
use LITE\Router;

$ctrl = Router::getController();
$act = Router::getAction();
$tmp = strtolower($ctrl . '/' . $act);
$navs = array();
$navs[$tmp] = 'active';
?>
<ul class="frm-tab" id="project-nav-tab">
	<li class="<?php echo $navs['task/handle']; ?>">
		<a href="<?php echo $this->getUrl('task/handle', array(
			"project_id" => $project_id,
			'task_id'    => $task_id
		)); ?>">任务处理</a>
	</li>
	<li class="<?php echo $navs['task/businessTrack']; ?>">
		<a href="<?php echo $this->getUrl('task/businessTrack', array(
			'project_id' => $project_id,
			"task_id"    => $task_id
		)); ?>">通话记录</a>
	</li>
	<li class="<?php echo $navs['task/designerTrack']; ?>">
		<a href="<?php echo $this->getUrl('task/designerTrack', array(
			'project_id' => $project_id,
			"task_id"    => $task_id
		)); ?>">设计跟踪</a>
	</li>
</ul>

<style>
	#project-nav-tab { margin-bottom:5px; }
</style>