<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

###############################################################################################

# POS SETTINGS

	define('LOADER',					false);#LOADING AT START OF PAGE



	define('BRANCH_CODE',				'FRENCHBAKER_SMMOA');
	define('BRANCH_HEADER', 'THE FRENCH BAKER');

	define('TERMINAL_ID',				'1');

	define('TERMINAL_NUMBER',			'01');

	define('PC_ID',						'1');

	##########################################

	define('ORDERING_STATION',			false);

	##########################################

	define('NEED_FOOD_SERVER',			false);

	define('AUTO_ADD_SERVICE_CHARGE',	true);

	define('AUTO_ADD_SERVICE_CHARGE_DELIVERY',	true);

	define('AUTO_ADD_SERVICE_CHARGE_TAKEOUT',	false);

	define('AUTO_ADD_HANDLING_CHARGE_PANDA',	true);

	define('ORDER_SLIP_PRINTER_SETUP',	true);

	define('SERVER_NO_SETUP',			false);

	

	define('SERVICE_CHARGE_ID',			'1');

	define('SERVICE_CHARGE_DELIVERY_ID','2');

	define('SERVICE_CHARGE_TAKEOUT_ID', '3');

	define('SERVICE_CHARGE_PANDA_ID', '3');

	define('CHECK_OIC_ID',				false);

	define('OIC_ID',					'4');

	define('MANAGER_CALL_IDS',			serialize(array(1,2,4)));



	define('BASE_TAX',					0.12); // Please use base tax form (eg. 0.12) not num form (12)

	define('SALE_TYPES',				serialize(array('DINEIN','COUNTER','DELIVERY','PICKUP','TAKEOUT','DRIVETHRU','RETAIL','FOODPANDA')));

	define('BACKUP_FOLDER',				'C:/xampp/htdocs/dine/backup/xreads'); // BACKUP FOLDER

	

	define('HIDE_DRAWER_AMOUNT',		true);

	

	define('HIDECHIT',		true); ##TRUE PARA MAHIDE YUN CHIT SA SALES	

###############################################################################################

# PRINTERS

	// define('BILLING_PRINTER',			false);

	define('BILLING_PRINTER',			'DEFAULT');

	#---------------------------------------------------------------------------------------------------------------------------------------------

	define('KITCHEN_PRINTER_NAME',		'KITCHEN');# TITLE NA LALABAS SA ORDER SLIP

	// define('KITCHEN_PRINTER_NUM',		false);# PAG NAKA FALSE, 0 YUNG NUMBER NG PRINT

	define('KITCHEN_PRINTER_NUM',		1);        # KUNG ILAN YUNG IPRIPRINT

	// define('KITCHEN_PRINTER',			false);# PAG NAKA FALSE, SUSUNDIN YUNG NAME NUNG NASA SETUP

	define('KITCHEN_PRINTER',			'KITCHEN');# NAME NG PRINTER SA DEVICES AND PRINTERS, PAG NAKA DEFAULT, YUNG NAKA CHECK NA DEFAULT PRINTER 

	#---------------------------------------------------------------------------------------------------------------------------------------------

	define('BEVERAGE_ID',				'2');       # ID SA SUBCATEGORIES

	define('BEVERAGE_PRINTER_NAME',		'BEVERAGE');# TITLE NA LALABAS SA ORDER SLIP

	// define('BEVERAGE_PRINTER_NUM',		false); # PAG NAKA FALSE, 0 YUNG NUMBER NG PRINT

	define('BEVERAGE_PRINTER_NUM',		1);			# KUNG ILAN YUNG IPRIPRINT

	// define('BEVERAGE_PRINTER',			false); # PAG NAKA FALSE, SUSUNDIN YUNG NAME NUNG NASA SETUP

	define('BEVERAGE_PRINTER',			'DEFAULT'); # NAME NG PRINTER SA DEVICES AND PRINTERS, PAG NAKA DEFAULT, YUNG NAKA CHECK NA DEFAULT PRINTER 

	#---------------------------------------------------------------------------------------------------------------------------------------------	

	define('PRINT1_ID',					'3');		# ID SA SUBCATEGORIES

	define('PRINT1_PRINTER_NAME',		'PASTRIES');# TITLE NA LALABAS SA ORDER SLIP

	define('PRINT1_PRINTER_NUM',		1);			# KUNG ILAN YUNG IPRIPRINT

	define('PRINT1_PRINTER',			'DEFAULT'); # NAME NG PRINTER SA DEVICES AND PRINTERS, PAG NAKA DEFAULT, YUNG NAKA CHECK NA DEFAULT PRINTER 

###############################################################################################

# MALL SETTINGS

	define('MALL_ENABLED',				false);

	// define('MALL_ENABLED',				true);

	// define('MALL',						false);

	// define('MALL',						'ortigas');

	// define('MALL',						'robinsons');

	//define('MALL',						'araneta');

	// define('MALL',						'megamall');

	// define('MALL',						'stalucia');

	// define('MALL',						'ayala');

	// define('MALL',						'eton');

	// define('MALL',						'vistamall');

	// define('MALL',						'cbmall');

 	// define('MALL',						'megaworld');

###############################################################################################

# FOR BAKER's STUDIO PROMO

	define('BUY2TAKE1'				,	false);

	define('BUY2TAKE1_STARTTIME'	,	'08:30 PM');

	define('BUY2TAKE1_ENDTIME'		,	'11:00 PM');	

###############################################################################################

# TRANS TYPES

	define('GC',						'8');

	define('FREE_SALE',			    	'99');

	define('SALES_TRANS',				'10');

	define('SALES_VOID_TRANS',			'11');

	define('RECEIVE_TRANS',				'20');

	define('SPOIL_TRANS',				'35');

	define('ADJUSTMENT_TRANS',			'30');

	define('CUST_DEPOSIT_TRANS',		'40');

	define('LOYALTY_CARD',				'50');

	define('STORE_ORDER',				'60');

	define('X_READ',					1);

	define('Z_READ',					2);

###############################################################################################

# FOR I FOODS DISCOUNTS  AND LOCAL TAX

	define('DISCOUNT_NET_OF_VAT'	,	false);

	define('DISCOUNT_NET_OF_VAT_EX'	,	'PWDISC');

	define('ADD_CHARGES_NET_OF_VAT'	,	false);

	define('PWDDISC'				,	'PWDISC');
	
	define("NEW_VAT_SALES",false);

################################################################################################

# PRINTER PAPER SETTINGS

	### FOR 80 MM

		// define('PAPER_WIDTH',			38);

		// define('PAPER_LINE',				"======================================");

		// define('PAPER_LINE_SINGLE',		"--------------------------------------");

		// define('PAPER_DET_COL_1',		4);

		// define('PAPER_DET_COL_2',		26);

		// define('PAPER_DET_SUBCOL',		18);

		// define('PAPER_DET_COL_3',		8);

		// define('PAPER_TOTAL_COL_1',		28);

		// define('PAPER_TOTAL_COL_2',		10);

		// define('PAPER_RD_COL_1',		18);

		// define('PAPER_RD_COL_2',		7);

		// define('PAPER_RD_COL_3',		13);

		// define('PAPER_RD_COL_3_3',		13);

		// define('PAPER_RD_COL_MID',		18);

		// define('PAPER_RD_COL_1_2',		4);

		// define('PAPER_RD_COL_1_4',		12);

		// define('PAPER_RECEIPT_TEXT',	10);

		// define('PAPER_RECEIPT_INPUT',	28);

		// define('PAPER_RECEIPT_TEXT_FT',	14);

		// define('PAPER_RECEIPT_INPUT_FT',30);



	### FOR 76 MM

		define('PAPER_WIDTH',			33);

// 		define('PAPER_LINE',			"=================================");

// 		define('PAPER_LINE_SINGLE',		"---------------------------------");

        define('PAPER_LINE',			"=======================================");
        
        define('PAPER_LINE_SINGLE',		"---------------------------------------");

		define('PAPER_DET_COL_1',		2);

		define('PAPER_DET_COL_2',		23);

		define('PAPER_DET_SUBCOL',		17);

		define('PAPER_DET_COL_3',		8);

		define('PAPER_TOTAL_COL_1',		23);

		define('PAPER_TOTAL_COL_2',		10);

		define('PAPER_RD_COL_1',		18);

		define('PAPER_RD_COL_2',		5);

		define('PAPER_RD_COL_3',		8);

		define('PAPER_RD_COL_3_3',		10);

		define('PAPER_RD_COL_1_4',		12);

		define('PAPER_RD_COL_1_2',		3);

		define('PAPER_RD_COL_MID',		13);

		define('PAPER_RECEIPT_TEXT',	10);

		define('PAPER_RECEIPT_INPUT',	23);

		define('PAPER_RECEIPT_TEXT_FT',	14);

		define('PAPER_RECEIPT_INPUT_FT',26);
		
		define('PAPER_R2_COL1',		23);				// 2 col in 1 line 
        define('PAPER_R2_COL2',		16);;				// 2 col in 1 line 
        
        define('PAPER_R3_COL1',		20);				// 3 col in 1 line
        define('PAPER_R3_COL2',		5);				// 3 col in 1 line
        define('PAPER_R3_COL3',		14);				// 3 col in 1 line

################################################################################################

/*

|--------------------------------------------------------------------------

| File and Directory Modes

|--------------------------------------------------------------------------

|

| These prefs are used when checking and setting modes when working

| with the file system.  The defaults are fine on servers with proper

| security, but you may wish (or even need) to change the values in

| certain environments (Apache running a separate process for each

| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should

| always be used to set the mode correctly.

|

*/

define('FILE_READ_MODE', 0644);

define('FILE_WRITE_MODE', 0666);

define('DIR_READ_MODE', 0755);

define('DIR_WRITE_MODE', 0777);

/*

|--------------------------------------------------------------------------

| File Stream Modes

|--------------------------------------------------------------------------

|

| These modes are used when working with fopen()/popen()

|

*/

define('FOPEN_READ',							'rb');

define('FOPEN_READ_WRITE',						'r+b');

define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care

define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care

define('FOPEN_WRITE_CREATE',					'ab');

define('FOPEN_READ_WRITE_CREATE',				'a+b');

define('FOPEN_WRITE_CREATE_STRICT',				'xb');

define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');





#DEFINE TIME RANGES

$time = array();

$time[] = array("FTIME"=>"0:00","TTIME"=>"1:00");

$time[] = array("FTIME"=>"1:00","TTIME"=>"2:00");

$time[] = array("FTIME"=>"2:00","TTIME"=>"3:00");

$time[] = array("FTIME"=>"3:00","TTIME"=>"4:00");

$time[] = array("FTIME"=>"4:00","TTIME"=>"5:00");

$time[] = array("FTIME"=>"5:00","TTIME"=>"6:00");

$time[] = array("FTIME"=>"6:00","TTIME"=>"7:00");

$time[] = array("FTIME"=>"7:00","TTIME"=>"8:00");

$time[] = array("FTIME"=>"8:00","TTIME"=>"9:00");

$time[] = array("FTIME"=>"9:00","TTIME"=>"10:00");

$time[] = array("FTIME"=>"10:00","TTIME"=>"11:00");

$time[] = array("FTIME"=>"11:00","TTIME"=>"12:00");

$time[] = array("FTIME"=>"12:00","TTIME"=>"13:00");

$time[] = array("FTIME"=>"13:00","TTIME"=>"14:00");

$time[] = array("FTIME"=>"14:00","TTIME"=>"15:00");

$time[] = array("FTIME"=>"15:00","TTIME"=>"16:00");

$time[] = array("FTIME"=>"16:00","TTIME"=>"17:00");

$time[] = array("FTIME"=>"17:00","TTIME"=>"18:00");

$time[] = array("FTIME"=>"18:00","TTIME"=>"19:00");

$time[] = array("FTIME"=>"19:00","TTIME"=>"20:00");

$time[] = array("FTIME"=>"20:00","TTIME"=>"21:00");

$time[] = array("FTIME"=>"21:00","TTIME"=>"22:00");

$time[] = array("FTIME"=>"22:00","TTIME"=>"23:00");

$time[] = array("FTIME"=>"23:00","TTIME"=>"0:00");

define('TIMERANGES', serialize($time));



/* End of file constants.php */

/* Location: ./application/config/constants.php */



define('menu_folder','uploads/menus/');

define('restograph_folder','restograph/');



define('LOCALSYNC',false);

define('MASTERMIGRATION',false); 

define('MIGRATED_MAIN_DB','main_sync');  // name of main database connection based on database.php

// define('MIGRATED_MASTER_DB','master'); // name of master database connection based on database.php

define('EXECUTE_MASTER_WITH_ZREADING',false); // enable if the system will migrate the data on the server when zreading was executed

define('EXECUTE_MASTER_WITH_EVERY_SALES',false); // enable if the system will migrate the data on the server after settlemenr of 1 order



###############################################################################################

# Company Settings

define("COMPANY_NAME", "");

define("COMPANY_ADDRESS", "");

define("TIN", "");

define("ACCRDN", "");

define("PERMIT", "");

define("SN", "");

define("MIN", "");

###############################################################################################







define("HAS_MOD_SUB_CAT_ID",TRUE);

define("ENABLE_ADD_LOCAL_USER",TRUE);

define("KITCHEN_SEQUENCE",false);

define("HAS_ITEM_RECIPE",true);

define("MENU_OTHER_DESC",false);

define("SHOW_SKU",false);

define("OTHER_MENU_ITEM_SALES",false);

define("READ_OTHER_MENU_ITEM_SALES",true);

define("EWT_DISCOUNT",false);