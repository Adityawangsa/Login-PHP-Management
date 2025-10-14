<?php
namespace Adityawangsaa\LoginPhpManagementV1\Controller;

use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use Adityawangsaa\LoginPhpManagementV1\App\View;
use Adityawangsaa\LoginPhpManagementV1\Domain\Session;
use Adityawangsaa\LoginPhpManagementV1\Exception\ValidationException;
use Adityawangsaa\LoginPhpManagementV1\Model\UserLoginRequest;
use Adityawangsaa\LoginPhpManagementV1\Model\UserProfileUpdateRequest;
use Adityawangsaa\LoginPhpManagementV1\Model\UserRegisterRequest;
use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
use Adityawangsaa\LoginPhpManagementV1\Service\SessionService;
use Adityawangsaa\LoginPhpManagementV1\Service\UserService;
use Exception;
use PhpParser\Node\Expr\FuncCall;

class UserController {
    private UserService $userService;
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;

    public function __construct()
    {
        // Koneksi database
        $connection = Database::getConnection();

        // Pembuatan objek untuk 3 elemen utama
        $userRepository = new UserRepository($connection);
        $sessionRepository = new SessionRepository($connection);

        $this->userService = new UserService($userRepository);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register()
    {
        View::render('User/register', [
            'title' => 'Register New User'
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        } catch (Exception $exception) {
            View::render('User/register', [
                'title' => 'Register New User',
                'error' => $exception->getMessage()
            ]);
        }
    }

    // Halaman Login
    public function login()
    {
        View::render("User/login", [
            'title' => 'Login User'
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->login($request);

            // Membuat cookie untuk users
            $this->sessionService->create($response->user->id);
            View::redirect("/");
        } catch (Exception $exception) {
            View::render('User/login', [
                'title' => 'Login User',   
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect("/");
    }

    public function updateProfile()
    {
        $user = $this->sessionService->current();

        View::render('User/profile', [
            "title" => "Update user profile",
            "user" => [
                "id" => $user->id,
                "name" => $user->name
            ]
        ]);
    }

    public function postUpdateProfile()
    {
        $user = $this->sessionService->current();

        $request = new UserProfileUpdateRequest();
        $request->id = $user->id;
        $request->name = $_POST['name'];

        try {
            $this->userService->updateProfile($request);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/profile', [
                "title" => "Update user profile",
                "error" => $exception->getMessage(),
                "user" => [
                    "id" => $user->id,
                    "name" => $_POST['name']
                ]
            ]);
        }
    }
}