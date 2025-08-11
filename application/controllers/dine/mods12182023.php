<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mods extends CI_Controller {
	public function index(){
        $this->load->model('dine/menu_model');
        $this->load->helper('dine/menu_helper');
        $this->load->helper('site/site_forms_helper');
        $data = $this->syter->spawn('mods');
        $data['page_title'] = fa('icon-book-open')." Modifiers";
        $th = array('ID','Name','Cost','Branch','Inactive');
        $data['code'] = create_rtable('modifiers','mod_id','modifiers-tbl',$th,'mods/search_modifiers_form');
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'modsListFormJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function get_modifiers($id=null,$asJson=true){
        $this->load->helper('site/pagination_helper');
        $pagi = null;
        $args = array();
        $total_rows = 50;
        if($this->input->post('pagi'))
            $pagi = $this->input->post('pagi');
        $post = array();
        
        if(count($this->input->post()) > 0){
            $post = $this->input->post();
        }
        if($this->input->post('name')){
            $lk  =$this->input->post('name');
            $args["(modifiers.name like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        }
        if($this->input->post('inactive')){
            $args['modifiers.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));
        }
        if($this->input->post('branch_id')){
            $args['modifiers.branch_code'] = array('use'=>'where','val'=>$this->input->post('branch_id'));
        }
        // $join = null;
        $join["branch_details"] = array('content'=>"branch_details.branch_code = modifiers.branch_code");
        $count = $this->site_model->get_tbl('modifiers',$args,array(),$join,true,'modifiers.*',null,null,true);
        $page = paginate('mods/get_modifiers',$count,$total_rows,$pagi);
        $items = $this->site_model->get_tbl('modifiers',$args,array(),$join,true,'modifiers.*,branch_details.branch_name',null,$page['limit']);
        $json = array();
        if(count($items) > 0){
            $ids = array();
            foreach ($items as $res) {
                $link = $this->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'mods/form/'.$res->mod_id."-".$res->branch_code,array('class'=>'btn btn-sm blue btn-outline edit','id'=>'edit-'.$res->mod_id,'ref'=>$res->mod_id,'ref2'=>$res->branch_code,'return'=>'true'));
                $json[$res->mod_id."-".$res->branch_code] = array(
                    "id"=>$res->mod_id,   
                    "title"=>ucwords(strtolower($res->name)),
                    "caption"=>"PHP ".num($res->cost),
                    "branch"=>$res->branch_name,   
                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),
                    "link"=>$link
                );
                $ids[] = $res->mod_id;
            }
        }
        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    }
    public function search_modifiers_form(){
        $this->load->helper('dine/mods_helper');
        $data['code'] = modSearchForm();
        $this->load->view('load',$data);
    }
    public function form($mod_id=null,$bcode=null){
        $this->load->model('dine/mods_model');
        $this->load->helper('dine/mods_helper');
        $data = $this->syter->spawn('mods');
        $data['page_title'] = fa('icon-book-open')." Add New Modifiers";
        if($mod_id != null){
            $result = $this->site_model->get_tbl('modifiers',array('mod_id'=>$mod_id));
            $mod = $result[0];
            $data['page_title'] = fa('icon-book-open').' '.$mod->name;
        }
        $data['code'] = modFormPage($mod_id,$bcode);
        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js','js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'modFormJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function details_load($mod_id=null,$bcode=null){
        $this->load->model('dine/mods_model');
        $this->load->helper('dine/mods_helper');
        $mod=array();
        if($mod_id != null){
            $mods = $this->mods_model->get_modifiers($mod_id,$bcode);
            $mod=$mods[0];
        }
        $data['code'] = modDetailsLoad($mod,$mod_id);
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'detailsLoadJs';
        $this->load->view('load',$data);
    }
    public function details_db(){
        $this->load->model('dine/mods_model');
        $this->load->model('dine/main_model');
        $this->load->model('dine/items_model');
        $id = "";
        $act = "";
        $msg = "";
        $error = "";
        $f_mod_id = $this->input->post('form_mod_id');
        // echo "<pre>",print_r($_POST),"</pre>";die();
        if(!empty($f_mod_id)){
            $branch_selected_ac = $this->input->post('bcode');
            if(!empty($branch_selected_ac)){
                foreach ($branch_selected_ac as $branch) {

                if(HAS_MOD_SUB_CAT_ID){

                    $items = array(
                        "name"=>$this->input->post('name'),
                        "has_recipe"=>(int)$this->input->post('has_recipe'),
                        "cost"=>$this->input->post('cost'),
                        "inactive"=> $this->input->post('inactive'),
                        "mod_code"=> $this->input->post('mod_code'),
                        "other_desc"=> $this->input->post('other_desc'),
                        "mod_sub_cat_id"=>$this->input->post('mod_sub_cat_id')
                    );
                }else{
                      $items = array(
                        "name"=>$this->input->post('name'),
                        "has_recipe"=>(int)$this->input->post('has_recipe'),
                        "cost"=>$this->input->post('cost'),
                        "mod_code"=>$this->input->post('mod_code'),
                        "other_desc"=>$this->input->post('other_desc'),
                        "inactive"=> $this->input->post('inactive')
                    );
                }

                $cat_id = $f_mod_id;
                 $id = $this->mods_model->update_modifiers($items, $cat_id,$branch);
                $act = 'update';
                $msg = "Updated modifier: ".$items['name'];
                    
                }
            }
            else{
                $msg = "Error:No Branch Selected!";
            }
        }else{
            $branch_selected = $this->input->post('b_code');
            if(empty($branch_selected)){
                 $msg = "Error:No Branch Selected!";
                 // site_alert($msg,'error');
             }else{
                $branches_code = $this->items_model->get_branch_detail();
                foreach ($branches_code as $key => $b_code) {
                    if(HAS_MOD_SUB_CAT_ID){

                        $items = array(
                            "name"=>$this->input->post('name'),
                            "has_recipe"=>(int)$this->input->post('has_recipe'),
                            "cost"=>$this->input->post('cost'),
                            "branch_code"=>$b_code->branch_code,
                            "mod_sub_cat_id"=>$this->input->post('mod_sub_cat_id'),
                            "mod_code"=>$this->input->post('mod_code'),
                            "other_desc"=>$this->input->post('other_desc'),
                            "inactive"=> 1
                        );
                    }else{
                        $items = array(
                            "name"=>$this->input->post('name'),
                            "has_recipe"=>(int)$this->input->post('has_recipe'),
                            "cost"=>$this->input->post('cost'),
                            "mod_code"=>$this->input->post('mod_code'),
                            "other_desc"=>$this->input->post('other_desc'),
                            "branch_code"=>$b_code->branch_code,
                            "inactive"=> 1
                        );
                    }

                    $mod_id = $this->mods_model->get_last_modifier_id($b_code->branch_code);
                    $items['mod_id'] =  $mod_id + 1;
                    // echo "<pre>",print_r($items),"</pre>";die();
                    $id = $this->mods_model->add_modifiers($items);    
                }
                if(!empty($branch_selected)){
                    foreach ($branch_selected as $branch) {
                    $items = array(
                        "name"=>$this->input->post('name'),
                        "has_recipe"=>(int)$this->input->post('has_recipe'),
                        "cost"=>$this->input->post('cost'),
                        "branch_code"=>$branch,
                        "mod_sub_cat_id"=>$this->input->post('mod_sub_cat_id'),
                        "mod_code"=>$this->input->post('mod_code'),
                        "inactive"=> $this->input->post('inactive'),
                        "other_desc"=> $this->input->post('other_desc'),
                    );

                    $mod_id = $this->mods_model->get_last_modifier_id($branch);
                    // echo "<pre>",print_r($id),"</pre>";die();
                    $this->mods_model->update_modifiers($items, $mod_id,$branch);
                    $act = 'add';
                    $msg = "Added new modifier: ".$items['name'];                    
                    }
                }
            }           
         }

        if($error == "")
        site_alert($msg,'success');  

        echo json_encode(array("id"=>$id,"desc"=>$this->input->post('name'),"act"=>$act,'msg'=>$msg));
    }
    public function recipe_load($mod_id=null){
        $this->load->model('dine/mods_model');
        $this->load->helper('dine/mods_helper');
        $mod_params = explode('-', $mod_id);//0 index is id 1 index is branch_code
        $details = $this->mods_model->get_modifier_recipe(null,$mod_params[0],null,$mod_params[1]);
        $mods = $this->mods_model->get_modifiers($mod_params[0],$mod_params[1]);
        if(count($mods) > 0){
        $mod=$mods[0];    
        }else{
           $mod=null; 
        }
        // echo "<pre>",print_r($details),"</pre>";die();
        $data['code'] = modRecipeLoad($mod_params[0],$details,$mod,$mod_params[1]);
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'recipeLoadJs';
        $this->load->view('load',$data);
    }
    public function search_items(){
        $search = $this->input->post('search');
        $branch_code = $this->input->post('branch_code');
        $this->load->model('dine/mods_model');
        $found = $this->mods_model->search_items($search,$branch_code);
        $items = array();
        if(count($found) > 0 ){
            foreach ($found as $res) {
                $items[] = array('key'=>$res->code." ".$res->name,'value'=>$res->item_id);
            }
        }
        echo json_encode($items);
    }
    public function get_item_details($item_id=null,$branch_code = null){
        $this->load->model('dine/items_model');
        $items = $this->items_model->get_item($item_id,$branch_code);
        // echo "<pre>",print_r($branch_code),"</pre>";die();
        $item = $items[0];
        $det['cost'] = $item->cost;
        $det['uom'] = $item->uom;
        echo json_encode($det);
    }
    public function recipe_db(){
        $this->load->model('dine/items_model');
        $this->load->model('dine/mods_model');
        $this->load->model('dine/menu_model');
        $dateNow = $this->menu_model->get_date_now();
        $mod_id = $this->input->post('mod_id');
        $item_id = $this->input->post('item-id-hid');
        $gotItem = $this->mods_model->get_modifier_recipe(null,$mod_id,$item_id);
        $branches = $this->menu_model->get_all_branch();
        $date_now = date("Y-m-d h:i:s");    
        foreach ($branches as $value) {
            $item_raw = $this->items_model->get_item($item_id,$value->branch_code);
            $item_cost = $item_raw[0];
            $mod_recipe_id = $this->mods_model->get_last_mod_recipe_id();
            $items[] = array(
                "mod_recipe_id"=>$mod_recipe_id+1,
                "mod_id"=>$mod_id,
                "item_id"=>$item_id,
                "uom"=>$this->input->post('item-uom-hid'),
                "qty"=>$this->input->post('qty'),
                "cost"=>$item_cost->cost,
                "branch_code"=>$value->branch_code
            );
        }
        if(count($gotItem) > 0){//edit
            $det = $gotItem[0];
            $chech = $this->input->post('branch_code');
            unset($items[0]['mod_recipe_id'],$items[0]['branch_code']);
            // echo "<pre>",print_r($det->mod_recipe_id),"</pre>";die();
            $check= $this->mods_model->update_modifier_recipe($items[0],$det->mod_recipe_id);
            $id = $det->mod_recipe_id;
            $act = "update";
            $msg = "Updated Item ".$this->input->post('item-search');
        }else{//add
            // $id = $this->mods_model->add_modifier_recipe($items);
            $id = $this->mods_model->get_last_mod_recipe_id();
            $this->mods_model->add_bulk_modifier_recipe($items);
            $act = "add";
            $msg = "Added New Item ".$this->input->post('item-search');
            $id +=1;
        }
        $this->make->sRow(array('id'=>'row-'.$id));
            $this->make->td($this->input->post('item-search'));
            $this->make->td(num($this->input->post('qty')));
            $this->make->td(num($this->input->post('item-cost')));
            $this->make->td(num($this->input->post('item-cost') * $this->input->post('qty')));
            $a = $this->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-'.$id,'return'=>true));
            $this->make->td($a);
        $this->make->eRow();
        $row = $this->make->code();
        $this->mods_model->update_modifiers(array("update_date"=>$dateNow),$mod_id);
        echo json_encode(array('row'=>$row,'msg'=>$msg,'act'=>$act,'id'=>$id));
    }
    public function remove_recipe_item(){
        $this->load->model('dine/mods_model');
        $this->load->model('dine/menu_model');
        $dateNow = $this->menu_model->get_date_now();
        $this->mods_model->delete_modifier_recipe_item($this->input->post('mod_recipe_id'));
        $this->mods_model->update_modifiers(array("update_date"=>$dateNow),$this->input->post('mod_recipe_id'));
        $json['msg'] = "Item Deleted.";
        echo json_encode($json);
    }
    public function get_recipe_total($asJson=true,$updateDB=true){
        $this->load->model('dine/mods_model');
        $mod_raw = $this->input->post('mod_id');
        $mod_datas = explode('-', $mod_raw);//0 index is = mod_id, 1 index is = branch_code
        $details = $this->mods_model->get_modifier_recipe_prices(null,$mod_datas[0],null,$mod_datas[1]);
        $mods = $this->mods_model->get_modifiers($mod_datas[0],$mod_datas[1]);
        $total = 0;
        foreach ($details as $res) {
            $total += $res->cost * $res->qty;
        }
        // if($updateDB){
        //     $this->mods_model->update_modifiers(array('cost'=>$total),$mod_datas[0],$mod_datas[1]);
        // }

        if($asJson)
            // $sell_price = iSetObj($mod,'cost');
            $sell_price = $mods[0]->cost+$total;
            // echo json_encode($sell_price);
            echo json_encode(array("total"=>num($sell_price)));
    }
    public function update_modifier_price($asJson=true,$updateDB=true){
        $this->load->model('dine/mods_model');
        $total = $this->input->post('total');
        $mod_raw = $this->input->post('mod_id');
        $mod_datas = explode('-', $mod_raw);//0 index is = mod_id, 1 index is = branch_code
        $a = $total;
        $b = str_replace( ',', '', $a );

        if( is_numeric( $b ) ) {
            $a = $b;
        }
        $this->mods_model->update_modifiers(array('cost'=>$a),$mod_datas[0],$mod_datas[1]);
    }
    public function groups(){
        $this->load->model('dine/menu_model');
        $this->load->helper('dine/menu_helper');
        $this->load->helper('site/site_forms_helper');
        $data = $this->syter->spawn('mods');
        $data['page_title'] = fa('icon-book-open')." Group Modifiers";

        $th = array('ID','Code','Name','Branch','Edit');
        $data['code'] = create_rtable('modifier_groups','mod_id','modifier_groups-tbl',$th,'mods/search_modifier_groups_form');
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'modGroupssListFormJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function get_modifier_groups($id=null,$asJson=true){
        $this->load->helper('site/pagination_helper');
        $pagi = null;
        $args = array();
        $total_rows = 30;
        if($this->input->post('pagi'))
            $pagi = $this->input->post('pagi');
        $post = array();
        
        if(count($this->input->post()) > 0){
            $post = $this->input->post();
        }
        if($this->input->post('name')){
            $lk  =$this->input->post('name');
            $args["(modifier_groups.name like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        }
        if($this->input->post('inactive')){
            $args['modifier_groups.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));
        }
        if($this->input->post('branch_id')){
            $args['modifier_groups.branch_code'] = array('use'=>'where','val'=>$this->input->post('branch_id'));
        }
        // $join = null;
        $join["branch_details"] = array('content'=>"branch_details.branch_code = modifier_groups.branch_code");
        $count = $this->site_model->get_tbl('modifier_groups',$args,array(),$join,true,'modifier_groups.*',null,null,true);
        $page = paginate('mods/get_modifier_groups',$count,$total_rows,$pagi);
        $items = $this->site_model->get_tbl('modifier_groups',$args,array(),$join,true,'modifier_groups.*,branch_details.branch_name',null,$page['limit']);
        $json = array();
        if(count($items) > 0){
            $ids = array();
            foreach ($items as $res) {
                // echo "<pre>",print_r($res),"</pre>";die();
                $link = $this->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'mods/group_form/'.$res->mod_group_id.'/'.$res->branch_code,array('class'=>'btn btn-sm blue btn-outline edit','id'=>'edit-'.$res->mod_group_id,'ref'=>$res->mod_group_id,'return'=>'true'));
                $json[$res->mod_group_id."-".$res->branch_code] = array(
                    "id"=>$res->mod_group_id,   
                    "code"=>$res->grp_code,   
                    "title"=>ucwords(strtolower($res->name)),   
                    "branch"=>$res->branch_name,   
                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),
                    "link"=>$link
                );
                $ids[] = $res->mod_group_id;
            }
        }
        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    }
    public function search_modifier_groups_form(){
        $this->load->helper('dine/mods_helper');
        $data['code'] = modGroupSearchForm();
        $this->load->view('load',$data);
    }
    // public function groups(){
    //     $this->load->model('dine/mods_model');
    //     $this->load->helper('dine/mods_helper');
    //     $data = $this->syter->spawn('mods');
    //     $data['page_subtitle'] = "Group Management";
    //     $grps = $this->mods_model->get_modifier_groups();
    //     $data['code'] = modGroupListPage($grps);
    //     $this->load->view('page',$data);
    // }
    public function group_form($mod_group_id=null,$bcode=null){
        $this->load->model('dine/mods_model');
        $this->load->helper('dine/mods_helper');
        $data = $this->syter->spawn('mods');
        $data['page_title'] = fa('icon-book-open')." Group Modifiers";
        if($mod_group_id != null){
            $result = $this->site_model->get_tbl('modifier_groups',array('mod_group_id'=>$mod_group_id));
            $mod = $result[0];
            $data['page_title'] = fa('icon-book-open').' '.$mod->name;
        }
        // echo $bcode;die();
        $data['code'] = modGroupFormPage($mod_group_id,$bcode);
        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js','js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js','js/ui-blockui.min.js','js/jquery.blockui.min.js');
        // $data['add_css'] = 'js/plugins/typeaheadmap/typeaheadmap.css';
        // $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js');
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'modGroupFormJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function group_details_load($mod_group_id=null,$bcode=null){
        $this->load->model('dine/mods_model');
        $this->load->helper('dine/mods_helper');
        $grp=array();
        if($mod_group_id != null){
            $grps = $this->mods_model->get_modifier_groups($mod_group_id,$bcode);
            $grp=$grps[0];
        }
        $data['code'] = modGroupDetailsLoad($grp,$mod_group_id);
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'groupDetailsLoadJs';
        $this->load->view('load',$data);
    }
    public function group_details_db(){
        $this->load->model('dine/mods_model');
        $this->load->model('dine/main_model');
        $branch = $this->input->post('branch_code');
        $this->load->model('dine/items_model');
        $id = "";
        $act = "";
        $msg = "";
        $error = "";
        $f_mod_grp_id = $this->input->post('form_mod_group_id');
        if(!empty($f_mod_grp_id)){
            $branch_selected_ac = $this->input->post('bcode');
            if(!empty($branch_selected_ac)){
                foreach ($branch_selected_ac as $branch) {
                $items = array(
                    "grp_code"=>$this->input->post('grp_code'),
                    "name"=>$this->input->post('name'),
                    "mandatory"=>(int)$this->input->post('mandatory'),
                    "multiple"=>(int)$this->input->post('multiple'),
                    "inactive"=> (int)$this->input->post('inactive'),
                );

                $mod_group_id = $f_mod_grp_id;
            // echo "<pre>",print_r($branch_selected_ac),"</pre>";die();
                $id = $this->mods_model->update_modifier_groups($items, $mod_group_id,$branch);
                $act = 'update';
                $msg = "Updated group modifier: ".$items['name'];
                    
                }
            }else{
                $msg = "Error:No Branch Selected!";
            }
        }else{
            $branch_selected = $this->input->post('b_code');
            if(empty($branch_selected)){
                $msg = "Error:No Branch Selected!";
                 // site_alert($msg,'error');
            }else{
                $branches_code = $this->items_model->get_branch_detail();
                foreach ($branches_code as $key => $b_code) {
                    $items = array(
                        "grp_code"=>$this->input->post('grp_code'),
                        "name"=>$this->input->post('name'),
                        "mandatory"=>(int)$this->input->post('mandatory'),
                        "multiple"=>(int)$this->input->post('multiple'),
                        "branch_code"=>$b_code->branch_code,
                        "inactive"=> 1
                    );

                    $mod_group_id = $this->mods_model->get_last_modifier_group_id($b_code->branch_code);
                    $items['mod_group_id'] =  $mod_group_id + 1;
                    // echo "<pre>",print_r($items),"</pre>";die();
                    $id = $this->mods_model->add_modifier_groups($items);    
                }
                if(!empty($branch_selected)){
                    foreach ($branch_selected as $branch) {
                    $items = array(
                        "grp_code"=>$this->input->post('grp_code'),
                        "name"=>$this->input->post('name'),
                        "mandatory"=>(int)$this->input->post('mandatory'),
                        "multiple"=>(int)$this->input->post('multiple'),
                        "inactive"=> (int)$this->input->post('inactive'),
                    );

                    $mod_group_id = $this->mods_model->get_last_modifier_group_id($branch);
                    // echo "<pre>",print_r($id),"</pre>";die();
                    $this->mods_model->update_modifier_groups($items, $mod_group_id,$branch);
                    $act = 'add';
                    $msg = "Added new group modifier: ".$items['name'];                    
                    }
                }
            }
         }  

        echo json_encode(array("id"=>$id,"desc"=>$this->input->post('name'),"act"=>$act,'msg'=>$msg));
    }
    public function group_modifiers_load($mod_group_id=null,$bcode=null){
        $this->load->model('dine/mods_model');
        $this->load->helper('dine/mods_helper');
        // echo $mod_group_id;
        $details = $this->mods_model->get_modifier_group_details(null,$mod_group_id,null,$bcode,true);

        $data['code'] = groupModifiersLoad($mod_group_id,$details,$bcode);
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'groupRecipeLoadJs';
        $this->load->view('load',$data);
    }
    public function search_modifiers(){
        $search = $this->input->post('search');
        $this->load->model('dine/mods_model');
        $found = $this->mods_model->search_modifiers($search);
        $items = array();
        if(count($found) > 0 ){
            foreach ($found as $res) {
                $items[] = array('key'=>$res->name,'value'=>$res->mod_id);
            }
        }
        echo json_encode($items);
    }
    public function group_modifiers_details_db(){
        $this->load->model('dine/mods_model');
        $this->load->model('dine/items_model');
        $msg= "";
        $act= "";
        $id= "";
        $mod_group_id = $this->input->post('mod_group_id');
        $mod_id = $this->input->post('mod_id');
        $mod_text = $this->input->post('mod_text');
        $branch_code = $this->input->post('branch_code');
        $qty = $this->input->post('qty');
         // echo "<pre>",print_r($_POST),"</pre>";die();
        $ex = explode(',', $branch_code);
        // $branches_code = $this->items_model->get_branch_detail();

        if($branch_code != 'null'){
            // echo 'gege';die();
            foreach ($ex as $branch) {
            $items = array(
                    "mod_group_id" => $mod_group_id,
                    "mod_id" => $mod_id,
                    "qty" => $qty,
                    "terminal_id" => 1,
                    "branch_code" => $branch
                );
                $id_det = $this->mods_model->get_last_modDet_id($branch);
                $items['id'] =  $id_det + 1;
                $id = $this->mods_model->add_modifier_group_details($items);
                $act = 'add';
                $msg = 'Added  Modifier '.$mod_text;

                $items2 = array(
                    "mod_group_id" => $mod_group_id,
                    "branch_code" => $branch
                );
                $this->mods_model->update_group_modifiers($items2,$mod_group_id,$branch);
            }
            // die();
        }else{
            // echo 'haha';die();
            $act = 'error';
            $msg = 'No branch selected!';
            // die();
        }
        // echo $id;
       
            $li = $this->make->li(
                    $this->make->checkbox(null,'dflt_'.$id,0,array('ref'=>$id,'return'=>true))." ".
                    $this->make->span(fa('fa-ellipsis-v'),array('class'=>'handle','return'=>true))." ".
                    $this->make->span($mod_text,array('class'=>'text','return'=>true))." ".
                    $this->make->A(fa('fa-lg fa-times'),'#',array('return'=>true,'class'=>'del','id'=>'del-'.$id,'ref'=>$id)),
                    array('return'=>true,'id'=>'li-'.$id)
                 );
        

        echo json_encode(array("id"=>$id,"desc"=>$mod_text,"act"=>$act,'msg'=>$msg,'li'=>$li));
    }
    public function default_group_modifier(){
        $this->load->model('dine/mods_model');
        $items = array('default'=>$this->input->post('dflt'));
        $this->mods_model->update_modifier_group_details($items,$this->input->post('group_mod_id'));
        // $this->mods_model->delete_modifier_group_details($this->input->post('group_mod_id'));
        $json['msg'] = "Modifier set to default.";
        echo json_encode($json);
    }    
    public function remove_group_modifier(){
        $this->load->model('dine/mods_model');
        $test = array();
        // echo "<pre>",print_r($this->input->post('group_mod_id')),"</pre>";die();
        $this->mods_model->update_modifiers_upd_date($test,$this->input->post('mod_group_id'));
        // echo $this->db->last_query();die();
        $this->mods_model->delete_modifier_group_details($this->input->post('group_mod_id'));
        $json['msg'] = "Modifier Deleted.";
        echo json_encode($json);
    }
    public function mod_sub_load($mod_id=null){
        $this->load->model('dine/mods_model');
        $this->load->helper('dine/mods_helper');

        $mod_subs = $this->mods_model->get_modifier_sub(null,$mod_id);

        $data['code'] = modSubLoad($mod_id,$mod_subs);
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'modsubLoadJs';
        $this->load->view('load',$data);
    }

    public function mod_sub_db(){
        $this->load->model('dine/mods_model');
        $this->load->model('dine/items_model');
        $mod_ids = $this->input->post('mod_id');
        $branch_selected = $this->input->post('b_code');
        if(empty($branch_selected)){
             $msg = "Error:No Branch Selected!";
             // site_alert($msg,'error');
         }else{
            $branches_code = $this->items_model->get_branch_detail();
            foreach ($branches_code as $key => $b_code) {
                    $items = array(
                        // "mod_id"=>$mod_id,
                        "submod_code"=>$this->input->post('submod_code'),
                        "name"=>$this->input->post('name'),
                        "cost"=>$this->input->post('cost'),
                        "group"=>$this->input->post('group'),
                        "is_auto"=>$this->input->post('is_auto'),
                        "qty"=>$this->input->post('qty'),
                        "branch_code"=>$b_code->branch_code,
                    );
                $items['mod_id'] =  $mod_ids;
                $mod_id = $this->mods_model->get_last_modifier_sub($b_code->branch_code);
                $items['mod_sub_id'] =  $mod_id + 1;
                $id = $this->mods_model->add_modifier_sub($items);
                $id = $items['mod_sub_id'];   
            }
        } 




            $act = "add";
            $msg = "Added New Item ".$this->input->post('item-search');

            $auto = 'No';
            if($this->input->post('is_auto') == 1){
                $auto = 'Yes';
            }
        // }
        // $id = 19;
        $this->make->sRow(array('id'=>'row-'.$id,'ref'=>$id,'modsub-name'=>$items['name'],'class'=>'modsub-row'));
            $this->make->td($this->input->post('submod_code'));
            $this->make->td($this->input->post('name'));
            $this->make->td($this->input->post('group'));
            $this->make->td($this->input->post('qty'));
            $this->make->td($auto);
            // $this->make->td(num($this->input->post('qty')));
            $this->make->td(num($this->input->post('cost')),array('style'=>'text-align:right'));
            // $this->make->td(num($this->input->post('item-cost') * $this->input->post('qty')));
            $a = $this->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-'.$id, 'ref'=>$id,'return'=>true));
            $this->make->td($a);
        $this->make->eRow();
        $row = $this->make->code();

        echo json_encode(array('row'=>$row,'msg'=>$msg,'act'=>$act,'id'=>$id));
    }

    public function remove_mod_sub(){
        $this->load->model('dine/mods_model');
        $this->mods_model->delete_modifier_sub($this->input->post('mod_sub_id'));
        $this->mods_model->remove_mod_sub_price(null,$this->input->post('mod_sub_id'));

        // $this->mods_model->db = $this->load->database('main', TRUE);
        // $this->mods_model->delete_modifier_sub($this->input->post('mod_sub_id'));
        // $this->mods_model->remove_mod_sub_price(null,$this->input->post('mod_sub_id'));

        $json['msg'] = "Modifier sub Deleted.";
        echo json_encode($json);
    }
    public function price_load($mod_id=null)
    {
        $this->load->model('dine/mods_model');
        $this->load->helper('dine/mods_helper');

        $det = $this->mods_model->get_mod_prices($mod_id);
        $data['code'] = modPricesLoad($mod_id,$det);
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'modPricesJs';
        $this->load->view('load',$data);
    }

     public function mod_prices_db()
    {
        $this->load->model('dine/mods_model');
        $this->load->model('dine/main_model');
        $this->load->model('dine/items_model');
        if (!$this->input->post())
            header('Location:'.base_url().'mods');
        $branch_selected = $this->input->post('b_code');
        // echo $this->mods_model->db->last_query();die();
        $mod_trans_type = $this->mods_model->get_mod_prices_detail($this->input->post('trans_type'));
        if(empty($branch_selected)){
             $msg = "Error:No Branch Selected!";
             // site_alert($msg,'error');
         }else{
            $branches_code = $this->items_model->get_branch_detail();
            foreach ($branches_code as $key => $b_code) {
                $items = array(
                    'mod_id' => $this->input->post('mod-id-hid'),
                    'trans_type' => $this->input->post('trans_type'),
                    'price' => $this->input->post('price'),
                    "branch_code"=>$b_code->branch_code,
                );
                // echo "<pre>",print_r($mod_trans_type),"</pre>";die();
                if(empty($mod_trans_type)){
                        $mod_id = $this->mods_model->get_last_modifier_prices($b_code->branch_code);
                        $items['id'] =  $mod_id + 1;
                        $id = $this->mods_model->add_mod_price($items);
                        $id = $items['id'];
                }
                // $det = $this->mods_model->get_mod_prices($items['id'],$items['trans_type']);
            }
        } 
        if(!empty($mod_trans_type)){
            foreach ($mod_trans_type as $mtt => $mtt_val) {
                if($mtt_val->trans_type == $this->input->post('trans_type')){
                    echo json_encode(array('result'=>'error','msg'=>'Transaction Type already has a price'));
                }
            }
        }else{
            $this->make->sRow(array('id'=>'row-'.$id,'class'=>'modsub-row'));
            $this->make->td($items['trans_type']);
            $this->make->td(num($items['price']),array('style'=>'text-align:right'));
            $a = $this->make->A(fa('fa-lg fa-times fa-fw'),'#',array('id'=>'del-'.$id,'ref'=>$id,'class'=>'del-item','return'=>true));
            $this->make->td($a,array('style'=>'text-align:right'));
            $this->make->eRow();
            $row = $this->make->code();
            echo json_encode(array('result'=>'success','msg'=>'Price has been added','row'=>$row));
        }

    }

    public function remove_mod_price()
    {
        $this->load->model('dine/mods_model');
        
        $id = $this->input->post('id');
        $this->mods_model->remove_mod_price($id);

        // $this->mods_model->db = $this->load->database('main', TRUE);
        // $this->mods_model->remove_mod_price($id);

        $json['msg'] = 'Price Removed.';
        echo json_encode($json);
    }

    public function mod_sub_price_load($mod_sub_id){
    // public function mod_sub_price_load(){        
        $this->load->model('dine/mods_model');
        $this->load->helper('dine/mods_helper');

        // $mod_sub_id = $this->input->post('mod_sub_id');
        // $mod_sub_name = $this->input->post('mod_sub_name');
        $mod_sub_name = '';
        $mod_sub = $this->mods_model->get_modifier_sub($mod_sub_id);

        if($mod_sub){
            $mod_sub_name = $mod_sub[0]->name;
        }
        $det = $this->mods_model->get_mod_sub_prices($mod_sub_id);
        $data['code'] = modsubPricesLoad($mod_sub_id,$mod_sub_name,$det);
        $data['load_js'] = 'dine/mod.php';
        $data['use_js'] = 'modsubPricesJs';

        $this->load->view('load',$data);
        
    }

     public function mod_sub_prices_db()
    {
        $this->load->model('dine/mods_model');
        $this->load->model('dine/main_model');
        $this->load->model('dine/items_model');
        if (!$this->input->post())
            header('Location:'.base_url().'mods');
        $branch_selected = $this->input->post('b_code');
        $mod_sub_trans_type = $this->mods_model->get_mod_sub_prices_detail($this->input->post('trans_type'));
        if(empty($branch_selected)){
             $msg = "Error:No Branch Selected!";
             // site_alert($msg,'error');
         }else{
        $branches_code = $this->items_model->get_branch_detail();
            foreach ($branches_code as $key => $b_code) {
                $items = array(
                    // 'mod_sub_id' => $this->input->post('modsub-id-hid'),
                    'trans_type' => $this->input->post('trans_type'),
                    'price' => $this->input->post('price'),
                    "branch_code"=>$b_code->branch_code,
                );
                // $id = $this->mods_model->get_last_mod_recipe_id();
                if(empty($mod_sub_trans_type)){
                        // $mod_id = $this->mods_model->get_last_modifier_prices($b_code->branch_code);
                        // $items['id'] =  $mod_id + 1;
                        // $id = $this->mods_model->add_mod_price($items);
                        // $id = $items['id'];
                    $items['mod_sub_id'] =  $this->input->post('modsub-id-hid');
                    $mod_id = $this->mods_model->get_last_modifier_sub_price($b_code->branch_code);
                    $items['id'] =  $mod_id + 1;
                    $id = $this->mods_model->add_mod_sub_price($items);
                    $id = $items['id'];
                }
                // $items['mod_sub_id'] =  $this->input->post('modsub-id-hid');
                // $mod_id = $this->mods_model->get_last_modifier_sub_price($b_code->branch_code);
                // $items['id'] =  $mod_id + 1;
                // $id = $this->mods_model->add_mod_sub_price($items);
                // $id = $items['id'];
                // $det = $this->mods_model->get_mod_sub_prices($items['mod_sub_id'],$items['trans_type']);
                // $det = $this->mods_model->get_mod_prices($items['mod_sub_id'],$items['trans_type'],$items['branch_code']);
                // $id +=1;
            }
        } 
        // $items = array(
        //     'mod_sub_id' => $this->input->post('modsub-id-hid'),
        //     'trans_type' => $this->input->post('trans_type'),
        //     'price' => $this->input->post('price'),
        // );


        // echo 'asdfasdf'; die();

        // if (count($det) == 0) {
            // $id = $this->mods_model->add_mod_sub_price($items);

            // $this->main_model->add_trans_tbl('modifier_sub_prices',$items);

            // $mod_group = $this->menu_model->get_modifier_groups(array('mod_group_id'=>$items['mod_group_id']));
            // $mod_group = $mod_group[0];
        if(!empty($mod_sub_trans_type)){
            foreach ($mod_sub_trans_type as $mtt => $mtt_val) {
                if($mtt_val->trans_type == $this->input->post('trans_type')){
                    echo json_encode(array('result'=>'error','msg'=>'Transaction Type already has a price'));
                }
            }
        }else{
            $this->make->sRow(array('id'=>'modsub-price-row-'.$id));
            $this->make->td($items['trans_type']);
            $this->make->td(num($items['price']),array('style'=>'text-align:right'));
            $a = $this->make->A(fa('fa-lg fa-times fa-fw'),'#',array('id'=>'modsub-price-del-'.$id,'ref'=>$id,'class'=>'del-modsub-price','return'=>true));
            $this->make->td($a,array('style'=>'text-align:right'));
            $this->make->eRow();

            $row = $this->make->code();

            echo json_encode(array('result'=>'success','msg'=>'Price has been added','row'=>$row));
        }

    }

    public function remove_mod_sub_price()
    {
        $this->load->model('dine/mods_model');
        
        $id = $this->input->post('id');
        $this->mods_model->remove_mod_sub_price($id);

        // $this->mods_model->db = $this->load->database('main', TRUE);
        // $this->mods_model->remove_mod_sub_price($id);

        $json['msg'] = 'Price Removed.';
        echo json_encode($json);
    }
}