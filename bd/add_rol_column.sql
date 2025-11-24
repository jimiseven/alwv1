-- Añadir campo rol a tabla vendedores
ALTER TABLE `vendedores` 
ADD COLUMN `rol` enum('admin','vendedor') DEFAULT 'vendedor' AFTER `activo`;

-- Actualizar vendedores existentes a 'vendedor' (por si acaso)
UPDATE `vendedores` SET `rol` = 'vendedor' WHERE `rol` IS NULL;

-- Opcional: Establecer primer admin (descomentar y cambiar ID)
-- UPDATE `vendedores` SET `rol` = 'admin' WHERE `id` = 1;
