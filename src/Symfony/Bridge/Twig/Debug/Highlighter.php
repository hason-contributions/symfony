<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Twig\Debug;

/**
 * The base class for syntax highlighters
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
abstract class Highlighter
{
    protected function lines($lines, $line, $count)
    {
        $code = '';
        $lastOpenSpan = '';

        $from = max($line - $count, 1);
        if ($from > 1 && 0 !== strpos($line[$from - 1], '<span')) {
            for ($i = $from - 2; $i >= 0; $i--) {
                if (preg_match('#^.*(</?span[^>]*>)#', $lines[$i], $match) && '/' != $match[1][1]) {
                    $lastOpenSpan = $match[1];
                    break;
                }
            }
        }

        for ($i = $from, $max = min($line + $count, count($lines)); $i <= $max; $i++) {
            $code .= '<li'.($i == $line ? ' class="selected"' : '').'><code>'.($lastOpenSpan.$lines[$i - 1]);
            if (preg_match('#^.*(</?span[^>]*>)#', $lines[$i - 1], $match)) {
                $lastOpenSpan = '/' != $match[1][1] ? $match[1] : '';
            }

            if ($lastOpenSpan) {
                $code .= '</span>';
            }

            $code .= "</code></li>\n";
        }

        return '<ol class="code" start="'.max($line - $count, 1).'">'.$code.'</ol>';
    }

    /**
     * Returns true if this class is able to highlight the given file name.
     *
     * @param string $name A file name
     *
     * @return bool true if this class supports the given file name, false otherwise
     */
    abstract public function supports($file);
}
