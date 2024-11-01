<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(<<<SQL
            CREATE OR REPLACE FUNCTION update_user_balance_after_account_update()
            RETURNS TRIGGER AS \$\$
            DECLARE
                total_balance BIGINT;
            BEGIN
                IF OLD.balance IS DISTINCT FROM NEW.balance THEN
                    SELECT SUM(balance) INTO total_balance
                    FROM accounts
                    WHERE user_id = NEW.user_id;

                    UPDATE users
                    SET balance= total_balance
                    WHERE id = NEW.user_id;
                end if;

                RETURN NEW;
            end;
            \$\$ LANGUAGE plpgsql;

            CREATE TRIGGER update_user_balance_after_account_update
            AFTER UPDATE OF balance ON accounts
            FOR EACH ROW
            EXECUTE FUNCTION update_user_balance_after_account_update();
            SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(<<<SQL
                DROP TRIGGER IF EXISTS update_user_balance_after_account_update ON accounts;
                DROP FUNCTION IF EXISTS update_user_balance_after_account_update();
            SQL);
    }
};
