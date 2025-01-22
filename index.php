<?php

require_once('../brain/Starting.php');

require_once('hipopay.php');

if (! $User->JID > 0) {
    echo "Bu alanı sadece giriş yapmış kullanıcılarımız görebilir.";
    exit;
} else {
    date_default_timezone_set('Europe/Istanbul');

    $hipopaySettings = $panelDb->table('api_hipopay_settings')
        ->where('is_active', 1)
        ->orderBy('id', 'desc')
        ->first();

    if (!$hipopaySettings) {
        die('Hipopay ayarları bulunamadı!');
    }

    $userID = $User->JID;
    $username = $User->StrUserID;
    $userEmail = $User->Email;

    $hipopay = new HipopayIntegration();

    $hipopay->setApiKey($hipopaySettings->api_key);
    $hipopay->setApiSecret($hipopaySettings->api_secret);
    $hipopay->setUserId($userID);
    $hipopay->setUserEmail($userEmail);
    $hipopay->setUsername($username);

    try {
        $response = $hipopay->createStoreSession();

        if (isset($response['success']) && $response['success']===true) {
            if (isset($response['data']['payment_url'])) {
                header('Location: '.$response['data']['payment_url']);
            } else {
                echo 'Hata: Ödeme URL\'si bulunamadı.';
            }
        } else {
            echo 'Hata: '.(isset($response['message']) ? $response['message'] : 'Bir hata oluştu');
        }


    } catch (Exception $e) {
        echo 'Hata: ' . $e->getMessage();
    }
}