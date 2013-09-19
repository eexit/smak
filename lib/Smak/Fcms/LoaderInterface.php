<?php

namespace Smak\Fcms;

/**
 * LoaderInterface.php
 *
 * @author Joris Berthelot <joris@berthelot.tel>
 * @copyright Copyright (c) 2013, Joris Berthelot
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
interface LoaderInterface
{
    public function find($file_uri);
}
