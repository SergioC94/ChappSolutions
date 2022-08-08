<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\RoomRepository;
use App\Repository\TypeRoomRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReservationController extends AbstractController
{

    const PAGINATOR_PAGE_SIZE = 6;

    /**
     * Gets the reservations paginated
     * @param $page CurrentPage to paginate
     * @return Response Returns reservation/index with reservations, currentPage and pagesCount
     */
    #[Route('/{page<\d+>?1}', name: 'app_index', methods: ['GET'])]
    #[Route('/reservation/{page<\d+>?1}', name: 'app_reservation_index', methods: ['GET'])]
    public function index(int $page, ReservationRepository $reservationRepository): Response
    {

        $error = "";
        try{

            $reservations  = $reservationRepository->getPaginateReservations(self::PAGINATOR_PAGE_SIZE,$page);
            $pagesCount = ceil( count($reservations) / self::PAGINATOR_PAGE_SIZE);

        }catch(\Exception $exception){
            $error = "No se han encontrado reservas";
        }
         
        return $this->render('reservation/index.html.twig', array(
            "reservations"=> $reservations,
            "currentPage" => $page,
            "pagesCount" => $pagesCount,
            "error"      => $error
        ));
    }

    /**
     * Creates the reservation object with session parameters and form parameters (in request)
     * if there is an exception or an error render room/index and show flash
     */
    #[Route('/reservation/new', name: 'app_reservation_new', methods: ['POST'])]
    public function new(Request $request, ReservationRepository $reservationRepository, RoomRepository $roomRepository, ValidatorInterface $validator)
    {
         $session = new Session();
         $reservation = new Reservation();
         $countException = 0;

         try{
             $dates = $session->get('dates');
    
             $reservation->setEntryDate(new DateTime($dates[0]));
             $reservation->setExitDate(new DateTime($dates[1]));
    
             $reservation->setGuestNumber($session->get('numberGuests'));
             $reservation->setLocator(uniqid());
    
             $room = $roomRepository->findAvailableByTypeRoom($dates,$session->get('typeRoom'));
             $reservation->setPrice($session->get('totalDays') * $room->getTypeRoom()->getPriceDay());
    
             $reservation->setRoom($room);
             $reservation->setContactDetails([
                'nombre' => $request->request->get('nameInput'),
                'email'  => $request->request->get('emailInput'),
                'telefono' => $request->request->get('numberInput') 
             ]);

             $errors = $validator->validate($reservation);

         }catch(\Expception $exception){
            $countException ++;
         }
        
         if (count($errors) > 0 || $countException > 0) {
            $this->addFlash('notice', 'Hay un error en la reserva, por favor pongase en contacto.');
            return $this->render('room/index.html.twig', []);
        }
     
         $reservationRepository->add($reservation, true);
         $this->addFlash('ok', 'Reserva realizada correctamente');
         return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        
    }

    #[Route('/reservation/reservation/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation, true);
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Store the type id in a session variable and return conctactDetails form
     */
    #[Route('/contactDetail', name: 'app_reservation_contactDetail', methods: ['POST'])]
    public function contactDetail(Request $request, TypeRoomRepository $typeRoomRepository): Response
    {
        $session = new Session();
        $session->set('typeRoom', $request->request->get('typeId'));

        return $this->render('reservation/contactDetails_form.html.twig', [
            'dates' => $session->get('dates'),
            'totalDays' => $session->get('totalDays'),
            'numberGuests' => $session->get('numberGuests'),
            'typeRoom' => $typeRoomRepository->find($request->request->get('typeId'))
        ]);
    }
}
