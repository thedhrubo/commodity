<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

    <?php require_once 'elements/head.php'; ?>

    <body>
        <?php require_once 'elements/header.php'; ?>

    <section id="services" class="section section-padded">
        <style>

            .bar{
                fill: steelblue;
            }

            .bar:hover{
                fill: brown;
            }

            .axis {
                font: 10px sans-serif;
            }

            .axis path,
            .axis line {
                fill: none;
                stroke: #000;
                shape-rendering: crispEdges;
            }

            /* to style the tooltip */


            .d3-tip {
                line-height: 1;
                font-weight: bold;
                padding: 12px;
                background: rgba(0, 0, 0, 0.8);
                color: #fff;
                border-radius: 2px;
            }

            /* Creates a small triangle extender for the tooltip */
            .d3-tip:after {
                box-sizing: border-box;
                display: inline;
                font-size: 10px;
                width: 100%;
                line-height: 1;
                color: rgba(0, 0, 0, 0.8);
                content: "\25BC";
                position: absolute;
                text-align: center;
            }

            /* Style northward tooltips differently */
            .d3-tip.n:after {
                margin: -1px 0 0 0;
                top: 100%;
                left: 0;
            }


        </style>

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
            <div id="open_chart"></div>
            <div id="high_chart"></div>
            <div id="low_chart"></div>
            <div id="close_chart"></div>

            <script src="http://d3js.org/d3.v3.min.js"></script>
            <script src="http://labratrevenge.com/d3-tip/javascripts/d3.tip.v0.6.3.js"></script>
            <script>
                // set the dimensions of the canvas
                var margin = {top: 20, right: 20, bottom: 70, left: 40},
                width = 600 - margin.left - margin.right,
                        height = 300 - margin.top - margin.bottom;


                // set the ranges
                var x = d3.scale.ordinal().rangeRoundBands([0, width], .05);

                var y = d3.scale.linear().range([height, 0]);

                // define the axis
                var xAxis = d3.svg.axis()
                        .scale(x)
                        .orient("bottom")


                var yAxis = d3.svg.axis()
                        .scale(y)
                        .orient("left")
                        .ticks(10);


                // add the SVG element
                var open_svg = d3.select("#open_chart").append("svg")
                        .attr("width", width + margin.left + margin.right)
                        .attr("height", height + margin.top + margin.bottom)
                        .append("g")
                        .attr("transform",
                                "translate(" + margin.left + "," + margin.top + ")");

                var open_tip = d3.tip()
                        .attr('class', 'd3-tip')
                        .offset([-10, 0])
                        .html(function (d) {
                            return "<strong>Open Price:</strong> <span style='color:red'>" + d.open_price + "</span>";
                        });

                open_svg.call(open_tip);

                var high_svg = d3.select("#open_chart").append("svg")
                        .attr("width", width + margin.left + margin.right)
                        .attr("height", height + margin.top + margin.bottom)
                        .append("g")
                        .attr("transform",
                                "translate(" + margin.left + "," + margin.top + ")");

                var high_tip = d3.tip()
                        .attr('class', 'd3-tip')
                        .offset([-10, 0])
                        .html(function (d) {
                            return "<strong>High Price:</strong> <span style='color:red'>" + d.high_price + "</span>";
                        });

                high_svg.call(high_tip);

                var low_svg = d3.select("#open_chart").append("svg")
                        .attr("width", width + margin.left + margin.right)
                        .attr("height", height + margin.top + margin.bottom)
                        .append("g")
                        .attr("transform",
                                "translate(" + margin.left + "," + margin.top + ")");

                var low_tip = d3.tip()
                        .attr('class', 'd3-tip')
                        .offset([-10, 0])
                        .html(function (d) {
                            return "<strong>Low Price:</strong> <span style='color:red'>" + d.low_price + "</span>";
                        });

                low_svg.call(low_tip);

                var close_svg = d3.select("#open_chart").append("svg")
                        .attr("width", width + margin.left + margin.right)
                        .attr("height", height + margin.top + margin.bottom)
                        .append("g")
                        .attr("transform",
                                "translate(" + margin.left + "," + margin.top + ")");

                var close_tip = d3.tip()
                        .attr('class', 'd3-tip')
                        .offset([-10, 0])
                        .html(function (d) {
                            return "<strong>Close Price:</strong> <span style='color:red'>" + d.close_price + "</span>";
                        })

                close_svg.call(close_tip);

                // load the data
                var fromdate = '<?php echo $fromdate; ?>';
                var todate = '<?php echo $todate; ?>';
                var sellmonth = '<?php echo $sellmonth; ?>';
                var sellyear = '<?php echo $sellyear; ?>';
                var commodity_name = '<?php echo $commodity_name; ?>';
                var url = "<?php echo site_url('welcome/readjson/'); ?>/" + fromdate + "/" + todate + "/" + sellmonth + "/" + sellyear + "/" + commodity_name;
        //            var url = "http://localhost/stock/welcome/readjson/" + fromdate + "/" + todate + "/" + sellmonth + "/"+sellyear;
                d3.json(url, function (error, data) {

                    data.forEach(function (d) {
                        d.open_price = d.open_price;
                        //alert(d.stockdate);
                        //var stock_date = new Date(d.stockdate);
                        var stock_date = new Date(d.stockdate.replace(/-/g, '\/'));
                        var stock_date_str = (stock_date.getMonth() + 1) + "-" + stock_date.getDate() + "-" + stock_date.getFullYear();
                        d.stockdate = stock_date_str;
                        //alert(d.stockdate);
                    });

                    // scale the range of the data
                    x.domain(data.map(function (d) {
                        return d.stockdate;
                    }));
                    y.domain([0, d3.max(data, function (d) {
                            return d.open_price;
                        })]);

                    // add axis
                    open_svg.append("g")
                            .attr("class", "x axis")
                            .attr("transform", "translate(0," + height + ")")
                            .call(xAxis)
                            .selectAll("text")
                            .style("text-anchor", "end")
                            .attr("dx", "-.8em")
                            .attr("dy", "-.55em")
                            .attr("transform", "rotate(-90)");

                    open_svg.append("g")
                            .attr("class", "y axis")
                            .call(yAxis)
                            .append("text")
                            .attr("transform", "rotate(-90)")
                            .attr("y", 5)
                            .attr("dy", ".71em")
                            .style("text-anchor", "end");
                    //.text("Frequency");


                    // Add bar chart
                    open_svg.selectAll("bar")
                            .data(data)
                            .enter().append("rect")
                            .attr("class", "bar")
                            .attr("x", function (d) {
                                return x(d.stockdate);
                            })
                            .attr("width", x.rangeBand())
                            .attr("y", function (d) {
                                return y(d.open_price);
                            })
                            .attr("height", function (d) {
                                return height - y(d.open_price);
                            })
                            .on('mouseover', open_tip.show)
                            .on('mouseout', open_tip.hide);

                });


                d3.json(url, function (error, data) {

                    data.forEach(function (d) {
                        d.high_price = d.highest_price;
                        var stock_date = new Date(d.stockdate.replace(/-/g, '\/'));
                        var stock_date_str = (stock_date.getMonth() + 1) + "-" + stock_date.getDate() + "-" + stock_date.getFullYear();
                        d.stockdate = stock_date_str;
                    });

                    // scale the range of the data
                    x.domain(data.map(function (d) {
                        return d.stockdate;
                    }));
                    y.domain([0, d3.max(data, function (d) {
                            return d.high_price;
                        })]);

                    // add axis
                    high_svg.append("g")
                            .attr("class", "x axis")
                            .attr("transform", "translate(0," + height + ")")
                            .call(xAxis)
                            .selectAll("text")
                            .style("text-anchor", "end")
                            .attr("dx", "-.8em")
                            .attr("dy", "-.55em")
                            .attr("transform", "rotate(-90)");

                    high_svg.append("g")
                            .attr("class", "y axis")
                            .call(yAxis)
                            .append("text")
                            .attr("transform", "rotate(-90)")
                            .attr("y", 5)
                            .attr("dy", ".71em")
                            .style("text-anchor", "end");
                    //.text("Frequency");


                    // Add bar chart
                    high_svg.selectAll("bar")
                            .data(data)
                            .enter().append("rect")
                            .attr("class", "bar")
                            .attr("x", function (d) {
                                return x(d.stockdate);
                            })
                            .attr("width", x.rangeBand())
                            .attr("y", function (d) {
                                return y(d.high_price);
                            })
                            .attr("height", function (d) {
                                return height - y(d.high_price);
                            })
                            .on('mouseover', high_tip.show)
                            .on('mouseout', high_tip.hide);
                });
                d3.json(url, function (error, data) {

                    data.forEach(function (d) {
                        d.low_price = d.lowest_price;
                        var stock_date = new Date(d.stockdate.replace(/-/g, '\/'));
                        var stock_date_str = (stock_date.getMonth() + 1) + "-" + stock_date.getDate() + "-" + stock_date.getFullYear();
                        d.stockdate = stock_date_str;
                    });

                    // scale the range of the data
                    x.domain(data.map(function (d) {
                        return d.stockdate;
                    }));
                    y.domain([0, d3.max(data, function (d) {
                            return d.low_price;
                        })]);

                    // add axis
                    low_svg.append("g")
                            .attr("class", "x axis")
                            .attr("transform", "translate(0," + height + ")")
                            .call(xAxis)
                            .selectAll("text")
                            .style("text-anchor", "end")
                            .attr("dx", "-.8em")
                            .attr("dy", "-.55em")
                            .attr("transform", "rotate(-90)");

                    low_svg.append("g")
                            .attr("class", "y axis")
                            .call(yAxis)
                            .append("text")
                            .attr("transform", "rotate(-90)")
                            .attr("y", 5)
                            .attr("dy", ".71em")
                            .style("text-anchor", "end");
                    //.text("Frequency");


                    // Add bar chart
                    low_svg.selectAll("bar")
                            .data(data)
                            .enter().append("rect")
                            .attr("class", "bar")
                            .attr("x", function (d) {
                                return x(d.stockdate);
                            })
                            .attr("width", x.rangeBand())
                            .attr("y", function (d) {
                                return y(d.low_price);
                            })
                            .attr("height", function (d) {
                                return height - y(d.low_price);
                            })
                            .on('mouseover', low_tip.show)
                            .on('mouseout', low_tip.hide);

                });
                d3.json(url, function (error, data) {

                    data.forEach(function (d) {
                        d.close_price = d.closed_price;
                        var stock_date = new Date(d.stockdate.replace(/-/g, '\/'));
                        var stock_date_str = (stock_date.getMonth() + 1) + "-" + stock_date.getDate() + "-" + stock_date.getFullYear();
                        d.stockdate = stock_date_str;
                    });

                    // scale the range of the data
                    x.domain(data.map(function (d) {
                        return d.stockdate;
                    }));
                    y.domain([0, d3.max(data, function (d) {
                            return d.close_price;
                        })]);

                    // add axis
                    close_svg.append("g")
                            .attr("class", "x axis")
                            .attr("transform", "translate(0," + height + ")")
                            .call(xAxis)
                            .selectAll("text")
                            .style("text-anchor", "end")
                            .attr("dx", "-.8em")
                            .attr("dy", "-.55em")
                            .attr("transform", "rotate(-90)");

                    close_svg.append("g")
                            .attr("class", "y axis")
                            .call(yAxis)
                            .append("text")
                            .attr("transform", "rotate(-90)")
                            .attr("y", 5)
                            .attr("dy", ".71em")
                            .style("text-anchor", "end");
                    //.text("Frequency");


                    // Add bar chart
                    close_svg.selectAll("bar")
                            .data(data)
                            .enter().append("rect")
                            .attr("class", "bar")
                            .attr("x", function (d) {
                                return x(d.stockdate);
                            })
                            .attr("width", x.rangeBand())
                            .attr("y", function (d) {
                                return y(d.close_price);
                            })
                            .attr("height", function (d) {
                                return height - y(d.close_price);
                            })
                            .on('mouseover', close_tip.show)
                            .on('mouseout', close_tip.hide);
                });
            </script>

    </section>

    <?php require_once 'elements/footer.php'; ?>

</body>

</html>



<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

