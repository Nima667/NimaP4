<?php
include 'includes/db.php';

// دریافت شناسه سیستم از URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("شناسه سیستم نامعتبر است.");
}

$id = $_GET['id'];

// دریافت اطلاعات سیستم از جدول `systems`
$system_query = "SELECT name FROM systems WHERE id=?";
$system_stmt = $conn->prepare($system_query);
$system_stmt->bind_param("i", $id);
$system_stmt->execute();
$system_result = $system_stmt->get_result();

if ($system_result->num_rows === 0) {
    die("سیستم مورد نظر یافت نشد.");
}

$system = $system_result->fetch_assoc();
$system_name = $system['name'];

// دریافت رکوردهای استفاده از جدول `records`
$query = "SELECT start_time, end_time, total_cost FROM records WHERE system_id=? ORDER BY start_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جزئیات سیستم - <?php echo htmlspecialchars($system_name); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/variable/Vazirmatn-Variable-font-face.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container">
        <h1>جزئیات سیستم: <?php echo htmlspecialchars($system_name); ?></h1><br>
        <a href="index.php" class="btn">بازگشت</a>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>ساعت شروع</th>
                        <th>ساعت پایان</th>
                        <th>مبلغ دریافتی</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($row['start_time'])); ?></td>
                            <td><?php echo date('H:i:s', strtotime($row['start_time'])); ?></td>
                            <td><?php echo $row['end_time'] ? date('H:i:s', strtotime($row['end_time'])) : '-'; ?></td>
                            <td><?php echo $row['total_cost'] ?? '-'; ?> تومان</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: red;">هیچ رکوردی برای این سیستم وجود ندارد.</p>
        <?php endif; ?>
    </div>
</body>
</html>