<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\AuthRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;

class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
}