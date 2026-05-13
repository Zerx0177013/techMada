<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="app-wrap">
    <?= view('layouts/sidebar') ?>
    <div class="main">
        <div class="topbar"><div><div class="topbar-title">Mes demandes de congé</div><div class="topbar-breadcrumb"><a href="<?= site_url('employe') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div></div><div class="topbar-actions"><a href="<?= site_url('employe/create') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a></div></div>
        <div class="content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash flash-danger" style="color:var(--danger);background-color:#fee2e2;padding:10px;margin-bottom:15px;border-radius:5px;"><i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <div class="data-card">
                <div class="data-card-head">
                    <h3>Toutes mes demandes</h3>
                    <div style="display:flex;gap:6px">
                        <select class="f-select" id="statutFilter" style="font-size:.8rem;padding:6px 10px;width:auto">
                            <option value="">Tous les statuts</option>
                            <option value="enAttente">En attente</option>
                            <option value="approuvee">Approuvée</option>
                            <option value="refusee">Refusée</option>
                            <option value="annulee">Annulée</option>
                        </select>
                    </div>
                </div>
                <table class="tbl">
                    <thead><tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr></thead>
                    <tbody id="congesTableBody">
                        <?php foreach($conges as $conge): ?>
                            <tr>
                                <td><span class="type-badge t-<?= strtolower(str_replace(' ', '-', $conge['type_libelle'])) ?>"><?= esc($conge['type_libelle']) ?></span></td>
                                <td class="td-muted"><?= date('d M Y', strtotime($conge['dateDebut'])) ?></td>
                                <td class="td-muted"><?= date('d M Y', strtotime($conge['dateFin'])) ?></td>
                                <td class="td-mono"><?= $conge['nbJours'] ?> j</td>
                                <td><span class="statut s-<?= strtolower($conge['statut']) ?>"><?= esc($conge['statut'] === 'enAttente' ? 'en attente' : $conge['statut']) ?></span></td>
                                <?php if ($conge['statut'] === 'approuvee'): ?>
                                    <td style="font-size:.78rem;color:var(--success)"><i class="bi bi-check-circle"></i> <?= esc($conge['commentaireRh'] ?? 'Validé') ?></td>
                                <?php elseif ($conge['statut'] === 'refusee'): ?>
                                    <td style="font-size:.78rem;color:var(--danger)"><?= esc($conge['commentaireRh'] ?? 'Refusé') ?></td>
                                <?php else: ?>
                                    <td class="td-muted" style="font-size:.78rem"><?= esc($conge['commentaireRh'] ?? '—') ?></td>
                                <?php endif; ?>
                                <td>
                                    <?php if ($conge['statut'] === 'enAttente'): ?>
                                        <form action="<?= site_url('employe/conges/cancel/' . $conge['id']) ?>" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?');" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-sm btn-cancel"><i class="bi bi-x"></i> Annuler</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="td-muted" style="font-size:.75rem">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($conges)): ?>
                            <tr><td colspan="7" class="td-muted" style="text-align:center">Aucune demande trouvée.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filter = document.getElementById('statutFilter');
    const tbody = document.getElementById('congesTableBody');

    filter.addEventListener('change', function() {
        const statut = this.value;
        const url = `<?= site_url('employe/conges') ?>?statut=${encodeURIComponent(statut)}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = '';
            if (data.conges.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="td-muted" style="text-align:center">Aucune demande trouvée.</td></tr>';
                return;
            }

            data.conges.forEach(conge => {
                const badgeClass = 't-' + conge.type_libelle.toLowerCase().replace(/ /g, '-');
                const statutClass = 's-' + conge.statut.toLowerCase();
                const displayStatut = conge.statut === 'enAttente' ? 'en attente' : conge.statut;
                
                let commentHtml = `<td class="td-muted" style="font-size:.78rem">${conge.commentaireRh || '—'}</td>`;
                if (conge.statut === 'approuvee') {
                    commentHtml = `<td style="font-size:.78rem;color:var(--success)"><i class="bi bi-check-circle"></i> ${conge.commentaireRh || 'Validé'}</td>`;
                } else if (conge.statut === 'refusee') {
                    commentHtml = `<td style="font-size:.78rem;color:var(--danger)">${conge.commentaireRh || 'Refusé'}</td>`;
                }

                let actionHtml = '<span class="td-muted" style="font-size:.75rem">—</span>';
                if (conge.statut === 'enAttente') {
                    const cancelUrl = `<?= site_url('employe/conges/cancel/') ?>${conge.id}`;
                    actionHtml = `
                        <form action="${cancelUrl}" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?');" style="display:inline;">
                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                            <button type="submit" class="btn-sm btn-cancel"><i class="bi bi-x"></i> Annuler</button>
                        </form>
                    `;
                }

                const dateDebut = new Date(conge.dateDebut).toLocaleDateString('fr-FR', {day: '2-digit', month: 'short', year: 'numeric'});
                const dateFin = new Date(conge.dateFin).toLocaleDateString('fr-FR', {day: '2-digit', month: 'short', year: 'numeric'});

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><span class="type-badge ${badgeClass}">${conge.type_libelle}</span></td>
                    <td class="td-muted">${dateDebut}</td>
                    <td class="td-muted">${dateFin}</td>
                    <td class="td-mono">${conge.nbJours} j</td>
                    <td><span class="statut ${statutClass}">${displayStatut}</span></td>
                    ${commentHtml}
                    <td>${actionHtml}</td>
                `;
                tbody.appendChild(tr);
            });
        });
    });
});
</script>

<?= $this->endSection() ?>