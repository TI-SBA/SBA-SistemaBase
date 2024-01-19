<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<TITLE>Alpha-Channels / Masks</TITLE>
<LINK TYPE="text/css" REL="stylesheet" HREF="../fpdf.css">
</HEAD>
<BODY ONLOAD="if(window.focus) window.focus()">
<H2>Alpha-Channels / Masks</H2>
<H4 CLASS="st">Informations</H4>
Author: Valentin Schmidt<BR>
License: Freeware<BR>
Version: 1.4<BR>
Requirements: FPDF 1.6<BR>
<BR>
<H4 CLASS="st">Description</H4>
This script allows to use images (PNGs or JPGs) with alpha-channels. The alpha-channel can be either supplied as separate 8-bit PNG ("mask"), or, for PNGs, also an internal alpha-channel can be used. For the latter, the GD 2.x extension is required.<BR>
<BR>
Specifying a separate mask image has several advantages:<BR>
- no GD required.<BR>
- Better quality (full 8-bit alpha channel, whereas GD internally only supports 7-bit alpha channels)<BR>
- much faster (extraction of embedded alpha-channel has to be done pixel-wise)<BR>
<BR>
<TT>function Image(<B>string</B> file, <B>float</B> x, <B>float</B> y [, <B>float</B> w [, <B>float</B> h [, <B>string</B> type [, <B>mixed</B> link [, <B>boolean</B> isMask [, <B>int</B> maskImg]]]]]])</TT><BR>
<BR>
Same parameters as for original Image()-method, with 2 additional (optional) parameters:<BR>
<TT><U>isMask</U></TT>: if specified and true, the image is used as mask for other images. In this case, the parameters x, y, w and h will be ignored and the mask image itself is not visible on the page.<BR>
<TT><U>maskImg</U></TT>: number of image resource (as returned by previously called Image() with isMask parameter set to true) that will be used as mask for this image.<BR>
<BR>
<TT>function ImagePngWithAlpha(<B>string</B> file, <B>float</B> x, <B>float</B> y [, <B>float</B> w [, <B>float</B> h [, <B>mixed</B> link]]])</TT><BR>
<BR>
Same parameters as for original Image()-method, but without a type parameter.
<BR>
<BR>
<H4 CLASS="st">Source</H4><TABLE WIDTH="100%" STYLE="color:#4040C0; border-style:ridge" BORDERCOLORLIGHT="#B0B0E0" BORDERCOLORDARK="#000000" BORDER="2" CELLPADDING=6 CELLSPACING=0 BGCOLOR="#F0F5FF"><TR><TD style="border-width:0px">
<NOBR>

<? show_source('fpdf_alpha.php'); ?>

</NOBR></TD></TR></TABLE>
<BR>
<H4 CLASS="st">Example</H4>Here's an example that demonstrates both specifying alpha channels as separate "mask" images and using PNG's with internal alpha channels.<BR>
<BR>
<TABLE WIDTH="100%" STYLE="color:#4040C0; border-style:ridge" BORDERCOLORLIGHT="#B0B0E0" BORDERCOLORDARK="#000000" BORDER="2" CELLPADDING=6 CELLSPACING=0 BGCOLOR="#F0F5FF"><TR><TD style="border-width:0px">
<NOBR>

<? show_source('ex.php'); ?>

</NOBR></TD></TR></TABLE>

<BR>
View the result <A HREF="ex.php" TARGET="_blank">here</A>.
<H4 CLASS="st">Download</H4><A HREF="fpdf_alpha_v1.4.zip">ZIP</A>
</BODY>
</HTML>
