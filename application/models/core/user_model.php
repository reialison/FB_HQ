<?php
class User_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	public function get_users($id=null,$args=array()){
		
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('users');
			if($id != null){
				if(is_array($id))
				{
					$this->db->where_in('users.id',$id);
				}else{
					$this->db->where('users.id',$id);
				}
			}
			if(!empty($args)){
				foreach ($args as $col => $val) {
					if(is_array($val)){
						if(!isset($val['use'])){
							$this->db->where_in($col,$val);
						}
						else{
							$func = $val['use'];
							if(isset($val['third'])){
								if(isset($val['operator'])){
									$this->db->$func($col." ".$val['operator']." ".$val['val']);
								}
								else
									$this->db->$func($col,$val['val'],$val['third']);
							}
							else{
								$this->db->$func($col,$val['val']);
							}
						}
					}
					else
						$this->db->where($col,$val);
				}
			}
			$this->db->order_by('fname');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_users($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('users',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_users($user,$id){
		$this->db->where('id', $id);
		$this->db->update('users', $user);

		return $this->db->last_query();
	}

	public function get_last_user_id($branch_code=null){
		$this->db->trans_start();
		if(empty($branch_code)){

			$this->db->select_max('sysid');
		}else{
			$this->db->select_max('id');
		}
		$this->db->from('users');

		if(empty($branch_code)){	
			$this->db->order_by('id desc');
		}else{
			$this->db->where('branch_code',$branch_code);
			$this->db->order_by('sysid desc');
		}
		$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			if(empty($branch_code)){	
				return $result[0]->sysid;
			}else{
				return $result[0]->id;
			}
		}else{
			return 0;
		}
		$this->db->trans_complete();
	}
	public function search_branches($search="",$branch_code=null){
		$this->db->trans_start();
			$this->db->select('branch_code');
			$this->db->from('branch_details');

			if($search != ""){
				$this->db->like('branch_details.branch_code', $search); 
			}
			$this->db->group_by("branch_details.branch_code");
			$this->db->order_by('branch_details.branch_code');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		// return $this->db->last_query();die();
		return $result;
	}
 	public function get_all_branch(){
		$this->db->select('branch_code');
		$this->db->from('branch_details');
		$query = $this->db->get();
		// echo $this->db->last_query();die();
		return $query->result();
	}
	public function get_user_branches($id=null,$sys_user_id=null,$branch_id=null){
		$this->db->trans_start();
			$this->db->select('user_branches.*,branch_details.branch_id,branch_details.branch_code,users.sysid');
			$this->db->from('user_branches');
			$this->db->join('users','user_branches.sys_user_id = users.sysid');
			$this->db->join('branch_details','user_branches.branch_id = branch_details.branch_id');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('user_branches.id',$id);
				}else{
					$this->db->where('user_branches.id',$id);
				}
			if($sys_user_id != null)	
				$this->db->where('user_branches.sys_user_id',$sys_user_id);
			if($branch_id != null)
				$this->db->where('user_branches.branch_id',$branch_id);
			
			$query = $this->db->get();
			$result = $query->result();
			// echo $this->db->last_query();die();
		$this->db->trans_complete();
		return $result;
	}
	public function new_get_user_branches($id=null,$sys_user_id=null,$branch_code=null){
		$this->db->trans_start();
			$this->db->select('user_branches.*,branch_details.branch_id,branch_details.branch_code,users.sysid');
			$this->db->from('user_branches');
			$this->db->join('users','user_branches.sys_user_id = users.sysid');
			$this->db->join('branch_details','user_branches.branch_id = branch_details.branch_id');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('user_branches.id',$id);
				}else{
					$this->db->where('user_branches.id',$id);
				}
			if($sys_user_id != null)	
				$this->db->where('user_branches.sys_user_id',$sys_user_id);
			if($branch_code != null)
				$this->db->where('users.branch_code',$branch_code);
			
			$query = $this->db->get();
			$result = $query->result();
			// echo $this->db->last_query();die();
		$this->db->trans_complete();
		return $result;
	}
	public function delete_user_branches($id,$branch){
		$this->db->where('sys_user_id', $id);
		$this->db->where('branch_id', $branch);
		$this->db->delete('user_branches'); 
	}
	public function add_user_branches($items){
		// echo "<pre>",print_r($items),"</pre>";die();
		$this->db->trans_start();
		$this->db->set('date_registered', 'NOW()', FALSE);
		$this->db->insert('user_branches',$items);
		$x=$this->db->insert_id();
		$this->db->trans_complete();
		return $x;
	}
	public function update_user_branches($items,$id){
		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update('user_branches', $items);
		$this->db->trans_complete();
		return $this->db->last_query();
	}
	// public function delete_user_branches($id){
	// 	$this->db->where('id', $id);
	// 	// $this->db->where('branch_code', $branch);
	// 	$this->db->delete('user_branches'); 
	// }
	public function get_last_userBranch_id(){
		$this->db->trans_start();
			$this->db->select_max('id');
			$this->db->from('user_branches');
			// $this->db->where('branch_code',$branch_code);
			$this->db->order_by('id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
			// echo $this->db->last_query();die();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->id;
		}else{
			return 0;
		}
	}
	public function update_user_branches_upd_date($items=array(),$sys_user_id){
		$this->db->trans_start();
		// $this->db->set('update_date','NOW()',FALSE);
		$this->db->where('sys_user_id',$sys_user_id);
		// $this->db->where('branch_code',$bcode);
		$this->db->update('user_branches',$items);
		// echo $this->db->last_query();die();
		$this->db->trans_complete();
	}
}
?>