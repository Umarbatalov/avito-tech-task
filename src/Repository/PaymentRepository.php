<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * Находим платежи по фильтру.
     *
     * @param array $filter
     * @param int $limit
     * @param int $offset
     *
     * @return mixed
     */
    public function findAllPayments(array $filter = [], int $limit = 50, int $offset = 0)
    {
        $builder = $this->createQueryBuilder('p');

        if (isset($filter['from'], $filter['to'])) {
            $builder
                ->andWhere('p.createdAt >= :from')
                ->andWhere('p.createdAt <= :to')
                ->setParameter('from', $filter['from'])
                ->setParameter('to', $filter['to']);
        }

        return $builder
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getArrayResult();
    }
}
