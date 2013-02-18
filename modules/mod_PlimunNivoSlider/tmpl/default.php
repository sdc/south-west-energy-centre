<?php 

/**
* @package   Plimun Nivo Slider
* @copyright Copyright (C) 2009 - 2010 Open Source Matters. All rights reserved.
* @license   http://www.gnu.org/licenses/lgpl.html GNU/LGPL, see LICENSE.php
* Contact to : info@plimun.com, plimun.com
**/



defined('_JEXEC') or die('Restricted access'); 

ini_set('display_errors',0);
$path=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$doc =& JFactory::getDocument();
$show_jquery=$params->get('show_jquery');
$load=$params->get('load');
$jver=$params->get('jver');





$doc->addStyleSheet ( 'modules/mod_PlimunNivoSlider/css/nivo-slider.css' );

$doc->addStyleSheet ( 'modules/mod_PlimunNivoSlider/themes/default/default.css' );


if($show_jquery=="yes" && $load=="onload" && $jver=="1.6.1")

{

$doc->addScript("modules/mod_PlimunNivoSlider/js/jquery-1.6.1.min.js");

}

else if ($show_jquery=="yes" && $load=="onload" && $jver!="1.6.1")
{
$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/".$jver."/jquery.min.js");
}


$uri 		=& JFactory::getURI();



$url= $uri->root();

$moduleid=$module->id;
$moduleclass_sfx	= 	$params->get( 'moduleclass_sfx');

$slidewidth 			= 	$params->get( 'slidewidth');

$slideheight		= 	$params->get( 'slideheight');

$imageeffect	= 	$params->get( "menu_style");

$navigation		= 	$params->get( 'navigation');
$manual		= 	$params->get( 'manual');
$timeinterval		= 	$params->get( 'timeinterval');

$velocity		= 	$params->get( 'velocity');
$linktarget		= 	$params->get( 'linktarget');
$linkedtitle		= 	$params->get( 'linkedtitle');
$border		= 	$params->get( 'border');
$bordercolor		= 	$params->get( 'bordercolor');
$borderrounded		= 	$params->get( 'borderrounded');
$shadow		= 	$params->get( 'shadow');







$arrows=$params->get('arrows');

$hidetools=$params->get('hidetools');



$navigation=$params->get('navigation');
$backgroundcolor		= 	$params->get( 'backgroundcolor');
$align		= 	$params->get( 'align');
$dotspos=$params->get('dotspos');
$dotsstyle=$params->get('dotsstyle');

//$arrowspos=$params->get('arrowspos');
$arrowsstyle=$params->get('arrowsstyle');
$labelcolor		= 	$params->get( 'labelcolor');

$desccolor		= 	$params->get( 'desccolor');

$labelsize		= 	$params->get( 'labelsize');

$descsize		= 	$params->get( 'descsize');

$titlefont		= 	$params->get( 'titlefont');

$descfont		= 	$params->get( 'descfont');
$captionbg		= 	$params->get( 'captionbg');
$captionob		= 	$params->get( 'captionob');
$captionpos		= 	$params->get( 'captionpos');
$captionwidth	= 	$params->get( 'captionwidth');
$captionh	= 	$params->get( 'captionh');
$captionh2	= 	$params->get( 'captionh2');
$hspace	= 	$params->get( 'hspace');
$vspace	= 	$params->get( 'vspace');
$cfromv	= 	$params->get( 'cfromv');
$cfromh	= 	$params->get( 'cfromh');
$crounded	= 	$params->get( 'crounded');


if($descfont=="arial")

{

$descfont='Arial, Helvetica, sans-serif';

}

if($titlefont=="arial")

{

$titlefont='Arial, Helvetica, sans-serif';

}

if($descfont=="tnr")

{

$descfont='"Times New Roman", Times, serif';

}

if($titlefont=="tnr")

{

$titlefont='"Times New Roman", Times, serif';

}

if($descfont=="cn")

{

$descfont='"Courier New", Courier, monospace';

}

if($titlefont=="cn")

{

$titlefont='"Courier New", Courier, monospace';

}

if($descfont=="georgia")

{

$descfont='Georgia, "Times New Roman", Times, serif';

}

if($titlefont=="georgia")

{

$titlefont='Georgia, "Times New Roman", Times, serif';

}

if($descfont=="verdana")

{

$descfont='Verdana, Arial, Helvetica, sans-serif';

}

if($descfont=="verdana")

{

$titlefont='Verdana, Arial, Helvetica, sans-serif';

}




if($manual=="yes")

{

$manual="true";

}

else

{$manual="false";}


if($navigation=="yes")

{

$navigation="true";

}

else

{$navigation="false";}




if($arrows=="yes")

{

$arrows="true";

}

else

{$arrows="false";}





if($hidetools=="yes")

{

$hidetools="true";

}

else

{$hidetools="false";}




$img1=$params->get('img1');

$img2=$params->get('img2');

$img3=$params->get('img3');

$img4=$params->get('img4');

$img5=$params->get('img5');

$img6=$params->get('img6');

$img7=$params->get('img7');

$img8=$params->get('img8');

$img9=$params->get('img9');

$img10=$params->get('img10');
$img11=$params->get('img11');

$img12=$params->get('img12');

$img13=$params->get('img13');

$img14=$params->get('img14');

$img15=$params->get('img15');

$img16=$params->get('img16');

$img17=$params->get('img17');

$img18=$params->get('img18');

$img19=$params->get('img19');

$img20=$params->get('img20');

$label1=$params->get('label1');

$label2=$params->get('label2');

$label3=$params->get( 'label3');

$label4=$params->get('label4');

$label5=$params->get('label5');

$label6=$params->get( 'label6');

$label7=$params->get('label7');

$label8=$params->get('label8');

$label9=$params->get( 'label9');

$label10=$params->get('label10');
$label11=$params->get('label11');

$label12=$params->get('label12');

$label13=$params->get( 'label13');

$label14=$params->get('label14');

$label15=$params->get('label15');

$label16=$params->get( 'label16');

$label17=$params->get('label17');

$label18=$params->get('label18');

$label19=$params->get( 'label19');

$label20=$params->get('label20');

$desc1=$params->get('desc1');

$desc2=$params->get('desc2');

$desc3=$params->get('desc3');

$desc4=$params->get('desc4');

$desc5=$params->get('desc5');

$desc6=$params->get('desc6');

$desc7=$params->get('desc7');

$desc8=$params->get('desc8');

$desc9=$params->get('desc9');

$desc10=$params->get('desc10');
$desc11=$params->get('desc11');

$desc12=$params->get('desc12');

$desc13=$params->get('desc13');

$desc14=$params->get('desc14');

$desc15=$params->get('desc15');

$desc16=$params->get('desc16');

$desc17=$params->get('desc17');

$desc18=$params->get('desc18');

$desc19=$params->get('desc19');

$desc20=$params->get('desc20');

$link1=$params->get( 'link1');

$link2=$params->get( 'link2');

$link3=$params->get( 'link3');

$link4=$params->get( 'link4');

$link5=$params->get( 'link5');

$link6=$params->get( 'link6');

$link7=$params->get( 'link7');

$link8=$params->get( 'link8');

$link9=$params->get( 'link9');

$link10=$params->get( 'link10');
$link11=$params->get( 'link11');

$link12=$params->get( 'link12');

$link13=$params->get( 'link13');

$link14=$params->get( 'link14');

$link15=$params->get( 'link15');

$link16=$params->get( 'link16');

$link17=$params->get( 'link17');

$link18=$params->get( 'link18');

$link19=$params->get( 'link19');

$link20=$params->get( 'link20');



/***********************************LABELS **********************************************/
$img=array($img1,$img2,$img3,$img4,$img5,$img6,$img7,$img8,$img9,$img10,$img11,$img12,$img13,$img14,$img15,$img16,$img17,$img18,$img19,$img20);
$labels=array($label1,$label2,$label3,$label4,$label5,$label6,$label7,$label8,$label9,$label10,$label11,$label12,$label13,$label14,$label15,$label16,$label17,$label18,$label19,$label20);

$descs=array($desc1,$desc2,$desc3,$desc4,$desc5,$desc6,$desc7,$desc8,$desc9,$desc10,$desc11,$desc12,$desc13,$desc14,$desc15,$desc16,$desc17,$desc18,$desc19,$desc20);
$links=array($link1,$link2,$link3,$link4,$link5,$link6,$link7,$link8,$link9,$link10,$link11,$link12,$link13,$link14,$link15,$link16,$link17,$link18,$link19,$link20);


$javascript="
var pns = jQuery.noConflict();
     pns(window).load(function() {

        pns('#slider".$moduleid."').nivoSlider({
		effect: '".$imageeffect."', // Specify sets like: 'fold,fade,sliceDown'
        slices: 15, // For slice animations
        boxCols: 8, // For box animations
        boxRows: 4, // For box animations
        animSpeed: ".$velocity.", // Slide transition speed
        pauseTime: ".$timeinterval.", // How long each slide will show
        startSlide: 0, // Set starting Slide (0 index)
        directionNav: ".$arrows.", // Next & Prev navigation
        directionNavHide: ".$hidetools.", // Only show on hover
        controlNav: ".$navigation.", // 1,2,3... navigation
        controlNavThumbs: false, // Use thumbnails for Control Nav
        controlNavThumbsFromRel: false, // Use image rel for thumbs
        controlNavThumbsSearch: '.jpg', // Replace this with...
        controlNavThumbsReplace: '_thumb.jpg', // ...this in thumb Image src
        keyboardNav: true, // Use left & right arrows
        pauseOnHover: true, // Stop animation while hovering
        manualAdvance: ".$manual.", // Force manual transitions
        captionOpacity: ".$captionob.", // Universal caption opacity
        prevText: 'Prev', // Prev directionNav text
        nextText: 'Next', // Next directionNav text
        beforeChange: function(){}, // Triggers before a slide transition
        afterChange: function(){}, // Triggers after a slide transition
        slideshowEnd: function(){}, // Triggers after all slides have been shown
        lastSlide: function(){}, // Triggers when last slide is shown
        afterLoad: function(){} // Triggers when slider has loaded
		
		
		});

    });
";


if($load=="onload")

{

$doc->addScriptDeclaration($javascript);

}



$count=0;
for($i=0;$i<20;$i++)
{
if($descs[$i]!="")
{
$descs[$i]='<p>'.$descs[$i].'</p>';
}

if($labels[$i]=="")

{$labels[$i]='';}

else
{
if($linkedtitle=="no" || $links[$i]=="")
{
$labels[$i]='<div id="label'.$i.$moduleid.'" class="nivo-html-caption">

                <h5>'.$labels[$i].'</h5>'.$descs[$i].'

            </div>
';
}
if($linkedtitle=="yes" && $links[$i]!="")
{
$labels[$i]='<div id="label'.$i.$moduleid.'" class="nivo-html-caption">

                <h5><a href="'.$links[$i].'" target="'.$linktarget.'">'.$labels[$i].'</a></h5>'.$descs[$i].'

            </div>
';
}

			}


if($img[$i]=="")

{

$image[$i]="";

}	

else

{

$image[$i]='<img src="'.$img[$i].'" alt="" width="'.$slidewidth.'px" height="'.$slideheight.'px" />';

if($labels[$i]!="")
{
$image[$i]='<img src="'.$img[$i].'" alt="" title="#label'.$i.$moduleid.'" width="'.$slidewidth.'px" height="'.$slideheight.'px" />';
}

if($links[$i]!="")
{
$image[$i]='<a href="'.$links[$i].'" target="'.$linktarget.'">'.$image[$i].'</a>';

}
$count++;

}

}//end for
 ?>


<style type="text/css">

caption, th, td {
	text-align:left;
	font-weight:normal;
}

.theme-default<?php echo $moduleid;?> .nivoSlider {
background-color:<?php echo $backgroundcolor;?>;
border:<?php echo $bordercolor;?> solid <?php echo $border;?>px;
<?php if($borderrounded=="yes"){ ?>
    -moz-border-radius: 8px 8px 8px 8px;
    -webkit-border-radius: 8px 8px 8px 8px;
    border-radius: 8px 8px 8px 8px;
	<?php }?>
	<?php if($shadow=="yes"){ ?>
    -webkit-box-shadow: 0px 1px 5px 0px #4a4a4a;
    -moz-box-shadow: 0px 1px 5px 0px #4a4a4a;
    box-shadow: 0px 1px 5px 0px #4a4a4a;
		<?php }?>
}

.theme-default<?php echo $moduleid;?> #slider<?php echo $moduleid;?> {
    width:<?php echo $slidewidth;?>px; /* Make sure your images are the same size */
    height:<?php echo $slideheight;?>px; /* Make sure your images are the same size */
	<?php
	if($navigation=="true" && $dotspos=="bottomm")
	{ ?>
	
	margin-bottom:25px;
	<?php }?>
		<?php
	if($arrows=="true" && $arrowsstyle=="style4")
	{ ?>
	
	margin-left:20px;
		margin-right:20px;

	<?php }?>
	
			<?php
	if($arrows=="true" && $arrowsstyle=="style10")
	{ ?>
	
	margin-left:15px;
		margin-right:15px;

	<?php }?>
	
}
			<?php
	if($captionpos=="bottom")
	{ ?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
	position:absolute;
	left:0px;
	bottom:0px;
	background:<?php echo $captionbg;?>;
	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo $captionob*100;?>)";
       filter: alpha(<?php echo $captionob*100;?>);
	opacity:<?php echo $captionob;?>; /* Overridden by captionOpacity setting */
	width:100%;
			<?php if($captionh=="full")
	{?>
	height:100%;
	<?php }?>
	
	<?php if($captionh=="custom")
	{?>
	height:<?php echo $captionh2;?>px;
	<?php }?>
	z-index:8;
}
<?php
}
?>
			<?php
	if($captionpos=="top")
	{ ?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
	position:absolute;
	left:0px;
	top:0px;
	background:<?php echo $captionbg;?>;
	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo $captionob*100;?>)";
    filter: alpha(<?php echo $captionob*100;?>);
	opacity:<?php echo $captionob;?>; /* Overridden by captionOpacity setting */
	width:100%;
	z-index:8;
		<?php if($captionh=="full")
	{?>
	height:100%;
	<?php }?>
	
			<?php if($captionh=="custom")
	{?>
	height:<?php echo $captionh2;?>px;
	<?php }?>
}
<?php
}
?>
	<?php
	if($captionpos=="right")
	{ ?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
	position:absolute;
	right:0px;
	bottom:0px;
	background:<?php echo $captionbg;?>;
	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo $captionob*100;?>)";
       filter: alpha(<?php echo $captionob*100;?>);
	opacity:<?php echo $captionob;?>; /* Overridden by captionOpacity setting */	width:<?php echo $captionwidth;?>px;
	<?php if($captionh=="full")
	{?>
	height:100%;
	<?php }?>
	<?php if($captionh=="custom")
	{?>
	height:<?php echo $captionh2;?>px;
	<?php }?>
		width:<?php echo $captionwidth;?>px;
	z-index:8;
}
<?php
}
?>

	<?php
	if($captionpos=="left")
	{ ?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
	position:absolute;
	left:0px;
	bottom:0px;
	background:<?php echo $captionbg;?>;
	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo $captionob*100;?>)";
     filter: alpha(<?php echo $captionob*100;?>);
	opacity:<?php echo $captionob;?>; /* Overridden by captionOpacity setting */
		width:<?php echo $captionwidth;?>px;
	<?php if($captionh=="full")
	{?>
	height:100%;
	<?php }?>
	<?php if($captionh=="custom")
	{?>
	height:<?php echo $captionh2;?>px;
	<?php }?>
	z-index:8;
}
<?php
}
?>
	<?php
	if($captionpos=="custom")
	{ ?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
	position:absolute;
	<?php echo $hspace;?>:<?php echo $cfromh;?>px;
	<?php echo $vspace;?>:<?php echo $cfromv;?>px;
	background:<?php echo $captionbg;?>;
	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo $captionob*100;?>)";
     filter: alpha(<?php echo $captionob*100;?>);
	opacity:<?php echo $captionob;?>; /* Overridden by captionOpacity setting */
		width:<?php echo $captionwidth;?>px;
	<?php if($captionh=="full")
	{?>
	height:100%;
	<?php }?>
				<?php if($captionh=="custom")
	{?>
	height:<?php echo $captionh2;?>px;
	<?php }?>
	z-index:8;
}
<?php
}
?>

<?php if($crounded=="right")
{?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
    -moz-border-radius: 0 8px 8px 0;
    -webkit-border-radius: 0 8px 8px 0;
    border-radius: 0 8px 8px 0;
	}
	<?php }?>

<?php if($crounded=="left")
{?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
    -moz-border-radius: 8px 0 0 8px;
    -webkit-border-radius: 8px 0 0 8px;
    border-radius: 8px 0 0 8px;
	}
	<?php }?>
	
	
<?php if($crounded=="top")
{?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
    -moz-border-radius: 8px 8px 0 0;
    -webkit-border-radius: 8px 8px 0 0;
    border-radius: 8px 8px 0 0;
	}
	<?php }?>
	
	<?php if($crounded=="bottom")
{?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
    -moz-border-radius: 0 0 8px 8px;
    -webkit-border-radius: 0 0 8px 8px;
    border-radius: 0 0 8px 8px;
	}
	<?php }?>
	
	<?php if($crounded=="both")
{?>
.theme-default<?php echo $moduleid;?> .nivo-caption {
    -moz-border-radius: 8px 8px 8px 8px;
    -webkit-border-radius: 8px 8px 8px 8px;
    border-radius: 8px 8px 8px 8px;
	}
	<?php }?>

.nivo-caption h3 {
    font-weight: bold;
    font-size: 18px;
    margin: 2px 0 2px 0;
    padding: 0 2px 0 7px;
	color:#FFFFFF;
}
.nivo-caption p {
    padding: 0 2px 0 7px;
    margin: 2px 0 2px 0;
}

.nivo-caption h5
{
padding-left: 5px !important;
}

 .theme-default<?php echo $moduleid;?> .nivo-caption h5, .theme-default<?php echo $moduleid;?>  .nivo-caption h5 a{
margin:0 !important;
<?php if($titlefont!="default")
{ ?>
font-family: <?php echo $titlefont;?> !important;
<?php } ?>
font-size:<?php echo $labelsize;?>px !important;
font-weight:normal !important; 
text-decoration:none !important;
padding-right: 5px !important;
padding-bottom:0px !important;
padding-top:5px !important;
color:<?php echo $labelcolor;?> !important;
line-height:<?php echo $labelsize+5;?>px !important;
display: block !important;
text-align:left !important;

}
.theme-default<?php echo $moduleid;?> .nivo-caption p{

letter-spacing: 0.4px !important;

line-height:<?php echo $descsize+5;?>px !important;

margin:0 !important;

<?php if($descfont!="default")

{ ?>

font-family: <?php echo $descfont;?> !important;

<?php } ?>

font-size:<?php echo $descsize;?>px !important;
padding-left: 5px !important;
padding-right: 5px !important;
padding-bottom:2px !important;
padding-top:0px !important;
color:<?php echo $desccolor;?> !important;
z-index:10 !important;
display: block !important;
text-align:left !important;

}

<?php
if($dotspos=="top")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNavHolder
{
	position:absolute;
	left:50%;
	width:auto;
	top:0;
	}
.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
	position: relative;
	left: -50%;
	top:5px;
	width:auto;
}

<?php
}
?>

<?php
if($dotspos=="topright")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav {

	position:absolute;
	right: 5px !important;
    top: 5px !important;
    margin-left:-40px; /* Tweak this to center bullets */
}

<?php
}
?>

<?php
if($dotspos=="topleft")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav {

	position:absolute;
	left: 45px !important;
    top: 5px !important;
    margin-left:-40px; /* Tweak this to center bullets */
}
<?php
}
?>

<?php
if($dotspos=="bottom")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-controlNavHolder
{
	position:absolute;
	left:50%;
	width:auto;
	top:100%;
	}
.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
	position: relative;
	left: -50%;
	bottom:20px;
	width:auto;
}

<?php
}
?>
<?php
if($dotspos=="bottomright")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav {

	position:absolute;
	bottom:5px !important;;
	right: 5px !important;
    margin-left:-40px; /* Tweak this to center bullets */
}

<?php
}
?>
<?php
if($dotspos=="bottomleft")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav {
	position:absolute;
	bottom:5px !important;;
	left: 45px !important;
    margin-left:-40px; /* Tweak this to center bullets */
}

<?php
}
?>
<?php
if($dotspos=="bottomm")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNavHolder
{
	position:absolute;
	left:50%;
	width:auto;
	top:100%;
	}
.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
	position: relative;
	left: -50%;
	bottom:-10px;
	width:auto;
}

<?php
}
?>
<?php
if($dotsstyle=="style1")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav a {
	display:block;
	width:17px;
	height:17px;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets.png) no-repeat;
	text-indent:-9999px;
	border:0;
	margin-right:3px;
	float:left;
	position: relative;
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active {

	background-position:0 -22px;
}

<?php
}
?>
<?php
if($dotsstyle=="style2")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav {
    padding:2px 0 0 5px;
    z-index:20;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a {
    display:block;
    width:18px;
    height:18px;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets2.png) no-repeat;
    text-indent:-9999px;
    border:0;
    margin-right:1px;
    float:left;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active {
    background-position:0 -22px;
}

<?php
}
?>
<?php
if($dotsstyle=="style3")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
    padding: 5px;
    font-size: 0px; 
    float: left;

}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a {
    margin-left:4px;
    width:8px;
    height:8px;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets3.png) left top;
    float: left; 
    text-indent: -1000px; 
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active, .theme-default<?php echo $moduleid;?> .nivo-controlNav a:hover {
    background-position: right top;
}
<?php
}
?>

<?php
if($dotsstyle=="style4")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
    font-size: 0px;
    padding: 0px;
    float: left;
    z-index: 40;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a
{
    margin-left: 0;
    width: 20px;
    height: 15px;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets4.png) right top;

    float: left;
    text-indent: -1000px;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active, .theme-default<?php echo $moduleid;?> .nivo-controlNav a:hover
{
    background-position: left top;
}
<?php
}
?>
<?php
if($dotsstyle=="style5")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
	font-size: 0px; 
	padding: 5px; 
	float: left;
	z-index:20;
	<?php if($dotspos=="bottom")
	{?>
		bottom:25px !important;
	<?php } ?>
		<?php if($dotspos=="bottomright" || $dotspos=="bottomleft")
	{?>
		bottom:0px !important;;
	<?php } ?>
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a
{
	margin: 0;
	width:16px;
	height:15px;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets5.png) left top;
	float: left; 
	text-indent: -1000px; 
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a:hover
{
	background-position: -16px 0;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active
{
	background-position: right top;
}

<?php
}
?>

<?php
if($dotsstyle=="style6")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
    font-size: 0px;
    padding: 2px;
    z-index: 20;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a
{
    width: 15px;
    height: 15px;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets6.png) left top;
    float: left;
    text-indent: -1000px;
    position: relative;
    margin-left: 2px;
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a:hover
{
    background-position: 0 50%;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active
{
    background-position: 0 100%;
}

<?php
}
?>
<?php
if($dotsstyle=="style7")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
	margin-bottom:5px;
	z-index:20;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a
{
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets7.png) no-repeat scroll 0 0 transparent;

    border: 0 none;
    display: block;
    float: left;
    cursor: pointer;
    margin-right: 4px;
    text-indent: -9999px;
    z-index: 100;
    height: 11px;
    width: 11px;
    outline: none;
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a:hover
{
    background-position: 100% 0;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active
{
    background-position: -11px;
}

<?php
}
?>
<?php
if($dotsstyle=="style8")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
    font-size: 0px;
    padding: 2px;
    z-index: 70;
		<?php if($dotspos=="bottom")
{
?>
	bottom:30px !important;

<?php }?>
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a
{
width:20px;
height:20px;
overflow:hidden;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets8.png) 0 0 no-repeat;
    float: left;
    text-indent: -1000px;
    position: relative;
    margin-left: 2px;
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a:hover
{
    background-position: 0 -30px;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active
{
    background-position: 0 100%;
}
<?php
}
?>
<?php
if($dotsstyle=="style9")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-controlNav
{
    font-size: 0px;
    padding: 2px;
    z-index: 70;
	margin-bottom:2px;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a
{
width:20px;
height:15px;
overflow:hidden;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets9.png) 0 0 no-repeat;
    float: left;
    text-indent: -1000px;
    position: relative;
    margin-left: 2px;
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a:hover
{
    background-position: 0 -30px;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active
{
    background-position: 0 -15px;
}
<?php
}
?>
<?php
if($dotsstyle=="style10")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav a {
    display:block;
    width:22px;
    height:15px;
    background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets10.png) no-repeat;
    text-indent:-9999px;
    border:0;
    margin-right:3px;
    float:left;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active {
    background-position:0 -22px;
}

.theme-default<?php echo $moduleid;?> .nivo-directionNav a {
	display:none;
}

<?php
}
?>
<?php
if($dotsstyle=="style11")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav {
	margin-bottom:5px;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a {
	display:block;
	width:13px;
	height:12px;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets11.png) no-repeat;
	text-indent:-9999px;
	border:0;
	margin-right:3px;
	float:left;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active {
	background-position:0 -12px;
}

<?php
}
?>
<?php
if($dotsstyle=="style12")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav {
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/line.png) repeat-x 0 6px;
	z-index:20;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a {
    display:block;
    width:13px;
    height:14px;
    background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/bullets12.png) no-repeat;
    text-indent:-9999px;
    border:0;
    margin-right:15px;
    float:left;
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a:last-child, a.last-child {margin-right:0px}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active {
    background-position:0 -15px;
}

<?php
}
?>
<?php
if($dotsstyle=="style13")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-controlNav {
margin-right:5px;
}
.theme-default<?php echo $moduleid;?> .nivo-controlNav a {
    margin-left: 5px; 
    height: 10px; 
    width: 10px; 
    float: left; 
    border: 1px solid #d6d6d6; 
    color: #d6d6d6; 
    text-indent: -9000px;
	margin-bottom:5px;
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a.active {
    background-color: #d6d6d6; 
    color: #FFFFFF; 
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav a.hover {
    background-color: #d6d6d6; 
    color: #FFFFFF; 
}
<?php
}
?>
<?php
/***********************************************ARROWS***************************************/
?>
<?php
if($arrowsstyle=="style1")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-directionNav a {
	display:block;
	width:30px;
	height:30px;
	background:url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows.png) no-repeat;
	text-indent:-9999px;
	border:0;
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav {
	background-position:-30px 0;
	right:15px;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav {
	left:15px;
}
<?php
}
?>
<?php
if($arrowsstyle=="style2")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-directionNav a {
    text-indent: -9000px; 
position:absolute;
    display:none;
    top:45%;
    margin-top:-28px;
    position:absolute;
    z-index:1001;
    height: 62px;
    width: 38px;
    background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows2.gif);
    display:block;
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav {
    background-position: 100% 0;
    right:-4px;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav {
    left:-4px;
    background-position: 0 0; 
}

<?php
}
?>

<?php
if($arrowsstyle=="style3")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{    text-indent: -9000px; 
display: block;
    position: absolute;
    display: none;
    top: 50%;
    margin-top: -37px;
    opacity: 0.7;
    position: absolute;
    z-index: 50;
    height: 75px;
    width: 60px;
    background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows3.png);
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
    display: block;
    background-position: 100% 0;
    right: 0px;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
    display: block;
    left: 0px;
    background-position: 0 0;
}

<?php
}
?>
<?php
if($arrowsstyle=="style4")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{    text-indent: -9000px; 
position:absolute;
	display:block;
	top:50%;
	margin-top:-28px;
	position:absolute;
	z-index:70;
	height: 56px;
	width: 29px;
    background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows4.png);
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
	background-position: 100% 0; 
	right:-29px;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
	left:-29px;
	background-position: 0 0; 
}

<?php
}
?>
<?php
if($arrowsstyle=="style5")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{    text-indent: -9000px; 
position:absolute;
	display:block;
	top:45%;
	margin-top:-16px;
	position:absolute;
	z-index:70;
	height: 67px;
	width: 32px;
    background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows5.png);
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
	background-position: 100% 0; 
	right:-7px;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
	left:-7px;
	background-position: 0 100%; 
}
<?php
}
?>
<?php
if($arrowsstyle=="style6")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{    text-indent: -9000px; 
outline:none;
	position:absolute;
	display:none;
	top:50%;
	width:56px;
	height:56px;
	margin:-28px 0 0 0;
	z-index:70;
	cursor:pointer;
	opacity:0.6 !important;
    -moz-border-radius:10px;
    -webkit-border-radius:10px;
    border-radius:10px;
    display:block;
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
	right:5px;
	background:#000 url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows6_next.png) no-repeat 50% 50%;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
	left:5px;
	background:#000 url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows6_prev.png) no-repeat 50% 50%;
}
<?php
}
?>
<?php
if($arrowsstyle=="style7")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{
	position:absolute;
	display:none;
	top:50%;
	margin-top:-22px;
	position:absolute;
	z-index:70;
	height: 45px;
	width: 45px;
    background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows7.png);
	display:block;    text-indent: -9000px; 
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
	background-position: 100% 0; 
	right:10px;
}
.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
	left:10px;
	background-position: 0 0; 
}
<?php
}
?>

<?php
if($arrowsstyle=="style8")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{
    position: absolute;
    display: block;
    top: 45%;
    margin-top: -33px;
    position: absolute;
    z-index: 70;
    height: 66px;
    width: 59px;
    background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows8.png);    text-indent: -9000px; 
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
    background-position: 100% 0;
    right: -2px;
}
.theme-default<?php echo $moduleid;?> a.nivo-nextNav:hover
{
    background-position: 100% 100%;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
    left: -2px;
    background-position: 0 0;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav:hover
{
    background-position: 0 100%;
}

<?php
}
?>
<?php
if($arrowsstyle=="style9")
{
?>
.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{
    position: absolute;
    display: none;
    top: 45%;
    margin-top: -15px;
    z-index: 70;
    height: 45px;
    width: 45px;
    background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows9.png);
    display: block;    text-indent: -9000px; 
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
    background-position: 100% 0;
    right: 10px;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
    left: 10px;
    background-position: 0 0;
}
<?php
}
?>

<?php
if($arrowsstyle=="style10")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{    text-indent: -9000px; 
z-index: 70;
    cursor: pointer;
    display: block;
    width: 38px;
    height: 77px;

    margin: -39px 0px 0px 0px !important;
    background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows10.png);
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
    background-position:  100% 0%;
    right: -19px;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
    background-position:  0% 100% ;
    left: -19px;
}

<?php
}
?>
<?php
if($arrowsstyle=="style11")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{    text-indent: -9000px; 
z-index: 70;
    cursor: pointer;
    display: block;
    width: 38px;
    height: 77px;

    margin: 0px 0px 0px 0px !important;
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
top: 0;
width: 40px;
height: 100%;
margin-top: 0px;
background-color: rgba(0, 0, 0, 0.3);
background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows11.png);
background-repeat: no-repeat;
position: absolute;
cursor: pointer;
background-position: 0 50%;
z-index:20;
}
.theme-default<?php echo $moduleid;?> a.nivo-nextNav:hover
{
background-position: -60px 50%;

}
.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
top: 0;
width: 40px;
height: 100%;
margin-top: 0px;
background-color: rgba(0, 0, 0, 0.3);
background-image: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows11.png);
background-repeat: no-repeat;
position: absolute;
cursor: pointer;
background-position: -180px 50%;
z-index:20;

}
.theme-default<?php echo $moduleid;?> a.nivo-prevNav:hover
{
background-position:-240px 50%
}

.theme-default<?php echo $moduleid;?> .nivo-controlNav {
z-index:25;
}

<?php
}
?>

<?php
if($arrowsstyle=="style12")
{
?>

.theme-default<?php echo $moduleid;?> .nivo-directionNav a
{    text-indent: -9000px; 
z-index: 70;
    display: block;
    width: 50px;
    height: 50px;
top:40% !important;
    margin: 0px 0px 0px 0px !important;
}

.theme-default<?php echo $moduleid;?> a.nivo-nextNav
{
width: 50px;
height: 50px;
background: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows12.png) 0 50px no-repeat;
position: absolute;
cursor: pointer;
background-position:0 0;
right:19px;
z-index:20;
}

.theme-default<?php echo $moduleid;?> a.nivo-prevNav
{
width: 50px;
height: 50px;
background: url(<?php echo JUri::root();?>modules/mod_PlimunNivoSlider/themes/default/arrows12.png) 0 50px no-repeat;
position: absolute;
cursor: pointer;
background-position:0 -50px;
left:19px;

z-index:20;

}

.theme-default<?php echo $moduleid;?> .nivo-controlNav {
z-index:25;
}

<?php
}
?>
</style>

<?php
//$doc->addScript("modules/mod_PlimunNivoSlider/js/jquery.nivo.slider.js");


if($jver=="1.6.1")
{
$j0=JUri::root()."modules/mod_PlimunNivoSlider/js/jquery-1.6.1.min.js";
}
else
{
$j0="http://ajax.googleapis.com/ajax/libs/jquery/".$jver."/jquery.min.js";
}
$j1=JUri::root()."modules/mod_PlimunNivoSlider/js/jquery.nivo.slider.js";


if($load=="onmod" && $show_jquery=="yes")
{
?>
<script src="<?php echo $j0;?>" type="text/javascript"></script>
<?php }?>

<script src="<?php echo $j1;?>" type="text/javascript"></script>


<div class="joomla_pns<?php echo $moduleclass_sfx?>" align="<?php echo $align;?>">

<div class="slider-wrapper theme-default<?php echo $moduleid;?>">

            <div id="slider<?php echo $moduleid;?>" class="nivoSlider">

             <?php echo $image[0].$image[1].$image[2].$image[3].$image[4].$image[5].$image[6].$image[7].$image[8].$image[9].$image[10].$image[11].$image[12].$image[13].$image[14].$image[15].$image[16].$image[17].$image[18].$image[19]?>

            </div>

               <?php echo $labels[0].$labels[1].$labels[2].$labels[3].$labels[4].$labels[5].$labels[6].$labels[7].$labels[8].$labels[9].$labels[10].$labels[11].$labels[12].$labels[13].$labels[14].$labels[15].$labels[16].$labels[17].$labels[18].$labels[19];?>


</div>

<?php
if($load=="onmod")

{?>


    <script type="text/javascript">
 var pns = jQuery.noConflict();
     pns(window).load(function() {

        pns('#slider<?php echo $moduleid;?>').nivoSlider({
		effect: '<?php echo $imageeffect;?>', // Specify sets like: 'fold,fade,sliceDown'
        slices: 15, // For slice animations
        boxCols: 8, // For box animations
        boxRows: 4, // For box animations
        animSpeed: <?php echo $velocity;?>, // Slide transition speed
        pauseTime: <?php echo $timeinterval;?>, // How long each slide will show
        startSlide: 0, // Set starting Slide (0 index)
        directionNav: <?php echo $arrows;?>, // Next & Prev navigation
        directionNavHide: <?php echo $hidetools;?>, // Only show on hover
        controlNav: <?php echo $navigation;?>, // 1,2,3... navigation
        controlNavThumbs: false, // Use thumbnails for Control Nav
        controlNavThumbsFromRel: false, // Use image rel for thumbs
        controlNavThumbsSearch: '.jpg', // Replace this with...
        controlNavThumbsReplace: '_thumb.jpg', // ...this in thumb Image src
        keyboardNav: true, // Use left & right arrows
        pauseOnHover: true, // Stop animation while hovering
        manualAdvance: <?php echo $manual;?>, // Force manual transitions
        captionOpacity: <?php echo $captionob;?>, // Universal caption opacity
        prevText: 'Prev', // Prev directionNav text
        nextText: 'Next', // Next directionNav text
        beforeChange: function(){}, // Triggers before a slide transition
        afterChange: function(){}, // Triggers after a slide transition
        slideshowEnd: function(){}, // Triggers after all slides have been shown
        lastSlide: function(){}, // Triggers when last slide is shown
        afterLoad: function(){} // Triggers when slider has loaded
		});

    });

    </script>
<?php }?>
</div>
