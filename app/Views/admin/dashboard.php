<?= $this->extend('layouts/app') ?>

<?php
/** @var array|null $currentUser */
$currentUser = $currentUser ?? session('user');
$pendingCount = (int)($pendingCount ?? 0);
$activeEmployees = (int)($activeEmployees ?? 0);
$approvedThisMonth = (int)($approvedThisMonth ?? 0);
$departementsCount = (int)($departementsCount ?? 0);
$absentCountToday = (int)($absentCountToday ?? 0);
$recentDemandes = $recentDemandes ?? [];
$absents = $absents ?? [];
$criticalSoldeCount = (int)($criticalSoldeCount ?? 0);

$uFullName = trim((string)($currentUser['prenom'] ?? '') . ' ' . (string)($currentUser['nom'] ?? ''));
$uInitials = strtoupper(mb_substr((string)($currentUser['prenom'] ?? ''), 0, 1) . mb_substr((string)($currentUser['nom'] ?? ''), 0, 1));
$uRole = (string)($currentUser['role'] ?? 'admin');
?>

<?= $this->section('content') ?>
<div class="app-wrap">
    <aside class="sidebar">
        <div class="sidebar-brand"><div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div><div class="sidebar-brand-name">TechMada RH<span>Administration</span></div></div>
        <div class="sidebar-section">Gestion</div>
        <ul class="sidebar-nav">
            <li><a href="<?= site_url('admin') ?>" class="active"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
            <li><a href="<?= site_url('rh') ?>"><i class="bi bi-inbox"></i> Toutes les demandes <span class="nav-badge alert"><?= esc((string)$pendingCount) ?></span></a></li>
            <li><a href="<?= site_url('admin/employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
            <li><a href="<?= site_url('admin/employes') ?>"><i class="bi bi-building"></i> Départements</a></li>
            <li><a href="<?= site_url('admin/employes') ?>"><i class="bi bi-tags"></i> Types de congé</a></li>
            <li><a href="<?= site_url('admin/employes') ?>"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
        </ul>
        <div class="sidebar-user"><div class="s-user-row"><div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem"><?= esc($uInitials !== '' ? $uInitials : 'AD') ?></div><div><div class="user-name"><?= esc($uFullName !== '' ? $uFullName : 'Administrateur') ?></div><div class="user-role"><?= esc($uRole) ?></div></div><a href="<?= site_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a></div></div>
    </aside>
    <div class="main">
        <div class="topbar"><div><div class="topbar-title">Vue d'ensemble</div><div class="topbar-breadcrumb">Administration</div></div><div class="topbar-actions"><a href="<?= site_url('admin/employes') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un employé</a></div></div>
        <div class="content">
            <div class="metrics">
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div><div class="metric-val"><?= esc((string)$activeEmployees) ?></div><div class="metric-label">Employés actifs</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div><div class="metric-val"><?= esc((string)$pendingCount) ?></div><div class="metric-label">Demandes en attente</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div><div class="metric-val"><?= esc((string)$approvedThisMonth) ?></div><div class="metric-label">Approuvées ce mois</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div><div class="metric-val"><?= esc((string)$departementsCount) ?></div><div class="metric-label">Départements</div></div>
                <div class="metric"><div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div></div><div class="metric-val"><?= esc((string)$absentCountToday) ?></div><div class="metric-label">Absents aujourd'hui</div></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">
                <div class="data-card" style="margin:0"><div class="data-card-head"><h3>Demandes récentes</h3><a href="<?= site_url('rh') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a></div><table class="tbl"><thead><tr><th>Employé</th><th>Type</th><th>Durée</th><th>Statut</th></tr></thead><tbody>
                <?php if (empty($recentDemandes)): ?>
                    <tr><td colspan="4" class="td-muted">Aucune demande</td></tr>
                <?php else: ?>
                    <?php foreach ($recentDemandes as $d): ?>
                        <tr>
                            <td><div style="display:flex;align-items:center;gap:7px"><div class="avatar <?= esc($d['avatar_class'] ?? 'av-green') ?>" style="width:28px;height:28px;font-size:.62rem"><?= esc($d['initials'] ?? '—') ?></div><span class="td-name" style="font-size:.84rem"><?= esc($d['full_name'] ?? '—') ?></span></div></td>
                            <td><span class="type-badge <?= esc($d['type_class'] ?? '') ?>"><?= esc($d['type_label'] ?? '—') ?></span></td>
                            <td class="td-mono"><?= esc($d['duree_label'] ?? '—') ?></td>
                            <td><span class="statut <?= esc($d['status_class'] ?? '') ?>"><?= esc($d['status_label'] ?? '—') ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody></table></div>
                <div style="display:flex;flex-direction:column;gap:1rem">
                    <div class="data-card" style="margin:0"><div class="data-card-head"><h3><i class="bi bi-person-slash" style="color:var(--muted);margin-right:5px"></i>Absents aujourd'hui</h3></div><div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
                        <?php if (empty($absents)): ?>
                            <div class="td-muted" style="font-size:.8rem">Aucun absent aujourd'hui</div>
                        <?php else: ?>
                            <?php foreach ($absents as $a): ?>
                                <div style="display:flex;align-items:center;gap:8px"><div class="avatar <?= esc($a['avatar_class'] ?? 'av-green') ?>" style="width:30px;height:30px;font-size:.65rem"><?= esc($a['initials'] ?? '—') ?></div><div><div style="font-size:.83rem;font-weight:500;color:var(--ink)"><?= esc(trim(($a['prenom'] ?? '') . ' ' . ($a['nom'] ?? '')) ?: '—') ?></div><div style="font-size:.72rem;color:var(--muted)"><?= esc($a['type_label'] ?? 'Congé') ?> · retour <?= esc($a['retour_label'] ?? '—') ?></div></div></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div></div>
                    <?php if ($criticalSoldeCount > 0): ?>
                        <div class="flash flash-warn" style="margin:0"><i class="bi bi-exclamation-triangle-fill"></i><span style="font-size:.8rem"><?= esc((string)$criticalSoldeCount) ?> employé(s) ont un solde annuel critique (≤ 2 jours). <a href="#" style="color:var(--warn);font-weight:500">Voir les soldes →</a></span></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
    </div>
</div>
<?= $this->endSection() ?>