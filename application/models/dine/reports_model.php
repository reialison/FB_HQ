<?php
class Reports_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	public function get_logs($user_id=null,$args=array(),$limit=0)
	{
		$this->db->select('
			logs.*,
			users.username,users.fname,users.mname,users.lname,users.suffix
			');
		$this->db->from('logs');
		$this->db->join('users','logs.user_id = users.id','left');
		if(isset($branch_id)){
			$this->db->where('logs.branch_code', $branch_id);
		}
		if (!is_null($user_id)) {
			if (is_array($user_id))
				$this->db->where_in('logs.user_id',$user_id);
			else
				$this->db->where('logs.user_id',$user_id);
		}
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
		$this->db->order_by('logs.datetime desc');
		$query = $this->db->get();
				// echo $this->db->last_query();
		return $query->result();
	}
	public function get_item_brief($item_id=null)
	{
		$this->db->select('
				items.item_id,items.barcode,items.code,items.name,items.uom
			');
		$this->db->from('items');
		if (!is_null($item_id)) {
			if (is_array($item_id))
				$this->db->where_in('items.item_id',$item_id);
			else
				$this->db->where('items.item_id',$item_id);
		}
		$this->db->order_by('items.name ASC');
		$query = $this->db->get();
		return $query->result();
	}
	public function add_item($items)
	{
		$this->db->set('reg_date','NOW()',FALSE);
		$this->db->insert('items',$items);
		return $this->db->insert_id();
	}
	public function update_item($items,$item_id)
	{
		$this->db->set('update_date','NOW()',FALSE);
		$this->db->where('item_id',$item_id);
		$this->db->update('items',$items);
	}
	public function get_dtr($from, $to)
	{
		$this->db->select("logs.*, users.username, users.fname, users.mname, users.lname");
		$this->db->from("logs");
		$this->db->join("users", "logs.user_id = users.id");
		$this->db->where("datetime >=", $from);
		$this->db->where("datetime <=", $to);
		$this->db->where_in("type", array("login", "logout"));
		$this->db->order_by("logs.datetime ASC");
		$q = $this->db->get();
		// echo $this->db->last_query();
		return $q->result();
	}
	public function get_total_hours($from, $to)
	{
		// $this->db->select("logs.action,time_to_sec(timediff(max(datetime), min(datetime) )) / 3600");
		// $this->db->from("logs");
		// $this->db->join("users", "logs.user_id = users.id");
		// $this->db->where("datetime >=", $from);
		// $this->db->where("datetime <=", $to);
		// $this->db->where_in("type", array("login", "logout"));
		// $this->db->group_by("DATE_FORMAT(datetime,'%Y-%m-%d'), user_id");
		// $q = $this->db->get();
		$sql = "SELECT `logs`.`action`, users.fname, users.mname, users.lname,
							  time_to_sec(timediff(max(datetime), min(datetime) )) / 3600 as time_to_sec
				FROM (`logs`) 
				JOIN `users` ON `logs`.`user_id` = `users`.`id` 
				WHERE `datetime` >= '".$from."' AND `datetime` <= '".$to."' 
				AND `type` IN ('login', 'logout') 
				GROUP BY DATE_FORMAT(datetime, '%Y-%m-%d'), `user_id` 
				ORDER BY `logs`.`action` ASC";
		$q = $this->db->query($sql);
		// echo $this->db->last_query();
		return $q->result_array();
	}

	// Rod's Model
	// For iPOS Reports
	public function get_daily_sales($branch_code, $date)
	{
		$trans_sales = array();
		$this->db->from("trans_sales ts");
		$this->db->where("branch_code", $branch_code);
		$this->db->where("DATE_FORMAT(datetime, '%Y-%m-%d') = ", $date);
		$this->db->where("type_id", 10);
		$this->db->where("inactive", 0);
		$this->db->order_by("trans_ref asc");

		$ts_get = $this->db->get();
		$ts_res  = $ts_get->result();		

		foreach ($ts_res as $k => $sale) 
		{
			
			// $total_guest = $this->get_total_guest($branch_code, $sale->sales_id);
			$vat = $this->get_vat($branch_code, $sale->sales_id);
			$vat_ex = $this->get_vat_ex($branch_code, $sale->sales_id);
			$zero_rated = $this->get_zero_rated($branch_code, $sale->sales_id);
			$trans_sales_discounts = $this->get_trans_sales_discount($branch_code, $sale->sales_id);
			$service_charge = $this->get_service_charge($branch_code, $sale->sales_id);
			$modifiers_amount = $this->get_trans_sales_menu_modifiers($branch_code, $sale->sales_id);
			$vat_disc_amt = 0;			
			$disc_amt = 0;
			$total_amount_discounted = 0;
			$SNDISC = 0;
			$PWDISC = 0;
			$OTHERSDISC = 0;
			foreach ($trans_sales_discounts as $kDisc => $disc) 
			{				
				$disc_amt += $disc->amount;
				if($disc->disc_code == 'SNDISC')
				{
					$SNDISC += $disc->amount;
				}elseif($disc->disc_code == "PWDISC"){
					$PWDISC += $disc->amount;
				}else{
					$OTHERSDISC += $disc->amount;
				}

			}

			$less_vat = $vat_ex * .12;
			// $less_vat += $zero_rated * .12;
			$total_amount_discounted  = $less_vat + $disc_amt;
			$total_sales = ($sale->total_amount + $total_amount_discounted) - $service_charge;
			$vat_sale = $total_sales - ($vat_ex + $vat + $less_vat + $OTHERSDISC);
			$vat_ex -= $zero_rated;

			$trans_sales[]	= array("reference"	=>	$sale->trans_ref,
									"total_sales"	=>	$total_sales + $modifiers_amount,
									"vat_sale"	=>	$vat_sale,
									"vat"	=>	$vat,
									"vat_ex"	=>	$vat_ex,
									"zero_rated"	=>	$zero_rated,
									"sndisc"	=>	$SNDISC, 
									"pwdisc"	=>	$PWDISC, 
									"othersdisc"	=>	$OTHERSDISC, 
									"less_vat"	=>	$less_vat,
									"total_amount_discounted"	=>	$total_amount_discounted,
									"service_charge"	=>	$service_charge, 
									"modifiers_amount"	=>	$modifiers_amount, 
									"net_amount"	=>	$sale->total_amount,
								);
		}
		return $trans_sales;
	}
	public function get_total_guest($branch_code, $sales_id)
	{
		// get total quest
		$this->db->select("guest");
		$this->db->from("trans_sales");
		$this->db->where("branch_code", $branch_code);
		$this->db->where("sales_id", $sales_id);
		$this->db->where("inactive", 0);
		$get = $this->db->get();
		$res = $get->result();

		$total_guest = 0;
		if(isset($res[0]))
		{
			$total_guest += $res[0]->guest;
		}		

		return $total_guest;
	}
	public function get_vat_ex($branch_code, $sales_id)
	{
		$this->db->select_sum("amount");
		$this->db->from("trans_sales_no_tax");
		$this->db->where("branch_code", $branch_code);
		$this->db->where("sales_id", $sales_id);

		$get = $this->db->get();
		$res = $get->result();

		$vat_ex = 0;
		if(isset($res[0]))
		{
			$vat_ex = $res[0]->amount;
		}
		return $vat_ex;
	}
	public function get_vat($branch_code, $sales_id)
	{
		$this->db->select_sum("amount");
		$this->db->from("trans_sales_tax");
		$this->db->where("branch_code", $branch_code);
		$this->db->where("sales_id", $sales_id);

		$get = $this->db->get();
		$res = $get->result();

		$vat = 0;
		if(isset($res[0]))
		{
			$vat = $res[0]->amount;
		}
		return $vat;	
	}
	public function get_zero_rated($branch_code, $sales_id)
	{
		$this->db->select_sum("amount");
		$this->db->from("trans_sales_zero_rated");
		$this->db->where("branch_code", $branch_code);
		$this->db->where("sales_id", $sales_id);

		$get = $this->db->get();
		$res = $get->result();

		$zero_rated = 0;
		if(isset($res[0]))
		{
			$zero_rated = $res[0]->amount;
		}
		return $zero_rated;	
	}
	public function get_trans_sales_discount($branch_code, $sales_id)
	{		
		$this->db->from("trans_sales_discounts");
		$this->db->where("branch_code", $branch_code);
		$this->db->where("sales_id", $sales_id);		

		$get = $this->db->get();
		$res = $get->result();

		return $res;
	}
	public function get_service_charge($branch_code, $sales_id)
	{
		$this->db->select();
		$this->db->from("trans_sales_charges");
		$this->db->where("branch_code", $branch_code);
		$this->db->where("sales_id", $sales_id);		

		$get = $this->db->get();
		$res = $get->result();

		$service_charge_amt = 0;
		foreach ($res as $k => $v) {
			$service_charge_amt += $v->amount;
		}

		return $service_charge_amt;
	}
	public function get_trans_sales_menu_modifiers($branch_code, $sales_id)
	{
		$this->db->select();
		$this->db->from("trans_sales_menu_modifiers");
		$this->db->where("branch_code", $branch_code);
		$this->db->where("sales_id", $sales_id);

		$get = $this->db->get();
		$res = $get->result();

		$modifiers_amount = 0;
		foreach ($res as $k => $v) {
			$modifiers_amount += $v->qty * $v->price;
		}

		return $modifiers_amount;
	}

	public function get_monthly_sales($branch_code, $month, $year)
	{
		$selected_month = $year."-".$month."-01";		
		$ctr_date = $year."-".$month."-01";		
		$trans_sales = array();
		while (date("Y-m-d", strtotime($ctr_date)) <= date("Y-m-t", strtotime($selected_month))) 
		{

			$this->db->select();
			$this->db->from("trans_sales ts");
			$this->db->where("branch_code", $branch_code);
			$this->db->where("DATE_FORMAT(datetime, '%Y-%m-%d') = ", $ctr_date);
			$this->db->where("type_id", 10);
			$this->db->where("inactive", 0);
			$this->db->order_by("trans_ref asc");

			$ts_get = $this->db->get();
			$ts_res  = $ts_get->result();	

			foreach ($ts_res as $kSale => $v) 
			{
				if(array_key_exists($ctr_date, $trans_sales))
				{

				}else{
					$trans_sales[$ctr_date] = array("or_beg"	=>	0,
													"or_end"	=>	0,
													"or_total"	=>	0,
													"accumulating_beg"	=>	0,	
													"accumulating_end"	=>	0,
													"accumulating_total"	=>	0,
													"sencit"	=>	0,
													"pwd"	=>	0,	
													"vat_disc"	=>	0,	
													"employee_disc"	=>	0,
													"z_read_counter"	=>	0,	
													"vat_sales"	=>	0,	
													"vat_ex"	=>	0,	
													"zero_rated"	=>	0,
													"vat"	=>	0,	
													"discount"	=>	0,
													"charges"	=>	0,
													"net_sales"	=> 0,
											);									
				}
			}
			
			$ctr_date = date("Y-m-d", strtotime($ctr_date. "+1 day"));
		}
		echo '<pre>', print_r($trans_sales), '</pre>';die();
	}

	public function get_promo($from,$to,$discount_id)
	{
		
		$this->db->select('count(*) qty, disc_code, date(trans_sales.datetime) date, trans_sales.branch_code');
		$this->db->from("trans_sales_discounts");
		$this->db->join("trans_sales", "trans_sales.sales_id = trans_sales_discounts.sales_id && trans_sales.branch_code = trans_sales_discounts.branch_code");
		
		$this->db->where("trans_sales.datetime >=", $from);
		$this->db->where("trans_sales.datetime <=", $to);
		$this->db->where("trans_sales.type_id", 10);
		$this->db->where("trans_sales.trans_ref is not null");
	
		$this->db->where("trans_sales.inactive", 0);

		if($discount_id != ''){
			$this->db->where("trans_sales_discounts.disc_code", $discount_id);
		}

		$this->db->group_by("trans_sales.branch_code, DATE(trans_sales.datetime)");

		$get = $this->db->get();
		$result = $get->result();			

		return $result;
	}
	public function get_item_inv_and_locs($date=null)
	{
		$prepare = '
			SELECT
				GROUP_CONCAT(
					\'SUM(IF(item_moves.loc_id =\',
					locations.loc_id,
					\', qty, NULL)) as "!!Loc-\',
					locations.loc_name, \'"\'
				) as msql
			FROM locations;
			';
		$query = $this->db->query($prepare);
		$query = $query->result();

		$prepped = $query[0]->msql;

		if (empty($prepped))
			return null;

		$sql = '
			SELECT
				-- item_moves.item_id,
				items.item_id,
				items.code,
				items.name,
				items.uom,
				SUM(item_moves.qty) as qoh,
				SUM( item_moves.qty * items.cost ) as val_inv,
				locations.loc_name,
				categories.name as cat_name,
				subcategories.name as sub_cat_name
			FROM items
			LEFT JOIN item_moves ON item_moves.item_id = items.item_id
			LEFT JOIN locations ON item_moves.loc_id = locations.loc_id
			LEFT JOIN categories ON categories.cat_id = items.cat_id
			LEFT JOIN subcategories ON subcategories.sub_cat_id = items.subcat_id
			WHERE items.inactive = "0"';
		// if($date){
		// 	$sql .=' and item_moves.reg_date <= "'.$date.' 23:59:59" ';
		// }
		$sql .=' GROUP BY items.item_id';
		// echo print_r($sql);die();
		$r_query = $this->db->query($sql);
		return $r_query->result_array();
		// return $r_query;
	}

	public function get_item_sales($args=array()){
		$this->db->select('trans_sales_items.*,items.code as item_code,items.name as item_name,items.cost as item_cost, items.cat_id,categories.name as cat_name');
		$this->db->from('trans_sales_items');
		$this->db->join('items','trans_sales_items.item_id = items.item_id');
		$this->db->join('categories','items.cat_id = categories.cat_id');
		$this->db->join('trans_sales','trans_sales_items.sales_id = trans_sales.sales_id');
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
		// $this->db->group_by('menus.menu_id');
		$query = $this->db->get();
		// echo $this->db->last_query();die();
		return $query->result();
	}

	function check_trans_zread($start,$end,$branch_code,$brand=''){		

        $read_date = date2Sql($start);
        ###########################################################################
        //JEDN
        $this->load->model('dine/setup_model');
        $details = $this->setup_model->get_branch_details($branch_code);
            
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;

        $pos_start = date2SqlDateTime($read_date." ".$open_time);
        $oa = date('a',strtotime($open_time));
        $ca = date('a',strtotime($close_time));
        $pos_end = date2SqlDateTime($read_date." ".$close_time);

        $date_from = $start." ".$open_time;
        $date_to = $end." ".$close_time;

        if($oa == $ca){
            $pos_end = date('Y-m-d H:i:s',strtotime($pos_end . "+1 days"));
        }

        // $args["DATE(shifts.check_in) = DATE('".date2Sql($read_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
        $args["shifts.check_in >= '".$pos_start."' and shifts.check_in <= '".$pos_end."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $select = "shifts.*";
        $shifts = $this->site_model->get_tbl('shifts',$args,array('check_in'=>'desc'),null,true,$select);
        $shts = array();
        foreach ($shifts as $shft) {
            $shts[] = $shft->shift_id;
        }
        ###########################################################################
        $args =  array(
                    'trans_sales.inactive' => 0,
                    'trans_sales.type_id' => SALES_TRANS,
                    "trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where','val'=>null,'third'=>false)
                  );
        if(count($shts) > 0){
             $args["trans_sales.shift_id"] = array('use'=>'where_in','val'=>$shts,'third'=>false);
        }
        else{
             $args["trans_sales.datetime >="] = $date_from;
             $args["trans_sales.datetime <="] = $date_to;
        }    
  
  		if($branch_code != ''){
  			 $args["trans_sales.branch_code"] = $branch_code;
  		}

  		if($brand != ''){
  			 $args["trans_sales.pos_id"] = $brand;
  		}

        $orders = $this->cashier_model->get_trans_sales(
            null,
            $args,
            'asc'
        ); 

        $total = 0;
        foreach ($orders as $val) {
            $total += $val->total_amount;
        }
       

        return $total;
	}

	function get_zread_amount($date,$branch_code,$brand=''){
		$this->db->select('sum(amount) as amount');
		$this->db->from("store_zread");
		
		$this->db->where("trans_date", $date);
		$this->db->where("branch_code", $branch_code);

		if($brand != ''){
			$this->db->where("pos_id", $brand);
		}

		$this->db->group_by('zread_id,branch_code,pos_id');

		$get = $this->db->get();
		$result = $get->result();			

		return $result ? $result[0]->amount : 0;
	}
	public function get_bad_order_rep($sdate, $edate, $terminal_id=null)
	{
		$this->db->select("tbo.*,menu.costing as cost,menu.cost as srp,tbor.qty,tbor.item_id,tbor.merch_type,menu.menu_name,menu.menu_code",false);
		// $this->db->from("trans_bad_order tbo");
		$this->db->from("trans_adjustment_menu tbo");
		$this->db->join("trans_adjustment_menu_details tbor", "tbor.adjustment_id = tbo.adjustment_id ","left");
		// $this->db->join("trans_bad_order_details tbor", "tbor.spoil_id = tbo.spoil_id ","left");
		$this->db->join("menus menu", "tbor.item_id = menu.menu_id ","left");
		// $this->db->join("members_type mt", "mt.id = cus.member_type_id ","left");
		// $this->db->join('terminals','ts.terminal_id = terminals.terminal_id',"left");	
		// $this->db->join('trans_sales_payments tsp','ts.sales_id = tsp.sales_id',"left");	

		$this->db->where("tbo.trans_date >=", $sdate);		
		$this->db->where("tbo.trans_date <", $edate);
		// $this->db->where("ts.type_id", 10);
		// $this->db->where("ts.trans_ref is not null");
 		$this->db->where("tbo.type", 5);
 		$this->db->where("tbo.inactive", 0);
 		// if(CONSOLIDATOR){
 		// 	if($terminal_id != null){
 		// 		$this->db->where("ts.terminal_id", $terminal_id);
 		// 	}
 		// }else{
 		// 	$this->db->where("ts.terminal_id", TERMINAL_ID);
 		// }	
 		// $this->db->group_by("menu.menu_id,trans_ref");
		$q = $this->db->get();
		// echo $this->db->last_query();die();
		$result = $q->result();
		return $result;
	}
	public function get_half_price_rep($sdate, $edate, $terminal_id=null)
	{
		if(CONSOLIDATOR){
				$this->db->select("tsm.*,sum(tsm.qty * tsm.price) as price,sum(tsm.qty) as qty,pd.promo_code,pd.promo_name,menu.menu_name,menu.menu_cat_id,menu.menu_code,ts.datetime as tsdate",false);
				$this->db->from("trans_sales ts");
				$this->db->join("trans_sales_menus tsm", "tsm.sales_id = ts.sales_id && tsm.pos_id = ts.terminal_id");
		}else{
				$this->db->select("tsm.*,sum(tsm.qty * tsm.price) as price,sum(tsm.qty) as qty,pd.promo_code,pd.promo_name,menu.menu_name,menu.menu_cat_id,menu.menu_code,ts.datetime as tsdate",false);
				$this->db->from("trans_sales ts");
				$this->db->join("trans_sales_menus tsm", "tsm.sales_id = ts.sales_id");	
		}
		$this->db->join("promo_discounts pd", "tsm.promo_id = pd.promo_id ");
		$this->db->join("menus menu", "tsm.menu_id = menu.menu_id ");
		// $this->db->join("menus menu", "tbor.item_id = menu.menu_id ","left");
		
		
		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where('type_id',10);
		$this->db->where('ts.inactive', 0);
		$this->db->where("ts.trans_ref is not null");
		$this->db->where("ts.void_ref is null");
		$this->db->where('pd.promo_code', '50PERCENTPROMO');
		$this->db->where('ts.type', 'takeout');
		$this->db->where('tsm.promo_id', 2);
 		if(CONSOLIDATOR){
 			if($terminal_id != null){
 				$this->db->where("ts.terminal_id", $terminal_id);
 			}
 		}else{
 			$this->db->where("ts.terminal_id", TERMINAL_ID);
 		}	
 		$this->db->group_by("menu.menu_id");
		$q = $this->db->get();
		// echo $this->db->last_query();die();
		$result = $q->result();
		return $result;
	}
}