<?

#
# You only need to set this variable if you're having
# problems getting NUNE to work.
#$custom_admin_path = '/home/apache/htdocs/banana/news/admin';

if (isset($custom_admin_path))
	$special_admin_path = $custom_admin_path;

else
	$special_admin_path = "news/admin";

require("$special_admin_path/config/nune.conf");
require("$special_admin_path/lib/nune-lib.php");

mysql_connect($hostname, $username, $password) or die('Unable to connect to database.');
mysql_select_db($database) or die('Unable to select database.');

if ($comment)
	nune_comment($comment);

else
	nune_list('archives');

mysql_close();

?>
