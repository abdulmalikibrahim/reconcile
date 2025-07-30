<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'App';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['upload_mb52'] = 'API/upload_mb52';
$route['upload_juklak'] = 'API/upload_juklak';
$route['upload_wip'] = 'API/upload_wip';
$route['fetch_data'] = 'API/fetch_data';
$route['load_data'] = 'API/load_data';
$route['load_data_wip'] = 'API/load_data_wip';
$route['load_data_actual'] = 'API/load_data_actual';
$route['load_data_mb52'] = 'API/load_data_mb52';
$route['load_data_mastersloc'] = 'API/load_data_mastersloc';
$route['load_data_juklak'] = 'API/load_data_juklak';
$route['load_data_sloc'] = 'API/load_data_sloc';
$route['update_sloc'] = 'API/update_sloc';
$route['delete_sloc'] = 'API/delete_sloc';
$route['remote_login'] = 'API/remote_login';
$route['check_remote'] = 'API/check_remote';
