<?php

namespace App\Helpers;

class ProfanityDetector
{

    private array $customBadWords;
	private string $dictionaryPath;
    private static array $leetspeakMap = [
		'0' => 'o',
		'1' => 'i',
		'2' => 'z',
		'3' => 'e',
		'4' => 'a',
		'5' => 's',
		'6' => 'g',
		'7' => 't',
		'8' => 'b',
		'9' => 'g',
    ];

    public function __construct(array $customBadWords = [])
    {
        $this->customBadWords = $customBadWords;
		$this->dictionaryPath = base_path('vendor/consoletvs/profanity/Dictionaries/Default.json');
    }

	private function getBadWordList(): array
    {
        $defaultDictionary = [];
		$dictionaryPath = base_path('vendor/consoletvs/profanity/Dictionaries/Default.json');
		$defaultDictionary = json_decode(file_get_contents($dictionaryPath), true) ?? [];

        foreach ($this->customBadWords as $word) {
            $defaultDictionary[] = [
                'language' => 'sg',
                'word' => strtolower($word)
            ];
        }

        return $defaultDictionary;
    }

	public function check($nickname)
	{
	    $normalized = strtolower($nickname);

	    $normalized = strtr($normalized, self::$leetspeakMap);

	    $normalized = preg_replace('/([a-z])\\1+/', '$1', $normalized);

		foreach ($this->getBadWordList() as $badWord) {
		    $word = strtolower($badWord['word']);

		    // Ignore short words like "as", "it", "is"
		    if (strlen($word) <= 3) continue;

		    $badNormalized = preg_replace('/([a-z])\\1+/', '$1', $word);
		    if (str_contains($normalized, $badNormalized)) {
		        return true;
		    }
		}

	    return false;
	}

}
