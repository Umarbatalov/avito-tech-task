<?php

declare(strict_types=1);

namespace App\Constraint;

use Symfony\Component\Validator\Constraints\Collection;

interface ConstraintFactoryInterface
{
    public function build(): Collection;
}
