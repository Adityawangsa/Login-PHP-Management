<?php
namespace Adityawangsaa\LoginPhpManagementV1\Service;

use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use Adityawangsaa\LoginPhpManagementV1\Domain\Session;
use Adityawangsaa\LoginPhpManagementV1\Domain\User;
use PHPUnit\Framework\TestCase;

function setcookie(string $name, string $value)
{
    echo "$name: $value";
}

class SessionServiceTest extends TestCase {
    private SessionRepository $sessionRepository;
    private SessionService $sessionService;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create("wangsaa");
        $result = $this->sessionRepository->findById($session->id);

        $this->expectOutputRegex("[X-AW-SESSION: $session->id]");
        $this::assertEquals("wangsaa", $result->userId);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "wangsaa";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();
        $this->expectOutputRegex("[X-AW-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "wangsaa";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();
        self::assertEquals($session->userId, $user->id);
    }
}