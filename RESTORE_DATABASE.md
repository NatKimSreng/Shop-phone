# How to Restore/Import Old Database in Railway

There are several ways to restore an old database in Railway, depending on what you have.

## Method 1: Import SQL Dump File (Recommended)

If you have a `.sql` dump file from your old database:

### Step 1: Prepare Your SQL File

1. Make sure your SQL file is ready (e.g., `backup.sql`)
2. The file should contain SQL statements like:
   ```sql
   CREATE TABLE users (...);
   INSERT INTO users VALUES (...);
   ```

### Step 2: Import via Railway CLI

```bash
# Connect to Railway database
railway connect

# Import the SQL file
railway run mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < backup.sql
```

Or for PostgreSQL:
```bash
railway run psql $DATABASE_URL < backup.sql
```

### Step 3: Import via Railway Dashboard

1. Go to Railway Dashboard → Your Database Service
2. Click on "Data" or "Query" tab
3. Paste your SQL commands and execute
4. Or use the "Import" feature if available

## Method 2: Import from Local SQLite Database

If you have a local SQLite database file (`database/database.sqlite`):

### Step 1: Export SQLite to SQL

```bash
# Install sqlite3 if not already installed
# On Windows: Download from sqlite.org
# On Mac/Linux: Usually pre-installed

# Export to SQL
sqlite3 database/database.sqlite .dump > backup.sql
```

### Step 2: Convert SQLite SQL to MySQL/PostgreSQL

SQLite syntax differs from MySQL/PostgreSQL. You'll need to:

1. **Manual conversion** - Edit the SQL file to match your Railway database type
2. **Use a tool** - Use online converters or scripts
3. **Use Laravel migrations** - Recreate structure via migrations, then import data

### Step 3: Import to Railway

Follow Method 1, Step 2 above.

## Method 3: Use Railway Database Restore Feature

If Railway has automatic backups:

1. Go to Railway Dashboard → Your Database Service
2. Look for "Backups" or "Restore" section
3. Select the backup you want to restore
4. Click "Restore"

**Note:** Railway's free tier may not have automatic backups. Check your plan.

## Method 4: Recreate Database with Migrations + Seeders

If you don't have a backup but want to recreate the structure:

### Step 1: Run Migrations

```bash
railway run php artisan migrate:fresh
```

This will:
- Drop all tables
- Recreate all tables from migrations

### Step 2: Import Data

If you have seeders with your data:

```bash
railway run php artisan db:seed
```

Or import specific seeders:

```bash
railway run php artisan db:seed --class=YourDataSeeder
```

## Method 5: Import via Laravel Tinker

For small amounts of data:

```bash
railway run php artisan tinker
```

Then manually create records:

```php
// Example: Create a user
User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);

// Or import from array
$users = [
    ['name' => 'User 1', 'email' => 'user1@example.com', ...],
    ['name' => 'User 2', 'email' => 'user2@example.com', ...],
];

foreach ($users as $userData) {
    User::create($userData);
}
```

## Method 6: Use Database GUI Tool

### Option A: TablePlus / DBeaver / phpMyAdmin

1. **Get Railway database connection string:**
   - Railway Dashboard → Database → Connect
   - Copy the connection string

2. **Connect with GUI tool:**
   - Use the connection details
   - Import your SQL file through the GUI

3. **Or use Railway's built-in query interface:**
   - Railway Dashboard → Database → Query
   - Paste and execute SQL

## Quick Commands Reference

### Export Current Railway Database

```bash
# MySQL
railway run mysqldump -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE > backup.sql

# PostgreSQL
railway run pg_dump $DATABASE_URL > backup.sql
```

### Import SQL File

```bash
# MySQL
railway run mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < backup.sql

# PostgreSQL
railway run psql $DATABASE_URL < backup.sql
```

### Reset and Recreate

```bash
# Fresh migration (WARNING: Deletes all data!)
railway run php artisan migrate:fresh

# Then seed
railway run php artisan db:seed
```

## Important Notes

⚠️ **WARNING:** 
- `migrate:fresh` will **DELETE ALL DATA**
- Always backup before importing
- Test imports on a staging environment first

✅ **Best Practices:**
- Keep regular backups of your database
- Export before major changes
- Store backups in version control (if small) or cloud storage
- Document your database structure

## Troubleshooting

### "Access Denied" Error
- Check database credentials in Railway environment variables
- Verify user has import permissions

### "Table Already Exists" Error
- Drop tables first: `railway run php artisan migrate:fresh`
- Or use `IF NOT EXISTS` in your SQL

### "Syntax Error" in SQL
- Check if SQL is for correct database type (MySQL vs PostgreSQL)
- SQLite SQL needs conversion for MySQL/PostgreSQL

### Large File Import Timeout
- Split large SQL files into smaller chunks
- Use command line instead of web interface
- Increase timeout in Railway settings if possible

## Need Help?

If you have a specific backup file or need help with a particular method, let me know:
- What type of database backup do you have? (SQL file, SQLite, etc.)
- What database type is Railway using? (MySQL, PostgreSQL)
- How large is the backup?

