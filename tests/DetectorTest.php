<?php

declare(strict_types=1);

namespace PHPMND\Tests;

use function array_merge;
use function in_array;
use PHPMND\Console\Option;
use PHPMND\Detector;
use PHPMND\Extension\ArgumentExtension;
use PHPMND\Extension\ArrayExtension;
use PHPMND\Extension\AssignExtension;
use PHPMND\Extension\ConditionExtension;
use PHPMND\Extension\DefaultParameterExtension;
use PHPMND\Extension\OperationExtension;
use PHPMND\Extension\PropertyExtension;
use PHPMND\Extension\ReturnExtension;
use PHPMND\Extension\SwitchCaseExtension;
use PHPUnit\Framework\TestCase;

class DetectorTest extends TestCase
{
    public function test_detect_default(): void
    {
        $detector = $this->createDetector($this->createOption());
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertSame(
            [
                [
                    'line' => 14,
                    'value' => 2,
                ],
                [
                    'line' => 15,
                    'value' => 15,
                ],
                [
                    'line' => 18,
                    'value' => 10,
                ],
                [
                    'line' => 20,
                    'value' => 5,
                ],
                [
                    'line' => 26,
                    'value' => 7,
                ],
                [
                    'line' => 31,
                    'value' => 18,
                ],
                [
                    'line' => 50,
                    'value' => -1,
                ],
            ],
            $fileReport->getEntries()
        );
    }

    public function test_detect_with_assign_extension(): void
    {
        $option = $this->createOption([new AssignExtension()]);
        $option->setIncludeNumericStrings(true);
        $detector = $this->createDetector($option);
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 5,
                'value' => '4',
            ],
            $fileReport->getEntries()
        );
    }

    public function test_detect_with_property_extension(): void
    {
        $option = $this->createOption([new PropertyExtension()]);
        $detector = $this->createDetector($option);
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 11,
                'value' => 6,
            ],
            $fileReport->getEntries()
        );
    }

    public function test_detect_with_array_extension(): void
    {
        $option = $this->createOption([new ArrayExtension()]);
        $detector = $this->createDetector($option);
        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 30,
                'value' => 13,
            ],
            $fileReport->getEntries()
        );
    }

    public function test_detect_with_argument_extension(): void
    {
        $option = $this->createOption([new ArgumentExtension()]);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 25,
                'value' => 4,
            ],
            $fileReport->getEntries()
        );
    }

    public function test_detect_with_default_parameter_extension(): void
    {
        $option = $this->createOption([new DefaultParameterExtension()]);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 13,
                'value' => 4,
            ],
            $fileReport->getEntries()
        );
    }

    public function test_detect_with_operation_extension(): void
    {
        $option = $this->createOption([new OperationExtension()]);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 40,
                'value' => 15,
            ],
            $fileReport->getEntries()
        );

        $this->assertNotContains(
            [
                'line' => 40,
                'value' => 21,
            ],
            $fileReport->getEntries()
        );
    }

    public function test_detect_with_ignore_number(): void
    {
        $ignoreNumbers = [2, 10];
        $option = $this->createOption();
        $option->setIgnoreNumbers($ignoreNumbers);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        foreach ($fileReport->getEntries() as $entry) {
            $this->assertFalse(in_array($entry['value'], $ignoreNumbers, true));
        }
    }

    public function test_detect_with_ignore_funcs(): void
    {
        $ignoreFuncs = ['round'];
        $option = $this->createOption([new ArgumentExtension()]);
        $option->setIgnoreFuncs($ignoreFuncs);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertNotContains(
            [
                'line' => 25,
                'value' => 4,
            ],
            $fileReport->getEntries()
        );
    }

    public function test_detect_include_strings(): void
    {
        $option = $this->createOption();
        $option->setIncludeStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 46,
                'value' => 'string',
            ],
            $fileReport->getEntries()
        );
    }

    public function test_detect_include_strings_and_ignore_string(): void
    {
        $option = $this->createOption();
        $option->setIncludeStrings(true);
        $option->setIgnoreStrings(['string']);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertNotContains(
            [
                'line' => 45,
                'value' => 'string',
            ],
            $fileReport->getEntries()
        );
    }

    public function test_dont_detect0_and1_with_include_numeric_strings(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new AssignExtension()]);
        $option->setIncludeNumericStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_2'));

        $this->assertEmpty($fileReport->getEntries());
    }

    public function test_detect_reading_number(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new ArrayExtension()]);
        $option->setIncludeNumericStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 64,
                'value' => 1234,
            ],
            $fileReport->getEntries()
        );
    }

    public function test_allow_array_mapping_with_array_extension(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new ArrayExtension()]);
        $option->setAllowArrayMapping(true);
        $option->setIncludeNumericStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $this->assertContains(
            [
                'line' => 32,
                'value' => 18,
            ],
            $fileReport->getEntries()
        );

        $this->assertContains(
            [
                'line' => 33,
                'value' => 1234,
            ],
            $fileReport->getEntries()
        );

        $this->assertContains(
            [
                'line' => 34,
                'value' => 1234,
            ],
            $fileReport->getEntries()
        );

        $this->assertNotContains(
            [
                'line' => 30,
                'value' => 13,
            ],
            $fileReport->getEntries()
        );
    }

    public function test_default_ignore_functions(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new ArrayExtension()]);
        $option->setIncludeNumericStrings(true);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_1'));

        $results = $fileReport->getEntries();

        $this->assertNotContains(
            [
                'line' => 56,
                'value' => 13,
            ],
            $results
        );

        $this->assertNotContains(
            [
                'line' => 57,
                'value' => 3.14,
            ],
            $results
        );

        $this->assertNotContains(
            [
                'line' => 58,
                'value' => 10,
            ],
            $results
        );
    }

    public function test_check_for_magic_array_constants(): void
    {
        $option = $this->createOption();
        $option->setExtensions([new ArrayExtension()]);
        $detector = $this->createDetector($option);

        $fileReport = $detector->detect(FileReportTest::getTestFile('test_3'));

        $this->assertContains(
            [
                'line' => 4,
                'value' => 2,
            ],
            $fileReport->getEntries()
        );
    }

    private function createOption(array $extensions = []): Option
    {
        $option = new Option();
        $option->setExtensions(
            array_merge(
                [
                    new ReturnExtension(),
                    new ConditionExtension(),
                    new SwitchCaseExtension(),
                ],
                $extensions
            )
        );

        return $option;
    }

    private function createDetector(Option $option): Detector
    {
        return new Detector($option);
    }
}
