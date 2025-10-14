<?php
namespace Adityawangsaa\LoginPhpManagementV1\Middleware;

use Adityawangsaa\LoginPhpManagementV1\App\View;
use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
use Adityawangsaa\LoginPhpManagementV1\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before(): void
    {
        $user = $this->sessionService->current();
        if($user != null){
            View::redirect('/');
        }
    }
}