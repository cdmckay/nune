<?

#
#	file: 	index.php
#	author: 	cameron "mercutio" mckay
#	purpose:	to authorize
#

require('config/nune.conf');

if ($mode == 'authorize') {

	mysql_connect($hostname, $username, $password) or die('Unable to connect to database');
	mysql_select_db("$dbName") or die('Unable to select database');

	$query 	= "SELECT * FROM $usertable WHERE username='$auth_username'";
	$result	= mysql_query($query);

	$check	= mysql_num_rows($result);

	if ($check) {

		$row		= mysql_fetch_array($result);
	
		$auth_password = crypt($auth_password, 'lI');
	
		if ($row[password] == $auth_password) {	

			$value = array($auth_username,$auth_password);
			$value = serialize($value);

			setcookie('cookie_auth',$value,time()+3600,"/",$www_site_address,0);
			$confirmed = 1;

		} else {

			$confirmed = 'Sorry, the passwords do not match.';

		}

	} else {

		$confirmed = 'Sorry, user does not exist.';

	}

} elseif ($cookie_auth && $cookie_auth != 'nothing') {

	$confirmed = 1;

}

?>	


<HTML>
<HEAD>
<TITLE> NUNE </TITLE>
</HEAD>

<BODY BGCOLOR="#FFFFFF" TEXT="#000000" VLINK="000099" ALINK="000099" LINK="000099">

<FONT FACE="Verdana">

<TABLE WIDTH="200" ALIGN="CENTER" BORDER="1" BGCOLOR="#003366"><TR>
<TD> <DIV ALIGN="CENTER"> <FONT COLOR="#FFFFFF"><B>NUNE</B></FONT> </DIV> </TD>
</TR></TABLE>

<BR>

<TABLE WIDTH="70%" ALIGN="CENTER" BORDER="1" BGCOLOR="#EEEEEE" CELLPADDING="4"><TR>
<TD>

Welcome to NUNE by Cameron McKay, a news script based off MyNews by Mike McMurray.<BR>
Login below to continue.  NUNE is licensed under the GPL.
<BR><BR>

<?

if ($confirmed == 1) {

	echo 'A cookie has been (hopefully) sent to your computer.<BR>';
	echo 'You are authorized, please <A HREF="newsadmin.php?special=admin">proceed</A>.';

} else {

	if ($confirmed && $confirmed != 1) {

		echo $confirmed;

	}

	echo '<FORM ACTION="index.php?mode=authorize" METHOD="post">';
	echo '<FONT FACE="Courier">';
	echo 'Username: <INPUT TYPE="text" NAME="auth_username" size="20"><BR>';
	echo 'Password: <INPUT TYPE="password" NAME="auth_password" size="20"><BR><BR>';
	echo '</FONT>';
	echo '<INPUT TYPE="submit" VALUE="submit"> <INPUT TYPE="reset" VALUE="clear"></FORM>';

	echo "<a href=\"http://$www_site_address\">Return to $www_site_name...</a>";

}

?>

</TD></TR></TABLE>

<BR>
<TABLE WIDTH="200" ALIGN="CENTER" BORDER="1" BGCOLOR="#003366"><TR>
<TD> <DIV ALIGN="CENTER"> <FONT COLOR="#FFFFFF"><B>Version 1.5a</B></FONT> </DIV> </TD>
</TR></TABLE>

</BODY>
</HTML>
