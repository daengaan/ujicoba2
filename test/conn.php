<?php
	error_reporting(E_ALL ^E_NOTICE);
	session_start();
	
	//you can define you database (used to collect the test results) name here. The default name is "db_quiz"
	define("QUIZ_DB_NAME", "db_quiz");
	
	/*
	  connect to database - you can change params here to connect to your MySQL database.
	  Params:
		  $db_hostName: The host name or IP address of your MySQL server 
		  $db_userName: The user accounts of your MySQL database 
			$db_passWord: The user password of your MySQL database 
	*/
	$db_hostName = "localhost";
	$db_userName = "root";
	$db_passWord = "";
  $conn = mysql_connect($db_hostName, $db_userName, $db_passWord) or die("Failed to connect database");
	
	//Check database - The following lines are used to check if you have created the database or not.
	$dblist = mysql_list_dbs($conn);
	$dbcount = mysql_num_rows($dblist);
	$hasQuizDB = false;
	for ($i = 0; $i < $dbcount; $i++) {
	  if (mysql_tablename($dblist, $i) == QUIZ_DB_NAME) {
		  $hasQuizDB = true;
			break;
		}
	}
	mysql_free_result($dblist);
	
	if (!$hasQuizDB) {
	  //Create database - If you have not created a database to collect the test results, the following lines will be excuted to create one.
		mysql_create_db(QUIZ_DB_NAME, $conn) or die("Create database ".QUIZ_DB_NAME." fail...");
    mysql_select_db(QUIZ_DB_NAME, $conn) or die("Unable to access database ".QUIZ_DB_NAME);
	}
	else {
    mysql_select_db(QUIZ_DB_NAME, $conn) or die("Unable to access database ".QUIZ_DB_NAME);
	}
	
	//Check tables - The following lines are used to check if you have created the table or not.
	function has_table($tblName) {
    $rs = mysql_list_tables(QUIZ_DB_NAME);
    while ($row = mysql_fetch_row($rs)) {
      if ($row[0] == $tblName) {
			  return true;
		  }
		}
		
		return false;
	}
		
  if (!has_table("quiz")) {
    //Create quiz table - If you have not created a table to collect the test results, the following lines will be excuted to create one.
		$sql  = "create table `quiz` (";
		$sql .= "`id` int(11) not null auto_increment, ";
		$sql .=	"`quizId` varchar(255), ";
		$sql .=	"`quizTitle` varchar(255), ";
		$sql .=	"`userName` varchar(255), ";
		$sql .=	"`userMail` varchar(255), ";
		$sql .=	"`userId` varchar(255), ";
		$sql .=	"`userScore` float, ";
		$sql .=	"`totalScore` float, ";
		$sql .=	"`passScore` float, ";
		$sql .=	"`passState` tinyint(4), ";
		$sql .=	"`Result` mediumtext, ";
		$sql .=	"`userDate` varchar(50), ";
		$sql .=	"`addDate` datetime, ";
		$sql .=	"primary key (`id`) ";
		$sql .=	") engine=myisam; ";
		mysql_query($sql, $conn) or die("Creating table quiz failed...");
	}
		
	if (!has_table("admin")) {
		//Create admin table - the following lines are used to create the administrater's table...
		$sql  = "create table `admin` (";
		$sql .=	"`id` int(11) not null auto_increment, ";
		$sql .=	"`userId` varchar(255) not null, ";
		$sql .=	"`userPwd` varchar(255) not null, ";
		$sql .=	"`sys` tinyint(1) not null, ";
		$sql .=	"`mdate` datetime not null, ";
		$sql .=	"primary key (`id`) ";
		$sql .=	") engine=myisam; ";
		mysql_query($sql, $conn) or die("Creating table admin failed...");

    //Initializing administration account...
    $sql = "insert into `admin` values (1, 'admin', '123456', 1, '2007-01-01 00:00:00')";
		mysql_query($sql, $conn) or die("Initializing admin data failed...");
	}
	
	if (!has_table("users")) {
		//Create users table - the following lines are used to create the users's table...
		$sql  = "create table `users` (";
		$sql .=	"`id` int(11) not null auto_increment, ";
		$sql .=	"`userId` varchar(255) not null, ";
		$sql .=	"`userPwd` varchar(255) not null, ";
		$sql .=	"primary key (`id`) ";
		$sql .=	") engine=myisam; ";
		mysql_query($sql, $conn) or die("Creating table users failed...");

    //Initializing administration account...
    $sql = "insert into `users` values (1, 'admin', '123456')";
		mysql_query($sql, $conn) or die("Initializing users data failed...");
	}

	
  function goto_url($url) {
    echo '<script language = "javascript">';
    echo '  window.location.href = "'.$url.'"';
    echo '</script>';
  }