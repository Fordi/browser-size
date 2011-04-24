REPLACE INTO browser_widths ( pixels, count, stamp ) 
	SELECT 
		:pixels, 
		IFNULL(MAX(COUNT),0)+1, 
		DATE(NOW()) 
	FROM browser_widths 
	WHERE 
		stamp=DATE(NOW()) AND
		pixels=:pixels;