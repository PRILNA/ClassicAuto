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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Classic Auto Dealers</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="css/my_style.css" rel="stylesheet" type="text/css" />
    <link href="css/ihover.css" rel="stylesheet" />
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
                      <li class="breadcrumb-item active">About us</li>
                    </ol>
                 </div><!--col-md-12 -->
             </div><!--row -->
          </div><!--container -->
          
          <div class="container"> 
             <div class="row">
             	<div class="col-md-12">
              		<h2 class="about_heading">About us</h2>
                 </div><!--col-md-12 -->
             </div><!--row -->
          </div><!--container -->
          
          <div class="container"> 
            <div class="row about_para_part">
             	<div class="col-md-12">
                     <p><img src="images/about_img_03.jpg" class="img-responsive about_img" /><span class="about_para">The Classic Auto Dealers was created in 2006 out of a passion to bring car enthusiasts the car they love delivered right at their doorstep. The six-men operation saw rapid growth after delivering our first order to the world’s best car racer - Michael Schumacher!.</span>
                     </p>
                    <p><span class="about_para">Through word of mouth, a lot of hard work and a commitment to become world’s best premium car dealers, The Classic Auto Dealers is now one of the most beloved premium car dealer in the world.</span>
                    </p>
                    <p><span class="about_para"> We believe that sticking to  inflexible quality checking and commitment given to the customer bring the best results. We are tireless in staying upto date of car trends. And we are obsessed with communicating and helping customers in their decision making process as well as service post purchase. Further more, we also ensure that we deliver every service and care our brand partners would deliver to their direct customers..!</span>
                    </p>
                    <p><span class="about_para"> We hold more than 10 years of auto dealer experience. Started in Kannur in 2006, it remains the core center of all our operations today. In 2010 we became No.1<sup>#</sup> premium car dealer in Inida. We have over 50 employees in our Kannur headquarters and over 3000 direct staffs around the globe.</span>
                    </p>
                    <p><span class="about_para">  Fast forward to 2016, we have delivered over 40000 cars world wide and by now we are the front runners in the world market. With extensive experience in premium car business we understand what a car dealer need to provide to their customers. With the great support and feedback we receive from our customers around the world, we expect to grow bigger and better and more importantly very much dependible and trustworthy to our customers!</span>
                    </p>
                </div><!--col-md-12 -->
            </div><!--row -->
          </div><!--container -->
         
           <div class="container">
           <div class="row">
                
                  <div class="col-md-3 pad_b_50 slideanim">
                      <ul class="nav nav-pills nav-stacked">
                        <li><a href="products.php?Brand=&#red">All</a></li>
                        <li><a href="products.php?Brand=mercedes-benz#red">Mercedes</a></li>
                        <li><a href="products.php?Brand=audi#red">Audi</a></li>
                        <li><a href="products.php?Brand=bmw#red">BMW</a></li>
                        <li><a href="products.php?Brand=jaguar#red">Jaguar</a></li>
                        <li><a href="products.php?Brand=volkswagen#red">Volkswagen</a></li>
                      </ul>
                  </div><!--col-md-3 -->
               
              <div class="col-md-9 pad_b_50">
                  <div class="col-md-1"></div><!--col-md-1 -->
                  <div class="col-md-10 quote slideanim">
                     <i class="fa fa-quote-left fa-5x quote_color" aria-hidden="true"></i><br>
                     <blockquote>
                         <p class="author">
                         Great service from start to finish! The friendliest group of employees! They were all helpful, reasonable and very professional. I used them when I bought my first ferrari back in 2015. I recommend them to every premium car customers. Thumbs way up! Even the delivery executives that showed up had the best attitude and adequate knowledge to do the wonderful job!"
                         </p>
                         <footer class="pull-right"><i>Joseph Stalin, Canada</i></footer>
                     </blockquote>
                  </div><!--col-md-10 -->
                  <div class="col-md-1"></div><!--col-md-1 -->
              </div><!--col-md-9 -->
                  
                </div><!--row -->
	       </div><!--container -->
          
      </div><!--row -->
        </div><!--container-fluid -->


   
    <div class="container-fluid c1 pad_b_30">
       <div class="row">
         <div class="container pad_b_30">
           <div class="row">
                <h3 class="heading  slideanim" align="center">OUR WEB DEVELOPERS</h3>
                
                    
                    <div class="col-md-4 col-sm-6 about_md_txt slideanim">
                         <div class="ih-item circle effect16 right_to_left center-block">
                             <a href="#">
                                <div class="img">
                                    <img src="images/bijin.jpg" class="img-circle img_border" /></div><!--img-->
                                    <div class="info">
                                    <h3 class="about_team_name">BIJIN KUMAR P</h3>
                                    <p class="about_team_role">Layout Designer<br>UI Designer</p>
                                </div><!--info-->
                            </a>
                        </div><!--ih-item-->
                    </div><!--col-md-4-->
                    
                    
                    <div class="col-md-4 col-sm-6 about_md_txt  slideanim">
                         <div class="ih-item circle effect16 right_to_left center-block">
                             <a href="#">
                                <div class="img">
                                    <img src="images/prilna.jpg" class="img-circle img_border" /></div><!--img-->
                                    <div class="info">
                                    <h3 class="about_team_name">PRILNA PV</h3>
                                    <p class="about_team_role">Web Designer</p>
                                </div><!--info-->
                            </a>
                        </div><!--ih-item-->
                    </div><!--col-md-4-->
                    
                    <div class="visible-sm-block"></div>
                    
                    
                    <div class="col-md-4 col-sm-6 about_md_txt  slideanim">
                         <div class="ih-item circle effect16 right_to_left center-block">
                             <a href="#">
                                <div class="img">
                                    <img src="images/vipin.jpg" class="img-circle img_border" /></div><!--img-->
                                    <div class="info">
                                    <h3 class="about_team_name">VIPIN PV</h3>
                                    <p class="about_team_role">Web Designer</p>
                                </div><!--info-->
                            </a>
                        </div><!--ih-item-->
                    </div><!--col-md-4-->
                    
                    
                    <div class="col-md-4 col-sm-6 about_md_txt  slideanim">
                         <div class="ih-item circle effect16 right_to_left center-block">
                             <a href="#">
                                <div class="img">
                                    <img src="images/shamseer.jpg" class="img-circle img_border" /></div><!--img-->
                                    <div class="info">
                                    <h3 class="about_team_name">SHAMSEER CH</h3>
                                    <p class="about_team_role">Front-End Developer<br>Tester</p>
                                </div><!--info-->
                            </a>
                        </div><!--ih-item-->
                    </div><!--col-md-4-->
                    
                    <div class="visible-sm-block"></div>
                    
                    
                    <div class="col-md-4 col-sm-6 about_md_txt  slideanim">
                         <div class="ih-item circle effect16 right_to_left center-block">
                             <a href="#">
                                <div class="img">
                                    <img src="images/suraj.jpg" class="img-circle img_border" /></div><!--img-->
                                    <div class="info">
                                    <h3 class="about_team_name">SURAJ KV</h3>
                                    <p class="about_team_role">Front-End Developer<br>Tester</p>
                                </div><!--info-->
                            </a>
                        </div><!--ih-item-->
                    </div><!--col-md-4-->
                    
                    
                    <div class="col-md-4 col-sm-6 about_md_txt  slideanim">
                         <div class="ih-item circle effect16 right_to_left center-block">
                             <a href="#">
                                <div class="img">
                                    <img src="images/jeemon.jpg" class="img-circle img_border" /></div><!--img-->
                                    <div class="info">
                                    <h3 class="about_team_name">JEEMON PUTHUSSERI</h3>
                                    <p class="about_team_role">Web Developer</p>
                                </div><!--info-->
                            </a>
                        </div><!--ih-item-->
                    </div><!--col-md-4-->
                    
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
