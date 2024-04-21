<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Source;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Source>
 *
 * @method Source|null find($id, $lockMode = null, $lockVersion = null)
 * @method Source|null findOneBy(array $criteria, array $orderBy = null)
 * @method Source[]    findAll()
 * @method Source[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Source::class);
    }

    /**
     * @param Source $data
     * @return void
     */
    public function upsertSourceWithArticle(Source $data): void
    {
        $entityManager = $this->getEntityManager();

        $source = $this->findOneBy(['name' => $data->getName()]);

        if (null === $source) {
            $source = new Source();
            $source->setCreatedAt(new \DateTimeImmutable());
        }

        $source->setName($data->getName());
        $source->setUpdatedAt(new \DateTimeImmutable());


        foreach ($data->getArticles() as $item) {
            $article = $entityManager->getRepository(Article::class)->findOneBy(['name' => $item->getName()]);

            if(null === $article) {
                $article = new Article();
                $article->setCreatedAt(new \DateTimeImmutable());
                $source->addArticle($article);
            }

            $article->setName($item->getName())
                ->setSource($source)
                ->setContent($item->getContent())
                ->setUrl($item->getUrl())
                ->setUpdatedAt(new \DateTimeImmutable());


            $entityManager->persist($article);
        }

        $entityManager->persist($source);
        $entityManager->flush();
    }

    //    /**
    //     * @return Source[] Returns an array of Source objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Source
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
