<?php

namespace App\DataFixtures;
 
use App\Entity\GroupTrick;
use App\Entity\Trick;
use App\Entity\Comment;

return [
    GroupTrick::class => [
        'group{1..5}' => [
            'name' => '<words(2,true)>',
        ],
    ],
    Trick::class => [
        'trick{1..30}' => [
            'title' => $title = '<text(50)>',
            'slug' => $slug = str_replace(" ", "_", $title),
            'description' => '<text(1000)>',
            'createdAt' => $createdAt = '<dateTimeBetween("-200 days", "now")>',
            'modifiedAt' => '<dateTimeBetween($createdAt, "now")>',
            'groupTrick' => '@group*',
        ],
    ],
    Comment::class => [
        'comment{1..200}' => [
            'content' => '<text(500)>',
            'createdAt' => '<dateTimeBetween("now", "+20days")>',
            'trick' => '@trick*',
        ],
    ],
];

