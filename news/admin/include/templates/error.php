<?

#
# This is the error template file.
#
# Variable Definitions:
#
# $font	-- preferred font
# $error	-- error message
#

?>

<HTML>
<HEAD>
<TITLE>NUNE Error Message</TITLE>
<STYLE>
<!--

     BODY { font-family: <? echo $font; ?>; }
     P { font-family: <? echo $font; ?>; }

//-->
</STYLE>
</HEAD>

<BODY>

<H3>Error...</H3>

<P>
Error: <? echo $error; ?><BR>
</P>

</BODY>

</HTML>
