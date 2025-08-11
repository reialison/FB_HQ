
            <div class="page-content-wrapper">             
                <!-- Content Header (Page header) -->
                <div class="page-content">
                    <div class="page-bar">
                        <ul class="page-breadcrumb">
                            <li>
                                <?php echo '<a href="'.base_url().'dashboard">Home</a>' ?>
                                    <i class="fa fa-circle"></i>
                            </li>
                                <li>
                                    <span>
                                    <?php
                                        if(isset($page_title))
                                        echo $page_title;
                                        ?>
                                    </span>
                                </li>                                
                            </li>
                        </ul>
                        <ul class="page-breadcrumb1" style="width: 200px;">
                            <li>
                                <span id="_branch_code" style="text-align: right;"></span>                                    
                            </li>
                        </ul>
                    </div>
                
                    <h3 class="page-title">
                        <?php
                            if(isset($page_title))
                                echo $page_title;
                        ?>
                    </h1>
                    <!-- <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li><a href="#">Examples</a></li>
                        <li class="active">Blank page</li>
                    </ol> -->
                
                <!-- Main content -->
                <section class="content <?php if(isset($page_no_padding) && $page_no_padding) echo ' no-padding'; ?>">
                    <?php 
                        if(isset($code))
                            echo $code; 
                    ?>
                </section><!-- /.content -->
            </div>    
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
        <?php
            if(isset($js))
                echo $js;
        ?> 
        <?php 
            if(isset($add_js)){
                if(is_array($add_js)){
                    foreach ($add_js as $path) {
                        echo '<script src="'.base_url().$path.'" type="text/javascript"  language="JavaScript"></script>';
                    }
                }
                else
                     echo '<script src="'.base_url().$add_js.'" type="text/javascript"  language="JavaScript"></script>';
            }
        ?> 