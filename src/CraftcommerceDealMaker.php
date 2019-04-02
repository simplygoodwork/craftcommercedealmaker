<?php
/**
 * CraftCommerce Deal Maker plugin for Craft CMS 3.x
 *
 * Checks if you are close a deal and tries to upsell ya
 *
 * @link      primitivesocial.com
 * @copyright Copyright (c) 2019 Primitive Social
 */

namespace primitivesocial\craftcommercedealmaker;

use primitivesocial\craftcommercedealmaker\services\CraftcommerceDealMakerService as CraftcommerceDealMakerService;
use primitivesocial\craftcommercedealmaker\variables\CraftcommerceDealMakerVariable as CraftcommerceDealMakerVariable;
use primitivesocial\craftcommercedealmaker\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use yii\base\Event;
use craft\web\twig\variables\CraftVariable;
use craft\commerce\elements\Order;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Primitive Social
 * @package   CraftcommerceDealMaker
 * @since     1.0.0
 *
 * @property  CraftcommerceDealMakerServiceService $craftcommerceDealMakerService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftcommerceDealMaker extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftcommerceDealMaker::$plugin
     *
     * @var CraftcommerceDealMaker
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * CraftcommerceDealMaker::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {

        parent::init();

        self::$plugin = $this;

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('craftcommercedealmaker', CraftcommerceDealMakerVariable::class);
            }
        );

        Event::on(
            Order::class,
            Order::EVENT_AFTER_ADD_LINE_ITEM,
            function (Event $e) {

                // Get lineitem from event
                $lineitem = $e->lineItem;

                // Get the deals
                $service = new CraftcommerceDealMakerService();

                $deals = $service->getDeals($lineitem);

                // Set variable
                $variable = $event->sender;

                $cdmv = new CraftcommerceDealMakerVariable();

                $cdmv->deals = $deals;

                $variable->set('craftcommercedealmaker', $cdmv);

            }
        );

        Craft::info(
            Craft::t(
                'craft-commerce-deal-maker',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'craft-commerce-deal-maker/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
