<?

require("$DOCUMENT_ROOT/news/admin/config/nune.conf");
require("$DOCUMENT_ROOT/news/admin/lib/nune-lib.php");

mysql_connect($hostname, $username, $password) or die('Unable to connect to database');
mysql_select_db($dbName) or die('Unable to select database');

if (!isset($show)) {

	$show = 0;

}

$result1 	= mysql_query( "SELECT Count(*) as total FROM $newstable") or die(mysql_error()); 
$row		= mysql_fetch_Array($result1); 
$total	= $row[total];

if ($show < $total && $list == 'archive') {

     $query =  "SELECT * FROM $newstable order by artnr desc LIMIT $limit, -1";

} elseif ($show < $total) {

     $query =  "SELECT * FROM $newstable order by artnr desc LIMIT $show, $limit";

} elseif ($list == 'archive') {

     $query    =  "SELECT * FROM $newstable WHERE(artnr LIKE '%$id%') order by artnr DESC LIMIT $limit, -1";

} else {

     $query    =  "SELECT * FROM $newstable WHERE(artnr LIKE '%$id%') order by artnr DESC LIMIT $show, $limit";

}

$insert = MYSQL_QUERY($query);
$number = MYSQL_NUMROWS($insert);

$i = 0;

if ($number < $i) {

	echo "No articles in database.";

} elseif ($list) {

	nune_list($list);

} else {

	nune_list('news');

}

mysql_close();

?>
