<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\Message;

/**
 * Class MessageRepository
 *
 * @package App\Repository
 */
class MessageRepository extends ServiceEntityRepository
{
    /**
     * MessageRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param int $id
     * @param int $trick_id
     *
     * @return mixed
     */
    public function showMore($id, $trick_id)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id < ' . $id)
            ->andWhere('m.trick = '. $trick_id)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
            ;
    }
}
