<?php

require("fileupload-class.php");

include 'header.inc';

if($submitted != true){

?>
<form enctype="multipart/form-data" name="miscfileupload" action="miscfileupload.php" method="post">
<input type="hidden" name="submitted" value="true">
<input type="hidden" name="arrayID" value="<?php echo $arrayID; ?>">
<table>

<tr>
<td><strong>File:</strong></td>
<td>
<input name="file" type="file"><font color="red"><strong>*</strong></font>
</td>
</tr>


<tr>
<td><input type="submit" name="submit" value="Submit"></td>
<td><input type="reset" value="Reset Form"></td>
</tr>
</table>
</form>
<?php
}else{


$my_uploader = new uploader('en'); // errors in English

			$my_uploader->max_filesize(9000000000);
			//$my_uploader->max_image_size(800, 800);
			$my_uploader->upload('file', '', '');
			$my_uploader->save_file('/var/www/html/edge2/IMAGES/', 2);

			if ($my_uploader->error) {
				$fileError = 1;
				$fileErrorText = $my_uploader->error;
				print($my_uploader->error . "<br><br>\n");
			} else {
				print("Thanks for uploading " . $my_uploader->file['name'] . "<br><br>\n");
				$inputfilename = $my_uploader->file['name'];

			}
}
?>
