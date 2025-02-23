<form action="{$flutterwaveUrl}" method="POST">
    <input type="hidden" name="public_key" value="{$publicKey}">
    <input type="hidden" name="amount" value="{$amount}">
    <input type="hidden" name="currency" value="{$currency}">
    <input type="hidden" name="tx_ref" value="{$paymentId}">
    <input type="hidden" name="description" value="{$description}">
    <button type="submit" class="pkp_button">{translate key="plugins.paymethod.flutterwave.payNow"}</button>
</form>
