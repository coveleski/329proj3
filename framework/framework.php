<?php
session_start();
class System {
	public $registerConfirm;
	public $session ;
	public function __construct(){
		$this->connect(); //Connect to the MySQL database
		$this->sessionCreate(); //Create a session for the user
	}
	public function connect(){ //Open the database information and connect to the database, else return an error
		include("db.php");
		$connect = mysql_connect($db_host,$db_username,$db_pass);
		mysql_select_db($db_name) or die("MySQL error: Could not connect to database.\n".mysql_error());
	}
	public function secure($input){ //Function to sanitize any user entries that come in contact with a MySQL query
		$secure = strip_tags(mysql_real_escape_string($input));
		return $secure;
	}
	public function sessionCreate(){ //Create the user session, set the id and password into the session, and create a cookie if requested
		if($_SESSION['id'] && $_SESSION['password']){
			$id = $this->secure($_SESSION['id']);
			$password = $this->secure($_SESSION['password']);
			$select = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' AND `password` = '$password' LIMIT 1");
			$this->session = mysql_fetch_array($select);
		}
	}
	
	public function Register($username, $password){ 			
		$username = $this->secure($username);
		$password = $this->secure($password);
			
		if(!empty($username)){		// Let's find the user by its ID
			
			$query = mysql_query("SELECT * FROM Users WHERE username = '$username'");
			$result = mysql_fetch_array($query);
				// If not, let's add it to the database
			if(mysql_num_rows($query) > 0 && $result['password'] == $password){
				$_SESSION['id'] = $result['ID'];
				$_SESSION['username'] = $result['username'];
				$_SESSION['level'] = $result['level'];
				return true;

			}elseif(mysql_num_rows($query) > 0 && $result['password'] != $password){
				return false;
			}elseif(mysql_num_rows($query) <= 0){
				$query = mysql_query("INSERT INTO Users (username, password,level) VALUES ('$username', '$password', '11')");
				$uid = mysql_insert_id();
				$query = mysql_query("SELECT * FROM Users WHERE ID = '$uid'");
				$result = mysql_fetch_array($query);
				$_SESSION['id'] = $result['ID'];
				$_SESSION['username'] = $result['username'];
				$_SESSION['level'] = $result['level'];
			} 
							
		}
		else {
		    // Something's missing, go back to square 1
		     return("Something Went Wrong.");
		}
				
	}
	
	
public function getUserLevel(){ 			
			
		if(isset($_SESSION['id'])){		// Let's find the user by its ID
				$uid = $this->secure($_SESSION['id']);
				$query = mysql_query("SELECT level FROM Users WHERE ID = '$uid' LIMIT 1");
				$check = mysql_num_rows($query);
				$level = mysql_fetch_array($query);
				if($check > 0){
					$_SESSION['maxlevel'] = $level[0];
					return $level[0]; //Return the level of the user on success
				}
				else{
					return array(false,"Invalid email or username."); //Return a message on error
				}

		}
		else{
			if(isset($_SESSION['maxlevel'])){
				return $_SESSION['maxlevel']; 
			}
			else {
				return 11;
			} 
		}
				
	}
	
	
	public function getUserAchievements(){ 			
			
		if(isset($_SESSION['id'])){		// Let's find the user by its ID
				$uid = $this->secure($_SESSION['id']);
				$query = mysql_query("SELECT level FROM Achievements WHERE ID = '$uid' LIMIT 1");
				$check = mysql_num_rows($query);
				$level = mysql_fetch_array($query);
				if($check > 0){
					return $level[0]; //Return the level of the user on success
				}
				else{
					return array(false,"Invalid email or username."); //Return a message on error
				}

		}
		
		return false;
			
		
				
	}


	public function updateUserLevel($task){ //Accept a friend request from a friend request ID
		if($_SESSION['id']){
			$uid = $this->secure($_SESSION['id']);
			$lvl =  $this->secure($_SESSION['level']);
			mysql_query("UPDATE Achievements SET level  = '$lvl' WHERE ID = '$uid' LIMIT 1"); 
			
		}
		
		return true;
	}
	
	
	
	
	public function updateUserLevel($lvl){ //Accept a friend request from a friend request ID
		$_SESSION['level'] = $_SESSION['level'] + 1;
		//$_SESSION['maxlevel'] = $_SESSION['maxlevel'] + 1;

		if($_SESSION['id']){
			$uid = $this->secure($_SESSION['id']);
			$lvl =  $this->secure($_SESSION['level']);
			mysql_query("UPDATE Users SET level  = '$lvl' WHERE ID = '$uid' LIMIT 1"); 
			
		}
		
		return true;
	}


	
	public function Logout(){ //Ends the session for the user, as well as removes the cookies
		unset($_SESSION['ID']);
		unset($_SESSION['username'] );
		unset($_SESSION['level'] ); 
		unset($_SESSION['id'] );
		unset($_SESSION['username'] );
		unset($_SESSION['correct'] );
		unset($_SESSION['problems']);
	}
	
	
	public function sendMail($user){
		$to      = $user;
		$subject = 'subject';
		$message = '		
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Email Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">	
		<tr>
			<td style="padding: 10px 0 30px 0;">
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc; border-collapse: collapse;">
					<tr>
						<td align="center" bgcolor="#70bbd9" style="padding: 40px 0 30px 0; color: #153643; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
							<img src="images/h1.jpg" alt="Email Magic" width="300" height="230" style="display: block;" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
										<b>Hello user!</b>
									</td>
								</tr>
								<tr>
									<td style="padding: 20px 0 30px 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
										From looking at our reports it appears you are advancing nicely.
										It appears as though some of these words might be a little too easy for you.
										You can always test out of your current level and move on to some tougher words!
									</td>
								</tr>
								<tr>
									<td>
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td width="260" valign="top">
													<table border="0" cellpadding="0" cellspacing="0" width="100%">
														<tr>
															<td>
																<img src="images/left.gif" alt="" width="100%" height="140" style="display: block;" />
															</td>
														</tr>
														<tr>
															<td style="padding: 25px 0 0 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
																Check out our site on a mobile page!
																We will be releasing a new version shortly that allows you to practice on the go
																or where ever you are!
															</td>
														</tr>
													</table>
												</td>
												<td style="font-size: 0; line-height: 0;" width="20">
													&nbsp;
												</td>
												<td width="260" valign="top">
													<table border="0" cellpadding="0" cellspacing="0" width="100%">
														<tr>
															<td>
																<img src="images/right.gif" alt="" width="100%" height="140" style="display: block;" />
															</td>
														</tr>
														<tr>
															<td style="padding: 25px 0 0 0; color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">
																Timeing is now being recorded so it is time to speed it up!
																We will be keeping track at how fast you can type out those pesky flash cards
																and providing acheivements for quickness.
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#ee4c50" style="padding: 30px 30px 30px 30px;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;" width="75%">
										&reg; CySpell 2013<br/>
										<a href="#" style="color: #ffffff;"><font color="#ffffff">Unsubscribe</font></a> to this newsletter instantly
									</td>
									<td align="right" width="25%">
										<table border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">
													<a href="http://www.twitter.com/" style="color: #ffffff;">
														<img src="images/tw.gif" alt="Twitter" width="38" height="38" style="display: block;" border="0" />
													</a>
												</td>
												<td style="font-size: 0; line-height: 0;" width="20">&nbsp;</td>
												<td style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">
													<a href="http://www.facebook.com/" style="color: #ffffff;">
														<img src="images/fb.gif" alt="Facebook" width="38" height="38" style="display: block;" border="0" />
													</a>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
				
		';
		$headers = 'From: jeremy@dubansky.com' . "\r\n" .
		    'Reply-To: jeremy@dubansky.com' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
	}

	public function truncate_text($text, $nbrChar, $append='...') {
		if(strlen($text) > $nbrChar) {
			$text = substr($text, 0, $nbrChar);
			$text .= $append;
		}
		return $text;
	}
}
?>