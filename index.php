<?php

use Dgame\File\Mode;
use Dgame\File\File;
use Dgame\File\Mode\DefaultModeParser;

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

class Offset
{
    private int $length;

    public function __construct(int $length)
    {
        $this->length = $length;
    }

    final public function getLength(): int
    {
        return $this->length;
    }
}

final class PositiveOffset extends Offset
{
    public function __construct(int $length)
    {
        assert($length >= 0);

        parent::__construct($length);
    }
}

final class NegativeOffset extends Offset
{
    public function __construct(int $length)
    {
        assert($length <= 0);

        parent::__construct($length);
    }
}
