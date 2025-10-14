<?php
namespace Adityawangsaa\LoginPhpManagementV1\Controller;

use Adityawangsaa\LoginPhpManagementV1\App\View;
use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
use Adityawangsaa\LoginPhpManagementV1\Service\SessionService;

class HomeController {
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function index()
    {
        $user = $this->sessionService->current();

        if($user == null) {
            View::render('Home/index', [
            'title' => 'Login PHP Management'
            ]);
        } else {
            View::render('Home/dashboard', [
                "title" => "Dashboard",
                "user"  => [
                    "name"  => $user->name
                ]
            ]);
        }
    }
}