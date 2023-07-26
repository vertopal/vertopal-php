<?php

/**
 * VertopalPHPLib - PHP library for Vertopal file conversion API.
 * PHP Version 5.5.
 *
 * @see https://github.com/vertopal/vertopal-php/ The Vertopal-PHP GitHub repo.
 *
 * @author    Vertopal <contact@vertopal.com>
 * @copyright 2023 Vertopal - https://www.vertopal.com
 * @license   MIT, see LICENSE for more details
 * @note      This program is distributed in the hope that it will be useful - 
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Vertopal\API;

use Vertopal\Vertopal;


/**
 * Interface class used as parent of different API versions.
 * Provides different methods for generating User-Agent string and HTTP headers.
 * 
 * @access public
 */
class ConnectionInterface
{

    /**
     * HTTP request timeout. in seconds.
     * The maximum number of seconds to allow cURL functions to execute.
     * @access public
     * @staticvar int
     */
    public static $timeout = 30;

    /**
     * Async (asynchronous) mode strategy.
     * @access public
     * @var string
     */
    public const ASYNC = "async";
    /**
     * Sync (synchronous) mode strategy.
     * @access public
     * @var string
     */
    public const SYNC = "sync";

    /**
     * Flag for decoding HTTP response.
     * @access protected
     * @var int
     */
    protected const RESPONSE_DECODE = 1;

    /**
     * Flag for writing HTTP response to disk.
     * @access protected
     * @var int
     */
    protected const RESPONSE_TO_FILE = 2;

    /**
     * Flag for raw HTTP response.
     * @access protected
     * @var int
     */
    protected const RESPONSE_RAW = 3;

    /**
     * Generate User-Agent string for HTTP request header.
     * 
     * @static
     * @access protected
     *
     * @return string Returns User-Agent string.
     */
    protected static function getUserAgent(): string
    {
        $product = Vertopal::LIBNAME;
        $productVersion = Vertopal::VERSION;
        $platformRelease = php_uname("r");
        $platformMachine = php_uname("m");
        $platformSystem = php_uname("s");
        // Rename Windows platform
        if ($platformSystem === "Windows NT") {
            $platformSystem = "Windows";
        }

        $platformFull = $platformSystem;

        if ($platformRelease) {
            // Shorten release info if contains hyphen
            if (str_contains($platformRelease, "-")) {
                $hyphenPosition = strpos($platformRelease, "-");
                $shortRelease = substr($platformRelease, 0, $hyphenPosition);
                $platformFull .= " {$shortRelease}";
            } else {
                $platformFull .= " {$platformRelease}";
            }
        }
        if ($platformMachine) {
            if ($platformMachine === "AMD64") {
                if (str_starts_with(strtolower($platformSystem), "windows")) {
                    $platformFull .= "; Win64";
                }
                $platformFull .= "; x64";
            } else {
                $platformFull .= "; {$platformMachine}";
            }
        }

        $userAgent = "{$product}/{$productVersion} ({$platformFull})";
        return $userAgent;
    }

    /**
     * Concatenate the token provided to build and return an HTTP header.
     * 
     * @static
     * @access public
     *
     * @param string $token Your Security-Token.
     * @return array Returns the HTTP header needed for API requests.
     */
    protected static function getHeaders(string $token): array
    {
        $userAgent = self::getUserAgent();
        return [
            "Authorization: Bearer {$token}",
            "User-Agent: {$userAgent}",
        ];
    }

}
