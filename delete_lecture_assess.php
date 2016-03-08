<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $user_id = $_GET['user_id'];
    $device_id = $_GET['device_id'];

    $unauthorized = 0;
    $sql_authority_check = "SELECT * FROM wicam.user WHERE user_idx = $user_id AND device_id = '$device_id' AND blacklist != 1";
    $result_authority_check = mysql_query($sql_authority_check);
    if (mysql_num_rows($result_authority_check) == 0)
        $unauthorized = 1;

    if ($unauthorized == 0) {

        $average_difficulty = 0;
        $average_instructiveness = 0;
        $my_difficulty = 0;
        $my_instructiveness = 0;
        $my_description = '';

        $sql_assess_delete = "DELETE FROM wicam.lecture_assess WHERE item_idx = $item_id AND writer_idx = $user_id";
        $result_assess_delete = mysql_query($sql_assess_delete, $mydb);

        $sql_get_average = "SELECT AVG(difficulty) difficulty, AVG(instructiveness) instructiveness FROM wicam.lecture_assess WHERE item_idx = $item_id";
        $result_get_average = mysql_query($sql_get_average);
        mysql_data_seek($result_get_average, 0);
        $array_average = mysql_fetch_array($result_get_average);
        $average_difficulty = $array_average['difficulty'];
        $average_instructiveness = $array_average['instructiveness'];

        if ($average_difficulty == null)
            $average_difficulty = 0;
        if ($average_instructiveness == null)
            $average_instructiveness = 0;

        $sql_lecture_update = "UPDATE wicam.lecture SET avg_difficulty = $average_difficulty, avg_instructiveness = $average_instructiveness WHERE item_idx = $item_id";
        mysql_query($sql_lecture_update, $mydb);

        $sql_get_mine = "SELECT * FROM wicam.lecture_assess WHERE item_idx = $item_id AND writer_idx = $user_id";
        $result_get_mine = mysql_query($sql_get_mine, $mydb);
        mysql_data_seek($result_get_average, 0);
        $array_mine = mysql_fetch_array($result_get_mine, 0);
        $my_difficulty = $array_mine['difficulty'];
        $my_instructiveness = $array_mine['instructiveness'];
        $my_description = $array_mine['description'];

        if ($my_difficulty == null)
            $my_difficulty = 0;
        if ($my_instructiveness == null)
            $my_instructiveness = 0;
        if ($my_description == null)
            $my_description = '';

        $json_data['average_difficulty'] = $average_difficulty;
        $json_data['average_instructiveness'] = $average_instructiveness;
        $json_data['my_difficulty'] = $my_difficulty;
        $json_data['my_instructiveness'] = $my_instructiveness;
        $json_data['my_description'] = $my_description;

        $json = json_encode($json_data);
        echo($json);

    }

    mysql_close($mydb);

?>