<?

#
#    name:     nune-lib.php
#    author:   cameron 'mercutio' mckay
#    purpose:  the functions which NUNE runs on (the "core" of the script if you will)
#
#    version:  1.5
#

#
# The nune_list function is used for displaying lists of items, used when edit_list, list and delete_list
# POST vars are given
#

function nune_list ($type) {

	global 	$insert,$number,$i,			# from newsadmin	
			$nune_access,$nune_username,	# from header
			$font,					# from nune.conf
			$when;					# from POSt

	if ($type == 'news') {

		while ($i < $number) {

     		$value = get_values($i);

		     print_html($value[artnr],$value[title],$value[previewtext],$value[author],$value[email],$value[date]);

		     $i++;

		}

	} elseif ($type == 'edit') {

		while ($i < $number) {

		     $value = get_values($i);		# get values like artnr, etc.

			if ($nune_access > 1 || ($nune_access == 1 && $author == $nune_username)) {

				echo "<a href=\"newsadmin.php?form=edit&item=$value[artnr]\">";
                    echo "Edit article<b> $value[artnr]</b></a> - $value[date] - $value[title]<br>";

			} else {

				echo "Edit article<b> $value[artnr]</b> - $value[date] - $value[title]<br>";

			}

			$i++;

		}

		print '<BR><A HREF="newsadmin.php?special=admin">Back to options...</A>';

	} elseif ($type == 'delete') {

		while ($i < $number) {

               $value = get_values($i);      # get values like artnr, etc.

               if ($nune_access > 1 || ($nune_access == 1 && $author == $nune_username)) {

                    echo "<a href=\"newsadmin.php?confirm=delete&item=$value[artnr]\">";
                    echo "Delete article<b> $value[artnr]</b></a> - $value[date] - $value[title]<br>";

               } else {

                    echo "Delete article<b> $value[artnr]</b> - $value[date] - $value[title]<br>";

               }

               $i++;

          }

          print '<BR><A HREF="newsadmin.php?special=admin">Back to options...</A>';

	} elseif ($type == 'quick') {

		while ($i < $number) {

			$value = get_values($i);      # get values like artnr, etc.

			echo "<FONT FACE=\"$font\"><A HREF=\"/#$value[artnr]\">$value[date] - $value[title]</A><br></FONT>";		
		
			$i++;

		}

	} elseif ($type == 'archive') {

		while ($i < $number) {

     		$value = get_values($i);

     #
     # if archive mode is enabled than we record the artnr into arrays
     # and skip print the html...
     #

		     preg_match("/([A-Z]+)\s\d{2}\s(\d{4})/i", $value[date], $month);
		     $arraymonth = "$month[1]-$month[2]";
		     $archive[$arraymonth][] = $i;

		     $i++;
		}

		if (!$when) {

     		echo "<SMALL><B><FONT FACE=\"$font\">";

     		foreach ($archive as $key => $value) {

		          $monthtext = "<A HREF=\"?list=archive&when=$key\">$key</A><BR>";
				echo $monthtext;

		     }

	 	    echo "</FONT></B></SMALL>";

		} else {

		     $archive_head = "<SMALL><B><FONT FACE=\"$font\">$when</FONT></B></SMALL><BR><BR>";
		     echo $archive_head;

		     $array_count = count($archive[$when])-1;

		     for ($j = 0; $j <= $array_count; $j++) {

		          $value = get_values($archive[$when][$j]);
		          print_html($value[$artnr],$value[title],$value[previewtext],$value[author],$valie[email],$value[date]);

	     	}

		}
	
	}

}

function nune_form ($type) {

	global	$nune_username,$nune_email,$item,$newstable,$nicedate;

	if ($type == 'add') {

		echo '<H3><B>Add a new article</B></H3>';
		echo '<form action="newsadmin.php?confirm=add" method="post">';
		echo 'Title:<br>';
		echo '<input type="text" name="title" size="40"><BR><BR>';
		echo "Article Text:<BR>";
		echo '<TEXTAREA NAME="previewtext" COLS=60 ROWS=7 WRAP=PHYSICAL></TEXTAREA><BR><BR>';
		echo 'Author:<BR>';
		echo "<B>$nune_username</B><BR><BR>";
		echo "<INPUT TYPE=\"hidden\" NAME=\"author\" VALUE=\"$nune_username\">";
		echo "E-Mail Address:<br>";
		echo "<input type=\"text\" name=\"email\" value=\"$nune_email\" size=\"40\"><BR><BR>";
		echo '<INPUT TYPE="submit" VALUE="submit"> <INPUT TYPE="reset" VALUE="clear">';
		echo "<br><input type=\"hidden\" name=\"date\" value=\"$nicedate\">";
		echo '</form>';
		echo "Today is $nicedate<BR><BR>";
		echo '<a href="newsadmin.php?list=news">View a list of all items</a>';

	} elseif ($type == 'edit') {

		$query = mysql_query("SELECT * FROM $newstable WHERE artnr=$item") or die(mysql_error());

		while ($row  =  mysql_fetch_array($query)) {

			$artnr		=    $row["artnr"];
			$title		=    stripslashes($row["title"]);
			$author		=    stripslashes($row["author"]);
			$previewtext	=    stripslashes($row["previewtext"]);
			$email		=    stripslashes($row["email"]);
			$date		=    $row["date"];

		}

		#
		# security check
		#

		if ($author != $nune_username && $nune_access < 2) {

			echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=newsadmin.php?special=admin\">";
			exit;

		}

          echo '<H3><B>Edit an existing article</B></H3>';
          echo "<form action=\"newsadmin.php?confirm=edit&item=$item\" method=\"post\">";
          echo 'Title:<br>';
		echo "<input type=\"text\" name=\"title\" size=\"40\" value=\"$title\">";
          echo "Article Text:<BR>";
          echo "<TEXTAREA NAME=\"previewtext\" COLS=60 ROWS=7 WRAP=PHYSICAL>$previewtext</TEXTAREA><BR><BR>";
          echo 'Author:<BR>';
          echo "<B>$author</B><BR><BR>";
          echo "<INPUT TYPE=\"hidden\" NAME=\"author\" VALUE=\"$author\">";
          echo "E-Mail Address:<br>";
          echo "<input type=\"text\" name=\"email\" value=\"$email\" size=\"40\"><BR><BR>";
          echo '<INPUT TYPE="submit" VALUE="submit"> <INPUT TYPE="reset" VALUE="clear">';
          echo "<br><input type=\"hidden\" name=\"date\" value=\"$nicedate\">";
          echo '</form>';
          echo "Article's Date was $date<BR><BR>";
          echo '<a href="newsadmin.php?list=news">View a list of all items</a>';

	}

}
	
function nune_confirm ($type) {

	global	$title, $previewtext, $author, $email, $date, $item,	# from POST
			$newstable,$font,								# from nune.conf
			$insert,$number,$i;           # from newsadmin

	if ($type == 'add') {

		$title         =    addslashes($title);
		$previewtext   =    addslashes($previewtext);
		$author        =    addslashes($author);
		$email         =    addslashes($email);

		$insert = "insert into $newstable values('$artnr','$title','$previewtext','$htmltext','$author','$email','$date')";
		$result = MYSQL_QUERY($insert);

		$query = "SELECT * FROM $newstable WHERE(artnr=$artnr)";
		$insert = MYSQL_QUERY($query);

		$artnr = mysql_insert_id();

		$title		= stripslashes($title);
		$previewtext	= stripslashes($previewtext);

		echo "<FONT FACE=\"$font\">";
		echo "<B>Article $artnr: $title</B> by <B>$author ($email)</B> has been added.<BR><BR>";
		echo "Article Text:<BR>$previewtext<BR>";
		echo '</FONT>';

		$i++;

		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=newsadmin.php?special=admin\">";

	} elseif ($type == 'edit') {

		$title         =    addslashes($title);
		$previewtext   =    addslashes($previewtext);
		$author        =    addslashes($author);
		$email         =    addslashes($email);

		$artnr = mysql_result($insert,$i,"artnr");

		$insert = "replace into $newstable values('$item','$title','$previewtext','$htmltext','$author','$email','$date')";
		$result = MYSQL_QUERY($insert);

		$query = "REPLACE * FROM $newstable WHERE artnr=\"$id\"";
		$insert = MYSQL_QUERY($query);

		$title         = stripslashes($title);
          $previewtext   = stripslashes($previewtext);

		echo "<FONT FACE=\"$font\">";
          echo "<B>Article $artnr: $title</B> by <B>$author ($email)</B> has been updated.<BR><BR>";
          echo "Article Text:<BR>$previewtext<BR>";
          echo '</FONT>';

          $i++;

          echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=newsadmin.php?special=admin\">";

	} elseif ($type == 'delete') {

		$artnr = mysql_result($insert,$i,"artnr");
		$result = MYSQL_QUERY($insert);

		$query = "DELETE FROM $newstable WHERE(artnr=$item)";
		$insert = MYSQL_QUERY($query);

		echo "<FONT FACE=\"$font\">";
          echo "<B>Article $artnr</B> has been deleted.<BR>";
          echo '</FONT>';

		print "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"1; URL=newsadmin.php?special=admin\">";

	}

}		
	
function nune_special ($type) {

	global	$nune_access,$nune_username,
			$www_site_address,$www_site_name,
			$head;

	if ($type == 'admin') {

		echo "What do you want to do?<br><br>";

		echo "<a href=\"newsadmin.php?form=add\">Add a new article</a><br>";
		echo '<BR>';
		echo "<a href=\"newsadmin.php?list=edit\">Edit a recent article</a><br>";
		echo "<a href=\"newsadmin.php?list=edit&type=archive\">Edit an archived article</a><br>";
		echo '<BR>';
		echo "<a href=\"newsadmin.php?list=delete\">Delete a recent article</a><br>";
		echo "<a href=\"newsadmin.php?list=delete&type=archive\">Delete an archived article</a><br>";
		echo '<BR>';

		if ($nune_access == 3) {

     		echo '<A HREF="useradmin.php"> Add, edit or delete users</A><BR>';
     		echo "<BR>\n";

		}

		echo "<a href=\"newsadmin.php?list=news\">View a list of all items</A><br>";
		echo "<BR>\n";
		echo "<a href=\"newsadmin.php?special=logout\">Logout of NUNE...</A><br>";
		echo "<BR>\n";
		echo "<a href=\"http://$www_site_address\">Return to $www_site_name...</a>";
	
	} elseif ($type == 'logout') {

		#
		# prepare yourself for the most advanced code ever seen by a human being
		#

		setcookie('cookie_auth','nothing',time()+3600,"/",$www_site_address,0);

		include("$head");

		echo 'Cookie should be removed.<BR>';
		echo "You are logged out, $nune_username.";

		#
		# make the page wait for 2 seconds and then reload
		#

		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"2; URL=index.php\">";

	}

}
	

#
# Special Functions
#

function print_html ($artnr,$title,$previewtext,$author,$email,$date) {

	#
	# this is how you want your news text to look
	#

     $fullhtml = ("

<A NAME=\"$artnr\"></A>
<SMALL><FONT FACE=\"Verdana\">
<B><FONT COLOR=\"#003366\">$title</FONT></B>
$date <a href=\"mailto:$email\">$author</a>
<FONT COLOR=\"#000000\"><BR>
$previewtext
</FONT>
<BR><BR><BR>
</FONT></SMALL>

     ");

     print "$fullhtml";

}

function get_values ($i) {

     global $insert;

     $value[artnr]        	=    mysql_result($insert,$i,"artnr");
     $value[title]        	=    stripslashes(mysql_result($insert,$i,"title"));        # stripslashes for obvious reasons
     $value[previewtext]  	=    stripslashes(mysql_result($insert,$i,"previewtext"));  # ...
     $value[author]       	=    stripslashes(mysql_result($insert,$i,"author"));       # ...
     $value[email]        	=    stripslashes(mysql_result($insert,$i,"email"));        #    ..done.
     $value[date]         	=    mysql_result($insert,$i,"date");

	return $value;

}

?>
