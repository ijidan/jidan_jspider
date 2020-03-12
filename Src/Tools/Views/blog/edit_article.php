<?php $templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>
<link rel="stylesheet" href="/kindeditor-4.1.7/themes/default/default.css" />
<link rel="stylesheet" href="/kindeditor-4.1.7/plugins/code/prettify.css" />
<script charset="utf-8" src="/kindeditor-4.1.7/kindeditor.js"></script>
<script charset="utf-8" src="/kindeditor-4.1.7/lang/en.js"></script>
<script charset="utf-8" src="/kindeditor-4.1.7/plugins/code/prettify.js"></script>
<script>
	KindEditor.ready(function(K) {
		window.editor = K.create('#editor_id',{langType : 'en',
			uploadJson : '/common/uploadKindEditor'});
	});
</script>

<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<form action="" class="frm" method="post">
		<table class="data-tbl frm-row-tbl">
			<tbody>
			<tr>
				<td>category</td>
				<td>
					<select name="category_id">
						<?php if(!$article["category_id"]):?>
							<option value="0">--choose later--</option>
						<?php endif;?>
						<?php foreach($categoryList as $category):?>
							<option value="<?php echo $category['id'];?>" <?php if($category['id']==$article["category_id"]):?> selected="selected"<?php endif;?>><?php echo $category["category_key"]."--".$category["name"];?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td>title</td>
				<td>
					<input type="text" class="long-txt" name="title" required value="<?php echo $article['title'];?>">
				</td>
			</tr>
			<tr>
				<td>Content</td>
				<td>
					<textarea id="editor_id" name="content" style="width:1000px;height:600px;visibility:hidden;"><?php echo $article['content'];?></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="submit" class="btn"></td>
			</tr>
			</tbody>
		</table>
	</form>
</div>
	<script>
		seajs.use(["jquery","ywj/msg", 'ywj/net'], function($,Msg,net){
			$("input[type='submit']").click(function(){
				var id=<?php echo $article["id"];?>;
				var category_id=$.trim($("select[name='category_id']").val());
				var title= $.trim($("input[name='title']").val());
				var content=window.editor.html();
				net.post("/blog/editArticle",{id:id,category_id:category_id,title:title,content:content},function(json){
					Msg.show("success");
				});
				return false;
			});
		});
	</script>
<?php include $templatePath . "inc/footer.inc.php"; ?>