<?php require_once('Connections/classicauto.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "user_add")) {
  $insertSQL = sprintf("INSERT INTO userdetails (Username, Password, Full_name, DOB) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['Username_add'], "text"),
                       GetSQLValueString($_POST['Password_add'], "text"),
                       GetSQLValueString($_POST['Full_name_add'], "text"),
                       GetSQLValueString($_POST['DOB_add'], "date"));

  mysql_select_db($database_classicauto, $classicauto);
  $Result1 = mysql_query($insertSQL, $classicauto) or die(mysql_error());

  $insertGoTo = "index.php";
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
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username_login'])) {
  $loginUsername=$_POST['username_login'];
  $password=$_POST['Password_login'];
  $MM_fldUserAuthorization = "Access_level";
  $MM_redirectLoginSuccess = "home.php";
  $MM_redirectLoginFailed = "index.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_classicauto, $classicauto);
  	
  $LoginRS__query=sprintf("SELECT Username, Password, Access_level FROM userdetails WHERE Username=%s AND Password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $classicauto) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'Access_level');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
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
    
    
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>

<body>
    
    <div class="container-fluid header">
        <div class="row">
            <div class="container ">
            
                	<div class="col-md-6">
                    <nav class="navbar">
                        <div class="navbar-header">
                          <img src="images/logo_03.png" class="img-responsive" style="margin:10px 10px 20px 0"/>
                        </div>
                        
                        <a href="#project" data-toggle="modal"><div class="project">Project<br>Info</div></a>
                        
                    </nav>
                    </div>
                    
                	<div class="col-md-6 animationbox hidden-xs  hidden-sm">
                    	<img class="img-thumbnail gandhi pull-right" src="images/mkganfdhi.jpg">
                        <p class="gandhitext">“A customer is not an interruption of our work.
                        He is the purpose of it. We are not doing him a favour by serving him.
                        He is doing us a favour by giving us the opportunity to do so.”
                        </p>
                        <img src="images/2000px-Gandhi_sigfnature.png" class="gandhisign  pull-right">
                    </div>
                    
                    
                    
                    <div id="project" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                    
                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">The Classic Auto Dealers</h4>
                          </div>
                          <div class="modal-body">
                                <p>
                                
                            	<strong>Project</strong>
                                <p>Bootstrap powered PHP website for online car sales with user accounts, options to manage products, news, customer orders and messages.</p>
                                </p><br>
                          
                            <p>
                            	<strong>Batch</strong>
								<p> Bijin Kumar P, Prilna PV, Vipin PV, Shamseer CH, Suraj KV, Jeemon Puthusseri
                                </p><br>
                                
                                <p>
                            	<strong>Project Features</strong>
                                <ul class="list-group">
                                  <li class="list-group-item">Dynamic web content connected with 5 database tables</li>
                                  <li class="list-group-item">Responsive layout with Bootstrap</li>
                                  <li class="list-group-item">Log in - Log out function</li>
                                  <li class="list-group-item">User creation option</li>
                                  <li class="list-group-item">Password reset with user validation</li>
                                  <li class="list-group-item">Admin panel to manage web content and customer interaction<br><small>Available only to admin level users</small></li>
                                  <li class="list-group-item">Admin can handle customer orders and messages</li>
                                  <li class="list-group-item">Responsive sliders</li>
                                  <li class="list-group-item">CSS animation</li>
                                  <li class="list-group-item">Smooth sliding</li>
                                </ul>
                                <ul>
                                  <li>
                                      <h4 class="list-group-item-heading">8 pages only</h4>
                                      <p class="list-group-item-text">
                                      	The website contain only 8 PHP pages! But they can turn into inifinite no. of web pages based on no. of products in  the database. These pages also fetch images of each individual product from its speicific folders!
                                      </p>
                                  </li><br>
                                  <li>
                                      <h4 class="list-group-item-heading"> Admin panel</h4>
                                      <p class="list-group-item-text">
                                      	Admin panel is the page where only the Administrator level users have the access to. Using Admin panel, the web administrator can 		access, edit or add web contents, view customer messages, view 	product orders placed by the customers and also manage its users. So the web adminitrator does not have to be a web coding/database expert to manage the web site content.
                                      </p>
                                  </li><br>
                                  <li>
                                      <h4 class="list-group-item-heading">Responsive layout</h4>
                                      <p class="list-group-item-text">
                                      	Our PHP web page is powered by Bootstrap to make it responsive and adaptive to the screen size of the users.
                                      </p>
                                  </li><br>
                                  <li>
                                      <h4 class="list-group-item-heading">Latest entry first</h4>
                                      <p class="list-group-item-text">
                                      	Database entries are heirarchialy treated by the webpage to ensure the latest news and product entries are displayed first.
                                      </p>
                                  </li><br>
                                  <li>
                                      <h4 class="list-group-item-heading">CSS 'slide-in' animation and smooth sliding</h4>
                                      <p class="list-group-item-text">
                                      	CSS animation effect that gets initiated only when the element is in the visibility of the user. Smooth sliding is also embedded to enhance user experience while sliding back to the top section of the page from the bottom.
                                      </p>
                                  </li><br>
                                </ul>
                                
                                
                                
                            
                            </p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                    
                      </div>
                    </div>
                    
                    
          </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->
    
    
    <div class="container-fluid c1">
        <div class="row">
            <div class="container ">
                <div class="col-md-7">
                        <img src="images/car_18.jpg" class="img-responsive" />
                </div><!--col-md-7 -->
                <div class="col-md-5">
                    <div class="row">
                        <div class="login">
                            <div class="col-md-12">
                                <h2 class="login_txt">Log In</h2>
                            </div>
                            <div class="col-md-6 lgn">
                                <a style="background-color:#3b5998;" href="#">LIKE US ON FACEBOOK</a>
                                <a style="background-color:#2ca7e0;" href="#">FOLLOW US ON TWITTER</a>
                                <a style="background-color:#db4437;" href="#">ADD US ON GOOGLE<sup>+</sup></a>
                            </div><!--col-md-6 -->
                            <div class="col-md-6">
                                <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" name="login_form">
                                    <input type="text" name="username_login" placeholder="Username" class="inputfield form-control" />
                                    <input type="password" name="Password_login" placeholder="Password" class="inputfield form-control" />
                                    <input type="submit" name="submit_login" value="Sign In"  class="inputfield form-control btn btn-success"/>
                                </form>
                            </div><!--col-md-6 -->
                            <div class="col-md-12">
                                <a class="pull-right" href="#createaccount_modal" data-toggle="modal">Create Account</a><br>
                                <a class="pull-right" href="password_reset.php">Forgot Password?</a>
                            </div>
                            <div class="clearfix"></div>
                        </div><!--login -->
                    </div><!--row -->
                </div><!--col-md-5 -->
            </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->
    
    
    
    
    
<!--Hidden in page, used as popup the page-->                        
                                             
    <div id="createaccount_modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Create New Account</h4>
          </div>
          <div class="modal-body">
            <h3 class="model-h3">Sign up now!</h3>
                <ul>
                    <li>All fields are mandatory.</li>
                    <li>Please make sure you enter valid user details.</li>
                    <li>Once the form is submitted, you will be directed to the login page.</li>
            </ul><br>
            	
                    <form method="post" name="user_add" action="<?php echo $editFormAction; ?>">
                      <table align="center" cellpadding="5" class="table-responsive">
                        <tr valign="top">
                          <td align="right" nowrap class="text-info">
                              Username:&nbsp;&nbsp;&nbsp;
                          </td>
                          <td>
                              <input class="form-control" type="text" name="Username_add" id="Username_add" required  placeholder="eg: ramesh" value="" pattern="[A-Za-z0-9_]{3,20}">
                                <span class="help-block small"><cite>
                                    Only letters, numbers and underscore are allowed.<br>Minimum 3 characters and not more than 20 characters length.
                                </cite></span>
                          </td>
                        </tr>
                        <tr valign="top">
                          <td align="right" nowrap class="text-info">
                              Password:&nbsp;&nbsp;&nbsp;
                          </td>
                          <td>
                              <input class="form-control"  type="password" name="Password_add" required placeholder="Password here" id="Password" value="">
                              
                          </td>
                        </tr>
                        <tr valign="top">
                          <td align="right" nowrap class="text-info">
                              Full Name:&nbsp;&nbsp;&nbsp;
                          </td>
                          <td>
                              <input class="form-control" type="text" name="Full_name_add" required placeholder="eg: Ramesh Pisharadi" id="Full_name_add" value="">
                          </td>
                        </tr>
                        <tr valign="top">
                          <td align="right" nowrap class="text-info">
                              Date of Birth:&nbsp;&nbsp;&nbsp;
                          </td>
                          <td>
                              <input class="form-control" type="date" name="DOB_add" id="DOB_add" required value="" size="32" placeholder="dd-mm-yyyy">
                              <span class="help-block small"><cite>
                                  Please enter the valid Date of Birth.<br><span class="text-warning">Date of Birth will be required for password reset.</span>
                              </cite></span>
                          </td>
                        </tr>
                        <tr valign="middle">
                          <td nowrap>&nbsp;</td>
                          <td><input class="btn btn-success" type="submit" value="Sign Up"></td>
                        </tr>
                      </table>
                      <input type="hidden" name="MM_insert" value="user_add">
                    </form>
            
            
            <p>&nbsp;</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div><!--createaccount_modal -->
    
             
                                             
    <div id="createaccount_modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Create New Account</h4>
          </div>
          <div class="modal-body">
            <h3 class="model-h3">Sign up now!</h3>
                <ul>
                    <li>All fields are mandatory.</li>
                    <li>Please make sure you enter valid user details.</li>
                    <li>Once the form is submitted, you will be directed to the login page.</li>
            	</ul><br>
                	
            	<p>&nbsp;</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div><!--createaccount_modal -->
    
    
    
    
    
    <div class="container-fluid c1" style="padding-top:44px;">
        <div class="row white_font">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <table border="0">
                          <tr>
                            <td valign="top"><i class="fa fa-globe fa-5x bg_green" aria-hidden="true"></i></td>
                            <td>
                                <h4>Global Delivery</h4>
                                <p class="feature-lp-text small">
                                    Our worldwide network of service delivery centers operate quickly and promptly to deliver the products ordered by customers anywhere around the world without any hassle. We also have logistics contract with Jet Airways and FedEx to deal with any unpredicted transportation issues.
                                </p>
                            </td>
                          </tr>
                        </table>
                    </div><!--col-md-4 -->
                    <div class="col-md-4">
                        <table border="0">
                          <tr>
                            <td valign="top"><i class="fa fa-comments fa-5x bg_green" aria-hidden="true"></i></td>
                            <td>
                                <h4>24x7 Customer Care</h4>
                                <p class="feature-lp-text small">
                                    We have special customer service division that offers 24x7 connectivity to the customers both through phone and e-mail. Our customer service division handles all our service related queries, suggestions, requests as well as complaints with a maximum turn around time of 6 hours. 
                                </p>
                            </td>
                          </tr>
                        </table>
                    </div><!--col-md-4 -->
                    <div class="col-md-4">
                        <table border="0">
                          <tr>
                            <td valign="top"><i class="fa fa-check-circle fa-5x bg_green" aria-hidden="true"></i></td>
                            <td>
                                <h4>Quality Assurance</h4>
                                <p class="feature-lp-text small">
                                    We have our own expert Quality Inspection Team to test and verify the features of the vehicle mentioned by the manufactors are absolutely on par. We have also introduced simulated weather and terrain testing which is performed only as per customer request at the time of ordering. 
                                </p>
                                </td>
                          </tr>
                        </table>
                    </div><!--col-md-4 -->
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
