<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/reads.php");
class Eton extends Reads {
	public function __construct(){
		parent::__construct();
		$this->load->helper('dine/eton_helper');
		$this->load->model('dine/cashier_model');
		$this->load->model('core/trans_model');
	}
	public function index(){
        $data = $this->syter->spawn(null);
        $data['code'] = etonPage();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/eton.php';
        $data['use_js'] = 'mainPageJs';
        $this->load->view('load',$data);
	}
	public function daily_files(){
		$data = $this->syter->spawn(null);
		$data['code'] = dailyFilesPage();
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/eton.php';
		$data['use_js'] = 'dailyFilesJS';
		$this->load->view('load',$data);
	}
	public function settings(){
		$data = $this->syter->spawn(null);
		$objs = $this->site_model->get_tbl('eton');
		$obj = array();
		if(count($objs) > 0){
			$obj = $objs[0];			
		}
		$data['code'] = settingsPage($obj);
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/eton.php';
		$data['use_js'] = 'settingsJS';
		$this->load->view('load',$data);	
	}
	public function settings_db(){
		$this->load->model('dine/main_model');
		$items = array(
			'tenant_code' => $this->input->post('tenant_code'),
			'file_path'   => $this->input->post('file_path'),
		);
		$id = $this->input->post('eton_id');
		$this->site_model->update_tbl('eton','id',$items,$id);
		$this->main_model->update_tbl('eton','id',$items,$id);
		$msg = "Updated Settings.";
		echo json_encode(array('id'=>$id,'msg'=>$msg));
	}
	public function get_file(){
		$date = $this->input->post('date');
		$mall_res = $this->site_model->get_tbl('eton');
		$daily_str = "";
		$hourly_str = "";
		$discount_str = "";
		if(count($mall_res) > 0){
			$year = date('Y',strtotime($date));
			$mall = $mall_res[0];			
			$file_path = filepathisize($mall->file_path).$year."/";;
			$tenant_code = $mall->tenant_code;			
			$eod = $this->old_grand_net_total($date);
			$eodnum = $eod['ctr'];
			$daynum = date('d',strtotime($date));
			$monthnum = date('n',strtotime($date));
			if($monthnum == 10) $monthnum = "A"; else if($monthnum == 11) $monthnum = "B";else if($monthnum == 12) $monthnum = "C";
			$filename = $tenant_code.TERMINAL_NUMBER.$eodnum.".".$monthnum.$daynum;	
			// $filename = substr($tenant_code,0,4).TERMINAL_NUMBER.$eodnum.".".$monthnum.$daynum;	
			$daily    = $file_path."S".$filename;
			$hourly   = $file_path."H".$filename;
			$discount = $file_path."D".$filename;
			if(file_exists($daily)){
				$myFile = $daily;
				$fh = fopen($myFile, 'r');
				$print = fread($fh, filesize($myFile));
				fclose($fh);
				$daily_str = "<pre style='margin:10px;'>".$print."</pre>";
			}
			else{
				$daily_str = "<center style='margin-top:10px;'>File not found.</center>";
			}
			if(file_exists($hourly)){
				$myFile = $hourly;
				$fh = fopen($myFile, 'r');
				$print = fread($fh, filesize($myFile));
				fclose($fh);
				$hourly_str = "<pre style='margin:10px;'>".$print."</pre>";
			}
			else{
				$hourly_str = "<center style='margin-top:10px;'>File not found.</center>";
			}
			if(file_exists($discount)){
				$myFile = $discount;
				$fh = fopen($myFile, 'r');
				$print = "";
				if(filesize($myFile) > 0)
					$print = fread($fh, filesize($myFile));
				fclose($fh);
				$discount_str = "<pre style='margin:10px;'>".$print."</pre>";
			}
			else{
				$discount_str = "<center style='margin-top:10px;'>File not found.</center>";
			}			
		}
		else{
			$daily_str = "<center style='margin-top:10px;'>Mall Settings not found</center>";
			$hourly_str = "<center style='margin-top:10px;'>Mall Settings not found</center>";
			$discount_str = "<center style='margin-top:10px;'>Mall Settings not found</center>";
		}
		echo json_encode(array('daily'=>$daily_str,'hourly'=>$hourly_str,'discount'=>$discount_str));
	}
	public function regen_file(){
		$date = $this->input->post('date');
		$rargs["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
		$rargs["read_type"] = 2;
		$select = "read_details.*";
		$this->site_model->db = $this->load->database('default', TRUE);
		$results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
		$zread = 0;
		foreach ($results as $res) {
			$zread = $res->id;
			$read_date = $res->read_date;
		}
		if($zread != 0){
			$reg = $this->eton_file($zread,true);
			echo json_encode(array('msg'=>$reg['msg'],'error'=>$reg['error']));
		}
		else{
			echo json_encode(array('msg'=>'No zread found on this date.','error'=>1));
		}
	}
}