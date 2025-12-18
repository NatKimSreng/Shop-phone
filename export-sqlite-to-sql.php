<?php
/**
 * Export SQLite database to SQL file
 * 
 * Usage: php export-sqlite-to-sql.php
 * 
 * This will create a backup.sql file from your local SQLite database
 */

$sqlitePath = __DIR__ . '/database/database.sqlite';
$outputFile = __DIR__ . '/backup.sql';

if (!file_exists($sqlitePath)) {
    echo "❌ SQLite database not found at: {$sqlitePath}\n";
    exit(1);
}

echo "📦 Exporting SQLite database to SQL...\n";

try {
    $pdo = new PDO("sqlite:{$sqlitePath}");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $output = fopen($outputFile, 'w');
    
    if (!$output) {
        throw new Exception("Cannot create output file: {$outputFile}");
    }
    
    // Get all tables
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    
    fwrite($output, "-- SQLite Database Export\n");
    fwrite($output, "-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
    
    foreach ($tables as $table) {
        if ($table === 'sqlite_sequence') {
            continue; // Skip SQLite internal table
        }
        
        echo "Exporting table: {$table}\n";
        
        // Get table structure
        $createTable = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$table}'")->fetchColumn();
        
        // Convert SQLite CREATE TABLE to MySQL/PostgreSQL compatible
        $createTable = convertSqliteToMySQL($createTable);
        
        fwrite($output, "\n-- Table: {$table}\n");
        fwrite($output, "DROP TABLE IF EXISTS `{$table}`;\n");
        fwrite($output, $createTable . ";\n\n");
        
        // Get table data
        $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows) > 0) {
            fwrite($output, "-- Data for table: {$table}\n");
            
            foreach ($rows as $row) {
                $columns = array_keys($row);
                $values = array_values($row);
                
                // Escape values
                $escapedValues = array_map(function($value) use ($pdo) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return $pdo->quote($value);
                }, $values);
                
                $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $escapedValues) . ");\n";
                fwrite($output, $sql);
            }
            
            fwrite($output, "\n");
        }
    }
    
    fclose($output);
    
    echo "✅ Export complete!\n";
    echo "📄 SQL file created: {$outputFile}\n";
    echo "\n💡 Next steps:\n";
    echo "   1. Review the SQL file\n";
    echo "   2. Adjust for MySQL/PostgreSQL if needed\n";
    echo "   3. Import to Railway using: railway run mysql < backup.sql\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Convert SQLite CREATE TABLE syntax to MySQL compatible
 */
function convertSqliteToMySQL($sql) {
    // Basic conversions (you may need to adjust based on your schema)
    $sql = str_replace('INTEGER PRIMARY KEY AUTOINCREMENT', 'INT AUTO_INCREMENT PRIMARY KEY', $sql);
    $sql = str_replace('AUTOINCREMENT', 'AUTO_INCREMENT', $sql);
    $sql = str_replace('TEXT', 'VARCHAR(255)', $sql);
    $sql = str_replace('REAL', 'DECIMAL(10,2)', $sql);
    $sql = str_replace('BLOB', 'LONGBLOB', $sql);
    
    // Remove SQLite-specific syntax
    $sql = preg_replace('/\s+WITHOUT\s+ROWID/i', '', $sql);
    
    return $sql;
}

