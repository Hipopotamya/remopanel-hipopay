# Remopanel Hipopay Extension

## Aşama 1: 
- Veritabanı tablolarını oluşturmak için aşağıdaki SQL sorgularını çalıştırınız.
```sql
CREATE TABLE `hipopay_transactions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `transaction_id` varchar(255) NOT NULL,
    `user_id` int(11) NOT NULL,
    `username` varchar(255) NOT NULL,
    `amount` decimal(10,2) NOT NULL,
    `commission_type` tinyint(1) NOT NULL COMMENT '1: TL, 2: Silk',
    `status` varchar(50) NOT NULL,
    `response_data` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `api_hipopay_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `api_key` varchar(255) NOT NULL,
    `api_secret` varchar(255) NOT NULL,
    `charge_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: TL, 2: Silk',
    `is_active` tinyint(1) NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Aşama 2:
- Dosyalar website ana dizinine yüklenmeli.

## Aşama 3:
- Hipopay bayi panelinde mağaza oluşturulur. (Mağaza callback adresine `https://example.com/hipopay/hipopay_notify.php` değeri girilir.)
- Mağazanın API anahtarları website admin panelindeki ayarlar kısmına girilir.
- Hipopay bayi panelinde ürün oluşturulur. (SKU değeri websitenizde teslim edilecek miktarı temsil etmektedir.)

## Aşama 4:
- Ödeme işlemini başlatmak için `https://example.com/hipopay/index.php` adresine yönlendirilir.

## Aşama 5:
- Ödeme onaylandığında ürün otomatik olarak teslim edilir.