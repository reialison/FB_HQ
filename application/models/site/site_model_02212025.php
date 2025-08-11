<?php
class Site_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
		// if($this->input->post('change_db')){
		// 	$this->change_db($this->input->post('change_db'));
		// }
		
		// $date = null;
		// if($this->input->post('daterange')){
		// 	$date = $this->input->post('daterange');
		// }  
		// else if($this->input->post('date')){
		// 	$date = $this->input->post('date');
		// }
		// else if($this->input->post('month')){
		// 	$today = sql2Date(phpNow());
		// 	$year = date('Y',strtotime($today));
        // 	$date = $year."-".$this->input->post('month')."-01";
		// }
		
		// if($date != null){
		// 	$dates = explode(" to ",$date);
		// 	$used = $dates[0];
		// 	$today = sql2Date(phpNow());
		// 	if(strtotime($used) < strtotime($today)){
		// 		$this->change_db('default');
		// 	}
		// }
		// else{
		// 	if($this->input->post('change_db')){
		// 		$this->change_db('default');
		// 	}
		// }


	}
	function change_db($name){
		// $this->db = $this->load->database($name, TRUE);
	}
	public function get_image($img_id=null,$img_ref_id=null,$img_tbl=null,$args=array(),$result=true){
		$this->db->select('*');
		$this->db->from('images');
		if (!is_null($img_id)){
			if (is_array($img_id))
				$this->db->where_in('images.img_id',$img_id);
			else
				$this->db->where('images.img_id',$img_id);
		}
		if (!is_null($img_ref_id)){
			if (is_array($img_id))
				$this->db->where_in('images.img_ref_id',$img_ref_id);
			else
				$this->db->where('images.img_ref_id',$img_ref_id);
		}
		if (!is_null($img_tbl)){
			if (is_array($img_tbl))
				$this->db->where_in('images.img_tbl',$img_tbl);
			else
				$this->db->where('images.img_tbl',$img_tbl);
		}
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						if(isset($val['third']))
							$this->db->$func($col,$val['val'],$val['third']);
						else
							$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}

		$query = $this->db->get();
		if($result){
			return $query->result();
		}
		else{
			return $query;
		}
	}
	public function get_tbl($table=null,$args=array(),$order=array(),$joinTables=null,$result=true,$select='*',$group=null,$limit=null,$count_only=false,$or=false,$args2=array()){
		
		$this->db->select($select,false);
		$this->db->from($table);
		if (!is_null($joinTables) && is_array($joinTables)) {
			foreach ($joinTables as $k => $v) {
				if(is_array($v))
					$this->db->join($k,$v['content'],(!empty($v['mode']) ? $v['mode'] : 'inner'));
				else
					$this->db->join($k,$v);
			}
		}
		// echo "<pre>",print_r($args),"</pre>";die();
		if(!empty($args)){
			foreach ($args as $col => $val) {
				// echo print_r($col);die();
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						if(isset($val['third']))
							$this->db->$func($col,$val['val'],$val['third']);
						else
							$this->db->$func($col,$val['val']);
					}
				}
				else
					if($or == true){
						foreach ($args2 as $col2 => $val2) {
							$this->db->where($col,$val);						
							$this->db->or_where($col2,$val2);
						}
					}else{
						$this->db->where($col,$val);						
					}
			}
		}
		if($group != null){
			$this->db->group_by($group);
		}
		if($limit != null){
			if(is_array($limit))
				$this->db->limit($limit[0],$limit[1]);
			else
				$this->db->limit($limit);
		}
		if(count($order) > 0){
			foreach ($order as $col => $val) {
				$this->db->order_by($col,$val);
			}
		}
		if($count_only){
			return $this->db->count_all_results();
		}
		else{
			// echo "<pre>",print_r($joinTables),"</pre>";die();
			$query = $this->db->get();
			// echo $this->db->last_query();

			// echo count($query->result());
			// // echo  $this->db->count_all_results();die()
			// foreach ($query->result() as $row)
		 //     { 
			// 		echo "<pre>",print_r($row),"</pre>";
		 //        // var_dump($row);
		 //        // die();
		 //     }
   //   die();
			// echo $this->db->last_query();die();

			if($result){
				return $query->result();
			}
			else{
				return $query;
			}
		}
	}
	public function get_tbl_cols($tbl=null){
		$cols = null;
		if($tbl != null){
			$cols = $this->db->list_fields($tbl);
		}
		return $cols;
	}
	public function add_tbl_batch($table_name,$items){
		$this->db->insert_batch($table_name,$items);
		return $this->db->insert_id();
	}
	public function add_tbl($table_name,$items,$set=array()){
		if(!empty($set)){
			foreach ($set as $key => $val) {
				$this->db->set($key, $val, FALSE);
			}
		}
		$this->db->insert($table_name,$items);
		return $this->db->insert_id();
	}
	public function update_tbl($table_name,$table_key,$items,$id=null,$set=array()){
		if(is_array($table_key)){
			foreach ($table_key as $key => $val) {
				if(is_array($val)){
					$this->db->where_in($key,$val);
				}
				else
					$this->db->where($key,$val);
			}
		}
		else{
			if(is_array($id)){
				$this->db->where_in($table_key,$id);
			}
			else
				$this->db->where($table_key,$id);
		}
		if(!empty($set)){
			foreach ($set as $key => $val) {
				$this->db->set($key, $val, FALSE);
			}
		}
		$this->db->update($table_name,$items);
		return $this->db->last_query();
	}
	public function delete_tbl_batch($table_name=null,$args=null){
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->delete($table_name);
	}
	public function delete_tbl($table_name=null,$args=null){
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->db->$func($col,$val['val']);
					}
				}
				else
					$this->db->where($col,$val);
			}
		}
		$this->db->delete($table_name);
	}
	public function get_user_details($id=null,$username=null,$password=null,$pin=null){
		$this->db->trans_start();
			$this->db->select('users.*, user_roles.role as user_role,user_roles.access as access, user_roles.id as user_role_id');
			$this->db->from('users');
			$this->db->join('user_roles','users.role = user_roles.id','LEFT');
			if($id != null){
				$this->db->where('users.id',$id);
			}
			if($pin != null){
				$this->db->where('users.pin',$pin);
			}
			// if($username != null && $password != null){
			$this->db->where('users.username',$username);
			$this->db->where('users.password',md5($password));
			// }
			$this->db->where('users.inactive',0);
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		// echo $username;
			// echo $this->db->last_query();die();

		if(count($result) > 0){
			if(count($result) == 1){
				return $result[0];
			}
			else{
				return $result;
			}
		}
		else{
			return array();
		}
	}

	public function get_db_now($format='php',$dateOnly=false){
		if($dateOnly)
			$query = $this->db->query("SELECT DATE(now()) as today");
		else
			$query = $this->db->query("SELECT now() as today");
		$result = $query->result();
		foreach($result as $val){
			$now = $val->today;
		}
		if($format=='php')
			return date('m/d/Y H:i:s',strtotime($now));
		else
			return $now;
	}

	public function update_language($items,$id){
		$this->db->trans_start();
		$this->db->where('id',$id);
		$this->db->update('users',$items);
		$this->db->trans_complete();
		return $this->db->last_query();
	}

	public function get_company_profile(){
		$this->db->trans_start();
			$this->db->select('company_profile.*');
			$this->db->from('company_profile');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result[0];
	}

	public function get_custom_val($tbl,$col,$where=null,$val=null,$returnAll=false){
		if(is_array($col)){
			$colTxt = "";
			foreach ($col as $col_txt) {
				$colTxt .= $col_txt.",";
			}
			$colTxt = substr($colTxt,0,-1);
			$this->db->select($tbl.".".$colTxt);
		}
		else{
			$this->db->select($tbl.".".$col);
		}
		$this->db->from($tbl);

		if($where != '' || $val != '')
			$this->db->where($tbl.".".$where,$val);
		
		$query = $this->db->get();
		$result = $query->result();
		if($returnAll){
			return $result;
		}
		else{
			if(count($result) > 0){
				if(count($result) == 1)
					return $result[0];
				else
					return $result;
			}
			else
				return "";
		}
	}

	public function update_profile($user,$id){
		$this->db->where('id', $id);
		$this->db->update('users', $user);

		return $this->db->last_query();
	}
	public function get_exportData($branch){
		$this->db->select('menus.menu_id,menus.branch_code,menus.menu_code,menus.menu_short_desc,menus.menu_name,menu_categories.menu_cat_name,menu_categories.menu_cat_id,menu_subcategories.menu_sub_cat_name,menu_subcategories.menu_sub_cat_id,menus.cost');
		$this->db->from('menus');
		$this->db->join('menu_categories', 'menus.menu_cat_id = menu_categories.menu_cat_id','left');
		$this->db->join('menu_subcategories', 'menus.menu_sub_cat_id = menu_subcategories.menu_sub_cat_id','left');
		$this->db->where('menus.branch_code', $branch);
		$this->db->group_by('menus.menu_id');
		$q = $this->db->get();
		return $q->result();
	}
	public function get_exportData_items($branch){
		$this->db->select('items.item_id,items.branch_code,items.code,items.barcode,items.name,categories.code as category_code,categories.name as c_name, subcategories.code as subcategories_code, subcategories.name as subcategories_name,items.cost,items.uom,items.cat_id,items.subcat_id,items.type');
		$this->db->from('items');
		$this->db->join('categories', 'items.cat_id = categories.cat_id','left');
		$this->db->join('subcategories', 'items.subcat_id = subcategories.sub_cat_id','left');
		$this->db->where('items.branch_code', $branch);
		$this->db->group_by('items.item_id');
		$q = $this->db->get();
		return $q->result();
	}
	public function get_data_graph($month,$year,$branch_code=""){
		$this->db->select('*,sum(total_sales) as sales,branch_code as branch_name');
		// $this->db->from('trans_sales use index (branch_code)');
		$this->db->from(' store_zread');
		 // $this->db->join('branch_details', 'branch_details.branch_code = trans_sales.branch_code','left');
		// $this->db->join('categories', 'items.cat_id = categories.cat_id','left');
		// $this->db->join('subcategories', 'items.subcat_id = subcategories.sub_cat_id','left');
		// $this->db->where('branch_code', 'pointone002');
		if($year != ''){
			$this->db->where('YEAR(trans_date) = '.$year);
		}
		if(!empty($branch_code))
		{
			$this->db->where('branch_code', $branch_code);			
		}
		// $this->db->where("void_ref is null");
		// $this->db->where('paid', 1);
		// $this->db->where('trans_sales.inactive', 0);
		$this->db->where('MONTH(trans_date) = '.$month);
		$this->db->group_by('branch_code');
		$this->db->limit(1000);
		$q = $this->db->get();
		// echo $this->db->last_query();die();
		return $q->result();
	}
	public function get_sales_per_store($year="",$branch_code=""){
		// $this->db->select('ts.branch_code,sum(ts.total_amount - charge.amount) as sales');
		$this->db->select('ts.branch_code,sum(ts.total_sales) as sales,ts.branch_code as branch_name');
		// $this->db->from('( select * from trans_sales group by branch_code,sales_id ) ts');
		$this->db->from('store_zread ts');
		// $this->db->join('branch_details', 'branch_details.branch_code = ts.branch_code','left');
		// $this->db->join('trans_sales_charges charge', 'ts.sales_id = charge.sales_id and ts.branch_code = charge.branch_code', 'left');
		// $this->db->join('categories', 'items.cat_id = categories.cat_id','left');
		// $this->db->join('subcategories', 'items.subcat_id = subcategories.sub_cat_id','left');
		// $this->db->where('branch_code', 'pointone002');
		if($year != ''){
			$this->db->where('YEAR(ts.datetime) = '.$year);
		}
		if(!empty($branch_code))
		{
			$this->db->where('ts.branch_code', $branch_code);			
		}
		// $this->db->where('ts.paid', 1);
		// $this->db->where('ts.inactive', 0);	
		// $this->db->where('ts.trans_ref is not null');
		// $this->db->where('ts.void_ref is null');
		$this->db->group_by('ts.branch_code');
		$q = $this->db->get();
		// echo $this->db->last_query();die();
		return $q->result();
	}
	// public function get_category_sales($branch_code="", $month="", $year=""){
	// 	$this->db->select('trans_sales.branch_code, menu_categories.menu_cat_name, sum(price*qty) as sales',false);
	// 	$this->db->from('( select * from trans_sales group by branch_code,sales_id )trans_sales');
	// 	$this->db->join('(select * from trans_sales_menus group by branch_code,sales_menu_id)trans_sales_menus', 'trans_sales.sales_id = trans_sales_menus.sales_id AND trans_sales.branch_code =  trans_sales_menus.branch_code','left');
	// 	$this->db->join('menus', 'menus.menu_id = trans_sales_menus.menu_id AND menus.branch_code = trans_sales_menus.branch_code','left');
	// 	$this->db->join('menu_categories', 'menu_categories.menu_cat_id = menus.menu_cat_id AND menu_categories.branch_code = menus.branch_code','left');
		
	// 	if(!empty($branch_code))
	// 	{
	// 		$this->db->where('trans_sales.branch_code', $branch_code);			
	// 	}
	// 	if(!empty($month))
	// 	{
	// 		$this->db->where('MONTH(trans_sales.datetime) = '.$month);			
	// 		$this->db->where('YEAR(trans_sales.datetime) = '.$year);			
	// 	}
	// 	$this->db->where('trans_sales.paid', 1);
	// 	$this->db->where('trans_sales.inactive', 0);
	// 	$this->db->where('trans_sales.trans_ref is not null');
	// 	$this->db->where('trans_sales.void_ref is null');
	// 	$this->db->group_by('menu_categories.menu_cat_name, trans_sales.branch_code');		
	// 	$q = $this->db->get();		

	// 	return $q->result();
	// }
	public function get_category_sales($branch_code="", $month="", $year=""){
		$this->db->select('branch_code, total_sales as sales',false);
		$this->db->from('store_zread');
		// $this->db->join('(select * from trans_sales_menus group by branch_code,sales_menu_id)trans_sales_menus', 'trans_sales.sales_id = trans_sales_menus.sales_id AND trans_sales.branch_code =  trans_sales_menus.branch_code','left');
		// $this->db->join('menus', 'menus.menu_id = trans_sales_menus.menu_id AND menus.branch_code = trans_sales_menus.branch_code','left');
		// $this->db->join('menu_categories', 'menu_categories.menu_cat_id = menus.menu_cat_id AND menu_categories.branch_code = menus.branch_code','left');
		
		if(!empty($branch_code))
		{
			$this->db->where('branch_code', $branch_code);			
		}
		if(!empty($month))
		{
			$this->db->where('MONTH(trans_date) = '.$month);			
			$this->db->where('YEAR(trans_date) = '.$year);			
		}
		// $this->db->where('trans_sales.paid', 1);
		// $this->db->where('trans_sales.inactive', 0);
		// $this->db->where('trans_sales.trans_ref is not null');
		// $this->db->where('trans_sales.void_ref is null');
		$this->db->group_by('branch_code');		
		$q = $this->db->get();		

		return $q->result();
	}

	public function get_details($where,$table,$group_by = array()){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from($table);
			if($where)
				$this->db->where($where);

			if($group_by)
				$this->db->group_by($group_by);

			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_tbl_batch_ignore($table_name,$items){
		$this->db->insert_ignore_batch($table_name,$items);
		return $this->db->insert_id();
	}
}
?>