<?php
/**
 * Hyeonmin Ko
 */

header("Content-Type: text/html; charset=UTF-8");

$mydb=mysql_connect( "localhost", "hungry", "handongi") or
die( "SQL server에 연결할 수 없습니다.");

mysql_query("SET NAMES UTF8");
mysql_select_db("hungryhandongi",$mydb);

$restaurant_image_dir = 'http://hungry.portfolio1000.com/wicam/image/restaurant/';

// admin 이 1인, 즉 관리자의 폰측 SharedPreference에 발급되는 코드.  정보의 변경이나 제거를 관리자에게 허용하는데 사용한다.
$authority_code = 'Abandoned; phone changed';

?>