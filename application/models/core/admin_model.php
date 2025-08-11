<?php
class Admin_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	public function get_user_roles($id=null,$exclude_admin=true){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('user_roles');
			if($id != null){
				$this->db->where('user_roles.id',$id);
			}
			if($exclude_admin)
				$this->db->where('user_roles.id !=', 1);
			$this->db->order_by('id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_user_roles($items){
		$this->db->insert('user_roles',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_user_roles($user,$id){
		$this->db->where('id', $id);
		$this->db->update('user_roles', $user);

		return $this->db->last_query();
	}

	public function restart(){
		$this->db->trans_start();
			$this->db->query('truncate table `cashout_details`');
			$this->db->query('truncate table `cashout_entries`');
			$this->db->query('truncate table `ci_sessions`');
			$this->db->query('truncate table `item_moves`');
			$this->db->query('truncate table `menu_moves`');
			$this->db->query('truncate table `logs`');
			$this->db->query('truncate table `read_details`');
			$this->db->query('truncate table `reasons`');
			$this->db->query('truncate table `rob_files`');
			$this->db->query('truncate table `shift_entries`');
			$this->db->query('truncate table `shifts`');
			$this->db->query('truncate table `trans_adjustment_details`');
			$this->db->query('truncate table `trans_adjustments`');
			$this->db->query('truncate table `trans_receiving_details`');
			$this->db->query('truncate table `trans_receivings`');
			$this->db->query('truncate table `trans_spoilage_details`');
			$this->db->query('truncate table `trans_spoilage`');
			$this->db->query('truncate table `trans_receiving_menu_details`');
			$this->db->query('truncate table `trans_receiving_menu`');
			$this->db->query('truncate table `trans_adjustment_menu_details`');
			$this->db->query('truncate table `trans_adjustment_menu`');
			$this->db->query('truncate table `trans_voids`');
			$this->db->query('truncate table `trans_refs`');
			$this->db->query('truncate table `trans_sales`');
			$this->db->query('truncate table `trans_sales_charges`');
			$this->db->query('truncate table `trans_sales_discounts`');
			$this->db->query('truncate table `trans_sales_items`');
			$this->db->query('truncate table `trans_sales_menu_modifiers`');
			$this->db->query('truncate table `trans_sales_menu_submodifiers`');
			$this->db->query('truncate table `trans_sales_menus`');
			$this->db->query('truncate table `trans_sales_no_tax`');
			$this->db->query('truncate table `trans_sales_payments`');
			$this->db->query('truncate table `trans_sales_tax`');
			$this->db->query('truncate table `trans_sales_zero_rated`');
			$this->db->query('truncate table `trans_sales_local_tax`');
			$this->db->query('truncate table `trans_sales_loyalty_points`');
			$this->db->query('truncate table `ortigas_read_details`');
			$this->db->query('truncate table `customers_bank`');
			$this->db->query('truncate table `sync_logs`');
			$this->db->query('truncate table `master_logs`');

			$this->db->query('truncate table `trans_gc`');
			$this->db->query('truncate table `trans_gc_charges`');
			$this->db->query('truncate table `trans_gc_discounts`');
			$this->db->query('truncate table `trans_gc_gift_cards`');
			$this->db->query('truncate table `trans_gc_local_tax`');
			$this->db->query('truncate table `trans_gc_loyalty_points`');
			$this->db->query('truncate table `trans_gc_no_tax`');
			$this->db->query('truncate table `trans_gc_payments`');
			$this->db->query('truncate table `trans_gc_tax`');
			$this->db->query('truncate table `trans_gc_zero_rated`');

			$this->db->query('truncate table `gift_cards`');

			$this->db->query('truncate table `store_zread`');

			$this->db->query('truncate table `latest_logs`');

			$this->db->query('truncate table `collections`');

			$this->db->query("UPDATE trans_types set next_ref = '00000001' WHERE type_id = 10");
			$this->db->query("UPDATE trans_types set next_ref = 'R000001' WHERE type_id = 20");
			$this->db->query("UPDATE trans_types set next_ref = 'A000001' WHERE type_id = 30");
			$this->db->query("UPDATE trans_types set next_ref = 'V000001' WHERE type_id = 11");
			$this->db->query("UPDATE trans_types set next_ref = 'C000001' WHERE type_id = 40");
			$this->db->query("UPDATE trans_types set next_ref = 'S000001' WHERE type_id = 35");

			$this->db->query("UPDATE trans_types set next_ref = 'GC00000001' WHERE type_id = 12");

			$this->db->query("UPDATE trans_types set next_ref = 'STR00000001' WHERE type_id = 60");
			$this->db->query("UPDATE trans_types set next_ref = 'EXP00000001' WHERE type_id = 65");

		$this->db->trans_complete();
	}

	public function master_restart(){

		$this->load->library('Db_manager');
		$this->master_db = $this->db_manager->get_connection('master');

		$this->master_db->trans_start();
			$this->master_db->query('truncate table `cashout_details`');
			$this->master_db->query('truncate table `cashout_entries`');
			$this->master_db->query('truncate table `ci_sessions`');
			$this->master_db->query('truncate table `item_moves`');
			$this->master_db->query('truncate table `logs`');
			$this->master_db->query('truncate table `read_details`');
			$this->master_db->query('truncate table `reasons`');
			$this->master_db->query('truncate table `rob_files`');
			$this->master_db->query('truncate table `shift_entries`');
			$this->master_db->query('truncate table `shifts`');
			$this->master_db->query('truncate table `trans_adjustment_details`');
			$this->master_db->query('truncate table `trans_adjustments`');
			$this->master_db->query('truncate table `trans_receiving_details`');
			$this->master_db->query('truncate table `trans_receivings`');
			$this->master_db->query('truncate table `trans_spoilage_details`');
			$this->master_db->query('truncate table `trans_spoilage`');
			$this->master_db->query('truncate table `trans_voids`');
			$this->master_db->query('truncate table `trans_refs`');
			$this->master_db->query('truncate table `trans_sales`');
			$this->master_db->query('truncate table `trans_sales_charges`');
			$this->master_db->query('truncate table `trans_sales_discounts`');
			$this->master_db->query('truncate table `trans_sales_items`');
			$this->master_db->query('truncate table `trans_sales_menu_modifiers`');
			$this->master_db->query('truncate table `trans_sales_menus`');
			$this->master_db->query('truncate table `trans_sales_no_tax`');
			$this->master_db->query('truncate table `trans_sales_payments`');
			$this->master_db->query('truncate table `trans_sales_tax`');
			$this->master_db->query('truncate table `trans_sales_zero_rated`');
			$this->master_db->query('truncate table `trans_sales_local_tax`');
			$this->master_db->query('truncate table `trans_sales_loyalty_points`');
			$this->master_db->query('truncate table `ortigas_read_details`');
			$this->master_db->query('truncate table `customers_bank`');
			// $this->master_db->query('truncate table `sync_logs`');
			$this->master_db->query('truncate table `master_logs`');

			$this->master_db->query('truncate table `trans_gc`');
			$this->master_db->query('truncate table `trans_gc_charges`');
			$this->master_db->query('truncate table `trans_gc_discounts`');
			$this->master_db->query('truncate table `trans_gc_gift_cards`');
			$this->master_db->query('truncate table `trans_gc_local_tax`');
			$this->master_db->query('truncate table `trans_gc_loyalty_points`');
			$this->master_db->query('truncate table `trans_gc_no_tax`');
			$this->master_db->query('truncate table `trans_gc_payments`');
			$this->master_db->query('truncate table `trans_gc_tax`');
			$this->master_db->query('truncate table `trans_gc_zero_rated`');

			$this->master_db->query('truncate table `gift_cards`');

			$this->master_db->query('truncate table `store_zread`');

			$this->master_db->query('truncate table `latest_logs`');

			$this->master_db->query("UPDATE trans_types set next_ref = '00000001' WHERE type_id = 10");
			$this->master_db->query("UPDATE trans_types set next_ref = 'R000001' WHERE type_id = 20");
			$this->master_db->query("UPDATE trans_types set next_ref = 'A000001' WHERE type_id = 30");
			$this->master_db->query("UPDATE trans_types set next_ref = 'V000001' WHERE type_id = 11");
			$this->master_db->query("UPDATE trans_types set next_ref = 'C000001' WHERE type_id = 40");
			$this->master_db->query("UPDATE trans_types set next_ref = 'S000001' WHERE type_id = 35");
		$this->db->trans_complete();
	}

	function download_trans_for_pos($tables,$branch,$terminal_id=null){
		// $this->load->library('Db_manager');
		// $this->main_db = $this->db_manager->get_connection(MIGRATED_MAIN_DB);
		$user = $this->session->userdata('user');
		$user_id = $user['id'];

		$type="add";

		$return = '';

		$counter = 0;

		$main = false;
		if($terminal_id==null){
			$main = true;
		}

		foreach ($tables as $table) {
			$select = $this->trans_sales_fields($table,$main);

			if($terminal_id == null){
				// $select = '*';
				// if($table == 'menus' || $table == 'menu_categories' || $table == 'menu_modifiers' || $table == 'menu_prices' || $table == 'menu_recipe' || $table == 'menu_schedules' || $table == 'menu_subcategories' || $table == 'menu_subcategory' || $table == 'modifier_group_details' || $table == 'modifier_groups' || $table == 'modifier_prices' || $table == 'modifier_recipe' || $table == 'modifier_sub' || $table == 'modifier_sub_prices' || $table == 'modifiers' || $table == 'items' || $table == 'categories' || $table == 'subcategories' || $table == 'users' || $table == 'receipt_discounts' || $table == 'charges' || $table == 'brands' || $table == 'transaction_types' || $table == 'payment_types' || $table == 'payment_type_fields' || $table == 'payment_group'){
				// 	$query = $this->db->select($select)->where('branch_code = "'.$branch.'"')->get($table);
				// 	$result = $query->result_array();
				// // }else if($table == 'trans_sales_menu_submodifiers'){
				// // 	$query = $this->db->select($select)->where('terminal_id = '.$terminal_id.' and branch_code = "'.$branch.'"')->get($table);
				// // 	$result = $query->result_array();
				// }else{
					$query = $this->db->select($select)->where('branch_code = "'.$branch.'"')->get($table);
					$result = $query->result_array();
				// }
			}else{
				// $select = '*';
				if($table == 'menus' || $table == 'menu_categories' || $table == 'menu_modifiers' || $table == 'menu_prices' || $table == 'menu_recipe' || $table == 'menu_schedules' || $table == 'menu_subcategories' || $table == 'menu_subcategory' || $table == 'modifier_group_details' || $table == 'modifier_groups' || $table == 'modifier_prices' || $table == 'modifier_recipe' || $table == 'modifier_sub' || $table == 'modifier_sub_prices' || $table == 'modifiers' || $table == 'items' || $table == 'categories' || $table == 'subcategories' || $table == 'users' || $table == 'receipt_discounts' || $table == 'charges' || $table == 'brands' || $table == 'transaction_types' || $table == 'payment_types' || $table == 'payment_type_fields' || $table == 'payment_group'){
					$query = $this->db->select($select)->where('branch_code = "'.$branch.'"')->get($table);
					$result = $query->result_array();
				// }else if($table == 'trans_sales_menu_submodifiers'){
				// 	$query = $this->db->select($select)->where('terminal_id = '.$terminal_id.' and branch_code = "'.$branch.'"')->get($table);
				// 	$result = $query->result_array();
				}else{
					$query = $this->db->select($select)->where('pos_id = '.$terminal_id.' and branch_code = "'.$branch.'"')->get($table);
					$result = $query->result_array();
				}
			}


			// echo $this->db->last_query(); die();

			// echo "<pre>",print_r($result),"</pre>";die();
			$num_fields = $query->num_fields();

			// if($select == '*'){
			$fields = $this->db->list_fields($table);
			$fields = explode(',', $select);


			// $selected_field = $this->json_master_logs($table);
				// $trans_id_raw = $this->db->select('sales_id,sync_id')->get_where($table,array('terminal_id' => 1))->result();
				// echo "<pre>",print_r($trans_id_raw),"</pre>";die();
				// $json_encode  = json_encode($trans_id_raw);
			// }else{

			// 	// $trans_id_raw = $this->object_flat($result,"sales_id");
			// 	// print_r((object) $result);exit;
			// 	// $trans_id_raw = (object) $result;
			// 	$json_encode  = json_encode(array('sales_id'=>$this->array_flat($query->result(),"sales_id")));
			// }

			// $fields[] = '';
			
			// $migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>''),NULL,'main_db');

			// print_r($fields);exit;
			$new_fields = array();
			foreach ($fields as $field)
			{
				$new_fields[] = '`'.$field.'`';

				if($counter % 100 == 0){
                    set_time_limit(60);  
                }

                $counter++;
			}




			foreach($result as $row){
				$new_row = array();
				// print_r($row[$fields[0]]);exit;
				$return .= 'INSERT INTO '.$table.' (' . implode(',', $new_fields) . ') ';

				foreach ($fields as $field)
				{
					// if($field == 'master_id'){
					// 	$new_row[] = '"'.$migration_id.'"';
					// }elseif($field == 'branch_code'){
					// 	$new_row[] = '"'.BRANCH_CODE.'"';
					// }else{
						$new_row[] = '"'.$row[$field].'"';
					// }
					
				}				

				$return .= 'VALUES(' . implode(',', $new_row) . ');';
				
				$return .= "\r\n";

				if($counter % 100 == 0){
                    set_time_limit(60);  
                }

                $counter++;
			}

			$return .= "\r\n";




			// $this->update_tbl($table,array('master_id'=>null),array("master_id"=>$migration_id),NULL,NULL,'main_db');
			
		}

			// echo "<pre>",print_r($return),"</pre>";die();

		return $return;
	}

	public function trans_sales_fields($table,$main=true){
		switch ($table) {
			case 'trans_sales':
				if($main){
					$fields = 'sales_id,mobile_sales_id,type_id,trans_ref,void_ref,type,user_id,shift_id,customer_id,total_amount,total_paid,memo,table_id,guest,datetime,update_date,paid,reason,void_user_id,printed,inactive,waiter_id,split,serve_no,billed,sync_id,terminal_id,master_id,pos_id';
				}else{
					$fields = 'sales_id,mobile_sales_id,type_id,trans_ref,void_ref,type,user_id,shift_id,customer_id,total_amount,total_paid,memo,table_id,guest,datetime,update_date,paid,reason,void_user_id,printed,inactive,waiter_id,split,serve_no,billed,sync_id,terminal_id,master_id';
				}
				break;

			case 'trans_sales_charges':
				if($main){
					$fields = 'sales_charge_id,sales_id,charge_id,charge_code,charge_name,rate,absolute,amount,sync_id,datetime,pos_id,master_id';
				}else{
					$fields = 'sales_charge_id,sales_id,charge_id,charge_code,charge_name,rate,absolute,amount,sync_id,datetime';
				}
				break;

			case 'trans_sales_discounts':
				if($main){
					$fields = 'sales_disc_id,sales_id,disc_id,type,disc_code,disc_rate,name,bday,code,guest,items,amount,no_tax,sync_id,datetime,remarks,master_id,pos_id';
				}else{
					$fields = 'sales_disc_id,sales_id,disc_id,type,disc_code,disc_rate,name,bday,code,guest,items,amount,no_tax,sync_id,datetime,remarks';
				}
				break;

			case 'trans_sales_items':
				if($main){
					$fields = 'sales_item_id,sales_id,line_id,item_id,price,qty,discount,no_tax,remarks,sync_id,datetime,nocharge,master_id,pos_id';
				}else{
					$fields = 'sales_item_id,sales_id,line_id,item_id,price,qty,discount,no_tax,remarks,sync_id,datetime,nocharge';
				}
				break;

			case 'trans_sales_local_tax':
				if($main){
					$fields = 'sales_local_tax_id,sales_id,amount,sync_id,datetime,master_id,pos_id';
				}else{
					$fields = 'sales_local_tax_id,sales_id,amount,sync_id,datetime';
				}
				break;

			case 'trans_sales_loyalty_points':
				if($main){
					$fields = 'loyalty_point_id,sales_id,card_id,code,cust_id,amount,points,sync_id,datetime,master_id,pos_id';
				}else{
					$fields = 'loyalty_point_id,sales_id,card_id,code,cust_id,amount,points,sync_id,datetime';
				}
				break;

			case 'trans_sales_menus':
				if($main){
					$fields = 'sales_menu_id,sales_id,line_id,menu_id,price,qty,discount,no_tax,remarks,kitchen_slip_printed,free_user_id,sync_id,datetime,nocharge,menu_name,free_reason,is_takeout,is_checked,ref_line_id,is_promo,promo_type,free_promo_amount,menu_category_id,pf_id,master_id,pos_id';
				}else{
					$fields = 'sales_menu_id,sales_id,line_id,menu_id,price,qty,discount,no_tax,remarks,kitchen_slip_printed,free_user_id,sync_id,datetime,nocharge,menu_name,free_reason,is_takeout,is_checked,ref_line_id,is_promo,promo_type,free_promo_amount,menu_category_id,pf_id';
				}
				break;

			case 'trans_sales_menu_submodifiers':
				if($main){
					$fields = 'sales_submod_id,sales_id,line_id,mod_id,price,qty,kitchen_slip_printed,sync_id,datetime,submod_name,mod_line_id,master_id,pos_id';
				}else{
					$fields = 'sales_submod_id,sales_id,line_id,mod_id,price,qty,kitchen_slip_printed,sync_id,datetime,submod_name,mod_line_id';
				}
				break;

			case 'trans_sales_menu_modifiers':
				if($main){
					$fields = 'sales_mod_id,sales_id,line_id,menu_id,mod_group_id,mod_id,price,qty,discount,kitchen_slip_printed,sync_id,datetime,menu_name,mod_group_name,mod_name,mod_line_id,master_id,pos_id';
				}else{
					$fields = 'sales_mod_id,sales_id,line_id,menu_id,mod_group_id,mod_id,price,qty,discount,kitchen_slip_printed,sync_id,datetime,menu_name,mod_group_name,mod_name,mod_line_id';
				}
				break;

			case 'trans_sales_no_tax':
				if($main){
					$fields = 'sales_no_tax_id,sales_id,amount,sync_id,datetime,master_id,pos_id';
				}else{
					$fields = 'sales_no_tax_id,sales_id,amount,sync_id,datetime';
				}
				break;

			case 'trans_sales_payments':
				if($main){
					$fields = 'payment_id,sales_id,payment_type,amount,to_pay,reference,card_type,card_number,approval_code,user_id,sync_id,datetime,master_id,pos_id';
				}else{
					$fields = 'payment_id,sales_id,payment_type,amount,to_pay,reference,card_type,card_number,approval_code,user_id,sync_id,datetime';
				}
				break;

			case 'trans_sales_tax':
				if($main){
					$fields = 'sales_tax_id,sales_id,name,rate,amount,sync_id,datetime,master_id,pos_id';
				}else{
					$fields = 'sales_tax_id,sales_id,name,rate,amount,sync_id,datetime';
				}
				break;

			case 'trans_sales_zero_rated':
				if($main){
					$fields = 'sales_zero_rated_id,sales_id,amount,sync_id,datetime,name,card_no,master_id,pos_id';
				}else{
					$fields = 'sales_zero_rated_id,sales_id,amount,sync_id,datetime,name,card_no';
				}
				break;

			case 'menus':
				if($main){
					$fields = 'menu_id,menu_code,menu_barcode,menu_name,menu_short_desc,menu_cat_id,menu_sub_cat_id,menu_sched_id,cost,reg_date,update_date,no_tax,free,inactive,costing,menu_sub_id,miaa_cat,reorder_qty,brand,unavailable,menu_qty,alcohol,master_id';
				}else{
					$fields = 'menu_id,menu_code,menu_barcode,menu_name,menu_short_desc,menu_cat_id,menu_sub_cat_id,menu_sched_id,cost,reg_date,update_date,no_tax,free,inactive,costing,menu_sub_id,miaa_cat,reorder_qty,brand,unavailable,menu_qty,alcohol,master_id';
				}
				break;

			case 'menu_categories':
				if($main){
					$fields = 'menu_cat_id,menu_cat_name,menu_sched_id,reg_date,inactive,arrangement,brand,unli,master_id';
				}else{
					$fields = 'menu_cat_id,menu_cat_name,menu_sched_id,reg_date,inactive,arrangement,brand,unli,master_id';
				}
				break;

			case 'menu_modifiers':
				if($main){
					$fields = 'id,menu_id,mod_group_id,master_id';
				}else{
					$fields = 'id,menu_id,mod_group_id,master_id';
				}
				break;

			case 'menu_prices':
				if($main){
					$fields = 'id,menu_id,trans_type,price,sync_id,datetime,master_id';
				}else{
					$fields = 'id,menu_id,trans_type,price,sync_id,datetime,master_id';
				}
				break;

			case 'menu_recipe':
				if($main){
					$fields = 'recipe_id,menu_id,item_id,uom,qty,cost,master_id';
				}else{
					$fields = 'recipe_id,menu_id,item_id,uom,qty,cost,master_id';
				}
				break;

			case 'menu_schedules':
				if($main){
					$fields = 'menu_sched_id,desc,inactive,master_id';
				}else{
					$fields = 'menu_sched_id,desc,inactive,master_id';
				}
				break;

			case 'menu_subcategories':
				if($main){
					$fields = 'menu_sub_cat_id,menu_sub_cat_name,reg_date,inactive,master_id';
				}else{
					$fields = 'menu_sub_cat_id,menu_sub_cat_name,reg_date,inactive,master_id';
				}
				break;

			case 'menu_subcategory':
				if($main){
					$fields = 'menu_sub_id,menu_sub_name,reg_date,inactive,category_id,master_id';
				}else{
					$fields = 'menu_sub_id,menu_sub_name,reg_date,inactive,category_id,master_id';
				}
				break;

			case 'modifier_group_details':
				if($main){
					$fields = 'id,mod_group_id,mod_id,default,master_id';
				}else{
					$fields = 'id,mod_group_id,mod_id,default,master_id';
				}
				break;

			case 'modifier_groups':
				if($main){
					$fields = 'mod_group_id,name,mandatory,multiple,inactive,min_no,master_id';
				}else{
					$fields = 'mod_group_id,name,mandatory,multiple,inactive,min_no,master_id';
				}
				break;

			case 'modifier_prices':
				if($main){
					$fields = 'id,mod_id,trans_type,price,sync_id,datetime,master_id';
				}else{
					$fields = 'id,mod_id,trans_type,price,sync_id,datetime,master_id';
				}
				break;

			case 'modifier_recipe':
				if($main){
					$fields = 'mod_recipe_id,mod_id,item_id,uom,qty,cost,master_id';
				}else{
					$fields = 'mod_recipe_id,mod_id,item_id,uom,qty,cost,master_id';
				}
				break;

			case 'modifier_sub':
				if($main){
					$fields = 'mod_sub_id,mod_id,name,cost,group,is_auto,datetime,master_id';
				}else{
					$fields = 'mod_sub_id,mod_id,name,cost,group,is_auto,datetime,master_id';
				}
				break;

			case 'modifier_sub_prices':
				if($main){
					$fields = 'id,mod_sub_id,trans_type,price,sync_id,datetime,master_id';
				}else{
					$fields = 'id,mod_sub_id,trans_type,price,sync_id,datetime,master_id';
				}
				break;

			case 'modifiers':
				if($main){
					$fields = 'mod_id,name,cost,has_recipe,reg_date,update_date,inactive,mod_sub_cat_id,mod_code,master_id';
				}else{
					$fields = 'mod_id,name,cost,has_recipe,reg_date,update_date,inactive,mod_sub_cat_id,mod_code,master_id';
				}
				break;

			case 'terminals':
				if($main){
					$fields = 'terminal_id,terminal_code,branch_code,terminal_name,comp_name,inactive,sync_id,master_id,datetime,permit,serial';
				}else{
					$fields = 'terminal_id,terminal_code,branch_code,terminal_name,comp_name,inactive,sync_id,datetime,permit,serial';
				}
				break;

			case 'items':
				if($main){
					$fields = 'item_id,barcode,code,name,desc,cat_id,subcat_id,supplier_id,uom,cost,type,no_per_pack,no_per_case,reorder_qty,max_qty,memo,reg_date,update_date,inactive,brand,costing,master_id';
				}else{
					$fields = 'item_id,barcode,code,name,desc,cat_id,subcat_id,supplier_id,uom,cost,type,no_per_pack,no_per_case,reorder_qty,max_qty,memo,reg_date,update_date,inactive,brand,costing';
				}
				break;

			case 'categories':
				if($main){
					$fields = 'cat_id,code,name,image,inactive,update_date,master_id';
				}else{
					$fields = 'cat_id,code,name,image,inactive';
				}
				break;

			case 'subcategories':
				if($main){
					$fields = 'sub_cat_id,cat_id,code,name,image,inactive,update_date,master_id';
				}else{
					$fields = 'sub_cat_id,cat_id,code,name,image,inactive';
				}
				break;

			case 'item_moves':
				if($main){
					$fields = 'move_id,type_id,type_id,trans_ref,loc_id,item_id,qty,uom,case_qty,pack_qty,curr_item_qty,reg_date,inactive,sync_id,master_id,datetime,cost,pos_id';
				}else{
					$fields = 'move_id,type_id,type_id,trans_ref,loc_id,item_id,qty,uom,case_qty,pack_qty,curr_item_qty,reg_date,inactive,sync_id,cost';
				}
				break;

			case 'trans_refs':
				if($main){
					$fields = 'id,type_id,trans_ref,user_id,inactive,sync_id,datetime,pos_id,master_id';
				}else{
					$fields = 'id,type_id,trans_ref,user_id,inactive,sync_id,datetime';
				}
				break;

			case 'users':
				if($main){
					$fields = 'id,username,password,pin,fname,mname,lname,role,gender,reg_date,inactive,sync_id,branch_code,datetime,master_id';
				}else{
					$fields = 'id,username,password,pin,fname,mname,lname,role,gender,reg_date,inactive,sync_id,datetime';
				}
				break;

			case 'receipt_discounts':
				if($main){
					$fields = 'disc_id,disc_code,disc_name,disc_rate,no_tax,fix,inactive,sync_id,datetime,master_id';
				}else{
					$fields = 'disc_id,disc_code,disc_name,disc_rate,no_tax,fix,inactive,sync_id,datetime';
				}
				break;

			case 'brands':
				if($main){
					$fields = 'id,brand_code,brand_name,inactive,sync_id,master_id,datetime';
				}else{
					$fields = 'id,brand_code,brand_name,inactive,sync_id,datetime';
				}
				break;

			case 'transaction_types':
				if($main){
					$fields = 'trans_id,trans_name,inactive,sync_id,master_id';
				}else{
					$fields = 'trans_id,trans_name,inactive';
				}
				break;

			case 'payment_types':
				if($main){
					$fields = 'payment_id,payment_code,description,payment_group_id,inactive,reg_date,master_id';
				}else{
					$fields = 'payment_id,payment_code,description,payment_group_id,inactive,reg_date';
				}
				break;

			case 'payment_type_fields':
				if($main){
					$fields = 'field_id,payment_id,field_name,inactive,master_id';
				}else{
					$fields = 'field_id,payment_id,field_name,inactive';
				}
				break;

			case 'payment_group':
				if($main){
					$fields = 'payment_group_id,code,description,inactive,reg_date,sync_id,master_id';
				}else{
					$fields = 'payment_group_id,code,description,inactive,reg_date,sync_id';
				}
				break;

			case 'charges':
				if($main){
					$fields = 'charge_id,charge_code,charge_name,charge_amount,absolute,no_tax,inactive,master_id';
				}else{
					$fields = 'charge_id,charge_code,charge_name,charge_amount,absolute,no_tax,inactive';
				}
				break;

			// case 'trans_gc':
			// 	$fields = 'gc_id,mobile_sales_id,type_id,trans_ref,void_ref,type,user_id,shift_id,customer_id,total_amount,total_paid,memo,table_id,guest,datetime,update_date,paid,reason,void_user_id,printed,inactive,waiter_id,split,serve_no,billed,sync_id,terminal_id,master_id';
			// 	break;
			
			default:
				// echo $table; die();
				$fields = '*';
				break;
		}
		

		return $fields;

	}

	function trans_json_file($tables,$branch,$terminal_id=null){
		$user = $this->session->userdata('user');
		$user_id = $user['id'];

		$type="add";

		$return = array();

		$counter = 0;

		$main = false;
		if($terminal_id==null){
			$main = true;
		}

		
		foreach ($tables as $table) {
			$select = $this->trans_sales_fields($table,$main);

			if($terminal_id == null){
				// $select = '*';
				// if($table == 'menus' || $table == 'menu_categories' || $table == 'menu_modifiers' || $table == 'menu_prices' || $table == 'menu_recipe' || $table == 'menu_schedules' || $table == 'menu_subcategories' || $table == 'menu_subcategory' || $table == 'modifier_group_details' || $table == 'modifier_groups' || $table == 'modifier_prices' || $table == 'modifier_recipe' || $table == 'modifier_sub' || $table == 'modifier_sub_prices' || $table == 'modifiers' || $table == 'items' || $table == 'categories' || $table == 'subcategories' || $table == 'users' || $table == 'receipt_discounts' || $table == 'charges' || $table == 'brands' || $table == 'transaction_types' || $table == 'payment_types' || $table == 'payment_type_fields' || $table == 'payment_group'){
				// 	$query = $this->db->select($select)->where('branch_code = "'.$branch.'"')->get($table);
				// 	$result = $query->result_array();
				// // }else if($table == 'trans_sales_menu_submodifiers'){
				// // 	$query = $this->db->select($select)->where('terminal_id = '.$terminal_id.' and branch_code = "'.$branch.'"')->get($table);
				// // 	$result = $query->result_array();
				// }else{
					$query = $this->db->select($select)->where('branch_code = "'.$branch.'"')->get($table);
					$result = $query->result_array();
				// }
			}else{
				// $select = '*';
				if($table == 'menus' || $table == 'menu_categories' || $table == 'menu_modifiers' || $table == 'menu_prices' || $table == 'menu_recipe' || $table == 'menu_schedules' || $table == 'menu_subcategories' || $table == 'menu_subcategory' || $table == 'modifier_group_details' || $table == 'modifier_groups' || $table == 'modifier_prices' || $table == 'modifier_recipe' || $table == 'modifier_sub' || $table == 'modifier_sub_prices' || $table == 'modifiers' || $table == 'items' || $table == 'categories' || $table == 'subcategories' || $table == 'users' || $table == 'receipt_discounts' || $table == 'charges' || $table == 'brands' || $table == 'transaction_types' || $table == 'payment_types' || $table == 'payment_type_fields' || $table == 'payment_group'){
					$query = $this->db->select($select)->where('branch_code = "'.$branch.'"')->get($table);
					$result = $query->result_array();
				// }else if($table == 'trans_sales_menu_submodifiers'){
				// 	$query = $this->db->select($select)->where('terminal_id = '.$terminal_id.' and branch_code = "'.$branch.'"')->get($table);
				// 	$result = $query->result_array();
				}else{
					$query = $this->db->select($select)->where('pos_id = '.$terminal_id.' and branch_code = "'.$branch.'"')->get($table);
					$result = $query->result_array();
				}
			}


			
			$return[$table] = $result;
		}

			// echo "<pre>",print_r($return),"</pre>";die();

		return $return;
	}

	function set_temp_trans_sales($branch_code='',$start_date='',$to_date=''){
		$user = $this->session->userdata('user');
		$details = $this->setup_model->get_branch_details($branch_code);

		$start_date = date('Y-m-d H:i:s',strtotime($start_date . "-1 days"));
		$to_date = date('Y-m-d H:i:s',strtotime($to_date . "+1 days"));
        
		$user_id = $user['id'];
		$session_id = $user['id'] .'_'. $details[0]->branch_id;
		$user['sess_id'] = $session_id;
    
		$this->session->set_userdata('user',$user);

        $this->db->trans_start();
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_charges`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_discounts`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_items`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_menu_modifiers`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_menu_submodifiers`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_menus`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_no_tax`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_payments`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_tax`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_zero_rated`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_local_tax`');
			$this->db->query('drop temporary table if exists `trans_sales'.$session_id.'_loyalty_points`');
			$this->db->query('drop temporary table if exists `reasons'.$session_id.'`');

			
		$this->db->trans_complete();
        // echo $start_date.'---'.$to_date;die();
		$ts = '(select concat(sales_id,"-",pos_id) sales_id from trans_sales use index(branch_code) where branch_code="'.$branch_code.'" && datetime >= "'. $start_date .'" && datetime <= "'.$to_date.'")';
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.' (INDEX branch_code (branch_code)) select * from trans_sales use index(branch_code) where branch_code="'.$branch_code.'" && datetime >= "'. $start_date .'" && datetime <= "'.$to_date.'" ');
// 			echo $this->db->last_query();die(); 
// 			echo "<pre>",print_r($session_id),"</pre>";die();
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_charges (INDEX branch_code (branch_code)) select * from trans_sales_charges use index(branch_code) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_discounts (INDEX branch_code (branch_code)) select * from trans_sales_discounts use index(branch_code) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_items (INDEX branch_code (branch_code)) select * from trans_sales_items use index(branch_code) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_menu_modifiers (INDEX branch_code (branch_code)) select * from trans_sales_menu_modifiers use index(branch_code) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_menu_submodifiers (INDEX branch_code (branch_code)) select * from trans_sales_menu_submodifiers use index(sales_id) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_menus (INDEX branch_code (branch_code)) select * from trans_sales_menus use index(branch_code) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_no_tax  (INDEX branch_code (branch_code)) select * from trans_sales_no_tax use index(branch_code) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_payments (INDEX branch_code (branch_code)) select * from trans_sales_payments use index(branch_code) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_tax (INDEX branch_code (branch_code)) select * from trans_sales_tax use index(branch_code) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_zero_rated (INDEX branch_code (branch_code)) select * from trans_sales_zero_rated use index(branch_code) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_local_tax (INDEX branch_code (branch_code)) select * from trans_sales_local_tax use index(sales_id) where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE trans_sales'.$session_id.'_loyalty_points (INDEX branch_code (branch_code)) select * from trans_sales_loyalty_points where branch_code = "'.$branch_code.'" && concat(sales_id,"-",pos_id) in '.$ts);
			$this->db->query('CREATE TEMPORARY TABLE reasons'.$session_id.' (INDEX branch_code (branch_code)) select * from reasons use index(branch_code) where branch_code = "'.$branch_code.'" && concat(trans_id,"-",pos_id) in '.$ts);
			
			// echo 'CREATE TEMPORARY TABLE trans_sales'.$session_id.'_menu_modifiers select * from trans_sales_menu_modifiers where branch_code = "'.$branch_code.'" && sales_id in '.$ts;
		// echo 'trans_sales'.$session_id.'_menus';
		// $r = $this->db->query('select * from  trans_sales1_menus')->result();
		// print_r($r);
    }
}
?>