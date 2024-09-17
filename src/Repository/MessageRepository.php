<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    // REVIEW: Use parameterized queries to prevent SQL injection,
    // avoid passing the entire Request object in repository methods,
    //ensure clear type hints and return types for better code clarity
    public function findByStatus(?string $status): array
    {
        $qb = $this->createQueryBuilder('m');

        // Filter by status if provided
        if ($status) {
            $qb->where('m.status = :status')
                ->setParameter('status', $status);
        }

        // Return the query result
        return $qb->getQuery()->getResult();
    }
}
