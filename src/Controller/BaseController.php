<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constraint\ConstraintFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseController extends AbstractController
{
    /**
     * @param array $data
     * @param ValidatorInterface $validator
     * @param ConstraintFactoryInterface $constraint
     *
     * @return array|null
     */
    public function validateRequest(
        array $data,
        ValidatorInterface $validator,
        ConstraintFactoryInterface $constraint
    ): ?array
    {
        $errors = $validator->validate($data, $constraint->build());

        if ($errors->count()) {
            $errorsInfo = [];

            /** @var ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $errorsInfo[] = [
                    'field' => $error->getPropertyPath(),
                    'code' => $error->getCode(),
                    'message' => $error->getMessage(),
                    'value' => $error->getInvalidValue(),
                ];
            }
        }

        return $errorsInfo ?? null;
    }
}
