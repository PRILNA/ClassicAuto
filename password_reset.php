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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE userdetails SET Password=%s WHERE Username=%s",
                       GetSQLValueString($_POST['Password'], "text"),
                       GetSQLValueString($_POST['Username'], "text"));

  mysql_select_db($database_classicauto, $classicauto);
  $Result1 = mysql_query($updateSQL, $classicauto) or die(mysql_error());
}

$colname_ca_password_reset = "-1";
if (isset($_GET['username_search'])) {
  $colname_ca_password_reset = $_GET['username_search'];
}
$coldob_ca_password_reset = "-1";
if (isset($_GET['dob_search'])) {
  $coldob_ca_password_reset = $_GET['dob_search'];
}
mysql_select_db($database_classicauto, $classicauto);
$query_ca_password_reset = sprintf("SELECT Username, Password, DOB FROM userdetails WHERE Username = %s AND DOB = %s", GetSQLValueString($colname_ca_password_reset, "text"),GetSQLValueString($coldob_ca_password_reset, "date"));
$ca_password_reset = mysql_query($query_ca_password_reset, $classicauto) or die(mysql_error());
$row_ca_password_reset = mysql_fetch_assoc($ca_password_reset);
$totalRows_ca_password_reset = mysql_num_rows($ca_password_reset);
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
<body>
    
    <div class="container-fluid header">
        <div class="row">
            <div class="container ">
            
                	<div class="col-md-6">
                    <nav class="navbar">
                        <div class="navbar-header">
                          <img src="images/logo_03.png" class="img-responsive" style="margin:10px 10px 20px 0"/>
                        </div>
                    </nav>
                    </div>
                    
                	<div class="col-md-6 animationbox hidden-xs  hidden-sm">
                    	<img class="img-thumbnail gandhi pull-right" src="images/mkganfdhi.jpg">
                        <p class="gandhitext">“A customer is not an interruption of our work.
                        He is the purpose of it. We are not doing him a favour by serving him.
                        He is doing us a favour by giving us the opportunity to do so.”
                        </p>
                        <img src="images/2000px-Gandhi_sigfnature.png" class="img-responsive gandhisign  pull-right">
                    </div>
                    
          </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->
    
    
    <div class="container-fluid c1">
        <div class="row">
            <div class="container ">
                <div class="col-md-7">
                        <img src="images/car_19.jpg" class="img-responsive" />
                </div><!--col-md-7 -->
                <div class="col-md-5">
                    <div class="row">
                        <div class="login">
                            <div class="col-md-12">
                                <h2>Password Reset</h2>
                            </div>
                            <div class="col-md-6">
                            	
                            	<h5 id="step1head" class="text-info">Step 1 - Validate User <i class="fa fa-check" id="step1tick"></i></h5>
                                <form METHOD="get" name="username_search" id="step1formbox">
                                                          <input id="username_search" required type="text" class="form-control" name="username_search" placeholder="Username">
                                                          <input id="dob_search" required type="date" class="form-control" name="dob_search">
                                                          <input id="search" type="submit" class="form-control btn-warning" name="search" value="Search">
                                </form>
                            </div><!--col-md-6 -->
                            <div class="col-md-6" id="step2formbox">
                           	  <h5 class="text-info">Step 2 - Update Password</h5>
                              <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                                <table align="center" class="table-responsive">
                                  <tr valign="baseline">
                                    <td><div id="username_result" class="username_result" style="margin-bottom:12px; text-align:left"><?php echo $row_ca_password_reset['Username']; ?></div></td>
                                  </tr>
                                  <tr valign="baseline">
                                    <td><input id="password_update_col" type="password" name="Password" value="" class="form-control" placeholder="New Password"></td>
                                  </tr>
                                  <tr valign="baseline">
                                    <td><input id="update_password_btn" type="submit" value="Update Password" class="form-control btn-success"></td>
                                  </tr>
                                </table>
                                <input type="hidden" name="MM_update" value="form1">
                                <input type="hidden" name="Username" value="<?php echo $row_ca_password_reset['Username']; ?>">
                              </form>
<!--script for successful search to set green colour text and tick mark-->
                              <script>
								if ((document.getElementById("username_result").innerHTML)==""){
									document.getElementById("step1tick").style.display="none";
									document.getElementById("step2formbox").style.opacity=".5";
									document.getElementById("password_update_col").disabled="true";
									document.getElementById("update_password_btn").disabled="true";
									}
								else{
									document.getElementById("step1tick").style.display="inline";
									document.getElementById("step1head").style.color="#5cb85c";
									document.getElementById("step1formbox").style.opacity=".5";
									document.getElementById("username_search").disabled="true";
									document.getElementById("dob_search").disabled="true";
									document.getElementById("search").disabled="true";
									document.getElementById("search").color="#333";
									}
                              </script>
                              
                              
                              
                            </div>
                            <!--col-md-6 -->
                            <div class="col-md-12">
                                <a class="pull-right" href="index.php">Go Back</a><br>
                            </div>
                            <div class="clearfix"></div>
                        </div><!--login -->
                    </div><!--row -->
                </div><!--col-md-5 -->
            </div><!--container -->
        </div><!--row -->
    </div><!--container-fluid -->
    
    
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
mysql_free_result($ca_password_reset);
?>
