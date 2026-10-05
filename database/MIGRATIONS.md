# EUDR migration baseline

Captured from the running `eudr_db_container` database `eudr` on 2026-10-05.
The 13 migrations replace the unrelated IT migrations and Laravel default schemas.
They use native MySQL DDL to preserve column types, collations, defaults, indexes,
foreign keys and the report-file source CHECK constraint. MySQL 8.0.16+ is required
for enforced CHECK constraints. Sequence counters and application data are excluded.

## New database

Configure the `mysql2` connection to an empty database, then run:

```sh
php artisan migrate --database=mysql2
```

The default seeder does not create accounts or document categories. Populate required
reference data and accounts separately. Legacy SQL files are historical references;
do not import them alongside these migrations.

## Existing database

The current EUDR database already has these 13 tables. On 2026-10-05, after a full
backup and a fresh schema comparison, the baseline was recorded in its new
`migrations` table. All 13 entries are in batch 1; Laravel reports nothing to migrate.
The steps below apply to other existing databases that have not been baselined.
Do not run the create migrations against it directly; table creation will fail.
After backing up and verifying that its schema matches this baseline, apply
`database/baseline_existing_database.sql` to record the migrations as completed.
The SQL intentionally fails if a migrations history already exists; reconcile that
history separately rather than overwriting it. Then future migrations can use
`php artisan migrate --database=mysql2` normally.

Do not run `migrate:fresh`, `migrate:reset`, or baseline rollback on a database whose
data must be retained: they drop application tables.

## Validation performed

In a separate temporary MySQL database: all 13 migrations succeeded, each table's
SHOW CREATE TABLE matched the live schema after normalizing sequence counters and
redundant charset notation, rollback removed all application tables, and running the
migrations again succeeded. The live application database was read only.

Sanctum's automatic token migration is disabled in AppServiceProvider because the
current application uses session login and the live schema has no token table.
