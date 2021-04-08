<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', __('Administrator'), [
            '*',
        ])->description(__('Administrator users can perform any action.'));

        Jetstream::role('editor', __('Editor'), [
            'anime:create',
            'anime:read',
            'anime:update',
            'anime:delete',
            'anime:restore',
            'artist:create',
            'artist:read',
            'artist:update',
            'artist:delete',
            'artist:restore',
            'entry:create',
            'entry:read',
            'entry:update',
            'entry:delete',
            'entry:restore',
            'image:create',
            'image:read',
            'image:update',
            'image:delete',
            'image:restore',
            'resource:create',
            'resource:read',
            'resource:update',
            'resource:delete',
            'resource:restore',
            'series:create',
            'series:read',
            'series:update',
            'series:delete',
            'series:restore',
            'song:create',
            'song:read',
            'song:update',
            'song:delete',
            'song:restore',
            'synonym:create',
            'synonym:read',
            'synonym:update',
            'synonym:delete',
            'synonym:restore',
            'theme:create',
            'theme:read',
            'theme:update',
            'theme:delete',
            'theme:restore',
            'video:read',
            'video:update',
        ])->description(__('Editor users have the ability to read, create, and update.'));

        Jetstream::role('viewer', __('Viewer'), [
            'anime:read',
            'artist:read',
            'entry:read',
            'image:read',
            'resource:read',
            'series:read',
            'song:read',
            'synonym:read',
            'theme:read',
            'video:read',
        ])->description(__('Viewers have read-only access.'));
    }
}
