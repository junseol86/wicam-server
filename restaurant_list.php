<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $unit = 10;
    $page = $_GET['page'];
    $school_id = $_GET['school_id'];
    $genre = $_GET['genre'];
    $keyword = $_GET['keyword'];
    $big_number = $_GET['big_number'];
    $small_number = $_GET['small_number'];
    $user_id = $_GET['user_id'];
    $see_favorite = $_GET['see_favorite'];
    $item_id = $_GET['item_id'];

    if ($item_id == '') { // 평상시
        $condition =
            "(school_idx = $school_id OR school_idx like '%|$school_id|%')
             AND genre LIKE '%$genre%'
        AND (item_name LIKE '%$keyword%' OR address LIKE '%$keyword%' OR description LIKE '%$keyword%') ";
    }
    else {
        $condition = "item_idx = $item_id"; // 아이템 추가나 변경 후 해당 아이템만 보여줄 시
    }

    $join = $see_favorite == 0 ? "" : " INNER JOIN wicam.restaurant_favorite fvrt ON rstrnt.item_idx = fvrt.item_idx ";
    $join_condition = $see_favorite == 0 ? "" : " AND fvrt.user_idx = $user_id ";

    $sql_count = "SELECT * FROM wicam.restaurant rstrnt $join
                  WHERE
                  $condition
                  $join_condition
                  ORDER BY
                  ($big_number % (rstrnt.item_idx + $small_number))";
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

//        $sql_comments = "SELECT item_comment_idx FROM wicam.restaurant_comment WHERE item_idx = $row[item_idx]";
//        $result_comments = mysql_query($sql_comments, $mydb);
//        $comments = mysql_num_rows($result_comments);
//
//        $sql_reports = $sql_comments." AND report = 1";
//        $result_reports = mysql_query($sql_reports, $mydb);
//        $reports = mysql_num_rows($result_reports);

        $school_id_array = array();
        $school_name_array = array();
        $sql_schools = "SELECT * FROM wicam.school";
        $result_schools = mysql_query($sql_schools);
        for ($k = 0; $k < mysql_num_rows($result_schools); $k++) {
            mysql_data_seek($result_schools, $k);
            $array_school = mysql_fetch_array($result_schools);
            if ($row['school_idx'] == $array_school['school_idx'] || !(strpos($row['school_idx'], '|'.$array_school['school_idx'].'|') === false) ) {
                array_push($school_name_array, $array_school['school_name']);
                array_push($school_id_array, $array_school['school_idx']);
            }
        }

        $photo = '';

        $sql_photo = "SELECT item_comment_idx FROM wicam.restaurant_comment WHERE item_idx = $row[item_idx] ORDER BY RAND()";
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


        $num_photos = 0;

        $sql_my_favorite = "SELECT * FROM wicam.restaurant_favorite WHERE item_idx = $row[item_idx] AND user_idx = $user_id";
        $result_my_favorite = mysql_query($sql_my_favorite, $mydb);
        $my_favorite = mysql_num_rows($result_my_favorite);

        $my_like = 0;
        $sql_my_like = "SELECT * FROM wicam.restaurant_like WHERE item_idx = $row[item_idx] AND user_idx = $user_id";
        $result_my_like = mysql_query($sql_my_like, $mydb);
        $my_like = mysql_num_rows($result_my_like);

        $authority = 0;
        if ($user_id == $row['writer_idx'])
            $authority = 1;
        else {
            $sql_authority = "SELECT modify_delete_authority_idx FROM wicam.modify_delete_authority WHERE
                              default_code = 2 AND item_idx = $row[item_idx] AND user_idx = $user_id AND device_id = '$device_id' AND authorized = 1";
            $result_authority = mysql_query($sql_authority, $mydb);
            if (mysql_num_rows($result_authority) > 0)
                $authority = 1;
        }

        $json_data['results'][$i] = array("restaurant_id"=>"$row[item_idx]", "restaurant_name"=>"$row[item_name]", "school_id_list"=>$school_id_array, "school_name_list"=>$school_name_array,
            "genre"=>"$row[genre]", "description"=>"$row[description]", "address"=>"$row[address]",
            "latitude"=>"$row[latitude]", "longitude"=>"$row[longitude]", "phone1"=>"$row[phone1]", "phone2"=>"$row[phone2]",
            "writer_id"=>"$row[writer_idx]", "modifier_id"=>"$row[modifier_idx]", "modifier_nickname"=>"$row[modifier_nickname]", "modify_time"=>"$row[modify_time]",
            "comments"=>"$row[comments]", "reports"=>"$row[reports]",
            "my_favorite"=>$my_favorite, "likes"=>"$row[likes]", "my_like"=>$my_like, "authority"=>$authority, "photo_list"=>$photo_array, "main_photo"=>$main_photo);

    }


    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>