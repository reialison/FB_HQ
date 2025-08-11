<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/cashier.php");
class Reprint extends Cashier {
    public function __construct(){
        parent::__construct();
        $this->load->helper('dine/reprint_helper');
        // $this->load->model('dine/cashier_model');
    }
    public function index(){
        $data = $this->syter->spawn('act_receipts');
        $data['page_title'] = 'Receipts';
        $data['code'] = printsPage();
        $data['load_js'] = 'dine/reprint';
        $data['use_js'] = 'printReceiptJs';
        $this->load->view('page',$data);
    }
    public function results(){
        $ref = $this->input->post('receipt');
        $branch_code = $this->input->post('branch_code');
        $brand = $this->input->post('brand');
        // $args['sales_id'] = array('use'=>'like','val'=>$ref); 
        $args['trans_ref'] = array('use'=>'like','val'=>$ref); 
        // $args['branch_code'] = array('use'=>'where','val'=>$branch_code);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);

        if($brand != ''){
            $args["trans_sales.pos_id  = '".$brand."' "] = array('use'=>'where','val'=>null,'third'=>false);
        } 
        $results = $this->site_model->get_tbl('trans_sales',$args,array('trans_sales.datetime'=>'desc'),null,true,'trans_ref,sales_id,datetime,total_amount,branch_code,pos_id','branch_code,sales_id,pos_id');
        // echo $this->site_model->db->last_query();die();
        // $code = "";
        $ids = array();
        // echo "<pre>",print_r($results),"</pre>";die();
        $this->make->sDiv(array('class'=>'list-group'));
        foreach ($results as $res) {
            $this->make->append('<a href="#" id="rec-'.$res->sales_id.'-'.$res->branch_code.'-'.$res->pos_id.'" class="rec list-group-item">');
                $this->make->sDiv();
                    $this->make->H(6,'Order No. <span class="pull-right"> total: '.$res->total_amount.'</span> '.$res->sales_id,array('style'=>'font-size:14px;margin:2px;'));
                $this->make->eDiv();
                    $this->make->p('Receipt No. '.$res->trans_ref.'<span class="pull-right">'.sql2Datetime($res->datetime).'</span>',array('style'=>'font-size:12px;margin:2px;'));
            $this->make->append('</a>');
            $ids[] = $res->sales_id.'-'.$res->branch_code.'-'.$res->pos_id;
        }
        $this->make->eDiv();
        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'ids'=>$ids));
    }
    public function view($sales_id=null,$noPrint=true,$branch_code="",$brand=""){
        if($noPrint)
            $reprint = false;
        else
            $reprint = true;

        // $branch_code = $this->input->post('branch_code');
        // $brand = $this->input->post('brand');
        // echo $branch_code;die();
        $print = $this->print_sales_receipt($sales_id,false,$noPrint,$reprint,null,true,1,0,null,false,false,$branch_code,$brand);   
        echo "<pre style='background-color:#fff'>";
            echo $print;
        echo "</pre>"; 
    }
    public function view_branch($sales_id=null,$branch_code="",$noPrint=true,$brand=""){
        if($noPrint)
            $reprint = false;
        else
            $reprint = true;
        // $branch_code = $this->input->post('branch_code');
        // echo $branch_code;die();
        $print = $this->print_sales_receipt($sales_id,false,$noPrint,$reprint,null,true,1,0,null,false,false,$branch_code,$brand);   
        echo "<pre style='background-color:#fff'>";
            echo $print;
        echo "</pre>"; 
    }
    public function allPrint(){
        $ref = $this->input->post('receipt');
        $sales = '2265';
        // $sales = '2314,2347,2339,2364';
        // $sales = '112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,157,159,160,161,162,164,165,166,167,169,170,171,172,173,174,175,176,177,178';
        $ids = explode(',', $sales);
        $args['sales_id'] = $ids; 
        // $args['sales_id'] = array('use'=>'like','val'=>$ref); 
        // $args['trans_ref'] = array('use'=>'or_like','val'=>$ref); 
         $this->db = $this->load->database('default', TRUE);
        $results = $this->site_model->get_tbl('trans_sales',$args,array('trans_sales.datetime'=>'desc'),null,true,'trans_ref,sales_id,datetime,total_amount,branch_code,pos_id');
        $code = "";
        $ids = array();

        $this->make->sDiv(array('class'=>'list-group'));
        foreach ($results as $res) {
            // $this->make->append('<a href="#" id="rec-'.$res->sales_id.'" class="rec list-group-item">');
            //     $this->make->sDiv();
            //         $this->make->H(6,'Order No. <span class="pull-right"> total: '.$res->total_amount.'</span> '.$res->sales_id,array('style'=>'font-size:14px;margin:2px;'));
            //     $this->make->eDiv();
            //         $this->make->p('Receipt No. '.$res->trans_ref.'<span class="pull-right">'.sql2Datetime($res->datetime).'</span>',array('style'=>'font-size:12px;margin:2px;'));
            // $this->make->append('</a>');
            // $ids[] = $res->sales_id;
            $this->view($res->sales_id,$res->branch_code,1,$res->pos_id);
        }
        $this->make->eDiv();
        $code = $this->make->code();
        echo json_encode(array('code'=>$code,'ids'=>$ids));
    }

    public function printReport(){
        $data = $this->syter->spawn('act_receipts_all');
        $data['page_title'] = 'Electronic Journal';
        $data['code'] = printAllPage();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/reprint';
        $data['use_js'] = 'printReceiptAllJs';
        $this->load->view('page',$data);
    }

    public function resultsAll(){
        $branch_code = $this->input->post('branch_code');
        $brand = $this->input->post('brand');
        $this->db = $this->load->database('default', TRUE);
        $setup = $this->setup_model->get_details_per_branch($branch_code);
        // echo $this->db->last_query();die();
        $set = $setup[0];
        $dates = explode(" to ",$this->input->post('calendar_range'));
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        // echo print_r($to);die();        
        $branch_code = $this->input->post('branch_code');

        // $args['sales_id'] = array('use'=>'like','val'=>$ref); 
        // $args['trans_ref'] = array('use'=>'or_like','val'=>$ref);
        $args['type_id'] = array('use'=>'where','val'=>10,'third'=>false);
        if($branch_code != null){ 
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        }

        if($brand != null){ 
        $args["trans_sales.pos_id  = '".$brand."' "] = array('use'=>'where','val'=>null,'third'=>false);
        }

        // $args['trans_ref'] = array('use'=>'or_like','val'=>$ref);
        $args["trans_sales.trans_ref IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false); 
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false); 
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $results = $this->site_model->get_tbl('trans_sales',$args,array('trans_sales.datetime'=>'desc'),null,true,'trans_ref,sales_id,datetime,total_amount,branch_code,pos_id','trans_sales.trans_ref');
        $code = "";
        $ids = array();

        // echo $this->site_model->db->last_query(); die();
        // var_dump($results); die();

        // $this->make->sDiv(array('class'=>'list-group'));
        foreach($results as $val){
            // $this->view($val->sales_id);
            $print = $this->print_sales_receipt($val->sales_id,false,true,false,null,true,1,0,null,false,false,$val->branch_code,$val->pos_id);   
            echo "<pre style='background-color:#fff'>";
                echo $print;
            echo "</pre>"; 
        }

    }

    public function printAll(){
        // $date = $this->input->post('calendar_range');
        // start_load(0);
        $this->db = $this->load->database('default', TRUE);

        // $_POST['branch_code'] = 'MAX_MAIN';
        // $_POST['brand'] = 1;
        // $_POST['calendar_range'] = '01/01/2022 to 01/20/2022';

        $branch_code = $this->input->post('branch_code');
        $brand = $this->input->post('brand');

        $setup = $this->setup_model->get_details($branch_code);
        $set = $setup[0];

        $dates = explode(" to ",$this->input->post('calendar_range'));
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);

        $args['type_id'] = array('use'=>'where','val'=>10,'third'=>false); 
        // $args['trans_ref'] = array('use'=>'or_like','val'=>$ref);
        $args["trans_sales.trans_ref IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false); 
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false); 
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false); 

        if($branch_code != null){ 
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        }

        if($brand != null){ 
        $args["trans_sales.pos_id  = '".$brand."' "] = array('use'=>'where','val'=>null,'third'=>false);
        }

        $results = $this->site_model->get_tbl('trans_sales',$args,array('trans_sales.datetime'=>'desc'),null,true,'trans_ref,sales_id,datetime,total_amount');
        // echo $this->site_model->db->last_query();
        // die();
        $code = "";
        $ids = array();

        // update_load(5);
        // sleep(1);

        // echo $this->db->last_query(); //die();
        // var_dump($results); die();

        // $this->make->sDiv(array('class'=>'list-group'));
        $filepath = 'C:/RECEIPT';
        if (!file_exists($filepath)) {   
            mkdir($filepath, 0777, true);
        }
        $ctr = 5;     
        // echo count($results);die();

        $array_sales_id = array();
        foreach($results as $val){
            $array_sales_id[] = $val->sales_id;
            // sleep(29);
            // echo "<pre>",print_r($val),"</pre>";die();
            // echo $val->trans_ref."\r\n";
            // $this->view($val->sales_id,false);

            // $ctr++;
            // if($ctr > 99){
            //     update_load(99);
            // }else{
            //     update_load($ctr);
            // }
            // old
//             $filename =   str_replace(':', '_', $from."-".$to.".jrn") ; //$val->trans_ref."-".date('Y-m-d',strtotime($val->datetime)).".jrn";
//             $text = $filepath."/".$filename;

//             $print = $this->print_sales_receipt($val->sales_id,false,true,false,null,true,1,0,null,false,false,$val->branch_code,$val->pos_id);
//             $fp = fopen($text, "a+");
//             fwrite($fp,$print);
//             fclose($fp);
            // old
// // var_dump($print);die();
        // echo "<pre style='background-color:#fff'>";
            // echo $print;
        // echo "</pre>"; 
            // update_load($ctr);



            // die();
        }
            // echo "<pre>",print_r($array_sales_id),"</pre>";die();
            set_time_limit(30);
            $print = $this->print_sales_receipt_ejournal($array_sales_id,false,false,false,null,false,1,0,null,false,false,$branch_code,$brand);   
        // die();
        update_load(100);

    }

}