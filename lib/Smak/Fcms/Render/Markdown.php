<?php

namespace Smak\Fcms\Parser;

use Smak\Fcms\RenderInterface;

/**
 * Markdown.php
 *
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2013, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Markdown implements RenderInterface
{
    const ID = "markdown";

    const FILE_EXTENSION = ".md";

    public function render(\SplFileInfo $file)
    {
        if (! $file->isReadable()) {
            throw new \RuntimeException('File has not read permissions.');
        }

        $contents = file_get_contents($file->getRealPath());

        if (false === $contents) {
            throw new \RuntimeException(error_get_last());
        }

        return \Michelf\Markdown::defaultTransform($contents);
    }
}
