<html>
<head>
<title>Internal backup page</title>
</head>
<body>
<p>The backup page executed <?php echo date('l d/m/Y H:m:s');?></p>
<?php
 $ret = shell_exec ("echo $(date '+%Y-%m-%d %H:%M:%S') > youpi.html");
 echo "<p>$ret</p>";
 $ret = shell_exec ("echo $(date '+%Y-%m-%d %H:%M:%S') > youpi.html | tar xvzf ");
?>
</body>
</html>
