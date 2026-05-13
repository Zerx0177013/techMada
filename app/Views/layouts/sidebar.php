<?php
/**
 * Sidebar partagé, adapté au rôle en session.
 * Variables optionnelles:
 * - $currentUser (array)
 * - $pendingCount (int)
 */

$currentUser = $currentUser ?? session('user') ?? [];
$role = (string) ($currentUser['role'] ?? 'employe');
$pendingCount = (int) ($pendingCount ?? 0);

$path = (string) service('uri')->getPath();
$isActive = static fn (string $prefix): bool => ($path === $prefix) || str_starts_with($path, $prefix . '/');

$fullName = trim((string) ($currentUser['prenom'] ?? '') . ' ' . (string) ($currentUser['nom'] ?? ''));
$initials = strtoupper(
    mb_substr((string) ($currentUser['prenom'] ?? ''), 0, 1) . mb_substr((string) ($currentUser['nom'] ?? ''), 0, 1)
);
if ($initials === '') {
    $initials = '—';
}

[$brandIcon, $brandSub, $avatarClass, $roleLabel] = match ($role) {
    'admin' => ['bi-shield-check', 'Administration', 'av-amber', 'Administrateur'],
    'rh' => ['bi-person-check', 'Espace responsable', 'av-blue', 'Responsable RH'],
    default => ['bi-briefcase', 'Espace employé', 'av-green', 'Employé'],
};

$items = match ($role) {
    'admin' => [
        ['url' => site_url('admin'), 'key' => 'admin', 'icon' => 'bi-speedometer2', 'label' => "Vue d'ensemble"],
        ['url' => site_url('rh'), 'key' => 'rh', 'icon' => 'bi-inbox', 'label' => 'Toutes les demandes', 'badge' => $pendingCount],
        ['url' => site_url('admin/employes'), 'key' => 'admin/employes', 'icon' => 'bi-people', 'label' => 'Employés'],
        ['url' => site_url('admin/departements'), 'key' => 'admin/departements', 'icon' => 'bi-building', 'label' => 'Départements'],
        ['url' => site_url('admin/types-conge'), 'key' => 'admin/types-conge', 'icon' => 'bi-tags', 'label' => 'Types de congé'],
    ],
    'rh' => [
        ['url' => site_url('rh'), 'key' => 'rh', 'icon' => 'bi-inbox', 'label' => 'Demandes à traiter', 'badge' => $pendingCount],
    ],
    default => [
        ['url' => site_url('employe'), 'key' => 'employe', 'icon' => 'bi-grid-1x2', 'label' => 'Tableau de bord'],
        ['url' => site_url('employe/create'), 'key' => 'employe/create', 'icon' => 'bi-plus-circle', 'label' => 'Nouvelle demande'],
        ['url' => site_url('employe/conges'), 'key' => 'employe/conges', 'icon' => 'bi-calendar3', 'label' => 'Mes demandes'],
    ],
};
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo-icon"><i class="bi <?= esc($brandIcon) ?>"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span><?= esc($brandSub) ?></span></div>
    </div>

    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
        <?php foreach ($items as $it): ?>
            <?php $active = $isActive((string) $it['key']); ?>
            <li>
                <a href="<?= esc($it['url']) ?>" class="<?= $active ? 'active' : '' ?>">
                    <i class="bi <?= esc((string) $it['icon']) ?>"></i>
                    <?= esc((string) $it['label']) ?>
                    <?php if (isset($it['badge']) && (int) $it['badge'] > 0): ?>
                        <span class="nav-badge alert"><?= esc((string) (int) $it['badge']) ?></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-user">
        <div class="s-user-row">
            <div class="avatar <?= esc($avatarClass) ?>"><?= esc($initials) ?></div>
            <div>
                <div class="user-name"><?= esc($fullName !== '' ? $fullName : 'Utilisateur') ?></div>
                <div class="user-role"><?= esc($roleLabel) ?></div>
            </div>
            <a href="<?= site_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</aside>
