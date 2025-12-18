# Import Database Backup to SQLite

If you're using SQLite in Railway (or locally), here's how to import the backup.

## Option 1: Direct SQLite Import (If Railway Uses SQLite)

Railway typically uses MySQL or PostgreSQL, but if you want to use SQLite:

### Step 1: Create SQLite Database in Railway

SQLite needs a persistent volume to store the database file:

1. **Create a Volume in Railway:**
   - Railway Dashboard → Your Project → "+ New" → "Volume"
   - Name: `sqlite-data`
   - Mount path: `/app/database`
   - Size: 1GB (or as needed)

2. **Set Environment Variable:**
   ```env
   DB_CONNECTION=sqlite
   ```

### Step 2: Import the Backup

Since `backup.sql` is already in SQLite format, you can import it directly:

```bash
# Connect to Railway container
railway run bash

# Then import
sqlite3 database/database.sqlite < backup.sql
```

Or if you have the file locally and want to copy it:

```bash
# Copy the SQLite database file directly (if you have it)
# The backup.sql needs to be converted back to SQLite format, or use the original database.sqlite file
```

## Option 2: Use Original SQLite File Directly

If you have the original `database/database.sqlite` file:

### Step 1: Copy SQLite File to Railway Volume

```bash
# If you have database.sqlite locally, you can copy it directly
# But Railway volumes need to be mounted first
```

### Step 2: Set Database Path

In Railway environment variables:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite
```

## Option 3: Convert SQL Backup Back to SQLite

If you only have `backup.sql` and want to create a SQLite database:

### Locally:

```bash
# Create new SQLite database
sqlite3 database/database.sqlite < backup.sql
```

### In Railway:

```bash
railway run sqlite3 database/database.sqlite < backup.sql
```

## Important Notes

⚠️ **Railway Considerations:**
- Railway's free tier doesn't support SQLite well (no persistent storage by default)
- You **need a Volume** for SQLite to persist data
- SQLite is better for local development
- For production on Railway, MySQL/PostgreSQL is recommended

✅ **When to Use SQLite:**
- Local development
- Small applications
- Single-server deployments
- When you have persistent volumes configured

## Recommended: Use MySQL/PostgreSQL in Railway

For Railway production, I recommend:
1. Use MySQL or PostgreSQL (Railway provides these)
2. Convert the SQLite backup to MySQL/PostgreSQL format
3. Import using the MySQL/PostgreSQL commands

See `IMPORT_BACKUP_TO_RAILWAY.md` for MySQL/PostgreSQL import instructions.

## Quick SQLite Import Command

If you're using SQLite locally or have it set up in Railway:

```bash
# Import backup.sql to SQLite
sqlite3 database/database.sqlite < backup.sql

# Or if the file is already SQLite format
cp backup.sqlite database/database.sqlite
```

## Verify Import

```bash
# Check tables
sqlite3 database/database.sqlite ".tables"

# Check data
sqlite3 database/database.sqlite "SELECT COUNT(*) FROM users;"
sqlite3 database/database.sqlite "SELECT COUNT(*) FROM products;"
```

