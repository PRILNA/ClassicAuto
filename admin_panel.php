<?php require_once('Connections/classicauto.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Administrator";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form6")) {
  $updateSQL = sprintf("UPDATE userdetails SET Password=%s, Full_name=%s, DOB=%s, Reg_date=%s, Access_level=%s WHERE Username=%s",
                       GetSQLValueString($_POST['Password'], "text"),
                       GetSQLValueString($_POST['Full_name'], "text"),
                       GetSQLValueString($_POST['DOB'], "date"),
                       GetSQLValueString($_POST['Reg_date'], "date"),
                       GetSQLValueString($_POST['Access_level'], "text"),
                       GetSQLValueString($_POST['Username'], "text"));

  mysql_select_db($database_classicauto, $classicauto);
  $Result1 = mysql_query($updateSQL, $classicauto) or die(mysql_error());

  $updateGoTo = "admin_panel.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
    $updateGoTo .= "#userdetails";
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE web_messages SET Name=%s, E_mail=%s, Message=%s, Time_stamp=%s, Task_completed=%s WHERE Sl_no=%s",
                       GetSQLValueString($_POST['Name'], "text"),
                       GetSQLValueString($_POST['E_mail'], "text"),
                       GetSQLValueString($_POST['Message'], "text"),
                       GetSQLValueString($_POST['Time_stamp'], "date"),
                       GetSQLValueString($_POST['Task_completed'], "text"),
                       GetSQLValueString($_POST['Sl_no'], "int"));

  mysql_select_db($database_classicauto, $classicauto);
  $Result1 = mysql_query($updateSQL, $classicauto) or die(mysql_error());

  $updateGoTo = "admin_panel.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
    $updateGoTo .= "#customermessage";
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form5")) {
  $updateSQL = sprintf("UPDATE orders SET Name=%s, E_mail=%s, Phone=%s, Car_model=%s, Time_stamp=%s, Task_completed=%s WHERE Sl_no=%s",
                       GetSQLValueString($_POST['Name'], "text"),
                       GetSQLValueString($_POST['E_mail'], "text"),
                       GetSQLValueString($_POST['Phone'], "text"),
                       GetSQLValueString($_POST['Car_model'], "text"),
                       GetSQLValueString($_POST['Time_stamp'], "date"),
                       GetSQLValueString($_POST['Task_completed'], "text"),
                       GetSQLValueString($_POST['Sl_no'], "int"));

  mysql_select_db($database_classicauto, $classicauto);
  $Result1 = mysql_query($updateSQL, $classicauto) or die(mysql_error());

  $updateGoTo = "admin_panel.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
    $updateGoTo .= "#orders_recieved";
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO news_updates (sl_no, news_header, news_body_text, search_text, image_name, update_timestamp) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['sl_no'], "int"),
                       GetSQLValueString($_POST['news_header'], "text"),
                       GetSQLValueString($_POST['news_body_text'], "text"),
                       GetSQLValueString($_POST['search_text'], "text"),
                       GetSQLValueString($_POST['image_name'], "text"),
                       GetSQLValueString($_POST['update_timestamp'], "date"));

  mysql_select_db($database_classicauto, $classicauto);
  $Result1 = mysql_query($insertSQL, $classicauto) or die(mysql_error());

  $insertGoTo = "admin_panel.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
    $updateGoTo .= "#newsupdates";
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO car_details (Sl_no, Brand, Model, Model2, `Description`, Image_name, Fuel_type, Body_type, Engine_displacement, Transmission, No_of_gears, Power, Torque, Mileage_city, Drive_type, Top_speed, Fuel_capacity_litre, Seating_capacity, Colours, Price, Air_conditioning, Power_streering, Navigation_system, Video_system, Sunroof, Leather_seats, Electronic_mirrors, Central_locking, Anti_lock_breaking_system, Airbag, Parking_sensors, Traction_control, Length, Width, Height, Ground_clearance) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Sl_no'], "int"),
                       GetSQLValueString($_POST['Brand'], "text"),
                       GetSQLValueString($_POST['Model'], "text"),
                       GetSQLValueString($_POST['Model2'], "text"),
                       GetSQLValueString($_POST['Description'], "text"),
                       GetSQLValueString($_POST['Image_name'], "text"),
                       GetSQLValueString($_POST['Fuel_type'], "text"),
                       GetSQLValueString($_POST['Body_type'], "text"),
                       GetSQLValueString($_POST['Engine_displacement'], "text"),
                       GetSQLValueString($_POST['Transmission'], "text"),
                       GetSQLValueString($_POST['No_of_gears'], "int"),
                       GetSQLValueString($_POST['Power'], "text"),
                       GetSQLValueString($_POST['Torque'], "text"),
                       GetSQLValueString($_POST['Mileage_city'], "text"),
                       GetSQLValueString($_POST['Drive_type'], "text"),
                       GetSQLValueString($_POST['Top_speed'], "text"),
                       GetSQLValueString($_POST['Fuel_capacity_litre'], "text"),
                       GetSQLValueString($_POST['Seating_capacity'], "int"),
                       GetSQLValueString($_POST['Colours'], "text"),
                       GetSQLValueString($_POST['Price'], "int"),
                       GetSQLValueString($_POST['Air_conditioning'], "text"),
                       GetSQLValueString($_POST['Power_streering'], "text"),
                       GetSQLValueString($_POST['Navigation_system'], "text"),
                       GetSQLValueString($_POST['Video_system'], "text"),
                       GetSQLValueString($_POST['Sunroof'], "text"),
                       GetSQLValueString($_POST['Leather_seats'], "text"),
                       GetSQLValueString($_POST['Electronic_mirrors'], "text"),
                       GetSQLValueString($_POST['Central_locking'], "text"),
                       GetSQLValueString($_POST['Anti_lock_breaking_system_ABS'], "text"),
                       GetSQLValueString($_POST['Airbag'], "text"),
                       GetSQLValueString($_POST['Parking_sensors'], "text"),
                       GetSQLValueString($_POST['Traction_control'], "text"),
                       GetSQLValueString($_POST['Length'], "text"),
                       GetSQLValueString($_POST['Width'], "text"),
                       GetSQLValueString($_POST['Height'], "text"),
                       GetSQLValueString($_POST['Ground_clearance'], "text"));

  mysql_select_db($database_classicauto, $classicauto);
  $Result1 = mysql_query($insertSQL, $classicauto) or die(mysql_error());

  $insertGoTo = "admin_panel.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
    $updateGoTo .= "#cardetails";
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$maxRows_ca_user = 1;
$pageNum_ca_user = 0;
if (isset($_GET['pageNum_ca_user'])) {
  $pageNum_ca_user = $_GET['pageNum_ca_user'];
}
$startRow_ca_user = $pageNum_ca_user * $maxRows_ca_user;

mysql_select_db($database_classicauto, $classicauto);
$query_ca_user = "SELECT * FROM userdetails";
$query_limit_ca_user = sprintf("%s LIMIT %d, %d", $query_ca_user, $startRow_ca_user, $maxRows_ca_user);
$ca_user = mysql_query($query_limit_ca_user, $classicauto) or die(mysql_error());
$row_ca_user = mysql_fetch_assoc($ca_user);

if (isset($_GET['totalRows_ca_user'])) {
  $totalRows_ca_user = $_GET['totalRows_ca_user'];
} else {
  $all_ca_user = mysql_query($query_ca_user);
  $totalRows_ca_user = mysql_num_rows($all_ca_user);
}
$totalPages_ca_user = ceil($totalRows_ca_user/$maxRows_ca_user)-1;

$maxRows_ca_web_messages_admin = 1;
$pageNum_ca_web_messages_admin = 0;
if (isset($_GET['pageNum_ca_web_messages_admin'])) {
  $pageNum_ca_web_messages_admin = $_GET['pageNum_ca_web_messages_admin'];
}
$startRow_ca_web_messages_admin = $pageNum_ca_web_messages_admin * $maxRows_ca_web_messages_admin;

mysql_select_db($database_classicauto, $classicauto);
$query_ca_web_messages_admin = "SELECT * FROM web_messages WHERE Task_completed = 'No'";
$query_limit_ca_web_messages_admin = sprintf("%s LIMIT %d, %d", $query_ca_web_messages_admin, $startRow_ca_web_messages_admin, $maxRows_ca_web_messages_admin);
$ca_web_messages_admin = mysql_query($query_limit_ca_web_messages_admin, $classicauto) or die(mysql_error());
$row_ca_web_messages_admin = mysql_fetch_assoc($ca_web_messages_admin);

if (isset($_GET['totalRows_ca_web_messages_admin'])) {
  $totalRows_ca_web_messages_admin = $_GET['totalRows_ca_web_messages_admin'];
} else {
  $all_ca_web_messages_admin = mysql_query($query_ca_web_messages_admin);
  $totalRows_ca_web_messages_admin = mysql_num_rows($all_ca_web_messages_admin);
}
$totalPages_ca_web_messages_admin = ceil($totalRows_ca_web_messages_admin/$maxRows_ca_web_messages_admin)-1;

mysql_select_db($database_classicauto, $classicauto);
$query_cardetails_entry = "SELECT * FROM car_details";
$cardetails_entry = mysql_query($query_cardetails_entry, $classicauto) or die(mysql_error());
$row_cardetails_entry = mysql_fetch_assoc($cardetails_entry);
$totalRows_cardetails_entry = mysql_num_rows($cardetails_entry);

mysql_select_db($database_classicauto, $classicauto);
$query_ca_news_update = "SELECT * FROM news_updates";
$ca_news_update = mysql_query($query_ca_news_update, $classicauto) or die(mysql_error());
$row_ca_news_update = mysql_fetch_assoc($ca_news_update);
$totalRows_ca_news_update = mysql_num_rows($ca_news_update);

$maxRows_ca_orders = 1;
$pageNum_ca_orders = 0;
if (isset($_GET['pageNum_ca_orders'])) {
  $pageNum_ca_orders = $_GET['pageNum_ca_orders'];
}
$startRow_ca_orders = $pageNum_ca_orders * $maxRows_ca_orders;

mysql_select_db($database_classicauto, $classicauto);
$query_ca_orders = "SELECT * FROM orders WHERE Task_completed = 'No'";
$query_limit_ca_orders = sprintf("%s LIMIT %d, %d", $query_ca_orders, $startRow_ca_orders, $maxRows_ca_orders);
$ca_orders = mysql_query($query_limit_ca_orders, $classicauto) or die(mysql_error());
$row_ca_orders = mysql_fetch_assoc($ca_orders);

if (isset($_GET['totalRows_ca_orders'])) {
  $totalRows_ca_orders = $_GET['totalRows_ca_orders'];
} else {
  $all_ca_orders = mysql_query($query_ca_orders);
  $totalRows_ca_orders = mysql_num_rows($all_ca_orders);
}
$totalPages_ca_orders = ceil($totalRows_ca_orders/$maxRows_ca_orders)-1;

$queryString_ca_web_messages_admin = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ca_web_messages_admin") == false && 
        stristr($param, "totalRows_ca_web_messages_admin") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ca_web_messages_admin = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ca_web_messages_admin = sprintf("&totalRows_ca_web_messages_admin=%d%s", $totalRows_ca_web_messages_admin, $queryString_ca_web_messages_admin);

$queryString_ca_user = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ca_user") == false && 
        stristr($param, "totalRows_ca_user") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ca_user = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ca_user = sprintf("&totalRows_ca_user=%d%s", $totalRows_ca_user, $queryString_ca_user);

$queryString_ca_orders = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ca_orders") == false && 
        stristr($param, "totalRows_ca_orders") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ca_orders = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ca_orders = sprintf("&totalRows_ca_orders=%d%s", $totalRows_ca_orders, $queryString_ca_orders);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Classic Auto Dealers</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="css/my_style.css" rel="stylesheet" type="text/css"/>
    <script src="js/jquery-2.1.3.min.js"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <script src="js/ourscript.js" type="text/javascript"></script>
    
    
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>

<body>
    
    <div class="container-fluid header" id="myPageTop">
        <div class="row">
            <div class="container ">
                	
                    <nav class="navbar">
                        <div class="navbar-header">
                          <button type="button" class="navbar-toggle navbar-right" data-toggle="collapse" data-target="#myNavbar">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span> 
                          </button>
                          <img src="images/logo_03.png" class="img-responsive" style="margin:10px 10px 20px 0"/>
                        </div>
                        <div class="collapse navbar-collapse" id="myNavbar">
                        
                        <div >
                          <ul class="nav navbar-nav navbar-right">
                            <li class="userstatus">
                            	<a>
                                	<span class="glyphicon glyphicon-user"></span>
                            		<?php echo $_SESSION['MM_Username']; ?>
                                </a>
                            </li>
                            <?php if ($_SESSION['MM_UserGroup']=="Administrator"){ ?>
        
                            <li class="userstatus2">
                            	<a href="admin_panel.php">
                                	<span class="glyphicon glyphicon-cog"></span> Admin Panel
                                </a>
                            </li>
                              <?php } ?>
                            <li class="userstatus2">
                            	<a href="<?php echo $logoutAction ?>">
                                	<span class="glyphicon glyphicon-log-out"></span>
                                    Log out
                                </a>
                            </li>
                          </ul>
                        </div>
                        
                        <span class="hidden-xs"><br><br><br></span>
                        
                        <div>
                          <ul class="nav navbar-nav navbar-right">
                            <li class="active"><a href="home.php"><i class="fa fa-home about_menu_icon_img" aria-hidden="true"></i>Home</a></li>
                            <li><a href="about.php"><i class="fa fa-users about_menu_icon_img" aria-hidden="true"></i>About</a></li>
                            <li><a href="products.php?Brand=&"><i class="fa fa-car about_menu_icon_img" aria-hidden="true"></i>Products</a></li>
                            <li><a href="contact.php"><i class="fa fa-envelope about_menu_icon_img" aria-hidden="true"></i>Contact</a></li>
                          </ul>
                          </div>
                        </div>
                    </nav>
                    
          </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->


    <div class="container-fluid c1_admin">
       <div class="row">

          <div class="container"> 
             <div class="row">
             	<div class="col-md-12">
              		<h2 class="about_heading"><span class="white_font">Admin Panel</span></h2>
                 </div><!--col-md-12 -->
             </div><!--row -->
          </div><!--container -->
          
        </div><!--row -->
    </div><!--container-fluid -->


    <div class="container-fluid c1_admin">
       <div class="row">

          <div class="container pad_b_30"> 
       		<div class="row">
             	<div class="col-md-12">
              		
                    <ul class="nav nav-pills">
                      <li><a href="#orders_recieved">Orders Pending</a></li>
                      <li><a href="#customermessage">Messages Pending</a></li>
                      <li><a href="#userdetails">User Details</a></li>
                      <li><a href="#newsupdate">Add News Feed</a></li>
                      <li><a href="#cardetails">Add Products</a></li>
                    </ul>
                    
                 </div><!--col-md-12 -->
              </div><!--row -->
          </div><!--container -->
          
          
          

         <div class="container pad_b_50" id="orders_recieved"> 
                
       	   <div class="header col-md-12 c1_admin2">
                	<h3 class="headerstyle2" align="center">Orders Recieved <small>(<?php if($totalRows_ca_orders==0){
							echo "<i class='fa fa-check yellow'></i> All orders completed";
						} else {echo "Orders pending: ".$totalRows_ca_orders;}
						
						?>)</small></h3>
               </div><!--col-md-12 -->
                	
             	<div class="col-md-12 c1_admin3">
                  <form method="post" name="form5" action="<?php echo $editFormAction; ?>">
                  
                  	
                  	
				<table align="center" class="table-responsive">
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Car Model</label></td>
                        <td><input class="form-control" readonly type="text" name="Car_model" value="<?php echo htmlentities($row_ca_orders['Car_model'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
            		<tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Name:</label></td>
                        <td><input class="form-control" readonly type="text" name="Name" value="<?php echo htmlentities($row_ca_orders['Name'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">E-Mail</label></td>
                        <td><input class="form-control" readonly type="text" name="E_mail" value="<?php echo htmlentities($row_ca_orders['E_mail'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Phone</label></td>
                        <td><input class="form-control" readonly type="text" name="Phone" value="<?php echo htmlentities($row_ca_orders['Phone'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Address</label></td>
                        <td><textarea class="form-control" cols="50" rows="5" readonly name="Address"><?php echo htmlentities($row_ca_orders['Address'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Message</label></td>
                        <td><textarea class="form-control" cols="50" rows="5" readonly name="Message"><?php echo htmlentities($row_ca_orders['Message'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Date & Time</label></td>
                        <td><input class="form-control" readonly type="text" name="Time_stamp" value="<?php echo htmlentities($row_ca_orders['Time_stamp'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Task Completed</label></td>
                        <td valign="baseline">
                         	<label class="radio-inline"><input type="radio" name="Task_completed" value="Yes" <?php if (!(strcmp(htmlentities($row_ca_orders['Task_completed'], ENT_COMPAT, 'utf-8'),"Yes"))) {echo "checked=\"checked\"";} ?>>Yes</label>
                          	<label class="radio-inline"><input type="radio" name="Task_completed" value="No" <?php if (!(strcmp(htmlentities($row_ca_orders['Task_completed'], ENT_COMPAT, 'utf-8'),"No"))) {echo "checked=\"checked\"";} ?>>No</label>
                        </td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td><input class="more_button2" type="submit" value="Update record"></td>
                      </tr>
                    </table>
                    <input type="hidden" name="Sl_no" value="<?php echo $row_ca_orders['Sl_no']; ?>">
                    <input type="hidden" name="MM_update" value="form5">
                    <input type="hidden" name="Sl_no" value="<?php echo $row_ca_orders['Sl_no']; ?>">
                  </form>
                    <br><br>
                  <table border="0" align="center">
                    <tr>
                      
                      <td><?php if ($pageNum_ca_orders > 0) { // Show if not first page ?>
                          <a href="<?php printf("%s?pageNum_ca_orders=%d%s", $currentPage, max(0, $pageNum_ca_orders - 1), $queryString_ca_orders); ?>"><i class="fa fa-chevron-left fa-2x yellow"></i></a>
                          <?php } // Show if not first page ?></td>
                      <td><?php if ($pageNum_ca_orders < $totalPages_ca_orders) { // Show if not last page ?>
                          <a href="<?php printf("%s?pageNum_ca_orders=%d%s", $currentPage, min($totalPages_ca_orders, $pageNum_ca_orders + 1), $queryString_ca_orders); ?>"><i class="fa fa-chevron-right fa-2x yellow"></i></a>
                          <?php } // Show if not last page ?></td>
                     
                    </tr>
                  </table>
<p>&nbsp;</p>
               </div>
           	   <!--col-md-12 -->
          </div><!--container -->
          
          

          <div class="container pad_b_30" id="customermessage"> 
                
             	<div class="header col-md-12 c1_admin2">
                	<h3 class="headerstyle2" align="center">Customer Messages <small>(<?php if($totalRows_ca_web_messages_admin==0){
							echo "<i class='fa fa-check yellow'></i> All messages completed";
						} else {echo "Messages pending: ".$totalRows_ca_web_messages_admin; }; ?>)</small>
                    </h3>
                </div><!--col-md-12 -->
                
             	<div class="col-md-12 c1_admin3">
                  
                  	
                  <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                    <table align="center" class="table-responsive">
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Message ID:</label></td>
                        <td><?php echo $row_ca_web_messages_admin['Sl_no']; ?></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Date & Time:</label></td>
                        <td><?php echo htmlentities($row_ca_web_messages_admin['Time_stamp'], ENT_COMPAT, 'utf-8'); ?></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Name:</label></td>
                        <td><input class="form-control" readonly type="text" name="Name" value="<?php echo htmlentities($row_ca_web_messages_admin['Name'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">E-mail:</label></td>
                        <td><input class="form-control" readonly type="text" name="E_mail" value="<?php echo htmlentities($row_ca_web_messages_admin['E_mail'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right" valign="top"><label class="form-group">Message:</label></td>
                        <td><textarea class="form-control" readonly name="Message" cols="50" rows="5"><?php echo htmlentities($row_ca_web_messages_admin['Message'], ENT_COMPAT, 'utf-8'); ?></textarea></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td valign="baseline"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Task-Completed:</label></td>
                        <td valign="baseline">
                        	<label class="radio-inline"><input type="radio" name="Task_completed" value="Yes"  <?php if (!(strcmp(htmlentities($row_ca_web_messages_admin['Task_completed'], ENT_COMPAT, 'utf-8'),"Yes"))) {echo "checked=\"checked\"";} ?>>Yes</label>
                       	  <label class="radio-inline"><input type="radio" name="Task_completed" value="No"  <?php if (!(strcmp(htmlentities($row_ca_web_messages_admin['Task_completed'], ENT_COMPAT, 'utf-8'),"No"))) {echo "checked=\"checked\"";} ?>>No</label>
                        </td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td><input class="more_button2" type="submit" value="Update record"></td>
                      </tr>
                    </table><br><br>
                  <table border="0" align="center">
                    <tr>
                      <td><?php if ($pageNum_ca_web_messages_admin > 0) { // Show if not first page ?>
                          	<a href="<?php printf("%s?pageNum_ca_web_messages_admin=%d%s", $currentPage, max(0, $pageNum_ca_web_messages_admin - 1), $queryString_ca_web_messages_admin."#customermessage"); ?>"><i class="fa fa-chevron-left fa-2x yellow"></i></a>
                          <?php } // Show if not first page ?></td>
                      <td><?php if ($pageNum_ca_web_messages_admin < $totalPages_ca_web_messages_admin) { // Show if not last page ?>
                          	<a href="<?php printf("%s?pageNum_ca_web_messages_admin=%d%s", $currentPage, min($totalPages_ca_web_messages_admin, $pageNum_ca_web_messages_admin + 1), $queryString_ca_web_messages_admin."#customermessage"); ?>"><i class="fa fa-chevron-right fa-2x yellow"></i></a>
                          <?php } // Show if not last page ?></td>
                    </tr>
                  </table>
                  
                    <input type="hidden" name="MM_update" value="form1">
                    <input type="hidden" name="Sl_no" value="<?php echo $row_ca_web_messages_admin['Sl_no']; ?>">
                  </form>
					<p>&nbsp;</p>
                </div><!--col-md-12 -->
          </div><!--container -->
    


          <div class="container pad_b_50" id="newsupdate"> 
             	<div class="header col-md-12 c1_admin2">
                	<h3 class="headerstyle2" align="center">Add News Update</h3>
                </div><!--col-md-12 -->
                
             	<div class="col-md-12 c1_admin3">
                  <form method="post" name="form3" action="<?php echo $editFormAction; ?>">
                    <table align="center" class="table-responsive">
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td><input class="form-control" type="hidden" name="sl_no" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">News Header:</label></td>
                        <td><input required class="form-control" type="text" name="news_header" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right" valign="top"><label class="form-group">News Content:</label></td>
                        <td><textarea required class="form-control"  name="news_body_text" cols="50" rows="5"></textarea></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td valign="baseline"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Search Text:</label></td>
                        <td><input required class="form-control" type="text" name="search_text" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Image Name:</label></td>
                        <td><input required class="form-control" type="text" name="image_name" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td><input class="more_button2" type="submit" value="Insert record"></td>
                      </tr>
                    </table>
                    <input type="hidden" name="update_timestamp" value="">
                    <input type="hidden" name="MM_insert" value="form3">
                  </form>
                  <p>&nbsp;</p>
                </div><!--col-md-12 -->
         </div><!--container -->
            


          <div class="container pad_b_50" id="userdetails">
          
             	<div class="header col-md-12 c1 c1_admin2">
                	<h3 class="headerstyle2" align="center">Modify User Details</h3>
                </div><!--col-md-12 -->
                
             	<div class="col-md-12 c1_admin3">
                  <form method="post" name="form6" action="<?php echo $editFormAction; ?>">
                    <table align="center" class="table-responsive">
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Username:</label></td>
                        <td><?php echo $row_ca_user['Username']; ?></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Full Name:</label></td>
                        <td><input class="form-control" type="text" name="Full_name" value="<?php echo htmlentities($row_ca_user['Full_name'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Password:</label></td>
                        <td><input class="form-control" type="text" name="Password" value="<?php echo htmlentities($row_ca_user['Password'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">DOB:</label></td>
                        <td><input class="form-control" type="date" name="DOB" value="<?php echo htmlentities($row_ca_user['DOB'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Reg Date:</label></td>
                        <td><input readonly class="form-control" type="text" name="Reg_date" value="<?php echo htmlentities($row_ca_user['Reg_date'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Access Level:</label></td>
                        <td>
                        <input class="form-control" type="text" name="Access_level" value="<?php echo htmlentities($row_ca_user['Access_level'], ENT_COMPAT, 'utf-8'); ?>" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td><input class="more_button2" type="submit" value="Update record"></td>
                      </tr>
                    </table>
                    <input type="hidden" name="MM_update" value="form6">
                    <input type="hidden" name="Username" value="<?php echo $row_ca_user['Username']; ?>">
                  </form><br><br>
                  <table border="0" align="center">
                    <tr>
                      <td><?php if ($pageNum_ca_user > 0) { // Show if not first page ?>
                          <a href="<?php printf("%s?pageNum_ca_user=%d%s", $currentPage, max(0, $pageNum_ca_user - 1), $queryString_ca_user."#userdetails"); ?>"><i class="fa fa-chevron-left fa-2x yellow"></i></a>
                          <?php } // Show if not first page ?></td>
                      <td><?php if ($pageNum_ca_user < $totalPages_ca_user) { // Show if not last page ?>
                          <a href="<?php printf("%s?pageNum_ca_user=%d%s", $currentPage, min($totalPages_ca_user, $pageNum_ca_user + 1), $queryString_ca_user."#userdetails"); ?>"><i class="fa fa-chevron-right fa-2x yellow"></i></a>
                          <?php } // Show if not last page ?></td>
                    </tr>
                  </table>
<p>&nbsp;</p>
                </div><!--col-md-12 -->
         </div><!--container -->
         
         
          
    

          <div class="container pad_b_50" id="cardetails"> 
             	<div class="header col-md-12 c1_admin2">
                	<h3 class="headerstyle2" align="center">Add New Product</h3>
                </div><!--col-md-12 -->
 
             	<div class="col-md-12 c1_admin3">
                  <form method="post" name="form2" action="<?php echo $editFormAction; ?>">
                    <table align="center" class="table-responsive">
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td><input class="form-control" type="hidden" name="Sl_no" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Brand:</label></td>
                        <td><input class="form-control" type="text" name="Brand" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Model:</label></td>
                        <td><input class="form-control" type="text" name="Model" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Version:</label></td>
                        <td><input class="form-control" type="text" name="Model2" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right" valign="top"><label class="form-group">Description:</label></td>
                        <td><textarea class="form-control" name="Description" cols="50" rows="5"></textarea></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td valign="baseline"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Image Path:</label></td>
                        <td><input class="form-control" type="text" name="Image_name" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Fuel Type:</label></td>
                        <td><input class="form-control" type="text" name="Fuel_type" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Body Type:</label></td>
                        <td><input class="form-control" type="text" name="Body_type" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Displacement:</label></td>
                        <td><input class="form-control" type="text" name="Engine_displacement" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Transmission:</label></td>
                        <td><input class="form-control" type="text" name="Transmission" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">No. of Gears:</label></td>
                        <td><input class="form-control" type="text" name="No_of_gears" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Power:</label></td>
                        <td><input class="form-control" type="text" name="Power" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Torque:</label></td>
                        <td><input class="form-control" type="text" name="Torque" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Mileage (city):</label></td>
                        <td><input class="form-control" type="text" name="Mileage_city" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Drive Type:</label></td>
                        <td><input class="form-control" type="text" name="Drive_type" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Top Speed:</label></td>
                        <td><input class="form-control" type="text" name="Top_speed" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Fuel Capacity:</label></td>
                        <td><input class="form-control" type="text" name="Fuel_capacity_litre" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Seating Capacity:</label></td>
                        <td><input class="form-control" type="text" name="Seating_capacity" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Colours:</label></td>
                        <td><input class="form-control" type="text" name="Colours" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Price:</label></td>
                        <td><input class="form-control" type="text" name="Price" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Air Conditioning:</label></td>
                        <td><input class="form-control" type="text" name="Air_conditioning" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Power Streering:</label></td>
                        <td><input class="form-control" type="text" name="Power_streering" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Navigation System:</label></td>
                        <td><input class="form-control" type="text" name="Navigation_system" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Video System:</label></td>
                        <td><input class="form-control" type="text" name="Video_system" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Sunroof:</label></td>
                        <td><input class="form-control" type="text" name="Sunroof" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Leather Seats:</label></td>
                        <td><input class="form-control" type="text" name="Leather_seats" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Electronic Mirrors:</label></td>
                        <td><input class="form-control" type="text" name="Electronic_mirrors" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Central Locking:</label></td>
                        <td><input class="form-control" type="text" name="Central_locking" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">ABS:</label></td>
                        <td><input class="form-control" type="text" name="Anti_lock_breaking_system_ABS" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Airbag:</label></td>
                        <td><input class="form-control" type="text" name="Airbag" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Parking Sensors:</label></td>
                        <td><input class="form-control" type="text" name="Parking_sensors" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Traction Control:</label></td>
                        <td><input class="form-control" type="text" name="Traction_control" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Length:</label></td>
                        <td><input class="form-control" type="text" name="Length" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Width:</label></td>
                        <td><input class="form-control" type="text" name="Width" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Height:</label></td>
                        <td><input class="form-control" type="text" name="Height" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right"><label class="form-group">Ground Clearance:</label></td>
                        <td><input class="form-control" type="text" name="Ground_clearance" value="" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right">&nbsp;</td>
                        <td><input class="more_button2" type="submit" value="Insert record"></td>
                      </tr>
                    </table>
                    <input type="hidden" name="MM_insert" value="form2">
                  </form>
                  <p>&nbsp;</p>
               </div><!--col-md-12 -->
          </div><!--container -->
          
         
         
          
        </div><!--row -->
    </div><!--container-fluid -->
   
            
          
          

    <div class="container-fluid">
        <div class="row">
            <div class="container">
       			<div class="row">
                <div class="col-xs-12 abcd">
                  	<a href="#myPageTop" class="uparrow"><i class="fa fa-4x fa-chevron-circle-up"></i></a>
                </div>
        		</div><!--row -->
            </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->
    
    
	<div class="container-fluid footer">
        <div class="row">
            <div class="container">
                <div class="col-md-4">
                <p>Copyright &copy; 2016</p>
                </div><!--col-md-4 -->
                <div class="col-md-4 text-center">
                    <i class="fa fa-facebook-square fa-2x facebook" aria-hidden="true"></i>
                    <i class="fa fa-twitter-square fa-2x twitter" aria-hidden="true"></i>
                    <i class="fa fa-google-plus-square fa-2x google" aria-hidden="true"></i>
                </div><!--col-md-4 -->
                <div class="col-md-4">
                    <span class="pull-right">Terms & Conditions</span>
                </div><!--col-md-4 -->
            </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid-->
    
</body>
</html>
<?php
mysql_free_result($ca_user);

mysql_free_result($ca_web_messages_admin);

mysql_free_result($cardetails_entry);

mysql_free_result($ca_news_update);

mysql_free_result($ca_orders);
?>
