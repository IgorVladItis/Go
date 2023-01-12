<?php

namespace App\Controller;

use App\Repository\MovesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\GameUploadType;
use App\Entity\Game;
use App\Entity\User;
use App\Entity\Moves;
use App\Repository\GameRepository;
use Psr\Log\LoggerInterface;

class GameController extends AbstractController
{
    #[Route('/game', name: 'app_game')]
    public function index(GameRepository $gameRepository): Response
    {

        $user = $this->getUser();
        if($user == null){
            return $this->redirectToRoute('main_login');
        }
        $allGame= $gameRepository->findBy(["user_id" => $user->getId()]);
        $game = new Game();
        $form = $this->createForm(GameUploadType::class, $game, [
            'action' => $this->generateUrl('app_game_upload'),
            'method' => 'POST',
        ]);

        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
            'form' => $form->createView(),
            'all_game' => $allGame,
        ]);
    }

    #[Route('/game/{id}/show', name: 'app_game_show')]
    public function show(int $id, GameRepository $game, MovesRepository $moves): Response
    {
        $game = $game->find($id);
        $moves = $moves->findBy(["Game_id" => $id]);
        $str = "abcdefghijklmnopqrs";
        $co = [];
        for ($X = 1; $X < 20; $X++) {
            for ($Y = 1; $Y < 20; $Y++) {
                $co [$X][$Y] = "t";
            }
        }
        foreach ($moves as $move) {
            $X = strpos($str, $move->getX()) + 1;
            $Y = strpos($str, $move->getY()) + 1;
            if ($move->getColor()[0] == "B") {
                $color = "b";
            } else {
                $color = "w";
            }
            $co [$X][$Y] = $color;
        }

        return $this->render('game/show.html.twig', [
            'controller_name' => 'GameController',
            'game' => $game,
            'co' => $co,
            'moves' => $moves,
        ]);
    }

    #[Route('/game/upload', name: 'app_game_upload')]
    public function appLoader(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $path = $request->files->get('game_upload') ['fileName']->getPathName();
        $json = file_get_contents($path);
        $rawArray = explode("(;", str_replace("]", "", $json));
        $infoArray = explode(";", $rawArray[1]);
        $firstMove = $infoArray[1];
        $firstMove = explode("[", $firstMove);
        $infoArray = explode("\n", $infoArray[0]);
        $moveArray = [];
        $moveArray [$firstMove[0] . "1"] = str_replace("\n", "", $firstMove[1]);
        $counter = 1;

        $emptyArray = [];
        foreach ($infoArray as $item) {
            $rawItem = explode("[", $item);
            if (count($rawItem) == 2) {
                $emptyArray[$rawItem[0]] = $rawItem[1];
            }
        }
        $game = new Game();

        $user = $this->getUser();

        $game->setGameDate(\DateTime::createFromFormat('Y-m-d', date("Y-m-d", strtotime($emptyArray["DT"]))));
        $game->setSize($emptyArray["SZ"]);
        $game->setPW($emptyArray["PW"]);
        $game->setPB($emptyArray["PB"]);
        $game->setUserId($user->getId());
        $entityManager->persist($game);
        $entityManager->flush();
        $strInfo = $game->getUserId()."Новая игра id:".$game->getId();
        $logger->info('Game upload'.$strInfo);
        $gameId = $game->getId();
        foreach ($rawArray as $item) {
            $rawItem = explode("[", $item);
            if (count($rawItem) == 2) {
                $counter++;
                $moveArray[$rawItem[0] . $counter] = str_replace("\n", "", $rawItem[1]);
            }
        }
        foreach ($moveArray as $key => $item) {


            $co = str_split($item);
            if (count($co) == 2) {
                $move = new Moves();
                $move->setColor($key);
                $move->setX($co[0]);
                $move->setY($co[1]);
                $move->setGameId($gameId);
                $entityManager->persist($move);
                $entityManager->flush();
                $this->logEvent()->info('Игра загруженна');
            }
        }


        return $this->redirectToRoute('app_game_show', ["id" => $gameId]);
    }

    public function logEvent(): Logger
    {
        $logger = new Logger("gameAdd");
        $logger->pushHandler(new StreamHandler(__DIR__.'Logs'.'/log_file.log', Level::Info));
        return $logger;
    }

}
