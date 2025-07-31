<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'TECHNOVAServices') ?></title>
    <!-- Responsivité gerer par BOOSTRAP -->
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
    <link rel="stylesheet" href="/site-informatique/assets/css/footer.css">
    <?php if (isset($css)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="/site-informatique/assets/js/main.js" defer></script>
</head>

<body>
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <!-- Contenu de la page récuperer dans la variable content  qui sera ensuite envoyer controlleur  -->
    <?= $content ?? '' ?>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>
