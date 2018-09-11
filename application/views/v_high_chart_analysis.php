<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

    <?php require_once 'elements/head.php'; ?>

    <body>
        <?php require_once 'elements/header.php'; ?>

    <section id="services" class="section section-padded">

        <script src="<?php echo site_url(); ?>js/highstock.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
        <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" />
        <script type="text/javascript">
            var fromdate = '<?php echo $fromdate; ?>';
            var fromdateSplit = fromdate.split("-");
            var fromdateSecond = '<?php echo strtotime($fromdate) * 1000; ?>';
            var todate = '<?php echo $todate; ?>';
            var todateSplit = todate.split("-");
            var todateSecond = '<?php echo strtotime($todate) * 1000; ?>';
            var sellmonth = '<?php echo $sellmonth; ?>';
            var sellyear = '<?php echo $sellyear; ?>';
            var commodity_name = '<?php echo $commodity_name; ?>';
            var url = '<?php echo site_url("welcome/readjson2/"); ?>/' + fromdate + "/" + todate + "/" + sellmonth + "/" + sellyear + "/" + commodity_name;

            var example = <?php echo $inputStr;?>;
//            var example = [[144543600000, 244.5, 244.5, 232.5, 232.5], [144630000000, 223.5, 223.5, 223.5, 223.5], [144975600000, 223, 223, 223, 223], [145062000000, 221.5, 221.5, 221.5, 221.5], [145148400000, 212, 212, 212, 212]]


            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            $.getJSON(url, function (data) {


                Highcharts.setOptions({
                    global: {
                        timezoneOffset: -7 * 60
                    }
                });



                var chart = Highcharts.stockChart('container', {
                    rangeSelector: {
                        selected: 0
                    },
                    title: {
                        text: capitalizeFirstLetter(commodity_name) + ' Commodity'
                    },
                    xAxis: {
                        type: 'datetime',
                        tickInterval: 24 * 3600 * 1000
                    },
                    //                tooltip:{
                    //                    formatter:function(){
                    //                        return Highcharts.dateFormat('%e - %b - %Y',
                    //                                              new Date(this.x));
                    //                    }
                    //                },
                    series: [{
                            type: 'ohlc',
                            name: capitalizeFirstLetter(commodity_name) + ' Commodity',
                            //pointInterval: 24 * 3600 * 1000,
                            data: data,
                            dataGrouping: {
                                //                            enabled: false,
                                units: [[
                                        'day', // unit name
                                        [1] // allowed multiples
                                    ], [
                                        'month',
                                        [1, 2, 3, 4, 6]
                                    ]]
                            }
                        }]
                }, function () {
                    setTimeout(function () {
                        $('input.highcharts-range-selector', $(chart.container).parent()).datepicker();
                        chart.xAxis[0].setExtremes(Date.UTC(fromdateSplit[0], fromdateSplit[1] - 1, fromdateSplit[2]),
                                Date.UTC(todateSplit[0], todateSplit[1] - 1, todateSplit[2]));
                    }, 0);
                });
                $('#button').click(function () {
                    chart.xAxis[0].setExtremes(Date.UTC(fromdateSplit[0], fromdateSplit[1] - 1, fromdateSplit[2]),
                            Date.UTC(todateSplit[0], todateSplit[1] - 1, todateSplit[2]));
                });

            });


            

//            $.getJSON(url, function (data) {
//
//
//                Highcharts.setOptions({
//                    global: {
//                        timezoneOffset: -7 * 60
//                    }
//                });
//                var chart = Highcharts.stockChart('Primecontainer', {
//                    rangeSelector: {
//                        selected: 0
//                    },
//                    title: {
//                        text: capitalizeFirstLetter(commodity_name) + ' Commodity'
//                    },
//                    xAxis: {
//                        type: 'datetime',
//                        tickInterval: 24 * 3600 * 1000
//                    },
//                    //                tooltip:{
//                    //                    formatter:function(){
//                    //                        return Highcharts.dateFormat('%e - %b - %Y',
//                    //                                              new Date(this.x));
//                    //                    }
//                    //                },
//                    series: [{
//                            type: 'ohlc',
//                            name: capitalizeFirstLetter(commodity_name) + ' Commodity',
//                            //pointInterval: 24 * 3600 * 1000,
//                            data: data,
//                            dataGrouping: {
//                                //                            enabled: false,
//                                units: [[
//                                        'day', // unit name
//                                        [1] // allowed multiples
//                                    ], [
//                                        'month',
//                                        [1, 2, 3, 4, 6]
//                                    ]]
//                            }
//                        }]
//                }, function () {
//                    setTimeout(function () {
//                        $('input.highcharts-range-selector', $(chart.container).parent()).datepicker();
//                        chart.xAxis[0].setExtremes(Date.UTC(fromdateSplit[0], fromdateSplit[1] - 1, fromdateSplit[2]),
//                                Date.UTC(todateSplit[0], todateSplit[1] - 1, todateSplit[2]));
//                    }, 0);
//                });
//                $('#button').click(function () {
//                    chart.xAxis[0].setExtremes(Date.UTC(fromdateSplit[0], fromdateSplit[1] - 1, fromdateSplit[2]),
//                            Date.UTC(todateSplit[0], todateSplit[1] - 1, todateSplit[2]));
//                });
//
//            });



            // Set the datepicker's date format
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: "1901:2025",
                onSelect: function (dateText) {
                    this.onchange();
                    this.onblur();
                }
            });

            var matched, browser;

            jQuery.uaMatch = function (ua) {
                ua = ua.toLowerCase();

                var match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
                        /(webkit)[ \/]([\w.]+)/.exec(ua) ||
                        /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
                        /(msie) ([\w.]+)/.exec(ua) ||
                        ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
                        [];

                return {
                    browser: match[ 1 ] || "",
                    version: match[ 2 ] || "0"
                };
            };

            matched = jQuery.uaMatch(navigator.userAgent);
            browser = {};

            if (matched.browser) {
                browser[ matched.browser ] = true;
                browser.version = matched.version;
            }

            // Chrome is Webkit, but Webkit is also Safari.
            if (browser.chrome) {
                browser.webkit = true;
            } else if (browser.webkit) {
                browser.safari = true;
            }

            jQuery.browser = browser;

        </script>

    </head>

    <body style="padding-top:20px;">
        <div>From Date : <?php echo date("m/d/Y", strtotime($fromdate)); ?></div>
        <div>To Date : <?php echo date("m/d/Y", strtotime($todate)); ?></div>
        <div>Selling Year : <?php echo $sellyear; ?></div>
        <div>Selling Month : 
            <?php
            if ($sellmonth == "F")
                echo "January";
            else if ($sellmonth == "G")
                echo "February";
            else if ($sellmonth == "H")
                echo "March";
            else if ($sellmonth == "J")
                echo "April";
            else if ($sellmonth == "K")
                echo "May";
            else if ($sellmonth == "M")
                echo "June";
            else if ($sellmonth == "N")
                echo "July";
            else if ($sellmonth == "Q")
                echo "August";
            else if ($sellmonth == "U")
                echo "September";
            else if ($sellmonth == "V")
                echo "October";
            else if ($sellmonth == "X")
                echo "November";
            else
                echo "December";
            ?>
        </div>
        <div>Commodity Name : <?php echo ucfirst($commodity_name); ?></div>
        <div>Matched First Date : <?php echo date("m/d/Y", strtotime($date[0])); ?></div>
        <div>Matched Second Date : <?php echo date("m/d/Y", strtotime($date[1])); ?></div>
        <div>Matched Third Date : <?php echo date("m/d/Y", strtotime($date[2])); ?></div>
        <?php
        $d = $date;
        unset($d[0]);
        unset($d[1]);
        unset($d[2]);
        $d = array_values($d);
        foreach ($d as $k => $dt) {
            if (empty($dt))
                continue;
            $r = $k + 4;
            ?>
            <div>Matched <?php echo $r; ?>th Date : <?php echo date("m/d/Y", strtotime($dt)); ?></div>
        <?php } ?>
        
        <div id="container" style="height: 400px; min-width: 310px"></div>
        <div id="Primecontainer" style="height: 400px; min-width: 310px"></div>
    <button id="button" class="autocompare">Set extremes</button>
</body>  




</section>

<?php require_once 'elements/footer.php'; ?>

</body>

<script type="text/javascript">
    var options = {
                chart: {
                    renderTo: 'Primecontainer',
                    type: 'line'
                },
                xAxis: {
                    type: 'datetime'

                },
                yAxis: {
                    title: {
                        text: 'test'
                    }

                },
                series: [{
                        data: example,
                        type: 'ohlc',
                    }]

            };
            var chart = new Highcharts.Chart(options);
    </script>

</html>



<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

