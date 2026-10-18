<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PwaAppController extends Controller
{
    /**
     * Show the staff PWA install & information page.
     */
    public function staffApp(Request $request): Response
    {
        /** @var Tenant|null $tenant */
        $tenant = tenant();
        $domain = $tenant instanceof Tenant ? $tenant->getPrimaryDomain() : null;

        return Inertia::render('tenant/staff/App', [
            'tenant' => [
                'id' => tenant('id'),
                'name' => $tenant instanceof Tenant ? $tenant->getShortName() : (tenant('name') ?? tenant('id')),
                'short_name' => $tenant instanceof Tenant ? $tenant->getShortName() : tenant('id'),
                'domain' => $domain ?? $request->getHost(),
                'theme_color' => $tenant instanceof Tenant ? $tenant->getStaffThemeColor() : '#0f172a',
                'icon_192' => $tenant instanceof Tenant ? $tenant->getStaffIcon192() : '/icons/staff-192x192.png',
                'icon_512' => $tenant instanceof Tenant ? $tenant->getStaffIcon512() : '/icons/staff-512x512.png',
            ],
            'features' => [
                'Offline Grading & Attendance' => 'Review and manage your class schedules and records without requiring a continuous internet connection.',
                'Instant Push Notifications' => 'Receive immediate alerts regarding school announcements, urgent updates, and schedule adjustments.',
                'Dedicated Full-Screen Workspace' => 'Eliminates distracting browser toolbars for a distraction-free, professional teaching environment.',
                'One-Touch Biometric Launch' => 'Fast, secure access directly from your device home screen or desktop application dock.',
            ],
        ]);
    }

    /**
     * Show the student & parent PWA install & information page.
     */
    public function usersApp(Request $request): Response
    {
        /** @var Tenant|null $tenant */
        $tenant = tenant();
        $domain = $tenant instanceof Tenant ? $tenant->getPrimaryDomain() : null;

        return Inertia::render('tenant/users/App', [
            'tenant' => [
                'id' => tenant('id'),
                'name' => $tenant instanceof Tenant ? $tenant->getShortName() : (tenant('name') ?? tenant('id')),
                'short_name' => $tenant instanceof Tenant ? $tenant->getShortName() : tenant('id'),
                'domain' => $domain ?? $request->getHost(),
                'theme_color' => $tenant instanceof Tenant ? $tenant->getUserThemeColor() : '#4f46e5',
                'icon_192' => $tenant instanceof Tenant ? $tenant->getUserIcon192() : '/icons/user-192x192.png',
                'icon_512' => $tenant instanceof Tenant ? $tenant->getUserIcon512() : '/icons/user-512x512.png',
            ],
            'features' => [
                'Real-Time Grades & Performance' => 'Monitor academic progress, GPA scores, and assignment feedback in real time.',
                'Class Schedules & Homework Timelines' => 'Never miss a deadline or exam with integrated course calendars and notifications.',
                'Direct School Communication' => 'Stay in touch with teachers, counselors, and administrative staff effortlessly.',
                'Offline Learning Access' => 'Read syllabus details and saved lesson materials even when you are disconnected.',
            ],
        ]);
    }
}
