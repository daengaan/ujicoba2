<?php
	//When you see this line in your Web browser, it means your web server does not support php, our php pages can not run on it. 
  //Administration page
  include_once("conn.php");
	
  if ($_SESSION["admin"] == "") {
	  goto_url("login.php");
  }
	
  $strInfo = trim($_REQUEST["info"]);
  if ($strInfo == "") {
	  $strUrl = "";
  } 
  else {
	  $strUrl = "&info=".urlencode($strInfo);
  }
  $delStr = trim($_REQUEST["del"]);

  if (is_numeric($delStr)) { 
	  mysql_query("delete from `quiz` where `id`='".$delStr."'", $conn);
	  goto_url("index.php?page=".$_REQUEST['page'].$strUrl);
  }
  elseif ($delStr == "true") { //delete all selected records
	  $delIds = implode(",", $_REQUEST["quiz"]);
	  mysql_query("delete from `quiz` where `id` in (".$delIds.")", $conn);
		goto_url("index.php");
  }
  elseif ($delStr == "all") { //clear all records
	  if ($strInfo == "") {
      mysql_query("delete from `quiz`", $conn);
	  } 
	  else {
	    mysql_query("delete from `quiz` where `quiztitle` like '%".$strInfo."%' or `userId` like '".$strInfo."'", $conn);
	  }
    goto_url("index.php");
  }
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html">
<link rel="stylesheet" type="text/css" href="style.css">
<title>Quiz Result Management Panel</title>
</head>

<script language="javascript">
<!--
  //checkbox select all
	function checkall(form, prefix, checkall) {
		var checkall = checkall ? checkall : 'chkall';
		for(var i = 0; i < form.elements.length; i++) {
			var e = form.elements[i];
			if(e.name != checkall && (!prefix || (prefix && e.name.match(prefix)))) {
				e.checked = form.elements(checkall).checked;;
			}
		}
	}
 
  function delAll() {
    var hasChecked = false;
    for (var i = 0; i <= document.getElementsByName('quiz[]').length - 1; i++) {
      if (document.getElementsByName('quiz[]')[i].checked) {
        hasChecked = true;
        break;
      }
    }
		
	  if (!hasChecked) {
      alert('No record has been selected!');
    }  
    else if (confirm('Do you want to delete all the selected records?')) {
      document.frmQuiz.submit(); 
    } 
  }
  
  function clearAll() {
    if (confirm('Do you want to clear all the records?')) {
      window.location.href = 'index.php?del=all&info=<?=urlencode($strInfo)?>';
    }  
  }
  
  function showQuiz(rst) {
    if (document.all(rst).style.display == 'none') {
      with (document.all('showrst')) {
        title = 'hide';
        innerHTML = '&lt;&lt;';
      }
      document.all(rst).style.display = 'block';
    }
    else {
      with (document.all('showrst')) {
        title = 'show';
        innerHTML = '&gt;&gt;';
      }
      document.all(rst).style.display = 'none';
    }
  }
  
  function openDialog() {
    if (window.showModalDialog != null) {  //ie
      window.showModalDialog("chgpwd.php", "admin", "dialogWidth=470px; dialogHeight=175px; status=no; help=no; scroll=no"); 
    }
    else {
		  var x= parseInt((screen.width - 470) / 2);
      var w = window.open("chgpwd.php", "admin", "modal=yes, left=" + x + ",top=125, width=470px, height=175px, status=no, help=no, scroll=no"); 
      w.focus();
    }
  }
-->
</script>
<body topmargin="8">
<center>
<table width="790px"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="35%"><a href="index.php" title="Index" style="color:#069;font:16px Tahoma, Verdana;font-weight:bold;float:left">Quiz Result Management Panel </a></td>
    <td width="65%" align="right"><font face="Arial" color="#006699" size="2"><b><?=$_SESSION["admin"]?></b></font>&nbsp;&nbsp;<a href="login.php?act=logout">Logout</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="adduser.php" target="_blank">&nbsp;<a href="adduser.php" target="_blank">Create User Accounts</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="javascript: openDialog(); return(false)">Modify the administrator's password</a></td>
  </tr>
</table>
<table border="1" width="790" cellpadding="0" cellspacing="0" bordercolor="#88BBDD" style="border-collapse: collapse">
  <form name="frmQuiz" method="POST" action="index.php?del=true">
  	<tr bgcolor="#6BABD6">
    <td><input type="checkbox" name="chkall" value="1" title="Selected all" onClick="checkall(this.form, 'quiz')" style="cursor: hand"></td>
    <td>No. </td>
    <td>Title </td>
    <td>User ID </td>
    <td>Score </td>
    <td>Full Score  </td>
    <td>Passing Score </td>
    <td>Passing Status </td>
    <td>Delete </td>
    <td>Date </td><u></u>
  </tr>
  <?php
		$curPage = $_GET['page'] ? $_GET['page'] : "1";
	  $pageSize = 16;
		if ($strInfo != "") { 
			$sql = "select `id` from `quiz` where `quiztitle` like '%".$strInfo."%' or `userId` like '%".$strInfo."%'";
		}
		else {
			$sql = "select `id` from `quiz`";
		}
		$rs = mysql_query($sql, $conn);
		$num = mysql_num_rows($rs);
		$pageCount = ceil($num / $pageSize);
		if ($curPage > $pageCount) {
		  $curPage = $pageCount;
	  }
			
		$start = ($curPage - 1) * $pageSize;
		if ($strInfo != "") {
		  $sql = "select * from `quiz` where `quizTitle` like '%".$strInfo."%' or `userId` like '%".$strInfo."%' order by `adddate` desc limit $start, $pageSize";
    }			
		else {
  		$sql = "select * from `quiz` order by `addDate` desc limit $start, $pageSize";
		}
		$rs = mysql_query($sql, $conn);
		if (!$rs) {
		  $pageSize = 0;
		}

		for ($i = 0; $i < $pageSize; $i++) {
			$row = mysql_fetch_array($rs, MYSQL_ASSOC);
			if (!$row) {
			  break;
		  }
			
			$recNo = $pageSize * ($curPage - 1) + $i + 1;
  ?>
  <tr class="row">
  	<td><input type="checkbox" name="quiz[]" value="<?=$row['id']?>"> </td>
  	<td><?php if($row['passState']) echo "<span style=\"color:#080\">".$recNo."</span>";else echo "<span style=\"color:#F00\">".$recNo."</span>"?> </td>
    <td><a href="index.php?page=<?=$curPage?>&amp;id=<?=$row['id']?><?=$strUrl?>"><?=str_replace($strInfo, "<span style=\"color:#F00\">$strInfo</span>", $row['quizTitle'])?></a> </td>
    <td><a href="mailto:<?=$row['userMail']?>" title="Send an email to <?=$row['userId']?>"><?=str_replace($strInfo, "<span style=\"color:#F00\">$strInfo</span>", $row['userId'])?></a> </td>
    <td><?=$row['userScore']?> </td>
    <td><?=$row['totalScore']?> </td>
    <td><?=$row['passScore']?> </td>
    <td><?php if ($row['passState']) echo "<span style=\"color:#080\">Pass</span>"; else echo "<span style=\"color:#F00\">Fail</span>"?> </td>
    <td><a href="index.php?del=<?=$row['id']?>&amp;page=<?=$curPage ?><?=$strUrl?>" onClick="javascript: return confirm('Do you want to delete this record?')">Delete</a> </td>
    <td><?=$row['addDate']?> </td>
  </tr>
  <?php
	  }
  ?>
  </form>
  <tr>
    <td colspan="6" align="left" bgcolor="#B1D2E9">
      <a href="#" onclick="javascript: delAll()">Delete selected</a>&nbsp;&nbsp;&nbsp;
      <a href="#" onclick="javascript: clearAll()">Clear all records</a>&nbsp;&nbsp;&nbsp;
      <font color="#008000">Records count: [<?=$num ?>]</font>
    </td>
    <td colspan="4" align="right" bgcolor="#B1D2E9">    
      <?php 
			  if ($curPage > 1) {
			?>
      <a href="index.php?page=1<?=$strUrl ?>">[First page]</a>&nbsp;&nbsp;&nbsp; 
      <a href="index.php?page=<?=$curPage-1 ?><?=$strUrl ?>">[Previous page]</a>&nbsp;&nbsp;&nbsp; 
      <?php
        }
        if ($curPage < $pageCount) {
      ?>
      <a href="index.php?page=<?=$curPage + 1 ?><?=$strUrl ?>">[Next page]</a>&nbsp;&nbsp;&nbsp; 
      <a href="index.php?page=<?=$pageCount ?><?=$strUrl ?>">[Last page]</a>
     <?php
        }
      ?>
    </td>
  </tr>
  <form name="frmSearch" method="GET" action="index.php">
  <tr>
    <td colspan="10" align="left" bgcolor="#B1D2E9">
      Enter a title or userID to search:&nbsp; 
      <input type="text" name="info" size="35" value="<?=$strInfo ?>">&nbsp; <input type="submit" value="search"></td>
  </tr>
	</form>
</table>
<?
  if ($_GET['id'] != "") {
    $sql = "select `Result`, `userDate` from `quiz` where `id`='".$_GET['id']."'";
	  $rs = mysql_query($sql, $conn);
		$row = mysql_fetch_array($rs, MYSQL_ASSOC);
?>
<br>
<table width="790" style="margin:0px;padding:0px;border:1px solid;;border-collapse: collapse;border-color:#88BBDD"> 
  <tr class="row">
    <td bgcolor="#6BABD6">Result:</td>
  </tr>
  <tr>
    <td><?=stripslashes($row['Result'])?> <br />====<br /> User's computer time: <?=$row['userDate']?></td>
  </tr>
</table>

<?
	}
?> 
</center>

</body>

</html>