<?php
return [

	'template'=> [
    'view_path'    => './template/'.config('web.ADMIN_TPL').'/',
    'view_suffix' => 'html',
	'view_depr'    => '-',
	'tpl_begin'    => '{{',
    'tpl_end'      => '}}',
    'taglib_begin' => '<',
    'taglib_end'   => '>',
    ],
		'view_replace_str'  =>  [
				'__ROOT__' => WEB_URL,
				'__INDEX__' => WEB_URL . '/index.php',
				'__HOME__' => WEB_URL . '/template/'.config('web.ADMIN_TPL').'/res',
				'__UPLOAD__' => '/uploads',
				'__PC__' =>WEB_URL. '/public/common/',
				'__IMG__' =>WEB_URL. '/public/images/',
			
		],
		//默认错误跳转对应的模板文件
		'dispatch_error_tmpl' => 'index/tips',
		//默认成功跳转对应的模板文件
		'dispatch_success_tmpl' => 'index/tips',
	

];
