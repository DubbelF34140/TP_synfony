<?php
// src/Service/Censurator.php
namespace App\Service;

class Censurator
{
    private $forbiddenWords = [
        'merde',
        'chié',
        'chier'
    ];

    public function purify(string $text): string
    {
        foreach ($this->forbiddenWords as $word) {
            $replacement = str_repeat('*', strlen($word));
            $text = str_ireplace($word, $replacement, $text);
        }

        return $text;
    }
}
