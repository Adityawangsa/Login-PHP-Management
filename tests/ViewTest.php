<?php
namespace Adityawangsaa\LoginPhpManagementV1\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase {
    public function testView()
    {
        View::render('Home/index', [
            "PHP Login Management"
        ]);

        $this->expectOutputRegex('[PHP Login Management]');
        $this->expectOutputRegex('[html]');
    }
}