<?php
/**
 * This file is part of jaapio\xml.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2016 Jaapio<jaap@ijaap.nl>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Jaapio\Xml\XPath;


class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTokens
     */
    public function testScannerRecognizesTokens($type, $value)
    {
        $lexer = new Lexer($value);
        $lexer->moveNext();
        $token = $lexer->lookahead;
        $this->assertEquals($type, $token['type']);
        $this->assertEquals($value, $token['value']);
    }

    public function testScannerRecognizesNodeName()
    {
        $lexer = new Lexer('/bookstore');
        $lexer->moveNext();
        $lexer->moveNext();
        $token = $lexer->lookahead;
        $this->assertEquals(Lexer::T_NODE_NAME, $token['type']);
        $this->assertEquals('bookstore', $token['value']);
    }

    public function testScannerRecognizesAttributeName()
    {
        $lexer = new Lexer('@lang');
        $lexer->moveNext();
        $token = $lexer->lookahead;
        $this->assertEquals(Lexer::T_ATTRIBUTE, $token['type']);
        $this->assertEquals('lang', $token['value']);
    }

    public function testScannerTokenizesSimpleXPathCorrectly()
    {
        $xpath = '//bookstore/book';
        $lexer = new Lexer($xpath);

        $tokens = [
            [
                'value' => '//',
                'type' => Lexer::T_NODE_SEARCH_SEPARATOR,
                'position' => 0,
            ],
            [
                'value' => 'bookstore',
                'type' => Lexer::T_NODE_NAME,
                'position' => 2
            ],
            [
                'value' => '/',
                'type' => Lexer::T_NODESEPARATOR,
                'position' => 12,
            ],
            [
                'value' => 'book',
                'type' => Lexer::T_NODE_NAME,
                'position' => 13
            ],
        ];

        foreach ($tokens as $expected) {
            $lexer->moveNext();
            $actual = $lexer->lookahead;
            $this->assertEquals($expected['value'], $actual['value']);
            $this->assertEquals($expected['type'], $actual['type']);
            $this->assertEquals($expected['position'], $actual['position']);
        }
        $this->assertFalse($lexer->moveNext());
    }

    public function provideTokens()
    {
        return array(
            array(Lexer::T_NODESEPARATOR, '/'),
            array(Lexer::T_NODE_SEARCH_SEPARATOR, '//'),
            array(Lexer::T_CURRENT_NODE, '.'),
            array(Lexer::T_PARENT_NODE, '..'),
            array(Lexer::T_OR, '|'),
        );
    }
}
