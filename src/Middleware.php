<?php

namespace DorsetDigital\URLRewriter;

use SilverStripe\Admin\AdminRootController;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\View\HTML;

class Middleware implements HTTPMiddleware
{

    use Injectable;
    use Configurable;

    /**
     * @config
     *
     * Enable rewriting
     * @var bool
     */
    private static $rewrite_enabled = false;

    /**
     * @config
     *
     * Enable rewrite in dev mode
     * @var bool
     */
    private static $enable_in_dev = false;

    /**
     * @config
     *
     * URL to be replaced
     * @var string
     */
    private static $old_url = null;

    /**
     * @config
     *
     * New URL to be used
     * @var string
     */
    private static $new_url = null;


    /**
     * @config
     *
     * Add dns-prefetch links to the html head
     * @var boolean
     */
    private static $add_prefetch = false;

    /**
     * Process the request
     * @param HTTPRequest $request
     * @param $delegate
     * @return
     */
    public function process(HTTPRequest $request, callable $delegate)
    {
        $response = $delegate($request);

        if (($this->canRun() === true) && ($response !== null)) {
            $response->addHeader('X-Rewrites', 'Enabled');
            $oldURL = $this->config()->get('old_url');
            $newURL = $this->config()->get('new_url');

            if ($this->getIsAdmin($request) === false) {
                $body = $response->getBody();
                $body = str_replace($oldURL, $newURL, $body);
                $response->setBody($body);
            }
        }
        return $response;
    }

    private function canRun()
    {
        $confEnabled = $this->config()->get('cdn_rewrite');
        $devEnabled = ((!Director::isDev()) || ($this->config()->get('enable_in_dev')));
        $validConf = (($this->config()->get('old_url') != '') && ($this->config()->get('new_url') != ""));

        return ($confEnabled && $devEnabled && $validConf);
    }


    /**
     * Determine whether the website is being viewed from an admin protected area or not
     * (shamelessly based on https://github.com/silverstripe/silverstripe-subsites)
     *
     * @param HTTPRequest $request
     * @return bool
     */
    private function getIsAdmin(HTTPRequest $request)
    {
        $adminPath = AdminRootController::admin_url();
        $currentPath = rtrim($request->getURL(), '/') . '/';
        if (substr($currentPath, 0, strlen($adminPath)) === $adminPath) {
            return true;
        }
        return false;
    }
}
