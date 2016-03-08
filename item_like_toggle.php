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
        case 0: $table = 'wicam.custom_like'; $item_table = 'wicam.custom'; break;
        case 1: $table = 'wicam.delivery_like'; $item_table = 'wicam.delivery'; break;
        case 2: $table = 'wicam.restaurant_like'; $item_table = 'wicam.restaurant'; break;
        case 4: $table = 'wicam.phonebook_like'; $item_table = 'wicam.phonebook'; break;
        case 5: $table = 'wicam.advertise_like'; $item_table = 'wicam.advertise'; break;
    }

    $sql_my_like = "SELECT * FROM $table WHERE item_idx = $item_id AND user_idx = $user_id";
    $result_my_like = mysql_query($sql_my_like, $mydb);

    $sql_toggle = mysql_num_rows($result_my_like) == 0 ? "INSERT INTO $table (item_idx, user_idx, like_time) VALUES ($item_id, $user_id, '$now')"
                                                            : "DELETE FROM $table WHERE item_idx = $item_id AND user_idx = $user_id";
    mysql_query($sql_toggle);

    $result_my_like = mysql_query($sql_my_like, $mydb);

    $sql_likes = "SELECT * FROM $table WHERE item_idx = $item_id";
    $result_likes = mysql_query($sql_likes);
    $likes = mysql_num_rows($result_likes);

    $sql_set_like_counts = "UPDATE $item_table SET likes = $likes WHERE item_idx = $item_id";
    mysql_query($sql_set_like_counts, $mydb);

    $json_data['my_like'] = mysql_num_rows($result_my_like);
    $json_data['likes'] = $likes;

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>