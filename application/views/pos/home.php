<?php
  $finish = (isset($finish)) ? $finish : false;
?>
<body>

<div data-role="page" id="homepage"  data-theme="b" class="">
<div data-role="main" id="left-panel" class="ui-content" data-position="left">
   
    </div>

    <div data-role="" data-position="fixed">
        <!-- <div class="nav_left_button"><a href="#" class="nav-toggle"><span></span></a></div> -->
        <div class="nav_center_logo"></div>
        <!-- <div class="nav_right_button"><a href="#right-panel"><img src="images/icons/white/user.png" alt="" title="" /></a></div> -->
        <div class="clear"></div>
    </div>
            <div role="main" class="ui-content">

        <div class="logo_container">
            <div class="logo">
            <!-- <img src="<?= base_url().'css/images/logo3.png'; ?>" alt="shell" title="shell" /> -->
            <h2></h2>
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
          showConfirmButton: false,

        });
        setTimeout(function(){
          window.location.href= "<?=base_url().'app/'?>";
        },4000);
      // $.mobile.activePage.find( "#left-panel" ).panel( "open" );
        
      }      
    }

  });

 // $(document).ready(function(){
 //        $.mobile.activePage.find( "#left-panel" ).panel( "open" );

 // })
</script>