<body>

<div data-role="page" id="features" class="secondarypage" data-theme="b">
    <div data-role="header" data-position="fixed" class="ui-panel-page-content-position-left ui-panel-page-content-display-reveal ui-panel-page-content upper-header">
      <div class="nav_left_button">
        <img src="<?=base_url().'img/food_rest.png'?>" class="rest_logo"> <label  class="rest_logo_label">Restaurant Name</label>
      </div>

        <label  class="rest_logo_name">iPos OrderApp</label>
      <div class="nav_right_button">
          <a class="shop-cart"> <span class="fa fa-inbox" style="color: #46bda9; font-size: 24px;margin-right: 4px;"></span> Shopping Bag (<span><?=  count($item_cart); ?></span>)</a>

      </div>
    </div>
    <div data-role="header" data-position="fixed" style="    margin-top: 50px;" class="ui-panel-page-content-position-left ui-panel-page-content-display-reveal ui-panel-page-content-open">
        <!-- <div class="nav_left_button"><a href="#" class="nav-toggle"><span></span></a></div> -->
        <div class="nav_right_button cart-main">  
          <!-- <a class="shop-cart"> <span class="fa fa-inbox" style="color: #46bda9; font-size: 24px;margin-right: 4px;"></span> Shopping Bag (<span><?=  count($item_cart); ?></span>)</a> -->
          <div id="cart-main">
                 <div>
                    <ul id="mini_cart">
                    <?php 


                      // echo "<pre>",print_r($item_cart),"</pre>";die();
                      if(isset($item_cart) && !empty($item_cart)){
                         foreach($item_cart as $i_c){

                          $img_src =  base_url().'img/noimage.jpg';
                          $qty = (isset($i_c['qty'])) ? $i_c['qty'] : 1 ;

                          // if(!empty($i_c['file_id'])){
                          //    $img_src
                          //     =  base_url()."app/image/".$i_c['file_id'];//'data:image/jpeg;base64,'.base64_encode( $i_c['item_img'] );
                          // }
                           if(!empty($i_c['item_img'])){
                              $img_src = base_url().$i_c['item_img'];
                           }


                    ?>
                       <li ref="<?= $i_c['item_id']; ?>">
                          <img src="<?=$img_src?>" alt="" />
                          <div class="description">
                          <div class="closeit">X</div>

                            <label><?=$i_c['item_name']?></label>
                            <div class="position"><p><span class="currency"><span></span><?= $i_c['unit_price_label'] ?></span>
                            <span class="sign">x</span>
                            <input type="hidden" name="srp_cart" value="<?= $i_c['unit_price'] ?>" />
                            <button class="btn-sm btn_cart_minus" style="background-color: #DD1D21!important;"><i class="fa fa-minus" aria-hidden="true"></i></button>
                            <input type="number" name="qty" min="1" value="<?=$qty?>" />
                            <button class="btn-sm btn_cart_plus" style="background-color:#008443!important;"><i class="fa fa-plus"></i></button></p></div>
                          </div>
                          <div class="clearfix"></div>
                       </li>

                    <?php 
                      }
                    } 

                    ?>
                 
                  </ul>
                  <div class="bottom">
                    <span class="total">Total: 41.00</span>
                    <div class="clearfix"></div>
                    <a href="#" id="checkout_cart" rel="external">Checkout <i class="fa fa-angle-right"></i></a>

                  </div>
                   <div class="bottom2">
                    <a href="#"  onclick="close_cart()" rel="external">Continue Shopping</i></a>
                     </div>
                </div>
            </div>
        </div>
        <div class="div_left_head">
          <h3 class="left_head">Restaurant Menu</h3>
        </div>
        <div>
          <form class="search" method="GET" action="<?=base_url().'app/search'?>">
            <input type="search" name="search" placeholder="Search entire store...">
            <button type="submit" id="search_btn"><i class="fa fa-search"></i></button>
         </form>
        </div>
    </div>



    

  <!-- content -->
  <div data-role="main" class="ui-content overflw">
    <div class="row">
      <div class="col-md-8">
        <div role="main" class="ui-content overflw main_b">
                   <div class="portlet-body">
                                    <!-- <p> Basic exemple. Resize the window to see how the tabs are moved into the dropdown </p> -->
                                    <div class="tabbable tabbable-tabdrop">
                                        <ul class="nav nav-tabs">
                                            <li class="active">
                                                <a href="#tab1" data-toggle="tab">Snacks</a>
                                            </li>
                                            <li>
                                                <a href="#tab2" data-toggle="tab">Meal</a>
                                            </li>
                                            <li>
                                                <a href="#tab3" data-toggle="tab">Bread</a>
                                            </li>
                                            <li>
                                                <a href="#tab4" data-toggle="tab">Drinks</a>
                                            </li>
                                            <li>
                                                <a href="#tab5" data-toggle="tab">Dessert</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab1">
                                                  <ul class="features_list_detailed">
                                                          <!-- <input type="text" id="search" value=""> -->
                                                          <!-- <li> -->
                                                          <!-- <h5><span>Latest Products</span></h5> -->


                                                            <?php 

                                                             // echo "<pre>",print_r($items),"</pre>" ;
                                                                if(empty($items)){
                                                            ?>
                                                              <div class="col-md-12">
                                                                  There are no available items on this category.
                                                              </div>

                                                            <?php
                                                                  
                                                                }else{
                                                            ?>
                                                           <div class="col-md-12">

                                                            <?php
                                                                  // echo "<pre>",print_r($items),"</pre>";die();
                                                                  foreach($items['snacks'] as $item){ 

                                                            ?>
                                                              <div class="col-md-3">

                                                            <?php
                                                                    $item_id = $item->menu_id ;
                                                                  
                                                                    $unit_price = $item->cost;
                                                                    $srp_label = number_format($item->cost,2,'.',',');
                                                                    $srp = number_format($item->cost,2);
                                                                    $img_src =  base_url().'img/noimage.jpg';

                                                                    if(!empty($item->img_id)){
                                                                      $img_src = base_url().$item->img_path;
                                                                      // $img_src = base_url()."app/image/".$item->img_id;
                                                                      //$img_src = 'data:image/jpeg;base64,'.base64_encode( $item->file_data );
                                                                    }

                                                            ?>
                                                                <div class="product-info" ref="<?=$item_id?>">

                                                                  <div class="product-thumb"  ref="<?=$item_id?>" imref="<?=$item->img_id; ?>">

                                                                    <img src="<?= $img_src ?>" class="img-responsive" alt=""/>
                                                                    <!-- <a data-remodal-target="modal">Quick View</a> -->
                                                                  </div>
                                                                  <!-- <div class="product-price"><?=$srp_label?></div> -->
                                                                  <h4 class="name_search"><a><?=$item->menu_name?></a></h4>

                                                                  <input type="hidden" id="srp" value="<?=$srp?>">
                                                                  <input type="hidden" id="desc" value="<?=$item->menu_short_desc;?>">
                                                                  <div class="marginize_bottom"><a href="#" ref="<?=$item_id?>" imref="<?=$item->img_id; ?>"  class="shop-btn"> <img src="<?=base_url().'img/basket.png'?>" class="basket_img basket"> Buy &nbsp;&nbsp;</a></div>
                                                                </div>
                                                            </div>

                                                            <?php 
                                                                }
                                                            ?>
                                                            </div>
                                                            <?php
                                                              }

                                                            ?>

                                                          
                                                          </li>      
                                                      </ul>
                                            </div>
                                            <div class="tab-pane" id="tab2">
                                                <ul class="features_list_detailed">
                                                          <!-- <input type="text" id="search" value=""> -->
                                                          <!-- <li> -->
                                                          <!-- <h5><span>Latest Products</span></h5> -->


                                                            <?php 

                                                             // echo "<pre>",print_r($items),"</pre>" ;
                                                                if(empty($items)){
                                                            ?>
                                                              <div class="col-md-12">
                                                                  There are no available items on this category.
                                                              </div>

                                                            <?php
                                                                  
                                                                }else{
                                                            ?>
                                                           <div class="col-md-12">

                                                            <?php
                                                                  // echo "<pre>",print_r($items),"</pre>";die();
                                                                  foreach($items['meals'] as $item){ 

                                                            ?>
                                                              <div class="col-md-3">

                                                            <?php
                                                                    $item_id = $item->menu_id ;
                                                                  
                                                                    $unit_price = $item->cost;
                                                                    $srp_label = number_format($item->cost,2,'.',',');
                                                                    $srp = number_format($item->cost,2);
                                                                    $img_src =  base_url().'img/noimage.jpg';

                                                                    if(!empty($item->img_id)){
                                                                      $img_src = base_url().$item->img_path;
                                                                      // $img_src = base_url()."app/image/".$item->img_id;
                                                                      //$img_src = 'data:image/jpeg;base64,'.base64_encode( $item->file_data );
                                                                    }

                                                            ?>
                                                                <div class="product-info" ref="<?=$item_id?>">

                                                                  <div class="product-thumb"  ref="<?=$item_id?>" imref="<?=$item->img_id; ?>">

                                                                    <img src="<?= $img_src ?>" class="img-responsive" alt=""/>
                                                                    <!-- <a data-remodal-target="modal">Quick View</a> -->
                                                                  </div>
                                                                  <!-- <div class="product-price"><?=$srp_label?></div> -->
                                                                  <h4 class="name_search"><a><?=$item->menu_name?></a></h4>

                                                                  <input type="hidden" id="srp" value="<?=$srp?>">
                                                                  <input type="hidden" id="desc" value="<?=$item->menu_short_desc;?>">
                                                                  <div class="marginize_bottom"><a href="#" ref="<?=$item_id?>" imref="<?=$item->img_id; ?>"  class="shop-btn"> <img src="<?=base_url().'img/basket.png'?>" class="basket_img basket"> Buy &nbsp;&nbsp;</a></div>
                                                                </div>
                                                            </div>

                                                            <?php 
                                                                }
                                                            ?>
                                                            </div>
                                                            <?php
                                                              }

                                                            ?>

                                                          
                                                          </li>      
                                                      </ul>
                                            </div>
                                            <div class="tab-pane" id="tab3">
                                                 <ul class="features_list_detailed">
                                                          <!-- <input type="text" id="search" value=""> -->
                                                          <!-- <li> -->
                                                          <!-- <h5><span>Latest Products</span></h5> -->


                                                            <?php 

                                                             // echo "<pre>",print_r($items),"</pre>" ;
                                                                if(empty($items)){
                                                            ?>
                                                              <div class="col-md-12">
                                                                  There are no available items on this category.
                                                              </div>

                                                            <?php
                                                                  
                                                                }else{
                                                            ?>
                                                           <div class="col-md-12">

                                                            <?php
                                                                  // echo "<pre>",print_r($items),"</pre>";die();
                                                                  foreach($items['bread'] as $item){ 

                                                            ?>
                                                              <div class="col-md-3">

                                                            <?php
                                                                    $item_id = $item->menu_id ;
                                                                  
                                                                    $unit_price = $item->cost;
                                                                    $srp_label = number_format($item->cost,2,'.',',');
                                                                    $srp = number_format($item->cost,2);
                                                                    $img_src =  base_url().'img/noimage.jpg';

                                                                    if(!empty($item->img_id)){
                                                                      $img_src = base_url().$item->img_path;
                                                                      // $img_src = base_url()."app/image/".$item->img_id;
                                                                      //$img_src = 'data:image/jpeg;base64,'.base64_encode( $item->file_data );
                                                                    }

                                                            ?>
                                                                <div class="product-info" ref="<?=$item_id?>">

                                                                  <div class="product-thumb"  ref="<?=$item_id?>" imref="<?=$item->img_id; ?>">

                                                                    <img src="<?= $img_src ?>" class="img-responsive" alt=""/>
                                                                    <!-- <a data-remodal-target="modal">Quick View</a> -->
                                                                  </div>
                                                                  <!-- <div class="product-price"><?=$srp_label?></div> -->
                                                                  <h4 class="name_search"><a><?=$item->menu_name?></a></h4>

                                                                  <input type="hidden" id="srp" value="<?=$srp?>">
                                                                  <input type="hidden" id="desc" value="<?=$item->menu_short_desc;?>">
                                                                  <div class="marginize_bottom"><a href="#" ref="<?=$item_id?>" imref="<?=$item->img_id; ?>"  class="shop-btn"> <img src="<?=base_url().'img/basket.png'?>" class="basket_img basket"> Buy &nbsp;&nbsp;</a></div>
                                                                </div>
                                                            </div>

                                                            <?php 
                                                                }
                                                            ?>
                                                            </div>
                                                            <?php
                                                              }

                                                            ?>

                                                          
                                                          </li>      
                                                      </ul>
                                            </div>
                                              <div class="tab-pane" id="tab4">
                                                 <ul class="features_list_detailed">
                                                          <!-- <input type="text" id="search" value=""> -->
                                                          <!-- <li> -->
                                                          <!-- <h5><span>Latest Products</span></h5> -->


                                                            <?php 

                                                             // echo "<pre>",print_r($items),"</pre>" ;
                                                                if(empty($items)){
                                                            ?>
                                                              <div class="col-md-12">
                                                                  There are no available items on this category.
                                                              </div>

                                                            <?php
                                                                  
                                                                }else{
                                                            ?>
                                                           <div class="col-md-12">

                                                            <?php
                                                                  // echo "<pre>",print_r($items),"</pre>";die();
                                                                  foreach($items['drinks'] as $item){ 

                                                            ?>
                                                              <div class="col-md-3">

                                                            <?php
                                                                    $item_id = $item->menu_id ;
                                                                  
                                                                    $unit_price = $item->cost;
                                                                    $srp_label = number_format($item->cost,2,'.',',');
                                                                    $srp = number_format($item->cost,2);
                                                                    $img_src =  base_url().'img/noimage.jpg';

                                                                    if(!empty($item->img_id)){
                                                                      $img_src = base_url().$item->img_path;
                                                                      // $img_src = base_url()."app/image/".$item->img_id;
                                                                      //$img_src = 'data:image/jpeg;base64,'.base64_encode( $item->file_data );
                                                                    }

                                                            ?>
                                                                <div class="product-info" ref="<?=$item_id?>">

                                                                  <div class="product-thumb"  ref="<?=$item_id?>" imref="<?=$item->img_id; ?>">

                                                                    <img src="<?= $img_src ?>" class="img-responsive" alt=""/>
                                                                    <!-- <a data-remodal-target="modal">Quick View</a> -->
                                                                  </div>
                                                                  <!-- <div class="product-price"><?=$srp_label?></div> -->
                                                                  <h4 class="name_search"><a><?=$item->menu_name?></a></h4>

                                                                  <input type="hidden" id="srp" value="<?=$srp?>">
                                                                  <input type="hidden" id="desc" value="<?=$item->menu_short_desc;?>">
                                                                  <div class="marginize_bottom"><a href="#" ref="<?=$item_id?>" imref="<?=$item->img_id; ?>"  class="shop-btn"> <img src="<?=base_url().'img/basket.png'?>" class="basket_img basket"> Buy &nbsp;&nbsp;</a></div>
                                                                </div>
                                                            </div>

                                                            <?php 
                                                                }
                                                            ?>
                                                            </div>
                                                            <?php
                                                              }

                                                            ?>

                                                          
                                                          </li>      
                                                      </ul>
                                            </div>
                                              <div class="tab-pane" id="tab5">
                                                 <ul class="features_list_detailed">
                                                          <!-- <input type="text" id="search" value=""> -->
                                                          <!-- <li> -->
                                                          <!-- <h5><span>Latest Products</span></h5> -->


                                                            <?php 

                                                             // echo "<pre>",print_r($items),"</pre>" ;
                                                                if(empty($items)){
                                                            ?>
                                                              <div class="col-md-12">
                                                                  There are no available items on this category.
                                                              </div>

                                                            <?php
                                                                  
                                                                }else{
                                                            ?>
                                                           <div class="col-md-12">

                                                            <?php
                                                                  // echo "<pre>",print_r($items),"</pre>";die();
                                                                  foreach($items['dessert'] as $item){ 

                                                            ?>
                                                              <div class="col-md-3">

                                                            <?php
                                                                    $item_id = $item->menu_id ;
                                                                  
                                                                    $unit_price = $item->cost;
                                                                    $srp_label = number_format($item->cost,2,'.',',');
                                                                    $srp = number_format($item->cost,2);
                                                                    $img_src =  base_url().'img/noimage.jpg';

                                                                    if(!empty($item->img_id)){
                                                                      $img_src = base_url().$item->img_path;
                                                                      // $img_src = base_url()."app/image/".$item->img_id;
                                                                      //$img_src = 'data:image/jpeg;base64,'.base64_encode( $item->file_data );
                                                                    }

                                                            ?>
                                                                <div class="product-info" ref="<?=$item_id?>">

                                                                  <div class="product-thumb"  ref="<?=$item_id?>" imref="<?=$item->img_id; ?>">

                                                                    <img src="<?= $img_src ?>" class="img-responsive" alt=""/>
                                                                    <!-- <a data-remodal-target="modal">Quick View</a> -->
                                                                  </div>
                                                                  <!-- <div class="product-price"><?=$srp_label?></div> -->
                                                                  <h4 class="name_search"><a><?=$item->menu_name?></a></h4>

                                                                  <input type="hidden" id="srp" value="<?=$srp?>">
                                                                  <input type="hidden" id="desc" value="<?=$item->menu_short_desc;?>">
                                                                  <div class="marginize_bottom"><a href="#" ref="<?=$item_id?>" imref="<?=$item->img_id; ?>"  class="shop-btn"> <img src="<?=base_url().'img/basket.png'?>" class="basket_img basket"> Buy &nbsp;&nbsp;</a></div>
                                                                </div>
                                                            </div>

                                                            <?php 
                                                                }
                                                            ?>
                                                            </div>
                                                            <?php
                                                              }

                                                            ?>

                                                          
                                                          </li>      
                                                      </ul>
                                            </div>
                                        </div>
                                    </div>
                    </div>
        </div>
      </div>
      <div class="col-md-4 white_b">
           <div role="main" class="ui-content overflw white_b">
                          <div>
                              <div class="col-md-12">
                                  <div class="info">
                                      <div class="img">
                                        

                                      </div>
                                      <div class="name">
                                        <h5 id="item_name"></h5>
                                      </div>
                                      <div class="desc">
                                          <label id="desc" class="side_texts"></label>
                                      </div>
                                       <div class="price">
                                          <label id="price" class="side_texts"></label>
                                      </div>
                                  </div>
                              </div>
                          </div>
            </div>
      </div>
         <!--    <div class="cart-main">
      
            </div> -->

    </div>
    </div>
  </div>
  <div data-role="main" id="left-panel" class="ui-content ui-panel ui-panel-position-left ui-panel-display-reveal ui-body-inherit ui-panel-animate ui-panel-open" data-position="left">


    <!-- <div data-role="panel" id="left-panel" data-display="reveal" data-position="left"> -->

              <nav class="main-nav">
                <ul>
                  <li class="first_li"><a href="javascript:void(0);" onclick="loading('<?=base_url().'app/shop/snacks'?>');" rel="external"><span class="icon-size"><!-- <i class="fa fa-cutlery"></i> --><img src ="<?= base_url().'img/icons/chips.png'?>" /></span><span class="classic">SNACKS</span></a></li>
                  <li class="two_li"><a href="javascript:void(0);" onclick="loading('<?=base_url().'app/shop/drinks'?>');"  rel="external"><span class="icon-size"><!--<i class="fa fa-coffee"></i>--><img src ="<?= base_url().'img/icons/drinks.png'?>" /></span><span class="classic">DRINKS</span></a></li>
                  <li class="third_li"><a href="javascript:void(0);" onclick="loading('<?=base_url().'app/shop/dessert'?>');"  rel="external"><span class="icon-size"><!--<i class="fa fa-coffee"></i>--><img src ="<?= base_url().'img/icons/dessert.png'?>" /></span><span class="classic">DESSERT</span></a></li>
                  <li class="four_li"><a href="javascript:void(0);" onclick="loading('<?=base_url().'app/shop/meals'?>');" rel="external"><span class="icon-size"><!--<i class="fa fa-briefcase"></i>--> <img src ="<?= base_url().'img/icons/meals.png'?>" /></span><span class="classic">MEALS</span></a></li>
                  <li class="five_li"><a href="javascript:void(0);" onclick="loading('<?=base_url().'app/shop/bread'?>');"  rel="external"><span class="icon-size"><!--<i class="fa fa-tag"></i>--><img src ="<?= base_url().'img/icons/Bread.png'?>" /></span><span class="classic">BREAD</span></a></li>

                </ul>
              </nav>

    </div>


       <input type="hidden" id="is_finish" value="<?=$finish?>">

</div><!-- /header -->
    <div data-role="footer">
    <h4 class="banner_footer">Powered by <img class="btm_logo" src="<?=base_url().'img/header_logo_p1.png'?>"></h4>

<!-- </div> -->
<!-- /page -->

<script>
  $(function(){
    update_total();
    $(document).on('click',' .product-thumb',function(){
      $(this).parents('.product-info').find('.shop-btn').click();
    });

    $(document).on('click','.shop-btn',function(e){
      e.preventDefault();
      wave($(this));
      var ref = $(this).attr('ref');
      var imref = $(this).attr('imref');
      console.log(ref);
      $.post("<?= base_url().'app/add_to_cart/'?>", {'ref':ref ,'imref':imref} ,function(resp){
        console.log(resp);
        if(resp){
          var img = $("ul.features_list_detailed").find("div.product-info[ref='"+ref+"']").find('.product-thumb').find('img').attr('src');
          var item_name =  $("ul.features_list_detailed").find("div.product-info[ref='"+ref+"']").find('h4:first').first().text();
          var item_price_label =  $("ul.features_list_detailed").find("div.product-info[ref='"+ref+"']").find('.product-price').first().text();
          var item_price =  $("ul.features_list_detailed").find("div.product-info[ref='"+ref+"']").find('input#srp').first().val();
          var item_desc =  $("ul.features_list_detailed").find("div.product-info[ref='"+ref+"']").find('input#desc').first().val();

          console.log(ref);
          console.log(item_price);
          $('div.info').show();
             $('div.info').find('.img img').remove();
                  $('div.info').find('.img').append("<img src='"+img+"'>");
                     $('div.info').find('#item_name').html(item_name);
                   $('div.info').find('#desc').html(item_desc);
                  $('div.info').find('#price').html(item_price);

          if($("li[ref='"+ref+"']").length == 0 ){ 
          // alert(ref);
              var li = "<li ref='"+ref+"'>";
                  li += "<img src='"+img+"'>";
                  li +=  '<div class="description">';
                  li +=  '<div class="closeit">X</div>';
                  li += "<label>"+item_name+"</label>";
                  li += "<div class='position'><p><span class='currency'><span>"+item_price_label+"</span></span>";
                  li += "<span class='sign'>x</span><button class=\"btn-sm btn_cart_minus\" style=\"background-color: #DD1D21!important; color:#fff!important;\"><i class=\"fa fa-minus\" aria-hidden=\"true\"></i></button><div class='ui-input-text ui-body-inherit ui-corner-all ui-shadow-inset ui-back'><input type='hidden' name='srp_cart' value='"+item_price+"' /><input type='number' name='qty' min='1' value='1' /></p></div><button class=\"btn-sm btn_cart_plus\" style=\"background-color: #008443!important;color:#fff!important\"><i class=\"fa fa-plus\" aria-hidden=\"true\"></i></button></div>";
                  li += "</div>";
                  li += "<div class='clearfix'></div>";
                  li += "</li>";

                  $('ul#mini_cart').append(li);


                  count_cart();
                  update_total();

          }
        }
      })
    });

    $(document).on('click',".closeit" , function() {
      var ref = $(this).parents('li').attr('ref');
      console.log(ref);
      $.post("<?= base_url().'app/remove_to_cart/'?>", {'ref':ref} ,function(resp){

        if(resp){

          $("li[ref='"+ref+"']").hide(500);

          setTimeout(function(){
            $("li[ref='"+ref+"']").remove();
            
             count_cart();
             update_total();
          },600);

        }

        
     });
    });

     $(document).on('click',".btn_cart_plus" , function() {
       var ref = $(this).parents('li').attr('ref');
       var close_val = parseInt($('ul li[ref='+ref+']').find('input[name=qty]').val());
       var new_val = close_val + 1;
        var items =[];
       $('ul li[ref='+ref+']').find('input[name=qty]').val(new_val);

         items.push({'ref':ref,'qty':new_val});
        console.log(items);
         $.post("<?= base_url().'app/update_to_cart/'?>", {'item_list':items} ,function(resp){
           
         });
        update_total();

     });

     $(document).on('click',".btn_cart_minus" , function() {
       var ref = $(this).parents('li').attr('ref');
       var close_val = parseInt($('ul li[ref='+ref+']').find('input[name=qty]').val());
        var new_val = close_val - 1;
        var items =[];

       if(close_val > 1)
        $('ul li[ref='+ref+']').find('input[name=qty]').val(new_val);
         items.push({'ref':ref,'qty':new_val});

         $.post("<?= base_url().'app/update_to_cart/'?>", {'item_list':items} ,function(resp){
         
         });
       update_total();
     });

     $(document).on('change keyup',"input[name=qty]" , function() {
      update_total();
     });

     $('#checkout_cart').on('click',function(){
       var items = [];

        $('ul#mini_cart li').each(function(){
          var ref = $(this).attr('ref');
          var qty = $(this).find('input[name=qty]').val();
          items.push({'ref':ref,'qty':qty});
        });

         $.post("<?= base_url().'app/update_to_cart/'?>", {'item_list':items} ,function(resp){

              console.log(resp);
            if(resp){
              window.location.href =  "<?= base_url().'app/cart'?>";

            }

        
         });
     });



    function update_total(){
      var total = 0;
     
      $('ul#mini_cart').find('li').each(function(i,e){
        var srp = $(e).find('input[name=srp_cart]').val();
        var qty = $(e).find('input[name=qty]').val();

         total += srp * qty;
      console.log(total); 
          
      });  

      $('span.total').html("Total:  " + total.toFixed(2));

      if(total > 0){

        $('span.header_total').html(""+ total.toFixed(2))
        $(".shop-cart span").html();
      }else{
         $('span.header_total').text('');
      }
      // return total;
    }

    function count_cart(){
        var ctr_i = $('ul#mini_cart li').length;
        $(".shop-cart span:last").html(ctr_i);

    }

    function wave($this){
      var cart = $('.cart-main');
      var imgtodrag = $this.parent().parent('.product-info').find("img").eq(0);
      if (imgtodrag) {
          var imgclone = imgtodrag.clone()
              .offset({
                  top: imgtodrag.offset().top,
                  left: imgtodrag.offset().left
              })
              .css({
                  'opacity': '0.5',
                  'position': 'absolute',
                  'height': '150px',
                  'width': '150px',
                  'z-index': '10000'
              })
              .appendTo($('body'))
              .animate({
                  'top': cart.offset().top + 10,
                  'left': cart.offset().left + 10,
                  'width': 75,
                  'height': 75
              }, 1000, 'easeInOutExpo');

          setTimeout(function() {
              cart.effect("shake", {
                  times: 2
              }, 200);
          }, 1500);

          imgclone.animate({
              'width': 0,
              'height': 0
          }, function() {
              // $this.detach()
          });
      }
    }

    $('form').on('submit',function(e){
      e.preventDefault();
      var search = $('input[name=search]').val();

      window.location.href= "<?=base_url().'app/search/'?>"+search;
      console.log(search);
    });

  });
    function close_cart(){
      $('a.shop-cart').click();
    }
    function loading(href){
          swal({
          title: "",
          text: "Loading...",
          showConfirmButton: false,
          imageUrl: "../../img/ajax-loader-green.gif"
        });
      window.location.href= href;
    };

  // alert('clicked me');
  // swal.enableLoading();
// });
</script>