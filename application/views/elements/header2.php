<div class="container">
    <header>
        <a class="logo" href="<?php echo site_url(); ?>">
            <img src="<?php echo site_url(); ?>img/blueray.png" alt="Blueray">
        </a>

        <nav class="mobile">
            <a class="menu" href="#">
                <img src="<?php echo site_url(); ?>img/mobile-menu.png" alt="Menu">
            </a>
            <ul class="submenu">
                <li  class = "sub arrow-right" >

                    <ul>   
                        <?php foreach ($this->menu as $key => $menu) { ?>
                            <li <?php if($this->data['menu']==$menu['id']) echo 'class="menu_active"'; ?>>

                                <a href="<?php echo site_url().'inside/page/menu/submenu/'.$menu['id']; ?>" title=""><?php echo $menu['name']; ?></a>    
                                <ul>
                                    <?php foreach ($this->submenu[$menu['id']] as $i => $submenu) { ?>
                                        <li >
                                            
                                                    <a href="<?php echo site_url().'inside/page/menu/'.$submenu['id']; ?>" title=""><?php echo $submenu['name']; ?></a>
                                                 
                                        </li> 
                                    <?php } ?>

                                </ul>    
                            </li>  
                        <?php } ?>



                    </ul>
                </li>
            </ul>
        </nav>
        <nav>
            <div id="menu_1">
                <ul id="ul_menu_1">
                    <?php foreach ($this->menu as $key => $menu) { ?>
                        <li  class="first_menu_li <?php if($this->data['menu']==$menu['id']) echo 'menu_active'; ?>">
                            <a href="<?php echo site_url().'inside/page/menu/submenu/'.$menu['id']; ?>" title=""><?php echo $menu['name']; ?></a>
                            <ul>
                                <?php foreach ($this->submenu[$menu['id']] as $i => $submenu) { ?>
                                    <li  class="daddy" class="first_menu_li">
                                        
                                                <a href="<?php echo site_url().'inside/page/submenu/'.$submenu['id']; ?>" title=""><?php echo $submenu['name']; ?></a>
                                     

                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
        <ul id="ul_link_list_1_link_list">
            <li class="item1" class="ul_link_list_1_link_list_first">
                <a href="#"><img src="<?php echo site_url(); ?>img/twitter%5Ficon.png " alt="Blueray Twitter"></a>
                <style>#ul_link_list_1_link_list li.item1 { margin: 5px 15px 5px 0px;}</style>
            </li>
            <li class="item2">
                <a href="#"><img src="<?php echo site_url(); ?>img/fb%5Ficon.png " alt="Blueray Facebook"></a>
            </li>
            <li class="item3" class="ul_link_list_1_link_list_last">
                <a href="#"><img src="<?php echo site_url(); ?>img/linkedin_icon.png" alt="Blueray LinkedIn"></a>
            </li>
        </ul>
        <img class="cover" src="<?php echo site_url(); ?>img/trueblue%5F960x290%5Fimg1-1.jpg" alt="">
        <div class="cover-label">
            We Do the Hard Work
        </div>
    </header>