<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/reads.php");
class Vistamall extends Reads {
	public function __construct(){
		parent::__construct();
		$this->load->helper('dine/vistamall_helper');
		$this->load->model('dine/cashier_model');
		$this->load->model('core/trans_model');
	}
	public function index(){
        $data = $this->syter->spawn(null);
        $data['code'] = vistamallPage();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/vistamall.php';
        $data['use_js'] = 'mainPageJs';
        $this->load->view('load',$data);
	}
	public function daily_files(){
		$data = $this->syter->spawn(null);
		$data['code'] = dailyFilesPage();
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/vistamall.php';
		$data['use_js'] = 'dailyFilesJS';
		$this->load->view('load',$data);
	}
	public function settings(){
		$data = $this->syter->spawn(null);
		$objs = $this->site_model->get_tbl('vistamall');
		$obj = array();
		if(count($objs) > 0){
			$obj = $objs[0];			
		}
		$data['code'] = settingsPage($obj);
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/vistamall.php';
		$data['use_js'] = 'settingsJS';
		$this->load->view('load',$data);	
	}
	public function settings_db(){
		$this->load->model('dine/main_model');
		$items = array(
			'stall_code' => $this->input->post('stall_code'),
			'sales_dep' => $this->input->post('sales_dep'),
			'file_path'   => $this->input->post('file_path'),
		);
		$id = $this->input->post('vistamall_id');
		$this->site_model->update_tbl('vistamall','id',$items,$id);
		$this->main_model->update_tbl('vistamall','id',$items,$id);
		$msg = "Updated Settings.";
		echo json_encode(array('id'=>$id,'msg'=>$msg));
	}
	public function get_file(){
		$date = $this->input->post('date');
		$mall_res = $this->site_model->get_tbl('vistamall');
		$daily_str = "";
		if(count($mall_res) > 0){
			$stall_code = $mall_res[0]->stall_code;
			$sales_dep = $mall_res[0]->sales_dep;
			$file_path = filepathisize($mall_res[0]->file_path);	
			$year = date('Y',strtotime($date));
			$file_path .= $year."/";
			$file = $file = date('mdy',strtotime($date));
			$find_file = $file_path.$file;
			$ctrs = array();
			foreach (glob($find_file."*") as $filefound) {
				if($filefound){
					$ctr_str = str_replace($find_file,'',$filefound);
					$ctr_str = str_replace('.sal','',$ctr_str);
					$ctrs[] = $ctr_str;
				}
			}
			if(count($ctrs) > 0){
				usort($ctrs, function($a, $b) {
			    	return intval($b) - intval($a);
				});
				$ctr = $ctrs[0];
				$file = $find_file.$ctr.".sal";
				if(file_exists($file)){
					$fh = fopen($file, 'r');
					$print = fread($fh, filesize($file));
					fclose($fh);
					$daily_str = "<pre style='margin:10px;'>".$print."</pre>";
				}
				else{
					$daily_str = "<center style='margin-top:10px;'>Mall File not found</center>";
				}
			}	
			else{
				$daily_str = "<center style='margin-top:10px;'>Mall File not found</center>";
			}
		}
		else{
			$daily_str = "<center style='margin-top:10px;'>Mall Settings not found</center>";
		}
		echo json_encode(array('daily'=>$daily_str));
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
			$reg = $this->vista_file($zread,true);
			echo json_encode(array('msg'=>$reg['msg'],'error'=>$reg['error']));
		}
		else{
			echo json_encode(array('msg'=>'No zread found on this date.','error'=>1));
		}
	}
}