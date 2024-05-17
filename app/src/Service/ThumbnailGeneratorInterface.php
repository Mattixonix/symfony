<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Service;

interface ThumbnailGeneratorInterface
{
    public function generate(string $filepath, string $filename): string;
    public function getThumbnailName(string $realpath): string;
}
