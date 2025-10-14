<?php
namespace Adityawangsaa\LoginPhpManagementV1\App {
    function header(string $value)
    {
        echo $value;
    }
}

namespace Adityawangsaa\LoginPhpManagementV1\Middleware {

    use Adityawangsaa\LoginPhpManagementV1\Config\Database;
    use Adityawangsaa\LoginPhpManagementV1\Domain\Session;
    use PHPUnit\Framework\TestCase;
    use Adityawangsaa\LoginPhpManagementV1\Domain\User;
    use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
    use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
    use Adityawangsaa\LoginPhpManagementV1\Service\SessionService;

    class MustNotLoginMiddlewareTest extends TestCase {

    private MustNotLoginMiddleware $middleware;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;


    protected function setUp(): void
    {
        $this->middleware = new MustNotLoginMiddleware();
        putenv("mode=test");

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testBeforeGuest()
    {
        $this->middleware->before();
        $this->expectOutputString("");
    }

    public function testBeforeLoginUser()
    {
        $user = new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = "rahasia";
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->middleware->before();
        $this->expectOutputRegex("[Location: /]");
    }
    }
}