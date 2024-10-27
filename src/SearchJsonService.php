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

    /**
     * Build regex pattern based on similar characters and allow optional characters.
     *
     * @param int $userId The ID of the user to retrieve.
     * @return array An associative array containing user details.
     * @throws InvalidArgumentException If the provided user ID is not valid.
     * @throws UserNotFoundException If no user is found with the provided ID.
     */
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

    /**
     * Searches for a specific key within a JSON string and returns its value.
     *
     * This function takes a JSON string and a key as input, decodes the JSON string into an associative array,
     * and searches for the specified key within the array. If the key is found, the corresponding value is returned.
     * If the key is not found, the function returns null.
     *
     * @param string $json The JSON string to search within.
     * @param string $key The key to search for in the JSON string.
     * @return mixed The value associated with the specified key, or null if the key is not found.
     */
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
        //
        // $query->where(function ($query) use ($fields, $regexPattern, $langs) {
        //     foreach ($fields as $field) {
        //         $query->orWhere(function ($query) use ($field, $regexPattern, $langs) {
        //             foreach ($langs as $lang) {
        //                 // Check if the field contains the language key to avoid errors
        //                 $jsonPath = "$.$lang";
        //                 $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT($field, $jsonPath)) IS NOT NULL
        //                 AND JSON_UNQUOTE(JSON_EXTRACT($field, $jsonPath)) REGEXP ?", [$regexPattern]);
        //             }
        //         });
        //     }
        // });

        // Execute and return the query results
        return $query;
    }
}
