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
{% set f = craft.craftcommercedealmaker.get() %}
{% if f %}
  {% for deal in f %}
    You ordered {{ deal.current_quantity }} of {{ deal.lineitem.getPurchasable().title }}! You can get {{ deal.discount.description }} if you order {{ deal.deal_quantity - deal.current_quantity }} more!
  {% endfor %}
{% endif %}
```

This displays a standard object with four params
**deal.lineitem**: The CraftCommerce LineItem associated with the deal
**deal.discount**: The CraftCommerce Discount object
**deal.current_quantity**: The current quantity of items in the Order object
**deal.deal_quantity**: The amount needed to get the deal

## Contributing

This is super early stage dev on this. Every PR is welcome.