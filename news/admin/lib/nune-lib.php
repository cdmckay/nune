<?

#
#    name:     nune-lib.php
#    author:   cameron 'mercutio' mckay
#    purpose:  the functions which NUNE runs on (the "core" of the script if you will)
#
#    version:	2.0
#

#
# The nune_list function is used for displaying lists of items, used when edit_list, list and delete_list
# POST vars are given
#

function nune_list ($type) {

	global 	$important_result;			# from news/index.php 

	if ($type == 'news') {

		while ($row = mysql_fetch_array($important_result)) {

			$row[title]		=	stripslashes($row[title]);
			$row[article]		=	stripslashes($row[article]);
			$row[email]		=	stripslashes($row[email]);

		     print_html($row[pid],$row[title],$row[article],$row[author],$row[email],$row[date]);

		}

	} elseif ($type == 'headlines') {

		while ($row = mysql_fetch_array($important_result)) {

			$row[title]		=	stripslashes($row[title]);
			
			echo "<A HREF=\"index.php#$row[pid]\">$row[title]</A><BR>";

		}

	} elseif ($type == 'archives') {

		global 	$_comment, $_archive, $error_path, $month, $newstable, $archives_page_path, $font,
				$arc_Month, $arc_Week, $arc_year;

		if (!$_archive[enable]) {

			$error = "Archives are not enabled.\n";
			include($error_path);
			exit;

		}

		if ($arc_Month && $arc_year) {

			$date = sprintf("%04d-%02d-__", $arc_year, $arc_Month);

			$query = mysql_query("SELECT * FROM $newstable WHERE internal_date LIKE '$date' ORDER BY pid DESC");

			while ($row = mysql_fetch_array($query)) {

				$row[title]         =    stripslashes($row[title]);
     	          $row[article]       =    stripslashes($row[article]);
     	          $row[email]         =    stripslashes($row[email]);
	
          	     print_html($row[pid],$row[title],$row[article],$row[author],$row[email],$row[date]);

          	}

		} elseif ($arc_Week && $arc_year) {

			$query = mysql_query("SELECT * FROM $newstable WHERE WEEK(internal_date)=$arc_Week AND YEAR(internal_date)=$arc_year ORDER BY internal_date DESC");

			while ($row = mysql_fetch_array($query)) {

				$row[title]         =    stripslashes($row[title]);
				$row[article]       =    stripslashes($row[article]);
				$row[email]         =    stripslashes($row[email]);

				print_html($row[pid],$row[title],$row[article],$row[author],$row[email],$row[date]);

			}

		} else {
	
			if ($_archive[sort_by] == 0) {

				$special_query	= 'MONTH(internal_date) AS Month, MONTHNAME(internal_date) AS monthy,';
				$special_as	= 'Month';

			} elseif ($_archive[sort_by] == 1) {

				$special_query = 'WEEK(internal_date) AS Week,';
				$special_as	= 'Week';

			}

			$query = mysql_query("SELECT $special_query YEAR(internal_date) AS year FROM $newstable ORDER BY pid DESC");

			while($row   = mysql_fetch_array($query)) {

				if ($hold[$special_as] != $row[$special_as] || $hold[year] != $row[year]) {

					$list .= "<A HREF=\"?show_archives=1&arc_$special_as=$row[$special_as]&arc_year=$row[year]\">$special_as $row[$special_as], Year $row[year] ($row[monthy])</A><BR>";

				}

				$hold[$special_as] = $row[$special_as];
				$hold[year] = $row[year];

			}

			include($archives_page_path);

		}

	}

}

function nune_option ($type) {

	global    $important_result,            # from news/index.php
			$nune_access,$nune_username,  # from header
			$font,$newstable,			# from nune.conf
			$when;                        # from POST

	if ($type == 'news') {

		echo '<H3>News Admin Menu...</H3>';

		echo '<A HREF="newsadmin.php?option=news_easyadmin"> News EasyAdmin </A><BR>';
          echo 'News EasyAdmin allows you to browse through each post and choose to either edit or delete. <BR><BR>';

          echo '<U>News QuickEdit</U><BR>';
          echo 'If you already know the post id number, you can quickly type it in here to save some time.<BR>';

          echo '<FORM ACTION="newsadmin.php" METHOD="post">' . "\n";
          echo 'Post id (pid): <INPUT TYPE="text" NAME="item" SIZE="8" MAXLENGTH="20"><BR><BR>' . "\n";

		echo '<INPUT TYPE="radio" NAME="form" VALUE="edit" CHECKED>Edit Post<BR>';
		echo '<INPUT TYPE="radio" NAME="confirm" VALUE="delete">Delete Post<BR><BR>';

          echo ' <INPUT TYPE="submit" VALUE="Edit or Delete Post"></FORM>' . "\n";

          echo "<a href=\"newsadmin.php?special=admin\">Back to options...</a>";

	} elseif ($type == 'news_easyadmin') {

		echo "<H3>News EasyAdmin</H3>\n";

		while ($row = mysql_fetch_array($important_result)) {

			if ($nune_access > 1 || ($nune_access == 1 && $row[author] == $nune_username)) {

				echo "[ <A HREF=\"newsadmin.php?form=edit&item=$row[pid]\">Edit</A> | ";
				echo "<A HREF=\"newsadmin.php?confirm=delete&item=$row[pid]\">Delete</A> ] ";

			} else {

				echo '[ Edit | Delete ] ';

			}

			echo "Article $row[pid] - <B>$row[title]</B> by $row[author]<BR>\n";

		}

		echo "<BR><a href=\"newsadmin.php?option=news\">Back to News Admin...</a>";

	} elseif ($type == 'archives') {

		global $month, $_archive;

		#
		# This code determines min and max year
		#

		$query 	= mysql_query("SELECT YEAR(internal_date) as year FROM $newstable ORDER BY pid");
		$min_year = mysql_result($query, 0);

		$query	= mysql_query("SELECT YEAR(internal_date) as year FROM $newstable ORDER BY pid DESC");
		$max_year = mysql_result($query, 0);

		echo '<H3>Archives Admin Menu...</H3>';

		echo '<A HREF="newsadmin.php?option=archives_easyadmin"> Archives EasyAdmin </A><BR>';
		echo 'Allows you to see every archived news article in one screen... is good for small sites but gets rather unruly for large sites.<BR><BR>';

		echo '<U>Archives AdvancedAdmin</U><BR>';
		echo 'You can use AdvancedAdmin to browse the archives by month. Or month and day. Or month and day and year.<BR><BR>';

		echo '<TABLE BORDER="0"><TR>';

		echo '<FORM ACTION="newsadmin.php?option=archives_easyadmin" METHOD="post">';
		echo '<TD>Month:</TD><TD><SELECT NAME="monthy">';
		
		echo '<OPTION VALUE="0">None';
		for ($i = 1; $i < count($month); ++$i)
			echo "<OPTION VALUE=\"$i\">$month[$i]\n";

		echo '</SELECT></TD></TR><TR>';
		echo '<TD>Day:</TD><TD><SELECT NAME="day">';
		
		echo '<OPTION VALUE="0">None';
		for ($i = 1; $i <= 31; ++$i)
			echo "<OPTION VALUE=\"$i\">$i\n";


		echo '</SELECT></TD></TR><TR>';
		echo '<TD>Year:&nbsp;&nbsp; </TD><TD><SELECT NAME="year">';

		echo '<OPTION VALUE="0">None';
		for ($i = $min_year; $i <= $max_year; ++$i) {

			echo "<OPTION VALUE=\"$i\">$i\n";

		}

		echo '</SELECT></TD></TR></TABLE><BR>';
		echo '<INPUT TYPE="submit" VALUE="Edit or Delete Posts">';
		echo '</FORM>';
	
		echo "<a href=\"newsadmin.php?special=admin\">Back to options...</a>";
	
	} elseif ($type == 'archives_easyadmin') {

		global	$important_result,
			 	$monthy, $day, $year;

		if ($monthy || $day || $year) {

			$admin_type = 'Advanced';

			if ($year)
                    $lookup = sprintf("%04d-", $year);

               else
                    $lookup = '____-';


			if ($monthy)
				$lookup .= sprintf("%02d-", $monthy);

			else
				$lookup .= '__-';

			if ($day)
				$lookup .= sprintf("%02d", $day);

			else
				$lookup .= '__';

		} else {

			$admin_type	= 'Easy';
			$lookup		= 'NO!';

		}

		if ($lookup != '____-__-__' && $lookup != 'NO!') {

			$query = mysql_query("SELECT * FROM $newstable WHERE internal_date LIKE '$lookup'");
			$important_result = $query;

		}			

		echo "<H3>Archives {$admin_type}Admin</H3>\n";

		if ($admin_type = 'Advanced') {

			if (!$monthy)
				$monthy = 'None';

			if (!$day) 
				$day = 'None';

			if (!$year)
				$year = 'None';

			echo "Month: $monthy, Day: $day, Year: $year<BR><BR>";

		}

		while ($row = mysql_fetch_array($important_result)) {

			if ($nune_access > 1) {

				echo "[ <A HREF=\"newsadmin.php?form=edit&item=$row[pid]\">Edit</A> | ";
				echo "<A HREF=\"newsadmin.php?confirm=delete&item=$row[pid]\">Delete</A> ] ";

			} else {

				echo '[ Edit | Delete ] ';

			}

				echo "Article $row[pid] - <B>$row[title]</B> by $row[author]<BR>\n";

		}

		echo "<BR><a href=\"newsadmin.php?option=archives\">Back to Archives Admin...</a>";

	}
	
}

function nune_form ($type) {

	global	$nune_username,$nune_email,$nune_access,$item,$newstable,$news_date,$internal_date;

	if ($type == 'add') {

		echo '<H3><B>Add a new article</B></H3>';
		echo '<form action="newsadmin.php?confirm=add" method="post">';
		echo 'Title:<br>';
		echo '<input type="text" name="title" size="40"><BR><BR>';
		echo "Article Text:<BR>";
		echo '<TEXTAREA NAME="article" COLS=60 ROWS=7 WRAP=PHYSICAL></TEXTAREA><BR><BR>';
		echo 'Author:<BR>';
		echo "<B>$nune_username</B><BR><BR>";
		echo "<INPUT TYPE=\"hidden\" NAME=\"author\" VALUE=\"$nune_username\">";
		echo "E-Mail Address:<br>";
		echo "<input type=\"text\" name=\"email\" value=\"$nune_email\" size=\"40\"><BR><BR>";
		echo '<INPUT TYPE="submit" VALUE="submit"> <INPUT TYPE="reset" VALUE="clear">';
		echo "<br><input type=\"hidden\" name=\"date\" value=\"$news_date\">";
		echo '</form>';
		echo "Today is $news_date<BR><BR>";
		echo '<a href="newsadmin.php?list=news">View a list of all items</a>';

	} elseif ($type == 'edit') {

		$query = mysql_query("SELECT * FROM $newstable WHERE pid=$item") or die(mysql_error());

		while ($row  =  mysql_fetch_array($query)) {

			$pid			=    $row[pid];
			$title		=    stripslashes($row[title]);
			$author		=    stripslashes($row[author]);
			$article		=    stripslashes($row[article]);
			$email		=    stripslashes($row[email]);
			$date		=    $row[date];
			$internal_date	=	$row[internal_date];

		}

		if ($nune_access == 1 && $nune_username != $author) {

			echo "Sorry, this is not your post.\n";
			exit;

		}

          echo '<H3><B>Edit an existing article</B></H3>';
          echo "<form action=\"newsadmin.php?confirm=edit&item=$item\" method=\"post\">";
          echo 'Title:<br>';
		echo "<input type=\"text\" name=\"title\" size=\"40\" value=\"$title\"><BR><BR>";
          echo "Article Text:<BR>";
          echo "<TEXTAREA NAME=\"article\" COLS=60 ROWS=7 WRAP=PHYSICAL>$article</TEXTAREA><BR><BR>";
          echo 'Author:<BR>';
          echo "<B>$author</B><BR><BR>";
          echo "<INPUT TYPE=\"hidden\" NAME=\"author\" VALUE=\"$author\">";
          echo "E-Mail Address:<br>";
          echo "<input type=\"text\" name=\"email\" value=\"$email\" size=\"40\"><BR><BR>";
          echo '<INPUT TYPE="submit" VALUE="submit"> <INPUT TYPE="reset" VALUE="clear"><BR>';
          echo "<input type=\"hidden\" name=\"date\" value=\"$news_date\">";
		echo "<INPUT TYPE=\"hidden\" NAME=\"internal_date\" VALUE=\"$internal_date\">";
          echo '</form>';
          echo "Article's Date was $date";

	} 

}
	
function nune_confirm ($type) {

	global	$pid, $title, $article, $author, $email, $date, $item,		# from POST
			$newstable,$font,$_comment,$commtable,					# from nune.conf
			$nune_username, $nune_access, $html_enable;

	if ($type == 'add') {

		if (!$html_enable) 
			$article = strip_tags($article);

		$title         =    addslashes($title);
		$article		=    addslashes($article);
		$author        =    addslashes($author);
		$email         =    addslashes($email);

		$result = mysql_query("INSERT INTO $newstable VALUES('','$title','$article','','$author','$email','$date',NOW())");

		$query  = mysql_query("SELECT * FROM $newstable WHERE(pid=$pid)");

		$pid = mysql_insert_id();

		$title		= stripslashes($title);
		$article		= stripslashes($article);

		echo "<FONT FACE=\"$font\">";
		echo "<B>Article $pid: $title</B> by <B>$author ($email)</B> has been added.<BR><BR>";
		echo "Article Text:<BR>$article<BR>";
		echo '</FONT>';

		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=newsadmin.php?special=admin\">";

	} elseif ($type == 'edit') {

		global $internal_date;

		if (!$html_enable)
			$article = strip_tags($article);

		$title         =    addslashes($title);
		$article		=    addslashes($article);
		$author        =    addslashes($author);
		$email         =    addslashes($email);

		$result = mysql_query("REPLACE INTO $newstable VALUES('$item','$title','$article','','$author','$email','$date','$internal_date')");

		$title	= stripslashes($title);
          $article	= stripslashes($article);

		echo "<FONT FACE=\"$font\">";
          echo "<B>Article $item: $title</B> by <B>$author ($email)</B> has been updated.<BR><BR>";
          echo "Article Text:<BR>$article<BR>";
          echo '</FONT>';

          echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=newsadmin.php?special=admin\">";

	} elseif ($type == 'delete') {

		$result = mysql_query ("SELECT author FROM $newstable WHERE pid=$item");
		$author = mysql_result($result, 0);

		if ($nune_access == 1 && $nune_username != $author) {

               echo "Sorry, this is not your post.\n";
               exit;

          }

		$insert = mysql_query("DELETE FROM $newstable WHERE(pid=$item)");

		# if auto_delete is on, then delete all the comments from that post as well

		if ($_comment[auto_delete])
			$query = mysql_query("DELETE FROM $commtable WHERE(assoc_post=$item)");

		echo "<FONT FACE=\"$font\">";
          echo "<B>Article $item</B> has been deleted.<BR>";

		if ($_comment[auto_delete])
			echo "And so have all the comments associated with it.<BR>";

		else
			echo "Warning: Comments associated with it remain.<BR>";
		
          echo '</FONT>';

		print "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"1; URL=newsadmin.php?special=admin\">";

	}

}		
	
function nune_special ($type) {

	global	$nune_access,$nune_username,
			$www_site_address, $www_site_name, $www_cookie_path, $cookie_name, $head,
			$_comment, $_archive;

	if ($type == 'admin') {

		echo '<H3>Main Admin Menu...</H3>';

		echo "<a href=\"newsadmin.php?form=add\">Add a new article</a><br>";
		echo '<BR>';
		echo "<A HREF=\"newsadmin.php?option=news\">Edit or delete recent news</A><BR>";
		echo "<a href=\"newsadmin.php?option=archives\">Edit or delete archived news</a><br>";

		if ($_comment[enable] && $nune_access > 1) {

			echo '<A HREF="commentadmin.php"> Edit or delete comments</A><BR>';

		}

		if ($nune_access == 3) {

     		echo '<BR><A HREF="useradmin.php"> Add, edit or delete users</A><BR>';

		}

		echo "<BR>\n";
		echo "<a href=\"newsadmin.php?special=logout\">Logout of NUNE...</A><br>";
		echo "<BR>\n";
		echo "<a href=\"http://$www_site_address\">Return to $www_site_name...</a>";
	
	} elseif ($type == 'logout') {

		#
		# prepare yourself for the most advanced code ever seen by a human being
		#

		setcookie($cookie_name[auth],'nothing',time()+3600,"/",$www_cookie_path,0);

		include("$head");

		echo 'Cookie should be removed.<BR>';
		echo "You are logged out, $nune_username.";

		#
		# make the page wait for 2 seconds and then reload
		#

		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=index.php\">";

	}

}
	
function nune_comment ($assoc_post) {

	global 	$commtable, $newstable;	# from nune.conf: mysql

	#
	# If it is an integer then we want to view comments
	#

	if ($assoc_post != 'confirm') {

		#
		# Grab the associated post and print it.
		#

		$query = mysql_query("SELECT * FROM $newstable WHERE pid=$assoc_post") or die(mysql_error());

		while ($row  =  mysql_fetch_array($query)) {
	
			$pid			=    $row[pid];
			$title         =    stripslashes($row[title]);
			$author        =    stripslashes($row[author]);
			$article		=    stripslashes($row[article]);
			$email         =    stripslashes($row[email]);
			$date          =    $row[date];

		}

		print_html($pid,$title,$article,$author,$email,$date);

		print_comments($assoc_post);

		print_comment_input($assoc_post);

	} else {

		#
		# Otherwise it wants confirmation...
		#

		global 	$com_user, $com_email, $com_comment, $host, $pid, $com_pass, $time, 		# from POST
				$_comment,
				$cookie_name, $$cookie_name[comments],	# the auto_cookie
				$www_cookie_path, $font,				# from nune.conf: misc
				$comment_confirm_path, $error_path;	# from nune.conf: paths

		#
		# First we'll start off the html
		#

		if ((strlen($com_user) == 0) || (strlen($com_comment) == 0)) {

			$check = -1;

		} else if ($_comment[auth]) {

			$check 	= nune_auth($com_user, $com_pass);
			$rank	= $check;

		} else if ($_comment[check]) {

			$check 	= 1;
			$rank	= nune_auth($com_user, $com_pass);

		} else {

			$check = 1;

		}

		if ($check > 0) {

	          $com_user		=    addslashes($com_user);
	          $com_email	=    addslashes($com_email);
			// Allow only certain tags in comments.
			$com_comment	=	strip_tags($com_comment, '<A>,<B>,<I>,<U>,<PRE>,<BLOCKQUOTE>,<P>,<BR>,<FONT>,<IMG>');
			$com_comment	=	addslashes($com_comment);

			$host		=	gethostbyaddr($host);

          	$insert = 
		"INSERT INTO $commtable VALUES('', '$pid', '$com_user', '$com_email', '$host', '$com_comment', '$time', '$rank')";

			$result = mysql_query($insert);

          	$cid = mysql_insert_id();

			$com_user      =    stripslashes($com_user);
          	$com_email     =    stripslashes($com_email);
          	$com_comment   =    stripslashes($com_comment);

			if ($_comment[auto_cookie]) {

				if (isset($$cookie_name[comments])) {

					# Cookie set... compare it to this one

					$cookie = serialize(array($com_user, $com_pass, $com_email));

					if ($$cookie_name[comments] != $test_array)
						setcookie($cookie_name[comments], $cookie, mktime(0,0,0,1,1,2010), "/", $www_cookie_path, 0);

				} else {

					$cookie = serialize(array($com_user, $com_pass, $com_email));
					setcookie($cookie_name[comments], $cookie, mktime(0,0,0,1,1,2010), "/", $www_cookie_path, 0);

				}

			}
			
			include($comment_confirm_path);

		} else if ($check = -1) {

			$error = 'Missing field.';
			include($error_path);

		} else {

			$error = 'You are not authorized to post comments.';
			include($error_path);

		}	

	}	
	
}

function nune_auth ($username, $password) {

	global $usertable;

     $query    = "SELECT * FROM $usertable WHERE username='$username' AND password=PASSWORD('$password')";
     $result   = mysql_query($query);

     $check    = mysql_num_rows($result);

     if ($check) {

          $row = mysql_fetch_array($result);

          return $row[access];

     } else {

          return 0;

     }

}

#
# Special Functions
#

function print_html ($pid,$title,$article,$author,$email,$date) {

	global 	$_comment, 
			$font, $comment, $commtable,
			$post_path, $sphinx;

	# get the amount of comments for this post

	$result 	= mysql_query("SELECT COUNT(*) AS total FROM $commtable WHERE assoc_post=$pid");
	$row		= mysql_fetch_array($result);
	$total	= $row[total];

	include($post_path);
	
}

function print_comment_input ($assoc_post) {

	global 	$_comment,	# from nune.conf
			$font,		# from nune.conf: misc
			$comment_date, # from nune.conf: tinkering
			$cookie_name, $$cookie_name[comments],	# the auto_cookie
			$comment_form_path;	# from nune.conf: path

	$host = getenv("REMOTE_ADDR");

	if ($$cookie_name[comments] && $_comment[auto_cookie]) {

		$values = unserialize(stripslashes($$cookie_name[comments]));

		list($cook_user, $cook_pass, $cook_email) = $values;

	}	

	include($comment_form_path);

}

function print_comments ($assoc_post) {

	global 	$commtable, $font, $custom_rank,
			$_comment,
			$comment_post_path;	# form nune.conf: path

	if ($custom_rank) {

		$rank	=	$custom_rank;

	} else {

		$rank[0]	=	'Anonymous';
		$rank[1]	=	'Member';
		$rank[2]	=	'Moderator';
		$rank[3]	=	'Administrator';

	}

	$query = "SELECT * FROM $commtable WHERE assoc_post=$assoc_post ORDER BY cid";
	$result = mysql_query($query);

	while ($row = mysql_fetch_array($result)) {
	
		$cid			=	$row["cid"];
		$com_user		=	stripslashes($row["username"]);
		$com_email	=	stripslashes($row["email"]);
		$com_host		=	$row["host"];
		$com_comment	=	stripslashes($row["comment"]);	
		$com_time		=	$row["date"];
		$com_rank		=	$rank[$row["post_rank"]];

		include($comment_post_path);

	}	

}

?>
