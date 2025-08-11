<?php
class Menu_model extends CI_Model{

	public function __construct()
	{
		parent::__construct();
	}
	public function get_menus($id=null,$cat_id=null,$notAll=false,$search=null,$branch_code=null){
		$this->db->trans_start();
			$this->db->select('menus.*,menu_categories.menu_cat_name as category_name,menu_schedules.desc as menu_schedule_name,branch_name');
			$this->db->from('menus');
			$this->db->join('menu_categories','menus.menu_cat_id = menu_categories.menu_cat_id');
			$this->db->join('menu_schedules','menus.menu_sched_id = menu_schedules.menu_sched_id','left');
			$this->db->join('branch_details','branch_details.branch_code = menus.branch_code');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('menus.menu_id',$id);
				}else{
					$this->db->where('menus.menu_id',$id);
				}
			if($cat_id != null){
				$this->db->where('menus.menu_cat_id',$cat_id);
			}
			if($notAll){
				$this->db->where('menus.inactive',0);
			}
			if($search != null){
				$this->db->like('menu_short_desc', $search);
				$this->db->or_like('menu_name', $search); 
			}
			if($branch_code != null){
				$this->db->where('menus.branch_code',$branch_code);
			}
			$this->db->group_by("menus.sysid");
			$this->db->order_by('menus.menu_name asc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}

	public function add_menus($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('menus',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function add_bulk_menus($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert_batch('menus', $items);
		$x=$this->db->insert_id();
		return $x;
	}

	public function update_menus($user,$id,$branch_code=null){
		$this->db->where('menu_id', $id);
		if($branch_code != null){
			$this->db->where('branch_code', $branch_code);

		}
		$this->db->update('menus', $user);
		// echo $this->db->last_query();die();
		return $this->db->last_query();
	}
	public function get_menu_categories($id=null,$notAll=false,$branch_code=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('menu_categories');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('menu_categories.menu_cat_id',$id);
					$this->db->where_in('menu_categories.branch_code',$branch_code);

				}else{
					$this->db->where('menu_categories.menu_cat_id',$id);
					$this->db->where_in('menu_categories.branch_code',$branch_code);
				}
			if($notAll){
				$this->db->where('menu_categories.inactive',0);
			}
			$this->db->order_by('menu_categories.menu_cat_name asc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_menu_categories($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('menu_categories',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function add_bulk_menus_cat($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert_batch('menu_categories', $items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function add_bulk_menu_recipe($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert_batch('menu_recipe', $items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_menu_categories($user,$id,$branch=null){
		$this->db->where('menu_cat_id', $id);
		if($branch != null){
			$this->db->where('branch_code', $branch);
		}
		$this->db->update('menu_categories', $user);

		return $this->db->last_query();
	}
	public function get_menu_subcategories($id=null,$notAll=false,$branch_code=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('menu_subcategories');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('menu_subcategories.menu_sub_cat_id',$id);
				}else{
					$this->db->where('menu_subcategories.menu_sub_cat_id',$id);
				}
			if($notAll){
				$this->db->where('menu_subcategories.inactive',0);
			}

			if(!empty($branch_code)){
					$this->db->where('menu_subcategories.branch_code',$branch_code);
			}
			$this->db->order_by('menu_sub_cat_id asc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		// echo $this->db->last_query();die();
		return $result;
	}
	public function add_menu_subcategories($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('menu_subcategories',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function add_bulk_menu_subcat($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert_batch('menu_subcategories', $items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_menu_subcategories($user,$id,$branch=null){
		$this->db->where('menu_sub_cat_id', $id);
		if($branch != null){
			$this->db->where('branch_code', $branch);
		}
		$this->db->update('menu_subcategories', $user);
		// echo $this->db->last_query();die();
		return $this->db->last_query();
	}
	public function get_menu_schedules($id=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('menu_schedules');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('menu_schedules.menu_sched_id',$id);
				}else{
					$this->db->where('menu_schedules.menu_sched_id',$id);
				}
			$this->db->order_by('menu_sched_id desc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_menu_schedules($items){
		$this->db->insert('menu_schedules',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_menu_schedules($item,$id){
		$this->db->where('menu_sched_id', $id);
		$this->db->update('menu_schedules', $item);

		return $this->db->last_query();
	}
	public function add_menu_schedule_details($items){
		$this->db->insert('menu_schedule_details',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_menu_schedule_details($item,$id){
		$this->db->where('id', $id);
		$this->db->update('menu_schedule_details', $item);

		return $this->db->last_query();
	}
	public function get_menu_schedule_details($id){
		$this->db->from('menu_schedule_details');
		// if($id != '')
			$this->db->where('menu_sched_id',$id);
		$query = $this->db->get();
		$result = $query->result();

		return $result;
	}
	public function validate_menu_schedule_details($id,$day){
		$this->db->from('menu_schedule_details');
		$this->db->where('menu_sched_id',$id);
		$this->db->where('day',$day);

		// $query = $this->db->get();
		// $result = $query->result();
		return $this->db->count_all_results();
	}
	public function delete_menu_schedule_details($id){
		$this->db->where('id', $id);
		$this->db->delete('menu_schedule_details');
	}
	// public function get_recipe_items($menu_id=null,$item_id=null,$id=null){
	// 	$this->db->trans_start();
	// 		$this->db->select('menu_recipe.*,menus.menu_name as item_name,menus.cost as item_cost');
	// 		$this->db->from('menu_recipe');
	// 		$this->db->join('menus','menu_recipe.menu_id=menus.menu_id');
	// 		$this->db->join('items','items.item_id=menus.menu_id');
	// 		if($id != null)
	// 			if(is_array($id))
	// 			{
	// 				$this->db->where_in('menu_recipe.recipe_id',$id);
	// 			}else{
	// 				$this->db->where('menu_recipe.recipe_id',$id);
	// 			}
	// 		if($menu_id != null)
	// 			$this->db->where_in('menu_recipe.menu_id',$menu_id);
	// 		if($item_id != null)
	// 			$this->db->where_in('menu_recipe.item_id',$item_id);
	// 		$this->db->order_by('recipe_id desc');
	// 		$query = $this->db->get();
	// 		$result = $query->result();
	// 	$this->db->trans_complete();
	// 	return $result;
	// }
	public function get_recipe_items($menu_id,$item_id = null,$id=null,$branch=null)
	{
		$this->db->select('
			menus.menu_code,
			menus.menu_barcode,
			menus.menu_name,
			menus.cost "menu_cost",
			items.item_id,
			items.name "item_name",
			items.barcode "item_barcode",
			items.code "item_code",
			items.cost "item_cost",
			menu_recipe.recipe_id,
			menu_recipe.uom,
			menu_recipe.qty,
			menu_recipe.menu_id,
			menu_recipe.branch_code
			');
		$this->db->from('menu_recipe');
		$this->db->join('menus','menu_recipe.menu_id = menus.menu_id AND menu_recipe.branch_code = menus.branch_code');
		$this->db->join('items','menu_recipe.item_id = items.item_id AND menu_recipe.branch_code = items.branch_code');

		if (is_array($menu_id))
			$this->db->where_in('menu_recipe.menu_id',$menu_id);
		else
			$this->db->where('menu_recipe.menu_id',$menu_id);

		if(!is_null($id)) {
			if(is_array($id))
				$this->db->where_in('menu_recipe.recipe_id',$id);
			else
				$this->db->where('menu_recipe.recipe_id',$id);
		}

		if (!is_null($item_id))
			$this->db->where('menu_recipe.item_id',$item_id);

		if (is_array($branch))
			$this->db->where_in('menu_recipe.branch_code',$branch);
		else
			$this->db->where('menu_recipe.branch_code',$branch);
		if (!is_null($item_id))
			$this->db->where('menu_recipe.item_id',$item_id);

		if (is_array($branch))
			$this->db->where_in('items.branch_code',$branch);
		else
			$this->db->where('items.branch_code',$branch);

		if (is_array($branch))
			$this->db->where_in('menus.branch_code',$branch);
		else
			$this->db->where('menus.branch_code',$branch);

		$this->db->group_by("menu_recipe.recipe_id");
		$this->db->order_by('menus.menu_name ASC, items.name ASC');
		$query = $this->db->get();
		// return $this->db->last_query();

		return $query->result();
	}
	public function get_recipe_items_branch($menu_id,$item_id = null,$id=null,$branch=null)
	{
		$this->db->select('
			menus.menu_code,
			menus.menu_barcode,
			menus.menu_name,
			menus.cost "menu_cost",
			items.item_id,
			items.name "item_name",
			items.barcode "item_barcode",
			items.code "item_code",
			items.cost "item_cost",
			menu_recipe.recipe_id,
			menu_recipe.uom,
			menu_recipe.qty,
			menu_recipe.menu_id,
			menu_recipe.branch_code
			');
		$this->db->from('menu_recipe');
		$this->db->join('menus','menu_recipe.menu_id = menus.menu_id AND menu_recipe.branch_code = menus.branch_code');
		$this->db->join('items','menu_recipe.item_id = items.item_id AND menu_recipe.branch_code = items.branch_code');

		if (is_array($menu_id))
			$this->db->where_in('menu_recipe.menu_id',$menu_id);
		else
			$this->db->where('menu_recipe.menu_id',$menu_id);

		if(!is_null($id)) {
			if(is_array($id))
				$this->db->where_in('menu_recipe.recipe_id',$id);
			else
				$this->db->where('menu_recipe.recipe_id',$id);
		}

		if (!is_null($item_id))
			$this->db->where('menu_recipe.item_id',$item_id);

		if(!is_null($branch)) {
			if (is_array($branch))
				$this->db->where_in('items.branch_code',$branch);
			else
				$this->db->where('items.branch_code',$branch);
		}
		// $this->db->group_by("menu_recipe.recipe_id");
		$this->db->order_by('menus.menu_name ASC, items.name ASC');
		$query = $this->db->get();
		// return $this->db->last_query();

		return $query->result();
	}
	// public function add_recipe_item($items){
	// 	$this->db->insert('menu_recipe',$items);
	// 	$x=$this->db->insert_id();
	// 	return $x;
	// }
	public function add_recipe_item($items)
	{
		$this->db->trans_start();
		$this->db->insert('menu_recipe',$items);
		$this->db->trans_complete();
		$id = $this->db->insert_id();
		return $id;
	}
	// public function update_recipe_item($menu_id=null,$item_id=null){
	// 	$this->db->where('menu_id', $menu_id);
	// 	$this->db->where('item_id', $item_id);
	// 	$this->db->update('menu_recipe', $item);

	// 	return $this->db->last_query();
	// }
	public function update_recipe_item($items,$menu_id,$item_id,$branch_code=null)
	{
		// $this->db->trans_start();
		$this->db->where('menu_id',$menu_id);
		$this->db->where('item_id',$item_id);
		if($branch_code != null){
			$this->db->where('branch_code',$branch_code);
		}
		// $this->db->where(array('menu_id'=>$menu_id,'item_id'=>$item_id,'branch_code',$branch_code));
		$this->db->update('menu_recipe',$items);
		return $this->db->last_query();

		// $this->db->trans_complete(); 
	}
	// public function remove_recipe_item($id){
	// 	$this->db->where('recipe_id', $id);
	// 	$this->db->delete('menu_recipe');
	// }
	public function remove_recipe_item($recipe_id)
	{
		$this->db->trans_start();
		$this->db->where('recipe_id',$recipe_id);
		$this->db->delete('menu_recipe');
		$this->db->trans_complete();
	}
	// public function search_items($search=""){
	// 	$this->db->trans_start();
	// 		$this->db->select('items.item_id,items.code,items.barcode,items.name');
	// 		$this->db->from('items');
	// 		if($search != ""){
	// 			$this->db->like('items.name', $search);
	// 			$this->db->or_like('items.code', $search);
	// 			$this->db->or_like('items.barcode', $search);
	// 		}
	// 		$this->db->order_by('items.name');
	// 		$query = $this->db->get();
	// 		$result = $query->result();
	// 	$this->db->trans_complete();
	// 	return $result;
	// }
	public function search_items($search=""){
		$this->db->trans_start();
			$this->db->select('items.item_id,items.code,items.barcode,items.name');
			$this->db->from('items');
			if($search != ""){
				$this->db->like('items.name', $search);
				$this->db->or_like('items.code', $search);
				$this->db->or_like('items.barcode', $search);
			}
			$this->db->order_by('items.name');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	/********	 	Menu Modifiers 		********/
	public function get_menu_modifiers($menu_id=null,$mod_group_id=null,$id=null,$branch_code=null){
			$this->db->select('menu_modifiers.*,modifier_groups.name as mod_group_name,modifier_groups.mandatory,modifier_groups.multiple');
			$this->db->from('menu_modifiers');
			$this->db->join('modifier_groups','menu_modifiers.mod_group_id=modifier_groups.mod_group_id');
			if($id != null){
				
				if(is_array($id))
				{
					$this->db->where_in('menu_modifiers.id',$id);
				}else{
					$this->db->where('menu_modifiers.id',$id);
				}
				if(is_array($branch_code))
				{
					$this->db->where_in('menu_modifiers.branch_code',$branch_code);
				}else{
					$this->db->where('menu_modifiers.branch_code',$branch_code);
				}
			}
			if($menu_id != null)
				$this->db->where_in('menu_modifiers.menu_id',$menu_id);
			if($mod_group_id != null)
				$this->db->where_in('menu_modifiers.mod_group_id',$mod_group_id);
			$this->db->order_by('id desc');
			$this->db->group_by("menu_modifiers.id");	
			$query = $this->db->get();
			$result = $query->result();
		// return $this->db->last_query();
		return $result;
	}
	public function get_modifier_groups($constraints = null)
	{
		$this->db->from('modifier_groups');
		if (!empty($constraints))
			$this->db->where($constraints);
		$this->db->order_by('name ASC');
		$query = $this->db->get();
		return $query->result();
	}
	public function search_modifier_groups($search="")
	{
		$this->db->from('modifier_groups');
		if ($search != "")
			$this->db->like('name',$search);
		$query = $this->db->get();
		return $query->result();
	}
	public function add_menu_modifier($items)
	{
		$this->db->trans_start();
		$this->db->insert('menu_modifiers',$items);
		$id = $this->db->insert_id();
		$this->db->trans_complete();
		return $id;
	}
	public function add_bulk_menu_modifier($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert_batch('menu_modifiers', $items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function remove_menu_modifier($id)
	{
		$this->db->trans_start();
		$this->db->where('id',$id);
		$this->db->delete('menu_modifiers');
		$this->db->trans_complete();
	}
	/********	 End of	Menu Modifiers 	********/
	public function get_cat_sales_rep($sdate, $edate, $menu_cat_id, $branch_id,$brand="")
	{

		// echo "sdate: ".$sdate . " edate: ".$edate." menu_cat_id ".$menu_cat_id." branch".$branch_id;
		// die();
		// die();
		$this->db->select("tsm.branch_code,mc.menu_cat_name, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12) +COALESCE(sum(modprice/1.12),0)+COALESCE(sum(submodprice/1.12),0) as vat_sales, sum(tsm.qty*tsm.price/1.12*.12) + COALESCE(sum(modprice/1.12*.12),0) + COALESCE(sum(submodprice/1.12*.12),0) as vat, sum(tsm.qty*tsm.price) + COALESCE(sum(modprice),0) + COALESCE(sum(submodprice),0) as gross, sum(tsm.qty*menu.costing) as cost,bd.branch_name",false);
		 // $this->db->select("tsm.branch_code,mc.menu_cat_name, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12) as vat_sales");
		$this->db->from("trans_sales_menus tsm");
		$this->db->join("trans_sales ts", "ts.sales_id = tsm.sales_id and ts.branch_code = tsm.branch_code and ts.pos_id = tsm.pos_id ","LEFT");	
		$this->db->join("menus menu", "menu.menu_id = tsm.menu_id AND menu.branch_code = tsm.branch_code","LEFT");		
		$this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id AND mc.branch_code = menu.branch_code","LEFT");
		$this->db->join("branch_details bd", "bd.branch_code = tsm.branch_code","LEFT");

		if($branch_id != ''){
			$mod_sql = "(select tsm.pos_id,tsm.branch_code,sum(tsm.qty * tsm.price) modprice,tsm.sales_id,menu_id, sum(tssubmod.qty * tssubmod.price) submodprice from trans_sales_menu_modifiers tsm
			left join trans_sales_menu_submodifiers tssubmod on tsm.mod_line_id = tssubmod.mod_line_id and tsm.sales_id = tssubmod.sales_id 
			where tsm.branch_code = '".$branch_id."' and tsm.pos_id = tssubmod.pos_id and tsm.branch_code = tssubmod.branch_code
			group by sales_id,menu_id,tsm.branch_code,pos_id) tsmod";
		}else{
			$mod_sql = "(select tsm.pos_id,tsm.branch_code,sum(tsm.qty * tsm.price) modprice,tsm.sales_id,menu_id, sum(tssubmod.qty * tssubmod.price) submodprice from trans_sales_menu_modifiers tsm
			left join trans_sales_menu_submodifiers tssubmod on tsm.mod_line_id = tssubmod.mod_line_id and tsm.sales_id = tssubmod.sales_id 
			where tsm.pos_id = tssubmod.pos_id and tsm.branch_code = tssubmod.branch_code
			group by sales_id,menu_id,tsm.branch_code,pos_id) tsmod";
		}

		$this->db->join($mod_sql, "tsmod.sales_id = tsm.sales_id and tsmod.menu_id = tsm.menu_id and tsmod.branch_code = tsm.branch_code and tsmod.pos_id = tsm.pos_id",'left');		
		if($menu_cat_id != "")
		{
			$this->db->where("mc.menu_cat_id", $menu_cat_id);					
		}
		if($branch_id != "")
		{	
			$this->db->where("ts.branch_code", $branch_id);
			// $this->db->where("tsm.branch_code", $branch_id);	
			// $this->db->where("menu.branch_code", $branch_id);	
			// $this->db->where("mc.branch_code", $branch_id);					
				
		}

		if($brand != "")
		{	
			$this->db->where("ts.pos_id", $brand);				
				
		}

		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		if(HIDECHIT){
 			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		}
		$this->db->group_by("mc.menu_cat_id, tsm.branch_code");	
		$this->db->order_by("mc.menu_cat_name ASC");	
		// echo "qwerty";die();

		// echo "yuiop";die();
		$q = $this->db->get();
		// echo "pooo";
		// echo $this->db->_error_message(); 
		// echo "<pre>",$this->db->error(),"</pre>";die();
		// echo $this->db->last_query();die();
		// var_dump($q);die();
		$result = $q->result();
		return $result;
	}
	//  menu modifier report pauli
	public function get_menu_modifer_sales_rep($sdate, $edate, $menu_cat_id, $branch_id, $brand="")
	{
		$this->db->select("mc.menu_cat_name, sum(tsmm.qty) as qty, sum(tsmm.qty*tsmm.price/1.12) as vat_sales, sum(tsmm.qty*tsmm.price/1.12*.12) as vat, sum(tsmm.qty*tsmm.price) as gross, menu.costing as cost,m.name as modifier_name,tsmm.branch_code");
		$this->db->from("( select * from trans_sales_menu_modifiers group by sales_mod_id,branch_code,pos_id ) tsmm");
		$this->db->join("(select * from trans_sales group by sales_id,branch_code,pos_id) ts", "ts.sales_id = tsmm.sales_id AND ts.branch_code = tsmm.branch_code AND ts.pos_id = tsmm.pos_id","LEFT");		
		$this->db->join("menus menu", "menu.menu_id = tsmm.menu_id AND menu.branch_code = tsmm.branch_code","LEFT");		
		$this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id AND menu.branch_code = mc.branch_code","LEFT");
		$this->db->join("modifiers m", "m.mod_id = tsmm.mod_id  AND m.branch_code = tsmm.branch_code","LEFT");		

		if($menu_cat_id != "")
		{
			$this->db->where("mc.menu_cat_id", $menu_cat_id);					
		}
		if($branch_id != "")
		{	
			$this->db->where("ts.branch_code", $branch_id);
		}

		if($brand != "")
		{	
			$this->db->where("ts.pos_id", $brand);
		}
		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		if(HIDECHIT){
 			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		}
		$this->db->group_by("tsmm.mod_id,tsmm.branch_code");	
		$this->db->order_by("mc.menu_cat_name ASC");	
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		return $result;
	}
	// end menu modifier report
	public function get_menu_sales_rep($sdate, $edate, $menu_cat_id,$branch_id,$brand="")
	{
		$this->db->select("menu.menu_name, mc.menu_cat_name, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12) +COALESCE(sum(modprice/1.12),0)+COALESCE(sum(submodprice/1.12),0) as vat_sales, sum(tsm.qty*tsm.price/1.12*.12) + COALESCE(sum(modprice/1.12*.12),0) + COALESCE(sum(submodprice/1.12*.12),0) as vat, sum(tsm.qty*tsm.price) + COALESCE(sum(modprice),0) + COALESCE(sum(submodprice),0) as gross, sum(tsm.qty*menu.costing) as cost, menu.branch_code",false);
		$this->db->from("trans_sales_menus tsm");
		$this->db->join("trans_sales ts", "ts.sales_id = tsm.sales_id and ts.branch_code = tsm.branch_code and ts.pos_id=tsm.pos_id","LEFT");		
		$this->db->join("menus menu", "menu.menu_id = tsm.menu_id AND menu.branch_code = tsm.branch_code");		
		$this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id AND mc.branch_code = menu.branch_code");

		if($branch_id != ''){
			$mod_sql = "(select tsm.pos_id,tsm.branch_code,sum(tsm.qty * tsm.price) modprice,tsm.sales_id,menu_id, sum(tssubmod.qty * tssubmod.price) submodprice from trans_sales_menu_modifiers tsm
			left join trans_sales_menu_submodifiers tssubmod on tsm.mod_line_id = tssubmod.mod_line_id and tsm.sales_id = tssubmod.sales_id 
			where tsm.branch_code = '".$branch_id."' and tsm.pos_id = tssubmod.pos_id and tsm.branch_code = tssubmod.branch_code
			group by sales_id,menu_id,tsm.branch_code,pos_id) tsmod";
		}else{
			$mod_sql = "(select tsm.pos_id,tsm.branch_code,sum(tsm.qty * tsm.price) modprice,tsm.sales_id,menu_id, sum(tssubmod.qty * tssubmod.price) submodprice from trans_sales_menu_modifiers tsm
			left join trans_sales_menu_submodifiers tssubmod on tsm.mod_line_id = tssubmod.mod_line_id and tsm.sales_id = tssubmod.sales_id 
			where tsm.pos_id = tssubmod.pos_id and tsm.branch_code = tssubmod.branch_code
			group by sales_id,menu_id,tsm.branch_code,pos_id) tsmod";
		}

		$this->db->join($mod_sql, "tsmod.sales_id = tsm.sales_id and tsmod.menu_id = tsm.menu_id and tsmod.branch_code = tsm.branch_code and tsmod.pos_id = tsm.pos_id",'left');		

		// $this->db->join("( select sum(tsm.qty * tsm.price) modprice,tsm.sales_id,menu_id, sum(tssubmod.qty * tssubmod.price) submodprice,tsm.pos_id,tsm.branch_code from (select * from trans_sales_menu_modifiers group by sales_mod_id,sales_id,branch_code,pos_id) tsm
		// 	left join (select * from trans_sales_menu_submodifiers group by sales_submod_id,sales_id,branch_code,pos_id) tssubmod on tsm.mod_line_id = tssubmod.mod_line_id and tsm.sales_id = tssubmod.sales_id and tsm.branch_code = tssubmod.branch_code and tsm.pos_id = tssubmod.pos_id
		// 	group by sales_id,menu_id,tsm.branch_code, pos_id ) tsmod", "tsmod.sales_id = tsm.sales_id and tsmod.menu_id = tsm.menu_id and tsmod.branch_code = tsm.branch_code and tsmod.pos_id = tsm.pos_id",'left');

		if($menu_cat_id != "")
		{
			$this->db->where("mc.menu_cat_id", $menu_cat_id);					
		}
		if($branch_id != "")
		{
			$this->db->where("tsm.branch_code", $branch_id);	
			$this->db->where("ts.branch_code", $branch_id);	
			$this->db->where("menu.branch_code", $branch_id);	
			$this->db->where("mc.branch_code", $branch_id);					
		}

		if($brand !=''){
			$this->db->where("tsm.pos_id", $brand);	
			$this->db->where("ts.pos_id", $brand);	
			$this->db->where("menu.brand", $brand);	
			$this->db->where("mc.brand", $brand);	
		}

		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		$this->db->where("ts.void_ref is null");

 		if(HIDECHIT){
 			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		}
		// $this->db->group_by("menu.menu_id, tsm.sales_menu_id,branch_code, tsm.pos_id");		
		$this->db->group_by("menu.menu_id");		
		$this->db->order_by("menu.menu_name ASC");
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		return $result;
	}
	public function get_payment_date($sdate, $edate,$branch_id="",$brand="")
 	{
		$this->db->select("tsm.*");
		$this->db->from("trans_sales_payments tsm");
		$this->db->join("trans_sales ts", "ts.sales_id = tsm.sales_id");		
		// $this->db->join("menus menu", "menu.menu_id = tsm.menu_id");		
		// $this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id");		
		// if($menu_cat_id != "")
		// {
			// $this->db->where("mc.menu_cat_id", $menu_cat_id);					
 		// }

 		if($branch_id != ''){
 			$this->db->where("ts.branch_code", $branch_id);	
 		}

 		if($brand != ''){
 			$this->db->where("ts.pos_id", $brand);	
 		}

 		$this->db->where("ts.datetime >=", $sdate);		
 		$this->db->where("ts.datetime <=", $edate);
 		$this->db->where("ts.type_id", 10);
 		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		// $this->db->group_by("tsm.payment_type");	
 		// $this->db->order_by("mc.menu_cat_name ASC");	
 		$q = $this->db->get();
 		$result = $q->result();
 		// echo $this->db->last_query();
 		return $result;
 	}

 	public function get_last_menu_id($branch_code=null){
		$this->db->trans_start();
			$this->db->select('menu_id');
			$this->db->from('menus');
			$this->db->order_by('menu_id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->menu_id;
		}else{
			return 0;
		}
	}
 	public function get_date_now($branch_code=null){
		$this->db->select('NOW() as date_now');
		$query = $this->db->get();
		$result = $query->result();
		if(isset($result[0])){
			return $result[0]->date_now;
		}else{
			return null;
		}	
	}

	public function get_monthly_sales($from, $to, $branch_code="",$brand="")
	{
		$this->db->select("branch_code, MONTH(datetime) as month, sum(total_amount) as amt");
		$this->db->from(" (select * from trans_sales group by branch_code,sales_id,pos_id ) trans_sales");
		$this->db->where("inactive", '0');
		if(!empty($branch_code))
		{
			$this->db->where("branch_code", $branch_code);
		}

		if(!empty($brand))
		{
			$this->db->where("pos_id", $brand);
		}

		$this->db->where("void_ref is null");

		$this->db->where("trans_ref is not null");
		$this->db->where('paid', 1);
		$this->db->where("DATE_FORMAT(datetime, '%Y-%m-%d') >=", $from);
		$this->db->where("DATE_FORMAT(datetime, '%Y-%m-%d') <=", $to);
		$this->db->group_by("branch_code, MONTH(datetime), YEAR(datetime)");

		$get = $this->db->get();

		// $subquery = $this->db->get_compiled_select();

		// $sql = "SELECT branch_code,month, sum(amt) as amt FROM($subquery) t";
		// $get = $this->db->query($sql);	

		$res = $get->result();		
		return $res;
	}
	public function get_yearly_sales($from, $to, $branch_code="",$brand="")
	{
		$this->db->select("trans_sales.branch_code, YEAR(datetime) as year, SUM(total_amount) as amt,bd.branch_name");
		$this->db->from("( select * from trans_sales group by branch_code,sales_id,pos_id )trans_sales");
		$this->db->join("branch_details bd", "bd.branch_code = trans_sales.branch_code");
		$this->db->where("trans_sales.inactive", 0);
		$this->db->where("trans_ref is not null");
		if(!empty($branch_code))
		{
			$this->db->where("trans_sales.branch_code", $branch_code);
		}

		if(!empty($brand))
		{
			$this->db->where("pos_id", $brand);
		}

		if(!empty($from))
		{
			$this->db->where("DATE_FORMAT(datetime, '%Y-%m-%d') >=", $from);
			$this->db->where("DATE_FORMAT(datetime, '%Y-%m-%d') <=", $to);			
		}
		$this->db->where("void_ref is null");

		$this->db->group_by("trans_sales.branch_code,  YEAR(datetime)");
		$get = $this->db->get();
		// echo $this->db->last_query();die();
		$res = $get->result();		
		return $res;
	}
	public function get_yearly_sales_dashboard($from, $to, $branch_code="")
	{
		$this->db->select("YEAR(datetime) as year, SUM(total_amount) as amt");
		$this->db->from("( select * from trans_sales group by branch_code,sales_id )trans_sales");
		
		$this->db->where('paid', 1);
		$this->db->where("void_ref is null");
		$this->db->where("trans_ref is not null");
		$this->db->where("inactive", 0);
		if(!empty($branch_code))
		{
			$this->db->where("branch_code", $branch_code);
		}
		if(!empty($from))
		{
			$this->db->where("DATE_FORMAT(datetime, '%Y-%m-%d') >=", $from);
			$this->db->where("DATE_FORMAT(datetime, '%Y-%m-%d') <=", $to);			
		}
		$this->db->group_by("YEAR(datetime)");
		$get = $this->db->get();
		$res = $get->result();		
		return $res;
	}
	public function get_branch_details($branch_code)
	{
		$this->db->select("*");
		$this->db->from("branch_details");
		if(!empty($branch_code))
		{
			$this->db->where("branch_code", $branch_code);
		}
		$get = $this->db->get();
		$res = $get->result();		
		return $res;
	}	

 	public function get_menu_categories_last_id($branch_code=null){
		$this->db->trans_start();
			$this->db->select('menu_cat_id,branch_code');
			$this->db->from('menu_categories');
			$this->db->where("branch_code", $branch_code);
			$this->db->order_by('menu_cat_id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		// echo "<pre>",print_r($result),"</pre>";die();
	// echo $this->db->last_query();die();
		if(isset($result[0])){
			return $result[0]->menu_cat_id;
		}else{
			return 0;
		}
	}
 	public function get_last_subcat_id($branch_code=null){
		$this->db->trans_start();
			$this->db->select('menu_sub_cat_id');
			$this->db->from('menu_subcategories');
			$this->db->where("branch_code", $branch_code);
			$this->db->order_by('menu_sub_cat_id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->menu_sub_cat_id;
		}else{
			return 0;
		}
	}
 	public function get_all_branch(){
		$this->db->select('branch_code,branch_name,address');
		$this->db->from('branch_details');
		$query = $this->db->get();
		// echo $this->db->last_query();die();
		return $query->result();
	}

	//retail report
	public function get_item_sales($from, $to, $branch_code="")
	{
		$select_array = array("trans_sales.branch_code", "DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d') as date", "sum(trans_sales_items.qty) tot_qty", "trans_sales_items.price", "items.code","items.name as item_name", "categories.name as cat_name", "subcategories.name as sub_cat_name", "sum(trans_sales_items.qty * trans_sales_items.price) as item_gross");
		$this->db->select($select_array);
		$this->db->from(" ( select * from trans_sales group by branch_code, sales_id )trans_sales");
		$this->db->join("(select * from trans_sales_items group by branch_code,sales_item_id)trans_sales_items", "trans_sales.sales_id = trans_sales_items.sales_id AND trans_sales.branch_code = trans_sales_items.branch_code");
		$this->db->join("items", "items.item_id = trans_sales_items.item_id AND items.branch_code = trans_sales_items.branch_code","left");
		$this->db->join("categories", "categories.cat_id = items.cat_id AND categories.branch_code = items.branch_code","left");
		$this->db->join("subcategories", "subcategories.sub_cat_id = items.subcat_id and subcategories.branch_code =  items.branch_code","left");
		$this->db->where("trans_sales.datetime >=", $from);
		$this->db->where("trans_sales.datetime <=", $to);
		$this->db->where("trans_sales.type_id", 10);
		$this->db->where("trans_sales.trans_ref is not null");
		if($branch_code!="")
		{
			$this->db->where("trans_sales.branch_code", $branch_code);			
		}
		$this->db->where("trans_sales.inactive", 0);
		$group_array = array("trans_sales.branch_code","trans_ref", "DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d')", "trans_sales_items.item_id", "trans_sales_items.price");
		$this->db->group_by($group_array);
		$this->db->order_by("items.name ASC");

		$get = $this->db->get();		
		

		return $get->result();
	}

	public function get_item_sales_breakdown($from, $to, $branch_code="")
	{
		$select_array = array("
			sum(case when(receipt_discounts.no_tax = 1) then
						(trans_sales_items.qty * trans_sales_items.price)			
				end) as vat_exempt,
			sum(round(case when(receipt_discounts.no_tax is null) then
								(trans_sales_items.qty * trans_sales_items.price/1.12*0.12)
							when(receipt_discounts.no_tax = 0) then 	
							 	((trans_sales_items.qty * trans_sales_items.price)-
							 	(trans_sales_items.qty * trans_sales_items.price * (trans_sales_discounts.disc_rate/100))) /1.12*0.12
					
				end,2)) as vat,
			sum(round(case when(receipt_discounts.no_tax = 1) then
								trans_sales_items.qty * trans_sales_items.price/1.12*(trans_sales_discounts.disc_rate/100) +
								trans_sales_items.qty * trans_sales_items.price/1.12*0.12
					       else 
					       		trans_sales_items.qty * trans_sales_items.price*(trans_sales_discounts.disc_rate/100)
					
				end,2)) as exempt_discount
			");
		$this->db->select($select_array,false);
		$this->db->from(" ( select * from trans_sales group by branch_code, sales_id )trans_sales");
		$this->db->join("(select * from trans_sales_items group by branch_code,sales_item_id)trans_sales_items", "trans_sales.sales_id = trans_sales_items.sales_id AND trans_sales.branch_code = trans_sales_items.branch_code");
		$this->db->join("items", "items.item_id = trans_sales_items.item_id AND items.branch_code = trans_sales_items.branch_code","left");
		$this->db->join("categories", "categories.cat_id = items.cat_id AND categories.branch_code = items.branch_code","left");
		$this->db->join("subcategories", "subcategories.sub_cat_id = items.subcat_id and subcategories.branch_code =  items.branch_code","left");
		$this->db->join("trans_sales_tax", "trans_sales_tax.sales_id = trans_sales_items.sales_id and trans_sales_tax.branch_code =  trans_sales_items.branch_code","left");
		$this->db->join("trans_sales_no_tax", "trans_sales_no_tax.sales_id = trans_sales_items.sales_id and trans_sales_no_tax.branch_code =  trans_sales_items.branch_code","left");
		$this->db->join("trans_sales_discounts", "trans_sales_discounts.sales_id = trans_sales_items.sales_id and trans_sales_discounts.branch_code =  trans_sales_items.branch_code","left");
		$this->db->join("receipt_discounts", "receipt_discounts.disc_id = trans_sales_discounts.disc_id","left");
		$this->db->where("trans_sales.datetime >=", $from);
		$this->db->where("trans_sales.datetime <=", $to);
		$this->db->where("trans_sales.type_id", 10);
		$this->db->where("trans_sales.trans_ref is not null");
		if($branch_code!="")
		{
			$this->db->where("trans_sales.branch_code", $branch_code);			
		}
		$this->db->where("trans_sales.inactive", 0);
		// $group_array = array("trans_sales.branch_code","trans_ref", "DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d')", "trans_sales_items.item_id", "trans_sales_items.price");
		// $this->db->group_by($group_array);
		$this->db->order_by("items.name ASC");

		$get = $this->db->get();		
		
// print_r($this->db->last_query());
		return $get->result();
	}
	
	public function get_mod_cat_sales_rep($sdate, $edate, $menu_cat_id, $branch_code="",$brand="")
	{
		$this->db->select("tsm.branch_code, sum(tsm.qty*tsm.price) as mod_gross");
		$this->db->from("trans_sales_menu_modifiers tsm");
		$this->db->join("trans_sales ts", "ts.sales_id = tsm.sales_id AND ts.branch_code = tsm.branch_code");		
		$this->db->join("menus menu", "menu.menu_id = tsm.menu_id AND menu.branch_code = tsm.branch_code");		
		$this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id AND mc.branch_code = menu.branch_code");		
		if($menu_cat_id != "")
		{
			$this->db->where("mc.menu_cat_id", $menu_cat_id);					
		}
		if($branch_code!="")
		{
			$this->db->where("tsm.branch_code", $branch_code);					
		}

		if($brand!="")
		{
			$this->db->where("tsm.pos_id", $brand);					
		}
		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
		$this->db->where("ts.inactive", 0);
		if(HIDECHIT){
			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
		}
		$this->db->group_by("tsm.branch_code, mc.menu_cat_id");	
		$this->db->order_by("mc.menu_cat_name ASC");	
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();
		return $result;
	}

	//for new maintenance subcategory
	public function get_menu_subcategory($id=null,$notAll=false,$branch=null){
		$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('menu_subcategory');
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('menu_subcategory.menu_sub_id',$id);
				}else{
					$this->db->where('menu_subcategory.menu_sub_id',$id);
				}
				if(is_array($branch))
				{
					$this->db->where_in('menu_subcategory.branch_code',$branch);
				}else{
					$this->db->where('menu_subcategory.branch_code',$branch);
				}

			if($notAll){
				$this->db->where('menu_subcategory.inactive',0);
			}
			$this->db->order_by('menu_sub_id asc');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		return $result;
	}
	public function add_menu_subcategory($items){
		$this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert('menu_subcategory',$items);
		$x=$this->db->insert_id();
		return $x;
	}
	public function update_menu_subcategory($user,$id,$branch=null){
		$this->db->where('menu_sub_id', $id);
		if($branch != null){
			$this->db->where('branch_code', $branch);
		}
		$this->db->update('menu_subcategory', $user);
		// echo $this->db->last_query();die();
		return $this->db->last_query();
	}
 	public function get_last_subcategory(){
		$this->db->trans_start();
			$this->db->select('menu_sub_id');
			$this->db->from('menu_subcategory');
			$this->db->order_by('menu_sub_id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->menu_sub_id;
		}else{
			return 0;
		}
	}
	public function add_bulk_menu_subcategory($items){
		// $this->db->set('reg_date', 'NOW()', FALSE);
		$this->db->insert_batch('menu_subcategory', $items);
		$x=$this->db->insert_id();
		return $x;
	}
 	public function get_last_recipe_id(){
		$this->db->trans_start();
			$this->db->select('recipe_id');
			$this->db->from('menu_recipe');
			$this->db->order_by('recipe_id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->recipe_id;
		}else{
			return 0;
		}
	}

	public function get_menu_sales_rep_retail($sdate, $edate, $menu_cat_id, $branch_code,$brand="")
	{
		$this->db->select("ts.branch_code,item.name as item_name, mc.name as cat_name, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12) as vat_sales, sum(tsm.qty*tsm.price/1.12*.12) as vat, sum(tsm.qty*tsm.price) as gross");
		$this->db->from("( select * from trans_sales_items group by sales_item_id,branch_code,pos_id ) tsm");
		$this->db->join("(select * from trans_sales group by sales_id, branch_code,pos_id) ts", "ts.sales_id = tsm.sales_id and ts.branch_code = tsm.branch_code and ts.pos_id = tsm.pos_id");		
		$this->db->join("items item", "item.item_id = tsm.item_id");		
		$this->db->join("categories mc", "mc.cat_id = item.cat_id");		
		if($menu_cat_id != "")
		{
			$this->db->where("mc.cat_id", $menu_cat_id);					
		}
		if($branch_code != "")
		{
			$this->db->where("ts.branch_code", $branch_code);
		}

		if($brand != "")
		{
			$this->db->where("ts.pos_id", $brand);
		}

		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		if(HIDECHIT){
 			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		}
		$this->db->group_by("item.item_id");		
		$this->db->order_by("item.name ASC");
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();
		return $result;
	}
	public function get_cat_sales_rep_retail($sdate, $edate, $item_cat_id, $branch_code,$brand="")
	{
		$this->db->select("ts.branch_code, mc.name, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12) as vat_sales, sum(tsm.qty*tsm.price/1.12*.12) as vat, sum(tsm.qty*tsm.price) as gross");
		$this->db->from("( select * from trans_sales_items group by branch_code,sales_item_id,pos_id ) tsm");
		$this->db->join("(select * from trans_sales group by branch_code,sales_id,pos_id) ts", "ts.sales_id = tsm.sales_id AND ts.branch_code = tsm.branch_code AND ts.pos_id = tsm.pos_id");		
		$this->db->join("items item", "item.item_id = tsm.item_id AND item.branch_code = tsm.branch_code");		
		$this->db->join("categories mc", "mc.cat_id = item.cat_id AND mc.branch_code = item.branch_code");		
		// if($item_cat_id != "")
		// {
		// 	$this->db->where("mc.cat_id", $item_cat_id);					
		// }
		if($branch_code != "")
		{
			$this->db->where("ts.branch_code", $branch_code);
		}

		if($brand != "")
		{
			$this->db->where("ts.pos_id", $brand);
		}

		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		if(HIDECHIT){
 			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		}
		$this->db->group_by("mc.cat_id, ts.branch_code");	
		$this->db->order_by("mc.name ASC");	
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		return $result;
	}

	public function get_mod_menu_sales_rep($sdate, $edate, $menu_cat_id, $branch_code)
	{
		$this->db->select("ts.branch_code,sum(tsm.qty*tsm.price) as mod_gross");
		$this->db->from("trans_sales_menu_modifiers tsm");
		$this->db->join("trans_sales ts", "ts.sales_id = tsm.sales_id");		
		$this->db->join("menus menu", "menu.menu_id = tsm.menu_id");		
		$this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id");		
		if($menu_cat_id != "")
		{
			$this->db->where("mc.menu_cat_id", $menu_cat_id);					
		}
		if($branch_code != "")
		{
			$this->db->where("ts.branch_code", $branch_code);
		}
		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
		$this->db->where("ts.inactive", 0);
		if(HIDECHIT){
			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
		}
		$this->db->group_by("menu.menu_id");		
		$this->db->order_by("menu.menu_name ASC");
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();
		return $result;
	}

 	public function get_last_menu_mod(){
		$this->db->trans_start();
			$this->db->select('id');
			$this->db->from('menu_modifiers');
			$this->db->order_by('id desc');
			$this->db->limit('1');
			$query = $this->db->get();
			$result = $query->result();
		$this->db->trans_complete();
		if(isset($result[0])){
			return $result[0]->id;
		}else{
			return 0;
		}
	}

	public function add_menu_history($menu)
	{
		$this->db->insert('menu_pricing_history',$menu);
		return $this->db->insert_id();
		$this->db->trans_complete();
	}

	public function add_bulk_menu_history($menu){
		$this->db->insert_batch('menu_pricing_history', $menu);
		$x=$this->db->insert_id();
		return $x;
	}

	public function get_issues_stamp($year, $branch_code="")
	{
		$data = array();

		$tbl = array('trans_sales_items','trans_sales_menus');	

		foreach($tbl as $i=>$each){
			$select_array = array("trans_sales.branch_code", 
							  "DATE_FORMAT(trans_sales.datetime,'%Y-%m') as date", 
							  "sum({$each}.qty) tot_qty",
							  "{$each}.remarks remarks",
							  "sum({$each}.qty * {$each}.price) tot_amount");

			$this->db->select($select_array);
			$this->db->from(" ( select * from trans_sales group by branch_code,sales_id )trans_sales");
			
			$group_item = $each == 'trans_sales_menus' ? 'sales_menu_id' : 'sales_item_id';
			$this->db->join("(select * from {$each} group by branch_code,".$group_item.") {$each}", "trans_sales.sales_id = {$each}.sales_id AND trans_sales.branch_code = {$each}.branch_code");

			if($each == 'trans_sales_menus'){
				$this->db->join("menus", "menus.menu_id = {$each}.menu_id AND menus.branch_code = {$each}.branch_code");				
			}else{
				$this->db->join("items", "items.item_id = {$each}.item_id AND items.branch_code = {$each}.branch_code");
			}


			$this->db->where("Year(trans_sales.datetime)=", $year);
			$this->db->where("trans_sales.type_id", 10);
			$this->db->where("{$each}.remarks >", 0);
			$this->db->where("trans_sales.trans_ref is not null");
			//$this->db->where("{$each}.price = 0");
			


			if($branch_code!="")
			{
				$this->db->where("trans_sales.branch_code", $branch_code);			
			}
			$this->db->where("trans_sales.inactive", 0);
			// $group_array = array("trans_sales.branch_code", "DATE_FORMAT(trans_sales.datetime,'%Y-%m')");
			$group_array = array("trans_sales.branch_code", "trans_sales.sales_id");

			$this->db->group_by($group_array);

			$get = $this->db->get();
			$result = $get->result();

		

			if($result){
				foreach ($result as $v) {
					array_push($data, $v);
				}
				
			}			
		}

		if($data){
			return $this->arrange_data($data);
		}

		return array('dates'=>array(),'list'=>array());
	}

	public function arrange_data($data){
		$dates = array();
		$branch = array();
		$list = array();

		foreach($data as $each){
			if(!in_array($each->date, $dates)){
				$dates[] = $each->date;
			}
		}

		foreach($data as $each){
			if(!in_array($each->branch_code, $branch)){
				$branch[] = $each->branch_code;
			}
		}

		foreach ($branch as $each){
			$br_list = array();
			$br_list['branch_code'] = $each;

			foreach($dates as $date){
				$qty = 0;
				$amt = 0;
				foreach($data as $v){
					$qty += ($date == $v->date && $v->branch_code == $each) ? $v->remarks : 0;
					$amt += ($date == $v->date && $v->branch_code == $each) ? $v->tot_amount : 0;
				}

				$br_list[$date] = array('qty'=>$qty,'amount'=>$amt); 
			}

			$list[] = $br_list;			
		}

		return array('dates'=>$dates,'list'=>$list);
	}

	public function get_voided_cat_sales_rep($sdate, $edate, $menu_cat_id, $branch_id,$brand='')
	{
		$this->db->select("mc.menu_cat_name, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12) as vat_sales, sum(tsm.qty*tsm.price/1.12*.12) as vat, sum(tsm.qty*tsm.price) as gross, sum(tsm.qty*menu.costing) as cost, tsm.branch_code, bd.branch_name");
		$this->db->from("trans_sales_menus tsm");
		$this->db->join("trans_sales ts", "ts.sales_id = tsm.sales_id && ts.branch_code = tsm.branch_code && ts.pos_id = tsm.pos_id");		
		$this->db->join("menus menu", "menu.menu_id = tsm.menu_id && menu.branch_code = tsm.branch_code");		
		$this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id && mc.branch_code = tsm.branch_code");	
		$this->db->join("branch_details bd", "bd.branch_code = tsm.branch_code");	

		if($menu_cat_id != "")
		{
			$this->db->where("mc.menu_cat_id", $menu_cat_id);					
		}

		if($branch_id != "")
		{	
			$this->db->where("tsm.branch_code", $branch_id);	
			$this->db->where("menu.branch_code", $branch_id);	
			$this->db->where("mc.branch_code", $branch_id);			
		}

		if($brand != "")
		{	
			$this->db->where("tsm.pos_id", $brand);	
			$this->db->where("menu.brand", $brand);	
			$this->db->where("mc.brand", $brand);			
		}

		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 11);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		if(HIDECHIT){
 			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		}
		$this->db->group_by("mc.menu_cat_id");	
		$this->db->order_by("mc.menu_cat_name ASC");	
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		return $result;
	}

	////for retail
	public function get_voided_cat_sales_rep_retail($sdate, $edate, $item_cat_id,$branch_id,$brand="")
	{
		$this->db->select("mc.name, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12) as vat_sales, sum(tsm.qty*tsm.price/1.12*.12) as vat, sum(tsm.qty*tsm.price) as gross, sum(tsm.qty*item.costing) as cost, tsm.branch_code");
		$this->db->from("( select * from trans_sales_items group by branch_code,sales_item_id ) tsm");
		$this->db->join("(select * from trans_sales group by branch_code,sales_id) ts", "ts.sales_id = tsm.sales_id  && ts.branch_code = tsm.branch_code");		
		$this->db->join("items item", "item.item_id = tsm.item_id && item.branch_code = tsm.branch_code");		
		$this->db->join("categories mc", "mc.cat_id = item.cat_id  && mc.branch_code = tsm.branch_code");

		if($item_cat_id != "")
		{
			$this->db->where("mc.cat_id", $item_cat_id);					
		}

		if($branch_id != "")
		{
			$this->db->where("tsm.branch_code", $branch_id);	
			$this->db->where("item.branch_code", $branch_id);	
			$this->db->where("mc.branch_code", $branch_id);						
		}

		if($brand != "")
		{
			$this->db->where("tsm.pos_id", $brand);	
			$this->db->where("item.brand", $brand);	
			// $this->db->where("mc.brand", $brand);						
		}

		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 11);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		if(HIDECHIT){
 			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		}
		$this->db->group_by("mc.cat_id");	
		$this->db->order_by("mc.name ASC");	
		$q = $this->db->get();
		$result = $q->result();
		 // echo $this->db->last_query();die();
		
		return $result;
	}
	public function get_price_history($menu_id){
    	$this->db->trans_start();
			$this->db->select('*');
			$this->db->from('menu_pricing_history');
			$this->db->where("menu_id", $menu_id);
		$this->db->trans_complete();	
		// $this->db->where('inactive', $inactive);
		$query = $this->db->get();
		// echo $this->db->last_query();die();
		return $query->result();
	}
	public function get_food_server_rep($sdate, $edate,$cashier="",$branch_code="")
	{
		$this->db->select("
			users.fname,
			users.lname,
			COUNT(ts.sales_id) as qty_sale,
			sum(ts.total_amount) as total_sales,
			ts.branch_code as trans_branch,
			GROUP_CONCAT(ts.sales_id ) as sales_id,
			SUM(trans_sales_discounts.amount) as disc_amount,
			SUM(trans_sales_charges.amount) as charges_amount,
			SUM(trans_sales_no_tax.amount)*.12 as tax,


			");
		
		$this->db->from("trans_sales ts");
		$this->db->join("users", "users.id = ts.waiter_id");
		$this->db->join("trans_sales_discounts", "trans_sales_discounts.sales_id = ts.sales_id","left");
		$this->db->join("trans_sales_charges", "trans_sales_charges.sales_id = ts.sales_id","left");
		$this->db->join("trans_sales_no_tax", "trans_sales_no_tax.sales_id = ts.sales_id","left");


		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		if($cashier != "")
		{
			$this->db->where("users.id", $cashier);					
		}
		if($branch_code != "")
		{
			$this->db->where("ts.branch_code", $branch_code);					
		}
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
		$this->db->order_by("users.fname ASC");
		$this->db->group_by("ts.waiter_id");

		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		return $result;
	}
	public function get_food_server_det($sales_id_raw,$branch_code)
	{	
		$sales_id = array();
		$sales_id = explode( ',', $sales_id_raw );
		// echo print_r($sales_id);die();
		$this->db->flush_cache();
		$this->db->select("trans_sales_menus.sales_id,SUM(trans_sales_menus.price) as price,menus.menu_name,SUM(trans_sales_menus.qty) as qty");
		$this->db->from("trans_sales_menus");
		$this->db->join("menus", "menus.menu_id = trans_sales_menus.menu_id");

		$this->db->group_by("trans_sales_menus.menu_id");
		// foreach ($sales_id as $key => $value) {
		// 	$this->db->or_where("trans_sales_menus.sales_id", $value);					
		// }
		if($branch_code != "")
		{
			$this->db->where("trans_sales_menus.branch_code", $branch_code);					
		}
		$this->db->where_in("trans_sales_menus.sales_id",$sales_id);	
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		return $result;
	}
	public function get_modifiers_sale($sales_id_raw=array(),$branch_code)
	{	
		$sales_id = array();
		$sales_id = explode( ',', $sales_id_raw );
		$this->db->flush_cache();
		$this->db->select("SUM(trans_sales_menu_modifiers.price) as mod_sales");
		$this->db->from("trans_sales_menu_modifiers");	
		if($branch_code != "")
		{
			$this->db->where("trans_sales_menu_modifiers.branch_code", $branch_code);					
		}
		$this->db->where_in("trans_sales_menu_modifiers.sales_id",$sales_id);			
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		if(isset($result[0])){
			return $result[0]->mod_sales;
		}else{
			return 0;
		}
		// return $result;
	}


	public function get_menu_sales_rep_brand($sdate, $edate, $menu_cat_id,$branch_id,$brand)
	{
		$this->db->select("menu.menu_name, mc.menu_cat_name, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12) +COALESCE(sum(modprice/1.12),0)+COALESCE(sum(submodprice/1.12),0) as vat_sales, sum(tsm.qty*tsm.price/1.12*.12) + COALESCE(sum(modprice/1.12*.12),0) + COALESCE(sum(submodprice/1.12*.12),0) as vat, sum(tsm.qty*tsm.price) + COALESCE(sum(modprice),0) + COALESCE(sum(submodprice),0) as gross, sum(tsm.qty*menu.costing) as cost, menu.branch_code, tsm.menu_id, menu.brand",false);
		$this->db->from("( select * from trans_sales_menus group by sales_menu_id,branch_code,pos_id ) tsm");
		$this->db->join("(select * from trans_sales group by sales_id,branch_code,pos_id) ts", "ts.sales_id = tsm.sales_id and ts.branch_code = tsm.branch_code and ts.pos_id = tsm.pos_id","LEFT");		
		$this->db->join("menus menu", "menu.menu_id = tsm.menu_id AND menu.branch_code = tsm.branch_code");		
		$this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id AND mc.branch_code = menu.branch_code");

		// $this->db->join("(select sum(tsm.qty * tsm.price) modprice,tsm.sales_id,menu_id, sum(tssubmod.qty * tssubmod.price) submodprice from (select * from trans_sales_menu_modifiers group by sales_mod_id,branch_code,pos_id) tsm
		// 	left join (select * from trans_sales_menu_submodifiers group by sales_submod_id,branch_code,pos_id) tssubmod on tsm.mod_line_id = tssubmod.mod_line_id and tsm.sales_id = tssubmod.sales_id and tsm.branch_code = tssubmod.branch_code and tsm.pos_id = tssubmod.pos_id
		// 	group by sales_id,menu_id) tsmod", "tsmod.sales_id = tsm.sales_id and tsmod.menu_id = tsm.menu_id",'left');

		$this->db->join("(select tsm.pos_id,tsm.branch_code,sum(tsm.qty * tsm.price) modprice,tsm.sales_id,menu_id, sum(tssubmod.qty * tssubmod.price) submodprice from (select * from trans_sales_menu_modifiers group by sales_id,sales_mod_id,branch_code,pos_id) tsm
			left join (select * from trans_sales_menu_submodifiers group by sales_id,sales_submod_id,branch_code,pos_id) tssubmod on tsm.mod_line_id = tssubmod.mod_line_id and tsm.sales_id = tssubmod.sales_id and tsm.pos_id = tssubmod.pos_id and tsm.branch_code = tssubmod.branch_code
			group by sales_id,menu_id,tsm.branch_code,pos_id) tsmod", "tsmod.sales_id = tsm.sales_id and tsmod.menu_id = tsm.menu_id and tsmod.branch_code = tsm.branch_code and tsmod.pos_id = tsm.pos_id",'left');

		if($menu_cat_id != "")
		{
			$this->db->where("mc.menu_cat_id", $menu_cat_id);					
		}
		if($brand != "")
		{
			$this->db->where("menu.brand", $brand);					
		}
		if($branch_id != "")
		{
			$this->db->where("tsm.branch_code", $branch_id);	
			$this->db->where("ts.branch_code", $branch_id);	
			$this->db->where("menu.branch_code", $branch_id);	
			$this->db->where("mc.branch_code", $branch_id);					
		}
		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		$this->db->where("ts.void_ref is null");

 		if(HIDECHIT){
 			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		}
		$this->db->group_by("menu.menu_id,branch_code");		
		$this->db->order_by("menu.menu_name ASC");
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		return $result;
	}

	public function get_menu_modifer_sales_rep_brand($sdate, $edate, $menu_cat_id, $branch_id, $brand)
	{
		$this->db->select("mc.menu_cat_name, sum(tsmm.qty) as qty, sum(tsmm.qty*tsmm.price/1.12) as vat_sales, sum(tsmm.qty*tsmm.price/1.12*.12) as vat, sum(tsmm.qty*tsmm.price) as gross, menu.costing as cost,m.name as modifier_name,tsmm.branch_code");
		$this->db->from("( select * from trans_sales_menu_modifiers group by sales_mod_id,branch_code,pos_id ) tsmm");
		$this->db->join("trans_sales ts", "ts.sales_id = tsmm.sales_id AND ts.branch_code = tsmm.branch_code AND ts.pos_id = tsmm.pos_id","LEFT");		
		$this->db->join("menus menu", "menu.menu_id = tsmm.menu_id AND menu.branch_code = tsmm.branch_code","LEFT");		
		$this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id AND menu.branch_code = mc.branch_code","LEFT");
		$this->db->join("modifiers m", "m.mod_id = tsmm.mod_id  AND m.branch_code = tsmm.branch_code","LEFT");		

		if($menu_cat_id != "")
		{
			$this->db->where("mc.menu_cat_id", $menu_cat_id);					
		}
		if($brand != "")
		{
			$this->db->where("menu.brand", $brand);					
		}
		if($branch_id != "")
		{	
			$this->db->where("ts.branch_code", $branch_id);
		}
		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		$this->db->where("ts.inactive", 0);
 		if(HIDECHIT){
 			$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		}
		$this->db->group_by("tsmm.mod_id,tsmm.branch_code");	
		$this->db->order_by("mc.menu_cat_name ASC");	
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();die();
		return $result;
	}
	/********	 	Menu Prices 		********/
	public function get_menu_prices($menu_id=null,$id=null,$branch_code=null){
			$this->db->select('menu_prices.*,transaction_types.trans_name');			
			$this->db->from('menu_prices');
			$this->db->join('transaction_types','transaction_types.trans_name=menu_prices.trans_type AND transaction_types.branch_code = menu_prices.branch_code');
			if($branch_code != null)
				$this->db->where('menu_prices.branch_code',$branch_code);
			if($id != null)
				if(is_array($id))
				{
					$this->db->where_in('menu_prices.trans_type',$id);
				}else{
					$this->db->where('menu_prices.trans_type',$id);
				}
			if($menu_id != null)
				$this->db->where_in('menu_prices.menu_id',$menu_id);
			// if($mod_group_id != null)
			// 	$this->db->where_in('menu_modifiers.mod_group_id',$mod_group_id);
			// $this->db->order_by('id desc');
			$this->db->group_by("menu_prices.id");
			$query = $this->db->get();
			$result = $query->result();
		return $result;
	}
	public function add_menu_price($items)
	{
		$this->db->trans_start();
		$this->db->insert('menu_prices',$items);
		$id = $this->db->insert_id();
		$this->db->trans_complete();
		return $id;
	}
	public function remove_menu_price($id)
	{
		$this->db->trans_start();
		$this->db->where('id',$id);
		$this->db->delete('menu_prices');
		$this->db->trans_complete();
	}
	public function get_last_menu_prices($branch_code=null){
		$this->db->trans_start();
			$this->db->select_max('id');
			$this->db->from('menu_prices');
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

	///
	public function get_menu_sales_rep_hourly($sdate, $edate, $brach_code=null)
	{
		$this->db->select("menu.menu_name,menu.menu_code, menu.cost as mprice, mc.menu_cat_name, sum(tsm.qty) as qty, sum(tsm.qty*tsm.price/1.12)+COALESCE(sum(modprice/1.12),0)+COALESCE(sum(submodprice/1.12),0) as vat_sales, sum(tsm.qty*tsm.price/1.12*.12) + COALESCE(sum(modprice/1.12*.12),0) + COALESCE(sum(submodprice/1.12*.12),0) as vat, sum(tsm.qty*tsm.price) + COALESCE(sum(modprice),0) + COALESCE(sum(submodprice),0) as gross, sum(tsm.qty*menu.costing) as cost, ts.trans_ref, ts.datetime, ts.type as trans_type, ts.guest, tsm.remarks as mremarks, tsm.line_id, tsm.sales_id as sales_id, ts.inactive, tt.terminal_code, CONCAT(tt.terminal_code,'-',tsm.sales_id) as ter_sales_id, tsm.pos_id as ter_id",false);
		$this->db->from("trans_sales_menus tsm");
		$this->db->join("trans_sales ts", "ts.sales_id = tsm.sales_id and ts.pos_id = tsm.pos_id and ts.branch_code = tsm.branch_code");		
		$this->db->join("menus menu", "menu.menu_id = tsm.menu_id and menu.branch_code = tsm.branch_code");		
		$this->db->join("menu_categories mc", "mc.menu_cat_id = menu.menu_cat_id and mc.branch_code = menu.branch_code");
		$this->db->join("terminals tt", "tt.branch_code = ts.branch_code && tt.pos_id = ts.pos_id");

		$this->db->join("(select sum(tsm.qty * tsm.price) modprice,tsm.sales_id,menu_id, tsm.pos_id, sum(tssubmod.qty * tssubmod.price) submodprice, tsm.branch_code from trans_sales_menu_modifiers tsm
			left join trans_sales_menu_submodifiers tssubmod on tsm.mod_line_id = tssubmod.mod_line_id and tsm.sales_id = tssubmod.sales_id and tsm.pos_id = tssubmod.pos_id and tsm.branch_code = tssubmod.branch_code
			group by sales_id,menu_id, tsm.pos_id) tsmod", "tsmod.sales_id = tsm.sales_id and tsmod.menu_id = tsm.menu_id and tsmod.pos_id = tsm.pos_id and tsmod.branch_code = tsm.branch_code",'left');

		// if($menu_cat_id != "")
		// {
		// 	$this->db->where("mc.menu_cat_id", $menu_cat_id);					
		// }
		$this->db->where("ts.datetime >=", $sdate);		
		$this->db->where("ts.datetime <", $edate);
		$this->db->where("ts.type_id", 10);
		$this->db->where("ts.trans_ref is not null");
 		// $this->db->where("ts.inactive", 0);
 		// $this->db->where("ts.pos_id", TERMINAL_ID);
 		$this->db->where("ts.branch_code", $brach_code);
 		// if(HIDECHIT){
 		// 	$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'chit')");
 		// }
 		// if(PRODUCT_TEST){
 		// 	$this->db->where("ts.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = 'producttest')");
 		// }
		$this->db->group_by("tsm.pos_id, ts.sales_id, tsm.line_id, tsm.menu_id");		
		$this->db->order_by("ts.datetime ASC, tsm.line_id ASC");
		$q = $this->db->get();
		$result = $q->result();
		// echo $this->db->last_query();
		return $result;
	}
	public function get_curr_menu_inv_and_locs()
	{
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
				menus.uom_id,
				menu_moves.branch_code,
				SUM(menu_moves.qty) as qoh,
				locations.loc_name,
				menu_categories.menu_cat_name as cat_name,
				menu_subcategories.menu_sub_cat_name as sub_cat_name
			FROM menu_moves
			JOIN menus ON menu_moves.item_id = menus.menu_id &&  menu_moves.branch_code = menus.branch_code
			LEFT JOIN locations ON menu_moves.loc_id = locations.loc_id && menu_moves.branch_code = locations.branch_code
			LEFT JOIN menu_categories ON menu_categories.menu_cat_id = menus.menu_cat_id && menu_categories.branch_code = menus.branch_code
			LEFT JOIN menu_subcategories ON menu_subcategories.menu_sub_cat_id = menus.menu_sub_cat_id && menu_subcategories.branch_code = menus.branch_code
			WHERE menu_moves.inactive = "0"
			GROUP BY menu_moves.item_id;
		';
		$r_query = $this->db->query($sql);

		return $r_query;
	}
}
?>