<?

#
# This is the normal post template.
#
# Variable Definitions:
#
# $font			-- preferred font
# $_comment[enable]	-- 1 if comments are enabled, 0 if they're not
# $comment		-- this variable is defined only if you are viewing a comments page
# $pid			-- post id number
# $title			-- article title
# $date			-- article date
# $email			-- posters email
# $author			-- poster
# $article		-- article text
# $comment_str 	-- the 'Read Comments' string (defined properly below)
#

// Check to see if it has new comments.
if ($sphinx[$pid] != $total)
	$new_status = " <FONT COLOR=\"RED\"><B>new</B></FONT>";

else
	$new_status = '';

if($_comment[enable] && !isset($comment))
	$comment_str = "<A HREF=\"?comment=$pid\">Read Comments ($total)</A>$new_status";

else
	$comment_str = 'Reading Comments...';

$html = ("

<A NAME=\"$pid\"></A>
<TABLE BGCOLOR=\"#000000\" WIDTH=\"99%\" CELLPADDING=\"1\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>
<TABLE BGCOLOR=\"#FFCD36\" WIDTH=\"100%\" CELLPADDING=\"3\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>
<B>$title (<A HREF=\"mailto:$email\">$author</A>)</B><BR>
Posted on $date
</TD></TR></TABLE>
</TD></TR></TABLE>
<BR>
<TABLE BGCOLOR=\"#000000\" WIDTH=\"99%\" CELLPADDING=\"1\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>
<TABLE BGCOLOR=\"#EEEEEE\" WIDTH=\"100%\" CELLPADDING=\"3\" CELLSPACING=\"0\" BORDER=\"0\"><TR><TD>
$article<BR><BR>
<B>$comment_str </B>
</TD></TR></TABLE>
</TD></TR></TABLE>
<BR>

");

print $html;

?>
