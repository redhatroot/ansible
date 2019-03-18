#!/bin/sh

/bin/cp -p {{ wpwebpath }}/wp-content/themes/twentyseventeen/assets/images/header_cats.jpg "{{ wpwebpath }}/wp-content/themes/twentyseventeen/assets/images/header.jpg"

mysql -u root {{ wpdbname }} < /tmp/cat_meme_takeover.sql

echo DONE!  Now reload the web page to see what the evil cat hacker clan did!
echo
echo

