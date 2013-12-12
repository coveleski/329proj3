<?php
if(isset($_GET['logout']) && $_GET['logout'] == 1){
	$system->Logout();
}

if(isset($_POST['cusername']) && $_POST['cpassword'] && $_POST['csubmit']){
	$error = $system->Register($_POST['cusername'],$_POST['cpassword']);
	
	if(!$error){
		echo("password was incorrect");
	}
}
	
?>


<?php if(isset($_SESSION['username'])) { ?>
	<?php echo ($_SESSION['username']); ?>	<a class="login" href="index.php?logout=1">Logout</a>
<?php } else { ?>
<form method="post" action="index.php">
	Username(Email):<input name="cusername" type="text" />
	Password:<input name="cpassword" type="password" />
	<input name="csubmit" type="submit" value="login" />
</form>

<?php }?>