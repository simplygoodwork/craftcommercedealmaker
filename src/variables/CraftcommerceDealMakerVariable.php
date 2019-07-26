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

	public function get($debug = false) {

		// Get settings
		$result = [];

		$upsellAt = CraftcommerceDealMaker::$plugin->getSettings()->upsellAt ?: 2;

		$upsellAtPercentage = CraftcommerceDealMaker::$plugin->getSettings()->upsellAtPercentage ?: .5;

		$order = Plugin::getInstance()->getCarts()->getCart();

		// Find any associated discounts
		foreach ($this->discounts as $discount) {

			// Get IDS and check if they're in the discount
			$lineitemIDs = $this->getLineItemIds($order->getLineItems());

			$discountLineItemIDs = $this->getDiscountedLineItemIds($discount);

			// This is what you get for rushing
			$found = false;

			foreach ($lineitemIDs as $li) {
				if(in_array($li, $discountLineItemIDs)) $found = true;
			}

			if($found) {

				// Set vars
				$items = [];

				$lowestPrice = 1234567890;

				$lowestLineItem = null;

				$quantity = 0;

				// Loop through line items and get quantity
				foreach ($order->getLineItems() as $lineitem) {

					if(in_array($lineitem->purchasableId, $lineitemIDs) && in_array($lineitem->purchasableId, $discountLineItemIDs)) {

						$items[] = $lineitem;

						$quantity += $lineitem->qty;

						if($lineitem->price < $lowestPrice) {

							$lowestPrice = $lineitem->price;

							$lowestLineItem = $lineitem;

						}

					}

				}

				// Figure out if we should apply this one
				if(
					$quantity < $discount->purchaseQty
					&& (
					 	$quantity > ($discount->purchaseQty - $upsellAt)
					 	|| $quantity > ($discount->purchaseQty * $upsellAtPercentage)
					)
				) {

					$dealItem = array(
						'lineitem'			=> $lowestLineItem,
						'cost'				=> $lowestLineItem->price,
						'name'				=> $lowestLineItem->getPurchasable()->title,
						'discount' 			=> $discount,
						'current_quantity'	=> $quantity,
						'deal_quantity'		=> $discount->purchaseQty,
						'available'			=> $items
					);

					if($debug) {
						$dealItem['debug'] = array(
							'lineitemIDs' => $lineitemIDs,
							'discountLineItemIDs' => $discountLineItemIDs
						);
					}

					$result[] = $dealItem;

				}

			}

		}

		return $result;

	}

	private function getLineItemIds($lineitems) {

		$output = array();

		foreach ($lineitems as $lineitem) {
			$output[] = $lineitem->purchasableId;
		}

		return $output;

	}

	private function getDiscountedLineItemIds($discount) {

		return $discount->getPurchasableIds();

	}

	public function dd() {
		$args = func_get_args();

		foreach ($args as $a) {
			var_dump('<pre>' . print_r($a, TRUE) . '</pre>');
		}

		die();
	}

}