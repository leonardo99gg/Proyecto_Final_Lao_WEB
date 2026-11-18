<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-dark text-white rounded-top-4 py-3">
                    <h4 class="mb-0"><i class="bi bi-tag-fill me-2"></i><?php echo isset($servicio) ? 'Editar Paquete' : 'Crear Paquete de Servicio'; ?></h4>
                </div>
                <div class="card-body p-4 bg-light">
                    <form action="servicio.php?action=<?php echo isset($servicio) ? 'update&id='.$servicio['id_servicio'] : 'create'; ?>" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-secondary">Nombre del Servicio / Paquete</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo isset($servicio) ? htmlspecialchars($servicio['nombre']) : ''; ?>" placeholder="Ej. Lavado Detallado Exterior" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">Auto ($)</label>
                                <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo isset($servicio) ? $servicio['precio'] : ''; ?>" placeholder="Ej. 150.00" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">Camioneta ($)</label>
                                <input type="number" step="0.01" name="precio_camioneta" class="form-control" value="<?php echo isset($servicio) ? $servicio['precio_camioneta'] : ''; ?>" placeholder="Ej. 200.00" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary">Moto ($)</label>
                                <input type="number" step="0.01" name="precio_moto" class="form-control" value="<?php echo isset($servicio) ? $servicio['precio_moto'] : '55.00'; ?>" placeholder="Ej. 55.00" required>
                            </div>
                        </div>

                        <div class="mb-4 pt-3 border-top">
                            <label class="form-label fw-bold text-secondary">Imagen Promocional (Opcional)</label>
                            <?php if (isset($servicio) && !empty($servicio['imagen_servicio'])): ?>
                                <div class="mb-2">
                                    <img src="../<?php echo htmlspecialchars($servicio['imagen_servicio']); ?>" width="100" class="rounded shadow-sm d-block mb-2">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="imagen_servicio" class="form-control" accept="image/png, image/jpeg, image/webp">
                            <div class="form-text">Esta imagen aparecerá en las tarjetas de tu página principal.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <a href="servicio.php" class="btn btn-outline-danger px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>