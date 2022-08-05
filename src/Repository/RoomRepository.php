<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 *
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function add(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
      * Search all rooms filterws by date and number of guest
      * @return Room[] Returns an array of room objects
     */
    public function findAvailableByData(array $dates, int $numberGuest): array
    {

        $query = $this->getEntityManager()->createQuery("SELECT DISTINCT c, COUNT(c) FROM App\Entity\Room c WHERE c.id NOT IN 
        (SELECT IDENTITY(r.room) FROM App\Entity\Reservation r WHERE :startDate BETWEEN r.entryDate and r.exitDate AND :endDate BETWEEN r.entryDate and r.exitDate)
        AND c.typeRoom IN (SELECT t.id FROM App\Entity\TypeRoom t where t.MaxGuests >= :numberGuest) GROUP BY c.typeRoom ORDER BY c.typeRoom");
        $query->setParameter('startDate', $dates[0]);
        $query->setParameter('endDate', $dates[1]);
        $query->setParameter('numberGuest', $numberGuest);
        return $query->getResult();
    }
    /**
      * Search all rooms filterws by date and number of guest
      * @return Room Returns an array of room objects
     */
    public function findAvailableByTypeRoom(array $dates, int $typeRoom): Room
    {

        $query = $this->getEntityManager()->createQuery("SELECT c FROM App\Entity\Room c WHERE c.id NOT IN 
        (SELECT IDENTITY(r.room) FROM App\Entity\Reservation r WHERE :startDate BETWEEN r.entryDate and r.exitDate AND :endDate BETWEEN r.entryDate and r.exitDate)
        AND c.typeRoom = :typeRoom");
        $query->setParameter('startDate', $dates[0]);
        $query->setParameter('endDate', $dates[1]);
        $query->setParameter('typeRoom', $typeRoom);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
