<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/cashier.php");
class App_get extends Cashier {
	var $data = null;
    public function app_rows($tbl=null,$primary_col=null){
        $order = 'asc';
        if($this->input->post('order'))
            $order = $this->input->post('order');
        $limit = null;
        if($this->input->post('limit'))
            $order = $this->input->post('limit');

        $result = $this->site_model->get_tbl($tbl,array(),array($primary_col=>'asc'),null,false,'*',null,$limit);  
        $res = $result->result();
        $cols = $result->list_fields();
        $rows = array();
        
        if(count($res) > 0 ){
            foreach ($res as $r) {
                $row = array();
                foreach ($cols as $col) {
                    $row[$col] = $r->$col;
                }
                $rows[$r->$primary_col] = $row;
            }
        }
        $col_str = "";
        $val_str = "";
        foreach ($cols as $col) {
            $col_str .= "`".$col."`,";
            $val_str .= "?,";
        }
        $col_str = substr($col_str,0,-1);
        $val_str = substr($val_str,0,-1);

        $this->output->set_header("Pragma: no-cache");
        $this->output->set_header("Access-Control-Allow-Origin: *");
        $this->output->set_header("Access-Control-Expose-Headers: Access-Control-Allow-Origin");
        $this->output->set_status_header(200);
        $this->output->set_content_type('application/json');
        
        ob_start();
        echo json_encode(array('rows'=>$rows,'cols'=>$cols,'colstr'=>$col_str,'valstr'=>$val_str,''));
    }
    public function app_users($username=null,$password=null){
        $args = array();
        $joinTables = array();
        $args['username'] = $username;
        $args['password'] = md5($password);
        $joinTables['user_roles'] = array('content'=>'users.role = user_roles.id');
        $select = 'users.fname,users.mname,users.lname,users.suffix,users.id as user_id,users.username,users.role,users.email,user_roles.role as role_name';
        $result = $this->site_model->get_tbl('users',$args,array(),$joinTables,false,$select);
        $res = $result->result();
        $cols = $result->list_fields();
        $row = array();
        $user_id = "";
        if(count($res) > 0){
            foreach ($res as $r) {
                $user_id = $r->user_id;
                foreach ($cols as $col) {
                    $row[$col] = $r->$col;   
                }
            }
            $img = "";
            $image = $this->site_model->get_image(null,$user_id,'users');
            if(count($image) > 0){
                $path = $image[0]->img_path;
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $img = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            $row['img'] = $img;
        }
        $this->output->set_header("Pragma: no-cache");
        $this->output->set_header("Access-Control-Allow-Origin: *");
        $this->output->set_header("Access-Control-Expose-Headers: Access-Control-Allow-Origin");
        $this->output->set_status_header(200);
        $this->output->set_content_type('application/json');
        ob_start();
        echo json_encode(array('user_id'=>$user_id,'row'=>$row));
    }
    public function app_save_order(){
        $msg = "";
        $stringData = $this->input->post('string');
        // $stringData = '{"trans_sales":[{"sales_id":4,"type_id":10,"trans_ref":null,"void_ref":null,"type":"dinein","user_id":1,"shift_id":null,"terminal_id":1,"customer_id":null,"total_amount":1815,"total_paid":0,"memo":null,"table_id":null,"guest":1,"datetime":"2015-11-06 13:32:31","update_date":null,"paid":0,"reason":null,"void_user_id":null,"printed":0,"waiter_id":1,"split":0,"sent":0,"inactive":0}],"trans_sales_local_tax":[{"sales_local_tax_id":4,"sales_id":4,"amount":0}],"trans_sales_menus":[{"sales_menu_id":9,"sales_id":4,"line_id":1,"menu_id":1,"price":100,"qty":1,"discount":null,"no_tax":0,"remarks":"","kitchen_slip_printed":0},{"sales_menu_id":10,"sales_id":4,"line_id":2,"menu_id":2,"price":1290,"qty":1,"discount":null,"no_tax":0,"remarks":"","kitchen_slip_printed":0},{"sales_menu_id":11,"sales_id":4,"line_id":3,"menu_id":3,"price":300,"qty":1,"discount":null,"no_tax":0,"remarks":"","kitchen_slip_printed":0},{"sales_menu_id":12,"sales_id":4,"line_id":4,"menu_id":5,"price":50,"qty":1,"discount":null,"no_tax":0,"remarks":"","kitchen_slip_printed":0},{"sales_menu_id":13,"sales_id":4,"line_id":5,"menu_id":68,"price":75,"qty":1,"discount":null,"no_tax":0,"remarks":"","kitchen_slip_printed":0}],"trans_sales_no_tax":[{"sales_no_tax_id":4,"sales_id":4,"amount":0}],"trans_sales_tax":[{"sales_tax_id":4,"sales_id":4,"name":"VAT","rate":12,"amount":194.4642857142857}]}';
        $data = json_decode($stringData);
        $mobile_id = "";
        $update = false;
        $sales_id = "";
        #### MOBILE ID GET #### 
            foreach ($data as $table_name => $row) {
                if($table_name == "trans_sales"){
                   foreach ($row as $ctr => $val) {
                       foreach ($val as $col_name => $col_value) {
                            if($col_name == 'sales_id'){
                                $mobile_id = $col_value;
                                break;
                            }
                       }
                   }
                   ###########
                }
            }
            $mobile_row = $this->site_model->get_tbl('trans_sales',array('mobile_sales_id'=>$mobile_id));    
            if(count($mobile_row) > 0){
                $update = true;
                foreach ($mobile_row as $res) {
                    $sales_id = $res->sales_id;
                }
            }
        #### MOBILE ID END ####
        #### INSERT AND GET SALES ID
            $sales_order = array();
            foreach ($data as $table_name => $row) {
               if($table_name == "trans_sales"){
                    foreach ($row as $ctr => $val) {
                        foreach ($val as $col_name => $col_val) {
                            if($col_name != "sales_id"){
                                $sales_order[$col_name] = $col_val;
                            }
                        }
                    }
               }
               ################ 
            }
            unset($sales_order['sent']);
            $sales_order['mobile_sales_id'] = $mobile_id;
            if(count($sales_order) > 0){
                if($update){
                    $this->site_model->update_tbl('trans_sales','sales_id',$sales_order,$sales_id);
                    $this->cashier_model->delete_trans_sales_menus($sales_id);
                    $this->cashier_model->delete_trans_sales_items($sales_id);
                    $this->cashier_model->delete_trans_sales_menu_modifiers($sales_id);
                    $this->cashier_model->delete_trans_sales_discounts($sales_id);
                    $this->cashier_model->delete_trans_sales_charges($sales_id);
                    $this->cashier_model->delete_trans_sales_tax($sales_id);
                    $this->cashier_model->delete_trans_sales_no_tax($sales_id);
                    $this->cashier_model->delete_trans_sales_zero_rated($sales_id);
                    $this->cashier_model->delete_trans_sales_local_tax($sales_id);
                }
                else{
                    $sales_id = $this->site_model->add_tbl('trans_sales',$sales_order); 
                }
            } 
        #### INSERT AND GET SALES ID
        if($sales_id != ""){
            $inserts = array();
            foreach ($data as $table_name => $row) {
                if($table_name != "trans_sales"){
                    $ins = array();
                    foreach ($row as $ctr => $val) {
                        $ctr = 1;
                        foreach ($val as $col_name => $col_val) {
                            if($ctr > 1){
                                if($col_name == 'sales_id'){
                                    $ins[$col_name] = $sales_id;
                                }
                                else{
                                    $ins[$col_name] = $col_val;
                                }
                            }
                            $ctr++;
                        }
                        $inserts[$table_name][] = $ins;
                    }
                    ########
                }
            }    
            foreach ($inserts as $tbl => $row) {
                $this->site_model->add_tbl_batch($tbl,$row);
            }
            $this->print_sales_receipt($sales_id,false);
            $this->print_os($sales_id);
        }        
        // echo var_dump($update);
        $this->output->set_header("Pragma: no-cache");
        $this->output->set_header("Access-Control-Allow-Origin: *");
        $this->output->set_header("Access-Control-Expose-Headers: Access-Control-Allow-Origin");
        $this->output->set_status_header(200);
        // $this->output->set_content_type('application/json');
        ob_start();   
        // echo json_encode(array('msg'=>$msg));     
    }
    public function app_get_open_orders(){
        $args = array(
            // "trans_sales.trans_ref"=>null,
            "trans_sales.terminal_id"=>TERMINAL_ID,
            "trans_sales.type_id"=>SALES_TRANS,
            "trans_sales.mobile_sales_id != "=>"",
            // "trans_sales.inactive"=>0,
        );
        $limit = null;
        $sales = $this->site_model->get_tbl('trans_sales',$args,array(),null,false,'*',null,$limit);
        $res = $sales->result();
        $cols = $sales->list_fields();

        $sales_ids = array();
        $mobile_ids = array();
        $mb = array();
        $trans = array();
        foreach ($res as $r) {
            $row = array();
            foreach ($cols as $col) {
                $row[$col] = $r->$col;
            }
            $trans[$r->mobile_sales_id] = $row;
            $sales_ids[] = $r->sales_id;
            $mobile_ids[] = $r->mobile_sales_id;
            $mb[$r->sales_id] = $r->mobile_sales_id;
        }

        $details = array();
        if(count($sales_ids) > 0){
            $tbl['trans_sales_charges'] = $this->cashier_model->get_trans_sales_charges(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_items'] = $this->cashier_model->get_trans_sales_items(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_discounts'] = $this->cashier_model->get_trans_sales_discounts(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_menu_modifiers'] = $this->cashier_model->get_trans_sales_menu_modifiers(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_menus'] = $this->cashier_model->get_trans_sales_menus(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_no_tax'] = $this->cashier_model->get_trans_sales_no_tax(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_payments'] = $this->cashier_model->get_trans_sales_payments(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_tax'] = $this->cashier_model->get_trans_sales_tax(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_zero_rated'] = $this->cashier_model->get_trans_sales_zero_rated(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_local_tax'] = $this->cashier_model->get_trans_sales_local_tax(null,array('sales_id'=>$sales_ids));
            foreach ($tbl as $table => $row) {
                $cl = $this->site_model->get_tbl_cols($table);
                $dets = array();
                foreach ($row as $r) {
                    $det = array();
                    foreach ($cl as $c) {
                        if($c == 'sales_id'){
                            $det['sales_id'] = $mb[$r->$c];                        
                        }
                        else{
                            $det[$c] = $r->$c;                        
                        }
                    }
                    reset($det);
                    $key = key($det);
                    unset($det[$key]);
                    $dets[] = $det;
                }####
                $details[$table]=$dets;
            }
        }

        echo json_encode(array('trans'=>$trans,'details'=>$details,'mobile_ids'=>$mobile_ids));
    }
    public function app_cancel_order(){
        $stringData = $this->input->post('string');
        $data = json_decode($stringData);
        $mobile_sales_id = $data[3];
        $items = array(
            'reason' => $data[0],
            'void_user_id' => $data[1],
            'inactive' => $data[2]
        );
        $error = "";
        $args['mobile_sales_id'] = $mobile_sales_id;
        $sales = $this->site_model->get_tbl('trans_sales',$args,array(),null,true,'sales_id');
        if(count($sales) > 0){
            $sales_id = $sales[0]->sales_id;
            $this->site_model->update_tbl('trans_sales','mobile_sales_id',$items,$mobile_sales_id);
            $print = $this->print_sales_receipt($sales_id,false);
            $this->print_os($sales_id,false);
        }
        else{
            $error = "Transaction not found. Cannot Cancel Order";
        }
    }
}