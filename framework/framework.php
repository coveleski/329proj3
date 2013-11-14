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
					return $level[0]; //Return the level of the user on success
				}
				else{
					return array(false,"Invalid email or username."); //Return a message on error
				}

		}
				
	}
	public function updateUserLevel($lvl){ //Accept a friend request from a friend request ID
		$_SESSION['level'] = $_SESSION['level'] + 1;
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
	public function friendAccept($rid){ //Accept a friend request from a friend request ID
		if($_SESSION['id']){
			$rid = $this->secure($rid);
			$uid = $this->secure($_SESSION['id']);
			$select = mysql_query("SELECT * FROM `friends` WHERE `rid` = '$rid' AND `fid` = '$uid' AND `status` = '0' LIMIT 1");
			$check = mysql_num_rows($select); //If the user is logged in, get the request details, and make sure they're not already friends
			if($check > 0){
				mysql_query("UPDATE `friends` SET `status` = '1' WHERE `rid` = '$rid' LIMIT 1"); //If we successfully retrieved the details of the request, update the friend status so they will appear as friends
				return array(true,"Friend request accepted."); //Return success message
			}
			else{
				return array(false,"Invalid friend request."); //Return failure message
			}
		}
	}
	public function friendAdd($id){ //Add a user as a friend by their ID
		if($_SESSION['id']){
				$id = $this->secure($id);
				$uid = $this->secure($_SESSION['id']);
				if($id == $_SESSION['id']){ //Check to see if the user is adding themself as a friend
					return array(false,"You cannot add yourself as a friend."); //Return a warning
				}
				else{
					$select = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
					$check = mysql_num_rows($select);
					if($check > 0){
						$select_request = mysql_query("SELECT `status` FROM `friends` WHERE '$uid' IN (uid,fid) AND '$id' IN (uid,fid) AND `uid` != `fid` LIMIT 1");
						$request_check = mysql_num_rows($select_request); //Check to make sure they're not already friends
						if($request_check > 0){
							$request_status = mysql_fetch_array($select_request);
							if($request_status[0] > 0){
								return array(false,"You are already friends with this user."); //If they are, return a warning
							}
							else{
								return array(false,"You already have a friend request pending with this user."); //Maybe they aren't, but have already sent a friend request.
							}
						}
						else{
							mysql_query("INSERT INTO `friends` (`uid`,`fid`) VALUES('$uid','$id')"); //Create the friend request
							mysql_query("INSERT INTO `friendsarchive` (`uid`,`fid`) VALUES('$uid','$id')");
							return array(true,"Friend request sent.");
						}
					}
					else{
						return array(false,"Could not find user."); //If the ID was invalid, return an error
					}
				}
			}
		}
	public function friendCheck($uid,$fid){ //Perform a check to see if 2 users are friends (useful for privacy settings 
		$uid = $this->secure($uid);
		$fid = $this->secure($fid);
		$select = mysql_query("SELECT * FROM `friends` WHERE '$uid' IN (`uid`,`fid`) AND '$fid' IN (`uid`,`fid`) LIMIT 1");
		$check = mysql_num_rows($select);
		if($check > 0){
			return 1;
		}
		else{
			return 0;
		}
	}
	public function friendDecline($rid){ //Decline a friend request from a friend request ID
		if($_SESSION['id']){
			$rid = $this->secure($rid);
			$uid = $this->secure($_SESSION['id']);
			$select = mysql_query("SELECT * FROM `friends` WHERE `rid` = '$rid' AND `fid` = '$uid' AND `status` = '0' LIMIT 1");
			$check = mysql_num_rows($select); //Request the friend request details from the database, and make sure that the friend request was meant for the user trying to decline it
			if($check > 0){
				mysql_query("DELETE FROM `friends` WHERE `rid` = '$rid' LIMIT 1"); //Delete the request from the database
				return array(true,"Friend request declined."); //Return success message
			}
			else{
				return array(false,"Invalid friend request."); //Return a failure message
			}
		}
	}
	public function friendList(){ //Return an array of the current user's friends
		if($_SESSION['id']){
			$uid = $this->secure($_SESSION['id']);
			$select = mysql_query("SELECT * FROM friends AS F, users AS U WHERE '$uid' IN (F.uid, F.fid) AND U.id IN (F.uid, F.fid) AND U.id != '$uid' ORDER BY F.status ASC");
			$check = mysql_num_rows($select); //Get all friends of the current user, then order the non-confirmed (pending) friends first, then order by who's online, and lastly alphabetically
			if($check > 0){
				$friends = array();
				while($list = mysql_fetch_array($select,MYSQL_ASSOC)){
					$friends[] = $list;
				}
				return array(true,$friends); //Return the array on success
			}
			else{
				return array(false,"No friends available."); //Return an warning if it could not find friends
			}
		}
	}
	public function friendNum(){ //Returns the number of friends the current user has, good for use with friendList()
		if($_SESSION['id']){
			$uid = $this->secure($_SESSION['id']);
			$select = mysql_query("SELECT `rid` FROM `friends` WHERE `status` = '1' AND `uid` = '$uid' OR `fid` = '$uid'");
			$num = mysql_num_rows($select);
			return $num;
		}
	}
	public function friendRemove($id){ //Remove a friend by ID
		if($_SESSION['id']){
			$id = $this->secure($id);
			$uid = $this->secure($_SESSION['id']);
			$select = mysql_query("SELECT * FROM `friends` AS F, `users` AS U WHERE '$uid' IN (F.uid, F.fid) AND '$id' IN (F.uid, F.fid) AND U.id != '$uid' LIMIT 1");
			$check = mysql_num_rows($select); //Check to make sure these users are friends
			if($check > 0){ //If they are, remove the data that links them
				mysql_query("DELETE FROM `friends` WHERE '$uid' IN (`uid`,`fid`) AND '$id' IN (`uid`,`fid`) LIMIT 1");
				$friend = mysql_fetch_array($select);
				if($friend['status'] > 0){
					return array(true,"Friend successfully removed."); //Return a message on success
				}
				else{
					return array(true,"Friend request canceled."); //Return an alternative message if the user removed the friend before they accepted the friend request, synonymous to canceling the friend request
				}
			}
			else{
				return array(false,"Invalid friend id."); // Return a message on error
			}
		}
	}
	public function friendRequestNum(){ //Similar to friendNum, get the number of pending friend requests for the current user
		if($_SESSION['id']){
			$uid = $this->secure($_SESSION['id']);
			$select = mysql_query("SELECT `rid` FROM `friends` WHERE `status` = '0' AND `fid` = '$uid'");
			$num = mysql_num_rows($select);
			return $num;
		}
	}
	public function userID($input){ //Returns a user's ID corresponding to either their username or email
		$input = $this->secure($input);
		if(strpos($input,"@")){
			$select = mysql_query("SELECT `id` FROM `users` WHERE `email` = '$input' LIMIT 1");
		}
		else{
			$select = mysql_query("SELECT `id` FROM `users` WHERE `username` = '$input' LIMIT 1");
		}
		$check = mysql_num_rows($select);
		$id = mysql_fetch_array($select);
		if($check > 0){
			return array(true,$id[0]); //Return the id of the user on success
		}
		else{
			return array(false,"Invalid email or username."); //Return a message on error
		}
	}

	public function userUsername($input){ 
		$input = $this->secure($input);
		$select = mysql_query("SELECT `username` FROM `USERS` WHERE `id` = '$input' LIMIT 1");
		$check = mysql_num_rows($select);
		$id = mysql_fetch_array($select);
		if($check > 0){
			return $id[0]; //Return the id of the user on success
		}
		else{
			return array(false,"Invalid email or username."); //Return a message on error
		}
	}
	public function userInfo($uid){ 
		$uid = $this->secure($uid);
		$select = mysql_query("SELECT ID, username, email, oauth_uid,Rating FROM `USERS` WHERE `ID` = '$uid' LIMIT 1");
		$check = mysql_num_rows($select);
		if($check > 0){
			while($list = mysql_fetch_array($select,MYSQL_ASSOC)){
				$profile[] = $list;
			}
			return $profile[0]; //Return the search results as an array upon success
		}
		else{
			return false; //Returns a message on error
		}
	}
	public function userSearch($username){ //Search for a user by username
		$username = $this->secure($username);
		$select = mysql_query("SELECT * FROM `users` WHERE `username` LIKE '%$username%' LIMIT 30"); //Return a max of 30 similar results
		$check = mysql_num_rows($select);
		if($check > 0){
			$users = array();
			while($list = mysql_fetch_array($select,MYSQL_ASSOC)){
				$users[] = $list;
			}
			return array(true,$users); //Return the search results as an array upon success
		}
		else{
			return array(false,"User not found."); //Returns a message on error
		}
	}
	public function userProfile($uid){ 
		$uid = $this->secure($uid);
		$select = mysql_query("SELECT * FROM users WHERE id = '$uid' LIMIT 1"); 
		$check = mysql_num_rows($select);
		if($check > 0){
			$profile = mysql_fetch_array($select);
			return $profile; //Return the information from the profile in an array on success
		}
		else{
			return false; //Returns a message on error
		}
	}
	public function friendsChat(){ 
		$time = time();
		if($_SESSION['id']){
			$user = $this->secure($_SESSION['id']);
			$select = mysql_query("
			SELECT u.id, u.username FROM
			friends f, users u
			WHERE 
			".$user." IN (f.uid, f.fid) AND 
			u.id IN (f.uid, f.fid) AND 
			u.id != ".$user." 
			ORDER BY f.status ASC
			"); 
			$check = mysql_num_rows($select);
			if($check > 0){
				$users =  mysql_fetch_array($select);
				return array($users); //Return the search results as an array upon success
			}
			else{
				return false; //Returns a message on error
			}
		}
	}
	public function usersOnlineChat(){ 
		$time = time();
		if($_SESSION['id']){
			$user = $this->secure($_SESSION['id']);
			$select = mysql_query("SELECT u.id,u.username FROM users u, checkins c WHERE c.time > ".$time." and c.uid = u.id and u.id !=".$user); 
			$check = mysql_num_rows($select);
			if($check > 0){
				$users =  mysql_fetch_array($select);
				return array($users); //Return the search results as an array upon success
			}
			else{
				return false; //Returns a message on error
			}
		}
	}
	
	public function usersOnlineC($class){ 
		$time = time();
			$class = $this->secure($class);
			$select = mysql_query("
			SELECT u.id, u.username
			FROM
			(SELECT ca.uid FROM classarchive ca, checkins c 
			WHERE ca.classid = ".$class."
			AND ca.uid = c.uid
			AND c.time > ".$time."
			) o, users u
			WHERE u.id = o.uid	
			GROUP BY u.id
			"); 
			$check = mysql_num_rows($select);
			if($check > 0){
				while($list = mysql_fetch_array($select,MYSQL_ASSOC)){
					$users[] = $list;
				}
				return $users;
			}
			else{
				return false; //Returns a message on error
			}
	}
		
	public function usersOnline(){ 
		$time = time();
			$select = mysql_query("SELECT u.id,u.username FROM users u, checkins c WHERE c.time > ".$time." and c.uid = u.id"); 
			$check = mysql_num_rows($select);
			if($check > 0){
				while($list = mysql_fetch_array($select,MYSQL_ASSOC)){
					$users[] = $list;
				}
				return $users;
			}
			else{
				return false; //Returns a message on error
			}
	}
	public function chatMessages($MSGuser) {
		if(isset($_SESSION['id'])){
			$MSGuser = $this->secure($MSGuser);
			$user = $this->secure($_SESSION['id']);
			if($MSGuser == 1){
			$select = mysql_query("SELECT u.username, x.message, x.date, u.id
			FROM users u, (
			SELECT toid, fromid, message, DATE
			FROM chat
			WHERE toid =1
			)x
			WHERE x.fromid = u.id
			ORDER BY  `x`.`date` ASC 
			LIMIT 0 , 30");
			}
			else{
			$select = mysql_query("SELECT u.username, x.message, x.date, u.id
			FROM users u, (
			SELECT toid, fromid, message, DATE
			FROM chat
			WHERE (
			toid = '$user'
			AND fromid = '$MSGuser'
			)
			OR (
			toid = '$MSGuser'
			AND fromid = '$user' )
			)x
			WHERE x.fromid = u.id
			ORDER BY  `x`.`date` ASC 
			LIMIT 0 , 30");
			}
			$check = mysql_num_rows($select);
			if($check > 0){
				while($list = mysql_fetch_array($select,MYSQL_ASSOC)){
					$msgs[] = $list;
				}
				return $msgs;
			}
		}
		else {
			return array(false,"Make sure you are logged in");
		}
	}
	public function chatSend($ToUser,$MSG) {
		if(isset($_SESSION['id'])){
			$today = date('Y-m-d H:i:s');
			$ToUser = $this->secure($ToUser);
			$FromUser = $this->secure($_SESSION['id']);
			$MSG = $this->secure($MSG);
			$select = mysql_query("INSERT INTO chat (toid, fromid, message,date) VALUES ('$ToUser', '$FromUser', '$MSG','$today')");
			if($select){
				return true;
			}
			
		}
		else {
			return "Make sure you are logged in";
		}
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