<?php
namespace Adityawangsaa\LoginPhpManagementV1\Repository;

use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use Adityawangsaa\LoginPhpManagementV1\Domain\Session;
use Adityawangsaa\LoginPhpManagementV1\Repository\SessionRepository;
use Adityawangsaa\LoginPhpManagementV1\Domain\User;
use PHPUnit\Framework\TestCase;

class SessionRepositoryTest extends TestCase {
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        // Menyimmpan data user
        $user = new User();
        $user->id = "wangsaa";
        $user->name = "Wangsaa";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }

    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "wangsaa";

        // Menyimpan ke database
        $this->sessionRepository->save($session);

        // Mengambil data dari database
        $result = $this->sessionRepository->findById($session->id);

        // Membandingkan data dari database dan template
        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->userId, $result->userId);
    }

    public function testDeleteById()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "wangsaa";

        // Simpan ke database
        $this->sessionRepository->save($session);

        // Hapus data di sana
        $this->sessionRepository->deleteById($session->id);

        // Cari data berdasarkan ID tertentu
        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testFindByIdNotFound()
    {
        $result = $this->sessionRepository->findById("tidakAda");
        self::assertNull($result);
    }
}