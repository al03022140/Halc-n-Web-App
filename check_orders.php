<?php
$pdo = new PDO('sqlite:database/database.sqlite');
$stmt = $pdo->query('SELECT id, invoice_number, start_image, end_image FROM orders WHERE start_image IS NOT NULL LIMIT 10');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo 'ID: ' . $row['id'] . ', Invoice: ' . $row['invoice_number'] . ', Start Image: ' . $row['start_image'] . PHP_EOL;
}