<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/cyspell/framework/config.php');

if(isset( $_SESSION["correct"] )){$_SESSION["correct"] = 0; }
if(isset( $_SESSION["problems"] )){$_SESSION["problems"] = 5; }



?>
<html>
<head>
<title>CySpell</title>
<link rel="stylesheet" href="css/styles.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="js/jquery.transit.min.js"></script>
<script src="js/javascript.js"></script>

<script>
$(document).ready(function(){
	$('#gradelist a').bind('mouseenter',function(){
		$(this).transition({backgroundColor: 'yellow',color: 'limegreen',border: '2px solid yellow', queue: false},150);
		$(this).bind('mouseout',function() {
			$(this).transition({backgroundColor: '',color: 'white',border: '2px solid white', queue: false},150);
			$(this).unbind('mouseout');
			return false;
		});
		return false;
	});
});

</script>
</head>
<body>
	<div id="banner">
		<?php include("profile.php"); ?>
	</div>
	
	<div id="map">
		<div id="gamelevels">
			<div id="k" class="grade">
				<ul id="kinderlist" class="gradelist">
					<li id="11" class="level"><a href="index.php?level=11"><img src="images/1.png" height="35" width="35"></a></li>
					<li id="12" class="level"><a href="index.php?level=12"><img src="images/2.png" height="35" width="35"></a></li>
					<li id="13" class="level"><a href="index.php?level=13"><img src="images/3.png" height="35" width="35"></a></li>
					<li id="14" class="level"><a href="index.php?level=14"><img src="images/4.png" height="35" width="35"></a></li>
					<li id="15" class="level"><a href="index.php?level=1t">TestOut</a></li>
				</ul>
			</div>
			<div id="1st" class="grade">
				<ul id="1stlist" class="gradelist">
					<li id="21" class="level"><a href="index.php?level=21"><img src="images/1.png" height="35" width="35"></a></li>
					<li id="22" class="level"><a href="index.php?level=22"><img src="images/2.png" height="35" width="35"></a></li>
					<li id="23" class="level"><a href="index.php?level=23"><img src="images/3.png" height="35" width="35"></a></li>
					<li id="24" class="level"><a href="index.php?level=24"><img src="images/4.png" height="35" width="35"></a></li>
					<li id="25" class="level"><a href="index.php?level=2t">TestOut</a></li>
				</ul>
			</div>
			<div id="2nd" class="grade">
				<ul id="2ndlist" class="gradelist">
					<li id="31" class="level"><a href="index.php?level=31"><img src="images/1.png" height="35" width="35"></a></li>
					<li id="32" class="level"><a href="index.php?level=32"><img src="images/2.png" height="35" width="35"></a></li>
					<li id="33" class="level"><a href="index.php?level=33"><img src="images/3.png" height="35" width="35"></a></li>
					<li id="34" class="level"><a href="index.php?level=34"><img src="images/4.png" height="35" width="35"></a></li>
					<li id="35" class="level"><a href="index.php?level=3t">TestOut</a></li>
				</ul>
			</div>
			<div id="3rd" class="grade">
				<ul id="3rdlist" class="gradelist">
					<li id="41" class="level"><a href="index.php?level=41"><img src="images/1.png" height="35" width="35"></a></li>
					<li id="42" class="level"><a href="index.php?level=42"><img src="images/2.png" height="35" width="35"></a></li>
					<li id="43" class="level"><a href="index.php?level=43"><img src="images/3.png" height="35" width="35"></a></li>
					<li id="44" class="level"><a href="index.php?level=44"><img src="images/4.png" height="35" width="35"></a></li>
					<li id="45" class="level"><a href="index.php?level=4t">TestOut</a></li>
				</ul>
			</div>
			<div id="4th" class="grade">
				<ul id="4thlist" class="gradelist">
					<li id="51" class="level"><a href="index.php?level=51"><img src="images/1.png" height="35" width="35"></a></li>
					<li id="52" class="level"><a href="index.php?level=52"><img src="images/2.png" height="35" width="35"></a></li>
					<li id="53" class="level"><a href="index.php?level=53"><img src="images/3.png" height="35" width="35"></a></li>
					<li id="54" class="level"><a href="index.php?level=54"><img src="images/4.png" height="35" width="35"></a></li>
					<li id="55" class="level"><a href="index.php?level=5t">TestOut</a></li>
				</ul>
			</div>
		</div>
		
	<div id="taskmap">
			<div id="level" class="task">
				<ul id="firstcol" class="tasklist">
					<li id="11" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
					<li id="12" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
					<li id="13" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
					<li id="14" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
				</ul>
			</div>
			<div id="inrow" class="task">
				<ul id="firstcol" class="tasklist">
					<li id="11" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
					<li id="12" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
					<li id="13" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
					<li id="14" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
				</ul>
			</div>
			<div id="misses" class="task">
				<ul id="firstcol" class="tasklist">
					<li id="11" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
					<li id="12" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
					<li id="13" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
					<li id="14" class="taskitem"><a href=""><img src="images/1.png" height="35" width="35"></a></li>
				</ul>
			</div>
		</div>
	
		
		
		
		
		
		
	</div>
	
	<div id="game">
		<?php
			include("exercises.php");
 		?>
	</div>

</body>
</html>


<script>
    var xdata = <?php echo json_encode($xdata); ?>;
	
	$( '.level' ).each(function( index ) {
			$(this).css( "opacity", "1" );
			$(this).css( "filter", "alpha(opacity=100)" );
		if( $(this).is( "#"+xdata['userlevel']) ){
			return false;
		}
	});



</script>
