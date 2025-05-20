<?php
/* @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile

/**
 * A helper file for Laravel, to provide autocomplete information to your IDE
 * Generated for Laravel 12.11.0.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */
namespace Elibyy\TCPDF\Facades {
    /**
     * 
     *
     * @method static \TCPDF AddPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false)
     * @method static \TCPDF AddSpotColor($name, $c, $m, $y, $k)
     * @method static \TCPDF AddSpotColorHtml($name, $c, $m, $y, $k)
     * @method static \TCPDF AddTTFFont($fontfamily, $fontstyle, $filename, $subset = 'default', $enc = '', $embed = true)
     * @method static \TCPDF SetTitle($title, $isUTF8 = false)
     * @method static \TCPDF SetSubject($subject, $isUTF8 = false)
     * @method static \TCPDF SetAuthor($author, $isUTF8 = false)
     * @method static \TCPDF SetKeywords($keywords, $isUTF8 = false)
     * @method static \TCPDF SetHeaderData($ln = '', $lw = 0, $ht = '', $hs = '', $tc = array(0, 0, 0), $lc = array(0, 0, 0))
     * @method static \TCPDF SetCreator($creator, $isUTF8 = false)
     * @method static \TCPDF writeHTML($html, $ln = true, $fill = false, $reseth = false, $cell = false, $align = '')
     * @method static \TCPDF Write($h, $txt, $link = '')
     * @method static \TCPDF Output($name = '', $dest = '')
     * @method static \TCPDF SetAutoPageBreak($auto, $margin = 0)
     * @method static \TCPDF SetMargins($left, $top, $right = -1, $keepmargins = false)
     * @method static \TCPDF SetProtection($permissions = array(), $user_pass = '', $owner_pass = null, $mode = 0, $pubkeys = null)
     * @method static \TCPDF SetPageOrientation($orientation)
     * @method static \TCPDF SetPageFormat($format, $orientation = '')
     * @method static \TCPDF SetImageScale($scale)
     * @method static \TCPDF Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '')
     * @method static \TCPDF MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
     * @method static \TCPDF Ln($h = null)
     * @mixin \TCPDF
     */
    class TCPDF {
        /**
         * 
         *
         * @static 
         */
        public static function reset()
        {
            /** @var \Elibyy\TCPDF\TCPDF $instance */
            return $instance->reset();
        }

        /**
         * 
         *
         * @static 
         */
        public static function changeFormat($format)
        {
            return \Elibyy\TCPDF\TCPDF::changeFormat($format);
        }

        /**
         * 
         *
         * @static 
         */
        public static function setHeaderCallback($headerCallback)
        {
            /** @var \Elibyy\TCPDF\TCPDF $instance */
            return $instance->setHeaderCallback($headerCallback);
        }

        /**
         * 
         *
         * @static 
         */
        public static function setFooterCallback($footerCallback)
        {
            /** @var \Elibyy\TCPDF\TCPDF $instance */
            return $instance->setFooterCallback($footerCallback);
        }

            }
    }

namespace Barryvdh\DomPDF\Facade {
    /**
     * 
     *
     * @method static \TCPDF AddPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false)
     * @method static \TCPDF AddSpotColor($name, $c, $m, $y, $k)
     * @method static \TCPDF AddSpotColorHtml($name, $c, $m, $y, $k)
     * @method static \TCPDF AddTTFFont($fontfamily, $fontstyle, $filename, $subset = 'default', $enc = '', $embed = true)
     * @method static \TCPDF SetTitle($title, $isUTF8 = false)
     * @method static \TCPDF SetSubject($subject, $isUTF8 = false)
     * @method static \TCPDF SetAuthor($author, $isUTF8 = false)
     * @method static \TCPDF SetKeywords($keywords, $isUTF8 = false)
     * @method static \TCPDF SetHeaderData($ln = '', $lw = 0, $ht = '', $hs = '', $tc = array(0, 0, 0), $lc = array(0, 0, 0))
     * @method static \TCPDF SetCreator($creator, $isUTF8 = false)
     * @method static \TCPDF writeHTML($html, $ln = true, $fill = false, $reseth = false, $cell = false, $align = '')
     * @method static \TCPDF Write($h, $txt, $link = '')
     * @method static \TCPDF Output($name = '', $dest = '')
     * @method static \TCPDF reset()
     * @method static \TCPDF SetAutoPageBreak($auto, $margin = 0)
     * @method static \TCPDF SetMargins($left, $top, $right = -1, $keepmargins = false)
     * @method static \TCPDF SetProtection($permissions = array(), $user_pass = '', $owner_pass = null, $mode = 0, $pubkeys = null)
     * @method static \TCPDF SetPageOrientation($orientation)
     * @method static \TCPDF SetPageFormat($format, $orientation = '')
     * @method static \TCPDF SetImageScale($scale)
     * @method static \TCPDF Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '')
     * @method static \TCPDF MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
     * @method static \TCPDF Ln($h = null)
     * @mixin \TCPDF
     */
    class Pdf {
        /**
         * Get the DomPDF instance
         *
         * @static 
         */
        public static function getDomPDF()
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->getDomPDF();
        }

        /**
         * Show or hide warnings
         *
         * @static 
         */
        public static function setWarnings($warnings)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->setWarnings($warnings);
        }

        /**
         * Load a HTML string
         *
         * @param string|null $encoding Not used yet
         * @static 
         */
        public static function loadHTML($string, $encoding = null)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->loadHTML($string, $encoding);
        }

        /**
         * Load a HTML file
         *
         * @static 
         */
        public static function loadFile($file)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->loadFile($file);
        }

        /**
         * Add metadata info
         *
         * @param array<string, string> $info
         * @static 
         */
        public static function addInfo($info)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->addInfo($info);
        }

        /**
         * Load a View and convert to HTML
         *
         * @param array<string, mixed> $data
         * @param array<string, mixed> $mergeData
         * @param string|null $encoding Not used yet
         * @static 
         */
        public static function loadView($view, $data = [], $mergeData = [], $encoding = null)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->loadView($view, $data, $mergeData, $encoding);
        }

        /**
         * Set/Change an option (or array of options) in Dompdf
         *
         * @param array<string, mixed>|string $attribute
         * @param null|mixed $value
         * @static 
         */
        public static function setOption($attribute, $value = null)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->setOption($attribute, $value);
        }

        /**
         * Replace all the Options from DomPDF
         *
         * @param array<string, mixed> $options
         * @static 
         */
        public static function setOptions($options, $mergeWithDefaults = false)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->setOptions($options, $mergeWithDefaults);
        }

        /**
         * Output the PDF as a string.
         * 
         * The options parameter controls the output. Accepted options are:
         * 
         * 'compress' = > 1 or 0 - apply content stream compression, this is
         *    on (1) by default
         *
         * @param array<string, int> $options
         * @return string The rendered PDF as string
         * @static 
         */
        public static function output($options = [])
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->output($options);
        }

        /**
         * Save the PDF to a file
         *
         * @static 
         */
        public static function save($filename, $disk = null)
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->save($filename, $disk);
        }

        /**
         * Make the PDF downloadable by the user
         *
         * @static 
         */
        public static function download($filename = 'document.pdf')
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->download($filename);
        }

        /**
         * Return a response with the PDF to show in the browser
         *
         * @static 
         */
        public static function stream($filename = 'document.pdf')
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->stream($filename);
        }

        /**
         * Render the PDF
         *
         * @static 
         */
        public static function render()
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->render();
        }

        /**
         * 
         *
         * @param array<string> $pc
         * @static 
         */
        public static function setEncryption($password, $ownerpassword = '', $pc = [])
        {
            /** @var \Barryvdh\DomPDF\PDF $instance */
            return $instance->setEncryption($password, $ownerpassword, $pc);
        }

            }
    }

namespace Illuminate\Http {
    /**
     * 
     *
     */
    class Request {
        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param array $rules
         * @param mixed $params
         * @static 
         */
        public static function validate($rules, ...$params)
        {
            return \Illuminate\Http\Request::validate($rules, ...$params);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
         * @param string $errorBag
         * @param array $rules
         * @param mixed $params
         * @static 
         */
        public static function validateWithBag($errorBag, $rules, ...$params)
        {
            return \Illuminate\Http\Request::validateWithBag($errorBag, $rules, ...$params);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $absolute
         * @static 
         */
        public static function hasValidSignature($absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignature($absolute);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @static 
         */
        public static function hasValidRelativeSignature()
        {
            return \Illuminate\Http\Request::hasValidRelativeSignature();
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @param mixed $absolute
         * @static 
         */
        public static function hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
        {
            return \Illuminate\Http\Request::hasValidSignatureWhileIgnoring($ignoreQuery, $absolute);
        }

        /**
         * 
         *
         * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestSignatureValidation()
         * @param mixed $ignoreQuery
         * @static 
         */
        public static function hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
        {
            return \Illuminate\Http\Request::hasValidRelativeSignatureWhileIgnoring($ignoreQuery);
        }

        /**
         * 
         *
         * @see \Inertia\ServiceProvider::registerRequestMacro()
         * @static 
         */
        public static function inertia()
        {
            return \Illuminate\Http\Request::inertia();
        }

            }
    }

namespace Illuminate\Routing {
    /**
     * 
     *
     * @mixin \Illuminate\Routing\RouteRegistrar
     */
    class Router {
        /**
         * 
         *
         * @see \Inertia\ServiceProvider::registerRouterMacro()
         * @param mixed $uri
         * @param mixed $component
         * @param mixed $props
         * @static 
         */
        public static function inertia($uri, $component, $props = [])
        {
            return \Illuminate\Routing\Router::inertia($uri, $component, $props);
        }

            }
    /**
     * 
     *
     */
    class Route {
        /**
         * 
         *
         * @see \Spatie\Permission\PermissionServiceProvider::registerMacroHelpers()
         * @param mixed $roles
         * @static 
         */
        public static function role($roles = [])
        {
            return \Illuminate\Routing\Route::role($roles);
        }

        /**
         * 
         *
         * @see \Spatie\Permission\PermissionServiceProvider::registerMacroHelpers()
         * @param mixed $permissions
         * @static 
         */
        public static function permission($permissions = [])
        {
            return \Illuminate\Routing\Route::permission($permissions);
        }

            }
    }

namespace Illuminate\Testing {
    /**
     * 
     *
     * @template TResponse of \Symfony\Component\HttpFoundation\Response
     * @mixin \Illuminate\Http\Response
     */
    class TestResponse {
        /**
         * 
         *
         * @see \Inertia\Testing\TestResponseMacros::assertInertia()
         * @param \Closure|null $callback
         * @static 
         */
        public static function assertInertia($callback = null)
        {
            return \Illuminate\Testing\TestResponse::assertInertia($callback);
        }

        /**
         * 
         *
         * @see \Inertia\Testing\TestResponseMacros::inertiaPage()
         * @static 
         */
        public static function inertiaPage()
        {
            return \Illuminate\Testing\TestResponse::inertiaPage();
        }

            }
    }


namespace  {
    class PDF extends \Elibyy\TCPDF\Facades\TCPDF {}
    class Pdf extends \Barryvdh\DomPDF\Facade\Pdf {}
}





