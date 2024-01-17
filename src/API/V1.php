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

use ValueError;


/**
 * Provides methods for sending requests to the Vertopal API-v1 endpoints.
 * 
 * @access public
 */
class V1 extends ConnectionInterface
{

    /**
     * The API endpoint.
     * @access private
     * @var string
     */
    private const ENDPOINT = "https://api.vertopal.com/v1";

    /**
     * Send an upload request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param string $filename Input file name.
     * @param string $filepath Path of the input file.
     * @param Credential $credential Your client credentials.
     * @return object|bool Returns the HTTP response or `false` on failure.
     */
    public static function upload(
        string $filename,
        string $filepath,
        Credential $credential
    ): mixed
    {
        $data = [
            "data" => "{" .
                "\"app\": \"{$credential->getApp()}\"" .
            "}",
            "file" => new \CURLFile($filepath, null, $filename),
        ];

        $response = self::request(
            "/upload/file",
            $credential->getToken(),
            $data,
        );
        
        return $response;
    }

    /**
     * Send an upload request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param string $outputFormat The output `format[-type]`, which input file
     *                             will be converted to.
     * @param Credential $credential Your client credentials.
     * @param string $connector The connector from the previous task (Upload).
     * @param string $inputformat The input `format[-type]`. If not specified,
     *                            the `format[-type]` of the input file will be
     *                            considered based on its extension and type.
     * @param string $mode Mode strategy of the task which can be `API.SYNC` or
     *                     `API.ASYNC`.
     * @return object|bool Returns the HTTP response or `false` on failure.
     */
    public static function convert(
        string $outputFormat,
        Credential $credential,
        string $connector,
        ?string $inputFormat = null,
        string $mode = self::ASYNC
    ): mixed
    {
        if ($inputFormat) {
            $ioField = "\"input\": \"{$inputFormat}\"," .
                       "\"output\": \"{$outputFormat}\"";
        } else {
            $ioField = "\"output\": \"{$outputFormat}\"";
        }

        $data = [
            "data" => "{" .
                "\"app\": \"{$credential->getApp()}\"," .
                "\"connector\": \"{$connector}\"," .
                "\"include\": [\"result\", \"entity\"]," .
                "\"mode\": \"{$mode}\"," .
                "\"parameters\": {" .
                    "{$ioField}" .
                "}" .
            "}"
        ];

        $response = self::request(
            "/convert/file",
            $credential->getToken(),
            $data,
        );
        
        return $response;
    }

    /**
     * Send a convert status request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param Credential $credential Your client credentials.
     * @param string $connector The connector of a convert task.
     * @return object|bool Returns the HTTP response or `false` on failure.
     */
    public static function status(
        Credential $credential,
        string $connector
    ): mixed
    {
        $data = [
            "data" => "{" .
                "\"app\": \"{$credential->getApp()}\"," .
                "\"connector\": \"{$connector}\"" .
            "}"
        ];

        $response = self::request(
            "/convert/status",
            $credential->getToken(),
            $data,
        );
        
        return $response;
    }

    /**
     * Send a task response request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param Credential $credential Your client credentials.
     * @param string $connector The connector from the previous task (Upload).
     * @return object|bool Returns the HTTP response or `false` on failure.
     */
    public static function taskResponse(
        Credential $credential,
        string $connector
    ): mixed
    {
        $data = [
            "data" => "{" .
                "\"app\": \"{$credential->getApp()}\"," .
                "\"connector\": \"{$connector}\"," .
                "\"include\": [\"result\"]" .
            "}"
        ];

        $response = self::request(
            "/task/response",
            $credential->getToken(),
            $data,
        );
        
        return $response;
    }

    /**
     * Send a download URL request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param Credential $credential Your client credentials.
     * @param string $connector The connector of a task.
     * @return object|bool Returns the HTTP response or `false` on failure.
     */
    public static function downloadURL(
        Credential $credential,
        string $connector
    ): mixed
    {
        $data = [
            "data" => "{" .
                "\"app\": \"{$credential->getApp()}\"," .
                "\"connector\": \"{$connector}\"" .
            "}"
        ];

        $response = self::request(
            "/download/url",
            $credential->getToken(),
            $data,
        );
        
        return $response;
    }

    /**
     * Send a download file request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param Credential $credential Your client credentials.
     * @param string $connector The connector of a task.
     * @param string $filename The output file name to save on the disk.
     * @return bool Returns `true` on success and `false` on failure.
     */
    public static function downloadFile(
        Credential $credential,
        string $connector,
        string $filename
    ): bool
    {
        $data = [
            "data" => "{" .
                "\"app\": \"{$credential->getApp()}\"," .
                "\"connector\": \"{$connector}\"" .
            "}"
        ];

        $response = self::request(
            "/download/url/get",
            $credential->getToken(),
            $data,
            self::RESPONSE_TO_FILE,
            $filename,
        );
        
        return $response;
    }

    /**
     * Send a format/get request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param Credential $credential Your client credentials.
     * @param string $format The specific `format[-type]` you want to get
     *                       its properties.
     * @return object|bool Returns the HTTP response or `false` on failure.
     */
    public static function formatGet(
        Credential $credential,
        string $format
    ): mixed
    {
        $data = [
            "data" => "{" .
                "\"app\": \"{$credential->getApp()}\"," .
                "\"parameters\": {" .
                    "\"format\": \"{$format}\"" .
                "}" .
            "}"
        ];

        $response = self::request(
            "/format/get",
            $credential->getToken(),
            $data,
        );
        
        return $response;
    }

    /**
     * Send a convert/graph request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param Credential $credential Your client credentials.
     * @param string $input The input `format[-type]`.
     * @param string $output The output `format[-type]`.
     * @return object|bool Returns the HTTP response or `false` on failure.
     */
    public static function convertGraph(
        Credential $credential,
        string $input,
        string $output
    ): mixed
    {
        $data = [
            "data" => "{" .
                "\"app\": \"{$credential->getApp()}\"," .
                "\"parameters\": {" .
                    "\"input\": \"{$input}\"," .
                    "\"output\": \"{$output}\"" .
                "}" .
            "}"
        ];

        $response = self::request(
            "/convert/graph",
            $credential->getToken(),
            $data,
        );
        
        return $response;
    }

    /**
     * Send a convert/formats request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param Credential $credential Your client credentials.
     * @param string $sublist Selects supported formats based on whether they
     *                        are inputs or outputs. Valid values
     *                        are `ConnectionInterface.INPUTS`
     *                        and `ConnectionInterface.OUTPUTS`.
     * @param string $format The specific `format[-type]` you want to
     *                       get its supported input or output formats.
     * @return object|bool Returns the HTTP response or `false` on failure.
     */
    public static function convertFormats(
        Credential $credential,
        string $sublist,
        ?string $format = null
    ): mixed
    {
        if (!in_array($sublist, [self::INPUTS, self::OUTPUTS])) {
            throw new ValueError('sublist must be ' .
                                 'either "inputs" or "outputs"');
        }

        if ($format) {
            $dataFormat = ",\"format\": \"{$format}\"";
        } else {
            $dataFormat = "";
        }

        $data = [
            "data" => "{" .
                "\"app\": \"{$credential->getApp()}\"," .
                "\"parameters\": {" .
                    "\"sublist\": \"{$sublist}\"" .
                    "{$dataFormat}" .
                "}" .
            "}"
        ];

        $response = self::request(
            "/convert/formats",
            $credential->getToken(),
            $data,
        );
        
        return $response;
    }

    /**
     * Send a HTTP request to the Vertopal API endpoint.
     * 
     * @static
     * @access public
     *
     * @param string $path Path of the API endpoint.
     * @param string $token The client security token.
     * @param array $data The HTTP Request data field.
     * @param bool $responseMode If `self::RESPONSE_DECODE`, the HTTP response
     *                           will be decoded as php object, if
     *                           `self::RESPONSE_TO_FILE` the HTTP response will
     *                           be saved to disk, and if `self::RESPONSE_RAW`
     *                           the HTTP response is returned in raw string.
     * @param string $outputFile The output file name when the `$responseMode`
     *                           is set to `SELF::RESPONSE_TO_FILE`.
     * @return object|bool Returns the HTTP response, `false` on failure,
     *                     or `true` if `$responseMode` is
     *                     `self::RESPONSE_TO_FILE`.
     */
    private static function request(
        string $path,
        string $token,
        array $data,
        int $responseMode = self::RESPONSE_DECODE,
        string $outputFile = null
    ): mixed
    {
        $requestEndpoint = self::ENDPOINT . $path;
        $headers = self::getHeaders($token);
        $output = null;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $requestEndpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if ($responseMode == self::RESPONSE_TO_FILE) {
            if (!$outputFile) {
                throw new ValueError("outputFile is not set.");
            }
            $output = fopen($outputFile, "w");
            curl_setopt($curl, CURLOPT_FILE, $output);
        }

        $response = curl_exec($curl);

        curl_close($curl);

        if (isset($output)) {
            fclose($output);
        }


        if ($response) {
            $responseCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            if ($responseMode == self::RESPONSE_DECODE) {
                return new Response($response, $responseCode);
            }
            return $response;
        }
        return false;
    }

}
