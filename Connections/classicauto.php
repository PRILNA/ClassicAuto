<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_classicauto = "localhost";
$database_classicauto = "classicauto";
$username_classicauto = "root";
$password_classicauto = "";
$classicauto = mysql_pconnect($hostname_classicauto, $username_classicauto, $password_classicauto) or trigger_error(mysql_error(),E_USER_ERROR); 
?>