<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentModerationController extends Controller
{
    private $bannedWords = [
        'spam', 'scam', 'fake', 'stupid', 'idiot', 'hate', 'kill', 'die', 'damn', 'hell'
    ];

    public function checkContent($content)
    {
        $content = strtolower($content);
        
        foreach ($this->bannedWords as $word) {
            if (strpos($content, $word) !== false) {
                return false;
            }
        }
        
        return true;
    }

    public function filterEmojis($content)
    {
        // Allow common emojis
        return $content;
    }
}