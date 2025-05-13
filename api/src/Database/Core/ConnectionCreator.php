<?php

namespace Leonardo8896\Hydrignis\Database\Core;

use PDO;

class ConnectionCreator 
{
    public static function createPDOConnection(): PDO
    {
        $connectionString = "mysql:host={$_ENV['HOST']};port={$_ENV['PORT']};dbname={$_ENV['DB_NAME']}";

        $connection = new PDO(
            $connectionString,
            $_ENV['USER'],
            $_ENV['PASSWORD'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_SSL_CA => ($tempCaPath = self::createTempCaPath())

            ]
        );

        self::unlinkTempCaPath($tempCaPath);

        return $connection;
    }

    private static function createTempCaPath():string
    {
        $certificado = str_replace('\\n', "\n", $_ENV['CA_FILE']);

        $tempPath = sys_get_temp_dir()."isrgrootx1.pem";
        file_put_contents($tempPath, $certificado);

        return $tempPath;
    }

    private static function unlinkTempCaPath($tempCaPath):void
    {
        if (file_exists($tempCaPath)) {
            unlink($tempCaPath);
        }
    }
}