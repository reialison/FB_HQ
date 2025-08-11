<body>
  <div data-role="page" id="features" class="secondarypage" data-theme="b">
    <div data-role="header" data-position="fixed">
      <div class="nav_left_button"><span></span></a></div>
      <div style="text-align:center">
            <a href="<?=base_url().'app/shop/snacks'?>" class="data_back" rel="external"><i class="fa fa-shopping-cart  "></i></a>
        </div>
        <!-- <div class="nav_center_logo"></div> -->
          <!-- <div class="clear"></div> -->
         <!-- </div> -->
         <!-- <div class="nav_center_logo"> -->
         </div>
         <div  style="text-align:center">
         <h1> <span> Order List</span></h1>
         </div>
              <div class="row">
                  <div class="col-md-12">
                      <div class="portlet light bordered">
                              <table class="table table-striped table-bordered table-hover haha-scroll table-checkable order-column" id="sample_1">
                                  <thead>
                                      <tr>
                                          <th> Plate Number </th>
                                          <th> Pump Number </th>
                                          <th style="text-align:center;"> Orders </th>
                                          <th> Payment Amount </th>
                                          <!-- <th> Amount Tendered </th> -->
                                          <th> Status </th>
                                          <th> Total </th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                  <?php foreach ($datatable_sales as $data) {
                                    // echo print_r($datatable_sales);die();
                                    // echo "<pre>",print_r($data),"</pre>";die();
                                   ?>
                                 
                                      <tr> 
                                          <td style="width: 12%;"><?php echo $data['plate_number'];?></td>
                                          <td style="width: 13%;"><?php echo $data['pump_number'];?></td>
                                          <td style="width: 35%;"><?php echo $data['items'];?></td>
                                          <td><?php echo number_format($data['payment_amount'],2);?></td>
                                          <?php if($data['suspended'] == 2 && $data['received'] == 0 ){
                                              $sus = "<span class='label btn-danger'>PENDING</span>";
                                          }elseif($data['suspended'] == 0){
                                              $sus = "<span class='label btn-success'>DISPATCHED</span>";
                                          }elseif($data['suspended'] == 2 && $data['received'] == 1 ){
                                              $sus = "<span class='label btn-primary'>RECEIVED</span>";
                                            }
                                           ?>
                                          <td><?php echo $sus;?></td>
                                          <td><?php echo $data['total'];?></td>
                                      </tr>
                                      <?php  }
                                      ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
                <div data-role="footer">
                <h4 class="banner_footer">Powered by <img class="btm_logo" src="<?=base_url().'img/header_logo_p1.png'?>"></h4>
            </div>
      </div>
   </body>
  <script src="../js/datatables/jquery.min.js" type="text/javascript"></script>
  <script src="../js/datatables/bootstrap.min.js" type="text/javascript"></script>
  <script src="../js/datatables/app.min.js" type="text/javascript"></script>
  <script src="../js/datatables/table-datatables-managed.min.js" type="text/javascript"></script>
  <script src="../js/datatables/datatable.js" type="text/javascript"></script>
  <script src="../js/datatables/datatables.min.js" type="text/javascript"></script>
  <script src="../js/datatables/datatables.bootstrap.js" type="text/javascript"></script>