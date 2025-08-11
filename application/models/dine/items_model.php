<?php
class Items_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
		if(LOCALSYNC){
            $this->load->model('core/sync_model');
        }
	}
	public function get_item($item_id=null,$branch_code=null,$args=array())
	{
		$this->db->select('
			items.*,
			categories.name as category,
			subcategories.name as subcategory,
			item_types.type as item_type,
			suppliers.name as supplier
			');
		$this->db->from('items');
		$this->db->join('categories','items.cat_id = categories.cat_id');
		$this->db->join('subcategories','subcategories.sub_cat_id = items.subcat_id','left');
		$this->db->join('item_types','items.type = item_types.id');
		$this->db->join('suppliers','items.supplier_id = suppliers.supplier_id','left');
		if (!is_null($item_id)) {
			if (is_array($item_id))
				$this->db->where_in('items.item_id',$item_id);
			else
				$this->db->where('items.item_id',$item_id);
		}

		if (!is_null($branch_code)) {
			if (is_array($branch_code))
				$this->db->where_in('items.branch_code',$branch_code);
			else
				$this->db->where('items.branch_code',$branch_code);
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
		$this->db->group_by('items.sysid');
		$this->db->order_by('items.name ASC');
		$query = $this->db->get();
		// echo $this->db->last_query();die();
		return $query->result();
	}
	public function get_customers($item_id=null,$args=array())
	{
		$this->db->select('customers.*');
		$this->db->from('customers');
		if (!is_null($item_id)) {
			if (is_array($item_id))
				$this->db->where_in('customers.id',$item_id);
			else
				$this->db->where('customers.id',$item_id);
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
		$this->db->order_by('customers.fname ASC');
		$query = $this->db->get();
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
		$this->db->trans_start();
		$this->db->set('reg_date','NOW()',FALSE);
		$this->db->insert('items',$items);
		return $this->db->insert_id();
		$this->db->trans_complete();
	}
	public function update_item($items,$item_id,$branch_code)
	{
		$this->db->trans_start();
		$this->db->set('update_date','NOW()',FALSE);
		$this->db->where('item_id',$item_id);
		$this->db->where('branch_code',$branch_code);
		$this->db->update('items',$items);
		$this->db->trans_complete();
	}
	public function get_latest_item_move($constraints=array())
	{
		$this->db->select('*');
		$this->db->from('item_moves');
		if (!empty($constraints))
			$this->db->where($constraints);
		$this->db->order_by('reg_date DESC, move_id DESC');
		$query = $this->db->get();
		$row = $query->row();
		$query->free_result();
		return $row;
	}
	public function get_last_item_qty($loc_id=null,$item_id=null){
		$this->db->select('curr_item_qty,item_id,loc_id');
		$this->db->from('item_moves');
		if($loc_id != null){
			$this->db->where('item_moves.loc_id',$loc_id);
		}
		if (!is_null($item_id)) {
			$this->db->where('item_moves.item_id',$item_id);
		}
		$this->db->order_by('reg_date DESC, move_id DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		$query->free_result();
		return $row;
	}
	public function move_items($loc_id,$items,$opts=array()){
		#items must be an array with qty and UOM
		$batch = array();
		foreach ($items as $item_id => $opt) {
			$last = $this->get_last_item_qty($loc_id,$item_id);
			$curr_qty = 0;
			if(count($last) > 0){
				$curr_qty = $last->curr_item_qty;
			}
			$opts['item_id'] = $item_id;
			$opts['qty'] = $opt['qty'];
			if(isset($opt['case_qty']))
				$opts['case_qty'] = $opt['case_qty'];
			if(isset($opt['pack_qty']))
				$opts['pack_qty'] = $opt['pack_qty'];

			$opts['uom'] = $opt['uom'];
			$opts['loc_id'] = $loc_id;
			$opts['curr_item_qty'] = $curr_qty + $opt['qty'];
			$now = $this->site_model->get_db_now();
			$datetime = date2SqlDateTime($now);
			$opts['reg_date'] = $datetime;
			$batch[] = $opts;
		}
		$this->add_item_moves_batch($batch);

		if(LOCALSYNC){
            $this->sync_model->add_item_moves_batch($loc_id);
        }
		// echo var_dump($batch);
	}
	public function add_item_moves_batch($items)
	{
		$this->db->trans_start();
		$this->db->insert_batch('item_moves',$items);
		$this->db->trans_complete();
	}
	public function get_curr_item_inv_and_locs($branch_code = null)
	{
		// $branch_code = "PauliPos";
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
				item_moves.item_id,
				items.code,
				items.name,
				items.uom,
				item_moves.branch_code,
				SUM(item_moves.qty) as qoh,
				locations.loc_name,
				categories.name as cat_name,
				branch_details.branch_name as branch_name,
				subcategories.name as sub_cat_name
			FROM item_moves
			JOIN items ON item_moves.item_id = items.item_id and items.branch_code = "'.$branch_code.'"
			LEFT JOIN locations ON item_moves.loc_id = locations.loc_id
			LEFT JOIN categories ON categories.cat_id = items.cat_id and categories.branch_code = "'.$branch_code.'"
			LEFT JOIN subcategories ON subcategories.sub_cat_id = items.subcat_id and subcategories.branch_code = "'.$branch_code.'"
			LEFT JOIN branch_details ON branch_details.branch_code = item_moves.branch_code and branch_details.branch_code = "'.$branch_code.'"
			WHERE item_moves.inactive = "0"';
		if($branch_code != null){
			$sql .= ' And item_moves.branch_code = "'.$branch_code.'" ';
		}
		$sql .=	' GROUP BY item_moves.item_id,item_moves.branch_code;
		';
		$r_query = $this->db->query($sql);
		// echo $this->db->last_query();die();

		return $r_query;
	}

	public function get_curr_item_menu_and_locs($branch_code = null)
	{
		// $branch_code = "PauliPos";
		$prepare = '
			SELECT
				GROUP_CONCAT(
					\'SUM(IF(menu_moves.loc_id =\',
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
				menu_moves.item_id,
				menus.menu_code,
				menus.menu_name,
				menu_moves.branch_code,
				SUM(menu_moves.qty) as qoh,
				locations.loc_name,
				menu_categories.menu_cat_name as cat_name,
				menu_subcategories.menu_sub_cat_name as sub_cat_name
			FROM menu_moves
			JOIN menus ON menu_moves.item_id = menus.menu_id and menu_moves.branch_code =  menus.branch_code 
			LEFT JOIN locations ON menu_moves.loc_id = locations.loc_id and menu_moves.branch_code =  locations.branch_code
			LEFT JOIN menu_categories ON menu_categories.menu_cat_id = menus.menu_cat_id and menu_categories.branch_code =  menus.branch_code
			LEFT JOIN menu_subcategories ON menu_subcategories.menu_sub_cat_id = menus.menu_sub_cat_id and menu_subcategories.branch_code =  menus.branch_code
			WHERE menu_moves.inactive = "0"';
		if($branch_code != null){
			$sql .= ' And menu_moves.branch_code = "'.$branch_code.'" ';
		}
		$sql .=	' GROUP BY menu_moves.item_id,menu_moves.branch_code;
		';

		// $sql = '
		// 	SELECT
		// 		trans_receiving_menu_details.item_id,
		// 		menus.menu_code,
		// 		menus.menu_name,
		// 		trans_receiving_menu.branch_code,				
		// 		SUM(trans_receiving_menu_details.qty) as qty_received,
		// 		0 ts_qty,
		// 		menu_categories.menu_cat_name as cat_name,
		// 		menu_subcategories.menu_sub_cat_name as sub_cat_name
		// 	FROM trans_receiving_menu
		// 	JOIN trans_receiving_menu_details ON trans_receiving_menu_details.receiving_id = trans_receiving_menu.receiving_id and trans_receiving_menu_details.branch_code =  trans_receiving_menu.branch_code 
		// 	JOIN menus ON trans_receiving_menu_details.item_id = menus.menu_id and trans_receiving_menu_details.branch_code =  menus.branch_code 			
		// 	LEFT JOIN menu_categories ON menu_categories.menu_cat_id = menus.menu_id and menu_categories.branch_code =  menus.branch_code
		// 	LEFT JOIN menu_subcategories ON menu_subcategories.menu_sub_cat_id = menus.menu_id and menu_subcategories.branch_code =  menus.branch_code
		// 	WHERE trans_receiving_menu.inactive = "0"';
		// if($branch_code != null){
		// 	$sql .= ' And trans_receiving_menu.branch_code = "'.$branch_code.'" ';
		// }
		// $sql .=	' GROUP BY trans_receiving_menu_details.item_id,trans_receiving_menu_details.branch_code';

		$r_query = $this->db->query($sql);
		// echo $this->db->last_query();die();

		return $r_query;
	}

	public function get_curr_item_menu_and_trans_sales_menus($branch_code = null)
	{
		// $branch_code = "PauliPos";

		$sql = '
			SELECT	
				trans_sales_menus.menu_id item_id,
				menus.menu_code,
				menus.menu_name,
				trans_sales.branch_code,				
				0 qty_received,
				SUM(trans_sales_menus.qty) as ts_qty,
				menu_categories.menu_cat_name as cat_name,
				menu_subcategories.menu_sub_cat_name as sub_cat_name
			FROM ( select * from trans_sales_menus group by branch_code,sales_menu_id )trans_sales_menus
			INNER JOIN (select * from trans_sales group by branch_code,sales_id)trans_sales ON trans_sales.sales_id = trans_sales_menus.sales_id and trans_sales.branch_code =  trans_sales_menus.branch_code 
			INNER JOIN menus ON menus.menu_id = trans_sales_menus.menu_id and menus.branch_code =  trans_sales_menus.branch_code
			INNER JOIN trans_types ON trans_sales.type_id = trans_types.type_id
			LEFT JOIN menu_categories ON menu_categories.menu_cat_id = menus.menu_id and menu_categories.branch_code =  menus.branch_code
			LEFT JOIN menu_subcategories ON menu_subcategories.menu_sub_cat_id = menus.menu_id and menu_subcategories.branch_code =  menus.branch_code
			
			WHERE trans_sales.trans_ref IS NOT NULL
			AND `trans_sales`.`type_id` =  10
			AND trans_sales.inactive = "0"';

		if($branch_code != null){
		// 	$sql .= ' And menu_moves.branch_code = "'.$branch_code.'" ';
			$sql .= ' And trans_sales_menus.branch_code = "'.$branch_code.'" ';
		}
		
		$sql .=	' GROUP BY trans_sales_menus.menu_id,trans_sales_menus.branch_code;';
		$r_query = $this->db->query($sql);
		// echo $this->db->last_query();die();

		return $r_query;
	}

	public function get_inventory_moves($date=null)
	{
		$prepare ='
			SELECT
				GROUP_CONCAT(
					\'SUM(IF(item_moves.type_id =\',
					trans_types.type_id,
					\',qty,NULL)) as "!!Trans-\',
					trans_types.name,
				\'"\') as msql
			FROM trans_types
		';

		$query = $this->db->query($prepare);
		$query = $query->result();

		$prepped = $query[0]->msql;

		if (empty($prepped))
			return null;

		$sql = '
			SELECT
				items.code,
				items.name,
				items.uom,
				'.$prepped.'
			FROM
				item_moves
			JOIN items ON item_moves.item_id = items.item_id
			JOIN trans_types ON item_moves.type_id = trans_types.type_id
			GROUP BY item_moves.item_id
		';
			// WHERE DATE(item_moves.reg_date) = \''.date('Y-m-d',strtotime($date)).'\'

		$r_query = $this->db->query($sql);
		return $r_query;
	}

	public function get_last_item_id($branch_code=null){
		$this->db->trans_start();
		$this->db->select_max('item_id');
		$this->db->from('items');

		if(!empty($branch_code)){
			$this->db->where('branch_code',$branch_code);
		}
		
		$this->db->order_by('item_id desc');
		$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->item_id;
		}else{
			return 0;
		}
		$this->db->trans_complete();
	}

	public function get_branch_detail($branch_code=null){
		// $this->db->trans_start();
			$this->db->select('branch_code');
			$this->db->from('branch_details');
			$query = $this->db->get();
			$result = $query->result();
			return $result;
		// $this->db->trans_complete();
	}

	public function get_items_code($code=null,$branch_code=null){
		$this->db->trans_start();
		$this->db->select('item_id');
		$this->db->from('items');
		$this->db->where('code',$code);
		$this->db->where('branch_code',$branch_code);
		$this->db->order_by('item_id desc');
		$this->db->limit('1');
		$query = $this->db->get();
		$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->item_id;
		}else{
			return 0;
		}
	}

	public function branch_show_menus($data=array()){
		$this->db->trans_start();
			$this->db->select('branch_code,inactive');
			$this->db->from($data[0]);
			$this->db->where($data[1],$data[2]);
			$query = $this->db->get();
			$result = $query->result();
			// echo $this->db->last_query();die();
			return $result;
		$this->db->trans_complete();
	}

	public function get_item_code_active($icode=null){
		$this->db->trans_start();
		$this->db->select('branch_code,inactive');
		$this->db->from('items');
		$this->db->where('code',$icode);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
		$this->db->trans_complete();
	}

	public function get_cat_code_active($icode=null){
		$this->db->trans_start();
		$this->db->select('branch_code,inactive');
		$this->db->from('categories');
		$this->db->where('code',$icode);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
		$this->db->trans_complete();
	}

	public function get_subcat_code_active($icode=null){
		$this->db->trans_start();
		$this->db->select('branch_code,inactive');
		$this->db->from('subcategories');
		$this->db->where('code',$icode);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
		$this->db->trans_complete();
	}

	public function get_modifier_active($mod_id=null){
		$this->db->trans_start();
		$this->db->select('branch_code,inactive');
		$this->db->from('modifiers');
		$this->db->where('mod_id',$mod_id);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
		$this->db->trans_complete();
	}

	public function get_trans_type_active($trans_id=null){
		$this->db->trans_start();
		$this->db->select('branch_code,inactive');
		$this->db->from('transaction_types');
		$this->db->where('trans_name',$trans_id);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
		$this->db->trans_complete();
	}

	public function get_grp_modifier_active($mod_id=null){
		$this->db->trans_start();
		$this->db->select('branch_code,inactive');
		$this->db->from('modifier_groups');
		$this->db->where('mod_group_id',$mod_id);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
		$this->db->trans_complete();
	}
	public function get_menu_moves_total($item_id=null,$args=array())
	{
		$this->db->select('
			sum(menu_moves.qty) as in_menu_qty
			');
		$this->db->from('menu_moves');
		if (!is_null($item_id)) {
			if (is_array($item_id))
				$this->db->where_in('menu_moves.item_id',$item_id);
			else
				$this->db->where('menu_moves.item_id',$item_id);
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
		$this->db->group_by('menu_moves.item_id');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_item_moves_total($item_id=null,$args=array())
	{
		$this->db->select('
			sum(item_moves.qty) as in_item_qty
			');
		$this->db->from('item_moves');
		if (!is_null($item_id)) {
			if (is_array($item_id))
				$this->db->where_in('item_moves.item_id',$item_id);
			else
				$this->db->where('item_moves.item_id',$item_id);
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
		$this->db->group_by('item_moves.item_id');
		$query = $this->db->get();
		return $query->result();
	}

	public function add_item_history($item)
	{
		$this->db->insert('item_pricing_history',$item);
		return $this->db->insert_id();
		$this->db->trans_complete();
	}
	public function get_price_history($item_id){
    	$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('item_pricing_history');
			$this->db->where("item_id", $item_id);
		$this->db->trans_complete();	
		// $this->db->where('inactive', $inactive);
		$query = $this->db->get();
		// echo $this->db->last_query();die();
		return $query->result();
	}
}