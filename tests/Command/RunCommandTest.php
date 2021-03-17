<?php

declare(strict_types=1);

namespace PHPMND\Tests\Command;

use PHPMND\Command\RunCommand;
use PHPMND\Console\Application;
use PHPMND\Container;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RunCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    protected function setUp(): void
    {
        $application = new Application(Container::create());
        $command = new RunCommand();
        $application->add($command);

        $this->commandTester = new CommandTester($application->find($command->getName()));
    }

    public function test_execute_no_files_found(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files'],
            '--suffixes' => 'bad_suffix',
        ]);

        $this->assertSame(RunCommand::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function test_execute_with_violation_option(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files'],
        ]);

        $this->assertSame(RunCommand::FAILURE, $this->commandTester->getStatusCode());
    }

    public function test_it_does_not_fail_command_when_file_on_path_does_not_exist(): void
    {
        $this->commandTester->execute([
            'directories' => ['tests/Fixtures/Files/FILE_DOES_NOT_EXIST.php'],
            '--extensions' => 'all',
        ]);

        $this->assertSame(RunCommand::SUCCESS, $this->commandTester->getStatusCode());
        $this->assertRegExp('/No files found to scan/i', $this->commandTester->getDisplay());
    }
}
