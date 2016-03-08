<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $unit = 5;
    $page = $_GET['page'];
    $user_id = $_GET['user_id'];

    $item_id = $_GET['item_id'];


    $table;
    $item_idx;

    $sql_count = "SELECT * FROM wicam.lecture_assess
                  WHERE item_idx = $item_id ORDER BY lecture_assess_idx DESC";
    $result_count = mysql_query($sql_count, $mydb);
    $total_count = mysql_num_rows($result_count);
    $to_be_added = $total_count - ($page * $unit);

    $json_data['num_results'] = iconv("CP949", "UTF-8", "$total_count");
    $json_data['page'] = $page;
    $json_data['unit'] = $unit;

    $sql_list = $sql_count." LIMIT ".strval($unit*$page).", {$unit}";
    $result_list = mysql_query($sql_list, $mydb);

    for ($i = 0; $i < (($unit < $to_be_added) ? $unit : $to_be_added); $i++) {
        mysql_data_seek($result_list, $i);
        $row = mysql_fetch_array($result_list);

        $json_data['results'][$i] = array("lecture_assess_id"=>"$row[lecture_assess_idx]", "item_id"=>"$row[item_idx]",
                                            "difficulty"=>"$row[difficulty]", "instructiveness"=>"$row[instructiveness]", "description"=>"$row[description]",
                                            "writer_id"=>"$row[writer_idx]", "write_time"=>"$row[write_time]");
    }

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>