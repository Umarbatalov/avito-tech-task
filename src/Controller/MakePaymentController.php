<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constraint\PaymentFormConstraintFactory;
use App\Entity\Payment;
use App\Repository\PaymentSessionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class MakePaymentController extends BaseController
{
    private ValidatorInterface $validator;
    private PaymentFormConstraintFactory $constraint;
    private PaymentSessionRepository $paymentRepository;

    /** @var EntityManagerInterface|EntityManager $em */
    private EntityManagerInterface $em;

    /**
     * MakePaymentController constructor.
     *
     * @param ValidatorInterface $validator
     * @param PaymentFormConstraintFactory $constraint
     * @param EntityManagerInterface $em
     * @param PaymentSessionRepository $paymentSessionRepository
     */
    public function __construct(
        ValidatorInterface $validator,
        PaymentFormConstraintFactory $constraint,
        EntityManagerInterface $em,
        PaymentSessionRepository $paymentSessionRepository
    ) {
        $this->validator = $validator;
        $this->constraint = $constraint;
        $this->em = $em;
        $this->paymentRepository = $paymentSessionRepository;
    }

    /**
     *
     * @Route("/payments", name="make_payment", methods={"POST"})
     *
     * @OA\Post(
     *     description="Создаем платеж, передав номер карты и номер сессии.",
     *     path="/payments",
     *     tags={"payments"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/PaymentFormConstraintFactory")
     *    ),
     *     @OA\Response(
     *        response=200,
     *        description="Платеж успешно совершен.",
     *        @OA\JsonContent(ref="#/components/schemas/Payment")
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Переданные данные невалидны или истек срок платежной сессии"
     *    )
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @return Response
     */
    public function makePayment(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validateRequest($data, $this->validator, $this->constraint);

        if ($errors !== null) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $paymentSession = $this->paymentRepository->find($data['session_uuid']);

        if ($paymentSession === null || $paymentSession->hasExpired() || $paymentSession->isPaid()) {
            return $this->json('Payment session not correct.', Response::HTTP_BAD_REQUEST);
        }

        $payment = new Payment();

        $payment->setSession($paymentSession);
        $payment->setAmount($paymentSession->getAmount());
        $payment->setPurpose($paymentSession->getPurpose());

        try {
            $this->em->persist($payment);
            $this->em->flush();
        } catch (ORMException $e) {
            return new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            [
                'id' => $payment->getId(),
                'session_uid' => $payment->getSession()->getUuid(),
                'amount' => $payment->getAmount(),
                'purpose' => $payment->getPurpose(),
                'created_at' => $payment->getCreatedAt(),
            ]
        );
    }
}
