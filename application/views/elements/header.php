<div class="preloader">
    <img src="<?php echo site_url(); ?>img/loader.gif" alt="Preloader image">
</div>
<nav class="navbar">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><img src="<?php echo site_url(); ?>img/logo.png" data-active-url="<?php echo site_url(); ?>img/logo-active.png" alt=""></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right main-nav">
                <?php if ($this->session->userdata('user_id')) { ?><li><a href="<?php echo site_url("welcome/full_analysis"); ?>">Home</a></li><?php } ?>
                <?php if ($this->session->userdata('user_id')) { ?><li><a href="<?php echo site_url("welcome/fifteenth_analysisClosePrice2ndPhase"); ?>">15 Matches 2nd Phase</a></li><?php } ?>
                <?php if ($this->session->userdata('user_id')) { ?><li><a href="<?php echo site_url("welcome/logout"); ?>">Logout</a></li><?php } ?>
                <!--					<li><a href="#team">Team</a></li>
                                                        <li><a href="#pricing">Pricing</a></li>
                                                        <li><a href="#" data-toggle="modal" data-target="#modal1" class="btn btn-blue">Sign Up</a></li>-->
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
