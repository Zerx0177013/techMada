<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="app-wrap">
    <?= view('layouts/sidebar', ['pendingCount' => $pendingCount ?? 0]) ?>
    <div class="main">
        <div class="topbar"><div><div class="topbar-title">Gestion des types de congé</div><div class="topbar-breadcrumb"><a href="<?= site_url('admin') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Types de congé</div></div></div>
        <div class="content">
            <?php if(session()->getFlashdata('success')): ?>
                <div class="flash flash-success"><i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            
            <?php if(session()->getFlashdata('errors')): ?>
                <div class="flash flash-error">
                    <i class="bi bi-exclamation-circle"></i>
                    <ul style="margin: 0; padding-left: 1rem;">
                        <?php foreach(session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php $isEdit = isset($typeToEdit) && $typeToEdit; ?>
            <div class="form-section">
                <h3><i class="bi <?= $isEdit ? 'bi-tag-fill' : 'bi-tags' ?>" style="color:var(--forest);margin-right:6px"></i><?= $isEdit ? 'Éditer le type de congé' : 'Ajouter un type de congé' ?></h3>
                <form action="<?= site_url($isEdit ? 'admin/types-conge/update/' . $typeToEdit['id'] : 'admin/types-conge/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-grid-3" style="margin-bottom:1rem">
                        <div class="f-group"><label class="f-label">Libellé</label><input type="text" class="f-input" name="libelle" placeholder="Ex: Congé Annuel, Maladie..." value="<?= old('libelle', $isEdit ? $typeToEdit['libelle'] : '') ?>" required/></div>
                        <div class="f-group"><label class="f-label">Jours Annuels</label><input type="number" step="0.5" class="f-input" name="joursAnnuels" placeholder="Ex: 30" value="<?= old('joursAnnuels', $isEdit ? $typeToEdit['joursAnnuels'] : '') ?>" required/></div>
                        <div class="f-group">
                            <label class="f-label">Déductible</label>
                            <select class="f-input" name="deductible" required>
                                <option value="1" <?= old('deductible', $isEdit ? $typeToEdit['deductible'] : '1') == '1' ? 'selected' : '' ?>>Oui (Déduit du solde)</option>
                                <option value="0" <?= old('deductible', $isEdit ? $typeToEdit['deductible'] : '1') == '0' ? 'selected' : '' ?>>Non (Ne déduit pas)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-forest"><i class="bi <?= $isEdit ? 'bi-save' : 'bi-plus' ?>"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le type' ?></button>
                        <?php if ($isEdit): ?>
                            <a href="<?= site_url('admin/types-conge') ?>" class="btn-secondary" style="text-decoration:none;">Annuler</a>
                        <?php else: ?>
                            <button type="reset" class="btn-secondary">Réinitialiser</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="data-card">
                <div class="data-card-head">
                    <h3>Tous les types de congé</h3>
                </div>
                <table class="tbl">
                    <thead><tr><th>ID</th><th>Libellé</th><th>Jours Annuels</th><th>Déductible</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach($types as $t): ?>
                        <tr>
                            <td class="td-muted">#<?= esc($t['id']) ?></td>
                            <td style="font-weight: 500; color: var(--ink);"><?= esc($t['libelle']) ?></td>
                            <td><?= esc($t['joursAnnuels']) ?></td>
                            <td>
                                <?php if ($t['deductible']): ?>
                                    <span style="color:var(--forest);font-weight:600;"><i class="bi bi-check2"></i> Oui</span>
                                <?php else: ?>
                                    <span style="color:#6c757d;"><i class="bi bi-x"></i> Non</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns" style="display: flex; gap: 0.5rem; align-items: center;">
                                    <a href="<?= site_url('admin/types-conge/edit/' . $t['id']) ?>" class="btn-sm btn-edit" style="text-decoration: none;"><i class="bi bi-pencil"></i> Éditer</a>
                                    <form action="<?= site_url('admin/types-conge/delete/' . $t['id']) ?>" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type de congé ? Toutes les données associées pourraient être affectées.');" style="margin: 0; padding: 0;">
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
