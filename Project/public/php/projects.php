<?php
/**
 * projects.php — Job Post CRUD & Bidding
 * SpecialistHub · PHP Stub
 *
 * Endpoints:
 *   GET    /php/projects.php?action=list&niche=data-science&status=open
 *   GET    /php/projects.php?action=get&id=42
 *   POST   /php/projects.php?action=create
 *   PUT    /php/projects.php?action=update&id=42
 *   DELETE /php/projects.php?action=delete&id=42
 *   GET    /php/projects.php?action=bids&project_id=42
 *   POST   /php/projects.php?action=submit_bid
 *   POST   /php/projects.php?action=shortlist_bid
 *   POST   /php/projects.php?action=withdraw_bid
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// TODO: $pdo = new PDO(...);

$action = $_GET['action'] ?? '';
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

// ── Helpers ──────────────────────────────────────────────────
function stubProject(int $id): array {
    $niches = ['data-science', 'legal', 'translation', 'finance', 'engineering'];
    return [
        'id'           => $id,
        'title'        => 'Stub Project #' . $id,
        'niche'        => $niches[array_rand($niches)],
        'description'  => 'Full description from DB...',
        'budget'       => rand(3, 50) * 1000,
        'currency'     => 'USD',
        'status'       => 'open',       // open | in_progress | completed | cancelled | disputed
        'visibility'   => 'public',     // public | private
        'bid_count'    => rand(0, 30),
        'deadline'     => date('Y-m-d', strtotime('+60 days')),
        'bid_expiry'   => date('Y-m-d', strtotime('+7 days')),
        'nda_required' => true,
        'client_id'    => 1,
        'created_at'   => date('c'),
        'milestones'   => [],           // Populated from milestones.php
    ];
}

switch ($action) {

    /**
     * LIST PROJECTS
     * Filters: niche, status, budget_min, budget_max, visibility, client_id
     */
    case 'list':
        $niche  = $_GET['niche']  ?? null;
        $status = $_GET['status'] ?? 'open';
        $limit  = min((int)($_GET['limit'] ?? 20), 100);

        // TODO:
        // $sql = 'SELECT * FROM projects WHERE status = ?';
        // $params = [$status];
        // if ($niche) { $sql .= ' AND niche = ?'; $params[] = $niche; }
        // $sql .= ' ORDER BY created_at DESC LIMIT ?';

        $projects = array_map('stubProject', range(1, min($limit, 5)));

        echo json_encode([
            'success'  => true,
            'total'    => 128,
            'page'     => 1,
            'projects' => $projects,
        ]);
        break;

    /**
     * GET SINGLE PROJECT
     */
    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'error' => 'ID required']); exit; }

        // TODO: $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?'); ...

        echo json_encode(['success' => true, 'project' => stubProject($id)]);
        break;

    /**
     * CREATE PROJECT
     * Body: { title, niche, description, budget, currency, visibility,
     *          niche_fields{}, bid_expiry_days, nda_required,
     *          interview_required, milestones[] }
     */
    case 'create':
        $required = ['title', 'niche', 'description', 'budget'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                echo json_encode(['success' => false, 'error' => "Field '$field' is required."]);
                exit;
            }
        }

        $allowed_niches = ['data-science', 'legal', 'translation', 'finance', 'engineering'];
        if (!in_array($body['niche'], $allowed_niches)) {
            echo json_encode(['success' => false, 'error' => 'Invalid niche.']);
            exit;
        }

        // TODO:
        // $stmt = $pdo->prepare('INSERT INTO projects
        //   (title, niche, description, budget, currency, visibility, client_id, nda_required, bid_expiry_at)
        //   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        // Insert milestones into milestones table...
        // Trigger skill-matching algorithm to notify specialists...

        echo json_encode([
            'success'    => true,
            'project_id' => 999,       // STUB: real ID from DB
            'message'    => 'Project created and live. Specialists are being notified.',
        ]);
        break;

    /**
     * UPDATE PROJECT
     * Body: partial project fields
     */
    case 'update':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'error' => 'ID required']); exit; }

        // TODO: Build dynamic UPDATE query, validate ownership, check status allows edits
        echo json_encode(['success' => true, 'message' => 'Project updated.']);
        break;

    /**
     * DELETE / CANCEL PROJECT
     */
    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        // TODO: Set status = 'cancelled', trigger refund logic if escrow active
        echo json_encode(['success' => true, 'message' => 'Project cancelled.']);
        break;

    /**
     * LIST BIDS FOR A PROJECT
     * Returns bids sorted by match score, rating, or price
     */
    case 'bids':
        $project_id = (int)($_GET['project_id'] ?? 0);
        $sort       = $_GET['sort'] ?? 'match_score'; // match_score | rating | price_asc | price_desc

        // TODO: JOIN bids with users, credentials, reputation scores
        echo json_encode([
            'success' => true,
            'bids' => [
                [
                    'id'           => 1,
                    'specialist'   => ['id' => 10, 'name' => 'Dr. Sarah Kim', 'rating' => 4.97, 'niche_success' => 98],
                    'amount'       => 9800,
                    'cover_letter' => 'Stub cover letter...',
                    'delivery_days'=> 70,
                    'status'       => 'pending', // pending | shortlisted | accepted | declined | expired
                    'submitted_at' => date('c'),
                ],
            ],
        ]);
        break;

    /**
     * SUBMIT BID
     * Body: { project_id, amount, cover_letter, delivery_days }
     */
    case 'submit_bid':
        // TODO: Validate specialist is verified, project is still open, bid expiry not passed
        // Check bid_expiry_at on project record
        echo json_encode(['success' => true, 'bid_id' => 888, 'message' => 'Bid submitted.']);
        break;

    /**
     * SHORTLIST BID — triggers NDA auto-generation
     * Body: { bid_id, project_id }
     */
    case 'shortlist_bid':
        $bid_id = (int)($body['bid_id'] ?? 0);
        // TODO: Update bid status = 'shortlisted'
        // Trigger NDA generator (see bids.php -> generate_nda)
        // Notify specialist
        echo json_encode([
            'success'   => true,
            'nda_url'   => '/documents/nda_stub.pdf', // TODO: real generated URL
            'message'   => 'Bid shortlisted. NDA generated and sent to both parties.',
        ]);
        break;

    /**
     * WITHDRAW BID (specialist action)
     * Body: { bid_id }
     */
    case 'withdraw_bid':
        // TODO: Mark bid withdrawn if within expiry window
        echo json_encode(['success' => true, 'message' => 'Bid withdrawn.']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action.']);
}
