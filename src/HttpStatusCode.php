<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * A collection of integer HTTP status codes by their common names.
 *
 * @package Bogosoft\Http\Routing
 */
class HttpStatusCode
{
    const ACCEPTED                        = 202;
    const ALREADY_REPORTED                = 208;
    const AUTHENTICATION_TIMEOUT          = 419;
    const BAD_GATEWAY                     = 502;
    const BAD_REQUEST                     = 400;
    const CONFLICT                        = 409;
    const CONTINUE                        = 100;
    const CREATED                         = 201;
    const EXPECTATION_FAILED              = 417;
    const FAILED_DEPENDENCY               = 424;
    const FORBIDDEN                       = 403;
    const FOUND                           = 302;
    const GATEWAY_TIMEOUT                 = 504;
    const GONE                            = 410;
    const HTTP_VERSION_NOT_SUPPORTED      = 505;
    const IM_A_TEAPOT                     = 418;
    const IM_USED                         = 226;
    const INSUFFICIENT_STORAGE            = 507;
    const INTERNAL_SERVER_ERROR           = 500;
    const LENGTH_REQUIRED                 = 411;
    const LOCKED                          = 423;
    const LOOP_DETECTED                   = 508;
    const METHOD_NOT_ALLOWED              = 405;
    const MISDIRECTED_REQUEST             = 421;
    const MOVED_PERMANENTLY               = 301;
    const MULTI_STATUS                    = 207;
    const MULTIPLE_CHOICES                = 300;
    const NETWORK_AUTHENTICATION_REQUIRED = 511;
    const NO_CONTENT                      = 204;
    const NON_AUTHORITATIVE               = 203;
    const NOT_ACCEPTABLE                  = 406;
    const NOT_EXTENDED                    = 510;
    const NOT_FOUND                       = 404;
    const NOT_IMPLEMENTED                 = 501;
    const NOT_MODIFIED                    = 304;
    const OK                              = 200;
    const PARTIAL_CONTENT                 = 206;
    const PAYLOAD_TOO_LARGE               = 413;
    const PAYMENT_REQUIRED                = 402;
    const PERMANENT_REDIRECT              = 308;
    const PRECONDITION_FAILED             = 412;
    const PRECONDITION_REQUIRED           = 428;
    const PROCESSING                      = 102;
    const PROXY_AUTHENTICATION_REQUIRED   = 407;
    const RANGE_NOT_SATISFIABLE           = 416;
    const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const REQUEST_TIMEOUT                 = 408;
    const RESET_CONTENT                   = 205;
    const SEE_OTHER                       = 303;
    const SERVICE_UNAVAILABLE             = 503;
    const SWITCH_PROXY                    = 306;
    const SWITCHING_PROTOCOLS             = 101;
    const TEMPORARY_REDIRECT              = 307;
    const TOO_MAN_REQUESTS                = 429;
    const UNAUTHORIZED                    = 401;
    const UNAVAILABLE_FOR_LEGAL_REASONS   = 451;
    const UNPROCESSABLE_ENTITY            = 422;
    const UNSUPPORTED_MEDIA_TYPE          = 415;
    const UPGRADE_REQUIRED                = 426;
    const URI_TOO_LONG                    = 414;
    const USE_PROXY                       = 305;
    const VARIANT_ALSO_NEGOTIATES         = 506;
}
