PHPPHP
======

![YO DAWG](http://i.stack.imgur.com/JarJ0.jpg)

A PHP VM implementation written in PHP.

This is a basic VM implemented in PHP using the [AST generating parser](https://github.com/nikic/PHP-Parser) developed by @nikic

To see what's supported so far, check out the opcodes.

Right now, functions (definitions and calls) are supported, if statements (with basic boolean operations), if statements, as are variables and some basic variable operations...

To see the current state of the implementation versus the [Core Language Test Suite](https://github.com/php/php-src/tree/master/tests/lang), check out the [test results file](https://github.com/ircmaxell/PHPPHP/blob/master/test-results.html.md).

Installation
============

To get the dependencies you need to use [composer](http://getcomposer.org):

    curl -s https://getcomposer.org/installer | php
    php composer.phar install

If you want to run the tests, install with the `--dev` flag. Grab a cup of coffee while it runs, as it will fetch the entire PHP source code in the process which can take a while (~10 minutes).

    php composer.phar install --dev

Usage
=====

## Linux/OSX
Use the `php.sh` shell script to invoke php.php. This requires that `php` be in your system path.

    ./php.sh -r "var_dump('foo');"

## Windows
Use the `php.bat` shell script to invoke php.php. This requires that `php.exe` be in your system path.

    php.bat -r "var_dump('foo');

## Manual
You can run the implementation from the command line using the `php.php` file directly.

    php php.php -r "var_dump('foo');"

Or with a file:

    php php.php ../test.php

It only supports relative includes off the base file now (no include path parsing *yet*)...

Testing
=======

## run-tests
To run the test suite (which mostly fails right now), install composer using the `--dev` flag (to install the test suite along side php's source code). Then use the `run-tests.(sh|bat)` file to execute them.

### Linux/OSX

    ./run-tests.sh

### Windows

    run-tests.bat

### Manual

If you choose not to install php's source code from composer, but have it elsewhere in your system path, you can use it directly.

    php path/to/runtests.php -p path/to/php.sh path/to/tests

TODO:
=====
Major things left to do:

1. Implement references properly
2. Implement Error Handling
3. Implement classes and objects
4. Implement Output Buffering.
5. Refactor output control to use SAPI modules
6. Implement true array hash tables (as opposed to the current reliance on the underlying hash table)
7. Implement Error Handling
8. Implement Exception Handling
9. Implement parameter parsing for core functions

For The Love Of God, Why?
=========================

There are a number of reasons why I did this...

1. It was something that I always wanted to do. For no particular reason other than I wanted to do it. I knew it was possible, but possible and doing it are two very different things.
2. It was far easier than I thought. The time to the initial commit (basic working VM) was only about 6 hours of work. So it's not like I spent a year building it...
3. It could be a useful education tool. For me learning the intricacies of the [Zend VM](http://lxr.php.net/xref/PHP_TRUNK/Zend/) better (I know it fairly well, but knowing and building give two different amounts of knowledge). But also for teaching others how the VM works. By giving a PHP implementation reference, hopefully more people can understand how the C implementation works (they both operate off the same generic implementation at this point).
4. It can enable certain interesting things. For example, we could hypothetically build an Opcode optimizer in PHP which parses the generated opcodes and optimizes things (removing redundant opcodes, statically compiling static expressions, etc). Then, we could build a PECL extension that would render those optimized opcodes directly into APC cache (or some other opcode cache mechanism).
5. It can be used to quickly mock up future functionality changes. Consider that it's easier to alter a PHP VM simply because you don't need to worry about memory management at all. So whipping up a POC for a significant feature should be a lot easier in PHP than C (at least for many non-full-time C developers).
6. It can be used to actually debug PHP code without working knowledge of GDB (and the underlying C structures). I wouldn't recommend this, as the chances of us getting it working 100% the same as the C implementation are practically 0, but it's a concept.
7. It could wind up becoming a full implementation (like PYPY). If we can compile the implementation using HipHop, and do some other lower-level tricks, there's a chance we could achieve performance somewhere near the C implementation. I doubt it, but it's possible. Especially if we add a JIT component (or a way of rendering out to machine code certain opcodes)...
8. **Why not?**

About The Authors
=================

Anthony Ferrara - [@ircmaxell](https://twitter.com/ircmaxell) [blog.ircmaxell.com](http://blog.ircmaxell.com)

Nikita Popov - [@nikita_ppv](https://twitter.com/nikita_ppv) [nikic.github.com](http://nikic.github.com/)
