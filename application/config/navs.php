<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



//////////////////////////////////////////////////

/// SIDE BAR LINKS                            ///

////////////////////////////////////////////////

// echo 

$nav = array();

$nav['dashboard'] = array('title'=>'<i class="icon-speedometer"></i> <span class="title">Dashboard</span>','path'=>'dashboard','exclude'=>0);

// $nav['email_settings'] = array('title'=>'<i class="icon-envelope-letter"></i> <span class="title">Email Settings</span></span>','path'=>'email/','exclude'=>0);	



		// $trans['receiving'] = array('title'=>'Receiving','path'=>'receiving','exclude'=>0);

		// $trans['adjustment'] = array('title'=>'Adjustment','path'=>'adjustment','exclude'=>0);

		// $trans['spoilage'] = array('title'=>'Spoilage','path'=>'spoilage','exclude'=>0);

	// $inventory['trans'] = array('title'=>'<span class="title">Transactions</span>','path'=>$trans,'exclude'=>0);

		$inq['inv_move'] = array('title'=>'Component Inventory Movements','path'=>'items/inv_move','exclude'=>0);

		$inq['menu_move'] = array('title'=>'Item Inventory Movement','path'=>'items/menu_move','exclude'=>0);

		$inq['item_inv'] = array('title'=>'Quantity On Hand','path'=>'items/inventory','exclude'=>0);
		$inq['menu_inv'] = array('title'=>' Menu Quantity On Hand','path'=>'menu/inventory','exclude'=>0);

		// $inq['store_order'] = array('title'=>'Store Order','path'=>'store_order','exclude'=>0);

	// $inventory['inq'] = array('title'=>'<span class="title">Inquiries</span>','path'=>$inq,'exclude'=>0);

// 		$nav['inq'] = array('title'=>'<i class="fa fa-commenting-o"></i> <span class="title">Inquiries</span>','path'=>$inq,'exclude'=>0);

		$items['list'] = array('title'=>'List','path'=>'items','exclude'=>0);

		$items['gcategories'] = array('title'=>'Categories','path'=>'settings/categories','exclude'=>0);

		$items['gsubcategories'] = array('title'=>'Sub Categories','path'=>'settings/subcategories','exclude'=>0);

// 	$inventory['items'] = array('title'=>'<span class="title">Items</span>','path'=>$items,'exclude'=>0);

	// $inventory['glocations'] = array('title'=>'Locations','path'=>'settings/locations','exclude'=>0);

	// $inventory['gsuppliers'] = array('title'=>'Suppliers','path'=>'settings/suppliers','exclude'=>0);

	// $inventory['guom'] = array('title'=>'UOM','path'=>'settings/uom','exclude'=>0);

    //$nav['items'] = array('title'=>'<i class="icon-social-dropbox"></i> <span class="title">Inventory</span></span>','path'=>$inventory,'exclude'=>0);	

	$menus['brands'] = array('title'=>'Branches','path'=>'setup/brands','exclude'=>0);

	$menus['menucat'] = array('title'=>'Item Categories','path'=>'menu/categories','exclude'=>0);

	$menus['menulist'] = array('title'=>'Item List','path'=>'menu','exclude'=>0);

	$menus['menusubcat'] = array('title'=>'Item Types','path'=>'menu/subcategories','exclude'=>0);

	// $menus['menusched'] = array('title'=>'Schedules','path'=>'menu/schedules','exclude'=>0);

		$mods['modslist'] = array('title'=>'List','path'=>'mods','exclude'=>0);

		$mods['modgrps'] = array('title'=>'Groups','path'=>'mods/groups','exclude'=>0);

	$menus['mods'] = array('title'=>'<span class="title">Modifiers</span>','path'=>$mods,'exclude'=>0);

	$menus['menusubcat2'] = array('title'=>'Item Subcategories','path'=>'menu/subcategories_new','exclude'=>0);

		// $pos_promos['promos'] = array('title'=>'Promos','path'=>'settings/promos','exclude'=>0);

// 		$menus['gift_cards'] = array('title'=>'<span class="title">Gift Cheque</span>','path'=>'gift_cards','exclude'=>0);

		// $pos_promos['coupons'] = array('title'=>'<span class="title">Coupons</span>','path'=>'coupons','exclude'=>0);

	// $menus['pos_promos'] = array('title'=>'<span class="title">Promos</span>','path'=>$pos_promos,'exclude'=>0);

	// $menus['charges'] = array('title'=>'<span class="title">Charges</span>','path'=>'charges','exclude'=>0);
// 	$menus['charges'] = array('title'=>'<span class="title">Charges</span>','path'=>'charges/charges_new','exclude'=>0);

	$menus['grecdiscs'] = array('title'=>'Receipt Discounts','path'=>'settings/discounts_new','exclude'=>0);
	$menus['trans_type'] = array('title'=>'Transaction Types','path'=>'settings/trans_type','exclude'=>0);
	$menus['payment_group'] = array('title'=>'Payment Group','path'=>'settings/payment_group','exclude'=>0);
	$menus['payment'] = array('title'=>'Payment Types','path'=>'settings/payment_mode','exclude'=>0);

	// $menus['promos'] = array('title'=>'Promos','path'=>'settings/promos_new','exclude'=>0);

	// $menus['gtaxrates'] = array('title'=>'Tax Rates','path'=>'settings/tax_rates','exclude'=>0);

	// $menus['tblmng'] = array('title'=>'Table Management','path'=>'settings/seat_management','exclude'=>0);

	// $menus['denomination'] = array('title'=>'Denominations','path'=>'settings/denomination','exclude'=>0);	

	$nav['menus'] = array('title'=>'<i class="icon-equalizer"></i> <span class="title">Maintenance</span>','path'=>$menus,'exclude'=>0);

// $nav['customers'] = array('title'=>'<i class="icon-users"></i> <span class="title">Customers</span>','path'=>'pos_customers','exclude'=>0);

	// $reps['inv_report'] = array('title'=>'Inventory Report','path'=>'reporting/inventory_report','exclude'=>0);

	// $reps['inv_rep'] = array('title'=>'Item Sales','path'=>'reporting/item_sales_ui','exclude'=>0);

	// $reps['Item_sales_day'] = array('title'=>'Item Sales Per Day','path'=>'reporting/item_sales_day_report','exclude'=>0);

	// $reps['issues_rep'] = array('title'=>'Issues Stamp Report','path'=>'reporting/issues_stamp_rep','exclude'=>0);
// 	$reps['collection_rep'] = array('title'=>'Collection Report','path'=>'reporting/collection_rep','exclude'=>0);

	// $reps['sales_rep'] = array('title'=>'Sales Report','path'=>'reporting/sales_rep','exclude'=>0);

// 	$reps['brand_rep'] = array('title'=>'Brand Report','path'=>'reporting/brand_rep','exclude'=>0);

// 	$reps['brand_sales_upload'] = array('title'=>'Brand Sales Upload Report','path'=>'reporting/branch_sales_upload','exclude'=>0);

// 	$reps['discounts_rep'] = array('title'=>'Discounts Report','path'=>'prints/discs_rep','exclude'=>0);
	// $reps['dtr_rep'] = array('title'=>'DTR','path'=>'reporting/dtr_rep','exclude'=>0);

// 	$reps['gc_sales_rep'] = array('title'=>'Gift Cheque Sales Report','path'=>'reporting/gc_rep','exclude'=>0);

// 	$reps['menu_sales_rep'] = array('title'=>'Menus Report','path'=>'reporting/menus_rep','exclude'=>0);

	// $reps['promo_rep'] = array('title'=>'Promo Redemption Report','path'=>'reporting/promo_rep','exclude'=>0);

// 	$reps['act_receipts'] = array('title'=>'Receipts','path'=>'reprint','exclude'=>0);

// 	$reps['act_receipts_all'] = array('title'=>'Electronic Journal','path'=>'reprint/printReport','exclude'=>0);

	// $reps['act_logs'] = array('title'=>'Activity Logs','path'=>'reports/activity_logs_ui','exclude'=>0);

	// $reps['drawer_count'] = array('title'=>'Drawer Count','path'=>'reports/drawer_count_ui','exclude'=>0);

	// $reps['rep_history'] = array('title'=>'Read History','path'=>'history','exclude'=>0);

	$reps['hourly_rep'] = array('title'=>'Hourly Sales','path'=>'reporting/hourly_rep','exclude'=>0);

// 	$reps['menu_sales_rep_hrly'] = array('title'=>'Hourly Menu Report','path'=>'reporting/menus_rep_hourly','exclude'=>0);

 	// $reps['monthly_be'] = array('title'=>'BIR Monthly Sales','path'=>'reports/monthly_sales_ui','exclude'=>0);

 	// $reps['daily_be'] = array('title'=>'BIR Daily Sales','path'=>'reports/daily_sales_ui','exclude'=>0);
	
 	// $reps['e_sales'] = array('title'=>'BIR E-Sales','path'=>'reporting/ejournal_rep','exclude'=>0);

/*

	$reps['Cashier Report'] = array('title'=>'Cashier Report','path'=>'reporting/cashier_report','exclude'=>0); // for viamare

    $reps['Charge Sales Summary Report'] = array('title'=>'Charge Sales Summary Report','path'=>'reporting/charge_sales_summary_report','exclude'=>0); // for viamare

*/

	// $reps['daily_be'] = array('title'=>'BIR Daily Sales','path'=>'reports/daily_sales_ui','exclude'=>0);

	
      $reps['menu_sales_rep'] = array('title'=>'Menu Item Sales','path'=>'prints/menu_item_sales','exclude'=>0);
       $reps['mod_sales_rep'] = array('title'=>'Menu Modifiers Sales','path'=>'prints/back_mod_sales_rep','exclude'=>0);
// 	$reps['mom_report'] = array('title'=>'Month to Month','path'=>'reporting/month_to_month_rep','exclude'=>0);

	// $reps['promo_report'] = array('title'=>'Promo Report','path'=>'reporting/promo_rep','exclude'=>0);

	// $reps['promo_report'] = array('title'=>'Promo Report','path'=>'reporting/promo_rep','exclude'=>0);

	$reps['system_sales_rep'] = array('title'=>'System Sales Report','path'=>'prints/back_system_sales','exclude'=>0);

// 	$reps['void_sales_rep'] = array('title'=>'Voided Sales Report','path'=>'reporting/void_sales_rep','exclude'=>0);
// 	$reps['xread_rep'] = array('title'=>'Xread Report','path'=>'prints/back_xread','exclude'=>0);
	$reps['zread_rep'] = array('title'=>'Zread Report','path'=>'prints/back_zread','exclude'=>0);
// 	$reps['ytd_report'] = array('title'=>'Year to Date','path'=>'reporting/year_to_date_rep','exclude'=>0);
// 	$reps['transite_rep'] = array('title'=>'Transight Report','path'=>'reporting/transite_rep','exclude'=>0);

	sort($reps);

$nav['reps'] = array('title'=>'<i class="icon-bar-chart"></i> <span class="title">Reports</span>','path'=>$reps,'exclude'=>0);

// $nav['setup'] = array('title'=>'<i class="icon-settings"></i> <span class="title">Setup</span>','path'=>'setup/details','exclude'=>0);

	$controlSettings['user'] = array('title'=>'Users','path'=>'user','exclude'=>0);

	$controlSettings['roles'] = array('title'=>'Roles','path'=>'admin/roles','exclude'=>0);
// 	$controlSettings['import'] = array('title'=>'Import Data','path'=>'admin/import_file','exclude'=>0);
// 	$controlSettings['export'] = array('title'=>'Export Data','path'=>'admin/export_data','exclude'=>0);

	// $controlSettings['restart'] = array('title'=>'Restart','path'=>'admin/restart','exclude'=>0);

	sort($controlSettings);

$nav['control'] = array('title'=>'<i class="icon-user"></i> <span class="title">Admin Control</span>','path'=>$controlSettings,'exclude'=>0);



// $nav['cashier'] = array('title'=>'<i class="fa fa-desktop"></i> <span>Cashier</span>','path'=>'cashier','exclude'=>0);

// 	$trans['receiving'] = array('title'=>'Receiving','path'=>'receiving','exclude'=>0);

// 	$trans['adjustment'] = array('title'=>'Adjustment','path'=>'adjustment','exclude'=>0);

// $nav['trans'] = array('title'=>'<i class="fa fa-random"></i> <span>Transactions</span>','path'=>$trans,'exclude'=>0);

// 	$items['list'] = array('title'=>'List','path'=>'items','exclude'=>0);

// 	$items['gcategories'] = array('title'=>'Categories','path'=>'settings/categories','exclude'=>0);

// 	$items['gsubcategories'] = array('title'=>'Sub Categories','path'=>'settings/subcategories','exclude'=>0);

// 	$items['item_inv'] = array('title'=>'Inventory','path'=>'items/inventory','exclude'=>0);

// $nav['items'] = array('title'=>'<i class="fa fa-flask"></i> <span>Items</span>','path'=>$items,'exclude'=>0);

// 	$menus['menulist'] = array('title'=>'List','path'=>'menu','exclude'=>0);

// 	$menus['menucat'] = array('title'=>'Categories','path'=>'menu/categories','exclude'=>0);

// 	$menus['menusubcat'] = array('title'=>'Sub Categories','path'=>'menu/subcategories','exclude'=>0);

// 	$menus['menusched'] = array('title'=>'Schedules','path'=>'menu/schedules','exclude'=>0);

// $nav['menu'] = array('title'=>'<i class="fa fa-cutlery"></i> <span>Menu</span>','path'=>$menus,'exclude'=>0);

// 	$mods['modslist'] = array('title'=>'List','path'=>'mods','exclude'=>0);

// 	$mods['modgrps'] = array('title'=>'Groups','path'=>'mods/groups','exclude'=>0);

// $nav['mods'] = array('title'=>'<i class="fa fa-tags"></i> <span>Modifiers</span>','path'=>$mods,'exclude'=>0);

	// $pos_promos['promo_free'] = array('title'=>'Free','path'=>'promo/free_menu','exclude'=>0);

	// $pos_promos['promos'] = array('title'=>'Promos','path'=>'settings/promos','exclude'=>0);

// 	$pos_promos['gift_cards'] = array('title'=>'<span>Gift Cards</span>','path'=>'gift_cards','exclude'=>0);

// 	$pos_promos['coupons'] = array('title'=>'<span>Coupons</span>','path'=>'coupons','exclude'=>0);

// $nav['pos_promos'] = array('title'=>'<i class="fa fa-tags"></i> <span>Promos</span>','path'=>$pos_promos,'exclude'=>0);



	// $resSettings['types'] = array('title'=>'Restaurants','path'=>'restaurant/','exclude'=>0);

// $nav['restaurant'] = array('title'=>'<i class="fa fa-cutlery"></i> <span>Restaurants</span>','path'=>'restaurants','exclude'=>0);

	

	//$dtr['schedules'] = array('title'=>'Schedules','path'=>'dtr/dtr_schedules','exclude'=>0);

	

// 	$dtr['shifts'] = array('title'=>'Shifts','path'=>'dtr/dtr_shifts','exclude'=>0);

// 	$dtr['scheduler'] = array('title'=>'Scheduler','path'=>'dtr/scheduler','exclude'=>0);

// $nav['dtr'] = array('title'=>'<i class="fa fa-clock-o"></i> <span>DTR</span>','path'=>$dtr,'exclude'=>0);

	// <i class="fa fa-gift"></i>

	// <i class="fa fa-tag"></i>

	// $reps['act_sales'] = array('title'=>'Sales','path'=>'reports/sales_rep_ui','exclude'=>0);



	// <i class="fa fa-asterisk"></i>



	

	

// $nav['general_settings'] = array('title'=>'<i class="fa fa-cogs"></i> <span>General Settings</span>','path'=>$generalSettings,'exclude'=>0);

	



	

	

	

	

// $nav['maintenance'] = array('title'=>'<i class="fa fa-cogs"></i> <span>Maintenance</span>','path'=>$maintenance,'exclude'=>0);





///ADMIN CONTROL////////////////////////////////



// $nav['messages'] = array('title'=>'<i class="fa fa-envelope-o"></i> <span>Messages</span>','path'=>'messages','exclude'=>1);

// $nav['messages'] = array('title'=>'<i class="fa fa-envelope-o"></i> <span>Messages</span>','path'=>'messages','exclude'=>1);

// $nav['preferences'] = array('title'=>'<i class="fa fa-wrench"></i> <span>Preferences</span>','path'=>'preference','exclude'=>1);

// $nav['profile'] = array('title'=>'<i class="fa fa-folder-o"></i> <span>Profile</span>','path'=>'profile','exclude'=>1);

///LOGOUT///////////////////////////////////////

// $nav['send_to_rob'] = array('title'=>'<i class="fa fa-envelope-o"></i> <span>RLC Server Files</span>','path'=>'reads/manual_send_to_rob','exclude'=>0);

$nav['logout'] = array('title'=>'<i class="icon-logout"></i> <span class="title">Logout</span>','path'=>'site/go_logout','exclude'=>1);

$config['sideNav'] = $nav;

