<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_newsletter = "localhost";
$database_newsletter = "s3mer_newsletter";
$username_newsletter = "s3mer_newsletter";
$password_newsletter = "EzF3rJ4vs87e";
$newsletter = mysql_pconnect($hostname_newsletter, $username_newsletter, $password_newsletter) or trigger_error(mysql_error(),E_USER_ERROR); 
?>