<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller {
    
	//-----------Branch Details-----start-----allyn
	public function details(){
        $this->load->model('dine/setup_model');
        $this->load->model('dine/cashier_model');
        $this->load->helper('dine/setup_helper');
        $details = $this->setup_model->get_details(1);
		$det = $details[0];
        $set = $this->cashier_model->get_pos_settings();
        $splashes = $this->site_model->get_image(null,null,'splash_images');

        $data = $this->syter->spawn('setup');
        $data['page_subtitle'] = 'Edit Branch Setup';
        $data['code'] = makeDetailsForm($det,$set,$splashes);
        // $data['add_js'] = array('js/plugins/timepicker/bootstrap-timepicker.min.js');
        // $data['add_css'] = array('css/timepicker/bootstrap-timepicker.min.css');
		$data['load_js'] = 'dine/setup.php';
		$data['use_js'] = 'detailsJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function upload_splash_images(){
        $this->load->helper('dine/setup_helper');
        $this->load->model('dine/settings_model');
        // $data['code'] = makeTableUploadForm($branch);
        
        $data['code'] = makeImageUploadForm();
        $data['load_js'] = 'dine/setup.php';
        $data['use_js'] = 'uploadSplashImagePopJs';
        $this->load->view('load',$data);
    }
    public function delete_splash_img($img_id=null){
        $error = "";
        $splashes = $this->site_model->get_image($img_id,null,'splash_images');
        if($img_id != ""){
            $this->site_model->delete_tbl('images',array('img_id'=>$img_id));
            unlink($splashes[0]->img_path);
        }
        else{
            $error = "No Image selected";
        }
        echo json_encode(array('error'=>$error));
    }    
    public function upload_splash_images_db(){
        // $this->load->model('dine/settings_model');
        // $image = null;
        // $upload = 'success';
        // $msg = "";
        // $src ="";
        // if(is_uploaded_file($_FILES['fileUpload']['tmp_name'])) {
        //     $file = file_get_contents($_FILES['fileUpload']['tmp_name']);
        //     $items = array(
        //         "img_blob"=>$file,
        //         "img_tbl"=>'splash_images',
        //     );
        //     $id = $this->site_model->add_tbl('images',$items);
        //     $msg =  "Image uploaded";
        //     site_alert($msg,'success');
        // }
        // else{
        //     $msg =  "Invalid Image";
        // }
        // echo json_encode(array('msg'=>$msg));
        $msg = '';
        $path = "uploads/splash/";
        if (!file_exists($path)) {   
            mkdir($path, 0777, true);
        }
        $file_name = $_FILES['fileUpload']['name'];
        $file_size =$_FILES['fileUpload']['size'];
        $file_tmp =$_FILES['fileUpload']['tmp_name'];
        $file_type=$_FILES['fileUpload']['type'];
        $file_ext=strtolower(end(explode('.',$_FILES['fileUpload']['name'])));
        $expensions= array("jpeg","jpg","png");         
        if(in_array($file_ext,$expensions)=== false){
           $msg = 'extension not allowed, please choose a JPEG or PNG file.';
        }          
        if($msg == ''){
            move_uploaded_file($file_tmp,$path.$file_name);
            $items = array(
                "img_file_name"=>$file_name,
                "img_path"=>$path.$file_name,
                "img_tbl"=>'splash_images',
            );
            $id = $this->site_model->add_tbl('images',$items);
            $msg =  "Image uploaded";
            site_alert($msg,'success');
        }
        echo json_encode(array('msg'=>$msg));
    }
    public function details_db(){
        $this->load->model('dine/setup_model');
        $this->load->model('dine/main_model');

        // $img = '';
        // $img = $_FILES['complogo']['tmp_name'];
            // $img = file_get_contents($tmp_name);
        // if(is_uploaded_file($_FILES['complogo']['tmp_name'])){
        //     $tmp_name = $_FILES['complogo']['tmp_name'];
        //     $img = file_get_contents($tmp_name);
        // }
        // echo 'IMAGE : '.$img;
        $items = array(
            "branch_code"=>$this->input->post('branch_code'),
            "branch_name"=>$this->input->post('branch_name'),
            "branch_desc"=>$this->input->post('branch_desc'),
            "contact_no"=>$this->input->post('contact_no'),
            "delivery_no"=>$this->input->post('delivery_no'),
            "address"=>$this->input->post('address'),
            "tin"=>$this->input->post('tin'),
            "machine_no"=>$this->input->post('machine_no'),
            "bir"=>$this->input->post('bir'),
            "serial"=>$this->input->post('serial'),
            "permit_no"=>$this->input->post('permit_no'),
            // "serial"=>$this->input->post('serial'),
            "accrdn"=>$this->input->post('accrdn'),
            "email"=>$this->input->post('email'),
            "website"=>$this->input->post('website'),
            "store_open" => date("H:i:s",strtotime($this->input->post('store_open'))),
            "store_close" => date("H:i:s",strtotime($this->input->post('store_close'))),
            // "rob_path" => $this->input->post('rob_path'),
            // "rob_username" => $this->input->post('rob_username'),
            // "rob_password" => $this->input->post('rob_password'),
            // "img"=>$img
            // "currency"=>$this->input->post('currency')
        );

            $this->setup_model->update_details($items, 1);
            $this->main_model->update_tbl('branch_details','branch_id',$items,1);
            // $id = $this->input->post('cat_id');
            $act = 'update';
            $msg = 'Updated Branch Details';

        echo json_encode(array('msg'=>$msg));
    }
    public function pos_settings_db(){
        $this->load->model('dine/setup_model');
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/main_model');
        $ctrl = "";
        foreach($this->input->post('chk') as $val){
            $ctrl .= $val.','; 
        }

        $ctrl = substr($ctrl, 0, -1);
        //echo $ctrl;

        $items = array(
            "no_of_receipt_print" => (int)$this->input->post('no_of_receipt_print'),
            "no_of_order_slip_print" => (int)$this->input->post('no_of_order_slip_print'),
            "kitchen_printer_name" => $this->input->post('kitchen_printer_name'),
            "kitchen_printer_name_no" => (int)$this->input->post('kitchen_printer_name_no'),
            "kitchen_beverage_printer_name" => $this->input->post('kitchen_beverage_printer_name'),
            "kitchen_beverage_printer_name_no" => (int)$this->input->post('kitchen_beverage_printer_name_no'),
            "open_drawer_printer" => $this->input->post('open_drawer_printer'),
            "local_tax" => $this->input->post('local_tax'),
            "controls"=> $ctrl,
            "loyalty_for_amount" => $this->input->post('loyalty_for_amount'),
            "loyalty_to_points" => $this->input->post('loyalty_to_points'),
        );
        $this->cashier_model->update_pos_settings($items, 1);
        $this->main_model->update_tbl('settings','id',$items,1);
        $act = 'update';
        $msg = 'Updated Branch Details';

        $items2 = array(
            "rec_footer"=>str_replace ("\r\n", "<br>", $this->input->post('rec_footer')),
            "pos_footer"=>str_replace ("\r\n", "<br>", $this->input->post('pos_footer')),
        );
        $this->setup_model->update_details($items2, 1);
        $this->main_model->update_tbl('branch_details','branch_id',$items2,1);

        echo json_encode(array('msg'=>$msg));
    }
    public function pos_database_db(){
        $this->load->model('dine/setup_model');
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/main_model');
        $items = array(
            "backup_path" => $this->input->post('backup_path'),
        );
        $this->cashier_model->update_pos_settings($items, 1);
        $this->main_model->update_tbl('settings','id',$items,1);
        $act = 'update';
        $msg = 'Updated Database Details';
        echo json_encode(array('msg'=>$msg));
    }
    public function download_backup_db(){
        $this->load->model('dine/cashier_model');
        $set = $this->cashier_model->get_pos_settings();
        $backup_folder = "C:/xampp/htdocs/dine/backup";
        if(iSetObj($set,'backup_path')){
            $backup_folder = iSetObj($set,'backup_path');
        }
        if (!file_exists($backup_folder)) { 
            $backup_folder = "C:/xampp/htdocs/dine/backup";
        }    
        $file_path = $backup_folder."/main";
        if (!file_exists($file_path)) {   
            mkdir($file_path, 0777, true);
        }
        $fileB = "main_db.sql";
        $this->db = $this->load->database('main', TRUE);        
        $this->load->dbutil();
        $prefs = array(
            "format" => 'txt',
            'ignore' => array('ci_sessions','logs')
        );
        $backup =& $this->dbutil->backup($prefs); 
        $this->db = $this->load->database('default', TRUE);     
        $this->load->helper('file');
        write_file($file_path.'/'.$fileB, $backup);
        if(file_exists($file_path.'/'.$fileB)){
            site_alert("Backed Up Successfully",'success');
            $this->load->helper('download');
            force_download('main_db.sql', $backup);
        }
        else{
            site_alert("Back Up failed",'error');
        }
        // redirect(base_url()."setup/details",'refresh');
        header("Location:".base_url()."setup/details");
        // header("Location:".base_url()."shift");
    }
	//-----------Branch Details-----end-----allyn

    //jed
    public function brands(){
        $this->load->model('dine/setup_model');
        $this->load->helper('dine/setup_helper');
        $this->load->helper('site/site_forms_helper');
        $data = $this->syter->spawn('general_settings');
        $data['page_title'] = fa('icon-doc')." Branches";
        // $data['page_subtitle'] = 'Item Category Management';
        $th = array('Code','Name','Branch','');
        $data['code'] = create_rtable('brands','id','brands-tbl',$th);
        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/wowdash.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js' ,'js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');
        $data['load_js'] = 'dine/setup.php';
        $data['use_js'] = 'brandJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }

    public function get_brands($id=null,$asJson=true){
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
        // if($this->input->post('name')){
        //     $lk  =$this->input->post('name');
        //     $args["(categories.name like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        // }
        // if($this->input->post('inactive')){
        //     $args['categories.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));
        // }
        // if($this->input->post('branch_id')){
        //     // echo $this->input->post('branch_id');die();
        //     $args['categories.branch_code'] = array('use'=>'where','val'=>$this->input->post('branch_id'));
        // }
        // $args['receipt_discounts.is_item_disc'] = array('use'=>'where','val'=>"0");
        // $join = null;
        $join["branch_details"] = array('content'=>"branch_details.branch_code = brands.branch_code");
        $count = $this->site_model->get_tbl('brands',$args,array(),$join,true,'brands.*',null,null,true);
        $page = paginate('setup/get_brands',$count,$total_rows,$pagi);
        $char = $this->site_model->get_tbl('brands',$args,array(),$join,true,'brands.*,branch_details.branch_name',null,$page['limit']);
        // echo "<pre>",print_r($disc),"</pre>";die();
        $json = array();
        if(count($char) > 0){
            $ids = array();
            foreach ($char as $res) {
                
                $link = $this->make->A(fa('fa-edit fa-lg').' Edit','#',array('class'=>'btn btn-outline-success-600 radius-8 px-20 py-11 align-items-center gap-2','id'=>'edit-'.$res->id.'-'.$res->branch_code,'ref'=>$res->id,'ref2'=>$res->branch_code,'return'=>'true'));

                

                $json[$res->id.'-'.$res->branch_code] = array(
                    "id"=>$res->brand_code,   
                    "title"=>ucwords(strtolower($res->brand_name)),
                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),   
                    "branch"=>$res->branch_name,
                    // "charge_amount"=>$charge_amount,
                    // "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),
                    "link"=>$link
                );
                // $ids[] = $res->disc_id;
            }
        }
         // echo "<pre>",print_r($json),"</pre>";die();
        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    }

    public function brands_form($ref=null,$branch=null){
        $this->load->helper('dine/setup_helper');
        $this->load->model('dine/setup_model');
        $data = $this->syter->spawn('general_spettings');
        $data['page_title'] = fa('icon-doc')." Branch";
        $brands = array();
        if($ref != null){
            $brands = $this->setup_model->get_brands($ref);
            $brands = $brands[0];
        }
        $data['code'] = makeBrandsForm($brands,$branch);
        $data['add_css'] = array('js/plugins/typeaheadmap/typeaheadmap.css','css/wowdash.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js' ,'js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js');
        $data['load_js'] = 'dine/setup.php';
        $data['use_js'] = 'brandformJs';
        $this->load->view('page',$data);
    }
    public function brands_db()
    {
        $this->load->model('dine/setup_model');
        $this->load->model('dine/main_model');
        $this->load->model('dine/menu_model');
        $branch_code = $this->input->post('b_code');
        $id = $this->input->post('id');
        $branches = $this->menu_model->get_all_branch();
        foreach ($branches as $value) {
            $b_id = $this->setup_model->get_last_brands($value->branch_code);
            $items[] = array(
                "id"=>$b_id+1,
                "brand_code"=>$this->input->post('brand_code'),
                "brand_name"=>$this->input->post('brand_name'),
                // "charge_amount"=>$this->input->post('charge_amount'),
                // "no_tax"=>(int)$this->input->post('no_tax'),
                // "absolute"=>(int)$this->input->post('absolute'),
                "branch_code"=>$value->branch_code,
                // "is_item_disc"=>"0",
                "inactive"=>1
            );
        }
        if($this->input->post('id')){
            unset($items[0]['id'],$items[0]['branch_code']);
            $inact = 0;
            if($this->input->post('inactive')){
                $inact = $this->input->post('inactive');
            }
            $inactive = array(
                "inactive"=>$inact,
            );
            if(!empty($branch_code)){
                foreach ($branch_code as $value) {
                    $this->setup_model->update_brands($items[0], $this->input->post('id'),$value);
                    $this->setup_model->update_brands($inactive, $this->input->post('id'),$value);
                    // echo "<pre>",print_r($inactive),"</pre>";die();
                }
            }
            else{
                $msg = 'Error! Please select branch. ';
                site_alert($msg,'error');
            }
            $id = $this->input->post('id');
            $act = 'update';
            $msg = 'Updated Brands: '.$items[0]['brand_name'];
            site_alert($msg,'success');
            // $this->main_model->update_tbl('receipt_discounts','disc_id',$items,$id);
        }else{
            $id = $this->setup_model->add_bulk_brands($items);
            $inactive = array(
                "inactive"=>0,
            );
            if(!empty($branch_code)){
                foreach ($branch_code as $value) {
                // echo "<pre>",print_r($value),"</pre>";die();
                   $check = $this->setup_model->update_brands($inactive,$items[0]['id'],$value);
                }
            }
            else{
                foreach ($branches as $value) {
                   $check = $this->setup_model->update_brands($inactive,$items[0]['id'],$value->branch_code);
                }
            // echo "<pre>",print_r($check),"</pre>";die();
            }

            $act = 'add';
            $msg = 'Added New Brands: '.$items[0]['brand_name'];
            site_alert($msg,'success');
            // $this->main_model->add_trans_tbl('receipt_discounts',$items);
        }
        echo json_encode(array("id"=>$id,"desc"=>$items[0]['brand_name'],"act"=>$act,'msg'=>$msg));
    }
}