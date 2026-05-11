<?php
/**
 * milestones.php — Milestone State Machine & Deliverable Workflow
 * SpecialistHub · PHP Stub
 *
 * Milestone States:
 *   locked → active → submitted → under_review → approved | revision_requested → approved
 *   Any state → disputed
 *
 * Endpoints:
 *   GET  /php/milestones.php?action=list&project_id=42
 *   POST /php/milestones.php?action=unlock_next       (escrow check before unlock)
 *   POST /php/milestones.php?action=submit_work
 *   POST /php/milestones.php?action=request_revision
 *   POST /php/milestones.php?action=approve
 *   POST /php/milestones.php?action=auto_approve_check (cron job trigger)
 *   POST /php/milestones.php?action=amend             (scope creep amendment)
 *   POST /php/milestones.php?action=snapshot          (WIP auto-archive)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$action = $_GET['action'] ?? '';
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

// ── Valid state transitions ───────────────────────────────────
const TRANSITIONS = [
    'locked'             => ['active'],
    'active'             => ['submitted'],
    'submitted'          => ['under_review'],
    'under_review'       => ['approved', 'revision_requested', 'disputed'],
    'revision_requested' => ['submitted'],
    'approved'           => [],
    'disputed'           => [],
];

function canTransition(string $from, string $to): bool {
    return in_array($to, TRANSITIONS[$from] ?? []);
}

switch ($action) {

    /**
     * LIST MILESTONES
     * Returns all milestones for a project with state, escrow info, and revision counts
     */
    case 'list':
        $project_id = (int)($_GET['project_id'] ?? 0);
        if (!$project_id) { echo json_encode(['success' => false, 'error' => 'project_id required']); exit; }

        // TODO: SELECT * FROM milestones WHERE project_id = ? ORDER BY phase_number ASC

        echo json_encode([
            'success'    => true,
            'milestones' => [
                [
                    'id'                => 1,
                    'phase_number'      => 1,
                    'title'             => 'Discovery & Data Analysis',
                    'description'       => 'Initial data audit and EDA notebook.',
                    'amount'            => 2000,
                    'currency'          => 'USD',
                    'status'            => 'approved',
                    'due_date'          => '2025-04-10',
                    'submitted_at'      => '2025-04-02',
                    'approved_at'       => '2025-04-05',
                    'inspection_days'   => 5,
                    'free_revisions'    => 1,
                    'revisions_used'    => 0,
                    'escrow_locked'     => false,
                    'escrow_released'   => true,
                ],
                [
                    'id'                => 2,
                    'phase_number'      => 2,
                    'title'             => 'Model Development',
                    'description'       => 'XGBoost model + evaluation report.',
                    'amount'            => 5000,
                    'currency'          => 'USD',
                    'status'            => 'under_review',
                    'due_date'          => '2025-05-20',
                    'submitted_at'      => '2025-05-01',
                    'approved_at'       => null,
                    'inspection_days'   => 5,
                    'auto_approve_at'   => '2025-05-06', // Inspection window deadline
                    'free_revisions'    => 2,
                    'revisions_used'    => 0,
                    'escrow_locked'     => true,
                    'escrow_released'   => false,
                ],
                [
                    'id'                => 3,
                    'phase_number'      => 3,
                    'title'             => 'Deployment & Documentation',
                    'amount'            => 3000,
                    'status'            => 'locked',
                    'due_date'          => '2025-06-10',
                    'inspection_days'   => 7,
                    'free_revisions'    => 1,
                    'revisions_used'    => 0,
                    'escrow_locked'     => true,
                    'escrow_released'   => false,
                ],
            ],
        ]);
        break;

    /**
     * UNLOCK NEXT MILESTONE
     * Called after previous milestone is approved and next escrow is confirmed locked
     * Body: { milestone_id, project_id }
     */
    case 'unlock_next':
        $milestone_id = (int)($body['milestone_id'] ?? 0);

        // TODO:
        // 1. Verify previous milestone is 'approved'
        // 2. Verify escrow for THIS milestone is locked (call escrow.php check)
        // 3. Transition status: locked → active
        // 4. Notify freelancer milestone is now active
        // 5. Start deadline escalation observer

        echo json_encode([
            'success' => true,
            'message' => 'Milestone unlocked. Freelancer has been notified.',
            'milestone' => ['id' => $milestone_id, 'status' => 'active'],
        ]);
        break;

    /**
     * SUBMIT WORK (Freelancer action)
     * Body: { milestone_id, files[], qa_checklist_complete, notes? }
     */
    case 'submit_work':
        $milestone_id = (int)($body['milestone_id'] ?? 0);
        $qa_complete  = (bool)($body['qa_checklist_complete'] ?? false);

        if (!$qa_complete) {
            echo json_encode([
                'success' => false,
                'error'   => 'QA checklist must be completed before submission.',
            ]);
            exit;
        }

        // TODO:
        // 1. Validate milestone status === 'active'
        // 2. Validate milestone belongs to authenticated specialist
        // 3. Save uploaded files to storage (S3 / local)
        // 4. Transition status: active → submitted → under_review
        // 5. Record submission timestamp
        // 6. Trigger WIP snapshot (auto_snapshot)
        // 7. Notify client — inspection window starts
        // 8. Schedule auto_approve_check job at (now + inspection_days)

        echo json_encode([
            'success'         => true,
            'message'         => 'Work submitted. Client inspection window started.',
            'auto_approve_at' => date('c', strtotime('+5 days')),
            'milestone'       => ['id' => $milestone_id, 'status' => 'under_review'],
        ]);
        break;

    /**
     * REQUEST REVISION (Client action)
     * Body: { milestone_id, revision_notes }
     */
    case 'request_revision':
        $milestone_id   = (int)($body['milestone_id'] ?? 0);
        $revision_notes = trim($body['revision_notes'] ?? '');

        // TODO:
        // 1. Fetch milestone, check revisions_used < free_revisions
        // 2. If over limit → charge paid revision fee (call payments.php)
        // 3. Transition: under_review → revision_requested
        // 4. Notify freelancer with notes
        // 5. Increment revisions_used

        echo json_encode([
            'success'        => true,
            'message'        => 'Revision requested. Freelancer has been notified.',
            'revisions_used' => 1,
            'revisions_free' => 2,
            'paid_revision'  => false,
        ]);
        break;

    /**
     * APPROVE MILESTONE (Client action)
     * Body: { milestone_id, rating?, feedback? }
     */
    case 'approve':
        $milestone_id = (int)($body['milestone_id'] ?? 0);

        // TODO:
        // 1. Validate milestone in under_review
        // 2. Transition: under_review → approved
        // 3. Call escrow.php → release funds to freelancer
        // 4. Update project progress %
        // 5. Unlock next milestone if exists
        // 6. Update freelancer reputation score
        // 7. Log to audit trail

        echo json_encode([
            'success'          => true,
            'message'          => 'Milestone approved. Funds released.',
            'released_amount'  => 5000,
            'next_milestone'   => 3,
        ]);
        break;

    /**
     * AUTO-APPROVE CHECK (Run as cron every hour)
     * No body — scans all milestones past their auto_approve_at date
     */
    case 'auto_approve_check':
        // TODO:
        // SELECT * FROM milestones
        //   WHERE status = 'under_review'
        //   AND auto_approve_at <= NOW()
        // For each: call approve logic silently

        // Stub: simulate finding 1 expired inspection window
        $auto_approved = [
            ['milestone_id' => 7, 'project_id' => 14, 'amount' => 3000],
        ];

        // TODO: Loop and process each
        echo json_encode([
            'success'       => true,
            'auto_approved' => $auto_approved,
            'count'         => count($auto_approved),
        ]);
        break;

    /**
     * AMEND CONTRACT SCOPE (Bilateral approval required)
     * Body: { milestone_id, project_id, proposed_changes, new_amount?, new_due_date? }
     */
    case 'amend':
        $project_id       = (int)($body['project_id'] ?? 0);
        $proposed_changes = trim($body['proposed_changes'] ?? '');

        if (!$proposed_changes) {
            echo json_encode(['success' => false, 'error' => 'Amendment description required.']);
            exit;
        }

        // TODO:
        // 1. Insert into amendments table with status = 'pending_approval'
        // 2. Notify other party — both must approve for amendment to take effect
        // 3. If both approve: update milestone record
        // 4. If either rejects: amendment discarded, log to audit trail

        echo json_encode([
            'success'       => true,
            'amendment_id'  => 42,
            'status'        => 'pending_other_party_approval',
            'message'       => 'Amendment proposed. Awaiting other party approval.',
        ]);
        break;

    /**
     * WIP SNAPSHOT (Auto-archive of intermediate deliverables)
     * Body: { milestone_id, files[] }
     * Called automatically at submission and on schedule
     */
    case 'snapshot':
        $milestone_id = (int)($body['milestone_id'] ?? 0);

        // TODO:
        // 1. Copy current deliverable files to /snapshots/milestone_{id}/v{n}/
        // 2. Record snapshot in DB with timestamp and version number
        // 3. Keep last N snapshots (configurable), purge older ones

        echo json_encode([
            'success'     => true,
            'snapshot_id' => 991,
            'version'     => 3,
            'saved_at'    => date('c'),
            'message'     => 'WIP snapshot saved automatically.',
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action.']);
}
