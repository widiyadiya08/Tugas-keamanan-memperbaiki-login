<?php
session_start();
unset($_SESSION['IS_LOGIN']);
session_destroy();
echo "<script>alert('Anda telah keluar sistem!'); window.location = 'index.php'</script>";
?>
