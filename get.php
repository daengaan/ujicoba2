<?php 
  //This page is used to receive the data sent by flash player and save them to the database. Do not open this page in Internet Explorer.
	if ($_POST["fromQuiz"] != "true") {
	  echo "No data are received from quiz player!";
		exit();
	}

  include_once("conn.php");
	//The following lines are used to receive the data from quiz player...
  $quizId     = $_POST["quizId"];
	$quizTitle  = $_POST["quizTitle"];
	$userName   = $_POST["USER_NAME"];
	$userMail   = $_POST["USER_MAIL"];
	$userId     = $_POST["sid"];
	$userScore  = $_POST["sp"];
	$totalScore = $_POST["totalScore"];
	$passScore  = $_POST["ps"];
	$passState  = ($_POST["passState"] == "True") ? 1 : 0;
	$strResults = $_POST["quesInfo"];
	$strResults = addslashes($strResults);
	$postDate   = $_POST["postDate"];
	
	//Adding data to the database...
	$sql = "INSERT INTO `quiz` (`id`, `quizId`, `quizTitle`, `userName`, `userMail`, `userId`, `userScore`, `totalScore`, `passScore`, `passState`, `Result`, `userDate`, `addDate`) ";
	$sql = $sql." VALUES (null, '".$quizId."', '".$quizTitle."', '".$userName."', '".$userMail."', '".$userId."', '".$userScore."', '".$totalScore."', '".$passScore."', '".$passState."', '".$strResults."', '".$postDate."', NOW())";
	$rs = mysql_query($sql);
	
	//Feedback for the flash player...
	if ($rs) {
	  echo "feedMsg=Data has been posted successfully";	
  }
	else {
	  echo "feedMsg=Failed to post data to database";
	}
?> 