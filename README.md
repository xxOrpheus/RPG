RPG
===

This is a base for a MMORPG. It uses the Ratchet library for handling WebSockets.

It is powered by PHP, jQuery, WebSockets, HTML5 and CSS3. It is intended for modern browsers only.

How to
======

To use this, you need Ratchet, memcache and memcached installed. I recommend using Composer to install Ratchet. There are composer files already in this project for you to use.

Simply install Composer, then, in the root directory of the project, run "composer update."

# memcache is a little more complicated to install. You can get the binaries from the PECL website. It is just a PHP extension.

You can probably find memcached with a simple Google search.

Then, all you need to do is open run.bat (if you're a linux user, I'm sure you can figure this out yourself.)

After it is running, just go to index.html in your browser. The basic map should appear. Click login, then click register.

It is a little slow.