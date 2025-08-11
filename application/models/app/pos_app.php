<?php
class Pos_app extends CI_Model
{

	function get_all_by_category($category="", $offset=0, $limit = 14)
	{
		//temporary constant
		if($category == 'snacks'){
			$cat = '1';
		}elseif($category == 'drinks'){
			$cat = '2';
		}elseif($category == 'dessert'){
			$cat = '3';
		}elseif($category == 'meals'){
			$cat = '4';
		}elseif($category == 'bread'){
			$cat = '5';
		}


		$this->db->select('menus.menu_id,menus.menu_name,menus.menu_short_desc,images.img_id,images.img_path, menus.cost');
		$this->db->from('menus');	
		$this->db->join('images', 'menus.menu_id = images.img_ref_id AND images.img_tbl="menus"','left');
		$this->db->where('menus.inactive','0');

		if(isset($cat) && !empty($cat)){
			$this->db->where('menus.menu_cat_id',$cat);
		}

		$result = $this->db->get();
		// $result = $this->db->query("(SELECT item_id, name, image_id, unit_price ,$app_files.file_data, $app_files.file_id FROM $items_table 
		// 	LEFT JOIN $app_files ON $items_table.image_id = $app_files.file_id
		// WHERE deleted = 0 and category = ".$this->db->escape($category). " ORDER BY name) UNION ALL (SELECT CONCAT('KIT ',item_kit_id), name, 'no_image' as image_id , 'no_image' as image_id2,unit_price , unit_price as u2 FROM $item_kits_table 
		// WHERE deleted = 0 and category = ".$this->db->escape($category). " ORDER BY name) ORDER BY name LIMIT $offset, $limit");
		// echo $this->db->last_query();die();
		return $result;
	}
	
	function get_image($file_id)
	{
		$this->db->from('images');
		$this->db->where('img_id',$file_id);
		$this->db->where('img_tbl','menus');
		$result = $this->db->get();
		$get_r = $result->result();
		// var_dump($get_r);die();
		$image = '';
		if(isset($get_r[0]->img_path)){
			$image = $get_r[0]->img_path;
		}
		// echo $image;die();
		return $image;
	}


	function get_item_info($item_id)
	{
		$this->db->from('menus');
		$this->db->where('menu_id',$item_id);
		$result = $this->db->get();
		$get_info = $result->result();

		return $get_info;
	}



	function get_employee_location($employee_id)
	{
		// var_dump($employee_id);die();
		$this->db->select('location_id');
		$this->db->from('employees_locations');
		$this->db->where('employee_id',$employee_id);
		$result = $this->db->get();
		$get_info = $result->result();
		$location_id = null;
		// echo "<pre>",print_r($result->result()),"</pre>";die();
		// echo $this->db->last_query();die();

		if(isset($get_info[0]->location_id)){
			$location_id = $get_info[0]->location_id;
		}

		
		 return $location_id;
	}

	function get_tax_rate(){
		// var_dump($employee_id);die();
		$this->db->select('value');
		$this->db->from('app_config');
		$this->db->where('key','default_tax_1_rate');
		$result = $this->db->get();
		$get_info = $result->result();
		$tax_rate = null;
		// echo "<pre>",print_r($result->result()),"</pre>";die();
		// echo $this->db->last_query();die();

		if(isset($get_info[0]->value)){
			$tax_rate = $get_info[0]->value;
		}

		
		 return $tax_rate;
	}


	function insert_sales($sales=array()){
		$this->db->insert('trans_sales', $sales);
		$insert_id = $this->db->insert_id();

		return $insert_id;
	}

	function insert_sales_tax($sales_tax=array()){
		$this->db->insert('trans_sales_tax', $sales_tax);
		$insert_id = $this->db->insert_id();

		return $insert_id;
	}

	function insert_payment_amount($payment=array()){
		$this->db->insert('sales_payments', $payment);
		$insert_id = $this->db->insert_id();

		return $insert_id;
	}

	function batch_insert($table_name = '',$data=array())
	{
		$inserted = null;
		if(!empty($table_name) && !empty($data)){
			$inserted = $this->db->insert_batch($table_name, $data); 
		}

		return $inserted;
	}

	function get_sales_status($sales_id = array())
	{
		// var_dump($employee_id);die();
		$this->db->select('sales_id');
		$this->db->from('trans_sales');
		$this->db->where_in('sales_id',$sales_id);
		$this->db->where('paid','0',FALSE);

		$result = $this->db->get();
		$get_info = $result->result();
			
		return $get_info;
	}

	// function get_sales_received_status($sales_id = array())
	// {
	// 	// var_dump($employee_id);die();
	// 	$this->db->select('sale_id,pump_number,plate_number');
	// 	$this->db->from('sales');
	// 	$this->db->where_in('sale_id',$sales_id);
	// 	$this->db->where('received','1');
	// 	$result = $this->db->get();
	// 	$get_info = $result->result();
			
	// 	return $get_info;
	// }


	function get_all_by_search($search, $offset=0, $limit = 14)
	{
		$items_table = $this->db->dbprefix('items');
		$item_kits_table = $this->db->dbprefix('item_kits');
		$app_files = $this->db->dbprefix('app_files');

		$this->db->select('menus.menu_id,menus.menu_short_desc,images.img_id,images.img_path, menus.cost');
		$this->db->from('menus');	
		$this->db->join('images', 'menus.menu_id = images.img_ref_id AND images.img_tbl="menus"','left');
		$this->db->where('menus.inactive','0');
		$this->db->where("(menus.menu_short_desc LIKE '%".$search."%' OR menus.cost LIKE '%".$search."%' )", NULL, FALSE);

	

		$result = $this->db->get();

		// $result = $this->db->query("(SELECT item_id, name, image_id, unit_price ,$app_files.file_data, $app_files.file_id FROM $items_table 
		// 	LEFT JOIN $app_files ON $items_table.image_id = $app_files.file_id
		// WHERE deleted = 0 and name COLLATE UTF8_GENERAL_CI like  ".$this->db->escape('%'.$search.'%'). " ORDER BY name) UNION ALL (SELECT CONCAT('KIT ',item_kit_id), name, 'no_image' as image_id , 'no_image' as image_id2,unit_price , unit_price as u2 FROM $item_kits_table 
		// WHERE deleted = 0 and name COLLATE UTF8_GENERAL_CI like ".$this->db->escape('%'.$search.'%'). " ORDER BY name) ORDER BY name LIMIT $offset, $limit");
		// echo $this->db->last_query();die();
		return $result;
	}
	function get_sales_payments() {
		// $this->db->select('');
		// $this->db->from();
		// $result = $this->db->get();
		// $get = $result->result();

		$location_id = $this->Employee->get_logged_in_employee_current_location_id();	
		$date = new DateTime("now");

 		$curr_date = $date->format('Y-m-d ');	
		
		$this->db->from('sales');
		$this->db->join('sales_payments', 'sales.sale_id = sales_payments.sale_id', 'left');
		// $this->db->join('sales_items', 'sales_payments.sale_id = sales_items.sale_id', 'left');
		$this->db->where('sales.deleted', 0);
		// $this->db->where('DATE(sale_time)',CURDATE());
		$this->db->where('DATE(sale_time)',$curr_date);
		// $this->db->order_by('sale_id');
		$sales = $this->db->get()->result_array();

		// echo "<pre>",print_r($sales),"</pre>";die();

		for($k=0;$k<count($sales);$k++)
		{
			$item_names = array();
			$this->db->select('name,unit_price,quantity_purchased');
			$this->db->from('items');
			$this->db->join('sales_items', 'sales_items.item_id = items.item_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				// echo 'haha';die();
				$item_names[] = $row['name'];
				$item_uprice[] = $row['unit_price'];
				$item_pur[] = $row['quantity_purchased'];

				if($sales[$k]['total']){
					$vals = $sales[$k]['total'];
					$vals += $row['unit_price'] * $row['quantity_purchased']; 
					$sales[$k]['total'] = $vals;
				}else{
			    	$sales[$k]['total'] = $row['unit_price'] * $row['quantity_purchased'];
				}

			}

			// echo "<pre>",print_r($sales),"</pre>";die();
			$this->db->select('name');
			$this->db->from('item_kits');
			$this->db->join('sales_item_kits', 'sales_item_kits.item_kit_id = item_kits.item_kit_id');
			$this->db->where('sale_id', $sales[$k]['sale_id']);
		
			foreach($this->db->get()->result_array() as $row)
			{
				$item_names[] = $row['name'];
			}
			// echo "<pre>",print_r($sales[$k]['total']),"</pre>";die();
			
			// $sales['total'] += $total[];
			$sales[$k]['items'] = implode(', ', $item_names);
			
		}
		
		return $sales;

	}

	function get_pending_order_count()
	{
		// var_dump($employee_id);die();
		$this->db->select('count(*) as counter');
		$this->db->from('trans_sales');
		$this->db->where('type','App Order');
		$this->db->where('paid','0',FALSE);

		$result = $this->db->get();
		$get_info = $result->result();
			// echo $this->db->last_query();die();
		return $get_info;
	}

}
?>
