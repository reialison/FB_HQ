<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/reads.php");
class Ortigas extends Reads {
	//TABLES USED 
	// 1. ortigas
	public function __construct(){
		parent::__construct();
		$this->load->helper('dine/ortigas_helper');
		$this->load->model('dine/cashier_model');
		$this->load->model('core/trans_model');
	}
	public function index(){
        $data = $this->syter->spawn(null);
        $data['code'] = ortigasPage();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/ortigas.php';
        $data['use_js'] = 'ortigasPageJs';
        $this->load->view('load',$data);
	}
	public function dailyFileRead(){
		$data = $this->syter->spawn(null);
		// $reads = $this->site_model->get_tbl('ortigas_read_details',array(),array('id'=>'desc'),null,true,'*','read_date');

		// $data['code'] = dailyFileReadPage($reads);
		$data['code'] = filesPage();
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/ortigas.php';
		$data['use_js'] = 'dailyFileReadJS';
		$this->load->view('load',$data);
	}
	public function get_zread_id(){
		$date = $this->input->post('date');
		$rargs["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
		$rargs["read_type"] = 2;
		$select = "read_details.*";
		$this->site_model->db = $this->load->database('default', TRUE);
		$results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
		$zread = 0;
		foreach ($results as $res) {
			$zread = $res->id;
		}
		echo $zread;
	}
	public function file_view($type='hourly'){
		$read_date = $_GET['date'];
		
		$objs = $this->site_model->get_tbl('ortigas');
		$obj = array();
		if(count($objs) > 0){
		    $obj = $objs[0];            
		}
		$tenant_code = $obj->tenant_code;
		$fd = date('mdY',strtotime($read_date));
		$readCTR = $this->cashier_model->get_lastest_z_read(Z_READ,date2Sql($read_date));
		$lastGT_ctr=0;
		if(count($readCTR) > 0){
	        $lastGT_ctr++;
		}
		// $ext = str_pad(($lastGT_ctr+1), 3, "0",STR_PAD_LEFT);
		$ext = str_pad((1), 3, "0",STR_PAD_LEFT);
		$file = $tenant_code.TERMINAL_NUMBER.$fd.".".$ext;
		$year = date('Y',strtotime($read_date));
		$month = date('M',strtotime($read_date));
		if($type == 'hourly'){
			$filename = "ortigas_files/hourly/".$year."/".$month."/"."H".$file;
		}
		else if($type == 'daily'){
			$filename = "ortigas_files/daily/".$year."/".$month."/"."D".$file;
		}
		else{
			$filename = "ortigas_files/invoice/".$year."/".$month."/"."I".$file;
		}
		if(file_exists($filename)){
			$myFile = $filename;
			$fh = fopen($myFile, 'r');
			$theData = fread($fh, filesize($myFile));
			fclose($fh);
			$rows = explode("\r\n", $theData);
			$print = "";
			foreach ($rows as $txt) {
				$print .= substr($txt, 0, 2) . ' ' .substr($txt, 2)."\r\n";
			}

			echo "<pre>".$print."</pre>";
		}
		else{
			echo "File not found.";
		}
	}		
	public function settings(){
		$data = $this->syter->spawn(null);
		$objs = $this->site_model->get_tbl('ortigas');
		$obj = array();
		if(count($objs) > 0){
			$obj = $objs[0];			
		}
		$data['code'] = ortigasSettingsPage($obj);
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/ortigas.php';
		$data['use_js'] = 'ortigasSettingsJs';
		$this->load->view('load',$data);
	}
	public function settings_db(){
		$items = array(
			'tenant_code'=>$this->input->post('tenant_code'),
			'sales_type'=>$this->input->post('sales_type'),
		);
		if ($this->input->post('ortigas_id')) {
			$id = $this->input->post('ortigas_id');
			$this->site_model->update_tbl('ortigas','id',$items,$id);
			$msg = "Updated Settings.";
		} else {
			$id = $this->site_model->add_tbl('ortigas',$items);
			$msg = "Updated Settings";
		}
		echo json_encode(array('id'=>$id,'msg'=>$msg));
	}
	public function regen_all(){
		$reads = $this->site_model->get_tbl('ortigas_read_details',array(),array(),null,true,'*','read_date');
		$ctr=0;
		$trans=0;
		foreach ($reads as $val) {
			$this->ortigas_file($val->zread_id,0,1);
			if($ctr == 2){
				sleep(3);
				echo $trans."<br>";
				$ctr=0;
			}
			$ctr++;
			$trans++;
		}	
	}
}