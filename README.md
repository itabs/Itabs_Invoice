Invoice
=====================
This extension allows shop owners to provide the payment method "Invoice" to their customers.

Facts
-----
- version: 1.2.0
- [extension on GitHub](https://github.com/itabs/Itabs_Invoice)

Description
-----------
This extension allows shop owners to provide the payment method "Invoice" to their customers.
This includes:
- Complete order via Invoice
- Generate invoice after order complete
- Notfiy customer about invoice
- Several validation rules to check if invoice payment is allowed for the customer

Validation rules are:
- Check if the customer is in a specified customer group
- Check if the customer has a specified number of "complete" orders
- Check if the customer orders reached a specified minimum order amount
- Check if the customer has invoices with the invoice state "open"

Requirements
------------
- PHP >= 5.3.0

Compatibility
-------------
- Magento >= 1.6
- Versions below should work down to version 1.4 without any problems but it is not actively tested.

Installation Instructions
-------------------------
1. Install the extension via Magento Connect with the key shown above or copy all the files into your document root.
2. Clear the cache, logout from the admin panel and then login again.
3. You can now enable the payment method via *System -> Configuration -> Sales -> Payment -> Invoice*

Uninstallation
--------------
To uninstall this extension you have to remove all extension files from your file system.

Support & Feature-Wishes
------------------------
If you have any issues or you are missing an feature with this extension, please open an issue on [GitHub](https://github.com/itabs/Itabs_Invoice/issues). Thank you.

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Rouven Alexander Rieker
- Website: [http://rouven-rieker.com](http://rouven-rieker.com)
- Twitter: [@therouv](https://twitter.com/therouv)

Licence
-------
[Open Software License (OSL 3.0)](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2013 ITABS GmbH / Rouven Alexander Rieker
