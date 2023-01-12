<?php

namespace App\Controller;

use App\Entity\Moves;
use App\Repository\MovesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

//#[Route('/moves')]
class MovesController extends AbstractController
{
    #[Route('/moves/', name: 'app_moves_index', methods: ['GET'])]
    public function index(MovesRepository $movesRepository, SerializerInterface $serializer): JsonResponse
    {
//        return $this->render('moves/index.html.twig', [
//            'moves' => $movesRepository->findAll(),
//        ]);

        return new JsonResponse(['moves' => $serializer->serialize($movesRepository->findAll(), JsonEncoder::FORMAT)], Response::HTTP_CREATED);

    }

    #[Route('/api/moves/new', name: 'app_moves_new', methods: ['POST'])]
    public function new(Request $request, MovesRepository $movesRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $newCoord = intval($request->get("coord"));
        $x = $newCoord % 19;
        $y = 19 - (($newCoord - $x) / 19);
        if ($x == 0) {
            $x = 19;
        }
        if ($y == 0) {
            $y = 19;
        }

        $str = str_split("abcdefghijklmnopqrs");
        $x = $str [$x - 1];
        $y = $str [$y - 1];
        $gameId = intval($request->get("game_id"));
        $exist = $movesRepository->findBy(["X" => $x, "Y" => $y, "Game_id" => $gameId]);

        if (count($exist) == 0) {
            $move = new Moves();
            $moves = $movesRepository->findBy(["Game_id" => $gameId]);
            $last = end($moves);
            $colorLast = str_split($last->getColor());
            $color = "B";
            if ($colorLast [0] == "B") {
                $color = "W";
            }
            $num = 0;
            foreach ($colorLast as $n) {
                if ($n != 0) {
                    $num = $num * 10 + intval($n);
                }
            }
            $move->setColor($color . $num++);
            $move->setX($x);
            $move->setY($y);
            $move->setGameId($gameId);
            $entityManager->persist($move);
            $entityManager->flush();
            return new JsonResponse(['moves' => $serializer->serialize($move, JsonEncoder::FORMAT), "color" => $color], Response::HTTP_CREATED);
        }

        return new JsonResponse(['moves' => $serializer->serialize($exist, JsonEncoder::FORMAT)], Response::HTTP_CREATED);
    }

    #[Route('/api/moves/previous', name: 'app_moves_previous', methods: ['GET', 'POST'])]
    public function previous(Request $request, MovesRepository $movesRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $str = "abcdefghijklmnopqrs";
        $gameId = intval($request->get("game_id"));
        $current = intval($request->get("current"));
        $moves = $movesRepository->findBefore(["Game_id" => $gameId, "id" => $current]);
        $current = 361 - (18 - strpos($str, $moves[0]->getX()) + 19 * (strpos($str, $moves[0]->getY())));
        $previous = 361 - (18 - strpos($str, $moves[1]->getX()) + 19 * (strpos($str, $moves[1]->getY())));

        return new JsonResponse(['current' => $current, 'previous' => $previous, "id" => $moves[1]->getId()], Response::HTTP_CREATED);
    }

    #[Route('/api/moves/next', name: 'app_moves_next', methods: ['GET', 'POST'])]
    public function next(Request $request, MovesRepository $movesRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $str = "abcdefghijklmnopqrs";
        $gameId = intval($request->get("game_id"));
        $current = intval($request->get("current"));
        $moves = $movesRepository->findAfter(["Game_id" => $gameId, "id" => $current]);
        if (count($moves) == 2) {
            $next = 361 - (18 - strpos($str, $moves[1]->getX()) + 19 * (strpos($str, $moves[1]->getY())));
            $current = 361 - (18 - strpos($str, $moves[0]->getX()) + 19 * (strpos($str, $moves[0]->getY())));
            $last = $moves[0];
            $colorLast = str_split($last->getColor());
            $color = "B";
            if ($colorLast [0] == "B") {
                $color = "W";
            }
            return new JsonResponse(['next' => $next, 'current' => $current, "color" => $color, "id" => $moves[1]->getId()], Response::HTTP_CREATED);
        } else {
            return new JsonResponse(['error' => 1], Response::HTTP_CREATED);
        }
    }

    #[Route('/moves/{id}', name: 'app_moves_show', methods: ['GET'])]
    public function show(Moves $move): Response
    {
        return $this->render('moves/show.html.twig', [
            'move' => $move,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_moves_edit', methods: ['POST'])]
//    public function edit(Request $request, Moves $move, MovesRepository $movesRepository): Response
//    {
//        $form = $this->createForm(MovesType::class, $move);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $movesRepository->save($move, true);
//
//            return $this->redirectToRoute('app_moves_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('moves/edit.html.twig', [
//            'move' => $move,
//            'form' => $form,
//        ]);
//    }

//    #[Route('/{id}', name: 'app_moves_delete', methods: ['POST'])]
//    public function delete(Request $request, Moves $move, MovesRepository $movesRepository): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $move->getId(), $request->request->get('_token'))) {
//            $movesRepository->remove($move, true);
//        }
//
//        return $this->redirectToRoute('app_moves_index', [], Response::HTTP_SEE_OTHER);
//    }
}
