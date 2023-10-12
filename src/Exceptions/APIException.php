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

namespace Vertopal\Exceptions;


/**
 * The parent class of the library exceptions.
 * 
 * The class hierarchy for the library exceptions is:
 * 
 * 
 * Throwable
 *  └── Exception
 *       └── APIException
 *            ├── OtherError
 *            ├── InputNotFoundError
 *            ├── NetworkConnectionError
 *            ├── APIResponseError
 *            │    ├── InvalidJSONResponseError
 *            │    ├── APIError
 *            │    │    ├── InternalServerError
 *            │    │    ├── NotFoundError
 *            │    │    ├── PostMethodAllowedError
 *            │    │    ├── MissingAuthorizationHeaderError
 *            │    │    ├── InvalidAuthorizationHeaderError
 *            │    │    ├── InvalidFieldError
 *            │    │    ├── MissingRequiredFieldError
 *            │    │    ├── WrongTypeFieldError
 *            │    │    ├── InvalidDataKeyError
 *            │    │    ├── MissingRequiredDataKeyError
 *            │    │    ├── WrongTypeDataKeyError
 *            │    │    ├── WrongValueDataKeyError
 *            │    │    ├── InvalidCredentialError
 *            │    │    ├── FreePlanDisallwedError
 *            │    │    ├── InsufficentVCreditsError
 *            │    │    ├── InvalidCallbackError
 *            │    │    ├── UnverifiedDomainCallbackError
 *            │    │    ├── NoConnectorDependentTaskError
 *            │    │    ├── NotReadyDependentTaskError
 *            │    │    ├── MismatchVersionDependentTaskError
 *            │    │    ├── MismatchDependentTaskError
 *            │    │    ├── FileNotExistsError
 *            │    │    ├── DownloadExpiredError
 *            │    │    ├── OnlyDevelopmentRequestError
 *            │    │    ├── InvalidParameterError
 *            │    │    ├── MissingRequiredParameterError
 *            │    │    ├── WrongTypeParameterError
 *            │    │    ├── WrongValueParameterError
 *            │    │    ├── OnlyDevelopmentFileError
 *            │    │    ├── NotValidExtensionError
 *            │    │    ├── LimitUploadSizeError
 *            │    │    ├── EmptyFileError
 *            │    │    ├── WrongOutputFormatStructureError
 *            │    │    ├── InvalidOutputFormatError
 *            │    │    ├── WrongInputFormatStructureError
 *            │    │    ├── InvalidInputFormatError
 *            │    │    ├── NoConverterInputToOutputError
 *            │    │    ├── NotMatchExtensionAndInputError
 *            │    │    └── FailedConvertError
 *            │    └── HTTPResponseError
 *            ├── APITaskError
 *            │    └── EntityStatusNotRunningError
 *            └── OutputWriteError
 * 
 * @access public
 */
class APIException extends \Exception {

}
