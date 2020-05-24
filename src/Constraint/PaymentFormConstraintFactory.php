<?php

declare(strict_types=1);

namespace App\Constraint;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * @OA\Schema()
 */
final class PaymentFormConstraintFactory implements ConstraintFactoryInterface
{
    /**
     * @OA\Property(
     *     property="session_uuid",
     *     type="string",
     *     example="10b47221-9948-11ea-9402-0242ac190002",
     *     description="Идентификатор сессии"
     * ),
     * @OA\Property(
     *     property="card_number",
     *     type="string",
     *     example="5500000000000004",
     *     description="Номер карты"
     * )
     */
    public function build(): Collection
    {
        return new Collection(
            [
                'allowExtraFields' => false,
                'allowMissingFields' => false,
                'fields' => [
                    'session_uuid' => [
                        new Assert\NotBlank(),
                        new Assert\Uuid(),
                    ],
                    'card_number' => [
                        new Assert\NotBlank(),
                        new Assert\Luhn(),
                    ],
                ],
            ]
        );
    }
}
