<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
   public function getFeed($user): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.author = :user OR p.author IN (:friends) OR p.private = false')
           ->setParameter('user', $user)
            ->setParameter('friends', $user->getFriends())
           ->orderBy('p.createdAt', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

   public function getUserPost(User $user, User $me): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.author = :user AND (p.author IN (:friends) OR p.private = false OR p.author = :me)')
           ->setParameter('user', $user)
              ->setParameter('me', $me)
            ->setParameter('friends', $me->getFriends())
           ->orderBy('p.createdAt', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
