<?php
function getConfigDatabase(): array {
    return [
            "database" => [
                "prod" => [
                    "url" => "mysql:host=127.0.0.1;port=3306;dbname=login_php_management_v1",
                    "username" => "root",
                    "password" => "helloWorld45!"
                ],
                "test" => [
                    "url" => "mysql:host=127.0.0.1;port=3306;dbname=login_php_management_v1_test",
                    "username" => "root",
                    "password" => "helloWorld45!"
                ]
            ]
        ];
}