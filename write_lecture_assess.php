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
        $my_description = 0;

        $sql_assess_check = "SELECT * FROM wicam.lecture_assess WHERE item_idx = $item_id AND writer_idx = $user_id";
        $result_assess_check = mysql_query($sql_assess_check, $mydb);

        if (mysql_num_rows($result_assess_check) == 0) {
            $sql_assess_write = "INSERT INTO wicam.lecture_assess (item_idx, writer_idx, difficulty, instructiveness, description, write_time)
                                  VALUES ($item_id, $user_id, $difficulty, $instructiveness, '$description', '$now')";
            $result_assess_write = mysql_query($sql_assess_write, $mydb);

            $sql_get_average = "SELECT AVG(difficulty) difficulty, AVG(instructiveness) instructiveness FROM wicam.lecture_assess WHERE item_idx = $item_id";
            $result_get_average = mysql_query($sql_get_average);
            mysql_data_seek($result_get_average, 0);
            $array_average = mysql_fetch_array($result_get_average);
            $average_difficulty = $array_average['difficulty'];
            $average_instructiveness = $array_average['instructiveness'];

            $sql_lecture_update = "UPDATE wicam.lecture SET avg_difficulty = $average_difficulty, avg_instructiveness = $average_instructiveness WHERE item_idx = $item_id";
            mysql_query($sql_lecture_update, $mydb);

            $sql_get_mine = "SELECT * FROM wicam.lecture_assess WHERE item_idx = $item_id AND writer_idx = $user_id";
            $result_get_mine = mysql_query($sql_get_mine, $mydb);
            mysql_data_seek($result_get_average, 0);
            $array_mine = mysql_fetch_array($result_get_mine, 0);
            $my_difficulty = $array_mine['difficulty'];
            $my_instructiveness = $array_mine['instructiveness'];
            $my_description = $array_mine['description'];
        }

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