<?php
declare(strict_types=1);

/**
 * Contrôleur de gestion des jeux - Belgium Vidéo Gaming
 */

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/Auth.php';
require_once __DIR__ . '/../../../core/Database.php';
require_once __DIR__ . '/../../models/Game.php';
require_once __DIR__ . '/../../models/Media.php';

class GamesController extends Controller
{
    public function __construct()
    {
        // Vérifier que l'utilisateur est connecté et a les droits admin/editor
        Auth::requireRole(['admin', 'editor']);
    }
    
    /**
     * Récupérer les informations d'un jeu via API
     */
    public function get(int $id): void
    {
        try {
            $game = Database::queryOne("
                SELECT g.*, m.filename, m.original_name, m.mime_type 
                FROM games g 
                LEFT JOIN media m ON g.cover_image_id = m.id 
                WHERE g.id = ?
            ", [$id]);
            
            if (!$game) {
                $this->jsonResponse(['success' => false, 'message' => 'Jeu non trouvé']);
                return;
            }
            
            // Construire l'URL de l'image
            $coverImage = null;
            if ($game['cover_image_id']) {
                $coverImage = [
                    'id' => $game['cover_image_id'],
                    'filename' => $game['filename'],
                    'original_name' => $game['original_name'],
                    'url' => '/public/uploads/' . $game['filename']
                ];
            }
            
            $gameData = [
                'id' => $game['id'],
                'title' => $game['title'],
                'slug' => $game['slug'],
                'description' => $game['description'],
                'platform' => $game['platform'],
                'genre' => $game['genre'],
                'release_date' => $game['release_date'],
                'cover_image' => $coverImage
            ];
            
            $this->jsonResponse(['success' => true, 'game' => $gameData]);
            
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Réponse JSON
     */
    private function jsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
