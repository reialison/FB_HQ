<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Menu extends CI_Controller {

    public function __construct(){

        parent::__construct();

        $this->load->model('dine/menu_model');

        $this->load->helper('dine/menu_helper');

    }

	public function index(){

        $this->load->model('dine/menu_model');

        $this->load->helper('dine/menu_helper');

        $this->load->helper('site/site_forms_helper');

        $data = $this->syter->spawn('menu');

        $data['page_title'] = fa(' icon-book-open').' Menus';

        // $menus = $this->menu_model->get_menus();

        $th = array('ID','Name','Description','Category','Cost','Register Date','Inactive');

        $data['code'] = create_rtable('menus','menu_id','menus-tbl',$th,'menu/search_menus_form',true,'grid','menu/export_form');

// echo $data['code'];die();

        // $data['code'] = menuListPage($menus);

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'listFormJs';

        $data['page_no_padding'] = true;

        $data['sideBarHide'] = true;

        $this->load->view('page',$data);

    }

    public function get_menus($id=null,$asJson=true,$resultOnly=false){

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

    // echo "<pre>",print_r($post),"</pre>";die(); 

        if($this->input->post('menu_name')){

            $lk  =$this->input->post('menu_name');

            $args["(menus.menu_name like '%".$lk."%' OR menus.menu_short_desc like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);

        }

        if($this->input->post('inactive')){

            $args['menus.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));

        }

        else{

            $args['menus.inactive'] = 0;

        }

        if($this->input->post('menu_cat_id')){

            $args['menus.menu_cat_id'] = array('use'=>'where','val'=>$this->input->post('menu_cat_id'));

        }



        if($this->input->post('branch_id')){

            // echo $this->input->post('branch_id');die();

            $args['menus.branch_code'] = array('use'=>'where','val'=>$this->input->post('branch_id'));

        }

            // $args["(menus.menu_id not like '%0%')"] =array('use'=>'where','val'=>"",'third'=>false);

        if($id != null){

            $args = array();

            $args['menus.menu_id'] = $id;

        }

        $join["menu_categories"] = array('content'=>"menus.menu_cat_id = menu_categories.menu_cat_id");

        $join["menu_subcategories"] = array('content'=>"menus.menu_sub_cat_id = menu_subcategories.menu_sub_cat_id");

        $count = $this->site_model->get_tbl('menus',$args,array(),$join,true,'menus.*,menu_categories.menu_cat_name,menu_subcategories.menu_sub_cat_name',null,null,true);

        $page = paginate('menu/get_menus',$count,$total_rows,$pagi);

        if(!$resultOnly){

            $groupm = 'menus.menu_id';

            $items = $this->site_model->get_tbl('menus',$args,array(),$join,true,'menus.*,menu_categories.menu_cat_name,menu_subcategories.menu_sub_cat_name',$groupm,$page['limit']);

            // echo $this->db->last_query();die();

            // $l_q = $this->db->last_query();

        }

        else{

            $groupm = 'menus.menu_id';

            $items = $this->site_model->get_tbl('menus',$args,array(),$join,true,'menus.*,menu_categories.menu_cat_name,menu_subcategories.menu_sub_cat_name',$groupm);

            return $items;

        }

        $json = array();

        if(count($items) > 0){

            $ids = array();

            foreach ($items as $res) {

                $link = $this->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'menu/form/'.$res->menu_id.'/'.$res->branch_code,array('class'=>'btn blue btn-sm btn-outline','return'=>'true'));

                $json[$res->menu_id.'/'.$res->branch_code] = array(

                    "id"=>$res->menu_id,   

                    // "branch_code"=>$res->branch_code,

                    "title"=>"[".$res->menu_code."] ".ucwords(strtolower($res->menu_name)),   

                    "desc"=>ucwords(strtolower($res->menu_short_desc)),   

                    "subtitle"=>ucwords(strtolower($res->menu_cat_name)),   

                    "caption"=>"PHP ".num($res->cost),

                    "date_reg"=>sql2Date($res->reg_date),

                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),

                    "link"=>$link,

                );

                $ids[] = $res->menu_id;

            }

            $images = $this->site_model->get_image(null,null,'menus',array('images.img_ref_id'=>$ids)); 

            foreach ($images as $res) {

                if(isset($json[$res->img_ref_id])){

                    $js = $json[$res->img_ref_id];

                    $js['image'] = $res->img_path;

                    $json[$res->img_ref_id] = $js;

                }

            }

        }

        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));

    }

    public function search_menus_form(){

        $data['code'] = menuSearchForm();

        $this->load->view('load',$data);

    }

    public function export_form(){

        $data['code'] = exportItemsForm();

        $this->load->view('load',$data);

    }

    public function export_data(){

        $this->load->model('dine/menu_model');

        

        $branch_id = $this->input->post('branch_id');

        // echo $branch_id;

        

        $menus = $this->site_model->get_exportData($branch_id);

        // echo "<pre>",print_r($data),"</pre>";die();

        $filename = date('Ymd')."- Menu List.csv";

        header('Content-Type: application/csv');

        header('Content-Disposition: attachment; filename="'.$filename.'";');

        $f = fopen('php://output', 'w');

        foreach ($menus as $val) {

                     $csv = array(

                    "menu_id"=>$val->menu_id,

                    "menu_code"=>$val->menu_code,

                    "menu_short_desc"=>$val->menu_short_desc,

                    "menu_name"=>$val->menu_name,

                    "menu_cat_name"=>$val->menu_cat_name,

                    "menu_cat_id"=>$val->menu_cat_id,

                    "menu_sub_cat_name"=>$val->menu_sub_cat_name,

                    "menu_sub_cat_id"=>$val->menu_sub_cat_id,

                    "cost"=>$val->cost

                    );

            fputcsv($f, $csv);

        }

        // $data['code'] = exportItemsForm();

        // $this->load->view('load',$data);

    }



    public function form($menu_id=null,$branch_code=NULL){

        $this->load->model('dine/menu_model');

        $this->load->helper('dine/menu_helper');

        $data = $this->syter->spawn('menu');

        $img = "";

        $data['page_title'] = fa('icon-book-open').' Add New Menu';

        if($menu_id != null){

            $result = $this->site_model->get_tbl('menus',array('menu_id'=>$menu_id));

            $menu = $result[0];

            $data['page_title'] = fa('icon-book-open').' '.$menu->menu_name;

            $images = $this->site_model->get_image(null,null,'menus',array('images.img_ref_id'=>$menu_id)); 

            foreach ($images as $res) {

                $img = $res->img_path;

            }

        }

        $data['code'] = menuFormPage($menu_id,$img,$branch_code);

        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');

        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js' ,'js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'menuFormJs';

        // $data['page_no_padding'] = true;

        $this->load->view('page',$data);

    }



    public function upload_image_load($menu_id=null){

        $res = array();

        if($menu_id != null){

            $result = $this->site_model->get_image(null,$menu_id,'menus');

            if(count($result) > 0)

                $res = $result[0];

        }

        $data['code'] = menuImagesLoad($menu_id,$res);

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'menuImageJs';

        $this->load->view('load',$data);

    }

    public function images_db(){

        $image = null;

        // if(is_uploaded_file($_FILES['fileUpload']['tmp_name'])) {

        //     $image = file_get_contents($_FILES['fileUpload']['tmp_name']);

        // }

        $ext = null;

        $msg = "";

        if(is_uploaded_file($_FILES['fileUpload']['tmp_name'])){

            $info = pathinfo($_FILES['fileUpload']['name']);

            if(isset($info['extension']))

            $ext = $info['extension'];

            $menu = $this->input->post('upload_menu_id');

            $newname = $menu.".".$ext;

            if (!file_exists("uploads/menus/")) {

                mkdir("uploads/menus/", 0777, true);

            }

            $target = 'uploads/menus/'.$newname;

            if(!move_uploaded_file( $_FILES['fileUpload']['tmp_name'], $target)){

                $msg = "Image Upload failed";

            }

            else{

                $new_image = $target;

                $result = $this->site_model->get_image(null,$this->input->post('upload_menu_id'),'menus');

                $items = array(

                    "img_file_name" => $newname,

                    "img_path" => $new_image,

                    "img_ref_id" => $this->input->post('upload_menu_id'),

                    "img_tbl" => 'menus',

                );

                if(count($result) > 0){

                    $this->site_model->update_tbl('images','img_id',$items,$result[0]->img_id);

                }

                else{

                    $id = $this->site_model->add_tbl('images',$items,array('datetime'=>'NOW()'));

                }

            }

            ####

        }



        echo json_encode(array('msg'=>$msg));

    }

    public function details_load($menu_id=null,$branch_code=null){

        $this->load->helper('dine/menu_helper');

        $this->load->model('dine/menu_model');

        $menu=array();

        if($menu_id != null){

            $menus = $this->menu_model->get_menus($menu_id,null,false,null,$branch_code);

            // echo $this->db->last_query();die();

            $menu=$menus[0];

        }

        // echo "<pre>",print_r($branch_code),"</pre>";die();

        $data['code'] = menuDetailsLoad($menu,$menu_id,$branch_code);

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'detailsLoadJs';

        $this->load->view('load',$data);

    }

    public function details_db(){

        $this->load->model('dine/menu_model');



        $this->load->model('dine/main_model');

        $branch_code = $this->input->post('b_code');

        $copy_to_all = $this->input->post('copy_to_all');

        $menu_id = $this->input->post('form_menu_id');

        $branch_edit = $this->input->post('f_branch_id');

        // echo "<pre>",count($branch_code),"</pre>";die();    

        $branches = $this->menu_model->get_all_branch();

        $miaa = null;



        $date = $this->input->post('date_effective');

        $date_effective = $date != '' ? date2Sql($date) : '';

        $miaa = "";

        if($this->input->post('miaa_cat')){

            $miaa = $this->input->post('miaa_cat');

        }   



        foreach ($branches as $value) {

            $menu_id = $this->menu_model->get_last_menu_id($branch_code);

            $dateNow = $this->menu_model->get_date_now();

            // echo "<pre>",print_r($dateNow),"<pre>";die();

            // $items['menu_id'] =  $menu_id + 1;

            $items[] = array(

                "menu_id" => $menu_id+1,

                "menu_code"=>$this->input->post('menu_code'),

                "menu_cat_id"=>$this->input->post('menu_cat_id'),

                "menu_sub_cat_id"=>$this->input->post('menu_sub_cat_id'),

                "menu_sub_id"=>$this->input->post('menu_sub_id'),

                "menu_barcode"=>$this->input->post('menu_barcode'),

                "menu_sched_id"=>$this->input->post('menu_sched_id'),

                "menu_short_desc"=>$this->input->post('menu_short_desc'),

                "menu_name"=>$this->input->post('menu_name'),

                "branch_code"=>$value->branch_code,

                "cost"=>$this->input->post('cost'),

                "reg_date"=>$dateNow,

                "costing"=>$this->input->post('costing'),

                "no_tax"=>(int)$this->input->post('no_tax'),

                "free"=>(int)$this->input->post('free'),

                // "kitchen_number"=>(int)$this->input->post('kitchen_number'),

                // "time_preparation"=>(int)$this->input->post('time_preparation'),

                "miaa_cat"=>$miaa,

                "inactive"=>1,

                "date_effective"=>$date_effective

            );

        }

        if($this->input->post('new')){

           $menu_id = $this->menu_model->get_last_menu_id($branch_code);

           // var_dump($menu_id);die();

            $items['menu_id'] =  $menu_id + 1;

            $id = $this->menu_model->add_menus($items);



            $this->set_menu_history($items);



            $act = 'add';

            $msg = 'Added new Menu '.$this->input->post('menu_name');

            // $items['menu_id'] = $id;

            // $this->main_model->add_trans_tbl('menus',$items);

            site_alert($msg,'success');

        }

        else{

            if($this->input->post('form_menu_id')){

                $menu_id = $this->input->post('form_menu_id');

                unset($items[0]['menu_id'],$items[0]['branch_code']);

                $inactive = array(

                    "inactive"=>$this->input->post('inactive'),

                );

                if(!empty($branch_code)){

                    foreach ($branch_code as $value) {

                        // echo "<pre>",print_r($menu_id),"</pre>";die();

                        $items[0]['branch_code'] = $value;

                        $items[0]['menu_id'] = $menu_id;                        



                        $this->menu_model->update_menus($items[0],$menu_id,$value);

                        $check = $this->menu_model->update_menus($inactive,$menu_id,$value);



                        $this->set_menu_history($items[0]);

                        

                    }



                }

                else{

                    $this->menu_model->update_menus($items[0],$this->input->post('form_menu_id'),$branch_edit);

                    $check = $this->menu_model->update_menus($inactive,$this->input->post('form_menu_id'),$branch_edit);



                    $items[0]['branch_code'] = $branch_edit;

                    $items[0]['menu_id'] = $menu_id;

                    

                    $this->set_menu_history($items[0]);

                }



                



                $id = $this->input->post('form_menu_id');

                $act = 'update';

                $msg = 'Updated Menu '.$this->input->post('menu_name');

                // die();

                // if($copy_to_all == '1'){

                   // $this->main_model->update_trans_tbl('menus',array('menu_id'=>$id),$items,$id);

                // }else{

                   // $this->main_model->update_trans_tbl('menus',array('menu_id'=>$id,'branch_code'=>$branch_code),$items,$id);



                // }

                // echo "<pre>",print_r($this->input->post()),"</pre>";die();

                // echo $this->db->last_query();die();

            }else{

                    $id = $this->menu_model->add_bulk_menus($items);

                  //  $this->add_bulk_menu_history($items);

                    $inactive = array(

                        "inactive"=>0,

                    );

                    foreach ($branch_code as $value) {

                        // echo "<pre>",print_r($value),"</pre>";die();

                        $this->menu_model->update_menus($inactive,$items[0]['menu_id'],$value);



                        $items[0]['branch_code'] = $value;

                        $this->set_menu_history($items[0]);

                    }

                    $act = 'add';

                    $msg = 'Added new Menu '.$this->input->post('menu_name');

                }

            site_alert($msg,'success');

        }



        echo json_encode(array("id"=>$id,"desc"=>$this->input->post('menu_name'),"act"=>$act,'msg'=>$msg,'branch_code'=>$branch_code));

    }



    public function add_bulk_menu_history($menus){

        $data = array();



        $user = $this->session->userdata('user');

        $user_id = $user['id'];



        foreach ($menus as  $value) {

           $value['user_id'] = $user_id;



           $menu = array(

                    'menu_id'=>$value['menu_id'],

                    'branch_code'=>$value['branch_code'],

                    'cost'=>$value['costing'],

                    'selling'=>$value['cost'],

                    'user_id'=>$user_id,

                    'date_time'=>$value['reg_date']

                );



           $data[]=$menu;

        }



        $this->menu_model->add_bulk_menu_history($data);

    }



    public function set_menu_history($menus){

        $user = $this->session->userdata('user');

        $user_id = $user['id'];



        $menu = array(

                    'menu_id'=>$menus['menu_id'],

                    'branch_code'=>$menus['branch_code'],

                    'cost'=>$menus['costing'],

                    'selling'=>$menus['cost'],

                    'user_id'=>$user_id,

                    'date_time'=>$menus['reg_date']

                );



        $this->menu_model->add_menu_history($menu);

    }



    public function recipe_load($menu_id=null,$branch=null){

        $det = $this->menu_model->get_recipe_items($menu_id,null,null,$branch);

        $det_branch = $this->menu_model->get_recipe_items_branch($menu_id,null,null);

        // echo "<pre>",print_r($det),"</pre>";die();

        $data['code'] = menuRecipeLoad($menu_id,null,$det,$branch,$det_branch);

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'recipeLoadJs';

        $this->load->view('load',$data);

    }

    public function recipe_search_item(){

        $search = $this->input->post('search');

        $results = $this->menu_model->search_items($search);

        $items = array();

        if(count($results) > 0 ){

            foreach ($results as $res) {

                $items[] = array('key'=>$res->code." ".$res->name,'value'=>$res->item_id);

            }

        }

        echo json_encode($items);

    }

    public function recipe_item_details($item_id=null,$branch=null){

        $this->load->model('dine/items_model');

        // echo "<pre>",print_r($branch),"</pre>";die();   

        $items = $this->items_model->get_item($item_id,$branch);

        $item = $items[0];

        $det['cost'] = $item->cost;

        $det['branch_code'] = $item->branch_code;

        $det['uom'] = $item->uom;

        echo json_encode($det);

    }

    public function recipe_details_db(){

        $this->load->model('dine/items_model');

        $branch_code = $this->input->post('b_code');

        $branches = $this->menu_model->get_all_branch();

        $dateNow = $this->menu_model->get_date_now();

        $item_id = $this->input->post('item-id-hid');   

        // echo "<pre>",$this->input->post('recipe-branch-hid'),"</pre>";die();

        // $date_now = date("Y-m-d h:i:s");    

        foreach ($branches as $value) {

            $item_raw = $this->items_model->get_item($item_id,$value->branch_code);

            $item_cost = $item_raw[0];

            $recipe_id = $this->menu_model->get_last_recipe_id();

            $items[] = array(

                'recipe_id'=>$recipe_id+1,

                'menu_id' => $this->input->post('menu-id-hid'),

                'item_id' => $item_id,

                'uom' => $this->input->post('item-uom-hid'),

                'qty' => $this->input->post('qty'),

                'cost' => $item_cost->cost,

                'branch_code' => $value->branch_code

            );

        }

        // $items_data = explode(",", $items[0]['item_id']);

        $recipe_det = $this->menu_model->get_recipe_items($items[0]['menu_id'],$items[0]['item_id'],null,$this->input->post('item-branch-hid'));

        if (count($recipe_det) > 0) {

            $det = $recipe_det[0];

            // foreach ($branches as $value) {

            unset($items[0]['recipe_id'],$items[0]['branch_code']);

            $check = $this->menu_model->update_recipe_item($items[0],$items[0]['menu_id'],$items[0]['item_id']);

            // }

            // echo "<pre>",print_r($check),"</pre>";die();

            $id = $det->recipe_id;

            $item_name = $det->item_name;

            $act = "update";

            $msg = "Updated item: ".$item_name;

        } else {

            // die();''/

            $this->load->model('dine/items_model');

            $detx = $this->items_model->get_item($items[0]['item_id'],$this->input->post('item-branch-hid'));

            $detx = $detx[0];

            $item_name = $detx->name;



            $this->menu_model->add_bulk_menu_recipe($items);

            $id = $this->menu_model->get_last_recipe_id();

            $act = 'add';

            $msg = 'Add new item: '.$this->input->post('menu_cat_name');

            // $this->main_model->add_trans_tbl('menu_categories',$items);

            site_alert($msg,'success');

        }

        $check = $this->menu_model->update_menus(array("update_date"=>$dateNow),$this->input->post('menu-id-hid'));

        $this->make->sRow(array('id'=>'row-'.$id));

            $this->make->td($item_name);

            $this->make->td($items[0]['uom']);

            $this->make->td($items[0]['cost']);

            $this->make->td($items[0]['qty']);

            $line_total = $items[0]['qty'] * $items[0]['cost'];

            $this->make->td(num($line_total));

            $a = $this->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-'.$id,'ref'=>$id,'class'=>'del-item','return'=>true));

            $this->make->td($a);

        $this->make->eRow();

        $row = $this->make->code();



        // echo "<pre>",print_r($act),"</pre>";die();

        echo json_encode(array('id'=>$id,'row'=>$row,'msg'=>$msg,'act'=>$act));

    }

    public function override_price_total($asJson=true,$updateDB=true){

        $this->load->model('resto/menu_model');

        $total = $this->input->post('total');

        $menu_id = $this->input->post('menu_id');

        $a = $total;

        $b = str_replace( ',', '', $a );



        if( is_numeric( $b ) ) {

            $a = $b;

        }

        $this->menu_model->update_menus(array('cost'=>$a),$menu_id);

    }

    public function get_recipe_total(){

        $menu_id = $this->input->post('menu_id');

        $branch = $this->input->post('branch_code');

        $total = 0;

        $recipe_det = $this->menu_model->get_recipe_items($menu_id,null,null,$branch);

        foreach ($recipe_det as $val) {

            $total += ($val->item_cost * $val->qty);

        }

        // echo "<pre>",print_r($total),"</pre>";die();

        echo json_encode(array('total'=>num($total)));

    }

    public function remove_recipe_item(){

        $recipe_id = $this->input->post('recipe_id');

        $dateNow = $this->menu_model->get_date_now();

        $this->menu_model->remove_recipe_item($recipe_id);

        $check = $this->menu_model->update_menus(array("update_date"=>$dateNow),$recipe_id);

        $json['msg'] = "Recipe Item Deleted.";

        echo json_encode($json);

    }

    /**********     Menu Modifier Groups   **********/

    public function modifier_load($menu_id=null,$branch_code=null)

    {

        $det = $this->menu_model->get_menu_modifiers($menu_id,null,null,$branch_code);

        // echo "<pre>",print_r($det),"</pre>";die();

        $data['code'] = menuModifierLoad($menu_id,$det);

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'menuModifierJs';

        $this->load->view('load',$data);

    }

    public function modifier_search_item()

    {

        $search = $this->input->post('search');

        $results = $this->menu_model->search_modifier_groups($search);

        $items = array();

        if(count($results) > 0 ){

            foreach ($results as $res) {

                $items[] = array('key'=>$res->mod_group_id." ".$res->name,'value'=>$res->mod_group_id);

            }

        }

        echo json_encode($items);

    }

    public function menu_modifier_db()

    {

        $branches = $this->menu_model->get_all_branch();

        $mod_id = $this->menu_model->get_last_menu_mod();

        if (!$this->input->post())

            header('Location:'.base_url().'menu');

        foreach ($branches as $value) {

            $items[] = array(

                'id'=>$mod_id+1,

                'menu_id' => $this->input->post('menu-id-hid'),

                'mod_group_id' => $this->input->post('mod-group-id-hid'),

                'branch_code'=>$value->branch_code

            );

        }

        $det = $this->menu_model->get_menu_modifiers($items[0]['menu_id'],$items[0]['mod_group_id']);

        // echo "<pre>",print_r($det),"</pre>";die();

        if (count($det) == 0) {

            $this->menu_model->add_bulk_menu_modifier($items);

            $id = $items[0]['id'];

            $mod_group = $this->menu_model->get_modifier_groups(array('mod_group_id'=>$items[0]['mod_group_id']));

            $mod_group = $mod_group[0];



            $this->make->sRow(array('id'=>'row-'.$id));

                $this->make->td(fa('fa-asterisk')." ".$mod_group->name);

                $a = $this->make->A(fa('fa-lg fa-times fa-fw'),'#',array('id'=>'del-'.$id,'ref'=>$id,'class'=>'del-item','return'=>true));

                $this->make->td($a,array('style'=>'text-align:right'));

            $this->make->eRow();



            $row = $this->make->code();



            echo json_encode(array('result'=>'success','msg'=>'Modifier group has been added','row'=>$row));

        } else

            echo json_encode(array('result'=>'error','msg'=>'Menu already has modifier group'));



    }

    public function remove_menu_modifier()

    {

        $id = $this->input->post('id');

        $this->menu_model->remove_menu_modifier($id);

        $json['msg'] = 'Removed modifier group';

        echo json_encode($json);

    }

    /*******   End of  Menu Modifier Groups   *******/

    public function categories(){

        $this->load->model('dine/menu_model');

        $this->load->helper('site/site_forms_helper');

        $menu_categories = $this->menu_model->get_menu_categories();

        $data = $this->syter->spawn('menu');

        $data['page_title'] = fa(' icon-book-open')."Menu Categories";

        // $data['page_subtitle'] = "Categories";

        // $data['code'] = site_list_form("menu/categories_form","categories_form","Categories",$menu_categories,'menu_cat_name',"menu_cat_id");

        // $data['add_js'] = 'js/site_list_forms.js';

        $th = array('ID','Name','Reg Date','Branch','Edit');

        $data['code'] = create_rtable('menu_categories','menu_cat_id','categories-tbl',$th,'menu/search_cat_menus_form');

        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');

        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js' ,'js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'categoryListJs';

        $data['page_no_padding'] = true;

        $this->load->view('page',$data);

    }

    public function get_menu_categories($id=null,$asJson=true){

        $this->load->helper('site/pagination_helper');

        $pagi = null;

        $args = array();

        $total_rows = 1000;

        if($this->input->post('pagi'))

            $pagi = $this->input->post('pagi');

        $post = array();

        

        if(count($this->input->post()) > 0){

            $post = $this->input->post();

        }

        if($this->input->post('menu_cat_name')){

            $lk  =$this->input->post('menu_cat_name');

            $args["(menu_categories.menu_cat_name like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);

        }

        if($this->input->post('inactive')){

            $args['menu_categories.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));

        }

        $count = $this->site_model->get_tbl('menu_categories',$args,array(),null,true,'menu_categories.*',null,null,true);

        $page = paginate('menu/get_menu_categories',$count,$total_rows,$pagi);

        $items = $this->site_model->get_tbl('menu_categories',$args,array(),null,true,'menu_categories.*',null,$page['limit']);

        $json = array();

        if(count($items) > 0){

            foreach ($items as $res) {

                $link = $this->make->A(fa('fa-edit fa-lg').' Edit','#',array('class'=>'btn blue btn-sm btn-outline edit','id'=>'edit-'.$res->menu_cat_id.'-'.$res->branch_code,'ref'=>$res->menu_cat_id,'branch'=>$res->branch_code,'return'=>'true'));

                $json[$res->menu_cat_id.'/'.$res->branch_code] = array(

                    "id"=>$res->menu_cat_id,   

                    "title"=>ucwords(strtolower($res->menu_cat_name)),   

                    "date_reg"=>sql2Date($res->reg_date),

                    "branch_code"=>$res->branch_code,

                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),

                    "link"=>$link

                );

            }

        }

        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));

    }

    public function categories_form($ref=null,$branch=null){

        $this->load->helper('dine/menu_helper');

        $this->load->model('dine/menu_model');

        $cat = array();

        if($ref != null){

            $cats = $this->menu_model->get_menu_categories($ref,false,$branch);

            $cat = $cats[0];

        }

        // echo "<pre>",print_r($cats),"</pre>";die();

        $this->data['code'] = makeMenuCategoriesForm($cat,null,$branch);

        $this->data['add_js'] = array('js/ui-blockui.min.js','js/jquery.blockui.min.js');

        $this->load->view('load',$this->data);

    }

    public function categories_form_db(){

        $this->load->model('dine/menu_model');

        $branch_code = $this->input->post('b_code');

        // $this->load->model('dine/main_model');

        $items = array();

        $branch_edit = $this->input->post('f_branch_id');

        // if($branch_code == null OR $branch_code == ""){

        $branches = $this->menu_model->get_all_branch($branch_code);

        // echo "<pre>",print_r($date_now),"</pre>";die();

        foreach ($branches as $value) {

            $menu_cat_id = $this->menu_model->get_menu_categories_last_id($value->branch_code);

            $dateNow = $this->menu_model->get_date_now();

            // $items['menu_id'] =  $menu_id + 1;

            $items[] = array(

                "menu_cat_id"=>$menu_cat_id+1,

                "reg_date"=>$dateNow,

                "arrangement"=>$this->input->post('cat_arrangement'),

                "menu_cat_name"=>$this->input->post('menu_cat_name'),

                "menu_sched_id"=>$this->input->post('menu_sched_id'),

                "branch_code"=>$value->branch_code,

                "inactive"=>1

            );

        }

        if($this->input->post('menu_cat_id')){

            // echo "<pre>",print_r($items),"</pre>";die();

            unset($items[0]['menu_cat_id'],$items[0]['branch_code']);

                // echo "<pre>",print_r($branch_code),"</pre>";die();

            // $this->menu_model->update_menu_categories($items[0],$this->input->post('menu_cat_id'));

            $inactive = array(

                "inactive"=>$this->input->post('inactive'),

            );

            if(!empty($branch_code)){

                foreach ($branch_code as $value) {

                    $this->menu_model->update_menu_categories($items[0],$this->input->post('menu_cat_id'),$value);

                    $this->menu_model->update_menu_categories($inactive,$this->input->post('menu_cat_id'),$value);

                    $id = $this->input->post('menu_cat_id');

                    $act = 'update';

                    $msg = 'Updated Menu Category  '.$this->input->post('menu_cat_name');

                    // $this->main_model->update_tbl('menu_categories','menu_cat_id',$items,$id);

                    site_alert($msg,'success');

                }

            }

            else{

                // $act = 'update';

                $msg = 'Error! Please select branch. ';

                site_alert($msg,'error');

                // $check = $this->menu_model->update_menu_categories($items[0],$this->input->post('menu_cat_id'),$branch_edit);

                // $this->menu_model->update_menu_categories($inactive,$this->input->post('menu_cat_id'),$branch_edit);

            }





        }else{



            $this->menu_model->add_bulk_menus_cat($items);

            $inactive = array(

                "inactive"=>0,

            );

            if(!empty($branch_code)){

                foreach ($branch_code as $value) {

                   $check = $this->menu_model->update_menu_categories($inactive,$items[0]['menu_cat_id'],$value);

                }

            }

            else{

                foreach ($branches as $value) {

                   $check = $this->menu_model->update_menu_categories($inactive,$items[0]['menu_cat_id'],$value->branch_code);

                }

            // echo "<pre>",print_r($check),"</pre>";die();

            }

            $id = $this->menu_model->get_menu_categories_last_id($items[0]['branch_code']);

            $act = 'add';

            $msg = 'Added  new Menu Category '.$this->input->post('menu_cat_name');

            // $this->main_model->add_trans_tbl('menu_categories',$items);

            site_alert($msg,'success');

        }

        echo json_encode(array("id"=>$id,"addOpt"=>$items[0]['menu_cat_name'],"desc"=>$this->input->post('menu_cat_name'),"act"=>$act,'msg'=>$msg));

    }

    public function search_cat_menus_form(){

        $data['code'] = menuCatSearchForm();

        $this->load->view('load',$data);

    }

    public function subcategories(){

        $this->load->model('dine/menu_model');

        $this->load->helper('dine/menu_helper');

        $this->load->helper('site/site_forms_helper');

        $data = $this->syter->spawn('menu');

        $data['page_title'] = fa(' icon-book-open')."Menu Types";

        $th = array('ID','Name','Branch','Inactive');

        $data['code'] = create_rtable('menu_subcategories','menu_sub_cat_id','subcategories-tbl',$th);

        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');

        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js' ,'js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'subcategoryListJs';

        $data['page_no_padding'] = true;

        $this->load->view('page',$data);

    }

    public function get_subcategories($id=null,$asJson=true){

        $this->load->helper('site/pagination_helper');

        $pagi = null;

        $args = array();

        $total_rows = 1000;

        if($this->input->post('pagi'))

            $pagi = $this->input->post('pagi');

        $post = array();

        

        if(count($this->input->post()) > 0){

            $post = $this->input->post();

        }

        if($this->input->post('menu_sub_cat_name')){

            $lk  =$this->input->post('menu_sub_cat_name');

            $args["(menu_subcategories.menu_sub_cat_name like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);

        }

        if($this->input->post('inactive')){

            $args['menu_subcategories.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));

        }

        $join = null;

        $count = $this->site_model->get_tbl('menu_subcategories',$args,array(),$join,true,'menu_subcategories.*',null,null,true);

        $page = paginate('menu/get_subcategories',$count,$total_rows,$pagi);

        $items = $this->site_model->get_tbl('menu_subcategories',$args,array(),$join,true,'menu_subcategories.*',null,$page['limit']);

        $json = array();

        if(count($items) > 0){

            $ids = array();

            foreach ($items as $res) {

                $link = $this->make->A(fa('fa-edit fa-lg').' Edit','#',array('class'=>'btn blue btn-sm btn-outline edit','id'=>'edit-'.$res->menu_sub_cat_id.'-'.$res->branch_code,'ref'=>$res->menu_sub_cat_id,'branch'=>$res->branch_code,'return'=>'true'));

                $json[$res->menu_sub_cat_id.'/'.$res->branch_code] = array(

                    "id"=>$res->menu_sub_cat_id,   

                    "title"=>ucwords(strtolower($res->menu_sub_cat_name)),

                    "branch_code"=>$res->branch_code,   

                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),

                    "link"=>$link

                );

                $ids[] = $res->menu_sub_cat_id;

            }

        }

        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));

    }

    public function subcategories_form($ref=null,$branch=null){

        $this->load->helper('dine/menu_helper');

        $this->load->model('dine/menu_model');

        $cat = array();

        if($ref != null){

            $cats = $this->menu_model->get_menu_subcategories($ref,null,$branch);

            $cat = $cats[0];

        }

        $this->data['code'] = makeMenuSubCategoriesForm($cat,$branch);

        $this->data['add_js'] = array('js/ui-blockui.min.js','js/jquery.blockui.min.js');

        $this->load->view('load',$this->data);

    }

    public function subcategories_form_db(){

        $this->load->model('dine/menu_model');

        $this->load->model('dine/main_model');

        $branch_code = $this->input->post('b_code');

        $branch_edit = $this->input->post('f_branch_id');

        $items = array();

        $branches = $this->menu_model->get_all_branch($branch_code);

        // $subcat_id = 0;    

        foreach ($branches as $value) {

            $subcat_id = $this->menu_model->get_last_subcat_id($value->branch_code);

            $dateNow = $this->menu_model->get_date_now();



            // $items['menu_id'] =  $menu_id + 1;

            $items[] = array(

                "menu_sub_cat_id"=>$subcat_id+1,

                "branch_code"=>$value->branch_code,

                "reg_date"=>$dateNow,

                "menu_sub_cat_name"=>$this->input->post('menu_sub_cat_name'),

                "inactive"=>1

            );

        }



        if($this->input->post('menu_sub_cat_id')){

            unset($items[0]['menu_sub_cat_id'],$items[0]['branch_code']);

            $inactive = array(

                "inactive"=>$this->input->post('inactive'),

            );

            if(!empty($branch_code)){

                foreach ($branch_code as $value) {

                    $this->menu_model->update_menu_subcategories($items[0],$this->input->post('menu_sub_cat_id'),$value);

                    $this->menu_model->update_menu_subcategories($inactive,$this->input->post('menu_sub_cat_id'),$value);

                    $id = $this->input->post('menu_sub_cat_id');

                    $act = 'update';

                    $msg = 'Updated Menu Sub Category . '.$this->input->post('menu_sub_cat_name');

                }

                //     echo "<pre>",print_r($value),"</pre>";

                // die();

                site_alert($msg,'success');

            }

            else{

                $act = 'error';

                $msg = 'Error! Please select branch. ';

                site_alert($msg,'error');

                // $check = $this->menu_model->update_menu_subcategories($items[0],$this->input->post('menu_sub_cat_id'),$branch_edit);

                // $this->menu_model->update_menu_subcategories($inactive,$this->input->post('menu_sub_cat_id'),$branch_edit);

            }





        }else{

            $this->menu_model->add_bulk_menu_subcat($items);

            $inactive = array(

                "inactive"=>0,

            );

            if(!empty($branch_code)){

                foreach ($branch_code as $value) {

                   $check = $this->menu_model->update_menu_subcategories($inactive,$items[0]['menu_sub_cat_id'],$value);

                }

            }

            $id = $this->menu_model->get_last_subcat_id($items[0]['branch_code']);

            // echo "<pre>",print_r($id),"</pre>";die();



            // else{

            //     foreach ($branches as $value) {

            //        $check = $this->menu_model->update_menu_subcategories($inactive,$items[0]['menu_sub_cat_id'],$value->branch_code);

            //     }

            // // echo "<pre>",print_r($check),"</pre>";die();

            // }





            $act = 'add';

            $msg = 'Added  new Menu Sub Category '.$this->input->post('menu_sub_cat_name');

            site_alert($msg,'success');

        }

        echo json_encode(array("id"=>$id,"addOpt"=>$items[0]['menu_sub_cat_name'],"desc"=>$this->input->post('menu_sub_cat_name'),"act"=>$act,'msg'=>$msg));

    }

    public function schedules(){

        $this->load->model('dine/menu_model');

        $this->load->helper('site/site_forms_helper');

        $menu_schedules = $this->menu_model->get_menu_schedules();

        $data = $this->syter->spawn('menu');

        $data['page_title'] = fa('icon-calendar')." Schedules";

        $data['code'] = site_list_form("menu/schedules_form","schedules_form","Schedules",$menu_schedules,'desc',"menu_sched_id");



        $data['add_js'] = 'js/site_list_forms.js';



        $this->load->view('page',$data);

    }

    public function schedules_form($ref=null){

        $this->load->helper('dine/menu_helper');

        $this->load->model('dine/menu_model');

        $sch = array();

        // if($ref == null)    $ref = $this->input->post('menu_sched_id');

        if($ref != null){

            $schs = $this->menu_model->get_menu_schedules($ref);

            // echo 'REF :: '.$ref;

            $sch = $schs[0];

        }

        $dets = $this->menu_model->get_menu_schedule_details($ref);



        $data['code'] = makeMenuSchedulesForm($sch,$dets);

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'scheduleJs';

        $this->load->view('load',$data);

    }

    public function menu_sched_db(){

        $this->load->model('dine/menu_model');

        $this->load->model('dine/main_model');

        $items = array();

        $items = array("desc"=>$this->input->post('desc'),

                        "inactive"=>(int)$this->input->post('inactive')

            );

        $id = $this->input->post('menu_sched_id');

        $add = "add";

        if($id != ''){

            $this->menu_model->update_menu_schedules($items,$id);

            $add = "upd";

            $this->main_model->update_tbl('menu_schedules','menu_sched_id',$items,$id);

        }else{

            $id = $this->menu_model->add_menu_schedules($items);

            $items['menu_sched_id'] = $id;

            $this->main_model->add_trans_tbl('menu_schedules',$items);

        }



        echo json_encode(array("id"=>$id,"act"=>$add,"desc"=>$this->input->post('desc')));

    }

    public function menu_sched_details_db(){

        $this->load->model('dine/menu_model');

        $this->load->model('dine/main_model');

        $items = array();

        $items = array("day"=>$this->input->post('day'),

                        "time_on"=>date('H:i:s',strtotime($this->input->post('time_on'))),

                        "time_off"=>date('H:i:s',strtotime($this->input->post('time_off'))),

                        "menu_sched_id"=>$this->input->post('sched_id')

                        );

        // $id = $this->input->post('sched_id');

        $day = $this->input->post('day');



        $count = $this->menu_model->validate_menu_schedule_details($this->input->post('sched_id'),$day);

        if($count == 0){

            // if($id != '')    $this->menu_model->update_menu_schedule_details($items,$id);

            // else             $this->menu_model->add_menu_schedule_details($items);

            $id = $this->menu_model->add_menu_schedule_details($items);

            $items['id'] = $id;

            $this->main_model->add_trans_tbl('menu_schedule_details',$items);

            // echo json_encode(array("msg"=>'success'));

            echo json_encode(array("msg"=>'Successfully Added',"id"=>$this->input->post('sched_id')));

        }else{

            echo json_encode(array("msg"=>'error',"id"=>$this->input->post('sched_id')));

            // echo json_encode(array("msg"=>$count));

            // echo json_encode(array("msg"=>$this->db->last_query()));

        }

    }

    public function remove_schedule_promo_details(){

        $this->load->model('dine/main_model');

        $id = $this->input->post('pr_sched_id');

        $this->menu_model->delete_menu_schedule_details($id);

        $this->main_model->delete_trans_tbl('menu_schedule_details',array('id'=>$id));

        echo json_encode(array("msg"=>'Successfully Deleted'));

    }

    public function print_excel(){

        $menus = $this->get_menus(null,false,true);

        $this->load->library('Excel');

        $sheet = $this->excel->getActiveSheet();

        $filename = "Menu List";

        $title = "Menu List";

        $styleHeaderCell = array(

            'borders' => array(

                'allborders' => array(

                    'style' => PHPExcel_Style_Border::BORDER_THIN

                )

            ),

            'fill' => array(

                'type' => PHPExcel_Style_Fill::FILL_SOLID,

                'color' => array('rgb' => '3C8DBC')

            ),

            'alignment' => array(

                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,

            ),

            'font' => array(

                'bold' => true,

                'size' => 14,

                'color' => array('rgb' => 'FFFFFF'),

            )

        );

        $styleNum = array(

            'alignment' => array(

                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,

            ),

        );

        $styleTxt = array(

            'alignment' => array(

                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,

            ),

        );

        $styleTitle = array(

            'alignment' => array(

                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,

            ),

            'font' => array(

                'bold' => true,

                'size' => 16,

            )

        );

        $headers = array('MENU CODE','SHORT NAME','FULL NAME','CATEGORY','SUBCATEGORY','SRP');

        $sheet->getColumnDimension('A')->setWidth(35);

        $sheet->getColumnDimension('B')->setWidth(35);

        $sheet->getColumnDimension('C')->setWidth(55);

        $sheet->getColumnDimension('D')->setWidth(30);

        $sheet->getColumnDimension('E')->setWidth(30);

        $sheet->getColumnDimension('F')->setWidth(10);

        $rc=1;

        // $sheet->mergeCells('A'.$rc.':F'.$rc);

        // $sheet->getCell('A'.$rc)->setValue($title);

        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);

        // $rc++;

        // $rc++;

        $col = 'A';

        foreach ($headers as $txt) {

            $sheet->getCell($col.$rc)->setValue($txt);

            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);

            $col++;

        }

        $rc++;

        foreach ($menus as $res) {

            $sheet->getCell('A'.$rc)->setValue($res->menu_code);

            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);

            $sheet->getCell('B'.$rc)->setValue($res->menu_name);

            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);

            $sheet->getCell('C'.$rc)->setValue($res->menu_short_desc);

            $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);

            $sheet->getCell('D'.$rc)->setValue($res->menu_cat_name);     

            $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);

            $sheet->getCell('E'.$rc)->setValue($res->menu_sub_cat_name);     

            $sheet->getStyle('E'.$rc)->applyFromArray($styleTxt);

            $sheet->getCell('F'.$rc)->setValue($res->cost);     

            $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);

            $rc++;

        }

        ob_end_clean();

        header('Content-type: application/vnd.ms-excel');

        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');

        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');

        $objWriter->save('php://output');

    }

    public function upload_excel_form(){

        $data['code'] = menuUploadForm();

        $this->load->view('load',$data);

    }

    public function upload_excel_db(){

        $this->load->model('dine/main_model');

        $temp = $this->upload_temp('menu_excel_temp');

        if($temp['error'] == ""){

            $now = $this->site_model->get_db_now('sql');

            $this->load->library('excel');

            $obj = PHPExcel_IOFactory::load($temp['file']);

            $sheet = $obj->getActiveSheet()->toArray(null,true,true,true);

            $count = count($sheet);

            $start = 4;

            $rows = array();

            for($i=$start;$i<=$count;$i++){

                if($sheet[$i]["B"] != ""){

                    $rows[] = array(

                        "menu_code"         => $sheet[$i]["A"],

                        "short_name"        => $sheet[$i]["B"],

                        "full_name"         => $sheet[$i]["C"],

                        "category"          => $sheet[$i]["D"],

                        "subcategory"       => $sheet[$i]["E"],

                        "price"             => $sheet[$i]["F"],

                    );

                }

            }

            if(count($rows) > 0){

                $dflt_schedule = 1;                

                #################################################################################################################################

                ### INACTIVE ALL

                    $this->site_model->update_tbl('menu_categories',array(),array('inactive'=>1));

                    $this->main_model->update_trans_tbl('menu_categories',array(),array('inactive'=>1));

                    $this->site_model->update_tbl('menu_subcategories',array(),array('inactive'=>1));

                    $this->main_model->update_trans_tbl('menu_subcategories',array(),array('inactive'=>1));

                    $this->site_model->update_tbl('menus',array(),array('inactive'=>1));

                    $this->main_model->update_trans_tbl('menus',array(),array('inactive'=>1));

                    $this->site_model->update_tbl('modifier_groups',array(),array('inactive'=>1));

                    $this->main_model->update_trans_tbl('modifier_groups',array(),array('inactive'=>1));

                    $this->site_model->update_tbl('modifiers',array(),array('inactive'=>1));

                    $this->main_model->update_trans_tbl('modifiers',array(),array('inactive'=>1));

                #################################################################################################################################

                ### INSERT CATEGORIES

                    $ins_categories = array();

                    foreach ($rows as $ctr => $row) {

                        if(!isset($ins_categories[$row['category']])){

                            $cat_name = $row['category'];

                            $ins_categories[$row['category']] = array(

                                'menu_cat_name' => strtoupper($cat_name),

                                'menu_sched_id' => $dflt_schedule,

                                'reg_date'      => $now,

                            );

                        }

                    }

                    $this->site_model->add_tbl_batch('menu_categories',$ins_categories);

                    $this->main_model->add_trans_tbl_batch('menu_categories',$ins_categories);

                #################################################################################################################################

                ### INSERT SUBCATEGORIES

                    $ins_subcategories = array();

                    foreach ($rows as $ctr => $row) {

                        if(!isset($ins_subcategories[$row['subcategory']])){

                            $subcat_name = $row['subcategory'];

                            $ins_subcategories[$row['subcategory']] = array(

                                'menu_sub_cat_name' => strtoupper($subcat_name),

                                'reg_date'          => $now,

                            );

                        }

                    }

                    $this->site_model->add_tbl_batch('menu_subcategories',$ins_subcategories);

                    $this->main_model->add_trans_tbl_batch('menu_subcategories',$ins_subcategories);

                #################################################################################################################################                ### GET ALL CATEGORIES AND SUBCATEGORIES

                    $result = $this->site_model->get_tbl('menu_categories',array('inactive'=>0));

                    $categories = array();

                    foreach ($result as $res) {

                        $categories[strtolower($res->menu_cat_name)] = $res;

                    }

                    $result = $this->site_model->get_tbl('menu_subcategories',array('inactive'=>0));

                    $subcategories = array();

                    foreach ($result as $res) {

                        $subcategories[strtolower($res->menu_sub_cat_name)] = $res;

                    }

                #################################################################################################################################

                ### INSERT MENUS

                    $menus = array();    

                    foreach ($rows as $ctr => $row) {

                        if(!isset($menus[$row['short_name']])){

                            $cat_id = 0;

                            if(isset($categories[strtolower($row['category'])])){

                                $cat = $categories[strtolower($row['category'])];

                                $cat_id = $cat->menu_cat_id;

                            }

                            $subcat_id = 0; 

                            if(isset($subcategories[strtolower($row['subcategory'])])){

                                $subcat = $subcategories[strtolower($row['subcategory'])];

                                $subcat_id = $subcat->menu_sub_cat_id;

                            }

                            $menus[$row['short_name']] = array(

                                'menu_code' => $row['menu_code'],

                                'menu_barcode' => $row['menu_code'],

                                'menu_name' => $row['short_name'],

                                'menu_short_desc' => $row['full_name'],

                                'menu_cat_id' => $cat_id,

                                'menu_sub_cat_id' => $subcat_id,

                                'menu_sched_id' => $dflt_schedule,

                                'cost' => $row['price'],

                                'reg_date' => $now,

                            );                            

                        }

                    }

                    $this->site_model->add_tbl_batch('menus',$menus);

                    $this->main_model->add_trans_tbl_batch('menus',$menus);

                #################################################################################################################################

            }

            unlink($temp['file']);

        }

        else{

            site_alert($temp['error'],"error");

        }

        redirect(base_url()."menu", 'refresh'); 

    }

    public function upload_excel_string(){

        $this->load->model('dine/main_model');

        $temp = $this->upload_temp('menu_excel_temp');

        if($temp['error'] == ""){

            $now = $this->site_model->get_db_now('sql');

            $this->load->library('excel');

            $obj = PHPExcel_IOFactory::load($temp['file']);

            $sheet = $obj->getActiveSheet()->toArray(null,true,true,true);

            $count = count($sheet);

            $start = 4;

            $rows = array();

            for($i=$start;$i<=$count;$i++){

                if($sheet[$i]["B"] != ""){

                    $rows[] = array(

                        "menu_code"         => $sheet[$i]["A"],

                        "short_name"        => $sheet[$i]["B"],

                        "full_name"         => $sheet[$i]["C"],

                        "category"          => $sheet[$i]["D"],

                        "subcategory"       => $sheet[$i]["E"],

                        "price"             => $sheet[$i]["F"],

                    );

                }

            }

            if(count($rows) > 0){

                $dflt_schedule = 1;                

                $query = "";

                #################################################################################################################################

                ### INACTIVE ALL

                    $query .= $this->db->update_string('menu_categories',array('inactive'=>1),array()).";\r\n";

                    $query .= $this->db->update_string('menu_subcategories',array('inactive'=>1),array()).";\r\n";

                    $query .= $this->db->update_string('menus',array('inactive'=>1),array()).";\r\n";

                    $query .= $this->db->update_string('modifier_groups',array('inactive'=>1),array()).";\r\n";

                    $query .= $this->db->update_string('modifiers',array('inactive'=>1),array()).";\r\n";

                    // echo "<pre>".$query."</pre>";

                    // return false;

                #################################################################################################################################

                ### INSERT CATEGORIES

                    $ct = array(

                        '101'=>'DIMSUM',

                        '102'=>'APPETIZER',

                        '103'=>'CONGEE ',

                        '104'=>'NOODLE SOUP',

                        '105'=>'ROASTING',

                        '106'=>'FRESH VEGETABLE',

                        '107'=>'SOUP',

                        '108'=>'HOTPOT',

                        '109'=>'CHINESE CLASSIC',

                        '110'=>'RICE/NOODLE',

                        '111'=>'DESSERT',

                        '112'=>'ADDONS',

                        '113'=>'SET MEAL(6PAX)',

                        '114'=>'DRINKS',

                        '115'=>'PROMO',

                        '116'=>'OTHERS',

                        '117'=>'SET MEAL(12PAX)',

                        '118'=>'BREAKFAST MEAL',

                        '119'=>'SET MEAL(10PAX)',

                    );

                    foreach ($ct as $id => $c) {

                        $items = array(

                            'menu_cat_id' => $id,

                            'menu_cat_name' => strtoupper($c),

                            'menu_sched_id' => $dflt_schedule,

                            'reg_date'      => $now,

                        );

                        $query .= $this->db->insert_string('menu_categories',$items).";\r\n";

                    }



                #################################################################################################################################

                ### INSERT SUBCATEGORIES

                    $sb = array(

                        '11'=>'FOOD',

                        '12'=>'BEVERAGES',

                        '13'=>'NON FOOD',

                        '14'=>'PROMO',

                        '15'=>'OTH',

                    );

                    foreach ($sb as $id => $c) {

                        $items = array(

                            'menu_sub_cat_id' => $id,

                            'menu_sub_cat_name' => strtoupper($c),

                            'reg_date'      => $now,

                        );

                        $query .= $this->db->insert_string('menu_subcategories',$items).";\r\n";

                    }

                    

                #################################################################################################################################

                ### INSERT MENUS

                    $menus = array();    

                    foreach ($rows as $ctr => $row) {

                        if(!isset($menus[$row['short_name']])){

                            $cat_id = 0;

                            foreach ($ct as $id => $c) {

                                if(strtolower($row['category']) == strtolower($c)){

                                    $cat_id = $id;

                                    break;

                                }

                            }

                            $subcat_id = 0; 

                            foreach ($sb as $id => $c) {

                                if(strtolower($row['subcategory']) == strtolower($c)){

                                    $subcat_id = $id;

                                    break;

                                }

                            }

                            $menus[$row['short_name']] = array(

                                'menu_code' => $row['menu_code'],

                                'menu_barcode' => $row['menu_code'],

                                'menu_name' => $row['short_name'],

                                'menu_short_desc' => $row['full_name'],

                                'menu_cat_id' => $cat_id,

                                'menu_sub_cat_id' => $subcat_id,

                                'menu_sched_id' => $dflt_schedule,

                                'cost' => $row['price'],

                                'reg_date' => $now,

                            );                            

                        }

                    }

                    foreach ($menus as $code => $row) {

                        $query .= $this->db->insert_string('menus',$row).";\r\n";

                    }

                    echo "<pre>".$query."</pre>";

                    return false;

                    // $this->site_model->add_tbl_batch('menus',$menus);

                    // $this->main_model->add_trans_tbl_batch('menus',$menus);

                #################################################################################################################################

            }

            unlink($temp['file']);

        }

        else{

            site_alert($temp['error'],"error");

        }

        redirect(base_url()."menu", 'refresh'); 

    }

    public function upload_temp($temp_file_name,$upload_file='menu_excel'){

        $error = "";

        $file  = "";

        $path  = './uploads/temp/';

        $config['upload_path']          = $path;

        // $config['allowed_types']        = 'xls|xlsx';

        $config['allowed_types']        = '*';

        $config['file_name']            = $temp_file_name;

        $config['overwrite']            = true;

        $this->load->library('upload', $config);

        $allowed_files = array('.xls','.xlsx');

        if (!$this->upload->do_upload($upload_file)){

            $error = $this->upload->display_errors();

        }

        else{

            $fileData = $this->upload->data('file_name');

            if(in_array($fileData['file_ext'],$allowed_files)){

                $file = $fileData['file_name'];

            }

            else{

                $error = 'File is not allowed';

                unlink($path.$fileData['file_name']);

            }

        }           

        return array('file'=>$path."".$file,'error'=>$error);    

    }

    public function menu_test(){

        $this->load->library('excel');

        $obj = PHPExcel_IOFactory::load('menu_codes_bluesmith.xlsx');

        $sheet = $obj->getActiveSheet()->toArray(null,true,true,true);

        $count = count($sheet);

        $start = 1;

        $menus = array();

        for($i=$start;$i<=$count;$i++){

            $menus[] = $sheet[$i]["A"];

        }

        $result = $this->site_model->get_tbl('menus',array('inactive'=>0));

        $db_menus = array();

        foreach ($result as $res) {

            $db_menus[] = strtolower($res->menu_code);

            

        }

        foreach ($menus as $val) {

            if(!in_array(strtolower($val),$db_menus)){

                echo $val."<br>";

            }

        }

    }

    public function menu_up(){

        $this->load->library('excel');

        $obj = PHPExcel_IOFactory::load('barcino_beverage.xlsx');

        $sheet = $obj->getActiveSheet()->toArray(null,true,true,true);

        $count = count($sheet);

        $start = 2;

        $rows = array();

        $query = "";

        $now = $this->site_model->get_db_now('sql');

        for($i=$start;$i<=$count;$i++){

            $rows[] = array(

                "menu_code"         => $sheet[$i]["A"],

                "menu_barcode"      => $sheet[$i]["B"],

                "menu_name"         => $sheet[$i]["C"],

                "menu_short_desc"   => $sheet[$i]["D"],

                "category"          => strtoupper($sheet[$i]["E"]),

                "subcategory"       => strtoupper($sheet[$i]["F"]),

                "cost"              => $sheet[$i]["G"],

            );

        }

        $categories = $this->site_model->get_tbl('menu_categories');

        $db_cats = array();

        foreach ($categories as $res) {

            $db_cats[$res->menu_cat_name] = $res->menu_cat_id;

        }

        $subcategories = $this->site_model->get_tbl('menu_subcategories');

        $db_subs = array();

        foreach ($subcategories as $res) {

            $db_subs[strtoupper($res->menu_sub_cat_name)] = $res->menu_sub_cat_id;

        }

        $menus = array();

        foreach ($rows as $ctr => $row) {

            if(!isset($menus[$row['menu_code']])){

                // $cat_id = 0;

                // foreach ($ct as $id => $c) {

                //     if(strtolower($row['category']) == strtolower($c)){

                //         $cat_id = $id;

                //         break;

                //     }

                // }

                // $subcat_id = 0; 

                // foreach ($sb as $id => $c) {

                //     if(strtolower($row['subcategory']) == strtolower($c)){

                //         $subcat_id = $id;

                //         break;

                //     }

                // }

                $cat_id = null;

                if(isset($db_cats[$row['category']]))

                    $cat_id = $db_cats[$row['category']];

                else{

                    echo "CAT - ".var_dump($row['category']);

                }

                // $subcat_id = $db_subs[$row['subcategory']];

                $subcat_id = null;

                if(isset($db_subs[$row['subcategory']]))

                    $subcat_id = $db_subs[$row['subcategory']];

                else{

                    echo "subCAT - ".var_dump($row['subcategory']);

                }



                $menus[$row['menu_code']] = array(

                    'menu_code' => $row['menu_code'],

                    'menu_barcode' => '',

                    'menu_name' => $row['menu_code'],

                    'menu_short_desc' => $row['menu_short_desc'],

                    'menu_cat_id' => $cat_id,

                    'menu_sub_cat_id' => $subcat_id,

                    'menu_sched_id' => 1,

                    'cost' => $row['cost'],

                    'reg_date' => $now,

                );            

            }

        }

        foreach ($menus as $code => $row) {

            $query .= $this->db->insert_string('menus',$row).";\r\n";

        }

        echo "<pre>".$query."</pre>";                

        

    }

    public function find_not_in($rows,$exl_col,$tbl,$tbl_col){

        $sub = array();

        foreach ($rows as $row) {

            if(!isset($sub[$row[$exl_col]]) && $row[$exl_col] != ""){

                $sub[$row[$exl_col]] = $row[$exl_col];

            }

        }

        $sub_categories = $this->site_model->get_tbl($tbl);

        $db_sub = array();

        foreach ($sub_categories as $res) {

            $db_sub[$res->$tbl_col] = $res->$tbl_col;

        }

        $not_in = array();

        foreach ($sub as $cat) {

            if(!in_array($cat, $db_sub)){

                $not_in[] = $cat;

            }

        }

        return $not_in;

    }



    //subcategories for pinlkberry

    //JED 8/8/2018

    public function subcategories_new(){

        $this->load->model('dine/menu_model');

        $this->load->helper('dine/menu_helper');

        $this->load->helper('site/site_forms_helper');

        $data = $this->syter->spawn('menu');

        $data['page_title'] = fa(' icon-book-open')."Menu Subcategories";

        $th = array('ID','Name','Under Category','Branch','Inactive','');

        $data['code'] = create_rtable('menu_subcategory','menu_sub_id','subcategory-tbl',$th);

        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');

        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js' ,'js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');

        $data['load_js'] = 'dine/menu.php';

        $data['use_js'] = 'subcategoryListNewJs';

        $data['page_no_padding'] = true;

        $this->load->view('page',$data);

    }

    public function get_subcategories_new($id=null,$asJson=true){

        $this->load->helper('site/pagination_helper');

        $pagi = null;

        $args = array();

        $total_rows = 1000;

        if($this->input->post('pagi'))

            $pagi = $this->input->post('pagi');

        $post = array();

        

        if(count($this->input->post()) > 0){

            $post = $this->input->post();

        }

        if($this->input->post('menu_sub_name')){

            $lk  =$this->input->post('menu_sub_name');

            $args["(menu_subcategory.menu_sub_name like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);

        }

        if($this->input->post('inactive')){

            $args['menu_subcategory.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));

        }

        // $join = null;

        $join["menu_categories"] = array('content'=>"menu_subcategory.category_id = menu_categories.menu_cat_id");

        $count = $this->site_model->get_tbl('menu_subcategory',$args,array(),$join,true,'menu_subcategory.*',null,null,true);

        $page = paginate('menu/get_subcategories_new',$count,$total_rows,$pagi);

        $items = $this->site_model->get_tbl('menu_subcategory',$args,array(),$join,true,'menu_subcategory.*,menu_categories.menu_cat_name',null,$page['limit']);

        // echo $this->site_model->db->last_query();

        // echo "<pre>",print_r($items),"</pre>";die();

        $json = array();

        if(count($items) > 0){

            $ids = array();

            foreach ($items as $res) {

                $link = $this->make->A(fa('fa-edit fa-lg').' Edit','#',array('class'=>'btn blue btn-sm btn-outline edit','id'=>'edit-'.$res->menu_sub_id.'-'.$res->branch_code,'ref'=>$res->menu_sub_id,'branch'=>$res->branch_code,'return'=>'true'));

                $json[$res->menu_sub_id.'/'.$res->branch_code] = array(

                    "id"=>$res->menu_sub_id,   

                    "title"=>ucwords(strtolower($res->menu_sub_name)),   

                    "cat"=>ucwords(strtolower($res->menu_cat_name)),

                    "branch_code"=>$res->branch_code,   

                    "inact"=>($res->inactive == 0 ? 'No' : 'Yes'),

                    "link"=>$link

                );

                $ids[] = $res->menu_sub_id;

            }

        }

        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));

    }

    public function subcategories_form_new($ref=null,$branch=null){

        $this->load->helper('dine/menu_helper');

        $this->load->model('dine/menu_model');

        $cat = array();

        if($ref != null){

            $cats = $this->menu_model->get_menu_subcategory($ref,null,$branch);

            $cat = $cats[0];

        }

        // echo "<pre>",print_r($branch),"</pre>";die();

        $this->data['code'] = makeMenuSubCategoriesNewForm($cat,$branch);

        $this->load->view('load',$this->data);

    }

    public function subcategories_form_new_db(){

        $this->load->model('dine/menu_model');

        $this->load->model('dine/main_model');

        $items = array();

        $branch_code = $this->input->post('b_code');

        $branch_edit = $this->input->post('f_branch_id');

        // var_dump($this->input->post('inactive')); die();

        $branches = $this->menu_model->get_all_branch();

        $date_now = date("Y-m-d h:i:s");    

        foreach ($branches as $value) {

            $sub_id = $this->menu_model->get_last_subcategory();

            $dateNow = $this->menu_model->get_date_now();

            $items[] = array(

                "menu_sub_id"=>$sub_id+1,

                "reg_date"=>$dateNow,

                "menu_sub_name"=>$this->input->post('menu_sub_name'),

                "inactive"=>1,

                "category_id"=>(int)$this->input->post('category_id'),

                "branch_code"=>$value->branch_code

            );

        }

        if($this->input->post('menu_sub_id')){

            unset($items[0]['reg_date'],$items[0]['branch_code'],$items[0]['menu_sub_id']);

            $inactive = array(

                "inactive"=>$this->input->post('inactive'),

            );

            if(!empty($branch_code)){

                foreach ($branch_code as $value) {

                    $this->menu_model->update_menu_subcategory($items[0],$this->input->post('menu_sub_id'),$value);

                    $this->menu_model->update_menu_subcategory($inactive,$this->input->post('menu_sub_id'),$value);

                }

                $id = $this->input->post('menu_sub_id');

                $act = 'update';

                $msg = 'Updated Menu Sub Category . '.$this->input->post('menu_sub_name');

            }

            else{

                $act = 'error';

                $msg = 'Error! Please select branch. ';

                site_alert($msg,'error');

                // $this->menu_model->update_menu_subcategory($items[0],$this->input->post('menu_sub_id'),$branch_edit);

                // $this->menu_model->update_menu_subcategory($inactive,$this->input->post('menu_sub_id'),$branch_edit);

                // foreach ($branches as $value) {

                // }

            // echo "<pre>",print_r($branch_edit),"</pre>";die();

            }

            // $this->main_model->update_tbl('menu_subcategory','menu_sub_id',$items,$id);

        }else{

            $id = $this->menu_model->add_bulk_menu_subcategory($items);

            $inactive = array(

                "inactive"=>0,

            );

            if(!empty($branch_code)){

                foreach ($branch_code as $value) {

                   $check = $this->menu_model->update_menu_subcategory($inactive,$items[0]['menu_sub_id'],$value);

                }

            }

            // else{

            //     foreach ($branches as $value) {

            //        $check = $this->menu_model->update_menu_subcategory($inactive,$items[0]['menu_sub_id'],$value->branch_code);

            //     }

            // }

            $act = 'add';

            $msg = 'Added  new Menu Sub Category '.$this->input->post('menu_sub_name');

            // $this->main_model->add_trans_tbl('menu_subcategory',$items);

        }

        echo json_encode(array("id"=>$id,"addOpt"=>$items[0]['menu_sub_name'],"desc"=>$this->input->post('menu_sub_name'),"act"=>$act,'msg'=>$msg));

    }

    // price listing in menus 

    // 09/13/2018

    // Nicko Q. 

    public function price_listing($menu_id=null,$branch_code=null){

        $this->load->helper('dine/menu_helper');

        $this->load->model('dine/menu_model');

        $menus=array();

        if($menu_id != null){

            $menus = $this->menu_model->get_menus($menu_id,null,false,null);

        }

        // echo "<pre>",print_r($menus),"</pre>";die();

        $data['code'] = makePriceListing($menus);

        $this->load->view('load',$data);

    }

    // price History in menus 

    // 09/13/2018

    // Nicko Q. 

    public function price_history($menu_id=null,$branch_code=null){

        $this->load->helper('dine/menu_helper');

        $this->load->model('dine/menu_model');

        $history = array();

        // echo "<pre>",print_r($menu_id),"</pre>";die();

        if($menu_id != null){

            $history = $this->menu_model->get_price_history($menu_id);

        }

        $data['code'] = makeHistorypricing($history);

        $this->load->view('load',$data);

    }

    public function recipe_listing($menu_id=null){

        $this->load->library('Excel');

        $this->load->model('dine/menu_model');

        $sheet = $this->excel->getActiveSheet();

        // $branch_id = $_GET['branch_id'];

        $branch = array();

        $rc=1;

        $title_name = "Recipe List";



        $sheet->mergeCells('A'.$rc.':P'.$rc);

        $sheet->getCell('A'.$rc)->setValue($title_name);

        $rc++;

        $branches = $this->menu_model->get_all_branch();

        // echo "<pre>",print_r($branches),"</pre>";die();

        if(!empty($branches)){

            foreach($branches as $val){

                $total = 0;

                $dateNow = $this->menu_model->get_date_now();

                $sheet->mergeCells('A'.$rc.':P'.$rc);

                $sheet->getCell('A'.$rc)->setValue("Branch : ".$val->branch_name);

                $rc++;

                $sheet->mergeCells('A'.$rc.':P'.$rc);

                $sheet->getCell('A'.$rc)->setValue("Branch Code : ".$val->branch_code);

                $rc++;

                $sheet->mergeCells('A'.$rc.':P'.$rc);

                $sheet->getCell('A'.$rc)->setValue("Address : ".$val->address);

                $rc++;

                $sheet->mergeCells('A'.$rc.':P'.$rc);

                $sheet->getCell('A'.$rc)->setValue("Date : ".$dateNow);

                $rc++;



                $rc++;

                $sheet->getStyle("A".$rc.":E".$rc)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A'.$rc.':'.'E'.$rc)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $sheet->getStyle('A'.$rc.':'.'E'.$rc)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);

                $sheet->getStyle('A'.$rc.':'.'E'.$rc)->getFill()->getStartColor()->setRGB('29bb04');

                $sheet->getStyle('A1:'.'E'.$rc)->getFont()->setBold(true);



                $sheet->getCell('A'.$rc)->setValue('ITEM');

                $sheet->getCell('B'.$rc)->setValue('UOM');

                $sheet->getCell('C'.$rc)->setValue('UNIT PRICE');

                $sheet->getCell('D'.$rc)->setValue('QUANTITY');

                $sheet->getCell('E'.$rc)->setValue('LINE TOTAL');

                

                $rc++;

                #DETAILS

                // echo "<pre>",print_r($det_branch),"</pre>";die();

                $det_branch = $this->menu_model->get_recipe_items_branch($menu_id,null,null,$val->branch_code);

                foreach ($det_branch as $res) {

                    // $sheet->getStyle("A".$rc.":D".$rc)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                    // $sheet->getStyle('A'.$rc.':'.'D'.$rc)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                    $sheet->getCell('A'.$rc)->setValue($res->item_name);

                    $sheet->getCell('B'.$rc)->setValue($res->uom);

                    $sheet->getCell('C'.$rc)->setValue($res->item_cost);

                    $sheet->getCell('D'.$rc)->setValue($res->qty);

                    $sheet->getCell('E'.$rc)->setValue(num($res->item_cost * $res->qty));

                    $total += $res->item_cost * $res->qty;

                    $rc++;

                }   

                $sheet->mergeCells('A'.$rc.':P'.$rc);

                $sheet->getCell('A'.$rc)->setValue("Total : ".$total);

                $rc++;

                $rc++;

            }

        }

        



        ob_end_clean();

        header('Content-type: application/vnd.ms-excel');

        header('Content-Disposition: attachment;filename="'.$title_name.'.xls"');

        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');

        $objWriter->save('php://output');

        }

}