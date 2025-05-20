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
  $insertSQL = sprintf("INSERT INTO web_messages (Name, E_mail, Message) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['Name'], "text"),
                       GetSQLValueString($_POST['E_mail'], "text"),
                       GetSQLValueString($_POST['Message'], "text"));

  mysql_select_db($database_classicauto, $classicauto);
  $Result1 = mysql_query($insertSQL, $classicauto) or die(mysql_error());

  $insertGoTo = "contact.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_classicauto, $classicauto);
$query_ca_user = "SELECT * FROM userdetails";
$ca_user = mysql_query($query_ca_user, $classicauto) or die(mysql_error());
$row_ca_user = mysql_fetch_assoc($ca_user);
$totalRows_ca_user = mysql_num_rows($ca_user);
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
                      <li class="breadcrumb-item"><a href="#">Home</a></li>
                      <li class="breadcrumb-item active">Contact us</li>
                    </ol>
                 </div><!--col-md-12 -->
             </div><!--row -->
          </div><!--container --> 
          
          <div class="container"> 
             <div class="row">
             	<div class="col-md-12">
              		<h2 class="about_heading">Contact us</h2>
                 </div><!--col-md-12 -->
             </div><!--row -->
          </div><!--container -->  
          
          <div class="container"> 
            <div class="row about_para_part">
             	<div class="col-md-12">
                    <p>
                    	<span class="about_para">
                        	Our customer care division works 24x7 - 365 days! Contact us through e-mail, web message or  customer care number, we will get back to you within the least possible time! Our official turn around time is as minimum as 3 hours. So don’t hesitate to reach us with your query, suggestion or complaint.
                        </span>
                    </p>
                </div><!--col-md-12 -->
            </div><!--row -->
          </div><!--container -->
          
          
          
          <div class="container pad_b_50"> 
            <div class="row">
                <div class="col-md-6">
                    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                        <div class="col-md-6 form-group">
                          <input class="form-control" id="name" name="Name" placeholder="Name" type="text" required>
                        </div>
                        <div class="col-md-6 form-group">
                          <input class="form-control" id="email" name="E_mail" placeholder="Email" type="email" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <textarea class="form-control" id="message" name="Message" placeholder="Message" rows="6"></textarea><br>
                            <input type="submit" class="btn more_button pull-right" value="Send">
                        </div>
                            <input type="hidden" name="MM_insert" value="form1">
                        <div class="clearfix"></div>
                    </form>
                </div><!--col-md-6 -->

                <div class="col-md-6">
                	<div class="col-md-2"><br><br></div><!--col-md-2 -->
                	<div class="col-md-8">
                    	<p>Contact us and we'll get back to you within 24 hours.</p><br>
                    	<p><span class="glyphicon glyphicon-map-marker fa-1x contact_sign_in_color"></span> <span class="contact_fontawesm">The Classic Auto Dealers,<br /></span>
                    	<span class="contact_fontawesm text" style="padding-left:43px;">Bank Road, Kannur</span></p>
                    	<p><span class="glyphicon glyphicon-envelope  contact_sign_in_color"></span> <span class="contact_fontawesm">classicautodealers@gmail.com</span></p>
                    	<p><span class="glyphicon  glyphicon-phone contact_sign_in_color"></span> <span class="contact_fontawesm">0490-123456789</span></p>
                    </div><!--col-md-8 -->
                    <div class="col-md-2"></div><!--col-md-2 -->
                </div><!--col-md-6 -->
            </div><!--row -->
          </div><!--container -->
         
            <div class="container">
                <div class="row">
                
                  <div class="col-md-3 slideanim pad_b_50">
                      <ul class="nav nav-pills nav-stacked">
                        <li><a href="products.php?Brand=&#red">All</a></li>
                        <li><a href="products.php?Brand=mercedes-benz#red">Mercedes</a></li>
                        <li><a href="products.php?Brand=audi#red">Audi</a></li>
                        <li><a href="products.php?Brand=bmw#red">BMW</a></li>
                        <li><a href="products.php?Brand=jaguar#red">Jaguar</a></li>
                        <li><a href="products.php?Brand=volkswagen#red">Volkswagen</a></li>
                      </ul>
                  </div><!--col-md-3 -->
                   
                  <div class="col-md-9 slideanim pad_b_50">
                    <div class="visible-sm-block visible-xs-block pad_b_30"></div>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3904.5214293982594!2d75.36394591441105!3d11.868735191585989!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba43d354a43e15f%3A0xcddf4057cd652e88!2sBank+Rd%2C+Padanapalam%2C+Kannur%2C+Kerala!5e0!3m2!1sen!2sin!4v1478957308222" width="100%" height="290px" frameborder="0" style="border:0" allowfullscreen></iframe>
                  </div><!--col-md-9 -->
                      
                </div><!--row -->
            </div><!--container -->
          
		</div><!--row -->
	</div><!--container-fluid -->
      
      <div class="container-fluid c1">
        	<div class="container pad_b_50">
            	<div class="row white_font">
                	<h3 class="heading  slideanim" align="center">OUR WORKING HOURS</h3>
                    <br><br>
                      <div class="col-md-6 pad_b_30" align="center">
                         <div class="row">
                            <div class="col-md-6 col-sm-6  slideanim">
                            	<h4 class="about_team_name">CUSTOMER CARE</h4>
                        		<p class="about_team_role">Phone, Email, Web</p>
                              	<div class="hours">
                                	<span class="hour_text_1">24</span>
                                    <span class="hour_text_2">HOURS</span>
                                    <span class="hour_text_3">365 DAYS</span>
                              	</div><!--hours -->
                            </div><!--col-md-6-->
                            <div class="col-md-6 col-sm-6  slideanim">
                    			<div class="visible-xs-block pad_b_30"></div>
                            	<h4 class="about_team_name">OFFICE HOURS</h4>
                        		<p class="about_team_role">(Except Public Holidays)</p>
                              	<div class="hours">
                                	<span class="hour_text_1">14</span>
                                    <span class="hour_text_2">HOURS</span>
                                    <span class="hour_text_3">8AM - 10PM</span>
                              	</div><!--hours -->
                            </div><!--col-md-6-->
                         </div><!--row -->

                        </div><!--col-md-6 -->
                        <div class="col-md-6 slideanim">
                           <div class="row">
                              <div class="col-md-1"></div><!--col-md-1 --><div class="visible-xs-block visible-sm-block pad_b_50"></div>
                                 <div class="col-md-10 border2">
                            	<h4 class="about_team_name">OUR SERVICE MOTTO</h4>
                                    <p class="hrs_para"><span><i><br><br>
                                    “A customer is the most important visitor on our premises. He is not dependent on us. We are dependent on him. He is not an interruption of our work. He is the purpose of it. He is not an outsider of our business. He is part of it. We are not doing him a favour by serving him. He is doing us a favour by giving us the opportunity to do so.”
                                    </i></span> <br /></p>
                                    <footer class="pull-right text-white">-MK Gandhi</span></footer><br><br><br>
                                 </div><!--col-md-10 -->
                              <div class="col-md-1"></div><!--col-md-1 -->
                           </div><!--row -->
                        </div><!--row -->
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
?>
