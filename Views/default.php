<!DOCTYPE HTML>
<html lang="en-US">
	<head>
		<meta charset="UTF-8" />
		<title>A Plea for Assistance, with a Carrot of Data</title>
		<script type="text/javascript" src="pixel.js"></script>
		<link rel="stylesheet" href="Static/landing.css"></link>
	</head>
	<body>
		<div id="page">
			<div id="section">
				<pre id="select">&lt;script type=<wbr />"text/<wbr />javascript"&gt;<wbr /><?=preg_replace('/([^\w]+)/','\1<wbr />', tpl('magic_script'))?><wbr />&lt;/script&gt;</pre>
				
				<p>
					<strong>Hello</strong>, fellow web developers.
				</p>
				<p>
					My name is Bryan Elliott, and I have a proposal for you.
				</p>
				<p>
					I've been on enough calls with clients and partners wanting to know if something is above or below the &ldquo;fold&rdquo;.  As you know, this question represents a fundamental lack of understanding of how the web works.
				</p>
				<p>
					At present there is a <em>sort-of</em> answer, found in <a href="http://browsersize.googlelabs.com/">Google Browser Size</a>, however, this solution has a number of shortfalls:
				</p>
				<ul>
					<li>The solution to click-through is clunky and lame</li>
					<li>The graphic is static, and isn't updated with actual traffic</li>
					<li>The aggregate traffic data used to produce the graphic is not public, so can't be used for mashing up other projects</li>
					<li>The color scheme isn't informative</li>
					<li>The graphic only works for left-aligned sites</li>
				</ul>
				<p>
					As a solution to these issues, I'm launching a live-traffic implementation.  I don't have the traffic drivers that Google has, so I must plead.
				</p>
				<p>
					So here's the deal: Add the code <a href="#select">above</a> to any sites you manage.  It simply works out the browser's viewport and reports it to me (in the form of a URL: http://fordi.org/sz/[Width]x[Height]).  This information is stored in a database as a daily aggregate<a class="footnote" href="#foot1">1</a>.
				</p>
				<p>
					In return, I will publish the <a href="data">past month's data</a>, the <a href="aggregateData">aggregate used for producing the maps</a>, the <a href="fullData">full history</a>, and foldmaps, aligned <a href="left.png">left</a>, <a href="center.png">centered</a>, and <a href="right.png">right</a>.
				</p>
				<p>
					Additionally, I intend to produce a competing tool to Google's offering and a <a href="javascript:<?=htmlEntities(tpl('bookmarklet'))?>">bookmarklet</a>.  If they want to snap it up, that's cool, too.
				</p>
				<p>
					Lastly, I intend to publish the source code for the backend work.
				</p>
				
				<ol class="footnotes">
					<li id="foot1">
						That is to say, the database itself is structured only to keep a running total of hits for a particular size for a given day.  My original thought was to not even discriminate for days, but it occurred to me that to keep current with use trends, I might want to be able to half-life the hits.  That means keeping time.  I think days are a wide enough span to preserve source anonymity.
					</li>
				</ul>
			</div>
		</div>
	</body>
</html>