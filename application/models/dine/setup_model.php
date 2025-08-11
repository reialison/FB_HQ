<?php
class Setup_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	public function get_branch_details($branch_id=""){
// 		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('branch_details');
			if($branch_id!=""){
				$this->db->where('branch_details.branch_code',$branch_id);				
			}
			$query = $this->db->get();
			$result = $query->result();
// 		$this->db->trans_complete();
		return $result;
	}
	//-----------Categories-----start-----allyn
	public function get_details($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('branch_details');
			if($id !=''){
				$this->db->where('branch_details.branch_code',$id);	
			}
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function get_details_new($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('branch_details');
			$this->db->where('branch_details.branch_id',$id);
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function update_details($items,$id){
		// $this->db->where('code', $id);
		$this->db->where('branch_id', $id);
		$this->db->update('branch_details', $items);
	}
	public function get_details_per_branch($branch_code=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('branch_details');
			if($branch_code != null){
			$this->db->where('branch_details.branch_code',$branch_code);
			}
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	//-----------Categories-----end-----allyn

	public function get_brands($id = null,$branch_code=null,$brand=null)
	{
		$this->db->from('brands');
		if (!is_null($id)) {
			if (is_array($id))
				$this->db->where_in('id',$id);
			else
				$this->db->where('id',$id);
		}

		if($branch_code!=''){
			$this->db->where('branch_code',$branch_code);
		}

		if($brand!=''){
			$this->db->where('brand_code',$brand);
		}
		$query = $this->db->get();
		return $query->result();
	}
	public function add_brands($items)
	{
		$this->db->trans_start();
		$this->db->insert('brands',$items);
		$id = $this->db->insert_id();
		$this->db->trans_complete();
		return $id;
	}
	public function add_bulk_brands($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert_batch('brands', $items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_brands($items,$id,$branch_code=null)
	{
		$this->db->trans_start();
		$this->db->where('id',$id);
		if($branch_code != null){
			$this->db->where('branch_code', $branch_code);
		}
		$this->db->update('brands',$items);
		$this->db->trans_complete();
	}
	public function get_last_brands($branch_code=null){
		$this->db->trans_start();
			$this->db->select('id,branch_code');
			$this->db->from('brands');
			$this->db->where("branch_code", $branch_code);
			$this->db->order_by('id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		// echo "<pre>",print_r($result),"</pre>";die();
	// echo $this->db->last_query();die();
		if(isset($result[0])){
			return $result[0]->id;
		}else{
			return 0;
		}
	}

	function get_user_branch(){
		$user = $this->session->userdata('user');

		$this->db->select('branch_details.*');
		$this->db->join('user_branches','user_branches.branch_id = branch_details.branch_id');

		$this->db->where('sys_user_id',$user['sysid']);
        // $this->db->where('sys_user_id',$user['id']);

		return $this->db->get('branch_details')->result();
	}
}
?>