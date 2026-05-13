<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="app-wrap">
    <?= view('layouts/sidebar', ['pendingCount' => $pendingCount ?? 0]) ?>
    <div class="main">
        <div class="topbar">
            <div>
                <div class="topbar-title">Gestion des employés</div>
                <div class="topbar-breadcrumb"><a href="<?= site_url('admin') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employés</div>
            </div>
            <div class="topbar-actions"><a href="#" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter</a></div>
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

            <?php $isEdit = isset($employeToEdit) && $employeToEdit; ?>
            <div class="form-section">
                <h3><i class="bi <?= $isEdit ? 'bi-pencil' : 'bi-person-plus' ?>" style="color:var(--forest);margin-right:6px"></i><?= $isEdit ? 'Éditer l\'employé' : 'Ajouter un employé' ?></h3>
                <form action="<?= site_url($isEdit ? 'admin/employes/update/'.$employeToEdit['id'] : 'admin/employes/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-grid-2" style="margin-bottom:1rem">
                        <div class="f-group"><label class="f-label">Prénom</label><input type="text" class="f-input" name="prenom" placeholder="Jean" value="<?= old('prenom', $isEdit ? $employeToEdit['prenom'] : '') ?>" required /></div>
                        <div class="f-group"><label class="f-label">Nom</label><input type="text" class="f-input" name="nom" placeholder="Rakoto" value="<?= old('nom', $isEdit ? $employeToEdit['nom'] : '') ?>" required /></div>
                        <div class="f-group"><label class="f-label">Email</label><input type="email" class="f-input" name="email" placeholder="jean.rakoto@techmada.mg" value="<?= old('email', $isEdit ? $employeToEdit['email'] : '') ?>" required /></div>
                        <div class="f-group"><label class="f-label">Mot de passe <?= $isEdit ? '<small>(laisser vide pour garder l\'actuel)</small>' : 'initial' ?></label><input type="password" class="f-input" name="password" placeholder="<?= $isEdit ? 'Nouveau mot de passe...' : 'À communiquer à l\'employé' ?>" <?= $isEdit ? '' : 'required' ?> /></div>
                        <div class="f-group"><label class="f-label">Département</label>
                            <select class="f-select" name="DepartementId" required>
                                <?php foreach ($departements as $dept): ?>
                                    <option value="<?= htmlspecialchars($dept['id']) ?>" <?= old('DepartementId', $isEdit ? $employeToEdit['DepartementId'] : '') == $dept['id'] ? 'selected' : '' ?>><?= htmlspecialchars($dept['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="f-group"><label class="f-label">Rôle</label>
                            <select class="f-select" name="role" required>
                                <?php $currentRole = old('role', $isEdit ? $employeToEdit['role'] : ''); ?>
                                <option value="employe" <?= $currentRole == 'employe' ? 'selected' : '' ?>>Employé</option>
                                <option value="rh" <?= $currentRole == 'rh' ? 'selected' : '' ?>>Responsable RH</option>
                                <option value="admin" <?= $currentRole == 'admin' ? 'selected' : '' ?>>Administrateur</option>
                            </select>
                        </div>
                        <div class="f-group"><label class="f-label">Date d'embauche</label><input type="date" class="f-input" name="dateEmbauche" value="<?= old('dateEmbauche', $isEdit ? $employeToEdit['dateEmbauche'] : date('Y-m-d')) ?>" required /></div>
                    </div>
                    <?php if (!$isEdit): ?>
                        <div class="flash flash-info" style="margin-bottom:1rem"><i class="bi bi-info-circle-fill"></i><span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span></div>
                    <?php endif; ?>
                    <div class="form-actions">
                        <button type="submit" class="btn-forest"><i class="bi <?= $isEdit ? 'bi-save' : 'bi-plus' ?>"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Créer l\'employé' ?></button>
                        <?php if ($isEdit): ?>
                            <a href="<?= site_url('admin/employes') ?>" class="btn-secondary" style="text-decoration:none;">Annuler</a>
                        <?php else: ?>
                            <button type="reset" class="btn-secondary">Réinitialiser</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="data-card">
                <div class="data-card-head">
                    <h3>Tous les employés</h3>
                    <div style="display:flex;gap:6px">
                        <input type="text" id="searchInput" class="f-input" placeholder="Rechercher..." style="width:200px;padding:6px 10px;font-size:.8rem" />
                        <select class="f-select" id="deptFilter" style="font-size:.8rem;padding:6px 10px;width:auto">
                            <option value="">Tous les depts</option>
                            <?php foreach ($departements as $dept): ?>
                                <option value="<?= htmlspecialchars($dept['nom']) ?>"><?= htmlspecialchars($dept['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Département</th>
                            <th>Rôle</th>
                            <th>Embauche</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employes as $emp): ?>
                            <tr <?= !$emp['actif'] ? 'style="opacity:.5"' : '' ?> data-dept="<?= htmlspecialchars($emp['dept_nom'] ?? 'Aucun') ?>" data-search="<?= strtolower(htmlspecialchars($emp['prenom'] . ' ' . $emp['nom'] . ' ' . $emp['email'])) ?>">
                                <td>
                                    <div class="profile-row">
                                        <div class="avatar av-green" style="width:32px;height:32px;font-size:.68rem">
                                            <?= strtoupper(substr($emp['prenom'], 0, 1) . substr($emp['nom'], 0, 1)) ?>
                                        </div>
                                        <div class="profile-info">
                                            <div class="pname"><?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?></div>
                                            <div class="pdept"><?= htmlspecialchars($emp['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="td-muted"><?= htmlspecialchars($emp['dept_nom'] ?? 'Aucun') ?></td>
                                <td><span class="type-badge <?= $emp['role'] === 'rh' ? 't-maladie' : '' ?>" style="<?= $emp['role'] === 'employe' ? 'background:#f1efe8;color:#444441' : '' ?>"><?= htmlspecialchars(ucfirst($emp['role'])) ?></span></td>
                                <td class="td-muted td-mono" style="font-size:.78rem"><?= htmlspecialchars($emp['dateEmbauche']) ?></td>
                                <td>
                                    <?php if ($emp['actif']): ?>
                                        <span class="statut s-approuvee" style="font-size:.68rem">actif</span>
                                    <?php else: ?>
                                        <span class="statut s-annulee" style="font-size:.68rem">inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-btns" style="display:flex;gap:4px;">
                                        <?php if ($emp['actif']): ?>
                                            <a href="<?= site_url('admin/employes/edit/' . $emp['id']) ?>" class="btn-sm btn-edit" style="text-decoration:none;"><i class="bi bi-pencil"></i> Éditer</a>
                                            <form action="<?= site_url('admin/employes/toggle/' . $emp['id']) ?>" method="post" onsubmit="return confirm('Voulez-vous vraiment désactiver cet employé ?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-sm btn-del"><i class="bi bi-slash-circle"></i></button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?= site_url('admin/employes/toggle/' . $emp['id']) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-sm btn-view"><i class="bi bi-arrow-counterclockwise"></i> Réactiver</button>
                                            </form>
                                        <?php endif; ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const deptFilter = document.getElementById('deptFilter');
        const tbody = document.querySelector('.tbl tbody');
        const rows = tbody.querySelectorAll('tr');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const dept = deptFilter.value;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                const deptData = row.getAttribute('data-dept') || '';

                const matchesSearch = searchData.includes(searchTerm);
                const matchesDept = dept === '' || deptData === dept;

                if (matchesSearch && matchesDept) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterTable);
        deptFilter.addEventListener('change', filterTable);
    });
</script>

<?= $this->endSection() ?>