<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $unit = 30;
    $order1;
    $order2;

    $order0 =
    $order1 = $favorite == 0 ? '' : ' case WHEN (default_code = 0 AND count = 1) OR (default_code != 0 AND count = 0) THEN 1 ELSE 2 END, ';
    $order2 = $recent_or_famous == 0 ? ' modify_time DESC ' : ' default_views DESC, views DESC ';

    $sql_count = "SELECT *, (SELECT COUNT(*) FROM wicam.content_favorite WHERE content_idx = wicam.content.content_idx AND user_idx = $user_id) count,
        (SELECT SUM(views) FROM wicam.content_default_views WHERE content_idx = wicam.content.content_idx AND school_idx = $school_id AND view_date > DATE_SUB('$now', INTERVAL 7 DAY)) default_views,
        (SELECT SUM(views) FROM wicam.content_views WHERE content_idx = wicam.content.content_idx AND view_date > DATE_SUB('$now', INTERVAL 7 DAY)) views
        FROM wicam.content
        WHERE hidden = 0 AND (content_name like '%$keyword%' || description like '%$keyword%') AND (school_idx = '1' OR school_idx = $school_id OR school_idx like '%|$school_id|%')";
    $result_count = mysql_query($sql_count, $mydb);

    $result_count = mysql_query($sql_count, $mydb);
    $total_count = mysql_num_rows($result_count);
    $to_be_added = $total_count - ($page * $unit);

    $json_data['num_results'] = iconv("CP949", "UTF-8", "$total_count");
    $json_data['page'] = $page;
    $json_data['unit'] = $unit;

    $sql_list = $sql_count." ORDER BY".$order1.$order2."LIMIT ".strval($unit*$page).", {$unit}";
    $result_list = mysql_query($sql_list, $mydb);

    $sql_schools = "SELECT * FROM wicam.school";
    $result_schools = mysql_query($sql_schools);

    for ($i = 0; $i < mysql_num_rows($result_list); $i++) {
        mysql_data_seek($result_list, $i);
        $row = mysql_fetch_array($result_list);

        $favorite = ($row['default_code'] == 0 && $row['count'] == 1) || ($row['default_code'] != 0 && $row['count'] == 0) ? 1 : 0;

        $school_id_array = array();
        $school_name_array = array();
        for ($k = 0; $k < mysql_num_rows($result_schools); $k++) {
            mysql_data_seek($result_schools, $k);
            $array_school = mysql_fetch_array($result_schools);
            if ($row['school_idx'] == $array_school['school_idx'] || !(strpos($row['school_idx'], '|'.$array_school['school_idx'].'|') === false) ) {
                array_push($school_name_array, $array_school['school_name']);
                array_push($school_id_array, $array_school['school_idx']);
            }
        }

        $json_data['results'][$i] = array("content_id"=>"$row[content_idx]", "default_code"=>"$row[default_code]", "content_name"=>"$row[content_name]", "description"=>"$row[description]", "content_type"=>"$row[content_type]",
                                            "contact"=>"$row[contact]", "url_link"=>"$row[url_link]", "package_name"=>"$row[package_name]", "school_id_list"=>$school_id_array, "school_name_list"=>$school_name_array,
                                            "writer_id"=>"$row[writer_idx]", "modifier_nickname"=>"$row[modifier_nickname]", "modify_time"=>"$row[modify_time]", "favorite" => "$favorite");
    }


    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);


?>