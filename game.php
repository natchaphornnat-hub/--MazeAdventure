
<?php
session_start();

$questions = [
    [
        "question" => "HTML ใช้สำหรับทำอะไร?",
        "choices" => [
            "A" => "สร้างโครงสร้างเว็บไซต์",
            "B" => "ตกแต่งสีเว็บไซต์",
            "C" => "จัดการฐานข้อมูล",
            "D" => "เปิดเว็บเซิร์ฟเวอร์"
        ],
        "answer" => "A"
    ],
    [
        "question" => "CSS ใช้สำหรับทำอะไร?",
        "choices" => [
            "A" => "เชื่อมต่อฐานข้อมูล",
            "B" => "ตกแต่งหน้าเว็บไซต์",
            "C" => "สร้างเว็บเซิร์ฟเวอร์",
            "D" => "ประมวลผล PHP"
        ],
        "answer" => "B"
    ],
    [
        "question" => "PHP ทำงานที่ส่วนใดเป็นหลัก?",
        "choices" => [
            "A" => "หน้าจอผู้ใช้",
            "B" => "แป้นพิมพ์",
            "C" => "ฝั่งเซิร์ฟเวอร์",
            "D" => "ไฟล์ CSS"
        ],
        "answer" => "C"
    ]
];

// แผนที่เขาวงกต
// 1 = กำแพง, 0 = ทางเดิน, 2 = ทางออก


$mazes = [
    // ด่านที่ 1 : HTML
    1 => [
        [1,1,1,1,1,1,1],
        [1,0,0,3,1,2,1],
        [1,0,1,0,1,0,1],
        [1,0,1,0,0,0,1],
        [1,0,1,1,1,0,1],
        [1,0,0,0,0,0,1],
        [1,1,1,1,1,1,1]
    ],

    // ด่านที่ 2 : CSS   
    2 => [
        [1,1,1,1,1,1,1],
        [1,0,1,0,0,2,1],
        [1,0,1,0,1,0,1],
        [1,0,0,0,1,0,1],
        [1,1,1,0,1,0,1],
        [1,3,0,0,0,0,1],
        [1,1,1,1,1,1,1]
    ],
     // ด่านที่ 3 : PHP
    3 => [
        [1,1,1,1,1,1,1],
        [1,0,1,0,0,2,1],
        [1,0,1,0,1,0,1],
        [1,0,0,0,1,0,1],
        [1,1,1,0,1,0,1],
        [1,0,0,0,0,3,1],
        [1,1,1,1,1,1,1]
]


];

// เริ่มเกมใหม่เมื่อยังไม่มีตำแหน่งตัวละคร
if (!isset($_SESSION['player'])) {
    $_SESSION['player'] = [1, 1];
    $_SESSION['steps'] = 0;
    $_SESSION['won'] = false;
    $_SESSION['has_key'] = false;
    $_SESSION['show_question'] = false;
    $_SESSION['score'] = 0;
    $_SESSION['level'] = 1;
}

if (isset($_POST['restart'])) {
    $_SESSION['player'] = [1, 1];
    $_SESSION['steps'] = 0;
    $_SESSION['won'] = false;
    $_SESSION['has_key'] = false;
    $_SESSION['show_question'] = false;
    $_SESSION['score'] = 0;
    $_SESSION['level'] = 1;
}

// เลือกแผนที่ตามด่านปัจจุบัน
$level = $_SESSION['level'] ?? 1;
$maze = $mazes[$level] ?? $mazes[1];
 // ตรวจคำตอบเมื่อผู้เล่นอยู่หน้าคำถาม
if (
    isset($_POST['answer']) &&
    ($_SESSION['show_question'] ?? false) &&
    !($_SESSION['won'] ?? false)
) {
    $answer = $_POST['answer'];

    
if ($answer === $questions[$_SESSION['level'] - 1]['answer']) {

    // เพิ่มคะแนน
    $_SESSION['score'] += 10;
    $_SESSION['quiz_sound'] = 'correct';
    $_SESSION['show_question'] = false;

    if ($_SESSION['level'] < 3) {

        // เปลี่ยนไปด่านถัดไป
        $_SESSION['level']++;

        // เริ่มตำแหน่งใหม่
        $_SESSION['player'] = [1, 1];
        $_SESSION['has_key'] = false;

        $_SESSION['message'] = "🎉 ผ่านด่านแล้ว! เข้าสู่ด่านที่ " . $_SESSION['level'];

    } else {

        // จบเกมเมื่อผ่านด่านที่ 3
        $_SESSION['won'] = true;
        $_SESSION['play_win_sound'] = true;
        $_SESSION['message'] = "🏆 MISSION COMPLETE! คุณพิชิตครบ 3 ด่านแล้ว";

    }

} else {
    $_SESSION['message'] = "❌ ตอบผิด ลองตอบอีกครั้ง";
    $_SESSION['quiz_sound'] = 'wrong';
}

}
$player = $_SESSION['player'];


// รับคำสั่งเดินจากปุ่ม

if (
    isset($_POST['move']) &&
    !$_SESSION['won'] &&
    !($_SESSION['show_question'] ?? false)
) {

    $directions = [
        'up' => [-1, 0],
        'down' => [1, 0],
        'left' => [0, -1],
        'right' => [0, 1]
    ];

    $move = $_POST['move'];

    if (isset($directions[$move])) {
        $newRow = $player[0] + $directions[$move][0];
        $newCol = $player[1] + $directions[$move][1];

        if (isset($maze[$newRow][$newCol])
            && $maze[$newRow][$newCol] != 1) {

            $_SESSION['player'] = [$newRow, $newCol];
            $_SESSION['steps']++;
            
    if ($maze[$newRow][$newCol] == 3) {
        $_SESSION['has_key'] = true;
        $_SESSION['play_key_sound'] = true;
} 
if ($maze[$newRow][$newCol] == 2) {
    // ต้องมีกุญแจก่อนจึงจะตอบคำถามได้
    if ($_SESSION['has_key'] ?? false) {
        $_SESSION['show_question'] = true;
        $_SESSION['player'] = $player;
        $_SESSION['steps']--;
    } else {
        $_SESSION['player'] = $player;
        $_SESSION['steps']--;
    }
}


        }
    }
}

$player = $_SESSION['player'];

$level = $_SESSION['level'] ?? 1;
$maze = $mazes[$level] ?? $mazes[1];

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maze Adventure - ด่านที่ 1</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="game-container">

    <h1>🗝️ เขาวงกตด่านที่ <?php echo $_SESSION['level']; ?></h1>


<?php if (!empty($_SESSION['message'])): ?>
    <p class="game-message">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
    </p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

    
<div class="game-hud">
    <div class="hud-item">
        🏰 LEVEL
        <strong><?php echo $_SESSION['level'] ?? 1; ?> / 3</strong>
    </div>

    <div class="hud-item">
        ⭐ SCORE
        <strong><?php echo $_SESSION['score'] ?? 0; ?> / 30</strong>
    </div>

    <div class="hud-item">
        👣 STEPS
        <strong><?php echo $_SESSION['steps'] ?? 0; ?></strong>
    </div>

    <div class="hud-item">
        🔑 KEY
        <strong>
            <?php echo !empty($_SESSION['has_key']) ? 'Collected' : 'Not Found'; ?>
        </strong>
    </div>
</div>


<div class="quest-panel">
    <h3>📜 CURRENT QUEST</h3>

    <?php if (!empty($_SESSION['has_key'])): ?>
        <p>✅ เก็บกุญแจสำเร็จ! ไปยังประตูทางออก 🏁</p>
    <?php else: ?>
        <p>🔑 ค้นหากุญแจเวทมนตร์เพื่อปลดล็อกทางออก</p>
    <?php endif; ?>
</div>

<div class="maze">
        <?php foreach ($maze as $rowIndex => $row): ?>
            <?php foreach ($row as $colIndex => $cell): ?>

                <?php
                $class = "path";
                $symbol = "";

                if ($cell == 1) {
                    $class = "wall";
                }

                if ($cell == 2) {
                    $class = "exit";
                    $symbol = "🏁";
                }

                
                 if ($cell == 3) {
                    if (!($_SESSION['has_key'] ?? false)) {
                        $class = "key";
                        $symbol = "🔑";
                }
            }


                if ($player == [$rowIndex, $colIndex]) {
                    $class = "player";
                    $symbol = "🧑";
                }
                ?>

                <div class="cell <?php echo $class; ?>">
                    <?php echo $symbol; ?>
                </div>

            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

<?php if ($_SESSION['show_question'] ?? false): ?>
    <div class="quiz-box">
        <h2>❓ คำถามปลดล็อกประตู</h2>

        <p><?php echo htmlspecialchars($questions[$_SESSION['level'] - 1]['question']); ?></p>

        <form method="POST">
            <?php foreach ($questions[$_SESSION['level'] - 1]['choices'] as $letter => $choice): ?>
                <button type="submit" name="answer"
                        value="<?php echo $letter; ?>">
                    <?php echo $letter . ". " . htmlspecialchars($choice); ?>
                </button>
            <?php endforeach; ?>
        </form>
    </div>
<?php endif; ?>

    <?php if ($_SESSION['won']): ?>
        
<div class="win-screen">
    <h1>🏆 MISSION COMPLETE!</h1>

    <h2>🎉 ยินดีด้วย! คุณพิชิตเขาวงกตครบ 3 ด่าน</h2>

    <p>คุณผ่านการทดสอบ HTML, CSS และ PHP สำเร็จแล้ว!</p>

    <h2>⭐ คะแนนรวม:
        <?php echo $_SESSION['score']; ?> / 30
    </h2>

    <p>👣 จำนวนก้าวทั้งหมด:
        <?php echo $_SESSION['steps']; ?> ก้าว
    </p>

    <p>🏅 ภารกิจสำเร็จ!</p>
</div>



<?php endif; ?>
    <form method="POST" id="moveForm">
    <input type="hidden" name="move" id="moveInput">
    </form>
    <form method="POST">
        <button type="submit" name="restart" value="1">
            🔄 เริ่มใหม่
        </button>
    </form>

    <p><a href="index.php">🏠 กลับหน้าหลัก</a></p>

</div>
<script src="sounds/win.js"></script>

<?php if (!empty($_SESSION['play_win_sound'])): ?>
<script>
    window.addEventListener("DOMContentLoaded", function() {
        playWinSound();
    });
</script>
<?php unset($_SESSION['play_win_sound']); ?>
<?php endif; ?>
<script src="sounds/quiz.js"></script>

<?php if (!empty($_SESSION['quiz_sound'])): ?>
<script>
    window.addEventListener("DOMContentLoaded", function() {
        playQuizSound(<?php echo json_encode($_SESSION['quiz_sound'] === 'correct'); ?>);
    });
</script>
<?php unset($_SESSION['quiz_sound']); ?>
<?php endif; ?>
<script src="sounds/key.js"></script>

<?php if (!empty($_SESSION['play_key_sound'])): ?>
<script>
    window.addEventListener("DOMContentLoaded", function() {
        playKeySound();
    });
</script>
<?php unset($_SESSION['play_key_sound']); ?>
<?php endif; ?>
<script src="sounds/footstep.js"></script>

<script>
document.addEventListener("keydown", function(event) {
    const keys = {
        "ArrowUp": "up",
        "ArrowDown": "down",
        "ArrowLeft": "left",
        "ArrowRight": "right",
        "w": "up",
        "a": "left",
        "s": "down",
        "d": "right"
    };

    const key = event.key.length === 1
        ? event.key.toLowerCase()
        : event.key;

    if (keys[key]) {
        event.preventDefault();

        if (event.repeat) return;

        const moveInput = document.getElementById("moveInput");
        const moveForm = document.getElementById("moveForm");

        if (moveInput && moveForm) {
            moveInput.value = keys[key];
        playFootstep();

        setTimeout(function() {
             moveForm.requestSubmit();
        }, 180);
        }
    }
});
</script>
</body>
</html>
