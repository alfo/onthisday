On This Day for Little Printer
=============

This is a simple publication for the [Little Printer](http://bergcloud.com/littleprinter/) that pulls information from either the Wikipedia or BBC 'On This Day' RSS feed.

It's still a bit rough around the edges, but it works. Feel free to improve it.

Scripts
-------

These are the assets defined by the [specification](http://remote.bergcloud.com/developers/reference/)

*	edition/
	*	index.php - does the processing for the [edition](http://remote.bergcloud.com/developers/reference/edition)
*	sample/
	*	index.php - does the processing for the [sample](http://remote.bergcloud.com/developers/reference/sample)
*	validate_config/	
	*	index.php - does the [validation](http://remote.bergcloud.com/developers/reference/validate_config) of config passed from BERG
*	meta.json - the configuration as defined in the [docs](http://remote.bergcloud.com/developers/reference/metajson)
*	icon.png - 55px by 55px icon

And these are the rest of them

*	functions.php - includes the functions for scraping data from Wikipedia or the BBC
*	header.php - contains the HTML for the header
*	footer.php - contains the HTML for the footer
*	style.css - the CSS for everything
*	reset.css - Eric Meyer's Reset CSS v2.0
*	phpQuery.php - for parsing the HTML from Wikipedia

Configuration
-------------

The only configuration defined in `validate_config` is `source`, which should either be set as `bbc` or `wikipedia`, and defines whether to obtain the data from the BBC or Wikipedia.

Feeds
-----

On This Day pulls from either the front page of the BBC site:

	http://news.bbc.co.uk/onthisday/default.stm

Or the Wikipedia feed:

	http://en.wikipedia.org/w/api.php?action=featuredfeed&feed=onthisday&feedformat=rss