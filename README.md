# 🚀 FlutterwaveOJS Plugin (Work In Progress)

## 📌 Description
The **FlutterwaveOJS Plugin** integrates **Flutterwave** with **Open Journal Systems (OJS)**, allowing journals to accept payments for:
- **Article Processing Charges (APC)**
- **Journal Subscriptions**
- **Donations**

> ⚠️ **Note:** This plugin is still a work in progress. The settings form now works, but API key persistence and payment integration remain problematic.

---

## 🎯 Features

### ✅ Implemented Features
- ✅ **Basic Plugin Registration** – The plugin successfully registers on OJS without errors.
- ✅ **Settings Form** – The settings form now loads under **Distribution > Payment**.
- ✅ **Basic Configuration Fields** – Admins can enter API credentials (Public Key, Secret Key, Encryption Key).
- ✅ **Test Mode Toggle** – Users can enable/disable test mode.

### 🏗️ Upcoming Implementations
- 🔧 **Fix API Key Persistence** – Ensure API keys remain stored after saving and refreshing.
- 🔧 **Proper Payment Workflow Integration** – Ensure payments link correctly to OJS orders.
- 🔧 **Webhook Handling** – Implement webhook processing for payment confirmation.
- 🔧 **Error Handling and Logging** – Improve debugging mechanisms for failed transactions.

---

## ✅ What Works
✔️ The plugin successfully registers on OJS.  
✔️ The settings form now appears in **Distribution > Payment**.  
✔️ Basic test mode toggling is available.  

---

## 🛑 Current Issues
❌ **API Keys Do Not Persist** – When the settings form is refreshed, the keys disappear.  
❌ **Defaults to Test Mode** – Even when unchecked, the plugin reverts to test mode.  
❌ **No Payment Processing Yet** – The plugin does not yet properly initiate or handle payments.  
❌ **Webhook Handling Missing** – No verification or logging of transactions.  

---

## 🔜 What’s Next
🔹 **Fix API Key Storage Issue** – Debug why settings are not saving correctly.  
🔹 **Implement Payment Processing** – Develop the payment form and transaction initiation.  
🔹 **Enable Webhook Handling** – Implement transaction verification and status updates.  
🔹 **Improve Debugging & Error Logging** – Ensure errors are logged for troubleshooting.  

---

## 🚀 Future Plans
🔹 **Subscription Support** – Enable recurring payments for journals using a subscription model.  
🔹 **Admin Dashboard Enhancements** – Add transaction history and filtering options.  
🔹 **Currency and Localization Support** – Allow admin selection of preferred currency.  
🔹 **Security Hardening** – Validate webhook requests and store credentials securely.  
🔹 **Compatibility with Future OJS Versions** – Follow best practices to ensure long-term usability.  

---

## 🤝 How You Can Help
This plugin is based on references from the **official PayPal plugin** and the **Malipo plugin** from [OtCloudCompany's GitHub](https://github.com/OtCloudCompany/Malipo). However, some challenges remain.

If you're experienced in:
- **OJS plugin development**
- **Payment gateway integrations**
- **Secure API storage**

Your assistance would be invaluable!  

### 💡 How to Contribute:
- 🔍 **Help debug and fix API key persistence issues**.
- 🛠️ **Assist with proper payment workflow integration**.
- 🔐 **Provide guidance on handling webhooks securely**.
- ⚡ **Improve error handling and debugging logs**.
- 🔄 **Ensure OJS compatibility across versions**.

📌 If you're interested in contributing, fork the repository and submit a **pull request**! 🚀

---

## 🔧 Installation

### 📥 Clone the Repository
```bash
git clone https://github.com/thathman/flutterwaveojs.git
```

### 📦 Install Composer Dependencies
```bash
composer install
```

### 📂 Upload to OJS Installation
Copy the contents of the plugin to:
```
plugins/paymethod/flutterwaveojs
```

### 🏗️ Enable the Plugin in OJS
1. Go to the **OJS Admin Dashboard**  
2. Navigate to **Settings > Website > Plugins**  
3. Enable **FlutterwaveOJS**  
