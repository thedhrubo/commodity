<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class M_common extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    /*
      @This function is used for fetching all the data which matched for all kinf of combined matching with each others.
      @ 3 Parameter Required = $parameter, $table_name, $row
      @ $parameter = All posted value exists into this parameter.
      @ $table_name = in which table will for search
      @ $row = How many days user defined.
      @ return = After fatching all combinational data.
     * 
     *     /*
     * This function is actively using in the project.
     * 
     *
     */

    function get_total_matching_diff_full($parameter, $table_name, $row, $count = 0, $perPage = 20, $pageNumber = 1) {
        $difference = $parameter['difference']; // tacking the difference from the $paramaeter array
        $select = "SELECT";
        $from = ' FROM ';
        $where = ' WHERE';
        $open = $parameter['open']; // tacking the open values from the $paramaeter array
        $high = $parameter['high']; // tacking the high values from the $paramaeter array
        $low = $parameter['low']; // tacking the low values from the $paramaeter array
        $close = $parameter['close']; // tacking the close values from the $paramaeter array
        $otherWhere = '';
        $yearWhere = '';
        for ($i = 1; $i <= $row; $i++) { // run a loop for number of days entered
            $nextRow = $i + 1; // taking the next row
            if ($i == 1)  // checking if this is first row for making decision that it will include comma or not as because this is dynamic query generation
                $select.=" s$i.id as id_$i,s$i.open_price as open_price_$i,s$i.highest_price as highest_price_$i,s$i.lowest_price as lowest_price_$i, s$i.closed_price as closed_price_$i, s$i.stockdate as stock_date_$i";
            else
                $select.=" ,s$i.id as id_$i,s$i.open_price as open_price_$i,s$i.highest_price as highest_price_$i,s$i.lowest_price as lowest_price_$i, s$i.closed_price as closed_price_$i, s$i.stockdate as stock_date_$i";
            if ($i == 1) // checking if this is first row for making decision that it will include comma or not as because this is dynamic query generation
                $from.=$table_name . ' as s' . $i . ' ';
            else
                $from.=' ,' . $table_name . ' as s' . $i . ' ';

            foreach ($open as $ii => $op) { // run a loop for number of opening for generate all possible combination with opening value with others for where codition
                if ($ii == 1 && $i == 1) // checking is this the first row of main loop and the the first row for inner loop
                    $where.= " (((s$i.open_price - s$ii.highest_price)/s$i.open_price)*100) BETWEEN " . (((($parameter['open'][$i] - $parameter['high'][$ii]) / $parameter['open'][$i]) * 100) - $difference) . " AND " . (((($parameter['open'][$i] - $parameter['high'][$ii]) / $parameter['open'][$i]) * 100) + $difference) . "";
                else
                    $where.= " AND (((s$i.open_price - s$ii.highest_price)/s$i.open_price)*100) BETWEEN " . (((($parameter['open'][$i] - $parameter['high'][$ii]) / $parameter['open'][$i]) * 100) - $difference) . " AND " . (((($parameter['open'][$i] - $parameter['high'][$ii]) / $parameter['open'][$i]) * 100) + $difference) . "";
                if ($i != $ii) // skip the combination with own
                    $where.= " AND (((s$i.open_price - s$ii.open_price)/s$i.open_price)*100) BETWEEN " . (((($parameter['open'][$i] - $parameter['open'][$ii]) / $parameter['open'][$i]) * 100) - $difference) . " AND " . (((($parameter['open'][$i] - $parameter['open'][$ii]) / $parameter['open'][$i]) * 100) + $difference) . "";
                $where.= " AND (((s$i.open_price - s$ii.lowest_price)/s$i.open_price)*100) BETWEEN " . (((($parameter['open'][$i] - $parameter['low'][$ii]) / $parameter['open'][$i]) * 100) - $difference) . " AND " . (((($parameter['open'][$i] - $parameter['low'][$ii]) / $parameter['open'][$i]) * 100) + $difference) . "";
                $where.= " AND (((s$i.open_price - s$ii.closed_price)/s$i.open_price)*100) BETWEEN " . (((($parameter['open'][$i] - $parameter['close'][$ii]) / $parameter['open'][$i]) * 100) - $difference) . " AND " . (((($parameter['open'][$i] - $parameter['close'][$ii]) / $parameter['open'][$i]) * 100) + $difference) . "";
            }
            foreach ($high as $ii => $hg) { // run a loop for number of high for generate all possible combination with high value with others for where codition
                if ($i != $ii) // skip the combination with own
                    $where.= " AND (((s$i.highest_price - s$ii.highest_price)/s$i.highest_price)*100) BETWEEN " . (((($parameter['high'][$i] - $parameter['high'][$ii]) / $parameter['high'][$i]) * 100) - $difference) . " AND " . (((($parameter['high'][$i] - $parameter['high'][$ii]) / $parameter['high'][$i]) * 100) + $difference) . "";

                $where.= " AND (((s$i.highest_price - s$ii.open_price)/s$i.highest_price)*100) BETWEEN " . (((($parameter['high'][$i] - $parameter['open'][$ii]) / $parameter['high'][$i]) * 100) - $difference) . " AND " . (((($parameter['high'][$i] - $parameter['open'][$ii]) / $parameter['high'][$i]) * 100) + $difference) . "";

                $where.= " AND (((s$i.highest_price - s$ii.lowest_price)/s$i.highest_price)*100) BETWEEN " . (((($parameter['high'][$i] - $parameter['low'][$ii]) / $parameter['high'][$i]) * 100) - $difference) . " AND " . (((($parameter['high'][$i] - $parameter['low'][$ii]) / $parameter['high'][$i]) * 100) + $difference) . "";
                $where.= " AND (((s$i.highest_price - s$ii.closed_price)/s$i.highest_price)*100) BETWEEN " . (((($parameter['high'][$i] - $parameter['close'][$ii]) / $parameter['high'][$i]) * 100) - $difference) . " AND " . (((($parameter['high'][$i] - $parameter['close'][$ii]) / $parameter['high'][$i]) * 100) + $difference) . "";
            }
            foreach ($low as $ii => $lw) {
                if ($i != $ii) // skip the combination with own
                    $where.= " AND (((s$i.lowest_price - s$ii.lowest_price)/s$i.lowest_price)*100) BETWEEN " . (((($parameter['low'][$i] - $parameter['low'][$ii]) / $parameter['low'][$i]) * 100) - $difference) . " AND " . (((($parameter['low'][$i] - $parameter['low'][$ii]) / $parameter['low'][$i]) * 100) + $difference) . "";

                $where.= " AND (((s$i.lowest_price - s$ii.open_price)/s$i.lowest_price)*100) BETWEEN " . (((($parameter['low'][$i] - $parameter['open'][$ii]) / $parameter['low'][$i]) * 100) - $difference) . " AND " . (((($parameter['low'][$i] - $parameter['open'][$ii]) / $parameter['low'][$i]) * 100) + $difference) . "";

                $where.= " AND (((s$i.lowest_price - s$ii.highest_price)/s$i.lowest_price)*100) BETWEEN " . (((($parameter['low'][$i] - $parameter['high'][$ii]) / $parameter['low'][$i]) * 100) - $difference) . " AND " . (((($parameter['low'][$i] - $parameter['high'][$ii]) / $parameter['low'][$i]) * 100) + $difference) . "";
                $where.= " AND (((s$i.lowest_price - s$ii.closed_price)/s$i.lowest_price)*100) BETWEEN " . (((($parameter['low'][$i] - $parameter['close'][$ii]) / $parameter['low'][$i]) * 100) - $difference) . " AND " . (((($parameter['low'][$i] - $parameter['close'][$ii]) / $parameter['low'][$i]) * 100) + $difference) . "";
            }
            foreach ($close as $ii => $cl) {
                if ($i != $ii) // skip the combination with own
                    $where.= " AND (((s$i.closed_price - s$ii.closed_price)/s$i.closed_price)*100) BETWEEN " . (((($parameter['close'][$i] - $parameter['close'][$ii]) / $parameter['close'][$i]) * 100) - $difference) . " AND " . (((($parameter['close'][$i] - $parameter['close'][$ii]) / $parameter['close'][$i]) * 100) + $difference) . "";


                $where.= " AND (((s$i.closed_price - s$ii.highest_price)/s$i.closed_price)*100) BETWEEN " . (((($parameter['close'][$i] - $parameter['high'][$ii]) / $parameter['close'][$i]) * 100) - $difference) . " AND " . (((($parameter['close'][$i] - $parameter['high'][$ii]) / $parameter['close'][$i]) * 100) + $difference) . "";

                $where.= " AND (((s$i.closed_price - s$ii.lowest_price)/s$i.closed_price)*100) BETWEEN " . (((($parameter['close'][$i] - $parameter['low'][$ii]) / $parameter['close'][$i]) * 100) - $difference) . " AND " . (((($parameter['close'][$i] - $parameter['low'][$ii]) / $parameter['close'][$i]) * 100) + $difference) . "";
                $where.= " AND (((s$i.closed_price - s$ii.open_price)/s$i.closed_price)*100) BETWEEN " . (((($parameter['close'][$i] - $parameter['open'][$ii]) / $parameter['close'][$i]) * 100) - $difference) . " AND " . (((($parameter['close'][$i] - $parameter['open'][$ii]) / $parameter['close'][$i]) * 100) + $difference) . "";
            }
            if ($i != $row) { // skip the last row
                $otherWhere .=" AND s$i.id+1=s$nextRow.id ";

                $yearWhere .=" AND s$i.sellyear = s$nextRow.sellyear and s$i.sellmonth = s$nextRow.sellmonth ";
            }
        }


        $select.=",s$row.sellmonth, s$row.sellyear";  // combine all selective column
        $query = $select . $from . $where . $otherWhere . $yearWhere; // generate full query
        if ($count == 0) {
            $offset = ($pageNumber - 1) * $perPage;
            $query = $query . " limit " . $perPage . " OFFSET " . $offset;
        }
//echo $query;exit;
        $query = $this->db->query($query);
    //    $last_query = $this->db->last_query();
//        echo $last_query;
//        exit;
        if ($count == 0) {
            $query = $query->result_array();
            return $query;
        } else {
            $numRows = $query->num_rows();
            return $numRows;
        }
    }

    /*
      @This function is used for fetching all the data which matched for all kinf of combined matching with each others.
      @ 3 Parameter Required = $parameter, $table_name, $row
      @ $parameter = All posted value exists into this parameter.
      @ $table_name = in which table will for search
      @ $row = How many days user defined.
      @ return = After fatching all combinational data.
     * 
     *     /*
     * This function is actively using in the project.
     * 
     *
     */

    function get_15_matching_diff_last_Close($parameter, $table_name, $row, $count = 0, $perPage = 20, $pageNumber = 1) {
        $difference = $parameter['difference']; // tacking the difference from the $paramaeter array
        $select = "SELECT";
        $from = ' FROM ';
        $where = ' WHERE';
        $open = $parameter['open']; // tacking the open values from the $paramaeter array
        $high = $parameter['high']; // tacking the high values from the $paramaeter array
        $low = $parameter['low']; // tacking the low values from the $paramaeter array
        $close = $parameter['close']; // tacking the close values from the $paramaeter array
        $otherWhere = '';
        $yearWhere = '';
        for ($i = 1; $i <= $row; $i++) { // run a loop for number of days entered
            $nextRow = $i + 1; // taking the next row
            if ($i == 1)  // checking if this is first row for making decision that it will include comma or not as because this is dynamic query generation
                $select.=" s$i.id as id_$i,s$i.open_price as open_price_$i,s$i.highest_price as highest_price_$i,s$i.lowest_price as lowest_price_$i, s$i.closed_price as closed_price_$i, s$i.stockdate as stock_date_$i, s$i.sellmonth, s$i.sellyear";
            else
                $select.=" ,s$i.id as id_$i,s$i.open_price as open_price_$i,s$i.highest_price as highest_price_$i,s$i.lowest_price as lowest_price_$i, s$i.closed_price as closed_price_$i, s$i.stockdate as stock_date_$i, s$i.sellmonth, s$i.sellyear";
            if ($i == 1) // checking if this is first row for making decision that it will include comma or not as because this is dynamic query generation
                $from.=$table_name . ' as s' . $i . ' ';
            else
                $from.=' ,' . $table_name . ' as s' . $i . ' ';
        }
        foreach ($open as $ii => $op) { // run a loop for number of opening for generate all possible combination with opening value with others for where codition
            if ($ii == 1) {
                $where.= " (((s$ii.open_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference) . " AND " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference) . "";
            } else {
                $where.= " AND (((s$ii.open_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference) . " AND " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference) . "";
            }
        }
        foreach ($high as $ii => $hg) { // run a loop for number of high for generate all possible combination with high value with others for where codition
            $where.= " AND (((s$ii.highest_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['high'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference) . " AND " . (((($parameter['high'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference) . "";
        }
        foreach ($low as $ii => $lw) {
            $where.= " AND (((s$ii.lowest_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['low'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference) . " AND " . (((($parameter['low'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference) . "";
        }
        foreach ($close as $ii => $cl) {
            if ($ii != $row) // skip the combination with own
                $where.= " AND (((s$ii.closed_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['close'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference) . " AND " . (((($parameter['close'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference) . "";
        }
        for ($i = 1; $i <= $row; $i++) {
            $nextRow = $i + 1;
            if ($i != $row) { // skip the last row
                $otherWhere .=" AND s$i.id+1=s$nextRow.id ";

                $yearWhere .=" AND s$i.sellyear = s$nextRow.sellyear and s$i.sellmonth = s$nextRow.sellmonth ";
            }
        }


        $select.=",s$row.sellmonth, s$row.sellyear";  // combine all selective column
        $query = $select . $from . $where . $otherWhere . $yearWhere; // generate full query
        if ($count == 0) {
            $offset = ($pageNumber - 1) * $perPage;
            $query = $query . " limit " . $perPage . " OFFSET " . $offset;
        }
//echo $query;exit;
        $query = $this->db->query($query);
       // $last_query = $this->db->last_query();
//        echo $last_query;
//        exit;
        if ($count == 0) {
            $query = $query->result_array();
            return $query;
        } else {
            $numRows = $query->num_rows();
            return $numRows;
        }
    }

    /*
      @This function is used for fetching all the data which matched for all kinf of combined matching with each others.
      @ 3 Parameter Required = $parameter, $table_name, $row
      @ $parameter = All posted value exists into this parameter.
      @ $table_name = in which table will for search
      @ $row = How many days user defined.
      @ return = After fatching all combinational data.
     * 
     *     /*
     * This function is actively using in the project.
     *  developed in 07/25/2018
     *
     */

    function get_15_matching_diff_last_Close2ndPhase($parameter, $table_name, $row, $count = 0, $perPage = 20, $pageNumber = 1) {
        $difference_1 = $parameter['difference_1']; // tacking the difference from the $paramaeter array
        $difference_2 = $parameter['difference_2']; // tacking the difference from the $paramaeter array
        $select = "SELECT";
        $from = ' FROM ';
        $where = ' WHERE';
        $open = $parameter['open']; // tacking the open values from the $paramaeter array
        $high = $parameter['high']; // tacking the high values from the $paramaeter array
        $low = $parameter['low']; // tacking the low values from the $paramaeter array
        $close = $parameter['close']; // tacking the close values from the $paramaeter array
        $otherWhere = '';
        $yearWhere = '';
        for ($i = 1; $i <= $row; $i++) { // run a loop for number of days entered
            $nextRow = $i + 1; // taking the next row
            if ($i == 1)  // checking if this is first row for making decision that it will include comma or not as because this is dynamic query generation
                $select.=" s$i.id as id_$i,s$i.open_price as open_price_$i,s$i.highest_price as highest_price_$i,s$i.lowest_price as lowest_price_$i, s$i.closed_price as closed_price_$i, s$i.stockdate as stock_date_$i, s$i.sellmonth, s$i.sellyear";
            else
                $select.=" ,s$i.id as id_$i,s$i.open_price as open_price_$i,s$i.highest_price as highest_price_$i,s$i.lowest_price as lowest_price_$i, s$i.closed_price as closed_price_$i, s$i.stockdate as stock_date_$i, s$i.sellmonth, s$i.sellyear";
            if ($i == 1) // checking if this is first row for making decision that it will include comma or not as because this is dynamic query generation
                $from.=$table_name . ' as s' . $i . ' ';
            else
                $from.=' ,' . $table_name . ' as s' . $i . ' ';
        }
        foreach ($open as $ii => $op) { // run a loop for number of opening for generate all possible combination with opening value with others for where codition
            if ($ii == 1) {
                $where.= " (((s$ii.open_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference_1) . " AND " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference_1) . "";
            } else {
                if ($ii == $row || $ii == $row - 1) {
                    $where.= " AND (((s$ii.open_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference_2) . " AND " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference_2) . "";
                } else {
                    $where.= " AND (((s$ii.open_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference_1) . " AND " . (((($parameter['open'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference_1) . "";
                }
            }
        }
        foreach ($high as $ii => $hg) { // run a loop for number of high for generate all possible combination with high value with others for where codition
            if ($ii == $row || $ii == $row - 1) {
                $where.= " AND (((s$ii.highest_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['high'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference_2) . " AND " . (((($parameter['high'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference_2) . "";
            } else {
                $where.= " AND (((s$ii.highest_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['high'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference_1) . " AND " . (((($parameter['high'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference_1) . "";
            }
        }
        foreach ($low as $ii => $lw) {
            if ($ii == $row || $ii == $row - 1) {
                $where.= " AND (((s$ii.lowest_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['low'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference_2) . " AND " . (((($parameter['low'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference_2) . "";
            } else {
                $where.= " AND (((s$ii.lowest_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['low'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference_1) . " AND " . (((($parameter['low'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference_1) . "";
            }
        }
        foreach ($close as $ii => $cl) {
            if ($ii != $row) {// skip the combination with own
                if ($ii == $row - 1) {
                    $where.= " AND (((s$ii.closed_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['close'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference_2) . " AND " . (((($parameter['close'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference_2) . "";
                } else {
                    $where.= " AND (((s$ii.closed_price - s$row.closed_price)/s$row.closed_price)*100) BETWEEN " . (((($parameter['close'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) - $difference_1) . " AND " . (((($parameter['close'][$ii] - $parameter['close'][$row]) / $parameter['close'][$row]) * 100) + $difference_1) . "";
                }
            }
        }
        for ($i = 1; $i <= $row; $i++) {
            $nextRow = $i + 1;
            if ($i != $row) { // skip the last row
                $otherWhere .=" AND s$i.id+1=s$nextRow.id ";

                $yearWhere .=" AND s$i.sellyear = s$nextRow.sellyear and s$i.sellmonth = s$nextRow.sellmonth ";
            }
        }


        $select.=",s$row.sellmonth, s$row.sellyear";  // combine all selective column
        $query = $select . $from . $where . $otherWhere . $yearWhere; // generate full query
        if ($count == 0) {
            $offset = ($pageNumber - 1) * $perPage;
            $query = $query . " limit " . $perPage . " OFFSET " . $offset;
        }
//echo $query;exit;
        $query = $this->db->query($query);
       //   $last_query = $this->db->last_query();
       // echo $last_query;
//        exit;
        if ($count == 0) {
            return $query->result_array();
//            return array('select'=>$select,'where'=>$where . $otherWhere . $yearWhere);
        } else {
            $numRows = $query->num_rows();
            return $numRows;
        }
    }

    /*
     * This function is actively using in the project.
     * 
     */

    function get_between_dates($fromdate, $todate, $sellmonth, $commodity_name) {
        $query = "select * from " . $commodity_name .
                " where stockdate between '" . $fromdate . "' and '" . $todate . "' 
                    and sellmonth = '" . $sellmonth . "' order by stockdate asc;";
        $query = $this->db->query($query);
        $last_query = $this->db->last_query();
        $query = $query->result();
        return $query;
    }

    /* insert column function expects two parameter. 1st one is table name and 2nd one is data. $data variable will be an array of data which expects information like following
     * $data['user_id'], $data['user_name] .... etc
     * 
     */

    function insert_row($table_name, $data) {
        $this->db->insert($table_name, $data);
        return $this->db->insert_id();
    }

    /* delete_row function expects two parameter. 1st one is table name and 2nd one is where condition. $where_param variable will be an array of data which expects information like following
     * $data['user_id'], $data['user_name] .... etc
     * 
     */

    function delete_row($table_name, $where_param) {
        $this->db->where($where_param);
        $this->db->delete($table_name);
        return $this->db->affected_rows();
    }

    /* delete_multiple_row function expects two parameter. 1st one is table name and 2nd one is where_in condition. $where_param variable will be an array of data which expects information like following
     * $data['user_id'], $data['user_name] .... etc
     * 
     */

    function delete_multiple_row($table_name, $select_column_name, $where_in_param) {
        $this->db->where_in($select_column_name, $where_in_param);
        $this->db->delete($table_name);
        return $this->db->affected_rows();
    }

    /* update_row function expects three parameter. 1st one is table name and 2nd one is where condition and the 3rd parameter is data to be updated. $where_param variable and $data will be an array of data which expects information like following
     * $where['user_id'], $where['user_name] .... etc
     * $data['user_id'], $data['user_name] .... etc
     */

    function update_row($table_name, $where_param, $data) {
        $this->db->where($where_param);
        $this->db->update($table_name, $data);
        return $this->db->affected_rows();
    }

    /* get_row function expects three parameter. 1st one is table name and 2nd one is where condition and the 3rd parameter is the fields which we select to grab from database. $where_param variable will be an array of data which expects information like following
     * $where['user_id'], $where['user_name] .... etc
     * $select param expects array and string both like following
     * $select_param['user_id'], $select_param['user_name']  or $select_param = "*"
     * and this function will return data in object format
     * 
     * this function is actively using in this project
     */

    function get_row($table_name, $where_param, $select_param, $group = "", $limit = "") {
        if (!empty($select_param))
            $this->db->select($select_param);
        if (!empty($where_param))
            $this->db->where($where_param);
        $this->db->group_by($group);
        if (!empty($limit))
            $this->db->limit($limit);
        $result = $this->db->get($table_name);
        return $result->result();
    }

    function get_row_datatable($table, $where = "") {

        if ($where != "") {
            $this->db->where('(' . $where . ')');
        }
        $result = $this->db->get($table);
        return $result->result();
    }

    function get_row_in_array_datatable($table_name, $where_param) {
        if ($where_param != "") {
            $this->db->where('(' . $where_param . ')');
        }
        $result = $this->db->get($table_name);

        return $result->result_array();
    }

    function get_row_in_array_datatable_order($table_name, $where_param, $order_by = false, $order_value = false) {
        if ($where_param != "") {
            $this->db->where('(' . $where_param . ')');
        }
        if (!empty($order_by))
            $this->db->order_by($order_by, $order_value);
        $result = $this->db->get($table_name);

        return $result->result_array();
    }

    function get_row_array($table_name, $where_param, $select_param, $group = "", $limit = "", $order_by = false, $order_value = false) {
        if (!empty($select_param))
            $this->db->select($select_param);
        if (!empty($where_param))
            $this->db->where($where_param);
        if (!empty($group))
            $this->db->group_by($group);
        if (!empty($order_by))
            $this->db->order_by($order_by, $order_value);
        if (!empty($limit))
            $this->db->limit($limit);
        $result = $this->db->get($table_name);
        return $result->result_array();
    }

    function get_row_wherein_array($table_name, $where_param, $select_param, $group = "", $limit = "", $order_by = false, $order_value = false) {
        if (!empty($select_param))
            $this->db->select($select_param);
        if (!empty($where_param))
            $this->db->where_in($where_param);
        if (!empty($group))
            $this->db->group_by($group);
        if (!empty($order_by))
            $this->db->order_by($order_by, $order_value);
        if (!empty($limit))
            $this->db->limit($limit);
        $result = $this->db->get($table_name);
        return $result->result_array();
    }

    function get_report_array($table_name, $where_param, $select_param, $postData) {
        if (!empty($select_param))
            $this->db->select($select_param);
        if (!empty($where_param))
            $this->db->where($where_param);

        if (!empty($postData['from_date']) && !empty($postData['to_date'])) {
            $this->db->where('created_date >=', $postData['from_date']);
            $this->db->where('created_date <=', $postData['to_date']);
        } else if (!empty($postData['from_date']) && empty($postData['to_date'])) {
            $this->db->where('created_date >=', $postData['from_date']);
            $this->db->where('created_date <=', date('Y-m-d'));
        } else if (empty($postData['from_date']) && !empty($postData['to_date'])) {
            $this->db->where('created_date <=', $postData['to_date']);
        }

        if (!empty($group))
            $this->db->group_by($group);
        if (!empty($limit))
            $this->db->limit($limit);
        $result = $this->db->get($table_name);
        return $result->result_array();
    }

    function get_row_pagination($table_name, $limit, $page) {
        $this->db->limit($limit, $page);
        $result = $this->db->get($table_name);
        return $result->result_array();
    }

    function customeQuery($sql) {
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function customeUpdate($sql) {
        $this->db->query($sql);
        return true;
    }

    function get_row_like($table_name, $where_param, $select_param, $like, $limit = "") {

        $this->db->select($select_param);
        //$this->db->select('CONCAT("<li>",division,",",district,",",thana,",",area,",",post_code,"</li>") as item',false);
        //$this->db->where();
        $this->db->where($like);
        if (!empty($limit))
            $this->db->limit($limit);
        $result = $this->db->get($table_name);
        return $result->result_array();
    }

    /* get_row function expects three parameter. 1st one is table name and 2nd one is where condition and the 3rd parameter is the fields which we select to grab from database. $where_param variable will be an array of data which expects information like following
     * $where['user_id'], $where['user_name] .... etc
     * $select param expects array and string both like following
     * $select_param['user_id'], $select_param['user_name']  or $select_param = "*"
     * and this function will return data in array format
     */

    function get_row_in_array($table_name, $where_param, $select_param) {
        $this->db->select($select_param);
        $this->db->where($where_param);
        $result = $this->db->get($table_name);
        return $result->result_array();
    }

    function get_row_user($where_param) {
        if ($where_param != "") {
            $this->db->where('(' . $where_param . ')');
        }
        $result = $this->db->get("`news_users`");
        return $result;
    }

    public function update_user($data, $where) {
        $this->db->where($where);
        if ($this->db->update('users', $data))
            return true;
        else
            return false;
    }

    public function get_search_result($table, $where, $select) {
        $this->db->select($select);
        $i = 1;
        foreach ($where as $item) {
            // if($i==1)
            $this->db->where($item);
            /*  else 
              $this->db->or_where($item);
              $i++; */
        }
        $result = $this->db->get($table);
        //echo $this->db->last_query();
        return $result->result_array();
        //return $this->db->get($table);
    }

    //Agent Login Process
    function login($username, $password, $user_type, $table_name) {
        $password = md5($password);
        $this->db->where("uname", $username);
        $this->db->where("pass", $password);
        $this->db->where("rank", $user_type);
        $res = $this->db->get($table_name);
        //echo $this->db->last_query();
        $num_rows = $res->num_rows();

        if ($num_rows == 1) {
            return $res->row();
        } else {
            return FALSE;
        }
    }

    //Agent Login without md5 Process
    function login_with_password_string($username, $password, $table_name) {
        $this->db->where("uname", $username);
        $this->db->where("pass", $password);
        //$this->db->where("rank", $user_type);
        $res = $this->db->get($table_name);
        //echo $this->db->last_query();
        $num_rows = $res->num_rows();

        if ($num_rows == 1) {
            return $res->row();
        } else {
            return FALSE;
        }
    }

    function change_password($data) {
        $this->db->where('id', 1);
        $res = $this->db->get("users");
        $res = $res->row();
        if ($res->password != md5($data['old_pass'])) {
            return "Invalid password";
        }

        if ($data['pass_1'] != $data['pass_2']) {
            return "Passwords not match";
        }

        if (strlen($data['pass_1']) < 6 || strlen($data['pass_1']) > 20) {
            return "Passwords length must be between 6 to 20 charecters";
        }

        $this->db->update("admin", array("password" => md5($data['pass_1'])));
        if ($this->db->affected_rows() > 0)
            return TRUE;
        else
            return "Cannot change password";
    }

    function get_login_row($table_name, $where_param, $select_param, $group = "", $limit = "") {
        $this->db->select($select_param);
        $this->db->or_where($where_param);
        $this->db->group_by($group);
        if (!empty($limit))
            $this->db->limit($limit);
        $result = $this->db->get($table_name);
        return $result->result();
    }

}

// END Admin_model Class

/* End of file admin_model.php */
/* Location: ./application/models/admin_model.php */