<?php

declare(strict_types=1);

namespace Dgame\Fs;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class Path
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getFilename(): string
    {
        return basename($this->path);
    }

    public function getParent(): self
    {
        return new self(dirname($this->path));
    }

    public function isLink(): bool
    {
        return is_link($this->path);
    }

    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    public function isExecutable(): bool
    {
        return is_executable($this->path);
    }

    public function getPathInfo(): PathInfo
    {
        return new PathInfo($this);
    }

    public function rename(string $newname): bool
    {
        return rename($this->path, $newname);
    }

    public function isDirectory(): bool
    {
        return is_dir($this->path);
    }

    public function exists(): bool
    {
        return file_exists($this->path);
    }

    public function isFile(): bool
    {
        return is_file($this->path);
    }

    public function deleteAll(): void
    {
        if ($this->isDirectory()) {
            $this->cleanContent();
        }

        $this->delete();
    }

    public function cleanContent(): void
    {
        if (!$this->exists()) {
            return;
        }

        if ($this->isFile()) {
            \Safe\file_put_contents($this->path, '');

            return;
        }

        $rit = new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS);
        $rit = new RecursiveIteratorIterator($rit, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($rit as $file) {
            $file->isDir() ? \Safe\rmdir($file->getPathname()) : \Safe\unlink($file->getPathname());
        }
    }

    public function delete(): void
    {
        if (!$this->exists()) {
            return;
        }

        if ($this->isFile()) {
            \Safe\unlink($this->path);
        } else {
            \Safe\rmdir($this->path);
        }
    }

    public function create(Permissions $permissions = null): void
    {
        if ($this->exists()) {
            return;
        }

        if ($this->isFile()) {
            \Safe\file_put_contents($this->path, '');

            if ($permissions !== null) {
                $this->changePermissionsTo($permissions);
            }

            return;
        }

        if ($permissions === null) {
            \Safe\mkdir($this->path, 0777, true);
        } else {
            \Safe\mkdir($this->path, $permissions->inOctal(), true);
        }
    }

    public function changePermissionsTo(Permissions $permissions): void
    {
        \Safe\chmod($this->path, $permissions->inOctal());
    }

    public function getPermissions(): Permissions
    {
        return Permissions::for($this);
    }

    public function __toString(): string
    {
        return $this->path;
    }
}
