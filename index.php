<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
<title>CSS3 Maximizer - Automatic CSS cross-browser generator for Chrome, Firefox, Safari, and IE.</title>
<style style="text/css">
body {
	overflow: hidden;
	margin: 0;
	border: 1px solid #000000;
	border: 1px solid rgba(0, 0, 0, 0.5);
	border-radius: 6px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	background: #0d0d0d;
	background: -webkit-gradient(linear, left top, left bottom, from(#000), to(#777));
	background: -webkit-linear-gradient(top, #000 0%, #777 100%);
	background: -moz-linear-gradient(top, #000 0%, #777 100%);
	background: -ms-gradient(top, #000 0%, #777 100%);
	background: -o-gradient(top, #000 0%, #777 100%);
	background: linear-gradient(top, #000 0%, #777 100%);
	box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.35);
	-moz-box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.35);
	-webkit-box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.35);
	text-shadow: 1px 1px 1px #000;
	-moz-text-shadow: 1px 1px 1px #000;
	color: #FFFFFF;
	color: rgba(255, 255, 255, 1);
	padding: 7px 8px 7px;
	font-size: 14px;
	font-family: helvetica neue, helvetica;
}
label {
	display: inline-block;
	margin-left: 3px;
	font-size: 15px;
}
h1 {
	margin: 0;
}
textarea {
	color: #676767;
	border: 1px solid #ccc;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	height: 100%;
	font-size: inherit;
	line-height: 1.35em;
	background: -webkit-gradient( linear, 0 50%, 0 100%, from(rgba(255,255,255,1)), to(rgba(255,255,255,0.85)) );
	background: -webkit-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.85) 100%);
	background: -moz-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.85) 100%);
	background: -ms-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.85) 100%);
	background: -o-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.85) 100%);
	background: linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(255, 255, 255, 0.85) 100%);
	padding: 8px;
}
input[type="submit"] {
	transition-property: background, color;
	-moz-transition-property: background, color;
	-ms-transition-property: background, color;
	-o-transition-property: background, color;
	-webkit-transition-property: background, color;
	transition-duration: .4s;
	-moz-transition-duration: .4s;
	-ms-transition-duration: .4s;
	-o-transition-duration: .4s;
	-webkit-transition-duration: .4s;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	font-weight: bold;
	font-size: inherit;
	padding: 7px 15px;
	color: #fff;
	cursor: pointer;
	border: 1px solid #444;
	border-top: 1px solid #666;
	border-right: 1px solid #555;
	border-left: 1px solid #555;
	background-color: #000000;
	background-color: rgba(0, 0, 0, 1);
	background: -webkit-gradient(linear, left top, left bottom, from(rgba(255,255,255,0.5)), to(rgba(0,0,0,0.5)));
	background: -webkit-linear-gradient(top, rgba(255, 255, 255, 0.5) 0%, rgba(0, 0, 0, 0.5) 100%);
	background: -moz-linear-gradient(top, rgba(255, 255, 255, 0.5) 0%, rgba(0, 0, 0, 0.5) 100%);
	background: -ms-gradient(top, rgba(255, 255, 255, 0.5) 0%, rgba(0, 0, 0, 0.5) 100%);
	background: -o-gradient(top, rgba(255, 255, 255, 0.5) 0%, rgba(0, 0, 0, 0.5) 100%);
	background: linear-gradient(top, rgba(255, 255, 255, 0.5) 0%, rgba(0, 0, 0, 0.5) 100%);
}
input[type="submit"]:HOVER {
	background-color: #1060b9;
	background-position: left bottom;
}
input[type="submit"]:ACTIVE {
	background-color: #FF0000;
	background-color: rgba(255, 0, 0, 0.7);
}
img {
	transition-property: opacity;
	-moz-transition-property: opacity;
	-ms-transition-property: opacity;
	-o-transition-property: opacity;
	-webkit-transition-property: opacity;
	transition-duration: .4s;
	-moz-transition-duration: .4s;
	-ms-transition-duration: .4s;
	-o-transition-duration: .4s;
	-webkit-transition-duration: .4s;
	opacity: 0.5;
}
img:hover {
	opacity: 1;
}
</style>
<script type="text/javascript">
window.onresize =
window.onload = function() {
	var d = document.getElementById("css");
	d.style.width = (window.innerWidth - 36) + "px";
	d.style.height = (window.innerHeight - d.offsetTop - 26) + "px";
};
</script>
</head>
<body>
<form method="POST">
<input type="submit" value="Maximize CSS!" title="Create Cross-Browser Code!" style="margin-bottom: 7px">
<input type="checkbox" value="checked" <?php echo $_POST['compress'] ? "checked" : "" ?> name="compress" id="compress"><label for="compress" title="Minify code!">Compress</label>
<a href="http://dev.w3.org/csswg/" style="float: right;" title="CSS Working Group"><img src="html5-badge-h-css3.png" style="height: 35px"></a>
<br>
<textarea id="css" name="q" style="width: 97%; height: 1000px">
<?php
if ($_POST['q']) {
	// maximize css
	require_once("./CSS3Maximizer.php");
	require_once("./inc/ColorSpace.php");
	$CSS3Maximizer = new CSS3Maximizer;
	$compress = $_POST['compress'] ? true : false;
	$css = $CSS3Maximizer->clean($_POST['q'], $compress);
	echo <<<X
{$css}
</textarea>
</form>
</body>
</html>
X;
} else {
	echo <<<X
#example {
	background: #0d0d0d;
	background: -webkit-gradient(linear, center top, center bottom, from(#777), color-stop(10%,#333), to(#000000));
	border: 1px solid rgba(0,0,0,0.5);
	border-radius: 6px;
	box-shadow: 3px 3px 3px rgba(0,0,0,0.35);
	color: rgba(255,255,255,0.5);
	text-shadow: 0 2px 4px #000;
	user-select: none;
	-webkit-transition: transform 0.3s ease-out;	
	-webkit-transform: rotate(7.5deg);
}
</textarea>
</form>
</body>
</html>
X;
}
?>
