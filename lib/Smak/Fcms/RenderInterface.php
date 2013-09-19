<?php

namespace Smak\Fcms;

/**
 * RenderInterface.php
 *
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2013, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
interface RenderInterface
{
    public function render(\SplFileInfo $file);
}
