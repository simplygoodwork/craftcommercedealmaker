# CraftCommerce Deal Maker plugin for Craft CMS 3.x

Checks if you are close a deal and tries to upsell ya

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require PrimitiveSocial/craft-commerce-deal-maker

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for CraftCommerce Deal Maker.

## CraftCommerce Deal Maker Overview

```
{% if craft.craftcommercedealmaker.deals %}
	{% for deal in craft.craftcommercedealmaker.deals %}
	This displays a standard object with four params
	'deal.lineitem' is the CraftCommerce line item associated with the deal
	'deal.discount' is the CraftCommerce Discount object
	'deal.current_quantity' is the current quantity of items in the Order object
	'deal.deal_quantity' is the amount needed to get the deal
	{% endfor %}
{% endif %}
```