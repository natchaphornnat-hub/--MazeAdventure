
<?php
session_start();

$gameName = "Maze Adventure";
$playerName = "นักผจญภัย";
$score = 0;
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $gameName; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="game-container">

    <h1>🗝️ เกมเขาวงกตผจญภัย</h1>

    <h2>ยินดีต้อนรับ <?php echo $playerName; ?></h2>

    <p>เดินทางผ่านเขาวงกต ตอบคำถาม และค้นหาทางออก!</p>

    <p>⭐ คะแนน: <?php echo $score; ?></p>

    <form action="game.php" method="POST">
        <button type="submit">🎮 เริ่มเกม</button>
    </form>
    </div>
</body>
</html>
