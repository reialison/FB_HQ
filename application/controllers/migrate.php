<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/dine/reads.php");
class Migrate extends Reads{
    // public function __construct() {        
        // parent::__construct();
    // }
    
    
    public function execute_migration($ajax=false){
        // var_dump(MASTERMIGRATION);die();

        if(MASTERMIGRATION){    

            $this->load->model('core/master_model');
            $exec = $this->master_model->execute_migration();

            if( $exec){
                if(isset($_POST['ajax'])){
                    echo true;
                }else{
                    echo "<pre>",print_r($exec),"</pre>";die();
                    // return true;
                }
            }
        }
    }

    public function batch_test(){
        echo "adasdasd";
        echo MASTERMIGRATION;
    }

    function test2(){
        
    }

    public function migrate_data(){
        // $this->load->library('Db_manager');
        // $this->db = $this->db_manager->get_connection('default');

        $json = file_get_contents("php://input");
        $data = json_decode($json);
// print_r($data);
        return $this->migrate_to_db($data);           
    }

    public function migrate_to_db($data){
        
        $model = $data->table;
        $tbl_name = $model;
        $branch_code = $data->branch_code;
        $tbl_id = $data->tbl_id;

        // $this->db->trans_start();

        $master_id = $this->set_master_id($data,$model,$tbl_id);

        $list = array();
        $ids = array();

        $tbl_fields = $this->db->list_fields($tbl_name);
        
        foreach($data->results as $each){
            $each->master_id = $master_id;

            if($data->table == '0_cust_branch'){                
                $each->branch_name = $branch_code;
            }else if(!isset($each->branch_code) || $each->branch_code != $branch_code){
                $each->branch_code = $branch_code;
            }

            // if(!isset($each->terminal_id)){
            //     $each->terminal_id = $data->terminal_id;
            // }
            if(in_array('terminal_id', $tbl_fields) && $tbl_name != 'terminals'){
                $each->terminal_id = $data->terminal_id;
            }

            if(in_array($tbl_name, array('trans_sales_items','trans_sales_menus'))){
                // $this->db->where('branch_code' , $branch_code)->where('terminal_id', $data->terminal_id)->where('sales_id', $each->sales_id)->where('line_id', $each->line_id)->where('pos_id', $each->pos_id)->where('datetime','!=', $each->datetime)->delete($tbl_name);
            }

            $list[] = (array) $each;
            $ids[] = $each->$tbl_id;

            // $this->db->trans_start()
            // $this->db->insert($tbl_name,$each);
            // $this->db->trans_complete();
        }

        $chunks = array_chunk($list, 500);

        $this->db->trans_start();
            foreach($chunks as $chunk){
                $this->db->insert_ignore_batch($tbl_name,$chunk);
            }
        $this->db->trans_complete();
        // exit;

        if($tbl_name == '0_cust_branch'){
            $result = $this->db->where('branch_name',$branch_code)->where_in($tbl_id,$ids)->get($tbl_name)->result();
        }else{
            $result = $this->db->where('branch_code',$branch_code)->where_in($tbl_id,$ids)->get($tbl_name)->result();
        }        

        $main_tbl_id = $tbl_id;

        if(in_array($tbl_name, array('0_debtor_trans','0_comments','0_voided'))){
            $tbl_id = array($tbl_id,'type');
        }

        $new_result = $this->object_flat($result,array($tbl_id,'pos_id'));

        $this->set_lastest_logs($branch_code,$master_id,$new_result,$tbl_name,$main_tbl_id);

        // $this->db->trans_complete();

        echo json_encode(array('success'=>'Ajax request submitted successfully', 'master_id'=>$master_id,'result'=>$new_result));        
    }

    public function set_master_id($data,$model,$tbl_id){
        $record_count=count($data->results);

        // $list = $data->results;
        // foreach($data->results as $each){
        //     $list[] =  $each;
        // }
        if($data->results){
            $json_encode  = json_encode(array($tbl_id=>$this->array_flat($data->results,$tbl_id)));
        
            $master_logs = array(
            'status'=> 1,
            'type'=> $data->type,
            'transaction'=> $model,
            'src_id'=> $json_encode, 
            'user_id'=> $data->user_id,
            'terminal_id'=> $data->terminal_id,
            'branch_code'=> $data->branch_code,
            'record_count'=> $record_count,
            );

            // array("status"=>"0","type"=>$data->type,"transaction"=>$model,"src_id"=>$json_encode,'user_id'=>$data->user_id,'terminal_id'=> $data->terminal_id ,'branch_code'=> $data->branch_code,'record_count'=> $record_count);

            $this->db->insert('master_logs',$master_logs);
            return $this->db->insert_id();
        }

        return 0;
        
    }

    public function set_lastest_logs($branch_code,$master_id,$result,$tbl_name,$tbl_id){
        $data = array('branch_code'=>$branch_code,'master_id'=>$master_id,'src_id'=>json_encode($result),'transaction'=>$tbl_name,'tbl_id'=>$tbl_id);
        // print_r($data);
       //  print_r($data);exit;

       $is_exist = $this->db->where('branch_code',$branch_code)->get('latest_logs')->result();
       if($is_exist){
        $this->db->where('branch_code',$branch_code);
        $this->db->update('latest_logs',$data);
       }else{
        $this->db->insert('latest_logs',$data);
       }
    }

    public function get_latest_logs(){
        $json = []; $status = 200; $error = "";
        

        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $branch_code = $data->branch_code;

        if($branch_code != ''){
            $result = $this->db->where('branch_code',$branch_code)->get('latest_logs')->result();
            // print_r(!$result->isEmpty() ? 1 : 0);exit;
            echo json_encode(array(['success'=>'Ajax request submitted successfully', 'result'=> !empty($result) ? $result[0] : array() ]));        
        }
       
    }

    public function array_flat($trans_id_raw=array(), $var=""){

        $return = array();
        $property_name = $var;
        array_walk_recursive($trans_id_raw, function($a) use (&$return,$var) { $return[] = $a->$var; });

        return $return;
    }



    public function object_flat($trans_id_raw=array(), $var=""){

        $return = array();
        $property_name = $var;

        if(is_array($var)){
            array_walk_recursive($trans_id_raw, function($a) use (&$return,$var) {
                $field = array();
             foreach($var as $each){
                    if(isset($a->$each)){
                        $field[$each] = $a->$each; 
                    }
                }  

                $return[] = (object) $field;
            });
                
                
        }else{
            array_walk_recursive($trans_id_raw, function($a) use (&$return,$var) { $return[] = (object) array($var => $a->$var);  });
        }

        

        return $return;
    }

    public function update_migrate_data(){
        $json = file_get_contents("php://input");
        $data = json_decode($json);
        
        $model = $data->table;
        $tbl_name = $model;
        $branch_code = $data->branch_code;
        $tbl_id = $data->tbl_id;
        $based_delete_id = $data->based_delete_id;
        $trans_id_raw = $data->trans_id_raw;
        
        $arr_tbl = array('trans_sales_charges','trans_sales_discounts','trans_sales_items','trans_sales_local_tax','trans_sales_loyalty_points','trans_sales_menu_modifiers','trans_sales_menu_submodifiers','trans_sales_menus','trans_sales_no_tax','trans_sales_payments','trans_sales_tax','trans_sales_zero_rated','trans_receiving_menu','trans_receiving_menu_details','trans_receivings','trans_receiving_details','trans_adjustments','menu_moves','item_moves','locations','terminals','0_debtors_master','0_cust_allocations','item_serials','0_bank_trans');

        $trans_tbl = array('trans_sales_charges','trans_sales_discounts','trans_sales_items','trans_sales_local_tax','trans_sales_loyalty_points','trans_sales_menu_modifiers','trans_sales_menu_submodifiers','trans_sales_menus','trans_sales_no_tax','trans_sales_payments','trans_sales_tax','trans_sales_zero_rated');
        
        if(in_array($tbl_name, $trans_tbl) && $trans_id_raw){
            $this->db->where_in('concat(sales_id,"-",pos_id)', $trans_id_raw);

            $this->db->where('branch_code' , $branch_code)->delete($tbl_name);
        }
                    
        if(count($data->results) == 0){
            echo '';return false;
        }

        

        $master_id = $this->set_master_id($data,$model,$tbl_id);
        $terminal_id = $data->terminal_id;

        $list = array();
        $ids = array();
        $delete_ids = array();
        $has_pos_id = false;

        $tbl_fields = $this->db->list_fields($tbl_name);

        foreach($data->results as $each){
            $each->master_id = $master_id;

            if($data->table == '0_cust_branch'){                
                $each->branch_name = $branch_code;
            }else if(!isset($each->branch_code) || $each->branch_code != $branch_code){
                $each->branch_code = $branch_code;
            }

            // if(!isset($each->terminal_id)){
            //     $each->terminal_id = $terminal_id;
            // }
            if(in_array('terminal_id', $tbl_fields) && $tbl_name != 'terminals'){
                $each->terminal_id = $terminal_id;
            }

            $list[] = (array) $each;
            $ids[] = $each->$tbl_id;
            

            if(isset($each->pos_id)){
                $has_pos_id = true;
                $delete_ids[] = array($based_delete_id => $each->$based_delete_id,'pos_id'=>$each->pos_id);
            }else{
                $delete_ids[] = $each->$based_delete_id;
            }
        }

        $chunks = array_chunk($list, 20);
        
        // $model = $this->get_model($model);

        
// print_r($list);
        if(in_array($tbl_name, $arr_tbl)){
            // if($based_delete_id != 'terminal_id'){
            //     $this->db->where('terminal_id', $terminal_id);
            // }

            $this->db->trans_start();
                if($has_pos_id){
                    foreach($delete_ids as $delete_id){
                        foreach($delete_id as $i=>$k){
                            $this->db->where($i, $k);
                        }

                        $this->db->where('branch_code' , $branch_code)->delete($tbl_name);
                    }

                    if(!empty($data->trans_sales)){
                        foreach($data->trans_sales as $sale){
                            $this->db->where('pos_id', $sale->pos_id);
                            $this->db->where('sales_id', $sale->sales_id);

                            $this->db->where('branch_code' , $branch_code)->delete($tbl_name);
                        }
                    }
                }else if($delete_ids){
                    $this->db->where('branch_code' , $branch_code)->where_in($based_delete_id, $delete_ids)->delete($tbl_name); 

                    if(!empty($data->trans_sales)){
                        foreach($data->trans_sales as $sale){
                            $this->db->where('sales_id', $sale->sales_id);

                            $this->db->where('branch_code' , $branch_code)->delete($tbl_name);
                        }
                    }   
                }
                

                foreach($chunks as $chunk){            
                    $this->db->insert_ignore_batch($tbl_name,$chunk);
                }  
            $this->db->trans_complete();  
        }else if(in_array($data->table, array('0_debtor_trans','0_comments','0_voided'))){

            $counter = 0;

            $this->db->trans_start();
                foreach($chunks as $chunk){      
                   foreach($chunk as $each){
                        $this->db->where('branch_code', $branch_code)
                        // ->where('terminal_id', $terminal_id)
                        ->where($tbl_id, $each[$tbl_id])->where('type', $each['type'])->update($tbl_name,$each);

                        if($counter % 100 == 0){
                            set_time_limit(60);  
                        }

                        $counter++;
                   }               
                   $counter++;
                }
            $this->db->trans_complete();
        }else{

            $counter = 0;

            $this->db->trans_start();
                foreach($chunks as $chunk){      
                   foreach($chunk as $each){
                        $where_branch = $data->table == '0_cust_branch' ? 'branch_name': 'branch_code';
                        
                        if(isset($each['pos_id'])){
                            $this->db->where('pos_id', $each['pos_id']);
                        }
                        
                        $this->db->where($where_branch, $branch_code)
                        // ->where('terminal_id', $terminal_id)
                        ->where($tbl_id, $each[$tbl_id])->update($tbl_name,$each);
                        
                        if($counter % 100 == 0){
                            set_time_limit(60);  
                        }

                        $counter++;
                   }      
                   $counter++;
                }
            $this->db->trans_complete();    
            
        }

        $result = array();
        if($ids){
            if($data->table == '0_cust_branch'){
                $result = $this->db->where('branch_name',$branch_code)->where_in($tbl_id,$ids)->get($tbl_name)->result();
            }elseif($data->table == 'trans_sales'){
                $result = $this->db->where('branch_code',$branch_code)->where_in($tbl_id,$ids)->where('master_id',$master_id)->get($tbl_name)->result();
            }else{
                $result = $this->db->where('branch_code',$branch_code)->where_in($tbl_id,$ids)->get($tbl_name)->result();
            }
        }
        

        $main_tbl_id = $tbl_id;

        if(in_array($data->table, array('0_debtor_trans','0_comments','0_voided'))){
            $tbl_id = array($tbl_id,'type');
        }

        $new_result = $this->object_flat($result,array($tbl_id,'pos_id'));

        $this->set_lastest_logs($branch_code,$master_id,$new_result,$tbl_name,$main_tbl_id);  

        echo json_encode(array('success'=>'Ajax request submitted successfully', 'master_id'=>$master_id,'result'=>$new_result));
    }

    public function branch_details(Request $request){
        $json = []; $status = 200; $error = "";
        $branch_code = $request->branch_code;
        // echo $branch_code;
        if($branch_code){
            $br_details = $this->db->where('branch_code',$branch_code)->get('branch_details')->row();

             echo json_encode(array('branch_details'=>$br_details));
        }
        else{
            echo json_encode(array('branch_details'=>false));
        }
        
    }

    function download_data(){
        $this->load->model('site/site_model');

        //   return false;
         $json = file_get_contents("php://input");
         $data = json_decode($json);
         
        //   if($data->branch_code != 'LLAOLLAO_ROB_MAGNOLIA'){
        //     return false;
        // }

        $tbl_name = $data->table_name;

        $exist = $this->db->where('branch_code',$data->branch_code)->get($tbl_name)->result();

        $list = $data->results;
        $based_field = $data->based_field;
        $selected_field = $data->selected_field;
        $branch_code = $data->branch_code;
        $terminal_id = $data->terminal_id;
        $tbl_id = $data->tbl_id;
        $id_list = isset($data->id_list) ? $data->id_list : array();
 
        $hq_list = $this->db->where('branch_code',$branch_code)->get($tbl_name)->result();
        $now = $this->site_model->get_db_now('sql');
        
        if(!$exist && $list){ 
            // $list = $this->db->get($tbl_name)->result();
            $master_id = $this->set_master_id($data,$tbl_name,$tbl_id);

            if(in_array($tbl_name, array('payment_group','payment_types','payment_type_fields','transaction_type_categories'))){
                $add_trans_wrapped = $this->formulate_object($list , array('master_id' => $master_id,'branch_code'=> $branch_code,'update_date'=>$now) , true,false);
            }else{
                $add_trans_wrapped = $this->formulate_object($list , array('master_id' => $master_id,'terminal_id'=> $terminal_id ,'branch_code'=> $branch_code,'update_date'=>$now) , true,false);
            }            

            // $trans_id_raw = $this->db->select($data->based_field)->where('`master_id` is null ', NULL , false)->where('branch_code',BRANCH_CODE)->get($table_name)->result();

            $this->db->trans_start();
                $this->db->insert_ignore_batch($tbl_name,$add_trans_wrapped);
            $this->db->trans_complete();

            $new_result = $this->object_flat($list,$tbl_id);

            echo json_encode(array('success'=>'Ajax request submitted successfully', 'master_id'=>$master_id,'src_id'=>$new_result,'result'=>'','update_date'=>$now));

        }else if(isset($data->update_date) && $data->update_date !=''){

            $update_date = $data->update_date;

            // if(in_array($tbl_name, array('receipt_discounts','charges','brands'))){
            //     // $trans_raw = $this->db->select($selected_field)->where('`datetime` > ',$update_date)->where('branch_code',$branch_code)->get($tbl_name)->result();
            //     if($id_list){
            //         $trans_raw = $this->db->select($selected_field)->where_not_in('concat('.$tbl_id.',"-",datetime)',$id_list,false)->where('branch_code',$branch_code)->get($tbl_name)->result();
            //     }else{
            //         $trans_raw = $this->db->select($selected_field)->where('`datetime` > ',$update_date)->where('branch_code',$branch_code)->get($tbl_name)->result();
            //     }
            // }else
            if(in_array($tbl_name, array('menus','items'))){
                // $trans_raw = $this->db->select($selected_field)->where('`date_effective` =','CURDATE()',false)->where('branch_code',$branch_code)->get($tbl_name)->result();
                if($id_list){
                    $trans_raw = $this->db->select($selected_field)->where('`date_effective` <=','CURDATE()',false)->where_not_in('concat('.$tbl_id.',"-",update_date)',$id_list,false)->where('branch_code',$branch_code)->get($tbl_name)->result();
                    
                }else{
                    $trans_raw = $this->db->select($selected_field)->where('`date_effective` <=','CURDATE()',false)->where('branch_code',$branch_code)->get($tbl_name)->result();
                }
            }else if(in_array($tbl_name, array(''))){
                if($id_list){
                    $trans_raw = $this->db->select($selected_field)->where_not_in($tbl_id,$id_list,false)->where('branch_code',$branch_code)->get($tbl_name)->result();
                    // echo $this->db->last_query();exit;
                }else{
                    // $trans_raw = $this->db->select($selected_field)->where('`update_date` > ',$update_date)->where('branch_code',$branch_code)->get($tbl_name)->result();
                }
            }else{ 
                // $trans_raw = $this->db->select($selected_field)->where('`update_date` > ',$update_date)->where('branch_code',$branch_code)->get($tbl_name)->result();
                if($id_list){
                    $trans_raw = $this->db->select($selected_field)->where_not_in('concat('.$tbl_id.',"-",update_date)',$id_list,false)->where('branch_code',$branch_code)->get($tbl_name)->result();
                    // echo $this->db->last_query();exit;
                }else{
                    $trans_raw = $this->db->select($selected_field)->where('`update_date` > ',$update_date)->where('branch_code',$branch_code)->get($tbl_name)->result();
                }
            }
            

            // echo $this->db->last_query();
            // $new_result = $this->object_flat($trans_raw,$tbl_id);

            $ndata = array(
                'branch_code'=>$data->branch_code,
                'terminal_id'=>$data->terminal_id,
                'results'=>$trans_raw,
                'type'=>$data->type,
                'user_id'=>$data->user_id);

            $master_id = $this->set_master_id((object)$ndata,$tbl_name,$tbl_id);

            echo json_encode(array('success'=>'Ajax request submitted successfully', 'master_id'=>$master_id,'src_id'=>'','result'=>$trans_raw,'hq_list'=>$hq_list,'update_date'=>$now));
        }else{
            $trans_raw = $this->db->select($selected_field)->where('branch_code',$data->branch_code)->where('master_id',null)->get($tbl_name)->result();
            // echo $this->db->last_query();exit;

            $ndata = array(
                'branch_code'=>$data->branch_code,
                'terminal_id'=>$data->terminal_id,
                'results'=>$trans_raw,
                'type'=>$data->type,
                'user_id'=>$data->user_id);

            $master_id = $this->set_master_id((object)$ndata,$tbl_name,$tbl_id);
            
            // $this->db->where('branch_code',$data->branch_code)->where('master_id',null)->update($tbl_name,array('master_id'=>$master_id));

            echo json_encode(array('success'=>'Ajax request submitted successfully', 'master_id'=>$master_id,'src_id'=>'','result'=>$trans_raw,'hq_list'=>$hq_list,'update_date'=>$now));
        }
    }

    public function formulate_object($object=NULL,$add_element=array(),$multi=false,$return_obj = true){
            $new_array = json_decode(json_encode($object), true);

            foreach($add_element as $key => $value){
                if($multi){
                    foreach($new_array as $obj => &$elm){

                        $new_array[$obj][$key] = $value;
                    }
                }else{
                    $new_array[$key] = $value;

                }
            }

            if($return_obj){
                return json_decode(json_encode($new_array));
                
            }else{
                return $new_array;
            }

    }

    function finish_log(){
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $items = array("status"=>$data->status,
                      "type"=>$data->type,
                      "transaction"=> $data->transaction,'user_id'=>$data->user_id,'terminal_id'=> $data->terminal_id ,'branch_code'=>$data->branch_code,'sender_ip_address'=>$data->sender_ip_address);

        $this->db->insert('master_logs',$items);

        echo json_encode(array('master_id'=>$this->db->insert_id()));
    }

    function check_trans_ref(){
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $branch_code = $data->branch_code;
        $tbl_name = $data->table_name;
        $latest_date = $data->latest_date;

        if($latest_date == ''){
            $latest_date = date('Y-m-d');
        }

        $db_date = $this->db->select('max(datetime) as sale_date',false)->where('branch_code',$branch_code)->get('trans_sales')->result();

        $trans_raw = array();
        $id_list = array(0);
        $date = '';
	
        if($db_date){
            $date = date('Y-m-d',strtotime($db_date[0]->sale_date));

            $trans_raw = $this->db->select('concat(sales_id,"-",pos_id) as id',false)
                                // ->where('date(`datetime`)',date('Y-m-d'))
                                ->where('date(`datetime`)',$date)
                                ->where('branch_code',$branch_code)
                                ->where('inactive',1)
                                ->where('type_id',10)
                                ->get('trans_sales')->result();

            if($trans_raw){
                $id_list = $this->array_flat($trans_raw,'id');
            }
        }

        // $trans_raw = $this->db->select('*')->where('date(`datetime`)',date('Y-m-d'))->where('branch_code',$branch_code)->where('type_id',10)->where('`trans_ref` is null ', NULL , false)->get($tbl_name)->result();
        

        echo json_encode(array('data'=>$id_list,'date'=>$date));
    }
    
    function check_migration(){
        $settings = $this->db->get('settings')->result();
        $allow_migration = isset($settings[0]->allow_migration) ? $settings[0]->allow_migration : 0 ;
        echo json_encode(array('allow_migration'=>$allow_migration));
    }
    
    function set_item_master_id(){
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $tbl_name = $data->tbl_name;
        $master_id = $data->master_id;
        $base_id = $data->base_id;
        $id_list = $data->id_list;
        $branch_code = $data->branch_code;
        $update_date = $data->update_date;

        if($id_list){
            $this->db->where('branch_code',$branch_code)->where_in($base_id,$id_list)->update($tbl_name,array('master_id'=>$master_id,'update_date'=>$update_date));
            echo 1;
        }
    }

    function check_transite_files(){
        $files=scandir("uploads/transight");
        $js_rcps = array();
        foreach ($files as $value){
            $file = explode('.',$value);

            if($file[0] != ''){
                $this->migrate_trans_site($value);
                $this->migrate_transite_storezread($value);

                unlink('uploads/transight/'.$value);
            }
        }
    }

    function migrate_trans_site($file_name=''){
        $fh = fopen('uploads/transight/'.$file_name,'r');

        $transite = array();
        $branch_code = '';
        $trans_date = '';
        $sc_disc = 0;
        $pwd_disc = 0;
        $athlete_disc = 0;
        $other_disc = 0;

        while ($line = fgets($fh)) {
            $det = explode(',',$line);           

            switch ($det[0]) {
                case 'SC':
                    $branch_code = trim($det[1]);
                    break;
                case 'BD':
                    $trans_date = $det[1];
                    break;
                case 'G':
                    $transite['gross_sales'] = $det[4];
                    break;
                case 'N':
                    $transite['net_sales'] = $det[4];
                    break;
                case 'VS':
                    $transite['vat_sales'] = $det[4];
                    break;
                case 'VE':
                    $transite['vat_exempt_sales'] = $det[4];
                    break;
                case 'NV':
                    $transite['non_vat_sales'] = $det[4];
                    break; 
                case 'V':
                    $transite['vat'] = $det[4];
                    break;
                
            }
            
            if($det[0] == 'D'){
                if(strpos($det[2], 'SC') !== false){
                    $sc_disc += $det[4]; 
                }elseif(strpos($det[2], 'PWD') !== false){
                    $pwd_disc += $det[4];
                }elseif(strpos($det[2], 'Athlete') !== false){
                    $athlete_disc += $det[4];
                }elseif(strpos($det[2], 'VAT EXEMPT') !== false){
                    $transite['vat_exempt'] = $det[4];
                }else{
                    $other_disc += $det[4];
                }

            }                      
        }

        $transite['branch_code'] = $branch_code;
        $transite['trans_date'] = $trans_date;

        $transite['sc_discount'] = $sc_disc;
        $transite['pwd_discount'] = $pwd_disc;
        $transite['athlete_discount'] = $athlete_disc;
        $transite['other_discount'] = $other_disc;

        $transite_rec = $this->db->get_where('transite_sales',array('trans_date'=>trim($trans_date),'branch_code'=>$branch_code))->result();
       
        if($transite_rec){
            $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales');
            $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales_menus');
            $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales_menu_modifiers');
            $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales_discounts');
            $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales_payments');
        }

        $this->db->insert('transite_sales',$transite);
        $transite_id = $this->db->insert_id();
    
        fclose($fh);

        $fh = fopen('uploads/transight/'.$file_name,'r');

        while ($line = fgets($fh)) {
            $det = explode(',',$line);

            if($det[0] == 'D' && strpos($det[2], 'VAT EXEMPT') === false){ 
                $transite_discount = array(
                    'transite_id' => $transite_id,
                    'disc_code'=>$det[1],
                    'name'=>$det[2],
                    'qty'=>$det[3],
                    'total_amount'=>$det[4],
                    'branch_code'=>$branch_code,
                );

                 $this->db->insert('transite_sales_discounts',$transite_discount);

            }elseif($det[0] == 'P'){
                $transite_payment = array(
                    'transite_id' => $transite_id,
                    'code'=>$det[1],
                    'name'=>$det[2],
                    'qty'=>$det[3],
                    'total_amount'=>$det[4],
                    'branch_code'=>$branch_code,
                );

                $this->db->insert('transite_sales_payments',$transite_payment);
            }elseif($det[0] == 'I'){
                $transite_menu = array(
                    'transite_id' => $transite_id,
                    'menu_code'=>$det[1],
                    'menu_name'=>$det[2],
                    'qty'=>$det[3],
                    'total_amount'=>$det[4],
                    'branch_code'=>$branch_code,
                );

                $this->db->insert('transite_sales_menus',$transite_menu);
            }elseif($det[0] == 'M'){
                // if(!isset($det[3])){
                //     print_r($det);exit;
                // }
                $transite_menu_modifier = array(
                    'transite_id' => $transite_id,
                    'mod_code'=>$det[1],
                    'mod_name'=>$det[2],
                    'qty'=>$det[3],
                    'total_amount'=>$det[4],
                    'branch_code'=>$branch_code,
                );

                $this->db->insert('transite_sales_menu_modifiers',$transite_menu_modifier);
            }


        }
        fclose($fh);
    }

    function migrate_transite_storezread($file_name=''){
        $fh = fopen('uploads/transight/'.$file_name,'r');

        $transite = array();
        $branch_code = '';
        $trans_date = '';
        $sc_disc = 0;
        $pwd_disc = 0;
        $athlete_disc = 0;
        $other_disc = 0;
        $vat_exempt = 0;
        $total_payment = 0;
        $menu_total = 0;
        $menu_qty = 0;
        $mods_total = 0;
        $mods_qty = 0;

        while ($line = fgets($fh)) {
            $det = explode(',',$line);           

            switch ($det[0]) {
                case 'SC':
                    $branch_code = trim($det[1]);
                    break;
                case 'BD':
                    $trans_date = trim($det[1]);
                    break;
                case 'G':
                    $transite['total_sales'] = trim($det[4]);
                    break;
                case 'N':
                    $transite['net_sales'] = trim($det[4]);
                    break;
                case 'VS':
                    $transite['vat_sales'] = trim($det[4]);
                    break;
                case 'VE':
                case 'NV':
                    $transite['vat_exempt_sales'] = trim($det[4]);
                    break;                
                case 'V':
                    $transite['vat'] = trim($det[4]);
                    break;
                
            }
            
            if($det[0] == 'D'){
                if(strpos($det[2], 'SC') !== false){
                    $sc_disc += trim($det[4]); 
                }elseif(strpos($det[2], 'PWD') !== false){
                    $pwd_disc += trim($det[4]);
                }elseif(strpos($det[2], 'Athlete') !== false){
                    $athlete_disc += trim($det[4]);
                }elseif(strpos($det[2], 'VAT EXEMPT') !== false){
                    $vat_exempt = trim($det[4]);
                }else{
                    $other_disc += trim($det[4]);
                }

            }                      
        }

        $transite['branch_code'] = $branch_code;
        $transite['trans_date'] = $trans_date;

        $transite['total_discount'] = $sc_disc+$pwd_disc+$athlete_disc+$other_disc;
        $transite['gross_sales'] = $transite['total_sales']-$vat_exempt;
        $transite['sc_pwd_vat_exempt'] = $vat_exempt;
        $transite['trans_types'] = json_encode(array('takeout'=>array('guest_count'=>1,'amount'=>$transite['net_sales'])));
        $transite['tc_total'] = $transite['net_sales'];
        $transite['guest_count'] = 1;
        $transite['avg'] = $transite['net_sales'];

        // $transite_rec = $this->db->get_where('transite_sales',array('trans_date'=>$trans_date,'branch_code'=>$branch_code))->result();
       
        // if($transite_rec){
        //     $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales');
        //     $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales_menus');
        //     $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales_menu_modifiers');
        //     $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales_discounts');
        //     $this->db->where('transite_id',$transite_rec[0]->transite_id)->delete('transite_sales_payments');
        // }

        // $this->db->insert('transite_sales',$transite);
        // $transite_id = $this->db->insert_id();
    
        fclose($fh);

        $fh = fopen('uploads/transight/'.$file_name,'r');
        

        while ($line = fgets($fh)) {
            $det = explode(',',$line);

            if($det[0] == 'D' && strpos($det[2], 'VAT EXEMPT') === false){ 
                if(isset($transite_discount[$det[1]])){
                    $transite_discount[$det[1]]['qty'] += trim($det[3]);
                    $transite_discount[$det[1]]['amount'] += trim($det[4]);                
                }else{
                    $transite_discount[$det[1]] = array( 
                        'name'=>$det[2],
                        'qty'=>trim($det[3]),
                        'amount'=>trim($det[4]),
                    );
                }

            }elseif($det[0] == 'P'){
                if(isset($transite_payment[$det[1]])){
                    $transite_payment[$det[1]]['qty'] += trim($det[3]);
                    $transite_payment[$det[1]]['amount'] += trim($det[4]); 
                }else{
                    $transite_payment[$det[1]] = array(
                        'name'=>$det[2],
                        'qty'=>trim($det[3]),
                        'amount'=>trim($det[4]),
                    );
                }
                
                $total_payment += $det[4];
            }elseif($det[0] == 'I'){
                if(isset($transite_menu[$det[1]])){
                    $transite_menu[$det[1]]['qty'] += trim($det[3]);
                    $transite_menu[$det[1]]['amount'] += trim($det[4]);
                }else{
                    $transite_menu[$det[1]] = array(
                        'menu_id'=>$det[1],
                        'cat_id'=>0,
                        'name'=>$det[2],
                        'qty'=>trim($det[3]),
                        'amount'=>trim($det[4]),                    
                    );
                }
                
                $menu_qty += trim($det[3]);
                $menu_total += trim($det[4]);                
            }elseif($det[0] == 'M'){
                // if(!isset($det[3])){
                //     print_r($det);exit;
                // }
                if(isset($transite_menu_modifier[$det[1]])){
                    $transite_menu_modifier[$det[1]][0]['qty'] += trim($det[3]);
                    $transite_menu_modifier[$det[1]][0]['total_amt'] += trim($det[4]); 
                }else{
                    $transite_menu_modifier[$det[1]][$det[1]] = array(
                        'menu_id'=>'modifiers',
                        'name'=>$det[2],
                        'qty'=>trim($det[3]),
                        'total_amt'=>trim($det[4]),
                    );
                }
                
                $mods_qty += trim($det[3]);
                $mods_total += trim($det[4]);                
            }


        }
        fclose($fh);

        $transite_menu['modifiers'] = array(
                        'menu_id'=>'modifiers',
                        'cat_id'=>0,
                        'name'=>'Modifiers',
                        'qty'=>$mods_qty,
                        'amount'=>$mods_total,                    
                    );

        $menu_qty += $mods_qty;
        $menu_total += $mods_total;

        $transite['menus']= json_encode($transite_menu);
        $transite['modifiers']= json_encode($transite_menu_modifier);
        $transite['discounts'] = json_encode($transite_discount);
        $transite['payment_types'] = json_encode($transite_payment);
        $transite['total_payment'] = $total_payment;
        $transite['menu_category_qty'] = $menu_qty;
        $transite['menu_category_total']  = $menu_total;        
        $transite['menu_cat_total'] = $menu_total;
        $transite['menu_total_qty'] = $menu_qty;
        $transite['menu_total'] = $menu_total;
        $transite['mods_total'] = $mods_total;
        $transite['menu_categories'] = json_encode(array('0'=>array('cat_id'=>0,'name'=>'FOOD','qty'=>$menu_qty,'amount'=>$menu_total)));
        $transite['sub_cats'] = json_encode(array('0'=>array('name'=>'FOOD','qty'=>$menu_qty,'amount'=>$menu_total)));
        $transite['sub_cat_qty'] = $menu_qty;
        $transite['sub_cat_total']  = $menu_total; 
        $transite['pos_id']  = 1; 
        $transite['hourly_sales'] = '[]';
        
        $storezread_rec = $this->db->select('zread_id')->get_where('store_zread',array('trans_date'=>trim($trans_date),'branch_code'=>$branch_code))->result();

        if($storezread_rec){
            $this->db->where('zread_id',$storezread_rec[0]->zread_id);
            $this->db->where('branch_code',$branch_code);
            $this->db->update('store_zread',$transite);
        }else{
            $store_zread = $this->db->select('max(zread_id) as zread_id',false)->where('branch_code',$branch_code)->get('store_zread')->result();
            
            if($store_zread){
                $transite['zread_id'] = $store_zread[0]->zread_id + 1;
            }else{
                $transite['zread_id'] = 1;
            }
            
            $this->db->insert('store_zread',$transite);
        }
        
    }
}