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

    // 컨텐츠 작성자가 맞는지 검사
    $sql_owner_check = "SELECT writer_idx FROM wicam.content WHERE content_idx = $content_id";
    $result_owner_check = mysql_query($sql_owner_check, $mydb);
    $row_result_check = mysql_fetch_row($result_owner_check);

    if ($user_id != $row_result_check[0])
        $unauthorized = 1;

    if ($unauthorized == 0 && $default_code == 0  && $content_id > 8) {

        if ($content_type == 1) { // 게시판형 컨텐츠일 시 아이템, 이미지, 댓글 등 모든 하위 항목들 삭제

            $sql_get_items = "SELECT item_idx FROM wicam.custom WHERE content_idx = $content_id";
            $result_get_items = mysql_query($sql_get_items, $mydb);

            if (mysql_num_rows($result_get_items) != null) {
                for ($i = 0; $i < mysql_num_rows($result_get_items); $i++) {
                    mysql_data_seek($result_get_items, $i);
                    $row_item = mysql_fetch_row($result_get_items);
                    $item_id = $row_item[0];

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
                }

                $sql_delete = "DELETE FROM wicam.custom WHERE item_idx = $item_id";
                $result_delete = mysql_query($sql_delete, $mydb);
            }

        }

        $sql_content_delete = "DELETE FROM wicam.content WHERE content_idx = $content_id";
        $result_content_delete = mysql_query($sql_content_delete, $mydb);

        $json_data['result'] = 'done';

        $json = json_encode($json_data);
        echo($json);
    }

    mysql_close($mydb);

?>