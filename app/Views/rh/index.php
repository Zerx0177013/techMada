<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="app-wrap">
    <aside class="sidebar">
        <div class="sidebar-brand"><div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div><div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div></div>
        <div class="sidebar-section">Menu</div>
        <ul class="sidebar-nav">
            <li><a href="<?= site_url('rh') ?>" class="active"><i class="bi bi-inbox"></i> Demandes à traiter <span class="nav-badge alert"><?= esc((string) ($pendingCount ?? 0)) ?></span></a></li>
            <li><a href="#"><i class="bi bi-archive"></i> Historique</a></li>
            <li><a href="#"><i class="bi bi-people"></i> Soldes employés</a></li>
        </ul>
        <?php $u = session()->get('user'); ?>
        <?php $uInitials = strtoupper(mb_substr((string)($u['prenom'] ?? ''), 0, 1) . mb_substr((string)($u['nom'] ?? ''), 0, 1)); ?>
        <?php $roleLabel = (($u['role'] ?? '') === 'admin') ? 'Administrateur' : 'Responsable RH'; ?>
        <div class="sidebar-user"><div class="s-user-row"><div class="avatar av-blue"><?= esc($uInitials ?: 'RH') ?></div><div><div class="user-name"><?= esc(trim((string)(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? '')))) ?></div><div class="user-role"><?= esc($roleLabel) ?></div></div><a href="<?= site_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a></div></div>
    </aside>
    <div class="main">
        <div class="topbar"><div><div class="topbar-title">Demandes à traiter</div><div class="topbar-breadcrumb"><a href="#">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div></div><div class="topbar-actions"><span style="font-size:.8rem;color:var(--warn);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px"><i class="bi bi-hourglass-split"></i> <?= esc((string) ($pendingCount ?? 0)) ?> en attente</span></div></div>
        <div class="content">
            <?php if ($msg = session()->getFlashdata('success')): ?>
                <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc($msg) ?></div>
            <?php endif; ?>
            <?php if ($msg = session()->getFlashdata('error')): ?>
                <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc($msg) ?></div>
            <?php endif; ?>
            <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
                <button style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--forest);background:var(--forest);color:var(--white);cursor:pointer">Tous</button>
                <select id="statutFilter" class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto;margin-left:auto">
                    <option value="">Tous les statuts</option>
                    <option value="enAttente">En attente</option>
                    <option value="approuvee">Approuvées</option>
                    <option value="refusee">Refusées</option>
                    <option value="annulee">Annulées</option>
                </select>
            </div>
            <div class="data-card">
                <div class="data-card-head"><h3>Toutes les demandes</h3></div>
                <table class="tbl">
                    <thead><tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Solde dispo</th><th>Statut</th><th>Actions</th></tr></thead>
                    <tbody id="congesTableBody">
                        <?php foreach (($demandes ?? []) as $d): ?>
                            <?php
                                $start = !empty($d['dateDebut']) ? new DateTime($d['dateDebut']) : null;
                                $end = !empty($d['dateFin']) ? new DateTime($d['dateFin']) : null;
                                $period = ($start && $end) ? ($start->format('d/m') . ' – ' . $end->format('d/m/Y')) : '—';
                                $statut = (string) ($d['statut'] ?? '');
                                $canApprove = (bool) ($d['can_approve'] ?? false);
                                $deductible = (int) ($d['type_deductible'] ?? 0) === 1;
                                $soldeDispo = $d['solde_dispo'];
                                $insuffisant = $deductible && ($soldeDispo === null || (int) $soldeDispo < (int) $d['nbJours']);
                            ?>
                            <tr data-statut="<?= esc($statut) ?>">
                                <td>
                                    <div class="profile-row">
                                        <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem"><?= esc((string) ($d['initials'] ?? '—')) ?></div>
                                        <div class="profile-info">
                                            <div class="pname"><?= esc(trim((string) (($d['emp_prenom'] ?? '') . ' ' . ($d['emp_nom'] ?? '')))) ?></div>
                                            <div class="pdept"><?= esc((string) ($d['dept_nom'] ?? '—')) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="type-badge <?= esc((string) ($d['type_class'] ?? 't-sans-solde')) ?>"><?= esc((string) ($d['type_libelle'] ?? '—')) ?></span></td>
                                <td class="td-muted" style="font-size:.8rem"><?= esc($period) ?></td>
                                <td class="td-mono"><?= esc((string) ($d['nbJours'] ?? 0)) ?> j</td>
                                <td>
                                    <?php if (!$deductible): ?>
                                        <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--muted)">—</span>
                                    <?php else: ?>
                                        <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:<?= $insuffisant ? 'var(--warn)' : 'var(--success)' ?>;font-weight:500"><?= esc((string) (int) $soldeDispo) ?> j</span>
                                        <?php if ($insuffisant): ?>
                                            <span style="font-size:.72rem;color:var(--danger)"> ⚠ insuffisant</span>
                                        <?php else: ?>
                                            <span style="font-size:.72rem;color:var(--muted)"> dispo</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($statut === 'enAttente'): ?>
                                        <span class="statut s-attente">en attente</span>
                                    <?php elseif ($statut === 'approuvee'): ?>
                                        <span class="statut s-approuvee">approuvée</span>
                                    <?php elseif ($statut === 'refusee'): ?>
                                        <span class="statut s-refusee">refusée</span>
                                    <?php elseif ($statut === 'annulee'): ?>
                                        <span class="statut s-annulee">annulée</span>
                                    <?php else: ?>
                                        <span class="statut s-annulee"><?= esc($statut) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($statut === 'enAttente'): ?>
                                        <div class="action-btns">
                                            <form action="<?= site_url('rh/conges/' . $d['id'] . '/approve') ?>" method="post" style="margin:0">
                                                <?= csrf_field() ?>
                                                <button class="btn-sm btn-approve" <?= $canApprove ? '' : 'disabled style="opacity:.4;cursor:not-allowed"' ?>><i class="bi bi-check-lg"></i> Approuver</button>
                                            </form>
                                            <a class="btn-sm btn-refuse" href="<?= site_url('rh?refuse=' . $d['id']) ?>"><i class="bi bi-x-lg"></i> Refuser</a>
                                        </div>
                                    <?php else: ?>
                                        <?php if (!empty($d['traite_prenom']) || !empty($d['traite_nom'])): ?>
                                            <span class="td-muted" style="font-size:.75rem">Traité par <?= esc(trim((string) (($d['traite_prenom'] ?? '') . ' ' . ($d['traite_nom'] ?? '')))) ?></span>
                                        <?php else: ?>
                                            <span class="td-muted" style="font-size:.75rem">—</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="noRowsRow" style="display:none"><td colspan="7" class="td-muted" style="text-align:center">Aucune demande trouvée.</td></tr>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($refuseTarget)): ?>
                <?php
                    $start = !empty($refuseTarget['dateDebut']) ? new DateTime($refuseTarget['dateDebut']) : null;
                    $end = !empty($refuseTarget['dateFin']) ? new DateTime($refuseTarget['dateFin']) : null;
                    $period = ($start && $end) ? ($start->format('d/m/Y') . ' – ' . $end->format('d/m/Y')) : '—';
                    $deductible = (int) ($refuseTarget['type_deductible'] ?? 0) === 1;
                    $soldeDispo = $refuseTarget['solde_dispo'];
                    $insuffisant = $deductible && ($soldeDispo === null || (int) $soldeDispo < (int) $refuseTarget['nbJours']);
                ?>
                <div style="margin-top:1.5rem">
                    <div class="form-section" style="border-color:var(--danger-br);background:var(--danger-bg)">
                        <h3 style="color:var(--danger)"><i class="bi bi-x-circle"></i> Confirmer le refus — <?= esc(trim((string) (($refuseTarget['emp_prenom'] ?? '') . ' ' . ($refuseTarget['emp_nom'] ?? '')))) ?></h3>
                        <div style="font-size:.875rem;color:var(--ink);margin-bottom:1rem">
                            Demande de <strong><?= esc((string) ($refuseTarget['nbJours'] ?? 0)) ?> jours</strong> · Période : <?= esc($period) ?> · Type : <?= esc((string) ($refuseTarget['type_libelle'] ?? '—')) ?><br>
                            <?php if ($insuffisant): ?>
                                <span style="font-size:.8rem;color:var(--danger)"><i class="bi bi-exclamation-triangle"></i> Solde insuffisant.</span>
                            <?php endif; ?>
                        </div>
                        <form action="<?= site_url('rh/conges/' . $refuseTarget['id'] . '/refuse') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="f-group">
                                <label class="f-label">Commentaire pour l'employé (optionnel)</label>
                                <textarea class="f-textarea" name="commentaire" placeholder="Ex : Solde insuffisant, veuillez contacter les RH pour un congé sans solde."></textarea>
                            </div>
                            <div class="form-actions">
                                <button class="btn-sm btn-refuse" style="padding:9px 16px;font-size:.875rem" type="submit"><i class="bi bi-x-lg"></i> Confirmer le refus</button>
                                <a class="btn-secondary" href="<?= site_url('rh') ?>"><i class="bi bi-arrow-left"></i> Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filter = document.getElementById('statutFilter');
    const tbody = document.getElementById('congesTableBody');

    if (!filter || !tbody) {
        return;
    }

    const noRowsRow = document.getElementById('noRowsRow');
    const rows = Array.from(tbody.querySelectorAll('tr')).filter((tr) => tr.id !== 'noRowsRow');

    function applyFilter() {
        const selected = filter.value;
        let visibleCount = 0;

        rows.forEach((tr) => {
            const statut = tr.dataset.statut || '';
            const show = selected === '' || statut === selected;
            tr.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        if (noRowsRow) {
            noRowsRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    filter.addEventListener('change', applyFilter);
    applyFilter();
});
</script>
<?= $this->endSection() ?>