<?php
/**
 * WHMCS n8n Provisioning Module
 */
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
function flowm8_MetaData()
{
    return array(
        'DisplayName' => 'n8n Provisioning Module',
        'APIVersion' => '1.1',
        'RequiresServer' => false,
    );
}

function flowm8_ConfigOptions()
{
    return array(
        'n8n Webhook Url' => array(
            'Type' => 'text',
            'Size' => '48',
            'Default' => 'http://localhost:5678/webhook/whmcs',
            'Description' => 'Enter the full webhook url here, including protocol',
        ),
        'n8n Bearer Token' => array(
            'Type' => 'password',
            'Size' => '32',
            'Default' => '',
            'Description' => 'Enter Bearer token here',
        ),
    );
}

function flowm8_CreateAccount(array $params) {
    $response = flowm8_sendHttpRequest($params, 'create');
    if (strpos($response, 'Error') !== false) {
        return $response;
    }
    logModuleCall('flowm8',__FUNCTION__,$params,$response);
    return 'success';
}

function flowm8_SuspendAccount(array $params) {
    $response = flowm8_sendHttpRequest($params, 'suspend');
    if (strpos($response, 'Error') !== false) {
        return $response;
    }
    logModuleCall('flowm8',__FUNCTION__,$params,$response);
    return 'success';
}

function flowm8_UnsuspendAccount(array $params) {
    $response = flowm8_sendHttpRequest($params, 'suspend');
    if (strpos($response, 'Error') !== false) {
        return $response;
    }
    logModuleCall('flowm8',__FUNCTION__,$params,$response);
    return 'success';
}

function flowm8_TerminateAccount(array $params) {
    $response = flowm8_sendHttpRequest($params, 'suspend');
    if (strpos($response, 'Error') !== false) {
        return $response;
    }
    logModuleCall('flowm8',__FUNCTION__,$params,$response);
    return 'success';
}

function flowm8_ChangePassword(array $params) {
    $response = flowm8_sendHttpRequest($params, 'suspend');
    if (strpos($response, 'Error') !== false) {
        return $response;
    }
    logModuleCall('flowm8',__FUNCTION__,$params,$response);
    return 'success';
}

function flowm8_sendHttpRequest(array $params, $action) {
    try {
        logModuleCall('flowm8',__FUNCTION__,$params,$action);
        $url = rtrim($params['configoption1'], '/') . '/' . $action;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $params['configoption2']
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpStatusCode !== 200) {
            throw new Exception("HTTP Error $httpStatusCode: $response");
        }
        curl_close($ch);
        return $response;
    } catch (Exception $e) {
        logModuleCall(
            'flowm8',
            'sendHttpRequest',
            $params,
            $e->getMessage(),
            $e->getTraceAsString(),
            []
        );
        return $e->getMessage();
    }
}
