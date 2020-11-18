<?php

declare(strict_types=1);

namespace Dgame\Fs;

use Dgame\Fs\Mode\DefaultModeParser;
use Dgame\Fs\Mode\ModeParser;

abstract class Mode
{
    protected bool $binary = false;

    private function __construct()
    {
    }

    public static function parse(string $mode, ?ModeParser $parser = null): Mode
    {
        $parser ??= new DefaultModeParser();
        $state = $parser->parse($mode);

        return $state->intoMode();
    }

    public static function read(): ReadMode
    {
        return new ReadMode();
    }

    public static function write(): WriteMode
    {
        return new WriteMode();
    }

    public static function append(): AppendMode
    {
        return new AppendMode();
    }

    public function inBinary(): self
    {
        $this->binary = true;

        return $this;
    }

    abstract public function __toString(): string;
}
