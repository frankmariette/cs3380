
# Question 1. This query ends up using an index on id as it is a primary key for the table and therefore unique, 
EXPLAIN ANALYZE SELECT id, is active, assets, name FROM banks WHERE id = '17317';

# Question 2. 
EXPLAIN ANALYZE SELECT * FROM banks WHERE state = 'Missouri';
# Seq Scan on banks  (cost=0.00..894.98 rows=996 width=124) (actual time=0.305..9.705 rows=996 loops=1)
#   Filter: ((state)::text = 'Missouri'::text)
#   Rows Removed by Filter: 26602
# Total runtime: 9.814 ms
CREATE INDEX ON banks(state);
EXPLAIN ANALYZE SELECT * FROM banks WHERE state = 'Missouri';
# Bitmap Heap Scan on banks  (cost=24.01..598.45 rows=996 width=124) (actual time=0.397..0.960 rows=996 loops=1)
#   Recheck Cond: ((state)::text = 'Missouri'::text)
#   ->  Bitmap Index Scan on banks_state_idx  (cost=0.00..23.76 rows=996 width=0) (actual time=0.323..0.323 rows=996 loops=1)
#         Index Cond: ((state)::text = 'Missouri'::text)
# Total runtime: 1.065 ms


#Question 3
SELECT * FROM banks ORDER BY name;
EXPLAIN ANALYZE SELECT * FROM banks ORDER BY name;
# Sort  (cost=4657.15..4726.14 rows=27598 width=124) (actual time=478.641..614.403 rows=27598 loops=1)
# Sort Key: name
# Sort Method: external merge  Disk: 3760kB
# Seq Scan on banks  (cost=0.00..825.98 rows=27598 width=124) (actual time=0.011..4.191 rows=27598 loops=1)
# Total runtime: 621.642 ms

CREATE INDEX ON banks(name);
EXPLAIN ANALYZE SELECT * FROM banks ORDER BY name;
# Index Scan using banks_name_idx on banks  (cost=0.41..3294.80 rows=27598 width=124) (actual time=0.018..16.313 rows=27598 loops=1)
# Total runtime: 17.824 ms
# Decrease of 603.818 ms or a 3488% improvement. 

# Question 4
CREATE INDEX ON banks(is_active);

# Question 5
SELECT * FROM banks WHERE is_active = TRUE;
EXPLAIN ANALYZE SELECT * FROM banks WHERE is_active = TRUE;
# Bitmap Heap Scan on banks  (cost=132.80..750.56 rows=6776 width=124) (actual time=3.274..6.293 rows=6776 loops=1)
#   Filter: is_active
#   ->  Bitmap Index Scan on banks_is_active_idx  (cost=0.00..131.11 rows=6776 width=0) (actual time=3.094..3.094 rows=6776 loops=1)
#         Index Cond: (is_active = true)
# Total runtime: 6.805 ms
SELECT * FROM banks WHERE is_active = FALSE;
EXPLAIN ANALYZE SELECT * FROM banks WHERE is_active = FALSE;
# Seq Scan on banks  (cost=0.00..825.98 rows=20822 width=124) (actual time=0.011..7.780 rows=20822 loops=1)
#   Filter: (NOT is_active)
#   Rows Removed by Filter: 6776
# Total runtime: 9.335 ms
# The index is created on a boolean value and by default indexes the true value. Because there are only one of two options, True/False,
# there is no reason to index both values. Indexing one will allow the table to simply remove the results of the index from the other. 

#Question 6
SELECT * FROM banks WHERE insured >= '2000-01-01';
EXPLAIN ANALYZE SELECT * FROM banks WHERE insured >= '2000-01-01';
#  Seq Scan on banks  (cost=0.00..894.98 rows=1450 width=124) (actual time=2.396..7.870 rows=1448 loops=1)
#   Filter: (insured > '2000-01-01'::date)
#   Rows Removed by Filter: 26150
# Total runtime: 7.982 ms

CREATE INDEX ON banks(insured) WHERE insured != '1934-01-01';
EXPLAIN ANALYZE SELECT * FROM banks WHERE insured >= '2000-01-01';
# Index Scan using banks_insured_idx on banks  (cost=0.29..573.93 rows=1450 width=124) (actual time=0.043..1.341 rows=1451 loops=1)
#   Index Cond: (insured >= '2000-01-01'::date)
# Total runtime: 1.491 ms

# Improvemnt of 6.491ms or 535% increase in speed. 


# Question 7
EXPLAIN ANALYZE SELECT id, name, city , state, assets, deposits FROM banks GrouP BY id HAVING assets/deposits < 0.5 AND deposits != 0;
#  HashAggregate  (cost=1055.88..1147.54 rows=9166 width=63) (actual time=33.303..33.348 rows=46 loops=1)
#   ->  Seq Scan on banks  (cost=0.00..1032.97 rows=9166 width=63) (actual time=25.646..33.269 rows=46 loops=1)
#         Filter: ((deposits <> 0::numeric) AND ((assets / deposits) < 0.5))
#         Rows Removed by Filter: 27552
# Total runtime: 33.426 ms

CREATE INDEX ratio ON banks (assets, deposits) WHERE deposits != 0 AND assets/deposits < 0.5;
EXPLAIN ANALYZE SELECT id, name, city , state, assets, deposits FROM banks GrouP BY id HAVING assets/deposits < 0.5 AND deposits != 0;

# HashAggregate  (cost=743.98..835.64 rows=9166 width=63) (actual time=0.141..0.205 rows=46 loops=1)
#   ->  Bitmap Heap Scan on banks  (cost=10.66..721.07 rows=9166 width=63) (actual time=0.034..0.076 rows=46 loops=1)
#         Recheck Cond: ((deposits <> 0::numeric) AND ((assets / deposits) < 0.5))
#         ->  Bitmap Index Scan on ratio  (cost=0.00..8.37 rows=9166 width=0) (actual time=0.023..0.023 rows=46 loops=1)
# Total runtime: 0.276 ms

# Improvement of 33.15 ms or 12100%




