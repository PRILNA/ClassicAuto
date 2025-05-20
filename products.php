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
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

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
    if (($strUsers == "") && true) { 
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

mysql_select_db($database_classicauto, $classicauto);
$query_ca_user = "SELECT * FROM userdetails";
$ca_user = mysql_query($query_ca_user, $classicauto) or die(mysql_error());
$row_ca_user = mysql_fetch_assoc($ca_user);
$totalRows_ca_user = mysql_num_rows($ca_user);

$maxRows_ca_news_updates = 6;
$pageNum_ca_news_updates = 0;
if (isset($_GET['pageNum_ca_news_updates'])) {
  $pageNum_ca_news_updates = $_GET['pageNum_ca_news_updates'];
}
$startRow_ca_news_updates = $pageNum_ca_news_updates * $maxRows_ca_news_updates;

mysql_select_db($database_classicauto, $classicauto);
$query_ca_news_updates = "SELECT * FROM news_updates ORDER BY sl_no DESC";
$query_limit_ca_news_updates = sprintf("%s LIMIT %d, %d", $query_ca_news_updates, $startRow_ca_news_updates, $maxRows_ca_news_updates);
$ca_news_updates = mysql_query($query_limit_ca_news_updates, $classicauto) or die(mysql_error());
$row_ca_news_updates = mysql_fetch_assoc($ca_news_updates);

if (isset($_GET['totalRows_ca_news_updates'])) {
  $totalRows_ca_news_updates = $_GET['totalRows_ca_news_updates'];
} else {
  $all_ca_news_updates = mysql_query($query_ca_news_updates);
  $totalRows_ca_news_updates = mysql_num_rows($all_ca_news_updates);
}
$totalPages_ca_news_updates = ceil($totalRows_ca_news_updates/$maxRows_ca_news_updates)-1;

$maxRows_ca_car_details = 11;
$pageNum_ca_car_details = 0;
if (isset($_GET['pageNum_ca_car_details'])) {
  $pageNum_ca_car_details = $_GET['pageNum_ca_car_details'];
}
$startRow_ca_car_details = $pageNum_ca_car_details * $maxRows_ca_car_details;

$colname_ca_car_details = "-1";
if (isset($_GET['Brand'])) {
  $colname_ca_car_details = $_GET['Brand'];
}
mysql_select_db($database_classicauto, $classicauto);
$query_ca_car_details = sprintf("SELECT * FROM car_details WHERE Brand LIKE %s ORDER BY Sl_no DESC", GetSQLValueString("%" . $colname_ca_car_details . "%", "text"));
$query_limit_ca_car_details = sprintf("%s LIMIT %d, %d", $query_ca_car_details, $startRow_ca_car_details, $maxRows_ca_car_details);
$ca_car_details = mysql_query($query_limit_ca_car_details, $classicauto) or die(mysql_error());
$row_ca_car_details = mysql_fetch_assoc($ca_car_details);

if (isset($_GET['totalRows_ca_car_details'])) {
  $totalRows_ca_car_details = $_GET['totalRows_ca_car_details'];
} else {
  $all_ca_car_details = mysql_query($query_ca_car_details);
  $totalRows_ca_car_details = mysql_num_rows($all_ca_car_details);
}
$totalPages_ca_car_details = ceil($totalRows_ca_car_details/$maxRows_ca_car_details)-1;

$queryString_ca_car_details = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ca_car_details") == false && 
        stristr($param, "totalRows_ca_car_details") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ca_car_details = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ca_car_details = sprintf("&totalRows_ca_car_details=%d%s", $totalRows_ca_car_details, $queryString_ca_car_details);

$queryString_ca_news_updates = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ca_news_updates") == false && 
        stristr($param, "totalRows_ca_news_updates") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ca_news_updates = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ca_news_updates = sprintf("&totalRows_ca_news_updates=%d%s", $totalRows_ca_news_updates, $queryString_ca_news_updates);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Classic Auto Dealers</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="css/my_style.css" rel="stylesheet" type="text/css" />
    <script src="js/jquery-2.1.3.min.js"></script>
    <script src="js/bootstrap.min.js" type="text/javascript" ></script>
    <script src="js/ourscript.js" type="text/javascript" ></script>
    
    
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
    
    <div class="container-fluid header2 pad_b_30">
            	<img class="img-responsive" src="images/product_banner_02.jpg" style="width:100%;">
    </div><!--container-fluid -->
    
    <div id="red" class="container-fluid header pad_b_30">
        <div class="row">
            <div class="container ">
        		<div class="row">
                
                  <div class="col-md-3 slideanim pad_b_30">
                      <ul class="nav nav-pills nav-stacked">
                        <li><a href="products.php?Brand=&#red">All</a></li>
                        <li><a href="products.php?Brand=mercedes-benz#red">Mercedes</a></li>
                        <li><a href="products.php?Brand=audi#red">Audi</a></li>
                        <li><a href="products.php?Brand=bmw#red">BMW</a></li>
                        <li><a href="products.php?Brand=jaguar#red">Jaguar</a></li>
                        <li><a href="products.php?Brand=volkswagen#red">Volkswagen</a></li>
                      </ul>
                  </div><!--col-md-3 -->
                      
                    <?php do { ?>
                         <div class="col-md-3 pad_b_30  slideanim"><div class="myrow">
                             <div class="product_box">
                                <img src="images/<?php echo $row_ca_car_details['Brand']; ?>/<?php echo $row_ca_car_details['Image_name']; ?>/1.jpg" class="img-responsive">
                                    <h4 style="color:#29816f;margin-top:5px;"><?php echo $row_ca_car_details['Brand']; ?> <?php echo $row_ca_car_details['Model']; ?></h4>
                                    <span class="label label-default"><?php echo $row_ca_car_details['Engine_displacement']; ?></span>
                                    <span class="label label-warning"><?php echo $row_ca_car_details['Fuel_type']; ?></span>
                                    <span class="label label-warning"><?php echo $row_ca_car_details['Body_type']; ?></span><br>
                                    <span class="label label-info"><?php echo $row_ca_car_details['Power']; ?></span>
                                    <span class="label label-info"><?php echo $row_ca_car_details['Torque']; ?></span>
                                    <span class="label label-info"><?php echo $row_ca_car_details['Drive_type']; ?></span>
                                    <span class="label label-info"><?php echo $row_ca_car_details['Transmission']; ?></span>
                                    <h3 class="price">
                                        <i class="fa fa-rupee"></i> 
                                        
                                        <?php 
                                            $aa=1.11;
                                            $aa=$row_ca_car_details['Price'];
                                            if($aa>9999999){
                                                echo (round($aa/10000000,2))." Crore";
                                                } else {
                                                echo (round($aa/100000,2))." Lac";
                                                }
                                        ?>
                                        
                                    </h3>
                                    <a href="car_details.php?Sl_no=<?php echo $row_ca_car_details['Sl_no']; ?>" class="more_button pad_b_20">Details</a>
                        </div><!--product_box -->
                    </div></div><!--col-md-3  and    myrow-->
                    <?php } while ($row_ca_car_details = mysql_fetch_assoc($ca_car_details)); ?>
                    
                    
                    <div class="col-xs-12 text-right">
                    	<?php if ($pageNum_ca_car_details > 0) { // Show if not first page ?>
                            <a href="<?php printf("%s?pageNum_ca_car_details=%d%s", $currentPage, max(0, $pageNum_ca_car_details - 1), $queryString_ca_car_details."#red"); ?>"><i class="fa fa-chevron-circle-left fa-2x yellow"></i></a>
                            <?php } // Show if not first page ?>
                        <?php if ($pageNum_ca_car_details < $totalPages_ca_car_details) { // Show if not last page ?>
                            <a href="<?php printf("%s?pageNum_ca_car_details=%d%s", $currentPage, min($totalPages_ca_car_details, $pageNum_ca_car_details + 1), $queryString_ca_car_details."#red"); ?>"><i class="fa fa-chevron-circle-right fa-2x yellow"></i></a>
                            <?php } // Show if not last page ?>
                     </div><!--col-xs-12 -->
                    
                    
                </div><!--row -->
          </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->
        
    
    
    <div class="container-fluid c04 upcoming">
        <div class="row pad_b_50">
            <div class="container   slideanim">
        		<div class="row">
                            
                	<div class="col-md-12 heading">
                    	<p class="topic_header">
                        	Coming Soon
                    	</p>
                    </div><!--col-md-12 -->
                    
                    <div class="col-md-6">
                    	<img src="images/up_07.png" class="img-responsive img-rounded">
                    </div><!--col-md-6 -->
    
					<div class="col-md-6">
    					<h3 class="latest_entry_subheader">THE AUDI RS 7</h3>
                        <p>
                            The complete 911 range is currently undergoing its
                            mid-cycle update, with the most recent model to be 
                            updated being the Turbo. It looks like the next will be 
                            the updated GT3, and judging by the latest prototype 
                            the reveal isn’t far. A debut in the coming months is 
                            likely, as we've already seen the updated GT3 
                            in race car form. the reveal isn’t far. A debut in the 
                            coming months.The complete 911 range is currently undergoing its
                            mid-cycle update, with the most recent model to be 
                            updated being the Turbo. 
                        </p>
    					<table class="table table-responsive table-bordered">
                            <tr>
                               <th>
                                	Major Specifications
                               </th>
                            </tr>
                            <tr>
                               <td>
                                	3993 cc, Petrol, 605 bhp @ 5700 RPM power
                               </td>
                            </tr> 
                            <tr>
                               <td>
                                	8-speed, Automatic, 4WD / AWD
                               </td>
                            </tr> 
                            <tr>
                               <td>
                                	4 Seater
                               </td>
                            </tr> 
                            <tr>
                               <td>
                                	Power Steering
                               </td>
                            </tr> 
                       </table>
                  </div><!--col-md-6 -->
                    
       		 </div><!--row -->
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

mysql_free_result($ca_news_updates);

mysql_free_result($ca_car_details);
?>
