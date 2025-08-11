<?php
class Main_model extends CI_Model {
	var $mb;	
	public function __construct(){
		parent::__construct();
		$this->mb = $this->load->database('default',true);
	}
	public function add_trans_sales_batch($items){
		$this->mb->insert_batch('trans_sales',$items);
		return $this->mb->insert_id();
	}
	public function add_trans_tbl_batch($table_name,$items){
		$this->mb->insert_batch($table_name,$items);
		return $this->mb->insert_id();
	}
	public function add_trans_tbl($table_name,$items){
		$this->mb->insert($table_name,$items);
		return $this->mb->insert_id();
	}
	public function delete_trans_tbl_batch($table_name=null,$args=null){
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->mb->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->mb->$func($col,$val['val']);
					}
				}
				else
					$this->mb->where($col,$val);
			}
		}
		$this->mb->delete($table_name);
	}
	public function delete_trans_tbl($table_name=null,$args=null){
		if(!empty($args)){
			foreach ($args as $col => $val) {
				if(is_array($val)){
					if(!isset($val['use'])){
						$this->mb->where_in($col,$val);
					}
					else{
						$func = $val['use'];
						$this->mb->$func($col,$val['val']);
					}
				}
				else
					$this->mb->where($col,$val);
			}
		}
		$this->mb->delete($table_name);
	}
	public function update_tbl($table_name,$table_key,$items,$id,$truelse=true){
		$this->mb->where($table_key,$id);
		if(!$truelse)
			$this->mb->update($table_name,$items,$truelse);
		else
			$this->mb->update($table_name,$items);
		return $this->mb->last_query();
	}
	public function update_trans_tbl($table_name,$table_key,$items,$id=null,$set=array()){
		if(is_array($table_key)){
			foreach ($table_key as $key => $val) {
				if(is_array($val)){
					$this->mb->where_in($key,$val);
				}
				else
					$this->mb->where($key,$val);
			}
		}
		else{
			if(is_array($id)){
				$this->mb->where_in($table_key,$id);
			}
			else
				$this->mb->where($table_key,$id);
		}
		if(!empty($set)){
			foreach ($set as $key => $val) {
				$this->mb->set($key, $val, FALSE);
			}
		}
		$this->mb->update($table_name,$items);
		return $this->mb->last_query();
	}
}