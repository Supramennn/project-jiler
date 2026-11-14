<?php
$db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
$row = $db->query("SELECT order_id, access_token, nomor_invoice FROM orders WHERE status = 'paid' ORDER BY id DESC LIMIT 1")->fetch();
echo $row['order_id'] . '|' . $row['access_token'] . '|' . $row['nomor_invoice'] . PHP_EOL;
