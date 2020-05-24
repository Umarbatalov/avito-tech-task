<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constraint\RegisterPaymentConstraintFactory;
use App\Entity\PaymentSession;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RegisterPaymentController extends BaseController
{
    private ValidatorInterface $validator;
    private RegisterPaymentConstraintFactory $constraint;

    /** @var EntityManagerInterface|EntityManager $em */
    private EntityManagerInterface $em;

    /**
     * MakePaymentController constructor.
     *
     * @param ValidatorInterface $validator
     * @param RegisterPaymentConstraintFactory $constraint
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ValidatorInterface $validator,
        RegisterPaymentConstraintFactory $constraint,
        EntityManagerInterface $em
    ) {
        $this->validator = $validator;
        $this->constraint = $constraint;
        $this->em = $em;
    }

    /**
     * @Route("/payments/register", name="register_payment", methods={"POST"})
     *
     * @OA\Post(
     *     path="/payments/register",
     *     description="Создание новой платежной сессии",
     *     tags={"payments"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *            type="object",
     *            required={"amount", "purpose"},
     *            allOf={@OA\Schema(ref="#/components/schemas/RegisterPaymentConstraintFactory")}
     *        )
     *    ),
     *     @OA\Response(
     *        response="200",
     *        description="При успешном ответе мы получим ссылку на форму оплаты платежа.",
     *        @OA\JsonContent(
     *           type="object",
     *           allOf={@OA\Schema(ref="#/components/schemas/PaymentSession")},
     *           @OA\Property(
     *              property="payment_link",
     *              type="string",
     *              example="https://example.com/payments/card/form?sessionId=69ed9c34-9d0e-11ea-84a8-0242ac150002"
     *           )
     *        )
     *    ),
     *    @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     description="Переданные данные невалидны."
     *   ),
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function registerPayment(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validateRequest($data, $this->validator, $this->constraint);

        if ($errors !== null) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $paymentSession = new PaymentSession();

        $paymentSession
            ->setAmount($data['amount'])
            ->setPurpose($data['purpose']);

        if (isset($data['confirmation_url'])) {
            $paymentSession->setConfirmationUrl($data['confirmation_url']);
        }

        try {
            $this->em->persist($paymentSession);
            $this->em->flush();
        } catch (ORMException $e) {
            return new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $paymentLink = $this->generateUrl(
            'payment_form',
            ['sessionId' => $paymentSession->getUuid(),],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(
            [
                'uuid' => $paymentSession->getUuid(),
                'amount' => $paymentSession->getAmount(),
                'purpose' => $paymentSession->getPurpose(),
                'confirmation_url' => $paymentSession->getConfirmationUrl(),
                'payment_link' => $paymentLink,
            ]
        );
    }
}
