<?php

namespace App\Repository;


use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class TrickRepository
 *
 * @package App\Repository
 */
class TrickRepository extends ServiceEntityRepository
{
    /**
     * TrickRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function showMore($id)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id < ' . $id)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
            ;
    }
}