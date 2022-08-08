<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function add(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Search reservations between two dates
     * @return Reservation[] an array of Reservation objects
     */
    public function findReservedRoomByDates(array $dates): array
    {

        $query = $this->getEntityManager()->createQuery("SELECT c FROM App\Entity\Room c WHERE c.id NOT IN (SELECT IDENTITY(r.room) FROM App\Entity\Reservation r WHERE :startDate BETWEEN r.entryDate and r.exitDate AND :endDate BETWEEN r.entryDate and r.exitDate)");
        $query->setParameter('startDate', $dates[0]);
        $query->setParameter('endDate', $dates[1]);
        return $query->getResult();
    }

    public function getPaginateReservations(int $pageSize=3, int $currentPage){
        $em=$this->getEntityManager();
         
        //Consulta DQL
        $dql = "SELECT r FROM App\Entity\Reservation r ORDER BY r.id DESC";
        $query = $em->createQuery($dql)
                               ->setFirstResult($pageSize * ($currentPage - 1))
                               ->setMaxResults($pageSize);
 
        $paginator = new Paginator($query, $fetchJoinCollection = true);
 
        return $paginator;
    }

}
