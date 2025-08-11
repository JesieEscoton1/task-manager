<?php

namespace App\Services\Chatbot;

use App\Models\FaqArticle;
use Illuminate\Support\Str;

class KnowledgeBaseService
{
    public function search(string $query, ?string $locale = 'en', ?string $location = null): ?FaqArticle
    {
        $builder = FaqArticle::query();
        if ($locale) {
            $builder->where('locale', $locale);
        }
        $articles = $builder->get();

        $best = null;
        $bestScore = 0;
        foreach ($articles as $article) {
            $score = 0;
            $haystack = Str::lower($article->title . ' ' . $article->content);
            $needle = Str::lower($query);
            if (Str::contains($haystack, $needle)) {
                $score += 10;
            }
            if ($location && Str::contains($haystack, Str::lower($location))) {
                $score += 3;
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $article;
            }
        }
        return $best;
    }
}


