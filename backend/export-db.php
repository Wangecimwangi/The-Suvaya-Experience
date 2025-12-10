<?php
/**
 * Database Export Script
 * Exports the current database structure and data to SQL file
 */

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get database name from connection
$dbName = 'suvaya_bakery';

// Output file
$outputFile = __DIR__ . '/database/current_export.sql';

// Ensure database directory exists
if (!is_dir(__DIR__ . '/database')) {
    mkdir(__DIR__ . '/database', 0755, true);
}

echo "Starting database export...\n";
echo "Database: $dbName\n";
echo "Output: $outputFile\n\n";

// Use mysqldump command
$command = sprintf(
    'mysqldump -u %s %s %s > %s 2>&1',
    escapeshellarg('root'),
    '-p""', // Empty password
    escapeshellarg($dbName),
    escapeshellarg($outputFile)
);

echo "Running mysqldump...\n";
exec($command, $output, $returnCode);

if ($returnCode === 0) {
    echo "\n✓ Database exported successfully!\n";
    echo "File: $outputFile\n";
    echo "Size: " . number_format(filesize($outputFile) / 1024, 2) . " KB\n";
} else {
    echo "\n✗ Export failed!\n";
    echo "Command: $command\n";
    echo "Output:\n" . implode("\n", $output) . "\n";
    exit(1);
}

// Also create a timestamped backup
$timestamp = date('Y-m-d_H-i-s');
$backupFile = __DIR__ . "/database/backup_$timestamp.sql";
copy($outputFile, $backupFile);
echo "\n✓ Backup created: $backupFile\n";

echo "\nExport complete!\n";
?>
