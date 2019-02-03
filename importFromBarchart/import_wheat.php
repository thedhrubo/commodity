<?php
date_default_timezone_set("America/New_York");
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, "https://marketdata.websol.barchart.com/getQuote.json?apikey=34f951498bc9911c18fe05dda40dc4c3&symbols=ZWH19&fields=month%2Cyear");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
$response = curl_exec($ch);
curl_close($ch);
$res = json_decode($response);
$data = !empty($res->results[0]) ? $res->results[0] : array();
if (!empty($data)) {
    require_once("/home/bowmantrading1/external_includes/mysql_pw.php");
    $mysqli = new mysqli($DBserver, $DBuser, $DBpassword, $DBname);
    // $link = mysqli_connect('localhost', 'root', '', 'data_analysis');
    $month = array("January"=>"F", "February" =>"G", "March" =>"H", "April" =>"J", "May" => "K", "June" => "M",
    "July"=>"N", "August" => "Q", "September" => "U", "October"=>"V", "November" =>"X", "December" => "Z"); 
    // $month = "";
    // if ($data->month == "January")
    //             $month = "F";
    //         else if ($data->month == "February")
    //             $month = "G";
    //         else if ($data->month == "March")
    //             $month = "H";
    //         else if ($data->month == "April")
    //             $month = "J";
    //         else if ($data->month == "May")
    //             $month = "K";
    //         else if ($data->month == "June")
    //             $month = "M";
    //         else if ($data->month == "July")
    //             $month = "N";
    //         else if ($data->month == "August")
    //             $month = "Q";
    //         else if ($data->monthh == "September")
    //             $month = "U";
    //         else if ($data->month == "October")
    //             $month = "V";
    //         else if ($data->month == "November")
    //             $month = "X";
    //         else
    //             $month = "Z";


    $sql = 'INSERT into wheat_back(open_price,highest_price,lowest_price,closed_price,stockdate,sellyear,sellmonth) '
            . 'values("' . $data->open . '","' . $data->high . '","' . $data->low . '","' . $data->close . '","' . date('Y-m-d', strtotime($data->tradeTimestamp)) . '","' . $data->year . '","' . $month[$data->month] .'")';
    if($mysqli->query($sql) === TRUE){
        
          $to = 'shaheen@4axiz.com,dhrubo@4axiz.com';
$subject = 'Data Import into commodity project';
$message = 'Dear Author,<br> Data imported for commodity project at : '.date('d-m-Y h:i:s A');
$headers = 'From: admin@4axiz.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    $headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
     //   mail($to, $subject, $message, $headers);
    echo $sql;
    echo $data->month;
    }else{
        echo $mysqli->error;
    }
    
}
?>