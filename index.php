<?php

use Dgame\Fs\Mode;
use Dgame\Fs\File;
use Dgame\Fs\Mode\DefaultModeParser;
use Dgame\Fs\Path;
use Dgame\Fs\Permission;
use Dgame\Fs\Permissions;

require_once 'vendor/autoload.php';

$modes = [
    'r',
    'r+',
    'rb',
    'rb+',
    'w',
    'w+',
    'wb',
    'wb+',
    'a',
    'a+',
    'ab',
    'ab+',
];

$parser = new DefaultModeParser();
foreach ($modes as $mode) {
    var_dump($parser->parse($mode));
}

var_dump((string) Mode::read()->inBinary());

$stream = File::memory(Mode::write()->inBinary()->withRead());
$stream->write('Hello');
print $stream->readFromStart(10);
$stream->write('Hello');
print $stream->readFromStart(10);

$permission = Permissions::new()
    ->forUser(Permission::readWriteExecute())
    ->forGroup(Permission::readWriteExecute())
    ->forOther(Permission::readWriteExecute())
    ->build();
var_dump($permission->toInt());
var_dump($permission->inOctal());
var_dump((string) $permission);

$permission = Permissions::new()
                         ->forUser(Permission::readWrite())
                         ->forGroup(Permission::readExecute())
                         ->forOther(Permission::readExecute())
                         ->build();
var_dump($permission->toInt());
var_dump($permission->inOctal());
var_dump((string) $permission);

$path = new Path('/etc/passwd');
var_dump($path->exists());
var_dump($path->getFilename());
var_dump($path->getPathInfo());
var_dump($path->getPermissions()->inOctal());
var_dump($path->getPermissions()->toInt());
var_dump((string) $path->getParent());
