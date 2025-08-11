<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "site";
$route['404_override'] = '';


$route['login'] = "site/login1";
$route['fixer'] = "dine/fixer";
$route['fixer/(:any)'] = "dine/fixer/$1";
# APP #
	$route['app_get'] = "dine/app_get";
	$route['app_get/(:any)'] = "dine/app_get/$1";

$route['download'] = "dine/download";
$route['download/(:any)'] = "dine/download/get_file/$1";


# MALL #
	$route['megamall'] = "dine/megamall";
	$route['megamall/(:any)'] = "dine/megamall/$1";
	$route['araneta'] = "dine/araneta";
	$route['araneta/(:any)'] = "dine/araneta/$1";
	$route['ortigas'] = "dine/ortigas";
	$route['ortigas/(:any)'] = "dine/ortigas/$1";
	$route['robinsons'] = "dine/robinsons";
	$route['robinsons/(:any)'] = "dine/robinsons/$1";
	$route['stalucia'] = "dine/stalucia";
	$route['stalucia/(:any)'] = "dine/stalucia/$1";
	$route['ayala'] = "dine/ayala";
	$route['ayala/(:any)'] = "dine/ayala/$1";
	$route['eton'] = "dine/eton";
	$route['eton/(:any)'] = "dine/eton/$1";
	$route['vistamall'] = "dine/vistamall";
	$route['vistamall/(:any)'] = "dine/vistamall/$1";
	$route['cbmall'] = "dine/cbmall";
 	$route['cbmall/(:any)'] = "dine/cbmall/$1";
 	$route['megaworld'] = "dine/megaworld";
 	$route['megaworld/(:any)'] = "dine/megaworld/$1";
	$route['over'] = "dine/over";
	$route['over/(:any)'] = "dine/over/$1";
# DINE #
	$route['settings'] = "dine/settings";
	$route['settings/(:any)'] = "dine/settings/$1";
	$route['items'] = "dine/items";
	$route['items/(:any)'] = "dine/items/$1";
	$route['customers'] = "dine/customers";
	$route['customers/(:any)'] = "dine/customers/$1";
	$route['setup'] = "dine/setup";
	$route['setup/(:any)'] = "dine/setup/$1";
	$route['menu'] = "dine/menu";
	$route['menu/(:any)'] = "dine/menu/$1";
	$route['mods'] = "dine/mods";
	$route['mods/(:any)'] = "dine/mods/$1";
	$route['email'] = "dine/email";
	$route['email/(:any)'] = "dine/email/$1";
	$route['receiving'] = "dine/receiving";
	$route['receiving/(:any)'] = "dine/receiving/$1";
	$route['adjustment'] = "dine/adjustment";
	$route['adjustment/(:any)'] = "dine/adjustment/$1";
	$route['spoilage'] = "dine/spoilage";
	$route['spoilage/(:any)'] = "dine/spoilage/$1";
	$route['cashier'] = "dine/cashier";
	$route['cashier/(:any)'] = "dine/cashier/$1";
	$route['manager'] = "dine/manager";
	$route['manager/(:any)'] = "dine/manager/$1";	$route['dtr'] = "dine/dtr";
	$route['dtr/(:any)'] = "dine/dtr/$1";
	$route['clock'] = "dine/clock";
	$route['clock/(:any)'] = "dine/clock/$1";
	$route['gift_cards'] = "dine/gift_cards";
	$route['gift_cards/(:any)'] = "dine/gift_cards/$1";
	$route['drawer'] = "dine/drawer";
	$route['drawer/(:any)'] = "dine/drawer/$1";
	$route['charges'] = "dine/charges";
	$route['charges/(:any)'] = "dine/charges/$1";
	$route['reports'] = "dine/reports";
	$route['reports/(:any)'] = "dine/reports/$1";
	$route['endofday'] = "dine/endofday";
	$route['endofday/(:any)'] = "dine/endofday/$1";	
	$route['shift'] = "dine/shift";
	$route['shift/(:any)'] = "dine/shift/$1";	
	$route['reads'] = "dine/reads";
	$route['reads/(:any)'] = "dine/reads/$1";
	$route['splash'] = "dine/splash";
	$route['splash/(:any)'] = "dine/splash/$1";	
	$route['main'] = "dine/main";
	$route['main/(:any)'] = "dine/main/$1";		
	$route['reprint'] = "dine/reprint";
	$route['reprint/(:any)'] = "dine/reprint/$1";	
	$route['coupons'] = "dine/coupons";
	$route['coupons/(:any)'] = "dine/coupons/$1";
	$route['importer'] = "dine/importer";
	$route['importer/(:any)'] = "dine/importer/$1";			
	$route['history'] = "dine/history";
	$route['history/(:any)'] = "dine/history/$1";			
	$route['prints'] = "dine/prints";
	$route['prints/(:any)'] = "dine/prints/$1";			
	$route['reporting'] = "dine/reporting";
	$route['reporting/(:any)'] = "dine/reporting/$1";			
	$route['pos_customers'] = "dine/pos_customers";
	$route['pos_customers/(:any)'] = "dine/pos_customers/$1";			
	$route['custs_bank'] = "dine/custs_bank";
	$route['custs_bank/(:any)'] = "dine/custs_bank/$1";			
	$route['loyalty'] = "dine/loyalty";
	$route['loyalty/(:any)'] = "dine/loyalty/$1";			
	$route['promo'] = "dine/promo";
	$route['promo/(:any)'] = "dine/promo/$1";
	$route['store_order'] = "dine/store_order";
	$route['store_order/(:any)'] = "dine/store_order/$1";	

# RESTO #
	$route['restaurants'] = "resto/restaurants";
	$route['restaurants/(:any)'] = "resto/restaurants/$1";
	$route['managements'] = "resto/managements";
	$route['managements/(:any)'] = "resto/managements/$1";
	// $route['menu'] = "resto/menu";
	// $route['menu/(:any)'] = "resto/menu/$1";
	$route['branches'] = "resto/branches";
	$route['branches/(:any)'] = "resto/branches/$1";

# APP #
	$route['ourMenu'] = "app/ourmenu";
	$route['ourMenu/(:any)'] = "app/ourmenu/$1";
	$route['order'] = "app/order";
	$route['order/(:any)'] = "app/order/$1";

# DASHBOARD #
	$route['dashboard'] = "core/dashboard";
	$route['dashboard/(:any)'] = "core/dashboard/$1";
	$route['search'] = "core/search";
	$route['search/(:any)'] = "core/search/$1";

# USER #
	$route['user'] = "core/user";
	$route['user/(:any)'] = "core/user/$1";

# ADMIN #
	$route['admin'] = "core/admin";
	$route['admin/(:any)'] = "core/admin/$1";

# TRANS #
	$route['trans'] = "core/trans";
	$route['trans/(:any)'] = "core/trans/$1";

# WAGON #
	$route['wagon'] = "core/wagon";
	$route['wagon/(:any)'] = "core/wagon/$1";	


#POS#
	$route['app'] = "app/pos/shop/$1";
	$route['app/shop/(:any)'] = "app/pos/shop/$1";
	$route['app/add_to_cart/(:any)'] = "app/pos/add_to_cart/$1";
	$route['app/add_to_cart'] = "app/pos/add_to_cart";
	$route['app/remove_to_cart'] = "app/pos/remove_to_cart";
	$route['app/update_to_cart'] = "app/pos/update_to_cart";
	$route['app/checkout'] = "app/pos/checkout";

	$route['app/cart'] = "app/pos/cart";
	$route['app/add_checkout_details'] = "app/pos/add_checkout_details";
	$route['app/check_order_status'] = "app/pos/check_order_status";
	$route['app/check_new_orders'] = "app/pos/check_new_orders";
	$route['app/check_order_received_status'] = "app/pos/check_order_received_status";


	$route['app/search/(:any)'] = "app/pos/search/$1";

	$route['app/table'] = "app/pos/tabledata";
	$route['app/image/(:any)'] = "app/pos/image/$1";

#RESTOGRAPH CSV
	$route['migrator'] = "third_party/migrator/index";
	$route['migrator/(:any)'] = "third_party/migrator/$1";

/* End of file routes.php */
/* Location: ./application/config/routes.php */