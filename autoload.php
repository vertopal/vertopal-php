<?php

/**
 * VertopalPHPLib - PHP library for Vertopal file conversion API.
 * PHP Version 5.5.
 *
 * @see https://github.com/vertopal/vertopal-php/ The Vertopal-PHP GitHub repo
 *
 * @author    Vertopal <contact@vertopal.com>
 * @copyright 2023 Vertopal - https://www.vertopal.com
 * @license   MIT, see LICENSE for more details
 * @note      This program is distributed in the hope that it will be useful - 
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

 
spl_autoload_register(
    function ($className) {
        $classPath = str_replace("\\", "/", $className);
        list($namespace, $classPath) = explode("/", $classPath, 2);
        
        $filePath = dirname(__FILE__) . "/src/" . $classPath . ".php";
        if (file_exists($filePath)) {
            require_once($filePath);
        }
    }
);
