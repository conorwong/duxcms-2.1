<?php
return array(
    /* URL规则 */
    'URL_ROUTE_RULES'=>array(
    	'page/:class_id' => 'Page/Category/index',
    	'class/:class_id' => 'Article/Category/index',
    	'content/:content_id' => 'Article/Content/index',
    	'form/:name' => 'DuxCms/Form/index',
    	'form_info/:name/:id' => 'DuxCms/Form/info',
    	'tags_list/:name' => 'DuxCms/Tags/index',
    	'tags/:name' => 'DuxCms/TagsContent/index',
	),
);