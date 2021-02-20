<?php

declare(strict_types=1);

namespace PHPMND\Tests;

use PHPMND\FileReport;
use Symfony\Component\Finder\SplFileInfo;
use PHPUnit\Framework\TestCase;

class FileReportTest extends TestCase
{
    public function testFileReport(): void
    {
        $file = self::getTestFile('test_1');
        $fileReport = new FileReport($file);
        $fileReport->addEntry(1, 1);

        $this->assertSame(
            [
                [
                    'line' => 1,
                    'value' => 1,
                ],
            ],
            $fileReport->getEntries()
        );

        $this->assertSame($file, $fileReport->getFile());
        $this->assertTrue($fileReport->hasMagicNumbers());
    }

    public static function getTestFile(string $name): SplFileInfo
    {
        return new SplFileInfo(
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . "$name.php",
            'tests' . DIRECTORY_SEPARATOR . 'files',
            'tests' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . "$name.php"
        );
    }
}
