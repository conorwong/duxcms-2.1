//base载入
Do.add('baseJs', {
    path: duxConfig.baseDir + 'base/base.js',
});

Do.add('base', {
    path: duxConfig.adminDir + 'base/admin.js',
    requires: ['baseJs']
});

//图表
Do.add('chartJs', {
    path: duxConfig.adminDir + 'chart/Chart.min.js',
});
Do.add('chart', {
    path: duxConfig.adminDir + 'chart/jquery.easypiechart.min.js'
});
//editor
Do.add('editorCss',{
    path : duxConfig.adminDir + 'keditor/themes/default/default.css',
    type : 'css'
});
Do.add('editorSrc', {
    path: duxConfig.adminDir + 'keditor/kindeditor-all-min.js',
    requires : ['editorCss']
});
Do.add('editor', {
    path: duxConfig.adminDir + 'keditor/lang/zh-CN.js',
    requires: ['editorSrc']
});