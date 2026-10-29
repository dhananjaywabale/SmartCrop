<?php

session_start();

// Destroy user session
session_unset();
session_destroy();

// Redirect to login page
echo "<script>
    alert('Logout successful!');
    window.location.href = '../html/login.html';
</script>";

?>