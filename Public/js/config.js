//base
Do.add('base', {
    path: baseDir + 'base/base.js'
});
//图表
Do.add('chartJs', {
    path: baseDir + 'chart/Chart.min.js',
});
Do.add('chart', {
    path: baseDir + 'chart/jquery.easypiechart.min.js'
});
//form
Do.add('form', {
    path: baseDir + 'form/Validform.min.js'
});
//dialog
Do.add('dialog',
{
    path : baseDir + 'dialog/layer.min.js'
}
);
//tip
Do.add('tipsCss', {
    path: baseDir + 'tips/toastr.css',
    type : 'css'
});
Do.add('tips', {
    path: baseDir + 'tips/toastr.min.js',
    requires : ['tipsCss']
});

//editor
Do.add('editorCss',{
    path : baseDir + 'keditor/themes/default/default.css',
    type : 'css'
});
Do.add('editorSrc', {
    path: baseDir + 'keditor/kindeditor-all-min.js',
    requires : ['editorCss']
});
Do.add('editor', {
    path: baseDir + 'keditor/lang/zh-CN.js',
    requires: ['editorSrc']
});

//time
Do.add('timeCss', {
    path: baseDir + 'time/jquery.datetimepicker.css',
    type: 'css'
});
Do.add('time', {
    path: baseDir + 'time/jquery.datetimepicker.js',
    requires: ['timeCss']
});

//webuploader
Do.add('webuploaderCss', {
    path: baseDir + 'webuploader/webuploader.css',
    type: 'css'
});
Do.add('webuploader', {
    path: baseDir + 'webuploader/webuploader.withoutimage.min.js',
    requires: ['webuploaderCss']
});

//sortable
Do.add('sortable', {
    path: baseDir + 'sortable/jquery.sortable.js'
});

//check
Do.add('checkCss', {
    path: baseDir + 'ui/check/skins/blue.css',
    type: 'css'
});
Do.add('check', {
    path: baseDir + 'ui/check/icheck.min.js',
    requires: ['checkCss']
});

//text
Do.add('textBox', {
    path: baseDir + 'text/jquery.textbox.js'
});

//switch
Do.add('switch', {
    path: baseDir + 'switch/jquery.SuperSlide.js'
});


//调试函数
function debug(obj) {
    if (typeof console != 'undefined') {
        console.log(obj);
    }
}