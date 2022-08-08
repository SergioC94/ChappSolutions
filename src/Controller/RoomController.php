<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    #[Route('/room', name: 'app_room_index')]
    public function index(): Response
    {
        return $this->render('room/index.html.twig');
    }

    /**
     * Saves dates, total days and number of guests in session.
     * Gets the rooms availables on those dates with $numberGuests
     * $dates[0] => $entryDate
     * $dates[1] => $exitDate
     */
    #[Route('/room/new', name: 'app_room_new', methods: ['POST'])]
    public function new(Request $request, RoomRepository $roomRepository)
    {
        $numberGuests =  $request->request->get('guests');
        $dates = explode(" - ", $request->request->get('daterange'));

        $roomsAvailable = $roomRepository->findAvailableByData($dates,$numberGuests);

        $entryDate = new DateTime($dates[0]);
        $exitDate = new DateTime($dates[1]);
        $diff = $entryDate->diff($exitDate);

        $session = new Session();
        $session->set('totalDays', $diff->days);
        $session->set('numberGuests', $numberGuests);
        $session->set('dates', $dates);

        return $this->render('room/table.html.twig', array('roomsAvailable' => $roomsAvailable, 'totalDays' => $diff->days));
    }
}
