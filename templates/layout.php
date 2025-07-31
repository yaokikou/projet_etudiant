<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
    <link rel="stylesheet" href=<?= $css ?>>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="/site-informatique/assets/js/main.js" defer></script>
</head>

<body>
    <?php require_once '../includes/header.php'; ?>

    <?= $content ?>
    
    <?php require_once '../includes/footer.php'; ?>
</body>

</html>