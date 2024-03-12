<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\FortuneCookie;
use App\Model\CategoryFortuneStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FortuneCookie>
 *
 * @method FortuneCookie|null find($id, $lockMode = null, $lockVersion = null)
 * @method FortuneCookie|null findOneBy(array $criteria, array $orderBy = null)
 * @method FortuneCookie[]    findAll()
 * @method FortuneCookie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FortuneCookieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FortuneCookie::class);
    }

    public function save(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public static function createFortuneCookiesStillInProductionCriteria(): Criteria
    {
        // uso una static function perchè così è chiamabile dalla entity
        return Criteria::create()
        ->andWhere(Criteria::expr()->eq('discontinued', false));
    }

    /**
    * @return CategoryFortuneStats
     */
    public function countNumberPrintedForCategory(Category $category): CategoryFortuneStats
    {
        // method 1 - qb e return array associativo
        /*
        $result = $this->createQueryBuilder("fortuneCookie")
        ->select("SUM(fortuneCookie.numberPrinted) as fortunesPrinted")
        ->addSelect("AVG(fortuneCookie.numberPrinted) fortunesAverage")
        ->addSelect("category.name")
        ->innerJoin("fortuneCookie.category", "category") // returns only rows with a match in both tables
        ->andWhere('fortuneCookie.category = :category')
        ->setParameter("category", $category)
        ->getQuery()
        // ->getSingleScalarResult();
        ->getSingleResult();
        */

        // usando una classe, possiamo preparare i dati in modo più pulito invece di avere un semplice array associativo


        // method 2 - qb e return classe
        /*$result = $this->createQueryBuilder("fortuneCookie")
        ->select(sprintf(
            "NEW %s(
                SUM(fortuneCookie.numberPrinted),
                AVG(fortuneCookie.numberPrinted),
                category.name
            )",
            CategoryFortuneStats::class
        ))
        ->innerJoin("fortuneCookie.category", "category") // returns only rows with a match in both tables
        ->andWhere('fortuneCookie.category = :category')
        ->setParameter("category", $category)
        ->getQuery()
        ->getSingleResult();
        */

        // method 3 - raw query e class
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT SUM(fortune_cookie.number_printed) AS fortunesPrinted, AVG(fortune_cookie.number_printed) fortunesAverage, category.name AS categoryName FROM fortune_cookie INNER JOIN category ON category.id = fortune_cookie.category_id WHERE fortune_cookie.category_id = :category';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'category' => $category->getId() // è possibile anche passare il parametro così: $stmt->bindValue('category', $category->getId());
        ]);

        // dd($result->fetchAssociative());

        return new CategoryFortuneStats(...$result->fetchAssociative()); // spread operator


        return $result;
    }

    //    /**
    //     * @return FortuneCookie[] Returns an array of FortuneCookie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FortuneCookie
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
