SELECT 
	pixels, SUM(count) as "count"
	FROM archive_widths
	GROUP BY pixels;