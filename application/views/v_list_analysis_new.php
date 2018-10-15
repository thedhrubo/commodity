<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

    <?php require_once 'elements/head.php'; ?>
    <style type="text/css">
        #pagination{
            margin: 40 40 0;
        }
        .input_text {
            display: inline;
            margin: 100px;
        }
        .input_name {
            display: inline;
            margin: 65px;
        }
        .input_email {
            display: inline;
            margin-left: 73px;
        }
        .input_num {
            display: inline;
            margin: 36px;
        }
        .input_country {
            display: inline;
            margin: 53px;
        }
        ul.tsc_pagination li a
        {
            border:solid 1px;
            border-radius:3px;
            -moz-border-radius:3px;
            -webkit-border-radius:3px;
            padding:6px 9px 6px 9px;
        }
        ul.tsc_pagination li
        {
            padding-bottom:1px;
        }
        ul.tsc_pagination li a:hover,
        ul.tsc_pagination li a.current
        {
            color:#FFFFFF;
            box-shadow:0px 1px #EDEDED;
            -moz-box-shadow:0px 1px #EDEDED;
            -webkit-box-shadow:0px 1px #EDEDED;
        }
        ul.tsc_pagination
        {
            margin:4px 0;
            padding:0px;
            height:100%;
            overflow:hidden;
            font:12px 'Tahoma';
            list-style-type:none;
        }
        ul.tsc_pagination li
        {
            float:left;
            margin:0px;
            padding:0px;
            margin-left:5px;
        }
        ul.tsc_pagination li a
        {
            color:black;
            display:block;
            text-decoration:none;
            padding:7px 10px 7px 10px;
        }
        ul.tsc_pagination li a img
        {
            border:none;
        }
        ul.tsc_pagination li a
        {
            color:#0A7EC5;
            border-color:#8DC5E6;
            background:#F8FCFF;
        }
        ul.tsc_pagination li a:hover,
        ul.tsc_pagination li a.current
        {
            text-shadow:0px 1px #388DBE;
            border-color:#3390CA;
            background:#58B0E7;
            background:-moz-linear-gradient(top, #B4F6FF 1px, #63D0FE 1px, #58B0E7);
            background:-webkit-gradient(linear, 0 0, 0 100%, color-stop(0.02, #B4F6FF), color-stop(0.02, #63D0FE), color-stop(1, #58B0E7));
        }
    </style>
    <body>
        <?php require_once 'elements/header.php'; ?>

    <section id="services" class="section section-padded">
        <h3><?php echo $analysis_kind; ?></h3>
        <div class="col-md-8">
            <ul>
                <?php
                if (count($date_array) > 0) {
                   // foreach ($date_array as $aaa) {
                        while ($each_item = $date_array->fetch_assoc()) {
                            $firstdate = $each_item['stock_date_1'];
                            $third_date = $each_item['stock_date_' . $row];
                            $first_weekday_num = date('w', strtotime($firstdate));
                            $third_weekday_num = date('w', strtotime($third_date));
                            if ($first_weekday_num == 1)
                                $startdate = date('Y-m-d', strtotime('-4 day', strtotime($firstdate)));
                            else if ($first_weekday_num == 2)
                                $startdate = date('Y-m-d', strtotime('-4 day', strtotime($firstdate)));
                            else if ($first_weekday_num == 3)
                                $startdate = date('Y-m-d', strtotime('-2 day', strtotime($firstdate)));
                            else if ($first_weekday_num == 4)
                                $startdate = date('Y-m-d', strtotime('-2 day', strtotime($firstdate)));
                            else
                                $startdate = date('Y-m-d', strtotime('-2 day', strtotime($firstdate)));


                            if ($third_weekday_num == 1)
                                $finaldate = date('Y-m-d', strtotime('+2 day', strtotime($third_date)));
                            else if ($third_weekday_num == 2)
                                $finaldate = date('Y-m-d', strtotime('+2 day', strtotime($third_date)));
                            else if ($third_weekday_num == 3)
                                $finaldate = date('Y-m-d', strtotime('+2 day', strtotime($third_date)));
                            else if ($third_weekday_num == 4)
                                $finaldate = date('Y-m-d', strtotime('+4 day', strtotime($third_date)));
                            else
                                $finaldate = date('Y-m-d', strtotime('+4 day', strtotime($third_date)));
                            ?>
                            <?php
                            $dateStr = '';
                            for ($i = 1; $i <= $row; $i++) {
                                $dateStr .=$each_item['stock_date_' . $i] . '/';
                            }
                            ?>

                            <li style="margin-bottom: 3px;">
                                <div style="width:20%;float: left"> Match ( <?php echo round(!empty($each_item['deviation']) ? $each_item['deviation'] : $each_item['outputdeviation'], 2); ?>% )</div>
                                <form target="_blank" style="width:10%;float: left" action="<?php echo site_url('welcome/individual_graph2/'); ?>" method="post">
                                    <input type="hidden" name="startdate" value="<?php echo $startdate ?>">
                                    <input type="hidden" name="finaldate" value="<?php echo $finaldate ?>">
                                    <input type="hidden" name="sellmonth" value="<?php echo!empty($each_item['sellmonth']) ? $each_item['sellmonth'] : $each_item['sellmonth_1']; ?>">
                                    <input type="hidden" name="sellyear" value="<?php echo!empty($each_item['sellyear']) ? $each_item['sellyear'] : $each_item['sellyear_1']; ?>">
                                    <input type="hidden" name="commodity_name" value="<?php echo $commodity_name; ?>">
                                    <input type="hidden" name="date" value="<?php echo $dateStr; ?>">
                                    <input type="hidden" name="inputParameter" value="<?php echo base64_encode(serialize($inputParameter)); ?>">
                                    <input type="submit" class="btn-primary btn-sm" value="New Chart">
                                </form>
                                <form target="_blank" style="width:10%;float: left" action="<?php echo site_url('welcome/individual_graph/'); ?>" method="post">
                                    <input type="hidden" name="startdate" value="<?php echo $startdate ?>">
                                    <input type="hidden" name="finaldate" value="<?php echo $finaldate ?>">
                                    <input type="hidden" name="sellmonth" value="<?php echo!empty($each_item['sellmonth']) ? $each_item['sellmonth'] : $each_item['sellmonth_1']; ?>">
                                    <input type="hidden" name="sellyear" value="<?php echo!empty($each_item['sellyear']) ? $each_item['sellyear'] : $each_item['sellyear_1']; ?>">
                                    <input type="hidden" name="commodity_name" value="<?php echo $commodity_name; ?>">
                                    <input type="hidden" name="date" value="<?php echo $dateStr; ?>">
                                    <input type="submit" class="btn-primary btn-sm" value="Old Chart">
                                </form>
                                <div style="clear: both"></div>
                <!--                    <a href="<?php echo site_url("welcome/individual_graph2/" . $startdate . "/" . $finaldate . "/" . $each_item['sellmonth'] . "/" . $each_item['sellyear'] . "/" . $commodity_name . "/" . $dateStr); ?>" target="_blank">New Chart</a>
                                /<a href="<?php echo site_url("welcome/individual_graph/" . $startdate . "/" . $finaldate . "/" . $each_item['sellmonth'] . "/" . $each_item['sellyear'] . "/" . $commodity_name . "/" . $dateStr); ?>" target="_blank">Old Chart</a>-->
                            </li>
                            <?php
              //          }
                    }
                } else
                    echo "There is no data matching with the input value";
                ?>

            </ul>
        </div>
        <div class="col-md-4">
            <?php
            if (count($date_array) > 0 && isset($other_info['close_up'])) {
                $total_row = $other_info['close_up'] + $other_info['close_down'] + $other_info['close_equal'];
                ?>
                <ul>
                    <li><b> Close up : <?php echo number_format(($other_info['close_up'] * 100) / $total_row, 2); ?>%</b></li>
                    <li><b> Close down : <?php echo number_format(($other_info['close_down'] * 100) / $total_row, 2); ?>%</b></li>
                    <li><b> Close Equal : <?php echo number_format(($other_info['close_equal'] * 100) / $total_row, 2); ?>%</b></li>
                </ul>
            <?php } ?>
            <hr>
            <?php
            if (count($date_array) > 0 && isset($other_info['move_up'])) {
                $total_row = $other_info['move_up'] + $other_info['move_down'] + $other_info['move_equal'];
                ?>
                <ul>
                    <li><b> Dominant move up : <?php echo number_format(($other_info['move_up'] * 100) / $total_row, 2); ?>%</b></li>
                    <li><b> Dominant move down : <?php echo number_format(($other_info['move_down'] * 100) / $total_row, 2); ?>%</b></li>
                    <li><b> Dominant move Equal : <?php echo number_format(($other_info['move_equal'] * 100) / $total_row, 2); ?>%</b></li>
                </ul>
            <?php } ?>
            <hr>
            <?php
            if (count($date_array) > 0 && isset($other_info['move_up_average'])) {
                ?>
                <ul>
                    <li><b> Move up average: <?php echo number_format($other_info['move_up_average'], 2); ?></b></li>
                    <li><b> Move down average : <?php echo number_format($other_info['move_down_average'], 2); ?></b></li>
                </ul>
            <?php } ?>
        </div>
        <div class="clearfix"></div>
    </section>
    <div id="pagination">
        <ul class="tsc_pagination">

            <!-- Show pagination links -->
            <?php
            foreach ($links as $link) {
                echo "<li>" . $link . "</li>";
            }
            ?>
    </div>

    <?php require_once 'elements/footer.php'; ?>

</body>

</html>



