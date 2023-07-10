<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        UserAuthenticatorInterface $userAuthenticator, 
        UserAuthenticator $authenticator, 
        EntityManagerInterface $entityManager, 
        SendMailService $mail,
        JWTService $jwt
        ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            

            //génération du jwt de l'utilisateur en injectant dans le JwtService comme paralmètre de la fonction


            //création du header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            //création du payload
            $payload = [
                'user_id' => $user->getId()
            ];

            //génération du token
            $token = $jwt->generate($header, $payload, $this->getParameter(('app.jwtsecret')));


            // dd($token);

            //envoi du mail
            $mail->send(
                'noreply@monsite.com',
                $user->getEmail(),
                'Activation de votre compte sur le site NTS6',
                'register',
                compact('user', 'token')
                // [
                //     'user'=>$user
                // ]
                );


            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser(
        $token, 
        JWTService $jwt, 
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): Response
    {
        // dd($jwt->check($token, $this->getParameter('app.jwtsecret')));
        //on vérifie si le token est valide, n'a pas expiré et n'a pas  été modifié
        if($jwt->isValid($token) &&  !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
                //on récupère le payload
                $payload = $jwt->getPayload($token);

                //on récupère le user du token
                $user = $userRepository->find($payload['user_id']);

                //on vérifie que l'utilisateur existe et n'a pas encore activé son compte
                if($user && !$user->getIsVerified())
                {
                    $user->setIsVerified(true);
                    $em->flush($user);
                    $this->addFlash('success', 'utilisateur activé');
                    return $this->RedirectToRoute('profile_index');
                }
                return $this->render('registration/verify.html.twig');

        }
        //else
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->RedirectToRoute('app_login');
    }

    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(
        JWTService $jwt,
        SendMailService $mail,
        UserRepository $user): Response
    {
        $user = $this->getUser();

        if(!$user)
        {
            $this->addFlash('danger', 'Vous devez être connectés pour accdéer à cette page');
            return $this->RedirectToRoute('app_login');
        }

        if($user->getIsVerified())
        {
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->RedirectToRoute('profile_index');
        }
        
        //génération du jwt de l'utilisateur en injectant dans le JwtService comme paralmètre de la fonction


            //création du header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            //création du payload
            $payload = [
                'user_id' => $user->getId()
            ];

            //génération du token
            $token = $jwt->generate($header, $payload, $this->getParameter(('app.jwtsecret')));


            // dd($token);

            //envoi du mail
            $mail->send(
                'noreply@monsite.com',
                $user->getEmail(),
                'Activation de votre compte sur le site NTS6',
                'register',
                compact('user', 'token')
                // [
                //     'user'=>$user
                // ]
                );

            $this->addFlash('success', 'Email de vérification envoyé');
            return $this->RedirectToRoute('profile_index');

    }



}
