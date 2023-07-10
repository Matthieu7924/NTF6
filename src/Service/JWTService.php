<?php
namespace App\Service;

use DateTime;
use DateTimeImmutable;

class JWTService
{
    //génération du token
    
    public function generate(array $header, array $payload, string $secret, int $validity =10800):string
    {
        if($validity > 0)
        {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            //iat=issued at
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        //encodage en base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        //nettoyage des valeurs encodées (retrait des + / et =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''] , $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''] , $base64Payload);

        //génération de la signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . ' ' . $base64Payload, $secret, true);
        
        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''] , $base64Signature);

        //création du token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;
        return $jwt;
    }

    //vérifier que le token est bien formaté
    public function isValid($token):bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    //VERIFIER SI LE ROKEN A EXPIRE
    //récupéartion du payload
    public function getPayload(string $token): array
    {
        //on démonte le token
        $array = explode('.', $token);

        //on décode le payload
        $payload = json_decode(base64_decode($array[1]), true);


        return $payload;
    }

    //récupéartion du pheader
    public function getHeader(string $token): array
    {
        //on démonte le token
        $array = explode('.', $token);

        //on décode le payload
        $header = json_decode(base64_decode($array[0]), true);


        return $header;
    }

    //on vérifie si le token a expiré
    public function isExpired(string $token): bool
    {
            $payload = $this->getPayload($token);

            $now = new DateTimeImmutable();
            
            return $payload['exp'] < $now->getTimestamp();
    }

    //on vérifie la skignature du token
    public function check(string $token, string $secret)
    {
        //on récupère le header et le payload
        $header = $this->getHeader($token); 
        $payload = $this->getPayload($token);

        //on régénère un token
        $verifToken = $this->generate($header, $payload, $secret, 0); //0 correspond à la valeur de $validity

        return $token === $verifToken;
    }

    
}