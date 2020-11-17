<?php

declare(strict_types=1);

namespace Dgame\File\Mode;

use DomainException;
use UnexpectedValueException;

final class DefaultModeParser implements ModeParser
{
    public function parse(string $mode): ModeState
    {
        $letters = str_split(strtolower($mode));
        $letter = reset($letters);
        if ($letter === false) {
            throw new UnexpectedValueException('Expected one of either "r", "w" or "a"');
        }

        $state = self::createInitialModeStateFrom($letter);
        foreach (array_slice($letters, 1) as $letter) {
            $state->with($letter);
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
