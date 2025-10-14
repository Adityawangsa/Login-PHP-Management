<?php
namespace Adityawangsaa\LoginPhpManagementV1\App {
    function header(string $value)
    {
        echo $value;
    }
}

namespace Adityawangsaa\LoginPhpManagementV1\Service {
    function setcookie(string $name, string $value)
    {
        echo "$name: $value";
    }
}

namespace Adityawangsaa\LoginPhpManagementV1\Controller {
    use Adityawangsaa\LoginPhpManagementV1\Config\Database;
    use Adityawangsaa\LoginPhpManagementV1\Domain\Session;
    use PHPUnit\Framework\TestCase;
    use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
    use Adityawangsaa\LoginPhpManagementV1\Domain\User;
    use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
    use Adityawangsaa\LoginPhpManagementV1\Service\SessionService;

    class UserControllerTest extends TestCase {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

            // Menghubungkan dan menghapus data di tabel session
            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register New User]");
        }

        public function testPostRegisterSuccess()
        {
            $_POST['id'] = "wangsaa";
            $_POST['name'] = "Wangsaa";
            $_POST['password'] = "rahasia";     

            $this->userController->postRegister(); 

            $this->expectOutputRegex('[Location: /users/login]');
        }

        public function testPostRegisterValidationError()
        {
            $_POST['id'] = "";
            $_POST['name'] = "";
            $_POST['password'] = "";

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register New User]");
            $this->expectOutputRegex("[Id, Name, Password can not blank!]");
        }

        public function testPostRegisterDuplicate()
        {
            // Menyimpan data ke database
            $user = new User();
            $user->id = "wangsaa";
            $user->name = "Wangsaa";
            $user->password = "rahasia";

            $this->userRepository->save($user);

            // Menyimpan data ke database versi 2
            $_POST['id'] = "wangsaa";
            $_POST['name'] = "Wangsaa";
            $_POST['password'] = "rahasia";

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register New User]");
            $this->expectOutputRegex("[User Id is already exists]");    
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = "wangsaa";
            $user->name = "Wangsaa";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-AW-SESSION: ]");
        }

        
    }
}