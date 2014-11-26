$.ajaxSetup({
    cache: false
});
(function ($) {
	//好友管理
	$.fn.duxFriends = function (options) {
		var defaults = {
            url: rootUrl + 'index.php?m=Member&c=Friends&a=attention',
            delFun: {},
			addFun: {}
        }
        var options = $.extend(defaults, options);
        this.each(function () {
			var obj = this;
			var attention = $(obj).find('.u-attention');
			attention.click(function() {
				var btn = this;
                $.post(options.url,
				{data:$(btn).attr('data')},
				function(json){
					if(json.status == 1){
						if(attention.hasClass('u-btn-primary')){
							$(btn).removeClass('u-btn-primary');
							$(btn).text('取消关注');
							options.addFun(btn,json);
						}else{
							$(btn).addClass('u-btn-primary');
							$(btn).text('加关注');
							options.delFun(btn,json);
						}
						
					}else{
						alert(json.info);
					}
				},'json');
            });
		});
	};
	//点赞
	$.fn.duxPraise = function (options) {
		var defaults = {
            url: rootUrl + 'index.php?m=Member&c=Praise&a=praise',
            delFun: {},
			addFun: {}
        }
        var options = $.extend(defaults, options);
        this.each(function () {
			var obj = this;
			var attention = $(obj).find('.u-praise');
			attention.click(function() {
				var btn = this;
                $.post(options.url,
				{data:$(btn).attr('data')},
				function(json){
					if(json.status == 1){
						if(attention.hasClass('u-btn-primary')){
							$(btn).removeClass('u-btn-primary');
							$(btn).text('已赞');
							options.addFun(btn,json);
						}else{
							$(btn).addClass('u-btn-primary');
							$(btn).text('赞一下');
							options.delFun(btn,json);
						}
						
					}else{
						alert(json.info);
					}
				},'json');
            });
		});
	};
	//私信
	$.fn.duxTextarea = function (options) {
		var defaults = {
        }
        var options = $.extend(defaults, options);
		var tta = this;
		Do.ready('textBox', function() {
			tta.each(function () {
				var obj = this;
				var logCount = $(obj).attr('log');
				$(obj).textbox({
					maxLength: 200,
					onInput: function(event, status) {
						$(logCount).text("还可以输入：" + status. leftLength);
					}
				});
			});
		});
	};
	//HOME
	$.duxHome = function (options) {
		var defaults = {
        }
        var options = $.extend(defaults, options);
		//关注
		$('#user-info').duxFriends({
			addFun:function(obj){},
			delFun:function(obj){}
		});
		//点赞
		$('#user-info').duxPraise({
			addFun:function(obj){},
			delFun:function(obj){}
		});
	};
	//头像
	$.duxAvatar = function (options) {
		var defaults = {
        }
        $('#upload').duxFileUpload({
        type: 'jpg,png,gif,bmp',
		uploadText: '上传头像',
			complete:function(data){
				$('.avatar-image').attr('src',data.url);
				$('#avatar').val(data.url);
			}
		});
	};
    //表格处理
    $.fn.duxTable = function (options) {
        var defaults = {
            selectAll: '#selectAll',
			selectDel: '#selectDel',
            actionUrl: '',
			actionParameter: {}
        }
        var options = $.extend(defaults, options);
		var element = this;
		Do.ready('dialog', function () {
			element.each(function () {
				var table = this;
				var id = $(this).attr('id');
				//处理多选单选
				$(options.selectAll).click(function () {
					$(table).find("[name='id[]']").each(function () {
						$(this).click();
					})
				});
				//选中变色
				$(table).find("[name='id[]']").click(function(e) {
					if ($(this).prop("checked")) {
						$(this).parents('tr').addClass('select-tr');
					} else {
						$(this).parents('tr').removeClass('select-tr');
					}
					
				});
				//处理删除
				$(table).find('.del').click(function () {
					var obj = this;
					var div = $(obj).parent().parent();
					var url = $(obj).attr('url');
					if(url == '' || url == null || url == 'undefined'){
						url = options.deleteUrl;
					}
					operat(
						obj,
						url,
						function(){
							div.remove();
						},
						function(){
					});
				});
				
				//批量删除
				$(options.selectDel).click(function () {
						//记录获取
						var ids = new Array();
						$(table).find("[name='id[]']").each(function () {
							if ($(this).prop("checked")) {
								ids.push($(this).val());
							}
						})
						if (ids.length == 0) {
							alert('请先选择操作记录');
							return false;
						}
						//删除项目
						var parameter = $.extend({ids: ids},options.actionParameter());
						$.post(options.actionUrl, parameter, function (json) {
							if (json.status) {
								window.location.reload();
							} else {
								alert(json.info);
							}
						}, 'json');
						
				});
				//处理动作
				function operat(obj,url,success,failure){
						var text = $(obj).text();
						var dialog = layer.confirm('你确定要' + text + '吗？', function () {
						var Dload = layer.load('操作执行中，请稍候...');
                        $.post(url, {
                                data: $(obj).attr('data')
                            },
                            function (json) {
								layer.close(Dload);
								layer.close(dialog);
                                if (json.status) {
									layer.msg(json.info, 2, 1);
									success();
                                } else {
									layer.msg(json.info, 2, 0);
									failure();
                                }
                            }, 'json');
                    });
				}
				
			});
		});
    };
    //表单处理
    $.fn.duxForm = function (options) {
        var defaults = {
			errorId : '',
            postFun: {},
            returnFun: {}
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            //表单提交
            var form = this;
            var status = $(form).find('#status');
            Do.ready('form','dialog', function () {
                $(form).Validform({
                    ajaxPost: true,
                    postonce: true,
                    tiptype: function (msg, o, cssctl) {
                        if (!o.obj.is("form")) {
                            //设置提示信息
							if(options.errorId == ''){
                           		var objtip = o.obj.siblings(".help-block");
							}else{
								var objtip = $(options.errorId);
							}
                            if (o.type == 2) {
                                //通过
                                var className = 'formitm-success';
                                status.html('');
								if(options.errorId != ''){
									objtip.text('');
								}
                            }
                            if (o.type == 3) {
                                //未通过
								if(options.errorId == ''){
									var html = '<span class="u-text-danger">您填写的信息不完整！</span>';
									status.html(html);
								}else{
									objtip.text(msg);
								}
                                var className = 'formitm-danger';
                            }
                            //设置样式
                            o.obj.parents('.formitm').removeClass('formitm-success formitm-danger');
                            o.obj.parents('.formitm').addClass(className);
							if(options.errorId == ''){
                            	objtip.text(msg);
							}
                        }
                    },
                    beforeSubmit: function (curform) {
                        //锁定按钮
                        $(form).find('#btn-group').find('button').attr('disabled', true);
                        $(form).find('#btn-submit').text($('#btn-submit').text() + '中...');
                        if ($.isFunction(options.postFun)) {
                            options.postFun();
                        }
                    },
                    callback: function (data) {
                        $(form).find('#btn-submit').text($('#btn-submit').text().replace('中...', ''));
                        if (data.status == 1) {
                            //成功返回
                            if ($.isFunction(options.returnFun)) {
                                options.returnFun(data);
                            } else {
                                if (data.url == null || data.url == '') {
									//不带连接
									layer.alert(data.info, 1, function(){
										window.location.reload();
									});
                                } else {
									//带连接
									$.layer({
										shade: [0],
										area: ['auto','auto'],
										dialog: {
											msg: data.info,
											btns: 2,                    
											type: 4,
											btn: ['继续操作','返回'],
											yes: function(){
												window.location.reload();
											}, no: function(){
												window.location.href = data.url;
											}
										}
									});
                                }
                            }
                        } else {
                            //失败返回
                            $(form).find('#btn-group').find('button').attr('disabled', false);
							layer.alert(data.info, 8);
                        }
                    }
                });
            });
        });
    };

    //时间插件
    $.fn.duxTime = function (options) {
        var defaults = {
            lang: 'ch'
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            var id = this;
            Do.ready('time', function () {
                $(id).datetimepicker(options);
            });
        });
    };

    //上传调用
    $.fn.duxFileUpload = function (options) {
        var defaults = {
            uploadUrl: rootUrl + 'index.php?m=Member&c=Upload&a=upload',
			uploadText: '上传',
            uploadParams: function (){
                //return {'toKen' : toKen}
            },
            type : [],
            complete: function () {}
            
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            var upButton = $(this);
            var urlVal = upButton.attr('data');
            urlVal = $('#' + urlVal);
            var buttonText = upButton.text();
            var preview = upButton.attr('preview');
            preview = $('#' + preview);
            /* 图片预览 */
            preview.click(function ()
            {
                if (urlVal.val() == ''){
                    alert('没有发现已上传图片！');
                }else{
                    window.open(urlVal.val());
                }
                return;
            }
            );
            var Params = '';
            if(options.uploadParams){
                var urlArr = [];
                for(var key in options.uploadParams){
                    urlArr.push(key + '=' + options.uploadParams[key]);
                }
                Params = '&' + urlArr.join('&');
            }
            /*创建上传*/
            Do.ready('webuploader', function () {
                var uploader = WebUploader.create({
                    swf: baseDir + 'webuploader/Uploader.swf',
                    server: options.uploadUrl,
                    pick: {
                        id : upButton,
                        multiple : false
                    },
                    resize: false,
                    auto: true,
                    accept : {
                        title: '指定格式文件',
                        extensions: options.type
                    },
                    formData: options.uploadParams()
                });
                //上传完毕
                uploader.on( 'uploadSuccess', function(file, data) {
                    if (data.status){
                        urlVal.val(data.data.url);
                        options.complete(data.data);
                    }else{
                        alert(data.info);
                    }
                });
                uploader.on( 'uploadError', function( file ) {
                    alert('文件上传失败');
                });
            });
		});
    };
	
	//编辑器调用
    $.fn.duxEditor = function (options) {
        var defaults = {
            uploadUrl: rootUrl + 'index.php?m=Member&c=Upload&a=editor',
            uploadParams: function () {},
            config: {}
        }
        var options = $.extend(defaults, options);
        var uploadParams = {
                        //session_id : sessId
                    };
        this.each(function () {
            var id = this;
			var idName = $(this).attr('id') + '_editor';
            Do.ready('editor', function () {
                //编辑器
                var editorConfig =
                {
                    allowFileManager : false,
                    uploadJson : options.uploadUrl,
                    filterMode: false,
                    extraFileUploadParams : $.extend(uploadParams, options.uploadParams()),
                    afterBlur : function ()
                    {
                        this.sync();
                    },
                    width : '100%'
                };
                editorConfig = $.extend(editorConfig, options.config);
				var str = idName + ' = KindEditor.create(id, editorConfig);';
				eval(str);
            });

        });
    };

	
	//表单页面处理
        $.fn.duxFormPage = function (options) {
            var defaults = {
                uploadUrl: rootUrl + 'index.php?m=DuxCms&c=AdminUpload&a=upload',
                uploadComplete: function () {},
                uploadParamsCallback: function () {},
				uploadType : '*',
                postFun: {},
                returnUrl: '',
                returnFun: {},
				form: true
            }
            var options = $.extend(defaults, options);
            this.each(function () {
                var form = this;
                form = $(form);
                //表单处理
				if(options.form){
					form.duxForm({
						postFun: options.postFun,
						returnUrl: options.returnUrl,
						returnFun: options.returnFun
					});
				}
				//文本框处理
				if (form.find(".u-textarea").length > 0) {
					form.find('.u-textarea').duxTextarea();
				}
                //时间处理
                if (form.find(".u-time").length > 0) {
                    form.find('.u-time').duxTime();
                }
				//编辑器
                if (form.find(".u-editor").length > 0) {
                    form.find('.u-editor').duxEditor({
                        uploadUrl: options.editorUploadUrl
                    });
                }
            });
        };
})(jQuery);