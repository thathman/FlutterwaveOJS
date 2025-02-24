{strip}
<form id="flutterwaveSettingsForm" class="pkp_form" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT component='plugins.paymethod.flutterwaveOJS.FlutterwaveOjsPlugin' op='manage' action='saveSettings'}">
    {csrf}

    <div class="pkp_form_section">
        <label for="publicKey">{translate key="plugins.paymethod.flutterwave.publicKey"}</label>
        <input type="text" name="publicKey" id="publicKey" value="{$publicKey|escape}" class="pkp_form_input" required>
    </div>

    <div class="pkp_form_section">
        <label for="secretKey">{translate key="plugins.paymethod.flutterwave.secretKey"}</label>
        <input type="password" name="secretKey" id="secretKey" value="{$secretKey|escape}" class="pkp_form_input" required>
    </div>

    <div class="pkp_form_section">
        <label for="encryptionKey">{translate key="plugins.paymethod.flutterwave.encryptionKey"}</label>
        <input type="password" name="encryptionKey" id="encryptionKey" value="{$encryptionKey|escape}" class="pkp_form_input">
    </div>

    <div class="pkp_form_section">
        <label for="testMode">{translate key="plugins.paymethod.flutterwave.enableTestMode"}</label>
        <input type="checkbox" name="testMode" id="testMode" {if $testMode}checked{/if}>
    </div>

    <div class="pkp_form_buttons">
        <button type="submit" class="pkp_button">{translate key="common.save"}</button>
    </div>
</form>
{/strip}
