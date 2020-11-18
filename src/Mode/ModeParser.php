<?php

declare(strict_types=1);

namespace Dgame\Fs\Mode;

interface ModeParser
{
    public function parse(string $mode): ModeState;
}
