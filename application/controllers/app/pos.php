<?php
// require_once ("secure_area.php");
class Pos extends CI_controller
{
	function __construct()
	{
		parent::__construct('pos');
		$this->load->helper('url');
	    $this->load->model('app/Pos_app');

		// if(!isset($_SESSION['person_id'])){
		// 	 redirect('/', 'refresh');
		// }
		// echo "<pre>",print_r($_SESSION),"</pre>";die();
		// $this->has_profit_permission = $this->Employee->has_module_action_permission('reports','show_profit',$this->Employee->get_logged_in_employee_info()->person_id);
		// $this->has_cost_price_permission = $this->Employee->has_module_action_permission('reports','show_cost_price',$this->Employee->get_logged_in_employee_info()->person_id);
	}

	function index()
	{
		// echo "tesa";die();
		$this->load->view('pos/parts/head');	
		$this->load->view('pos/parts/foot');	
		$this->load->view("pos/home");
	}

	function index2()
	{
		// echo "tesa";
		$this->load->view('parts/head2');	
		$this->load->view('parts/foot');	
		$this->load->view("pos/home2");
	}

	function shop($category="")
	{
		$category = strtolower(urldecode($category));

		// temporary constant for demo
		$items = $this->Pos_app->get_all_by_category('snacks');
		$data['items']['snacks'] = $items->result();
		$items = $this->Pos_app->get_all_by_category('dessert');
		$data['items']['dessert'] = $items->result();
		$items = $this->Pos_app->get_all_by_category('bread');
		$data['items']['bread'] = $items->result();
		$items = $this->Pos_app->get_all_by_category('meals');
		$data['items']['meals'] = $items->result();
		$items = $this->Pos_app->get_all_by_category('drinks');
		$data['items']['drinks'] = $items->result();
		$data['item_cart'] = $this->get_cart_items();
		$data['finish'] = false;
		// echo "<pre>",print_r($_SESSION),"</pre>";die();
		$file_ads = './'. menu_folder;
		$data['image_compiled'] = array();
		if ($handle = opendir($file_ads)) {

   			 while (false !== ($entry = readdir($handle))) {

        		if ($entry != "." && $entry != "..") {
        			$data['image_compiled'][] = array("path"=>base_url().menu_folder.''.$entry,"image_name"=>$entry);
       			}
 	   		}

    		closedir($handle);
		}
		// echo "<pre>",print_r($data),"</pre>";die();

		$this->load->view('pos/parts/head');	
		$this->load->view("pos/shop_list",$data);	
		$this->load->view('pos/parts/foot');	
	}

	function search($search="")
	{
		// echo "sss";die();
		$search = urldecode($search);

		// echo $search;die();

		$items = $this->Pos_app->get_all_by_search($search);
		// echo $this->db->last_query();die();
		$this->data['items'] = $items->result();


		$this->data['item_cart'] = $this->get_cart_items();
		$this->data['finish'] = false;
		// echo "<pre>",print_r($_SESSION),"</pre>";die();

		$this->load->view('pos/parts/head');	
		$this->load->view("pos/shop_list",$this->data);	
		$this->load->view('pos/parts/foot');	
	}
	

	function add_to_cart($item_info=array()){
		if(session_id() == '') {
				session_start();
		}
		// unset($_SESSION['cart_items']);die();
		$post = $this->input->post();
// echo "<pre>",print_r($post),"</pre>";die();
		// echo count($post);die();
		// if(count($post) > 1){

		// }else{
			$item_info = array('item_id' => $post['ref'] , 'file_id' => $post['imref'] ,'qty'=>1);
		// }
			// echo "<pre>",print_r($item_info),"</pre>";
// echo "<pre>before:",print_r($_SESSION),"</pre>";
		$_SESSION['cart_items'][$post['ref']] =  $item_info; 
// echo "<pre>",print_r($_SESSION),"</pre>";die();
		
		echo true;
	}


	function update_to_cart($item_info=array()){
		if(session_id() == '') {
				session_start();
		}
		// unset($_SESSION['cart_items']);die();
		$post = $this->input->post();
    

		if(!empty($post['item_list'])){
			foreach($post['item_list'] as $item){
				$_SESSION['cart_items'][$item['ref']]['qty'] = $item['qty']; 
			}
		}
		// echo "<pre>",print_r($_SESSION),"</pre>";die();
		
		echo true;
	}

	function remove_to_cart($item_info=array()){
		if(session_id() == '') {
				session_start();
		}
		// unset($_SESSION['cart_items']);die();
		$post = $this->input->post();
		// session_start();

		// echo count($post);die();
		// if(count($post) > 1){

		// }else{

		if(isset($_SESSION['cart_items'][$post['ref']])){

			unset($_SESSION['cart_items'][$post['ref']]);
		}
		// }
		
		echo true;
	}

	function get_cart_items($img_included=true,$calculate_total = false){
		if(session_id() == '') {
		session_start();
		}
		$cart_items = array();
		// $total = 0;
		// $total_tax = 0;
		if(!empty($_SESSION['cart_items'])){
			$cart_items = $_SESSION['cart_items'];
// echo "<pre>",print_r($cart_items),"</pre>";die();
			foreach($cart_items as $c_id => &$c_items){
				$item_id = $c_id;
				$item_data = $this->Pos_app->get_item_info($item_id);

				if(!empty($item_data[0]) && isset($item_data[0])){
					$cart_items[$c_id]['item_name'] = $item_data[0]->menu_short_desc;
					$cart_items[$c_id]['unit_price_label'] = number_format($item_data[0]->cost,2,'.',',');
					$cart_items[$c_id]['unit_price'] = number_format($item_data[0]->cost,2);
					$cart_items[$c_id]['tax_included'] = '1'; 

					if($calculate_total){
						if(isset( $cart_items['summary']['total'])){

						 $cart_items['summary']['total']  += $item_data[0]->cost * $cart_items[$c_id]['qty'] ;
						  // $cart_items['summary']['total']
						}else{
							$cart_items['summary']['total']  = $item_data[0]->cost * $cart_items[$c_id]['qty'] ;
						}
					}
					// $cart_items[$c_id]['img_path'] = $
					if(!empty($c_items['file_id'])){
						
						$file_id = (int) $c_items['file_id'];

						$img_data = $this->Pos_app->get_image($file_id);
						// echo $img_data;die();

						// if($img_included)
						if(!empty($img_data)){
							// print_r($img_data);die();
							$cart_items[$c_id]['item_img'] = $img_data;
						}
					}
				}


			}



		}
			// echo "<pre>",print_r($cart_items),"</pre>";die();

		return $cart_items;

	}


	function cart()
	{
		if(session_id() == '') {
				session_start();
		}

            $this->load->helper('form_helper');

		$cart_items = array();

		if(!empty($_SESSION['cart_items'])){
			$cart_items = $_SESSION['cart_items'];
		}

		$data['payment_options']=array(
				lang('sales_cash') => lang('sales_cash'),
				lang('sales_credit') => lang('sales_credit')
				);


		$data['item_cart'] = $this->get_cart_items(); 
		$data['checkout_details'] =  (isset($_SESSION['cart_details'])) ?  $_SESSION['cart_details'] : array();
		// echo "<pre>",print_r($data),"</pre>";die();
		$this->load->view('pos/parts/head');	
		$this->load->view("pos/cart",$data);	
		$this->load->view('pos/parts/foot');	

	}



	function checkout()
	{
		if(session_id() == '') {
				session_start();
		}
		// echo "<pre>",print_r($this->session->userdata('user')['id']),"</pre>";
         $this->load->model('dine/clock_model');
		$cart_items = array();
		$employee_id = isset($this->session->userdata('user')['id'])  ? $this->session->userdata('user')['id'] :1;
		$checkout_details = isset($_SESSION['cart_details']) ?  $_SESSION['cart_details'] : array();
	    $get_shift = $this->clock_model->get_shift_id(null,$employee_id);
	    $shift_id =  isset($get_shift[0]->shift_id) ? $get_shift[0]->shift_id : 9999999;
	    

	    $cart_items =  $this->get_cart_items(false); 
		$get_total = $this->get_cart_items(false,true); 

		$payment_type = (isset($_SESSION['cart_details']['payment_type'])) ? $checkout_details['payment_type'] : '';
		$comment = (isset($_SESSION['cart_details']['comment'])) ? $checkout_details['comment'] : '';
		$payment_amount = (isset($_SESSION['cart_details']['payment_amount'])) ? $checkout_details['payment_amount'] : '';
		$amount_due = (isset($get_total['summary']['total'])) ? $get_total['summary']['total'] : 0;
		$tax_rate = 12 ; //temporary constant
		$dividend = "1.".$tax_rate;
		 				$srp = $amount_due / floatval($dividend);
		 				$srp_tax =  $srp * floatval('.'.$tax_rate);
		
		// echo $amount_due. " : " .$srp_tax;die();
		// echo $employee_id;die();
 		// $employee_location = $this->Pos_app->get_employee_location($employee_id);
 		// $tax_rate = $this->Pos_app->get_tax_rate();

		$sales = $sales_items  = $sales_item_taxes =  array();

		$error = false;
		// echo $tax_rate;

		// echo "<pre>",print_r($cart_items),"</pre>";die();

		if(!empty($cart_items)) {

			// $cart_items = array('type_id' => SALES_TRANS,
			// 					'type' => 'takeout',
			// 					'user_id'=> $employee_id,
			// 					'shift_id' => $shift_id,
			// 					'terminal_id' => 1, //temporary constant
			// 					'total_amount' => '',
			// 					'total_paid' => $payment_amount,
			// 					'memo'=>$comment

			// 	);
			$now = $this->site_model->get_db_now();
			$strtotime_now = date('Y-m-d H:i:s',strtotime($now));
// 			echo $strtotime_now . "<br>";
// 			echo date('Y-m-d H:i:s');
// echo date('Y-m-d h:i:s');
// 			die();

			$sales = array('type_id' => SALES_TRANS,
								'type' => 'App Order',
								'user_id'=> $employee_id,
								'shift_id' => $shift_id,
								'terminal_id' => 1, //temporary constant
								'total_amount' => $amount_due,
								'total_paid' => $payment_amount,
								'memo'=>$comment,
								'datetime' => $strtotime_now,

				);
	 		$sales_id = $this->Pos_app->insert_sales($sales);
			
			

	 		// $payment = array('sale_id'=>$sales_id,
				// 			'payment_amount'=> $payment_amount,
				// 			'payment_type'=> $payment_type,

				// 			);
	 		//  // echo "<pre>",print_r($payment),"</pre>";die();
	 		//  $this->Pos_app->insert_payment_amount($payment);
	 		// // echo $this->db->last_query();die();
	 		$ctr = 1;


	 		if($sales_id){
	 			$sales_item_taxes = array('sales_id'=>$sales_id,
		 								'name'=> 'VAT',
		 								'rate'=> $tax_rate,
		 								'amount'=> $srp_tax);
	 			$this->Pos_app->insert_sales_tax($sales_item_taxes);

		 		foreach($cart_items as $item){
		 			$srp = $item['unit_price'];
		 			$srp_tax = 0;
		 			

		 			// var_dump($srp);
		 			// var_dump(" ".$srp_tax);
		 			// die();
		 			$sales_items[] = array('sales_id'=>$sales_id,
		 									'menu_id'=> $item['item_id'],
		 									'line_id' => $ctr , 
		 									'qty'=> $item['qty'],
		 									// 'item_cost_price'=> $item['cost_price'],
		 									'price'=> $srp);

		 			$ctr++;
		 		}

		 		 $sales_resp = $this->Pos_app->batch_insert('trans_sales_menus',$sales_items);


		 		

	 		}else{
	 			$error = true;
	 		}

		}else{
			 redirect('/app', 'refresh');
		}
		unset($_SESSION['cart_items']);
		unset($_SESSION['cart_details']);

		//add to session pending orders

		$_SESSION['pending_orders'][$sales_id] = $sales_id;
		$_SESSION['unreceived_orders'][$sales_id] = $sales_id;

		$this->data['finish'] = true;
		$this->load->view('pos/parts/head');	
		$this->load->view("pos/home",$this->data);	
		$this->load->view('pos/parts/foot');	


	}

	function add_checkout_details($item_info=array()){
		if(session_id() == '') {
				session_start();
		}
		// unset($_SESSION['cart_items']);die();
		$post = $this->input->post();

		$checkout_info = array('comment'=>$post['comment'],'payment_amount' => $post['payment_amount'],'payment_type' => $post['payment_type'] );
		$_SESSION['cart_details'] =  $checkout_info; 

		// echo "<pre>",print_r($_SESSION),"</pre>";die();
		echo true;
	}


	function check_order_status()
	{

		if(session_id() == '') {
				session_start();
		}
					// echo "<pre>",print_r($_SESSION),"</pre>";

		if(isset($_SESSION['pending_orders']) && !empty($_SESSION['pending_orders'])){

			$pending_orders = $_SESSION['pending_orders'];
			$get_ids = array();
			foreach($pending_orders as $sale_id => $p_o){
				$get_ids[] = $sale_id;
			}
			$check_orders = $this->Pos_app->get_sales_status($get_ids);
	// echo "<pre>",print_r($_SESSION),"</pre>";
			foreach($check_orders as $c_o){
				if(isset($_SESSION['pending_orders'][$c_o->sales_id]))
					unset($_SESSION['pending_orders'][$c_o->sales_id]);
			}

			// echo "<pre>",print_r($check_orders),"</pre>";die();
			echo json_encode($check_orders);
		}else{
			echo json_encode(array());
		}

	}

	function check_order_received_status()
	{
		if(isset($_SESSION['unreceived_orders']) && !empty($_SESSION['unreceived_orders'])){

			$pending_orders = $_SESSION['unreceived_orders'];
			$get_ids = array();
			foreach($pending_orders as $sale_id => $p_o){
				$get_ids[] = $sale_id;
			}
			$check_orders = $this->Pos_app->get_sales_received_status($get_ids);
	// echo "<pre>",print_r($_SESSION),"</pre>";
			foreach($check_orders as $c_o){
				if(isset($_SESSION['unreceived_orders'][$c_o->sale_id]))
					unset($_SESSION['unreceived_orders'][$c_o->sale_id]);
			}

			// echo "<pre>",print_r($check_orders),"</pre>";die();
			echo json_encode($check_orders);
		}else{
			echo json_encode(array());
		}

	}

	

	function image($file_id){
		ob_clean();
		$data['image'] = $this->Pos_app->get_image($file_id);
		$this->load->view('pos/image',$data);	
	}
	function tabledata()
	{
		// $sales_id = $this->Pos_app->insert_sales($sales);
		$data = array();

		$data['datatable_sales'] = $this->Pos_app->get_sales_payments();
		// echo $this->db->last_query();die();
		// $test['get_total'] = $this->Pos_app->get_total_();
		// $data['amount_change'] = $this->sale_lib->is_sale_cash_payment();
		// echo "<pre>",print_r($data),"</pre>";die();
		$this->load->view('pos/parts/head');	
		$this->load->view("pos/table",$data);	
		$this->load->view('pos/parts/foot');
	}
	function check_new_orders()
	{
		$check_orders = $this->Pos_app->get_pending_order_count();
		$status = false;
	// echo "<pre>",print_r($check_orders),"</pre>";die();
		if(isset($check_orders[0]->counter)) {
			$pending_order_count = $check_orders[0]->counter;

			// echo $pending_order_count;die();
			// $_SESSION['pending_order_count'] = ;

			if(!isset($_SESSION['pending_order_count'])){
				$_SESSION['pending_order_count'] = $pending_order_count;
			}

// echo $_SESSION['pending_order_count']. " ". $pending_order_count;
			if($_SESSION['pending_order_count'] != $pending_order_count){
				// echo "ss";
				$status = true;
			}
// 			echo  $pending_order_count;
// echo $_SESSION['pending_order_count'];
			$_SESSION['pending_order_count'] = $pending_order_count ;
		
		}

	 echo $status;
	}
}
?>