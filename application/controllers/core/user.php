<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	var $data = null;
	public function index(){
        $this->load->helper('site/site_forms_helper');   
		$result = $this->site_model->get_tbl('user_roles');
        $th = array('Username','Name','Role','Date Registered');
        $data = $this->syter->spawn('user');
        $data['page_title'] = fa('icon-user').' Users';
        $data['code'] = create_rtable('users','id','users-tbl',$th);
        $data['load_js'] = 'site/admin';
        $data['add_css'] = 'css/wowdash.css';
        $data['use_js'] = 'usersListJs';
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $this->load->view('page',$data);
	}
    public function get_users($id=null,$asJson=true){
        $post = array();
        $page = "";

        $joinTables['user_roles'] = array('content'=>'users.role = user_roles.id');
        $select = 'users.*, user_roles.role';
        $items = $this->site_model->get_tbl('users',array(),array('users.id'=>'asc'),$joinTables,true,$select);
        $json = array();
        if(count($items) > 0){
            foreach ($items as $res) {
                $json[$res->id] = array(
                    // "id"=>$res->id,   
                    "username"=>$res->username,   
                    "name"=>$res->fname." ".$res->mname." ".$res->lname." ".$res->suffix,   
                    "role"=>$res->role,   
                    "reg_date"=>sql2Date($res->reg_date),
                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes')
                );
            }
        }
        if($asJson){
            // echo json_encode($json);
            echo json_encode(array('rows'=>$json,'page'=>"",'post'=>$post));
        }
        else{
            return array('rows'=>$json,'page'=>"",'post'=>$post);   
            // return $json;
        }
    }
	public function users_form($ref=null){
        $this->load->helper('core/user_helper');
        $this->load->model('core/user_model');
        $data = $this->syter->spawn('user');
        $user = array();
        $user_branch = array();
        $img = array();
        $data['page_title'] = fa('icon-user').' Add New User';
        if($ref != null){
            $users = $this->user_model->get_users($ref);
            $user_branch = $this->user_model->new_get_user_branches(null,$users[0]->sysid,$users[0]->branch_code);
            // echo "<pre>",print_r($user_branch),"</pre>";die();
            // $user_branch = $this->user_model->get_user_branches(null,null,$ref);
            $user = $users[0];
            $data['page_title'] = fa('icon-user').' '.ucwords(strtolower($user->fname." ".$user->mname." ".$user->lname." ".$user->suffix));
            $result = $this->site_model->get_image(null,$ref,'users');
            if(count($result) > 0){
                $img = $result[0];
            }
        }
        // echo var_dump($user);
        $data['code'] = makeUserForm($user,$img,$user_branch);
        $data['add_css'] = array('css/wowdash.css','js/plugins/typeaheadmap/typeaheadmap.css','css/bootstrap-select/bootstrap-select.css','js/plugins/jquery-multi-select/css/multi-select.css');
        $data['add_js'] = array('js/plugins/typeaheadmap/typeaheadmap.js','js/plugins/bootstrap-select/bootstrap-select.min.js','js/plugins/jquery-multi-select/js/jquery.multi-select.js','js/ui-blockui.min.js','js/jquery.blockui.min.js');
        $data['load_js'] = 'site/admin';
        $data['use_js'] = 'userFormJs';
        $this->load->view('page',$data);
    }
    public function users_db(){
        $this->load->model('core/user_model');
        $this->load->model('dine/main_model');
        $items = array();
        $noError = true;

        $check_pin = $this->site_model->get_tbl('users',array('pin'=>$this->input->post('pin')));
        
        if(LOCALSYNC){
            $this->load->model('core/sync_model');
        }

        // if(count($check_pin) > 0){
        //     if($this->input->post('id')){
        //         if($check_pin[0]->id == $this->input->post('id')){                
        //             $noError = true;
        //         }
        //         else{
        //             $noError = false;
        //             $msg = "Invalid Pin.";
        //         }
        //     }
        //     else{
        //         $noError = false;
        //         $msg = "Invalid Pin.";
        //     }    
        // }
        if(!$noError){
            echo json_encode(array("id"=>"","desc"=>"","act"=>"error",'msg'=>$msg));
            return false;    
        }

        $branch_code = $this->input->post('branch_id');

        if($this->input->post('id')){
            $items = array(
                "fname"=>$this->input->post('fname'),
                "mname"=>$this->input->post('mname'),
                "password"=>md5($this->input->post('password')),
                "lname"=>$this->input->post('lname'),
                "role"=>$this->input->post('role'),
                "suffix"=>$this->input->post('suffix'),
                "gender"=>$this->input->post('gender'),
                "email"=>$this->input->post('email'),
                "pin"=>md5($this->input->post('pin')),
                "inactive"=>(int)$this->input->post('inactive'),
                "branch_code"=> $branch_code != '' ? $branch_code : null,
            );

            $user =$this->user_model->get_users($this->input->post('id'));

            if($user[0]->branch_code != $items['branch_code']){
                $id = $this->user_model->get_last_user_id($items['branch_code']);
                $id += 1;

                $items['id'] = $id;
            }

            $this->user_model->update_users($items,$this->input->post('id'));
            $id = $this->input->post('id');
            $act = 'update';
            $msg = 'Updated User '.$this->input->post('fname').' '.$this->input->post('lname');
            // $this->main_model->add_trans_tbl('users',$items);
            if(empty($items['password'])){
                unset($items['password']);
            }
            $this->main_model->update_tbl('users','id',$items,$id);
            // $this->sync_model->update_users($id);

        }
        else{

            $id = $this->user_model->get_last_user_id($branch_code);
            $id += 1;

            $items = array(
                "id"=>$id,
                "username"=>$this->input->post('uname'),
                "password"=>md5($this->input->post('password')),
                "fname"=>$this->input->post('fname'),
                "mname"=>$this->input->post('mname'),
                "lname"=>$this->input->post('lname'),
                "role"=>$this->input->post('role'),
                "suffix"=>$this->input->post('suffix'),
                "gender"=>$this->input->post('gender'),
                "email"=>$this->input->post('email'),
                "pin"=>$this->input->post('pin'),
                "inactive"=>(int)$this->input->post('inactive'),
                "branch_code"=> $branch_code != '' ? $branch_code : null,
            );

            $id = $this->user_model->add_users($items);
            $act = 'add';
            $msg = 'Added  new User '.$this->input->post('fname').' '.$this->input->post('lname');
            // $users_id = $this->main_model->add_trans_tbl('users',$items);
            // $this->sync_model->add_users($users_id);

        }
        $image = null;
        $ext = null;
        if(is_uploaded_file($_FILES['fileUpload']['tmp_name'])){
            $this->site_model->delete_tbl('images',array('img_tbl'=>'users','img_ref_id'=>$id));
            $info = pathinfo($_FILES['fileUpload']['name']);
            if(isset($info['extension']))
                $ext = $info['extension'];
            $newname = $id.".png";            
            $res_id = $id;
            if (!file_exists("uploads/".$res_id."/")) {
                mkdir("uploads/users/", 0777, true);
            }
            $target = 'uploads/users/'.$newname;
            if(!move_uploaded_file( $_FILES['fileUpload']['tmp_name'], $target)){
                $msg = "Image Upload failed";
            }
            else{
                $new_image = $target;
                $result = $this->site_model->get_image(null,$this->input->post('id'),'users');
                $items = array(
                    "img_path" => $new_image,
                    "img_file_name" => $newname,
                    "img_ref_id" => $id,
                    "img_tbl" => 'users',
                );
                if(count($result) > 0){
                    $this->site_model->update_tbl('images','id',$items,$result[0]->img_id);
                }
                else{
                    $imgid = $this->site_model->add_tbl('images',$items,array('datetime'=>'NOW()'));
                }
            }
            ####
        }
        site_alert($msg,'success');
        echo json_encode(array("id"=>$id,"desc"=>$this->input->post('fname').' '.$this->input->post('lname'),"act"=>$act,'msg'=>$msg));
    }
    public function user_branches_load(){
        $this->load->helper('core/user_helper');
        $this->load->model('core/user_model');
        // echo $mod_group_id;
        // $details = $this->mods_model->get_modifier_group_details(null,$mod_group_id,null,$bcode,true);

        $data['code'] = userBranchesLoad();
        $data['load_js'] = 'site/admin';
        $data['use_js'] = 'userBranchesLoadJs';
        $this->load->view('page',$data);
    }
    public function search_branches(){
        $search = $this->input->post('search');
        $branch_code = $this->input->post('branch_code');
        $this->load->model('core/user_model');
        $found = $this->user_model->search_branches($search,$branch_code);
        $branches = array();
        if(count($found) > 0 ){
            foreach ($found as $res) {
                $branches[] = array('key'=>$res->branch_code." ".$res->branch_name,'value'=>$res->branch_desc);
            }
        }
        echo json_encode($branches);
    }
    public function user_branches_db(){
        $this->load->model('core/user_model');
        $this->load->model('dine/main_model');
        $msg= "";
        $act= "";
        $id= "";
        $now = $this->site_model->get_db_now();
			$strtotime_now = date('Y-m-d H:i:s',strtotime($now));
        $sys_user_id = $this->input->post('sys_user_id');
        $branch_id = $this->input->post('branch_id');
        $branch_txt = $this->input->post('branch_txt');
        // $date_registered = $this->input->post('date_registered');
        // print_r($_POST);
        // $branches_code = $this->items_model->get_branch_detail();

        // if($branch_code != 'null'){
            // echo 'gege';die();
            // foreach ($ex as $branch) {
            $items = array(
                    "sys_user_id" => $sys_user_id,
                    "branch_id" => $branch_id,
                    "date_registered" => $strtotime_now,
                    // "terminal_id" => 1,
                    // "branch_code" => $branch
                );
                $id_det = $this->user_model->get_last_userBranch_id();
                $items['id'] =  $id_det + 1;
                $id = $this->user_model->add_user_branches($items);
                // echo $this->db->last_query();die();
                $act = 'add';
                $msg = 'Added  Branch '.$branch_txt;

       
            $li = $this->make->li(
                    // $this->make->checkbox(null,'dflt_'.$id,0,array('ref'=>$id,'return'=>true))." ".
                    $this->make->span(fa('fa-ellipsis-v'),array('class'=>'handle','return'=>true))." ".
                    $this->make->span($branch_txt,array('class'=>'text','return'=>true))." ".
                    $this->make->A(fa('fa-lg fa-times'),'#',array('return'=>true,'class'=>'del','id'=>'del-'.$id,'ref'=>$id)),
                    array('return'=>true,'id'=>'li-'.$id)
                 );
        

        echo json_encode(array("id"=>$id,"desc"=>$branch_id,"act"=>$act,'msg'=>$msg,'li'=>$li));
    }
    public function remove_user_branch(){
        $this->load->model('core/user_model');
        $test = array();
        // echo "<pre>",print_r($this->input->post('group_mod_id')),"</pre>";die();
        // $this->user_model->update_user_branch_upd_date($test,$this->input->post('sysid'));
        $this->user_model->delete_user_branches($this->input->post('sysid'),$this->input->post('branch_id'));
        // echo $this->db->last_query();die();
        // $this->user_model->delete_modifier_group_details($this->input->post('group_mod_id'));
        $json['msg'] = "Users Branch Deleted.";
        echo json_encode($json);
    }
    /*
        public function index(){
            $this->load->model('core/user_model');
            $this->load->helper('site/site_forms_helper');
            $user_list = $this->user_model->get_users();
            $data = $this->syter->spawn('user');
            $data['code'] = site_list_form("user/users_form","users_form","Users",$user_list,array('fname','mname','lname','suffix'),"id");
            $data['add_js'] = 'js/site_list_forms.js';
            $this->load->view('page',$data);
        }
        public function users_form($ref=null){
            $this->load->helper('core/user_helper');
            $this->load->model('core/user_model');
            $user = array();
            if($ref != null){
                $users = $this->user_model->get_users($ref);
                $user = $users[0];
            }
            // echo var_dump($user);
            $this->data['code'] = makeUserForm($user);
            $this->load->view('load',$this->data);
        }
        public function users_db(){
            $this->load->model('core/user_model');
            $this->load->model('dine/main_model');
            $items = array();

            if($this->input->post('id')){
                $items = array(
                    "fname"=>$this->input->post('fname'),
                    "mname"=>$this->input->post('mname'),
                    "lname"=>$this->input->post('lname'),
                    "role"=>$this->input->post('role'),
                    "suffix"=>$this->input->post('suffix'),
                    "gender"=>$this->input->post('gender'),
                    "email"=>$this->input->post('email'),
                    "pin"=>$this->input->post('pin'),
                );

                $this->user_model->update_users($items,$this->input->post('id'));
                $id = $this->input->post('id');
                $act = 'update';
                $msg = 'Updated User '.$this->input->post('fname').' '.$this->input->post('lname');
                // $this->main_model->add_trans_tbl('users',$items);
                $this->main_model->update_tbl('users','id',$items,$id);
            }
            else{
                $items = array(
                    "username"=>$this->input->post('uname'),
                    "password"=>md5($this->input->post('password')),
                    "fname"=>$this->input->post('fname'),
                    "mname"=>$this->input->post('mname'),
                    "lname"=>$this->input->post('lname'),
                    "role"=>$this->input->post('role'),
                    "suffix"=>$this->input->post('suffix'),
                    "gender"=>$this->input->post('gender'),
                    "email"=>$this->input->post('email'),
                    "pin"=>$this->input->post('pin'),
                );

                $id = $this->user_model->add_users($items);
                $act = 'add';
                $msg = 'Added  new User '.$this->input->post('fname').' '.$this->input->post('lname');
                $this->main_model->add_trans_tbl('users',$items);
            }
            echo json_encode(array("id"=>$id,"desc"=>$this->input->post('fname').' '.$this->input->post('lname'),"act"=>$act,'msg'=>$msg));
        }
     */   
}