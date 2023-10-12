<?php

/**
 * VertopalPHPLib - PHP library for Vertopal file conversion API.
 * PHP Version 7.4.0
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


/**
 * Client credential constructor class for HTTP requests.
 * Uses the app ID and security token as client credentials
 * for the API authorization.
 * 
 * @access public
 */
class Credential
{

    /**
     * Client credentials: app ID.
     * @access private
     * @var string
     */
    private $app;

    /**
     * Client credentials: security token.
     * @access private
     * @var string
     */
    private $token;

    /**
     * Credential constructor.
     * 
     * @access public
     * 
     * @param string $app The app ID.
     * @param string $token The security token.
     */
    public function __construct(string $app, string $token)
    {
        $this->app = $app;
        $this->token = $token;
    }

    /**
     * Getter method for the `$app` property.
     * 
     * @access public
     * 
     * @return string Returns the app ID.
     */
    public function getApp(): string
    {
        return $this->app;
    }

    /**
     * Getter method for the `$token` property.
     * 
     * @access public
     * 
     * @return string Returns the security token.
     */
    public function getToken(): string
    {
        return $this->token;
    }

}
