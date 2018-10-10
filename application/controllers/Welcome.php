<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    function __construct() {
        parent::__construct();
        $this->load->model("M_common", "m_common");
        $this->load->helper('array');
        $this->load->library('session');
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            $data = array();
            $this->load->view('v_full_analysis', $data);
        } else {
            $this->login();
        }
    }

    public function logout() {
        $this->session->unset_userdata('user_id');
        $this->session->sess_destroy();
        $this->login();
    }

    public function login() {
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            $this->index();
        } else {
            $data = array();
            $this->load->view('v_login', $data);
        }
    }

    public function login_action() {
        $where['username'] = $this->input->post("user_name");
        $where['password'] = md5($this->input->post("password"));
        $user_id = $this->m_common->get_row("user", $where, "user_id");
        if (count($user_id) > 0)
            $session_data = array("user_id" => $user_id[0]->user_id);
        $this->session->set_userdata($session_data);
        redirect_with_msg("welcome", "Login Successful");
    }

    public function check_proc() {
        $this->setOutputMode(NORMAL);
        $output = $this->m_common->procedure_call();
        echo $output;
    }

    public function deviation($givenvalue, $targetvalue) {
        if ($givenvalue > $targetvalue) {
            $givenvalue = $givenvalue - $targetvalue;
        } else {
            $givenvalue = $targetvalue - $givenvalue;
        }
        if ($targetvalue == 0)
            $targetvalue = 0.5;
        return 100 * ($givenvalue / $targetvalue);
    }

    public function deviation_new($givenvalue, $targetvalue) {
        $this->setOutputMode(NORMAL);
        if ($givenvalue > $targetvalue) {
            $givenvalue = $givenvalue - $targetvalue;
        } else {
            $givenvalue = $targetvalue - $givenvalue;
        }
        if ($targetvalue == 0)
            $targetvalue = 0.5;
        return 100 * ($givenvalue / $targetvalue);
    }

    public function individual_graph() {

        $data['fromdate'] = $this->input->post('startdate');
        $data['todate'] = $this->input->post('finaldate');
        $data['sellmonth'] = $this->input->post('sellmonth');
        ;
        $data['sellyear'] = $this->input->post('sellyear');
        ;
        $data['commodity_name'] = $this->input->post('commodity_name');
        $date = $this->input->post('date');
        $data['date'] = explode('/', $date);

        $this->load->view("v_detailed_analysis", $data);
    }

    public function individual_graph2() {

        $data['fromdate'] = $this->input->post('startdate');
        $data['todate'] = $this->input->post('finaldate');
        $data['sellmonth'] = $this->input->post('sellmonth');
        $data['sellyear'] = $this->input->post('sellyear');
        $data['commodity_name'] = $this->input->post('commodity_name');
        $date = $this->input->post('date');
        $data['date'] = explode('/', $date);
        $data['inputParameter'] = unserialize(base64_decode($this->input->post('inputParameter')));
        $inputStr = "";
        $i = 0;
        foreach ($data['inputParameter']['open'] as $key => $value) {
            if ($i != 0) {
                $inputStr = $inputStr . " ,";
            }
            $inputStr = $inputStr . "[" . $value . ",";
            $inputStr = $inputStr . $data['inputParameter']['high'][$key] . ",";
            $inputStr = $inputStr . $data['inputParameter']['low'][$key] . ",";
            $inputStr = $inputStr . $data['inputParameter']['close'][$key] . "]";

            $i = $i + 1;
        }
        $inputStr = "[" . $inputStr . "]";
        $data['inputStr'] = $inputStr;
        $this->load->view("v_high_chart_analysis", $data);
    }

    public function readjson($fromdate, $todate, $sellmonth, $sellyear, $commodity_name) {
        $data['stockvalue'] = $this->m_common->get_between_dates($fromdate, $todate, $sellmonth, $commodity_name);
        $data['stockvalue'] = json_encode($data['stockvalue']);
        header('Content-Type: text/plain');
        $this->output->set_content_type('text/plain');
        echo $data['stockvalue'];
    }

    public function readjson2($fromdate, $todate, $sellmonth, $sellyear, $commodity_name) {
        $table_name = $commodity_name;
        $where_param['sellmonth'] = $sellmonth;
        $where_param['sellyear'] = $sellyear;
        $select_param = array('stockdate', 'open_price', 'highest_price', 'lowest_price', 'closed_price');
        $stockvalue = $this->m_common->get_row($table_name, $where_param, $select_param);
        $finalvalue = array();
        foreach ($stockvalue as $value) {
            $finalvalue[] = array(strtotime($value->stockdate) * 1000, floatval($value->open_price), floatval($value->highest_price), floatval($value->lowest_price), floatval($value->closed_price));
        }
        $total_row = count($finalvalue);
        echo json_encode($finalvalue);
    }

    /*
     * @ This is the function for full combined Analysis Module intialization.
     * @ Author 4axiz IT Ltd.
     * @ This is the view page loading function for analysis page.
     * @ Parameter = Not required
     * @ POST = Not Required
     * @ GET = Not Required
     */

    public function full_analysis() {
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            $data = array();
            $this->load->view('v_full_analysis', $data);
        } else {
            $this->login();
        }
    }

    public function fifteenth_analysisClosePrice() {
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            $data = array();
            $this->load->view('v_fifteenth_analysis', $data);
        } else {
            $this->login();
        }
    }

    public function fifteenth_analysisClosePrice2ndPhase() {
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            $data = array();
            $this->load->view('v_fifteenth_analysis2ndPhase', $data);
        } else {
            $this->login();
        }
    }

    /*
     * This version is working now. Talked with client and they wanted to check the values which comes thorugh this idea of the percentage matches.
     * 
     * @ This is the function for full combined Analysis Module calculation.
     * @ Author 4axiz IT Ltd.
     * @ This is the data featching and calculating function for data analysis.
     * @ Parameter = Not required
     * @ POST = Yes
     * @ GET = Not Required
     * @ Result = By user given data this function fetched the all possible combinated data and then calculate the deviation for them and show view files.
     */

    public function detailed_full_analysis() {

        $postData = $this->input->post();
        if (!empty($postData)) {
            $this->session->set_userdata($postData);
        } else {
            $postData['open'] = $this->session->userdata('open');
            $postData['high'] = $this->session->userdata('high');
            $postData['low'] = $this->session->userdata('low');
            $postData['close'] = $this->session->userdata('close');
            $postData['difference'] = $this->session->userdata('difference');
            $postData['category_name'] = $this->session->userdata('category_name');
        }

        $difference = $postData['difference'];
        $parameter = array();
        $row = 0;
        $params = '';
        foreach ($postData['open'] as $key => $open) {

            if (!empty($params))
                $params.='|' . $open . ',' . $postData['high'][$key] . ',' . $postData['low'][$key] . ',' . $postData['close'][$key];
            else
                $params.=$open . ',' . $postData['high'][$key] . ',' . $postData['low'][$key] . ',' . $postData['close'][$key];

            $row = $key + 1;
            $parameter['open'][$row] = $open;
            $parameter['high'][$row] = $postData['high'][$key];
            $parameter['low'][$row] = $postData['low'][$key];
            $parameter['close'][$row] = $postData['close'][$key];
        }
        $parameter['difference'] = $difference;
        $table_name = $postData['category_name'];
        $data['inputParameter'] = $parameter;
        if ($this->uri->segment(3)) {
            $page = ($this->uri->segment(3));
        } else {
            $page = 1;
        }

        $query = $this->m_common->get_total_matching_diff_full_query($parameter, $table_name, $row); // fethch the result depends on posted value

        $sql = "CALL get_total_matching_diff_full('" . $query . "', $row, '" . $params . "',$page,150)";

        $result = $this->db->query($sql);
        $all_info = $result->result_array();

        /*
         * Implementing paging option
         */
        $this->load->library('pagination');
        $config = array();
        $config["base_url"] = base_url() . "/welcome/detailed_full_analysis";
        $total_row = !empty($all_info) ? $all_info[0]['rowcount'] : '0';
        $config["total_rows"] = $total_row;
        $config["per_page"] = 150;
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = $total_row;
        $config['cur_tag_open'] = '&nbsp;<a class="current">';
        $config['cur_tag_close'] = '</a>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Previous';

        $this->pagination->initialize($config);

        $str_links = $this->pagination->create_links();
        $data["links"] = explode('&nbsp;', $str_links);
        $totalInputRow = count($parameter['open']);


        $data['date_array'] = $all_info;
        $data['row'] = $totalInputRow;
        $data['commodity_name'] = $table_name;
        $data['analysis_kind'] = "Full analysis";

        $this->load->view("v_list_analysis", $data); // load all data into a view file
    }

    /*
     * 
     * 
     * @ This is the function for fifteenth matches Module calculation. Comparing the matched between the last input days closed price with others
     * @ Author dhrubo Saha
     * @ This is the data featching and calculating function for data analysis.
     * @ Parameter = Not required
     * @ POST = Yes
     * @ GET = Not Required
     * @ Result = By user given data this function fetched the all possible combinated data and then calculate the deviation for them and show view files.
     */

    public function detailed_fifteenth_analysis_closePrice() {

        $postData = $this->input->post();
        if (!empty($postData)) {
            $this->session->set_userdata($postData);
        } else {
            $postData['open'] = $this->session->userdata('open');
            $postData['high'] = $this->session->userdata('high');
            $postData['low'] = $this->session->userdata('low');
            $postData['close'] = $this->session->userdata('close');
            $postData['difference'] = $this->session->userdata('difference');
            $postData['category_name'] = $this->session->userdata('category_name');
        }


        $difference = $postData['difference'];
        $parameter = array();
        $row = 0;
        $params = '';
        foreach ($postData['open'] as $key => $open) {

            if (!empty($params))
                $params.='|' . $open . ',' . $postData['high'][$key] . ',' . $postData['low'][$key] . ',' . $postData['close'][$key];
            else
                $params.=$open . ',' . $postData['high'][$key] . ',' . $postData['low'][$key] . ',' . $postData['close'][$key];

            $row = $key + 1;
            $parameter['open'][$row] = $open;
            $parameter['high'][$row] = $postData['high'][$key];
            $parameter['low'][$row] = $postData['low'][$key];
            $parameter['close'][$row] = $postData['close'][$key];
        }
        $parameter['difference'] = $difference;
        $table_name = $postData['category_name'];

        $data['inputParameter'] = $parameter;

        /*
         * Implementing paging option
         */

        if ($this->uri->segment(3)) {
            $page = ($this->uri->segment(3));
        } else {
            $page = 1;
        }

        $sql = "CALL sp_detailed_fifteenth_analysis_closePrice('" . $postData['category_name'] . "', $row, '" . $params . "'," . $difference . ",$page,150)";
        $result = $this->GetMultipleQueryResult($sql);
        $all_info = !empty($result[0]) ? $result[0] : array();
        $other_info = !empty($result[1][0]) ? $result[1][0] : array();

        $this->load->library('pagination');
        $config = array();
        $config["base_url"] = base_url() . "/welcome/detailed_fifteenth_analysis_closePrice";
        $total_row = !empty($other_info['total_rows']) ? $other_info['total_rows'] : '0'; //$this->m_common->get_15_matching_diff_last_Close($parameter, $table_name, $row, 1);
        $config["total_rows"] = $total_row;
        $config["per_page"] = 150;
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = $total_row;
        $config['cur_tag_open'] = '&nbsp;<a class="current">';
        $config['cur_tag_close'] = '</a>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Previous';

        $this->pagination->initialize($config);

        $str_links = $this->pagination->create_links();
        $data["links"] = explode('&nbsp;', $str_links);
        $totalInputRow = count($parameter['open']);
        $data['date_array'] = $all_info;
        $data['other_info'] = $other_info;
        $data['row'] = $totalInputRow;
        $data['commodity_name'] = $table_name;
        $data['analysis_kind'] = "15 matches analysis";
        $this->load->view("v_list_analysis", $data); // load all data into a view file
    }

    /*
     * .
     * 
     * @ This is the function for fifteenth matches Module calculation. Comparing the matched between the last input days closed price with others
     * @ Author dhrubo Saha
     * @ This is the data featching and calculating function for data analysis.
     * @ Parameter = Not required
     * @ POST = Yes
     * @ GET = Not Required
     * @ Result = By user given data this function fetched the all possible combinated data and then calculate the deviation for them and show view files.
     */

    public function detailed_fifteenth_analysis_closePrice2ndPhase() {

        $postData = $this->input->post();
        if (!empty($postData)) {
            $this->session->set_userdata($postData);
        } else {
            $postData['open'] = $this->session->userdata('open');
            $postData['high'] = $this->session->userdata('high');
            $postData['low'] = $this->session->userdata('low');
            $postData['close'] = $this->session->userdata('close');
            $postData['difference_1'] = $this->session->userdata('difference_1');
            $postData['difference_2'] = $this->session->userdata('difference_2');
            $postData['category_name'] = $this->session->userdata('category_name');
        }

        $parameter = array();
        $row = 0;
        $params = '';
        foreach ($postData['open'] as $key => $open) {
            if (!empty($params))
                $params.='|' . $open . ',' . $postData['high'][$key] . ',' . $postData['low'][$key] . ',' . $postData['close'][$key];
            else
                $params.=$open . ',' . $postData['high'][$key] . ',' . $postData['low'][$key] . ',' . $postData['close'][$key];
            $row = $key + 1;
            $parameter['open'][$row] = $open;
            $parameter['high'][$row] = $postData['high'][$key];
            $parameter['low'][$row] = $postData['low'][$key];
            $parameter['close'][$row] = $postData['close'][$key];
        }

        $parameter['difference_1'] = $postData['difference_1'];
        $parameter['difference_2'] = $postData['difference_2'];
        $table_name = $postData['category_name'];

        $data['inputParameter'] = $parameter;
        $data['analysis_kind'] = "15 matches analysis 2nd Phase";
        /*
         * Implementing paging option
         */
        $this->load->library('pagination');
        $config = array();
        $config["base_url"] = base_url() . "/welcome/detailed_fifteenth_analysis_closePrice2ndPhase";
        $total_row = $this->m_common->get_15_matching_diff_last_Close2ndPhase($parameter, $table_name, $row, 1);
        $config["total_rows"] = $total_row;
        $config["per_page"] = 150;
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = $total_row;
        $config['cur_tag_open'] = '&nbsp;<a class="current">';
        $config['cur_tag_close'] = '</a>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Previous';

        $this->pagination->initialize($config);
        if ($this->uri->segment(3)) {
            $page = ($this->uri->segment(3));
        } else {
            $page = 1;
        }

        $sql = "CALL sp_getresult('" . $postData['category_name'] . "', $row, '" . $params . "'," . $postData['difference_1'] . "," . $postData['difference_2'] . ",$page,150)";
        $result = $this->db->query($sql);
        $all_info = $result->result_array();
        $str_links = $this->pagination->create_links();
        $data["links"] = explode('&nbsp;', $str_links);
        $totalInputRow = count($parameter['open']);

        $data['date_array'] = $all_info;
        $data['row'] = $totalInputRow;
        $data['commodity_name'] = $table_name;
        $this->load->view("v_list_analysis", $data); // load all data into a view file
    }

    public function GetMultipleQueryResult($queryString) {
        if (empty($queryString)) {
            return false;
        }

        $index = 0;
        $ResultSet = array();

        /* execute multi query */
        if (mysqli_multi_query($this->db->conn_id, $queryString)) {
            do {
                if (false != $result = mysqli_store_result($this->db->conn_id)) {
                    $ResultSet[$index] = $result->fetch_all(MYSQLI_ASSOC);
                }
                $index++;
            } while (mysqli_next_result($this->db->conn_id));
        }

        return $ResultSet;
    }

}
