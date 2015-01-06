<?php
return array(
    /* URL规则 */
    'URL_ROUTE_RULES'=>array(
    	'page/:urlname' => 'Page/Category/index',
    	'class/:urlname' => 'Article/Category/index',
    	'content/:urltitle' => 'Article/Content/index',
    	'form/:name' => 'DuxCms/Form/index',
    	'form_info/:name/:id' => 'DuxCms/Form/info',
    	'tags_list/:name' => 'DuxCms/Tags/index',
    	'tags/:name' => 'DuxCms/TagsContent/index',
	),
);