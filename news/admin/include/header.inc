<?

#
# this is the authorization routine... it checks the username and password against
# the one stored in the mySQL database...
#

require ('config/nune.conf');

if ($cookie_auth) {

	$value	= unserialize(stripslashes($cookie_auth));

	list($auth_username,$auth_password) = $value;

     mysql_connect($hostname, $username, $password) or die('Unable to connect to database');
     mysql_select_db("$dbName") or die('Unable to select database');

     $query    = "SELECT * FROM $usertable WHERE username='$auth_username'";
     $result   = mysql_query($query);

     $check    = mysql_num_rows($result);

     if ($check) {

          $row      = mysql_fetch_array($result);

          if ($row[password] == $auth_password) {

               $confirmed = 1;

			$nune_username	= $row[username];
			$nune_password	= $auth_password;
			$nune_email	= $row[email];
			$nune_access	= $row[access];

          } else {

			$confirmed = 0;

          }

	} else {

		$confirmed = 0;

	}

} else {

	$confirmed = 0;

}

if (!$confirmed) {

	header("Location: http://$www_site_address/news/admin");
     exit;

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
