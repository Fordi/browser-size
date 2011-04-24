SELECT 
	pixels, 
	SUM(count*POW(0.5, (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(stamp))/2629800)) as "count"
FROM browser_heights
GROUP BY pixels 
ORDER BY pixels DESC;