<?php
include 'includes/db.php';

// حذف رکورد
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM systems WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    // ریلود صفحه پس از حذف
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// افزودن/ویرایش رکورد
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $last_service = $_POST['last_service'];
        $cost_per_second = $_POST['cost_per_second'];

        $stmt = $conn->prepare("INSERT INTO systems (name, last_service, cost_per_second) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $last_service, $cost_per_second);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $last_service = $_POST['last_service'];
        $cost_per_second = $_POST['cost_per_second'];

        $stmt = $conn->prepare("UPDATE systems SET name=?, last_service=?, cost_per_second=? WHERE id=?");
        $stmt->bind_param("ssdi", $name, $last_service, $cost_per_second, $id);
        $stmt->execute();
        $stmt->close();
    }
    // ریلود صفحه پس از افزودن/ویرایش
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$query = "SELECT * FROM systems";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت سیستم‌ها</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/variable/Vazirmatn-Variable-font-face.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container">
        <h1>مدیریت سیستم‌ها</h1>
        <br>
        <a href="index.php" class="btn" id="btnback1">بازگشت</a>
        <br>

        <!-- فرم افزودن/ویرایش -->
        <form method="POST" class="form">
            <input type="hidden" name="id" id="edit-id">
            <br>
            <label for="name">نام سیستم:</label>
            <input type="text" name="name" id="name" required>

            <label for="last_service">تاریخ آخرین سرویس:</label>
            <input type="date" name="last_service" id="last_service" required>

            <label for="cost_per_second">هزینه هر ثانیه:</label>
            <input type="number" step="0.01" name="cost_per_second" id="cost_per_second" required>

            <button type="submit" name="add" class="btn">افزودن</button>
            <button type="submit" name="edit" class="btn" id="edit-btn" style="display: none;">ویرایش</button>
        </form>

        <!-- نمایش لیست سیستم‌ها -->
        <div class="systems">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="system-card">
                    <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                    <p>هزینه هر ثانیه: <?php echo htmlspecialchars($row['cost_per_second']); ?> تومان</p>
                    <p>آخرین سرویس: <?php echo htmlspecialchars($row['last_service']); ?></p>

                    <!-- دکمه ویرایش -->
                    <button class="btn edit-system-btn" 
                            data-id="<?php echo $row['id']; ?>" 
                            data-name="<?php echo $row['name']; ?>" 
                            data-last-service="<?php echo $row['last_service']; ?>" 
                            data-cost-per-second="<?php echo $row['cost_per_second']; ?>">
                        ویرایش
                    </button>

                    <!-- دکمه حذف -->
                    <a href="?delete=<?php echo $row['id']; ?>" 
                       class="btn delete-btn" 
                       onclick="return confirm('آیا از حذف این سیستم اطمینان دارید؟');">
                        حذف
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>