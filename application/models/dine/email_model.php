<?php
class Email_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
 	public function get_last_email(){
		// $this->db->trans_start();
		$this->db->select('id');
		$this->db->from('email_setting');
		$this->db->order_by('id desc');
		$this->db->limit('1');
		$query = $this->db->get();
		$result = $query->result();
		// $this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->id;
		}else{
			return 0;
		}
	}
	public function get_email_settings($id=null,$notAll=false){
		$this->db->select('*');
		$this->db->from('email_setting');
		if($id != null)
			if(is_array($id))
			{
				$this->db->where_in('email_setting.id',$id);
			}else{
				$this->db->where('email_setting.id',$id);
			}

		if($notAll){
			$this->db->where('email_setting.inactive',0);
		}
		$this->db->order_by('id asc');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
	public function get_main_recipients_main(){
		$this->db->select('*');
		$this->db->from('email_setting');
		$this->db->where('email_setting.types', 1);
		$this->db->where('email_setting.inactive', 0);
		$this->db->order_by('name asc');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
	public function get_main_recipients_cc(){
		$this->db->select('*');
		$this->db->from('email_setting');
		$this->db->where('email_setting.types', 2);
		$this->db->where('email_setting.inactive', 0);
		$this->db->order_by('name asc');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}

	public function check_email($data=null){
		$this->db->select('id,email_address');
		$this->db->from('email_setting');
		$this->db->where('email_address', $data);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}

	public function update_email_set($user,$id){
		$this->db->where('id', $id);
		$this->db->update('email_setting', $user);
		// echo $this->db->last_query();die();
		return $this->db->last_query();
	}
	public function add_email_set($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('email_setting',$items);
		$x=$this->db->insert_id();
		return $x;
	}


	public function get_all_branches($id=null,$notAll=false){
		$this->db->select('branch_code,branch_id');
		$this->db->from('branch_details');
		$this->db->order_by('branch_code asc');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
}
?>