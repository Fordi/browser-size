The Browser Size Project
========================

The Goal
--------
The idea here is to create and maintain a record of users' browser 
sizes, and to be able to communicate quickly the likelihood of all 
browser sizes in the context of a design.

Examples of the output can be found at Cache/image-*

The Methods
-----------
To get user data, I want to get a snippet of code on as many pages as I 
can, so as to capture as many browser sizes as I can.  

The code in question:
<pre>&lt;script type="text/javascript"&gt;(function(d){var b=(d.documentElement||d.body);(new Image()).src="http://fordi.org/sz/"+b.clientWidth+"x"+b.clientHeight})(document)&lt;/script&gt;</pre>

This snippet simply captures your browser's viewport dimensions, and 
makes a request to my server, e.g., http://fordi.org/sz/826x624

On the server side, the request is recorded once, leaving a note in the 
session that you've been checked.  When stored, the browser size is 
stored only as an aggregate; that is to say, the database looks to see 
how many instances of an 826 pixel wide screen it has seen today, and 
increments that.

The information is stored indefinitely, but for the purposes of 
performance and in staying current, a trade-off is made between keeping 
data and degrading it.

Data older than 10 months is moved from the main tables into archive 
tables, so as not to clutter up the indexing space.  All data has a 
half-life of 1 month.  The granularity, based on how I'm collecting the 
data (daily aggregate) is one day.  This means that data at the 10 month 
mark  has less than 1/1024th the impact as a fresh click.

The aggregated data, month's data, and full archive are all available 
for consumption as JSON.

The API
-------
For the moment, API calls are unauthenticated.  They are requested in 
the form of:

> http://fordi.org/sz/<em>method</em>

The following section will list out methods in the following form:

_returnContentType_ _methodName_ [_arguments_]

> Description of action  
> Considerations and limitations  
>  
> * Argument : Description

application/json data

> Get the past month's data as a JSON object  
> Updated daily after initial demand

application/json fullData

> Get the entire archive of data  
> Updated monthly after initial demand

application/json aggregateData

> Get the aggregate data (summed impressions with 1 month half life)  
> Updated daily after initial demand

image/png left.png

> Get a left-aligned foldmap  
> Updated weekly after initial demand  
> Note that if you are the first to request a foldmap this week, you get to wait for the system to generate them

image/png right.png

> Get a right-aligned foldmap  
> Updated weekly after initial demand  
> Note that if you are the first to request a foldmap this week, you get to wait for the system to generate them


image/png center.png

> Get a center-aligned foldmap  
> Updated weekly after initial demand  
> Note that if you are the first to request a foldmap this week, you get to wait for the system to generate them

HTTP301 phpSource

> Get the source code for this application  
> Presently links you to this repository

text/javascript magic.js

> Magic script for use if you don't want to put an inline JS snippet on your page  
> Can change at any time; you may want to copy it out.

The Madness
-------------------
It may not look it, but this project is based on an incredibly simplified MVC architecture.

There's only one controller, and that's /index.php, which also acts as dispatcher (which is, in an MVC arch, just the &Uuml;berkontroller).  The controller delegates its work to a switch statement (for complex endpoints, like /\d+x\d/), then to a set of actions, found in /Actions/[Action].php.

Our models and views are managed via a pair of simple utility functions (found in /Libraries/utilities.php): tpl and execQuery.  There's no hand-holding; the controller must do everything for itself.

String tpl(String viewName, Array localData=array())

> Executes a PHP script with some preloaded data, and returns the output content as a string  
>   
> * String viewName : refers to a PHP script in Views/[viewName].php  
> * Array localData : data with which the view will populate itself

PDOStatement execQuery(String modelName, Array localData=array())

> Executes an SQL statement with some preloaded data, and returns the PDOStatement handle with which to collect the data  
>  
> * String modelName : refers to an SQL script in Models/[modelName].sql  
> * Array localData : data with which to populate the SQL script
