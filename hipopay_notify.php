<?php
include_once('hipopay_sql.php');
require_once('hipopay.php');
date_default_timezone_set('Europe/Istanbul');

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if ($data) {
    $hipopaySettings = $panelDb->table('api_hipopay_settings')
        ->where('is_active', 1)
        ->orderBy('id', 'desc')
        ->first();

    if (!$hipopaySettings) {
        die('Hipopay ayarları bulunamadı!');
    }

    $hipopay = new HipopayIntegration();
    $hipopay->setApiKey($hipopaySettings->api_key);
    $hipopay->setApiSecret($hipopaySettings->api_secret);

    if ($hipopay->getIpnStatusIsSuccess()) {
        $userId = $data['user_id'];
        $username = $data['name'];
        $amount = $data['product']['sku'];
        $transactionId = $data['transaction_id'];

        $user = $accountDb->table('TB_User')
            ->select(['JID'])
            ->where('JID', $userId)
            ->where('StrUserID', $username)
            ->first();

        if (!is_null($user)) {
            $transaction = $panelDb->table('hipopay_transactions')
                ->select(['id'])
                ->where('transaction_id', $transactionId)
                ->first();



            if (is_null($transaction)) {
                $transaction = $panelDb->table('hipopay_transactions')->insert([
                    'transaction_id' => $transactionId,
                    'user_id' => intval($userId),
                    'username' => $username,
                    'amount' => floatval($amount),
                    'commission_type' => intval($hipopaySettings->charge_type),
                    'status' => 'completed',
                    'response_data' => is_array($data) ? json_encode($data) : $data,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                if ($transaction) {
                    if ($hipopaySettings->charge_type == 1) {
                        $result = ChargeTL($username, $userId, $amount);
                    } else {
                        $result = ChargeSilk($userId, $amount);
                    }

                    echo $result ? "OK" : "Yükleme başarısız";
                } else {
                    echo "Log kaydı oluşturulamadı";
                }
            } else {
                echo "OK";
            }
        } else {
            echo "Kullanıcı bulunamadı";
        }
    } else {
        echo "Hash doğrulaması başarısız";
    }
} else {
    echo "Geçersiz istek";
}

function ChargeSilk($jid, $value)
{
    global $accountDb;

    $sorgu = $accountDb->table('SK_Silk')->select(['JID'])->where('JID', $jid)->first();

    if (is_null($sorgu)) {
        $insert = $accountDb->table('SK_Silk')->insert([
            'JID' => $jid,
            'silk_own' => $value,
            'silk_gift' => 0,
            'silk_point' => 0
        ]);

        return $insert ? true : false;
    } else {
        $update = $accountDb->table('SK_Silk')->where('JID', $jid)->increment('silk_own', $value);

        return $update ? true : false;
    }
}

function ChargeTL($username, $jid, $value)
{

    global $accountDb;

    $kontrol = $accountDb->table('TB_User')->select(['credit'])->where('StrUserID', $username)->first();

    if (! is_null($kontrol) && is_null($kontrol->credit)) {
        $eklemeyap = $accountDb->table('TB_User')->where('StrUserID', $username)->update([
            'credit' => $value
        ]);

        return $eklemeyap ? true : false;
    } else {
        $girdiolustur = $accountDb->table('TB_User')->where('StrUserID', $username)->increment('credit', $value);

        return $girdiolustur ? true : false;
    }
}
