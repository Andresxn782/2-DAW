# Modelo de datos de Comunidad Velnora

```
socios
	- id
	- nombre
	- apellidos
	- usuario         (único)
	- email           (único)
	- contrasena      (hash de password_hash, nunca la contraseña)
	- plan

publicaciones
	- id
	- id_socio        -> socios.id
	- fecha
	- texto
	- tipo            (entreno, logro, pregunta)

reacciones
	- id
	- id_publicacion  -> publicaciones.id
	- id_socio        -> socios.id
	- tipo            (fuerza, fuego, aplauso)
	- fecha
	- un socio solo puede poner una reacción de cada tipo en cada publicación

seguimientos
	- id_seguidor     -> socios.id
	- id_seguido      -> socios.id
	- fecha
	- nadie se puede seguir a sí mismo
```

Relaciones:

- Un socio escribe muchas publicaciones (1 a N).
- Una publicación recibe muchas reacciones de muchos socios (N a M, con la tabla `reacciones` en medio).
- Los socios se siguen entre sí (N a M de la tabla `socios` consigo misma).

Se diferencia del modelo de clase en que los seguimientos son en una dirección (como en Instagram, no como "amistades") y en que las publicaciones y las reacciones tienen tipo.
