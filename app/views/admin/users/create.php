<?php
/**
 * Vue : Créer un nouvel utilisateur
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Utilisateur - Admin</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-content">
                <h1>Créer un Nouvel Utilisateur</h1>
                <div class="header-actions">
                    <a href="/users.php" class="btn btn-secondary">← Retour Liste</a>
                </div>
            </div>
        </div>

        <!-- Messages d'erreur/succès -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire de création -->
        <div class="form-container">
            <form method="POST" action="/users.php?action=create" class="form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-section">
                    <h3>Informations de base</h3>
                    
                    <div class="form-group">
                        <label for="login" class="form-label">Nom d'utilisateur *</label>
                        <input type="text" id="login" name="login" class="form-input" 
                               value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" 
                               required minlength="3" maxlength="20">
                        <small>Entre 3 et 20 caractères</small>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe *</label>
                        <input type="password" id="password" name="password" class="form-input" 
                               required minlength="8">
                        <small>Minimum 8 caractères</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Rôle et permissions</h3>
                    
                    <div class="form-group">
                        <label for="role_id" class="form-label">Rôle *</label>
                        <select id="role_id" name="role_id" class="form-select" required>
                            <option value="">Sélectionner un rôle</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" 
                                        <?= (isset($_POST['role_id']) && $_POST['role_id'] == $role['id']) ? 'selected' : '' ?>>
                                    <?= ucfirst($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <div class="form-checkbox">
                            <input type="checkbox" id="is_active" name="is_active" value="1" 
                                   <?= (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : '' ?>>
                            <label for="is_active">Utilisateur actif</label>
                        </div>
                        <small>Un utilisateur inactif ne peut pas se connecter</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Créer l'utilisateur</button>
                    <a href="/users.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validation côté client
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.form');
            const loginInput = document.getElementById('login');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const roleSelect = document.getElementById('role_id');

            form.addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessage = '';

                // Validation du nom d'utilisateur
                if (loginInput.value.length < 3) {
                    errorMessage += 'Le nom d\'utilisateur doit contenir au moins 3 caractères.\n';
                    isValid = false;
                }

                // Validation de l'email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value)) {
                    errorMessage += 'L\'email n\'est pas valide.\n';
                    isValid = false;
                }

                // Validation du mot de passe
                if (passwordInput.value.length < 8) {
                    errorMessage += 'Le mot de passe doit contenir au moins 8 caractères.\n';
                    isValid = false;
                }

                // Validation du rôle
                if (!roleSelect.value) {
                    errorMessage += 'Veuillez sélectionner un rôle.\n';
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Erreurs de validation:\n' + errorMessage);
                }
            });
        });
    </script>
</body>
</html>
