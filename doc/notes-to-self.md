
## example queries for getting total billed and total paid (hence, balance)

SELECT DISTINCT c.* FROM credit c JOIN credit_invoice ci ON c.id = ci.credit_id JOIN invoice i ON i.id = ci.invoice_id
JOIN service s ON s.invoice_id = i.id JOIN service_person sp ON sp.service_id = s.id WHERE person_id = 2;
```
+----+----------+------------+-------+--------+

| id | payer_id | date       | notes | amount |
+----+----------+------------+-------+--------+
| 24 |        2 | 2025-02-15 |       | 100000 |
| 49 |        2 | 2025-03-20 |       | 100000 |
| 69 |        2 | 2025-04-28 |       | 100000 |
+----+----------+------------+-------+--------+
3 rows in set (0.003 sec)

SELECT SUM(fee)/100 FROM service s JOIN service_person sp ON s.id = sp.service_id 
WHERE  sp.person_id = 2 AND s.invoice_id IS NOT NULL;
+--------------+
| SUM(fee)/100 |
+--------------+
|    3000.0000 |
+--------------+
1 row in set (0.001 sec)
```
