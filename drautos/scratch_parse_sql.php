<?php
$file = __DIR__ . '/u909342762_dr.sql';
if (!file_exists($file)) {
    die("File not found: $file\n");
}

$handle = fopen($file, 'r');
if (!$handle) {
    die("Failed to open file\n");
}

$table_counts = [];
$insert_table = '';
$insert_buffer = '';
$in_insert = false;

while (($line = fgets($handle)) !== false) {
    // Check if line specifies table structure to register the table
    if (preg_match('/^-- Table structure for table `([^`]+)`/', $line, $matches)) {
        $tbl = $matches[1];
        if (!isset($table_counts[$tbl])) {
            $table_counts[$tbl] = 0;
        }
    }
    
    // Check if line starts an insert statement
    if (preg_match('/^INSERT INTO `([^`]+)`/i', $line, $matches)) {
        $insert_table = $matches[1];
        $insert_buffer = $line;
        $in_insert = true;
    } elseif ($in_insert) {
        $insert_buffer .= $line;
    }
    
    // Check if the insert statement is complete (ends with semicolon)
    if ($in_insert && preg_match('/;\s*$/', $line)) {
        $pos = stripos($insert_buffer, 'VALUES');
        if ($pos !== false) {
            $values_str = substr($insert_buffer, $pos + 6);
            $count = count_sql_rows($values_str);
            if (!isset($table_counts[$insert_table])) {
                $table_counts[$insert_table] = 0;
            }
            $table_counts[$insert_table] += $count;
        }
        $in_insert = false;
        $insert_buffer = '';
    }
}
fclose($handle);

echo "TABLE SUMMARY:\n";
asort($table_counts);
foreach ($table_counts as $tbl => $cnt) {
    if ($cnt > 0) {
        echo "- $tbl: $cnt rows\n";
    }
}

if (isset($table_counts['products'])) {
    echo "\nProducts found in SQL dump: " . $table_counts['products'] . "\n";
} else {
    echo "\nNo products table or inserts found in SQL dump.\n";
}

function count_sql_rows($str) {
    $str = trim($str);
    if (empty($str)) return 0;
    
    // Remove trailing semicolon if exists
    if (substr($str, -1) === ';') {
        $str = substr($str, 0, -1);
    }
    
    $len = strlen($str);
    $in_string = false;
    $string_char = '';
    $escaped = false;
    $depth = 0;
    $rows_count = 0;
    
    for ($i = 0; $i < $len; $i++) {
        $c = $str[$i];
        
        if ($escaped) {
            $escaped = false;
            continue;
        }
        
        if ($c === '\\') {
            $escaped = true;
            continue;
        }
        
        if ($in_string) {
            if ($c === $string_char) {
                $in_string = false;
            }
            continue;
        }
        
        if ($c === "'" || $c === '"') {
            $in_string = true;
            $string_char = $c;
            continue;
        }
        
        if ($c === '(') {
            if ($depth === 0) {
                // Start of a row tuple
            }
            $depth++;
        } elseif ($c === ')') {
            $depth--;
            if ($depth === 0) {
                // End of a row tuple
                $rows_count++;
            }
        }
    }
    
    return $rows_count;
}
