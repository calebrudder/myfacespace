<!DOCTYPE html>
<html>

<head>
    <title>MyFaceSpace: logout</title>
</head>

<body>
    <?php
        session_start();
        session_destroy();
        header("Location: login.php");
    ?>
</body>

</html>
