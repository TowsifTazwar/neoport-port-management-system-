<?php
require_once __DIR__ . '/config/db.php';
$pdo = pms_pdo();

$stmt = $pdo->query("
    SELECT 
        sa.id AS agent_id,
        sa.agent_name,
        sa.email AS agent_email,
        sa.is_primary_contact,
        p.id AS partner_id,
        p.company_name
    FROM 
        shipping_agents sa
    JOIN 
        partners p ON sa.partner_id = p.id
    ORDER BY 
        p.company_name, sa.is_primary_contact DESC
");

$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipping Agent Verification</title>
    <style>
        body { font-family: sans-serif; margin: 2em; background-color: #f4f4f9; color: #333; }
        h1 { color: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #0056b3; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .primary { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Shipping Agent Information</h1>
    <p>This table shows all shipping agents and their associated company information from the database.</p>
    <table>
        <thead>
            <tr>
                <th>Agent ID</th>
                <th>Agent Name</th>
                <th>Agent Email</th>
                <th>Company Name</th>
                <th>Partner ID</th>
                <th>Is Primary Contact?</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($agents)): ?>
                <tr><td colspan="6">No shipping agents found in the database.</td></tr>
            <?php else: ?>
                <?php foreach ($agents as $agent): ?>
                    <tr>
                        <td><?= htmlspecialchars($agent['agent_id']) ?></td>
                        <td><?= htmlspecialchars($agent['agent_name']) ?></td>
                        <td><?= htmlspecialchars($agent['agent_email']) ?></td>
                        <td><?= htmlspecialchars($agent['company_name']) ?></td>
                        <td><?= htmlspecialchars($agent['partner_id']) ?></td>
                        <td class="<?= $agent['is_primary_contact'] ? 'primary' : '' ?>">
                            <?= $agent['is_primary_contact'] ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
