<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $restaurant_id = $_GET['restaurant_id'];


    $sql_photo = "SELECT item_comment_idx FROM wicam.restaurant_comment WHERE item_idx = $restaurant_id ORDER BY RAND()";
    $result_photo = mysql_query($sql_photo, $mydb);

    $photo_array = array();
    for ($j = 0; $j < mysql_num_rows($result_photo); $j++) {
        mysql_data_seek($result_photo, $j);
        $row_photo = mysql_fetch_row($result_photo);
        $photo_id = $row_photo[0];

        if (file_exists('../image/restaurant/'.$photo_id.'.jpg')) {
            array_push($photo_array, $photo_id);
        }
    }

    $main_photo = '';
    if (sizeof($photo_array) != 0)
        $main_photo = $photo_array[0];

    $json_data['main_photo'] = $main_photo;
    $json_data['photo_list'] = $photo_array;

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>