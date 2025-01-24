<?php
if (time() > $_SESSION['timeout']) {
    session_unset(); 
    session_destroy();
    header('Location: login.html');
    exit();
  }
?>