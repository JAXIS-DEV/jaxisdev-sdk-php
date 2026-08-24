# jaxisdev/sdk-php - Official PHP SDK

![Packagist Version](https://img.shields.io/packagist/v/jaxisdev/sdk-php)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-777bb4)
![License](https://img.shields.io/packagist/l/jaxisdev/sdk-php)

El **Jaxis SDK** oficial para PHP. Misma API y comportamiento que [`@jaxisdev/sdk`](https://www.npmjs.com/package/@jaxisdev/sdk) (Node.js), adaptado a PHP: integración con SMS Gateway, WhatsApp API y Billing (próximamente).

Construido para aplicaciones de alto rendimiento con negociación automática de JWT y un cliente HTTP basado en Guzzle.

## 🚀 Features

- **Autenticación invisible**: pasa un token de servicio por tenant o tus credenciales de Cognito, y el SDK gestiona la inyección y rotación del token automáticamente.
- **SMS Gateway SDK**: envío instantáneo de SMS con tracking.
- **WhatsApp API**: acceso directo a la infraestructura de mensajería de negocio de WhatsApp.
- **Billing & Account Flow**: *(Próximamente)* integración nativa con Stripe y los módulos de facturación de Jaxis.

---

## 📦 Instalación

```bash
composer require jaxisdev/sdk-php
```

Requiere PHP 8.1 o superior.

---

## ⚡ Quick Start

```php
<?php

require 'vendor/autoload.php';

use Jaxis\Jaxis;

// 1. Inicializa el SDK
// Recomendado: un token de servicio por tenant emitido desde el panel admin v2,
// independiente de cualquier login de usuario -- pensado para integraciones
// servidor-a-servidor.
$jaxis = new Jaxis([
    'serviceToken' => 'jaxis_sk_live_xxxxxx',
    // Alternativa: login humano via Cognito (panel admin / scripts):
    // 'auth' => ['username' => 'hello@company.com', 'password' => 'super-secret-password'],
]);

// 2. Envía un SMS
try {
    $response = $jaxis->sms->send([
        // Puede ser un string o un array: ['+1234567', '+9876543']
        'to' => '+1234567890',
        'text' => 'Welcome to Jaxis!',
    ]);
    echo 'Message subid: ' . $response['subid'];
} catch (\Throwable $e) {
    echo 'Failed to send SMS: ' . $e->getMessage();
}

// 3. Envía un mensaje de WhatsApp (templateId desde jaxis.templates -- propio o genérico)
$templateResponse = $jaxis->whatsapp->send([
    'to' => '+1234567890',
    'templateId' => 'tmpl_abc123',
    'params' => ['nombre' => 'Ana'],
]);
echo 'Message ID: ' . $templateResponse['id'];
```

---

## 🔧 Compatibilidad

| Entorno | Soportado |
| --- | --- |
| PHP 8.1 | ✅ |
| PHP 8.2 | ✅ |
| PHP 8.3 | ✅ |

Depende de [`guzzlehttp/guzzle`](https://github.com/guzzle/guzzle) para las peticiones HTTP.

---

## 🛠️ API Reference

### `auth`

Gestionado internamente. También puedes disparar logins explícitos con `$jaxis->auth->signIn($username, $password)`.

### `sms`

- `$jaxis->sms->send(['to' => ..., 'text' => ...])`: envía un SMS saliente.

### `whatsapp`

- `$jaxis->whatsapp->send(['to' => ..., 'templateId' => ..., 'params' => [...]])`: envía una plantilla de WhatsApp a un destinatario.
- `$jaxis->whatsapp->sendBatch(['destinations' => [...], 'templateId' => ..., ...])`: igual, para múltiples `destinations` en una sola llamada.

---

## 📝 License

MIT License. Ver [LICENSE](LICENSE) para más detalles.
