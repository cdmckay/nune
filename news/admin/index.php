<?

#
#	file: 	index.php
#	author: 	cameron "mercutio" mckay
#	purpose:	to authorize
#

require('config/nune.conf');

if ($mode == 'authorize') {

	mysql_connect($hostname, $username, $password) or die('Unable to connect to database.');
	mysql_select_db($database) or die('Unable to select database.');

	$query 	= "SELECT username FROM $usertable WHERE username='$auth_username' AND password=PASSWORD('$auth_password')";
	$result	= mysql_query($query);

	$check	= mysql_num_rows($result);

	if ($check) {

		$result = mysql_query("SELECT PASSWORD('$auth_password')");
		$crypt_password = mysql_result($result, 0);

		$value = array($auth_username,$auth_password);
		$value = serialize($value);

		setcookie($cookie_name[auth],$value,time()+10000,"/",$www_cookie_path,0);
		$confirmed = 1;

	} else {

		$confirmed = 'Sorry, user does not exist or invalid password.';

	}

} elseif ($$cookie_name[auth] && $$cookie_name[auth] != 'nothing') {

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

Welcome to NUNE News Script by Cameron McKay.<BR>
Login below to continue.  NUNE is licensed under a BSD-style license.
<BR><BR>

<?

if ($confirmed == 1) {

	echo "A cookie ($cookie_name[auth]) has been (hopefully) sent to your computer.<BR>";
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
<TD> <DIV ALIGN="CENTER"> <FONT COLOR="#FFFFFF"><B><? echo $version; ?></B></FONT> </DIV> </TD>
</TR></TABLE>

</BODY>
</HTML>
