<?

#
#	file:	useradmin.php
#	author:	cameron "mercutio" mckay
#	purpose:	this file is used to create news users, edit existing ones, delete unwanted ones
#

require('config/nune.conf');

include($head);

#
# This is the main control structure for this file.  Depending on the POST results,
# it executes a function.  It's clean and clear.
#

if ($nune_access < 3) {

	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=newsadmin.php?special=admin\">";
	exit;

}

mysql_connect($hostname, $username, $password) or die('Unable to connect to database.');
mysql_select_db($database) or die('Unable to select database');

if (!$option && !$confirm) {	# if there is no mode argument

	display_option('');		# then just display the options

} else {					# otherwise, mode must be argumented

	if ($option)
		display_option($option);

	elseif ($confirm)
		send_confirm($confirm);

}						# now we're done that...

mysql_close();

include($foot);

#
# Functions
#

function display_option ($option_name) {

	if ($option_name == 'add') {

		echo "<H3><B>Add a new user</B></H3>\n";
		echo '<FORM ACTION="useradmin.php?confirm=add" METHOD="post">';
		echo 'Username:<BR>';
		echo '<INPUT TYPE="text" NAME="new_username" SIZE="20"><BR><BR>';
		echo 'Password:<BR>';
          echo '<INPUT TYPE="password" NAME="new_password" SIZE="20"><BR><BR>';
		echo 'E-Mail:<BR>';
          echo '<INPUT TYPE="text" NAME="new_email" SIZE="40"><BR><BR>';
		echo 'Access Level (1-3):<BR><BR>';
		echo '1:<INPUT TYPE="radio" NAME="new_access" VALUE="1" CHECKED>Can only edit and delete own posts.<BR>';
		echo '2:<INPUT TYPE="radio" NAME="new_access" VALUE="2">Can edit and delete any posts.<BR>';
		echo '3:<INPUT TYPE="radio" NAME="new_access" VALUE="3">Can edit and delete any posts, and create, edit and delete users.<BR><BR>';
		echo '<input type="submit" value="submit"> ';
		echo '<input type="reset" value="clear">';
		echo '</FORM>';

		echo '<A HREF="useradmin.php">Back to user options...</A>';

	} elseif ($option_name == 'edit') {

		global $usertable,$person;

		if ($person) {

			$result	= mysql_query("SELECT * FROM $usertable WHERE username='$person'");
			$row		= mysql_fetch_array($result);

			$row[username]	=	stripslashes($row[username]);
			$row[email]	=	stripslashes($row[email]);

			echo "<H3><B>Edit an existing user</B></H3>\n";
			echo '<FORM ACTION="useradmin.php?confirm=edit" METHOD="post">';
			echo 'Username:<BR>';
			echo "<B>$row[username]</B><BR><BR>";
			echo "<INPUT TYPE=\"hidden\" NAME=\"new_username\" VALUE=\"$row[username]\">";
			echo 'New Password:<BR>';
			echo '<INPUT TYPE="password" NAME="new_password" SIZE="20"><BR><BR>';
			echo 'Use old password?<BR>';
			echo "<INPUT TYPE=\"radio\" NAME=\"old_password\" VALUE=\"$row[password]\" CHECKED>Yes<BR>";
			echo '<INPUT TYPE="radio" NAME="old_password" VALUE="0">No<BR><BR>';
			echo 'E-Mail:<BR>';
			echo "<INPUT TYPE=\"text\" NAME=\"new_email\" VALUE=\"$row[email]\" SIZE=\"40\"><BR><BR>";
			echo 'Access Level (1-3):<BR><BR>';

			if ($row[access] == 1)
				$CHECKED[1] = 'CHECKED';

			elseif ($row[access] == 2)
				$CHECKED[2] = 'CHECKED'; 

			elseif ($row[access] == 3)
				$CHECKED[3] = 'CHECKED';

			echo '1:<INPUT TYPE="radio" NAME="new_access" VALUE="1"' . " $CHECKED[1]" . '>
			Can only edit and delete own posts.<BR>';
			echo '2:<INPUT TYPE="radio" NAME="new_access" VALUE="2"' . " $CHECKED[2]" . '>
			Can edit and delete any posts.<BR>';
			echo '3:<INPUT TYPE="radio" NAME="new_access" VALUE="3"' . " $CHECKED[3]" . '>
			Can edit and delete any posts, and create, edit and delete users.<BR><BR>';
			echo "<input type=\"hidden\" name=\"user_id\" value=\"$row[user_id]\">\n";
			echo '<input type="submit" value="submit"> ';
			echo '<input type="reset" value="clear">';
			echo '</FORM>';

			echo '<A HREF="useradmin.php">Back to user options...</A>';

		} else {

			$result	= mysql_query("SELECT username,access FROM $usertable");
		
			echo '<FORM ACTION="useradmin.php?option=edit" METHOD="post">';
			echo 'Select a user to edit...<BR><BR>';
			echo '<SELECT NAME="person" SIZE="5">';
			
			while ($row = mysql_fetch_array($result)) {
				$userlist[] = $row['username'];
				$access[]   = $row['access'];
			}

			for ($i = 0; $i < count($userlist); $i++)
				echo "<OPTION VALUE=\"$userlist[$i]\">&gt;&gt; [Level $access[$i]] $userlist[$i]\n";

			echo '</SELECT><BR><BR>';
			echo '<input type="submit" value="submit"> ';
			echo '<input type="reset" value="clear">';
			echo '</FORM>';

		}		

	} elseif ($option_name == 'delete') {

		global $usertable;

		$result   = mysql_query("SELECT username FROM $usertable");

		echo '<FORM ACTION="useradmin.php?confirm=delete" METHOD="post">';
		echo 'Select a user to vaporize...<BR><BR>';
		echo '<SELECT NAME="person" SIZE="5">';

		while ($row = mysql_fetch_array($result))
			$userlist[] = $row['username'];

		for ($i = 0; $i <= count($userlist)-1; $i++)
			echo "<OPTION VALUE=\"$userlist[$i]\">$userlist[$i]\n";

		echo '</SELECT><BR><BR>';
		echo '<input type="submit" value="submit"> ';
		echo '<input type="reset" value="clear">';
		echo '</FORM>';

	} else {

		echo '<H3>User Admin Menu...</H3>';

		echo '<A HREF="useradmin.php?option=add"> Add a new user </A><BR>';	
		echo '<A HREF="useradmin.php?option=edit"> Edit an existing user </A><BR>';
		echo '<A HREF="useradmin.php?option=delete"> Delete an old user </A><BR>';
		echo '<BR>';
		echo "<a href=\"newsadmin.php?special=admin\">Back to options...</a>";

	}

}

function send_confirm ($argument) {

	global $usertable;

	if ($argument == 'add') {

		#
		# well, this is actually the very first thing we do is import the global variables we need
		#

		global $new_username, $new_password, $new_email, $new_access;

		#
		# first thing we do is check if the user name already exists
		#

		$result	= mysql_query("select * from $usertable where username=\"$new_username\"");
		$check 	= mysql_num_rows($result);

		if ($check) {

			echo 'User already exists!';

		} else {

			$result = 
		mysql_query("INSERT INTO $usertable VALUES('','$new_username',PASSWORD('$new_password'),'$new_email','$new_access')");

			$user_id = mysql_insert_id();

			echo "User successfully created.<BR><BR>";

			echo "Username: $new_username<BR>";
			echo "User ID: $user_id<BR>";
			echo "E-Mail: $new_email<BR>";
			echo "Access Level: $new_access";

		}
	
	} elseif ($argument == 'edit') {

		global $new_username, $new_password, $new_email, $new_access, $old_password, $user_id;

		if ($old_password)
			$result = 
			mysql_query("REPLACE INTO $usertable VALUES('$user_id','$new_username','$old_password','$new_email','$new_access')");

		else
			$result = 
			mysql_query("REPLACE INTO $usertable VALUES('$user_id','$new_username',PASSWORD('$new_password'),'$new_email','$new_access')");

		echo "User successfully changed.<BR><BR>";

          echo "Username: $new_username<BR>";
		echo "User ID: $user_id<BR>";
          echo "New E-Mail: $new_email<BR>";
          echo "New Access Level: $new_access";

	} elseif ($argument == 'delete') {

		global $person, $nune_username;

		if ((strcasecmp($person, $nune_username)) == 0) {

			echo "User to delete is same as current user.<BR>\n";
			echo "Cannot allow deletion.\n";

		} else {

			$result	= mysql_query("DELETE FROM $usertable WHERE(username='$person')");

			echo "User successfully terminated.<BR><BR>";
			echo "Username: $person is now unperson.";
	
		}

	}

	#
     # make the page wait for 2 seconds and then reload
     #

     echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=useradmin.php\">";

}
