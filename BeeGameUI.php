<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bee Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        .message {
            margin-bottom: 20px;
            font-weight: bold;
        }

        .bees-container {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .bee-type {
            text-align: center;
        }

        .bee-info {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Thalasseo est infesté par les abeilles ! </h1>
    <?php if (isset($view->message) && $view->turn > 0) : ?>
        <p class="message"><?= $view->message ?></p>
    <?php endif; ?>

    <?php if (isset($view->message)) : ?>
        <p class="message">Tour : <?= $view->turn ?></p>
    <?php endif; ?>

    <form name="input" action="play.php" method="post">
        <input type="submit" name="action" value="Viser une abeille">
    </form>

    <?php if (!empty($view->queen)) : ?>
    <div class="bees-container">
        <div class="bee-info">
            <p class="bee-type">Queen</p>
            <p>Quantité: <?= $view->queen['quantity'] ?></p>
            <p>Hit Points: <?= $view->queen['life'] ?></p>
        </div>
        
        <div class="bee-info">
            <p class="bee-type">Workers</p>
            <p>Quantité: <?= $view->worker['quantity'] ?></p>
            <p>Hit Points: <?= $view->worker['life'] ?></p>
        </div>

        <div class="bee-info">
            <p class="bee-type">Scouts</p>
            <p>Quantité: <?= $view->drone['quantity'] ?></p>
            <p>Hit Points: <?= $view->drone['life'] ?></p>
        </div>
    </div>
<?php endif; ?>

</body>

</html>
