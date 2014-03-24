```
   __ ________   ________  ____
  / // / __/ /  /  _/ __ \/ __/
 / _  / _// /___/ // /_/ /\ \
/_//_/___/____/___/\____/___/

```
Introduction
============

Helios is a Titan from Greek Methodology , he represents the Sun. see [wikipedia:helios](http://en.wikipedia.org/wiki/Helios)

However in this context Helios is a framework for creating command line applications in PHP. It is heavily inspired by Ruby's
[Thor](http://whatisthor.com). Helios is not designed to be a task runner like make and rake etc, but rather a way of
writing CLI applications , although it could always be a task runner very easily.

_Helios is designed to work with PHP >= 5.3_

__WARNING__
Helios is still very much a work in progress and this document is more a design doc , rather than a finished readme

Task File
=========
Helios uses a task file to define the commands (tasks) that you wish to run with helios. A task file is simple a PHP
file that defines a class called `Task` that extends `\Helios\Task` base class, from now on this will be referred too
as a task class.

The task file can have one of the following names , and it will picked up automatically.

* helios.php
* taskfile.php
* heliostask.php

each method on the task class will then become a command on the helios application. Tasks files can include other task
files , with other classes in them , in this case the class name will be the namespace and the method will become the
command

for example

```
class Foo extends \Helios\Task {
    final public method bar() {

    }
 }
```

will be invoked with the command `helios foo bar`

Arguments , Switches and Vars
============================
There 3 types of data that can be passed into a task , they are arguments , switchs and vars.

Arguments
---------
Arguments are used to pass required data to a task they contain no prefix or
suffix and are made available as a parameter on a method

for example

```
class Foo extends \Helios\Task {
    final public function bar($s) {
        echo $s;
    }
 }
```

invoked with

`helios bar hello`

would echo the word "hello" , because hello is passed to the parameter $s on the method.

*Assuming that the class is the in the taskfile and not an include.*  

Vars
----

Vars are used to pass extra data into the command. They use a `-` or `--` in front
of them to signify that they are vars and not commands or arguments. for example

`--output=~/logs/out.log` or `-o ~/logs/out.log` These are registered with the
task file and then made available on the `$this->options` class variable.

For example if you passed

```
helios bar --output=~/logs/out.log
```

The code to get at it in the task file would look like

```
class Foo extends \Helios\Task {
  final public function bar() {
    echo $this->options->output;
  }
}
```

and would echo "~/logs/out.log" to the CLI.

Switches
--------

Are special vars that can only have a boolean value i.e. true or false. They are
used much in the same way as the vars and are made available to class in the same
way as vars using `$this->options`.

The only difference is in the way they are invoked on the CLI. They can not
have extra data passed to them. For each switch that you register you will also
register 2 states true and false , following the standard command line convention
these appear as

True
```
--some-switch
-s
```

and false
```
--no-some-switch
-S
```

Vars and Switches
-----------------

Unlike Arguments , you must register a switch and a var before using it in your
code , this is done using the class var $optionsMap on the task class and setting
the value to an array.

example

```
class Foo extends \Helios\Task {
  final public $optionsMap = [
    "command" => [
      "debug" => [ALIAS => "verbose" , TYPE => "boolean" , DEFAULT => TRUE]
    ]
  ]
}
```

Will register a switch "debug" on the command "command" with a default value of
true, it will also alias "verbose" to "debug" meaning you can use it on the CLI
with

```
--debug
--verbose
-d
-v

--no-debug
--no-verbose
-D
-V
```

it will only be made available to the command that it has been mapped against in
this case command.

To make a switch global i.e. available to all tasks in task file use the
global map this is simple done by registering them against the command global i.e.
to make the above available globally you would change it to

```
class Foo extends \Helios\Task {
  final public $optionsMap = [
    "global" => [
      "debug" => [ALIAS => "verbose" , TYPE => "boolean" , DEFAULT => TRUE]
    ]
  ]
}
```

Finally you are able to change the way you invoke the vars and switches when
registering them using the INVOKE_WITH parameter this is mainly when you want
to stop name collisions such as `-v` could mean version or verbose.

so we could alter the above code only to allow verbose with the long syntax

```
$optionsMap = [
  "global" => [
    "debug" => [
      ALIAS       => "verbose",
      TYPE        => "boolean",
      DEFAULT     => TRUE,
      INVOKE_WITH => ["--verbose", "--debug" , "-d"]
      INVOKE      => ALLOW_BOTH
    ]
  ]
]
```

INVOKE = ALLOW_BOTH means that it will automatically register the other state
i.e. `--no-verbose` , `--no-debug` and `-D`. if you wanted to stop this behaviour
you can use INVOKE = ALLOW_ONLY which will only allow the options you have set
in INVOKE_WITH.

MORE
====

@TODO write more of the readme including document helper methods for outputing
etc.

LICENSE
=======
The MIT License (MIT)

Copyright (c) 2014 Martin Haynes AKA DotMH

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.