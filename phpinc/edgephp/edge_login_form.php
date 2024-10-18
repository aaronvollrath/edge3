

<?php
	//if($logged_in != 1){
if(strcmp($_SESSION['username'], "") == 0){
?>

<form name="login" method="post"  action="<?php  $_SERVER['PHP_SELF'] ?>">
<p>Username:
<input name="uname" type="text">
Password:
<input name="passwd" type="password">
<input type="submit" name="submit" value="Submit" >
</p>
</form>
<a href="register.php">Need to Register?</a><br>
<?php
}else{ //$logged_in == 1.....
//echo "This is logged_in: $logged_in";
?>
Welcome <?php echo $_SESSION['firstname'] ?> <?php echo $_SESSION['lastname'] ?>!
<form name="logout" method="post" action="<?php  $_SERVER['PHP_SELF'] ?>">
<input name="logout" type="submit" value="Log out">
</form>
<br>
<form name="update" method="post" action="update.php">
<input name="update" type="submit" value="Update your account.">
</form>
<?php
}
?> 

