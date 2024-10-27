<?php

namespace Nourayman\SearchJson;

use Illuminate\Database\Eloquent\Builder;

class SearchJsonService
{
    // Define character groups for normalization and searching
    protected $similarChars = [
        'ا' => '[إأآا]', // Match any form of 'ا'
        'أ' => '[إأآا]', // Match any form of 'ا' for 'أ'
        'إ' => '[إأآا]', // Match any form of 'ا' for 'إ'
        'آ' => '[إأآا]', // Match any form of 'ا' for 'آ'
        'ه' => '[هة]',   // Match 'ه' or 'ة'
        'ة' => '[هة]',   // Match 'ة' or 'ه'
        'ي' => '[ييىئ]', // Match 'ي', 'ى', 'ئ'
        'ى' => '[ييىئ]', // Match 'ى', 'ي', 'ئ'
        'و' => '[ووؤ]',  // Match 'و', 'ؤ'
        'ؤ' => '[ووؤ]',  // Match 'ؤ', 'و'
    ];

    // Build regex pattern based on similar characters and allow optional characters
    public function buildRegexPattern($text)
    {
        // Split the input text into words
        $words = explode(' ', $text);

        // Initialize an empty array to store the regex patterns
        $patterns = [];

        // Loop through each word and build the regex pattern
        foreach ($words as $word) {
            // Loop through the characters in the word
            $pattern = '';
            $chars = mb_str_split($word);
            foreach ($chars as $char) {
                // Use similar character regex group if defined, else use the character as-is
                $pattern .= $this->similarChars[$char] ?? preg_quote($char, '/');
            }

            // Add the pattern to the array
            $patterns[] = $pattern;
        }
        // Return the array of regex patterns
        return $patterns;
    }

    // Search method to apply regex term to JSON fields
    public function searchJson(Builder $query, $field, $term,  $langs = ['ar', 'en'])
    {
        // Build the regex pattern for the search term
        $regexPatterns = $this->buildRegexPattern($term);

        // Apply the regex to each field and language combination
        $query->where(function ($query) use ($field, $langs, $regexPatterns) {
            foreach ($regexPatterns as $pattern) {
                $query->where(function ($query) use ($field, $langs, $pattern) {
                    foreach ($langs as $lang) {
                        if ($lang == 'en')
                            $query->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT($field, '$.$lang')) )REGEXP ?", [$pattern]);
                        else
                            $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT($field, '$.$lang')) REGEXP ?", [$pattern]);
                    }
                });
            }
        });
      
        // Execute and return the query results
        return $query;
    }
}
