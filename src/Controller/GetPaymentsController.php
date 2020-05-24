<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PaymentRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GetPaymentsController extends AbstractController
{
    private PaymentRepository $paymentRepository;

    /**
     * GetPaymentsController constructor.
     *
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @Route("/payments", name="get_payments_by_period", methods={"GET"})
     *
     * @OA\Get(
     *     path="/payments",
     *     description="Получаем список всех платежей за переданный период.",
     *     tags={"payments"},
     *     @OA\Parameter(
     *        name="filter[from]",
     *        in="query",
     *        description="Дата, от которой будет производится поиск.",
     *        @OA\Schema(type="datetime", example="2020-05-24")
     *     ),
     *     @OA\Parameter(
     *        name="filter[to]",
     *        in="query",
     *        description="Дата, до которой будет производится поиск.",
     *        @OA\Schema(type="datetime", example="2020-05-25")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Массив найденных платежей",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 ref="#/components/schemas/Payment"
     *             )
     *         )
     *     )
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getPayments(Request $request): Response
    {
        $filter = $request->get('filter') ?? [];

        $payments = $this->paymentRepository->findAllPayments($filter);

        return $this->json($payments);
    }
}
