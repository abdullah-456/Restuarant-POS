<?php
// reset_database.php
$dbPath = __DIR__ . '/database/pos_system.sqlite';

// Delete existing database
if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "✓ Deleted existing database\n";
}

// Create new database file
touch($dbPath);
echo "✓ Created new database file\n";

echo "\nNow run:\n";
echo "php artisan migrate:fresh --seed\n";