<?

#
#    file:     commentadmin.php
#    author:   cameron "mercutio" mckay
#    purpose:	this program is used to edit and delete comments
#

require('config/nune.conf');

include($head);

if ($nune_access < 2) {

	# Return back to menu if they're not at least a moderator

	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=newsadmin.php?special=admin\">";
	exit;

}

mysql_connect($hostname, $username, $password) or die('Unable to connect to database.');
mysql_select_db($database) or die('Unable to select database');

if (!$option && !$confirm) {  # if there is no mode argument

	display_option('');      # then just display the options

} else {                      # otherwise, mode must be argumented

	if ($option) 
		display_option($option);

	elseif ($confirm)
		send_confirm($confirm);

}                             # now we're done that...

mysql_close();

include($foot);

#
# Functions!
#

function display_option ($option) {

	global $cid, $commtable, $custom_rank;

	if ($option == 'edit') {

		if (comment_exists($cid)) {

			if ($custom_rank) {

				$rank = $custom_rank;

			} else {

				$rank[0]	=	'Anonymous';
				$rank[1]	=	'Member';
				$rank[2]	=	'Moderator';
				$rank[3]	=	'Administrator';

			}

			$result	= mysql_query("SELECT * FROM $commtable WHERE cid=$cid");

			$row      = mysql_fetch_array($result);

			$user	=    stripslashes($row[username]);
               $email	=    stripslashes($row[email]);
               $comment	=    stripslashes($row[comment]);

			echo "<H3><B>Edit comment id: $cid</B> (Associated with Post id: $row[assoc_post])</H3>\n";
			echo '<FORM ACTION="commentadmin.php?confirm=edit" METHOD="post">';
			echo 'Username:<BR>';
			echo "<INPUT TYPE=\"text\" SIZE=\"20\" MAXLENGTH=\"20\" NAME=\"new_user\" VALUE=\"$user\"><BR><BR>";
			echo 'Host:<BR>';
			echo "<B>$row[host]</B><BR><BR>";
			echo "<INPUT TYPE=\"hidden\" NAME=\"host\" VALUE=\"$row[host]\">";
			echo 'E-Mail:<BR>';
			echo "<INPUT TYPE=\"text\" NAME=\"new_email\" VALUE=\"$email\" SIZE=\"40\" MAXLENGTH=\"50\"><BR><BR>";
			echo 'Post Rank:<BR>';

			if ($row[post_rank] == 0)
				$CHECKED[0] = 'CHECKED';

               elseif ($row[post_rank] == 1)
                    $CHECKED[1] = 'CHECKED';

               elseif ($row[post_rank] == 2)
                    $CHECKED[2] = 'CHECKED';

               elseif ($row[post_rank] == 3)
                    $CHECKED[3] = 'CHECKED';

			for($i = 0; $i <= 3; ++$i) 
               	echo "$i:<INPUT TYPE=\"radio\" NAME=\"new_rank\" VALUE=\"$i\" $CHECKED[$i]>$rank[$i]<BR>";

			echo "<BR>Comment Text:<BR>";
			echo "<TEXTAREA NAME=\"new_comment\" COLS=60 ROWS=7 WRAP=PHYSICAL>$comment</TEXTAREA><BR>";

			echo "<br><INPUT TYPE=\"hidden\" name=\"date\" value=\"$row[date]\">";
			echo "<INPUT TYPE=\"hidden\" name=\"cid\" value=\"$cid\">";
			echo "<INPUT TYPE=\"hidden\" name=\"assoc_post\" value=\"$row[assoc_post]\">";

			echo '<INPUT TYPE="submit" VALUE="submit"> <INPUT TYPE="reset" VALUE="clear">';
			echo '</FORM>';

			echo "Comment's Date was $row[date]<BR><BR>";

		} else {

			echo "I'm sorry Comment id $cid does not exist.\n";
			echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=commentadmin.php\">";

		}

	} else if ($option == 'delete') {

		if (comment_exists($cid)) {

			$result 	= mysql_query("DELETE FROM $commtable WHERE cid=$cid");

			if ($result)
				echo "Comment id $cid was successfully deleted.\n";

			else
				echo "An error occured during deletion.\n";

		} else {

			echo "I'm sorry Comment id $cid does not exist.\n"; 

		}

		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=commentadmin.php\">";

	} else if ($option == 'fastadmin') {

		$result = mysql_query("SELECT * FROM $commtable ORDER BY assoc_post DESC");

		echo "<H3>Comment FastAdmin</H3>\n";

		while ($row = mysql_fetch_array($result)) {

			$user     =    stripslashes($row[username]);

			echo "[ <A HREF=\"commentadmin.php?option=edit&cid=$row[cid]\">Edit</A> | ";
			echo "<A HREF=\"commentadmin.php?option=delete&cid=$row[cid]\">Delete</A> ] ";
			echo "Article $row[assoc_post] (cid $row[cid]) by $user @ $row[date]<BR>\n";

		}

		echo "<BR><a href=\"commentadmin.php\">Back to Comment Admin...</a>";

	} else if ($option == 'easyadmin') {

		global $limit, $post, $newstable, $commtable;

		echo "<H3>Comment EasyAdmin</H3>\n";

		$result = mysql_query("SELECT * FROM $newstable ORDER BY pid DESC LIMIT 0, $limit");

		while ($row = mysql_fetch_array($result)) {

			echo "[ <A HREF=\"commentadmin.php?option=easyadmin&post=$row[pid]\">Show Comments</A> ] ";
			echo "Article $row[pid] - <B>$row[title]</B> by $row[author]<BR>\n";

			if ($post == $row[pid]) {

				$query = mysql_query("SELECT * FROM $commtable WHERE assoc_post=$row[pid] ORDER BY cid");

				while ($row = mysql_fetch_array($query)) {

					$user     =    stripslashes($row[username]);

					echo "&nbsp&nbsp [ <A HREF=\"commentadmin.php?option=edit&cid=$row[cid]\">Edit</A> | ";
					echo "<A HREF=\"commentadmin.php?option=delete&cid=$row[cid]\">Delete</A> ] ";
					echo "Comment id $row[cid] by $user @ $row[date]<BR>\n";

				}

			}

		}

		echo "<BR><a href=\"commentadmin.php\">Back to Comment Admin...</a>";

     } else {

          echo '<H3>Comment Admin Menu...</H3>';

		echo '<A HREF="commentadmin.php?option=fastadmin"> Comment FastAdmin </A><BR>';
          echo 'Comment FastAdmin allows you to quickly browse through every single comment and choose to either edit or delete each comment.<BR><BR>';

		echo '<A HREF="commentadmin.php?option=easyadmin"> Comment EasyAdmin </A><BR>';
          echo 'Comment EasyAdmin allows you to browse through each recent post and choose to either view, edit or delete each comment within that post.<BR><BR>';

		echo '<U>Comment QuickEdit</U><BR>';
		echo 'If you already know the comment id number, you can quickly type it in here to save some time.<BR>';

		echo '<FORM ACTION="commentadmin.php" METHOD="post">' . "\n";

		echo 'Comment id (cid): <INPUT TYPE="text" NAME="cid" SIZE="8" MAXLENGTH="20"><BR><BR>' . "\n";

		echo '<INPUT TYPE="radio" NAME="option" VALUE="edit" CHECKED>Edit Comment<BR>';
		echo '<INPUT TYPE="radio" NAME="option" VALUE="delete">Delete Comment<BR><BR>';
	
		echo ' <INPUT TYPE="submit" VALUE="Edit or Delete Comment"></FORM>' . "\n";

          echo "<a href=\"newsadmin.php?special=admin\">Back to options...</a>";

     }

}

function send_confirm ($argument) {

	global $commtable;

	if ($argument == 'edit') {

		global $cid, $assoc_post, $new_user, $new_email, $host, $new_comment, $date, $new_rank;

		$new_user		= addslashes($new_user);
		$new_email	= addslashes($new_email);
		$new_comment	= addslashes($new_comment);

		$result 	= mysql_query("REPLACE INTO $commtable VALUES('$cid','$assoc_post','$new_user','$new_email','$host','$new_comment','$date','$new_rank')");

		if ($result)
			echo "Comment id $cid successfully changed.";

		else
			echo "An error occured while trying to change comment id $cid.";

	}

	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=commentadmin.php\">";

}
	
#
# General Functions
#

#
# comment_exists -- checks to see if the cid exists
# returns: 1 is it does, 0 if it doesn't
#

function comment_exists ($cid) {

	global $commtable;

	$result = mysql_query("SELECT * FROM $commtable WHERE cid=$cid");
	$check = mysql_num_rows($result);

	if ($check)
		return 1;

	else
		return 0;

}
