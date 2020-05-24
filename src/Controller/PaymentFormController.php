<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PaymentSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PaymentFormController extends AbstractController
{
    private PaymentSessionRepository $paymentSessionRepository;

    /**
     * PaymentFormController constructor.
     *
     * @param PaymentSessionRepository $paymentSessionRepository
     */
    public function __construct(PaymentSessionRepository $paymentSessionRepository)
    {
        $this->paymentSessionRepository = $paymentSessionRepository;
    }

    /**
     * @Route("/payments/card/form", name="payment_form", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getPaymentFormPage(Request $request): Response
    {
        $sessionId = $request->get('sessionId');

        if ($sessionId === null) {
            return $this->redirectToRoute('index');
        }

        if ($sessionId !== null) {
            $paymentSession = $this->paymentSessionRepository->find($sessionId);

            if ($paymentSession !== null && $paymentSession->hasNotExpired()) {
                $paymentProps = [
                    'amount' => $paymentSession->getAmount(),
                    'purpose' => $paymentSession->getPurpose(),
                ];
            }
        }

        return $this->render('payment_form/index.html.twig', $paymentProps ?? []);
    }
}
