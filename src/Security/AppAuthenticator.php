<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class AppAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    const HEADER_TOKEN_NAME = "x-api-token";

    public function supports(Request $request): bool
    {
        return $request->headers->has(self::HEADER_TOKEN_NAME);
    }

    public function getCredentials(Request $request)
    {
        return $apiToken = $request->headers->get(self::HEADER_TOKEN_NAME);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        return $this->userRepository->findByApiToken($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        /**
         * Once getUser function get a user, so the user token is valid
         */
        return false;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = [
            'message' => "Wrong token"
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $data = [
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
     return false;
    }
}
