<?php

namespace App\Service;

interface FlashServiceInterface
{
    public function addFlashSuccess(string $message): void;

    public function addFlashError(string $message): void;
}