<?

#
# This part deals with the date...
#

$nicedate = date("[h:i A] F d Y");

#
# Now the rest...
#

require('config/nune.conf');
require('lib/nune-lib.php'); 

mysql_connect($hostname, $username, $password) or die('Unable to connect to database');
mysql_select_db($dbName) or die('Unable to select database');

if (!isset($show)) {
	$show = 0;
}

$result1 	=	mysql_query( "SELECT Count(*) as total FROM $newstable") or die(mysql_error()); 
$row		=	mysql_fetch_Array($result1); 
$total	=	$row[total];

if ($show < $total && $type == 'archive') {

	$query =  "SELECT * FROM $newstable order by artnr desc LIMIT $limit, -1";

} elseif ($show < $total) {

	$query =  "SELECT * FROM $newstable order by artnr desc LIMIT $show, $limit";

} elseif ($show = all) {

	$query =  "SELECT * FROM $newstable order by artnr desc";

} elseif ($type == 'archive') {

     $query    =  "SELECT * FROM $newstable WHERE(artnr LIKE '%$id%') order by artnr DESC LIMIT $limit, -1";

} else {

	$query	=  "SELECT * FROM $newstable WHERE(artnr LIKE '%$id%') order by artnr DESC LIMIT $show, $limit";

}

$insert 	= mysql_query($query);
$number 	= mysql_num_rows($insert);

$i = 0;

#
# include header file
#

if ($special != 'logout') {

	include("$head");

}

#
# decide what to do
#

if ($number < $i) {

	echo "No articles in databases.";

} elseif ($list) { 

	nune_list($list);

} elseif ($form) {

	nune_form($form);

} elseif ($confirm) {

	nune_confirm($confirm);

} elseif ($special) {

	nune_special($special);

} else {

	echo 'Neep!';

}

include("$foot");

mysql_close();

?>
