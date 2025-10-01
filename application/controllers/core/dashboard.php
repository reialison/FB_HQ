<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (realpath(dirname(__FILE__) . '/..')."/dine/prints.php");
class Dashboard extends Prints {
	var $data = null;
    var $user_branches = array();
    public function __construct(){
        parent::__construct();
        $this->load->helper('core/dashboard_helper');  
        $this->load->model('dine/cashier_model');
        $this->load->model('core/user_model');

        $user = $this->session->userdata('user');
        $sysid = $user['sysid'];

        $user_branch = $this->user_model->get_user_branches(null,$sysid);

        if($user_branch){
            foreach($user_branch as $branch){
                $this->user_branches[] = $branch->branch_code;
            }
        }

    }
    public function index(){
        $data = $this->syter->spawn('dashboard');
        $data['page_title'] = fa('icon-speedometer').' Monthly Sales Dashboard ';
        $today = $this->site_model->get_db_now();
        $user = $this->session->userdata('user');
        $user_id = $user['sysid'];

        // $user = $this->session->userdata('user');
        // $user['sess_id'] = '';
    
        // $this->session->set_userdata('user',$user);
        // $todayTransNo = 0;    
        // // $lastZread = $this->cashier_model->get_lastest_z_read(Z_READ,$today);
        // // $lastGT = 0;
        // // if(count($lastZread) > 0){
        // //     $lastGT = $lastZread[0]->grand_total;
        // // }
        // $lastGT = 0;
        // // $gt = $this->old_grand_net_total($today);
        // // $lastGT = $gt['true_grand_total'];
        
        // // echo '<pre>', print_r($aa), '</pre>';die();

        // $todaySales = 0;
       
        $lsu = array();
        // // echo '<pre>', print_r($lsu), '</pre>';die();
        // // echo $this->site_model->db->last_query();die();
        // // $args['']

        //  // $today = $this->site_model->get_db_now();

        // $args3 = array();
        // // $today = '2018-8-30';
        // // $todaySales = 0;
        
        $ts = array();
        $lastGT = 0;
        // $gt = $this->old_grand_net_total($today);
        // $lastGT = $gt['true_grand_total'];
        $todayTransNo = 0;        
        //  $select = 'count(sales_id) as today_no_trans';
        // $args = array();
        // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.inactive"] = 0;
        // $args["trans_sales.paid"] = 1;
        // $args["trans_sales.type_id"] = SALES_TRANS;        
        // $aa = $this->site_model->get_tbl('( select distinct(concat(branch_code,sales_id )),total_amount,branch_code,trans_ref,sales_id,inactive,paid,type_id,datetime from trans_sales )trans_sales',$args,array(),null,true,$select);
        // // echo $this->site_model->db->last_query()."<br>";        
        // if(count($aa) > 0){
        //     // $todayTransNo = (isset($aa[0]->today_sales) ? $aa[0]->today_sales : 0);
        //     $todayTransNo = $aa[0]->today_no_trans; 
        // }
        // echo '<pre>', print_r($aa), '</pre>';die();

        $todaySales = 0;
        // $select = 'sum(total_amount) as today_sales';
        // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.inactive"] = 0;
        // $args["trans_sales.paid"] = 1;
        // $args["trans_sales.type_id"] = SALES_TRANS;
        // $args["DATE(trans_sales.datetime) = '".date2Sql($today)."'"] = array('use'=>'where','val'=>null,'third'=>false);;
        // $ts = $this->site_model->get_tbl('( select distinct(concat(branch_code,sales_id )),total_amount,branch_code,trans_ref,inactive,paid,type_id,datetime from trans_sales )trans_sales',$args,array(),null,true,$select);
        // // echo $this->site_model->db->last_query();die();
        // if(count($ts) > 0){
        //     $todaySales = (isset($ts[0]->today_sales) ? $ts[0]->today_sales : 0);
        //     // $todayTransNo = $ts[0]->today_no_trans; 
        // }

        // $select = 'master_logs.master_id,MAX(`migrate_date`) as mdate,master_logs.branch_code,transaction,branch_name';
        // $args = array();
        // $args["master_logs.transaction"] = 'master_logs';
        // $args2["master_logs.transaction"] = 'trans_sales';
        // $group = 'master_logs.branch_code desc';
        // $join = array();
        // $join['branch_details'] = array('content'=>'branch_details.branch_code = master_logs.branch_code');
        // // $args["trans_sales.inactive"] = 0;
        // $lsu = $this->site_model->get_tbl('master_logs',$args,array(),$join,true,$select,$group,null,false,true,$args2);
        // echo '<pre>', print_r($lsu), '</pre>';die();
        // echo $this->site_model->db->last_query();die();
        // $args['']

         // $today = $this->site_model->get_db_now();

        // $args3 = array();
        // // $today = '2018-8-30';
        // // $todaySales = 0;
        // $select3 = 'sum(total_amount) as today_sales,trans_sales.branch_code,branch_name';
        // $args3["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args3["trans_sales.inactive"] = 0;
        // $args3["trans_sales.paid"] = 1;
        // $args3["trans_sales.type_id"] = SALES_TRANS;
        // // if($branch_code != ""){
        // // $args3["trans_sales.branch_code"] = $branch_code;            
        // // }
        // $args3["DATE(trans_sales.datetime) = '".date2Sql($today)."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $group3 = 'trans_sales.branch_code desc';
        // $join = array();
        // $join['branch_details'] = array('content'=>'branch_details.branch_code = trans_sales.branch_code');
        // $ts = $this->site_model->get_tbl('( select distinct(concat(trans_sales.branch_code,sales_id )),trans_ref,total_amount,trans_sales.branch_code,inactive,paid,type_id,datetime from trans_sales )trans_sales',$args3,array(),$join,true,$select3,$group3);
        $branch_latest_upload = array();
        $select = 'MAX(`trans_date`) as mdate,store_zread.branch_code,company_owned';
        $args = array();
        $join = array();
        $join['branch_details use index(branch_code)'] = array('content'=>'branch_details.branch_code = store_zread.branch_code');
        
        if($this->user_branches){
            $args["store_zread.branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        
        $group = 'branch_code ASC';
        $branch_latest_upload = $this->site_model->get_tbl('store_zread',$args,array(),$join,true,$select,$group);
        $c_company_owned = array();
        $count_fr = $count_co = 0;
        $args["company_owned"] = 0;
        $c_company_owned = $this->site_model->get_tbl('store_zread',$args,array(),$join,true,$select,$group);
        $count_co = count($c_company_owned);
        $args2 = array();
        if($this->user_branches){
            $args2["store_zread.branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $c_franchisee = array();
        $args2["company_owned"] = 1;
        $c_franchisee = $this->site_model->get_tbl('store_zread',$args2,array(),$join,true,$select,$group);
        $count_fr = count($c_franchisee);
        // echo '<pre>', print_r($branch_latest_upload), '</pre>';die();

        $menus = array();
        // $calendar= $this->site_model->get_db_now('sql');        
        // $curr = true;
        // $args = array();
        // if(!empty($branch_code)){
        //     $args["trans_sales.branch_code"] = array('use'=>'where','val'=>$branch_code);            
        // }

        // $args['trans_sales.datetime'] = array('use'=>'like','val'=>date('Y-m',strtotime($calendar)));
        // $trans = $this->trans_sales($args,$curr);
        // $sales = $trans['sales'];
        // $menus = $this->menu_toppings($sales['settled']['ids']);
        
        // echo "<pre>",print_r($menus),"</pre>";die();
        
        // usort($menus, function($a, $b) {
        //     return $b['qty'] - $a['qty'];
        // });
        // echo '<pre>', print_r($menus), '</pre>';die();

        // $data['code'] = dashboardMain($lastGT,$todaySales,$todayTransNo,$lsu,$ts);
        // $data['sideBarHide'] = true;
        // $data['add_css'] = array('css/morris/morris.css');
        // $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/jqueryKnob/jquery.knob.js','js/plugins/sparkline/jquery.sparkline.min.js','assets/global/plugins/counterup/jquery.waypoints.min.js','assets/global/plugins/morris/morris.min.js','js/charts-echarts.min.js','js/echarts/echarts.js','js/charts-amcharts.min.js', 'assets/global/plugins/amcharts/amcharts/amcharts.js', 'assets/global/plugins/amcharts/amcharts/pie.js', 'assets/global/plugins/amcharts/amcharts/serial.js', 
        //     'assets/global/plugins/jquery.blockui.min.js');
        // $data['page_no_padding'] = true;
        // $data['load_js'] = 'dine/dashboard.php';
        // $data['use_js'] = 'dashBoardJs';
        // $data['add_js'] = 'js/site_list_forms.js';
        // $this->load->view('page',$data);
        $data['code'] = dashboardMain($lastGT,$todaySales,$todayTransNo,$lsu,$ts,$branch_latest_upload,$menus,$count_co,$count_fr);
        $data['sideBarHide'] = true;
        $data['add_css'] = array('css/morris/morris.css','css/wowdash.css');
        $data['load_js'] = 'dine/dashboard.php';
        $data['use_js'] = 'dashBoardJs';
        $data['add_js'] = array(
        'js/plugins/morris/jquery-ui.min.js',
        'js/plugins/morris/bootstrap.bundle.min.js',
        'js/plugins/morris/apexcharts.min.js',
        'js/plugins/morris/morris.min.js',
        'js/plugins/jqueryKnob/jquery.knob.js',
        'js/plugins/sparkline/jquery.sparkline.min.js',
        'assets/global/plugins/counterup/jquery.waypoints.min.js',
        'assets/global/plugins/morris/morris.min.js',
        'js/charts-echarts.min.js','js/echarts/echarts.js',
        'js/charts-amcharts.min.js',
        'assets/global/plugins/amcharts/amcharts/amcharts.js', 
        'assets/global/plugins/amcharts/amcharts/pie.js', 
        'assets/global/plugins/amcharts/amcharts/serial.js', 
        'assets/global/plugins/jquery.blockui.min.js',
        'js/plugins/morris/dataTables.min.js',
        'js/plugins/morris/file-upload.js',
        'js/plugins/morris/iconify-icon.min.js',
        // 'js/plugins/morris/jquery-3.7.1.min.js',
        // 'js/plugins/morris/jquery-jvectormap-2.0.5.min.js',
        'js/plugins/morris/jquery-jvectormap-world-mill-en.js',
        'js/plugins/morris/homeOneChart.js',
        
        
    );
        $data['page_no_padding'] = true;
        
        // $data['add_js'] = 'js/site_list_forms.js';
        $this->load->view('page',$data);
    }

    public function get_dashboard_details(){
        $today = $this->site_model->get_db_now();
        $lastGT = 0;
        // $gt = $this->old_grand_net_total($today);
        // $lastGT = $gt['true_grand_total'];
        $todayTransNo = 0;        
         // $select = 'count(sales_id) as today_no_trans';
         $select = 'sum(invoice_count) as today_no_trans';
        $args = array();
        // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.inactive"] = 0;
        // $args["trans_sales.paid"] = 1;
        // $args["trans_sales.type_id"] = SALES_TRANS;        
        // $aa = $this->site_model->get_tbl('trans_sales use index(branch_code)',$args,array(),null,true,$select);
        $aa = $this->site_model->get_tbl('store_zread',$args,array(),null,true,$select);
        // echo $this->site_model->db->last_query()."<br>";        
        if(count($aa) > 0){
            // $todayTransNo = (isset($aa[0]->today_sales) ? $aa[0]->today_sales : 0);
            $todayTransNo = $aa[0]->today_no_trans; 
        }

        $todaySales = 0;
        // $select = 'sum(total_amount) as today_sales';
        // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.inactive"] = 0;
        // $args["trans_sales.paid"] = 1;
        // $args["trans_sales.type_id"] = SALES_TRANS;
        // $args["DATE(trans_sales.datetime) = '".date2Sql($today)."'"] = array('use'=>'where','val'=>null,'third'=>false);;
        // $ts = $this->site_model->get_tbl('trans_sales use index(branch_code)',$args,array(),null,true,$select);
        // // echo $this->site_model->db->last_query();die();
        // if(count($ts) > 0){
        //     $todaySales = (isset($ts[0]->today_sales) ? $ts[0]->today_sales : 0);
        //     // $todayTransNo = $ts[0]->today_no_trans; 
        // }

        // $select = 'MAX(`datetime`) as mdate,trans_sales.branch_code,"trans_sales" as transaction,branch_name';
        $select = 'MAX(`trans_date`) as mdate,branch_code,branch_code as branch_name';
        $args = array();
        // $args2["master_logs.transaction"] = 'trans_sales';
        $group = 'branch_code desc';
        $join = array();
        // $join['branch_details'] = array('content'=>'branch_details.branch_code = trans_sales.branch_code');
        // $lsu = $this->site_model->get_tbl('trans_sales use index(branch_code)',$args,array(),$join,true,$select,$group,null,false,true,$args2);
        $lsu = $this->site_model->get_tbl('store_zread',$args,array(),$join,true,$select,$group);
        $lsu = last_update_date($lsu);
        // echo '<pre>', print_r($lsu), '</pre>';die();
        // echo $this->site_model->db->last_query();die();
        // $args['']

         // $today = $this->site_model->get_db_now();

        $args3 = array();
        // $today = '2018-8-30';
        // $todaySales = 0;
        // $select3 = 'sum(total_amount) as today_sales,trans_sales.branch_code,branch_name';
        $select3 = 'sum(total_sales) as today_sales,branch_code,branch_code as branch_name';
        // $args3["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args3["trans_sales.inactive"] = 0;
        // $args3["trans_sales.paid"] = 1;
        // $args3["trans_sales.type_id"] = SALES_TRANS;
        // if($branch_code != ""){
        // $args3["trans_sales.branch_code"] = $branch_code;            
        // }
        $args3["DATE(trans_date) = '".date2Sql($today)."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $group3 = 'branch_code desc';
        $join = array();
        // $join['branch_details'] = array('content'=>'branch_details.branch_code = trans_sales.branch_code');
        // $ts = $this->site_model->get_tbl('trans_sales use index(branch_code)',$args3,array(),$join,true,$select3,$group3);
        $ts = $this->site_model->get_tbl('store_zread',$args3,array(),$join,true,$select3,$group3);
        $ts = today_sales($ts);

        echo json_encode(array('lastGT'=>$lastGT,'todaySales'=>$todaySales,'todayTransNo'=>num($todayTransNo),'lsu'=>$lsu,'ts'=>$ts));
    }

    public function get_last_gt(){
        // echo "<pre>",print_r($this->session->userdata('user')),"</pre>";die();
        $branch_code = $this->input->post("branch_code");   
        $today = $this->site_model->get_db_now();
        $dates = date("m",strtotime($today));
        $year = date("Y");
        $select = 'MONTH(trans_date),sum(gross_sales) as true_grand_total,store_zread.branch_code';
        $args3 = array();
        $join = array();
        $user = $this->session->userdata('user'); 
        
        $lastGT = 0;
        if($this->user_branches){
            $args3["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args3["MONTH(trans_date) = ".$dates.""] = array('use'=>'where','val'=>null,'third'=>false);
        $args3["YEAR(trans_date) = ".$year.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt = $this->site_model->get_tbl('store_zread',$args3,array(),$join,true,$select);
        // echo $this->site_model->db->last_query(); die();
        // $gt = $this->old_grand_net_total(date2SqlDateTime($today), false, $branch_code);
        $lastGT = $gt[0]->true_grand_total;


        $dates2 = date("m",strtotime('"-1 month"'));
        $select2 = 'MONTH(trans_date),sum(gross_sales) as true_grand_total,store_zread.branch_code';
        $args2 = array();
        $join2 = array();
        $lastGT2 = 0;
        $perc = 0;
        $user = $this->session->userdata('user'); 
        
        if($this->user_branches){
            $args2["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);        
        }
        $args2["MONTH(trans_date) = ".$dates2.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt2 = $this->site_model->get_tbl('store_zread',$args2,array(),$join2,true,$select2);
        $lastGT2 = $gt2[0]->true_grand_total;

        if($lastGT2 == 0){
            $lastGT2 = 1;
        }

        if($lastGT > 0){
            $perc = ($lastGT / $lastGT2) * 100;
            if($lastGT > $lastGT2){
                $perc = '+ '.num($perc);
            }else{
                $perc = '- '.num($perc);
            }
        }
        // echo num($test);die();
        echo json_encode(array("lastGT"=>num($lastGT),'perc'=>$perc));
        // echo num($lastGT);
    }
    public function get_net_sales(){
        $branch_code = $this->input->post("branch_code");        
        $today = $this->site_model->get_db_now();
        // echo $branch_code;die();
        // $today = '2024-09-20';
        // $dates = date2Sql($today);
        $dates = date("m",strtotime($today));
        // $select = 'MAX(old_gt) as true_grand_total,branch_code';
        $select = 'MONTH(trans_date),sum(net_sales) as true_grand_total,store_zread.branch_code';
        $args3 = array();
        $join = array();
        $user = $this->session->userdata('user'); 
        
        $lastGT = 0;
        if($this->user_branches){
            $args3["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args3["MONTH(trans_date) = ".$dates.""] = array('use'=>'where','val'=>null,'third'=>false);
        $year = date("Y");
        $args3["YEAR(trans_date) = ".$year.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt = $this->site_model->get_tbl('store_zread',$args3,array(),$join,true,$select);

        // echo $this->site_model->db->last_query(); die();
        // $gt = $this->old_grand_net_total(date2SqlDateTime($today), false, $branch_code);
        $lastGT = $gt[0]->true_grand_total;
        // echo num($lastGT);
        $dates2 = date("m",strtotime('"-1 month"'));
        $select2 = 'MONTH(trans_date),sum(net_sales) as true_grand_total,store_zread.branch_code';
        $args2 = array();
        $join2 = array();
        $lastGT2 = 0;
        $perc = 0;
        $user = $this->session->userdata('user'); 
        
        if($this->user_branches){
            $args2["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args2["MONTH(trans_date) = ".$dates2.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt2 = $this->site_model->get_tbl('store_zread',$args2,array(),$join2,true,$select2);
        $lastGT2 = $gt2[0]->true_grand_total;

        // $perc = ($lastGT / $lastGT2) * 100;

        if($lastGT2 == 0){
            $lastGT2 = 1;
        }

        if($lastGT > 0){
            $perc = ($lastGT / $lastGT2) * 100;
            if($lastGT > $lastGT2){
                $perc = '+ '.num($perc);
            }else{
                $perc = '- '.num($perc);
            }
        }
        // echo num($test);die();
        echo json_encode(array("lastGT"=>num($lastGT),'perc'=>$perc));
    }
    public function get_vat_sales(){
        $branch_code = $this->input->post("branch_code");        
        $today = $this->site_model->get_db_now();
        // $today = '2024-09-20';
        // $dates = date2Sql($today);
        $dates = date("m",strtotime($today));
        // $select = 'MAX(old_gt) as true_grand_total,branch_code';
        $select = 'MONTH(trans_date),sum(vat_sales+vat_exempt_sales) as true_grand_total,store_zread.branch_code';
        $args3 = array();
        $join = array();
        $user = $this->session->userdata('user'); 
        
        $lastGT = 0;
        if($this->user_branches){
            $args3["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args3["MONTH(trans_date) = ".$dates.""] = array('use'=>'where','val'=>null,'third'=>false);
        $year = date("Y");
        $args3["YEAR(trans_date) = ".$year.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt = $this->site_model->get_tbl('store_zread',$args3,array(),$join,true,$select);
        
        // echo $this->site_model->db->last_query(); die();
        // $gt = $this->old_grand_net_total(date2SqlDateTime($today), false, $branch_code);
        $lastGT = $gt[0]->true_grand_total;

        $dates2 = date("m",strtotime('"-1 month"'));
        $select2 = 'MONTH(trans_date),sum(vat_sales) as true_grand_total,store_zread.branch_code';
        $args2 = array();
        $join2 = array();
        
        $lastGT2 = 0;
        $perc = 0;
        if($this->user_branches){
            $args2["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args2["MONTH(trans_date) = ".$dates2.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt2 = $this->site_model->get_tbl('store_zread',$args2,array(),$join2,true,$select2);
        $lastGT2 = $gt2[0]->true_grand_total;

        $perc = $lastGT - $lastGT2;

        // if($lastGT > $lastGT2){
        //     $perc = '+ '.num($perc);
        // }else{
        //     $perc = '- '.num($perc);
        // }
        echo json_encode(array("lastGT"=>num($lastGT),'perc'=>num($perc)));
        // echo num($lastGT);
    }
    public function get_discount_sales(){
        $branch_code = $this->input->post("branch_code");        
        $today = $this->site_model->get_db_now();
        // $today = '2024-09-20';
        // $dates = date2Sql($today);
        $dates = date("m",strtotime($today));
        // $select = 'MAX(old_gt) as true_grand_tlotal,branch_code';
        $select = 'MONTH(trans_date),sum(total_discount) as true_grand_total,store_zread.branch_code';
        $args3 = array();
        $join = array();
        $lastGT = 0;
        $user = $this->session->userdata('user'); 
        
        $lastGT = 0;
        if($this->user_branches){
            $args3["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args3["MONTH(trans_date) = ".$dates.""] = array('use'=>'where','val'=>null,'third'=>false);
        $year = date("Y");
        $args3["YEAR(trans_date) = ".$year.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt = $this->site_model->get_tbl('store_zread',$args3,array(),$join,true,$select);
        
        // echo $this->site_model->db->last_query(); die();
        // $gt = $this->old_grand_net_total(date2SqlDateTime($today), false, $branch_code);
        $lastGT = $gt[0]->true_grand_total;
        $dates2 = date("m",strtotime('"-1 month"'));
        $select2 = 'MONTH(trans_date),sum(total_discount) as true_grand_total,store_zread.branch_code';
        $args2 = array();
        $join2 = array();
        $lastGT2 = 0;
        $perc = 0;
        $user = $this->session->userdata('user'); 
        
        if($this->user_branches){
            $args2["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args2["MONTH(trans_date) = ".$dates2.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt2 = $this->site_model->get_tbl('store_zread',$args2,array(),$join2,true,$select2);
        $lastGT2 = $gt2[0]->true_grand_total;

        $perc = $lastGT - $lastGT2;

        // if($lastGT > $lastGT2){
        //     $perc = '+ '.num($perc);
        // }else{
        //     $perc = '- '.num($perc);
        // }
        echo json_encode(array("lastGT"=>num($lastGT),'perc'=>num($perc)));
        // echo num($lastGT);
    }
    public function get_trans_count_sales(){
        $branch_code = $this->input->post("branch_code");        
        $today = $this->site_model->get_db_now();
        // $today = '2024-09-20';
        // $dates = date2Sql($today);
        $dates = date("m",strtotime($today));
        // $select = 'MAX(old_gt) as true_grand_tlotal,branch_code';
        $select = 'MONTH(trans_date),sum(invoice_count) as true_grand_total,store_zread.branch_code';
        $args3 = array();
        $join = array();
        $lastGT = 0;
        $user = $this->session->userdata('user'); 
        
        $lastGT = 0;
        if($this->user_branches){
            $args3["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args3["MONTH(trans_date) = ".$dates.""] = array('use'=>'where','val'=>null,'third'=>false);
        $year = date("Y");
        $args3["YEAR(trans_date) = ".$year.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt = $this->site_model->get_tbl('store_zread',$args3,array(),$join,true,$select);
        
        // echo $this->site_model->db->last_query(); die();
        // $gt = $this->old_grand_net_total(date2SqlDateTime($today), false, $branch_code);
        $lastGT = $gt[0]->true_grand_total;
        $dates2 = date("m",strtotime('"-1 month"'));
        $select2 = 'MONTH(trans_date),sum(invoice_count) as true_grand_total,store_zread.branch_code';
        $args2 = array();
        $join2 = array();
        $lastGT2 = 0;
        $perc = 0;
        $user = $this->session->userdata('user'); 
        if($this->user_branches){
            $args2["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args2["MONTH(trans_date) = ".$dates2.""] = array('use'=>'where','val'=>null,'third'=>false);
        $year = date("Y");
        $args3["YEAR(trans_date) = ".$year.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt2 = $this->site_model->get_tbl('store_zread',$args2,array(),$join2,true,$select2);
        $lastGT2 = $gt2[0]->true_grand_total;

        $perc = $lastGT - $lastGT2;

        // if($lastGT > $lastGT2){
        //     $perc = '+ '.num($perc);
        // }else{
        //     $perc = '- '.num($perc);
        // }
        echo json_encode(array("lastGT"=>num($lastGT,0,'',','),'perc'=>num($perc)));
        // echo num($lastGT);
    }
    public function get_avg_sales(){
        $branch_code = $this->input->post("branch_code");        
        $today = $this->site_model->get_db_now();
        // $today = '2024-09-20';
        // $dates = date2Sql($today);
        $dates = date("m",strtotime($today));
        // $select = 'MAX(old_gt) as true_grand_tlotal,branch_code';
        $select = 'MONTH(trans_date),sum(net_sales)/sum(guest_count) as true_grand_total,store_zread.branch_code';
        $args3 = array();
        $join = array();
        $lastGT = 0;
        $user = $this->session->userdata('user'); 
        
        $lastGT = 0;
        if($this->user_branches){
            $args3["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args3["MONTH(trans_date) = ".$dates.""] = array('use'=>'where','val'=>null,'third'=>false);
        $year = date("Y");
        $args3["YEAR(trans_date) = ".$year.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt = $this->site_model->get_tbl('store_zread',$args3,array(),$join,true,$select);
        
        // echo $this->site_model->db->last_query(); die();
        // $gt = $this->old_grand_net_total(date2SqlDateTime($today), false, $branch_code);
        $lastGT = $gt[0]->true_grand_total;
        $dates2 = date("m",strtotime('"-1 month"'));
        $select2 = 'MONTH(trans_date),sum(net_sales)/sum(guest_count) as true_grand_total,store_zread.branch_code';
        $args2 = array();
        $join2 = array();
        $lastGT2 = 0;
        $perc = 0;
        $user = $this->session->userdata('user'); 
        if($this->user_branches){
            $args2["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args2["MONTH(trans_date) = ".$dates2.""] = array('use'=>'where','val'=>null,'third'=>false);
        $gt2 = $this->site_model->get_tbl('store_zread',$args2,array(),$join2,true,$select2);
        $lastGT2 = $gt2[0]->true_grand_total;

        $perc = $lastGT - $lastGT2;

        // if($lastGT > $lastGT2){
        //     $perc = '+ '.num($perc);
        // }else{
        //     $perc = '- '.num($perc);
        // }
        echo json_encode(array("lastGT"=>num($lastGT),'perc'=>num($perc)));
        // echo num($lastGT);
    }
    public function get_top_menus(){
        
        $branch_code = $this->input->post("branch_code");
        $calendar= $this->site_model->get_db_now();        
        $curr = true;
        if($this->user_branches){
            $args["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }
        $args["DATE(trans_sales.datetime) = DATE('".date2Sql($calendar)."') "] = array('use'=>'where','val'=>null,'third'=>false);
        $trans = $this->trans_sales($args,$curr);
        $sales = $trans['sales'];
        $trans_menus = $this->menu_top_sales($sales['settled']['ids'],$curr,$branch_code);
        $menus = $trans_menus['menus']; 

        $menus = array_merge($menus,$trans_menus['items']);

        usort($menus, function($a, $b) {
            return $b['qty'] - $a['qty'];
        });
        
        $this->make->sTable(array('class'=>'table table-striped'));
            $ctr = 1;
            $this->make->sRow();
                $this->make->th('#',array('width'=>'10'));
                $this->make->th('Name');
                $this->make->th('QTY',array('width'=>'10'));
                $this->make->th('Amount',array('width'=>'25'));
            $this->make->eRow();

            if(empty($menus)){
                $this->make->sRow();
                        $this->make->td('No data available',array('colspan'=>4));
                        // $this->make->td($res['name']);
                        // $this->make->td($res['qty']);
                        // $this->make->td(num($res['amount']) );
                $this->make->eRow();    
            }else{
                foreach ($menus as $res) {
                    $this->make->sRow();
                        $this->make->td($ctr.".");
                        $this->make->td($res['name']);
                        $this->make->td($res['qty']);
                        $this->make->td(num($res['amount']) );
                    $this->make->eRow();                
                    if($ctr == 3)
                        break;
                    $ctr++;
                }
            }
        $this->make->eTable();
        echo $this->make->code();
    }

    public function get_top_toppings(){
        $branch_code = $this->input->post("branch_code");
        $calendar= $this->site_model->get_db_now('sql');        
        $curr = true;
        $args = array();
        if($this->user_branches){
            $args["branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }

        $args['trans_sales.datetime'] = array('use'=>'like','val'=>date('Y-m',strtotime($calendar)));
        $args['trans_sales.inactive'] = 0;
        $args['trans_sales.type_id'] = 10;
        $args['trans_sales.trans_ref is not null'] = array('use'=>'where','val'=>null,'third'=>false);

        $menus = $this->menu_toppings($args);
        // echo "<pre>",print_r($menus),"</pre>";die();
        
        usort($menus, function($a, $b) {
            return $b->total_qty - $a->total_qty;
        });
        
        $this->make->sTable(array('class'=>'table table-striped'));
            $ctr = 1;
            $this->make->sRow();
                $this->make->th('#',array('width'=>'10'));
                $this->make->th('Name');
                $this->make->th('QTY',array('width'=>'10'));
                $this->make->th('Amount',array('width'=>'25'));
            $this->make->eRow();
            if(empty($menus)){
                    $this->make->sRow();
                        $this->make->td('No data available',array('colspan'=>4));
                        // $this->make->td($res['name']);
                        // $this->make->td($res['qty']);
                        // $this->make->td(num($res['amount']) );
                    $this->make->eRow();    
            }else{
                foreach ($menus as $res) {
                    $this->make->sRow();
                        $this->make->td($ctr.".");
                        $this->make->td($res->menu_name);
                        $this->make->td($res->total_qty);
                        $this->make->td(num($res->total_amount) );
                    $this->make->eRow();                
                    if($ctr == 10)
                        break;
                    $ctr++;
                }
            }
        $this->make->eTable();
        echo $this->make->code();
    }
    public function summary_orders(){
        
        $today = $this->site_model->get_db_now(null,true);
        $args = array();
        $args["DATE(trans_sales.datetime)"] = $today;
        $orders = array();
        $ords = $this->cashier_model->get_trans_sales(null,$args);
        $types = unserialize(SALE_TYPES);
        $set = $this->cashier_model->get_pos_settings();
        if(count($set) > 0){
            $types = array();
            $ids = explode(',',$set->controls);
            foreach($ids as $value){
                $text = explode('=>',$value);
                if($text[0] == 1){
                    $types[]='dinein';
                }elseif($text[0] == 7){
                    $types[]='drivethru';
                }else{
                    $types[]=$text[1];
                }
            }
        }

        $status = array('Open'=>'blue','Settled'=>'green','Cancel'=>'yellow','Void'=>'red');
        
        foreach ($types as $typ) {
            $open = 0;
            $settled = 0;
            $cancel = 0;
            $void = 0;
            foreach ($ords as $res) {
                if(strtolower($res->type) == strtolower($typ)){
                    if($res->type_id == 10){
                        if($res->trans_ref != "" && $res->inactive == 0){
                            $settled += $res->total_amount;
                        }
                        elseif($res->trans_ref == ""){
                            if($res->inactive == 0){
                                $open += $res->total_amount;
                            }
                            else{
                                $cancel += $res->total_amount;
                            }
                        }
                    }
                    else{
                        $void += $res->total_amount;
                    }
                }
            }
            $orders[$typ] = array('label'=>$typ,'open'=>$open,'settled'=>$settled,'cancel'=>$cancel,'void'=>$void);
        }
        $shift_sales = array();
        foreach ($ords as $res) {
            if($res->type_id == 10){
                if($res->trans_ref != "" && $res->inactive == 0){
                    if(isset($shift_sales[$res->shift_id])){
                        $shift_sales[$res->shift_id] += $res->total_amount;
                    }
                    else
                        $shift_sales[$res->shift_id] = $res->total_amount;
                }    
            }
        }
        $shifts = array();
        foreach ($shift_sales as $shift_id => $total) {
            if(!in_array($shift_id, $shifts))
                $shifts[] = $shift_id;
        }
        $shs = array();
        if(count($shifts) > 0){
            $select = "shifts.shift_id,users.username,users.fname,users.mname,users.lname,users.suffix";
            $joinTables['users'] = array('content'=>'shifts.user_id = users.id');
            $sh = $this->site_model->get_tbl('shifts',array('shift_id'=>$shifts),array(),$joinTables,true,$select);
            foreach ($sh as $res) {
                // $shs[$res->shift_id] = array('label'=>$res->fname." ".$res->mname." ".$res->lname." ".$res->suffix,'value'=>numInt($shift_sales[$res->shift_id]) );
                $shs[$res->shift_id] = array('label'=>$res->username,'value'=>numInt($shift_sales[$res->shift_id]) );
            }
        }
        if(count($shs) == 0){
            $shs[]=array('label'=>'No Sales Found','value'=>numInt(0));
        }

        $total_trans = 0;
        $stat = array();
        $total_sales = 0;
        foreach ($orders as $type => $opt) {
            foreach ($opt as $txt => $val) {
                if($txt != 'label'){
                    if(isset($stat[strtolower($txt)]))
                        $stat[strtolower($txt)] += $val;
                    else
                        $stat[strtolower($txt)] = $val;
                    $total_trans += $val;

                    if($txt == 'open' || $txt == 'settled')
                        $total_sales += $val;
                }
            }
        }

        // <div class="row">
        //     <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
        //         <input type="text" class="knob" data-readonly="true" value="80" data-width="60" data-height="60" data-fgColor="#f56954"/>
        //         <div class="knob-label">CPU</div>
        //     </div><!-- ./col -->
        //     <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
        //         <input type="text" class="knob" data-readonly="true" value="50" data-width="60" data-height="60" data-fgColor="#00a65a"/>
        //         <div class="knob-label">Disk</div>
        //     </div><!-- ./col -->
        //     <div class="col-xs-4 text-center">
        //         <input type="text" class="knob" data-readonly="true" value="30" data-width="60" data-height="60" data-fgColor="#3c8dbc"/>
        //         <div class="knob-label">RAM</div>
        //     </div><!-- ./col -->
        // </div><!-- /.row -->
        $this->make->sDivRow(); 
        foreach ($status as $txt => $color) {
            $this->make->sDivCol(6,'center');
                if($total_trans == 0 || $stat[strtolower($txt)] == 0)
                    $percent = 0;
                else
                    $percent = ($stat[strtolower($txt)]/$total_trans) * 100;
                $this->make->append(
                     '
                         <div class="easy-pie-chart">
                            <div class=" number '.$color.'" data-percent="'.num($percent,0).'">
                                <span>'.num($percent,0).'</span>%  </div>
                                <div>'.$txt.'</div>
                            <a class="title" href="javascript:;">'.small( num($stat[strtolower($txt)]) ."/".num($total_trans) ).'
                                <i class="icon-arrow-right"></i>
                            </a>
                        </div>         
                    '
                        // <div class="knob">
                        //     <div class="'.$color.'" data-percent="'.num($percent,0).'"  data-scale-color="#000000"></div>
                        //     <span>'.num($percent,0).'</span>% 
                        //     <div>'.$txt.'</div>
                        //     <div class="knob-label">'.small( num($stat[strtolower($txt)]) ."/".num($total_trans) ).'</div>
                        // </div>

                     // <div class="knob" data-percent="98" data-scale-color="#000000"></div>'.$txt.'
                     // <div class="knob-label">'.small( num($stat[strtolower($txt)]) ."/".num($total_trans) ).'</div>
                        // <div class="knob-label">'.$txt.'</div>
                        // <div class="knob" data-readonly="true" value="'.num($percent,0).'" data-skin="tron" data-thickness="0.2" data-angleArc="250" data-angleOffset="-125" data-width="100" data-height="100" data-fgColor="'.$color.'"/>
                        // <div class="knob-label">'.small( num($stat[strtolower($txt)]) ."/".num($total_trans) ).'</div>
                 );
            $this->make->eDivCol();
            // $this->make->sDiv(array('class'=>'clearfix'));
            //     $this->make->span($txt,array('class'=>'pull-left'));
            //     $this->make->span(small( num($stat[strtolower($txt)]) ."/".num($total_trans) ),array('class'=>'pull-right'));
            // $this->make->eDiv();
            // $this->make->sDiv(array('style'=>'margin-bottom:10px;'));
            //     $this->make->progressBar($total_trans,$stat[strtolower($txt)],null,0,$color,array());
            // $this->make->eDiv();
        }
        $this->make->eDivRow(); 
        $code = $this->make->code();
        
        echo json_encode(array("orders"=>$orders,'shift_sales'=>$shs,'types'=>$types,'code'=>$code));
    }
    public function get_total_trans()
    {
        $branch_code = $this->input->post("branch_code");
        $todayTransNo = 0;
        $select = 'sum(invoice_count) as today_no_trans';
        // $select = 'count(sales_id) as today_no_trans';
        // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.inactive"] = 0;
        // $args["trans_sales.paid"] = 1;
        // $args["trans_sales.type_id"] = SALES_TRANS;

        if($branch_code != ''){
            $args["branch_code"] = $branch_code;
        }
        
        // $aa = $this->site_model->get_tbl('( select * from trans_sales group by branch_code,sales_id,pos_id )trans_sales',$args,array(),null,true,$select);
        $aa = $this->site_model->get_tbl('store_zread',$args,array(),null,true,$select);
        // echo $this->site_model->db->last_query();die();
        if(count($aa) > 0){
            // $todayTransNo = (isset($aa[0]->today_sales) ? $aa[0]->today_sales : 0);
            $todayTransNo = $aa[0]->today_no_trans; 
        }
        echo num($todayTransNo);
    }
    public function get_sales_today()
    {
        $branch_code = $this->input->post("branch_code");
        $today = $this->site_model->get_db_now();
        $todaySales = 0;
        $select = 'sum(total_amount) as today_sales';
        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive"] = 0;
        $args["trans_sales.paid"] = 1;
        $args["trans_sales.type_id"] = SALES_TRANS;
        if($branch_code != ""){
        $args["trans_sales.branch_code"] = $branch_code;            
        }
        $args["DATE(trans_sales.datetime) = '".date2Sql($today)."'"] = array('use'=>'where','val'=>null,'third'=>false);;
        $ts = $this->site_model->get_tbl('trans_sales use index(branch_code)',$args,array(),null,true,$select);
        // echo $this->site_model->db->last_query();die();
        if(count($ts) > 0){
            $todaySales = (isset($ts[0]->today_sales) ? $ts[0]->today_sales : 0);
            // $todayTransNo = $ts[0]->today_no_trans; 
        }
        echo num($todaySales);
    }

    function chart_data()
    {
        
        // $this->load->model('reports/Inventory_summary');
        // $model = $this->Inventory_summary;
        // $today = $this->site_model->get_data_graph();
        // $year_drop = $this->input->post('year');
        // $branch_code = $this->input->post("branch_code");
        $user = $this->session->userdata('user');
        $user_id = $user['sysid'];
        $year = date("Y");
        $mdata = array();
        for($i=1;$i<=12;$i++){
            $top_sales_loc = $this->site_model->get_data_graph($i,$year,$this->user_branches);
            if($top_sales_loc){
                foreach ($top_sales_loc as $k => $val) {
                    // echo $val->branch_code;die();
                    $mdata[''][$i] = array('amount'=>$val->sales);

                }
            }
        }

        // echo "<pre>",print_r($mdata),"</pre>";die();
        // var_dump($mdata); die();
        $data = array();
        foreach($mdata as $key => $value){


            $data_val = array();
            $jan = $feb = $mar = $apr = $may = $jun = $jul = $aug = $sep = $oct = $nov = $dec = 0;
            for($i=1;$i<=12;$i++){
                // echo $value[$i]['amount'];

                $value[$i]['amount'] = isset($value[$i]['amount']) ? $value[$i]['amount'] : 0;
                if($i == 1){
                    if($value[$i]['amount']){
                        $jan += $value[$i]['amount'];
                    }
                }elseif($i == 2){
                    if($value[$i]['amount']){
                        $feb += $value[$i]['amount'];
                    }
                }elseif($i == 3){
                    if($value[$i]['amount']){
                        $mar = $value[$i]['amount'];
                    }
                }elseif($i == 4){
                    if($value[$i]['amount']){
                        $apr = $value[$i]['amount'];
                    }
                }elseif($i == 5){
                    if($value[$i]['amount']){
                        $may = $value[$i]['amount'];
                    }
                }elseif($i == 6){
                    if($value[$i]['amount']){
                        $jun = $value[$i]['amount'];
                    }
                }elseif($i == 7){
                    if($value[$i]['amount']){
                        $jul = $value[$i]['amount'];
                    }
                }elseif($i == 8){
                    if($value[$i]['amount']){
                        $aug = $value[$i]['amount'];
                    }
                }elseif($i == 9){
                    if($value[$i]['amount']){
                        $sep = $value[$i]['amount'];
                    }
                }elseif($i == 10){
                    if($value[$i]['amount']){
                        $oct = $value[$i]['amount'];
                    }
                }elseif($i == 11){
                    if($value[$i]['amount']){
                        $nov = $value[$i]['amount'];
                    }
                }elseif($i == 12){
                    if($value[$i]['amount']){
                        $dec = $value[$i]['amount'];
                    }
                }

            }

            $data[] = array('name'=>$key,
                            // 'type'=>'bar',
                            'data'=>array((float)$jan,(float)$feb,(float)$mar,(float)$apr,(float)$may,(float)$jun,(float)$jul,(float)$aug,(float)$sep,(float)$oct,(float)$nov,(float)$dec)
                            );
        }
        // echo "<pre>",print_r($data),"<pre>";die();
        echo json_encode(array('datas'=>$data));
    }
    function comp_weekly_data()
    {
        $mdata = array();
        $today = $this->site_model->get_db_now();
        $wdate = date('Y-m-d',strtotime($today));
        $wdate_to = $wdate;
        $wdate_to = strtotime("-7 days", strtotime($wdate_to)); //-7 days for last week. -30 for last week
        $wdate_to = date("Y-m-d", $wdate_to);
        $user = $this->session->userdata('user');
        $user_id = $user['sysid'];
        $select = 'sum(gross_sales) as amount,trans_date';
        $group = 'store_zread.trans_date,branch_details.company_owned desc';
        $args["store_zread.trans_date between '".$wdate_to."' and '".$wdate."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["branch_details.company_owned = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        
        if($this->user_branches){
            $args["store_zread.branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }

        $join = array();
        $join['branch_details use index(branch_code)'] = array('content'=>'branch_details.branch_code = store_zread.branch_code');
        
        $mdata = $this->site_model->get_tbl('store_zread',$args,array(),$join,true,$select,$group);

        $data = array();
        foreach($mdata as $key => $value){
            // $data_val = array();
            $sun = "";
            $mon = "";
            $tue = "";
            $wed = "";
            $thu = "";
            $fri = "";
            $sat = "";
            $day =  date("D", strtotime($value->trans_date));
            if($day == 'Sun'){
                $sun = $value->amount;
            }else if($day == 'Mon'){
                $mon = $value->amount;
            }else if($day == 'Tue'){
                $tue = $value->amount;
            }else if($day == 'Wed'){
                $wed = $value->amount;
            }else if($day == 'Thu'){
                $thu = $value->amount;
            }else if($day == 'Fri'){
                $fri = $value->amount;
            }else if($day == 'Sat'){
                $sat = $value->amount;
            }
            $data[] = array('name'=>'Sales','data'=>array((float)$sun,(float)$mon,(float)$tue,(float)$wed,(float)$thu,(float)$fri,(float)$sat));
        }


        // echo "<pre>",print_r($data),"<pre>";die();
        echo json_encode(array('datas'=>$data));
    }
    function franchisee_weekly_data()
    {
        $mdata = array();
        $today = $this->site_model->get_db_now();
        $wdate = date('Y-m-d',strtotime($today));
        $wdate_to = $wdate;
        $wdate_to = strtotime("-7 days", strtotime($wdate_to)); //-7 days for last week. -30 for last week
        $wdate_to = date("Y-m-d", $wdate_to);
        $user = $this->session->userdata('user');
        $user_id = $user['sysid'];
        $select = 'sum(gross_sales) as amount,trans_date';
        $group = 'store_zread.trans_date,branch_details.company_owned desc';
        $args["store_zread.trans_date between '".$wdate_to."' and '".$wdate."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["branch_details.company_owned = 1"] = array('use'=>'where','val'=>null,'third'=>false);
        
        if($this->user_branches){
            $args["store_zread.branch_code"] = array('use'=>'where_in','val'=>$this->user_branches,'third'=>false);
        }

        $join = array();
        $join['branch_details use index(branch_code)'] = array('content'=>'branch_details.branch_code = store_zread.branch_code');
        
        $mdata = $this->site_model->get_tbl('store_zread',$args,array(),$join,true,$select,$group);

        $data = array();
        foreach($mdata as $key => $value){
            // $data_val = array();
            $sun = "";
            $mon = "";
            $tue = "";
            $wed = "";
            $thu = "";
            $fri = "";
            $sat = "";
            $day =  date("D", strtotime($value->trans_date));
            if($day == 'Sun'){
                $sun = $value->amount;
            }else if($day == 'Mon'){
                $mon = $value->amount;
            }else if($day == 'Tue'){
                $tue = $value->amount;
            }else if($day == 'Wed'){
                $wed = $value->amount;
            }else if($day == 'Thu'){
                $thu = $value->amount;
            }else if($day == 'Fri'){
                $fri = $value->amount;
            }else if($day == 'Sat'){
                $sat = $value->amount;
            }
            $data[] = array('name'=>'Sales','data'=>array((float)$sun,(float)$mon,(float)$tue,(float)$wed,(float)$thu,(float)$fri,(float)$sat));
        }


        // echo "<pre>",print_r($data),"<pre>";die();
        echo json_encode(array('datas'=>$data));
    }
    public function pie_chart()
    {      
        $year = date("Y");        
        $mdata = array();
        
        $top_sales_loc = $this->site_model->get_sales_per_store(null,$this->user_branches);
        if($top_sales_loc){
            foreach ($top_sales_loc as $k => $val) 
            {                    
                $branch_code = substr($val->branch_name, 0, 500);

                // $acronym = implode('',array_diff_assoc(str_split(ucwords($branch_code)),str_split(strtolower($branch_code))));
                $mdata[] = array('store'=>$branch_code,'value'=>numInt($val->sales,2));

            }
        }     

        echo json_encode($mdata);
    }
    public function category_sales_chart()
    {
        $branch_code = $this->input->post("branch_code");
        $month = date("m");
        $year = date("Y");
        $data = array();
        $cat_sales = $this->site_model->get_category_sales($branch_code, "", $year);
        foreach ($cat_sales as $k => $v) 
        {            
            $cat_name = "Others";
            if($v->menu_cat_name != null){
                $cat_name = $v->menu_cat_name;
            }
            // $data[] = array("category"  =>  $cat_name,
            //                 "amount"    =>  $v->sales,
            //                 "color"     =>  $this->rand_color());
            $data[] = array("cat_name" => $cat_name,
                            "townName" => $cat_name,
                            "distance" => numInt($v->sales,2)
                            );
        }        
        echo json_encode($data);
    }
    public function year_to_date_chart()
    {
        $this->load->model("dine/menu_model");
        $branch_code = $this->input->post("branch_code");
        $data = $this->menu_model->get_yearly_sales_dashboard("", "", $branch_code);
        $json = array();
        foreach ($data as $key => $v) 
        {
            $to_date_sales = $this->menu_model->get_yearly_sales_dashboard(date("Y-01-01", strtotime($v->year."-01-01")), date("Y-m-d", strtotime($v->year.date("-m-d"))), $branch_code);
            // if($v->year == 2018)
            // {
            //     echo '<pre>', print_r($this->db->last_query()), '</pre>';die();                
            // }
            $to_date_sales_amt = 0;
            foreach ($to_date_sales as $k => $val) 
            {
                $to_date_sales_amt += $val->amt;
            }            
            $json[] = array("year"  =>  $v->year,
                            "total_sales"   =>  numInt($to_date_sales_amt));
                            // "to_date_sales"   => $to_date_sales_amt,
                            // "total_sales"   =>  $v->amt);
        }
        echo json_encode($json);
    }
    public function month_to_month_gen()
    {
        $this->load->model('dine/setup_model');        
        $this->load->model("dine/menu_model");        
        
        $branch_id = $this->input->post('branch_id');        
        start_load(0);
        
        $date = $this->input->post("date");                
        $this->menu_model->db = $this->load->database('default', TRUE);
        // $date = '2018-04-30';
        $to = date("Y-m", strtotime($date));                
        $from = date('Y-m', strtotime($to . ' -1 month'));        
        // echo $from;exit;
        $param = array();
        // for ($i=date("Y-m", strtotime($from)); $i <= date("Y-m", strtotime($to)); $i++) { 
        for ($i=date("Y-m", strtotime($from)); $i <= date("Y-m", strtotime($to)); $i=date('Y-m', strtotime($i . ' +1 month'))) { 
            $param[date("m", strtotime($i."-01"))] = array("from"=>$i."-01", "to"=>date("Y-m-t", strtotime($i."-01")));            
        }        
        $this->session->set_userdata("param", $param);
        $branch_details = $this->menu_model->get_branch_details($branch_id);                      
        $this->session->set_userdata("branch_details", $branch_details);
        $trans = array();
        $counter = 0;
        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();                
                    $this->make->sRow();
                    $this->make->th("");
                    // $this->make->th("Branch");
                    foreach ($param as $k => $v) {
                        $monthNum  = $k;
                        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                        $monthName = $dateObj->format('F');
                        $this->make->th($monthName);                                                
                    }                                   
                    $this->make->eRow();
                $this->make->eTableHead();                

                $this->make->sTableBody();
                $total = array();
                foreach ($branch_details as $key => $vbranch) {
                    $this->make->sRow();
                        $this->make->td($vbranch->branch_name);
                        // $this->make->td();
                        foreach ($param as $k => $v) {
                            $amt = 0;
                            $sales = $this->menu_model->get_monthly_sales($v["from"], $v["to"], $vbranch->branch_code);
                            if(!empty($sales)){                                
                                   $this->make->td(num($sales[0]->amt), array("style"=>"text-align:right"));
                                   $amt += $sales[0]->amt;
                            }else{
                                   $this->make->td("0.00", array("style"=>"text-align:right"));                                
                            }                        
                            if(array_key_exists($k, $total)){
                                $total[$k] += $amt;
                            }else{
                                $total[$k] = $amt;                                
                            }
                        }
                    $this->make->eRow();
                }
                $this->make->sRow();
                    $this->make->th("Total");           
                    foreach ($total as $key => $value) 
                    {                        
                        $this->make->th(num($value), array("style"=>"text-align:right"));                                    
                    }         
                $this->make->eRow();
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('date');
        echo json_encode($json);
    }

    public function branch_latest_upload()
    {
        $this->load->model('dine/setup_model');        
        $this->load->model("dine/menu_model");        
        
        $branch_id = $this->input->post('branch_id');        
        start_load(0);
        
        $date = $this->input->post("date");                
        $this->menu_model->db = $this->load->database('default', TRUE);
        // $date = '2018-04-30';
        $to = date("Y-m-d", strtotime($date));                
        $from = date('Y-m-01', strtotime($to . ' -2 months'));        
        
        $param = array();
        for ($i=date("Y-m", strtotime($from)); $i <= date("Y-m", strtotime($to)); $i++) { 
            $param[date("m", strtotime($i."-01"))] = array("from"=>$i."-01", "to"=>date("Y-m-t", strtotime($i."-01")));            
        }        
        $this->session->set_userdata("param", $param);
        $branch_details = $this->menu_model->get_branch_details($branch_id);                      
        $this->session->set_userdata("branch_details", $branch_details);
        $trans = array();
        $counter = 0;
        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();                
                    $this->make->sRow();
                    $this->make->th("Branch Name");
                    $this->make->th("Last Sale Upload");
                    $this->make->th("Status");
                    $this->make->eRow();
                $this->make->eTableHead();                

                $this->make->sTableBody();
                $total = array();
                foreach ($branch_details as $key => $vbranch) {
                    $this->make->sRow();
                        $this->make->td($vbranch->branch_name);
                        // $this->make->td();
                        foreach ($param as $k => $v) {
                            $amt = 0;
                            $sales = $this->menu_model->get_monthly_sales($v["from"], $v["to"], $vbranch->branch_code);
                            if(!empty($sales)){                                
                                   $this->make->td(num($sales[0]->amt), array("style"=>"text-align:right"));
                                   $amt += $sales[0]->amt;
                            }else{
                                   $this->make->td("0.00", array("style"=>"text-align:right"));                                
                            }                        
                            if(array_key_exists($k, $total)){
                                $total[$k] += $amt;
                            }else{
                                $total[$k] = $amt;                                
                            }
                        }
                    $this->make->eRow();
                }
                $this->make->sRow();
                    $this->make->th("Total");           
                    foreach ($total as $key => $value) 
                    {                        
                        $this->make->th(num($value), array("style"=>"text-align:right"));                                    
                    }         
                $this->make->eRow();
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('date');
        echo json_encode($json);
    }
    ######################
    ###### FUNCTIONS #####
    ######################
    function rand_color() 
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }    
    ######################
    ######################
    ######################
}