<?php
declare(strict_types=1);

namespace Nubium\Keywords;

class SearchKeywordsService
{
	private KeywordsHelper $keywordsHelper;


	public function __construct(KeywordsHelper $keywordsHelper)
	{
		$this->keywordsHelper = $keywordsHelper;
	}


	public function createSearchKeywords(string $text): string
	{
		return $this->keywordsHelper->createKeywords($text, true);
	}

	public function queryToKeywords(string $query): string
	{
		return $this->keywordsHelper->createKeywords($query, false);
	}
}
