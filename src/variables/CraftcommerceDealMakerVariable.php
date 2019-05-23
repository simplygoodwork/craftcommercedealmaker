<?php
/**
 * CraftCommerce Deal Maker plugin for Craft CMS 3.x
 *
 * Checks if you are close a deal and tries to upsell ya
 *
 * @link      primitivesocial.com
 * @copyright Copyright (c) 2019 Primitive Social
 */

namespace primitivesocial\craftcommercedealmaker\variables;

use primitivesocial\craftcommercedealmaker\CraftcommerceDealMaker;
use primitivesocial\craftcommercedealmaker\services\CraftcommerceDealMakerService;

use Craft;
use craft\commerce\Plugin;
use craft\commerce\services\Discounts;
use craft\commerce\models\LineItem;
use craft\commerce\elements\Order;
use craft\commerce\services\Carts;

class CraftcommerceDealMakerVariable
{

	private $discounts;

	public function __construct() {
		$this->discounts = Plugin::getInstance()->getDiscounts()->getAllDiscounts();
	}

	public function get() {

		// Get settings
		$result = [];

		$upsellAt = CraftcommerceDealMaker::$plugin->getSettings()->upsellAt ?: 2;

		$upsellAtPercentage = CraftcommerceDealMaker::$plugin->getSettings()->upsellAtPercentage ?: .5;

		$order = Plugin::getInstance()->getCarts()->getCart();

		$lineitems = $order->getLineItems();

		foreach ($lineitems as $lineitem) {

			// Find any associated discounts
			foreach ($this->discounts as $discount) {

				// Get purchaseable IDs
				$ids = $discount->getPurchasableIds();

				$available = [];

				$lowestPrice = 1234567890;
			
				foreach ($ids as $id) {

					// If discount exists, and is within upsell threshold				
					if(
						$id == $lineitem->purchasableId
						&& $lineitem->qty < $discount->purchaseQty
						&& (
						 	$discount->purchaseQty - $upsellAt <= $lineitem->qty
						 	|| ($discount->purchaseQty * $upsellAtPercentage) <= $lineitem->qty
						)
					) {

						if(!is_array($available)) $available = array();

						$available[] = array(
							'lineitem'			=> $lineitem,
							'cost'				=> $lineitem->price,
							'name'				=> $lineitem->getPurchasable()->title,
							'discount' 			=> $discount,
							'current_quantity'	=> $lineitem->qty,
							'deal_quantity'		=> $discount->purchaseQty,
						);

						if($lineitem->price < $lowestPrice) {
							$lowestPrice = $lineitem->price;
						}

					}

				}

				$filtered = array_filter($available, function($a) use ($lowestPrice) {
					return $a['cost'] <= $lowestPrice;
				});

				$result = array_merge($result, $filtered);

			}

		}

		return $result;

	}

	public function dd() {
		$args = func_get_args();

		foreach ($args as $a) {
			var_dump('<pre>' . print_r($a, TRUE) . '</pre>');
		}

		die();
	}

}