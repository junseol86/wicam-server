<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $unit = 10;
    $page = $_GET['page'];
    $school_id = $_GET['school_id'];
    $filter = $_GET['filter'];

    $advertise = "SELECT item_idx as idx, '5' as content_idx, item_idx as photo_idx, item_name as title, description as text, writer_nickname as person, url_link as value1, '' as value2, write_time as time, 'advertise' as content, school_idx as school FROM
wicam.advertise";
    $custom = "SELECT item.item_idx as idx, custom.content_idx as content_idx, item.item_idx as photo_idx, custom.content_name as title, item.item_name as text, item.writer_nickname as person, item.description as value1, '' as value2, item.write_time as time, 'custom' as content, custom.school_idx as school FROM
wicam.content custom INNER JOIN wicam.custom item on custom.content_idx = item.content_idx";
    $delivery = "SELECT dlvry.item_idx as idx, '1' as content_idx, cmt.item_comment_idx as photo_idx, dlvry.item_name as title, comment as text, cmt.writer_nickname as person, cmt.url_link as value1, '' as value2, cmt.write_time as time, 'delivery' as content, dlvry.school_idx as school FROM
wicam.delivery_comment cmt INNER JOIN wicam.delivery dlvry ON cmt.item_idx = dlvry.item_idx";
    $restaurant = "SELECT rstrnt.item_idx as idx, '2' as content_idx, cmt.item_comment_idx as photo_idx, rstrnt.item_name as title, comment as text, cmt.writer_nickname as person, cmt.url_link as value1, '' as value2, cmt.write_time as time, 'restaurant' as content, rstrnt.school_idx as school FROM
wicam.restaurant_comment cmt INNER JOIN wicam.restaurant rstrnt ON cmt.item_idx = rstrnt.item_idx";
    $lecture = "SELECT lctr.item_idx as idx, '3' as content_idx, '' as photo_idx, lctr.lecture_name as title, description as text, lctr.professor_name as person, ass.difficulty as value1, ass.instructiveness as value2, ass.write_time as time, 'lecture' as content, lctr.school_idx as school FROM
wicam.lecture_assess ass INNER JOIN wicam.lecture lctr ON ass.item_idx = lctr.item_idx";

    $sql_filtered = "";

    switch ($filter) {
        case "all": $sql_filtered = $advertise." UNION ".$custom." UNION ".$delivery." UNION ".$restaurant." UNION ".$lecture;
            break;
        case "advertise": $sql_filtered = $advertise; break;
        case "custom": $sql_filtered = $custom; break;
        case "delivery": $sql_filtered = $delivery; break;
        case "restaurant": $sql_filtered = $restaurant; break;
        case "lecture": $sql_filtered = $lecture; break;
    }


    $sql_count = $sql_filtered. " ORDER BY
	    CASE
		    WHEN school = $school_id OR school LIKE '%$school_id%' THEN 1
            ELSE 2 END,
	    time DESC";
    $result_count = mysql_query($sql_count, $mydb);
    $total_count = mysql_num_rows($result_count);
    $to_be_added = $total_count - ($page * $unit);

    $json_data['num_results'] = iconv("CP949", "UTF-8", "$total_count");
    $json_data['page'] = $page;
    $json_data['unit'] = $unit;

    $sql_list = $sql_count." LIMIT ".strval($unit*$page).", {$unit}";
    $result_list = mysql_query($sql_list, $mydb);

    $sql_schools = "SELECT * FROM wicam.school";
    $result_schools = mysql_query($sql_schools);

    for ($i = 0; $i < (($unit < $to_be_added) ? $unit : $to_be_added); $i++) {
        mysql_data_seek($result_list, $i);
        $array = mysql_fetch_array($result_list);

        $school = '';
        for ($k = 0; $k < mysql_num_rows($result_schools); $k++) {
            mysql_data_seek($result_schools, $k);
            $array_school = mysql_fetch_array($result_schools);
            if ($array['school'] == $array_school['school_idx'] || !(strpos($array['school'], '|'.$array_school['school_idx'].'|') === false) ) {
                $school = $school.$array_school['school_name'].' ';
            }
        }

        $has_pic = 0;
        switch ($array['content']) {
            case 'advertise':
                $has_pic = file_exists('../image/advertise/item_'.$array['photo_idx'].'.jpg') ? 1 : 0;
                break;
            case 'custom':
                $has_pic = file_exists('../image/custom/item_'.$array['photo_idx'].'.jpg') ? 1 : 0;
                break;
            case 'delivery':
                $has_pic = file_exists('../image/delivery/'.$array['photo_idx'].'.jpg') ? 1 : 0;
                break;
            case 'restaurant':
                $has_pic = file_exists('../image/restaurant/'.$array['photo_idx'].'.jpg') ? 1 : 0;
                break;
            case 'lecture':
                $has_pic = 0;
                break;
        }

        $json_data['results'][$i] = array("idx"=>"$array[idx]", "content_idx"=>"$array[content_idx]", "photo_idx"=>"$array[photo_idx]",
            "title"=>"$array[title]", "text"=>"$array[text]", "person"=>"$array[person]",
            "value1"=>"$array[value1]", "value2"=>"$array[value2]", "has_pic"=>"$has_pic",
            "time"=>"$array[time]", "school"=>"$school", "content"=>"$array[content]");
    }


    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>