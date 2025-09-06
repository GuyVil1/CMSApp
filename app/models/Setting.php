<?php
declare(strict_types=1);

/**
 * Modèle Setting - Belgium Vidéo Gaming
 * Gestion des paramètres du site
 */

require_once __DIR__ . '/../../core/Database.php';

class Setting
{
    private int $id;
    private string $key;
    private ?string $value;
    private ?string $description;
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }
    
    private function hydrate(array $data): void
    {
        $this->id = (int) $data['id'];
        $this->key = $data['key'];
        $this->value = $data['value'];
        $this->description = $data['description'];
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getKey(): string { return $this->key; }
    public function getValue(): ?string { return $this->value; }
    public function getDescription(): ?string { return $this->description; }
    
    /**
     * Récupérer une option par sa clé
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            $sql = "SELECT value FROM settings WHERE `key` = ?";
            $result = Database::query($sql, [$key]);
            
            return !empty($result) ? $result[0]['value'] : $default;
        } catch (Exception $e) {
            error_log("Erreur récupération setting: " . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * Définir une option
     */
    public static function set(string $key, ?string $value, ?string $description = null): bool
    {
        try {
            // Vérifier si l'option existe
            $existing = self::get($key);
            
            if ($existing !== null) {
                // Mettre à jour
                $sql = "UPDATE settings SET value = ?, description = ? WHERE `key` = ?";
                return Database::execute($sql, [$value, $description, $key]) !== false;
            } else {
                // Créer
                $sql = "INSERT INTO settings (`key`, value, description) VALUES (?, ?, ?)";
                return Database::execute($sql, [$key, $value, $description]) !== false;
            }
        } catch (Exception $e) {
            error_log("Erreur sauvegarde setting: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer toutes les options
     */
    public static function getAll(): array
    {
        try {
            $sql = "SELECT * FROM settings ORDER BY `key`";
            $results = Database::query($sql);
            
            $settings = [];
            foreach ($results as $row) {
                $settings[$row['key']] = $row['value'];
            }
            
            return $settings;
        } catch (Exception $e) {
            error_log("Erreur récupération settings: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Vérifier si une option est activée (valeur '1' ou 'true')
     */
    public static function isEnabled(string $key): bool
    {
        $value = self::get($key);
        return in_array($value, ['1', 'true', 'on', 'yes'], true);
    }
    
    /**
     * Initialiser les options par défaut
     */
    public static function initDefaults(): void
    {
        $defaults = [
            'allow_registration' => ['value' => '1', 'description' => 'Autoriser les nouvelles inscriptions'],
            'dark_mode' => ['value' => '0', 'description' => 'Activer le mode sombre par défaut'],
            'maintenance_mode' => ['value' => '0', 'description' => 'Activer le mode maintenance'],
            'default_theme' => ['value' => 'defaut', 'description' => 'Thème par défaut du site'],
            'footer_tagline' => ['value' => 'Votre source #1 pour l\'actualité jeux vidéo en Belgique. Reviews, tests, guides et tout l\'univers gaming depuis 2020.', 'description' => 'Phrase d\'accroche du footer'],
            'social_twitter' => ['value' => '', 'description' => 'URL du compte Twitter'],
            'social_facebook' => ['value' => '', 'description' => 'URL du compte Facebook'],
            'social_youtube' => ['value' => '', 'description' => 'URL du compte YouTube'],
            'header_logo' => ['value' => 'Logo.svg', 'description' => 'Logo affiché dans le header'],
            'footer_logo' => ['value' => 'Logo_neutre_500px.png', 'description' => 'Logo affiché dans le footer'],
            'legal_mentions_content' => ['value' => '', 'description' => 'Contenu des mentions légales'],
            'legal_privacy_content' => ['value' => '', 'description' => 'Contenu de la politique de confidentialité'],
            'legal_cgu_content' => ['value' => '', 'description' => 'Contenu des conditions générales d\'utilisation'],
            'legal_cookies_content' => ['value' => '', 'description' => 'Contenu de la politique des cookies']
        ];
        
        foreach ($defaults as $key => $data) {
            if (self::get($key) === null) {
                self::set($key, $data['value'], $data['description']);
            }
        }
    }
}