# Changelog

## Sin publicar (siguiente: v1.1.0)

- `whatsapp->checkNumber($phone)`: valida si un número tiene WhatsApp antes de mandarle una plantilla. Genera un costo por llamada.
- `whatsapp->send`/`whatsapp->sendBatch`: el endpoint interno cambió de
  `/v2/svc/messages/whatsapp/batch` a `/v2/svc/messages/whatsapp` (se
  quitó el sufijo `/batch` en el backend para que WhatsApp siga el mismo
  patrón que `/v2/svc/messages/sms`). No es un cambio para quien consume
  el SDK -- mismos parámetros, misma respuesta, solo cambió la URL
  interna.
- README: se documentó `whatsapp->checkNumber($phone)`, que ya estaba en
  el código (desde `checkNumber`, 04720a9) pero nunca se agregó a la
  tabla de la API Reference.

## v1.0.1

- Sin cambios de código. Release nuevo para forzar el re-crawl de Packagist (el tag `v1.0.0` no había quedado indexado).

## v1.0.0

- Primer release del SDK PHP, puerto de [`@jaxisdev/sdk`](https://www.npmjs.com/package/@jaxisdev/sdk) (Node.js).
- Cliente `Jaxis` con Guzzle: inyección de `Authorization: Bearer` (token de servicio o login Cognito) y refresh automático en 401.
- Módulos `sms` (`send`) y `whatsapp` (`send`, `sendBatch`).
