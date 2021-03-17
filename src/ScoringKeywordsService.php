<?php
declare(strict_types=1);

namespace Nubium\Keywords;


use Nubium\SentenceScoring\Edge\Bridge\IPrepareKeywords;

class ScoringKeywordsService implements IPrepareKeywords
{
	private KeywordsHelper $keywordsHelper;


	public function __construct(KeywordsHelper $keywordsHelper)
	{
		$this->keywordsHelper = $keywordsHelper;
	}


	public function stripToKeywords(string $sentence): string
	{
		return $this->keywordsHelper->createKeywords($sentence, false);
	}
}
