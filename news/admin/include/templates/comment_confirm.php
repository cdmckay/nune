<?

#
# This is the template for the comment confirmation page.
#
# Here are the variable definitions that you can use:
#
# $font		-- your preferred fonts (from nune.conf)
# $cid 		-- comment id number
# $com_user	-- contents of the username field
# $com_email	-- contents of the email field
# $time		-- the date and time it was posted
# $com_comment -- contents of the comment field
# $rank		-- poster's rank (if $comm_check is on)
# $pid		-- post id number
# $host		-- poster's host
#

?>

<HTML>

<HEAD>
<META HTTP-EQUIV="Refresh" CONTENT="2; URL=?comment=<? echo $pid; ?>">
<TITLE>NUNE Comment Confirmation</TITLE>
<STYLE>
<!--

	BODY { font-family: <? echo $font; ?>; }
	P { font-family: <? echo $font; ?>; }

//-->
</STYLE>
</HEAD>

<BODY>

<P>
<B>Comment id <? echo $cid; ?></B> by <B><? echo $com_user; ?> (<? echo $host; ?>)</B> has been added.<BR>
Thank you for your comment.
</P>

</BODY>

</HTML>
