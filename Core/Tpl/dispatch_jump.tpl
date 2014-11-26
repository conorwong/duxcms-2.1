<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>跳转提示</title>
<style>
html,body { margin: 0; padding: 0; background-color: #fff; font-family: Microsoft YaHei,Verdana, Geneva, sans-serif; color:#333; }
.msg { width:600px; padding:20px; margin:auto; position:absolute; left:50%; top:50%; margin-top:-130px; margin-left:-320px; background-color:#eee; font-size:14px;}
.msg h2 { font-size:18px;}
a { color:#333;}
</style>
</head>
<body>
	<div class="msg">
    	<h2>页面正在跳转中...</h2>
        <p>
        <present name="message">
        {$message}
        <else/>
        {$error}
        </present>
         <b id="wait">{$waitSecond}</b> 秒后为您<a id="href" href="<?php echo($jumpUrl); ?>">跳转</a>
        </p>
    </div>
</body>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</html>
