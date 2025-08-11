<?php
class Migrator_model extends CI_Model
{

	function get_sales()
	{

		$this->db->select("trans_sales.trans_ref, trans_sales_menus.qty , trans_sales_menus.menu_id ,
		 trans_sales_menus.price , trans_sales_discounts.disc_code, trans_sales.user_id,
		 TIME(trans_sales_payments.datetime) as timeonly ,trans_sales.guest ,
		 DATE(trans_sales_payments.datetime) as invoice_date, trans_sales.table_id,
		 trans_sales_discounts.disc_code as discount_code, trans_sales_discounts.disc_rate,menus.menu_name,
		  trans_sales_menus.free_user_id as empty, trans_sales_menus.free_user_id,trans_sales_discounts.amount as discount_amount,
		     trans_sales_menus.free_user_id as empty2 , trans_sales_tax.`amount` AS tax_amount, trans_sales_charges.amount as charges ");
		$this->db->from('trans_sales');
		$this->db->join('trans_sales_discounts', 'trans_sales.sales_id = trans_sales_discounts.sales_id','left');
		$this->db->join('trans_sales_payments', 'trans_sales.sales_id = trans_sales_payments.sales_id','left');
		$this->db->join('trans_sales_menus', 'trans_sales.sales_id = trans_sales_menus.sales_id','left');
		$this->db->join('menus', 'trans_sales_menus.sales_menu_id = menus.menu_id','left');
		$this->db->join('trans_sales_tax', 'trans_sales.sales_id = trans_sales_tax.sales_id ','left');
		$this->db->join('trans_sales_charges', 'trans_sales.sales_id = trans_sales_charges.sales_id ','left');
		$this->db->where('trans_sales.paid','1');
		$this->db->where('DATE(trans_sales_payments.datetime)','CURDATE()',false);

		// DATE(column) = CURDATE(); 
		$result = $this->db->get();
		// echo $this->db->last_query();die();
		return $result;
	}
	
	

}
?>
