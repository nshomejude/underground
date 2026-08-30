<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Careers. Static, curated copy — there is no public job board, and never
 * will be one: a firm built on discretion does not advertise open
 * requisitions. This page explains the kind of people we hire, the
 * practice areas we recruit into, and how to reach us directly.
 */
final class CareersController extends Controller
{
    public function index(): View
    {
        return view('careers.index', [
            'traits' => [
                [
                    'icon' => 'shield-check',
                    'title' => 'Discreet by Instinct',
                    'body' => 'You have already spent a career not being the one who talks. Confidentiality is not a policy you follow here — it is a habit you arrived with.',
                ],
                [
                    'icon' => 'globe',
                    'title' => 'Globally Minded',
                    'body' => 'You have worked across borders, cultures, and institutions, and you read a room the same way whether it is in Lagos, London, or Jakarta.',
                ],
                [
                    'icon' => 'target',
                    'title' => 'Sharp Under Pressure',
                    'body' => 'You are comfortable being handed an ambiguous, high-stakes problem and no deck to hide behind, and you are trusted to close it out.',
                ],
                [
                    'icon' => 'handshake',
                    'title' => 'Principal, Not Staff',
                    'body' => 'You expect to sit at the table, not brief the person who does. Every hire here is expected to carry a mandate personally.',
                ],
            ],
            'practiceAreas' => [
                [
                    'icon' => 'landmark',
                    'title' => 'Government & Political Affairs',
                    'body' => 'Former ministerial advisers, diplomats, and legislative staff who understand how a decision actually gets made inside government.',
                ],
                [
                    'icon' => 'radar',
                    'title' => 'Strategic Intelligence & Analysis',
                    'body' => 'Analysts who can turn fragmented, sensitive information into a judgement a principal can act on with confidence.',
                ],
                [
                    'icon' => 'coins',
                    'title' => 'Investment & Capital Strategy',
                    'body' => 'Professionals who have structured cross-border capital for sovereign, institutional, or private allocators.',
                ],
                [
                    'icon' => 'megaphone',
                    'title' => 'Media & Narrative Management',
                    'body' => 'Communicators who have shaped how institutions are understood by the audiences that decide their fate, and know when silence is the better instrument.',
                ],
                [
                    'icon' => 'building-2',
                    'title' => 'Crisis & Special Situations',
                    'body' => 'People who are called in once a mandate has already gone public, and whose job is to make sure it does not stay that way.',
                ],
            ],
            'careersEmail' => 'careers@underground.network',
        ]);
    }
}
