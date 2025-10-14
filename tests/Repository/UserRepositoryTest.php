<?php
use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use Adityawangsaa\LoginPhpManagementV1\Domain\User;
use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
use Adityawangsaa\LoginPhpManagementV1\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase {
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    public function setUp(): Void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = "rahasia";

        $this->userRepository->save($user);
        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    public function testFindByIdNotFound()
    {
        $result = $this->userRepository->findById('not found');
        self::assertNull($result);
    }

    public function testUpdate()
    {
        // Membuat data user
        $user = new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = "rahasia";
        
        $this->userRepository->save($user);

        // Merubah data name di user
        $user->name = "Aditya";
        $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }
}

// Hal yang Harus Dilakukan Saat Membuat Service:
// 1. Membuat class dengan nama UserService
// 2. Membuat model sebagai parameter di class UserService
// 2a. Membuat class UserRegisterRequest
// 2aa. Membuat beberapa variabel berupa kolom tabel yang bersifat nullable
// 3. Mengisikan nilai parameter dengan UserRegisterRequest
// 4. Membuat return value dari UserService bernama UserRegisterResponse
// 5. Mengisikan nilai dari Domain/User di UserRegisterResponse sebagai nilai kembalian di UserService
// 6. Memasukkan UserRepository melalui function constructor
// 7. Memasukkan function validateUserRegisterRequest pada function request dengan parameternya UserRegisterRequest