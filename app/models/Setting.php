<?php
declare(strict_types=1);

/**
 * Modèle Setting - Gestion des paramètres du site
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
        $this->id = (int)($data['id'] ?? 0);
        $this->key = $data['key'] ?? '';
        $this->value = $data['value'] ?? null;
        $this->description = $data['description'] ?? null;
    }
    
    /**
     * Trouver un paramètre par clé
     */
    public static function findByKey(string $key): ?self
    {
        $sql = "SELECT * FROM settings WHERE `key` = ?";
        $data = Database::queryOne($sql, [$key]);
        
        return $data ? new self($data) : null;
    }
    
    /**
     * Obtenir la valeur d'un paramètre
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::findByKey($key);
        return $setting ? $setting->getValue() : $default;
    }
    
    /**
     * Définir la valeur d'un paramètre
     */
    public static function set(string $key, $value, ?string $description = null): bool
    {
        $setting = self::findByKey($key);
        
        if ($setting) {
            // Mettre à jour le paramètre existant
            return $setting->update(['value' => $value]);
        } else {
            // Créer un nouveau paramètre
            $newSetting = self::create([
                'key' => $key,
                'value' => $value,
                'description' => $description
            ]);
            return $newSetting !== null;
        }
    }
    
    /**
     * Trouver tous les paramètres
     */
    public static function findAll(): array
    {
        $sql = "SELECT * FROM settings ORDER BY `key` ASC";
        $results = Database::query($sql);
        
        return array_map(fn($data) => new self($data), $results);
    }
    
    /**
     * Créer un nouveau paramètre
     */
    public static function create(array $data): ?self
    {
        $sql = "INSERT INTO settings (`key`, `value`, `description`) VALUES (?, ?, ?)";
        
        $params = [
            $data['key'],
            $data['value'] ?? null,
            $data['description'] ?? null
        ];
        
        if (Database::execute($sql, $params)) {
            $id = Database::lastInsertId();
            return self::findByKey($data['key']);
        }
        
        return null;
    }
    
    /**
     * Mettre à jour un paramètre
     */
    public function update(array $data): bool
    {
        $sql = "UPDATE settings SET `value` = ?, `description` = ? WHERE `key` = ?";
        
        $params = [
            $data['value'] ?? $this->value,
            $data['description'] ?? $this->description,
            $this->key
        ];
        
        return Database::execute($sql, $params) > 0;
    }
    
    /**
     * Supprimer un paramètre
     */
    public function delete(): bool
    {
        $sql = "DELETE FROM settings WHERE `key` = ?";
        return Database::execute($sql, [$this->key]) > 0;
    }
    
    /**
     * Obtenir tous les paramètres comme un tableau associatif
     */
    public static function getAllAsArray(): array
    {
        $settings = self::findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->getKey()] = $setting->getValue();
        }
        
        return $result;
    }
    
    /**
     * Initialiser les paramètres par défaut
     */
    public static function initializeDefaults(): void
    {
        $defaults = [
            'site_name' => 'Belgium Vidéo Gaming',
            'site_tagline' => 'Actus, tests et trailers',
            'site_description' => 'Votre source #1 pour l\'actualité jeux vidéo en Belgique',
            'contact_email' => 'contact@belgium-video-gaming.be',
            'contact_address' => 'Bruxelles, Belgique',
            'social_facebook' => '',
            'social_twitter' => '',
            'social_instagram' => '',
            'social_youtube' => '',
            'analytics_google' => '',
            'seo_keywords' => 'jeux vidéo, belgique, gaming, actualités, tests',
            'seo_description' => 'Site d\'actualité jeux vidéo belge avec tests, guides et trailers',
            'posts_per_page' => '10',
            'enable_comments' => '1',
            'enable_registration' => '1',
            'maintenance_mode' => '0',
            'maintenance_message' => 'Site en maintenance. Merci de revenir plus tard.'
        ];
        
        foreach ($defaults as $key => $value) {
            if (!self::findByKey($key)) {
                self::create([
                    'key' => $key,
                    'value' => $value,
                    'description' => 'Paramètre par défaut'
                ]);
            }
        }
    }
    
    /**
     * Obtenir les paramètres de contact
     */
    public static function getContactSettings(): array
    {
        return [
            'email' => self::get('contact_email'),
            'address' => self::get('contact_address'),
            'facebook' => self::get('social_facebook'),
            'twitter' => self::get('social_twitter'),
            'instagram' => self::get('social_instagram'),
            'youtube' => self::get('social_youtube')
        ];
    }
    
    /**
     * Obtenir les paramètres SEO
     */
    public static function getSeoSettings(): array
    {
        return [
            'title' => self::get('site_name'),
            'description' => self::get('seo_description'),
            'keywords' => self::get('seo_keywords'),
            'analytics' => self::get('analytics_google')
        ];
    }
    
    /**
     * Vérifier si le mode maintenance est activé
     */
    public static function isMaintenanceMode(): bool
    {
        return (bool)self::get('maintenance_mode', false);
    }
    
    /**
     * Obtenir le message de maintenance
     */
    public static function getMaintenanceMessage(): string
    {
        return self::get('maintenance_message', 'Site en maintenance. Merci de revenir plus tard.');
    }
    
    /**
     * Obtenir le nombre d'articles par page
     */
    public static function getPostsPerPage(): int
    {
        return (int)self::get('posts_per_page', 10);
    }
    
    /**
     * Vérifier si les commentaires sont activés
     */
    public static function areCommentsEnabled(): bool
    {
        return (bool)self::get('enable_comments', true);
    }
    
    /**
     * Vérifier si l'inscription est activée
     */
    public static function isRegistrationEnabled(): bool
    {
        return (bool)self::get('enable_registration', true);
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getKey(): string { return $this->key; }
    public function getValue(): ?string { return $this->value; }
    public function getDescription(): ?string { return $this->description; }
    
    /**
     * Convertir en tableau pour l'API
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'description' => $this->description
        ];
    }
}
