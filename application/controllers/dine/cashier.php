<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/reads.php");
class Cashier extends Reads {
    public function __construct(){
        parent::__construct();
        $this->load->model('dine/cashier_model');
        $this->load->helper('core/string_helper');
        $this->load->model('site/site_model');

        // if(LOCALSYNC){
            // $this->load->model('core/sync_model');
        // }
        // $this->load->helper('dine/cpanel_helper');
    }
    #CONTROL PANEL SECTION
        public function index(){
            $this->load->helper('dine/cashier_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null);
            sess_clear('trans_mod_cart');
            sess_clear('trans_cart');
            sess_clear('counter');
            sess_clear('trans_disc_cart');
            sess_clear('trans_charge_cart');
            sess_clear('trans_type_cart');
            // $time = $this->site_model->get_db_now();
            // $prev_date = date('Y-m-d', strtotime($time .' -1 day'));
            // $result = $this->cashier_model->get_latest_read_date(Z_READ);
            // $need_eod = false;
            // if(!empty($result)){
            //     if(strtotime($prev_date) > strtotime($result->maxi)){
            //         $need_eod = true;
            //     }
            // }
            $set = $this->cashier_model->get_pos_settings();
            $today = date2Word($this->site_model->get_db_now('sql'));
            $data['code'] = indexPage(false,$set,$today);
            $data['add_css'] = array('css/cashier.css','css/onscrkeys.css','css/virtual_keyboard.css');
            $data['add_js'] = array('js/on_screen_keys.js','js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'controlPanelJs';
            $this->load->view('cashier',$data);
        }
        public function search_panel(){
            $this->load->helper('dine/cashier_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null);
            sess_clear('trans_mod_cart');
            sess_clear('trans_cart');
            sess_clear('counter');
            sess_clear('trans_disc_cart');
            sess_clear('trans_charge_cart');
            $set = $this->cashier_model->get_pos_settings();
            $today = sql2Date($this->site_model->get_db_now('sql'));
            $data['code'] = searchPanelPage(false,$set,$today);
            $data['add_css'] = array('css/cashier.css','css/onscrkeys.css','css/virtual_keyboard.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
            $data['add_js'] = array('js/on_screen_keys.js','js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'searchPanelJs';
            $this->load->view('cashier',$data);
        }
        public function index2(){
            $this->load->helper('dine/cashier_helper');
            $this->load->helper('dine/cpanel_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null);
            $today = sql2Date($this->site_model->get_db_now('sql'));
            $user = $data['user'];
            $data['add_css'] = array('css/cashier.css','css/onscrkeys.css','css/cpanel.css');
            $set = $this->cashier_model->get_pos_settings();
            $data['code'] = cpanel($user,$set,$today);
            $data['load_js'] = 'dine/cpanel';
            $data['use_js'] = 'cpanelJs';
            $data['noNavbar'] = true;
            $this->load->view('cashier',$data);
        }
        function guest_edit_call(){
            $this->load->helper('dine/manager_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null,false);
            $data['code'] = onScrNumPadOnly('guest-call-pin-login');
            $data['add_css'] = array('css/pos.css','css/onscrkeys.css');
            $data['add_js'] = array('js/on_screen_keys.js');
            $this->load->view('load',$data);
        }
        function server_no_call(){
            $this->load->helper('dine/manager_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null,false);
            $data['code'] = onScrNumPadOnly('server-no-pin');
            $data['add_css'] = array('css/pos.css','css/onscrkeys.css');
            $data['add_js'] = array('js/on_screen_keys.js');
            $this->load->view('load',$data);
        }
        function server_no_call_go($no=0){
            $trans_type_cart = sess('trans_type_cart');
            $cart = $trans_type_cart[0];
            $cart['serve_no'] = $no;
            $trans_type_cart[0] = $cart;
            $this->session->set_userdata('trans_type_cart',$trans_type_cart);
        }
        function guest_edit_call_go($no=0){
            $trans_type_cart = sess('trans_type_cart');
            $cart = $trans_type_cart[0];
            $cart['guest'] = $no;
            $trans_type_cart[0] = $cart;
            $this->session->set_userdata('trans_type_cart',$trans_type_cart);
        }
        function manager_call(){
            $this->load->helper('dine/manager_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null,false);
            $data['code'] = onScrNumPwdPadOnly('manager-call-pin-login');
            $data['add_css'] = array('css/pos.css','css/onscrkeys.css');
            $data['add_js'] = array('js/on_screen_keys.js');
            $this->load->view('load',$data);
        }
        function manager_reasons(){
            $this->load->helper('dine/manager_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null,false);
            $data['code'] = managerReasonsPage();
            $data['add_css'] = array('css/pos.css','css/onscrkeys.css', 'css/cashier.css');
            $data['add_js'] = array('js/on_screen_keys.js');
            $data['load_js'] = 'dine/manager';
            $data['use_js'] = 'managerReasonsJs';
            $this->load->view('load',$data);
        }
        function manager_go_login() {
            $this->load->model('dine/manager_model');
            $pin = MD5($this->input->post('pin'));
            $manager = $this->manager_model->get_manager_by_pin($pin);
            $man = array();
            if (!isset($manager->id)) {
                echo json_encode(array('error_msg'=>'Invalid manager pin','manager'=>$man));
            } else {
                $this->session->set_userdata('manager_privs',array('method'=>'page','id'=>$manager->id));
                $man = array(
                    "manager_id"=>$manager->id,
                    "manager_username"=>$manager->username
                );
                echo json_encode(array('success_msg'=>'Go','manager'=>$man));
            }

            // return false;
        }
        public function _remap($method,$params=array()){
            $this->load->model('dine/clock_model');
                $user = $this->session->userdata('user');
                $user_id = $user['role_id'];
                if(ORDERING_STATION){
                    if($method == 'settle'){
                        $now = $this->site_model->get_db_now();
                        $user = $this->session->userdata('user');
                        $user_id = $user['id'];
                        $shift = $this->clock_model->get_curr_shift(date2Sql($now),$user_id);
                        if(count($shift) > 0){
                            call_user_func_array(array($this,$method), $params);
                        }
                        else{
                            site_alert('You need to start a shift before selling.','error');
                            header("Location:".base_url()."shift");
                        }
                    }
                    else{
                        call_user_func_array(array($this,$method), $params);  
                    }   
                    return false;         
                }
                $high_position = array(1,2);
                if(!CHECK_OIC_ID){
                    $high_position[] = OIC_ID;
                }
                // if($user_id == 1 || $user_id == 2){
                if(in_array($user_id,$high_position)){
                    if($method == 'counter' || $method == 'tables' || $method == 'delivery' ){
                        $now = $this->site_model->get_db_now();
                        $user = $this->session->userdata('user');
                        $user_id = $user['id'];
                        $shift = $this->clock_model->get_curr_shift(date2Sql($now),$user_id);
                        if(count($shift) > 0){
                            call_user_func_array(array($this,$method), $params);
                        }
                        else{
                            site_alert('You need to start a shift before selling.','error');
                            header("Location:".base_url()."shift");
                        }   
                    }
                    else if($method == 'settle'){
                        $now = $this->site_model->get_db_now();
                        $user = $this->session->userdata('user');
                        $user_id = $user['id'];
                        $shift = $this->clock_model->get_curr_shift(date2Sql($now),$user_id);
                        if(count($shift) > 0){
                            call_user_func_array(array($this,$method), $params);
                        }
                        else{
                            site_alert('You need to start a shift before you can settle.','error');
                            header("Location:".base_url()."shift");
                        }
                    }
                    else                
                        call_user_func_array(array($this,$method), $params);
                }
                else{
                    if($method != 'manager_call' && $method != 'manager_go_login' && $method != 'manager_reasons' && $method != 'app_save_order' && $method != 'app_rows' && $method != 'app_users' && $method != 'app_get_open_orders' && $method != 'app_cancel_order' && $method != 'counter2'){
                        $now = $this->site_model->get_db_now();
                        $user = $this->session->userdata('user');
                        $user_id = $user['id'];
                        $shift = $this->clock_model->get_curr_shift(date2Sql($now),$user_id);
                        if(count($shift) > 0){
                            if($method != 'index' && $method != 'summary' && $method != 'summary_orders' && $method != 'end_shift' && $method != 'read_shift_sales' && $method != 'end_day'){
                                $time = $this->site_model->get_db_now();
                                $yesterday = date('Y-m-d',strtotime($time. "-1 days"));
                                $unclosed_shifts = $this->clock_model->get_shift_id(null,$user_id,$yesterday);
                                $error = "";
                                call_user_func_array(array($this,$method), $params);
                            }
                            else{
                                call_user_func_array(array($this,$method), $params);
                            }
                        }
                        else{
                            // site_alert('You need to start a shift before selling.','error');
                            header("Location:".base_url()."shift");
                        }

                    }
                    else{
                        call_user_func_array(array($this,$method), $params);
                    }
                }
        }
        public function orders($terminal='my',$status='open',$types='all',$now='all_trans',$search_id='none',$server_id='0',$show='box'){
            $this->load->model('dine/cashier_model');
            $this->load->model('site/site_model');
                // "trans_sales.terminal_id"=>TERMINAL_ID,
            $args = array(
                "trans_sales.trans_ref"=>null,
                "trans_sales.type_id"=>SALES_TRANS,
                "trans_sales.inactive"=>0,
            );
            if($now != 'all_trans'){
                // echo "here";
                $date = $this->site_model->get_db_now(null,true);
                // $args["DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d') <= "] = date('Y-m-d',strtotime($date. "-1 days"));
                $date_from = date('Y-m-d',strtotime($date. "-1 days"));
                $date_to = $date;
                $args["DATE(trans_sales.datetime)  BETWEEN DATE('".$date_from."') AND DATE('".$date_to."')"] = array('use'=>'where','val'=>null,'third'=>false);
            }
            else{
                $this->db = $this->load->database('main',true);
            }
            // if($terminal != 'my')
            //     unset($args["trans_sales.terminal_id"]);
            if($status != 'open'){
                unset($args["trans_sales.trans_ref"]);
                if($status == 'settled'){
                    $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                    $args["trans_sales.inactive"] = 0;
                }else if($status == 'cancel'){
                    $args["trans_sales.trans_ref  IS NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                    $args["trans_sales.inactive"] = 1;
                }else{
                    $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                     $args["trans_sales.inactive"] = 1;
                }
            }
            if($types != 'all'){
                $args["trans_sales.type"] = $types;
            }
            if($search_id != 'none'){
                $args["trans_sales.sales_id"] = array('use'=>'like','val'=>$search_id,'third'=>false);
                if($status == 'settled'){
                    $args["trans_sales.trans_ref"] = array('use'=>'or_like','val'=>$search_id,'third'=>false);
                }
            }
            if($server_id != 0){
                $args["trans_sales.waiter_id"] = $server_id;
            }
            #STOPPED HERE
                  // "trans_sales.terminal_id"=>TERMINAL_ID,
            if($this->input->post())  {
              $args = array(
                  "trans_sales.trans_ref"=>null,
                  "trans_sales.type_id"=>SALES_TRANS,
                  "trans_sales.inactive"=>0,
              );
              if($this->input->post('s_status')){
                switch ($this->input->post('s_status')) {
                    case 'settled':
                        unset($args["trans_sales.trans_ref"]);
                        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                        $args["trans_sales.inactive"] = 0;
                        break;
                    case 'void':
                        unset($args["trans_sales.trans_ref"]);
                        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                        $args["trans_sales.inactive"] = 1;
                        break;
                    case 'cancel':
                        unset($args["trans_sales.trans_ref"]);
                        $args["trans_sales.trans_ref IS NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                        $args["trans_sales.inactive"] = 1;
                    default:
                        break;
                }
              }
              if($this->input->post('s_order_type')){
                $args["trans_sales.type"] = $this->input->post('s_order_type');
              }
              if($this->input->post('s_ref_code')){
                $args["trans_sales.sales_id"] = array('use'=>'like','val'=>$this->input->post('s_ref_code'),'third'=>false);
              }
              if($this->input->post('s_rec_code')){
                $args["trans_sales.trans_ref"] = array('use'=>'like','val'=>$this->input->post('s_rec_code'),'third'=>false);
              }
              if($this->input->post('s_from_amt')){
                $args["trans_sales.total_amount >= "] = $this->input->post('s_from_amt');
              }
              if($this->input->post('s_to_amt')){
                $args["trans_sales.total_amount <= "] = $this->input->post('s_to_amt');
              }
              if($this->input->post('s_order_date')){
                $date_today = date2Sql($this->site_model->get_db_now(null,true));
                $orD = date2Sql($this->input->post('s_order_date'));
                if(strtotime($orD) >= strtotime($date_today)){
                    $date_from = date('Y-m-d',strtotime($orD. "-1 days"));
                    $date_to = $orD;
                    $args["DATE(trans_sales.datetime)  BETWEEN DATE('".$date_from."') AND DATE('".$date_to."')"] = array('use'=>'where','val'=>null,'third'=>false);
                }
                else{
                    $this->db = $this->load->database('main',true);
                    $date_from = date('Y-m-d',strtotime($orD. "-1 days"));
                    $date_to = $orD;
                    $args["DATE(trans_sales.datetime)  BETWEEN DATE('".$date_from."') AND DATE('".$date_to."')"] = array('use'=>'where','val'=>null,'third'=>false);
                }
              }
            }
            $orders = $this->cashier_model->get_trans_sales(null,$args);
            // echo $this->cashier_model->db->last_query();
            // return false;
            $code = "";
            $ids = array();
            $time = $this->site_model->get_db_now();
            $this->make->sDivRow();
            $ord=array();
            $combine_cart = sess('trans_combine_cart');
            foreach ($orders as $res) {
                if($res->trans_ref == null and $res->inactive == 0){
                    $status = "open";
                }else if($res->trans_ref != null and $res->inactive == 0){
                    $status = "settled";
                }else{
                    $status = "voided";
                }
                $ord[$res->sales_id] = array(
                    "type"=>$res->type,
                    "status"=>$status,
                    "user_id"=>$res->user_id,
                    "name"=>$res->username,
                    "terminal_id"=>$res->terminal_id,
                    "terminal_name"=>$res->terminal_name,
                    "shift_id"=>$res->shift_id,
                    "datetime"=>$res->datetime,
                    "amount"=>$res->total_amount,
                    "table_id"=>$res->table_id
                );
                if($show == "box"){
                    $this->make->sDivCol(6,'left',0);
                        $this->make->sDiv(array('class'=>'order-btn','id'=>'order-btn-'.$res->sales_id,'ref'=>$res->sales_id));
                            if($res->trans_ref == null and $res->inactive == 0){
                                $this->make->sBox('default',array('class'=>'box-solid'));
                            }else if($res->trans_ref != null and $res->inactive == 0){
                                $this->make->sBox('default',array('class'=>'box-solid bg-green'));
                            }
                            else if($res->trans_ref != null and $res->inactive == 1){
                                $this->make->sBox('default',array('class'=>'box-solid','style'=>'background-color: #ed4959;'));
                            }
                            else{
                                $this->make->sBox('default',array('class'=>'box-solid','style'=>'background-color: #EE7600;'));
                            }
                                $this->make->sBoxBody();
                                    $this->make->sDivRow();
                                        $this->make->sDivCol(6);
                                            $splitTxt = '';
                                            if($res->split != 0){
                                                if($res->sales_id == $res->split){
                                                    $splitTxt = fa('fa-code fa-lg fa-fw');
                                                }
                                                else{
                                                    $splitTxt = '(From ORDER #'.$res->split.')';
                                                }
                                            }
                                            $this->make->H(5,"ORDER #".$res->sales_id." ".$splitTxt,array("style"=>'font-weight:700;'));
                                            if($res->trans_ref == null and $res->inactive == 0){
                                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#888'));
                                                $this->make->H(6,strtoupper($res->terminal_name),array("style"=>'color:#888'));
                                            }else if($res->trans_ref != null and $res->inactive == 0){
                                                // $this->make->H(5,$res->trans_ref,array("style"=>'color:#fff'));
                                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#fff'));
                                                // $this->make->H(6,'FS - '.strtoupper($res->waiterfname." ".$res->waitermname." ".$res->waiterlname." ".$res->waitersuffix),array("style"=>'color:#fff'));
                                                $this->make->H(6,strtoupper($res->terminal_name),array("style"=>'color:#fff'));
                                            }else{
                                                // $this->make->H(5,$res->trans_ref,array("style"=>'color:#fff'));
                                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#fff'));
                                                // $this->make->H(6,'FS - '.strtoupper($res->waiterfname." ".$res->waitermname." ".$res->waiterlname." ".$res->waitersuffix),array("style"=>'color:#fff'));
                                                $this->make->H(6,strtoupper($res->terminal_name),array("style"=>'color:#fff'));
                                            }
                                            if($res->reason != null)
                                                $this->make->H(6,'('.ucwords($res->reason).')',array("style"=>'color:#fff'));
                                            $this->make->H(5,tagWord(strtoupper(ago($res->datetime,$time) ) ) );
                                        $this->make->eDivCol();
                                        $this->make->sDivCol(6);
                                            if($res->trans_ref != "")
                                                $this->make->H(4,'#'.$res->trans_ref,array('class'=>'text-center','style'=>'font-weight:bold;text-shadow: 1px 3px 5px rgba(0, 0, 0, 0.5);-webkit-font-smoothing: antialiased !important;opacity: 0.8;'));
                                            else
                                                $this->make->H(4,'Order Total',array('class'=>'text-center'));
                                            $this->make->H(3,num($res->total_amount),array('class'=>'text-center'));
                                            $tbl_n = "";
                                            if($res->table_id != ""){
                                                $tbl_n = " - ".strtoupper($res->table_name);
                                            }
                                            $serve_no = "";
                                            if($res->type == "counter" || $res->type == 'takeout'){
                                                if($res->serve_no > 0){
                                                    $serve_no = " - SERVE #".$res->serve_no;
                                                }
                                            }
                                            $this->make->H(5,strtoupper($res->type).$tbl_n.$serve_no,array('class'=>'text-center','style'=>'font-weight:bold;text-shadow: 1px 3px 5px rgba(0, 0, 0, 0.5);-webkit-font-smoothing: antialiased !important;opacity: 0.8;'));
                                        $this->make->eDivCol();
                                    $this->make->eDivRow();

                                $this->make->eBoxBody();
                            $this->make->eBox();
                        $this->make->eDiv();
                    $this->make->eDivCol();
                }
                else if($show=='combineList'){
                    $got = false;
                    if(count($combine_cart) > 0){
                        foreach ($combine_cart as $key => $co) {
                            if($co['sales_id'] == $res->sales_id){
                                $got = true;
                                break;
                            }
                        }
                    }
                    if(!$got){
                        $this->make->sDivRow(array('class'=>'orders-list-div-btnish','id'=>'order-btnish-'.$res->sales_id));
                            $this->make->sDivCol(4);
                                $this->make->sDiv(array('style'=>'margin-left:10px;'));
                                    $this->make->H(5,strtoupper($res->type)." #".$res->sales_id,array("style"=>'font-weight:700;'));
                                    $tbl_n = "";
                                    if($res->table_id != ""){
                                        $tbl_n = strtoupper($res->table_name);
                                        $this->make->H(5,$tbl_n,array('style'=>'font-weight:bold;text-shadow: 1px 3px 5px rgba(0, 0, 0, 0.5);-webkit-font-smoothing: antialiased !important;opacity: 0.8;')); 
                                    }
                                    $this->make->H(5,strtoupper($res->username),array("style"=>'color:#888'));
                                    $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#888'));
                                $this->make->eDiv();
                            $this->make->eDivCol();
                            $this->make->sDivCol(4);
                                $this->make->sDiv(array('style'=>'margin-left:10px;'));
                                    $this->make->H(4,'BALANCE DUE',array('class'=>'text-center'));
                                    $this->make->H(3,num($res->total_amount),array('class'=>'text-center','style'=>'margin-top:10px;'));
                                $this->make->eDiv();
                            $this->make->eDivCol();
                            $this->make->sDivCol(4);
                                $this->make->sDiv(array('class'=>'order-btn-right-container','style'=>'margin-left:10px;margin-right:10px;margin-top:15px;'));
                                    $this->make->button(fa('fa-angle-double-right fa-lg fa-fw'),array('id'=>'add-to-btn-'.$res->sales_id,'ref'=>$res->sales_id,'class'=>'add-btn-row btn-block counter-btn-green'));
                                $this->make->eDiv();
                            $this->make->eDivCol();
                        $this->make->eDivRow();
                    }
                }
                $ids[] = $res->sales_id;
            }
            //}
            $this->make->eDivRow();
            $code = $this->make->code();
            $has = 1;
            if(count($ord) == 0){
                $has = 0;
            }
            echo json_encode(array('code'=>$code,'ids'=>$ord,'has'=>$has));
        }
        public function new_orders($terminal='my',$status='open',$types='all',$now='all_trans',$search_id='none',$server_id='0',$last_id=null){
            $this->load->model('dine/cashier_model');
            $this->load->model('site/site_model');
            $args = array(
                "trans_sales.trans_ref"=>null,
                "trans_sales.terminal_id"=>TERMINAL_ID,
                "trans_sales.type_id"=>SALES_TRANS,
                "trans_sales.inactive"=>0,
            );
            if($now != 'all_trans'){
                $date = $this->site_model->get_db_now(null,true);
                $args["DATE_FORMAT(trans_sales.datetime,'%Y-%m-%d')"] = $date;
            }
            else{
                $this->db = $this->load->database('main',true);
            }
            if($terminal != 'my')
                unset($args["trans_sales.terminal_id"]);
            if($status != 'open'){
                unset($args["trans_sales.trans_ref"]);
                if($status == 'settled'){
                    $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                    $args["trans_sales.inactive"] = 0;
                }else{
                     $args["trans_sales.inactive"] = 1;
                }
            }
            if($types != 'all'){
                $args["trans_sales.type"] = $types;
            }
            if($search_id != 'none'){
                $args["trans_sales.sales_id"] = array('use'=>'like','val'=>$search_id,'third'=>false);;
                if($status == 'settled'){
                    $args["trans_sales.trans_ref"] = array('use'=>'or_like','val'=>$search_id,'third'=>false);
                }
            }
            if($server_id != 0){
                $args["trans_sales.waiter_id"] = $server_id;
            }
            if($last_id != ""){
                $args["trans_sales.sales_id >"] = $last_id;
            }
            $orders = $this->cashier_model->get_trans_sales(null,$args);
            $code = "";
            $ids = array();
            $time = $this->site_model->get_db_now();
            $ord=array();
            $combine_cart = sess('trans_combine_cart');
            foreach ($orders as $res) {
                if($res->trans_ref == null and $res->inactive == 0){
                    $status = "open";
                }else if($res->trans_ref != null and $res->inactive == 0){
                    $status = "settled";
                }else{
                    $status = "voided";
                }
                $ord[$res->sales_id] = array(
                    "type"=>$res->type,
                    "status"=>$status,
                    "user_id"=>$res->user_id,
                    "name"=>$res->username,
                    "terminal_id"=>$res->terminal_id,
                    "terminal_name"=>$res->terminal_name,
                    "shift_id"=>$res->shift_id,
                    "datetime"=>$res->datetime,
                    "amount"=>$res->total_amount
                );
                    $this->make->sDivCol(6,'left',0);
                        $this->make->sDiv(array('class'=>'order-btn','id'=>'order-btn-'.$res->sales_id,'ref'=>$res->sales_id));
                            if($res->trans_ref == null and $res->inactive == 0){
                                $this->make->sBox('default',array('class'=>'box-solid'));
                            }else if($res->trans_ref != null and $res->inactive == 0){
                                $this->make->sBox('default',array('class'=>'box-solid bg-green'));
                            }else{
                                $this->make->sBox('default',array('class'=>'box-solid','style'=>'background-color: #ed4959;'));
                            }
                                $this->make->sBoxBody();
                                    $this->make->sDivRow();
                                        $this->make->sDivCol(6);
                                            $splitTxt = '';
                                            if($res->split != 0){
                                                if($res->sales_id == $res->split){
                                                    $splitTxt = fa('fa-code fa-lg fa-fw');
                                                }
                                                else{
                                                    $splitTxt = '(From ORDER #'.$res->split.')';
                                                }
                                            }
                                            $this->make->H(5,"ORDER #".$res->sales_id." ".$splitTxt,array("style"=>'font-weight:700;'));
                                            if($res->trans_ref == null and $res->inactive == 0){
                                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#888'));
                                                // $this->make->H(6,'FS - '.strtoupper($res->waiterfname." ".$res->waitermname." ".$res->waiterlname." ".$res->waitersuffix),array("style"=>'color:#888'));
                                                $this->make->H(6,strtoupper($res->terminal_name),array("style"=>'color:#888'));
                                            }else if($res->trans_ref != null and $res->inactive == 0){
                                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#fff'));
                                                // $this->make->H(6,'FS - '.strtoupper($res->waiterfname." ".$res->waitermname." ".$res->waiterlname." ".$res->waitersuffix),array("style"=>'color:#fff'));
                                                $this->make->H(6,strtoupper($res->terminal_name),array("style"=>'color:#fff'));
                                            }else{
                                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#fff'));
                                                // $this->make->H(6,'FS - '.strtoupper($res->waiterfname." ".$res->waitermname." ".$res->waiterlname." ".$res->waitersuffix),array("style"=>'color:#fff'));
                                                $this->make->H(6,strtoupper($res->terminal_name),array("style"=>'color:#fff'));
                                            }
                                            if($res->reason != null)
                                                $this->make->H(6,'('.ucwords($res->reason).')',array("style"=>'color:#fff'));
                                            $this->make->H(5,tagWord(strtoupper(ago($res->datetime,$time) ) ) );
                                        $this->make->eDivCol();
                                        $this->make->sDivCol(6);
                                            $this->make->H(4,'Order Total',array('class'=>'text-center'));
                                            $this->make->H(3,num($res->total_amount),array('class'=>'text-center'));
                                            $tbl_n = "";
                                            if($res->table_id != ""){
                                                $tbl_n = " - ".strtoupper($res->table_name);
                                            }
                                            $this->make->H(5,strtoupper($res->type).$tbl_n,array('class'=>'text-center','style'=>'font-weight:bold;text-shadow: 1px 3px 5px rgba(0, 0, 0, 0.5);-webkit-font-smoothing: antialiased !important;opacity: 0.8;'));
                                        $this->make->eDivCol();
                                    $this->make->eDivRow();

                                $this->make->eBoxBody();
                            $this->make->eBox();
                        $this->make->eDiv();
                    $this->make->eDivCol();
                $ids[] = $res->sales_id;
            }
            //}
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'ids'=>$ord));
        }
        public function order_view($sales_id=null,$prev=false){
            if($prev)
                $this->db = $this->load->database('main',true);

            $order = $this->get_order(false,$sales_id);
            $ord = $order['order'];
            $det = $order['details'];
            $discs = $order['discounts'];
            $charges = $order['charges'];
            $zero_rated = $order['zero_rated'];
            $total = 0;
            $totals = $this->total_trans(false,$det,$discs,$charges,$zero_rated);
            $this->make->H(3,strtoupper($ord['type'])." #".$ord['sales_id'],array('class'=>'receipt text-center'));
            $this->make->H(5,sql2DateTime($ord['datetime']),array('class'=>'receipt text-center'));
            $waiter_name = trim($ord['waiter_name']);
            if($waiter_name != "")
                $this->make->H(5,'Food Server: '.$ord['waiter_name'],array('class'=>'receipt text-center'));
            $this->make->append('<hr>');
            $this->make->sDiv(array('class'=>'body'));
                $this->make->sUl();
                    foreach ($det as $menu_id => $opt) {
                        $qty = $this->make->span($opt['qty'],array('class'=>'qty','return'=>true));
                        $name = $this->make->span($opt['name'],array('class'=>'name','return'=>true));
                        $cost = $this->make->span($opt['price'],array('class'=>'cost','return'=>true));
                        $price = $opt['price'];
                        $this->make->li($qty." ".$name." ".$cost);
                        if($opt['remarks'] != ""){
                            $remarks = $this->make->span(fa('fa-text-width').' '.ucwords($opt['remarks']),array('class'=>'name','style'=>'margin-left:36px;','return'=>true));
                            $this->make->li($remarks);
                        }
                        if(isset($opt['modifiers']) && count($opt['modifiers']) > 0){
                            foreach ($opt['modifiers'] as $mod_id => $mod) {
                                $name = $this->make->span($mod['name'],array('class'=>'name','style'=>'margin-left:36px;','return'=>true));
                                $cost = "";
                                if($mod['price'] > 0 )
                                    $cost = $this->make->span($mod['price'],array('class'=>'cost','return'=>true));
                                $this->make->li($name." ".$cost);
                                $price += $mod['price'];
                            }
                        }
                        $total += $opt['qty'] * $price  ;
                    }

                    if(count($charges) > 0){
                        foreach ($charges as $charge_id => $ch) {
                            $qty = $this->make->span(fa('fa fa-tag'),array('class'=>'qty','return'=>true));
                            $name = $this->make->span($ch['name'],array('class'=>'name','return'=>true));
                            $tx = $ch['amount'];
                            if($ch['absolute'] == 0)
                                $tx = $ch['amount']."%";
                            $cost = $this->make->span($tx,array('class'=>'cost','return'=>true));
                            $this->make->li($qty." ".$name." ".$cost);
                        }
                    }

                $this->make->eUl();
            $this->make->eDiv();
            $this->make->append('<hr>');
            $this->make->H(3,'TOTAL: '.num($totals['total']),array('class'=>'receipt text-center'));
            $this->make->H(4,'DISCOUNT: '.num($totals['discount']),array('class'=>'receipt text-center'));
            $code = $this->make->code();
            echo json_encode(array('code'=>$code));
        }
        public function change_order_to($sales_id=null,$old=false){
            $this->load->model('dine/cashier_model');
            $tbl_id = null;
            $type = null;
            $error = '';
            if($this->input->post('type'))
                $type = $this->input->post('type');

            if($this->input->post('tbl_id'))
                $tbl_id = $this->input->post('tbl_id');

            $this->cashier_model->update_trans_sales(array('type'=>$type,'table_id'=>$tbl_id),$sales_id);
            echo json_encode(array('error'=>$error));
        }
        public function other_reason_pop(){
            $this->make->textarea('Type Reason','other-reason-txt');
            echo $this->make->code();    
        }
        public function void_order($sales_id=null,$old=false){
            $this->load->model('dine/cashier_model');
            $this->load->model('dine/items_model');
            $this->load->model('core/trans_model');
            $approver = "";
            $reason = "";
            $error = '';
            if($this->input->post('reason'))
                $reason = $this->input->post('reason');
            if($this->input->post('approver'))
                $approver = $this->input->post('approver');

            $now = $this->site_model->get_db_now();
            $user = $this->session->userdata('user');
            $user_id = $user['id'];
            $shift = $this->clock_model->get_curr_shift(date2Sql($now),$user_id);
            if(count($shift) == 0){
                $error = 'You need to start a shift before voiding.';
                echo json_encode(array('error'=>$error));
                return false;
            }
            if(!$reason || $reason == ""){
                $error = 'Please sight the reason.';
                echo json_encode(array('error'=>$error));
                return false;                
            }
            if($old){
                $this->db = $this->load->database('main',true);
                    $order = $this->get_order(false,$sales_id);
                    $head = $order['order'];
                    $this->cashier_model->update_trans_sales(array('reason'=>$reason,'void_user_id'=>$approver,'inactive'=>1),$sales_id);
                $this->db = $this->load->database('default',true);
                    $trans = $this->load_trans(false,$order,true);
                    $void = $this->submit_trans(false,null,true,$sales_id);
                    // $asJson=true,$submit=null,$void=false,$void_ref=null,$cart=null,$mod_cart=null,$print=false,$split_id=null,$printKitSlip=false
                    $this->finish_trans($void['id'],true,true);
                    $this->cashier_model->update_trans_sales(array('reason'=>$reason,'void_user_id'=>$approver,'inactive'=>1),$sales_id);
                $this->db = $this->load->database('main',true);
                    $print = $this->print_sales_receipt($sales_id,false);
                    $this->print_os($sales_id,false);


                if(LOCALSYNC){
                    $this->sync_model->update_trans_sales($sales_id);
                }


                if(MALL_ENABLED){
                    if(MALL == 'megamall'){
                        $this->sm_file($head['datetime']);
                    }
                }    


            }
            else{
                $order = $this->get_order_header(false,$sales_id);
                $datetime = $order['datetime'];
                if($order['paid'] == 0){
                    $this->cashier_model->update_trans_sales(array('reason'=>$reason,'void_user_id'=>$approver,'inactive'=>1),$sales_id);
                    
                    $print = $this->print_sales_receipt($sales_id,false);
                    if(LOCALSYNC){
                        $this->sync_model->update_trans_sales($sales_id);
                    }
                    $this->print_os($sales_id,false);
                }
                else{
                    $order = $this->get_order(false,$sales_id);
                    $trans = $this->load_trans(false,$order,true);
                    $void = $this->submit_trans(false,null,true,$sales_id);
                    $this->finish_trans($void['id'],true,true);
                    $this->cashier_model->update_trans_sales(array('reason'=>$reason,'void_user_id'=>$approver,'inactive'=>1),$sales_id);
                    $print = $this->print_sales_receipt($sales_id,false);
                    $this->print_os($sales_id,false);
                    if(MALL_ENABLED){
                        if(MALL == 'megamall'){
                            $this->sm_file($datetime);
                        }
                    }
                }

                if(LOCALSYNC){
                    $this->sync_model->update_trans_sales($sales_id);
                }
            }
            echo json_encode(array('error'=>$error));
        }
    #COUNTER TRANSACTIONS SECTION  
        public function counter($type=null,$sales_id=null){
            $this->load->model('site/site_model');
            $this->load->model('dine/cashier_model');
            $this->load->helper('dine/cashier_helper');
            $data = $this->syter->spawn(null);
            $loaded = null;
            $order = array();

            $loc_res = $this->site_model->get_tbl('settings',array(),array(),null,true,'*',null,1);
            $local_tax = $loc_res[0]->local_tax;
            $kitchen_printer = "";
            if(iSetObj($loc_res[0],'kitchen_printer_name') != ""){
                $kitchen_printer = iSetObj($loc_res[0],'kitchen_printer_name');
            }

            if($sales_id != null){
                $order = $this->get_order(false,$sales_id);
                $trans = $this->load_trans(false,$order);
                $time = $trans['datetime'];
                $type = $type." #".$order['order']['sales_id'];
                $loaded = "loaded";
            }
            else{
                $trans = $this->new_trans(false,$type);
                $time = $trans['datetime'];
            }
            if(isset($order['order']))
                $order = $order['order'];
            $typeCN = sess('trans_type_cart');

            if(isset($typeCN[0]['table'])){
                $error = $this->check_tbl_activity($typeCN[0]['table'],false);
                if($error == ""){
                    $this->update_tbl_activity($typeCN[0]['table']);

                    if(LOCALSYNC){

                        $this->sync_model->update_table_activity($typeCN[0]['table']);
                    }
                }
                else{
                    site_alert($error,'error');
                    header("Location:".base_url()."cashier");
                }
            }

            $data['code'] = counterPage($type,$time,$loaded,$order,$typeCN,$local_tax,$kitchen_printer);
            // $data['add_css'] = 'css/cashier.css';
            $data['add_css'] = array('css/virtual_keyboard.css', 'css/cashier.css');
            $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js','js/jquery.scannerdetection.js');
            // $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'counterJs';
            $data['noNavbar'] = true;
            $this->load->view('cashier',$data);
        }
        public function customer_call(){
            // $this->load->helper('dine/manager_helper');
            // $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null,false);
            $data['code'] = "easd";
            // $data['add_css'] = array('css/pos.css','css/onscrkeys.css');
            // $data['add_js'] = array('js/on_screen_keys.js');
            $this->load->view('load',$data);
        }  
        public function food_server_call(){
            // $this->load->helper('dine/manager_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null,false);
            $data['code'] = onScrNumPwdPadOnly('fs-call-pin-login');
            $data['add_css'] = array('css/pos.css','css/onscrkeys.css');
            $data['add_js'] = array('js/on_screen_keys.js');
            $this->load->view('load',$data);
        }
        public function food_server_login() {
            $pin = $this->input->post('pin');
            $employee = $this->site_model->get_tbl('users',array("pin"=>$pin));
            $emp = array();
            if(count($employee) > 0){
                $em = $employee[0];
                $emp = array(
                    "emp_id"=>$em->id,
                    "emp_username"=>$em->username
                );
                echo json_encode(array('success_msg'=>'Go','emp'=>$emp));
            }
            else{
                echo json_encode(array('error_msg'=>'Invalid pin','emp'=>$emp));
            }
        }
        function record_delete_line($cart=null,$id=null,$type=null,$reason=null,$man_id=null,$man_user=null) {
            $this->load->model('dine/cashier_model');
            $counter = sess('counter');
            $reasons = sess('reasons');

            $wagon = $this->session->userData($cart);
            $ar = $wagon[$id];
            $log_user = $this->session->userdata('user');
            $res = array();
            $sales_id = null;
            $time = $this->site_model->get_db_now();
            $res_mod = array();

            if(count($counter['sales_id'])){
                $sales_id = $counter['sales_id'];
            }
            if($type == 'menu'){
                $res = array(
                    'user_id'=>$log_user['id'],
                    'manager_id'=>$man_id,
                    'type'=>$type,
                    'ref_id'=>$ar['menu_id'],
                    'ref_name'=>"line:".$id." - ".$ar['name']." - qty:".$ar['qty'],
                    "reason"=>$reason,
                    "trans_id"=>$sales_id,
                    "datetime"=>date2Sql($time)
                );
                $mods = $this->session->userData('trans_mod_cart');
                foreach ($mods as $line_id => $mod) {
                    if($mod['trans_id'] == $id){
                        $res_mod[] = array(
                            'user_id'=>$log_user['id'],
                            'manager_id'=>$man_id,
                            'type'=>'mod',
                            'ref_id'=>$mod['mod_id'],
                            'ref_name'=>"menu:".$ar['menu_id']." line:".$mod['trans_id']." - ".$mod['name']." - qty:".$ar['qty'],
                            "reason"=>$reason,
                            "trans_id"=>$sales_id,
                            "datetime"=>date2Sql($time)
                        );                        
                    }
                }
            }
            else if($type == 'mod'){
                $res = array(
                    'user_id'=>$log_user['id'],
                    'manager_id'=>$man_id,
                    'type'=>$type,
                    'ref_id'=>$ar['mod_id'],
                    'ref_name'=>"menu:".$ar['menu_id']." line:".$ar['trans_id']." - ".$ar['name']." - qty:".$ar['qty'],
                    "reason"=>$reason,
                    "trans_id"=>$sales_id,
                    "datetime"=>date2Sql($time)
                );
            }
            if(!empty($res)){
                sess_add('reasons',$res);
                if(count($res_mod) > 0){
                    foreach ($res_mod as $rmctr => $md) {
                        sess_add('reasons',$md);
                    }
                }
                // $this->cashier_model->add_reasons($res);
            }
        }
        public function get_branch_details($asJson=true,$branch_code="",$brand=""){
           $this->load->model('dine/setup_model');
           $details = $this->setup_model->get_branch_details($branch_code);
           // echo "<pre>",print_r($details),"</pre>";die();
           $det = array();
           foreach ($details as $res) {
               $det = array(
                        "id"=>$res->branch_id,
                        "code"=>$res->branch_code,
                        "name"=>$res->branch_name,
                        "desc"=>$res->branch_desc,
                        "contact_no"=>$res->contact_no,
                        "delivery_no"=>$res->delivery_no,
                        "address"=>$res->address,
                        "base_location"=>$res->base_location,
                        "currency"=>$res->currency,
                        "tin"=>$res->tin,
                        "machine_no"=>$res->machine_no,
                        "bir"=>$res->bir,
                        "permit_no"=>$res->permit_no,
                        "serial"=>$res->serial,
                        "accrdn"=>$res->accrdn,
                        "email"=>$res->email,
                        "website"=>$res->website,
                        "pos_footer"=>$res->pos_footer,
                        "rec_footer"=>$res->rec_footer,
                        "layout"=>base_url().'uploads/'.$res->image
                      );
           }
           if($asJson)
                echo json_encode($det);
            else
                return $det;
        }
        public function tables($type='dinein'){
            $this->load->model('site/site_model');
            $this->load->model('dine/cashier_model');
            $this->load->helper('dine/cashier_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null);
            sess_clear('trans_type_cart');
            $data['code'] = tablesPage($type);

            $data['add_css'] = array('css/cashier.css','css/onscrkeys.css','css/rtag.css');
            $data['add_js'] = array('js/on_screen_keys.js');
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'tablesJs';
            $data['noNavbar'] = true;
            $this->load->view('cashier',$data);
        }
        function transfer_tables(){
            $this->load->helper('dine/cashier_helper');
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null,false);
            $tables = $this->get_tables(false);
            $data['code'] = tableTransfer($tables);
            $data['add_css'] = array('css/pos.css','css/onscrkeys.css', 'css/cashier.css');
            $data['add_js'] = array('js/on_screen_keys.js');
            $data['load_js'] = 'dine/cashier';
            $data['use_js'] = 'tableTransferJs';
            $this->load->view('load',$data);
        }
        function go_transfer_table($sales_id=null,$table_id=null){
            $this->load->model('dine/cashier_model');
            $error = "";
            $items = array('table_id'=>$table_id);
            $this->cashier_model->update_trans_sales($items,$sales_id);
            site_alert('Order #'.$sales_id." successfully transfered",'success');
        }
        public function get_tables($asJson=true,$tbl_id=null){
            $this->load->model('dine/cashier_model');
            $tbl = array();
            $occ = array();
            $occ_tbls = $this->cashier_model->get_occupied_tables();
            $billed = array();
            foreach ($occ_tbls as $det) {
              $occ[] = $det->table_id;
            }
            foreach ($occ_tbls as $det) {
              if($det->billed == 1)
                $billed[] = $det->table_id;
            }
            $tables = $this->cashier_model->get_tables();
            foreach ($tables as $res) {
                $status = 'green';
                if(in_array($res->tbl_id, $occ)){
                    if(in_array($res->tbl_id, $billed)){
                        $status = 'orange';
                    }
                    else
                        $status = 'red';
                }
                $tbl[$res->tbl_id] = array(
                    "name"=> $res->name,
                    "top"=> $res->top,
                    "left"=> $res->left,
                    "stat"=> $status
                );
            }
            if($asJson)
                echo json_encode($tbl);
            else
                return $tbl;
        }
        public function get_tbl_status($asJson=true){
            $tbls = $this->get_tables(false);
            if($asJson)
                echo json_encode($tbls);
            else
                return $tbl;
        }    
        public function check_occupied_tables($asJson=true){
            $this->load->model('dine/cashier_model');
            $tbls = $this->get_tables(false);
            $occ = array();
            $ucc = array();
            foreach ($tbls as $tbl_id => $val) {
                if($val['stat']=='red'){
                    $occ[] = array('id'=>$tbl_id,'name'=>$val['name']);
                }
                else{
                    $ucc[] = array('id'=>$tbl_id,'name'=>$val['name']);
                }
            }
            
            // $tbl = array();
            // $occ = array();
            // $occ_tbls = $this->cashier_model->get_occupied_tables();
            // foreach ($occ_tbls as $det) {
            //   $occ[] = array('id'=>$det->table_id,'name'=>$det->name);
            // }

            if($asJson)
                echo json_encode(array('occ'=>$occ,'ucc'=>$ucc));
            else
                return array('occ'=>$occ,'ucc'=>$ucc);
        }
        function get_table_orders($asJson=true,$tbl_id=null){
            $this->load->model('dine/cashier_model');
            $this->load->model('site/site_model');
            $args = array();
            $args["trans_sales.trans_ref  IS NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.inactive"] = 0;
            $args["trans_sales.table_id"] = $tbl_id;
            $orders = $this->cashier_model->get_trans_sales(null,$args);
            $time = $this->site_model->get_db_now();
            $this->make->sDivRow();
            $ord=array();
            foreach ($orders as $res) {
                $status = "open";
                if($res->trans_ref != "")
                    $status = "settled";
                $ord[$res->sales_id] = array(
                    "type"=>$res->type,
                    "status"=>$status,
                    "user_id"=>$res->user_id,
                    "name"=>$res->username,
                    "terminal_id"=>$res->terminal_id,
                    "terminal_name"=>$res->terminal_name,
                    "shift_id"=>$res->shift_id,
                    "datetime"=>$res->datetime,
                    "amount"=>$res->total_amount
                );
                $this->make->sDivCol(4,'left',0);
                        $this->make->sDiv(array('class'=>'order-btn','id'=>'order-btn-'.$res->sales_id,'ref'=>$res->sales_id));
                            if($res->trans_ref == null){
                                $this->make->sBox('default',array('class'=>'box-solid'));
                            }else{
                                $this->make->sBox('default',array('class'=>'box-solid bg-green'));
                            }
                                $this->make->sBoxBody();
                                    $this->make->sDivRow();
                                        $this->make->sDivCol(6);
                                            $this->make->sDiv(array('style'=>'margin-left:20px;'));
                                                $this->make->H(5,strtoupper($res->type)." #".$res->sales_id,array("style"=>'font-weight:700;'));
                                                if($res->trans_ref == null){
                                                    $this->make->H(5,strtoupper($res->username),array("style"=>'color:#888'));
                                                    $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#888'));
                                                }else{
                                                    $this->make->H(5,strtoupper($res->username),array("style"=>'color:#fff'));
                                                    $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#fff'));
                                                }
                                                $this->make->H(5,tagWord(strtoupper(ago($res->datetime,$time) ) ) );
                                            $this->make->eDiv();
                                        $this->make->eDivCol();
                                        $this->make->sDivCol(6);
                                            $this->make->H(4,'Order Total',array('class'=>'text-center'));
                                            $this->make->H(3,num($res->total_amount),array('class'=>'text-center'));
                                        $this->make->eDivCol();
                                    $this->make->eDivRow();
                                    $this->make->sDivRow();
                                        if(ORDER_SLIP_PRINTER_SETUP){
                                            $this->make->sDivCol(4);
                                                $this->make->button(fa('fa-print fa-lg fa-fw').' RePrint Order Slip',array('id'=>'print-os-btn-'.$res->sales_id,'ref'=>$res->sales_id,'class'=>'transfer-btns btn-block tables-btn-teal'));
                                            $this->make->eDivCol();
                                            $this->make->sDivCol(4);
                                                $this->make->button(fa('fa-exchange fa-lg fa-fw').' Transfer Table',array('id'=>'transfer-btn-'.$res->sales_id,'ref'=>$res->sales_id,'class'=>'transfer-btns btn-block tables-btn-orange'));
                                            $this->make->eDivCol();
                                            $this->make->sDivCol(4);
                                                $this->make->button(fa('fa-print fa-lg fa-fw').' Print Billing',array('id'=>'print-btn-'.$res->sales_id,'ref'=>$res->sales_id,'class'=>'transfer-btns btn-block tables-btn-green'));
                                            $this->make->eDivCol();
                                        }
                                        else{
                                            $this->make->sDivCol(6);
                                                $this->make->button(fa('fa-exchange fa-lg fa-fw').' Transfer Table',array('id'=>'transfer-btn-'.$res->sales_id,'ref'=>$res->sales_id,'class'=>'transfer-btns btn-block tables-btn-orange'));
                                            $this->make->eDivCol();
                                            $this->make->sDivCol(6);
                                                $this->make->button(fa('fa-print fa-lg fa-fw').' Print Billing',array('id'=>'print-btn-'.$res->sales_id,'ref'=>$res->sales_id,'class'=>'transfer-btns btn-block tables-btn-green'));
                                            $this->make->eDivCol();
                                        }
                                    $this->make->eDivRow();
                                $this->make->eBoxBody();
                            $this->make->eBox();
                        $this->make->eDiv();
                $this->make->eDivCol();
            }
            $this->make->eDivRow();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'ids'=>$ord));
        }
        function get_charges($asJson=true){
            $this->load->model('dine/cashier_model');
            $this->load->model('dine/settings_model');
            $charges = $this->settings_model->get_charges();
            $discs = array();
            $this->make->sDivRow();
                foreach ($charges as $res) {
                    if($res->inactive != 1){
                        $text = num($res->charge_amount);
                        if($res->absolute == 0){
                            $text .= " %";
                        }
                        $this->make->sDivCol(12);
                            $this->make->button("[".strtoupper($res->charge_code)."] ".strtoupper($res->charge_name)." <br> ".$text,
                                                array('id'=>'charges-btn-'.$res->charge_id,'class'=>'disc-btn-row btn-block counter-btn-orange double'));
                        $this->make->eDivCol();
                        $ids[$res->charge_id] = array(
                            "charge_code"=>$res->charge_code,
                            "charge_name"=>$res->charge_name,
                            "charge_amount"=>$res->charge_amount,
                            "no_tax"=>$res->no_tax,
                            "absolute"=>$res->absolute
                        );
                    }
                }
            $this->make->eDivRow();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'ids'=>$ids));
        }
        public function get_discounts($asJson=true){
            $this->load->model('dine/cashier_model');
            $this->load->model('dine/settings_model');
            $trans_disc_cart = sess('trans_disc_cart');
            $typeCN = sess('trans_type_cart');
            $discounts = $this->settings_model->get_receipt_discounts();
            $discs = array();
            $this->make->sDivRow();
                foreach ($discounts as $res) {
                    if($res->inactive != 1){
                        $this->make->sDivCol(12);
                            $this->make->button("[".strtoupper($res->disc_code)."] ".strtoupper($res->disc_name),array('id'=>'item-disc-btn-'.$res->disc_code,'class'=>'disc-btn-row btn-block counter-btn-green'));
                        $this->make->eDivCol();
                        $ids[$res->disc_code] = array(
                            "disc_code"=>$res->disc_code,
                            "disc_id"=>$res->disc_id,
                            "disc_name"=>$res->disc_name,
                            "disc_rate"=>$res->disc_rate,
                            "fix"=>$res->fix,
                            "no_tax"=>$res->no_tax
                        );
                        $guest = null;
                        if(isset($typeCN[0]['guest'])){
                            $ids[$res->disc_code]['guest'] = $typeCN[0]['guest'];
                        }else{
                             $ids[$res->disc_code]['guest'] = 1;
                        }
                        if(isset($trans_disc_cart[$res->disc_code])){
                            $row = $trans_disc_cart[$res->disc_code];
                            $ids[$res->disc_code]['guest'] = $row['guest'];
                            $ids[$res->disc_code]['disc_type'] = $row['disc_type'];
                            foreach ($row['persons'] as $code => $per) {
                                $ids[$res->disc_code]['persons'][$code] = array(
                                    'name' => $per['name'],
                                    'code' => $per['code'],
                                    'bday' => $per['bday']
                                );
                            }
                        }
                    }
                }
            $this->make->eDivRow();
            $code = $this->make->code();

            echo json_encode(array('code'=>$code,'ids'=>$ids));
        }
        public function remove_person_disc($disc=null,$code=null){
            $trans_disc_cart = sess('trans_disc_cart');
            $persons = array();
            if(isset($trans_disc_cart[$disc]['persons'])){
             $persons = $trans_disc_cart[$disc]['persons'];
            }
            unset($persons[$code]);
            $trans_disc_cart[$disc]['persons'] = $persons;
            sess_initialize('trans_disc_cart',$trans_disc_cart);
            echo json_encode($trans_disc_cart[$disc]);
        }
        public function load_disc_persons($disc=null){
            $trans_disc_cart = sess('trans_disc_cart');
                $persons = array();
            if(isset($trans_disc_cart[$disc]['persons'])){
                $persons = $trans_disc_cart[$disc]['persons'];
            }
            $this->make->sUl();
            $items = array();
            foreach ($persons as $res) {
                $this->make->sLi(array('id'=>'disc-person-'.$res['code'],'class'=>'disc-person','style'=>'padding:5px;padding-bottom:10px;padding-top:10px;border-bottom:1px solid #ddd;'));
                    $link = $this->make->A(fa('fa-times fa-lg'),'#',array('id'=>'disc-person-rem-'.$res['code'],'class'=>'pull-right','return'=>true));            
                    $this->make->H(4,fa('fa-credit-card')." ".$res['code']." - ".fa('fa-user')." ".$res['name']." ".$res['bday']." ".$link,array('style'=>'margin:0;padding:0;margin-left:10px;'));
                $this->make->eLi();    
                $items[$res['code']] = array(
                    "name"=> $res['code'],
                    "bday"=> $res['bday'],
                    "disc"=> $disc
                );
            }
            $this->make->eUl();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'items'=>$items));
        }
        public function add_person_disc(){
           $trans_disc_cart = sess('trans_disc_cart');
           $okay = true;
           if($trans_disc_cart){
                foreach ($trans_disc_cart as $key => $value) {
                    if($key == 'PWDISC' || $key == 'SNDISC'){
                        // echo 'aaa'.$this->input->post('disc-disc-code');
                        if($this->input->post('disc-disc-code') == 'PWDISC' || $this->input->post('disc-disc-code') == 'SNDISC'){
                            // echo 'aaaaaa2222';
                            // $okay = true;
                        }else{
                            // echo 'bbb3333';
                            $okay = false;
                        }
                    }else{
                        // echo 'sss';
                        if($this->input->post('disc-disc-code') == 'PWDISC' || $this->input->post('disc-disc-code') == 'SNDISC'){
                            $okay = false;
                        }
                    }
                }
           }

            $persons = array();
            if(isset($trans_disc_cart[$this->input->post('disc-disc-code')]['persons'])){
                $persons = $trans_disc_cart[$this->input->post('disc-disc-code')]['persons'];
            }
           // var_dump($okay);
            if(!$okay){
                $error = "Cannot Add Double Discounts.";
            }else{

               $error = "";
               $items = array();
               $bday = null;
               // var_dump($persons);

                if($this->input->post('disc-cust-bday'))
                    $bday = $this->input->post('disc-cust-bday');
               
                if(!isset($persons[$this->input->post('disc-cust-code')])){
                    if(count($persons) >= $this->input->post('guests')){
                        $error = "Person is in limit with the no of guest.";
                    }
                    else{
                        // echo $this->input->post('disc-disc-code');
                        if($this->input->post('disc-disc-code') != 'PWDISC' && $this->input->post('disc-disc-code') != 'SNDISC'){
                    
                            if(count($persons) > 0){
                                $error = "Cannot Add More Discounts.";
                            }else{
                                $persons[$this->input->post('disc-cust-code')] = array(
                                    "name"  => $this->input->post('disc-cust-name'),
                                    "code"  => $this->input->post('disc-cust-code'),
                                    "bday"  => $bday
                                );
                            }
                        }else{
                            $persons[$this->input->post('disc-cust-code')] = array(
                                "name"  => $this->input->post('disc-cust-name'),
                                "code"  => $this->input->post('disc-cust-code'),
                                "bday"  => $bday
                            );                    
                            
                        }
                    }
                }
                else{
                    $error = "Person is Already added.";
                }

                $trans_disc_cart[$this->input->post('disc-disc-code')]['persons'] = $persons;
                sess_initialize('trans_disc_cart',$trans_disc_cart);
            }

            $this->make->sUl(array());
            $items = array();
            foreach ($persons as $res) {
                $this->make->sLi(array('id'=>'disc-person-'.$res['code'],'class'=>'disc-person','style'=>'padding:5px;padding-bottom:10px;padding-top:10px;border-bottom:1px solid #ddd;'));
                    $link = $this->make->A(fa('fa-times fa-lg'),'#',array('id'=>'disc-person-rem-'.$res['code'],'class'=>'pull-right','return'=>true));            
                    $this->make->H(4,fa('fa-credit-card')." ".$res['code']." - ".fa('fa-user')." ".$res['name']." ".$res['bday']." ".$link,array('style'=>'margin:0;padding:0;margin-left:10px;'));
                $this->make->eLi();   
                $items[$res['code']] = array(
                    "name"=> $res['code'],
                    "bday"=> $res['bday'],
                    "disc"=> $this->input->post('disc-disc-code')
                );
            }
            $this->make->eUl();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'items'=>$items,'error'=>$error));
        }
        public function add_trans_disc(){
           $trans_disc_cart = sess('trans_disc_cart');
           $disc_cart = array();
           $error = "";
           if(isset($trans_disc_cart[$this->input->post('disc-disc-code')])){
            $disc_cart = $trans_disc_cart[$this->input->post('disc-disc-code')];
           }
           if($this->input->post('guests') > 0){
            if(isset($disc_cart['persons']) && count($disc_cart['persons']) <= $this->input->post('guests')){
                $disc_cart['guest'] =  $this->input->post('guests'); 
                $disc_cart['disc_rate'] =  $this->input->post('disc-disc-rate'); 
                $disc_cart['disc_code'] =  $this->input->post('disc-disc-code'); 
                $disc_cart['disc_id'] =  $this->input->post('disc-disc-id'); 
                $disc_cart['disc_type'] =  $this->input->post('type'); 
                $disc_cart['no_tax'] =  $this->input->post('disc-no-tax'); 
                $disc_cart['fix'] =  $this->input->post('disc-fix'); 
                $trans_disc_cart[$this->input->post('disc-disc-code')] = $disc_cart;
                sess_initialize('trans_disc_cart',$trans_disc_cart);
                // echo var_dump($trans_disc_cart);
            }
            else{
                $error = "Invalid No. of Persons";
            }
           }
           else{
            $error = "Invalid total No. Of Guests";
           }
           echo json_encode(array('error'=>$error));
        }
        public function del_trans_disc($disc_code=null){
           $trans_disc_cart = sess('trans_disc_cart');
           unset($trans_disc_cart[$disc_code]);
           sess_initialize('trans_disc_cart',$trans_disc_cart);
        }
        public function update_tbl_activity($tbl_id=null,$remove=false){
            $active = $this->site_model->get_db_now();
            if(!$remove){
                $items = array(
                    'tbl_id'=>$tbl_id,
                    'pc_id'=>PC_ID,
                    'last_activity'=>date2SqlDateTime($active)
                );
                $res = $this->site_model->get_tbl('table_activity',array('tbl_id'=>$tbl_id,'pc_id'=>PC_ID));
                if(count($res)>0){
                    $this->site_model->update_tbl('table_activity','id',$items,$res[0]->id);     
                    if(LOCALSYNC){
                        $this->sync_model->update_table_activity($res[0]->id);
                    }          
                }
                else{
                    $log_id = $this->site_model->add_tbl('table_activity',$items); 
                    if(LOCALSYNC){
                        $this->sync_model->update_table_activity($log_id);
                    }   

                }        
            }
            else{
                $this->site_model->delete_tbl('table_activity',array('pc_id'=>PC_ID));

                   if(LOCALSYNC){
                        $this->sync_model->delete_table_activity(PC_ID);
                    }  
            }
        }
        public function check_tbl_activity($tbl_id=null,$asJson=true){
            $error = "";
            $res = $this->site_model->get_tbl('table_activity',array('tbl_id'=>$tbl_id));
            if(count($res)>0){
                $error = "TERMINAL ".$res[0]->pc_id." is currently editing this table";            
            }
            else{
                $error = "";
            }
            if($asJson){
                echo json_encode(array('error'=>$error));
            }
            else{
                return $error;
            }
        }
        public function combine($type=null,$sales_id=null){
            $this->load->model('site/site_model');
            $this->load->model('dine/cashier_model');
            $this->load->helper('dine/cashier_helper');
            sess_clear('trans_combine_cart');
            $data = $this->syter->spawn(null);
            $order = $this->get_order(false,$sales_id);
            $trans = $this->load_trans(false,$order);
            $time = $trans['datetime'];
            $type = $type." #".$order['order']['sales_id'];
            sess_add('trans_combine_cart',array('sales_id'=>$order['order']['sales_id'],'balance'=>$order['order']['balance']));

            $data['code'] = combinePage($type,$time,$order['order']);
            $data['add_css'] = 'css/cashier.css';
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'combineJs';
            $data['noNavbar'] = true;
            $this->load->view('cashier',$data);
        }
        public function save_combine(){
            $trans_combine_cart = sess('trans_combine_cart');
            $main_sales_id = null;
            $trans_cart = array();
            $trans_mod_cart = array();
            $ctr = 1;
            $liner = 0;
            foreach ($trans_combine_cart as $key => $co) {
                $sales_id = $co['sales_id'];
                $order = $this->get_order(false,$sales_id);
                $header = $order['order'];
                $details = $order['details'];
                $com = "";
                if($ctr == 1){
                    $main_sales_id = $sales_id;
                    foreach ($details as $line_id => $menu){
                        $trans_cart[$line_id] = array(
                            "menu_id"=> $menu['menu_id'],
                            "name"=> $menu['name'],
                            "cost"=> $menu['price'],
                            "qty"=> $menu['qty'],
                            "remarks"=> $menu['remarks'],
                            "kitchen_slip_printed"=> $menu['kitchen_slip_printed'],
                        );
                        if(count($menu['modifiers']) > 0){
                            foreach ($menu['modifiers'] as $mod) {
                                if($mod['line_id'] == $line_id){
                                    $trans_mod_cart[] = array(
                                        "trans_id"=>$mod['line_id'],
                                        "mod_id"=>$mod['id'],
                                        "menu_id"=>$menu['menu_id'],
                                        "menu_name"=>$menu['name'],
                                        "name"=>$mod['name'],
                                        "cost"=>$mod['price'],
                                        "qty"=>$mod['qty'],
                                        "kitchen_slip_printed"=>$mod['kitchen_slip_printed'],
                                    );
                                }
                            }#END FOR EACH
                        }#END IF
                        $liner = $line_id;
                    }#END MAIN FOR EACH
                }
                else{
                    foreach ($details as $line_id => $menu){
                        $liner++;
                        $trans_cart[$liner] = array(
                            "menu_id"=> $menu['menu_id'],
                            "name"=> $menu['name'],
                            "cost"=> $menu['price'],
                            "qty"=> $menu['qty'],
                            "remarks"=> $menu['remarks'],
                            "kitchen_slip_printed"=> $menu['kitchen_slip_printed'],
                        );
                        if(count($menu['modifiers']) > 0){
                            foreach ($menu['modifiers'] as $mod) {
                                if($mod['line_id'] == $line_id){
                                    $trans_mod_cart[] = array(
                                        "trans_id"=>$liner,
                                        "mod_id"=>$mod['id'],
                                        "menu_id"=>$menu['menu_id'],
                                        "menu_name"=>$menu['name'],
                                        "name"=>$mod['name'],
                                        "cost"=>$mod['price'],
                                        "qty"=>$mod['qty'],
                                        "kitchen_slip_printed"=>$mod['kitchen_slip_printed'],
                                    );
                                }
                            }#END FOR EACH
                        }#END IF
                    }#END MAIN FOR EACH
                    $this->cashier_model->update_trans_sales(array('inactive'=>1,'reason'=>'combined to receipt# '.$main_sales_id),$sales_id);
                    $com .= $sales_id.",";
                    $this->sync_model->update_trans_sales($sales_id);
                }#END IF
                $ctr++;
            }
            $sale = $this->submit_trans(false,null,false,null,$trans_cart,$trans_mod_cart);
            $com = substr($com, 0,-1);
            site_alert('Success! Reciept #'.$com.' combined to reciept#'.$sale['id'],'success');
        }
        public function split($type=null,$sales_id=null){
            $this->load->model('site/site_model');
            $this->load->model('dine/cashier_model');
            $this->load->helper('dine/cashier_helper');
            sess_clear('trans_split_cart');
            $data = $this->syter->spawn(null);
            $order = $this->get_order(false,$sales_id);
            $trans = $this->load_trans(false,$order);
            $time = $trans['datetime'];
            $type = $type." #".$order['order']['sales_id'];
            $data['code'] = splitPage($type,$time,$sales_id,$order['order']);
            $data['add_css'] = 'css/cashier.css';
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'splitJs';
            $data['noNavbar'] = true;
            $this->load->view('cashier',$data);
        }
        public function save_split($id=null){
            $trans_split_cart = sess('trans_split_cart');
            $trans_cart = sess('trans_cart');
            $trans_mod_cart = sess('trans_mod_cart');

            $ctr = 1;
            $error = "";
            foreach ($trans_cart as $trans_id => $tr) {
                if($tr['qty'] > 0){
                    $error = "Please Assign All Items";
                    break;
                }
            }

            if($error == ""){
                $split_into = "";

                foreach ($trans_split_cart as $num => $row) {
                    if($ctr > 1){
                        $counter = sess('counter');
                        unset($counter['sales_id']);
                        $this->session->set_userData('counter',$counter);
                    }
                    $sale = $this->submit_trans(false,null,false,null,$row,null,false,$id);
                    $this->print_sales_receipt($sale['id'],false);
                    $ctr++;
                    $split_into .= " #".$sale['id'].", ";
                }
                site_alert('Success! Transaction split into '.substr($split_into,0,-1),'success');
            }

            echo json_encode(array('error'=>$error));
        }
        public function even_split($num=null,$sales_id=null){
            sess_clear('trans_split_cart');
            $trans_cart = sess('trans_cart');
            $trans_mod_cart = sess('trans_mod_cart');
            $ctr = 1;
            $error = "";
            $split_into = "";
            foreach ($trans_cart as $trans_id => $opt) {
                $opt['cost'] = $opt['cost']/$num;
                $trans_cart[$trans_id] = $opt;
            }
            foreach ($trans_mod_cart as $trans_mod_id => $mod) {
                $mod['cost'] = $mod['cost']/$num;
                $trans_mod_cart[$trans_mod_id] = $mod;
            }
            // for ($i=1; $i <= $num; $i++) {
            //     if($ctr > 1){
            //         $counter = sess('counter');
            //         unset($counter['sales_id']);
            //         $this->session->set_userData('counter',$counter);
            //     }
            //     // $sale = $this->submit_trans(false,null,false,null,$trans_cart,$trans_mod_cart);
            //     $ctr++;
            //     $split_into .= " #".$sale['id'].", ";
            // }
            sess_initialize("trans_cart",$trans_cart);
            sess_initialize("trans_mod_cart",$trans_mod_cart);
            $totals = $this->total_trans(false);
            $split_total = $totals['total'];
            $splits = array('total'=>$split_total,'by'=>$num);
            $this->print_sales_receipt($sales_id,false,false,false,$splits);
            // site_alert('Success! Transaction split into '.substr($split_into,0,-1),'success');
            echo json_encode(array('error'=>$error));
        }
        public function new_split_block($num=0){
            $code = "";
            $trans_split_cart = sess('trans_split_cart');
            // if(count($trans_split_cart) > 0){
            //     $num = max(array_keys($trans_split_cart)) + 1;
            // }
            // else{
            //     if($num > 0){
            //         $num += 1;
            //     }
            // }
            $this->make->sDivCol(4);
                $this->make->sDiv(array('class'=>'sel-div','id'=>'sel-div-'.$num));
                    $this->make->sDiv(array('class'=>'sel-trans-list'));
                        $this->make->sUl(array("style"=>'padding-top:10px;'));
                            // $this->make->li('<span class="qty">100</span><span class="name">100</span><span class="cost">100</span>');
                        $this->make->eUl();
                    $this->make->eDiv();
                    $this->make->sDivRow();
                        $this->make->sDivCol(4);
                            $this->make->button(fa('fa-plus fa-lg fa-fw'),array('class'=>'add-btn btn-block counter-btn-green'));
                        $this->make->eDivCol();
                        $this->make->sDivCol(4);
                            $this->make->button(fa('fa-minus fa-lg fa-fw'),array('class'=>'del-btn btn-block counter-btn-red'));
                        $this->make->eDivCol();
                        $this->make->sDivCol(4);
                            $this->make->button(fa('fa-trash-o fa-lg fa-fw'),array('class'=>'remove-btn btn-block counter-btn-orange'));
                        $this->make->eDivCol();
                    $this->make->eDivRow();
                $this->make->eDiv();
            $this->make->eDivCol();

            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'num'=>$num));
        }
        public function clear_split(){
            $code = "";
            $trans_split_cart = sess('trans_split_cart');
            $trans_cart = sess('trans_cart');
            $trans_mod_cart = sess('trans_mod_cart');
            $upds = array();
            if(count($trans_split_cart) > 0){
                foreach ($trans_split_cart as $num => $trans) {
                    foreach ($trans as $line_id => $row) {
                        $tr_cart = $trans_cart[$line_id];
                        $tr_cart['qty'] += $row['qty'];

                        $trans_cart[$line_id] = $tr_cart;
                        sess_update('trans_cart',$line_id,$trans_cart[$line_id]);
                        $upds[$line_id] = $tr_cart['qty'];
                    }
                }
            }
            sess_clear('trans_split_cart');
            echo json_encode(array('content'=>$upds));
        }
        public function add_split_block($num=1,$line_id=null){
            $code = "";
            $trans_split_cart = sess('trans_split_cart');
            $trans_cart = sess('trans_cart');
            $trans_mod_cart = sess('trans_mod_cart');
            $from_qty = 0;
            if(isset($trans_cart[$line_id])){
               if(!isset($trans_split_cart[$num][$line_id])){
                   $trans_split_cart[$num][$line_id] = $trans_cart[$line_id];
                   $trans_split_cart[$num][$line_id]['qty'] = 0;
               }
               $tr_cart = $trans_cart[$line_id];
               $tr_cart['qty'] -= 1;
               $from_qty = $tr_cart['qty'];
               $trans_cart[$line_id] = $tr_cart;

               $tr_spl_cart = $trans_split_cart[$num][$line_id];
               $tr_spl_cart['qty'] += 1;
               $split_qty = $tr_spl_cart['qty'];
               $trans_split_cart[$num][$line_id] = $tr_spl_cart;

               sess_update('trans_split_cart',$num,$trans_split_cart[$num]);
               sess_update('trans_cart',$line_id,$trans_cart[$line_id]);
               // echo var_dump(sess('trans_split_cart'));
            }
            // $code = $this->make->code();
            echo json_encode(array('from_qty'=>$from_qty,'split_qty'=>$split_qty));
        }
        public function minus_split_block($num=1,$line_id=null){
            $code = "";
            $trans_split_cart = sess('trans_split_cart');
            $trans_cart = sess('trans_cart');
            $trans_mod_cart = sess('trans_mod_cart');
            $from_qty = 0;
            if(isset($trans_cart[$line_id])){
               if(!isset($trans_split_cart[$num][$line_id])){
                   $trans_split_cart[$num][$line_id] = $trans_cart[$line_id];
               }
               $tr_cart = $trans_cart[$line_id];
               $tr_cart['qty'] += 1;
               $from_qty = $tr_cart['qty'];
               $trans_cart[$line_id] = $tr_cart;

               $tr_spl_cart = $trans_split_cart[$num][$line_id];
               $tr_spl_cart['qty'] -= 1;
               $split_qty = $tr_spl_cart['qty'];
               $trans_split_cart[$num][$line_id] = $tr_spl_cart;
               sess_update('trans_split_cart',$num,$trans_split_cart[$num]);
               sess_update('trans_cart',$line_id,$trans_cart[$line_id]);
               // echo var_dump(sess('trans_split_cart'));
            }
            // $code = $this->make->code();
            echo json_encode(array('from_qty'=>$from_qty,'split_qty'=>$split_qty));
        }
        public function remove_split_block($num=null){
            $code = "";
            $trans_split_cart = sess('trans_split_cart');
            $trans_cart = sess('trans_cart');
            $trans_mod_cart = sess('trans_mod_cart');
            $upds = array();
            if(isset($trans_split_cart[$num]) ){
                foreach ($trans_split_cart[$num] as $line_id => $row) {
                    $tr_cart = $trans_cart[$line_id];
                    $tr_cart['qty'] += $row['qty'];

                    $trans_cart[$line_id] = $tr_cart;
                    sess_update('trans_cart',$line_id,$trans_cart[$line_id]);
                    $upds[$line_id] = $tr_cart['qty'];
                }
            }

            if(isset($trans_split_cart[$num]))
                sess_delete('trans_split_cart',$num);
            echo json_encode(array('content'=>$upds));
        }
        #Delivery
        public function delivery(){
            $this->load->model('site/site_model');
            $this->load->model('dine/cashier_model');
            $this->load->helper('dine/cashier_helper');
            sess_clear('trans_type_cart');
            $data = $this->syter->spawn(null);
            $data['code'] = deliveryPage();
            $data['add_css'] = array('css/cashier.css','css/virtual_keyboard.css');
            $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'deliveryJs';
            $data['noNavbar'] = true;
            $this->load->view('cashier',$data);
        }
        public function pickup(){
            $this->load->model('site/site_model');
            $this->load->model('dine/cashier_model');
            $this->load->helper('dine/cashier_helper');
            $data = $this->syter->spawn(null);
            $data['code'] = deliveryPage(array(),'pickup');
            $data['add_css'] = array('css/cashier.css','css/virtual_keyboard.css');
            $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'deliveryJs';
            $data['noNavbar'] = true;
            $this->load->view('cashier',$data);
        }
        public function search_customers($search=null){
            $this->load->model('dine/customers_model');
            $found = array();
            if($search != ""){
                $found = $this->customers_model->search_customers($search);
            }
            $results = array();
            if(count($found) > 0 ){
                foreach ($found as $res) {
                    $results[$res->cust_id] = array('name'=>ucwords(strtolower($res->fname." ".$res->mname." ".$res->lname." ".$res->suffix)),'phone'=>$res->phone,'email'=>$res->email);
                }
            }
            echo json_encode($results);
        }
        public function search_gift_card($search = null){
            $this->load->model('dine/gift_cards_model');

            if (is_null($search)) {
                echo json_encode(array('error'=>'Please enter gift card code'));
                return false;
            }
            $search = str_replace("-", "", $search);
            $return = $this->gift_cards_model->get_gift_card_info($search,false);

            if (empty($return)) {
                echo json_encode(array('error'=>'Gift card does not exist'));
            } else {
                if ($return[0]->inactive == 1)
                    echo json_encode(array('error'=>'Gift card has already been used'));
                else
                    echo json_encode(array('gc_id'=>$return[0]->gc_id,'card_no'=>$return[0]->card_no,'amount'=>number_format($return[0]->amount,2)));
            }
            return false;
        }
        public function search_coupon($search = null){
            if (is_null($search)) {
                echo json_encode(array('error'=>'Please enter coupon card code'));
                return false;
            }

            $search = str_replace("-", "", $search);
            $today = date2Sql($this->site_model->get_db_now('sql'));
            $args['card_no'] = $search;
            $return = $this->site_model->get_tbl('coupons',$args);

            if (empty($return)) {
                echo json_encode(array('error'=>'Coupon does not exist'));
            } else {
                if ($return[0]->inactive == 1)
                    echo json_encode(array('error'=>'Coupon has already been used.'));
                else if( strtotime($today) > strtotime($return[0]->expiration)  ){
                    echo json_encode(array('error'=>'Coupon has expired.'));
                }
                else
                    echo json_encode(array('coupon_id'=>$return[0]->coupon_id,'card_no'=>$return[0]->card_no,'amount'=>number_format($return[0]->amount,2)));
            }
            return false;
        }
        public function get_customers($id=null){
            $this->load->model('dine/customers_model');
            $found = $this->customers_model->get_customer($id);
            $results = array();
            if(count($found) > 0 ){
                foreach ($found as $res) {
                    $results[$res->cust_id] = array(
                        'cust_id'=>ucwords(strtolower($res->cust_id)),
                        'fname'=>ucwords(strtolower($res->fname)),
                        'lname'=>ucwords(strtolower($res->lname)),
                        'mname'=>ucwords(strtolower($res->mname)),
                        'suffix'=>ucwords(strtolower($res->suffix)),
                        'email'=>$res->email,
                        'phone'=>ucwords(strtolower($res->phone)),
                        'street_no'=>ucwords(strtolower($res->street_no)),
                        'street_address'=>ucwords(strtolower($res->street_address)),
                        'city'=>ucwords(strtolower($res->city)),
                        'region'=>ucwords(strtolower($res->region)),
                        'zip'=>$res->zip
                    );
                }
            }
            echo json_encode($results);
        }
        public function get_waiters($id=null){
            $this->load->model('core/user_model');
            $found = $this->user_model->get_users($id);
            $results = array();
            if(count($found) > 0 ){
                $this->make->sDivRow();
                    foreach ($found as $res) {
                        // ucwords(strtolower($res->fname." ".$res->mname." ".$res->lname." ".$res->suffix))
                        $this->make->sDivCol(4);
                            $this->make->sDiv(array('style'=>'margin:2px'));
                            $this->make->button(ucwords(strtolower($res->username)),
                                                array('id'=>'waiters-btn-'.$res->id,'class'=>'btn-block counter-btn-silver'));
                            $this->make->eDiv();
                        $this->make->eDivCol();
                        $results[$res->id] = array(
                            'user_id'=>ucwords(strtolower($res->id)),
                            'fname'=>ucwords(strtolower($res->fname)),
                            'uname'=>ucwords(strtolower($res->username)),
                            'lname'=>ucwords(strtolower($res->lname)),
                            'mname'=>ucwords(strtolower($res->mname)),
                            'suffix'=>ucwords(strtolower($res->suffix)),
                            'full_name'=>ucwords(strtolower($res->fname." ".$res->mname." ".$res->lname." ".$res->suffix))
                        );
                    }
                $this->make->eDivRow();
            }
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'ids'=>$results));
        }
        public function new_trans($asJson=true,$type=null){
            $this->load->model('site/site_model');
            $this->load->model('dine/clock_model');
            sess_clear('trans_mod_cart');
            sess_clear('trans_cart');
            // sess_clear('trans_items_cart');
            sess_clear('counter');
            sess_clear('trans_disc_cart');
            sess_clear('trans_charge_cart');
            sess_clear('reasons');
            $time = $this->site_model->get_db_now();
            $user = $this->session->userdata('user');
            // $get_shift = $this->clock_model->get_shift_id(date2Sql($time),$user['id']);
            $get_shift = $this->clock_model->get_shift_id(null,$user['id']);
            $shift_id = 0;
            if(count($get_shift) > 0){
                $shift_id = $get_shift[0]->shift_id;
            }
            $counter = array(
                "datetime"=> $time,
                "sales_id"=> null,
                "shift_id"=> $shift_id,
                "terminal_id"=> TERMINAL_ID,
                "user_id"=> $user['id'],
                "type"=> $type
            );
            if($type != 'dinein' && $type != 'delivery' && $type != 'eatigo' && $type != 'bigdish')
                sess_clear('trans_type_cart');

            $this->session->set_userData('counter',$counter);
            if($asJson)
                echo json_encode($counter);
            else
                return $counter;
        }
        public function retrack_trans($asJson=true,$type=null){
            $this->load->model('site/site_model');
            $this->load->model('dine/clock_model');
            sess_clear('trans_mod_cart');
            sess_clear('trans_cart');
            // sess_clear('trans_items_cart');
            sess_clear('counter');
            sess_clear('trans_disc_cart');
            sess_clear('trans_charge_cart');
            sess_clear('reasons');
            $time = $this->site_model->get_db_now();
            $user = $this->session->userdata('user');
            // $get_shift = $this->clock_model->get_shift_id(date2Sql($time),$user['id']);
            $get_shift = $this->clock_model->get_shift_id(null,$user['id']);
            $shift_id = 0;
            if(count($get_shift) > 0){
                $shift_id = $get_shift[0]->shift_id;
            }
            $counter = array(
                "datetime"=> $time,
                "sales_id"=> null,
                "shift_id"=> $shift_id,
                "terminal_id"=> TERMINAL_ID,
                "user_id"=> $user['id'],
                "type"=> $type
            );

            $this->session->set_userData('counter',$counter);
            if($asJson)
                echo json_encode($counter);
            else
                return $counter;
        }
        public function update_trans_customer($customer_id=null){
            $trans_type_cart = sess('trans_type_cart');
            $trans_type_cart[0]['customer_id'] = $customer_id;
            sess_initialize('trans_type_cart',$trans_type_cart);
        }    
        public function update_trans($update=null,$value=null,$unset=false){
            $counter = sess('counter');
            if($unset){
                if(isset($counter[$update])){
                    unset($counter[$update]);
                }
            }
            else{
                $counter[$update] = $value;
                if($update == 'zero_rated'){
                    $trans_cart = sess('trans_cart');
                    foreach ($trans_cart as $line_id => $opt) {
                        $opt['no_tax'] = $value;
                        $trans_cart[$line_id] = $opt;
                    }
                    sess_initialize('trans_cart',$trans_cart);
                }
            }
            sess_initialize('counter',$counter);
            echo json_encode($counter);
        }
        public function load_trans($asJson=true,$trans=null,$noSalesId=false){
            $this->load->model('site/site_model');
            $this->load->model('dine/clock_model');
            sess_clear('trans_mod_cart');
            sess_clear('trans_cart');
            sess_clear('counter');
            sess_clear('trans_disc_cart');
            sess_clear('trans_charge_cart');
            sess_clear('trans_type_cart');
            sess_clear('reasons');
            $time = $this->site_model->get_db_now();
            $user = $this->session->userdata('user');
            $get_shift = $this->clock_model->get_shift_id(null,$user['id']);
            // $get_shift = $this->clock_model->get_shift_id(date2Sql($time),$user['id']);
            $shift_id = 0;
            if(count($get_shift) > 0){
                $shift_id = $get_shift[0]->shift_id;
            }
            $order=$trans['order'];
            $details=$trans['details'];
            $discounts = $trans['discounts'];
            $charges = $trans['charges'];
            $zero_rated=$trans['zero_rated'];
            $sales_id = $order['sales_id'];
            if($noSalesId)
                $sales_id = "";
            $counter = array(
                "datetime"=> sql2DateTime($order['datetime']),
                "shift_id"=> $shift_id,
                "sales_id"=> $sales_id,
                "terminal_id"=> TERMINAL_ID,
                "user_id"=> $user['id'],
                "type"=> $order['type']
            );
            if(count($zero_rated) > 0){
                foreach ($zero_rated as $zid => $opt) {
                    if($opt['amount'] > 0){
                      $counter['zero_rated'] = 1;
                      break;
                    }
                }
            }
            if(isset($order['waiter_id'])){
                $counter['waiter_id'] = $order['waiter_id'];
            }
            $trans_type_cart = array();
            if($order['type'] == 'dinein'){
                $trans_type_cart[0]['type']='dinein';
                $trans_type_cart[0]['table']=$order['table_id'];
                $trans_type_cart[0]['guest']=$order['guest'];
            }
            if($order['type'] == 'delivery'){
                $trans_type_cart[0]['type']='delivery';
                $trans_type_cart[0]['customer_id']=$order['customer_id'];
            }
            if($order['type'] == 'retail'){
                $trans_type_cart[0]['type']='retail';
                $trans_type_cart[0]['customer_id']=$order['customer_id'];
            }
            if($order['type'] == 'counter'){
                $trans_type_cart[0]['type']='counter';
                $trans_type_cart[0]['serve_no']=$order['serve_no'];
            }
            if($order['type'] == 'takeout'){
                $trans_type_cart[0]['type']='takeout';
                $trans_type_cart[0]['serve_no']=$order['serve_no'];
            }

            $trans_cart = array();
            $trans_mod_cart = array();
            $trans_disc_cart = array();
            $trans_charge_cart = array();
            foreach ($details as $line_id => $menu) {
                $fuid = "";
                $kspr = "";
                if(isset($menu['free_user_id']))
                    $fuid = $menu['free_user_id'];
                if(isset($menu['kitchen_slip_printed']))
                    $kspr = $menu['kitchen_slip_printed'];
                $trans_cart[$line_id] = array(
                    "menu_id"=> $menu['menu_id'],
                    "name"=> $menu['name'],
                    "cost"=> $menu['price'],
                    "qty"=> $menu['qty'],
                    "no_tax"=> $menu['no_tax'],
                    "remarks"=> $menu['remarks'],
                    "free_user_id"=> $fuid,
                    "kitchen_slip_printed"=>$kspr
                );
                if(isset($menu['retail']))
                  $trans_cart[$line_id]['retail'] = $menu['retail'];
                if(isset($menu['modifiers']) && count($menu['modifiers']) > 0){
                    foreach ($menu['modifiers'] as $mod) {
                        if($mod['line_id'] == $line_id){
                            $trans_mod_cart[] = array(
                                "trans_id"=>$mod['line_id'],
                                "mod_id"=>$mod['id'],
                                "mod_group_id"=>$mod['mod_group_id'],
                                "menu_id"=>$menu['menu_id'],
                                "menu_name"=>$menu['name'],
                                "name"=>$mod['name'],
                                "cost"=>$mod['price'],
                                "qty"=>$mod['qty'],
                                "kitchen_slip_printed"=>$menu['kitchen_slip_printed']
                            );
                        }
                    }#END FOR EACH
                }#END IF
            }
            if(count($discounts) > 0){
                foreach ($discounts as $disc_code => $dc) {
                    $trans_disc_cart[$disc_code] = array(
                        // "name"  => $dc['name'],
                        // "code"  => $dc['code'],
                        "no_tax"  => $dc['no_tax'],
                        "guest" => $dc['guest'],
                        "disc_rate" => $dc['disc_rate'],
                        "disc_id" => $dc['disc_id'],
                        "disc_code" => $dc['disc_code'],
                        "disc_type" => $dc['disc_type'],
                        "persons" => $dc['persons'],
                        "fix" => $dc['fix'],
                        // "items" => $dc['items']
                    );
                }
            }
            if(count($charges) > 0){
                foreach ($charges as $charge_id => $dc) {
                    $trans_charge_cart[$charge_id] = array(
                        "name"  => $dc['name'],
                        "code"  => $dc['code'],
                        "amount"  => $dc['amount'],
                        "absolute" => $dc['absolute']
                    );
                }
            }

            if(isset($counter['zero_rated']) && $counter['zero_rated'] > 0){
                foreach ($trans_cart as $line_id => $opt) {
                    $opt['no_tax'] = 1;
                    $trans_cart[$line_id] = $opt;
                }
            }

            $this->session->set_userData('trans_cart',$trans_cart);
            $this->session->set_userData('trans_mod_cart',$trans_mod_cart);
            if(count($trans_type_cart) > 0){
                $this->session->set_userData('trans_type_cart',$trans_type_cart);
            }
            if(count($trans_disc_cart) > 0){
                $this->session->set_userData('trans_disc_cart',$trans_disc_cart);
            }
            if(count($trans_charge_cart) > 0){
                $this->session->set_userData('trans_charge_cart',$trans_charge_cart);
            }
            $this->session->set_userData('counter',$counter);
            if($asJson)
                echo json_encode($counter);
            else
                return $counter;
        }
        public function get_trans_cart($asJson=true){
            $trans_cart = sess('trans_cart');
            $trans_mod_cart = sess('trans_mod_cart');
            $order = null;
            foreach ($trans_cart as $trans_id => $menu) {
                $free_user_id = "";
                if(isset($menu['free_user_id'])){
                    $free_user_id = $menu['free_user_id'];
                }
                if(!isset($menu['retail'])){
                    $order[$trans_id] =  array(
                        "menu_id"=> $menu['menu_id'],
                        "name"=> $menu['name'],
                        "cost"=> $menu['cost'],
                        "qty"=> $menu['qty'],
                        "remarks"=> $menu['remarks'],
                        "free_user_id"=> $free_user_id,
                        "kitchen_slip_printed"=> $menu["kitchen_slip_printed"]
                    );
                    $mods = array();
                    if(count($trans_mod_cart) > 0){
                        foreach ($trans_mod_cart as $id => $mod) {
                            if($mod['trans_id'] == $trans_id){
                                $mods[$id] = array(
                                    "trans_id"=>$mod['trans_id'],
                                    "mod_id"=>$mod['mod_id'],
                                    "menu_id"=>$mod['menu_id'],
                                    "menu_name"=>$mod['menu_name'],
                                    "name"=>$mod['name'],
                                    "cost"=>$mod['cost'],
                                    "qty"=>$mod['qty'],
                                    "kitchen_slip_printed"=>$mod["kitchen_slip_printed"]
                                );
                            }#IF
                        }#FOREACH
                    }#IF
                    $order[$trans_id]['modifiers'] = $mods;
                }
                else{
                    $order[$trans_id] =  array(
                        "menu_id"=> $menu['menu_id'],
                        "name"=> $menu['name'],
                        "cost"=> $menu['cost'],
                        "qty"=> $menu['qty'],
                        "remarks"=> $menu['remarks'],
                        "retail"=>$menu['retail']
                    );
                }

            }
            if($asJson)
                echo json_encode($order);
            else
                return $order;
        }
        public function get_trans_charges($asJson=true){
            $this->load->model('dine/settings_model');
            $trans_charge_cart = sess('trans_charge_cart');
            $counter = sess('counter');
            
            $charge = null;
            foreach ($trans_charge_cart as $charge_id => $dc) {
                $charge[$charge_id] = array(
                    "name"  => $dc['name'],
                    "code"  => $dc['code'],
                    "amount"  => $dc['amount'],
                    "absolute" => $dc['absolute']
                );
            }
            if(AUTO_ADD_SERVICE_CHARGE){
                if($counter['type'] == 'dinein' || $counter['type'] == 'eatigo' || $counter['type'] == 'bigdish'){
                    if(!isset($charge[SERVICE_CHARGE_ID])){
                        $serc = $this->settings_model->get_charges(SERVICE_CHARGE_ID);
                        $sc = $serc[0];
                        $charge[$sc->charge_id] = array(
                            "name"  => $sc->charge_name,
                            "code"  => $sc->charge_code,
                            "amount"  => $sc->charge_amount,
                            "absolute" => $sc->absolute
                        );
                        sess_add("trans_charge_cart",$charge[$sc->charge_id],$sc->charge_id);
                    }
                }
            }
            if(AUTO_ADD_SERVICE_CHARGE_DELIVERY){
                if($counter['type'] == 'delivery'){
                    if(!isset($charge[SERVICE_CHARGE_DELIVERY_ID])){
                        $serc = $this->settings_model->get_charges(SERVICE_CHARGE_DELIVERY_ID);
                        $sc = $serc[0];
                        $charge[$sc->charge_id] = array(
                            "name"  => $sc->charge_name,
                            "code"  => $sc->charge_code,
                            "amount"  => $sc->charge_amount,
                            "absolute" => $sc->absolute
                        );
                        sess_add("trans_charge_cart",$charge[$sc->charge_id],$sc->charge_id);
                    }
                }
            }
            if(AUTO_ADD_SERVICE_CHARGE_TAKEOUT){
                if($counter['type'] == 'takeout'){
                    if(!isset($charge[SERVICE_CHARGE_TAKEOUT_ID])){
                        $serc = $this->settings_model->get_charges(SERVICE_CHARGE_TAKEOUT_ID);
                        $sc = $serc[0];
                        $charge[$sc->charge_id] = array(
                            "name"  => $sc->charge_name,
                            "code"  => $sc->charge_code,
                            "amount"  => $sc->charge_amount,
                            "absolute" => $sc->absolute
                        );
                        sess_add("trans_charge_cart",$charge[$sc->charge_id],$sc->charge_id);
                    }
                }
            }
            if(AUTO_ADD_HANDLING_CHARGE_PANDA){
                if($counter['type'] == 'foodpanda' || $counter['type'] == 'honestbee'){
                    if(!isset($charge[SERVICE_CHARGE_PANDA_ID])){
                        $serc = $this->settings_model->get_charges(SERVICE_CHARGE_PANDA_ID);
                        $sc = $serc[0];
                        $charge[$sc->charge_id] = array(
                            "name"  => $sc->charge_name,
                            "code"  => $sc->charge_code,
                            "amount"  => $sc->charge_amount,
                            "absolute" => $sc->absolute
                        );
                        sess_add("trans_charge_cart",$charge[$sc->charge_id],$sc->charge_id);
                    }
                }
            }
            if($asJson)
                echo json_encode($charge);
            else
                return $charge;
        }
        // public function get_order($asJson=true,$sales_id=null,$branch_code="",$brand=""){
        //     /*
        //      * -------------------------------------------
        //      *   Load receipt data
        //      * -------------------------------------------
        //     */
        //     $this->load->model('dine/cashier_model');
        //     $orders = $this->cashier_model->get_trans_sales($sales_id,array(),'desc',null,$branch_code,$brand);
        //     $order = array();
        //     $details = array();
        //     foreach ($orders as $res) {
        //         $order = array(
        //             "sales_id"=>$res->sales_id,
        //             'ref'=>$res->trans_ref,
        //             "type"=>$res->type,
        //             "table_id"=>$res->table_id,
        //             "table_name"=>$res->table_name,
        //             "guest"=>$res->guest,
        //             "serve_no"=>$res->serve_no,
        //             "user_id"=>$res->user_id,
        //             "customer_id"=>$res->customer_id,
        //             "name"=>$res->username,
        //             // "terminal_id"=>$res->terminal_id,
        //             // "terminal_name"=>$res->terminal_name,
        //             // "terminal_code"=>$res->terminal_code,
        //             "shift_id"=>$res->shift_id,
        //             "datetime"=>$res->datetime,
        //             "amount"=>$res->total_amount,
        //             "balance"=>round($res->total_amount,2) - round($res->total_paid,2),
        //             "paid"=>$res->paid,
        //             "printed"=>$res->printed,
        //             "inactive"=>$res->inactive,
        //             "waiter_id"=>$res->waiter_id,
        //             "void_ref"=>$res->void_ref,
        //             "reason"=>$res->reason,
        //             "waiter_name"=>ucwords(strtolower($res->waiterfname." ".$res->waitermname." ".$res->waiterlname." ".$res->waitersuffix)),
        //             "waiter_username"=>ucwords(strtolower($res->waiterusername))
        //             // "pay_type"=>$res->pay_type,
        //             // "pay_amount"=>$res->pay_amount,
        //             // "pay_ref"=>$res->pay_ref,
        //             // "pay_card"=>$res->pay_card,
        //         );
        //     }

        //     $args = array("trans_sales_menus.sales_id"=>$sales_id,"trans_sales_menus.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_menus.pos_id'] = $brand;
        //     }
        //     $order_menus = $this->cashier_model->get_trans_sales_menus(null,$args);

        //     $args = array("trans_sales_items.sales_id"=>$sales_id,"trans_sales_items.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_items.pos_id'] = $brand;
        //     }
        //     $order_items = $this->cashier_model->get_trans_sales_items(null,$args);

        //     $args = array("trans_sales_menu_modifiers.sales_id"=>$sales_id,"trans_sales_menu_modifiers.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_menu_modifiers.pos_id'] = $brand;
        //     }
        //     $order_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,$args);

        //     $args = array("trans_sales_discounts.sales_id"=>$sales_id,"trans_sales_discounts.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_discounts.pos_id'] = $brand;
        //     }
        //     $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,$args);

        //     $args = array("trans_sales_tax.sales_id"=>$sales_id,"trans_sales_tax.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_tax.pos_id'] = $brand;
        //     }
        //     $sales_tax = $this->cashier_model->get_trans_sales_tax(null,$args);

        //     $args = array("trans_sales_payments.sales_id"=>$sales_id,"trans_sales_payments.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_payments.pos_id'] = $brand;
        //     }
        //     $sales_payments = $this->cashier_model->get_trans_sales_payments(null,$args);

        //     $args = array("trans_sales_no_tax.sales_id"=>$sales_id,"trans_sales_no_tax.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_no_tax.pos_id'] = $brand;
        //     }
        //     $sales_no_tax = $this->cashier_model->get_trans_sales_no_tax(null,$args);

        //     $args = array("trans_sales_zero_rated.sales_id"=>$sales_id,"trans_sales_zero_rated.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_zero_rated.pos_id'] = $brand;
        //     }
        //     $sales_zero_rated = $this->cashier_model->get_trans_sales_zero_rated(null,$args);

        //     $args = array("trans_sales_charges.sales_id"=>$sales_id,"trans_sales_charges.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_charges.pos_id'] = $brand;
        //     }
        //     $sales_charges = $this->cashier_model->get_trans_sales_charges(null,$args);

        //     $args = array("trans_sales_local_tax.sales_id"=>$sales_id,"trans_sales_local_tax.branch_code"=>$branch_code);
        //     if($brand != ''){
        //         $args['trans_sales_local_tax.pos_id'] = $brand;
        //     }
        //     $sales_local_tax = $this->cashier_model->get_trans_sales_local_tax(null,$args);
        //     $pays = array();
        //     foreach ($sales_payments as $py) {
        //         $pays[$py->payment_id] = array(
        //                 "sales_id"      => $py->sales_id,
        //                 "payment_type"  => $py->payment_type,
        //                 "amount"        => $py->amount,
        //                 "to_pay"        => $py->to_pay,
        //                 "reference"     => $py->reference,
        //                 "card_type"     => $py->card_type,
        //                 "card_number"   => $py->card_number,
        //                 "approval_code"   => $py->approval_code,
        //                 "user_id"       => $py->user_id,
        //                 "datetime"      => $py->datetime,
        //             );
        //     }
        //     foreach ($order_menus as $men) {
        //         $details[$men->line_id] = array(
        //             "id"=>$men->sales_menu_id,
        //             "menu_id"=>$men->menu_id,
        //             "name"=>$men->menu_name,
        //             "code"=>$men->menu_code,
        //             "price"=>$men->price,
        //             "qty"=>$men->qty,
        //             "no_tax"=>$men->no_tax,
        //             "discount"=>$men->discount,
        //             "remarks"=>$men->remarks,
        //             "free_user_id"=>$men->free_user_id,
        //             "kitchen_slip_printed"=>$men->kitchen_slip_printed
        //         );
        //         $mods = array();
        //         foreach ($order_mods as $mod) {
        //             if($mod->line_id == $men->line_id){
        //                 $mods[$mod->sales_mod_id] = array(
        //                     "id"=>$mod->mod_id,
        //                     "sales_mod_id"=>$mod->sales_mod_id,
        //                     "mod_group_id"=>$mod->mod_group_id,
        //                     "line_id"=>$mod->line_id,
        //                     "name"=>$mod->mod_name,
        //                     "price"=>$mod->price,
        //                     "qty"=>$mod->qty,
        //                     "discount"=>$mod->discount,
        //                     "kitchen_slip_printed"=>$mod->kitchen_slip_printed
        //                 );
        //             }
        //         }
        //         $details[$men->line_id]['modifiers'] = $mods;
        //     }
        //     foreach ($order_items as $men){
        //         $details[$men->line_id] = array(
        //             "id"=>$men->sales_item_id,
        //             "menu_id"=>$men->item_id,
        //             "name"=>$men->name,
        //             "code"=>$men->code,
        //             "price"=>$men->price,
        //             "qty"=>$men->qty,
        //             "no_tax"=>$men->no_tax,
        //             "discount"=>$men->discount,
        //             "remarks"=>$men->remarks,
        //             "retail"=>1
        //         );
        //     }
        //     ### CHANGED #############
        //     $discounts = array();
        //     $persons = array();
        //     foreach ($sales_discs as $dc) {
        //         $discounts[$dc->disc_code] = array(
        //                 "no_tax"  => $dc->no_tax,
        //                 "guest" => $dc->guest,
        //                 "disc_rate" => $dc->disc_rate,
        //                 "disc_id" => $dc->disc_id,
        //                 "disc_code" => $dc->disc_code,
        //                 "disc_type" => $dc->type,
        //                 "fix" => $dc->fix,
        //                 "persons" => array()
        //         );
        //     }
        //     foreach ($sales_discs as $dc) {
        //         $pcode = $dc->code;
        //         $bday = "";
        //         if($dc->bday != "")
        //             $bday = sql2Date($dc->bday);
        //         $person = array(
        //             "name"  => $dc->name,
        //             "code"  => $dc->code,
        //             "bday"  => $bday,
        //             "amount" => $dc->amount,
        //             "disc_rate" => $dc->disc_rate,
        //         );
        //         if(isset($discounts[$dc->disc_code])){
        //             $dscp =  $discounts[$dc->disc_code]['persons'];
        //             $dscp[$pcode] = $person;
        //             $discounts[$dc->disc_code]['persons'] = $dscp;
        //         }
        //     }
        //     ### CHANGED #############
        //     $tax = array();
        //     foreach ($sales_tax as $tx) {
        //         $tax[$tx->sales_tax_id] = array(
        //                 "sales_id"  => $tx->sales_id,
        //                 "name"  => $tx->name,
        //                 "rate" => $tx->rate,
        //                 "amount" => $tx->amount
        //             );
        //     }
        //     $no_tax = array();
        //     foreach ($sales_no_tax as $nt) {
        //         $no_tax[$nt->sales_no_tax_id] = array(
        //             "sales_id" => $nt->sales_id,
        //             "amount" => $nt->amount,
        //         );
        //     }
        //     $zero_rated = array();
        //     foreach ($sales_zero_rated as $zt) {
        //         $zero_rated[$zt->sales_zero_rated_id] = array(
        //             "sales_id" => $zt->sales_id,
        //             "amount" => $zt->amount,
        //         );
        //     }
        //     $local_tax = array();
        //     foreach ($sales_local_tax as $lt) {
        //         $local_tax[$lt->sales_local_tax_id] = array(
        //             "sales_id" => $lt->sales_id,
        //             "amount" => $lt->amount,
        //         );
        //     }
        //     $charges = array();
        //     foreach ($sales_charges as $ch) {
        //         $charges[$ch->charge_id] = array(
        //                 "name"  => $ch->charge_name,
        //                 "code"  => $ch->charge_code,
        //                 "amount"  => $ch->rate,
        //                 "absolute" => $ch->absolute,
        //                 "total_amount" => $ch->amount
        //             );
        //     }
        //     if($asJson)
        //         echo json_encode(array('order'=>$order,"details"=>$details,"discounts"=>$discounts,"taxes"=>$tax,"no_tax"=>$no_tax,"zero_rated"=>$zero_rated,"payments"=>$pays,"charges"=>$charges,"local_tax"=>$local_tax));
        //     else
        //         return array('order'=>$order,"details"=>$details,"discounts"=>$discounts,"taxes"=>$tax,"no_tax"=>$no_tax,"zero_rated"=>$zero_rated,"payments"=>$pays,"charges"=>$charges,"local_tax"=>$local_tax);
        // }
        public function get_order($asJson=true,$sales_id=null,$branch_code="",$brand=""){
            /*
             * -------------------------------------------
             *   Load receipt data
             * -------------------------------------------
            */
            $this->load->model('dine/cashier_model');
            $orders = $this->cashier_model->get_trans_sales($sales_id,array(),'desc',null,$branch_code,$brand);
            $order = array();
            $details = array();
            foreach ($orders as $res) {
                $order = array(
                    "sales_id"=>$res->sales_id,
                    'ref'=>$res->trans_ref,
                    "type"=>$res->type,
                    "table_id"=>$res->table_id,
                    "table_name"=>$res->table_name,
                    "guest"=>$res->guest,
                    "serve_no"=>$res->serve_no,
                    "user_id"=>$res->user_id,
                    "customer_id"=>$res->customer_id,
                    "name"=>$res->username,
                    "terminal_id"=>$res->terminal_id,
                    "customer_name"=>$res->customer_name,
                    "cust_address"=>$res->cust_address,
                    "tin"=>$res->tin,
                    "business_style"=>$res->business_style,
                    "printed"=>$res->printed,
                    // "terminal_name"=>$res->terminal_name,
                    // "terminal_code"=>$res->terminal_code,
                    "shift_id"=>$res->shift_id,
                    "datetime"=>$res->datetime,
                    "amount"=>$res->total_amount,
                    "balance"=>round($res->total_amount,2) - round($res->total_paid,2),
                    "paid"=>$res->paid,
                    "printed"=>$res->printed,
                    "inactive"=>$res->inactive,
                    "waiter_id"=>$res->waiter_id,
                    "void_ref"=>$res->void_ref,
                    "reason"=>$res->reason,
                    "ewt1"=>$res->ewt1,
                    "ewt2"=>$res->ewt2,
                    "ewt5"=>$res->ewt5,
                    "waiter_name"=>ucwords(strtolower($res->waiterfname." ".$res->waitermname." ".$res->waiterlname." ".$res->waitersuffix)),
                    "waiter_username"=>ucwords(strtolower($res->waiterusername))
                    // "pay_type"=>$res->pay_type,
                    // "pay_amount"=>$res->pay_amount,
                    // "pay_ref"=>$res->pay_ref,
                    // "pay_card"=>$res->pay_card,
                );
            }

            $args = array("trans_sales_menus.sales_id"=>$sales_id,"trans_sales_menus.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_menus.pos_id'] = $brand;
            }
            $order_menus = $this->cashier_model->get_trans_sales_menus(null,$args);

            $args = array("trans_sales_items.sales_id"=>$sales_id,"trans_sales_items.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_items.pos_id'] = $brand;
            }
            $order_items = $this->cashier_model->get_trans_sales_items(null,$args);

            $args = array("trans_sales_menu_modifiers.sales_id"=>$sales_id,"trans_sales_menu_modifiers.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_menu_modifiers.pos_id'] = $brand;
            }
            $order_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,$args);

            $args = array("trans_sales_discounts.sales_id"=>$sales_id,"trans_sales_discounts.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_discounts.pos_id'] = $brand;
            }
            $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,$args);

            $args = array("trans_sales_tax.sales_id"=>$sales_id,"trans_sales_tax.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_tax.pos_id'] = $brand;
            }
            $sales_tax = $this->cashier_model->get_trans_sales_tax(null,$args);

            $args = array("trans_sales_payments.sales_id"=>$sales_id,"trans_sales_payments.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_payments.pos_id'] = $brand;
            }
            $sales_payments = $this->cashier_model->get_trans_sales_payments(null,$args);

            $args = array("trans_sales_no_tax.sales_id"=>$sales_id,"trans_sales_no_tax.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_no_tax.pos_id'] = $brand;
            }
            $sales_no_tax = $this->cashier_model->get_trans_sales_no_tax(null,$args);

            $args = array("trans_sales_zero_rated.sales_id"=>$sales_id,"trans_sales_zero_rated.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_zero_rated.pos_id'] = $brand;
            }
            $sales_zero_rated = $this->cashier_model->get_trans_sales_zero_rated(null,$args);

            $args = array("trans_sales_charges.sales_id"=>$sales_id,"trans_sales_charges.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_charges.pos_id'] = $brand;
            }
            $sales_charges = $this->cashier_model->get_trans_sales_charges(null,$args);

            $args = array("trans_sales_local_tax.sales_id"=>$sales_id,"trans_sales_local_tax.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_local_tax.pos_id'] = $brand;
            }
            $sales_local_tax = $this->cashier_model->get_trans_sales_local_tax(null,$args);

            $args = array("trans_sales_payment_fields.sales_id"=>$sales_id,"trans_sales_payment_fields.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_payment_fields.pos_id'] = $brand;
            }

            $payment_fields = $this->cashier_model->get_trans_sales_payment_fields(null,$args);
            $pays = array();
            foreach ($sales_payments as $py) {
                $pays[$py->payment_id] = array(
                        "sales_id"      => $py->sales_id,
                        "payment_type"  => $py->payment_type,
                        "amount"        => $py->amount,
                        "to_pay"        => $py->to_pay,
                        "reference"     => $py->reference,
                        "card_type"     => $py->card_type,
                        "card_number"   => $py->card_number,
                        "approval_code"   => $py->approval_code,
                        "user_id"       => $py->user_id,
                        "datetime"      => $py->datetime,
                    );
            }
            foreach ($order_menus as $men) {
                $details[$men->line_id] = array(
                    "id"=>$men->sales_menu_id,
                    "menu_id"=>$men->menu_id,
                    "name"=>$men->menu_name,
                    "code"=>$men->menu_code,
                    "price"=>$men->price,
                    "qty"=>$men->qty,
                    "no_tax"=>$men->no_tax,
                    "discount"=>$men->discount,
                    "remarks"=>$men->remarks,
                    "free_user_id"=>$men->free_user_id,
                    "is_takeout"=>$men->is_takeout,
                    "kitchen_slip_printed"=>$men->kitchen_slip_printed
                );
                $mods = array();
                foreach ($order_mods as $mod) {
                    if($mod->line_id == $men->line_id){
                        $mods[$mod->sales_mod_id] = array(
                            "id"=>$mod->mod_id,
                            "sales_mod_id"=>$mod->sales_mod_id,
                            "mod_group_id"=>$mod->mod_group_id,
                            "line_id"=>$mod->line_id,
                            "name"=>$mod->mod_name,
                            "price"=>$mod->price,
                            "qty"=>$mod->qty,
                            "discount"=>$mod->discount,
                            "kitchen_slip_printed"=>$mod->kitchen_slip_printed
                        );
                    }
                }
                $details[$men->line_id]['modifiers'] = $mods;
            }
            foreach ($order_items as $men){
                $details[$men->line_id] = array(
                    "id"=>$men->sales_item_id,
                    "menu_id"=>$men->item_id,
                    "name"=>$men->name,
                    "code"=>$men->code,
                    "price"=>$men->price,
                    "qty"=>$men->qty,
                    "no_tax"=>$men->no_tax,
                    "discount"=>$men->discount,
                    "remarks"=>$men->remarks,
                    "retail"=>1
                );
            }
            ### CHANGED #############
            $discounts = array();
            $persons = array();
            foreach ($sales_discs as $dc) {
                $discounts[$dc->disc_code] = array(
                        "no_tax"  => $dc->no_tax,
                        "guest" => $dc->guest,
                        "disc_rate" => $dc->disc_rate,
                        "disc_id" => $dc->disc_id,
                        "disc_code" => $dc->disc_code,
                        "disc_type" => $dc->type,
                        "fix" => $dc->fix,
                        "name" => $dc->name,
                        "code" => $dc->code,
                        "bday" => $dc->bday,
                        "items" => $dc->items,
                        "openamt" => 0,
                        "persons" => array()
                );
            }
            foreach ($sales_discs as $dc) {
                $pcode = $dc->code;
                $bday = "";
                if($dc->bday != "")
                    $bday = sql2Date($dc->bday);
                $person = array(
                    "name"  => $dc->name,
                    "code"  => $dc->code,
                    "bday"  => $bday,
                    "amount" => $dc->amount,
                    "disc_rate" => $dc->disc_rate,
                );
                if(isset($discounts[$dc->disc_code])){
                    $dscp =  $discounts[$dc->disc_code]['persons'];
                    $dscp[$pcode] = $person;
                    $discounts[$dc->disc_code]['persons'] = $dscp;
                }
            }
            ### CHANGED #############
            $tax = array();
            foreach ($sales_tax as $tx) {
                $tax[$tx->sales_tax_id] = array(
                        "sales_id"  => $tx->sales_id,
                        "name"  => $tx->name,
                        "rate" => $tx->rate,
                        "amount" => $tx->amount
                    );
            }
            $no_tax = array();
            foreach ($sales_no_tax as $nt) {
                $no_tax[$nt->sales_no_tax_id] = array(
                    "sales_id" => $nt->sales_id,
                    "amount" => $nt->amount,
                );
            }
            $zero_rated = array();
            foreach ($sales_zero_rated as $zt) {
                $zero_rated[$zt->sales_zero_rated_id] = array(
                    "sales_id" => $zt->sales_id,
                    "amount" => $zt->amount,
                    "name" => $zt->name,
                    "card_no" => $zt->card_no
                );
            }
            $local_tax = array();
            foreach ($sales_local_tax as $lt) {
                $local_tax[$lt->sales_local_tax_id] = array(
                    "sales_id" => $lt->sales_id,
                    "amount" => $lt->amount,
                );
            }
            $charges = array();
            foreach ($sales_charges as $ch) {
                $charges[$ch->charge_id] = array(
                        "name"  => $ch->charge_name,
                        "code"  => $ch->charge_code,
                        "amount"  => $ch->rate,
                        "absolute" => $ch->absolute,
                        "total_amount" => $ch->amount,
                        "rate"=>$ch->rate
                    );
            }
            $pfields = array();
            foreach ($payment_fields as $pf) {
                $pfields[$pf->field_id] = array(
                        "field_name"  => $pf->field_name,
                        "value"  => $pf->value,
                    );
            }
            if($asJson)
                echo json_encode(array('order'=>$order,"details"=>$details,"discounts"=>$discounts,"taxes"=>$tax,"no_tax"=>$no_tax,"zero_rated"=>$zero_rated,"payments"=>$pays,"charges"=>$charges,"local_tax"=>$local_tax,"pfields"=>$pfields));
            else
                return array('order'=>$order,"details"=>$details,"discounts"=>$discounts,"taxes"=>$tax,"no_tax"=>$no_tax,"zero_rated"=>$zero_rated,"payments"=>$pays,"charges"=>$charges,"local_tax"=>$local_tax,"pfields"=>$pfields);
        }
        public function get_orderss($asJson=true,$sales_id=null,$branch_code="",$brand=""){
            /*
             * -------------------------------------------
             *   Load receipt data
             * -------------------------------------------
            */
            $this->load->model('dine/cashier_model');
            // $imp_arr[] = implode(",",$sales_id);
            // $imp_arr[] = implode(',', array_map('add_quotes', $sales_id));
            // echo "<pre>",print_r($imp_arr),"</pre>";die();
            $orders = $this->cashier_model->get_trans_sales($sales_id,array(),'desc',null,$branch_code,$brand);
            $order = array();
            $details = array();
            foreach ($orders as $res) {
                $order[] = array(
                    "sales_id"=>$res->sales_id,
                    'ref'=>$res->trans_ref,
                    "type"=>$res->type,
                    "table_id"=>$res->table_id,
                    "table_name"=>$res->table_name,
                    "guest"=>$res->guest,
                    "serve_no"=>$res->serve_no,
                    "user_id"=>$res->user_id,
                    "customer_id"=>$res->customer_id,
                    "name"=>$res->username,
                    // "terminal_id"=>$res->terminal_id,
                    // "terminal_name"=>$res->terminal_name,
                    // "terminal_code"=>$res->terminal_code,
                    "shift_id"=>$res->shift_id,
                    "datetime"=>$res->datetime,
                    "amount"=>$res->total_amount,
                    "balance"=>round($res->total_amount,2) - round($res->total_paid,2),
                    "paid"=>$res->paid,
                    "printed"=>$res->printed,
                    "inactive"=>$res->inactive,
                    "waiter_id"=>$res->waiter_id,
                    "void_ref"=>$res->void_ref,
                    "reason"=>$res->reason,
                    "waiter_name"=>ucwords(strtolower($res->waiterfname." ".$res->waitermname." ".$res->waiterlname." ".$res->waitersuffix)),
                    "waiter_username"=>ucwords(strtolower($res->waiterusername))
                    // "pay_type"=>$res->pay_type,
                    // "pay_amount"=>$res->pay_amount,
                    // "pay_ref"=>$res->pay_ref,
                    // "pay_card"=>$res->pay_card,
                );
            }

            $args = array("trans_sales_menus.sales_id"=>$sales_id,"trans_sales_menus.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_menus.pos_id'] = $brand;
            }
            $order_menus = $this->cashier_model->get_trans_sales_menus(null,$args);

            $args = array("trans_sales_items.sales_id"=>$sales_id,"trans_sales_items.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_items.pos_id'] = $brand;
            }
            $order_items = $this->cashier_model->get_trans_sales_items(null,$args);

            $args = array("trans_sales_menu_modifiers.sales_id"=>$sales_id,"trans_sales_menu_modifiers.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_menu_modifiers.pos_id'] = $brand;
            }
            $order_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,$args);

            $args = array("trans_sales_discounts.sales_id"=>$sales_id,"trans_sales_discounts.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_discounts.pos_id'] = $brand;
            }
            $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,$args);

            $args = array("trans_sales_tax.sales_id"=>$sales_id,"trans_sales_tax.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_tax.pos_id'] = $brand;
            }
            $sales_tax = $this->cashier_model->get_trans_sales_tax(null,$args);

            $args = array("trans_sales_payments.sales_id"=>$sales_id,"trans_sales_payments.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_payments.pos_id'] = $brand;
            }
            $sales_payments = $this->cashier_model->get_trans_sales_payments(null,$args);

            $args = array("trans_sales_no_tax.sales_id"=>$sales_id,"trans_sales_no_tax.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_no_tax.pos_id'] = $brand;
            }
            $sales_no_tax = $this->cashier_model->get_trans_sales_no_tax(null,$args);

            $args = array("trans_sales_zero_rated.sales_id"=>$sales_id,"trans_sales_zero_rated.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_zero_rated.pos_id'] = $brand;
            }
            $sales_zero_rated = $this->cashier_model->get_trans_sales_zero_rated(null,$args);

            $args = array("trans_sales_charges.sales_id"=>$sales_id,"trans_sales_charges.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_charges.pos_id'] = $brand;
            }
            $sales_charges = $this->cashier_model->get_trans_sales_charges(null,$args);

            $args = array("trans_sales_local_tax.sales_id"=>$sales_id,"trans_sales_local_tax.branch_code"=>$branch_code);
            if($brand != ''){
                $args['trans_sales_local_tax.pos_id'] = $brand;
            }
            $sales_local_tax = $this->cashier_model->get_trans_sales_local_tax(null,$args);
            $pays = array();
            foreach ($sales_payments as $py) {
                $pays[$py->payment_id] = array(
                        "sales_id"      => $py->sales_id,
                        "payment_type"  => $py->payment_type,
                        "amount"        => $py->amount,
                        "to_pay"        => $py->to_pay,
                        "reference"     => $py->reference,
                        "card_type"     => $py->card_type,
                        "card_number"   => $py->card_number,
                        "approval_code"   => $py->approval_code,
                        "user_id"       => $py->user_id,
                        "datetime"      => $py->datetime,
                    );
            }
            foreach ($order_menus as $men) {
                $details[$men->line_id] = array(
                    "id"=>$men->sales_menu_id,
                    "menu_id"=>$men->menu_id,
                    "name"=>$men->menu_name,
                    "code"=>$men->menu_code,
                    "price"=>$men->price,
                    "qty"=>$men->qty,
                    "no_tax"=>$men->no_tax,
                    "discount"=>$men->discount,
                    "remarks"=>$men->remarks,
                    "free_user_id"=>$men->free_user_id,
                    "kitchen_slip_printed"=>$men->kitchen_slip_printed
                );
                $mods = array();
                foreach ($order_mods as $mod) {
                    if($mod->line_id == $men->line_id){
                        $mods[$mod->sales_mod_id] = array(
                            "id"=>$mod->mod_id,
                            "sales_mod_id"=>$mod->sales_mod_id,
                            "mod_group_id"=>$mod->mod_group_id,
                            "line_id"=>$mod->line_id,
                            "name"=>$mod->mod_name,
                            "price"=>$mod->price,
                            "qty"=>$mod->qty,
                            "discount"=>$mod->discount,
                            "kitchen_slip_printed"=>$mod->kitchen_slip_printed
                        );
                    }
                }
                $details[$men->line_id]['modifiers'] = $mods;
            }
            foreach ($order_items as $men){
                $details[$men->line_id] = array(
                    "id"=>$men->sales_item_id,
                    "menu_id"=>$men->item_id,
                    "name"=>$men->name,
                    "code"=>$men->code,
                    "price"=>$men->price,
                    "qty"=>$men->qty,
                    "no_tax"=>$men->no_tax,
                    "discount"=>$men->discount,
                    "remarks"=>$men->remarks,
                    "retail"=>1
                );
            }
            ### CHANGED #############
            $discounts = array();
            $persons = array();
            foreach ($sales_discs as $dc) {
                $discounts[$dc->disc_code] = array(
                        "no_tax"  => $dc->no_tax,
                        "guest" => $dc->guest,
                        "disc_rate" => $dc->disc_rate,
                        "disc_id" => $dc->disc_id,
                        "disc_code" => $dc->disc_code,
                        "disc_type" => $dc->type,
                        "fix" => $dc->fix,
                        "persons" => array()
                );
            }
            foreach ($sales_discs as $dc) {
                $pcode = $dc->code;
                $bday = "";
                if($dc->bday != "")
                    $bday = sql2Date($dc->bday);
                $person = array(
                    "name"  => $dc->name,
                    "code"  => $dc->code,
                    "bday"  => $bday,
                    "amount" => $dc->amount,
                    "disc_rate" => $dc->disc_rate,
                );
                if(isset($discounts[$dc->disc_code])){
                    $dscp =  $discounts[$dc->disc_code]['persons'];
                    $dscp[$pcode] = $person;
                    $discounts[$dc->disc_code]['persons'] = $dscp;
                }
            }
            ### CHANGED #############
            $tax = array();
            foreach ($sales_tax as $tx) {
                $tax[$tx->sales_tax_id] = array(
                        "sales_id"  => $tx->sales_id,
                        "name"  => $tx->name,
                        "rate" => $tx->rate,
                        "amount" => $tx->amount
                    );
            }
            $no_tax = array();
            foreach ($sales_no_tax as $nt) {
                $no_tax[$nt->sales_no_tax_id] = array(
                    "sales_id" => $nt->sales_id,
                    "amount" => $nt->amount,
                );
            }
            $zero_rated = array();
            foreach ($sales_zero_rated as $zt) {
                $zero_rated[$zt->sales_zero_rated_id] = array(
                    "sales_id" => $zt->sales_id,
                    "amount" => $zt->amount,
                );
            }
            $local_tax = array();
            foreach ($sales_local_tax as $lt) {
                $local_tax[$lt->sales_local_tax_id] = array(
                    "sales_id" => $lt->sales_id,
                    "amount" => $lt->amount,
                );
            }
            $charges = array();
            foreach ($sales_charges as $ch) {
                $charges[$ch->charge_id] = array(
                        "name"  => $ch->charge_name,
                        "code"  => $ch->charge_code,
                        "amount"  => $ch->rate,
                        "absolute" => $ch->absolute,
                        "total_amount" => $ch->amount
                    );
            }
            if($asJson)
                echo json_encode(array('order'=>$order,"details"=>$details,"discounts"=>$discounts,"taxes"=>$tax,"no_tax"=>$no_tax,"zero_rated"=>$zero_rated,"payments"=>$pays,"charges"=>$charges,"local_tax"=>$local_tax));
            else
                return array('order'=>$order,"details"=>$details,"discounts"=>$discounts,"taxes"=>$tax,"no_tax"=>$no_tax,"zero_rated"=>$zero_rated,"payments"=>$pays,"charges"=>$charges,"local_tax"=>$local_tax);
        }
        public function get_order_header($asJson=true,$sales_id=null,$args=array()){
            $this->load->model('dine/cashier_model');
            $this->load->model('dine/clock_model');
            $this->load->model('site/site_model');
            $orders = $this->cashier_model->get_trans_sales($sales_id,$args);
            $time = $this->site_model->get_db_now();
            foreach ($orders as $res) {
                $get_shift = $this->clock_model->get_shift_id(date2Sql($time),$res->user_id);
                $shift_id = $res->shift_id;
                if(count($get_shift) > 0){
                    $shift_id = $get_shift[0]->shift_id;
                }
                $order = array(
                    "sales_id"=>$res->sales_id,
                    "type"=>$res->type,
                    "user_id"=>$res->user_id,
                    "name"=>$res->username,
                    "terminal_id"=>$res->terminal_id,
                    "terminal_name"=>$res->terminal_name,
                    "shift_id"=>$shift_id,
                    "datetime"=>$res->datetime,
                    "amount"=>numInt($res->total_amount),
                    "balance"=>numInt($res->total_amount) - numInt($res->total_paid),
                    "paid"=>numInt($res->paid)
                );
            }
            if($asJson)
                echo json_encode($order);
            else
                return $order;
        }
        public function get_menu_categories($asJson=true){
            $this->load->model('dine/menu_model');
            $categories = $this->menu_model->get_menu_categories(null,true);
            $json = array();
            foreach ($categories as $cat) {
                $json[$cat->menu_cat_id] = array(
                    'name'=>$cat->menu_cat_name
                );
            }
            echo json_encode($json);
        }
        public function get_menu_cats($asJson=true){
            $this->load->model('dine/menu_model');
            $categories = $this->menu_model->get_menu_categories(null,true);
            $json = array();
            foreach ($categories as $cat) {
                $json[$cat->menu_cat_id] = array(
                    'name'=>$cat->menu_cat_name,
                    'id'=>$cat->menu_cat_id
                );
            }
            usort($json, function($a, $b) {
                return strcmp($a["name"], $b["name"]);
            });
            echo json_encode($json);
        }
        public function get_item_categories($asJson=true){
            $this->load->model('dine/settings_model');
            $categories = $this->settings_model->get_category(null,true);
            $json = array();
            foreach ($categories as $cat) {
                $json[$cat->cat_id] = array(
                    'name'=>$cat->name
                );
            }
            echo json_encode($json);
        }
        public function get_item_scanned(){
            $this->load->model('dine/items_model');
            $barcode = $this->input->post('barcode');
            $args['items.barcode'] = array('use'=>'where','val'=>$barcode);
            $item_list = $this->items_model->get_item(null,$args);
            if($item_list){
                $opt = array('cost'=>$item_list[0]->cost,'name'=>$item_list[0]->name);
                echo json_encode(array('error'=>"",'item_id'=>$item_list[0]->item_id,'opt'=>$opt));
            }
            else{
                echo json_encode(array('error'=>"Item Not Found.",'item_id'=>'','opt'=>''));
            }
        }    
        public function get_item_lists(){
            $this->load->model('dine/items_model');
            $args = array();
            $search = "";
            $code = "";
            $title = "";
            $args['items.type'] = array(2,3);
            if($this->input->post('search')){
                $search = $this->input->post('search');
                $title = "Search Results for \"".$search."\"";
                $args['items.barcode'] = array('use'=>'like','val'=>$search);
                $args['items.code'] = array('use'=>'or_like','val'=>$search);
                $args['items.name'] = array('use'=>'or_like','val'=>$search);
                $args['items.desc'] = array('use'=>'or_like','val'=>$search);
            }
            if($this->input->post('cat_id')){
                $title = $this->input->post('cat_name');
                $args['items.cat_id'] = $this->input->post('cat_id');
            }

            $item_list = $this->items_model->get_item(null,$args);
            $this->make->sUl(array('class'=>'ul-hover-blue'));
            $items = array();
            foreach ($item_list as $res) {
                $this->make->sLi(array('id'=>'retail-item-'.$res->item_id,'class'=>'retail-item','style'=>'padding:5px;padding-bottom:10px;padding-top:10px;border-bottom:1px solid #ddd;'));
                    $cost = $this->make->span($res->cost,array('class'=>'pull-right','return'=>true));            
                    $this->make->H(4,$res->name." ".$cost,array('style'=>'margin:0;padding:0;margin-left:10px;'));            
                    $this->make->H(5,$res->desc,array('style'=>'margin:0;padding:0;margin-left:10px;'));            
                $this->make->eLi();   
                $items[$res->item_id] = array(
                    "name"=> $res->name,
                    "cost"=> $res->cost
                );
            }
            $this->make->eUl();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'title'=>$title,'items'=>$items));
        }
        public function get_customers_lists(){
            $this->load->model('dine/items_model');
            $args = array();
            $search = "";
            $code = "";
            $title = "";
            if($this->input->post('search')){
                $search = $this->input->post('search');
                $title = "Search Results for \"".$search."\"";
                $args['customers.fname'] = array('use'=>'like','val'=>$search);
                $args['customers.mname'] = array('use'=>'or_like','val'=>$search);
                $args['customers.lname'] = array('use'=>'or_like','val'=>$search);
            }
            $item_list = $this->items_model->get_customers(null,$args);
            $this->make->sUl(array('class'=>'ul-hover-blue'));
            $items = array();
            foreach ($item_list as $res) {
                $this->make->sLi(array('id'=>'customer-item-'.$res->cust_id,'class'=>'customer-item','style'=>'padding:5px;padding-bottom:10px;padding-top:10px;border-bottom:1px solid #ddd;'));
                    $this->make->H(4,$res->fname." ".$res->mname." ".$res->lname,array('style'=>'margin:0;padding:0;margin-left:10px;'));            
                $this->make->eLi();   
                $items[$res->cust_id] = array(
                    "name"=> $res->fname." ".$res->mname." ".$res->lname,
                );
            }
            $this->make->eUl();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'title'=>$title,'items'=>$items));
        }
        public function scan_code($code=null){
            $this->load->model('dine/items_model');
            $args['items.type'] = array(2,3);
            $args['items.barcode'] = $code;
            $item_list = $this->items_model->get_item(null,$args);
            // echo $this->items_model->db->last_query();
            $error = "";
            $item = array();
            if(count($item_list) > 0){
                foreach ($item_list as $res) {
                    $item =  array(
                        "item_id" => $res->item_id,
                        "name"=> $res->name,
                        "cost"=> $res->cost
                    );
                    break;
                }    
            }
            else{
                $error = "Item not found.";
            }
            echo json_encode(array('error'=>$error,'item'=>$item));
        }
        ### NOT USED IN GETTING MENUS
        public function get_menus($cat_id=null,$item_id=null,$asJson=true){
            $this->load->model('dine/menu_model');
            $this->load->model('dine/cashier_model');
            $this->load->model('site/site_model');
            $menus = $this->menu_model->get_menus($item_id,$cat_id,true);
            $json = array();

            if(count($menus) > 0){
                $ids = array();
                foreach ($menus as $res) {
                    $json[$res->menu_id] = array(
                        "name"=>$res->menu_name,
                        "category"=>$res->menu_cat_id,
                        "cost"=>$res->cost,
                        "no_tax"=>$res->no_tax,
                        "free"=>$res->free,
                    );
                    $ids[] = $res->menu_id;
                }
                $promos = $this->cashier_model->get_menu_promos($ids);
                $prs = array();
                $prm = array();
                foreach ($promos as $pr) {
                    $prs[] = $pr->promo_id;
                    $prm[$pr->item_id][] = array('id'=>$pr->promo_id,'val'=>$pr->value,'abs'=>$pr->absolute);
                }

                $time = $this->site_model->get_db_now();
                $day = strtolower(date('D',strtotime($time)));
                $sched = $this->cashier_model->get_menu_promo_schedule($prs,$day,date2SqlDateTime($time));

                $schs = array();
                foreach ($sched as $sc) {
                    $schs[] = $sc->promo_id;
                }

                foreach ($json as $menu_id => $opt) {
                    if(isset($prm[$menu_id])){
                        foreach ($prm[$menu_id] as $p) {
                            if(in_array($p['id'], $schs)){
                                if($p['abs'] == 0){
                                    $opt['cost'] -= $pr->value;

                                }
                                else{
                                    $opt['cost'] -=  ($pr->value / 100) * $opt['cost'];
                                }
                                $json[$menu_id] = $opt;
                                break;
                            }
                        }####
                    }
                }

            }
            echo json_encode($json);
        }
        public function get_menus_search_sorted($cat_id=null,$item_id=null,$asJson=true){
            $this->load->model('dine/menu_model');
            $this->load->model('dine/cashier_model');
            $this->load->model('site/site_model');
            $search = $this->input->post('search');
            $menus = $this->menu_model->get_menus($item_id,$cat_id,true,$search);
            // echo $this->db->last_query(); die();
            $json = array();
            $now = $this->site_model->get_db_now();
            $day = strtolower(date('D',strtotime($now)));
            $time = strtolower(date('H:i:s',strtotime($now)));
            if(count($menus) > 0){
                $ids = array();
                $mnschds = array();
                foreach ($menus as $res) {
                    if($res->inactive == 0){
                        $json[$res->menu_id] = array(
                            "id"=>$res->menu_id,
                            "name"=>$res->menu_name,
                            "category"=>$res->menu_cat_id,
                            "cost"=>$res->cost,
                            "no_tax"=>$res->no_tax,
                            "free"=>$res->free,
                            "sched"=>$res->menu_sched_id,
                        );
                        $ids[] = $res->menu_id;
                        if($res->menu_sched_id != 0 || $res->menu_sched_id != "")
                            $mnschds[] = $res->menu_sched_id;
                    }
                }
                $ntfnd = array();
                if(count($mnschds) > 0){
                    $args['menu_schedule_details.menu_sched_id'] = $mnschds;
                    $args['menu_schedule_details.day'] = $day;
                    $sched = $this->site_model->get_tbl('menu_schedule_details',$args);
                    $mn_sched = array();
                    foreach ($json as $menu_id => $mn) {
                        foreach ($sched as $res) {
                            if($mn['sched'] == $res->menu_sched_id){
                                $mn_sched[$menu_id] = array('time_on'=>$res->time_on,'time_off'=>$res->time_off);
                            }
                        }                           
                    }
                    $found = array();
                    if(count($mn_sched) > 0){
                        foreach ($mn_sched as $menu_id => $mnsc) {
                            $tm  = strtotime($time);
                            $stm = strtotime($mnsc['time_on']);
                            $etm = strtotime($mnsc['time_off']);
                            if($etm > $stm ){
                                if($tm <= $stm){
                                    unset($json[$menu_id]);
                                    
                                }   
                                if($tm >= $etm){
                                    unset($json[$menu_id]);
                                                                             
                                }
                            }
                            $found[] = $menu_id;
                        }
                    }
                    if(count($mn_sched) > 0){
                        foreach ($json as $menu_id => $mn) {
                            if($mn['sched'] != "" && $mn['sched'] != 0){
                               if(!in_array($menu_id,$found)){
                                unset($json[$menu_id]);
                               }
                            }
                        }
                    }
                }  
                $promos = $this->cashier_model->get_menu_promos($ids);
                $prs = array();
                $prm = array();
                foreach ($promos as $pr) {
                    $prs[] = $pr->promo_id;
                    $prm[$pr->item_id][] = array('id'=>$pr->promo_id,'val'=>$pr->value,'abs'=>(int)$pr->absolute);
                }
                $time = $this->site_model->get_db_now();
                $day = strtolower(date('D',strtotime($time)));
                $sched = $this->cashier_model->get_menu_promo_schedule($prs,$day,date2SqlDateTime($time));
                $schs = array();
                foreach ($sched as $sc) {
                    $schs[] = $sc->promo_id;
                }
                foreach ($json as $menu_id => $opt) {
                    if(isset($prm[$menu_id])){
                        foreach ($prm[$menu_id] as $p) {
                            if(in_array($p['id'], $schs)){
                                if($p['abs'] == 1){
                                    $opt['cost'] -= $pr->value;
                                }
                                else{
                                    $opt['cost'] -=  ($pr->value / 100) * $opt['cost'];
                                }
                                $json[$menu_id] = $opt;
                                break;
                            }
                        }####
                    }
                }
            }
            usort($json, function($a, $b) {
                return strcmp($a["name"], $b["name"]);
            });
            echo json_encode($json);
        }
        public function get_menus_sorted($cat_id=null,$item_id=null,$asJson=true){
            $this->load->model('dine/menu_model');
            $this->load->model('dine/cashier_model');
            $this->load->model('site/site_model');
            $menus = $this->menu_model->get_menus($item_id,$cat_id,true);
            $now = $this->site_model->get_db_now();
            $day = strtolower(date('D',strtotime($now)));
            $time = strtolower(date('H:i:s',strtotime($now)));
            $json = array();
            if(count($menus) > 0){
                $ids = array();
                $mnschds = array();
                foreach ($menus as $res) {
                    $json[$res->menu_id] = array(
                        "id"=>$res->menu_id,
                        "name"=>$res->menu_name,
                        "category"=>$res->menu_cat_id,
                        "cost"=>$res->cost,
                        "no_tax"=>$res->no_tax,
                        "free"=>$res->free,
                        "sched"=>$res->menu_sched_id,
                    );
                    $ids[] = $res->menu_id;
                    if($res->menu_sched_id != 0 || $res->menu_sched_id != "")
                        $mnschds[] = $res->menu_sched_id;
                }
                #########################
                ### SCHEDULE
                #########################
                    $ntfnd = array();
                    if(count($mnschds) > 0){
                        $args['menu_schedule_details.menu_sched_id'] = $mnschds;
                        $args['menu_schedule_details.day'] = $day;
                        $sched = $this->site_model->get_tbl('menu_schedule_details',$args);
                        $mn_sched = array();
                        foreach ($json as $menu_id => $mn) {
                            foreach ($sched as $res) {
                                if($mn['sched'] == $res->menu_sched_id){
                                    $mn_sched[$menu_id] = array('time_on'=>$res->time_on,'time_off'=>$res->time_off);
                                }
                            }                           
                        }
                        $found = array();
                        if(count($mn_sched) > 0){
                            foreach ($mn_sched as $menu_id => $mnsc) {
                                $tm  = strtotime($time);
                                $stm = strtotime($mnsc['time_on']);
                                $etm = strtotime($mnsc['time_off']);
                                if($etm > $stm ){
                                    if($tm <= $stm){
                                        unset($json[$menu_id]);
                                        
                                    }   
                                    if($tm >= $etm){
                                        unset($json[$menu_id]);
                                                                                 
                                    }
                                }
                                $found[] = $menu_id;
                            }
                        }
                        if(count($mn_sched) > 0){
                            foreach ($json as $menu_id => $mn) {
                                if($mn['sched'] != "" && $mn['sched'] != 0){
                                   if(!in_array($menu_id,$found)){
                                    unset($json[$menu_id]);
                                   }
                                }
                            }
                        }
                    }                    
                #########################
                ### PROMO
                #########################
                    $promos = $this->cashier_model->get_menu_promos($ids);
                    $prs = array();
                    $prm = array();
                    foreach ($promos as $pr) {
                        $prs[] = $pr->promo_id;
                        $prm[$pr->item_id][] = array('id'=>$pr->promo_id,'val'=>$pr->value,'abs'=>(int)$pr->absolute);
                    }
                    $time = $this->site_model->get_db_now();
                    $day = strtolower(date('D',strtotime($time)));
                    $sched = $this->cashier_model->get_menu_promo_schedule($prs,$day,date2SqlDateTime($time));
                    $schs = array();
                    foreach ($sched as $sc) {
                        $schs[] = $sc->promo_id;
                    }
                    foreach ($json as $menu_id => $opt) {
                        if(isset($prm[$menu_id])){
                            foreach ($prm[$menu_id] as $p) {
                                if(in_array($p['id'], $schs)){
                                    if($p['abs'] == 1){
                                        $opt['cost'] -= $pr->value;
                                    }
                                    else{
                                        $opt['cost'] -=  ($pr->value / 100) * $opt['cost'];
                                    }
                                    $json[$menu_id] = $opt;
                                    break;
                                }
                            }####
                        }
                    }
            }
            usort($json, function($a, $b) {
                return strcmp($a["name"], $b["name"]);
            });
            echo json_encode($json);
        }
        public function get_menu_modifiers_wth_dflt($menu_id=null,$trans_id=null){
            $this->load->model('dine/menu_model');
            $this->load->model('dine/mods_model');
            $menu_mods = $this->menu_model->get_menu_modifiers($menu_id);
            $group = array();
            $grp = array();
            $dflts = array();
            if(count($menu_mods) > 0){
                foreach ($menu_mods as $res) {
                    $group[$res->mod_group_id] = array(
                        "name"=>$res->mod_group_name,
                        "mandatory"=>$res->mandatory,
                        "multiple"=>$res->multiple
                    );

                    $grp[] = $res->mod_group_id;
                }
                $details = $this->mods_model->get_modifier_group_details(null,$grp);
                $dets = array();
                foreach ($details as $det) {
                    if($det->mod_inactive == 0){
                        $dets=array(
                            "name"=>$det->mod_name,
                            "cost"=>$det->mod_cost,
                            "default"=>$det->default
                        );
                        
                        $group[$det->mod_group_id]['details'][$det->mod_id] = $dets;
                    }
                }
                
                if($this->input->post('add_defaults')){
                    $name  = 'trans_mod_cart';
                    $wagon = array();
                    if($this->session->userData($name)){
                        $wagon = $this->session->userData($name);
                    }
                    foreach ($group as $mod_group_id => $mod_grp) {
                        foreach ($mod_grp['details'] as $mod_id => $mod) {
                            if($mod['default'] == 1){
                                $defaults = array(
                                    'trans_id'=>$trans_id,
                                    'mod_group_id'=>$mod_group_id,
                                    'mod_id'=>$mod_id,
                                    'menu_id'=>$menu_id,
                                    'menu_name'=>$this->input->post('menu_name'),
                                    'mandatory'=>$mod_grp['mandatory'],
                                    'multiple'=>$mod_grp['multiple'],
                                    'name'=>$mod['name'],
                                    'cost'=>$mod['cost'],
                                    'qty'=>1
                                ); 
                                $wagon[] = $defaults;
                                $dflts[] = $defaults;
                            }
                        }
                    }    
                    $this->session->set_userData($name,$wagon);
                }


            }
            echo json_encode(array('group'=>$group,'dflts'=>$dflts));
        }
        public function get_menu_modifiers($menu_id=null,$trans_id=null){
            $this->load->model('dine/menu_model');
            $this->load->model('dine/mods_model');
            $menu_mods = $this->menu_model->get_menu_modifiers($menu_id);
            $group = array();
            $grp = array();
            $dflts = array();
            if(count($menu_mods) > 0){
                foreach ($menu_mods as $res) {
                    $group[$res->mod_group_id] = array(
                        "name"=>$res->mod_group_name,
                        "mandatory"=>$res->mandatory,
                        "multiple"=>$res->multiple
                    );

                    $grp[] = $res->mod_group_id;
                }
                $details = $this->mods_model->get_modifier_group_details(null,$grp);
                $dets = array();
                foreach ($details as $det) {
                    if($det->mod_inactive == 0){
                        $dets=array(
                            "name"=>$det->mod_name,
                            "cost"=>$det->mod_cost,
                            "default"=>$det->default
                        );
                        
                        $group[$det->mod_group_id]['details'][$det->mod_id] = $dets;
                    }
                }
                
                if($this->input->post('add_defaults')){
                    $name  = 'trans_mod_cart';
                    $wagon = array();
                    if($this->session->userData($name)){
                        $wagon = $this->session->userData($name);
                    }
                    foreach ($group as $mod_group_id => $mod_grp) {
                        foreach ($mod_grp['details'] as $mod_id => $mod) {
                            if($det->default == 1){
                                $defaults = array(
                                    'trans_id'=>$trans_id,
                                    'mod_id'=>$mod_id,
                                    'menu_id'=>$mod_group_id,
                                    'menu_name'=>$this->input->post('menu_name'),
                                    'mandatory'=>$mod_grp['mandatory'],
                                    'multiple'=>$mod_grp['multiple'],
                                    'name'=>$mod['name'],
                                    'cost'=>$mod['cost'],
                                    'qty'=>1
                                ); 
                                $wagon[] = $defaults;
                                $dflts[] = $defaults;
                            }
                        }
                    }    
                    $this->session->set_userData($name,$wagon);
                }


            }
            echo json_encode($group);
        }
        public function add_trans_modifier(){
            $wagon = array();
            $error = null;
            $name  = 'trans_mod_cart';
            $id = null;
            $row = null;
            if($this->session->userData($name)){
                $wagon = $this->session->userData($name);
            }
            $row = $this->input->post();

            if(count($wagon) > 0){
                // foreach($wagon as $key => $det) {
                //     // echo $det['mod_id'].' == '.$row['mod_id'].' && '.$det['trans_id'].' == '.$row['trans_id'];
                    // if($row['multiple'] == 0){
                //         // if($det['mod_id'] == $row['mod_id'] && $det['trans_id'] == $row['trans_id']){
                //         if($det['trans_id'] == $row['trans_id']){
                //             $error = 'You can only choose 1 on this modifier.';
                //             break;
                //         }
                //         else{
                //             $error = null;
                //         }
                //     }
                //     else{
                //        $error = null;
                //     }
                // }
                if($row['multiple'] < 1){
                    $row['multiple'] = 1;
                }    
                $ctr=1;
                foreach ($wagon as $key => $det) {
                    if($det['trans_id'] == $row['trans_id'] && $det['mod_group_id'] == $row['mod_group_id']){
                        $ctr++;
                    }
                }
                if($ctr > $row['multiple']){
                    $error = 'You can only choose up '.$row['multiple'].' on this modifier.';
                }


                if($error == null)
                        $wagon[] = $row;
            }
            else{
                $wagon[] = $row;
            }
            $id = max(array_keys($wagon));
            $this->session->set_userData($name,$wagon);
            echo json_encode(array("items"=>$row,"id"=>$id,"error"=>$error));
        }
        public function delete_trans_menu_modifier($trans_id=null){
            $wagon = array();
            $error = null;
            $name  = 'trans_mod_cart';
            $id = null;
            $row = null;
            $wagon = $this->session->userData($name);
            foreach ($wagon as $key => $det) {
                if($det['trans_id'] == $trans_id){
                    unset($wagon[$key]);
                }
            }
            $this->session->set_userData($name,$wagon);
            echo json_encode(array("items"=>$row,"id"=>$id));
        }
        public function update_trans_qty($trans_id=null){
            $wagon = array();
            $error = null;
            $name  = 'trans_cart';
            $wagon = $this->session->userData($name);
            $row = $wagon[$trans_id];
            $char = $this->input->post('operator');
            $val = $this->input->post('value');
            switch($char){
                case "times":
                    $row['qty'] *= $val;
                    break;
                case "equal":
                    $row['qty'] = $val;
                    break;
                case "plus";
                    $row['qty'] += $val;
                    break;
                case "minus";
                    $row['qty'] -= $val;
                    if($row['qty'] <= 0)
                        $row['qty'] = 1;
                    break;
            }
            $wagon[$trans_id] = $row;
            $this->session->set_userData($name,$wagon);
            echo json_encode(array("error"=>null,"qty"=>$row['qty']));
        }
        public function update_free_menu($trans_id=null){
            $wagon = array();
            $error = null;
            $name  = 'trans_cart';
            $wagon = $this->session->userData($name);
            $row = $wagon[$trans_id];
            $approver = $this->input->post('free_user_id');
            $row['cost'] = 0;
            $row['free_user_id'] = $approver;
            $wagon[$trans_id] = $row;
            $this->session->set_userData($name,$wagon);
        }
        public function add_trans_remark($trans_id=null){
            $wagon = array();
            $error = null;
            $name  = 'trans_cart';
            $wagon = $this->session->userData($name);
            $row = $wagon[$trans_id];
            $remarks = $this->input->post('line-remarks');
            $row['remarks'] = $remarks;
            $wagon[$trans_id] = $row;
            $this->session->set_userData($name,$wagon);
            echo json_encode(array("error"=>null,"remarks"=>$row['remarks']));
        }
        public function remove_trans_remark($trans_id=null){
            $wagon = array();
            $error = null;
            $name  = 'trans_cart';
            $wagon = $this->session->userData($name);
            $row = $wagon[$trans_id];
            if(isset($row['remarks']))
                unset($row['remarks']);
            $this->session->set_userData($name,$wagon);
            echo json_encode(array("error"=>null));
        }
        public function trans_exempt_to_tax(){
            $trans_cart = sess('trans_cart');
            $error = "";
            $tax = $this->get_tax_rates(false);
            if(count($tax) > 0){
                foreach ($trans_cart as $trans_id => $v) {
                    $v['no_tax'] = 1;
                    $trans_cart[$trans_id] = $v;
                }
            }
            sess_initialize('trans_cart',$trans_cart);
            echo json_encode(array("error"=>$error));
        }
        public function buy2take1_qty(){
            $trans_cart = array();
            if($this->session->userData('trans_cart')){
                $trans_cart = $this->session->userData('trans_cart');
            }
            $cart_total_qty = 0;
            foreach ($trans_cart as $trans_id => $trans){
                $cart_total_qty += $trans['qty'];
            }    
            $qty = floor($cart_total_qty/2);
            echo json_encode(array('qty'=>$qty));
        }
        public function total_trans($asJson=true,$cart=null,$disc_cart=null,$charge_cart=null,$zero_rated=null){
            $counter = sess('counter');
            if(is_array($zero_rated)){
                 // && isset($zero_rated['amount']) && $zero_rated['amount'] > 0
                 foreach ($zero_rated as $zid => $opt) {
                     if($opt['amount'] > 0){
                        $counter['zero_rated'] = 1;
                        break;
                     }
                 }            
            }
            $trans_cart = array();
            if($this->session->userData('trans_cart')){
                $trans_cart = $this->session->userData('trans_cart');
            }
            $trans_mod_cart = array();
            if($this->session->userData('trans_mod_cart')){
                $trans_mod_cart = $this->session->userData('trans_mod_cart');
                // echo var_dump($trans_mod_cart);
                // return false;
            }
            if(is_array($cart)){
                $trans_cart = $cart;
            }
            $total = 0;
            $gross = 0;
            $discount = 0;
            $zero_rated = 0;
            $vat_sales = 0;
            $non_vat_sales = 0;
            $cart_total_qty = 0;
            if(count($trans_cart) > 0){
                foreach ($trans_cart as $trans_id => $trans){
                    if(isset($trans['cost']))
                        $cost = $trans['cost'];
                    if(isset($trans['price']))
                        $cost = $trans['price'];

                    if(isset($trans['modifiers'])){
                        foreach ($trans['modifiers'] as $trans_mod_id => $mod) {
                            if($trans_id == $mod['line_id'])
                                $cost += $mod['price'];
                        }
                    }

                    else{
                        if(count($trans_mod_cart) > 0){
                            foreach ($trans_mod_cart as $trans_mod_id => $mod) {
                                if($trans_id == $mod['trans_id'])
                                    $cost += $mod['cost'];
                            }
                        }
                    }
                    if(isset($counter['zero_rated']) && $counter['zero_rated'] == 1){
                        $rate = 1.12;
                        // $cost = num(($cost / $rate),2);
                        $cost = ($cost / $rate);
                        $zero_rated += $trans['qty'] * $cost;
                    }
                    $total += $trans['qty'] * $cost;
                    $cart_total_qty += $trans['qty'];
                }
            }
            $gross = $total;
            $trans_disc_cart = sess('trans_disc_cart');
            if(is_array($disc_cart)){
                $trans_disc_cart = $disc_cart;
            }
            $discs = array();
            $less_vat = 0;
            $vatss = 0;
            $ps_counter = 0;
            if(count($trans_disc_cart) > 0 ){
                $error_disc = 0;
                foreach ($trans_disc_cart as $disc_id => $row) {
                    if(!isset($row['disc_type'])){
                        $error_disc = 1;
                    }
                    else{
                        if($row['disc_type'] == "")
                            $error_disc = 1;
                    }
                }
                if($error_disc == 0){
                    foreach ($trans_disc_cart as $disc_id => $row) {
                        $rate = $row['disc_rate'];
                        $guests = $row['guest'];

                        if($row['disc_type'] == 'equal'){
                            $no_persons = count($row['persons']);
                            $ps_counter += $no_persons;
                        }
                    }
                }
            }
 
            // echo $ps_counter.'ssss';
 
            if(count($trans_disc_cart) > 0 ){
                $error_disc = 0;
                foreach ($trans_disc_cart as $disc_id => $row) {
                    if(!isset($row['disc_type'])){
                        $error_disc = 1;
                    }
                    else{
                        if($row['disc_type'] == "")
                            $error_disc = 1;
                    }
                }
                if($error_disc == 0){
                    foreach ($trans_disc_cart as $disc_id => $row) {
                        // echo $total." ==== ";
                        $rate = $row['disc_rate'];
                        $guests = $row['guest'];
                        switch ($row['disc_type']) {
                            case "equal":
                                    $divi = $total/$row['guest'];
                                    $divi_less = $divi;
                                    $lv = 0;
                                    if($row['no_tax'] == 1){
                                        $divi_less = ($divi / 1.12);
                                        $lv = $divi - $divi_less;
                                    }
                                    $no_persons = count($row['persons']);
                                    foreach ($row['persons'] as $code => $per) {
                                        $discs[] = array('type'=>$row['disc_code'],'amount'=>($rate / 100) * $divi_less);
                                        $discount += ($rate / 100) * $divi_less;
                                        $less_vat += $lv;
                                    }
                                    $tl = $divi * ( abs($row['guest'] - $no_persons) );
                                    $tdl = ($divi_less * $no_persons) - $discount;
                                    $tl2 = $divi * ( abs($row['guest'] - $ps_counter) );
                                    $vat1 = $tl2 / 1.12;
                                    $vatss = ($vat1 * 0.12);

                                    // echo $vatss.'eeeee-';
                                    // $total = $tl + $tdl;
                                    // $total = ($divi * $row['guest']) - $discount;
                                    break;
                            default:
                                if($row['fix'] == 0){
                                    if(DISCOUNT_NET_OF_VAT && $row['disc_code'] != DISCOUNT_NET_OF_VAT_EX){
                                        $no_citizens = count($row['persons']);
                                        $total_net_vat = ($total / 1.12);                     
                                        foreach ($row['persons'] as $code => $per) {
                                            $discs[] = array('type'=>$row['disc_code'],'amount'=>($rate / 100) * $total_net_vat);
                                            $discount += ($rate / 100) * $total_net_vat;
                                        }
                                        // $total -= $discount;
                                    }
                                    else{
                                        $no_citizens = count($row['persons']);
                                        if($row['no_tax'] == 1)
                                            $total = ($total / 1.12);                     
                                        
                                        foreach ($row['persons'] as $code => $per) {
                                            $discs[] = array('type'=>$row['disc_code'],'amount'=>($rate / 100) * $total);
                                            $discount += ($rate / 100) * $total;
                                        }
                                        // $total -= $discount;
                                    }
                                }
                                else{
                                    $discs[] = array('type'=>$row['disc_code'],'amount'=>$rate);
                                    $discount += $rate;
                                    // $total -= $discount;
                                }

                        }
                        // echo $discount."<br>";
                    }
                }
            }
            // echo $total."<br>";
            // echo $less_vat."<br>";
            // echo $discount."<br>";
            $total -= $discount + $less_vat;
            $total_for_charge = $total - $less_vat;
            // echo $total;    
            $trans_charge_cart = sess('trans_charge_cart');
            if(is_array($charge_cart)){
                $trans_charge_cart = $charge_cart;
            }
            #CHARGES
            $charges = array();
            $total_charges = 0;
            $net_total = $total;

            if(ADD_CHARGES_NET_OF_VAT){
                $amount_cmpt = ($gross / 1.12);              
            }
            else{
                $amount_cmpt = $net_total;                
            }
            #
            #GET VATABLE AMOUNT (FOR SM)
            #
                if(MALL_ENABLED && MALL == 'megamall'){
                    $discountt = 0;
                    $taxable_amount = 0;
                    $not_taxable_amount = 0;
                    $discss = array();
                    $item_count = count($trans_cart);
                    foreach ($trans_cart as $trans_id => $v) {
                        if(isset($v['cost']))
                            $cost = $v['cost'];
                        if(isset($v['price']))
                            $cost = $v['price'];
                        ####################
                        if(isset($v['modifiers'])){
                            foreach ($v['modifiers'] as $trans_mod_id => $m) {
                                if($trans_id == $m['line_id']){
                                    $cost += $m['price'];
                                }
                            }
                        }
                        else{
                            if(count($trans_mod_cart) > 0){
                                foreach ($trans_mod_cart as $trans_mod_id => $m) {
                                    if($trans_id == $m['trans_id']){
                                        $cost += $m['cost'];
                                    }
                                }
                            }
                        }
                        ####################
                        foreach ($trans_disc_cart as $disc_id => $row) {
                            $rate = $row['disc_rate'];
                            switch ($row['disc_type']) {
                                case "equal":
                                        // $divi = $cost/$row['guest'];
                                        // $discount = ($rate / 100) * $divi;
                                        // $cost -= $discount;

                                        $divi = $cost/$row['guest'];
                                        $divi_less = $divi;
                                        if($row['no_tax'] == 1){
                                            $divi_less = ($divi / 1.12);
                                        }
                                        $no_persons = count($row['persons']);
                                        foreach ($row['persons'] as $code => $per) {
                                            $discss[] = array('type'=>$row['disc_code'],'amount'=>($rate / 100) * $divi_less);
                                            $discountt += ($rate / 100) * $divi_less;
                                        }
                                        $tl = $divi * ( abs($row['guest'] - $no_persons) );
                                        $tdl = ($divi_less * $no_persons) - $discountt;
                                        $cost = $tl - $tdl;
                                        // $cost = ($divi * $row['guest']) - $discount;
                                        break;
                                default:
                                    if($row['fix'] == 0){
                                        $no_citizens = count($row['persons']);
                                        if($row['no_tax'] == 1)
                                            $cost = ($cost / 1.12);                     
                                        foreach ($row['persons'] as $code => $per) {
                                            $discss[] = array('type'=>$row['disc_code'],'amount'=>($rate / 100) * $cost);
                                            $discountt += ($rate / 100) * $cost;
                                        }
                                        $cost -= $discountt;
                                    }
                                    else{
                                        $rate = $rate/$item_count;
                                        $discss[] = array('type'=>$row['disc_code'],'amount'=>$rate);
                                        $discountt = $rate; 
                                        $cost -= $discountt;

                                    }
                                    // $discount = ($rate / 100) * $cost;
                                    // $cost -= $discount;
                            }
                        }

                        if($v['no_tax'] == 0){
                            $taxable_amount += $cost * $v['qty'];
                        }
                        else{
                            $not_taxable_amount += $cost * $v['qty'];
                        }
                    }
                    if($not_taxable_amount > 0){
                        $has_no_tax_disc = false;
                        foreach ($trans_disc_cart as $disc_id => $row) {
                            if($row['no_tax'] == 1){
                                $has_no_tax_disc = true;
                                break;
                            }    
                        }    
                        if($has_no_tax_disc){
                            $amount_cmpt = $net_total;                       
                        }
                        else{
                            $amount_cmpt = ($taxable_amount/1.12) + $not_taxable_amount;
                        }
                    }
                    else{
                        if($taxable_amount > 0){
                            $amount_cmpt = ($taxable_amount/1.12);
                        }
                        else{
                            $amount_cmpt = $net_total;                                    
                        }
                    }
                }
            #
            # END GET VATABLE AMOUNT (FOR SM)
            # 

            if(count($trans_charge_cart) > 0 ){
                // echo $am."<br>";
                foreach ($trans_charge_cart as $charge_id => $opt) {
                    $charge_amount = $opt['amount'];
                    if($opt['absolute'] == 0){
                        // $charge_amount = ($opt['amount'] / 100) * $am;
                        // echo '<pre>',print_r($trans_disc_cart),'</pre>';
                        //Modified by Jed 11/28/2017
                        //wrong charges pag may discounts
                        if(count($trans_disc_cart) > 0 ){
                            $error_disc = 0;
                            foreach ($trans_disc_cart as $disc_id => $row) {
                                if(!isset($row['disc_type'])){
                                    $error_disc = 1;
                                }
                                else{
                                    if($row['disc_type'] == "")
                                        $error_disc = 1;
                                }
                            }

                            if($error_disc == 0){
                                $has_no_tax_disc = false;
                                foreach ($trans_disc_cart as $disc_id => $row) {
                                    if($row['no_tax'] == 1){
                                        $has_no_tax_disc = true;
                                        break;
                                    }    
                                }
                                // var_dump($has_no_tax_disc);
                                if($has_no_tax_disc){
                                    // echo $total." -- ".$vatss.'AAAAA';
                                    $charge_amount = ($opt['amount'] / 100) * ($total - $vatss);         
                                }
                                else{
                                    $charge_amount = ($opt['amount'] / 100) * ($total/1.12);
                                }
                                
                            }else{
                                $charge_amount = ($opt['amount'] / 100) * ($total/1.12);
                            }

                        }else{
                            $charge_amount = ($opt['amount'] / 100) * ($total/1.12);
                        }
                    }
                    $charges[$charge_id] = array('code'=>$opt['code'],
                                       'name'=>$opt['name'],
                                       'amount'=>$charge_amount,
                                       );
                    $total_charges += $charge_amount;
                }
                $total += $total_charges;
            }

            $loc_res = $this->site_model->get_tbl('settings',array(),array(),null,true,'local_tax',null,1);
            $local_tax = $loc_res[0]->local_tax;
            $lt_amt = 0;
            if($local_tax > 0){
                // $lt_amt = ($local_tax / 100) * $net_total;
                $lt_amt = ($local_tax / 100) * $amount_cmpt;
                $total += $lt_amt;
            }
            if($asJson)
                echo json_encode(array('total'=>$total,'discount'=>$discount,'discs'=>$discs,'charge'=>$total_charges,'charges'=>$charges,'zero_rated'=>$zero_rated,'local_tax'=>$lt_amt,'cart_total_qty'=>$cart_total_qty));
            else
                return array('total'=>$total,'discount'=>$discount,'discs'=>$discs,'charge'=>$total_charges,'charges'=>$charges,'zero_rated'=>$zero_rated,'local_tax'=>$lt_amt,'cart_total_qty'=>$cart_total_qty);
        }
        public function get_tax_rates($asJson=true,$tax_id=null){
            $this->load->model('dine/settings_model');
            $taxes = $this->settings_model->get_tax_rates($tax_id);
            $tax = array();
            foreach ($taxes as $res) {
                $tax[$res->tax_id] = array(
                    "name"=>$res->name,
                    "rate"=>$res->rate
                );
            }
            if($asJson)
                echo json_encode($tax);
            else
                return $tax;
        }
        public function submit_trans($asJson=true,$submit=null,$void=false,$void_ref=null,$cart=null,$mod_cart=null,$print=false,$split_id=null,$printKitSlip=false){
            $this->load->model('dine/cashier_model');
            $counter = sess('counter');
            $trans_cart = sess('trans_cart');
            $trans_mod_cart = sess('trans_mod_cart');
            $trans_type_cart = sess('trans_type_cart');
            $trans_disc_cart = sess('trans_disc_cart');
            $trans_charge_cart = sess('trans_charge_cart');
            $loyalty_card = sess('loyalty_card');
            $reasons = sess('reasons');
            
            $totals  = $this->total_trans(false,$cart);
            $total_amount = $totals['total'];
            $charges = $totals['charges'];
            $local_tax = $totals['local_tax'];
            $error = null;
            $act = null;
            $sales_id = null;
            $type = null;
            $type_id = SALES_TRANS;
            $print_echo = array();
            if($void === true){
                $type_id = SALES_VOID_TRANS;
            }

            if($void_ref == null || $void_ref == 0)
                $void_ref = null;

            $serve_no = 0;
            if(isset($trans_type_cart[0]['serve_no'])){
                $serve_no = $trans_type_cart[0]['serve_no'];
            }

            if(count($trans_cart) <= 0){
                $error = "Error! There are no items.";
            }
            else if(count($counter) <= 0){
                $error = "Error! Shift or User is invalid.";
            }
            else if(NEED_FOOD_SERVER && !isset($counter['waiter_id']) ){
                $error = "Please Select Serve No.";
            }
            else if(SERVER_NO_SETUP && $serve_no == 0 && $counter['type'] == 'counter' ){
                $error = "Please Select Serve No.";
            }
            else if(SERVER_NO_SETUP && $serve_no == 0 && $counter['type'] == 'takeout' ){
                $error = "Please Select Serve No.";
            }
            else{
                if(count($trans_disc_cart) > 0){
                    foreach ($trans_disc_cart as $disc_id => $row) {
                        if(!isset($row['disc_type'])){
                            $error = "Select Discount Type. If equally Divided or All Items.";
                        }
                        else{
                            if($row['disc_type'] == "")
                                $error = "Select Discount Type. If equally Divided or All Items.";
                        }
                    }
                    if($error != null){
                        if($asJson){
                            echo json_encode(array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type));
                            return false;
                        }
                        else{
                            return array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type);
                        }
                    }
                }


                if(is_array($cart)){
                    $trans_cart = $cart;
                }
                if(is_array($mod_cart)){
                    $trans_mod_cart = $mod_cart;
                }
                $type = $counter['type'];
                #save sa trans_sales
                $table = null;
                $guest = 0;
                
                $customer = null;
                if(isset($trans_type_cart[0]['table'])){
                    $table = $trans_type_cart[0]['table'];
                }
                if(isset($trans_type_cart[0]['guest'])){
                    $guest = $trans_type_cart[0]['guest'];
                }
                
                if(count($trans_disc_cart) > 0){
                    foreach ($trans_disc_cart as $disc_code => $dc) {
                        $guest = $dc['guest'];
                    }
                } 
                if(isset($trans_type_cart[0]['customer_id'])){
                    $customer = $trans_type_cart[0]['customer_id'];
                }
                if(count($loyalty_card) > 0){
                    foreach ($loyalty_card as $code => $row) {
                        $customer = $row['cust_id'];
                        // $loyalty = array(
                        //     "cust_id" => $row['cust_id'],
                        //     "code" => $row['code']
                        // );
                    }

                }
                $waiter = null;
                if(isset($counter['waiter_id'])){
                    $waiter = $counter['waiter_id'];
                }

                $splid = 0;
                if($split_id != null)
                    $splid = $split_id;
                 // $total_amount = number_format($total_amount, 2, '.', '');
                 $total_amount = $total_amount;
                 $trans_sales = array(
                    "user_id"       => $counter['user_id'],
                    "type_id"       => $type_id,
                    "shift_id"      => $counter['shift_id'],
                    "terminal_id"   => $counter['terminal_id'],
                    "type"          => $counter['type'],
                    "datetime"      => date2SqlDateTime($counter['datetime']),
                    "total_amount"  => $total_amount,
                    "void_ref"      => $void_ref,
                    "memo"          => null,
                    "table_id"      => $table,
                    "guest"         => $guest,
                    "serve_no"      => $serve_no,
                    "customer_id"   => $customer,
                    "waiter_id"     => $waiter,
                    "split"         => $splid
                );

                $user = $this->session->userdata('user');
                if(isset($counter['sales_id']) && $counter['sales_id'] != null){
                    $sales_id = $counter['sales_id'];
                    $this->cashier_model->update_trans_sales($trans_sales,$sales_id);

                    $log_id = $this->logs_model->add_logs('Sales Order',$user['id'],$user['full_name']." Updated Sales Order #".$sales_id,$sales_id);
                    if(LOCALSYNC){
                        $this->sync_model->update_trans_sales($sales_id);
                        $this->sync_model->add_logs($log_id);

                        //delete items
                        $this->sync_model->delete_trans_sales_menus($sales_id);
                        $this->sync_model->delete_trans_sales_items($sales_id);
                        $this->sync_model->delete_trans_sales_menu_modifiers($sales_id);
                        $this->sync_model->delete_trans_sales_discounts($sales_id);
                        $this->sync_model->delete_trans_sales_charges($sales_id);
                        $this->sync_model->delete_trans_sales_tax($sales_id);
                        $this->sync_model->delete_trans_sales_no_tax($sales_id);
                        $this->sync_model->delete_trans_sales_zero_rated($sales_id);
                        $this->sync_model->delete_trans_sales_local_tax($sales_id);
                    }


                    $this->cashier_model->delete_trans_sales_menus($sales_id);
                    $this->cashier_model->delete_trans_sales_items($sales_id);
                    $this->cashier_model->delete_trans_sales_menu_modifiers($sales_id);
                    $this->cashier_model->delete_trans_sales_discounts($sales_id);
                    $this->cashier_model->delete_trans_sales_charges($sales_id);
                    $this->cashier_model->delete_trans_sales_tax($sales_id);
                    $this->cashier_model->delete_trans_sales_no_tax($sales_id);
                    $this->cashier_model->delete_trans_sales_zero_rated($sales_id);
                    $this->cashier_model->delete_trans_sales_local_tax($sales_id);
                    $act="update";


                    if($submit === null || $submit == 0 || $submit == null)
                        site_alert('Transaction Updated.','success');
                }
                else{
                    $sales_id = $this->cashier_model->add_trans_sales($trans_sales);
                    $log_id = $this->logs_model->add_logs('Sales Order',$user['id'],$user['full_name']." Added New Sales Order #".$sales_id,$sales_id);
                    $act="add";

                    if(LOCALSYNC){
                        $this->sync_model->add_trans_sales($sales_id);
                        $this->sync_model->add_logs($log_id);
                    }
                }
                #save sa trans_sales_menus
                $trans_sales_menu = array();
                $trans_sales_items = array();
                foreach ($trans_cart as $trans_id => $v) {
                    $remarks = $serial_key = null;
                    $rate = (isset($trans_disc_cart[$trans_id]['disc_percent'])) ?  $trans_disc_cart[$trans_id]['disc_percent'] : 0 ;
                    $percent_disc = ($v['cost'] * $v['qty']) * ($rate / 100);
                    $percent_abs = (isset($trans_disc_cart[$trans_id]['disc_absolute'])) ? $trans_disc_cart[$trans_id]['disc_absolute'] : 0;
                    $total_disc = $percent_disc + $percent_abs;



                    if(isset($v['remarks']) && $v['remarks'] != ""){
                        $remarks = $v['remarks'];
                    }
                    $kitchen_slip_printed=0;
                    if(isset($v['kitchen_slip_printed']) && $v['kitchen_slip_printed'] != ""){
                        $kitchen_slip_printed = $v['kitchen_slip_printed'];
                    }
                    $free = null;
                    if(isset($v['free_user_id'])){
                        $free = $v['free_user_id'];
                    }
                    if(!isset($v['retail'])){
                        $trans_sales_menu[] = array(
                            "sales_id" => $sales_id,
                            "line_id" => $trans_id,
                            "menu_id" => $v['menu_id'],
                            "price" => $v['cost'],
                            "qty" => $v['qty'],
                            "no_tax" => $v['no_tax'],
                            "discount"=> 0,
                            "remarks"=>$remarks,
                            "kitchen_slip_printed"=>$kitchen_slip_printed,
                            "free_user_id" => $free,
                        );
                    }
                    else{
                        $trans_sales_items[] = array(
                            "sales_id" => $sales_id,
                            "line_id" => $trans_id,
                            "item_id" => $v['menu_id'],
                            "price" => $v['cost'],
                            "qty" => $v['qty'],
                            "no_tax" => $v['no_tax'],
                            "discount"=> 0,
                            "remarks"=>$remarks,
                        );
                    }
                }
                if(count($trans_sales_menu) > 0)
                {
                    $trans_id = $this->cashier_model->add_trans_sales_menus($trans_sales_menu);
                     if(LOCALSYNC){
                        $this->sync_model->add_trans_sales_menus($sales_id);
                    }
                }
                
                if(count($trans_sales_items) > 0)
                {
                    $this->cashier_model->add_trans_sales_items($trans_sales_items);
                    if(LOCALSYNC){
                        $this->sync_model->add_trans_sales_items($sales_id);
                    }
                }
                #save sa trans_sales_menu_modifiers
                if(count($trans_mod_cart) > 0){
                    $trans_sales_menu_modifiers = array();
                    foreach ($trans_mod_cart as $trans_mod_id => $m) {
                        $kitchen_slip_printed=0;
                        if(isset($m['kitchen_slip_printed']) && $m['kitchen_slip_printed'] != ""){
                            $kitchen_slip_printed = $m['kitchen_slip_printed'];
                        }
                        if(isset($trans_cart[$m['trans_id']])){
                            $trans_sales_menu_modifiers[] = array(
                                "sales_id" => $sales_id,
                                "line_id" => $m['trans_id'],
                                "menu_id" => $m['menu_id'],
                                "mod_group_id" => $m['mod_group_id'],
                                "mod_id" => $m['mod_id'],
                                "price" => $m['cost'],
                                "qty" => $m['qty'],
                                "discount"=> 0,
                                "kitchen_slip_printed"=>$kitchen_slip_printed
                            );
                        }
                    }
                    if(count($trans_sales_menu_modifiers) > 0)
                    {
                        $trans_id = $this->cashier_model->add_trans_sales_menu_modifiers($trans_sales_menu_modifiers);
                        if(LOCALSYNC){
                            $this->sync_model->add_trans_sales_menu_modifiers($sales_id);
                        }
                    }
                }
                #save sa trans_sales_discounts
                if(count($trans_disc_cart) > 0){
                    $trans_sales_disc_cart = array();
                    $total = 0;
                    foreach ($trans_cart as $trans_id => $trans){
                        if(isset($trans['cost']))
                            $cost = $trans['cost'];
                        if(isset($trans['price']))
                            $cost = $trans['price'];

                        if(isset($trans['modifiers'])){
                            foreach ($trans['modifiers'] as $trans_mod_id => $mod) {
                                if($trans_id == $mod['line_id'])
                                    $cost += $mod['price'];
                            }
                        }

                        else{
                            if(count($trans_mod_cart) > 0){
                                foreach ($trans_mod_cart as $trans_mod_id => $mod) {
                                    if($trans_id == $mod['trans_id'])
                                        $cost += $mod['cost'];
                                }
                            }
                        }
                        if(isset($counter['zero_rated']) && $counter['zero_rated'] == 1){
                            $rate = 1.12;
                            $cost = ($cost / $rate);
                            if(isset($zero_rated)){
                                $zero_rated += $v['qty'] * $cost;
                            }else{
                                $zero_rated = $v['qty'] * $cost;
                            }
                        }
                        $total += $trans['qty'] * $cost;
                    }

                    foreach ($trans_disc_cart as $disc_id => $dc) {
                        $dit = "";
                        if(isset($dc['items'])){
                            foreach ($dc['items'] as $lines) {
                                $dit .= $lines.",";
                            }
                            if($dit != "")
                                $dit = substr($dit,0,-1);                        
                        }
                        

                        $discount = 0;
                        $rate = $dc['disc_rate'];
                        switch ($dc['disc_type']) {
                            case "equal":
                                $divi = $total/$dc['guest'];
                                $divi_less = $divi;
                                if($dc['no_tax'] == 1){
                                    $divi_less = ($divi / 1.12);
                                }
                                $no_persons = count($dc['persons']);
                                // foreach ($row['persons'] as $code => $per) {
                                $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $divi_less);
                                $discount = ($rate / 100) * $divi_less;
                                // }
                                // $total = ($divi * $row['guest']) - $discount;

                                break;
                            default:
                                // $no_citizens = count($dc['persons']);
                                // if($dc['no_tax'] == 1)
                                //     $total = ($total / 1.12);                     
                                // $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $total);
                                // $discount = ($rate / 100) * $total;
                                if($dc['fix'] == 0){
                                    if(DISCOUNT_NET_OF_VAT && $row['disc_code'] != DISCOUNT_NET_OF_VAT_EX){
                                        $no_citizens = count($dc['persons']);
                                        $total_net_vat = ($total / 1.12);                     
                                        foreach ($dc['persons'] as $code => $per) {
                                            $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $total_net_vat);
                                            $discount += ($rate / 100) * $total_net_vat;
                                        }
                                        $total -= $discount; 
                                    }
                                    else{
                                        $no_citizens = count($dc['persons']);

                                        // echo $no_citizens; die();

                                        if($dc['no_tax'] == 1)
                                            $total = ($total / 1.12);                     
                                        foreach ($dc['persons'] as $code => $per) {
                                            $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $total);
                                            $discount += ($rate / 100) * $total;
                                        }
                                        $total -= $discount;                                        
                                    }    
                                }
                                else{
                                    $discs[] = array('type'=>$dc['disc_code'],'amount'=>$rate);
                                    $discount += $rate;
                                    $total -= $discount;
                                }
                                // }    
                        }
                        foreach ($dc['persons'] as $pcode => $oper) {
                            $dcBday = null;
                            if(isset($oper['bday']) && $oper['bday'] != "")
                                $dcBday = date2Sql($oper['bday']);
                            $trans_sales_disc_cart[] = array(
                                "sales_id"=>$sales_id,
                                "disc_id"=>$dc['disc_id'],
                                "disc_code"=>$dc['disc_code'],
                                "disc_rate"=>$dc['disc_rate'],
                                "no_tax"=>$dc['no_tax'],
                                "type"=>$dc['disc_type'],
                                "name"=>$oper['name'],
                                "bday"=>$dcBday,
                                "code"=>$oper['code'],
                                "items"=>$dit,
                                "guest"=>$dc['guest'],
                                "amount"=>$discount
                            );
                        }
                    }
                    if(count($trans_sales_disc_cart) > 0)
                    {
                        $this->cashier_model->add_trans_sales_discounts($trans_sales_disc_cart);
                          if(LOCALSYNC){
                            $this->sync_model->add_trans_sales_discounts($sales_id);
                        }
                    }
                }
                #save sa trans_sales_charges
                $total_charge = 0;
                if(count($trans_charge_cart) > 0){
                    $trans_sales_charge_cart = array();
                    foreach ($trans_charge_cart as $charge_id => $ch) {
                        $trans_sales_charge_cart[] = array(
                            "sales_id"=>$sales_id,
                            "charge_id"=>$charge_id,
                            "charge_code"=>$ch['code'],
                            "charge_name"=>$ch['name'],
                            "rate"=>$ch['amount'],
                            "absolute"=>$ch['absolute'],
                            "amount"=>$charges[$charge_id]['amount']
                        );
                        $total_charge += $charges[$charge_id]['amount'];
                    }
                    if(count($trans_sales_charge_cart) > 0)
                    {
                        $this->cashier_model->add_trans_sales_charges($trans_sales_charge_cart);

                         if(LOCALSYNC){
                            $this->sync_model->add_trans_sales_charges($sales_id);
                        }
                    }
                }
                #SAVE SA TRANS_SALES_TAX
                // $total_amount
                $tax = $this->get_tax_rates(false);
                $zero_rated = 0;
                $total = 0;
                if(count($tax) > 0){
                    $taxable_amount = 0;
                    $not_taxable_amount = 0;
                    foreach ($trans_cart as $trans_id => $v) {
                        $cost = $v['cost'];
                        if(count($trans_mod_cart) > 0){
                            foreach ($trans_mod_cart as $trans_mod_id => $m) {
                                if($trans_id == $m['trans_id']){
                                    $cost += $m['cost'];
                                }
                            }
                        }
                        if($v['no_tax'] == 0){
                            if(isset($counter['zero_rated']) && $counter['zero_rated'] == 1){
                                $rate = 1.12;
                                $cost = ($cost / $rate);
                                $zero_rated += $v['qty'] * $cost;
                            }
                            $total = $v['qty'] * $cost;
                            $taxable_amount += $total;
                            // echo $total."<br>";
                            foreach ($trans_disc_cart as $disc_id => $dc) {
                                $discount = 0;
                                $rate = $dc['disc_rate'];
                                switch ($dc['disc_type']) {
                                    case "equal":
                                        $divi = $total/$dc['guest'];
                                        $no_tax_persons = count($dc['persons']);
                                        if($dc['no_tax'] == 1){
                                            $divi_less = ($divi / 1.12);
                                            $less_vat = $divi - $divi_less;
                                            // $not_taxable_amount += $divi_less * $no_tax_persons;
                                            // $taxable_amount -= $divi + $less_vat;
                                            $no_tax = $divi_less * $no_tax_persons;
                                            $not_taxable_amount += $no_tax;
                                            $taxable_amount -= ($no_tax + ($less_vat * $no_tax_persons));
                                        }                             
                                        break;
                                    default:
                                        $no_citizens = count($dc['persons']);
                                        $no_cost_total = $total;
                                        if($dc['no_tax'] == 1){
                                            $no_cost_total = $total / 1.12;
                                            $taxable_amount = 0;
                                            $not_taxable_amount = $no_cost_total;
                                        }
                                        else{
                                            if($dc['fix'] == 0){
                                                if(DISCOUNT_NET_OF_VAT && $row['disc_code'] != DISCOUNT_NET_OF_VAT_EX){
                                                    $total_net_vat = $total / 1.12;
                                                    $discount = ($rate / 100) * $total_net_vat;                                                    
                                                    $taxable_amount -= $discount;
                                                    // $taxable_amount = $total - $discount;
                                                    $not_taxable_amount = 0;
                                                }
                                                else{
                                                    $discount = ($rate / 100) * $total;                                                    
                                                    $total_discount = $discount * $no_citizens;
                                                    // $taxable_amount = $total - $discount;
                                                    $taxable_amount -= $discount;
                                                    $not_taxable_amount = 0;
                                                    // echo $discount.'--'.$total_discount.'--'.$taxable_amount.'-<br><br><br>';
                                                }
                                            }
                                            else{
                                                $discount = $rate;
                                                $total_discount = $discount;
                                                // $taxable_amount = $total - $discount;
                                                $taxable_amount -= $discount;
                                                $not_taxable_amount = 0;                                                
                                            }
                                        }###
                                }
                            }
                        }
                        else{
                          if(isset($counter['zero_rated']) && $counter['zero_rated'] == 1){
                                $rate = 1.12;
                                $cost = ($cost / $rate);
                                $zero_rated += $v['qty'] * $cost;
                                $not_taxable_amount += $v['qty'] * $cost;
                          }
                          else{
                                $not_taxable_amount += $cost * $v['qty'];
                          }  
                        }
                    }
                    //remove charges 
                    $trans_sales_zero_rated[] = array(
                        "sales_id"=>$sales_id,
                        "amount"=>$zero_rated
                    );

                    if(count($trans_sales_zero_rated) > 0)
                        $this->cashier_model->add_trans_sales_zero_rated($trans_sales_zero_rated);
                    $trans_sales_no_tax[] = array(
                        "sales_id"=>$sales_id,
                        "amount"=>$not_taxable_amount
                    );
                    if(count($trans_sales_no_tax) > 0)
                        $this->cashier_model->add_trans_sales_no_tax($trans_sales_no_tax);


                    if(LOCALSYNC){
                        if(count($trans_sales_zero_rated) > 0)
                            $this->sync_model->add_trans_sales_zero_rated($sales_id);

                        if(count($trans_sales_no_tax) > 0)
                            $this->sync_model->add_trans_sales_no_tax($sales_id);
                    }

                    $am = $taxable_amount;
                    $trans_sales_tax = array();

                    foreach ($tax as $tax_id => $tx) {
                        $rate = ($tx['rate'] / 100);
                        $tax_value = ($am / ($rate + 1) ) * $rate;
                        // ($am / 1.12) * .12
                        $trans_sales_tax[] = array(
                            "sales_id"=>$sales_id,
                            "name"=>$tx['name'],
                            "rate"=>$tx['rate'],
                            "amount"=>$tax_value,
                        );
                        $am -= $tax_value;
                    }
                    
                    if(count($trans_sales_tax) > 0)
                    {
                        $this->cashier_model->add_trans_sales_tax($trans_sales_tax);
                        if(LOCALSYNC){
                            $this->sync_model->add_trans_sales_tax($sales_id);
                        }
                    }



                    
                }
                ### LOCAL TAX 
                if($local_tax > 0){
                    $trans_sales_local_tax[] = array(
                        "sales_id"=>$sales_id,
                        "amount"=>$local_tax
                    );
                    if(count($trans_sales_local_tax) > 0)
                    {
                        $this->cashier_model->add_trans_sales_local_tax($trans_sales_local_tax);
                         if(LOCALSYNC){
                            $this->sync_model->add_trans_sales_local_tax($sales_id);
                        }
                        
                    }

                    

                }
                #print
                if ($print == "true" || $print === true){
                    // $set = $this->cashier_model->get_pos_settings();
                    // $return_print_str=false,$add_reprinted=true,$splits=null,$include_footer=true
                    // $no_prints = $set->no_of_receipt_print;
                    // $print_echo = $this->print_sales_receipt($sales_id,false,false,true,null,true,$no_prints);
                    $print_echo = $this->print_sales_receipt($sales_id,false);
                }
                if ($printKitSlip == "true" || $printKitSlip === true){
                    $pet = $this->cashier_model->get_pos_settings();
                    $kitchen_printer = $pet->kitchen_printer_name;
                    if(KITCHEN_PRINTER){
                        $kitchen_printer = KITCHEN_PRINTER;
                    }
                    if(count($reasons) > 0){
                        foreach ($reasons as $ctrr => $re) {
                            $reason_id = $this->cashier_model->add_reasons($re);
                            if(LOCALSYNC){
                             $this->sync_model->add_reasons($reason_id);
                            }
                        }
                        $this->print_os_removes($sales_id,$reasons);
                    }
                    if($kitchen_printer != ""){
                        $this->print_os($sales_id);            
                    }
                }
            }
            // echo var_dump($kitchen_printer);
            // return false;
            $this->update_tbl_activity(null,true);
            if($asJson)
                echo json_encode(array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type));
            else
                return array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type);
        }
    #SETTLEMENT SECTION
        public function settle($sales_id=null){
            $this->load->model('site/site_model');
            $this->load->model('dine/cashier_model');
            $this->load->model('dine/settings_model');
            $this->load->helper('dine/cashier_helper');
            $this->load->helper('core/on_screen_key_helper');
            $loyalty_card = array(); 
            sess_initialize('loyalty_card',$loyalty_card);            
            $data = $this->syter->spawn(null);
            $order = $this->get_order(false,$sales_id);
            
            if(isset($order['order']['table_id']) && $order['order']['table_id'] != ""){
                if(isset($order['order']['table_id'])){
                    $error = $this->check_tbl_activity($order['order']['table_id'],false);
                    if($error == ""){
                        $this->update_tbl_activity($order['order']['table_id']);
                    }
                    else{
                        site_alert($error,'error');
                        header("Location:".base_url()."cashier");
                    }
                }
            }

            $trans_retake = false;
            if($this->input->get('trans_retake')){
                $trans_retake = true;
            }
            // $discounts = $this->settings_model->get_receipt_discounts();
            $totals = $this->total_trans(false,$order['details'],$order['discounts'],$order['charges'],$order['zero_rated']);
            
            $data['code'] = settlePage($order['order'],$order['details'],$order['discounts'],$totals,$order['charges'],$trans_retake);
            $data['add_css'] = array('css/cashier.css','css/onscrkeys.css');
            $data['add_js'] = array('js/on_screen_keys.js');

            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'settleJs';
            // $data['noNavbar'] = true;
            $this->load->view('cashier',$data);
        }
        public function loyalty(){
            $this->load->helper('core/on_screen_key_helper');
            $data = $this->syter->spawn(null,false);
            $data['code'] = onScrNumPwdPadOnly('loyalty-code');
            $data['add_css'] = array('css/pos.css','css/onscrkeys.css');
            $data['add_js'] = array('js/on_screen_keys.js');
            $this->load->view('load',$data);
        }
        public function loyalty_remove(){
            sess_clear('loyalty_card');
        }    
        public function loyalty_add(){
            $code = $this->input->post('pin');
            $card = array();
            $join['customers'] = array('content'=>'loyalty_cards.cust_id = customers.cust_id');
            $select = "loyalty_cards.*,customers.cust_id,customers.fname,customers.mname,customers.lname,customers.suffix";
            $details = $this->site_model->get_tbl('loyalty_cards',array('code'=>$code),array(),$join,true,$select);
            $error = 0;
            if(Count($details) > 0){
                $loyalty_card = array(); 
                sess_initialize('loyalty_card',$loyalty_card);
                $det = $details[0];
                $name = $det->fname." ".$det->mname." ".$det->lname." ".$det->suffix;
                $loyalty_card = array('name'=>$name,'cust_id'=>$det->cust_id,'code'=>$det->code,'card_id'=>$det->card_id);
                sess_add('loyalty_card',$loyalty_card,$det->code);
                $card = sess('loyalty_card');
                $msg = "Loyalty Card Added on transaction.";
            }
            else{
                $error = 1;
                $msg = "Card not Found.";
            }
            echo json_encode(array('error'=>$error,'msg'=>$msg,'card'=>$card));
        }
        public function get_order_payments($asJson=true,$sales_id=null,$payment_id=null){
            $this->load->model('dine/cashier_model');
            $payments = $this->cashier_model->get_trans_sales_payments($payment_id,array('trans_sales_payments.sales_id'=>$sales_id));
            $pays = array();
            foreach ($payments as $res) {
                $pays[$res->payment_id] = array(
                    "sales_id"=>$res->sales_id,
                    "type"=>$res->payment_type,
                    "amount"=>$res->amount,
                    "reference"=>$res->reference,
                    "datetime"=>$res->datetime,
                    "user_id"=>$res->user_id,
                    "username"=>$res->username,
                    "to_pay"=>$res->to_pay,
                    "card_type"=>$res->card_type
                );
            }
            if($asJson)
                echo json_encode($pays);
            else
                return $pays;
        }
        public function add_payment($sales_id=null,$amount=null,$type=null){
            $this->load->model('dine/cashier_model');
            $this->load->model('site/site_model');
            $loyalty_card = sess('loyalty_card');

            $order = $this->get_order_header(false,$sales_id);
            $error = "";
            $payments = $this->get_order_payments(false,$sales_id);
            $total_to_pay = $order['amount'];
            $paid = $order['paid'];
            $total_paid = 0;
            $balance = $order['balance'];
            if(count($payments) > 0){
                foreach ($payments as $pay_id => $pay) {
                    $total_paid += $pay['amount'];
                }
            }
            if($total_to_pay >= $total_paid)
                $total_to_pay -= $total_paid;
            else
                $total_to_pay = 0;
            $change = 0;
            

            $log_user = $this->session->userdata('user');
            if($total_to_pay >= 0){
                $payment = array(
                    'sales_id'      =>  $sales_id,
                    'payment_type'  =>  $type,
                    'amount'        =>  $amount,
                    'to_pay'        =>  $total_to_pay,
                    "user_id"       =>  $log_user['id'],
                    // 'reference'     =>  null,
                    // 'card_type'     =>  null
                );

                $update_tr = array();
                if ($type=="credit") {
                    $payment['card_type'] = $this->input->post('card_type');
                    $ma = $this->input->post('card_number');
                    for ($i=0,$x=strlen($ma); $i < $x-4; $i++) { $ma[$i] = "*"; }
                    $payment['card_number'] = $ma;
                    $payment['approval_code'] = $this->input->post('approval_code');
                } elseif ($type=="debit") {
                    $payment['card_number'] = $this->input->post('card_number');
                    $payment['approval_code'] = $this->input->post('approval_code');
                } elseif ($type=="smac") {
                    $payment['card_number'] = $this->input->post('card_number');
                } elseif ($type=="eplus") {
                    $payment['card_number'] = $this->input->post('card_number');
                } elseif ($type=="online") {
                    $payment['card_number'] = $this->input->post('card_number');
                } elseif ($type=="gc") {
                    $this->load->model('dine/gift_cards_model');
                    $gc_id = $this->input->post('gc_id');
                    $gc_code = $this->input->post('gc_code');

                    

                    $result = $this->gift_cards_model->get_gift_cards($gc_id,false);

                    if (empty($result)) {
                        echo json_encode(array('error'=>'Gift card is invalid'));
                        return false;
                    }


                    $this->gift_cards_model->update_gift_cards(array('inactive'=>1),$gc_id);
                    $payment['reference'] = $gc_code;
                    $payment['amount'] = $result[0]->amount;
                    $amount = $result[0]->amount;

                    if(LOCALSYNC){
                        $this->sync_model->update_gift_cards($gc_id);
                    }
                } elseif ($type=="loyalty") {
                    $card_code = $this->input->post('card_number');
                    $cards = $this->site_model->get_tbl('loyalty_cards',array('code'=>$card_code));
                    if(count($cards) > 0){
                        $card = $cards[0];
                        $payment['card_number'] = $card->code;
                        $points = $card->points;
                        if($points > $amount){
                            $litems = array(
                                "sales_id" => $sales_id,
                                "card_id" => $card->card_id,
                                "code" => $card->code,
                                "cust_id" => $card->cust_id,
                                "amount" => $amount,
                                "points" => $amount * -1,
                            );
                            $loyalty_id = $this->site_model->add_tbl('trans_sales_loyalty_points',$litems);
                            $this->cashier_model->update_loyalty_card_points($card->card_id,$amount * -1);

                            if(LOCALSYNC){
                                $this->sync_model->add_trans_sales_loyalty_points($loyalty_id);
                                $this->sync_model->update_loyalty_card_points($card->card_id);
                            }
                            $update_tr['customer_id']   = $card->cust_id;
                        }
                        else{
                            echo json_encode(array('error'=>'Loyalty Card points is insufficient'));
                            return false;                            
                        }
                    }
                    else{
                        echo json_encode(array('error'=>'Loyalty Card not found.'));
                        return false;
                    }
                } elseif ($type=="coupon") {
                    $coupon_id = $this->input->post('coupon_id');
                    $coupon_code = $this->input->post('coupon_code');

                    $today = date2Sql($this->site_model->get_db_now('sql'));
                    $cargs['card_no'] = $coupon_code;
                    $result = $this->site_model->get_tbl('coupons',$cargs);
                    if (empty($result)) {
                        echo json_encode(array('error'=>'Coupon is invalid'));
                        return false;
                    }

                    if(LOCALSYNC){
                        $this->sync_model->update_coupons($coupon_id);
                    }

                    $this->site_model->update_tbl('coupons','coupon_id',array('inactive'=>1),$coupon_id);
                    $payment['reference'] = $coupon_code;
                    $payment['amount'] = $result[0]->amount;
                    $amount = $result[0]->amount;

                } elseif ($type=="chit") {
                    $payment['user_id'] = $this->input->post('manager_id');
                } elseif ($type=="deposit") {
                    $payment['reference'] = $this->input->post('customer_id');
                    $update_tr['customer_id'] = $this->input->post('customer_id');
                    $cb = array(
                        'trans_ref'             => $sales_id,
                        'type_id'               => SALES_TRANS,
                        'payment'               => 1,
                        'cust_id'               => $this->input->post('customer_id'),
                        "amount"                => ($amount * -1),
                        "user_id"               => $log_user['id'],
                        "pos_id"                => TERMINAL_ID
                    );


                    $cb_id = $this->site_model->add_tbl('customers_bank',$cb,array('datetime'=>'NOW()'));

                        if(LOCALSYNC){
                            $this->sync_model->add_customers_bank($cb_id);
                        }

                } elseif ($type=="foodpanda") {
                    $payment['approval_code'] = $this->input->post('card_number');
                } elseif ($type=="check") {
                    $payment['card_number'] = $this->input->post('bank');
                    $payment['approval_code'] = $this->input->post('cdate');
                    $payment['reference'] = $this->input->post('check_no');
                }
                $curr_shift_id = $order['shift_id'];
                $time = $this->site_model->get_db_now();
                $get_curr_shift = $this->clock_model->get_shift_id(date2Sql($time),$log_user['id']);
                if(count($get_curr_shift) > 0){
                    $curr_shift_id = $get_curr_shift[0]->shift_id;
                }


                $payment_id = $this->cashier_model->add_trans_sales_payments($payment);
                
                if(LOCALSYNC){
                    $this->sync_model->add_trans_sales_payments($payment_id);
                }

                $new_total_paid = 0;
                if($amount > $total_to_pay){
                    $new_total_paid = $order['amount'];
                    $balance = 0;
                }
                else{
                    $new_total_paid = $total_paid+$amount;
                    $balance = $balance - $amount;
                    // $balance = $total_to_pay - $amount;
                }
                $update_tr['total_paid'] = $new_total_paid; 
                $update_tr['user_id']    = $log_user['id']; 
                $update_tr['shift_id']   = $curr_shift_id; 
                if(numInt($balance) == 0) {
                    if(count($loyalty_card) > 0){
                        foreach ($loyalty_card as $code => $row) {
                            $update_tr['customer_id']   = $row['cust_id']; 
                        }
                    }
                }
                // var_dump($payment);

                $this->cashier_model->update_trans_sales($update_tr,$sales_id);

                if(LOCALSYNC){
                    $this->sync_model->update_trans_sales($sales_id);
                }

                $log_user = $this->session->userdata('user');
                $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Added Payment ".$amount." on Sales Order #".$sales_id,$sales_id);

                if(LOCALSYNC){
                    $this->sync_model->add_logs($log_id);
                }

                if (numInt($balance) == 0) {
                //     // if ($paid == 0) {
                        // $this->finish_trans($sales_id,true);
                        // $this->finish_trans($sales_id,true);
                        $toMain = false;
                        if($this->input->post('trans_retake'))
                            $toMain = true;
                        $this->finish_trans($sales_id,true,false,$toMain);

                        $set = $this->cashier_model->get_pos_settings();

                        ###############
                        ## FOR LOYALTY
                        if(count($loyalty_card) > 0){
                            $for_amt = $set->loyalty_for_amount;
                            $to_points = $set->loyalty_to_points;
                            $num_points = floor($new_total_paid / $for_amt);
                            $total_points = $to_points * $num_points;

                            $litems = array();
                            $card_id = array();
                            foreach ($loyalty_card as $code => $row) {
                                $litems = array(
                                    "sales_id" => $sales_id,
                                    "card_id" => $row['card_id'],
                                    "code" => $row['code'],
                                    "cust_id" => $row['cust_id'],
                                    "amount" => $new_total_paid,
                                    "points" => $total_points,
                                );

                                $card_id[$row['card_id']] = $total_points;
                            }
                            if(count($litems) > 0){
                                $this->site_model->add_tbl('trans_sales_loyalty_points',$litems);
                                foreach ($card_id as $cid => $pt) {
                                    $this->cashier_model->update_loyalty_card_points($cid,$pt);
                                     if(LOCALSYNC){
                                        $this->sync_model->update_loyalty_card_points($cid);
                                     }
                                }


                            }
                        }   
                        ###############




                        $no_prints = $set->no_of_receipt_print;
                        $order_slip_prints = $set->no_of_order_slip_print;
                        $approved_by = null;
                        if($type == 'chit'){
                            $approved_by = $payment['user_id'];
                            $app = $this->site_model->get_user_details($approved_by);
                            $log_id = $this->logs_model->add_logs('Sales Order',$app->id,$app->fname." ".$app->mname." ".$app->lname." ".$app->suffix." Approved CHIT Payment ".$amount." on Sales Order #".$sales_id,$sales_id);
                             if(LOCALSYNC){
                                 $this->sync_model->add_logs($log_id);
                            }

                        }


                        $this->print_os($sales_id);
 //                        if(EXECUTE_MASTER_WITH_EVERY_SALES){
 //                              // $this->load->model('core/master_model');
 //                              // $this->master_model->execute_migration();
 //                             $str = exec('mastercall.bat'); 

 // // var_dump($str);
 //                        }

                        $print_echo = $this->print_sales_receipt($sales_id,false,false,true,null,true,$no_prints,$order_slip_prints,$approved_by,false,true);
                        
                        if(MALL_ENABLED){
                            if(MALL == 'megamall'){
                                $this->sm_file($order['datetime']);
                            }
                        }

                //     // }
                }
                // if($paid == 0){
                //     $move = true;
                // }
                // else
                //     $move = false;
                // if(in_array($type, array([0]=>'cash'))){
                if ($type == 'cash') {
                    if($amount > $total_to_pay){
                        $change = $amount - $total_to_pay;
                    }
                }
            }
            else{
                $error = 'Invalid Amount.';
            }
            echo json_encode(array('error'=>$error,'change'=>$change,'tendered'=>$amount,'balance'=>num($balance) ));


            if(empty($error)){
 //                if(EXECUTE_MASTER_WITH_EVERY_SALES){
 //                              // $this->load->model('core/master_model');
 //                              // $this->master_model->execute_migration();
 //                             $str = exec('mastercall.bat'); 

 // // var_dump($str);
 //                        }

            }
        }
        public function delete_payment($payment_id=null,$sales_id=null){
            $this->load->model('dine/cashier_model');
            $order = $this->get_order_header(false,$sales_id);
            $error = "";
            if($order['paid'] == 1){
                $error = "paid";
                echo json_encode(array('error'=>$error));
            }else{
                if(LOCALSYNC){
                    $this->sync_model->delete_trans_sales_payments($payment_id); // delete in main before ot deletes on dine
                    $this->sync_model->update_trans_sales($sales_id);
                }                    
                $this->cashier_model->delete_trans_sales_payments($payment_id);
                $payment = $this->get_order_payments(false,$sales_id);
                $error = "";
                $balance = 0;
                $total_paid = 0;
                foreach ($payment as $payment_id => $pay) {
                    $total_paid += $pay['amount'];
                }
                $this->cashier_model->update_trans_sales(array('total_paid'=>$total_paid),$sales_id);
                echo json_encode(array('error'=>$error,'balance'=>$order['amount'] - $total_paid));
            }
        }
        public function finish_trans($sales_id=null,$move=false,$void=false,$toMain=false){
            $this->load->model('dine/cashier_model');
            $this->load->model('dine/items_model');
            $this->load->model('core/trans_model');
            $loc_id = 1;
            $trans_type = SALES_TRANS;
            if($void)
                $trans_type = SALES_VOID_TRANS;
            $ref = $this->trans_model->get_next_ref($trans_type);
            $this->trans_model->db->trans_start();
                $this->trans_model->save_ref($trans_type,$ref);
                $this->cashier_model->update_trans_sales(array('trans_ref'=>$ref,'paid'=>1),$sales_id);

                if(LOCALSYNC){
                    $this->sync_model->update_trans_sales($sales_id);
                }

                $log_user = $this->session->userdata('user');
                $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Settled Payment on Sales Order #".$sales_id." Reference #".$ref,$sales_id);
                

                if(LOCALSYNC){
                    $this->sync_model->add_logs($log_id);
                }


                if($move || $move == "true"){
                    $opts = array(
                        "type_id" => $trans_type,
                        "trans_id" => $sales_id,
                        "trans_ref" => $ref,
                    );
                    if($void)
                        $rrr = true;
                    else
                        $rrr = false;
                    $items = $this->order_items_used($sales_id,$rrr);
                    if(count($items) > 0 ){
                        $this->items_model->move_items($loc_id,$items,$opts);
                    }
                }
                $this->update_tbl_activity(0,true);   

                // if(LOCALSYNC){
                //     $this->sync_model->update_table_activityc($log_id);
                // }


                // tempory disable the transfer to main

                // if($toMain){
                //     $this->order_to_main($sales_id);
                // }

            $this->trans_model->db->trans_complete();
        }
        public function settle_transactions($sales_id=null){
            $payments = $this->get_order_payments(false,$sales_id);
            $this->make->sDiv(array('class'=>'pay-row-list','style'=>'padding:10px;'));
            $icons = array(
                "cash"=>'money',
                "credit"=>'credit-card',
                "debit"=>'credit-card',
                "gift"=>'gift',
                "check"=>'check-square-o',
            );
            $ids = array();
            foreach ($payments as $payment_id => $pay) {
                $this->make->sDiv(array('class'=>'pay-row-div bg-blue','id'=>'pay-row-div-'.$payment_id));
                    $this->make->sDivRow();
                        $this->make->sDivCol(2,'left',0,array('style'=>'margin-right:20px;'));
                            $this->make->H(3,fa('fa-'.$icons[$pay['type']].' fa-3x fa-fw'),array('class'=>'headline'));
                        $this->make->eDivCol();
                        $this->make->sDivCol(2);
                            $this->make->H(5,strtoupper($pay['type']));
                            $this->make->H(5,strtoupper(sql2DateTime($pay['datetime'])));
                        $this->make->eDivCol();
                        $this->make->sDivCol(5);
                            $this->make->H(5,'Tendered: PHP '.strtoupper(num($pay['amount'])));
                            $change = 0;
                            if($pay['amount'] > $pay['to_pay'])
                                $change = $pay['amount'] - $pay['to_pay'];
                            $this->make->H(5,'Change:   PHP '.strtoupper(num($change)));
                            $this->make->H(5,strtoupper($pay['username']));
                        $this->make->eDivCol();
                        $this->make->sDivCol(2,'right',0,array('style'=>'margin-top:10px;'));
                            $this->make->button(fa('fa-ban fa-lg fa-fw').' VOID',array('id'=>'void-payment-btn-'.$payment_id,'ref'=>$payment_id,'class'=>'btn-block settle-btn-red double'));
                        $this->make->eDivCol();
                    $this->make->eDivRow();
                $this->make->eDiv();
                $ids[] = $payment_id;
            }
            $this->make->eDiv();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'ids'=>$ids));
        }
        public function manager_settle_transactions($sales_id=null){
            // echo "Sales ID : ".$sales_id."<br>";
            $payments = $this->get_order_payments(false,$sales_id);
            // echo $this->db->last_query();
            $this->make->sDiv(array('class'=>'pay-row-list','style'=>'padding:10px; background-color:#0073b7;'));
            $icons = array(
                "cash"=>'money',
                "credit"=>'credit-card',
                "debit"=>'credit-card',
                "gift"=>'gift',
                "check"=>'check-square-o',
            );
            $ids = array();
            foreach ($payments as $payment_id => $pay) {
                $this->make->sDiv(array('class'=>'pay-row-div bg-blue','id'=>'pay-row-div-'.$payment_id));
                    $this->make->sDivRow(array('class'=>'bg-blue'));
                        $this->make->sDivCol(2,'left',0,array('style'=>'margin-right:20px;', 'class'=>'bg-blue'));
                            $this->make->H(3,fa('fa-'.$icons[$pay['type']].' fa-3x fa-fw'),array('class'=>'headline'));
                        $this->make->eDivCol();
                        $this->make->sDivCol(2,'',0,array('class'=>'bg-blue'));
                            // $this->make->H(5,'Sales ID #'.$sales_id); // !!!
                            $this->make->H(5,strtoupper($pay['type']));
                            $this->make->H(5,strtoupper(sql2DateTime($pay['datetime'])));
                        $this->make->eDivCol();
                        $this->make->sDivCol(5,'',0,array('class'=>'bg-blue'));
                            $this->make->H(5,'Tendered: PHP '.strtoupper(num($pay['amount'])));
                            $change = 0;
                            if($pay['amount'] > $pay['to_pay'])
                                $change = $pay['amount'] - $pay['to_pay'];
                            $this->make->H(5,'Change:   PHP '.strtoupper(num($change)));
                            $this->make->H(5,strtoupper($pay['username']));
                        $this->make->eDivCol();
                        // $this->make->sDivCol(2,'right',0,array('style'=>'margin-top:10px;'));
                            // $this->make->button(fa('fa-ban fa-lg fa-fw').' VOID',array('id'=>'void-payment-btn-'.$payment_id,'ref'=>$payment_id,'class'=>'btn-block settle-btn-red double'));
                        // $this->make->eDivCol();
                        $this->make->sDivCol(2,'right',0,array('style'=>'margin-top:10px;', 'class'=>'bg-blue'));
                           $this->make->H(5,"Sales ID #".$sales_id);
                        $this->make->eDivCol();
                    $this->make->eDivRow();
                $this->make->eDiv();
                $ids[] = $payment_id;
            }
            $this->make->eDiv();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'ids'=>$ids));
        }
        public function order_items_used($sales_id=null,$add=false){
            $this->load->model('dine/cashier_model');
            $this->load->model('dine/menu_model');
            $this->load->model('dine/mods_model');
            $this->load->model('dine/items_model');
            $order = $this->get_order(false,$sales_id);
            $details = $order['details'];
            $menus = array();
            $mods = array();
            $items = array();
            foreach ($details as $det) {
                if(!isset($det['retail'])){
                    $menus[] = $det['menu_id'];
                    if(count($det['modifiers']) > 0){
                        foreach ($det['modifiers'] as $mod_id => $mod) {
                            $mods[] = $mod['id'];
                        }
                    }
                }
                else{
                    $items[] = $det['menu_id'];
                }
            }
            $me = array();
            $itms = array();
            $menu_recipe = array();
            if(count($menus) > 0)
                $menu_recipe = $this->menu_model->get_recipe_items($menus);
            if(count($items) > 0){
                $menu_items = $this->items_model->get_item($items);  
                foreach ($menu_items as $itm) {
                    $itms[$itm->item_id] = array('item_uom'=>$itm->uom);
                }      
            }
            
            foreach ($menu_recipe as $mn) {
                $me[$mn->menu_id][$mn->item_id] = array('item_uom'=>$mn->uom,'item_qty'=>$mn->qty);
            }
            $mods_recipe = array();
            if(count($mods) > 0)
                $mods_recipe = $this->mods_model->get_modifier_recipe(null,$mods);
            $mo = array();
            foreach ($mods_recipe as $mn) {
                $mo[$mn->mod_id][$mn->item_id] = array('item_uom'=>$mn->uom,'item_qty'=>$mn->qty);
            }
            $items = array();
            foreach ($details as $line_id => $det) {
                $mul = $det['qty'];
                if(!isset($det['retail'])){
                    if(isset($me[$det['menu_id']])){
                       foreach ($me[$det['menu_id']] as $item_id => $opt) {
                         
                           if(isset($items[$item_id])){
                                if($add)
                                    $items[$item_id]['qty'] += ($mul * $opt['item_qty']);
                                else
                                    $items[$item_id]['qty'] += (($mul * $opt['item_qty']) * -1);

                                $items[$item_id]['uom'] = $opt['item_uom'];
                           }
                           else{
                                if($add)
                                    $items[$item_id]['qty'] = ($mul * $opt['item_qty']);
                                else
                                    $items[$item_id]['qty'] = (($mul * $opt['item_qty']) * -1);
                                $items[$item_id]['uom'] = $opt['item_uom'];
                           }

                       }
                    }
                    #
                    if(count($det['modifiers']) > 0){
                        foreach ($det['modifiers'] as $mod_id => $mod) {
                            if(isset($mo[$mod['id']])){
                                foreach ($mo[$mod['id']] as $mod_item_id => $mopt) {
                                   if(isset($items[$mod_item_id])){
                                        if($add)
                                            $items[$mod_item_id]['qty'] += ($mul * $mopt['item_qty']);
                                        else
                                            $items[$mod_item_id]['qty'] += (($mul * $mopt['item_qty']) * -1);
                                        $items[$mod_item_id]['uom'] = $mopt['item_uom'];
                                   }
                                   else{
                                        if($add)
                                            $items[$mod_item_id]['qty'] = ($mul * $mopt['item_qty']);
                                        else
                                            $items[$mod_item_id]['qty'] = (($mul * $mopt['item_qty']) * -1);
                                        $items[$mod_item_id]['uom'] = $mopt['item_uom'];
                                   }
                               }
                            }
                            #
                        }
                    }
                    #
                }
                else{
                   if(isset($itms[$det['menu_id']])){
                        if(isset($items[$det['menu_id']])){
                            if($add)
                                $items[$det['menu_id']]['qty'] += $det['qty'];
                            else
                                $items[$det['menu_id']]['qty'] += ($det['qty'] * -1);

                            $items[$det['menu_id']]['uom'] = $itms[$itm->item_id]['item_uom'];
                       }
                       else{
                            if($add)
                                $items[$det['menu_id']]['qty'] = $det['qty'];
                            else
                                $items[$det['menu_id']]['qty'] = ($det['qty'] * -1);
                            $items[$det['menu_id']]['uom'] = $itms[$itm->item_id]['item_uom'];
                       }
                   } 
                }
            }
            #
            return $items;
        }
        public function get_custs_deposit_amount($cust_id=null){
            $json = array();
            $args['customers.inactive'] = 0;
            $args['customers.cust_id'] = $cust_id;
            $select = " customers.cust_id,customers.fname,customers.mname,customers.lname,customers.suffix,
                        SUM(customers_bank.amount) AS cust_money,
                        MAX(customers_bank.datetime) AS last_deposit";
            $join["customers_bank"] = array('content'=>"customers.cust_id = customers_bank.cust_id");
            $order['last_deposit'] = 'desc';
            $group = 'customers_bank.cust_id';
            $items = $this->site_model->get_tbl('customers',$args,$order,$join,true,$select,$group);
            if(count($items) > 0){
                $res = $items[0];
                $json = array(
                    'cust_id'=>$res->cust_id,
                    'amount'=>$res->cust_money,
                    'full_name'=>$res->fname." ".$res->mname." ".$res->lname." ".$res->suffix
                );
            }
            echo json_encode(array('result'=>$json));
        }
    #PRINTING SECTION    
        public function reprint_receipt_previous($sales_id=null){
            $this->print_sales_receipt($sales_id,false,false,true,null,true,1,0,null,true);           
        }
        public function print_testing(){
            $print_str = "";
            $wrap = wordwrap('Hapchan Robinson Cebu Restaurant',25,"|#|");
            $exp = explode("|#|", $wrap);
            foreach ($exp as $v) {
                $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
            }
            $print_str .= $this->align_center('TIN: 888-8888-888-8888',PAPER_WIDTH," ")."\r\n";
            $print_str .= $this->align_center('ACCRDN: AC888888888888',PAPER_WIDTH," ")."\r\n";

            $filename = "sales.txt";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);

            $batfile = "print.bat";
            $fh1 = fopen($batfile,'w+');
            $root = dirname(BASEPATH);
            $battxt ="NOTEPAD /P \"".realpath($root."/".$filename)."\"";
            fwrite($fh1, $battxt);
            fclose($fh1);
            session_write_close();
            // exec($filename);
            exec($batfile);
            session_start();
            unlink($filename);
            unlink($batfile);
        }    
        // public function print_sales_receipt($sales_id=null,$asJson=true,$return_print_str=false,$add_reprinted=true,$splits=null,$include_footer=true,$no_prints=1,$order_slip_prints=0,$approved_by=null,$main_db=false,$openDrawer=false,$branch_code="",$brand=""){
        //     if($main_db){
        //         $this->db = $this->load->database('main', TRUE);
        //     }
        //     $branch = $this->get_branch_details(false,$branch_code);
        //     $return = $this->get_order(false,$sales_id,$branch_code,$brand);
        //     $order = $return['order'];
        //     $details = $return['details'];
        //     $payments = $return['payments'];
        //     $discounts = $return['discounts'];
        //     $local_tax = $return['local_tax'];
        //     $charges = $return['charges'];
        //     $tax = $return['taxes'];
        //     $no_tax = $return['no_tax'];
        //     $zero_rated = $return['zero_rated'];
        //     $totalsss = $this->total_trans(false,$details,$discounts);
        //     $discs = $totalsss['discs'];
        //     $print_str = "\r\n";

        //     $brands = $this->setup_model->get_brands(null,$branch_code);

        //     if(count($brands) > 1){
        //         $brd = $this->setup_model->get_brands($brands[0]->id);

        //         $print_str .= $this->align_center($brd[0]->brand_name,PAPER_WIDTH," ")."\r\n";
        //         $print_str .= $this->align_center($brd[0]->brand_name,PAPER_WIDTH," ")."\r\n";
        //     }else{

        //         $wrap = wordwrap($branch['name'],PAPER_WIDTH,"|#|");
        //         $exp = explode("|#|", $wrap);
        //         foreach ($exp as $v) {
        //             $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
        //         }

        //         $wrap = wordwrap($branch['desc'],PAPER_WIDTH,"|#|");
        //         $exp = explode("|#|", $wrap);

        //         foreach ($exp as $v) {
        //             $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
        //         }
        //     }
            

        //     $wrap = wordwrap($branch['address'],PAPER_WIDTH,"|#|");
        //     $exp = explode("|#|", $wrap);
        //     foreach ($exp as $v) {
        //         $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
        //     }

        //     if($branch['tin'] != ""){
        //         // $print_str .= $this->append_chars('VAT REG TIN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['tin'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
        //         $print_str .= $this->append_chars('VAT REG TIN:'.$branch['tin'],"right",PAPER_WIDTH," ")."\r\n";
        //     }
        //     // if($branch['accrdn'] != ""){
        //     //     $print_str .= $this->append_chars('ACCRDN:',"right",PAPER_DET_SUBCOL," ").$this->append_chars($branch['accrdn'],"right",PAPER_TOTAL_COL_1," ")."\r\n";
        //     // }
        //     if($branch['machine_no'] != ""){
        //         $print_str .= $this->append_chars('MIN:'.$branch['machine_no'],"right",PAPER_WIDTH," ")."\r\n";
        //         // $print_str .= $this->append_chars('MIN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['machine_no'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
        //     }
        //     if($branch['permit_no'] != ""){
        //         $print_str .= $this->append_chars('PERMIT:'.$branch['permit_no'],"right",PAPER_WIDTH," ")."\r\n";
        //         // $print_str .= $this->append_chars('PERMIT:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['permit_no'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
        //     }
        //     if($branch['serial'] != ""){
        //         $print_str .= $this->append_chars('SN:'.$branch['serial'],"right",PAPER_WIDTH," ")."\r\n";
        //         // $print_str .= $this->append_chars('SN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['serial'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
        //     }
        //     // $print_str .= "\r\n";
        //     if (!empty($order['void_ref']) || $order['inactive'] == 1) {
        //         $print_str .= $this->align_center("***** VOIDED TRANSACTION *****",PAPER_WIDTH," ")."\r\n";
        //         $print_str .= $order['reason']."\r\n\r\n";
        //     }
        //     $header_print_str = $print_str;
        //     $header_print_str .= PAPER_LINE."\r\n";
        //          if (!empty($payments)){
        //             $header_print_str .= "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
        //                 // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
        //         }
        //         else{
        //             $header_print_str .= "Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
        //                 // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
        //         }
        //     $header_print_str .= PAPER_LINE."\r\n";

        //     // $print_str .= $this->align_center(date('Y-m-d H:i:s',strtotime($order['datetime']))." ".$order['terminal_name']." ".$order['name'],42," ")."\r\n";



        //     $print_str .= $this->append_chars(ucwords($order['name']),"right",PAPER_RD_COL_MID," ").$this->append_chars(date('Y-m-d H:i:s',strtotime($order['datetime'])),"left",PAPER_TOTAL_COL_2," ")."\r\n"
        //         // ."Terminal ID : ".$order['terminal_code']."\r\n"
        //         .PAPER_LINE."\r\n";

        //     if (!empty($payments)){
        //         $print_str .= "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
        //             // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
        //     }
        //     else{
        //         $print_str .= "Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
        //             // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
        //     }

        //     if($order['waiter_id'] != ""){
        //         $print_str .= "FS - ".ucwords(strtolower($order['waiter_name']))."\r\n";
        //     }

        //     $orddetails = "";
        //     if($order['table_id'] != "" || $order['table_id'] != 0)
        //         $orddetails .= $order['table_name']." ";

        //     if($order['guest'] != 0)
        //         $orddetails .= "Guest #".$order['guest'];

        //     if($order['serve_no'] != 0)
        //         $orddetails .= "Serve #".$order['serve_no'];

        //     if($orddetails != "")
        //         $print_str .= $this->align_center($orddetails,PAPER_WIDTH," ")."\r\n";
            

        //     $log_user = $this->session->userdata('user');
        //     if (!empty($payments)) {
        //         if($add_reprinted){
        //             if($order['printed'] >= 1){
        //                 $print_str .= $this->align_center('[REPRINTED]',PAPER_WIDTH," ")."\r\n";
        //                 $this->cashier_model->update_trans_sales(array('printed'=>$order['printed']+1),$order['sales_id']);
        //                 $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Reprinted Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                        
        //             }
        //             else{
        //                 $this->cashier_model->update_trans_sales(array('printed'=>1,'billed'=>1),$order['sales_id']);
                     
                      
        //                 if(!$return_print_str){
        //                     $log_id =  $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
        //                 }
        //             }
        //         }
        //         else{
        //             $this->cashier_model->update_trans_sales(array('printed'=>1,'billed'=>1),$order['sales_id']);
        //             if(!$return_print_str){
        //                  $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
        //             }
        //         }
        //     }
        //     else{
        //         $this->cashier_model->update_trans_sales(array('billed'=>1),$order['sales_id']);
        //          $log_id =  $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Billing on Sales Order #".$order['sales_id'],$order['sales_id']);
        //     }

        //     if(LOCALSYNC){
        //         if(isset($log_id)){
        //             $this->sync_model->add_logs($log_id);
        //         }

        //         $this->sync_model->update_trans_sales($sales_id);

        //     }

        //     if($order['customer_id'] != ""){
        //         if($main_db){
        //             $this->db = $this->load->database('default', TRUE);
        //         }
        //         $this->load->model('dine/customers_model');
        //         $customers = $this->customers_model->get_customer($order['customer_id']);
        //         if(count($customers) > 0){
        //             $cust = $customers[0];
        //             $name = strtolower($cust->fname." ".$cust->mname." ".$cust->lname." ".$cust->suffix);
        //             $print_str .= "Customer : ".$this->append_chars(ucwords($name),"right",19," ")."\r\n";
        //             if($order['type'] == 'delivery'){
        //                 $print_str .= "Contact  : ".$this->append_chars(ucwords($cust->phone),"right",19," ")."\r\n";
        //                 $address = strtolower($cust->street_no." ".$cust->street_address." ".$cust->city." ".$cust->region." ".$cust->zip);
        //                 $print_str .= "Address  : ".$this->append_chars(ucwords($address),"right",19," ")."\r\n";
        //             }
        //         }

        //         if($main_db){
        //             $this->db = $this->load->database('main', TRUE);
        //         }
        //     }
        //     if($order['type'] == 'delivery' && $order['customer_id'] != ""){
            
        //     }


        //     $print_str .= $this->append_chars("","right",PAPER_WIDTH,"=")."\r\n";

        //     $pre_total = 0;
        //     $post_details = array();
        //     $discs_items = array();
        //     foreach ($discs as $disc) {
        //         if(isset($disc['items']))
        //             $discs_items[$disc['type']] = $disc['items'];
        //     }

        //     $dscTxt = array();
        //     foreach ($details as $line_id => $val) {
        //         foreach ($discs_items as $type => $dissss) {
        //             if(in_array($line_id, $dissss)){
        //                 $qty = 1;
        //                 if(isset($dscTxt[$val['menu_id']][$type]['qty'])){
        //                     $qty = $dscTxt[$val['menu_id']][$type]['qty'] + 1;
        //                 }
        //                 $dscTxt[$val['menu_id']][$type] = array('txt' => '#'.$type,'qty' => $qty);
        //             }
        //         }
        //     }

        //     foreach ($details as $line_id => $val) {
        //         if (!isset($post_details[$val['menu_id']])) {
        //             $dscsacs = array();
        //             if(isset($dscTxt[$val['menu_id']])){
        //                 $dscsacs = $dscTxt[$val['menu_id']];
        //             }
                    
        //             if(SHOW_SKU){
        //                 $iname = "[".$val['code']."]".$val['name'];
        //             }else{
        //                 $iname = $val['name'];
        //             }
        //             $remarksArr = array();
        //             if($val['remarks'] != '')
        //                 $remarksArr = array($val['remarks']." x ".$val['qty']);
        //             $post_details[$val['menu_id']] = array(
        //                 'name' => $iname,
        //                 'code' => $val['code'],
        //                 'price' => $val['price'],
        //                 'no_tax' => $val['no_tax'],
        //                 'discount' => $val['discount'],
        //                 'qty' => $val['qty'],
        //                 'discounted'=>$dscsacs,
        //                 'remarks'=>$remarksArr,
        //                 'modifiers' => array()
        //             );
        //         } else {
        //             if($val['remarks'] != "")
        //                 $post_details[$val['menu_id']]['remarks'][]= $val['remarks']." x ".$val['qty'];
        //             $post_details[$val['menu_id']]['qty'] += $val['qty'];
        //         }

        //         if (empty($val['modifiers']))
        //             continue;

        //         $modifs = $val['modifiers'];
        //         $n_modifiers = $post_details[$val['menu_id']]['modifiers'];
        //         foreach ($modifs as $vv) {
        //             if (!isset($n_modifiers[$vv['id']])) {
        //                 $n_modifiers[$vv['id']] = array(
        //                     'name' => $vv['name'],
        //                     'price' => $vv['price'],
        //                     'qty' => $val['qty'],
        //                     'discount' => $vv['discount']
        //                 );
        //             } else {
        //                 $n_modifiers[$vv['id']]['qty'] += $val['qty'];
        //             }
        //         }
        //         $post_details[$val['menu_id']]['modifiers'] = $n_modifiers;
        //     }
        //     /* END NEW BLOCK */
        //     $tot_qty = 0;
        //     foreach ($post_details as $val) {
        //         $tot_qty += $val['qty'];
        //         $print_str .= $this->append_chars($val['qty'],"right",PAPER_DET_COL_1," ");

        //         $len = strlen($val['name']);

        //         if($val['qty'] == 1){
        //             $lgth = 21;
        //         }else{
        //             $lgth = 16;
        //         }

        //         if($len > $lgth){
        //             $arr2 = str_split($val['name'], $lgth);
        //             $counter = 1;
        //             foreach($arr2 as $k => $vv){
        //                 if($counter == 1){
        //                     if ($val['qty'] == 1) {
        //                         $print_str .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").
        //                             $this->append_chars(number_format($val['price'],2),"left",PAPER_DET_COL_3," ")."\r\n";
        //                     } else {
        //                         $print_str .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
        //                             $this->append_chars(number_format($val['price'] * $val['qty'],2),"left",PAPER_DET_COL_3," ")."\r\n";
        //                     }
        //                 }else{
        //                     // if ($val['qty'] == 1) {
        //                         $print_str .= $this->append_chars("","right",PAPER_DET_COL_1," ");
        //                         $print_str .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").
        //                             $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
        //                     // } else {
        //                         // $print_str .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
        //                         //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
        //                     // }
        //                 }
        //                 $counter++;
        //             }
                    
        //             if ($val['qty'] == 1) {
        //                 $pre_total += $val['price'];
        //             }else{
        //                 $pre_total += $val['price'] * $val['qty'];
        //             }
        //         }else{
        //             if ($val['qty'] == 1) {
        //                 $print_str .= $this->append_chars(substrwords($val['name'],100,""),"right",PAPER_DET_COL_2," ").
        //                     $this->append_chars(number_format($val['price'],2),"left",PAPER_DET_COL_3," ")."\r\n";
        //                 $pre_total += $val['price'];
        //             } else {
        //                 $print_str .= $this->append_chars(substrwords($val['name'],100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
        //                     $this->append_chars(number_format($val['price'] * $val['qty'],2),"left",PAPER_DET_COL_3," ")."\r\n";
        //                 $pre_total += $val['price'] * $val['qty'];
        //             }
        //         }
        //         if(count($val['discounted']) > 0){
        //             foreach ($val['discounted'] as $dssstxt) {
        //               $print_str .= "      ";
        //               $print_str .= $this->append_chars($dssstxt['txt']." x ".$dssstxt['qty'],"right",PAPER_DET_COL_2," ")."\r\n";
        //             }
        //         }
        //         if(isset($val['remarks']) && count($val['remarks']) > 0){
        //             foreach ($val['remarks'] as $rmrktxt) {
        //                 $print_str .= "     * ";
        //                 $print_str .= $this->append_chars(ucwords($rmrktxt),"right",PAPER_DET_COL_2," ")."\r\n";
        //             }
        //         }

        //         if (empty($val['modifiers']))
        //             continue;

        //         $modifs = $val['modifiers'];
        //         foreach ($modifs as $vv) {
        //             $print_str .= "   * ".$vv['qty']." ";

        //             if ($vv['qty'] == 1) {
        //                 $print_str .= $this->append_chars(substrwords($vv['name'],100,""),"right",PAPER_DET_SUBCOL," ")
        //                     .$this->append_chars(number_format($vv['price'],2),"left",PAPER_DET_COL_3," ")."\r\n";
        //                 $pre_total += $vv['price'];
        //             } else {
        //                 $print_str .= $this->append_chars(substrwords($vv['name'],100,"")." @ ".$vv['price'],"right",PAPER_DET_SUBCOL," ")
        //                     .$this->append_chars(number_format($vv['price'] * $vv['qty'],2),"left",PAPER_DET_COL_3," ")."\r\n";
        //                 $pre_total += $vv['price'] * $vv['qty'];
        //             }
        //         }
                


        //         //DISCOUNT PALATANDAAN
        //         // if(in_array($val[''], haystack))

        //     }

        //     $print_str .= $this->append_chars("","right",PAPER_WIDTH,"=");

        //     // $vat = round($order['amount'] / (1 + BASE_TAX) * BASE_TAX,1);
        //     $vat = 0;
        //     if($tax > 0){
        //         foreach ($tax as $tx) {
        //            $vat += $tx['amount'];
        //         }
        //     }
        //     $no_tax_amt = 0;
        //     foreach ($no_tax as $k=>$v) {
        //         $no_tax_amt += $v['amount'];
        //     }

        //     $zero_rated_amt = 0;
        //     foreach ($zero_rated as $k=>$v) {
        //         $zero_rated_amt += $v['amount'];
        //     }
        //     if($zero_rated_amt > 0){
        //         $no_tax_amt = 0;
        //     }

        //     $print_str .= "\r\n".$this->append_chars(ucwords("TOTAL"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(($pre_total),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //     $print_str .= $this->append_chars(ucwords("TOTAL QTY"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format(($tot_qty),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //     // if(count($discs) > 0){
        //     //     foreach ($discs as $ds) {
        //     //         $print_str .= "\r\n".$this->append_chars(strtoupper($ds['type']),"right",28," ").$this->append_chars("P (".number_format($ds['amount'],2).")","left",10," ")."\r\n";
        //     //     }
        //     // }
        //     // $print_str .= "\r\n";
        //     $total_discounts = 0;
        //     $total_discounts_sm = 0;
        //     foreach ($discounts as $dcs_ci => $dcs) {
        //         foreach ($dcs['persons'] as $code => $dcp) {
        //             // $print_str .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
        //             // $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
        //             // $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
        //             // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
        //             $total_discounts += $dcp['amount'];
        //             $dcAmt = $dcp['amount'];
        //             // if(MALL_ENABLED && MALL == 'megamall'){
        //             //     if($dcs_ci == PWDDISC){
        //             //         $dcAmt = $dcAmt / 1.12;       
        //             //     }
        //             // }
        //             $total_discounts_sm += $dcAmt;
        //         }
        //     }
        //     $total_discounts_non_vat = 0;
        //     foreach ($discounts as $dcs_ci => $dcs) {
               
        //         foreach ($dcs['persons'] as $code => $dcp) {
        //             // $print_str .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
        //             // $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
        //             // $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
        //             // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
        //             if($dcs['no_tax'] == 1){
        //                 $total_discounts_non_vat += $dcp['amount'];
        //             }
        //         }
        //     }
        //     $total_charges = 0;
        //     if(count($charges) > 0){
        //         foreach ($charges as $charge_id => $opt) {
        //             $total_charges += $opt['total_amount'];
        //         }
        //     }
        //     $local_tax_amt = 0;
        //     if(count($local_tax) > 0){
        //         foreach ($local_tax as $lt_id => $lt) {
        //             $local_tax_amt += $lt['amount'];
        //         }
        //     }
        //     // echo num($total_charges + $local_tax_amt);

        //     // echo '((('.$order['amount'].' - ('.$total_charges.' + '.$local_tax_amt.') - '.$vat.') - '.$no_tax_amt.'+'.$total_discounts_non_vat.') -'.$zero_rated_amt;


        //     $vat_sales = ( ( ( $order['amount'] - ($total_charges + $local_tax_amt) ) - $vat)  - $no_tax_amt + $total_discounts_non_vat ) - $zero_rated_amt;

        //     // echo '===== '.$vat_sales;
        //     // $vat_sales = ( ( ( $order['amount'] ) - $vat)  - $no_tax_amt + $total_discounts) - $zero_rated_amt;
        //     // echo "vat_sales= ((".$order['amount']." - ".$total_charges."))- ".$vat." )- ".$no_tax_amt." + ".$total_discounts." - ".$zero_rated_amt;
        //     if($vat_sales < 0){
        //         $vat_sales = 0;
        //     }
        //     $print_str .= "\r\n".$this->append_chars(ucwords("VAT SALES"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(num($vat_sales),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //     $print_str .= $this->append_chars(ucwords("VAT EXEMPT SALES"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($no_tax_amt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //     $print_str .= $this->append_chars(ucwords("VAT ZERO RATED"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($zero_rated_amt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            
            
            
        //     #CONDITION TO NA PINADAGDAG NG TAGAYTAY - FOR SENIOR CITEZEN VIEW VAT PLUS DISCOUNT
        //     // if(count($discounts) >0){
        //     //     if(count($dcs['persons']) > 0){
        //     //         $print_str .= $this->append_chars(ucwords("Less VAT (12%)"),"right",28," ").$this->append_chars("(".number_format($pre_total - $no_tax_amt,2).")","left",10," ")."\r\n";
        //     //     }
        //     // }
        //     // else{
        //     //     if($tax > 0){
        //     //         foreach ($tax as $tx) {
        //     //            $print_str .= $this->append_chars($tx['name']."(".$tx['rate']."%)","right",28," ").$this->append_chars(number_format($tx['amount'],2),"left",10," ")."\r\n";
        //     //         }
        //     //     }
        //     // }


        //     #CONDITION TO NA PARA SA TAGUEGARAO
        //     if($tax > 0){
        //         foreach ($tax as $tx) {
        //            $print_str .= $this->append_chars($tx['name']."(".$tx['rate']."%)","right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($tx['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //         }
        //     }
        //     if(count($local_tax) > 0){
        //         $local_tax_amt = 0;
        //         foreach ($local_tax as $lt_id => $lt) {
        //             $local_tax_amt += $lt['amount'];
        //         }
        //         $print_str .= $this->append_chars(ucwords("LOCAL TAX"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($local_tax_amt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //     }
        //     if(count($discounts) >0){
        //         $hasSMPWD = false;
        //         if(count($dcs['persons']) > 0){
        //             $print_str .= "\r\n";
        //             $print_str .= PAPER_LINE."\r\n";
        //             $print_str .= "          Discount Details"."\r\n";
        //             $print_str .= PAPER_LINE."\r\n";

        //             foreach ($discounts as $dcs_ci => $dcs) {
        //                 foreach ($dcs['persons'] as $code => $dcp) {
        //                     $discRateTxt = " (".$dcp['disc_rate']."%)";
        //                     if($dcs['fix'] == 1){
        //                         $discRateTxt = " (".$dcp['disc_rate'].")";
        //                     }
        //                     $dcAmt = $dcp['amount'];
        //                     // if(MALL_ENABLED && MALL == 'megamall'){
        //                     //     if($dcs_ci == PWDDISC){
        //                     //         $dcAmt = $dcAmt / 1.12; 
        //                     //         $hasSMPWD = true;      
        //                     //     }
        //                     // }
        //                     $print_str .= $this->append_chars($dcs_ci.$discRateTxt,"right",PAPER_TOTAL_COL_1," ").$this->append_chars('P'.num($dcAmt),"left",PAPER_TOTAL_COL_2," ");
        //                     $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",PAPER_TOTAL_COL_1," ");
        //                     $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",PAPER_TOTAL_COL_1," ")."\r\n";
        //                 }
        //             }
        //             // $print_str .= "\r\n";
        //             // echo $pre_total." - ".$order['amount']." - ".$total_charges." - ".$total_discounts; die();
        //             $less_vat = ($pre_total - ($order['amount'] - $total_charges + $local_tax_amt ) ) - $total_discounts;
        //             // $less_vat = ($pre_total - ($order['amount'] - num($total_charges + $local_tax_amt) ) ) - $total_discounts;

        //             // $print_str .= $this->append_chars(ucwords("Total Discount"),"right",28," ").$this->append_chars(number_format($total_discounts,2),"left",10," ")."\r\n";
        //             $print_str .= $this->append_chars(ucwords("Total Less VAT"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //             if(MALL_ENABLED && MALL == 'megamall' && $hasSMPWD){
        //                 $print_str .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts_sm + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //             }
        //             else{
        //                 $print_str .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //             }
        //         }
        //     }

        //     if(count($charges) > 0){
        //         $print_str .= "\r\n";
        //         $print_str .= PAPER_LINE."\r\n";
        //         $print_str .= "              CHARGES"."\r\n";
        //         $print_str .= PAPER_LINE."\r\n";
        //         foreach ($charges as $charge_id => $opt) {
        //             $charge_amount = $opt['total_amount'];
        //             // if($opt['absolute'] == 0){
        //             //     $charge_amount = ($opt['amount'] / 100) * ($order['amount'] - $vat);
        //             // }
        //             $print_str .= $this->append_chars($opt['name'],"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($charge_amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //         }
        //         $print_str .= PAPER_LINE."\r\n";
        //     }

        //     if (!empty($payments)) {

        //         $print_str .= "\r\n";
        //         // $print_str .= "\r\n"."======================================"."\r\n";
        //         $print_str .= $this->append_chars("Amount due","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

        //         $pay_total = 0;
        //         $gft_ctr = 0;
        //         $nor_ctr = 0;
        //         foreach ($payments as $payment_id => $opt) {

        //             $print_str .= $this->append_chars(ucwords($opt['payment_type']),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($opt['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    
        //             if($opt['payment_type'] == 'check'){
        //                 $print_str .= $this->append_chars("     Check # ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
        //             }else{
        //                 if (!empty($opt['reference']) && $opt['payment_type'] != 'deposit') {
        //                     $print_str .= $this->append_chars("     Reference ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
        //                 }
        //             }

        //             if($opt['payment_type'] == 'foodpanda'){
        //                 if (!empty($opt['approval_code']))
        //                         $print_str .= $this->append_chars("  Order Code: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
        //             }else if($opt['payment_type'] == 'check'){
        //                 $print_str .= $this->append_chars("     Bank: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
        //             }else{
        //                 if (!empty($opt['card_number'])) {
        //                     $print_str .= $this->append_chars("  Card #: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
        //                     if (!empty($opt['approval_code']))
        //                         $print_str .= $this->append_chars("  Approval #: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
        //                 }
        //             }
        //             $pay_total += $opt['amount'];
        //             if($opt['payment_type'] == 'gc'){
        //                 $gft_ctr++;
        //             }
        //             else
        //                 $nor_ctr++;
                    
        //         }
        //         if($gft_ctr == 1 && $nor_ctr == 0)
        //             $print_str .= $this->append_chars("Change","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(0,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //         else
        //             $print_str .= $this->append_chars("Change","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($pay_total - $order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //         $print_str .= PAPER_LINE."\r\n";
        //         if ($include_footer) {
        //             $rec_footer = "";
        //             if($branch['rec_footer'] != ""){
        //                 $wrap = str_replace ("<br>","\r\n", $branch['rec_footer'] );
        //                 $exp = explode("\r\n", $wrap);
        //                 foreach ($exp as $v) {
        //                     $wrap2 = wordwrap($v,35,"|#|");
        //                     $exp2 = explode("|#|", $wrap2);  
        //                     foreach ($exp2 as $v2) {
        //                         $rec_footer .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
        //                     }
        //                 }                        
        //             }    
        //             $print_str .= $rec_footer;
        //             if($branch['contact_no'] != ""){
        //                 $print_str .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
        //                              .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
        //             }
        //             if($branch['email'] != ""){
        //                 $print_str .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
        //                            .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
        //             }
        //             if($branch['website'] != ""){
        //                 $print_str .= $this->align_center("Please visit us at",PAPER_WIDTH," ")."\r\n"
        //                              .$this->align_center($branch['website'],PAPER_WIDTH," ")."\r\n";
        //             }
        //             $print_str .= PAPER_LINE."\r\n";
        //             // $print_str .= "\r\n";
        //             $print_str .= $this->append_chars('Name:',"right",PAPER_RECEIPT_TEXT," ")
        //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
        //             $print_str .= $this->append_chars('Company:',"right",PAPER_RECEIPT_TEXT," ")
        //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
        //             $print_str .= $this->append_chars('Address:',"right",PAPER_RECEIPT_TEXT," ")
        //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
        //             $print_str .= $this->append_chars('TIN:',"right",PAPER_RECEIPT_TEXT," ")
        //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n";
        //             // $print_str .= "\r\n";
        //             // $print_str .= "\r\n";
        //             $print_str .= PAPER_LINE."\r\n";
        //             $print_str .= $this->align_center("POS Vendor Details",PAPER_WIDTH," ")."\r\n";
        //             $print_str .= PAPER_LINE."\r\n";
        //             $print_str .= $this->align_center("PointOne Integrated Tech., Inc.",PAPER_WIDTH," ")."\r\n";
        //             $print_str .= $this->align_center("1409 Prestige Tower",PAPER_WIDTH," ")."\r\n";
        //             $print_str .= $this->align_center("Ortigas Center, Pasig City",PAPER_WIDTH," ")."\r\n";
        //             $print_str .= $this->append_chars('POS Version:',"right",PAPER_RECEIPT_TEXT_FT," ")
        //                        .  $this->append_chars('iPos ver 1.0',"right",PAPER_RECEIPT_INPUT_FT," ")."\r\n";
        //             $print_str .= $this->append_chars('TIN:',"right",PAPER_RECEIPT_TEXT_FT," ")
        //                        .  $this->append_chars('008543444-000',"right",PAPER_RECEIPT_INPUT_FT," ")."\r\n";
        //             $print_str .= $this->append_chars('Date Issued:',"right",PAPER_RECEIPT_TEXT_FT," ")
        //                        .  $this->append_chars('December 22, 2014',"right",PAPER_RECEIPT_INPUT_FT," ")."\r\n";
        //                        // .  $this->append_chars(date2Word($order['datetime']),"right",PAPER_TOTAL_COL_2," ")."\r\n";
        //             $print_str .= $this->append_chars('Valid Until:',"right",PAPER_RECEIPT_TEXT_FT," ")
        //                        .  $this->append_chars(date2Word('December 22, 2019'),"right",PAPER_RECEIPT_INPUT_FT," ")."\r\n";
        //                        // .  $this->append_chars(date2Word( date('Y-m-d',strtotime($order['datetime'].' +5 year')) ),"right",PAPER_TOTAL_COL_2," ")."\r\n";
        //             if($branch['accrdn'] != ""){
        //                 $print_str .= $this->align_center('ACCRDN:',PAPER_WIDTH," ")."\r\n".$this->align_center($branch['accrdn'],PAPER_WIDTH," ")."\r\n";
        //             }
        //             // $print_str .= $this->align_center('This Invoice/Receipt',PAPER_WIDTH," ")."\r\n";
        //             // $print_str .= $this->align_center('Shall be valid',PAPER_WIDTH," ")."\r\n";
        //             // $print_str .= $this->align_center('Five(5) Years from the date of',PAPER_WIDTH," ")."\r\n";
        //             // $print_str .= $this->align_center('The Permit to use.',PAPER_WIDTH," ")."\r\n";



        //             // if($branch['pos_footer'] != ""){
        //             //     // $print_str .= PAPER_LINE."\r\n";
        //             //     $print_str .= "\r\n";
        //             //     $wrap = str_replace ("<br>","\r\n", $branch['pos_footer'] );
        //             //     $exp = explode("\r\n", $wrap);
        //             //     foreach ($exp as $v) {
        //             //         $wrap2 = wordwrap($v,35,"|#|");
        //             //         $exp2 = explode("|#|", $wrap2);  
        //             //         foreach ($exp2 as $v2) {
        //             //             $print_str .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
        //             //         }
        //             //     }
        //             // }

        //         }
        //     } else {
        //         $print_str .= "\r\n".$this->append_chars("","right",PAPER_WIDTH,"=");
        //         $print_str .= "\r\n\r\n".$this->append_chars("Billing Amount","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //         if(is_array($splits)){
        //             $print_str .= $this->append_chars("Split Amount by ".$splits['by'],"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($splits['total'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //         }
        //         if ($include_footer) {
        //             // $print_str .= "\r\n\r\n";
        //             if($branch['contact_no'] != ""){
        //                 $print_str .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
        //                              .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
        //             }
        //             if($branch['email'] != ""){
        //                 $print_str .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
        //                            .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
        //             }
        //             if($branch['website'] != "")
        //                 $print_str .= $this->align_center("Please visit us at \r\n".$branch['website'],PAPER_WIDTH," ")."\r\n";
        //         }

        //     }

        //     if (!empty($payments)) {
        //         $print_str .= "\r\n";
        //         foreach ($discounts as $dcs_ci => $dcs) {
        //             if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
        //                 $print_str .= PAPER_LINE."\r\n";
        //                 $print_str .= $this->align_center("OSCA/PWD Details",PAPER_WIDTH," ")."\r\n";
        //                 $print_str .= PAPER_LINE."\r\n";
        //                 // $print_str .= $this->align_center(PAPER_LINE,42," ");
        //                 break;
        //             }
        //         }
        //         foreach ($discounts as $dcs_ci => $dcs) {
        //             if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
        //                 foreach ($dcs['persons'] as $code => $dcp) {
        //                     // ."\r\n"
        //                     $print_str .= "\r\n".$this->append_chars("ID NO      : ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
        //                     $print_str .= "\r\n".$this->append_chars("NAME       : ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
        //                     $print_str .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
        //                     $print_str .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
        //                     $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
        //                     // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
        //                 }
        //             }
        //         }
        //         foreach ($discounts as $dcs_ci => $dcs) {
        //             if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
        //                 $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
        //                 break;
        //             }
        //         }
        //     }

        //     if($approved_by != null){
        //         $app = $this->site_model->get_user_details($approved_by);
        //         $approver = $app->fname." ".$app->mname." ".$app->lname." ".$app->suffix;
        //         $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
        //         $print_str .= "\r\n".$this->append_chars("Approved By : ".$approver,"right",PAPER_TOTAL_COL_1," ");
        //         $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
        //         $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
        //     }


        //     if ($return_print_str) {
        //         return $print_str;
        //     }
        //     // echo "<pre>".$print_str."</pre>";
        //     // return false;

        //     $filename = "sales.txt";
        //     $fp = fopen($filename, "w+");
        //     fwrite($fp,$print_str);
        //     fclose($fp);

        //     $batfile = "print.bat";
        //     $fh1 = fopen($batfile,'w+');
        //     $root = dirname(BASEPATH);
        //     $battxt ="NOTEPAD /P \"".realpath($root."/".$filename)."\"";

        //     if(BILLING_PRINTER){
        //         if(BILLING_PRINTER != "DEFAULT"){
        //             $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".BILLING_PRINTER."\"  ";   
        //         }
        //     }

        //     if($openDrawer){
        //         $pet = $this->cashier_model->get_pos_settings();
        //         $open_drawer_printer = $pet->open_drawer_printer;
        //         if($open_drawer_printer != ""){
        //             $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".$open_drawer_printer."\"  ";   
        //         }            
        //     }

        //     fwrite($fh1, $battxt);
        //     fclose($fh1);
        //     session_write_close();
        //     // exec($filename);
        //     for ($i=0; $i < $no_prints; $i++) { 
        //         exec($batfile);
        //     }
        //     session_start();
        //     unlink($filename);
        //     unlink($batfile);

        //     if($order_slip_prints > 0){
        //         $this->print_order_slip($header_print_str,$post_details,$order_slip_prints);            
        //     }

        //     if ($asJson)
        //         echo json_encode(array('msg'=>'Receipt # '.(!empty($order['ref']) ? $order['ref'] : $sales_id).' has been printed'));
        //     else
        //         return array('msg'=>'Receipt # '.(!empty($order['ref']) ? $order['ref'] : $sales_id).' has been printed');
        // }
        public function print_sales_receipt($sales_id=null,$asJson=true,$return_print_str=false,$add_reprinted=true,$splits=null,$include_footer=true,$no_prints=1,$order_slip_prints=0,$approved_by=null,$main_db=false,$openDrawer=false,$branch_code="",$brand=""){
            if($main_db){
                $this->db = $this->load->database('main', TRUE);
            }
            $branch = $this->get_branch_details(false,$branch_code);
            $return = $this->get_order(false,$sales_id,$branch_code,$brand);
            $order = $return['order'];
            $is_printed = $order['printed'];
            $details = $return['details'];
            $payments = $return['payments'];
            $discounts = $return['discounts'];
            $local_tax = $return['local_tax'];
            $charges = $return['charges'];
            $tax = $return['taxes'];
            $no_tax = $return['no_tax'];
            $zero_rated = $return['zero_rated'];
            $pfields = $return['pfields'];
            $totalsss = $this->total_trans(false,$details,$discounts);
            $discs = $totalsss['discs'];
            $print_str = "\r\n";

            $brands = $this->setup_model->get_brands(null,$branch_code);
            // $wrap = wordwrap($branch['name'],PAPER_WIDTH,"|#|");
            // $exp = explode("|#|", $wrap);
            // foreach ($exp as $v) {
            //     $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
            // }
            // if(defined('RECEIPT_ADDITIONAL_HEADER_BELOW_BRANCH') && !empty(RECEIPT_ADDITIONAL_HEADER_BELOW_BRANCH)){
            //     // $print_str .= $this->align_center(RECEIPT_ADDITIONAL_HEADER_BELOW_BRANCH,PAPER_WIDTH," ")."\r\n";                

            //     if(PRINT_VERSION && PRINT_VERSION=='V3' && file_exists('uploads/brands/logos.png')){
            //         $print_str .= $this->align_center('<img src="'.base_url().'uploads/brands/logos.png" width="50px" height="65px" style="display: block;
            //           margin-top: 10px;
            //           margin-left: auto;
            //           margin-right: auto;
            //           width: 50%;">',PAPER_WIDTH," ")."\r\n";
            //     }
            //     else if(PRINT_VERSION && PRINT_VERSION=='V3'){
            //         if($includeExcel){
            //             $print_str .= '<center><span style="font-size:15px">'.$branch['name'].'</span></center>'."\r\n";
            //         }else{
            //             $print_str .= $this->align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
            //         }
            //     }
            //     else{
            //         $print_str .= $this->align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
            //     }
                
            // }

            // if(RECEIPT_OWNED){

            //     $len1 = strlen(RECEIPT_OWNED);
            //     $lgth1 = 35;
                
            //     if($len1 > $lgth1){
            //         $arr_split = str_split(RECEIPT_OWNED, $lgth1);
            //         foreach($arr_split as $k => $vv){
            //             $print_str .= $this->align_center($vv,PAPER_WIDTH," ")."\r\n";
            //         }
            //     }else{
            //         $print_str .= $this->align_center(RECEIPT_OWNED,PAPER_WIDTH," ")."\r\n";
            //     }


            // }

            // $wrap = wordwrap($branch['desc'],PAPER_WIDTH,"|#|");
            // $exp = explode("|#|", $wrap);
            // foreach ($exp as $v) {
            //     $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
            // }
            $splt = 1;
            // if($order['guest'] > 1){
            //     $splt = $order['guest'];
            // }

            $wrap = wordwrap($branch['address'],PAPER_WIDTH,"|#|");
            $exp = explode("|#|", $wrap);
            foreach ($exp as $v) {
                $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
            }   
                // $print_str .= "\r\n";
            if($branch['tin'] != ""){
                // $print_str .= $this->append_chars('VAT REG TIN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['tin'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
                $print_str .= $this->align_center('VAT REG TIN:'.$branch['tin'],PAPER_WIDTH," ")."\r\n";
            }
            // if($branch['accrdn'] != ""){
            //     $print_str .= $this->append_chars('ACCRDN:',"right",PAPER_DET_SUBCOL," ").$this->append_chars($branch['accrdn'],"right",PAPER_TOTAL_COL_1," ")."\r\n";
            // }
            if($branch['serial'] != ""){
                $print_str .= $this->align_center('S/N:'.$branch['serial'],PAPER_WIDTH," ")."\r\n";
                // $print_str .= $this->append_chars('SN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['serial'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
            }
            if($branch['machine_no'] != ""){
                $print_str .= $this->align_center('MIN:'.$branch['machine_no'],PAPER_WIDTH," ")."\r\n";
                // $print_str .= $this->append_chars('MIN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['machine_no'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
            }
            // if($branch['permit_no'] != ""){
            //     $print_str .= $this->append_chars('PERMIT:'.$branch['permit_no'],"right",PAPER_WIDTH," ")."\r\n";
            //     // $print_str .= $this->append_chars('PERMIT:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['permit_no'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
            // }


            $print_str .= "\r\n";
              // $print_str .= $this->append_chars(date('M d, Y h:i A',strtotime($order['datetime'])),"left",PAPER_WIDTH," ")."\r\n";
            $print_str .=  $this->align_center(date('M d, Y h:i A',strtotime($order['datetime'])),PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";

            // if (!empty($order['void_ref']) || $order['inactive'] == 1) {
            if ($order['ref'] != null && $order['inactive'] == 1) {
                $print_str .= $this->align_center("***** VOIDED TRANSACTION *****",PAPER_WIDTH," ")."\r\n";
                $print_str .= $order['reason']."\r\n\r\n";
            }
            $cancelled = 0;
            if ($order['ref'] == null && $order['inactive'] == 1) {
                $print_str .= $this->align_center("***** CANCELLED TRANSACTION *****",PAPER_WIDTH," ")."\r\n";
                $print_str .= $order['reason']."\r\n\r\n";
                $cancelled = 1;
            }
             // $print_str .= "\r\n".PAPER_LINE."\r\n";
            // $header_print_str = $print_str;
             // $header_print_str .= PAPER_LINE."\r\n";
                // if (!empty($payments)){
                //         $header_print_str = "Receipt # ".$order['ref']."\r\n";
                //         // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
                // }
                // else{
                //     $header_print_str = "Reference # ".$order['sales_id']."\r\n";
                //         // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
                // }
             // $header_print_str .= "\r\n".PAPER_LINE."\r\n";

           
            // $header_print_str .= PAPER_LINE."\r\n";

            // $print_str .= $this->align_center(date('Y-m-d H:i:s',strtotime($order['datetime']))." ".$order['terminal_name']." ".$order['name'],42," ")."\r\n";


            // if($order['update_date']){
            //     // $print_str .= $this->append_chars(ucwords($order['name']),"right",PAPER_RD_COL_3_3," ").$this->append_chars(date('Y-m-d H:i:s',strtotime($order['update_date'])),"left",PAPER_TOTAL_COL_2," ")."\r\n"
            //     //       . $this->append_chars("Terminal ID : ".$order['terminal_code'],"right",PAPER_RD_COL_3," ").$this->append_chars("","left",PAPER_TOTAL_COL_2," ")."\r\n"
            //     //     ."\r\n";
            //       // $print_str .= $this->append_chars("Terminal ID : ".$order['terminal_code'],"right",PAPER_RD_COL_3," ").$this->append_chars("","left",PAPER_TOTAL_COL_2," ")."\r\n";
            //         // ."\r\n";
            //         // .PAPER_LINE."\r\n";
            // }else{
            //     // $print_str .= $this->append_chars(ucwords($order['name']),"right",PAPER_RD_COL_3_3," ").$this->append_chars(date('Y-m-d H:i:s',strtotime($order['datetime'])),"left",PAPER_TOTAL_COL_2," ")."\r\n"
            //     //       . $this->append_chars("Terminal ID : ".$order['terminal_code'],"right",PAPER_RD_COL_MID," ").$this->append_chars("","left",PAPER_TOTAL_COL_2," ")."\r\n"
            //     //     ."\r\n";
            //     // $print_str .=  $this->append_chars("Terminal ID : ".$order['terminal_code'],"right",PAPER_RD_COL_MID," ").$this->append_chars("","left",PAPER_TOTAL_COL_2," ")."\r\n"
            //         // ."\r\n";
            //         // .PAPER_LINE."\r\n";
                
            // }
            $print_str .= "\r\n";

            if (!empty($payments)){
                 // if(PRINT_VERSION=="V2"){
                 //    if($order['type'] == "mgtpromo"){
                 //        $print_str .=  $this->align_center("MGTPROMO #: ".$order['ref']."",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                 //    }else{
                 //        $print_str .=  $this->align_center("<bold>OFFICIAL RECEIPT</bold>",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                 //        $print_str .=  $this->align_center("<bold>OR #: ".$order['ref']."</bold>",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                 //    }
                 //     // $print_str .=  $this->align_center("<bold>QUEUING #: ".$order['terminal_id'].'-'.$order['queue_id']."</bold>",PAPER_RD_COL_MID,"")."\r\n";


                 //  }else{
                    if($order['type'] == "mgtpromo"){
                        $print_str .=  $this->align_center("MGTPROMO #: ".$order['ref']."",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                    }else{
                        $print_str .=  $this->align_center("OFFICIAL  RECEIPT ",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                        
                        // if(PRINT_VERSION && PRINT_VERSION=='V3'){
                        //     // $print_str .=  $this->align_center("<span style='font-size:15px'>OR #: ".$order['ref'].'</span>',PAPER_RD_COL_MID,"  ")."\r\n";
                        //     if($includeExcel){
                        //      $print_str .=  $this->align_center("<span style='font-size:15px'>OR #: ".$order['ref'].'</span>',PAPER_RD_COL_MID,"  ")."\r\n";
                        //     }else{
                        //         $print_str .=  $this->align_center("OR #: ".$order['ref'],PAPER_RD_COL_MID,"  ")."\r\n"; 
                        //     }
                        // }else{
                           $print_str .=  $this->align_center("OR #: ".$order['ref'],PAPER_RD_COL_MID,"  ")."\r\n"; 
                        // }
                    }

                    // $print_str .=  $this->align_center("QUEUING #: ".$order['terminal_id'].'-'.$order['queue_id'],PAPER_RD_COL_MID,"")."\r\n";
                  // }
                    // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
            } else{
                $print_str .= $this->align_center("REFERENCE #: ".$order['sales_id'],PAPER_RD_COL_MID," ");//"Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
                // $print_str .=  $this->align_center($order['sales_id'],PAPER_RD_COL_MID,"  ")."\r\n";

                // $print_str .=  $this->align_center("QUEUING",PAPER_RD_COL_MID," ");
                // $print_str .=  $this->align_center("#: ".$order['terminal_id'].'-'.$order['queue_id'],PAPER_RD_COL_MID,"  ")."\r\n";

                    // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
            }
            // if(PRINT_VERSION=="V2"){
            //  $print_str .=  $this->align_center("\r\n <bold>".strtoupper($order['type'])."</bold>",PAPER_RD_COL_MID," ")."\r\n";
            // // }else if(PRINT_VERSION=="V3"){
            // if($includeExcel){
            //         $print_str .=  $this->align_center("\r\n"."<span style='font-size:15px'>".strtoupper($order['type'])."</span>",PAPER_RD_COL_MID," ")."\r\n";
            //         $print_str .=  $this->align_center("\r\n"."<span style='font-size:15px'>"."Table No:  ".$order['table_name']."</span>",PAPER_RD_COL_MID," ")."\r\n";
            //     }else{
            //         $print_str .=  $this->align_center("\r\n".strtoupper($order['type']),PAPER_RD_COL_MID," ")."\r\n";
            //         $print_str .=  $this->align_center("Table No:  ".$order['table_name'],PAPER_RD_COL_MID,"  ")."\r\n";
            //     }
            // }else{
             $print_str .=  $this->align_center("\r\n".strtoupper($order['type']),PAPER_RD_COL_MID," ")."\r\n";
             $print_str .=  $this->align_center("Table No:  ".$order['table_name'],PAPER_RD_COL_MID,"  ")."\r\n";
            // }
            // $print_str .= $this->append_chars("Table No:  ".$order['table_name'],"left",PAPER_WIDTH," ")."\r\n";
            // $print_str .= $this->append_chars("Standee No:  ".$order['serve_no'],"left",PAPER_WIDTH," ")."\r\n";
            // $print_str .= $this->append_chars("Register No:  ".$order['terminal_id'],"left",PAPER_WIDTH," ")."\r\n";
            // $print_str .= $this->append_chars("Cashier Name:  ".ucwords($order['name']),"left",PAPER_WIDTH," ")."\r\n";

            $print_str .=  $this->align_center("Serve No:  ".$order['serve_no'],PAPER_RD_COL_MID," ")."\r\n";
            $print_str .=  $this->align_center("Register No:  ".$order['terminal_id'],PAPER_RD_COL_MID,"  ")."\r\n";
            $print_str .=  $this->align_center("Cashier Name:  ".ucwords($order['name']),PAPER_RD_COL_MID,"  ")."\r\n";
            if($order['waiter_username']){
                $print_str .=  $this->align_center("Food Server:  ".ucwords($order['waiter_username']),PAPER_RD_COL_MID,"  ")."\r\n";
            }

            $print_str .= PAPER_LINE_SINGLE."\r\n";

            if($order['customer_id'] != ""){
                if($main_db){
                    $this->db = $this->load->database('default', TRUE);
                }
                $this->load->model('dine/customers_model');
                $customers = $this->customers_model->get_customer($order['customer_id']);
                if(count($customers) > 0){
                    $cust = $customers[0];
                    $name = strtolower($cust->fname." ".$cust->mname." ".$cust->lname." ".$cust->suffix);
                    $print_str .= "Customer : ".$this->append_chars(ucwords($name),"right",19," ")."\r\n";
                    if($order['type'] == 'delivery'){
                        $print_str .= "Contact  : ".$this->append_chars(ucwords($cust->phone),"right",19," ")."\r\n";
                        $address = strtolower($cust->street_no." ".$cust->street_address." ".$cust->city." ".$cust->region." ".$cust->zip);
                        $print_str .= "Address  : ".$this->append_chars(ucwords($address),"right",19," ")."\r\n";
                    }
                }

                if($main_db){
                    $this->db = $this->load->database('main', TRUE);
                }

                $print_str .= PAPER_LINE_SINGLE."\r\n";
            }
            if (!empty($payments)){
                if($order['customer_name']){
                    // $print_str .= $this->append_chars('Name: ',"left",PAPER_RECEIPT_TEXT," ")
                    //             .  $this->append_chars($order['customer_name'],"right",PAPER_RECEIPT_INPUT," ")."\r\n"."\r\n";
                    $print_str .=  $this->append_chars("Name:",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars($order['customer_name'],"right",PAPER_RECEIPT_INPUT,"")
                                    ."\r\n";
                    $print_str .=  $this->append_chars("     ",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT+(strlen("Business Style:") - strlen("Name:")),"-")
                                    ."\r\n"."\r\n";                                    
                }else{
                    // $print_str .= $this->append_chars('Name:',"left",PAPER_RECEIPT_TEXT," ")
                    //             .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    $print_str .=  $this->append_chars("Name:",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT+(strlen("Business Style:") - strlen("Name:")),"_")
                                    ."\r\n"."\r\n";
                }
                if($order['cust_address']){
                    // $print_str .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
                    //             .  $this->append_chars($order['cust_address'],"right",PAPER_RECEIPT_INPUT," ")."\r\n"."\r\n";
                    $print_str .=  $this->append_chars("Address:",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars($order['cust_address'],"right",PAPER_RECEIPT_INPUT,"")
                                    ."\r\n";
                    $print_str .=  $this->append_chars("        ",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT+(strlen("Business Style:") - strlen("Address:")),"-")
                                    ."\r\n"."\r\n";
                }else{
                    // $print_str .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
                    //             .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    $print_str .=  $this->append_chars("Address:",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT+(strlen("Business Style:") - strlen("Address:")),"_")
                                    ."\r\n"."\r\n";
                }
                if($order['tin']){
                    // $print_str .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
                    //             .  $this->append_chars($order['tin'],"right",PAPER_RECEIPT_INPUT," ")."\r\n"."\r\n";
                    $print_str .=  $this->append_chars("TIN:",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars($order['tin'],"right",PAPER_RECEIPT_INPUT+(strlen("Business Style:") - strlen("TIN:")),"")
                                    ."\r\n";
                    $print_str .=  $this->append_chars("    ",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT+(strlen("Business Style:") - strlen("TIN:")),"-")
                                    ."\r\n"."\r\n";
                }else{
                    // $print_str .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
                    //             .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    $print_str .=  $this->append_chars("TIN:",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT+(strlen("Business Style:") - strlen("TIN:")),"_")
                                    ."\r\n"."\r\n";
                }
                if($order['business_style']){
                    // $print_str .= $this->append_chars('Business Style:',"left",PAPER_RECEIPT_TEXT," ")
                    //             .  $this->append_chars($order['business_style'],"right",PAPER_RECEIPT_INPUT," ")."\r\n"."\r\n";
                    $print_str .=  $this->append_chars("Business Style:",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars($order['business_style'],"right",PAPER_RECEIPT_INPUT,"")
                                    ."\r\n";
                     $print_str .=  $this->append_chars("               ",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"-")
                                    ."\r\n"."\r\n";
                }else{
                    // $print_str .= $this->append_chars('Business Style:',"left",PAPER_RECEIPT_TEXT," ")
                    //             .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    $print_str .=  $this->append_chars("Business Style:",PAPER_TOTAL_COL_1,"  ")
                                    .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")
                                    ."\r\n"."\r\n";
                }
                $print_str .= PAPER_LINE_SINGLE."\r\n";
            }

            // $print_str .= $this->append_chars('Name:',"left",PAPER_RECEIPT_TEXT," ")
            //                 .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
            // $print_str .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
            //                 .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
            // $print_str .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
            //                 .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
            // $print_str .= $this->append_chars('Business Style:',"left",PAPER_RECEIPT_TEXT," ")
            //                 .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";

            //HEADER 3


            // if($order['waiter_id'] != ""){
            //     $print_str .= "FS - ".ucwords(strtolower($order['waiter_name']))."\r\n";
            // }


            // $orddetails = "";
            // if($order['table_id'] != "" || $order['table_id'] != 0)
            //     $orddetails .= $order['table_name']." ";

            // if($order['guest'] != 0)
            //     $orddetails .= "Guest #".$order['guest'];

            // if($order['serve_no'] != 0)
            //     $orddetails .= "Serve #".$order['serve_no'];

            // if($orddetails != "")
            //     $print_str .= $this->append_chars($orddetails,"left",PAPER_TOTAL_COL_2," ")."\r\n";//$this->align_center($orddetails,PAPER_WIDTH," ")."\r\n";
            // else{
            //  $print_str .= "\r\n";
            // }

            $log_user = $this->session->userdata('user');
            if (!empty($payments)) {
                if($add_reprinted){
                    if($order['printed'] >= 1){
                        $print_str .= $this->align_center('[REPRINTED]',PAPER_WIDTH," ")."\r\n";
                        // if($main_db){
                            $this->cashier_model->update_trans_sales(array('printed'=>$order['printed']+1),$order['sales_id']);
                            $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Reprinted Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                        // }
                        
                    }
                    else{
                        // if($main_db){
                            $this->cashier_model->update_trans_sales(array('printed'=>1,'billed'=>1),$order['sales_id']);
                         
                          
                            if(!$return_print_str){
                                $log_id =  $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                            }
                        // }
                    }
                }
                else{
                    // if($main_db){
                        $this->cashier_model->update_trans_sales(array('printed'=>1,'billed'=>1),$order['sales_id']);
                        if(!$return_print_str){
                             $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                        }
                    // }
                }
            }
            else{
                // if($main_db){
                    $this->cashier_model->update_trans_sales(array('billed'=>1),$order['sales_id']);
                    $log_id =  $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Billing on Sales Order #".$order['sales_id'],$order['sales_id']);
                // }
            }

            // if(LOCALSYNC){
            //     // if(isset($log_id)){
            //          // $this->sync_model->add_logs($log_id);
            //     // }

            //     $this->sync_model->update_trans_sales($sales_id);

            // }

            
            if($order['type'] == 'delivery' && $order['customer_id'] != ""){
            
            }


            // $print_str .= PAPER_LINE."\r\n";          

            // $print_str .= $this->append_chars("","right",PAPER_WIDTH,"=")."\r\n";

            $pre_total = 0;
            $post_details = array();
            $post_details_nc = array();
            $discs_items = array();
            foreach ($discs as $disc) {
                if(isset($disc['items']))
                    $discs_items[$disc['type']] = $disc['items'];
            }

            // echo "<pre>",print_r($details),"</pre>";die();

            $dscTxt = array();
            foreach ($details as $line_id => $val) {
                foreach ($discs_items as $type => $dissss) {
                    if(in_array($line_id, $dissss)){
                        $qty = 1;
                        if(isset($dscTxt[$val['menu_id'].'_'.$val['price']][$type]['qty'])){
                            $qty = $dscTxt[$val['menu_id'].'_'.$val['price']][$type]['qty'] + 1;
                        }
                        $dscTxt[$val['menu_id'].'_'.$val['price']][$type] = array('txt' => '#'.$type,'qty' => $qty);
                    }
                }
            }

            foreach ($details as $line_id => $val) {

                $prce = $val['price'];
                // if(WEIGHTED_CATEGORY != 0){
                //     if($val['menu_cat_id'] == WEIGHTED_CATEGORY){
                //         $prce = ($val['price'] * $val['qty']) + $line_id;
                //     }
                // }

                if($val['is_takeout'] == 1){
                    if (!isset($post_details_nc[$val['menu_id'].'_'.$prce])) {
                        $dscsacs = array();
                        if(isset($dscTxt[$val['menu_id'].'_'.$prce])){
                            $dscsacs = $dscTxt[$val['menu_id'].'_'.$prce];
                        }
                        $remarksArr = array();
                        if($val['remarks'] != '')
                            $remarksArr = array($val['remarks']." x ".$val['qty']);

                        if(SHOW_SKU){
                            $iname = "[".$val['code']."]".$val['name'];
                        }else{
                            $iname = $val['name'];
                        }

                        // $post_details_nc[$val['menu_id'].'_'.$val['price']] = array(
                        $post_details_nc[$val['menu_id'].'_'.$prce] = array(
                            'name' => $iname,
                            'code' => $val['code'],
                            'price' => $val['price'],
                            'no_tax' => $val['no_tax'],
                            'discount' => $val['discount'],
                            'qty' => $val['qty'],
                            'discounted'=>$dscsacs,
                            'remarks'=>$remarksArr,
                            'modifiers' => array()
                        );
                    } else {
                        if($val['remarks'] != "")
                            $post_details_nc[$val['menu_id'].'_'.$prce]['remarks'][]= $val['remarks']." x ".$val['qty'];
                        $post_details_nc[$val['menu_id'].'_'.$prce]['qty'] += $val['qty'];
                    }

                    if (empty($val['modifiers']))
                        continue;

                    $modifs = $val['modifiers'];
                    $n_modifiers = $post_details_nc[$val['menu_id'].'_'.$prce]['modifiers'];
                    foreach ($modifs as $vv) {
                        if (!isset($n_modifiers[$vv['id']])) {
                            $n_modifiers[$vv['id']] = array(
                                'name' => $vv['name'],
                                'price' => $vv['price'],
                                'qty' => $val['qty'],
                                'discount' => $vv['discount']
                            );
                        } else {
                            $n_modifiers[$vv['id']]['qty'] += $val['qty'];
                        }
                    }
                    $post_details_nc[$val['menu_id'].'_'.$prce]['modifiers'] = $n_modifiers;
                }else{
                    if (!isset($post_details[$val['menu_id'].'_'.$prce])) {
                        $dscsacs = array();
                        if(isset($dscTxt[$val['menu_id'].'_'.$prce])){
                            $dscsacs = $dscTxt[$val['menu_id'].'_'.$prce];
                        }
                        $remarksArr = array();
                        if($val['remarks'] != '')
                            $remarksArr = array($val['remarks']." x ".$val['qty']);
                        
                        if(SHOW_SKU){
                            $iname = "[".$val['code']."]".$val['name'];
                        }else{
                            $iname = $val['name'];
                        }

                        $post_details[$val['menu_id'].'_'.$prce] = array(
                            'name' => $iname,
                            'code' => $val['code'],
                            'price' => $val['price'],
                            'no_tax' => $val['no_tax'],
                            'discount' => $val['discount'],
                            'qty' => $val['qty'],
                            'discounted'=>$dscsacs,
                            'remarks'=>$remarksArr,
                            'modifiers' => array()
                        );
                    } else {
                        if($val['remarks'] != "")
                            $post_details[$val['menu_id'].'_'.$prce]['remarks'][]= $val['remarks']." x ".$val['qty'];
                        $post_details[$val['menu_id'].'_'.$prce]['qty'] += $val['qty'];
                    }

                    if (empty($val['modifiers']))
                        continue;

                    $modifs = $val['modifiers'];
                    $n_modifiers = $post_details[$val['menu_id'].'_'.$prce]['modifiers'];
                    foreach ($modifs as $vv) {
                        if (!isset($n_modifiers[$vv['id']])) {
                            $n_modifiers[$vv['id']] = array(
                                'name' => $vv['name'],
                                'price' => $vv['price'],
                                'qty' => $vv['qty'],
                                'discount' => $vv['discount'],
                                // 'mod_line_id' => $vv['mod_line_id'],
                                'subcategories' => array()
                            );

                            if(isset($vv['submodifiers']) && count($vv['submodifiers']) > 0){
                                $submods = array();
                                foreach($vv['submodifiers'] as $subid => $smods){
                                    if($vv['id'] == $smods['mod_id']){

                                        // $submods = $n_modifiers[$vv['id']]['submodifiers'];

                                        // if(isset($submods['sales_submod_id'])){}

                                        $submods[$smods['sales_submod_id']] = array(
                                            'name'=>$smods['name'],
                                            'qty'=>$smods['qty'],
                                            'price'=>$smods['price'],
                                            'mod_line_id'=>$smods['mod_line_id'],
                                        );
                                        
                                    }
                                }
                                $n_modifiers[$vv['id']]['submodifiers'] = $submods;
                            }


                        } else {
                            $n_modifiers[$vv['id']]['qty'] += $vv['qty'];

                            if(isset($n_modifiers[$vv['id']]['submodifiers']) && count($n_modifiers[$vv['id']]['submodifiers']) > 0){
                                $submods = $n_modifiers[$vv['id']]['submodifiers'];
                                foreach($vv['submodifiers'] as $subid => $smods){
                                    

                                    if($vv['id'] == $smods['mod_id']){

                                        if(!isset($submods[$smods['sales_submod_id']])){
                                            $submods[$smods['sales_submod_id']] = array(
                                                'name'=>$smods['name'],
                                                'qty'=>$smods['qty'],
                                                'price'=>$smods['price'],
                                                'mod_line_id'=>$smods['mod_line_id'],
                                            );
                                            $n_modifiers[$vv['id']]['submodifiers'] = $submods;
                                        }

                                        
                                    }
                                }
                            }

                        }
                    }
                    $post_details[$val['menu_id'].'_'.$prce]['modifiers'] = $n_modifiers;
                }

            }
            /* END NEW BLOCK */
            $tot_qty = 0;
              $print_str .= $this->append_chars("QTY" ,"right",PAPER_DET_COL_1," ");
               $print_str .= $this->append_chars("  Item Description","right",PAPER_DET_COL_2," ").
                $this->append_chars("Amount","left",PAPER_DET_COL_3," ")."\r\n\r\n";
            foreach ($post_details as $val) {
                $tot_qty += $val['qty'];
                $print_str .= $this->append_chars($val['qty'],"right",4," ");

                $len = strlen($val['name']);

                if($val['qty'] == 1){
                    $lgth = 19;
                }else{
                    $lgth = 13;
                }

                if($len > $lgth){
                    // $arr2 = str_split($val['name'], $lgth);
                    $arr2 = explode("\n", wordwrap($val['name'], 24, "\n"));
                    $counter = 1;
                    $sec_line = "";
                    foreach($arr2 as $k => $vv){
                        if($counter == 1){
                            if ($val['qty'] == 1) {
                                $print_str .= $this->append_chars(substrwords($vv,100,""),"right",24," ").
                                    $this->append_chars(number_format($val['price']/$splt,2),"left",10," ")."\r\n";
                            } else {
                                $print_str .= $this->append_chars(substrwords($vv,100,"")."@".$val['price'],"right",24," ").
                                    $this->append_chars(' '.number_format(($val['price'] * $val['qty'])/$splt,2),"left",10," ")."\r\n";
                            }
                        }else{

                            $sec_line .= $vv;
                            // if ($val['qty'] == 1) {
                                // $print_str .= $this->append_chars(" ","right",PAPER_DET_COL_1,"  ");
                                // $print_str .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").
                                //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                            // } else {
                                // $print_str .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
                                //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                            // }
                        }
                        $counter++;
                    }

                    $len2 = strlen($sec_line);

                    if($len2 > 19){
                        $arr2nd = str_split($sec_line, 21);
                        $counter = 1;

                        foreach($arr2nd as $k => $vv){
                            $print_str .= $this->append_chars(" ","right",4,"  ");
                            $print_str .= $this->append_chars(substrwords($vv,100,""),"right",24," ").
                            $this->append_chars("","left",10," ")."\r\n";
                        }

                    }else{
                        $print_str .= $this->append_chars(" ","right",4,"  ");
                        $print_str .= $this->append_chars(substrwords($sec_line,100,""),"right",24," ").
                         $this->append_chars("","left",10," ")."\r\n";
                    }
                    
                    if ($val['qty'] == 1) {
                        $pre_total += $val['price'];
                    }else{
                        $pre_total += $val['price'] * $val['qty'];
                    }
                }else{
                    if ($val['qty'] == 1) {
                        $print_str .= $this->append_chars(substrwords($val['name'],100,""),"right",24," ").
                            $this->append_chars(number_format($val['price']/$splt,2),"left",10," ")."\r\n";
                        $pre_total += $val['price'];
                    } else {
                        $print_str .= $this->append_chars(substrwords($val['name'],100,"")."@".$val['price'],"right",24," ").
                            $this->append_chars(number_format(($val['price'] * $val['qty'])/$splt,2),"left",10," ")."\r\n";
                        $pre_total += $val['price'] * $val['qty'];
                    }
                }
                if(count($val['discounted']) > 0){
                    foreach ($val['discounted'] as $dssstxt) {
                      $print_str .= "      ";
                      $print_str .= $this->append_chars($dssstxt['txt']." x ".number_format($dssstxt['qty']/$splt,2),"right",PAPER_DET_COL_2," ")."\r\n";
                    }
                }
                if(isset($val['remarks']) && count($val['remarks']) > 0){
                    // if(KERMIT){
                    //     foreach ($val['remarks'] as $rmrktxt) {
                    //         $print_str .= "     * ";
                    //         $print_str .= $this->append_chars(ucwords($rmrktxt),"right",PAPER_DET_COL_2," ")."\r\n";
                    //     }
                    // }
                }

                if (empty($val['modifiers']))
                    continue;

                $modifs = $val['modifiers'];
                foreach ($modifs as $vv) {
                    $print_str .= $this->append_chars("*".$vv['qty'],"right",4," ");
                    // $print_str .= "*".$vv['qty'];
                    // $print_str .= "   * 1 ";

                    if ($vv['qty'] == 1) {
                        $print_str .= $this->append_chars(substrwords($vv['name'],100,""),"right",24," ")
                            .$this->append_chars(number_format($vv['price']/$splt,2),"left",10," ")."\r\n";
                        $pre_total += $vv['price'];
                    } else {
                        $print_str .= $this->append_chars(substrwords($vv['name'],100,"")." @ ".$vv['price'],"right",24," ")
                            .$thizero_rateds->append_chars(number_format(($vv['price'] * $vv['qty'])/$splt,2),"left",10," ")."\r\n";
                        $pre_total += $vv['price'] * $vv['qty'];
                    }

                    if(isset($vv['submodifiers']) > 0){

                        foreach($vv['submodifiers'] as $subid => $smod){
                            // $print_str .= "   ";
                            $print_str .= $this->append_chars("","right",4," ");
                            if($smod['qty'] == 1){
                                $print_str .= $this->append_chars(substrwords($smod['name'],100,""),"right",24," ")
                                .$this->append_chars(number_format(($smod['price'] * $smod['qty'])/$splt,2),"left",10," ")."\r\n";
                            }else{
                                $print_str .= $this->append_chars($smod['qty'] .' '.substrwords($smod['name'],100,""),"right",24," ")
                                .$this->append_chars(number_format(($smod['price'] * $smod['qty'])/$splt,2),"left",10," ")."\r\n";
                            }
                            $pre_total += $smod['qty']*$smod['price'];
                        }
                    }
                }
                

                $print_str .= "\r\n";
                //DISCOUNT PALATANDAAN
                // if(in_array($val[''], haystack))

            }

            //for no charges
            if($post_details_nc){
                // $print_str .= "\r\n";
                $print_str .= PAPER_LINE."\r\n";
                $print_str .= "          TAKE OUT"."\r\n";
                $print_str .= PAPER_LINE."\r\n";
                foreach ($post_details_nc as $val) {
                    $tot_qty += $val['qty'];
                    $print_str .= $this->append_chars($val['qty'],"right",4," ");

                    $len = strlen($val['name']);

                    if($val['qty'] == 1){
                        $lgth = 21;
                    }else{
                        $lgth = 13;
                    }

                    if($len > $lgth){
                        $arr2 = str_split($val['name'], $lgth);
                        $counter = 1;
                        $sec_line = "";
                        foreach($arr2 as $k => $vv){
                            if($counter == 1){
                                if ($val['qty'] == 1) {
                                    $print_str .= $this->append_chars(substrwords($vv,100,""),"right",24," ").
                                        $this->append_chars(number_format($val['price']/$splt,2),"left",10," ")."\r\n";
                                } else {
                                    $print_str .= $this->append_chars(substrwords($vv,100,"")." @".$val['price'],"right",24," ").
                                        $this->append_chars(' '.number_format(($val['price'] * $val['qty'])/$splt,2),"left",10," ")."\r\n";
                                }
                            }else{

                                $sec_line .= $vv;
                                // if ($val['qty'] == 1) {
                                    // $print_str .= $this->append_chars(" ","right",PAPER_DET_COL_1,"  ");
                                    // $print_str .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").
                                    //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                                // } else {
                                    // $print_str .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
                                    //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                                // }
                            }
                            $counter++;
                        }

                        $len2 = strlen($sec_line);

                        if($len2 > 21){
                            $arr2nd = str_split($sec_line, 21);
                            $counter = 1;

                            foreach($arr2nd as $k => $vv){
                                $print_str .= $this->append_chars(" ","right",4,"  ");
                                $print_str .= $this->append_chars(substrwords($vv,100,""),"right",24," ").
                                $this->append_chars("","left",10," ")."\r\n";
                            }

                        }else{
                            $print_str .= $this->append_chars(" ","right",4," ");
                            $print_str .= $this->append_chars(substrwords($sec_line,100,""),"right",24," ").
                             $this->append_chars("","left",10," ")."\r\n";
                        }
                        
                        if ($val['qty'] == 1) {
                            $pre_total += $val['price'];
                        }else{
                            $pre_total += $val['price'] * $val['qty'];
                        }
                    }else{
                        if ($val['qty'] == 1) {
                            $print_str .= $this->append_chars(substrwords($val['name'],100,""),"right",24," ").
                                $this->append_chars(number_format($val['price']/$splt,2),"left",10," ")."\r\n";
                            $pre_total += $val['price'];
                        } else {
                            $print_str .= $this->append_chars(substrwords($val['name'],100,"")." ".$val['price'],"right",24," ").
                                $this->append_chars(number_format(($val['price'] * $val['qty'])/$splt,2),"left",10," ")."\r\n";
                            $pre_total += $val['price'] * $val['qty'];
                        }
                    }
                    if(count($val['discounted']) > 0){
                        foreach ($val['discounted'] as $dssstxt) {
                          $print_str .= "      ";
                          $print_str .= $this->append_chars($dssstxt['txt']." x ".number_format($dssstxt['qty']/$splt,2),"right",PAPER_DET_COL_2," ")."\r\n";
                        }
                    }
                    if(isset($val['remarks']) && count($val['remarks']) > 0){
                        // foreach ($val['remarks'] as $rmrktxt) {
                        //     $print_str .= "     * ";
                        //     $print_str .= $this->append_chars(ucwords($rmrktxt),"right",PAPER_DET_COL_2," ")."\r\n";
                        // }
                    }

                    if (empty($val['modifiers']))
                        continue;

                    $modifs = $val['modifiers'];
                    foreach ($modifs as $vv) {
                        // $print_str .= "   * ".$vv['qty']." ";
                        $print_str .= $this->append_chars("*".$vv['qty'],"right",4," ");

                        if ($vv['qty'] == 1) {
                            $print_str .= $this->append_chars(substrwords($vv['name'],100,""),"right",24," ")
                                .$this->append_chars(number_format($vv['price']/$splt,2),"left",10," ")."\r\n";
                            $pre_total += $vv['price'];
                        } else {
                            $print_str .= $this->append_chars(substrwords($vv['name'],100,"")." ".$vv['price'],"right",24," ")
                                .$this->append_chars(number_format(($vv['price'] * $vv['qty'])/$splt,2),"left",10," ")."\r\n";
                            $pre_total += $vv['price'] * $vv['qty'];
                        }
                    }
                    


                    //DISCOUNT PALATANDAAN
                    // if(in_array($val[''], haystack))

                }
            }

            // $print_str .= $this->append_chars("","right",PAPER_WIDTH,"=");

            // $vat = round($order['amount'] / (1 + BASE_TAX) * BASE_TAX,1);
            $vat = 0;
            if($tax > 0){
                foreach ($tax as $tx) {
                   $vat += $tx['amount'];
                }
            }
            $no_tax_amt = 0;
            foreach ($no_tax as $k=>$v) {
                $no_tax_amt += $v['amount'];
            }

            $zname = "";
            $zcard = "";
            $zero_rated_amt = 0;
            foreach ($zero_rated as $k=>$v) {
                $zero_rated_amt += $v['amount'];
                $zname = $v['name'];
                $zcard = $v['card_no'];
            }
            if($zero_rated_amt > 0){
                $no_tax_amt = 0;
            }

            $print_str .= PAPER_LINE_SINGLE."\r\n";
            // $print_str .= "\r\n".$this->append_chars(ucwords("TOTAL"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(($pre_total),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= $this->append_chars(ucwords("TOTAL QTY"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format(($tot_qty),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // // if(count($discs) > 0){
            //     foreach ($discs as $ds) {
            //         $print_str .= "\r\n".$this->append_chars(strtoupper($ds['type']),"right",28," ").$this->append_chars("P (".number_format($ds['amount'],2).")","left",10," ")."\r\n";
            //     }
            // }
            // $print_str .= "\r\n";
            

            //start of not show for cancelled trans

            if($cancelled == 0){

                $total_discounts = 0;
                $total_discounts_sm = 0;

                $per_item_disc = false;
                foreach ($discounts as $dcs_ci => $dcs) {
                    if($dcs['items'] != ""){
                        $per_item_disc = true;
                    }
                }
                if($per_item_disc){
                    foreach ($discounts as $dcs_ci => $dcs) {
                        // foreach ($dcs['persons'] as $code => $dcp) {
                            // $print_str .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                            // $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                            // $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                            // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                            $total_discounts += $dcs['amount'];
                            $dcAmt = $dcs['amount'];
                            // if(MALL_ENABLED && MALL == 'megamall'){
                            //     if($dcs_ci == PWDDISC){
                            //         $dcAmt = $dcAmt / 1.12;       
                            //     }
                            // }
                            $total_discounts_sm += $dcAmt;
                        // }
                    }
                    $total_discounts_non_vat = 0;
                    foreach ($discounts as $dcs_ci => $dcs) {
                       
                        // foreach ($dcs['persons'] as $code => $dcp) {
                            // $print_str .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                            // $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                            // $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                            // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                            if($dcs['no_tax'] == 1){
                                $total_discounts_non_vat += $dcs['amount'];
                            }
                        // }
                    }

                }else{
                    foreach ($discounts as $dcs_ci => $dcs) {
                        foreach ($dcs['persons'] as $code => $dcp) {
                            // $print_str .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                            // $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                            // $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                            // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                            $total_discounts += $dcp['amount'];
                            $dcAmt = $dcp['amount'];
                            // if(MALL_ENABLED && MALL == 'megamall'){
                            //     if($dcs_ci == PWDDISC){
                            //         $dcAmt = $dcAmt / 1.12;       
                            //     }
                            // }
                            $total_discounts_sm += $dcAmt;
                        }
                    }
                    $total_discounts_non_vat = 0;
                    foreach ($discounts as $dcs_ci => $dcs) {
                       
                        foreach ($dcs['persons'] as $code => $dcp) {
                            // $print_str .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                            // $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                            // $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                            // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                            if($dcs['no_tax'] == 1){
                                $total_discounts_non_vat += $dcp['amount'];
                            }
                        }
                    }
                }

                $total_charges = 0;
                if(count($charges) > 0){
                    foreach ($charges as $charge_id => $opt) {
                        $total_charges += $opt['total_amount'];
                    }
                }
                $local_tax_amt = 0;
                if(count($local_tax) > 0){
                    foreach ($local_tax as $lt_id => $lt) {
                        $local_tax_amt += $lt['amount'];
                    }
                }
                // echo num($total_charges + $local_tax_amt);

                // echo '((('.$order['amount'].' - ('.$total_charges.' + '.$local_tax_amt.') - '.$vat.') - '.$no_tax_amt.'+'.$total_discounts_non_vat.') -'.$zero_rated_amt;


                // $vat_sales = ( ( ( $order['amount'] + $order['ewt_amount'] - ($total_charges + $local_tax_amt) ) - $vat)  - $no_tax_amt + $total_discounts_non_vat ) - $zero_rated_amt;
                $vat_sales = ( ( ( $order['amount'] + $order['ewt1'] + $order['ewt2'] + $order['ewt5'] - ($total_charges + $local_tax_amt) ) - $vat)  - $no_tax_amt + $total_discounts_non_vat ) - $zero_rated_amt;

                // if($order['ewt_amount']){
                //     $vat_sales = $order['total_gross'] / 1.12;
                // }
                // echo '===== '.$vat_sales;
                // $vat_sales = ( ( ( $order['amount'] ) - $vat)  - $no_tax_amt + $total_discounts) - $zero_rated_amt;
                // echo "vat_sales= ((".$order['amount']." - ".$total_charges."))- ".$vat." )- ".$no_tax_amt." + ".$total_discounts." - ".$zero_rated_amt;
                if($vat_sales < 0){
                    $vat_sales = 0;
                }

                $print_str .= $this->append_chars(" " ,"right",2," ");
                $print_str .= $this->append_chars("Total Amount","right",24," ").
                $this->append_chars(number_format($pre_total/$splt,2),"left",12," ")."\r\n";
                if(count($discounts) >0){
                    $less_vat = round(($pre_total - ($order['amount'] - $total_charges + $local_tax_amt ) ) - $total_discounts,2);
                
                    if($less_vat >0){

                        $print_str .= $this->append_chars(" " ,"right",2," ");
                        $print_str .= $this->append_chars(ucwords("Less VAT"),"right",24," ").$this->append_chars('('.number_format($less_vat/$splt,2).')',"left",12," ")."\r\n";
                    }

                  // if(count($dcs['persons']) > 0){
                  //      $print_str .= $this->append_chars(ucwords("Less VAT (12%)"),"right",28," ").$this->append_chars("(".number_format($pre_total - $no_tax_amt,2).")","left",10," ")."\r\n";
                  //  }
                }else{
                    // $print_str .= $this->append_chars("Sub-Total","right",PAPER_DET_COL_2," ").
                    // $this->append_chars(number_format($pre_total,2),"left",PAPER_DET_COL_3," ")."\r\n";
                }
                if(count($discounts) >0){
                    $hasSMPWD = false;

                    if($per_item_disc){
                        foreach ($discounts as $dcs_ci => $dcs) {
                            // foreach ($dcs['persons'] as $code => $dcp) {
                            if($dcs['disc_code'] != "DIPLOMAT" && $dcs['disc_code'] != "QTYDISC"){    
                                $discRateTxt = " (".$dcs['disc_rate']."%)";
                                if($dcs['fix'] == 1){
                                    $discRateTxt = " (".$dcs['disc_rate'].")";
                                }
                                $dcAmt = $dcs['amount'];
                                // if(MALL_ENABLED && MALL == 'megamall'){
                                //     if($dcs_ci == PWDDISC){
                                //         $dcAmt = $dcAmt / 1.12; 
                                //         $hasSMPWD = true;      
                                //     }
                                // }

                                $where = array('disc_code'=>$dcs['disc_code']);
                                $det = $this->site_model->get_details($where,'receipt_discounts');

                                // if(SALES_DISC_DESC){
                                //     if($det[0]->no_tax == 0 && $det[0]->disc_rate != 0){
                                //         $discount_text = "Less SALES DISCOUNT";
                                //     }else{
                                //         $discount_text = "Less ".$det[0]->disc_name." DISCOUNT";
                                //     }
                                // }else{
                                    $discount_text = "Less ".$det[0]->disc_name." DISCOUNT";
                                // }

                                $lend = strlen($discount_text);

                                $lgthd = 21;

                                if($lend > $lgthd){
                                    $arr2 = str_split($discount_text, $lgthd);
                                    $counter = 1;
                                    foreach($arr2 as $k => $vv){
                                        if($counter == 1){
                                            $print_str .= $this->append_chars(" " ,"right",2," ");
                                            $print_str .= $this->append_chars($vv,"right",24," ").$this->append_chars('('.num($dcAmt/$splt).')',"left",12," ")."\r\n";
                                        }else{
                                            $print_str .= $this->append_chars(" " ,"right",2," ");
                                            $print_str .= $this->append_chars($vv,"right",24," ").$this->append_chars('',"left",12," ")."\r\n";
                                        }
                                        $counter++;
                                    }
                                    
                                    
                                }else{
                                    $print_str .= $this->append_chars(" " ,"right",2," ");
                                    $print_str .= $this->append_chars($discount_text,"right",24," ").$this->append_chars('('.num($dcAmt/$splt).')',"left",12," ")."\r\n";
                                }

                                //   $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");
                                // $print_str .= $this->append_chars("Less ".$det[0]->disc_name." DISC","right",PAPER_DET_COL_2," ").$this->append_chars('('.num($dcAmt).")","left",PAPER_DET_COL_3," ")."\r\n";
                                
                            // }
                            }
                        }
                    }else{
                        if(count($dcs['persons']) > 0){
                            // $print_str .= "\r\n";
                            // $print_str .= PAPER_LINE."\r\n";
                            // $print_str .= "          Discount Details"."\r\n";
                            // $print_str .= PAPER_LINE."\r\n";

                            foreach ($discounts as $dcs_ci => $dcs) {
                                if($dcs_ci != "DIPLOMAT" && $dcs_ci != "QTYDISC"){
                                    foreach ($dcs['persons'] as $code => $dcp) {
                                        $discRateTxt = " (".$dcp['disc_rate']."%)";
                                        if($dcs['fix'] == 1){
                                            $discRateTxt = " (".$dcp['disc_rate'].")";
                                        }
                                        $dcAmt = $dcp['amount'];
                                        // if(MALL_ENABLED && MALL == 'megamall'){
                                        //     if($dcs_ci == PWDDISC){
                                        //         $dcAmt = $dcAmt / 1.12; 
                                        //         $hasSMPWD = true;      
                                        //     }
                                        // }

                                        $where = array('disc_code'=>$dcs_ci);
                                        $det = $this->site_model->get_details($where,'receipt_discounts');

                                        // if(SALES_DISC_DESC){

                                        //     if($det[0]->no_tax == 0 && $det[0]->disc_rate != 0){
                                        //         $discount_text = "Less SALES DISCOUNT";
                                        //     }else{
                                        //         // $discount_text = "Less ".$det[0]->disc_name." DISCOUNT";
                                        //         $discRateTxtPercent = "";
                                        //         if($det[0]->disc_rate == 0 && $det[0]->fix == 0 && $det[0]->no_tax == 0){
                                        //             //open pecentage
                                        //             $discRateTxtPercent = " (".$dcp['disc_rate']."%)";
                                        //         }

                                        //         $discount_text = "Less ".$det[0]->disc_name.$discRateTxtPercent." DISCOUNT";
                                        //     }
                                        // }else{
                                            $discRateTxtPercent = "";
                                            if($det[0]->disc_rate == 0 && $det[0]->fix == 0 && $det[0]->no_tax == 0){
                                                //open pecentage
                                                $discRateTxtPercent = " (".$dcp['disc_rate']."%)";
                                            }

                                            $discount_text = "Less ".$det[0]->disc_name.$discRateTxtPercent." DISCOUNT";
                                        // }

                                        
                                        $lend = strlen($discount_text);

                                        $lgthd = 21;
                                        
                                        if($lend > $lgthd){
                                            $arr2 = str_split($discount_text, $lgthd);
                                            $counter = 1;
                                            foreach($arr2 as $k => $vv){
                                                if($counter == 1){
                                                    $print_str .= $this->append_chars(" " ,"right",2," ");
                                                    $print_str .= $this->append_chars($vv,"right",24," ").$this->append_chars('('.num($dcAmt/$splt).')',"left",12," ")."\r\n";
                                                }else{
                                                    $print_str .= $this->append_chars(" " ,"right",2," ");
                                                    $print_str .= $this->append_chars($vv,"right",24," ").$this->append_chars('',"left",12," ")."\r\n";
                                                }
                                                $counter++;
                                            }
                                            
                                            
                                        }else{
                                            $print_str .= $this->append_chars(" " ,"right",2," ");
                                            $print_str .= $this->append_chars($discount_text,"right",24," ").$this->append_chars('('.num($dcAmt/$splt).')',"left",12," ")."\r\n";
                                        }
                                        
                                    }
                                }
                            }
                            // $print_str .= "\r\n";
                            // echo $pre_total." - ".$order['amount']." - ".$total_charges." - ".$total_discounts; die();
                            // $less_vat = ($pre_total - ($order['amount'] - num($total_charges + $local_tax_amt) ) ) - $total_discounts;

                            // $print_str .= $this->append_chars(ucwords("Total Discount"),"right",28," ").$this->append_chars(number_format($total_discounts,2),"left",10," ")."\r\n";
                            // $print_str .= $this->append_chars(ucwords("Total Less VAT"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            // if(MALL_ENABLED && MALL == 'megamall' && $hasSMPWD){
                            //     $print_str .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts_sm + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            // }
                            // else{
                            //     $print_str .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            // }
                        }
                    }

                } 
                // $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");
                // $print_str .= $this->append_chars("Service Charge (8.5%)","right",PAPER_DET_COL_2," ").
                // $this->append_chars(number_format($total_charges,2),"left",PAPER_DET_COL_3," ")."\r\n";
                if(count($charges) > 0){
                    // $print_str .= "\r\n";
                    // $print_str .= PAPER_LINE."\r\n";
                    // $print_str .= "              CHARGES"."\r\n";
                    // $print_str .= PAPER_LINE."\r\n";
                    foreach ($charges as $charge_id => $opt) {
                        $charge_amount = $opt['total_amount'];
                        // if($opt['absolute'] == 0){
                        //     $charge_amount = ($opt['amount'] / 100) * ($order['amount'] - $vat);
                        // }
                        $print_str .= $this->append_chars(" " ,"right",2," ");     
                        if($opt['absolute'] == 1){
                            $print_str .= $this->append_chars($opt['name'],"right",24," ").$this->append_chars(number_format($charge_amount/$splt,2),"left",12," ")."\r\n";
                        }else{
                            $print_str .= $this->append_chars($opt['name'] ."(".$opt['rate']."%)","right",24," ").$this->append_chars(number_format($charge_amount/$splt,2),"left",12," ")."\r\n";
                        }
                // $print_str .= $this->append_chars("Service Charge (8.5%)","right",PAPER_DET_COL_2," ").
                    }
                    // $print_str .= PAPER_LINE."\r\n";
                }
                // if(PRINT_VERSION == 'V2'){    
                //     $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");     
                //     $print_str .="<bold>";
                //     $print_str .= $this->append_chars(number_format($tot_qty/$splt,2)." Item(s) Total Due","right",PAPER_DET_COL_2," ")."</bold><bold>".
                //     $this->append_chars(number_format($order['amount']/$splt,2),"left",PAPER_DET_COL_3," ")."</bold>"."\r\n";
                // }else{

                    $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");
                    $print_str .= $this->append_chars(number_format($tot_qty/$splt)." Item(s)","right",PAPER_DET_COL_2," ")."\r\n";
                    if($order['ewt1']){
                        $print_str .= $this->append_chars(" " ,"right",2," ");
                        $print_str .= $this->append_chars("Less EWT1%","right",24," ").
                        $this->append_chars(num($order['ewt1'],2),"left",12," ")."\r\n";
                    }
                    if($order['ewt2']){
                        $print_str .= $this->append_chars(" " ,"right",2," ");
                        $print_str .= $this->append_chars("Less EWT2%","right",24," ").
                        $this->append_chars(num($order['ewt2'],2),"left",12," ")."\r\n";
                    }
                    if($order['ewt5']){
                        $print_str .= $this->append_chars(" " ,"right",2," ");
                        $print_str .= $this->append_chars("Less EWT5%","right",24," ").
                        $this->append_chars(num($order['ewt5'],2),"left",12," ")."\r\n";
                    }
                    // if(PRINT_VERSION == 'V3'){
                    //     // $print_str .= '<div style="height:25px;border:solid yellow;display:inline-block;">

                    //     // <span style="font-size:15px">
                    //     if($includeExcel){
                            $print_str .= '<div style="text-align:left;widht:50%;border:solid white;display:inline;float:left;margin-left:10px;"><span style="font-size:17px"><b>Total Due</b></span></div>
                                 <div style="text-align:right;widht:100%;border:solid white;float:right;margin-top:-20px;margin-right:10px;"><span style="font-size:17px"><b>'.number_format($order['amount']/$splt,2).'</b></span></div>';
                        $print_str .= "\r\n\r\n"; 
                    //     }else{
                    //      $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," "); 
                    //      $print_str .= $this->append_chars(number_format($tot_qty/$splt)." Item(s) Total Due","right",PAPER_DET_COL_2," ").
                    //     $this->append_chars(number_format($order['amount']/$splt,2),"left",PAPER_DET_COL_3," ")."\r\n";   
                    //     }
                    //     // </div><br>';

                    // }else{
                        // $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," "); 
                        // $print_str .= $this->append_chars(number_format($tot_qty/$splt)." Item(s) Total Due","right",PAPER_DET_COL_2," ").
                        // $this->append_chars(number_format($order['amount']/$splt,2),"left",PAPER_DET_COL_3," ")."\r\n";
                    // }

                // }
                $py_ref = "";
                if (!empty($payments)) {

                    // $print_str .= "\r\n";
                    // $print_str .= "\r\n"."======================================"."\r\n";
                    //   if(PRINT_VERSION=="V2"){
                    //         $print_str .= "STARTCUT==============================ENDCUT";
                    //     }
                    // $print_str .= $this->append_chars("Amount due","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    //   if(PRINT_VERSION=="V2"){
                    //       $print_str .= "STARTCUT==============================ENDCUT";
                    //    }
                    $pay_total = 0;
                    $gft_ctr = 0;
                    $nor_ctr = 0;
                    $py_ref = "";
                    $total_payment = 0;
                    $has_cash_payment = false;
                    foreach ($payments as $payment_id => $opt) {
                        $print_str .= $this->append_chars(" " ,"right",2," "); 
                        if($opt['payment_type'] == 'gc'){
                            $print_str .= $this->append_chars(ucwords('gift cheque'),"right",24," ").
                            $this->append_chars(number_format($opt['amount']/$splt,2),"left",12," ")."\r\n";
                        }else{
                            $print_str .= $this->append_chars(ucwords($opt['payment_type']),"right",24," ").
                            $this->append_chars(number_format($opt['amount']/$splt,2),"left",12," ")."\r\n";
                        }    
                        // $print_str .= $this->append_chars(ucwords($opt['payment_type']),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($opt['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        
                        if($opt['payment_type'] == 'check'){
                            $print_str .= $this->append_chars("     Check # ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                        }else{
                            if (!empty($opt['reference']) && $opt['payment_type'] != 'deposit') {
                                $py_ref = $opt['reference'];
                                // $print_str .= $this->append_chars("     Reference ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                            }
                        }

                        if($opt['payment_type'] == 'foodpanda'){
                            if (!empty($opt['approval_code']))
                                    $print_str .= $this->append_chars("  Order Code: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                        }else if($opt['payment_type'] == 'check'){
                            $print_str .= $this->append_chars("     Bank: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                        }else if($opt['payment_type'] == 'picc'){
                            $print_str .= $this->append_chars("     Name: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                        }else if($opt['payment_type'] == 'egift'){
                            $print_str .= $this->append_chars("  Reference Amount : ".$opt['card_type'],"right",PAPER_WIDTH," ")."\r\n";
                        }else{
                            if (!empty($opt['card_number'])) {

                                if($opt['payment_type'] == 'cash'){
                                    if (!empty($opt['card_type'])) {
                                        $print_str .= $this->append_chars("  Currency Paid in: ".$opt['card_type'],"right",PAPER_WIDTH," ")."\r\n";
                                        $print_str .= $this->append_chars("  Amount: ".num($opt['card_number']),"right",PAPER_WIDTH," ")."\r\n";
                                    }
                                }else{
                                    if (!empty($opt['card_type'])) {
                                        $print_str .= $this->append_chars("  Card Type: ".$opt['card_type'],"right",PAPER_WIDTH," ")."\r\n";
                                    }
                                    $print_str .= $this->append_chars("  Card #: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                                    if (!empty($opt['approval_code']))
                                        $print_str .= $this->append_chars("  Approval #: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                                }

                            }
                        }
                        // $pay_total += $opt['amount'];
                        // if($opt['payment_type'] == 'gc'){
                        //     $gft_ctr++;
                        // }
                        // else
                        //     $nor_ctr++;
                        if($opt['payment_type'] == 'gc'){
                            if($opt['amount'] > $opt['to_pay']){
                                $total_payment  += $opt['to_pay'];
                            }else{
                                $total_payment  += $opt['amount'];
                            }
                        }
                        else{
                            $total_payment  += $opt['amount'];
                        }


                        if($opt['payment_type'] == 'cash' && $is_printed <= 0){
                            $open_drawer = true;
                            $has_cash_payment = true;
                        }

                        
                    }
                    if($pfields){
                        foreach ($pfields as $fid => $val) {
                            $print_str .= $this->append_chars("   ".$val['field_name'].": " .$val['value'],"right",PAPER_WIDTH," ")."\r\n";
                        }
                    }

                     // if(PRINT_VERSION=="V2"){
                     //        $print_str .= "STARTCUT==============================ENDCUT";
                     //    }

                     $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");     
                    // if($gft_ctr == 1 && $nor_ctr == 0)
                    //     $print_str .= $this->append_chars('Change',"right",PAPER_DET_COL_2," ").$this->append_chars(number_format(0,2),"left",PAPER_DET_COL_3," ")."\r\n";
                    // else
                        $print_str .= $this->append_chars('Change',"right",27," ").$this->append_chars(number_format((abs($total_payment - $order['amount']))/$splt,2),"left",9," ")."\r\n";
                 
                       //  if(PRINT_VERSION=="V2"){
                       //    $print_str .= "STARTCUT==============================ENDCUT";
                       // }
                    $print_str .= PAPER_LINE_SINGLE."\r\n";
                    if(NEW_VAT_SALES){
                        $new_vat_sales = $order['amount'] / 1.12;
                    }else{
                        $new_vat_sales = $vat_sales/$splt;

                    }

                     $print_str .= "\r\n".$this->append_chars(" " ,"right",2," ").$this->append_chars(ucwords("VATABLE SALES"),"right",24," ").$this->append_chars(num($new_vat_sales),"left",12," ")."\r\n";
                    // }
                    $print_str .= $this->append_chars(" " ,"right",2," ").$this->append_chars(ucwords("VATEXEMPT"),"right",24," ").$this->append_chars(number_format(($no_tax_amt-$total_discounts_non_vat)/$splt,2),"left",12," ")."\r\n";
                    $print_str .= $this->append_chars(" " ,"right",2," ").$this->append_chars(ucwords("Zero Rated"),"right",24," ").$this->append_chars(number_format($zero_rated_amt/$splt,2),"left",12," ")."\r\n";
                    
                       if($tax > 0){
                            foreach ($tax as $tx) {
                               $print_str .=  $this->append_chars(" " ,"right",2," ").$this->append_chars($tx['name']." Amount","right",24," ").$this->append_chars(number_format(abs($tx['amount']/$splt),2),"left",12," ")."\r\n";
                            }
                        }
                    $print_str .= "\r\n";
                    

                    $total_discount_snr_count = $total_discount_pwd_count = $total_discount_count = 0;
                    if($per_item_disc){
                        foreach ($discounts as $dcs_ci => $dcs) {
                                if($dcs['disc_code'] == 'SNDISC' || $dcs['disc_code'] == 'PWDISC'){
                                    // echo "<pre>",print_r($dcs['persons']),"</pre>";die();
                                    // foreach ($dcs['persons'] as $code => $dcp) {   
                                        if($dcs['disc_code'] == 'SNDISC'){
                                            $total_discount_snr_count += 1;                            
                                        } 
                                        if($dcs['disc_code'] == 'PWDISC'){

                                            $total_discount_pwd_count += 1;                            
                                        } 
                                                                       }
                                // }
                        }
                    }else{
                        foreach ($discounts as $dcs_ci => $dcs) {
                                if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                                    // echo "<pre>",print_r($dcs['persons']),"</pre>";die();
                                    foreach ($dcs['persons'] as $code => $dcp) {   
                                        if($dcs_ci == 'SNDISC'){
                                            $total_discount_snr_count += 1;                            
                                        } 
                                        if($dcs_ci == 'PWDISC'){

                                            $total_discount_pwd_count += 1;                            
                                        } 
                                                                       }
                                }
                        }
                    }

                   
                    // if(SHOW_GUEST_COUNT){
                        $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ").$this->append_chars(ucwords("Total Guest Count"),"right",PAPER_DET_COL_2," ").$this->append_chars((($order['guest'] >0) ? $order['guest']: 1),"left",PAPER_DET_COL_3," ")."\r\n";
                    // }
                    
                    if($total_discount_snr_count > 0){

                        $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ").$this->append_chars(ucwords("Total SC Guest Count"),"right",PAPER_DET_COL_2," ").$this->append_chars($total_discount_snr_count,"left",PAPER_DET_COL_3," ")."\r\n\r\n";
                    }

                    if($total_discount_pwd_count > 0){

                        $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ").$this->append_chars(ucwords("Total PWD Guest Count:"),"right",PAPER_DET_COL_2," ").$this->append_chars($total_discount_pwd_count,"left",PAPER_DET_COL_3," ")."\r\n\r\n";
                    }

                    


                    $print_str .= PAPER_LINE_SINGLE."\r\n\r\n";

                    if ($include_footer) {
                        $rec_footer = "";
                        if($branch['rec_footer'] != ""){
                            $wrap = str_replace ("<br>","\r\n", $branch['rec_footer'] );
                            $exp = explode("\r\n", $wrap);
                            foreach ($exp as $v) {
                                $wrap2 = wordwrap($v,35,"|#|");
                                $exp2 = explode("|#|", $wrap2);  
                                foreach ($exp2 as $v2) {
                                    $rec_footer .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                                }
                            }                        
                        }
                        if($order['inactive'] == 0){
                            $print_str .= $rec_footer;
                        }    
                            $print_str .= "\r\n";
                            $print_str .= $this->align_center("POS Vendor Details",PAPER_WIDTH," ")."\r\n";
                            $print_str .= "\r\n";
                            $print_str .= $this->align_center("PointOne Integrated Tech., Inc.",PAPER_WIDTH," ")."\r\n";
                            $print_str .= $this->align_center("1409 Prestige Tower",PAPER_WIDTH," ")."\r\n";
                            $print_str .= $this->align_center("Ortigas Center, Pasig City",PAPER_WIDTH," ")."\r\n";
                            $print_str .= $this->append_chars('TIN:',"right",PAPER_RD_COL_3," ")
                                       .  $this->append_chars('008543444-000',"left",PAPER_DET_SUBCOL," ")."\r\n";
                            if($branch['accrdn'] != ""){
                                $print_str .= $this->append_chars('Accred. No.',"right",PAPER_RD_COL_3," ")
                                       .  $this->append_chars($branch['accrdn'],"left",PAPER_DET_SUBCOL," ")."\r\n";
                                // $print_str .= $this->align_center('ACCRDN:',PAPER_WIDTH," ")."\r\n".$this->align_center($branch['accrdn'],PAPER_WIDTH," ")."\r\n";
                            }
                            $print_str .= $this->append_chars('Permit No.:',"right",PAPER_RD_COL_3," ")
                                       .  $this->append_chars($branch['permit_no'],"left",PAPER_DET_SUBCOL," ")."\r\n";
                            // $print_str .= $this->append_chars('POS Version:',"right",PAPER_RD_COL_3," ")
                            //            .  $this->append_chars('iPos ver 1.0',"left",PAPER_DET_SUBCOL," ")."\r\n";
                            $print_str .= $this->append_chars('Date Issued:',"right",PAPER_RD_COL_3," ")
                                       .  $this->append_chars('December 22, 2014',"left",PAPER_DET_SUBCOL," ")."\r\n";
                                       // .  $this->append_chars(date2Word($order['datetime']),"right",PAPER_TOTAL_COL_2," ")."\r\n";
                            $print_str .= $this->append_chars('Valid Until:',"right",PAPER_RD_COL_3," ")
                                       .  $this->append_chars(date2Word('December 22, 2025'),"left",PAPER_DET_SUBCOL," ")."\r\n";
                                       // .  $this->append_chars(date2Word( date('Y-m-d',strtotime($order['datetime'].' +5 year')) ),"right",PAPER_TOTAL_COL_2," ")."\r\n";

                        if($branch['contact_no'] != ""){
                            $print_str .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
                                         .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
                        }
                        if($branch['email'] != ""){
                            $print_str .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
                                       .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
                        }
                        if($branch['website'] != ""){
                            $print_str .= $this->align_center("Please visit us at",PAPER_WIDTH," ")."\r\n"
                                         .$this->align_center($branch['website'],PAPER_WIDTH," ")."\r\n";
                        }

                        if($per_item_disc){
                            foreach ($discounts as $dcs_ci => $dcs) {
                                if($dcs['disc_code'] == 'SNDISC'){
                                    // if($dcs_ci == 'SNDISC'){
                                    $print_str .= "\r\n".$this->append_chars("Senior Citizen Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                    $print_str .= "\r\n".$this->append_chars("Senior ID No.: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                    // }
                                    // if($dcs_ci == 'PWDISC'){
                                    //     $print_str .= "\r\n".$this->append_chars("PWD TIN: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                    //     $print_str .= "\r\n".$this->append_chars("OSCA ID No.: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                    // }
                                }elseif($dcs['disc_code'] == 'PWDISC'){
                                    $print_str .= "\r\n".$this->append_chars("PWD Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                    $print_str .= "\r\n".$this->append_chars("PWD ID No.: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");

                                }
                                // elseif($dcs['disc_code'] == 'D1018'){
                                //     $print_str .= "\r\n".$this->append_chars("Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("VIP ID NO: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");

                                // }elseif($dcs['disc_code'] == 'D1006'){
                                //     $print_str .= "\r\n".$this->append_chars("Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("National Athlete ID No.: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                // }elseif($dcs['disc_code'] == 'DIPLOMAT'){
                                //     $print_str .= "\r\n".$this->append_chars("Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("TIN: ","right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("Diplomat ID No: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                else{
                                    $print_str .= "\r\n".$this->append_chars("Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                    $print_str .= "\r\n".$this->append_chars("ID NO.: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                }
                            }
                        }else{
                            foreach ($discounts as $dcs_ci => $dcs) {
                                if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                                    foreach ($dcs['persons'] as $code => $dcp) {
                                        // ."\r\n"
                                        // ."\r\n"
                                        if($dcs_ci == 'SNDISC'){
                                            $print_str .= "\r\n".$this->append_chars("Senior Citizen Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                            $print_str .= "\r\n".$this->append_chars("Senior ID No.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                        }
                                        if($dcs_ci == 'PWDISC'){
                                            $print_str .= "\r\n".$this->append_chars("PWD Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                            $print_str .= "\r\n".$this->append_chars("PWD ID No.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                        }

                                       

                                    }
                                }else{
                                    if($dcs_ci != 'DIPLOMAT'){ 
                                        $print_str .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                        $print_str .= "\r\n".$this->append_chars("ID NO.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                    }
                                }
                                // elseif($dcs_ci == 'D1018'){
                                //              $print_str .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                //             $print_str .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                //             $print_str .= "\r\n".$this->append_chars("VIP ID NO: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");

                                // }elseif($dcs_ci == 'D1006'){
                                //             $print_str .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                //             $print_str .= "\r\n".$this->append_chars("National Athlete ID No.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                         

                                // }elseif($dcs['disc_code'] == 'DIPLOMAT'){
                                //     $print_str .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("TIN: ","right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("Diplomat ID No: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                // }else{
                                //     $print_str .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                //     $print_str .= "\r\n".$this->append_chars("ID NO.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                // }

                            }
                        }


                        // if($zero_rated_amt > 0){
                        //         $print_str .= "\r\n".$this->append_chars("Name: ".$zname,"right",PAPER_TOTAL_COL_1," ");
                        //         $print_str .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                        //         $print_str .= "\r\n".$this->append_chars("TIN: ","right",PAPER_TOTAL_COL_1," ");
                        //         $print_str .= "\r\n".$this->append_chars("Diplomat ID No: ".$zcard,"right",PAPER_TOTAL_COL_1," ");
                        // }

                        if(isset($py_ref) && !empty($py_ref)){
                            $print_str .= "\r\n".$this->append_chars("Name: ","right",PAPER_TOTAL_COL_1," ");
                            $print_str .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                            $print_str .= "\r\n".$this->append_chars("TIN: ","right",PAPER_TOTAL_COL_1," ");
                            $print_str .= "\r\n".$this->append_chars("GC Serial No: ".$py_ref,"right",PAPER_TOTAL_COL_1," ");

                        }
                        // $print_str .= PAPER_LINE."\r\n";
                        if($order['inactive'] == 0){
                            $print_str .= "\r\n";
                            // if(PRINT_VERSION == 'V1'){
                            //     $print_str .= $this->append_chars('Name:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                            //            // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            //     $print_str .= $this->append_chars('Company:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                            //                // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            //     $print_str .= $this->append_chars('Address:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                            //                // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            //     $print_str .= $this->append_chars('TIN:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                            //     $print_str .= $this->append_chars('Signature:',"left",PAPER_RECEIPT_TEXT," ")
                            //            .  $this->append_chars('____________________',"left",PAPER_RECEIPT_INPUT,"_")."\r\n";
                            // }else{
                            //     $print_str .= $this->append_chars('Name:',"left",PAPER_RECEIPT_TEXT," ")
                            //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            //     $print_str .= $this->append_chars('Company:',"left",PAPER_RECEIPT_TEXT," ")
                            //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            //     $print_str .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
                            //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            //     $print_str .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
                            //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            //     $print_str .= $this->append_chars('Signature:',"left",PAPER_RECEIPT_TEXT," ")
                            //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            // }


                           
                                       // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n";
                            // $print_str .= "\r\n";
                            // $print_str .= "\r\n";
                            // $print_str .= PAPER_LINE."\r\n";

                            // if($order['inactive'] == 0){
                            //     $print_str .= $rec_footer;
                            // }  
                            // $print_str .= PAPER_LINE."\r\n";
                           
                            // $print_str .= $this->align_center('This Invoice/Receipt',PAPER_WIDTH," ")."\r\n";
                            // $print_str .= $this->align_center('Shall be valid',PAPER_WIDTH," ")."\r\n";
                            // $print_str .= $this->align_center('Five(5) Years from the date of',PAPER_WIDTH," ")."\r\n";
                            // $print_str .= $this->align_center('The Permit to use.',PAPER_WIDTH," ")."\r\n";



                            // if($branch['pos_footer'] != ""){
                            //     // $print_str .= PAPER_LINE."\r\n";
                            //     $print_str .= "\r\n";
                            //     $wrap = str_replace ("<br>","\r\n", $branch['pos_footer'] );
                            //     $exp = explode("\r\n", $wrap);
                            //     foreach ($exp as $v) {
                            //         $wrap2 = wordwrap($v,35,"|#|");
                            //         $exp2 = explode("|#|", $wrap2);  
                            //         foreach ($exp2 as $v2) {
                            //             $print_str .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                            //         }
                            //     }
                            // }
                        }

                    }

                  
                    if (!empty($order['ref']) && $order['inactive'] == 0) {
                        // $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                        $print_str .= "\r\n\r\n".$this->append_chars("_________________________________","right",PAPER_TOTAL_COL_1," ");
                        $print_str .= "\r\n".$this->append_chars("Customer Signature Over Printed Name","right",PAPER_TOTAL_COL_1," ");
                        // $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                        $print_str .= "\r\n";
                    }
                   // $print_str .="\r\n".$this->align_center("THIS INVOICE/RECEIPT WILL BE VALID FOR FIVE(5) YEARS FROM THE DATE OF THE PERMIT TO USE",PAPER_WIDTH," ")."\r\n";
                        // $print_str .= "\r\n".$this->append_chars("THIS INVOICE/RECEIPT WILL BE VALID FOR","right",PAPER_TOTAL_COL_1," ");
                        // $print_str .= "\r\n".$this->append_chars("FIVE(5) YEARS FROM THE DATE OF THE PERMIT","right",PAPER_TOTAL_COL_1," ");
                        //  $print_str .= "\r\n".$this->append_chars("TO USE","right",PAPER_TOTAL_COL_1," ");
                
                }else{
                    // if(SHOW_GUEST_COUNT){
                    //     $print_str .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ").$this->append_chars(ucwords("Total Guest Count"),"right",PAPER_DET_COL_2," ").$this->append_chars((($order['guest'] >0) ? $order['guest']: 1),"left",PAPER_DET_COL_3," ")."\r\n";
                    // }
                } 

                 
                
                #CONDITION TO NA PINADAGDAG NG TAGAYTAY - FOR SENIOR CITEZEN VIEW VAT PLUS DISCOUNT
                // if(count($discounts) >0){
                //     if(count($dcs['persons']) > 0){
                //         $print_str .= $this->append_chars(ucwords("Less VAT (12%)"),"right",28," ").$this->append_chars("(".number_format($pre_total - $no_tax_amt,2).")","left",10," ")."\r\n";
                //     }
                // }
                // else{
                //     if($tax > 0){
                //         foreach ($tax as $tx) {
                //            $print_str .= $this->append_chars($tx['name']."(".$tx['rate']."%)","right",28," ").$this->append_chars(number_format($tx['amount'],2),"left",10," ")."\r\n";
                //         }
                //     }
                // }


                #CONDITION TO NA PARA SA TAGUEGARAO
                // if($tax > 0){
                //     foreach ($tax as $tx) {
                //        $print_str .= $this->append_chars($tx['name']."(".$tx['rate']."%)","right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format(abs($tx['amount']),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //     }
                // }
                // if(count($local_tax) > 0){
                //     $local_tax_amt = 0;
                //     foreach ($local_tax as $lt_id => $lt) {
                //         $local_tax_amt += $lt['amount'];
                //     }
                //     $print_str .= $this->append_chars(ucwords("LOCAL TAX"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($local_tax_amt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // }
                // if(count($discounts) >0){
                //     $hasSMPWD = false;
                //     if(count($dcs['persons']) > 0){
                //         $print_str .= "\r\n";
                //         // $print_str .= PAPER_LINE."\r\n";
                //         $print_str .= "          Discount Details"."\r\n";
                //         // $print_str .= PAPER_LINE."\r\n";

                //         foreach ($discounts as $dcs_ci => $dcs) {
                //             foreach ($dcs['persons'] as $code => $dcp) {
                //                 $discRateTxt = " (".$dcp['disc_rate']."%)";
                //                 if($dcs['fix'] == 1){
                //                     $discRateTxt = " (".$dcp['disc_rate'].")";
                //                 }
                //                 $dcAmt = $dcp['amount'];
                //                 // if(MALL_ENABLED && MALL == 'megamall'){
                //                 //     if($dcs_ci == PWDDISC){
                //                 //         $dcAmt = $dcAmt / 1.12; 
                //                 //         $hasSMPWD = true;      
                //                 //     }
                //                 // }
                //                 if(KERMIT){
                //                     $print_str .= $this->append_chars($dcs_ci.$discRateTxt,"right",PAPER_TOTAL_COL_1," ").$this->append_chars('P'.num($dcAmt),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //                     $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                //                     $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",PAPER_TOTAL_COL_1," ")."\r\n";
                //                 }else{
                //                      $print_str .= $this->append_chars($dcs_ci.$discRateTxt,"right",PAPER_TOTAL_COL_1," ").$this->append_chars('P'.num($dcAmt),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //                 }
                //             }
                //         }
                //         // $print_str .= "\r\n";
                //         // echo $pre_total." - ".$order['amount']." - ".$total_charges." - ".$total_discounts; die();
                //         $less_vat = round(($pre_total - ($order['amount'] - $total_charges + $local_tax_amt ) ) - $total_discounts,2);
                //         // $less_vat = ($pre_total - ($order['amount'] - num($total_charges + $local_tax_amt) ) ) - $total_discounts;

                //         // $print_str .= $this->append_chars(ucwords("Total Discount"),"right",28," ").$this->append_chars(number_format($total_discounts,2),"left",10," ")."\r\n";
                //         $print_str .= $this->append_chars(ucwords("Total Less VAT"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //         if(MALL_ENABLED && MALL == 'megamall' && $hasSMPWD){
                //             $print_str .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts_sm + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //         }
                //         else{
                //             $print_str .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //         }
                //     }
                // }

                // if(count($charges) > 0){
                //     $print_str .= "\r\n";
                //     // $print_str .= PAPER_LINE."\r\n";
                //     // $print_str .= "              CHARGES"."\r\n";
                //     // $print_str .= PAPER_LINE."\r\n";
                //     foreach ($charges as $charge_id => $opt) {
                //         $charge_amount = $opt['total_amount'];
                //         // if($opt['absolute'] == 0){
                //         //     $charge_amount = ($opt['amount'] / 100) * ($order['amount'] - $vat);
                //         // }
                //         $print_str .= $this->append_chars($opt['name'],"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($charge_amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //     }
                //     // $print_str .= PAPER_LINE."\r\n";
                // }

                if (!empty($payments)) {

                    // $print_str .= "\r\n";
                    // // $print_str .= "\r\n"."======================================"."\r\n";
                    //   if(PRINT_VERSION=="V2"){
                    //         $print_str .= "STARTCUT==============================ENDCUT";
                    //     }
                    // $print_str .= $this->append_chars("Amount due","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    //   if(PRINT_VERSION=="V2"){
                    //       $print_str .= "STARTCUT==============================ENDCUT";
                    //    }
                    // $pay_total = 0;
                    // $gft_ctr = 0;
                    // $nor_ctr = 0;
                  
                    // foreach ($payments as $payment_id => $opt) {

                    //     $print_str .= $this->append_chars(ucwords($opt['payment_type']),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($opt['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        
                    //     if($opt['payment_type'] == 'check'){
                    //         $print_str .= $this->append_chars("     Check # ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                    //     }else{
                    //         if (!empty($opt['reference']) && $opt['payment_type'] != 'deposit') {
                    //             $print_str .= $this->append_chars("     Reference ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                    //         }
                    //     }

                    //     if($opt['payment_type'] == 'foodpanda'){
                    //         if (!empty($opt['approval_code']))
                    //                 $print_str .= $this->append_chars("  Order Code: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                    //     }else if($opt['payment_type'] == 'check'){
                    //         $print_str .= $this->append_chars("     Bank: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                    //     }else if($opt['payment_type'] == 'picc'){
                    //         $print_str .= $this->append_chars("     Name: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                    //     }else{
                    //         if (!empty($opt['card_number'])) {
                    //             if (!empty($opt['card_type'])) {
                    //                 $print_str .= $this->append_chars("  Card Type: ".$opt['card_type'],"right",PAPER_WIDTH," ")."\r\n";
                    //             }
                    //             $print_str .= $this->append_chars("  Card #: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                    //             if (!empty($opt['approval_code']))
                    //                 $print_str .= $this->append_chars("  Approval #: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                    //         }
                    //     }
                    //     $pay_total += $opt['amount'];
                    //     if($opt['payment_type'] == 'gc'){
                    //         $gft_ctr++;
                    //     }
                    //     else
                    //         $nor_ctr++;


                    //     if($opt['payment_type'] == 'cash' && $is_printed <= 0){
                    //         $open_drawer = true;
                    //     }

                        
                    // }
                    // if($gft_ctr == 1 && $nor_ctr == 0)
                    //     $print_str .= $this->append_chars("Change","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(0,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    // else
                    //     $print_str .= $this->append_chars("Change","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(abs($pay_total - $order['amount']),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    // $print_str .= PAPER_LINE."\r\n";

                    // if ($order['ref'] != null && $order['inactive'] == 1) {
                    //     // $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                    //     $print_str .= "\r\n".$this->append_chars("_________________________________","right",PAPER_TOTAL_COL_1," ");
                    //     $print_str .= "\r\n".$this->append_chars("  Customer's Name & Signature","right",PAPER_TOTAL_COL_1," ");
                    //     // $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                    //     $print_str .= "\r\n";
                    // }


                    // if ($include_footer) {
                    //     $rec_footer = "";
                    //     if($branch['rec_footer'] != ""){
                    //         $wrap = str_replace ("<br>","\r\n", $branch['rec_footer'] );
                    //         $exp = explode("\r\n", $wrap);
                    //         foreach ($exp as $v) {
                    //             $wrap2 = wordwrap($v,35,"|#|");
                    //             $exp2 = explode("|#|", $wrap2);  
                    //             foreach ($exp2 as $v2) {
                    //                 $rec_footer .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                    //             }
                    //         }                        
                    //     }
                    //     // if($order['inactive'] == 0){
                    //     //     $print_str .= $rec_footer;
                    //     // }    
                    //         $print_str .= "\r\n";
                    //         $print_str .= $this->align_center("POS Vendor Details",PAPER_WIDTH," ")."\r\n";
                    //         // $print_str .= PAPER_LINE."\r\n";
                    //         $print_str .= $this->align_center("PointOne Integrated Tech., Inc.",PAPER_WIDTH," ")."\r\n";
                    //         $print_str .= $this->align_center("1409 Prestige Tower",PAPER_WIDTH," ")."\r\n";
                    //         $print_str .= $this->align_center("Ortigas Center, Pasig City",PAPER_WIDTH," ")."\r\n";
                    //         $print_str .= $this->append_chars('POS Version:',"right",PAPER_RD_COL_3," ")
                    //                    .  $this->append_chars('iPos ver 1.0',"left",PAPER_DET_SUBCOL," ")."\r\n";
                    //         $print_str .= $this->append_chars('TIN:',"right",PAPER_RD_COL_3," ")
                    //                    .  $this->append_chars('008543444-000',"left",PAPER_DET_SUBCOL," ")."\r\n";
                    //         $print_str .= $this->append_chars('Date Issued:',"right",PAPER_RD_COL_3," ")
                    //                    .  $this->append_chars('December 22, 2014',"left",PAPER_DET_SUBCOL," ")."\r\n";
                    //                    // .  $this->append_chars(date2Word($order['datetime']),"right",PAPER_TOTAL_COL_2," ")."\r\n";
                    //         $print_str .= $this->append_chars('Valid Until:',"right",PAPER_RD_COL_3," ")
                    //                    .  $this->append_chars(date2Word('December 22, 2025'),"left",PAPER_DET_SUBCOL," ")."\r\n";
                    //                    // .  $this->append_chars(date2Word( date('Y-m-d',strtotime($order['datetime'].' +5 year')) ),"right",PAPER_TOTAL_COL_2," ")."\r\n";
                    //         if($branch['accrdn'] != ""){
                    //             $print_str .= $this->append_chars('ACCRDN:',"right",PAPER_RD_COL_3," ")
                    //                    .  $this->append_chars($branch['accrdn'],"left",PAPER_DET_SUBCOL," ")."\r\n";
                    //             // $print_str .= $this->align_center('ACCRDN:',PAPER_WIDTH," ")."\r\n".$this->align_center($branch['accrdn'],PAPER_WIDTH," ")."\r\n";
                    //         }

                    //     if($branch['contact_no'] != ""){
                    //         $print_str .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
                    //                      .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
                    //     }
                    //     if($branch['email'] != ""){
                    //         $print_str .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
                    //                    .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
                    //     }
                    //     if($branch['website'] != ""){
                    //         $print_str .= $this->align_center("Please visit us at",PAPER_WIDTH," ")."\r\n"
                    //                      .$this->align_center($branch['website'],PAPER_WIDTH," ")."\r\n";
                    //     }
                    //     $print_str .= PAPER_LINE."\r\n";
                    //     if($order['inactive'] == 0){
                    //         $print_str .= "\r\n";
                    //         if(PRINT_VERSION == 'V1'){
                    //             $print_str .= $this->append_chars('Name:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                    //                    // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //             $print_str .= $this->append_chars('Company:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                    //                        // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //             $print_str .= $this->append_chars('Address:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                    //                        // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //             $print_str .= $this->append_chars('TIN:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                    //             $print_str .= $this->append_chars('Signature:',"left",PAPER_RECEIPT_TEXT," ")
                    //                    .  $this->append_chars('____________________',"left",PAPER_RECEIPT_INPUT,"_")."\r\n";
                    //         }else{
                    //             $print_str .= $this->append_chars('Name:',"left",PAPER_RECEIPT_TEXT," ")
                    //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //             $print_str .= $this->append_chars('Company:',"left",PAPER_RECEIPT_TEXT," ")
                    //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //             $print_str .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
                    //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //             $print_str .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
                    //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //             $print_str .= $this->append_chars('Signature:',"left",PAPER_RECEIPT_TEXT," ")
                    //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //         }


                           
                    //                    // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n";
                    //         // $print_str .= "\r\n";
                    //         $print_str .= "\r\n";
                    //         $print_str .= PAPER_LINE."\r\n";

                    //         if($order['inactive'] == 0){
                    //             $print_str .= $rec_footer;
                    //         }  
                    //         // $print_str .= PAPER_LINE."\r\n";
                           
                    //         // $print_str .= $this->align_center('This Invoice/Receipt',PAPER_WIDTH," ")."\r\n";
                    //         // $print_str .= $this->align_center('Shall be valid',PAPER_WIDTH," ")."\r\n";
                    //         // $print_str .= $this->align_center('Five(5) Years from the date of',PAPER_WIDTH," ")."\r\n";
                    //         // $print_str .= $this->align_center('The Permit to use.',PAPER_WIDTH," ")."\r\n";



                    //         // if($branch['pos_footer'] != ""){
                    //         //     // $print_str .= PAPER_LINE."\r\n";
                    //         //     $print_str .= "\r\n";
                    //         //     $wrap = str_replace ("<br>","\r\n", $branch['pos_footer'] );
                    //         //     $exp = explode("\r\n", $wrap);
                    //         //     foreach ($exp as $v) {
                    //         //         $wrap2 = wordwrap($v,35,"|#|");
                    //         //         $exp2 = explode("|#|", $wrap2);  
                    //         //         foreach ($exp2 as $v2) {
                    //         //             $print_str .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                    //         //         }
                    //         //     }
                    //         // }
                    //     }

                    // }
                } else {
                    $is_billing = true;
                    $print_str .= "\r\n".$this->append_chars("","right",PAPER_WIDTH,"=");
                    // if(PRINT_VERSION == 'V1'){
                        // $print_str .= "\r\n\r\n".$this->append_chars("Billing Amount","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    // }
                    if(is_array($splits)){
                        $print_str .= $this->append_chars("Split Amount by ".$splits['by'],"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($splits['total']/$splt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    }

                    // if($per_item_disc){
                    //     //for senior deaitls with signature forbiliing
                    //     foreach ($discounts as $dcs_ci => $dcs) {
                    //         if($dcs['disc_code'] == 'SNDISC' || $dcs['disc_code'] == 'PWDISC'){
                    //             // $print_str .= PAPER_LINE."\r\n";
                    //             $print_str .= "\r\n";
                    //             $print_str .= $this->align_center("OSCA/PWD Details",PAPER_WIDTH," ")."\r\n";
                    //             // $print_str .= PAPER_LINE."\r\n";
                    //             // $print_str .= $this->align_center(PAPER_LINE,42," ");
                    //             break;
                    //         }
                    //     }
                    //     foreach ($discounts as $dcs_ci => $dcs) {
                    //         if($dcs['disc_code'] == 'SNDISC' || $dcs['disc_code'] == 'PWDISC'){
                    //             // foreach ($dcs['persons'] as $code => $dcp) {
                    //                 // ."\r\n"
                    //                 // ."\r\n"
                    //                 $print_str .= "\r\n".$this->append_chars("ID NO      : ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                    //                 $print_str .= "\r\n".$this->append_chars("NAME       : ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                    //                 $print_str .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                    //                 $print_str .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                    //                 $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                    //                 // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                    //             // }
                    //         }else{
                    //             if($dcs['disc_code'] != 'DIPLOMAT'){
                    //                 $print_str .= "\r\n".$this->append_chars("ID NO      : ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                    //                 $print_str .= "\r\n".$this->append_chars("NAME       : ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                    //             }
                    //         }
                    //     }
                    // }else{
                    //     //for senior deaitls with signature forbiliing
                    //     foreach ($discounts as $dcs_ci => $dcs) {
                    //         if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                    //             // $print_str .= PAPER_LINE."\r\n";
                    //             $print_str .= "\r\n";
                    //             $print_str .= $this->align_center("OSCA/PWD Details",PAPER_WIDTH," ")."\r\n";
                    //             // $print_str .= PAPER_LINE."\r\n";
                    //             // $print_str .= $this->align_center(PAPER_LINE,42," ");
                    //             break;
                    //         }
                    //     }
                    //     foreach ($discounts as $dcs_ci => $dcs) {
                    //         if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                    //             foreach ($dcs['persons'] as $code => $dcp) {
                    //                 // ."\r\n"
                    //                 // ."\r\n"
                    //                 $print_str .= "\r\n".$this->append_chars("ID NO      : ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                    //                 $print_str .= "\r\n".$this->append_chars("NAME       : ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                    //                 $print_str .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                    //                 $print_str .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                    //                 $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                    //                 // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                    //             }
                    //         }else{
                    //             if($dcs_ci != 'DIPLOMAT'){
                    //                 foreach ($dcs['persons'] as $code => $dcp) {
                    //                     // ."\r\n"
                    //                     // ."\r\n"
                    //                     $print_str .= "\r\n".$this->append_chars("ID NO      : ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                    //                     $print_str .= "\r\n".$this->append_chars("NAME       : ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                    //                     // $print_str .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                    //                     // $print_str .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                    //                     // $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                    //                     // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                    //                 }
                    //             }
                    //         }
                    //     }
                    // }


                    if ($include_footer) {
                        // $print_str .= "\r\n\r\n";
                        if($branch['contact_no'] != ""){
                            $print_str .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
                                         .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
                        }
                        if($branch['email'] != ""){
                            $print_str .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
                                       .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
                        }
                        if($branch['website'] != "")
                            $print_str .= $this->align_center("Please visit us at \r\n".$branch['website'],PAPER_WIDTH," ")."\r\n";
                    }

                }

                if (!empty($payments)) {
                    // $print_str .= "\r\n";
                    // foreach ($discounts as $dcs_ci => $dcs) {
                    //     if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                    //         // $print_str .= PAPER_LINE."\r\n";
                    //         $print_str .= $this->align_center("OSCA/PWD Details",PAPER_WIDTH," ")."\r\n";
                    //         // $print_str .= PAPER_LINE."\r\n";
                    //         // $print_str .= $this->align_center(PAPER_LINE,42," ");
                    //         break;
                    //     }
                    // }
                    // foreach ($discounts as $dcs_ci => $dcs) {
                    //     if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                    //         foreach ($dcs['persons'] as $code => $dcp) {
                    //             // ."\r\n"
                    //             // ."\r\n"
                    //             $print_str .= "\r\n".$this->append_chars("ID NO      : ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                    //             $print_str .= "\r\n".$this->append_chars("NAME       : ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                    //             $print_str .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                    //             $print_str .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                    //             $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                    //             // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                    //         }
                    //     }
                    // }
                    // foreach ($discounts as $dcs_ci => $dcs) {
                    //     if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                    //         // $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                    //         break;
                    //     }
                    // }

                    // if($zero_rated_amt != 0){
                    //     $print_str .= $this->align_center("Diplomat Details",PAPER_WIDTH," ")."\r\n";
                    //     foreach ($zero_rated as $k=>$v) {
                    //         // $zero_rated_amt += $v['amount'];
                    //         $print_str .= "\r\n".$this->append_chars("ID NO      : ".$v['card_no'],"right",PAPER_TOTAL_COL_1," ");
                    //         $print_str .= "\r\n".$this->append_chars("NAME       : ".$v['name'],"right",PAPER_TOTAL_COL_1," ");
                    //         $print_str .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                    //         $print_str .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                    //         $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";   
                    //     }

                    // }

                }

                if($approved_by != null){
                    // $app = $this->site_model->get_user_details($approved_by);
                    // $approver = $app->fname." ".$app->mname." ".$app->lname." ".$app->suffix;
                    // // $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                    // $print_str .= "\r\n".$this->append_chars("Approved By : ".$approver,"right",PAPER_TOTAL_COL_1," ");
                    // $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                    // $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                }

            }
            //end of showing for cancelled trans



            if ($return_print_str) {
                return $print_str;
            }
            // echo "<pre>".$print_str."</pre>";
            // return false;
            $js_rcps = '';

            if(PRINT_VERSION && PRINT_VERSION == 'V2'){

                if(isset($order['ref'])){
                    $order_ref = $order['ref'];
                }else{
                    $order_ref = "";
                }
                // if(BILLING_PRINTER){
                //     if(BILLING_PRINTER != "DEFAULT"){
                // echo $print_str;die();
                         $this->do_print_receipt_v2($print_str,$order['amount'],$is_billing,$open_drawer,$order_ref,$branch['name']);  
                //     }
                // }

                if($openDrawer){
                    $pet = $this->cashier_model->get_pos_settings();
                    $open_drawer_printer = $pet->open_drawer_printer;
                    // if($open_drawer_printer != ""){
                    //     $this->do_print_receipt_v2($print_str);  
                    //     // $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".$open_drawer_printer."\"  ";   
                    // }            
                }
            }else if(PRINT_VERSION && PRINT_VERSION == 'V3'){
                if(ORDERING_STATION && MOBILE_OS){
                    $dev_url = 'http://' . $this->db->hostname.'/ipos/mobile_sales/queue_mobile_bill';
                // $dev_url = 'https://point1solution.net/max_hq_api/api/migrate_trans';
                // if($this->environment == 'dev'){
                    $url = $dev_url;
                // }else{
                    // $url = $this->prod_url.'migrate_data';
                // }

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "post",
                        CURLOPT_POSTFIELDS => json_encode(array('sales_id'=>$sales_id)),
                        CURLOPT_HTTPHEADER => array('Content-Type:application/json',
                        CURLOPT_SSL_VERIFYPEER=>false
                        )
                        
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                        return 'error';
                    } else {
                        // return 'success';

                        
                        $result = json_decode($response);
                        // print_r($response);exit;
                        
                    }
                }else{
                    // $js_rcp = $this->html_print($print_str,true, $sales_id);

                    // $js_rcps[] = array('printer'=>BILLING_PRINTER,'value'=>$js_rcp);
                    // else{
                    // dito nag piprint kapag submit ng payment
                    // for ($i=0; $i < $no_prints; $i++) { 
                        // exec($batfile);
                    $js_rcp = $this->html_print($print_str,true, $sales_id);
                    $js_rcps[] = array('printer'=>BILLING_PRINTER,'value'=>$js_rcp);
                    if($no_prints >= 2){
                        if(defined('RECEIPT_ADDITIONAL_HEADER_BELOW_BRANCH') && !empty(RECEIPT_ADDITIONAL_HEADER_BELOW_BRANCH)){
                            // $print_str2 .= $this->align_center(RECEIPT_ADDITIONAL_HEADER_BELOW_BRANCH,PAPER_WIDTH," ")."\r\n";                

                            if(PRINT_VERSION && PRINT_VERSION=='V3' && file_exists('uploads/brands/logos.png')){
                                $print_str2 .= $this->align_center('<img src="'.base_url().'uploads/brands/logos.png" width="200px" height="150px" style="display: block;
                                  margin-top: 10px;
                                  margin-left: auto;
                                  margin-right: auto;
                                  width: 50%;">',PAPER_WIDTH," ")."\r\n";
                            }
                            else if(PRINT_VERSION && PRINT_VERSION=='V3'){
                                if($includeExcel){
                                    $print_str2 .= '<center><span style="font-size:15px">'.$branch['name'].'</span></center>'."\r\n";
                                }else{
                                    $print_str2 .= $this->align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
                                }
                            }
                            else{
                                $print_str2 .= $this->align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
                            }
                            
                        }

                        if(RECEIPT_OWNED){
                            $print_str2 .= $this->align_center(RECEIPT_OWNED,PAPER_WIDTH," ")."\r\n";
                        }

                        // $wrap = wordwrap($branch['desc'],PAPER_WIDTH,"|#|");
                        // $exp = explode("|#|", $wrap);
                        // foreach ($exp as $v) {
                        //     $print_str2 .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
                        // }
                        $splt = 1;
                        // if($order['guest'] > 1){
                        //     $splt = $order['guest'];
                        // }

                        $wrap = wordwrap($branch['address'],PAPER_WIDTH,"|#|");
                        $exp = explode("|#|", $wrap);
                        foreach ($exp as $v) {
                            $print_str2 .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
                        }   
                            // $print_str2 .= "\r\n";
                        if($branch['tin'] != ""){
                            // $print_str2 .= $this->append_chars('VAT REG TIN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['tin'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
                            $print_str2 .= $this->align_center('VAT REG TIN:'.$branch['tin'],PAPER_WIDTH," ")."\r\n";
                        }
                        // if($branch['accrdn'] != ""){
                        //     $print_str2 .= $this->append_chars('ACCRDN:',"right",PAPER_DET_SUBCOL," ").$this->append_chars($branch['accrdn'],"right",PAPER_TOTAL_COL_1," ")."\r\n";
                        // }
                        if($branch['serial'] != ""){
                            $print_str2 .= $this->align_center('S/N:'.$branch['serial'],PAPER_WIDTH," ")."\r\n";
                            // $print_str2 .= $this->append_chars('SN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['serial'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
                        }
                        if($branch['machine_no'] != ""){
                            $print_str2 .= $this->align_center('MIN:'.$branch['machine_no'],PAPER_WIDTH," ")."\r\n";
                            // $print_str2 .= $this->append_chars('MIN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['machine_no'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
                        }
                        // if($branch['permit_no'] != ""){
                        //     $print_str2 .= $this->append_chars('PERMIT:'.$branch['permit_no'],"right",PAPER_WIDTH," ")."\r\n";
                        //     // $print_str2 .= $this->append_chars('PERMIT:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['permit_no'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
                        // }


                        $print_str2 .= "\r\n";
                          // $print_str2 .= $this->append_chars(date('M d, Y h:i A',strtotime($order['datetime'])),"left",PAPER_WIDTH," ")."\r\n";
                        $print_str2 .=  $this->align_center(date('M d, Y h:i A',strtotime($order['datetime'])),PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";

                        // if (!empty($order['void_ref']) || $order['inactive'] == 1) {
                        if ($order['ref'] != null && $order['inactive'] == 1) {
                            $print_str2 .= $this->align_center("***** VOIDED TRANSACTION *****",PAPER_WIDTH," ")."\r\n";
                            $print_str2 .= $order['reason']."\r\n\r\n";
                        }
                        $cancelled = 0;
                        if ($order['ref'] == null && $order['inactive'] == 1) {
                            $print_str2 .= $this->align_center("***** CANCELLED TRANSACTION *****",PAPER_WIDTH," ")."\r\n";
                            $print_str2 .= $order['reason']."\r\n\r\n";
                            $cancelled = 1;
                        }
                         // $print_str2 .= "\r\n".PAPER_LINE."\r\n";
                        // $header_print_str = $print_str2;
                         // $header_print_str .= PAPER_LINE."\r\n";
                            // if (!empty($payments)){
                            //         $header_print_str = "Receipt # ".$order['ref']."\r\n";
                            //         // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
                            // }
                            // else{
                            //     $header_print_str = "Reference # ".$order['sales_id']."\r\n";
                            //         // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
                            // }
                         // $header_print_str .= "\r\n".PAPER_LINE."\r\n";

                       
                        // $header_print_str .= PAPER_LINE."\r\n";

                        // $print_str2 .= $this->align_center(date('Y-m-d H:i:s',strtotime($order['datetime']))." ".$order['terminal_name']." ".$order['name'],42," ")."\r\n";


                        if($order['update_date']){
                            // $print_str2 .= $this->append_chars(ucwords($order['name']),"right",PAPER_RD_COL_3_3," ").$this->append_chars(date('Y-m-d H:i:s',strtotime($order['update_date'])),"left",PAPER_TOTAL_COL_2," ")."\r\n"
                            //       . $this->append_chars("Terminal ID : ".$order['terminal_code'],"right",PAPER_RD_COL_3," ").$this->append_chars("","left",PAPER_TOTAL_COL_2," ")."\r\n"
                            //     ."\r\n";
                              // $print_str2 .= $this->append_chars("Terminal ID : ".$order['terminal_code'],"right",PAPER_RD_COL_3," ").$this->append_chars("","left",PAPER_TOTAL_COL_2," ")."\r\n";
                                // ."\r\n";
                                // .PAPER_LINE."\r\n";
                        }else{
                            // $print_str2 .= $this->append_chars(ucwords($order['name']),"right",PAPER_RD_COL_3_3," ").$this->append_chars(date('Y-m-d H:i:s',strtotime($order['datetime'])),"left",PAPER_TOTAL_COL_2," ")."\r\n"
                            //       . $this->append_chars("Terminal ID : ".$order['terminal_code'],"right",PAPER_RD_COL_MID," ").$this->append_chars("","left",PAPER_TOTAL_COL_2," ")."\r\n"
                            //     ."\r\n";
                            // $print_str2 .=  $this->append_chars("Terminal ID : ".$order['terminal_code'],"right",PAPER_RD_COL_MID," ").$this->append_chars("","left",PAPER_TOTAL_COL_2," ")."\r\n"
                                // ."\r\n";
                                // .PAPER_LINE."\r\n";
                            
                        }
                        $print_str2 .= "\r\n";

                        if (!empty($payments)){
                             if(PRINT_VERSION=="V2"){
                                if($order['type'] == "mgtpromo"){
                                    $print_str2 .=  $this->align_center("MGTPROMO #: ".$order['ref']."",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                                }else{
                                    $print_str2 .=  $this->align_center("<bold>OFFICIAL RECEIPT</bold>",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                                    $print_str2 .=  $this->align_center("<bold>OR #: ".$order['ref']."</bold>",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                                }
                                 // $print_str2 .=  $this->align_center("<bold>QUEUING #: ".$order['terminal_id'].'-'.$order['queue_id']."</bold>",PAPER_RD_COL_MID,"")."\r\n";


                              }else{
                                if($order['type'] == "mgtpromo"){
                                    $print_str2 .=  $this->align_center("MGTPROMO #: ".$order['ref']."",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                                }else{
                                    $print_str2 .=  $this->align_center("OFFICIAL  RECEIPT ",PAPER_RD_COL_MID,"  ")."\r\n";// "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                                    
                                    if(PRINT_VERSION && PRINT_VERSION=='V3'){
                                        // $print_str2 .=  $this->align_center("<span style='font-size:15px'>OR #: ".$order['ref'].'</span>',PAPER_RD_COL_MID,"  ")."\r\n";
                                        if($includeExcel){
                                         $print_str2 .=  $this->align_center("<span style='font-size:15px'>OR #: ".$order['ref'].'</span>',PAPER_RD_COL_MID,"  ")."\r\n";
                                        }else{
                                            $print_str2 .=  $this->align_center("OR #: ".$order['ref'],PAPER_RD_COL_MID,"  ")."\r\n"; 
                                        }
                                    }else{
                                       $print_str2 .=  $this->align_center("OR #: ".$order['ref'],PAPER_RD_COL_MID,"  ")."\r\n"; 
                                    }
                                }

                                // $print_str2 .=  $this->align_center("QUEUING #: ".$order['terminal_id'].'-'.$order['queue_id'],PAPER_RD_COL_MID,"")."\r\n";
                              }
                                // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
                        } else{
                            $print_str2 .= $this->align_center("REFERENCE #: ".$order['sales_id'],PAPER_RD_COL_MID," ");//"Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
                            // $print_str2 .=  $this->align_center($order['sales_id'],PAPER_RD_COL_MID,"  ")."\r\n";

                            // $print_str2 .=  $this->align_center("QUEUING",PAPER_RD_COL_MID," ");
                            // $print_str2 .=  $this->align_center("#: ".$order['terminal_id'].'-'.$order['queue_id'],PAPER_RD_COL_MID,"  ")."\r\n";

                                // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
                        }
                        if(PRINT_VERSION=="V2"){
                         $print_str2 .=  $this->align_center("\r\n <bold>".strtoupper($order['type'])."</bold>",PAPER_RD_COL_MID," ")."\r\n";
                        }else if(PRINT_VERSION=="V3"){
                        if($includeExcel){
                                $print_str2 .=  $this->align_center("\r\n"."<span style='font-size:15px'>".strtoupper($order['type'])."</span>",PAPER_RD_COL_MID," ")."\r\n";
                                $print_str2 .=  $this->align_center("\r\n"."<span style='font-size:15px'>"."Table No:  ".$order['table_name']."</span>",PAPER_RD_COL_MID," ")."\r\n";
                            }else{
                                $print_str2 .=  $this->align_center("\r\n".strtoupper($order['type']),PAPER_RD_COL_MID," ")."\r\n";
                                $print_str2 .=  $this->align_center("Table No:  ".$order['table_name'],PAPER_RD_COL_MID,"  ")."\r\n";
                            }
                        }else{
                         $print_str2 .=  $this->align_center("\r\n".strtoupper($order['type']),PAPER_RD_COL_MID," ")."\r\n";
                         $print_str2 .=  $this->align_center("Table No:  ".$order['table_name'],PAPER_RD_COL_MID,"  ")."\r\n";
                        }
                        // $print_str2 .= $this->append_chars("Table No:  ".$order['table_name'],"left",PAPER_WIDTH," ")."\r\n";
                        // $print_str2 .= $this->append_chars("Standee No:  ".$order['serve_no'],"left",PAPER_WIDTH," ")."\r\n";
                        // $print_str2 .= $this->append_chars("Register No:  ".$order['terminal_id'],"left",PAPER_WIDTH," ")."\r\n";
                        // $print_str2 .= $this->append_chars("Cashier Name:  ".ucwords($order['name']),"left",PAPER_WIDTH," ")."\r\n";

                        $print_str2 .=  $this->align_center("Serve No:  ".$order['serve_no'],PAPER_RD_COL_MID," ")."\r\n";
                        $print_str2 .=  $this->align_center("Register No:  ".$order['terminal_id'],PAPER_RD_COL_MID,"  ")."\r\n";
                        $print_str2 .=  $this->align_center("Cashier Name:  ".ucwords($order['name']),PAPER_RD_COL_MID,"  ")."\r\n";

                        $print_str2 .= PAPER_LINE_SINGLE."\r\n";

                        if($order['customer_id'] != ""){
                            if($main_db){
                                $this->db = $this->load->database('default', TRUE);
                            }
                            $this->load->model('dine/customers_model');
                            $customers = $this->customers_model->get_customer($order['customer_id']);
                            if(count($customers) > 0){
                                $cust = $customers[0];
                                $name = strtolower($cust->fname." ".$cust->mname." ".$cust->lname." ".$cust->suffix);
                                $print_str2 .= "Customer : ".$this->append_chars(ucwords($name),"right",19," ")."\r\n";
                                if($order['type'] == 'delivery'){
                                    $print_str2 .= "Contact  : ".$this->append_chars(ucwords($cust->phone),"right",19," ")."\r\n";
                                    $address = strtolower($cust->street_no." ".$cust->street_address." ".$cust->city." ".$cust->region." ".$cust->zip);
                                    $print_str2 .= "Address  : ".$this->append_chars(ucwords($address),"right",19," ")."\r\n";
                                }
                            }

                            if($main_db){
                                $this->db = $this->load->database('main', TRUE);
                            }

                            $print_str2 .= PAPER_LINE_SINGLE."\r\n";
                        }

                        if($order['customer_name']){
                            // $print_str2 .= $this->append_chars('Name: ',"left",PAPER_RECEIPT_TEXT," ")
                            //             .  $this->append_chars($order['customer_name'],"right",PAPER_RECEIPT_INPUT," ")."\r\n"."\r\n";
                            $print_str2 .=  $this->append_chars("Name:",PAPER_TOTAL_COL_1,"  ")
                                            .  $this->append_chars($order['customer_name'],"right",PAPER_RECEIPT_INPUT,"_")
                                            ."\r\n"."\r\n";
                        }else{
                            // $print_str2 .= $this->append_chars('Name:',"left",PAPER_RECEIPT_TEXT," ")
                            //             .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            $print_str2 .=  $this->append_chars("Name:",PAPER_TOTAL_COL_1,"  ")
                                            .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")
                                            ."\r\n"."\r\n";
                        }
                        if($order['cust_address']){
                            // $print_str2 .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
                            //             .  $this->append_chars($order['cust_address'],"right",PAPER_RECEIPT_INPUT," ")."\r\n"."\r\n";
                            $print_str2 .=  $this->append_chars("Address:",PAPER_TOTAL_COL_1,"  ")
                                            .  $this->append_chars($order['cust_address'],"right",PAPER_RECEIPT_INPUT,"_")
                                            ."\r\n"."\r\n";
                        }else{
                            // $print_str2 .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
                            //             .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            $print_str2 .=  $this->append_chars("Address:",PAPER_TOTAL_COL_1,"  ")
                                            .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")
                                            ."\r\n"."\r\n";
                        }
                        if($order['tin']){
                            // $print_str2 .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
                            //             .  $this->append_chars($order['tin'],"right",PAPER_RECEIPT_INPUT," ")."\r\n"."\r\n";
                            $print_str2 .=  $this->append_chars("TIN:",PAPER_TOTAL_COL_1,"  ")
                                            .  $this->append_chars($order['tin'],"right",PAPER_RECEIPT_INPUT,"_")
                                            ."\r\n"."\r\n";
                        }else{
                            // $print_str2 .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
                            //             .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            $print_str2 .=  $this->append_chars("TIN:",PAPER_TOTAL_COL_1,"  ")
                                            .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")
                                            ."\r\n"."\r\n";
                        }
                        if($order['business_style']){
                            // $print_str2 .= $this->append_chars('Business Style:',"left",PAPER_RECEIPT_TEXT," ")
                            //             .  $this->append_chars($order['business_style'],"right",PAPER_RECEIPT_INPUT," ")."\r\n"."\r\n";
                            $print_str2 .=  $this->append_chars("Business Style:",PAPER_TOTAL_COL_1,"  ")
                                            .  $this->append_chars($order['business_style'],"right",PAPER_RECEIPT_INPUT,"_")
                                            ."\r\n"."\r\n";
                        }else{
                            // $print_str2 .= $this->append_chars('Business Style:',"left",PAPER_RECEIPT_TEXT," ")
                            //             .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                            $print_str2 .=  $this->append_chars("Business Style:",PAPER_TOTAL_COL_1,"  ")
                                            .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")
                                            ."\r\n"."\r\n";
                        }

                        // $print_str2 .= $this->append_chars('Name:',"left",PAPER_RECEIPT_TEXT," ")
                        //                 .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                        // $print_str2 .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
                        //                 .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                        // $print_str2 .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
                        //                 .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                        // $print_str2 .= $this->append_chars('Business Style:',"left",PAPER_RECEIPT_TEXT," ")
                        //                 .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";

                        $print_str2 .= PAPER_LINE_SINGLE."\r\n";
                        //HEADER 3


                        // if($order['waiter_id'] != ""){
                        //     $print_str2 .= "FS - ".ucwords(strtolower($order['waiter_name']))."\r\n";
                        // }


                        // $orddetails = "";
                        // if($order['table_id'] != "" || $order['table_id'] != 0)
                        //     $orddetails .= $order['table_name']." ";

                        // if($order['guest'] != 0)
                        //     $orddetails .= "Guest #".$order['guest'];

                        // if($order['serve_no'] != 0)
                        //     $orddetails .= "Serve #".$order['serve_no'];

                        // if($orddetails != "")
                        //     $print_str2 .= $this->append_chars($orddetails,"left",PAPER_TOTAL_COL_2," ")."\r\n";//$this->align_center($orddetails,PAPER_WIDTH," ")."\r\n";
                        // else{
                        //  $print_str2 .= "\r\n";
                        // }

                        $log_user = $this->session->userdata('user');
                        if (!empty($payments)) {
                            // if($add_reprinted){
                                // if($order['printed'] >= 1){
                                    $print_str2 .= $this->align_center('[REPRINTED]',PAPER_WIDTH," ")."\r\n";
                                    // if($main_db){
                                        // $this->cashier_model->update_trans_sales(array('printed'=>$order['printed']+1),$order['sales_id']);
                                        // $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Reprinted Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                                    // }
                                    
                                // }
                                // else{
                                    // if($main_db){
                                        // $this->cashier_model->update_trans_sales(array('printed'=>1,'billed'=>1),$order['sales_id']);
                                     
                                      
                                        // if(!$return_print_str){
                                            // $log_id =  $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                                        // }
                                    // }
                                // }
                            // }
                            // else{
                            //     // if($main_db){
                            //         $this->cashier_model->update_trans_sales(array('printed'=>1,'billed'=>1),$order['sales_id']);
                            //         if(!$return_print_str){
                            //              $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                            //         }
                            //     // }
                            // }
                        }
                        else{
                            // if($main_db){
                                $this->cashier_model->update_trans_sales(array('billed'=>1),$order['sales_id']);
                                $log_id =  $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Billing on Sales Order #".$order['sales_id'],$order['sales_id']);
                            // }
                        }

                        // if(LOCALSYNC){
                        //     // if(isset($log_id)){
                        //          // $this->sync_model->add_logs($log_id);
                        //     // }

                        //     $this->sync_model->update_trans_sales($sales_id);

                        // }

                        
                        if($order['type'] == 'delivery' && $order['customer_id'] != ""){
                        
                        }


                        // $print_str2 .= PAPER_LINE."\r\n";          

                        // $print_str2 .= $this->append_chars("","right",PAPER_WIDTH,"=")."\r\n";

                        $pre_total = 0;
                        $post_details = array();
                        $post_details_nc = array();
                        $discs_items = array();
                        foreach ($discs as $disc) {
                            if(isset($disc['items']))
                                $discs_items[$disc['type']] = $disc['items'];
                        }

                        // echo "<pre>",print_r($details),"</pre>";die();

                        $dscTxt = array();
                        foreach ($details as $line_id => $val) {
                            foreach ($discs_items as $type => $dissss) {
                                if(in_array($line_id, $dissss)){
                                    $qty = 1;
                                    if(isset($dscTxt[$val['menu_id'].'_'.$val['price']][$type]['qty'])){
                                        $qty = $dscTxt[$val['menu_id'].'_'.$val['price']][$type]['qty'] + 1;
                                    }
                                    $dscTxt[$val['menu_id'].'_'.$val['price']][$type] = array('txt' => '#'.$type,'qty' => $qty);
                                }
                            }
                        }

                        foreach ($details as $line_id => $val) {

                            $prce = $val['price'];
                            // if(WEIGHTED_CATEGORY != 0){
                            //     if($val['menu_cat_id'] == WEIGHTED_CATEGORY){
                            //         $prce = ($val['price'] * $val['qty']) + $line_id;
                            //     }
                            // }

                            if($val['is_takeout'] == 1){
                                if (!isset($post_details_nc[$val['menu_id'].'_'.$prce])) {
                                    $dscsacs = array();
                                    if(isset($dscTxt[$val['menu_id'].'_'.$prce])){
                                        $dscsacs = $dscTxt[$val['menu_id'].'_'.$prce];
                                    }
                                    $remarksArr = array();
                                    if($val['remarks'] != '')
                                        $remarksArr = array($val['remarks']." x ".$val['qty']);

                                    if(SHOW_SKU){
                                        $iname = "[".$val['code']."]".$val['name'];
                                    }else{
                                        $iname = $val['name'];
                                    }

                                    // $post_details_nc[$val['menu_id'].'_'.$val['price']] = array(
                                    $post_details_nc[$val['menu_id'].'_'.$prce] = array(
                                        'name' => $iname,
                                        'code' => $val['code'],
                                        'price' => $val['price'],
                                        'no_tax' => $val['no_tax'],
                                        'discount' => $val['discount'],
                                        'qty' => $val['qty'],
                                        'discounted'=>$dscsacs,
                                        'remarks'=>$remarksArr,
                                        'modifiers' => array()
                                    );
                                } else {
                                    if($val['remarks'] != "")
                                        $post_details_nc[$val['menu_id'].'_'.$prce]['remarks'][]= $val['remarks']." x ".$val['qty'];
                                    $post_details_nc[$val['menu_id'].'_'.$prce]['qty'] += $val['qty'];
                                }

                                if (empty($val['modifiers']))
                                    continue;

                                $modifs = $val['modifiers'];
                                $n_modifiers = $post_details_nc[$val['menu_id'].'_'.$prce]['modifiers'];
                                foreach ($modifs as $vv) {
                                    if (!isset($n_modifiers[$vv['id']])) {
                                        $n_modifiers[$vv['id']] = array(
                                            'name' => $vv['name'],
                                            'price' => $vv['price'],
                                            'qty' => $val['qty'],
                                            'discount' => $vv['discount']
                                        );
                                    } else {
                                        $n_modifiers[$vv['id']]['qty'] += $val['qty'];
                                    }
                                }
                                $post_details_nc[$val['menu_id'].'_'.$prce]['modifiers'] = $n_modifiers;
                            }else{
                                if (!isset($post_details[$val['menu_id'].'_'.$prce])) {
                                    $dscsacs = array();
                                    if(isset($dscTxt[$val['menu_id'].'_'.$prce])){
                                        $dscsacs = $dscTxt[$val['menu_id'].'_'.$prce];
                                    }
                                    $remarksArr = array();
                                    if($val['remarks'] != '')
                                        $remarksArr = array($val['remarks']." x ".$val['qty']);
                                    
                                    if(SHOW_SKU){
                                        $iname = "[".$val['code']."]".$val['name'];
                                    }else{
                                        $iname = $val['name'];
                                    }

                                    $post_details[$val['menu_id'].'_'.$prce] = array(
                                        'name' => $iname,
                                        'code' => $val['code'],
                                        'price' => $val['price'],
                                        'no_tax' => $val['no_tax'],
                                        'discount' => $val['discount'],
                                        'qty' => $val['qty'],
                                        'discounted'=>$dscsacs,
                                        'remarks'=>$remarksArr,
                                        'modifiers' => array()
                                    );
                                } else {
                                    if($val['remarks'] != "")
                                        $post_details[$val['menu_id'].'_'.$prce]['remarks'][]= $val['remarks']." x ".$val['qty'];
                                    $post_details[$val['menu_id'].'_'.$prce]['qty'] += $val['qty'];
                                }

                                if (empty($val['modifiers']))
                                    continue;

                                $modifs = $val['modifiers'];
                                $n_modifiers = $post_details[$val['menu_id'].'_'.$prce]['modifiers'];
                                foreach ($modifs as $vv) {
                                    if (!isset($n_modifiers[$vv['id']])) {
                                        $n_modifiers[$vv['id']] = array(
                                            'name' => $vv['name'],
                                            'price' => $vv['price'],
                                            'qty' => $vv['qty'],
                                            'discount' => $vv['discount'],
                                            'mod_line_id' => $vv['mod_line_id'],
                                            'subcategories' => array()
                                        );

                                        if(isset($vv['submodifiers']) && count($vv['submodifiers']) > 0){
                                            $submods = array();
                                            foreach($vv['submodifiers'] as $subid => $smods){
                                                if($vv['id'] == $smods['mod_id']){

                                                    // $submods = $n_modifiers[$vv['id']]['submodifiers'];

                                                    // if(isset($submods['sales_submod_id'])){}

                                                    $submods[$smods['sales_submod_id']] = array(
                                                        'name'=>$smods['name'],
                                                        'qty'=>$smods['qty'],
                                                        'price'=>$smods['price'],
                                                        'mod_line_id'=>$smods['mod_line_id'],
                                                    );
                                                    
                                                }
                                            }
                                            $n_modifiers[$vv['id']]['submodifiers'] = $submods;
                                        }


                                    } else {
                                        $n_modifiers[$vv['id']]['qty'] += $vv['qty'];

                                        if(isset($n_modifiers[$vv['id']]['submodifiers']) && count($n_modifiers[$vv['id']]['submodifiers']) > 0){
                                            $submods = $n_modifiers[$vv['id']]['submodifiers'];
                                            foreach($vv['submodifiers'] as $subid => $smods){
                                                

                                                if($vv['id'] == $smods['mod_id']){

                                                    if(!isset($submods[$smods['sales_submod_id']])){
                                                        $submods[$smods['sales_submod_id']] = array(
                                                            'name'=>$smods['name'],
                                                            'qty'=>$smods['qty'],
                                                            'price'=>$smods['price'],
                                                            'mod_line_id'=>$smods['mod_line_id'],
                                                        );
                                                        $n_modifiers[$vv['id']]['submodifiers'] = $submods;
                                                    }

                                                    
                                                }
                                            }
                                        }

                                    }
                                }
                                $post_details[$val['menu_id'].'_'.$prce]['modifiers'] = $n_modifiers;
                            }

                        }
                        /* END NEW BLOCK */
                        $tot_qty = 0;
                          $print_str2 .= $this->append_chars("QTY" ,"right",PAPER_DET_COL_1," ");
                           $print_str2 .= $this->append_chars("  Item Description","right",PAPER_DET_COL_2," ").
                            $this->append_chars("Amount","left",PAPER_DET_COL_3," ")."\r\n\r\n";
                        foreach ($post_details as $val) {
                            $tot_qty += $val['qty'];
                            $print_str2 .= $this->append_chars($val['qty'],"right",4," ");

                            $len = strlen($val['name']);

                            if($val['qty'] == 1){
                                $lgth = 19;
                            }else{
                                $lgth = 13;
                            }

                            if($len > $lgth){
                                $arr2 = str_split($val['name'], $lgth);
                                $counter = 1;
                                $sec_line = "";
                                foreach($arr2 as $k => $vv){
                                    if($counter == 1){
                                        if ($val['qty'] == 1) {
                                            $print_str2 .= $this->append_chars(substrwords($vv,100,""),"right",24," ").
                                                $this->append_chars(number_format($val['price']/$splt,2),"left",10," ")."\r\n";
                                        } else {
                                            $print_str2 .= $this->append_chars(substrwords($vv,100,"")."@".$val['price'],"right",24," ").
                                                $this->append_chars(' '.number_format(($val['price'] * $val['qty'])/$splt,2),"left",10," ")."\r\n";
                                        }
                                    }else{

                                        $sec_line .= $vv;
                                        // if ($val['qty'] == 1) {
                                            // $print_str2 .= $this->append_chars(" ","right",PAPER_DET_COL_1,"  ");
                                            // $print_str2 .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").
                                            //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                                        // } else {
                                            // $print_str2 .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
                                            //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                                        // }
                                    }
                                    $counter++;
                                }

                                $len2 = strlen($sec_line);

                                if($len2 > 19){
                                    $arr2nd = str_split($sec_line, 21);
                                    $counter = 1;

                                    foreach($arr2nd as $k => $vv){
                                        $print_str2 .= $this->append_chars(" ","right",4,"  ");
                                        $print_str2 .= $this->append_chars(substrwords($vv,100,""),"right",24," ").
                                        $this->append_chars("","left",10," ")."\r\n";
                                    }

                                }else{
                                    $print_str2 .= $this->append_chars(" ","right",4,"  ");
                                    $print_str2 .= $this->append_chars(substrwords($sec_line,100,""),"right",24," ").
                                     $this->append_chars("","left",10," ")."\r\n";
                                }
                                
                                if ($val['qty'] == 1) {
                                    $pre_total += $val['price'];
                                }else{
                                    $pre_total += $val['price'] * $val['qty'];
                                }
                            }else{
                                if ($val['qty'] == 1) {
                                    $print_str2 .= $this->append_chars(substrwords($val['name'],100,""),"right",24," ").
                                        $this->append_chars(number_format($val['price']/$splt,2),"left",10," ")."\r\n";
                                    $pre_total += $val['price'];
                                } else {
                                    $print_str2 .= $this->append_chars(substrwords($val['name'],100,"")."@".$val['price'],"right",24," ").
                                        $this->append_chars(number_format(($val['price'] * $val['qty'])/$splt,2),"left",10," ")."\r\n";
                                    $pre_total += $val['price'] * $val['qty'];
                                }
                            }
                            if(count($val['discounted']) > 0){
                                foreach ($val['discounted'] as $dssstxt) {
                                  $print_str2 .= "      ";
                                  $print_str2 .= $this->append_chars($dssstxt['txt']." x ".number_format($dssstxt['qty']/$splt,2),"right",PAPER_DET_COL_2," ")."\r\n";
                                }
                            }
                            if(isset($val['remarks']) && count($val['remarks']) > 0){
                                // if(KERMIT){
                                //     foreach ($val['remarks'] as $rmrktxt) {
                                //         $print_str2 .= "     * ";
                                //         $print_str2 .= $this->append_chars(ucwords($rmrktxt),"right",PAPER_DET_COL_2," ")."\r\n";
                                //     }
                                // }
                            }

                            if (empty($val['modifiers']))
                                continue;

                            $modifs = $val['modifiers'];
                            foreach ($modifs as $vv) {
                                $print_str2 .= $this->append_chars("*".$vv['qty'],"right",4," ");
                                // $print_str2 .= "*".$vv['qty'];
                                // $print_str2 .= "   * 1 ";

                                if ($vv['qty'] == 1) {
                                    $print_str2 .= $this->append_chars(substrwords($vv['name'],100,""),"right",24," ")
                                        .$this->append_chars(number_format($vv['price']/$splt,2),"left",10," ")."\r\n";
                                    $pre_total += $vv['price'];
                                } else {
                                    $print_str2 .= $this->append_chars(substrwords($vv['name'],100,"")." @ ".$vv['price'],"right",24," ")
                                        .$this->append_chars(number_format(($vv['price'] * $vv['qty'])/$splt,2),"left",10," ")."\r\n";
                                    $pre_total += $vv['price'] * $vv['qty'];
                                }

                                if(isset($vv['submodifiers']) > 0){

                                    foreach($vv['submodifiers'] as $subid => $smod){
                                        // $print_str2 .= "   ";
                                        $print_str2 .= $this->append_chars("","right",4," ");
                                        if($smod['qty'] == 1){
                                            $print_str2 .= $this->append_chars(substrwords($smod['name'],100,""),"right",24," ")
                                            .$this->append_chars(number_format(($smod['price'] * $smod['qty'])/$splt,2),"left",10," ")."\r\n";
                                        }else{
                                            $print_str2 .= $this->append_chars($smod['qty'] .' '.substrwords($smod['name'],100,""),"right",24," ")
                                            .$this->append_chars(number_format(($smod['price'] * $smod['qty'])/$splt,2),"left",10," ")."\r\n";
                                        }
                                        $pre_total += $smod['qty']*$smod['price'];
                                    }
                                }
                            }
                            

                            $print_str2 .= "\r\n";
                            //DISCOUNT PALATANDAAN
                            // if(in_array($val[''], haystack))

                        }

                        //for no charges
                        if($post_details_nc){
                            // $print_str2 .= "\r\n";
                            $print_str2 .= PAPER_LINE."\r\n";
                            $print_str2 .= "          TAKE OUT"."\r\n";
                            $print_str2 .= PAPER_LINE."\r\n";
                            foreach ($post_details_nc as $val) {
                                $tot_qty += $val['qty'];
                                $print_str2 .= $this->append_chars($val['qty'],"right",4," ");

                                $len = strlen($val['name']);

                                if($val['qty'] == 1){
                                    $lgth = 21;
                                }else{
                                    $lgth = 13;
                                }

                                if($len > $lgth){
                                    $arr2 = str_split($val['name'], $lgth);
                                    $counter = 1;
                                    $sec_line = "";
                                    foreach($arr2 as $k => $vv){
                                        if($counter == 1){
                                            if ($val['qty'] == 1) {
                                                $print_str2 .= $this->append_chars(substrwords($vv,100,""),"right",24," ").
                                                    $this->append_chars(number_format($val['price']/$splt,2),"left",10," ")."\r\n";
                                            } else {
                                                $print_str2 .= $this->append_chars(substrwords($vv,100,"")." @".$val['price'],"right",24," ").
                                                    $this->append_chars(' '.number_format(($val['price'] * $val['qty'])/$splt,2),"left",10," ")."\r\n";
                                            }
                                        }else{

                                            $sec_line .= $vv;
                                            // if ($val['qty'] == 1) {
                                                // $print_str2 .= $this->append_chars(" ","right",PAPER_DET_COL_1,"  ");
                                                // $print_str2 .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").
                                                //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                                            // } else {
                                                // $print_str2 .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
                                                //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                                            // }
                                        }
                                        $counter++;
                                    }

                                    $len2 = strlen($sec_line);

                                    if($len2 > 21){
                                        $arr2nd = str_split($sec_line, 21);
                                        $counter = 1;

                                        foreach($arr2nd as $k => $vv){
                                            $print_str2 .= $this->append_chars(" ","right",4,"  ");
                                            $print_str2 .= $this->append_chars(substrwords($vv,100,""),"right",24," ").
                                            $this->append_chars("","left",10," ")."\r\n";
                                        }

                                    }else{
                                        $print_str2 .= $this->append_chars(" ","right",4," ");
                                        $print_str2 .= $this->append_chars(substrwords($sec_line,100,""),"right",24," ").
                                         $this->append_chars("","left",10," ")."\r\n";
                                    }
                                    
                                    if ($val['qty'] == 1) {
                                        $pre_total += $val['price'];
                                    }else{
                                        $pre_total += $val['price'] * $val['qty'];
                                    }
                                }else{
                                    if ($val['qty'] == 1) {
                                        $print_str2 .= $this->append_chars(substrwords($val['name'],100,""),"right",24," ").
                                            $this->append_chars(number_format($val['price']/$splt,2),"left",10," ")."\r\n";
                                        $pre_total += $val['price'];
                                    } else {
                                        $print_str2 .= $this->append_chars(substrwords($val['name'],100,"")." ".$val['price'],"right",24," ").
                                            $this->append_chars(number_format(($val['price'] * $val['qty'])/$splt,2),"left",10," ")."\r\n";
                                        $pre_total += $val['price'] * $val['qty'];
                                    }
                                }
                                if(count($val['discounted']) > 0){
                                    foreach ($val['discounted'] as $dssstxt) {
                                      $print_str2 .= "      ";
                                      $print_str2 .= $this->append_chars($dssstxt['txt']." x ".number_format($dssstxt['qty']/$splt,2),"right",PAPER_DET_COL_2," ")."\r\n";
                                    }
                                }
                                if(isset($val['remarks']) && count($val['remarks']) > 0){
                                    // foreach ($val['remarks'] as $rmrktxt) {
                                    //     $print_str2 .= "     * ";
                                    //     $print_str2 .= $this->append_chars(ucwords($rmrktxt),"right",PAPER_DET_COL_2," ")."\r\n";
                                    // }
                                }

                                if (empty($val['modifiers']))
                                    continue;

                                $modifs = $val['modifiers'];
                                foreach ($modifs as $vv) {
                                    // $print_str2 .= "   * ".$vv['qty']." ";
                                    $print_str2 .= $this->append_chars("*".$vv['qty'],"right",4," ");

                                    if ($vv['qty'] == 1) {
                                        $print_str2 .= $this->append_chars(substrwords($vv['name'],100,""),"right",24," ")
                                            .$this->append_chars(number_format($vv['price']/$splt,2),"left",10," ")."\r\n";
                                        $pre_total += $vv['price'];
                                    } else {
                                        $print_str2 .= $this->append_chars(substrwords($vv['name'],100,"")." ".$vv['price'],"right",24," ")
                                            .$this->append_chars(number_format(($vv['price'] * $vv['qty'])/$splt,2),"left",10," ")."\r\n";
                                        $pre_total += $vv['price'] * $vv['qty'];
                                    }
                                }
                                


                                //DISCOUNT PALATANDAAN
                                // if(in_array($val[''], haystack))

                            }
                        }

                        // $print_str2 .= $this->append_chars("","right",PAPER_WIDTH,"=");

                        // $vat = round($order['amount'] / (1 + BASE_TAX) * BASE_TAX,1);
                        $vat = 0;
                        if($tax > 0){
                            foreach ($tax as $tx) {
                               $vat += $tx['amount'];
                            }
                        }
                        $no_tax_amt = 0;
                        foreach ($no_tax as $k=>$v) {
                            $no_tax_amt += $v['amount'];
                        }

                        $zname = "";
                        $zcard = "";
                        $zero_rated_amt = 0;
                        foreach ($zero_rated as $k=>$v) {
                            $zero_rated_amt += $v['amount'];
                            $zname = $v['name'];
                            $zcard = $v['card_no'];
                        }
                        if($zero_rated_amt > 0){
                            $no_tax_amt = 0;
                        }

                        $print_str2 .= PAPER_LINE_SINGLE."\r\n";
                        // $print_str2 .= "\r\n".$this->append_chars(ucwords("TOTAL"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(($pre_total),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        // $print_str2 .= $this->append_chars(ucwords("TOTAL QTY"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format(($tot_qty),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        // // if(count($discs) > 0){
                        //     foreach ($discs as $ds) {
                        //         $print_str2 .= "\r\n".$this->append_chars(strtoupper($ds['type']),"right",28," ").$this->append_chars("P (".number_format($ds['amount'],2).")","left",10," ")."\r\n";
                        //     }
                        // }
                        // $print_str2 .= "\r\n";
                        

                        //start of not show for cancelled trans

                        if($cancelled == 0){

                            $total_discounts = 0;
                            $total_discounts_sm = 0;

                            $per_item_disc = false;
                            foreach ($discounts as $dcs_ci => $dcs) {
                                if($dcs['items'] != ""){
                                    $per_item_disc = true;
                                }
                            }
                            if($per_item_disc){
                                foreach ($discounts as $dcs_ci => $dcs) {
                                    // foreach ($dcs['persons'] as $code => $dcp) {
                                        // $print_str2 .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                                        // $print_str2 .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                                        // $print_str2 .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                                        // $print_str2 .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                                        $total_discounts += $dcs['amount'];
                                        $dcAmt = $dcs['amount'];
                                        // if(MALL_ENABLED && MALL == 'megamall'){
                                        //     if($dcs_ci == PWDDISC){
                                        //         $dcAmt = $dcAmt / 1.12;       
                                        //     }
                                        // }
                                        $total_discounts_sm += $dcAmt;
                                    // }
                                }
                                $total_discounts_non_vat = 0;
                                foreach ($discounts as $dcs_ci => $dcs) {
                                   
                                    // foreach ($dcs['persons'] as $code => $dcp) {
                                        // $print_str2 .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                                        // $print_str2 .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                                        // $print_str2 .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                                        // $print_str2 .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                                        if($dcs['no_tax'] == 1){
                                            $total_discounts_non_vat += $dcs['amount'];
                                        }
                                    // }
                                }

                            }else{
                                foreach ($discounts as $dcs_ci => $dcs) {
                                    foreach ($dcs['persons'] as $code => $dcp) {
                                        // $print_str2 .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                                        // $print_str2 .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                                        // $print_str2 .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                                        // $print_str2 .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                                        $total_discounts += $dcp['amount'];
                                        $dcAmt = $dcp['amount'];
                                        // if(MALL_ENABLED && MALL == 'megamall'){
                                        //     if($dcs_ci == PWDDISC){
                                        //         $dcAmt = $dcAmt / 1.12;       
                                        //     }
                                        // }
                                        $total_discounts_sm += $dcAmt;
                                    }
                                }
                                $total_discounts_non_vat = 0;
                                foreach ($discounts as $dcs_ci => $dcs) {
                                   
                                    foreach ($dcs['persons'] as $code => $dcp) {
                                        // $print_str2 .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                                        // $print_str2 .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                                        // $print_str2 .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                                        // $print_str2 .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                                        if($dcs['no_tax'] == 1){
                                            $total_discounts_non_vat += $dcp['amount'];
                                        }
                                    }
                                }
                            }

                            $total_charges = 0;
                            if(count($charges) > 0){
                                foreach ($charges as $charge_id => $opt) {
                                    $total_charges += $opt['total_amount'];
                                }
                            }
                            $local_tax_amt = 0;
                            if(count($local_tax) > 0){
                                foreach ($local_tax as $lt_id => $lt) {
                                    $local_tax_amt += $lt['amount'];
                                }
                            }
                            // echo num($total_charges + $local_tax_amt);

                            // echo '((('.$order['amount'].' - ('.$total_charges.' + '.$local_tax_amt.') - '.$vat.') - '.$no_tax_amt.'+'.$total_discounts_non_vat.') -'.$zero_rated_amt;


                            $vat_sales = ( ( ( $order['amount'] - ($total_charges + $local_tax_amt) ) - $vat)  - $no_tax_amt + $total_discounts_non_vat ) - $zero_rated_amt;

                            // echo '===== '.$vat_sales;
                            // $vat_sales = ( ( ( $order['amount'] ) - $vat)  - $no_tax_amt + $total_discounts) - $zero_rated_amt;
                            // echo "vat_sales= ((".$order['amount']." - ".$total_charges."))- ".$vat." )- ".$no_tax_amt." + ".$total_discounts." - ".$zero_rated_amt;
                            if($vat_sales < 0){
                                $vat_sales = 0;
                            }

                            $print_str2 .= $this->append_chars(" " ,"right",2," ");
                            $print_str2 .= $this->append_chars("Total Amount","right",24," ").
                            $this->append_chars(number_format($pre_total/$splt,2),"left",12," ")."\r\n";
                            if(count($discounts) >0){
                                $less_vat = round(($pre_total - ($order['amount'] - $total_charges + $local_tax_amt ) ) - $total_discounts,2);
                            
                                if($less_vat >0){

                                    $print_str2 .= $this->append_chars(" " ,"right",2," ");
                                    $print_str2 .= $this->append_chars(ucwords("Less VAT"),"right",24," ").$this->append_chars('('.number_format($less_vat/$splt,2).')',"left",12," ")."\r\n";
                                }

                              // if(count($dcs['persons']) > 0){
                              //      $print_str2 .= $this->append_chars(ucwords("Less VAT (12%)"),"right",28," ").$this->append_chars("(".number_format($pre_total - $no_tax_amt,2).")","left",10," ")."\r\n";
                              //  }
                            }else{
                                // $print_str2 .= $this->append_chars("Sub-Total","right",PAPER_DET_COL_2," ").
                                // $this->append_chars(number_format($pre_total,2),"left",PAPER_DET_COL_3," ")."\r\n";
                            }
                            if(count($discounts) >0){
                                $hasSMPWD = false;

                                if($per_item_disc){
                                    foreach ($discounts as $dcs_ci => $dcs) {
                                        // foreach ($dcs['persons'] as $code => $dcp) {
                                        if($dcs['disc_code'] != "DIPLOMAT" && $dcs['disc_code'] != "QTYDISC"){    
                                            $discRateTxt = " (".$dcs['disc_rate']."%)";
                                            if($dcs['fix'] == 1){
                                                $discRateTxt = " (".$dcs['disc_rate'].")";
                                            }
                                            $dcAmt = $dcs['amount'];
                                            // if(MALL_ENABLED && MALL == 'megamall'){
                                            //     if($dcs_ci == PWDDISC){
                                            //         $dcAmt = $dcAmt / 1.12; 
                                            //         $hasSMPWD = true;      
                                            //     }
                                            // }

                                            $where = array('disc_code'=>$dcs['disc_code']);
                                            $det = $this->site_model->get_details($where,'receipt_discounts');

                                            if(SALES_DISC_DESC){
                                                if($det[0]->no_tax == 0 && $det[0]->disc_rate != 0){
                                                    $discount_text = "Less SALES DISCOUNT";
                                                }else{
                                                    $discount_text = "Less ".$det[0]->disc_name." DISCOUNT";
                                                }
                                            }else{
                                                $discount_text = "Less ".$det[0]->disc_name." DISCOUNT";
                                            }

                                            $lend = strlen($discount_text);

                                            $lgthd = 21;

                                            if($lend > $lgthd){
                                                $arr2 = str_split($discount_text, $lgthd);
                                                $counter = 1;
                                                foreach($arr2 as $k => $vv){
                                                    if($counter == 1){
                                                        $print_str2 .= $this->append_chars(" " ,"right",2," ");
                                                        $print_str2 .= $this->append_chars($vv,"right",24," ").$this->append_chars('('.num($dcAmt/$splt).')',"left",12," ")."\r\n";
                                                    }else{
                                                        $print_str2 .= $this->append_chars(" " ,"right",2," ");
                                                        $print_str2 .= $this->append_chars($vv,"right",24," ").$this->append_chars('',"left",12," ")."\r\n";
                                                    }
                                                    $counter++;
                                                }
                                                
                                                
                                            }else{
                                                $print_str2 .= $this->append_chars(" " ,"right",2," ");
                                                $print_str2 .= $this->append_chars($discount_text,"right",24," ").$this->append_chars('('.num($dcAmt/$splt).')',"left",12," ")."\r\n";
                                            }

                                            //   $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");
                                            // $print_str2 .= $this->append_chars("Less ".$det[0]->disc_name." DISC","right",PAPER_DET_COL_2," ").$this->append_chars('('.num($dcAmt).")","left",PAPER_DET_COL_3," ")."\r\n";
                                            
                                        // }
                                        }
                                    }
                                }else{
                                    if(count($dcs['persons']) > 0){
                                        // $print_str2 .= "\r\n";
                                        // $print_str2 .= PAPER_LINE."\r\n";
                                        // $print_str2 .= "          Discount Details"."\r\n";
                                        // $print_str2 .= PAPER_LINE."\r\n";

                                        foreach ($discounts as $dcs_ci => $dcs) {
                                            if($dcs_ci != "DIPLOMAT" && $dcs_ci != "QTYDISC"){
                                                foreach ($dcs['persons'] as $code => $dcp) {
                                                    $discRateTxt = " (".$dcp['disc_rate']."%)";
                                                    if($dcs['fix'] == 1){
                                                        $discRateTxt = " (".$dcp['disc_rate'].")";
                                                    }
                                                    $dcAmt = $dcp['amount'];
                                                    // if(MALL_ENABLED && MALL == 'megamall'){
                                                    //     if($dcs_ci == PWDDISC){
                                                    //         $dcAmt = $dcAmt / 1.12; 
                                                    //         $hasSMPWD = true;      
                                                    //     }
                                                    // }

                                                    $where = array('disc_code'=>$dcs_ci);
                                                    $det = $this->site_model->get_details($where,'receipt_discounts');

                                                    if(SALES_DISC_DESC){

                                                        if($det[0]->no_tax == 0 && $det[0]->disc_rate != 0){
                                                            $discount_text = "Less SALES DISCOUNT";
                                                        }else{
                                                            // $discount_text = "Less ".$det[0]->disc_name." DISCOUNT";
                                                            $discRateTxtPercent = "";
                                                            if($det[0]->disc_rate == 0 && $det[0]->fix == 0 && $det[0]->no_tax == 0){
                                                                //open pecentage
                                                                $discRateTxtPercent = " (".$dcp['disc_rate']."%)";
                                                            }

                                                            $discount_text = "Less ".$det[0]->disc_name.$discRateTxtPercent." DISCOUNT";
                                                        }
                                                    }else{
                                                        $discRateTxtPercent = "";
                                                        if($det[0]->disc_rate == 0 && $det[0]->fix == 0 && $det[0]->no_tax == 0){
                                                            //open pecentage
                                                            $discRateTxtPercent = " (".$dcp['disc_rate']."%)";
                                                        }

                                                        $discount_text = "Less ".$det[0]->disc_name.$discRateTxtPercent." DISCOUNT";
                                                    }

                                                    
                                                    $lend = strlen($discount_text);

                                                    $lgthd = 21;
                                                    
                                                    if($lend > $lgthd){
                                                        $arr2 = str_split($discount_text, $lgthd);
                                                        $counter = 1;
                                                        foreach($arr2 as $k => $vv){
                                                            if($counter == 1){
                                                                $print_str2 .= $this->append_chars(" " ,"right",2," ");
                                                                $print_str2 .= $this->append_chars($vv,"right",24," ").$this->append_chars('('.num($dcAmt/$splt).')',"left",12," ")."\r\n";
                                                            }else{
                                                                $print_str2 .= $this->append_chars(" " ,"right",2," ");
                                                                $print_str2 .= $this->append_chars($vv,"right",24," ").$this->append_chars('',"left",12," ")."\r\n";
                                                            }
                                                            $counter++;
                                                        }
                                                        
                                                        
                                                    }else{
                                                        $print_str2 .= $this->append_chars(" " ,"right",2," ");
                                                        $print_str2 .= $this->append_chars($discount_text,"right",24," ").$this->append_chars('('.num($dcAmt/$splt).')',"left",12," ")."\r\n";
                                                    }
                                                    
                                                }
                                            }
                                        }
                                        // $print_str2 .= "\r\n";
                                        // echo $pre_total." - ".$order['amount']." - ".$total_charges." - ".$total_discounts; die();
                                        // $less_vat = ($pre_total - ($order['amount'] - num($total_charges + $local_tax_amt) ) ) - $total_discounts;

                                        // $print_str2 .= $this->append_chars(ucwords("Total Discount"),"right",28," ").$this->append_chars(number_format($total_discounts,2),"left",10," ")."\r\n";
                                        // $print_str2 .= $this->append_chars(ucwords("Total Less VAT"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                        // if(MALL_ENABLED && MALL == 'megamall' && $hasSMPWD){
                                        //     $print_str2 .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts_sm + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                        // }
                                        // else{
                                        //     $print_str2 .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                        // }
                                    }
                                }

                            } 
                            // $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");
                            // $print_str2 .= $this->append_chars("Service Charge (8.5%)","right",PAPER_DET_COL_2," ").
                            // $this->append_chars(number_format($total_charges,2),"left",PAPER_DET_COL_3," ")."\r\n";
                            if(count($charges) > 0){
                                // $print_str2 .= "\r\n";
                                // $print_str2 .= PAPER_LINE."\r\n";
                                // $print_str2 .= "              CHARGES"."\r\n";
                                // $print_str2 .= PAPER_LINE."\r\n";
                                foreach ($charges as $charge_id => $opt) {
                                    $charge_amount = $opt['total_amount'];
                                    // if($opt['absolute'] == 0){
                                    //     $charge_amount = ($opt['amount'] / 100) * ($order['amount'] - $vat);
                                    // }
                                    $print_str2 .= $this->append_chars(" " ,"right",2," ");     
                                    if($opt['absolute'] == 1){
                                        $print_str2 .= $this->append_chars($opt['name'],"right",24," ").$this->append_chars(number_format($charge_amount/$splt,2),"left",12," ")."\r\n";
                                    }else{
                                        $print_str2 .= $this->append_chars($opt['name'] ."(".$opt['rate']."%)","right",24," ").$this->append_chars(number_format($charge_amount/$splt,2),"left",12," ")."\r\n";
                                    }
                            // $print_str2 .= $this->append_chars("Service Charge (8.5%)","right",PAPER_DET_COL_2," ").
                                }
                                // $print_str2 .= PAPER_LINE."\r\n";
                            }
                            if(PRINT_VERSION == 'V2'){    
                                $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");     
                                $print_str2 .="<bold>";
                                $print_str2 .= $this->append_chars(number_format($tot_qty/$splt,2)." Item(s) Total Due","right",PAPER_DET_COL_2," ")."</bold><bold>".
                                $this->append_chars(number_format($order['amount']/$splt,2),"left",PAPER_DET_COL_3," ")."</bold>"."\r\n";
                            }else{

                                $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");
                                $print_str2 .= $this->append_chars(number_format($tot_qty/$splt)." Item(s)","right",PAPER_DET_COL_2," ")."\r\n";
                                

                                if(PRINT_VERSION == 'V3'){

                                    // $print_str2 .= '<div style="height:25px;border:solid yellow;display:inline-block;">

                                    // <span style="font-size:15px">
                                    if($includeExcel){
                                        $print_str2 .= '<div style="text-align:left;widht:50%;border:solid white;display:inline;float:left;margin-left:10px;"><span style="font-size:17px"><b>Total Due</b></span></div>
                                            <div style="text-align:right;widht:100%;border:solid white;float:right;margin-top:-20px;margin-right:10px;"><span style="font-size:17px"><b>'.number_format($order['amount']/$splt,2).'</b></span></div>';
                                    $print_str2 .= "\r\n\r\n"; 
                                    }else{
                                     $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," "); 
                                     $print_str2 .= $this->append_chars(number_format($tot_qty/$splt)." Item(s) Total Due","right",PAPER_DET_COL_2," ").
                                    $this->append_chars(number_format($order['amount']/$splt,2),"left",PAPER_DET_COL_3," ")."\r\n";   
                                    }
                                    // </div><br>';

                                }else{
                                    $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," "); 
                                    $print_str2 .= $this->append_chars(number_format($tot_qty/$splt)." Item(s) Total Due","right",PAPER_DET_COL_2," ").
                                    $this->append_chars(number_format($order['amount']/$splt,2),"left",PAPER_DET_COL_3," ")."\r\n";
                                }

                            }
                            $py_ref = "";
                            if (!empty($payments)) {

                                // $print_str2 .= "\r\n";
                                // $print_str2 .= "\r\n"."======================================"."\r\n";
                                //   if(PRINT_VERSION=="V2"){
                                //         $print_str2 .= "STARTCUT==============================ENDCUT";
                                //     }
                                // $print_str2 .= $this->append_chars("Amount due","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                //   if(PRINT_VERSION=="V2"){
                                //       $print_str2 .= "STARTCUT==============================ENDCUT";
                                //    }
                                $pay_total = 0;
                                $gft_ctr = 0;
                                $nor_ctr = 0;
                                $py_ref = "";
                                $total_payment = 0;
                                $has_cash_payment = false;
                                foreach ($payments as $payment_id => $opt) {
                                    $print_str2 .= $this->append_chars(" " ,"right",2," "); 
                                    if($opt['payment_type'] == 'gc'){
                                        $print_str2 .= $this->append_chars(ucwords('gift cheque'),"right",24," ").
                                        $this->append_chars(number_format($opt['amount']/$splt,2),"left",12," ")."\r\n";
                                    }else{
                                        $print_str2 .= $this->append_chars(ucwords($opt['payment_type']),"right",24," ").
                                        $this->append_chars(number_format($opt['amount']/$splt,2),"left",12," ")."\r\n";
                                    }    
                                    // $print_str2 .= $this->append_chars(ucwords($opt['payment_type']),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($opt['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                    
                                    if($opt['payment_type'] == 'check'){
                                        $print_str2 .= $this->append_chars("     Check # ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                                    }else{
                                        if (!empty($opt['reference']) && $opt['payment_type'] != 'deposit') {
                                            $py_ref = $opt['reference'];
                                            // $print_str2 .= $this->append_chars("     Reference ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                                        }
                                    }

                                    if($opt['payment_type'] == 'foodpanda'){
                                        if (!empty($opt['approval_code']))
                                                $print_str2 .= $this->append_chars("  Order Code: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                                    }else if($opt['payment_type'] == 'check'){
                                        $print_str2 .= $this->append_chars("     Bank: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                                    }else if($opt['payment_type'] == 'picc'){
                                        $print_str2 .= $this->append_chars("     Name: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                                    }else if($opt['payment_type'] == 'egift'){
                                        $print_str2 .= $this->append_chars("  Reference Amount : ".$opt['card_type'],"right",PAPER_WIDTH," ")."\r\n";
                                    }else{
                                        if (!empty($opt['card_number'])) {

                                            if($opt['payment_type'] == 'cash'){
                                                if (!empty($opt['card_type'])) {
                                                    $print_str2 .= $this->append_chars("  Currency Paid in: ".$opt['card_type'],"right",PAPER_WIDTH," ")."\r\n";
                                                    $print_str2 .= $this->append_chars("  Amount: ".num($opt['card_number']),"right",PAPER_WIDTH," ")."\r\n";
                                                }
                                            }else{
                                                if (!empty($opt['card_type'])) {
                                                    $print_str2 .= $this->append_chars("  Card Type: ".$opt['card_type'],"right",PAPER_WIDTH," ")."\r\n";
                                                }
                                                $print_str2 .= $this->append_chars("  Card #: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                                                if (!empty($opt['approval_code']))
                                                    $print_str2 .= $this->append_chars("  Approval #: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                                            }

                                        }
                                    }
                                    // $pay_total += $opt['amount'];
                                    // if($opt['payment_type'] == 'gc'){
                                    //     $gft_ctr++;
                                    // }
                                    // else
                                    //     $nor_ctr++;
                                    if($opt['payment_type'] == 'gc'){
                                        if($opt['amount'] > $opt['to_pay']){
                                            $total_payment  += $opt['to_pay'];
                                        }else{
                                            $total_payment  += $opt['amount'];
                                        }
                                    }
                                    else{
                                        $total_payment  += $opt['amount'];
                                    }


                                    if($opt['payment_type'] == 'cash' && $is_printed <= 0){
                                        $open_drawer = true;
                                        $has_cash_payment = true;
                                    }

                                    
                                }
                                if($pfields){
                                    foreach ($pfields as $fid => $val) {
                                        $print_str2 .= $this->append_chars("   ".$val['field_name'].": " .$val['value'],"right",PAPER_WIDTH," ")."\r\n";
                                    }
                                }

                                 if(PRINT_VERSION=="V2"){
                                        $print_str2 .= "STARTCUT==============================ENDCUT";
                                    }

                                 $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ");     
                                // if($gft_ctr == 1 && $nor_ctr == 0)
                                //     $print_str2 .= $this->append_chars('Change',"right",PAPER_DET_COL_2," ").$this->append_chars(number_format(0,2),"left",PAPER_DET_COL_3," ")."\r\n";
                                // else
                                    $print_str2 .= $this->append_chars('Change',"right",PAPER_DET_COL_2," ").$this->append_chars(number_format((abs($total_payment - $order['amount']))/$splt,2),"left",PAPER_DET_COL_3," ")."\r\n";
                             
                                    if(PRINT_VERSION=="V2"){
                                      $print_str2 .= "STARTCUT==============================ENDCUT";
                                   }
                                $print_str2 .= PAPER_LINE_SINGLE."\r\n";
                                 $print_str2 .= "\r\n".$this->append_chars(" " ,"right",2," ").$this->append_chars(ucwords("VATABLE SALES"),"right",24," ").$this->append_chars(num($vat_sales/$splt),"left",12," ")."\r\n";
                                $print_str2 .= $this->append_chars(" " ,"right",2," ").$this->append_chars(ucwords("VATEXEMPT"),"right",24," ").$this->append_chars(number_format(($no_tax_amt-$total_discounts_non_vat)/$splt,2),"left",12," ")."\r\n";
                                $print_str2 .= $this->append_chars(" " ,"right",2," ").$this->append_chars(ucwords("Zero Rated"),"right",24," ").$this->append_chars(number_format($zero_rated_amt/$splt,2),"left",12," ")."\r\n";
                                
                                   if($tax > 0){
                                        foreach ($tax as $tx) {
                                           $print_str2 .=  $this->append_chars(" " ,"right",2," ").$this->append_chars($tx['name']." Amount","right",24," ").$this->append_chars(number_format(abs($tx['amount']/$splt),2),"left",12," ")."\r\n";
                                        }
                                    }
                                $print_str2 .= "\r\n";
                                

                                $total_discount_snr_count = $total_discount_pwd_count = $total_discount_count = 0;
                                if($per_item_disc){
                                    foreach ($discounts as $dcs_ci => $dcs) {
                                            if($dcs['disc_code'] == 'SNDISC' || $dcs['disc_code'] == 'PWDISC'){
                                                // echo "<pre>",print_r($dcs['persons']),"</pre>";die();
                                                // foreach ($dcs['persons'] as $code => $dcp) {   
                                                    if($dcs['disc_code'] == 'SNDISC'){
                                                        $total_discount_snr_count += 1;                            
                                                    } 
                                                    if($dcs['disc_code'] == 'PWDISC'){

                                                        $total_discount_pwd_count += 1;                            
                                                    } 
                                                                                   }
                                            // }
                                    }
                                }else{
                                    foreach ($discounts as $dcs_ci => $dcs) {
                                            if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                                                // echo "<pre>",print_r($dcs['persons']),"</pre>";die();
                                                foreach ($dcs['persons'] as $code => $dcp) {   
                                                    if($dcs_ci == 'SNDISC'){
                                                        $total_discount_snr_count += 1;                            
                                                    } 
                                                    if($dcs_ci == 'PWDISC'){

                                                        $total_discount_pwd_count += 1;                            
                                                    } 
                                                                                   }
                                            }
                                    }
                                }

                               
                               //  if(SHOW_GUEST_COUNT){
                               //      $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ").$this->append_chars(ucwords("Total Guest Count"),"right",PAPER_DET_COL_2," ").$this->append_chars((($order['guest'] >0) ? $order['guest']: 1),"left",PAPER_DET_COL_3," ")."\r\n";
                               // };
                                
                                if($total_discount_snr_count > 0){

                                    $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ").$this->append_chars(ucwords("Total SC Guest Count"),"right",PAPER_DET_COL_2," ").$this->append_chars($total_discount_snr_count,"left",PAPER_DET_COL_3," ")."\r\n\r\n";
                                }

                                if($total_discount_pwd_count > 0){

                                    $print_str2 .= $this->append_chars(" " ,"right",PAPER_DET_COL_1," ").$this->append_chars(ucwords("Total PWD Guest Count:"),"right",PAPER_DET_COL_2," ").$this->append_chars($total_discount_pwd_count,"left",PAPER_DET_COL_3," ")."\r\n\r\n";
                                }

                                


                                $print_str2 .= PAPER_LINE_SINGLE."\r\n\r\n";

                                if ($include_footer) {
                                    $rec_footer = "";
                                    if($branch['rec_footer'] != ""){
                                        $wrap = str_replace ("<br>","\r\n", $branch['rec_footer'] );
                                        $exp = explode("\r\n", $wrap);
                                        foreach ($exp as $v) {
                                            $wrap2 = wordwrap($v,35,"|#|");
                                            $exp2 = explode("|#|", $wrap2);  
                                            foreach ($exp2 as $v2) {
                                                $rec_footer .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                                            }
                                        }                        
                                    }
                                    if($order['inactive'] == 0){
                                        $print_str2 .= $rec_footer;
                                    }    
                                        $print_str2 .= "\r\n";
                                        $print_str2 .= $this->align_center("POS Vendor Details",PAPER_WIDTH," ")."\r\n";
                                        $print_str2 .= "\r\n";
                                        $print_str2 .= $this->align_center("PointOne Integrated Tech., Inc.",PAPER_WIDTH," ")."\r\n";
                                        $print_str2 .= $this->align_center("1409 Prestige Tower",PAPER_WIDTH," ")."\r\n";
                                        $print_str2 .= $this->align_center("Ortigas Center, Pasig City",PAPER_WIDTH," ")."\r\n";
                                        $print_str2 .= $this->append_chars('TIN:',"right",PAPER_RD_COL_3," ")
                                                   .  $this->append_chars('008543444-000',"left",PAPER_DET_SUBCOL," ")."\r\n";
                                        if($branch['accrdn'] != ""){
                                            $print_str2 .= $this->append_chars('Accred. No.',"right",PAPER_RD_COL_3," ")
                                                   .  $this->append_chars($branch['accrdn'],"left",PAPER_DET_SUBCOL," ")."\r\n";
                                            // $print_str2 .= $this->align_center('ACCRDN:',PAPER_WIDTH," ")."\r\n".$this->align_center($branch['accrdn'],PAPER_WIDTH," ")."\r\n";
                                        }
                                        $print_str2 .= $this->append_chars('Permit No.:',"right",PAPER_RD_COL_3," ")
                                                   .  $this->append_chars($branch['permit_no'],"left",PAPER_DET_SUBCOL," ")."\r\n";
                                        // $print_str2 .= $this->append_chars('POS Version:',"right",PAPER_RD_COL_3," ")
                                        //            .  $this->append_chars('iPos ver 1.0',"left",PAPER_DET_SUBCOL," ")."\r\n";
                                        $print_str2 .= $this->append_chars('Date Issued:',"right",PAPER_RD_COL_3," ")
                                                   .  $this->append_chars('December 22, 2014',"left",PAPER_DET_SUBCOL," ")."\r\n";
                                                   // .  $this->append_chars(date2Word($order['datetime']),"right",PAPER_TOTAL_COL_2," ")."\r\n";
                                        $print_str2 .= $this->append_chars('Valid Until:',"right",PAPER_RD_COL_3," ")
                                                   .  $this->append_chars(date2Word('December 22, 2025'),"left",PAPER_DET_SUBCOL," ")."\r\n";
                                                   // .  $this->append_chars(date2Word( date('Y-m-d',strtotime($order['datetime'].' +5 year')) ),"right",PAPER_TOTAL_COL_2," ")."\r\n";

                                    if($branch['contact_no'] != ""){
                                        $print_str2 .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
                                                     .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
                                    }
                                    if($branch['email'] != ""){
                                        $print_str2 .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
                                                   .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
                                    }
                                    if($branch['website'] != ""){
                                        $print_str2 .= $this->align_center("Please visit us at",PAPER_WIDTH," ")."\r\n"
                                                     .$this->align_center($branch['website'],PAPER_WIDTH," ")."\r\n";
                                    }

                                    if($per_item_disc){
                                        foreach ($discounts as $dcs_ci => $dcs) {
                                            if($dcs['disc_code'] == 'SNDISC'){
                                                // if($dcs_ci == 'SNDISC'){
                                                $print_str2 .= "\r\n".$this->append_chars("Senior Citizen Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("Senior ID No.: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                                // }
                                                // if($dcs_ci == 'PWDISC'){
                                                //     $print_str2 .= "\r\n".$this->append_chars("PWD TIN: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                                //     $print_str2 .= "\r\n".$this->append_chars("OSCA ID No.: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                                // }
                                            }elseif($dcs['disc_code'] == 'PWDISC'){
                                                $print_str2 .= "\r\n".$this->append_chars("PWD Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("PWD ID No.: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");

                                            }
                                            // elseif($dcs['disc_code'] == 'D1018'){
                                            //     $print_str2 .= "\r\n".$this->append_chars("Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("VIP ID NO: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");

                                            // }elseif($dcs['disc_code'] == 'D1006'){
                                            //     $print_str2 .= "\r\n".$this->append_chars("Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("National Athlete ID No.: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                            // }elseif($dcs['disc_code'] == 'DIPLOMAT'){
                                            //     $print_str2 .= "\r\n".$this->append_chars("Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("TIN: ","right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("Diplomat ID No: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                            else{
                                                $print_str2 .= "\r\n".$this->append_chars("Name: ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("ID NO.: ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                            }
                                        }
                                    }else{
                                        foreach ($discounts as $dcs_ci => $dcs) {
                                            if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                                                foreach ($dcs['persons'] as $code => $dcp) {
                                                    // ."\r\n"
                                                    // ."\r\n"
                                                    if($dcs_ci == 'SNDISC'){
                                                        $print_str2 .= "\r\n".$this->append_chars("Senior Citizen Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                                        $print_str2 .= "\r\n".$this->append_chars("Senior ID No.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                                    }
                                                    if($dcs_ci == 'PWDISC'){
                                                        $print_str2 .= "\r\n".$this->append_chars("PWD Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                                        $print_str2 .= "\r\n".$this->append_chars("PWD ID No.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                                    }

                                                   

                                                }
                                            }else{
                                                if($dcs_ci != 'DIPLOMAT'){ 
                                                    $print_str2 .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                                    $print_str2 .= "\r\n".$this->append_chars("ID NO.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                                }
                                            }
                                            // elseif($dcs_ci == 'D1018'){
                                            //              $print_str2 .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                            //             $print_str2 .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                            //             $print_str2 .= "\r\n".$this->append_chars("VIP ID NO: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");

                                            // }elseif($dcs_ci == 'D1006'){
                                            //             $print_str2 .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                            //             $print_str2 .= "\r\n".$this->append_chars("National Athlete ID No.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                                     

                                            // }elseif($dcs['disc_code'] == 'DIPLOMAT'){
                                            //     $print_str2 .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("TIN: ","right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("Diplomat ID No: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                            // }else{
                                            //     $print_str2 .= "\r\n".$this->append_chars("Name: ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                            //     $print_str2 .= "\r\n".$this->append_chars("ID NO.: ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                            // }

                                        }
                                    }


                                    // if($zero_rated_amt > 0){
                                    //         $print_str2 .= "\r\n".$this->append_chars("Name: ".$zname,"right",PAPER_TOTAL_COL_1," ");
                                    //         $print_str2 .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                    //         $print_str2 .= "\r\n".$this->append_chars("TIN: ","right",PAPER_TOTAL_COL_1," ");
                                    //         $print_str2 .= "\r\n".$this->append_chars("Diplomat ID No: ".$zcard,"right",PAPER_TOTAL_COL_1," ");
                                    // }

                                    if(isset($py_ref) && !empty($py_ref)){
                                        $print_str2 .= "\r\n".$this->append_chars("Name: ","right",PAPER_TOTAL_COL_1," ");
                                        $print_str2 .= "\r\n".$this->append_chars("Address: ","right",PAPER_TOTAL_COL_1," ");
                                        $print_str2 .= "\r\n".$this->append_chars("TIN: ","right",PAPER_TOTAL_COL_1," ");
                                        $print_str2 .= "\r\n".$this->append_chars("GC Serial No: ".$py_ref,"right",PAPER_TOTAL_COL_1," ");

                                    }
                                    // $print_str2 .= PAPER_LINE."\r\n";
                                    if($order['inactive'] == 0){
                                        $print_str2 .= "\r\n";
                                        // if(PRINT_VERSION == 'V1'){
                                        //     $print_str2 .= $this->append_chars('Name:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                                        //            // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                        //     $print_str2 .= $this->append_chars('Company:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                                        //                // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                        //     $print_str2 .= $this->append_chars('Address:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                                        //                // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                        //     $print_str2 .= $this->append_chars('TIN:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                                        //     $print_str2 .= $this->append_chars('Signature:',"left",PAPER_RECEIPT_TEXT," ")
                                        //            .  $this->append_chars('____________________',"left",PAPER_RECEIPT_INPUT,"_")."\r\n";
                                        // }else{
                                        //     $print_str2 .= $this->append_chars('Name:',"left",PAPER_RECEIPT_TEXT," ")
                                        //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                        //     $print_str2 .= $this->append_chars('Company:',"left",PAPER_RECEIPT_TEXT," ")
                                        //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                        //     $print_str2 .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
                                        //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                        //     $print_str2 .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
                                        //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                        //     $print_str2 .= $this->append_chars('Signature:',"left",PAPER_RECEIPT_TEXT," ")
                                        //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                        // }


                                       
                                                   // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n";
                                        // $print_str2 .= "\r\n";
                                        // $print_str2 .= "\r\n";
                                        // $print_str2 .= PAPER_LINE."\r\n";

                                        // if($order['inactive'] == 0){
                                        //     $print_str2 .= $rec_footer;
                                        // }  
                                        // $print_str2 .= PAPER_LINE."\r\n";
                                       
                                        // $print_str2 .= $this->align_center('This Invoice/Receipt',PAPER_WIDTH," ")."\r\n";
                                        // $print_str2 .= $this->align_center('Shall be valid',PAPER_WIDTH," ")."\r\n";
                                        // $print_str2 .= $this->align_center('Five(5) Years from the date of',PAPER_WIDTH," ")."\r\n";
                                        // $print_str2 .= $this->align_center('The Permit to use.',PAPER_WIDTH," ")."\r\n";



                                        // if($branch['pos_footer'] != ""){
                                        //     // $print_str2 .= PAPER_LINE."\r\n";
                                        //     $print_str2 .= "\r\n";
                                        //     $wrap = str_replace ("<br>","\r\n", $branch['pos_footer'] );
                                        //     $exp = explode("\r\n", $wrap);
                                        //     foreach ($exp as $v) {
                                        //         $wrap2 = wordwrap($v,35,"|#|");
                                        //         $exp2 = explode("|#|", $wrap2);  
                                        //         foreach ($exp2 as $v2) {
                                        //             $print_str2 .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                                        //         }
                                        //     }
                                        // }
                                    }

                                }

                              
                                if (!empty($order['ref']) && $order['inactive'] == 0) {
                                    // $print_str2 .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                                    $print_str2 .= "\r\n\r\n".$this->append_chars("_________________________________","right",PAPER_TOTAL_COL_1," ");
                                    $print_str2 .= "\r\n".$this->append_chars("Customer Signature Over Printed Name","right",PAPER_TOTAL_COL_1," ");
                                    // $print_str2 .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                                    $print_str2 .= "\r\n";
                                }
                               // $print_str2 .="\r\n".$this->align_center("THIS INVOICE/RECEIPT WILL BE VALID FOR FIVE(5) YEARS FROM THE DATE OF THE PERMIT TO USE",PAPER_WIDTH," ")."\r\n";
                                    // $print_str2 .= "\r\n".$this->append_chars("THIS INVOICE/RECEIPT WILL BE VALID FOR","right",PAPER_TOTAL_COL_1," ");
                                    // $print_str2 .= "\r\n".$this->append_chars("FIVE(5) YEARS FROM THE DATE OF THE PERMIT","right",PAPER_TOTAL_COL_1," ");
                                    //  $print_str2 .= "\r\n".$this->append_chars("TO USE","right",PAPER_TOTAL_COL_1," ");
                            
                            } 

                             
                            
                            #CONDITION TO NA PINADAGDAG NG TAGAYTAY - FOR SENIOR CITEZEN VIEW VAT PLUS DISCOUNT
                            // if(count($discounts) >0){
                            //     if(count($dcs['persons']) > 0){
                            //         $print_str2 .= $this->append_chars(ucwords("Less VAT (12%)"),"right",28," ").$this->append_chars("(".number_format($pre_total - $no_tax_amt,2).")","left",10," ")."\r\n";
                            //     }
                            // }
                            // else{
                            //     if($tax > 0){
                            //         foreach ($tax as $tx) {
                            //            $print_str2 .= $this->append_chars($tx['name']."(".$tx['rate']."%)","right",28," ").$this->append_chars(number_format($tx['amount'],2),"left",10," ")."\r\n";
                            //         }
                            //     }
                            // }


                            #CONDITION TO NA PARA SA TAGUEGARAO
                            // if($tax > 0){
                            //     foreach ($tax as $tx) {
                            //        $print_str2 .= $this->append_chars($tx['name']."(".$tx['rate']."%)","right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format(abs($tx['amount']),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            //     }
                            // }
                            // if(count($local_tax) > 0){
                            //     $local_tax_amt = 0;
                            //     foreach ($local_tax as $lt_id => $lt) {
                            //         $local_tax_amt += $lt['amount'];
                            //     }
                            //     $print_str2 .= $this->append_chars(ucwords("LOCAL TAX"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($local_tax_amt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            // }
                            // if(count($discounts) >0){
                            //     $hasSMPWD = false;
                            //     if(count($dcs['persons']) > 0){
                            //         $print_str2 .= "\r\n";
                            //         // $print_str2 .= PAPER_LINE."\r\n";
                            //         $print_str2 .= "          Discount Details"."\r\n";
                            //         // $print_str2 .= PAPER_LINE."\r\n";

                            //         foreach ($discounts as $dcs_ci => $dcs) {
                            //             foreach ($dcs['persons'] as $code => $dcp) {
                            //                 $discRateTxt = " (".$dcp['disc_rate']."%)";
                            //                 if($dcs['fix'] == 1){
                            //                     $discRateTxt = " (".$dcp['disc_rate'].")";
                            //                 }
                            //                 $dcAmt = $dcp['amount'];
                            //                 // if(MALL_ENABLED && MALL == 'megamall'){
                            //                 //     if($dcs_ci == PWDDISC){
                            //                 //         $dcAmt = $dcAmt / 1.12; 
                            //                 //         $hasSMPWD = true;      
                            //                 //     }
                            //                 // }
                            //                 if(KERMIT){
                            //                     $print_str2 .= $this->append_chars($dcs_ci.$discRateTxt,"right",PAPER_TOTAL_COL_1," ").$this->append_chars('P'.num($dcAmt),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            //                     $print_str2 .= "\r\n".$this->append_chars($dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                            //                     $print_str2 .= "\r\n".$this->append_chars($dcp['code'],"right",PAPER_TOTAL_COL_1," ")."\r\n";
                            //                 }else{
                            //                      $print_str2 .= $this->append_chars($dcs_ci.$discRateTxt,"right",PAPER_TOTAL_COL_1," ").$this->append_chars('P'.num($dcAmt),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            //                 }
                            //             }
                            //         }
                            //         // $print_str2 .= "\r\n";
                            //         // echo $pre_total." - ".$order['amount']." - ".$total_charges." - ".$total_discounts; die();
                            //         $less_vat = round(($pre_total - ($order['amount'] - $total_charges + $local_tax_amt ) ) - $total_discounts,2);
                            //         // $less_vat = ($pre_total - ($order['amount'] - num($total_charges + $local_tax_amt) ) ) - $total_discounts;

                            //         // $print_str2 .= $this->append_chars(ucwords("Total Discount"),"right",28," ").$this->append_chars(number_format($total_discounts,2),"left",10," ")."\r\n";
                            //         $print_str2 .= $this->append_chars(ucwords("Total Less VAT"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            //         if(MALL_ENABLED && MALL == 'megamall' && $hasSMPWD){
                            //             $print_str2 .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts_sm + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            //         }
                            //         else{
                            //             $print_str2 .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            //         }
                            //     }
                            // }

                            // if(count($charges) > 0){
                            //     $print_str2 .= "\r\n";
                            //     // $print_str2 .= PAPER_LINE."\r\n";
                            //     // $print_str2 .= "              CHARGES"."\r\n";
                            //     // $print_str2 .= PAPER_LINE."\r\n";
                            //     foreach ($charges as $charge_id => $opt) {
                            //         $charge_amount = $opt['total_amount'];
                            //         // if($opt['absolute'] == 0){
                            //         //     $charge_amount = ($opt['amount'] / 100) * ($order['amount'] - $vat);
                            //         // }
                            //         $print_str2 .= $this->append_chars($opt['name'],"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($charge_amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                            //     }
                            //     // $print_str2 .= PAPER_LINE."\r\n";
                            // }

                            if (!empty($payments)) {

                                // $print_str2 .= "\r\n";
                                // // $print_str2 .= "\r\n"."======================================"."\r\n";
                                //   if(PRINT_VERSION=="V2"){
                                //         $print_str2 .= "STARTCUT==============================ENDCUT";
                                //     }
                                // $print_str2 .= $this->append_chars("Amount due","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                //   if(PRINT_VERSION=="V2"){
                                //       $print_str2 .= "STARTCUT==============================ENDCUT";
                                //    }
                                // $pay_total = 0;
                                // $gft_ctr = 0;
                                // $nor_ctr = 0;
                              
                                // foreach ($payments as $payment_id => $opt) {

                                //     $print_str2 .= $this->append_chars(ucwords($opt['payment_type']),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($opt['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                    
                                //     if($opt['payment_type'] == 'check'){
                                //         $print_str2 .= $this->append_chars("     Check # ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                                //     }else{
                                //         if (!empty($opt['reference']) && $opt['payment_type'] != 'deposit') {
                                //             $print_str2 .= $this->append_chars("     Reference ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                                //         }
                                //     }

                                //     if($opt['payment_type'] == 'foodpanda'){
                                //         if (!empty($opt['approval_code']))
                                //                 $print_str2 .= $this->append_chars("  Order Code: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                                //     }else if($opt['payment_type'] == 'check'){
                                //         $print_str2 .= $this->append_chars("     Bank: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                                //     }else if($opt['payment_type'] == 'picc'){
                                //         $print_str2 .= $this->append_chars("     Name: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                                //     }else{
                                //         if (!empty($opt['card_number'])) {
                                //             if (!empty($opt['card_type'])) {
                                //                 $print_str2 .= $this->append_chars("  Card Type: ".$opt['card_type'],"right",PAPER_WIDTH," ")."\r\n";
                                //             }
                                //             $print_str2 .= $this->append_chars("  Card #: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                                //             if (!empty($opt['approval_code']))
                                //                 $print_str2 .= $this->append_chars("  Approval #: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                                //         }
                                //     }
                                //     $pay_total += $opt['amount'];
                                //     if($opt['payment_type'] == 'gc'){
                                //         $gft_ctr++;
                                //     }
                                //     else
                                //         $nor_ctr++;


                                //     if($opt['payment_type'] == 'cash' && $is_printed <= 0){
                                //         $open_drawer = true;
                                //     }

                                    
                                // }
                                // if($gft_ctr == 1 && $nor_ctr == 0)
                                //     $print_str2 .= $this->append_chars("Change","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(0,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                // else
                                //     $print_str2 .= $this->append_chars("Change","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(abs($pay_total - $order['amount']),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                // $print_str2 .= PAPER_LINE."\r\n";

                                // if ($order['ref'] != null && $order['inactive'] == 1) {
                                //     // $print_str2 .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                                //     $print_str2 .= "\r\n".$this->append_chars("_________________________________","right",PAPER_TOTAL_COL_1," ");
                                //     $print_str2 .= "\r\n".$this->append_chars("  Customer's Name & Signature","right",PAPER_TOTAL_COL_1," ");
                                //     // $print_str2 .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                                //     $print_str2 .= "\r\n";
                                // }


                                // if ($include_footer) {
                                //     $rec_footer = "";
                                //     if($branch['rec_footer'] != ""){
                                //         $wrap = str_replace ("<br>","\r\n", $branch['rec_footer'] );
                                //         $exp = explode("\r\n", $wrap);
                                //         foreach ($exp as $v) {
                                //             $wrap2 = wordwrap($v,35,"|#|");
                                //             $exp2 = explode("|#|", $wrap2);  
                                //             foreach ($exp2 as $v2) {
                                //                 $rec_footer .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                                //             }
                                //         }                        
                                //     }
                                //     // if($order['inactive'] == 0){
                                //     //     $print_str2 .= $rec_footer;
                                //     // }    
                                //         $print_str2 .= "\r\n";
                                //         $print_str2 .= $this->align_center("POS Vendor Details",PAPER_WIDTH," ")."\r\n";
                                //         // $print_str2 .= PAPER_LINE."\r\n";
                                //         $print_str2 .= $this->align_center("PointOne Integrated Tech., Inc.",PAPER_WIDTH," ")."\r\n";
                                //         $print_str2 .= $this->align_center("1409 Prestige Tower",PAPER_WIDTH," ")."\r\n";
                                //         $print_str2 .= $this->align_center("Ortigas Center, Pasig City",PAPER_WIDTH," ")."\r\n";
                                //         $print_str2 .= $this->append_chars('POS Version:',"right",PAPER_RD_COL_3," ")
                                //                    .  $this->append_chars('iPos ver 1.0',"left",PAPER_DET_SUBCOL," ")."\r\n";
                                //         $print_str2 .= $this->append_chars('TIN:',"right",PAPER_RD_COL_3," ")
                                //                    .  $this->append_chars('008543444-000',"left",PAPER_DET_SUBCOL," ")."\r\n";
                                //         $print_str2 .= $this->append_chars('Date Issued:',"right",PAPER_RD_COL_3," ")
                                //                    .  $this->append_chars('December 22, 2014',"left",PAPER_DET_SUBCOL," ")."\r\n";
                                //                    // .  $this->append_chars(date2Word($order['datetime']),"right",PAPER_TOTAL_COL_2," ")."\r\n";
                                //         $print_str2 .= $this->append_chars('Valid Until:',"right",PAPER_RD_COL_3," ")
                                //                    .  $this->append_chars(date2Word('December 22, 2025'),"left",PAPER_DET_SUBCOL," ")."\r\n";
                                //                    // .  $this->append_chars(date2Word( date('Y-m-d',strtotime($order['datetime'].' +5 year')) ),"right",PAPER_TOTAL_COL_2," ")."\r\n";
                                //         if($branch['accrdn'] != ""){
                                //             $print_str2 .= $this->append_chars('ACCRDN:',"right",PAPER_RD_COL_3," ")
                                //                    .  $this->append_chars($branch['accrdn'],"left",PAPER_DET_SUBCOL," ")."\r\n";
                                //             // $print_str2 .= $this->align_center('ACCRDN:',PAPER_WIDTH," ")."\r\n".$this->align_center($branch['accrdn'],PAPER_WIDTH," ")."\r\n";
                                //         }

                                //     if($branch['contact_no'] != ""){
                                //         $print_str2 .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
                                //                      .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
                                //     }
                                //     if($branch['email'] != ""){
                                //         $print_str2 .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
                                //                    .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
                                //     }
                                //     if($branch['website'] != ""){
                                //         $print_str2 .= $this->align_center("Please visit us at",PAPER_WIDTH," ")."\r\n"
                                //                      .$this->align_center($branch['website'],PAPER_WIDTH," ")."\r\n";
                                //     }
                                //     $print_str2 .= PAPER_LINE."\r\n";
                                //     if($order['inactive'] == 0){
                                //         $print_str2 .= "\r\n";
                                //         if(PRINT_VERSION == 'V1'){
                                //             $print_str2 .= $this->append_chars('Name:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                                //                    // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                //             $print_str2 .= $this->append_chars('Company:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                                //                        // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                //             $print_str2 .= $this->append_chars('Address:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                                //                        // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                //             $print_str2 .= $this->append_chars('TIN:',"right",PAPER_RECEIPT_TEXT," ")."\r\n";
                                //             $print_str2 .= $this->append_chars('Signature:',"left",PAPER_RECEIPT_TEXT," ")
                                //                    .  $this->append_chars('____________________',"left",PAPER_RECEIPT_INPUT,"_")."\r\n";
                                //         }else{
                                //             $print_str2 .= $this->append_chars('Name:',"left",PAPER_RECEIPT_TEXT," ")
                                //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                //             $print_str2 .= $this->append_chars('Company:',"left",PAPER_RECEIPT_TEXT," ")
                                //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                //             $print_str2 .= $this->append_chars('Address:',"left",PAPER_RECEIPT_TEXT," ")
                                //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                //             $print_str2 .= $this->append_chars('TIN:',"left",PAPER_RECEIPT_TEXT," ")
                                //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                //             $print_str2 .= $this->append_chars('Signature:',"left",PAPER_RECEIPT_TEXT," ")
                                //                        .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                                //         }


                                       
                                //                    // .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n";
                                //         // $print_str2 .= "\r\n";
                                //         $print_str2 .= "\r\n";
                                //         $print_str2 .= PAPER_LINE."\r\n";

                                //         if($order['inactive'] == 0){
                                //             $print_str2 .= $rec_footer;
                                //         }  
                                //         // $print_str2 .= PAPER_LINE."\r\n";
                                       
                                //         // $print_str2 .= $this->align_center('This Invoice/Receipt',PAPER_WIDTH," ")."\r\n";
                                //         // $print_str2 .= $this->align_center('Shall be valid',PAPER_WIDTH," ")."\r\n";
                                //         // $print_str2 .= $this->align_center('Five(5) Years from the date of',PAPER_WIDTH," ")."\r\n";
                                //         // $print_str2 .= $this->align_center('The Permit to use.',PAPER_WIDTH," ")."\r\n";



                                //         // if($branch['pos_footer'] != ""){
                                //         //     // $print_str2 .= PAPER_LINE."\r\n";
                                //         //     $print_str2 .= "\r\n";
                                //         //     $wrap = str_replace ("<br>","\r\n", $branch['pos_footer'] );
                                //         //     $exp = explode("\r\n", $wrap);
                                //         //     foreach ($exp as $v) {
                                //         //         $wrap2 = wordwrap($v,35,"|#|");
                                //         //         $exp2 = explode("|#|", $wrap2);  
                                //         //         foreach ($exp2 as $v2) {
                                //         //             $print_str2 .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                                //         //         }
                                //         //     }
                                //         // }
                                //     }

                                // }
                            } else {
                                $is_billing = true;
                                $print_str2 .= "\r\n".$this->append_chars("","right",PAPER_WIDTH,"=");
                                if(PRINT_VERSION == 'V1'){
                                    $print_str2 .= "\r\n\r\n".$this->append_chars("Billing Amount","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                }
                                if(is_array($splits)){
                                    $print_str2 .= $this->append_chars("Split Amount by ".$splits['by'],"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($splits['total']/$splt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                }

                                if($per_item_disc){
                                    //for senior deaitls with signature forbiliing
                                    foreach ($discounts as $dcs_ci => $dcs) {
                                        if($dcs['disc_code'] == 'SNDISC' || $dcs['disc_code'] == 'PWDISC'){
                                            // $print_str2 .= PAPER_LINE."\r\n";
                                            $print_str2 .= "\r\n";
                                            $print_str2 .= $this->align_center("OSCA/PWD Details",PAPER_WIDTH," ")."\r\n";
                                            // $print_str2 .= PAPER_LINE."\r\n";
                                            // $print_str2 .= $this->align_center(PAPER_LINE,42," ");
                                            break;
                                        }
                                    }
                                    foreach ($discounts as $dcs_ci => $dcs) {
                                        if($dcs['disc_code'] == 'SNDISC' || $dcs['disc_code'] == 'PWDISC'){
                                            // foreach ($dcs['persons'] as $code => $dcp) {
                                                // ."\r\n"
                                                // ."\r\n"
                                                $print_str2 .= "\r\n".$this->append_chars("ID NO      : ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("NAME       : ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                                                // $print_str2 .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                                            // }
                                        }else{
                                            if($dcs['disc_code'] != 'DIPLOMAT'){
                                                $print_str2 .= "\r\n".$this->append_chars("ID NO      : ".$dcs['code'],"right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("NAME       : ".$dcs['name'],"right",PAPER_TOTAL_COL_1," ");
                                            }
                                        }
                                    }
                                }else{
                                    //for senior deaitls with signature forbiliing
                                    foreach ($discounts as $dcs_ci => $dcs) {
                                        if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                                            // $print_str2 .= PAPER_LINE."\r\n";
                                            $print_str2 .= "\r\n";
                                            $print_str2 .= $this->align_center("OSCA/PWD Details",PAPER_WIDTH," ")."\r\n";
                                            // $print_str2 .= PAPER_LINE."\r\n";
                                            // $print_str2 .= $this->align_center(PAPER_LINE,42," ");
                                            break;
                                        }
                                    }
                                    foreach ($discounts as $dcs_ci => $dcs) {
                                        if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                                            foreach ($dcs['persons'] as $code => $dcp) {
                                                // ."\r\n"
                                                // ."\r\n"
                                                $print_str2 .= "\r\n".$this->append_chars("ID NO      : ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("NAME       : ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                                                $print_str2 .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                                                // $print_str2 .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                                            }
                                        }else{
                                            if($dcs_ci != 'DIPLOMAT'){
                                                foreach ($dcs['persons'] as $code => $dcp) {
                                                    // ."\r\n"
                                                    // ."\r\n"
                                                    $print_str2 .= "\r\n".$this->append_chars("ID NO      : ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                                    $print_str2 .= "\r\n".$this->append_chars("NAME       : ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                                    // $print_str2 .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                                                    // $print_str2 .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                                                    // $print_str2 .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                                                    // $print_str2 .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                                                }
                                            }
                                        }
                                    }
                                }


                                if ($include_footer) {
                                    // $print_str2 .= "\r\n\r\n";
                                    if($branch['contact_no'] != ""){
                                        $print_str2 .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
                                                     .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
                                    }
                                    if($branch['email'] != ""){
                                        $print_str2 .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
                                                   .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
                                    }
                                    if($branch['website'] != "")
                                        $print_str2 .= $this->align_center("Please visit us at \r\n".$branch['website'],PAPER_WIDTH," ")."\r\n";
                                }

                            }

                            if (!empty($payments)) {
                                // $print_str2 .= "\r\n";
                                // foreach ($discounts as $dcs_ci => $dcs) {
                                //     if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                                //         // $print_str2 .= PAPER_LINE."\r\n";
                                //         $print_str2 .= $this->align_center("OSCA/PWD Details",PAPER_WIDTH," ")."\r\n";
                                //         // $print_str2 .= PAPER_LINE."\r\n";
                                //         // $print_str2 .= $this->align_center(PAPER_LINE,42," ");
                                //         break;
                                //     }
                                // }
                                // foreach ($discounts as $dcs_ci => $dcs) {
                                //     if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                                //         foreach ($dcs['persons'] as $code => $dcp) {
                                //             // ."\r\n"
                                //             // ."\r\n"
                                //             $print_str2 .= "\r\n".$this->append_chars("ID NO      : ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                                //             $print_str2 .= "\r\n".$this->append_chars("NAME       : ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                                //             $print_str2 .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                                //             $print_str2 .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                                //             $print_str2 .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                                //             // $print_str2 .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                                //         }
                                //     }
                                // }
                                // foreach ($discounts as $dcs_ci => $dcs) {
                                //     if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                                //         // $print_str2 .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                                //         break;
                                //     }
                                // }

                                // if($zero_rated_amt != 0){
                                //     $print_str2 .= $this->align_center("Diplomat Details",PAPER_WIDTH," ")."\r\n";
                                //     foreach ($zero_rated as $k=>$v) {
                                //         // $zero_rated_amt += $v['amount'];
                                //         $print_str2 .= "\r\n".$this->append_chars("ID NO      : ".$v['card_no'],"right",PAPER_TOTAL_COL_1," ");
                                //         $print_str2 .= "\r\n".$this->append_chars("NAME       : ".$v['name'],"right",PAPER_TOTAL_COL_1," ");
                                //         $print_str2 .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                                //         $print_str2 .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                                //         $print_str2 .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";   
                                //     }

                                // }

                            }

                            if($approved_by != null){
                                // $app = $this->site_model->get_user_details($approved_by);
                                // $approver = $app->fname." ".$app->mname." ".$app->lname." ".$app->suffix;
                                // // $print_str2 .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                                // $print_str2 .= "\r\n".$this->append_chars("Approved By : ".$approver,"right",PAPER_TOTAL_COL_1," ");
                                // $print_str2 .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                                // $print_str2 .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                            }

                        }
                        $js_rcp2 = $this->html_print($print_str2,true, $sales_id);
                        $js_rcps[] = array('printer'=>BILLING_PRINTER,'value'=>$js_rcp2);
                    }

                    // }

                }
                // }
                // $filename = "sales.rtf";
                // $fp = fopen($filename, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);

                //rtf file                
                // require APPPATH . 'libraries/PHPRtfLite.php';

                // register PHPRtfLite class loader
                // PHPRtfLite::registerAutoloader();

                // rtf document
                // $rtf = new PHPRtfLite();

                //paragraph formats
                // $parFormat = new PHPRtfLite_ParFormat();

                // $parGreyLeft = new PHPRtfLite_ParFormat();
                // $parGreyLeft->setShading(10);

                // $parGreyCenter = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
                // $parGreyCenter->setShading(10);

                // $dir = dirname(__FILE__);

                // $sect = $rtf->addSection();

                // $sect->writeText($print_str, new PHPRtfLite_Font(), new PHPRtfLite_ParFormat());
                // $sect->writeText($print_str);
                // $rtf->save($filename, false); // false is only used for unit test

                // $batfile = "print.bat";
                // $fh1 = fopen($batfile,'w+');
                // $root = dirname(BASEPATH);
                // $battxt ="write /p \"".realpath($root."/".$filename)."\"";

                // if(BILLING_PRINTER){
                //     if(BILLING_PRINTER != "DEFAULT"){
                //         $battxt = "write /p \"".realpath($root."/".$filename)."\" \"".BILLING_PRINTER."\"  ";   
                //     }
                // }

                 //rtf file     end

                if($openDrawer && $has_cash_payment){
                    // $pet = $this->cashier_model->get_pos_settings();
                    // $open_drawer_printer = $pet->open_drawer_printer;
                    // if($open_drawer_printer != ""){
                    //     $battxt = "write /p \"".realpath($root."/".$filename)."\" \"".$open_drawer_printer."\"  ";  

                    //     fwrite($fh1, $battxt);
                    //     fclose($fh1);
                    //     session_write_close();
                    //     // exec($filename);
                    //     for ($i=0; $i < $no_prints; $i++) { 
                    //         exec($batfile);
                    //     }
                    //     session_start();
                    //     unlink($filename);
                    //     unlink($batfile); 
                    // } 
                    $filename = "open.txt";
                    $fp = fopen($filename, "w+");
                    fwrite($fp," ");
                    fclose($fp);

                    $batfile = "print.bat";
                    $fh1 = fopen($batfile,'w+');
                    $root = dirname(BASEPATH);

                    $battxt =  "NOTEPAD /P \"".realpath($root."/".$filename)."\"";

                    $pet = $this->cashier_model->get_pos_settings();
                    $open_drawer_printer = $pet->open_drawer_printer;
                    if($open_drawer_printer != ""){
                        $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".$open_drawer_printer."\"  ";   
                        fwrite($fh1,$battxt);
                        fclose($fh1);
                        session_write_close();
                        exec($batfile);
                        // exec($filename);
                        session_start();
                        unlink($filename);
                        unlink($batfile);           
                    }   


                }

                

                // echo $js_rcp;
            }else{
                // $this->do_print_os($print_str,$printer['printer'],$printer['no']);  

                $filename = "sales.txt";
                $fp = fopen($filename, "w+");
                fwrite($fp,$print_str);
                fclose($fp);

                $batfile = "print.bat";
                $fh1 = fopen($batfile,'w+');
                $root = dirname(BASEPATH);
                $battxt ="NOTEPAD /P \"".realpath($root."/".$filename)."\"";

                if(BILLING_PRINTER){
                    if(BILLING_PRINTER != "DEFAULT"){
                        $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".BILLING_PRINTER."\"  ";   
                    }
                }

                if($openDrawer && $has_cash_payment){
                    $pet = $this->cashier_model->get_pos_settings();
                    $open_drawer_printer = $pet->open_drawer_printer;
                    if($open_drawer_printer != ""){
                        $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".$open_drawer_printer."\"  ";   
                    }            
                }

                fwrite($fh1, $battxt);
                fclose($fh1);
                session_write_close();
                // exec($filename);
                for ($i=0; $i < $no_prints; $i++) { 
                    exec($batfile);
                }
                session_start();
                unlink($filename);
                unlink($batfile);
            }

            if($order_slip_prints > 0){
                if(PRINT_VERSION && PRINT_VERSION == 'V3'){
                    $print_str = $this->print_order_slip($header_print_str,$post_details,$order_slip_prints);
                    $js_rcp = $this->html_print($print_str);
                    $js_rcps[] = array('printer'=>BILLING_PRINTER,'value'=>$js_rcp);
                }else{
                    $this->print_order_slip($header_print_str,$post_details,$order_slip_prints);
                }
                            
            }


            if ($asJson)
                echo json_encode(array('msg'=>'Receipt # '.(!empty($order['ref']) ? $order['ref'] : $sales_id).' has been printed','js_rcps'=>$js_rcps,'ord_slip'=>$order_slip_prints));
            else
                return array('msg'=>'Receipt # '.(!empty($order['ref']) ? $order['ref'] : $sales_id).' has been printed','js_rcps'=>$js_rcps,'ord_slip'=>$order_slip_prints);
        }
        public function print_order_slip($header_print_str=null,$post_details=array(),$order_slip_prints=0){
            $print_str = $header_print_str;
            $print_str .=  $this->align_center('Order Slip',38," ")."\r\n";
            $tot_qty = 0;
            foreach ($post_details as $val) {
                $tot_qty += $val['qty'];
                $print_str .= $this->append_chars($val['qty'],"right",4," ");

                $len = strlen($val['name']);

                if($len > 18){
                    $arr2 = str_split($val['name'], 18);
                    $counter = 1;
                    foreach($arr2 as $k => $vv){
                        if($counter == 1){
                            if ($val['qty'] == 1) {
                                $print_str .= $this->append_chars(substrwords($vv,21,""),"right",26," ").
                                    $this->append_chars(null,"left",8," ")."\r\n";
                            } else {
                                $print_str .= $this->append_chars(substrwords($vv,21,"")." @ ".$val['price'],"right",26," ").
                                    $this->append_chars(null,"left",8," ")."\r\n";
                            }
                        }else{
                            // if ($val['qty'] == 1) {
                                $print_str .= $this->append_chars("","right",4," ");
                                $print_str .= $this->append_chars(substrwords($vv,21,""),"right",26," ").
                                    $this->append_chars(null,"left",8," ")."\r\n";
                            // } else {
                                // $print_str .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
                                //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                            // }
                        }
                        $counter++;
                    }

                }else{
                    if ($val['qty'] == 1) {
                        $print_str .= $this->append_chars(substrwords($val['name'],21,""),"right",26," ").
                            $this->append_chars(null,"left",8," ")."\r\n";
                    } else {
                        $print_str .= $this->append_chars(substrwords($val['name'],21,"")." @ ".$val['price'],"right",26," ").
                            $this->append_chars(null,"left",8," ")."\r\n";
                    }
                }

                if(count($val['discounted']) > 0){
                    foreach ($val['discounted'] as $dssstxt) {
                      $print_str .= "      ";
                      $print_str .= $this->append_chars($dssstxt['txt']." x ".$dssstxt['qty'],"right",23," ")."\r\n";
                    }
                }
                if(isset($val['remarks']) && count($val['remarks']) > 0){
                    foreach ($val['remarks'] as $rmrktxt) {
                        $print_str .= "     * ";
                        $print_str .= $this->append_chars(ucwords($rmrktxt),"right",23," ")."\r\n";
                    }
                }

                if (empty($val['modifiers']))
                    continue;

                $modifs = $val['modifiers'];
                foreach ($modifs as $vv) {
                    $print_str .= "     * ";

                    if ($vv['qty'] == 1) {
                        $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                            .$this->append_chars(null,"left",8," ")."\r\n";
                    } else {
                        $print_str .= $this->append_chars(substrwords($vv['name'],18,"")." @ ".$vv['price'],"right",23," ")
                            .$this->append_chars(null,"left",8," ")."\r\n";
                    }
                }
            }
            $print_str .= "\r\n"."--------------------------------------"."\r\n";
            $print_str .= $this->append_chars(ucwords("TOTAL QTY"),"right",28," ").$this->append_chars(number_format(($tot_qty),2),"left",10," ")."\r\n";

            $filename = "order.txt";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);

            $batfile = "print.bat";
            $fh1 = fopen($batfile,'w+');
            $root = dirname(BASEPATH);

            fwrite($fh1, "NOTEPAD /P \"".realpath($root."/".$filename)."\"");
            fclose($fh1);
            session_write_close();
            for ($i=0; $i < $order_slip_prints; $i++) { 
                exec($batfile);
            }
            session_start();
            unlink($filename);
            unlink($batfile);
        }
        #############################
        #                           #
        # New Order Slip Printing   #
        #                           #
        #############################
        public function set_printers(){
            $pet = $this->cashier_model->get_pos_settings();
            
            $kitchen_printer = $pet->kitchen_printer_name;
            if(KITCHEN_PRINTER){
                $kitchen_printer = KITCHEN_PRINTER;
            }
            $kitchen_printer_no = $pet->kitchen_printer_name_no;
            if(KITCHEN_PRINTER_NUM){
                $kitchen_printer_no = KITCHEN_PRINTER_NUM;
            }    
            
            $kitchen_printer_beverage = $pet->kitchen_beverage_printer_name;
            if(BEVERAGE_PRINTER){
                $kitchen_printer_beverage = BEVERAGE_PRINTER;
            }
            $kitchen_printer_beverage_no = $pet->kitchen_beverage_printer_name_no;

            if(BEVERAGE_PRINTER_NUM){
                $kitchen_printer_beverage_no = BEVERAGE_PRINTER_NUM;
            }

            $printers = array(
                KITCHEN_PRINTER_NAME=>array('printer'=>$kitchen_printer,'no'=>$kitchen_printer_no,'sub_cat_id'=>1),
                BEVERAGE_PRINTER_NAME=>array('printer'=>$kitchen_printer_beverage,'no'=>$kitchen_printer_beverage_no,'sub_cat_id'=>BEVERAGE_ID),
                PRINT1_PRINTER_NAME=>array('printer'=>PRINT1_PRINTER,'no'=>PRINT1_PRINTER_NUM,'sub_cat_id'=>PRINT1_ID),
            );
            return $printers;
        }
        public function print_os($sales_id=null,$not_printed_only=true,$reprinted=false,$use_main=false){
            $printers = $this->set_printers();
            if($use_main){
                $this->db = $this->load->database('main',true);
            }
            $return = $this->get_order(false,$sales_id);
            $order = $return['order'];
            $details = $return['details'];
            $update_line_ids = array();
            $update_line_mod_ids = array();
            foreach ($printers as $name => $printer) {
                $has_to_print = 0;
                $update_line_ids = array();
                // $print_str = $this->os_header();
                $print_str = "";
                $print_str .=  align_center(strtoupper('ORDER SLIP'),PAPER_WIDTH," ")."\r\n";
                // echo "<pre>",print_r($order),"</pre>";die();
                $print_str .= "Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
                $guest = (int)$order['guest'];
                // echo var_dump($guest);
                if($guest == 0)
                    $guest = 1;
                $print_str .= "No Guest: ".$guest."\r\n";
              
                $serve_no = $order['serve_no'];
                if($serve_no > 0)
                    $print_str .= "Serve No: ".$serve_no."\r\n";
                if($order['waiter_username'] != "")
                    $print_str .= "FS: ".$order['waiter_username']."\r\n";
                $print_str .= $order['datetime']."\r\n";
                $print_str .= "======================================"."\r\n";
                if($order['inactive'] == 1){
                    $print_str .=  align_center('*** VOIDED TRANSACTION ***',PAPER_WIDTH," ")."\r\n\r\n";
                }
                $print_str .=  align_center(strtoupper($name),PAPER_WIDTH," ")."\r\n";
                if($order['table_name'] != ""){
                    $print_str .=  align_center($order['table_name'],PAPER_WIDTH," ")."\r\n\r\n";
                }
                if($reprinted){
                    $print_str .=  align_center('[REPRINTED]',PAPER_WIDTH," ")."\r\n\r\n";
                }
                ###############################################################
                foreach ($details as $line_id => $val) {
                    if(isset($val['retail']) &&  $val['retail'] == 1){
                        continue;
                    }
                    $category = $this->site_model->get_tbl('menus',array('menu_id'=>$val['menu_id']),array(),null,true,'menu_sub_cat_id');
                    $cat = $category[0];
                    if($printer['sub_cat_id'] == $cat->menu_sub_cat_id){
                        $all_str = "";
                        $menu_str = "";
                        $menu_str .= $this->append_chars($val['qty'],"right",4," ");

                        $len = strlen($val['name']);

                        if($len > 30){
                            $arr2 = str_split($val['name'], 30);
                            $counter = 1;
                            foreach($arr2 as $k => $vv){
                                if($counter == 1){
                                   $menu_str .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").$this->append_chars(null,"left",8," ")."\r\n";
                                    
                                }else{
                                    // if ($val['qty'] == 1) {
                                        $menu_str .= $this->append_chars("","right",4," ");
                                        $menu_str .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").$this->append_chars(null,"left",8," ")."\r\n";
                                    // } else {
                                        // $print_str .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
                                        //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                                    // }
                                }
                                $counter++;
                            }

                        }else{
                            $menu_str .= $this->append_chars(substrwords($val['name'],100,""),"right",PAPER_DET_COL_2," ").$this->append_chars(null,"left",8," ")."\r\n";
                        }

                        if(isset($val['remarks']) && count($val['remarks']) != ""){
                            $menu_str .= "     * ";
                            $menu_str .= $this->append_chars(ucwords($val['remarks']),"right",PAPER_DET_COL_2," ")."\r\n";
                        }
                        
                        if($not_printed_only){
                            if($val['kitchen_slip_printed'] == 0){  
                                $all_str .= $menu_str;
                                $update_line_ids[]=$val['id'];
                                $has_to_print++;
                            }   
                        }   
                        else{
                            $all_str .= $menu_str;
                            $update_line_ids[]=$val['id'];
                            $has_to_print++;
                        }

                        if(count($val['modifiers']) > 0){
                            foreach ($val['modifiers'] as $mod_id => $mod) {
                                $mod_str = "";
                                $kitchen_mod_slip_printed = $mod['kitchen_slip_printed'];
                                if($mod['kitchen_slip_printed'] == ""){
                                    $kitchen_mod_slip_printed = 0;
                                }
                                $mod_str .= "     - ";
                                $mod_str .= $this->append_chars(substrwords($mod['name'],100,""),"right",PAPER_DET_COL_2," ")
                                    .$this->append_chars(null,"left",8," ")."\r\n";
                                if($not_printed_only){
                                    if($kitchen_mod_slip_printed == 0){ 
                                        $all_str .= $mod_str;
                                        $update_line_mod_ids[]=$mod['sales_mod_id'];
                                        $has_to_print++;
                                    }   
                                }   
                                else{
                                    $all_str .= $mod_str;
                                    $update_line_mod_ids[]=$mod['sales_mod_id'];
                                    $has_to_print++;
                                }
                            }
                            ########################     
                        }
                        $print_str .= $all_str;
                    }
                }
                ##### MENU FOR EACH END #######################################################
                if($has_to_print > 0){
                    $this->do_print_os($print_str,$printer['printer'],$printer['no']);  
                    if(count($update_line_ids) > 0){
                        foreach ($update_line_ids as $sales_menu_id) {
                            $this->site_model->update_tbl('trans_sales_menus','sales_menu_id',array('kitchen_slip_printed'=>1),$sales_menu_id);      
                        }   
                    }
                    if(count($update_line_mod_ids) > 0){
                        foreach ($update_line_mod_ids as $sales_mod_id) {
                            $this->site_model->update_tbl('trans_sales_menu_modifiers','sales_mod_id',array('kitchen_slip_printed'=>1),$sales_mod_id);      
                        }   
                    }           
                }
                // echo "<pre>".$print_str."</pre>";
            }################# END PRINT #######################################################
        }
        public function print_os_removes($sales_id=null,$removes=array()){
            $reasons = $removes;
            $reasons = sess('reasons');
            $printers = $this->set_printers();
            $return = $this->get_order(false,$sales_id);
            $order = $return['order'];
            $details = $return['details'];
            $cancels = array();
            foreach ($reasons as $ctr => $rea) {
                if($rea['type'] == 'mod' || $rea['type'] == 'menu'){
                    $exp = explode(' - ', $rea['ref_name']);
                    $type = $rea['type'];
                    $name = $exp[1];
                    $qty = explode(':', $exp[2]);
                    $on_id = null;
                    $menuLine = explode(' ',$exp[0]);
                    if(count($menuLine) > 1){
                        $on = explode(':',$menuLine[0]);
                        $ln = explode(':',$menuLine[1]);
                        $on_id = $on[1]; 
                        $ln_id = $ln[1]; 
                    }
                    else{
                        $ln = explode(':',$menuLine[0]);
                        $ln_id = $ln[1]; 
                    }
                    $cancels[] = array(
                        'line' => $ln_id,
                        'item' => $rea['ref_id'],
                        'name' => $name,
                        'type' => $type,
                        'qty' => $qty[1],
                        'on_id' => $on_id,
                        'reason'=>$rea['reason'],
                        'datetime'=>$rea['datetime']
                    );                
                }
            }
            $merge = array();
            foreach ($cancels as $ctr => $cn) {
                if($cn['type'] == 'menu'){
                    $merge[$cn['line']] = array(
                        'item'  => $cn['item'],
                        'name'  => $cn['name'],
                        'reason' =>$cn['reason'],
                        'type' => $cn['type'],
                        'qty' => $cn['qty'],
                        'mods' => array(),
                        'on_id' => $cn['on_id']
                    );
                }
            }
            foreach ($cancels as $ctr => $cn) {
                if($cn['type'] == 'mod'){
                    if(isset($merge[$cn['line']]) ){
                            if($cn['line'] == $cn['line']){
                                $mrMD = $merge[$cn['line']];
                                $mrMD['mods'][] = array('mod_name'=>$cn['name']);
                                $merge[$cn['line']] = $mrMD;                                
                            }
                    }
                    else{
                        $merge[$cn['line']] = array(
                            'item'  => $cn['item'],
                            'name'  => $cn['name'],
                            'reason' =>$cn['reason'],
                            'type' => $cn['type'],
                            'mods' => array(),
                            'on_id' => $cn['on_id']
                        );
                    }
                }
            }
            foreach ($printers as $name => $printer) {
                $has_to_print = 0;
                $print_str = $this->os_header();
                $print_str .=  align_center(strtoupper('Cancelled ORDER SLIP'),38," ")."\r\n";
                $print_str .= "Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
                if($order['waiter_username'] != "")
                    $print_str .= "FS: ".$order['waiter_username']."\r\n";
                $print_str .= $order['datetime']."\r\n";
                $print_str .= "======================================"."\r\n";
                $print_str .=  align_center(strtoupper($name),38," ")."\r\n";
                if($order['table_name'] != ""){
                    $print_str .=  align_center($order['table_name'],38," ")."\r\n\r\n";
                }
                foreach ($merge as $line_id => $val) {
                    if($val['type'] == 'menu'){
                        $print_str .= $this->append_chars("**Cancelled Menu(s)","right",23," ")."\r\n";
                        $category = $this->site_model->get_tbl('menus',array('menu_id'=>$val['item']),array(),null,true,'menu_sub_cat_id');
                        $cat = $category[0];
                        if($printer['sub_cat_id'] == $cat->menu_sub_cat_id){
                            $print_str .= $this->append_chars($val['qty'],"right",4," ");
                            
                            $len = strlen($val['name']);
                            if($len > 30){
                                $arr2 = str_split($val['name'], 30);
                                $counter = 1;
                                foreach($arr2 as $k => $vv){
                                    if($counter == 1){
                                       $print_str .= $this->append_chars(substrwords($vv,100,""),"right",100," ").$this->append_chars(null,"left",8," ")."\r\n"; 
                                        
                                    }else{
                                        $print_str .= $this->append_chars("","right",4," ");
                                        $print_str .= $this->append_chars(substrwords($vv,100,""),"right",100," ").$this->append_chars(null,"left",8," ")."\r\n";
                                    }
                                    $counter++;
                                }

                            }else{
                                $print_str .= $this->append_chars(substrwords($val['name'],100,""),"right",100," ").$this->append_chars(null,"left",8," ")."\r\n"; 
                            }                       
                            if(count($val['mods']) > 0){
                                foreach ($val['mods'] as $key => $mod) {
                                    $print_str .= "     - ";
                                    $print_str .= $this->append_chars(substrwords($mod['mod_name'],100,""),"right",23," ")
                                        .$this->append_chars(null,"left",8," ")."\r\n";
                                }
                            }
                            $has_to_print++;
                            ######    
                        }
                    }###########################

                }

                foreach ($merge as $line_id => $val) {
                    if($val['type'] == 'mod'){
                        $print_str .= "\r\n".$this->append_chars("**Cancelled Modifier(s)","right",23," ")."\r\n";
                        $category = $this->site_model->get_tbl('menus',array('menu_id'=>$val['on_id']),array(),null,true,'menu_name,menu_sub_cat_id');
                        $cat = $category[0];
                        if($printer['sub_cat_id'] == $cat->menu_sub_cat_id){
                            $print_str .= $this->append_chars($val['name']." on ".$cat->menu_name." line ".$line_id,"right",26," ").$this->append_chars(null,"left",8," ")."\r\n";                        
                            $has_to_print++;
                        }
                    }
                }

               if($has_to_print > 0){
                   $this->do_print_os($print_str,$printer['printer'],$printer['no']);  
               }
            }
        }
        public function do_print_os($print_str="",$kitchen_printer="",$kitchen_printer_no=1){
            $filename = "order.txt";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);
            $batfile = "print.bat";
            $fh1 = fopen($batfile,'w+');
            $root = dirname(BASEPATH);
            $battxt = "NOTEPAD /P \"".realpath($root."/".$filename)."\" \r\n";
            if($kitchen_printer != ""){
                if($kitchen_printer != 'DEFAULT')
                    $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".$kitchen_printer."\"  ";
            }
            fwrite($fh1, $battxt);
            fclose($fh1);
            session_write_close();
            for ($i=0; $i < $kitchen_printer_no; $i++) { 
                exec($batfile);
            }
            session_start();
            unlink($filename);
            unlink($batfile);
        }   
        public function os_header(){
            $branch_details = $this->setup_model->get_branch_details();
            $branch = array();
            foreach ($branch_details as $bv) {
                $branch = array(
                    'id' => $bv->branch_id,
                    'res_id' => $bv->res_id,
                    'branch_code' => $bv->branch_code,
                    'name' => $bv->branch_name,
                    'branch_desc' => $bv->branch_desc,
                    'contact_no' => $bv->contact_no,
                    'delivery_no' => $bv->delivery_no,
                    'address' => $bv->address,
                    'base_location' => $bv->base_location,
                    'currency' => $bv->currency,
                    'inactive' => $bv->inactive,
                    'tin' => $bv->tin,
                    'machine_no' => $bv->machine_no,
                    'bir' => $bv->bir,
                    'permit_no' => $bv->permit_no,
                    'serial' => $bv->serial,
                    'accrdn' => $bv->accrdn,
                    'email' => $bv->email,
                    'website' => $bv->website,
                    'store_open' => $bv->store_open,
                    'store_close' => $bv->store_close,
                );
            }
            $userdata = $this->session->userdata('user');
            $print_str = "\r\n\r\n";
            $wrap = wordwrap($branch['name'],35,"|#|");
            $exp = explode("|#|", $wrap);
            foreach ($exp as $v) {
                $print_str .= align_center($v,38," ")."\r\n";
            }
            $wrap = wordwrap($branch['address'],35,"|#|");
            $exp = explode("|#|", $wrap);
            foreach ($exp as $v) {
                $print_str .= align_center($v,38," ")."\r\n";
            }
            $print_str .= 
             align_center('TIN: '.$branch['tin'],38," ")."\r\n"
            .align_center('ACCRDN: '.$branch['accrdn'],38," ")."\r\n"
            // .$this->align_center('BIR # '.$branch['bir'],42," ")."\r\n"
            .align_center('MIN: '.$branch['machine_no'],38," ")."\r\n"
            // .align_center('SN: '.$branch['serial'],38," ")."\r\n"
            .align_center('PERMIT: '.$branch['permit_no'],38," ")."\r\n";
            $print_str .= "======================================"."\r\n";
            return $print_str;
        }
    #REINPUT SECTION
        public function retrack(){
            sess_clear('trans_mod_cart');
            sess_clear('trans_cart');
            sess_clear('counter');
            sess_clear('trans_disc_cart');
            sess_clear('trans_charge_cart');
            sess_clear('reasons');
            sess_clear('trans_type_cart');
            $this->load->helper('dine/cashier_helper');
            $data = $this->syter->spawn(null);
            $data['page_title'] = fa('fa-random').' Retracking';
            $data['code'] = retrackPage();
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'retrackJs';
            $data['sideBarHide'] = true;
            $this->load->view('page',$data);
        }
        public function retrack_load(){
            $this->load->library('make');
            $this->site_model->db = $this->load->database('main', TRUE);
            $set = $this->cashier_model->get_pos_settings();
            start_load(0);
            sleep(1);
            $read_date = $this->input->post('read_date');
            update_load(10);
            $sargs["DATE(shifts.check_in) = DATE('".date2Sql($read_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            $join['users'] = array('content'=>'shifts.user_id = users.id');
            $select = "shifts.*,users.fname,users.lname,users.mname,users.suffix,users.username";
            $shift_results = $this->site_model->get_tbl('shifts',$sargs,array('check_in'=>'desc'),$join,true,$select);
            $shift_ids = array();
            foreach ($shift_results as $res) {
                $shift_ids[] = $res->shift_id;
            }    
            // $args   = $this->get_from_to_read_date($read_date);
            $args['trans_sales.shift_id'] = $shift_ids;
            $results = $this->cashier_model->get_trans_sales(null,$args);
            foreach ($results as $res) {
                if($res->type_id == 10){
                    $this->make->sRow();
                        $this->make->td($res->sales_id);
                        $this->make->td($res->trans_ref);
                        $this->make->td(sql2Date($res->datetime));
                        $this->make->td(toTimeW($res->datetime));
                        $this->make->td(num($res->total_amount));
                        $this->make->td(strtoupper($res->username));
                        $status = "";
                            if($res->trans_ref != "" && $res->inactive == 0){
                                $status = "SETTLED";
                            }
                            else if($res->trans_ref == "" && $res->inactive == 1){
                                $status = "CANCEL";
                            }  
                            else if($res->trans_ref != "" && $res->inactive == 1){
                                $status = "VOID";
                            }  
                            else{
                                $status = "OPEN";
                            }  
                        $this->make->td($status);
                    $this->make->eRow();
                }
            }
            $sales_rows = $this->make->code();
            update_load(60);
            

            $ids = explode(',',$set->controls);
            $types = array();
            foreach($ids as $value){
                $text = explode('=>',$value);
                if($text[0] == 1){
                    $texts='dine-in';
                }else{
                    $texts=$text[1];
                }
                $types[$text[1]] = $texts;                
            }

            foreach ($shift_results as $res) {
                $in = "";
                if($res->check_in != "")
                    $in = toTimeW($res->check_in);
                $out = "";
                if($res->check_out != "")
                    $out = toTimeW($res->check_out);
                $this->make->sRow();
                    $this->make->td(strtoupper($res->username));
                    $this->make->td(sql2Date($res->check_in));
                    $this->make->td($in);
                    $this->make->td($out);
                    $cashout = $res->cashout_id;
                    if($res->xread_id == "")
                        $cashout = "None";
                    $this->make->td($cashout);
                    $xread = $res->xread_id;
                    if($res->xread_id == "")
                        $xread = "None";
                    $this->make->td($xread);

                    $add = $this->make->button(fa('fa-plus'),array('id'=>'add-shift-'.$res->shift_id,'class'=>'add-shift-order btn-sm','style'=>'height:20px;line-height:0px;','ref'=>$res->shift_id,'user'=>$res->user_id,'return'=>true),'primary');
                    $sel = $this->make->select(null,'type-shift-'.$res->shift_id,$types,null,array('return'=>true),'',$add);
                    $this->make->td($sel);
                $this->make->eRow();
            }    
            $shift_rows = $this->make->code();            
            update_load(100);
            echo json_encode(array('sales_rows'=>$sales_rows,'shift_rows'=>$shift_rows));
        }
        public function get_from_to_read_date($date){
            $rargs["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            $select = "read_details.*";
            $results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
            $args = array();
            $from = "";
            $to = "";
            $datetimes = array();
            foreach ($results as $res) {
                $datetimes[] = $res->scope_from;
                $datetimes[] = $res->scope_to;
            }
            usort($datetimes, function($a, $b) {
              $ad = new DateTime($a);
              $bd = new DateTime($b);
              if ($ad == $bd) {
                return 0;
              }
              return $ad > $bd ? 1 : -1;
            });
            foreach ($datetimes as $dt) {
                $from = $dt;
                break;
            }    
            foreach ($datetimes as $dt) {
                $to = $dt;
            }    
            if($from != "" && $to != ""){
                $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            }
            else{
                $args["DATE(trans_sales.datetime) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
                $from = date('Y-m-d 00:00',strtotime($date));
                $to = date('Y-m-d 24:00',strtotime($date));
            }
            return $args;
        }
        public function counter_retrack($type=null,$sales_id=null){
            $this->load->model('site/site_model');
            $this->load->model('dine/cashier_model');
            $this->load->helper('dine/cashier_helper');
            $data = $this->syter->spawn(null);
            $typeCN = sess('trans_type_cart');

            $loaded = null;
            $order = array();

            $loc_res = $this->site_model->get_tbl('settings',array(),array(),null,true,'*',null,1);
            $local_tax = $loc_res[0]->local_tax;          
            $trans = $this->retrack_trans(false,$type);
            $time = $trans['datetime'];            
           
            if(isset($typeCN[0]['table'])){
                $tbl = $this->cashier_model->get_tables($typeCN[0]['table']);
                if($tbl){
                    $tcn = $typeCN[0];
                    $tcn['table_name'] = $tbl[0]->name;
                    $typeCN[0] = $tcn;
                    sess_initialize('trans_type_cart',$typeCN);
                }
            }
            $trans['user_id'] = $typeCN[0]['re_user_id'];
            $trans['shift_id'] = $typeCN[0]['re_shift_id'];
            sess_initialize('counter',$trans);
            
            $data['code'] = counterRetrackPage($type,$time,$loaded,$order,$typeCN,$local_tax);            
            $data['add_css'] = array('css/virtual_keyboard.css', 'css/cashier.css');
            $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js','js/jquery.scannerdetection.js');           
            $data['load_js'] = 'dine/cashier.php';
            $data['use_js'] = 'counterRetrackJs';
            $data['noNavbar'] = true;
            $this->load->view('cashier',$data);
        }
        function edit_datetime(){
            $this->load->helper('dine/cashier_helper');
            $data = $this->syter->spawn(null,false);
            $this->make->input('Date & Time','datetime',null,null,array('class'=>'rOkay datetimepicker','style'=>'position:initial;'),fa('fa-calendar'));
            $data['code'] = $this->make->code();
            // $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
            // $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
            $data['load_js'] = 'dine/cashier';
            $data['use_js'] = 'editDatetimeJs';
            $this->load->view('load',$data);
        }
        function update_datetime_trans(){
            $counter = sess('counter');
            $counter['datetime'] = date2SqlDateTime($this->input->post('datetime'));
            sess_initialize('counter',$counter);
            echo $counter['datetime'];
        }
        public function order_to_main($sales_order_id){
            $this->load->model('dine/main_model');
            $this->load->model('dine/cashier_model');
            $this->load->model('site/site_model');
            $args = array();
            $orders = array();
            $cols = array();
            // if($date_from != null)
            //    $args['trans_sales.datetime >='] = $date_from;
            // if($date_to != null)
            //    $args['trans_sales.datetime <='] = $date_to;
            
           
            $args =  array("trans_sales.sales_id" => $sales_order_id);
            $result = $this->cashier_model->get_just_trans_sales(
                null,
                $args,
                'asc'
            );
            $orders = $result->result();
            $cols = $result->list_fields();
            $prints = "";
            $sales = array();
            $sales_ids = array();
            if(count($orders) > 0 ){
                foreach ($orders as $ord) {
                    $row = array();
                    $sales_ids[] = $ord->sales_id;
                    foreach ($cols as $col) {
                        $row[$col] = $ord->$col;
                    }
                    $row['pos_id'] = TERMINAL_ID;
                    $sales[] = $row;
                }

                // $args['trans_sales.sales_id'] = $sales_ids;
                // $args['trans_sales.pos_id'] = TERMINAL_ID;
                // $this->main_model->delete_trans_tbl_batch('trans_sales',$args);
                $this->main_model->add_trans_sales_batch($sales);

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
                $tbl['reasons'] = $this->cashier_model->get_just_reasons($sales_ids);
                // echo var_dump($tbl['trans_sales_local_tax'])
                $details = array();
                foreach ($tbl as $table => $row) {
                    $cl = $this->site_model->get_tbl_cols($table);
                    unset($cl[0]);
                    $dets = array();
                    foreach ($row as $r) {
                        $det = array();
                        foreach ($cl as $c) {
                            $det[$c] = $r->$c;
                        }
                        $det['pos_id'] = TERMINAL_ID;
                        $dets[] = $det;
                    }####
                    $details[$table]=$dets;
                }
                #
                foreach ($details as $table => $tbl_rows) {
                    if(count($tbl_rows) > 0){
                        $bargs = array();
                        if($table == 'reasons'){
                            $bargs[$table.'.trans_id'] = $sales_ids;                        
                        }
                        else
                            $bargs[$table.'.sales_id'] = $sales_ids;                        
                        $bargs[$table.'.pos_id'] = TERMINAL_ID;
                        $this->main_model->delete_trans_tbl_batch($table,$bargs);
                        $this->main_model->add_trans_tbl_batch($table,$tbl_rows);
                    }
                }
                #
            }
        }
    #########################################
    #########################################
    public function print_kitchen_order_slip($sales_id=null,$kitchen_printer=null,$kitchen_printer_no=0,$just_true=false){
        $return = $this->get_order(false,$sales_id);
        $branch = $this->get_branch_details(false);
        $order = $return['order'];
        $details = $return['details'];

        $discounts = $return['discounts'];
        $totalsss = $this->total_trans(false,$details,$discounts);
        $discs = $totalsss['discs'];


        $print_str = "\r\n\r\n\r\n\r\n";
        $wrap = wordwrap($branch['name'],25,"|#|");
        $exp = explode("|#|", $wrap);
        foreach ($exp as $v) {
            $print_str .= $this->align_center($v,38," ")."\r\n";
        }

        $wrap = wordwrap($branch['address'],35,"|#|");
        $exp = explode("|#|", $wrap);
        foreach ($exp as $v) {
            $print_str .= $this->align_center($v,38," ")."\r\n";
        }

            // .$this->align_center(wordwrap($branch['address'],20,"|#|"),38," ")."\r\n"
            $print_str .= 
            $this->align_center('TIN: '.$branch['tin'],38," ")."\r\n"
            .$this->align_center('ACCRDN: '.$branch['accrdn'],38," ")."\r\n"
            // .$this->align_center('BIR: '.$branch['bir'],42," ")."\r\n"
            .$this->align_center('MIN: '.$branch['machine_no'],38," ")."\r\n"
            // .$this->align_center('SN #'.$branch['serial'],38," ")."\r\n"
            .$this->align_center('PERMIT: '.$branch['permit_no'],38," ")."\r\n\r\n";
            // ."=========================================="."\r\n"
            // ;
        if (!empty($order['void_ref']) || $order['inactive'] == 1) {
            $print_str .= $this->align_center("***** VOIDED TRANSACTION *****",38," ")."\r\n";
            $print_str .= $order['reason']."\r\n\r\n";
        }
        $header_print_str = $print_str;
        $header_print_str .= "======================================"."\r\n";
            if (!empty($payments)){
                $header_print_str .= "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                    // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
            }
            else{
                $header_print_str .= "Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
                    // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
            }
            $header_print_str .= $order['table_name']."\r\n";
            if($order['waiter_username'] != "")
                $header_print_str .= "FS: ".$order['waiter_username']."\r\n";
            $header_print_str .= $order['datetime']."\r\n";
        $header_print_str .= "======================================"."\r\n";
        $print_str = $header_print_str;
        ################################
        $discs_items = array();
        foreach ($discs as $disc) {
            if(isset($disc['items']))
                $discs_items[$disc['type']] = $disc['items'];
        }
        $dscTxt = array();
        foreach ($details as $line_id => $val) {
            foreach ($discs_items as $type => $dissss) {
                if(in_array($line_id, $dissss)){
                    $qty = 1;
                    if(isset($dscTxt[$val['menu_id']][$type]['qty'])){
                        $qty = $dscTxt[$val['menu_id']][$type]['qty'] + 1;
                    }
                    $dscTxt[$val['menu_id']][$type] = array('txt' => '#'.$type,'qty' => $qty);
                }
            }
        }
        $post_details = array();
        $update_line_ids = array();
        $update_line_mod_ids = array();
        $added_modifs = array();
        foreach ($details as $line_id => $val) {
            $modif_check = false;
            $category = $this->site_model->get_tbl('menus',array('menu_id'=>$val['menu_id']),array(),null,true,'menu_sub_cat_id');
            $cat = $category[0];
            if(BEVERAGE_ID != $cat->menu_sub_cat_id){
                if($just_true == false){
                    if($val['kitchen_slip_printed'] == 0){
                            if (!isset($post_details[$val['menu_id']])) {
                                $dscsacs = array();
                                if(isset($dscTxt[$val['menu_id']])){
                                    $dscsacs = $dscTxt[$val['menu_id']];
                                }
                                $remarksArr = array();
                                if($val['remarks'] != '')
                                    $remarksArr = array($val['remarks']." x ".$val['qty']);
                                
                                $kitchen_slip_printed = $val['kitchen_slip_printed'];
                                if($val['kitchen_slip_printed'] == ""){
                                    $kitchen_slip_printed = 0;
                                }

                                $post_details[$val['menu_id']] = array(
                                    'name' => $val['name'],
                                    'code' => $val['code'],
                                    'price' => $val['price'],
                                    'no_tax' => $val['no_tax'],
                                    'discount' => $val['discount'],
                                    'qty' => $val['qty'],
                                    'discounted'=>$dscsacs,
                                    'kitchen_slip_printed'=>$kitchen_slip_printed,
                                    'remarks'=>$remarksArr,
                                    'modifiers' => array()
                                );
                                $update_line_ids[]=$val['id'];
                            } else {
                                if($val['remarks'] != "")
                                    $post_details[$val['menu_id']]['remarks'][]= $val['remarks']." x ".$val['qty'];
                                $post_details[$val['menu_id']]['qty'] += $val['qty'];
                                $update_line_ids[]=$val['id'];
                            }
                            if (empty($val['modifiers']))
                                continue;
                            
                            $modif_check = true;
                            $modifs = $val['modifiers'];
                            $n_modifiers = $post_details[$val['menu_id']]['modifiers'];
                            foreach ($modifs as $vv) {
                                
                                $kitchen_slip_printed = $vv['kitchen_slip_printed'];
                                if($vv['kitchen_slip_printed'] == ""){
                                    $kitchen_slip_printed = 0;
                                }

                                if (!isset($n_modifiers[$vv['id']])) {
                                    $n_modifiers[$vv['id']] = array(
                                        'name' => $vv['name'],
                                        'price' => $vv['price'],
                                        'kitchen_slip_printed'=> $kitchen_slip_printed,
                                        'qty' => $val['qty'],
                                        'discount' => $vv['discount']
                                    );
                                    $update_line_mod_ids[]=$vv['sales_mod_id'];
                                } else {
                                    $n_modifiers[$vv['id']]['qty'] += $val['qty'];
                                    $update_line_mod_ids[]=$vv['sales_mod_id'];
                                }
                            }
                            $post_details[$val['menu_id']]['modifiers'] = $n_modifiers;
                    }
                    if (!empty($val['modifiers'])){
                        if(!$modif_check){
                            $modifs = $val['modifiers'];
                            foreach ($modifs as $vv) {
                                if($vv['kitchen_slip_printed'] == 0){
                                    $kitchen_slip_printed = $vv['kitchen_slip_printed'];
                                    if($vv['kitchen_slip_printed'] == ""){
                                        $kitchen_slip_printed = 0;
                                    }
                                    if(!isset($added_modifs['sales_mod_id'])){
                                        $added_modifs[$vv['id']] = array(
                                            'name' => $vv['name'],
                                            'price' => $vv['price'],
                                            'kitchen_slip_printed'=> $kitchen_slip_printed,
                                            'qty' => $val['qty'],
                                            'discount' => $vv['discount']
                                        );
                                        $update_line_mod_ids[]=$vv['sales_mod_id'];
                                    }
                                    else{
                                        $added_modifs[$vv['id']]['qty'] += $val['qty'];
                                        $update_line_mod_ids[]=$vv['sales_mod_id'];
                                    }
                                }
                                ####
                            }
                            #####    
                        }
                        ###########
                    }
                }
                else{
                    if (!isset($post_details[$val['menu_id']])) {
                        $dscsacs = array();
                        if(isset($dscTxt[$val['menu_id']])){
                            $dscsacs = $dscTxt[$val['menu_id']];
                        }
                        $remarksArr = array();
                        if($val['remarks'] != '')
                            $remarksArr = array($val['remarks']." x ".$val['qty']);
                        
                        $kitchen_slip_printed = $val['kitchen_slip_printed'];
                        if($val['kitchen_slip_printed'] == ""){
                            $kitchen_slip_printed = 0;
                        }

                        $post_details[$val['menu_id']] = array(
                            'name' => $val['name'],
                            'code' => $val['code'],
                            'price' => $val['price'],
                            'no_tax' => $val['no_tax'],
                            'discount' => $val['discount'],
                            'qty' => $val['qty'],
                            'discounted'=>$dscsacs,
                            'kitchen_slip_printed'=>$kitchen_slip_printed,
                            'remarks'=>$remarksArr,
                            'modifiers' => array()
                        );
                        $update_line_ids[]=$val['id'];
                    } else {
                        if($val['remarks'] != "")
                            $post_details[$val['menu_id']]['remarks'][]= $val['remarks']." x ".$val['qty'];
                        $post_details[$val['menu_id']]['qty'] += $val['qty'];
                        $update_line_ids[]=$val['id'];
                    }
                    if (empty($val['modifiers']))
                        continue;
                    
                    $modif_check = true;
                    $modifs = $val['modifiers'];
                    $n_modifiers = $post_details[$val['menu_id']]['modifiers'];
                    foreach ($modifs as $vv) {
                        
                        $kitchen_slip_printed = $vv['kitchen_slip_printed'];
                        if($vv['kitchen_slip_printed'] == ""){
                            $kitchen_slip_printed = 0;
                        }

                        if (!isset($n_modifiers[$vv['id']])) {
                            $n_modifiers[$vv['id']] = array(
                                'name' => $vv['name'],
                                'price' => $vv['price'],
                                'kitchen_slip_printed'=> $kitchen_slip_printed,
                                'qty' => $val['qty'],
                                'discount' => $vv['discount']
                            );
                            $update_line_mod_ids[]=$vv['sales_mod_id'];
                        } else {
                            $n_modifiers[$vv['id']]['qty'] += $val['qty'];
                            $update_line_mod_ids[]=$vv['sales_mod_id'];
                        }
                    }
                    $post_details[$val['menu_id']]['modifiers'] = $n_modifiers;
                    if (!empty($val['modifiers'])){
                        if(!$modif_check){
                            $modifs = $val['modifiers'];
                            foreach ($modifs as $vv) {
                                
                                    $kitchen_slip_printed = $vv['kitchen_slip_printed'];
                                    if($vv['kitchen_slip_printed'] == ""){
                                        $kitchen_slip_printed = 0;
                                    }
                                    if(!isset($added_modifs['sales_mod_id'])){
                                        $added_modifs[$vv['id']] = array(
                                            'name' => $vv['name'],
                                            'price' => $vv['price'],
                                            'kitchen_slip_printed'=> $kitchen_slip_printed,
                                            'qty' => $val['qty'],
                                            'discount' => $vv['discount']
                                        );
                                        $update_line_mod_ids[]=$vv['sales_mod_id'];
                                    }
                                    else{
                                        $added_modifs[$vv['id']]['qty'] += $val['qty'];
                                        $update_line_mod_ids[]=$vv['sales_mod_id'];
                                    }
                                ####
                            }
                            #####    
                        }
                        ###########
                    }
                    ##################################################################
                }    
            }
        }
        $print_str .=  $this->align_center('Order Slip',38," ")."\r\n";
        $tot_qty = 0;
        $needs_to_print = 0;
        foreach ($post_details as $menu_id => $val) {
             if($just_true == false){
                if($val['kitchen_slip_printed'] == 0){
                    $tot_qty += $val['qty'];
                    $print_str .= $this->append_chars($val['qty'],"right",4," ");
                    if ($val['qty'] == 1) {
                        $print_str .= $this->append_chars(substrwords($val['name'],21,""),"right",26," ").
                            $this->append_chars(null,"left",8," ")."\r\n";
                    } else {
                        $print_str .= $this->append_chars(substrwords($val['name'],21,"")." @ ".$val['price'],"right",26," ").
                            $this->append_chars(null,"left",8," ")."\r\n";
                    }
                    if(count($val['discounted']) > 0){
                        foreach ($val['discounted'] as $dssstxt) {
                          $print_str .= "      ";
                          $print_str .= $this->append_chars($dssstxt['txt']." x ".$dssstxt['qty'],"right",23," ")."\r\n";
                        }
                    }
                    if(isset($val['remarks']) && count($val['remarks']) > 0){
                        foreach ($val['remarks'] as $rmrktxt) {
                            $print_str .= "     * ";
                            $print_str .= $this->append_chars(ucwords($rmrktxt),"right",23," ")."\r\n";
                        }
                    }
                    $needs_to_print++;
                }
                if (empty($val['modifiers']))
                    continue;
                $modifs = $val['modifiers'];
                foreach ($modifs as $vv) {
                    if($vv['kitchen_slip_printed'] == 0){
                        $print_str .= "     * ".$vv['qty']." ";
                        if ($vv['qty'] == 1) {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        } else {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        }
                        $needs_to_print++;
                    }
                }
                ##########################
            }
            else{
                    $tot_qty += $val['qty'];
                    $print_str .= $this->append_chars($val['qty'],"right",4," ");
                    if ($val['qty'] == 1) {
                        $print_str .= $this->append_chars(substrwords($val['name'],21,""),"right",26," ").
                            $this->append_chars(null,"left",8," ")."\r\n";
                    } else {
                        $print_str .= $this->append_chars(substrwords($val['name'],21,"")." @ ".$val['price'],"right",26," ").
                            $this->append_chars(null,"left",8," ")."\r\n";
                    }
                    if(count($val['discounted']) > 0){
                        foreach ($val['discounted'] as $dssstxt) {
                          $print_str .= "      ";
                          $print_str .= $this->append_chars($dssstxt['txt']." x ".$dssstxt['qty'],"right",23," ")."\r\n";
                        }
                    }
                    if(isset($val['remarks']) && count($val['remarks']) > 0){
                        foreach ($val['remarks'] as $rmrktxt) {
                            $print_str .= "     * ";
                            $print_str .= $this->append_chars(ucwords($rmrktxt),"right",23," ")."\r\n";
                        }
                    }
                    $needs_to_print++;
                if (empty($val['modifiers']))
                    continue;
                $modifs = $val['modifiers'];
                foreach ($modifs as $vv) {
                        $print_str .= "     * ".$vv['qty']." ";
                        if ($vv['qty'] == 1) {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        } else {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        }
                        $needs_to_print++;
                }
                ##########################
            }
        }
        if(count($added_modifs) > 0){
            $print_str .= $this->append_chars('Modifiers Added.',"right",4," ")."\r\n";
            foreach ($added_modifs as $vv) {
                if($just_true == false){
                    if($vv['kitchen_slip_printed'] == 0){
                        $print_str .= "     * ".$vv['qty']." ";
                        if ($vv['qty'] == 1) {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        } else {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        }
                        $needs_to_print++;
                    }
                }
                else{
                    $print_str .= "     * ".$vv['qty']." ";
                    if ($vv['qty'] == 1) {
                        $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                            .$this->append_chars(null,"left",8," ")."\r\n";
                    } else {
                        $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                            .$this->append_chars(null,"left",8," ")."\r\n";
                    }
                    $needs_to_print++;
                }############################
            }
        } 

        $print_str .= "\r\n"."--------------------------------------"."\r\n";
        $print_str .= $this->append_chars(ucwords("TOTAL QTY"),"right",28," ").$this->append_chars(number_format(($tot_qty),2),"left",10," ")."\r\n";

        if($needs_to_print > 0){
            $filename = "order.txt";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);
            $batfile = "print.bat";
            $fh1 = fopen($batfile,'w+');
            $root = dirname(BASEPATH);
            // $battxt = "NOTEPAD /P \"".realpath($root."/".$filename)."\" \r\n";
            $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".$kitchen_printer."\"  ";
            fwrite($fh1, $battxt);
            fclose($fh1);
            session_write_close();
            for ($i=0; $i < $kitchen_printer_no; $i++) { 
                exec($batfile);
            }
            // exec($batfile);
            session_start();
            unlink($filename);
            unlink($batfile);
        }
        ##########################
        ### UPDATE SLIP PRINTED
        ##########################
            if(count($update_line_ids) > 0){
                foreach ($update_line_ids as $sales_menu_id) {
                    $this->site_model->update_tbl('trans_sales_menus','sales_menu_id',array('kitchen_slip_printed'=>1),$sales_menu_id);      
                }   
            }
            if(count($update_line_mod_ids) > 0){
                foreach ($update_line_mod_ids as $sales_mod_id) {
                    $this->site_model->update_tbl('trans_sales_menu_modifiers','sales_mod_id',array('kitchen_slip_printed'=>1),$sales_mod_id);      
                }   
            }
    }
    public function print_kitchen_order_slip_beverage($sales_id=null,$kitchen_beverage_printer=null,$kitchen_printer_beverage_no=0,$just_true=false){
        $return = $this->get_order(false,$sales_id);
        $branch = $this->get_branch_details(false);
        $order = $return['order'];
        $details = $return['details'];

        $discounts = $return['discounts'];
        $totalsss = $this->total_trans(false,$details,$discounts);
        $discs = $totalsss['discs'];


        $print_str = "\r\n\r\n\r\n\r\n";
        $wrap = wordwrap($branch['name'],25,"|#|");
        $exp = explode("|#|", $wrap);
        foreach ($exp as $v) {
            $print_str .= $this->align_center($v,38," ")."\r\n";
        }

        $wrap = wordwrap($branch['address'],35,"|#|");
        $exp = explode("|#|", $wrap);
        foreach ($exp as $v) {
            $print_str .= $this->align_center($v,38," ")."\r\n";
        }

            // .$this->align_center(wordwrap($branch['address'],20,"|#|"),38," ")."\r\n"
            $print_str .= 
            $this->align_center('TIN: '.$branch['tin'],38," ")."\r\n"
            .$this->align_center('ACCRDN: '.$branch['accrdn'],38," ")."\r\n"
            // .$this->align_center('BIR: '.$branch['bir'],42," ")."\r\n"
            .$this->align_center('MIN: '.$branch['machine_no'],38," ")."\r\n"
            // .$this->align_center('SN #'.$branch['serial'],38," ")."\r\n"
            .$this->align_center('PERMIT: '.$branch['permit_no'],38," ")."\r\n\r\n";
            // ."=========================================="."\r\n"
            // ;
        if (!empty($order['void_ref']) || $order['inactive'] == 1) {
            $print_str .= $this->align_center("***** VOIDED TRANSACTION *****",38," ")."\r\n";
            $print_str .= $order['reason']."\r\n\r\n";
        }
        $header_print_str = $print_str;
        $header_print_str .= "======================================"."\r\n";
             if (!empty($payments)){
                $header_print_str .= "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                    // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
            }
            else{
                $header_print_str .= "Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
                    // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
            }
            $header_print_str .= $order['table_name']."\r\n";
            if($order['waiter_username'] != "")
                $header_print_str .= "FS: ".$order['waiter_username']."\r\n";
            $header_print_str .= $order['datetime']."\r\n";

        $header_print_str .= "======================================"."\r\n";

        $print_str = $header_print_str;
        ################################
        $discs_items = array();
        foreach ($discs as $disc) {
            if(isset($disc['items']))
                $discs_items[$disc['type']] = $disc['items'];
        }

        $dscTxt = array();
        foreach ($details as $line_id => $val) {
            foreach ($discs_items as $type => $dissss) {
                if(in_array($line_id, $dissss)){
                    $qty = 1;
                    if(isset($dscTxt[$val['menu_id']][$type]['qty'])){
                        $qty = $dscTxt[$val['menu_id']][$type]['qty'] + 1;
                    }
                    $dscTxt[$val['menu_id']][$type] = array('txt' => '#'.$type,'qty' => $qty);
                }
            }
        }

        $post_details = array();
        $update_line_ids = array();
        $update_line_mod_ids = array();
        $added_modifs = array();
        foreach ($details as $line_id => $val) {
            $modif_check = false;
            $category = $this->site_model->get_tbl('menus',array('menu_id'=>$val['menu_id']),array(),null,true,'menu_sub_cat_id');
            $cat = $category[0];
            if(BEVERAGE_ID == $cat->menu_sub_cat_id){
                if($just_true == false){
                    if($val['kitchen_slip_printed'] == 0){
                            if (!isset($post_details[$val['menu_id']])) {
                                $dscsacs = array();
                                if(isset($dscTxt[$val['menu_id']])){
                                    $dscsacs = $dscTxt[$val['menu_id']];
                                }
                                $remarksArr = array();
                                if($val['remarks'] != '')
                                    $remarksArr = array($val['remarks']." x ".$val['qty']);
                                
                                $kitchen_slip_printed = $val['kitchen_slip_printed'];
                                if($val['kitchen_slip_printed'] == ""){
                                    $kitchen_slip_printed = 0;
                                }

                                $post_details[$val['menu_id']] = array(
                                    'name' => $val['name'],
                                    'code' => $val['code'],
                                    'price' => $val['price'],
                                    'no_tax' => $val['no_tax'],
                                    'discount' => $val['discount'],
                                    'qty' => $val['qty'],
                                    'discounted'=>$dscsacs,
                                    'kitchen_slip_printed'=>$kitchen_slip_printed,
                                    'remarks'=>$remarksArr,
                                    'modifiers' => array()
                                );
                                $update_line_ids[]=$val['id'];
                            } else {
                                if($val['remarks'] != "")
                                    $post_details[$val['menu_id']]['remarks'][]= $val['remarks']." x ".$val['qty'];
                                $post_details[$val['menu_id']]['qty'] += $val['qty'];
                                $update_line_ids[]=$val['id'];
                            }
                            if (empty($val['modifiers']))
                                continue;
                            
                            $modif_check = true;
                            $modifs = $val['modifiers'];
                            $n_modifiers = $post_details[$val['menu_id']]['modifiers'];
                            foreach ($modifs as $vv) {
                                
                                $kitchen_slip_printed = $vv['kitchen_slip_printed'];
                                if($vv['kitchen_slip_printed'] == ""){
                                    $kitchen_slip_printed = 0;
                                }

                                if (!isset($n_modifiers[$vv['id']])) {
                                    $n_modifiers[$vv['id']] = array(
                                        'name' => $vv['name'],
                                        'price' => $vv['price'],
                                        'kitchen_slip_printed'=> $kitchen_slip_printed,
                                        'qty' => $val['qty'],
                                        'discount' => $vv['discount']
                                    );
                                    $update_line_mod_ids[]=$vv['sales_mod_id'];
                                } else {
                                    $n_modifiers[$vv['id']]['qty'] += $val['qty'];
                                    $update_line_mod_ids[]=$vv['sales_mod_id'];
                                }
                            }
                            $post_details[$val['menu_id']]['modifiers'] = $n_modifiers;
                        
                    }
                    if (!empty($val['modifiers'])){
                        if(!$modif_check){
                            $modifs = $val['modifiers'];
                            foreach ($modifs as $vv) {
                                if($vv['kitchen_slip_printed'] == 0){
                                    $kitchen_slip_printed = $vv['kitchen_slip_printed'];
                                    if($vv['kitchen_slip_printed'] == ""){
                                        $kitchen_slip_printed = 0;
                                    }
                                    if(!isset($added_modifs['sales_mod_id'])){
                                        $added_modifs[$vv['id']] = array(
                                            'name' => $vv['name'],
                                            'price' => $vv['price'],
                                            'kitchen_slip_printed'=> $kitchen_slip_printed,
                                            'qty' => $val['qty'],
                                            'discount' => $vv['discount']
                                        );
                                        $update_line_mod_ids[]=$vv['sales_mod_id'];
                                    }
                                    else{
                                        $added_modifs[$vv['id']]['qty'] += $val['qty'];
                                        $update_line_mod_ids[]=$vv['sales_mod_id'];
                                    }
                                }
                                ####
                            }
                            #####    
                        }
                        ###########
                    }
                }
                else{
                    if (!isset($post_details[$val['menu_id']])) {
                        $dscsacs = array();
                        if(isset($dscTxt[$val['menu_id']])){
                            $dscsacs = $dscTxt[$val['menu_id']];
                        }
                        $remarksArr = array();
                        if($val['remarks'] != '')
                            $remarksArr = array($val['remarks']." x ".$val['qty']);
                        
                        $kitchen_slip_printed = $val['kitchen_slip_printed'];
                        if($val['kitchen_slip_printed'] == ""){
                            $kitchen_slip_printed = 0;
                        }

                        $post_details[$val['menu_id']] = array(
                            'name' => $val['name'],
                            'code' => $val['code'],
                            'price' => $val['price'],
                            'no_tax' => $val['no_tax'],
                            'discount' => $val['discount'],
                            'qty' => $val['qty'],
                            'discounted'=>$dscsacs,
                            'kitchen_slip_printed'=>$kitchen_slip_printed,
                            'remarks'=>$remarksArr,
                            'modifiers' => array()
                        );
                        $update_line_ids[]=$val['id'];
                    } else {
                        if($val['remarks'] != "")
                            $post_details[$val['menu_id']]['remarks'][]= $val['remarks']." x ".$val['qty'];
                        $post_details[$val['menu_id']]['qty'] += $val['qty'];
                        $update_line_ids[]=$val['id'];
                    }
                    if (empty($val['modifiers']))
                        continue;
                    
                    $modif_check = true;
                    $modifs = $val['modifiers'];
                    $n_modifiers = $post_details[$val['menu_id']]['modifiers'];
                    foreach ($modifs as $vv) {
                        
                        $kitchen_slip_printed = $vv['kitchen_slip_printed'];
                        if($vv['kitchen_slip_printed'] == ""){
                            $kitchen_slip_printed = 0;
                        }

                        if (!isset($n_modifiers[$vv['id']])) {
                            $n_modifiers[$vv['id']] = array(
                                'name' => $vv['name'],
                                'price' => $vv['price'],
                                'kitchen_slip_printed'=> $kitchen_slip_printed,
                                'qty' => $val['qty'],
                                'discount' => $vv['discount']
                            );
                            $update_line_mod_ids[]=$vv['sales_mod_id'];
                        } else {
                            $n_modifiers[$vv['id']]['qty'] += $val['qty'];
                            $update_line_mod_ids[]=$vv['sales_mod_id'];
                        }
                    }
                    $post_details[$val['menu_id']]['modifiers'] = $n_modifiers;
                        
                    if (!empty($val['modifiers'])){
                        if(!$modif_check){
                            $modifs = $val['modifiers'];
                            foreach ($modifs as $vv) {
                                    $kitchen_slip_printed = $vv['kitchen_slip_printed'];
                                    if($vv['kitchen_slip_printed'] == ""){
                                        $kitchen_slip_printed = 0;
                                    }
                                    if(!isset($added_modifs['sales_mod_id'])){
                                        $added_modifs[$vv['id']] = array(
                                            'name' => $vv['name'],
                                            'price' => $vv['price'],
                                            'kitchen_slip_printed'=> $kitchen_slip_printed,
                                            'qty' => $val['qty'],
                                            'discount' => $vv['discount']
                                        );
                                        $update_line_mod_ids[]=$vv['sales_mod_id'];
                                    }
                                    else{
                                        $added_modifs[$vv['id']]['qty'] += $val['qty'];
                                        $update_line_mod_ids[]=$vv['sales_mod_id'];
                                    }
                                ####
                            }
                            #####    
                        }
                        ###########
                    }
                    ################################
                }    
            }
        }

        $print_str .=  $this->align_center('Order Slip',38," ")."\r\n";
        $tot_qty = 0;
        $needs_to_print = 0;
        foreach ($post_details as $menu_id => $val) {
            if($just_true == false){    
                if($val['kitchen_slip_printed'] == 0){
                    $tot_qty += $val['qty'];
                    $print_str .= $this->append_chars($val['qty'],"right",4," ");
                    if ($val['qty'] == 1) {
                        $print_str .= $this->append_chars(substrwords($val['name'],21,""),"right",26," ").
                            $this->append_chars(null,"left",8," ")."\r\n";
                    } else {
                        $print_str .= $this->append_chars(substrwords($val['name'],21,"")." @ ".$val['price'],"right",26," ").
                            $this->append_chars(null,"left",8," ")."\r\n";
                    }
                    if(count($val['discounted']) > 0){
                        foreach ($val['discounted'] as $dssstxt) {
                          $print_str .= "      ";
                          $print_str .= $this->append_chars($dssstxt['txt']." x ".$dssstxt['qty'],"right",23," ")."\r\n";
                        }
                    }
                    if(isset($val['remarks']) && count($val['remarks']) > 0){
                        foreach ($val['remarks'] as $rmrktxt) {
                            $print_str .= "     * ";
                            $print_str .= $this->append_chars(ucwords($rmrktxt),"right",23," ")."\r\n";
                        }
                    }
                    $needs_to_print++;
                }
                if (empty($val['modifiers']))
                    continue;
                $modifs = $val['modifiers'];
                foreach ($modifs as $vv) {
                    if($vv['kitchen_slip_printed'] == 0){
                        $print_str .= "     * ".$vv['qty']." ";
                        if ($vv['qty'] == 1) {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        } else {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        }
                        $needs_to_print++;
                    }
                }
                ##########################
            }
            else{
                   $tot_qty += $val['qty'];
                   $print_str .= $this->append_chars($val['qty'],"right",4," ");
                   if ($val['qty'] == 1) {
                       $print_str .= $this->append_chars(substrwords($val['name'],21,""),"right",26," ").
                           $this->append_chars(null,"left",8," ")."\r\n";
                   } else {
                       $print_str .= $this->append_chars(substrwords($val['name'],21,"")." @ ".$val['price'],"right",26," ").
                           $this->append_chars(null,"left",8," ")."\r\n";
                   }
                   if(count($val['discounted']) > 0){
                       foreach ($val['discounted'] as $dssstxt) {
                         $print_str .= "      ";
                         $print_str .= $this->append_chars($dssstxt['txt']." x ".$dssstxt['qty'],"right",23," ")."\r\n";
                       }
                   }
                   if(isset($val['remarks']) && count($val['remarks']) > 0){
                       foreach ($val['remarks'] as $rmrktxt) {
                           $print_str .= "     * ";
                           $print_str .= $this->append_chars(ucwords($rmrktxt),"right",23," ")."\r\n";
                       }
                   }
                   $needs_to_print++;
               if (empty($val['modifiers']))
                   continue;
               $modifs = $val['modifiers'];
               foreach ($modifs as $vv) {
                       $print_str .= "     * ".$vv['qty']." ";
                       if ($vv['qty'] == 1) {
                           $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                               .$this->append_chars(null,"left",8," ")."\r\n";
                       } else {
                           $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                               .$this->append_chars(null,"left",8," ")."\r\n";
                       }
                       $needs_to_print++;
               }
               ########################## 
            }    
        }
        if(count($added_modifs) > 0){
            $print_str .= $this->append_chars('Modifiers Added.',"right",4," ")."\r\n";
            foreach ($added_modifs as $vv) {
                if($just_true == false){  
                    if($vv['kitchen_slip_printed'] == 0){
                        $print_str .= "     * ".$vv['qty']." ";
                        if ($vv['qty'] == 1) {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        } else {
                            $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                                .$this->append_chars(null,"left",8," ")."\r\n";
                        }
                        $needs_to_print++;
                    }
                }
                else{
                   $print_str .= "     * ".$vv['qty']." ";
                   if ($vv['qty'] == 1) {
                       $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                           .$this->append_chars(null,"left",8," ")."\r\n";
                   } else {
                       $print_str .= $this->append_chars(substrwords($vv['name'],18,""),"right",23," ")
                           .$this->append_chars(null,"left",8," ")."\r\n";
                   }
                   $needs_to_print++; 
                }
            }
        } 

        $print_str .= "\r\n"."--------------------------------------"."\r\n";
        $print_str .= $this->append_chars(ucwords("TOTAL QTY"),"right",28," ").$this->append_chars(number_format(($tot_qty),2),"left",10," ")."\r\n";


        if($needs_to_print > 0){
            $filename = "order.txt";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);
            $batfile = "print.bat";
            $fh1 = fopen($batfile,'w+');
            $root = dirname(BASEPATH);
            $battxt = "NOTEPAD /P \"".realpath($root."/".$filename)."\" \r\n";
            if($kitchen_beverage_printer != "")
                $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".$kitchen_beverage_printer."\"  ";
            fwrite($fh1, $battxt);
            fclose($fh1);
            session_write_close();
            for ($i=0; $i < $kitchen_printer_beverage_no; $i++) { 
                exec($batfile);
            }
            // exec($batfile);
            session_start();
            unlink($filename);
            unlink($batfile);
        }
        ##########################
        ### UPDATE SLIP PRINTED
        ##########################
            if(count($update_line_ids) > 0){
                foreach ($update_line_ids as $sales_menu_id) {
                    $this->site_model->update_tbl('trans_sales_menus','sales_menu_id',array('kitchen_slip_printed'=>1),$sales_menu_id);      
                }   
            }
            if(count($update_line_mod_ids) > 0){
                foreach ($update_line_mod_ids as $sales_mod_id) {
                    $this->site_model->update_tbl('trans_sales_menu_modifiers','sales_mod_id',array('kitchen_slip_printed'=>1),$sales_mod_id);      
                }   
            }
    }
    public function append_chars($string,$position = "right",$count = 0, $char = ""){
        $rep_count = $count - strlen($string);
        $append_string = "";
        for ($i=0; $i < $rep_count ; $i++) {
            $append_string .= $char;
        }
        if ($position == 'right')
            return $string.$append_string;
        else
            return $append_string.$string;
    }
    public function align_center($string,$count,$char = " "){
        $rep_count = $count - strlen($string);
        for ($i=0; $i < $rep_count; $i++) {
            if ($i % 2 == 0) {
                $string = $char.$string;
            } else {
                $string = $string.$char;
            }
        }
        return $string;
    }
    public function manager_view_orders($terminal='my',$status='open',$types='all',$now=null,$show='box'){
        $this->load->model('dine/cashier_model');
        $this->load->model('site/site_model');
        $args = array(
            "trans_sales.trans_ref"=>null,
            "trans_sales.terminal_id"=>TERMINAL_ID,
            "trans_sales.type_id"=>SALES_TRANS,
            "trans_sales.inactive"=>0,
        );
        if($terminal != 'my')
            unset($args["trans_sales.terminal_id"]);
        if($status != 'open'){
            unset($args["trans_sales.trans_ref"]);
            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        }
        if($types != 'all'){
            $args["trans_sales.type"] = $types;
        }
        $orders = $this->cashier_model->get_trans_sales(null,$args);
        // echo $this->cashier_model->db->last_query();
        $code = "";
        $ids = array();
        $time = $this->site_model->get_db_now();
        $this->make->sDivRow();
        $ord=array();
        $combine_cart = sess('trans_combine_cart');
        foreach ($orders as $res) {
            $status = "open";
            if($res->trans_ref != "")
                $status = "settled";
            $ord[$res->sales_id] = array(
                "type"=>$res->type,
                "status"=>$status,
                "user_id"=>$res->user_id,
                "name"=>$res->username,
                "terminal_id"=>$res->terminal_id,
                "terminal_name"=>$res->terminal_name,
                "shift_id"=>$res->shift_id,
                "datetime"=>$res->datetime,
                "amount"=>$res->total_amount
            );
            if($show == "box"){
                $this->make->sDivCol(4,'left',0);
                    $this->make->sDiv(array('class'=>'order-btn','id'=>'order-btn-'.$res->sales_id,'ref'=>$res->sales_id));
                        if($res->trans_ref == null){
                            $this->make->sBox('default',array('class'=>'box-solid'));
                        }else{
                            $this->make->sBox('default',array('class'=>'box-solid bg-green'));
                        }
                            $this->make->sBoxBody();
                                $this->make->sDivRow();
                                    $this->make->sDivCol(6);
                                        $this->make->H(5,strtoupper($res->type)." #".$res->sales_id,array("style"=>'font-weight:700;'));
                                        if($res->trans_ref == null){
                                            $this->make->H(5,strtoupper($res->username),array("style"=>'color:#888'));
                                            $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#888'));
                                        }else{
                                            $this->make->H(5,strtoupper($res->username),array("style"=>'color:#fff'));
                                            $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#fff'));
                                        }
                                        $this->make->H(5,tagWord(strtoupper(ago($res->datetime,$time) ) ) );
                                    $this->make->eDivCol();
                                    $this->make->sDivCol(6);
                                        $this->make->H(4,'Order Total',array('class'=>'text-center'));
                                        $this->make->H(3,num($res->total_amount),array('class'=>'text-center'));
                                    $this->make->eDivCol();
                                $this->make->eDivRow();

                            $this->make->eBoxBody();
                        $this->make->eBox();
                    $this->make->eDiv();
                $this->make->eDivCol();
            }
            else if($show=='combineList'){
                $got = false;
                if(count($combine_cart) > 0){
                    foreach ($combine_cart as $key => $co) {
                        if($co['sales_id'] == $res->sales_id){
                            $got = true;
                            break;
                        }
                    }
                }
                if(!$got){
                    $this->make->sDivRow(array('class'=>'orders-list-div-btnish sel-row','id'=>'order-btnish-'.$res->sales_id, "ref"=>$res->sales_id, "type"=>$res->type));
                        $this->make->sDivCol(6);
                            $this->make->sDiv(array('style'=>'margin-left:10px;'));
                                $this->make->H(5,strtoupper($res->type)." #".$res->sales_id,array("style"=>'font-weight:700;'));
                                $this->make->H(5,strtoupper($res->username),array("style"=>'color:#888'));
                                $this->make->H(5,strtoupper($res->terminal_name),array("style"=>'color:#888'));
                            $this->make->eDiv();
                        $this->make->eDivCol();
                        $this->make->sDivCol(6);
                            $this->make->sDiv(array('style'=>'margin-left:10px;'));
                                if($status != 'open')
                                    $this->make->H(4,'ORDER TOTAL',array('class'=>'text-center'));
                                else
                                    $this->make->H(4,'BALANCE DUE',array('class'=>'text-center'));
                                // $this->make->H(3,num($res->total_amount),array('class'=>'text-center','style'=>'margin-top:10px;'));
                                $this->make->H(3,num($res->total_amount),array('class'=>'text-center'));
                            $this->make->eDiv();
                        $this->make->eDivCol();
                        // $this->make->sDivCol(4);
                            // $this->make->sDiv(array('class'=>'order-btn-right-container','style'=>'margin-left:10px;margin-right:10px;margin-top:15px;'));
                                // $this->make->button(fa('fa-angle-double-right fa-lg fa-fw'),array('id'=>'add-to-btn-'.$res->sales_id,'ref'=>$res->sales_id,'class'=>'add-btn-row btn-block counter-btn-green'));
                            // $this->make->eDiv();
                        // $this->make->eDivCol();
                    $this->make->eDivRow();
                }
            }
            $ids[] = $res->sales_id;
        }
        //}
        $this->make->eDivRow();
        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'ids'=>$ord));
    }
    public function script_print_all(){
        $read_date = '2016-05-30';
        $zread_id = 2;
        $args = array();
        if($zread_id != null){
            $lastRead = $this->cashier_model->get_z_read($zread_id);
            $zread = array();
            if(count($lastRead) > 0){
                foreach ($lastRead as $res) {
                    $zread = array(
                        'from' => $res->scope_from,
                        'to'   => $res->scope_to,
                        'old_gt_amnt' => $res->old_total,
                        'grand_total' => $res->grand_total,
                        'read_date' => $res->read_date,
                        'id' => $res->id,
                        'user_id'=>$res->user_id
                    );
                    $read_date = $res->read_date;
                }           
            }
            $args['trans_sales.datetime >= '] = $zread['from'];             
            $args['trans_sales.datetime <= '] = $zread['to'];
            // $file_flg = "Z".date('Ymd',strtotime($read_date)).".flg";
        }

        $curr = true; 
        $trans = $this->trans_sales($args,$curr);
        $sales = $trans['sales'];
        echo count($sales['settled']['ids'])."<br><br>";
        // echo var_dump($sales['settled']['ids']);
        foreach ($sales['settled']['ids'] as $key => $sales_id) {
            $this->print_sales_receipt($sales_id,false,false,false);
            // break;
        }
        
        // $this->print_sales_receipt(130,false,false,false);
        // $sales['settled']['ids']
    }    
    public function scenario(){
        $trans = 0;
        $payments = array(
            '1'=> array(
                    array('cash'=>array('type'=>'cash','amount'=>326.79,'card_number'=>null,'approval'=>null))
                   ) ,
            '2'=> array(
                    array('gc'=>array('type'=>'gc','amount'=>326.79,'card_number'=>'12345678','approval'=>null))
                  ),
            '3'=> array(
                    array('debit'=>array('type'=>'debit','amount'=>326.79,'card_number'=>'12345678','approval'=>'12345678'))
                  ),
            '4'=> array(
                    array('credit'=>array('type'=>'credit','amount'=>326.79,'card_number'=>'12345678','approval'=>'12345678'))
                  ),
            '5'=> array(
                    array('eplus'=>array('type'=>'eplus','amount'=>326.79,'card_number'=>'12345678','approval'=>null))
                  ),
            '6'=> array(
                    array('smac'=>array('type'=>'smac','amount'=>326.79,'card_number'=>'12345678','approval'=>null))
                  ),
            '7'=> array(
                    array('online'=>array('type'=>'online','amount'=>326.79,'card_number'=>'12345678','approval'=>null))
                  ),
            '8'=> array(
                    array('cash'=>array('type'=>'cash','amount'=>126.79,'card_number'=>null,'approval'=>null)),
                    array('credit'=>array('type'=>'credit','amount'=>200.00,'card_number'=>'12345678','approval'=>'12345678')),
                  ),
            '9'=> array(
                    array('cash'=>array('type'=>'cash','amount'=>126.79,'card_number'=>null,'approval'=>null)),
                    array('gc'=>array('type'=>'gc','amount'=>200.00,'card_number'=>'12345678','approval'=>null)),
                  ),
            '10'=> array(
                    array('cash'=>array('type'=>'cash','amount'=>126.79,'card_number'=>null,'approval'=>null)),
                    array('debit'=>array('type'=>'debit','amount'=>200.00,'card_number'=>'12345678','approval'=>'12345678')),
                  ),
            '11'=> array(
                    array('cash'=>array('type'=>'cash','amount'=>126.79,'card_number'=>null,'approval'=>null)),
                    array('eplus'=>array('type'=>'eplus','amount'=>200.00,'card_number'=>'12345678','approval'=>null)),
                  ),
            '12'=> array(
                    array('cash'=>array('type'=>'cash','amount'=>250.00,'card_number'=>null,'approval'=>null)),
                    array('smac'=>array('type'=>'smac','amount'=>76.79,'card_number'=>'12345678','approval'=>null)),
                  ),
            '14'=> array(
                    array('cash'=>array('type'=>'cash','amount'=>176.79,'card_number'=>null,'approval'=>null)),
                    array('coupon'=>array('type'=>'coupon','amount'=>150.00,'card_number'=>'12345678','approval'=>null)),
                  ),
            '15'=> array(
                    array('credit'=>array('type'=>'credit','amount'=>126.79,'card_number'=>'12345678','approval'=>'12345678')),
                    array('gc'=>array('type'=>'gc','amount'=>200.00,'card_number'=>'12345678','approval'=>null)),
                  ),
            '16'=> array(
                    array('credit'=>array('type'=>'credit','amount'=>200.00,'card_number'=>'12345678','approval'=>'12345678')),
                    array('debit'=>array('type'=>'debit','amount'=>126.79,'card_number'=>'12345678','approval'=>'12345678'))
                  ),
            '17'=> array(
                    array('credit'=>array('type'=>'credit','amount'=>200.00,'card_number'=>'12345678','approval'=>'12345678')),
                    array('eplus'=>array('type'=>'eplus','amount'=>126.79,'card_number'=>'12345678','approval'=>null)),
                  ),
            '18'=> array(
                    array('credit'=>array('type'=>'credit','amount'=>225.00,'card_number'=>'12345678','approval'=>'12345678')),
                    array('smac'=>array('type'=>'smac','amount'=>101.79,'card_number'=>'12345678','approval'=>null)),
                  ),
            '20'=> array(
                    array('credit'=>array('type'=>'credit','amount'=>200.00,'card_number'=>'12345678','approval'=>'12345678')),
                    array('coupon'=>array('type'=>'coupon','amount'=>126.79,'card_number'=>'12345678','approval'=>null)),
                  ),
            '21'=> array(
                    array('debit'=>array('type'=>'debit','amount'=>126.79,'card_number'=>'12345678','approval'=>'12345678')),
                    array('gc'=>array('type'=>'gc','amount'=>200.00,'card_number'=>'12345678','approval'=>null) ),
                  ),
            '22'=> array(
                    array('debit'=>array('type'=>'debit','amount'=>200.00,'card_number'=>'12345678','approval'=>'12345678')),
                    array('eplus'=>array('type'=>'eplus','amount'=>126.79,'card_number'=>'12345678','approval'=>null)),
                  ),
            '23'=> array(
                    array('debit'=>array('type'=>'debit','amount'=>225.00,'card_number'=>'12345678','approval'=>'12345678')),
                    array('smac'=>array('type'=>'smac','amount'=>101.79,'card_number'=>'12345678','approval'=>null)),
                  ),
            '25'=> array(
                    array('debit'=>array('type'=>'debit','amount'=>126.79,'card_number'=>'12345678','approval'=>'12345678')),
                    array('coupon'=>array('type'=>'coupon','amount'=>200.00,'card_number'=>'12345678','approval'=>null)),
                  ),
        );
        $ctr = 1;
        $last = count($payments);
        foreach ($payments as $no => $pay) {
            $this->cashier_model->db = $this->load->database('default', TRUE);
            $this->site_model->db = $this->load->database('default', TRUE);
            // $trans_cart = sess('trans_cart');
            // echo $no."==";
            // echo var_dump($trans_cart)."<br>";
            $order = $this->submit_trans(false,null,false,null,null,null,false,null,false);
            // echo var_dump($order)."<br>";
            $sales_id = $order['id'];
            foreach ($pay as $key => $val) {
                foreach ($val as $v) {
                    $send = false;
                    if($ctr == $last){
                        $send = true;
                    }
                    $this->add_payment2($sales_id,$v['amount'],$v['type'],$v['approval'],$v['card_number'],$send);
                }
            }
            $this->cashier_model->db = $this->load->database('default', TRUE);
            $this->site_model->db = $this->load->database('default', TRUE);
            $trans++;
            $ctr++;
        }
        echo "created ".$trans." orders";

        
        // for ($i=1; $i <= 21; $i++) { 
        //     $this->add_payment2($sales_id=null,$amount=null,$type=null,$approval_code=null,$card_number=null);
        // }
    }
    public function add_payment2($sales_id=null,$amount=null,$type=null,$approval_code=null,$card_number=null,$send=false){
        $this->load->model('dine/cashier_model');
        $this->load->model('site/site_model');
        $order = $this->get_order_header(false,$sales_id);
        $error = "";
        $payments = $this->get_order_payments(false,$sales_id);
        $total_to_pay = $order['amount'];
        $paid = $order['paid'];
        $total_paid = 0;
        $balance = $order['balance'];
        if(count($payments) > 0){
            foreach ($payments as $pay_id => $pay) {
                $total_paid += $pay['amount'];
            }
        }
        if($total_to_pay >= $total_paid)
            $total_to_pay -= $total_paid;
        else
            $total_to_pay = 0;
        $change = 0;
        

        $log_user = $this->session->userdata('user');
        if($total_to_pay > 0){
            $payment = array(
                'sales_id'      =>  $sales_id,
                'payment_type'  =>  $type,
                'amount'        =>  $amount,
                'to_pay'        =>  $total_to_pay,
                "user_id"       =>  $log_user['id'],
                // 'reference'     =>  null,
                // 'card_type'     =>  null
            );


            if ($type=="credit") {
                $payment['card_type'] = 'master';
                $ma = $card_number;
                for ($i=0,$x=strlen($ma); $i < $x-4; $i++) { $ma[$i] = "*"; }
                $payment['card_number'] = $ma;
                $payment['approval_code'] = $approval_code;
            } elseif ($type=="debit") {
                $payment['card_number'] = $card_number;
                $payment['approval_code'] = $approval_code;
            } elseif ($type=="smac") {
                $payment['card_number'] = $card_number;
            } elseif ($type=="eplus") {
                $payment['card_number'] = $card_number;
            } elseif ($type=="online") {
                $payment['card_number'] = $card_number;
            } elseif ($type=="gc") {
                $this->load->model('dine/gift_cards_model');
                $payment['reference'] = $card_number;
                $payment['amount'] = $amount;
            } elseif ($type=="coupon") {
                $payment['reference'] = $card_number;
                $payment['amount'] = $amount;
            } elseif ($type=="chit") {
                $payment['user_id'] = $this->input->post('manager_id');
            }
            $curr_shift_id = $order['shift_id'];
            $time = $this->site_model->get_db_now();
            $get_curr_shift = $this->clock_model->get_shift_id(date2Sql($time),$log_user['id']);
            if(count($get_curr_shift) > 0){
                $curr_shift_id = $get_curr_shift[0]->shift_id;
            }
            $payment_id = $this->cashier_model->add_trans_sales_payments($payment);
            $new_total_paid = 0;
            if($amount > $total_to_pay){
                $new_total_paid = $order['amount'];
                $balance = 0;
            }
            else{
                $new_total_paid = $total_paid+$amount;
                $balance = $balance - $amount;
                // $balance = $total_to_pay - $amount;
            }

            // var_dump($payment);
            $this->cashier_model->update_trans_sales(array('total_paid'=>$new_total_paid,'user_id'=>$log_user['id'],'shift_id'=>$curr_shift_id),$sales_id);
            $log_user = $this->session->userdata('user');
            $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Added Payment ".$amount." on Sales Order #".$sales_id,$sales_id);

            if (num($balance) == 0) {
            //     // if ($paid == 0) {
                    $this->finish_trans($sales_id,true);
                    $set = $this->cashier_model->get_pos_settings();
                    $no_prints = $set->no_of_receipt_print;
                    $order_slip_prints = $set->no_of_order_slip_print;
                    $approved_by = null;
                    if($type == 'chit'){
                        $approved_by = $payment['user_id'];
                        $app = $this->site_model->get_user_details($approved_by);
                        $this->logs_model->add_logs('Sales Order',$app->id,$app->fname." ".$app->mname." ".$app->lname." ".$app->suffix." Approved CHIT Payment ".$amount." on Sales Order #".$sales_id,$sales_id);
                    }
                    $print_echo = $this->print_sales_receipt($sales_id,false,false,true,null,true,$no_prints,$order_slip_prints,$approved_by,false,true);
                    
                    if($send){
                        if(MALL_ENABLED){
                            if(MALL == 'megamall'){
                                $this->sm_file($order['datetime']);
                            }
                        }
                    }

            //     // }
            }
            // if($paid == 0){
            //     $move = true;
            // }
            // else
            //     $move = false;
            // if(in_array($type, array([0]=>'cash'))){
            if ($type == 'cash') {
                if($amount > $total_to_pay){
                    $change = $amount - $total_to_pay;
                }
            }
        }
        else{
            $error = 'Amount Received.';
        }
        // echo json_encode(array('error'=>$error,'change'=>$change,'tendered'=>$amount,'balance'=>num($balance) ));
    }
    public function counter2($type=null,$sales_id=null){
        $this->load->model('site/site_model');
        $this->load->model('dine/cashier_model');
        $this->load->helper('dine/cashier_helper');
        $data = $this->syter->spawn(null);
        $loaded = null;
        $order = array();

        $loc_res = $this->site_model->get_tbl('settings',array(),array(),null,true,'*',null,1);
        $local_tax = $loc_res[0]->local_tax;
        $kitchen_printer = "";
        if(iSetObj($loc_res[0],'kitchen_printer_name') != ""){
            $kitchen_printer = iSetObj($loc_res[0],'kitchen_printer_name');
        }

        if($sales_id != null){
            $order = $this->get_order(false,$sales_id);
            $trans = $this->load_trans(false,$order);
            $time = $trans['datetime'];
            $type = $type." #".$order['order']['sales_id'];
            $loaded = "loaded";
        }
        else{
            $trans = $this->new_trans(false,$type);
            $time = $trans['datetime'];
        }
        if(isset($order['order']))
            $order = $order['order'];
        $typeCN = sess('trans_type_cart');
        
        if(isset($typeCN[0]['table'])){
            $error = $this->check_tbl_activity($typeCN[0]['table'],false);
            if($error == ""){
                $this->update_tbl_activity($typeCN[0]['table']);
            }
            else{
                site_alert($error,'error');
                header("Location:".base_url()."cashier");
            }
        }

        $data['code'] = counterPage2($type,$time,$loaded,$order,$typeCN,$local_tax,$kitchen_printer);
        // $data['add_css'] = 'css/cashier.css';
        $data['add_css'] = array('css/virtual_keyboard.css', 'css/cashier.css');
        $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
        $data['load_js'] = 'dine/cashier.php';
        $data['use_js'] = 'counterJs2';
        $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }
    public function submit_trans2($asJson=true,$submit=null,$void=false,$void_ref=null,$cart=null,$mod_cart=null,$print=false,$split_id=null,$printKitSlip=false){
        $error = null;
        $act = null;
        $sales_id = null;
        $type = null;
        $must_ref = $this->input->post('must_ref');
        $must_datetime = $this->input->post('must_datetime');
        $must_shift = 413;
        $must_user_id = 27;
        // $this->site_model->db = $this->load->database('main', TRUE);
        $ref_check = $this->site_model->get_tbl('trans_sales',array('trans_ref ='=>$must_ref));
        // $this->site_model->db = $this->load->database('default', TRUE);
        if(count($ref_check) > 0){
            $error = "Trans ref is already inserted";
        }
        else{
            $this->load->model('dine/cashier_model');
            $counter = sess('counter');
            $trans_cart = sess('trans_cart');
            $trans_mod_cart = sess('trans_mod_cart');
            $trans_type_cart = sess('trans_type_cart');
            $trans_disc_cart = sess('trans_disc_cart');
            $trans_charge_cart = sess('trans_charge_cart');
            $reasons = sess('reasons');
            
            $totals  = $this->total_trans(false,$cart);
            $total_amount = $totals['total'];
            $charges = $totals['charges'];
            $local_tax = $totals['local_tax'];
            
            
            $type_id = SALES_TRANS;
            $print_echo = array();
            if($void === true){
                $type_id = SALES_VOID_TRANS;
            }

            if($void_ref == null || $void_ref == 0)
                $void_ref = null;

            if(count($trans_cart) <= 0){
                $error = "Error! There are no items.";
            }
            else if(count($counter) <= 0){
                $error = "Error! Shift or User is invalid.";
            }
            else if(NEED_FOOD_SERVER && !isset($counter['waiter_id']) ){
                $error = "Please Select a Food Server.";
            }
            else{
                if(count($trans_disc_cart) > 0){
                    foreach ($trans_disc_cart as $disc_id => $row) {
                        if(!isset($row['disc_type'])){
                            $error = "Select Discount Type. If equally Divided or All Items.";
                        }
                        else{
                            if($row['disc_type'] == "")
                                $error = "Select Discount Type. If equally Divided or All Items.";
                        }
                    }
                    if($error != null){
                        if($asJson){
                            echo json_encode(array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type));
                            return false;
                        }
                        else{
                            return array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type);
                        }
                    }
                }


                if(is_array($cart)){
                    $trans_cart = $cart;
                }
                if(is_array($mod_cart)){
                    $trans_mod_cart = $mod_cart;
                }
                $type = $counter['type'];
                #save sa trans_sales
                $table = null;
                $guest = 0;
                $customer = null;
                if(isset($trans_type_cart[0]['table'])){
                    $table = $trans_type_cart[0]['table'];
                }
                if(isset($trans_type_cart[0]['guest'])){
                    $guest = $trans_type_cart[0]['guest'];
                }
                if(count($trans_disc_cart) > 0){
                    foreach ($trans_disc_cart as $disc_code => $dc) {
                        $guest = $dc['guest'];
                    }
                } 
                if(isset($trans_type_cart[0]['customer_id'])){
                    $customer = $trans_type_cart[0]['customer_id'];
                }
                $waiter = null;
                if(isset($counter['waiter_id'])){
                    $waiter = $counter['waiter_id'];
                }
                $splid = 0;
                if($split_id != null)
                    $splid = $split_id;
                 // $total_amount = number_format($total_amount, 2, '.', '');
                 $total_amount = $total_amount;
                 $trans_sales = array(
                    "user_id"       => $must_user_id,
                    // "user_id"       => $counter['user_id'],
                    "type_id"       => $type_id,
                    "trans_ref"     => $must_ref,
                    // "shift_id"      => $counter['shift_id'],
                    "shift_id"      => $must_shift,
                    "terminal_id"   => $counter['terminal_id'],
                    "type"          => $counter['type'],
                    // "datetime"      => date2SqlDateTime($counter['datetime']),
                    "datetime"      => date2SqlDateTime($must_datetime),
                    "total_amount"  => $total_amount,
                    "void_ref"      => $void_ref,
                    "memo"          => null,
                    "table_id"      => $table,
                    "guest"         => $guest,
                    "customer_id"   => $customer,
                    "waiter_id"     => $waiter,
                    "split"         => $splid
                );
                $user = $this->session->userdata('user');
                if(isset($counter['sales_id']) && $counter['sales_id'] != null){
                    $sales_id = $counter['sales_id'];
                    $this->cashier_model->update_trans_sales($trans_sales,$sales_id);
                    $this->logs_model->add_logs('Sales Order',$user['id'],$user['full_name']." Updated Sales Order #".$sales_id,$sales_id);
                    $this->cashier_model->delete_trans_sales_menus($sales_id);
                    $this->cashier_model->delete_trans_sales_items($sales_id);
                    $this->cashier_model->delete_trans_sales_menu_modifiers($sales_id);
                    $this->cashier_model->delete_trans_sales_discounts($sales_id);
                    $this->cashier_model->delete_trans_sales_charges($sales_id);
                    $this->cashier_model->delete_trans_sales_tax($sales_id);
                    $this->cashier_model->delete_trans_sales_no_tax($sales_id);
                    $this->cashier_model->delete_trans_sales_zero_rated($sales_id);
                    $this->cashier_model->delete_trans_sales_local_tax($sales_id);
                    $act="update";
                    if($submit === null || $submit == 0 || $submit == null)
                        site_alert('Transaction Updated.','success');
                }
                else{
                    $sales_id = $this->cashier_model->add_trans_sales($trans_sales);
                    $this->logs_model->add_logs('Sales Order',$user['id'],$user['full_name']." Added New Sales Order #".$sales_id,$sales_id);
                    $act="add";
                }
                #save sa trans_sales_menus
                $trans_sales_menu = array();
                $trans_sales_items = array();
                foreach ($trans_cart as $trans_id => $v) {
                    $remarks = null;
                    if(isset($v['remarks']) && $v['remarks'] != ""){
                        $remarks = $v['remarks'];
                    }
                    $kitchen_slip_printed=0;
                    if(isset($v['kitchen_slip_printed']) && $v['kitchen_slip_printed'] != ""){
                        $kitchen_slip_printed = $v['kitchen_slip_printed'];
                    }
                    if(!isset($v['retail'])){
                        $trans_sales_menu[] = array(
                            "sales_id" => $sales_id,
                            "line_id" => $trans_id,
                            "menu_id" => $v['menu_id'],
                            "price" => $v['cost'],
                            "qty" => $v['qty'],
                            "no_tax" => $v['no_tax'],
                            "discount"=> 0,
                            "remarks"=>$remarks,
                            "kitchen_slip_printed"=>$kitchen_slip_printed
                        );
                    }
                    else{
                        $trans_sales_items[] = array(
                            "sales_id" => $sales_id,
                            "line_id" => $trans_id,
                            "item_id" => $v['menu_id'],
                            "price" => $v['cost'],
                            "qty" => $v['qty'],
                            "no_tax" => $v['no_tax'],
                            "discount"=> 0,
                            "remarks"=>$remarks
                        );
                    }
                }
                if(count($trans_sales_menu) > 0)
                    $this->cashier_model->add_trans_sales_menus($trans_sales_menu);
                
                if(count($trans_sales_items) > 0)
                    $this->cashier_model->add_trans_sales_items($trans_sales_items);
                #save sa trans_sales_menu_modifiers
                if(count($trans_mod_cart) > 0){
                    $trans_sales_menu_modifiers = array();
                    foreach ($trans_mod_cart as $trans_mod_id => $m) {
                        $kitchen_slip_printed=0;
                        if(isset($m['kitchen_slip_printed']) && $m['kitchen_slip_printed'] != ""){
                            $kitchen_slip_printed = $m['kitchen_slip_printed'];
                        }
                        if(isset($trans_cart[$m['trans_id']])){
                            $trans_sales_menu_modifiers[] = array(
                                "sales_id" => $sales_id,
                                "line_id" => $m['trans_id'],
                                "menu_id" => $m['menu_id'],
                                "mod_group_id" => $m['mod_group_id'],
                                "mod_id" => $m['mod_id'],
                                "price" => $m['cost'],
                                "qty" => $m['qty'],
                                "discount"=> 0,
                                "kitchen_slip_printed"=>$kitchen_slip_printed
                            );
                        }
                    }
                    if(count($trans_sales_menu_modifiers) > 0)
                        $this->cashier_model->add_trans_sales_menu_modifiers($trans_sales_menu_modifiers);
                }
                #save sa trans_sales_discounts
                if(count($trans_disc_cart) > 0){
                    $trans_sales_disc_cart = array();
                    $total = 0;
                    foreach ($trans_cart as $trans_id => $trans){
                        if(isset($trans['cost']))
                            $cost = $trans['cost'];
                        if(isset($trans['price']))
                            $cost = $trans['price'];

                        if(isset($trans['modifiers'])){
                            foreach ($trans['modifiers'] as $trans_mod_id => $mod) {
                                if($trans_id == $mod['line_id'])
                                    $cost += $mod['price'];
                            }
                        }

                        else{
                            if(count($trans_mod_cart) > 0){
                                foreach ($trans_mod_cart as $trans_mod_id => $mod) {
                                    if($trans_id == $mod['trans_id'])
                                        $cost += $mod['cost'];
                                }
                            }
                        }
                        if(isset($counter['zero_rated']) && $counter['zero_rated'] == 1){
                            $rate = 1.12;
                            $cost = ($cost / $rate);
                            $zero_rated += $v['qty'] * $cost;
                        }
                        $total += $trans['qty'] * $cost;
                    }

                    foreach ($trans_disc_cart as $disc_id => $dc) {
                        $dit = "";
                        if(isset($dc['items'])){
                            foreach ($dc['items'] as $lines) {
                                $dit .= $lines.",";
                            }
                            if($dit != "")
                                $dit = substr($dit,0,-1);                        
                        }
                        

                        $discount = 0;
                        $rate = $dc['disc_rate'];
                        switch ($dc['disc_type']) {
                            case "equal":
                                $divi = $total/$dc['guest'];
                                $divi_less = $divi;
                                if($dc['no_tax'] == 1){
                                    $divi_less = ($divi / 1.12);
                                }
                                $no_persons = count($dc['persons']);
                                // foreach ($row['persons'] as $code => $per) {
                                $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $divi_less);
                                $discount = ($rate / 100) * $divi_less;
                                // }
                                // $total = ($divi * $row['guest']) - $discount;

                                break;
                            default:
                                // $no_citizens = count($dc['persons']);
                                // if($dc['no_tax'] == 1)
                                //     $total = ($total / 1.12);                     
                                // $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $total);
                                // $discount = ($rate / 100) * $total;
                                if($dc['fix'] == 0){
                                    if(DISCOUNT_NET_OF_VAT && $row['disc_code'] != DISCOUNT_NET_OF_VAT_EX){
                                        $no_citizens = count($dc['persons']);
                                        $total_net_vat = ($total / 1.12);                     
                                        foreach ($dc['persons'] as $code => $per) {
                                            $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $total_net_vat);
                                            $discount += ($rate / 100) * $total_net_vat;
                                        }
                                        $total -= $discount; 
                                    }
                                    else{
                                        $no_citizens = count($dc['persons']);
                                        if($dc['no_tax'] == 1)
                                            $total = ($total / 1.12);                     
                                        foreach ($dc['persons'] as $code => $per) {
                                            $discs[] = array('type'=>$dc['disc_code'],'amount'=>($rate / 100) * $total);
                                            $discount += ($rate / 100) * $total;
                                        }
                                        $total -= $discount;                                        
                                    }    
                                }
                                else{
                                    $discs[] = array('type'=>$dc['disc_code'],'amount'=>$rate);
                                    $discount += $rate;
                                    $total -= $discount;
                                }
                                // }    
                        }
                        foreach ($dc['persons'] as $pcode => $oper) {
                            $dcBday = null;
                            if(isset($oper['bday']) && $oper['bday'] != "")
                                $dcBday = date2Sql($oper['bday']);
                            $trans_sales_disc_cart[] = array(
                                "sales_id"=>$sales_id,
                                "disc_id"=>$dc['disc_id'],
                                "disc_code"=>$dc['disc_code'],
                                "disc_rate"=>$dc['disc_rate'],
                                "no_tax"=>$dc['no_tax'],
                                "type"=>$dc['disc_type'],
                                "name"=>$oper['name'],
                                "bday"=>$dcBday,
                                "code"=>$oper['code'],
                                "items"=>$dit,
                                "guest"=>$dc['guest'],
                                "amount"=>$discount
                            );
                        }
                    }
                    if(count($trans_sales_disc_cart) > 0)
                        $this->cashier_model->add_trans_sales_discounts($trans_sales_disc_cart);
                }
                #save sa trans_sales_charges
                $total_charge = 0;
                if(count($trans_charge_cart) > 0){
                    $trans_sales_charge_cart = array();
                    foreach ($trans_charge_cart as $charge_id => $ch) {
                        $trans_sales_charge_cart[] = array(
                            "sales_id"=>$sales_id,
                            "charge_id"=>$charge_id,
                            "charge_code"=>$ch['code'],
                            "charge_name"=>$ch['name'],
                            "rate"=>$ch['amount'],
                            "absolute"=>$ch['absolute'],
                            "amount"=>$charges[$charge_id]['amount']
                        );
                        $total_charge += $charges[$charge_id]['amount'];
                    }
                    if(count($trans_sales_charge_cart) > 0)
                        $this->cashier_model->add_trans_sales_charges($trans_sales_charge_cart);
                }
                #SAVE SA TRANS_SALES_TAX
                // $total_amount
                $tax = $this->get_tax_rates(false);
                $zero_rated = 0;
                $total = 0;
                if(count($tax) > 0){
                    $taxable_amount = 0;
                    $not_taxable_amount = 0;
                    foreach ($trans_cart as $trans_id => $v) {
                        $cost = $v['cost'];
                        if(count($trans_mod_cart) > 0){
                            foreach ($trans_mod_cart as $trans_mod_id => $m) {
                                if($trans_id == $m['trans_id']){
                                    $cost += $m['cost'];
                                }
                            }
                        }
                        if($v['no_tax'] == 0){
                            if(isset($counter['zero_rated']) && $counter['zero_rated'] == 1){
                                $rate = 1.12;
                                $cost = ($cost / $rate);
                                $zero_rated += $v['qty'] * $cost;
                            }
                            $total += $v['qty'] * $cost;

                            $taxable_amount = $total;
                            foreach ($trans_disc_cart as $disc_id => $dc) {
                                $discount = 0;
                                $rate = $dc['disc_rate'];
                                switch ($dc['disc_type']) {
                                    case "equal":
                                        $divi = $total/$dc['guest'];
                                        $no_tax_persons = count($dc['persons']);
                                        $tax_persons = abs($dc['guest'] - $no_persons);
                                        $taxable_amount = $divi * $tax_persons; 
                                        $divi_less = $divi;
                                        if($dc['no_tax'] == 1){
                                            $divi_less = ($divi / 1.12);
                                            // $discount = (($rate / 100) * $divi_less) * $no_tax_persons;
                                            $not_taxable_amount = $divi_less * $no_tax_persons;
                                        }
                                        else{
                                            $discount = (($rate / 100) * $divi) * $no_tax_persons;
                                            $taxable_amount += $divi - $discount;
                                            // $taxable_amount += $divi * $tax_persons;
                                        }                                
                                        break;
                                    default:
                                        $no_citizens = count($dc['persons']);
                                        $no_cost_total = $total;
                                        if($dc['no_tax'] == 1){
                                            $no_cost_total = $total / 1.12;
                                            // $discount = ($rate / 100) * $total;
                                            // $total_discount = $discount * $no_citizens;
                                            $taxable_amount = 0;
                                            $not_taxable_amount = $no_cost_total;
                                        }
                                        else{
                                            if($dc['fix'] == 0){
                                                if(DISCOUNT_NET_OF_VAT && $row['disc_code'] != DISCOUNT_NET_OF_VAT_EX){
                                                    $total_net_vat = $total / 1.12;
                                                    $discount = ($rate / 100) * $total_net_vat;                                                    
                                                    // $total_discount = $discount * $no_citizens;
                                                    // $taxable_amount = $total - $discount;
                                                    $taxable_amount -= $discount;
                                                    $not_taxable_amount = 0;
                                                }
                                                else{
                                                    $discount = ($rate / 100) * $total;                                                    
                                                    $total_discount = $discount * $no_citizens;
                                                    $taxable_amount -= $discount;
                                                    $not_taxable_amount = 0;
                                                }
                                            }
                                            else{
                                                $discount = $rate;
                                                $total_discount = $discount;
                                                $taxable_amount -= $discount;
                                                $not_taxable_amount = 0;                                                
                                            }
                                        }###
                                }
                            }
                        }
                        else{
                          if(isset($counter['zero_rated']) && $counter['zero_rated'] == 1){
                                $rate = 1.12;
                                $cost = ($cost / $rate);
                                $zero_rated += $v['qty'] * $cost;
                                $not_taxable_amount += $v['qty'] * $cost;
                          }
                          else{
                                $not_taxable_amount += $cost * $v['qty'];
                          }  
                        }
                        // if($v['no_tax'] == 0){
                        //     $taxable_amount += $cost * $v['qty'];
                        // }
                        // else{
                        // }
                    }
                    //remove charges
                    

                    $trans_sales_zero_rated[] = array(
                        "sales_id"=>$sales_id,
                        "amount"=>$zero_rated
                    );
                    // echo var_dump($trans_sales_zero_rated);
                    // return false;
                    if(count($trans_sales_zero_rated) > 0)
                        $this->cashier_model->add_trans_sales_zero_rated($trans_sales_zero_rated);
                    $trans_sales_no_tax[] = array(
                        "sales_id"=>$sales_id,
                        "amount"=>$not_taxable_amount
                    );
                    if(count($trans_sales_no_tax) > 0)
                        $this->cashier_model->add_trans_sales_no_tax($trans_sales_no_tax);

                    $am = $taxable_amount;
                    $trans_sales_tax = array();

                    foreach ($tax as $tax_id => $tx) {
                        $rate = ($tx['rate'] / 100);
                        $tax_value = ($am / ($rate + 1) ) * $rate;
                        // ($am / 1.12) * .12
                        $trans_sales_tax[] = array(
                            "sales_id"=>$sales_id,
                            "name"=>$tx['name'],
                            "rate"=>$tx['rate'],
                            "amount"=>$tax_value,
                        );
                        $am -= $tax_value;
                    }
                    
                    if(count($trans_sales_tax) > 0)
                        $this->cashier_model->add_trans_sales_tax($trans_sales_tax);
                }
                ### LOCAL TAX 
                if($local_tax > 0){
                    $trans_sales_local_tax[] = array(
                        "sales_id"=>$sales_id,
                        "amount"=>$local_tax
                    );
                    if(count($trans_sales_local_tax) > 0)
                        $this->cashier_model->add_trans_sales_local_tax($trans_sales_local_tax);
                }
                #print
                if ($print == "true" || $print === true){
                    // $set = $this->cashier_model->get_pos_settings();
                    // $return_print_str=false,$add_reprinted=true,$splits=null,$include_footer=true
                    // $no_prints = $set->no_of_receipt_print;
                    // $print_echo = $this->print_sales_receipt($sales_id,false,false,true,null,true,$no_prints);
                    $print_echo = $this->print_sales_receipt($sales_id,false);
                }
                if ($printKitSlip == "true" || $printKitSlip === true){
                    $pet = $this->cashier_model->get_pos_settings();
                    $kitchen_printer = $pet->kitchen_printer_name;
                    if(KITCHEN_PRINTER){
                        $kitchen_printer = KITCHEN_PRINTER;
                    }
                    if(count($reasons) > 0){
                        foreach ($reasons as $ctrr => $re) {
                            $this->cashier_model->add_reasons($re);
                        }
                        $this->print_os_removes($sales_id,$reasons);
                    }
                    if($kitchen_printer != ""){
                        $this->print_os($sales_id);            
                    }
                }
                $payment = array(
                    'sales_id'      =>  $sales_id,
                    'payment_type'  =>  'cash',
                    'amount'        =>  $total_amount,
                    'to_pay'        =>  $total_amount,
                    "user_id"       =>  $must_user_id,
                    // 'reference'     =>  null,
                    // 'card_type'     =>  null
                );
                $payment_id = $this->cashier_model->add_trans_sales_payments($payment);
                $this->finish_trans2($sales_id,true);
                site_alert('Transaction Added','success');
            }

        }    
        $this->update_tbl_activity(null,true);
        if($asJson)
            echo json_encode(array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type));
        else
            return array('error'=>$error,'act'=>$act,'id'=>$sales_id,'type'=>$type);
    }
    public function finish_trans2($sales_id=null,$move=false,$void=false){
        $this->load->model('dine/cashier_model');
        $this->load->model('dine/items_model');
        $this->load->model('core/trans_model');
        $loc_id = 1;
        $trans_type = SALES_TRANS;
        if($void)
        $trans_type = SALES_VOID_TRANS;
        // $ref = $this->trans_model->get_next_ref($trans_type);
        $this->trans_model->db->trans_start();
        // $this->trans_model->save_ref($trans_type,$ref);
        $this->cashier_model->update_trans_sales(array('paid'=>1),$sales_id);
        $log_user = $this->session->userdata('user');
        $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Settled Payment on Sales Order #".$sales_id,$sales_id);
        if($move || $move == "true"){
            $opts = array(
                "type_id" => $trans_type,
                "trans_id" => $sales_id,
                "trans_ref" => '0000',
            );
            if($void)
                $rrr = true;
            else
                $rrr = false;
            $items = $this->order_items_used($sales_id,$rrr);
            if(count($items) > 0 )
                $this->items_model->move_items($loc_id,$items,$opts);
        }
        $this->update_tbl_activity(0,true);   
        $this->trans_model->db->trans_complete();
    }
    public function find_missing(){
        $ref = '000096646';
        for ($i=0; $i <= 186; $i++) { 
            if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1) 
            {
                list($all, $prefix, $number, $postfix) = $result;
                $dig_count = strlen($number); // How many digits? eg. 0003 = 4
                $fmt = '%0' . $dig_count . 'd'; // Make a format string - leading zeroes
                $nextval =  sprintf($fmt, intval($number + 1)); // Add one on, and put prefix back on

                $new_ref=$prefix.$nextval.$postfix;
            }
            else 
                $new_ref=$ref;
            $res = $this->site_model->get_tbl('trans_sales',array('trans_ref ='=>$new_ref));
            if(count($res) == 0){
                echo "MISSING - ".$new_ref."<br>";
            }
            echo $new_ref."<br>";
            $ref = $new_ref;

        }
    }

    function credit_no_fix(){
        // $this->load->model('dine/cashier_model');
        $str = $this->input->post('str');
        $cut = $this->input->post('cut');
        // echo $str;
        // die();

        $exp = explode($cut,$str);
        $num = $exp[0];
        $new_num = substr($num, 2);

        echo $new_num;
    }

    public function check_payment($sales_id=null){
        $this->load->model('dine/cashier_model');
        $order = $this->get_order_header(false,$sales_id);
        $error = "";
        if($order['paid'] == 1){
            $error = "paid";
            echo json_encode(array('error'=>$error));
        }else{
            echo json_encode(array('error'=>$error));
        }
    }
    public function print_sales_receipt_ejournal($sales_id=null,$asJson=true,$return_print_str=false,$add_reprinted=true,$splits=null,$include_footer=true,$no_prints=1,$order_slip_prints=0,$approved_by=null,$main_db=false,$openDrawer=false,$branch_code="",$brand=""){
            if($main_db){
                $this->db = $this->load->database('main', TRUE);
            }
            $branch = $this->get_branch_details(false,$branch_code);
            $return = $this->get_orderss(false,$sales_id,$branch_code,$brand);
            $orders = $return['order'];
            // echo "<pre>",print_r($orders),"</pre>";die();
            $details = $return['details'];
            $payments = $return['payments'];
            $discounts = $return['discounts'];
            $local_tax = $return['local_tax'];
            $charges = $return['charges'];
            $tax = $return['taxes'];
            $no_tax = $return['no_tax'];
            $zero_rated = $return['zero_rated'];
            $totalsss = $this->total_trans(false,$details,$discounts);
            $discs = $totalsss['discs'];
            $print_str = "\r\n";

            $brands = $this->setup_model->get_brands(null,$branch_code);

            if(count($brands) > 1){
                $brd = $this->setup_model->get_brands($brands[0]->id);

                $print_str .= $this->align_center($brd[0]->brand_name,PAPER_WIDTH," ")."\r\n";
                $print_str .= $this->align_center($brd[0]->brand_name,PAPER_WIDTH," ")."\r\n";
            }else{

                $wrap = wordwrap($branch['name'],PAPER_WIDTH,"|#|");
                $exp = explode("|#|", $wrap);
                foreach ($exp as $v) {
                    $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
                }

                $wrap = wordwrap($branch['desc'],PAPER_WIDTH,"|#|");
                $exp = explode("|#|", $wrap);

                foreach ($exp as $v) {
                    $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
                }
            }
            

            $wrap = wordwrap($branch['address'],PAPER_WIDTH,"|#|");
            $exp = explode("|#|", $wrap);
            foreach ($exp as $v) {
                $print_str .= $this->align_center($v,PAPER_WIDTH," ")."\r\n";
            }

            if($branch['tin'] != ""){
                // $print_str .= $this->append_chars('VAT REG TIN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['tin'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
                $print_str .= $this->append_chars('VAT REG TIN:'.$branch['tin'],"right",PAPER_WIDTH," ")."\r\n";
            }
            // if($branch['accrdn'] != ""){
            //     $print_str .= $this->append_chars('ACCRDN:',"right",PAPER_DET_SUBCOL," ").$this->append_chars($branch['accrdn'],"right",PAPER_TOTAL_COL_1," ")."\r\n";
            // }
            if($branch['machine_no'] != ""){
                $print_str .= $this->append_chars('MIN:'.$branch['machine_no'],"right",PAPER_WIDTH," ")."\r\n";
                // $print_str .= $this->append_chars('MIN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['machine_no'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
            }
            if($branch['permit_no'] != ""){
                $print_str .= $this->append_chars('PERMIT:'.$branch['permit_no'],"right",PAPER_WIDTH," ")."\r\n";
                // $print_str .= $this->append_chars('PERMIT:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['permit_no'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
            }
            if($branch['serial'] != ""){
                $print_str .= $this->append_chars('SN:'.$branch['serial'],"right",PAPER_WIDTH," ")."\r\n";
                // $print_str .= $this->append_chars('SN:',"right",PAPER_RECEIPT_TEXT," ").$this->append_chars($branch['serial'],"right",PAPER_RECEIPT_INPUT," ")."\r\n";
            }
            // $print_str .= "\r\n";
            foreach ($orders as $order) {
                # code...
          
                if (!empty($order['void_ref']) || $order['inactive'] == 1) {
                    $print_str .= $this->align_center("***** VOIDED TRANSACTION *****",PAPER_WIDTH," ")."\r\n";
                    $print_str .= $order['reason']."\r\n\r\n";
                }
                $header_print_str = $print_str;
                $header_print_str .= PAPER_LINE."\r\n";
                     if (!empty($payments)){
                        $header_print_str .= "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                            // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
                    }
                    else{
                        $header_print_str .= "Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
                            // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
                    }
                $header_print_str .= PAPER_LINE."\r\n";

                // $print_str .= $this->align_center(date('Y-m-d H:i:s',strtotime($order['datetime']))." ".$order['terminal_name']." ".$order['name'],42," ")."\r\n";



                $print_str .= $this->append_chars(ucwords($order['name']),"right",PAPER_RD_COL_MID," ").$this->append_chars(date('Y-m-d H:i:s',strtotime($order['datetime'])),"left",PAPER_TOTAL_COL_2," ")."\r\n"
                    // ."Terminal ID : ".$order['terminal_code']."\r\n"
                    .PAPER_LINE."\r\n";

                if (!empty($payments)){
                    $print_str .= "Receipt # ".$order['ref']." - ".strtoupper($order['type'])."\r\n";
                        // $this->align_center("Receipt # ".$order['ref']." - ".strtoupper($order['type']),42," ")."\r\n";
                }
                else{
                    $print_str .= "Reference # ".$order['sales_id']." - ".strtoupper($order['type'])."\r\n";
                        // $this->align_center(strtoupper($order['type'])." # ".$order['sales_id'],42," ")."\r\n";
                }

                if($order['waiter_id'] != ""){
                    $print_str .= "FS - ".ucwords(strtolower($order['waiter_name']))."\r\n";
                }

                $orddetails = "";
                if($order['table_id'] != "" || $order['table_id'] != 0)
                    $orddetails .= $order['table_name']." ";

                if($order['guest'] != 0)
                    $orddetails .= "Guest #".$order['guest'];

                if($order['serve_no'] != 0)
                    $orddetails .= "Serve #".$order['serve_no'];

                if($orddetails != "")
                    $print_str .= $this->align_center($orddetails,PAPER_WIDTH," ")."\r\n";
                

                $log_user = $this->session->userdata('user');
                if (!empty($payments)) {
                    if($add_reprinted){
                        if($order['printed'] >= 1){
                            $print_str .= $this->align_center('[REPRINTED]',PAPER_WIDTH," ")."\r\n";
                            $this->cashier_model->update_trans_sales(array('printed'=>$order['printed']+1),$order['sales_id']);
                            $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Reprinted Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                            
                        }
                        else{
                            $this->cashier_model->update_trans_sales(array('printed'=>1,'billed'=>1),$order['sales_id']);
                         
                          
                            if(!$return_print_str){
                                $log_id =  $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                            }
                        }
                    }
                    else{
                        $this->cashier_model->update_trans_sales(array('printed'=>1,'billed'=>1),$order['sales_id']);
                        if(!$return_print_str){
                             $log_id = $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Receipt on Sales Order #".$order['sales_id']." Reference #".$order['ref'],$order['sales_id']);
                        }
                    }
                }
                else{
                    $this->cashier_model->update_trans_sales(array('billed'=>1),$order['sales_id']);
                     $log_id =  $this->logs_model->add_logs('Sales Order',$log_user['id'],$log_user['full_name']." Printed Billing on Sales Order #".$order['sales_id'],$order['sales_id']);
                }

                if(LOCALSYNC){
                    if(isset($log_id)){
                        $this->sync_model->add_logs($log_id);
                    }

                    $this->sync_model->update_trans_sales($sales_id);

                }

                if($order['customer_id'] != ""){
                    if($main_db){
                        $this->db = $this->load->database('default', TRUE);
                    }
                    $this->load->model('dine/customers_model');
                    $customers = $this->customers_model->get_customer($order['customer_id']);
                    if(count($customers) > 0){
                        $cust = $customers[0];
                        $name = strtolower($cust->fname." ".$cust->mname." ".$cust->lname." ".$cust->suffix);
                        $print_str .= "Customer : ".$this->append_chars(ucwords($name),"right",19," ")."\r\n";
                        if($order['type'] == 'delivery'){
                            $print_str .= "Contact  : ".$this->append_chars(ucwords($cust->phone),"right",19," ")."\r\n";
                            $address = strtolower($cust->street_no." ".$cust->street_address." ".$cust->city." ".$cust->region." ".$cust->zip);
                            $print_str .= "Address  : ".$this->append_chars(ucwords($address),"right",19," ")."\r\n";
                        }
                    }

                    if($main_db){
                        $this->db = $this->load->database('main', TRUE);
                    }
                }
                if($order['type'] == 'delivery' && $order['customer_id'] != ""){
                
                }


                $print_str .= $this->append_chars("","right",PAPER_WIDTH,"=")."\r\n";

                $pre_total = 0;
                $post_details = array();
                $discs_items = array();
                foreach ($discs as $disc) {
                    if(isset($disc['items']))
                        $discs_items[$disc['type']] = $disc['items'];
                }

                $dscTxt = array();
                foreach ($details as $line_id => $val) {
                    foreach ($discs_items as $type => $dissss) {
                        if(in_array($line_id, $dissss)){
                            $qty = 1;
                            if(isset($dscTxt[$val['menu_id']][$type]['qty'])){
                                $qty = $dscTxt[$val['menu_id']][$type]['qty'] + 1;
                            }
                            $dscTxt[$val['menu_id']][$type] = array('txt' => '#'.$type,'qty' => $qty);
                        }
                    }
                }

                // foreach ($details as $line_id => $val) {
                //     if (!isset($post_details[$val['menu_id']])) {
                //         $dscsacs = array();
                //         if(isset($dscTxt[$val['menu_id']])){
                //             $dscsacs = $dscTxt[$val['menu_id']];
                //         }
                //         $remarksArr = array();
                //         if($val['remarks'] != '')
                //             $remarksArr = array($val['remarks']." x ".$val['qty']);
                //         $post_details[$val['menu_id']] = array(
                //             'name' => $val['name'],
                //             'code' => $val['code'],
                //             'price' => $val['price'],
                //             'no_tax' => $val['no_tax'],
                //             'discount' => $val['discount'],
                //             'qty' => $val['qty'],
                //             'discounted'=>$dscsacs,
                //             'remarks'=>$remarksArr,
                //             'modifiers' => array()
                //         );
                //     } else {
                //         if($val['remarks'] != "")
                //             $post_details[$val['menu_id']]['remarks'][]= $val['remarks']." x ".$val['qty'];
                //         $post_details[$val['menu_id']]['qty'] += $val['qty'];
                //     }

                //     if (empty($val['modifiers']))
                //         continue;

                //     $modifs = $val['modifiers'];
                //     $n_modifiers = $post_details[$val['menu_id']]['modifiers'];
                //     foreach ($modifs as $vv) {
                //         if (!isset($n_modifiers[$vv['id']])) {
                //             $n_modifiers[$vv['id']] = array(
                //                 'name' => $vv['name'],
                //                 'price' => $vv['price'],
                //                 'qty' => $val['qty'],
                //                 'discount' => $vv['discount']
                //             );
                //         } else {
                //             $n_modifiers[$vv['id']]['qty'] += $val['qty'];
                //         }
                //     }
                //     $post_details[$val['menu_id']]['modifiers'] = $n_modifiers;
                // }
                /* END NEW BLOCK */
                $tot_qty = 0;
                // foreach ($post_details as $val) {
                //     $tot_qty += $val['qty'];
                //     $print_str .= $this->append_chars($val['qty'],"right",PAPER_DET_COL_1," ");

                //     $len = strlen($val['name']);

                //     if($val['qty'] == 1){
                //         $lgth = 21;
                //     }else{
                //         $lgth = 16;
                //     }

                //     if($len > $lgth){
                //         $arr2 = str_split($val['name'], $lgth);
                //         $counter = 1;
                //         foreach($arr2 as $k => $vv){
                //             if($counter == 1){
                //                 if ($val['qty'] == 1) {
                //                     $print_str .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").
                //                         $this->append_chars(number_format($val['price'],2),"left",PAPER_DET_COL_3," ")."\r\n";
                //                 } else {
                //                     $print_str .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
                //                         $this->append_chars(number_format($val['price'] * $val['qty'],2),"left",PAPER_DET_COL_3," ")."\r\n";
                //                 }
                //             }else{
                //                 // if ($val['qty'] == 1) {
                //                     $print_str .= $this->append_chars("","right",PAPER_DET_COL_1," ");
                //                     $print_str .= $this->append_chars(substrwords($vv,100,""),"right",PAPER_DET_COL_2," ").
                //                         $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                //                 // } else {
                //                     // $print_str .= $this->append_chars(substrwords($vv,100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
                //                     //     $this->append_chars("","left",PAPER_DET_COL_3," ")."\r\n";
                //                 // }
                //             }
                //             $counter++;
                //         }
                        
                //         if ($val['qty'] == 1) {
                //             $pre_total += $val['price'];
                //         }else{
                //             $pre_total += $val['price'] * $val['qty'];
                //         }
                //     }else{
                //         if ($val['qty'] == 1) {
                //             $print_str .= $this->append_chars(substrwords($val['name'],100,""),"right",PAPER_DET_COL_2," ").
                //                 $this->append_chars(number_format($val['price'],2),"left",PAPER_DET_COL_3," ")."\r\n";
                //             $pre_total += $val['price'];
                //         } else {
                //             $print_str .= $this->append_chars(substrwords($val['name'],100,"")." @ ".$val['price'],"right",PAPER_DET_COL_2," ").
                //                 $this->append_chars(number_format($val['price'] * $val['qty'],2),"left",PAPER_DET_COL_3," ")."\r\n";
                //             $pre_total += $val['price'] * $val['qty'];
                //         }
                //     }
                //     if(count($val['discounted']) > 0){
                //         foreach ($val['discounted'] as $dssstxt) {
                //           $print_str .= "      ";
                //           $print_str .= $this->append_chars($dssstxt['txt']." x ".$dssstxt['qty'],"right",PAPER_DET_COL_2," ")."\r\n";
                //         }
                //     }
                //     if(isset($val['remarks']) && count($val['remarks']) > 0){
                //         foreach ($val['remarks'] as $rmrktxt) {
                //             $print_str .= "     * ";
                //             $print_str .= $this->append_chars(ucwords($rmrktxt),"right",PAPER_DET_COL_2," ")."\r\n";
                //         }
                //     }

                //     if (empty($val['modifiers']))
                //         continue;

                //     $modifs = $val['modifiers'];
                //     foreach ($modifs as $vv) {
                //         $print_str .= "   * ".$vv['qty']." ";

                //         if ($vv['qty'] == 1) {
                //             $print_str .= $this->append_chars(substrwords($vv['name'],100,""),"right",PAPER_DET_SUBCOL," ")
                //                 .$this->append_chars(number_format($vv['price'],2),"left",PAPER_DET_COL_3," ")."\r\n";
                //             $pre_total += $vv['price'];
                //         } else {
                //             $print_str .= $this->append_chars(substrwords($vv['name'],100,"")." @ ".$vv['price'],"right",PAPER_DET_SUBCOL," ")
                //                 .$this->append_chars(number_format($vv['price'] * $vv['qty'],2),"left",PAPER_DET_COL_3," ")."\r\n";
                //             $pre_total += $vv['price'] * $vv['qty'];
                //         }
                //     }
                    


                //     //DISCOUNT PALATANDAAN
                //     // if(in_array($val[''], haystack))

                // }

                $print_str .= $this->append_chars("","right",PAPER_WIDTH,"=");

                // $vat = round($order['amount'] / (1 + BASE_TAX) * BASE_TAX,1);
                $vat = 0;
                if($tax > 0){
                    foreach ($tax as $tx) {
                       $vat += $tx['amount'];
                    }
                }
                $no_tax_amt = 0;
                foreach ($no_tax as $k=>$v) {
                    $no_tax_amt += $v['amount'];
                }

                $zero_rated_amt = 0;
                foreach ($zero_rated as $k=>$v) {
                    $zero_rated_amt += $v['amount'];
                }
                if($zero_rated_amt > 0){
                    $no_tax_amt = 0;
                }

                $print_str .= "\r\n".$this->append_chars(ucwords("TOTAL"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(($pre_total),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= $this->append_chars(ucwords("TOTAL QTY"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format(($tot_qty),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // if(count($discs) > 0){
                //     foreach ($discs as $ds) {
                //         $print_str .= "\r\n".$this->append_chars(strtoupper($ds['type']),"right",28," ").$this->append_chars("P (".number_format($ds['amount'],2).")","left",10," ")."\r\n";
                //     }
                // }
                // $print_str .= "\r\n";
                $total_discounts = 0;
                $total_discounts_sm = 0;
                foreach ($discounts as $dcs_ci => $dcs) {
                    foreach ($dcs['persons'] as $code => $dcp) {
                        // $print_str .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                        // $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                        // $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                        // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                        $total_discounts += $dcp['amount'];
                        $dcAmt = $dcp['amount'];
                        // if(MALL_ENABLED && MALL == 'megamall'){
                        //     if($dcs_ci == PWDDISC){
                        //         $dcAmt = $dcAmt / 1.12;       
                        //     }
                        // }
                        $total_discounts_sm += $dcAmt;
                    }
                }
                $total_discounts_non_vat = 0;
                foreach ($discounts as $dcs_ci => $dcs) {
                   
                    foreach ($dcs['persons'] as $code => $dcp) {
                        // $print_str .= $this->append_chars($dcs_ci,"right",28," ").$this->append_chars('P'.num($dcp['amount']),"left",10," ");
                        // $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",28," ");
                        // $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",28," ")."\r\n";
                        // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                        if($dcs['no_tax'] == 1){
                            $total_discounts_non_vat += $dcp['amount'];
                        }
                    }
                }
                $total_charges = 0;
                if(count($charges) > 0){
                    foreach ($charges as $charge_id => $opt) {
                        $total_charges += $opt['total_amount'];
                    }
                }
                $local_tax_amt = 0;
                if(count($local_tax) > 0){
                    foreach ($local_tax as $lt_id => $lt) {
                        $local_tax_amt += $lt['amount'];
                    }
                }
                // echo num($total_charges + $local_tax_amt);

                // echo '((('.$order['amount'].' - ('.$total_charges.' + '.$local_tax_amt.') - '.$vat.') - '.$no_tax_amt.'+'.$total_discounts_non_vat.') -'.$zero_rated_amt;


                $vat_sales = ( ( ( $order['amount'] - ($total_charges + $local_tax_amt) ) - $vat)  - $no_tax_amt + $total_discounts_non_vat ) - $zero_rated_amt;

                // echo '===== '.$vat_sales;
                // $vat_sales = ( ( ( $order['amount'] ) - $vat)  - $no_tax_amt + $total_discounts) - $zero_rated_amt;
                // echo "vat_sales= ((".$order['amount']." - ".$total_charges."))- ".$vat." )- ".$no_tax_amt." + ".$total_discounts." - ".$zero_rated_amt;
                if($vat_sales < 0){
                    $vat_sales = 0;
                }
                $print_str .= "\r\n".$this->append_chars(ucwords("VAT SALES"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(num($order['amount']),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= $this->append_chars(ucwords("VAT EXEMPT SALES"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($no_tax_amt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= $this->append_chars(ucwords("VAT ZERO RATED"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($zero_rated_amt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                
                
                
                #CONDITION TO NA PINADAGDAG NG TAGAYTAY - FOR SENIOR CITEZEN VIEW VAT PLUS DISCOUNT
                // if(count($discounts) >0){
                //     if(count($dcs['persons']) > 0){
                //         $print_str .= $this->append_chars(ucwords("Less VAT (12%)"),"right",28," ").$this->append_chars("(".number_format($pre_total - $no_tax_amt,2).")","left",10," ")."\r\n";
                //     }
                // }
                // else{
                //     if($tax > 0){
                //         foreach ($tax as $tx) {
                //            $print_str .= $this->append_chars($tx['name']."(".$tx['rate']."%)","right",28," ").$this->append_chars(number_format($tx['amount'],2),"left",10," ")."\r\n";
                //         }
                //     }
                // }


                #CONDITION TO NA PARA SA TAGUEGARAO
                // if($tax > 0){
                //     foreach ($tax as $tx) {
                //        $print_str .= $this->append_chars($tx['name']."(".$tx['rate']."%)","right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($tx['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //     }
                // }
                // if(count($local_tax) > 0){
                //     $local_tax_amt = 0;
                //     foreach ($local_tax as $lt_id => $lt) {
                //         $local_tax_amt += $lt['amount'];
                //     }
                //     $print_str .= $this->append_chars(ucwords("LOCAL TAX"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($local_tax_amt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // }
                // if(count($discounts) >0){
                //     $hasSMPWD = false;
                //     if(count($dcs['persons']) > 0){
                //         $print_str .= "\r\n";
                //         $print_str .= PAPER_LINE."\r\n";
                //         $print_str .= "          Discount Details"."\r\n";
                //         $print_str .= PAPER_LINE."\r\n";

                //         foreach ($discounts as $dcs_ci => $dcs) {
                //             foreach ($dcs['persons'] as $code => $dcp) {
                //                 $discRateTxt = " (".$dcp['disc_rate']."%)";
                //                 if($dcs['fix'] == 1){
                //                     $discRateTxt = " (".$dcp['disc_rate'].")";
                //                 }
                //                 $dcAmt = $dcp['amount'];
                //                 // if(MALL_ENABLED && MALL == 'megamall'){
                //                 //     if($dcs_ci == PWDDISC){
                //                 //         $dcAmt = $dcAmt / 1.12; 
                //                 //         $hasSMPWD = true;      
                //                 //     }
                //                 // }
                //                 $print_str .= $this->append_chars($dcs_ci.$discRateTxt,"right",PAPER_TOTAL_COL_1," ").$this->append_chars('P'.num($dcAmt),"left",PAPER_TOTAL_COL_2," ");
                //                 $print_str .= "\r\n".$this->append_chars($dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                //                 $print_str .= "\r\n".$this->append_chars($dcp['code'],"right",PAPER_TOTAL_COL_1," ")."\r\n";
                //             }
                //         }
                //         // $print_str .= "\r\n";
                //         // echo $pre_total." - ".$order['amount']." - ".$total_charges." - ".$total_discounts; die();
                //         $less_vat = ($pre_total - ($order['amount'] - $total_charges + $local_tax_amt ) ) - $total_discounts;
                //         // $less_vat = ($pre_total - ($order['amount'] - num($total_charges + $local_tax_amt) ) ) - $total_discounts;

                //         // $print_str .= $this->append_chars(ucwords("Total Discount"),"right",28," ").$this->append_chars(number_format($total_discounts,2),"left",10," ")."\r\n";
                //         $print_str .= $this->append_chars(ucwords("Total Less VAT"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //         if(MALL_ENABLED && MALL == 'megamall' && $hasSMPWD){
                //             $print_str .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts_sm + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //         }
                //         else{
                //             $print_str .= $this->append_chars(ucwords("Total Amount Discounted"),"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format( ($total_discounts + $less_vat),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //         }
                //     }
                // }

                // if(count($charges) > 0){
                //     $print_str .= "\r\n";
                //     $print_str .= PAPER_LINE."\r\n";
                //     $print_str .= "              CHARGES"."\r\n";
                //     $print_str .= PAPER_LINE."\r\n";
                //     foreach ($charges as $charge_id => $opt) {
                //         $charge_amount = $opt['total_amount'];
                //         // if($opt['absolute'] == 0){
                //         //     $charge_amount = ($opt['amount'] / 100) * ($order['amount'] - $vat);
                //         // }
                //         $print_str .= $this->append_chars($opt['name'],"right",PAPER_TOTAL_COL_1," ").$this->append_chars(number_format($charge_amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //     }
                //     $print_str .= PAPER_LINE."\r\n";
                // }

                if (!empty($payments)) {

                    $print_str .= "\r\n";
                    $print_str .= $this->append_chars("Amount due","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                    $print_str .= "\r\n"."================================="."\r\n";
                    $pay_total = 0;
                    $gft_ctr = 0;
                    $nor_ctr = 0;
                    // foreach ($payments as $payment_id => $opt) {

                    //     $print_str .= $this->append_chars(ucwords($opt['payment_type']),"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($opt['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        
                    //     if($opt['payment_type'] == 'check'){
                    //         $print_str .= $this->append_chars("     Check # ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                    //     }else{
                    //         if (!empty($opt['reference']) && $opt['payment_type'] != 'deposit') {
                    //             $print_str .= $this->append_chars("     Reference ".$opt['reference'],"right",PAPER_WIDTH," ")."\r\n";
                    //         }
                    //     }

                    //     if($opt['payment_type'] == 'foodpanda'){
                    //         if (!empty($opt['approval_code']))
                    //                 $print_str .= $this->append_chars("  Order Code: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                    //     }else if($opt['payment_type'] == 'check'){
                    //         $print_str .= $this->append_chars("     Bank: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                    //     }else{
                    //         if (!empty($opt['card_number'])) {
                    //             $print_str .= $this->append_chars("  Card #: ".$opt['card_number'],"right",PAPER_WIDTH," ")."\r\n";
                    //             if (!empty($opt['approval_code']))
                    //                 $print_str .= $this->append_chars("  Approval #: ".$opt['approval_code'],"right",PAPER_WIDTH," ")."\r\n";
                    //         }
                    //     }
                    //     $pay_total += $opt['amount'];
                    //     if($opt['payment_type'] == 'gc'){
                    //         $gft_ctr++;
                    //     }
                    //     else
                    //         $nor_ctr++;
                        
                    // }
                    // if($gft_ctr == 1 && $nor_ctr == 0)
                    //     $print_str .= $this->append_chars("Change","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format(0,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    // else
                    //     $print_str .= $this->append_chars("Change","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($pay_total - $order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    // $print_str .= PAPER_LINE."\r\n";
                    // if ($include_footer) {
                    //     $rec_footer = "";
                    //     if($branch['rec_footer'] != ""){
                    //         $wrap = str_replace ("<br>","\r\n", $branch['rec_footer'] );
                    //         $exp = explode("\r\n", $wrap);
                    //         foreach ($exp as $v) {
                    //             $wrap2 = wordwrap($v,35,"|#|");
                    //             $exp2 = explode("|#|", $wrap2);  
                    //             foreach ($exp2 as $v2) {
                    //                 $rec_footer .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                    //             }
                    //         }                        
                    //     }    
                    //     $print_str .= $rec_footer;
                    //     if($branch['contact_no'] != ""){
                    //         $print_str .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
                    //                      .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
                    //     }
                    //     if($branch['email'] != ""){
                    //         $print_str .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
                    //                    .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
                    //     }
                    //     if($branch['website'] != ""){
                    //         $print_str .= $this->align_center("Please visit us at",PAPER_WIDTH," ")."\r\n"
                    //                      .$this->align_center($branch['website'],PAPER_WIDTH," ")."\r\n";
                    //     }
                    //     $print_str .= PAPER_LINE."\r\n";
                    //     // $print_str .= "\r\n";
                    //     $print_str .= $this->append_chars('Name:',"right",PAPER_RECEIPT_TEXT," ")
                    //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //     $print_str .= $this->append_chars('Company:',"right",PAPER_RECEIPT_TEXT," ")
                    //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //     $print_str .= $this->append_chars('Address:',"right",PAPER_RECEIPT_TEXT," ")
                    //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n"."\r\n";
                    //     $print_str .= $this->append_chars('TIN:',"right",PAPER_RECEIPT_TEXT," ")
                    //                .  $this->append_chars('',"right",PAPER_RECEIPT_INPUT,"_")."\r\n";
                    //     // $print_str .= "\r\n";
                    //     // $print_str .= "\r\n";
                    //     $print_str .= PAPER_LINE."\r\n";
                    //     $print_str .= $this->align_center("POS Vendor Details",PAPER_WIDTH," ")."\r\n";
                    //     $print_str .= PAPER_LINE."\r\n";
                    //     $print_str .= $this->align_center("PointOne Integrated Tech., Inc.",PAPER_WIDTH," ")."\r\n";
                    //     $print_str .= $this->align_center("1409 Prestige Tower",PAPER_WIDTH," ")."\r\n";
                    //     $print_str .= $this->align_center("Ortigas Center, Pasig City",PAPER_WIDTH," ")."\r\n";
                    //     $print_str .= $this->append_chars('POS Version:',"right",PAPER_RECEIPT_TEXT_FT," ")
                    //                .  $this->append_chars('iPos ver 1.0',"right",PAPER_RECEIPT_INPUT_FT," ")."\r\n";
                    //     $print_str .= $this->append_chars('TIN:',"right",PAPER_RECEIPT_TEXT_FT," ")
                    //                .  $this->append_chars('008543444-000',"right",PAPER_RECEIPT_INPUT_FT," ")."\r\n";
                    //     $print_str .= $this->append_chars('Date Issued:',"right",PAPER_RECEIPT_TEXT_FT," ")
                    //                .  $this->append_chars('December 22, 2014',"right",PAPER_RECEIPT_INPUT_FT," ")."\r\n";
                    //                // .  $this->append_chars(date2Word($order['datetime']),"right",PAPER_TOTAL_COL_2," ")."\r\n";
                    //     $print_str .= $this->append_chars('Valid Until:',"right",PAPER_RECEIPT_TEXT_FT," ")
                    //                .  $this->append_chars(date2Word('December 22, 2019'),"right",PAPER_RECEIPT_INPUT_FT," ")."\r\n";
                    //                // .  $this->append_chars(date2Word( date('Y-m-d',strtotime($order['datetime'].' +5 year')) ),"right",PAPER_TOTAL_COL_2," ")."\r\n";
                    //     if($branch['accrdn'] != ""){
                    //         $print_str .= $this->align_center('ACCRDN:',PAPER_WIDTH," ")."\r\n".$this->align_center($branch['accrdn'],PAPER_WIDTH," ")."\r\n";
                    //     }
                    //     // $print_str .= $this->align_center('This Invoice/Receipt',PAPER_WIDTH," ")."\r\n";
                    //     // $print_str .= $this->align_center('Shall be valid',PAPER_WIDTH," ")."\r\n";
                    //     // $print_str .= $this->align_center('Five(5) Years from the date of',PAPER_WIDTH," ")."\r\n";
                    //     // $print_str .= $this->align_center('The Permit to use.',PAPER_WIDTH," ")."\r\n";



                    //     // if($branch['pos_footer'] != ""){
                    //     //     // $print_str .= PAPER_LINE."\r\n";
                    //     //     $print_str .= "\r\n";
                    //     //     $wrap = str_replace ("<br>","\r\n", $branch['pos_footer'] );
                    //     //     $exp = explode("\r\n", $wrap);
                    //     //     foreach ($exp as $v) {
                    //     //         $wrap2 = wordwrap($v,35,"|#|");
                    //     //         $exp2 = explode("|#|", $wrap2);  
                    //     //         foreach ($exp2 as $v2) {
                    //     //             $print_str .= $this->align_center($v2,PAPER_WIDTH," ")."\r\n";
                    //     //         }
                    //     //     }
                    //     // }

                    // }
                } else {
                    $print_str .= "\r\n".$this->append_chars("","right",PAPER_WIDTH,"=");
                    $print_str .= "\r\n\r\n".$this->append_chars("Billing Amount","right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($order['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    // if(is_array($splits)){
                    //     $print_str .= $this->append_chars("Split Amount by ".$splits['by'],"right",PAPER_TOTAL_COL_1," ").$this->append_chars("P ".number_format($splits['total'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    // }
                    // if ($include_footer) {
                    //     // $print_str .= "\r\n\r\n";
                    //     if($branch['contact_no'] != ""){
                    //         $print_str .= $this->align_center("For feedback, please call us at",PAPER_WIDTH," ")."\r\n"
                    //                      .$this->align_center($branch['contact_no'],PAPER_WIDTH," ")."\r\n";
                    //     }
                    //     if($branch['email'] != ""){
                    //         $print_str .= $this->align_center("Or Email us at",PAPER_WIDTH," ")."\r\n" 
                    //                    .$this->align_center($branch['email'],PAPER_WIDTH," ")."\r\n";
                    //     }
                    //     if($branch['website'] != "")
                    //         $print_str .= $this->align_center("Please visit us at \r\n".$branch['website'],PAPER_WIDTH," ")."\r\n";
                    // }

                }

                // if (!empty($payments)) {
                //     $print_str .= "\r\n";
                //     foreach ($discounts as $dcs_ci => $dcs) {
                //         if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                //             $print_str .= PAPER_LINE."\r\n";
                //             $print_str .= $this->align_center("OSCA/PWD Details",PAPER_WIDTH," ")."\r\n";
                //             $print_str .= PAPER_LINE."\r\n";
                //             // $print_str .= $this->align_center(PAPER_LINE,42," ");
                //             break;
                //         }
                //     }
                //     foreach ($discounts as $dcs_ci => $dcs) {
                //         if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                //             foreach ($dcs['persons'] as $code => $dcp) {
                //                 // ."\r\n"
                //                 $print_str .= "\r\n".$this->append_chars("ID NO      : ".$dcp['code'],"right",PAPER_TOTAL_COL_1," ");
                //                 $print_str .= "\r\n".$this->append_chars("NAME       : ".$dcp['name'],"right",PAPER_TOTAL_COL_1," ");
                //                 $print_str .= "\r\n".$this->append_chars("ADDRESS    : ","right",PAPER_TOTAL_COL_1," ");
                //                 $print_str .= "\r\n".$this->append_chars("SIGNATURE  : ","right",PAPER_TOTAL_COL_1," ");
                //                 $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                //                 // $print_str .= "\r\n".$this->append_chars(asterisks($dcp['code']),"right",28," ")."\r\n";
                //             }
                //         }
                //     }
                //     foreach ($discounts as $dcs_ci => $dcs) {
                //         if($dcs_ci == 'SNDISC' || $dcs_ci == 'PWDISC'){
                //             $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                //             break;
                //         }
                //     }
                // }

                // if($approved_by != null){
                //     $app = $this->site_model->get_user_details($approved_by);
                //     $approver = $app->fname." ".$app->mname." ".$app->lname." ".$app->suffix;
                //     $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                //     $print_str .= "\r\n".$this->append_chars("Approved By : ".$approver,"right",PAPER_TOTAL_COL_1," ");
                //     $print_str .= "\r\n".$this->append_chars("             ____________________","right",PAPER_TOTAL_COL_1," ")."\r\n";
                //     $print_str .= $this->align_center(PAPER_LINE,PAPER_WIDTH," ");
                // }
              }


            if ($return_print_str) {
                return $print_str;
            }
            // echo "<pre>".$print_str."</pre>";
            // return false;

            $filename = "sales.txt";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);

            $batfile = "print.bat";
            $fh1 = fopen($batfile,'w+');
            $root = dirname(BASEPATH);
            $battxt ="NOTEPAD /P \"".realpath($root."/".$filename)."\"";

            if(BILLING_PRINTER){
                if(BILLING_PRINTER != "DEFAULT"){
                    $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".BILLING_PRINTER."\"  ";   
                }
            }

            if($openDrawer){
                $pet = $this->cashier_model->get_pos_settings();
                $open_drawer_printer = $pet->open_drawer_printer;
                if($open_drawer_printer != ""){
                    $battxt = "NOTEPAD /PT \"".realpath($root."/".$filename)."\" \"".$open_drawer_printer."\"  ";   
                }            
            }

            fwrite($fh1, $battxt);
            fclose($fh1);
            session_write_close();
            // exec($filename);
            for ($i=0; $i < $no_prints; $i++) { 
                exec($batfile);
            }
            session_start();
            unlink($filename);
            unlink($batfile);

            if($order_slip_prints > 0){
                $this->print_order_slip($header_print_str,$post_details,$order_slip_prints);            
            }

            if ($asJson)
                echo json_encode(array('msg'=>'Receipt # '.(!empty($order['ref']) ? $order['ref'] : $sales_id).' has been printed'));
            else
                return array('msg'=>'Receipt # '.(!empty($order['ref']) ? $order['ref'] : $sales_id).' has been printed');
        }
}