<?php

namespace tests\units\Smak\Portfolio\Fs;

use Symfony\Component\Finder\Adapter;
use atoum;

class FsAdapter extends atoum
{   
    /**
     * Returns adapters are a dataProvider
     */
    public function getAdaptersTestData()
    {
        return array_map(
            function ($adapter) { return array($adapter); },
            $this->getValidAdapters()
        );
    }

    /**
     * Returns valid adapters
     */
    protected function getValidAdapters()
    {
        return array_filter(
            array(
                new Adapter\BsdFindAdapter(),
                new Adapter\GnuFindAdapter(),
                new Adapter\PhpAdapter()
            ),
            function (Adapter\AdapterInterface $adapter) {
                return $adapter->isSupported();
            }
        );
    }
}

