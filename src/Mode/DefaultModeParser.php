<?php

declare(strict_types=1);

namespace Dgame\Fs\Mode;

use DomainException;
use UnexpectedValueException;

use function Symfony\Component\String\s;

final class DefaultModeParser implements ModeParser
{
    public function parse(string $mode): ModeState
    {
        $letters = s($mode)->lower()->chunk();
        $firstLetter = array_shift($letters);
        if ($firstLetter === null) {
            throw new UnexpectedValueException('Expected one of either "r", "w" or "a"');
        }

        $state = self::createInitialModeStateFrom($firstLetter->toString());
        foreach ($letters as $letter) {
            $state->with($letter->toString());
        }

        return $state;
    }

    /**
     * @param string $letter
     *
     * @return ModeState
     */
    private static function createInitialModeStateFrom(string $letter): ModeState
    {
        switch ($letter) {
            case 'r':
                return new ReadModeState();
            case 'w':
                return new WriteModeState();
            case 'a':
                return new AppendModeState();
            default:
                throw new DomainException('Expected one of "r", "w" or "a" not ' . $letter);
        }
    }
}
