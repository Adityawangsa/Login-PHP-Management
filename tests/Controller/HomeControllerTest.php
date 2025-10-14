<?php
namespace Adityawangsaa\LoginPhpManagementV1\Controller;

use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use Adityawangsaa\LoginPhpManagementV1\Domain\Session;
use Adityawangsaa\LoginPhpManagementV1\Domain\User;
use Adityawangsaa\LoginPhpManagementV1\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase {
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();
        
        $this->expectOutputRegex("[Login Management]");
    }

    public function testUserLogin()
    {
        // Membuat dan menyimpan data user
        $user =  new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = "rahasia";
        $this->userRepository->save($user);

        // Membuat dan menyimpan data cookie user
        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);
        
        // Mengatur data cookie user
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();
        $this->expectOutputRegex("[Hello Wangsaa]");
    }
}