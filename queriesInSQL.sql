-- First
SELECT COUNT(id)
FROM transactions
WHERE created_at >= NOW() - INTERVAL '1 hour';

-- Second
SELECT SUM(amount) AS total_amount from transactions
where created_at >= NOW() - INTERVAL '1 month';

-- Third
SELECT card_id,
       SUM(amount)         AS total_amount,
       (SELECT SUM(amount)
        FROM transactions
        WHERE created_at >= NOW() - INTERVAL '1 month'
          AND user_id = 1) AS overall_total_amount
from transactions
where created_at >= NOW() - INTERVAL '1 month'
  AND user_id = 1
group by card_id;
