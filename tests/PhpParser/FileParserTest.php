<?php

declare(strict_types=1);

namespace PHPMND\Tests\PhpParser;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class FileParserTest extends TestCase
{
    private static function createFileInfo(string $path, string $contents): SplFileInfo
    {
        return new class($path, $contents) extends SplFileInfo {
            /**
             * @var string
             */
            private $contents;

            public function __construct(string $path, string $contents)
            {
                parent::__construct($path, $path, $path);

                $this->contents = $contents;
            }

            public function getContents(): string
            {
                return $this->contents;
            }
        };
    }
}
