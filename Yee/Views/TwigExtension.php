<?php
/**
 * Yee - a tiny PHP 5 framework
 *
 * @author      Andreas Maier
 * @link        http://www.yeephp.com
 * @copyright   2014 Andreas Maier
 * @version     1.0.0
 * @package     YeeViews
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
namespace Yee\Views;

use Yee\Yee;

class TwigExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'yee';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('urlFor', array($this, 'urlFor')),
            new \Twig_SimpleFunction('baseUrl', array($this, 'base')),
            new \Twig_SimpleFunction('siteUrl', array($this, 'site')),
        );
    }

    public function urlFor($name, $params = array(), $appName = 'default')
    {
        return Yee::getInstance($appName)->urlFor($name, $params);
    }

    public function site($url, $withUri = true, $appName = 'default')
    {
        return $this->base($withUri, $appName) . '/' . ltrim($url, '/');
    }

    public function base($withUri = true, $appName = 'default')
    {
        $req = Yee::getInstance($appName)->request();
        $uri = $req->getUrl();

        if ($withUri) {
            $uri .= $req->getRootUri();
        }
        return $uri;
    }
}
