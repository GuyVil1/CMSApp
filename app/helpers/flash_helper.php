<?php
/**
 * Helper pour l'affichage des messages flash
 */

/**
 * Afficher tous les messages flash
 */
function displayFlashMessages(): void
{
    if (isset($_SESSION['flash']) && !empty($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $type => $message) {
            echo '<div class="flash flash-' . htmlspecialchars($type) . '">';
            echo htmlspecialchars($message);
            echo '</div>';
        }
        // Supprimer les messages après affichage
        unset($_SESSION['flash']);
    }
}

/**
 * Afficher un message flash spécifique
 */
function displayFlashMessage(string $type): void
{
    if (isset($_SESSION['flash'][$type])) {
        echo '<div class="flash flash-' . htmlspecialchars($type) . '">';
        echo htmlspecialchars($_SESSION['flash'][$type]);
        echo '</div>';
        unset($_SESSION['flash'][$type]);
    }
}

/**
 * Vérifier s'il y a des messages flash
 */
function hasFlashMessages(): bool
{
    return isset($_SESSION['flash']) && !empty($_SESSION['flash']);
}

/**
 * Vérifier s'il y a un type de message flash spécifique
 */
function hasFlashMessage(string $type): bool
{
    return isset($_SESSION['flash'][$type]);
}

/**
 * Obtenir un message flash sans l'afficher
 */
function getFlashMessage(string $type): ?string
{
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}
