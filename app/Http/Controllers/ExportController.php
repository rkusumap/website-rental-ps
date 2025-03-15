<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    public function index()
    {
        $uuid = (string) Str::uuid();
        // Define the name of the exported file
        $fileName = 'database_export_' . date('Y-m-d_H-i-s'). '_' . $uuid . '.sql';

        // Get the database connection
        $connection = DB::getPdo();

        // Set the path to save the SQL file in the public directory
        $path = public_path($fileName);

        // Open a file to write
        $file = fopen($path, 'w');

        // Add database creation statements (optional)
        fwrite($file, 'CREATE DATABASE IF NOT EXISTS `' . env('DB_DATABASE') . '`;' . PHP_EOL);
        fwrite($file, 'USE `' . env('DB_DATABASE') . '`;' . PHP_EOL . PHP_EOL);

        // Get all tables from the database
        $tables = $connection->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        // Loop through each table and export its structure and data
        foreach ($tables as $table) {
            // Export the table structure (CREATE TABLE)
            $this->exportTableStructure($table, $connection, $file);

            // Export the table data (INSERT INTO)
            $this->exportTableData($table, $connection, $file);

            // Add a line break between tables
            fwrite($file, PHP_EOL);
        }

        // Close the file
        fclose($file);

        // Return a response indicating success
        return response()->json(['success' => 'SQL file exported and saved to the public folder!', 'file' => $fileName]);
    }

    /**
     * Export the structure of a table (CREATE TABLE statement)
     */
    private function exportTableStructure($table, $connection, $file)
    {
        // Get the CREATE TABLE statement for the table
        $createStatement = $connection->query('SHOW CREATE TABLE ' . $table)->fetch(\PDO::FETCH_ASSOC);

        // Write the CREATE TABLE statement to the file
        fwrite($file, $createStatement['Create Table'] . ';' . PHP_EOL . PHP_EOL);
    }

    /**
     * Export the data of a table (INSERT INTO statements)
     */
    private function exportTableData($table, $connection, $file)
    {
        // Get all data from the table
        $rows = $connection->query('SELECT * FROM ' . $table)->fetchAll(\PDO::FETCH_ASSOC);

        // If the table has data, generate INSERT INTO statements
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $columns = array_keys($row);
                $values = array_map(function ($value) {
                    return is_null($value) ? 'NULL' : '"' . addslashes($value) . '"';
                }, array_values($row));

                $insertStatement = 'INSERT INTO `' . $table . '` (`' . implode('`, `', $columns) . '`) VALUES (' . implode(', ', $values) . ');' . PHP_EOL;
                fwrite($file, $insertStatement);
            }
            fwrite($file, PHP_EOL);  // Add a line break after data
        }
    }
}
