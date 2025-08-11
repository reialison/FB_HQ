<?php
  $finish = (isset($finish)) ? $finish : false;
?>
<body>

<div data-role="page" id="homepage"  data-theme="b" class="home_min_wid">
<div data-role="main" id="left-panel" class="ui-content" data-position="left">
    <!-- <div data-role="panel" id="left-panel" data-display="reveal" data-position="left"> -->

              <nav class="main-nav">
                <ul>
                  <li class="first_li"><a href="<?=base_url().'app/shop2/snacks'?>" rel="external"><span class="icon-size"><!-- <i class="fa fa-cutlery"></i> --><img src ="<?= base_url().'img/icons/chips.png'?>" /></span><span>SNACKS</span></a></li>
                  <li class="two_li"><a href="<?=base_url().'app/shop2/nab'?>" rel="external" ><span class="icon-size"><!--<i class="fa fa-coffee"></i>--><img src ="<?= base_url().'img/icons/drinks.png'?>" /></span><span>DRINKS</span></a></li>
                  <li class="four_li"><a href="<?=base_url().'app/shop2/tobacco'?>" data-transition="slidefade" rel="external" ><span class="icon-size"><!--<i class="fa fa-briefcase"></i>--> <img src ="<?= base_url().'img/icons/tobacco.png'?>" /></span><span>TOBACCO</span></a></li>
                  <li class="five_li"><a href="<?=base_url().'app/shop2/food and coffee'?>"  data-transition="slidefade" rel="external"><span class="icon-size"><!--<i class="fa fa-tag"></i>--><img src ="<?= base_url().'img/icons/coffee.png'?>" /></span><span>FOOD & COFFEE</span></a></li>
                </ul>
              </nav>

    </div><!-- /panel -->

    <div data-role="" data-position="fixed">
        <!-- <div class="nav_left_button"><a href="#" class="nav-toggle"><span></span></a></div> -->
        <div class="nav_center_logo"></div>
        <!-- <div class="nav_right_button"><a href="#right-panel"><img src="images/icons/white/user.png" alt="" title="" /></a></div> -->
        <div class="clear"></div>
    </div>
            <div role="main" class="ui-content">

        <div class="logo_container">
            <div class="logo">
            <img src="<?= base_url().'css/images/logo3.png'; ?>" alt="shell" title="shell" />
            <h2>Shell</h2>
            <span></span>                        
            </div>                     
        </div>
        <!-- <div class="slide_info"></div> -->

    </div><!-- /content -->
   <!--  <div id='navmenu'>
        <ul>
          <li class='has-sub'>
          </ul>
        </li>
    </div> -->



    


       <input type="hidden" id="is_finish" value="<?=$finish?>">
       <div data-role="footer">
    <h4 class="banner_footer">Powered by <img class="btm_logo" src="<?=base_url().'img/header_logo_p1.png'?>"></h4>

<script>
  $(function(){


    check_finish();


    function check_finish(){
      var is_finish = $('#is_finish').val();

      if(is_finish){

        swal({
          title: "Thank you for shopping!",
          type:'success',
          text: "Your order is now on process and will be ready in a minute.",
          timer: 7000,
          showConfirmButton: false
        });
      // $.mobile.activePage.find( "#left-panel" ).panel( "open" );
        
      }      
    }

  });

 // $(document).ready(function(){
 //        $.mobile.activePage.find( "#left-panel" ).panel( "open" );

 // })
</script>