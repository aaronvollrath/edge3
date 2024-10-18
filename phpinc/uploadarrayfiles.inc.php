



<?php

#--------------------------------#
# Variables
#--------------------------------#

// The path to the directory where you want the 
// uploaded files to be saved. This MUST end with a 
// trailing slash unless you use $path = ""; to 
// upload to the current directory. Whatever directory
// you choose, please chmod 777 that directory.
	
$path = "$datafilelocation";
// The name of the file field in your form.
	
	$upload_file_name = "userfile";
// The name of the file field in your form.

	$upload_file_name = "userfile";
// ACCEPT mode - if you only want to accept
// a certain type of file.
// possible file types that PHP recognizes includes:
//
// OPTIONS INCLUDE:
//  text/plain
//  image/gif
//  image/jpeg
//  image/png
	
	// Accept ONLY gifs's
	#$acceptable_file_types = "image/gifs";
	
	// Accept GIF and JPEG files
	//$acceptable_file_types = "image/gif|image/jpeg|image/pjpeg";
	
	// Accept ALL files
	$acceptable_file_types = "";

// If no extension is supplied, and the browser or PHP
// can not figure out what type of file it is, you can
// add a default extension - like ".jpg" or ".txt"

	$default_extension = "";

// MODE: if your are attempting to upload
// a file with the same name as another file in the
// $path directory
//
// OPTIONS:
//   1 = overwrite mode
//   2 = create new with incremental extention
//   3 = do nothing if exists, highest protection

	$mode = 2;
	
	
#--------------------------------#
# PHP
#--------------------------------#
	if (isset($_POST['submit'])) {
		#analyze($_POST);
	
			
		// Create a new instance of the class
		$my_uploader = new uploader('en'); // for error messages in french, try: uploader('fr');
		
		// OPTIONAL: set the max filesize of uploadable files in bytes
		$my_uploader->max_filesize(10000000000000000000);
		
		// OPTIONAL: if you're uploading images, you can set the max pixel dimensions 
		//$my_uploader->max_image_size(800, 800); // max_image_size($width, $height)
		
		// UPLOAD the file
		if ($my_uploader->upload($upload_file_name, $acceptable_file_types, $default_extension)) {
			$my_uploader->save_file($path, $mode);
		}
		
		if ($my_uploader->error) {
			echo $my_uploader->error . "<br><br>\n";
		
		} else {
			// Successful upload!
			# the index 'extention' was not my incorrect spelling....came w/ the uploader library...
			if($my_uploader->file['extention'] == ".zip"){
				#echo "the file is a zip file<br>";
				$thisname = strtoupper($my_uploader->file['name']);
				print("<h3>".$thisname . " was successfully uploaded!</h3><hr>");
				print("<h3><font color='red'><strong>Do not refresh this page or there will be duplicate files created!</strong></font></h3>");
				#  We need to create a directory in $datafilelocation to accomodate the uploaded data files.
				#  This directory is based on the array name.  The name format is:  US22502567_251485031887
				#  So, we need to explode into an array...
				$name = $my_uploader->file['raw_name'];
				$dirarray = explode("_",$name);
				$dir = $dirarray[0]."_".$dirarray[1];
				$dir = strtoupper($dir);
				chdir($datafilelocation); # change directory to the upload path
				$command = "unzip $name.zip -d ./$dir";
				$str=exec($command);
				// Print all the array details...
				#print_r($my_uploader->file);
				# Delete the uploaded file....
				$command = "rm $name.zip";
				$str=exec($command);
			}else{
				echo "ERROR: THE FILE UPLOADED WAS NOT A ZIP ARCHIVE!<BR>";
			}
			
			

 		}
 	}else{




#--------------------------------#
# HTML FORM
#--------------------------------#
?>
	<form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
	<input type="hidden" name="submitted" value="true">
		
		<h3>Upload Array Data Files</h3><br>
		<input name="<?= $upload_file_name; ?>" type="file">
		<input type="submit" value="Upload File" name="submit" >
	</form>
	<hr>

<?php
	
	}
?>


