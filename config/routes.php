<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'home/dashboard/';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['edit/(:any)/(:any)'] = "edit/index/$1/$2";
//$route['(:any)'] = 'live/live/index/$1';
//$route['live/(:any)'] = 'modules/live/index/$1';



#$route['(:any)'] = "home/dashboard/$1";

#$route['login'] = "login/login/";
/*$route['admin/dashboard'] = "admin/common/dashboard/index";
$route['admin/logout'] = "admin/common/logout/index";
$route['admin/register'] = "admin/common/register/index";

// Users
$route['admin/users/user_edit/(:any)'] = "admin/user/users/user_edit/$1";
$route['admin/users/user_password/(:any)'] = "admin/user/users/user_password/$1";
$route['admin/users/user_key/(:any)'] = "admin/user/users/user_key/$1";*/