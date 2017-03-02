<?php

namespace Ipunkt\LaravelJsonApiDoc\Services\Documentation\Parsers;

class DocblockParser
{
    /**
     * Remove docblocks from comment block
     *
     * @param string $block
     * @return string
     */
    public function removeTags(string $block): string
    {
        $pattern = "~^ \\* @.*\n~m";
        return preg_replace($pattern, "", $block);
    }

    /**
     * returns pure comment
     *
     * @param string $block
     * @return string
     */
    public function pureComment(string $block): string
    {
        $withoutTags = $this->removeTags($block);
        // Remove from beginning:
        // '/**'
        // ' * '
        // ' */'
        // and at the ending:
        // ' *'
        $pattern = '~^(/\*\*)|^( \*\/)|^( \*)$|^( \* )~m';
        return preg_replace($pattern, "", $withoutTags);
    }

    /**
     * find tags
     *
     * @param string $tag
     * @param string $block
     * @return array|mixed
     */
    public function findTags(string $tag, string $block)
    {
        $pattern = "~^ \\* @$tag ?(.*)$~m";

        $matches = array();
        $numMatches = preg_match_all($pattern, $block, $matches);
        if ($numMatches < 1) {
            return array();
        }

        return $matches[1];
    }

    /**
     *  does a tag exists
     *
     * @param string $tag
     * @param string $docblock
     * @return bool
     */
    public function hasTag(string $tag, string $docblock): bool
    {
        $pattern = "~\\* @$tag~";
        return preg_match($pattern, $docblock) > 0;
    }
}
