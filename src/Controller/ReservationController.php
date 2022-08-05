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

class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    #[Route('/reservation', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/reservation/new', name: 'app_reservation_new', methods: ['POST'])]
    public function new(Request $request, ReservationRepository $reservationRepository, RoomRepository $roomRepository)
    {
        $session = new Session();

         $reservation = new Reservation();
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
            'telefono' => $request->request->get('telInput') 
         ]);

         $reservationRepository->add($reservation, true);
         return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        // $reservation = new Reservation();
        // $form = $this->createForm(ReservationType::class, $reservation);
        
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $reservationRepository->add($reservation, true);

        //     return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        // }

        // return $this->renderForm('reservation/new.html.twig', [
        //     'reservation' => $reservation,
        //     'form' => $form,
        // ]);
    }

    #[Route('/reservation/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/reservation/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationRepository->add($reservation, true);

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/reservation/reservation/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation, true);
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }

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
