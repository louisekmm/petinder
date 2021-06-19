<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<base href="<?=URL?>libs/jpegcam/">
	<title>JPEGCam Test Page</title>
	<meta name="generator" content="TextMate http://macromates.com/">
	<meta name="author" content="Joseph Huckaby">
	<!-- Date: 2008-03-15 -->
</head>
<body>
	<table><tr><td valign=top>

	<!-- First, include the JPEGCam JavaScript Library -->
	<script type="text/javascript" src="webcam.min.js"></script>
	
	<!-- Configure a few settings -->
	<script language="JavaScript">
		webcam.set_api_url( 'test.php' );
		webcam.set_quality( 90 ); // JPEG quality (1 - 100)
		webcam.set_shutter_sound( true ); // play shutter click sound
	</script>
	
	<!-- Next, write the movie to the page at 320x240 -->
	<script language="JavaScript">
		document.write( webcam.get_html(320, 240) );
	</script>
	
	<!-- Some buttons for controlling things -->
	<br/><form>
		<input type=button value="<?=$this->lng['configurar_webcam']?>" onClick="webcam.configure()">
		&nbsp;&nbsp;
		<input type=button value="<?=$this->lng['tirar_webcam']?>" onClick="take_snapshot()">
	</form>
	
	<!-- Code to handle the server response (see test.php) -->
	<script language="JavaScript">
		webcam.set_hook( 'onComplete', 'my_completion_handler' );
		
		function take_snapshot() {
			// take snapshot and upload to server
			document.getElementById('upload_results').innerHTML = '<h1>Aguarde...</h1>';
			webcam.snap();
		}
		
		function my_completion_handler(msg) {
			// extract URL out of PHP output
			if (msg.match(/(http\:\/\/\S+)/)) {
				var image_url = RegExp.$1;
				// show JPEG image in page
				document.location = image_url;
				
				webcam.reset();
			}
			else alert("Error: " + msg);
		}
	</script>
	
	</td><td width=50>&nbsp;</td><td valign=top>
		<div id="upload_results" style="background-color:#eee;"></div>
	</td></tr></table>
</body>
</html>