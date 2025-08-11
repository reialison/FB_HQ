<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/prints.php");
class Reads extends Prints {
	public function __construct(){
		parent::__construct();
		$this->load->model('dine/cashier_model');
        $this->load->helper('core/string_helper');
        $this->load->helper('dine/login_helper');
        $this->load->model('site/site_model');
    }
    public function manual_send_to_rob(){
        $data = $this->syter->spawn('send_to_rob');
        $files = $this->cashier_model->get_rob_files();
        $data['code'] = robFiles($files);
        $this->load->view('page',$data);
    }
    public function send_to_rob_man($id){
        $file = $this->cashier_model->get_rob_files(null,$id);
        foreach ($file as $res) {
            $reads = $this->cashier_model->get_last_z_read(Z_READ,date2Sql($res->date_created));
            foreach ($reads as $red) {
                $unsent_id = $red->id;
            }
            $rob = $this->send_to_rob($unsent_id);
            if($rob['error'] == ""){
                site_alert("File:".$rob['file']." Sales File successfully sent to RLC server","success");
            }
            else{
                site_alert($rob['error'],"error");
            }
       }
       // redirect(base_url()."reads/manual_send_to_rob",'refresh');
       redirect(base_url()."manager",'refresh');
    }
	public function go_auto_zread(){
		$data = $this->syter->spawn(null,false);
		$data['code'] = makeZreadAutoPage();
		$data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css');
		$data['add_js'] = array('js/on_screen_keys.js','js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js','js/virtual_keyboard.js');
		$data['load_js'] = 'site/login';
		$data['use_js'] = 'autoZreadJs';
		$this->load->view('login',$data);
	}
	public function auto_zread(){
        $tries = 0;
        if($this->session->userdata('rob_sent')){
            $tries = $this->session->userdata('rob_sent');
        }
        $tries += 1;
		$time = $this->site_model->get_db_now();
		
        $txt ="";
        // echo var_dump($unsent);
        $rlc = $this->cashier_model->get_rob_path();
        $path =  $rlc->rob_path;
        if($path != ""){
            $unsent = $this->cashier_model->get_unsent_rob_files();
            if(count($unsent) > 0){
               foreach ($unsent as $res) {
                    
                    $reads = $this->cashier_model->get_last_z_read(Z_READ,date2Sql($res->date_created));
                    foreach ($reads as $red) {
                        $unsent_id = $red->id;
                    }
                    $rob = $this->send_to_rob($unsent_id,false);
                    if($rob['error'] == ""){
                        $txt .= "File:".$rob['file']." Sales File successfully sent to RLC server <br>";
                    }
                    else{
                        $txt .= $rob['error']."<br>";
                    }
               }
            }
        }

        $last_zread = $this->cashier_model->get_last_z_read(Z_READ,date2Sql($time));
		$last_read_date = $last_zread[0]->read_date;
		$range = createDateRangeArray($last_read_date,date2Sql($time));
		
		if(date('Y-m-d',strtotime($time. "-1 days")) != $range[0]){
	        if(count($range) >= 3){
		        // echo "<pre>Processing Recent End of Day Please wait...</pre>";
		        $open_time = '6:00 AM';
		        $close_time = '5:00 AM';
		        $ctr = 1;
		        foreach ($range as $date) {
		        	if(date2Sql($last_read_date) != date2Sql($date) && date2Sql($time) != date2Sql($date)){
		        		$read_date = $date;
		        		if($ctr == 1){
		        			$start = $last_zread[0]->scope_to;
			        		$plus = date('Y-m-d',strtotime($start . "+1 days"));
			        		$end = date2SqlDateTime($plus." ".$close_time);
		        		}
		        		else{
			        		$start = date2SqlDateTime($date." ".$open_time);
			        		$plus = date('Y-m-d',strtotime($start . "+1 days"));
			        		$end = date2SqlDateTime($plus." ".$close_time);
		        		}
                        $ctr++;
                        
                        $zread_id = $this->go_zread($asJson=false,$start,$end,$read_date);
                        if($zread_id){
                            $txt .= "Z Read for ".$read_date." successfully processed. <br>";
                        }
                        if(MALL_ENABLED){
    				        if(MALL == "robinsons"){
                                $rob = $this->send_to_rob($zread_id);
        				        if($rob['error'] == ""){
        				            $txt .= "File:".$rob['file']." Sales File successfully sent to RLC server <br>";
        				        }
        				        else{
        				            $txt .= $rob['error']."<br>";
        				        }
                            }
                            else if(MALL == "ortigas"){
                                
                            }
                        }#####################
                        ### MALL END
                        ######################    
		        		// break;
		        	}	
		        }        	
			}
        }
        $txt .= "Redirecting...<br>";
        // sleep(10);
        echo $txt;
        $this->session->set_userdata('rob_sent',$tries);
        redirect(base_url()."site/login",'refresh');
	}
    public function manual_zread(){
        $start = '2016-12-12 2016-12-12 06:55:17';
        $from = '2016-12-12 22:10:10';
        $read_date = '2016-06-12';
        $this->go_zread(false,$start,$from,$read_date);
    }
    public function manual_send_to_rob_hihi(){
        // $zread_id = 2;
        // $zread_id = 7;
        $zread_id = 8;
        $this->send_to_rob($zread_id);
    }
    public function manual_xread(){
        $this->load->model('dine/clock_model');
        $in = '2016-04-23 16:00:00';
        $out = '2016-04-23 21:00:00';
        $read_date = '2016-04-23'; 
        $shift_id = 19;
        $user_id = 39;
        $read_details = array(
            'read_type' => X_READ,
            'read_date' => $read_date,
            'user_id'   => $user_id,
            'scope_from'=> $in,
            'scope_to'  => $out
        );
        $id = $this->cashier_model->add_read_details($read_details);
        $this->clock_model->update_clockout(array('xread_id'=>$id,'check_out'=>$out),$shift_id);
    }
	public function go_zread($asJson=true,$start=null,$end=null,$read_date=null){
        $date_from = $start;
        $date_to = $end;
        ###########################################################################
        $args["DATE(shifts.check_in) = DATE('".date2Sql($read_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
        $select = "shifts.*";
        $shifts = $this->site_model->get_tbl('shifts',$args,array('check_in'=>'desc'),null,true,$select);
        $shts = array();
        foreach ($shifts as $shft) {
            $shts[] = $shft->shift_id;
        }
        ###########################################################################
        $args =  array(
                    'trans_sales.inactive' => 0,
                    'trans_sales.type_id' => SALES_TRANS,
                    "trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where','val'=>null,'third'=>false)
                  );
        if(count($shts) > 0){
             $args["trans_sales.shift_id"] = array('use'=>'where_in','val'=>$shts,'third'=>false);
        }
        else{
             $args["trans_sales.datetime >="] = $date_from;
             $args["trans_sales.datetime <="] = $date_to;
        }    
        // $args =  array(
        //             'trans_sales.datetime >=' => $date_from,
        //             'trans_sales.datetime <=' => $date_to,
        //             'trans_sales.inactive' => 0,
        //             'trans_sales.type_id' => SALES_TRANS,
        //             "trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where','val'=>null,'third'=>false)
        //         );
        // if($date_from == null){
        //     unset($args['trans_sales.datetime >=']);
        // }
        $orders = $this->cashier_model->get_trans_sales(
            null,
            $args,
            'asc'
        );
        foreach ($orders as $res) {
            $date_from = $res->datetime;
            break;
        }
        $max_date = $start;
        $total = 0;
        foreach ($orders as $val) {
            $total += $val->total_amount;
        }
        $prev_sales = 0;
        if (!empty($max_date)) {
            $resultx = $this->cashier_model->get_last_new_gt(Z_READ,$max_date,$date_from);
            if (!empty($resultx[0]))
                $prev_sales = $resultx[0]->grand_total;
        }
        if($prev_sales == "")
            $prev_sales = 0;

        $new_grand_total = $total+$prev_sales;
        $user_id = 1;
        $read = date2Sql($date_from);
        if($read_date != null){
            $read = date2Sql($read_date);
        }

        // echo "(".$read.")";
        $zread_id = $this->cashier_model->add_read_details(
            array(
                'read_date'   => $read,
                'read_type'   => Z_READ,
                'user_id'     => $user_id,
                'old_total'   => $prev_sales,
                'grand_total' => $new_grand_total,
                'scope_from'  => $date_from,
                'scope_to'    => $date_to
            )
        );

        // TO MAIN SALES
        $this->load->library('../controllers/dine/main');
        update_load(70);
        sleep(1);
        // $this->main->sales_to_main($date_from,$date_to);
        $this->main->sales_to_main($shts);
        $this->main->reads_to_main($zread_id);

        
        if(!$asJson)
            return $zread_id;
    }
    public function detail_zread($zread_id=null,$asJson=false){
        $zread = array(
            'old_gt_amnt'   => 0,
            'grand_total'   => 0,
            'read_date'     => null,
            'id'            => null,
            'user_id'       => null,
            'from'          => null,
            'to'            => null,
            'ctr'           => null
        );
        $lastRead = $this->cashier_model->get_z_read($zread_id);
        if(count($lastRead) > 0){
            foreach ($lastRead as $res) {
                $zread = array(
                    'old_gt_amnt' => $res->old_total,
                    'grand_total' => $res->grand_total,
                    'read_date' => $res->read_date,
                    'id' => $res->id,
                    'user_id'=>$res->user_id,
                    'ctr'=>$res->ctr
                );
                $read_date = $res->read_date;
            }
            $rargs["DATE(read_details.read_date) = DATE('".date2Sql($read_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            $select = "read_details.*";
            $results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
            // echo $this->site_model->db->last_query();
            $args = array();
            $from = "";
            $to = "";
            $datetimes = array();
            foreach ($results as $res) {
                $datetimes[] = $res->scope_from;
                $datetimes[] = $res->scope_to;
                // break;
            }
            usort($datetimes, function($a, $b) {
              $ad = new DateTime($a);
              $bd = new DateTime($b);
              if ($ad == $bd) {
                return 0;
              }
              return $ad > $bd ? 1 : -1;
            });
            foreach ($datetimes as $dt) {
                $from = $dt;
                break;
            }    
            foreach ($datetimes as $dt) {
                $to = $dt;
            }
            $zread['from'] = $from;
            $zread['to'] = $to;
        }
        if(!$asJson){
            return $zread;
        }
        else{
            echo json_encode($zread);
        }
    }
    public function write_file($file,$print_str){
        if(is_array($file)){
            foreach ($file as $name) {
                $fp = fopen($name, "w+");
                fwrite($fp,$print_str);
                fclose($fp);
            }
        }
        else{
            $fp = fopen($file, "w+");
            fwrite($fp,$print_str);
            fclose($fp);
        }
    }
    public function resend_to_rob($zread_id){
        $rob = $this->send_to_rob($zread_id);
        if($rob['error'] == ""){
            site_alert("File:".$rob['file']." Sales File successfully sent to RLC server","success");
        }
        else{
            site_alert($rob['error'],"error");
        }
        // redirect(base_url()."manager",'refresh');
    }
    public function resend_to_rob_two($zread_id){
        $rob = $this->send_to_rob($zread_id);
        if($rob['error'] == ""){
            site_alert("File:".$rob['file']." Sales File successfully sent to RLC server","success");
        }
        else{
            site_alert($rob['error'],"error");
        }
        // redirect(base_url()."manager",'refresh');
    }    
    ##################
    ### MALLS 
    ##################
        public function eton_file($zread_id=null,$regen=false){
            $error = 0;
            $error_msg = null;
            $eton_db = $this->site_model->get_tbl('eton');
            $discount_list = $this->site_model->get_tbl('receipt_discounts',array('inactive'=>0));
            $tenant_code = $eton_db[0]->tenant_code;
            $file_path = filepathisize($eton_db[0]->file_path);
            $zread = $this->detail_zread($zread_id);

            if(isset($zread['read_date']) && $zread['read_date'] != null){
                $dscs = array();
                foreach ($discount_list as $res) {
                    $dscs[$res->disc_code] = array('amount'=>0,'code'=>$res->disc_code,'name'=>$res->disc_name);
                }
                $year = date('Y',strtotime($zread['read_date']));
                $file_path .= $year."/";
                if (!file_exists($file_path)) {   
                    mkdir($file_path, 0777, true);
                }
                ##############################################################################
                ### GET DATA
                    $curr = false;
                    $args['trans_sales.datetime >= '] = $zread['from'];             
                    $args['trans_sales.datetime <= '] = $zread['to'];  
                    $trans = $this->trans_sales($args,$curr);
                    $sales = $trans['sales'];
                    $void = $trans['void'];
                    $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
                    $gross = $trans_menus['gross'];
                    $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
                    $tax_disc = $trans_discounts['tax_disc_total'];
                    $no_tax_disc = $trans_discounts['no_tax_disc_total'];
                    $discounts = $trans_discounts['total'];
                    $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
                    $charges = $trans_charges['total'];
                    $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
                    $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
                    $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
                    $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
                    $payments = $this->payment_breakdown_sales($sales['settled']['ids'],$curr);
                    $eod = $this->old_grand_net_total($zread['from']);
                    $tax = $trans_tax['total'];
                    $no_tax = $trans_no_tax['total'];
                    $zero_rated = $trans_zero_rated['total'];
                    $no_tax -= $zero_rated;
                    $nontaxable = $no_tax - $no_tax_disc;
                    $local_tax = $trans_local_tax['total'];
                    $net = $trans['net'];
                    $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
                    if($less_vat < 0)
                        $less_vat = 0;
                    $types = $trans['types'];
                ##############################################################################
                ### CREATE DAILY FILE
                    $tc = $tenant_code;
                    $str="";$dlmtr="\r\n";
                    $sndisc = $othdisc = 0; # TOTAL SENIOR AND OTHER DISCOUNTS
                        foreach ($trans_discounts['types'] as $code => $disc) {
                            if($code == "SNDISC" || $code == "PWDISC"){
                                $sndisc += $disc['amount'];
                            }
                            else{
                                $othdisc += $disc['amount'];
                            }
                        }                        
                    $cash = $cards = $gcs = 0; # TOTAL CASH,CARDS,GCS 
                        foreach ($payments['payments'] as $pay) {
                            if($pay->amount > $pay->to_pay)
                                $amount = $pay->to_pay;
                            else
                                $amount = $pay->amount;
                            if($pay->payment_type == 'credit' || $pay->payment_type == 'debit')
                                $cards += $amount;
                            else if($pay->payment_type == 'gc')
                                $gcs += $amount;
                            else
                                $cash += $amount;
                        }
                    $grosls = $netsls = 0; # TOTAL GROSS AND NET SALES
                        $netsls += $cash + $cards + $gcs;
                        $grosls += $cash + $cards + $gcs + $sndisc + $othdisc; 
                    $newgt = $eod['old_grand_total'] + $netsls; # NEW END OF DAY
                    $guest = $count = $pernet = 0; # TOTAL GUEST AND TRANSACTION COUNT
                        $types = $trans['types'];
                        foreach ($types as $type => $tp) {
                            foreach ($tp as $id => $opt){
                                if($opt->guest == 0)
                                    $guest += 1;
                                else
                                    $guest += $opt->guest;
                                $count++;
                                $pernet += $opt->total_paid;
                            }
                        }
                    $str .= cc("01".$tc,$dlmtr);
                    $str .= cc("02".TERMINAL_ID,$dlmtr);
                    $str .= cc("03".date("mdY",strtotime($zread['read_date'])),$dlmtr);
                    $str .= cc("04".num($eod['old_grand_total'],2,'',''),$dlmtr);
                    $str .= cc("05".num($newgt,2,'',''),$dlmtr);
                    $str .= cc("06".num($grosls,2,'',''),$dlmtr);
                    $str .= cc("07".num($no_tax,2,'',''),$dlmtr);
                    $str .= cc("08".num($sndisc,2,'',''),$dlmtr);
                    $str .= cc("09".num($othdisc,2,'',''),$dlmtr);                    
                    $str .= cc("10".num(0,2,'',''),$dlmtr);
                    $str .= cc("11".num($tax,2,''),$dlmtr);
                    $str .= cc("12".num($charges,2,'',''),$dlmtr);
                    $str .= cc("13".num($netsls,2,'',''),$dlmtr);
                    $str .= cc("14".num($cash,2,'',''),$dlmtr);
                    $str .= cc("15".num($cards,2,'',''),$dlmtr);
                    $str .= cc("16".num($gcs,2,'',''),$dlmtr);
                    $str .= cc("17".num($void,2,'',''),$dlmtr);
                    $str .= cc("18".num($guest,2,'',''),$dlmtr);
                    $str .= cc("191",$dlmtr);
                    $str .= cc("20".$count,$dlmtr);
                    $str .= cc("2101",$dlmtr);
                    $str .= cc("22".num($pernet,2,'',''),$dlmtr);
                    $eodnum = $eod['ctr'];
                    $daynum = date('d',strtotime($zread['read_date']));
                    $monthnum = date('n',strtotime($zread['read_date']));
                    if($monthnum == 10) $monthnum = "A"; else if($monthnum == 11) $monthnum = "B";else if($monthnum == 12) $monthnum = "C";
                    $daily_str = substr($str,0,(strlen($dlmtr) * -1));
                    $daily_file = "S".$tc.TERMINAL_NUMBER.$eodnum.".".$monthnum.$daynum;
                    // $daily_file = "S".substr($tc,0,4).TERMINAL_NUMBER.$eodnum.".".$monthnum.$daynum;
                ##############################################################################
                ### CREATE HOURLY FILE
                    $hour_code = array(1=>array('start'=>'01:01','end'=>'02:00'),2=>array('start'=>'02:01','end'=>'03:00'),
                                       3=>array('start'=>'03:01','end'=>'04:00'),4=>array('start'=>'04:01','end'=>'05:00'),
                                       5=>array('start'=>'05:01','end'=>'06:00'),6=>array('start'=>'06:01','end'=>'07:00'),
                                       7=>array('start'=>'07:01','end'=>'08:00'),8=>array('start'=>'08:01','end'=>'09:00'),
                                       9=>array('start'=>'09:01','end'=>'10:00'),10=>array('start'=>'10:01','end'=>'11:00'),
                                       11=>array('start'=>'11:01','end'=>'12:00'),12=>array('start'=>'12:01','end'=>'13:00'),
                                       13=>array('start'=>'13:01','end'=>'14:00'),14=>array('start'=>'14:01','end'=>'15:00'),
                                       15=>array('start'=>'15:01','end'=>'16:00'),16=>array('start'=>'16:01','end'=>'17:00'),
                                       17=>array('start'=>'17:01','end'=>'18:00'),18=>array('start'=>'18:01','end'=>'19:00'),
                                       19=>array('start'=>'19:01','end'=>'20:00'),20=>array('start'=>'20:01','end'=>'21:00'),
                                       21=>array('start'=>'21:01','end'=>'22:00'),22=>array('start'=>'22:01','end'=>'23:00'),
                                       23=>array('start'=>'23:01','end'=>'00:00'),24=>array('start'=>'00:01','end'=>'01:00'),
                                      );
                    $str="";$dlmtr="\r\n";
                    $str .= cc("01".$tc,$dlmtr);
                    $str .= cc("02".TERMINAL_ID,$dlmtr);
                    $str .= cc("03".date("mdY",strtotime($zread['read_date'])),$dlmtr);
                    $hrsales = array();
                    if(count($trans['all_orders']) > 0){
                        foreach ($trans['all_orders'] as $sales_id => $val) {
                            
                            if($val->type_id == SALES_TRANS && $val->trans_ref != "" && $val->inactive == 0){
                                if($val->guest == 0)
                                    $guest = 1;
                                else
                                    $guest = $val->guest;
                                $time = DateTime::createFromFormat('Y-m-d H:i:s',$val->datetime);
                                foreach ($hour_code as $hcode => $range) {
                                    $start = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['start'].":00");
                                    $end = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['end'].":00");
                                    if($time >= $start && $time <= $end){
                                        if(!isset($hrsales[$hcode])){
                                            $hrsales[$hcode] = array('amount'=>$val->total_paid,'guest'=>$guest,'count'=>1);
                                        }
                                        else{
                                            $hr = $hrsales[$hcode];
                                            $hr['amount'] += $val->total_paid;
                                            $hr['guest'] += $guest;
                                            $hr['count'] += 1;
                                            $hrsales[$hcode] = $hr;
                                        }
                                    }
                                }
                            }

                        }    
                    }    
                    $nethr   = 0;
                    $counthr = 0;
                    $guesthr = 0;
                    if(count($hrsales) > 0){
                        foreach ($hrsales as $code => $hr) {
                            $str .= cc("04".$code,$dlmtr);
                            $str .= cc("05".num($hr['amount'],2,'',''),$dlmtr);
                            $str .= cc("06".$hr['count'],$dlmtr);
                            $str .= cc("07".$hr['guest'],$dlmtr);
                            $nethr   += $hr['amount'];
                            $counthr += $hr['count'];
                            $guesthr += $hr['guest'];
                        }
                    }
                    $str .= cc("08".num($nethr,2,'',''),$dlmtr);    
                    $str .= cc("09".$counthr,$dlmtr);    
                    $str .= cc("10".$guesthr,$dlmtr);    
                    $eodnum = $eod['ctr'];
                    $daynum = date('d',strtotime($zread['read_date']));
                    $monthnum = date('n',strtotime($zread['read_date']));
                    if($monthnum == 10) $monthnum = "A"; else if($monthnum == 11) $monthnum = "B";else if($monthnum == 12) $monthnum = "C";
                    $hourly_str = substr($str,0,(strlen($dlmtr) * -1));
                    $hourly_file = "H".$tc.TERMINAL_NUMBER.$eodnum.".".$monthnum.$daynum;
                ##############################################################################
                ### CREATE DISCOUNT FILE
                    $str="";$dlmtr="\r\n";
                   
                    foreach ($trans_discounts['types'] as $code => $disc) {
                        if(isset($dscs[$code])){
                            $d = $dscs[$code];
                            $d['amount'] += $disc['amount'];
                            $dscs[$code] = $d;
                        }   
                    }
                    
                    // foreach ($trans_discounts['types'] as $code => $disc) {
                    //     $str .= cc(strtoupper($code),',');
                    //     $str .= cc(strtoupper(substr($disc['name'],0,25)),',');
                    //     $str .= cc(numInt($disc['amount']),',');
                    //     $str = substr($str,0,-1);
                    //     $str .= $dlmtr;
                    // }
                    foreach ($dscs as $code => $disc) {
                        $str .= cc(strtoupper($code),',');
                        $str .= cc(strtoupper(substr($disc['name'],0,25)),',');
                        $str .= cc(numInt($disc['amount']),',');
                        $str = substr($str,0,-1);
                        $str .= $dlmtr;
                    }
                    $eodnum = $eod['ctr'];
                    $daynum = date('d',strtotime($zread['read_date']));
                    $monthnum = date('n',strtotime($zread['read_date']));
                    if($monthnum == 10) $monthnum = "A"; else if($monthnum == 11) $monthnum = "B";else if($monthnum == 12) $monthnum = "C";
                    $disc_str = substr($str,0,(strlen($dlmtr) * -1));
                    $disc_file = "D".$tc.TERMINAL_NUMBER.$eodnum.".".$monthnum.$daynum;
                ##############################################################################
                $this->write_file($file_path.$daily_file,$daily_str);
                $this->write_file($file_path.$hourly_file,$hourly_str);
                $this->write_file($file_path.$disc_file,$disc_str);
                // echo "<pre>".$daily_str."</pre>";
                // echo "<pre>".$hourly_str."</pre>";
                // echo "<pre>".$disc_str."</pre>";
                if($regen){
                    $error = 0;
                    $msg = "ETON File successfully created.";
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert("ETON File successfully created.","success");
                }
            } 
            else{
                $error = 1;
                $msg = "No Zread Found.";
                if($regen){
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert($msg,"error");
                }
            }
        }   
                public function ayala_file($zread_id=null,$regen=false){ 
            $zread = $this->detail_zread($zread_id);
            $error = 0;
            $error_msg = null;
            $print_str = "";
            $txt_headers = array('TRANDATE','OLDGT','NEWGT',
                             'DLYSALE','TOTDISC','TOTREF',
                             'TOTCAN','VAT','TENTNAME',
                             'BEGINV','ENDINV','BEGOR',
                             'ENDOR','TRANCNT','LOCALTX',
                             'SERVCHARGE','NOTAXSALE','RAWGROSS',
                             'DLYLOCTAX','OTHERS','TERMNUM');
            if(isset($zread['read_date']) && $zread['read_date'] != null){
                ###########################
                ### GET DETAILS
                    $mall_res = $this->site_model->get_tbl('ayala');
                    $mall = array();
                    if(count($mall_res) > 0){
                        $mall = $mall_res[0];            
                    }
                    $year = date('Y',strtotime($zread['read_date']));
                    $file_path = "C:/AYALA/".$year."/";
                    if (!file_exists($file_path)) {   
                        mkdir($file_path, 0777, true);
                    }

                    $curr = false;
                    $args['trans_sales.datetime >= '] = $zread['from'];             
                    $args['trans_sales.datetime <= '] = $zread['to'];  
                    $trans = $this->trans_sales($args,$curr);
                    $sales = $trans['sales'];
                    $void = $trans['void'];
                    $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
                    $gross = $trans_menus['gross'];
                    $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
                    $tax_disc = $trans_discounts['tax_disc_total'];
                    $no_tax_disc = $trans_discounts['no_tax_disc_total'];
                    $discounts = $trans_discounts['total'];
                    $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
                    $charges = $trans_charges['total'];
                    $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
                    $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
                    $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
                    $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);

                    $tax = $trans_tax['total'];
                    $no_tax = $trans_no_tax['total'];
                    $zero_rated = $trans_zero_rated['total'];
                    $no_tax -= $zero_rated;
                    $nontaxable = $no_tax - $no_tax_disc;
                    $local_tax = $trans_local_tax['total'];
                    $gt = $this->old_grand_net_total($zread['from']);
                    $net = $trans['net'];
                    $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
                    // $less_vat = $trans_discounts['vat_exempt_total'];
                    if($less_vat < 0)
                        $less_vat = 0;
                    $types = $trans['types'];
                    $types_total = array();
                    $guestCount = 0;
                    foreach ($types as $type => $tp) {
                        foreach ($tp as $id => $opt){
                            if(isset($types_total[$type])){
                                $types_total[$type] += $opt->total_amount;
                            }
                            else{
                                $types_total[$type] = $opt->total_amount;
                            }
                            if($opt->guest == 0)
                                $guestCount += 1;
                            else
                                $guestCount += $opt->guest;
                        }
                    }
                    $trans_count = 0;
                    foreach ($types_total as $typ => $tamnt) {
                        $trans_count += count($types[$typ]);
                    }    
                    $vo = count($sales['void']['orders']);
                    $co = count($sales['cancel']['orders']);
                    $begor = 0;
                    $endor = 0;
                    $first_inv = array();
                    $last_inv = array();
                    $first_ref = 0;
                    $last_ref = 0;
                    $first_val = 0;
                    $last_val = 0;
                    $invs = array();
                    foreach ($trans['all_orders'] as $ord) {
                        if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
                            $ref = $ord->trans_ref;
                            if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
                                list($all, $prefix, $number, $postfix) = $result;
                                $ref_val = intval($number);
                                $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
                            }
                        }
                    }
                    ksort($invs);
                    $first_inv = reset($invs);
                    $last_inv = end($invs);
                    if(count($first_inv) > 0){
                        $first_ref = $first_inv['ref'];
                        $first_val = $first_inv['val'];
                    }
                    if(count($last_inv) > 0){
                        $last_ref = $last_inv['ref'];
                        $last_val = $last_inv['val'];
                    }
                    if($trans_count > 0){
                        $trans_count = ($last_val - $first_val) + 1; 
                    }
                    if($first_ref == ""){
                        $first_ref = 0;
                    }
                    if($last_ref == ""){
                        $last_ref = 0;
                    }
                    $print['trandate'] = sql2Date($zread['read_date']);
                    $print['oldgt'] = numInt($gt['old_grand_total']);
                    $print['newgt'] = numInt(0);#DOWN
                    $print['dlysale'] = numInt(0);#DOWN
                    $print['totdisc'] = numInt($discounts);
                    $print['totref'] = numInt(0);
                    $print['totcan'] = numInt($void);
                    $print['vat'] = numInt(0);#DOWN
                    $print['tentname'] = $mall->store_name;
                    // $print['beginv'] = iSetObj($trans['first_ref'],'trans_ref',0);
                    $print['beginv'] = $first_ref;
                    // $print['endinv'] = iSetObj($trans['last_ref'],'trans_ref',0);
                    $print['endinv'] = $last_ref;
                    // $print['begor'] = iSetObj($trans['first_ref'],'sales_id',0);
                    $print['begor'] = $begor;
                    // $print['endor'] = iSetObj($trans['last_ref'],'sales_id',0);
                    $print['endor'] = $endor;

                    $print['trancnt'] = $trans_count;
                    $print['localtx'] = numInt($local_tax);
                    $print['servcharge'] = numInt($charges);
                    $print['notaxsale'] = numInt($nontaxable);
                    $print['rawgross'] = numInt($gross + $charges + $void);
                    $print['dlyloctax'] = numInt(0);#DOWN
                    $print['others'] = $guestCount;
                    $print['termnum'] = TERMINAL_ID;
                    ## $print['totcan'] -
                    $print['vat'] = numInt(($print['rawgross'] - $print['totdisc'] - $print['totcan'] - $print['totref'] -  $print['servcharge'] - $print['notaxsale'] - $print['localtx'] - numInt($less_vat)) * (1/9.333333));
                    $print['dlysale'] = numInt($print['rawgross'] - $print['totdisc'] - $print['totcan'] - $print['totref'] - $print['servcharge'] - $print['vat'] - $less_vat + $print['localtx']);
                    $print['dlyloctax'] = numInt($print['dlysale'] - $print['localtx']);
                    
                    $print['rawgross'] = numInt($print['rawgross'] - $less_vat);
                    $print['newgt'] = numInt($print['oldgt'] + $print['dlysale'] + $print['vat']);
                    // $print['dlysale'] = numInt($print['dlysale'] + $less_vat);
                ###########################
                ### WRITE DAILY FILE
                    foreach ($txt_headers as $txth) {
                        $print_str .= $txth.",";
                    }
                    $print_str = substr($print_str,0,-1);
                    $print_str .= "\r\n";
                    foreach ($print as $name => $val) {
                        $print_str .= $val.",";    
                    }
                    $print_str = substr($print_str,0,-1);

                    $dbfheaders = array(array('TRANDATE','D',8),
                                        array('OLDGT','N',15,2),
                                        array('NEWGT','N',15,2),
                                        array('DLYSALE','N',11,2),
                                        array('TOTDISC','N',11,2),
                                        array('TOTREF','N',11,2),
                                        array('TOTCAN','N',11,2),
                                        array('VAT','N',11,2),
                                        array('TENTNAME','C',15),
                                        array('BEGINV','N',15,0),
                                        array('ENDINV','N',15,0),
                                        array('BEGOR','N',15,0),
                                        array('ENDOR','N',15,0),
                                        array('TRANCNT','N',9,0),
                                        array('LOCALTX','N',11,2),
                                        array('SERVCHARGE','N',11,2),
                                        array('NOTAXSALE','N',11,2),
                                        array('RAWGROSS','N',11,2),
                                        array('DLYLOCTAX','N',11,2),
                                        array('OTHERS','N',11,0),
                                        array('TERMNUM','N',3,0));

                    $dbf_file = $file_path.$mall->xxx_no.(date('md',strtotime($zread['read_date']) ) ).".dbf";
                    if (dbase_create($dbf_file, $dbfheaders)) {
                        $dbase = dbase_open($dbf_file,2);
                        if($dbase){
                            $row = array();
                            foreach ($print as $key => $row_val) {
                                if($key == 'trandate'){
                                    $row[] = date('Ymd',strtotime($row_val));
                                }
                                else
                                    $row[] = $row_val;
                            }
                            $dadd = dbase_add_record($dbase,$row);
                            if($dadd){
                                $txt_file = $file_path.$mall->contract_no.(date('md',strtotime($zread['read_date']) ) ).".txt";
                                $this->write_file(array($txt_file),$print_str);
                            }
                            dbase_close($dbase);
                        }
                    }                    
                ###########################
                ### WRITE HOURLY FILE
                    $dates = array();
                    $txt_headers = array('TRANDATE','HOUR','SALES','TRANCNT','TENTNAME','TERMNUM');
                    $print_str = "";
                    foreach ($txt_headers as $txth) {
                        $print_str .= $txth.",";
                    }
                    $print_str = substr($print_str,0,-1);
                    $print_str .= "\r\n";
                    $ranges = array();
                    foreach (unserialize(TIMERANGES) as $ctr => $time) {
                        $key = date('H',strtotime($time['FTIME']));
                        $ranges[$key] = array('start'=>date("H:i", strtotime($time['FTIME'])),'end'=>date("H:i", strtotime($time['TTIME'])),'tc'=>0,'net'=>0);
                    }
                    $printH = array();
                    // if(count($sales['settled']['orders']) > 0){
                    if(count($trans['all_orders']) > 0){
                        // foreach ($sales['settled']['orders'] as $sales_id => $val) {
                        foreach ($trans['all_orders'] as $sales_id => $val) {
                            $dates[date2Sql($val->datetime)]['ranges'] = $ranges;
                        }
                        $hids = array();
                        foreach ($trans['all_orders'] as $aod) {
                            $hids[] = $aod->sales_id;
                        }

                        $chs = $this->site_model->get_tbl('trans_sales_charges',array("trans_sales_charges.sales_id"=>$hids));
                        $chrgs = array();
                        if(count($chs) > 0){
                            foreach ($chs as $res) {
                                if(isset($chrgs[$res->sales_id])){
                                    $chrgs[$res->sales_id] += $res->amount;
                                }else{
                                    $chrgs[$res->sales_id] = $res->amount;  
                                }
                            }
                        }
                        // $txs = $this->site_model->get_tbl('trans_sales_tax',array("trans_sales_tax.sales_id"=>$sales['settled']['ids']));
                        // $taxes = array();
                        // if(count($txs) > 0){
                        //     foreach ($txs as $res) {
                        //       $taxes[$res->sales_id] = $res->amount;  
                        //     }
                        // }
                        // foreach ($sales['settled']['orders'] as $sales_id => $val) {
                        foreach ($trans['all_orders'] as $sales_id => $val) {
                            if($val->type_id == SALES_TRANS && $val->trans_ref != ""){
                                if(isset($dates[date2Sql($val->datetime)])){
                                    $date_arr = $dates[date2Sql($val->datetime)];
                                    $range = $date_arr['ranges'];
                                    $H = date('H',strtotime($val->datetime));
                                    if(isset($range[$H])){
                                        if($val->inactive == 0){
                                            $mount = $val->total_amount;
                                            if(isset($chrgs[$val->sales_id])){
                                                $mount -= $chrgs[$val->sales_id];
                                            }
                                        }
                                        else{
                                            $mount = 0;
                                        }
                                        $r = $range[$H];
                                        $r['date'] = $val->datetime;
                                        $r['tc'] += 1;
                                        $r['net'] += $mount;
                                        $range[$H] = $r;
                                    }
                                    else{
                                        $r = array();
                                        $r['date'] = $val->datetime;
                                        $r['tc'] = 0;
                                        $r['net'] = 0;
                                        $range[$H] = $r;  
                                    }
                                    $dates[date2Sql($val->datetime)]['ranges'] = $range;
                                }
                            }

                        }
                        // echo var_dump($dates);
                        // return false;
                        $time = array("06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00",
                                        "14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00",
                                        "23:00");
                        $timeNxt = array("00:00","01:00","02:00","03:00","04:00","05:00");
                        $dates = array_reverse($dates);
                        foreach ($dates as $date => $val) {
                            $ranges = $val['ranges'];
                            foreach ($time as $tm) {
                                foreach ($ranges as $key => $ran) {
                                    if($ran['start'] == $tm){
                                        $print_str .= sql2Date($date).",";
                                        $print_str .= $ran['start'].",";
                                        $print_str .= numInt($ran['net']).",";
                                        $print_str .= $ran['tc'].",";
                                        $print_str .= $mall->store_name.",";
                                        $print_str .= TERMINAL_ID.",";
                                        $print_str = substr($print_str,0,-1);
                                        $print_str .= "\r\n";
                                        $printH[] = array(date('Ymd',strtotime($date)),
                                                          $ran['start'],
                                                          numInt($ran['net']),
                                                          $ran['tc'],
                                                          $mall->store_name,
                                                          TERMINAL_ID);
                                    }
                                }
                            }
                            break;
                        }  
                        $tomorrow = date('Y-m-d',strtotime($date . "+1 days"));    
                        if(isset($dates[$tomorrow])){
                            $val = $dates[$tomorrow];
                            $ranges = $val['ranges'];
                            foreach ($timeNxt as $tm) {
                                foreach ($ranges as $key => $ran) {
                                    if($ran['start'] == $tm){
                                        $print_str .= sql2Date($tomorrow).",";
                                        $print_str .= $ran['start'].",";
                                        $print_str .= numInt($ran['net']).",";
                                        $print_str .= $ran['tc'].",";
                                        $print_str .= $mall->store_name.",";
                                        $print_str .= TERMINAL_ID.",";
                                        $print_str = substr($print_str,0,-1);
                                        $print_str .= "\r\n";
                                        $printH[] = array(date('Ymd',strtotime($tomorrow)),
                                                          $ran['start'],
                                                          numInt($ran['net']),
                                                          $ran['tc'],
                                                          $mall->store_name,
                                                          TERMINAL_ID);
                                    }
                                }
                            }
                        }
                        else{
                            foreach ($timeNxt as $tm) {
                                $print_str .= sql2Date($tomorrow).",";
                                $print_str .= $tm.",";
                                $print_str .= numInt(0).",";
                                $print_str .= "0,";
                                $print_str .= $mall->store_name.",";
                                $print_str .= TERMINAL_ID.",";
                                $print_str = substr($print_str,0,-1);
                                $print_str .= "\r\n";
                                $printH[] = array(date('Ymd',strtotime($tomorrow)),
                                                          $tm,
                                                          numInt(0),
                                                          0,
                                                          $mall->store_name,
                                                          TERMINAL_ID);
                            }
                        }
                    }
                    else{
                        $time = array("06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00",
                                        "14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00",
                                        "23:00");
                        $timeNxt = array("00:00","01:00","02:00","03:00","04:00","05:00");
                        foreach ($time as $tm) {
                            $print_str .= sql2Date($zread['read_date']).",";
                            $print_str .= $tm.",";
                            $print_str .= num(0).",";
                            $print_str .= "0,";
                            $print_str .= $mall->store_name.",";
                            $print_str .= TERMINAL_ID.",";
                            $print_str = substr($print_str,0,-1);
                            $print_str .= "\r\n";
                            $printH[] = array(date('Ymd',strtotime($zread['read_date'])),
                                                      $tm,
                                                      numInt(0),
                                                      0,
                                                      $mall->store_name,
                                                      TERMINAL_ID);
                        }
                        $tomorrow = date('Y-m-d',strtotime($zread['read_date'] . "+1 days"));  
                        foreach ($timeNxt as $tm) {
                            $print_str .= sql2Date($tomorrow).",";
                            $print_str .= $tm.",";
                            $print_str .= num(0).",";
                            $print_str .= "0,";
                            $print_str .= $mall->store_name.",";
                            $print_str .= TERMINAL_ID.",";
                            $print_str = substr($print_str,0,-1);
                            $print_str .= "\r\n";
                            $printH[] = array(date('Ymd',strtotime($tomorrow)),
                                                      $tm,
                                                      numInt(0),
                                                      0,
                                                      $mall->store_name,
                                                      TERMINAL_ID);
                        }    
                    }
                    $dbfheaders = array(array('TRANDATE','D',8),
                                        array('HOUR','C',5),
                                        array('SALES','N',11,2),
                                        array('TRANCNT','N',9,0),
                                        array('TENTNAME','C',15),
                                        array('TERMNUM','N',3,0));

                    $dbfh_file = $file_path.$mall->xxx_no.(date('md',strtotime($zread['read_date']) ) )."H.dbf";
                    if (dbase_create($dbfh_file, $dbfheaders)) {
                        $dbase = dbase_open($dbfh_file,2);
                        if($dbase){
                            foreach ($printH as $rows) {
                                $dadd = dbase_add_record($dbase,$rows);
                            }                            
                            $txth_file = $file_path.$mall->contract_no.(date('md',strtotime($zread['read_date']) ) )."H.txt";
                            $this->write_file(array($txth_file),$print_str);
                            dbase_close($dbase);
                        }
                    }    
                ###########################
                // $test = "\r\n\r\n\r\n\r\n";
                // foreach ($print as $name => $val) {
                //    $test .=  $name." - ".$val."\r\n";
                // }   
                // echo "<pre>".$test."</pre>"; 
                if($regen){
                    $error = 0;
                    $msg = "AYALA File successfully created.";
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert("AYALA File successfully created.","success");
                }    
            } 
            else{
                $error = 1;
                $msg = "No Zread Found.";
                if($regen){
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert($msg,"error");
                }
            }
        }
        public function stalucia_file($zread_id=null,$regen=false){
            $zread = $this->detail_zread($zread_id);
            $error = 0;
            $error_msg = null;
            if(isset($zread['read_date']) && $zread['read_date'] != null){
                $print_str = "";
                ###########################
                ### SET FILE NAMES
                    $mall_res = $this->site_model->get_tbl('stalucia');
                    $mall = array();
                    if(count($mall_res) > 0){
                        $mall = $mall_res[0];            
                    }
                    $file_path = "C:/STALUCIA/";
                    if (!file_exists($file_path)) {   
                        mkdir($file_path, 0777, true);
                    }
                    $sle_file = $file_path.(date('mdY',strtotime($zread['read_date']))).".sle";
                    $dat_file = $file_path.($mall->tenant_code.".dat");
                ###########################
                ###########################
                ### SET DETAILS
                    $args['trans_sales.datetime >= '] = $zread['from'];             
                    $args['trans_sales.datetime <= '] = $zread['to'];             
                    $trans = $this->trans_sales($args,true);
                    $sales = $trans['sales'];
                    $all_orders = $trans['all_orders'];
                    $guests = array();
                    $sn_guests = array();
                    foreach ($all_orders as $sales_id => $ord) {
                        if($ord->trans_ref != ""){
                            $ids[] = $sales_id;
                            $guests[$sales_id] = $ord->guest;
                        }
                    }
                    // return false;
                    // $ids = array_merge($sales['settled']['ids'],$sales['void']['ids']);
                    $tTAX = 0;
                    $tGROSS = 0;
                    $tNET = 0;
                    if(count($ids) > 0){
                        $this->cashier_model->db = $this->load->database('main', TRUE);
                        $this->site_model->db = $this->load->database('main', TRUE);
                        $select = 'trans_sales_menus.*,menus.menu_code,menus.menu_name,menus.cost as sell_price,menus.menu_cat_id as cat_id,menus.menu_sub_cat_id as sub_cat_id';
                        $join = null;
                        $join['menus'] = array('content'=>'trans_sales_menus.menu_id = menus.menu_id');
                        $menus = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$ids),array('sales_id'=>'asc'),$join,true,$select);
                        $mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));
                        $discounts = $this->site_model->get_tbl('trans_sales_discounts',array("sales_id"=>$ids),array('sales_id'=>'asc'));
                        $line_disc = array();
                        foreach ($ids as $sales_id) {
                            $disc_amt = 0;
                            foreach ($discounts as $disc) {
                                if($disc->sales_id == $sales_id){
                                    $disc_amt += $disc->amount;
                                }
                                
                            }
                            $senior = false;
                            foreach ($discounts as $disc) {
                                if($disc->sales_id == $sales_id){
                                    if($disc->disc_code == 'SNDISC'){
                                        $senior = true;
                                        break;
                                    }
                                }
                            }

                            foreach ($discounts as $disc) {
                                if($disc->sales_id == $sales_id){
                                    if($disc->disc_code == 'SNDISC'){
                                        if(!isset($sn_guests[$sales_id])){
                                            $sn_guests[$sales_id] = 1;
                                        }
                                        else{
                                            $sn = $sn_guests[$sales_id];
                                            $sn += 1;
                                            $sn_guests[$sales_id] = $sn;
                                        }
                                    }
                                }
                            }


                            $total_qty = 0;
                            foreach ($menus as $ms) {
                                if($ms->sales_id == $sales_id){
                                    $total_qty += 1;
                                }
                            }
                            $ld = 0;
                            if($disc_amt > 0){

                                $ld = $disc_amt / $total_qty;
                            }
                            $line_disc[$sales_id] = array("amt"=>$ld,"senior"=>$senior);
                        }

                        foreach ($menus as $ms) {
                            $print_str .= '"'.$mall->tenant_code.'",';
                            $sales_id = $ms->sales_id;
                            $order = array();
                            if(isset($all_orders[$sales_id])){
                                $order = $all_orders[$sales_id];
                                // $order = $sales['settled']['orders'][$sales_id];
                            }
                            if(isset($sales['void']['orders'][$sales_id])){
                                $void = $all_orders[$sales_id];
                                $order = $all_orders[$void->void_ref];
                            }
                            // if($void == true){
                            //    if(iSetObj($vo,'void_ref') != ""){
                                
                            //      if(isset($all_orders[$vo->void_ref])){
                            //         $order = $all_orders[$vo->void_ref];

                            //      }
                            //    }
                            // }

                            $print_str .= TERMINAL_NUMBER.$order->trans_ref.',';
                            $print_str .= '"'.( sql2Date($zread['read_date']) ).'",';
                            $print_str .= '"'.( date('H:i',strtotime($order->datetime)) ).'",';
                            $print_str .= $ms->qty.',';
                            $total_price = $ms->price;
                            foreach ($mods as $md) {
                                if($md->sales_id == $sales_id && $md->line_id == $ms->line_id){
                                    $total_price += $md->price;
                                }
                            }

                            $tax = (($total_price * $ms->qty) / 1.12 );
                            $tax = $tax * 0.12;
                            $used_dis = 0;
                            if(isset($line_disc[$sales_id])){
                                $ld = $line_disc[$sales_id];
                                if($ld['senior']){
                                    // $total_price = $total_price / 1.12;
                                    if(isset($sn_guests[$sales_id])){
                                        $gst = $guests[$sales_id];
                                        $sng = $sn_guests[$sales_id];
                                        $div_price = $total_price / $gst;
                                        $vat_p = abs($gst - $sng);
                                        $vat_price = $div_price * $vat_p;
                                        $non_vat_price = $div_price * $sng;
                                        $tax = ($vat_price * $ms->qty) / 1.12;
                                        $tax = $tax * 0.12;
                                        $non_tax = $non_vat_price / 1.12;                                        
                                        $total_price = $non_tax + $vat_price;
                                        $used_dis = (( (  ($total_price * $ms->qty) - $tax) /$gst) * 0.20) * $sng;


                                    }
                                }
                                else{
                                    $tpdisc = ($total_price * $ms->qty) - $ld['amt'];
                                    $tax = $tpdisc / 1.12;
                                    $tax = $tax * 0.12;
                                }
                            }

                            $print_str .= numInt($total_price).',';
                            $gross = $total_price * $ms->qty;
                            // if(isset($line_disc[$sales_id])){
                            //     $ld = $line_disc[$sales_id];
                            //     if($ld['senior']){
                            //         $gross = $gross / 1.12;
                            //     }
                            // }
                            $print_str .= numInt($gross).',';
                            $tGROSS += $gross;
                            $dis = 0;
                            if(isset($line_disc[$sales_id])){
                                if($ld['senior']){
                                    $dis = $used_dis;
                                }   
                                else 
                                    $dis = $line_disc[$sales_id]['amt'];
                            }
                            // $vat_ex = 0;
                            // if(isset($line_disc[$sales_id])){
                            //     $ld = $line_disc[$sales_id];
                            //     if($ld['senior']){
                            //         $added = ($gross / $order->guest);
                            //         $c = ($added / 1.12);
                            //         $vat_ex = $added - $c;
                            //         // echo $vat_ex."<br>";
                            //         $dis += $vat_ex;
                            //     }
                            // }
                            $print_str .= numInt($dis).',';
                            // $tax = ($gross - $dis) / 1.12 * 0.12;
                            // if(isset($line_disc[$sales_id])){
                            //     $ld = $line_disc[$sales_id];
                            //     if($ld['senior']){
                            //         $tax = 0;
                            //     }
                            // }
                            $print_str .= numInt($tax).',';
                            $tTAX += $tax;
                            $net = $gross - $dis - $tax;

                            $print_str .= numInt($net).',';
                            $tNET += $net;
                            // $type = "";
                            // if(in_array($sales_id, $sales['settled']['ids']))
                            $type = "S";
                            if(in_array($sales_id, $sales['void']['ids']))
                                $type = "V";
                            $print_str .= '"'.$type.'"';
                            $print_str .= "\r\n";
                        }
                    }
                    else{
                        $print_str .= '"'.$mall->tenant_code.'",';
                        $print_str .= '0,';
                        $print_str .= '"'.( sql2Date($zread['read_date']) ).'",';
                        $print_str .= '"'.( date('H:i',strtotime('07:00 AM')) ).'",';
                        $print_str .= '0,';
                        $print_str .= numInt(0).',';
                        $print_str .= numInt(0).',';
                        $print_str .= numInt(0).',';
                        $print_str .= numInt(0).',';
                        $print_str .= numInt(0).',';
                        $print_str .= '"S"';
                        $print_str .= "\r\n";
                    }
                ###########################
                ###########################
                ### WRITE FILE
                    // echo "TOTAL GROSS = ".$tGROSS."<br><br>";
                    // echo "TOTAL TAX = ".$tTAX."<br><br>";
                    // echo "TOTAL NET = ".$tNET."<br><br>";
                    // echo "<pre>".$print_str."</pre>";
                    $this->write_file(array($sle_file,$dat_file),$print_str);
                ###########################
                if($regen){
                    $error = 0;
                    $msg = "Sta. Lucia File successfully created.";
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert("Sta. Lucia File successfully created.","success");
                }
            }
            else{
                $error = 1;
                $msg = "No Zread Found.";
                if($regen){
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert($msg,"error");
                }
            }
        }
        public function send_to_rob($id=null,$increment=true){
            $print_str  = "";
            $lastRead = $this->cashier_model->get_z_read($id);
            $rlc = $this->cashier_model->get_rob_path();
            $tenantC = "00000000";
            if($rlc->rob_tenant_code != "")
                $tenantC = $rlc->rob_tenant_code;
            if(count($lastRead) > 0){
                foreach ($lastRead as $res) {
                    $date_from = $res->scope_from;
                    $date_to = $res->scope_to;
                    $old_gt_amnt = $res->old_total;
                    $grand_total = $res->grand_total;
                    $read_date = $res->read_date;
                }           
            }

            #CREATE FILE NAME
                // $file = substr(TENANT_CODE, -4).date('m',strtotime($read_date)).date('d',strtotime($read_date)).".".TERMINAL_NUMBER;
                $file = substr($tenantC, -4).date('m',strtotime($read_date)).date('d',strtotime($read_date)).".".TERMINAL_NUMBER;
                $check = $this->cashier_model->get_rob_files($file); 
                $ctr = 1;
                $new = true;
                if(count($check) > 0){
                    foreach ($check as $res) {
                        if($increment){
                            if($res->inactive == 0){
                                $ctr = $res->print+1;
                            }
                            else {
                                $ctr = $res->print;
                            }
                        }
                        else{
                            $ctr = $res->print;
                        }
                        $new = false;
                    }
                }
                
                // $filename =  substr(TENANT_CODE, -4).date('m',strtotime($read_date)).date('d',strtotime($read_date)).".".TERMINAL_NUMBER.$ctr;
                $filename =  substr($tenantC, -4).date('m',strtotime($read_date)).date('d',strtotime($read_date)).".".TERMINAL_NUMBER.$ctr;
                $check_file = false;
                $filenameCheck =  substr($tenantC, -4).date('m',strtotime($read_date)).date('d',strtotime($read_date)).".".TERMINAL_NUMBER."1";
                $c = 1;

                while ($check_file==false) {
                    $file_path = 'rob/';
                    if (!file_exists($file_path.$filenameCheck)) {   
                        $check_file = true;
                        $filename = $filenameCheck;
                    }
                    else{
                        $c++;
                        $filenameCheck =  substr($tenantC, -4).date('m',strtotime($read_date)).date('d',strtotime($read_date)).".".TERMINAL_NUMBER.$c;
                    }
                }
                // $filename = $this->check_if_rlc_txt_exists($filename,$ctr,$read_date);

                $title_name = $filename;
                $time = $this->site_model->get_db_now();

                $args = array();
                $terminal = TERMINAL_ID;
                
                if(!empty($terminal)){
                    $args['trans_sales.terminal_id'] = $terminal;
                }
                $args["trans_sales.datetime  BETWEEN '".$date_from."' AND '".$date_to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            //#########################################################################//
                $this->db = $this->load->database('main',true);
                $trans_sales = $this->cashier_model->get_trans_sales(null,$args);
                $orders = array();
                $orders['cancel'] = array(); 
                $orders['sale'] = array();
                $orders['void'] = array();
                $gross = 0;
                $gross_ids = array();
                $paid = 0;
                $paid_ctr = 0;
                $types = array();
                foreach ($trans_sales as $sale) {
                    if($sale->type_id == 10){
                        if($sale->trans_ref != "" && $sale->inactive == 0){
                            $orders['sale'][$sale->sales_id] = $sale;
                            $gross += $sale->total_amount;
                            $gross_ids[] = $sale->sales_id;
                            if($sale->total_paid > 0){
                                $paid += $sale->total_paid;
                                $paid_ctr ++;                   
                            }
                            $types[$sale->type][$sale->sales_id] = $sale;
                        }
                        else if($sale->trans_ref == "" && $sale->inactive == 1){
                            $orders['cancel'][$sale->sales_id] = $sale;
                        }
                    }
                    else{
                        $orders['void'][$sale->sales_id] = $sale;
                        // $gross += $sale->total_amount;
                        // $gross_ids[] = $sale->sales_id;
                    }
                }
            #DISCOUNTS 
                $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$gross_ids));
                // echo var_dump($gross_ids);
                $total_disc = 0;
                $disc_codes = array();
                foreach ($sales_discs as $discs) {
                    if(!isset($disc_codes[$discs->disc_code])){
                        $disc_codes[$discs->disc_code] = array('qty'=> 1,'amount'=>$discs->amount);
                    }
                    else{
                        $disc_codes[$discs->disc_code]['qty'] += 1;
                        $disc_codes[$discs->disc_code]['amount'] += $discs->amount;
                    }
                    $total_disc += $discs->amount;
                }
            #TENANT ID 
                // $print_str .= "01".str_pad(TENANT_CODE, 16, "0",STR_PAD_LEFT)."\r\n";
                $print_str .= "01".str_pad($tenantC, 16, "0",STR_PAD_LEFT)."\r\n";
            #POS TERMINAL NO. 
                $print_str .= "02".str_pad(TERMINAL_NUMBER, 16, "0",STR_PAD_LEFT)."\r\n";
            #GROSS SALES
                //REGULAR
                $total_regular_discs = 0;
                $reg_disc_ctr = 0;
                foreach ($disc_codes as $code => $dc) {
                    if($code != "SNDISC" && $code != "PWDISC"){
                        $total_regular_discs += $dc['amount'];
                        $reg_disc_ctr += $dc['qty'];                                    
                    }
                }
                $total_regular_discs = numInt($total_regular_discs);
                //SENIOR
                $total_senior_discs = 0;
                $seni_disc_ctr = 0;
                foreach ($disc_codes as $code => $dc) {
                    if($code == "SNDISC"){
                        $total_senior_discs += $dc['amount'];
                        $seni_disc_ctr += $dc['qty'];                                    
                    }
                }
                $total_senior_discs = numInt($total_senior_discs);
                //PWD
                $total_pwd_discs = 0;
                $pwd_disc_ctr = 0;
                foreach ($disc_codes as $code => $dc) {
                    if($code == "PWDISC"){
                        $total_pwd_discs += $dc['amount'];
                        $pwd_disc_ctr += $dc['qty'];                                    
                    }
                }
                $total_pwd_discs = numInt($total_pwd_discs);
                //LOCAL TAX
                $localTax=0;
                $gross += ($total_regular_discs+$total_senior_discs+$total_pwd_discs+$localTax);
                $gross = numInt($gross);
                $print_str .= "03".str_pad($gross, 16, "0",STR_PAD_LEFT)."\r\n";
                
                // $vat = ($gross/1.12) * .12;
                // $vat = numInt($vat);
                // $gross $total_senior_discs
                
                //NON VAT
                $no_tax = $this->cashier_model->get_trans_sales_no_tax(null,array("trans_sales_no_tax.sales_id"=>$gross_ids,"trans_sales_no_tax.amount >"=>0));
                $ntctr = 0;
                $nttotal = 0;
                foreach ($no_tax as $nt) {
                    $nttotal += $nt->amount;
                    $ntctr++;
                }
                // $vat_24 = $nttotal-$total_senior_discs;
                $vat_24 = $nttotal-$total_senior_discs-$total_pwd_discs;
                // echo $vat_24;
                // return false;
                $vat = ((($gross -$total_senior_discs-$total_pwd_discs-$vat_24)/1.12)*0.12);

                // $vat = $gross - $nttotal;
                // $vat = ($vat/1.12) * .12;
                $print_str .= "04".str_pad(numInt($vat), 16, "0",STR_PAD_LEFT)."\r\n";
            #VOID SALES
                // $total_cancel = 0;
                // $cn_ctr = 0;
                // foreach ($orders['cancel'] as $cn) {
                //     $total_cancel += $cn->total_amount;
                //     $cn_ctr++;
                // }
                // $total_cancel = numInt($total_cancel);
                // $print_str .= "05".str_pad($total_cancel, 16, "0",STR_PAD_LEFT)."\r\n";
                $total_void = 0;
                $vd_ctr = 0;
                $vids = array();
                foreach ($orders['void'] as $vd) {
                    $total_void += $vd->total_amount;
                    $vd_ctr++;
                    $vids[] = $vd->sales_id;
                }
                if(count($vids) > 0){
                    $vsales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$vids));
                    foreach ($vsales_discs as $ds) {
                            $total_void += $ds->amount;
                    }
                }
                $total_void = numInt($total_void);
                $print_str .= "05".str_pad($total_void, 16, "0",STR_PAD_LEFT)."\r\n";
                $print_str .= "06".str_pad($vd_ctr, 16, "0",STR_PAD_LEFT)."\r\n";
            #REGULAR DISCOUNTS
                $print_str .= "07".str_pad($total_regular_discs, 16, "0",STR_PAD_LEFT)."\r\n";
                $print_str .= "08".str_pad($reg_disc_ctr, 16, "0",STR_PAD_LEFT)."\r\n";
            #TOTAL VOIDED
                $print_str .= "09".str_pad(num(0), 16, "0",STR_PAD_LEFT)."\r\n";
                $print_str .= "10".str_pad(0, 16, "0",STR_PAD_LEFT)."\r\n";
            #SENIOR DISCOUNTS
                $print_str .= "11".str_pad($total_senior_discs, 16, "0",STR_PAD_LEFT)."\r\n";
                $print_str .= "12".str_pad($seni_disc_ctr, 16, "0",STR_PAD_LEFT)."\r\n";
            #SERVICE CHARGE
                $total_service_charge = 0;
                if(count($gross_ids) > 0){
                    $sales_charges = $this->cashier_model->get_trans_sales_charges(null,array("trans_sales_charges.sales_id"=>$gross_ids));
                    $total_charge = 0;
                    $charges_codes = array();
                    foreach ($sales_charges as $chg) {
                        if(!isset($charges_codes[$chg->charge_code])){
                            $charges_codes[$chg->charge_code] = array('qty'=> 1,'amount'=>$chg->amount);
                        }
                        else{
                            $charges_codes[$chg->charge_code]['qty'] += 1;
                            $charges_codes[$chg->charge_code]['amount'] += $chg->amount;
                        }
                        $total_charge += $chg->amount;
                    }
                    $serv_chg_ctr = 0;
                    foreach ($charges_codes as $code => $sv) {
                        if($code == "Service Charge"){
                            $total_service_charge += $sv['amount'];
                            $serv_chg_ctr += $sv['qty'];                                    
                        }
                    }
                    $total_service_charge = numInt($total_service_charge);
                }
                $print_str .= "13".str_pad($total_service_charge, 16, "0",STR_PAD_LEFT)."\r\n";
            #PREVIOUS EOD    
                $lastRead = $this->cashier_model->get_lastest_z_read(Z_READ,date2Sql($read_date));
                $lastOLDGT=0;
                $lastNEWGT=0;
                $lastGT_ctr=0;
                if(count($lastRead) > 0){
                    $new = "";
                    foreach ($lastRead as $res) {
                        $go = false;
                        if($new != $res->read_date){
                            $go=true;
                            $new = $res->read_date;
                        }
                        if($go){
                            $lastNEWGT = $res->grand_total;
                            $lastGT_ctr++;
                        }
                    }           
                }
                $print_str .= "14".str_pad($lastGT_ctr, 16, "0",STR_PAD_LEFT)."\r\n";
                $print_str .= "15".str_pad(numInt($lastNEWGT), 16, "0",STR_PAD_LEFT)."\r\n";
            #CURRENT EOD    
                $print_str .= "16".str_pad($lastGT_ctr+1, 16, "0",STR_PAD_LEFT)."\r\n";
                $newEOD = $gross-$total_regular_discs-$total_senior_discs-$total_pwd_discs+$lastNEWGT;
                $print_str .= "17".str_pad(numInt($newEOD), 16, "0",STR_PAD_LEFT)."\r\n";
                // $print_str .= "17".str_pad(numInt($grand_total), 16, "0",STR_PAD_LEFT)."\r\n";
            #DATE    
                $print_str .= "18".str_pad(sql2Date($read_date), 16, "0",STR_PAD_LEFT)."\r\n";
            #NOVELTY SALES
                $print_str .= "19".str_pad(numInt(0), 16, "0",STR_PAD_LEFT)."\r\n";
            #MISCELLANEOUS SALES
                $print_str .= "20".str_pad(numInt(0), 16, "0",STR_PAD_LEFT)."\r\n";
            #LOCAL TAX
                $print_str .= "21".str_pad(numInt($localTax), 16, "0",STR_PAD_LEFT)."\r\n";    
            #CREDIT SALES
                $credit_total = 0;
                $credit_qty = 0;
                if(count($gross_ids) > 0){
                    $payments = $this->cashier_model->get_trans_sales_payments_group(null,array("trans_sales_payments.sales_id"=>$gross_ids));
                    $pays = array();
                    foreach ($payments as $py) {
                        if(!isset($pays[$py->payment_type])){
                            $pays[$py->payment_type] = array('qty'=>$py->count,'amount'=>$py->total_paid);
                        }
                        else{
                            $pays[$py->payment_type]['qty'] += $py->count;
                            $pays[$py->payment_type]['amount'] += $py->total_paid;
                        }
                    }
                    foreach ($pays as $type => $pay) {
                        if($type == "credit"){
                            $credit_total += $pay['amount'];
                            $credit_qty += $pay['qty'];                
                        }
                    }
                }
                $print_str .= "22".str_pad(numInt($credit_total), 16, "0",STR_PAD_LEFT)."\r\n"; 

            #VAT ON CREDIT SALES
                $print_str .= "23".str_pad(numInt( ($credit_total/1.12) * 0.12), 16, "0",STR_PAD_LEFT)."\r\n";  
            #NON-VAT SALES
                ##############
                ### OLD 
                // $print_str .= "24".str_pad(numInt($nttotal-$total_senior_discs), 16, "0",STR_PAD_LEFT)."\r\n";
                ##############
                $print_str .= "24".str_pad(numInt($nttotal-$total_senior_discs-$total_pwd_discs), 16, "0",STR_PAD_LEFT)."\r\n";
            #PHARMA SALES
                $print_str .= "25".str_pad(numInt(0), 16, "0",STR_PAD_LEFT)."\r\n";
            #NON PHARMA TAX
                $print_str .= "26".str_pad(numInt(0), 16, "0",STR_PAD_LEFT)."\r\n"; 
            #PWD DISCOUNTS
                $print_str .= "27".str_pad($total_pwd_discs, 16, "0",STR_PAD_LEFT)."\r\n";
            #KIOSK SOMETHING
                // $menu_cat_sales = $this->cashier_model->get_trans_sales_categories(GC,array("trans_sales_menus.sales_id"=>$gross_ids));
                // $menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$gross_ids));
                // $cats = array();
                // foreach ($menu_cat_sales as $cat){
                //     $cost = $cat->price;
                //     foreach ($menu_cat_sale_mods as $cod) {
                //         if($cat->sales_id == $cod->sales_id && $cod->line_id == $cat->line_id){
                //             $cost += $cod->price;
                //         }
                //     }
                //     $cost = $cost * $cat->qty;
                //     foreach ($sales_discs as $cis){
                //         if($cis->sales_id == $cat->sales_id){
                //             $rate = $cis->disc_rate;
                //             switch ($cis->type) {
                //                 // case "item":
                //                 //             $items = explode(',',$cis->items);
                //                 //             foreach ($items as $lid) {
                //                 //                 if($cat->line_id == $lid){
                //                 //                     $discount = ($rate / 100) * $cost;
                //                 //                     $cost -= $discount;
                //                 //                 }
                //                 //             }
                //                 //             break;
                //                 // case "equal":
                //                 //             $divi = $cost/$cis->guest;
                //                 //             $discount = ($rate / 100) * $divi;
                //                 //             $cost -= $discount;
                //                 //             break;
                //                 // default:

                //                 //         $discount = ($rate / 100) * $cost;
                //                 //         $cost -= $discount;
                //                 //         break;

                //                 case "equal":
                //                          $divi = $cost/$cis->guest;
                //                          if($cis->no_tax == 1)
                //                              $divi = ($divi / 1.12);
                //                          $discount = ($rate / 100) * $divi;
                //                          $cost = ($divi * $cis->guest) - $discount;
                //                          break;
                //                 default:
                //                      if($cis->no_tax == 1)
                //                          $cost = ($cost / 1.12);                     
                //                      $discount = ($rate / 100) * $cost;
                //                      $cost -= $discount;       
                //             }                           
                //         }
                //     }
                //     if(!isset($cats[$cat->menu_cat_id])){
                //         $cats[$cat->menu_cat_id] = array(
                //             "cat_name"=>$cat->menu_cat_name,
                //             "amount"=>$cost,
                //             "qty"=>$cat->qty
                //         );                      
                //     }
                //     else{
                //         $cats[$cat->menu_cat_id]['amount'] += $cost;
                //         $cats[$cat->menu_cat_id]['qty'] += $cat->qty;
                //     }
                // }
                // $kiosk_total = 0;
                // foreach ($cats as $cat_id => $opt) {
                //     $kiosk_total += $opt['amount'];
                // }
                // $print_str .= "28".str_pad(numInt($kiosk_total), 16, "0",STR_PAD_LEFT)."\r\n";
                $gc_total = 0;
                // foreach ($pays as $type => $pay) {
                //     if($type == "gc"){
                //         $gc_total += $pay['amount'];
                //     }
                // }
                $print_str .= "28".str_pad(numInt($gc_total), 16, "0",STR_PAD_LEFT)."\r\n";
            #REPRINTED TOTAL
                $total_reprinted = 0;
                $re_ctr = 0;
                foreach ($orders['sale'] as $vd) {
                    if($vd->printed > 1){
                        $total_reprinted += $vd->total_amount;
                        $re_ctr++;                    
                    }
                }
                $total_reprinted = numInt($total_reprinted);
                $print_str .= "29".str_pad($total_reprinted, 16, "0",STR_PAD_LEFT)."\r\n";
                $print_str .= "30".str_pad($re_ctr, 16, "0",STR_PAD_LEFT)."\r\n";
            // echo "<pre>".$print_str."</pre>";
            // $path = $this->cashier_model->get_rob_path();
            // $filename = $path.$filename.".txt";
            // $fp = fopen($filename, "w+");
            // fwrite($fp,$print_str);
            // fclose($fp);
            // return false;
            $path =  $rlc->rob_path;
            $not_sent = 0;
            $error = "";
            if($path != ""){
                // $localFile = 'rob/'.$filename.".txt";
                // $ftpFile = $filename.".txt";
                // $filename = 'rob/'.$filename.".txt";
                $file_path = 'rob/';
                if (!file_exists($file_path)) {   
                    mkdir($file_path, 0777, true);
                }
                $localFile = $file_path.$filename;
                $ftpFile   = $filename;
                $filename  = $file_path.$filename;
            
                $fp = fopen($filename, "w+");
                fwrite($fp,$print_str);
                fclose($fp);       
                
                $ftp_server = $path;
                $can = (pingAddress($ftp_server));
                if($can){
                    $ftp_conn = ftp_connect($ftp_server) ;
                    if($ftp_conn){
                        $login = ftp_login($ftp_conn, $rlc->rob_username, $rlc->rob_password);
                            if (ftp_put($ftp_conn, $ftpFile, $localFile, FTP_ASCII)) {
                                $error = "";
                                $not_sent = 0;
                            } else {
                                $error = "Sales File is not Sent To RLC server. Please Contact your POS vendor";
                                $not_sent = 1;
                            }
                        ftp_close($ftp_conn); 
                    }
                    else{
                        $error = "No connection to RLC Server";
                        $not_sent = 1;
                    }
                }
                else{
                    $error = "No connection to RLC Server";
                    $not_sent = 1;
                }     
            }
            else{
                $error = "No connection to RLC Server";
                $not_sent = 1;
            }
            
            if($new){
               $item = array(
                "code"=>$file,
                "file"=>$filename,
                "print"=>1,
                "date_created"=>date2SqlDateTime($date_from),
                "inactive"=>(int)$not_sent
               );
               $this->db = $this->load->database('default',true);
               $id = $this->cashier_model->add_rob_files($item);
            }
            else{
               $this->db = $this->load->database('default',true);
               $id = $this->cashier_model->update_rob_files(array('print'=>$ctr,'file'=>$filename,"inactive"=>(int)$not_sent),$file);
            }

            return array('error'=>$error,'file'=>$filename);
        }
        public function sm_file($read_date=null,$zread_id=null){
            // $read_date = '2015-09-28';
            // $read_date = '2015-09-29 14:25:56';
            // $zread_id = 38;
            $mgms = $this->site_model->get_tbl('megamall');
            $mgm = array();
            if(count($mgms) > 0){
                $mgm = $mgms[0];            
            }
            $br_code = $mgm->br_code;
            $tenant_code = $mgm->tenant_no;
            $class_code = $mgm->class_code;
            $trade_code = $mgm->trade_code;
            $outlet_no = $mgm->outlet_no;

            $print_str = "";
            #### CREATE FILE 
                $args = array();
                $file_flg = null;
                if($zread_id != null){
                    $lastRead = $this->cashier_model->get_z_read($zread_id);
                    $zread = array();
                    if(count($lastRead) > 0){
                        foreach ($lastRead as $res) {
                            $zread = array(
                                'from' => $res->scope_from,
                                'to'   => $res->scope_to,
                                'old_gt_amnt' => $res->old_total,
                                'grand_total' => $res->grand_total,
                                'read_date' => $res->read_date,
                                'id' => $res->id,
                                'user_id'=>$res->user_id
                            );
                            $read_date = $res->read_date;
                        }           
                    }
                    $args['trans_sales.datetime >= '] = $zread['from'];             
                    $args['trans_sales.datetime <= '] = $zread['to'];
                    $file_flg = "Z".date('ymd',strtotime($read_date)).".flg";
                }
                $file_txt = date('mdY',strtotime($read_date)).".txt";
                $file_txt2 = date('ymd',strtotime($read_date)).".txt";
                $file_txt3 = date('mdy',strtotime($read_date)).".txt";

                $file_csv = date('mdY',strtotime($read_date)).".csv";
                $file_csv2 = date('ymd',strtotime($read_date)).".csv";
                $file_csv3 = date('mdy',strtotime($read_date)).".csv";

                $file_flg = "Z".date('ymd',strtotime($read_date)).".flg";
                
                $year = date('Y',strtotime($read_date));
                $month = date('M',strtotime($read_date));
                if (!file_exists("C:/SM/")) {   
                    mkdir("C:/SM/", 0777, true);
                }
                
                $text = "C:/SM/".$file_txt;
                $text2 = "C:/SM/".$file_txt2;
                $text3 = "C:/SM/".$file_txt3;

                $csv = "C:/SM/".$file_csv;
                $csv2 = "C:/SM/".$file_csv2;
                $csv3 = "C:/SM/".$file_csv3;

                $flg = null;
                if($file_flg != null)
                    $flg = "C:/SM/".$file_flg;
            #### GET POS MACHINE DETAILS
                $mesults = $this->site_model->get_tbl('branch_details');
                $mes = $mesults[0];
                $serial_no = $mes->serial;
                $machine_no = $mes->machine_no;
            #### GET TRANS
                if($zread_id == null){
                    $last_zread = $this->cashier_model->get_last_z_read(Z_READ,$read_date);
                    $date_from = null;
                    if(count($last_zread) > 0 ){
                        $args['trans_sales.datetime >= '] = $last_zread[0]->scope_to;             
                    }
                    $args['trans_sales.datetime <= '] = $read_date;
                }

                $curr = true; 
                $trans = $this->trans_sales($args,$curr);
                $sales = $trans['sales'];
                $net = $trans['net'];

                $gt = $this->old_grand_net_total($read_date);
                $old_gt = $gt['true_grand_total'];
                $new_gt = $gt['true_grand_total'] + $net;
            #### DISCOUNTS
                $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
                $discounts = $trans_discounts['total']; 
                $tax_disc = $trans_discounts['tax_disc_total']; 
                $no_tax_disc = $trans_discounts['no_tax_disc_total']; 
                $sc_disc = 0;
                $pwd_disc = 0;
                $other_disc = 0;
                $emp_disc = 0;
                $vip_disc = 0;
                foreach ($trans_discounts['types'] as $code => $disc) {
                    if($code == 'SNDISC'){
                        $sc_disc += $disc['amount'];
                    }
                    else if($code == 'PWDISC'){
                        $pwd_disc += $disc['amount'];
                    }
                    else if($code == 'EMPDISC'){
                        $emp_disc += $disc['amount'];
                    }
                    else if($code == 'VIPDISC'){
                        $vip_disc += $disc['amount'];
                    }
                    else{
                        $other_disc += $disc['amount'];
                    }
                }
            #### VOIDS
                $total_void = 0;
                if(isset($trans['sales']['void']['orders']) && count($trans['sales']['void']['orders']) > 0){
                    $void = $trans['sales']['void']['orders'];
                    if(count($void) > 0){
                        foreach ($void as $v) {
                            $total_void += $v->total_amount;
                        }                
                    } 
                }
            #### TAX
                $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
                $tax = $trans_tax['total'];
            #### LOCAL TAX
                $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
                $local_tax = $trans_local_tax['total']; 
            #### CHARGES
                $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
                $charges_types = $trans_charges['types'];
                $charges = $trans_charges['total']; 
                $sc = 0;
                $oc = $local_tax;
                foreach ($charges_types as $id => $row) {
                    if($id == SERVICE_CHARGE_ID){
                        $sc += $row['amount'];
                    }
                    else{
                        $oc += $row['amount'];
                    }
                }
            #### NO TAX
                $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
                $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
                $no_tax = $trans_no_tax['total'];
                $zero_rated = $trans_zero_rated['total'];
                $no_tax -= $zero_rated;
                $vat_exempt_sales = numInt($no_tax) - numInt($no_tax_disc);
                // echo numInt($no_tax)."-".numInt($no_tax_disc."---<br>";
            #### PAYMENTS
                $cash_pay_sales = 0;
                $cash_pay_ctr = 0;
                $gc_pay_sales = 0;
                $gc_pay_ctr = 0;
                $other_pay_sales = 0;
                $other_pay_ctr = 0;
                $smac_pay_sales = 0;
                $smac_pay_ctr = 0;
                $eplus_pay_sales = 0;
                $eplus_pay_ctr = 0;
                $cards = array(
                    'debit' => 0,
                    'Master Card' => 0,
                    'VISA' => 0,
                    'AmEx' => 0,
                    'jcb' => 0,
                    'diners' => 0,
                    'other' => 0
                );
                $card_ctr = array(
                    'debit' => 0,
                    'Master Card' => 0,
                    'VISA' => 0,
                    'AmEx' => 0,
                    'jcb' => 0,
                    'diners' => 0,
                    'other' => 0
                );
                  
                if(count($sales['settled']['ids']) > 0){
                    $pargs["trans_sales_payments.sales_id"] = $sales['settled']['ids'];
                    if($zread_id != null)
                        $this->site_model->db = $this->load->database('main', TRUE);
                    else
                        $this->site_model->db = $this->load->database('default', TRUE);
                    $pesults = $this->site_model->get_tbl('trans_sales_payments',$pargs); 
                    // echo $this->site_model->db->last_query();
                    foreach ($pesults as $pes) {
                        if($pes->amount > $pes->to_pay)
                            $amount = $pes->to_pay;
                        else
                            $amount = $pes->amount;

                        if($pes->payment_type == 'cash'){
                            $cash_pay_sales += $amount;
                            $cash_pay_ctr += 1;
                        }
                        elseif($pes->payment_type == 'credit'){
                            if(isset($cards[$pes->card_type])){
                                $cards[$pes->card_type] += $amount;
                                $card_ctr[$pes->card_type] += 1;
                            }
                            else{
                                $cards['other'] += $amount;
                                $card_ctr['other'] += 1;
                            }
                        }
                        elseif($pes->payment_type == 'debit'){
                            $cards['debit'] += $amount;
                            $card_ctr['debit'] += 1;
                        }
                        elseif($pes->payment_type == 'gc'){
                            $gc_pay_sales += $amount;
                            $gc_pay_ctr += 1;
                        }
                        else{
                            $other_pay_sales += $amount;
                            $other_pay_ctr += 1;
                        }

                        if($pes->payment_type == 'smac'){
                            $smac_pay_sales += $amount;
                            $smac_pay_ctr += 1;
                        }
                        if($pes->payment_type == 'eplus'){
                            $eplus_pay_sales += $amount;
                            $eplus_pay_ctr += 1;
                        }

                    }
                }
            #### PRINT
                $charges_sales = $gc_pay_sales + $cards['debit'] + $other_pay_sales + $cards['Master Card'] + $cards['VISA']
                                 + $cards['AmEx'] + $cards['diners'] + $cards['jcb'] + $cards['other'];
                $daily_sales = ($cash_pay_sales + $charges_sales );
                $credit_sales = $cards['Master Card'] + $cards['VISA'] + $cards['AmEx'] + $cards['diners'] + $cards['jcb'] + $cards['other'];

                $tax_disc -= $pwd_disc;
                $department_sum = (($daily_sales - $sc - $oc + $tax_disc - $vat_exempt_sales)/1.12) + $vat_exempt_sales + $sc + $oc;
                $vat_inclusive_sales = $daily_sales - $sc -$oc + $tax_disc - $vat_exempt_sales;
                $sales_with_vat = ($daily_sales - $sc - $oc + $tax_disc - $vat_exempt_sales);
                $vat = $sales_with_vat - ($sales_with_vat/1.12);

                // echo "DAILY SALES = ".$daily_sales."<br><br>";
                // echo "DEPARTMENT SUM = ".$department_sum."<br>";
                // echo "VAT INCLUSIVE SALES = ".$vat_inclusive_sales."<br>";
                // echo "CHARGES SALES = ".$charges_sales."<br>";
                // echo "VAT = ".$vat."<br>";

                // HEADER 1 - 5
                $print_str = commar($print_str,array($br_code,$tenant_code,$class_code,$trade_code,$outlet_no));
                // OLD GT AND NEW GT 6 - 7
                $print_str = commar($print_str,array(numInt($new_gt),numInt($old_gt) ));
                // SALES TYPE 8
                $print_str = commar($print_str,'SM01');
                // DEPARTMENT SUM 9
                $print_str = commar($print_str,numInt($department_sum));
                // REGULAR DISCOUNT 10
                $print_str = commar($print_str,numInt($other_disc));
                // EMPLOYEE DISCOUNT 11
                $print_str = commar($print_str,numInt($emp_disc));
                // SENIOR CITIZEN DISCOUNT 12
                $print_str = commar($print_str,numInt($sc_disc));
                // VIP DISCOUNT 13
                $print_str = commar($print_str,numInt($vip_disc));
                // PWD DISCOUNT 14
                // $pwd_disc = $pwd_disc / 1.12;
                $print_str = commar($print_str,numInt($pwd_disc));
                // GPC DISCOUNT 15
                $print_str = commar($print_str,numInt(0));
                // RESERVE DISCOUNT 16 - 21
                // for ($i=0; $i <= 6; $i++) { 
                    $print_str = commar($print_str,numInt(0));
                    $print_str = commar($print_str,numInt(0));
                    $print_str = commar($print_str,numInt(0));
                    $print_str = commar($print_str,numInt(0));
                    $print_str = commar($print_str,numInt(0));
                    $print_str = commar($print_str,numInt(0));
                // }
                // VAT 22
                $print_str = commar($print_str,numInt($vat));
                // echo $tax;
                // OTHER VAT 23
                $print_str = commar($print_str,numInt($local_tax));
                // ADJUSTMENTS 24 - 28
                $print_str = commar($print_str,array(numInt(0),numInt(0),numInt(0),numInt(0),numInt(0))); 
                // DAILY SALES 29


                $print_str = commar($print_str,numInt($daily_sales));
                // VOID 30
                $print_str = commar($print_str,numInt($total_void));
                // REFUND 31
                $print_str = commar($print_str,numInt(0));
                // SALES INCLUSIVE OF VAT 32
                $print_str = commar($print_str,numInt($vat_inclusive_sales));
                // NON VAT SALES 33
                $print_str = commar($print_str,numInt($vat_exempt_sales));
                // PAYMENTS 34 - 44 
                    ##CHARGE SALES
                     $print_str = commar($print_str,numInt($charges_sales));        
                    ##CASH SALES
                         $print_str = commar($print_str,numInt($cash_pay_sales));        
                    ##GC SALES
                         $print_str = commar($print_str,numInt($gc_pay_sales));        
                    ##DEBIT SALES
                         $print_str = commar($print_str,numInt($cards['debit']));        
                    ##OTHER SALES
                         $print_str = commar($print_str,numInt($other_pay_sales));     
                    ##MASTER CARD SALES
                         $print_str = commar($print_str,numInt($cards['Master Card']));        
                    ##VISA SALES
                         $print_str = commar($print_str,numInt($cards['VISA']));
                    ##AMERICAN EXPRESS SALES
                         $print_str = commar($print_str,numInt($cards['AmEx']));
                    ##DINERS SALES
                         $print_str = commar($print_str,numInt($cards['diners']));
                    ##JCB SALES
                         $print_str = commar($print_str,numInt($cards['jcb']));
                    ##OTHER CARD
                         $print_str = commar($print_str,numInt($cards['other']));                            
                // SERVICE CHARGE 45
                $print_str = commar($print_str,numInt($sc));
                // OTHER CHARGE 46
                $print_str = commar($print_str,numInt($local_tax));
                // OTHER CHARGE 47 - 49
                    $allIDS = $sales['settled']['ids'];
                    if(count($allIDS) == 0){
                        $print_str = commar($print_str,0);
                        $print_str = commar($print_str,0);
                        $print_str = commar($print_str,0);
                    }
                    else{
                        asort($allIDS);
                        foreach ($allIDS as $key) {
                            $print_str = commar($print_str,$key);
                            break;
                        }
                        $last_key = null;
                        foreach ($allIDS as $key) {
                           $last_key = $key;
                        }
                        $print_str = commar($print_str,$last_key);
                        $print_str = commar($print_str,count($allIDS));
                    }
                // BEGINNING INVOICE 50
                $print_str = commar($print_str,iSetObj($trans['first_ref'],'trans_ref',0));
                // ENDING INVOICE 51
                $print_str = commar($print_str,iSetObj($trans['last_ref'],'trans_ref',0));
                // PAYMENT COUNTER TRANS 52 - 61
                    ##CASH TRANSACTIONS
                         $print_str = commar($print_str,$cash_pay_ctr);
                    ##GC TRANSACTIONS
                         $print_str = commar($print_str,$gc_pay_ctr);
                    ##debit TRANSACTIONS
                         $print_str = commar($print_str,$card_ctr['debit']);
                    ##OTHER TENDER TRANSACTIONS
                         $print_str = commar($print_str,$other_pay_ctr);
                    ##MASTER CARD TRANSACTIONS
                         $print_str = commar($print_str,$card_ctr['Master Card']);
                    ##VISA TRANSACTIONS
                         $print_str = commar($print_str,$card_ctr['VISA']);
                    ##AMERICAN EXPRESS TRANSACTIONS
                         $print_str = commar($print_str,$card_ctr['AmEx']);
                    ##DINERS TRANSACTIONS
                         $print_str = commar($print_str,$card_ctr['diners']);
                    ##jcb TRANSACTIONS
                         $print_str = commar($print_str,$card_ctr['jcb']);
                    ##OTHER TRANSACTIONS
                         $print_str = commar($print_str,$card_ctr['other']);
                // MACHINE NO 62
                $print_str = commar($print_str,$machine_no);
                // SERIAL NO 63
                $print_str = commar($print_str,$serial_no);
                // SERIAL NO 64
                $zread_ctr = 0;
                if($zread_id != null)
                    $zread_ctr = $gt['ctr'];
                $print_str = commar($print_str,$zread_ctr);
                // SERIAL NO 65
                $dt = $read_date;
                if($zread_id != null){
                    $dt = $zread['to'];
                }
                $print_str = commar($print_str,date('His',strtotime($dt)) );
                // SERIAL NO 66
                $print_str = commar($print_str,date('mdY',strtotime($dt)) );

                $print_str = substr($print_str,0,-1);
                // $fp = fopen($text, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);
                $fp = fopen($text2, "w+");
                fwrite($fp,$print_str);
                fclose($fp);
                // $fp = fopen($text3, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);

                // $fp = fopen($csv, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);
                // $fp = fopen($csv2, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);
                // $fp = fopen($csv3, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);

                if($flg != null){
                    $fp = fopen($flg, "w+");
                    fwrite($fp,$print_str);
                    fclose($fp);
                }
            // echo "<pre>$print_str</pre>";
        }    
        public function ortigas_file($id=null,$save=1,$regen=0){
            $print_str  = "";
            $lastRead = $this->cashier_model->get_z_read($id);
            $zread = array();
            if(count($lastRead) > 0){
                foreach ($lastRead as $res) {
                    $zread = array(
                        // 'from' => $res->scope_from,
                        // 'to'   => $res->scope_to,
                        'old_gt_amnt' => $res->old_total,
                        'grand_total' => $res->grand_total,
                        'read_date' => $res->read_date,
                        'id' => $res->id,
                        'user_id'=>$res->user_id
                    );
                    $read_date = $res->read_date;
                }
                $rargs["DATE(read_details.read_date) = DATE('".date2Sql($read_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
                $select = "read_details.*";
                $results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
                // echo $this->site_model->db->last_query();
                $args = array();
                $from = "";
                $to = "";
                $datetimes = array();
                foreach ($results as $res) {
                    $datetimes[] = $res->scope_from;
                    $datetimes[] = $res->scope_to;
                    // break;
                }
                usort($datetimes, function($a, $b) {
                  $ad = new DateTime($a);
                  $bd = new DateTime($b);
                  if ($ad == $bd) {
                    return 0;
                  }
                  return $ad > $bd ? 1 : -1;
                });
                foreach ($datetimes as $dt) {
                    $from = $dt;
                    break;
                }    
                foreach ($datetimes as $dt) {
                    $to = $dt;
                }
                $zread['from'] = $from;
                $zread['to'] = $to;
            }
            ####################
            ### CREATE FILE NAME
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
                    foreach ($lastRead as $res) {
                        $lastGT_ctr++;
                    }           
                }
                $ext = str_pad(($lastGT_ctr+1), 3, "0",STR_PAD_LEFT);
                if($regen == 1){
                    $ext = str_pad((1), 3, "0",STR_PAD_LEFT);
                }
                $file = $tenant_code.TERMINAL_NUMBER.$fd.".".$ext;
            if($regen == 1){
                $this->cashier_model->db = $this->load->database('main', TRUE);
                $this->site_model->db = $this->load->database('main', TRUE);
            }    
            ####################
            ### HOURLY SALES 
                $this->ortigas_generate_hourly($file,$zread,$obj);
            ####################
            ### INVOICE SALES 
                $this->ortigas_generate_invoice($file,$zread,$obj);
            ####################
            ### DAILY SALES   
                $s = false;  
                if($save == 1)
                    $s = true;
                $this->ortigas_generate_daily($file,$zread,$obj,$lastGT_ctr+1,$s);
        }
        public function ortigas_generate_hourly($file,$zread=array(),$mall=array()){
            $year = date('Y',strtotime($zread['read_date']));
            $month = date('M',strtotime($zread['read_date']));
            if (!file_exists("ortigas_files/hourly/".$year."/".$month."/")) {   
                mkdir("ortigas_files/hourly/".$year."/".$month, 0777, true);
            }
            $filename = "ortigas_files/hourly/".$year."/".$month."/"."H".$file;
            #################
            ## GENERATE FILE
                $total_cover = $total_check = $total_sales = $total_count = 0;
                $counter = 1;
                $print_str = "";
                #TENANT CODE 
                    $print_str .= "01".iSetObj($mall,'tenant_code')."\r\n";
                # POS TERMINAL NUMBER
                    $print_str .= "02".TERMINAL_NUMBER."\r\n";
                # DATE
                    $date = date('mdY',strtotime($zread['read_date']));
                    $print_str .= "03".$date."\r\n";
                $time = unserialize(TIMERANGES);    
                $hour = array();
                foreach ($time as $tm) {
                   $h = date('G',strtotime($tm['FTIME']));
                   if($h == 0)
                      $h = 24;
                   $hour[$h] = array('from' => date('G',strtotime($tm['FTIME'])),
                                     'to'=>date('G',strtotime($tm['TTIME'])) ); 
                }

                $args["trans_sales.datetime  BETWEEN '".$zread['from']."' AND '".$zread['to']."'"] = array('use'=>'where','val'=>null,'third'=>false);
                $args['trans_sales.inactive'] = 0;
                $args['trans_sales.type_id'] = SALES_TRANS;
                $trans_sales = $this->cashier_model->get_trans_sales(null,$args);
                $sales = array();
                foreach ($hour as $code => $hr) {
                    $net = 0;
                    $count = 0;
                    $cover = 0;
                    foreach ($trans_sales as $res) {                           
                        $st = date('G',strtotime($res->datetime));
                        // echo $st."-------".$hr['from']."<br>";
                        if($st == $hr['from']){
                            // echo $code." ------ here<br>";
		                    if($res->type_id == 10){
		                        if($res->trans_ref != "" && $res->inactive == 0){
				                    $vargs["trans_sales_tax.sales_id"] = $res->sales_id;
				                    $vesults = $this->site_model->get_tbl('trans_sales_tax',$vargs);
				                    $total_vat = 0;
				                    foreach ($vesults as $ves) {
				                       if($ves->name == 'VAT'){
				                           $total_vat += $ves->amount;
				                       }
				                    }
				                    $cargs["trans_sales_charges.sales_id"] = $res->sales_id;
				                    $cesults = $this->site_model->get_tbl('trans_sales_charges',$cargs);   
				                    $total_service_charges = 0;
				                    $total_delivery_charges = 0;
				                    $other_charges = 0;
				                    $total_charges = 0;
				                    foreach ($cesults as $ces) {
				                        $total_charges += $ces->amount; 
				                    }
		                            $net += ($res->total_amount - $total_charges) - $total_vat;                            
		                            $count += 1;
		                            $c = $res->guest;
		                            if($res->guest == 0)                         
		                                $c = 1;   
		                            $cover += $c;                            
		                        }
		                    }    	
                        }#########
                    } 

                    $sales[$code] = array('net'=>$net,'count'=>$count,'cover'=>$cover);
                    // $gross_ids[] = $res->sales_id;
                    // echo "#########################<br>";
                }
                $total_net = 0;
                $total_count = 0;
                ## GET VAT

                ksort($sales);
                foreach ($sales as $sc => $val) {
                    # HOUR CODE 
                        $print_str .= "04".str_pad($sc,2,0,STR_PAD_LEFT)."\r\n";
                    # NET SALES 
                        $print_str .= "05".num($val['net'],2,'','')."\r\n";
                    # COUNT SALES 
                        $print_str .= "06".$val['count']."\r\n";
                    # COVER SALES 
                        $print_str .= "07".$val['cover']."\r\n";
                    $total_net += $val['net'];
                    $total_count += $val['count'];
                }
                $print_str .= "08".num($total_net,2,'','')."\r\n";
                $print_str .= "09".$total_count."\r\n";
                $fp = fopen($filename, "w+");
                fwrite($fp,$print_str);
                fclose($fp);
            // echo "<pre>".$print_str."</pre>";
        }
        public function ortigas_generate_invoice($file,$zread=array(),$mall=array()){
           $year = date('Y',strtotime($zread['read_date']));
           $month = date('M',strtotime($zread['read_date']));
           if (!file_exists("ortigas_files/invoice/".$year."/".$month."/")) {   
               mkdir("ortigas_files/invoice/".$year."/".$month, 0777, true);
           }
           $filename = "ortigas_files/invoice/".$year."/".$month."/"."I".$file; 
           #################
           ## GENERATE FILE
                $args["trans_sales.datetime  BETWEEN '".$zread['from']."' AND '".$zread['to']."'"] = array('use'=>'where','val'=>null,'third'=>false);
                $trans_sales = $this->cashier_model->get_trans_sales(null,$args,'asc');
                $print_str = "";
                #TENANT CODE 
                    $print_str .= "01".iSetObj($mall,'tenant_code')."\r\n";
                # POS TERMINAL NUMBER
                    $print_str .= "02".TERMINAL_NUMBER."\r\n";
                # DATE
                    $date = date('mdY',strtotime($zread['read_date']));
                    $print_str .= "03".$date."\r\n";
                # POS TERMINAL NUMBER
                    $print_str .= "04".TERMINAL_NUMBER."\r\n";
                    foreach ($trans_sales as $sale) {
                        if($sale->type_id == 10){
                            if($sale->trans_ref != "" && $sale->inactive == 0){

                            	$vargs["trans_sales_tax.sales_id"] = $sale->sales_id;
                            	$vesults = $this->site_model->get_tbl('trans_sales_tax',$vargs);
                            	$total_vat = 0;
                            	foreach ($vesults as $ves) {
                            	   if($ves->name == 'VAT'){
                            	       $total_vat += $ves->amount;
                            	   }
                            	}
                            	$cargs["trans_sales_charges.sales_id"] = $sale->sales_id;
                            	$cesults = $this->site_model->get_tbl('trans_sales_charges',$cargs);   
                            	$total_service_charges = 0;
                            	$total_delivery_charges = 0;
                            	$other_charges = 0;
                            	$total_charges = 0;
                            	foreach ($cesults as $ces) {
                            	    $total_charges += $ces->amount; 
                            	}
                                #invoice number 
                                    $print_str .= "05".$sale->trans_ref."\r\n";
                                #NET SALES
                                    $print_str .= "05".num(($sale->total_amount - $total_charges) - $total_vat,2,'','')."\r\n";
                                #STATUS
                                    $print_str .= "0701\r\n";
                            }
                        }
                        // else{
                        //     #invoice number 
                        //         $print_str .= "05".$sale->void_ref."\r\n";
                        //     #NET SALES
                        //         $print_str .= "05".$sale->total_amount."\r\n";
                        //     #STATUS
                        //         $print_str .= "0704\r\n";
                        // }
                    }
                    $fp = fopen($filename, "w+");
                    fwrite($fp,$print_str);
                    fclose($fp);
        }
        public function ortigas_generate_daily($file,$zread=array(),$mall=array(),$zread_ctr=0,$save=false){
            $year = date('Y',strtotime($zread['read_date']));
            $month = date('M',strtotime($zread['read_date']));
            if (!file_exists("ortigas_files/daily/".$year."/".$month."/")) {   
                mkdir("ortigas_files/daily/".$year."/".$month, 0777, true);
            }
            $filename = "ortigas_files/daily/".$year."/".$month."/"."D".$file; 
            #################
            ## GET SALES
                $print_str = "";
                $args["trans_sales.datetime  BETWEEN '".$zread['from']."' AND '".$zread['to']."'"] = array('use'=>'where','val'=>null,'third'=>false);
                $trans_sales = $this->cashier_model->get_trans_sales(null,$args,'asc');
                $orders = array();
                $orders['cancel'] = array(); 
                $orders['sale'] = array();
                $orders['void'] = array();
                $gross = 0;
                $gross_ids = array();
                $all_ids = array();
                $paid = 0;
                $paid_ctr = 0;
                $total_sales = 0;
                $total_void = 0;
                $types = array();
                $cover = 0;
                foreach ($trans_sales as $sale) {
                   if($sale->type_id == 10){
                       if($sale->trans_ref != "" && $sale->inactive == 0){
                           $orders['sale'][$sale->sales_id] = $sale;
                           $gross += $sale->total_amount;
                           $total_sales += $sale->total_amount;
                           $gross_ids[] = $sale->sales_id;
                           if($sale->total_paid > 0){
                               $paid += $sale->total_paid;
                               $paid_ctr ++;                   
                           }
                           if($sale->guest > 0){
                             $cover += $sale->guest;
                           }
                           else{
                             $cover += 1;
                           }

                           $types[$sale->type][$sale->sales_id] = $sale;
                           $all_ids[] = $sale->sales_id;
                       }
                       else if($sale->trans_ref == "" && $sale->inactive == 1){
                           $orders['cancel'][$sale->sales_id] = $sale;
                       }
                   }
                   else{
                       $all_ids[] = $sale->sales_id;
                       $orders['void'][$sale->sales_id] = $sale;
                       $total_void += $sale->total_amount;
                   }
                }
                ## GET DISCOUNTS
                    $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$gross_ids));
                    $total_disc = 0;
                    $disc_codes = array();
                    foreach ($sales_discs as $discs) {
                        if(!isset($disc_codes[$discs->disc_code])){
                            $disc_codes[$discs->disc_code] = array('qty'=> 1,'amount'=>$discs->amount);
                        }
                        else{
                            $disc_codes[$discs->disc_code]['qty'] += 1;
                            $disc_codes[$discs->disc_code]['amount'] += $discs->amount;
                        }
                        $total_disc += $discs->amount;
                    }
                    //REGULAR
                        $total_regular_discs = 0;
                        $reg_disc_ctr = 0;
                        foreach ($disc_codes as $code => $dc) {
                            if($code != "SNDISC" && $code != "PWDISC"){
                                $total_regular_discs += $dc['amount'];
                                $reg_disc_ctr += $dc['qty'];                                    
                            }
                        }
                    //SENIOR
                        $total_senior_discs = 0;
                        $seni_disc_ctr = 0;
                        $senior = 0;
                        foreach ($disc_codes as $code => $dc) {
                            if($code == "SNDISC"){
                                $total_senior_discs += $dc['amount'];
                                $seni_disc_ctr += $dc['qty'];       
                                $senior++;                             
                            }
                        }
                    //PWD
                        $total_pwd_discs = 0;
                        $pwd_disc_ctr = 0;
                        foreach ($disc_codes as $code => $dc) {
                            if($code == "PWDISC"){
                                $total_pwd_discs += $dc['amount'];
                                $pwd_disc_ctr += $dc['qty'];                                    
                            }
                        }
                ## GET VAT
                   $total_vat = 0;
                   if(count($gross_ids) > 0){
                    $vargs["trans_sales_tax.sales_id"] = $gross_ids;
                    $vesults = $this->site_model->get_tbl('trans_sales_tax',$vargs);
                    foreach ($vesults as $ves) {
                       if($ves->name == 'VAT'){
                           $total_vat += $ves->amount;
                       }
                    }
                   }
                ## GET NON VAT
                   $total_non_vat = 0;
                   if(count($gross_ids) > 0){
                    $nvargs["trans_sales_no_tax.sales_id"] = $gross_ids;
                    $nvesults = $this->site_model->get_tbl('trans_sales_no_tax',$nvargs);
                    foreach ($nvesults as $nves) {
                       $total_non_vat += $nves->amount;
                    }    
                   } 
                ## GET ZERO RATED
                    $zrctr = 0;
                    $zrtotal = 0;
                    if(count($gross_ids) > 0){
                        $zero_rated = $this->cashier_model->get_trans_sales_zero_rated(null,array("trans_sales_zero_rated.sales_id"=>$gross_ids,"trans_sales_zero_rated.amount >"=>0));
                        foreach ($zero_rated as $zt) {
                            $zrtotal += $zt->amount;
                            $zrctr++;
                        }
                    }
                ## GET SERVICE CHARGES
                    $total_service_charges = 0;
                    $total_delivery_charges = 0;
                    $other_charges = 0;
                    if(count($gross_ids) > 0){
                        $cargs["trans_sales_charges.sales_id"] = $gross_ids;
                        $cesults = $this->site_model->get_tbl('trans_sales_charges',$cargs);   
                        foreach ($cesults as $ces) {
                            if($ces->charge_id == 1){
                                $total_service_charges += $ces->amount;
                            }
                            else if($ces->charge_id == 2){
                                $total_delivery_charges += $ces->amount;
                            }
                            else{
                                $other_charges += $ces->amount;
                            }
                        }     
                    }
                ## GET LOCAL TAX
                    $total_local_tax = 0;
                    if(count($gross_ids) > 0){
                        $targs["trans_sales_local_tax.sales_id"] = $gross_ids;
                        $tesults = $this->site_model->get_tbl('trans_sales_local_tax',$targs);   
                        foreach ($tesults as $tes) {
                            $total_local_tax += $tes->amount;
                        }
                    }                
                    $this->site_model->db = $this->load->database('default', TRUE);
                ## GET ORTIGAS TAX READS
                    $orgs['read_date < '] = date2Sql($zread['read_date']);
                    $orgs['no_tax = '] = 0;
                    $tax_read = $this->site_model->get_tbl('ortigas_read_details',$orgs,array('read_date'=>'desc','id'=>'desc'),null,true,'*',null,1);
                    $tax_old_gt = 0;
                    if(count($tax_read) > 0){
                        $tax_old_gt = $tax_read[0]->grand_total;
                    }
                ## GET ORTIGAS NON TAX READS
                    $orgs['read_date <= '] = date2Sql($zread['read_date']);
                    $orgs['no_tax = '] = 1;
                    $tax_read = $this->site_model->get_tbl('ortigas_read_details',$orgs,array('read_date'=>'desc','id'=>'desc'),null,true,'*',null,1);
                    $tax_non_old_gt = 0;
                    if(count($tax_read) > 0){
                        $tax_non_old_gt = $tax_read[0]->grand_total;
                    }        
                $this->site_model->db = $this->load->database('main', TRUE);
            #################
            ## GENERATE FILE
                // $total_sales += $total_senior_discs;
                $total_non_disc = $total_senior_discs;
                $total_non_vat -= $total_non_disc;
                $total_add_on_charges = $total_service_charges + $total_delivery_charges + $total_local_tax + $other_charges;
                $overall_net = $total_sales - $total_add_on_charges;
                $total_deduc = $total_pwd_discs + $total_regular_discs + $total_add_on_charges;
                
                $net = $overall_net - $total_non_vat;
                $gross = $net + $total_deduc;
                $vat = (($gross - $total_deduc) * 0.12) / 1.12;
                $net = $gross - $total_deduc - $vat;
                # TENANT CODE
                    $print_str .= "01".iSetObj($mall,'tenant_code')."\r\n";
                # POS TERMINAL NUMBER
                    $print_str .= "02".TERMINAL_NUMBER."\r\n";
                # DATE
                    $date = date('mdY',strtotime($zread['read_date']));
                    $print_str .= "03".$date."\r\n";    
                # OLD TAX ACCUMULATED SALES 
                    $print_str .= "04".num($tax_old_gt,2,'','')."\r\n";
                # NEW TAX ACCUMULATED SALES 
                    $print_str .= "05".num($tax_old_gt + $net,2,'','')."\r\n";
                    // $print_str .= "05".num($tax_old_gt + ($net+$tv),2,'','')."\r\n";
                    $tax_new_gt = $tax_old_gt + $net;
                # TOTAL GROSS AMOUNT
                    $print_str .= "06".num($gross,2,'','')."\r\n";
                # TOTAL DEDUCTIONS AMOUNT
                    $print_str .= "07".num($total_deduc,2,'','')."\r\n";
                # TOTAL PROMO SALES
                    $print_str .= "08".num(0,2,'','')."\r\n";
                # TOTAL PWD DISCOUNT
                    $print_str .= "09".num($total_pwd_discs,2,'','')."\r\n";
                # TOTAL REFUND AMOUNT
                    $print_str .= "10".num(0,2,'','')."\r\n";
                # TOTAL RETURND ITEMS AMOUNT
                    $print_str .= "11".num(0,2,'','')."\r\n";
                # TOTAL OTHER TAXES
                    $print_str .= "12".num($total_local_tax,2,'','')."\r\n";
                # TOTAL SERVICE CHARGE AMOUNT
                    $print_str .= "13".num($total_service_charges,2,'','')."\r\n";
                # TOTAL ADJUSTMENT DISCOUNT
                    $print_str .= "14".num(0,2,'','')."\r\n";
                # TOTAL VOID AMOUNT
                    $print_str .= "15".num(0,2,'','')."\r\n";
                # TOTAL DISCOUNT CARDS
                    $print_str .= "16".num(0,2,'','')."\r\n";
                # TOTAL DELIVERY CHARGES
                    $print_str .= "17".num($total_delivery_charges,2,'','')."\r\n";
                # TOTAL GIFT CERTIFICATES
                    $print_str .= "18".num(0,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC DISCOUNT 1
                    $print_str .= "19".num($total_regular_discs,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC DISCOUNT 2
                    $print_str .= "20".num(0,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC DISCOUNT 3
                    $print_str .= "21".num(0,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC DISCOUNT 4
                    $print_str .= "22".num(0,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC DISCOUNT 5
                    $print_str .= "23".num(0,2,'','')."\r\n";
                # TOTAL OF ALL NON APPROVED STORE DISCOUNTS
                    $print_str .= "24".num(0,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 1
                    $print_str .= "25".num(0,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 2
                    $print_str .= "26".num(0,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 3
                    $print_str .= "27".num(0,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 4
                    $print_str .= "28".num(0,2,'','')."\r\n";
                # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 5
                    $print_str .= "29".num(0,2,'','')."\r\n";
                # TOTAL VAT/TAX AMOUNT
                    // $vat = $net-$total_non_vat-$total_vat;
                    // $print_str .= "30".num($vat,2,'',''). "-".num($vat) ."\r\n";
                    $print_str .= "30".num($vat, 2,'','')."\r\n";
                # TOTAL NET SALES AMOUNT
                    $print_str .= "31".num($net, 2,'','')."\r\n";
                # TOTAL COVER COUNT
                    $total_tax_cover = $cover;
                    if($total_tax_cover < 0)
                        $total_tax_cover *= -1;
                    $print_str .= "32".num($total_tax_cover, 0,'','')."\r\n";
                # TOTAL COVER COUNT
                    $print_str .= "33".num($zread_ctr, 0,'','')."\r\n";
                # TOTAL TRANS COUNT
                    $no_trans = count($gross_ids);
                    $print_str .= "34".num($no_trans, 0,'','')."\r\n";
                # SALES TYPE
                    $print_str .= "35".iSetObj($mall,'sales_type')."\r\n";
                # AMOUNT
                    // $print_str .= "36".num($vatable, 0,'','')."\r\n";
                    $print_str .= "36".num($net, 2,'','')."\r\n";
                # OLD NON TAX ACCUMULATED SALES 
                    $print_str .= "37".num($tax_non_old_gt,2,'','')."\r\n";
                # NEW NON TAX ACCUMULATED SALES 
                    $total_non_deduc = $total_senior_discs;
                    $no_net_total = $total_non_vat;
                    $non_gross = $no_net_total +  $total_non_deduc;
                    $print_str .= "38".num($tax_non_old_gt + ($no_net_total),2,'','')."\r\n";
                    $tax_non_new_gt = $tax_non_old_gt + $no_net_total;
                    // echo $tax_non_old_gt."<br><br>";
                # TOTAL GROSS NON TAX 
                    $print_str .= "39".num($non_gross,2,'','')."\r\n";
                # TOTAL DEDUCTIONS
                    $print_str .= "40".num($total_non_deduc,2,'','')."\r\n";
                # TOTAL PROMO SALES AMOUNT
                    $print_str .= "41".num(0,2,'','')."\r\n";
                # TOTAL SENIOR CITIZEN DISCOUNT
                    $print_str .= "42".num($total_senior_discs,2,'','')."\r\n";
                ################### NON 
                    # TOTAL REFUND AMOUNT
                        $print_str .= "43".num(0,2,'','')."\r\n";
                    # TOTAL RETURND ITEMS AMOUNT
                        $print_str .= "44".num(0,2,'','')."\r\n";
                    # TOTAL OTHER TAXES
                        $print_str .= "45".num(0,2,'','')."\r\n";
                    # TOTAL SERVICE CHARGE AMOUNT
                        $print_str .= "46".num(0,2,'','')."\r\n";
                    # TOTAL ADJUSTMENT DISCOUNT
                        $print_str .= "47".num(0,2,'','')."\r\n";
                    # TOTAL VOID AMOUNT
                        $print_str .= "48".num(0,2,'','')."\r\n";
                    # TOTAL DISCOUNT CARDS
                        $print_str .= "49".num(0,2,'','')."\r\n";
                    # TOTAL DELIVERY CHARGES
                        $print_str .= "50".num(0,2,'','')."\r\n";
                    # TOTAL GIFT CERTIFICATES
                        $print_str .= "51".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC DISCOUNT 1
                        $print_str .= "52".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC DISCOUNT 2
                        $print_str .= "53".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC DISCOUNT 3
                        $print_str .= "54".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC DISCOUNT 4
                        $print_str .= "55".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC DISCOUNT 5
                        $print_str .= "56".num(0,2,'','')."\r\n";
                    # TOTAL OF ALL NON APPROVED STORE DISCOUNTS
                        $print_str .= "57".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 1
                        $print_str .= "58".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 2
                        $print_str .= "59".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 3
                        $print_str .= "60".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 4
                        $print_str .= "61".num(0,2,'','')."\r\n";
                    # TOTAL STORE SPECIFIC NON APPROVED DISCOUNT 5
                        $print_str .= "62".num(0,2,'','')."\r\n";
                # VAT
                    $print_str .= "63".num(0,2,'','')."\r\n";
                # TOTAL NET SALES
                    $print_str .= "64".num(($total_non_vat),2,'','')."\r\n";
                # GRAND TOTAL NET SALES
                    $net_sales = $net + ($total_non_vat + $vat);
                    $print_str .= "65".num($net + $no_net_total,2,'','')."\r\n";
            if($save){
                if($tax_old_gt == ""){
                    $tax_old_gt = 0;
                }
                if($tax_new_gt == ""){
                    $tax_new_gt = 0;
                }
                $this->site_model->db = $this->load->database('default', TRUE);
                $this->site_model->delete_tbl('ortigas_read_details',array('zread_id'=>$zread['id']));
                $item_tax = array(
                    'zread_id' => $zread['id'],
                    'read_date'=> $zread['read_date'],
                    'user_id'=> $zread['user_id'],
                    'old_total'=> $tax_old_gt,
                    'grand_total'=> $tax_new_gt,
                    'scope_from'=> $zread['from'],
                    'scope_to'=> $zread['to'],
                    'no_tax'=> 0,
                );
                $this->site_model->add_tbl('ortigas_read_details',$item_tax,array('reg_date'=>'NOW()'));
                if($tax_non_old_gt == ""){
                    $tax_non_old_gt = 0;
                }
                if($tax_non_new_gt == ""){
                    $tax_non_new_gt = 0;
                }

                $item_no_tax = array(
                    'zread_id' => $zread['id'],
                    'read_date'=> $zread['read_date'],
                    'user_id'=> $zread['user_id'],
                    'old_total'=> $tax_non_old_gt,
                    'grand_total'=> $tax_non_new_gt,
                    'scope_from'=> $zread['from'],
                    'scope_to'=> $zread['to'],
                    'no_tax'=> 1,
                );
                $this->site_model->add_tbl('ortigas_read_details',$item_no_tax,array('reg_date'=>'NOW()'));
                $this->site_model->db = $this->load->database('main', TRUE);
            }
            // echo "<pre>".$print_str."</pre>";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);                    
        }
        public function araneta_file_old($zread_id=null){
            $araneta_db = $this->site_model->get_tbl('araneta');
            $araneta = $araneta_db[0];
            $months = array('01'=>'1','02'=>'2','03'=>'3','04'=>'4','05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'A','11'=>'B','12'=>'C');
            // S = summary file , L = for transaaction list , C for monthly file
            $print_str  = "";
            $lastRead = $this->cashier_model->get_z_read($zread_id);
            $zread = array();
            if(count($lastRead) > 0){
                foreach ($lastRead as $res) {
                    $zread = array(
                        'from' => $res->scope_from,
                        'to'   => $res->scope_to,
                        'old_gt_amnt' => $res->old_total,
                        'grand_total' => $res->grand_total,
                        'read_date' => $res->read_date,
                        'id' => $res->id,
                        'user_id'=>$res->user_id
                    );
                    $read_date = $res->read_date;
                }           
            }
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            
            $args['trans_sales.datetime >= '] = $zread['from'];             
            $args['trans_sales.datetime <= '] = $zread['to'];             
            $trans = $this->trans_sales($args);
            #######################
            ## DAILY 
                $file = $araneta->lessee_name;
                $this->araneta_generate_daily($file,$zread,$trans,$araneta);
            #######################
            ## TRANS LIST 
                $file = $araneta->lessee_name;
                $this->araneta_generate_list($file,$zread,$trans,$araneta);
        }
        public function araneta_generate_list($file,$zread,$trans,$araneta){
            $print_str = "";
            $year = date('Y',strtotime($zread['read_date']));
            $month = date('M',strtotime($zread['read_date']));
            $m = date('m',strtotime($zread['read_date']));
            $d = date('d',strtotime($zread['read_date']));
            $mo = array('01'=>'1','02'=>'2','03'=>'3','04'=>'4','05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'A','11'=>'B','12'=>'C');
            if (!file_exists("C:/Araneta/".$year."/")) {   
                mkdir("C:/Araneta/".$year, 0777, true);
            }

            $filename = "C:/Araneta/".$year."/".$file."L.".$mo[$m].$d; 
            $curr = false;
            $sales = $trans['sales'];
            $settled = $trans['sales']['settled']['orders'];
            foreach ($settled as $sales_id => $set){
                $print_str .= $araneta->space_code." ";
                $print_str .= $araneta->lessee_no." ";
                $print_str .= date('m/d/y',strtotime($set->datetime))." ";
                $print_str .= date('H:i:s',strtotime($set->datetime))." ";
                $print_str .= $sales_id." ";
                $print_str .= $set->trans_ref." ";
                $print_str .= TERMINAL_ID." ";
                $print_str .= numInt(0)." ";#GROSS
                $trans_discounts = $this->discounts_sales($sales_id,$curr);
                $discounts = $trans_discounts['total'];
                $tax_disc = $trans_discounts['tax_disc_total']; 
                $no_tax_disc = $trans_discounts['no_tax_disc_total'];  
                $print_str .= numInt($discounts)." ";
                $print_str .= numInt(0)." ";#VOID
                $print_str .= numInt(0)." ";#REFUND
                $print_str .= numInt(0)." ";#ADJ
                $trans_tax = $this->tax_sales($sales_id,$curr);
                $tax = $trans_tax['total'];
                $print_str .= numInt($tax)." ";
                
                $trans_local_tax = $this->local_tax_sales($sales_id,$curr);
                $local_tax = $trans_local_tax['total']; 
                $trans_charges = $this->charges_sales($sales_id,$curr);
                $charges_types = $trans_charges['types'];
                $charges = $trans_charges['total']; 
                $sc = 0;
                $oc = $local_tax;
                foreach ($charges_types as $id => $row) {
                    if($id == SERVICE_CHARGE_ID){
                        $sc += $row['amount'];
                    }
                    else{
                        $oc += $row['amount'];
                    }
                }
                $print_str .= numInt($oc)." ";
                $print_str .= numInt($sc)." ";
                $trans_no_tax = $this->no_tax_sales($sales_id,$curr);
                $trans_zero_rated = $this->zero_rated_sales($sales_id,$curr);
                $no_tax = $trans_no_tax['total'];
                $zero_rated = $trans_zero_rated['total'];
                $no_tax -= $zero_rated;
                $no_tax = $no_tax - $no_tax_disc;
                $net_no_adds = $set->total_amount-($charges+$local_tax);
                // $taxable = ($net_no_adds - ($tax + $no_tax)); 
                $taxable =   ($net_no_adds - ($tax + ($no_tax+$zero_rated))  );
                $print_str .= numInt($taxable)." ";
                $print_str .= numInt($no_tax)." ";
                // $print_str .= numInt($set->total_amount          )." ";
                $print_str .= numInt($net_no_adds)." ";

                $payments = $this->payment_sales($sales_id,$curr);
                $payments_types = $payments['types'];
                $cash = 0;
                $opay = 0;
                foreach ($payments_types as $type => $val) {
                    if($type == 'cash' ){
                        $cash += $val['amount'];
                    }
                    else
                        $opay += $val['amount'];
                }
                $print_str .= numInt($opay)." ";
                $print_str .= numInt($cash)." ";
                $print_str .= "\r\n";
            }
            // echo "<pre>".$print_str."</pre>";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);
        }    
        public function araneta_generate_daily($file,$zread,$trans,$araneta,$monthtype=false){
            $print_str = "";
            $mo = array('01'=>'1','02'=>'2','03'=>'3','04'=>'4','05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'A','11'=>'B','12'=>'C');
            $year = date('Y',strtotime($zread['read_date']));
            $month = date('M',strtotime($zread['read_date']));
            $m = date('m',strtotime($zread['read_date']));
            $d = date('d',strtotime($zread['read_date']));
            if(!$monthtype){
                if (!file_exists("C:/Araneta/".$year."/")) {   
                    mkdir("C:/Araneta/".$year."/", 0777, true);
                }
                $filename = "C:/Araneta/".$year."/".$file."S.".$mo[$m].$d; 
            }
            else{
                if (!file_exists("C:/Araneta/".$year."/")) {   
                    mkdir("C:/Araneta/".$year."/", 0777, true);
                }
                $filename = "C:/Araneta/".$year."/".$file."C.".$mo[$m]."00";    
            }
            

            $curr = false;
            $old_gt = $this->old_grand_net_total($zread['from']);
            $new_gt = $old_gt['old_grand_total'] + $trans['net'];
            $sales = $trans['sales'];
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
            $discounts = $trans_discounts['total']; 
            $tax_disc = $trans_discounts['tax_disc_total']; 
            $no_tax_disc = $trans_discounts['no_tax_disc_total']; 
            $total_void = 0;
            if(isset($trans['sales']['void']['orders']) && count($trans['sales']['void']['orders']) > 0){
                $void = $trans['sales']['void']['orders'];
                if(count($void) > 0){
                    foreach ($void as $v) {
                        $total_void += $v->total_amount;
                    }                
                } 
            }
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
            $tax = $trans_tax['total'];

            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
            $local_tax = $trans_local_tax['total']; 
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
            $charges_types = $trans_charges['types'];
            $charges = $trans_charges['total']; 
            $sc = 0;
            $oc = $local_tax;
            foreach ($charges_types as $id => $row) {
                if($id == SERVICE_CHARGE_ID){
                    $sc += $row['amount'];
                }
                else{
                    $oc += $row['amount'];
                }
            }
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);


            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;
            $net = $trans['net'];
            // $net_no_adds = $trans['net']-$charges-$local_tax;
            // $taxable = ($net_no_adds - ($tax + $no_tax)); 

            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            $nontaxable = $no_tax - $no_tax_disc;
            $taxable =   ($net_no_adds - ($tax + ($nontaxable+$zero_rated))  );
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;

            $payments = $this->payment_sales($sales['settled']['ids'],$curr);
            $payments_types = $payments['types'];
            $cash = 0;
            $opay = 0;
            foreach ($payments_types as $type => $val) {
                if($type == 'cash' ){
                    $cash += $val['amount'];
                }
                else
                    $opay += $val['amount'];
            }
            #########################
            #### GENERATE TEXT FILE
                $print_str .= $araneta->space_code." ";
                $print_str .= $araneta->lessee_no." ";
                $print_str .= numInt($new_gt)." ";
                $print_str .= numInt($old_gt['old_grand_total'])." ";
                $print_str .= numInt($trans['net'])." ";
                $print_str .= numInt($discounts)." ";
                $print_str .= numInt(0)." ";
                $print_str .= numInt($total_void)." ";
                $print_str .= numInt(0)." ";
                $print_str .= numInt($trans['net'] + $total_void)." ";#GROSS
                $print_str .= numInt($tax)." ";
                $print_str .= numInt($oc)." ";
                $print_str .= numInt($sc)." ";
                $print_str .= numInt($taxable)." ";
                $print_str .= numInt($nontaxable)." ";
                $print_str .= numInt($opay)." ";
                $print_str .= numInt($cash)." ";
                
                $first_ref = iSetObj($trans['first_ref'],'trans_ref',$trans['first_ref']);
                $last_ref = iSetObj($trans['last_ref'],'trans_ref',$trans['last_ref']);

                $print_str .= $first_ref." ";
                $print_str .= $last_ref." ";
                $print_str .= $trans['ref_count']." ";
                $print_str .= "0 ";
                $print_str .= $old_gt['ctr']." ";
                $print_str .= date('m/d/y',strtotime($zread['read_date']))." ";
                $print_str .= date('H:i:s',strtotime($zread['from']))." ";
                $print_str .= date('H:i:s',strtotime($zread['to']))." ";
                for ($i=0; $i <= 10; $i++) { 
                    $print_str .= numInt(0)." ";
                }
            
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp); 
        }
        public function araneta_month_file($date=null){
            $araneta_db = $this->site_model->get_tbl('araneta');
            $araneta = $araneta_db[0];
            $months = array('01'=>'1','02'=>'2','03'=>'3','04'=>'4','05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'A','11'=>'B','12'=>'C');
            // S = summary file , L = for transaaction list , C for monthly file
            // $time = $this->site_model->get_db_now();
            $rargs["MONTH(read_details.read_date) = MONTH('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            $select = "read_details.*";
            $results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
            foreach ($results as $res) {
                $from = $res->scope_from;
                break;
            }
            foreach ($results as $res) {
                $to = $res->scope_to;
            }
            $read_date = date("Y-m-t", strtotime($from) );
            $zread = array(
                'from' => $from,
                'to'   => $to,
                'read_date' => $read_date,
            );

            $user = $this->session->userdata('user');
            $args['trans_sales.datetime >= '] = $zread['from'];             
            $args['trans_sales.datetime <= '] = $zread['to'];             
            $trans = $this->trans_sales($args);
            #######################
            ## DAILY 
                $file = $araneta->lessee_name;
                $this->araneta_generate_daily($file,$zread,$trans,$araneta,true);
        }
        public function araneta_file($zread_id=null,$regen=false){
            $error = 0;
            $error_msg = null;
            $mall_db = $this->site_model->get_tbl('araneta');
            $mall = array('lessee_name'=>$mall_db[0]->lessee_name,'lessee_no'=>$mall_db[0]->lessee_no,'space_code'=>$mall_db[0]->space_code);
            $file_path = filepathisize($mall_db[0]->file_path);
            $zread = $this->detail_zread($zread_id);    
            if(isset($zread['read_date']) && $zread['read_date'] != null){
                $year = date('Y',strtotime($zread['read_date']));
                $file_path .= $year."/";
                if (!file_exists($file_path)) {   
                    mkdir($file_path, 0777, true);
                }
                $mon = array('01'=>'1','02'=>'2','03'=>'3','04'=>'4','05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'A','11'=>'B','12'=>'C');
                ##############################################################################
                ### GET DATA
                    $curr = false;
                    $args['trans_sales.datetime >= '] = $zread['from'];             
                    $args['trans_sales.datetime <= '] = $zread['to'];  
                    $trans = $this->trans_sales($args,$curr);
                    $sales = $trans['sales'];
                    $void  = $trans['void'];
                    $net = $trans['net'];
                    $eod = $this->old_grand_net_total($zread['from'],true);
                    $ids = $trans['all_ids'];
                    $all_ids = $ids;
                    $this->cashier_model->db = $this->load->database('main', TRUE);
                    $this->site_model->db = $this->load->database('main', TRUE);
                    $select = 'trans_sales_menus.*,menus.menu_code,menus.menu_name,menus.cost as sell_price,menus.menu_cat_id as cat_id,menus.menu_sub_cat_id as sub_cat_id';
                    $join = null;
                    $join['menus'] = array('content'=>'trans_sales_menus.menu_id = menus.menu_id');
                    $menus = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$ids),array('sales_id'=>'asc'),$join,true,$select);
                    $mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));                   
                    $sales_tax = $this->site_model->get_tbl('trans_sales_tax',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                    $sales_no_tax = $this->site_model->get_tbl('trans_sales_no_tax',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                    $sales_discs = $this->site_model->get_tbl('trans_sales_discounts',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                    $sales_charges = $this->site_model->get_tbl('trans_sales_charges',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                    $sales_payments = $this->site_model->get_tbl('trans_sales_payments',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                ############################################################################## 
                ### TRANSACTION LIST FILE
                    $transfile = substr($mall['lessee_name'],0,4).substr($mall['space_code'],0,3)."L.".$mon[date('m',strtotime($zread['read_date']))].date('d',strtotime($zread['read_date']));
                    $str = "";
                    $settled = $trans['sales']['settled']['orders'];
                    $all_orders = $trans['all_orders'];
                    usort($settled, function($a, $b) {
                        return strtotime($a->datetime) - strtotime($b->datetime);
                    });
                    $voided = $trans['sales']['void']['orders'];
                    usort($voided, function($a, $b) {
                        return strtotime($a->datetime) - strtotime($b->datetime);
                    });
                    $total_net = 0;
                    $total_disc = 0;
                    $total_tax = 0;
                    $total_otcharge = 0;
                    $total_svcharge = 0;
                    $total_taxable = 0;
                    $total_nontaxable = 0;
                    $total_cash = 0;
                    $total_chrg = 0;
                    foreach ($settled as $key => $row) {
                        $str .= substr($mall['space_code'],0,6)." ";
                        $str .= substr($mall['lessee_no'],0,5)." ";
                        $date = date('m/d/y',strtotime($row->datetime));
                        $time = date('H:i:s',strtotime($row->datetime));
                        $str .= substr($date,0,8)." ";
                        $str .= substr($time,0,8)." ";
                        $str .= substr($row->sales_id,0,9)." ";
                        // $ref = ltrim($row->trans_ref, '0');
                        $ref = preg_replace('/^0+/','', $row->trans_ref);
                        $str .= substr($ref,0,9)." ";
                        $str .= substr(TERMINAL_ID,0,9)." ";
                        $no_items = 0;
                        $gross = 0;
                        foreach ($menus as $mn) {
                            if($mn->sales_id == $row->sales_id){
                                $no_items += $mn->qty;
                            }
                            if($row->sales_id == $mn->sales_id){
                                $price = $mn->price;
                                foreach ($mods as $md) {
                                    if($row->sales_id == $md->sales_id && $mn->menu_id == $md->menu_id){
                                        $price += $md->price * $md->qty;
                                    }
                                }
                                $gross += $price * $mn->qty;
                            }
                        }
                        $disc = 0;
                        $non_disc = 0;
                        foreach ($sales_discs as $dc) {
                            if($dc->sales_id == $row->sales_id){
                                $disc += $dc->amount;
                                if($dc->no_tax == 1)
                                    $non_disc += $dc->amount;
                            }
                        }
                        $tax = 0;
                        foreach ($sales_tax as $tx) {
                            if($tx->sales_id == $row->sales_id){
                                $tax += $tx->amount;
                            }
                        }
                        $notax = 0;
                        foreach ($sales_no_tax as $ntx) {
                            if($ntx->sales_id == $row->sales_id){
                                $notax += $ntx->amount;
                            }
                        }
                        $svcharge = 0;
                        $otcharge = 0;
                        foreach ($sales_charges as $ch) {
                            if($ch->sales_id == $row->sales_id){
                                if($ch->charge_id == SERVICE_CHARGE_ID){
                                    $svcharge += $ch->amount;
                                }
                                else{
                                    $otcharge += $ch->amount;
                                }
                            }
                        }
                        $net = $row->total_amount;
                        $total_net += $net;
                        $less_vat = (($gross+$otcharge+$svcharge) - $disc) - $net;
                        if($less_vat < 0)
                            $less_vat = 0;
                        $nontaxable = $notax - $non_disc;
                        $taxable =   ($gross - $disc - $less_vat - $nontaxable) / (BASE_TAX + 1);
                        $str .= substr($no_items,0,6)." ";#ITEMS
                        $str .= substr(numInt($net),0,12)." ";#GROSS
                        $str .= substr(numInt($disc),0,9)." ";#DISC
                        $total_disc += $disc;
                        $str .= substr(numInt(0),0,12)." ";#VOID
                        $str .= substr(numInt(0),0,12)." ";#RFND
                        $str .= substr(numInt(0),0,9)." ";#ADJ
                        $str .= substr(numInt($tax),0,9)." ";#TAX
                        $total_tax += $tax;
                        $str .= substr(numInt($otcharge),0,12)." ";#OTHRS
                        $total_otcharge += $otcharge;
                        $str .= substr(numInt($svcharge),0,12)." ";#SVCRG
                        $total_svcharge += $svcharge;
                        $str .= substr(numInt($taxable),0,12)." ";#TAXSL
                        $total_taxable += $taxable;
                        $str .= substr(numInt($nontaxable),0,12)." ";#NTXSL
                        $total_nontaxable += $nontaxable;
                        $str .= substr(numInt($net-($svcharge+$otcharge)),0,12)." ";#NET
                        $cash = 0;
                        $chrg = 0;
                        foreach ($sales_payments as $py) {
                            if($row->sales_id == $py->sales_id){
                                if($py->amount > $py->to_pay)
                                    $amount = $py->to_pay;
                                else
                                    $amount = $py->amount;
                                if($py->payment_type != 'cash'){
                                    $chrg += $amount;
                                }
                                else
                                    $cash += $amount;
                            }
                        }
                        $str .= substr(numInt($chrg),0,12)." ";#CHRG
                        $total_chrg += $chrg;
                        $str .= substr(numInt($cash),0,12)."\r\n";#CASH
                        $total_cash += $cash;
                    }
                    $total_voided = 0;
                    $total_void_count = 0;
                    foreach ($voided as $key => $row) {
                        $str .= substr($mall['space_code'],0,6)." ";
                        $str .= substr($mall['lessee_no'],0,5)." ";
                        $date = date('m/d/y',strtotime($row->datetime));
                        $time = date('H:i:s',strtotime($row->datetime));
                        $str .= substr($date,0,8)." ";
                        $str .= substr($time,0,8)." ";
                        // $ref = ltrim($row->void_ref, '0');
                        $ref = preg_replace('/^0+/','', $row->void_ref);
                        $str .= substr($ref,0,9)." ";
                        $trans_ref = "";
                        foreach ($all_orders as $key => $ao) {
                            if($row->void_ref == $ao->sales_id ){
                                $trans_ref = $ao->trans_ref;
                            }
                        }
                        if($trans_ref != "")
                            $trans_ref = preg_replace('/^0+/','', $trans_ref);
                        $str .= substr($trans_ref,0,9)." ";
                        $str .= substr(TERMINAL_ID,0,9)." ";
                        $no_items = 0;
                        $gross = 0;
                        foreach ($menus as $mn) {
                            if($mn->sales_id == $row->sales_id){
                                $no_items += $mn->qty;
                            }
                            if($row->sales_id == $mn->sales_id){
                                $price = $mn->price;
                                foreach ($mods as $md) {
                                    if($row->sales_id == $md->sales_id && $mn->menu_id == $md->menu_id){
                                        $price += $md->price * $md->qty;
                                    }
                                }
                                $gross += $price * $mn->qty;
                            }
                        }
                        $disc = 0;
                        $non_disc = 0;
                        foreach ($sales_discs as $dc) {
                            if($dc->sales_id == $row->sales_id){
                                $disc += $dc->amount;
                                if($dc->no_tax == 1)
                                    $non_disc += $dc->amount;
                            }
                        }
                        $tax = 0;
                        foreach ($sales_tax as $tx) {
                            if($tx->sales_id == $row->sales_id){
                                $tax += $tx->amount;
                            }
                        }
                        $notax = 0;
                        foreach ($sales_no_tax as $ntx) {
                            if($ntx->sales_id == $row->sales_id){
                                $notax += $ntx->amount;
                            }
                        }
                        $svcharge = 0;
                        $otcharge = 0;
                        foreach ($sales_charges as $ch) {
                            if($ch->sales_id == $row->sales_id){
                                if($ch->charge_id == SERVICE_CHARGE_ID){
                                    $svcharge += $ch->amount;
                                }
                                else{
                                    $otcharge += $ch->amount;
                                }
                            }
                        }
                        $net = $row->total_amount;
                        $less_vat = (($gross+$otcharge+$svcharge) - $disc) - $net;
                        if($less_vat < 0)
                            $less_vat = 0;
                        $nontaxable = $notax - $non_disc;
                        $taxable =   ($gross - $disc - $less_vat - $nontaxable) / (BASE_TAX + 1);
                        $str .= substr($no_items,0,6)." ";#ITEMS
                        $str .= substr(numInt($gross),0,12)." ";#GROSS
                        $str .= substr(numInt($disc),0,9)." ";#DISC
                        $str .= substr(numInt($row->total_amount),0,12)." ";#VOID
                        $total_voided += $row->total_amount;
                        $str .= substr(numInt(0),0,12)." ";#RFND
                        $str .= substr(numInt(0),0,9)." ";#ADJ
                        $str .= substr(numInt($tax),0,9)." ";#TAX
                        $str .= substr(numInt($otcharge),0,12)." ";#OTHRS
                        $str .= substr(numInt($svcharge),0,12)." ";#SVCRG
                        $str .= substr(numInt($taxable),0,12)." ";#TAXSL
                        $str .= substr(numInt($nontaxable),0,12)." ";#NTXSL
                        $str .= substr(numInt($net-($svcharge+$otcharge)),0,12)." ";#NET
                        $cash = 0;
                        $chrg = 0;
                        foreach ($sales_payments as $py) {
                            if($row->void_ref == $py->sales_id){
                                if($py->amount > $py->to_pay)
                                    $amount = $py->to_pay;
                                else
                                    $amount = $py->amount;
                                if($py->payment_type != 'cash'){
                                    $chrg += $amount;
                                }
                                else
                                    $cash += $amount;
                            }
                        }
                        $str .= substr(numInt($chrg),0,12)." ";#CHRG
                        $total_chrg += $chrg;
                        $str .= substr(numInt($cash),0,12)."\r\n";#CASH
                        $total_cash += $cash;
                        $total_void_count++;
                    }                    
                    $this->write_file($file_path.$transfile,$str);
                ##############################################################################
                ### DAILY FILE
                    $dailyfile = substr($mall['lessee_name'],0,4).substr($mall['space_code'],0,3)."S.".$mon[date('m',strtotime($zread['read_date']))].date('d',strtotime($zread['read_date']));
                    $str = "";
                    $str .= substr($mall['space_code'],0,6)." ";
                    $str .= substr($mall['lessee_no'],0,5)." ";
                    $str .= substr(numInt($eod['true_grand_total']+$total_net+$total_voided),0,12)." ";
                    $str .= substr(numInt($eod['true_grand_total']),0,12)." ";
                    $str .= substr(numInt($total_net+$total_voided),0,12)." ";
                    $str .= substr(numInt($total_disc),0,9)." ";
                    $str .= substr(numInt($total_voided),0,12)." ";
                    $str .= substr(numInt(0),0,12)." ";
                    $str .= substr(numInt(0),0,12)." ";
                    $str .= substr(numInt($total_net),0,12)." ";
                    $str .= substr(numInt($total_tax),0,9)." ";
                    $str .= substr(numInt($total_otcharge),0,12)." ";
                    $str .= substr(numInt($total_svcharge),0,12)." ";
                    $str .= substr(numInt($total_taxable),0,12)." ";
                    $str .= substr(numInt($total_nontaxable),0,12)." ";
                    $str .= substr(numInt($total_net),0,12)." ";
                    $str .= substr(numInt($total_chrg),0,12)." ";
                    $str .= substr(numInt($total_cash),0,12)." ";
                    usort($all_ids, function($a, $b) {
                        return strtotime($a) - strtotime($b);
                    });
                    $first_id = 0;
                    if(isset($all_ids[0]))
                        $first_id = $all_ids[0];
                    $last_id = 0;
                    if(count($all_ids) > 0)
                        $last_id = array_values(array_slice($all_ids, -1))[0];
                    $str .= substr($first_id,0,9)." ";
                    $str .= substr($last_id,0,9)." ";
                    $str .= substr($last_id - $first_id + 1,0,9)." ";
                    $str .= substr($total_void_count,0,9)." ";
                    $str .= substr($eod['ctr'],0,9)." ";
                    $date = date('m/d/y',strtotime($zread['read_date']));
                    $str .= substr($date,0,8)." ";
                    $all_orders = $trans['all_orders'];
                    usort($all_orders, function($a, $b) {
                        return strtotime($a->datetime) - strtotime($b->datetime);
                    });
                    $time_open = 0;
                    $time_close = 0;
                    if(count($all_orders) > 0){
                        $op =  array_values($all_orders)[0];
                        $time_open = date('H:i:s',strtotime($op->datetime));
                        $cl = array_values(array_slice($all_orders, -1))[0];
                        $time_close = date('H:i:s',strtotime($cl->datetime));
                    }
                    $str .= substr($time_open,0,8)." ";
                    $str .= substr($time_close,0,8)." ";
                    for ($i=1; $i <= 10; $i++) { 
                        if($i == 10)
                            $str .= substr(numInt(0),0,12)."\r\n";
                        else
                            $str .= substr(numInt(0),0,12)." ";
                    }
                    $today_str = $str;
                    $this->write_file($file_path.$dailyfile,$str);
                ##############################################################################
                ### MONTHLY FILE
                    $monthlyfile = substr($mall['lessee_name'],0,4).substr($mall['space_code'],0,3)."C.".$mon[date('m',strtotime($zread['read_date']))]."00";
                    $str = "";
                    if(!file_exists($file_path.$monthlyfile)){
                        $str = $today_str;
                    }
                    else{
                        $fh = fopen($file_path.$monthlyfile, 'r');
                        $old_str = fread($fh, filesize($file_path.$monthlyfile));
                        fclose($fh);
                        $lines = explode('\r\n',$old_str);
                        $strs = array();
                        foreach ($lines as $ln) {
                            $lns = explode(' ',$ln);
                            $strs[$lns[23]] = $ln;
                        }
                        $today = date('m/d/y',strtotime($zread['read_date']));
                        $strs[$today] = $today_str;
                        $str = "";
                        foreach ($strs as $s) {
                            $str .= $s;
                        }
                    }
                    $this->write_file($file_path.$monthlyfile,$str);
                if($regen){
                    $error = 0;
                    $msg = "ARANETA File successfully created.";
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert("ARANETA File successfully created.","success");
                } 
            }
            else{
                $error = 1;
                $msg = "No Zread Found.";
                if($regen){
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert($msg,"error");
                }
            }
        }
        public function vista_file($zread_id=null,$regen=false){
            $error = 0;
            $error_msg = null;
            $mall_db = $this->site_model->get_tbl('vistamall');
            $stall_code = $mall_db[0]->stall_code;
            $sales_dep = $mall_db[0]->sales_dep;
            $file_path = filepathisize($mall_db[0]->file_path);
            $zread = $this->detail_zread($zread_id);
            if(isset($zread['read_date']) && $zread['read_date'] != null){
                $year = date('Y',strtotime($zread['read_date']));
                $file_path .= $year."/";
                if (!file_exists($file_path)) {   
                    mkdir($file_path, 0777, true);
                }
                ##############################################################################
                ### GET DATA
                    $curr = false;
                    $args['trans_sales.datetime >= '] = $zread['from'];             
                    $args['trans_sales.datetime <= '] = $zread['to'];  
                    $trans = $this->trans_sales($args,$curr);
                    $sales = $trans['sales'];
                    $void  = $trans['void'];
                    $net = $trans['net'];
                    $eod = $this->old_grand_net_total($zread['from']);
                    $ids = $trans['sales']['settled']['ids'];
                    $this->cashier_model->db = $this->load->database('main', TRUE);
                    $this->site_model->db = $this->load->database('main', TRUE);
                    $select = 'trans_sales_menus.*,menus.menu_code,menus.menu_name,menus.cost as sell_price,menus.menu_cat_id as cat_id,menus.menu_sub_cat_id as sub_cat_id';
                    $join = null;
                    $join['menus'] = array('content'=>'trans_sales_menus.menu_id = menus.menu_id');
                    $menus = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$ids),array('sales_id'=>'asc'),$join,true,$select);
                    $mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));
                    
                    $sales_tax = $this->site_model->get_tbl('trans_sales_tax',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                    $sales_no_tax = $this->site_model->get_tbl('trans_sales_no_tax',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                    $sales_discs = $this->site_model->get_tbl('trans_sales_discounts',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                    $sales_charges = $this->site_model->get_tbl('trans_sales_charges',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                ##############################################################################
                    $hour_code = array(1=>array('start'=>'01:01','end'=>'02:00'),2=>array('start'=>'02:01','end'=>'03:00'),
                                       3=>array('start'=>'03:01','end'=>'04:00'),4=>array('start'=>'04:01','end'=>'05:00'),
                                       5=>array('start'=>'05:01','end'=>'06:00'),6=>array('start'=>'06:01','end'=>'07:00'),
                                       7=>array('start'=>'07:01','end'=>'08:00'),8=>array('start'=>'08:01','end'=>'09:00'),
                                       9=>array('start'=>'09:01','end'=>'10:00'),10=>array('start'=>'10:01','end'=>'11:00'),
                                       11=>array('start'=>'11:01','end'=>'12:00'),12=>array('start'=>'12:01','end'=>'13:00'),
                                       13=>array('start'=>'13:01','end'=>'14:00'),14=>array('start'=>'14:01','end'=>'15:00'),
                                       15=>array('start'=>'15:01','end'=>'16:00'),16=>array('start'=>'16:01','end'=>'17:00'),
                                       17=>array('start'=>'17:01','end'=>'18:00'),18=>array('start'=>'18:01','end'=>'19:00'),
                                       19=>array('start'=>'19:01','end'=>'20:00'),20=>array('start'=>'20:01','end'=>'21:00'),
                                       21=>array('start'=>'21:01','end'=>'22:00'),22=>array('start'=>'22:01','end'=>'23:00'),
                                       23=>array('start'=>'23:01','end'=>'00:00'),24=>array('start'=>'00:01','end'=>'01:00'),
                                      );
                    $str = "";
                    if(count($trans['all_orders']) > 0){
                        $hour_sales = array();
                        foreach ($hour_code as $code => $range) {
                            $gross = 0;
                            $vat = 0;
                            $nonvat = 0;
                            $non_tax_disc = 0;
                            $discount = 0;
                            $charges = 0;
                            $trans_cnt = 0;
                            $cancel_cnt = 0;
                            $cancel_amt = 0;
                            $vat_sales = 0;
                            $non_vat = 0;
                            $sales_date = "";
                            $sales_hour = "";
                            foreach ($trans['sales']['settled']['orders'] as $val) {
                                if($val->type_id == SALES_TRANS && $val->trans_ref != "" && $val->inactive == 0){
                                    $time = DateTime::createFromFormat('Y-m-d H:i:s',$val->datetime);
                                    $start = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['start'].":00");
                                    $end = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['end'].":00");
                                    if($time >= $start && $time <= $end){
                                        $sales_date = sql2Date($val->datetime);
                                        $sales_hour = date('H:00',strtotime($val->datetime));
                                        foreach ($menus as $mn) {
                                            if($val->sales_id == $mn->sales_id){
                                                $price = $mn->price;
                                                foreach ($mods as $md) {
                                                    if($val->sales_id == $md->sales_id && $mn->menu_id == $md->menu_id){
                                                        $price += $md->price * $md->qty;
                                                    }
                                                }
                                                $gross += $price * $mn->qty;
                                            }
                                        }
                                        foreach ($sales_tax as $st) {
                                            if($st->sales_id == $val->sales_id)
                                                $vat += $st->amount;
                                        }
                                        foreach ($sales_no_tax as $snt) {
                                            if($snt->sales_id == $val->sales_id)
                                                $nonvat += $snt->amount;
                                        }
                                        foreach ($sales_discs as $sd) {
                                            if($sd->sales_id == $val->sales_id){
                                                $discount += $sd->amount;
                                                if($sd->no_tax == 1){
                                                    $non_tax_disc += $sd->amount;
                                                }
                                            }
                                        }
                                        foreach ($sales_charges as $sc) {
                                            if($sc->sales_id == $val->sales_id)
                                                $charges += $sc->amount;
                                        }
                                        $trans_cnt += 1;   
                                    }
                                }    
                            }
                            foreach ($trans['sales']['void']['orders'] as $val) {
                                $time = DateTime::createFromFormat('Y-m-d H:i:s',$val->datetime);
                                $start = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['start'].":00");
                                $end = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['end'].":00");
                                if($time >= $start && $time <= $end){
                                    $cancel_cnt += 1;
                                    $cancel_amt += $val->total_amount;
                                }    
                            }
                            #########################################################################################################
                            if($trans_cnt > 0){
                                $non_vat = $nonvat - $non_tax_disc;
                                $hour_sales[] = array(
                                    "record_id" => '01',
                                    "stall_code"=> '"'.$stall_code.'"',
                                    "sales_date"=> $sales_date,
                                    "sales_time"=> $sales_hour,
                                    "gross"     => numInt($gross),
                                    "vat"       => numInt($vat),
                                    "discount"  => numInt($discount),
                                    "charges"   => numInt($charges),
                                    "trans_cnt" => $trans_cnt,
                                    "sales_dep" => '"'.$sales_dep.'"',
                                    "no_refund" => 0,
                                    "amt_refund"=> numInt(0),
                                    "no_cancel" => $cancel_cnt,
                                    "amt_cancel"=> numInt($cancel_amt),
                                    "non_vat"   => numInt($non_vat),
                                    "pos_no"    => TERMINAL_NUMBER,
                                );
                            }
                        }
                        foreach ($hour_sales as $row) {
                            $str_row = '';
                            foreach ($row as $rw) {
                                $str_row .= $rw.' ';
                            }
                            $str_row = substr($str_row,0,-1)."\r\n";
                            $str .= $str_row;
                        }
                    }
                    $total_gross = 0;
                    $total_vat = 0;
                    $total_discount = 0;
                    $total_charge = 0;
                    $total_trans_cnt = 0;
                    $total_cancel_cnt = 0;
                    $total_cancel_amt = 0;
                    $total_non_vat = 0;
                    foreach ($hour_sales as $row) {
                        $total_gross += $row['gross'];
                        $total_vat += $row['vat'];
                        $total_discount += $row['discount'];
                        $total_charge += $row['charges'];
                        $total_trans_cnt += $row['trans_cnt'];
                        $total_cancel_cnt += $row['no_cancel'];
                        $total_cancel_amt += $row['amt_cancel'];
                        $total_non_vat += $row['non_vat'];
                    }
                    $str .= '99 "'.$stall_code.'" '.sql2Date($zread['read_date']).' ';
                    $str .= $total_gross.' '.$total_vat.' '.$total_discount.' '.$total_charge.' ';
                    $str .= $total_trans_cnt.' "'.$sales_dep.'" 0 '.num(0).' ';
                    $str .= $total_cancel_cnt.' '.$total_cancel_amt.' '.$total_non_vat.' '.TERMINAL_NUMBER."\r\n";

                    $old_gt = $eod['old_grand_total'];
                    $gt_ctr = $eod['ctr'];
                    $new_gt = $old_gt + $net;
                    $str .= '95 "'.$stall_code.'" '.sql2Date($zread['read_date']).' ';
                    $str .= numInt($old_gt).' '.numInt($new_gt).' '.$gt_ctr.' '.TERMINAL_NUMBER;
                    $ctr = 0;
                    $file = date('mdy',strtotime($zread['read_date']));
                    $has = true;
                    while ($has) {
                        $ctr++;
                        $samp = $file.str_pad($ctr, 2, '0', STR_PAD_LEFT).".sal";
                        if(!file_exists($file_path.$samp)){
                            $has = false;
                        }
                    }
                    $file = $file.str_pad($ctr, 2, '0', STR_PAD_LEFT).".sal";
                    $this->write_file($file_path.$file,$str);
                ##############################################################################
                if($regen){
                    $error = 0;
                    $msg = "VISTAMALL File successfully created.";
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert("VISTAMALL File successfully created.","success");
                }
            }
            else{
                $error = 1;
                $msg = "No Zread Found.";
                if($regen){
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert($msg,"error");
                }
            }    
        }
                public function cb_file($read_date=null,$zread_id=null,$invoice=""){ 
            // $read_date = '2015-09-28';
            // $read_date = '2015-09-29 14:25:56';
            // $zread_id = 38;

            $mgms = $this->site_model->get_tbl('cbmall');
            $mgm = array();
            if(count($mgms) > 0){
                $mgm = $mgms[0];            
            }
            $tenant_code = $mgm->tenant_code;
            $filepath = $mgm->file_path;

            $print_str = "";
            #### CREATE FILE 
                $args = array();
                $file_flg = null;
                if($zread_id != null){
                    $lastRead = $this->cashier_model->get_z_read($zread_id);
                    $zread = array();

                    if(count($lastRead) > 0){
                        foreach ($lastRead as $res) {
                            $zread = array(
                                'from' => $res->scope_from,
                                'to'   => $res->scope_to,
                                'old_gt_amnt' => $res->old_total,
                                'grand_total' => $res->grand_total,
                                'read_date' => $res->read_date,
                                'id' => $res->id,
                                'user_id'=>$res->user_id
                            );
                            $read_date = $res->read_date;
                        }           
                    $args['trans_sales.datetime >= '] = $zread['from'];             
                    $args['trans_sales.datetime <= '] = $zread['to'];
                    }
                    // $file_flg = "Z".date('ymd',strtotime($read_date)).".flg";
                }

                $file_txt = "TMS".date('ymd',strtotime($read_date))."".$invoice.".".TERMINAL_NUMBER;
                // $file_txt2 = date('ymd',strtotime($read_date)).".txt";
                // $file_txt3 = date('mdy',strtotime($read_date)).".txt";

                // $file_csv = date('mdY',strtotime($read_date)).".csv";
                // $file_csv2 = date('ymd',strtotime($read_date)).".csv";
                // $file_csv3 = date('mdy',strtotime($read_date)).".csv";

                // $file_flg = "Z".date('ymd',strtotime($read_date)).".flg";
                
                $year = date('Y',strtotime($read_date));
                $month = date('M',strtotime($read_date));
                if (!file_exists($filepath)) {   
                    mkdir($filepath, 0777, true);
                }
                
                $text = $filepath."/".$file_txt;
                // $text2 = "C:/SM/".$file_txt2;
                // $text3 = "C:/SM/".$file_txt3;

                // $csv = "C:/SM/".$file_csv;
                // $csv2 = "C:/SM/".$file_csv2;
                // $csv3 = "C:/SM/".$file_csv3;
                //var_dump($text); die();
                // $flg = null;
                // if($file_flg != null)
                //     $flg = "C:/SM/".$file_flg;
            #### GET POS MACHINE DETAILS
                $mesults = $this->site_model->get_tbl('branch_details');
                $mes = $mesults[0];
                $serial_no = $mes->serial;
                $machine_no = $mes->machine_no;
            #### GET TRANS
                if($zread_id == null){
                    $last_zread = $this->cashier_model->get_last_z_read(Z_READ,$read_date);
                    $date_from = null;
                    if(count($last_zread) > 0 ){
                        $args['trans_sales.datetime >= '] = $last_zread[0]->scope_to;             
                    }
                    $args['trans_sales.datetime <= '] = $read_date;
                }


                $curr = true; 
                $trans = $this->trans_sales($args,$curr);
                // echo '<pre>',print_r($trans),'</pre>';
                $sales = $trans['sales'];
                $net = $trans['net'];
                $customer_count = $trans['customer_count'];
                $ref_count = $trans['ref_count'];

                $gt = $this->old_grand_net_total($read_date);
                $old_gt = $gt['true_grand_total'];
                $new_gt = $gt['true_grand_total'] + $net;
            
            #### GROSS
                $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
                $gross = $trans_menus['gross'];
            #### DISCOUNTS
                $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
                $discounts = $trans_discounts['total']; 
                $tax_disc = $trans_discounts['tax_disc_total']; 
                $no_tax_disc = $trans_discounts['no_tax_disc_total']; 
                $sc_disc = 0;
                $pwd_disc = 0;
                $other_disc = 0;
                $emp_disc = 0;
                $vip_disc = 0;
                $promo_disc = 0;
                $sc_disc_c = $emp_disc_c = $promo_disc_c = $other_disc_c = $vat_ex_count = 0;
                foreach ($trans_discounts['types'] as $code => $disc) {
                    if($code == 'SNDISC'){
                        $sc_disc += $disc['amount'];
                        $sc_disc_c++;
                        $vat_ex_count += 1;
                    }
                    else if($code == 'PWDISC'){
                        // $pwd_disc += $disc['amount'];
                        $other_disc += $disc['amount'];
                        $other_disc_c++;
                        $vat_ex_count += 1;
                    }
                    else if($code == 'EMPDISC'){
                        $emp_disc += $disc['amount'];
                        $emp_disc_c++;
                    }
                    else if($code == 'VIPDISC'){
                        $vip_disc += $disc['amount'];
                    }
                    else if($code == 'PROMO'){
                        $promo_disc += $disc['amount'];
                        $promo_disc_c++;
                    }
                    else{
                        $other_disc += $disc['amount'];
                        $other_disc_c++;
                    }
                }
            #### VOIDS
                $total_void = 0;
                $total_void_ctr = 0;
                if(isset($trans['sales']['void']['orders']) && count($trans['sales']['void']['orders']) > 0){
                    $void = $trans['sales']['void']['orders'];
                    if(count($void) > 0){
                        foreach ($void as $v) {
                            $total_void += $v->total_amount;
                            $total_void_ctr += 1;
                        }                
                    } 
                }
            #### TAX
                $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
                $tax = $trans_tax['total'];
                $vatable_count = $trans_tax['vatable_count'];
            #### LOCAL TAX
                $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
                $local_tax = $trans_local_tax['total']; 
            #### CHARGES
                $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
                $charges_types = $trans_charges['types'];
                $charges = $trans_charges['total']; 
                $sc = 0;
                $oc = $local_tax;
                $sc_count = $oc_count = 0;
                foreach ($charges_types as $id => $row) {
                    if($id == SERVICE_CHARGE_ID){
                        $sc += $row['amount'];
                        $sc_count++;
                    }
                    else{
                        $oc += $row['amount'];
                        $oc_count++;
                    }
                }
            #### NO TAX
                $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
                $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
                $no_tax = $trans_no_tax['total'];
                $vat_ex_count = $trans_no_tax['vat_ex_count'];
                $zero_rated = $trans_zero_rated['total'];
                $no_tax -= $zero_rated;
                $vat_exempt_sales = numInt($no_tax) - numInt($no_tax_disc);
                // echo numInt($no_tax)."-".numInt($no_tax_disc."---<br>";
            #### PAYMENTS
                $cash_pay_sales = 0;
                $cash_pay_ctr = 0;
                $gc_pay_sales = 0;
                $gc_pay_ctr = 0;
                $other_pay_sales = 0;
                $other_pay_ctr = 0;
                $smac_pay_sales = 0;
                $smac_pay_ctr = 0;
                $eplus_pay_sales = 0;
                $eplus_pay_ctr = 0;
                $card_sales = 0;
                $card_sales_ctr = 0;
                $cards = array(
                    'debit' => 0,
                    'Master Card' => 0,
                    'VISA' => 0,
                    'AmEx' => 0,
                    'jcb' => 0,
                    'diners' => 0,
                    'other' => 0
                );
                $card_ctr = array(
                    'debit' => 0,
                    'Master Card' => 0,
                    'VISA' => 0,
                    'AmEx' => 0,
                    'jcb' => 0,
                    'diners' => 0,
                    'other' => 0
                );
                  
                if(count($sales['settled']['ids']) > 0){
                    $pargs["trans_sales_payments.sales_id"] = $sales['settled']['ids'];
                    if($zread_id != null)
                        $this->site_model->db = $this->load->database('main', TRUE);
                    else
                        $this->site_model->db = $this->load->database('default', TRUE);
                    $pesults = $this->site_model->get_tbl('trans_sales_payments',$pargs); 
                    // echo $this->site_model->db->last_query();
                    // echo '<pre>',print_r($pesults),'</pre>';
                    // die();
                    foreach ($pesults as $pes) {
                        if($pes->amount > $pes->to_pay)
                            $amount = $pes->to_pay;
                        else
                            $amount = $pes->amount;

                        if($pes->payment_type == 'cash'){
                            $cash_pay_sales += $amount;
                            $cash_pay_ctr += 1;
                        }
                        elseif($pes->payment_type == 'credit'){
                            if(isset($cards[$pes->card_type])){
                                $cards[$pes->card_type] += $amount;
                                $card_ctr[$pes->card_type] += 1;
                            }
                            else{
                                $cards['other'] += $amount;
                                $card_ctr['other'] += 1;
                            }
                            $card_sales += $amount;
                            $card_sales_ctr += 1;
                        }
                        elseif($pes->payment_type == 'debit'){
                            $cards['debit'] += $amount;
                            $card_ctr['debit'] += 1;
                            $card_sales += $amount;
                            $card_sales_ctr += 1;
                        }
                        elseif($pes->payment_type == 'gc'){
                            $gc_pay_sales += $amount;
                            $gc_pay_ctr += 1;
                        }
                        else{
                            $other_pay_sales += $amount;
                            $other_pay_ctr += 1;
                        }

                        if($pes->payment_type == 'smac'){
                            $smac_pay_sales += $amount;
                            $smac_pay_ctr += 1;
                        }
                        if($pes->payment_type == 'eplus'){
                            $eplus_pay_sales += $amount;
                            $eplus_pay_ctr += 1;
                        }

                    }
                }
            #### PRINT
                $charges_sales = $gc_pay_sales + $cards['debit'] + $other_pay_sales + $cards['Master Card'] + $cards['VISA']
                                 + $cards['AmEx'] + $cards['diners'] + $cards['jcb'] + $cards['other'];
                $daily_sales = ($cash_pay_sales + $charges_sales );
                $credit_sales = $cards['Master Card'] + $cards['VISA'] + $cards['AmEx'] + $cards['diners'] + $cards['jcb'] + $cards['other'];

                $tax_disc -= $pwd_disc;
                $department_sum = (($daily_sales - $sc - $oc + $tax_disc - $vat_exempt_sales)/1.12) + $vat_exempt_sales + $sc + $oc;
                $vat_inclusive_sales = $daily_sales - $sc -$oc + $tax_disc - $vat_exempt_sales;
                $sales_with_vat = ($daily_sales - $sc - $oc + $tax_disc - $vat_exempt_sales);
                $vat = $sales_with_vat - ($sales_with_vat/1.12);

                $vatable_sales = $sales_with_vat / 1.12;

                // echo "DAILY SALES = ".$daily_sales."<br><br>";
                // echo "DEPARTMENT SUM = ".$department_sum."<br>";
                // echo "VAT INCLUSIVE SALES = ".$vat_inclusive_sales."<br>";
                // echo "CHARGES SALES = ".$charges_sales."<br>";
                // echo "VAT = ".$vat."<br>";
                
                //HEADER 1 - 6
                $print_str = tabr($print_str,array(date('m-d-Y',strtotime($read_date)),$tenant_code,date('Y-m-d',strtotime($read_date)),date('H:i:s',strtotime($read_date)),TERMINAL_NUMBER,$invoice));
                // 7
                $print_str = tabr($print_str,numInt($gross));
                // 8
                $print_str = tabr($print_str,numInt($vatable_sales));
                // 9
                $print_str = tabr($print_str,numInt(0));
                // 10
                $print_str = tabr($print_str,numInt($no_tax));
                // 11
                $print_str = tabr($print_str,numInt($sc_disc));
                // 12
                $print_str = tabr($print_str,numInt($emp_disc));
                // 13
                $print_str = tabr($print_str,numInt($promo_disc));
                // 14
                $print_str = tabr($print_str,numInt($other_disc));
                // 15
                $print_str = tabr($print_str,numInt(0));
                // 16
                $print_str = tabr($print_str,numInt($sc));
                // 17
                $print_str = tabr($print_str,numInt($oc));
                // 18
                $print_str = tabr($print_str,numInt($net));
                // 19
                $print_str = tabr($print_str,numInt($old_gt));
                // 20
                $print_str = tabr($print_str,numInt($new_gt));
                // 21
                $zread_ctr = 0;
                if($zread_id != null)
                    $zread_ctr = $gt['ctr'];
                $print_str = tabr($print_str,$zread_ctr);
                // 22
                $print_str = tabr($print_str,numInt($cash_pay_sales));
                // 23
                $print_str = tabr($print_str,numInt($card_sales));
                // 24
                $print_str = tabr($print_str,numInt(0));
                // 25
                $print_str = tabr($print_str,numInt($gc_pay_sales));
                // 26
                $print_str = tabr($print_str,numInt($eplus_pay_sales));
                // 27
                $print_str = tabr($print_str,numInt($other_pay_sales));
                // 28
                $print_str = tabr($print_str,numInt($total_void));
                // 29
                $print_str = tabr($print_str,numInt(0));
                // 30
                $print_str = tabr($print_str,numInt(0));
                // 31
                $print_str = tabr($print_str,$vatable_count);
                // 32
                $print_str = tabr($print_str,0);
                // 33
                $print_str = tabr($print_str,$vat_ex_count);
                // 34-37
                $print_str = tabr($print_str,array($sc_disc_c,$emp_disc_c,$promo_disc_c,$other_disc_c));
                // 38
                $print_str = tabr($print_str,0);
                // 39-40
                $print_str = tabr($print_str,array($sc_count,$oc_count));
                // 41
                $print_str = tabr($print_str,$cash_pay_ctr);
                // 42
                $print_str = tabr($print_str,$card_sales_ctr);
                // 43
                $print_str = tabr($print_str,0);
                // 44
                $print_str = tabr($print_str,numInt($gc_pay_ctr));
                // 45
                $print_str = tabr($print_str,numInt($eplus_pay_ctr));
                // 46
                $print_str = tabr($print_str,numInt($other_pay_ctr));
                // 47 - 50
                $print_str = tabr($print_str,array(0,$total_void_ctr,0,0));
                // 51
                $print_str = tabr($print_str,$customer_count);
                // 52
                $print_str = tabr($print_str,$ref_count);
                // 53 - 55
                $print_str = tabr($print_str,array(0,'S',numInt(12)));

                // // HEADER 1 - 5
                // $print_str = commar($print_str,array($br_code,$tenant_code,$class_code,$trade_code,$outlet_no));
                // // OLD GT AND NEW GT 6 - 7
                // $print_str = commar($print_str,array(numInt($new_gt),numInt($old_gt) ));
                // // SALES TYPE 8
                // $print_str = commar($print_str,'SM01');
                // // DEPARTMENT SUM 9
                // $print_str = commar($print_str,numInt($department_sum));
                // // REGULAR DISCOUNT 10
                // $print_str = commar($print_str,numInt($other_disc));
                // // EMPLOYEE DISCOUNT 11
                // $print_str = commar($print_str,numInt($emp_disc));
                // // SENIOR CITIZEN DISCOUNT 12
                // $print_str = commar($print_str,numInt($sc_disc));
                // // VIP DISCOUNT 13
                // $print_str = commar($print_str,numInt($vip_disc));
                // // PWD DISCOUNT 14
                // // $pwd_disc = $pwd_disc / 1.12;
                // $print_str = commar($print_str,numInt($pwd_disc));
                // // GPC DISCOUNT 15
                // $print_str = commar($print_str,numInt(0));
                // // RESERVE DISCOUNT 16 - 21
                // // for ($i=0; $i <= 6; $i++) { 
                //     $print_str = commar($print_str,numInt(0));
                //     $print_str = commar($print_str,numInt(0));
                //     $print_str = commar($print_str,numInt(0));
                //     $print_str = commar($print_str,numInt(0));
                //     $print_str = commar($print_str,numInt(0));
                //     $print_str = commar($print_str,numInt(0));
                // // }
                // // VAT 22
                // $print_str = commar($print_str,numInt($vat));
                // // echo $tax;
                // // OTHER VAT 23
                // $print_str = commar($print_str,numInt($local_tax));
                // // ADJUSTMENTS 24 - 28
                // $print_str = commar($print_str,array(numInt(0),numInt(0),numInt(0),numInt(0),numInt(0))); 
                // // DAILY SALES 29


                // $print_str = commar($print_str,numInt($daily_sales));
                // // VOID 30
                // $print_str = commar($print_str,numInt($total_void));
                // // REFUND 31
                // $print_str = commar($print_str,numInt(0));
                // // SALES INCLUSIVE OF VAT 32
                // $print_str = commar($print_str,numInt($vat_inclusive_sales));
                // // NON VAT SALES 33
                // $print_str = commar($print_str,numInt($vat_exempt_sales));
                // // PAYMENTS 34 - 44 
                //     ##CHARGE SALES
                //      $print_str = commar($print_str,numInt($charges_sales));        
                //     ##CASH SALES
                //          $print_str = commar($print_str,numInt($cash_pay_sales));        
                //     ##GC SALES
                //          $print_str = commar($print_str,numInt($gc_pay_sales));        
                //     ##DEBIT SALES
                //          $print_str = commar($print_str,numInt($cards['debit']));        
                //     ##OTHER SALES
                //          $print_str = commar($print_str,numInt($other_pay_sales));     
                //     ##MASTER CARD SALES
                //          $print_str = commar($print_str,numInt($cards['Master Card']));        
                //     ##VISA SALES
                //          $print_str = commar($print_str,numInt($cards['VISA']));
                //     ##AMERICAN EXPRESS SALES
                //          $print_str = commar($print_str,numInt($cards['AmEx']));
                //     ##DINERS SALES
                //          $print_str = commar($print_str,numInt($cards['diners']));
                //     ##JCB SALES
                //          $print_str = commar($print_str,numInt($cards['jcb']));
                //     ##OTHER CARD
                //          $print_str = commar($print_str,numInt($cards['other']));                            
                // // SERVICE CHARGE 45
                // $print_str = commar($print_str,numInt($sc));
                // // OTHER CHARGE 46
                // $print_str = commar($print_str,numInt($local_tax));
                // // OTHER CHARGE 47 - 49
                //     $allIDS = $sales['settled']['ids'];
                //     if(count($allIDS) == 0){
                //         $print_str = commar($print_str,0);
                //         $print_str = commar($print_str,0);
                //         $print_str = commar($print_str,0);
                //     }
                //     else{
                //         asort($allIDS);
                //         foreach ($allIDS as $key) {
                //             $print_str = commar($print_str,$key);
                //             break;
                //         }
                //         $last_key = null;
                //         foreach ($allIDS as $key) {
                //            $last_key = $key;
                //         }
                //         $print_str = commar($print_str,$last_key);
                //         $print_str = commar($print_str,count($allIDS));
                //     }
                // // BEGINNING INVOICE 50
                // $print_str = commar($print_str,iSetObj($trans['first_ref'],'trans_ref',0));
                // // ENDING INVOICE 51
                // $print_str = commar($print_str,iSetObj($trans['last_ref'],'trans_ref',0));
                // // PAYMENT COUNTER TRANS 52 - 61
                //     ##CASH TRANSACTIONS
                //          $print_str = commar($print_str,$cash_pay_ctr);
                //     ##GC TRANSACTIONS
                //          $print_str = commar($print_str,$gc_pay_ctr);
                //     ##debit TRANSACTIONS
                //          $print_str = commar($print_str,$card_ctr['debit']);
                //     ##OTHER TENDER TRANSACTIONS
                //          $print_str = commar($print_str,$other_pay_ctr);
                //     ##MASTER CARD TRANSACTIONS
                //          $print_str = commar($print_str,$card_ctr['Master Card']);
                //     ##VISA TRANSACTIONS
                //          $print_str = commar($print_str,$card_ctr['VISA']);
                //     ##AMERICAN EXPRESS TRANSACTIONS
                //          $print_str = commar($print_str,$card_ctr['AmEx']);
                //     ##DINERS TRANSACTIONS
                //          $print_str = commar($print_str,$card_ctr['diners']);
                //     ##jcb TRANSACTIONS
                //          $print_str = commar($print_str,$card_ctr['jcb']);
                //     ##OTHER TRANSACTIONS
                //          $print_str = commar($print_str,$card_ctr['other']);
                // // MACHINE NO 62
                // $print_str = commar($print_str,$machine_no);
                // // SERIAL NO 63
                // $print_str = commar($print_str,$serial_no);
                // // SERIAL NO 64
                // $zread_ctr = 0;
                // if($zread_id != null)
                //     $zread_ctr = $gt['ctr'];
                // $print_str = commar($print_str,$zread_ctr);
                // // SERIAL NO 65
                // $dt = $read_date;
                // if($zread_id != null){
                //     $dt = $zread['to'];
                // }
                // $print_str = commar($print_str,date('His',strtotime($dt)) );
                // // SERIAL NO 66
                // $print_str = commar($print_str,date('mdY',strtotime($dt)) );

                // $print_str = substr($print_str,0,-1);
                // $fp = fopen($text, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);
                $fp = fopen($text, "w+");
                fwrite($fp,$print_str);
                fclose($fp);
                // $fp = fopen($text3, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);

                // $fp = fopen($csv, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);
                // $fp = fopen($csv2, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);
                // $fp = fopen($csv3, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);

                // if($flg != null){
                //     $fp = fopen($flg, "w+");
                //     fwrite($fp,$print_str);
                //     fclose($fp);
                // }
            // echo "<pre>$print_str</pre>";
        }
        public function cbmall_file($read_date=null,$zread_id=null,$regen=false){
            $mgms = $this->site_model->get_tbl('cbmall');
            $mgm = array();
            if(count($mgms) > 0){
                $mgm = $mgms[0];            
            }
            $tenant_code = $mgm->tenant_code;
            $filepath = $mgm->file_path;

            $print_str = "";
            #### CREATE FILE 
                $args = array();
                $file_flg = null;
                if($zread_id != null){
                    $lastRead = $this->cashier_model->get_z_read($zread_id);
                    $zread = array();

                    if(count($lastRead) > 0){
                        foreach ($lastRead as $res) {
                            $zread = array(
                                'from' => $res->scope_from,
                                'to'   => $res->scope_to,
                                'old_gt_amnt' => $res->old_total,
                                'grand_total' => $res->grand_total,
                                'read_date' => $res->read_date,
                                'id' => $res->id,
                                'user_id'=>$res->user_id
                            );
                            $read_date = $res->read_date;
                        }           
                    $args['trans_sales.datetime >= '] = $zread['from'];             
                    $args['trans_sales.datetime <= '] = $zread['to'];
                    }
                    // $file_flg = "Z".date('ymd',strtotime($read_date)).".flg";
                }

                $file_txt = "TMS".date('ymd',strtotime($read_date))."".TERMINAL_NUMBER.".EOD";
                // $file_txt2 = date('ymd',strtotime($read_date)).".txt";
                // $file_txt3 = date('mdy',strtotime($read_date)).".txt";

                // $file_csv = date('mdY',strtotime($read_date)).".csv";
                // $file_csv2 = date('ymd',strtotime($read_date)).".csv";
                // $file_csv3 = date('mdy',strtotime($read_date)).".csv";

                // $file_flg = "Z".date('ymd',strtotime($read_date)).".flg";
                
                $year = date('Y',strtotime($read_date));
                $month = date('M',strtotime($read_date));
                if (!file_exists($filepath)) {   
                    mkdir($filepath, 0777, true);
                }
                
                $text = $filepath."/".$file_txt;
                // $text2 = "C:/SM/".$file_txt2;
                // $text3 = "C:/SM/".$file_txt3;

                // $csv = "C:/SM/".$file_csv;
                // $csv2 = "C:/SM/".$file_csv2;
                // $csv3 = "C:/SM/".$file_csv3;
                //var_dump($text); die();
                // $flg = null;
                // if($file_flg != null)
                //     $flg = "C:/SM/".$file_flg;
            #### GET POS MACHINE DETAILS
                $mesults = $this->site_model->get_tbl('branch_details');
                $mes = $mesults[0];
                $serial_no = $mes->serial;
                $machine_no = $mes->machine_no;
            #### GET TRANS
                if($zread_id == null){
                    $last_zread = $this->cashier_model->get_last_z_read(Z_READ,$read_date);
                    $date_from = null;
                    if(count($last_zread) > 0 ){
                        $args['trans_sales.datetime >= '] = $last_zread[0]->scope_to;             
                    }
                    $args['trans_sales.datetime <= '] = $read_date;
                }


                $curr = true; 
                $trans = $this->trans_sales($args,$curr);
                // echo '<pre>',print_r($trans['first_ref']),'</pre>';
                $sales = $trans['sales'];
                $net = $trans['net'];
                $customer_count = $trans['customer_count'];
                $ref_count = $trans['ref_count'];

                $gt = $this->old_grand_net_total($read_date);
                $old_gt = $gt['true_grand_total'];
                $new_gt = $gt['true_grand_total'] + $net;
            
            #### GROSS
                $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
                $gross = $trans_menus['gross'];
            #### DISCOUNTS
                $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
                $discounts = $trans_discounts['total']; 
                $tax_disc = $trans_discounts['tax_disc_total']; 
                $no_tax_disc = $trans_discounts['no_tax_disc_total']; 
                $sc_disc = 0;
                $pwd_disc = 0;
                $other_disc = 0;
                $emp_disc = 0;
                $vip_disc = 0;
                $promo_disc = 0;
                $sc_disc_c = $emp_disc_c = $promo_disc_c = $other_disc_c = $all_disc_c = 0;
                foreach ($trans_discounts['types'] as $code => $disc) {
                    if($code == 'SNDISC'){
                        $sc_disc += $disc['amount'];
                        $sc_disc_c++;
                    }
                    // else if($code == 'PWDISC'){
                    //     $pwd_disc += $disc['amount'];
                    // }
                    else if($code == 'EMPDISC'){
                        $emp_disc += $disc['amount'];
                        $emp_disc_c++;
                    }
                    else if($code == 'VIPDISC'){
                        $vip_disc += $disc['amount'];
                    }
                    else if($code == 'PROMO'){
                        $promo_disc += $disc['amount'];
                        $promo_disc_c++;
                    }
                    else{
                        $other_disc += $disc['amount'];
                        $other_disc_c++;
                    }
                    $all_disc_c += 1;
                }
            #### VOIDS
                $total_void = 0;
                $total_void_ctr = 0;
                if(isset($trans['sales']['void']['orders']) && count($trans['sales']['void']['orders']) > 0){
                    $void = $trans['sales']['void']['orders'];
                    if(count($void) > 0){
                        foreach ($void as $v) {
                            $total_void += $v->total_amount;
                            $total_void_ctr += 1;
                        }                
                    } 
                }
            #### TAX
                $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
                $tax = $trans_tax['total'];
            #### LOCAL TAX
                $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
                $local_tax = $trans_local_tax['total']; 
            #### CHARGES
                $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
                $charges_types = $trans_charges['types'];
                $charges = $trans_charges['total']; 
                $sc = 0;
                $oc = $local_tax;
                $sc_count = $oc_count = 0;
                foreach ($charges_types as $id => $row) {
                    if($id == SERVICE_CHARGE_ID){
                        $sc += $row['amount'];
                        $sc_count++;
                    }
                    else{
                        $oc += $row['amount'];
                        $oc_count++;
                    }
                }
            #### NO TAX
                $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
                $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
                $no_tax = $trans_no_tax['total'];
                $zero_rated = $trans_zero_rated['total'];
                $no_tax -= $zero_rated;
                $vat_exempt_sales = numInt($no_tax) - numInt($no_tax_disc);
                // echo numInt($no_tax)."-".numInt($no_tax_disc."---<br>";
            #### PAYMENTS
                $cash_pay_sales = 0;
                $cash_pay_ctr = 0;
                $gc_pay_sales = 0;
                $gc_pay_ctr = 0;
                $other_pay_sales = 0;
                $other_pay_ctr = 0;
                $smac_pay_sales = 0;
                $smac_pay_ctr = 0;
                $eplus_pay_sales = 0;
                $eplus_pay_ctr = 0;
                $card_sales = 0;
                $card_sales_ctr = 0;
                $cards = array(
                    'debit' => 0,
                    'Master Card' => 0,
                    'VISA' => 0,
                    'AmEx' => 0,
                    'jcb' => 0,
                    'diners' => 0,
                    'other' => 0
                );
                $card_ctr = array(
                    'debit' => 0,
                    'Master Card' => 0,
                    'VISA' => 0,
                    'AmEx' => 0,
                    'jcb' => 0,
                    'diners' => 0,
                    'other' => 0
                );
                  
                if(count($sales['settled']['ids']) > 0){
                    $pargs["trans_sales_payments.sales_id"] = $sales['settled']['ids'];
                    if($zread_id != null)
                        $this->site_model->db = $this->load->database('main', TRUE);
                    else
                        $this->site_model->db = $this->load->database('default', TRUE);
                    $pesults = $this->site_model->get_tbl('trans_sales_payments',$pargs); 
                    // echo $this->site_model->db->last_query();
                    // echo '<pre>',print_r($pesults),'</pre>';
                    // die();
                    foreach ($pesults as $pes) {
                        if($pes->amount > $pes->to_pay)
                            $amount = $pes->to_pay;
                        else
                            $amount = $pes->amount;

                        if($pes->payment_type == 'cash'){
                            $cash_pay_sales += $amount;
                            $cash_pay_ctr += 1;
                        }
                        elseif($pes->payment_type == 'credit'){
                            if(isset($cards[$pes->card_type])){
                                $cards[$pes->card_type] += $amount;
                                $card_ctr[$pes->card_type] += 1;
                            }
                            else{
                                $cards['other'] += $amount;
                                $card_ctr['other'] += 1;
                            }
                            $card_sales += $amount;
                            $card_sales_ctr += 1;
                        }
                        elseif($pes->payment_type == 'debit'){
                            $cards['debit'] += $amount;
                            $card_ctr['debit'] += 1;
                            $card_sales += $amount;
                            $card_sales_ctr += 1;
                        }
                        elseif($pes->payment_type == 'gc'){
                            $gc_pay_sales += $amount;
                            $gc_pay_ctr += 1;
                        }
                        else{
                            $other_pay_sales += $amount;
                            $other_pay_ctr += 1;
                        }

                        if($pes->payment_type == 'smac'){
                            $smac_pay_sales += $amount;
                            $smac_pay_ctr += 1;
                        }
                        if($pes->payment_type == 'eplus'){
                            $eplus_pay_sales += $amount;
                            $eplus_pay_ctr += 1;
                        }

                    }
                }
            #### PRINT
                $charges_sales = $gc_pay_sales + $cards['debit'] + $other_pay_sales + $cards['Master Card'] + $cards['VISA']
                                 + $cards['AmEx'] + $cards['diners'] + $cards['jcb'] + $cards['other'];
                $daily_sales = ($cash_pay_sales + $charges_sales );
                $credit_sales = $cards['Master Card'] + $cards['VISA'] + $cards['AmEx'] + $cards['diners'] + $cards['jcb'] + $cards['other'];

                $tax_disc -= $pwd_disc;
                $department_sum = (($daily_sales - $sc - $oc + $tax_disc - $vat_exempt_sales)/1.12) + $vat_exempt_sales + $sc + $oc;
                $vat_inclusive_sales = $daily_sales - $sc -$oc + $tax_disc - $vat_exempt_sales;
                $sales_with_vat = ($daily_sales - $sc - $oc + $tax_disc - $vat_exempt_sales);
                $vat = $sales_with_vat - ($sales_with_vat/1.12);

                $vatable_sales = $sales_with_vat / 1.12;

                // echo "DAILY SALES = ".$daily_sales."<br><br>";
                // echo "DEPARTMENT SUM = ".$department_sum."<br>";
                // echo "VAT INCLUSIVE SALES = ".$vat_inclusive_sales."<br>";
                // echo "CHARGES SALES = ".$charges_sales."<br>";
                // echo "VAT = ".$vat."<br>";
                
                //HEADER 1 - 3
                $print_str = tabr($print_str,array($tenant_code,TERMINAL_NUMBER,date('Y-m-d',strtotime($read_date))));
                // 4 - 5
                $print_str = tabr($print_str,array(iSetObj($trans['first_ref'],'trans_ref',0),iSetObj($trans['last_ref'],'trans_ref',0)));
                // 6
                $print_str = tabr($print_str,numInt($gross));
                // 7
                $print_str = tabr($print_str,numInt($vatable_sales));
                // 8
                $print_str = tabr($print_str,numInt(0));
                // 9
                $print_str = tabr($print_str,numInt($no_tax));
                // 10
                $print_str = tabr($print_str,numInt($total_void));
                // 11
                $print_str = tabr($print_str,$total_void_ctr);
                // 12
                $print_str = tabr($print_str,numInt($discounts));
                // 13
                $print_str = tabr($print_str,$all_disc_c);
                // 14
                $print_str = tabr($print_str,numInt(0));
                // 15
                $print_str = tabr($print_str,numInt(0));
                // 16
                $print_str = tabr($print_str,numInt($sc_disc));
                // 17
                $print_str = tabr($print_str,$sc_disc_c);
                // 18
                $print_str = tabr($print_str,numInt($sc));
                // 19
                $print_str = tabr($print_str,numInt($card_sales));
                // 20
                $print_str = tabr($print_str,numInt($cash_pay_sales));
                // 21
                $print_str = tabr($print_str,numInt(0));
                // 22
                $print_str = tabr($print_str,numInt($gc_pay_sales));
                // 23
                $print_str = tabr($print_str,numInt($eplus_pay_sales));
                // 24
                $print_str = tabr($print_str,numInt($other_pay_sales));
                // 25
                $print_str = tabr($print_str,0);
                // 26
                $zread_ctr = 0;
                if($zread_id != null)
                    $zread_ctr = $gt['ctr'];
                $print_str = tabr($print_str,$zread_ctr - 1);
                // 27
                $print_str = tabr($print_str,numInt($old_gt));
                // 28
                $print_str = tabr($print_str,$zread_ctr);
                // 29
                $print_str = tabr($print_str,numInt($new_gt));
                // 30
                $print_str = tabr($print_str,$ref_count);

                // $print_str = substr($print_str,0,-1);
                // $fp = fopen($text, "w+");
                // fwrite($fp,$print_str);
                // fclose($fp);
                $fp = fopen($text, "w+");
                fwrite($fp,$print_str);
                fclose($fp);

                if($regen){
                    $error = 0;
                    $msg = "CBMALL File successfully created.";
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert("CBMALL File successfully created.","success");
                }

               
            // echo "<pre>$print_str</pre>";
        }

        public function megaworld_file($zread_id=null,$regen=false){
            $error = 0;
            $error_msg = null;
            $mall_db        = $this->site_model->get_tbl('megaworld');
            $tenant_code    = $mall_db[0]->tenant_code;
            $sales_type     = $mall_db[0]->sales_type;
            $file_path      = filepathisize($mall_db[0]->file_path);
            $zread          = $this->detail_zread($zread_id);
            $eod_ctr = $zread['ctr'] + 1;
            $dlmtr          = "\r\n";
            $mon = array('01'=>'1','02'=>'2','03'=>'3','04'=>'4','05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'A','11'=>'B','12'=>'C');
            if(isset($zread['read_date']) && $zread['read_date'] != null){
                $year = date('Y',strtotime($zread['read_date']));
                $file_path .= $year."/";
                if (!file_exists($file_path)) {   
                    mkdir($file_path, 0777, true);
                }
                ###################################################################################################################################
                ### GET DATA
                    $curr = false;
                    $args['trans_sales.datetime >= '] = $zread['from'];             
                    $args['trans_sales.datetime <= '] = $zread['to'];  
                    $trans = $this->trans_sales($args,$curr);
                    // echo '<pre>',print_r($trans),'</pre>';
                    // die();
                    $sales = $trans['sales'];
                    $void  = $trans['void'];
                    $net = $trans['net'];
                    $eod = $this->old_grand_net_total($zread['from']);
                    $ids = $trans['sales']['settled']['ids'];
                    $this->cashier_model->db = $this->load->database('main', TRUE);
                    $this->site_model->db = $this->load->database('main', TRUE);
                    $select = 'trans_sales_menus.*,menus.menu_code,menus.menu_name,menus.cost as sell_price,menus.menu_cat_id as cat_id,menus.menu_sub_cat_id as sub_cat_id';
                    $join = null;
                    if($ids){
                        $menus = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$ids),array('sales_id'=>'asc'),$join,true,$select);
                        $mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));
                        $sales_tax = $this->site_model->get_tbl('trans_sales_tax',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                        $sales_no_tax = $this->site_model->get_tbl('trans_sales_no_tax',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                        $sales_discs = $this->site_model->get_tbl('trans_sales_discounts',array('sales_id'=>$ids),array('sales_id'=>'asc'),
                                                                   array('receipt_discounts'=>'trans_sales_discounts.disc_id = receipt_discounts.disc_id'),
                                                                   true,"trans_sales_discounts.*,receipt_discounts.disc_name as disc_desc"  
                                                                  );
                        $sales_charges = $this->site_model->get_tbl('trans_sales_charges',array('sales_id'=>$ids),array('sales_id'=>'asc'));
                        $sales_payments = $this->site_model->get_tbl('trans_sales_payments',array('sales_id'=>$ids),array('sales_id'=>'asc'));

                        // die('ssssaaaaa');
                    }else{
                        // die('ssss');
                        $menus = array();
                        $mods = array();
                        $sales_tax = array();
                        $sales_no_tax = array();
                        $sales_discs = array();
                        $sales_charges = array();
                        $sales_payments = array();
                    }
                ###################################################################################################################################
                ### HOURLY FILE
                     $hour_code = array(1=>array('start'=>'01:00:01','end'=>'02:00:00'),2=>array('start'=>'02:00:01','end'=>'03:00:00'),
                                       3=>array('start'=>'03:00:01','end'=>'04:00:00'),4=>array('start'=>'04:00:01','end'=>'05:00:00'),
                                       5=>array('start'=>'05:00:01','end'=>'06:00:00'),6=>array('start'=>'06:00:01','end'=>'07:00:00'),
                                       7=>array('start'=>'07:00:01','end'=>'08:00:00'),8=>array('start'=>'08:00:01','end'=>'09:00:00'),
                                       9=>array('start'=>'09:00:01','end'=>'10:00:00'),10=>array('start'=>'10:00:01','end'=>'11:00:00'),
                                       11=>array('start'=>'11:00:01','end'=>'12:00:00'),12=>array('start'=>'12:00:01','end'=>'13:00:00'),
                                       13=>array('start'=>'13:00:01','end'=>'14:00:00'),14=>array('start'=>'14:00:01','end'=>'15:00:00'),
                                       15=>array('start'=>'15:00:01','end'=>'16:00:00'),16=>array('start'=>'16:00:01','end'=>'17:00:00'),
                                       17=>array('start'=>'17:00:01','end'=>'18:00:00'),18=>array('start'=>'18:00:01','end'=>'19:00:00'),
                                       19=>array('start'=>'19:00:01','end'=>'20:00:00'),20=>array('start'=>'20:00:01','end'=>'21:00:00'),
                                       21=>array('start'=>'21:00:01','end'=>'22:00:00'),22=>array('start'=>'22:00:01','end'=>'23:00:00'),
                                       23=>array('start'=>'23:00:01','end'=>'23:59:59'),24=>array('start'=>'00:00:01','end'=>'01:00:00'),
                                      );
                    $str = "";
                    if(count($trans['all_orders']) > 0){
                        $hour_sales = array();
                        $sales_date = "";
                        foreach ($hour_code as $code => $range) {
                            $gross = 0;
                            $net = 0;
                            $vat = 0;
                            $nonvat = 0;
                            $non_tax_disc = 0;
                            $discount = 0;
                            $charges = 0;
                            $trans_cnt = 0;
                            $cancel_cnt = 0;
                            $cancel_amt = 0;
                            $vat_sales = 0;
                            $non_vat = 0;
                            $sales_hour = "";
                            $guest = 0;
                            foreach ($trans['sales']['settled']['orders'] as $val) {
                                if($val->type_id == SALES_TRANS && $val->trans_ref != "" && $val->inactive == 0){
                                    $time = DateTime::createFromFormat('Y-m-d H:i:s',$val->datetime);
                                    $start = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['start']);
                                    $end = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['end']);
                                    if($time >= $start && $time <= $end){
                                        $sales_date = sql2Date($val->datetime);
                                        // echo $sales_date.'in';
                                        $sales_hour = date('H:00',strtotime($val->datetime));
                                        $net += $val->total_amount;
                                        $guest += $val->guest;
                                        foreach ($menus as $mn) {
                                            if($val->sales_id == $mn->sales_id){
                                                $price = $mn->price;
                                                foreach ($mods as $md) {
                                                    if($val->sales_id == $md->sales_id && $mn->menu_id == $md->menu_id){
                                                        $price += $md->price * $md->qty;
                                                    }
                                                }
                                                $gross += $price * $mn->qty;
                                            }
                                        }
                                        foreach ($sales_tax as $st) {
                                            if($st->sales_id == $val->sales_id)
                                               $vat += round($st->amount,2);
                                        }
                                        foreach ($sales_no_tax as $snt) {
                                            if($snt->sales_id == $val->sales_id)
                                                 $nonvat += round($snt->amount,2);
                                        }
                                        foreach ($sales_discs as $sd) {
                                            if($sd->sales_id == $val->sales_id){
                                                $discount += $sd->amount;
                                                if($sd->no_tax == 1){
                                                    $non_tax_disc += round($sd->amount,2);
                                                }
                                            }
                                        }
                                        foreach ($sales_charges as $sc) {
                                            if($sc->sales_id == $val->sales_id)
                                                    $charges += round($sc->amount,2);
                                        }
                                        $trans_cnt += 1;                                           
                                    }
                                }    
                            }
                            foreach ($trans['sales']['void']['orders'] as $val) {
                                $time = DateTime::createFromFormat('Y-m-d H:i:s',$val->datetime);
                                $start = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['start'].":00");
                                $end = DateTime::createFromFormat('Y-m-d H:i:s',date2Sql($val->datetime)." ".$range['end'].":00");
                                if($time >= $start && $time <= $end){
                                    $cancel_cnt += 1;
                                    $cancel_amt += $val->total_amount;
                                }    
                            }
                            #########################################################################################################
                            if($trans_cnt > 0){
                                $non_vat = $nonvat - $non_tax_disc;
                                $less_vat = (($gross+$charges) - $discount) - $net;
                                if($less_vat < 0)
                                    $less_vat = 0;
                                $hour_sales[] = array(
                                    "tenant_code"=> $tenant_code,
                                    "pos_no"    => TERMINAL_NUMBER,
                                    "sales_date"=> $sales_date,
                                    "sales_time"=> $sales_hour,
                                    "hour_code" => $code,
                                    "gross"     => numInt($gross),
                                    "net"       => numInt($net),
                                    "vat"       => numInt($vat),
                                    "non_vat"   => numInt($non_vat),
                                    "less_vat"  => numInt($less_vat),
                                    "discount"  => numInt($discount),
                                    "charges"   => numInt($charges),
                                    "trans_cnt" => $trans_cnt,                
                                    "void"      => numInt($cancel_amt),
                                    "no_cancel" => $cancel_cnt,
                                    "guest"     => $guest,
                                );
                            }
                        }
                        // echo 'sss'.$sales_date.'sss'; die();
                         $str .= "01".$tenant_code.$dlmtr;
                        $str .= "02".TERMINAL_ID.$dlmtr;
                        $str .= "03".date('mdY',strtotime($sales_date)).$dlmtr;
                        $total_net = 0;
                        $total_trans_cnt = 0;
                        $total_guest = 0;
                        foreach ($hour_sales as $row) {
                            $str .= "04".$row['hour_code'].$dlmtr;
                            $str .= "05".numNoDot($row['net']).$dlmtr;
                            $str .= "06".$row['trans_cnt'].$dlmtr;
                            $str .= "07".$row['guest'].$dlmtr;
                            $total_net += $row['net'];
                            $total_trans_cnt += $row['trans_cnt'];
                            $total_guest += $row['guest'];
                        }
                        $str .= "08".numNoDot($total_net).$dlmtr;
                        $str .= "09".$total_trans_cnt.$dlmtr;
                        $str .= "10".$total_guest;
                    }else{
                        foreach ($hour_code as $code => $range) {
                            $sales_date = sql2Date(date('Y-m-d',strtotime($zread['read_date'])));
                            // echo $sales_date.'in';
                            // $sales_hour = date('H:00',strtotime($val->datetime));
                            $sales_hour = date('H:00');
                            $hour_sales[] = array(
                                    "tenant_code"=> $tenant_code,
                                    "pos_no"    => TERMINAL_NUMBER,
                                    "sales_date"=> $sales_date,
                                    "sales_time"=> $sales_hour,
                                    "hour_code" => $code,
                                    "gross"     => numInt(0),
                                    "net"       => numInt(0),
                                    "vat"       => numInt(0),
                                    "non_vat"   => numInt(0),
                                    "less_vat"  => numInt(0),
                                    "discount"  => numInt(0),
                                    "charges"   => numInt(0),
                                    "trans_cnt" => 0,                
                                    "void"      => numInt(0),
                                    "no_cancel" => 0,
                                    "guest"     => 0,
                                );
                        }

                        $str .= "01".$tenant_code.$dlmtr;
                        $str .= "02".TERMINAL_ID.$dlmtr;
                        $str .= "03".date('mdY',strtotime($sales_date)).$dlmtr;
                        $total_net = 0;
                        $total_trans_cnt = 0;
                        $total_guest = 0;
                        foreach ($hour_sales as $row) {
                            $str .= "04".$row['hour_code'].$dlmtr;
                            $str .= "05".numNoDot($row['net']).$dlmtr;
                            $str .= "06".$row['trans_cnt'].$dlmtr;
                            $str .= "07".$row['guest'].$dlmtr;
                            $total_net += $row['net'];
                            $total_trans_cnt += $row['trans_cnt'];
                            $total_guest += $row['guest'];
                        }
                        $str .= "08".numNoDot($total_net).$dlmtr;
                        $str .= "09".$total_trans_cnt.$dlmtr;
                        $str .= "10".$total_guest;
                    }
                    $hrlyfile = "H".substr($tenant_code,0,4).TERMINAL_NUMBER.$eod_ctr.".".$mon[date('m',strtotime($zread['read_date']))].date('d',strtotime($zread['read_date']));
                    $this->write_file($file_path.$hrlyfile,$str);
                ###################################################################################################################################
                ### DISCOUNT FILE
                    $str = "";
                    $sdiscs = array();
                    foreach ($sales_discs as $res) {
                        if(!isset($sdiscs[$res->disc_code])){
                            $sdiscs[$res->disc_code] = array('code'=>$res->disc_code,'desc'=>$res->disc_desc,'amount'=>round($res->amount,2));
                        }
                        else{
                            $disc = $sdiscs[$res->disc_code];
                            $disc['amount'] += round($res->amount,2);
                            $sdiscs[$res->disc_code] = $disc;

                        }
                   }
                    // var_dump($sdiscs); die();   
                    $cc = count($sdiscs);
                    $counters = 1;
                    foreach ($sdiscs as $code => $dsc) {
                        if($counters == $cc){
                            $str .= substr($code,0,6).",".substr($dsc['desc'],0,25).",".numInt($dsc['amount']);
                        }else{
                            $str .= substr($code,0,6).",".substr($dsc['desc'],0,25).",".numInt($dsc['amount']).$dlmtr;
                        }
                        $counters++;
                    }
                    $discfile = "D".substr($tenant_code,0,4).TERMINAL_NUMBER.$eod_ctr.".".$mon[date('m',strtotime($zread['read_date']))].date('d',strtotime($zread['read_date']));
                    $this->write_file($file_path.$discfile,$str);
                ###################################################################################################################################
                ### DAILY FILE
                    $old_gt = $eod['old_grand_total'];
                    $total_net = 0;
                    $total_gross = 0;
                    $total_nontax = 0;
                    $total_vat = 0;
                    $total_charges = 0;
                    $total_void = 0;
                    $total_guest = 0;
                    $total_trans_cnt = 0;
                    foreach ($hour_sales as $row) {
                        $total_net += $row['net'];
                        $total_gross += $row['gross'];
                        $total_nontax += $row['non_vat'];
                        $total_vat += $row['vat'];
                        $total_charges += $row['charges'];
                        $total_void += $row['void'];
                        $total_guest += $row['guest'];
                        $total_trans_cnt += $row['trans_cnt'];
                    }    
                    $total_senior = 0;
                    $total_pwd = 0;
                    if(isset($sdiscs['SNDISC']))
                        $total_senior = $sdiscs['SNDISC']['amount'];
                    if(isset($sdiscs['PWDISC']))
                        $total_pwd = $sdiscs['PWDISC']['amount'];
                    $total_other_disc = 0;
                    foreach ($sdiscs as $code => $dsc) {
                        if($code != 'SNDISC' && $code != 'PWDISC')
                            $total_other_disc += $dsc['amount'];
                    }
                    $cash_sales = 0;
                    $charge_sales = 0;
                    $gc_sales = 0;
                    foreach ($sales_payments as $pay) {
                        if($pay->amount > $pay->to_pay)
                            $amount = $pay->to_pay;
                        else
                            $amount = $pay->amount;
                        if($pay->payment_type == 'cash'){
                            $cash_sales += $amount;
                        }
                        else if($pay->payment_type == 'gc'){
                            if($pay->amount > $pay->to_pay){
                                $gc_sales += $pay->amount;
                            }else{
                                $gc_sales += $pay->amount;  
                            }
                        }
                        else{
                            $charge_sales += $amount;
                        }
                    }
                    $tgross = $total_net + $total_senior + $total_pwd + $total_other_disc;
                    $str = "01".$tenant_code.$dlmtr;
                    $str .= "02".TERMINAL_ID.$dlmtr;
                    $str .= "03".date('mdY',strtotime($zread['read_date'])).$dlmtr;
                    $str .= "04".numNoDot(numInt($old_gt)).$dlmtr;
                    $str .= "05".numNoDot(numInt($old_gt+$total_net)).$dlmtr;
                    $str .= "06".numNoDot(numInt($tgross)).$dlmtr;
                    $str .= "07".numNoDot(numInt($total_nontax)).$dlmtr;
                    $str .= "08".numNoDot(numInt($total_senior + $total_pwd)).$dlmtr;
                    $str .= "09".numNoDot(numInt($total_other_disc)).$dlmtr;
                    $str .= "10000".$dlmtr;
                    $str .= "11".numNoDot(numInt($total_vat)).$dlmtr;
                    $str .= "12".numNoDot(numInt($total_charges)).$dlmtr;
                    $str .= "13".numNoDot(numInt($total_net)).$dlmtr;
                    $str .= "14".numNoDot(numInt($cash_sales)).$dlmtr;
                    $str .= "15".numNoDot(numInt($charge_sales)).$dlmtr;
                    $str .= "16".numNoDot(numInt($gc_sales)).$dlmtr;
                    $str .= "17".numNoDot(numInt($total_void)).$dlmtr;
                    $str .= "18".$total_guest.$dlmtr;
                    $str .= "19".$eod['ctr'].$dlmtr;
                    $str .= "20".$total_trans_cnt.$dlmtr;
                    $str .= "21".$sales_type.$dlmtr;
                    $str .= "22".numNoDot($total_net);
                    $dailyfile = "S".substr($tenant_code,0,4).TERMINAL_NUMBER.$eod_ctr.".".$mon[date('m',strtotime($zread['read_date']))].date('d',strtotime($zread['read_date']));
                    $this->write_file($file_path.$dailyfile,$str);
                ###################################################################################################################################
                // update for main
                $this->site_model->db = $this->load->database('main', TRUE);
                $items = array(
                    'ctr'=>$eod_ctr
                );
                $this->site_model->update_tbl('read_details','read_id',$items,$zread['id']);

                // update for dine
                $this->site_model->db = $this->load->database('default', TRUE);
                $items = array(
                    'ctr'=>$eod_ctr
                );
                $this->site_model->update_tbl('read_details','id',$items,$zread['id']);

                if($regen){
                    $error = 0;
                    $msg = "MEGAWORLD File successfully created.";
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert("MEGAWORLD File successfully created.","success");
                }

                
            }
            else{
                $error = 1;
                $msg = "No Zread Found.";
                if($regen){
                    return array('error'=>$error,'msg'=>$msg);
                }
                else{
                    site_alert($msg,"error");
                }
            }    
        }

    ##################
    ### SCRIPT FIX
    ##################
        public function script_delete(){
           $this->load->model('core/trans_model');
           $this->db = $this->load->database('main', TRUE);
           $result = $this->site_model->get_tbl('trans_sales_payments',array('payment_type'=>'chit'));
           
           $ids = array();
           foreach ($result as $res) {
               if(!in_array($res->sales_id, $ids)){
                    $ids[] = $res->sales_id;
               }
           }
           $args['trans_sales.sales_id'] = $ids;
           $sales = $this->trans_model->get_trans_sales(null,$args,'ASC');
           $query = "";
           $in_str = "IN(";
           foreach ($sales as $res) {
              $in_str .= $res->sales_id.",";
           }
           $in_str = substr($in_str, 0,-1).")";
           $tbls = array(
                'trans_sales',
                'trans_sales_charges',
                'trans_sales_items',
                'trans_sales_discounts',
                'trans_sales_menu_modifiers',
                'trans_sales_menus',
                'trans_sales_no_tax',
                'trans_sales_payments',
                'trans_sales_tax',
                'trans_sales_zero_rated',
                'trans_sales_local_tax',
                'reasons',
           );
           foreach ($tbls as $txt) {
                if($txt == "reasons")
                   $query .= "DELETE FROM ".$txt." WHERE trans_id ".$in_str.";\r\n";
                else
                   $query .= "DELETE FROM ".$txt." WHERE sales_id ".$in_str.";\r\n";
           }
           echo "<pre>".$query."</pre>"; 
           $filename = "remove_chits.sql";
           $fp = fopen($filename, "w+");
           fwrite($fp,$query);
           fclose($fp);
           echo "file created"; 
        }  
        public function script_reref(){
           $this->load->model('core/trans_model');
           $start_ref = "00000001";
           // $start_ref = $this->next_ref(10,"00000009");
           $this->db = $this->load->database('main', TRUE);
           $args['trans_sales.type_id'] = SALES_TRANS;
           $args['trans_sales.inactive'] = 0;
           $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
           $sales = $this->trans_model->get_trans_sales(null,$args,'ASC');
           $query = "";
           $ctr = 1;
           foreach ($sales as $res) {
                // $query .= $res->sales_id;
                if($ctr == 1){
                    $next = $start_ref;
                }
                else{
                    $next = $this->next_ref(10,$next);
                }
                $update_trans_ref = $this->trans_model->update_trans_ref_string(array('trans_ref'=>$next),$res->sales_id);    
                $query .= $update_trans_ref.";\r\n";
                $ctr++;
           }
           $next = $this->next_ref(10,$next);
           $query .= "UPDATE trans_types SET next_ref = '".$next."' where type_id=10;";
           echo "<pre>".$query."</pre>"; 
           $filename = "reref.sql";
           $fp = fopen($filename, "w+");
           fwrite($fp,$query);
           fclose($fp);
           echo "file created";
        } 
        public function insert_reref(){
            $this->load->model('core/trans_model');
            $args = array();
            

            // $user_id=36;
            // $shift_id=18;
            // $args['trans_sales.shift_id'] = 1;
            // $id_ctr=424;
            // $start_ref = "00000423";

            $user_id=39;
            $shift_id=19;
            $args['trans_sales.shift_id'] = 2;
            $id_ctr=453;
            $start_ref = "00000452";
            

            $result = $this->cashier_model->get_just_trans_sales(
                null,
                $args,
                'asc'
            );
            
            $orders = $result->result();
            $cols = $result->list_fields();
            $sales = array();
            $sales_ids = array();
            $s_id = array();
            foreach ($orders as $ord) {
                $row = array();
                $id_ctr += 1;
                $s_id[$ord->sales_id] = $id_ctr; 
                $sales_ids[] = $ord->sales_id;
                foreach ($cols as $col) {
                    if($col != "id"){
                        $row[$col] = $ord->$col;
                    }
                }
                // $row['pos_id'] = TERMINAL_ID;
                $sales[] = $row;
            }
            
            $ctr = 1;

            foreach ($sales as $key => $row) {
                
                if($ctr == 1){
                    $next = $start_ref;
                }
                else{
                    $next = $this->next_ref(10,$next);
                }

                $row['sales_id'] = $s_id[$row['sales_id']];
                $row['trans_ref'] = $next;
                if($user_id == 36){
                    $row['datetime'] = str_replace('2016-04-23 13', '2016-04-23 10', $row['datetime']);
                    $row['update_date'] = str_replace('2016-04-23 13','2016-04-23 10',$row['update_date']);                    
                }
                else{                    
                    $row['datetime'] = str_replace('2016-04-23 14', '2016-04-23 16', $row['datetime']);
                    $row['update_date'] = str_replace('2016-04-23 14','2016-04-23 16',$row['update_date']);                    
                }
                $row['shift_id'] = $shift_id;
                $row['user_id'] = $user_id;
                $sales[$key] = $row;
                $ctr++;
            }
            $query = "";
            $ctr = 1;
            $total = 0;

            foreach ($sales as $row) {
                $write_string = $this->db->insert_string('trans_sales',$row);
                if($ctr != 1){
                    $query .= str_replace('INSERT INTO `trans_sales` (`sales_id`, `mobile_sales_id`, `type_id`, `trans_ref`, `void_ref`, `type`, `user_id`, `shift_id`, `terminal_id`, `customer_id`, `total_amount`, `total_paid`, `memo`, `table_id`, `guest`, `datetime`, `update_date`, `paid`, `reason`, `void_user_id`, `printed`, `inactive`, `waiter_id`, `split`) VALUES',"", $write_string);
                }
                else{
                    $query .= $write_string;
                }
                $query .= ",\r\n";
                $ctr++;
                $total += $row['total_amount'];
            }
            $query = substr($query, 0,-3);
            $query .= ";\r\n";
            $tbl['trans_sales_charges'] = $this->cashier_model->get_trans_sales_charges(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_items'] = $this->cashier_model->get_trans_sales_items(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_discounts'] = $this->cashier_model->get_trans_sales_discounts(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_menu_modifiers'] = $this->cashier_model->get_trans_sales_menu_modifiers(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_menus'] = $this->cashier_model->get_trans_sales_menus(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_no_tax'] = $this->cashier_model->get_trans_sales_no_tax(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_payments'] = $this->cashier_model->get_trans_sales_payments(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_tax'] = $this->cashier_model->get_trans_sales_tax(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_zero_rated'] = $this->cashier_model->get_trans_sales_zero_rated(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_local_tax'] = $this->cashier_model->get_trans_sales_local_tax(null,array('sales_id'=>$sales_ids));
            $tbl['reasons'] = $this->cashier_model->get_just_reasons($sales_ids);
            $details = array();
            foreach ($tbl as $table => $row) {
                $cl = $this->site_model->get_tbl_cols($table);
                unset($cl[0]);
                $dets = array();
                foreach ($row as $r) {
                    $det = array();
                    foreach ($cl as $c) {
                        if($c != "id"){
                            if($table == 'trans_sales_payments'){
                                if($c == 'datetime'){
                                    if($user_id == 36){
                                        $det[$c] = str_replace('2016-04-23 13','2016-04-23 10',$r->datetime);
                                    }
                                    else{
                                       $det[$c] = str_replace('2016-04-23 14','2016-04-23 16',$r->datetime); 
                                    }
                                }
                                else{
                                    $det[$c] = $r->$c;
                                }
                            }
                            else{
                                $det[$c] = $r->$c;
                            }
                        }
                    }
                    // $det['pos_id'] = TERMINAL_ID;
                    $dets[] = $det;
                }####
                $details[$table]=$dets;
            }
            foreach ($details as $tbl => $det) {
                foreach ($det as $id => $row) {
                    if($tbl == "reasons")
                        $row['trans_id'] = $s_id[$row['trans_id']];
                    else
                        $row['sales_id'] = $s_id[$row['sales_id']];
                    $det[$id] = $row;
                }
                $details[$tbl] = $det;
            }
            foreach ($details as $tbl => $det) {
                $query .= "\r\n";
                foreach ($det as $id => $items) {
                    $write_string = $this->db->insert_string($tbl,$items);
                    $query .= $write_string;
                    $query .= ";\r\n";
                }
            }    

            $query .= "\r\n";
            $query .= "\r\n";


            // $args['trans_sales.shift_id'] = 1;

            // $this->db = $this->load->database('main', TRUE);
            // $result = $this->site_model->get_tbl('trans_sales_payments',array('payment_type'=>'credit'));
            
            // $ids = array();
            // foreach ($result as $res) {
            //     if(!in_array($res->sales_id, $ids)){
            //          $ids[] = $res->sales_id;
            //     }
            // }

            // $args['trans_sales.datetime >='] = '2015-07-17 18:00:00';
            // $args['trans_sales.datetime <='] = '2015-07-18 05:00:00';
            // $args['trans_sales.sales_id'] = $ids;


            // if($user_id == 36){
            //     $query .= "INSERT INTO `shifts` (`shift_id`, `user_id`, `check_in`, `check_out`, `xread_id`, `cashout_id`, `terminal_id`) VALUES ('".$shift_id."', '".$user_id."', '2016-04-23 10:00:00', '2016-04-23 17:00:00', NULL, NULL, '1');";
            //     $query .= "\r\n";
            //     $query .= "INSERT INTO `shift_entries` (`shift_id`, `amount`, `user_id`, `trans_date`) VALUES ('".$shift_id."', '5000', '".$user_id."', '2016-04-23 10:00:00');";
            //     $query .= "\r\n";
            //     $query .= "\r\n";
            //     $query .= "INSERT INTO `read_details` (`id`, `read_type`, `read_date`, `user_id`, `old_total`, `grand_total`, `reg_date`, `scope_from`, `scope_to`) VALUES ('1', '1', '2016-04-23', '".$user_id."', NULL, NULL, '2016-04-23 10:00:00', '2016-04-23 17:00:00', '2016-04-23 17:00:00');";
            // }
            // else{
            //     $query .= "INSERT INTO `shifts` (`shift_id`, `user_id`, `check_in`, `check_out`, `xread_id`, `cashout_id`, `terminal_id`) VALUES ('".$shift_id."', '".$user_id."', '2016-04-23 17:00:01', '2016-04-23 23:00:00', NULL, NULL, '1');";
            //     $query .= "\r\n";
            //     $query .= "INSERT INTO `shift_entries` (`shift_id`, `amount`, `user_id`, `trans_date`) VALUES ('".$shift_id."', '5000', '".$user_id."', '2016-04-23 17:00:01');";
            //     $query .= "\r\n";
            //     $query .= "\r\n";
            //     $query .= "INSERT INTO `read_details` (`id`, `read_type`, `read_date`, `user_id`, `old_total`, `grand_total`, `reg_date`, `scope_from`, `scope_to`) VALUES ('2', '1', '2016-04-23', '".$user_id."', NULL, NULL, '2016-04-23 23:00:00', '2016-04-23 17:00:01', '2016-04-23 23:00:00');";

            
            //     $query .= "\r\n";
            //     $query .= "\r\n";
            //     $query .= "### ZREAD";
            //     $query .= "\r\n";
            //     $query .= "INSERT INTO `read_details` (`id`, `read_type`, `read_date`, `user_id`, `old_total`, `grand_total`, `reg_date`, `scope_from`, `scope_to`) VALUES ('3', '2', '2016-04-23', '".$user_id."', '0', '0', '2016-04-23 10:00:00', '2016-04-23 10:00:00', '2016-04-23 23:00:00');";
            // }

            echo "<pre>".$query."</pre>"; 
        }    
        public function script_update(){
           $this->load->model('core/trans_model');
           $sales = $this->trans_model->get_trans_sales(null,array("trans_ref"=>"00000009"),'ASC');
           // echo var_dump($sales);
           // $ctr = 1;
           // foreach ($sales as $res) {
           //      if($ctr > 1){
           //          if($ctr <= 800){
           //              if($ctr == 2){
           //                  $next_ref = '000007217';
           //                  $refs=$this->trans_model->write_ref(10,$next_ref,$res->user_id);
           //                  $this->trans_model->update_next_ref(10,$refs['ref']);       
           //                  $this->cashier_model->update_trans_sales(array('trans_ref'=>$next_ref),$res->sales_id);     
           //              }
           //              else{
           //                  $next_ref = $this->trans_model->get_next_ref();
           //                  $refs=$this->trans_model->write_ref(10,$next_ref,$res->user_id);
           //                  $this->trans_model->update_next_ref(10,$refs['ref']);  
           //                  $this->cashier_model->update_trans_sales(array('trans_ref'=>$next_ref),$res->sales_id);     
           //              } 

           //          }
           //      }    
           //      $ctr++;
           // }
           // echo "done";
           $ctr = 1;
           $next = "";
           $print_str = "";
           $write_str = "";
           $update_str = "";
           foreach ($sales as $res) {
                if($ctr > 1){
                    // if($ctr <= 800){
                        if($ctr == 2){
                            $next = $this->next_ref(10,'00000009');
                            $write_string = $this->trans_model->write_ref_string(10,$next,$res->user_id);
                            $write_str .= $write_string;
                            // $update_string = $this->trans_model->update_next_ref_string(10,$next);    
                            $update_trans_ref = $this->trans_model->update_trans_ref_string(array('trans_ref'=>$next),$res->sales_id);     
                        }
                        else{
                            $next = $this->next_ref(10,$next);
                            $write_string=$this->trans_model->write_ref_string(10,$next,$res->user_id);
                            $write_str .= str_replace('INSERT INTO `trans_refs` (`type_id`, `trans_ref`, `user_id`) VALUES',"", $write_string);
                            // $update_string = $this->trans_model->update_next_ref_string(10,$next);  
                            $update_trans_ref = $this->trans_model->update_trans_ref_string(array('trans_ref'=>$next),$res->sales_id);     
                        } 
                         $write_str .= ",\r\n";
                         $update_str .= $update_trans_ref.";\r\n";
                        // $print_str .= $update_trans_ref.";\r\n\r\n";
                    }
                // }    
                $ctr++;
           }
           $print_str .= substr($write_str,0,-3).";\r\n";
           $print_str .= $update_str."\r\n";
           echo '<pre>'.$print_str.'</pre>';

            $filename = "sql_fix.sql";
            $fp = fopen($filename, "w+");
            fwrite($fp,$print_str);
            fclose($fp);
            echo "file created";
        }
        public function get_next_ref(){
            $this->load->model('core/trans_model');
            $sales = $this->trans_model->get_trans_sales(null,array("trans_ref"=>"00000009"),'ASC');
            $total = count($sales);
            
            $next = "";
            for($i=1;$i<=$total;$i++){
                if($i == 1)
                    $next = $this->next_ref(10,'00000009');
                else{
                    $next = $this->next_ref(10,$next);
                }
                // echo $next."\r\n";
            }
            echo $next;
        }
        public function next_ref($trans_type,$ref){
            if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1) 
            {
                list($all, $prefix, $number, $postfix) = $result;
                $dig_count = strlen($number); // How many digits? eg. 0003 = 4
                $fmt = '%0' . $dig_count . 'd'; // Make a format string - leading zeroes
                $nextval =  sprintf($fmt, intval($number + 1)); // Add one on, and put prefix back on

                $new_ref=$prefix.$nextval.$postfix;
            }
            else 
                $new_ref=$ref;
            return $new_ref;
        }
        public function last_id_getter(){
            $this->db = $this->load->database('default', TRUE);
            $select = "max(sales_id) as last";
            $result = $this->site_model->get_tbl('trans_sales',array(),array(),null,true,$select);
            $nxt_id = $result[0]->last;
            echo "LAST SALES ID = ".$nxt_id."<br>";

            $select = "max(shift_id) as last_shift_id";
            $last_shift = $this->site_model->get_tbl('shifts',array(),array(),null,true,$select);
            $nxt_shift_id = $last_shift[0]->last_shift_id;
            echo "LAST SHIFT ID = ".$nxt_shift_id."<br>";

            $select = "max(entry_id) as last";
            $result = $this->site_model->get_tbl('shift_entries',array(),array(),null,true,$select);
            $nxt_id = $result[0]->last;
            echo "LAST READ SHIFT ENTRY ID = ".$nxt_id."<br>";


            $select = "max(cashout_id) as last";
            $result = $this->site_model->get_tbl('cashout_entries',array(),array(),null,true,$select);
            $nxt_id = $result[0]->last;
            echo "LAST CASHOUT ID = ".$nxt_id."<br>";
            $select = "max(id) as last";
            $result = $this->site_model->get_tbl('cashout_details',array(),array(),null,true,$select);
            $nxt_id = $result[0]->last;
            echo "LAST CASHOUT DETAILS = ".$nxt_id."<br>";
            
            $select = "max(id) as last";
            $result = $this->site_model->get_tbl('read_details',array(),array(),null,true,$select);
            $nxt_id = $result[0]->last;
            echo "LAST READ DETAILS = ".$nxt_id."<br>";
        }    
        public function sale_fixer(){
            $this->load->model('core/trans_model');
            $this->load->model('dine/clock_model');
            $query = "";

            //ALTER TABLE tbl AUTO_INCREMENT=310;
            // $query .= "DELETE FROM trans_sales where DATE(`datetime`) = DATE('2016-04-22');\r\n";

            ####################################################
            # USER 36
                // $des_shift_id = 1;
                // $shift_user = 36;
                // $shift_in_datetime = '2016-04-22 12:54:54';
                // $shift_out_datetime = '2016-04-22 15:38:04';

                // $start_ref = "0000001";
                // $id_ctr = 1206;    
                // $nxt_shift_id = 49 + 1;
                // $nxt_entry_id = 49 + 1;
                // $nxt_cashout_id = 48;
                // $nxt_cashout_detail_id = 485 + 1;
                // $nxt_read_id = 81 + 1;
            
            ####################################################
            # USER 37
                // $des_shift_id = 2;
                // $shift_user = 37;
                // $shift_in_datetime = '2016-04-22 16:53:20';
                // $shift_out_datetime = '2016-04-22 21:05:44';

                // $start_ref = "0000001";
                // $id_ctr = 1217;    
                // $nxt_shift_id = 50 + 1;
                // $nxt_entry_id = 50 + 1;
                // $nxt_cashout_id = 49;
                // $nxt_cashout_detail_id = 530 + 1;
                // $nxt_read_id = 82 + 1;
            ####################################################
            # USER 37
                $des_shift_id = 3;
                $shift_user = 39;
                $shift_in_datetime = '2016-04-22 15:46:35';
                $shift_out_datetime = '2016-04-22 22:06:52';

                $start_ref = "0000001";
                $id_ctr = 1228;    
                $nxt_shift_id = 51 + 1;
                $nxt_entry_id = 51 + 1;
                $nxt_cashout_id = 50;
                $nxt_cashout_detail_id = 579 + 1;
                $nxt_read_id = 83 + 1;
            ####################################################
            $nxct = $nxt_cashout_id+1;
            $des_shift_date = '2016-04-22';
            $items = array(
                'shift_id'=>$nxt_shift_id,
                'user_id'=>$shift_user,
                'check_in'=>$shift_in_datetime,
                'check_out'=>$shift_out_datetime,
                'xread_id'=>$nxt_read_id,
                'cashout_id'=>$nxct,
                'terminal_id'=>TERMINAL_ID,
                // 'pos_id'=>TERMINAL_ID,
            );
            $query .= $this->db->insert_string('shifts',$items).";\r\n";
            $items = array(
                'entry_id'=>$nxt_entry_id,
                'shift_id'=>$nxt_shift_id,
                'amount'=>10000,
                'user_id'=>$shift_user,
                'trans_date'=>$shift_in_datetime,
                // 'pos_id'=>TERMINAL_ID,
            );
            $query .= $this->db->insert_string('shift_entries',$items).";\r\n";

            $this->db = $this->load->database('main', TRUE);
            $args['shift_id'] = $des_shift_id; 
            $args["DATE(trans_sales.datetime) = DATE('".date2Sql($des_shift_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            
            $result = $this->cashier_model->get_just_trans_sales(null,$args,'asc');
            $orders = $result->result();
            $cols = $result->list_fields();
            
            $sales_ids = array();
            $s_id = array();
            foreach ($orders as $ord) {
                $row = array();
                $id_ctr += 1;
                $s_id[$ord->sales_id] = $id_ctr; 
                $sales_ids[] = $ord->sales_id;
                foreach ($cols as $col) {
                    if($col != "id" && $col != "pos_id"){
                        $row[$col] = $ord->$col;
                    }
                }
                $sales[] = $row;
            }
            $next_sales_id = $id_ctr+1;
            $ctr=0;
            $total = 0;
            foreach ($sales as $key => $row) {
                if($ctr == 0){
                    $next = $start_ref;
                }
                else{
                    $next = $this->next_ref(10,$next);
                }
                $row['sales_id'] = $s_id[$row['sales_id']];
                $row['trans_ref'] = $next;
                $row['shift_id'] = $nxt_shift_id;
                $row['user_id'] = $shift_user;
                $sales[$key] = $row;
                $ctr++;
                if($row['inactive'] == 0)
                    $total += $row['total_amount'];
            }
            foreach ($sales as $row) {
                $query .= $this->db->insert_string('trans_sales',$row).";\r\n";
            }
            $query .= "\r\n#".($ctr)."--".$total."\r\n";

            $targs["DATE(trans_sales.datetime) = DATE('".date2Sql($des_shift_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            $targs['trans_sales.sales_id'] = $sales_ids;
            $targs['trans_sales.shift_id'] = $des_shift_id;

            $join['trans_sales'] = array('content'=>'trans_sales_charges.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_charges'] = $this->site_model->get_tbl('trans_sales_charges',$targs,array(),$join);
            $query .= "#trans_sales_charges".count($tbl['trans_sales_charges'])."\r\n";

            $join['trans_sales'] = array('content'=>'trans_sales_payments.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_payments'] = $this->site_model->get_tbl('trans_sales_payments',$targs,array(),$join);
            $query .= "#trans_sales_payments".count($tbl['trans_sales_payments'])."\r\n";
            $payments = $tbl['trans_sales_payments'];

            $join['trans_sales'] = array('content'=>'trans_sales_items.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_items'] = $this->site_model->get_tbl('trans_sales_items',$targs,array(),$join);
            $query .= "#trans_sales_items".count($tbl['trans_sales_items'])."\r\n";

            $join['trans_sales'] = array('content'=>'trans_sales_discounts.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_discounts'] = $this->site_model->get_tbl('trans_sales_discounts',$targs,array(),$join);
            $query .= "#trans_sales_discounts".count($tbl['trans_sales_discounts'])."\r\n";

            $join['trans_sales'] = array('content'=>'trans_sales_menu_modifiers.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_menu_modifiers'] = $this->site_model->get_tbl('trans_sales_menu_modifiers',$targs,array(),$join);
            $query .= "#trans_sales_menu_modifiers".count($tbl['trans_sales_menu_modifiers'])."\r\n";

            $join['trans_sales'] = array('content'=>'trans_sales_menus.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_menus'] = $this->site_model->get_tbl('trans_sales_menus',$targs,array(),$join);
            $query .= "#trans_sales_menus".count($tbl['trans_sales_menus'])."\r\n";

            $join['trans_sales'] = array('content'=>'trans_sales_no_tax.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_no_tax'] = $this->site_model->get_tbl('trans_sales_no_tax',$targs,array(),$join);
            $query .= "#trans_sales_no_tax".count($tbl['trans_sales_no_tax'])."\r\n";
            // echo $this->site_model->db->last_query()."<br><br>";

            $join['trans_sales'] = array('content'=>'trans_sales_tax.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_tax'] = $this->site_model->get_tbl('trans_sales_tax',$targs,array(),$join);
            $query .= "#trans_sales_tax".count($tbl['trans_sales_tax'])."\r\n";

            $join['trans_sales'] = array('content'=>'trans_sales_zero_rated.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_zero_rated'] = $this->site_model->get_tbl('trans_sales_zero_rated',$targs,array(),$join);
            $query .= "#trans_sales_zero_rated".count($tbl['trans_sales_zero_rated'])."\r\n";

            $join['trans_sales'] = array('content'=>'trans_sales_local_tax.sales_id = trans_sales.sales_id');
            $tbl['trans_sales_local_tax'] = $this->site_model->get_tbl('trans_sales_local_tax',$targs,array(),$join);
            $query .= "#trans_sales_local_tax".count($tbl['trans_sales_local_tax'])."\r\n";

            $join['trans_sales'] = array('content'=>'reasons.trans_id = trans_sales.sales_id');
            $tbl['reasons'] = $this->site_model->get_tbl('reasons',$targs,array(),$join);
            $query .= "#reasons".count($tbl['reasons'])."\r\n";

            $details = array();
            foreach ($tbl as $table => $row) {
                $cl = $this->site_model->get_tbl_cols($table);
                unset($cl[0]);
                $dets = array();
                foreach ($row as $r) {
                    $det = array();
                    foreach ($cl as $c) {
                        if($c != "id" && $c != "pos_id"){
                            $det[$c] = $r->$c;
                        }
                    }
                    // $det['pos_id'] = TERMINAL_ID;
                    $dets[] = $det;
                }####
                $details[$table]=$dets;
            }
            foreach ($details as $tbl => $det) {
                foreach ($det as $id => $row) {
                    if($tbl == "reasons")
                        $row['trans_id'] = $s_id[$row['trans_id']];
                    else
                        $row['sales_id'] = $s_id[$row['sales_id']];
                    $det[$id] = $row;
                }
                $details[$tbl] = $det;
            }
            foreach ($details as $tbl => $det) {
                $query .= "\r\n";
                foreach ($det as $id => $items) {
                    $write_string = $this->db->insert_string($tbl,$items);
                    $query .= $write_string;
                    $query .= ";\r\n";
                }
            } 

            $shift = $this->site_model->get_tbl('shifts',array('shift_id'=>$des_shift_id),array(),null,true,'*');
            $xread_id = $shift[0]->xread_id;
            $cashout_id = $shift[0]->cashout_id;

            $cargs['cashout_id'] = $cashout_id;
            $cashout_entries = $this->clock_model->get_cashout_entries(null,$cargs);
            $cashout_entries_cols = $this->site_model->get_tbl_cols('cashout_entries');
            $rows = array();
            foreach ($cashout_entries as $en) {
                $row = array();
                foreach ($cashout_entries_cols as $col) {
                    if($col != "id" && $col != "pos_id"){
                        if($col == 'cashout_id'){
                            $nxt_cashout_id += 1;
                            $row['cashout_id'] = $nxt_cashout_id;
                        }
                        else
                        $row[$col] = $en->$col;
                    }
                }
                // $row['pos_id'] = TERMINAL_ID;
                $rows[] = $row;
            }
            foreach ($rows as $ctr => $row) {
                $write_string = $this->db->insert_string('cashout_entries',$row);
                $query .= $write_string;
                $query .= ";\r\n";
            }
            $cargs['cashout_id'] = $cashout_id;
            $cashout_details = $this->clock_model->get_cashout_details(null,$cargs);
            $cashout_details_cols = $this->site_model->get_tbl_cols('cashout_details');     
            $cashout_details_cols[0] = 'cashout_detail_id';
            $rows = array();
            foreach ($cashout_details as $en) {
                $row = array();
                foreach ($cashout_details_cols as $col) {
                    if($col != "pos_id"){
                        if($col == 'cashout_detail_id'){
                            $nxt_cashout_detail_id += 1;
                            $row[$col] = $nxt_cashout_detail_id;
                        }
                        else
                            $row[$col] = $en->$col;
                    }
                }
                // $row['pos_id'] = TERMINAL_ID;
                $rows[] = $row;
            }
            $next_cashout_did = $nxt_cashout_detail_id+1;
            foreach ($rows as $ctr => $row) {
                $write_string = $this->db->insert_string('cashout_details',$row);
                $query .= "#".$write_string;
                $query .= ";\r\n";
            }

            $rows = array();
            foreach ($cashout_details as $en) {
                $row = array();
                foreach ($cashout_details_cols as $col) {
                    if($col != "pos_id"){
                        if($col == 'cashout_detail_id'){
                            $col = 'id';
                            $nxt_cashout_detail_id += 1;
                            $row[$col] = $nxt_cashout_detail_id;
                        }
                        else
                            $row[$col] = $en->$col;
                    }
                }
                // $row['pos_id'] = TERMINAL_ID;
                $rows[] = $row;
            }
            $next_cashout_did = $nxt_cashout_detail_id+1;
            foreach ($rows as $ctr => $row) {
                $write_string = $this->db->insert_string('cashout_details',$row);
                $query .= $write_string;
                $query .= ";\r\n";
            }



            $rows = array();
            $reads = $this->site_model->get_tbl('read_details',array('read_id'=>$xread_id),array());
            $reads_col = $this->site_model->get_tbl_cols('read_details');
            foreach ($reads as $rd) {
                $row = array();
                foreach ($reads_col as $col) {
                    if($col != "id" && $col != "pos_id"){
                        if($col == 'read_id'){
                            $col = "id";
                            // $nxt_read_id += 1;
                            $row[$col] = $nxt_read_id;
                        }
                        else
                            $row[$col] = $rd->$col;
                    }
                }
                // $row['pos_id'] = TERMINAL_ID;
                $rows[] = $row;
            }
            foreach ($rows as $ctr => $row) {
                $write_string = $this->db->insert_string('read_details',$row);
                $query .= $write_string;
                $query .= ";\r\n";
            }
            // echo "#SALES_ID NEXT ID === ".$next_sales_id."<br>";
            // echo "#CASHOUT_DETAIL_ID NEXT ID === ".$next_cashout_did;
            echo "<pre>".$query."</pre>";
            return false;
        }
        public function recreate(){
            $from_date = '2016-06-13';
            $to_date = '2016-06-21';
            $range = createDateRangeArray($from_date,$to_date);

            $last_ref = '00002600';
            foreach ($range as $date) {
                $sales_ids = array();
                $this->site_model->db = $this->load->database('main', TRUE);
                $this->cashier_model->db = $this->load->database('main', TRUE);
                $result = $this->site_model->get_tbl('trans_sales_payments',
                                                     array("DATE(datetime) = '".$date."'"=>array('use'=>'where','val'=>null,'third'=>false)),
                                                    array('datetime'=>'asc'));
                foreach ($result as $res) {
                    $sales_ids[] = $res->sales_id;
                }
                $tbl['trans_sales_charges'] = $this->cashier_model->get_trans_sales_charges(null,array('sales_id'=>$sales_ids));
                $tbl['trans_sales_items'] = $this->cashier_model->get_trans_sales_items(null,array('sales_id'=>$sales_ids));
                $tbl['trans_sales_discounts'] = $this->cashier_model->get_trans_sales_discounts(null,array('sales_id'=>$sales_ids));
                $tbl['trans_sales_menu_modifiers'] = $this->cashier_model->get_trans_sales_menu_modifiers(null,array('sales_id'=>$sales_ids));
                $tbl['trans_sales_menus'] = $this->cashier_model->get_trans_sales_menus(null,array('sales_id'=>$sales_ids));
                $tbl['trans_sales_no_tax'] = $this->cashier_model->get_trans_sales_no_tax(null,array('sales_id'=>$sales_ids));
                $tbl['trans_sales_payments'] = $this->cashier_model->get_trans_sales_payments(null,array('sales_id'=>$sales_ids));
                $tbl['trans_sales_tax'] = $this->cashier_model->get_trans_sales_tax(null,array('sales_id'=>$sales_ids));
                $tbl['trans_sales_zero_rated'] = $this->cashier_model->get_trans_sales_zero_rated(null,array('sales_id'=>$sales_ids));
                $tbl['trans_sales_local_tax'] = $this->cashier_model->get_trans_sales_local_tax(null,array('sales_id'=>$sales_ids));
                $tbl['reasons'] = $this->cashier_model->get_just_reasons($sales_ids);
                $tbl['shifts'] = $this->site_model->get_tbl('shifts',
                                                            array("DATE(check_in) = '".$date."'"=>array('use'=>'where','val'=>null,'third'=>false))
                                                          );

                $sale = array();
                foreach ($sales_ids as $sid) {
                    $row = array("sales_id" => null,"mobile_sales_id" => null,"type_id" => null,"trans_ref" => null,
                             "void_ref" => null,"type"=>"dinein","user_id" => null,"shift_id" => null,"terminal_id" => 1,
                             "customer_id" => null,"total_amount" => null,"total_paid" => null,"memo" => null,
                             "table_id" => 129,"guest" => 1,"datetime" => null,"update_date" => null,"paid" => 1,
                             "reason" => null,"void_user_id" => null,"printed" => 1,"inactive" => 0,"waiter_id" => null,
                             "split" => 0,"pos_id" => 1);
                    
                    #!!!!
                    $row['sales_id'] = $sid;
                    $row['type_id']  = 10;

                    if (preg_match('/^(\D*?)(\d+)(.*)/', $last_ref, $result) == 1) {
                        list($all, $prefix, $number, $postfix) = $result;
                        $dig_count = strlen($number); // How many digits? eg. 0003 = 4
                        $fmt = '%0' . $dig_count . 'd'; // Make a format string - leading zeroes
                        $nextval =  sprintf($fmt, intval($number + 1)); // Add one on, and put prefix back on
                        $new_ref=$prefix.$nextval.$postfix;
                    }
                    else 
                        $new_ref=$last_ref;
                    #!!!!
                    $row['trans_ref']  = $new_ref;
                    $last_ref = $new_ref;
                    $datetime = null;
                    $user_id = null;
                    foreach ($tbl['trans_sales_payments'] as $tsp) {
                        if($sid == $tsp->sales_id){
                            $datetime = $tsp->datetime;
                            $user_id = $tsp->user_id;
                            break;
                        }
                    }
                    #!!!!
                    $row['user_id'] = $user_id;
                    $shift_id = null;
                    foreach ($tbl['shifts'] as $sh) {
                        if($user_id == $sh->user_id){
                            $pay_datetime = DateTime::createFromFormat('H:i a', toTime($datetime) );
                            $check_in = DateTime::createFromFormat('H:i a', toTime($sh->check_in) );
                            $check_out = DateTime::createFromFormat('H:i a', toTime($sh->check_out) );
                            if ($pay_datetime > $check_in && $pay_datetime < $check_out){
                                $shift_id = $sh->shift_id;
                                break;
                            }
                        }
                    }
                    #!!!
                    $row['shift_id'] = $shift_id;
                    $total = 0;
                    foreach ($tbl['trans_sales_payments'] as $tsp) {
                        $amount = 0;
                        if($sid == $tsp->sales_id){
                            if($tsp->amount > $tsp->to_pay)
                                $amount = $tsp->to_pay;
                            else
                                $amount = $tsp->amount;

                            $total += $amount;
                        }
                    }
                    $row['total_amount'] = $total;
                    $row['total_paid'] = $total;
                    $row['datetime'] = $datetime;
                    $row['update_date'] = $datetime;

                    if(count($sale) > 0){
                        $none = true;
                        foreach ($sale as $s => $srow) {
                            if($srow['sales_id'] == $sid){
                                $none = false;
                            }
                        }
                        if($none)
                            $sale[] = $row;
                    }
                    else
                        $sale[] = $row;
                }
                $query = "";
                foreach ($sale as $row) {
                    $query .= $this->db->insert_string('trans_sales',$row).";\r\n";
                }
                echo "#".$date."\r\n";
                echo "<pre>".$query."</pre>";
            }   
        }
        public function extract_main(){
            $from_date = '2016-06-13';
            $to_date = '2016-06-22';
            $this->site_model->db = $this->load->database('main', TRUE);
            $this->cashier_model->db = $this->load->database('main', TRUE);
            $query = "";
            $range = createDateRangeArray($from_date,$to_date);
            foreach ($range as $date) {
                $query .= "#".$date."\r\n\r\n";     
                $where = array("DATE(check_in) = '".$date."'"=>array('use'=>'where','val'=>null,'third'=>false));
                $shift_res = $this->site_model->get_tbl('shifts',$where,array(),null,false);
                $rows = $shift_res->result();
                $cols = $shift_res->list_fields();
                
                $query .= "#shifts\r\n";     
                $xread_id = array();
                $cashout_id = array();
                $shift_id = array();
                $ctr = 1;
                foreach ($rows as $res) {
                    $row = array();
                    foreach ($cols as $name) {
                        if($name != "id"){
                            $row[$name] = $res->$name;
                        }
                    }
                    $query .= $this->db->insert_string('shifts',$row).";\r\n";
                    $shift_id[] = $res->shift_id;
                    $cashout_id[] = $res->cashout_id;
                    $xread_id[] = $res->xread_id;
                    $ctr += 1;
                }
                $query .= "#shifts - ".$ctr."\r\n\r\n";     

                $query .= "#shift_entries\r\n";
                $ctr = 1;
                $where = array("shift_id"=>$shift_id);
                $shift_entries_res = $this->site_model->get_tbl('shift_entries',$where,array(),null,false);
                $rows = $shift_entries_res->result();
                $cols = $shift_entries_res->list_fields();
                foreach ($rows as $res) {
                    $row = array();
                    foreach ($cols as $name) {
                        if($name != "id"){
                            $row[$name] = $res->$name;
                        }
                    }
                    $query .= $this->db->insert_string('shift_entries',$row).";\r\n";
                    $ctr += 1;
                }
                $query .= "#shift_entries - ".$ctr."\r\n\r\n"; 
                
                $where = array("cashout_id"=>$cashout_id);
                $cash['cashout_entries'] = $this->site_model->get_tbl('cashout_entries',$where,array(),null,false);
                $cash['cashout_details'] = $this->site_model->get_tbl('cashout_details',$where,array(),null,false);
                $where = array("read_id"=>$xread_id);
                $cash['read_details'] = $this->site_model->get_tbl('read_details',$where,array(),null,false);
                foreach ($cash as $tbl_name => $resu) {
                    $rows = $resu->result();
                    $cols = $resu->list_fields();
                    $query .= "#".$tbl_name."\r\n";
                    $ctr = 1;
                    foreach ($rows as $res) {
                        $row = array();
                        foreach ($cols as $name) {
                            if($name != "id"){
                                $row[$name] = $res->$name;
                            }
                        }
                        $query .= $this->db->insert_string($tbl_name,$row).";\r\n";
                        $ctr += 1;
                    }
                    $query .= "#".$tbl_name." - ".$ctr."\r\n\r\n";
                }

                $query .= "#trans_sales\r\n";
                $ctr = 1;
                $where = array("shift_id"=>$shift_id);
                $sales_res = $this->site_model->get_tbl('trans_sales',$where,array(),null,false);
                $rows = $sales_res->result();
                $cols = $sales_res->list_fields();
                $sales_ids = array();   
                foreach ($rows as $res) {
                    $row = array();
                    foreach ($cols as $name) {
                        if($name != "id"){
                            $row[$name] = $res->$name;
                        }
                    }
                    $query .= $this->db->insert_string('trans_sales',$row).";\r\n";
                    $ctr += 1;
                    $sales_ids[] = $res->sales_id;
                }
                $query .= "#trans_sales - ".$ctr."\r\n\r\n";

                // $tbl['trans_sales_charges'] = $this->site_model->get_tbl('trans_sales_charges',array('sales_id'=>$sales_ids),array(),null,false);
                // $tbl['trans_sales_items'] = $this->site_model->get_tbl('trans_sales_items',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_discounts'] = $this->site_model->get_tbl('trans_sales_discounts',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_menu_modifiers'] = $this->site_model->get_tbl('trans_sales_menu_modifiers',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_menus'] = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_no_tax'] = $this->site_model->get_tbl('trans_sales_no_tax',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_payments'] = $this->site_model->get_tbl('trans_sales_payments',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_tax'] = $this->site_model->get_tbl('trans_sales_tax',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_zero_rated'] = $this->site_model->get_tbl('trans_sales_zero_rated',array('sales_id'=>$sales_ids),array(),null,false);
                // $tbl['trans_sales_local_tax'] = $this->site_model->get_tbl('trans_sales_local_tax',array('sales_id'=>$sales_ids),array(),null,false);
                foreach ($tbl as $tbl_name => $resu) {
                    $rows = $resu->result();
                    $cols = $resu->list_fields();
                    $query .= "#".$tbl_name."\r\n";
                    $ctr = 1;
                    foreach ($rows as $res) {
                        $row = array();
                        foreach ($cols as $name) {
                            if($name != "id"){
                                $row[$name] = $res->$name;
                            }
                        }
                        $query .= $this->db->insert_string($tbl_name,$row).";\r\n";
                        $ctr += 1;
                    }
                    $query .= "#".$tbl_name." - ".$ctr."\r\n\r\n";
                }
                $where = array("DATE(read_date) = '".$date."'"=>array('use'=>'where','val'=>null,'third'=>false),'read_type'=>2);
                $z_res = $this->site_model->get_tbl('read_details',$where,array(),null,false);
                $rows = $z_res->result();
                $cols = $z_res->list_fields();
                $query .= "#ZREAD\r\n";
                $ctr = 1;
                foreach ($rows as $res) {
                    $row = array();
                    foreach ($cols as $name) {
                        if($name != "id"){
                            $row[$name] = $res->$name;
                        }
                    }
                    $query .= $this->db->insert_string('read_details',$row).";\r\n";
                    $ctr += 1;
                }
                $query .= "#ZREAD - ".$ctr."\r\n\r\n";
            }
            echo "<pre>".$query."</pre>";
        }
        public function extract($to_main=false){
            $from_date = '2016-08-04';
            $to_date = '2016-08-05';

            $this->site_model->db = $this->load->database('default', TRUE);
            $this->cashier_model->db = $this->load->database('default', TRUE);
            $query = "";
            $sh_string = "";
            $range = createDateRangeArray($from_date,$to_date);
            $use_shift_id = 637;
            $use_read_id = 965;
            $use_cashout_id = 755;
            $use_sales_id = 9183;
            foreach ($range as $date) {
                $query .= "#".$date."\r\n\r\n";     
                $where = array("DATE(check_in) = '".$date."'"=>array('use'=>'where','val'=>null,'third'=>false));
                $shift_res = $this->site_model->get_tbl('shifts',$where,array(),null,false);
                $rows = $shift_res->result();
                $cols = $shift_res->list_fields();
                

                $query .= "#shifts\r\n";     
                $xread_id = array();
                $cashout_id = array();
                $shift_id = array();
                $ctr = 1;
                $shftsID = array();
                $readID = array();
                $cashoutID = array();
                foreach ($rows as $res) {
                    $row = array();
                    foreach ($cols as $name) {
                        if($name != "id"){
                            if($name == 'shift_id'){
                                $use_shift_id += 1;
                                $row[$name] = $use_shift_id;
                                $shftsID[$res->shift_id] = $use_shift_id;
                                $sh_string .= $use_shift_id.",";
                            }
                            else if($name == 'xread_id'){
                                $use_read_id += 1;
                                $row[$name] = $use_read_id;
                                $readID[$res->xread_id] = $use_read_id;
                            }
                            else if($name == 'cashout_id'){
                                $use_cashout_id += 1;
                                $row[$name] = $use_cashout_id;

                                $cashoutID[$res->cashout_id] = $use_cashout_id;
                            }
                            else
                                $row[$name] = $res->$name;
                        }
                    }
                    if(!$to_main)
                        if(isset($row['pos_id']))unset($row['pos_id']);
                    $query .= $this->db->insert_string('shifts',$row).";\r\n";
                    $shift_id[] = $res->shift_id;
                    $cashout_id[] = $res->cashout_id;
                    $xread_id[] = $res->xread_id;
                    $ctr += 1;
                }
                $query .= "#shifts - ".$ctr."\r\n\r\n";     

                $query .= "#shift_entries\r\n";
                $ctr = 1;
                $where = array("shift_id"=>$shift_id);
                $shift_entries_res = $this->site_model->get_tbl('shift_entries',$where,array(),null,false);
                $rows = $shift_entries_res->result();
                $cols = $shift_entries_res->list_fields();
                foreach ($rows as $res) {
                    $row = array();
                    foreach ($cols as $name) {
                        if($name != "id" && $name != 'entry_id'){
                            if($name == 'shift_id'){
                                $row[$name] = $use_shift_id;
                            }
                            else
                                $row[$name] = $res->$name;
                        }
                    }
                    if(!$to_main)
                        if(isset($row['pos_id']))unset($row['pos_id']);
                    $row['shift_id'] = $shftsID[$res->shift_id];
                    $query .= $this->db->insert_string('shift_entries',$row).";\r\n";
                    $ctr += 1;
                }
                $query .= "#shift_entries - ".$ctr."\r\n\r\n"; 
                
                $where = array("cashout_id"=>$cashout_id);
                $cash['cashout_entries'] = $this->site_model->get_tbl('cashout_entries',$where,array(),null,false);
                $cash['cashout_details'] = $this->site_model->get_tbl('cashout_details',$where,array(),null,false);
                foreach ($cash as $tbl_name => $resu) {
                    $rows = $resu->result();
                    $cols = $resu->list_fields();
                    $query .= "#".$tbl_name."\r\n";
                    $ctr = 1;
                    foreach ($rows as $res) {
                        $row = array();
                        foreach ($cols as $name) {
                            if($name != "id" && $name != 'cashout_detail_id'){
                                if($name == 'cashout_id'){
                                    // $row[$name] = $use_cashout_id;
                                    $row['cashout_id'] = $cashoutID[$res->cashout_id];
                                }
                                else
                                    $row[$name] = $res->$name;
                            }
                        }
                        if(!$to_main)
                         if(isset($row['pos_id']))unset($row['pos_id']);

                        $query .= $this->db->insert_string($tbl_name,$row).";\r\n";
                        $ctr += 1;
                    }
                    $query .= "#".$tbl_name." - ".$ctr."\r\n\r\n";
                }
                $where = array("id"=>$xread_id);
                $read_details_res = $this->site_model->get_tbl('read_details',$where,array(),null,false);
                $rows = $read_details_res->result();
                $cols = $read_details_res->list_fields();
                $query .= "#read_details\r\n";
                $ctr = 1;
                foreach ($rows as $res) {
                    $row = array();
                    foreach ($cols as $name) {
                        if($name != "id"){
                            if($name == 'read_id'){
                                // $row['id'] = $use_read_id;
                                $row['id'] = $readID[$res->read_id];
                            }
                            else
                                $row[$name] = $res->$name;
                        }
                    }
                    if(!$to_main)
                        if(isset($row['pos_id']))unset($row['pos_id']);
                    $query .= $this->db->insert_string('read_details',$row).";\r\n";
                    $ctr += 1;
                }
                $query .= "#read_details - ".$ctr."\r\n\r\n"; 



                $query .= "#trans_sales\r\n";
                $ctr = 1;
                $where = array("shift_id"=>$shift_id);
                $use_sid = array();
                $sales_res = $this->site_model->get_tbl('trans_sales',$where,array(),null,false);
                $rows = $sales_res->result();
                $cols = $sales_res->list_fields();
                $sales_ids = array();   
                foreach ($rows as $res) {
                    $row = array();
                    foreach ($cols as $name) {
                        if($name != "id"){
                            $row[$name] = $res->$name;
                        }
                    }
                    if(!$to_main)
                        if(isset($row['pos_id']))unset($row['pos_id']);
                    $use_sales_id += 1;
                    $row['sales_id'] = $use_sales_id;
                    // $row['shift_id'] = $use_shift_id;
                    $row['shift_id'] = $shftsID[$res->shift_id];
                    $query .= $this->db->insert_string('trans_sales',$row).";\r\n";
                    $ctr += 1;
                    // $sales_ids[] = $row['sales_id'];
                    $sales_ids[] = $res->sales_id;
                    $use_sid[$res->sales_id] = $use_sales_id;
                }
                $query .= "#trans_sales - ".$ctr."\r\n\r\n";

                $tbl['trans_sales_charges'] = $this->site_model->get_tbl('trans_sales_charges',array('sales_id'=>$sales_ids),array(),null,false);
                // $tbl['trans_sales_items'] = $this->site_model->get_tbl('trans_sales_items',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_discounts'] = $this->site_model->get_tbl('trans_sales_discounts',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_menu_modifiers'] = $this->site_model->get_tbl('trans_sales_menu_modifiers',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_menus'] = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_no_tax'] = $this->site_model->get_tbl('trans_sales_no_tax',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_payments'] = $this->site_model->get_tbl('trans_sales_payments',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_tax'] = $this->site_model->get_tbl('trans_sales_tax',array('sales_id'=>$sales_ids),array(),null,false);
                $tbl['trans_sales_zero_rated'] = $this->site_model->get_tbl('trans_sales_zero_rated',array('sales_id'=>$sales_ids),array(),null,false);
                // $tbl['trans_sales_local_tax'] = $this->site_model->get_tbl('trans_sales_local_tax',array('sales_id'=>$sales_ids),array(),null,false);
                $ignore_cols = array('sales_disc_id','sales_menu_id','sales_no_tax_id','payment_id','sales_tax_id','sales_zero_rated_id');
                foreach ($tbl as $tbl_name => $resu) {
                    $rows = $resu->result();
                    $cols = $resu->list_fields();
                    $query .= "#".$tbl_name."\r\n";
                    $ctr = 1;
                    foreach ($rows as $res) {
                        $row = array();
                        foreach ($cols as $name) {
                            if($name != "id"){
                                if(!in_array($name,$ignore_cols))
                                    $row[$name] = $res->$name;
                            }
                        }
                        if(!$to_main)
                            if(isset($row['pos_id']))unset($row['pos_id']);
                        $row['sales_id'] = $use_sid[$res->sales_id];
                        $query .= $this->db->insert_string($tbl_name,$row).";\r\n";
                        $ctr += 1;
                    }
                    $query .= "#".$tbl_name." - ".$ctr."\r\n\r\n";
                }
                $where = array("DATE(read_date) = '".$date."'"=>array('use'=>'where','val'=>null,'third'=>false),'read_type'=>2);
                $z_res = $this->site_model->get_tbl('read_details',$where,array(),null,false);
                $rows = $z_res->result();
                $cols = $z_res->list_fields();
                $query .= "#ZREAD\r\n";
                $ctr = 1;
                foreach ($rows as $res) {
                    $row = array();
                    foreach ($cols as $name) {
                        if($name != "id"){
                            if($name == 'read_id'){
                                $use_read_id += 1;
                                $row['id'] = $use_read_id;
                                // $row['id'] = $res->$name;
                            }
                            else
                                $row[$name] = $res->$name;
                            // $row[$name] = $res->$name;
                        }
                    }
                    if(!$to_main)
                        if(isset($row['pos_id']))unset($row['pos_id']);
                    $query .= $this->db->insert_string('read_details',$row).";\r\n";
                    $ctr += 1;
                }
                $query .= "#ZREAD - ".$ctr."\r\n\r\n";
                
            }
            echo "#".$sh_string;
            echo "<pre>".$query."</pre>";
        }
        public function shift_fixer(){
            $query = "";
            $this->site_model->db = $this->load->database('main', TRUE);
            $this->cashier_model->db = $this->load->database('main', TRUE);
            
            $query .= "#JULY 3\r\n";
            $read_date = '2016-07-03';
            $from_date = $read_date.' 14:50:00';
            $to_date = $read_date.' 21:20:49';
            $shift_id = 6;
            $xread_id = 11;
            $zread_id = 7;
            $zfrom_date = $read_date.' 10:02:47';
            $query .= "UPDATE shifts set check_in = '".date2SqlDateTime($from_date)."',check_out = '".date2SqlDateTime($to_date)."' where shift_id = ".$shift_id.";\r\n";
            $result = $this->site_model->get_tbl('trans_sales',array('shift_id'=>$shift_id));
            foreach ($result as $res) {
                $rand = $this->rand_date($from_date,$to_date);
                $set['datetime'] = $rand;
                $set['update_date'] = date("Y-m-d H:i:s", strtotime('+'.rand(1,10).' minutes '.$rand));
                $where['trans_sales.sales_id'] = $res->sales_id;
                $query .= $this->db->update_string('trans_sales',$set,$where).";\r\n";
            }
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($from_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$xread_id.";\r\n";
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($zfrom_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$zread_id.";\r\n";
            
            $query .= "#JULY 4\r\n";
            $read_date = '2016-07-04';
            $from_date = $read_date.' 09:48:47';
            $to_date = $read_date.' 21:50:29';
            $shift_id = 5;
            $xread_id = 8;
            $zread_id = 9;
            $zfrom_date = $read_date.' 09:48:47';
            $query .= "UPDATE shifts set check_in = '".date2SqlDateTime($from_date)."',check_out = '".date2SqlDateTime($to_date)."' where shift_id = ".$shift_id.";\r\n";
            $result = $this->site_model->get_tbl('trans_sales',array('shift_id'=>$shift_id));
            foreach ($result as $res) {
                $rand = $this->rand_date($from_date,$to_date);
                $set['datetime'] = $rand;
                $set['update_date'] = date("Y-m-d H:i:s", strtotime('+'.rand(1,10).' minutes '.$rand));
                $where['trans_sales.sales_id'] = $res->sales_id;
                $query .= $this->db->update_string('trans_sales',$set,$where).";\r\n";
            }
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($from_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$xread_id.";\r\n";
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($zfrom_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$zread_id.";\r\n";
            
            $query .= "#JULY 5\r\n";
            $read_date = '2016-07-05';
            $from_date = $read_date.' 09:25:43';
            $to_date = $read_date.' 22:01:39';
            $shift_id = 7;
            $xread_id = 10;
            $zread_id = 12;
            $zfrom_date = $read_date.' 09:25:43';
            $query .= "UPDATE shifts set check_in = '".date2SqlDateTime($from_date)."',check_out = '".date2SqlDateTime($to_date)."' where shift_id = ".$shift_id.";\r\n";
            $result = $this->site_model->get_tbl('trans_sales',array('shift_id'=>$shift_id));
            foreach ($result as $res) {
                $rand = $this->rand_date($from_date,$to_date);
                $set['datetime'] = $rand;
                $set['update_date'] = date("Y-m-d H:i:s", strtotime('+'.rand(1,10).' minutes '.$rand));
                $where['trans_sales.sales_id'] = $res->sales_id;
                $query .= $this->db->update_string('trans_sales',$set,$where).";\r\n";
            }
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($from_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$xread_id.";\r\n";
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($zfrom_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$zread_id.";\r\n";
            
            $query .= "#JULY 6\r\n";
            $read_date = '2016-07-06';
            $from_date = $read_date.' 09:35:43';
            $to_date = $read_date.' 22:21:39';
            $shift_id = 8;
            $xread_id = 13;
            $zfrom_date = $read_date.' 09:35:43';
            $query .= "UPDATE shifts set check_in = '".date2SqlDateTime($from_date)."',check_out = '".date2SqlDateTime($to_date)."' where shift_id = ".$shift_id.";\r\n";
            $result = $this->site_model->get_tbl('trans_sales',array('shift_id'=>$shift_id));
            foreach ($result as $res) {
                $rand = $this->rand_date($from_date,$to_date);
                $set['datetime'] = $rand;
                $set['update_date'] = date("Y-m-d H:i:s", strtotime('+'.rand(1,10).' minutes '.$rand));
                $where['trans_sales.sales_id'] = $res->sales_id;
                $query .= $this->db->update_string('trans_sales',$set,$where).";\r\n";
            }
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($from_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$xread_id.";\r\n";
            $zread_id = 14;
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($zfrom_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$zread_id.";\r\n";
            $zread_id = 15;
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($zfrom_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$zread_id.";\r\n";
            
            $query .= "#JULY 7\r\n";
            $read_date = '2016-07-07';
            $from_date = $read_date.' 09:15:43';
            $to_date = $read_date.' 14:21:39';
            $shift_id = 9;
            $xread_id = 16;
            $zfrom_date = $read_date.' 09:15:43';
            $query .= "UPDATE shifts set check_in = '".date2SqlDateTime($from_date)."',check_out = '".date2SqlDateTime($to_date)."' where shift_id = ".$shift_id.";\r\n";
            $result = $this->site_model->get_tbl('trans_sales',array('shift_id'=>$shift_id));
            foreach ($result as $res) {
                $rand = $this->rand_date($from_date,$to_date);
                $set['datetime'] = $rand;
                $set['update_date'] = date("Y-m-d H:i:s", strtotime('+'.rand(1,10).' minutes '.$rand));
                $where['trans_sales.sales_id'] = $res->sales_id;
                $query .= $this->db->update_string('trans_sales',$set,$where).";\r\n";
            }
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($from_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$xread_id.";\r\n";
            $zread_id = 17;
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($zfrom_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$zread_id.";\r\n";
            $zread_id = 18;
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($zfrom_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$zread_id.";\r\n";

            $read_date = '2016-07-07';
            $from_date = $read_date.' 14:25:43';
            $to_date = $read_date.' 22:01:39';
            $shift_id = 10;
            $xread_id = 19;
            $zread_id = 20;
            $zfrom_date = $read_date.' 14:25:43';
            $query .= "UPDATE shifts set check_in = '".date2SqlDateTime($from_date)."',check_out = '".date2SqlDateTime($to_date)."' where shift_id = ".$shift_id.";\r\n";
            $result = $this->site_model->get_tbl('trans_sales',array('shift_id'=>$shift_id));
            foreach ($result as $res) {
                $rand = $this->rand_date($from_date,$to_date);
                $set['datetime'] = $rand;
                $set['update_date'] = date("Y-m-d H:i:s", strtotime('+'.rand(1,10).' minutes '.$rand));
                $where['trans_sales.sales_id'] = $res->sales_id;
                $query .= $this->db->update_string('trans_sales',$set,$where).";\r\n";
            }
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($from_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$xread_id.";\r\n";
            $query .= "UPDATE read_details set read_date = '".$read_date."',scope_from = '".date2SqlDateTime($zfrom_date)."',scope_to ='".date2SqlDateTime($to_date)."' where read_id = ".$zread_id.";\r\n";


            echo "<pre>".$query."</pre>";
        }
        function rand_date($min_date, $max_date) {
            $min_epoch = strtotime($min_date);
            $max_epoch = strtotime($max_date);
            $rand_epoch = rand($min_epoch, $max_epoch);
            return date('Y-m-d H:i:s', $rand_epoch);
        }
    ##################
    ### DUGTONG     
        public function recover(){
            $data = $this->syter->spawn('menu');
            $data['page_title'] = fa('fa-user').' POS TECCHNICAL';
            $this->make->sBox('solid');
                $this->make->sBoxBody();
                    $this->make->H(3,'RECOVER XREAD BACKUP',array('class'=>'page-header'));
                    $this->make->sDivRow();
                        $this->make->sDivCol(4);
                            $this->make->input('DATABASE NAME','xdatabase',null,null,array());
                        $this->make->eDivCol();
                        $this->make->sDivCol(4);
                            $this->make->date('XREAD DATE','xdate',null,null,array());
                        $this->make->eDivCol();
                        $this->make->sDivCol(4);
                            $this->make->button('PROCESS',array('id'=>'xprocess-btn','style'=>'margin-top:23px;'),'success');
                        $this->make->eDivCol();
                    $this->make->eDivRow();
                    $this->make->H(3,'MANUAL ZREAD',array('class'=>'page-header'));
                    $this->make->sDivRow();
                        $this->make->sDivCol(4);
                            $this->make->date('ZREAD DATE','zdate',null,null,array());
                        $this->make->eDivCol();
                        $this->make->sDivCol(4);
                            $this->make->button('PROCESS',array('id'=>'zprocess-btn','style'=>'margin-top:23px;'),'success');
                        $this->make->eDivCol();
                    $this->make->eDivRow();
                $this->make->eBoxBody();
            $this->make->eBox();
            $data['code'] = $this->make->code();
            $data['load_js'] = 'dine/reads.php';
            $data['use_js'] = 'recoverJs';
            $this->load->view('page',$data);
        }    
        public function zinsert($xdate=null){
            $read_date = $xdate;
            if($this->input->post('zdate'))
                $read_date = $this->input->post('zdate');
            $error = "";
            $args["DATE(shifts.check_in) = DATE('".date2Sql($read_date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            $select = "shifts.*";
            $shifts = $this->site_model->get_tbl('shifts',$args,array('check_in'=>'desc'),null,true,$select);
            $shift_ids = array();
            $datetimes = array();
            foreach ($shifts as $shft) {
                $shift_ids[] = $shft->shift_id;
                $datetimes[] = $shft->check_in;
                $datetimes[] = $shft->check_out;
            }
            if(count($shift_ids) == 0){
                $error = "No SHIFTS found on ".$read_date;
                echo json_encode(array('error'=>$error));
                return false;
            }
            $this->load->library('../controllers/dine/main');
            foreach ($shift_ids as $shf_id) {
                $this->main->shifts_to_main($shf_id);
            }
            usort($datetimes, function($a, $b) {
                return strtotime($a) - strtotime($b);
            });
            $date_from = "";
            $date_to = "";
            $ctr = 1;
            foreach ($datetimes as $dt) {
                if($ctr == 1) $date_from = $dt;
                $date_to = $dt;
                $ctr++;
            }
            $read_id = $this->go_zread(false,$date_from,$date_to,$read_date);
            if($this->input->post('zdate'))
                echo json_encode(array('error'=>$error));
        }    
        public function xinsert(){
            $dbname = $this->input->post('xdb');
            $xdate = $this->input->post('xdate');
            // echo $dbname.' -- '.$xdate; die();
            $error = "";
            $last_sales_id = 0;
            // $this->db = $this->load->database('main',true);
            $row = $this->db->query('SELECT MAX(sales_id) AS `maxid` FROM `trans_sales`')->row();
            if ($row->maxid != "") {
                $last_sales_id = $row->maxid; 
            }
            $config['hostname'] = 'localhost';
            $config['username'] = 'root';
            $config['password'] = '';
            $config['database'] = $dbname;
            $config['dbdriver'] = 'mysql';
            $config['dbprefix'] = '';
            $config['pconnect'] = FALSE;
            $config['db_debug'] = TRUE;
            $config['cache_on'] = FALSE;
            $config['cachedir'] = '';
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $this->site_model->db = $this->load->database($config,true);
            $where = array("DATE(check_in) = '".date2Sql($xdate)."'"=>array('use'=>'where','val'=>null,'third'=>false));
            $shifts_res = $this->site_model->get_tbl('shifts',$where);
            $shift_trans = array();
            foreach ($shifts_res as $shf) {
                $old_shf_id = $shf->shift_id;
                $shift_trans[$old_shf_id] = array();
                ##############################################################################################################
                ### SHIFT HEADER
                    $shift = array(
                        'user_id'       => $shf->user_id,
                        'check_in'      => $shf->check_in,
                        'check_out'     => $shf->check_out,
                        'terminal_id'   => $shf->terminal_id,
                    );
                    $shift_trans[$old_shf_id]['shifts'] = $shift;
                    #shift_entries
                    $where = array('shift_id'=>$old_shf_id);
                    $shift_entries_res = $this->site_model->get_tbl('shift_entries',$where);
                    $shift_entries = array();
                    foreach ($shift_entries_res as $res) {
                        $shift_entries[] = array(
                            'amount'    =>  $res->amount,
                            'user_id'   =>  $res->user_id,
                            'trans_date'=>  $res->trans_date,
                        );
                    }
                    $shift_trans[$old_shf_id]['shift_entries'] = $shift_entries;
                    #cashout_entries
                    $where = array('cashout_id'=>$shf->cashout_id);
                    $cashout_entries_res = $this->site_model->get_tbl('cashout_entries',$where);
                    $cashout_entries = array();
                    foreach ($cashout_entries_res as $res) {
                        $cashout_entries = array(
                            'drawer_amount'     =>  $res->drawer_amount,
                            'count_amount'      =>  $res->count_amount,
                            'user_id'           =>  $res->user_id,
                            'terminal_id'       =>  $res->terminal_id,
                            'trans_date'        =>  $res->trans_date,
                        );
                    }
                    $shift_trans[$old_shf_id]['cashout_entries'] = $cashout_entries;
                    #cashout_details
                    $where = array('cashout_id'=>$shf->cashout_id);
                    $cashout_details_res = $this->site_model->get_tbl('cashout_details',$where);
                    $cashout_details = array();
                    foreach ($cashout_details_res as $res) {
                        $cashout_details[] = array(
                            'type'          =>  $res->type,
                            'denomination'  =>  $res->denomination,
                            'reference'     =>  $res->reference,
                            'total'         =>  $res->total,
                        );
                    }
                    $shift_trans[$old_shf_id]['cashout_details'] = $cashout_details;
                ##############################################################################################################
                ### SHIFT DETAILS
                    $where = array("shift_id"=>$old_shf_id);
                    $sales_res = $this->site_model->get_tbl('trans_sales',$where,array(),null,false);
                    $rows = $sales_res->result();
                    $cols = $sales_res->list_fields();
                    $trans_sales = array();  
                    $use_sid = array();
                    foreach ($rows as $res) {
                        $row = array();
                        foreach ($cols as $name) {
                            if($name != "sales_id" && $name != "shift_id"){
                                $row[$name] = $res->$name;
                            }
                        }
                        $last_sales_id += 1;
                        $row['sales_id'] = $last_sales_id;
                        $trans_sales[] = $row;
                        $sales_ids[] = $res->sales_id;
                        $use_sid[$res->sales_id] = $last_sales_id;
                    }
                    $shift_trans[$old_shf_id]['trans_sales'] = $trans_sales;
                    $tbl['trans_sales_charges']         = $this->site_model->get_tbl('trans_sales_charges',array('sales_id'=>$sales_ids),array(),null,false);
                    $tbl['trans_sales_items']           = $this->site_model->get_tbl('trans_sales_items',array('sales_id'=>$sales_ids),array(),null,false);
                    $tbl['trans_sales_discounts']       = $this->site_model->get_tbl('trans_sales_discounts',array('sales_id'=>$sales_ids),array(),null,false);
                    $tbl['trans_sales_menu_modifiers']  = $this->site_model->get_tbl('trans_sales_menu_modifiers',array('sales_id'=>$sales_ids),array(),null,false);
                    $tbl['trans_sales_menus']           = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$sales_ids),array(),null,false);
                    $tbl['trans_sales_no_tax']          = $this->site_model->get_tbl('trans_sales_no_tax',array('sales_id'=>$sales_ids),array(),null,false);
                    $tbl['trans_sales_payments']        = $this->site_model->get_tbl('trans_sales_payments',array('sales_id'=>$sales_ids),array(),null,false);
                    $tbl['trans_sales_tax']             = $this->site_model->get_tbl('trans_sales_tax',array('sales_id'=>$sales_ids),array(),null,false);
                    $tbl['trans_sales_zero_rated']      = $this->site_model->get_tbl('trans_sales_zero_rated',array('sales_id'=>$sales_ids),array(),null,false);
                    $tbl['trans_sales_local_tax']       = $this->site_model->get_tbl('trans_sales_local_tax',array('sales_id'=>$sales_ids),array(),null,false);
                    $ignore_cols = array('sales_id','sales_charge_id','sales_disc_id','sales_item_id','sales_menu_id','sales_no_tax_id','payment_id','sales_tax_id','sales_zero_rated_id','sales_local_tax_id','sales_mod_id');
                    foreach ($tbl as $tbl_name => $resu) {
                        $rows = $resu->result();
                        $cols = $resu->list_fields();
                        $tbl_trans = array();
                        foreach ($rows as $res) {
                            $row = array();
                            foreach ($cols as $name) {
                                if(!in_array($name,$ignore_cols))
                                    $row[$name] = $res->$name;
                            }
                            if(isset($use_sid[$res->sales_id])){
                                $row['sales_id'] = $use_sid[$res->sales_id];
                                $tbl_trans[] = $row;
                            }
                        }
                        $shift_trans[$old_shf_id][$tbl_name] = $tbl_trans;
                    }
                ##############################################################################################################
            }
            $this->site_model->db = $this->load->database('default',true);
            foreach ($shift_trans as $old_shf_id => $shf) {
                $shifts = $shf['shifts'];
                $use_shift_id = $this->site_model->add_tbl('shifts',$shifts);
                
                $shift_entries = $shf['shift_entries'];
                foreach ($shift_entries as $key => $row) {
                    $row['shift_id'] = $use_shift_id;
                    $shift_entries[$key] = $row;
                }
                $this->site_model->add_tbl_batch('shift_entries',$shift_entries);
                
                $cashout_entries = $shf['cashout_entries'];
                $use_cashout_id = $this->site_model->add_tbl('cashout_entries',$cashout_entries);
                
                $cashout_details = $shf['cashout_details'];
                foreach ($cashout_details as $key => $row) {
                    $row['cashout_id'] = $use_cashout_id;
                    $cashout_details[$key] = $row;
                }
                $this->site_model->add_tbl_batch('cashout_details',$cashout_details);
                ##############################################################################################################
                $trans_sales = $shf['trans_sales'];
                foreach ($trans_sales as $key => $row) {
                    $row['shift_id'] = $use_shift_id;
                    $trans_sales[$key] = $row;
                }

                // var_dump($trans_sales); die();

                $this->site_model->add_tbl_batch('trans_sales',$trans_sales);
                if(count($shf['trans_sales_charges']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_charges',$shf['trans_sales_charges']);
                if(count($shf['trans_sales_items']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_items',$shf['trans_sales_items']);
                if(count($shf['trans_sales_discounts']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_discounts',$shf['trans_sales_discounts']);
                if(count($shf['trans_sales_menu_modifiers']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_menu_modifiers',$shf['trans_sales_menu_modifiers']);
                if(count($shf['trans_sales_menus']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_menus',$shf['trans_sales_menus']);
                if(count($shf['trans_sales_no_tax']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_no_tax',$shf['trans_sales_no_tax']);
                if(count($shf['trans_sales_payments']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_payments',$shf['trans_sales_payments']);
                if(count($shf['trans_sales_tax']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_tax',$shf['trans_sales_tax']);
                if(count($shf['trans_sales_zero_rated']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_zero_rated',$shf['trans_sales_zero_rated']);
                if(count($shf['trans_sales_local_tax']) > 0)
                    $this->site_model->add_tbl_batch('trans_sales_local_tax',$shf['trans_sales_local_tax']);
                ##############################################################################################################
                $xread = array(
                    'read_type'     =>  1,
                    'read_date'     =>  date2Sql($shifts['check_in']),
                    'user_id'       =>  $shifts['user_id'],
                    'scope_from'    =>  $shifts['check_in'],
                    'scope_to'      =>  $shifts['check_out'],
                );
                $use_xread_id = $this->site_model->add_tbl('read_details',$xread);
                $this->site_model->update_tbl('shifts','shift_id',array('cashout_id'=>$use_cashout_id,'xread_id'=>$use_xread_id),$use_shift_id);
            }
            echo json_encode(array('error'=>$error));
        }
        public function xinsert_batch(){
            // $dbname = $this->input->post('xdb');
            // $xdate = $this->input->post('xdate');
            $dbname = 'dine_hap_malolos_sale';
            $xdates = array('2016-12-18',
                            '2016-12-19',
                            '2016-12-20',
                            );
            foreach ($xdates as $xdate) {
                $error = "";
                $last_sales_id = 0;
                $this->db = $this->load->database('main',true);
                $row = $this->db->query('SELECT MAX(sales_id) AS `maxid` FROM `trans_sales`')->row();
                if ($row->maxid != "") {
                    $last_sales_id = $row->maxid; 
                }
                $config['hostname'] = 'localhost';
                $config['username'] = 'root';
                $config['password'] = '';
                $config['database'] = $dbname;
                $config['dbdriver'] = 'mysql';
                $config['dbprefix'] = '';
                $config['pconnect'] = FALSE;
                $config['db_debug'] = TRUE;
                $config['cache_on'] = FALSE;
                $config['cachedir'] = '';
                $config['char_set'] = 'utf8';
                $config['dbcollat'] = 'utf8_general_ci';
                $this->site_model->db = $this->load->database($config,true);
                $where = array("DATE(check_in) = '".date2Sql($xdate)."'"=>array('use'=>'where','val'=>null,'third'=>false));
                $shifts_res = $this->site_model->get_tbl('shifts',$where);
                $shift_trans = array();
                foreach ($shifts_res as $shf) {
                    $old_shf_id = $shf->shift_id;
                    $shift_trans[$old_shf_id] = array();
                    ##############################################################################################################
                    ### SHIFT HEADER
                        $shift = array(
                            'user_id'       => $shf->user_id,
                            'check_in'      => $shf->check_in,
                            'check_out'     => $shf->check_out,
                            'terminal_id'   => $shf->terminal_id,
                        );
                        $shift_trans[$old_shf_id]['shifts'] = $shift;
                        #shift_entries
                        $where = array('shift_id'=>$old_shf_id);
                        $shift_entries_res = $this->site_model->get_tbl('shift_entries',$where);
                        $shift_entries = array();
                        foreach ($shift_entries_res as $res) {
                            $shift_entries[] = array(
                                'amount'    =>  $res->amount,
                                'user_id'   =>  $res->user_id,
                                'trans_date'=>  $res->trans_date,
                            );
                        }
                        $shift_trans[$old_shf_id]['shift_entries'] = $shift_entries;
                        #cashout_entries
                        $where = array('cashout_id'=>$shf->cashout_id);
                        $cashout_entries_res = $this->site_model->get_tbl('cashout_entries',$where);
                        $cashout_entries = array();
                        foreach ($cashout_entries_res as $res) {
                            $cashout_entries = array(
                                'drawer_amount'     =>  $res->drawer_amount,
                                'count_amount'      =>  $res->count_amount,
                                'user_id'           =>  $res->user_id,
                                'terminal_id'       =>  $res->terminal_id,
                                'trans_date'        =>  $res->trans_date,
                            );
                        }
                        $shift_trans[$old_shf_id]['cashout_entries'] = $cashout_entries;
                        #cashout_details
                        $where = array('cashout_id'=>$shf->cashout_id);
                        $cashout_details_res = $this->site_model->get_tbl('cashout_details',$where);
                        $cashout_details = array();
                        foreach ($cashout_details_res as $res) {
                            $cashout_details[] = array(
                                'type'          =>  $res->type,
                                'denomination'  =>  $res->denomination,
                                'reference'     =>  $res->reference,
                                'total'         =>  $res->total,
                            );
                        }
                        $shift_trans[$old_shf_id]['cashout_details'] = $cashout_details;
                    ##############################################################################################################
                    ### SHIFT DETAILS
                        $where = array("shift_id"=>$old_shf_id);
                        $sales_res = $this->site_model->get_tbl('trans_sales',$where,array(),null,false);
                        $rows = $sales_res->result();
                        $cols = $sales_res->list_fields();
                        $trans_sales = array();  
                        $use_sid = array();
                        foreach ($rows as $res) {
                            $row = array();
                            foreach ($cols as $name) {
                                if($name != "sales_id" && $name != "shift_id"){
                                    $row[$name] = $res->$name;
                                }
                            }
                            $last_sales_id += 1;
                            $row['sales_id'] = $last_sales_id;
                            $trans_sales[] = $row;
                            $sales_ids[] = $res->sales_id;
                            $use_sid[$res->sales_id] = $last_sales_id;
                        }
                        $shift_trans[$old_shf_id]['trans_sales'] = $trans_sales;
                        $tbl['trans_sales_charges']         = $this->site_model->get_tbl('trans_sales_charges',array('sales_id'=>$sales_ids),array(),null,false);
                        $tbl['trans_sales_items']           = $this->site_model->get_tbl('trans_sales_items',array('sales_id'=>$sales_ids),array(),null,false);
                        $tbl['trans_sales_discounts']       = $this->site_model->get_tbl('trans_sales_discounts',array('sales_id'=>$sales_ids),array(),null,false);
                        $tbl['trans_sales_menu_modifiers']  = $this->site_model->get_tbl('trans_sales_menu_modifiers',array('sales_id'=>$sales_ids),array(),null,false);
                        $tbl['trans_sales_menus']           = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$sales_ids),array(),null,false);
                        $tbl['trans_sales_no_tax']          = $this->site_model->get_tbl('trans_sales_no_tax',array('sales_id'=>$sales_ids),array(),null,false);
                        $tbl['trans_sales_payments']        = $this->site_model->get_tbl('trans_sales_payments',array('sales_id'=>$sales_ids),array(),null,false);
                        $tbl['trans_sales_tax']             = $this->site_model->get_tbl('trans_sales_tax',array('sales_id'=>$sales_ids),array(),null,false);
                        $tbl['trans_sales_zero_rated']      = $this->site_model->get_tbl('trans_sales_zero_rated',array('sales_id'=>$sales_ids),array(),null,false);
                        $tbl['trans_sales_local_tax']       = $this->site_model->get_tbl('trans_sales_local_tax',array('sales_id'=>$sales_ids),array(),null,false);
                        $ignore_cols = array('sales_id','sales_charge_id','sales_disc_id','sales_item_id','sales_menu_id','sales_no_tax_id','payment_id','sales_tax_id','sales_zero_rated_id','sales_local_tax_id','sales_mod_id','pos_id');
                        foreach ($tbl as $tbl_name => $resu) {
                            $rows = $resu->result();
                            $cols = $resu->list_fields();
                            $tbl_trans = array();
                            foreach ($rows as $res) {
                                $row = array();
                                foreach ($cols as $name) {
                                    if(!in_array($name,$ignore_cols))
                                        $row[$name] = $res->$name;
                                }
                                if(isset($use_sid[$res->sales_id])){
                                    $row['sales_id'] = $use_sid[$res->sales_id];
                                    $tbl_trans[] = $row;
                                }
                            }
                            $shift_trans[$old_shf_id][$tbl_name] = $tbl_trans;
                        }
                    ##############################################################################################################
                }
                $this->site_model->db = $this->load->database('default',true);
                foreach ($shift_trans as $old_shf_id => $shf) {
                    $shifts = $shf['shifts'];
                    $use_shift_id = $this->site_model->add_tbl('shifts',$shifts);
                    
                    $shift_entries = $shf['shift_entries'];
                    foreach ($shift_entries as $key => $row) {
                        $row['shift_id'] = $use_shift_id;
                        $shift_entries[$key] = $row;
                    }
                    $this->site_model->add_tbl_batch('shift_entries',$shift_entries);
                    
                    $cashout_entries = $shf['cashout_entries'];
                    $use_cashout_id = $this->site_model->add_tbl('cashout_entries',$cashout_entries);
                    
                    $cashout_details = $shf['cashout_details'];
                    foreach ($cashout_details as $key => $row) {
                        $row['cashout_id'] = $use_cashout_id;
                        $cashout_details[$key] = $row;
                    }
                    $this->site_model->add_tbl_batch('cashout_details',$cashout_details);
                    ##############################################################################################################
                    $trans_sales = $shf['trans_sales'];
                    foreach ($trans_sales as $key => $row) {
                        $row['shift_id'] = $use_shift_id;
                        unset($row['id']);
                        unset($row['pos_id']);
                        $trans_sales[$key] = $row;
                    }
                    $this->site_model->add_tbl_batch('trans_sales',$trans_sales);
                    if(count($shf['trans_sales_charges']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_charges',$shf['trans_sales_charges']);
                    if(count($shf['trans_sales_items']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_items',$shf['trans_sales_items']);
                    if(count($shf['trans_sales_discounts']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_discounts',$shf['trans_sales_discounts']);
                    if(count($shf['trans_sales_menu_modifiers']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_menu_modifiers',$shf['trans_sales_menu_modifiers']);
                    if(count($shf['trans_sales_menus']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_menus',$shf['trans_sales_menus']);
                    if(count($shf['trans_sales_no_tax']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_no_tax',$shf['trans_sales_no_tax']);
                    if(count($shf['trans_sales_payments']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_payments',$shf['trans_sales_payments']);
                    if(count($shf['trans_sales_tax']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_tax',$shf['trans_sales_tax']);
                    if(count($shf['trans_sales_zero_rated']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_zero_rated',$shf['trans_sales_zero_rated']);
                    if(count($shf['trans_sales_local_tax']) > 0)
                        $this->site_model->add_tbl_batch('trans_sales_local_tax',$shf['trans_sales_local_tax']);
                    ##############################################################################################################
                    $xread = array(
                        'read_type'     =>  1,
                        'read_date'     =>  date2Sql($shifts['check_in']),
                        'user_id'       =>  $shifts['user_id'],
                        'scope_from'    =>  $shifts['check_in'],
                        'scope_to'      =>  $shifts['check_out'],
                    );
                    $use_xread_id = $this->site_model->add_tbl('read_details',$xread);
                    $this->site_model->update_tbl('shifts','shift_id',array('cashout_id'=>$use_cashout_id,'xread_id'=>$use_xread_id),$use_shift_id);
                }
                $this->zinsert($xdate);
                if($error != ""){
                    break;
                }
            }
            echo json_encode(array('error'=>$error));
        }    
    ##################
}