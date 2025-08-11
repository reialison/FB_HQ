<?php
class Sync_model extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->library('Db_manager');
		$this->migrate_db = $this->db_manager->get_connection(MIGRATED_MAIN_DB);

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

	public function update_tbl($table_name,$table_key,$items,$id=null,$set=array(),$db="migrate_db"){
		if(is_array($table_key)){
			foreach ($table_key as $key => $val) {
				if(is_array($val)){
					$this->$db->where_in($key,$val);
				}
				else
					$this->$db->where($key,$val,FALSE);
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

	public function update_tbl_batch($db,$table_name,$items,$key){
		$this->$db->update_batch($table_name,$items,$key);
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
	/***
		@uthor : Justin X. ^_- 10/17/2017
		get_last_migration() : get the last recorded log of migration to determine the action to be taken
	***/


	public function get_last_migration(){
		$this->migrate_db->trans_start();
			$this->migrate_db->select('*');
			$this->migrate_db->from('sync_logs');
			$this->migrate_db->order_by('sync_id desc');
			$this->migrate_db->limit(1);
			$query = $this->migrate_db->get();
			$result = $query->result();
		$this->migrate_db->trans_complete();

		// echo $this->db->last_query();die();
		return $result;
	}

// migrate table activity @Justin
	public function update_table_activity($table_activity_id=NULL, $migration_id = false){
		if(!empty($table_activity_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$table_activity_raw = $this->db->get_where('table_activity',array('tbl_id'=>$table_activity_id))->result();

			if(!empty($table_activity_raw[0]) && isset($table_activity_raw[0])){

				$table_activity_header = $table_activity_raw[0]; // get the record
						
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"table_activity","src_id"=>$table_activity_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"table_activity","src_id"=>$table_activity_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('table_activity',array("tbl_id"=>$table_activity_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
												
					$this->migrate_db->trans_complete();
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed
					$table_order_wrapped = $this->formulate_object($table_activity_header , array('sync_id' => $migration_id));

					$this->update_tbl('table_activity',array('tbl_id'=>$table_activity_id),$table_order_wrapped);
					
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL);
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}


	public function update_gift_cards($gift_check_id , $migration_id = false ){
			if(!empty($gift_check_id)){
			// echo "<pre>",print_r($_SESSION),"</pre>";die();
			$user = $this->session->userdata('user');
            // echo "<pre>",print_r($user ),"</pre>";die();
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$gift_card_raw = $this->db->get_where('gift_cards',array('gc_id'=>$gift_check_id))->result();

			if(!empty($gift_card_raw[0]) && isset($gift_card_raw[0])){

				$gift_card_header = $gift_card_raw[0]; // get the record
						
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"gift_cards","src_id"=>$gift_check_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"gift_cards","src_id"=>$gift_check_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('gift_cards',array("gc_id"=>$gift_check_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
												
					$this->migrate_db->trans_complete();
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed
					$gift_card_wrapped = $this->formulate_object($gift_card_header , array('sync_id' => $migration_id));

					$this->update_tbl('gift_cards',array('gc_id'=>$gift_check_id),$gift_card_wrapped);
					
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL);
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}

	// migrate trans sales loyalty points @Justin
	public function add_trans_sales_loyalty_points($loyalty_id=NULL, $migration_id=false){
		if(!empty($loyalty_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_sales_loyalty_raw = $this->db->get_where('trans_sales_loyalty_points',array('loyalty_point_id'=>$loyalty_id))->result();

			if(!empty($trans_sales_loyalty_raw[0]) && isset($trans_sales_loyalty_raw[0])){

				$trans_sales_loyalty_header = $trans_sales_loyalty_raw[0]; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_loyalty_points","src_id"=>$loyalty_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_loyalty_points","src_id"=>$loyalty_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_loyalty_points',array("loyalty_point_id"=>$loyalty_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$trans_sales_loyalty_wrapped = $this->formulate_object($trans_sales_loyalty_header , array('sync_id' => $migration_id));
				
					$this->add_tbl('trans_sales_loyalty_points',$trans_sales_loyalty_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}


	// update loyalty card points
	public function update_loyalty_card_points($loyalty_id , $migration_id = false ){
			if(!empty($gift_check_id)){
			// echo "<pre>",print_r($_SESSION),"</pre>";die();
			$user = $this->session->userdata('user');
            // echo "<pre>",print_r($user ),"</pre>";die();
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$loyalty_card_raw = $this->db->get_where('loyalty_cards',array('card_id'=>$loyalty_id))->result();

			if(!empty($loyalty_card_raw[0]) && isset($loyalty_card_raw[0])){

				$loyalty_card_header = $loyalty_card_raw[0]; // get the record
						
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"loyalty_cards","src_id"=>$loyalty_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"loyalty_cards","src_id"=>$loyalty_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('loyalty_cards',array("card_id"=>$loyalty_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
												
					$this->migrate_db->trans_complete();
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed
					$loyalty_card_wrapped = $this->formulate_object($loyalty_card_header , array('sync_id' => $migration_id));

					$this->update_tbl('loyalty_cards',array('card_id'=>$loyalty_id),$loyalty_card_wrapped);
					
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL);
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}

	// update coupons
	public function update_coupons($coupon_id , $migration_id = false ){
			if(!empty($coupon_id)){
			// echo "<pre>",print_r($_SESSION),"</pre>";die();
			$user = $this->session->userdata('user');
            // echo "<pre>",print_r($user ),"</pre>";die();
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$coupons_raw = $this->db->get_where('coupons',array('coupon_id'=>$coupon_id))->result();

			if(!empty($coupons_raw[0]) && isset($coupons_raw[0])){

				$coupons_header = $coupons_raw[0]; // get the record
						
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"coupons","src_id"=>$coupon_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"coupons","src_id"=>$coupon_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('coupons',array('coupon_id'=>$coupon_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
												
					$this->migrate_db->trans_complete();
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed
					$coupons_wrapped = $this->formulate_object($coupons_header , array('sync_id' => $migration_id));

					$this->update_tbl('coupons',array('coupon_id'=>$coupon_id),$coupons_wrapped);
					
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL);
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}

	// migrate add customer bank @Justin
	public function add_customers_bank($cb_id=NULL, $migration_id=false){
		if(!empty($cb_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$add_customers_bank_raw = $this->db->get_where('customers_bank',array('bank_id'=>$cb_id))->result();

			if(!empty($add_customers_bank_raw[0]) && isset($add_customers_bank_raw[0])){

				$add_customers_bank_header = $add_customers_bank_raw[0]; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"customers_bank","src_id"=>$cb_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"customers_bank","src_id"=>$cb_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('customers_bank',array("bank_id"=>$cb_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_customers_bank_wrapped = $this->formulate_object($add_customers_bank_header , array('sync_id' => $migration_id));
				
					$this->add_tbl('customers_bank',$add_customers_bank_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

// migrate add trans sales payments @Justin
	public function add_trans_sales_payments($trans_payment_id=NULL, $migration_id=false){
		if(!empty($trans_payment_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$add_trans_payment_raw = $this->db->select('`sales_id` , `payment_type` ,`amount` ,`to_pay` ,`reference` ,`card_type` ,`card_number` ,`approval_code` ,
							`user_id` ,`datetime`')->get_where('trans_sales_payments',array('payment_id'=>$trans_payment_id))->result();

			if(!empty($add_trans_payment_raw[0]) && isset($add_trans_payment_raw[0])){

				$add_customers_bank_header = $add_trans_payment_raw[0]; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_payments","src_id"=>$trans_payment_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_payments","src_id"=>$trans_payment_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_payments',array("payment_id"=>$trans_payment_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_customers_bank_wrapped = $this->formulate_object($add_customers_bank_header , array('sync_id' => $migration_id));
				
					$this->add_tbl('trans_sales_payments',$add_customers_bank_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

    // delete logs 
	public function delete_trans_sales_payments($tbl_id , $migration_id = false ){

		if(!empty($tbl_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$table_raw = $this->db->get_where('trans_sales_payments',array('payment_id'=>$tbl_id))->result();
// var_dump($table_raw);die();
			if(!empty($table_raw) && isset($table_raw)){

				$table_header = $table_raw; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('trans_sales_payments',array('payment_id'=>$tbl_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_payments","src_id"=>$tbl_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"table_activity","src_id"=>$tbl_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}


	// migrate add trans sales payments @Justin
	public function add_trans_sales($sales_id=NULL, $migration_id=false){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$add_trans_raw = $this->db->get_where('trans_sales',array('sales_id'=>$sales_id))->result();


			if(!empty($add_trans_raw[0]) && isset($add_trans_raw[0])){

				$add_trans_header = $add_trans_raw[0]; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales',array("sales_id"=>$sales_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id,'branch_code'=>BRANCH_CODE));
					$this->add_tbl('trans_sales',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

// update trans_sales
	public function update_trans_sales($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"trans_sales","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"trans_sales","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales',array('sales_id'=>$sales_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
												
					$this->migrate_db->trans_complete();
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed
					$trans_sales_wrapped = $this->formulate_object($trans_sales_header , array('sync_id' => $migration_id));

					$this->update_tbl('trans_sales',array('sales_id'=>$sales_id),$trans_sales_wrapped);
					
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL);
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}

	// migrate add trans sales  menus @Justin
	public function add_trans_sales_menus($trans_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_raw = $this->db->get_where('trans_sales_menus',array('sales_id'=>$trans_id))->result();

			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_menus","src_id"=>$trans_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_menus","src_id"=>$trans_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_menus',array("sales_id"=>$trans_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id) , true,false);
				// echo "<pre>",print_r($trans_raw),"</pre>";
								// echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();

					$this->add_tbl_batch('trans_sales_menus',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}




	// migrate add trans sales  items @Justin
	public function add_trans_sales_items ($trans_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_raw = $this->db->get_where('trans_sales_items',array('sales_id'=>$trans_id))->result();

			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_items","src_id"=>$trans_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_items","src_id"=>$trans_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_items',array("sales_id"=>$trans_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('trans_sales_items',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

	// migrate add trans sales  items @Justin
	public function add_trans_sales_menu_modifiers ($trans_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_raw = $this->db->get_where('trans_sales_menu_modifiers',array('sales_id'=>$trans_id))->result();

			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_menu_modifiers","src_id"=>$trans_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_menu_modifiers","src_id"=>$trans_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_menu_modifiers',array("sales_id"=>$trans_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('trans_sales_menu_modifiers',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

		// migrate add trans sales  discounts @Justin
	public function add_trans_sales_discounts ($trans_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_raw = $this->db->get_where('trans_sales_discounts',array('sales_id'=>$trans_id))->result();

			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_discounts","src_id"=>$trans_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_discounts","src_id"=>$trans_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_discounts',array("sales_id"=>$trans_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('trans_sales_discounts',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

		// migrate add trans sales  charges @Justin
	public function add_trans_sales_charges ($trans_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_raw = $this->db->get_where('trans_sales_charges',array('sales_id'=>$trans_id))->result();

			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_charges","src_id"=>$trans_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_charges","src_id"=>$trans_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_charges',array("sales_id"=>$trans_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('trans_sales_charges',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

	// migrate add trans sales zero rated @Justin

	public function add_trans_sales_zero_rated ($trans_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_raw = $this->db->get_where('trans_sales_zero_rated',array('sales_id'=>$trans_id))->result();

			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_zero_rated","src_id"=>$trans_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_zero_rated","src_id"=>$trans_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_zero_rated',array("sales_id"=>$trans_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('trans_sales_zero_rated',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

	// migrate add trans sales no tax @Justin

	public function add_trans_sales_no_tax ($trans_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_raw = $this->db->get_where('trans_sales_no_tax',array('sales_id'=>$trans_id))->result();

			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_no_tax","src_id"=>$trans_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_no_tax","src_id"=>$trans_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_no_tax',array("sales_id"=>$trans_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('trans_sales_no_tax',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}


	// migrate add trans sales no tax @Justin

	public function add_trans_sales_tax ($trans_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_raw = $this->db->get_where('trans_sales_tax',array('sales_id'=>$trans_id))->result();

			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_tax","src_id"=>$trans_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_tax","src_id"=>$trans_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_tax',array("sales_id"=>$trans_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('trans_sales_tax',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

	// migrate add trans sales no tax @Justin

	public function add_trans_sales_local_tax ($trans_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$trans_raw = $this->db->get_where('trans_sales_local_tax',array('sales_id'=>$trans_id))->result();

			if(!empty($trans_raw) && isset($trans_raw)){

				$add_trans_header = $trans_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_local_tax","src_id"=>$trans_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_sales_local_tax","src_id"=>$trans_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_sales_local_tax',array("sales_id"=>$trans_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('trans_sales_local_tax',$add_trans_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

	// migrate add item moves batch @Justin

	public function add_item_moves_batch($loc_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$item_moves_raw = $this->db->get_where('item_moves',array('loc_id'=>$loc_id , 'sync_id' => NULL) )->result();

			if(!empty($item_moves_raw) && isset($item_moves_raw)){

				$item_moves_header = $item_moves_raw; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"item_moves","src_id"=>$loc_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"item_moves","src_id"=>$loc_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('item_moves',array('loc_id'=>$loc_id , 'sync_id' => NULL),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$item_moves_wrapped = $this->formulate_object($item_moves_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('item_moves',$item_moves_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}

	// migrate add reasons @Justin

	public function add_reasons ($reason_id=NULL, $migration_id=false){
		if(!empty($trans_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$reasons_raw = $this->db->get_where('reasons',array('id'=>$trans_id))->result();

			if(!empty($reasons_raw[0]) && isset($reasons_raw[0])){

				$reasons_header = $reasons_raw[0]; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"add_reasons","src_id"=>$reason_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"add_reasons","src_id"=>$reason_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('reasons',array("id"=>$reason_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$reasons_wrapped = $this->formulate_object($reasons_header , array('sync_id' => $migration_id),true,false);
				
					$this->add_tbl_batch('reasons',$reasons_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}


	// migrate add logs @Justin
	public function add_logs($log_id=NULL, $migration_id=false){
		if(!empty($log_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$add_logs_raw = $this->db->get_where('logs',array('id'=>$log_id))->result();

			if(!empty($add_logs_raw[0]) && isset($add_logs_raw[0])){

				$add_logs_header = $add_logs_raw[0]; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"logs","src_id"=>$log_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"logs","src_id"=>$log_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('logs',array("id"=>$log_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_logs_wrapped = $this->formulate_object($add_logs_header , array('sync_id' => $migration_id));
				
					$this->add_tbl('logs',$add_logs_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}



    // delete logs 
	public function delete_table_activity($tbl_id , $migration_id = false ){
		if(!empty($tbl_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$table_raw = $this->db->get_where('table_activity',array('pc_id'=>$tbl_id))->result();

			if(!empty($table_raw) && isset($table_raw)){

				$table_header = $table_raw; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('table_activity',array('pc_id'=>$tbl_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"table_activity","src_id"=>$tbl_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"table_activity","src_id"=>$tbl_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}

// delete trans_sales_menus
	public function delete_trans_sales_menus($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales_menus',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('trans_sales_menus',array('sales_id'=>$sales_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_menus","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_menus","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}


// delete delete_trans_sales_items
	public function delete_trans_sales_items($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales_items',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('trans_sales_items',array('sales_id'=>$sales_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_items","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_items","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	

	// delete delete_trans_sales_menu_modifiers
	public function delete_trans_sales_menu_modifiers($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales_menu_modifiers',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed
						$this->delete_tbl('trans_sales_menu_modifiers',array('sales_id'=>$sales_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_menu_modifiers","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_menu_modifiers","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}

	// delete delete_trans_sales_discounts
	public function delete_trans_sales_discounts($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales_discounts',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('trans_sales_discounts',array('sales_id'=>$sales_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_discounts","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_discounts","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}

	// delete trans_sales_charges
	public function delete_trans_sales_charges($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales_charges',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('trans_sales_charges',array('sales_id'=>$sales_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_charges","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_charges","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}

	// delete trans_sales_charges
	public function delete_trans_sales_tax($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales_tax',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('trans_sales_tax',array('sales_id'=>$sales_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_tax","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_tax","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}

// delete delete_trans_sales_no_tax
	public function delete_trans_sales_no_tax($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales_no_tax',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('trans_sales_no_tax',array('sales_id'=>$sales_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_no_tax","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_no_tax","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		} 
	}

	// delete delete_trans_sales_no_tax
	public function delete_trans_sales_zero_rated($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales_zero_rated',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('trans_sales_zero_rated',array('sales_id'=>$sales_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_zero_rated","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_zero_rated","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		} 
	}

	// delete delete_trans_sales_no_tax
	public function delete_trans_sales_local_tax($sales_id , $migration_id = false ){
		if(!empty($sales_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('trans_sales_local_tax',array('sales_id'=>$sales_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if($migration_id){					
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed

						$this->delete_tbl('trans_sales_local_tax',array('sales_id'=>$sales_id));
					
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_local_tax","src_id"=>$sales_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"1","type"=>"delete","transaction"=>"trans_sales_local_tax","src_id"=>$sales_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
		
				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		} 
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


	/** if there are pending transactions process them through automation - Justin 10/30/2017**/
	public function check_backlogs(){
			$pending_logs = $this->db->get_where('sync_logs',array('status'=>'0'))->result();
// echo "<pre>",print_r($pending_logs),	"</pre>";die();
			if(!empty($pending_logs)){
				foreach ($pending_logs as $key => $logs) {
					$trans_type = $logs->transaction;
					$trans_action = $logs->type;
					$migrate_id = $logs->sync_id;
					$src_id = $logs->src_id;

					// if($trans_type == 'sales_order_entry'){
					// 	if($trans_action == 'add'){
					// 		$this->add_sales_order($src_id, $migrate_id);
					// 	}elseif($trans_action == 'update'){
					// 		$this->update_sales_order($src_id, $migrate_id);
					// 	}elseif($trans_action == 'delete'){
					// 		$this->delete_sales_order($src_id, array('status'=>'inactive','inactive'=>'1', $migrate_id));
					// 	}
					// }elseif($trans_type == 'delivery_note'){
					// 	if($trans_action == 'add'){
					// 		$this->add_delivery_note($src_id, $migrate_id);
					// 	}elseif($trans_action == 'update' || $trans_action == 'delete'){
					// 		$this->update_delivery_note($src_id, $migrate_id);
					// 	}
					// }elseif($trans_type == 'sales_invoice'){
					// 	if($trans_action == 'add'){
					// 		$this->add_sales_invoice($src_id, $migrate_id);
					// 	}elseif($trans_action == 'update'){
					// 		$this->update_sales_invoice($src_id,$migrate_id);
					// 	}
					// }
				}
			}

			return true;
			// echo "<pre>",print_r($pending_logs),"</pre>";die();
	}


	// migrate add trans_ref @Justin
	public function add_trans_ref($log_id=NULL, $migration_id=false){
		if(!empty($log_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$add_logs_raw = $this->db->get_where('trans_refs',array('id'=>$log_id))->result();

			if(!empty($add_logs_raw[0]) && isset($add_logs_raw[0])){

				$add_logs_header = $add_logs_raw[0]; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_refs","src_id"=>$log_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"trans_refs","src_id"=>$log_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_refs',array("id"=>$log_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_logs_wrapped = $this->formulate_object($add_logs_header , array('sync_id' => $migration_id));
				
					$this->add_tbl('trans_refs',$add_logs_wrapped);
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}


	// migrate trans ref @Justin
	public function update_next_ref($type_id=NULL, $migration_id = false){
		if(!empty($type_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$next_raw = $this->db->get_where('trans_types',array('type_id'=>$type_id))->result();

			if(!empty($next_raw[0]) && isset($next_raw[0])){

				$next_header = $next_raw[0]; // get the record
						
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"trans_types","src_id"=>$type_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"trans_types","src_id"=>$type_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('trans_types',array("type_id"=>$type_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
												
					$this->migrate_db->trans_complete();
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed
					$next_wrapped = $this->formulate_object($next_header , array('sync_id' => $migration_id));

					$this->update_tbl('trans_types',array('type_id'=>$type_id),$next_wrapped);
					
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL);
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}


	// migrate add users @Justin
	public function add_users($users_id=NULL, $migration_id=false){
		if(!empty($users_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			$add_trans_raw = $this->db->get_where('users',array('id'=>$users_id))->result();

			// echo "<pre>",print_r($add_trans_raw),"</pre>";die();
			if(!empty($add_trans_raw[0]) && isset($add_trans_raw[0])){

				$add_trans_header = $add_trans_raw[0]; // get the record
				
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"users","src_id"=>$users_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"add","transaction"=>"users","src_id"=>$users_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('users',array("id"=>$users_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
					
					$this->migrate_db->trans_complete();
					
				}else{
					$is_automated = 1;
				}

				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback

					$add_trans_wrapped = $this->formulate_object($add_trans_header , array('sync_id' => $migration_id,'branch_code'=>BRANCH_CODE));
				// echo "<pre>",print_r($add_trans_wrapped),"</pre>";die();
					$this->add_tbl('users',$add_trans_wrapped);
					// echo $this->db->query();die();
// echo "add";die();
				
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'migrate_db');
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog 
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			
				
			}
				
		}

	}


	// update users
	public function update_users($users_id , $migration_id = false ){
		if(!empty($users_id)){
			$user = $this->session->userdata('user');
			$user_id = $user['id'];

			// var_dump($user_id);die();
			$trans_sales_raw = $this->db->get_where('users',array('id'=>$users_id))->result();

			if(!empty($trans_sales_raw[0]) && isset($trans_sales_raw[0])){

				$trans_sales_header = $trans_sales_raw[0]; // get the record
						
				$is_automated = 0;

				if(!$migration_id){
					//start the migration of new sales order
					$this->migrate_db->trans_start(); // start update the local record with migration_id

						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"users","src_id"=>$users_id,'user_id'=>$user_id)); // status 0 pending ,  1 success , 3 failed
						$migration_id = $this->add_tbl('sync_logs',array("status"=>"0","type"=>"update","transaction"=>"users","src_id"=>$users_id,'user_id'=>$user_id),NULL,'db'); // status 0 pending ,  1 success , 3 failed
						$this->update_tbl('users',array('id'=>$users_id),array('sync_id'=>$migration_id),NULL,NULL,'db');
												
					$this->migrate_db->trans_complete();
				}else{
					$is_automated = 1;
				}


				$this->migrate_db->trans_start(); // start the mmigration if failed it will rollback
					// $migration_id = $this->add_tbl('migration_logs',array("status"=>"1")); // status 0 pending ,  1 success , 3 failed
					$trans_sales_wrapped = $this->formulate_object($trans_sales_header , array('sync_id' => $migration_id));

					$this->update_tbl('users',array('id'=>$users_id),$trans_sales_wrapped);
					
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL);
					$this->update_tbl('sync_logs',array("sync_id"=>$migration_id),array('status'=>"1","is_automated"=>$is_automated),NULL,NULL,'db');

				$this->migrate_db->trans_complete();

				$this->check_backlogs(); //check backlog	
				// var_dump($this->migrate_db->trans_status());
				if($this->migrate_db->trans_status()){
					return true;
				}else{
					return false;
				}
			}
		}
	}


}

?>