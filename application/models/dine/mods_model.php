<?php
class Mods_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	public function get_modifiers($id=null,$bcode=null){
		$this->db->trans_start();
			$this->db->select('modifiers.*');
			$this->db->from('modifiers');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('modifiers.mod_id',$id);
					$this->db->where_in('modifiers.branch_code',$bcode);
				}else{
					$this->db->where('modifiers.mod_id',$id);
					$this->db->where_in('modifiers.branch_code',$bcode);
				}
			$this->db->order_by('modifiers.name desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		// return $this->db->last_query();
		return $result;
	}
	public function add_modifiers($items){
		$this->db->trans_start();
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('modifiers',$items);
		$x=$this->db->insert_id();
		$this->db->trans_complete();
		return $x;
	}
	public function update_modifiers($user,$id,$bcode=null){
		$this->db->trans_start();
		$this->db->where('mod_id', $id);
		if($bcode != null){
			$this->db->where('branch_code', $bcode);	
		}
		$this->db->update('modifiers', $user);
		$this->db->trans_complete();
		return $this->db->last_query();
	}
	public function update_group_modifiers($items,$mod_group_id,$bcode){
		$this->db->trans_start();
		$this->db->set('update_date','NOW()',FALSE);
		$this->db->where('mod_group_id',$mod_group_id);
		$this->db->where('branch_code',$bcode);
		$this->db->update('modifier_groups',$items);
		$this->db->trans_complete();
	}
	public function search_items($search="",$branch_code=null){
		$this->db->trans_start();
			$this->db->select('items.item_id,items.code,items.barcode,items.name');
			$this->db->from('items');

			if($search != ""){
				$this->db->like('items.name', $search); 
				$this->db->or_like('items.code', $search); 
				$this->db->or_like('items.barcode', $search);
				$this->db->or_like('items.branch_code', $branch_code); 
			}
			$this->db->group_by("items.item_id");
			$this->db->order_by('items.name');
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
 	public function get_last_mod_recipe_id(){
		$this->db->trans_start();
			$this->db->select('mod_recipe_id');
			$this->db->from('modifier_recipe');
			$this->db->order_by('mod_recipe_id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->mod_recipe_id;
		}else{
			return 0;
		}
	}
	public function get_modifier_recipe($id=null,$mod_id=null,$item_id=null,$branch_code=null){
		$this->db->trans_start();
			$this->db->select('modifier_recipe.*,items.code,items.name');
			$this->db->from('modifier_recipe');
			$this->db->join('items','modifier_recipe.item_id=items.item_id AND modifier_recipe.branch_code = items.branch_code');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('modifier_recipe.mod_recipe_id',$id);
				}else{
					$this->db->where('modifier_recipe.mod_recipe_id',$id);
				}
			if($branch_code != null)
				if(is_array($branch_code))
				{
					$this->db->where_in('modifier_recipe.branch_code',$branch_code);
				}else{
					$this->db->where('modifier_recipe.branch_code',$branch_code);
				}
			if($mod_id != null){
				$this->db->where_in('modifier_recipe.mod_id',$mod_id);
			}
			if($item_id != null){
				$this->db->where('modifier_recipe.item_id',$item_id);
			}
			$this->db->group_by("modifier_recipe.mod_recipe_id");
			// $this->db->order_by('items.name desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		// return $this->db->last_query();
		return $result;
	}
	public function add_modifier_recipe($items){
		$this->db->insert('modifier_recipe',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function add_bulk_modifier_recipe($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert_batch('modifier_recipe', $items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_modifier_recipe($user,$id,$branch=null){
		$this->db->where('mod_recipe_id', $id);
		if($branch != null){
			$this->db->where('branch_code', $branch);

		}
		$this->db->update('modifier_recipe', $user);

		return $this->db->last_query();
	}
	public function delete_modifier_recipe_item($id){
		$this->db->where('mod_recipe_id', $id);
		$this->db->delete('modifier_recipe'); 
	}
	public function get_modifier_recipe_prices($id=null,$mod_id=null,$item_id=null,$branch_code){
		$this->db->trans_start();
			$this->db->select('modifier_recipe.item_id,modifier_recipe.qty,modifier_recipe.cost');
			$this->db->from('modifier_recipe');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('modifier_recipe.mod_recipe_id',$id);
				}else{
					$this->db->where('modifier_recipe.mod_recipe_id',$id);
				}
			if($branch_code != null)
				if(is_array($branch_code))
				{
					$this->db->where_in('modifier_recipe.branch_code',$branch_code);
				}else{
					$this->db->where('modifier_recipe.branch_code',$branch_code);
				}

			if($mod_id != null){
				$this->db->where('modifier_recipe.mod_id',$mod_id);
			}
			if($item_id != null){
				$this->db->where('modifier_recipe.item_id',$item_id);
			}
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		// return $this->db->last_query();
		return $result;
	}
	public function get_modifier_groups($id=null){
		$this->db->trans_start();
			$this->db->select('modifier_groups.*');
			$this->db->from('modifier_groups');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('modifier_groups.mod_group_id',$id);
				}else{
					$this->db->where('modifier_groups.mod_group_id',$id);
				}
			$this->db->order_by('modifier_groups.name desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_modifier_groups($items){
		$this->db->trans_start();
		$this->db->insert('modifier_groups',$items);
		$x=$this->db->insert_id();
		$this->db->trans_complete();
		return $x;
	}
	public function update_modifier_groups($user,$id,$bcode){
		$this->db->trans_start();
		$this->db->where('mod_group_id', $id);
		$this->db->where('branch_code', $bcode);
		$this->db->update('modifier_groups', $user);
		$this->db->trans_complete();
		return $this->db->last_query();
	}
	public function get_modifier_group_details($id=null,$mod_group_id=null,$mod_id=null,$branch_code=null,$group=false){
		$this->db->trans_start();
			$this->db->select('modifier_group_details.*,modifiers.name as mod_name,modifiers.cost as mod_cost,modifiers.inactive as mod_inactive');
			$this->db->from('modifier_group_details');
			$this->db->join('modifiers','modifier_group_details.mod_id = modifiers.mod_id');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('modifier_group_details.id',$id);
				}else{
					$this->db->where('modifier_group_details.id',$id);
				}
			if($mod_id != null)	
				$this->db->where('modifier_group_details.mod_id',$mod_id);
			if($branch_code != null)	
				$this->db->where('modifiers.branch_code',$branch_code);
			if($mod_group_id != null)	
				if(is_array($mod_group_id))
					$this->db->where_in('modifier_group_details.mod_group_id',$mod_group_id);
				else
					$this->db->where('modifier_group_details.mod_group_id',$mod_group_id);
			if($group == true){
				$this->db->group_by('modifier_group_details.mod_group_id,modifier_group_details.id');	
			}	
			$query = $this->db->get();
			$result = $query->result();
			// echo $this->db->last_query();die();
		$this->db->trans_complete();
		return $result;
	}
	public function add_modifier_group_details($items){
		$this->db->trans_start();
		$this->db->insert('modifier_group_details',$items);
		$x=$this->db->insert_id();
		$this->db->trans_complete();
		return $x;
	}
	public function update_modifier_group_details($user,$id){
		$this->db->trans_start();
		$this->db->where('id', $id);
		$this->db->update('modifier_group_details', $user);
		$this->db->trans_complete();
		return $this->db->last_query();
	}
	public function delete_modifier_group_details($id){
		$this->db->where('id', $id);
		// $this->db->where('branch_code', $branch);
		$this->db->delete('modifier_group_details'); 
	}
	public function update_modifiers_upd_date($items=array(),$mod_id){
		$this->db->trans_start();
		$this->db->set('update_date','NOW()',FALSE);
		$this->db->where('mod_group_id',$mod_id);
		// $this->db->where('branch_code',$bcode);
		$this->db->update('modifier_groups',$items);
		// echo $this->db->last_query();die();
		$this->db->trans_complete();
	}
	public function search_modifiers($search=""){
		$this->db->trans_start();
			$this->db->select('modifiers.mod_id,modifiers.name');
			$this->db->from('modifiers');
			if($search != ""){
				$this->db->like('modifiers.name', $search); 
			}
			$this->db->order_by('modifiers.name');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function get_last_modifier_id($branch_code=null){
		$this->db->trans_start();
			$this->db->select_max('mod_id');
			$this->db->from('modifiers');
			$this->db->where('branch_code',$branch_code);
			$this->db->order_by('mod_id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
			// echo $this->db->last_query();die();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->mod_id;
		}else{
			return 0;
		}
	}

	public function get_last_modifier_group_id($branch_code=null){
		$this->db->trans_start();
		$this->db->select_max('mod_group_id');
		$this->db->from('modifier_groups');
		$this->db->where('branch_code',$branch_code);
		$this->db->order_by('mod_group_id desc');
		$this->db->limit('1');
		$query = $this->db->get();
		$result = $query->result();
		// echo $this->db->last_query();die();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->mod_group_id;
		}else{
			return 0;
		}
	}
	public function get_last_modifier_det_id($branch_code=null){
		$this->db->trans_start();
		$this->db->select_max('id');
		$this->db->from('modifier_group_details');
		$this->db->where('branch_code',$branch_code);
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

	public function get_last_modDet_id($branch_code=null){
		$this->db->trans_start();
			$this->db->select_max('id');
			$this->db->from('modifier_group_details');
			$this->db->where('branch_code',$branch_code);
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
	public function get_modifier_sub($id=null,$mod_id=null){
		$this->db->trans_start();
			$this->db->select('modifier_sub.*');
			$this->db->from('modifier_sub');
			
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('modifier_sub.mod_sub_id',$id);
				}else{
					$this->db->where('modifier_sub.mod_sub_id',$id);
				}
			if($mod_id != null){
				$this->db->where_in('modifier_sub.mod_id',$mod_id);
			}
			if($mod_id != null){
				$this->db->where('modifier_sub.mod_id',$mod_id);
			}
			$this->db->group_by("modifier_sub.mod_sub_id");
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function add_modifier_sub($items){
		$this->db->insert('modifier_sub',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_modifier_sub($items,$id){
		$this->db->where('mod_sub_id', $id);
		$this->db->update('modifier_sub', $items);

		return $this->db->last_query();
	}
	public function delete_modifier_sub($id){
		$this->db->where('mod_sub_id', $id);
		$this->db->delete('modifier_sub'); 
	}

	public function get_mod_prices($mod_id=null,$id=null,$branch_code=null){
			$this->db->select('modifier_prices.*,transaction_types.trans_name');			
			$this->db->from('modifier_prices');
			$this->db->join('transaction_types','transaction_types.trans_name=modifier_prices.trans_type');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('modifier_prices.trans_type',$id);
				}else{
					$this->db->where('modifier_prices.trans_type',$id);
				}
			if($branch_code != null)
				$this->db->where('modifier_prices.branch_code',$branch_code);
			if($mod_id != null)
				$this->db->where_in('modifier_prices.mod_id',$mod_id);
			
			// if($mod_group_id != null)
			// 	$this->db->where_in('menu_modifiers.mod_group_id',$mod_group_id);
			// $this->db->order_by('id desc');
			$this->db->group_by("modifier_prices.id");
			$query = $this->db->get();
			$result = $query->result();
		return $result;
	}

	public function add_mod_price($items)
	{
		$this->db->trans_start();
		$this->db->insert('modifier_prices',$items);
		$id = $this->db->insert_id();
		$this->db->trans_complete();
		return $id;
	}
	public function remove_mod_price($id)
	{
		$this->db->trans_start();
		$this->db->where('id',$id);
		$this->db->delete('modifier_prices');
		$this->db->trans_complete();
	}

	public function get_mod_sub_prices($mod_sub_id=null,$id=null,$branch_code=null){
			$this->db->select('modifier_sub_prices.*,transaction_types.trans_name');			
			$this->db->from('modifier_sub_prices');
			$this->db->join('transaction_types','transaction_types.trans_name=modifier_sub_prices.trans_type');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('modifier_sub_prices.trans_type',$id);
				}else{
					$this->db->where('modifier_sub_prices.trans_type',$id);
				}
			if($branch_code != null)
				$this->db->where('modifier_sub_prices.branch_code',$branch_code);
			if($mod_sub_id != null)
				$this->db->where_in('modifier_sub_prices.mod_sub_id',$mod_sub_id);
			// if($mod_group_id != null)
			// 	$this->db->where_in('menu_modifiers.mod_group_id',$mod_group_id);
			// $this->db->order_by('id desc');
			$this->db->group_by("modifier_sub_prices.trans_type");
			$query = $this->db->get();
			$result = $query->result();
		return $result;
	}

	public function add_mod_sub_price($items)
	{
		$this->db->trans_start();
		$this->db->insert('modifier_sub_prices',$items);
		$id = $this->db->insert_id();
		$this->db->trans_complete();
		return $id;
	}
	public function remove_mod_sub_price($id=null,$mod_sub_id=null)
	{
		$this->db->trans_start();

		if($id != null){
			$this->db->where('id',$id);
		}

		if($mod_sub_id != null){
			$this->db->where('mod_sub_id',$mod_sub_id);
		}
		
		$this->db->delete('modifier_sub_prices');
		$this->db->trans_complete();
	}
	public function get_last_modifier_prices($branch_code=null){
		$this->db->trans_start();
			$this->db->select_max('id');
			$this->db->from('modifier_prices');
			$this->db->where('branch_code',$branch_code);
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
	public function get_last_modifier_sub($branch_code=null){
		$this->db->trans_start();
			$this->db->select_max('mod_sub_id');
			$this->db->from('modifier_sub');
			$this->db->where('branch_code',$branch_code);
			$this->db->order_by('mod_sub_id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
			// echo $this->db->last_query();die();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->mod_sub_id;
		}else{
			return 0;
		}
	}
	public function get_last_modifier_sub_price($branch_code=null){
		$this->db->trans_start();
			$this->db->select_max('id');
			$this->db->from('modifier_sub_prices');
			$this->db->where('branch_code',$branch_code);
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
	public function get_mod_prices_detail($trans_type=null,$mod_id=null){
		// $this->db->trans_start();
			$this->db->select('trans_type');
			$this->db->from('modifier_prices');
			$this->db->where('trans_type',$trans_type);
			
			if($mod_id != ''){
			    $this->db->where('mod_id',$mod_id);    
			}
			
			$this->db->group_by("modifier_prices.trans_type");
			$query = $this->db->get();
			$result = $query->result();
			return $result;
		// $this->db->trans_complete();
	}
	public function get_mod_sub_prices_detail($trans_type=null){
		// $this->db->trans_start();
			$this->db->select('trans_type');
			$this->db->from('modifier_sub_prices');
			$this->db->where('trans_type',$trans_type);
			$this->db->group_by("modifier_sub_prices.trans_type");
			$query = $this->db->get();
			$result = $query->result();
			return $result;
		// $this->db->trans_complete();
	}

}
?>