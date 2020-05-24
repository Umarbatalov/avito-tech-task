<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PaymentSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentSession[]    findAll()
 * @method PaymentSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PaymentSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentSession::class);
    }
}
