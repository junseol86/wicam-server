<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $sql_list = "SELECT * FROM wicam.school WHERE school_idx != 1
                  ORDER BY
                  CASE school_idx
                    WHEN 248 THEN 1
                    WHEN 230 THEN 2
                    ELSE 3 END,
                    school_name ASC";
    $result_list = mysql_query($sql_list, $mydb);

    for ($i = 0; $i < mysql_num_rows($result_list); $i++) {
        mysql_data_seek($result_list, $i);
        $row = mysql_fetch_array($result_list);

        $json_data['results'][$i] = array("school_id"=>"$row[school_idx]", "school_name"=>"$row[school_name]", "school_contents"=>"$row[school_contents]");
    }


    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>