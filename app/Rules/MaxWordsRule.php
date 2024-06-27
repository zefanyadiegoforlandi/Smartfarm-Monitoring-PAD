<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MaxWordsRule implements Rule
{
    protected $maxWords;

    public function __construct($maxWords)
    {
        $this->maxWords = $maxWords;
    }

    public function passes($attribute, $value)
    {
        return str_word_count($value) <= $this->maxWords;
    }

    public function message()
    {
        return ':attribute tidak boleh lebih dari ' . $this->maxWords . ' words.';
    }
}
