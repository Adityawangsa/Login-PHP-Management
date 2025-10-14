<?php
namespace Adityawangsaa\LoginPhpManagementV1\Service;

use Adityawangsaa\LoginPhpManagementV1\Exception\ValidationException;
use Adityawangsaa\LoginPhpManagementV1\Model\UserRegisterRequest;
use Adityawangsaa\LoginPhpManagementV1\Model\UserRegisterResponse;
use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use Adityawangsaa\LoginPhpManagementV1\Domain\User;
use Adityawangsaa\LoginPhpManagementV1\Model\UserLoginRequest;
use Adityawangsaa\LoginPhpManagementV1\Model\UserLoginResponse;
use Adityawangsaa\LoginPhpManagementV1\Model\UserPasswordUpdateRequest;
use Adityawangsaa\LoginPhpManagementV1\Model\UserPasswordUpdateResponse;
use Adityawangsaa\LoginPhpManagementV1\Model\UserProfileUpdateRequest;
use Adityawangsaa\LoginPhpManagementV1\Model\UserProfileUpdateResponse;
use Exception;
use PhpParser\Node\Expr\FuncCall;

class UserService {
    private UserRepository $userRepository;

    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validationUserRegistrationRequest($request);

        try {
            // Database Transaction
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);

            if ($user != null) {
                throw new ValidationException("User Id is already exists");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            // Simpan data ke database
            $this->userRepository->save($user);

            // Membuat nilai balik
            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    // Mengecek apakah nilai parameternya sudah valid
    private function validationUserRegistrationRequest($request)
    {
        if ($request->id == null || $request->name == null || $request->password == null ||
        trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == ""){
            throw new ValidationException("Id, Name, Password can not blank!");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validationUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("id or password is wrong");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException("Id or password is wrong");
        }

    }

    public function validationUserLoginRequest($request)
    {
        if ($request->id == null || $request->password == null ||
        trim($request->id) == "" || trim($request->password) == "") {
            throw new ValidationException("id, password can not blank!");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validationUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if($user == null){
                throw new ValidationException("User is not found");
            }

            $user->name = $request->name;
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function validationUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if ($request->id == null || $request->name == null ||
        trim($request->id) == "" || trim($request->name) == "") {
            throw new ValidationException("id, Name can not blank!");
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
    {
        $this->validationUserPasswordUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null){
                throw new ValidationException("User is not found");
            }

            if(!password_verify($request->oldPassword, $user->password)){
                throw new ValidationException("Old password is wrong");
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function validationUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if($request->id == null || $request->oldPassword == null || $request->newPassword == null ||
            trim($request->id) == "" || trim($request->oldPassword) == "" || trim($request->newPassword) == "")
            {
                throw new ValidationException("Id, Old Password, New Password can not blank!");
            }
    }
}