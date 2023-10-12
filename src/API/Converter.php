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

use Vertopal\Exceptions\APIError;
use Vertopal\Exceptions\EntityStatusNotRunningError;
use Vertopal\Exceptions\HTTPResponseError;
use Vertopal\Exceptions\InputNotFoundError;


/**
 * Provides different methods to simplify a file conversion.
 * 
 * @access public
 */
class Converter
{

    /**
     * The input file name or path.
     * @access private
     * @var string
     */
    private $inputFile;

    /**
     * Client credentials: app ID and security token.
     * @access private
     * @var Credential
     */
    private $credential;

    /**
     * The connector of the convert task.
     * @access private
     * @var ?string
     */
    private $convertConnector;

    /**
     * The status of the convert.
     * @access private
     * @var ?string
     */
    private $convertStatus;

    /**
     * Number of total vCredits spent for the conversion.
     * @access public
     * @var ?int
     */
    public $vcredits;

    /**
     * Map the api response error code to its Exception class name.
     * @access private
     * @var array
     */
    private const ERROR_CODES = [
        "INTERNAL_SERVER_ERROR" => "InternalServerError",
        "NOT_FOUND" => "NotFoundError",
        "POST_METHOD_ALLOWED" => "PostMethodAllowedError",
        "MISSING_AUTHORIZATION_HEADER" => "MissingAuthorizationHeaderError",
        "INVALID_AUTHORIZATION_HEADER" => "InvalidAuthorizationHeaderError",
        "INVALID_FIELD" => "InvalidFieldError",
        "MISSING_REQUIRED_FIELD" => "MissingRequiredFieldError",
        "WRONG_TYPE_FIELD" => "WrongTypeFieldError",
        "INVALID_DATA_KEY" => "InvalidDataKeyError",
        "MISSING_REQUIRED_DATA_KEY" => "MissingRequiredDataKeyError",
        "WRONG_TYPE_DATA_KEY" => "WrongTypeDataKeyError",
        "WRONG_VALUE_DATA_KEY" => "WrongValueDataKeyError",
        "INVALID_CREDENTIAL" => "InvalidCredentialError",
        "FREE_PLAN_DISALLOWED" => "FreePlanDisallwedError",
        "INSUFFICIENT_VCREDITS" => "InsufficentVCreditsError",
        "INVALID_CALLBACK" => "InvalidCallbackError",
        "UNVERIFIED_DOMAIN_CALLBACK" => "UnverifiedDomainCallbackError",
        "NO_CONNECTOR_DEPENDENT_TASK" => "NoConnectorDependentTaskError",
        "NOT_READY_DEPENDENT_TASK" => "NotReadyDependentTaskError",
        "MISMATCH_VERSION_DEPENDENT_TASK" => "MismatchVersionDependentTaskError",
        "MISMATCH_DEPENDENT_TASK" => "MismatchDependentTaskError",
        "FILE_NOT_EXISTS" => "FileNotExistsError",
        "DOWNLOAD_EXPIRED" => "DownloadExpiredError",
        "ONLY_DEVELOPMENT_REQUEST" => "OnlyDevelopmentRequestError",
        "INVALID_PARAMETER" => "InvalidParameterError",
        "MISSING_REQUIRED_PARAMETER" => "MissingRequiredParameterError",
        "WRONG_TYPE_PARAMETER" => "WrongTypeParameterError",
        "WRONG_VALUE_PARAMETER" => "WrongValueParameterError",
        "ONLY_DEVELOPMENT_FILE" => "OnlyDevelopmentFileError",
        "NOT_VALID_EXTENSION" => "NotValidExtensionError",
        "LIMIT_UPLOAD_SIZE" => "LimitUploadSizeError",
        "EMPTY_FILE" => "EmptyFileError",
        "WRONG_OUTPUT_FORMAT_STRUCTURE" => "WrongOutputFormatStructureError",
        "INVALID_OUTPUT_FORMAT" => "InvalidOutputFormatError",
        "WRONG_INPUT_FORMAT_STRUCTURE" => "WrongInputFormatStructureError",
        "INVALID_INPUT_FORMAT" => "InvalidInputFormatError",
        "NO_CONVERTER_INPUT_TO_OUTPUT" => "NoConverterInputToOutputError",
        "NOT_MATCH_EXTENSION_AND_INPUT" => "NotMatchExtensionAndInputError",
        "FAILED_CONVERT" => "FailedConvertError",
    ];

    /**
     * Converter constructor.
     * 
     * @access public
     * 
     * @param string $inputFile The input file name or path.
     * @param Credential $credential The client credentials.
     */
    public function __construct(string $inputFile, Credential $credential) {
        $this->inputFile = $inputFile;
        $this->credential = $credential;
        $this->convertConnector = null;
        $this->convertStatus = null;
        $this->vcredits = null;
    }

    /**
     * Start converting the input file to the desired output format.
     * 
     * @access public
     * 
     * @param string $outputFormat The output `format[-type]`.
     * @param ?string $inputFormat The input `format[-type]`. If not
     * specified, the `format[-type]` of the input file will be 
     * considered based on its extension and type. Defaults to `null`.
     * @return void
     */
    public function convert(
        string $outputFormat,
        ?string $inputFormat = null
    ): void
    {
        $filepath = realpath($this->inputFile);
        if (!$filepath || !is_file($filepath)) {
            throw new InputNotFoundError;
        }
        $filename = basename($filepath);

        $uploadResponse = $this->callTask(
            [V1::class, "upload"],
            [
                "filename" => $filename,
                "filepath" => $filepath,
                "credential" => $this->credential,
            ],
        );
        $uploadConnector = $uploadResponse->result->output->connector;

        $convertResponse = $this->callTask(
            [V1::class, "convert"],
            [
                "outputFormat" => $outputFormat,
                "credential" => $this->credential,
                "connector" => $uploadConnector,
                "inputFormat" => $inputFormat,
                "mode" => V1::ASYNC,
            ],
        );
        if ($convertResponse->entity->status != "running") {
            throw new EntityStatusNotRunningError;
        }
        $this->convertConnector = $convertResponse->entity->id;
    }

    /**
     * Wait for the convert to complete.
     * 
     * @access public
     * 
     * @param array $sleepPattern Sleeps seconds to delay
     * between each check for the completion
     * of the convert. Defaults to `(10, 20, 30, 60)`.
     * @return void
     */
    public function wait(array $sleepPattern = [10, 20, 30, 60]): void
    {
        $sleepStep = 0;
        while (!$this->isCompleted()) {
            sleep($sleepPattern[$sleepStep]);
            if ($sleepStep < sizeof($sleepPattern) - 1) {
                $sleepStep += 1;
            }
        }
    }

    /**
     * Check if the convert task is completed or not.
     * 
     * @access public
     * 
     * @return bool `true` if the convert task is completed, otherwise `false`.
     */
    public function isCompleted(): bool
    {
        if ($this->convertTaskStatus()->task == "completed") {
            return true;
        }
        return false;
    }

    /**
     * Check if the result of the convert task is successful or not.
     * 
     * @access public
     * 
     * @return bool `true` if the convert is successful, otherwise `false`.
     */
    public function isConverted(): bool
    {
        if ($this->convertStatus == "successful") {
            return true;
        }
        return false;
    }

    /**
     * Download the converted file and write it to the disk.
     * 
     * @access public
     * 
     * @param ?string $filename Output file name or path
     * to write to the disk. If set to `null`, the filename
     * received from the API server will be used. Defaults to `null`.
     * @return string The absolute path of the downloaded file if `$filename`
     * is set to `null`, otherwise the `$filename`.
     */
    public function download(?string $filename = null): string
    {
        $downloadURL = $this->downloadURL();

        if ($filename) {
            $outputFile = $filename;
        } else {
            $outputFile = $downloadURL->filename;
        }

        V1::downloadFile(
            $this->credential,
            $downloadURL->connector,
            $outputFile,
        );

        if ($path = realpath($outputFile)) {
            return $path;
        }
        return $filename;
    }

    /**
     * Send a download request and get the download connector and filename.
     * 
     * @access public
     * 
     * @return object A PHP object with `connector` (download connector), and
     * `filename` (download filename).
     */
    protected function downloadURL(): object
    {
        $json = $this->callTask(
            [V1::class, "downloadURL"],
            [
                "credential" => $this->credential,
                "connector" => $this->convertConnector,
            ],
        );
    
        return (object) [
            "connector" => $json->result->output->connector,
            "filename" => $json->result->output->name,
        ];
    }

    /**
     * Run a convert task response request using Vertopal API.
     * 
     * @access public
     * 
     * @return object A PHP object with `task` (task status),
     * `vcredits` (used vCredits), and `convert` (convert status) properties.
     */
    protected function convertTaskStatus(): object
    {
        $json = $this->callTask(
            [V1::class, "taskResponse"],
            [
                "credential" => $this->credential,
                "connector" => $this->convertConnector,
            ],
        );

        $result = $json->result->output->result;
        if ((array) $result) {
            $this->convertStatus = $result->output->status;
            $this->vcredits = $json->result->output->entity->vcredits;
        } else {
            $this->convertStatus = null;
        }

        return (object) [
            "task" => $json->result->output->entity->status,
            "vcredits" => $this->vcredits,
            "convert" => $this->convertStatus,
        ];
    }

    /**
     * Call an API task.
     * 
     * @access public
     * 
     * @param callable $func The API task callable.
     * @param array $kwargs Keyword arguments of the callable `$func`.
     * @return object JSON response of the API cast to PHP object.
     */
    protected function callTask(callable $func, array $kwargs): object
    {
        $response = call_user_func_array($func, $kwargs);
        $json = $response->json();

        $code = null;
        if (isset($json->code)) {
            $code = $json->code;
            $message = isset($json->message) ? $json->message : "";
        }
        if (isset($json->result->error->code)) {
            $code = $json->result->error->code;
            if (isset($json->result->error->message)) {
                $message = $json->result->error->message;
            } else {
                $message = "";
            }
        }
        if ($code) {
            if (isset(self::ERROR_CODES[$code])) {
                $exc = "Vertopal\Exceptions\\" . self::ERROR_CODES[$code];
                throw new $exc($message);
            }
            throw new APIError;
        }
        if (in_array(floor($response->statusCode / 100), [4, 5])) {
            throw new HTTPResponseError;
        }
        return $json;
    }

}
