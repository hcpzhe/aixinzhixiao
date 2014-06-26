<?php
define('THINK_PATH', './ThinkPHP/');

//定义项目名称
define('APP_NAME', 'aixin');

//定义项目路径
define('APP_PATH', './aixin/');

//define ( 'RUNTIME_PATH', './Runtime/' );

//定义公共目录路径
define('PUBLIC_PATH', './Public/');

//定义公共目录路径
define('SESSION_PATH', './Session/');


//session存储路径生成
if (!is_dir(SESSION_PATH)) mkdir(SESSION_PATH);

$path = SESSION_PATH.'Admin/';
if (!is_dir($path)) mkdir($path);

$path = SESSION_PATH.'Home/';
if (!is_dir($path)) mkdir($path);

if (!is_dir(PUBLIC_PATH)) mkdir(PUBLIC_PATH);

//
////编辑器内容 所用图片上传路径
//$path = APP_PATH.'Public/textUpload/';
//if (!is_dir($path)) mkdir($path);
//
////项目_upload文件上传路径
//$path = APP_PATH.'Public/Uploads/';
//if (!is_dir($path)) mkdir($path);

//加载框架入口文件
require './ThinkPHP/ThinkPHP.php';
