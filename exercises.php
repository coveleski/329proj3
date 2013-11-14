<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . '/cyspell/framework/config.php');

if(!isset( $_SESSION["correct"] )){$_SESSION["correct"] = 0; }
if(!isset( $_SESSION["problems"] )){$_SESSION["problems"] = 5; }
if(!isset( $_SESSION["level"] )){$_SESSION["level"] = 11; }


if(isset( $_GET["level"] ) && $_GET["level"] != ""){ $level = $_GET["level"]; }          else { $level = $_SESSION["level"]; }
if(isset( $_GET["correct"] ) && $_GET["correct"] != ""){ $correct = $_GET["correct"]; }    else { $correct = $_SESSION["correct"]; }

$updated="0";


	
if(isset($_GET["problems"]) &&  isset($_GET['level'])  &&   isset( $_GET["correct"] )  ){
	
	if($correct){ $_SESSION['correct'] = $_SESSION['correct'] + 1; }
	if($_SESSION["problems"] == 0){
	echo("updated");
		if($_SESSION['correct'] >= 5){
			
			echo("updated");
			$updated = $system->updateUserLevel(1);
			
		}
		$_SESSION['problems'] = 5;
		$_SESSION['correct'] = 0;
			
	}
	$_SESSION['problems'] = $_SESSION['problems'] - 1;

}

if($updated) {  $updated = "1";  }
	
     $xdata = array(
          'userlevel'    => "".$_SESSION['level'],
          'numcorrect'    => 'bar',
          'refresh'       => "".$updated,
          'wordlist' => array('green','blue')

     );
     
     
     
     
    $randfile = random_pic();
    $info = pathinfo($randfile);
	$file_name =  basename($randfile,'.'.$info['extension']);
     
     
	function random_pic($dir = 'images/Grade1Images')
	{
	    $files = glob($dir . '/*.*');
	    $file = array_rand($files);
	    
	    return $files[$file];
	}

?>


	<div id="imageDIV">	
		<?php      echo("For Testing, the correct value is: ".$file_name); ?>
		<br>
		<img id="image" border="0" src="<?php echo($randfile); ?>"  width="300" height="225">
	</div>
	
	<div id="formstuff">
	
		<div class="exercisetext">
			<br>
			<?php
			if($level > $_SESSION['level']){                           // check to see if a new level is too high for the user
				echo("Nice try! still, ");
				$level = $_SESSION['level'];
			}


			
			switch ($level)
			{
			    case 11: echo "Level 1 of Kindergarten";break;
			    case 12: echo "Level 2 of Kindergarten";break;
			    case 13: echo "Level 3 of Kindergarten";break;
			    case 14: echo "Level 4 of Kindergarten";break;
			    case 21: echo "Level 1 of 1st grade";break;
			    case 22: echo "Level 2 of 1st grade";break;


			    break;
			}
			
			
			?>
			<br>
			
			<?php echo($_SESSION["problems"]);?> exercises left.

			
			 <br>

			<?php echo($_SESSION['correct']);?> exercises correct.

		</div>

		<span class="exercisetext">What is this?</span>
		
		<div id="alertArea" style="display: none;"></div>
		<div id="formDIV">
			<form id="Spellform" method="get">
				<input type="hidden" id="problems" value="<?php echo($_SESSION["problems"]);?>" >
				<input type="hidden" id="level" value="<?php echo($level);?>" >
				<input type="hidden" id="imagename" value="<?php echo($file_name);?>" >
				<input id="spellhere" type="text" name="answer" autocomplete="off"><br>
				<a class="Button" id="submitword">Submit</a>
			</form>
		</div>
		
	</div>
	
	
<script type="text/javascript">

    var xdata = <?php echo json_encode($xdata); ?>;
    
   // alert(xdata['refresh']);

	if(xdata['refresh'] == "1"){   location.reload();  }
	
	$(document).keypress(function(e) {
    	if(e.which == 13) {
    	    var spelling = $("#spellhere").val();
			var problems = $("#problems").val();
			var level    = $("#level").val();
			var imagename = $("#imagename").val();
			
			if (spelling == "") {
				$("#alertArea").html("<ul><li><center>Something went wrong.</center></li></ul>");
				$("#alertArea").show();
			}
			else if(spelling.toLowerCase() == imagename.toLowerCase()){
				testWord(problems,level, 1);
			} else{
				testWord(problems, level, 0);    
			}
			return false; 
    	}
	});

	$("#submitword").click(function() {
		var spelling = $("#spellhere").val();
		var problems = $("#problems").val();
		var level    = $("#level").val();
		var imagename = $("#imagename").val();

		if (spelling == "") {
			$("#alertArea").html("<ul><li><center>Something went wrong.</center></li></ul>");
			$("#alertArea").show();
		}
		else if(spelling.toLowerCase() == imagename.toLowerCase()){
			testWord(problems, level, 1);
		} else{
			testWord(problems, level,0);    
		}
	});



</script>