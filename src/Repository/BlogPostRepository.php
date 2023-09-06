<?php

namespace App\Repository;

use App\Entity\BlogPost;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogPost>
 *
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(BlogPost $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(BlogPost $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getAll(): array
    {
        $queryBuilder = $this->createQueryBuilder('b');

        $currentDate = new DateTime();

        $queryBuilder
            ->andWhere($queryBuilder->expr()->lt('b.publishedAt', ':currentDate'))
            ->andWhere($queryBuilder->expr()->isNull('b.deletedAt'))
            ->setParameter('currentDate', $currentDate);

        $blogPosts = $queryBuilder->getQuery()->getResult();

        return $blogPosts;
    }

    public function getAllInArray(): array
    {
        $blogPosts = $this->getAll();

        $blogPostArray = $this->collectionToArry($blogPosts);

        return $blogPostArray;
    }

    private function collectionToArry($collection): array
    {
        $returnArray = [];

        foreach ($collection as $item) {
            array_push($returnArray, $item->toArray());
        }
        return $returnArray;
    }
}
