<?php
class Gift_cards_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	public function get_gift_cards($id=null,$getInactive=true,$params=array())
	{
		$this->db->select('*');
		$this->db->from('gift_cards');
		// $this->db->join('categories','items.cat_id = categories.cat_id');
		// $this->db->join('subcategories','items.subcat_id = subcategories.sub_cat_id');
		// $this->db->join('item_types','items.type = item_types.id');
		// $this->db->join('suppliers','items.supplier_id = suppliers.supplier_id');

		if(!empty($params)){
			foreach ($params as $key => $value) {
				if(!empty($value)) {
					$this->db->where($key,$value);
				}
			}
		}
		if (!is_null($id)) {
			if (is_array($id))
				$this->db->where_in('gift_cards.gc_id',$id);
			else
				$this->db->where('gift_cards.gc_id',$id);
		}
		if (!$getInactive)
			$this->db->where('inactive',0);

		$this->db->order_by('gift_cards.gc_id ASC');
		$query = $this->db->get();
		// echo $this->db->last_query();die();
		return $query->result();
	}
	public function get_gift_card_info($cardno=null,$getInactive=true)
	{
		$sql = "SELECT * FROM `gift_cards` WHERE '$cardno' = replace(card_no,'-','') ";
		if (!$getInactive) {
			$sql .= " AND inactive = 0 ";
		}
		$sql .= " ORDER BY gift_cards.gc_id ASC";
		$query = $this->db->query($sql);
		// echo $this->db->last_query();
		return $query->result();
	}
	public function get_all_gift_card_count($cardno=null){
		$sql = "SELECT COUNT(*) as total_count FROM `gift_cards` WHERE '$cardno' = replace(card_no,'-','')";
		$query = $this->db->query($sql);
		// // echo $this->db->last_query();
		// // $total=$this->db->count_all_results();
		return $query->result();
	}
	public function add_gift_cards($items)
	{
		// $this->db->set('reg_date','NOW()',FALSE);
		$this->db->insert('gift_cards',$items);
		return $this->db->insert_id();
	}
	public function update_gift_cards($items,$id)
	{
		// $this->db->set('update_date','NOW()',FALSE);
		$this->db->where('gc_id',$id);
		$this->db->update('gift_cards',$items);
	}

	public function get_gift_cards_rep_retail($sdate, $edate, $branch_code,$brand='')
	{
		$this->db->select("ts.trans_ref as ref,tsm.description_id,tsm.price, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12) as vat_sales, sum(tsm.qty*tsm.price/1.12*.12) as vat, sum(tsm.qty*tsm.price) as gross,ts.branch_code,tsm.gc_from,tsm.gc_to",false);
		$this->db->from("( select * from trans_gc_gift_cards group by gc_gift_card_id, branch_code,pos_id ) tsm");
		$this->db->join("(select * from trans_gc group by gc_id,branch_code,pos_id) ts", "ts.gc_id = tsm.gc_id and ts.branch_code = tsm.branch_code and ts.pos_id = tsm.pos_id");
		if($branch_code != "")
		{
			$this->db->where("ts.branch_code", $branch_code);					
		}

		if($brand != "")
		{
			$this->db->where("ts.pos_id", $brand);					
		}

		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <=", $edate);
		$this->db->where("ts.type_id", 12);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		if(HIDECHIT){
 			$this->db->where("ts.gc_id NOT IN (SELECT gc_id from trans_gc_payments where payment_type = 'chit')");
 		}
 		// if(PRODUCT_TEST){
 		// 	$this->db->where("ts.gc_id NOT IN (SELECT gc_id from trans_gc_payments where payment_type = 'producttest')");
 		// }
		$this->db->group_by("tsm.description_id,ts.trans_ref,ts.branch_code,tsm.pos_id");		
		$this->db->order_by("tsm.description_id ASC");
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();
		return $result;
	}

	public function get_last_gc_id($branch_code=null,$brand=null){
		$this->db->trans_start();
			$this->db->select('gc_id');
			$this->db->from('gift_cards');
			$this->db->order_by('gc_id desc');
			$this->db->limit('1');

			if($branch_code != null){
				$this->db->where('branch_code',$branch_code);
			}

			if($brand != null){
				$this->db->where('brand_id',$brand);
			}

			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->menu_id;
		}else{
			return 0;
		}
	}

	public function new_get_gift_card_brand($id=null,$notAll=false){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('gift_cards');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('gift_cards.gc_id',$id);
				}else{
					$this->db->where('gift_cards.gc_id',$id);
				}
			if($notAll){
				$this->db->where('gift_cards.inactive',0);
			}

			// $this->db->where('gift_cards.description_id is not null');
			// $this->db->order_by('gift_cards.description_id asc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function new_get_gift_cards_rep_retail($sdate, $edate, $gc_type = "")
	{
		$this->db->select("ts.trans_ref as ref,ts.branch_code,tsm.amount,tsm.reference",false);
		$this->db->from("trans_sales_payments tsm");
		$this->db->join("trans_sales ts", "ts.sales_id = tsm.sales_id && ts.pos_id = tsm.pos_id && ts.branch_code = tsm.branch_code");
		// $this->db->join("(select * from gift_cards group by description_id,brand_id) gc", "gc.description_id = tsm.description_id",'left');
		// if($gc_type != "")
		// {
		// 	$this->db->where("tsm.card_no", $gc_type);					
		// }
		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		$this->db->where("tsm.payment_type", 'gc');
 		// if(HIDECHIT){
 		// 	$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_gc_payments where payment_type = 'chit')");
 		// }
 		// if(PRODUCT_TEST){
 		// 	$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_gc_payments where payment_type = 'producttest')");
 		// }
		// $this->db->group_by("tsm.description_id,ts.trans_ref");		
		// $this->db->group_by("tsm.card_no");		
		// $this->db->order_by("tsm.description_id ASC");
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		return $result;
	}
}