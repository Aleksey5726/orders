<?php

namespace app\interfaces;

Interface OrderLoaderInterface
{
    public function load (string $source): string;
}
