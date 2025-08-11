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
        
        $arr_tbl = array('trans_sales_charges','trans_sales_discounts','trans_sales_items','trans_sales_local_tax','trans_sales_loyalty_points','trans_sales_menu_modifiers','trans_sales_menu_submodifiers','trans_sales_menus','trans_sales_no_tax','trans_sales_payments','trans_sales_tax','trans_sales_zero_rated','trans_receiving_menu','trans_receiving_menu_details','trans_receivings','trans_receiving_details','trans_adjustments','menu_moves','item_moves','locations','terminals','0_debtors_master','0_cust_allocations','item_serials','0_bank_trans');

        $trans_tbl = array('trans_sales_charges','trans_sales_discounts','trans_sales_items','trans_sales_local_tax','trans_sales_loyalty_points','trans_sales_menu_modifiers','trans_sales_menu_submodifiers','trans_sales_menus','trans_sales_no_tax','trans_sales_payments','trans_sales_tax','trans_sales_zero_rated');
        
        if(!empty($data->trans_sales) && in_array($tbl_name, $trans_tbl)){
            foreach($data->trans_sales as $sale){
                if(isset($sale->pos_id)){
                    $this->db->where('pos_id', $sale->pos_id);   
                }
                $this->db->where('sales_id', $sale->sales_id);

                $this->db->where('branch_code' , $branch_code)->delete($tbl_name);
                
                // echo $this->db->last_query();
            }
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
         
        //   if(!in_array($data->branch_code,array('','LLAOLLAO_SHANGRILA'))){
        //       return false;
        //   }

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
                    $trans_raw = $this->db->select($selected_field)->where('`date_effective` =','CURDATE()',false)->where_not_in('concat('.$tbl_id.',"-",update_date)',$id_list,false)->where('branch_code',$branch_code)->get($tbl_name)->result();
                    
                }else{
                    $trans_raw = $this->db->select($selected_field)->where('`date_effective` =','CURDATE()',false)->where('branch_code',$branch_code)->get($tbl_name)->result();
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

        // $trans_raw = $this->db->select('*')->where('date(`datetime`)',date('Y-m-d'))->where('branch_code',$branch_code)->where('type_id',10)->where('`trans_ref` is null ', NULL , false)->get($tbl_name)->result();
        $trans_raw = $this->db->select('*')
                                // ->where('date(`datetime`)',date('Y-m-d'))
                                ->where('date(`datetime`)',$latest_date)
                                ->where('branch_code',$branch_code)
                                ->where('(`trans_ref` is null || reason is null)', NULL , false)
                                ->get($tbl_name)->result();

        echo json_encode(array('data'=>$trans_raw));
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


}