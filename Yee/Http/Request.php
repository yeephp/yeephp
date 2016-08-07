<?php
/**
 * Yee - a tiny PHP 5 framework
 *
 * @author      Andreas Maier <info@yeephp.com>
 * @copyright   2014 Andreas Maier
 * @link        http://www.yeephp.com
 * @license     http://www.yeephp.com/license
 * @version     1.0.0
 * @package     Yee
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Yee\Http;

/**
 * Yee HTTP Request
 *
 * This class provides a human-friendly interface to the Yee environment variables;
 * environment variables are passed by reference and will be modified directly.
 *
 * @package Yee
 * @author  Andreas Maier
 * @since   1.0.0
 */
class Request
{
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_OVERRIDE = '_METHOD';

    /**
     * @var array
     */
    protected static $formDataMediaTypes = array('application/x-www-form-urlencoded');

    /**
     * Application Environment
     * @var \Yee\Environment
     */
    protected $env;

    /**
     * HTTP Headers
     * @var \Yee\Http\Headers
     */
    public $headers;

    /**
     * HTTP Cookies
     * @var \Yee\Helper\Set
     */
    public $cookies;

    /**
     * Constructor
     * @param \Yee\Environment $env
     */
    public function __construct(\Yee\Environment $env)
    {
        $this->env = $env;
        $this->headers = new \Yee\Http\Headers(\Yee\Http\Headers::extract($env));
        $this->cookies = new \Yee\Helper\Set(\Yee\Http\Util::parseCookieHeader($env['HTTP_COOKIE']));
    }

    /**
     * Get HTTP method
     * @return string
     */
    public function getMethod()
    {
        return $this->env['REQUEST_METHOD'];
    }

    /**
     * Is this a GET request?
     * @return bool
     */
    public function isGet()
    {
        return $this->getMethod() === self::METHOD_GET;
    }

    /**
     * Is this a POST request?
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() === self::METHOD_POST;
    }

    /**
     * Is this a PUT request?
     * @return bool
     */
    public function isPut()
    {
        return $this->getMethod() === self::METHOD_PUT;
    }

    /**
     * Is this a PATCH request?
     * @return bool
     */
    public function isPatch()
    {
        return $this->getMethod() === self::METHOD_PATCH;
    }

    /**
     * Is this a DELETE request?
     * @return bool
     */
    public function isDelete()
    {
        return $this->getMethod() === self::METHOD_DELETE;
    }

    /**
     * Is this a HEAD request?
     * @return bool
     */
    public function isHead()
    {
        return $this->getMethod() === self::METHOD_HEAD;
    }

    /**
     * Is this a OPTIONS request?
     * @return bool
     */
    public function isOptions()
    {
        return $this->getMethod() === self::METHOD_OPTIONS;
    }

    /**
     * Is this an AJAX request?
     * @return bool
     */
    public function isAjax()
    {
        if ($this->params('isajax')) {
            return true;
        } elseif (isset($this->headers['X_REQUESTED_WITH']) && $this->headers['X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is this an XHR request? (alias of Yee_Http_Request::isAjax)
     * @return bool
     */
    public function isXhr()
    {
        return $this->isAjax();
    }

    /**
     * Fetch GET and POST data
     *
     * This method returns a union of GET and POST data as a key-value array, or the value
     * of the array key if requested; if the array key does not exist, NULL is returned,
     * unless there is a default value specified.
     *
     * @param  string           $key
     * @param  mixed            $default
     * @return array|mixed|null
     */
    public function params($key = null, $default = null)
    {
        $union = array_merge($this->get(), $this->post());
        if ($key) {
            return isset($union[$key]) ? $union[$key] : $default;
        }

        return $union;
    }

    /**
     * Fetch GET data
     *
     * This method returns a key-value array of data sent in the HTTP request query string, or
     * the value of the array key if requested; if the array key does not exist, NULL is returned.
     *
     * @param  string           $key
     * @param  mixed            $default Default return value when key does not exist
     * @return array|mixed|null
     */
    public function get($key = null, $default = null)
    {
        if (!isset($this->env['yee.request.query_hash'])) {
            $output = array();
            if (function_exists('mb_parse_str') && !isset($this->env['yee.tests.ignore_multibyte'])) {
                mb_parse_str($this->env['QUERY_STRING'], $output);
            } else {
                parse_str($this->env['QUERY_STRING'], $output);
            }
            $this->env['yee.request.query_hash'] = Util::stripSlashesIfMagicQuotes($output);
        }
        if ($key) {
            if (isset($this->env['yee.request.query_hash'][$key])) {
                return $this->env['yee.request.query_hash'][$key];
            } else {
                return $default;
            }
        } else {
            return $this->env['yee.request.query_hash'];
        }
    }

    /**
     * Fetch POST data
     *
     * This method returns a key-value array of data sent in the HTTP request body, or
     * the value of a hash key if requested; if the array key does not exist, NULL is returned.
     *
     * @param  string           $key
     * @param  mixed            $default Default return value when key does not exist
     * @return array|mixed|null
     * @throws \RuntimeException If environment input is not available
     */
    public function post($key = null, $default = null)
    {
        if (!isset($this->env['yee.input'])) {
            throw new \RuntimeException('Missing Yee.input in environment variables');
        }
        if (!isset($this->env['yee.request.form_hash'])) {
            $this->env['yee.request.form_hash'] = array();
            if ($this->isFormData() && is_string($this->env['yee.input'])) {
                $output = array();
                if (function_exists('mb_parse_str') && !isset($this->env['yee.tests.ignore_multibyte'])) {
                    mb_parse_str($this->env['yee.input'], $output);
                } else {
                    parse_str($this->env['yee.input'], $output);
                }
                $this->env['yee.request.form_hash'] = Util::stripSlashesIfMagicQuotes($output);
            } else {
                $this->env['yee.request.form_hash'] = Util::stripSlashesIfMagicQuotes($_POST);
            }
        }
        if ($key) {
            if (isset($this->env['yee.request.form_hash'][$key])) {
                return $this->env['yee.request.form_hash'][$key];
            } else {
                return $default;
            }
        } else {
            return $this->env['yee.request.form_hash'];
        }
    }

    /**
     * Fetch PUT data (alias for \Yee\Http\Request::post)
     * @param  string           $key
     * @param  mixed            $default Default return value when key does not exist
     * @return array|mixed|null
     */
    public function put($key = null, $default = null)
    {
        return $this->post($key, $default);
    }

    /**
     * Fetch PATCH data (alias for \Yee\Http\Request::post)
     * @param  string           $key
     * @param  mixed            $default Default return value when key does not exist
     * @return array|mixed|null
     */
    public function patch($key = null, $default = null)
    {
        return $this->post($key, $default);
    }

    /**
     * Fetch DELETE data (alias for \Yee\Http\Request::post)
     * @param  string           $key
     * @param  mixed            $default Default return value when key does not exist
     * @return array|mixed|null
     */
    public function delete($key = null, $default = null)
    {
        return $this->post($key, $default);
    }

    /**
     * Fetch COOKIE data
     *
     * This method returns a key-value array of Cookie data sent in the HTTP request, or
     * the value of a array key if requested; if the array key does not exist, NULL is returned.
     *
     * @param  string            $key
     * @return array|string|null
     */
    public function cookies($key = null)
    {
        if ($key) {
            return $this->cookies->get($key);
        }

        return $this->cookies;
       
    }

    /**
     * Does the Request body contain parsed form data?
     * @return bool
     */
    public function isFormData()
    {
        $method = isset($this->env['yee.method_override.original_method']) ? $this->env['yee.method_override.original_method'] : $this->getMethod();

        return ($method === self::METHOD_POST && is_null($this->getContentType())) || in_array($this->getMediaType(), self::$formDataMediaTypes);
    }

    /**
     * Get Headers
     *
     * This method returns a key-value array of headers sent in the HTTP request, or
     * the value of a hash key if requested; if the array key does not exist, NULL is returned.
     *
     * @param  string $key
     * @param  mixed  $default The default value returned if the requested header is not available
     * @return mixed
     */
    public function headers($key = null, $default = null)
    {
        if ($key) {
            return $this->headers->get($key, $default);
        }

        return $this->headers;
        // if ($key) {
        //     $key = strtoupper($key);
        //     $key = str_replace('-', '_', $key);
        //     $key = preg_replace('@^HTTP_@', '', $key);
        //     if (isset($this->env[$key])) {
        //         return $this->env[$key];
        //     } else {
        //         return $default;
        //     }
        // } else {
        //     $headers = array();
        //     foreach ($this->env as $key => $value) {
        //         if (strpos($key, 'yee.') !== 0) {
        //             $headers[$key] = $value;
        //         }
        //     }
        //
        //     return $headers;
        // }
    }

    /**
     * Get Body
     * @return string
     */
    public function getBody()
    {
        return $this->env['yee.input'];
    }

    /**
     * Get Content Type
     * @return string|null
     */
    public function getContentType()
    {
        return $this->headers->get('CONTENT_TYPE');
    }

    /**
     * Get Media Type (type/subtype within Content Type header)
     * @return string|null
     */
    public function getMediaType()
    {
        $contentType = $this->getContentType();
        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);

            return strtolower($contentTypeParts[0]);
        }

        return null;
    }

    /**
     * Get Media Type Params
     * @return array
     */
    public function getMediaTypeParams()
    {
        $contentType = $this->getContentType();
        $contentTypeParams = array();
        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
            $contentTypePartsLength = count($contentTypeParts);
            for ($i = 1; $i < $contentTypePartsLength; $i++) {
                $paramParts = explode('=', $contentTypeParts[$i]);
                $contentTypeParams[strtolower($paramParts[0])] = $paramParts[1];
            }
        }

        return $contentTypeParams;
    }

    /**
     * Get Content Charset
     * @return string|null
     */
    public function getContentCharset()
    {
        $mediaTypeParams = $this->getMediaTypeParams();
        if (isset($mediaTypeParams['charset'])) {
            return $mediaTypeParams['charset'];
        }

        return null;
    }

    /**
     * Get Content-Length
     * @return int
     */
    public function getContentLength()
    {
        return $this->headers->get('CONTENT_LENGTH', 0);
    }

    /**
     * Get Host
     * @return string
     */
    public function getHost()
    {
        if (isset($this->env['HTTP_HOST'])) {
            if (strpos($this->env['HTTP_HOST'], ':') !== false) {
                $hostParts = explode(':', $this->env['HTTP_HOST']);

                return $hostParts[0];
            }

            return $this->env['HTTP_HOST'];
        }

        return $this->env['SERVER_NAME'];
    }

    /**
     * Get Host with Port
     * @return string
     */
    public function getHostWithPort()
    {
        return sprintf('%s:%s', $this->getHost(), $this->getPort());
    }

    /**
     * Get Port
     * @return int
     */
    public function getPort()
    {
        return (int)$this->env['SERVER_PORT'];
    }

    /**
     * Get Scheme (https or http)
     * @return string
     */
    public function getScheme()
    {
        return $this->env['yee.url_scheme'];
    }

    /**
     * Get Script Name (physical path)
     * @return string
     */
    public function getScriptName()
    {
        return $this->env['SCRIPT_NAME'];
    }

    /**
     * LEGACY: Get Root URI (alias for Yee_Http_Request::getScriptName)
     * @return string
     */
    public function getRootUri()
    {
        return $this->getScriptName();
    }

    /**
     * Get Path (physical path + virtual path)
     * @return string
     */
    public function getPath()
    {
        return $this->getScriptName() . $this->getPathInfo();
    }

    /**
     * Get Path Info (virtual path)
     * @return string
     */
    public function getPathInfo()
    {
        return $this->env['PATH_INFO'];
    }

    /**
     * LEGACY: Get Resource URI (alias for Yee_Http_Request::getPathInfo)
     * @return string
     */
    public function getResourceUri()
    {
        return $this->getPathInfo();
    }

    /**
     * Get URL (scheme + host [ + port if non-standard ])
     * @return string
     */
    public function getUrl()
    {
        $url = $this->getScheme() . '://' . $this->getHost();
        if (($this->getScheme() === 'https' && $this->getPort() !== 443) || ($this->getScheme() === 'http' && $this->getPort() !== 80)) {
            $url .= sprintf(':%s', $this->getPort());
        }

        return $url;
    }

    /**
     * Get IP
     * @return string
     */
    public function getIp()
    {
        $keys = array('X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'REMOTE_ADDR');
        foreach ($keys as $key) {
            if (isset($this->env[$key])) {
                return $this->env[$key];
            }
        }

        return $this->env['REMOTE_ADDR'];
    }

    /**
     * Get Referrer
     * @return string|null
     */
    public function getReferrer()
    {
        return $this->headers->get('HTTP_REFERER');
    }

    /**
     * Get Referer (for those who can't spell)
     * @return string|null
     */
    public function getReferer()
    {
        return $this->getReferrer();
    }

    /**
     * Get User Agent
     * @return string|null
     */
    public function getUserAgent()
    {
        return $this->headers->get('HTTP_USER_AGENT');
    }
}
