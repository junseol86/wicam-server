<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $user_id = $_GET['user_id'];

    $default_code = $_GET['default_code'];
    $content_id = $_GET['content_id'];
    $content_type = $_GET['content_type'];
    $item_id = $_GET['item_id'];


    $table;
    $item_idx;

    switch ($default_code) {
        case 0: $table = 'wicam.custom_favorite'; break;
        case 1: $table = 'wicam.delivery_favorite'; break;
        case 2: $table = 'wicam.restaurant_favorite'; break;
        case 4: $table = 'wicam.phonebook_favorite'; break;
        case 5: $table = 'wicam.advertise_favorite'; break;
    }

    $sql_favorite = "SELECT * FROM $table WHERE item_idx = $item_id AND user_idx = $user_id";
    $result_favorite = mysql_query($sql_favorite, $mydb);

    $sql_toggle = mysql_num_rows($result_favorite) == 0 ? "INSERT INTO $table (item_idx, user_idx) VALUES ($item_id, $user_id)"
                                                            : "DELETE FROM $table WHERE item_idx = $item_id AND user_idx = $user_id";
    mysql_query($sql_toggle);

    $result_favorite = mysql_query($sql_favorite, $mydb);

    $json_data['result'] = mysql_num_rows($result_favorite);

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>