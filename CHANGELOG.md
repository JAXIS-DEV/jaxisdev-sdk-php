# Changelog

## v1.0.0

- Primer release del SDK PHP, puerto de [`@jaxisdev/sdk`](https://www.npmjs.com/package/@jaxisdev/sdk) (Node.js).
- Cliente `Jaxis` con Guzzle: inyección de `Authorization: Bearer` (token de servicio o login Cognito) y refresh automático en 401.
- Módulos `sms` (`send`) y `whatsapp` (`send`, `sendBatch`).
