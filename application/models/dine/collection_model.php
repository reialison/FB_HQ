<?php
class Collection_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	

	public function get_collections_report($sdate, $edate, $branch_code,$brand='')
	{
		$this->db->select("*",false);
		$this->db->from("( select * from collections group by col_id, branch_code,pos_id ) col");
		$this->db->join("(select * from banks group by bank_id,branch_code) banks", "banks.bank_id = col.bank_id and banks.branch_code = col.branch_code");
		$this->db->join("brands", "brands.id = col.pos_id and brands.branch_code = col.branch_code",'left');
		if($branch_code != "")
		{
			$this->db->where("col.branch_code", $branch_code);					
		}

		if($brand != "")
		{
			$this->db->where("col.pos_id", $brand);					
		}

		$this->db->where("col.deposit_date >=", $sdate);		
		$this->db->where("col.deposit_date <=", $edate);
 		// $this->db->where("ts.inactive", 0);
 		
		$this->db->group_by("col.col_id,col.branch_code,col.pos_id");		
		$this->db->order_by("col.col_id ASC");
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();
		return $result;
	}

}