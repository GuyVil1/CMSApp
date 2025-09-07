<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Belgium Video Gaming</title>
    <link rel="stylesheet" href="/admin.css">
    <style>
        .monitoring-header { 
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: var(--admin-text);
            padding: var(--admin-spacing-lg);
            border-radius: 12px;
            margin-bottom: var(--admin-spacing-lg);
            box-shadow: var(--admin-shadow);
        }
        .monitoring-header h1 { 
            color: var(--admin-primary);
            margin: 0 0 10px 0;
            font-size: 2.5em;
        }
        .monitoring-header p { 
            color: var(--admin-text-muted);
            margin: 0;
        }
        .monitoring-actions { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            margin-top: 15px; 
            flex-wrap: wrap; 
        }
        .system-status { 
            padding: 8px 16px; 
            border-radius: 20px; 
            font-weight: bold; 
            font-size: 0.9em; 
        }
        .status-healthy { 
            background: rgba(39, 174, 96, 0.2); 
            color: #27ae60; 
            border: 1px solid rgba(39, 174, 96, 0.3); 
        }
        .status-warning { 
            background: rgba(243, 156, 18, 0.2); 
            color: #f39c12; 
            border: 1px solid rgba(243, 156, 18, 0.3); 
        }
        .status-critical { 
            background: rgba(231, 76, 60, 0.2); 
            color: #e74c3c; 
            border: 1px solid rgba(231, 76, 60, 0.3); 
        }
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: var(--admin-spacing-md); 
            margin-bottom: var(--admin-spacing-lg); 
        }
        .stat-card { 
            background: var(--admin-card-bg); 
            padding: var(--admin-spacing-lg); 
            border-radius: 12px; 
            box-shadow: var(--admin-shadow);
            border: 1px solid var(--admin-border);
            text-align: center;
        }
        .stat-value { 
            font-size: 2.5em; 
            font-weight: bold; 
            color: var(--admin-primary);
            margin-bottom: 5px;
        }
        .stat-label { 
            color: var(--admin-text-muted); 
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .metric-section { 
            background: var(--admin-card-bg); 
            padding: var(--admin-spacing-lg); 
            border-radius: 12px; 
            box-shadow: var(--admin-shadow);
            margin-bottom: var(--admin-spacing-md);
            border: 1px solid var(--admin-border);
        }
        .metric-title { 
            font-size: 1.3em; 
            font-weight: bold; 
            margin-bottom: var(--admin-spacing-md); 
            color: var(--admin-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .metric-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .metric-table th, .metric-table td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid var(--admin-border); 
        }
        .metric-table th { 
            background: rgba(255, 255, 255, 0.05); 
            font-weight: bold; 
            color: var(--admin-text);
            text-transform: uppercase;
            font-size: 0.8em;
            letter-spacing: 0.5px;
        }
        .metric-table td { 
            color: var(--admin-text-muted);
        }
        .status-good { color: #27ae60; }
        .status-warning { color: #f39c12; }
        .status-danger { color: #e74c3c; }
        .alert-item { 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 8px; 
            border-left: 4px solid;
        }
        .alert-critical { 
            background: rgba(231, 76, 60, 0.1); 
            border-left-color: #e74c3c;
        }
        .alert-warning { 
            background: rgba(243, 156, 18, 0.1); 
            border-left-color: #f39c12;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="monitoring-header">
            <h1>📊 Dashboard de Monitoring</h1>
            <p>Surveillance des performances en temps réel</p>
            <div class="monitoring-actions">
                <div class="system-status status-<?= $systemStatus ?>">
                    <?php if ($systemStatus === 'healthy'): ?>
                        ✅ Système sain
                    <?php elseif ($systemStatus === 'warning'): ?>
                        ⚠️ Avertissements
                    <?php else: ?>
                        🚨 Problèmes critiques
                    <?php endif; ?>
                </div>
                <a href="/admin/dashboard" class="btn">← Dashboard</a>
                <a href="/admin/settings" class="btn">⚙️ Settings</a>
                <button class="btn btn-info" onclick="refreshData()">🔄 Actualiser</button>
                <button class="btn btn-info auto-refresh" onclick="toggleAutoRefresh()">⏰ Auto-refresh</button>
                <button class="btn" onclick="resetMetrics()">🗑️ Reset</button>
            </div>
        </div>

        <!-- Alertes -->
        <?php if (!empty($alerts)): ?>
        <div class="metric-section">
            <div class="metric-title">🚨 Alertes Actives (<?= count($alerts) ?>)</div>
            <?php foreach ($alerts as $alert): ?>
                <div class="alert-item alert-<?= $alert['level'] ?>">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <strong style="color: var(--admin-text);"><?= htmlspecialchars($alert['message']) ?></strong>
                            <div style="font-size: 0.9em; color: var(--admin-text-muted); margin-top: 5px;">
                                <?= htmlspecialchars($alert['action']) ?>
                            </div>
                        </div>
                        <div style="font-size: 0.8em; color: var(--admin-text-muted);">
                            <?= date('H:i:s', $alert['timestamp']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Statistiques principales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['total_requests'] ?></div>
                <div class="stat-label">Requêtes totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-value <?= $stats['avg_response_time'] < 100 ? 'status-good' : ($stats['avg_response_time'] < 500 ? 'status-warning' : 'status-danger') ?>">
                    <?= $stats['avg_response_time'] ?>ms
                </div>
                <div class="stat-label">Temps de réponse moyen</div>
            </div>
            <div class="stat-card">
                <div class="stat-value <?= $stats['error_rate'] < 1 ? 'status-good' : ($stats['error_rate'] < 5 ? 'status-warning' : 'status-danger') ?>">
                    <?= $stats['error_rate'] ?>%
                </div>
                <div class="stat-label">Taux d'erreur</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= formatBytes($stats['memory_usage']) ?></div>
                <div class="stat-label">Utilisation mémoire</div>
            </div>
        </div>

        <!-- Métriques système -->
        <div class="metric-section">
            <div class="metric-title">🖥️ Métriques Système</div>
            <table class="metric-table">
                <tr>
                    <th>Métrique</th>
                    <th>Valeur</th>
                    <th>Statut</th>
                </tr>
                <tr>
                    <td>Mémoire utilisée</td>
                    <td><?= formatBytes($systemMetrics['memory_usage']) ?></td>
                    <td class="<?= $systemMetrics['memory_usage'] < 50 * 1024 * 1024 ? 'status-good' : 'status-warning' ?>">
                        <?= $systemMetrics['memory_usage'] < 50 * 1024 * 1024 ? '✅ Bon' : '⚠️ Élevé' ?>
                    </td>
                </tr>
                <tr>
                    <td>Pic mémoire</td>
                    <td><?= formatBytes($systemMetrics['memory_peak']) ?></td>
                    <td class="<?= $systemMetrics['memory_peak'] < 100 * 1024 * 1024 ? 'status-good' : 'status-warning' ?>">
                        <?= $systemMetrics['memory_peak'] < 100 * 1024 * 1024 ? '✅ Bon' : '⚠️ Élevé' ?>
                    </td>
                </tr>
                <tr>
                    <td>Limite mémoire</td>
                    <td><?= $systemMetrics['memory_limit'] ?></td>
                    <td class="status-good">✅ Configuré</td>
                </tr>
                <tr>
                    <td>Temps d'exécution</td>
                    <td><?= round($systemMetrics['execution_time'], 3) ?>s</td>
                    <td class="<?= $systemMetrics['execution_time'] < 1 ? 'status-good' : 'status-warning' ?>">
                        <?= $systemMetrics['execution_time'] < 1 ? '✅ Rapide' : '⚠️ Lent' ?>
                    </td>
                </tr>
                <tr>
                    <td>Version PHP</td>
                    <td><?= $systemMetrics['php_version'] ?></td>
                    <td class="status-good">✅ À jour</td>
                </tr>
                <?php if (isset($systemMetrics['database_metrics']['db_ping_time'])): ?>
                <tr>
                    <td>Ping DB</td>
                    <td><?= round($systemMetrics['database_metrics']['db_ping_time'], 2) ?>ms</td>
                    <td class="<?= $systemMetrics['database_metrics']['db_ping_time'] < 10 ? 'status-good' : 'status-warning' ?>">
                        <?= $systemMetrics['database_metrics']['db_ping_time'] < 10 ? '✅ Rapide' : '⚠️ Lent' ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Métriques de base de données -->
        <?php if (isset($systemMetrics['database_metrics']) && !isset($systemMetrics['database_metrics']['error'])): ?>
        <div class="metric-section">
            <div class="metric-title">🗄️ Base de Données</div>
            <table class="metric-table">
                <tr>
                    <th>Table</th>
                    <th>Nombre d'enregistrements</th>
                    <th>Statut</th>
                </tr>
                <?php foreach ($systemMetrics['database_metrics'] as $key => $value): ?>
                    <?php if (strpos($key, 'table_') === 0 && strpos($key, '_count') !== false): ?>
                        <?php 
                        $tableName = str_replace(['table_', '_count'], '', $key);
                        $status = $value > 0 ? 'status-good' : 'status-warning';
                        $statusText = $value > 0 ? '✅ Données' : '⚠️ Vide';
                        ?>
                        <tr>
                            <td><?= ucfirst($tableName) ?></td>
                            <td><?= number_format($value) ?></td>
                            <td class="<?= $status ?>"><?= $statusText ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>

        <!-- Métriques de performance -->
        <div class="metric-section">
            <div class="metric-title">⚡ Métriques de Performance</div>
            <table class="metric-table">
                <tr>
                    <th>Composant</th>
                    <th>Requêtes</th>
                    <th>Temps moyen</th>
                    <th>Temps min</th>
                    <th>Temps max</th>
                    <th>P95</th>
                </tr>
                <?php foreach ($metrics['timers'] as $name => $timer): ?>
                <tr>
                    <td><?= htmlspecialchars($name) ?></td>
                    <td><?= $timer['count'] ?></td>
                    <td><?= round($timer['avg'], 2) ?>ms</td>
                    <td><?= round($timer['min'], 2) ?>ms</td>
                    <td><?= round($timer['max'], 2) ?>ms</td>
                    <td><?= round($timer['p95'], 2) ?>ms</td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Compteurs -->
        <div class="metric-section">
            <div class="metric-title">📊 Compteurs</div>
            <table class="metric-table">
                <tr>
                    <th>Métrique</th>
                    <th>Valeur</th>
                </tr>
                <?php foreach ($metrics['counters'] as $name => $value): ?>
                <tr>
                    <td><?= htmlspecialchars($name) ?></td>
                    <td><?= $value ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        let autoRefreshInterval;
        
        function refreshData() {
            fetch('/admin/monitoring/api')
                .then(response => response.json())
                .then(data => {
                    // Mettre à jour les statistiques principales
                    updateStats(data.stats);
                    // Mettre à jour les métriques système
                    updateSystemMetrics(data.system);
                    // Mettre à jour les métriques de performance
                    updatePerformanceMetrics(data.metrics.timers);
                    // Mettre à jour les compteurs
                    updateCounters(data.metrics.counters);
                })
                .catch(error => console.error('Erreur:', error));
        }
        
        function updateStats(stats) {
            // Mettre à jour les cartes de statistiques
            const totalRequests = document.querySelector('.stat-card:nth-child(1) .stat-value');
            const avgResponseTime = document.querySelector('.stat-card:nth-child(2) .stat-value');
            const errorRate = document.querySelector('.stat-card:nth-child(3) .stat-value');
            const memoryUsage = document.querySelector('.stat-card:nth-child(4) .stat-value');
            
            if (totalRequests) totalRequests.textContent = stats.total_requests || 0;
            if (avgResponseTime) avgResponseTime.textContent = (stats.avg_response_time || 0) + 'ms';
            if (errorRate) errorRate.textContent = (stats.error_rate || 0) + '%';
            if (memoryUsage) memoryUsage.textContent = formatBytes(stats.memory_usage || 0);
        }
        
        // Fonction formatBytes pour JavaScript
        function formatBytes(bytes, precision = 2) {
            if (bytes === 0) return '0 B';
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            const k = 1024;
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(precision)) + ' ' + units[i];
        }
        
        function updateSystemMetrics(systemMetrics) {
            // Mettre à jour les métriques système dans le tableau
            const rows = document.querySelectorAll('.metric-section:nth-child(3) .metric-table tr');
            if (rows.length > 1) {
                // Mémoire utilisée
                if (rows[1] && rows[1].cells[1]) {
                    rows[1].cells[1].textContent = formatBytes(systemMetrics.memory_usage || 0);
                }
                // Pic mémoire
                if (rows[2] && rows[2].cells[1]) {
                    rows[2].cells[1].textContent = formatBytes(systemMetrics.memory_peak || 0);
                }
                // Temps d'exécution
                if (rows[4] && rows[4].cells[1]) {
                    rows[4].cells[1].textContent = (systemMetrics.execution_time || 0).toFixed(3) + 's';
                }
            }
        }
        
        function updatePerformanceMetrics(timers) {
            // Mettre à jour le tableau des métriques de performance
            const tbody = document.querySelector('.metric-section:nth-child(4) .metric-table tbody');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            for (const [name, timer] of Object.entries(timers)) {
                const row = tbody.insertRow();
                row.insertCell(0).textContent = name;
                row.insertCell(1).textContent = timer.count || 0;
                row.insertCell(2).textContent = (timer.avg || 0).toFixed(2) + 'ms';
                row.insertCell(3).textContent = (timer.min || 0).toFixed(2) + 'ms';
                row.insertCell(4).textContent = (timer.max || 0).toFixed(2) + 'ms';
                row.insertCell(5).textContent = (timer.p95 || 0).toFixed(2) + 'ms';
            }
        }
        
        function updateCounters(counters) {
            // Mettre à jour le tableau des compteurs
            const tbody = document.querySelector('.metric-section:nth-child(5) .metric-table tbody');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            for (const [name, value] of Object.entries(counters)) {
                const row = tbody.insertRow();
                row.insertCell(0).textContent = name;
                row.insertCell(1).textContent = value;
            }
        }
        
        function toggleAutoRefresh() {
            const button = document.querySelector('.auto-refresh');
            if (!button) return;
            
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
                button.textContent = '⏰ Auto-refresh OFF';
            } else {
                autoRefreshInterval = setInterval(refreshData, 5000);
                button.textContent = '⏰ Auto-refresh ON';
            }
        }
        
        function resetMetrics() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les métriques ?')) {
                fetch('/admin/monitoring/reset', { method: 'POST' })
                    .then(() => location.reload());
            }
        }
        
        // Auto-refresh par défaut
        toggleAutoRefresh();
    </script>
</body>
</html>

<?php
// Fonction helper pour formater les bytes
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
