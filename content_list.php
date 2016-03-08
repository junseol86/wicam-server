<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $sql_list = "SELECT * FROM wicam.content
        WHERE (hidden = 0 AND school_idx = '1' OR school_idx = $school_id OR school_idx like '%|$school_id|%')";
    $result_list = mysql_query($sql_list, $mydb);

    $sql_schools = "SELECT * FROM wicam.school";
    $result_schools = mysql_query($sql_schools);

    for ($i = 0; $i < mysql_num_rows($result_list); $i++) {
        mysql_data_seek($result_list, $i);
        $row = mysql_fetch_array($result_list);

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

        $views = 0;
        if ($row['default_code'] != 0) { // 기본 컨텐츠라면 학교마다 조회수가 다르므로 contdnt_default_view 테이블에서 조회수를 가져온다
            $sql_views = "SELECT SUM(views) FROM wicam.content_default_views WHERE content_idx = $row[content_idx] AND school_idx = $school_id
                AND view_date > DATE_SUB(now(), INTERVAL 7 DAY)";
            $result_views = mysql_query($sql_views, $mydb);

            $row_views = mysql_fetch_row($result_views);
            $views = $row_views[0] == '' ? 0 : $row_views[0];
        }
        else { // 일반 컨텐츠라면 조회수가 row에 저장되어 있다
            $sql_views = "SELECT SUM(views) FROM wicam.content_views WHERE content_idx = $row[content_idx]
            AND view_date > DATE_SUB(now(), INTERVAL 7 DAY)";
            $result_views = mysql_query($sql_views, $mydb);

            $row_views = mysql_fetch_row($result_views);
            $views = $row_views[0] == '' ? 0 : $row_views[0];
        }

        $json_data['results'][$i] = array("content_id"=>"$row[content_idx]", "default_code"=>"$row[default_code]", "content_name"=>"$row[content_name]", "description"=>"$row[description]", "content_type"=>"$row[content_type]",
                                            "contact"=>"$row[contact]", "url_link"=>"$row[url_link]", "package_name"=>"$row[package_name]", "school_id_list"=>$school_id_array, "school_name_list"=>$school_name_array,
                                            "views"=>"$views", "writer_id"=>"$row[writer_idx]", "modifier_nickname"=>"$row[modifier_nickname]", "modify_time"=>"$row[modify_time]");
    }


    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);


?>