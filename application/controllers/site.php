<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/dine/reads.php");
class Site extends Reads {
	public function loader($method=null,$params=array()){
        session_start();
        $load = false;
        if(isset($_SESSION['load'])){
	        $load = true;        	
        }
        if($load == false && $method != 'load' || $method != 'get_load' || $method != 'go_load'){
        	if(LOADER)
        		header("Location:".base_url()."site/load");
        	else
        		header("Location:".base_url()."site/login1");
        }
        else
	        call_user_func_array(array($this,$method), $params);
    }
	public function index(){
		$data = $this->syter->spawn('dashboard');
		$data['code'] = "";
		$this->load->view('page',$data);
	}
	public function load(){
		$this->load->helper('site/site_load_helper');
		$data = $this->syter->spawn('load',false,false);
		$data['code'] = makeLoader();
		$data['noNavbar'] = true; /*displays the navbar. Uncomment this line to hide the navbar.*/
		$data['html_bg_class'] = 'lockscreen';
		$data['load_js'] = 'site/load';
		$data['use_js'] = 'loadJs';
		$this->load->view('cashier',$data);
	}
	public function backup(){
		$time = $this->site_model->get_db_now();
		$got_backup=false;
		if (!file_exists("backup/")) {
            mkdir("backup/", 0777, true);
        }
        $fileB = date('Ymd',strtotime($time)).".sql";
        if(!file_exists('backup/'.$fileB)){
			$this->load->dbutil();
			$prefs = array(
				"format" => 'txt',
				'ignore' => array('ci_sessions','logs')
			);
			$backup =& $this->dbutil->backup($prefs); 
			$this->load->helper('file');
			write_file('backup/'.$fileB, $backup);
			$got_backup = true; 
        }
        return $got_backup;
	}
	public function backup_main(){
		$set = $this->cashier_model->get_pos_settings();
		$backup_folder = "C:/xampp/htdocs/dine/backup";
		$extra_backup_folder = "";
		if(iSetObj($set,'backup_path')){
		    $extra_backup_folder = iSetObj($set,'backup_path');
		}
		// if (!file_exists($backup_folder)) { 
		//     $backup_folder = "C:/xampp/htdocs/dine/backup";
		// }    
		$file_path = $backup_folder."/main";
		if (!file_exists($file_path)) {   
		    mkdir($file_path, 0777, true);
		}

		if($extra_backup_folder != ""){
			$exfile_path = $extra_backup_folder."/main";
			if (!file_exists($exfile_path)) {   
			    mkdir($exfile_path, 0777, true);
			}
		}


		$fileB = "main_db.sql";
		$this->db = $this->load->database('main', TRUE);		
		$this->load->dbutil();
		$prefs = array(
		    "format" => 'txt',
		    'ignore' => array('ci_sessions','logs')
		);
		$backup =& $this->dbutil->backup($prefs); 
		$this->db = $this->load->database('default', TRUE);		
		$this->load->helper('file');
		write_file($file_path.'/'.$fileB, $backup);
		if($extra_backup_folder != ""){
			write_file($exfile_path.'/'.$fileB, $backup);
		}
		$backedUp = false;
		if(!file_exists($file_path.'/'.$fileB)){
			$backedUp = true;
		}
		return $backedUp;	
	}	
	public function go_load(){
		session_start();
		$this->load->model('dine/clock_model');
		$this->load->model('site/site_model');
		$this->load->model('dine/cashier_model');
		$this->load->model('dine/setup_model');

		start_load(0,'Loading Database...');
			sleep(1);
			$error = "";
			$time = $this->site_model->get_db_now();
			$details = $this->setup_model->get_branch_details();
			
			$open_time = $details[0]->store_open;
			$close_time = $details[0]->store_close;

			$last_zread_res = $this->cashier_model->get_latest_read_date(Z_READ);
			// echo $this->db->last_query(); die();
			$got_z_read = true;
			$check_date = null;
			if(!empty($last_zread_res)){
			    $check_date = $last_zread_res->maxi;
			    if($last_zread_res->maxi == null)
			        $got_z_read = false;
			}
			else{
			    $got_z_read = false;
			}
			if($check_date != null){
	        	$first_shift = $this->site_model->get_tbl('shifts',array('check_in >'=>$check_date),array('check_in'=>'asc'),null,true,'*',null,1);
	            if(count($first_shift) > 0){
	                $check_date = $first_shift[0]->check_in;
	            }
	            else{
	    		    $shifts_today = $this->cashier_model->get_next_x_read_details($check_date);
	    		    if(count($shifts_today) > 0){
		    		    foreach ($shifts_today as $res) {
		    		        $check_date = $res->scope_from;
		    		        break;
		    		    }		        	
	    		    }
	    		    else{
	    		    	$yesterday = date('Y-m-d',strtotime($time . "-1 days"));
	    		    	$check_date = date('Y-m-d',strtotime($check_date ));
	    		    	$date1 = strtotime($yesterday);
	    		    	$date2 = strtotime($check_date);
	    		    	if($date1 == $date2){
	    		    		$check_date = $time;
	    		    	}
	    		    }
	            }

			}
			else{
			    if($got_z_read){
			        $shifts_today = $this->cashier_model->get_next_x_read_details(date2Sql($time));
			        foreach ($shifts_today as $res) {
			            $check_date = $res->scope_from;
			            break;
			        }
			    }
			    else{
			        $first_shift = $this->site_model->get_tbl('read_details',array('read_type'=>1),array('scope_from'=>'asc'),null,true,'*',null,1);
			        if(count($first_shift) > 0){
			            $check_date = $first_shift[0]->scope_from;
			        }
			    }
			}
			if($check_date == null){
				$first_shift = $this->site_model->get_tbl('shifts',array(),array('check_in'=>'asc'),null,true,'*',null,1);
			    if(count($first_shift) > 0){
			        $check_date = $first_shift[0]->check_in;
			    }
			}
			if($check_date != null){
				$check_date = date2Sql($check_date);
			}
			else{
				#######################################
				## MEANS FIRST TIME THE POS IS OPENED
				update_load(100,'Redirecting...');
				echo json_encode(array('error'=>$error));
				$_SESSION['load'] = true;
				return false;		
			}
			$pos_start = date2SqlDateTime($check_date." ".$open_time);
			$oa = date('a',strtotime($open_time));
			$ca = date('a',strtotime($close_time));
			$pos_end = date2SqlDateTime($check_date." ".$close_time);
			if($oa == $ca){
				$pos_end = date('Y-m-d H:i:s',strtotime($pos_end . "+1 days"));
			}

			$right_now = $time;
			$str_pos_end = strtotime($pos_end);
			$str_pos_now = strtotime($right_now);
			$go_check = false;
			if($str_pos_now > $str_pos_end){
				$go_check = true;
			}
			$got_back_up = false;
			// if($go_check){
			// 	update_load(20,'Checking Shifts...');
			// 		sleep(1);
		 //        	$time2 = $this->site_model->get_db_now();
   //                  $yesterday = date('Y-m-d',strtotime($time2. "-1 days"));
		 //        	$unclosed_shifts = $this->clock_model->get_shift_id(null,null,$yesterday);
		 //        	if(count($unclosed_shifts) > 0){
   //      				update_load(100,'Redirecting...');
			// 			echo json_encode(array('error'=>'unclosed'));        		
			// 			$_SESSION['load'] = true;
			// 			$_SESSION['problem'] = 'unclosed_shifts';
		 //        		return false;				
		 //        	}
		 //        	$unclosed_trans = $this->clock_model->get_open_trans(null,null,$yesterday);
		 //        	if(count($unclosed_trans) > 0){
   //      				update_load(100,'Redirecting...');
			// 			echo json_encode(array('error'=>'unclosed'));        		
			// 			$_SESSION['load'] = true;
			// 			// $_SESSION['problem'] = 'unclosed_transactions';
		 //        		return false;

		 //        		// die('asdfsafsadf');				
		 //        	}		
				update_load(40,'Processing End Of Day...');
					sleep(1);
					$range = createDateRangeArray($check_date,date2Sql($time));
					$ctr = 1;
					
					foreach ($range as $rd) {
						if( date2Sql($time) != date2Sql($rd)){
							if($ctr == 1){
								update_load(50,'Backing Up Database...');
									sleep(1);
									$backup = $this->backup();
									$got_back_up = true;
							}

			        		$read_date = $rd;
			        		$start = date2SqlDateTime($read_date." ".$open_time);
			        		$end = date2SqlDateTime($read_date." ".$close_time);
			        		$oa = date('a',strtotime($open_time));
							$ca = date('a',strtotime($close_time));
			        		if($oa == $ca){
			        			$end = date('Y-m-d H:i:s',strtotime($end . "+1 days"));
			        		}
			        		
			        		$this->cashier_model->db = $this->load->database('default', TRUE);
			        		$this->site_model->db = $this->load->database('default', TRUE);

			        		$zread_id = $this->go_zread($asJson=false,$start,$end,$read_date);
			        		
			        		if(MALL_ENABLED){
				                if(MALL == "robinsons"){
				                    $rob = $this->send_to_rob($zread_id,$increment);
				                    if($rob['error'] == ""){
				                        site_alert("File:".$rob['file']." Sales File successfully sent to RLC server.",'success');
				                    }
				                    else{
				                        site_alert($rob['error'],'error');
				                    }
				                }
				                else if(MALL == "ortigas"){
				                    $this->ortigas_file($zread_id);
				                }
				                else if(MALL == "araneta"){
				                    $this->araneta_file($zread_id);
				                    $last_date = date("Y-m-t", strtotime($read_date));
				                    $now_date = date("Y-m-d", strtotime($read_date));
				                    if($last_date == $now_date){
				                        $this->araneta_month_file($now_date);
				                    }
				                }
				                else if (MALL == 'megamall') {
				                    $this->sm_file($read_date,$zread_id);
				                }
				                else if (MALL == 'stalucia') {
				                    $this->stalucia_file($zread_id);
				                }
				                else if (MALL == 'ayala'){
				                    $this->ayala_file($zread_id);
 								}
 				                else if (MALL == 'cbmall') {
				                    $this->cbmall_file($read_date,$zread_id);
				                }
				                else if (MALL == 'megaworld') {
				                    $this->megaworld_file($zread_id);
				                }
				            }

						}	
						$ctr++;
					}
				
			}
        	// $read_from = $check_date;
	 //        if(strtotime(sql2Date($read_from)) < strtotime(sql2Date($time))){
		//         update_load(60,'Refreshing Database...');
		// 			sleep(1);
		// 			if(!$got_back_up){
		// 				update_load(70,'Backing Up Database Before Clearing...');
		// 					sleep(1);
		// 					$backup = $this->backup();
		// 			}
		// 			else{
		// 				$backup = $got_back_up;
		// 			}
		// 			if($backup){
		// 				$this->load->library('../controllers/dine/main');
		// 				$this->main->remove_recent_data(null,$read_from);
		// 			}		
	 //        }
		// sleep(1);
		// $this->session->unset_userdata('user');

//		if(MALL_ENABLED){
			// if(MALL == "robinsons"){
			// 	update_load(75,'Checking Unset Robinson Files...');	
			// 	sleep(1);
			// 	$rlc = $this->cashier_model->get_rob_path();
		 //        $path =  $rlc->rob_path;
		 //        if($path != ""){
		 //        	$ftp_server = $path;
		 //            $can = (pingAddress($ftp_server));
		 //            if($can){
			//             $unsent = $this->cashier_model->get_unsent_rob_files();
			// 	        if(count($unsent) > 0){
			// 				$ftp_conn = ftp_connect($ftp_server) ;
			// 				if($ftp_conn){
			// 				   update_load(95,'Sending Unsent Files...');
			// 				   sleep(1);
			// 				   foreach ($unsent as $res) {
			// 				        $reads = $this->cashier_model->get_last_z_read(Z_READ,date2Sql($res->date_created));
			// 				        foreach ($reads as $red) {
			// 				            $unsent_id = $red->id;
			// 				        }
			// 				        $rob = $this->send_to_rob($unsent_id,false);
			// 				   }#FOREACH
			// 				}
			//         	}
		 //            }
		 //        }
	  //   	}
	  //   }
	//     update_load(80,'Backing Up Database...');
	// 	$backed_up = $this->backup_main();
	// 	update_load(100,'Redirecting...');
	// 	sleep(1);
	// 	$_SESSION['load'] = true;
	// 	echo json_encode(array('error'=>$error));
	// }
	public function get_load(){
		$load = sess('site_load');
		$text = sess('site_load_text');
		echo json_encode(array('load'=>$load,'text'=>$text));
	}
	public function login($shift=false,$end_shift=false){
		$this->load->model('site/site_model');
		$this->load->model('dine/cashier_model');
		$this->load->model('dine/clock_model');
		$this->load->helper('core/on_screen_key_helper');
		$this->load->helper('dine/login_helper');		
		$this->site_model->delete_tbl('table_activity',array('pc_id'=>PC_ID));
		$data = $this->syter->spawn(null,false);
		if(isset($data['problem']) && $data['problem'] == 'unclosed_shifts'){
			$shift =  true;
		}
        $unclosed_shifts = $this->clock_model->get_shift_id();
        $error = "";
        $rot_shifts = array();
        $shifts_open = array();
        $users = array();
        $rot_users = array();
        $time = $this->site_model->get_db_now();
        $today = sql2Date($time);
        if(count($unclosed_shifts) > 0){
	    	foreach ($unclosed_shifts as $res) {
	    		$check = sql2Date($res->check_in);
    			if(strtotime($check) < strtotime($today)){
    				$rot_users[] = $res->user_id;
    			}
    			else	
		    		$users[] = $res->user_id;
	    	}
	    	if($shift != false){
		    	if(count($rot_users) > 0){
		    		$error = "You must first close the old shifts before starting.";
		    		$rot_shifts = $this->clock_model->get_user_details($rot_users);
		    	}
	    	}
	    	if(count($users) > 0)
		    	$shifts_open = $this->clock_model->get_user_details($users);	    		
		}
        // unset($_SESSION['load']);
		$splashes = $this->site_model->get_image(null,null,'splash_images');

		if(MALL_ENABLED){
			if(MALL == "robinsons"){
				$now = $this->site_model->get_db_now();
				$reads = $this->site_model->get_tbl('read_details',array('read_type'=>2,'read_date'=>date2Sql($now)));
				if(count($reads) > 0){
					$error = "You cant Transact Because you already Have END OF DAY(ZREAD) for this day. Please Contact Robinsons Admin.";
					$_SESSION['problem'] = 'Already Have ZREAD';
				}
			}
		}

		$data['code'] = makeLoginPage($error,$shifts_open,$rot_shifts,$end_shift,$splashes);
		$data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css');
		$data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js','js/virtual_keyboard.js','js/on_screen_keys.js');
		$data['load_js'] = 'site/login';
		$data['use_js'] = 'loginJs';
		$this->load->view('login',$data);
	}
	// public function get_login_unclosed_shifts(){
	// 	$this->load->model('dine/clock_model');
	// 	$unclosed_shifts = $this->clock_model->get_shift_id();
	// 	$unclosed = array();
	// 	$users = array();
	// 	foreach ($unclosed_shifts as $res) {
	// 		if(!isset($unclosed[$res->user_id])){
	// 			$unclosed[$res->user_id] = array(
	// 				'shift_id' => $res->shift_id,
	// 				'check_in' => $res->check_in,
	// 			);
	// 			$users[] = $res->user_id;
	// 		}
	// 	}
	// 	$users_result = $this->clock_model->get_user_details($users);
	// 	foreach ($users_result as $res) {
	// 		if(isset($unclosed[$res->id])){
	// 			$shf = $unclosed[$res->id];
	// 			$shf['username'] = substr($res->username,0,10).'...';
	// 			$shf['name'] = ucwords($res->fname." ".$res->mname." ".$res->lname." ".$res->suffix);
	// 			$unclosed[$res->id] = $shf;
	// 		}
	// 	}
	// 	echo json_encode($unclosed);
	// }
	public function go_login(){
		$this->load->model('site/site_model');
		$this->load->model('dine/clock_model');
		$now = $this->site_model->get_db_now();
		$time = date('H:i',strtotime($now) );

		$open = "9:00";
		$close = "4:00";

		$error_msg = "";
		$send_redirect = "";
		
		$error = 0;
		if(MALL_ENABLED){
			if(MALL == 'robinsons'){
				if(strtotime($open) >= strtotime($time) && strtotime($close) < strtotime($time)){
					$error_msg = "POS is locked in this time. You can open the POS  from 9:00 AM to 4:00 AM next day";
					$error = 1;
				}
				
			}
		}

		if($error == 0){
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$pin = $this->input->post('pin');
			$pin_id = $this->input->post('pin_id');
			$bra = $this->input->post('branch');
			if($pin == ""){
				$error_msg = "Error! Wrong login!";
				echo json_encode(array('error_msg'=>$error_msg));
				return false;
			}
			$user = $this->site_model->get_user_details($pin_id,$username,$password,$pin);

			// echo "<pre>",print_r($user),"</pre>";die();
			$error_msg = null;
			$path = null;
			$send_redirect = null;
			if(!isset($user->id)){
				$error_msg = "Wrong login!";
			}
			else{
				$goSign = true;
				if(CHECK_OIC_ID){
					if($user->user_role_id == OIC_ID){
						$goSign = false;
					}
				}

				if(!$goSign){
					$error_msg = "User Role has no previlage to login.";
					echo json_encode(array('error_msg'=>$error_msg));
					return false;
				}

				$img = base_url().'img/avatar.jpg';
				$result = $this->site_model->get_image(null,$user->id,'users');
	            if(count($result) > 0){
	                $img = base_url().$result[0]->img_path;
	            }
				$session_details['user'] = array(
					"sysid"=>$user->sysid,
					"id"=>$user->id,
					"username"=>$user->username,
					"fname"=>$user->fname,
					"lname"=>$user->lname,
					"mname"=>$user->mname,
					"suffix"=>$user->suffix,
					"full_name"=>$user->fname." ".$user->mname." ".$user->lname." ".$user->suffix,
					"role_id"=>$user->user_role_id,
					"role"=>$user->user_role,
					"access"=>$user->access,
					"img"=>$img,
				);
				$send_redirect = base_url()."dashboard";
				// $send_redirect = base_url()."setup/brands";
				if ($user->user_role_id > 2) {
					$send_redirect = base_url()."shift";				
				}
				if(ORDERING_STATION){
					$send_redirect = base_url()."cashier/tables";				
				}
				$this->session->set_userdata($session_details);
				$this->logs_model->add_logs('login',$user->id,$user->fname." ".$user->mname." ".$user->lname." ".$user->suffix." Logged In.",null);
			}
		}
		echo json_encode(array('error_msg'=>$error_msg,'redirect_address'=>$send_redirect));
	}
	public function go_logout(){
		$user = $this->session->userdata('user');
		$this->logs_model->add_logs('logout',$user['id'],$user['full_name']." Logged Out.",null);
		$this->session->sess_destroy();
		redirect(base_url()."login",'refresh');
	}
	public function end_shift(){
		$user = $this->session->userdata('user');
		$this->logs_model->add_logs('logout',$user['id'],$user['full_name']." Logged Out.",null);
		$this->session->sess_destroy();

		redirect(base_url()."site/login/0/1",'refresh');
	}
	public function site_alerts(){
		$site_alerts = array();
		$alerts = array();
		if($this->session->userdata('site_alerts')){
			$site_alerts = $this->session->userdata('site_alerts');
		}

		foreach ($site_alerts as $alert) {
			$alerts[] = $alert;
		}
		echo json_encode(array("alerts"=>$alerts));
	}
	public function clear_site_alerts(){
		if($this->session->userdata('site_alerts'))
			$this->session->unset_userdata('site_alerts');
	}

	public function execute_migration($ajax=false){
		// var_dump(MASTERMIGRATION);die();
		$has_ic = $this->has_ic(); // check if has internet connection 
		if(MASTERMIGRATION && $has_ic){	

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


	public function master_restartxxx(){
		 $this->load->model('core/admin_model');
         $exec = $this->admin_model->master_restart();
	}

	public function test(){
		// var_dump(MASTERMIGRATION);die();
		// if(MASTERMIGRATION){	

            $this->load->model('core/master_model');
            $rec = $this->master_model->test()[0];
            $data = $rec->src_id;
            $t= json_decode($data,false);
            echo "<pre>",print_r($t),"</pre>";die();
			if($this->master_model->execute_migration()){

			}
        // }
	}


	public function testing_batch(){
		                             $str = exec('mastercall.bat'); 
		var_dump($str);
		echo "asdf";die();
	}


	// check if has internet connection
	public function has_ic()
	{
	    $connected = @fsockopen("www.example.com", 80); 
	                                        //website, port  (try 80 or 443)
	    if ($connected){
	        $is_conn = true; //action when connected
	        fclose($connected);
	    }else{
	        $is_conn = false; //action in connection failure
	    }

	    var_dump($is_conn);die();
	    return $is_conn;

	}

//Nicko
	public function login1(){
		$data = $this->syter->spawn(null,false);
		$data['add_css'] = array('assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css','assets/pages/css/login-4.min.css');
		$data['add_js'] = array('assets/global/plugins/jquery-validation/js/jquery.validate.min.js','assets/global/plugins/jquery-validation/js/additional-methods.min.js','assets/global/plugins/select2/js/select2.full.min.js','assets/global/plugins/backstretch/jquery.backstretch.min.js','assets/pages/scripts/login-4.min.js');

		$data['load_js'] = 'site/login.php';
		$data['use_js'] = 'loginJs1';

		$this->load->view('login2',$data);
		// echo "<pre>",print_r($data),"</pre>";die();
	}
	public function go_login1(){
		$this->load->model('site/site_model');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$pin = $this->input->post('pin');
		// $result = array();
		
		$user = $this->site_model->get_user_details(null,$username,$password,$pin);
		$error_msg = null;
		$img = base_url().'img/avatar.jpg';
		// $result = $this->site_model->get_image(null,$user->id,'users');
		if(!isset($user->id)){
			$error_msg = "Wrong login!";
		}
        // if(count($result) > 0){
        //     $img = base_url().$result[0]->img_path;
        // }

		else{
		$session_details['user'] = array(
					"sysid"=>$user->sysid,
					"id"=>$user->id,
					"username"=>$user->username,
					"fname"=>$user->fname,
					"lname"=>$user->lname,
					"mname"=>$user->mname,
					"suffix"=>$user->suffix,
					"full_name"=>$user->fname." ".$user->mname." ".$user->lname." ".$user->suffix,
					"role_id"=>$user->user_role_id,
					"role"=>$user->user_role,
					"access"=>$user->access,
					"img"=>$img,
				);

		// $send_redirect = base_url()."dashboard";	
		$this->session->set_userdata($session_details);
		$this->logs_model->add_logs('login',$user->id,$user->fname." ".$user->mname." ".$user->lname." ".$user->suffix." Logged In.",null);
		}
		echo json_encode(array('error_msg'=>$error_msg));
		// echo "<pre>",print_r($error_msg),"</pre>";die();
	}
//end++
}
