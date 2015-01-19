function no(){
	alert('感谢您对DuxCms的支持！');
	window.close();
}
function showmsg(msg,tyle){
	var html = '<p class="'+tyle+'">'+msg+'</p>';
	$('.m-log').append(html);
}
function insok(homeUrl,adminUrl){
	var html = '\
	<a href="'+homeUrl+'" class="submit">返回首页</a>\
	<a href="'+adminUrl+'" class="submit">进入后台</a>\
	';
	$('.m-foot').html(html);
}