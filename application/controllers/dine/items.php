<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Items extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('dine/items_model');
        $this->load->model('site/site_model');
        $this->load->helper('dine/items_helper');
    }
    public function index(){
        $this->load->helper('site/site_forms_helper');
        $data = $this->syter->spawn('items');
        $data['page_title'] = fa('icon-social-dropbox')." Items List";
        $th = array('ID','Name','Category','SRP','Register Date','Inactive','');
        $data['code'] = create_rtable('items','item_id','items-tbl',$th,'items/search_items_form',false,'list','items/export_form');
        $data['load_js'] = 'dine/items.php';
        $data['use_js'] = 'listFormJs';
        $data['page_no_padding'] = true;
        // $data['sideBarHide'] = true;
        $this->load->view('page',$data);
    }
    public function get_items($id=null,$asJson=true){
        $this->load->helper('site/pagination_helper');
        $pagi = null;
        $args = array();
        $total_rows = 100;
        if($this->input->post('pagi'))
            $pagi = $this->input->post('pagi');
        $post = array();
        
        if(count($this->input->post()) > 0){
            $post = $this->input->post();
        }
        if($this->input->post('name')){
            $lk  =$this->input->post('name');
            $args["(items.name like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        }
        if($this->input->post('inactive')){
            $args['items.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));
        }
        if($this->input->post('cat_id')){
            $args['items.cat_id'] = array('use'=>'where','val'=>$this->input->post('cat_id'));
        }
        if($this->input->post('branch_id')){
            // echo $this->input->post('branch_id');die();
            $args['items.branch_code'] = array('use'=>'where','val'=>$this->input->post('branch_id'));
        }
        $join["categories"] = array('content'=>"items.cat_id = categories.cat_id");
        $count = $this->site_model->get_tbl('items',$args,array(),$join,true,'items.*,categories.name as cat_name',null,null,true);
        $page = paginate('items/get_items',$count,$total_rows,$pagi);
        $groupm = 'items.item_id';
        $items = $this->site_model->get_tbl('items',$args,array(),$join,true,'items.*,categories.name as cat_name',$groupm,$page['limit']);
        $json = array();
        if(count($items) > 0){
            $ids = array();
            foreach ($items as $res) {
                $link = $this->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'items/setup/'.$res->item_id.'/'.$res->branch_code,array('class'=>'btn btn-sm blue btn-outline','return'=>'true'));
                // $link = $this->make->A(fa('fa-home'),base_url().'items/setup/'.$res->item_id,array('class'=>'btn btn-block btn-primary','return'=>'true'));
                $json[$res->item_id.'-'.$res->branch_code] = array(
                    "id"=>$res->item_id,   
                    "title"=>"[".$res->code."] ".ucwords(strtolower($res->name)),   
                    "desc"=>ucwords(strtolower($res->cat_name)),   
                    // "subtitle"=>ucwords(strtolower($res->menu_cat_name)),   
                    "caption"=>"PHP ".num($res->cost),
                    "date_reg"=>(empty($res->reg_date)) ? '' : sql2Date($res->reg_date),
                    "status"=>($res->inactive == 0 ? 'No' : 'Yes'),
                    // "branch"=>$res->branch_code,   
                    "link"=>$link
                );
                $ids[] = $res->item_id;
            }
        }
        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    }
    public function search_items_form(){
        $data['code'] = itemsSearchForm();
        $this->load->view('load',$data);
    }
    public function get_subcategories($cat_id = null){
        $results = $this->site_model->get_custom_val('subcategories',
            array('sub_cat_id,name,code'),
            (is_null($cat_id) ? null : 'cat_id'),
            (is_null($cat_id) ? null : $cat_id),
            true);
        $echo_array = array();
        foreach ($results as $val) {
            $echo_array[$val->sub_cat_id] = "[ ".$val->code." ] ".$val->name;
        }
        echo json_encode($echo_array);
    }
    public function setup($item_id = null,$bcode = null){
        $data = $this->syter->spawn();
        $img = "";
        if (is_null($item_id))
            $data['page_title'] = fa('icon-social-dropbox')." Add New Item";
        else {
            $item = $this->items_model->get_item($item_id,$bcode);
            $item = $item[0];
            if (!empty($item->code)) {
                $data['page_title'] = fa('icon-social-dropbox')." ".iSetObj($item,'name');
                if (!empty($item->update_date))
                    $data['page_subtitle'] = "Last updated ".$item->update_date;

            } else {
                header('Location:'.base_url().'items/setup');
            }
            $images = $this->site_model->get_image(null,null,'items',array('images.img_ref_id'=>$item_id)); 
            foreach ($images as $res) {
                $img = $res->img_path;
            }
        }

        $data['code'] = items_form_container($item_id,$img,$bcode);
        $data['load_js'] = "dine/items.php";
        $data['use_js'] = "itemFormContainerJs";
        $this->load->view('page',$data);
    }
    public function setup_load($item_id = null,$branch_code=null){
        $details = array();
        if (!is_null($item_id))
            $item = $this->items_model->get_item($item_id,$branch_code);
        if (!empty($item))
            $details = $item[0];
        // $b = $this->items_model->get_branch_detail();
        // echo "<pre>",print_r($b),"</pre>";die();
        $data['code'] = items_details_form($details,$item_id,$branch_code);
        $data['load_js'] = "dine/items.php";
        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js' ,'js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');
        $data['use_js'] = "itemDetailsJs";
        $this->load->view('load',$data);
    }
    public function item_details_db(){
        $this->load->model('dine/main_model');
        $error = "";
        $msg = "";
        $item_id = $this->input->post('item_id');

        $date = $this->input->post('date_effective');
        $date_effective = $date != '' ? date2Sql($date) : '';

        if(!empty($item_id)){
            $branch_selected_ac = $this->input->post('bcode');
            // echo $branch_selected_ac."haha";die();
            if(!empty($branch_selected_ac)){
                foreach ($branch_selected_ac as $branch) {
                $items = array(
                    'barcode' => $this->input->post('barcode'),
                    'code' => $this->input->post('code'),
                    'name' => $this->input->post('name'),
                    'desc' => $this->input->post('desc'),
                    'cat_id' => $this->input->post('cat_id'),
                    'subcat_id' => $this->input->post('subcat_id'),
                    'supplier_id' => $this->input->post('supplier_id'),
                    'uom' => $this->input->post('uom'),
                    'cost' => $this->input->post('cost'),
                    'type' => $this->input->post('type'),
                    'no_per_pack' => $this->input->post('no_per_pack'),
                    'no_per_pack_uom' => $this->input->post('no_per_pack_uom'),
                    'no_per_case' => $this->input->post('no_per_case'),
                    'reorder_qty' => $this->input->post('reorder_qty'),
                    'max_qty' => $this->input->post('max_qty'),
                    'inactive' => (int)$this->input->post('inactive'),
                    'date_effective' => $date_effective
                    );

                $id = $item_id;
                // echo "<pre>",print_r($items),"</pre>";die();
                $this->items_model->update_item($items,$id,$branch);

                $items['item_id'] = $id;
                $items['branch_code']=$branch;
                $this->set_item_history($items);

                $msg = "Updated item: ".$items['name'];
                    
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
                $item_id = $this->items_model->get_last_item_id();
                $item_id += 1;

                foreach ($branches_code as $key => $b_code) {
                        $items = array(
                        'barcode' => $this->input->post('barcode'),
                        "branch_code"=>$b_code->branch_code,
                        'code' => $this->input->post('code'),
                        'name' => $this->input->post('name'),
                        'desc' => $this->input->post('desc'),
                        'cat_id' => $this->input->post('cat_id'),
                        'subcat_id' => $this->input->post('subcat_id'),
                        'supplier_id' => $this->input->post('supplier_id'),
                        'uom' => $this->input->post('uom'),
                        'cost' => $this->input->post('cost'),
                        'type' => $this->input->post('type'),
                        'no_per_pack' => $this->input->post('no_per_pack'),
                        'no_per_pack_uom' => $this->input->post('no_per_pack_uom'),
                        'no_per_case' => $this->input->post('no_per_case'),
                        'reorder_qty' => $this->input->post('reorder_qty'),
                        'max_qty' => $this->input->post('max_qty'),
                        'inactive' => 1,
                        'date_effective' => $date_effective
                        );

                   //     $item_id = $this->items_model->get_last_item_id($b_code->branch_code);
                        $items['item_id'] =  $item_id;
                        $id = $this->items_model->add_item($items);

                      //  $this->set_item_history($items);
                        // $msg = "Added new item: ".$items['name'];
                }
                $branch_selected = $this->input->post('b_code');
                if(!empty($branch_selected)){
                    foreach ($branch_selected as $branch) {
                    $items = array(
                        'barcode' => $this->input->post('barcode'),
                        'code' => $this->input->post('code'),
                        'name' => $this->input->post('name'),
                        'desc' => $this->input->post('desc'),
                        'cat_id' => $this->input->post('cat_id'),
                        'subcat_id' => $this->input->post('subcat_id'),
                        'supplier_id' => $this->input->post('supplier_id'),
                        'uom' => $this->input->post('uom'),
                        'cost' => $this->input->post('cost'),
                        'type' => $this->input->post('type'),
                        'no_per_pack' => $this->input->post('no_per_pack'),
                        'no_per_pack_uom' => $this->input->post('no_per_pack_uom'),
                        'no_per_case' => $this->input->post('no_per_case'),
                        'reorder_qty' => $this->input->post('reorder_qty'),
                        'max_qty' => $this->input->post('max_qty'),
                        'inactive' => (int)$this->input->post('inactive'),
                        'date_effective' => $date_effective
                        );

                    $id = $this->items_model->get_last_item_id($branch);
                    // echo "<pre>",print_r($id),"</pre>";die();
                    $this->items_model->update_item($items,$id,$branch);

                    $items['item_id'] = $id;
                    $items['branch_code']=$branch;
                    $this->set_item_history($items);

                    $msg = "Added new item: ".$items['name'];                    
                    }
                }
            }
        }            

        if($error == "")
        site_alert($msg,'success');
        echo json_encode(array('msg'=>$msg,'error'=>$error));
    }

    public function set_item_history($items){ 
        $user = $this->session->userdata('user');
        $user_id = $user['id'];

        $item = array(
                    'item_id'=>$items['item_id'],
                    'branch_code'=>$items['branch_code'],
                    'cost'=>$items['cost'],
                    'user_id'=>$user_id,
                    'date_time'=>$items['date_effective']
                );

        $this->items_model->add_item_history($item);
    }

    public function image_db(){
        $image = null;
        $ext = null;
        $msg = "";
        if(is_uploaded_file($_FILES['fileUpload']['tmp_name'])){
            $info = pathinfo($_FILES['fileUpload']['name']);
            if(isset($info['extension']))
            $ext = $info['extension'];
            $menu = $this->input->post('upid');
            $newname = $menu.".".$ext;
            if (!file_exists("uploads/items/")) {
                mkdir("uploads/items/", 0777, true);
            }
            $target = 'uploads/items/'.$newname;
            if(!move_uploaded_file( $_FILES['fileUpload']['tmp_name'], $target)){
                $msg = "Image Upload failed";
            }
            else{
                $new_image = $target;
                $result = $this->site_model->get_image(null,$this->input->post('upid'),'items');
                $items = array(
                    "img_file_name" => $newname,
                    "img_path" => $new_image,
                    "img_ref_id" => $this->input->post('upid'),
                    "img_tbl" => 'items',
                );
                if(count($result) > 0){
                    $this->site_model->update_tbl('images','img_id',$items,$result[0]->img_id);
                }
                else{
                    $id = $this->site_model->add_tbl('images',$items,array('datetime'=>'NOW()'));
                }
            }
        }
        echo json_encode(array('msg'=>$msg));
    }
    ########################################################################
    ### INQURIES
        public function inventory(){
            $data = $this->syter->spawn('items');
            $query = $this->items_model->get_curr_item_inv_and_locs();
            $menu_query = $this->items_model->get_curr_item_menu_and_locs();
            $records = array();
            $menu_records = array();
            if($query)
                $records = $query->result_array();

            if($menu_query)
                $menu_records = $query->result_array();

            // $loc_fields = array();
            // if (!empty($records)) {
            //     $xx = $records[0];
            //     foreach ($xx as $k => $v) {
            //         if (strpos($k, "!!Loc-") === false)
            //             continue;

            //         $loc_fields[$k] = str_replace("!!Loc-", "", $k);
            //     }
            // }

            $data['code'] = item_inventory_and_location_container($records, $menu_records);
            // $data['page_title'] = fa('fa-random')." Items Inventory";
            $data['page_title'] = fa('icon-social-dropbox').' Quantity On Hand';
            // $data['page_subtitle'] = "Current item count and location";
            $data['load_js'] = "dine/items.php";
            $data['use_js'] = "inventoryJS";
            $this->load->view('page',$data);
        }
        public function inventories($branch_id){
            $post = array();
                if(count($this->input->post()) > 0){
                    $post = $this->input->post();
            } 
            // echo $post['branch_id'];die();
            $bcode = null;
            if(isset($post['branch_id']) && $post['branch_id'] != ""){
                $bcode = $post['branch_id'];
            }
            $data = $this->syter->spawn('items');
            $query = $this->items_model->get_curr_item_inv_and_locs($branch_id);
            $records = array();
            if($query)
                $records = $query->result_array();

            $loc_fields = array();
            if (!empty($records)) {
                $xx = $records[0];
                foreach ($xx as $k => $v) {
                    if (strpos($k, "!!Loc-") === false)
                        continue;

                    $loc_fields[$k] = str_replace("!!Loc-", "", $k);
                }
            }

            $data['code'] = item_inventory_and_location_container($records, $loc_fields);
            // $data['page_title'] = fa('fa-random')." Items Inventory";
            $data['page_title'] = fa('icon-social-dropbox').' Quantity On Hand';
            // $data['page_subtitle'] = "Current item count and location";
            $data['load_js'] = "dine/items.php";
            $data['use_js'] = "inventoryJS";
            $this->load->view('page',$data);
        }
        public function menu_inventories($branch_id){
            $post = array();
                if(count($this->input->post()) > 0){
                    $post = $this->input->post();
            } 
            // echo $post['branch_id'];die();
            $bcode = null;
            if(isset($post['branch_id']) && $post['branch_id'] != ""){
                $bcode = $post['branch_id'];
            }
            $data = $this->syter->spawn('items');
            $query = $this->items_model->get_curr_item_menu_and_locs($branch_id);
            // $trans_sales_query = $this->items_model->get_curr_item_menu_and_trans_sales_menus($branch_id);
            $records = array();
            $ts_records = array();
            if($query)
                $records = $query->result_array();
            // if($trans_sales_query)
            //     $ts_records = $trans_sales_query->result_array();
            // $loc_fields = array();
            // if (!empty($records)) {
            //     $xx = $records[0];
            //     foreach ($xx as $k => $v) {
            //         if (strpos($k, "!!Loc-") === false)
            //             continue;

            //         $loc_fields[$k] = str_replace("!!Loc-", "", $k);
            //     }
            // }
            // echo "<pre>",print_r($ts_records),"</pre>";die();
            $data['code'] = menu_inventory_and_location_container($records, $ts_records);
            // $data['page_title'] = fa('fa-random')." Items Inventory";
            $data['page_title'] = fa('icon-social-dropbox').' Quantity On Hand';
            // $data['page_subtitle'] = "Current item count and location";
            $data['load_js'] = "dine/items.php";
            $data['use_js'] = "inventoryJS";
            $this->load->view('page',$data);
        }
        public function inv_move(){
            $data = $this->syter->spawn('items');
            $data['page_title'] = fa('icon-shuffle').' Component Inventory Movement';
            $data['code'] = invMovePage();           
            $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
            $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','js/jspdf.js');
            $data['load_js'] = "dine/items.php";
            $data['use_js'] = "invMoveJS";
            $this->load->view('page',$data);
        }
        public function get_inv_move($id=null,$asJson=true){
            $this->load->helper('site/pagination_helper');
            $pagi = null;
            // $total_rows = 100;
            if($this->input->post('pagi'))
                $pagi = $this->input->post('pagi');
            $post = array();
            if(count($this->input->post()) > 0){
                $post = $this->input->post();
            }
            $table  = "item_moves";
            $select = "item_moves.*,items.name,items.code,trans_types.name as particular,items.no_per_pack as it_per_pack,items.no_per_pack_uom as it_per_pack_uom,branch_details.branch_name";
            $join["items"] = array('content'=>"item_moves.item_id = items.item_id");
            $join["trans_types"] = array('content'=>"item_moves.type_id = trans_types.type_id");
            $join["branch_details"] = array('content'=>"branch_details.branch_code = item_moves.branch_code");
            $args = array();
            $args2 = array();
            $args3 = array();
            if(isset($post['item-search']) && $post['item-search'] != ""){
                $args['item_moves.item_id'] = $post['item-search'];
                $args2['item_moves.item_id'] = $post['item-search'];
                $args3['item_moves.item_id'] = $post['item-search'];
            }
            if(isset($post['branch_id']) && $post['branch_id'] != ""){
                $args['item_moves.branch_code'] = $post['branch_id'];
            }
            if(isset($post['calendar_range']) && $post['calendar_range'] != ""){
                $daterange = $post['calendar_range'];
                $dates = explode(" to ",$daterange);
                $from = date2SqlDateTime($dates[0].'00:00:01');
                $to = date2SqlDateTime($dates[1].'23:59:59');
                $args["item_moves.reg_date  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
                $args2["item_moves.reg_date  < '".$from."'"] = array('use'=>'where','val'=>null,'third'=>false);
                $args3["item_moves.reg_date  > '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            }
            $args['item_moves.inactive'] = 0;
            $args2['item_moves.inactive'] = 0;
            $args3['item_moves.inactive'] = 0;
            $order = array('item_moves.reg_date'=>'asc','items.name'=>'asc');
            $group = null;
            // $count = $this->site_model->get_tbl($table,$args,$order,$join,true,$select,$group,null,true);
            // $page = paginate('items/get_items',$count,$total_rows,$pagi);
            $items = $this->site_model->get_tbl($table,$args,$order,$join,true,$select,$group);
            // echo $this->site_model->db->last_query();die();
            $item_moves_sum = $this->items_model->get_item_moves_total(null,$args2);
            // echo $this->site_model->db->last_query();
            $item_moves_after = $this->items_model->get_item_moves_total(null,$args3);
            // return false;

            $before_m_item = 0;
            if($item_moves_sum){
                $before_m_item = $item_moves_sum[0]->in_item_qty;
            }

            $after_m_item = 0;
            if($item_moves_after){
                $after_m_item = $item_moves_after[0]->in_item_qty;
            }

            $json = array();
            $html = "";
            if(count($items) > 0){
                $ctr = 1;
                $last = count($items);
                $colspan = 5;
                $curr = $before_m_item;
                foreach ($items as $res) {
                    if($ctr == 1){
                        $this->make->sRow(array('class'=>'tdhd'));
                            $this->make->sTd(array('colspan'=>$colspan));
                                $this->make->span("Quantity on Hand Before ".sql2Date($from)." ".toTimeW($from));
                            $this->make->eTd();
                            $this->make->td("");
                            $this->make->td("");
                            $this->make->td("");
                            $this->make->td($before_m_item,array('class'=>'text-right','style'=>'border-right:5px solid #fff !important;'));
                            $this->make->td("");
                            $this->make->td("");
                            $this->make->td("");
                            $c = "";
                            if($res->it_per_pack > 0){
                                $c = $before_m_item/$res->it_per_pack;
                            }    
                            $this->make->td($c,array('class'=>'text-right'));
                        $this->make->eRow();
                    }
                    $this->make->sRow();
                        $this->make->td(ucwords(strtolower($res->particular)));
                        $this->make->td($res->trans_ref);
                        $this->make->td(sql2Date($res->reg_date)." ".toTimeW($res->reg_date));
                        $this->make->td(ucwords(strtolower($res->name)));
                        $this->make->td($res->branch_name);
                        $this->make->td($res->uom,array('class'=>'text-center'));
                        $in = "";
                        $out = "";
                        // if($res->qty >= 0)
                        //     $in = num($res->qty);
                        // else
                        //     $out = num($res->qty);

                        if($res->qty >= 0){
                            $in = $res->qty;
                            $curr += $res->qty;
                            $out = "";
                        }
                        else{
                            $out = $res->qty;
                            $curr += $res->qty;
                            $out = $out * -1;
                        }

                        $this->make->td($in,array('class'=>'text-right'));
                        $this->make->td($out,array('class'=>'text-right'));
                        $this->make->td($curr,array('class'=>'text-right','style'=>'border-right:1px solid #fff;'));

                        $this->make->td($res->it_per_pack_uom,array('class'=>'text-center'));
                        $in = "";
                        $out = "";
                        $curr2 = "";
                        if($res->it_per_pack > 0){
                            $curr2 = $curr/$res->it_per_pack;
                            if($res->qty >= 0){
                                $val = $res->qty/$res->it_per_pack;
                                $in = num($val);
                            }
                            else{
                                $val = $res->qty/$res->it_per_pack;
                                $out = num($val);
                            }
                        }
                        $this->make->td($in,array('class'=>'text-right'));
                        $this->make->td($out,array('class'=>'text-right'));
                        $this->make->td($curr2,array('class'=>'text-right'));
                    
                    $this->make->eRow();

                    // if($ctr == $last){
                    //     $this->make->sRow(array('class'=>'tdhd'));
                    //         $this->make->sTd(array('colspan'=>$colspan));
                    //             $this->make->span("Quantity on Hand Before ".sql2Date($res->reg_date)." ".toTimeW($res->reg_date));
                    //         $this->make->eTd();
                    //         $this->make->td("");
                    //         $this->make->td("");
                    //         $this->make->td(num($res->curr_item_qty)." ".$res->uom,array('class'=>'text-right','style'=>'border-right:5px solid #fff !important;'));
                    //         $this->make->td("");
                    //         $this->make->td("");
                    //         $this->make->td("");
                    //         $c = "";
                    //         if($res->it_per_pack > 0){
                    //             $c = $res->curr_item_qty/$res->it_per_pack;
                    //         } 
                    //         $this->make->td(num($c)." ".$res->it_per_pack_uom,array('class'=>'text-right'));
                    //     $this->make->eRow();
                    // }
                    $ctr++;
                }

                $this->make->sRow(array('class'=>'tdhd'));
                    $this->make->sTd(array('colspan'=>$colspan));
                        $this->make->span("Quantity on Hand After ".sql2Date($res->reg_date)." ".toTimeW($res->reg_date));
                    $this->make->eTd();
                    $this->make->td("");
                    $this->make->td("");
                    $this->make->td("");
                    $this->make->td($curr + $after_m_item,array('class'=>'text-right','style'=>'border-right:5px solid #fff !important;'));
                    $this->make->td("");
                    $this->make->td("");
                    $this->make->td("");
                    $c = "";
                    if($res->it_per_pack > 0){
                        $c = ($curr + $after_m_item)/$res->it_per_pack;
                    } 
                    $this->make->td($c,array('class'=>'text-right'));
                $this->make->eRow();


                $json['html'] = $this->make->code();
            }
            echo json_encode($json);
        }
    public function inv_moves_pdf()
    {
    // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Component Inventory Movements Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_code = $this->input->get("branch_id");
        $setup = $this->setup_model->get_details($branch_code);
        $set = $setup[0];
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $set->branch_name, $set->address);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        // $this->menu_model->db = $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);                

        $pdf->Write(0, 'Component Inventory Movements Report', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);      
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Write(0, 'Report Period:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $daterange, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(200);
        $pdf->Write(0, 'Report Generated:    '.(new \DateTime())->format('Y-m-d H:i:s'), '', 0, 'L', true, 0, false, false, 0);
        $pdf->Write(0, 'Transaction Time:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(200);
        $user = $this->session->userdata('user');
        $pdf->Write(0, 'Generated by:    '.$user["full_name"], '', 0, 'L', true, 0, false, false, 0);        
        $pdf->ln(1);      
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln();              

        // echo "<pre>", print_r($trans), "</pre>";die();
        $pdf->SetFont('helvetica', 'B', 10);
        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(22, 0, 'Description', 'B', 0, 'L');        
        $pdf->Cell(25, 0, 'Reference #', 'B', 0, 'L');        
        $pdf->Cell(30, 0, 'Trans Date', 'B', 0, 'L');                
        $pdf->Cell(40, 0, 'Item Name', 'B', 0, 'L');        
        $pdf->Cell(30, 0, 'Branch', 'B', 0, 'L');        
        $pdf->Cell(15, 0, 'UOM', 'B', 0, 'L');        
        $pdf->Cell(15, 0, 'Qty In', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'Qty Out', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'QOH', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'UOM', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'Qty In', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'Qty Out', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'QOH', 'B', 0, 'R');        
        $pdf->ln();

        $table  = "item_moves";
        $select = "item_moves.*,items.name,items.code,trans_types.name as particular,items.no_per_pack as it_per_pack,items.no_per_pack_uom as it_per_pack_uom";
        $join["items"] = array('content'=>"item_moves.item_id = items.item_id");
        $join["trans_types"] = array('content'=>"item_moves.type_id = trans_types.type_id");
        $args = array();
        $args2 = array();
        $args3 = array();
        if(isset($_GET['item-search']) && $_GET['item-search'] != ""){
            $args['item_moves.item_id'] = $_GET['item-search'];
            $args2['item_moves.item_id'] = $_GET['item-search'];
            $args3['item_moves.item_id'] = $_GET['item-search'];
        }
        // echo "<pre>", print_r($_GET), "</pre>";die();
        if(isset($_GET['branch_id']) && $_GET['branch_id'] != ""){
            $args['item_moves.branch_code'] = $_GET['branch_id'];
        }
        if(isset($_GET['calendar_range']) && $_GET['calendar_range'] != ""){
            $daterange = $_GET['calendar_range'];
            $dates = explode(" to ",$daterange);
            $from = date2SqlDateTime($dates[0].'00:00:01');
            $to = date2SqlDateTime($dates[1].'23:59:59');
            $args["item_moves.reg_date  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            $args2["item_moves.reg_date  < '".$from."'"] = array('use'=>'where','val'=>null,'third'=>false);
            $args3["item_moves.reg_date  > '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        }
        $args['item_moves.inactive'] = 0;
        $args2['item_moves.inactive'] = 0;
        $args3['item_moves.inactive'] = 0;
        $order = array('item_moves.reg_date'=>'asc','items.name'=>'asc');
        $group = null;
        $items = $this->site_model->get_tbl($table,$args,$order,$join,true,$select,$group);
        // echo "<pre>", print_r($items), "</pre>";die();    
        $item_moves_sum = $this->items_model->get_item_moves_total(null,$args2);
        $item_moves_after = $this->items_model->get_item_moves_total(null,$args3);

        $trans_count = count($items);

        $before_m_item = 0;
        if($item_moves_sum){
            $before_m_item = $item_moves_sum[0]->in_item_qty;
        }

        $after_m_item = 0;
        if($item_moves_after){
            $after_m_item = $item_moves_after[0]->in_item_qty;
        }

        $json = array();
        $html = "";
        $pdf->SetFont('helvetica', '', 10);
        if(count($items) > 0){
            $ctr = 1;
            $last = count($items);
            $colspan = 5;
            $curr = $before_m_item;
            foreach ($items as $res) {
                if($ctr == 1){
                    $pdf->Cell(190, 0, 'Quantity on Hand Before '.sql2Date($from).' '.toTimeW($from), 'B', 0, 'L');  
                    $pdf->Cell(17, 0, $before_m_item, 'B', 0, 'R');  
                    $c = "";
                    if($res->it_per_pack > 0){
                        $c = $before_m_item/$res->it_per_pack;
                    } 
                    $pdf->Cell(60, 0, $c, 'B', 0, 'R');
                }
                $ctr++;
                $pdf->ln();
                $pdf->SetFont('helvetica', '', 9);
                // $this->make->sRow();
                $cell_border = array(
                   'R' => array('width' => 0.2),
                   'B' => array('width' => 0.2)
                );
                $pdf->Cell(22, 0, ucwords(strtolower($res->particular)), $cell_border, 0, 'L'); 
                $pdf->Cell(20, 0, $res->trans_ref, $cell_border, 0, 'L'); 
                $pdf->Cell(35, 0,sql2Date($res->reg_date)." ".toTimeW($res->reg_date), $cell_border, 0, 'L');
                $width = 40;
                $font_size = 9;
                $height = 0;
                do {
                    $font_size -= 0.5;
                    $string_font_width = $pdf->GetStringWidth($res->name,'helvetica','',$font_size) + 3; # +4 Not exactly sure but works
                    $height = 4.2;
                } while ($string_font_width > $width);

                $pdf->SetFont('helvetica','',$font_size);
                $pdf->cell(40,$height,$res->name,$cell_border,0,'L'); 
                $pdf->SetFont('helvetica','',9);
                $font_size = 9;
                do {
                    $font_size -= 0.5;
                    $string_font_width = $pdf->GetStringWidth($res->branch_code,'helvetica','',$font_size) + 3; # +4 Not exactly sure but works
                    $height = 4.2;
                } while ($string_font_width > $width);
                $pdf->SetFont('helvetica','',$font_size);
                $pdf->Cell(30, $height, $res->branch_code, $cell_border, 0, 'L'); 
                $pdf->SetFont('helvetica','',9);
                $pdf->Cell(15, 0, $res->uom, $cell_border, 0, 'C'); 
                $in = "";
                $out = "";
                if($res->qty >= 0){
                    $in = $res->qty;
                    $curr += $res->qty;
                    $out = "";
                }
                else{
                    $out = $res->qty;
                    $curr += $res->qty;
                    $out = $out * -1;
                }

                $pdf->Cell(15, 0, $in, $cell_border, 0, 'R'); 
                $pdf->Cell(15, 0, $out, $cell_border, 0, 'R'); 
                $pdf->Cell(15, 0, $curr, $cell_border, 0, 'R'); 
                $pdf->Cell(15, 0, $res->it_per_pack_uom, $cell_border, 0, 'C'); 
                $in = "";
                $out = "";
                $curr2 = "";
                if($res->it_per_pack > 0){
                    $curr2 = $curr/$res->it_per_pack;
                    if($res->qty >= 0){
                        $val = $res->qty/$res->it_per_pack;
                        $in = num($val);
                    }
                    else{
                        $val = $res->qty/$res->it_per_pack;
                        $out = num($val);
                    }
                }
                $pdf->Cell(15, 0, $in, $cell_border, 0, 'R'); 
                $pdf->Cell(15, 0, $out, $cell_border, 0, 'R'); 
                $pdf->Cell(15, 0, $curr2, $cell_border, 0, 'R');
                $ctr++;
            }
            // $pdf->ln();
            // $pdf->Cell(190, 0, 'Quantity on Hand After '.sql2Date($res->reg_date).' '.toTimeW($res->reg_date), 'B', 0, 'L');  
            // $pdf->Cell(17, 0, $curr + $after_m_item, 'B', 0, 'R');  
            // $c = "";
            // if($res->it_per_pack > 0){
            //     $c = ($curr + $after_m_item)/$res->it_per_pack;
            // } 
            // $pdf->Cell(60, 0, $c, 'B', 0, 'R');
        }             

       
        update_load(100); 
        // -----------------------------------------------------------------------------

        //Close and output PDF document
        header_remove('Set-Cookie');

        $pdf->Output('component_inventory_moves_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+  
    }

    public function inventory_moves_excel()
    {

        $this->load->model("dine/menu_model");
        $this->load->model('dine/setup_model');
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Inventory Movements Report';
        $rc=1;
        #GET VALUES
        start_load(0);
        $branch_code = $_GET['branch_id'];
        $setup = $this->setup_model->get_details($branch_code);
        $set = $setup[0];

        update_load(10);
        sleep(1);
        // echo 'das';die();      
        $daterange = $_GET['calendar_range'];  
        $table  = "item_moves";
        $select = "item_moves.*,items.name,items.code,trans_types.name as particular,items.no_per_pack as it_per_pack,items.no_per_pack_uom as it_per_pack_uom";
        $join["items"] = array('content'=>"item_moves.item_id = items.item_id");
        $join["trans_types"] = array('content'=>"item_moves.type_id = trans_types.type_id");
        $args = array();
        $args2 = array();
        $args3 = array();
        if(isset($post['item-search']) && $post['item-search'] != ""){
            $args['item_moves.item_id'] = $post['item-search'];
            $args2['item_moves.item_id'] = $post['item-search'];
            $args3['item_moves.item_id'] = $post['item-search'];
        }
        // echo "<pre>", print_r($_GET), "</pre>";die();
        if(isset($_GET['branch_id']) && $_GET['branch_id'] != ""){
            $args['item_moves.branch_code'] = $_GET['branch_id'];
        }
        if(isset($_GET['calendar_range']) && $_GET['calendar_range'] != ""){
            $daterange = $_GET['calendar_range'];
            $dates = explode(" to ",$daterange);
            $from = date2SqlDateTime($dates[0].'00:00:01');
            $to = date2SqlDateTime($dates[1].'23:59:59');
            $args["item_moves.reg_date  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            $args2["item_moves.reg_date  < '".$from."'"] = array('use'=>'where','val'=>null,'third'=>false);
            $args3["item_moves.reg_date  > '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        }
        $args['item_moves.inactive'] = 0;
        $args2['item_moves.inactive'] = 0;
        $args3['item_moves.inactive'] = 0;
        $order = array('item_moves.reg_date'=>'asc','items.name'=>'asc');
        $group = null;
        $items = $this->site_model->get_tbl($table,$args,$order,$join,true,$select,$group);
        // echo "<pre>", print_r($items), "</pre>";die();    
        $item_moves_sum = $this->items_model->get_item_moves_total(null,$args2);
        $item_moves_after = $this->items_model->get_item_moves_total(null,$args3);

        $trans_count = count($items);

        $before_m_item = 0;
        if($item_moves_sum){
            $before_m_item = $item_moves_sum[0]->in_item_qty;
        }

        $after_m_item = 0;
        if($item_moves_after){
            $after_m_item = $item_moves_after[0]->in_item_qty;
        } 

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
                'size' => 12,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleHeaderCell2 = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C5D9F1')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array('rgb' => '000000'),
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
        $styleCenter = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldCenter = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleItalicLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'italic' => true,
                'size' => 12,
            )
        );
        $styleItalicRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'italic' => true,
                'size' => 12,
            )
        );
        $headers = array('Description','Reference #','Trans Date', 'Item Name', 'Branch', 'UOM', 'Qty In', 'Qty Out', 'QOH', 'UOM', 'Qty In', 'Qty Out', 'QOH');
        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;  
        if(count($items) > 0){
            $ctr = 1;
            $last = count($items);
            $curr = $before_m_item;
            $new_rc = 8;
            foreach ($items as $res) {
                if($ctr == 1){
                    $sheet->mergeCells('A7:F7');
                    $sheet->getCell('A'.$rc)->setValue('Quantity on Hand Before '.sql2Date($from).' '.toTimeW($from));
                    $sheet->getCell('I7')->setValue($before_m_item); 
                    $c = "";
                    if($res->it_per_pack > 0){
                        $c = $before_m_item/$res->it_per_pack;
                    } 
                    $sheet->getCell('M7')->setValue($c);
                }
                $sheet->getCell('A'.$new_rc)->setValue(ucwords(strtolower($res->particular)));
                $sheet->getCell('B'.$new_rc)->setValue('="'.$res->trans_ref.'"');
                $sheet->getCell('C'.$new_rc)->setValue(sql2Date($res->reg_date)." ".toTimeW($res->reg_date));
                $sheet->getCell('D'.$new_rc)->setValue($res->name);
                $sheet->getCell('E'.$new_rc)->setValue($res->branch_code);
                $sheet->getCell('F'.$new_rc)->setValue($res->uom);
                $in = "";
                $out = "";
                if($res->qty >= 0){
                    $in = $res->qty;
                    $curr += $res->qty;
                    $out = "";
                }
                else{
                    $out = $res->qty;
                    $curr += $res->qty;
                    $out = $out * -1;
                }

                $sheet->getCell('G'.$new_rc)->setValue($in);
                $sheet->getCell('H'.$new_rc)->setValue($out);
                $sheet->getCell('I'.$new_rc)->setValue($curr);
                $sheet->getCell('J'.$new_rc)->setValue($res->it_per_pack_uom); 
                $in = "";
                $out = "";
                $curr2 = "";
                if($res->it_per_pack > 0){
                    $curr2 = $curr/$res->it_per_pack;
                    if($res->qty >= 0){
                        $val = $res->qty/$res->it_per_pack;
                        $in = num($val);
                    }
                    else{
                        $val = $res->qty/$res->it_per_pack;
                        $out = num($val);
                    }
                }
                $sheet->getCell('K'.$new_rc)->setValue($in);
                $sheet->getCell('L'.$new_rc)->setValue($out);
                $sheet->getCell('M'.$new_rc)->setValue($curr2);

                $new_rc++;

                // $sheet->mergeCells('A7:F7');
            } 
                $sheet->mergeCells('A'.$new_rc.':F'.$new_rc);
                $sheet->getCell('A'.$new_rc)->setValue('Quantity on Hand After '.sql2Date($res->reg_date).' '.toTimeW($res->reg_date));
                $sheet->getCell('I'.$new_rc)->setValue($curr + $after_m_item); 
                $c = "";
                if($res->it_per_pack > 0){
                    $c = ($curr + $after_m_item)/$res->it_per_pack;
                } 
                $sheet->getCell('M'.$new_rc)->setValue($c);
        }  

        update_load(100);        
       
        ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    ########################################################################
    ### PRINTING    
        public function print_inventory(){
                $this->load->library('Excel');
                $sheet = $this->excel->getActiveSheet();
                $this->load->model('dine/items_model');

                $get_inventory = $this->items_model->get_inventory_moves();
                echo 'dasda';die();
                $fields = $get_inventory->result_array();

                $fields = array();
                if (!empty($get_inventory)) {
                    $row = $get_inventory[0];
                    foreach ($row as $k => $v) {
                        if (strpos($k, "!!Trans-") === false)
                            continue;

                        $fields[$k] = str_replace("!!Trans-", "", $k);
                    }
                }

                /*Print Headers*/
                // Print "Item Code"
                // Print "Item Name"
                foreach ($fields as $i => $iv) {
                    // Print $iv
                }

                foreach ($get_inventory as $inv) {
                    // set initial column (eg. A) to be used as $sheet->getColumnDimension('A')

                    // Print $inv['code']
                    // Print $inv['name']

                    $initial_column = "C";
                    foreach ($fields as $kf => $kv) {
                        // Print $inv[$kf]
                        ++$initial_column;
                    }
                }



                //$date = $this->input->get('date_to');
                //$date_param = (is_null($date) ? date('Y-m-d') : $date);
                //$terminal = $this->input->get('terminal');
                //$cashier = $this->input->get('cashier');

                // $sheet->getColumnDimension('A')->setWidth(15);
                // $sheet->getColumnDimension('B')->setWidth(5);
                // $sheet->getColumnDimension('C')->setWidth(15);
                // //-----------------------------------------------------------------------------
                // //START HEADER
                // //-----------------------------------------------------------------------------
                // $rc = 1;
                // $filename='Hourly Sales Report';
                // $sheet->getCell('A'.$rc)->setValue($filename);
                // $sheet->getStyle('A'.$rc.':'.'I'.$rc)->getFont()->setBold(true);
                // $sheet->getStyle('A'.$rc.':'.'I'.$rc)->getFont()->getColor()->setRGB('FF0000');

                // if (!empty($cashier)){
                //     $cashier_name = $cashier;
                // }else{
                //     $cashier_name = 'All Cashier';
                // }
                // $rc++;
                // $sheet->getCell('A'.$rc)->setValue('Employee');
                // $sheet->getStyle('A'.$rc)->getFont()->setBold(true);
                // $sheet->getCell('B'.$rc)->setValue($cashier_name);

                // if (!empty($terminal)){
                //     $terminal_name = $terminal;
                // }else{
                //     $terminal_name = 'All Terminal';
                // }
                // $rc++;
                // $sheet->getCell('A'.$rc)->setValue('PC');
                // $sheet->getStyle('A'.$rc)->getFont()->setBold(true);
                // $sheet->getCell('B'.$rc)->setValue($terminal_name);

                // $rc++;
                // $sheet->getCell('A'.$rc)->setValue('Date');
                // $sheet->getStyle('A'.$rc)->getFont()->setBold(true);
                // // $sheet->getCell('B'.$rc)->setValue(date("Y-m-d H:i:s"));
                // $sheet->getCell('B'.$rc)->setValue($date_param);

                // $rc++;
                // $sheet->getCell('A'.$rc)->setValue('Printed on');
                // $sheet->getStyle('A'.$rc)->getFont()->setBold(true);
                // // $sheet->getCell('B'.$rc)->setValue(date("Y-m-d H:i:s"));
                // $sheet->getCell('B'.$rc)->setValue(date("d M y g:i:s A"));
                // $rc++;
                // $sheet->getStyle('A'.$rc.':'.'D'.$rc)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED);

                // //-----------------------------------------------------------------------------
                // //END HEADER
                // //-----------------------------------------------------------------------------

                // $rc++;

                // $ctr=1;
                // $gtotal_net_sales = 0;
                // foreach(unserialize(TIMERANGES) as $k=>$v){
                //     $rc++;
                //     $sheet->getCell('B'.$rc)->setValue($ctr.' '.$v['FTIME'].' - '.$v['TTIME']);
                //     $rc++;
                //     $sheet->getCell('A'.$rc)->setValue('Net Sales Total');

                //     $net_sales_total = $this->settings_model->get_hourly_sales(null,$v['FTIME'],$v['TTIME'],$date);
                //     $net_sales_total = $net_sales_total[0];
                //     $col_a = $col_b = 0;
                //     // $sheet->getCell('C'.$rc)->setValue($col_a);
                //     $col_b = $net_sales_total;
                //     // $sheet->getCell('A'.$rc)->setValue('-->'.$col_b->total_per_hour);
                //     // $sheet->getCell('A'.$rc)->setValue($this->db->last_query());
                //     $sheet->getCell('D'.$rc)->setValue(number_format($col_b->total_per_hour,2));
                //     $gtotal_net_sales += $col_b->total_per_hour;

                //     $rc++;
                //     $sheet->getCell('A'.$rc)->setValue('Average $/Cover');

                //     $col_a = $col_b = 0;
                //     $sheet->getCell('C'.$rc)->setValue($col_a);
                //     $sheet->getCell('D'.$rc)->setValue(number_format($col_b,2));

                //     $rc++;
                //     $sheet->getCell('A'.$rc)->setValue('Average $/Check');

                //     $col_a = $col_b = 0;
                //     $sheet->getCell('C'.$rc)->setValue($col_a);
                //     $sheet->getCell('D'.$rc)->setValue(number_format($col_b,2));

                //     $ctr++;
                // }

                // $rc++;
                // $sheet->getCell('A'.$rc)->setValue('TOTAL');
                // $rc++;
                // $sheet->getCell('A'.$rc)->setValue('Net Sales Total');
                // $sheet->getCell('D'.$rc)->setValue(number_format($gtotal_net_sales,2));


                // Redirect output to a client’s web browser (Excel2007)
                //clean the output buffer
                // ob_end_clean();

                // header('Content-type: application/vnd.ms-excel');
                // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
                // header('Cache-Control: max-age=0');
                // $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
                // $objWriter->save('php://output');

                $filename='inventory'.phpNow().'.xls';
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                $objWriter->save('php://output');
        }
        public function list_excel(){
            $this->load->library('Excel');
            $sheet = $this->excel->getActiveSheet();

            $table = "items";
            $join = array(
                'categories'    => 'items.cat_id = categories.cat_id',
                'subcategories' => 'items.subcat_id = subcategories.cat_id',
            );
            $select = 'items.*,categories.code as cat_code,subcategories.code as sub_cat_code';
            $args = array();
            $order = array('categories.code'=>'asc','subcategories.code'=>'asc','items.name'=>'asc');
            $items = $this->site_model->get_tbl($table,$args,$order,$join,true,$select);

            $rows = array();
            foreach ($items as $res) {
                $row = array(
                    'CODE' => $res->code,
                    'BARCODE' => $res->barcode,
                    'NAME' => $res->name,
                    'CATEGORY' => $res->cat_code,
                    'SUBCATEGORY' => $res->sub_cat_code,
                    'UOM' => $res->uom,
                    'COST' => $res->cost,
                );
                $rows[] = $row;
            }
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
            $rc = 1;
            $sheet->getColumnDimension('A')->setWidth(40);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $headers = array('A'=>'CODE','B'=>'BARCODE','C'=>'NAME','D'=>'CATEGORY','E'=>'SUBCATEGORY','F'=>'UOM','G'=>'COST');
            foreach ($headers as $let => $text) {
                $sheet->getCell($let.$rc)->setValue($text);
                // $sheet->getStyle($let.$rc)->getFont()->setBold(true);
                // $sheet->getStyle($let.$rc)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFFF');
                $sheet->getStyle($let.$rc)->applyFromArray($styleHeaderCell);
            }
            $rc+=1;
            foreach ($rows as $row) {
                foreach ($headers as $let => $text) {
                    $sheet->getCell($let.$rc)->setValue($row[$text]);
                    $sheet->getStyle($let.$rc)->applyFromArray($styleTxt);
                }
                $rc++;
            }
            $filename = 'Item List.xls';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            $objWriter->save('php://output');
        }
    ########################################################################    
    ### UPLOADING
    public function upload_sub_cat_items(){
        $this->load->library('excel');
        $obj = PHPExcel_IOFactory::load('barcino_items2.xlsx');
        $sheet = $obj->getActiveSheet()->toArray(null,true,true,true);
        $count = count($sheet);
        $start = 2;
        $rows = array();
        $query = "";
        for($i=$start;$i<=$count;$i++){
            $rows[] = array(
                // "item_code"         => $sheet[$i]["A"],
                // "barcode"           => $sheet[$i]["B"],
                // "name"              => $sheet[$i]["C"],
                "category"          => $sheet[$i]["F"],
                "subcategory"       => $sheet[$i]["G"],
                // "uom"               => $sheet[$i]["G"],
                // "cost"              => $sheet[$i]["H"],
            );
        }
        $cats = array();
        foreach ($rows as $row) {
            if(!isset($cats[$row['category']]) && $row['category'] != ""){
                $cats[$row['category']] = $row['category'];
            }
        }
        $item_categories = $this->site_model->get_tbl('categories');
        $db_cats = array();
        foreach ($item_categories as $res) {
            $db_cats[$res->code] = $res->code;
        }
        foreach ($cats as $cat) {
            if(!in_array($cat, $db_cats)){
                echo "walang ".$cat."<br>";
            }
        }

        $item_categories = $this->site_model->get_tbl('categories');
        $db_cats = array();
        foreach ($item_categories as $res) {
            $db_cats[$res->code] = $res->cat_id;
        }

        $items = array();
        $ctr = 0;
        foreach ($rows as $row) {
            if(!isset($items[$row['subcategory']]) && $row['subcategory'] != ""){
                $det =  array(
                    'cat_id'            => $db_cats[$row['category']],
                    'code'              => $row['subcategory'],
                    'name'              => $row['subcategory'],
                );
                $query .= $this->db->insert_string('subcategories',$det).";\r\n";
                $items[$row['subcategory']] = $det;
                $ctr++;
            }
        }
        $query .= "#".$ctr;
        echo "<pre>".$query."</pre>";
    }    
    public function upload_items(){
        $this->load->library('excel');
        $obj = PHPExcel_IOFactory::load('barcino_items2.xlsx');
        $sheet = $obj->getActiveSheet()->toArray(null,true,true,true);
        $count = count($sheet);
        $start = 2;
        $rows = array();
        $query = "";
        for($i=$start;$i<=$count;$i++){
            // if($sheet[$i]["B"] != ""){
                $rows[] = array(
                    "item_code"         => $sheet[$i]["B"],
                    "barcode"           => $sheet[$i]["C"],
                    "name"              => $sheet[$i]["D"],
                    "category"          => $sheet[$i]["F"],
                    "subcategory"       => $sheet[$i]["G"],
                    "uom"               => $sheet[$i]["H"],
                    "cost"              => $sheet[$i]["I"],
                );
            // }
        }
        // $uoms = array();
        // foreach ($rows as $row) {
        //     if(!isset($uoms[$row['uom']]) && $row['uom'] != ""){
        //         $items =  array(
        //             'code' => $row['uom'],
        //             'name' => $row['uom'],
        //         );
        //         $query .= $this->db->insert_string('uom',$items).";\r\n";
        //         $uoms[$row['uom']] = $items;
        //     }
        // }

        $cats = array();
        foreach ($rows as $row) {
            if(!isset($cats[$row['category']]) && $row['category'] != ""){
                $cats[$row['category']] = $row['category'];
            }
        }
        $item_categories = $this->site_model->get_tbl('categories');
        $db_cats = array();
        foreach ($item_categories as $res) {
            $db_cats[$res->code] = $res->code;
        }
        foreach ($cats as $cat) {
            if(!in_array($cat, $db_cats)){
                echo "walang ".$cat."<br>";
            }
        }

        $sub = array();
        foreach ($rows as $row) {
            if(!isset($sub[$row['subcategory']]) && $row['subcategory'] != ""){
                $sub[$row['subcategory']] = $row['subcategory'];
            }
        }
        $sub_categories = $this->site_model->get_tbl('subcategories');
        $db_sub = array();
        foreach ($sub_categories as $res) {
            $db_sub[$res->code] = $res->code;
        }
        foreach ($sub as $cat) {
            if(!in_array($cat, $db_sub)){
                echo "walang ".$cat."<br>";
            }
        }


        $item_categories = $this->site_model->get_tbl('categories');
        $db_cats = array();
        foreach ($item_categories as $res) {
            $db_cats[$res->code] = $res->cat_id;
        }
        $sub_categories = $this->site_model->get_tbl('subcategories');
        $db_sub = array();
        foreach ($sub_categories as $res) {
            $db_sub[$res->code] = $res->sub_cat_id;
        }
        $uoms = $this->site_model->get_tbl('uom');
        $db_uom = array();
        foreach ($uoms as $res) {
            $db_uom[strtoupper($res->code)] = $res->code;
        }

        $items = array();
        $ctr = 1;
        foreach ($rows as $row) {
            if(!isset($items[$row['item_code']]) && $row['item_code'] != ""){
                $sub = "";
                if(isset($db_sub[$row['subcategory']]))
                    $sub = $db_sub[$row['subcategory']];
                $det =  array(
                    'code'              => $row['item_code'],
                    'name'              => $row['name'],
                    'desc'              => $row['name'],
                    "barcode"           => $row['barcode'],
                    "cat_id"            => $db_cats[$row['category']],
                    "subcat_id"         => $sub,
                    "uom"               => $db_uom[$row['uom']],
                    "cost"              => $row['cost'],
                );
                $query .= $this->db->insert_string('items',$det).";\r\n";
                $items[$row['item_code']] = $det;
                $ctr++;
            }
        }

        $query .= "#".$ctr;
        echo "<pre>".$query."</pre>";
    }
    public function upload_menu_recipe(){
        $this->load->library('excel');
        $obj = PHPExcel_IOFactory::load('barcino_recipe.xlsx');
        $sheet = $obj->getActiveSheet()->toArray(null,true,true,true);
        $count = count($sheet);
        $start = 2;
        $rows = array();
        $query = "";
        for($i=$start;$i<=$count;$i++){
            $rows[] = array(
                "menu_code"         => $sheet[$i]["A"],
                "item_code"         => $sheet[$i]["B"],
                "qty"               => $sheet[$i]["C"],
                "uom"               => $sheet[$i]["D"],
                "cost"              => $sheet[$i]["E"],
            );
        }
        
        $sub = array();
        foreach ($rows as $row) {
            if(!isset($sub[$row['item_code']]) && $row['item_code'] != ""){
                $sub[$row['item_code']] = $row['item_code'];
            }
        }
        $sub_categories = $this->site_model->get_tbl('items');
        $db_sub = array();
        foreach ($sub_categories as $res) {
            $db_sub[$res->code] = $res->code;
        }
        foreach ($sub as $cat) {
            if(!in_array($cat, $db_sub)){
                echo "walang ".$cat."<br>";
            }
        }
        return false;


        $menus = $this->site_model->get_tbl('menus');
        $db_menus = array();
        foreach ($menus as $res) {
            $db_menus[$res->menu_code] = $res->menu_id;
        }
        

        $items = $this->site_model->get_tbl('items');
        $db_items = array();
        foreach ($items as $res) {
            $db_items[$res->code] = $res->item_id;
        }
        $query ="";

        foreach ($rows as $row) {
            $det = array(
                'menu_id'   => $db_menus[preg_replace('/\s+/', '', $row['menu_code'])],
                'item_id'   => $db_items[preg_replace('/\s+/', '', $row['item_code'])],
                'uom'       => $row['uom'],
                'qty'       => $row['qty'],
                'cost'      => $row['cost'],
            );
            $query .= $this->db->insert_string('menu_recipe',$det).";\r\n";
        }
        echo "<pre>".$query."</pre>";
    }
    public function export_form(){
        $data['code'] = exportItemsForm();
        $this->load->view('load',$data);
    }
    public function export_data(){
        $this->load->model('dine/menu_model');
        
        $branch_id = $this->input->post('branch_id');
        // echo $branch_id;
        
        $data = $this->site_model->get_exportData_items($branch_id);
        // echo "<pre>",print_r($data),"</pre>";die();
        $filename = date('Ymd')."- Item List.csv";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        $f = fopen('php://output', 'w');
        foreach ($data as $val) {
            $csv = array(
                    "code"=>$val->code,
                    "barcode"=>$val->barcode,
                    "name"=>$val->name,
                    "category_code"=>$val->category_code,
                    "category_name"=>$val->c_name,
                    "subcategories_name"=>$val->subcategories_name,
                    "uom"=>$val->uom,
                    "cost"=>$val->cost
                    );
            // $csv = array(
            //         "code"=>"code",
            //         "barcode"=>"barcode",
            //         "name"=>"name",
            //         "category_code"=>"category_code",
            //         "category_name"=>"category_name",
            //         "subcategories_name"=>"subcategories_name",
            //         "uom"=>"uom",
            //         "cost"=>"cost"
            //         );
            fputcsv($f, $csv);
        }
        // $data['code'] = exportItemsForm();
        // $this->load->view('load',$data);
    }
    public function export_item_hierarchy(){
        $this->load->model('dine/menu_model');
        
        $branch_id = $this->input->post('branch_id');
        // echo $branch_id;
        
        $data = $this->site_model->get_exportData_items($branch_id);
        // echo "<pre>",print_r($data),"</pre>";die();
        $filename = date('Ymd')."- Item List.p1";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        $f = fopen('php://output', 'w');
        foreach ($data as $val) {
            $csv = array(
                    "item_id"=>"!@#$%^&*()_+".$val->item_id."!@#$%^&*()_+",
                    "code"=>"!@#$%^&*()_+".$val->code."!@#$%^&*()_+",
                    "barcode"=>"!@#$%^&*()_+".$val->barcode."!@#$%^&*()_+",
                    "name"=>"!@#$%^&*()_+".$val->name."!@#$%^&*()_+",
                    "category_code"=>"!@#$%^&*()_+".$val->category_code."!@#$%^&*()_+",
                    "category_name"=>"!@#$%^&*()_+".$val->c_name."!@#$%^&*()_+",
                    "subcategory_code"=>"!@#$%^&*()_+".$val->subcategories_code."!@#$%^&*()_+",
                    "subcategories_name"=>"!@#$%^&*()_+".$val->subcategories_name."!@#$%^&*()_+",
                    "uom"=>"!@#$%^&*()_+".$val->uom."!@#$%^&*()_+",
                    "cat_id"=>"!@#$%^&*()_+".$val->cat_id."!@#$%^&*()_+",
                    "subcat_id"=>"!@#$%^&*()_+".$val->subcat_id."!@#$%^&*()_+",
                    "type"=>"!@#$%^&*()_+".$val->type."!@#$%^&*()_+",
                    "cost"=>"!@#$%^&*()_+".$val->cost."!@#$%^&*()_+"
                    );            
            fputcsv($f, $csv);
        }
    }
    // price listing in Items 
    // 09/13/2018
    // Nicko Q. 
    public function price_listing($item_id=null,$branch_code=null){
        $menus=array();
        if(isset($item_id)){
            $item = $this->items_model->get_item($item_id);
        }
        // echo "<pre>",print_r($item),"</pre>";die();
        $data['code'] = makePriceListing($item);
        $this->load->view('load',$data);
    }
    public function price_history_item($item_id=null,$branch_code=null){
        $history = array();
        // echo "<pre>",print_r($menu_id),"</pre>";die();
        if($item_id != null){
            $history = $this->items_model->get_price_history($item_id);
        }
        $data['code'] = makeHistorypricingItem($history);
        $this->load->view('load',$data);
    }
    //menu mocvement
        public function menu_move(){
            $data = $this->syter->spawn('inq');
            $data['page_title'] = fa('fa-random').' Item Inventory Movement';
            $data['code'] = menuMovePage();           
            $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
            $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','js/jspdf.js');
            $data['load_js'] = "dine/items.php";
            $data['use_js'] = "menuMoveJS";
            $this->load->view('page',$data);
        }

        public function get_menu_move($id=null,$asJson=true){
            sess_clear('moves_array');
            $this->load->helper('site/pagination_helper');
            $pagi = null;
            // $total_rows = 100;
            if($this->input->post('pagi'))
                $pagi = $this->input->post('pagi');
            $post = array();
            if(count($this->input->post()) > 0){
                $post = $this->input->post();
            }

            $daterange = $post['calendar_range'];
            $dates = explode(" to ",$daterange);
            // $from = date2SqlDateTime($dates[0]);
            // $to = date2SqlDateTime($dates[1]);

            $from_date = date2Sql($dates[0]); 
            $to_date = date2Sql($dates[1]);

            $strtt =  strtotime($from_date);
            $moves_array = array();
            $curr_date = date('Y-m-d');
            do{
                // $this->site_model->db = $this->load->database('default', TRUE);

                $datefrom = date('Y-m-d', $strtt);

                $table  = "menu_moves";
                $select = "menu_moves.*,menus.menu_name,menus.menu_code,trans_types.name as particular";
                $join["menus"] = array('content'=>"menu_moves.item_id = menus.menu_id AND menu_moves.branch_code = menus.branch_code");
                $join["trans_types"] = array('content'=>"menu_moves.type_id = trans_types.type_id");
                $args = array();
                // $args2 = array();
                if(isset($post['menu-search']) && $post['menu-search'] != ""){
                    $args['menu_moves.item_id'] = $post['menu-search'];
                }
                if(isset($post['branch_id']) && $post['branch_id'] != ""){
                    $args['menu_moves.branch_code'] = $post['branch_id'];
                }
                // if(isset($post['calendar_range']) && $post['calendar_range'] != ""){
                    // $daterange = $post['calendar_range'];
                    // $dates = explode(" to ",$daterange);
                    // $from = date2SqlDateTime($dates[0]);
                    // $to = date2SqlDateTime($dates[1]);
                    // $args["menu_moves.reg_date  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
                $args["DATE(menu_moves.reg_date)  = '".$datefrom."'"] = array('use'=>'where','val'=>null,'third'=>false);
                // }
                $args['menu_moves.inactive'] = 0;
                $order = array('menu_moves.reg_date'=>'desc','menus.menu_name'=>'asc');
                $group = null;
                // $count = $this->site_model->get_tbl($table,$args,$order,$join,true,$select,$group,null,true);
                // $page = paginate('items/get_items',$count,$total_rows,$pagi);
                $items = $this->site_model->get_tbl($table,$args,$order,$join,true,$select,$group);
                // echo $this->site_model->db->last_query();
                // return false;
                // $args['items.barcode'] = array('use'=>'where','val'=>$barcode);
                // echo $this->db->last_query();die();
                // echo "<pre>",print_r($items),"</pre>";die();
                // if($items){
                //     foreach($items as $res){
                //         $ids = strtotime($res->reg_date);
                //         if(isset($moves_array[$ids][$res->trans_ref])){
                //             $moves_array[$ids][$res->trans_ref]['qty'] += $res->qty;
                //         }else{
                //             $moves_array[$ids][$res->trans_ref] = array(
                //                 'particular'=>$res->particular,
                //                 'trans_ref'=>$res->trans_ref,
                //                 'reg_date'=>$res->reg_date,
                //                 'code'=>$res->menu_code,
                //                 'name'=>$res->menu_name,
                //                 'qty'=>$res->qty,
                //             );
                //         }
                //     }
                // }
                if($items){
                    foreach($items as $res){
                        $ids = strtotime($res->reg_date);
                        if(isset($moves_array[$res->item_id][$res->trans_ref])){
                            $moves_array[$res->item_id][$res->trans_ref]['qty'] += $res->qty;
                        }else{
                            $moves_array[$res->item_id][$res->trans_ref] = array(
                                'particular'=>$res->particular,
                                'trans_ref'=>$res->trans_ref,
                                'reg_date'=>$res->reg_date,
                                'code'=>$res->menu_code,
                                'name'=>$res->menu_name,
                                'qty'=>$res->qty,
                            );
                        }
                    }
                }

                // $tablem = "trans_sales_menus";
                // $selectm = "trans_sales_menus.*,menus.menu_name,menus.menu_code,trans_types.name as particular,trans_sales.datetime, trans_sales.trans_ref";
                // // $selectm_s = "sum(qty) as menu_sales_qty";
                // $joinm["trans_sales"] = array('content'=>"trans_sales.sales_id = trans_sales_menus.sales_id AND trans_sales.branch_code = '".$post['branch_id']."'");
                // $joinm["menus"] = array('content'=>"menus.menu_id = trans_sales_menus.menu_id AND menus.branch_code = '".$post['branch_id']."'");
                // $joinm["trans_types"] = array('content'=>"trans_sales.type_id = trans_types.type_id");
                // $args3 = array();
                // $args4 = array();
                // // $join["trans_types"] = array('content'=>"menu_moves.type_id = trans_types.type_id");
                // // $args3["trans_sales.trans_ref IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                // // $args3["trans_sales.type_id"] = 10;
                // // $args3["trans_sales.inactive"] = '0';

                // $args4["trans_sales.trans_ref IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                // $args4["trans_sales.type_id"] = 10;
                // $args4["trans_sales.inactive"] = '0';


                // if(isset($post['menu-search']) && $post['menu-search'] != ""){
                //     // $args2['menu_moves.item_id'] = $post['menu-search'];
                //     // $args3['trans_sales_menus.menu_id'] = $post['menu-search'];
                //     $args4['trans_sales_menus.menu_id'] = $post['menu-search'];
                // }
                // if(isset($post['branch_id']) && $post['branch_id'] != ""){
                //     $args4['trans_sales_menus.branch_code'] = $post['branch_id'];
                // }
                // // if(isset($post['calendar_range']) && $post['calendar_range'] != ""){
                // //     $daterange = $post['calendar_range'];
                // //     $dates = explode(" to ",$daterange);
                // //     $from = date2SqlDateTime($dates[0]);
                // //     $to = date2SqlDateTime($dates[1]);
                // //     $fromD = date2Sql($dates[0]);
                // // $args2["DATE(menu_moves.reg_date)  < '".$fromD."'"] = array('use'=>'where','val'=>null,'third'=>false);
                // // $args3["DATE(trans_sales.datetime)  < '".$fromD."'"] = array('use'=>'where','val'=>null,'third'=>false);
                // $args4["DATE(trans_sales.datetime) = '".$datefrom."'"] = array('use'=>'where','val'=>null,'third'=>false);
                // // }

                // $ordermm = null;
                // $groupmm = null;
                // // if($curr_date == $datefrom){
                //     // $this->site_model->db = $this->load->database('default', TRUE);
                //     // $get_menus_sales = $this->site_model->get_tbl($tablem,$args4,$ordermm,$joinm,true,$selectm,$groupmm); 
                // // }else{
                //     // $this->site_model->db = $this->load->database('main', TRUE);
                //     $get_menus_sales = $this->site_model->get_tbl($tablem,$args4,$ordermm,$joinm,true,$selectm,$groupmm); 
                // // }

                // // echo $this->db->last_query().'<br><br>';
                // if($get_menus_sales){
                //     foreach($get_menus_sales as $res){
                //         $ids = strtotime($res->datetime);
                //         if(isset($moves_array[$ids][$res->trans_ref])){
                //             $moves_array[$ids][$res->trans_ref]['qty'] -= $res->qty;
                //         }else{
                //             $moves_array[$ids][$res->trans_ref] = array(
                //                 'particular'=>$res->particular,
                //                 'trans_ref'=>$res->trans_ref,
                //                 'reg_date'=>$res->datetime,
                //                 'code'=>$res->menu_code,
                //                 'name'=>$res->menu_name,
                //                 'qty'=>-$res->qty,
                //             );
                //         }
                //     }
                // }


                $strtt = strtotime($datefrom . ' + 1 day');

            }while($datefrom != $to_date);



            // $args2 = array();

            // $tablem = "trans_sales_menus";
            // $selectm = "trans_sales_menus.*,menus.menu_name,menus.menu_code,trans_types.name as particular,trans_sales.datetime, trans_sales.trans_ref";
            // $selectm_s = "sum(qty) as menu_sales_qty";
            // $joinm["trans_sales"] = array('content'=>"trans_sales.sales_id = trans_sales_menus.sales_id");
            // $joinm["menus"] = array('content'=>"menus.menu_id = trans_sales_menus.menu_id");
            // $joinm["trans_types"] = array('content'=>"trans_sales.type_id = trans_types.type_id");
            // $args3 = array();
            // // $join["trans_types"] = array('content'=>"menu_moves.type_id = trans_types.type_id");
            // $args3["trans_sales.trans_ref IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            // $args3["trans_sales.type_id"] = 10;
            // $args3["trans_sales.inactive"] = '0';


            // if(isset($post['menu-search']) && $post['menu-search'] != ""){
            //     $args2['menu_moves.item_id'] = $post['menu-search'];
            //     $args3['trans_sales_menus.menu_id'] = $post['menu-search'];
            // }
           
            //     $fromD = date2Sql($dates[0]);
            //     $args2["DATE(menu_moves.reg_date)  < '".$fromD."'"] = array('use'=>'where','val'=>null,'third'=>false);
            //     $args3["DATE(trans_sales.datetime)  < '".$fromD."'"] = array('use'=>'where','val'=>null,'third'=>false);
            //     // $args4["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            // // }
            // $menu_moves_sum = $this->items_model->get_menu_moves_total(null,$args2);

            // $orderm = null;
            // $groupm = 'trans_sales_menus.menu_id';
            // $menus_sales_total = $this->site_model->get_tbl($tablem,$args3,$orderm,$joinm,true,$selectm_s,$groupm);

            // $before_s_menu = 0;
            // if($menus_sales_total){
            //     $before_s_menu = $menus_sales_total[0]->menu_sales_qty;
            // }
            // $before_m_menu = 0;
            // if($menu_moves_sum){
            //     $before_m_menu = $menu_moves_sum[0]->in_menu_qty;
            // }

            // $before_qty = (int) $before_m_menu - (int) $before_s_menu;

           

            ksort($moves_array);

            $json = array();
            $html = "";
            // $colspan = 5;
            // $this->make->sRow(array('class'=>'tdhd'));
            //     $this->make->sTd(array('colspan'=>$colspan));
            //         $this->make->span("Quantity on Hand Before ".sql2Date($fromD));
            //     $this->make->eTd();
            //     $this->make->td("");
            //     $this->make->td(num($before_qty),array('class'=>'text-right','style'=>'border-right:5px solid #fff !important;'));
            //     $c = "";

            // $this->make->eRow();

            $this->session->set_userdata('moves_array',$moves_array);


            if(count($moves_array) > 0){
                $ctr = 1;
                $last = count($moves_array);
                // $curr = $before_qty;
                foreach ($moves_array as $dt =>$value) {

                    $args2 = array();
                    // if(isset($post['menu-search']) && $post['menu-search'] != ""){
                        // $args2['menu_moves.item_id'] = $post['menu-search'];
                        $args2['menu_moves.item_id'] = $dt;
                    // }
                    // if(isset($post['calendar_range']) && $post['calendar_range'] != ""){
                        // $daterange = $post['calendar_range'];
                        // $dates = explode(" to ",$daterange);
                        // $from = date2SqlDateTime($dates[0]);
                        // $to = date2SqlDateTime($dates[1]);
                    $fromD = date2Sql($dates[0]);
                    $args2["DATE(menu_moves.reg_date)  < '".$fromD."'"] = array('use'=>'where','val'=>null,'third'=>false);
                        // $args3["DATE(trans_sales.datetime)  < '".$fromD."'"] = array('use'=>'where','val'=>null,'third'=>false);
                        // $args4["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
                    // }
                    $menu_moves_sum = $this->items_model->get_menu_moves_total(null,$args2);

                    $before_m_menu = 0;
                    if($menu_moves_sum){
                        $before_m_menu = $menu_moves_sum[0]->in_menu_qty;
                    }

                    $before_qty = (int) $before_m_menu;

                    $curr = $before_qty;

                    $colspan = 5;

                    $where = array('menu_id'=>$dt);
                    $menudet = $this->site_model->get_details($where,'menus');

                    $this->make->sRow(array('class'=>'tdhd'));
                        $this->make->sTd(array('colspan'=>$colspan));
                            $this->make->span($menudet[0]->menu_name." Quantity on Hand Before ".sql2Date($fromD));
                        $this->make->eTd();
                        $this->make->td("");
                        $this->make->td(num($before_qty),array('class'=>'text-right','style'=>'border-right:5px solid #fff !important;'));
                        $c = "";
                        // if($res->it_per_pack > 0){
                        //     $c = $res->curr_item_qty/$res->it_per_pack;
                        // }    
                        // $this->make->td(num($c)." ".$res->it_per_pack_uom,array('class'=>'text-right'));
                    $this->make->eRow();


                    foreach ($value as $key => $res) {
                    

                        $this->make->sRow();
                            $this->make->td(ucwords(strtolower($res['particular'])));
                            $this->make->td($res['trans_ref']);
                            $this->make->td(sql2Date($res['reg_date'])." ".toTimeW($res['reg_date']));
                            $this->make->td("[".$res['code']."] ".ucwords(strtolower($res['name'])));
                            // $this->make->td($res->uom,array('class'=>'text-center'));
                            $in = "0";
                            $out = "0";
                            if($res['qty'] >= 0){
                                $in = $res['qty'];
                                $curr += $res['qty'];
                            }
                            else{
                                $out = $res['qty'];
                                $curr += $res['qty'];
                            }

                            $this->make->td($in,array('class'=>'text-right'));
                            $this->make->td($out * -1,array('class'=>'text-right'));
                            // $this->make->td(num($res->curr_item_qty),array('class'=>'text-right','style'=>'border-right:1px solid #fff;'));
                            $this->make->td($curr,array('class'=>'text-right','style'=>'border-right:1px solid #fff;'));

                            
                        
                        $this->make->eRow();

                    }

                }
            }
            $json['html'] = $this->make->code();
            
            echo json_encode($json);
        }

    public function menu_moves_pdf()
    {
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Item Inventory Movements Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_code = $this->input->post("branch_id");
        $setup = $this->setup_model->get_details($branch_code);
        $set = $setup[0];
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $set->branch_name, $set->address);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        // $this->menu_model->db = $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);                

        $pdf->Write(0, 'Item Inventory Movements Report', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);      
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Write(0, 'Report Period:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $daterange, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(200);
        $pdf->Write(0, 'Report Generated:    '.(new \DateTime())->format('Y-m-d H:i:s'), '', 0, 'L', true, 0, false, false, 0);
        // $pdf->Write(0, 'Transaction Time:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(200);
        $user = $this->session->userdata('user');
        $pdf->Write(0, 'Generated by:    '.$user["full_name"], '', 0, 'L', true, 0, false, false, 0);        
        $pdf->ln(1);      
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln();              

        // echo "<pre>", print_r($trans), "</pre>";die();
        $pdf->SetFont('helvetica', 'B', 10);
        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(30, 0, 'Description', 'B', 0, 'L');        
        $pdf->Cell(40, 0, 'Reference #', 'B', 0, 'L');        
        $pdf->Cell(50, 0, 'Trans Date', 'B', 0, 'L');                
        $pdf->Cell(87, 0, 'Item Name', 'B', 0, 'L');        
        // $pdf->Cell(30, 0, 'Branch', 'B', 0, 'L');        
        // $pdf->Cell(15, 0, 'UOM', 'B', 0, 'L');        
        $pdf->Cell(20, 0, 'Qty In', 'B', 0, 'R');        
        $pdf->Cell(20, 0, 'Qty Out', 'B', 0, 'R');        
        $pdf->Cell(20, 0, 'QOH', 'B', 0, 'R');        
        // $pdf->Cell(15, 0, 'UOM', 'B', 0, 'R');        
        // $pdf->Cell(15, 0, 'Qty In', 'B', 0, 'R');        
        // $pdf->Cell(15, 0, 'Qty Out', 'B', 0, 'R');        
        // $pdf->Cell(15, 0, 'QOH', 'B', 0, 'R');        
        $pdf->ln();

        $moves_array = $this->session->userdata('moves_array');

        if(count($moves_array) > 0){
            $ctr = 1;
            $last = count($moves_array);
            // $curr = $before_qty;
            foreach ($moves_array as $dt =>$value) {

                $args2 = array();
                // if(isset($post['menu-search']) && $post['menu-search'] != ""){
                    // $args2['menu_moves.item_id'] = $post['menu-search'];
                    $args2['menu_moves.item_id'] = $dt;
                // }
                // if(isset($post['calendar_range']) && $post['calendar_range'] != ""){
                    // $daterange = $post['calendar_range'];
                    // $dates = explode(" to ",$daterange);
                    // $from = date2SqlDateTime($dates[0]);
                    // $to = date2SqlDateTime($dates[1]);
                $fromD = date2Sql($dates[0]);
                $args2["DATE(menu_moves.reg_date)  < '".$fromD."'"] = array('use'=>'where','val'=>null,'third'=>false);
                    // $args3["DATE(trans_sales.datetime)  < '".$fromD."'"] = array('use'=>'where','val'=>null,'third'=>false);
                    // $args4["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
                // }
                $menu_moves_sum = $this->items_model->get_menu_moves_total(null,$args2);

                $before_m_menu = 0;
                if($menu_moves_sum){
                    $before_m_menu = $menu_moves_sum[0]->in_menu_qty;
                }

                $before_qty = (int) $before_m_menu;

                $curr = $before_qty;

                $colspan = 5;

                $where = array('menu_id'=>$dt);
                $menudet = $this->site_model->get_details($where,'menus');

                // $this->make->sRow(array('class'=>'tdhd'));
                //     $this->make->sTd(array('colspan'=>$colspan));
                //         $this->make->span($menudet[0]->menu_name." Quantity on Hand Before ".sql2Date($fromD));
                //     $this->make->eTd();
                //     $this->make->td("");
                //     $this->make->td(num($before_qty),array('class'=>'text-right','style'=>'border-right:5px solid #fff !important;'));
                //     $c = "";
                //     // if($res->it_per_pack > 0){
                //     //     $c = $res->curr_item_qty/$res->it_per_pack;
                //     // }    
                //     // $this->make->td(num($c)." ".$res->it_per_pack_uom,array('class'=>'text-right'));
                // $this->make->eRow();


                $pdf->cell(218,0,$menudet[0]->menu_name." Quantity on Hand Before ".sql2Date($fromD),'B',0,'L'); 
                $pdf->cell(50,0,$before_qty,'B',0,'R'); 
                $pdf->ln();

                foreach ($value as $key => $res) {
                

                    // $this->make->sRow();
                        $cell_border = array(
                           'R' => array('width' => 0.2),
                           'B' => array('width' => 0.2)
                        );

                        $pdf->cell(30,0,ucwords(strtolower($res['particular'])),$cell_border,0,'L');
                        $pdf->cell(40,0,$res['trans_ref'],$cell_border,0,'L');
                        $pdf->cell(50,0,sql2Date($res['reg_date'])." ".toTimeW($res['reg_date']),$cell_border,0,'L');
                        $pdf->cell(87,0,"[".$res['code']."] ".ucwords(strtolower($res['name'])),$cell_border,0,'L');
                        $in = "0";
                        $out = "0";
                        if($res['qty'] >= 0){
                            $in = $res['qty'];
                            $curr += $res['qty'];
                        }
                        else{
                            $out = $res['qty'];
                            $curr += $res['qty'];
                        }
                        $pdf->cell(20,0,$in,$cell_border,0,'R');
                        $pdf->cell(20,0,$out * -1,$cell_border,0,'R');
                        $pdf->cell(20,0,$curr,$cell_border,0,'R');

                        $pdf->ln();

                        // $this->make->td(ucwords(strtolower($res['particular'])));
                        // $this->make->td($res['trans_ref']);
                        // $this->make->td(sql2Date($res['reg_date'])." ".toTimeW($res['reg_date']));
                        // $this->make->td("[".$res['code']."] ".ucwords(strtolower($res['name'])));
                        // $this->make->td($res->uom,array('class'=>'text-center'));

                        // $this->make->td($in,array('class'=>'text-right'));
                        // $this->make->td($out * -1,array('class'=>'text-right'));
                        // // $this->make->td(num($res->curr_item_qty),array('class'=>'text-right','style'=>'border-right:1px solid #fff;'));
                        // $this->make->td($curr,array('class'=>'text-right','style'=>'border-right:1px solid #fff;'));

                        
                    
                    // $this->make->eRow();

                }
                $pdf->ln();

            }
        }

        $json = array();
        $html = "";
        $pdf->SetFont('helvetica', '', 10);
        // if(count($items) > 0){
        //     $ctr = 1;
        //     $last = count($items);
        //     $colspan = 5;
        //     $curr = $before_m_item;
        //     foreach ($items as $res) {
        //         if($ctr == 1){
        //             $pdf->Cell(190, 0, 'Quantity on Hand Before '.sql2Date($from).' '.toTimeW($from), 'B', 0, 'L');  
        //             $pdf->Cell(17, 0, $before_m_item, 'B', 0, 'R');  
        //             $c = "";
        //             if($res->it_per_pack > 0){
        //                 $c = $before_m_item/$res->it_per_pack;
        //             } 
        //             $pdf->Cell(60, 0, $c, 'B', 0, 'R');
        //         }
        //         $ctr++;
        //         $pdf->ln();
        //         $pdf->SetFont('helvetica', '', 9);
        //         // $this->make->sRow();
        //         $cell_border = array(
        //            'R' => array('width' => 0.2),
        //            'B' => array('width' => 0.2)
        //         );
        //         $pdf->Cell(22, 0, ucwords(strtolower($res->particular)), $cell_border, 0, 'L'); 
        //         $pdf->Cell(20, 0, $res->trans_ref, $cell_border, 0, 'L'); 
        //         $pdf->Cell(35, 0,sql2Date($res->reg_date)." ".toTimeW($res->reg_date), $cell_border, 0, 'L');
        //         $width = 40;
        //         $font_size = 9;
        //         $height = 0;
        //         do {
        //             $font_size -= 0.5;
        //             $string_font_width = $pdf->GetStringWidth($res->name,'helvetica','',$font_size) + 3; # +4 Not exactly sure but works
        //             $height = 4.2;
        //         } while ($string_font_width > $width);

        //         $pdf->SetFont('helvetica','',$font_size);
        //         $pdf->cell(40,$height,$res->name,$cell_border,0,'L'); 
        //         $pdf->SetFont('helvetica','',9);
        //         $font_size = 9;
        //         do {
        //             $font_size -= 0.5;
        //             $string_font_width = $pdf->GetStringWidth($res->branch_code,'helvetica','',$font_size) + 3; # +4 Not exactly sure but works
        //             $height = 4.2;
        //         } while ($string_font_width > $width);
        //         $pdf->SetFont('helvetica','',$font_size);
        //         $pdf->Cell(30, $height, $res->branch_code, $cell_border, 0, 'L'); 
        //         $pdf->SetFont('helvetica','',9);
        //         $pdf->Cell(15, 0, $res->uom, $cell_border, 0, 'C'); 
        //         $in = "";
        //         $out = "";
        //         if($res->qty >= 0){
        //             $in = $res->qty;
        //             $curr += $res->qty;
        //             $out = "";
        //         }
        //         else{
        //             $out = $res->qty;
        //             $curr += $res->qty;
        //             $out = $out * -1;
        //         }

        //         $pdf->Cell(15, 0, $in, $cell_border, 0, 'R'); 
        //         $pdf->Cell(15, 0, $out, $cell_border, 0, 'R'); 
        //         $pdf->Cell(15, 0, $curr, $cell_border, 0, 'R'); 
        //         $pdf->Cell(15, 0, $res->it_per_pack_uom, $cell_border, 0, 'C'); 
        //         $in = "";
        //         $out = "";
        //         $curr2 = "";
        //         if($res->it_per_pack > 0){
        //             $curr2 = $curr/$res->it_per_pack;
        //             if($res->qty >= 0){
        //                 $val = $res->qty/$res->it_per_pack;
        //                 $in = num($val);
        //             }
        //             else{
        //                 $val = $res->qty/$res->it_per_pack;
        //                 $out = num($val);
        //             }
        //         }
        //         $pdf->Cell(15, 0, $in, $cell_border, 0, 'R'); 
        //         $pdf->Cell(15, 0, $out, $cell_border, 0, 'R'); 
        //         $pdf->Cell(15, 0, $curr2, $cell_border, 0, 'R');
        //         $ctr++;
        //     }
        //     $pdf->ln();
        //     $pdf->Cell(190, 0, 'Quantity on Hand After '.sql2Date($res->reg_date).' '.toTimeW($res->reg_date), 'B', 0, 'L');  
        //     $pdf->Cell(17, 0, $curr + $after_m_item, 'B', 0, 'R');  
        //     $c = "";
        //     if($res->it_per_pack > 0){
        //         $c = ($curr + $after_m_item)/$res->it_per_pack;
        //     } 
        //     $pdf->Cell(60, 0, $c, 'B', 0, 'R');
        // }             

       
        // update_load(100); 
        // -----------------------------------------------------------------------------

        //Close and output PDF document
        header_remove('Set-Cookie');

        $pdf->Output('item_inventory_moves_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+  
    }

     public function get_recipe_total(){
        $menu_id = $this->input->post('menu_id');
        $recipe_det = $this->items_model->get_recipe_items($menu_id);
        $total = 0;
        foreach ($recipe_det as $val) {
            $total += ($val->item_cost * $val->qty);
        }
        echo json_encode(array('total'=>num($total)));
    }
    public function recipe_load($item_id=null){
        $det = $this->items_model->get_item_recipe_items($item_id);
        $data['code'] = itemRecipeLoad($item_id,null,$det);
        $data['load_js'] = 'dine/items.php';
        $data['use_js'] = 'recipeLoadJs';
        $this->load->view('load',$data);
    }
    public function recipe_item_details($item_id=null){
        $this->load->model('dine/items_model');
        $items = $this->items_model->get_item($item_id);
        $item = $items[0];
        $det['cost'] = $item->item_cost;
        $det['uom'] = $item->uom;
        echo json_encode($det);
    }
    public function recipe_details_db(){
        $items = array(
            'menu_id' => $this->input->post('menu-id-hid'),
            'item_id' => $this->input->post('item-id-hid'),
            'uom' => $this->input->post('item-uom-hid'),
            'qty' => $this->input->post('qty'),
            'cost' => $this->input->post('item-cost')
        );
        $recipe_det = $this->items_model->get_recipe_items($items['menu_id'],$items['item_id']);
        if (count($recipe_det) > 0) {
            $det = $recipe_det[0];
            $this->items_model->update_recipe_item($items,$items['menu_id'],$items['item_id']);
            $id = $det->recipe_id;
            $item_name = $det->item_name;
            $act = "update";
            $msg = "Updated item: ".$item_name;
        } else {
            $this->load->model('dine/items_model');
            $detx = $this->items_model->get_item($items['item_id']);
            $detx = $detx[0];

            $item_name = $detx->name;
            $id = $this->items_model->add_recipe_item($items);
            $act = "add";
            $msg = "Add new item: ".$this->input->post('item-search');
        }

        $this->make->sRow(array('id'=>'row-'.$id));
            $this->make->td($item_name);
            $this->make->td($items['uom']);
            $this->make->td($items['cost']);
            $this->make->td($items['qty']);
            $this->make->td($items['qty'] * $items['cost']);
            $a = $this->make->A(fa('fa-trash-o fa-fw fa-lg'),'#',array('id'=>'del-'.$id,'ref'=>$id,'class'=>'del-item','return'=>true));
            $this->make->td($a);
        $this->make->eRow();
        $row = $this->make->code();

        echo json_encode(array('id'=>$id,'row'=>$row,'msg'=>$msg,'act'=>$act));
    }
    public function remove_recipe_item(){
        $recipe_id = $this->input->post('recipe_id');
        $this->items_model->remove_recipe_item($recipe_id);
        $json['msg'] = "Recipe Item Deleted.";
        echo json_encode($json);
    }

}