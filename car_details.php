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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO orders (Sl_no, Name, E_mail, Phone, Address, Car_model, Time_stamp, Task_completed, Message) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Sl_no'], "int"),
                       GetSQLValueString($_POST['Name'], "text"),
                       GetSQLValueString($_POST['E_mail'], "text"),
                       GetSQLValueString($_POST['Phone'], "text"),
                       GetSQLValueString($_POST['Address'], "text"),
                       GetSQLValueString($_POST['Car_model'], "text"),
                       GetSQLValueString($_POST['Time_stamp'], "date"),
                       GetSQLValueString($_POST['Task_completed'], "text"),
                       GetSQLValueString($_POST['Message'], "text"));

  mysql_select_db($database_classicauto, $classicauto);
  $Result1 = mysql_query($insertSQL, $classicauto) or die(mysql_error());
}

mysql_select_db($database_classicauto, $classicauto);
$query_ca_user = "SELECT * FROM userdetails";
$ca_user = mysql_query($query_ca_user, $classicauto) or die(mysql_error());
$row_ca_user = mysql_fetch_assoc($ca_user);
$totalRows_ca_user = mysql_num_rows($ca_user);

$maxRows_ca_car_details_single = 1;
$pageNum_ca_car_details_single = 0;
if (isset($_GET['pageNum_ca_car_details_single'])) {
  $pageNum_ca_car_details_single = $_GET['pageNum_ca_car_details_single'];
}
$startRow_ca_car_details_single = $pageNum_ca_car_details_single * $maxRows_ca_car_details_single;

$colname_ca_car_details_single = "-1";
if (isset($_GET['Sl_no'])) {
  $colname_ca_car_details_single = $_GET['Sl_no'];
}
mysql_select_db($database_classicauto, $classicauto);
$query_ca_car_details_single = sprintf("SELECT * FROM car_details WHERE Sl_no = %s", GetSQLValueString($colname_ca_car_details_single, "int"));
$query_limit_ca_car_details_single = sprintf("%s LIMIT %d, %d", $query_ca_car_details_single, $startRow_ca_car_details_single, $maxRows_ca_car_details_single);
$ca_car_details_single = mysql_query($query_limit_ca_car_details_single, $classicauto) or die(mysql_error());
$row_ca_car_details_single = mysql_fetch_assoc($ca_car_details_single);

if (isset($_GET['totalRows_ca_car_details_single'])) {
  $totalRows_ca_car_details_single = $_GET['totalRows_ca_car_details_single'];
} else {
  $all_ca_car_details_single = mysql_query($query_ca_car_details_single);
  $totalRows_ca_car_details_single = mysql_num_rows($all_ca_car_details_single);
}
$totalPages_ca_car_details_single = ceil($totalRows_ca_car_details_single/$maxRows_ca_car_details_single)-1;

mysql_select_db($database_classicauto, $classicauto);
$query_ca_orderform = "SELECT * FROM orders";
$ca_orderform = mysql_query($query_ca_orderform, $classicauto) or die(mysql_error());
$row_ca_orderform = mysql_fetch_assoc($ca_orderform);
$totalRows_ca_orderform = mysql_num_rows($ca_orderform);
?>



<?php
    function ourtickmark_icon($tickvalue) {
    if($tickvalue=="Yes"){
        echo ("<i class='fa fa-check fa-icon-green'></i>");
        } elseif($tickvalue=="No") {
        echo ("<i class='fa fa-close fa-icon-red'></i>");}
		else { echo ($tickvalue);}
		}
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
    
    
    <link rel="stylesheet" type="text/css" href="engine3/style.css" />
    
    
	<style>
    </style>
    
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
    
    
    <div class="container-fluid">
       <div class="row">
          <div class="container">
       			<hr class="border_dvdr"/>
           </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->


    <div class="container-fluid">
       <div class="row">
       
          <div class="container">
             <div class="row">
             	<div class="col-md-12">
                    <ol class="breadcrumb text-success">
                      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                      <li class="breadcrumb-item"><a href="products.php?Brand=&">Products</a></li>
                      <li class="breadcrumb-item active">Details</li>
                    </ol>
                 </div><!--col-md-12 -->
             </div><!--row -->
          </div><!--container -->
    
          
        <div class="container"> 
         <div class="row">
            <div class="col-md-12" style="padding-bottom:5px;">
                <h2 class="about_heading"><?php echo $row_ca_car_details_single['Brand']; ?>&nbsp;<?php echo $row_ca_car_details_single['Model']; ?>&nbsp;<small><small><?php echo $row_ca_car_details_single['Model2']; ?></small></small></h2>
           </div><!--col-md-12 -->
         </div><!--row -->
        </div><!--container -->
      
      
      <div class="container pad_b_50">
      	<div class="row">
        
        	<div class="col-md-6 pad_b_30">
               
                <div id="wowslider-container3">
                <div class="ws_images"><ul>
                        <li><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/a.jpg" alt="" title="" id="wows3_0"/></li>
                        <li><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/b.jpg" alt="" title="" id="wows3_1"/></li>
                        <li><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/c.jpg" alt="" title="" id="wows3_2"/></li>
                        <li><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/d.jpg" alt="" title="" id="wows3_3"/></li>
                        <li><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/e.jpg" alt="" title="" id="wows3_4"/></li>
                        <li><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/f.jpg" alt="" title="" id="wows3_5"/></li>
                    </ul></div>
                    <div class="ws_thumbs">
                <div>
                        <a href="#" title=""><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/a.jpg" alt="" /></a>
                        <a href="#" title=""><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/b.jpg" alt="" /></a>
                        <a href="#" title=""><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/c.jpg" alt="" /></a>
                        <a href="#" title=""><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/d.jpg" alt="" /></a>
                        <a href="#" title=""><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/e.jpg" alt="" /></a>
                        <a href="#" title=""><img src="images/<?php echo $row_ca_car_details_single['Brand']; ?>/<?php echo $row_ca_car_details_single['Image_name']; ?>/f.jpg" alt="" /></a>
                    </div>
                </div>
                <div class="ws_script" style="position:absolute;left:-99%"></div>
                <div class="ws_shadow"></div>
                </div>	
                <script type="text/javascript" src="engine3/wowslider.js"></script>
                <script type="text/javascript" src="engine3/script.js"></script>
                    
            </div><!--col-md-6 -->
            
            <div class="col-md-6">
                <div class="row">
                    
                    <div class="col-md-12 car_details_amount">
                        <h2>
                                <span class="label label-danger" style="padding-top:10px;">
                                <i class="fa fa-rupee"></i> 

                                <?php 
                                    $aa=1.11;
                                    $aa=$row_ca_car_details_single['Price'];
                                    if($aa>9999999){
                                        echo (round($aa/10000000,2))." Crore";
                                        } else {
                                        echo (round($aa/100000,2))." Lac";
                                        }
                                ?>

                                </span>
                            </h2>


                           <div class="panel-group" id="accordion">
                              <div class="panel panel-default">
                                <div class="panel-heading">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                      <h4 class="panel-title">
                                        Specifications
                                      </h4>
                                    </a>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse in">
                                  <div class="panel-body">

                                      <div class="col-md-6">
                                          <table class="table table-responsive table-hover">
                                            <tr>
                                                <td>Fuel Type</td><th><?php echo $row_ca_car_details_single['Fuel_type']; ?></th>
                                            </tr>
                                            <tr>
                                                <td>Engine</td><th><?php echo $row_ca_car_details_single['Engine_displacement']; ?></th>
                                            </tr>
                                           
                                            <tr>
                                                <td>Torque</td><th><?php echo $row_ca_car_details_single['Torque']; ?></th>
                                            </tr>
                                            <tr>
                                                <td>Top Speed</td><th><?php echo $row_ca_car_details_single['Top_speed']; ?></th>
                                            </tr>
                                          </table>
                                      </div>

                                      <div class="col-md-6">
                                          <table class="table table-responsive table-hover">
                                           
                                            <tr>
                                                <td>Transmission</td><th><?php echo $row_ca_car_details_single['Transmission']; ?></th>
                                            </tr>
                                            <tr>
                                                <td>Power</td><th><?php echo $row_ca_car_details_single['Power']; ?></th>
                                            </tr>
                                            <tr>
                                                <td>Mileage <small>(city)</small></td><th><?php echo $row_ca_car_details_single['Mileage_city']; ?></th>
                                            </tr>
                                            <tr>
                                                <td>Colours</td><th><?php echo $row_ca_car_details_single['Colours']; ?></th>
                                            </tr>
                                          </table>
                                      </div>

                                  </div>
                                </div>
                              </div>
                              <div class="panel panel-default">
                                <div class="panel-heading">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                      <h4 class="panel-title">
                                        Features
                                      </h4>
                                    </a>
                                </div>
                                <div id="collapse2" class="panel-collapse collapse">
                                  <div class="panel-body">


                                      <div class="col-md-6">
                                          <table class="table table-responsive table-hover">
                                            <tr>
                                                <td>Air Conditioning</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Air_conditioning']); ?></th>
                                            </tr>
                                            <tr>
                                                <td>Power Steering</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Power_streering']); ?></th>
                                            </tr>
                                            <tr>
                                                <td>Navigation System</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Navigation_system']); ?></th>
                                            </tr>
                                            <tr>
                                                <td>Video System</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Video_system']); ?></th>
                                            </tr>
                                          </table>
                                      </div>

                                      <div class="col-md-6">
                                          <table class="table table-responsive table-hover">
                                            <tr>
                                                <td>Sun roof</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Sunroof']); ?></th>
                                            </tr>
                                            <tr>
                                                <td>Leather Seats</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Leather_seats']); ?></th>
                                            </tr>
                                            <tr>
                                                <td>Electronic Mirrors</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Electronic_mirrors']); ?></th>
                                            </tr>
                                            <tr>
                                                <td>Parking Sensors</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Parking_sensors']); ?></th>
                                            </tr>
                                          </table>
                                      </div>
                                  </div>
                                </div>
                              </div>
                              <div class="panel panel-default">
                                <div class="panel-heading">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                                      <h4 class="panel-title">
                                        Safety and Security
                                      </h4>
                                    </a>
                                </div>
                                <div id="collapse3" class="panel-collapse collapse">
                                  <div class="panel-body">

                                      <div class="col-md-6">
                                          <table class="table table-responsive table-hover">
                                            <tr>
                                                <td>Anti-lock Breaking</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Anti_lock_breaking_system']); ?></th>
                                            </tr>
                                            <tr>
                                                <td>Air Bag</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Airbag']); ?></th>
                                            </tr>
                                          </table>
                                      </div>

                                      <div class="col-md-6">
                                          <table class="table table-responsive table-hover">
                                            <tr>
                                                <td>Traction Control</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Traction_control']); ?></th>
                                            </tr>
                                            <tr>
                                                <td>Central Locking</td>
                                                <th><?php ourtickmark_icon($row_ca_car_details_single['Central_locking']); ?></th>
                                            </tr>
                                          </table>
                                      </div>
                                    </div>
                                </div>
                              </div>
                              <div class="panel panel-default">
                                <div class="panel-heading">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">
                                      <h4 class="panel-title">
                                        Capacity and Dimensions
                                      </h4>
                                    </a>
                                </div>
                                <div id="collapse4" class="panel-collapse collapse">
                                  <div class="panel-body">

                                      <div class="col-md-6">
                                          <table class="table table-responsive table-hover">
                                            <tr>
                                                <td>Seating Capacity</td><th><?php echo $row_ca_car_details_single['Seating_capacity']; ?></th>
                                            </tr>
                                            <tr>
                                                <td>Length</td><th><?php echo $row_ca_car_details_single['Length']; ?></th>
                                            </tr>
                                            <tr>
                                                <td>Height</td><th><?php echo $row_ca_car_details_single['Height']; ?></th>
                                            </tr>
                                          </table>                     
                                      </div>

                                      <div class="col-md-6">
                                          <table class="table table-responsive table-hover">
                                            <tr>
                                                <td>Fuel Capacity</td><th><?php echo $row_ca_car_details_single['Fuel_capacity_litre']; ?></th>
                                            </tr>
                                            <tr>
                                                <td>Width</td><th><?php echo $row_ca_car_details_single['Width']; ?></th>
                                            </tr>
                                            <tr>
                                                <td>Ground Clearance</td><th><?php echo $row_ca_car_details_single['Ground_clearance']; ?></th>
                                            </tr>
                                          </table>
                                      </div>
                                </div>
                              </div>
                            </div>
                    </div><!-- panel -->

                    </div><!--col-md-12 -->

                    <div class="col-md-12 text-center">
                                <a href="#orderform"><button class="more_button btn btn-success" type="Place Order">Place Order</button></a></p> 
                    </div><!--col-md-12 -->

                </div><!--row -->
            </div><!--col-md-6 -->

        </div><!--row -->
     </div><!--container -->
          
      
                         
          
    <div class="container pad_b_50">
      <div class="row">
                
                  <div class="col-md-3  slideanim pad_b_30">
                      <ul class="nav nav-pills nav-stacked">
                        <li><a href="products.php?Brand=&#red">All</a></li>
                        <li><a href="products.php?Brand=mercedes-benz#red">Mercedes</a></li>
                        <li><a href="products.php?Brand=audi#red">Audi</a></li>
                        <li><a href="products.php?Brand=bmw#red">BMW</a></li>
                        <li><a href="products.php?Brand=jaguar#red">Jaguar</a></li>
                        <li><a href="products.php?Brand=volkswagen#red">Volkswagen</a></li>
                      </ul>
                  </div><!--col-md-3 -->
          
  
          <div class="col-md-9  slideanim">
                <p class="content_hd">Our Review</p><hr class="bvdr4" />
                <p>
					<?php echo nl2br(($row_ca_car_details_single['Description']));
                    ?>
               	</p>
          </div><!--col-md-9 -->
          

        </div><!--row -->
   </div><!--container -->
   


</div><!--row -->
</div><!--container-fluid -->

   
   
    
<div class="container-fluid c1_admin" id="orderform">
    <div class="row">                    
          
                
	<div class="container pad_b_50  slideanim">
   		 <h3 class=" pad_b_30" align="center">ORDER FORM</h3>
    
			<p class="text-center"> Please fill in the form with appropriate details and submit, we will get back to you as soon as possible.</p>   <br> 	
          <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="">
            <table align="center" class="table-responsive">
              </tr>
              <tr valign="baseline">
                <td nowrap align="right"><label class="form-group">Car model:</label></td>
                <td><input class="form-control" required readonly type="text" name="Car_model" value="<?php echo $row_ca_car_details_single['Brand']; ?>&nbsp;<?php echo $row_ca_car_details_single['Model']; ?>&nbsp;<?php echo $row_ca_car_details_single['Model2']; ?>" size="32"></td>
              </tr>
              <tr valign="baseline">
                <td nowrap align="right"><label class="form-group">Your name:</label></td>
                <td><input class="form-control" required type="text" name="Name" value="" size="32"></td>
              </tr>
              <tr valign="baseline">
                <td nowrap align="right"><label class="form-group">E-mail:</label></td>
                <td><input class="form-control" type="text" name="E_mail" value="" size="32"></td>
              </tr>
              <tr valign="baseline">
                <td nowrap align="right"><label class="form-group">Phone:</label></td>
                <td><input class="form-control" required type="text" name="Phone" value="" size="32"></td>
              </tr>
              <tr valign="baseline">
                <td nowrap align="right" valign="top"><label class="form-group">Address:</label></td>
                <td><textarea class="form-control" required name="Address" cols="50" rows="5"></textarea></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td></td>
              <tr valign="baseline">
                <td nowrap align="right" valign="top"><label class="form-group">Message:</label></td>
                <td><textarea required class="form-control" name="Message" cols="50" rows="5">Please process my order as soon as possible.
                </textarea></td>
              </tr>
              <tr valign="baseline">
                <td nowrap align="right">&nbsp;</td>
                <td><input type="submit" value="Order" class="more_button2"></td>
              </tr>
            </table>
            <input type="hidden" name="Sl_no" value="">
            <input type="hidden" name="Time_stamp" value="">
            <input type="hidden" name="Task_completed" value="No">
            <input type="hidden" name="MM_insert" value="form1">
          </form>
          <p>&nbsp;</p>
        </div>
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

mysql_free_result($ca_car_details_single);

mysql_free_result($ca_orderform);
?>
