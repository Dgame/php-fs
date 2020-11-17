<?php

declare(strict_types=1);

namespace Dgame\File\Mode;

interface ModeParser
{
    public function parse(string $mode): ModeState;
}
