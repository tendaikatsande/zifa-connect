<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions by module
        $permissions = [
            // Players
            'players' => [
                'players.view' => 'View players list',
                'players.view_any' => 'View any player details',
                'players.create' => 'Create new players',
                'players.update' => 'Update player information',
                'players.delete' => 'Delete draft players',
                'players.approve' => 'Approve player registrations',
                'players.reject' => 'Reject player registrations',
                'players.upload_documents' => 'Upload player documents',
            ],
            // Clubs
            'clubs' => [
                'clubs.view' => 'View clubs list',
                'clubs.view_any' => 'View any club details',
                'clubs.create' => 'Create new clubs',
                'clubs.update' => 'Update club information',
                'clubs.delete' => 'Delete clubs',
                'clubs.approve' => 'Approve club registrations',
                'clubs.manage_officials' => 'Manage club officials',
                'clubs.upload_documents' => 'Upload club documents',
            ],
            // Transfers
            'transfers' => [
                'transfers.view' => 'View transfers list',
                'transfers.view_any' => 'View any transfer details',
                'transfers.create' => 'Initiate transfers',
                'transfers.approve_club' => 'Approve transfers as club',
                'transfers.approve_zifa' => 'Approve transfers as ZIFA',
                'transfers.reject' => 'Reject transfers',
            ],
            // Invoices & Payments
            'finance' => [
                'invoices.view' => 'View invoices',
                'invoices.view_any' => 'View any invoice',
                'invoices.create' => 'Create invoices',
                'invoices.cancel' => 'Cancel invoices',
                'payments.initiate' => 'Initiate payments',
                'payments.view' => 'View payment status',
            ],
            // Competitions
            'competitions' => [
                'competitions.view' => 'View competitions',
                'competitions.create' => 'Create competitions',
                'competitions.update' => 'Update competitions',
                'competitions.delete' => 'Delete competitions',
                'matches.view' => 'View matches',
                'matches.create' => 'Create matches',
                'matches.manage' => 'Manage match details',
            ],
            // Officials & Referees
            'officials' => [
                'officials.view' => 'View officials',
                'officials.create' => 'Register officials',
                'officials.update' => 'Update officials',
                'referees.view' => 'View referees',
                'referees.create' => 'Register referees',
                'referees.update' => 'Update referees',
                'courses.view' => 'View training courses',
                'courses.create' => 'Create training courses',
                'courses.enroll' => 'Enroll in courses',
            ],
            // Disciplinary
            'disciplinary' => [
                'disciplinary.view' => 'View disciplinary cases',
                'disciplinary.create' => 'Create disciplinary cases',
                'disciplinary.manage' => 'Manage sanctions and appeals',
            ],
            // Registrations
            'registrations' => [
                'registrations.view' => 'View registrations',
                'registrations.approve' => 'Approve registrations',
                'registrations.reject' => 'Reject registrations',
            ],
            // Funds
            'funds' => [
                'funds.view' => 'View funds',
                'funds.create' => 'Create funds',
                'funds.disburse' => 'Disburse funds',
            ],
            // Reports
            'reports' => [
                'reports.dashboard' => 'View dashboard',
                'reports.financial' => 'View financial reports',
                'reports.registrations' => 'View registration reports',
                'reports.transfers' => 'View transfer reports',
            ],
            // FIFA Sync
            'fifa' => [
                'fifa.view_status' => 'View FIFA sync status',
                'fifa.trigger_sync' => 'Trigger FIFA sync',
                'fifa.view_mismatches' => 'View FIFA data mismatches',
            ],
            // System
            'system' => [
                'settings.view' => 'View system settings',
                'settings.update' => 'Update system settings',
                'users.view' => 'View users',
                'users.manage' => 'Manage users',
                'audit.view' => 'View audit logs',
            ],
        ];

        // Create all permissions
        foreach ($permissions as $module => $modulePermissions) {
            foreach ($modulePermissions as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name],
                    [
                        'module' => $module,
                        'description' => $description,
                        'guard_name' => 'web',
                    ]
                );
            }
        }

        // Create roles with permissions
        // NOTE: Explicit permissions prevent auto-expansion when new permissions are added
        $roles = [
            'super_admin' => [
                'description' => 'Full system access',
                'permissions' => ['*'], // All permissions - intentional for super admin only
            ],
            'zifa_admin' => [
                'description' => 'ZIFA administrative staff',
                'permissions' => [
                    // Players
                    'players.view', 'players.view_any', 'players.create', 'players.update',
                    'players.delete', 'players.approve', 'players.reject', 'players.upload_documents',
                    // Clubs
                    'clubs.view', 'clubs.view_any', 'clubs.create', 'clubs.update',
                    'clubs.delete', 'clubs.approve', 'clubs.manage_officials', 'clubs.upload_documents',
                    // Transfers
                    'transfers.view', 'transfers.view_any', 'transfers.create',
                    'transfers.approve_club', 'transfers.approve_zifa', 'transfers.reject',
                    // Finance
                    'invoices.view', 'invoices.view_any', 'invoices.create', 'invoices.cancel',
                    'payments.initiate', 'payments.view',
                    // Competitions
                    'competitions.view', 'competitions.create', 'competitions.update', 'competitions.delete',
                    'matches.view', 'matches.create', 'matches.manage',
                    // Officials
                    'officials.view', 'officials.create', 'officials.update',
                    'referees.view', 'referees.create', 'referees.update',
                    'courses.view', 'courses.create', 'courses.enroll',
                    // Disciplinary
                    'disciplinary.view', 'disciplinary.create', 'disciplinary.manage',
                    // Registrations
                    'registrations.view', 'registrations.approve', 'registrations.reject',
                    // Funds
                    'funds.view', 'funds.create', 'funds.disburse',
                    // Reports
                    'reports.dashboard', 'reports.financial', 'reports.registrations', 'reports.transfers',
                    // FIFA
                    'fifa.view_status', 'fifa.trigger_sync', 'fifa.view_mismatches',
                ],
            ],
            'zifa_finance' => [
                'description' => 'ZIFA finance department',
                'permissions' => [
                    // Finance - explicit permissions
                    'invoices.view', 'invoices.view_any', 'invoices.create', 'invoices.cancel',
                    'payments.initiate', 'payments.view',
                    // Reports
                    'reports.dashboard', 'reports.financial',
                    // Funds - view only
                    'funds.view',
                ],
            ],
            'club_admin' => [
                'description' => 'Club administrator',
                'permissions' => [
                    'players.view', 'players.create', 'players.update',
                    'players.upload_documents', 'clubs.view', 'clubs.update',
                    'clubs.upload_documents', 'clubs.manage_officials',
                    'transfers.view', 'transfers.create', 'transfers.approve_club',
                    'invoices.view', 'payments.initiate', 'payments.view',
                    'matches.view', 'reports.dashboard',
                ],
            ],
            'referee' => [
                'description' => 'Match official',
                'permissions' => [
                    'matches.view', 'matches.manage', 'courses.view',
                    'courses.enroll', 'reports.dashboard',
                ],
            ],
            'official' => [
                'description' => 'Club official or coach',
                'permissions' => [
                    'players.view', 'clubs.view', 'matches.view',
                    'courses.view', 'courses.enroll', 'reports.dashboard',
                ],
            ],
            'player' => [
                'description' => 'Registered player',
                'permissions' => [
                    'players.view', 'clubs.view', 'matches.view',
                    'reports.dashboard',
                ],
            ],
        ];

        foreach ($roles as $name => $config) {
            $role = Role::firstOrCreate(
                ['name' => $name],
                [
                    'description' => $config['description'],
                    'guard_name' => 'web',
                ]
            );

            // Assign permissions to role
            if ($config['permissions'] === ['*']) {
                // Super admin gets all permissions
                $role->permissions()->sync(Permission::pluck('id'));
            } else {
                $permissionIds = [];
                foreach ($config['permissions'] as $pattern) {
                    if (str_ends_with($pattern, '.*')) {
                        // Wildcard pattern - get all permissions starting with prefix
                        $prefix = str_replace('.*', '', $pattern);
                        $ids = Permission::where('name', 'like', $prefix . '%')->pluck('id');
                        $permissionIds = array_merge($permissionIds, $ids->toArray());
                    } else {
                        $permission = Permission::where('name', $pattern)->first();
                        if ($permission) {
                            $permissionIds[] = $permission->id;
                        }
                    }
                }
                $role->permissions()->sync(array_unique($permissionIds));
            }
        }
    }
}
