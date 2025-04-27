<?php
include 'includes/db.php';

// ذخیره اطلاعات ارسالی از طریق AJAX
if (isset($_POST['save_record'])) {
    $system_id = $_POST['system_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $total_cost = $_POST['total_cost'];

    $stmt = $conn->prepare("INSERT INTO records (system_id, start_time, end_time, total_cost) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $system_id, $start_time, $end_time, $total_cost);
    $stmt->execute();
    $stmt->close();

    exit; // پایان پاسخ سرور
}

// دریافت تمام سیستم‌ها از پایگاه داده
$query = "SELECT * FROM systems";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گیم نت</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/variable/Vazirmatn-Variable-font-face.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container">
        <h1>مدیریت گیم نت</h1>
        <br>
        <a href="manage_systems.php" class="btn">مدیریت سیستم‌ها</a>

        <div class="systems">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="system-card">
                    <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                    <p>هزینه هر ثانیه: <?php echo htmlspecialchars($row['cost_per_second']); ?> تومان</p>
                    <p>آخرین سرویس: <?php echo htmlspecialchars($row['last_service']); ?></p>

                    <!-- تایمر -->
                    <div class="timer" id="timer-<?php echo $row['id']; ?>" style="display: none; font-size: 18px; color: red; margin-bottom: 10px;">
                        زمان باقی‌مانده: <span id="time-<?php echo $row['id']; ?>">00:00:00</span>
                    </div>

                    <button class="btn start-btn" data-id="<?php echo $row['id']; ?>" data-cost-per-second="<?php echo $row['cost_per_second']; ?>">شروع</button>
                    <button class="btn end-btn" data-id="<?php echo $row['id']; ?>" style="display: none;">پایان</button>
                    <a href="details.php?id=<?php echo $row['id']; ?>" class="btn">جزئیات</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>