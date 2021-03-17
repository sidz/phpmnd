<?php

declare(strict_types=1);

namespace PHPMND\Tests;

use function array_map;
use function iterator_to_array;
use PHPMND\Console\Option;
use PHPMND\DetectionResult;
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
use PHPMND\PhpParser\FileParser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class DetectorTest extends TestCase
{
    private const FIXTURES_DIR = __DIR__ . '/Fixtures/Files';

    /**
     * @var Option
     */
    private $option;

    /**
     * @var Detector
     */
    private $detector;

    protected function setUp(): void
    {
        $this->option = new Option();
        $this->option->setExtensions([
            new ReturnExtension(),
            new ConditionExtension(),
            new SwitchCaseExtension(),
        ]);

        $this->detector = new Detector(
            new FileParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7)),
            $this->option
        );
    }

    public function test_detect_default(): void
    {
        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

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
            $this->getActualResult($result)
        );
    }

    public function test_detect_with_assign_extension(): void
    {
        $this->option->setExtensions([new AssignExtension()]);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 5,
                    'value' => '4',
                ],
                [
                    'line' => 18,
                    'value' => 3,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function test_detect_with_property_extension(): void
    {
        $this->option->setExtensions([new PropertyExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 11,
                    'value' => 6,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function test_detect_with_array_extension(): void
    {
        $this->option->setExtensions([new ArrayExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 30,
                    'value' => 13,
                ],
                [
                    'line' => 32,
                    'value' => 18,
                ],
                [
                    'line' => 33,
                    'value' => 123,
                ],
                [
                    'line' => 33,
                    'value' => 1234,
                ],
                [
                    'line' => 34,
                    'value' => 1234,
                ],
                [
                    'line' => 64,
                    'value' => 1234,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function test_detect_with_argument_extension(): void
    {
        $this->option->setExtensions([new ArgumentExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 3,
                    'value' => 3,
                ],
                [
                    'line' => 25,
                    'value' => 4,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function test_detect_with_default_parameter_extension(): void
    {
        $this->option->setExtensions([new DefaultParameterExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 13,
                    'value' => 4,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function test_detect_with_operation_extension(): void
    {
        $this->option->setExtensions([new OperationExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertSame(
            [
                [
                    'line' => 40,
                    'value' => 15,
                ],
                [
                    'line' => 43,
                    'value' => 20,
                ],
                [
                    'line' => 43,
                    'value' => 21,
                ],
            ],
            $this->getActualResult($result)
        );
    }

    public function test_detect_with_ignore_number(): void
    {
        $ignoreNumbers = [2, 10];
        $this->option->setIgnoreNumbers($ignoreNumbers);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        foreach ($this->getActualResult($result) as $entry) {
            $this->assertNotContains($entry['value'], $ignoreNumbers);
        }
    }

    public function test_detect_with_ignore_funcs(): void
    {
        $this->option->setExtensions([new ArgumentExtension()]);
        $this->option->setIgnoreFuncs(['round']);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertNotContains(
            [
                'line' => 25,
                'value' => 4,
            ],
            $this->getActualResult($result)
        );
    }

    public function test_detect_include_strings(): void
    {
        $this->option->setIncludeStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertContains(
            [
                'line' => 46,
                'value' => 'string',
            ],
            $this->getActualResult($result)
        );
    }

    public function test_detect_include_strings_and_ignore_string(): void
    {
        $this->option->setIncludeStrings(true);
        $this->option->setIgnoreStrings(['string']);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertNotContains(
            [
                'line' => 45,
                'value' => 'string',
            ],
            $this->getActualResult($result)
        );
    }

    public function test_dont_detect0_and1_with_include_numeric_strings(): void
    {
        $this->option->setExtensions([new AssignExtension()]);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_2.php'));

        $this->assertEmpty($this->getActualResult($result));
    }

    public function test_detect_reading_number(): void
    {
        $this->option->setExtensions([new ArrayExtension()]);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $this->assertContains(
            [
                'line' => 64,
                'value' => 1234,
            ],
            $this->getActualResult($result)
        );
    }

    public function test_allow_array_mapping_with_array_extension(): void
    {
        $this->option->setExtensions([new ArrayExtension()]);
        $this->option->setAllowArrayMapping(true);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $result = $this->getActualResult($result);

        $this->assertContains(
            [
                'line' => 32,
                'value' => 18,
            ],
            $result
        );

        $this->assertContains(
            [
                'line' => 33,
                'value' => 1234,
            ],
            $result
        );

        $this->assertContains(
            [
                'line' => 34,
                'value' => 1234,
            ],
            $result
        );

        $this->assertNotContains(
            [
                'line' => 30,
                'value' => 13,
            ],
            $result
        );
    }

    public function test_default_ignore_functions(): void
    {
        $this->option->setExtensions([new ArrayExtension()]);
        $this->option->setIncludeNumericStrings(true);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_1.php'));

        $results = $this->getActualResult($result);

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
        $this->option->setExtensions([new ArrayExtension()]);

        $result = $this->detector->detect($this->createSplFileInfo(self::FIXTURES_DIR . '/test_3.php'));

        $this->assertContains(
            [
                'line' => 4,
                'value' => 2,
            ],
            $this->getActualResult($result)
        );
    }

    private function createSplFileInfo(string $filePath): SplFileInfo
    {
        return new SplFileInfo($filePath, '', '');
    }

    private function getActualResult(iterable $result): array
    {
        return array_map(
            static function (DetectionResult $detectionResult) {
                return [
                    'line' => $detectionResult->getLine(),
                    'value' => $detectionResult->getValue(),
                ];
            },
            iterator_to_array($result, false)
        );
    }
}
