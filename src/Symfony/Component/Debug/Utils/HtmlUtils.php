<?php

namespace Symfony\Component\Debug\Utils;

class HtmlUtils
{
    public static function abbrClass($class)
    {
        $parts = explode('\\', $class);

        return sprintf('<abbr title="%s">%s</abbr>', $class, array_pop($parts));
    }

    public static function abbrMethod($method)
    {

    }

    private function formatPath($path, $line)
    {
        $path = $this->escapeHtml($path);
        $file = preg_match('#[^/\\\\]*$#', $path, $file) ? $file[0] : $path;

        if ($linkFormat = $this->fileLinkFormat) {
            $link = strtr($this->escapeHtml($linkFormat), array('%f' => $path, '%l' => (int) $line));

            return sprintf('<a href="%s" title="Go to source">%s line %d</a>', $link, $file, $line);
        }

        return sprintf('<a title="%s line %3$d" ondblclick="var f=this.innerHTML;this.innerHTML=this.title;this.title=f;">%s line %d</a>', $path, $file, $line);
    }

    public static function ss($file, $line, $rootDir = '', $text = null)
    {
        $file = trim($file);

        if (null === $text) {
            $text = str_replace('\\', '/', $file);
            if (0 === strpos($text, $rootDir)) {
                $text = substr($text, strlen($rootDir));
                $text = explode('/', $text, 2);
                $text = sprintf('<abbr title="%s%2$s">%s</abbr>%s', $rootDir, $text[0], isset($text[1]) ? '/'.$text[1] : '');
            }
        }

        $text = "$text at line $line";

        if (false !== $link = $this->getFileLink($file, $line)) {
            if (PHP_VERSION_ID >= 50400) {
                $flags = ENT_QUOTES | ENT_SUBSTITUTE;
            } else {
                $flags = ENT_QUOTES;
            }

            return sprintf('<a href="%s" title="Click to open this file" class="file_link">%s</a>', htmlspecialchars($link, $flags, $this->charset), $text);
        }

        return $text;
    }

    /**
     * Formats an array as a string.
     *
     * @param array $args The argument array
     *
     * @return string
     */
    private function formatArgs(array $args)
    {
        $result = array();
        foreach ($args as $key => $item) {
            if (is_array($item)) {
                $formattedValue = $this->dumpVariable($item);
            } elseif ($item instanceof \stdClass) {
                $formattedValue = $this->dumpVariable(array_values((array) $item));
            } elseif ($item instanceof Data) {
                $dump = fopen('php://memory', 'r+b');
                $dumper = new HtmlDumper($dump);
                $dumper->dump($item);
                rewind($dump);

                $formattedValue = stream_get_contents($dump);
            }

            $result[] = is_int($key) ? $formattedValue : sprintf('$%s = %s', $key, $formattedValue);
        }

        return implode(', ', $result);
    }

    private function dumpVariable($item)
    {
        switch ($item[0]) {
            case 'object':
                return sprintf('<em>object</em>(%s)', $this->formatClass($item[1]));
            case 'array':
                $result = array();
                foreach ((array) $item[1] as $key => $value) {
                    $formattedValue = is_array($value) ? $this->dumpVariable($value) : $value;
                    $result[] = is_int($key) ? $formattedValue : sprintf("'%s' => %s", $key, $formattedValue);
                }

                return sprintf('<em>array</em>(%s)', implode(', ', $result));
            case 'string':
                return sprintf("'%s'", $this->escapeHtml($item[1]));
            case 'null':
                return '<em>null</em>';
            case 'boolean':
                return '<em>'.strtolower(var_export($item[1], true)).'</em>';
            case 'resource':
                return '<em>resource</em>';
            default:
                return str_replace("\n", '', var_export($this->escapeHtml((string) $item[1]), true));
        }
    }
}
