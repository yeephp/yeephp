<?php
/**
 * Yee - a tiny PHP 5 framework
 *
 * @author      Andreas Maier <info@yeephp.com>
 * @copyright   2011 Andreas Maier
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
namespace Yee\Middleware;

 /**
  * HTTP Method Override
  *
  * This is middleware for a Yee application that allows traditional
  * desktop browsers to submit pseudo PUT and DELETE requests by relying
  * on a pre-determined request parameter. Without this middleware,
  * desktop browsers are only able to submit GET and POST requests.
  *
  * This middleware is included automatically!
  *
  * @package    Yee
  * @author     Andreas Maier
  * @since      1.0.0
  */
class MethodOverride extends \Yee\Middleware
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     * @param  array  $settings
     */
    public function __construct($settings = array())
    {
        $this->settings = array_merge(array('key' => '_METHOD'), $settings);
    }

    /**
     * Call
     *
     * Implements Yee middleware interface. This method is invoked and passed
     * an array of environment variables. This middleware inspects the environment
     * variables for the HTTP method override parameter; if found, this middleware
     * modifies the environment settings so downstream middleware and/or the Yee
     * application will treat the request with the desired HTTP method.
     *
     * @return array[status, header, body]
     */
    public function call()
    {
        $env = $this->app->environment();
        if (isset($env['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            // Header commonly used by Backbone.js and others
            $env['yee.method_override.original_method'] = $env['REQUEST_METHOD'];
            $env['REQUEST_METHOD'] = strtoupper($env['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } elseif (isset($env['REQUEST_METHOD']) && $env['REQUEST_METHOD'] === 'POST') {
            // HTML Form Override
            $req = new \Yee\Http\Request($env);
            $method = $req->post($this->settings['key']);
            if ($method) {
                $env['yee.method_override.original_method'] = $env['REQUEST_METHOD'];
                $env['REQUEST_METHOD'] = strtoupper($method);
            }
        }
        $this->next->call();
    }
}
