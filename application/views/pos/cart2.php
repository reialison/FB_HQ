
<body>

<div data-role="page" id="features" class="secondarypage" data-theme="b">

<!-- <div data-role="main" id="left-panel" class="ui-content" data-position="left">

              <nav class="main-nav">
               <ul>
				<li class="first_li"><a href="<?=base_url().'app/shop/snacks'?>" rel="external"><span class="icon-size">><img src ="<?= base_url().'img/icons/chips.png'?>" /></span><span>SNACKS</span></a></li>
                  <li class="two_li"><a href="<?=base_url().'app/shop/nab'?>" rel="external" ><span class="icon-size"><img src ="<?= base_url().'img/icons/drinks.png'?>" /></span><span>DRINKS</span></a></li>
                  <li class="four_li"><a href="<?=base_url().'app/shop/tobacco'?>" data-transition="slidefade" rel="external" ><span class="icon-size"><img src ="<?= base_url().'img/icons/tobacco.png'?>" /></span><span>TOBACCO</span></a></li>
                  <li class="five_li"><a href="<?=base_url().'app/shop/food and coffee'?>"  data-transition="slidefade" rel="external"><span class="icon-size"><img src ="<?= base_url().'img/icons/coffee.png'?>" /></span><span>FOOD & COFFEE</span></a></li>
                </ul>
              </nav>

    </div> -->

    <div data-role="header" data-position="fixed">
        <div class="nav_left_button"><span></span></a></div>
        <div class="nav_center_logo"></div>
        <!-- <div class="nav_right_button"><a href="#right-panel"><img src="images/icons/white/user.png" alt="" title="" /></a></div> -->
        <div class="clear"></div>
    </div><!-- /header -->

    

  <!-- content -->
   <div role="main" class="ui-content">
    <div class="col-md-12" style="    margin-top: 10px;">
            <div class="cart-main">
             <!-- <div class="cart-main"> -->

             <?php // echo "<pre>",print_r($item_cart),"</pre>"; ?>
				<h5> <img src="<?=base_url().'img/basket_red.png'?>" class="basket_img_top_cart"> Your Shopping Bag</h5>
				<table class="cart-table">
					<thead>
						<tr>
							<th width="15%">&nbsp;</th>
							<th width="30%">Product Name</th>
							<th width="15%">Unit Price</th>
							<th width="10%">Quantity</th>
							<th width="18%">Total</th>
							<th width="10%">&nbsp;</th>
						</tr>
					</thead>

					<tbody>

						<?php 

						if(!empty($item_cart)){
							foreach($item_cart as $item){ 
								$img_src =  base_url().'img/noimage.jpg';
		                        $qty = (isset($item['qty'])) ? $item['qty'] : 1 ;
		                         if(!empty($item['file_id'])){
                             $img_src =  base_url()."app/image/".$item['file_id'];//'data:image/jpeg;base64,'.base64_encode( $i_c['item_img'] );
                          }

		                        // if(!empty($item['item_img'])){
		                        //    $img_src = 'data:image/jpeg;base64,'.base64_encode( $item['item_img'] );
		                        // }

						?>
						
							<tr ref="<?=$item['item_id']?>">
								<td><img src="<?=$img_src?>" alt=""/></td>
								<td>
									<?=$item['item_name']?>
								</td>
								<td><?= " ".$item['unit_price_label']; ?></td>

								<td>
									<!-- <div class="quantity"> -->
									    <input type='hidden' name='srp_cart' value="<?=$item['unit_price']?>" />
									    <button class="btn-sm btn_cart_minus" style="    background-color: #DD1D21!important;
    float: left;
    max-width: 36px;
    margin-top: 6px;"><i class="fa fa-minus" aria-hidden="true"></i></button>
										<input type="number" name="qty" maxlength="12" value="<?=$qty?>" class="input-text qty bk_color qty" />
										<button class="btn-sm btn_cart_plus" style="    background-color: #008443!important;
    float: left;
    max-width: 36px;
    margin-top: 6px;"><i class="fa fa-plus"></i></button>

									<!-- </div> -->
								</td>
								<td>

									<label id="l_subtotal">0.00</label>
								</td>
								<td>
									<div class="closeit">X</div>
								</td>
							</tr>

						<?php
							}
						}else{ ?>
							<tr><td colspan="6">No items in your bag</td></tr>
						<?php } ?>
					
					</tbody>
				</table>

				<?php if(!empty($item_cart)) {  ?>
					<div class="row cart-bottom">
						
						<div class="col-md-8 col-md-offset-2">
							<ul>
								<li>Bag Subtotal <span id="subtotal">41.00</span></li>
								<!-- <li>Shipping Service <span>Free Shipping</span></li> -->
								<li>Order Total <span id="total">41.00</span></li>
							</ul>
						</div>
					</div>

					<div class="row cart-bottom">
						<div class="col-md-4 col-md-offset-2">
							<div class="add_info">
								Pump Number: <input type="number" name="pump_number" maxlength="12" value="" class="" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="add_info">
								Plate Number: <input type="text" name="plate_number" maxlength="12" value="" class="" />
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<div class="add_info">
								Any Request: <textarea name="comment"  value="" class="" ></textarea>
							</div>
						</div>
						
					</div>

				<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-md-12 mgn">
							<div class="dual-btns">
								<!-- <button class="btn-1 btn-orange btn-2 btn-style" style="max-width:180px;" id="update_cart">Update Bag</button> -->
								<button class="btn-1 btn-blue btn-2 btn-style" style="max-width:265px;"  id="checkout">Proceed to checkout</button>
								<button class="btn-1 btn-green btn-2 btn-style" style="max-width:250px;" id="back_shopping">Continue Shopping</button>
							</div>

						</div>

	 <div data-role="footer">
    <h4 class="banner_footer">Powered by <img class="btm_logo" src="<?=base_url().'img/header_logo_p1.png'?>"></h4>
					<!-- </div> -->

	<!-- <div id='navmenu'>
        <ul>
          <li class='has-sub'>
          </ul>
        </li>
    </div> -->

	


    <script>
    $(function(){
    	update_total();
    });
	$(document).on('click',".closeit" , function() {
      var ref = $(this).parents('tr').attr('ref');
      console.log(ref);
      $.post("<?= base_url().'app/remove_to_cart/'?>", {'ref':ref} ,function(resp){

        if(resp){

          $("tr[ref='"+ref+"']").hide(500);

          setTimeout(function(){
            $("tr[ref='"+ref+"']").remove();
            
             count_cart();
          },600);

        }

        
     });
    });

     $(document).on('change keyup',"input[name=qty]" , function() {
      update_total();
     });

     $("#back_shopping").on('click',function(){
     	window.location.href = "<?= base_url().'app/shop2/snacks'?>" ;
     });

     $('#update_cart').on('click',function(){
       var items = [];

        $('table tr').each(function(){
          var ref = $(this).attr('ref');
          var qty = $(this).find('input[name=qty]').val();

          if(ref !==undefined){

          	items.push({'ref':ref,'qty':qty});
          }
        });

         $.post("<?= base_url().'app/update_to_cart/'?>", {'item_list':items} ,function(resp){

              console.log(resp);
            if(resp){
            	swal({
				  title: "Your cart was successfully updated!",
				  text: "Do you want to proceed to checkout?",
				  type: "success",
				  showCancelButton: true,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Yes, I'm done!",
				  cancelButtonText: "No, I want more!",
				  closeOnConfirm: false
				},
				function(){
		   			var pump_number = $('input[name=pump_number]').val();
					var plate_number = $('input[name=plate_number]').val();
					var comment = $('textarea[name=comment]').val();

					if(pump_number.length <= 0 || plate_number.length <= 0){
								swal({
								  title: "Oopss..",
								  text: "Please fill out pump number and plate number.",
								  type: "warning",
								  showCancelButton: false,
								  confirmButtonColor: "#DD6B55",
								  confirmButtonText: "Okay",
								  closeOnConfirm: true
								
								});
					}else{			
				   		window.location.href =  "<?= base_url().'app/checkout2'?>";
					}
				});
              // swal('Your cart was successfully updated!','','success');
            }

        
         });
     });

     $('#checkout').on('click',function(){

     	 var items = [];

        $('table tr').each(function(){
          var ref = $(this).attr('ref');
          var qty = $(this).find('input[name=qty]').val();

          if(ref !==undefined){

          	items.push({'ref':ref,'qty':qty});
          }
        });

         $.post("<?= base_url().'app/update_to_cart/'?>", {'item_list':items} ,function(resp){

         	if(resp){

		     	swal({
						  title: "Are you sure you want to checkout?",
						  text: "",
						  type: "warning",
						  showCancelButton: true,
						  confirmButtonColor: "#DD6B55",
						  confirmButtonText: "Yes, I'm done!",
						  cancelButtonText: "No, I want more!",
						  closeOnConfirm: false
						},
						function(){
						    var pump_number = $('input[name=pump_number]').val();
							var plate_number = $('input[name=plate_number]').val();
							var comment = $('textarea[name=comment]').val();

							if(pump_number.length <= 0 || plate_number.length <= 0){
								swal({
								  title: "Oopss..",
								  text: "Please fill out pump number and plate number.",
								  type: "warning",
								  showCancelButton: false,
								  confirmButtonColor: "#DD6B55",
								  confirmButtonText: "Okay",
								  closeOnConfirm: true
								
								});
							}else{
  								$.post("<?= base_url().'app/add_checkout_details/'?>", {'pump_number':pump_number,'plate_number':plate_number,'comment':comment} ,function(resp){

  									// console.log(resp);
  									if(resp)
						 	 			window.location.href =  "<?= base_url().'app/checkout2'?>";

  								});
							}
				});
         	}
	     });
     });

 	$(document).on('click',".btn_cart_plus" , function() {
       var ref = $(this).parents('tr').attr('ref');
       var close_val = parseInt($('table.cart-table tr[ref='+ref+']').find('input[name=qty]').val());
       $('table.cart-table tr[ref='+ref+']').find('input[name=qty]').val(close_val + 1);
        update_total();

     });

     $(document).on('click',".btn_cart_minus" , function() {
       var ref = $(this).parents('tr').attr('ref');
        var close_val = parseInt($('table.cart-table tr[ref='+ref+']').find('input[name=qty]').val());
       if(close_val > 1)
        $('table.cart-table tr[ref='+ref+']').find('input[name=qty]').val(close_val - 1);

       update_total();
     });

     $(document).on('change keyup',"input[name=qty]" , function() {
      update_total();
     });

	 function update_total(){
      var total = 0;

      $('table tr').each(function(i,e){
      	var ref = $(this).attr('ref');
        var srp = $(e).find('input[name=srp_cart]').val();
        var qty = $(e).find('input[name=qty]').val();

		if(srp !== undefined){
			 var equate = srp * qty;

			 $('tr[ref='+ref+']').find('#l_subtotal').html(" " + equate.toFixed(2));

        	 total += equate;

       	}
      // console.log(srp); 
          
      });  

      $('span#subtotal').html(' ' + total.toFixed(2));
      $('span#total').html(' ' + total.toFixed(2));
      // return total;
    }


    function count_cart(){
        var ctr_i = $('.cart-table tbody tr').length;
        $(".shop-cart span").html(ctr_i);

        if(ctr_i <= 0){
			swal({
				title: "Oopss..",
				text: "No items in your cart.",
				type: "warning",
				showCancelButton: false,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Okay",
				closeOnConfirm: true
								
			},function(){
				  window.location.href = "<?= base_url().'app/shop2/snacks'?>" ;
			});
        }

    }
    </script>

