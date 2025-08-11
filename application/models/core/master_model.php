<?php
/**
add and update functions were separated from each of the tables to migrate for easier maintenance and understanding. @Justin 11/2017
**/

class Master_model extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->library('Db_manager');
		$this->main_db = $this->db_manager->get_connection(MIGRATED_MAIN_DB);
		$this->migrate_db = $this->db_manager->get_connection(MIGRATED_MASTER_DB);
		$this->db = $this->db_manager->get_connection('default');
		// echo "<pre>",print_r($this->migrate_db->select('*')->where('menu_id','1')->get('menus')->result()),"</pre>";
		// echo MIGRATED_MASTER_DB; 
		// var_dump($this->migrate_db);die();
	}
	public function add_tbl($table_name,$items,$set=array(),$db="migrate_db"){
		if(!empty($set)){
			foreach ($set as $key => $val) {
				$this->$db->set($key, $val, FALSE);
			}
		}
		$this->$db->insert($table_name,$items);
		return $this->$db->insert_id();
	}

	public function add_tbl_batch($table_name,$items,$db='migrate_db'){
		$this->$db->insert_batch($table_name,$items);
		return $this->$db->insert_id();
	}

	public function update_tbl($table_name,$table_key,$items,$id=null,$set=array(),$db="migrate_db",$or = false){
		if(is_array($table_key)){
			foreach ($table_key as $key => $val) {
				if($or){

					if(is_array($val)){
						$this->$db->or_where_in($key,$val);
					}
					else
						$this->$db->or_where($key,$val,FALSE);
				}else{

					if(is_array($val)){
						$this->$db->where_in($key,$val);
					}
					else
						$this->$db->where($key,$val,FALSE);
				}
			}
		}
		else{
			if(is_array($id)){
				$this->$db->where_in($table_key,$id);
			}
			else
				$this->$db->where($table_key,$id);
		}
		if(!empty($set)){
			foreach ($set as $key => $val) {
				$this->$db->set($key, $val, FALSE);
			}
		}
		$this->$db->update($table_name,$items);
		return $this->$db->last_query();
	}

	public function update_tbl_batch($table_name,$items,$key,$db="migrate_db"){

		$this->$db->update_batch($table_name,$items,$key);
			
		return $this->$db->affected_rows();
	}


	public function update_trans_tbl_batch($table_name,$items,$key,$db="migrate_db"){
		// echo $table_name;
		// echo "<pre>",print_r($items),"</pre>";
		// echo "<br>";
		// echo $key;
		// die();
		// $this->$db->where('BRANCH_CODE',$name);
		$branch_code = BRANCH_CODE;
		$terminal_id = TERMINAL_ID;
		if( !empty($branch_code) && !empty($terminal_id)) {
			$this->$db->where('branch_code',BRANCH_CODE);
			$this->$db->where('terminal_id',TERMINAL_ID);
			$this->$db->update_batch($table_name,$items,$key);
			
		}
		// echo $this->$db->last_query();die();
		return $this->$db->affected_rows();
	}

	public function delete_tbl_batch($table_name=null,$args=null,$db="migrate_db"){
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->$db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->$db->$func($col,$val['val']);
					}
				}
				else
					$this->$db->where($col,$val);
			}
		}
		$this->$db->delete($table_name);
	}

	public function delete_trans_misc_batch($table_name=null,$args=null,$db="migrate_db"){
		$branch_code = BRANCH_CODE;
		$terminal_id = TERMINAL_ID;
		 // $data = $rec->src_id;
        // $args_decoded = json_decode($args,false);
        // $args = $this->formulate_object($args_decoded,array(),true,false);
		// echo "<pre>",print_r($args),"</pre>";
		if( !empty($branch_code) && !empty($terminal_id)) {
			if(!empty($args)){

				// foreach($args as $a){

					foreach ($args as $col => $val) {
						// foreach($val)
						if(is_array($val)){
							if(!isset($val['use'])){
								$this->$db->where_in($col,$val);
							}
							else{
								$func = $val['use'];
								$this->$db->$func($col,$val['val']);
							}
						}
						else
							$this->$db->where($col,$val);

					
					}
				// }
				// search for specific branch_code and terminal_id
				$this->$db->where('branch_code',BRANCH_CODE);
				$this->$db->where('terminal_id',TERMINAL_ID);
				$this->$db->delete($table_name);
				// echo $this->$db->last_query();die();
				
			}


		}
	}

	public function delete_tbl($table_name=null,$args=null,$db="migrate_db"){
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->$db->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->$db->$func($col,$val['val']);
					}
				}
				else
					$this->$db->where($col,$val);
			}
		}
		$this->$db->delete($table_name);
	}

	public function formulate_object($object=NULL,$add_element=array(),$multi=false,$return_obj = true){
			$new_array = json_decode(json_encode($object), true);

			foreach($add_element as $key => $value){
				if($multi){
					foreach($new_array as $obj => &$elm){

						$new_array[$obj][$key] = $value;
					}
				}else{
					$new_array[$key] = $value;

				}
			}

			if($return_obj){
				return json_decode(json_encode($new_array));
				
			}else{
				return $new_array;
			}

	}





	// TO MASTER DB

	// migrate add trans sales  @Justin
	public function add_trans_sales($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		// if(!empty($trans_id)){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
			$type="add";
			$table_name = 'trans_sales';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('`sales_id` , `mobile_sales_id` ,`type_id` ,`trans_ref` ,`void_ref` ,`type` ,`user_id` ,`shift_id` ,
							`terminal_id` ,`customer_id` ,`total_amount` ,`total_paid` ,`memo` ,`table_id` ,`guest` ,`datetime`,
							`update_date` ,`paid` ,`reason` ,`void_user_id` ,`printed` ,`inactive` ,`waiter_id` ,`split` ,
							`serve_no` ,`billed`, `sync_id`,`terminal_id`,`branch_code`')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select('sales_id')->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('`sales_id` , `mobile_sales_id` ,`type_id` ,`trans_ref` ,`void_ref` ,`type` ,`user_id` ,`shift_id` ,
							`terminal_id` ,`customer_id` ,`total_amount` ,`total_paid` ,`memo` ,`table_id` ,`guest` ,`datetime`,
							`update_date` ,`paid` ,`reason` ,`void_user_id` ,`printed` ,`inactive` ,`waiter_id` ,`split` ,
							`serve_no` ,`billed` , `sync_id`,`terminal_id`,`branch_code`')->where('master_id',$migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('`sales_id` , `mobile_sales_id` ,`type_id` ,`trans_ref` ,`void_ref` ,`type` ,`user_id` ,`shift_id` ,
							`terminal_id` ,`customer_id` ,`total_amount` ,`total_paid` ,`memo` ,`table_id` ,`guest` ,`datetime`,
							`update_date` ,`paid` ,`reason` ,`void_user_id` ,`printed` ,`inactive` ,`waiter_id` ,`split` ,
							`serve_no` ,`billed` , `sync_id`,`terminal_id`,`branch_code`')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select('sales_id')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
		// }

	}


	// migrate add trans sales_charges  @Justin
	public function add_trans_sales_charges($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
			$type = 'add';
		    $table_name = 'trans_sales_charges';
		    $selected_field = 'sales_charge_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}


// migrate add trans sales_discounts  @Justin
	public function add_trans_sales_discounts($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_discounts';
		    $selected_field = 'sales_disc_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

	

		// migrate add trans sales_items  @Justin
	public function add_trans_sales_items($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_items';
		    $selected_field = 'sales_item_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id',$migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

		// migrate add trans sales local_tax  @Justin
	public function add_trans_sales_local_tax($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_local_tax';
		    $selected_field = 'sales_local_tax_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id',$migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}


	// migrate add trans sales loyalty points  @Justin
	public function add_trans_sales_loyalty_points($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_loyalty_points';
		    $selected_field = 'loyalty_point_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id',$migration_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

		// migrate add trans_sales_menu_modifiers  @Justin
	public function add_trans_sales_menu_modifiers($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_menu_modifiers';
		    $selected_field = 'sales_mod_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}


	// migrate add trans_sales_menu_modifiers  @Justin
	public function add_trans_sales_menus($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_menus';
		    $selected_field = 'sales_menu_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				if(count($trans_id_raw) > 10000) // records that exceeds 20000 clogs the migration , temporary solution @justinx02022018
						$json_encode  = "";// json_encode($trans_id_raw);
				else
					$json_encode =  json_encode($trans_id_raw);
						
			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					if(count($trans_id_raw) > 10000) // records that exceeds 20000 clogs the migration , temporary solution @justinx02022018
						$json_encode  = "";// json_encode($trans_id_raw);
					else
					$json_encode =  json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

		// migrate add trans_sales_no_tax  @Justin
	public function add_trans_sales_no_tax($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_no_tax';
		    $selected_field = 'sales_no_tax_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

	// migrate add trans_sales_payments  @Justin
	public function add_trans_sales_payments($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_payments';
		    $selected_field = 'payment_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

	// migrate add trans_sales_tax  @Justin
	public function add_trans_sales_tax($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_tax';
		    $selected_field = 'sales_tax_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

	// migrate add trans_sales_zero_rated  @Justin
	public function add_trans_sales_zero_rated($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_zero_rated';
		    $selected_field = 'sales_zero_rated_id,sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id',$migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
	}


	// migrate add trans_sales_zero_rated  @Justin
	public function add_trans_refs($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_refs';
		    $selected_field = 'id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id',$migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					// echo $this->migrate_db->last_query();
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id ),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
	}


	// migrate update trans_sales  @Justin
	public function update_trans_sales($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales';
		    $selected_field = '`sales_id` , `mobile_sales_id` ,`type_id` ,`trans_ref` ,`void_ref` ,`type` ,`user_id` ,`shift_id` ,
							`terminal_id` ,`customer_id` ,`total_amount` ,`total_paid` ,`memo` ,`table_id` ,`guest` ,`datetime`,
							`update_date` ,`paid` ,`reason` ,`void_user_id` ,`printed` ,`inactive` ,`waiter_id` ,`split` ,
							`serve_no` ,`billed` , `sync_id`,`terminal_id`,`branch_code`';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select($selected_field)->where('master_id',$migrate_local_id)->get($table_name)->result();
			}else{
				$trans_raw = $this->main_db->select($selected_field)->where('`update_date` > ',$update_date)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('`update_date` > ',$update_date)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`update_date` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
								// echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					// $this->update_trans_tbl_batch($table_name,$add_trans_wrapped,'master_id','main_db');

				 	$this->update_trans_tbl_batch($table_name,$add_trans_wrapped,'sales_id');
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
	}

	// migrate update trans_sales_charges  -  @Justin
	public function update_trans_sales_charges($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_charges';
		    $selected_field = 'trans_sales_charges.sales_charge_id, trans_sales_charges.sales_id';
		    $based_delete_id = 'sales_id';
		    $based_delete_id_select = 'trans_sales.sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('trans_sales_charges.*')->join($table_name,'trans_sales.sales_id = trans_sales_charges.sales_id','left')->where('trans_sales_charges.master_id',$migrate_local_id)->where('trans_sales_charges.sales_id is not null',null,false)->get('trans_sales')->result();
				$trans_id_raw = $this->main_db->select($selected_field)->join($table_name,'trans_sales.sales_id = trans_sales_charges.sales_id','left')->where('trans_sales_charges.master_id', $migrate_local_id)->get('trans_sales')->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id_select)->join($table_name,'trans_sales.sales_id = trans_sales_charges.sales_id','left')->where('trans_sales_charges.master_id', $migrate_local_id)->group_by($based_delete_id)->get('trans_sales')->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('trans_sales_charges.*')->join($table_name,'trans_sales.sales_id = trans_sales_charges.sales_id','left')->where('`trans_sales.update_date` > ',$update_date)->where('trans_sales_charges.sales_id is not null',null,false)->get('trans_sales')->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->join($table_name,'trans_sales.sales_id = trans_sales_charges.sales_id','left')->where('`trans_sales.update_date` > ',$update_date)->get('trans_sales')->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id_select)->join($table_name,'trans_sales.sales_id = trans_sales_charges.sales_id','left')->where('`trans_sales.update_date` > ',$update_date)->group_by($based_delete_id)->get('trans_sales')->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl

			// echo $this->main_db->last_query();
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_based_id_raw) && isset($trans_based_id_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



// echo "<pre> trans based id raw:"  , print_r($add_trans_header),"</pre>"; die();
				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
					if(!empty($add_trans_header)){

				 		$this->add_tbl_batch($table_name,$add_trans_wrapped);
					}
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}


	// migrate update trans_sales_charges  -  @Justin
	public function update_trans_sales_menus($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'trans_sales_menus';
		    $selected_field = 'sales_menu_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
	}



// migrate update trans_sales_discounts  -  @Justin
	public function update_trans_sales_discounts($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_discounts';
		    $selected_field = 'trans_sales_discounts.sales_disc_id, trans_sales_discounts.sales_id';
		    $based_delete_id = 'sales_id';
		    $based_delete_id_select = 'trans_sales.sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('trans_sales_discounts.*')->join($table_name,'trans_sales.sales_id = trans_sales_discounts.sales_id','left')->where('trans_sales_discounts.master_id', $migrate_local_id)->where('trans_sales_discounts.sales_id is not null',null,false)->get('trans_sales')->result();
				$trans_id_raw = $this->main_db->select($selected_field)->join($table_name,'trans_sales.sales_id = trans_sales_discounts.sales_id','left')->where('trans_sales_discounts.master_id', $migrate_local_id=NULL)->get('trans_sales')->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id_select)->join($table_name,'trans_sales.sales_id = trans_sales_discounts.sales_id','left')->where('trans_sales_discounts.master_id', $migrate_local_id=NULL)->group_by($based_delete_id)->get('trans_sales')->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('trans_sales_discounts.*')->join($table_name,'trans_sales.sales_id = trans_sales_discounts.sales_id','left')->where('`trans_sales.update_date` > ',$update_date)->where('trans_sales_discounts.sales_id is not null',null,false)->get('trans_sales')->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->join($table_name,'trans_sales.sales_id = trans_sales_discounts.sales_id','left')->where('`trans_sales.update_date` > ',$update_date)->get('trans_sales')->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id_select)->join($table_name,'trans_sales.sales_id = trans_sales_discounts.sales_id','left')->where('`trans_sales.update_date` > ',$update_date)->group_by($based_delete_id)->get('trans_sales')->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_based_id_raw) && isset($trans_based_id_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	if(!empty($add_trans_header)){
				 		$this->add_tbl_batch($table_name,$add_trans_wrapped);
					}
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}


	// migrate update trans_sales_items  -  @Justin
	public function update_trans_sales_items($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_items';
		    $selected_field = 'sales_item_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);				

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}


	// migrate update trans_sales_local_tax  -  @Justin
	public function update_trans_sales_local_tax($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_local_tax';
		    $selected_field = 'sales_local_tax_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
			
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}


	// migrate update trans_sales_loyalty_points  -  @Justin
	public function update_trans_sales_loyalty_points($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_loyalty_points';
		    $selected_field = 'loyalty_point_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

	// migrate update trans_sales_menu_modifiers  -  @Justin
	public function update_trans_sales_menu_modifiers($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_menu_modifiers';
		    $selected_field = 'sales_mod_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

	// migrate update trans_sales_no_tax  -  @Justin
	public function update_trans_sales_no_tax($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_no_tax';
		    $selected_field = 'sales_no_tax_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

	// migrate update trans_sales_payments  -  @Justin
	public function update_trans_sales_payments($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_payments';
		    $selected_field = 'payment_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

// migrate update trans_sales_tax  -  @Justin
	public function update_trans_sales_tax($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_tax';
		    $selected_field = 'sales_tax_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}


// migrate update trans_sales_tax  -  @Justin
	public function update_trans_sales_zero_rated($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_sales_zero_rated';
		    $selected_field = 'sales_zero_rated_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
				
	}



// migrate update trans_voids  -  @Justin
	public function update_trans_voids($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		 $table_name = 'trans_voids';
		    $selected_field = 'sales_zero_rated_id, sales_id';
		    $based_delete_id = 'sales_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

		

			if($automated){
				$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_id_raw = $this->main_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('master_id', $migrate_local_id)->group_by($based_delete_id)->get($table_name)->result();
				$json_encode  = json_encode($trans_id_raw);
			}else{
				$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->get($table_name)->result();
			
				$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->get($table_name)->result();
				$trans_based_id_raw = $this->main_db->select($based_delete_id)->where('`datetime` > ',$update_date)->group_by($based_delete_id)->get($table_name)->result();
				// echo $this->main_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);
					
			}				
			
			// $this->tbl
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"update","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);
						// $this->update_tbl($table_name,array('`sales_id`  ' => "'".$update_date."'" ,'`branch_code`' => "'".BRANCH_CODE."'", '`terminal_id`' => TERMINAL_ID ),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}



				$this->migrate_db->trans_start(); // start the migration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
					
// echo "<pre> trans based id raw:"  , print_r($trans_based_id_raw),"</pre>"; //die();

				if(!empty($trans_based_id_raw)){
					$delete_args = array();
					foreach($trans_based_id_raw as $b){
						$delete_args[$based_delete_id][]  = $b->$based_delete_id ;
					}

					$this->delete_trans_misc_batch($table_name,$delete_args);
					// delete_trans_misc_batch($table_name=null,$args=null
				 	$this->add_tbl_batch($table_name,$add_trans_wrapped);
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');
				}

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}


	// download changes in menu  -  @Justin
	public function download_menus($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		    $table_name = 'menus';
		    $selected_field = 'menu_id,menu_code,menu_barcode,menu_name,menu_short_desc,
		    					menu_cat_id,menu_sub_cat_id,menu_sched_id,cost,reg_date, update_date,no_tax,
		    					free,inactive,costing';
		    $based_field = 'menu_id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			//check first if the menu on this terminal id and branch code already exist on master DB if not copy all menus and details
			// echo "<pre>",print_r($check_if_menu_exists),"</pre>";die();
			$check_if_menu_exists = $this->migrate_db->select($selected_field)->where('terminal_id',TERMINAL_ID)->where('branch_code',BRANCH_CODE)->get($table_name)->result();
		// echo count($check_if_menu_exists);die();
			if(count($check_if_menu_exists) <= 0){
				$this->upload_menus();
				$this->upload_menu_subcategories();
				$this->upload_menu_modifiers();
				// $this->upload_modifier_group_details();
				$this->upload_menu_categories();
				return true;
				exit; // exit since there will be no action to take if the master has no copy of menu of main db
			}
		

			if($automated){
				$trans_raw = $this->migrate_db->select($selected_field)->where('master_id', $migrate_local_id)->get($table_name)->result();
			}else{
				$trans_raw = $this->migrate_db->select($selected_field)->where('`update_date` > ',$update_date)->where('terminal_id',TERMINAL_ID)->where('branch_code',BRANCH_CODE)->get($table_name)->result();
				$trans_id_raw = $this->migrate_db->select($based_field)->where('`update_date` > ',$update_date)->where('terminal_id',TERMINAL_ID)->where('branch_code',BRANCH_CODE)->get($table_name)->result();
				// echo $this->migrate_db->last_query();die();
				$json_encode  = json_encode($trans_id_raw);		
			}				
			// echo $this->migrate_db->last_query();
			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					// echo "aa";die();
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"download","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>"download","transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed					
						$this->update_tbl($table_name,array('`update_date` > ' => "'".$update_date."'" ),array('master_id'=>$migration_id),NULL,NULL,'migrate_db',true);

				 		// $this->update_tbl_batch($table_name,$add_trans_wrapped,'menu_id','main_db');

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id =  $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id) , true,false);
					$add_trans_wrapped_default = $this->formulate_object($add_trans_header , array() , true,false);

				// echo "<pre>",print_r($trans_raw),"</pre>";
								// echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					// $this->update_trans_tbl_batch($table_name,$add_trans_wrapped,'master_id','main_db');

				
				 	$this->update_tbl_batch($table_name,$add_trans_wrapped,'menu_id','main_db');
				 	$this->update_tbl_batch($table_name,$add_trans_wrapped_default,'menu_id','db');

				// echo $this->db->last_query();
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
			
	}


	// upload menus from main to master  @Justin
	public function upload_menus($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		
			$table_name = 'menus';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
		    $based_field = 'menu_id';
		    $type = 'upload';

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($based_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					
						$this->update_tbl($table_name,array( 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');


					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				

	}


	// upload menu modifiers from main to master  @Justin
	public function upload_menu_modifiers($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		
			$table_name = 'menu_modifiers';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
		    $based_field = 'id,menu_id';
		    $type = 'upload';
			$check_if_menu_exists = $this->migrate_db->select('*')->where('terminal_id',TERMINAL_ID)->where('branch_code',BRANCH_CODE)->get($table_name)->result();


			if(count($check_if_menu_exists) > 0){
				return true;
			}
			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($based_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					
						$this->update_tbl($table_name,array( 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');


					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE) , true,false);
				
					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				

	}


	// upload menu categories from main to master  @Justin
	public function upload_menu_categories($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		
			$table_name = 'menu_categories';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
		    $based_field = 'menu_cat_id';
		    $type = 'upload';
			$check_if_menu_exists = $this->migrate_db->select('*')->where('terminal_id',TERMINAL_ID)->where('branch_code',BRANCH_CODE)->get($table_name)->result();


			if(count($check_if_menu_exists) > 0){
				return true;
			}

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($based_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					
						$this->update_tbl($table_name,array( 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');


					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE) , true,false);
				
					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				

	}

	// upload menu subcategories from main to master  @Justin
	public function upload_menu_subcategories($migration_id=NULL, $update_date = NULL, $automated=false, $migrate_local_id=NULL){
		
			$table_name = 'menu_subcategories';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
		    $based_field = 'menu_sub_cat_id';
		    $type = 'upload';
			$check_if_menu_exists = $this->migrate_db->select('*')->where('terminal_id',TERMINAL_ID)->where('branch_code',BRANCH_CODE)->get($table_name)->result();


			if(count($check_if_menu_exists) > 0){
				return true;
			}

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($based_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					
						$this->update_tbl($table_name,array( 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');


					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE) , true,false);
				
					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				

	}

		// migrate add trans_sales_no_tax  @Justin
	public function add_users($migration_id=NULL, $update_date = NULL, $automated=false,  $migrate_local_id=NULL){
		// var_dump($migration_id);
		// var_dump($update_date);
		// var_dump($automated);

		// die();
		    $table_name = 'users';
		    $selected_field = 'id';
			$user = $this->session->userdata('user');
			$user_id = $user['id'];
			$type = "add";

			if(empty($migration_id)){

				$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
				$trans_id_raw = $this->main_db->select($selected_field)->get_where($table_name,array('master_id' => NULL))->result();
				$json_encode  = json_encode($trans_id_raw);

			}else{

				if($automated){
					$trans_raw = $this->main_db->select('*')->where('master_id', $migrate_local_id)->get($table_name)->result();
				}else{
					$trans_raw = $this->main_db->select('*')->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$trans_id_raw = $this->main_db->select($selected_field)->where('`datetime` > ',$update_date)->or_where('master_id' ,NULL)->get($table_name)->result();
					$json_encode  = json_encode($trans_id_raw);
					
				}				
			}

			// echo "<pre>",print_r($trans_raw),"</pre>";die();


			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;
				$record_count = count($trans_raw);

				// if not automated add to logs else don't add since we already have migration_id
				if(!$automated){
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count,'sender_ip_address'=>$_SERVER['REMOTE_ADDR'])); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=> $table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					if(empty($migrate_id)){
						$this->update_tbl($table_name,array('master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');
					}else{
						$this->update_tbl($table_name,array('`datetime` > ' => "'".$update_date."'" , 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db',true);

					}

					$this->migrate_db->trans_complete();
					
				}else{
					$migration_sync_id = $migration_id;
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id,'terminal_id'=>TERMINAL_ID,'branch_code'=> BRANCH_CODE) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
				// 				echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
					$this->update_tbl('master_logs',array("master_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('master_logs',array("master_sync_id"=>$migration_sync_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

				$this->migrate_db->trans_complete();

				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}else{
				return false;
			}
				
				
	}

	// // upload menu subcategories from main to master  @Justin
	// public function upload_modifier_groups($migration_id, update_date = NULL, $automated=false){
		
	// 		$table_name = 'menu_subcategories';
	// 		$user = $this->session->userdata('user');
	// 		$user_id = $user['id'];
	// 	    $based_field = 'menu_sub_cat_id';
	// 	    $type = 'upload';

	// 		if(empty($migration_id)){

	// 			$trans_raw = $this->main_db->select('*')->get_where($table_name,array('master_id' => NULL))->result();
	// 			$trans_id_raw = $this->main_db->select($based_field)->get_where($table_name,array('master_id' => NULL))->result();
	// 			$json_encode  = json_encode($trans_id_raw);

	// 		}else{

	// 			if($automated){
	// 				$trans_raw = $this->main_db->select('*')->where('master_id',$migration_id)->get($table_name)->result();
	// 			}else{
	// 				$trans_raw = $this->main_db->select('*')->get($table_name)->result();
	// 				$json_encode  = json_encode($trans_id_raw);
					
	// 			}				
	// 		}

	// 		// echo "<pre>",print_r($trans_raw),"</pre>";die();


	// 		if(!empty($trans_raw) && isset($trans_raw)){

	// 			$add_trans_header = $trans_raw; // get the record
				
	// 			$is_automated = 0;
	// 			$record_count = count($trans_raw);

	// 			// if not automated add to logs else don't add since we already have migration_id
	// 			if(!$automated){
	// 				$this->migrate_db->trans_start(); // start update the local record with migration_id

	// 					$migration_sync_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'terminal_id'=> TERMINAL_ID ,'branch_code'=> BRANCH_CODE,'record_count'=> $record_count)); // status 0 pending ,  1 success , 3 failed
	// 					$migration_id = $this->add_tbl('master_logs',array("status"=>"0","type"=>$type,"transaction"=>$table_name,"src_id"=>$json_encode,'user_id'=>$user_id,'master_sync_id'=>$migration_sync_id),NULL,'main_db'); // status 0 pending ,  1 success , 3 failed
					
					
	// 					$this->update_tbl($table_name,array( 'master_id' => NULL),array('master_id'=>$migration_id),NULL,NULL,'main_db');


	// 				$this->migrate_db->trans_complete();
					
	// 			}else{
	// 				$is_automated = 1;
	// 			}

	// 			$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

	// 				$add_trans_wrapped = $this->formulate_object($add_trans_header , array('master_id' => $migration_id) , true,false);
				
	// 				$this->add_tbl_batch($table_name,$add_trans_wrapped);
				
	// 				$this->update_tbl('master_logs',array("master_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
	// 				$this->update_tbl('master_logs',array("master_sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'main_db');

	// 			$this->migrate_db->trans_complete();

	// 			// var_dump($this->migrate_db->trans_status());
	// 			if($this->migrate_db->trans_status()){
	// 				return true;
	// 			}else{
	// 				return false;
	// 			}
			
				
	// 		}
				

	// }
	/***
	** check the last log of migration if no logs it will return false @ Justin
	*/ 


	public function check_last_log(){
		$last_logs = $this->main_db->order_by('master_id desc')->limit('1')->get_where('master_logs')->result();
		$last_log = false;
		// echo "<pre>",print_r($last_logs),"</pre>";die();

		if(isset($last_logs[0]) && !empty($last_logs)){
			$last_log = $last_logs[0];
		}

		return $last_log;


	}



	/***
	** executes the migration by running the functions listed in $functions_array @ Justin
	*/ 
	public function execute_migration(){
		$last_log = $this->check_last_log();

		// add records from main to master
		$add_functions_array = array('add_trans_sales','add_trans_sales_charges','add_trans_sales_discounts','add_trans_sales_items','add_trans_sales_local_tax',
			'add_trans_sales_loyalty_points','add_trans_sales_menu_modifiers','add_trans_sales_menus','add_trans_sales_no_tax','add_trans_sales_payments',
			'add_trans_sales_tax','add_trans_sales_zero_rated','add_trans_refs'	,'add_users');


		// update records from main to master
			// $update_functions_array = array('update_trans_sales_charges');
		$update_functions_array = array('update_trans_sales','update_trans_sales_charges','update_trans_sales_discounts',
										'update_trans_sales_items','update_trans_sales_local_tax','update_trans_sales_loyalty_points',
										'update_trans_sales_menu_modifiers','update_trans_sales_menus','update_trans_sales_no_tax',
										'update_trans_sales_payments','update_trans_sales_tax','update_trans_sales_zero_rated');

		// download records including update from master to main
		$download_functions_array = array('download_menus');

			// echo "a";die();
		//check back log before migrating new data
		// echo "<pre>",print_r($this->check_backlogs()),"</pre>";die();
		if($this->check_backlogs()) {
			foreach($add_functions_array as $func){

				if($last_log){
					$migrate_id = $last_log->master_sync_id;
					$last_update = $last_log->migrate_date;
					$this->$func($migrate_id,$last_update);

				}else{
					$this->$func();
				}
			}

			if(!empty($last_log)){
				foreach($update_functions_array as $func){

					if($last_log){
						$migrate_id = $last_log->master_sync_id;
						$last_update = $last_log->migrate_date;
						$this->$func($migrate_id,$last_update);

					}else{
						$this->$func();
					}
				}
			}


			// run download array()

			foreach($download_functions_array as $func){

				if($last_log){
					// $migrate_id = $last_log->master_id;
					$migrate_id = $last_log->master_sync_id;
					$last_update = $last_log->migrate_date;
					$this->$func($migrate_id,$last_update);

				}else{
					$this->$func();
				}
			}

		}

		return json_encode($last_log);

		// echo "<pre>",print_r($last_log),"</pre>";die();
	}

	/** if there are pending transactions process them through automation - Justin 11/9/2017**/
	public function check_backlogs(){
		// echo "d";die();
			$pending_logs = $this->main_db->get_where('master_logs',array('status'=>'0'),10)->result(); // limit to 10
			// echo $this->main_db->last_query();die();
			if(!empty($pending_logs)){
			// echo "<pre>",print_r($pending_logs),"</pre>";die();
				foreach ($pending_logs as $key => $logs) {
					$trans_type = $logs->transaction;
					$trans_action = $logs->type;
					$migrate_local_id = $logs->master_id;
					$migrate_id = $logs->master_sync_id;
					$migrate_date = $logs->migrate_date;
					$function = $trans_action."_".$trans_type;

					if(method_exists($this , $function)){ // check if the function class is existing in this model if yes call the function
							// echo $function;die();
						$this->$function($migrate_id,$migrate_date,true,$migrate_local_id );
					}
				
				}
			}

			return true;
	}



	public function test(){
		$pending_logs = $this->main_db->get_where('master_logs',array('master_id'=>'18'))->result();
		return $pending_logs;
	}

}

?>