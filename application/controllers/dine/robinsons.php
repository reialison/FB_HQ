<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Robinsons extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper('dine/robinsons_helper');
		$this->load->model('dine/cashier_model');
		$this->load->model('core/trans_model');
		$this->load->model('dine/setup_model');
		$this->load->model('dine/main_model');
	}
	public function index(){
        // $details = $this->setup_model->get_details(1);
		// $det = $details[0];

        // $files = $this->cashier_model->get_rob_files();
        // $list = $this->cashier_model->get_lastest_z_read(Z_READ);

        $data['code'] = robFiles();
        // $data['code'] = robFiles($list,$det);
        $data['load_js'] = 'dine/robinson.php';
        $data['use_js'] = 'robPageJs';
        $this->load->view('load',$data);
	}
	public function daily_files(){
		$data = $this->syter->spawn(null);
		$data['code'] = dailyFilesPage();
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/robinson.php';
		$data['use_js'] = 'dailyFilesJS';
		$this->load->view('load',$data);
	}
	public function get_z_read(){
		$this->cashier_model->db = $this->load->database('default', TRUE);
		$result = $this->cashier_model->get_last_z_read_on_date(Z_READ,date2Sql($this->input->post('date')));
		$zread_id = 0;
		if(count($result) > 0){
			$res = $result[0];
			$zread_id = $res->id;
		}
		echo json_encode(array('zread_id'=>$zread_id));
	}	
	public function get_file(){
		$this->site_model->db = $this->load->database('default', TRUE);
		$args["DATE(rob_files.date_created) = '".date2Sql($this->input->post('date'))."'"] = array('use'=>'where','val'=>"",'third'=>false);
		$mall_res = $this->site_model->get_tbl('rob_files',$args);
		$mall = array();
		$txtPrint = "";
		if(count($mall_res) > 0){
		    $mall = $mall_res[0];            
		}
		else{
			$txtPrint = "<center style='margin-top:10px;'>File not found.</center>";
			echo json_encode(array('daily'=>$txtPrint));
			return false;
		}
		$txt_file = $mall->file;
		if(file_exists($txt_file)){
			$myFile = $txt_file;
			$fh = fopen($myFile, 'r');
			$print = fread($fh, filesize($myFile));
			fclose($fh);
			$txtPrint = "<pre style='margin:10px;'>".$print."</pre>";
		}
		else{
			$txtPrint = "<center style='margin-top:10px;'>File not found.</center>";
		}
		echo json_encode(array('daily'=>$txtPrint));
	}
	public function settings(){
		$data = $this->syter->spawn(null);
        $details = $this->setup_model->get_details(1);
		$det = $details[0];
		$data['code'] = settingsPage($det);
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/robinson.php';
		$data['use_js'] = 'detailsJs';
		$this->load->view('load',$data);	
	}
	public function details_db(){
		$items = array(
			"rob_tenant_code" => $this->input->post('rob_tenant_code'),
			"rob_path" => $this->input->post('rob_path'),
			"rob_username" => $this->input->post('rob_username'),
			"rob_password" => $this->input->post('rob_password'),
		);
		$this->setup_model->update_details($items, 1);
		$this->main_model->update_tbl('branch_details','branch_id',$items,1);
		$act = 'update';
		$msg = 'Updated Branch Details';
		echo json_encode(array('msg'=>$msg));
	}
}