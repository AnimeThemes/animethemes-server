<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Database\Seeder;

/**
 * Class PermissionSeeder.
 */
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var Role */
        $admin = Role::findOrCreate('Admin');

        /** @var Role */
        $wikiEditor = Role::findOrCreate('Wiki Editor');

        /** @var Role */
        $wikiViewer = Role::findOrCreate('Wiki Viewer');

        // Admin Resources
        $this->configureAdminResourcePermissions($admin, 'announcement', true);
        $this->configureAdminResourcePermissions($admin, 'balance', true);
        $this->configureAdminResourcePermissions($admin, 'invitation', true);
        $this->configureAdminResourcePermissions($admin, 'permission', false);
        $this->configureAdminResourcePermissions($admin, 'role', false);
        $this->configureAdminResourcePermissions($admin, 'transaction', true);
        $this->configureAdminResourcePermissions($admin, 'user', true);

        // Wiki Resources
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'anime');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'anime synonym');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'anime theme');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'anime theme entry');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'artist');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'external resource');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'image');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'page');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'series');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'song');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'studio');
        $this->configureWikiResourcePermissions($admin, $wikiEditor, $wikiViewer, 'video');

        // Special Permissions
        $this->configureSpecialPermissions($admin, $wikiEditor, $wikiViewer);
    }

    /**
     * Assign permissions for admin resources.
     *
     * @param  Role  $admin
     * @param  string  $adminResource
     * @param  bool  $includeSoftDeletion
     * @return void
     */
    protected function configureAdminResourcePermissions(Role $admin, string $adminResource, bool $includeSoftDeletion): void
    {
        $permissions = [];

        $permissions[] = Permission::findOrCreate("view $adminResource");
        if ($adminResource !== 'permission') {
            $permissions[] = Permission::findOrCreate("create $adminResource");
            $permissions[] = Permission::findOrCreate("update $adminResource");
            $permissions[] = Permission::findOrCreate("delete $adminResource");
        }

        if ($includeSoftDeletion) {
            $permissions[] = Permission::findOrCreate("restore $adminResource");
            $permissions[] = Permission::findOrCreate("force delete $adminResource");
        }

        $admin->givePermissionTo($permissions);
    }

    /**
     * Assign permissions for wiki resources.
     *
     * @param  Role  $admin
     * @param  Role  $editor
     * @param  Role  $viewer
     * @param  string  $wikiResource
     * @return void
     */
    protected function configureWikiResourcePermissions(Role $admin, Role $editor, Role $viewer, string $wikiResource): void
    {
        $adminPermissions = [];
        $editorPermissions = [];
        $viewerPermissions = [];

        $view = Permission::findOrCreate("view $wikiResource");
        $adminPermissions[] = $view;
        $editorPermissions[] = $view;
        $viewerPermissions[] = $view;

        $create = Permission::findOrCreate("create $wikiResource");
        $adminPermissions[] = $create;
        if ($wikiResource !== 'video') {
            $editorPermissions[] = $create;
        }

        $update = Permission::findOrCreate("update $wikiResource");
        $adminPermissions[] = $update;
        $editorPermissions[] = $update;

        $delete = Permission::findOrCreate("delete $wikiResource");
        $adminPermissions[] = $delete;
        if ($wikiResource !== 'video') {
            $editorPermissions[] = $delete;
        }

        $restore = Permission::findOrCreate("restore $wikiResource");
        $adminPermissions[] = $restore;
        if ($wikiResource !== 'video') {
            $editorPermissions[] = $restore;
        }

        $forceDelete = Permission::findOrCreate("force delete $wikiResource");
        $adminPermissions[] = $forceDelete;

        $admin->givePermissionTo($adminPermissions);
        $editor->givePermissionTo($editorPermissions);
        $viewer->givePermissionTo($viewerPermissions);
    }

    /**
     * Configure special permissions.
     *
     * @param  Role  $admin
     * @param  Role  $editor
     * @param  Role  $viewer
     * @return void
     */
    protected function configureSpecialPermissions(Role $admin, Role $editor, Role $viewer): void
    {
        $adminPermissions = [];
        $editorPermissions = [];
        $viewerPermissions = [];

        $viewNova = Permission::findOrCreate('view nova');
        $adminPermissions[] = $viewNova;
        $editorPermissions[] = $viewNova;
        $viewerPermissions[] = $viewNova;

        $viewTelescope = Permission::findOrCreate('view telescope');
        $adminPermissions[] = $viewTelescope;

        $viewHorizon = Permission::findOrCreate('view horizon');
        $adminPermissions[] = $viewHorizon;

        $bypassApiRateLimiter = Permission::findOrCreate('bypass api rate limiter');
        $adminPermissions[] = $bypassApiRateLimiter;

        $admin->givePermissionTo($adminPermissions);
        $editor->givePermissionTo($editorPermissions);
        $viewer->givePermissionTo($viewerPermissions);
    }
}
