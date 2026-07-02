<?php

declare(strict_types=1);

namespace App\Filament\Commands\FileGenerators\Resources;

use App\Enums\NavigationGroup;
use Filament\Commands\FileGenerators\Resources\ResourceClassGenerator as BaseResourceClassGenerator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Override;
use UnitEnum;

final class ResourceClassGenerator extends BaseResourceClassGenerator
{
    #[Override]
    protected function addPropertiesToClass(ClassType $class): void
    {
        parent::addPropertiesToClass($class);

        $this->addNavigationGroupPropertyToClass($class);
    }

    #[Override]
    protected function addNavigationIconPropertyToClass(ClassType $class): void
    {
        // Do nothing, we don't want navigation icon property
    }

    private function addNavigationGroupPropertyToClass(ClassType $class): void
    {
        $this->namespace->addUse(UnitEnum::class);
        $this->namespace->addUse(NavigationGroup::class);

        $class->addProperty('navigationGroup', new Literal('NavigationGroup::System'))
            ->setProtected()
            ->setStatic()
            ->setType('string|UnitEnum|null');
    }
}
