PHPPHP
======

![YO DAWG](http://i.stack.imgur.com/JarJ0.jpg)

A PHP VM implementation written in PHP.

This is a basic VM implemented in PHP using the [AST generating parser](https://github.com/nikic/PHP-Parser) developed by @nikic

To see what's supported so far, check out the opcodes.

Right now, functions (definitions and calls) are supported, if statements (with basic boolean operations), if statements, as are variables and some basic variable operations...

Usage
=====

You can run the implementation from the command line using the `php.php` file.

    php php.php -r "var_dump('foo');"
    
Or with a file:

    php php.php ../test.php
    
It only supports relative includes off the base file now (no include path parsing *yet*)...

For The Love Of God, Why?
=========================

I ask, *Why Not?*
