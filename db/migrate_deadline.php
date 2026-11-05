<?php
require_once '../db/database.php';
$pdo->exec("ALTER TABLE tasks MODIFY COLUMN deadline DATETIME NULL");
echo "Done. deadline column updated to DATETIME.";
