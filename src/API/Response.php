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
 * Provides different methods for holding the response of an HTTP request.
 * 
 * @access public
 */
class Response
{

    /**
     * The HTTP response body.
     * @access public
     * @var string
    */
    public $response;

    /**
     * The status code of the HTTP response.
     * @access public
     * @var int
     */
    public $statusCode;

    /**
     * Response constructor.
     * 
     * @access public
     * 
     * @param string $response The HTTP response body.
     * @param int $statusCode The status code of the HTTP response.
     */
    public function __construct(string $response, int $statusCode) {
        $this->response = $response;
        $this->statusCode = $statusCode;
    }

    /**
     * Decode the HTTP response body to a JSON array or PHP object.
     * 
     * @access public
     * 
     * @return mixed The HTTP response in JSON.
     */
    public function json(): mixed
    {
        return json_decode($this->response);
    }

}
