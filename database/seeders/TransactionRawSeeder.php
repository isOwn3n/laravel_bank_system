<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionRawSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("
            DO \$\$
            DECLARE
                total_records INT := 100000;
                chunk_size INT := 10000;
                statuses TEXT[] := ARRAY['pending', 'confirmed', 'cancelled'];
            BEGIN
                FOR i IN 1..total_records BY chunk_size LOOP
                    INSERT INTO transactions (card_id, user_id, amount, is_deposit, fee, status)
                    SELECT
                        (SELECT id FROM accounts ORDER BY random() LIMIT 1) AS card_id,
                        (SELECT id FROM users ORDER BY random() LIMIT 1) AS user_id,
                        trunc(random() * 100000) AS amount,
                        (random() < 0.02)::BOOLEAN AS is_deposit,
                        500 AS fee,
                        statuses[ceil(random() * array_length(statuses, 1))]
                    FROM generate_series(1, chunk_size);
                END LOOP;
            END \$\$;
        ");
    }
}
