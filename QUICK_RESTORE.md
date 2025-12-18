# Quick Database Restore Guide

## If You Have a SQL File

### Import to Railway (MySQL):

```bash
# 1. Make sure your SQL file is ready
# 2. Import it
railway run mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < backup.sql
```

### Import to Railway (PostgreSQL):

```bash
railway run psql $DATABASE_URL < backup.sql
```

## If You Have Local SQLite Database

### Step 1: Export SQLite to SQL

```bash
# Run the export script
php export-sqlite-to-sql.php
```

This creates a `backup.sql` file.

### Step 2: Review and Adjust SQL

Open `backup.sql` and check:
- Table names are correct
- Data types match your Railway database (MySQL/PostgreSQL)
- Adjust syntax if needed

### Step 3: Import to Railway

```bash
# For MySQL
railway run mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < backup.sql

# For PostgreSQL  
railway run psql $DATABASE_URL < backup.sql
```

## If You Don't Have a Backup

### Option 1: Recreate with Migrations

```bash
# This will DELETE all data and recreate tables
railway run php artisan migrate:fresh

# Then seed data
railway run php artisan db:seed
```

### Option 2: Use Railway Dashboard

1. Go to Railway Dashboard → Your Database
2. Click "Query" tab
3. Paste your SQL commands
4. Execute

## Need to Export Current Railway Database First?

```bash
# MySQL
railway run mysqldump -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE > current_backup.sql

# PostgreSQL
railway run pg_dump $DATABASE_URL > current_backup.sql
```

## Important Notes

⚠️ **WARNING:**
- Importing will **OVERWRITE** existing data
- Always backup current database first
- Test on staging if possible

✅ **Before Importing:**
1. Backup current Railway database
2. Review your SQL file
3. Check table structure matches

