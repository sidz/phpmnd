<?php

declare(strict_types=1);

namespace PHPMND\Tests;

use const DIRECTORY_SEPARATOR;
use PHPMND\FileReport;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class FileReportTest extends TestCase
{
    public function test_file_report(): void
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
            __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . "$name.php",
            'tests' . DIRECTORY_SEPARATOR . 'Fixtures',
            'tests' . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . "$name.php"
        );
    }
}
