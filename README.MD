## Requirements

The following versions of PHP are supported.

* PHP 7.1+

## Installation

To install, use composer:

```
php composer.phar require pe/component-cronos-core
```

## Usage

```php
<?php

namespace PE\Component\Cronos;

use PE\Component\Cronos\Core\Executor\ExecutorMultiThread;
use PE\Component\Cronos\Core\Executor\ExecutorSingleThread;
use PE\Component\Cronos\Core\Handler;
use PE\Component\Cronos\Core\ModuleInterface;
use PE\Component\Cronos\Core\Runner;
use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Core\Server;
use PE\Component\Cronos\Core\ServerInterface;
use PE\Component\Cronos\Process\Daemon;
use PE\Component\Cronos\Process\Process;

// Configure single thread executor
$executorSingleThread = new ExecutorSingleThread(
    10000// <-- Max executed tasks, 0 for infinite
);

// OR use multi thread if posix bases operating system used
$executorMultiThread = new ExecutorMultiThread(
    new Process(),     // <-- Thread prototype, for use forking and set title
    10000,             // <-- Max executed tasks, 0 for infinite
    5,                 // <-- Max child processes at same time,
    'cornos %s process'// <-- Title format for master and child
);

// Configure task handler, by default you can use callable handler
$handler = new Handler(function (ServerInterface $server, TaskInterface $task) {
    // You can set task estimate at start
    $server->setTaskEstimate($task, 10000);
    
    // Handle task execution depends on project
    
    // You can set task progress at end of processing chunk of data
    $server->setTaskProgress($task, 999);
    
    // You can return one of statuses below, DONE used if none returned
    return TaskInterface::STATUS_IN_PROGRESS
        ?? TaskInterface::STATUS_DONE
        ?? TaskInterface::STATUS_ERROR;
});

// Configure runner
$runner = new Runner(
    $executorSingleThread ?? $executorMultiThread,
    $handler
);

// Configure daemon, optional, use NULL if it not used
$daemon = new Daemon('path/to/pid.file');

// Configure server
$server = new Server($runner, $daemon ?? null);

// Configure modules by call:
/* @var $module ModuleInterface */
$module = '...';
$server->attachModule('UNIQUE_MODULE_NAME', $module);

// Now you can run server by call:
$server->run();

// Or you can use daemon logic by call methods:
$server->start();// For run in background
$server->stop(); // For stop background process
```
