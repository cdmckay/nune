<?

#
# You only need to set this variable if you're having
# problems getting NUNE to work.
#$custom_admin_path = '/home/apache/htdocs/news/admin';

if (isset($custom_admin_path))
	$special_admin_path = $custom_admin_path;

else
	$special_admin_path = "news/admin";

require("$special_admin_path/config/nune.conf");
require("$special_admin_path/lib/nune-lib.php");

mysql_connect($hostname, $username, $password) or die('Unable to connect to database.');
mysql_select_db($database) or die('Unable to select database.');

$query =  "SELECT * FROM $newstable ORDER BY pid DESC LIMIT 0, $limit";

#
# Find out how many articles are in the database
#

$important_result 	= mysql_query($query);
$number 			= mysql_num_rows($important_result);

if ($number < 1)
	echo "No articles in database.";

elseif ($comment) 
	nune_comment($comment);

elseif ($list)
	nune_list($list);

else
	nune_list('news');

mysql_close();

?>
