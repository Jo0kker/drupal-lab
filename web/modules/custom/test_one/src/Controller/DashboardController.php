<?php

namespace Drupal\test_one\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\test_one\Repository\SessionsRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DashboardController extends ControllerBase
{
    /**
     * @var SessionsRepository
     */
    private $repository;

    public function __construct(SessionsRepository $repository)
    {

        $this->repository = $repository;
    }

    public static function create(ContainerInterface $container): DashboardController
    {
        return new static(
            $container->get('test_one.repository')
        );
    }

    public function dashboard(): array
    {
        // Do something with your variables here.
        $myText = 'Ceci est mon text qui vien du controller';
        $myNumber = 1;
        $myArray = [1, 2, 3];

        return [
            // Your theme hook name.
            '#theme' => 'test_one',
            // Your variables.
            '#variable1' => $myText,
            '#variable2' => $myNumber,
            '#variable3' => $myArray,
        ];
    }

    public function demo(): array
    {
        $sessions = $this->repository->findActiveSessions();
        $log = $this->repository->findLogStat();
        return [
            // Your theme hook name.
            '#theme' => 'demo',
            // Your sessionsiables.
            '#sessions' => $sessions,
            '#logStats' => $log
        ];

    }
}
