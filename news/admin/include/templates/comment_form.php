<?

#
# This is the comment form template.
#
# Variable Definitions:
#
# $font			-- prefered font
# $cook_user 		-- username from cookie
# $cook_pass		-- password from cookie
# $cook_email		-- email from cookie
# $_comment[auth]	-- 1 if authorization is on, 0 if it's not
# $_comment[check]	-- 1 if checking is on, 0 if it's not
# $host			-- comment posters host address
# $assoc_post		-- the post id number which the comment is associated with 
# $comment_date	-- the comments date and time
#

#
# NOTE: Do not change any of the NAME field, or else the comments script will
# cease to function.
#

#
# Browser Determining Code (from Lingo, my now defunct project)
#

//
// Name: html_browser()
//
// Purpose:
// Get the browser type.
//
// Arguments:
// None.
//
// Returns: browser name.
//
 
function html_browser()
{
     // Need HTTP_USER_AGENT to determine browser.
     global $HTTP_USER_AGENT;
 
     // Set the $BROWSER variable.
     // If Navigator then...
     if (preg_match("/Nav/i", $HTTP_USER_AGENT))
          $BROWSER = 'Navigator';
 
     // If Lynx...
     else if (preg_match("/Lynx/i", $HTTP_USER_AGENT))
          $BROWSER = 'Lynx';
 
     // If Explorer...                       
     else if (preg_match("/IE/i", $HTTP_USER_AGENT))
          $BROWSER = 'Explorer';
 
     // If w3m...
     else if (preg_match("/w3m/i", $HTTP_USER_AGENT))
          $BROWSER = 'w3m';

	// If KDE Konqueror...
	else if (preg_match("/konqueror/i", $HTTP_USER_AGENT))
		$BROWSER = 'Konqueror';

	// Else unknown...
     else
          $BROWSER = 'Unknown';
 
     return $BROWSER;
}                           	

# Apply the browser determining code.
$BROWSER = html_browser();

echo "\n\n<!-- Browser Identified as: $BROWSER -->\n\n";

if (preg_match("/explorer/i", $BROWSER)) {
	$length = '60';

} else if (preg_match("/navigator/i", $BROWSER)) {
	$length = '39';

} else if (preg_match("/konqueror/i", $BROWSER)) {
	$length = '70'; 

} else {
	$length = '50';

}

?>

<TABLE BGCOLOR="#000000" WIDTH="99%" CELLPADDING="1" CELLSPACING="0" BORDER="0"><TR><TD>
<TABLE BGCOLOR="#EEEEEE" WIDTH="100%" CELLPADDING="3" CELLSPACING="0" BORDER="0"><TR><TD>

<FORM ACTION="?comment=confirm" METHOD="POST">

<TABLE BORDER="0" ALIGN="CENTER"><TR><TD>

<B>Allowed Tags</B>
<HR>
<pre>
&lt;A HREF="/"&gt;Hyperlink&lt/A&gt;                   <A HREF="/">Hyperlink</A>
&lt;B&gt;Bold&lt;/B&gt;                                 <B>Bold</B>
&lt;I&gt;Italics&lt;/I&gt;                              <I>Italics</I> 
&lt;U&gt;Underline&lt;/U&gt;                            <U>Underline</U>
&lt;PRE&gt;Preformat&lt;/PRE&gt;                        Preformat
&lt;BLOCKQUOTE&gt;Blockquote&lt;/BLOCKQUOTE&gt;         Indentation
&lt;P&gt;Paragraph&lt;/P&gt;                            Paragraph
&lt;BR&gt;                                        Line Break
&lt;FONT FACE,SIZE ..&gt;&lt;/FONT&gt;                  Font Properties
&lt;IMG SRC="i/banana_3.png"&gt;                  Image Stuff
</pre>

<TABLE BORDER="0" ALIGN="LEFT">
<TR><TD>Username:</TD>
<TD><INPUT TYPE="text" NAME="com_user" VALUE="<? echo $GLOBALS[cookie][username]; ?>" SIZE="30" MAXLENGTH="20"></TD>
</TR>

<?

#
# This checks to see if you have comment authorization or comment checking enabled to
# determine whether or not a password field is necessary.
#

if ($_comment[auth] || $_comment[check]) {

echo "<TR><TD>Password:</TD>\n";
echo "<TD><INPUT TYPE=\"password\" NAME=\"com_pass\" VALUE=\"$cook_pass\" SIZE=\"30\" MAXLENGTH=\"50\"></TD>\n";
echo '</TR>'  . "\n";

}

?>

<TR><TD>E-Mail Address:</TD>
<TD><INPUT TYPE="text" NAME="com_email" VALUE="<? echo $GLOBALS[cookie][email]; ?>" SIZE="30" MAXLENGTH="50"></TD>
</TR>
</TABLE>

</TD></TR><TR><TD>

<TEXTAREA NAME="com_comment" COLS="<? echo $length; ?>" ROWS="8" WRAP="VIRTUAL"></TEXTAREA>

</TD></TR><TR><TD ALIGN="RIGHT">
<input type="Submit" value="Post Comment">
</TD></TR></TABLE>

<? # Leave these alone... they need be sent. ?>

<INPUT TYPE="hidden" NAME="pid"  VALUE="<? echo $assoc_post;   ?>">
<INPUT TYPE="hidden" NAME="host" VALUE="<? echo $host;         ?>">
<INPUT TYPE="hidden" NAME="time" VALUE="<? echo $comment_date; ?>">

</FORM>

</TD></TR></TABLE>
</TD></TR></TABLE>
