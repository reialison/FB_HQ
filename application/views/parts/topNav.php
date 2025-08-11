        <div class="page-header navbar navbar-fixed-top">
            <div class="page-header-inner ">
                <div class="page-logo">
                    <!-- <a href="<?php echo base_url().'cashier'; ?>" class="mtop4" > -->
                    <a href="#" class="mtop4" >
                        <img src = '<?php echo base_url(); ?>img/clickLogo.png' height="35" >
                    </a>
                    
                </div>
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
                <!-- Header Navbar: style can be found in header.less -->
                <!--<nav class="navbar navbar-static-top" role="navigation">-->
                <!-- Sidebar toggle button-->
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->

                        <!-- <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <img alt="" class="img-circle" src="<?php echo $user_img; ?>" height="29" width="29"/>
                                <span class="username username-hide-on-mobile"> <?php echo $user["full_name"] ?></span>
                            </a>
                        </li> -->
                        <li class="dropdown dropdown-user">
                            <a href="<?php echo base_url().'user/users_form/'.$user["id"]; ?>" class="dropdown-toggle"  data-close-others="true">
                                <img alt="" class="img-circle" src="<?php echo $user_img; ?>" height="29" width="29"/>
                                <span class="username username-hide-on-mobile"> <?php echo $user["full_name"] ?></span>
                            </a>
                        </li>
                        <!-- <li class="dropdown dropdown-extended" id="header_inbox_bar">
                            <a href="<?php echo base_url().'cashier'; ?>" class="dropdown-toggle" data-hover="dropdown" data-close-others="true">
                                <i class="icon-screen-desktop"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default"></ul>
                        </li> -->
                        <li class="dropdown dropdown-extended">
                            <a href="<?Php echo base_url()."site/go_logout" ?>" class="dropdown-toggle" data-hover="dropdown" data-close-others="true">
                                <i class="icon-logout"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default"></ul>
                        </li>
                        <!-- Messages: style can be found in dropdown.less-->
                        
                        <!-- User Account: style can be found in dropdown.less -->
                 

<!--
                    <li class="dropdown dropdown-extended dropdown-notification" >
                        <a href="<?php echo base_url().'cashier'; ?>" class="dropdown-toggle" >
                            <i class="fa fa-desktop fa-lg fa-fw"></i>
                            <span class="username username-hide-on-mobile">Terminal</span>
                        </a>
                    </li>
                        
                    <li class="dropdown dropdown-user">
                        <a href="<?php echo base_url().'cashier'; ?>">
                            <i class="fa fa-desktop fa-lg fa-fw"></i>Terminal
                        </a>
                    </li>
-->





                                <!-- Menu Body -->
                                <!-- <li class="user-body">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Preferences</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </li> -->
                                <!-- Menu Footer-->
<!-- ======= -->
                    <!-- </ul> -->
<!-- >>>>>>> ae20e32de4b295114988ea205b812ec809f9778c -->
                        </li>
                    </ul>
                </div>
                </div>       
            </div>
        </div>
    </div>
    <div class="clearfix"> </div>

    <div class="wrapper row-offcanvas row-offcanvas-left">

        <div class="page-container">
