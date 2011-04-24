SELECT 
	pixels, SUM(count) as "count"
	FROM archive_heights
	GROUP BY pixels;