<?php
// =====================
// 🧾 Vista: Gestión de Facturas (Administrador)
// =====================
// $facturas viene del controlador FacturaControlador::gestionar()
// =====================
?>

<div class="admin-usuarios">
    <h1>🧾 Gestión de Facturas</h1>

    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Paciente</th>
                <th>Fecha</th>
                <th>Monto Total</th>
                <th>Método de Pago</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($facturas)): ?>
                <tr>
                    <td colspan="7" style="text-align:center; color:#777;">
                        ⚠️ No hay facturas registradas
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($facturas as $factura): ?>
                    <tr>
                        <td><?= htmlspecialchars($factura->getIdFactura()) ?></td>
                        <td><?= htmlspecialchars($factura->getPaciente()->getUsuario()->getNombre()) ?></td>
                        <td><?= htmlspecialchars($factura->getFechaEmision()) ?></td>
                        <td>S/ <?= number_format($factura->getMontoTotal(), 2) ?></td>
                        <td><?= htmlspecialchars($factura->getMetodoPago()) ?></td>
                        <td><?= htmlspecialchars($factura->getEstado()) ?></td>
                        <td>
                            <!-- 👁️ Ver Detalle -->
                            <a href="<?= BASE_URL ?>/index.php?accion=verFactura&id=<?= $factura->getIdFactura() ?>" 
                               class="btn-editar">
                                👁️ Ver
                            </a>

                            <!-- 🗑️ Eliminar -->
                            <a href="<?= BASE_URL ?>/index.php?accion=eliminarFactura&id=<?= $factura->getIdFactura() ?>" 
                               class="btn-eliminar"
                               onclick="return confirm('⚠️ ¿Estás seguro que deseas eliminar esta factura?')">
                                🗑️ Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
