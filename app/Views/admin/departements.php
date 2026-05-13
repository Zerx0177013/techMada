<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="app-wrap">
    <?= view('layouts/sidebar', ['pendingCount' => $pendingCount ?? 0]) ?>
    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Gestion des départements</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('admin') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Départements</div>
            </div>
        </div>
        <div class="content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash flash-success"><i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="flash flash-error">
                    <i class="bi bi-exclamation-circle"></i>
                    <ul style="margin: 0; padding-left: 1rem;">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php $isEdit = isset($deptToEdit) && $deptToEdit; ?>
            <div class="form-section">
                <h3><i class="bi <?= $isEdit ? 'bi-building-check' : 'bi-building' ?>" style="color:var(--forest);margin-right:6px"></i><?= $isEdit ? 'Éditer le département' : 'Ajouter un département' ?></h3>
                <form action="<?= site_url($isEdit ? 'admin/departements/update/' . $deptToEdit['id'] : 'admin/departements/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-grid-2" style="margin-bottom:1rem">
                        <div class="f-group"><label class="f-label">Nom du département</label><input type="text" class="f-input" name="nom" placeholder="Ex: IT, Finance..." value="<?= old('nom', $isEdit ? $deptToEdit['nom'] : '') ?>" required /></div>
                        <div class="f-group"><label class="f-label">Description</label><input type="text" class="f-input" name="description" placeholder="Courte description" value="<?= old('description', $isEdit ? $deptToEdit['description'] : '') ?>" /></div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-forest"><i class="bi <?= $isEdit ? 'bi-save' : 'bi-plus' ?>"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le département' ?></button>
                        <?php if ($isEdit): ?>
                            <a href="<?= site_url('admin/departements') ?>" class="btn-secondary" style="text-decoration:none;">Annuler</a>
                        <?php else: ?>
                            <button type="reset" class="btn-secondary">Réinitialiser</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="data-card">
                <div class="data-card-head">
                    <h3>Tous les départements</h3>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departements as $dept): ?>
                            <tr>
                                <td class="td-muted">#<?= esc($dept['id']) ?></td>
                                <td style="font-weight: 500; color: var(--ink);"><?= esc($dept['nom']) ?></td>
                                <td class="td-muted"><?= esc($dept['description'] ?? 'Aucune description') ?></td>
                                <td>
                                    <div class="action-btns" style="display: flex; gap: 0.5rem; align-items: center;">
                                        <a href="<?= site_url('admin/departements/edit/' . $dept['id']) ?>" class="btn-sm btn-edit" style="text-decoration: none;"><i class="bi bi-pencil"></i> Éditer</a>
                                        <form action="<?= site_url('admin/departements/delete/' . $dept['id']) ?>" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce département ? Toutes les données associées pourraient être affectées.');" style="margin: 0; padding: 0;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-sm" style="background: transparent; color: #dc3545; border: 1px solid #dc3545; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;"><i class="bi bi-trash"></i> Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
    </div>
</div>
<?= $this->endSection() ?>