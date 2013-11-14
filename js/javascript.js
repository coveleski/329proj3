                     
	function ajax(url, selector){   
		$.ajax({
		type: "GET",
		url: url,
		success: function(data){
			$(selector).html(data);
		}
		});
	}
            
	function testWord(problems,level,correct) {
		ajax("exercises.php?problems="+problems+"&level="+level+"&correct="+correct, "#game");
	}