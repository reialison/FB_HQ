<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/reads.php");
class Ayala extends Reads {
	public function __construct(){
		parent::__construct();
		$this->load->helper('dine/ayala_helper');
		$this->load->model('dine/cashier_model');
		$this->load->model('core/trans_model');
	}
	public function index(){
        $data = $this->syter->spawn(null);
        $data['code'] = ayalaPage();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/ayala.php';
        $data['use_js'] = 'ayalaPageJs';
        $this->load->view('load',$data);
	}
	public function daily_files(){
		$data = $this->syter->spawn(null);
		$data['code'] = dailyFilesPage();
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/ayala.php';
		$data['use_js'] = 'dailyFilesJS';
		$this->load->view('load',$data);
	}
	public function back_track_files(){
		$data = $this->syter->spawn(null);
		$data['code'] = backTrackPage();
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/ayala.php';
		$data['use_js'] = 'backtrackFilesJS';
		$this->load->view('load',$data);
	}
	public function settings(){
		$data = $this->syter->spawn(null);
		$objs = $this->site_model->get_tbl('ayala');
		$obj = array();
		if(count($objs) > 0){
			$obj = $objs[0];			
		}
		$data['code'] = settingsPage($obj);
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/ayala.php';
		$data['use_js'] = 'settingsJS';
		$this->load->view('load',$data);	
	}
	public function settings_db(){
		$this->load->model('dine/main_model');
		$items = array(
			'store_name'=>$this->input->post('store_name'),
			'xxx_no'=>$this->input->post('xxx_no'),
			'contract_no'=>$this->input->post('contract_no'),
			'text_file_path'=>$this->input->post('text_file_path'),
			'dbf_path'=>$this->input->post('dbf_path'),
			'dbf_tenant_name'=>$this->input->post('dbf_tenant_name'),
		);
		$id = $this->input->post('mall_id');
		$this->site_model->update_tbl('ayala','id',$items,$id);
		$this->main_model->update_tbl('ayala','id',$items,$id);
		$msg = "Updated Settings.";
		echo json_encode(array('id'=>$id,'msg'=>$msg));
	}
	public function get_file(){
		$this->site_model->db = $this->load->database('default', TRUE);
		$mall_res = $this->site_model->get_tbl('ayala');
		$mall = array();
		if(count($mall_res) > 0){
		    $mall = $mall_res[0];            
		}
		$date = $this->input->post('date');
		$year = date('Y',strtotime($date));
		$file_path = "C:/AYALA/".$year."/";
		
		$txtPrint = "";
		$txt_file = $file_path.$mall->contract_no.(date('md',strtotime($date) ) ).".txt";
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

		$txtHPrint = "";
		$txth_file = $file_path.$mall->contract_no.(date('md',strtotime($date) ) )."H.txt";
		if(file_exists($txth_file)){
			$myFile = $txth_file;
			$fh = fopen($myFile, 'r');
			$print2 = fread($fh, filesize($myFile));
			fclose($fh);
			$txtHPrint = "<pre style='margin:10px;'>".$print2."</pre>";
		}
		else{
			$txtHPrint = "<center style='margin-top:10px;'>File not found.</center>";
		}
		echo json_encode(array('daily'=>$txtPrint,'hourly'=>$txtHPrint));
	}
	public function regen_file(){
		$date = $this->input->post('date');
		// $date = '04/25/2016';
		$rargs["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
		$rargs["read_type"] = 2;
		$select = "read_details.*";
		$this->site_model->db = $this->load->database('main', TRUE);
		$results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
		$zread = 0;
		foreach ($results as $res) {
			$zread = $res->id;
			$read_date = $res->read_date;
		}
		if($zread != 0){
			$reg = $this->ayala_file($zread,true);
			echo json_encode(array('msg'=>$reg['msg'],'error'=>$reg['error']));
		}
		else{
			echo json_encode(array('msg'=>'No zread found on this date.','error'=>1));
		}
	}
	public function back_track_file(){
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		// $start_date = '05/30/2016';
		// $end_date = '05/31/2016';

		$rargs["DATE(read_details.read_date) >= DATE('".date2Sql($start_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
		$rargs["DATE(read_details.read_date) <= DATE('".date2Sql($end_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);


		$rargs["read_type"] = 2;
		$select = "read_details.*";
		$this->site_model->db = $this->load->database('default', TRUE);
		$results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
		// echo $this->site_model->db->last_query();
		// echo var_dump($results);
		// return false;
		
		$msg = "Files Generated";
		if(count($results) > 0){
			foreach ($results as $res) {
				$zread = $res->id;
				// $read_date = $res->read_date;
				$reg = $this->ayala_file($zread,true);
			}
		}
		echo json_encode(array('msg'=>$msg));
		// if($zread != 0){
		// }
		// else{
		// 	echo json_encode(array('msg'=>'No zread found on this date.','error'=>1));
		// }
	}
}