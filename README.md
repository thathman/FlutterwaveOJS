# FlutterwaveOJS Plugin (Work In Progress)

## Description
The **FlutterwaveOJS Plugin** integrates **Flutterwave** with **Open Journal Systems (OJS)**, allowing journals to accept payments for **Article Processing Charges (APC)**, subscriptions, and donations.

> **Note:** This plugin is still a work in progress. As of now, the plugin registers successfully on OJS without errors, but the **settings form** cannot be loaded on the **Distribution > Payment** page.

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/thathman/flutterwaveojs.git
   ```

2. **Install Composer Dependencies**:
   - Navigate to the plugin directory and run:
   ```bash
   composer install
   ```

3. **Upload to your OJS installation**:
   - Copy the contents of the plugin to the `plugins/paymethod` directory of your OJS installation.

4. **Enable the plugin**:
   - Go to the OJS **admin dashboard**.
   - Navigate to **Settings > Website > Plugins** and enable **FlutterwaveOJS**.

## Features

- **Payment Methods Supported**:
  - Cards
  - Bank Transfers
  - USSD
  - Mobile Money
  - Multiple currencies support:
    - USD (United States Dollar)
    - EUR (Euro)
    - GBP (British Pound Sterling)
    - CAD (Canadian Dollar)
    - AUD (Australian Dollar)
    - NGN (Nigerian Naira)
    - KES (Kenyan Shilling)

- **Live/Test Mode**:
  - Can toggle between **Live** and **Test** modes for payment processing.

- **Customizable Settings**:
  - You can configure API keys, live/test mode, etc. (Settings form to be fixed).

## What Works

- The plugin successfully registers on OJS without errors.
- OJS recognizes the plugin and allows it to be enabled through the dashboard.

## What’s Not Working

- The **settings form** is not yet loading on the **Distribution > Payment** page.
- Since the settings form is not functional, **payment methods** have not been tested.

## How You Can Help

I have been using the **official PayPal plugin** and the **Malipo plugin** from [OtCloudCompany's GitHub](https://github.com/OtCloudCompany/Malipo) as references while working on this plugin. However, I need further assistance.

If you're familiar with **OJS plugin development**, or have experience working with **payment gateway plugins**, your assistance would be greatly appreciated.

## Link to Plugin on GitHub

You can view and contribute to the plugin here:
[https://github.com/thathman/flutterwaveojs](https://github.com/thathman/flutterwaveojs)

## Future Plans

- **Fix the settings form**: This is the current roadblock that needs to be addressed for further testing.
- **Payment method testing**: Once the form is functional, testing for payment transactions will be conducted.
- **Full documentation**: Complete the plugin’s documentation and user guides for easier integration with OJS.

## Contact

For inquiries or collaboration, feel free to contact me at [hello@hendrix.com.ng](mailto:hello@hendrix.com.ng).
