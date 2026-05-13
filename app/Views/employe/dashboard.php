<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="app-wrap">
    <?= view('layouts/sidebar') ?>
    <div class="main">
        <div class="topbar">
            <div><div class="topbar-title">Tableau de bord</div><div class="topbar-breadcrumb">Accueil</div></div>
            <div class="topbar-actions"><a href="<?= site_url('employe/create') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a></div>
        </div>
        <div class="content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <div class="metrics">
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div><div class="metric-val"><?= count($employe['demandeCongesEnAttente'] ?? []) ?></div><div class="metric-label">En attente</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div><div class="metric-val"><?= count($employe['demandeCongesApprouvees'] ?? []) ?></div><div class="metric-label">Approuvées</div></div>
                
                <?php 
                    $soldeAnnuel = !empty($employe['soldeAnnuel']) ? reset($employe['soldeAnnuel']) : ['joursAttribues' => 0, 'joursPris' => 0];
                    $joursRestantsAnnuel = $soldeAnnuel['joursAttribues'] - $soldeAnnuel['joursPris'];
                ?>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div><div class="metric-val"><?= $joursRestantsAnnuel ?></div><div class="metric-label">Jours restants</div><div class="metric-sub">sur <?= $soldeAnnuel['joursAttribues'] ?> (Annuel)</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div><div class="metric-val"><?= count($employe['demandeCongesRefusees'] ?? []) ?></div><div class="metric-label">Refusées</div></div>
            </div>
            <div class="data-card">
                <div class="data-card-head"><h3>Mes soldes de congés — <?= date('Y') ?></h3></div>
                <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
                    <?php if (!empty($employe['soldes'])): ?>
                        <?php foreach($employe['soldes'] as $s): ?>
                            <?php 
                                $restant = max(0, $s['joursAttribues'] - $s['joursPris']);
                                $pourcentage = $s['joursAttribues'] > 0 ? ($restant / $s['joursAttribues']) * 100 : 0;
                            ?>
                            <div class="solde-card" style="margin:0">
                                <div class="solde-header"><span class="solde-type"><?= esc($s['type_libelle']) ?></span><span class="solde-nums"><strong><?= $restant ?></strong> / <?= $s['joursAttribues'] ?> j</span></div>
                                <div class="solde-bar"><div class="solde-fill <?= $pourcentage < 30 ? 'warn' : '' ?>" style="width:<?= $pourcentage ?>%"></div></div>
                                <div class="solde-label"><?= $restant ?> jours restants · <?= $s['joursPris'] ?> pris</div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun solde trouvé pour cette année.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="data-card">
                <div class="data-card-head"><h3>Mes dernières demandes</h3><a href="<?= site_url('employe/conges') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a></div>
                <table class="tbl">
                    <thead><tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Motif</th></tr></thead>
                    <tbody>
                        <?php 
                            $historiqueConges = array_merge($employe['demandeCongesEnAttente'], $employe['demandeCongesApprouvees'], $employe['demandeCongesRefusees'], $employe['demandeCongesAnnulees']);
                            usort($historiqueConges, function($a, $b) {
                                return strtotime($b['createdAt']) - strtotime($a['createdAt']);
                            });
                            $latestConges = array_slice($historiqueConges, 0, 5); 
                        ?>
                        <?php if (empty($latestConges)): ?>
                            <tr><td colspan="6" class="td-muted">Aucune demande récente</td></tr>
                        <?php else: ?>
                            <?php foreach ($latestConges as $conge): ?>
                                <tr>
                                    <td><span class="type-badge t-<?= strtolower(str_replace(' ', '-', $conge['type_libelle'])) ?>"><?= esc($conge['type_libelle']) ?></span></td>
                                    <td class="td-muted"><?= date('d M Y', strtotime($conge['dateDebut'])) ?></td>
                                    <td class="td-muted"><?= date('d M Y', strtotime($conge['dateFin'])) ?></td>
                                    <td class="td-mono"><?= $conge['nbJours'] ?> j</td>
                                    <td>
                                        <span class="statut s-<?= strtolower($conge['statut']) ?>">
                                            <?= esc($conge['statut'] === 'enAttente' ? 'en attente' : $conge['statut']) ?>
                                        </span>
                                    </td>
                                    <td class="td-muted"><?= esc($conge['motif']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — Projet CodeIgniter 4</div>
    </div>
</div>
<?= $this->endSection() ?>