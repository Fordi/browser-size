SELECT 
	pixels, SUM(count) as "count"
	FROM browser_heights
	WHERE stamp >= DATE(FROM_UNIXTIME(UNIX_TIMESTAMP()-2629800))
	GROUP BY pixels;