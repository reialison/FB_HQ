<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/reads.php");
require APPPATH.'/libraries/phpmailer/PHPMailerAutoload.php';
class Email extends Reads {
    public function __construct(){
        parent::__construct();
        $this->load->model('dine/menu_model');
        $this->load->helper('dine/email_helper');
    }
    public function index(){
        $this->load->model('dine/menu_model');
        $this->load->helper('dine/menu_helper');
        $this->load->helper('site/site_forms_helper');
        $data = $this->syter->spawn('menu');
        $data['page_title'] = fa(' icon-envelope-letter').' Email Maintenance ';
        // $menus = $this->menu_model->get_menus();
        $th = array('ID','Name','Email Address','Email Setting Types','Inactive', ' ');
        $data['code'] = create_rtable('email','email_id','email-tbl',$th);
        // echo $data['code'];die();
        // $data['code'] = menuListPage($menus);
        $data['load_js'] = 'dine/email_js.php';
        $data['use_js'] = 'emaillistJS';
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $this->load->view('page',$data);
    }
    public function get_email_settings($id=null,$asJson=true){
        $this->load->helper('site/pagination_helper');
        $this->load->model('dine/email_model');
        $pagi = null;
        $args = array();
        $total_rows = 50;
        if($this->input->post('pagi'))
            $pagi = $this->input->post('pagi');
        $post = array();
        
        if(count($this->input->post()) > 0){
            $post = $this->input->post();
        }
        $join = null;
        $count = $this->site_model->get_tbl('email_setting',$args,array(),$join,true,'email_setting.*',null,null,true);
        $page = paginate('email/get_email_settings',$count,$total_rows,$pagi);
        $items = $this->site_model->get_tbl('email_setting',$args,array(),$join,true,'email_setting.*',null,$page['limit']);
        // echo "<pre>",print_r($items),"</pre>";die();
        $json = array();
        if(count($items) > 0){
            $ids = array();
            foreach ($items as $res) {
                if($res->types == 1){
                    $type = "Main receiver";
                }
                else{
                    $type = "Carbon Copy(Cc)";   
                }
                // $name = $this->email_model->get_data_email($res->email_);
                $link = $this->make->A(fa('fa-edit fa-lg').'  Edit',base_url().'mods/form/'.$res->id,array('class'=>'btn btn-outline-success-600 radius-8 px-20 py-11 align-items-center gap-2','id'=>'edit-'.$res->id,'ref'=>$res->id,'return'=>'true'));
                $json[$res->id] = array(
                    "id"=>$res->id,   
                    "Name"=>ucwords(strtolower($res->name)),
                    "email"=>$res->email_address,
                    "types"=>$type,
                    "inactive_view"=>($res->inactive == 0 ? 'No' : 'Yes'),
                    "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),
                    "link"=>$link
                );
                $ids[] = $res->id;
            }
        }
        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    } 
    public function email_form($ref=null){
        $this->load->helper('dine/email_helper');
        $this->load->model('dine/email_model');
        $cat = array();
        if($ref != null){
            $cats = $this->email_model->get_email_settings($ref,null);
            $cat = $cats[0];
        }
        $this->data['code'] = makeEmailForm($cat);
        $this->load->view('load',$this->data);
    }
    public function email_form_db(){
        $this->load->model('dine/menu_model');
        $this->load->model('dine/email_model');
        $items = array();
        $validate_email = null;
        $validate_id = 0;
        // var_dump($this->input->post('inactive')); die();
        // $email_id = $this->email_model->get_last_email();
        $items = array(
            // "id"=>$email_id+1,
            "name"=>$this->input->post('name'),
            "email_address"=>$this->input->post('email'),
            "types"=>$this->input->post('email_types'),
            "inactive"=>$this->input->post('inactive')
        );
        if(!filter_var($items['email_address'], FILTER_VALIDATE_EMAIL)) {
            $msg = 'Invalid Email.';
            site_alert($msg,"error");
            die();
        }
        $check_email = $this->email_model->check_email($items['email_address']);
        if(!empty($check_email)){
            $validate_email = $check_email[0]->email_address;
            $validate_id = $check_email[0]->id;
        }
        // echo "<pre>",print_r($items['email_address']),"</pre>";die();
        if($this->input->post('email_id')){
            if($validate_email == $items['email_address'] && $this->input->post('email_id') != $validate_id) {
                $msg = 'Email Already Exist.edit';
                site_alert($msg,"error");
                die();
            }
            else{
                $this->email_model->update_email_set($items,$this->input->post('email_id'));
                $id = $this->input->post('email_id');
                $act = 'update';
                $msg = 'Updated Email Recipients  '.$this->input->post('name');
                // $this->main_model->update_tbl('menu_subcategory','menu_sub_id',$items,$id);
            }
        }else{
            if(!empty($check_email)) {
                $msg = 'Email Already Exist. ';
                site_alert($msg,"error");
                die();
            }
            $id = $this->email_model->add_email_set($items);
            $act = 'add';
            $msg = 'Added  new Email Recipients '.$this->input->post('name');
            // $this->main_model->add_trans_tbl('menu_subcategory',$items);
        }
        site_alert($msg,"success");
        echo json_encode(array("id"=>$id,"addOpt"=>$items['name'],"desc"=>$this->input->post('name'),"act"=>$act,'msg'=>$msg));
    }
    public function send_email($e_subject, $file, $title, $e_message, $branch_code=""){
        
        $mail = new PHPMailer;
        $this->load->model('dine/email_model');

        $cc_arr = array();
        $main_arr = array();
        $cc_mail = $this->email_model->get_main_recipients_cc();
        $main_mail = $this->email_model->get_main_recipients_main();
        $branches_raw = $this->email_model->get_all_branches();
        $branches = array();

        foreach($branches_raw as $branch){
            $branch_id = $branch->branch_id;
            $branch_code = $branch->branch_code;
            $branches[] = $branch_code;
        }
        // echo "<pre>",print_r(),"</pre>";die();

        $branch_label = implode(' , ', $branches);
        // echo $
            // $cc_arr[] = $cc_mail[$key]->email_address;
        // echo "<pre>",print_r($receiver),"</pre>";die();


        $mail = new PHPMailer;
        $mail->SMTPDebug = 2;
        //Set the hostname of the mail server
        // $mail->Host = 'smtp.gmail.com';
        // $mail->Port = 587;
        // $mail->SMTPSecure = 'tls';
        // $mail->SMTPAuth = true;
        // $mail->Username = "pointoneserver@gmail.com";
        // $mail->Password = "p0!nt0n3";
        $mail->From = "info@pointone.ph";
        $mail->FromName = "Info";


        foreach ($main_mail as $key => $value) {
            $mail->addAddress($value->email_address);
        }
        foreach ($cc_mail as $key => $value) {
            $mail->AddCC($value->email_address);
        }
        $mail->isHTML(true);

        $mail->Subject = $e_subject;
        // $headers[] = "Cc:".$cc_receiver;
        // $subject = $e_subject;  

        $message = "
            <html>
                <body><Br>
                    <center>
                        <table width='300' height='200' >
                            <tr>
                                <th bgcolor='#3fddff'>Branch: </th>
                                <td bgcolor='#bfced8'><font color='black'><center>".$branch_label."</center></font></td>
                            </tr>
                            <tr>
                                <th bgcolor='#3fddff'>Title: </th>
                                <td bgcolor='#bfced8'><font color='black'><center>".$title."</center></font></td>
                            </tr>
                            <tr>
                                <th bgcolor='#3fddff'>Date: </th>
                                <td bgcolor='#bfced8'><font color='black'><center>".date("m/d/Y")."</center></font></td>
                            </tr>
                            <tr>
                                <th bgcolor='#3fddff'>Message: </th>
                                <td bgcolor='#bfced8'><font color='black'><center>".$e_message."</center></font></td>
                            </tr>
                            <tr>
                                <th bgcolor='#3fddff'>Link: </th>
                                <td bgcolor='#bfced8'><font color='black'></font>
                                    <center>
                                        <form>
                                            <a href='".str_replace("/automail", "", base_url()).$file."'><input type='button' value='Click to download' 
                                            style='                
                                            background-color: #fb8712;
                                            border: none;
                                            color: white;
                                            padding: 15px 32px;
                                            text-align: center;
                                            text-decoration: none;
                                            display: inline-block;
                                            font-size: 16px;
                                            margin: 4px 2px;
                                            cursor: pointer;'
                                            /></a>
                                        </form>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </center>
                </body>
            </html>
        ";
        $mail->Body = $message;
        if(!$mail->send()) 
        {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } 
        else 
        {
            echo "Message has been sent successfully";
        }

        // echo $message;
        // die();
        // mail($main_receiver,$subject, $message ,implode("\r\n", $headers));
    }    
    public function send_reports()
    {
        // die("aaa");
        $file = $this->get_monthly_reports();    
        $this->send_email("Monthly Sales ".date("F Y", strtotime("-1 month")), $file, "Monthly Sales", "Please click the download button for the copy of the report.");

        $file = $this->get_daily_reports();    
        $this->send_email("Daily Sales ".date("m/d/Y", strtotime("-1 day")),$file, "Daily Sales", "Please click the download button for the copy of the report.");   

        $file = $this->check_hourly_sales_excel();    
        $this->send_email("Hourly Sales ".date("m/d/Y", strtotime("-1 day")), $file, "Hourly Sales", "Please click the download button for the copy of the report.");   

        $file = $this->monthly_menus_rep_excel();    
        $this->send_email("Menu Sales ".date("F Y", strtotime("-1 month")),$file, "Menu Sales", "Please click the download button for the copy of the report.");   

        $file = $this->item_sales_rep_excel();    
        $this->send_email("Item Sales ".date("F Y", strtotime("-1 month")), $file, "Item Sales", "Please click the download button for the copy of the report.");   

        $file = $this->sales_rep_excel();    
        $this->send_email("Category Sales ".date("F Y", strtotime("-1 month")), $file, "Category Sales", "Please click the download button for the copy of the report.");   
    }
    public function send_monthly_reports()
    {
        echo "Sending...<br>";
        $file = $this->get_monthly_reports();    
        $this->send_email("Monthly Sales ".date("F Y", strtotime("-1 month")), $file, "Monthly Sales", "Please click the download button for the copy of the report.");
        echo "Sent...";    
    }
    public function send_daily_reports()
    {
        echo "Sending...<br>";
        $branch_details = $this->setup_model->get_branch_details();
        foreach ($branch_details as $k => $v) {
            $file = $this->get_daily_reports($v->branch_code);    
            $this->send_email($v->branch_code." Daily Sales ".date("m/d/Y", strtotime("-1 day")),$file, "Daily Sales", "Please click the download button for the copy of the report.", $v->branch_code);            
        }
        echo "Sent...";    
    }
    public function send_hourly_sales_reports()
    {
        $file = $this->check_hourly_sales_excel();    
        $this->send_email("Hourly Sales ".date("m/d/Y", strtotime("-1 day")), $file, "Hourly Sales", "Please click the download button for the copy of the report.");   
    }
     public function send_yearly_menu_sales_reports()
    {
        $file = $this->yearly_menus_rep_excel();    
        $this->send_email("Menu Sales ".date("F Y", strtotime("-1 year")),$file, "Yearly Menu Sales", "Please click the download button for the copy of the report.");        
    }
    public function send_monthly_menu_sales_reports()
    {
        $file = $this->monthly_menus_rep_excel();    
        $this->send_email("Menu Sales ".date("F Y", strtotime("-1 month")),$file, "Monthly Menu Sales", "Please click the download button for the copy of the report.");        
    }
    public function send_daily_menu_sales_reports()
    {
        $file = $this->daily_menus_rep_excel();    
        $this->send_email("Menu Sales ".date("F Y", strtotime("-1 day")),$file, " Daily Menu Sales", "Please click the download button for the copy of the report.");        
    }
    public function send_yearly_item_sales_reports()
    {
        $file = $this->yearly_item_sales_rep_excel();    
        $this->send_email("Item Sales ".date("F Y", strtotime("-1 year")), $file, "Item Sales", "Please click the download button for the copy of the report.");
    }
    public function send_monthly_item_sales_reports()
    {
        $file = $this->monthly_item_sales_rep_excel();    
        $this->send_email("Item Sales ".date("F Y", strtotime("-1 month")), $file, "Item Sales", "Please click the download button for the copy of the report.");
    }
    public function send_daily_item_sales_reports()
    {
        $file = $this->daily_item_sales_rep_excel();    
        $this->send_email("Item Sales ".date("F Y", strtotime("-1 day")), $file, "Item Sales", "Please click the download button for the copy of the report.");
    }
    public function send_yearly_sales_reports()
    {
        $file = $this->yearly_sales_rep_excel();    
        $this->send_email("Category Sales ".date("Y", strtotime("-1 year")), $file, "Category Sales", "Please click the download button for the copy of the report.");
    }
    public function send_monthly_sales_reports()
    {
        $file = $this->monthly_sales_rep_excel();    
        $this->send_email("Category Sales ".date("F Y", strtotime("-1 month")), $file, "Category Sales", "Please click the download button for the copy of the report.");
    }
     public function send_daily_sales_reports()
    {
        $file = $this->daily_sales_rep_excel();    
        $this->send_email("Category Sales ".date("F Y d", strtotime("-1 day")), $file, "Category Sales", "Please click the download button for the copy of the report.");
    }
    public function get_monthly_reports($branch_id=""){
        // sess_clear('month_array');
        // sess_clear('month_date');
        $this->load->helper('dine/reports_helper');
        // $month = $this->input->post('month');
        // $year = $this->input->post('year');
        // $branch_id = $this->input->post('branch_id');
        // $branch_id = "";
        $json = $this->input->post('json');
        // $start_month = date($year.'-'.$month.'-01');
        // $end_month = date("Y-m-t", strtotime($start_month));
        $start_month = date("Y-m-01", strtotime("-1 month"));
        $end_month = date("Y-m-t", strtotime("-1 month"));
        $month_date = array('text'=>sql2Date($start_month).' to '.sql2Date($end_month),'month_year'=>$start_month);
        update_load(10);
        sleep(1);
        $load = 10;
                // echo $cmonth.'<br>';
        $month_array = array();
        while (strtotime($start_month) <= strtotime($end_month)) {
            // echo "$start_month\n";
            $post = $this->set_post(null,$start_month);
            if($branch_id != ''){
                $post['args'] += array('trans_sales.branch_code'=>$branch_id);
            }
            unset($post['args']['terminal']);
            unset($post['args']['trans_sales.terminal_id']);
            // echo "<pre>",print_r($post['args']),"</pre>";die();
            $trans = $this->trans_sales($post['args']);
            $sales = $trans['sales'];

            // if($start_month == '2018-01-12'){
            //     echo "<pre>",print_r($post),"</pre>";die();
            //     echo $this->db->last_query();die();
            // }

            $trans_menus = $this->menu_sales($sales['settled']['ids'],false,$branch_id);
            
            // }else{
            //   continue;
            // }

            $trans_charges = $this->charges_sales($sales['settled']['ids'],false,$branch_id);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],false, $branch_id);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],false, $branch_id);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],false, $branch_id);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],false, $branch_id);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],false, $branch_id);
            // $payments = $this->payment_sales($sales['settled']['ids']);
            $gross = $trans_menus['gross'];
            // echo "<pre>",print_r($trans_discounts),"</pre>";die();
            
            $net = $trans['net'];
            $void = $trans['void'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            if($less_vat < 0)
                $less_vat = 0;
            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;
            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            $nontaxable = $no_tax - $no_tax_disc;

            //                 if($start_month == '2018-01-12'){

            //                     echo "gross: ".$gross ." , discounts: ".$discounts . ",less_vat ".$less_vat.", nontaxable".$nontaxable;

            // echo "<pre>",print_r($sales['settled']['ids']),"</pre>";
            //                     die();
            //                 }
            
            $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;
            $net_sales = $gross + $charges - $discounts - $less_vat;
            // $final_gross = $gross;
            $vat_ = $taxable * .12;
            $gt = $this->old_grand_net_total($start_month,false,$branch_id);
            $types = $trans_discounts['types'];
            // $qty = 0;
            $sndisc = 0;
            $pwdisc = 0;
            $othdisc = 0;
            foreach ($types as $code => $val) {
                $amount = $val['amount'];
                if($code == 'PWDISC'){
                    // $amount = $val['amount'] / 1.12;
                    $pwdisc = $val['amount'];
                }elseif($code == 'SNDISC'){
                    $sndisc = $val['amount'];
                }else{
                    $othdisc = $val['amount'];
                }
                // $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                      .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $qty += $val['qty'];
            }
            // echo $pwdisc; die();
            $month_array[$start_month] = array(
                'cr_beg'=>iSetObj($trans['first_ref'],'trans_ref'),
                'cr_end'=>iSetObj($trans['last_ref'],'trans_ref'),
                'cr_count'=>$trans['ref_count'],
                'beg'=>$gt['old_grand_total'],
                'new'=>$gt['old_grand_total']+$net_no_adds,
                'ctr'=>$gt['ctr'],
                'vatsales'=>$taxable,
                'vatex'=>$nontaxable,
                'zero_rated'=>$zero_rated,
                'vat'=>$vat_,
                'net_sales'=>$net_sales,
                'pwdisc'=>$pwdisc,
                'sndisc'=>$sndisc,
                'othdisc'=>$othdisc,
                'lessvat'=>$less_vat,
                'gross'=>$gross,
                // 'senior'=>
            );
            $load += 2;
            update_load($load);
            sleep(1);
            $start_month = date("Y-m-d", strtotime("+1 day", strtotime($start_month)));
        }
        // echo "<pre>",print_r($month_array),"</pre>";die();
        // var_dump($month_array);
        // die();
        // $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
        // $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
        update_load(75);
        sleep(2);
        // $this->session->set_userdata('month_array',$month_array);
        // $this->session->set_userdata('month_date',$month_date);
        // //diretso excel na
        // $this->load->library('Excel');
        // $sheet = $this->excel->getActiveSheet();
        // $sheet->getCell('A1')->setValue('Point One Integrated Solutions Inc.');
        update_load(100);
        // ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename=monthly_sales.xls"');
        // header('Cache-Control: max-age=0');
        // $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');

        $this->load->library('Excel');
        // $month_array = $this->session->userData('month_array');
        // $month_date = $this->session->userData('month_date');
        $sheet = $this->excel->getActiveSheet();
        $branch_details = $this->setup_model->get_branch_details($branch_id);
        $branch = array();
        foreach ($branch_details as $bv) {
            $branch = array(
                'id' => $bv->branch_id,
                'res_id' => $bv->res_id,
                'branch_code' => $bv->branch_code,
                'name' => $bv->branch_name,
                'branch_desc' => $bv->branch_desc,
                'contact_no' => $bv->contact_no,
                'delivery_no' => $bv->delivery_no,
                'address' => $bv->address,
                'base_location' => $bv->base_location,
                'currency' => $bv->currency,
                'inactive' => $bv->inactive,
                'tin' => $bv->tin,
                'machine_no' => $bv->machine_no,
                'bir' => $bv->bir,
                'permit_no' => $bv->permit_no,
                'serial' => $bv->serial,
                'accrdn' => $bv->accrdn,
                'email' => $bv->email,
                'website' => $bv->website,
                'store_open' => $bv->store_open,
                'store_close' => $bv->store_close,
            );
        }
        $content = "
        <table width=\"100%\" cellpadding=\"1px\" border=\"0px\">
            <tr>
                <td><font size=8.5>Company Name: <b>".COMPANY_NAME."</b></font></td>
            </tr>
            <tr>
                <td><font size=8.5>Address: <b>".COMPANY_ADDRESS."</b></font></td>
            </tr>
            <tr>
                <td><font size=8.5>TIN #: <b>".TIN."</b></font></td>
            </tr>
            <tr>
                <td><font size=8.5>ACCRDN #: <b>".ACCRDN."</b></font></td>
            </tr>
            <tr>
                <td><font size=8.5>PERMIT #: <b>".PERMIT."</b></font></td>
            </tr>
            <tr>
                <td><font size=8.5>SN #: <b>".SN."</b></font></td>
            </tr>
            <tr>
                <td><font size=8.5>MIN #: <b>".MIN."</b></font></td>
            </tr>
            <tr>
                <td><font size=8.5><b>MONTHLY SALES REPORT</b></font></td>
            </tr>
            <tr>
                <td><font size=8.5><b>Date: ".date("m/d/01",strtotime("-1 month"))." to ".date("m/d/t",strtotime("-1 month"))."</b></font></td>
            </tr>        
        ";

        $content .= "<tr>
                        <td><font size=8.5><b>Day</b></font></td>
                        <td><font size=8.5><b>Accumulating OR</b></font></td>
                        <td><font size=8.5><b>Accumulating OR</b></font></td>
                     </tr>";

        $content .= "</table>";

        $sheet->mergeCells('A1:Q1');
        $sheet->mergeCells('A2:Q2');
        $sheet->mergeCells('A3:Q3');
        $sheet->mergeCells('A4:Q4');
        $sheet->mergeCells('A5:Q5');
        $sheet->mergeCells('A6:Q6');
        $sheet->mergeCells('A7:Q7');
        $sheet->mergeCells('A8:Q8');
        $sheet->mergeCells('A9:Q9');
        // $sheet->getCell('A1')->setValue($branch['name']);
        // $sheet->getCell('A2')->setValue($branch['address']);
        // $sheet->setCellValueExplicit('A3', 'TIN #'.$branch['tin'], PHPExcel_Cell_DataType::TYPE_STRING);
        // $sheet->setCellValueExplicit('A4', 'ACCRDN #'.$branch['accrdn'], PHPExcel_Cell_DataType::TYPE_STRING);
        // $sheet->setCellValueExplicit('A5', 'PERMIT #'.$branch['permit_no'], PHPExcel_Cell_DataType::TYPE_STRING);
        // $sheet->setCellValueExplicit('A6', 'SN #'.$branch['serial'], PHPExcel_Cell_DataType::TYPE_STRING);
        // // $sheet->getCell('A7')->setValue($branch['machine_no']);
        // $sheet->setCellValueExplicit('A7', 'MIN #'.$branch['machine_no'], PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getCell('A8')->setValue('Monthly Sales Report');
        $sheet->getCell('A9')->setValue($month_date['text']);
        $rn = 10;
        $sheet->mergeCells('A10:A11');
        $sheet->getCell('A'.$rn)->setValue('Day');
        $sheet->mergeCells('B'.$rn.':D'.$rn);
        $sheet->getCell('B'.$rn)->setValue('Accumulating OR');
        $sheet->getCell('B11')->setValue('Beg');
        $sheet->getCell('C11')->setValue('End');
        $sheet->getCell('D11')->setValue('Total');
        $sheet->mergeCells('E'.$rn.':G'.$rn);
        $sheet->getCell('E'.$rn)->setValue('Accumulating Sales');
        $sheet->getCell('E11')->setValue('Beg');
        $sheet->getCell('F11')->setValue('End');
        $sheet->getCell('G11')->setValue('Total');
        $sheet->mergeCells('H10:H11');
        $sheet->getCell('H'.$rn)->setValue('Z-Read Counter');
        $sheet->mergeCells('I10:I11');
        $sheet->getCell('I'.$rn)->setValue('VATable Sales');
        $sheet->mergeCells('J10:J11');
        $sheet->getCell('J'.$rn)->setValue('VAT-Exempt Sales');
        $sheet->mergeCells('K10:K11');
        $sheet->getCell('K'.$rn)->setValue('Zero Rated Sales');
        $sheet->mergeCells('L10:L11');
        $sheet->getCell('L'.$rn)->setValue('VAT 12%');
        $sheet->mergeCells('M'.$rn.':P'.$rn);
        $sheet->getCell('M'.$rn)->setValue('Discount');
        $sheet->getCell('M11')->setValue('Senior Citizen');
        $sheet->getCell('N11')->setValue('PWD');
        $sheet->getCell('O11')->setValue('VAT Disc');
        $sheet->getCell('P11')->setValue('Employee');
        $sheet->mergeCells('Q10:Q11');
        $sheet->getCell('Q'.$rn)->setValue('Net Sales');
        $sheet->getStyle("A".$rn.":Q11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$rn.':'.'Q11')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('A'.$rn.':'.'Q11')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle('A'.$rn.':'.'Q11')->getFill()->getStartColor()->setRGB('29bb04');
        $sheet->getStyle('A1:'.'Q11')->getFont()->setBold(true);
        $rn = 12;
        //     $month_array[$start_month] = array(
        //         'cr_beg'=>iSetObj($trans['first_ref'],'trans_ref'),
        //         'cr_end'=>iSetObj($trans['last_ref'],'trans_ref'),
        //         'cr_count'=>$trans['ref_count'],
        //         'beg'=>$gt['old_grand_total'],
        //         'new'=>$gt['old_grand_total']+$net_no_adds,
        //         'ctr'=>$gt['ctr'],
        //         'vatsales'=>$taxable,
        //         'vatex'=>$nontaxable,
        //         'zero_rated'=>$zero_rated,
        //         'vat'=>$vat_,
        //         'net_sales'=>$net_sales,
        //         'pwdisc'=>$pwdisc,
        //         'sndisc'=>$sndisc,
        //         'othdisc'=>$othdisc,
        //         'lessvat'=>$less_vat,
        //         // 'senior'=>
        //     );
        if($month_array){
            $vatsales_total = 0;
            $vatex_total = 0;
            $zero_rate_total = $vat_total = $net_sales_total = $pwdisc_total = $sndisc_total = $othdisc_total = $lessvat_total = 0;
            foreach($month_array as $date => $vals){
                $sheet->getCell('A'.$rn)->setValue($date);
                // $sheet->getCell('B'.$rn)->setValue($vals['cr_beg']);
                $sheet->setCellValueExplicit('B'.$rn, $vals['cr_beg'], PHPExcel_Cell_DataType::TYPE_STRING);
                // $sheet->getCell('C'.$rn)->setValue($vals['cr_end']);
                $sheet->setCellValueExplicit('C'.$rn, $vals['cr_end'], PHPExcel_Cell_DataType::TYPE_STRING);
                if($vals['vatsales']){
                    $sheet->getCell('D'.$rn)->setValue($vals['cr_count']);
                    $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('E'.$rn)->setValue($vals['beg']);
                    $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('F'.$rn)->setValue($vals['new']);
                    $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('G'.$rn)->setValue($vals['gross']);
                    $sheet->getCell('H'.$rn)->setValue($vals['ctr']);
                    $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('I'.$rn)->setValue($vals['vatsales']);
                    $sheet->getStyle('J'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('J'.$rn)->setValue($vals['vatex']);
                    $sheet->getStyle('K'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('K'.$rn)->setValue($vals['zero_rated']);
                    $sheet->getStyle('L'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('L'.$rn)->setValue($vals['vat']);
                    $sheet->getStyle('M'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('M'.$rn)->setValue($vals['sndisc']);
                    $sheet->getStyle('N'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('N'.$rn)->setValue($vals['pwdisc']);
                    $sheet->getStyle('O'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('O'.$rn)->setValue($vals['lessvat']);
                    $sheet->getStyle('P'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('P'.$rn)->setValue($vals['othdisc']);
                    $sheet->getStyle('Q'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('Q'.$rn)->setValue($vals['net_sales']);
                    $vatsales_total += $vals['vatsales'];
                    $vatex_total += $vals['vatex'];
                    $zero_rate_total += $vals['zero_rated'];
                    $vat_total += $vals['vat'];
                    $sndisc_total += $vals['sndisc'];
                    $pwdisc_total += $vals['pwdisc'];
                    $lessvat_total += $vals['lessvat'];
                    $othdisc_total += $vals['othdisc'];
                    $net_sales_total += $vals['net_sales'];
                }
                $rn++;
            }
            $sheet->getStyle('A'.$rn.':Q'.$rn)->getFont()->setBold(true);
            $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('I'.$rn)->setValue($vatsales_total);
            $sheet->getStyle('J'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('J'.$rn)->setValue($vatex_total);
            $sheet->getStyle('K'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('K'.$rn)->setValue($zero_rate_total);
            $sheet->getStyle('L'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('L'.$rn)->setValue($vat_total);
            $sheet->getStyle('M'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('M'.$rn)->setValue($sndisc_total);
            $sheet->getStyle('N'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('N'.$rn)->setValue($pwdisc_total);
            $sheet->getStyle('O'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('O'.$rn)->setValue($lessvat_total);
            $sheet->getStyle('P'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('P'.$rn)->setValue($othdisc_total);
            $sheet->getStyle('Q'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('Q'.$rn)->setValue($net_sales_total);
        }
        if (ob_get_contents()) 
            ob_end_clean();
        
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename='.date('F Y',strtotime($month_date['month_year'])).'.xls');
        // header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');        
        $filename = 'uploads/reports/monthly_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;
    }
    public function get_daily_reports($branch_id=""){
        // sess_clear('daily_array');
        // sess_clear('daily_date');
        $this->load->helper('dine/reports_helper');        
        $date = date("Y-m-d", strtotime("-1 day"));
        $this->load->helper('dine/reports_helper');            
        // $branch_id = $this->input->post('branch_id');
        // echo $branch_id;die();
        $json = $this->input->post('json');
        // $start_month = date('Y-'.$month.'-01');
        $date = date("Y-m-d", strtotime($date));
        $date = "2018-08-31";
        // $branch_id = 'VIAMARE-ALABANG';
        update_load(2);
        sleep(1);
        $load = 2;
                // echo $cmonth.'<br>';
        $month_array = array();
        $post = $this->set_post(null,$date);
        // if($branch_id){
            // $post['args'] = array('trans_sales.branch_code'=>$branch_id);
        // }
        $trans = $this->trans_sales($post['args'],false,$branch_id);
        // echo $this->cashier_model->db->last_query();die();
        $sales = $trans['sales'];
        $settled = $trans['sales']['settled']['orders'];
       // echo "<pre>",print_r($settled),"</pre>";die();
        usort($settled, function($a, $b) {
            return $a->trans_ref - $b->trans_ref;
        });
        $daily_array = array();
        // die();
        // echo "<pre>",print_r($settled),"</pre>";die();
        foreach ($settled as $key => $set){
            // $trans_menus = $this->menu_sales($sales_id,$curr);
            $trans_charges = $this->charges_sales($set->sales_id,false,$branch_id);
            $trans_discounts = $this->discounts_sales($set->sales_id,false,$branch_id);
            $trans_local_tax = $this->local_tax_sales($set->sales_id,false,$branch_id);
            $trans_tax = $this->tax_sales($set->sales_id,false,$branch_id);
            $trans_no_tax = $this->no_tax_sales($set->sales_id,false,$branch_id);
            $trans_zero_rated = $this->zero_rated_sales($set->sales_id,false,$branch_id);
            $trans_menus = $this->menu_sales($set->sales_id,false,$branch_id);
            $gross = $trans_menus['gross'];
            // echo $gross;die();
            // $print_str .= align_center($set->trans_ref,PAPER_WIDTH," ")."\r\n";
            // $print_str .= align_center($set->datetime,PAPER_WIDTH," ")."\r\n";
            $net = $set->total_amount;
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $local_tax = $trans_local_tax['total'];
            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;

            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            $nontaxable = $no_tax - $no_tax_disc;
            $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            // if($set->trans_ref == "00000002"){
            //     echo "zero_rated:".$zero_rated."<br>";
            //     echo "no_tax:".$no_tax."<br>";
            //     echo "no_tax_disc:".$no_tax_disc."<br>";
            //     die();
            // }
            if($taxable < 0){
                $taxable = 0;
            }
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;

            $types = $trans_discounts['types'];
            // echo var_dump($types);
            // $qty = 0;
            $sndisc = 0;
            $pwdisc = 0;
            $othdisc = 0; 
            $pwddisc_lss = 0;
            $sndisc_lss = 0;
            $no_tax_disc = 0;
            $snc_no_of_guest = 0;
            $pwd_no_of_guest = 0;
            foreach ($types as $code => $val) {
                // $amount = $val['amount'];
                if($code == 'PWDISC'){
                    // $amount = $val['amount'] / 1.12;
                    // $pwdisc = $gross - $set->total_amount;
                    $pwdisc = $val['amount'];
                    if($val['no_tax'] == 1)
                        $pwddisc_lss++;
                    // Modified by Rod
                    $pwd_no_of_guest += $val["qty"];                    
                    // End
                }elseif($code == 'SNDISC'){
                    $sndisc = $val['amount'];
                    $sndisc_lss++;
                    // Modified by Rod
                    $snc_no_of_guest += $val["qty"];                    
                    // End
                }else{
                    $othdisc = $val['amount'];
                }
                // $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                      .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $qty += $val['qty'];
                if($val['no_tax'] == 1)
                    $no_tax_disc += $val['amount'];

            }
            $no_no_tax = $pwddisc_lss+$sndisc_lss;
            $less_vat = $gross - $taxable - $no_tax - $tax;
            if($no_no_tax){
                $lv = $less_vat / $no_no_tax;
            }else{
                $lv = 0;
            }

            // if($set->sales_id == 9245){                    
            //     echo '<pre>', print_r($types), '</pre><br>';
            //     echo "no_tax_disc".$no_tax_disc."<br>";
            //     echo "tax".$tax."<br>";
            //     echo "lv".$less_vat."<br>";
            //     echo "no_no_tax".$no_no_tax."<br>";
            //     echo "sndisc".$sndisc."<br>";
            //     echo "sndisc_lss".$sndisc_lss."<br>";
            //     echo "pwdisc".$pwdisc."<br>";
            //     echo "pwddisc_lss".$pwddisc_lss."<br>";
            //     echo '<pre>', print_r($no_tax), '</pre>';
            //     echo "sndisc_w_vat:".$sndisc_w_vat;
            //     echo "pwdisc_w_vat:".$pwd_w_vat;
            //     echo "charges:".$charges;
            //     die();                    
            // }
            // New computation of Senior and PWD approved by Sir Rei
            // Added by Rod
            $sndisc_w_vat = 0;
            $pwd_w_vat = 0;
            $total_no_of_guest = $snc_no_of_guest + $pwd_no_of_guest;
            if($snc_no_of_guest > 0){
                $sndisc_w_vat = $sndisc + (($less_vat/$total_no_of_guest) * $snc_no_of_guest);
            }
            if($pwd_no_of_guest > 0){
                $pwd_w_vat = $pwdisc + (($less_vat/$total_no_of_guest) * $pwd_no_of_guest);                    
            }
            // End

            // $no_tax -= $no_tax_disc; // Comment by Rod - Follow the iPOS computation

            // $print_str .= append_chars(substrwords('VAT SALES',18,""),"right",23," ")
            //              .append_chars(numInt(($taxable)),"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('VAT SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                      .append_chars(numInt($taxable),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $total_net = $taxable + $no_tax + $zero_rated + $tax;
            // $print_str .= append_chars(substrwords('VAT EXEMPT SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //              .append_chars(numInt(($no_tax)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords('ZERO RATED',13,""),"right",PAPER_TOTAL_COL_1," ")
            //              .append_chars(numInt(($zero_rated)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords('VAT',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(numInt(($tax)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars("","right",23," ").append_chars("----------","left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(numInt(($total_net)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords('Charges',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(numInt(($charges)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(numInt(($local_tax)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords('Discounts',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(numInt(($discounts)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= PAPER_LINE."\r\n";
            // $print_str .= append_chars(substrwords('NET SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //              .append_chars(numInt(($set->total_amount)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $daily_array[] = array(
                'branch_code'=>$branch,
                'or_number'=>$set->trans_ref,
                'vatable'=>$taxable,
                'vatex'=>$no_tax,
                'zero_rated'=>$zero_rated,
                'vat'=>$tax,
                'gross'=>$gross,
                // 'sndisc'=>$sndisc + ($lv * $sndisc_lss), // Comment by Rod
                // 'pwdisc'=>$pwdisc + ($lv * $pwddisc_lss), // Comment by Rod
                'sndisc'=>$sndisc_w_vat, // New computation ordered by Sir Rei
                'pwdisc'=>$pwd_w_vat, // New computation ordered by Sir Rei
                'charges'=>$charges, // Add Net of Service Charge and Service Charge
                'othdisc'=>$othdisc,
                'netsales'=>$set->total_amount,
            );
            if($load == 74){
                update_load($load);
                sleep(1);
            }else{
                $load += 1;
                update_load($load);
                sleep(1);
            }
        }
        
        update_load(75);
        sleep(2);
        $this->session->set_userdata('daily_array',$daily_array);
        $this->session->set_userdata('daily_date',$date);
        $this->session->set_userdata('branch_id',$branch_id);
        // //diretso excel na
        // $this->load->library('Excel');
        // $sheet = $this->excel->getActiveSheet();
        // $sheet->getCell('A1')->setValue('Point One Integrated Solutions Inc.');
        update_load(100);
        // ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename=monthly_sales.xls"');
        // header('Cache-Control: max-age=0');
        // $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        
        $this->load->library('Excel');
        // $daily_array = $this->session->userData('daily_array');
        // $daily_date = $this->session->userData('daily_date');
        // $branch_id = $this->session->userData('branch_id');
        // echo $branch_id;die();
        $sheet = $this->excel->getActiveSheet();
        $branch_details = $this->setup_model->get_branch_details($branch_id);
        $branch = array();
        foreach ($branch_details as $bv) {
            $branch = array(
                'id' => $bv->branch_id,
                'res_id' => $bv->res_id,
                'branch_code' => $bv->branch_code,
                'name' => $bv->branch_name,
                'branch_desc' => $bv->branch_desc,
                'contact_no' => $bv->contact_no,
                'delivery_no' => $bv->delivery_no,
                'address' => $bv->address,
                'base_location' => $bv->base_location,
                'currency' => $bv->currency,
                'inactive' => $bv->inactive,
                'tin' => $bv->tin,
                'machine_no' => $bv->machine_no,
                'bir' => $bv->bir,
                'permit_no' => $bv->permit_no,
                'serial' => $bv->serial,
                'accrdn' => $bv->accrdn,
                'email' => $bv->email,
                'website' => $bv->website,
                'store_open' => $bv->store_open,
                'store_close' => $bv->store_close,
            );
        }
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        $sheet->mergeCells('A4:J4');
        $sheet->mergeCells('A5:J5');
        $sheet->mergeCells('A6:J6');
        $sheet->mergeCells('A7:J7');
        $sheet->mergeCells('A8:J8');
        $sheet->mergeCells('A9:J9');
        $sheet->getCell('A1')->setValue(COMPANY_NAME);
        $sheet->getCell('A2')->setValue(COMPANY_ADDRESS);
        $sheet->setCellValueExplicit('A3', 'TIN #'.TIN, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('A4', 'ACCRDN #'.ACCRDN, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('A5', 'PERMIT #'.PERMIT, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('A6', 'SN #'.SN, PHPExcel_Cell_DataType::TYPE_STRING);
        // $sheet->getCell('A7')->setValue($branch['machine_no']);
        $sheet->setCellValueExplicit('A7', 'MIN #'.MIN, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getCell('A8')->setValue('Daily Sales Report');
        $sheet->getCell('A9')->setValue(sql2Date($date));
        $rn = 10;
        $sheet->mergeCells('A10:A11');
        $sheet->getCell('A'.$rn)->setValue('Branch');
        $sheet->mergeCells('B10:B11');
        $sheet->getCell('B'.$rn)->setValue('OR Number');
        $sheet->mergeCells('C10:C11');
        $sheet->getCell('C'.$rn)->setValue('Total Sales');
        $sheet->mergeCells('D10:D11');
        $sheet->getCell('D'.$rn)->setValue('VATable Sales');
        $sheet->mergeCells('E10:E11');
        $sheet->getCell('E'.$rn)->setValue('VAT-Exempt Sales');
        $sheet->mergeCells('F10:F11');
        $sheet->getCell('F'.$rn)->setValue('Zero Rated Sales');
        $sheet->mergeCells('G10:G11');
        $sheet->getCell('G'.$rn)->setValue('VAT');
        $sheet->mergeCells('H'.$rn.':J'.$rn);
        $sheet->getCell('H'.$rn)->setValue('Discount');
        $sheet->getCell('H11')->setValue('Senior Citizen');
        $sheet->getCell('I11')->setValue('PWD');
        $sheet->getCell('J11')->setValue('Regular');
        $sheet->mergeCells('K10:K11');
        $sheet->getCell('K'.$rn)->setValue('Service Charge');
        $sheet->mergeCells('L10:L11');
        $sheet->getCell('L'.$rn)->setValue('Net of Service Charge');
        $sheet->mergeCells('M10:M11');
        $sheet->getCell('M'.$rn)->setValue('Net Sales');
        $sheet->getStyle("A".$rn.":M11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$rn.':'.'M11')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('A'.$rn.':'.'M11')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle('A'.$rn.':'.'M11')->getFill()->getStartColor()->setRGB('29bb04');
        $sheet->getStyle('A1:'.'M11')->getFont()->setBold(true);
        $rn = 12;
        // daily_array[] = array(
        //         'or_number'=>$set->trans_ref,
        //         'vatable'=>$taxable,
        //         'vatex'=>$no_tax,
        //         'vat'=>$tax,
        //         'gross'=>$gross,
        //         'sndisc'=>$sndisc,
        //         'pwdisc'=>$pwdisc,
        //         'othdisc'=>$othdisc,
        //         'netsales'=>$set->total_amount,
        //     );
        if($daily_array){
            $vatsales_total = 0;
            $vatex_total = 0;
            $zero_rate_total = $vat_total = $net_sales_total = $pwdisc_total = $sndisc_total = $othdisc_total = $gross_total = 0;
            $total_service_charges = $net_of_service_charge = 0;
            // echo "<pre>",print_r($daily_array),"</pre>";die();
            foreach($daily_array as $date => $vals){
                // $sheet->getCell('A'.$rn)->setValue($vals['or_number']);
                // if($branch_id !=""){   
                $sheet->setCellValueExplicit('A'.$rn, $vals['branch_code'], PHPExcel_Cell_DataType::TYPE_STRING);
                // }
                
                $sheet->setCellValueExplicit('B'.$rn, $vals['or_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                // $sheet->getCell('B'.$rn)->setValue($vals['cr_beg']);
                $sheet->getStyle('C'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('C'.$rn)->setValue($vals['gross']);
                $sheet->getStyle('D'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('D'.$rn)->setValue($vals['vatable']);
                $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('E'.$rn)->setValue($vals['vatex']);
                $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('F'.$rn)->setValue($vals['zero_rated']);
                $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('G'.$rn)->setValue($vals['vat']);
                $sheet->getStyle('H'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('H'.$rn)->setValue($vals['sndisc']);
                $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('I'.$rn)->setValue($vals['pwdisc']);
                $sheet->getStyle('J'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('J'.$rn)->setValue($vals['othdisc']);
                $sheet->getStyle('K'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('K'.$rn)->setValue($vals['charges']);
                $sheet->getStyle('L'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('L'.$rn)->setValue($vals['netsales']-$vals['charges']);
                $sheet->getStyle('M'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('M'.$rn)->setValue($vals['netsales']);
                // $sheet->getCell('C'.$rn)->setValue($vals['cr_end']);
                // $sheet->setCellValueExplicit('C'.$rn, $vals['cr_end'], PHPExcel_Cell_DataType::TYPE_STRING);
                // if($vals['vatsales']){
                //     $sheet->getCell('D'.$rn)->setValue($vals['cr_count']);
                //     $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('E'.$rn)->setValue($vals['beg']);
                //     $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('F'.$rn)->setValue($vals['new']);
                //     $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('G'.$rn)->setValue($vals['gross']);
                //     $sheet->getCell('H'.$rn)->setValue($vals['ctr']);
                //     $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('I'.$rn)->setValue($vals['vatsales']);
                //     $sheet->getStyle('J'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('J'.$rn)->setValue($vals['vatex']);
                //     $sheet->getStyle('K'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('K'.$rn)->setValue($vals['zero_rated']);
                //     $sheet->getStyle('L'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('L'.$rn)->setValue($vals['vat']);
                //     $sheet->getStyle('M'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('M'.$rn)->setValue($vals['sndisc']);
                //     $sheet->getStyle('N'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('N'.$rn)->setValue($vals['pwdisc']);
                //     $sheet->getStyle('O'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('O'.$rn)->setValue($vals['lessvat']);
                //     $sheet->getStyle('P'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('P'.$rn)->setValue($vals['othdisc']);
                //     $sheet->getStyle('Q'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                //     $sheet->getCell('Q'.$rn)->setValue($vals['net_sales']);
                    $vatsales_total += $vals['vatable'];
                    $vatex_total += $vals['vatex'];
                    $zero_rate_total += $vals['zero_rated'];
                    $vat_total += $vals['vat'];
                    $sndisc_total += $vals['sndisc'];
                    $pwdisc_total += $vals['pwdisc'];
                    $gross_total += $vals['gross'];
                    $othdisc_total += $vals['othdisc'];
                    $net_sales_total += $vals['netsales'];

                    $total_service_charges += $vals['charges'];
                    $net_of_service_charge += $vals['netsales'] - $vals['charges'];
                // }
                $rn++;
            }
            $sheet->getStyle('A'.$rn.':M'.$rn)->getFont()->setBold(true);
            $sheet->getStyle('C'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('C'.$rn)->setValue($gross_total);
            $sheet->getStyle('D'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('D'.$rn)->setValue($vatsales_total);
            $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('E'.$rn)->setValue($vatex_total);
            $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('F'.$rn)->setValue($zero_rate_total);
            $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('G'.$rn)->setValue($vat_total);
            $sheet->getStyle('H'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('H'.$rn)->setValue($sndisc_total);
            $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('I'.$rn)->setValue($pwdisc_total);
            $sheet->getStyle('J'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('J'.$rn)->setValue($othdisc_total);
            $sheet->getStyle('K'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('K'.$rn)->setValue($total_service_charges);
            $sheet->getStyle('L'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('L'.$rn)->setValue($net_of_service_charge);
            $sheet->getStyle('M'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('M'.$rn)->setValue($net_sales_total);
        }
        if (ob_get_contents()) 
            ob_end_clean();

        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename=daily_report.xls');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/'.$branch_id.'_daily_report_'.date("Ymd", strtotime("-1 day")).'.xls';
        $objWriter->save("../".$filename);
        return $filename;
    }
    public function check_hourly_sales_excel_old(){
        $this->load->model('dine/email_model');
        $this->load->library('Excel');
        // $sheet = $this->excel->getActiveSheet();
        $filename = 'Hourly Sales Report';
        $get_branches = $this->email_model->get_all_branches();
        // echo "<pre>",print_r($get_branches),"</pre>";die();
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
            
        // $date = $_GET['calendar_range'];        

        // $datesx = explode(" to ",$date);
        // $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[0])));
        // $date_to = (empty($dates[1]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[1])));
        // $date_from = $datesx[0];
        // $date_to = $datesx[1];
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d 23:59:59");

        update_load(10);
        sleep(1);

        $args = array();

        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);
        foreach($get_branches as $branch){
            $branch_code = $branch->branch_code;
            $branch_id = $branch->branch_id;
            // echo $branch_code;die();
            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.datetime between '".date('Y-m-d H:i:s',strtotime($date_from))."' and '".date('Y-m-d H:i:s',strtotime($date_to))."'"] = array('use'=>'where','val'=>null,'third'=>false);
             $args["trans_sales.branch_code"] = array('use'=>'where','val'=>$branch_code,'third'=>true);
            $post = $this->set_post($_GET['calendar_range']);
            $curr = true;
            $trans = $this->trans_sales($args,$curr);
         
            ${'sales'.$branch_id} = $trans['sales'];
        }

        update_load(20);
        sleep(1);

        $ranges = array();
        foreach (unserialize(TIMERANGES) as $ctr => $time) {
            $key = date('H',strtotime($time['FTIME']));
            $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0);
            // $ranges[$key] = array();
        }

        update_load(30);
        sleep(1);

        // echo "<pre>",print_r($ranges),"</pre>";
        // die();
        foreach($get_branches as $branch){
            $branch_id = $branch->branch_id;
            ${'dates'.$branch_id} = array();

            if(count(${'sales'.$branch_id}['settled']['orders']) > 0){
                foreach (${'sales'.$branch_id}['settled']['orders'] as $sales_id => $val) {
                     ${'dates'.$branch_id}[date2Sql($val->datetime)]['ranges'] = $ranges;
                }
                foreach (${'sales'.$branch_id}['settled']['orders'] as $sales_id => $val) {
                    if(isset( ${'dates'.$branch_id}[date2Sql($val->datetime)])){
                        $date_arr =  ${'dates'.$branch_id}[date2Sql($val->datetime)];
                        $range = $date_arr['ranges'];
                        $H = date('H',strtotime($val->datetime));
                        if(isset($range[$H])){
                            $r = $range[$H];
                            $r['tc'] += 1;
                            $r['net'] += $val->total_amount;
                            $range[$H] = $r;
                        }
                        ${'dates'.$branch_id}[date2Sql($val->datetime)]['ranges'] = $range;
                    }
                }
            }
        }

        update_load(60);
        sleep(1);        

        $i=0;
         foreach($get_branches as $branch){ 
           $branch_code = $branch->branch_code;
            $branch_id = $branch->branch_id;
             $rc=1;
             $this->excel->createSheet($i); 
             $this->excel->setActiveSheetIndex($i);
             $sheet = $this->excel->getActiveSheet();
             $sheet->setTitle($branch_code);
             $styleHeaderCell = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '3C8DBC')
                ),
                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'font' => array(
                    'bold' => true,
                    'size' => 14,
                    'color' => array('rgb' => 'FFFFFF'),
                )
            );
            $styleNum = array(
                'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
            );
            $styleTxt = array(
                'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
            );
            $styleCenter = array(
                'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );
            $styleTitle = array(
                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'font' => array(
                    'bold' => true,
                    'size' => 16,
                )
            );
            
            $headers = array('Time','Total Count','Net','Average');
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            // $sheet->getColumnDimension('E')->setWidth(20);
            // $sheet->getColumnDimension('F')->setWidth(20);
            // $sheet->getColumnDimension('G')->setWidth(20);


            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Hourly Sales Report');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
            $rc++;
            
            // $dates = explode(" to ",$_GET['date']);
            $from = sql2DateTime($date_from);
            $to = sql2DateTime($date_to);
            $sheet->getCell('A'.$rc)->setValue('Date From: '.$from);
            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $rc++;

            $sheet->getCell('A'.$rc)->setValue('Date To: '.$to);
            $sheet->mergeCells('A'.$rc.':G'.$rc);
            $rc++;
            $col = 'A';
            foreach ($headers as $txt) {
                $sheet->getCell($col.$rc)->setValue($txt);
                $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
                $col++;
            }

            $rc++;
            $ctr = 0;
            $gtavg = $gtctr = $gtnet = 0;
            update_load(80);
            sleep(1);
            foreach(${'dates'.$branch_id} as $key1 => $v1){
                $sheet->getCell('A'.$rc)->setValue(sql2Date($key1));
                $sheet->mergeCells('A'.$rc.':D'.$rc);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleCenter);
                $rc++;

                $ranges = $v1['ranges'];
                //$txt .= sql2Date($key1);
                $tavg = 0;
                $tctr = 0;
                $tnet = 0;
                foreach($ranges as $key2 => $ran){
                    if($ran['tc'] == 0 || $ran['net'] == 0)
                        $avg = 0;
                    else
                        $avg = $ran['net']/$ran['tc'];
                    $ctr += $ran['tc'];

                    $sheet->getCell('A'.$rc)->setValue($ran['start']."-".$ran['end']);
                    $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                    $sheet->getCell('B'.$rc)->setValue($ran['tc']);
                    $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);
                    $sheet->getCell('C'.$rc)->setValue(numInt($ran['net']));
                    $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                    $sheet->getCell('D'.$rc)->setValue(numInt($avg));     
                    $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);

                    // $this->make->sRow();
                    //     $this->make->th($ran['start']."-".$ran['end']);
                    //     $this->make->th($ran['tc']);
                    //     $this->make->th(numInt($ran['net']));
                    //     $this->make->th(numInt($avg));
                    // $this->make->eRow();
                    $tctr += $ran['tc'];
                    $tnet += $ran['net'];
                    // $tavg += $avg;
                    $rc++;
                    // if($ctr == 0 || $ran['net'] == 0)
                    //     $tavg = 0;
                    // else
                    //     $tavg += $ran['net']/$ctr;

                }
                
                $gtctr += $tctr;
                $gtnet += $tnet;

            }

            if(empty($gtctr) && $gtctr == '0'){
                $gtavg = 0;
            }else{
                $gtavg = $gtnet/$gtctr;
            }
            $sheet->getCell('A'.$rc)->setValue('TOTAL');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($gtctr);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);
            $sheet->getCell('C'.$rc)->setValue(numInt($gtnet));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('D'.$rc)->setValue(numInt($gtavg));     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            $i++;
        }
        // foreach ($menus as $res) {
        //         $sheet->getCell('A'.$rc)->setValue($res['code']);
        //         $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('B'.$rc)->setValue($res['name']);
        //         $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('C'.$rc)->setValue($res['sell_price']);
        //         $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('D'.$rc)->setValue($res['qty']);     
        //         $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('E'.$rc)->setValue(num( ($res['qty'] / $total_qty) * 100 ).'%');     
        //         $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('F'.$rc)->setValue(num($res['amount']));     
        //         $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('G'.$rc)->setValue(num( ($res['amount'] / $menu_total) * 100 ).'%');
        //         $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);

        //     $rc++;
        // } 
        // $rc++;

        
        update_load(100);
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/hourly_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;
    }

    public function check_hourly_sales_excel(){
        $this->load->model('dine/email_model');
        $this->load->model('dine/download_model');
        $this->load->library('Excel');
        
        $filename = 'Hourly Sales Report';
        $get_branches = $this->email_model->get_all_branches();
       
        #GET VALUES
        start_load(0);
         
        // $date_from = date("Y-11-26");
        $date_from = date("Y-m-d");
        // $date_to = date("Y-m-d 23:59:59");
        $date_to = date("Y-m-d H:i:s");

        update_load(10);
        sleep(1);

        $args = array();

        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);
    
        foreach($get_branches as $branch){
            $branch_code = $branch->branch_code;
            $branch_id = $branch->branch_id;
            // echo $branch_code;die();
            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.datetime between '".date('Y-m-d H:i:s',strtotime($date_from))."' and '".date('Y-m-d H:i:s',strtotime($date_to))."'"] = array('use'=>'where','val'=>null,'third'=>false);
             $args["trans_sales.branch_code"] = array('use'=>'where','val'=>$branch_code,'third'=>true);
            $post = $this->set_post($_GET['calendar_range']);
            $curr = true;
            $trans = $this->trans_sales($args,$curr);
            // echo $this->db->last_query()
            // echo "<pre>",print_r($trans),"</pre>";die();
            ${'sales'.$branch_id} = $trans['sales'];
        }

        update_load(20);
        sleep(1);

        $ranges = array();
        foreach (unserialize(TIMERANGES) as $ctr => $time) {
            $key = date('H',strtotime($time['FTIME']));
            $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0);
        }

        update_load(30);
        sleep(1);

        // echo "<pre>",print_r($ranges),"</pre>";
        // die();
        foreach($get_branches as $branch){
            $branch_id = $branch->branch_id;
            ${'dates'.$branch_id} = array();

            if(count(${'sales'.$branch_id}['settled']['orders']) > 0){
                foreach (${'sales'.$branch_id}['settled']['orders'] as $sales_id => $val) {
                     ${'dates'.$branch_id}[date2Sql($val->datetime)]['ranges'] = $ranges;
                }
                foreach (${'sales'.$branch_id}['settled']['orders'] as $sales_id => $val) {
                    if(isset( ${'dates'.$branch_id}[date2Sql($val->datetime)])){
                        $date_arr =  ${'dates'.$branch_id}[date2Sql($val->datetime)];
                        $range = $date_arr['ranges'];
                        $H = date('H',strtotime($val->datetime));
                        if(isset($range[$H])){
                            $r = $range[$H];
                            $r['tc'] += 1;
                            $r['net'] += $val->total_amount;
                            $range[$H] = $r;
                        }
                        ${'dates'.$branch_id}[date2Sql($val->datetime)]['ranges'] = $range;
                    }
                }
            }
        }

        update_load(60);
        sleep(1);        

        $i=0;
        $rc=1;

        $this->excel->createSheet($i); 
         $this->excel->setActiveSheetIndex($i);
         $sheet = $this->excel->getActiveSheet();
         $sheet->setTitle('Hourly Sales');
         $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 14,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleFooterCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),                
            'font' => array(
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleCenter = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 16,
            )
        );
        
        $headers = array('Branch','Total');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':B'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Hourly Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;
        
        $from = sql2DateTime($date_from);
        $to = sql2DateTime($date_to);
        $sheet->getCell('A'.$rc)->setValue('Date From: '.$from);
        $sheet->mergeCells('A'.$rc.':B'.$rc);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue('Date To: '.$to);
        $sheet->mergeCells('A'.$rc.':B'.$rc);
        $rc++;
        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;

        $total = 0;
         foreach($get_branches as $branch){ 
           $branch_code = $branch->branch_code;
            $branch_id = $branch->branch_id;
            
            $ctr = 0;
            $gtctr = 0;
            
            update_load(80);
            sleep(1);
            foreach(${'dates'.$branch_id} as $key1 => $v1){
                $ranges = $v1['ranges'];
                
                $tavg = 0;
                $tctr = 0;
                $tnet = 0;
                foreach($ranges as $key2 => $ran){
                    if($ran['tc'] == 0 || $ran['net'] == 0)
                        $avg = 0;
                    else
                        $avg = $ran['net']/$ran['tc'];
                    $ctr += $ran['tc'];

                    
                    $tctr += $ran['tc'];
                    $tnet += $ran['net'];

                }
                
                $gtctr += $tctr;
            }

            $total += $tnet;

            $sheet->getCell('A'.$rc)->setValue($branch_code);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($tnet,2));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);

            $rc++;           
            
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleFooterCell);
        $sheet->getCell('B'.$rc)->setValue(num($total,2));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleFooterCell);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);
        
        update_load(100);
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');        

        $filename = 'uploads/reports/hourly_sales_report_'.date("Ymd").'.xls';

        $file = 'hourly_sales_report_'.date("Ymd").'.xls';

        $md5_file = md5_file("../".$filename);

        $objWriter->save("../".$filename);

        $this->download_model->add_file($file,$md5_file);
        
        // return $filename;

        return 'download/'.$md5_file;
    }

    public function trans_sales($args=array(),$curr=false,$branch_code=""){
        $this->load->model('dine/cashier_model');
        $n_results = array();
        // if($curr){
        //     $this->cashier_model->db = $this->load->database('default', TRUE);
        //     $n_results  = $this->cashier_model->get_trans_sales(null,$args);
        // }
        $this->cashier_model->db = $this->load->database('default', TRUE);

        $results = $this->cashier_model->get_trans_sales(null,$args,'',null,$branch_code);
        $orders = array();
        // if(count($n_results) > 0){
        //     foreach ($n_results as $nres) {
        //         if(!isset($orders[$nres->sales_id])){
        //             $orders[$nres->sales_id] = $nres;
        //         }
        //     }
        // }
        foreach ($results as $res) {
            if(!isset($orders[$res->sales_id])){
                $orders[$res->sales_id] = $res;
            }
        }
        $sales = array();
        $all_ids = array();
        $sales['void'] = array();
        $sales['cancel'] = array();
        $sales['settled'] = array();
        $sales['void']['ids'] = array();
        $sales['cancel']['ids'] = array();
        $sales['settled']['ids'] = array();
        $sales['void']['orders'] = array();
        $sales['cancel']['orders'] = array();
        $sales['settled']['orders'] = array();
        $net = 0;
        $void_amount = 0;
        $types = array();
        $ordsnums = array();
        $all_orders = array();
        foreach ($orders as $sales_id => $sale) {
            if($sale->type_id == 10){
                if($sale->trans_ref != "" && $sale->inactive == 0){
                    $sales['settled']['ids'][] = $sales_id;
                    // $net += $sale->total_amount;
                    $net += round($sale->total_amount,2);
                    $types[$sale->type][$sale->sales_id] = $sale;
                    $ordsnums[$sale->trans_ref] = $sale;
                    $sales['settled']['orders'][$sales_id] = $sale;
                }
                else if($sale->trans_ref == "" && $sale->inactive == 1){
                    $sales['cancel']['ids'][] = $sales_id;
                    $sales['cancel']['orders'][$sales_id] = $sale;
                }
            }
            else{
                $sales['void']['ids'][] = $sales_id;
                $sales['void']['orders'][$sales_id] = $sale;
                $void_amount += round($sale->total_amount,2);
            }
            $all_ids[] = $sales_id;
            $all_orders[$sales_id] = $sale;
        }
        ksort($ordsnums);
        $arr_ordsnums = array_slice($ordsnums, 0, 1);
        $first = array_shift($arr_ordsnums);
        $last = end($ordsnums);
        $ref_ctr = count($ordsnums);
        return array('all_ids'=>$all_ids,'all_orders'=>$all_orders,'sales'=>$sales,'net'=>$net,'void'=>$void_amount,'types'=>$types,'refs'=>$ordsnums,'first_ref'=>$first,'last_ref'=>$last,'ref_count'=>$ref_ctr);
    }
    public function set_post($set_range=null,$set_calendar=null){
        $this->load->model('dine/setup_model');
        $args = array();
        $from = "";
        $to = "";
        $date = "";
        $range = $this->input->post('calendar_range');
        $calendar = $this->input->post('calendar');
        $branch_id = $this->input->post('branch_id');

        if($set_range != null )
            $range = $set_range;
        if($set_calendar != null )
            $calendar = $set_calendar;
        // $range = '2015/10/28 7:00 AM to 2015/10/28 10:00 PM';
        // $calendar = '12/04/2015';
        $title = "";
        if($this->input->post('title'))
            $title = $this->input->post('title');
        // $title = 'ZREAD';
        
        if($branch_id){
            $args['trans_sales.branch_code'] = $branch_id;
        }


        if($range != ""){
            $daterange = $range;
            $dates = explode(" to ",$daterange);
            $from = date2SqlDateTime($dates[0]);
            $to = date2SqlDateTime($dates[1]);
            $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        }
        if($calendar != ""){
            $date = date2Sql($calendar);

            $details = $this->setup_model->get_branch_details($branch_id);
            
            $open_time = "00:00:00";
            $close_time = "00:00:00";
            if(isset($details[0])){
                $open_time = $details[0]->store_open;
                $close_time = $details[0]->store_close;                    
            }

            $from = date2SqlDateTime($date." ".$open_time);
            $oa = date('a',strtotime($open_time));
            $ca = date('a',strtotime($close_time));
            $to = date2SqlDateTime($date." ".$close_time);
            if($oa == $ca && $close_time < $open_time){
                $to = date('Y-m-d H:i:s',strtotime($to . "+1 days"));
            }

            //old jed
            // $rargs["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            // $select = "read_details.*";
            // $results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
            // // echo $this->site_model->db->last_query();
            // $args = array();
            // $from = "";
            // $to = "";
            // $datetimes = array();
            // foreach ($results as $res) {
            //     $datetimes[] = $res->scope_from;
            //     $datetimes[] = $res->scope_to;
            //     // break;
            // }
            // usort($datetimes, function($a, $b) {
            //   $ad = new DateTime($a);
            //   $bd = new DateTime($b);
            //   if ($ad == $bd) {
            //     return 0;
            //   }
            //   return $ad > $bd ? 1 : -1;
            // });
            // foreach ($datetimes as $dt) {
            //     $from = $dt;
            //     break;
            // }    
            // foreach ($datetimes as $dt) {
            //     $to = $dt;
            // }
            //end

            // echo $from."-".$to;
            // $rargs2["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            // $select = "read_details.*";
            // $results2 = $this->site_model->get_tbl('read_details',$rargs2,array('scope_to'=>'desc'),"",true,$select);
            // foreach ($results2 as $res) {
            //     $to = $res->scope_to;
            //     break;
            // }
            if($from != "" && $to != ""){
                $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            }
            else{
                $args["DATE(trans_sales.datetime) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
                $from = date('Y-m-d 00:00',strtotime($date));
                $to = date('Y-m-d 24:00',strtotime($date));
            }
        }
        $emp = "All";
        if($this->input->post('cashier')){
            $args['trans_sales.user_id'] = $this->input->post('cashier');
            $server = $this->manager_model->get_server_details($this->input->post('cashier'));
            $emp = $server[0]->fname." ".$server[0]->mname." ".$server[0]->lname." ".$server[0]->suffix;
        }
        $shift = $this->input->post('shift_id');
        // $shift = 1;
        if($shift != ""){
            $join['users'] = array('content'=>'shifts.user_id = users.id');
            $jargs['shifts.shift_id'] = $shift;
            $select = "shifts.*,users.fname,users.lname,users.mname,users.suffix,users.username";
            $results = $this->site_model->get_tbl('shifts',$jargs,array('check_in'=>'desc'),$join,true,$select);
            $res = $results[0];
            $server = $this->manager_model->get_server_details($res->user_id);
            $emp = $server[0]->fname." ".$server[0]->mname." ".$server[0]->lname." ".$server[0]->suffix;
            $from = date2SqlDateTime($res->check_in);
            if($res->check_out == ""){
                $today = $this->site_model->get_db_now();
                $to = date2SqlDateTime($today);
            }
            else
                $to = date2SqlDateTime($res->check_out);
            $args = array();
            $args['trans_sales.shift_id'] = $shift;
        }
        $terminal = TERMINAL_ID;
        $args['trans_sales.terminal_id'] = $terminal;
        return array('args'=>$args,'from'=>$from,'to'=>$to,'date'=>$date,'terminal'=>$terminal,"employee"=>$emp,"title"=>$title,"shift_id"=>$shift);
    }
    public function yearly_menus_rep_excel(){
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Menu Sales Report';
        $rc=1;
        #GET VALUES
            start_load(0);
            $date_from = date("Y-m-01", strtotime("-1 year"));
            $date_to = date("Y-m-t", strtotime("-1 year"));
            $calendar_range = $date_from." to ".$date_to;
            $post = $this->set_post($calendar_range,null);
            $curr = true;
            update_load(10);
            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            update_load(15);
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
            // echo "<pre>",print_r($post),"</pre>";die();
            update_load(20);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
            update_load(25);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
            update_load(30);
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
            update_load(35);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
            update_load(40);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
            update_load(45);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
            update_load(50);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr);
            update_load(53);
            $gross = $trans_menus['gross']; 
            $net = $trans['net'];
            $charges = $trans_charges['total']; 
            $discounts = $trans_discounts['total']; 
            $local_tax = $trans_local_tax['total']; 
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            if($less_vat < 0)
                $less_vat = 0;
            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;
            update_load(55);
            $cats = $trans_menus['cats'];                 
            $menus = $trans_menus['menus'];
            $menu_total = $trans_menus['menu_total'];
            $total_qty = $trans_menus['total_qty'];
            update_load(60);
            usort($menus, function($a, $b) {
                return $b['amount'] - $a['amount'];
            });
            update_load(80);
        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 14,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 16,
            )
        );
        
        $headers = array('Code','Name','Price (SRP)','QTY','QTY(AVG)','Amount','Amount(AVG)','Cost','Total Cost');
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Menu Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;
        
        $dates = explode(" to ",$_GET['calendar_range']);
        $from = sql2DateTime($dates[0]);
        $to = sql2DateTime($dates[1]);
        $sheet->getCell('A'.$rc)->setValue('Date From: '.$from);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue('Date To: '.$to);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;
        $branch_code = $_GET['branch_code'];
        $sheet->getCell('A'.$rc)->setValue('Branch: '.$branch_code);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;
        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }

        $rc++;
        // echo "<pre>",print_r($post),"</pre>";die();
        foreach ($menus as $res) {
                $sheet->getCell('A'.$rc)->setValue($res['code']);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($res['name']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue($res['sell_price']);
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('D'.$rc)->setValue($res['qty']);     
                $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('E'.$rc)->setValue(num( ($res['qty'] / $total_qty) * 100 ).'%');     
                $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('F'.$rc)->setValue(num($res['amount']));     
                $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('G'.$rc)->setValue(num( ($res['amount'] / $menu_total) * 100 ).'%');
                $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('H'.$rc)->setValue($res['cost_price']);
                $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('I'.$rc)->setValue($res['cost_price'] * $res['qty']);
                $sheet->getStyle('I'.$rc)->applyFromArray($styleNum);

            $rc++;
        } 
        $rc++;


        $mods_total = $trans_menus['mods_total'];
        if($mods_total > 0){
            $sheet->getCell('A'.$rc)->setValue('Total Modifiers Sale: ');
            $sheet->getCell('B'.$rc)->setValue(num($mods_total));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $rc++;
        }
        $net_no_adds = $net-$charges-$local_tax;
        $sheet->getCell('A'.$rc)->setValue('Total Sales: ');
        $sheet->getCell('B'.$rc)->setValue(num($net));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $txt = numInt(($charges));
        if($charges > 0)
            $txt = "(".numInt(($charges)).")";
        $sheet->getCell('A'.$rc)->setValue('Total Charges: ');
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $txt = numInt(($local_tax));
        if($local_tax > 0)
            $txt = "(".numInt(($local_tax)).")";
        $sheet->getCell('A'.$rc)->setValue('Total Local Tax: ');
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total Discounts: ');
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total VAT EXEMPT: ');
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total Gross Sales: ');
        $sheet->getCell('B'.$rc)->setValue(num($gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        
        update_load(100);
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/yearly_menu_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;
    }
    public function monthly_menus_rep_excel(){
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Menu Sales Report';
        $rc=1;
        #GET VALUES
            start_load(0);
            $date_from = date("Y-m-01", strtotime("-1 month"));
            $date_to = date("Y-m-t", strtotime("-1 month"));
            $calendar_range = $date_from." to ".$date_to;
            $post = $this->set_post($calendar_range,null);
            $curr = true;
            update_load(10);
            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            update_load(15);
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
            // echo "<pre>",print_r($post),"</pre>";die();
            update_load(20);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
            update_load(25);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
            update_load(30);
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
            update_load(35);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
            update_load(40);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
            update_load(45);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
            update_load(50);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr);
            update_load(53);
            $gross = $trans_menus['gross']; 
            $net = $trans['net'];
            $charges = $trans_charges['total']; 
            $discounts = $trans_discounts['total']; 
            $local_tax = $trans_local_tax['total']; 
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            if($less_vat < 0)
                $less_vat = 0;
            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;
            update_load(55);
            $cats = $trans_menus['cats'];                 
            $menus = $trans_menus['menus'];
            $menu_total = $trans_menus['menu_total'];
            $total_qty = $trans_menus['total_qty'];
            update_load(60);
            usort($menus, function($a, $b) {
                return $b['amount'] - $a['amount'];
            });
            update_load(80);
        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 14,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 16,
            )
        );
        
        $headers = array('Code','Name','Price (SRP)','QTY','QTY(AVG)','Amount','Amount(AVG)','Cost','Total Cost');
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Menu Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;
        
        $dates = explode(" to ",$_GET['calendar_range']);
        $from = sql2DateTime($dates[0]);
        $to = sql2DateTime($dates[1]);
        $sheet->getCell('A'.$rc)->setValue('Date From: '.$from);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue('Date To: '.$to);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;
        $branch_code = $_GET['branch_code'];
        $sheet->getCell('A'.$rc)->setValue('Branch: '.$branch_code);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;
        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }

        $rc++;
        // echo "<pre>",print_r($post),"</pre>";die();
        foreach ($menus as $res) {
                $sheet->getCell('A'.$rc)->setValue($res['code']);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($res['name']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue($res['sell_price']);
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('D'.$rc)->setValue($res['qty']);     
                $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('E'.$rc)->setValue(num( ($res['qty'] / $total_qty) * 100 ).'%');     
                $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('F'.$rc)->setValue(num($res['amount']));     
                $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('G'.$rc)->setValue(num( ($res['amount'] / $menu_total) * 100 ).'%');
                $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('H'.$rc)->setValue($res['cost_price']);
                $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('I'.$rc)->setValue($res['cost_price'] * $res['qty']);
                $sheet->getStyle('I'.$rc)->applyFromArray($styleNum);

            $rc++;
        } 
        $rc++;


        $mods_total = $trans_menus['mods_total'];
        if($mods_total > 0){
            $sheet->getCell('A'.$rc)->setValue('Total Modifiers Sale: ');
            $sheet->getCell('B'.$rc)->setValue(num($mods_total));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $rc++;
        }
        $net_no_adds = $net-$charges-$local_tax;
        $sheet->getCell('A'.$rc)->setValue('Total Sales: ');
        $sheet->getCell('B'.$rc)->setValue(num($net));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $txt = numInt(($charges));
        if($charges > 0)
            $txt = "(".numInt(($charges)).")";
        $sheet->getCell('A'.$rc)->setValue('Total Charges: ');
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $txt = numInt(($local_tax));
        if($local_tax > 0)
            $txt = "(".numInt(($local_tax)).")";
        $sheet->getCell('A'.$rc)->setValue('Total Local Tax: ');
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total Discounts: ');
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total VAT EXEMPT: ');
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total Gross Sales: ');
        $sheet->getCell('B'.$rc)->setValue(num($gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        
        update_load(100);
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/monthly_menu_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;
    }
    public function daily_menus_rep_excel(){
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Menu Sales Report';
        $rc=1;
        #GET VALUES
            start_load(0);
            $date_from = date("Y-m-01", strtotime("-1 day"));
            $date_to = date("Y-m-t", strtotime("-1 day"));
            $calendar_range = $date_from." to ".$date_to;
            $post = $this->set_post($calendar_range,null);
            $curr = true;
            update_load(10);
            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            update_load(15);
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
            // echo "<pre>",print_r($post),"</pre>";die();
            update_load(20);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
            update_load(25);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
            update_load(30);
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
            update_load(35);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
            update_load(40);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
            update_load(45);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
            update_load(50);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr);
            update_load(53);
            $gross = $trans_menus['gross']; 
            $net = $trans['net'];
            $charges = $trans_charges['total']; 
            $discounts = $trans_discounts['total']; 
            $local_tax = $trans_local_tax['total']; 
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            if($less_vat < 0)
                $less_vat = 0;
            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;
            update_load(55);
            $cats = $trans_menus['cats'];                 
            $menus = $trans_menus['menus'];
            $menu_total = $trans_menus['menu_total'];
            $total_qty = $trans_menus['total_qty'];
            update_load(60);
            usort($menus, function($a, $b) {
                return $b['amount'] - $a['amount'];
            });
            update_load(80);
        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 14,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 16,
            )
        );
        
        $headers = array('Code','Name','Price (SRP)','QTY','QTY(AVG)','Amount','Amount(AVG)','Cost','Total Cost');
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Menu Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;
        
        $dates = explode(" to ",$_GET['calendar_range']);
        $from = sql2DateTime($dates[0]);
        $to = sql2DateTime($dates[1]);
        $sheet->getCell('A'.$rc)->setValue('Date From: '.$from);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue('Date To: '.$to);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;
        $branch_code = $_GET['branch_code'];
        $sheet->getCell('A'.$rc)->setValue('Branch: '.$branch_code);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;
        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }

        $rc++;
        // echo "<pre>",print_r($post),"</pre>";die();
        foreach ($menus as $res) {
                $sheet->getCell('A'.$rc)->setValue($res['code']);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($res['name']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue($res['sell_price']);
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('D'.$rc)->setValue($res['qty']);     
                $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('E'.$rc)->setValue(num( ($res['qty'] / $total_qty) * 100 ).'%');     
                $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('F'.$rc)->setValue(num($res['amount']));     
                $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('G'.$rc)->setValue(num( ($res['amount'] / $menu_total) * 100 ).'%');
                $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('H'.$rc)->setValue($res['cost_price']);
                $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('I'.$rc)->setValue($res['cost_price'] * $res['qty']);
                $sheet->getStyle('I'.$rc)->applyFromArray($styleNum);

            $rc++;
        } 
        $rc++;


        $mods_total = $trans_menus['mods_total'];
        if($mods_total > 0){
            $sheet->getCell('A'.$rc)->setValue('Total Modifiers Sale: ');
            $sheet->getCell('B'.$rc)->setValue(num($mods_total));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $rc++;
        }
        $net_no_adds = $net-$charges-$local_tax;
        $sheet->getCell('A'.$rc)->setValue('Total Sales: ');
        $sheet->getCell('B'.$rc)->setValue(num($net));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $txt = numInt(($charges));
        if($charges > 0)
            $txt = "(".numInt(($charges)).")";
        $sheet->getCell('A'.$rc)->setValue('Total Charges: ');
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $txt = numInt(($local_tax));
        if($local_tax > 0)
            $txt = "(".numInt(($local_tax)).")";
        $sheet->getCell('A'.$rc)->setValue('Total Local Tax: ');
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total Discounts: ');
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total VAT EXEMPT: ');
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total Gross Sales: ');
        $sheet->getCell('B'.$rc)->setValue(num($gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        
        update_load(100);
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/daily_menu_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;
    }
    public function yearly_item_sales_rep_excel()
    {
        // $this->menu_model->db = $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Item Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details(1);
        if(!empty($setup)){
            $set = $setup[0];            
            $store_open = $set->store_open;
        }else{
            $store_open = "00:00:00";            
        }

        update_load(10);
        sleep(1);
        
        // $daterange = $_GET['calendar_range'];        
        // $dates = explode(" to ",$daterange);        
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $dates[0] = date("Y-m-01", strtotime("-1 year"));
        $dates[1] = date("Y-m-t", strtotime("-1 year"));
        $from = date2SqlDateTime($dates[0]. " ".$store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$store_open);
        $trans = $this->menu_model->get_item_sales($from, $to, $branch_code); 
        $trans_mod = $this->menu_model->get_mod_cat_sales_rep($from, $to, "");       

        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleCenter = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldCenter = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        
        $headers = array('Branch', 'Transaction Date', 'Item Code','Item Description','Category','Sub Category','Quantity Sold','Price', 'Total');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($branch_code);
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($set->address);        
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;                          

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $total = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        // echo print_r($trans);die();
        foreach ($trans as $val) {
            $tot_gross += $val->item_gross;
            $tot_cost += 0;
        }
        foreach ($trans_mod as $vv) {
            $tot_mod_gross += $vv->mod_gross;
        }        

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->branch_code);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->date);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue($v->code);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);            
            $sheet->getCell('D'.$rc)->setValue(num($v->item_name));     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('E'.$rc)->setValue($v->cat_name);     
            $sheet->getStyle('E'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('F'.$rc)->setValue($v->sub_cat_name);     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleTxt);            
            $sheet->getCell('G'.$rc)->setValue(num($v->tot_qty));                                 
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('H'.$rc)->setValue(num($v->price));     
            $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('I'.$rc)->setValue(num($v->tot_qty * $v->price));     
            $sheet->getStyle('I'.$rc)->applyFromArray($styleNum);       

            // Grand Total
            $tot_qty += $v->tot_qty;
            $total += $v->tot_qty * $v->price;
            
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('F'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('H'.$rc)->setValue(num($total));     
        $sheet->getStyle('H'.$rc)->applyFromArray($styleBoldRight);
        $rc++;

        ///////////fpr payments
        // $this->cashier_model->db = $this->load->database('main', TRUE);
        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if($menu_cat_id != 0){
        //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // }


        $post = $this->set_post();
        // $curr = $this->search_current();
        $curr = false;
        $trans = $this->trans_sales($args,$curr);
        $sales = $trans['sales'];

        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_code);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_code);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_code);

        $gross = $trans_menus['gross'];

        $net = $trans['net'];
        $void = $trans['void'];
        $charges = $trans_charges['total'];
        $discounts = $trans_discounts['total'];
        $local_tax = $trans_local_tax['total'];
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        if($less_vat < 0)
            $less_vat = 0;

        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        $zero_rated = $trans_zero_rated['total'];
        $no_tax -= $zero_rated;

        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        $nontaxable = $no_tax - $no_tax_disc;
        $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;

        $vat_ = $taxable * .12;

        $rc++;
        $sheet->getCell('A'.$rc)->setValue('GROSS');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_gross + $tot_mod_gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($taxable));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($vat_));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT EXEMPT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($nontaxable-$zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('ZERO RATED');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //MENU SUB CAT
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('SUB CATEGORIES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $subcats = $trans_menus['sub_cats'];
        $total = 0;
        foreach ($subcats as $id => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $total += $val['amount'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('SubTotal'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        $rc++;
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('A'.$rc)->setValue(strtoupper('MODIFIERS TOTAL'));
        $sheet->getCell('B'.$rc)->setValue(num($trans_menus['mods_total']));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('TOTAL'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total + $trans_menus['mods_total']));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        //DISCOUNTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('DISCOUNT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_discounts['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('VAT EXEMPT'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // //CAHRGES
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('CHARGES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_charges['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($charges));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //PAYMENTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('PAYMENT MODE');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('PAYMENT AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $payments_types = $payments['types'];
        $payments_total = $payments['total'];
        foreach ($payments_types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($payments_total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        if($trans['total_chit']){
            $rc++; $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper('Total Chit'));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
            $sheet->getCell('B'.$rc)->setValue(num($trans['total_chit']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }


        $types = $trans['types'];
        $types_total = array();
        $guestCount = 0;
        foreach ($types as $type => $tp) {
            foreach ($tp as $id => $opt){
                if(isset($types_total[$type])){
                    $types_total[$type] += round($opt->total_amount,2);

                }
                else{
                    $types_total[$type] = round($opt->total_amount,2);
                }
                if($opt->guest == 0)
                    $guestCount += 1;
                else
                    $guestCount += $opt->guest;
            }
        }
        $rc++;
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Trans Count'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $tc_total  = 0;
        $tc_qty = 0;
        foreach ($types_total as $typ => $tamnt) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($typ));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(count($types[$typ]));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('C'.$rc)->setValue(num($tamnt));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $tc_total += $tamnt;
            $tc_qty += count($types[$typ]);
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('TC TOTAL'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tc_total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        
        update_load(100);        
       
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/yearly_item_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function monthly_item_sales_rep_excel()
    {
        // $this->menu_model->db = $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Item Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details(1);
        if(!empty($setup)){
            $set = $setup[0];            
            $store_open = $set->store_open;
        }else{
            $store_open = "00:00:00";            
        }

        update_load(10);
        sleep(1);
        
        // $daterange = $_GET['calendar_range'];        
        // $dates = explode(" to ",$daterange);        
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $dates[0] = date("Y-m-01", strtotime("-1 month"));
        $dates[1] = date("Y-m-t", strtotime("-1 month"));
        $from = date2SqlDateTime($dates[0]. " ".$store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$store_open);
        $trans = $this->menu_model->get_item_sales($from, $to, $branch_code); 
        $trans_mod = $this->menu_model->get_mod_cat_sales_rep($from, $to, "");       

        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleCenter = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldCenter = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        
        $headers = array('Branch', 'Transaction Date', 'Item Code','Item Description','Category','Sub Category','Quantity Sold','Price', 'Total');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($branch_code);
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($set->address);        
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;                          

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $total = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        // echo print_r($trans);die();
        foreach ($trans as $val) {
            $tot_gross += $val->item_gross;
            $tot_cost += 0;
        }
        foreach ($trans_mod as $vv) {
            $tot_mod_gross += $vv->mod_gross;
        }        

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->branch_code);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->date);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue($v->code);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);            
            $sheet->getCell('D'.$rc)->setValue(num($v->item_name));     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('E'.$rc)->setValue($v->cat_name);     
            $sheet->getStyle('E'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('F'.$rc)->setValue($v->sub_cat_name);     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleTxt);            
            $sheet->getCell('G'.$rc)->setValue(num($v->tot_qty));                                 
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('H'.$rc)->setValue(num($v->price));     
            $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('I'.$rc)->setValue(num($v->tot_qty * $v->price));     
            $sheet->getStyle('I'.$rc)->applyFromArray($styleNum);       

            // Grand Total
            $tot_qty += $v->tot_qty;
            $total += $v->tot_qty * $v->price;
            
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('F'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('H'.$rc)->setValue(num($total));     
        $sheet->getStyle('H'.$rc)->applyFromArray($styleBoldRight);
        $rc++;

        ///////////fpr payments
        // $this->cashier_model->db = $this->load->database('main', TRUE);
        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if($menu_cat_id != 0){
        //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // }


        $post = $this->set_post();
        // $curr = $this->search_current();
        $curr = false;
        $trans = $this->trans_sales($args,$curr);
        $sales = $trans['sales'];

        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_code);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_code);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_code);

        $gross = $trans_menus['gross'];

        $net = $trans['net'];
        $void = $trans['void'];
        $charges = $trans_charges['total'];
        $discounts = $trans_discounts['total'];
        $local_tax = $trans_local_tax['total'];
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        if($less_vat < 0)
            $less_vat = 0;

        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        $zero_rated = $trans_zero_rated['total'];
        $no_tax -= $zero_rated;

        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        $nontaxable = $no_tax - $no_tax_disc;
        $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;

        $vat_ = $taxable * .12;

        $rc++;
        $sheet->getCell('A'.$rc)->setValue('GROSS');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_gross + $tot_mod_gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($taxable));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($vat_));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT EXEMPT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($nontaxable-$zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('ZERO RATED');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //MENU SUB CAT
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('SUB CATEGORIES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $subcats = $trans_menus['sub_cats'];
        $total = 0;
        foreach ($subcats as $id => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $total += $val['amount'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('SubTotal'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        $rc++;
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('A'.$rc)->setValue(strtoupper('MODIFIERS TOTAL'));
        $sheet->getCell('B'.$rc)->setValue(num($trans_menus['mods_total']));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('TOTAL'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total + $trans_menus['mods_total']));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        //DISCOUNTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('DISCOUNT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_discounts['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('VAT EXEMPT'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // //CAHRGES
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('CHARGES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_charges['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($charges));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //PAYMENTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('PAYMENT MODE');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('PAYMENT AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $payments_types = $payments['types'];
        $payments_total = $payments['total'];
        foreach ($payments_types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($payments_total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        if($trans['total_chit']){
            $rc++; $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper('Total Chit'));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
            $sheet->getCell('B'.$rc)->setValue(num($trans['total_chit']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }


        $types = $trans['types'];
        $types_total = array();
        $guestCount = 0;
        foreach ($types as $type => $tp) {
            foreach ($tp as $id => $opt){
                if(isset($types_total[$type])){
                    $types_total[$type] += round($opt->total_amount,2);

                }
                else{
                    $types_total[$type] = round($opt->total_amount,2);
                }
                if($opt->guest == 0)
                    $guestCount += 1;
                else
                    $guestCount += $opt->guest;
            }
        }
        $rc++;
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Trans Count'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $tc_total  = 0;
        $tc_qty = 0;
        foreach ($types_total as $typ => $tamnt) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($typ));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(count($types[$typ]));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('C'.$rc)->setValue(num($tamnt));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $tc_total += $tamnt;
            $tc_qty += count($types[$typ]);
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('TC TOTAL'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tc_total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        
        update_load(100);        
       
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/monthly_item_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function daily_item_sales_rep_excel()
    {
        // $this->menu_model->db = $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Item Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details(1);
        if(!empty($setup)){
            $set = $setup[0];            
            $store_open = $set->store_open;
        }else{
            $store_open = "00:00:00";            
        }

        update_load(10);
        sleep(1);
        
        // $daterange = $_GET['calendar_range'];        
        // $dates = explode(" to ",$daterange);        
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $dates[0] = date("Y-m-01", strtotime("-1 day"));
        $dates[1] = date("Y-m-t", strtotime("-1 day"));
        $from = date2SqlDateTime($dates[0]. " ".$store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$store_open);
        $trans = $this->menu_model->get_item_sales($from, $to, $branch_code); 
        $trans_mod = $this->menu_model->get_mod_cat_sales_rep($from, $to, "");       

        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleCenter = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldCenter = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        
        $headers = array('Branch', 'Transaction Date', 'Item Code','Item Description','Category','Sub Category','Quantity Sold','Price', 'Total');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($branch_code);
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($set->address);        
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;                          

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $total = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        // echo print_r($trans);die();
        foreach ($trans as $val) {
            $tot_gross += $val->item_gross;
            $tot_cost += 0;
        }
        foreach ($trans_mod as $vv) {
            $tot_mod_gross += $vv->mod_gross;
        }        

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->branch_code);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->date);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue($v->code);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);            
            $sheet->getCell('D'.$rc)->setValue(num($v->item_name));     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('E'.$rc)->setValue($v->cat_name);     
            $sheet->getStyle('E'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('F'.$rc)->setValue($v->sub_cat_name);     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleTxt);            
            $sheet->getCell('G'.$rc)->setValue(num($v->tot_qty));                                 
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('H'.$rc)->setValue(num($v->price));     
            $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('I'.$rc)->setValue(num($v->tot_qty * $v->price));     
            $sheet->getStyle('I'.$rc)->applyFromArray($styleNum);       

            // Grand Total
            $tot_qty += $v->tot_qty;
            $total += $v->tot_qty * $v->price;
            
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('F'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('H'.$rc)->setValue(num($total));     
        $sheet->getStyle('H'.$rc)->applyFromArray($styleBoldRight);
        $rc++;

        ///////////fpr payments
        // $this->cashier_model->db = $this->load->database('main', TRUE);
        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if($menu_cat_id != 0){
        //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // }


        $post = $this->set_post();
        // $curr = $this->search_current();
        $curr = false;
        $trans = $this->trans_sales($args,$curr);
        $sales = $trans['sales'];

        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_code);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_code);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_code);

        $gross = $trans_menus['gross'];

        $net = $trans['net'];
        $void = $trans['void'];
        $charges = $trans_charges['total'];
        $discounts = $trans_discounts['total'];
        $local_tax = $trans_local_tax['total'];
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        if($less_vat < 0)
            $less_vat = 0;

        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        $zero_rated = $trans_zero_rated['total'];
        $no_tax -= $zero_rated;

        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        $nontaxable = $no_tax - $no_tax_disc;
        $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;

        $vat_ = $taxable * .12;

        $rc++;
        $sheet->getCell('A'.$rc)->setValue('GROSS');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_gross + $tot_mod_gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($taxable));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($vat_));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT EXEMPT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($nontaxable-$zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('ZERO RATED');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //MENU SUB CAT
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('SUB CATEGORIES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $subcats = $trans_menus['sub_cats'];
        $total = 0;
        foreach ($subcats as $id => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $total += $val['amount'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('SubTotal'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        $rc++;
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('A'.$rc)->setValue(strtoupper('MODIFIERS TOTAL'));
        $sheet->getCell('B'.$rc)->setValue(num($trans_menus['mods_total']));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('TOTAL'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total + $trans_menus['mods_total']));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        //DISCOUNTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('DISCOUNT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_discounts['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('VAT EXEMPT'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // //CAHRGES
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('CHARGES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_charges['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($charges));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //PAYMENTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('PAYMENT MODE');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('PAYMENT AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $payments_types = $payments['types'];
        $payments_total = $payments['total'];
        foreach ($payments_types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($payments_total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        if($trans['total_chit']){
            $rc++; $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper('Total Chit'));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
            $sheet->getCell('B'.$rc)->setValue(num($trans['total_chit']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }


        $types = $trans['types'];
        $types_total = array();
        $guestCount = 0;
        foreach ($types as $type => $tp) {
            foreach ($tp as $id => $opt){
                if(isset($types_total[$type])){
                    $types_total[$type] += round($opt->total_amount,2);

                }
                else{
                    $types_total[$type] = round($opt->total_amount,2);
                }
                if($opt->guest == 0)
                    $guestCount += 1;
                else
                    $guestCount += $opt->guest;
            }
        }
        $rc++;
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Trans Count'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $tc_total  = 0;
        $tc_qty = 0;
        foreach ($types_total as $typ => $tamnt) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($typ));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(count($types[$typ]));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('C'.$rc)->setValue(num($tamnt));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $tc_total += $tamnt;
            $tc_qty += count($types[$typ]);
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('TC TOTAL'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tc_total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        
        update_load(100);        
       
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/daily_item_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function yearly_sales_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Category Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details(1);
        $set = $setup[0];

        update_load(10);
        sleep(1);
        
        $menu_cat_id = "";//$_GET['menu_cat_id'];        
        // $daterange = $_GET['calendar_range'];        
        $date_from = date("Y-m-01", strtotime("-1 year"));
        $date_to = date("Y-m-t", strtotime("-1 year"));
        $daterange = $date_from." to ".$date_to;        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        // $branch_id = $this->input->post('branch_id');
        $branch_id = $_GET['branch_id'];   
        $trans = $this->menu_model->get_cat_sales_rep($from, $to, $menu_cat_id,$branch_id);   
        $trans_payment = $this->menu_model->get_payment_date($from, $to);   

        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleCenter = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldCenter = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        if($branch_id == ""){
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin','Branch');
        }
        else{
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin');
        }
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        // $sheet->getColumnDimension('H')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue(COMPANY_NAME);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue(COMPANY_ADDRESS);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;                          

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        foreach ($trans as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
            $tot_margin += $val->gross - $val->cost;
        }

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->menu_cat_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->qty);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('C'.$rc)->setValue(num($v->vat_sales));
            // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('D'.$rc)->setValue(num($v->vat));     
            // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('C'.$rc)->setValue(num($v->gross));     
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('D'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            if($tot_cost != 0){
                $sheet->getCell('E'.$rc)->setValue(num($v->cost));                     
            }else{
                $sheet->getCell('E'.$rc)->setValue(num(0));                                     
            }
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('G'.$rc)->setValue(num($v->gross - $v->cost));     
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            if($branch_id == ""){
                $sheet->getCell('H'.$rc)->setValue($v->branch_code);
                $sheet->getStyle('H'.$rc)->applyFromArray($styleTxt);
            }      
            // Grand Total
            $tot_qty += $v->qty;
            // $tot_vat_sales += $v->vat_sales;
            // $tot_vat += $v->vat;
            // $tot_gross += $v->gross;
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('C'.$rc)->setValue(num($tot_vat_sales));     
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('D'.$rc)->setValue(num($tot_vat));     
        // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('C'.$rc)->setValue(num($tot_gross));     
        $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('D'.$rc)->setValue("");     
        $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('E'.$rc)->setValue(num($tot_cost));     
        $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('F'.$rc)->setValue("");     
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('G'.$rc)->setValue(num($tot_margin));     
        $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
        $rc++; 


        ///////////fpr payments
        $this->cashier_model->db = $this->load->database('default', TRUE);
        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if($menu_cat_id != 0){
        //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // }


        $post = $this->set_post();
        // $curr = $this->search_current();
        $curr = false;
        $trans = $this->trans_sales($args,$curr,$branch_id);
        $sales = $trans['sales'];

        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_id);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_id);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_id);

        $gross = $trans_menus['gross'];

        $net = $trans['net'];
        $void = $trans['void'];
        $charges = $trans_charges['total'];
        $discounts = $trans_discounts['total'];
        $local_tax = $trans_local_tax['total'];
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        if($less_vat < 0)
            $less_vat = 0;

        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        $zero_rated = $trans_zero_rated['total'];
        $no_tax -= $zero_rated;

        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        $nontaxable = $no_tax - $no_tax_disc;
        $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;

        $vat_ = $taxable * .12;

        $rc++;
        $sheet->getCell('A'.$rc)->setValue('GROSS');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($taxable));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($vat_));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT EXEMPT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($nontaxable-$zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('ZERO RATED');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //MENU SUB CAT
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('SUB CATEGORIES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $subcats = $trans_menus['sub_cats'];
        $total = 0;
        foreach ($subcats as $id => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $total += $val['amount'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        //DISCOUNTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('DISCOUNT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_discounts['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('VAT EXEMPT'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // //CAHRGES
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('CHARGES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_charges['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($charges));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //PAYMENTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('PAYMENT MODE');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('PAYMENT AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $payments_types = $payments['types'];
        $payments_total = $payments['total'];
        foreach ($payments_types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($payments_total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        
        update_load(100);        
       
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/yearly_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function monthly_sales_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Category Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details(1);
        $set = $setup[0];

        update_load(10);
        sleep(1);
        
        $menu_cat_id = "";//$_GET['menu_cat_id'];        
        // $daterange = $_GET['calendar_range'];        
        $date_from = date("Y-m-01", strtotime("-1 month"));
        $date_to = date("Y-m-t", strtotime("-1 month"));
        $daterange = $date_from." to ".$date_to;        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        // $branch_id = $this->input->post('branch_id');
        $branch_id = $_GET['branch_id'];   
        $trans = $this->menu_model->get_cat_sales_rep($from, $to, $menu_cat_id,$branch_id);   
        $trans_payment = $this->menu_model->get_payment_date($from, $to);   

        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleCenter = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldCenter = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        if($branch_id == ""){
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin','Branch');
        }
        else{
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin');
        }
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        // $sheet->getColumnDimension('H')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue(COMPANY_NAME);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue(COMPANY_ADDRESS);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;                          

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        foreach ($trans as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
            $tot_margin += $val->gross - $val->cost;
        }

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->menu_cat_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->qty);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('C'.$rc)->setValue(num($v->vat_sales));
            // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('D'.$rc)->setValue(num($v->vat));     
            // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('C'.$rc)->setValue(num($v->gross));     
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('D'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            if($tot_cost != 0){
                $sheet->getCell('E'.$rc)->setValue(num($v->cost));                     
            }else{
                $sheet->getCell('E'.$rc)->setValue(num(0));                                     
            }
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('G'.$rc)->setValue(num($v->gross - $v->cost));     
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            if($branch_id == ""){
                $sheet->getCell('H'.$rc)->setValue($v->branch_code);
                $sheet->getStyle('H'.$rc)->applyFromArray($styleTxt);
            }      
            // Grand Total
            $tot_qty += $v->qty;
            // $tot_vat_sales += $v->vat_sales;
            // $tot_vat += $v->vat;
            // $tot_gross += $v->gross;
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('C'.$rc)->setValue(num($tot_vat_sales));     
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('D'.$rc)->setValue(num($tot_vat));     
        // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('C'.$rc)->setValue(num($tot_gross));     
        $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('D'.$rc)->setValue("");     
        $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('E'.$rc)->setValue(num($tot_cost));     
        $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('F'.$rc)->setValue("");     
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('G'.$rc)->setValue(num($tot_margin));     
        $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
        $rc++; 


        ///////////fpr payments
        $this->cashier_model->db = $this->load->database('default', TRUE);
        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if($menu_cat_id != 0){
        //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // }


        $post = $this->set_post();
        // $curr = $this->search_current();
        $curr = false;
        $trans = $this->trans_sales($args,$curr,$branch_id);
        $sales = $trans['sales'];

        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_id);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_id);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_id);

        $gross = $trans_menus['gross'];

        $net = $trans['net'];
        $void = $trans['void'];
        $charges = $trans_charges['total'];
        $discounts = $trans_discounts['total'];
        $local_tax = $trans_local_tax['total'];
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        if($less_vat < 0)
            $less_vat = 0;

        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        $zero_rated = $trans_zero_rated['total'];
        $no_tax -= $zero_rated;

        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        $nontaxable = $no_tax - $no_tax_disc;
        $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;

        $vat_ = $taxable * .12;

        $rc++;
        $sheet->getCell('A'.$rc)->setValue('GROSS');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($taxable));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($vat_));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT EXEMPT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($nontaxable-$zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('ZERO RATED');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //MENU SUB CAT
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('SUB CATEGORIES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $subcats = $trans_menus['sub_cats'];
        $total = 0;
        foreach ($subcats as $id => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $total += $val['amount'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        //DISCOUNTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('DISCOUNT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_discounts['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('VAT EXEMPT'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // //CAHRGES
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('CHARGES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_charges['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($charges));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //PAYMENTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('PAYMENT MODE');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('PAYMENT AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $payments_types = $payments['types'];
        $payments_total = $payments['total'];
        foreach ($payments_types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($payments_total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        
        update_load(100);        
       
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/monthly_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function daily_sales_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Category Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details(1);
        $set = $setup[0];

        update_load(10);
        sleep(1);
        
        $menu_cat_id = "";//$_GET['menu_cat_id'];        
        // $daterange = $_GET['calendar_range'];        
        $date_from = date("Y-m-01", strtotime("-1 day"));
        $date_to = date("Y-m-t", strtotime("-1 day"));
        $daterange = $date_from." to ".$date_to;        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        // $branch_id = $this->input->post('branch_id');
        $branch_id = $_GET['branch_id'];   
        $trans = $this->menu_model->get_cat_sales_rep($from, $to, $menu_cat_id,$branch_id);   
        $trans_payment = $this->menu_model->get_payment_date($from, $to);   

        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleCenter = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldCenter = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        if($branch_id == ""){
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin','Branch');
        }
        else{
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin');
        }
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        // $sheet->getColumnDimension('H')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue(COMPANY_NAME);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue(COMPANY_ADDRESS);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;                          

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        foreach ($trans as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
            $tot_margin += $val->gross - $val->cost;
        }

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->menu_cat_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->qty);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('C'.$rc)->setValue(num($v->vat_sales));
            // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('D'.$rc)->setValue(num($v->vat));     
            // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('C'.$rc)->setValue(num($v->gross));     
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('D'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            if($tot_cost != 0){
                $sheet->getCell('E'.$rc)->setValue(num($v->cost));                     
            }else{
                $sheet->getCell('E'.$rc)->setValue(num(0));                                     
            }
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('G'.$rc)->setValue(num($v->gross - $v->cost));     
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            if($branch_id == ""){
                $sheet->getCell('H'.$rc)->setValue($v->branch_code);
                $sheet->getStyle('H'.$rc)->applyFromArray($styleTxt);
            }      
            // Grand Total
            $tot_qty += $v->qty;
            // $tot_vat_sales += $v->vat_sales;
            // $tot_vat += $v->vat;
            // $tot_gross += $v->gross;
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('C'.$rc)->setValue(num($tot_vat_sales));     
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('D'.$rc)->setValue(num($tot_vat));     
        // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('C'.$rc)->setValue(num($tot_gross));     
        $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('D'.$rc)->setValue("");     
        $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('E'.$rc)->setValue(num($tot_cost));     
        $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('F'.$rc)->setValue("");     
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('G'.$rc)->setValue(num($tot_margin));     
        $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
        $rc++; 


        ///////////fpr payments
        $this->cashier_model->db = $this->load->database('default', TRUE);
        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if($menu_cat_id != 0){
        //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // }


        $post = $this->set_post();
        // $curr = $this->search_current();
        $curr = false;
        $trans = $this->trans_sales($args,$curr,$branch_id);
        $sales = $trans['sales'];

        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_id);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_id);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_id);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_id);

        $gross = $trans_menus['gross'];

        $net = $trans['net'];
        $void = $trans['void'];
        $charges = $trans_charges['total'];
        $discounts = $trans_discounts['total'];
        $local_tax = $trans_local_tax['total'];
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        if($less_vat < 0)
            $less_vat = 0;

        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        $zero_rated = $trans_zero_rated['total'];
        $no_tax -= $zero_rated;

        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        $nontaxable = $no_tax - $no_tax_disc;
        $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;

        $vat_ = $taxable * .12;

        $rc++;
        $sheet->getCell('A'.$rc)->setValue('GROSS');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($taxable));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($vat_));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT EXEMPT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($nontaxable-$zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('ZERO RATED');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //MENU SUB CAT
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('SUB CATEGORIES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $subcats = $trans_menus['sub_cats'];
        $total = 0;
        foreach ($subcats as $id => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $total += $val['amount'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        //DISCOUNTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('DISCOUNT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_discounts['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('VAT EXEMPT'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // //CAHRGES
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('CHARGES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_charges['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($charges));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //PAYMENTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('PAYMENT MODE');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('PAYMENT AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $payments_types = $payments['types'];
        $payments_total = $payments['total'];
        foreach ($payments_types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($payments_total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        
        update_load(100);        
       
        ob_end_clean();
        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        $filename = 'uploads/reports/daily_sales_report_'.date("Ymd").'.xls';
        $objWriter->save("../".$filename);
        return $filename;

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function menu_sales($ids=array(),$curr=false,$branch_code=""){
        $cats = array();
        $this->site_model->db = $this->load->database('default', TRUE);
        $cat_res = $this->site_model->get_tbl('menu_categories',array('branch_code'=>$branch_code));
        foreach ($cat_res as $ces) {
            $cats[$ces->menu_cat_id] = array('cat_id'=>$ces->menu_cat_id,'name'=>$ces->menu_cat_name,'qty'=>0,'amount'=>0);
        }
         // echo $this->site_model->db->last_query(); die();
        $sub_cats = array();
        // $sales_menu_cat = $this->menu_model->get_menu_subcategories(null,true);
        // foreach ($sales_menu_cat as $rc) {
        //     $sub_cats[$rc->menu_sub_cat_id] = array('name'=>$rc->menu_sub_cat_name,'qty'=>0,'amount'=>0);
        // }

        //sabi ni sir hardcode na food and bev lang ang menu sub cat
        $sub_cats[1] = array('name'=>'FOOD','qty'=>0,'amount'=>0);
        $sub_cats[2] = array('name'=>'BEVERAGE','qty'=>0,'amount'=>0);

        $menu_net_total = 0;
        $menu_qty_total = 0;
        $item_net_total = 0;
        $item_qty_total = 0;
        $menus = array();
        $free_menus = array();
        $ids_used = array();
        if(count($ids) > 0){
            $select = 'trans_sales_menus.*,menus.menu_code,menus.menu_name,menus.cost as sell_price,menus.menu_cat_id as cat_id,menus.menu_sub_cat_id as sub_cat_id, menus.costing';
            $join = null;
            if($branch_code != ""){
            $join['menus'] = array('content'=>"trans_sales_menus.menu_id = menus.menu_id  AND menus.branch_code= '".$branch_code."'");
            }else{
            $join['menus'] = array('content'=>'trans_sales_menus.menu_id = menus.menu_id');
            }
            $n_menu_res = array();
            // if($curr){
            //     $this->site_model->db = $this->load->database('default', TRUE);
            //     $n_menu_res = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$ids,'trans_sales_menus.branch_code'=>$branch_code),array(),$join,true,$select);
            // }
            $this->site_model->db= $this->load->database('default', TRUE);
            if($branch_code != ""){
                $menu_res = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$ids,'trans_sales_menus.branch_code'=>$branch_code),array(),$join,true,$select);
            }else{
                $menu_res = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$ids),array('trans_sales_menus.branch_code'=>'trans_sales_menus.branch_code desc'),$join,true,$select);
            }
            // echo $this->site_model->db->last_query(); die();
            // echo "<pre>",print_r($menu_res),"</pre>";die();
            foreach ($menu_res as $ms) {
                if(!in_array($ms->sales_id, $ids_used)){
                    $ids_used[] = $ms->sales_id;
                }
                if(!isset($menus[$ms->menu_id])){
                    $mn = array();
                    $mn['name'] = $ms->menu_name;
                    $mn['branch_code'] = $ms->branch_code;
                    $mn['cat_id'] = $ms->cat_id;
                    $mn['qty'] = $ms->qty;
                    $mn['sell_price'] = $ms->sell_price;
                    $mn['cost_price'] = $ms->costing;
                    $mn['code'] = $ms->menu_code;
                    $mn['amount'] = $ms->price * $ms->qty;
                    $menus[$ms->menu_id] = $mn;
                }
                else{
                    $mn = $menus[$ms->menu_id];
                    $mn['qty'] += $ms->qty;
                    $mn['amount'] += $ms->price * $ms->qty;
                    $menus[$ms->menu_id] = $mn;
                }

                //pinabago ni sir dapat daw food and bev lang
                if($ms->sub_cat_id != 2){
                    //food
                    $sub = $sub_cats[1];
                    $sub['qty'] += $ms->qty;
                    $sub['amount'] += $ms->price * $ms->qty;
                    $sub_cats[1] = $sub;
                }else{
                    //bev
                    $sub = $sub_cats[2];
                    $sub['qty'] += $ms->qty;
                    $sub['amount'] += $ms->price * $ms->qty;
                    $sub_cats[2] = $sub;
                }
                // if(isset($sub_cats[$ms->sub_cat_id])){
                //     $sub = $sub_cats[$ms->sub_cat_id];
                //     $sub['qty'] += $ms->qty;
                //     $sub['amount'] += $ms->price * $ms->qty;
                //     $sub_cats[$ms->sub_cat_id] = $sub;
                // }
                if(isset($cats[$ms->cat_id])){
                    $cat = $cats[$ms->cat_id];
                    $cat['qty'] += $ms->qty;
                    $cat['amount'] += $ms->price * $ms->qty;
                    $cats[$ms->cat_id] = $cat;
                }
                $menu_net_total += $ms->price * $ms->qty;
                $menu_qty_total += $ms->qty;
                if($ms->free_user_id != "" && $ms->free_user_id != 0){
                    $free_menus[] = $ms;
                }
            }
            // if(count($n_menu_res) > 0){
            //     foreach ($n_menu_res as $ms) {
            //         if(!in_array($ms->sales_id, $ids_used)){
            //             if(!isset($menus[$ms->menu_id])){
            //                 $mn = array();
            //                 $mn['name'] = $ms->menu_name;
            //                 $mn['cat_id'] = $ms->cat_id;
            //                 $mn['qty'] = $ms->qty;
            //                 $mn['amount'] = $ms->price * $ms->qty;
            //                 $mn['sell_price'] = $ms->sell_price;
            //                 $mn['cost_price'] = $ms->costing;
            //                 $mn['code'] = $ms->menu_code;
            //                 $menus[$ms->menu_id] = $mn;
            //             }
            //             else{
            //                 $mn = $menus[$ms->menu_id];
            //                 $mn['qty'] += $ms->qty;
            //                 $mn['amount'] += $ms->price * $ms->qty;
            //                 $menus[$ms->menu_id] = $mn;
            //             }
            //             //pinabago ni sir dapat daw food and bev lang
            //             if($ms->sub_cat_id != 2){
            //                 //food
            //                 $sub = $sub_cats[1];
            //                 $sub['qty'] += $ms->qty;
            //                 $sub['amount'] += $ms->price * $ms->qty;
            //                 $sub_cats[1] = $sub;
            //             }else{
            //                 //bev
            //                 $sub = $sub_cats[2];
            //                 $sub['qty'] += $ms->qty;
            //                 $sub['amount'] += $ms->price * $ms->qty;
            //                 $sub_cats[2] = $sub;
            //             }
            //             // if(isset($sub_cats[$ms->sub_cat_id])){
            //             //     $sub = $sub_cats[$ms->sub_cat_id];
            //             //     $sub['qty'] += $ms->qty;
            //             //     $sub['amount'] += $ms->price * $ms->qty;
            //             //     $sub_cats[$ms->sub_cat_id] = $sub;
            //             // }
            //             if(isset($cats[$ms->cat_id])){
            //                 $cat = $cats[$ms->cat_id];
            //                 $cat['qty'] += $ms->qty;
            //                 $cat['amount'] += $ms->price * $ms->qty;
            //                 $cats[$ms->cat_id] = $cat;
            //             }
            //             $menu_net_total += $ms->price * $ms->qty;
            //             $menu_qty_total += $ms->qty;
            //             if($ms->free_user_id != "" && $ms->free_user_id != 0){
            //                 $free_menus[] = $ms;
            //             }
            //         }
            //     }
            // }
        }
        $total_md = 0;
        $mids_used = array();
        $mods = array();
        if(count($ids) > 0){
            $n_menu_cat_sale_mods=array();
            // if($curr){
            //     $this->cashier_model->db = $this->load->database('default', TRUE);
            //     $n_menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));
            // }
            $this->cashier_model->db = $this->load->database('default', TRUE);
            if($branch_code != ""){
            $menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids,'trans_sales_menu_modifiers.branch_code'=>$branch_code));                    
            }else{
            $menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));
            }
            foreach ($menu_cat_sale_mods as $res) {
                if(!in_array($res->sales_id, $mids_used)){
                    $mids_used[] = $res->sales_id;
                }
                if(!isset($mods[$res->mod_id])){
                    $mods[$res->mod_id] = array(
                        'name'=>$res->mod_name,
                        'price'=>$res->price,
                        'qty'=>$res->qty,
                        'total_amt'=>$res->qty * $res->price,
                    );
                }
                else{
                    $mod = $mods[$res->mod_id];
                    $mod['qty'] += $res->qty;
                    $mod['total_amt'] += $res->qty * $res->price;
                    $mods[$res->mod_id] = $mod;
                }
            }
            // if(count($n_menu_cat_sale_mods) > 0){
            //     foreach ($n_menu_cat_sale_mods as $res) {
            //         if(!in_array($res->sales_id, $mids_used)){
            //             if(!isset($mods[$res->mod_id])){
            //                 $mods[$res->mod_id] = array(
            //                     'name'=>$res->mod_name,
            //                     'price'=>$res->price,
            //                     'qty'=>$res->qty,
            //                     'total_amt'=>$res->qty * $res->price,
            //                 );
            //             }
            //             else{
            //                 $mod = $mods[$res->mod_id];
            //                 $mod['qty'] += $res->qty;
            //                 $mod['total_amt'] += $res->qty * $res->price;
            //                 $mods[$res->mod_id] = $mod;
            //             }
            //         }
            //     }
            // }
            foreach ($mods as $modid => $md) {
                $total_md += $md['total_amt'];
            }
        }
        #ITEMS
        if(count($ids) > 0){
            $select = 'trans_sales_items.*,items.code as item_code,items.name as item_name,items.cost as item_cost';
            $join = null;
            $join['items'] = array('content'=>"trans_sales_items.item_id = items.item_id AND items.branch_code= '".$branch_code."'");
            $n_item_res = array();
            // if($curr){
            //     $this->site_model->db = $this->load->database('default', TRUE);
            //     $n_item_res = $this->site_model->get_tbl('trans_sales_items',array('sales_id'=>$ids),array(),$join,true,$select);
            // }
            $this->site_model->db= $this->load->database('default', TRUE);
            if($branch_code != ""){
                $item_res = $this->site_model->get_tbl('trans_sales_items',array('sales_id'=>$ids,'trans_sales_items.branch_code'=>$branch_code),array(),$join,true,$select);
            }else{
                $item_res = $this->site_model->get_tbl('trans_sales_items',array('sales_id'=>$ids),array(),$join,true,$select);
            }
            // echo $this->site_model->db->last_query();die();
            $items = array();
            $itids_used = array();
            
            foreach ($item_res as $ms) {
                if(!in_array($ms->sales_id, $itids_used)){
                    $itids_used[] = $ms->sales_id;
                }
                // if(!isset($menus[$ms->item_id])){
                    $mn = array();
                    $mn['name'] = $ms->item_name;
                    $mn['qty'] = $ms->qty;
                    $mn['price'] = $ms->price;
                    $mn['code'] = $ms->item_code;
                    $mn['amount'] = $ms->price * $ms->qty;
                    $items[$ms->item_id] = $mn;
                // }
                // else{
                    $mn = $items[$ms->item_id];
                    $mn['qty'] += $ms->qty;
                    $mn['amount'] += $ms->price * $ms->qty;
                    $items[$ms->item_id] = $mn;
                // }
                $item_net_total += $ms->price * $ms->qty;
                $item_qty_total += $ms->qty;
            }
            // if(count($n_item_res) > 0){
            //     foreach ($n_item_res as $ms) {
            //         if(!in_array($ms->sales_id, $itids_used)){
            //             if(!isset($items[$ms->item_id])){
            //                 $mn = array();
            //                 $mn['name'] = $ms->item_name;
            //                 $mn['qty'] = $ms->qty;
            //                 $mn['price'] = $ms->price;
            //                 $mn['code'] = $ms->item_code;
            //                 $mn['amount'] = $ms->price * $ms->qty;
            //                 $items[$ms->item_id] = $mn;
            //             }
            //             else{
            //                 $mn = $items[$ms->item_id];
            //                 $mn['qty'] += $ms->qty;
            //                 $mn['amount'] += $ms->price * $ms->qty;
            //                 $items[$ms->item_id] = $mn;
            //             }
            //             $item_net_total += $ms->price * $ms->qty;
            //             $item_qty_total += $ms->qty;
            //         }
            //     }
            // }    

        }  
        // echo  $menu_net_total."<br>".$total_md."<br>".$item_net_total."<br>";die();
        // var_dump($menu_net_total+$total_md+$item_net_total); die();
         // echo "<pre>",print_r($menus),"</pre>";die();
        return array('gross'=>$menu_net_total+$total_md+$item_net_total,'menu_total'=>$menu_net_total,'total_qty'=>$menu_qty_total,'menus'=>$menus,'cats'=>$cats,'sub_cats'=>$sub_cats,'mods_total'=>$total_md,'mods'=>$mods,'free_menus'=>$free_menus,'item_total'=>$item_net_total,'item_total_qty'=>$item_qty_total);
    }
}