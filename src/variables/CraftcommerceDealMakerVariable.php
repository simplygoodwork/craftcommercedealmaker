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

		// Find any associated discounts
		foreach ($this->discounts as $discount) {

			// Get IDS and check if they're in the discount
			$lineitemIDs = $this->getLineItemIds($lineitems);
			
			$discountLineItemIDs = $this->getDiscountedLineItemIds($discount);

			if(empty(array_intersect($lineitemIDs, $discountLineItemIDs))) continue;

			// Set vars
			$available = $items = [];

			$lowestPrice = 1234567890;

			$lowestLineItem = null;

			$quantity = 0;

			// Loop through line items and get quantity
			foreach ($lineitems as $lineitem) {

				if(!in_array($lineitem->purchasableId, $lineitemIDs)) continue;

				$items[] = $lineitem;

				$quantity += $lineitem->qty;

				if($lineitem->price < $lowestPrice) {

					$lowestPrice = $lineitem->price;

					$lowestLineItem = $lineitem;

				}

			}

			// Figure out if we should apply this one
			if(
				$quantity < $discount->purchaseQty
				&& (
				 	$discount->purchaseQty - $upsellAt <= $quantity
				 	|| ($discount->purchaseQty * $upsellAtPercentage) <= $quantity
				)
			) {

				if(!is_array($available)) $available = array();

				$dealItem = array(
					'lineitem'			=> $lowestLineItem,
					'cost'				=> $lowestLineItem->price,
					'name'				=> $lowestLineItem->getPurchasable()->title,
					'discount' 			=> $discount,
					'current_quantity'	=> $quantity,
					'deal_quantity'		=> $discount->purchaseQty,
					'available'			=> $items
				);

				$result[] = $dealItem;

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