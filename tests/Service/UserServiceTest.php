<?php
use Adityawangsaa\LoginPhpManagementV1\Exception\ValidationException;
use Adityawangsaa\LoginPhpManagementV1\Model\UserRegisterRequest;
use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
use Adityawangsaa\LoginPhpManagementV1\Service\UserService;
use Adityawangsaa\LoginPhpManagementV1\Domain\User;
use Adityawangsaa\LoginPhpManagementV1\Model\UserLoginRequest;
use Adityawangsaa\LoginPhpManagementV1\Model\UserPasswordUpdateRequest;
use Adityawangsaa\LoginPhpManagementV1\Model\UserProfileUpdateRequest;
use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
use PhpParser\Node\Expr\FuncCall;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase {
    private UserRepository $userRepository;
    private UserService $userService;
    private SessionRepository $sessionRepository;

    public function setUp(): Void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository($connection);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new UserRegisterRequest();
        $user->id = "adityaa";
        $user->name = "Adityaa";
        $user->password = "rahasia";

        // Menyimpan data ke database
        $response = $this->userService->register($user);

        self::assertEquals($user->id, $response->user->id);
        self::assertEquals($user->name, $response->user->name);
        self::assertNotEquals($user->password, $response->user->password);

        self::assertTrue(password_verify($user->password, $response->user->password));
    }

    public function testSaveFailed()
    {
        $this->expectException(ValidationException::class);
        
        $response = new UserRegisterRequest();
        $response->id = "";
        $response->name = "";
        $response->password = "";

        $this->userService->register($response);
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = "ailaa";
        $user->name = "Ailaa";
        $user->password = "rahasia";
        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $response = new UserRegisterRequest();
        $response->id = "ailaa";
        $response->name = "Ailaa";
        $response->password = "rahasia";
        $this->userService->register($response);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "wangsaa";
        $request->password = "Wangsaa";
        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = password_hash("wangsaa", PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "wangsaa";
        $request->password = "wangsaa";

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = password_hash("wangsaa", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserLoginRequest();
        $request->id = "wangsaa";
        $request->password = "wangsaa";

        $response = $this->userService->login($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "wangsaa";
        $request->name = "Aditya";

        $this->userService->updateProfile($request);
        $result = $this->userRepository->findById($user->id);
        
        self::assertEquals($request->name, $result->name);
    }

    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";

        $this->userService->updateProfile($request);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserProfileUpdateRequest();
        $request->id = "wangsaa";
        $request->name = "Aditya";

        $this->userService->updateProfile($request);
    }

    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = "wangsaa";
        $request->oldPassword = "rahasia";
        $request->newPassword = "pritaa";

        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);
        self::assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdatePasswordValidationError()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "wangsaa";
        $request->oldPassword = "";
        $request->newPassword = "";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordWrongOldPassword()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = "pritaa";
        $user->name = "Pritaa";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = "pritaa";
        $request->oldPassword = "admin123";
        $request->newPassword = "new";

        $this->userService->updatePassword($request);
    }

    public function testUpdatePasswordNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserPasswordUpdateRequest();
        $request->id = "wangsaa";
        $request->oldPassword = "rahasia";
        $request->newPassword = "new";

        $this->userService->updatePassword($request);
    }
}