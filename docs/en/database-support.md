# Database Support in Webillium CMS

Webillium CMS supports various database systems through Laravel's database abstraction layer. This document explains the specific database features and compatibility notes.

## Supported Database Systems

Webillium CMS has been tested and supports:

- **MySQL/MariaDB** (primary development target)
- **PostgreSQL** (including schema support)
- **SQLite** (for development/testing)
- Other Laravel-supported databases (with varying levels of compatibility)

## PostgreSQL Support

Webillium CMS includes specific enhancements for PostgreSQL support:

### PostgreSQL Schema Support

The system can detect and work with PostgreSQL schemas:

```php
// Internal implementation in ModulsController
if ($driver === 'pgsql') {
    // PostgreSQL specific query to get column names
    $schema = 'public'; // Default schema
    $columns = DB::select("
        SELECT column_name
        FROM information_schema.columns
        WHERE table_schema = ? AND table_name = ?
    ", [$schema, $table]);
    
    // Extract column names from result
    $columns = array_map(function($col) {
        return $col->column_name;
    }, $columns);
}
```

### Case Sensitivity

Be aware that PostgreSQL table and column names are case-sensitive by default. For maximum compatibility:

- Use lowercase names for tables and columns
- If using uppercase or mixed-case identifiers, enclose them in double-quotes in your custom queries

## Multi-Database Configuration

If you need to work with multiple database connections:

1. Configure your additional connections in Laravel's `config/database.php`
2. Use Laravel's database connection switching:

```php
// Example of using a different connection in your controller
public function hookQueryIndex(&$query) {
    $query->connection('secondary_db');
    // Rest of your query...
}
```

## Table Detection and Column Listing

Webillium CMS includes robust methods for detecting tables and listing columns across different database systems:

```php
// Getting all database tables
$tables = CRUDBooster::listTables();

// Getting columns for a specific table
$columns = CRUDBooster::getTableColumns('your_table_name');
```

These methods include fallbacks to ensure they work across different database systems.

## Database Troubleshooting

If you encounter database-related issues:

### Column Detection Issues

If columns aren't detected properly, you can implement a custom column detection method:

```php
public function getTableColumns($table) {
    $driver = DB::connection()->getDriverName();
    
    if ($driver === 'your_driver') {
        // Custom column detection logic
        return $your_columns;
    }
    
    // Fall back to the default method
    return CRUDBooster::getTableColumns($table);
}
```

### Case Sensitivity Issues

If you're experiencing issues with case sensitivity:

```php
// For PostgreSQL, you might need to enclose identifiers in double quotes
DB::statement('SELECT * FROM "YourTable" WHERE "ColumnName" = ?', ['value']);
```

## SQL Query Logging

For debugging database queries, you can enable query logging:

```php
// In your controller's method
DB::enableQueryLog();
// Your database operations...
$queries = DB::getQueryLog();
Log::info('Database queries:', $queries);
```

## What's Next
- [How To Make A Join Table in Grid Data](./how-to-join-in-grid-data.md)
- [How To Put Custom Condition At Grid Data Query](./how-to-put-custom-condition-grid-data.md)

## Table Of Contents
- [Back To Index](./index.md)