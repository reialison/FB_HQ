<?php
	class download_model extends CI_Model{
		public function __construct()
		{
			parent::__construct();
		}

		public function add_file($file, $code){
			$get_code = $this->get_code($code);

			if(!$get_code){
				$this->db->set('file_name',$file);
				$this->db->set('code',$code);

				$this->db->insert('downloads');
			}
			
		}

		public function get_code($code){
			$this->db->select('*');
			$this->db->from('downloads');
			$this->db->where('code',$code);

			$query = $this->db->get();
			
			$result = $query->result();

			return $result;
		}
	}
?>