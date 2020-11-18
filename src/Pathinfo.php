<?php

declare(strict_types=1);

namespace Dgame\Fs;

final class Pathinfo
{
    private string $dirname;
    private string $filename;
    private ?string $extension;

    public function __construct(Path $path)
    {
        $info = pathinfo((string) $path);
        [
            'dirname' => $this->dirname,
            'filename' => $this->filename,
        ] = $info;
        $this->extension = $info['extension'] ?? null;
    }

    /**
     * @return string
     */
    public function getDirname(): string
    {
        return $this->dirname;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }
}
