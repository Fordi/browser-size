REPLACE INTO browser_heights ( pixels, count, stamp ) 
	SELECT 
		:pixels, 
		IFNULL(MAX(count),0)+1, 
		DATE(NOW()) 
	FROM browser_heights 
	WHERE 
		stamp=DATE(NOW()) AND 
		pixels=:pixels;