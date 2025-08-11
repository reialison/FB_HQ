<?php
// require_once ("secure_area.php");
class Migrator extends CI_controller
{
	function __construct()
	{
		parent::__construct('pos');
		$this->load->helper('url');
	    $this->load->model('third_party/Migrator_model');

		// if(!isset($_SESSION['person_id'])){
		// 	 redirect('/', 'refresh');
		// }
		// echo "<pre>",print_r($_SESSION),"</pre>";die();
		// $this->has_profit_permission = $this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id);
		// $this->has_cost_price_permission = $this->Employee->has_module_action_permission('reports','show_cost_price',$this->Employee->get_logged_in_employee_info()->person_id);
	}

	function index()
	{	
		$headers = array(array('invoice_number','qty','menu_code','price','disccode','cashier_id','invoice_time','guest',	'invoice_date',	'table_num',	'discname',	'discpercent',	'menu_name','page_type',	'added',	'odiscount',	'disc2',	'taxes',	'service_charge'));
		$sales_data = $this->Migrator_model->get_sales()->result();
		
		$output = array_merge($headers,$sales_data);
		echo "<pre>",print_r($sales_data),"</pre>";die();
		$file_name  = restograph_folder.'restograph_'.date('Y-m-d').'.csv';


		$fp = fopen($file_name, 'a+');
		foreach ($headers as $fields) {
			fputcsv($fp, $fields);
		}

		foreach ($sales_data as $fields) {
			$fields = (array) $fields;
		    fputcsv($fp, $fields);
		} 


		fclose($fp); 

		return true;
	}


}
?>