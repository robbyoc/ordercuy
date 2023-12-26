<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
|	https://codeigniter.com/userguide3/general/routing.html
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
$route['default_controller'] = 'Auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth
$route['login'] = 'Auth/login';
$route['register'] = 'Auth/register';
$route['logout'] = 'Auth/logout';

// User
$route['userHome'] = 'User/home';
$route['userOrder'] = 'User/order';
$route['userHistory'] = 'User/orderHistory';
$route['userCart'] = 'User/cartItems';
$route['userProfile'] = 'User/profile';
$route['userEdit'] = 'User/editProfile';
$route['userUpdate'] = 'User/updateProfile';
$route['deactivateAccount'] = 'User/deactivateAccount';
$route['deleteAccount'] = 'User/deleteAccount';
$route['addToCart/(:any)'] = 'User/addToCart/$1';
$route['decreaseQuantity/(:any)'] = 'User/decreaseQuantity/$1';
$route['increaseQuantity/(:any)'] = 'User/increaseQuantity/$1';
$route['submitOrder'] = 'User/submitOrder';
$route['removeFromCart/(:any)/(:any)'] = 'User/removeFromCart/$1/$2';
$route['submitOrder'] = 'User/submitOrder';

// Admin
$route['adminHome'] = 'Admin/home';
$route['adminOrder'] = 'Admin/order';
$route['adminMenu'] = 'Admin/menu';
$route['addMenu'] = 'Admin/addMenu';
$route['editMenu/(:any)'] = 'Admin/editMenu/$1';
$route['deleteMenu/(:any)'] = 'Admin/deleteMenu/$1';
$route['adminReport'] = 'Admin/report';
$route['manageAcc'] = 'Admin/manageAcc';
$route['adminEdit'] = 'Admin/adminEditProfile';
$route['adminUpdate'] = 'Admin/adminUpdateProfile';
$route['adminDeactivateAcc/(:any)'] = 'Admin/deactivateAcc/$1';
$route['adminActivateAcc/(:any)'] = 'Admin/activateAcc/$1';
$route['adminDeleteAcc/(:any)'] = 'Admin/deleteAcc/$1';
