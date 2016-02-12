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

use Doctrine\Common\Lexer\AbstractLexer;

final class Lexer extends AbstractLexer
{

    const T_NODESEPARATOR = 1;
    const T_NODE_SEARCH_SEPARATOR = 2;
    const T_CURRENT_NODE = 3;
    const T_PARENT_NODE = 4;
    const T_OR = 5;


    const T_NODE_NAME = 101;
    const T_ATTRIBUTE = 102;

    public function __construct($input)
    {
        $this->setInput($input);
    }

    public function setInput($input)
    {
        $input = preg_replace('~/([a-z_][a-z0-9_-])~', '/:$1', $input);
        parent::setInput($input);
    }

    /**
     * Lexical catchable patterns.
     *
     * @return array
     */
    protected function getCatchablePatterns()
    {
        return array(
            '\.{2,}',
            '\/{2,}',
            ':[a-z_][a-z0-9_]*',
            '@[a-z_][a-z0-9_]*',
        );
    }

    /**
     * Lexical non-catchable patterns.
     *
     * @return array
     */
    protected function getNonCatchablePatterns()
    {
        return array('\s+');
    }

    /**
     * Retrieve token type. Also processes the token value if necessary.
     *
     * @param string $value
     *
     * @return integer
     */
    protected function getType(&$value)
    {
        switch (true) {
            case $value === '//':
                return static::T_NODE_SEARCH_SEPARATOR;
            case $value === '/':
                return static::T_NODESEPARATOR;
            case $value === '.':
                return static::T_CURRENT_NODE;
            case $value === '..':
                return static::T_PARENT_NODE;
            case $value === '|':
                return static::T_OR;
            case $value[0] === ':':
                $value = ltrim($value, ':');
                return static::T_NODE_NAME;
            case $value[0] === '@':
                $value = ltrim($value, '@');
                return static::T_ATTRIBUTE;
        }
    }
}
