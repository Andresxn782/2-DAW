# Definiciones

Un sistema informático casi nunca vive solo: necesita hablar con otros sistemas. En Velnora, por ejemplo, la web de reservas, la app del móvil y el torno de la entrada tienen que leer los mismos socios.

## API

**API** significa *Application Programming Interface*: la interfaz que un programa ofrece a otros programas.

- Comunica dos sistemas que pueden ser completamente diferentes (otro lenguaje, otro sistema operativo, otra empresa).
- Es un **contrato**: las dos partes acuerdan cómo se pasan la información.
  - El software A (el servidor) se compromete a responder en un formato.
  - El software B (el cliente) se compromete a enviar y a interpretar ese formato.
- Si el contrato no cambia, cada parte puede cambiar por dentro todo lo que quiera.

## Servicio web

Un **servicio web** es una API a la que se llega por la red usando protocolos web (HTTP o HTTPS). No devuelve una página para personas, sino datos para otro programa.

## Protocolos y tecnologías

| Pieza | Para qué sirve |
|---|---|
| HTTP / HTTPS | El transporte. HTTPS cifra la comunicación. |
| URL | Dice qué recurso pido: `/api/socios/3`. |
| Método (verbo) | Dice qué quiero hacer: `GET` leer, `POST` crear, `PUT` modificar, `DELETE` borrar. |
| Cabeceras | Información extra: `Content-Type`, `Accept`, `X-API-Key`… |
| Código de estado | Cómo ha ido: `200` bien, `201` creado, `400` petición mal hecha, `401` sin permiso, `404` no existe, `500` error del servidor. |
| JSON | Formato de texto ligero, el más usado hoy. |
| XML + XSD | Formato más antiguo y estricto; el XSD es el esquema que valida el XML. |

## Arquitecturas

- **REST**: cada cosa es un recurso con su URL y se usan los verbos de HTTP. Normalmente responde en JSON. Es lo que hago en esta unidad.
- **SOAP**: todo va dentro de un sobre XML y se describe con un archivo WSDL. Se sigue viendo en bancos y administraciones.
- **GraphQL**: el cliente pide exactamente los campos que quiere en una sola URL.

## Ventajas de los servicios web

- Son independientes del lenguaje: mi API en PHP la puede usar una app en Kotlin o un script en Python.
- Reutilizan el código: la lógica está una sola vez en el servidor.
- Usan HTTP, que pasa por cualquier cortafuegos y ya está en todas partes.
- Separan el servidor del cliente, así que puedo cambiar la web sin tocar la API.
