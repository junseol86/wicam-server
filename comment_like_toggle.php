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
    $comment_id = $_GET['comment_id'];


    $table;
    $comment_idx;

    switch ($default_code) {
        case 0: $table = 'wicam.custom_comment_like'; $item_comment_table = 'wicam.custom_comment'; break;
        case 1: $table = 'wicam.delivery_comment_like'; $item_comment_table = 'wicam.delivery_comment'; break;
        case 2: $table = 'wicam.restaurant_comment_like'; $item_comment_table = 'wicam.restaurant_comment'; break;
        case 4: $table = 'wicam.phonebook_comment_like'; $item_comment_table = 'wicam.phonebook_comment'; break;
        case 5: $table = 'wicam.advertise_comment_like'; $item_comment_table = 'wicam.advertise_comment'; break;
    }

    $sql_my_like = "SELECT * FROM $table WHERE item_comment_idx = $comment_id AND user_idx = $user_id";
    $result_my_like = mysql_query($sql_my_like, $mydb);

    $sql_toggle = mysql_num_rows($result_my_like) == 0 ? "INSERT INTO $table (item_comment_idx, user_idx) VALUES ($comment_id, $user_id)"
                                                            : "DELETE FROM $table WHERE item_comment_idx = $comment_id AND user_idx = $user_id";
    mysql_query($sql_toggle);

    $result_my_like = mysql_query($sql_my_like, $mydb);

    $sql_likes = "SELECT * FROM $table WHERE item_comment_idx = $comment_id";
    $result_likes = mysql_query($sql_likes);

    $likes = mysql_num_rows($result_likes);
    $set_likes_count = "UPDATE $item_comment_table SET likes = $likes WHERE item_comment_idx = $comment_id";
    mysql_query($set_likes_count, $mydb);

    $json_data['my_like'] = mysql_num_rows($result_my_like);
    $json_data['likes'] = $likes;

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>