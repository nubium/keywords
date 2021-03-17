<?php
declare(strict_types=1);

namespace Nubium\Keywords;

use RuntimeException;
use Transliterator;

class KeywordsHelper
{
	/** @var array<string,string> */
	protected array $cache = [];


	public function createKeywords(string $text, bool $mergedKeywords): string
	{
		$cacheKey = $text . '|' . (int)$mergedKeywords;
		if (!array_key_exists($cacheKey, $this->cache)) {
			// transliterujeme vsechno krome azbuky na latinku a odstranime diakritiku z latinky
			$text = $this->transliterate('[:^Cyrillic:]Any-Latin;Latin-ASCII', $text);

			// odstranime modifier letters (neco jako diakritika)
			$text = trim((string)preg_replace('/[\p{Lm}]+/u', '', $text));

			// vsechno co neni pismeno nebo cilo nahradime za mezeru a trimneme
			$text = trim((string)preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text));

			// zapamatujeme si query pred pridavanim mezer
			$originalText = $text;

			// pridame mezery kde si myslime, ze chybi
			$text = (string)preg_replace('/(\p{Ll})(\p{Lu})/u', '$1 $2', $text); // MyHomeVideo -> My Home Video
			$text = (string)preg_replace('/(\p{N})(\p{L})/u', '$1 $2', $text); // S01E01 -> S01 E01

			if ($mergedKeywords) {
				// pridame originalni slova rozbita pri pridavani mezer
				$baseKeywords = array_unique(explode(' ', $text));
				$extraKeywords = array_unique(array_diff(explode(' ', $originalText), $baseKeywords));
				if (count($extraKeywords) > 0) {
					$text .= ' ' . implode(' ', $extraKeywords);
				}
			}

			// prevedeme vsechno co zbylo (vcetbe azbuky) na lower-case
			$this->cache[$cacheKey] = $this->transliterate('Any-Lower()', $text);
		}
		return $this->cache[$cacheKey];
	}


	private function transliterate(string $rules, string $text): string
	{
		$transliterator = Transliterator::create($rules);
		if (!$transliterator) {
			throw new RuntimeException(Transliterator::class . "::createFromRules('$rules') failed.");
		}

		$transliteratedText = $transliterator->transliterate($text);
		if ($transliteratedText === false) {
			throw new RuntimeException("Transliteration of '$text' failed: " . $transliterator->getErrorMessage());
		}

		return $transliteratedText;
	}
}
