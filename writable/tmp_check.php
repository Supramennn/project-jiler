<?php
$db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
$order = 'INV-261114-62ED6E';
$db->exec("UPDATE orders SET status_email = 'gagal', email_sent_at = NULL WHERE order_id = " . $db->quote($order));
$row = $db->query("SELECT status, status_email FROM orders WHERE order_id = " . $db->quote($order))->fetch(PDO::FETCH_ASSOC);
echo 'BEFORE retry: ' . $row['status'] . ' / ' . $row['status_email'] . PHP_EOL;
