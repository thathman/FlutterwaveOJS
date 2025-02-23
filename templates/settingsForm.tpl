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
        <label for="liveMode">{translate key="plugins.paymethod.flutterwave.liveMode"}</label>
        <select name="liveMode" id="liveMode" class="pkp_form_input">
            <option value="0" {if $liveMode == 0}selected{/if}>{translate key="plugins.paymethod.flutterwave.sandboxMode"}</option>
            <option value="1" {if $liveMode == 1}selected{/if}>{translate key="plugins.paymethod.flutterwave.liveMode"}</option>
        </select>
    </div>

    <div class="pkp_form_section">
        <button type="submit" class="pkp_button">{translate key="common.save"}</button>
    </div>

    <div class="pkp_form_section">
        <button type="button" id="testApiKeys" class="pkp_button">{translate key="plugins.paymethod.flutterwave.testKeys"}</button>
        <button type="button" id="testWebhook" class="pkp_button">{translate key="plugins.paymethod.flutterwave.testWebhook"}</button>
    </div>

</form>

<script>
    document.getElementById('testApiKeys').addEventListener('click', function() {
        fetch('{url router=$smarty.const.ROUTE_COMPONENT component="plugins.paymethod.flutterwaveOJS.FlutterwaveOjsPlugin" op="manage" action="testKeys"}')
            .then(response => response.json())
            .then(data => alert(data.message))
            .catch(error => alert('Error: ' + error));
    });

    document.getElementById('testWebhook').addEventListener('click', function() {
        fetch('{url router=$smarty.const.ROUTE_COMPONENT component="plugins.paymethod.flutterwaveOJS.FlutterwaveOjsPlugin" op="manage" action="testWebhook"}')
            .then(response => response.json())
            .then(data => alert(data.message))
            .catch(error => alert('Error: ' + error));
    });
</script>
{/strip}
