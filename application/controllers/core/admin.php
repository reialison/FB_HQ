<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	var $data = null;
    public function roles(){
        $this->load->model('core/admin_model');
        $this->load->helper('site/site_forms_helper');
        $role_list = $this->admin_model->get_user_roles();
        $data = $this->syter->spawn('roles');
        $data['page_title'] = fa('icon-user').' User Roles';
        $data['code'] = site_list_form("admin/roles_form","roles_form","Roles List",$role_list,array('role'),"id");
        $data['add_js'] = 'js/site_list_forms.js';
        $data['add_css'] = 'css/wowdash.css';
        $this->load->view('page',$data);
    }
    public function roles_form($ref=null){
        $this->load->helper('core/admin_helper');
        $this->load->model('core/admin_model');
        $role = array();
        $access = array();
        if($ref != null){
            $roles = $this->admin_model->get_user_roles($ref);
            $role = $roles[0];
            $access = explode(',',$role->access);
        }
        $navs = $this->syter->get_navs();
        $this->data['code'] = rolesForm($role,$access,$navs);
        $this->data['load_js'] = 'site/admin';
        $this->data['use_js'] = 'rolesJs';
        $this->load->view('load',$this->data);
    }
    public function roles_db(){
        $this->load->model('core/admin_model');
        $links = $this->input->post('roles');
        $role = $this->input->post('role');
        $desc = $this->input->post('description');
        $access = "";
        foreach ($links as $li) {
            $access .= $li.",";
        }
        $access = substr($access,0,-1);
        $items = array(
            "role"=>$role,
            "description"=>$desc,
            "access"=>$access
        );
        if($this->input->post('role_id')){
            $this->admin_model->update_user_roles($items,$this->input->post('role_id'));
            $id = $this->input->post('role_id');
            $act = 'update';
            $msg = 'Updated role '.$role;
        }
        else{
            $id = $this->admin_model->add_user_roles($items);
            $act = 'add';
            $msg = 'Added  new role '.$role;   
        }
        echo json_encode(array("id"=>$id,"desc"=>$role,"act"=>$act,'msg'=>$msg));
    }
    public function restart_down(){
        $this->load->helper('core/admin_helper');
        $data = $this->syter->spawn('restart');
        $data['page_title'] = fa('fa-refresh')." Restart POS";
        $data['code'] = restartPage();
        $data['load_js'] = 'site/admin.php';
        $data['use_js'] = 'restartJs';
        $this->load->view('page',$data);
    }
    public function go_restart_down(){
        $this->load->model('core/admin_model');
        $this->admin_model->restart();
        // $this->db = $this->load->database('main', TRUE);
        // $this->admin_model->restart();
        session_start();
        unset($_SESSION['load']);
        unset($_SESSION['problem']);
        $this->session->sess_destroy();
        
    }

    public function admin_master_restart(){
        $this->load->model('core/admin_model');
        $restart = $this->admin_model->master_restart();
        var_dump($restart);
    }

    public function import_file(){
        $this->load->helper('core/admin_helper');
        $this->load->model('core/admin_model');
       
        // $this->data['code'] = '';importForm();
        // $this->data['load_js'] = 'site/admin';
        // $this->data['use_js'] = 'rolesJs';
        // $this->load->view('load',$this->data);

        // $this->load->model('core/admin_model');
        // $this->load->helper('site/site_forms_helper');
        
        $this->data = $this->syter->spawn('roles');
        $this->data['page_title'] = fa('icon-doc').' Import File';
        $this->data['code'] = importForm();
        $this->data['load_js'] = 'site/admin';
        $this->data['use_js'] = 'uploadFormJs';
        $this->load->view('page',$this->data);
    }

    public function upload_file(){
        // $lines = file('update-001.sql');

        if ($_FILES['fileUpload']['error']!=UPLOAD_ERR_OK)
        {
            $msg = lang('items_excel_import_failed');
            // echo json_encode( array('success'=>false,'message'=>$msg) );
            echo json_encode(array("act"=>'error','msg'=>$msg));
            return;
        }
        else
        {   

            $allowed_extensions = array('sql');
            $extension = strtolower(pathinfo($_FILES["fileUpload"]["name"], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowed_extensions))
            {
                    echo json_encode(array("act"=>'error','msg'=>'File extension is invalid.'));

                    return;
            }

            if (($lines = file($_FILES['fileUpload']['tmp_name'])) !== FALSE)
            { 
                $counter = 0;
                foreach($lines as $line) {
                    // Skip it if it's a comment
                    if (substr($line, 0, 2) == '--' || $line == '')
                        continue;

                    // Add this line to the current templine we are creating
                    // $templine.=$line;

                    // If it has a semicolon at the end, it's the end of the query so can process this templine
                    if (substr(trim($line), -1, 1) == ';') {
                        // Perform the query
                        $this->db->query($line);
                        // Reset temp variable to empty
                        $templine = '';
                    }

                    if($counter % 100 == 0){
                        set_time_limit(60);  
                    }

                    $counter++;
                }

            }

            $msg = 'Data successfully uploaded';
            site_alert($msg,'success');
            echo json_encode(array("act"=>'success','msg'=>$msg));
        }
        
    }

    public function export_data()
    {
        $this->load->helper('core/admin_helper');
        $this->load->model('core/admin_model');
        
        $data = $this->syter->spawn('export');        
        $data['page_title'] = fa('icon-doc').' Export Data';
        $data['code'] = exportForm();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css','assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','assets/global/plugins/select2/js/select2.full.min.js','assets/pages/scripts/components-select2.min.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'site/admin';
        $data['use_js'] = 'exportJS';
        $this->load->view('page',$data);
    }

    function download_trans_for_pos(){
        $this->load->model('core/admin_model');

        // $branch = $this->input->get('branch');
        $branch = $this->input->post('branch');
        // $file_name = 'sales/tran_sales_upto_'. date('m_d_Y').'.sql';
        $where = array('branch_code'=>$branch);
        $ter = $this->site_model->get_details($where,'terminals');

        $folder_path = "export";
   
        // List of name of files inside
        // specified folder
        $files = glob($folder_path.'/*'); 
           
        // Deleting all the files in the list
        foreach($files as $file) {
           
            if(is_file($file)) 
            
                // Delete the given file
                unlink($file); 
        }

        $files = array();

        // foreach($ter as $arr_id => $vals){

        //     $file_name = 'export/'.$vals->branch_code.'_'.$vals->terminal_code.'.sql';
            // echo $file_name;

            // $fp = fopen($file_name, "w+");

            // fwrite($fp,$vals->branch_code);
            // fclose($fp);
            // $tables = array('trans_sales','trans_sales_charges','trans_sales_discounts','trans_sales_items',
            //             'trans_sales_local_tax','trans_sales_loyalty_points','trans_sales_menus',
            //             'trans_sales_menu_submodifiers','trans_sales_menu_modifiers','trans_sales_no_tax','trans_sales_payments',
            //             'trans_sales_tax','trans_sales_zero_rated'
            //             ,'trans_gc','trans_gc_charges','trans_gc_discounts',
            //             'trans_gc_local_tax','trans_gc_loyalty_points','trans_gc_gift_cards',
            //             'trans_gc_no_tax','trans_gc_payments',
            //             'trans_gc_tax','trans_gc_zero_rated','menus','menu_categories','menu_modifiers','menu_prices','menu_recipe','menu_schedules','menu_subcategories','menu_subcategory','modifier_group_details',
            //             'modifier_groups','modifier_prices','modifier_recipe','modifier_sub','modifier_sub_prices','modifiers','terminals','items','categories','subcategories','item_moves','trans_refs','users','receipt_discounts','charges','brands','transaction_types'
            //         );
        
        //     $tables = array('trans_sales','trans_sales_charges','trans_sales_discounts','trans_sales_items',
        //                 'trans_sales_local_tax','trans_sales_loyalty_points','trans_sales_menus',
        //                 'trans_sales_menu_submodifiers','trans_sales_menu_modifiers','trans_sales_no_tax','trans_sales_payments',
        //                 'trans_sales_tax','trans_sales_zero_rated',
        //                 'menus','menu_categories','menu_modifiers','menu_prices','menu_recipe','menu_schedules','menu_subcategories','menu_subcategory','modifier_group_details',
        //                 'modifier_groups','modifier_prices','modifier_recipe','modifier_sub','modifier_sub_prices','modifiers','terminals','items','categories','subcategories','item_moves','trans_refs','users','receipt_discounts','charges','brands','transaction_types','payment_types','payment_type_fields','payment_group'
        //             );

        //     $return = $this->admin_model->download_trans_for_pos($tables,$vals->branch_code,$vals->terminal_id);

        //     $new_file = fopen($file_name, 'w+');
        //     fwrite($new_file, $return);
        //     fclose($new_file);

        // }

        $tables = array('trans_sales','trans_sales_charges','trans_sales_discounts','trans_sales_items',
                        'trans_sales_local_tax','trans_sales_loyalty_points','trans_sales_menus',
                        'trans_sales_menu_submodifiers','trans_sales_menu_modifiers','trans_sales_no_tax','trans_sales_payments',
                        'trans_sales_tax','trans_sales_zero_rated',
                        'menus','menu_categories','menu_modifiers','menu_prices','menu_recipe','menu_schedules','menu_subcategories','menu_subcategory','modifier_group_details',
                        'modifier_groups','modifier_prices','modifier_recipe','modifier_sub','modifier_sub_prices','modifiers','terminals','items','categories','subcategories','item_moves','trans_refs','users','receipt_discounts','charges','brands','transaction_types','payment_types','payment_type_fields','payment_group'
                    );

        $return = $this->admin_model->download_trans_for_pos($tables,$branch);
        $file_name = 'export/'.$branch.'_main.sql';
        $new_file = fopen($file_name, 'w+');
        fwrite($new_file, $return);
        fclose($new_file);

        echo json_encode(array('file'=>$file_name));

        site_alert('Generation successful!','success');

        // $salefile = fopen("sales/sales.txt", "w+") or die("Unable to open file!");
        // while ($line = fgets($salefile)) {
        //     if(file_exists($line)){
        //         unlink(trim($line));
        //     }
        // }
        // fwrite($salefile, $file_name);

        // fclose($salefile);

        // $tables = array('trans_sales','trans_sales_charges','trans_sales_discounts','trans_sales_items',
        //                 'trans_sales_local_tax','trans_sales_loyalty_points','trans_sales_menu_modifiers',
        //                 'trans_sales_menu_submodifiers','trans_sales_menus','trans_sales_no_tax','trans_sales_payments',
        //                 'trans_sales_tax','trans_sales_zero_rated'
        //                 ,'trans_gc','trans_gc_charges','trans_gc_discounts',
        //                 'trans_gc_local_tax','trans_gc_loyalty_points','trans_gc_gift_cards',
        //                 'trans_gc_no_tax','trans_gc_payments',
        //                 'trans_gc_tax','trans_gc_zero_rated'
        //             );
        // // $tables = $tables = array('trans_sales');
        // $return = $this->master_model->download_trans_for_hq($tables);

        // // print_r($return);exit;
        
        // $new_file = fopen($file_name, 'w+');
        // fwrite($new_file, $return);
        // fclose($new_file);
        
        // header("Location:" . base_url() . $file_name);
    }
}