<?

#
# this is the authorization routine... it checks the username and password against
# the one stored in the mySQL database...
#

require ('config/nune.conf');

if ($$cookie_name[auth]) {

	$value	= unserialize(stripslashes($$cookie_name[auth]));

	list($auth_username,$auth_password) = $value;

     mysql_connect($hostname, $username, $password) or die('Unable to connect to database.');
     mysql_select_db($database) or die('Unable to select database.');

     $query    = "SELECT * FROM $usertable WHERE username='$auth_username' AND password=PASSWORD('$auth_password')";
	$result   = mysql_query($query);

	$check    = mysql_num_rows($result);

     if ($check) {

		$confirmed = 1;

		$row = mysql_fetch_array($result);

		$nune_username	= $row[username];
		$nune_password	= $auth_password;
		$nune_email	= $row[email];
		$nune_access	= $row[access];

	} else {

		$confirmed = 0;
		$errorlevel = '1';

	}

} else {

	$confirmed = 0;
	$errorlevel = '2';

}

if (!$confirmed) {

	#echo $errorlevel;
	header("Location: http://$www_admin_path");
     exit;

}

?>

<HTML>
<HEAD>
<TITLE> NUNE </TITLE>
<STYLE>
<!--

	TD { font-family: Verdana, Arial, Helvetica; font-size: 12pt; }
	INPUT { font-family: Verdana, Arial, Helvetica; }

-->
</STYLE> 
</HEAD>

<BODY BGCOLOR="#FFFFFF" TEXT="#000000" VLINK="000099" ALINK="000099" LINK="000099">

<FONT FACE="Verdana">

<TABLE WIDTH="200" ALIGN="CENTER" BORDER="1" BGCOLOR="#003366"><TR>
<TD> <DIV ALIGN="CENTER"> <FONT COLOR="#FFFFFF"><B>NUNE</B></FONT> </DIV> </TD>
</TR></TABLE>

<BR>

<TABLE WIDTH="70%" ALIGN="CENTER" BORDER="1" BGCOLOR="#EEEEEE" CELLPADDING="4"><TR>
<TD>
