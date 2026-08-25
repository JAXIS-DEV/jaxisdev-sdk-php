# Changelog

## Sin publicar

- `whatsapp->checkNumber($phone)`: valida si un número tiene WhatsApp antes de mandarle una plantilla. Requiere que el tenant tenga la revisión de número habilitada, genera un costo por llamada.

## v1.0.1

- Sin cambios de código. Release nuevo para forzar el re-crawl de Packagist (el tag `v1.0.0` no había quedado indexado).

## v1.0.0

- Primer release del SDK PHP, puerto de [`@jaxisdev/sdk`](https://www.npmjs.com/package/@jaxisdev/sdk) (Node.js).
- Cliente `Jaxis` con Guzzle: inyección de `Authorization: Bearer` (token de servicio o login Cognito) y refresh automático en 401.
- Módulos `sms` (`send`) y `whatsapp` (`send`, `sendBatch`).
