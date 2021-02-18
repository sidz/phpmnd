<?php

namespace PHPMND\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use PHPMND\FileReport;
use PHPMND\FileReportList;

class FileReportListTest extends TestCase
{
    public function testAddFileReport(): void
    {
        $fileReportList = new FileReportList;
        /** @var FileReport|Mock $fileReport */
        $fileReport = $this->createMock(FileReport::class);
        $fileReport
            ->method('hasMagicNumbers')
            ->willReturn(true);

        $fileReportList->addFileReport($fileReport);

        $this->assertSame([$fileReport], $fileReportList->getFileReports());
        $this->assertTrue($fileReportList->hasMagicNumbers());
    }

    public function testDoesNotHaveMagicNumbers(): void
    {
        $fileReportList = new FileReportList;

        $this->assertFalse($fileReportList->hasMagicNumbers());
    }
}
