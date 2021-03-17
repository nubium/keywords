<?php
declare(strict_types=1);

namespace Nubium\Keywords\Tests;

use Nubium\Keywords\KeywordsHelper;
use PHPUnit\Framework\TestCase;

class KeywordsHelperTest extends TestCase
{
	/**
	 * @dataProvider dpTestCreateKeywords
	 */
	public function testCreateKeywords(string $text, string $expectedResult, bool $mergedKeyWords): void
	{
		$helper = new KeywordsHelper();
		$actualResult = $helper->createKeywords($text, $mergedKeyWords);

		static::assertSame($expectedResult, $actualResult);
	}

	/**
	 * @return array<array>
	 */
	public function dpTestCreateKeywords(): array
	{
		return [
			// translit
			['foo', 'foo', false],
			['Foo', 'foo', false],                              // convert latin to lowercase
			['Українська Абетка ', 'українська абетка', false], // preserve cyrillic and convert it to lowercase (Ukrainian)
			['Čeština', 'cestina', false],                      // remove basic latin accents (Czech)
			['chữ Quốc ngữ', 'chu quoc ngu', false],            // remove extended latin accents (Vietnamese)
			['Ελληνική γλώσσα', 'ellenike glossa', false],      // transliterate Greek to lower case latin
			['العربيةالعربية', 'alrbytalrbyt', false],          // transliterate suicidal bomb manual to lower case latin

			// separators
			['.Foo./-Bar', 'foo bar', false],                   // replace sequence of separators with single space
			['FooBarTEST', 'foo bar test', false],              // split latin camel-case
			['УкраїнськаАбетка ', 'українська абетка', false],  // split cyrillic camel-case
			['ΕλληνικήΓλώσσα', 'ellenike glossa', false],       // split Greek camel-case
			['S01E32', 's01 e32', false],                       // split words after number in latin
			['С01Е32', 'с01 е32', false],                       // split words after number in cyrillic (Ukrainian)
			['C01Э32', 'c01 э32', false],                       // split words after number in cyrillic (Russian)

			// merge original keywords
			['FooBar01TEST', 'foo bar01 test foobar01test', true], // basic
			['FooBar FooBar', 'foo bar foo bar foobar', true],     // remove duplicities in merged text
			['Foo Bar FooBar', 'foo bar foo bar foobar', true],    // but do not remove duplicities in base text

			// some real life examples
			['MiyaGi &amp; Эндшпиль – Топи до талого Братан (2017) (320  kbps) (YouTube 2 MP3 Converter).mp3', 'miya gi amp эндшпиль топи до талого братан 2017 320 kbps you tube 2 mp3 converter mp3 miyagi youtube', true],
			['[Специалист] Эксперт по программированию на JavaScript ( + jQuery + AJAX + Node.js) (2016).edc', 'специалист эксперт по программированию на java script j query ajax node js 2016 edc javascript jquery', true],
			['Screenshot from 2018-02-15 13-57-18.png', 'screenshot from 2018 02 15 13 57 18 png', true],
			['IMG_20170928_152516.JPG', 'img 20170928 152516 jpg', true],
			['[Bunny_Hat]Macross_06_(66F512AA).mkv', 'bunny hat macross 06 66 f512 aa mkv 66f512aa', true],
			['ColonialCharterTFA.rar ', 'colonial charter tfa rar colonialchartertfa', true],
		];
	}
}
