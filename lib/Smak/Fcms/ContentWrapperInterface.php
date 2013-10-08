<?php

namespace Smak\Fcms;

interface ContentWrapperInterface
{
    public function getEngine();

    public function getContentPath();

    public function getIndexBasename();

    public function getExtensions();

    public function getIndex();
}