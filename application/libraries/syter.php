<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Syter{
	var $curr_page = null;
    var $access = null;
    function __construct($config=array()){
        if (count($config) > 0){
			$this->initialize($config);
		}
    }
    function initialize($config = array()){
		foreach ($config as $key => $val){
			if (isset($this->$key)){
				$this->$key = $val;
			}
		}
	}
	function spawn($curr_page=null,$check_login=true,$check_load=true){
		$CI =& get_instance();
		$setup = array();
		if($check_load){
			session_start();
			if(!isset($_SESSION['load'])){
				if(LOADER)
					redirect(base_url().'site/loader','refresh');
			}
			else{
				if(isset($_SESSION['problem']) && $_SESSION['problem'] != ""){
					if($_SESSION['problem'] == 'Already Have ZREAD'){
						$setup['problem'] = "You cant Transact Because you already Have END OF DAY(ZREAD) for this day. Please Contact Robinsons Admin.";
					}
					else
						$setup['problem'] = $_SESSION['problem'];
				}
			}
		}
		$log = array();
		$img = base_url().'img/avatar.jpg';
		if($check_login){
			$log = $this->checkLogin();
			if($log['access'] == "all")
				$access = 'all';
			else
				$access = explode(",",$log['access']);
			$this->access = $access;

			$setup['user'] = $log;
			if(isset($log['img']))
				$img = $log['img'];
		}

		$setup['user_img'] = $img;
		$setup['css'] = $this->initialize_includes($CI->config->item('incCss'),'css');
		$setup['js'] = $this->initialize_includes($CI->config->item('incJs'),'js');

		$menu = $CI->config->item('sideNav');
		// echo "<pre>",print_r($menu),"</pre>";die();
		$setup['sideNav'] = $this->initialize_side_nav($menu);

		$CI->lang->load('en_control_panel', 'english');
		$CI->lang->load('en_num_pad', 'english');

		$page_title = "";
		if($curr_page != null){
			$page = $this->get_current_page($curr_page,$menu);
			$page_title = isset($page['title'])?$page['title']:'';
		}
		$setup['page_title'] = $page_title;

		return $setup;
	}
	function get_navs(){
		$CI =& get_instance();
		return $CI->config->item('sideNav');
	}
	function initialize_includes($incs,$type){
		$includes = "";
		if($type=='css'){
			foreach ($incs as $val) {
				$txt = '<link href="'.base_url().$val.'" rel="stylesheet">';
				$includes .= $txt;
			}
		}
		else{
			foreach ($incs as $val) {
				$txt = '<script src="'.base_url().$val.'" type="text/javascript"  language="JavaScript"></script>';
				$includes .= $txt;
			}
		}
		return $includes;
	}
	function initialize_side_nav($navs){
		$sidemenu = $this->build_menu($navs);
		return $sidemenu;
	}
	function build_menu($navs,$sub=false){
		$menu = "";
		// echo "<pre>",print_r($_SERVER),"</pre>";die();

		if(isset($_SERVER['REDIRECT_URL'])){

			$arr = explode("/",$_SERVER['REDIRECT_URL'] );

			if(isset($arr[0]) && !empty($arr[0])){
				$main_url = $arr[0];
			}else{

				if(isset($arr[1]) && !empty($arr[1])){
					$main_url = $arr[1];
				}
			}
		}else{
			$main_url = "ipos";
		}


		foreach ($navs as $page_key => $nav) {
			$class = ""; 
			if(!is_array($nav['path'])){

				if($this->checkAccess($page_key,$nav)){
					if(isset($_SERVER['REDIRECT_QUERY_STRING']) && "/".$nav['path'] == $_SERVER['REDIRECT_QUERY_STRING'])
						$class = "active";

					$menu .= "<li class='nav-item $class'>";
						$menu .= $this->linkitize($nav,$sub);
					$menu .= "</li>";
				}

			}
			else{
				// echo "<pre>",print_r($nav),"</pre>";
				if($this->checkAccess($page_key,$nav)){
			
					$menu .= "<li class='nav-item'>";
							$menu .= $this->linkitize($nav,$sub);

							$menu .= "<ul class='sub-menu'>";
								$menu .= $this->build_menu($nav['path'],true);
							$menu .= "</ul>";
					$menu .= "</li>";
				}
			}
		}
		// die();
		return $menu;
	}
	function get_current_page($curr_page,$navs){
		$page = array();
		foreach ($navs as $key => $nav) {
			if($key != $curr_page){
				if(is_array($nav['path']))
				$page = $this->get_current_page($curr_page,$nav['path']);
			}
			else{
				$page = $nav;
				break;
			}
		}
		return $page;
	}
	function linkitize($link,$sub=false){
		$text = "";
		$url = "javascript:;";
		if(!is_array($link['path']))
			$url = base_url().$link['path'];
// echo $url;die();
		$text .= "<a href='".$url."'"."class='nav-link nav-toggle'>";
			// if($sub==true)
			// 	$text .= "<i class='fa fa-angle-double-right'></i>";

			$text .= $link['title'];

			if(is_array($link['path']))
				$text .= "<i class='arrow open'></i>";

		$text .= "</a>";

		return $text;
	}
	function checkLogin(){
		$CI =& get_instance();
		if($CI->session->userdata('user')){
			return $CI->session->userdata('user');
		}
		else{
			redirect('login','refresh');
		}
	}
	function checkAccess($pageKey=null,$nav){
		$ret = false;
		$access = $this->access;
		if(is_array($access)){
			
			if(isset($nav['exclude']) && $nav['exclude'] == 0){
				if(in_array($pageKey,$access)){
					$ret = true;
				}
			}
			else{
				$ret = true;
			}
		}
		else{
			$ret = true;
		}
		return $ret;
	}
}

/* End of file Access.php */
/* Location: ./application/libraries/Access.php */