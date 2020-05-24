<?php

declare(strict_types=1);

namespace App\Constraint;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema()
 */
final class RegisterPaymentConstraintFactory implements ConstraintFactoryInterface
{
    /**
     * @OA\Property(
     *      property="amount",
     *      type="object",
     *      @OA\Property(
     *         property="value",
     *         type="float",
     *         example=1000.03,
     *         description="Сумма"
     *     ),
     *      @OA\Property(
     *         property="currency",
     *         type="string",
     *         example="RUB",
     *         enum={"RUB"},
     *         description="Валюта"
     *     )
     * ),
     * @OA\Property(
     *     property="purpose",
     *     type="string",
     *     example="За электроэнергию",
     *     description="Назначение платежа."
     * ),
     * @OA\Property(
     *     property="confirmation_url",
     *     type="string",
     *     example="https://website.com/return_url",
     *     description="URL магазина на который будет отправлено уведомлене."
     * )
     *
     */
    public function build(): Assert\Collection
    {
        return new Assert\Collection(
            [
                'allowExtraFields' => false,
                'allowMissingFields' => false,
                'fields' => [
                    'amount' => new Assert\Collection(
                        [
                            'value' => [
                                new Assert\NotBlank(),
                                new Assert\Type('float'),
                            ],
                            'currency' => [
                                new Assert\NotBlank(),
                                new Assert\Type('string'),
                                new Assert\EqualTo('RUB'),
                            ],
                        ]
                    ),
                    'confirmation_url' => new Assert\Optional(
                        [
                            new Assert\NotBlank(),
                            new Assert\Url(),
                        ]
                    ),
                    'purpose' => [
                        new Assert\NotBlank(),
                        new Assert\Type('string'),
                        new Assert\Length(['max' => 100,]),
                    ],
                ],
            ]
        );
    }
}
