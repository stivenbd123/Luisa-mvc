<?php
session_start();
session_destroy();
header("Location: ../views/html/auth/login.php");
exit();