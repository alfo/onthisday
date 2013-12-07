On This Day for Little Printer
=============

This is a simple publication for the [Little Printer](http://bergcloud.com/littleprinter/) that pulls information from either the Wikipedia or BBC 'On This Day' RSS feed.

It's still a bit rough around the edges, but it works. Feel free to improve it.

It is under a Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.

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

You can choose to receive newer events (typically 1800s to 2000s) or older events (1000s to 1600s or so). This decision is simply put into effect by selecting the last events in the RSS feed as opposed to the first ones.

There is no option to choose a feed because support for the BBC as a source was dropped at launch because their RSS was unreliable. You will notice that the code for scraping the front page of the BBC is still there, but not available for consumer use because it is also unreliable.

Announcements
-------------

I recently implemented an announcement system that allows me to tell users about important changes. The announcements are stored in a [JSON file](https://github.com/alfo/onthisday/blob/master/announcements.json), and a simple function checks whether the date attached to the announcement is today's date. If it is, it includes another 'view' file ([`announcement.php`](https://github.com/alfo/onthisday/blob/master/announcement.php)) which prints out the announcement to the users.

Feeds
-----

On This Day pulls from the Wikipedia feed:

	http://en.wikipedia.org/w/api.php?action=featuredfeed&feed=onthisday&feedformat=rss

The BBC front page is

	http://news.bbc.co.uk/onthisday/default.stm

And the BBC RSS (stuck on the 25th October) is

	http://news.bbc.co.uk/rss/on_this_day/front_page/rss.xml

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/alfo/onthisday/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

