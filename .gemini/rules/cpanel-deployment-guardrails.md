# cPanel & PHP 8 Deployment Guardrails

## 1. Blade Template & AJAX Routing Invariants
- **NEVER use root-relative paths in client-side scripts**:
  Avoid `url: '/path'`, ``url: `/${param}```, or `fetch('/path')`.
- **ALWAYS use Laravel URL helpers**:
  Use `{{ url('/path') }}` or `{{ route('route.name') }}` so that applications deployed in subfolders (e.g., `https://domain.com/testing/public/`) automatically resolve with the correct base path.
- **Force HTTPS Behind Proxies**:
  In `AppServiceProvider::boot()`, ensure HTTPS scheme is enforced when `app.url` starts with `https://`.

## 2. Eloquent Relationships & PHP 8 Type Compatibility
- **Primary/Foreign Key Type Alignment**:
  Ensure foreign key and local key types match. Never use a string business code (e.g., `kode = '1.A'`) as the local key when the foreign key column is an unsigned integer (`bigint UNSIGNED` ID `1..N`).
- **PHP 8 Comparison Invariant**:
  Remember that in PHP 8+, `1 == '1.A'` evaluates to `false`. Always match integer foreign keys to primary key `id`.

## 3. Idempotent Non-Destructive SQL Dumps for phpMyAdmin
- **No DROP TABLE**: Always use `CREATE TABLE IF NOT EXISTS`.
- **Inline Primary Keys & Auto-Increment**: Define `PRIMARY KEY` and `AUTO_INCREMENT` directly inside `CREATE TABLE` to prevent separate `ALTER TABLE ... ADD PRIMARY KEY` execution failures in phpMyAdmin.
- **Idempotent Data Insertion**: Use `REPLACE INTO` instead of raw `INSERT INTO` to gracefully update existing records without duplicate key errors.
- **Dynamic Schema Additions**: Prepend new columns with safe conditional checks against `information_schema.COLUMNS` using prepared statements.
