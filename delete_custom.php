<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $unauthorized = 0;
    $sql_authority_check = "SELECT * FROM wicam.user WHERE user_idx = $user_id AND device_id = '$device_id' AND blacklist != 1";
    $result_authority_check = mysql_query($sql_authority_check);
    if (mysql_num_rows($result_authority_check) == 0)
        $unauthorized = 1;
    else {
        $sql_writer = "SELECT writer_idx FROM wicam.custom WHERE item_idx = $item_id";
        $result_writer = mysql_query($sql_writer, $mydb);
        $row_writer = mysql_fetch_row($result_writer);
        $writer_id = $row_writer[0];

        $sql_device_id = "SELECT device_id FROM wicam.user WHERE user_idx = $writer_id";
        $result_device_id = mysql_query($sql_device_id, $mydb);
        $row_device_id = mysql_fetch_row($result_device_id);
        $writer_device_id = $row_device_id[0];

        if (!($user_id == $writer_id && $device_id == $writer_device_id)) {
            include 'authority_check.php';
        }
    }

    if ($unauthorized == 0) {

        $sql_delete = "DELETE FROM wicam.custom WHERE item_idx = $item_id";
        $result_delete = mysql_query($sql_delete, $mydb);

        if ($result_delete == true) {

            $file_path = "../image/custom/item_".$item_id.".jpg";
            if (file_exists($file_path))
                unlink($file_path);


            // 아이템의 즐겨찾기 전부 제거
            $sql_favorite_delete = "DELETE FROM wicam.custom_favorite WHERE item_idx = $item_id";
            mysql_query($sql_favorite_delete, $mydb);

            // 아이템의 좋아요 전부 제거
            $sql_like_delete = "DELETE FROM wicam.custom_like WHERE item_idx = $item_id";
            mysql_query($sql_like_delete, $mydb);

            // 아이템의 댓글, 댓글의 사진, 댓글의 좋아요 제거
            $sql_comment_list = "SELECT item_comment_idx FROM wicam.custom_comment WHERE item_idx = $item_id";
            $result_comment_list = mysql_query($sql_comment_list, $mydb);
            if (mysql_num_rows($result_comment_list) != null) {
                for ($i = 0; $i < mysql_num_rows($result_comment_list); $i++) {
                    mysql_data_seek($result_comment_list, $i);
                    $row_comment_id = mysql_fetch_row($result_comment_list);
                    $comment_id = $row_comment_id[0];

                    if (file_exists('../image/custom/'.$comment_id.'.jpg'))
                        unlink('../image/custom/'.$comment_id.'.jpg');

                    $sql_comment_like_delete = "DELETE FROM wicam.custom_comment_like WHERE item_comment_idx = $comment_id";
                    mysql_query($sql_comment_like_delete, $mydb);

                }
            }
            $sql_delete_comment = "DELETE FROM wicam.custom_comment WHERE item_idx = $item_id";
            mysql_query($sql_delete_comment, $mydb);

            $json_data['result'] = 'success';
            $json = json_encode($json_data);
            echo($json);
        }
    }

    mysql_close($mydb);

?>