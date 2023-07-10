<?php
namespace App\Service;

use DateTime;
use DateTimeImmutable;

class JWTService
{
    //génération du token
    public function generate(array $header, array $payload, string $secret, int $validity =10800):string
    {
        if($validity <= 0)
        {
            return "";
        }
        $now = new DateTimeImmutable();
        $exp = $now->getTimestamp() + $validity;

        //iat=issued at
        $payload['iat'] = $now->getTimestamp();
        $payload['exp'] = $exp;

        //encodage en base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        //nettoyage des valeurs encodées (retrait des + / et =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''] , $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''] , $base64Payload);

        //génération de la signature
        
        return $jwt;
    }
}