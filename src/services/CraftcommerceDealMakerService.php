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
use Commerce\Helpers\CommerceDbHelper;
use craft\commerce\Plugin;
use craft\commerce\services\Discounts;
use craft\commerce\models\LineItem;
use craft\commerce\elements\Order;

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

	protected $discounts;

	public function __construct() {

		$this->discounts = Plugin::getInstance()->getDiscounts()->getAllDiscounts();

	}

	public function getDeals(LineItem $lineitem)
	{

		// Iniital object
		$result = false;
		
		// Check our Plugin's settings for the upsell
		$upsellAt = CraftcommerceDealMaker::$plugin->getSettings()->upsellAt ?: 2;

		// Find any associated discounts
		foreach ($this->discounts as $discount) {

			// Get purchaseable IDs
			$ids = $discount->getPurchasableIds();
		
			foreach ($ids as $id) {

				// If discount exists, and is within upsell threshold				
				if($id == $lineitem->purchasableId && $discount->purchaseQty - $upsellAt <= $lineitem->qty && $lineitem->qty < $discount->purchaseQty) {

					if(!is_array($result)) $result = array();

					$result[] = array(
						'lineitem'			=> $lineitem,
						'discount' 			=> $discount,
						'current_quantity'	=> $lineitem->qty,
						'deal_quantity'		=> $discount->purchaseQty,
					);

				}

			}

		}
		
		return $result;

	}

}