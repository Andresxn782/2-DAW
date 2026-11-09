# Guía de diseño · «Tinta y latón»

Rediseño visual de *art | interfaz integrada*. No cambia ni el contenido ni la estructura: solo colores, tipografía, formas y símbolos. Toda la identidad vive en las variables de `:root` en `css/estilo.css`.

## Idea
Una interfaz editorial y tranquila, como un cuaderno de buena calidad: papel cálido, tinta oscura y un solo detalle metálico. Tiene que ser elegante y tener carácter propio sin llamar la atención.

## Color
| Variable | Nombre | Hex | Uso |
|---|---|---|---|
| `--tinta` | Tinta petróleo | `#1b2a2c` | Cabecera, menú, pie y textos. Un negro con un punto de verde azulado: serio, pero más cálido y con más profundidad que el gris. |
| `--laton` | Latón | `#b48c57` | El **único** acento: lo que se pulsa o lo que está activo. Es un dorado envejecido, no brillante; se ve con claridad sin chillar. |
| `--laton-claro` | Pergamino | `#f2eadc` | Foco, selección y zonas de arrastre. |
| `--papel` | Papel hueso | `#f7f5f0` | Fondo de trabajo. Cansa menos la vista que el blanco puro. |
| `--piedra` | Piedra | `#ece8e0` | Columna de vistas. La separa con un cambio de tono, no con una línea. |

Los colores de estado (`--exito`, `--info`, `--aviso`, `--error`) están desaturados para que convivan con la paleta y no se peleen con el latón.

Regla: **el texto sobre latón va en tinta, no en blanco**. Así se lee mejor y el resultado parece más de imprenta.

## Tipografía
- **Fraunces** (títulos, cabecera de tablas y leyendas): una serif contemporánea con curvas suaves. Es lo que da el toque editorial y creativo. La cursiva se reserva para pocos sitios (título de tabla, leyendas de `fieldset`, lema del login).
- **Manrope** (texto e interfaz): una sans geométrica, limpia y muy legible a tamaño pequeño. Equilibra la personalidad de la serif.
- **JetBrains Mono** (código, registro de eventos, `output`, `kbd`): alinea números y datos.
- Las etiquetas de formulario y los encabezados de tabla van en versalitas espaciadas (mayúsculas pequeñas con tracking): ordenan sin pesar.

Las fuentes están incluidas en `/fonts` (licencia SIL OFL), así que funcionan sin conexión.

## Formas
- **Hoja**: `border-radius: 14px 3px` → dos esquinas amplias en diagonal y dos casi rectas. Se aplica a tarjetas, botones, fichas, toasts, el panel y el modal. Es el gesto propio del diseño: se nota al mirar con atención y no antes.
- Los campos de texto tienen esquinas casi rectas (4px) para que se lean como «zonas de escritura».
- Líneas finas de 1px en lugar de bordes gruesos: el filo de latón bajo la cabecera, el separador de columnas y el borde del login.
- Sombras suaves, teñidas de tinta y no de negro puro.

## Símbolos
| Antes | Ahora | Motivo |
|---|---|---|
| ◀ ▶ | ‹ › | Comillas angulares tipográficas: más finas y discretas. |
| ▲ ▼ | ↑ ↓ | Flechas de línea, en la misma familia visual que el texto. |
| × (error) | ✕ | Más geométrico y alineado con ✓. |
| Etiqueta «CREAR» en píldora | — CREAR | Raya y versalitas, como una sección de un libro. |
| Títulos de columna | — MÓDULOS | Pequeño filete de latón delante. |

## Logotipo
La A de art en latón sobre una hoja de tinta petróleo, con un filo interior fino del mismo latón. Repite la forma de hoja y la paleta.
