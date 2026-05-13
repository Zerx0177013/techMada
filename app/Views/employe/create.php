<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="app-wrap">
    <aside class="sidebar">
        <div class="sidebar-brand"><div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div><div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div></div>
        <ul class="sidebar-nav" style="margin-top:1rem">
            <li><a href="<?= site_url('employe') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
            <li><a href="<?= site_url('employe/create') ?>" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
            <li><a href="<?= site_url('employe/conges') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
            <li><a href="#"><i class="bi bi-person"></i> Mon profil</a></li>
        </ul>
        <div class="sidebar-user"><div class="s-user-row"><div class="avatar av-green"><?= substr($employe['prenom'], 0, 1) . substr($employe['nom'], 0, 1) ?></div><div><div class="user-name"><?= esc($employe['prenom'] . ' ' . $employe['nom']) ?></div><div class="user-role"><?= esc(ucfirst($employe['role'])) ?></div></div></div></div>
    </aside>
    <div class="main">
        <div class="topbar"><div><div class="topbar-title">Nouvelle demande de congé</div><div class="topbar-breadcrumb"><a href="<?= site_url('employe') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande</div></div></div>
        <div class="content">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="flash flash-danger" style="color:var(--danger);background-color:#fee2e2;padding:10px;margin-bottom:15px;border-radius:5px;"><i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="flash flash-danger" style="color:var(--danger);background-color:#fee2e2;padding:10px;margin-bottom:15px;border-radius:5px;">
                    <ul style="margin:0;padding-left:20px;">
                    <?php foreach (session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form action="<?= site_url('employe/store') ?>" method="post">
                <?= csrf_field() ?>
                <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">
                    <div>
                        <div class="form-section">
                            <h3>Détails de la demande</h3>
                            <div class="f-group" style="margin-bottom:1rem">
                                <label class="f-label">Type de congé <span style="color:var(--danger)">*</span></label>
                                <select name="type_conge" class="f-select" required>
                                    <option value="">-- Choisir un type --</option>
                                    <?php foreach ($typesConges as $tc): ?>
                                        <?php 
                                            $sold = $soldesParType[$tc['id']] ?? null;
                                            $restantText = '';
                                            if ($tc['deductible'] == 1 && $sold) {
                                                $restantText = ' (' . max(0, $sold['joursAttribues'] - $sold['joursPris']) . ' j restants)';
                                            } else if ($tc['deductible'] == 0) {
                                                $restantText = ' (Non déductible)';
                                            }
                                        ?>
                                        <option value="<?= $tc['id'] ?>" <?= old('type_conge') == $tc['id'] ? 'selected' : '' ?>><?= esc($tc['libelle']) . $restantText ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-grid-2" style="margin-bottom:1rem">
                                <div class="f-group"><label class="f-label">Date de début <span style="color:var(--danger)">*</span></label><input type="date" name="date_debut" class="f-input" value="<?= old('date_debut') ?>" required/></div>
                                <div class="f-group"><label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label><input type="date" name="date_fin" class="f-input" value="<?= old('date_fin') ?>" required/></div>
                            </div>
                            
                            <div class="f-group" style="margin-bottom:1rem"><label class="f-label">Motif (optionnel)</label><textarea name="motif" class="f-textarea" placeholder="Précisez le motif de votre demande si nécessaire..."><?= old('motif') ?></textarea><div class="f-hint">Le motif est visible par le responsable RH.</div></div>
                            <div class="form-actions"><button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button><a href="<?= site_url('employe') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a></div>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:1rem">
                        <div class="data-card" style="margin:0">
                            <div class="data-card-head"><h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
                            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
                                <?php if (!empty($soldesParType)): ?>
                                    <?php foreach ($soldesParType as $s): ?>
                                        <?php
                                            $restant = max(0, $s['joursAttribues'] - $s['joursPris']);
                                            $pourcentage = $s['joursAttribues'] > 0 ? ($restant / $s['joursAttribues']) * 100 : 0;
                                        ?>
                                        <div>
                                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                                                <span style="font-size:.8rem;color:var(--ink)"><?= esc($s['type_libelle']) ?></span>
                                                <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:<?= $pourcentage < 30 ? 'var(--warn)' : 'var(--forest)' ?>;font-weight:500"><?= $restant ?> j</span>
                                            </div>
                                            <div class="solde-bar"><div class="solde-fill <?= $pourcentage < 30 ? 'warn' : '' ?>" style="width:<?= $pourcentage ?>%"></div></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="font-size:0.8rem">Aucun solde trouvé.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flash flash-info" style="margin:0"><i class="bi bi-info-circle-fill"></i><span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span></div>
                        <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem"><div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles</div><ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7"><li>Préavis minimum : 48h avant la date de début</li><li>Pas de chevauchement avec une demande en cours</li><li>Solde insuffisant = demande refusée automatiquement</li></ul></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
    </div>
</div>
<?= $this->endSection() ?>