<?php use Tools\Models\Article;

$templatePath = $this->getTemplatePath(); ?>
<?php include $templatePath . "inc/header.inc.php"; ?>


<div id="col-aside">
	<?php include $templatePath . "inc/asidenav.inc.php"; ?>
</div>
<div id="col-main">
	<div class="operate-bar">
		<a href="/blog/addArticle" class="btn" rel="popup" data-width="1200">new article</a>
	</div>
	<table class="data-tbl" data-empty-fill="1">
		<thead>
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Operation</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($articleList as $article): ?>
			<tr>
				<td><?php echo $article["id"]; ?></td>
				<td><?php echo $article["title"] ?></td>
				<td>
					<a target="_blank" href="/blog/editArticle?id=<?php echo $article["id"]; ?>"
					   class="btn-link">Edit</a>|
					<?php if ($article['visibility'] == Article::VISIBILITY_YES): ?>
						<a rel="async"
						   href="/blog/toggleArticle?id=<?php echo $article["id"]; ?>&visibility=<?php echo Article::VISIBILITY_NO; ?>"
						   class="btn-link">delete</a>
					<?php else: ?>
						<a rel="async"
						   href="/blog/toggleArticle?id=<?php echo $article["id"]; ?>&visibility=<?php echo Article::VISIBILITY_YES; ?>"
						   class="btn-link">publish</a>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $paginate; ?>
	<div class="chat-window" style="right: 10px;">
		<div class="chat-window-title">
			<div class="text">HỖ TRỢ</div>
		</div>
		<div class="chat-window-content" style="height: 300px;">
			<div class="chat-window-inner-content user-list" id="user-list-container" style="height: 100px;">

			</div>
		</div>
	</div>
	<div class="chat-window" id="chat-content-container" style="right: 247px;display:none;">
		<div class="chat-window-title">
			<div class="close" id="close-chat-content-container"></div>
			<div class="text" id="chat-service-name">Echobot</div>
		</div>
		<div class="chat-window-content" style="display:block;">
			<div class="chat-window-inner-content message-board pm-window" style="height: 235px;">
				<div class="messages-wrapper" style="height: 214px;"></div>
				<div class="chat-window-text-box-wrapper">
					<textarea rows="1" id="message-container" class="chat-window-text-box"
					          style="overflow: hidden; word-wrap: break-word; resize: none; height: 21px;"></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="/js/reconnecting-websocket.js"></script>
<script type="text/javascript">

	var socket;
	var clientId = "";
	var serviceList = [];
	// 打开聊天窗口
	function startChat(ele) {
		var $this = $(ele);
		$this.css({"-webkit-animation": ""});
		var currServiceId = $this.data("id");
		var currService = getServiceById(currServiceId);
		$("#chat-service-name").html(currService.name);
		renderMessage(currServiceId);
	}

	//拉取客户信息
	function getServiceById(serviceId) {
		var service = {};
		for (var i = 0; i < serviceList.length; i++) {
			var currService = serviceList[i];
			if(currService.id == serviceId){
				service = currService;
				break;
			}
		}
		return service;
	}

	//消息渲染
	function renderMessage(serviceId) {
		var messageStr = getMessage(serviceId);
		var $wrapper = $("#chat-content-container .messages-wrapper");
		$wrapper.html(messageStr);
		$wrapper.scrollTop($wrapper[0].scrollHeight);
		$("#chat-content-container").show();
	}

	//获取消息
	function getMessage(serviceId) {
		var messageKey = getMessageKey(serviceId);
		return sessionStorage.getItem(messageKey);
	}

	/**
	 * 添加信息
	 * @param serviceId
	 * @param message
	 * @param direct
	 * @returns null
	 */
	function pushMessage(serviceId, message, direct) {
		var messageKey = getMessageKey(serviceId);
		var messageStr = getMessage(serviceId);
		if(!messageStr){
			messageStr = "";
		}
		var service = getServiceById(serviceId);
		var img = direct == 1 ? "http://www.gravatar.com/avatar/4ec6b20c5fed48b6b01e88161c0a3e20.jpg" : service.avatar;
		messageStr = messageStr + '<div class="chat-message" data-direct=' + direct + '>' +
			'<div class="chat-gravatar-wrapper">' +
			'<img class="profile-picture" src="' + img + '">' +
			'</div><div class="chat-text-wrapper"><p>' + message + ' </p></div></div>';
		sessionStorage.setItem(messageKey, messageStr);
		return null;
	}

	/**
	 * 获取key
	 * @param serviceId
	 * @returns {string}
	 */
	function getMessageKey(serviceId) {
		return "message_" + serviceId;
	}

	seajs.use(["jquery", "ywj/msg", "jquery-lazyload"], function ($, Msg) {


		/**
		 * 初始化
		 * @param serviceArr
		 * @returns null
		 */
		function initService(serviceArr) {
			var content = "";
			if(serviceArr){
				for (var i = 0; i < serviceArr.length; i++) {
					var service = serviceArr[i];
					content += '<div class="user-list-item" onclick="startChat(this)" data-id=' + service.id + ' id="service_' + service.id + '"> ' +
						'<img class="profile-picture" src="' + service.avatar + '"> ' +
						'<div class="profile-status online"></div> ' +
						'<div class="content">' + service.name + '</div> ' +
						'</div>';
				}
			}
			$("#user-list-container").html(content);
			return null;
		}


		//关闭窗口
		$("#close-chat-content-container").click(function () {
			$("#chat-content-container").hide();
		});
		//初始化SOCKET
		function initSocket(option) {
			//服务器地址
			var locate = window.location;
			var url = option.url ? option.url : "ws://" + locate.hostname + ":" + locate.port + _get_basepath() + "/websocket";
			//回调函数
			var callback = option.callback;
			if(typeof callback !== "function"){
				console.log('callback 必须为函数');
				return false;
			}
			//一些对浏览器的兼容已经在插件里面完成
			var websocket = new ReconnectingWebSocket(url);
			//var websocket = new WebSocket(url);

			//连接发生错误的回调方法
			websocket.onerror = function () {
				console.log("websocket.error");
			};

			//连接成功建立的回调方法
			websocket.onopen = function (message) {
				console.log("onopen");
			}

			//接收到消息的回调方法
			websocket.onmessage = function (message) {
				console.info(message);
				var messageData = JSON.parse(message.data);
				var eventId = parseInt(messageData.event_id);
				var eventData = messageData.event_data;
				switch (eventId) {
					case 901:
						clientId = eventData.client_id;
						var data = {
							"event_id": 101,
							"event_data": []
						};
						websocket.send(JSON.stringify(data));
						break;
					case 902:
						serviceList = eventData.service_list;
						initService(serviceList);
						break;
					case 903:
						debugger;
						var currServiceId = eventData["service_id"];
						var currMessage = eventData["message"];
						var $userListItem = $("#service_" + currServiceId);
						//判断是否隐藏，演示
						var _display = $('#chat-content-container').css('display');
						pushMessage(currServiceId, currMessage, 2);
						if(_display == "none"){
							$userListItem.css({"-webkit-animation": "twinkling 1s infinite ease-in-out"});
						} else {
							renderMessage(currServiceId);
						}


						break;
				}
				console.info(event.data);
			}
			//连接关闭的回调方法
			websocket.onclose = function () {
				console.log("websocket.onclose");
				websocket.close();
			}

			//监听窗口关闭事件，当窗口关闭时，主动去关闭websocket连接，防止连接还没断开就关闭窗口，server端会抛异常。
			window.onbeforeunload = function () {
				websocket.close();
			}
			return websocket;
		}

		//点击进行聊天
		$(function () {
			if(("WebSocket" in window)){
				var option = {
					url: "ws://192.168.33.10:8282",
					callback: function (data) {
					}
				};
				socket = initSocket(option);
			}
			//具体发送消息的过程
			$("#message-container").keydown(function () {
				var $this = $(this);
				var _keyCode = event.keyCode;
				var _serviceId = 2;
				var _message = $this.val();
				console.info(_keyCode);
				if(_keyCode == 13 && socket){
					var data = {
						"event_id": 102,
						"event_data": {
							"service_id": _serviceId,
							"message": _message
						}
					};
					socket.send(JSON.stringify(data));
					pushMessage(_serviceId, _message, 1);
					renderMessage(_serviceId);
					$this.val("");
					return false;
				}
				return true;
			});
		});
	});

</script>

<?php include $templatePath . "inc/footer.inc.php"; ?>
