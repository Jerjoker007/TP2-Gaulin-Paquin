<?php

namespace App\Repository\Eloquent;

use App\Models\Review;
use App\Repository\ReviewRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Review::class);
    }
}