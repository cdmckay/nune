<?

#
# Now the rest...
#

require('config/nune.conf');
require('lib/nune-lib.php'); 

mysql_connect($hostname, $username, $password) or die('Unable to connect to database.');
mysql_select_db($database) or die('Unable to select database.');

if ($option == 'archives' || $option == 'archives_easyadmin')
	$query =  "SELECT * FROM $newstable ORDER BY pid DESC LIMIT $limit, -1";

else 
	$query =  "SELECT * FROM $newstable order by pid DESC LIMIT 0, $limit";

$important_result 	= mysql_query($query);

#
# include header file
#

if ($special != 'logout')
	include($head);

#
# decide what to do
#

if ($option)
	nune_option($option);

elseif ($list)
	nune_list($list);

elseif ($form)
	nune_form($form);

elseif ($confirm)
	nune_confirm($confirm);

elseif ($special)
	nune_special($special);

else
	echo 'An error has occured.  Invalid arguments.';

include($foot);

mysql_close();

?>
