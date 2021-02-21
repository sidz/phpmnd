<?php

declare(strict_types=1);

namespace PHPMND\Tests;

use PHPMND\FileReport;
use PHPMND\FileReportList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileReportListTest extends TestCase
{
    public function test_add_file_report(): void
    {
        $fileReportList = new FileReportList();
        /** @var FileReport|MockObject $fileReport */
        $fileReport = $this->createMock(FileReport::class);
        $fileReport
            ->method('hasMagicNumbers')
            ->willReturn(true);

        $fileReportList->addFileReport($fileReport);

        $this->assertSame([$fileReport], $fileReportList->getFileReports());
        $this->assertTrue($fileReportList->hasMagicNumbers());
    }

    public function test_does_not_have_magic_numbers(): void
    {
        $fileReportList = new FileReportList();

        $this->assertFalse($fileReportList->hasMagicNumbers());
    }
}
