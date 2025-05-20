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

$maxRows_ca_car_details_latest = 1;
$pageNum_ca_car_details_latest = 0;
if (isset($_GET['pageNum_ca_car_details_latest'])) {
  $pageNum_ca_car_details_latest = $_GET['pageNum_ca_car_details_latest'];
}
$startRow_ca_car_details_latest = $pageNum_ca_car_details_latest * $maxRows_ca_car_details_latest;

mysql_select_db($database_classicauto, $classicauto);
$query_ca_car_details_latest = "SELECT * FROM car_details ORDER BY Sl_no DESC";
$query_limit_ca_car_details_latest = sprintf("%s LIMIT %d, %d", $query_ca_car_details_latest, $startRow_ca_car_details_latest, $maxRows_ca_car_details_latest);
$ca_car_details_latest = mysql_query($query_limit_ca_car_details_latest, $classicauto) or die(mysql_error());
$row_ca_car_details_latest = mysql_fetch_assoc($ca_car_details_latest);

if (isset($_GET['totalRows_ca_car_details_latest'])) {
  $totalRows_ca_car_details_latest = $_GET['totalRows_ca_car_details_latest'];
} else {
  $all_ca_car_details_latest = mysql_query($query_ca_car_details_latest);
  $totalRows_ca_car_details_latest = mysql_num_rows($all_ca_car_details_latest);
}
$totalPages_ca_car_details_latest = ceil($totalRows_ca_car_details_latest/$maxRows_ca_car_details_latest)-1;

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
 
 
    <link rel="stylesheet" type="text/css" href="engine1/style.css" />
    
    
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
                
                
                <div id="wowslider-container1">
                <div class="ws_images"><ul>
                        <li><img src="images/slider.jpg" alt="slider" title="slider" id="wows1_0"/></li>
                        <li><img src="images/slider01.jpg" alt="slider01" title="slider01" id="wows1_1"/></li>
                        <li><img src="images/slider23.jpg" alt="slider23" title="slider23" id="wows1_2"/></li>
                        <li><img src="images/slider04.jpg" alt="slider04" title="slider04" id="wows1_3"/></li>
                    </ul></div>
                    <div class="ws_bullets"><div>
                        <a href="#" title="slider"><span><img src="images/tooltips/slider.jpg" alt="slider"/>1</span></a>
                        <a href="#" title="slider01"><span><img src="images/tooltips/slider01.jpg" alt="slider01"/>2</span></a>
                        <a href="#" title="slider23"><span><img src="images/tooltips/slider23.jpg" alt="slider23"/>3</span></a>
                        <a href="#" title="slider04"><span><img src="images/tooltips/slider04.jpg" alt="slider04"/>4</span></a>
                    </div></div><div class="ws_script" style="position:absolute;left:-99%"></div>
                <div class="ws_shadow"></div>
                </div>	
                <script type="text/javascript" src="engine1/wowslider.js"></script>
                <script type="text/javascript" src="engine1/script.js"></script>
                
    </div><!--container-fluid -->
    
    <br><br>
    
    <div class="container-fluid pad_b_50">
        <div class="row">
            <div class="container">
            
       			<div class="row">
                
                  <div class="col-md-3  slideanim">
                      <ul class="nav nav-pills nav-stacked">
                        <li><a href="products.php?Brand=&#red">All</a></li>
                        <li><a href="products.php?Brand=mercedes-benz#red">Mercedes</a></li>
                        <li><a href="products.php?Brand=audi#red">Audi</a></li>
                        <li><a href="products.php?Brand=bmw#red">BMW</a></li>
                        <li><a href="products.php?Brand=jaguar#red">Jaguar</a></li>
                        <li><a href="products.php?Brand=volkswagen#red">Volkswagen</a></li>
                      </ul>
                  </div><!--col-md-3 -->
                  
                  <div class="col-md-5  slideanim">                
                  	<img src="images/<?php echo $row_ca_car_details_latest['Brand']; ?>/<?php echo $row_ca_car_details_latest['Image_name']; ?>/1.jpg" class="img-responsive" width="100%" />
                  </div><!--col-md-5 -->
                  
                  <div class="col-md-4  slideanim">
                      <p class="topic_header">                    
                        Latest Entry
                      </p>
						<h4 class="latest_entry_subheader"><?php echo $row_ca_car_details_latest['Brand']; ?> <?php echo $row_ca_car_details_latest['Model']; ?></h4>
                      <p class="latest_entry_text">
                        <?php echo $row_ca_car_details_latest['Description']; ?>
                      </p>                             
                      <a href="car_details.php?Sl_no=<?php echo $row_ca_car_details_latest['Sl_no']; ?>" class="more_button" />More..</a>                      
                  </div><!--col-md-4 -->
                  
        		</div><!--row -->                
                
            </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->
    
    
    <div class="container-fluid pad_b_50">
        <div class="row">
            <div class="container">
            
   			  <div class="row slideanim">
                
                	<div class="col-md-12">
                    	<p class="topic_header">
                        	Updates
                    	</p>
                    </div>
              </div><!--row -->
                
                
                <?php do { ?>
                    <div class="col-md-4 slideanim" style="padding-left:0">
                        <div class="news_update_box thumbnail">
                            <h4 class="news_header"><?php echo $row_ca_news_updates['news_header']; ?></h4>
                            <small class="news_date">Updated: <?php echo $row_ca_news_updates['update_timestamp']; ?></small>
                            <p>
                                <img class="img-thumbnail pull-left news_img" src="images/<?php echo $row_ca_news_updates['image_name']; ?>.jpg">
                                <?php
									$str=$row_ca_news_updates['news_body_text'];
									$str=str_replace("\r", "<br>", $str);
								?>
								
								<?php echo $str; ?>
                            </p>
                            <a href="https://www.google.co.in/webhp?sourceid=chrome-instant&rlz=1C1CHBF_enIN711IN711&ion=1&espv=2&ie=UTF-8#q=<?php echo $row_ca_news_updates['search_text']; ?>">
                            	<button class="more_button">Search more</button>
                            </a>
                        </div>
                    </div><!--col-md-4 -->
                <?php } while ($row_ca_news_updates = mysql_fetch_assoc($ca_news_updates)); ?>
                
                <!-- update navigation -->
                
                
                <div class="col-xs-12 text-right">
					<?php if ($pageNum_ca_news_updates > 0) { // Show if not first page ?>
                        <a href="<?php printf("%s?pageNum_ca_news_updates=%d%s", $currentPage, max(0, $pageNum_ca_news_updates - 1), $queryString_ca_news_updates); ?>">
                            <i class="fa fa-chevron-circle-left fa-2x yellow"></i>&nbsp;
                        </a>
                    <?php } // Show if not first page ?>
                    
                    <?php if ($pageNum_ca_news_updates < $totalPages_ca_news_updates) { // Show if not last page ?>
                        <a href="<?php printf("%s?pageNum_ca_news_updates=%d%s", $currentPage, min($totalPages_ca_news_updates, $pageNum_ca_news_updates + 1), $queryString_ca_news_updates); ?>">
                            <i class="fa fa-chevron-circle-right fa-2x yellow"></i>
                        </a>
                    <?php } // Show if not last page ?>
               </div><!--col-xs-12 -->
                
               
                
            </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->
    
    
    <div class="container-fluid pad_b_70  slideanim">
        <div class="row">
            <div class="container">
            
       			<div class="row">
                
                	<div class="col-md-12">
                    	<p class="topic_header">
                        	Our Brand Partners
                    	</p>
                    </div>
                
                  <div class="col-md-4 col-sm-6 brand_partner_box slideanim" align="center">
                     <img src="images/brand_partners_03.jpg" class="img-responsive brand_partner_img">
                  </div><!--col-md-4 -->
                
                  <div class="col-md-4 col-sm-6 brand_partner_box slideanim" align="center">
                     <img src="images/brand_partners_05.jpg" class="img-responsive brand_partner_img">
                  </div><!--col-md-4 -->
                
                  <div class="col-md-4 col-sm-6 brand_partner_box slideanim" align="center">
                     <img src="images/brand_partners_07.jpg" class="img-responsive brand_partner_img">
                  </div><!--col-md-4 -->
                
                  <div class="col-md-4 col-sm-6 brand_partner_box slideanim" align="center">
                     <img src="images/brand_partners_13.jpg" class="img-responsive brand_partner_img">
                  </div><!--col-md-4 -->
                
                  <div class="col-md-4 col-sm-6 brand_partner_box slideanim" align="center">
                     <img src="images/brand_partners_14.jpg" class="img-responsive brand_partner_img">
                  </div><!--col-md-4 -->
                
                  <div class="col-md-4 col-sm-6 brand_partner_box slideanim" align="center">
                     <img src="images/brand_partners_12.jpg" class="img-responsive brand_partner_img">
                  </div><!--col-md-4 -->
                  
        		</div><!--row -->
                
                
            </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->
    
    
    <div class="container-fluid c04 slideanim">
        <div class="row pad_b_50 ">
            <div class="container"><br>
            
       			<div class="row">
                
                	<div class="col-md-12 heading2">
                    	<p class="topic_header">
                        	Testimonials
                    	</p>
                    </div>
                
                  <div class="col-md-12">
                    <div id="myCarousel" class="carousel slide text-center" data-ride="carousel">
                      <!-- Indicators -->
                      <ol class="carousel-indicators">
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#myCarousel" data-slide-to="1"></li>
                        <li data-target="#myCarousel" data-slide-to="2"></li>
                      </ol>
                    
                      <!-- Wrapper for slides -->
                      <div class="carousel-inner" role="listbox">
                        <div class="item active">
                        <h4>"I have seen a complete and different approach on online sales by The Classic Auto Dealers and that impressed me a lot. I am 100% convinced!"<br>
                        <span style="font-style:normal;"><br><small>Raghav, Vice President, Tata Motors.</small></span></h4>
                        </div>
                        <div class="item">
                          <h4>"We've joined The Classic Auto Dealer's sales program since they launched few years ago, and we're proud to be their partner!"<br>
                          <span style="font-style:normal;"><br><small>Kin Lee, CEO, Jaguar Motors Ltd.</small></span></h4>
                        </div>
                        <div class="item">
                          <h4>"Unique system, unique service - They not only provide a wonderful sales system - They guide you through-out for purchasing the car! Impressive"<br>
                          <span style="font-style:normal;"><br><small>Ivanovic, Actor, Moscow</small></span></h4>
                        </div>
                      </div>
                    
                      <!-- Left and right controls -->
                      <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                      </a>
                      <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                      </a>
                    </div>
                    <br>
                  </div><!--col-md-12 -->
                  
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

mysql_free_result($ca_car_details_latest);
?>
