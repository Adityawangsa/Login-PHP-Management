<?php
namespace Adityawangsaa\LoginPhpManagementV1\App;

class View
{

    public static function render(string $view, $model)
    {
        require __DIR__ . '/../View/header.php';
        require __DIR__ . '/../View/' . $view . '.php';
        require __DIR__ . '/../View/footer.php';
    }

    // public static function redirect(string $url)
    // {
    //     header("Location:  $url");

    //     if (getenv("mode") != "test") {
            
    //     exit();
    //     }

    // }

    
    public static function redirect(string $url)
    {
        $header = "Location: $url";

        // Jika sedang mode test, cukup tampilkan string agar PHPUnit bisa membaca
        if (getenv("mode") === "test") {
            echo $header;
            return;
        }

        header($header);
        exit();
    }
}