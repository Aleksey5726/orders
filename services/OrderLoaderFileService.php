<?php

namespace app\services;

use app\exceptions\OrderLoadException;
use app\interfaces\OrderLoaderInterface;

class OrderLoaderFileService implements OrderLoaderInterface
{
    private string $path;

    public function __construct(string $basePath, string $storagePath)
    {
        $this->path = $basePath . DIRECTORY_SEPARATOR .  $storagePath;
    }

    public function load(string $source): string
    {
        try {
            return file_get_contents($this->path . DIRECTORY_SEPARATOR . $source);
        } catch (\Exception $exception) {
            throw new OrderLoadException($exception->getMessage());
        }
    }
}
