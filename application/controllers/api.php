<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/dine/reads.php");
class Api extends Reads{
	public function __construct() {
	    parent::__construct();
	}
	
	
	public function migrate_trans(){
        $json = file_get_contents("php://input");
        // $data = json_decode($json);

        $new_file = fopen('json_data.txt', 'w+');
        fwrite($new_file, $json);
        fclose($new_file);

        echo json_encode(array('status'=>'success'));
    }

    public function check_product_key(){ 
        $json = file_get_contents("php://input");
        $data = json_decode($json);

        $this->db->where('pr_key',$data->key);
        $this->db->where('branch_code',$data->branch_code);
        $this->db->where_in('machine_id',$data->machine_id);
        $this->db->where_in('serial_number',$data->serial_number);
        $this->db->where('inactive',0);

        $result = $this->db->get('product_keys')->result();
        $status = $result ? 'valid' : 'invalid';
        $machine_id = $result ? $result[0]->machine_id : '';
        $serial_number = $result ? $result[0]->serial_number : '';

        echo json_encode(array('status'=>$status,'hq_mid'=>$machine_id,'hq_serial_number'=>$serial_number));
    }

    function sample_api(){
        $this->load->model('core/admin_model');

        // $branch = $this->input->get('branch');
        $branch = 'CHOOKS0003';
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

        $tables = array('trans_sales','trans_sales_charges','trans_sales_discounts','trans_sales_items',
                        'trans_sales_local_tax','trans_sales_loyalty_points','trans_sales_menus',
                        'trans_sales_menu_submodifiers','trans_sales_menu_modifiers','trans_sales_no_tax','trans_sales_payments',
                        'trans_sales_tax','trans_sales_zero_rated',                        
                    );

        $return = $this->admin_model->trans_json_file($tables,$branch);
        $file_name = 'export/'.$branch.'_main.txt';
        $new_file = fopen($file_name, 'w+');
        fwrite($new_file, json_encode($return));
        fclose($new_file);

        // echo json_encode(array('file'=>$file_name));

        site_alert('Generation successful!','success');

        
        
        // header("Location:" . base_url() . $file_name);

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename='.basename($file_name));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_name));
        header("Content-Type: text/plain");
        readfile($file);
    }
    
    function json(){
        $result = $this->db->limit(1)->get('trans_sales')->result();
        $result2 = $this->db->limit(1)->get('trans_sales_menus')->result();
        $result3 = $this->db->limit(1)->get('trans_sales_menu_modifiers')->result();

        echo '<pre>'. json_encode(array('trans_sales'=>$result,'trans_sales_menus'=>$result2,'trans_sales_menu_modifiers'=>$result3,),JSON_PRETTY_PRINT);
    }
    
    function tbl($tbl_name = ''){ 
        if($tbl_name != ''){
            ini_set('memory_limit', '-1');
            set_time_limit(3600);
        
            $result = $this->db
                        //   ->like('datetime','2024-05-01')
                           ->get($tbl_name)->result();
            
            echo json_encode(array($tbl_name=>$result));
            
            // $contentArr = str_split(json_encode($result), 65536);
            // foreach ($contentArr as $part) {
            //     echo $part;
            // }
            
            // file_put_contents('test.txt', json_encode(array($tbl_name=>$result)));
            
            // $myfile = fopen("test.txt", "r") or die("Unable to open file!");
            // $result = fread($myfile,filesize("test.txt"));
            // fclose($myfile);
            
            // echo $result;
        }
    }


}