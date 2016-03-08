<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $unit = 30;
    $page = $_GET['page'];
    $content_id = $_GET['content_id'];
    $keyword = $_GET['keyword'];
    $user_id = $_GET['user_id'];
    $see_favorite = $_GET['see_favorite'];
    $item_id = $_GET['item_id'];

    if ($item_id == '') { // 평상시
        $condition =
            "content_idx = $content_id
        AND (modifier_nickname LIKE '%$keyword%' OR item_name LIKE '%$keyword%' OR description LIKE '%$keyword%')";
    }
    else {
        $condition = "item_idx = $item_id"; // 아이템 추가나 변경 후 해당 아이템만 보여줄 시
    }

    $join = $see_favorite == 0 ? "" : " INNER JOIN wicam.custom_favorite fvrt ON cstm.item_idx = fvrt.item_idx ";
    $join_condition = $see_favorite == 0 ? "" : " AND fvrt.user_idx = $user_id ";

    $sql_count = "SELECT * FROM wicam.custom cstm $join
                  WHERE
                  $condition
                  $join_condition
                  ORDER BY cstm.item_idx DESC ";
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

        // 전단지가 있는지 확인
        $has_photo = 0;
        if (file_exists('../image/custom/item_'.$row['item_idx'].'.jpg'))
            $has_photo = 1;

        $sql_my_favorite = "SELECT * FROM wicam.custom_favorite WHERE item_idx = $row[item_idx] AND user_idx = $user_id";
        $result_my_favorite = mysql_query($sql_my_favorite, $mydb);
        $my_favorite = mysql_num_rows($result_my_favorite);

        $my_like = 0;
        $sql_my_like = "SELECT * FROM wicam.custom_like WHERE item_idx = $row[item_idx] AND user_idx = $user_id";
        $result_my_like = mysql_query($sql_my_like, $mydb);
        $my_like = mysql_num_rows($result_my_like);

        $authority = 0;
        if ($user_id == $row['writer_idx'])
            $authority = 1;
        else {
            $sql_authority = "SELECT modify_delete_authority_idx FROM wicam.modify_delete_authority WHERE
                              default_code = 1 AND item_idx = $row[item_idx] AND user_idx = $user_id AND device_id = '$device_id' AND authorized = 1";
            $result_authority = mysql_query($sql_authority, $mydb);
            if (mysql_num_rows($result_authority) > 0)
                $authority = 1;
        }

        $json_data['results'][$i] = array("custom_id"=>"$row[item_idx]", "custom_name"=>"$row[item_name]",
            "url_link"=>"$row[url_link]", "description"=>"$row[description]",
            "phone1"=>"$row[phone1]", "phone2"=>"$row[phone2]", "has_photo"=>"$has_photo",
            "writer_id"=>"$row[writer_idx]", "modifier_id"=>"$row[modifier_idx]", "modifier_nickname"=>"$row[modifier_nickname]", "modify_time"=>"$row[modify_time]",
            "comments"=>"$row[comments]", "reports"=>"$row[reports]",
            "my_favorite"=>$my_favorite, "likes"=>"$row[likes]", "my_like"=>$my_like, "authority"=>$authority);

    }


    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>