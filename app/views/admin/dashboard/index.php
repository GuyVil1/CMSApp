<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - GameNews Belgium</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎮 Tableau de bord</h1>
            <p>Bienvenue dans l'administration de Belgium Vidéo Gaming</p>
        </div>

        <div class="user-info">
            <h3>👤 Informations utilisateur</h3>
            <div class="user-details">
                <div class="user-detail">
                    <strong>Login :</strong> <?= htmlspecialchars($user['login']) ?>
                </div>
                <div class="user-detail">
                    <strong>Email :</strong> <?= htmlspecialchars($user['email']) ?>
                </div>
                <div class="user-detail">
                    <strong>Rôle :</strong> <?= htmlspecialchars($user['role_name']) ?>
                </div>
                <div class="user-detail">
                    <strong>Dernière connexion :</strong> 
                    <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais' ?>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['articles'] ?></div>
                <div class="stat-label">Articles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['users'] ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['games'] ?></div>
                <div class="stat-label">Jeux</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['categories'] ?></div>
                <div class="stat-label">Catégories</div>
            </div>
        </div>

        <div class="actions-section">
            <h3 style="color: var(--admin-primary); margin-bottom: var(--admin-spacing-lg); text-align: center; font-size: 1.5em;">🚀 Actions rapides</h3>
            
            <!-- Première rangée : Gestion du contenu -->
            <div class="actions-row" style="margin-bottom: var(--admin-spacing-lg);">
                <div class="action-group" style="text-align: center;">
                    <h4 style="color: var(--admin-text-muted); margin-bottom: var(--admin-spacing-md); font-size: 1.1em; text-transform: uppercase; letter-spacing: 0.5px;">📝 Gestion du contenu</h4>
                    <div class="actions-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--admin-spacing-md);">
                        <a href="/admin/articles" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">📝</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Gérer les articles</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                        
                        <a href="/admin/articles/create" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-success); border: 1px solid var(--admin-success); border-radius: 12px; text-decoration: none; color: white; transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">➕</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Nouvel article</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.2); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: white; font-weight: bold; font-size: 1.2em;">→</span>
                            </div>
                        </a>
                        
                        <a href="/admin/media" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">🖼️</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Gérer les médias</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Deuxième rangée : Administration et navigation -->
            <div class="actions-row">
                <div class="action-group" style="text-align: center;">
                    <h4 style="color: var(--admin-text-muted); margin-bottom: var(--admin-spacing-md); font-size: 1.1em; text-transform: uppercase; letter-spacing: 0.5px;">⚙️ Administration & Navigation</h4>
                    <div class="actions-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--admin-spacing-md);">
                        <a href="/admin/themes" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">🎨</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Gérer les thèmes</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                        
                        <a href="/users.php" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">👥</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Gérer les utilisateurs</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                        
                        <a href="/tags.php" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">🏷️</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Gérer les tags</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                        
                        <a href="/categories.php" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">📂</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Gérer les catégories</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                        
                        <a href="/games.php" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">🎮</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Gérer les jeux</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                        
                        <a href="/hardware.php" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">🖥️</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Gérer les hardware</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                        
                        <a href="/genres.php" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">🎭</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Gérer les genres</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                        
                        <a href="/" class="action-btn" style="display: flex; flex-direction: column; align-items: center; padding: var(--admin-spacing-lg); background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; text-decoration: none; color: var(--admin-text); transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <div class="action-icon" style="font-size: 2.5em; margin-bottom: var(--admin-spacing-sm);">🏠</div>
                            <span class="action-text" style="font-weight: 600; font-size: 0.9em;">Retour au site</span>
                            <div class="action-hover" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, var(--admin-primary) 0%, rgba(255, 215, 0, 0.8) 100%); opacity: 0; transition: opacity 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                <span style="color: var(--admin-dark); font-weight: bold;">→</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Bouton de déconnexion centré en bas -->
            <div style="text-align: center; margin-top: var(--admin-spacing-xl);">
                <a href="/logout" class="logout-btn" style="display: inline-flex; align-items: center; gap: var(--admin-spacing-sm); padding: var(--admin-spacing-md) var(--admin-spacing-xl); background: var(--admin-secondary); color: white; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s ease; border: 2px solid var(--admin-secondary);">
                    <span style="font-size: 1.2em;">🚪</span>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </div>
    
    <style>
        /* Effets de survol pour les boutons d'action */
        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            border-color: var(--admin-primary);
        }
        
        .action-btn:hover .action-hover {
            opacity: 1;
        }
        
        /* Animation pour le bouton de déconnexion */
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
            background: #c0392b;
            border-color: #c0392b;
        }
        
        /* Responsive design pour les grilles */
        @media (max-width: 1200px) {
            .actions-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        
        @media (max-width: 768px) {
            .actions-grid {
                grid-template-columns: 1fr !important;
            }
            
            .action-btn {
                padding: var(--admin-spacing-md) !important;
            }
            
            .action-icon {
                font-size: 2em !important;
            }
        }
    </style>
</body>
</html>
