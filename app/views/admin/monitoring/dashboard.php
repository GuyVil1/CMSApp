<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - Belgium Video Gaming</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: #2c3e50; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat-value { font-size: 2em; font-weight: bold; color: #2c3e50; }
        .stat-label { color: #7f8c8d; margin-top: 5px; }
        .metric-section { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .metric-title { font-size: 1.2em; font-weight: bold; margin-bottom: 15px; color: #2c3e50; }
        .metric-table { width: 100%; border-collapse: collapse; }
        .metric-table th, .metric-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .metric-table th { background: #ecf0f1; font-weight: bold; }
        .status-good { color: #27ae60; }
        .status-warning { color: #f39c12; }
        .status-danger { color: #e74c3c; }
        .refresh-btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .refresh-btn:hover { background: #2980b9; }
        .auto-refresh { margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Dashboard de Monitoring</h1>
            <p>Surveillance des performances en temps réel</p>
            <button class="refresh-btn" onclick="refreshData()">🔄 Actualiser</button>
            <button class="refresh-btn auto-refresh" onclick="toggleAutoRefresh()">⏰ Auto-refresh</button>
            <button class="refresh-btn" onclick="resetMetrics()" style="background: #e74c3c;">🗑️ Reset</button>
        </div>

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
            </table>
        </div>

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
