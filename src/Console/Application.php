<?php

declare(strict_types=1);

namespace PHPMND\Console;

use PHPMND\Command\RunCommand;
use PHPMND\Container;
use function sprintf;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    public const VERSION = '2.4.0';
    private const NAME = 'phpmnd';

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        parent::__construct(self::NAME, self::VERSION);
        $this->setDefaultCommand('run', true);

        $this->container = $container;
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->getLongVersion());
        $output->writeln('');

        if ($input->getFirstArgument() === null) {
            $input = new ArrayInput(['--help']);
        }

        return parent::doRun($input, $output);
    }

    public function getLongVersion(): string
    {
        return sprintf(
            '<info>%s</info> version <comment>%s</comment> by Povilas Susinskas',
            $this->getName(),
            $this->getVersion()
        );
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    protected function getDefaultCommands(): array
    {
        return [new HelpCommand(), new RunCommand()];
    }
}
