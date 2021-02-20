<?php

declare(strict_types=1);

namespace PHPMND;

use PHPMND\Extension\ArgumentExtension;
use PHPMND\Extension\ArrayExtension;
use PHPMND\Extension\AssignExtension;
use PHPMND\Extension\ConditionExtension;
use PHPMND\Extension\DefaultParameterExtension;
use PHPMND\Extension\Extension;
use PHPMND\Extension\OperationExtension;
use PHPMND\Extension\PropertyExtension;
use PHPMND\Extension\ReturnExtension;
use PHPMND\Extension\SwitchCaseExtension;

class ExtensionResolver
{
    const ALL_EXTENSIONS = 'all';

    /**
     * @var Extension[]
     */
    private $allExtensions;

    /**
     * @var Extension[]
     */
    private $defaultExtensions;

    /**
     * @var Extension[]
     */
    private $resolvedExtensions = [];

    public function resolve(array $extensionNames): array
    {
        $this->resolvedExtensions = $this->defaults();
        if (($allKey = array_search(self::ALL_EXTENSIONS, $extensionNames)) !== false) {
            $this->resolvedExtensions = $this->all();
            unset($extensionNames[$allKey]);
        }

        foreach ($extensionNames as $extensionName) {
            if ($this->startsWithMinus($extensionName)) {
                $this->removeExtension($extensionName);
                continue;
            }

            $this->addExtension($extensionName);
        }

        return $this->resolvedExtensions;
    }

    public function defaults(): array
    {
        if (null === $this->defaultExtensions) {
            $this->defaultExtensions = [
                new ConditionExtension,
                new ReturnExtension,
                new SwitchCaseExtension
            ];
        }

        return $this->defaultExtensions;
    }

    public function all(): array
    {
        if (null === $this->allExtensions) {
            $this->allExtensions = array_merge(
                [
                    new ArgumentExtension,
                    new ArrayExtension,
                    new AssignExtension,
                    new DefaultParameterExtension,
                    new OperationExtension,
                    new PropertyExtension
                ],
                $this->defaults()
            );
        }

        return $this->allExtensions;
    }

    private function addExtension(string $extensionName): void
    {
        if ($this->exists($extensionName)) {
            foreach ($this->all() as $extension) {
                if ($extension->getName() === $extensionName) {
                    $this->resolvedExtensions[] = $extension;

                    return;
                }
            };
        }
    }

    private function removeExtension(string $extensionName): void
    {
        $extensionNameWithoutMinus = substr($extensionName, 1);
        if ($this->exists($extensionNameWithoutMinus)) {
            foreach ($this->resolvedExtensions as $key => $resolvedExtension) {
                if ($extensionNameWithoutMinus === $resolvedExtension->getName()) {
                    unset($this->resolvedExtensions[$key]);

                    return;
                }
            }
        }
    }

    private function exists(string $extensionName): bool
    {
        foreach ($this->all() as $extension) {
            if ($extension->getName() === $extensionName) {
                return true;
            }
        }

        throw new \InvalidArgumentException(sprintf('Extension "%s" does not exist', $extensionName));
    }

    private function startsWithMinus(string $extensionName): bool
    {
        return 0 === strpos($extensionName, '-');
    }
}
