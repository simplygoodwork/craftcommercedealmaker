<?php
/**
 * CraftCommerce Deal Maker plugin for Craft CMS 3.x
 *
 * Checks if you are close a deal and tries to upsell ya
 *
 * @link      primitivesocial.com
 * @copyright Copyright (c) 2019 Primitive Social
 */

namespace primitivesocial\craftcommercedealmaker\services;

use primitivesocial\craftcommercedealmaker\CraftcommerceDealMaker;

use Craft;
use craft\base\Component;

/**
 * CraftcommerceDealMakerService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Primitive Social
 * @package   CraftcommerceDealMaker
 * @since     1.0.0
 */
class CraftcommerceDealMakerService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     CraftcommerceDealMaker::$plugin->craftcommerceDealMakerService->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (CraftcommerceDealMaker::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
