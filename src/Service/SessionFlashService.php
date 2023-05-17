<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionFlashService implements FlashServiceInterface
{
    public const SUCCESS_TYPE = 'success';
    public const ERROR_TYPE = 'error';

    private readonly SessionInterface $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function addFlashSuccess(string $message): void
    {
        $this->addFlash(self::SUCCESS_TYPE, $message);
    }

    public function addFlashError(string $message): void
    {
        $this->addFlash(self::ERROR_TYPE, $message);
    }

    private function addFlash(string $type, $message)
    {
        $this->session->getFlashBag()->add($type, $message);
    }
}