# Import Database Backup to Railway

Your database backup (`backup.sql`) has been pushed to the repository. Here's how to import it to Railway.

## Step 1: Download or Access the SQL File

The `backup.sql` file is now in your repository. You can:
- Download it from GitHub
- Or it's already in your local project

## Step 2: Import to Railway

### For SQLite Database:

If you're using SQLite in Railway (requires a Volume for persistence):

```bash
# Import directly to SQLite
railway run sqlite3 database/database.sqlite < backup.sql

# Or if you have the original database.sqlite file, copy it directly
```

**Note:** Railway's free tier doesn't support SQLite well without persistent volumes. See `IMPORT_TO_SQLITE.md` for detailed SQLite setup.

### For MySQL Database:

```bash
# Method 1: Using Railway CLI
railway run mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < backup.sql

# Method 2: If you have the file locally, upload and import
# First, copy file to Railway container (if needed)
railway run bash
# Then inside the container:
mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < backup.sql
```

### For PostgreSQL Database:

```bash
railway run psql $DATABASE_URL < backup.sql
```

## Step 3: Convert SQLite Syntax (If Needed)

The backup.sql file is exported from SQLite. You may need to adjust it for MySQL/PostgreSQL:

### Common Conversions Needed:

1. **AUTOINCREMENT → AUTO_INCREMENT** (MySQL)
2. **INTEGER PRIMARY KEY → INT PRIMARY KEY AUTO_INCREMENT** (MySQL)
3. **TEXT → VARCHAR(255)** or **TEXT** (both work)
4. **Remove SQLite-specific syntax**

### Quick Fix Script:

If you get syntax errors, you can manually edit the SQL file or use this approach:

```bash
# For MySQL - basic conversion
sed -i 's/INTEGER PRIMARY KEY AUTOINCREMENT/INT AUTO_INCREMENT PRIMARY KEY/g' backup.sql
sed -i 's/AUTOINCREMENT/AUTO_INCREMENT/g' backup.sql
```

## Step 4: Import via Railway Dashboard (Alternative)

1. Go to Railway Dashboard → Your Database Service
2. Click on "Query" or "Data" tab
3. Copy and paste the SQL commands from `backup.sql`
4. Execute them one by one (or all at once if supported)

## Step 5: Verify Import

After importing, verify the data:

```bash
railway run php artisan tinker
```

Then check:
```php
// Check users
App\Models\User::count();
App\Models\User::all();

// Check products
App\Models\Product::count();

// Check orders
App\Models\Order::count();
```

## Important Notes

⚠️ **WARNING:**
- Importing will **OVERWRITE** existing data in Railway
- Always backup your current Railway database first:
  ```bash
  railway run mysqldump -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE > railway_backup.sql
  ```

✅ **What's in the backup:**
- Users (including admin: natkimsreng@gmail.com)
- Categories
- Products
- Authors
- Orders
- Order Items
- Migrations history

## Troubleshooting

### "Syntax Error" on Import

The SQL file uses SQLite syntax. You may need to:
1. Manually edit the SQL file for MySQL/PostgreSQL
2. Or use the migrations to recreate structure, then import only data

### "Table Already Exists" Error

Drop tables first:
```bash
railway run php artisan migrate:fresh
# Then import
railway run mysql < backup.sql
```

### Import Partially Fails

Import in sections:
1. Import structure (CREATE TABLE statements)
2. Then import data (INSERT statements)

## Alternative: Use Migrations + Seeders

Instead of importing the SQL file, you could:
1. Run migrations to create structure
2. Create seeders for your data
3. Run seeders to populate data

This is more maintainable but requires more setup.

