<?

#
# This the comment post template.
#
# Variable Definitions:
#
# $font		-- preferred font
# $cid		-- comment id number
# $com_user 	-- commenter's username
# $com_email	-- commenter's email
# $com_host	-- commenter's hostname (defined properly below)
# $com_comment -- commenter's comment
# $com_time	-- time of comment
# $com_rank	-- commenter's rank (defined properly below)
#

#
# If you want to show hosts, then define the string here.
#

if ($_comment[show_host])
	$com_host =    "($com_host)";

else
	$com_host =	'';

#
# If you want to show ranks, then define the string here.
#

if ($_comment[check])
	$com_rank =    "<BR><SMALL>($com_rank)</SMALL>";

else
	$com_rank =	'';

#
# The comment post layout...
#

$location_href = "/?comment=$assoc_post#cid:$cid";

$html = (" 

<A NAME=\"cid:$cid\"></A>
<TABLE BGCOLOR=\"#000000\" WIDTH=\"99%\" CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>
<TABLE BGCOLOR=\"#000000\" WIDTH=\"100%\" CELLPADDING=\"3\" CELLSPACING=\"1\" BORDER=\"0\"><TR><TD BGCOLOR=\"#7286B9\">
Posted by <A HREF=\"mailto:$com_email\">$com_user</A> $com_host @ $com_time [<A HREF=\"$location_href\">#$cid</A>]<BR>
</TD></TR>
<TR><TD BGCOLOR=\"#FFFFFF\">
$com_comment
</TD></TR></TABLE>
</TD></TR></TABLE>
<BR>

");

echo $html;

?>
