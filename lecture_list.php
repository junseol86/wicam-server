<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $unit = 30;
    $page = $_GET['page'];
    $school_id = $_GET['school_id'];
    $keyword = $_GET['keyword'];
    $user_id = $_GET['user_id'];
    $item_id = $_GET['item_id'];

    if ($item_id == '') { // 평상시
        $condition =
            "school_idx = $school_id
        AND (lecture_name LIKE '%$keyword%' OR professor_name LIKE '%$keyword%' OR major LIKE '%$keyword%')";
    }
    else {
        $condition = "item_idx = $item_id"; // 아이템 추가나 변경 후 해당 아이템만 보여줄 시
    }

    $sql_count = "SELECT * FROM wicam.lecture
                  WHERE
                  $condition
                  ORDER BY lecture_name ASC, professor_name ASC ";
    $result_count = mysql_query($sql_count, $mydb);
    $total_count = mysql_num_rows($result_count);
    $to_be_added = $total_count - ($page * $unit);

    $json_data['num_results'] = iconv("CP949", "UTF-8", "$total_count");
    $json_data['page'] = $page;
    $json_data['unit'] = $unit;

    $sql_list = $sql_count."LIMIT ".strval($unit*$page).", {$unit}";
    $result_list = mysql_query($sql_list, $mydb);

    for ($i = 0; $i < (($unit < $to_be_added) ? $unit : $to_be_added); $i++) {
        mysql_data_seek($result_list, $i);
        $row = mysql_fetch_array($result_list);

        $authority = 0;
        if ($user_id == $row['writer_idx'])
            $authority = 1;

        $my_difficulty = 0;
        $my_instructiveness = 0;
        $my_description = '';
        $sql_my_assess = "SELECT * FROM wicam.lecture_assess WHERE item_idx = $row[item_idx] AND writer_idx = $user_id";
        $result_my_assess = mysql_query($sql_my_assess, $mydb);
        if (mysql_num_rows($result_my_assess) > 0) {
            mysql_data_seek($result_my_assess, 0);
            $array_my_assess = mysql_fetch_array($result_my_assess);
            $my_difficulty = $array_my_assess['difficulty'];
            $my_instructiveness = $array_my_assess['instructiveness'];
            $my_description = $array_my_assess['description'];
        }

        $json_data['results'][$i] = array("lecture_id"=>"$row[item_idx]", "lecture_name"=>"$row[lecture_name]", "professor_name"=>"$row[professor_name]", "major"=>"$row[major]",
            "writer_id"=>"$row[writer_idx]", "write_time"=>"$row[write_time]",
            "avg_difficulty"=>"$row[avg_difficulty]", "avg_instructiveness"=>"$row[avg_instructiveness]",
            "my_difficulty"=>"$my_difficulty", "my_instructiveness"=>"$my_instructiveness", "my_description"=>"$my_description",
            "authority"=>$authority);
    }


    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>