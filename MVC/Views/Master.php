<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Khách Sạn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./Public/Css/style_hello.css">
    <link rel="stylesheet" href="./Public/Css/login_style.css">
    <link rel="stylesheet" href="./Public/Css/admin_style.css"> 
    <link rel="stylesheet" href="./Public/Css/department_style.css">
</head>
<body>
    <body class="<?php echo isset($_GET['controller']) ? $_GET['controller'] : 'Default'; ?>">
    <?php echo $data['content']; ?>
</body>
</body>
</html>